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

        // 4️⃣ Faida ya Marejesho - Profit from debt repayments
        $marejesho = $company->marejeshos()
            ->with(['madeni' => function($query) {
                $query->with('bidhaa');
            }])
            ->whereDate('created_at', '>=', Carbon::now()->subDays(30))
            ->get()
            ->filter(function($marejesho) {
                return $marejesho->madeni && $marejesho->madeni->bidhaa;
            })
            ->groupBy(function($marejesho) {
                return $marejesho->madeni->bidhaa->jina;
            })
            ->map(function ($repayments, $productName) {
                $totalProfit = $repayments->sum(function($marejesho) {
                    // Calculate profit from each repayment
                    $debt = $marejesho->madeni;
                    $buyingPrice = $debt->bidhaa->bei_nunua ?? 0;
                    $quantity = $debt->idadi;
                    
                    // Calculate actual discount if any
                    $actualDiscount = $debt->punguzo_aina === 'bidhaa'
                        ? $debt->punguzo * $quantity
                        : $debt->punguzo;
                    
                    // Selling price after discount (debt amount)
                    $sellingPrice = $debt->jumla;
                    
                    // Total buying cost
                    $totalBuyingCost = $buyingPrice * $quantity;
                    
                    // Profit = Selling price - Buying cost
                    return $sellingPrice - $totalBuyingCost;
                });
                
                return [
                    'jina' => $productName,
                    'total' => $totalProfit,
                    'repayment_count' => $repayments->count()
                ];
            })
            ->values()
            ->toArray();

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

        // 6️⃣ Mwenendo wa Biashara - Business trends
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

            // Profit from debt repayments
            $faidaMarejesho = $company->marejeshos()
                ->with(['madeni' => function($query) {
                    $query->with('bidhaa');
                }])
                ->whereBetween('created_at', [$fromDt, $toDt])
                ->get()
                ->filter(function($marejesho) {
                    return $marejesho->madeni && $marejesho->madeni->bidhaa;
                })
                ->sum(function($marejesho) {
                    $debt = $marejesho->madeni;
                    $buyingPrice = $debt->bidhaa->bei_nunua ?? 0;
                    $quantity = $debt->idadi;
                    
                    // Calculate actual discount if any
                    $actualDiscount = $debt->punguzo_aina === 'bidhaa'
                        ? $debt->punguzo * $quantity
                        : $debt->punguzo;
                    
                    // Selling price after discount (debt amount)
                    $sellingPrice = $debt->jumla;
                    
                    // Total buying cost
                    $totalBuyingCost = $buyingPrice * $quantity;
                    
                    // Profit = Selling price - Buying cost
                    return $sellingPrice - $totalBuyingCost;
                });

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
                'faida_marejesho' => (float) $faidaMarejesho, // ADDED: Profit from debt repayments
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
            'marejesho', // CHANGED: gharama to marejesho
            'mauzo',
            'mwenendoSummary',
            'thamaniKampuniFormatted',
            'bidhaaList',
            'thamaniBefore',
            'thamaniAfter',
            'faida',
        ));
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

        // Profit from debt repayments
        $faidaMarejesho = $company->marejeshos()
            ->with(['madeni' => function($query) {
                $query->with('bidhaa');
            }])
            ->whereBetween('created_at', [$from, $to])
            ->get()
            ->filter(function($marejesho) {
                return $marejesho->madeni && $marejesho->madeni->bidhaa;
            })
            ->sum(function($marejesho) {
                $debt = $marejesho->madeni;
                $buyingPrice = $debt->bidhaa->bei_nunua ?? 0;
                $quantity = $debt->idadi;
                
                // Calculate actual discount if any
                $actualDiscount = $debt->punguzo_aina === 'bidhaa'
                    ? $debt->punguzo * $quantity
                    : $debt->punguzo;
                
                // Selling price after discount (debt amount)
                $sellingPrice = $debt->jumla;
                
                // Total buying cost
                $totalBuyingCost = $buyingPrice * $quantity;
                
                // Profit = Selling price - Buying cost
                return $sellingPrice - $totalBuyingCost;
            });

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
            'faida_marejesho' => (float) $faidaMarejesho, // ADDED
            'faida_mauzo' => (float) $faidaMauzo,
            'fedha_droo' => (float) $fedhaDroo,
            'faida_halisi' => (float) $faidaHalisi,
        ]);
    }
}