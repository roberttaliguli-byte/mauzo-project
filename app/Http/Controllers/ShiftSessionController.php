<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\ShiftSession;
use App\Models\User;
use App\Models\Wafanyakazi;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ShiftSessionController extends Controller
{
    /* ------------------------------------------------------------------ */
    /* Helpers                                                             */
    /* ------------------------------------------------------------------ */

    private function owner(): array
    {
        $owner = ShiftSession::currentOwner();

        if (!$owner || !$owner['company_id']) {
            abort(403, 'Huna ruhusa. Tafadhali ingia kwanza.');
        }

        return $owner;
    }

    private function canView(ShiftSession $session, array $owner): bool
    {
        if ((int) $session->company_id !== (int) $owner['company_id']) {
            return false;
        }

        if ($owner['can_view_all']) {
            return true;
        }

        if ($owner['mfanyakazi_id']) {
            return (int) $session->mfanyakazi_id === (int) $owner['mfanyakazi_id'];
        }

        return (int) $session->user_id === (int) $owner['user_id'];
    }

    /**
     * Only bosses and mkubwa employees may see profit/cost figures.
     */
    private function canViewProfit(array $owner): bool
    {
        return (bool) ($owner['can_view_all'] ?? false);
    }

    /**
     * Remove profit and cost figures from a report summary.
     */
    private function filterSummary(array $summary, bool $canViewProfit): array
    {
        if ($canViewProfit) {
            return $summary;
        }

        unset($summary['totals']['total_profit'], $summary['totals']['total_cost']);

        if (isset($summary['products']) && is_array($summary['products'])) {
            $summary['products'] = array_map(function ($product) {
                unset($product['faida'], $product['gharama']);

                return $product;
            }, $summary['products']);
        }

        return $summary;
    }

    /**
     * Remove profit from the flattened session payload used by the UI.
     */
    private function filterSessionForViewer(array $data, bool $canViewProfit): array
    {
        if ($canViewProfit) {
            return $data;
        }

        unset($data['totals']['total_profit'], $data['totals']['total_cost']);

        if (isset($data['summary']) && is_array($data['summary'])) {
            $data['summary'] = $this->filterSummary($data['summary'], false);
        }

        return $data;
    }

    private function formatSession(ShiftSession $session, bool $withDetails = false, bool $canViewProfit = true): array
    {
        $totals = $session->listSummary();

        $data = [
            'id' => $session->id,
            'status' => $session->status,
            'is_open' => $session->status === 'open',
            'owner_name' => $session->owner_name,
            'owner_role' => $session->owner_role,
            'owner_initials' => $session->owner_initials,
            'owner_type' => $session->mfanyakazi_id ? 'employee' : 'boss',
            'owner_id' => $session->mfanyakazi_id ?: $session->user_id,
            'started_at' => optional($session->started_at)->toIso8601String(),
            'started_at_text' => optional($session->started_at)->format('d/m/Y H:i'),
            'closed_at' => optional($session->closed_at)->toIso8601String(),
            'closed_at_text' => optional($session->closed_at)->format('d/m/Y H:i'),
            'duration_text' => $session->duration_text,
            'duration_minutes' => $session->duration_minutes,
            'opening_balance' => (float) $session->opening_balance,
            'closing_cash' => $session->closing_cash !== null ? (float) $session->closing_cash : null,
            'expected_cash' => $totals['expected_cash'] ?? (float) $session->expected_cash,
            'cash_difference' => $totals['cash_difference'] ?? ($session->cash_difference !== null ? (float) $session->cash_difference : null),
            'notes' => $session->notes,
            'report_sent_at' => optional($session->report_sent_at)->format('d/m/Y H:i'),
            'report_sent_to' => $session->report_sent_to,
            'totals' => $totals,
        ];

        if ($withDetails) {
            $data['summary'] = $session->summary();
        }

        return $this->filterSessionForViewer($data, $canViewProfit);
    }

    /* ------------------------------------------------------------------ */
    /* Endpoints                                                           */
    /* ------------------------------------------------------------------ */

    /**
     * Current open shift for the logged-in user (used by the Mauzo page widget).
     */
    public function current()
    {
        $owner = $this->owner();
        $canViewProfit = $this->canViewProfit($owner);
        $session = ShiftSession::activeForCurrentUser(true);

        return response()->json([
            'success' => true,
            'has_open' => (bool) $session,
            'session' => $session ? $this->formatSession($session, true, $canViewProfit) : null,
            'viewer' => [
                'name' => $owner['name'],
                'type' => $owner['type'],
                'can_view_all' => $owner['can_view_all'],
                'can_view_profit' => $canViewProfit,
            ],
        ]);
    }

    /**
     * Start a new shift for the logged-in user.
     */
    public function start(Request $request)
    {
        $owner = $this->owner();
        $canViewProfit = $this->canViewProfit($owner);

        $validated = $request->validate([
            'opening_balance' => 'nullable|numeric|min:0',
        ]);

        $existing = ShiftSession::activeForCurrentUser(true);
        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Tayari una shift iliyoanza. Funga shift ya sasa kwanza kabla ya kuanza nyingine.',
                'session' => $this->formatSession($existing, true, $canViewProfit),
            ], 409);
        }

        $session = ShiftSession::create([
            'company_id' => $owner['company_id'],
            'user_id' => $owner['user_id'],
            'mfanyakazi_id' => $owner['mfanyakazi_id'],
            'status' => 'open',
            'started_at' => now(),
            'opening_balance' => $validated['opening_balance'] ?? 0,
        ]);

        ShiftSession::forgetActiveCache();

        \App\Helpers\ActivityHelper::log(
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

    /**
     * Close a shift and save the final balance.
     * Bosses / full-access employees may pass session_id to close someone else's shift.
     */
    public function close(Request $request)
    {
        $owner = $this->owner();
        $canViewProfit = $this->canViewProfit($owner);

        $validated = $request->validate([
            'session_id' => 'nullable|integer|exists:shift_sessions,id',
            'closing_cash' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        if (!empty($validated['session_id'])) {
            $session = ShiftSession::findOrFail($validated['session_id']);
            if (!$this->canView($session, $owner)) {
                abort(403, 'Huna ruhusa ya kufunga shift hii.');
            }
        } else {
            $session = ShiftSession::activeForCurrentUser(true);
        }

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Hakuna shift iliyoanza kwa mtumiaji huyu.',
            ], 404);
        }

        if ($session->status !== 'open') {
            return response()->json([
                'success' => false,
                'message' => 'Shift hii tayari imefungwa.',
                'session' => $this->formatSession($session, true, $canViewProfit),
            ], 409);
        }

        $closingCash = $request->filled('closing_cash') ? (float) $validated['closing_cash'] : null;
        $summary = $session->closeWith($closingCash, $validated['notes'] ?? null);
        $session->refresh();

        \App\Helpers\ActivityHelper::log(
            'shift_closed',
            "Shift ya {$session->owner_name} imefungwa - Mauzo: Tsh " . number_format($summary['totals']['total_sales'], 0),
            $session
        );

        return response()->json([
            'success' => true,
            'message' => 'Shift imefungwa na salio limehifadhiwa.',
            'session' => $this->formatSession($session, true, $canViewProfit),
            'summary' => $this->filterSummary($summary, $canViewProfit),
            'whatsapp_text' => $this->buildMessage($session, $summary, $canViewProfit),
        ]);
    }

    /**
     * Page shown in the left navigation for mdogo employees: their own shifts only.
     * Bosses / mkubwa employees use the Uchambuzi "Vikao vya Shift" tab instead.
     */
    public function mine()
    {
        $owner = $this->owner();
        $canViewProfit = $this->canViewProfit($owner);
        $shiftCanViewAll = (bool) ($owner['can_view_all'] ?? false);
        $companyId = $owner['company_id'];

        $bossUsers = $shiftCanViewAll
            ? User::where('company_id', $companyId)->whereIn('role', ['boss', 'admin'])->orderBy('name')->get()
            : collect();

        $employeeUsers = $shiftCanViewAll
            ? Wafanyakazi::where('company_id', $companyId)->orderBy('jina')->get()
            : collect();

        return view('shift.mine', [
            'showProfit' => $canViewProfit,
            'shiftCanViewAll' => $shiftCanViewAll,
            'bossUsers' => $bossUsers,
            'employeeUsers' => $employeeUsers,
        ]);
    }

    /**
     * List shifts for the Uchambuzi report and the employee's "Shift Yangu" page.
     * Employees only receive their own; bosses / full-access employees receive all.
     */
    public function index(Request $request)
    {
        $owner = $this->owner();
        $canViewProfit = $this->canViewProfit($owner);

        $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
            'status' => 'nullable|in:open,closed',
            'user_type' => 'nullable|in:boss,employee',
            'user_id' => 'nullable|integer',
        ]);

        $query = ShiftSession::query()
            ->with(['user', 'mfanyakazi'])
            ->visibleToCurrentUser()
            ->orderByDesc('started_at');

        if ($request->filled('from')) {
            $query->where('started_at', '>=', Carbon::parse($request->input('from'))->startOfDay());
        }

        if ($request->filled('to')) {
            $query->where('started_at', '<=', Carbon::parse($request->input('to'))->endOfDay());
        }

        if ($request->filled('status') && in_array($request->input('status'), ['open', 'closed'], true)) {
            $query->where('status', $request->input('status'));
        }

        if ($owner['can_view_all'] && $request->filled('user_type') && $request->filled('user_id')) {
            if ($request->input('user_type') === 'employee') {
                $query->where('mfanyakazi_id', (int) $request->input('user_id'));
            } elseif ($request->input('user_type') === 'boss') {
                $query->where('user_id', (int) $request->input('user_id'));
            }
        }

        $sessions = $query->limit(200)->get()
            ->map(fn(ShiftSession $session) => $this->formatSession($session, false, $canViewProfit));

        return response()->json([
            'success' => true,
            'sessions' => $sessions,
            'viewer' => [
                'name' => $owner['name'],
                'type' => $owner['type'],
                'can_view_all' => $owner['can_view_all'],
                'can_view_profit' => $canViewProfit,
            ],
        ]);
    }

    /**
     * Full detail report for one shift.
     */
    public function report(ShiftSession $shiftSession)
    {
        $owner = $this->owner();
        $canViewProfit = $this->canViewProfit($owner);

        if (!$this->canView($shiftSession, $owner)) {
            abort(403, 'Huna ruhusa ya kuona shift hii.');
        }

        return response()->json([
            'success' => true,
            'session' => $this->formatSession($shiftSession, true, $canViewProfit),
            'viewer' => [
                'can_view_profit' => $canViewProfit,
            ],
        ]);
    }

    /**
     * Build a copyable or WhatsApp report. SMS sending was removed.
     */
    public function sendReport(Request $request, ShiftSession $shiftSession)
    {
        $owner = $this->owner();
        $canViewProfit = $this->canViewProfit($owner);

        if (!$this->canView($shiftSession, $owner)) {
            abort(403, 'Huna ruhusa ya kutuma ripoti ya shift hii.');
        }

        $validated = $request->validate([
            'channel' => 'required|in:whatsapp,copy',
            'phone' => 'nullable|string|max:20',
        ]);

        $summary = $shiftSession->summary();
        $message = $this->buildMessage($shiftSession, $summary, $canViewProfit);

        if ($validated['channel'] === 'copy') {
            return response()->json([
                'success' => true,
                'channel' => 'copy',
                'message' => $message,
            ]);
        }

        $phone = $validated['phone'] ?? $this->defaultBossPhone($shiftSession->company_id);
        $formattedPhone = $phone ? $this->formatPhone($phone) : null;

        return response()->json([
            'success' => true,
            'channel' => 'whatsapp',
            'phone' => $formattedPhone,
            'message' => $message,
            'wa_link' => $formattedPhone
                ? 'https://wa.me/' . $formattedPhone . '?text=' . rawurlencode($message)
                : 'https://wa.me/?text=' . rawurlencode($message),
        ]);
    }

    /* ------------------------------------------------------------------ */
    /* Message helpers                                                     */
    /* ------------------------------------------------------------------ */

    private function defaultBossPhone($companyId): ?string
    {
        $company = Company::find($companyId);

        return $company?->phone ?: null;
    }

    private function formatPhone(string $phone): ?string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (strlen($phone) < 9) {
            return null;
        }

        if (str_starts_with($phone, '0')) {
            $phone = '255' . substr($phone, 1);
        } elseif (str_starts_with($phone, '7') || str_starts_with($phone, '6')) {
            $phone = '255' . $phone;
        }

        return $phone;
    }

    private function buildMessage(ShiftSession $session, array $summary, bool $includeProfit = true): string
    {
        $companyName = $session->company->company_name ?? 'Duka';
        $totals = $summary['totals'];
        $products = $summary['products'];

        $lines = [];
        $lines[] = strtoupper($companyName);
        $lines[] = 'RIPOTI YA SHIFT';
        $lines[] = 'Mfanyakazi: ' . $session->owner_name;
        $lines[] = 'Kuanza: ' . optional($session->started_at)->format('d/m/Y H:i');

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
            $difference = $totals['cash_difference'] ?? 0;
            $lines[] = 'Tofauti: Tsh ' . number_format($difference, 0);
        }

        if (!empty($products)) {
            $lines[] = '--------------------------';
            $lines[] = 'BIDHAA ZILIZOUZWA:';
            foreach (array_slice($products, 0, 8) as $product) {
                $lines[] = '- ' . $product['jina'] . ': ' . number_format($product['idadi'], 0)
                    . ' x Tsh ' . number_format($product['kiasi'], 0);
            }
            if (count($products) > 8) {
                $lines[] = '... na bidhaa ' . (count($products) - 8) . ' zaidi';
            }
        }

        if (!empty($summary['expenses'])) {
            $lines[] = '--------------------------';
            $lines[] = 'MATUMIZI:';
            foreach (array_slice($summary['expenses'], 0, 5) as $expense) {
                $lines[] = '- ' . ($expense['aina'] ?: 'Matumizi') . ': Tsh ' . number_format($expense['gharama'], 0);
            }
        }

        $lines[] = '--------------------------';
        $lines[] = 'MauzoSheetAI';

        return implode("\n", $lines);
    }
}
