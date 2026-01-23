<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Madeni extends Model
{
    use HasFactory;

    protected $table = 'madenis';

protected $fillable = [
    'company_id',
    'bidhaa_id',
    'mteja_id',
    'idadi',
    'bei',
    'jumla',
    'punguzo',        // Add this
    'punguzo_aina',   // Add this
    'baki',
    'jina_mkopaji',
    'simu',
    'tarehe_malipo',
];
protected $casts = [
    'tarehe_malipo' => 'date',
];

    /**
     * Mahusiano (Relationships)
     */

    // 1️⃣ Kila deni linahusiana na kampuni moja
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // 2️⃣ Deni linahusu bidhaa fulani
    public function bidhaa()
    {
        return $this->belongsTo(Bidhaa::class);
    }

    // 3️⃣ Deni linahusu mteja (mkopaji)
    public function mteja()
    {
        return $this->belongsTo(Mteja::class);
    }

    // 4️⃣ Deni linaweza kuwa na marejesho mengi
    public function marejeshos()
    {
        return $this->hasMany(Marejesho::class, 'madeni_id');
    }
}
