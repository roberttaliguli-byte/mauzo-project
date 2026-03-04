<?php
// app/Models/BidhaaHistory.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BidhaaHistory extends Model
{
    use HasFactory;

    protected $table = 'bidhaa_histories';

    protected $fillable = [
        'bidhaa_id',
        'company_id',
        'idadi_iliyoingizwa',
        'idadi_iliyouzwa',
        'idadi_iliyobaki',
        'bei_nunua',
        'bei_kuuza',
        'aina_ya_shughuli',
        'maelezo',
        'mtumiaji_id',
    ];

    protected $casts = [
        'idadi_iliyoingizwa' => 'decimal:2',
        'idadi_iliyouzwa' => 'decimal:2',
        'idadi_iliyobaki' => 'decimal:2',
        'bei_nunua' => 'decimal:2',
        'bei_kuuza' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    public function bidhaa()
    {
        return $this->belongsTo(Bidhaa::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function mtumiaji()
    {
        return $this->belongsTo(User::class, 'mtumiaji_id');
    }
}