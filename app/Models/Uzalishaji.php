<?php
// app/Models/Uzalishaji.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Uzalishaji extends Model
{
    use HasFactory;

    protected $table = 'uzalishajis';

    protected $fillable = [
        'tarehe',
        'jina',
        'aina_bidhaa',
        'maelezo',
        'jumla_gharama',
        'idadi_iliyozalishwa',
        'kipimo',
        'gharama_kwa_moja',
        'bei_kununua_ilipendekezwa',
        'bei_kuuza_ilichaguliwa',
        'faida_kwa_moja',
        'asilimia_faida',
        'faida_ya_jumla',
        'bidhaa_id',
        'company_id',
        'mtumiaji_id',
        'mfanyakazi_id',
        'imekamilika',
        'status'
    ];

    protected $casts = [
        'tarehe' => 'date',
        'jumla_gharama' => 'decimal:2',
        'idadi_iliyozalishwa' => 'decimal:2',
        'gharama_kwa_moja' => 'decimal:2',
        'bei_kununua_ilipendekezwa' => 'decimal:2',
        'bei_kuuza_ilichaguliwa' => 'decimal:2',
        'faida_kwa_moja' => 'decimal:2',
        'asilimia_faida' => 'decimal:2',
        'faida_ya_jumla' => 'decimal:2',
        'imekamilika' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function gharama()
    {
        return $this->hasMany(UzalishajiGharama::class);
    }

    public function bidhaa()
    {
        return $this->belongsTo(Bidhaa::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function mtumiaji()
    {
        return $this->belongsTo(User::class, 'mtumiaji_id');
    }

    public function mfanyakazi()
    {
        return $this->belongsTo(Wafanyakazi::class, 'mfanyakazi_id');
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('imekamilika', true);
    }

    public function scopePending($query)
    {
        return $query->where('imekamilika', false);
    }

    public function scopeByCompany($query)
    {
        $companyId = $this->getCompanyId();
        return $query->where('company_id', $companyId);
    }

    // Get company ID
    private function getCompanyId()
    {
        if (Auth::check()) {
            return Auth::user()->company_id;
        } elseif (Auth::guard('mfanyakazi')->check()) {
            return Auth::guard('mfanyakazi')->user()->company_id;
        }
        return null;
    }

    // Format helpers
    public function getFormattedJumlaGharamaAttribute()
    {
        return number_format($this->jumla_gharama, 0);
    }

    public function getFormattedGharamaKwaMojaAttribute()
    {
        return number_format($this->gharama_kwa_moja, 0);
    }

    public function getFormattedFaidaKwaMojaAttribute()
    {
        return number_format($this->faida_kwa_moja ?? 0, 0);
    }

    public function getFormattedFaidaYaJumlaAttribute()
    {
        return number_format($this->faida_ya_jumla ?? 0, 0);
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($uzalishaji) {
            if (Auth::check()) {
                $uzalishaji->mtumiaji_id = Auth::id();
                $uzalishaji->company_id = Auth::user()->company_id;
            } elseif (Auth::guard('mfanyakazi')->check()) {
                $uzalishaji->mfanyakazi_id = Auth::guard('mfanyakazi')->id();
                $uzalishaji->company_id = Auth::guard('mfanyakazi')->user()->company_id;
            }
        });
    }
}