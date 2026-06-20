<?php
// app/Models/UzalishajiGharama.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UzalishajiGharama extends Model
{
    use HasFactory;

    protected $table = 'uzalishaji_gharama';

    protected $fillable = [
        'uzalishaji_id',
        'jina',
        'kundi',
        'kiasi',
        'gharama',
        'company_id'
    ];

    protected $casts = [
        'kiasi' => 'decimal:2',
        'gharama' => 'decimal:2',
    ];

    public function uzalishaji()
    {
        return $this->belongsTo(Uzalishaji::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Category system
    public static function getCategories()
    {
        return [
            'malighafi' => ['label' => 'Malighafi', 'icon' => 'fa-boxes', 'color' => 'amber'],
            'mishahara' => ['label' => 'Mishahara', 'icon' => 'fa-users', 'color' => 'blue'],
            'usafiri' => ['label' => 'Usafiri', 'icon' => 'fa-truck', 'color' => 'green'],
            'ufungashaji' => ['label' => 'Ufungashaji', 'icon' => 'fa-box', 'color' => 'purple'],
            'umeme_na_nishati' => ['label' => 'Nishati', 'icon' => 'fa-bolt', 'color' => 'yellow'],
            'gharama_nyingine' => ['label' => 'Nyingine', 'icon' => 'fa-ellipsis-h', 'color' => 'gray']
        ];
    }

    public static function getCategoryLabel($kundi)
    {
        $categories = self::getCategories();
        return $categories[$kundi]['label'] ?? $kundi;
    }

    public static function getCategoryColor($kundi)
    {
        $categories = self::getCategories();
        return $categories[$kundi]['color'] ?? 'gray';
    }

    public static function getCategoryIcon($kundi)
    {
        $categories = self::getCategories();
        return $categories[$kundi]['icon'] ?? 'fa-circle';
    }
}