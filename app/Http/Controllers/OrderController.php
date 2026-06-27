<?php
// app/Http/Controllers/OrderController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Bidhaa;
use App\Models\Mteja;
use App\Models\Company;
use App\Models\Mauzo;
use App\Helpers\ActivityHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    private function getAuthUser()
    {
        if (Auth::guard('admin')->check()) {
            return Auth::guard('admin')->user();
        }
        if (Auth::guard('web')->check()) {
            return Auth::guard('web')->user();
        }
        if (Auth::guard('mfanyakazi')->check()) {
            return Auth::guard('mfanyakazi')->user();
        }
        return null;
    }

    private function getCompanyId()
    {
        $user = $this->getAuthUser();
        return $user ? $user->company_id : null;
    }

    private function getUserId()
    {
        $user = $this->getAuthUser();
        return $user ? $user->id : null;
    }

    private function getCompanyName()
    {
        $companyId = $this->getCompanyId();
        $company = Company::find($companyId);
        return $company ? $company->company_name : 'Mauzo Sheet';
    }

    private function getProcessorName()
    {
        $user = $this->getAuthUser();
        if (!$user) return 'System';
        
        if (Auth::guard('web')->check()) {
            return $user->name ?? $user->username ?? 'Boss';
        }
        if (Auth::guard('mfanyakazi')->check()) {
            return $user->jina ?? 'Employee';
        }
        return 'System';
    }

    private function getProcessorType()
    {
        if (Auth::guard('web')->check()) return 'Boss';
        if (Auth::guard('mfanyakazi')->check()) return 'Employee';
        return 'System';
    }

    public function getProducts(Request $request)
    {
        $companyId = $this->getCompanyId();
        
        $products = Bidhaa::where('company_id', $companyId)
            ->where('idadi', '>', 0)
            ->select('id', 'jina', 'bei_kuuza', 'bei_uzo_jumla', 'idadi', 'aina', 'kipimo', 'image', 'image_path', 'image_mime_type', 'image_size')
            ->orderBy('jina')
            ->get();
        
        foreach ($products as $product) {
            $product->image_data_url = $this->getProductImageUrl($product);
            $product->has_image = $product->has_image;
        }
        
        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }
    
    private function generateOrderNumber()
    {
        $companyId = $this->getCompanyId();
        $date = now()->format('Ymd');
        $prefix = "ORD-{$date}-";
        $lastOrder = Order::where('company_id', $companyId)
            ->where('order_number', 'like', $prefix . '%')
            ->orderBy('order_number', 'desc')
            ->first();
        if ($lastOrder) {
            $lastNumber = intval(substr($lastOrder->order_number, strlen($prefix)));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        return $prefix . $newNumber;
    }

    /**
     * Get product by barcode
     */
    public function getProductByBarcode($barcode)
    {
        $companyId = $this->getCompanyId();
        
        $product = Bidhaa::where('barcode', $barcode)
            ->where('company_id', $companyId)
            ->first();
        
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Bidhaa haipatikani kwa barcode hii'
            ], 404);
        }
        
        $product->image_data_url = $this->getProductImageUrl($product);
        $product->has_image = $product->has_image;
        
        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    /**
     * Search customers
     */
    public function searchCustomers(Request $request)
    {
        $companyId = $this->getCompanyId();
        $search = $request->get('q', '');
        
        $customers = Mteja::where('company_id', $companyId)
            ->where(function($query) use ($search) {
                $query->where('jina', 'LIKE', "%{$search}%")
                      ->orWhere('simu', 'LIKE', "%{$search}%")
                      ->orWhere('customer_code', 'LIKE', "%{$search}%");
            })
            ->orderBy('jina')
            ->limit(20)
            ->get(['id', 'jina', 'simu', 'customer_code']);
        
        return response()->json([
            'success' => true,
            'data' => $customers
        ]);
    }

    private function getProductImageUrl($product)
    {
        if (!$product->has_image) {
            return null;
        }

        if ($product->image_path) {
            $path = storage_path('app/public/' . $product->image_path);
            if (file_exists($path)) {
                try {
                    $content = file_get_contents($path);
                    $mimeType = $product->image_mime_type ?: mime_content_type($path);
                    return 'data:' . $mimeType . ';base64,' . base64_encode($content);
                } catch (\Exception $e) {
                    Log::warning('Failed to read image from filesystem: ' . $e->getMessage());
                }
            }
            
            if (Storage::disk('public')->exists($product->image_path)) {
                try {
                    $content = Storage::disk('public')->get($product->image_path);
                    $mimeType = $product->image_mime_type ?: Storage::disk('public')->mimeType($product->image_path);
                    return 'data:' . $mimeType . ';base64,' . base64_encode($content);
                } catch (\Exception $e) {
                    Log::warning('Failed to read image from storage: ' . $e->getMessage());
                }
            }
        }

        if (!empty($product->image)) {
            try {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_buffer($finfo, $product->image);
                finfo_close($finfo);
                return 'data:' . $mimeType . ';base64,' . base64_encode($product->image);
            } catch (\Exception $e) {
                Log::warning('Failed to read image from BLOB: ' . $e->getMessage());
            }
        }

        return null;
    }

    /**
     * Display the orders page
     */
    public function index(Request $request)
    {
        $user = $this->getAuthUser();
        if (!$user) {
            return redirect()->route('login')->withErrors(['Unauthorized access']);
        }
        $companyId = $user->company_id;

        $bidhaa = Bidhaa::where('company_id', $companyId)
            ->select('id', 'jina', 'bei_kuuza', 'bei_uzo_jumla', 'bei_nunua', 'idadi', 'barcode', 'aina', 'kipimo', 'image', 'image_path', 'image_mime_type', 'image_size')
            ->orderBy('jina')
            ->get();

        $imageCount = 0;
        foreach ($bidhaa as $product) {
            $product->image_data_url = $this->getProductImageUrl($product);
            $product->has_image = $product->has_image;
            
            if ($product->has_image) {
                $imageCount++;
                Log::info("Order: Product {$product->id} - {$product->jina} has image");
            }
        }

        Log::info("Order page loaded: Total products: {$bidhaa->count()}, Products with images: {$imageCount}");

        $wateja = Mteja::where('company_id', $companyId)->orderBy('jina')->get();

        $orders = Order::where('company_id', $companyId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $orderStats = [
            'total' => Order::where('company_id', $companyId)->count(),
            'saved' => Order::where('company_id', $companyId)->where('status', 'saved')->count(),
            'confirmed' => Order::where('company_id', $companyId)->where('status', 'confirmed')->count(),
            'paid' => Order::where('company_id', $companyId)->where('status', 'paid')->count(),
            'cancelled' => Order::where('company_id', $companyId)->where('status', 'cancelled')->count()
        ];

        $companyName = $this->getCompanyName();

        return view('mauzo.index', compact('bidhaa', 'wateja', 'orders', 'orderStats', 'companyName'));
    }

    /**
     * Get all placed orders
     */
    public function getPlacedOrders(Request $request)
    {
        $companyId = $this->getCompanyId();
        
        $query = Order::where('company_id', $companyId);
        
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'LIKE', "%{$search}%")
                  ->orWhere('customer_name', 'LIKE', "%{$search}%")
                  ->orWhere('customer_phone', 'LIKE', "%{$search}%");
            });
        }
        
        $orders = $query->orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    /**
     * Store a new order (both save and pay)
     */
    public function store(Request $request)
    {
        $user = $this->getAuthUser();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access'], 401);
        }
        $companyId = $user->company_id;
        $processorName = $this->getProcessorName();
        $processorType = $this->getProcessorType();

        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:bidhaas,id',
            'items.*.name' => 'required|string',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.total' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'status' => 'required|in:saved,confirmed,paid',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Check stock for all items
            $itemDetails = [];
            foreach ($request->items as $item) {
                $bidhaa = Bidhaa::find($item['id']);
                if (!$bidhaa) {
                    throw new \Exception("Bidhaa not found: {$item['id']}");
                }
                if ($item['qty'] > $bidhaa->idadi) {
                    throw new \Exception("Insufficient stock for {$bidhaa->jina}. Available: {$bidhaa->idadi}, Requested: {$item['qty']}");
                }
                $itemDetails[] = [
                    'bidhaa' => $bidhaa,
                    'qty' => $item['qty']
                ];
            }

            $orderNumber = $this->generateOrderNumber();

            $itemsWithDetails = [];
            foreach ($request->items as $item) {
                $bidhaa = Bidhaa::find($item['id']);
                $itemsWithDetails[] = [
                    'bidhaa_id' => $item['id'],
                    'jina' => $item['name'],
                    'aina' => $bidhaa->aina ?? '',
                    'kipimo' => $bidhaa->kipimo ?? '',
                    'idadi' => floatval($item['qty']),
                    'bei' => floatval($item['price']),
                    'punguzo' => 0,
                    'subtotal' => floatval($item['total']),
                    'total' => floatval($item['total'])
                ];
            }

            $order = Order::create([
                'company_id' => $companyId,
                'order_number' => $orderNumber,
                'customer_id' => null,
                'customer_name' => $request->customer_name ?? 'Walk-in Customer',
                'customer_phone' => $request->customer_phone ?? '',
                'items' => $itemsWithDetails,
                'subtotal' => floatval($request->subtotal),
                'discount' => floatval($request->discount ?? 0),
                'discount_type' => 'jumla',
                'total' => floatval($request->total),
                'status' => $request->status,
                'notes' => null,
                'created_by' => $this->getUserId()
            ]);

            // LOG: Order created
            ActivityHelper::log(
                'order_created',
                "Order {$orderNumber} imeundwa na {$processorName} ({$processorType}) - Jumla: " . number_format($order->total, 0) . " TZS",
                $order,
                $order->total
            );

            // If status is paid, update stock and create Mauzo records
            if ($request->status === 'paid') {
                foreach ($request->items as $item) {
                    $bidhaa = Bidhaa::find($item['id']);
                    if ($bidhaa) {
                        $newStock = max(0, $bidhaa->idadi - floatval($item['qty']));
                        $bidhaa->update(['idadi' => $newStock]);

                        $mauzo = Mauzo::create([
                            'company_id' => $companyId,
                            'bidhaa_id' => $item['id'],
                            'mteja_id' => null,
                            'idadi' => floatval($item['qty']),
                            'bei' => floatval($item['price']),
                            'punguzo' => 0,
                            'punguzo_aina' => 'bidhaa',
                            'jumla' => floatval($item['total']),
                            'lipa_kwa' => 'cash',
                            'receipt_no' => $orderNumber,
                            'mauzo_ya' => 'order',
                            'imeundwa_na' => $this->getUserId()
                        ]);

                        // LOG: Sale from order
                        ActivityHelper::logSale($mauzo, $bidhaa->jina, $mauzo->jumla);
                    }
                }

                // LOG: Order paid
                ActivityHelper::log(
                    'order_paid',
                    "Order {$orderNumber} imelipiwa na {$processorName} ({$processorType}) - Jumla: " . number_format($order->total, 0) . " TZS",
                    $order,
                    $order->total
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $request->status === 'paid' ? 'Order paid and generated successfully!' : 'Order saved successfully!',
                'order' => $order,
                'order_number' => $orderNumber
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order creation failed: ' . $e->getMessage());
            
            // LOG: Error
            ActivityHelper::log(
                'order_error',
                "Hitilafu katika kuunda order: " . $e->getMessage() . " (by {$processorName})",
                null,
                null
            );
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to process order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a single order
     */
    public function show($id)
    {
        $companyId = $this->getCompanyId();
        $order = Order::where('company_id', $companyId)->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

    /**
     * Update order status (pay order)
     */
    public function updateStatus(Request $request, $id)
    {
        $user = $this->getAuthUser();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access'], 401);
        }
        $companyId = $user->company_id;
        $order = Order::where('company_id', $companyId)->findOrFail($id);
        $processorName = $this->getProcessorName();
        $processorType = $this->getProcessorType();
        
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:saved,confirmed,paid,cancelled'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $oldStatus = $order->status;
            $newStatus = $request->status;
            $order->status = $newStatus;
            
            // LOG: Status change
            ActivityHelper::log(
                'order_status_change',
                "Order {$order->order_number} imebadilishwa kutoka {$oldStatus} hadi {$newStatus} na {$processorName} ({$processorType})",
                $order,
                $order->total
            );
            
            // If paying a saved/confirmed order
            if ($newStatus === 'paid' && $oldStatus !== 'paid') {
                foreach ($order->items as $item) {
                    $bidhaa = Bidhaa::find($item['bidhaa_id']);
                    if ($bidhaa) {
                        if ($bidhaa->idadi < $item['idadi']) {
                            throw new \Exception("Insufficient stock for {$bidhaa->jina}. Available: {$bidhaa->idadi}, Requested: {$item['idadi']}");
                        }
                        $newStock = max(0, $bidhaa->idadi - floatval($item['idadi']));
                        $bidhaa->update(['idadi' => $newStock]);

                        $mauzo = Mauzo::create([
                            'company_id' => $companyId,
                            'bidhaa_id' => $item['bidhaa_id'],
                            'mteja_id' => $order->customer_id,
                            'idadi' => floatval($item['idadi']),
                            'bei' => floatval($item['bei']),
                            'punguzo' => 0,
                            'punguzo_aina' => 'bidhaa',
                            'jumla' => floatval($item['total']),
                            'lipa_kwa' => 'cash',
                            'receipt_no' => $order->order_number,
                            'mauzo_ya' => 'order',
                            'imeundwa_na' => $this->getUserId()
                        ]);

                        ActivityHelper::logSale($mauzo, $bidhaa->jina, $mauzo->jumla);
                    }
                }

                // LOG: Order paid
                ActivityHelper::log(
                    'order_paid',
                    "Order {$order->order_number} imelipiwa na {$processorName} ({$processorType}) - Jumla: " . number_format($order->total, 0) . " TZS",
                    $order,
                    $order->total
                );
            }
            
            // If cancelling a paid order, restore stock
            if ($newStatus === 'cancelled' && $oldStatus === 'paid') {
                foreach ($order->items as $item) {
                    $bidhaa = Bidhaa::find($item['bidhaa_id']);
                    if ($bidhaa) {
                        $newStock = $bidhaa->idadi + floatval($item['idadi']);
                        $bidhaa->update(['idadi' => $newStock]);
                    }
                }
                
                ActivityHelper::log(
                    'order_cancelled',
                    "Order {$order->order_number} imefutwa (cancelled) na {$processorName} ({$processorType}) - Stock imerejeshwa",
                    $order,
                    $order->total
                );
            }
            
            $order->save();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully!',
                'data' => $order
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order status update failed: ' . $e->getMessage());
            
            ActivityHelper::log(
                'order_error',
                "Hitilafu katika kubadilisha hali ya order {$order->order_number}: " . $e->getMessage() . " (by {$processorName})",
                $order,
                null
            );
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete an order
     */
    public function destroy($id)
    {
        $user = $this->getAuthUser();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access'], 401);
        }
        
        $companyId = $user->company_id;
        $order = Order::where('company_id', $companyId)->findOrFail($id);
        $processorName = $this->getProcessorName();
        $processorType = $this->getProcessorType();
        $orderNumber = $order->order_number;
        $orderTotal = $order->total;
        
        DB::beginTransaction();
        try {
            // Restore stock if order was paid
            if ($order->status === 'paid') {
                foreach ($order->items as $item) {
                    $bidhaa = Bidhaa::find($item['bidhaa_id']);
                    if ($bidhaa) {
                        $newStock = $bidhaa->idadi + floatval($item['idadi']);
                        $bidhaa->update(['idadi' => $newStock]);
                    }
                }
            }
            
            // LOG before deletion
            ActivityHelper::log(
                'order_deleted',
                "Order {$orderNumber} imefutwa na {$processorName} ({$processorType}) - Jumla: " . number_format($orderTotal, 0) . " TZS" . ($order->status === 'paid' ? " (Stock imerejeshwa)" : ""),
                $order,
                $orderTotal
            );
            
            $order->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order deleted successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order deletion failed: ' . $e->getMessage());
            
            ActivityHelper::log(
                'order_error',
                "Hitilafu katika kufuta order {$orderNumber}: " . $e->getMessage() . " (by {$processorName})",
                null,
                null
            );
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get order statistics
     */
    public function getStats()
    {
        $companyId = $this->getCompanyId();
        
        $stats = [
            'total' => Order::where('company_id', $companyId)->count(),
            'saved' => Order::where('company_id', $companyId)->where('status', 'saved')->count(),
            'confirmed' => Order::where('company_id', $companyId)->where('status', 'confirmed')->count(),
            'paid' => Order::where('company_id', $companyId)->where('status', 'paid')->count(),
            'cancelled' => Order::where('company_id', $companyId)->where('status', 'cancelled')->count(),
            'total_revenue' => Order::where('company_id', $companyId)->where('status', 'paid')->sum('total')
        ];
        
        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Generate invoice for order
     */
    public function generateInvoice($id)
    {
        $companyId = $this->getCompanyId();
        $order = Order::where('company_id', $companyId)->findOrFail($id);
        
        $company = Company::find($companyId);
        
        // Log invoice generation
        ActivityHelper::log(
            'order_invoice',
            "Invoice imechapishwa kwa order {$order->order_number} na " . $this->getProcessorName(),
            $order,
            $order->total
        );
        
        $pdf = PDF::loadView('orders.invoice', compact('order', 'company'));
        
        return $pdf->download("invoice-{$order->order_number}.pdf");
    }

    /**
     * Share order via WhatsApp
     */
    public function shareWhatsApp($id)
    {
        $companyId = $this->getCompanyId();
        $order = Order::where('company_id', $companyId)->findOrFail($id);
        
        ActivityHelper::log(
            'order_shared',
            "Order {$order->order_number} imeshirikiwa kwa WhatsApp na " . $this->getProcessorName(),
            $order,
            $order->total
        );
        
        $message = $this->formatOrderForWhatsApp($order);
        $phone = $order->customer_phone ? preg_replace('/[^0-9]/', '', $order->customer_phone) : '';
        
        if ($phone) {
            return redirect("https://wa.me/{$phone}?text=" . urlencode($message));
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Hakuna namba ya simu ya mteja'
        ]);
    }

    /**
     * Format order for WhatsApp
     */
    private function formatOrderForWhatsApp($order)
    {
        $text = "*ORDER {$order->order_number}*\n\n";
        $text .= "Mteja: {$order->customer_name}\n";
        $text .= "Tarehe: " . $order->created_at->format('d/m/Y H:i') . "\n";
        $text .= "Hali: " . ucfirst($order->status) . "\n";
        $text .= "-----------------------------------\n";
        
        if ($order->items) {
            foreach ($order->items as $item) {
                $text .= "{$item['jina']} x {$item['idadi']} = " . number_format($item['total'], 0) . " TZS\n";
            }
        }
        
        $text .= "-----------------------------------\n";
        $text .= "*JUMLA: " . number_format($order->total, 0) . " TZS*\n";
        $text .= "\nAsante kwa kununua!";
        
        return $text;
    }
}