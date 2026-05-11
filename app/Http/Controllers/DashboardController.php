<?php

namespace App\Http\Controllers;

use App\Models\Bidhaa;
use App\Models\Mauzo;
use App\Models\Matumizi;
use App\Models\Madeni;
use App\Models\Marejesho;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;
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
        
        // --- STOCK / INVENTORY METRICS ---
        $inventoryMetrics = $this->getInventoryMetrics($companyId);
        
        // --- TOP SELLING PRODUCTS ---
        $topProducts = $this->getTopSellingProducts($companyId);
        
        // --- TODAY'S FINANCIALS ---
        $todayFinancials = $this->getTodayFinancials($companyId, $today);
        
        // --- ALL-TIME FINANCIALS ---
        $allTimeFinancials = $this->getAllTimeFinancials($companyId);
        
        // --- DEBT SUMMARY ---
        $debtSummary = $this->getDebtSummary($companyId);
        
        // Combine all data for the view
        $dashboardData = array_merge(
            compact('company', 'companyId'),
            $inventoryMetrics,
            $topProducts,
            $todayFinancials,
            $allTimeFinancials,
            $debtSummary
        );
        
        return view('dashboard.index', $dashboardData);
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
     * Get inventory metrics
     */
    private function getInventoryMetrics($companyId)
    {
        return [
            'jumlaBidhaa' => Bidhaa::where('company_id', $companyId)->count(),
            'jumlaIdadi' => Bidhaa::where('company_id', $companyId)->sum('idadi'),
            'thamani' => Bidhaa::where('company_id', $companyId)
                ->selectRaw('SUM(idadi * bei_nunua) as jumla')
                ->value('jumla'),
            'bidhaaZilizopo' => Bidhaa::where('company_id', $companyId)
                ->where('idadi', '>', 0)
                ->count(),
            'bidhaaZimeisha' => Bidhaa::where('company_id', $companyId)
                ->where('idadi', 0)
                ->count(),
            'bidhaaKaribiaKuisha' => Bidhaa::where('company_id', $companyId)
                ->where('idadi', '<', 10)
                ->count(),
        ];
    }
    
    /**
     * Get top 3 selling products
     */
    private function getTopSellingProducts($companyId)
    {
        return [
            'bidhaaTopSales' => Bidhaa::where('company_id', $companyId)
                ->withSum('mauzos', 'idadi')
                ->orderByDesc('mauzos_sum_idadi')
                ->take(3)
                ->get()
        ];
    }
    
    /**
     * Get today's financial metrics
     */
    private function getTodayFinancials($companyId, $today)
    {
        // Cash sales today
        $mauzoLeo = $this->getTodayCashSales($companyId, $today);
        
        // Debt repayments today
        $mapatoMadeni = $this->getTodayDebtRepayments($companyId, $today);
        
        // Total revenue today
        $mapatoLeo = $mauzoLeo + $mapatoMadeni;
        
        // Expenses today
        $matumiziLeo = $this->getTodayExpenses($companyId, $today);
        
        // Net cash today
        $fedhaLeo = $mapatoLeo - $matumiziLeo;
        
        // Profit calculations
        $profitData = $this->getTodayProfit($companyId, $today);
        
        return [
            'mauzoLeo' => $mauzoLeo,
            'mapatoMadeni' => $mapatoMadeni,
            'mapatoLeo' => $mapatoLeo,
            'matumiziLeo' => $matumiziLeo,
            'fedhaLeo' => $fedhaLeo,
            'faidaMauzo' => $profitData['faidaMauzo'],
            'faidaMarejesho' => $profitData['faidaMarejesho'],
            'jumlaFaida' => $profitData['jumlaFaida'],
            'faidaHalisiLeo' => $profitData['faidaHalisiLeo']
        ];
    }
    
    /**
     * Get today's cash sales
     */
    private function getTodayCashSales($companyId, $today)
    {
        return Mauzo::whereHas('bidhaa', fn($q) => $q->where('company_id', $companyId))
            ->whereDate('created_at', $today)
            ->sum(DB::raw('(bei - punguzo) * idadi'));
    }
    
    /**
     * Get today's debt repayments
     */
    private function getTodayDebtRepayments($companyId, $today)
    {
        return Marejesho::whereHas('madeni', function($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
            ->whereDate('tarehe', $today)
            ->sum('kiasi');
    }
    
    /**
     * Get today's expenses
     */
    private function getTodayExpenses($companyId, $today)
    {
        return Matumizi::where('company_id', $companyId)
            ->whereDate('created_at', $today)
            ->sum('gharama');
    }
    
    /**
     * Get today's profit calculations
     */
    private function getTodayProfit($companyId, $today)
    {
        // Profit from cash sales
        $faidaMauzo = $this->calculateCashSalesProfit($companyId, $today);
        
        // Profit from debt repayments
        $faidaMarejesho = $this->calculateDebtRepaymentProfit($companyId, $today);
        
        $jumlaFaida = $faidaMauzo + $faidaMarejesho;
        $matumiziLeo = $this->getTodayExpenses($companyId, $today);
        $faidaHalisiLeo = $jumlaFaida - $matumiziLeo;
        
        return [
            'faidaMauzo' => $faidaMauzo,
            'faidaMarejesho' => $faidaMarejesho,
            'jumlaFaida' => $jumlaFaida,
            'faidaHalisiLeo' => $faidaHalisiLeo
        ];
    }
    
    /**
     * Calculate profit from cash sales
     */
    private function calculateCashSalesProfit($companyId, $today)
    {
        $faidaMauzo = 0;
        
        $mauzosLeo = Mauzo::with('bidhaa')
            ->whereHas('bidhaa', fn($q) => $q->where('company_id', $companyId))
            ->whereDate('created_at', $today)
            ->get();
        
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
    private function calculateDebtRepaymentProfit($companyId, $today)
    {
        $faidaMarejesho = 0;
        $debtProgress = [];
        
        $marejeshosLeo = Marejesho::with(['madeni.bidhaa'])
            ->whereHas('madeni', fn($q) => $q->where('company_id', $companyId))
            ->whereDate('tarehe', $today)
            ->orderBy('tarehe', 'asc')
            ->get();
        
        foreach ($marejeshosLeo as $marejesho) {
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
        }
        
        // Stage 2: Cost already recovered, all is profit
        if ($progress['is_cost_recovered'] && $remainingAmount > 0) {
            $profit += $remainingAmount;
        }
        
        return $profit;
    }
    
    /**
     * Get all-time financial metrics
     */
    private function getAllTimeFinancials($companyId)
    {
        $allMauzos = Mauzo::whereHas('bidhaa', fn($q) => $q->where('company_id', $companyId))->get();
        $allMarejeshos = Marejesho::whereHas('madeni', function($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })->get();
        
        $totalCashSales = $allMauzos->sum('jumla');
        $totalDebtRepayments = $allMarejeshos->sum('kiasi');
        $totalMapato = $totalCashSales + $totalDebtRepayments;
        $totalMatumizi = Matumizi::where('company_id', $companyId)->sum('gharama');
        $jumlaKuu = $totalMapato - $totalMatumizi;
        
        return [
            'allMauzos' => $allMauzos,
            'allMarejeshos' => $allMarejeshos,
            'totalMapato' => $totalMapato,
            'totalMatumizi' => $totalMatumizi,
            'jumlaKuu' => $jumlaKuu
        ];
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