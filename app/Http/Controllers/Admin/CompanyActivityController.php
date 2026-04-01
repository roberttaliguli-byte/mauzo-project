<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use App\Models\Wafanyakazi;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CompanyActivityController extends Controller
{
    protected $activityService;

    public function __construct(ActivityService $activityService)
    {
        $this->activityService = $activityService;
    }

    /**
     * Display company activity report
     */
    public function index(Request $request)
    {
        try {
            $search = $request->input('search');
            $status = $request->input('status');
            
            // Get summary statistics
            $stats = $this->activityService->getDashboardStats();
            
            // Get today's active companies
            $todayActiveCompanies = $this->activityService->getTodayActiveCompanies();
            
            // Build the query with filters
            $companiesQuery = Company::query();
            
            // Apply search filter if provided
            if ($search) {
                $companiesQuery->where(function($q) use ($search) {
                    $q->where('company_name', 'LIKE', "%{$search}%")
                      ->orWhere('owner_name', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%")
                      ->orWhere('phone', 'LIKE', "%{$search}%");
                });
            }
            
            // Apply status filter
            if ($status === 'active') {
                $activeCompanyIds = $this->getActiveCompanyIds();
                if (!empty($activeCompanyIds)) {
                    $companiesQuery->whereIn('id', $activeCompanyIds);
                } else {
                    $companiesQuery->whereRaw('1 = 0'); // No results
                }
            } elseif ($status === 'inactive') {
                $activeCompanyIds = $this->getActiveCompanyIds();
                if (!empty($activeCompanyIds)) {
                    $companiesQuery->whereNotIn('id', $activeCompanyIds);
                }
            }
            
            // Get paginated results
            $companies = $companiesQuery->orderBy('created_at', 'desc')
                ->paginate(10)
                ->withQueryString();
            
            // Enrich companies with activity data
            foreach ($companies as $company) {
                $company->is_active = $this->activityService->isCompanyActive($company->id);
                $company->active_users_count = $this->activityService->getActiveUsersCount($company->id);
                $company->today_login_count = $this->activityService->getTodayLoginsCount($company->id);
                $company->total_login_count = $this->activityService->getTotalLoginsCount($company->id);
                $company->last_login_date = $this->activityService->getLastLoginDate($company->id);
                
                // Load users relationship
                $company->load(['users' => function($q) {
                    $q->select('id', 'company_id', 'name', 'username', 'last_activity_at', 'last_login_at', 'login_count', 'role');
                }]);
            }

            return view('admin.company-activity', compact(
                'companies', 
                'stats', 
                'search', 
                'status',
                'todayActiveCompanies'
            ));

        } catch (\Exception $e) {
            Log::error('Error in company activity index: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return view('admin.company-activity', [
                'companies' => collect([]),
                'stats' => [
                    'active_companies' => 0,
                    'inactive_companies' => 0,
                    'total_companies' => 0,
                    'active_users_now' => 0,
                    'today_logins' => 0,
                    'today_unique_users' => 0,
                    'active_percentage' => 0
                ],
                'search' => $search ?? null,
                'status' => $status ?? null,
                'todayActiveCompanies' => collect([]),
                'error' => 'Hitilafu katika kupakia data'
            ]);
        }
    }

    /**
     * Get company details for the modal
     */
    public function getCompanyDetails($id)
    {
        try {
            Log::info('Fetching company details for ID: ' . $id);
            
            // Find the company
            $company = Company::find($id);
            
            if (!$company) {
                return response()->json([
                    'success' => false,
                    'message' => 'Company not found'
                ], 404);
            }

            // Get users for this company
            $users = User::where('company_id', $company->id)
                ->select('id', 'name', 'username', 'email', 'last_activity_at', 'last_login_at', 'login_count', 'role')
                ->get();

            // Get employees if table exists
            $employees = collect();
            if (class_exists('App\Models\Wafanyakazi')) {
                try {
                    $employees = Wafanyakazi::where('company_id', $company->id)
                        ->select('id', 'jina as name', 'username', 'last_activity_at', 'last_login_at', 'login_count')
                        ->get();
                } catch (\Exception $e) {
                    Log::warning('Error fetching employees: ' . $e->getMessage());
                }
            }

            // Get weekly activity
            $weeklyActivity = $this->activityService->getWeeklyActivity($company->id);
            
            // Count currently active users
            $activeUsersCount = $this->activityService->getActiveUsersCount($company->id);
            
            $activeEmployeesCount = 0;
            if ($employees->isNotEmpty()) {
                $activeEmployeesCount = $employees->filter(function($emp) {
                    return $emp->last_activity_at && 
                           Carbon::parse($emp->last_activity_at)->gt(Carbon::now()->subMinutes(10));
                })->count();
            }

            // Get login statistics
            $totalLogins = $this->activityService->getTotalLoginsCount($company->id);
            $lastLogin = $this->activityService->getLastLoginDate($company->id);
            $dailyActiveUsers = $this->activityService->getTodayLoginsCount($company->id);

            // Get daily active users count (unique users who logged in today)
            $today = Carbon::now('Africa/Nairobi')->startOfDay();
            $dailyActiveUsersUnique = 0;
            try {
                $dailyActiveUsersUnique = DB::table('login_histories')
                    ->where('company_id', $company->id)
                    ->whereDate('login_at', $today)
                    ->distinct('user_id')
                    ->count('user_id');
            } catch (\Exception $e) {
                Log::warning('Error counting daily active users: ' . $e->getMessage());
            }

            // Prepare company data
            $companyData = [
                'id' => $company->id,
                'name' => $company->company_name,
                'is_active' => ($activeUsersCount + $activeEmployeesCount) > 0,
                'active_users' => $activeUsersCount + $activeEmployeesCount,
                'total_users' => $users->count() + $employees->count(),
                'total_logins' => $totalLogins,
                'last_login' => $lastLogin,
                'daily_active_users' => $dailyActiveUsersUnique,
                'weekly_activity' => $weeklyActivity
            ];

            // Combine users and employees for display
            $allUsers = collect();
            
            foreach ($users as $user) {
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
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading company details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper method to get active company IDs
     */
    private function getActiveCompanyIds()
    {
        try {
            return User::where('last_activity_at', '>=', Carbon::now()->subMinutes(10))
                ->distinct('company_id')
                ->pluck('company_id')
                ->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }
}