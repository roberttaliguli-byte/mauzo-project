<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Mauzo;
use App\Models\Bidhaa;
use App\Models\Matumizi;
use App\Models\Marejesho;
use App\Models\Madeni;
use Carbon\Carbon;
use App\Models\LoginHistory;
use App\Models\Manunuzi;

class UchambuziController extends Controller
{
    /**
     * Get authenticated user from either guard (boss or employee)
     */
    private function getAuthenticatedUser()
    {
        if (Auth::guard('mfanyakazi')->check()) {
            return Auth::guard('mfanyakazi')->user();
        }
        if (Auth::guard('web')->check()) {
            return Auth::guard('web')->user();
        }
        abort(403, 'Unauthorized - Please login first');
    }

    /**
     * Main dashboard page
     */
    public function index()
    {
        $user = $this->getAuthenticatedUser();
        $company = $user->company;

        if (!$company) {
            return redirect()->back()->with('error', 'User has no company assigned!');
        }

        $today = Carbon::today();

        // ---------- 1. Faida kwa Bidhaa (profit per product) ----------
        $faidaBidhaa = $company->bidhaa()
            ->with('mauzos')
            ->get()
            ->map(function ($bidhaa) {
                $totalSold = $bidhaa->mauzos->sum('idadi');
                $totalRevenue = $bidhaa->mauzos->sum('jumla');
                $totalCost = $totalSold * $bidhaa->bei_nunua;
                return [
                    'jina' => $bidhaa->jina,
                    'faida' => $totalRevenue - $totalCost,
                    'items_sold' => $totalSold,
                    'revenue' => $totalRevenue,
                    'cost' => $totalCost,
                ];
            })
            ->filter(fn($item) => $item['items_sold'] > 0)
            ->values()
            ->toArray();

        // ---------- 2. Faida kwa Siku (last 30 days) ----------
        $faidaSiku = $company->mauzo()
            ->with('bidhaa')
            ->whereDate('created_at', '>=', Carbon::now()->subDays(30))
            ->get()
            ->groupBy(fn($m) => $m->created_at->format('Y-m-d'))
            ->map(function ($mauzos, $date) {
                $totalRevenue = $mauzos->sum('jumla');
                $totalCost = $mauzos->sum(fn($m) => $m->idadi * $m->bidhaa->bei_nunua);
                return [
                    'day' => Carbon::parse($date)->format('d/m'),
                    'faida' => $totalRevenue - $totalCost,
                    'revenue' => $totalRevenue,
                    'cost' => $totalCost,
                ];
            })
            ->sortBy('day')
            ->values()
            ->toArray();

        // ---------- 3. Mauzo kwa Siku (last 30 days) ----------
        $mauzoSiku = $company->mauzo()
            ->whereDate('created_at', '>=', Carbon::now()->subDays(30))
            ->get()
            ->groupBy(fn($m) => $m->created_at->format('Y-m-d'))
            ->map(fn($mauzos, $date) => [
                'day' => Carbon::parse($date)->format('d/m'),
                'total' => $mauzos->sum('jumla')
            ])
            ->sortBy('day')
            ->values()
            ->toArray();

        // ---------- 4. Faida ya Marejesho (using FIFO method) ----------
        $marejesho = $this->calculateFifoProfit($company, Carbon::now()->subDays(30), Carbon::now());

        // ---------- 5. Mauzo Jumla kwa Bidhaa ----------
        $mauzo = $company->bidhaa()
            ->with('mauzos')
            ->get()
            ->map(fn($b) => [
                'jina' => $b->jina,
                'total' => $b->mauzos->sum('jumla'),
                'items_sold' => $b->mauzos->sum('idadi'),
            ])
            ->filter(fn($item) => $item['items_sold'] > 0)
            ->values()
            ->toArray();

        // ---------- 6. Mwenendo wa Biashara (summary for today/week/month/year) ----------
        $computeBetween = function (Carbon $from, Carbon $to) use ($company) {
            $fromDt = $from->copy()->startOfDay();
            $toDt = $to->copy()->endOfDay();

            $mapatoMauzo = $company->mauzo()->whereBetween('created_at', [$fromDt, $toDt])->sum('jumla');
            $mapatoMadeni = $company->marejeshos()->whereBetween('created_at', [$fromDt, $toDt])->sum('kiasi');
            $jumlaMapato = $mapatoMauzo + $mapatoMadeni;
            $jumlaMatumizi = $company->matumizi()->whereBetween('created_at', [$fromDt, $toDt])->sum('gharama');

            $costOfGoodsSold = $company->mauzo()
                ->with('bidhaa')
                ->whereBetween('created_at', [$fromDt, $toDt])
                ->get()
                ->sum(fn($m) => $m->idadi * $m->bidhaa->bei_nunua);

            $faidaMarejesho = $this->calculateFifoProfitTotal($company, $fromDt, $toDt);
            $faidaMauzo = $mapatoMauzo - $costOfGoodsSold;
            $faidaHalisi = ($faidaMauzo + $faidaMarejesho) - $jumlaMatumizi;
            $fedhaDroo = $jumlaMapato - $jumlaMatumizi;

            return [
                'mapato_mauzo' => (float) $mapatoMauzo,
                'mapato_madeni' => (float) $mapatoMadeni,
                'jumla_mapato' => (float) $jumlaMapato,
                'jumla_mat' => (float) $jumlaMatumizi,
                'gharama_bidhaa' => (float) $costOfGoodsSold,
                'faida_marejesho' => (float) $faidaMarejesho,
                'faida_mauzo' => (float) $faidaMauzo,
                'fedha_droo' => (float) $fedhaDroo,
                'faida_halisi' => (float) $faidaHalisi,
                'date' => $from->format('d/m/Y'),
            ];
        };

        $sikuData = $computeBetween($today, $today);
        $wikiStart = Carbon::now()->startOfWeek();
        $wikiEnd = Carbon::now()->endOfWeek();
        $wikiData = $computeBetween($wikiStart, $wikiEnd);
        $wikiData['date'] = $wikiStart->format('d/m') . ' - ' . $wikiEnd->format('d/m');
        $mweziStart = Carbon::now()->startOfMonth();
        $mweziEnd = Carbon::now()->endOfMonth();
        $mweziData = $computeBetween($mweziStart, $mweziEnd);
        $mweziData['date'] = $mweziStart->format('F Y');
        $mwakaStart = Carbon::now()->startOfYear();
        $mwakaEnd = Carbon::now()->endOfYear();
        $mwakaData = $computeBetween($mwakaStart, $mwakaEnd);
        $mwakaData['date'] = $mwakaStart->format('Y');

        $mwenendoSummary = [
            'siku' => $sikuData,
            'wiki' => $wikiData,
            'mwezi' => $mweziData,
            'mwaka' => $mwakaData,
        ];

        // ---------- 7. Thamani ya Kampuni ----------
        $thamaniBefore = $company->bidhaa->sum(fn($b) => $b->idadi * $b->bei_nunua);
        $thamaniAfter = $company->bidhaa->sum(fn($b) => $b->idadi * $b->bei_kuuza);
        $faida = $thamaniAfter - $thamaniBefore;
        $thamaniKampuniFormatted = 'Tsh ' . number_format($thamaniAfter, 0);

        // ---------- 8. Bidhaa List for table ----------
        $bidhaaList = $company->bidhaa()
            ->select('jina', 'aina', 'kipimo', 'idadi', 'bei_nunua', 'bei_kuuza')
            ->with('mauzos')
            ->get()
            ->map(function ($bidhaa) {
                $bidhaa->thamani_kabla = $bidhaa->idadi * $bidhaa->bei_nunua;
                $bidhaa->thamani_baada = $bidhaa->idadi * $bidhaa->bei_kuuza;
                $bidhaa->faida = $bidhaa->thamani_baada - $bidhaa->thamani_kabla;
                $bidhaa->total_sold = $bidhaa->mauzos->sum('idadi');
                return $bidhaa;
            });

        // ---------- HISTORIA DATA ----------
        // 1. Login History (last 5)
        $loginHistories = LoginHistory::where('company_id', $company->id)
            ->with('user')
            ->orderBy('login_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($login) {
                $userName = $login->user ? ($login->user->name ?? $login->user->username ?? 'Mtumiaji') : 'Mtumiaji aliyeondolewa';
                return (object) [
                    'user_name' => $userName,
                    'login_at'  => $login->login_at,
                    'logout_at' => $login->logout_at,
                ];
            });

        // 2. Recent Sales (5)
        $recentSales = Mauzo::where('company_id', $company->id)
            ->with(['bidhaa', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(fn($sale) => (object) [
                'type' => '💰 Mauzo',
                'description' => "Uuzaji wa " . ($sale->bidhaa->jina ?? 'Bidhaa') . " - " . number_format($sale->jumla, 0) . " TZS",
                'user_name' => $sale->user ? ($sale->user->name ?? $sale->user->username) : 'Mfumo',
                'created_at' => $sale->created_at,
            ]);

        // 3. Recent Purchases (5)
        $recentPurchases = Manunuzi::where('company_id', $company->id)
            ->with(['bidhaa', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(fn($p) => (object) [
                'type' => '📦 Manunuzi',
                'description' => "Ununuzi wa " . ($p->bidhaa->jina ?? 'Bidhaa') . " - " . number_format($p->bei, 0) . " TZS",
                'user_name' => $p->user ? ($p->user->name ?? $p->user->username) : 'Mfumo',
                'created_at' => $p->created_at,
            ]);

        // 4. Recent Expenses (5)
        $recentExpenses = Matumizi::where('company_id', $company->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(fn($e) => (object) [
                'type' => '📉 Matumizi',
                'description' => ($e->maelezo ?? 'Matumizi') . " - " . number_format($e->gharama, 0) . " TZS",
                'user_name' => $e->user ? ($e->user->name ?? $e->user->username) : 'Mfumo',
                'created_at' => $e->created_at,
            ]);

        // 5. Recent Debt Repayments (5)
        $recentRepayments = Marejesho::where('company_id', $company->id)
            ->with(['madeni.bidhaa', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(fn($r) => (object) [
                'type' => '🔄 Marejesho',
                'description' => ($r->madeni && $r->madeni->bidhaa ? 'Deni la ' . $r->madeni->bidhaa->jina : 'Deni') . " - " . number_format($r->kiasi, 0) . " TZS",
                'user_name' => $r->user ? ($r->user->name ?? $r->user->username) : 'Mfumo',
                'created_at' => $r->created_at,
            ]);

        // Merge all (optional)
        $recentActivities = collect()
            ->concat($recentSales)
            ->concat($recentPurchases)
            ->concat($recentExpenses)
            ->concat($recentRepayments)
            ->sortByDesc('created_at')
            ->values();

        return view('uchambuzi.index', compact(
            'faidaBidhaa', 'faidaSiku', 'mauzoSiku', 'marejesho', 'mauzo',
            'mwenendoSummary', 'thamaniKampuniFormatted', 'bidhaaList',
            'thamaniBefore', 'thamaniAfter', 'faida',
            'loginHistories', 'recentSales', 'recentPurchases',
            'recentExpenses', 'recentRepayments', 'recentActivities'
        ));
    }

    /**
     * Calculate FIFO profit for debt repayments (grouped by product)
     */
    private function calculateFifoProfit($company, $fromDate, $toDate)
    {
        $repayments = $company->marejeshos()
            ->with(['madeni' => fn($q) => $q->with('bidhaa')])
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->orderBy('tarehe', 'asc')
            ->get()
            ->filter(fn($r) => $r->madeni && $r->madeni->bidhaa);

        $debtProgress = [];
        $productProfits = [];

        foreach ($repayments as $marejesho) {
            $debt = $marejesho->madeni;
            $debtId = $debt->id;
            $productName = $debt->bidhaa->jina;
            $amount = $marejesho->kiasi;

            if (!isset($productProfits[$productName])) {
                $productProfits[$productName] = ['jina' => $productName, 'total' => 0, 'repayment_count' => 0];
            }

            if (!isset($debtProgress[$debtId])) {
                $buyingPrice = $debt->bidhaa->bei_nunua ?? 0;
                $quantity = $debt->idadi;
                $totalCost = $buyingPrice * $quantity;
                $debtProgress[$debtId] = [
                    'total_cost' => $totalCost,
                    'recovered_so_far' => 0,
                    'is_cost_recovered' => false
                ];
            }

            $progress = &$debtProgress[$debtId];
            $remaining = $amount;
            $profit = 0;

            if (!$progress['is_cost_recovered']) {
                $toRecover = $progress['total_cost'] - $progress['recovered_so_far'];
                if ($remaining <= $toRecover) {
                    $progress['recovered_so_far'] += $remaining;
                } else {
                    $costPortion = $toRecover;
                    $progress['recovered_so_far'] += $costPortion;
                    $progress['is_cost_recovered'] = true;
                    $profit = $remaining - $costPortion;
                }
            }
            if ($progress['is_cost_recovered'] && $remaining > 0) {
                $profit = $remaining;
            }

            if ($profit > 0) $productProfits[$productName]['total'] += $profit;
            $productProfits[$productName]['repayment_count']++;
        }

        return array_values($productProfits);
    }

    /**
     * Calculate total FIFO profit for a date range
     */
    private function calculateFifoProfitTotal($company, $fromDt, $toDt)
    {
        $repayments = $company->marejeshos()
            ->with(['madeni' => fn($q) => $q->with('bidhaa')])
            ->whereBetween('created_at', [$fromDt, $toDt])
            ->orderBy('tarehe', 'asc')
            ->get()
            ->filter(fn($r) => $r->madeni && $r->madeni->bidhaa);

        $debtProgress = [];
        $totalProfit = 0;

        foreach ($repayments as $marejesho) {
            $debt = $marejesho->madeni;
            $debtId = $debt->id;
            $amount = $marejesho->kiasi;

            if (!isset($debtProgress[$debtId])) {
                $buyingPrice = $debt->bidhaa->bei_nunua ?? 0;
                $quantity = $debt->idadi;
                $totalCost = $buyingPrice * $quantity;
                $debtProgress[$debtId] = [
                    'total_cost' => $totalCost,
                    'recovered_so_far' => 0,
                    'is_cost_recovered' => false
                ];
            }

            $progress = &$debtProgress[$debtId];
            $remaining = $amount;

            if (!$progress['is_cost_recovered']) {
                $toRecover = $progress['total_cost'] - $progress['recovered_so_far'];
                if ($remaining <= $toRecover) {
                    $progress['recovered_so_far'] += $remaining;
                } else {
                    $costPortion = $toRecover;
                    $progress['recovered_so_far'] += $costPortion;
                    $progress['is_cost_recovered'] = true;
                    $totalProfit += ($remaining - $costPortion);
                }
            } else {
                $totalProfit += $remaining;
            }
        }
        return $totalProfit;
    }

    /**
     * AJAX endpoint – get all records for a specific activity type
     */
    public function getAllActivities(Request $request)
    {
        $user = $this->getAuthenticatedUser();
        $company = $user->company;
        $type = $request->query('type');

        $query = match($type) {
            'sales'      => Mauzo::where('company_id', $company->id)->with(['bidhaa', 'user'])->orderBy('created_at', 'desc'),
            'purchases'  => Manunuzi::where('company_id', $company->id)->with(['bidhaa', 'user'])->orderBy('created_at', 'desc'),
            'expenses'   => Matumizi::where('company_id', $company->id)->with('user')->orderBy('created_at', 'desc'),
            'repayments' => Marejesho::where('company_id', $company->id)->with(['madeni.bidhaa', 'user'])->orderBy('created_at', 'desc'),
            default => abort(400),
        };

        $items = $query->get()->map(fn($item) => match($type) {
            'sales' => [
                'description' => "Uuzaji wa " . ($item->bidhaa->jina ?? 'Bidhaa') . " - " . number_format($item->jumla, 0) . " TZS",
                'user_name'   => $item->user ? ($item->user->name ?? $item->user->username) : 'Mfumo',
                'created_at'  => $item->created_at->format('Y-m-d H:i:s'),
            ],
            'purchases' => [
                'description' => "Ununuzi wa " . ($item->bidhaa->jina ?? 'Bidhaa') . " - " . number_format($item->bei, 0) . " TZS",
                'user_name'   => $item->user ? ($item->user->name ?? $item->user->username) : 'Mfumo',
                'created_at'  => $item->created_at->format('Y-m-d H:i:s'),
            ],
            'expenses' => [
                'description' => ($item->maelezo ?? 'Matumizi') . " - " . number_format($item->gharama, 0) . " TZS",
                'user_name'   => $item->user ? ($item->user->name ?? $item->user->username) : 'Mfumo',
                'created_at'  => $item->created_at->format('Y-m-d H:i:s'),
            ],
            'repayments' => [
                'description' => ($item->madeni && $item->madeni->bidhaa ? 'Deni la ' . $item->madeni->bidhaa->jina : 'Deni') . " - " . number_format($item->kiasi, 0) . " TZS",
                'user_name'   => $item->user ? ($item->user->name ?? $item->user->username) : 'Mfumo',
                'created_at'  => $item->created_at->format('Y-m-d H:i:s'),
            ],
        });

        return response()->json($items);
    }

    /**
     * AJAX endpoint for custom date range summary (Mwenendo)
     */
    public function mwenendoRange(Request $request)
    {
        $request->validate([
            'from' => 'required|date',
            'to'   => 'required|date|after_or_equal:from',
        ]);

        $user = $this->getAuthenticatedUser();
        $company = $user->company;

        $from = Carbon::parse($request->query('from'))->startOfDay();
        $to   = Carbon::parse($request->query('to'))->endOfDay();

        $mapatoMauzo = $company->mauzo()->whereBetween('created_at', [$from, $to])->sum('jumla');
        $mapatoMadeni = $company->marejeshos()->whereBetween('created_at', [$from, $to])->sum('kiasi');
        $jumlaMapato = $mapatoMauzo + $mapatoMadeni;
        $jumlaMatumizi = $company->matumizi()->whereBetween('created_at', [$from, $to])->sum('gharama');

        $costOfGoodsSold = $company->mauzo()
            ->with('bidhaa')
            ->whereBetween('created_at', [$from, $to])
            ->get()
            ->sum(fn($m) => $m->idadi * $m->bidhaa->bei_nunua);

        $faidaMarejesho = $this->calculateFifoProfitTotal($company, $from, $to);
        $faidaMauzo = $mapatoMauzo - $costOfGoodsSold;
        $faidaHalisi = ($faidaMauzo + $faidaMarejesho) - $jumlaMatumizi;
        $fedhaDroo = $jumlaMapato - $jumlaMatumizi;

        return response()->json([
            'mapato_mauzo' => (float) $mapatoMauzo,
            'mapato_madeni' => (float) $mapatoMadeni,
            'jumla_mapato' => (float) $jumlaMapato,
            'jumla_mat' => (float) $jumlaMatumizi,
            'gharama_bidhaa' => (float) $costOfGoodsSold,
            'faida_marejesho' => (float) $faidaMarejesho,
            'faida_mauzo' => (float) $faidaMauzo,
            'fedha_droo' => (float) $fedhaDroo,
            'faida_halisi' => (float) $faidaHalisi,
        ]);
    }
}