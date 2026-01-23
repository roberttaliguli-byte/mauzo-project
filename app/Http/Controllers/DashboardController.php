<?php

namespace App\Http\Controllers;

use App\Services\GeneralReportService;
use App\Models\Bidhaa;
use App\Models\Mauzo;
use App\Models\Manunuzi;
use App\Models\Matumizi;
use App\Models\Madeni;
use App\Models\Marejesho;
use App\Models\Company;
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

        // --- Fetch Company ---
        $company = Company::find($companyId);

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

        // --- Today Sales (with discount) ---
        $mauzoLeo = Mauzo::whereHas('bidhaa', fn($q) => $q->where('company_id', $companyId))
                          ->whereDate('created_at', $today)
                          ->sum(DB::raw('(bei - punguzo) * idadi'));

        // --- Today Debt Payments (Madeni) ---
        $mapatoMadeni = Marejesho::whereHas('madeni', function($q) use ($companyId) {
                             $q->where('company_id', $companyId);
                         })
                         ->whereDate('tarehe', $today)
                         ->sum('kiasi');

        // --- Mapato Leo (Total Revenue: Sales + Debt Payments) ---
        $mapatoLeo = $mauzoLeo + $mapatoMadeni;

        // --- Today Expenses ---
        $matumiziLeo = Matumizi::where('company_id', $companyId)
                                ->whereDate('created_at', $today)
                                ->sum('gharama');

        // --- Fedha Leo (Net Cash: Revenue - Expenses) ---
        $fedhaLeo = $mapatoLeo - $matumiziLeo;

        // --- PROFIT CALCULATIONS FOR FAIDA YA LEO ---
        // Profit from cash sales
        $faidaMauzo = 0;
        $mauzosLeo = Mauzo::with('bidhaa')
            ->whereHas('bidhaa', function($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
            ->whereDate('created_at', $today)
            ->get();

        foreach ($mauzosLeo as $mauzo) {
            $buyingPrice = $mauzo->bidhaa->bei_nunua ?? 0;
            $sellingPrice = $mauzo->bei;
            $quantity = $mauzo->idadi;
            
            $actualDiscount = $mauzo->punguzo;
            if ($mauzo->punguzo_aina === 'bidhaa') {
                $actualDiscount = $mauzo->punguzo * $quantity;
            }
            
            $faidaMauzo += ($sellingPrice - $buyingPrice) * $quantity - $actualDiscount;
        }

        // Profit from debt repayments
        $faidaMarejesho = 0;
        $marejeshosLeo = Marejesho::with(['madeni.bidhaa'])
            ->whereHas('madeni', function($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
            ->whereDate('tarehe', $today)
            ->get();

        foreach ($marejeshosLeo as $marejesho) {
            if ($marejesho->madeni && $marejesho->madeni->bidhaa) {
                $debt = $marejesho->madeni;
                
                // SIMPLE: Profit = jumla (discounted price) - buying cost
                $buyingPrice = $debt->bidhaa->bei_nunua ?? 0;
                $quantity = $debt->idadi;
                $totalBuyingCost = $buyingPrice * $quantity;
                $actualSellingPrice = $debt->jumla;
                
                $profit = $actualSellingPrice - $totalBuyingCost;
                $faidaMarejesho += $profit;
            }
        }

        // Total profit today
        $jumlaFaida = $faidaMauzo + $faidaMarejesho;
        
        // Net profit today (profit - expenses)
        $faidaHalisiLeo = $jumlaFaida - $matumiziLeo;

        // --- For Jumla Kuu section (All-time calculations) ---
        $allMauzos = Mauzo::whereHas('bidhaa', fn($q) => $q->where('company_id', $companyId))->get();
        $allMarejeshos = Marejesho::whereHas('madeni', function($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })->get();
        
        // Total income all time = Cash sales + All debt repayments
        $totalCashSales = $allMauzos->sum('jumla');
        $totalDebtRepayments = $allMarejeshos->sum('kiasi');
        $totalMapato = $totalCashSales + $totalDebtRepayments;
        
        // Total expenses all time
        $totalMatumizi = Matumizi::where('company_id', $companyId)->sum('gharama');
        
        // Net income all time = Total income - Total expenses
        $jumlaKuu = $totalMapato - $totalMatumizi;

        // --- Debt Summary ---
        $jumlaMadeni = Madeni::where('company_id', $companyId)->sum('baki');
        $idadiMadeni = Madeni::where('company_id', $companyId)->count();

        // Pass all variables to the view
        return view('dashboard.index', compact(
            'company',
            'jumlaBidhaa',
            'jumlaIdadi',
            'thamani',
            'bidhaaZilizopo',
            'bidhaaZimeisha',
            'bidhaaKaribiaKuisha',
            'bidhaaTopSales',
            'mapatoLeo',           // Total revenue today
            'matumiziLeo',         // Expenses today
            'fedhaLeo',            // Net cash today
            'faidaHalisiLeo',      // Net profit today
            'jumlaMadeni',
            'idadiMadeni',
            // ADD THESE NEW VARIABLES FOR FINANCIAL OVERVIEW:
            'mauzoLeo',           // Today's cash sales (for Mapato section)
            'mapatoMadeni',       // Today's debt repayments (for Mapato section)
            'faidaMauzo',         // Profit from cash sales
            'faidaMarejesho',     // Profit from debt repayments  
            'jumlaFaida',         // Total profit today (faida ya leo)
            'allMauzos',          // For Jumla Kuu section
            'allMarejeshos',      // For Jumla Kuu section
            'totalMapato',        // Total income all time
            'totalMatumizi',      // Total expenses all time
            'jumlaKuu'            // Net income all time
        ));
    }
}