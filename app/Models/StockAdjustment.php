<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    protected $guarded = [];
    protected $casts = [
        'affects_expected' => 'boolean',
        'old_qty' => 'float',
        'new_qty' => 'float',
        'difference' => 'float',
    ];
}
