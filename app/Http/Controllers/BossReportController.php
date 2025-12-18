<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Expense;
use App\Models\Customer;
use App\Models\Supplier;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class BossReportController extends Controller
{
    /**
     * Display the report selection page
     */
    public function index()
    {
        $reportTypes = [
            'mauzo' => [
                'title' => 'Ripoti ya Mauzo',
                'description' => 'Orodha kamili ya mauzo yote',
                'icon' => '📈'
            ],
            'faida' => [
                'title' => 'Ripoti ya Faida',
                'description' => 'Faida na mapato ya biashara',
                'icon' => '💰'
            ],
            'gharama' => [
                'title' => 'Ripoti ya Gharama',
                'description' => 'Gharama zote za uendeshaji',
                'icon' => '💸'
            ],
            'hisabati' => [
                'title' => 'Ripoti ya Hisabati',
                'description' => 'Bidhaa zilizopo kwenye hisa',
                'icon' => '📦'
            ],
            'wateja' => [
                'title' => 'Ripoti ya Wateja',
                'description' => 'Taarifa za wateja wote',
                'icon' => '👥'
            ],
            'mwenendo' => [
                'title' => 'Ripoti ya Mwenendo',
                'description' => 'Mienendo ya biashara kwa muda',
                'icon' => '📊'
            ],
            'jumla' => [
                'title' => 'Ripoti ya Jumla',
                'description' => 'Muhtasari kamili wa biashara',
                'icon' => '📋'
            ]
        ];

        return view('boss.reports.index', compact('reportTypes'));
    }

    /**
     * Generate and download report
     */
    public function generate(Request $request)
    {
        $request->validate([
            'type' => 'required|in:mauzo,faida,gharama,hisabati,wateja,mwenendo,jumla',
            'period' => 'required|in:leo,jana,wiki,mwezi,mwaka,yote,custom',
            'from' => 'nullable|date',
            'to' => 'nullable|date',
            'format' => 'in:pdf,html'
        ]);

        $type = $request->type;
        $period = $request->period;
        $format = $request->format ?? 'pdf';
        
        // Get report data
        $data = $this->getReportData($type, $period, $request);
        $data['reportType'] = $type;
        $data['period'] = $period;
        $data['generatedAt'] = Carbon::now()->format('d/m/Y H:i:s');
        
        // Add company info
        $data['companyName'] = config('app.name', 'Biashara Yako');
        $data['companyAddress'] = 'Dar es Salaam, Tanzania';
        
        if ($format === 'html') {
            return view('boss.reports.templates.' . $type, $data);
        }
        
        // Generate PDF
        $pdf = Pdf::loadView('boss.reports.templates.' . $type, $data);
        
        // Set paper size and orientation
        $pdf->setPaper('A4', 'portrait');
        
        // Download with appropriate filename
        return $pdf->download($this->getFileName($type, $period));
    }

    /**
     * Preview report on screen
     */
    public function preview(Request $request)
    {
        $type = $request->query('type', 'mauzo');
        $period = $request->query('period', 'leo');
        
        $data = $this->getReportData($type, $period, $request);
        $data['reportType'] = $type;
        $data['period'] = $period;
        $data['generatedAt'] = Carbon::now()->format('d/m/Y H:i:s');
        $data['companyName'] = config('app.name', 'Biashara Yako');
        
        return view('boss.reports.preview', $data);
    }

    /**
     * Get report data based on type and period
     */
    private function getReportData($type, $period, $request)
    {
        $dateRange = $this->getDateRange($period, $request);
        
        switch ($type) {
            case 'mauzo':
                return $this->getSalesData($dateRange);
            case 'faida':
                return $this->getProfitData($dateRange);
            case 'gharama':
                return $this->getExpensesData($dateRange);
            case 'hisabati':
                return $this->getInventoryData();
            case 'wateja':
                return $this->getCustomersData($dateRange);
            case 'mwenendo':
                return $this->getTrendsData($dateRange);
            case 'jumla':
                return $this->getSummaryData($dateRange);
            default:
                return $this->getSalesData($dateRange);
        }
    }

    /**
     * Get date range based on period
     */
    private function getDateRange($period, $request)
    {
        $now = Carbon::now();
        $today = Carbon::today();
        
        switch ($period) {
            case 'leo':
                $start = $today->copy()->startOfDay();
                $end = $today->copy()->endOfDay();
                $label = 'Leo (' . $today->format('d/m/Y') . ')';
                break;
                
            case 'jana':
                $start = $today->copy()->subDay()->startOfDay();
                $end = $today->copy()->subDay()->endOfDay();
                $label = 'Jana (' . $start->format('d/m/Y') . ')';
                break;
                
            case 'wiki':
                $start = $today->copy()->startOfWeek();
                $end = $today->copy()->endOfWeek();
                $label = 'Wiki hii (' . $start->format('d/m') . ' - ' . $end->format('d/m/Y') . ')';
                break;
                
            case 'mwezi':
                $start = $today->copy()->startOfMonth();
                $end = $today->copy()->endOfMonth();
                $label = 'Mwezi huu (' . $start->format('F Y') . ')';
                break;
                
            case 'mwaka':
                $start = $today->copy()->startOfYear();
                $end = $today->copy()->endOfYear();
                $label = 'Mwaka huu (' . $start->format('Y') . ')';
                break;
                
            case 'yote':
                $start = null;
                $end = null;
                $label = 'Muda wote';
                break;
                
            case 'custom':
                $start = Carbon::parse($request->from)->startOfDay();
                $end = Carbon::parse($request->to)->endOfDay();
                $label = $start->format('d/m/Y') . ' - ' . $end->format('d/m/Y');
                break;
                
            default:
                $start = $today->copy()->startOfDay();
                $end = $today->copy()->endOfDay();
                $label = 'Leo';
                break;
        }
        
        return [
            'start' => $start,
            'end' => $end,
            'label' => $label
        ];
    }

    /**
     * Get sales data
     */
    private function getSalesData($dateRange)
    {
        $query = Sale::with(['product', 'customer'])
                    ->select('*', DB::raw('quantity * unit_price as total_amount'));
        
        if ($dateRange['start']) {
            $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
        }
        
        $sales = $query->orderBy('created_at', 'desc')->get();
        
        $summary = [
            'Jumla ya Mauzo' => $sales->sum('total_amount'),
            'Idadi ya Mauzo' => $sales->count(),
            'Wastani wa Mauzo' => $sales->avg('total_amount'),
            'Mauzo ya Juu' => $sales->max('total_amount'),
            'Mauzo ya Chini' => $sales->min('total_amount')
        ];
        
        return [
            'title' => 'Ripoti ya Mauzo',
            'sales' => $sales,
            'summary' => $summary,
            'dateRange' => $dateRange['label']
        ];
    }

    /**
     * Get profit data
     */
    private function getProfitData($dateRange)
    {
        $query = Sale::with('product')
                    ->select(
                        '*',
                        DB::raw('quantity * unit_price as revenue'),
                        DB::raw('quantity * cost_price as cost'),
                        DB::raw('(quantity * unit_price) - (quantity * cost_price) as profit')
                    );
        
        if ($dateRange['start']) {
            $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
        }
        
        $sales = $query->orderBy('created_at', 'desc')->get();
        
        $totalRevenue = $sales->sum('revenue');
        $totalCost = $sales->sum('cost');
        $totalProfit = $sales->sum('profit');
        
        $summary = [
            'Jumla ya Mapato' => $totalRevenue,
            'Jumla ya Gharama' => $totalCost,
            'Faida ya Jumla' => $totalProfit,
            'Asilimia ya Faida' => $totalRevenue > 0 ? round(($totalProfit / $totalRevenue) * 100, 2) : 0,
            'Mauzo yenye Faida Kubwa' => $sales->max('profit')
        ];
        
        return [
            'title' => 'Ripoti ya Faida',
            'sales' => $sales,
            'summary' => $summary,
            'dateRange' => $dateRange['label'],
            'totalRevenue' => $totalRevenue,
            'totalCost' => $totalCost,
            'totalProfit' => $totalProfit
        ];
    }

    /**
     * Get expenses data
     */
    private function getExpensesData($dateRange)
    {
        $query = Expense::query();
        
        if ($dateRange['start']) {
            $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
        }
        
        $expenses = $query->orderBy('created_at', 'desc')->get();
        
        // Group by category
        $categories = $expenses->groupBy('category')->map(function ($items) {
            return [
                'count' => $items->count(),
                'total' => $items->sum('amount'),
                'items' => $items
            ];
        });
        
        $summary = [
            'Jumla ya Gharama' => $expenses->sum('amount'),
            'Idadi ya Gharama' => $expenses->count(),
            'Wastani wa Gharama' => $expenses->avg('amount'),
            'Gharama Kubwa' => $expenses->max('amount'),
            'Aina za Gharama' => $categories->count()
        ];
        
        return [
            'title' => 'Ripoti ya Gharama',
            'expenses' => $expenses,
            'categories' => $categories,
            'summary' => $summary,
            'dateRange' => $dateRange['label']
        ];
    }

    /**
     * Get inventory data
     */
    private function getInventoryData()
    {
        $products = Product::orderBy('jina')->get();
        
        $totalValue = $products->sum(function($product) {
            return $product->idadi * $product->bei_nunua;
        });
        
        $totalSellingValue = $products->sum(function($product) {
            return $product->idadi * $product->bei_kuuza;
        });
        
        $potentialProfit = $products->sum(function($product) {
            return $product->idadi * ($product->bei_kuuza - $product->bei_nunua);
        });
        
        $lowStock = $products->where('idadi', '<', 10)->count();
        
        $summary = [
            'Jumla ya Bidhaa' => $products->count(),
            'Thamani ya Jumla' => $totalValue,
            'Thamani ya Kuuza' => $totalSellingValue,
            'Faida ya Kiasi' => $potentialProfit,
            'Bidhaa chini ya 10' => $lowStock
        ];
        
        return [
            'title' => 'Ripoti ya Hisabati',
            'products' => $products,
            'summary' => $summary
        ];
    }

    /**
     * Get customers data
     */
    private function getCustomersData($dateRange)
    {
        $customers = Customer::withCount(['sales' => function($query) use ($dateRange) {
            if ($dateRange['start']) {
                $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
            }
        }])
        ->withSum(['sales' => function($query) use ($dateRange) {
            if ($dateRange['start']) {
                $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
            }
        }], 'total_price')
        ->orderBy('sales_sum_total_price', 'desc')
        ->get();
        
        $summary = [
            'Jumla ya Wateja' => $customers->count(),
            'Wateja walionunua' => $customers->where('sales_count', '>', 0)->count(),
            'Jumla ya Mauzo' => $customers->sum('sales_sum_total_price'),
            'Wastani wa Mauzo kwa Mteja' => $customers->where('sales_count', '>', 0)->avg('sales_sum_total_price'),
            'Mteja Bora' => $customers->max('sales_sum_total_price')
        ];
        
        return [
            'title' => 'Ripoti ya Wateja',
            'customers' => $customers,
            'summary' => $summary,
            'dateRange' => $dateRange['label']
        ];
    }

    /**
     * Get trends data
     */
    private function getTrendsData($dateRange)
    {
        // Daily sales trend
        $dailySales = Sale::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(quantity * unit_price) as total_sales'),
                DB::raw('COUNT(*) as transaction_count')
            )
            ->when($dateRange['start'], function($query) use ($dateRange) {
                return $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
            })
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Product performance
        $productPerformance = Sale::select(
                'product_id',
                DB::raw('SUM(quantity) as total_sold'),
                DB::raw('SUM(quantity * unit_price) as total_revenue')
            )
            ->with('product')
            ->when($dateRange['start'], function($query) use ($dateRange) {
                return $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
            })
            ->groupBy('product_id')
            ->orderBy('total_sold', 'desc')
            ->limit(10)
            ->get();
        
        // Hourly trend
        $hourlyTrend = Sale::select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('COUNT(*) as transactions')
            )
            ->when($dateRange['start'], function($query) use ($dateRange) {
                return $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
            })
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();
        
        return [
            'title' => 'Ripoti ya Mwenendo',
            'dailySales' => $dailySales,
            'productPerformance' => $productPerformance,
            'hourlyTrend' => $hourlyTrend,
            'dateRange' => $dateRange['label']
        ];
    }

    /**
     * Get summary data
     */
    private function getSummaryData($dateRange)
    {
        // Sales data
        $salesData = $this->getSalesData($dateRange);
        $profitData = $this->getProfitData($dateRange);
        $expensesData = $this->getExpensesData($dateRange);
        $inventoryData = $this->getInventoryData();
        $customersData = $this->getCustomersData($dateRange);
        
        // Calculate net profit
        $netProfit = $profitData['totalProfit'] - $expensesData['summary']['Jumla ya Gharama'];
        
        $overallSummary = [
            'Mapato ya Jumla' => $profitData['totalRevenue'],
            'Gharama ya Bidhaa' => $profitData['totalCost'],
            'Faida ya Mauzo' => $profitData['totalProfit'],
            'Gharama za Uendeshaji' => $expensesData['summary']['Jumla ya Gharama'],
            'Faida ya Wazi' => $netProfit,
            'Asilimia ya Faida' => $profitData['totalRevenue'] > 0 ? round(($netProfit / $profitData['totalRevenue']) * 100, 2) : 0,
            'Thamani ya Hisa' => $inventoryData['summary']['Thamani ya Jumla'],
            'Idadi ya Wateja' => $customersData['summary']['Jumla ya Wateja']
        ];
        
        return [
            'title' => 'Ripoti ya Jumla ya Biashara',
            'salesSummary' => $salesData['summary'],
            'profitSummary' => $profitData['summary'],
            'expensesSummary' => $expensesData['summary'],
            'inventorySummary' => $inventoryData['summary'],
            'customersSummary' => $customersData['summary'],
            'overallSummary' => $overallSummary,
            'dateRange' => $dateRange['label']
        ];
    }

    /**
     * Generate filename for download
     */
    private function getFileName($type, $period)
    {
        $typeNames = [
            'mauzo' => 'mauzo',
            'faida' => 'faida',
            'gharama' => 'gharama',
            'hisabati' => 'hisabati',
            'wateja' => 'wateja',
            'mwenendo' => 'mwenendo',
            'jumla' => 'jumla'
        ];
        
        $periodNames = [
            'leo' => 'leo',
            'jana' => 'jana',
            'wiki' => 'wiki',
            'mwezi' => 'mwezi',
            'mwaka' => 'mwaka',
            'yote' => 'yote',
            'custom' => 'tarehe'
        ];
        
        $typeName = $typeNames[$type] ?? 'ripoti';
        $periodName = $periodNames[$period] ?? 'muda';
        $date = Carbon::now()->format('Y-m-d_H-i');
        
        return "{$typeName}_{$periodName}_{$date}.pdf";
    }

    /**
     * Get report statistics
     */
    public function statistics()
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();
        
        $stats = [
            'leo' => [
                'mauzo' => Sale::whereDate('created_at', $today)->sum(DB::raw('quantity * unit_price')),
                'mauzo_count' => Sale::whereDate('created_at', $today)->count(),
                'faida' => Sale::whereDate('created_at', $today)
                    ->sum(DB::raw('(quantity * unit_price) - (quantity * cost_price)')),
                'gharama' => Expense::whereDate('created_at', $today)->sum('amount')
            ],
            'jana' => [
                'mauzo' => Sale::whereDate('created_at', $yesterday)->sum(DB::raw('quantity * unit_price')),
                'mauzo_count' => Sale::whereDate('created_at', $yesterday)->count(),
                'faida' => Sale::whereDate('created_at', $yesterday)
                    ->sum(DB::raw('(quantity * unit_price) - (quantity * cost_price)')),
                'gharama' => Expense::whereDate('created_at', $yesterday)->sum('amount')
            ],
            'wiki' => [
                'mauzo' => Sale::where('created_at', '>=', $thisWeek)->sum(DB::raw('quantity * unit_price')),
                'mauzo_count' => Sale::where('created_at', '>=', $thisWeek)->count(),
                'faida' => Sale::where('created_at', '>=', $thisWeek)
                    ->sum(DB::raw('(quantity * unit_price) - (quantity * cost_price)')),
                'gharama' => Expense::where('created_at', '>=', $thisWeek)->sum('amount')
            ],
            'mwezi' => [
                'mauzo' => Sale::where('created_at', '>=', $thisMonth)->sum(DB::raw('quantity * unit_price')),
                'mauzo_count' => Sale::where('created_at', '>=', $thisMonth)->count(),
                'faida' => Sale::where('created_at', '>=', $thisMonth)
                    ->sum(DB::raw('(quantity * unit_price) - (quantity * cost_price)')),
                'gharama' => Expense::where('created_at', '>=', $thisMonth)->sum('amount')
            ]
        ];
        
        return response()->json($stats);
    }
}