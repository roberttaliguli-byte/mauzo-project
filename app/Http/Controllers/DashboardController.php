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
        $today = Carbon::today();

        // Determine which guard is logged in
        if (Auth::guard('mfanyakazi')->check()) {
            $user = Auth::guard('mfanyakazi')->user();
            $companyId = $user->company_id ?? null;
        } else {
            $user = Auth::user(); // default web guard (Boss)
            $companyId = $user->company_id ?? null;
            
        }

        if (!$companyId) {
            abort(403, 'Company not found for user.');
        }

        // --- Stock / Inventory Metrics ---
        $jumlaBidhaa = Bidhaa::where('company_id', $companyId)->count();
        $jumlaIdadi = Bidhaa::where('company_id', $companyId)->sum('idadi');
        $thamani = Bidhaa::where('company_id', $companyId)
                         ->selectRaw('SUM(idadi * bei_nunua) as jumla')
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

        // --- Top-selling products ---
        $bidhaaTopSales = Bidhaa::where('company_id', $companyId)
                                ->withSum('mauzos', 'idadi')
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

        // --- Debt Summary ---
        $jumlaMadeni = Madeni::where('company_id', $companyId)->sum('baki');
        $idadiMadeni = Madeni::where('company_id', $companyId)->count();

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
