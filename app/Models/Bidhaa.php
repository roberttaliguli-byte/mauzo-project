<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Marejesho;
use App\Models\Madeni;

class Bidhaa extends Model
{
    use HasFactory;

    // ✅ Explicitly link to the correct table
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
