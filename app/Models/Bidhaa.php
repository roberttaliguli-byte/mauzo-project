<?php
// app/Models/Bidhaa.php

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
        'bei_uzo_jumla',
        'bei_kiasi_cha_chaguo',
        'expiry',
        'barcode',
        'company_id',
    ];

    protected $casts = [
        'idadi' => 'decimal:2',
        'bei_nunua' => 'decimal:2',
        'bei_kuuza' => 'decimal:2',
        'bei_uzo_jumla' => 'decimal:2',
        'expiry' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getCurrentPriceAttribute(): float
    {
        if ($this->bei_kiasi_cha_chaguo === 'jumla' && $this->bei_uzo_jumla !== null) {
            return (float) $this->bei_uzo_jumla;
        }
        return (float) $this->bei_kuuza;
    }

    public function getFormattedCurrentPriceAttribute(): string
    {
        return number_format($this->current_price, 0) . ' TZS';
    }

    public function getPriceTypeLabelAttribute(): string
    {
        return $this->bei_kiasi_cha_chaguo === 'jumla' ? 'Jumla' : 'Rejareja';
    }

    public function getRetailPriceDisplayAttribute(): string
    {
        return number_format($this->bei_kuuza, 0) . ' TZS';
    }

    public function getWholesalePriceDisplayAttribute(): ?string
    {
        if ($this->bei_uzo_jumla !== null) {
            return number_format($this->bei_uzo_jumla, 0) . ' TZS';
        }
        return null;
    }

    public function getPricesDisplayAttribute(): string
    {
        $prices = "Rejareja: " . number_format($this->bei_kuuza, 0) . " TZS";
        if ($this->bei_uzo_jumla !== null) {
            $prices .= " | Jumla: " . number_format($this->bei_uzo_jumla, 0) . " TZS";
        }
        return $prices;
    }

    public function getFormattedIdadiAttribute(): string
    {
        return number_format($this->idadi, 2);
    }

    public function getIdadiAsFloatAttribute(): float
    {
        return (float) $this->idadi;
    }

    public function getIsOutOfStockAttribute(): bool
    {
        return $this->idadi <= 0;
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->idadi > 0 && $this->idadi < 10;
    }

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

    public function manunuzi()
    {
        return $this->hasMany(Manunuzi::class);
    }

    public function mauzos()
    {
        return $this->hasMany(Mauzo::class, 'bidhaa_id');
    }

    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class);
    }

    public function histories()
    {
        return $this->hasMany(\App\Models\BidhaaHistory::class)->orderBy('created_at', 'desc');
    }

    public static function boot()
    {
        parent::boot();
        
        static::created(function ($bidhaa) {
            $bidhaa->recordHistory('ingizo', $bidhaa->idadi, 'Bidhaa mpya imeingizwa');
        });
        
        static::updated(function ($bidhaa) {
            if ($bidhaa->wasChanged('idadi')) {
                $oldIdadi = $bidhaa->getOriginal('idadi');
                $newIdadi = $bidhaa->idadi;
                
                if ($newIdadi > $oldIdadi) {
                    $bidhaa->recordHistory(
                        'ingizo', 
                        $newIdadi - $oldIdadi, 
                        'Idadi imeongezwa'
                    );
                } elseif ($newIdadi < $oldIdadi) {
                    $bidhaa->recordHistory(
                        'mauzo', 
                        $oldIdadi - $newIdadi, 
                        'Idadi imepungua (mauzo/marekebisho)'
                    );
                }
            }
        });
    }

    public function recordHistory($type, $quantity, $description = null)
    {
        return $this->histories()->create([
            'company_id' => $this->company_id,
            'idadi_iliyoingizwa' => $type === 'ingizo' ? $quantity : 0,
            'idadi_iliyouzwa' => $type === 'mauzo' ? $quantity : 0,
            'idadi_iliyobaki' => $this->idadi,
            'bei_nunua' => $this->bei_nunua,
            'bei_kuuza' => $this->bei_kuuza,
            'bei_uzo_jumla' => $this->bei_uzo_jumla,
            'aina_ya_shughuli' => $type,
            'maelezo' => $description,
            'mtumiaji_id' => auth()->id(),
        ]);
    }
}