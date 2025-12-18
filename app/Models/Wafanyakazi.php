<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Wafanyakazi extends Authenticatable

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
        'role',
        'tarehe_kuzaliwa',
        'getini',
        'company_id', // ✅ added for company linkage
    ];


    protected $hidden = ['password'];


    /**
     * ✅ Uhusiano na kampuni (Company)
     */
    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class);
    }
}
