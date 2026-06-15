<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'mfanyakazi_id',  // Add this for employees
        'company_id',
        'login_at',
        'logout_at',
        'ip_address'
    ];

    protected $casts = [
        'login_at' => 'datetime',
        'logout_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mfanyakazi()
    {
        return $this->belongsTo(Wafanyakazi::class, 'mfanyakazi_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Get user name regardless of type
    public function getUserNameAttribute()
    {
        if ($this->user) {
            return $this->user->name ?? $this->user->username;
        }
        if ($this->mfanyakazi) {
            return $this->mfanyakazi->jina;
        }
        return 'Unknown User';
    }

    // Get user type
    public function getUserTypeAttribute()
    {
        if ($this->user) return 'Boss';
        if ($this->mfanyakazi) return 'Employee';
        return 'Unknown';
    }

    // Scope for today's logins
    public function scopeToday($query)
    {
        return $query->whereDate('login_at', today());
    }

    // Scope for last 7 days
    public function scopeLast7Days($query)
    {
        return $query->where('login_at', '>=', now()->subDays(7));
    }
    
    // Scope for company
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }
}