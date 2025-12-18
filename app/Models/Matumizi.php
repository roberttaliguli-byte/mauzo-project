<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matumizi extends Model
{
    use HasFactory;

    protected $fillable = [
        'aina',
        'maelezo',
        'gharama',
        'company_id',
    ];

    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class);
    }
}
