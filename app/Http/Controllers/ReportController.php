<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Company;
use PDF;

class ReportController extends Controller
{
    public function index()
    {
        // Total companies
        $totalCompanies = Company::count();

        // Free trial companies
        $freeTrialCompanies = Company::where('package', 'Free Trial 14 days')->count();
        $freeTrialPercentage = $totalCompanies > 0 ? round(($freeTrialCompanies / $totalCompanies) * 100, 1) : 0;

        // Paid package companies
        $paidPackageCompanies = Company::where('package', '!=', 'Free Trial 14 days')
            ->whereNotNull('package')
            ->count();
        $paidPackagePercentage = $totalCompanies > 0 ? round(($paidPackageCompanies / $totalCompanies) * 100, 1) : 0;

        // Today's registrations
        $todayRegistrations = Company::whereDate('created_at', Carbon::today())->count();

        // New companies this week
        $newCompaniesThisWeek = Company::whereBetween('created_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ])->count();

        // Package distribution
        $package180Companies = Company::where('package', '180 days')->count();
        $package180Percentage = $totalCompanies > 0 ? round(($package180Companies / $totalCompanies) * 100, 1) : 0;

        $package366Companies = Company::where('package', '366 days')->count();
        $package366Percentage = $totalCompanies > 0 ? round(($package366Companies / $totalCompanies) * 100, 1) : 0;

        $noPackageCompanies = Company::whereNull('package')->orWhere('package', '')->count();
        $noPackagePercentage = $totalCompanies > 0 ? round(($noPackageCompanies / $totalCompanies) * 100, 1) : 0;

        // Recent registrations (last 24 hours)
        $recentRegistrations = Company::where('created_at', '>=', Carbon::now()->subDay())
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get();

        // Monthly registration trends (last 6 months)
        $monthlyRegistrations = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $count = Company::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $monthlyRegistrations[] = [
                'month' => $date->format('M'),
                'year' => $date->format('Y'),
                'count' => $count
            ];
        }

        // Today growth calculation
        $yesterdayRegistrations = Company::whereDate('created_at', Carbon::yesterday())->count();
        $todayGrowth = $yesterdayRegistrations > 0 ?
            round((($todayRegistrations - $yesterdayRegistrations) / $yesterdayRegistrations) * 100, 1) : 0;

        // New paid companies this month
        $newPaidThisMonth = Company::where('package', '!=', 'Free Trial 14 days')
            ->whereNotNull('package')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        return view('admin.reports', compact(
            'totalCompanies',
            'freeTrialCompanies',
            'freeTrialPercentage',
            'paidPackageCompanies',
            'paidPackagePercentage',
            'todayRegistrations',
            'newCompaniesThisWeek',
            'package180Companies',
            'package180Percentage',
            'package366Companies',
            'package366Percentage',
            'noPackageCompanies',
            'noPackagePercentage',
            'recentRegistrations',
            'monthlyRegistrations',
            'todayGrowth',
            'newPaidThisMonth'
        ));
    }

    public function generate(Request $request)
    {
        $type = $request->get('type', 'companies');
        return back()->with('success', 'Ripoti ya ' . $type . ' imetengenezwa kikamilifu!');
    }

    public function export($format)
    {
        return back()->with('success', 'Ripoti imepakuliwa kwa muundo wa ' . strtoupper($format));
    }

    public function downloadCompaniesReport(Request $request)
    {
        $period = $request->get('period', 'today');

        $query = Company::query();

        switch ($period) {
            case 'today':
                $query->whereDate('created_at', Carbon::today());
                $title = 'Makampuni Yaliyosajiliwa Leo';
                $filename = 'companies_today_' . Carbon::today()->format('Y_m_d');
                break;

            case 'yesterday':
                $query->whereDate('created_at', Carbon::yesterday());
                $title = 'Makampuni Yaliyosajiliwa Jana';
                $filename = 'companies_yesterday_' . Carbon::yesterday()->format('Y_m_d');
                break;

            case 'this_week':
                $query->whereBetween('created_at', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                ]);
                $title = 'Makampuni Yaliyosajiliwa Wiki Hii';
                $filename = 'companies_this_week_' . Carbon::now()->format('Y_m_d');
                break;

            case 'last_week':
                $query->whereBetween('created_at', [
                    Carbon::now()->subWeek()->startOfWeek(),
                    Carbon::now()->subWeek()->endOfWeek()
                ]);
                $title = 'Makampuni Yaliyosajiliwa Wiki Iliyopita';
                $filename = 'companies_last_week_' . Carbon::now()->format('Y_m_d');
                break;

            case 'this_month':
                $query->whereYear('created_at', Carbon::now()->year)
                    ->whereMonth('created_at', Carbon::now()->month);
                $title = 'Makampuni Yaliyosajiliwa Mwezi Huu';
                $filename = 'companies_this_month_' . Carbon::now()->format('Y_m');
                break;

            case 'last_month':
                $query->whereYear('created_at', Carbon::now()->subMonth()->year)
                    ->whereMonth('created_at', Carbon::now()->subMonth()->month);
                $title = 'Makampuni Yaliyosajiliwa Mwezi Uliopita';
                $filename = 'companies_last_month_' . Carbon::now()->subMonth()->format('Y_m');
                break;

            default:
                $title = 'Orodha ya Makampuni Yote';
                $filename = 'companies_all_' . Carbon::now()->format('Y_m_d');
        }

        $companies = $query->orderBy('created_at', 'desc')->get();

        // ONLY PDF
        return $this->downloadPDF($companies, $title, $filename);
    }

    private function downloadPDF($companies, $title, $filename)
    {
        $data = [
            'companies' => $companies,
            'title' => $title,
            'date' => Carbon::now()->format('d/m/Y H:i'),
            'total' => $companies->count()
        ];

        $pdf = PDF::loadView('admin.exports.companies-pdf', $data);
        return $pdf->download($filename . '.pdf');
    }
}
