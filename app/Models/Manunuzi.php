<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Manunuzi extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'bidhaa_id',
        'idadi',
        'bei',
        'unit_cost',
        'expiry',
        'saplaya',
        'simu',
        'mengineyo',
    ];

    protected $casts = [
        'idadi' => 'decimal:2',
        'bei' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'expiry' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship: Manunuzi belongs to one Bidhaa
     */
    public function bidhaa()
    {
        return $this->belongsTo(Bidhaa::class);
    }

    /**
     * Relationship: Manunuzi belongs to a specific company
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get formatted idadi with decimal places
     */
    public function getFormattedIdadiAttribute(): string
    {
        return number_format($this->idadi, 2);
    }

    /**
     * Get formatted total cost
     */
    public function getFormattedBeiAttribute(): string
    {
        return number_format($this->bei, 2);
    }

    /**
     * Get formatted unit cost
     */
    public function getFormattedUnitCostAttribute(): string
    {
        return number_format($this->unit_cost, 2);
    }
}