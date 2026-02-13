<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bidhaa extends Model
{
    use HasFactory;

    protected $table = 'bidhaas';

    protected $fillable = [
        'jina',
        'aina',
        'kipimo',
        'idadi',
        'bei_nunua',
        'bei_kuuza',
        'expiry',
        'barcode',
        'company_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'expiry' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Uhusiano na Manunuzi
     */
    public function manunuzi()
    {
        return $this->hasMany(Manunuzi::class);
    }

    /**
     * Uhusiano na Mauzo
     */
    public function mauzos()
    {
        return $this->hasMany(Mauzo::class, 'bidhaa_id');
    }

    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class);
    }
}