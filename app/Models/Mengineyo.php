<?php
// app/Models/Mengineyo.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mengineyo extends Model
{
    protected $table = 'mengineyo';
    
    protected $fillable = [
        'chanzo', 'kiasi', 'maelezo', 'tarehe', 'aina', 'mfanyakazi_id', 'company_id'
    ];
    
    protected $casts = [
        'tarehe' => 'date',
        'kiasi' => 'decimal:2'
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
    
    public function scopeSearch($query, $term)
    {
        if ($term) {
            return $query->where('chanzo', 'like', "%{$term}%")
                         ->orWhere('maelezo', 'like', "%{$term}%");
        }
        return $query;
    }
}