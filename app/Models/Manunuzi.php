<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Manunuzi extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'bidhaa_id',
        'idadi',
        'bei',
        'expiry',
        'saplaya',
        'simu',
        'mengineyo',
    ];

    /**
     * Relationship: Manunuzi belongs to one Bidhaa
     */
    public function bidhaa()
    {
        return $this->belongsTo(Bidhaa::class);
    }

    /**
     * Relationship: Manunuzi belongs to a specific company
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

}
