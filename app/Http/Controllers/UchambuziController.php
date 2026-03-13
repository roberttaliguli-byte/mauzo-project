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
use Illuminate\Support\Facades\DB;

class UchambuziController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $company = $user->company;

        if (!$company) {
            return redirect()->back()->with('error', 'User has no company assigned!');
        }

        $today = Carbon::today();

        // 1️⃣ Faida Kwa Bidhaa - REAL profit from sold items only
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
            ->filter(fn($item) => $item['items_sold'] > 0) // Only show products that have been sold
            ->values()
            ->toArray();

        // 2️⃣ Faida Kwa Siku (Last 30 days) - REAL daily profit
        $faidaSiku = $company->mauzo()
            ->with('bidhaa')
            ->whereDate('created_at', '>=', Carbon::now()->subDays(30))
            ->get()
            ->groupBy(function($mauzo) {
                return $mauzo->created_at->format('Y-m-d');
            })
            ->map(function ($mauzos, $date) {
                $totalRevenue = $mauzos->sum('jumla');
                $totalCost = $mauzos->sum(function($mauzo) {
                    return $mauzo->idadi * $mauzo->bidhaa->bei_nunua;
                });
                
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

        // 3️⃣ Mauzo Kwa Siku (Last 30 days) - Daily sales only
        $mauzoSiku = $company->mauzo()
            ->whereDate('created_at', '>=', Carbon::now()->subDays(30))
            ->get()
            ->groupBy(function($mauzo) {
                return $mauzo->created_at->format('Y-m-d');
            })
            ->map(function ($mauzos, $date) {
                return [
                    'day' => Carbon::parse($date)->format('d/m'),
                    'total' => $mauzos->sum('jumla')
                ];
            })
            ->sortBy('day')
            ->values()
            ->toArray();

        // 4️⃣ Faida ya Marejesho - Profit from debt repayments using FIFO method
        $marejesho = $this->calculateFifoProfit($company, Carbon::now()->subDays(30), Carbon::now());

        // 5️⃣ Mauzo Jumla kwa Bidhaa - Total sales per product
        $mauzo = $company->bidhaa()
            ->with('mauzos')
            ->get()
            ->map(function ($bidhaa) {
                return [
                    'jina' => $bidhaa->jina,
                    'total' => $bidhaa->mauzos->sum('jumla'),
                    'items_sold' => $bidhaa->mauzos->sum('idadi'),
                ];
            })
            ->filter(fn($item) => $item['items_sold'] > 0)
            ->values()
            ->toArray();

        // 6️⃣ Mwenendo wa Biashara - Business trends with FIFO method
        $computeBetween = function (Carbon $from, Carbon $to) use ($company) {
            $fromDt = $from->copy()->startOfDay();
            $toDt = $to->copy()->endOfDay();

            // Sales revenue
            $mapatoMauzo = $company->mauzo()
                ->whereBetween('created_at', [$fromDt, $toDt])
                ->sum('jumla');

            // Debt repayments amount
            $mapatoMadeni = $company->marejeshos()
                ->whereBetween('created_at', [$fromDt, $toDt])
                ->sum('kiasi');

            // Total income
            $jumlaMapato = $mapatoMauzo + $mapatoMadeni;

            // Expenses
            $jumlaMatumizi = $company->matumizi()
                ->whereBetween('created_at', [$fromDt, $toDt])
                ->sum('gharama');

            // COST OF GOODS SOLD for regular sales
            $costOfGoodsSold = $company->mauzo()
                ->with('bidhaa')
                ->whereBetween('created_at', [$fromDt, $toDt])
                ->get()
                ->sum(function($mauzo) {
                    return $mauzo->idadi * $mauzo->bidhaa->bei_nunua;
                });

            // Profit from debt repayments using FIFO method
            $faidaMarejesho = $this->calculateFifoProfitTotal($company, $fromDt, $toDt);

            // Gross profit from sales (Revenue - Cost of Goods Sold)
            $faidaMauzo = $mapatoMauzo - $costOfGoodsSold;

            // Total profit (Sales profit + Debt repayment profit - Expenses)
            $faidaHalisi = ($faidaMauzo + $faidaMarejesho) - $jumlaMatumizi;

            // Cash flow (Total income - Expenses)
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

        // Today
        $sikuData = $computeBetween($today, $today);

        // This week
        $wikiStart = Carbon::now()->startOfWeek();
        $wikiEnd = Carbon::now()->endOfWeek();
        $wikiData = $computeBetween($wikiStart, $wikiEnd);
        $wikiData['date'] = $wikiStart->format('d/m') . ' - ' . $wikiEnd->format('d/m');

        // This month
        $mweziStart = Carbon::now()->startOfMonth();
        $mweziEnd = Carbon::now()->endOfMonth();
        $mweziData = $computeBetween($mweziStart, $mweziEnd);
        $mweziData['date'] = $mweziStart->format('F Y');

        // This year
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

        // 7️⃣ Thamani ya Kampuni - Company value (current stock)
        $thamaniBefore = $company->bidhaa->sum(fn($b) => $b->idadi * $b->bei_nunua);
        $thamaniAfter = $company->bidhaa->sum(fn($b) => $b->idadi * $b->bei_kuuza);
        $faida = $thamaniAfter - $thamaniBefore;
        $thamaniKampuniFormatted = 'Tsh ' . number_format($thamaniAfter, 0);

        // 8️⃣ Bidhaa List for table
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

        return view('uchambuzi.index', compact(
            'faidaBidhaa',
            'faidaSiku',
            'mauzoSiku',
            'marejesho',
            'mauzo',
            'mwenendoSummary',
            'thamaniKampuniFormatted',
            'bidhaaList',
            'thamaniBefore',
            'thamaniAfter',
            'faida',
        ));
    }

    /**
     * Calculate FIFO profit for debt repayments grouped by product
     */
    private function calculateFifoProfit($company, $fromDate, $toDate)
    {
        // Get all repayments in date range with their debts
        $repayments = $company->marejeshos()
            ->with(['madeni' => function($query) {
                $query->with('bidhaa');
            }])
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->orderBy('tarehe', 'asc')
            ->get()
            ->filter(function($marejesho) {
                return $marejesho->madeni && $marejesho->madeni->bidhaa;
            });

        // Track each debt's progress
        $debtProgress = [];
        $productProfits = [];

        foreach ($repayments as $marejesho) {
            $debt = $marejesho->madeni;
            $debtId = $debt->id;
            $productName = $debt->bidhaa->jina;
            $repaymentAmount = $marejesho->kiasi;

            // Initialize product profit tracking
            if (!isset($productProfits[$productName])) {
                $productProfits[$productName] = [
                    'jina' => $productName,
                    'total' => 0,
                    'repayment_count' => 0
                ];
            }

            // Initialize debt tracking if not exists
            if (!isset($debtProgress[$debtId])) {
                $buyingPrice = $debt->bidhaa->bei_nunua ?? 0;
                $quantity = $debt->idadi;
                $totalCost = $buyingPrice * $quantity;
                $totalSellingPrice = $debt->jumla;

                $debtProgress[$debtId] = [
                    'total_cost' => $totalCost,
                    'total_selling' => $totalSellingPrice,
                    'recovered_so_far' => 0,
                    'is_cost_recovered' => false
                ];
            }

            $progress = &$debtProgress[$debtId];
            $remainingAmount = $repaymentAmount;
            $profitFromThis = 0;

            // Stage 1: Recover cost first
            if (!$progress['is_cost_recovered']) {
                $remainingToRecover = $progress['total_cost'] - $progress['recovered_so_far'];

                if ($remainingAmount <= $remainingToRecover) {
                    // All goes to cost recovery
                    $progress['recovered_so_far'] += $remainingAmount;
                    $profitFromThis = 0;
                    $remainingAmount = 0;
                } else {
                    // Part goes to cost recovery, rest is profit
                    $costPortion = $remainingToRecover;
                    $progress['recovered_so_far'] += $costPortion;
                    $progress['is_cost_recovered'] = true;

                    $profitPortion = $remainingAmount - $costPortion;
                    $profitFromThis = $profitPortion;
                    $remainingAmount = 0;
                }
            }

            // Stage 2: If cost already recovered, all is profit
            if ($progress['is_cost_recovered'] && $remainingAmount > 0) {
                $profitFromThis = $remainingAmount;
            }

            // Add to product total
            if ($profitFromThis > 0) {
                $productProfits[$productName]['total'] += $profitFromThis;
            }
            
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
            ->with(['madeni' => function($query) {
                $query->with('bidhaa');
            }])
            ->whereBetween('created_at', [$fromDt, $toDt])
            ->orderBy('tarehe', 'asc')
            ->get()
            ->filter(function($marejesho) {
                return $marejesho->madeni && $marejesho->madeni->bidhaa;
            });

        $debtProgress = [];
        $totalProfit = 0;

        foreach ($repayments as $marejesho) {
            $debt = $marejesho->madeni;
            $debtId = $debt->id;
            $repaymentAmount = $marejesho->kiasi;

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
            $remainingAmount = $repaymentAmount;

            // Stage 1: Recover cost first
            if (!$progress['is_cost_recovered']) {
                $remainingToRecover = $progress['total_cost'] - $progress['recovered_so_far'];

                if ($remainingAmount <= $remainingToRecover) {
                    $progress['recovered_so_far'] += $remainingAmount;
                } else {
                    $costPortion = $remainingToRecover;
                    $progress['recovered_so_far'] += $costPortion;
                    $progress['is_cost_recovered'] = true;

                    $profitPortion = $remainingAmount - $costPortion;
                    $totalProfit += $profitPortion;
                }
            } 
            // Stage 2: If cost already recovered, all is profit
            else if ($progress['is_cost_recovered']) {
                $totalProfit += $remainingAmount;
            }
        }

        return $totalProfit;
    }

    public function mwenendoRange(Request $request)
    {
        $request->validate([
            'from' => 'required|date',
            'to' => 'required|date|after_or_equal:from',
        ]);

        $company = Auth::user()->company;
        $from = Carbon::parse($request->query('from'))->startOfDay();
        $to = Carbon::parse($request->query('to'))->endOfDay();

        // Sales revenue
        $mapatoMauzo = $company->mauzo()
            ->whereBetween('created_at', [$from, $to])
            ->sum('jumla');

        // Debt repayments amount
        $mapatoMadeni = $company->marejeshos()
            ->whereBetween('created_at', [$from, $to])
            ->sum('kiasi');

        // Total income
        $jumlaMapato = $mapatoMauzo + $mapatoMadeni;

        // Expenses
        $jumlaMatumizi = $company->matumizi()
            ->whereBetween('created_at', [$from, $to])
            ->sum('gharama');

        // COST OF GOODS SOLD for regular sales
        $costOfGoodsSold = $company->mauzo()
            ->with('bidhaa')
            ->whereBetween('created_at', [$from, $to])
            ->get()
            ->sum(function($mauzo) {
                return $mauzo->idadi * $mauzo->bidhaa->bei_nunua;
            });

        // Profit from debt repayments using FIFO method
        $faidaMarejesho = $this->calculateFifoProfitTotal($company, $from, $to);

        // Gross profit from sales (Revenue - Cost of Goods Sold)
        $faidaMauzo = $mapatoMauzo - $costOfGoodsSold;

        // Total profit (Sales profit + Debt repayment profit - Expenses)
        $faidaHalisi = ($faidaMauzo + $faidaMarejesho) - $jumlaMatumizi;

        // Cash flow (Total income - Expenses)
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