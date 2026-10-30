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
        'batch_id',
        'risiti_number',
        'bidhaa_id',
        'new_bidhaa',              // NEW — snapshot of a product that doesn't exist yet
        'idadi',
        'bei',
        'bei_ya_awali',
        'unit_cost',
        'bei_kuuza_draft',         // NEW — selling price captured while drafting
        'expiry',
        'saplaya',
        'simu',
        'chanzo',
        'mengineyo',
        'hali_ya_malipo',
        'kiasi_kilicholipwa',
        'deni_lililobaki',
        'created_by',
        // Draft fields
        'status',
        'draft_token',
        'draft_owner_id',
        'draft_updated_at',
    ];

    protected $casts = [
        'idadi' => 'decimal:2',
        'bei' => 'decimal:2',
        'bei_ya_awali' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'bei_kuuza_draft' => 'decimal:2',   // NEW
        'new_bidhaa' => 'array',            // NEW — JSON <-> PHP array
        'kiasi_kilicholipwa' => 'decimal:2',
        'deni_lililobaki' => 'decimal:2',
        'expiry' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'draft_updated_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($manunuzi) {
            // Auto-generate risiti number if not provided
            if (empty($manunuzi->risiti_number)) {
                $manunuzi->risiti_number = self::generateRisitiNumber($manunuzi->company_id);
            }

            if (empty($manunuzi->created_by) && auth()->check()) {
                $manunuzi->created_by = auth()->id();
            }

            if ($manunuzi->hali_ya_malipo === 'imekamilika') {
                $manunuzi->kiasi_kilicholipwa = $manunuzi->bei;
                $manunuzi->deni_lililobaki = 0;
            } elseif ($manunuzi->hali_ya_malipo === 'deni') {
                $manunuzi->kiasi_kilicholipwa = 0;
                $manunuzi->deni_lililobaki = $manunuzi->bei;
            } elseif ($manunuzi->hali_ya_malipo === 'nusu') {
                $manunuzi->deni_lililobaki = $manunuzi->bei - $manunuzi->kiasi_kilicholipwa;
            }

            // Draft defaults
            if (empty($manunuzi->status)) {
                $manunuzi->status = 'final';
            }
        });
    }

    public static function generateRisitiNumber($companyId): string
    {
        $date = now()->format('Ymd');
        $prefix = "MN-{$date}-";

        $lastRisiti = self::where('company_id', $companyId)
            ->where('risiti_number', 'LIKE', "{$prefix}%")
            ->orderBy('risiti_number', 'desc')
            ->first();

        $newSequence = $lastRisiti ? ((int) substr($lastRisiti->risiti_number, -4)) + 1 : 1;

        return $prefix . str_pad($newSequence, 4, '0', STR_PAD_LEFT);
    }

    // ============ RELATIONSHIPS ============

    public function bidhaa()
    {
        return $this->belongsTo(Bidhaa::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ============ SCOPES ============

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [
            $startDate . ' 00:00:00',
            $endDate . ' 23:59:59',
        ]);
    }

    public function scopePaymentStatus($query, $status)
    {
        return $query->where('hali_ya_malipo', $status);
    }

    public function scopeWithDeni($query)
    {
        return $query->where('deni_lililobaki', '>', 0);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('risiti_number', 'LIKE', "%{$term}%")
              ->orWhere('batch_id', 'LIKE', "%{$term}%")
              ->orWhere('saplaya', 'LIKE', "%{$term}%")
              ->orWhere('simu', 'LIKE', "%{$term}%")
              ->orWhereHas('bidhaa', function ($subQ) use ($term) {
                  $subQ->where('jina', 'LIKE', "%{$term}%")
                       ->orWhere('aina', 'LIKE', "%{$term}%");
              });
        });
    }

    /** Only finalized purchases (exclude drafts) */
    public function scopeFinal($query)
    {
        return $query->where('status', 'final');
    }

    /** Only drafts */
    public function scopeDrafts($query)
    {
        return $query->where('status', 'draft');
    }

    /** Drafts for a specific company + owner */
    public function scopeDraftsFor($query, $companyId, $ownerId)
    {
        return $query->where('company_id', $companyId)
                     ->where('status', 'draft')
                     ->where('draft_owner_id', $ownerId);
    }

    // ============ ACCESSORS ============

    public function getFormattedIdadiAttribute(): string
    {
        return number_format($this->idadi, 2);
    }

    public function getFormattedBeiAttribute(): string
    {
        return number_format($this->bei, 2);
    }

    public function getFormattedUnitCostAttribute(): string
    {
        return number_format($this->unit_cost, 2);
    }

    public function getFormattedKiasiKilicholipwaAttribute(): string
    {
        return number_format($this->kiasi_kilicholipwa, 2);
    }

    public function getFormattedDeniLililobakiAttribute(): string
    {
        return number_format($this->deni_lililobaki, 2);
    }

    public function getHaliYaMalipoLabelAttribute(): string
    {
        return match($this->hali_ya_malipo) {
            'imekamilika' => 'Imekamilika',
            'deni' => 'Deni',
            'nusu' => 'Malipo ya Nusu',
            default => 'Haijulikani',
        };
    }

    public function getHaliYaMalipoColorAttribute(): string
    {
        return match($this->hali_ya_malipo) {
            'imekamilika' => 'text-green-600 bg-green-50',
            'deni' => 'text-red-600 bg-red-50',
            'nusu' => 'text-amber-600 bg-amber-50',
            default => 'text-gray-600 bg-gray-50',
        };
    }

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

    public function getHasDeniAttribute(): bool
    {
        return $this->deni_lililobaki > 0;
    }

    public function getIsPaidAttribute(): bool
    {
        return $this->hali_ya_malipo === 'imekamilika' && $this->deni_lililobaki <= 0;
    }

    public function getIsDraftAttribute(): bool
    {
        return $this->status === 'draft';
    }

    public function getIsFinalAttribute(): bool
    {
        return $this->status === 'final';
    }

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
}