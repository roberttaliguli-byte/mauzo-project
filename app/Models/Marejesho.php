<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marejesho extends Model
{
    use HasFactory;

    protected $fillable = [
        'madeni_id',
        'company_id',
        'kiasi',
        'tarehe',
    ];

    

    /**
     * 🧩 A rejesho belongs to one deni (madeni)
     */
    public function madeni()
    {
        return $this->belongsTo(Madeni::class, 'madeni_id');

        
    }

    /**
     * 🏢 Optional: A rejesho belongs to a company
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * 📦 Optional: Get the product (bidhaa) through the deni
     */
    public function bidhaa()
    {
        return $this->hasOneThrough(
            Bidhaa::class,
            Madeni::class,
            'id',         // Foreign key on madenis table
            'id',         // Foreign key on bidhaa table
            'madeni_id',  // Local key on marejeshos
            'bidhaa_id'   // Local key on madenis
        );
    }
}
