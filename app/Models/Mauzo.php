<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mauzo extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'bidhaa_id',
        'madeni_id',
        'receipt_no',
        'idadi',
        'bei',
        'bei_type_used',
        'punguzo',
        'punguzo_aina',
        'jumla',
        'is_debt_repayment',
        'reprint_count',
        'lipa_kwa',
        'mteja_id',
    ];

    protected $casts = [
        'bei' => 'decimal:2',
        'punguzo' => 'decimal:2',
        'jumla' => 'decimal:2',
        'reprint_count' => 'integer'
    ];

    const PAYMENT_METHODS = [
        'cash' => 'Cash',
        'lipa_namba' => 'Lipa Namba',
        'bank' => 'Bank'
    ];

    const DEFAULT_PAYMENT_METHOD = 'cash';

    public function bidhaa()
    {
        return $this->belongsTo(Bidhaa::class);
    }

    public function mteja()
    {
        return $this->belongsTo(Mteja::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function getLipaKwaNameAttribute()
    {
        return self::PAYMENT_METHODS[$this->lipa_kwa] ?? 'Unknown';
    }

    public function scopeByPaymentMethod($query, $method)
    {
        return $query->where('lipa_kwa', $method);
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function getNetTotalAttribute()
    {
        $subtotal = $this->bei * $this->idadi;
        
        if (!$this->punguzo) {
            return $subtotal;
        }
        
        if ($this->punguzo_aina === 'bidhaa') {
            $totalDiscount = $this->punguzo * $this->idadi;
        } else {
            $totalDiscount = $this->punguzo;
        }
        
        return $subtotal - $totalDiscount;
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function getTotalDiscountAttribute()
    {
        if (!$this->punguzo) {
            return 0;
        }
        
        if ($this->punguzo_aina === 'bidhaa') {
            return $this->punguzo * $this->idadi;
        }
        
        return $this->punguzo;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->lipa_kwa)) {
                $model->lipa_kwa = self::DEFAULT_PAYMENT_METHOD;
            }
            if (empty($model->bei_type_used)) {
                $model->bei_type_used = 'rejareja';
            }
        });
    }
}