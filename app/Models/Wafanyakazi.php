<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Wafanyakazi extends Authenticatable
{
    use HasFactory;

    protected $table = 'wafanyakazis';

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
        'role', // ✅ Make sure this is included
        'tarehe_kuzaliwa',
        'getini',
        'company_id',
    ];

    protected $hidden = ['password'];

    // ✅ Add this to always set role as 'mfanyakazi'
    protected $attributes = [
        'role' => 'mfanyakazi',
    ];

    /**
     * ✅ Uhusiano na kampuni (Company)
     */
    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class);
    }
}