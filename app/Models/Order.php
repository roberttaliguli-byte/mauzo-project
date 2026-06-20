<?php
// app/Models/Order.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'order_number',
        'customer_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'customer_address',
        'delivery_address',
        'items',
        'subtotal',
        'discount',
        'discount_type',
        'delivery_fee',
        'tax',
        'total',
        'status',
        'order_type',
        'table_number',
        'special_instructions',
        'notes',
        'created_by',
        'paid_at',
        'confirmed_at',
        'processing_at',
        'ready_at',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
        'transferred_to_cart',
        'transferred_at'
    ];

    protected $casts = [
        'items' => 'array',
        'subtotal' => 'float',
        'discount' => 'float',
        'delivery_fee' => 'float',
        'tax' => 'float',
        'total' => 'float',
        'paid_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'processing_at' => 'datetime',
        'ready_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'transferred_at' => 'datetime',
        'transferred_to_cart' => 'boolean'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'paid_at',
        'transferred_at'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function customer()
    {
        return $this->belongsTo(Mteja::class, 'customer_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all Mauzo records created from this order
     */
    public function mauzos()
    {
        return $this->hasMany(Mauzo::class, 'order_id');
    }

    /**
     * Check if order has been transferred to Mauzo
     */
    public function getIsTransferredToMauzoAttribute()
    {
        return $this->mauzos()->count() > 0;
    }
}