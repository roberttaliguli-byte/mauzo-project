<?php
// app/Models/Wafanyakazi.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Helpers\ActivityHelper;

class Wafanyakazi extends Authenticatable
{
    use HasFactory;

    protected $table = 'wafanyakazis';

    protected $fillable = [
        'jina',
        'simu',
        'jinsia',
        'anuani',
        'barua_pepe',
        'ndugu',
        'simu_ndugu',
        'username',
        'password',
        'role',
        'tarehe_kuzaliwa',
        'getini',
        'company_id',
        'uwezo',
        'salary',
        'salary_currency',
        'salary_frequency',
        'allow_counter_access',
        'remarks',
        'last_login_at',
        'login_count',
        'active'
    ];

    protected $hidden = ['password'];

    protected $attributes = [
        'role' => 'mfanyakazi',
        'uwezo' => 'mdogo',
        'salary' => 0,
        'salary_currency' => 'TZS',
        'salary_frequency' => 'monthly',
        'allow_counter_access' => false,
        'active' => true,
        'login_count' => 0
    ];

    protected $casts = [
        'salary' => 'float',
        'allow_counter_access' => 'boolean',
        'active' => 'boolean',
        'last_login_at' => 'datetime',
        'tarehe_kuzaliwa' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function salaries()
    {
        return $this->hasMany(EmployeeSalary::class, 'mfanyakazi_id');
    }

    public function salaryDeductions()
    {
        return $this->hasMany(EmployeeSalaryDeduction::class, 'mfanyakazi_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'created_by', 'id');
    }

    public function getCurrentSalaryAttribute()
    {
        return $this->salary ?? 0;
    }

    public function getTotalDeductionsAttribute()
    {
        return $this->salaryDeductions()
            ->where('status', 'approved')
            ->sum('amount');
    }

    public function getNetSalaryAttribute()
    {
        return $this->getCurrentSalaryAttribute() - $this->getTotalDeductionsAttribute();
    }

    public function hasFullAccess(): bool
    {
        return $this->uwezo === 'mkubwa';
    }

    public function hasCounterAccess(): bool
    {
        return $this->allow_counter_access && $this->getini === 'ingia';
    }

    public function recordLogin($ipAddress = null)
    {
        LoginHistory::create([
            'mfanyakazi_id' => $this->id,
            'company_id' => $this->company_id,
            'login_at' => now(),
            'ip_address' => $ipAddress ?? request()->ip(),
            'user_type' => 'mfanyakazi'
        ]);

        ActivityHelper::logLogin($this, 'employee');

        $this->update([
            'last_login_at' => now(),
            'login_count' => ($this->login_count ?? 0) + 1
        ]);
    }

    public function recordLogout()
    {
        $lastLogin = LoginHistory::where('mfanyakazi_id', $this->id)
            ->whereNull('logout_at')
            ->latest('login_at')
            ->first();

        if ($lastLogin) {
            $lastLogin->update(['logout_at' => now()]);
        }

        ActivityHelper::logLogout($this, 'employee');
    }
}