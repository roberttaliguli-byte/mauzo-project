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
        'role',
        'tarehe_kuzaliwa',
        'getini',
        'company_id',
        'uwezo', // Add this
    ];

    protected $hidden = ['password'];

    protected $attributes = [
        'role' => 'mfanyakazi',
        'uwezo' => 'mdogo', // Default to mdogo
    ];

    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class);
    }
    
    // Helper method to check if employee has full access
    public function hasFullAccess(): bool
    {
        return $this->uwezo === 'mkubwa';
    }
}