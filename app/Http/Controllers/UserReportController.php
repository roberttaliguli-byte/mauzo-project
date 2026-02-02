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
            'report_type' => 'required|in:sales,manunuzi,general',
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
            case 'general':
                $generalData = $this->getGeneralData($companyId, $dateRange);
                $data = array_merge($data, $generalData);
                break;
        }

        // Generate PDF
        $pdf = PDF::loadView('user.reports.pdf', $data);
        $pdf->setPaper('A4', 'portrait');
        
        $fileName = $this->generateFileName($reportType, $timePeriod, $request);
        
        return $pdf->download($fileName);
    }

    // Get sales data - FIXED
    private function getSalesData($companyId, $dateRange)
    {
        try {
            // Get sales
            $sales = Mauzo::where('company_id', $companyId)
                ->when($dateRange['start'], function($q) use ($dateRange) {
                    // Use DATE() function to compare only date part
                    $q->whereDate('created_at', '>=', $dateRange['start']->format('Y-m-d'));
                })
                ->when($dateRange['end'], function($q) use ($dateRange) {
                    $q->whereDate('created_at', '<=', $dateRange['end']->format('Y-m-d'));
                })
                ->with('bidhaa')
                ->orderBy('created_at', 'desc')
                ->get();

            // Calculate totals
            $totalSales = 0;
            $totalProfit = 0;

            foreach ($sales as $sale) {
                if (!$sale->bidhaa) continue;
                
                // Calculate actual discount
                $actualDiscount = $this->calculateActualDiscount(
                    $sale->punguzo, 
                    $sale->punguzo_aina, 
                    $sale->idadi
                );
                
                // Get buying price
                $buyingPrice = $sale->bidhaa->bei_nunua ?? 0;
                
                // Calculate profit
                $profit = ($sale->bei * $sale->idadi) - ($buyingPrice * $sale->idadi) - $actualDiscount;
                
                $totalSales += $sale->jumla;
                $totalProfit += $profit;
            }

            // Get debt repayments - FIXED: Use DATE comparison for tarehe column
            $totalDebtRepayments = Marejesho::where('company_id', $companyId)
                ->when($dateRange['start'], function($q) use ($dateRange) {
                    // Use DATE comparison for DATE column (tarehe)
                    $q->whereDate('tarehe', '>=', $dateRange['start']->format('Y-m-d'));
                })
                ->when($dateRange['end'], function($q) use ($dateRange) {
                    $q->whereDate('tarehe', '<=', $dateRange['end']->format('Y-m-d'));
                })
                ->sum('kiasi');

            $grandTotal = $totalSales + $totalDebtRepayments;

            return [
                'sales' => $sales,
                'totalSales' => $totalSales,
                'totalProfit' => $totalProfit,
                'totalDebtRepayments' => $totalDebtRepayments,
                'grandTotal' => $grandTotal,
            ];

        } catch (\Exception $e) {
            return [
                'sales' => collect([]),
                'totalSales' => 0,
                'totalProfit' => 0,
                'totalDebtRepayments' => 0,
                'grandTotal' => 0,
            ];
        }
    }

    // Get purchases data - FIXED
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

            // Calculate total cost
            $totalCost = 0;

            foreach ($manunuzi as $purchase) {
                $cost = $purchase->idadi * $purchase->bei;
                $totalCost += $cost;
            }

            return [
                'manunuzi' => $manunuzi,
                'totalCost' => $totalCost,
            ];

        } catch (\Exception $e) {
            return [
                'manunuzi' => collect([]),
                'totalCost' => 0,
            ];
        }
    }

    // Get general data - FIXED
    private function getGeneralData($companyId, $dateRange)
    {
        try {
            // Mapato ya Mauzo - FIXED: Use whereDate for created_at
            $mapatoMauzo = Mauzo::where('company_id', $companyId)
                ->when($dateRange['start'], function($q) use ($dateRange) {
                    $q->whereDate('created_at', '>=', $dateRange['start']->format('Y-m-d'));
                })
                ->when($dateRange['end'], function($q) use ($dateRange) {
                    $q->whereDate('created_at', '<=', $dateRange['end']->format('Y-m-d'));
                })
                ->sum('jumla');

            // Mapato ya Madeni - FIXED: Use whereDate for tarehe (DATE column)
            $mapatoMadeni = Marejesho::where('company_id', $companyId)
                ->when($dateRange['start'], function($q) use ($dateRange) {
                    $q->whereDate('tarehe', '>=', $dateRange['start']->format('Y-m-d'));
                })
                ->when($dateRange['end'], function($q) use ($dateRange) {
                    $q->whereDate('tarehe', '<=', $dateRange['end']->format('Y-m-d'));
                })
                ->sum('kiasi');

            // Jumla ya Mapato
            $jumlaMapato = $mapatoMauzo + $mapatoMadeni;

            // Jumla ya Matumizi - FIXED: Use whereDate for created_at
            $jumlaMatumizi = Matumizi::where('company_id', $companyId)
                ->when($dateRange['start'], function($q) use ($dateRange) {
                    $q->whereDate('created_at', '>=', $dateRange['start']->format('Y-m-d'));
                })
                ->when($dateRange['end'], function($q) use ($dateRange) {
                    $q->whereDate('created_at', '<=', $dateRange['end']->format('Y-m-d'));
                })
                ->sum('gharama');

            // Faida ya Marejesho - FIXED: Use whereDate for tarehe
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

            // Faida ya Mauzo - FIXED: Use whereDate for created_at
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
                'mapatoMauzo' => $mapatoMauzo,
                'mapatoMadeni' => $mapatoMadeni,
                'jumlaMapato' => $jumlaMapato,
                'jumlaMatumizi' => $jumlaMatumizi,
                'faidaMarejesho' => $faidaMarejesho,
                'faidaMauzo' => $faidaMauzo,
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

    // Helper method to calculate actual discount
    private function calculateActualDiscount($discount, $discountType, $quantity)
    {
        if ($discountType === 'bidhaa') {
            return $discount * $quantity;
        }
        return $discount;
    }

    // Get date range based on period - CORRECT VERSION
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
            'general' => 'jumla'
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