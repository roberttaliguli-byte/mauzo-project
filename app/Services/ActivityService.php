<?php
// app/Services/ActivityService.php

namespace App\Services;

use App\Models\Company;
use App\Models\User;
use App\Models\Wafanyakazi;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ActivityService
{
    protected $activeTimeWindow = 10; // minutes

    public function getActiveCompanies()
    {
        return Company::where(function($query) {
            $query->whereHas('users', function($q) {
                $q->where('last_activity_at', '>=', now()->subMinutes($this->activeTimeWindow));
            })->orWhereHas('wafanyakazi', function($q) {
                $q->where('last_activity_at', '>=', now()->subMinutes($this->activeTimeWindow));
            });
        })->get();
    }

    public function getInactiveCompanies()
    {
        return Company::whereDoesntHave('users', function($q) {
            $q->where('last_activity_at', '>=', now()->subMinutes($this->activeTimeWindow));
        })->whereDoesntHave('wafanyakazi', function($q) {
            $q->where('last_activity_at', '>=', now()->subMinutes($this->activeTimeWindow));
        })->get();
    }

    public function getDashboardStats()
    {
        $totalCompanies = Company::count();
        
        $activeCompanies = Company::where(function($query) {
            $query->whereHas('users', function($q) {
                $q->where('last_activity_at', '>=', now()->subMinutes($this->activeTimeWindow));
            })->orWhereHas('wafanyakazi', function($q) {
                $q->where('last_activity_at', '>=', now()->subMinutes($this->activeTimeWindow));
            });
        })->count();
        
        $inactiveCompanies = $totalCompanies - $activeCompanies;
        
        // Count active users (from users table)
        $totalActiveUsers = User::where('last_activity_at', '>=', now()->subMinutes($this->activeTimeWindow))->count();
        
        // Count active employees (from wafanyakazi table)
        $totalActiveEmployees = Wafanyakazi::where('last_activity_at', '>=', now()->subMinutes($this->activeTimeWindow))->count();
        
        $todayLogins = DB::table('login_histories')
            ->whereDate('login_at', today())
            ->count();

        return [
            'active_companies' => $activeCompanies,
            'inactive_companies' => $inactiveCompanies,
            'total_companies' => $totalCompanies,
            'active_users_now' => $totalActiveUsers + $totalActiveEmployees,
            'today_logins' => $todayLogins,
            'active_percentage' => $totalCompanies > 0 ? round(($activeCompanies / $totalCompanies) * 100, 2) : 0
        ];
    }
}