<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Bidhaa extends Model
{
    use HasFactory;

    protected $table = 'bidhaas';

    protected $fillable = [
        'jina',
        'aina',
        'kipimo',
        'idadi',
        'bei_nunua',
        'bei_kuuza',
        'bei_uzo_jumla',
        'bei_kiasi_cha_chaguo',
        'expiry',
        'barcode',
        'company_id',
        'image',        // Keep for backward compatibility
        'image_path',
        'image_mime_type',
        'image_size',
    ];

    protected $casts = [
        'idadi' => 'decimal:2',
        'bei_nunua' => 'decimal:2',
        'bei_kuuza' => 'decimal:2',
        'bei_uzo_jumla' => 'decimal:2',
        'expiry' => 'date:Y-m-d',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'image_size' => 'integer',
    ];

    protected $appends = [
        'image_url',
        'image_base64',
        'formatted_image_size',
        'has_image'
    ];

    /**
     * Get image URL (for filesystem storage)
     */
    public function getImageUrlAttribute()
    {
        if ($this->image_path) {
            return asset('storage/' . $this->image_path);
        }
        return null;
    }

    /**
     * Get image as base64 (for backward compatibility)
     */
    public function getImageBase64Attribute()
    {
        // Check filesystem first
        if ($this->image_path) {
            $path = storage_path('app/public/' . $this->image_path);
            if (file_exists($path)) {
                $content = file_get_contents($path);
                $mimeType = $this->image_mime_type ?: mime_content_type($path);
                return 'data:' . $mimeType . ';base64,' . base64_encode($content);
            }
        }
        
        // Fallback to BLOB for backward compatibility
        if ($this->image) {
            $mimeType = $this->getImageMimeType();
            return 'data:' . $mimeType . ';base64,' . base64_encode($this->image);
        }
        
        return null;
    }

    /**
     * Get formatted image size
     */
    public function getFormattedImageSizeAttribute()
    {
        if ($this->image_size) {
            $size = $this->image_size;
            if ($size < 1024) {
                return $size . ' B';
            } elseif ($size < 1024 * 1024) {
                return number_format($size / 1024, 2) . ' KB';
            } else {
                return number_format($size / (1024 * 1024), 2) . ' MB';
            }
        }
        return null;
    }

    /**
     * Check if product has image
     */
    public function getHasImageAttribute()
    {
        return !empty($this->image_path) || !empty($this->image);
    }

    /**
     * Legacy: Get image mime type (for backward compatibility)
     */
    private function getImageMimeType()
    {
        if (empty($this->image)) {
            return 'image/jpeg';
        }
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_buffer($finfo, $this->image);
        finfo_close($finfo);
        
        return $mimeType ?: 'image/jpeg';
    }

    /**
     * Check if product has image (legacy method)
     */
    public function hasImage()
    {
        return $this->has_image;
    }

    // Get image as base64 for display (legacy)
    public function getImageBase64LegacyAttribute()
    {
        if ($this->image) {
            $mimeType = $this->getImageMimeType();
            return 'data:' . $mimeType . ';base64,' . base64_encode($this->image);
        }
        return null;
    }

    public function getCurrentPriceAttribute(): float
    {
        if ($this->bei_kiasi_cha_chaguo === 'jumla' && $this->bei_uzo_jumla !== null) {
            return (float) $this->bei_uzo_jumla;
        }
        return (float) $this->bei_kuuza;
    }

    public function getFormattedCurrentPriceAttribute(): string
    {
        return number_format($this->current_price, 0) . ' TZS';
    }

    public function getPriceTypeLabelAttribute(): string
    {
        return $this->bei_kiasi_cha_chaguo === 'jumla' ? 'Jumla' : 'Rejareja';
    }

    public function getRetailPriceDisplayAttribute(): string
    {
        return number_format($this->bei_kuuza, 0) . ' TZS';
    }

    public function getWholesalePriceDisplayAttribute(): ?string
    {
        if ($this->bei_uzo_jumla !== null) {
            return number_format($this->bei_uzo_jumla, 0) . ' TZS';
        }
        return null;
    }

    public function getPricesDisplayAttribute(): string
    {
        $prices = "Rejareja: " . number_format($this->bei_kuuza, 0) . " TZS";
        if ($this->bei_uzo_jumla !== null) {
            $prices .= " | Jumla: " . number_format($this->bei_uzo_jumla, 0) . " TZS";
        }
        return $prices;
    }

    public function getFormattedIdadiAttribute(): string
    {
        return number_format($this->idadi, 2);
    }

    public function getIdadiAsFloatAttribute(): float
    {
        return (float) $this->idadi;
    }

    public function getIsOutOfStockAttribute(): bool
    {
        return $this->idadi <= 0;
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->idadi > 0 && $this->idadi < 10;
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->idadi <= 0) {
            return 'out_of_stock';
        }
        if ($this->idadi < 10) {
            return 'low_stock';
        }
        return 'in_stock';
    }

    public function manunuzi()
    {
        return $this->hasMany(Manunuzi::class);
    }

    public function mauzos()
    {
        return $this->hasMany(Mauzo::class, 'bidhaa_id');
    }

    public function madeni()
    {
        return $this->hasMany(Madeni::class);
    }

    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class);
    }

    public function histories()
    {
        return $this->hasMany(\App\Models\BidhaaHistory::class)->orderBy('created_at', 'desc');
    }

    public static function boot()
    {
        parent::boot();
        
        static::created(function ($bidhaa) {
            $bidhaa->recordHistory('ingizo', $bidhaa->idadi, 'Bidhaa mpya imeingizwa');
        });
        
        static::updated(function ($bidhaa) {
            if ($bidhaa->wasChanged('idadi')) {
                $oldIdadi = $bidhaa->getOriginal('idadi');
                $newIdadi = $bidhaa->idadi;
                
                if ($newIdadi > $oldIdadi) {
                    $bidhaa->recordHistory(
                        'ingizo', 
                        $newIdadi - $oldIdadi, 
                        'Idadi imeongezwa'
                    );
                } elseif ($newIdadi < $oldIdadi) {
                    $bidhaa->recordHistory(
                        'mauzo', 
                        $oldIdadi - $newIdadi, 
                        'Idadi imepungua (mauzo/marekebisho)'
                    );
                }
            }
        });

        // Delete associated records when product is deleted
        static::deleting(function ($bidhaa) {
            // Delete all Mauzo records associated with this product
            $bidhaa->mauzos()->delete();
            
            // Delete all Madeni records associated with this product
            $bidhaa->madeni()->delete();
            
            // Delete all Manunuzi records associated with this product
            $bidhaa->manunuzi()->delete();
            
            // Delete image file if exists
            if ($bidhaa->image_path) {
                Storage::disk('public')->delete($bidhaa->image_path);
            }
            
            // Log the deletion
            \Log::info("Product deleted: {$bidhaa->jina} (ID: {$bidhaa->id}) - All associated records deleted");
        });
    }

    public function recordHistory($type, $quantity, $description = null)
    {
        return $this->histories()->create([
            'company_id' => $this->company_id,
            'idadi_iliyoingizwa' => $type === 'ingizo' ? $quantity : 0,
            'idadi_iliyouzwa' => $type === 'mauzo' ? $quantity : 0,
            'idadi_iliyobaki' => $this->idadi,
            'bei_nunua' => $this->bei_nunua,
            'bei_kuuza' => $this->bei_kuuza,
            'bei_uzo_jumla' => $this->bei_uzo_jumla,
            'aina_ya_shughuli' => $type,
            'maelezo' => $description,
            'mtumiaji_id' => auth()->id(),
        ]);
    }
}