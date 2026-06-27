<?php
// app/Http/Controllers/PublicShowcaseController.php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Bidhaa;
use App\Models\Order;
use App\Models\Mteja;
use App\Models\Mauzo;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class PublicShowcaseController extends Controller
{
    /**
     * Display the public showcase page for a company
     */
    public function show($identifier)
    {
        // Try to find company by ID or name
        $company = Company::where('id', $identifier)
            ->orWhere('company_name', 'LIKE', str_replace('-', ' ', $identifier))
            ->orWhere('company_name', 'LIKE', $identifier)
            ->first();
        
        if (!$company) {
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

        // Process product images - supports both filesystem and BLOB storage
        foreach ($products as $product) {
            $product->image_data_url = $this->getProductImageUrl($product);
            $product->has_image = $product->has_image; // Uses the accessor from model
        }

        // Get featured products
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

        // Check if customer is logged in via session
        $customer = null;
        $customerCode = session('customer_code_' . $company->id);
        
        if ($customerCode) {
            $customer = Mteja::where('company_id', $company->id)
                ->where('customer_code', $customerCode)
                ->first();
        }

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
            'whatsappNumber',
            'customer'
        ));
    }

    /**
     * Get product image as base64 data URL or file URL
     * Supports both filesystem storage and BLOB storage
     * 
     * @param \App\Models\Bidhaa $product
     * @return string|null
     */
    private function getProductImageUrl($product)
    {
        // 1. Check if product has image using the model's hasImage method
        if (!$product->has_image) {
            return null;
        }

        // 2. Try filesystem storage first (image_path)
        if ($product->image_path) {
            // Try direct file access
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
            
            // Try using Storage facade
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

        // 3. Fallback to BLOB storage (legacy)
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
     * Get business hours from company settings
     */
    private function getBusinessHours($company)
    {
        $defaultHours = [
            'monday' => ['open' => '08:00', 'close' => '18:00', 'closed' => false],
            'tuesday' => ['open' => '08:00', 'close' => '18:00', 'closed' => false],
            'wednesday' => ['open' => '08:00', 'close' => '18:00', 'closed' => false],
            'thursday' => ['open' => '08:00', 'close' => '18:00', 'closed' => false],
            'friday' => ['open' => '08:00', 'close' => '18:00', 'closed' => false],
            'saturday' => ['open' => '09:00', 'close' => '16:00', 'closed' => false],
            'sunday' => ['open' => '09:00', 'close' => '16:00', 'closed' => false],
        ];

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
            $product->has_image = $product->has_image;
        }

        return response()->json([
            'success' => true,
            'data' => $products,
            'count' => $products->count()
        ]);
    }

    /**
     * Get authenticated user (boss or employee)
     */
    private function getAuthUser()
    {
        // Check if user is logged in as boss
        if (auth()->check()) {
            $user = auth()->user();
            return [
                'type' => 'boss',
                'id' => $user->id,
                'name' => $user->name ?? 'Unknown',
                'role' => 'boss',
                'user_id' => $user->id,
                'mfanyakazi_id' => null
            ];
        }
        
        // Check if user is logged in as employee
        if (auth()->guard('mfanyakazi')->check()) {
            $employee = auth()->guard('mfanyakazi')->user();
            return [
                'type' => 'employee',
                'id' => $employee->id,
                'name' => $employee->jina ?? 'Employee',
                'role' => $employee->uwezo ?? 'mdogo',
                'user_id' => null,
                'mfanyakazi_id' => $employee->id
            ];
        }
        
        return null;
    }

    /**
     * Generate customer code from phone number
     */
    private function generateCustomerCodeFromPhone($phone, $companyId)
    {
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        $phoneCode = substr($cleanPhone, -6);
        $prefix = 'CUST';
        $companyCode = str_pad($companyId, 3, '0', STR_PAD_LEFT);
        
        $baseCode = $prefix . $companyCode . $phoneCode;
        $existing = Mteja::where('company_id', $companyId)
            ->where('customer_code', $baseCode)
            ->first();
        
        if ($existing) {
            $suffix = str_pad(rand(1, 99), 2, '0', STR_PAD_LEFT);
            return $prefix . $companyCode . $phoneCode . $suffix;
        }
        
        return $baseCode;
    }

    /**
     * Generate order number
     */
    private function generateOrderNumber($companyId)
    {
        $date = now()->format('Ymd');
        $prefix = "ORD-{$date}-";
        
        DB::beginTransaction();
        try {
            $lastOrder = Order::where('company_id', $companyId)
                ->where('order_number', 'like', $prefix . '%')
                ->lockForUpdate()
                ->orderBy('order_number', 'desc')
                ->first();
            
            if ($lastOrder && $lastOrder->order_number) {
                $lastNumber = intval(substr($lastOrder->order_number, strlen($prefix)));
                $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $newNumber = '0001';
            }
            
            DB::commit();
            return $prefix . $newNumber;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order number generation failed for company ' . $companyId . ': ' . $e->getMessage());
            return $prefix . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
        }
    }

    /**
     * Get status label
     */
    private function getStatusLabel($status)
    {
        $labels = [
            'saved' => 'Inasubiri',
            'confirmed' => 'Imethibitishwa',
            'processing' => 'Inachakatwa',
            'ready' => 'Tayari',
            'shipped' => 'Imesafirishwa',
            'delivered' => 'Imewasilishwa',
            'cancelled' => 'Imefutwa'
        ];
        return $labels[$status] ?? $status;
    }

    /**
     * Get status color
     */
    private function getStatusColor($status)
    {
        $colors = [
            'saved' => 'yellow',
            'confirmed' => 'blue',
            'processing' => 'purple',
            'ready' => 'indigo',
            'shipped' => 'orange',
            'delivered' => 'green',
            'cancelled' => 'red'
        ];
        return $colors[$status] ?? 'gray';
    }

    /**
     * Validate customer code and login
     */
    public function validateCustomer(Request $request, $companyId)
    {
        $company = Company::find($companyId);
        if (!$company) {
            return response()->json(['success' => false, 'message' => 'Company not found'], 404);
        }

        $code = $request->get('code');
        if (!$code) {
            return response()->json(['success' => false, 'message' => 'Customer code is required'], 400);
        }

        $customer = Mteja::where('company_id', $company->id)
            ->where('customer_code', $code)
            ->first();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid customer code. Please check and try again.'
            ], 404);
        }

        session(['customer_code_' . $company->id => $customer->customer_code]);
        session(['customer_id_' . $company->id => $customer->id]);

        return response()->json([
            'success' => true,
            'message' => 'Customer logged in successfully',
            'customer' => [
                'id' => $customer->id,
                'jina' => $customer->jina,
                'simu' => $customer->simu,
                'barua_pepe' => $customer->barua_pepe,
                'anapoishi' => $customer->anapoishi,
                'customer_code' => $customer->customer_code,
                'created_at' => $customer->created_at
            ]
        ]);
    }

    /**
     * Logout customer
     */
    public function logoutCustomer(Request $request, $companyId)
    {
        $company = Company::find($companyId);
        if (!$company) {
            return response()->json(['success' => false, 'message' => 'Company not found'], 404);
        }

        session()->forget('customer_code_' . $company->id);
        session()->forget('customer_id_' . $company->id);

        return response()->json([
            'success' => true,
            'message' => 'Customer logged out successfully'
        ]);
    }

    /**
     * Get customer orders
     */
    public function getCustomerOrders(Request $request, $companyId)
    {
        $company = Company::find($companyId);
        if (!$company) {
            return response()->json(['success' => false, 'message' => 'Company not found'], 404);
        }

        $customerId = $request->get('customer_id');
        if (!$customerId) {
            return response()->json(['success' => false, 'message' => 'Customer ID is required'], 400);
        }

        $customer = Mteja::where('company_id', $company->id)
            ->where('id', $customerId)
            ->first();

        if (!$customer) {
            return response()->json(['success' => false, 'message' => 'Customer not found'], 404);
        }

        $orders = Order::where('company_id', $company->id)
            ->where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'orders' => $orders->map(function($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'total' => $order->total,
                    'status' => $order->status,
                    'status_label' => $this->getStatusLabel($order->status),
                    'status_color' => $this->getStatusColor($order->status),
                    'created_at' => $order->created_at,
                    'created_at_formatted' => $order->created_at->format('d/m/Y H:i'),
                    'items_count' => count($order->items ?? []),
                    'items' => $order->items ?? [],
                    'order_type' => $order->order_type ?? 'delivery',
                    'customer_name' => $order->customer_name,
                    'customer_phone' => $order->customer_phone,
                    'delivery_address' => $order->delivery_address,
                    'special_instructions' => $order->special_instructions,
                ];
            })
        ]);
    }

    /**
     * Customer orders page
     */
    public function customerOrders($identifier)
    {
        $company = Company::where('id', $identifier)
            ->orWhere('company_name', 'LIKE', str_replace('-', ' ', $identifier))
            ->orWhere('company_name', 'LIKE', $identifier)
            ->first();

        if (!$company) {
            abort(404, 'Company not found');
        }

        $customerCode = session('customer_code_' . $company->id);
        $customer = null;
        
        if ($customerCode) {
            $customer = Mteja::where('company_id', $company->id)
                ->where('customer_code', $customerCode)
                ->first();
        }

        if (!$customer) {
            return redirect()->route('public.showcase', ['identifier' => $company->id])
                ->with('error', 'Please login with your customer code to view orders');
        }

        $orders = Order::where('company_id', $company->id)
            ->where('customer_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($orders as $order) {
            $order->status_label = $this->getStatusLabel($order->status);
            $order->status_color = $this->getStatusColor($order->status);
        }

        $stats = [
            'total' => $orders->count(),
            'saved' => $orders->where('status', 'saved')->count(),
            'confirmed' => $orders->where('status', 'confirmed')->count(),
            'processing' => $orders->where('status', 'processing')->count(),
            'ready' => $orders->where('status', 'ready')->count(),
            'shipped' => $orders->where('status', 'shipped')->count(),
            'delivered' => $orders->where('status', 'delivered')->count(),
            'cancelled' => $orders->where('status', 'cancelled')->count(),
        ];

        return view('customer-orders', compact(
            'company',
            'customer',
            'orders',
            'stats'
        ));
    }

    /**
     * Get customer orders via AJAX
     */
    public function getCustomerOrdersJson(Request $request, $companyId)
    {
        $company = Company::find($companyId);
        if (!$company) {
            return response()->json(['success' => false, 'message' => 'Company not found'], 404);
        }

        $customerCode = session('customer_code_' . $company->id);
        if (!$customerCode) {
            return response()->json(['success' => false, 'message' => 'Not logged in'], 401);
        }

        $customer = Mteja::where('company_id', $company->id)
            ->where('customer_code', $customerCode)
            ->first();

        if (!$customer) {
            return response()->json(['success' => false, 'message' => 'Customer not found'], 404);
        }

        $orders = Order::where('company_id', $company->id)
            ->where('customer_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'orders' => $orders->map(function($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'total' => $order->total,
                    'status' => $order->status,
                    'status_label' => $this->getStatusLabel($order->status),
                    'status_color' => $this->getStatusColor($order->status),
                    'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                    'created_at_formatted' => $order->created_at->format('d/m/Y H:i'),
                    'items_count' => count($order->items ?? []),
                    'items' => $order->items ?? [],
                    'order_type' => $order->order_type ?? 'delivery',
                    'customer_name' => $order->customer_name,
                    'customer_phone' => $order->customer_phone,
                    'delivery_address' => $order->delivery_address,
                    'special_instructions' => $order->special_instructions,
                ];
            })
        ]);
    }

    /**
     * Find customer by phone number
     */
    public function findCustomerByPhone(Request $request, $companyId)
    {
        $company = Company::find($companyId);
        if (!$company) {
            return response()->json(['success' => false, 'message' => 'Company not found'], 404);
        }

        $phone = $request->get('phone');
        if (!$phone) {
            return response()->json(['success' => false, 'message' => 'Phone number is required'], 400);
        }

        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);

        $customer = Mteja::where('company_id', $company->id)
            ->where('simu', $cleanPhone)
            ->first();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'No customer found with this phone number. Please register first.'
            ], 404);
        }

        $createdAt = 'N/A';
        if ($customer->created_at) {
            try {
                $createdAt = $customer->created_at->format('d/m/Y');
            } catch (\Exception $e) {
                $createdAt = 'N/A';
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Customer found!',
            'customer' => [
                'id' => $customer->id,
                'jina' => $customer->jina,
                'simu' => $customer->simu,
                'customer_code' => $customer->customer_code,
                'registered_from' => $customer->registered_from ?? 'boss',
                'created_at' => $createdAt,
            ]
        ]);
    }

    /**
     * Process order payment and create Mauzo records
     */
    private function processOrderPayment($order, $company, $authUser = null)
    {
        $items = $order->items ?? [];
        $companyId = $company->id;
        $orderNumber = $order->order_number;
        
        // Get user IDs for tracking
        $userId = null;
        $mfanyakaziId = null;
        $processorName = 'System';
        
        if ($authUser) {
            $userId = $authUser['user_id'] ?? null;
            $mfanyakaziId = $authUser['mfanyakazi_id'] ?? null;
            $processorName = $authUser['name'] ?? 'Unknown';
            $processorType = $authUser['type'] ?? 'system';
        }
        
        Log::info("Processing payment for order: {$orderNumber}", [
            'user_id' => $userId,
            'mfanyakazi_id' => $mfanyakaziId,
            'processor' => $processorName,
            'processor_type' => $processorType ?? 'unknown'
        ]);

        foreach ($items as $item) {
            $bidhaaId = $item['bidhaa_id'] ?? null;
            if (!$bidhaaId) {
                Log::warning("No bidhaa_id found in item", ['item' => $item]);
                continue;
            }

            $bidhaa = Bidhaa::find($bidhaaId);
            if (!$bidhaa) {
                Log::warning("Product not found: {$bidhaaId}");
                continue;
            }

            // Check if Mauzo already exists for this order item
            $existingMauzo = Mauzo::where('order_id', $order->id)
                ->where('bidhaa_id', $bidhaaId)
                ->first();

            if ($existingMauzo) {
                Log::info("Mauzo already exists for product: {$bidhaa->jina}", ['order_id' => $order->id]);
                continue;
            }

            // Calculate values
            $idadi = floatval($item['idadi'] ?? 0);
            $bei = floatval($item['bei'] ?? $item['price'] ?? 0);
            $punguzo = floatval($item['punguzo'] ?? 0);
            $punguzoAina = $item['punguzo_aina'] ?? 'bidhaa';
            $jumla = floatval($item['total'] ?? ($idadi * $bei));

            // Update stock
            $newStock = max(0, $bidhaa->idadi - $idadi);
            $bidhaa->update(['idadi' => $newStock]);

            // Create Mauzo record - with correct user/employee tracking
            $mauzoData = [
                'company_id' => $companyId,
                'order_id' => $order->id,
                'order_number' => $orderNumber,
                'bidhaa_id' => $bidhaaId,
                'mteja_id' => $order->customer_id,
                'user_id' => $userId, // NULL for employees
                'mfanyakazi_id' => $mfanyakaziId, // NULL for bosses
                'idadi' => $idadi,
                'bei' => $bei,
                'bei_type_used' => 'rejareja',
                'punguzo' => $punguzo,
                'punguzo_aina' => $punguzoAina,
                'jumla' => $jumla,
                'lipa_kwa' => 'cash',
                'lipa_kwa_type' => null,
                'receipt_no' => $orderNumber,
                'mauzo_ya' => 'order',
                'imeundwa_na' => $userId ?? $mfanyakaziId,
                'sale_date' => now()
            ];

            $mauzo = Mauzo::create($mauzoData);

            Log::info("Mauzo created for order: {$orderNumber}", [
                'bidhaa' => $bidhaa->jina,
                'idadi' => $idadi,
                'jumla' => $jumla,
                'processor' => $processorName,
                'processor_type' => $processorType ?? 'unknown',
                'user_id' => $userId,
                'mfanyakazi_id' => $mfanyakaziId,
                'mauzo_id' => $mauzo->id
            ]);

            // Log activity
            ActivityLog::create([
                'company_id' => $companyId,
                'activity_type' => 'mauzo',
                'description' => "Mauzo from order {$orderNumber} - {$bidhaa->jina} x {$idadi} (by {$processorName})",
                'user_name' => $processorName,
                'user_role' => $processorType ?? 'system',
                'amount' => $jumla,
            ]);
        }

        // Update order status to paid
        $order->status = 'paid';
        $order->paid_at = now();
        $order->save();

        Log::info("Order {$orderNumber} marked as paid by {$processorName}");
    }

    /**
     * Place order from showcase
     */
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
            'customer_code' => 'nullable|string',
            'status' => 'sometimes|in:saved,paid,confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Get authenticated user (boss or employee)
        $authUser = $this->getAuthUser();

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
                    'punguzo_aina' => 'bidhaa',
                    'subtotal' => floatval($product->bei_kuuza) * floatval($item['quantity']),
                    'total' => floatval($product->bei_kuuza) * floatval($item['quantity'])
                ];
            }

            // Generate order number
            $orderNumber = $this->generateOrderNumber($company->id);

            // ===== CUSTOMER HANDLING =====
            $customer = null;
            $cleanPhone = preg_replace('/[^0-9]/', '', $request->customer_phone);

            // 1. Check if customer exists by code
            if ($request->customer_code) {
                $customer = Mteja::where('company_id', $company->id)
                    ->where('customer_code', $request->customer_code)
                    ->first();
            }

            // 2. If not found by code, try by phone
            if (!$customer) {
                $customer = Mteja::where('company_id', $company->id)
                    ->where('simu', $cleanPhone)
                    ->first();
            }

            // 3. If still not found, create new customer
            if (!$customer) {
                $customerCode = $this->generateCustomerCodeFromPhone($cleanPhone, $company->id);
                
                $customer = Mteja::create([
                    'company_id' => $company->id,
                    'customer_code' => $customerCode,
                    'jina' => $request->customer_name,
                    'simu' => $cleanPhone,
                    'barua_pepe' => $request->customer_email,
                    'anapoishi' => $request->delivery_address,
                    'maelezo' => 'Registered via showcase order',
                    'registered_from' => 'showcase',
                ]);

                Log::info("New customer registered from showcase: {$customer->jina} ({$customer->customer_code}) for company {$company->id}");
            } else {
                // Update existing customer info
                $customer->update([
                    'jina' => $request->customer_name,
                    'barua_pepe' => $request->customer_email ?? $customer->barua_pepe,
                    'anapoishi' => $request->delivery_address ?? $customer->anapoishi,
                ]);
            }

            // Calculate totals
            $subtotal = floatval($request->subtotal);
            $total = floatval($request->total);
            $deliveryFee = floatval($request->delivery_fee ?? 0);
            $tax = 0;

            // Create order
            $orderStatus = $request->status ?? 'saved';
            
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
                'status' => $orderStatus,
                'order_type' => $request->order_type ?? 'delivery',
                'table_number' => $request->table_number ?? null,
                'special_instructions' => $request->special_instructions ?? null,
                'notes' => $request->special_instructions ?? null,
                'created_by' => $authUser ? $authUser['id'] : null
            ]);

            // If order is paid immediately, process payment
            if ($orderStatus === 'paid') {
                $this->processOrderPayment($order, $company, $authUser);
            }

            // Store customer in session
            session(['customer_code_' . $company->id => $customer->customer_code]);
            session(['customer_id_' . $company->id => $customer->id]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $orderStatus === 'paid' ? 'Order paid successfully!' : 'Order placed successfully!',
                'order' => $order,
                'order_number' => $orderNumber,
                'customer_code' => $customer->customer_code,
                'is_new_customer' => $customer->wasRecentlyCreated ?? false,
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

        return response()->json([
            'success' => true,
            'data' => [
                'order_number' => $order->order_number,
                'status' => $order->status,
                'status_label' => $this->getStatusLabel($order->status),
                'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                'total' => $order->total,
                'items' => count($order->items),
                'customer_name' => $order->customer_name
            ]
        ]);
    }

    /**
     * Find customer code by phone number (API endpoint for AJAX)
     */
    public function findCustomerCode(Request $request, $companyId)
    {
        $company = Company::find($companyId);
        if (!$company) {
            return response()->json(['success' => false, 'message' => 'Company not found'], 404);
        }

        $phone = $request->get('phone');
        if (!$phone) {
            return response()->json(['success' => false, 'message' => 'Phone number is required'], 400);
        }

        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        
        if (strlen($cleanPhone) < 7) {
            return response()->json(['success' => false, 'message' => 'Invalid phone number format'], 400);
        }

        $customer = Mteja::where('company_id', $company->id)
            ->where('simu', $cleanPhone)
            ->first();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'No customer found with this phone number. Please place an order first to register.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Customer found!',
            'customer' => [
                'id' => $customer->id,
                'jina' => $customer->jina,
                'simu' => $customer->simu,
                'customer_code' => $customer->customer_code,
                'registered_from' => $customer->registered_from ?? 'boss',
                'created_at' => $customer->created_at ? $customer->created_at->format('d/m/Y') : 'N/A',
            ]
        ]);
    }
}