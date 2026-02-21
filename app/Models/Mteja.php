<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mteja extends Model
{
    use HasFactory;

    protected $fillable = [
        'jina',
        'simu',
        'barua_pepe',
        'anapoishi',
        'maelezo',
        'company_id', // Add this line
    ];
}