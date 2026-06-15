<?php

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
        'uwezo', // Add this
    ];

    protected $hidden = ['password'];

    protected $attributes = [
        'role' => 'mfanyakazi',
        'uwezo' => 'mdogo', // Default to mdogo
    ];

    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class);
    }
    
    // Helper method to check if employee has full access
    public function hasFullAccess(): bool
    {
        return $this->uwezo === 'mkubwa';
    }

// Add to Wafanyakazi model
public function recordLogin($ipAddress = null)
{
    LoginHistory::create([
        'mfanyakazi_id' => $this->id,
        'company_id' => $this->company_id,
        'login_at' => now(),
        'ip_address' => $ipAddress ?? request()->ip()
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