<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Mauzo;
use App\Models\Bidhaa;
use App\Models\Matumizi;
use App\Models\Marejesho;
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

        // 4️⃣ Gharama ya Bidhaa Zilizouzwa - Cost of goods sold
        $gharama = $company->bidhaa()
            ->with('mauzos')
            ->get()
            ->map(function ($bidhaa) {
                $totalSold = $bidhaa->mauzos->sum('idadi');
                
                return [
                    'jina' => $bidhaa->jina,
                    'total' => $totalSold * $bidhaa->bei_nunua,
                    'items_sold' => $totalSold,
                ];
            })
            ->filter(fn($item) => $item['items_sold'] > 0)
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

            // Debt repayments
            $mapatoMadeni = $company->marejeshos()
                ->whereBetween('tarehe', [$fromDt->toDateString(), $toDt->toDateString()])
                ->sum('kiasi');

            // Total income
            $jumlaMapato = $mapatoMauzo + $mapatoMadeni;

            // Expenses
            $jumlaMatumizi = $company->matumizi()
                ->whereBetween('created_at', [$fromDt, $toDt])
                ->sum('gharama');

            // COST OF GOODS SOLD
            $costOfGoodsSold = $company->mauzo()
                ->with('bidhaa')
                ->whereBetween('created_at', [$fromDt, $toDt])
                ->get()
                ->sum(function($mauzo) {
                    return $mauzo->idadi * $mauzo->bidhaa->bei_nunua;
                });

            // Gross profit (Revenue - Cost of Goods Sold)
            $faidaMauzo = $mapatoMauzo - $costOfGoodsSold;

            // Net profit (Gross profit - Expenses)
            $faidaHalisi = $faidaMauzo - $jumlaMatumizi;

            // Cash flow (Total income - Expenses)
            $fedhaDroo = $jumlaMapato - $jumlaMatumizi;

            return [
                'mapato_mauzo' => (float) $mapatoMauzo,
                'mapato_madeni' => (float) $mapatoMadeni,
                'jumla_mapato' => (float) $jumlaMapato,
                'jumla_mat' => (float) $jumlaMatumizi,
                'gharama_bidhaa' => (float) $costOfGoodsSold,
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
            'gharama',
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

        // Debt repayments
        $mapatoMadeni = $company->marejeshos()
            ->whereBetween('tarehe', [$from->toDateString(), $to->toDateString()])
            ->sum('kiasi');

        // Total income
        $jumlaMapato = $mapatoMauzo + $mapatoMadeni;

        // Expenses
        $jumlaMatumizi = $company->matumizi()
            ->whereBetween('created_at', [$from, $to])
            ->sum('gharama');

        // COST OF GOODS SOLD
        $costOfGoodsSold = $company->mauzo()
            ->with('bidhaa')
            ->whereBetween('created_at', [$from, $to])
            ->get()
            ->sum(function($mauzo) {
                return $mauzo->idadi * $mauzo->bidhaa->bei_nunua;
            });

        // Gross profit
        $faidaMauzo = $mapatoMauzo - $costOfGoodsSold;

        // Net profit
        $faidaHalisi = $faidaMauzo - $jumlaMatumizi;

        // Cash flow
        $fedhaDroo = $jumlaMapato - $jumlaMatumizi;

        return response()->json([
            'mapato_mauzo' => (float) $mapatoMauzo,
            'mapato_madeni' => (float) $mapatoMadeni,
            'jumla_mapato' => (float) $jumlaMapato,
            'jumla_mat' => (float) $jumlaMatumizi,
            'gharama_bidhaa' => (float) $costOfGoodsSold,
            'faida_mauzo' => (float) $faidaMauzo,
            'fedha_droo' => (float) $fedhaDroo,
            'faida_halisi' => (float) $faidaHalisi,
        ]);
    }
}