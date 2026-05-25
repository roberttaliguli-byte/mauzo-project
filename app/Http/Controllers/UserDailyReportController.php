<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mauzo;
use App\Models\Marejesho;
use App\Models\Matumizi;
use App\Models\Manunuzi;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserDailyReportController extends Controller
{
    /**
     * Get the authenticated user from either guard
     */
    private function getAuthenticatedUser()
    {
        if (Auth::guard('mfanyakazi')->check()) {
            return Auth::guard('mfanyakazi')->user();
        }
        
        if (Auth::guard('web')->check()) {
            return Auth::guard('web')->user();
        }
        
        abort(403, 'Unauthorized - Please login first');
    }
    
    /**
     * Get company for current user
     */
    private function getCompany()
    {
        $user = $this->getAuthenticatedUser();
        return $user->company;
    }
    
    /**
     * Get company ID for current user
     */
    private function getCompanyId()
    {
        $company = $this->getCompany();
        return $company->id;
    }

    /**
     * Display the daily report page
     */
    public function index()
    {
        $company = $this->getCompany();
        return view('user.daily_reports.index', compact('company'));
    }

    /**
     * Generate the daily/weekly/monthly report
     */
    public function generate(Request $request)
    {
        $request->validate([
            'report_sub_type' => 'required|in:mauzo,faida,biashara,matumizi',
            'group_by' => 'required|in:day,week,month',
            'date_range' => 'required|in:today,yesterday,week,two_days,three_days,month,year,custom',
            'from' => 'required_if:date_range,custom|date',
            'to' => 'required_if:date_range,custom|date|after_or_equal:from',
        ]);

        $dateRange = $this->getDateRange($request->date_range, $request);
        $companyId = $this->getCompanyId();
        $company = $this->getCompany();
        
        $reportSubType = $request->report_sub_type;
        $groupBy = $request->group_by;
        
        // Get grouped data
        $groupedData = $this->getGroupedReportData($companyId, $dateRange, $groupBy, $reportSubType);
        
        // Calculate grand totals
        $grandTotals = $this->calculateGrandTotals($groupedData, $reportSubType);
        
        // Get report title
        $reportTitle = $this->getReportTitle($reportSubType);
        $groupByLabel = $this->getGroupByLabel($groupBy);
        
        return response()->json([
            'success' => true,
            'data' => [
                'report_title' => $reportTitle,
                'report_sub_type' => $reportSubType,
                'group_by' => $groupBy,
                'group_by_label' => $groupByLabel,
                'date_range_label' => $this->getDateRangeLabel($request->date_range, $request),
                'grouped_data' => $groupedData,
                'grand_totals' => $grandTotals,
                'company_name' => $company->company_name ?? 'Biashara',
                'generated_at' => Carbon::now()->format('d/m/Y H:i:s')
            ]
        ]);
    }

    /**
     * Download PDF report
     */
    public function download(Request $request)
    {
        $request->validate([
            'report_sub_type' => 'required|in:mauzo,faida,biashara,matumizi',
            'group_by' => 'required|in:day,week,month',
            'date_range' => 'required|in:today,yesterday,week,two_days,three_days,month,year,custom',
            'from' => 'required_if:date_range,custom|date',
            'to' => 'required_if:date_range,custom|date|after_or_equal:from',
        ]);

        $dateRange = $this->getDateRange($request->date_range, $request);
        $companyId = $this->getCompanyId();
        $company = $this->getCompany();
        
        $reportSubType = $request->report_sub_type;
        $groupBy = $request->group_by;
        
        // Get grouped data
        $groupedData = $this->getGroupedReportData($companyId, $dateRange, $groupBy, $reportSubType);
        
        // Calculate grand totals
        $grandTotals = $this->calculateGrandTotals($groupedData, $reportSubType);
        
        // Prepare PDF data
        $pdfData = [
            'report_title' => $this->getReportTitle($reportSubType),
            'report_sub_type' => $reportSubType,
            'group_by' => $groupBy,
            'group_by_label' => $this->getGroupByLabel($groupBy),
            'date_range_label' => $this->getDateRangeLabel($request->date_range, $request),
            'grouped_data' => $groupedData,
            'grand_totals' => $grandTotals,
            'company_name' => $company->company_name ?? 'Biashara',
            'generated_at' => Carbon::now()->format('d/m/Y H:i:s'),
            'display_from' => $dateRange['start'] ? $dateRange['start']->format('d/m/Y') : null,
            'display_to' => $dateRange['end'] ? $dateRange['end']->format('d/m/Y') : null,
        ];
        
        $pdf = PDF::loadView('user.daily_reports.pdf', $pdfData);
        $pdf->setPaper('A4', 'portrait');
        
        $fileName = $this->generateFileName($reportSubType, $request->date_range, $request);
        
        return $pdf->download($fileName);
    }

    /**
     * Get date range based on period
     */
    private function getDateRange($period, $request)
    {
        $today = Carbon::today();
        $range = ['start' => null, 'end' => null];

        switch ($period) {
            case 'today':
                $range['start'] = $today->copy()->startOfDay();
                $range['end'] = $today->copy()->endOfDay();
                break;
            case 'yesterday':
                $yesterday = $today->copy()->subDay();
                $range['start'] = $yesterday->startOfDay();
                $range['end'] = $yesterday->endOfDay();
                break;
            case 'week':
                $range['start'] = $today->copy()->startOfWeek()->startOfDay();
                $range['end'] = $today->copy()->endOfDay();
                break;
            case 'two_days':
                $range['start'] = $today->copy()->subDays(1)->startOfDay();
                $range['end'] = $today->copy()->endOfDay();
                break;
            case 'three_days':
                $range['start'] = $today->copy()->subDays(2)->startOfDay();
                $range['end'] = $today->copy()->endOfDay();
                break;
            case 'month':
                $range['start'] = $today->copy()->startOfMonth()->startOfDay();
                $range['end'] = $today->copy()->endOfDay();
                break;
            case 'year':
                $range['start'] = $today->copy()->startOfYear()->startOfDay();
                $range['end'] = $today->copy()->endOfDay();
                break;
            case 'custom':
                if ($request->filled('from')) {
                    $range['start'] = Carbon::parse($request->from)->startOfDay();
                }
                if ($request->filled('to')) {
                    $range['end'] = Carbon::parse($request->to)->endOfDay();
                }
                break;
        }

        return $range;
    }

    /**
     * Get grouped report data
     */
    private function getGroupedReportData($companyId, $dateRange, $groupBy, $reportSubType)
    {
        $startDate = $dateRange['start'];
        $endDate = $dateRange['end'];
        
        if (!$startDate || !$endDate) {
            return [];
        }
        
        $groups = $this->generateDateGroups($startDate, $endDate, $groupBy);
        $results = [];
        $weekCounter = 1;
        $previousWeekKey = null;
        
        foreach ($groups as $groupKey => $groupLabel) {
            $groupStart = Carbon::parse($groupKey);
            $groupEnd = $groupStart->copy();
            
            if ($groupBy === 'day') {
                $groupEnd = $groupStart->copy()->endOfDay();
            } elseif ($groupBy === 'week') {
                $groupEnd = $groupStart->copy()->endOfWeek();
            } elseif ($groupBy === 'month') {
                $groupEnd = $groupStart->copy()->endOfMonth();
            }
            
            // Adjust end date if beyond report range
            if ($groupEnd > $endDate) {
                $groupEnd = $endDate;
            }
            
            $data = $this->getReportDataByType($companyId, $groupStart, $groupEnd, $reportSubType);
            
            // Add week separator for week grouping
            $weekSeparator = false;
            $weekNumber = null;
            if ($groupBy === 'week') {
                $currentWeekKey = $groupStart->format('Y-W');
                if ($previousWeekKey !== null && $currentWeekKey !== $previousWeekKey) {
                    $weekSeparator = true;
                    $weekCounter++;
                }
                $weekNumber = $weekCounter;
                $previousWeekKey = $currentWeekKey;
            }
            
            $results[] = [
                'period_key' => $groupKey,
                'period_label' => $groupLabel,
                'start_date' => $groupStart->format('d/m/Y'),
                'end_date' => $groupEnd->format('d/m/Y'),
                'display_date' => $this->getDisplayDateRange($groupStart, $groupEnd, $groupBy),
                'week_separator' => $weekSeparator,
                'week_number' => $weekNumber,
                'data' => $data
            ];
        }
        
        return $results;
    }

    /**
     * Get display date range for a period
     */
    private function getDisplayDateRange($start, $end, $groupBy)
    {
        if ($groupBy === 'day') {
            return $start->format('d/m/Y') . ' (' . $this->getDayName($start) . ')';
        } elseif ($groupBy === 'week') {
            return $start->format('d/m') . ' - ' . $end->format('d/m/Y');
        } else {
            return $start->format('F Y');
        }
    }

    /**
     * Generate date groups
     */
    private function generateDateGroups($startDate, $endDate, $groupBy)
    {
        $groups = [];
        $current = clone $startDate;
        $end = clone $endDate;
        
        switch ($groupBy) {
            case 'day':
                while ($current <= $end) {
                    $groups[$current->format('Y-m-d')] = $this->getDayName($current);
                    $current->addDay();
                }
                break;
                
            case 'week':
                // Start from Monday of the first week
                $weekStart = $startDate->copy()->startOfWeek(Carbon::MONDAY);
                $weekNumber = 1;
                while ($weekStart <= $end) {
                    $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);
                    if ($weekEnd > $end) {
                        $weekEnd = $end;
                    }
                    $groups[$weekStart->format('Y-m-d')] = "Wiki " . $weekNumber . " (" . $weekStart->format('d/m') . " - " . $weekEnd->format('d/m') . ")";
                    $weekStart->addWeek();
                    $weekNumber++;
                }
                break;
                
            case 'month':
                $monthStart = $startDate->copy()->startOfMonth();
                while ($monthStart <= $end) {
                    $groups[$monthStart->format('Y-m')] = $monthStart->format('F Y');
                    $monthStart->addMonth();
                }
                break;
        }
        
        return $groups;
    }

    /**
     * Get day name in Swahili
     */
    private function getDayName($date)
    {
        $dayNames = [
            'Monday' => 'Jumatatu',
            'Tuesday' => 'Jumanne',
            'Wednesday' => 'Jumatano',
            'Thursday' => 'Alhamisi',
            'Friday' => 'Ijumaa',
            'Saturday' => 'Jumamosi',
            'Sunday' => 'Jumapili'
        ];
        
        $englishDay = $date->format('l');
        return $dayNames[$englishDay] ?? $englishDay;
    }

    /**
     * Get report data by type
     */
    private function getReportDataByType($companyId, $startDate, $endDate, $reportSubType)
    {
        switch ($reportSubType) {
            case 'mauzo':
                return $this->getSalesSummaryData($companyId, $startDate, $endDate);
            case 'faida':
                return $this->getProfitSummaryData($companyId, $startDate, $endDate);
            case 'biashara':
                return $this->getBusinessSummaryData($companyId, $startDate, $endDate);
            case 'matumizi':
                return $this->getExpensesSummaryData($companyId, $startDate, $endDate);
            default:
                return $this->getSalesSummaryData($companyId, $startDate, $endDate);
        }
    }

    /**
     * Get sales summary data - ENHANCED with Mapato ya Madeni and Faida
     */
    private function getSalesSummaryData($companyId, $startDate, $endDate)
    {
        // Get sales data
        $salesTotal = Mauzo::where('company_id', $companyId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('jumla');
        
        $salesCount = Mauzo::where('company_id', $companyId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
        
        // Get debt repayments
        $debtRepaymentsTotal = Marejesho::where('company_id', $companyId)
            ->whereBetween('tarehe', [$startDate, $endDate])
            ->sum('kiasi');
        
        $repaymentsCount = Marejesho::where('company_id', $companyId)
            ->whereBetween('tarehe', [$startDate, $endDate])
            ->count();
        
        // Calculate profit from sales
        $salesProfit = 0;
        $mauzos = Mauzo::where('company_id', $companyId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with('bidhaa')
            ->get();
        
        foreach ($mauzos as $mauzo) {
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
                
                $totalRevenue = ($sellingPrice * $quantity) - $totalDiscount;
                $totalCost = $buyingPrice * $quantity;
                $salesProfit += $totalRevenue - $totalCost;
            }
        }
        
        // Calculate profit from repayments (FIFO method)
        $repaymentProfit = 0;
        $marejeshos = Marejesho::where('company_id', $companyId)
            ->whereBetween('tarehe', [$startDate, $endDate])
            ->with(['madeni.bidhaa'])
            ->orderBy('tarehe', 'asc')
            ->get();
        
        $debtProgress = [];
        foreach ($marejeshos as $marejesho) {
            if (isset($marejesho->madeni) && isset($marejesho->madeni->bidhaa)) {
                $debt = $marejesho->madeni;
                $debtId = $debt->id;
                $repaymentAmount = $marejesho->kiasi;
                
                if (!isset($debtProgress[$debtId])) {
                    $buyingPrice = $debt->bidhaa->bei_nunua ?? 0;
                    $quantity = $debt->idadi;
                    $totalCost = $buyingPrice * $quantity;
                    
                    $debtProgress[$debtId] = [
                        'total_cost' => $totalCost,
                        'recovered_so_far' => 0,
                        'is_cost_recovered' => false
                    ];
                }
                
                $progress = &$debtProgress[$debtId];
                $remainingAmount = $repaymentAmount;
                
                if (!$progress['is_cost_recovered']) {
                    $remainingToRecover = $progress['total_cost'] - $progress['recovered_so_far'];
                    
                    if ($remainingAmount <= $remainingToRecover) {
                        $progress['recovered_so_far'] += $remainingAmount;
                        $remainingAmount = 0;
                    } else {
                        $costPortion = $remainingToRecover;
                        $progress['recovered_so_far'] += $costPortion;
                        $progress['is_cost_recovered'] = true;
                        $profitPortion = $remainingAmount - $costPortion;
                        $repaymentProfit += $profitPortion;
                        $remainingAmount = 0;
                    }
                }
                
                if ($progress['is_cost_recovered'] && $remainingAmount > 0) {
                    $repaymentProfit += $remainingAmount;
                }
            }
        }
        
        $totalProfit = $salesProfit + $repaymentProfit;
        $totalIncome = $salesTotal + $debtRepaymentsTotal;
        
        // Get by payment method
        $salesCash = Mauzo::where('company_id', $companyId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('lipa_kwa', 'cash')
            ->sum('jumla');
        
        $salesMobile = Mauzo::where('company_id', $companyId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('lipa_kwa', 'lipa_namba')
            ->sum('jumla');
        
        $salesBank = Mauzo::where('company_id', $companyId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('lipa_kwa', 'bank')
            ->sum('jumla');
        
        return [
            'total_sales' => $salesTotal,
            'total_repayments' => $debtRepaymentsTotal,
            'total_income' => $totalIncome,
            'sales_count' => $salesCount,
            'repayments_count' => $repaymentsCount,
            'sales_cash' => $salesCash,
            'sales_mobile' => $salesMobile,
            'sales_bank' => $salesBank,
            'sales_profit' => $salesProfit,
            'repayment_profit' => $repaymentProfit,
            'total_profit' => $totalProfit
        ];
    }

    /**
     * Get profit summary data
     */
    private function getProfitSummaryData($companyId, $startDate, $endDate)
    {
        // Calculate sales profit
        $salesProfit = 0;
        $mauzos = Mauzo::where('company_id', $companyId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with('bidhaa')
            ->get();
        
        foreach ($mauzos as $mauzo) {
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
                
                $totalRevenue = ($sellingPrice * $quantity) - $totalDiscount;
                $totalCost = $buyingPrice * $quantity;
                $salesProfit += $totalRevenue - $totalCost;
            }
        }
        
        // Calculate repayment profit
        $repaymentProfit = 0;
        $marejeshos = Marejesho::where('company_id', $companyId)
            ->whereBetween('tarehe', [$startDate, $endDate])
            ->with(['madeni.bidhaa'])
            ->orderBy('tarehe', 'asc')
            ->get();
        
        $debtProgress = [];
        foreach ($marejeshos as $marejesho) {
            if (isset($marejesho->madeni) && isset($marejesho->madeni->bidhaa)) {
                $debt = $marejesho->madeni;
                $debtId = $debt->id;
                $repaymentAmount = $marejesho->kiasi;
                
                if (!isset($debtProgress[$debtId])) {
                    $buyingPrice = $debt->bidhaa->bei_nunua ?? 0;
                    $quantity = $debt->idadi;
                    $totalCost = $buyingPrice * $quantity;
                    
                    $debtProgress[$debtId] = [
                        'total_cost' => $totalCost,
                        'recovered_so_far' => 0,
                        'is_cost_recovered' => false
                    ];
                }
                
                $progress = &$debtProgress[$debtId];
                $remainingAmount = $repaymentAmount;
                
                if (!$progress['is_cost_recovered']) {
                    $remainingToRecover = $progress['total_cost'] - $progress['recovered_so_far'];
                    
                    if ($remainingAmount <= $remainingToRecover) {
                        $progress['recovered_so_far'] += $remainingAmount;
                        $remainingAmount = 0;
                    } else {
                        $costPortion = $remainingToRecover;
                        $progress['recovered_so_far'] += $costPortion;
                        $progress['is_cost_recovered'] = true;
                        $profitPortion = $remainingAmount - $costPortion;
                        $repaymentProfit += $profitPortion;
                        $remainingAmount = 0;
                    }
                }
                
                if ($progress['is_cost_recovered'] && $remainingAmount > 0) {
                    $repaymentProfit += $remainingAmount;
                }
            }
        }
        
        // Get expenses
        $totalExpenses = Matumizi::where('company_id', $companyId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('gharama');
        
        $totalProfit = $salesProfit + $repaymentProfit;
        $netProfit = $totalProfit - $totalExpenses;
        
        return [
            'sales_profit' => $salesProfit,
            'repayment_profit' => $repaymentProfit,
            'total_profit' => $totalProfit,
            'expenses' => $totalExpenses,
            'net_profit' => $netProfit
        ];
    }

    /**
     * Get business summary data - ENHANCED with separate Mapato ya Mauzo and Mapato ya Madeni
     */
    private function getBusinessSummaryData($companyId, $startDate, $endDate)
    {
        // Get sales data
        $totalSales = Mauzo::where('company_id', $companyId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('jumla');
        
        $salesCount = Mauzo::where('company_id', $companyId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
        
        // Get debt repayments
        $totalRepayments = Marejesho::where('company_id', $companyId)
            ->whereBetween('tarehe', [$startDate, $endDate])
            ->sum('kiasi');
        
        $repaymentsCount = Marejesho::where('company_id', $companyId)
            ->whereBetween('tarehe', [$startDate, $endDate])
            ->count();
        
        // Calculate profit
        $salesProfit = 0;
        $mauzos = Mauzo::where('company_id', $companyId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with('bidhaa')
            ->get();
        
        foreach ($mauzos as $mauzo) {
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
                
                $totalRevenue = ($sellingPrice * $quantity) - $totalDiscount;
                $totalCost = $buyingPrice * $quantity;
                $salesProfit += $totalRevenue - $totalCost;
            }
        }
        
        // Calculate repayment profit
        $repaymentProfit = 0;
        $marejeshos = Marejesho::where('company_id', $companyId)
            ->whereBetween('tarehe', [$startDate, $endDate])
            ->with(['madeni.bidhaa'])
            ->orderBy('tarehe', 'asc')
            ->get();
        
        $debtProgress = [];
        foreach ($marejeshos as $marejesho) {
            if (isset($marejesho->madeni) && isset($marejesho->madeni->bidhaa)) {
                $debt = $marejesho->madeni;
                $debtId = $debt->id;
                $repaymentAmount = $marejesho->kiasi;
                
                if (!isset($debtProgress[$debtId])) {
                    $buyingPrice = $debt->bidhaa->bei_nunua ?? 0;
                    $quantity = $debt->idadi;
                    $totalCost = $buyingPrice * $quantity;
                    
                    $debtProgress[$debtId] = [
                        'total_cost' => $totalCost,
                        'recovered_so_far' => 0,
                        'is_cost_recovered' => false
                    ];
                }
                
                $progress = &$debtProgress[$debtId];
                $remainingAmount = $repaymentAmount;
                
                if (!$progress['is_cost_recovered']) {
                    $remainingToRecover = $progress['total_cost'] - $progress['recovered_so_far'];
                    
                    if ($remainingAmount <= $remainingToRecover) {
                        $progress['recovered_so_far'] += $remainingAmount;
                        $remainingAmount = 0;
                    } else {
                        $costPortion = $remainingToRecover;
                        $progress['recovered_so_far'] += $costPortion;
                        $progress['is_cost_recovered'] = true;
                        $profitPortion = $remainingAmount - $costPortion;
                        $repaymentProfit += $profitPortion;
                        $remainingAmount = 0;
                    }
                }
                
                if ($progress['is_cost_recovered'] && $remainingAmount > 0) {
                    $repaymentProfit += $remainingAmount;
                }
            }
        }
        
        // Get expenses
        $totalExpenses = Matumizi::where('company_id', $companyId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('gharama');
        
        $totalIncome = $totalSales + $totalRepayments;
        $totalProfit = $salesProfit + $repaymentProfit;
        $netProfit = $totalProfit - $totalExpenses;
        
        // Get payment method breakdown for sales
        $salesCash = Mauzo::where('company_id', $companyId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('lipa_kwa', 'cash')
            ->sum('jumla');
        
        $salesMobile = Mauzo::where('company_id', $companyId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('lipa_kwa', 'lipa_namba')
            ->sum('jumla');
        
        $salesBank = Mauzo::where('company_id', $companyId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('lipa_kwa', 'bank')
            ->sum('jumla');
        
        return [
            'total_income' => $totalIncome,
            'total_sales' => $totalSales,
            'total_repayments' => $totalRepayments,
            'sales_count' => $salesCount,
            'repayments_count' => $repaymentsCount,
            'sales_cash' => $salesCash,
            'sales_mobile' => $salesMobile,
            'sales_bank' => $salesBank,
            'total_profit' => $totalProfit,
            'sales_profit' => $salesProfit,
            'repayment_profit' => $repaymentProfit,
            'expenses' => $totalExpenses,
            'net_profit' => $netProfit,
            'cash_flow' => $totalIncome - $totalExpenses
        ];
    }

    /**
     * Get expenses summary data
     */
    private function getExpensesSummaryData($companyId, $startDate, $endDate)
    {
        $expenses = Matumizi::where('company_id', $companyId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
        
        $totalExpenses = $expenses->sum('gharama');
        $expensesCount = $expenses->count();
        
        $expensesByCategory = [];
        foreach ($expenses as $expense) {
            $category = $expense->aina ?: 'Zingine';
            if (!isset($expensesByCategory[$category])) {
                $expensesByCategory[$category] = 0;
            }
            $expensesByCategory[$category] += $expense->gharama;
        }
        
        return [
            'total_expenses' => $totalExpenses,
            'expenses_count' => $expensesCount,
            'expenses_by_category' => $expensesByCategory
        ];
    }

    /**
     * Calculate grand totals
     */
    private function calculateGrandTotals($groupedData, $reportSubType)
    {
        $grandTotals = [];
        
        foreach ($groupedData as $group) {
            $data = $group['data'];
            
            switch ($reportSubType) {
                case 'mauzo':
                    if (!isset($grandTotals['total_sales'])) $grandTotals['total_sales'] = 0;
                    if (!isset($grandTotals['total_repayments'])) $grandTotals['total_repayments'] = 0;
                    if (!isset($grandTotals['total_income'])) $grandTotals['total_income'] = 0;
                    if (!isset($grandTotals['sales_count'])) $grandTotals['sales_count'] = 0;
                    if (!isset($grandTotals['total_profit'])) $grandTotals['total_profit'] = 0;
                    
                    $grandTotals['total_sales'] += $data['total_sales'];
                    $grandTotals['total_repayments'] += $data['total_repayments'];
                    $grandTotals['total_income'] += $data['total_income'];
                    $grandTotals['sales_count'] += $data['sales_count'];
                    $grandTotals['total_profit'] += $data['total_profit'];
                    break;
                    
                case 'faida':
                    if (!isset($grandTotals['total_profit'])) $grandTotals['total_profit'] = 0;
                    if (!isset($grandTotals['expenses'])) $grandTotals['expenses'] = 0;
                    if (!isset($grandTotals['net_profit'])) $grandTotals['net_profit'] = 0;
                    
                    $grandTotals['total_profit'] += $data['total_profit'];
                    $grandTotals['expenses'] += $data['expenses'];
                    $grandTotals['net_profit'] += $data['net_profit'];
                    break;
                    
                case 'biashara':
                    if (!isset($grandTotals['total_income'])) $grandTotals['total_income'] = 0;
                    if (!isset($grandTotals['total_sales'])) $grandTotals['total_sales'] = 0;
                    if (!isset($grandTotals['total_repayments'])) $grandTotals['total_repayments'] = 0;
                    if (!isset($grandTotals['expenses'])) $grandTotals['expenses'] = 0;
                    if (!isset($grandTotals['net_profit'])) $grandTotals['net_profit'] = 0;
                    if (!isset($grandTotals['total_profit'])) $grandTotals['total_profit'] = 0;
                    
                    $grandTotals['total_income'] += $data['total_income'];
                    $grandTotals['total_sales'] += $data['total_sales'];
                    $grandTotals['total_repayments'] += $data['total_repayments'];
                    $grandTotals['expenses'] += $data['expenses'];
                    $grandTotals['net_profit'] += $data['net_profit'];
                    $grandTotals['total_profit'] += $data['total_profit'];
                    break;
                    
                case 'matumizi':
                    if (!isset($grandTotals['total_expenses'])) $grandTotals['total_expenses'] = 0;
                    if (!isset($grandTotals['expenses_count'])) $grandTotals['expenses_count'] = 0;
                    
                    $grandTotals['total_expenses'] += $data['total_expenses'];
                    $grandTotals['expenses_count'] += $data['expenses_count'];
                    break;
            }
        }
        
        return $grandTotals;
    }

    /**
     * Get report title
     */
    private function getReportTitle($reportSubType)
    {
        $titles = [
            'mauzo' => 'Ripoti ya Mauzo na Mapato kwa Vipindi',
            'faida' => 'Ripoti ya Faida kwa Vipindi',
            'biashara' => 'Ripoti ya Muhtasari wa Biashara kwa Vipindi',
            'matumizi' => 'Ripoti ya Matumizi kwa Vipindi'
        ];
        
        return $titles[$reportSubType] ?? 'Ripoti kwa Vipindi';
    }

    /**
     * Get group by label
     */
    private function getGroupByLabel($groupBy)
    {
        $labels = [
            'day' => 'Kwa Siku',
            'week' => 'Kwa Wiki',
            'month' => 'Kwa Mwezi'
        ];
        
        return $labels[$groupBy] ?? 'Kwa Vipindi';
    }

    /**
     * Get date range label
     */
    private function getDateRangeLabel($period, $request)
    {
        $labels = [
            'today' => 'Leo',
            'yesterday' => 'Jana',
            'week' => 'Wiki hii',
            'two_days' => 'Siku 2 zilizopita',
            'three_days' => 'Siku 3 zilizopita',
            'month' => 'Mwezi huu',
            'year' => 'Mwaka huu',
            'custom' => 'Tarehe Maalum'
        ];
        
        $label = $labels[$period] ?? 'Kipindi';
        
        if ($period === 'custom' && $request->filled('from') && $request->filled('to')) {
            $from = Carbon::parse($request->from)->format('d/m/Y');
            $to = Carbon::parse($request->to)->format('d/m/Y');
            $label = "{$from} - {$to}";
        }
        
        return $label;
    }

    /**
     * Generate file name
     */
    private function generateFileName($reportSubType, $timePeriod, $request)
    {
        $typeNames = [
            'mauzo' => 'mauzo_na_mapato_kwa_vipindi',
            'faida' => 'faida_kwa_vipindi',
            'biashara' => 'muhtasari_biashara',
            'matumizi' => 'matumizi_kwa_vipindi'
        ];
        
        $periodNames = [
            'today' => 'leo',
            'yesterday' => 'jana',
            'week' => 'wiki',
            'two_days' => 'siku_2',
            'three_days' => 'siku_3',
            'month' => 'mwezi',
            'year' => 'mwaka',
            'custom' => 'tarehe'
        ];
        
        $type = $typeNames[$reportSubType] ?? 'ripoti';
        $period = $periodNames[$timePeriod] ?? 'muda';
        
        if ($timePeriod === 'custom' && $request->filled('from') && $request->filled('to')) {
            $from = Carbon::parse($request->from)->format('Y_m_d');
            $to = Carbon::parse($request->to)->format('Y_m_d');
            $period = "{$from}_mpaka_{$to}";
        }
        
        $date = now()->format('Y_m_d_H_i');
        
        return "{$type}_{$period}_{$date}.pdf";
    }
}