<?php
// app/Models/Banking.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banking extends Model
{
    protected $table = 'banking';
    
    protected $fillable = [
        'benki', 'kiasi', 'saldo_baada_ya_banking', 
        'maelezo', 'tarehe', 'status', 'mfanyakazi_id', 'company_id'
    ];
    
    protected $casts = [
        'tarehe' => 'date',
        'kiasi' => 'decimal:2',
        'saldo_baada_ya_banking' => 'decimal:2'
    ];
    
    public function mfanyakazi()
    {
        return $this->belongsTo(Wafanyakazi::class, 'mfanyakazi_id');
    }
    
    public function scopeBetweenDates($query, $start, $end)
    {
        if ($start && $end) {
            return $query->whereBetween('tarehe', [$start, $end]);
        }
        return $query;
    }
    
    public function scopeByBank($query, $bank)
    {
        if ($bank && $bank !== 'all') {
            return $query->where('benki', $bank);
        }
        return $query;
    }
}