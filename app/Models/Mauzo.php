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
        'madeni_id',
        'receipt_no',
        'idadi',
        'bei',
        'punguzo',
        'punguzo_aina', // ✅ ADD THIS LINE
        'jumla',
        'is_debt_repayment',
        'reprint_count', // ✅ Also add this if not already
    ];

    protected $casts = [
        'bei' => 'decimal:2',
        'punguzo' => 'decimal:2',
        'jumla' => 'decimal:2',
        'reprint_count' => 'integer'
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
     *  Accessor: Get total with discount applied correctly
     * -----------------------------
     */
    public function getNetTotalAttribute()
    {
        $subtotal = $this->bei * $this->idadi;
        
        if (!$this->punguzo) {
            return $subtotal;
        }
        
        // Calculate discount based on type
        if ($this->punguzo_aina === 'bidhaa') {
            // Discount per item
            $totalDiscount = $this->punguzo * $this->idadi;
        } else {
            // Fixed discount amount
            $totalDiscount = $this->punguzo;
        }
        
        return $subtotal - $totalDiscount;
    }

    /**
     * -----------------------------
     *  Accessor: Get total discount (not per item)
     * -----------------------------
     */
    public function getTotalDiscountAttribute()
    {
        if (!$this->punguzo) {
            return 0;
        }
        
        if ($this->punguzo_aina === 'bidhaa') {
            // Discount per item
            return $this->punguzo * $this->idadi;
        }
        
        // Fixed discount amount
        return $this->punguzo;
    }
}