<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mauzo;
use App\Models\Bidhaa;
use App\Models\Manunuzi;
use App\Models\Matumizi;
use App\Models\Marejesho;
use Carbon\Carbon;
use PDF;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserReportController extends Controller
{
    // Show report selection page
    public function select()
    {
        $user = Auth::user();
        $company = $user->company;
        
        return view('user.reports.select', compact('company'));
    }

    // Handle PDF download
    public function download(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:sales,manunuzi,matumizi,general,mapato_by_method',
            'time_period' => 'required|in:today,yesterday,week,month,year,custom',
            'from' => 'required_if:time_period,custom|date',
            'to' => 'required_if:time_period,custom|date|after_or_equal:from',
        ]);

        $timePeriod = $request->get('time_period');
        $reportType = $request->get('report_type');
        
        // Get date range
        $dateRange = $this->getDateRange($timePeriod, $request);
        
        // Get user and company
        $user = Auth::user();
        $company = $user->company;
        $companyId = $company->id;

        // Prepare base data
        $data = [
            'reportType' => $reportType,
            'selectedPeriod' => $timePeriod,
            'dateFrom' => $dateRange['start'] ? $dateRange['start']->format('Y-m-d') : null,
            'dateTo' => $dateRange['end'] ? $dateRange['end']->format('Y-m-d') : null,
            'companyName' => $company->company_name ?? 'Biashara',
            'date' => Carbon::now()->format('d/m/Y'),
            'currentTime' => Carbon::now()->format('H:i:s'),
        ];

        // Get data based on report type
        switch ($reportType) {
            case 'sales':
                $salesData = $this->getSalesData($companyId, $dateRange);
                $data = array_merge($data, $salesData);
                break;
            case 'manunuzi':
                $purchasesData = $this->getPurchasesData($companyId, $dateRange);
                $data = array_merge($data, $purchasesData);
                break;
            case 'matumizi':
                $expensesData = $this->getExpensesData($companyId, $dateRange);
                $data = array_merge($data, $expensesData);
                break;
            case 'general':
                $generalData = $this->getGeneralData($companyId, $dateRange);
                $data = array_merge($data, $generalData);
                break;
            case 'mapato_by_method':
                $incomeData = $this->getIncomeByPaymentMethod($companyId, $dateRange);
                $data = array_merge($data, $incomeData);
                break;
        }

        // Generate PDF
        $pdf = PDF::loadView('user.reports.pdf', $data);
        $pdf->setPaper('A4', 'portrait');
        
        $fileName = $this->generateFileName($reportType, $timePeriod, $request);
        
        return $pdf->download($fileName);
    }

    // Get sales data
    private function getSalesData($companyId, $dateRange)
    {
        try {
            // Get sales with payment method
            $sales = Mauzo::where('company_id', $companyId)
                ->when($dateRange['start'], function($q) use ($dateRange) {
                    $q->whereDate('created_at', '>=', $dateRange['start']->format('Y-m-d'));
                })
                ->when($dateRange['end'], function($q) use ($dateRange) {
                    $q->whereDate('created_at', '<=', $dateRange['end']->format('Y-m-d'));
                })
                ->with('bidhaa')
                ->orderBy('created_at', 'desc')
                ->get();

            // Calculate totals by payment method
            $totalCashSales = 0;
            $totalMobileSales = 0;
            $totalBankSales = 0;
            $totalSales = 0;
            $totalProfit = 0;
            $salesByMethod = [];

            foreach ($sales as $sale) {
                if (!$sale->bidhaa) continue;
                
                $totalSales += $sale->jumla;
                
                // Categorize by payment method
                $paymentMethod = $sale->lipa_kwa ?? 'cash';
                
                // Also track in separate totals
                switch($paymentMethod) {
                    case 'cash':
                        $totalCashSales += $sale->jumla;
                        break;
                    case 'lipa_namba':
                        $totalMobileSales += $sale->jumla;
                        break;
                    case 'bank':
                        $totalBankSales += $sale->jumla;
                        break;
                }
            }

            // Get debt repayments by payment method
            $debtRepayments = Marejesho::where('company_id', $companyId)
                ->when($dateRange['start'], function($q) use ($dateRange) {
                    $q->whereDate('tarehe', '>=', $dateRange['start']->format('Y-m-d'));
                })
                ->when($dateRange['end'], function($q) use ($dateRange) {
                    $q->whereDate('tarehe', '<=', $dateRange['end']->format('Y-m-d'));
                })
                ->orderBy('tarehe', 'desc')
                ->get();

            $totalCashDebts = 0;
            $totalMobileDebts = 0;
            $totalBankDebts = 0;
            $totalDebtRepayments = 0;

            foreach ($debtRepayments as $repayment) {
                $totalDebtRepayments += $repayment->kiasi;
                
                // Categorize by payment method
                $paymentMethod = $repayment->lipa_kwa ?? 'cash';
                
                // Also track in separate totals
                switch($paymentMethod) {
                    case 'cash':
                        $totalCashDebts += $repayment->kiasi;
                        break;
                    case 'lipa_namba':
                        $totalMobileDebts += $repayment->kiasi;
                        break;
                    case 'bank':
                        $totalBankDebts += $repayment->kiasi;
                        break;
                }
            }

            $grandTotal = $totalSales + $totalDebtRepayments;
            $totalCashIncome = $totalCashSales + $totalCashDebts;
            $totalMobileIncome = $totalMobileSales + $totalMobileDebts;
            $totalBankIncome = $totalBankSales + $totalBankDebts;

            return [
                'sales' => $sales,
                'debtRepayments' => $debtRepayments,
                'totalSales' => $totalSales,
                'totalDebtRepayments' => $totalDebtRepayments,
                'grandTotal' => $grandTotal,
                
                // Categorized totals
                'totalCashSales' => $totalCashSales,
                'totalMobileSales' => $totalMobileSales,
                'totalBankSales' => $totalBankSales,
                'totalCashDebts' => $totalCashDebts,
                'totalMobileDebts' => $totalMobileDebts,
                'totalBankDebts' => $totalBankDebts,
                'totalCashIncome' => $totalCashIncome,
                'totalMobileIncome' => $totalMobileIncome,
                'totalBankIncome' => $totalBankIncome,
            ];

        } catch (\Exception $e) {
            return [
                'sales' => collect([]),
                'debtRepayments' => collect([]),
                'totalSales' => 0,
                'totalDebtRepayments' => 0,
                'grandTotal' => 0,
                'totalCashSales' => 0,
                'totalMobileSales' => 0,
                'totalBankSales' => 0,
                'totalCashDebts' => 0,
                'totalMobileDebts' => 0,
                'totalBankDebts' => 0,
                'totalCashIncome' => 0,
                'totalMobileIncome' => 0,
                'totalBankIncome' => 0,
            ];
        }
    }

    // Get income categorized by payment method
    private function getIncomeByPaymentMethod($companyId, $dateRange)
    {
        try {
            // Get sales by payment method
            $salesByMethod = Mauzo::where('company_id', $companyId)
                ->when($dateRange['start'], function($q) use ($dateRange) {
                    $q->whereDate('created_at', '>=', $dateRange['start']->format('Y-m-d'));
                })
                ->when($dateRange['end'], function($q) use ($dateRange) {
                    $q->whereDate('created_at', '<=', $dateRange['end']->format('Y-m-d'));
                })
                ->select('lipa_kwa', DB::raw('COUNT(*) as count'), DB::raw('SUM(jumla) as total'))
                ->groupBy('lipa_kwa')
                ->get()
                ->map(function($item) {
                    $item->method_name = $this->getPaymentMethodName($item->lipa_kwa ?? 'cash');
                    $item->type = 'mauzo';
                    return $item;
                });

            // Get debt repayments by payment method
            $debtsByMethod = Marejesho::where('company_id', $companyId)
                ->when($dateRange['start'], function($q) use ($dateRange) {
                    $q->whereDate('tarehe', '>=', $dateRange['start']->format('Y-m-d'));
                })
                ->when($dateRange['end'], function($q) use ($dateRange) {
                    $q->whereDate('tarehe', '<=', $dateRange['end']->format('Y-m-d'));
                })
                ->select('lipa_kwa', DB::raw('COUNT(*) as count'), DB::raw('SUM(kiasi) as total'))
                ->groupBy('lipa_kwa')
                ->get()
                ->map(function($item) {
                    $item->method_name = $this->getPaymentMethodName($item->lipa_kwa ?? 'cash');
                    $item->type = 'madeni';
                    return $item;
                });

            // Calculate totals
            $totalCash = 0;
            $totalMobile = 0;
            $totalBank = 0;
            
            foreach ($salesByMethod as $item) {
                switch($item->lipa_kwa) {
                    case 'cash': $totalCash += $item->total; break;
                    case 'lipa_namba': $totalMobile += $item->total; break;
                    case 'bank': $totalBank += $item->total; break;
                }
            }
            
            foreach ($debtsByMethod as $item) {
                switch($item->lipa_kwa) {
                    case 'cash': $totalCash += $item->total; break;
                    case 'lipa_namba': $totalMobile += $item->total; break;
                    case 'bank': $totalBank += $item->total; break;
                }
            }
            
            $grandTotal = $totalCash + $totalMobile + $totalBank;

            return [
                'salesByMethod' => $salesByMethod,
                'debtsByMethod' => $debtsByMethod,
                'totalCash' => $totalCash,
                'totalMobile' => $totalMobile,
                'totalBank' => $totalBank,
                'grandTotal' => $grandTotal,
                'reportSubtitle' => 'Ripoti ya Mapato Kulingana na Njia ya Malipo',
            ];

        } catch (\Exception $e) {
            return [
                'salesByMethod' => collect([]),
                'debtsByMethod' => collect([]),
                'totalCash' => 0,
                'totalMobile' => 0,
                'totalBank' => 0,
                'grandTotal' => 0,
                'reportSubtitle' => 'Ripoti ya Mapato Kulingana na Njia ya Malipo',
            ];
        }
    }

    // Get purchases data - FIXED: Calculate jumla properly
// Get purchases data - FIXED: Calculate jumla properly
private function getPurchasesData($companyId, $dateRange)
{
    try {
        $manunuzi = Manunuzi::where('company_id', $companyId)
            ->when($dateRange['start'], function($q) use ($dateRange) {
                $q->whereDate('created_at', '>=', $dateRange['start']->format('Y-m-d'));
            })
            ->when($dateRange['end'], function($q) use ($dateRange) {
                $q->whereDate('created_at', '<=', $dateRange['end']->format('Y-m-d'));
            })
            ->with('bidhaa')
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate total cost - Just use the bei field directly
        // Based on your example: Ribbon has 2 items but no price listed,
        // Calculator has 5 items at 30,000 each = 150,000
        // Another calculator has 2 items at 20,000 each = 40,000
        // Total should be: 30,000 + 30,000 + 20,000 = 80,000 (NOT 280,000)
        
        $totalCost = 0;
        $processedManunuzi = [];

        foreach ($manunuzi as $purchase) {
     
            // Add purchase to processed array
            $processedManunuzi[] = $purchase;
            
            // Calculate line total for display
            $purchase->line_total = $purchase->bei * $purchase->idadi;
        }


        $totalCost = $manunuzi->sum('bei');

        return [
            'manunuzi' => $processedManunuzi,
            'totalCost' => $totalCost,
        ];

    } catch (\Exception $e) {
        return [
            'manunuzi' => collect([]),
            'totalCost' => 0,
        ];
    }
}

    // Get general data - FIXED: Added fedha dukani calculation
    private function getGeneralData($companyId, $dateRange)
    {
        try {
            // Mapato ya Mauzo by payment method
            $salesByMethod = Mauzo::where('company_id', $companyId)
                ->when($dateRange['start'], function($q) use ($dateRange) {
                    $q->whereDate('created_at', '>=', $dateRange['start']->format('Y-m-d'));
                })
                ->when($dateRange['end'], function($q) use ($dateRange) {
                    $q->whereDate('created_at', '<=', $dateRange['end']->format('Y-m-d'));
                })
                ->select('lipa_kwa', DB::raw('SUM(jumla) as total'))
                ->groupBy('lipa_kwa')
                ->get()
                ->keyBy('lipa_kwa');

            $mapatoCashMauzo = $salesByMethod['cash']->total ?? 0;
            $mapatoMobileMauzo = $salesByMethod['lipa_namba']->total ?? 0;
            $mapatoBankMauzo = $salesByMethod['bank']->total ?? 0;
            $mapatoMauzo = $mapatoCashMauzo + $mapatoMobileMauzo + $mapatoBankMauzo;

            // Mapato ya Madeni by payment method
            $debtsByMethod = Marejesho::where('company_id', $companyId)
                ->when($dateRange['start'], function($q) use ($dateRange) {
                    $q->whereDate('tarehe', '>=', $dateRange['start']->format('Y-m-d'));
                })
                ->when($dateRange['end'], function($q) use ($dateRange) {
                    $q->whereDate('tarehe', '<=', $dateRange['end']->format('Y-m-d'));
                })
                ->select('lipa_kwa', DB::raw('SUM(kiasi) as total'))
                ->groupBy('lipa_kwa')
                ->get()
                ->keyBy('lipa_kwa');

            $mapatoCashMadeni = $debtsByMethod['cash']->total ?? 0;
            $mapatoMobileMadeni = $debtsByMethod['lipa_namba']->total ?? 0;
            $mapatoBankMadeni = $debtsByMethod['bank']->total ?? 0;
            $mapatoMadeni = $mapatoCashMadeni + $mapatoMobileMadeni + $mapatoBankMadeni;

            // Jumla ya Mapato by payment method
            $jumlaMapatoCash = $mapatoCashMauzo + $mapatoCashMadeni;
            $jumlaMapatoMobile = $mapatoMobileMauzo + $mapatoMobileMadeni;
            $jumlaMapatoBank = $mapatoBankMauzo + $mapatoBankMadeni;
            $jumlaMapato = $jumlaMapatoCash + $jumlaMapatoMobile + $jumlaMapatoBank;

            // Jumla ya Matumizi
            $jumlaMatumizi = Matumizi::where('company_id', $companyId)
                ->when($dateRange['start'], function($q) use ($dateRange) {
                    $q->whereDate('created_at', '>=', $dateRange['start']->format('Y-m-d'));
                })
                ->when($dateRange['end'], function($q) use ($dateRange) {
                    $q->whereDate('created_at', '<=', $dateRange['end']->format('Y-m-d'));
                })
                ->sum('gharama');

            // Faida ya Marejesho
            $faidaMarejesho = 0;
            $marejeshos = Marejesho::where('company_id', $companyId)
                ->when($dateRange['start'], function($q) use ($dateRange) {
                    $q->whereDate('tarehe', '>=', $dateRange['start']->format('Y-m-d'));
                })
                ->when($dateRange['end'], function($q) use ($dateRange) {
                    $q->whereDate('tarehe', '<=', $dateRange['end']->format('Y-m-d'));
                })
                ->with(['madeni.bidhaa'])
                ->get();

            foreach ($marejeshos as $marejesho) {
                if ($marejesho->madeni && $marejesho->madeni->bidhaa) {
                    $buyingPrice = $marejesho->madeni->bidhaa->bei_nunua ?? 0;
                    $quantity = $marejesho->madeni->idadi;
                    
                    $totalBuyingCost = $buyingPrice * $quantity;
                    $actualSellingPrice = $marejesho->madeni->jumla;
                    
                    $profit = $actualSellingPrice - $totalBuyingCost;
                    $faidaMarejesho += $profit;
                }
            }

            // Faida ya Mauzo
            $faidaMauzo = 0;
            $mauzos = Mauzo::where('company_id', $companyId)
                ->when($dateRange['start'], function($q) use ($dateRange) {
                    $q->whereDate('created_at', '>=', $dateRange['start']->format('Y-m-d'));
                })
                ->when($dateRange['end'], function($q) use ($dateRange) {
                    $q->whereDate('created_at', '<=', $dateRange['end']->format('Y-m-d'));
                })
                ->with('bidhaa')
                ->get();

            foreach ($mauzos as $mauzo) {
                if ($mauzo->bidhaa) {
                    $buyingPrice = $mauzo->bidhaa->bei_nunua ?? 0;
                    $actualDiscount = $this->calculateActualDiscount(
                        $mauzo->punguzo, 
                        $mauzo->punguzo_aina, 
                        $mauzo->idadi
                    );
                    
                    $profit = ($mauzo->bei * $mauzo->idadi) - ($buyingPrice * $mauzo->idadi) - $actualDiscount;
                    $faidaMauzo += $profit;
                }
            }

            // FIXED: Fedha Dukani calculation - Jumla Mapato minus Matumizi
            $fedhaDukani = $jumlaMapato - $jumlaMatumizi;

            // Faida Halisi
            $totalProfit = $faidaMauzo + $faidaMarejesho;
            $faidaHalisi = $totalProfit - $jumlaMatumizi;

            return [
                // Mapato by payment method
                'mapatoCashMauzo' => $mapatoCashMauzo,
                'mapatoMobileMauzo' => $mapatoMobileMauzo,
                'mapatoBankMauzo' => $mapatoBankMauzo,
                'mapatoMauzo' => $mapatoMauzo,
                
                'mapatoCashMadeni' => $mapatoCashMadeni,
                'mapatoMobileMadeni' => $mapatoMobileMadeni,
                'mapatoBankMadeni' => $mapatoBankMadeni,
                'mapatoMadeni' => $mapatoMadeni,
                
                'jumlaMapatoCash' => $jumlaMapatoCash,
                'jumlaMapatoMobile' => $jumlaMapatoMobile,
                'jumlaMapatoBank' => $jumlaMapatoBank,
                'jumlaMapato' => $jumlaMapato,
                
                // Other totals
                'jumlaMatumizi' => $jumlaMatumizi,
                'faidaMarejesho' => $faidaMarejesho,
                'faidaMauzo' => $faidaMauzo,
                
                // FIXED: Fedha dukani calculation
                'fedhaDukani' => $fedhaDukani,
                
                'faidaHalisi' => $faidaHalisi,
            ];

        } catch (\Exception $e) {
            return [
                'mapatoMauzo' => 0,
                'mapatoMadeni' => 0,
                'jumlaMapato' => 0,
                'jumlaMatumizi' => 0,
                'faidaMarejesho' => 0,
                'faidaMauzo' => 0,
                'fedhaDukani' => 0,
                'faidaHalisi' => 0,
            ];
        }
    }

    // Helper method to get payment method name
    private function getPaymentMethodName($method)
    {
        $methods = [
            'cash' => 'Cash',
            'lipa_namba' => 'Lipa Namba',
            'bank' => 'Bank'
        ];
        
        return $methods[$method] ?? 'Cash';
    }

    // Helper method to calculate actual discount
    private function calculateActualDiscount($discount, $discountType, $quantity)
    {
        if ($discountType === 'bidhaa') {
            return $discount * $quantity;
        }
        return $discount;
    }

    // Get date range based on period
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
    
    // Get expenses data
    private function getExpensesData($companyId, $dateRange)
    {
        try {
            $matumizi = Matumizi::where('company_id', $companyId)
                ->when($dateRange['start'], function($q) use ($dateRange) {
                    $q->whereDate('created_at', '>=', $dateRange['start']->format('Y-m-d'));
                })
                ->when($dateRange['end'], function($q) use ($dateRange) {
                    $q->whereDate('created_at', '<=', $dateRange['end']->format('Y-m-d'));
                })
                ->orderBy('created_at', 'desc')
                ->get();

            // Calculate totals by category
            $totalsByCategory = [];
            $totalExpenses = 0;

            foreach ($matumizi as $expense) {
                $category = $expense->aina ?: 'Zingine';
                $totalExpenses += $expense->gharama;
                
                if (!isset($totalsByCategory[$category])) {
                    $totalsByCategory[$category] = 0;
                }
                $totalsByCategory[$category] += $expense->gharama;
            }

            return [
                'matumizi' => $matumizi,
                'totalExpenses' => $totalExpenses,
                'totalsByCategory' => $totalsByCategory,
            ];

        } catch (\Exception $e) {
            return [
                'matumizi' => collect([]),
                'totalExpenses' => 0,
                'totalsByCategory' => [],
            ];
        }
    }

    // Generate file name
    private function generateFileName($reportType, $timePeriod, $request)
    {
        $typeNames = [
            'sales' => 'mauzo',
            'manunuzi' => 'manunuzi',
            'matumizi' => 'matumizi',
            'general' => 'jumla',
            'mapato_by_method' => 'mapato_njia_malipo'
        ];

        $periodNames = [
            'today' => 'leo',
            'yesterday' => 'jana',
            'week' => 'wiki',
            'month' => 'mwezi',
            'year' => 'mwaka',
            'custom' => 'tarehe'
        ];

        $type = $typeNames[$reportType] ?? 'ripoti';
        $period = $periodNames[$timePeriod] ?? 'muda';
        
        if ($timePeriod === 'custom' && $request->filled('from') && $request->filled('to')) {
            $from = Carbon::parse($request->from)->format('Y_m_d');
            $to = Carbon::parse($request->to)->format('Y_m_d');
            $period = "{$from}_mpaka_{$to}";
        }

        $date = now()->format('Y_m_d_H_i');
        
        return "{$type}_{$period}_{$date}.pdf";
    }
    // Add this method to your UserReportController
public function preview(Request $request)
{
    $request->validate([
        'report_type' => 'required|in:sales,manunuzi,matumizi,general',
        'time_period' => 'required|in:today,yesterday,week,month,year,custom',
        'from' => 'required_if:time_period,custom|date',
        'to' => 'required_if:time_period,custom|date|after_or_equal:from',
    ]);

    $timePeriod = $request->get('time_period');
    $reportType = $request->get('report_type');
    
    // Get date range
    $dateRange = $this->getDateRange($timePeriod, $request);
    
    // Get user and company
    $user = Auth::user();
    $company = $user->company;
    $companyId = $company->id;

    // Prepare base data
    $data = [
        'reportType' => $reportType,
        'selectedPeriod' => $timePeriod,
        'dateFrom' => $dateRange['start'] ? $dateRange['start']->format('Y-m-d') : null,
        'dateTo' => $dateRange['end'] ? $dateRange['end']->format('Y-m-d') : null,
        'companyName' => $company->company_name ?? 'Biashara',
        'date' => Carbon::now()->format('d/m/Y'),
        'currentTime' => Carbon::now()->format('H:i:s'),
    ];

    // Get data based on report type
    switch ($reportType) {
        case 'sales':
            $salesData = $this->getSalesData($companyId, $dateRange);
            $data = array_merge($data, $salesData);
            break;
        case 'manunuzi':
            $purchasesData = $this->getPurchasesData($companyId, $dateRange);
            $data = array_merge($data, $purchasesData);
            break;
        case 'matumizi':
            $expensesData = $this->getExpensesData($companyId, $dateRange);
            $data = array_merge($data, $expensesData);
            break;
        case 'general':
            $generalData = $this->getGeneralData($companyId, $dateRange);
            $data = array_merge($data, $generalData);
            break;
    }

    // Generate PDF for preview
    $pdf = PDF::loadView('user.reports.pdf', $data);
    $pdf->setPaper('A4', 'portrait');
    
    return $pdf->stream('preview.pdf');
}
}