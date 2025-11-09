<?php

namespace App\Http\Controllers;

use App\Models\Bidhaa;
use App\Models\Mauzo;
use App\Models\Manunuzi;
use App\Models\Matumizi;
use App\Models\Madeni;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $companyId = Auth::user()->company_id;
        $today = Carbon::today();

        // --- Stock / Inventory Metrics (all-time) ---
        $jumlaBidhaa = Bidhaa::where('company_id', $companyId)->count();
        $jumlaIdadi = Bidhaa::where('company_id', $companyId)->sum('idadi');
        $thamani = Bidhaa::where('company_id', $companyId)
                         ->select(DB::raw('SUM(idadi * bei_nunua) as jumla'))
                         ->value('jumla');

        $bidhaaZilizopo = Bidhaa::where('company_id', $companyId)
                                ->where('idadi', '>', 0)
                                ->count();

        $bidhaaZimeisha = Bidhaa::where('company_id', $companyId)
                                 ->where('idadi', 0)
                                 ->count();

        $bidhaaKaribiaKuisha = Bidhaa::where('company_id', $companyId)
                                     ->where('idadi', '<', 10)
                                     ->count();

// --- Top-selling products (all-time) ---
$bidhaaTopSales = Bidhaa::where('company_id', $companyId)
                        ->withSum('mauzos', 'idadi') // sum all sales for each product
                        ->orderByDesc('mauzos_sum_idadi')
                        ->take(3)
                        ->get();


                        
        // --- Today Financial Metrics ---
        $mauzoLeo = Mauzo::whereHas('bidhaa', fn($q) => $q->where('company_id', $companyId))
                          ->whereDate('created_at', $today)
                          ->sum(DB::raw('idadi * bei'));

        $manunuziLeo = Manunuzi::where('company_id', $companyId)
                                ->whereDate('created_at', $today)
                                ->sum(DB::raw('idadi * bei'));

        $matumiziLeo = Matumizi::where('company_id', $companyId)
                                ->whereDate('created_at', $today)
                                ->sum('gharama');

        $faidaHalisiLeo = $mauzoLeo - ($manunuziLeo + $matumiziLeo);

        // --- Madeni Summary ---
$jumlaMadeni = Madeni::where('company_id', $companyId)
                      ->sum('baki'); // total outstanding debt

$idadiMadeni = Madeni::where('company_id', $companyId)
                      ->count(); // total number of debt records

        return view('dashboard.index', compact(
            'jumlaBidhaa',
            'jumlaIdadi',
            'thamani',
            'bidhaaZilizopo',
            'bidhaaZimeisha',
            'bidhaaKaribiaKuisha',
            'bidhaaTopSales',
            'mauzoLeo',
            'manunuziLeo',
            'matumiziLeo',
            'faidaHalisiLeo',
            'jumlaMadeni',
            'idadiMadeni'
        ));
    }
}
