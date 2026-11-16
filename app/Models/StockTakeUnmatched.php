<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTakeUnmatched extends Model
{
    protected $table = 'stock_take_unmatched';
    protected $guarded = [];
    protected $casts = ['quantity' => 'float'];
}
