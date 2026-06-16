<?php
// app/Http/Controllers/OrderController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Bidhaa;
use App\Models\Mteja;
use App\Models\Company;
use App\Models\Mauzo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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
     * Display the orders page
     */
    public function index(Request $request)
    {
        $user = $this->getAuthUser();
        if (!$user) {
            return redirect()->route('login')->withErrors(['Unauthorized access']);
        }
        $companyId = $user->company_id;

        // Get all products for the product grid
        $bidhaa = Bidhaa::where('company_id', $companyId)
            ->select('id', 'jina', 'bei_kuuza', 'bei_uzo_jumla', 'bei_nunua', 'idadi', 'barcode', 'aina', 'kipimo', 'image')
            ->orderBy('jina')
            ->get();

        // Process images for each product (convert BLOB to base64)
        foreach ($bidhaa as $product) {
            $product->image_data_url = null;
            $product->has_image = false;
            
            if ($product->image) {
                try {
                    // Detect mime type from binary data
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mimeType = finfo_buffer($finfo, $product->image);
                    finfo_close($finfo);
                    $product->image_data_url = 'data:' . $mimeType . ';base64,' . base64_encode($product->image);
                    $product->has_image = true;
                } catch (\Exception $e) {
                    $product->image_data_url = null;
                    $product->has_image = false;
                }
            }
        }

        // Get customers
        $wateja = Mteja::where('company_id', $companyId)->orderBy('jina')->get();

        // Get all orders
        $orders = Order::where('company_id', $companyId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get order statistics
        $orderStats = [
            'total' => Order::where('company_id', $companyId)->count(),
            'saved' => Order::where('company_id', $companyId)->where('status', 'saved')->count(),
            'confirmed' => Order::where('company_id', $companyId)->where('status', 'confirmed')->count(),
            'paid' => Order::where('company_id', $companyId)->where('status', 'paid')->count(),
            'cancelled' => Order::where('company_id', $companyId)->where('status', 'cancelled')->count()
        ];

        // Get company name
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
        
        // Filter by status
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }
        
        // Search by order number or customer name
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

        // Validate request
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
            foreach ($request->items as $item) {
                $bidhaa = Bidhaa::find($item['id']);
                if (!$bidhaa) {
                    throw new \Exception("Bidhaa not found: {$item['id']}");
                }
                if ($item['qty'] > $bidhaa->idadi) {
                    throw new \Exception("Insufficient stock for {$bidhaa->jina}. Available: {$bidhaa->idadi}, Requested: {$item['qty']}");
                }
            }

            // Generate order number
            $orderNumber = $this->generateOrderNumber();

            // Prepare items with details
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

            // Create order
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

            // If status is paid, update stock and create Mauzo records
            if ($request->status === 'paid') {
                foreach ($request->items as $item) {
                    $bidhaa = Bidhaa::find($item['id']);
                    if ($bidhaa) {
                        // Update stock
                        $newStock = max(0, $bidhaa->idadi - floatval($item['qty']));
                        $bidhaa->update(['idadi' => $newStock]);

                        // Create Mauzo record (for reporting)
                        Mauzo::create([
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
                    }
                }
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
            $order->status = $request->status;
            
            // If paying a saved/confirmed order
            if ($request->status === 'paid' && $oldStatus !== 'paid') {
                // Update stock and create Mauzo records for each item
                foreach ($order->items as $item) {
                    $bidhaa = Bidhaa::find($item['bidhaa_id']);
                    if ($bidhaa) {
                        // Check stock
                        if ($bidhaa->idadi < $item['idadi']) {
                            throw new \Exception("Insufficient stock for {$bidhaa->jina}. Available: {$bidhaa->idadi}, Requested: {$item['idadi']}");
                        }
                        // Update stock
                        $newStock = max(0, $bidhaa->idadi - floatval($item['idadi']));
                        $bidhaa->update(['idadi' => $newStock]);

                        // Create Mauzo record
                        Mauzo::create([
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
                    }
                }
            }
            
            // If cancelling a paid order, restore stock
            if ($request->status === 'cancelled' && $oldStatus === 'paid') {
                foreach ($order->items as $item) {
                    $bidhaa = Bidhaa::find($item['bidhaa_id']);
                    if ($bidhaa) {
                        $newStock = $bidhaa->idadi + floatval($item['idadi']);
                        $bidhaa->update(['idadi' => $newStock]);
                    }
                }
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
            
            $order->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order deleted successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order deletion failed: ' . $e->getMessage());
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
}