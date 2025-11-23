<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mauzo extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'bidhaa_id',
        'madeni_id',          // ✅ ADD THIS
        'idadi',
        'bei',
        'punguzo',
        'jumla',
        'is_debt_repayment',  // ✅ ADD THIS
    ];

    /**
     * -----------------------------
     *  Relationship: Each sale belongs to a Bidhaa
     * -----------------------------
     */
    public function bidhaa()
    {
        return $this->belongsTo(Bidhaa::class);
    }

    /**
     * -----------------------------
     *  Relationship: Each sale belongs to a Company
     * -----------------------------
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * -----------------------------
     *  Accessor: Get total with discount applied
     * -----------------------------
     */
    public function getNetTotalAttribute()
    {
        return ($this->bei * $this->idadi) - ($this->punguzo ?? 0);
    }

}
