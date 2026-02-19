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
    // Show report selection page
    public function select()
    {
        $user = Auth::user();
        $company = $user->company;
        
        return view('user.reports.select', compact('company'));
    }

    // Generate report preview
    public function generate(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:sales,manunuzi,matumizi,general,mapato_by_method',
            'date_range' => 'required|in:today,yesterday,week,month,year,custom',
            'from' => 'required_if:date_range,custom|date',
            'to' => 'required_if:date_range,custom|date|after_or_equal:from',
        ]);

        $dateRange = $this->getDateRange($request->date_range, $request);
        $user = Auth::user();
        $company = $user->company;
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

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    // Download PDF report
    public function download(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:sales,manunuzi,matumizi,general,mapato_by_method',
            'date_range' => 'required|in:today,yesterday,week,month,year,custom',
            'from' => 'required_if:date_range,custom|date',
            'to' => 'required_if:date_range,custom|date|after_or_equal:from',
        ]);

        $dateRange = $this->getDateRange($request->date_range, $request);
        $user = Auth::user();
        $company = $user->company;
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

        // Generate PDF
        $pdf = PDF::loadView('user.reports.pdf', $data);
        $pdf->setPaper('A4', 'portrait');
        
        $fileName = $this->generateFileName($reportType, $request->date_range, $request);
        
        return $pdf->download($fileName);
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

            foreach ($sales as $sale) {
                if (!$sale->bidhaa) continue;
                
                $totalSales += $sale->jumla;
                
                // Categorize by payment method
                $paymentMethod = $sale->lipa_kwa ?? 'cash';
                
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
                
                $paymentMethod = $repayment->lipa_kwa ?? 'cash';
                
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

            // Group sales by date for detailed view
            $salesByDate = [];
            foreach ($sales as $sale) {
                $dateKey = $sale->created_at->format('Y-m-d');
                if (!isset($salesByDate[$dateKey])) {
                    $salesByDate[$dateKey] = [
                        'date' => $sale->created_at->format('d/m/Y'),
                        'sales' => [],
                        'total' => 0,
                        'cash' => 0,
                        'mobile' => 0,
                        'bank' => 0
                    ];
                }
                
                $salesByDate[$dateKey]['sales'][] = $sale;
                $salesByDate[$dateKey]['total'] += $sale->jumla;
                
                $paymentMethod = $sale->lipa_kwa ?? 'cash';
                switch($paymentMethod) {
                    case 'cash':
                        $salesByDate[$dateKey]['cash'] += $sale->jumla;
                        break;
                    case 'lipa_namba':
                        $salesByDate[$dateKey]['mobile'] += $sale->jumla;
                        break;
                    case 'bank':
                        $salesByDate[$dateKey]['bank'] += $sale->jumla;
                        break;
                }
            }

            return [
                'sales' => $sales,
                'salesByDate' => $salesByDate,
                'debtRepayments' => $debtRepayments,
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
                'reportTitle' => 'Ripoti ya Mauzo',
                'reportSubtitle' => 'Mapato na mauzo kwa kipindi kilichochaguliwa'
            ];

        } catch (\Exception $e) {
            return [
                'sales' => collect([]),
                'salesByDate' => [],
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
                'reportTitle' => 'Ripoti ya Mauzo',
                'reportSubtitle' => 'Mapato na mauzo kwa kipindi kilichochaguliwa'
            ];
        }
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
                switch($item->lipa_kwa ?? 'cash') {
                    case 'cash': $totalCash += $item->total; break;
                    case 'lipa_namba': $totalMobile += $item->total; break;
                    case 'bank': $totalBank += $item->total; break;
                }
            }
            
            foreach ($debtsByMethod as $item) {
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
            return [
                'mapatoMauzo' => 0,
                'mapatoMadeni' => 0,
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