<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    // Allow mass assignment for these fields
    protected $fillable = [
        'package',
        'amount',
        'status',
        'pesapal_tracking_id',
        'pesapal_merchant_reference',
        'company_id', // include if you plan to assign manually
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
