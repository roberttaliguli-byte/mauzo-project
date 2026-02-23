<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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

}
