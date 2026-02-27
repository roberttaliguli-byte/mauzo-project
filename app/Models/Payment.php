<?php
// app/Models/Payment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'transaction_reference',
        'pesapal_transaction_tracking_id',
        'merchant_reference',
        'package_type',
        'amount',
        'currency',
        'phone_number',
        'payment_method',
        'status',
        'payment_request_data',
        'payment_response_data',
        'ipn_data',
        'payment_date',
        'expiry_date'
    ];

    protected $casts = [
        'payment_request_data' => 'array',
        'payment_response_data' => 'array',
        'ipn_data' => 'array',
        'payment_date' => 'datetime',
        'expiry_date' => 'datetime'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Generate unique transaction reference
    public static function generateTransactionReference()
    {
        return 'TXN-' . strtoupper(uniqid()) . '-' . date('Ymd');
    }

    // Generate merchant reference
    public static function generateMerchantReference()
    {
        return 'MER-' . time() . '-' . rand(1000, 9999);
    }

    // Get package amount with updated pricing
    public static function getPackageAmount($packageType)
    {
        $amounts = [
            'Free Trial 14 days' => 0,
            '30 days' => 1000,    // 15,000 TZS for 1 month
            '180 days' => 75000,   // 75,000 TZS for 6 months
            '366 days' => 150000    // 150,000 TZS for 1 year
        ];

        return $amounts[$packageType] ?? 0;
    }

    // Check if payment is successful
    public function isSuccessful()
    {
        return $this->status === 'completed';
    }

    // Get package duration in days
    public function getPackageDaysAttribute()
    {
        $days = [
            'Free Trial 14 days' => 14,
            '30 days' => 30,
            '180 days' => 180,
            '366 days' => 366
        ];

        return $days[$this->package_type] ?? 0;
    }

    // Get formatted amount with currency
    public function getFormattedAmountAttribute()
    {
        if ($this->amount == 0) {
            return 'Free';
        }
        return 'TZS ' . number_format($this->amount);
    }

    // Get payment status badge class
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'completed' => 'bg-green-100 text-green-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            'failed' => 'bg-red-100 text-red-800',
            'cancelled' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    // Get payment status text in Swahili
    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'completed' => 'Imekamilika',
            'pending' => 'Inasubiri',
            'failed' => 'Imeshindwa',
            'cancelled' => 'Imeghairiwa',
            default => 'Haijulikani'
        };
    }

    // Get payment method icon
    public function getPaymentMethodIconAttribute()
    {
        return match($this->payment_method) {
            'TIGO' => 'fas fa-phone-alt text-pink-600',
            'VODACOM' => 'fas fa-phone-alt text-green-600',
            'AIRTEL' => 'fas fa-phone-alt text-red-600',
            default => 'fas fa-credit-card'
        };
    }

    // Get payment method color
    public function getPaymentMethodColorAttribute()
    {
        return match($this->payment_method) {
            'TIGO' => 'pink',
            'VODACOM' => 'green',
            'AIRTEL' => 'red',
            default => 'gray'
        };
    }

    // Scope for completed payments
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // Scope for pending payments
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Scope for failed payments
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    // Scope for payments in date range
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    // Scope for payments by package type
    public function scopeByPackage($query, $packageType)
    {
        return $query->where('package_type', $packageType);
    }

    // Check if payment is expired
    public function isExpired()
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    // Get days until expiry
    public function getDaysUntilExpiryAttribute()
    {
        if (!$this->expiry_date) {
            return null;
        }
        
        return now()->diffInDays($this->expiry_date, false);
    }

    // Get expiry status
    public function getExpiryStatusAttribute()
    {
        if (!$this->expiry_date) {
            return 'no_expiry';
        }
        
        $daysLeft = $this->days_until_expiry;
        
        if ($daysLeft < 0) {
            return 'expired';
        } elseif ($daysLeft <= 7) {
            return 'expiring_soon';
        } else {
            return 'active';
        }
    }

    // Get expiry badge class
    public function getExpiryBadgeClassAttribute()
    {
        return match($this->expiry_status) {
            'expired' => 'bg-red-100 text-red-800',
            'expiring_soon' => 'bg-yellow-100 text-yellow-800',
            'active' => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    // Get package discount amount (compared to monthly rate)
    public function getDiscountAmountAttribute()
    {
        if ($this->package_type === 'Free Trial 14 days' || $this->amount == 0) {
            return 0;
        }

        $monthlyRate = 15000; // Base monthly rate
        
        return match($this->package_type) {
            '180 days' => (6 * $monthlyRate) - $this->amount, // 90,000 - 75,000 = 15,000
            '366 days' => (12 * $monthlyRate) - $this->amount, // 180,000 - 150,000 = 30,000
            default => 0
        };
    }

    // Get discount percentage
    public function getDiscountPercentageAttribute()
    {
        if ($this->discount_amount == 0) {
            return 0;
        }

        $monthlyTotal = match($this->package_type) {
            '180 days' => 6 * 15000, // 90,000
            '366 days' => 12 * 15000, // 180,000
            default => 0
        };

        return $monthlyTotal > 0 ? round(($this->discount_amount / $monthlyTotal) * 100) : 0;
    }

    // Get formatted discount
    public function getFormattedDiscountAttribute()
    {
        if ($this->discount_amount == 0) {
            return 'Hakuna punguzo';
        }
        
        return 'Punguzo la TZS ' . number_format($this->discount_amount) . ' (' . $this->discount_percentage . '%)';
    }

    // Boot method for model events
    protected static function boot()
    {
        parent::boot();

        // Set default values before creating
        static::creating(function ($payment) {
            if (empty($payment->currency)) {
                $payment->currency = 'TZS';
            }
            if (empty($payment->status)) {
                $payment->status = 'pending';
            }
        });

        // Log when payment is completed
        static::updated(function ($payment) {
            if ($payment->isDirty('status') && $payment->status === 'completed') {
                \Log::info('Payment completed', [
                    'payment_id' => $payment->id,
                    'company_id' => $payment->company_id,
                    'amount' => $payment->amount,
                    'package' => $payment->package_type
                ]);
            }
        });
    }
}