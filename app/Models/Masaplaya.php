<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Masaplaya extends Model
{
    use HasFactory;

    protected $fillable = [
        'jina',
        'simu',
        'barua_pepe',
        'anaopoishi',
        'ofisi',
        'maelezo',
    ];

    public function company()
{
    return $this->belongsTo(\App\Models\Company::class);
}

}
