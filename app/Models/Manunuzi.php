<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Manunuzi extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'batch_id', // 👈 ADDED THIS LINE
        'risiti_number',
        'bidhaa_id',
        'idadi',
        'bei',
        'bei_ya_awali',
        'unit_cost',
        'expiry',
        'saplaya',
        'simu',
        'chanzo',
        'mengineyo',
        'hali_ya_malipo',
        'kiasi_kilicholipwa',
        'deni_lililobaki',
        'created_by',
    ];

    protected $casts = [
        'idadi' => 'decimal:2',
        'bei' => 'decimal:2',
        'bei_ya_awali' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'kiasi_kilicholipwa' => 'decimal:2',
        'deni_lililobaki' => 'decimal:2',
        'expiry' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot method - auto-generate risiti number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($manunuzi) {
            // Auto-generate risiti number if not provided
            if (empty($manunuzi->risiti_number)) {
                $manunuzi->risiti_number = self::generateRisitiNumber($manunuzi->company_id);
            }

            // Set created_by if authenticated
            if (empty($manunuzi->created_by) && auth()->check()) {
                $manunuzi->created_by = auth()->id();
            }

            // Set initial balance based on payment status
            if ($manunuzi->hali_ya_malipo === 'imekamilika') {
                $manunuzi->kiasi_kilicholipwa = $manunuzi->bei;
                $manunuzi->deni_lililobaki = 0;
            } elseif ($manunuzi->hali_ya_malipo === 'deni') {
                $manunuzi->kiasi_kilicholipwa = 0;
                $manunuzi->deni_lililobaki = $manunuzi->bei;
            } elseif ($manunuzi->hali_ya_malipo === 'nusu') {
                $manunuzi->deni_lililobaki = $manunuzi->bei - $manunuzi->kiasi_kilicholipwa;
            }
        });
    }

    /**
     * Generate unique risiti number per company
     * Format: MN-YYYYMMDD-XXXX (e.g., MN-20241215-0001)
     */
    public static function generateRisitiNumber($companyId): string
    {
        $date = now()->format('Ymd');
        $prefix = "MN-{$date}-";

        // Get the last risiti number for this company today
        $lastRisiti = self::where('company_id', $companyId)
            ->where('risiti_number', 'LIKE', "{$prefix}%")
            ->orderBy('risiti_number', 'desc')
            ->first();

        if ($lastRisiti) {
            // Extract the sequence number and increment
            $lastSequence = (int) substr($lastRisiti->risiti_number, -4);
            $newSequence = $lastSequence + 1;
        } else {
            $newSequence = 1;
        }

        return $prefix . str_pad($newSequence, 4, '0', STR_PAD_LEFT);
    }

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

    /**
     * Relationship: Who created this entry
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get formatted idadi with decimal places
     */
    public function getFormattedIdadiAttribute(): string
    {
        return number_format($this->idadi, 2);
    }

    /**
     * Get formatted total cost
     */
    public function getFormattedBeiAttribute(): string
    {
        return number_format($this->bei, 2);
    }

    /**
     * Get formatted unit cost
     */
    public function getFormattedUnitCostAttribute(): string
    {
        return number_format($this->unit_cost, 2);
    }

    /**
     * Get formatted amount paid
     */
    public function getFormattedKiasiKilicholipwaAttribute(): string
    {
        return number_format($this->kiasi_kilicholipwa, 2);
    }

    /**
     * Get formatted remaining debt
     */
    public function getFormattedDeniLililobakiAttribute(): string
    {
        return number_format($this->deni_lililobaki, 2);
    }

    /**
     * Get payment status label in Swahili
     */
    public function getHaliYaMalipoLabelAttribute(): string
    {
        return match($this->hali_ya_malipo) {
            'imekamilika' => 'Imekamilika',
            'deni' => 'Deni',
            'nusu' => 'Malipo ya Nusu',
            default => 'Haijulikani',
        };
    }

    /**
     * Get payment status color class
     */
    public function getHaliYaMalipoColorAttribute(): string
    {
        return match($this->hali_ya_malipo) {
            'imekamilika' => 'text-green-600 bg-green-50',
            'deni' => 'text-red-600 bg-red-50',
            'nusu' => 'text-amber-600 bg-amber-50',
            default => 'text-gray-600 bg-gray-50',
        };
    }

    /**
     * Get source label in Swahili
     */
    public function getChanzoLabelAttribute(): string
    {
        return match($this->chanzo) {
            'saplaya' => 'Kutoka kwa Saplaya',
            'duka' => 'Kutoka Duka',
            'soko' => 'Kutoka Sokoni',
            'mengineyo' => 'Mengineyo',
            default => 'Haijulikani',
        };
    }

    /**
     * Check if this purchase has unpaid balance
     */
    public function getHasDeniAttribute(): bool
    {
        return $this->deni_lililobaki > 0;
    }

    /**
     * Check if this purchase is fully paid
     */
    public function getIsPaidAttribute(): bool
    {
        return $this->hali_ya_malipo === 'imekamilika' && $this->deni_lililobaki <= 0;
    }

    /**
     * Record a payment against this purchase
     */
    public function recordPayment(float $amount): bool
    {
        if ($amount <= 0 || $amount > $this->deni_lililobaki) {
            return false;
        }

        $newPaid = $this->kiasi_kilicholipwa + $amount;
        $newBalance = $this->bei - $newPaid;

        $this->update([
            'kiasi_kilicholipwa' => $newPaid,
            'deni_lililobaki' => max(0, $newBalance),
            'hali_ya_malipo' => $newBalance <= 0 ? 'imekamilika' : 'nusu',
        ]);

        return true;
    }

    /**
     * Scope: Filter by company
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope: Filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [
            $startDate . ' 00:00:00',
            $endDate . ' 23:59:59'
        ]);
    }

    /**
     * Scope: Filter by payment status
     */
    public function scopePaymentStatus($query, $status)
    {
        return $query->where('hali_ya_malipo', $status);
    }

    /**
     * Scope: Only unpaid purchases
     */
    public function scopeWithDeni($query)
    {
        return $query->where('deni_lililobaki', '>', 0);
    }

    /**
     * Scope: Search by risiti number or supplier
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('risiti_number', 'LIKE', "%{$term}%")
              ->orWhere('batch_id', 'LIKE', "%{$term}%") // 👈 ADDED THIS LINE FOR SEARCHING
              ->orWhere('saplaya', 'LIKE', "%{$term}%")
              ->orWhere('simu', 'LIKE', "%{$term}%")
              ->orWhereHas('bidhaa', function ($subQ) use ($term) {
                  $subQ->where('jina', 'LIKE', "%{$term}%")
                       ->orWhere('aina', 'LIKE', "%{$term}%");
              });
        });
    }
}