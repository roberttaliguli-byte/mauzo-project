<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ActivityHelper;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\ShiftSession;
use App\Models\User;
use App\Models\Wafanyakazi;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Mobile equivalent of the web Shift widget (resources/views/shift/partials/widget.blade.php).
 *
 * Uses the Sanctum token owner instead of the web session guards, so bosses
 * and wafanyakazi get exactly the same shift behaviour in the app.
 */
class ShiftApiController extends Controller
{
    /* ── Owner / permissions ───────────────────────────────── */

    private function owner(Request $request): ?array
    {
        $user = $request->user();
        if (!$user) return null;

        if ($user instanceof Wafanyakazi) {
            return [
                'company_id'    => $user->company_id,
                'user_id'       => null,
                'mfanyakazi_id' => $user->id,
                'name'          => $user->jina,
                'type'          => 'employee',
                'role'          => $user->role ?? 'mfanyakazi',
                'can_view_all'  => ($user->uwezo ?? 'mdogo') === 'mkubwa',
            ];
        }

        return [
            'company_id'    => $user->company_id,
            'user_id'       => $user->id,
            'mfanyakazi_id' => null,
            'name'          => $user->name ?? $user->username,
            'type'          => 'boss',
            'role'          => $user->role,
            'can_view_all'  => in_array($user->role, ['boss', 'admin'], true),
        ];
    }

    private function err(string $message, int $status = 422)
    {
        return response()->json(['success' => false, 'message' => $message], $status);
    }

    private function for403()
    {
        return $this->err('Akaunti yako haijaunganishwa na kampuni.', 403);
    }

    private function canView(ShiftSession $session, array $owner): bool
    {
        if ((int) $session->company_id !== (int) $owner['company_id']) return false;
        if ($owner['can_view_all']) return true;
        if ($owner['mfanyakazi_id']) {
            return (int) $session->mfanyakazi_id === (int) $owner['mfanyakazi_id'];
        }
        return (int) $session->user_id === (int) $owner['user_id'];
    }

    /** Only bosses and 'mkubwa' employees may see profit/cost figures. */
    private function canViewProfit(array $owner): bool
    {
        return (bool) ($owner['can_view_all'] ?? false);
    }

    private function filterSummary(array $summary, bool $canViewProfit): array
    {
        if ($canViewProfit) return $summary;

        unset($summary['totals']['total_profit'], $summary['totals']['total_cost']);
        if (isset($summary['products']) && is_array($summary['products'])) {
            $summary['products'] = array_map(function ($p) {
                unset($p['faida'], $p['gharama']);
                return $p;
            }, $summary['products']);
        }
        return $summary;
    }

    private function formatSession(
        ShiftSession $session,
        bool $withDetails = false,
        bool $canViewProfit = true
    ): array {
        $totals = $session->listSummary();

        $data = [
            'id'               => $session->id,
            'status'           => $session->status,
            'is_open'          => $session->status === 'open',
            'owner_name'       => $session->owner_name,
            'owner_role'       => $session->owner_role,
            'owner_initials'   => $session->owner_initials,
            'owner_type'       => $session->mfanyakazi_id ? 'employee' : 'boss',
            'owner_id'         => $session->mfanyakazi_id ?: $session->user_id,
            'started_at'       => optional($session->started_at)->toIso8601String(),
            'started_at_text'  => optional($session->started_at)->format('d/m/Y H:i'),
            'closed_at'        => optional($session->closed_at)->toIso8601String(),
            'closed_at_text'   => optional($session->closed_at)->format('d/m/Y H:i'),
            'duration_text'    => $session->duration_text,
            'duration_minutes' => $session->duration_minutes,
            'opening_balance'  => (float) $session->opening_balance,
            'closing_cash'     => $session->closing_cash !== null ? (float) $session->closing_cash : null,
            'expected_cash'    => $totals['expected_cash'] ?? (float) $session->expected_cash,
            'cash_difference'  => $totals['cash_difference'] ?? ($session->cash_difference !== null ? (float) $session->cash_difference : null),
            'notes'            => $session->notes,
            'report_sent_at'   => optional($session->report_sent_at)->format('d/m/Y H:i'),
            'report_sent_to'   => $session->report_sent_to,
            'totals'           => $totals,
        ];

        if ($withDetails) {
            $data['summary'] = $session->summary();
        }

        if (!$canViewProfit) {
            unset($data['totals']['total_profit'], $data['totals']['total_cost']);
            if (isset($data['summary'])) {
                $data['summary'] = $this->filterSummary($data['summary'], false);
            }
        }

        return $data;
    }

    private function activeSession(array $owner, bool $fresh = true): ?ShiftSession
    {
        ShiftSession::forgetActiveCache();

        $query = ShiftSession::where('company_id', $owner['company_id'])
            ->where('status', 'open');

        if ($owner['mfanyakazi_id']) {
            $query->where('mfanyakazi_id', $owner['mfanyakazi_id']);
        } else {
            $query->where('user_id', $owner['user_id']);
        }

        return $query->latest('started_at')->first();
    }

    /* ── Endpoints ─────────────────────────────────────────── */

    /** GET /api/shift/current */
    public function current(Request $request)
    {
        $owner = $this->owner($request);
        if (!$owner || !$owner['company_id']) return $this->for403();

        $canViewProfit = $this->canViewProfit($owner);
        $session = $this->activeSession($owner);

        return response()->json([
            'success'  => true,
            'has_open' => (bool) $session,
            'session'  => $session ? $this->formatSession($session, true, $canViewProfit) : null,
            'viewer'   => [
                'name'            => $owner['name'],
                'type'            => $owner['type'],
                'can_view_all'    => $owner['can_view_all'],
                'can_view_profit' => $canViewProfit,
            ],
        ]);
    }

    /** POST /api/shift/start */
    public function start(Request $request)
    {
        $owner = $this->owner($request);
        if (!$owner || !$owner['company_id']) return $this->for403();
        $canViewProfit = $this->canViewProfit($owner);

        $validated = $request->validate([
            'opening_balance' => 'nullable|numeric|min:0',
        ]);

        if ($existing = $this->activeSession($owner)) {
            return response()->json([
                'success' => false,
                'message' => 'Tayari una shift iliyoanza. Funga shift ya sasa kwanza kabla ya kuanza nyingine.',
                'session' => $this->formatSession($existing, true, $canViewProfit),
            ], 409);
        }

        $session = ShiftSession::create([
            'company_id'     => $owner['company_id'],
            'user_id'        => $owner['user_id'],
            'mfanyakazi_id'  => $owner['mfanyakazi_id'],
            'status'         => 'open',
            'started_at'     => now(),
            'opening_balance' => $validated['opening_balance'] ?? 0,
        ]);
        ShiftSession::forgetActiveCache();

        ActivityHelper::log(
            'shift_started',
            "{$owner['name']} ameanza shift mpya",
            $session
        );

        return response()->json([
            'success' => true,
            'message' => 'Shift imeanza kikamilifu. Mauzo na matumizi yote yatarekodiwa kwenye shift hii.',
            'session' => $this->formatSession($session->fresh(), true, $canViewProfit),
        ]);
    }

    /** POST /api/shift/close */
    public function close(Request $request)
    {
        $owner = $this->owner($request);
        if (!$owner || !$owner['company_id']) return $this->for403();
        $canViewProfit = $this->canViewProfit($owner);

        $validated = $request->validate([
            'closing_cash' => 'nullable|numeric|min:0',
            'notes'        => 'nullable|string|max:1000',
        ]);

        $session = $this->activeSession($owner);
        if (!$session) {
            return $this->err('Hakuna shift iliyoanza kwa mtumiaji huyu.', 404);
        }
        if ($session->status !== 'open') {
            return $this->err('Shift hii tayari imefungwa.', 409);
        }

        $closingCash = $request->filled('closing_cash') ? (float) $validated['closing_cash'] : null;
        $summary = $session->closeWith($closingCash, $validated['notes'] ?? null);
        $session->refresh();

        ActivityHelper::log(
            'shift_closed',
            "Shift ya {$session->owner_name} imefungwa - Mauzo: Tsh " . number_format($summary['totals']['total_sales'], 0),
            $session
        );

        return response()->json([
            'success'       => true,
            'message'       => 'Shift imefungwa na salio limehifadhiwa.',
            'session'       => $this->formatSession($session, true, $canViewProfit),
            'summary'       => $this->filterSummary($summary, $canViewProfit),
            'whatsapp_text' => $this->buildMessage($session, $summary, $canViewProfit),
        ]);
    }

    /** GET /api/shift/sessions */
    public function index(Request $request)
    {
        $owner = $this->owner($request);
        if (!$owner || !$owner['company_id']) return $this->for403();
        $canViewProfit = $this->canViewProfit($owner);

        $query = ShiftSession::where('company_id', $owner['company_id'])
            ->orderByDesc('started_at');

        if (!$owner['can_view_all']) {
            if ($owner['mfanyakazi_id']) {
                $query->where('mfanyakazi_id', $owner['mfanyakazi_id']);
            } else {
                $query->where('user_id', $owner['user_id']);
            }
        }

        if ($request->filled('status') && in_array($request->status, ['open', 'closed'], true)) {
            $query->where('status', $request->status);
        }

        $sessions = $query->limit(200)->get()
            ->map(fn (ShiftSession $s) => $this->formatSession($s, false, $canViewProfit))
            ->values();

        return response()->json([
            'success'  => true,
            'sessions' => $sessions,
            'viewer'   => [
                'name'            => $owner['name'],
                'can_view_all'    => $owner['can_view_all'],
                'can_view_profit' => $canViewProfit,
            ],
        ]);
    }

    /** GET /api/shift/{id}/report */
    public function report(Request $request, int $id)
    {
        $owner = $this->owner($request);
        if (!$owner || !$owner['company_id']) return $this->for403();
        $canViewProfit = $this->canViewProfit($owner);

        $session = ShiftSession::where('company_id', $owner['company_id'])->find($id);
        if (!$session || !$this->canView($session, $owner)) {
            return $this->err('Shift hii haipatikani.', 404);
        }

        return response()->json([
            'success' => true,
            'session' => $this->formatSession($session, true, $canViewProfit),
            'viewer'  => ['can_view_profit' => $canViewProfit],
            'message' => $this->buildMessage($session, $session->summary(), $canViewProfit),
        ]);
    }

    /** POST /api/shift/{id}/send-report */
    public function sendReport(Request $request, int $id)
    {
        $owner = $this->owner($request);
        if (!$owner || !$owner['company_id']) return $this->for403();
        $canViewProfit = $this->canViewProfit($owner);

        $session = ShiftSession::where('company_id', $owner['company_id'])->find($id);
        if (!$session || !$this->canView($session, $owner)) {
            return $this->err('Shift hii haipatikani.', 404);
        }

        $validated = $request->validate([
            'channel' => 'required|in:copy,whatsapp',
            'phone'   => 'nullable|string|max:20',
        ]);

        $message = $this->buildMessage($session, $session->summary(), $canViewProfit);
        if ($validated['channel'] === 'copy') {
            return response()->json(['success' => true, 'channel' => 'copy', 'message' => $message]);
        }

        $phone = $validated['phone'] ?? (Company::find($session->company_id)->phone ?? null);
        $digits = $phone ? preg_replace('/[^0-9]/', '', $phone) : null;
        if ($digits) {
            if (str_starts_with($digits, '0')) {
                $digits = '255' . substr($digits, 1);
            } elseif (str_starts_with($digits, '7') || str_starts_with($digits, '6')) {
                $digits = '255' . $digits;
            }
        }

        return response()->json([
            'success' => true,
            'channel' => 'whatsapp',
            'phone'   => $digits,
            'message' => $message,
            'wa_link' => $digits
                ? 'https://wa.me/' . $digits . '?text=' . rawurlencode($message)
                : 'https://wa.me/?text=' . rawurlencode($message),
        ]);
    }

    /* ── Report message (same wording as the web widget) ───── */

    private function buildMessage(ShiftSession $session, array $summary, bool $includeProfit): string
    {
        $companyName = Company::find($session->company_id)?->company_name ?? 'Duka';
        $totals = $summary['totals'];
        $lines = [
            strtoupper($companyName),
            'RIPOTI YA SHIFT',
            'Mfanyakazi: ' . $session->owner_name,
            'Kuanza: ' . optional($session->started_at)->format('d/m/Y H:i'),
        ];
        if ($session->closed_at) {
            $lines[] = 'Kufunga: ' . $session->closed_at->format('d/m/Y H:i');
        }
        $lines[] = '--------------------------';
        $lines[] = 'Mauzo: Tsh ' . number_format($totals['total_sales'], 0);
        $lines[] = 'Bidhaa zilizouzwa: ' . number_format($totals['total_items'], 0);
        if ($includeProfit) {
            $lines[] = 'Faida: Tsh ' . number_format($totals['total_profit'], 0);
        }
        $lines[] = 'Matumizi: Tsh ' . number_format($totals['total_expenses'], 0);
        $lines[] = 'Fedha taslimu: Tsh ' . number_format($totals['cash_sales'], 0);
        $lines[] = 'Salio linalotarajiwa: Tsh ' . number_format($totals['expected_cash'], 0);
        if ($totals['closing_cash'] !== null) {
            $lines[] = 'Salio lililohesabiwa: Tsh ' . number_format($totals['closing_cash'], 0);
            $lines[] = 'Tofauti: Tsh ' . number_format($totals['cash_difference'] ?? 0, 0);
        }
        if (!empty($summary['products'])) {
            $lines[] = '--------------------------';
            $lines[] = 'BIDHAA ZILIZOUZWA:';
            foreach (array_slice($summary['products'], 0, 8) as $p) {
                $lines[] = '- ' . $p['jina'] . ': ' . number_format($p['idadi'], 0)
                    . ' x Tsh ' . number_format($p['kiasi'], 0);
            }
        }
        $lines[] = '--------------------------';
        $lines[] = 'MauzoSheet';

        return implode("\n", $lines);
    }
}
