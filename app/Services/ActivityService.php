<?php
// app/Services/ActivityService.php

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
            
            // Active companies (with at least one active user in last 10 minutes)
            $activeCompanies = Company::whereHas('users', function($query) {
                $query->where('last_activity_at', '>=', Carbon::now()->subMinutes($this->activeTimeWindow));
            })->count();
            
            $inactiveCompanies = $totalCompanies - $activeCompanies;
            
            // Active users from users table
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
            
            // Return default values on error
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
            return Company::whereHas('users', function($query) {
                $query->where('last_activity_at', '>=', Carbon::now()->subMinutes($this->activeTimeWindow));
            })->withCount(['users as active_users_count' => function($query) {
                $query->where('last_activity_at', '>=', Carbon::now()->subMinutes($this->activeTimeWindow));
            }])->get();
        } catch (\Exception $e) {
            Log::error('Error in getActiveCompanies: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get list of inactive companies
     */
    public function getInactiveCompanies()
    {
        try {
            return Company::whereDoesntHave('users', function($query) {
                $query->where('last_activity_at', '>=', Carbon::now()->subMinutes($this->activeTimeWindow));
            })->get();
        } catch (\Exception $e) {
            Log::error('Error in getInactiveCompanies: ' . $e->getMessage());
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
     * Get company activity statistics for all companies
     */
    public function getCompanyActivityStats()
    {
        try {
            $companies = Company::all();
            $stats = [];

            foreach ($companies as $company) {
                $stats[] = [
                    'id' => $company->id,
                    'name' => $company->company_name,
                    'owner' => $company->owner_name,
                    'is_active' => $this->isCompanyActive($company->id),
                    'active_users' => $this->getActiveUsersCount($company->id),
                    'total_users' => $this->getTotalUsersCount($company->id),
                    'today_logins' => $this->getTodayLoginsCount($company->id),
                    'total_logins' => $this->getTotalLoginsCount($company->id),
                    'last_login' => $this->getLastLoginDate($company->id),
                    'weekly_activity' => $this->getWeeklyActivity($company->id)
                ];
            }

            return $stats;
        } catch (\Exception $e) {
            Log::error('Error in getCompanyActivityStats: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Check if a company is active
     */
    private function isCompanyActive($companyId)
    {
        try {
            return User::where('company_id', $companyId)
                ->where('last_activity_at', '>=', Carbon::now()->subMinutes($this->activeTimeWindow))
                ->exists();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get active users count for a company
     */
    private function getActiveUsersCount($companyId)
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
     * Get total users count for a company
     */
    private function getTotalUsersCount($companyId)
    {
        try {
            return User::where('company_id', $companyId)->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get today's logins count for a company
     */
    private function getTodayLoginsCount($companyId)
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
    private function getTotalLoginsCount($companyId)
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
    private function getLastLoginDate($companyId)
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
    private function getWeeklyActivity($companyId)
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

    /**
     * Update employee activity timestamp
     */
    public function updateEmployeeActivity($employeeId)
    {
        try {
            if (class_exists('App\Models\Wafanyakazi')) {
                $employee = Wafanyakazi::find($employeeId);
                if ($employee && isset($employee->last_activity_at)) {
                    $employee->last_activity_at = Carbon::now();
                    $employee->save();
                    return true;
                }
            }
            return false;
        } catch (\Exception $e) {
            Log::error('Error updating employee activity: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Clean old login histories (for scheduled task)
     */
    public function cleanOldLoginHistories($days = 90)
    {
        try {
            $cutoffDate = Carbon::now()->subDays($days);
            $deleted = DB::table('login_histories')
                ->where('login_at', '<', $cutoffDate)
                ->delete();
            
            Log::info("Cleaned {$deleted} old login history records");
            return $deleted;
        } catch (\Exception $e) {
            Log::error('Error cleaning login histories: ' . $e->getMessage());
            return 0;
        }
    }
}