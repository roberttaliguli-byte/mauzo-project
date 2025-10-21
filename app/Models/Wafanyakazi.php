<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wafanyakazi extends Model
{
    use HasFactory;

    protected $table = 'wafanyakazis'; // ensure table name is correct

    protected $fillable = [
        'jina',
        'simu',
        'jinsia',
        'anuani',
        'barua_pepe',
        'ndugu',
        'simu_ndugu',
        'username',
        'password',
        'tarehe_kuzaliwa',
        'company_id', // ✅ added for company linkage
    ];

    /**
     * ✅ Uhusiano na kampuni (Company)
     */
    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class);
    }
}
