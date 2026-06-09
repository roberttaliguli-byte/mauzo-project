<?php
// app/Services/OrderService.php
namespace App\Services;

use App\Models\Order;
use App\Models\Bidhaa;
use App\Models\Mauzo;
use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderService
{
    protected $companyId;

    public function __construct()
    {
        $this->companyId = $this->getCompanyId();
    }

    private function getCompanyId()
    {
        if (Auth::guard('mfanyakazi')->check()) {
            return Auth::guard('mfanyakazi')->user()->company_id;
        } elseif (Auth::guard('web')->check()) {
            return Auth::guard('web')->user()->company_id;
        }
        return null;
    }

    private function getUserId()
    {
        if (Auth::guard('web')->check()) return Auth::guard('web')->id();
        if (Auth::guard('mfanyakazi')->check()) {
            $user = Auth::guard('mfanyakazi')->user();
            return $user->user_id ?? $user->id;
        }
        return 1;
    }

    public function createOrder(array $data)
    {
        DB::beginTransaction();
        try {
            $subtotal = 0;
            $itemsWithDetails = [];
            
            foreach ($data['items'] as $item) {
                $bidhaa = Bidhaa::find($item['bidhaa_id']);
                $itemSubtotal = $item['bei'] * $item['idadi'];
                $itemDiscount = $item['punguzo'] ?? 0;
                $itemTotal = $itemSubtotal - $itemDiscount;
                $subtotal += $itemTotal;
                
                $itemsWithDetails[] = [
                    'bidhaa_id' => $item['bidhaa_id'],
                    'jina' => $bidhaa->jina,
                    'aina' => $bidhaa->aina ?? '',
                    'kipimo' => $bidhaa->kipimo ?? '',
                    'idadi' => $item['idadi'],
                    'bei' => $item['bei'],
                    'punguzo' => $item['punguzo'] ?? 0,
                    'subtotal' => $itemSubtotal,
                    'total' => $itemTotal
                ];
            }

            $discount = $data['discount'] ?? 0;
            if ($discount > $subtotal) $discount = $subtotal;
            $total = $subtotal - $discount;

            $orderNumber = $this->generateOrderNumber();

            $order = Order::create([
                'company_id' => $this->companyId,
                'order_number' => $orderNumber,
                'customer_id' => $data['customer_id'] ?? null,
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'] ?? '',
                'customer_email' => $data['customer_email'] ?? '',
                'customer_address' => $data['customer_address'] ?? '',
                'items' => $itemsWithDetails,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'discount_type' => 'jumla',
                'total' => $total,
                'status' => $data['status'],
                'notes' => $data['notes'] ?? null,
                'created_by' => $this->getUserId()
            ]);

            DB::commit();
            return ['success' => true, 'order' => $order];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order creation failed: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function updateStatus($orderId, $newStatus)
    {
        $order = Order::where('company_id', $this->companyId)->findOrFail($orderId);
        
        DB::beginTransaction();
        try {
            $oldStatus = $order->status;
            $order->status = $newStatus;
            
            if ($newStatus === 'paid' && !$order->paid_at) {
                $order->paid_at = now();
            }
            
            $order->save();
            
            // If order is paid, automatically transfer to cart
            if ($newStatus === 'paid' && $oldStatus !== 'paid') {
                $this->transferToCart($order);
            }
            
            DB::commit();
            return ['success' => true, 'order' => $order];

        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function transferToCart(Order $order)
    {
        if ($order->transferred_to_cart) {
            return ['success' => false, 'message' => 'Order already transferred to cart'];
        }

        $cartItems = [];
        foreach ($order->items as $item) {
            $cartItems[] = [
                'jina' => $item['jina'],
                'bei' => $item['bei'],
                'idadi' => $item['idadi'],
                'punguzo' => $item['punguzo'],
                'punguzo_aina' => 'bidhaa',
                'actual_discount' => $item['punguzo'] * $item['idadi'],
                'jumla' => $item['total'],
                'bidhaa_id' => $item['bidhaa_id'],
                'timestamp' => now()->toISOString(),
                'company_id' => $this->companyId
            ];
        }

        $order->transferred_to_cart = true;
        $order->transferred_at = now();
        $order->save();

        return ['success' => true, 'items' => $cartItems];
    }

    private function generateOrderNumber()
    {
        $prefix = 'ORD-' . date('Ymd');
        $lastOrder = Order::where('order_number', 'like', $prefix . '%')
            ->orderBy('order_number', 'desc')->first();
        
        if ($lastOrder) {
            $lastNumber = intval(substr($lastOrder->order_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return $prefix . '-' . $newNumber;
    }
}