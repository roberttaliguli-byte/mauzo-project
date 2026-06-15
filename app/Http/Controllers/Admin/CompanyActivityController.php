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
            $stats = $this->getDashboardStats();
            
            // Get today's active companies
            $todayActiveCompanies = $this->getTodayActiveCompanies();
            
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
                    $companiesQuery->whereRaw('1 = 0');
                }
            } elseif ($status === 'inactive') {
                $activeCompanyIds = $this->getActiveCompanyIds();
                if (!empty($activeCompanyIds)) {
                    $companiesQuery->whereNotIn('id', $activeCompanyIds);
                }
            }
            
            // Get paginated results
            $companies = $companiesQuery->orderBy('created_at', 'desc')
                ->paginate(15)
                ->withQueryString();
            
            // Enrich companies with activity data
            foreach ($companies as $company) {
                $company->is_active = $this->isCompanyActive($company->id);
                $company->active_users_count = $this->getActiveUsersCount($company->id);
                $company->active_employees_count = $this->getActiveEmployeesCount($company->id);
                $company->today_login_count = $this->getTodayLoginsCount($company->id);
                $company->total_login_count = $this->getTotalLoginsCount($company->id);
                $company->last_login_date = $this->getLastLoginDate($company->id);
                $company->total_users = User::where('company_id', $company->id)->count();
                $company->total_employees = Wafanyakazi::where('company_id', $company->id)->count();
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
            
            return view('admin.company-activity', [
                'companies' => collect([]),
                'stats' => $this->getEmptyStats(),
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

            // Get employees
            $employees = Wafanyakazi::where('company_id', $company->id)
                ->select('id', 'jina as name', 'username', 'last_activity_at', 'last_login_at', 'login_count', 'role')
                ->get();

            // Get weekly activity
            $weeklyActivity = $this->getWeeklyActivity($company->id);
            
            // Count currently active users and employees
            $activeUsersCount = $this->getActiveUsersCount($company->id);
            $activeEmployeesCount = $this->getActiveEmployeesCount($company->id);

            // Get login statistics
            $totalLogins = $this->getTotalLoginsCount($company->id);
            $lastLogin = $this->getLastLoginDate($company->id);
            
            // Get daily active users count
            $today = Carbon::now('Africa/Nairobi')->startOfDay();
            $dailyActiveUsersUnique = DB::table('login_histories')
                ->where('company_id', $company->id)
                ->whereDate('login_at', $today)
                ->count();

            // Prepare company data
            $companyData = [
                'id' => $company->id,
                'name' => $company->company_name,
                'owner_name' => $company->owner_name,
                'email' => $company->email,
                'phone' => $company->phone,
                'is_active' => ($activeUsersCount + $activeEmployeesCount) > 0,
                'active_users' => $activeUsersCount,
                'active_employees' => $activeEmployeesCount,
                'total_users' => $users->count(),
                'total_employees' => $employees->count(),
                'total_logins' => $totalLogins,
                'last_login' => $lastLogin,
                'daily_active_users' => $dailyActiveUsersUnique,
                'weekly_activity' => $weeklyActivity,
                'package' => $company->package ?? 'Free Trial',
                'package_end' => $company->package_end ? Carbon::parse($company->package_end)->format('d/m/Y') : 'N/A'
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
                    'type' => 'Boss'
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
                    'role' => $employee->role ?? 'employee',
                    'last_activity' => $lastActivity ? $lastActivity->toDateTimeString() : null,
                    'is_active' => $lastActivity && $lastActivity->gt(Carbon::now()->subMinutes(10)),
                    'last_login' => $employee->last_login_at,
                    'login_count' => $employee->login_count ?? 0,
                    'type' => 'Mfanyakazi'
                ]);
            }

            $allUsers = $allUsers->sortByDesc('last_activity');

            return response()->json([
                'success' => true,
                'company' => $companyData,
                'users' => $allUsers->values()
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching company details: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading company details'
            ], 500);
        }
    }

    // Helper Methods
    private function getDashboardStats()
    {
        $activeCompanyIds = $this->getActiveCompanyIds();
        $totalCompanies = Company::count();
        $activeCompanies = count($activeCompanyIds);
        $inactiveCompanies = $totalCompanies - $activeCompanies;
        
        // Count active users
        $activeUsersNow = User::where('last_activity_at', '>=', Carbon::now()->subMinutes(10))->count();
        $activeEmployeesNow = Wafanyakazi::where('last_activity_at', '>=', Carbon::now()->subMinutes(10))->count();
        
        // Today's logins
        $today = Carbon::now('Africa/Nairobi')->startOfDay();
        $todayLogins = DB::table('login_histories')
            ->whereDate('login_at', $today)
            ->count();
        
        $todayUniqueUsers = DB::table('login_histories')
            ->whereDate('login_at', $today)
            ->distinct('user_id')
            ->count('user_id');
        
        return [
            'total_companies' => $totalCompanies,
            'active_companies' => $activeCompanies,
            'inactive_companies' => $inactiveCompanies,
            'active_users_now' => $activeUsersNow + $activeEmployeesNow,
            'today_logins' => $todayLogins,
            'today_unique_users' => $todayUniqueUsers,
            'active_percentage' => $totalCompanies > 0 ? round(($activeCompanies / $totalCompanies) * 100) : 0
        ];
    }

    private function getEmptyStats()
    {
        return [
            'active_companies' => 0,
            'inactive_companies' => 0,
            'total_companies' => 0,
            'active_users_now' => 0,
            'today_logins' => 0,
            'today_unique_users' => 0,
            'active_percentage' => 0
        ];
    }

    private function getActiveCompanyIds()
    {
        $activeUserCompanyIds = User::where('last_activity_at', '>=', Carbon::now()->subMinutes(10))
            ->whereNotNull('company_id')
            ->distinct('company_id')
            ->pluck('company_id')
            ->toArray();
            
        $activeEmployeeCompanyIds = Wafanyakazi::where('last_activity_at', '>=', Carbon::now()->subMinutes(10))
            ->whereNotNull('company_id')
            ->distinct('company_id')
            ->pluck('company_id')
            ->toArray();
        
        return array_unique(array_merge($activeUserCompanyIds, $activeEmployeeCompanyIds));
    }

    private function isCompanyActive($companyId)
    {
        $hasActiveUser = User::where('company_id', $companyId)
            ->where('last_activity_at', '>=', Carbon::now()->subMinutes(10))
            ->exists();
            
        $hasActiveEmployee = Wafanyakazi::where('company_id', $companyId)
            ->where('last_activity_at', '>=', Carbon::now()->subMinutes(10))
            ->exists();
        
        return $hasActiveUser || $hasActiveEmployee;
    }

    private function getActiveUsersCount($companyId)
    {
        return User::where('company_id', $companyId)
            ->where('last_activity_at', '>=', Carbon::now()->subMinutes(10))
            ->count();
    }

    private function getActiveEmployeesCount($companyId)
    {
        return Wafanyakazi::where('company_id', $companyId)
            ->where('last_activity_at', '>=', Carbon::now()->subMinutes(10))
            ->count();
    }

    private function getTodayLoginsCount($companyId)
    {
        $today = Carbon::now('Africa/Nairobi')->startOfDay();
        
        return DB::table('login_histories')
            ->where('company_id', $companyId)
            ->whereDate('login_at', $today)
            ->count();
    }

    private function getTotalLoginsCount($companyId)
    {
        return DB::table('login_histories')
            ->where('company_id', $companyId)
            ->count();
    }

    private function getLastLoginDate($companyId)
    {
        $lastLogin = DB::table('login_histories')
            ->where('company_id', $companyId)
            ->orderBy('login_at', 'desc')
            ->first();
        
        return $lastLogin ? Carbon::parse($lastLogin->login_at)->setTimezone('Africa/Nairobi') : null;
    }

    private function getTodayActiveCompanies()
    {
        $today = Carbon::now('Africa/Nairobi')->startOfDay();
        
        $companyIds = DB::table('login_histories')
            ->whereDate('login_at', $today)
            ->distinct('company_id')
            ->pluck('company_id');
        
        $companies = Company::whereIn('id', $companyIds)->get();
        
        foreach ($companies as $company) {
            $company->today_login_count = DB::table('login_histories')
                ->where('company_id', $company->id)
                ->whereDate('login_at', $today)
                ->count();
            $company->is_active = $this->isCompanyActive($company->id);
            $company->last_login_date = $this->getLastLoginDate($company->id);
        }
        
        return $companies->sortByDesc('today_login_count');
    }

    private function getWeeklyActivity($companyId)
    {
        $weeklyData = [];
        $now = Carbon::now('Africa/Nairobi');
        
        for ($i = 6; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i);
            $dayName = $date->format('D');
            
            $loginCount = DB::table('login_histories')
                ->where('company_id', $companyId)
                ->whereDate('login_at', $date)
                ->distinct('user_id')
                ->count('user_id');
            
            $employeeCount = Wafanyakazi::where('company_id', $companyId)
                ->whereDate('last_activity_at', $date)
                ->count();
            
            $weeklyData[] = [
                'day' => $dayName,
                'active_users' => $loginCount + $employeeCount,
                'date' => $date->format('Y-m-d')
            ];
        }
        
        return $weeklyData;
    }
}