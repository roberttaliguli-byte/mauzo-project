<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'user_id',
        'mfanyakazi_id',
        'user_type',
        'user_name',
        'action',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array'
    ];

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

    // Get user name
    public function getUserNameAttribute()
    {
        if ($this->user_name) return $this->user_name;
        if ($this->user) return $this->user->name ?? $this->user->username;
        if ($this->mfanyakazi) return $this->mfanyakazi->jina;
        return 'System';
    }

    // Scope for company
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    // Scope by action
    public function scopeOfAction($query, $action)
    {
        return $query->where('action', $action);
    }

    // Scope recent
    public function scopeRecent($query, $limit = 50)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }
}