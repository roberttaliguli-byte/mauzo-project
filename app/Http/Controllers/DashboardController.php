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
        
        // Get all data needed for the dashboard
        $todaysMauzos = $this->getTodaysMauzos($companyId, $today);
        $todaysMarejeshos = $this->getTodaysMarejeshos($companyId, $today);
        $todaysMatumizi = $this->getTodaysMatumizi($companyId, $today);
        
        // All time data
        $allTimeMauzos = $this->getAllTimeMauzos($companyId);
        $allTimeMarejeshos = $this->getAllTimeMarejeshos($companyId);
        $allMatumizi = $this->getAllMatumizi($companyId);
        
        // Calculate financial metrics
        $mapatoMauzo = $todaysMauzos->sum(fn($m) => $m->jumla);
        $mapatoMadeni = $todaysMarejeshos->sum('kiasi');
        $mapatoLeo = $mapatoMauzo + $mapatoMadeni;
        $matumiziLeo = $todaysMatumizi->sum('gharama');
        $fedhaLeo = $mapatoLeo - $matumiziLeo;
        
        // Calculate profit
        $faidaMauzo = $this->calculateCashSalesProfit($todaysMauzos);
        $faidaMarejesho = $this->calculateDebtRepaymentProfit($todaysMarejeshos);
        $jumlaFaida = $faidaMauzo + $faidaMarejesho;
        $faidaHalisiLeo = $jumlaFaida - $matumiziLeo;
        
        // Inventory metrics
        $inventoryMetrics = $this->getInventoryMetrics($companyId);
        
        // Top selling products
        $bidhaaTopSales = $this->getTopSellingProducts($companyId);
        
        // Debt summary
        $debtSummary = $this->getDebtSummary($companyId);
        
        // All time totals
        $totalMapato = $allTimeMauzos->sum('jumla') + $allTimeMarejeshos->sum('kiasi');
        $totalMatumizi = $allMatumizi->sum('gharama');
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
     * Get today's sales
     */
    private function getTodaysMauzos($companyId, $today)
    {
        return Mauzo::whereHas('bidhaa', fn($q) => $q->where('company_id', $companyId))
            ->with('bidhaa')
            ->whereDate('created_at', $today)
            ->get();
    }
    
    /**
     * Get today's debt repayments
     */
    private function getTodaysMarejeshos($companyId, $today)
    {
        return Marejesho::with(['madeni.bidhaa'])
            ->whereHas('madeni', fn($q) => $q->where('company_id', $companyId))
            ->whereDate('tarehe', $today)
            ->get();
    }
    
    /**
     * Get today's expenses
     */
    private function getTodaysMatumizi($companyId, $today)
    {
        return Matumizi::where('company_id', $companyId)
            ->whereDate('created_at', $today)
            ->get();
    }
    
    /**
     * Get all time sales
     */
    private function getAllTimeMauzos($companyId)
    {
        return Mauzo::whereHas('bidhaa', fn($q) => $q->where('company_id', $companyId))
            ->with('bidhaa')
            ->get();
    }
    
    /**
     * Get all time debt repayments
     */
    private function getAllTimeMarejeshos($companyId)
    {
        return Marejesho::whereHas('madeni', fn($q) => $q->where('company_id', $companyId))
            ->get();
    }
    
    /**
     * Get all time expenses
     */
    private function getAllMatumizi($companyId)
    {
        return Matumizi::where('company_id', $companyId)->get();
    }
    
    /**
     * Get inventory metrics
     */
    private function getInventoryMetrics($companyId)
    {
        $bidhaaZote = Bidhaa::where('company_id', $companyId)->get();
        $jumlaBidhaa = $bidhaaZote->count();
        $jumlaIdadi = $bidhaaZote->sum('idadi');
        $thamani = $bidhaaZote->sum(fn($b) => $b->idadi * ($b->bei_nunua ?? 0));
        $bidhaaZilizopo = $bidhaaZote->where('idadi', '>', 0)->count();
        $bidhaaZimeisha = $bidhaaZote->where('idadi', 0)->count();
        $bidhaaKaribiaKuisha = $bidhaaZote->where('idadi', '<', 10)->where('idadi', '>', 0)->count();
        
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