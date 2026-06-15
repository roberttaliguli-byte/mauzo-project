<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Helpers\ActivityHelper;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

// inside User model
protected $fillable = [
    'company_id',
    'username',
    'name',
    'email',
    'password',
    'role',
    'email_verification_token',
    'is_approved',
    'email_verified_at'
];

public function company()
{
    return $this->belongsTo(\App\Models\Company::class);
}

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

// Add relationship
public function loginHistories()
{
    return $this->hasMany(LoginHistory::class);
}

// Check if user is active (within last 10 minutes)
public function isActive()
{
    return $this->last_activity_at && $this->last_activity_at->gt(now()->subMinutes(10));
}

// Update last activity
public function updateLastActivity()
{
    $this->update(['last_activity_at' => now()]);
}


// Add to User model
public function recordLogin($ipAddress = null)
{
    // Record to login_histories (keep your existing)
    LoginHistory::create([
        'user_id' => $this->id,
        'company_id' => $this->company_id,
        'login_at' => now(),
        'ip_address' => $ipAddress ?? request()->ip()
    ]);
    
    // Record to activity log
    ActivityHelper::logLogin($this, 'boss');
    
    // Update last login
    $this->update([
        'last_login_at' => now(),
        'login_count' => ($this->login_count ?? 0) + 1
    ]);
}

public function recordLogout()
{
    $lastLogin = LoginHistory::where('user_id', $this->id)
        ->whereNull('logout_at')
        ->latest('login_at')
        ->first();
        
    if ($lastLogin) {
        $lastLogin->update(['logout_at' => now()]);
    }
    
    ActivityHelper::logLogout($this, 'boss');
}

}
