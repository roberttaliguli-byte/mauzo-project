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
        'lipa_kwa',
        'lipa_kwa_type',
        'user_id',        // ✅ Add this - for boss who recorded
        'mfanyakazi_id'   // ✅ Add this - for employee who recorded
    ];

    protected $casts = [
        'tarehe' => 'date',
        'created_at' => 'datetime'
    ];

    /**
     * 🧩 A rejesho belongs to one deni (madeni)
     */
    public function madeni()
    {
        return $this->belongsTo(Madeni::class, 'madeni_id');
    }

    /**
     * 👤 User (boss) who recorded this repayment
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * 👤 Employee who recorded this repayment
     */
    public function mfanyakazi()
    {
        return $this->belongsTo(Wafanyakazi::class, 'mfanyakazi_id');
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
    
    /**
     * 🏷️ Get user name who recorded this repayment
     */
    public function getRecordedByAttribute()
    {
        if ($this->user) {
            return $this->user->name ?? $this->user->username;
        }
        if ($this->mfanyakazi) {
            return $this->mfanyakazi->jina;
        }
        return 'System';
    }
}