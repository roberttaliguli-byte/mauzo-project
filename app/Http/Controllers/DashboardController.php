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
            // Log out the user and redirect to login page
            if (Auth::guard('mfanyakazi')->check()) {
                Auth::guard('mfanyakazi')->logout();
            } else {
                Auth::logout();
            }
            
            return redirect()->route('login')->with('error', 'Company not found. Please login again.');
        }

        // --- Fetch Company ---
        $company = Company::find($companyId);
        
        if (!$company) {
            // Log out the user and redirect to login page
            if (Auth::guard('mfanyakazi')->check()) {
                Auth::guard('mfanyakazi')->logout();
            } else {
                Auth::logout();
            }
            
            return redirect()->route('login')->with('error', 'Company not found. Please login again.');
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
            if ($mauzo->bidhaa) {
                $buyingPrice = $mauzo->bidhaa->bei_nunua ?? 0;
                $sellingPrice = $mauzo->bei;
                $quantity = $mauzo->idadi;
                
                $totalDiscount = 0;
                if ($mauzo->punguzo_aina === 'bidhaa') {
                    $totalDiscount = $mauzo->punguzo * $quantity;
                } else {
                    $totalDiscount = $mauzo->punguzo;
                }
                
                $totalRevenueBeforeDiscount = $sellingPrice * $quantity;
                $totalRevenueAfterDiscount = $totalRevenueBeforeDiscount - $totalDiscount;
                $totalBuyingCost = $buyingPrice * $quantity;
                $profit = $totalRevenueAfterDiscount - $totalBuyingCost;
                $faidaMauzo += $profit;
            }
        }

        // Profit from debt repayments using FIFO method
        $faidaMarejesho = 0;
        $marejeshosLeo = Marejesho::with(['madeni.bidhaa'])
            ->whereHas('madeni', function($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
            ->whereDate('tarehe', $today)
            ->orderBy('tarehe', 'asc') // Process in chronological order
            ->get();

        // Track each debt's repayment progress
        $debtProgress = [];

        foreach ($marejeshosLeo as $marejesho) {
            if (isset($marejesho->madeni) && isset($marejesho->madeni->bidhaa)) {
                $debt = $marejesho->madeni;
                $debtId = $debt->id;
                $repaymentAmount = $marejesho->kiasi;

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

                // Stage 1: Recover cost first
                if (!$progress['is_cost_recovered']) {
                    $remainingToRecover = $progress['total_cost'] - $progress['recovered_so_far'];

                    if ($remainingAmount <= $remainingToRecover) {
                        // All goes to cost recovery
                        $progress['recovered_so_far'] += $remainingAmount;
                        // No profit from this repayment
                        $remainingAmount = 0;
                    } else {
                        // Part goes to cost recovery, rest is profit
                        $costPortion = $remainingToRecover;
                        $progress['recovered_so_far'] += $costPortion;
                        $progress['is_cost_recovered'] = true;

                        // Remaining is profit
                        $profitPortion = $remainingAmount - $costPortion;
                        $faidaMarejesho += $profitPortion;
                        $remainingAmount = 0;
                    }
                }

                // Stage 2: If cost already recovered, all is profit
                if ($progress['is_cost_recovered'] && $remainingAmount > 0) {
                    $faidaMarejesho += $remainingAmount;
                }
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
            'companyId',
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
            'faidaMarejesho',     // Profit from debt repayments (FIFO method)
            'jumlaFaida',         // Total profit today (faida ya leo)
            'allMauzos',          // For Jumla Kuu section
            'allMarejeshos',      // For Jumla Kuu section
            'totalMapato',        // Total income all time
            'totalMatumizi',      // Total expenses all time
            'jumlaKuu'            // Net income all time
        ));
    }
}