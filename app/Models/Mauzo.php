<?php
// app/Models/Mauzo.php

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
        'lipa_kwa_type',
        'mteja_id',
        'user_id',
        'mfanyakazi_id', // Add this field
        'sale_date',
        'order_id',
        'order_number'
    ];

    protected $casts = [
        'bei' => 'decimal:2',
        'punguzo' => 'decimal:2',
        'jumla' => 'decimal:2',
        'reprint_count' => 'integer',
        'idadi' => 'float'
    ];

    const PAYMENT_METHODS = [
        'cash' => 'Cash',
        'lipa_namba' => 'Lipa Namba',
        'bank' => 'Bank'
    ];

    const DEFAULT_PAYMENT_METHOD = 'cash';

    const LIPA_NAMBA_TYPES = [
        'mpesa' => 'M-Pesa',
        'mixx_by_yas' => 'Mixx by Yas',
        'airtel_money' => 'Airtel Money',
        'halopesa' => 'HaloPesa',
        'other' => 'Nyingine'
    ];

    const BANK_TYPES = [
        'crdb' => 'CRDB',
        'nmb' => 'NMB',
        'nbc' => 'NBC',
        'other' => 'Nyingine'
    ];

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

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function mfanyakazi()
    {
        return $this->belongsTo(\App\Models\Wafanyakazi::class, 'mfanyakazi_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function getLipaKwaNameAttribute()
    {
        $paymentName = self::PAYMENT_METHODS[$this->lipa_kwa] ?? 'Unknown';
        
        if ($this->lipa_kwa_type && $this->lipa_kwa !== 'cash') {
            if ($this->lipa_kwa === 'lipa_namba') {
                $typeName = self::LIPA_NAMBA_TYPES[$this->lipa_kwa_type] ?? $this->lipa_kwa_type;
                return "{$paymentName} ({$typeName})";
            } elseif ($this->lipa_kwa === 'bank') {
                $typeName = self::BANK_TYPES[$this->lipa_kwa_type] ?? $this->lipa_kwa_type;
                return "{$paymentName} ({$typeName})";
            }
        }
        
        return $paymentName;
    }

    public function getProcessorNameAttribute()
    {
        if ($this->user_id) {
            $user = $this->user;
            return $user ? $user->name : 'Unknown User';
        }
        if ($this->mfanyakazi_id) {
            $employee = $this->mfanyakazi;
            return $employee ? $employee->jina : 'Unknown Employee';
        }
        return 'System';
    }

    public function getProcessorTypeAttribute()
    {
        if ($this->user_id) return 'Boss';
        if ($this->mfanyakazi_id) return 'Employee';
        return 'System';
    }

    public function scopeByPaymentMethod($query, $method)
    {
        return $query->where('lipa_kwa', $method);
    }

    public function scopeByPaymentType($query, $type)
    {
        return $query->where('lipa_kwa_type', $type);
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

    public function getSaleDateAttribute($value)
    {
        return $value ?? $this->created_at;
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

    public function setCreatedAt($value)
    {
        $this->{static::CREATED_AT} = $value;
        return $this;
    }

    public function setUpdatedAt($value)
    {
        $this->{static::UPDATED_AT} = $value;
        return $this;
    }
}