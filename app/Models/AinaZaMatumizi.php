<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AinaZaMatumizi extends Model
{
    use HasFactory;

    protected $table = 'aina_za_matumizi';
    
    protected $fillable = [
        'jina',
        'maelezo',
        'rangi',
        'kategoria',
        'company_id'
    ];

    /**
     * Relationship with company
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Relationship with matumizi
     */
    public function matumizi()
    {
        return $this->hasMany(Matumizi::class, 'aina', 'jina');
    }

    /**
     * Scope for current company
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }
}