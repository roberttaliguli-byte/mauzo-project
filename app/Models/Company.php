<?php   
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
    'company_name', 'owner_name', 'owner_gender', 'owner_dob',
    'location', 'region', 'phone', 'email',
    'is_verified', 'package', 'database_name'
];


    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function user()
{
    return $this->hasOne(\App\Models\User::class, 'company_id');
}


    // Relationships to data
public function bidhaa()
{
    return $this->hasMany(\App\Models\Bidhaa::class);
}

    public function wateja()
    {
        return $this->hasMany(Mteja::class);
    }


public function wafanyakazi()
{
    return $this->hasMany(\App\Models\Wafanyakazi::class);
}

    public function masaplaya()
{
    return $this->hasMany(\App\Models\Masaplaya::class);
}

    public function matumizi()
{
    return $this->hasMany(\App\Models\Matumizi::class);
}

    public function manunuzi()
    {
        return $this->hasMany(\App\Models\Manunuzi::class);
    }

    public function mauzo()
    {
        return $this->hasMany(\App\Models\Mauzo::class);
    }

    public function madeni()
    {
        return $this->hasMany(\App\Models\Madeni::class);
    }
    public function marejeshos()
    {
        return $this->hasMany(\App\Models\Marejesho::class);
    }
    
// Add relationship
public function loginHistories()
{
    return $this->hasMany(LoginHistory::class);
}

// Check if company is active (any user active in last 10 minutes)
public function isActive()
{
    // Check both users and employees
    $hasActiveUser = $this->users()
        ->where('last_activity_at', '>=', now()->subMinutes(10))
        ->exists();
    
    $hasActiveEmployee = false;
    
    // Check if wafanyakazi relationship exists
    if (method_exists($this, 'wafanyakazi')) {
        $hasActiveEmployee = $this->wafanyakazi()
            ->where('last_activity_at', '>=', now()->subMinutes(10))
            ->exists();
    }
    
    return $hasActiveUser || $hasActiveEmployee;
}

// Get active users count for this company
public function getActiveUsersCount()
{
    $activeUsers = $this->users()
        ->where('last_activity_at', '>=', now()->subMinutes(10))
        ->count();
    
    $activeEmployees = 0;
    
    // Check if wafanyakazi relationship exists
    if (method_exists($this, 'wafanyakazi')) {
        $activeEmployees = $this->wafanyakazi()
            ->where('last_activity_at', '>=', now()->subMinutes(10))
            ->count();
    }
    
    return $activeUsers + $activeEmployees;
}

public function getTotalUsersCount()
{
    $usersCount = $this->users()->count();
    
    $employeesCount = 0;
    
    // Check if wafanyakazi relationship exists
    if (method_exists($this, 'wafanyakazi')) {
        $employeesCount = $this->wafanyakazi()->count();
    }
    
    return $usersCount + $employeesCount;
}

// Get total login count
public function getTotalLoginCount()
{
    return $this->loginHistories()->count();
}

// Get last login date
public function getLastLoginDate()
{
    return $this->loginHistories()->max('login_at');
}

// Get daily active users for today
public function getDailyActiveUsers()
{
    return $this->loginHistories()
        ->whereDate('login_at', today())
        ->distinct('user_id')
        ->count('user_id');
}

// Get weekly activity data for chart
public function getWeeklyActivity()
{
    $data = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = now()->subDays($i);
        $count = $this->loginHistories()
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
}
}