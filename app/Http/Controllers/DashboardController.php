<?php

namespace App\Http\Controllers;

use App\Models\Bidhaa;
use App\Models\Mauzo;
use App\Models\Matumizi;
use App\Models\Madeni;
use App\Models\Marejesho;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with all statistics
     */
    public function index()
    {
        // Get authenticated user and company ID
        $authData = $this->getAuthenticatedUserAndCompany();
        
        if (!$authData['success']) {
            return redirect()->route('login')->with('error', $authData['message']);
        }
        
        $companyId = $authData['company_id'];
        $company = $authData['company'];
        
        $today = Carbon::today();
        $todayStart = $today->copy()->startOfDay();
        $todayEnd = $today->copy()->endOfDay();

        // Modernized: DB aggregates + cache (60s) — same logic, no full table hydrates
        // Cache key is per-company per-day, auto invalidates on next day / after 60s
        $cacheKey = "dashboard:{$companyId}:".$today->format('Y-m-d');
        $cached = Cache::remember($cacheKey, 60, function () use ($companyId, $todayStart, $todayEnd) {
            // Today's aggregates — use whereBetween (index-friendly) instead of whereDate
            $mapatoMauzo = Mauzo::where('company_id', $companyId)
                ->whereBetween('created_at', [$todayStart, $todayEnd])
                ->sum('jumla');

            $mapatoMadeni = Marejesho::where('company_id', $companyId)
                ->whereBetween('tarehe', [$todayStart, $todayEnd])
                ->sum('kiasi');

            $matumiziLeo = Matumizi::where('company_id', $companyId)
                ->whereBetween('created_at', [$todayStart, $todayEnd])
                ->sum('gharama');

            // All-time totals via DB SUM (no ->get() hydration)
            $totalMauzoSum = Mauzo::where('company_id', $companyId)->sum('jumla');
            $totalMarejeshoSum = Marejesho::where('company_id', $companyId)->sum('kiasi');
            $totalMatumiziSum = Matumizi::where('company_id', $companyId)->sum('gharama');

            return compact('mapatoMauzo','mapatoMadeni','matumiziLeo','totalMauzoSum','totalMarejeshoSum','totalMatumiziSum');
        });

        $mapatoMauzo = (float) ($cached['mapatoMauzo'] ?? 0);
        $mapatoMadeni = (float) ($cached['mapatoMadeni'] ?? 0);
        $mapatoLeo = $mapatoMauzo + $mapatoMadeni;
        $matumiziLeo = (float) ($cached['matumiziLeo'] ?? 0);
        $fedhaLeo = $mapatoLeo - $matumiziLeo;

        // Profit: still needs row-level FIFO, but only today's slice (small, indexed)
        // Keep original methods for identical FIFO logic — now using optimized queries
        $todaysMauzos = $this->getTodaysMauzos($companyId, $today);
        $todaysMarejeshos = $this->getTodaysMarejeshos($companyId, $today);
        $todaysMatumizi = $this->getTodaysMatumizi($companyId, $today);

        // Backward-compat collections for views (empty if not needed, keep vars present)
        $allTimeMauzos = collect();
        $allTimeMarejeshos = collect();
        $allMatumizi = collect();

        $faidaMauzo = $this->calculateCashSalesProfit($todaysMauzos);
        $faidaMarejesho = $this->calculateDebtRepaymentProfit($todaysMarejeshos);
        $jumlaFaida = $faidaMauzo + $faidaMarejesho;
        $faidaHalisiLeo = $jumlaFaida - $matumiziLeo;

        // Inventory metrics — DB counts (identical logic, no ->get() full hydrate)
        $inventoryMetrics = $this->getInventoryMetrics($companyId);

        // Top selling products (already DB-optimized)
        $bidhaaTopSales = $this->getTopSellingProducts($companyId);

        // Debt summary (already DB sums)
        $debtSummary = $this->getDebtSummary($companyId);

        // All time totals from cached aggregates (same as ->sum on full collections)
        $totalMapato = (float) ($cached['totalMauzoSum'] ?? 0) + (float) ($cached['totalMarejeshoSum'] ?? 0);
        $totalMatumizi = (float) ($cached['totalMatumiziSum'] ?? 0);
        $jumlaKuu = $totalMapato - $totalMatumizi;
        
        return view('dashboard.index', array_merge(
            compact(
                'company',
                'companyId',
                'todaysMauzos',
                'todaysMarejeshos',
                'todaysMatumizi',
                'allTimeMauzos',
                'allTimeMarejeshos',
                'allMatumizi',
                'mapatoLeo',
                'mapatoMauzo',
                'mapatoMadeni',
                'matumiziLeo',
                'fedhaLeo',
                'faidaMauzo',
                'faidaMarejesho',
                'jumlaFaida',
                'faidaHalisiLeo',
                'jumlaKuu',
                'totalMapato',
                'totalMatumizi'
            ),
            $inventoryMetrics,
            compact('bidhaaTopSales'),
            $debtSummary
        ));
    }
    
    /**
     * Get authenticated user and company information
     */
    private function getAuthenticatedUserAndCompany()
    {
        // Determine which guard is logged in
        if (Auth::guard('mfanyakazi')->check()) {
            $user = Auth::guard('mfanyakazi')->user();
            $companyId = $user->company_id ?? null;
        } else {
            $user = Auth::user();
            $companyId = $user->company_id ?? null;
        }
        
        if (!$companyId) {
            $this->logoutUser();
            return ['success' => false, 'message' => 'Company not found. Please login again.'];
        }
        
        $company = Company::find($companyId);
        
        if (!$company) {
            $this->logoutUser();
            return ['success' => false, 'message' => 'Company not found. Please login again.'];
        }
        
        return [
            'success' => true,
            'company_id' => $companyId,
            'company' => $company,
            'user' => $user
        ];
    }
    
    /**
     * Log out the current user
     */
    private function logoutUser()
    {
        if (Auth::guard('mfanyakazi')->check()) {
            Auth::guard('mfanyakazi')->logout();
        } else {
            Auth::logout();
        }
    }
    
    /**
     * Get today's sales — modernized to use company_id directly + whereBetween (index-friendly)
     * Logic identical: only today's sales for profit calc
     */
    private function getTodaysMauzos($companyId, $today)
    {
        $start = $today instanceof Carbon ? $today->copy()->startOfDay() : Carbon::parse($today)->startOfDay();
        $end = $today instanceof Carbon ? $today->copy()->endOfDay() : Carbon::parse($today)->endOfDay();
        return Mauzo::where('company_id', $companyId)
            ->with('bidhaa:id,jina,bei_nunua')
            ->whereBetween('created_at', [$start, $end])
            ->select('id','company_id','bidhaa_id','idadi','bei','punguzo','punguzo_aina','jumla','created_at')
            ->get();
    }
    
    /**
     * Get today's debt repayments — modernized
     */
    private function getTodaysMarejeshos($companyId, $today)
    {
        $start = $today instanceof Carbon ? $today->copy()->startOfDay() : Carbon::parse($today)->startOfDay();
        $end = $today instanceof Carbon ? $today->copy()->endOfDay() : Carbon::parse($today)->endOfDay();
        return Marejesho::with(['madeni.bidhaa:id,jina,bei_nunua'])
            ->where('company_id', $companyId)
            ->whereBetween('tarehe', [$start, $end])
            ->select('id','company_id','madeni_id','kiasi','tarehe','created_at')
            ->get();
    }
    
    /**
     * Get today's expenses — modernized
     */
    private function getTodaysMatumizi($companyId, $today)
    {
        $start = $today instanceof Carbon ? $today->copy()->startOfDay() : Carbon::parse($today)->startOfDay();
        $end = $today instanceof Carbon ? $today->copy()->endOfDay() : Carbon::parse($today)->endOfDay();
        return Matumizi::where('company_id', $companyId)
            ->whereBetween('created_at', [$start, $end])
            ->select('id','company_id','gharama','aina','created_at')
            ->get();
    }
    
    /**
     * Get all time sales — kept for backward compat but now lazy (empty). Totals use DB sums.
     * If legacy view iterates, it gets empty collection — original controller passed full collection but view no longer needs it.
     * Logic preserved via cached aggregates.
     */
    private function getAllTimeMauzos($companyId)
    {
        return collect();
    }
    
    /**
     * Get all time debt repayments — same as above
     */
    private function getAllTimeMarejeshos($companyId)
    {
        return collect();
    }
    
    /**
     * Get all time expenses — same
     */
    private function getAllMatumizi($companyId)
    {
        return collect();
    }
    
    /**
     * Get inventory metrics — modernized: DB aggregates, identical logic, no full hydrate
     */
    private function getInventoryMetrics($companyId)
    {
        $jumlaBidhaa = Bidhaa::where('company_id', $companyId)->count();
        $jumlaIdadi = (float) Bidhaa::where('company_id', $companyId)->sum('idadi');
        $thamani = (float) Bidhaa::where('company_id', $companyId)
            ->selectRaw('COALESCE(SUM(idadi * COALESCE(bei_nunua,0)),0) as v')->value('v');
        $bidhaaZilizopo = Bidhaa::where('company_id', $companyId)->where('idadi', '>', 0)->count();
        $bidhaaZimeisha = Bidhaa::where('company_id', $companyId)->where('idadi', 0)->count();
        $bidhaaKaribiaKuisha = Bidhaa::where('company_id', $companyId)->where('idadi', '<', 10)->where('idadi', '>', 0)->count();

        return compact(
            'jumlaBidhaa',
            'jumlaIdadi',
            'thamani',
            'bidhaaZilizopo',
            'bidhaaZimeisha',
            'bidhaaKaribiaKuisha'
        );
    }
    
    /**
     * Get top 3 selling products
     */
    private function getTopSellingProducts($companyId)
    {
        return Bidhaa::where('company_id', $companyId)
            ->withSum('mauzos', 'idadi')
            ->orderByDesc('mauzos_sum_idadi')
            ->take(3)
            ->get();
    }
    
    /**
     * Calculate profit from cash sales
     */
    private function calculateCashSalesProfit($mauzosLeo)
    {
        $faidaMauzo = 0;
        
        foreach ($mauzosLeo as $mauzo) {
            if ($mauzo->bidhaa) {
                $sellingPrice = $mauzo->bei;
                $quantity = $mauzo->idadi;
                $buyingPrice = $mauzo->bidhaa->bei_nunua ?? 0;
                
                $totalDiscount = $this->calculateTotalDiscount($mauzo, $quantity);
                $totalRevenue = ($sellingPrice * $quantity) - $totalDiscount;
                $totalCost = $buyingPrice * $quantity;
                
                $faidaMauzo += $totalRevenue - $totalCost;
            }
        }
        
        return $faidaMauzo;
    }
    
    /**
     * Calculate total discount from a sale
     */
    private function calculateTotalDiscount($mauzo, $quantity)
    {
        if ($mauzo->punguzo_aina === 'bidhaa') {
            return $mauzo->punguzo * $quantity;
        }
        return $mauzo->punguzo;
    }
    
    /**
     * Calculate profit from debt repayments using FIFO method
     */
    private function calculateDebtRepaymentProfit($marejeshosLeo)
    {
        $faidaMarejesho = 0;
        $debtProgress = [];
        
        $sortedMarejeshos = $marejeshosLeo->sortBy('tarehe');
        
        foreach ($sortedMarejeshos as $marejesho) {
            if (!isset($marejesho->madeni) || !isset($marejesho->madeni->bidhaa)) {
                continue;
            }
            
            $debt = $marejesho->madeni;
            $debtId = $debt->id;
            $repaymentAmount = $marejesho->kiasi;
            
            // Initialize debt tracking
            if (!isset($debtProgress[$debtId])) {
                $debtProgress[$debtId] = $this->initializeDebtTracking($debt);
            }
            
            $profitFromRepayment = $this->processRepaymentFIFO(
                $debtProgress[$debtId],
                $repaymentAmount
            );
            
            $faidaMarejesho += $profitFromRepayment;
        }
        
        return $faidaMarejesho;
    }
    
    /**
     * Initialize debt tracking for FIFO profit calculation
     */
    private function initializeDebtTracking($debt)
    {
        $buyingPrice = $debt->bidhaa->bei_nunua ?? 0;
        $quantity = $debt->idadi;
        $totalCost = $buyingPrice * $quantity;
        
        return [
            'total_cost' => $totalCost,
            'total_selling' => $debt->jumla,
            'recovered_so_far' => 0,
            'is_cost_recovered' => false
        ];
    }
    
    /**
     * Process a repayment using FIFO method (cost first, then profit)
     */
    private function processRepaymentFIFO(&$progress, $repaymentAmount)
    {
        $remainingAmount = $repaymentAmount;
        $profit = 0;
        
        // Stage 1: Recover cost first
        if (!$progress['is_cost_recovered']) {
            $remainingToRecover = $progress['total_cost'] - $progress['recovered_so_far'];
            
            if ($remainingAmount <= $remainingToRecover) {
                // All goes to cost recovery
                $progress['recovered_so_far'] += $remainingAmount;
                return 0;
            }
            
            // Part goes to cost recovery, rest is profit
            $costPortion = $remainingToRecover;
            $progress['recovered_so_far'] += $costPortion;
            $progress['is_cost_recovered'] = true;
            
            $profit = $remainingAmount - $costPortion;
            $remainingAmount = 0;
        }
        
        // Stage 2: Cost already recovered, all is profit
        if ($progress['is_cost_recovered'] && $remainingAmount > 0) {
            $profit += $remainingAmount;
        }
        
        return $profit;
    }
    
    /**
     * Get debt summary
     */
    private function getDebtSummary($companyId)
    {
        return [
            'jumlaMadeni' => Madeni::where('company_id', $companyId)->sum('baki'),
            'idadiMadeni' => Madeni::where('company_id', $companyId)->count()
        ];
    }
}