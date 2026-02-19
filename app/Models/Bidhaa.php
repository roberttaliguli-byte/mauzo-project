<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bidhaa extends Model
{
    use HasFactory;

    protected $table = 'bidhaas';

    protected $fillable = [
        'jina',
        'aina',
        'kipimo',
        'idadi',
        'bei_nunua',
        'bei_kuuza',
        'expiry',
        'barcode',
        'company_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'idadi' => 'decimal:2', // Add this line for decimal support
        'bei_nunua' => 'decimal:2',
        'bei_kuuza' => 'decimal:2',
        'expiry' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get formatted idadi with decimal places
     */
    public function getFormattedIdadiAttribute(): string
    {
        return number_format($this->idadi, 2);
    }

    /**
     * Get idadi as float
     */
    public function getIdadiAsFloatAttribute(): float
    {
        return (float) $this->idadi;
    }

    /**
     * Check if quantity is zero
     */
    public function getIsOutOfStockAttribute(): bool
    {
        return $this->idadi <= 0;
    }

    /**
     * Check if quantity is low (less than 10)
     */
    public function getIsLowStockAttribute(): bool
    {
        return $this->idadi > 0 && $this->idadi < 10;
    }

    /**
     * Get stock status
     */
    public function getStockStatusAttribute(): string
    {
        if ($this->idadi <= 0) {
            return 'out_of_stock';
        }
        if ($this->idadi < 10) {
            return 'low_stock';
        }
        return 'in_stock';
    }

    /**
     * Uhusiano na Manunuzi
     */
    public function manunuzi()
    {
        return $this->hasMany(Manunuzi::class);
    }

    /**
     * Uhusiano na Mauzo
     */
    public function mauzos()
    {
        return $this->hasMany(Mauzo::class, 'bidhaa_id');
    }

    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class);
    }
}