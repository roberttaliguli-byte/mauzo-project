<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\SmsLog;
use App\Services\SMSService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminSmsController extends Controller
{
    protected $smsService;

    public function __construct(SMSService $smsService)
    {
        $this->smsService = $smsService;
    }

    // In Laravel 11, use this syntax instead of constructor middleware
    public static function middleware(): array
    {
        return [
            'auth',
            'role:admin'
        ];
    }

    public function index()
    {
        // Get all companies with their SMS statistics
        $companies = Company::withCount(['smsLogs as total_sms' => function($query) {
                $query->whereNotNull('sent_at');
            }])
            ->withCount(['smsLogs as today_sms' => function($query) {
                $query->whereDate('sent_at', today());
            }])
            ->withCount(['smsLogs as month_sms' => function($query) {
                $query->whereMonth('sent_at', now()->month)
                      ->whereYear('sent_at', now()->year);
            }])
            ->withCount(['smsLogs as week_sms' => function($query) {
                $query->whereBetween('sent_at', [now()->startOfWeek(), now()->endOfWeek()]);
            }])
            ->orderBy('total_sms', 'desc')
            ->paginate(15);

        // Overall statistics
        $totalSmsAll = SmsLog::count();
        $totalSmsToday = SmsLog::whereDate('sent_at', today())->count();
        $totalSmsMonth = SmsLog::whereMonth('sent_at', now()->month)
            ->whereYear('sent_at', now()->year)
            ->count();
        $totalSmsWeek = SmsLog::whereBetween('sent_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        
        // Active companies (those that sent SMS in last 30 days)
        $activeCompanies = SmsLog::where('sent_at', '>=', now()->subDays(30))
            ->distinct('company_id')
            ->count('company_id');

        return view('admin.sms.index', compact(
            'companies',
            'totalSmsAll',
            'totalSmsToday',
            'totalSmsMonth',
            'totalSmsWeek',
            'activeCompanies'
        ));
    }

    /**
     * Show form to send SMS to a specific company
     */
    public function sendToCompanyForm(Company $company)
    {
        // Get company's SMS statistics
        $totalSms = SmsLog::where('company_id', $company->id)->count();
        $monthSms = SmsLog::where('company_id', $company->id)
            ->whereMonth('sent_at', now()->month)
            ->whereYear('sent_at', now()->year)
            ->count();
        
        $recentSms = SmsLog::where('company_id', $company->id)
            ->orderBy('sent_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.sms.send-to-company', compact('company', 'totalSms', 'monthSms', 'recentSms'));
    }

    /**
     * Send SMS to a company
     */
    public function sendToCompany(Request $request, Company $company)
    {
        $request->validate([
            'message' => 'required|string|max:1600',
            'send_to_owner' => 'boolean'
        ]);

        $recipients = [];
        
        // Send to company owner if requested
        if ($request->send_to_owner && $company->phone) {
            $recipients[] = $company->phone;
        }
        
        if (empty($recipients)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hakuna namba za simu za kutuma ujumbe'
                ], 422);
            }
            return redirect()->back()->with('error', 'Hakuna namba za simu za kutuma ujumbe');
        }

        $reference = 'ADMIN_TO_' . $company->id . '_' . time();
        
        // Add prefix to message to identify it's from admin
           // Format message: greeting + message + footer
    $message = "UJUMBE KUTOKA KWA Msimamizi:\n\n" . $request->message . "\n\n---\n powered by www.mauzosheetai.co.tz\n";
    
        $result = $this->smsService->sendSms($recipients, $message, $reference);
        
        // Log admin action
        \Illuminate\Support\Facades\Log::info('Admin sent SMS to company', [
            'company_id' => $company->id,
            'company_name' => $company->company_name,
            'recipients' => $recipients,
            'result' => $result
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($result);
        }

        if ($result['success']) {
            return redirect()->route('admin.sms.send-to-company', $company->id)
                ->with('success', 'Ujumbe umetumwa kikamilifu kwa kampuni!');
        }
        
        return redirect()->back()->with('error', $result['message']);
    }

    /**
     * View detailed SMS report for a specific company
     */
    public function companyReport(Company $company, Request $request)
    {
        $query = SmsLog::where('company_id', $company->id);
        
        // Apply date filters
        if ($request->filled('start_date')) {
            $query->whereDate('sent_at', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('sent_at', '<=', $request->end_date);
        }
        
        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $smsLogs = $query->orderBy('sent_at', 'desc')->paginate(20);
        
        // Statistics
        $totalSent = SmsLog::where('company_id', $company->id)->count();
        $totalFailed = SmsLog::where('company_id', $company->id)
            ->whereIn('status', ['FAILED', 'REJECTED'])
            ->count();
        $totalDelivered = SmsLog::where('company_id', $company->id)
            ->where('status', 'DELIVERED')
            ->count();
        
        return view('admin.sms.company-report', compact(
            'company',
            'smsLogs',
            'totalSent',
            'totalFailed',
            'totalDelivered'
        ));
    }

    /**
     * Export company SMS report to PDF
     */
    public function exportCompanyReport(Company $company, Request $request)
    {
        $query = SmsLog::where('company_id', $company->id);
        
        if ($request->filled('start_date')) {
            $query->whereDate('sent_at', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('sent_at', '<=', $request->end_date);
        }
        
        $smsLogs = $query->orderBy('sent_at', 'desc')->get();
        
        $totalSent = $smsLogs->count();
        $totalFailed = $smsLogs->whereIn('status', ['FAILED', 'REJECTED'])->count();
        $totalDelivered = $smsLogs->where('status', 'DELIVERED')->count();
        
        $pdf = Pdf::loadView('admin.sms.pdf.company-report', compact(
            'company',
            'smsLogs',
            'totalSent',
            'totalFailed',
            'totalDelivered',
            'request'
        ));
        
        return $pdf->download('sms-report-' . $company->company_name . '-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Get all SMS logs across all companies (admin view)
     */
    public function allLogs(Request $request)
    {
        $query = SmsLog::with('company');
        
        // Filter by company
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }
        
        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('sent_at', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('sent_at', '<=', $request->end_date);
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $logs = $query->orderBy('sent_at', 'desc')->paginate(30);
        
        $companies = Company::orderBy('company_name')->get(['id', 'company_name']);
        
        // Summary statistics
        $summary = [
            'total' => SmsLog::count(),
            'today' => SmsLog::whereDate('sent_at', today())->count(),
            'month' => SmsLog::whereMonth('sent_at', now()->month)->count(),
            'failed' => SmsLog::whereIn('status', ['FAILED', 'REJECTED'])->count(),
            'delivered' => SmsLog::where('status', 'DELIVERED')->count(),
        ];
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'logs' => $logs,
                'summary' => $summary
            ]);
        }
        
        return view('admin.sms.all-logs', compact('logs', 'companies', 'summary'));
    }

    /**
     * Get SMS statistics for dashboard charts (API)
     */
    public function stats(Request $request)
    {
        $period = $request->get('period', 'week');
        
        switch($period) {
            case 'week':
                $startDate = now()->startOfWeek();
                $endDate = now()->endOfWeek();
                break;
            case 'month':
                $startDate = now()->startOfMonth();
                $endDate = now()->endOfMonth();
                break;
            case 'year':
                $startDate = now()->startOfYear();
                $endDate = now()->endOfYear();
                break;
            default:
                $startDate = now()->subDays(7);
                $endDate = now();
        }
        
        $stats = SmsLog::whereBetween('sent_at', [$startDate, $endDate])
            ->selectRaw('DATE(sent_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
        
        // Get top 10 companies by SMS usage
        $topCompanies = SmsLog::selectRaw('company_id, COUNT(*) as total')
            ->where('sent_at', '>=', now()->subDays(30))
            ->with('company')
            ->groupBy('company_id')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();
        
        return response()->json([
            'success' => true,
            'stats' => $stats,
            'top_companies' => $topCompanies,
            'period' => $period
        ]);
    }

    /**
     * Show bulk SMS form
     */
    public function sendBulkForm()
    {
        $companies = Company::orderBy('company_name')->get(['id', 'company_name', 'phone']);
        
        return view('admin.sms.bulk-send', compact('companies'));
    }

    /**
     * Send bulk SMS to multiple companies
     */
    public function sendBulk(Request $request)
    {
        $request->validate([
            'company_ids' => 'required|array|min:1',
            'company_ids.*' => 'exists:companies,id',
            'message' => 'required|string|max:1600'
        ]);
        
        $companies = Company::whereIn('id', $request->company_ids)->get();
        $successCount = 0;
        $failCount = 0;
        $results = [];
        
        foreach ($companies as $company) {
if ($company->phone) {
    $reference = 'ADMIN_BULK_' . $company->id . '_' . time();
    
    // Format message: greeting + message + footer
    $message = "📢 UJUMBE KUTOKA KWA Msimamizi:\n\n" . $request->message . "\n\n---\nEndelea kufurahia huduma bora kupitia www.mauzosheetai.co.tz\nPowered by Blackscience Technology";
     
$result = $this->smsService->sendSms($company->phone, $message, $reference);
    
    if ($result['success']) {
        $successCount++;
    } else {
        $failCount++;
    }
    
    $results[] = [
        'company' => $company->company_name,
        'success' => $result['success'],
        'message' => $result['message']
    ];
} else {
                $failCount++;
                $results[] = [
                    'company' => $company->company_name,
                    'success' => false,
                    'message' => 'Kampuni haina namba ya simu'
                ];
            }
            
            // Small delay to avoid overwhelming the SMS API
            usleep(500000);
        }
        
        return response()->json([
            'success' => $successCount > 0,
            'message' => "Ujumbe umetumwa kwa kampuni {$successCount} kikamilifu, {$failCount} zimeshindwa",
            'details' => $results
        ]);
    }
}