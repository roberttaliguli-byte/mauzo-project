<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Matumizi extends Model
{
    use HasFactory;

    protected $fillable = [
        'aina',
        'maelezo',
        'gharama',
        'company_id',
        'created_at' // Allow created_at to be fillable
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class);
    }
    
    // Accessor to get formatted date
    public function getFormattedDateAttribute()
    {
        return $this->created_at ? $this->created_at->format('d/m/Y') : '';
    }
    
    // Accessor to get formatted time
    public function getFormattedTimeAttribute()
    {
        return $this->created_at ? $this->created_at->format('H:i') : '';
    }
    // Add this to your Matumizi model
public function user()
{
    return $this->belongsTo(\App\Models\User::class);
}
}