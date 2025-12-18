<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AinaYaMatumizi extends Model
{
    use HasFactory;

    protected $table = 'aina_za_matumizi'; // Specify the table name

    protected $fillable = [
        'jina',
        'maelezo',
        'rangi',
        'kategoria',
        'company_id'
    ];

    /**
     * Relationship with company
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Relationship with matumizi
     */
    public function matumizi()
    {
        return $this->hasMany(Matumizi::class, 'aina', 'jina');
    }
}