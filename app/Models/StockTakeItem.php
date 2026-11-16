<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTakeItem extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_unavailable'      => 'boolean',
        'counted_at'          => 'datetime',
        'previous_counted_at' => 'datetime',
        'resolved_at'         => 'datetime',
        'system_qty'          => 'float',
        'physical_qty'        => 'float',
        'previous_qty'        => 'float',
        'received_qty'        => 'float',
        'sold_qty'            => 'float',
        'returns_in_qty'      => 'float',
        'returns_out_qty'     => 'float',
        'adjustments_qty'     => 'float',
        'expected_qty'        => 'float',
        'diff_system'         => 'float',
        'diff_expected'       => 'float',
        'variance'            => 'float',
        'purchase_price'      => 'float',
        'sell_price'          => 'float',
    ];

    public function take()   { return $this->belongsTo(StockTake::class, 'stock_take_id'); }
    public function bidhaa() { return $this->belongsTo(Bidhaa::class, 'bidhaa_id'); }
}
