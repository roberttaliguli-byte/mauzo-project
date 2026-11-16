<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTake extends Model
{
    protected $guarded = [];

    protected $casts = [
        'started_at'  => 'datetime',
        'closed_at'   => 'datetime',
        'applied_at'  => 'datetime',
        'summary'     => 'array',
    ];

    public function items()     { return $this->hasMany(StockTakeItem::class); }
    public function unmatched() { return $this->hasMany(StockTakeUnmatched::class); }
    public function previous()  { return $this->belongsTo(StockTake::class, 'previous_take_id'); }

    public function isOpen(): bool   { return $this->status === 'counting'; }
    public function isClosed(): bool { return $this->status === 'closed'; }
    public function isApplied(): bool { return !is_null($this->applied_at); }
}
