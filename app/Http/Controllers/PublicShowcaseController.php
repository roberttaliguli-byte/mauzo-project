<?php
// app/Http/Controllers/PublicShowcaseController.php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Bidhaa;
use App\Models\Order;
use App\Models\Mteja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PublicShowcaseController extends Controller
{
    /**
     * Display the public showcase page for a company
     * Single template for all companies
     */
    public function show($identifier)
    {
        // Try to find company by ID or name
        $company = Company::where('id', $identifier)
            ->orWhere('company_name', 'LIKE', str_replace('-', ' ', $identifier))
            ->orWhere('company_name', 'LIKE', $identifier)
            ->first();
        
        if (!$company) {
            // Try to find by slug (if you have slug field)
            $company = Company::where('slug', $identifier)->first();
        }
        
        if (!$company) {
            abort(404, 'Company not found. Please check the link and try again.');
        }

        // Get all products with stock > 0
        $products = Bidhaa::where('company_id', $company->id)
            ->where('idadi', '>', 0)
            ->orderBy('jina')
            ->get();

        // Process product images
        foreach ($products as $product) {
            $product->image_data_url = $this->getProductImageUrl($product);
            $product->has_image = !empty($product->image);
        }

        // Get featured products (products with highest stock or random)
        $featuredProducts = $products->sortByDesc('idadi')->take(8);

        // Get categories for filtering
        $categories = Bidhaa::where('company_id', $company->id)
            ->where('idadi', '>', 0)
            ->select('aina')
            ->distinct()
            ->pluck('aina')
            ->filter()
            ->values();

        // Get business hours from settings
        $businessHours = $this->getBusinessHours($company);

        // Get social media links
        $socialLinks = $this->getSocialLinks($company);

        // Generate unique session ID for this customer
        $sessionId = session()->getId();
        $customerToken = session('customer_token', Str::random(32));
        session(['customer_token' => $customerToken]);

        // Get company stats
        $stats = [
            'total_products' => $products->count(),
            'total_categories' => $categories->count(),
            'in_stock' => $products->where('idadi', '>', 10)->count(),
            'low_stock' => $products->where('idadi', '>', 0)->where('idadi', '<=', 10)->count()
        ];

        // Get company phone for WhatsApp
        $whatsappNumber = $company->phone ? preg_replace('/[^0-9]/', '', $company->phone) : null;

        return view('showcase', compact(
            'company',
            'products',
            'featuredProducts',
            'categories',
            'businessHours',
            'socialLinks',
            'sessionId',
            'customerToken',
            'stats',
            'whatsappNumber'
        ));
    }

    /**
     * Get product image as base64 data URL
     */
    private function getProductImageUrl($product)
    {
        if (empty($product->image)) {
            return null;
        }

        try {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_buffer($finfo, $product->image);
            finfo_close($finfo);
            return 'data:' . $mimeType . ';base64,' . base64_encode($product->image);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get business hours from company settings
     */
    private function getBusinessHours($company)
    {
        // Default business hours
        $defaultHours = [
            'monday' => ['open' => '08:00', 'close' => '18:00', 'closed' => false],
            'tuesday' => ['open' => '08:00', 'close' => '18:00', 'closed' => false],
            'wednesday' => ['open' => '08:00', 'close' => '18:00', 'closed' => false],
            'thursday' => ['open' => '08:00', 'close' => '18:00', 'closed' => false],
            'friday' => ['open' => '08:00', 'close' => '18:00', 'closed' => false],
            'saturday' => ['open' => '09:00', 'close' => '16:00', 'closed' => false],
            'sunday' => ['open' => '09:00', 'close' => '16:00', 'closed' => false],
        ];

        // Check if company has custom settings
        $settings = is_array($company->settings) ? $company->settings : json_decode($company->settings ?? '{}', true);
        $hours = $settings['business_hours'] ?? $defaultHours;

        return $hours;
    }

    /**
     * Get social media links
     */
    private function getSocialLinks($company)
    {
        $settings = is_array($company->settings) ? $company->settings : json_decode($company->settings ?? '{}', true);
        return [
            'facebook' => $settings['facebook'] ?? null,
            'instagram' => $settings['instagram'] ?? null,
            'twitter' => $settings['twitter'] ?? null,
            'youtube' => $settings['youtube'] ?? null,
            'tiktok' => $settings['tiktok'] ?? null,
            'whatsapp' => $settings['whatsapp'] ?? null,
        ];
    }

    /**
     * Search products via AJAX
     */
    public function searchProducts(Request $request, $companyId)
    {
        $company = Company::find($companyId);
        if (!$company) {
            return response()->json(['success' => false, 'message' => 'Company not found'], 404);
        }

        $search = $request->get('q', '');
        $category = $request->get('category', '');
        $minPrice = $request->get('min_price', 0);
        $maxPrice = $request->get('max_price', null);
        $sort = $request->get('sort', 'name');

        $query = Bidhaa::where('company_id', $company->id)
            ->where('idadi', '>', 0);

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('jina', 'LIKE', "%{$search}%")
                  ->orWhere('aina', 'LIKE', "%{$search}%")
                  ->orWhere('barcode', 'LIKE', "%{$search}%")
                  ->orWhere('kipimo', 'LIKE', "%{$search}%");
            });
        }

        if (!empty($category)) {
            $query->where('aina', $category);
        }

        if ($minPrice > 0) {
            $query->where('bei_kuuza', '>=', $minPrice);
        }

        if ($maxPrice && $maxPrice > 0) {
            $query->where('bei_kuuza', '<=', $maxPrice);
        }

        // Sorting
        switch ($sort) {
            case 'price_low':
                $query->orderBy('bei_kuuza', 'asc');
                break;
            case 'price_high':
                $query->orderBy('bei_kuuza', 'desc');
                break;
            case 'popular':
                $query->orderBy('idadi', 'desc');
                break;
            default:
                $query->orderBy('jina', 'asc');
        }

        $products = $query->get();

        foreach ($products as $product) {
            $product->image_data_url = $this->getProductImageUrl($product);
            $product->has_image = !empty($product->image);
        }

        return response()->json([
            'success' => true,
            'data' => $products,
            'count' => $products->count()
        ]);
    }

public function placeOrder(Request $request, $companyId)
{
    $company = Company::find($companyId);
    if (!$company) {
        return response()->json(['success' => false, 'message' => 'Company not found'], 404);
    }

    $validator = Validator::make($request->all(), [
        'items' => 'required|array|min:1',
        'items.*.product_id' => 'required|exists:bidhaas,id',
        'items.*.quantity' => 'required|numeric|min:0.01',
        'customer_name' => 'required|string|max:255',
        'customer_phone' => 'required|string|max:20',
        'customer_email' => 'nullable|email|max:255',
        'delivery_address' => 'nullable|string|max:500',
        'special_instructions' => 'nullable|string|max:500',
        'order_type' => 'required|in:delivery,pickup,dine_in',
        'subtotal' => 'required|numeric|min:0',
        'total' => 'required|numeric|min:0',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422);
    }

    DB::beginTransaction();
    try {
        // Verify stock for all items
        $itemsWithDetails = [];
        foreach ($request->items as $item) {
            $product = Bidhaa::find($item['product_id']);
            if (!$product) {
                throw new \Exception("Product not found: {$item['product_id']}");
            }
            if ($item['quantity'] > $product->idadi) {
                throw new \Exception("Insufficient stock for {$product->jina}. Available: {$product->idadi}, Requested: {$item['quantity']}");
            }

            $itemsWithDetails[] = [
                'bidhaa_id' => $product->id,
                'jina' => $product->jina,
                'aina' => $product->aina,
                'kipimo' => $product->kipimo,
                'idadi' => floatval($item['quantity']),
                'bei' => floatval($product->bei_kuuza),
                'punguzo' => 0,
                'subtotal' => floatval($product->bei_kuuza) * floatval($item['quantity']),
                'total' => floatval($product->bei_kuuza) * floatval($item['quantity'])
            ];
        }

        // Generate order number
        $orderNumber = $this->generateOrderNumber($company->id);

        // Create or find customer
        $customer = Mteja::where('company_id', $company->id)
            ->where('simu', $request->customer_phone)
            ->first();

        if (!$customer) {
            $customer = Mteja::create([
                'company_id' => $company->id,
                'jina' => $request->customer_name,
                'simu' => $request->customer_phone,
                'email' => $request->customer_email,
                'anwani' => $request->delivery_address ?? '',
                'aliyepo' => $request->delivery_address ?? '',
            ]);
        }

        // Calculate totals
        $subtotal = floatval($request->subtotal);
        $total = floatval($request->total);
        $deliveryFee = floatval($request->delivery_fee ?? 0);
        $tax = 0;

        // Create order - using only fields that exist
        $order = Order::create([
            'company_id' => $company->id,
            'order_number' => $orderNumber,
            'customer_id' => $customer->id,
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'customer_email' => $request->customer_email,
            'customer_address' => $request->delivery_address,
            'delivery_address' => $request->delivery_address,
            'items' => $itemsWithDetails,
            'subtotal' => $subtotal,
            'discount' => 0,
            'discount_type' => 'jumla',
            'delivery_fee' => $deliveryFee,
            'tax' => $tax,
            'total' => $total,
            'status' => 'saved',
            'order_type' => $request->order_type ?? 'delivery',
            'table_number' => $request->table_number ?? null,
            'special_instructions' => $request->special_instructions ?? null,
            'notes' => $request->special_instructions ?? null,
            'created_by' => null
        ]);

        DB::commit();

        // Send notification to company
        $this->notifyCompanyOfNewOrder($company, $order);

        return response()->json([
            'success' => true,
            'message' => 'Order placed successfully!',
            'order' => $order,
            'order_number' => $orderNumber
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Public order placement failed: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to place order: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Generate order number
     */
    private function generateOrderNumber($companyId)
    {
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
     * Notify company of new order
     */
    private function notifyCompanyOfNewOrder($company, $order)
    {
        Log::info("New order placed for company {$company->company_name}: Order #{$order->order_number}");
        // You can add:
        // - Email notification to company owner
        // - SMS notification
        // - Webhook to company's dashboard
        // - Real-time notification using Pusher/WebSockets
    }

    /**
     * Get order status
     */
    public function getOrderStatus(Request $request, $companyId)
    {
        $orderNumber = $request->get('order_number');
        if (!$orderNumber) {
            return response()->json(['success' => false, 'message' => 'Order number required'], 400);
        }

        $order = Order::where('company_id', $companyId)
            ->where('order_number', $orderNumber)
            ->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }

        $statusLabels = [
            'saved' => 'Pending',
            'confirmed' => 'Confirmed',
            'processing' => 'Processing',
            'ready' => 'Ready',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled'
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'order_number' => $order->order_number,
                'status' => $order->status,
                'status_label' => $statusLabels[$order->status] ?? $order->status,
                'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                'total' => $order->total,
                'items' => count($order->items),
                'customer_name' => $order->customer_name
            ]
        ]);
    }

    /**
     * Generate QR code for the showcase page
     */
    public function generateQR($companyId)
    {
        $company = Company::find($companyId);
        if (!$company) {
            return response()->json(['success' => false, 'message' => 'Company not found'], 404);
        }

        $url = route('public.showcase', ['identifier' => $company->id]);
        
        if (class_exists('SimpleSoftwareIO\QrCode\Facades\QrCode')) {
            $qrCode = QrCode::size(300)
                ->backgroundColor(255, 255, 255)
                ->color(245, 158, 11)
                ->generate($url);
            
            return response()->json([
                'success' => true,
                'qr_code' => $qrCode,
                'url' => $url
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'QR Code generation not available'
        ]);
    }
}
