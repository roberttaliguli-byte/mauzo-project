<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $table = 'activity_logs';

    protected $fillable = [
        'company_id',
        'user_id',
        'mfanyakazi_id',
        'user_name',
        'user_role',
        'activity_type',
        'description',
        'model_type',
        'model_id',
        'amount',
        'ip_address',
        'user_agent',
        'created_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'amount' => 'decimal:2'
    ];

    public $timestamps = false;

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mfanyakazi()
    {
        return $this->belongsTo(Wafanyakazi::class, 'mfanyakazi_id');
    }

    // Scope for filtering by company - FIXED to use where instead of forCompany
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    // Scope for recent activities - FIXED
    public function scopeRecent($query, $limit = 50)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    // Scope by activity type
    public function scopeOfType($query, $type)
    {
        return $query->where('activity_type', $type);
    }

    // Get recorded by user name
    public function getRecordedByAttribute()
    {
        return $this->user_name ?? 'System';
    }
}