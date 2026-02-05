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
        'punguzo',
        'punguzo_aina',
        'jumla',
        'is_debt_repayment',
        'reprint_count',
        'lipa_kwa', // ✅ ADDED payment method
    ];

    protected $casts = [
        'bei' => 'decimal:2',
        'punguzo' => 'decimal:2',
        'jumla' => 'decimal:2',
        'reprint_count' => 'integer'
    ];

    /**
     * Payment method options
     */
    const PAYMENT_METHODS = [
        'cash' => 'Cash',
        'lipa_namba' => 'Lipa Namba',
        'bank' => 'Bank'
    ];

    /**
     * Default payment method
     */
    const DEFAULT_PAYMENT_METHOD = 'cash';

    /**
     * Relationship: Each sale belongs to a Bidhaa
     */
    public function bidhaa()
    {
        return $this->belongsTo(Bidhaa::class);
    }

    /**
     * Relationship: Each sale belongs to a Company
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Accessor: Get payment method name
     */
    public function getLipaKwaNameAttribute()
    {
        return self::PAYMENT_METHODS[$this->lipa_kwa] ?? 'Unknown';
    }

    /**
     * Scope: Filter by payment method
     */
    public function scopeByPaymentMethod($query, $method)
    {
        return $query->where('lipa_kwa', $method);
    }

    /**
     * Scope: Filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Accessor: Get total with discount applied correctly
     */
    public function getNetTotalAttribute()
    {
        $subtotal = $this->bei * $this->idadi;
        
        if (!$this->punguzo) {
            return $subtotal;
        }
        
        // Calculate discount based on type
        if ($this->punguzo_aina === 'bidhaa') {
            // Discount per item
            $totalDiscount = $this->punguzo * $this->idadi;
        } else {
            // Fixed discount amount
            $totalDiscount = $this->punguzo;
        }
        
        return $subtotal - $totalDiscount;
    }

    /**
     * Accessor: Get total discount (not per item)
     */
    public function getTotalDiscountAttribute()
    {
        if (!$this->punguzo) {
            return 0;
        }
        
        if ($this->punguzo_aina === 'bidhaa') {
            // Discount per item
            return $this->punguzo * $this->idadi;
        }
        
        // Fixed discount amount
        return $this->punguzo;
    }

    /**
     * Boot method to set default payment method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->lipa_kwa)) {
                $model->lipa_kwa = self::DEFAULT_PAYMENT_METHOD;
            }
        });
    }
}