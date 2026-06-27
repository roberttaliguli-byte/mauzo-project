<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mauzo;
use App\Models\Bidhaa;
use App\Models\Manunuzi;
use App\Models\Matumizi;
use App\Models\Marejesho;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserReportController extends Controller
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
     * Clean UTF-8 string to prevent encoding errors
     */
    private function cleanUtf8String($value)
    {
        if (is_null($value)) {
            return null;
        }
        
        if (is_string($value)) {
            // Remove invalid UTF-8 characters
            $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
            
            // Remove control characters except newlines and tabs
            $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $value);
            
            return $value;
        }
        
        if (is_array($value)) {
            return array_map([$this, 'cleanUtf8String'], $value);
        }
        
        if (is_object($value)) {
            $cleaned = clone $value;
            foreach (get_object_vars($cleaned) as $key => $prop) {
                $cleaned->$key = $this->cleanUtf8String($prop);
            }
            return $cleaned;
        }
        
        return $value;
    }
    
    // Show report selection page
    public function select()
    {
        $company = $this->getCompany();
        
        return view('user.reports.select', compact('company'));
    }

    // Generate report preview
    public function generate(Request $request)
    {
        try {
            $request->validate([
                'report_type' => 'required|in:sales,manunuzi,matumizi,general,mapato_by_method',
                'date_range' => 'required|in:today,yesterday,week,month,year,custom',
                'from' => 'required_if:date_range,custom|date',
                'to' => 'required_if:date_range,custom|date|after_or_equal:from',
            ]);

            $dateRange = $this->getDateRange($request->date_range, $request);
            $company = $this->getCompany();
            $companyId = $company->id;
            $reportType = $request->report_type;

            // Get data based on report type
            $data = $this->getReportData($reportType, $companyId, $dateRange);
            
            // Add common data
            $data['reportType'] = $reportType;
            $data['dateRange'] = $request->date_range;
            $data['dateFrom'] = $dateRange['start'] ? $dateRange['start']->format('Y-m-d') : null;
            $data['dateTo'] = $dateRange['end'] ? $dateRange['end']->format('Y-m-d') : null;
            $data['companyName'] = $company->company_name ?? 'Biashara';
            $data['generatedAt'] = Carbon::now()->format('d/m/Y H:i:s');
            $data['displayFrom'] = $dateRange['start'] ? $dateRange['start']->format('d/m/Y') : null;
            $data['displayTo'] = $dateRange['end'] ? $dateRange['end']->format('d/m/Y') : null;

            // Clean all UTF-8 data before returning
            $cleanedData = $this->cleanUtf8String($data);

            return response()->json([
                'success' => true,
                'data' => $cleanedData
            ]);

        } catch (\Exception $e) {
            \Log::error('Generate report error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error generating report: ' . $e->getMessage()
            ], 500);
        }
    }

    // Download PDF report
    public function download(Request $request)
    {
        try {
            $request->validate([
                'report_type' => 'required|in:sales,manunuzi,matumizi,general,mapato_by_method',
                'date_range' => 'required|in:today,yesterday,week,month,year,custom',
                'from' => 'required_if:date_range,custom|date',
                'to' => 'required_if:date_range,custom|date|after_or_equal:from',
            ]);

            $dateRange = $this->getDateRange($request->date_range, $request);
            $company = $this->getCompany();
            $companyId = $company->id;
            $reportType = $request->report_type;

            // Get data based on report type
            $data = $this->getReportData($reportType, $companyId, $dateRange);
            
            // Add common data
            $data['reportType'] = $reportType;
            $data['dateRange'] = $request->date_range;
            $data['dateFrom'] = $dateRange['start'] ? $dateRange['start']->format('Y-m-d') : null;
            $data['dateTo'] = $dateRange['end'] ? $dateRange['end']->format('Y-m-d') : null;
            $data['companyName'] = $company->company_name ?? 'Biashara';
            $data['generatedAt'] = Carbon::now()->format('d/m/Y H:i:s');
            $data['displayFrom'] = $dateRange['start'] ? $dateRange['start']->format('d/m/Y') : null;
            $data['displayTo'] = $dateRange['end'] ? $dateRange['end']->format('d/m/Y') : null;

            // Clean data before passing to PDF
            $cleanedData = $this->cleanUtf8String($data);

            // Generate PDF
            $pdf = PDF::loadView('user.reports.pdf', $cleanedData);
            $pdf->setPaper('A4', 'portrait');
            
            $fileName = $this->generateFileName($reportType, $request->date_range, $request);
            
            return $pdf->download($fileName);

        } catch (\Exception $e) {
            \Log::error('PDF Download Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error generating report: ' . $e->getMessage()
            ], 500);
        }
    }

    // Get report data based on type
    private function getReportData($reportType, $companyId, $dateRange)
    {
        switch ($reportType) {
            case 'sales':
                return $this->getSalesData($companyId, $dateRange);
            case 'manunuzi':
                return $this->getPurchasesData($companyId, $dateRange);
            case 'matumizi':
                return $this->getExpensesData($companyId, $dateRange);
            case 'general':
                return $this->getGeneralData($companyId, $dateRange);
            case 'mapato_by_method':
                return $this->getIncomeByPaymentMethod($companyId, $dateRange);
            default:
                return [];
        }
    }
    
    // Get sales data with payment type details
    private function getSalesData($companyId, $dateRange)
    {
        try {
            // Get sales with payment method AND payment type
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

            // Add formatted payment method with type
            foreach ($sales as $sale) {
                if (!$sale->bidhaa) continue;
                
                // Clean product data
                $sale->bidhaa->jina = $this->cleanUtf8String($sale->bidhaa->jina);
                $sale->bidhaa->aina = $this->cleanUtf8String($sale->bidhaa->aina);
                $sale->bidhaa->kipimo = $this->cleanUtf8String($sale->bidhaa->kipimo);
                
                $lipaKwaType = $sale->lipa_kwa_type ?? null;
                $sale->formatted_payment = $this->formatPaymentMethod($sale->lipa_kwa, $lipaKwaType);
                $sale->formatted_idadi = $this->formatDecimal($sale->idadi);
            }

            // Calculate totals by payment method AND type
            $totalCashSales = 0;
            $totalMobileSales = 0;
            $totalBankSales = 0;
            $totalSales = 0;
            
            // For detailed breakdown by payment type
            $salesByPaymentType = [
                'cash' => ['total' => 0, 'count' => 0],
                'mpesa' => ['total' => 0, 'count' => 0],
                'mixx_by_yas' => ['total' => 0, 'count' => 0],
                'airtel_money' => ['total' => 0, 'count' => 0],
                'halopesa' => ['total' => 0, 'count' => 0],
                'crdb' => ['total' => 0, 'count' => 0],
                'nmb' => ['total' => 0, 'count' => 0],
                'nbc' => ['total' => 0, 'count' => 0],
                'other_lipa_namba' => ['total' => 0, 'count' => 0],
                'other_bank' => ['total' => 0, 'count' => 0],
            ];

            foreach ($sales as $sale) {
                if (!$sale->bidhaa) continue;
                
                $totalSales += $sale->jumla;
                $paymentMethod = $sale->lipa_kwa ?? 'cash';
                $paymentType = $sale->lipa_kwa_type ?? null;
                
                // Categorize by payment method
                switch($paymentMethod) {
                    case 'cash':
                        $totalCashSales += $sale->jumla;
                        $salesByPaymentType['cash']['total'] += $sale->jumla;
                        $salesByPaymentType['cash']['count']++;
                        break;
                    case 'lipa_namba':
                        $totalMobileSales += $sale->jumla;
                        // Categorize by specific mobile money type
                        $mobileKey = $paymentType ?? 'other_lipa_namba';
                        if (!in_array($mobileKey, ['mpesa', 'mixx_by_yas', 'airtel_money', 'halopesa'])) {
                            $mobileKey = 'other_lipa_namba';
                        }
                        $salesByPaymentType[$mobileKey]['total'] += $sale->jumla;
                        $salesByPaymentType[$mobileKey]['count']++;
                        break;
                    case 'bank':
                        $totalBankSales += $sale->jumla;
                        // Categorize by specific bank type
                        $bankKey = $paymentType ?? 'other_bank';
                        if (!in_array($bankKey, ['crdb', 'nmb', 'nbc'])) {
                            $bankKey = 'other_bank';
                        }
                        $salesByPaymentType[$bankKey]['total'] += $sale->jumla;
                        $salesByPaymentType[$bankKey]['count']++;
                        break;
                }
            }

            // Get debt repayments with payment type details
            $debtRepayments = Marejesho::where('company_id', $companyId)
                ->when($dateRange['start'], function($q) use ($dateRange) {
                    $q->whereDate('tarehe', '>=', $dateRange['start']->format('Y-m-d'));
                })
                ->when($dateRange['end'], function($q) use ($dateRange) {
                    $q->whereDate('tarehe', '<=', $dateRange['end']->format('Y-m-d'));
                })
                ->with(['madeni.bidhaa'])
                ->orderBy('tarehe', 'desc')
                ->get();

            // Add formatted payment method to repayments
            foreach ($debtRepayments as $repayment) {
                if ($repayment->madeni && $repayment->madeni->bidhaa) {
                    $repayment->madeni->bidhaa->jina = $this->cleanUtf8String($repayment->madeni->bidhaa->jina);
                    $repayment->madeni->bidhaa->aina = $this->cleanUtf8String($repayment->madeni->bidhaa->aina);
                    $repayment->madeni->bidhaa->kipimo = $this->cleanUtf8String($repayment->madeni->bidhaa->kipimo);
                }
                
                $lipaKwaType = $repayment->lipa_kwa_type ?? null;
                $repayment->formatted_payment = $this->formatPaymentMethod($repayment->lipa_kwa, $lipaKwaType);
                $repayment->formatted_idadi = $this->formatDecimal($repayment->madeni->idadi ?? 0);
            }

            $totalCashDebts = 0;
            $totalMobileDebts = 0;
            $totalBankDebts = 0;
            $totalDebtRepayments = 0;
            
            $debtsByPaymentType = [
                'cash' => ['total' => 0, 'count' => 0],
                'mpesa' => ['total' => 0, 'count' => 0],
                'mixx_by_yas' => ['total' => 0, 'count' => 0],
                'airtel_money' => ['total' => 0, 'count' => 0],
                'halopesa' => ['total' => 0, 'count' => 0],
                'crdb' => ['total' => 0, 'count' => 0],
                'nmb' => ['total' => 0, 'count' => 0],
                'nbc' => ['total' => 0, 'count' => 0],
                'other_lipa_namba' => ['total' => 0, 'count' => 0],
                'other_bank' => ['total' => 0, 'count' => 0],
            ];

            foreach ($debtRepayments as $repayment) {
                $totalDebtRepayments += $repayment->kiasi;
                $paymentMethod = $repayment->lipa_kwa ?? 'cash';
                $paymentType = $repayment->lipa_kwa_type ?? null;
                
                switch($paymentMethod) {
                    case 'cash':
                        $totalCashDebts += $repayment->kiasi;
                        $debtsByPaymentType['cash']['total'] += $repayment->kiasi;
                        $debtsByPaymentType['cash']['count']++;
                        break;
                    case 'lipa_namba':
                        $totalMobileDebts += $repayment->kiasi;
                        $mobileKey = $paymentType ?? 'other_lipa_namba';
                        if (!in_array($mobileKey, ['mpesa', 'mixx_by_yas', 'airtel_money', 'halopesa'])) {
                            $mobileKey = 'other_lipa_namba';
                        }
                        $debtsByPaymentType[$mobileKey]['total'] += $repayment->kiasi;
                        $debtsByPaymentType[$mobileKey]['count']++;
                        break;
                    case 'bank':
                        $totalBankDebts += $repayment->kiasi;
                        $bankKey = $paymentType ?? 'other_bank';
                        if (!in_array($bankKey, ['crdb', 'nmb', 'nbc'])) {
                            $bankKey = 'other_bank';
                        }
                        $debtsByPaymentType[$bankKey]['total'] += $repayment->kiasi;
                        $debtsByPaymentType[$bankKey]['count']++;
                        break;
                }
            }

            $grandTotal = $totalSales + $totalDebtRepayments;
            $totalCashIncome = $totalCashSales + $totalCashDebts;
            $totalMobileIncome = $totalMobileSales + $totalMobileDebts;
            $totalBankIncome = $totalBankSales + $totalBankDebts;

            // Combine sales and repayments for detailed view
            $allTransactions = collect();
            foreach ($sales as $sale) {
                if (!$sale->bidhaa) continue;
                
                $allTransactions->push([
                    'type' => 'Mauzo',
                    'date' => $sale->created_at,
                    'product_name' => $this->cleanUtf8String($sale->bidhaa->jina ?? 'N/A'),
                    'product_aina' => $this->cleanUtf8String($sale->bidhaa->aina ?? 'N/A'),
                    'product_kipimo' => $this->cleanUtf8String($sale->bidhaa->kipimo ?? 'N/A'),
                    'idadi' => $sale->formatted_idadi,
                    'payment_method' => $sale->formatted_payment,
                    'amount' => $sale->jumla,
                    'lipa_kwa' => $sale->lipa_kwa,
                    'lipa_kwa_type' => $sale->lipa_kwa_type
                ]);
            }
            
            foreach ($debtRepayments as $repayment) {
                $productName = 'N/A';
                $productAina = 'N/A';
                $productKipimo = 'N/A';
                
                if ($repayment->madeni && $repayment->madeni->bidhaa) {
                    $productName = $repayment->madeni->bidhaa->jina ?? 'N/A';
                    $productAina = $repayment->madeni->bidhaa->aina ?? 'N/A';
                    $productKipimo = $repayment->madeni->bidhaa->kipimo ?? 'N/A';
                }
                
                $allTransactions->push([
                    'type' => 'Marejesho ya Deni',
                    'date' => $repayment->tarehe,
                    'product_name' => $this->cleanUtf8String($productName),
                    'product_aina' => $this->cleanUtf8String($productAina),
                    'product_kipimo' => $this->cleanUtf8String($productKipimo),
                    'idadi' => $repayment->formatted_idadi,
                    'payment_method' => $repayment->formatted_payment,
                    'amount' => $repayment->kiasi,
                    'lipa_kwa' => $repayment->lipa_kwa,
                    'lipa_kwa_type' => $repayment->lipa_kwa_type
                ]);
            }
            
            $allTransactions = $allTransactions->sortByDesc('date')->values();

            return [
                'sales' => $sales,
                'debtRepayments' => $debtRepayments,
                'allTransactions' => $allTransactions,
                'totalSales' => $totalSales,
                'totalDebtRepayments' => $totalDebtRepayments,
                'grandTotal' => $grandTotal,
                'totalCashSales' => $totalCashSales,
                'totalMobileSales' => $totalMobileSales,
                'totalBankSales' => $totalBankSales,
                'totalCashDebts' => $totalCashDebts,
                'totalMobileDebts' => $totalMobileDebts,
                'totalBankDebts' => $totalBankDebts,
                'totalCashIncome' => $totalCashIncome,
                'totalMobileIncome' => $totalMobileIncome,
                'totalBankIncome' => $totalBankIncome,
                'salesByPaymentType' => $salesByPaymentType,
                'debtsByPaymentType' => $debtsByPaymentType,
                'reportTitle' => 'Ripoti ya Mauzo',
                'reportSubtitle' => 'Mapato na mauzo kwa kipindi kilichochaguliwa'
            ];

        } catch (\Exception $e) {
            \Log::error('Error in getSalesData: ' . $e->getMessage());
            return [
                'sales' => collect([]),
                'debtRepayments' => collect([]),
                'allTransactions' => collect([]),
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
                'salesByPaymentType' => [],
                'debtsByPaymentType' => [],
                'reportTitle' => 'Ripoti ya Mauzo',
                'reportSubtitle' => 'Mapato na mauzo kwa kipindi kilichochaguliwa'
            ];
        }
    }

    /**
     * Format payment method with type for display
     */
    private function formatPaymentMethod($lipaKwa, $lipaKwaType = null)
    {
        // Safety check - if null or empty, default to 'Cash'
        if (empty($lipaKwa)) {
            return 'Cash';
        }
        
        // Clean the values
        $lipaKwa = strtolower(trim($lipaKwa));
        
        if ($lipaKwa === 'cash') {
            return 'Cash';
        }
        
        if ($lipaKwa === 'lipa_namba') {
            $typeNames = [
                'mpesa' => 'M-Pesa',
                'mixx_by_yas' => 'Mixx by Yas',
                'airtel_money' => 'Airtel Money',
                'halopesa' => 'HaloPesa',
                'other' => 'Nyingine'
            ];
            
            $typeName = 'Lipa Namba';
            if (!empty($lipaKwaType)) {
                $lipaKwaType = strtolower(trim($lipaKwaType));
                $typeName = $typeNames[$lipaKwaType] ?? ucfirst($lipaKwaType);
            }
            
            return "Lipa Namba ({$typeName})";
        }
        
        if ($lipaKwa === 'bank') {
            $typeNames = [
                'crdb' => 'CRDB',
                'nmb' => 'NMB',
                'nbc' => 'NBC',
                'other' => 'Nyingine'
            ];
            
            $typeName = 'Benki';
            if (!empty($lipaKwaType)) {
                $lipaKwaType = strtolower(trim($lipaKwaType));
                $typeName = $typeNames[$lipaKwaType] ?? ucfirst($lipaKwaType);
            }
            
            return "Benki ({$typeName})";
        }
        
        return ucfirst($lipaKwa);
    }

    // Get purchases data with decimal support
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

            $totalCost = 0;
            $totalItems = 0;
            $processedManunuzi = [];

            foreach ($manunuzi as $purchase) {
                if ($purchase->bidhaa) {
                    $purchase->bidhaa->jina = $this->cleanUtf8String($purchase->bidhaa->jina);
                    $purchase->bidhaa->aina = $this->cleanUtf8String($purchase->bidhaa->aina);
                    $purchase->bidhaa->kipimo = $this->cleanUtf8String($purchase->bidhaa->kipimo);
                }
                
                // Format IDADI to handle decimals properly
                $purchase->formatted_idadi = $this->formatDecimal($purchase->idadi);
                $purchase->unit_cost = $purchase->idadi > 0 ? $purchase->bei / $purchase->idadi : 0;
                
                $processedManunuzi[] = $purchase;
                $totalCost += $purchase->bei;
                $totalItems += $purchase->idadi;
            }

            // Group by date
            $purchasesByDate = [];
            foreach ($processedManunuzi as $purchase) {
                $dateKey = $purchase->created_at->format('Y-m-d');
                if (!isset($purchasesByDate[$dateKey])) {
                    $purchasesByDate[$dateKey] = [
                        'date' => $purchase->created_at->format('d/m/Y'),
                        'purchases' => [],
                        'total' => 0,
                        'items' => 0
                    ];
                }
                
                $purchasesByDate[$dateKey]['purchases'][] = $purchase;
                $purchasesByDate[$dateKey]['total'] += $purchase->bei;
                $purchasesByDate[$dateKey]['items'] += $purchase->idadi;
            }

            return [
                'manunuzi' => $processedManunuzi,
                'purchasesByDate' => $purchasesByDate,
                'totalCost' => $totalCost,
                'totalItems' => $totalItems,
                'averageCost' => $totalItems > 0 ? $totalCost / $totalItems : 0,
                'reportTitle' => 'Ripoti ya Manunuzi',
                'reportSubtitle' => 'Manunuzi ya bidhaa kwa kipindi kilichochaguliwa'
            ];

        } catch (\Exception $e) {
            \Log::error('Error in getPurchasesData: ' . $e->getMessage());
            return [
                'manunuzi' => collect([]),
                'purchasesByDate' => [],
                'totalCost' => 0,
                'totalItems' => 0,
                'averageCost' => 0,
                'reportTitle' => 'Ripoti ya Manunuzi',
                'reportSubtitle' => 'Manunuzi ya bidhaa kwa kipindi kilichochaguliwa'
            ];
        }
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

            // Clean expense descriptions
            foreach ($matumizi as $expense) {
                $expense->maelezo = $this->cleanUtf8String($expense->maelezo);
                $expense->aina = $this->cleanUtf8String($expense->aina);
            }

            // Calculate totals by category
            $totalsByCategory = [];
            $totalExpenses = 0;

            foreach ($matumizi as $expense) {
                $category = $expense->aina ?: 'Zingine';
                $category = $this->cleanUtf8String($category);
                $totalExpenses += $expense->gharama;
                
                if (!isset($totalsByCategory[$category])) {
                    $totalsByCategory[$category] = 0;
                }
                $totalsByCategory[$category] += $expense->gharama;
            }

            // Group by date
            $expensesByDate = [];
            foreach ($matumizi as $expense) {
                $dateKey = $expense->created_at->format('Y-m-d');
                if (!isset($expensesByDate[$dateKey])) {
                    $expensesByDate[$dateKey] = [
                        'date' => $expense->created_at->format('d/m/Y'),
                        'expenses' => [],
                        'total' => 0
                    ];
                }
                
                $expensesByDate[$dateKey]['expenses'][] = $expense;
                $expensesByDate[$dateKey]['total'] += $expense->gharama;
            }

            return [
                'matumizi' => $matumizi,
                'expensesByDate' => $expensesByDate,
                'totalExpenses' => $totalExpenses,
                'totalsByCategory' => $totalsByCategory,
                'reportTitle' => 'Ripoti ya Matumizi',
                'reportSubtitle' => 'Matumizi ya fedha kwa kipindi kilichochaguliwa'
            ];

        } catch (\Exception $e) {
            \Log::error('Error in getExpensesData: ' . $e->getMessage());
            return [
                'matumizi' => collect([]),
                'expensesByDate' => [],
                'totalExpenses' => 0,
                'totalsByCategory' => [],
                'reportTitle' => 'Ripoti ya Matumizi',
                'reportSubtitle' => 'Matumizi ya fedha kwa kipindi kilichochaguliwa'
            ];
        }
    }

    // Get income by payment method
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
                ->get();

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
                ->get();

            // Calculate totals
            $totalCash = 0;
            $totalMobile = 0;
            $totalBank = 0;
            
            foreach ($salesByMethod as $item) {
                $item->lipa_kwa = $this->cleanUtf8String($item->lipa_kwa);
                switch($item->lipa_kwa ?? 'cash') {
                    case 'cash': $totalCash += $item->total; break;
                    case 'lipa_namba': $totalMobile += $item->total; break;
                    case 'bank': $totalBank += $item->total; break;
                }
            }
            
            foreach ($debtsByMethod as $item) {
                $item->lipa_kwa = $this->cleanUtf8String($item->lipa_kwa);
                switch($item->lipa_kwa ?? 'cash') {
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
                'reportTitle' => 'Ripoti ya Mapato kwa Njia ya Malipo',
                'reportSubtitle' => 'Mapato kulingana na njia ya malipo'
            ];

        } catch (\Exception $e) {
            \Log::error('Error in getIncomeByPaymentMethod: ' . $e->getMessage());
            return [
                'salesByMethod' => collect([]),
                'debtsByMethod' => collect([]),
                'totalCash' => 0,
                'totalMobile' => 0,
                'totalBank' => 0,
                'grandTotal' => 0,
                'reportTitle' => 'Ripoti ya Mapato kwa Njia ya Malipo',
                'reportSubtitle' => 'Mapato kulingana na njia ya malipo'
            ];
        }
    }

    // Get general data
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

            // FAIDA YA MAUZO - Standard profit calculation
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

            // FAIDA YA MAREJESHO - FIFO method (cost recovery first, then profit)
            $faidaMarejesho = 0;
            $marejeshos = Marejesho::where('company_id', $companyId)
                ->when($dateRange['start'], function($q) use ($dateRange) {
                    $q->whereDate('tarehe', '>=', $dateRange['start']->format('Y-m-d'));
                })
                ->when($dateRange['end'], function($q) use ($dateRange) {
                    $q->whereDate('tarehe', '<=', $dateRange['end']->format('Y-m-d'));
                })
                ->with(['madeni.bidhaa'])
                ->orderBy('tarehe', 'asc') // Process in chronological order
                ->get();

            // Track each debt's repayment progress
            $debtProgress = [];

            foreach ($marejeshos as $marejesho) {
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

            // Fedha Dukani
            $fedhaDukani = $jumlaMapato - $jumlaMatumizi;

            // Faida Halisi
            $totalProfit = $faidaMauzo + $faidaMarejesho;
            $faidaHalisi = $totalProfit - $jumlaMatumizi;

            return [
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
                'jumlaMatumizi' => $jumlaMatumizi,
                'faidaMarejesho' => $faidaMarejesho,
                'faidaMauzo' => $faidaMauzo,
                'fedhaDukani' => $fedhaDukani,
                'faidaHalisi' => $faidaHalisi,
                'reportTitle' => 'Ripoti ya Jumla ya Biashara',
                'reportSubtitle' => 'Muhtasari wa shughuli zote za biashara'
            ];

        } catch (\Exception $e) {
            \Log::error('Error in getGeneralData: ' . $e->getMessage());
            
            return [
                'mapatoCashMauzo' => 0,
                'mapatoMobileMauzo' => 0,
                'mapatoBankMauzo' => 0,
                'mapatoMauzo' => 0,
                'mapatoCashMadeni' => 0,
                'mapatoMobileMadeni' => 0,
                'mapatoBankMadeni' => 0,
                'mapatoMadeni' => 0,
                'jumlaMapatoCash' => 0,
                'jumlaMapatoMobile' => 0,
                'jumlaMapatoBank' => 0,
                'jumlaMapato' => 0,
                'jumlaMatumizi' => 0,
                'faidaMarejesho' => 0,
                'faidaMauzo' => 0,
                'fedhaDukani' => 0,
                'faidaHalisi' => 0,
                'reportTitle' => 'Ripoti ya Jumla ya Biashara',
                'reportSubtitle' => 'Muhtasari wa shughuli zote za biashara'
            ];
        }
    }

    // Helper method to format decimals
    private function formatDecimal($value)
    {
        if (is_numeric($value)) {
            return $value % 1 == 0 ? (string)(int)$value : number_format($value, 2);
        }
        return $value;
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
}