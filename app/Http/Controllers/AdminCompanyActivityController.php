<?php
// app/Http/Controllers/AdminCompanyActivityController.php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use App\Models\Wafanyakazi;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminCompanyActivityController extends Controller
{
    protected $activityService;

    public function __construct(ActivityService $activityService)
    {
        $this->activityService = $activityService;
    }

    public function index()
    {
        $companies = Company::with(['users' => function($q) {
            $q->select('id', 'company_id', 'name', 'username', 'last_activity_at', 'last_login_at', 'login_count');
        }])->withCount('wafanyakazi')->paginate(10);

        $stats = $this->activityService->getDashboardStats();

        return view('admin.company-activity', compact('companies', 'stats'));
    }

    public function getCompanyDetails($id)
    {
        try {
            $company = Company::with(['users' => function($q) {
                $q->select('id', 'company_id', 'name', 'username', 'last_activity_at', 'last_login_at', 'login_count');
            }])->findOrFail($id);

            // Get employees for this company
            $employees = Wafanyakazi::where('company_id', $company->id)
                ->select('id', 'jina as name', 'username', 'last_activity_at', 'last_login_at', 'login_count')
                ->get();

            // Calculate weekly activity from both users and employees
            $weeklyActivity = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                
                // Count active users on this day
                $activeUsers = DB::table('login_histories')
                    ->where('company_id', $company->id)
                    ->whereDate('login_at', $date)
                    ->distinct('user_id')
                    ->count('user_id');
                
                // For employees, we need to check their last_activity_at
                $activeEmployees = Wafanyakazi::where('company_id', $company->id)
                    ->whereDate('last_activity_at', $date)
                    ->count();
                
                $weeklyActivity[] = [
                    'date' => $date->format('Y-m-d'),
                    'day' => $date->format('D'),
                    'active_users' => $activeUsers + $activeEmployees
                ];
            }

            // Count currently active users (last 10 minutes)
            $activeUsersCount = $company->users()
                ->where('last_activity_at', '>=', now()->subMinutes(10))
                ->count();
                
            $activeEmployeesCount = Wafanyakazi::where('company_id', $company->id)
                ->where('last_activity_at', '>=', now()->subMinutes(10))
                ->count();

            $totalLogins = DB::table('login_histories')
                ->where('company_id', $company->id)
                ->count();

            $lastLogin = DB::table('login_histories')
                ->where('company_id', $company->id)
                ->max('login_at');

            $dailyActiveUsers = DB::table('login_histories')
                ->where('company_id', $company->id)
                ->whereDate('login_at', today())
                ->distinct('user_id')
                ->count('user_id');

            $companyData = [
                'id' => $company->id,
                'name' => $company->company_name,
                'is_active' => ($activeUsersCount + $activeEmployeesCount) > 0,
                'active_users' => $activeUsersCount + $activeEmployeesCount,
                'total_users' => $company->users->count() + $employees->count(),
                'total_logins' => $totalLogins,
                'last_login' => $lastLogin,
                'daily_active_users' => $dailyActiveUsers,
                'weekly_activity' => $weeklyActivity
            ];

            // Combine users and employees for display
            $allUsers = collect();
            
            foreach ($company->users as $user) {
                $allUsers->push([
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'last_activity' => $user->last_activity_at,
                    'is_active' => $user->last_activity_at && $user->last_activity_at->gt(now()->subMinutes(10)),
                    'last_login' => $user->last_login_at,
                    'login_count' => $user->login_count ?? 0,
                    'type' => 'boss'
                ]);
            }
            
            foreach ($employees as $employee) {
                $lastActivity = $employee->last_activity_at ? 
                    \Carbon\Carbon::parse($employee->last_activity_at) : null;
                
                $allUsers->push([
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'username' => $employee->username,
                    'last_activity' => $employee->last_activity_at,
                    'is_active' => $lastActivity && $lastActivity->gt(now()->subMinutes(10)),
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
            \Log::error('Error fetching company details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading company details',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}