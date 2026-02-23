<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
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

    public function company()
    {
        return $this->belongsTo(Company::class);
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
}