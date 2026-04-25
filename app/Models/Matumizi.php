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
    // Example for Mauzo model
public function user()
{
    return $this->belongsTo(\App\Models\User::class, 'user_id'); // adjust foreign key if needed
}
}
