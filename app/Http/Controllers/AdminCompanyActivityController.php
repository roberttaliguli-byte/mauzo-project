<?php
// app/Http/Controllers/AdminCompanyActivityController.php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use App\Models\Wafanyakazi;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AdminCompanyActivityController extends Controller
{
    protected $activityService;

    public function __construct(ActivityService $activityService)
    {
        $this->activityService = $activityService;
    }

    /**
     * Display the company activity dashboard
     */
    public function index()
    {
        try {
            // Get companies with users and employee count
            $companies = Company::with(['users' => function($q) {
                $q->select('id', 'company_id', 'name', 'username', 'last_activity_at', 'last_login_at', 'login_count', 'role');
            }])->withCount('wafanyakazi')->paginate(10);

            // Get today's date in East African Time
            $today = Carbon::now('Africa/Nairobi')->startOfDay();
            $todayStr = $today->format('Y-m-d');

            // Add today's login information to each company
            foreach ($companies as $company) {
                // Count today's logins for this company from login_histories
                $company->today_login_count = DB::table('login_histories')
                    ->where('company_id', $company->id)
                    ->whereDate('login_at', $today)
                    ->count();

                // Check if company has any login today
                $company->has_today_login = $company->today_login_count > 0;

                // Get unique users who logged in today
                $company->today_unique_users = DB::table('login_histories')
                    ->where('company_id', $company->id)
                    ->whereDate('login_at', $today)
                    ->distinct('user_id')
                    ->count('user_id');

                // Get last login date
                $company->last_login_date = DB::table('login_histories')
                    ->where('company_id', $company->id)
                    ->max('login_at');

                // Check if company is currently active (any user active in last 10 minutes)
                $company->is_active = $company->users()
                    ->where('last_activity_at', '>=', Carbon::now()->subMinutes(10))
                    ->exists();

                // Get active users count
                $company->get_active_users_count = $company->users()
                    ->where('last_activity_at', '>=', Carbon::now()->subMinutes(10))
                    ->count();

                // Get total login count
                $company->get_total_login_count = DB::table('login_histories')
                    ->where('company_id', $company->id)
                    ->count();
            }

            // Get dashboard statistics
            $stats = $this->activityService->getDashboardStats();

            return view('admin.company-activity', compact('companies', 'stats'));

        } catch (\Exception $e) {
            Log::error('Error in company activity index: ' . $e->getMessage());
            
            // Return empty data on error
            $companies = collect([]);
            $stats = [
                'active_companies' => 0,
                'inactive_companies' => 0,
                'total_companies' => 0,
                'active_users_now' => 0,
                'today_logins' => 0,
                'today_unique_users' => 0,
                'active_percentage' => 0
            ];
            
            return view('admin.company-activity', compact('companies', 'stats'))
                ->with('error', 'Hitilafu katika kupakia data. Tafadhali jaribu tena.');
        }
    }

    /**
     * Get company details for the modal
     */
    public function getCompanyDetails($id)
    {
        try {
            Log::info('Fetching company details for ID: ' . $id);
            
            // Find the company with users
            $company = Company::with(['users' => function($q) {
                $q->select('id', 'company_id', 'name', 'username', 'last_activity_at', 'last_login_at', 'login_count', 'role', 'email');
            }])->find($id);
            
            if (!$company) {
                Log::error('Company not found with ID: ' . $id);
                return response()->json([
                    'success' => false,
                    'message' => 'Company not found'
                ], 404);
            }
            
            Log::info('Company found: ' . $company->company_name);

            // Get employees for this company
            try {
                $employees = collect(); // Default empty collection
                if (class_exists('App\Models\Wafanyakazi')) {
                    $employees = Wafanyakazi::where('company_id', $company->id)
                        ->select('id', 'jina as name', 'username', 'last_activity_at', 'last_login_at', 'login_count')
                        ->get();
                    Log::info('Employees found: ' . $employees->count());
                }
            } catch (\Exception $e) {
                Log::error('Error fetching employees: ' . $e->getMessage());
                $employees = collect();
            }

            // Get today's date
            $today = Carbon::now('Africa/Nairobi')->startOfDay();

            // Calculate weekly activity from login_histories
            $weeklyActivity = [];
            try {
                for ($i = 6; $i >= 0; $i--) {
                    $date = Carbon::now()->subDays($i);
                    
                    // Count active users on this day
                    $activeUsers = DB::table('login_histories')
                        ->where('company_id', $company->id)
                        ->whereDate('login_at', $date)
                        ->distinct('user_id')
                        ->count('user_id');
                    
                    $weeklyActivity[] = [
                        'date' => $date->format('Y-m-d'),
                        'day' => $date->format('D'),
                        'active_users' => $activeUsers
                    ];
                }
                Log::info('Weekly activity calculated');
            } catch (\Exception $e) {
                Log::error('Error calculating weekly activity: ' . $e->getMessage());
                $weeklyActivity = [];
            }

            // Count currently active users (last 10 minutes)
            $activeUsersCount = $company->users()
                ->where('last_activity_at', '>=', Carbon::now()->subMinutes(10))
                ->count();
                
            $activeEmployeesCount = 0;
            try {
                if ($employees->isNotEmpty()) {
                    $activeEmployeesCount = $employees->filter(function($emp) {
                        return $emp->last_activity_at && 
                               Carbon::parse($emp->last_activity_at)->gt(Carbon::now()->subMinutes(10));
                    })->count();
                }
            } catch (\Exception $e) {
                Log::error('Error counting active employees: ' . $e->getMessage());
            }

            // Get login statistics
            $totalLogins = DB::table('login_histories')
                ->where('company_id', $company->id)
                ->count();

            $lastLogin = DB::table('login_histories')
                ->where('company_id', $company->id)
                ->max('login_at');

            $dailyActiveUsers = DB::table('login_histories')
                ->where('company_id', $company->id)
                ->whereDate('login_at', $today)
                ->distinct('user_id')
                ->count('user_id');

            $todayLogins = DB::table('login_histories')
                ->where('company_id', $company->id)
                ->whereDate('login_at', $today)
                ->count();

            // Prepare company data
            $companyData = [
                'id' => $company->id,
                'name' => $company->company_name,
                'is_active' => ($activeUsersCount + $activeEmployeesCount) > 0,
                'active_users' => $activeUsersCount + $activeEmployeesCount,
                'total_users' => $company->users->count() + $employees->count(),
                'total_logins' => $totalLogins,
                'last_login' => $lastLogin,
                'daily_active_users' => $dailyActiveUsers,
                'today_logins' => $todayLogins,
                'weekly_activity' => $weeklyActivity
            ];

            // Combine users and employees for display
            $allUsers = collect();
            
            foreach ($company->users as $user) {
                $lastActivity = $user->last_activity_at ? 
                    Carbon::parse($user->last_activity_at)->setTimezone('Africa/Nairobi') : null;
                
                $allUsers->push([
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'role' => $user->role ?? 'boss',
                    'last_activity' => $lastActivity ? $lastActivity->toDateTimeString() : null,
                    'is_active' => $lastActivity && $lastActivity->gt(Carbon::now()->subMinutes(10)),
                    'last_login' => $user->last_login_at,
                    'login_count' => $user->login_count ?? 0,
                    'type' => 'user'
                ]);
            }
            
            foreach ($employees as $employee) {
                $lastActivity = $employee->last_activity_at ? 
                    Carbon::parse($employee->last_activity_at)->setTimezone('Africa/Nairobi') : null;
                
                $allUsers->push([
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'username' => $employee->username,
                    'email' => null,
                    'role' => 'employee',
                    'last_activity' => $lastActivity ? $lastActivity->toDateTimeString() : null,
                    'is_active' => $lastActivity && $lastActivity->gt(Carbon::now()->subMinutes(10)),
                    'last_login' => $employee->last_login_at,
                    'login_count' => $employee->login_count ?? 0,
                    'type' => 'employee'
                ]);
            }

            return response()->json([
                'success' => true,
                'company' => $companyData,
                'users' => $allUsers
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching company details: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading company details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get activity statistics for API
     */
    public function getActivityStats()
    {
        try {
            $stats = $this->activityService->getDashboardStats();
            return response()->json($stats);
        } catch (\Exception $e) {
            Log::error('Error getting activity stats: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get chart data for visualization
     */
    public function getChartData()
    {
        try {
            $companies = Company::all();
            $chartData = [];

            foreach ($companies as $company) {
                $chartData[] = [
                    'name' => $company->company_name,
                    'active_users' => $company->users()
                        ->where('last_activity_at', '>=', Carbon::now()->subMinutes(10))
                        ->count(),
                    'weekly_data' => $this->getWeeklyActivity($company->id)
                ];
            }

            return response()->json($chartData);
        } catch (\Exception $e) {
            Log::error('Error getting chart data: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get weekly activity for a company
     */
    private function getWeeklyActivity($companyId)
    {
        try {
            $data = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $count = 0;
                
                if (DB::getSchemaBuilder()->hasTable('login_histories')) {
                    $count = DB::table('login_histories')
                        ->where('company_id', $companyId)
                        ->whereDate('login_at', $date)
                        ->distinct('user_id')
                        ->count('user_id');
                }
                
                $data[] = [
                    'date' => $date->format('Y-m-d'),
                    'day' => $date->format('D'),
                    'active_users' => $count
                ];
            }
            return $data;
        } catch (\Exception $e) {
            Log::error('Error in getWeeklyActivity: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Export report as PDF (placeholder for future implementation)
     */
    public function exportPDF(Request $request)
    {
        // This would be implemented with a PDF library like DomPDF
        return redirect()->back()->with('info', 'PDF export functionality coming soon');
    }

    /**
     * Export report as Excel (placeholder for future implementation)
     */
    public function exportExcel(Request $request)
    {
        // This would be implemented with Laravel Excel
        return redirect()->back()->with('info', 'Excel export functionality coming soon');
    }
}