<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Mauzo;
use App\Models\Bidhaa;
use App\Models\Matumizi;
use App\Models\Madeni;
use App\Models\Marejesho;
use Carbon\Carbon;

class UchambuziController extends Controller
{
    /**
     * Show dashboard / uchambuzi index (filtered by user's company)
     */
    public function index()
    {
        $user = Auth::user();
        $company = $user->company;

        if (!$company) {
            return redirect()->back()->with('error', 'User has no company assigned!');
        }

        $today = Carbon::today();

        // ===============================
        // 1️⃣ Faida Kwa Bidhaa (profit per product)
        // ===============================
        $faidaBidhaa = $company->bidhaa()
            ->with('mauzos')
            ->get()
            ->map(function ($b) {
                $totalCost = $b->mauzos->sum(fn($m) => $m->idadi * $m->bidhaa->bei_nunua);
                $totalRevenue = $b->mauzos->sum('jumla');
                return [
                    'jina' => $b->jina,
                    'faida' => $totalRevenue - $totalCost,
                ];
            })->toArray();

        // ===============================
        // 2️⃣ Faida Kwa Siku (daily profit)
        // ===============================
        $faidaSiku = $company->mauzo()
            ->with('bidhaa')
            ->get()
            ->groupBy(fn($m) => $m->created_at->format('Y-m-d'))
            ->map(fn($items, $date) => [
                'day' => $date,
                'faida' => $items->sum(fn($m) => $m->jumla - ($m->idadi * $m->bidhaa->bei_nunua))
            ])
            ->values()
            ->toArray();

        // ===============================
        // 3️⃣ Mauzo Kwa Siku (daily sales)
        // ===============================
        $mauzoSiku = $company->mauzo()
            ->with('bidhaa')
            ->get()
            ->groupBy(fn($m) => $m->created_at->format('Y-m-d'))
            ->map(fn($items, $date) => [
                'day' => $date,
                'total' => $items->sum('jumla')
            ])
            ->values()
            ->toArray();

        // ===============================
        // 4️⃣ Gharama (total cost per product)
        // ===============================
        $gharama = $company->bidhaa()
            ->with('mauzos')
            ->get()
            ->map(fn($b) => [
                'jina' => $b->jina,
                'total' => $b->mauzos->sum(fn($m) => $m->idadi * $m->bidhaa->bei_nunua)
            ])->toArray();

        // ===============================
        // 5️⃣ Mauzo Jumla (total sales per product)
        // ===============================
        $mauzo = $company->bidhaa()
            ->with('mauzos')
            ->get()
            ->map(fn($b) => [
                'jina' => $b->jina,
                'total' => $b->mauzos->sum('jumla')
            ])->toArray();

        // ===============================
        // 6️⃣ Mwenendo wa Biashara (summary: siku/wiki/mwezi/mwaka)
        // ===============================
        $computeBetween = function (Carbon $from, Carbon $to) use ($company) {
            $fromDt = $from->startOfDay()->toDateTimeString();
            $toDt = $to->endOfDay()->toDateTimeString();

            $mapatoMauzo = $company->mauzo()->whereBetween('created_at', [$fromDt, $toDt])->sum('jumla');
            $mapatoMadeni = $company->marejeshos()->whereBetween('tarehe', [$from->toDateString(), $to->toDateString()])->sum('kiasi');
            $jumlaMapato = $mapatoMauzo + $mapatoMadeni;
            $jumlaMatumizi = $company->matumizi()->whereBetween('created_at', [$fromDt, $toDt])->sum('gharama');

            return [
                'mapato_mauzo' => (float) $mapatoMauzo,
                'mapato_madeni' => (float) $mapatoMadeni,
                'jumla_mapato' => (float) $jumlaMapato,
                'jumla_mat' => (float) $jumlaMatumizi,
                'faida_mauzo' => (float) $jumlaMapato,
                'fedha_droo' => (float) ($jumlaMapato - $jumlaMatumizi),
                'faida_halisi' => (float) ($jumlaMapato - $jumlaMatumizi),
            ];
        };

        $sikuData = $computeBetween($today, $today);
        $sikuData['date'] = $today->format('Y-m-d');

        $wikiStart = Carbon::now()->startOfWeek();
        $wikiEnd = Carbon::now()->endOfWeek();
        $wikiData = $computeBetween($wikiStart, $wikiEnd);
        $wikiData['date'] = $wikiStart->format('Y-m-d') . ' - ' . $wikiEnd->format('Y-m-d');

        $mweziStart = Carbon::now()->startOfMonth();
        $mweziEnd = Carbon::now()->endOfMonth();
        $mweziData = $computeBetween($mweziStart, $mweziEnd);
        $mweziData['date'] = $mweziStart->format('Y-m-d') . ' - ' . $mweziEnd->format('Y-m-d');

        $mwakaStart = Carbon::now()->startOfYear();
        $mwakaEnd = Carbon::now()->endOfYear();
        $mwakaData = $computeBetween($mwakaStart, $mwakaEnd);
        $mwakaData['date'] = $mwakaStart->format('Y-m-d') . ' - ' . $mwakaEnd->format('Y-m-d');

        $mwenendoSummary = [
            'siku' => $sikuData,
            'wiki' => $wikiData,
            'mwezi' => $mweziData,
            'mwaka' => $mwakaData,
            'today' => $sikuData,
        ];

// ===============================
// 7️⃣ Thamani ya Kampuni (company total value)
// ===============================

// Before sales (based on bei_nunua)
$thamaniBefore = $company->bidhaa->sum(fn($b) => $b->idadi * $b->bei_nunua);

// After sales (based on bei_kuuza)
$thamaniAfter = $company->bidhaa->sum(fn($b) => $b->idadi * $b->bei_kuuza);

// Faida (Profit)
$faida = $thamaniAfter - $thamaniBefore;

// Formatted total for top summary
$thamaniKampuniFormatted = 'Tsh ' . number_format($thamaniAfter, 2);

// ===============================
// 8️⃣ Bidhaa List (for table)
// ===============================
$bidhaaList = $company->bidhaa()
    ->select('jina', 'aina', 'kipimo', 'idadi', 'bei_nunua', 'bei_kuuza')
    ->get()
    ->map(function ($b) {
        $b->thamani_kabla = $b->idadi * $b->bei_nunua;
        $b->thamani_baada = $b->idadi * $b->bei_kuuza;
        $b->faida = $b->thamani_baada - $b->thamani_kabla;
        return $b;
    });

// ===============================
// Return to View
// ===============================

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

    /**
     * API endpoint: return mwenendo summary for a custom date range
     */
    public function mwenendoRange(Request $request)
    {
        $request->validate([
            'from' => 'required|date',
            'to' => 'required|date|after_or_equal:from',
        ]);

        $company = Auth::user()->company;
        $from = Carbon::parse($request->query('from'))->startOfDay();
        $to = Carbon::parse($request->query('to'))->endOfDay();

        $mapatoMauzo = $company->mauzo()->whereBetween('created_at', [$from, $to])->sum('jumla');
        $mapatoMadeni = $company->marejeshos()->whereBetween('tarehe', [$from, $to])->sum('kiasi');
        $jumlaMapato = $mapatoMauzo + $mapatoMadeni;
        $jumlaMatumizi = $company->matumizi()->whereBetween('created_at', [$from, $to])->sum('gharama');

        $faidaMauzo = $jumlaMapato;
        $fedhaDroo = $jumlaMapato - $jumlaMatumizi;
        $faidaHalisi = $jumlaMapato - $jumlaMatumizi;

        return response()->json([
            'mapato_mauzo' => (float) $mapatoMauzo,
            'mapato_madeni' => (float) $mapatoMadeni,
            'jumla_mapato' => (float) $jumlaMapato,
            'jumla_mat' => (float) $jumlaMatumizi,
            'faida_mauzo' => (float) $faidaMauzo,
            'fedha_droo' => (float) $fedhaDroo,
            'faida_halisi' => (float) $faidaHalisi,
        ]);
    }
}
