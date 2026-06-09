<?php
// app/Models/Order.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'order_number', 'customer_id', 'customer_name', 
        'customer_phone', 'customer_email', 'customer_address', 'items', 
        'subtotal', 'discount', 'discount_type', 'total', 'status', 
        'payment_method', 'payment_type', 'notes', 'created_by', 
        'paid_at', 'sale_id', 'transferred_to_cart', 'transferred_at', 'payment_reference'
    ];

    protected $casts = [
        'items' => 'array',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_at' => 'datetime',
        'transferred_at' => 'datetime',
        'created_at' => 'datetime'
    ];

    const STATUS_SAVED = 'saved';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_PAID = 'paid';
    const STATUS_CANCELLED = 'cancelled';

    const STATUS_CONFIG = [
        'saved' => ['label' => 'Saved', 'color' => 'gray', 'bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'icon' => 'fa-save'],
        'confirmed' => ['label' => 'Confirmed', 'color' => 'blue', 'bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'icon' => 'fa-check-circle'],
        'paid' => ['label' => 'Paid', 'color' => 'green', 'bg' => 'bg-green-100', 'text' => 'text-green-700', 'icon' => 'fa-money-bill-wave'],
        'cancelled' => ['label' => 'Cancelled', 'color' => 'red', 'bg' => 'bg-red-100', 'text' => 'text-red-700', 'icon' => 'fa-times-circle']
    ];

    public function company() { return $this->belongsTo(Company::class); }
    public function customer() { return $this->belongsTo(Mteja::class, 'customer_id'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function sale() { return $this->belongsTo(Mauzo::class, 'sale_id'); }

    public function getStatusLabelAttribute() { return self::STATUS_CONFIG[$this->status]['label'] ?? $this->status; }
    public function getStatusBgAttribute() { return self::STATUS_CONFIG[$this->status]['bg'] ?? 'bg-gray-100'; }
    public function getStatusTextAttribute() { return self::STATUS_CONFIG[$this->status]['text'] ?? 'text-gray-700'; }
    public function getStatusIconAttribute() { return self::STATUS_CONFIG[$this->status]['icon'] ?? 'fa-circle'; }
}