<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Matumizi extends Model
{
    use HasFactory;

    protected $fillable = [
        'aina',
        'maelezo',
        'gharama',
        'company_id',
        'shift_session_id',
        'user_id',
        'mfanyakazi_id',
        'created_at' // Allow created_at to be fillable
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class);
    }
    
    // Accessor to get formatted date
    public function getFormattedDateAttribute()
    {
        return $this->created_at ? $this->created_at->format('d/m/Y') : '';
    }
    
    // Accessor to get formatted time
    public function getFormattedTimeAttribute()
    {
        return $this->created_at ? $this->created_at->format('H:i') : '';
    }
    // Add this to your Matumizi model
public function user()
{
    return $this->belongsTo(\App\Models\User::class);
}

    public function mfanyakazi()
    {
        return $this->belongsTo(\App\Models\Wafanyakazi::class, 'mfanyakazi_id');
    }

    public function shiftSession()
    {
        return $this->belongsTo(\App\Models\ShiftSession::class, 'shift_session_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            try {
                // Record who spent the money when possible.
                if (empty($model->user_id) && empty($model->mfanyakazi_id)) {
                    if (\Illuminate\Support\Facades\Auth::guard('mfanyakazi')->check()) {
                        $model->mfanyakazi_id = \Illuminate\Support\Facades\Auth::guard('mfanyakazi')->id();
                    } elseif (\Illuminate\Support\Facades\Auth::guard('web')->check()) {
                        $model->user_id = \Illuminate\Support\Facades\Auth::guard('web')->id();
                    }
                }

                // Attach the expense to the user's open shift, so it appears in
                // that shift's closing balance (and never in someone else's).
                if (empty($model->shift_session_id) && $model->company_id) {
                    $session = \App\Models\ShiftSession::activeForCurrentUser();
                    if ($session && (int) $session->company_id === (int) $model->company_id) {
                        $model->shift_session_id = $session->id;
                    }
                }
            } catch (\Throwable $e) {
                \Log::warning('Shift attribution failed for expense: ' . $e->getMessage());
            }
        });
    }
}