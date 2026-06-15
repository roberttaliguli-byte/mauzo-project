<?php

namespace App\Services;

use App\Models\Company;
use App\Models\User;
use App\Models\Wafanyakazi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ActivityService
{
    protected $activeTimeWindow = 10; // minutes

    /**
     * Get dashboard statistics
     */
    public function getDashboardStats()
    {
        try {
            // Total companies
            $totalCompanies = Company::count();
            
            // Get active company IDs from both users and employees
            $activeUserCompanyIds = User::where('last_activity_at', '>=', Carbon::now()->subMinutes($this->activeTimeWindow))
                ->whereNotNull('company_id')
                ->distinct('company_id')
                ->pluck('company_id')
                ->toArray();
                
            $activeEmployeeCompanyIds = [];
            try {
                if (class_exists('App\Models\Wafanyakazi')) {
                    $activeEmployeeCompanyIds = Wafanyakazi::where('last_activity_at', '>=', Carbon::now()->subMinutes($this->activeTimeWindow))
                        ->whereNotNull('company_id')
                        ->distinct('company_id')
                        ->pluck('company_id')
                        ->toArray();
                }
            } catch (\Exception $e) {
                Log::warning('Error counting active employee companies: ' . $e->getMessage());
            }
            
            $activeCompanyIds = array_unique(array_merge($activeUserCompanyIds, $activeEmployeeCompanyIds));
            $activeCompanies = count($activeCompanyIds);
            $inactiveCompanies = $totalCompanies - $activeCompanies;
            
            // Active users from users table (bosses)
            $totalActiveUsers = User::where('last_activity_at', '>=', Carbon::now()->subMinutes($this->activeTimeWindow))->count();
            
            // Active employees from wafanyakazi table
            $totalActiveEmployees = 0;
            try {
                if (class_exists('App\Models\Wafanyakazi')) {
                    $totalActiveEmployees = Wafanyakazi::where('last_activity_at', '>=', Carbon::now()->subMinutes($this->activeTimeWindow))->count();
                }
            } catch (\Exception $e) {
                Log::warning('Error counting active employees: ' . $e->getMessage());
            }
            
            // Today's date in East African Time
            $today = Carbon::now('Africa/Nairobi')->startOfDay();
            
            // Today's logins from login_histories
            $todayLogins = 0;
            $todayUniqueUsers = 0;
            
            if (DB::getSchemaBuilder()->hasTable('login_histories')) {
                $todayLogins = DB::table('login_histories')
                    ->whereDate('login_at', $today)
                    ->count();
                    
                $todayUniqueUsers = DB::table('login_histories')
                    ->whereDate('login_at', $today)
                    ->distinct('user_id')
                    ->count('user_id');
            }

            // Calculate active percentage
            $activePercentage = $totalCompanies > 0 ? round(($activeCompanies / $totalCompanies) * 100, 2) : 0;

            return [
                'active_companies' => $activeCompanies,
                'inactive_companies' => $inactiveCompanies,
                'total_companies' => $totalCompanies,
                'active_users_now' => $totalActiveUsers + $totalActiveEmployees,
                'today_logins' => $todayLogins,
                'today_unique_users' => $todayUniqueUsers,
                'active_percentage' => $activePercentage
            ];

        } catch (\Exception $e) {
            Log::error('Error in getDashboardStats: ' . $e->getMessage());
            
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
    }

    /**
     * Get list of active companies
     */
    public function getActiveCompanies()
    {
        try {
            $activeUserCompanyIds = User::where('last_activity_at', '>=', Carbon::now()->subMinutes($this->activeTimeWindow))
                ->whereNotNull('company_id')
                ->distinct('company_id')
                ->pluck('company_id')
                ->toArray();
                
            $activeEmployeeCompanyIds = [];
            try {
                if (class_exists('App\Models\Wafanyakazi')) {
                    $activeEmployeeCompanyIds = Wafanyakazi::where('last_activity_at', '>=', Carbon::now()->subMinutes($this->activeTimeWindow))
                        ->whereNotNull('company_id')
                        ->distinct('company_id')
                        ->pluck('company_id')
                        ->toArray();
                }
            } catch (\Exception $e) {
                Log::warning('Error getting active employee companies: ' . $e->getMessage());
            }
            
            $activeCompanyIds = array_unique(array_merge($activeUserCompanyIds, $activeEmployeeCompanyIds));
            
            return Company::whereIn('id', $activeCompanyIds)->get();
        } catch (\Exception $e) {
            Log::error('Error in getActiveCompanies: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get companies that logged in today
     */
    public function getTodayActiveCompanies()
    {
        try {
            $today = Carbon::now('Africa/Nairobi')->startOfDay();
            
            return Company::whereHas('loginHistories', function($query) use ($today) {
                $query->whereDate('login_at', $today);
            })->withCount(['loginHistories as today_login_count' => function($query) use ($today) {
                $query->whereDate('login_at', $today);
            }])->get();
        } catch (\Exception $e) {
            Log::error('Error in getTodayActiveCompanies: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Check if a company is active
     */
    public function isCompanyActive($companyId)
    {
        try {
            $hasActiveUser = User::where('company_id', $companyId)
                ->where('last_activity_at', '>=', Carbon::now()->subMinutes($this->activeTimeWindow))
                ->exists();
                
            $hasActiveEmployee = false;
            try {
                if (class_exists('App\Models\Wafanyakazi')) {
                    $hasActiveEmployee = Wafanyakazi::where('company_id', $companyId)
                        ->where('last_activity_at', '>=', Carbon::now()->subMinutes($this->activeTimeWindow))
                        ->exists();
                }
            } catch (\Exception $e) {
                Log::warning('Error checking active employee: ' . $e->getMessage());
            }
            
            return $hasActiveUser || $hasActiveEmployee;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get active users count for a company (bosses only)
     */
    public function getActiveUsersCount($companyId)
    {
        try {
            return User::where('company_id', $companyId)
                ->where('last_activity_at', '>=', Carbon::now()->subMinutes($this->activeTimeWindow))
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    /**
     * Get active employees count for a company
     */
    public function getActiveEmployeesCount($companyId)
    {
        try {
            if (class_exists('App\Models\Wafanyakazi')) {
                return Wafanyakazi::where('company_id', $companyId)
                    ->where('last_activity_at', '>=', Carbon::now()->subMinutes($this->activeTimeWindow))
                    ->count();
            }
            return 0;
        } catch (\Exception $e) {
            Log::warning('Error getting active employees count: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get today's logins count for a company
     */
    public function getTodayLoginsCount($companyId)
    {
        try {
            $today = Carbon::now('Africa/Nairobi')->startOfDay();
            
            return DB::table('login_histories')
                ->where('company_id', $companyId)
                ->whereDate('login_at', $today)
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get total logins count for a company
     */
    public function getTotalLoginsCount($companyId)
    {
        try {
            return DB::table('login_histories')
                ->where('company_id', $companyId)
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get last login date for a company
     */
    public function getLastLoginDate($companyId)
    {
        try {
            return DB::table('login_histories')
                ->where('company_id', $companyId)
                ->max('login_at');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get weekly activity for a company
     */
    public function getWeeklyActivity($companyId)
    {
        try {
            $data = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $count = DB::table('login_histories')
                    ->where('company_id', $companyId)
                    ->whereDate('login_at', $date)
                    ->distinct('user_id')
                    ->count('user_id');
                
                $data[] = [
                    'date' => $date->format('Y-m-d'),
                    'day' => $date->format('D'),
                    'active_users' => $count
                ];
            }
            return $data;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Update user activity timestamp
     */
    public function updateUserActivity($userId)
    {
        try {
            $user = User::find($userId);
            if ($user) {
                $user->last_activity_at = Carbon::now();
                $user->save();
                return true;
            }
            return false;
        } catch (\Exception $e) {
            Log::error('Error updating user activity: ' . $e->getMessage());
            return false;
        }
    }
}