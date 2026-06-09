<?php
// app/Http/Controllers/OrderController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Mteja;
use App\Models\Bidhaa;
use App\Models\Mauzo;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    protected function getCompanyId()
    {
        if (Auth::guard('mfanyakazi')->check()) {
            return Auth::guard('mfanyakazi')->user()->company_id;
        } elseif (Auth::guard('web')->check()) {
            return Auth::guard('web')->user()->company_id;
        }
        return null;
    }
    
    protected function getUserId()
    {
        if (Auth::guard('web')->check()) {
            return Auth::guard('web')->id();
        }
        if (Auth::guard('mfanyakazi')->check()) {
            $user = Auth::guard('mfanyakazi')->user();
            return $user->user_id ?? $user->id;
        }
        return 1;
    }

// Add this method to your OrderController
public function create()
{
    $companyId = $this->getCompanyId();
    
    // Get products (bidhaa) for the order form
    $bidhaa = Bidhaa::where('company_id', $companyId)
        ->orderBy('jina')
        ->get(['id', 'jina', 'bei_kuuza', 'aina', 'kipimo', 'idadi']);
    
    // Get customers (wateja) for customer search
    $wateja = Mteja::where('company_id', $companyId)
        ->orderBy('jina')
        ->get(['id', 'jina', 'simu', 'barua_pepe', 'anapoishi']);
    
    // Return the view with both variables
    return view('mauzo.index', compact('bidhaa', 'wateja'));
}

    public function index(Request $request)
    {
        $companyId = $this->getCompanyId();
        if (!$companyId) {
            return response()->json(['success' => false, 'message' => 'Company not found'], 400);
        }
        
        $query = Order::where('company_id', $companyId);

        if ($request->status && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        if ($request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);
        
        $stats = [
            'saved' => Order::where('company_id', $companyId)->where('status', 'saved')->count(),
            'confirmed' => Order::where('company_id', $companyId)->where('status', 'confirmed')->count(),
            'paid' => Order::where('company_id', $companyId)->where('status', 'paid')->count(),
            'cancelled' => Order::where('company_id', $companyId)->where('status', 'cancelled')->count(),
            'total_revenue' => Order::where('company_id', $companyId)->where('status', 'paid')->sum('total'),
            'pending_revenue' => Order::where('company_id', $companyId)->whereIn('status', ['saved', 'confirmed'])->sum('total')
        ];

        return response()->json([
            'success' => true,
            'orders' => $orders,
            'stats' => $stats
        ]);
    }

    public function show($id)
    {
        $companyId = $this->getCompanyId();
        $order = Order::where('company_id', $companyId)->findOrFail($id);
        return response()->json(['success' => true, 'order' => $order]);
    }

    public function store(Request $request)
    {
        $companyId = $this->getCompanyId();
        if (!$companyId) {
            return response()->json(['success' => false, 'message' => 'Company not found'], 400);
        }
        
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.bidhaa_id' => 'required|exists:bidhaas,id',
            'items.*.idadi' => 'required|numeric|min:0.01',
            'items.*.bei' => 'required|numeric|min:0',
            'customer_name' => 'required|string|max:255',
            'status' => 'required|in:saved,confirmed'
        ]);

        DB::beginTransaction();
        try {
            $subtotal = 0;
            $itemsWithDetails = [];
            
            foreach ($validated['items'] as $item) {
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

            $discount = $validated['discount'] ?? 0;
            if ($discount > $subtotal) $discount = $subtotal;
            $total = $subtotal - $discount;

            $orderNumber = 'ORD-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            while (Order::where('order_number', $orderNumber)->exists()) {
                $orderNumber = 'ORD-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            }

            $order = Order::create([
                'company_id' => $companyId,
                'order_number' => $orderNumber,
                'customer_id' => $validated['customer_id'] ?? null,
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'] ?? '',
                'customer_email' => $validated['customer_email'] ?? '',
                'customer_address' => $validated['customer_address'] ?? '',
                'items' => $itemsWithDetails,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'discount_type' => 'jumla',
                'total' => $total,
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
                'created_by' => $this->getUserId()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $validated['status'] === 'confirmed' ? 'Oda imethibitishwa kikamilifu!' : 'Oda imehifadhiwa kama rasimu!',
                'order' => $order
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Kuna tatizo: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $companyId = $this->getCompanyId();
        $order = Order::where('company_id', $companyId)->findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'required|in:saved,confirmed,paid,cancelled'
        ]);

        DB::beginTransaction();
        try {
            $order->status = $validated['status'];
            if ($validated['status'] === 'paid' && !$order->paid_at) {
                $order->paid_at = now();
            }
            $order->save();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $validated['status'] === 'confirmed' ? 'Oda imethibitishwa! Inaweza kutumwa kwenye Kikapu.' : 'Status imebadilishwa!',
                'order' => $order
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Kuna tatizo katika kubadilisha status'
            ], 500);
        }
    }

    public function destroy($id)
    {
        $companyId = $this->getCompanyId();
        $order = Order::where('company_id', $companyId)->findOrFail($id);
        
        if (!in_array($order->status, ['saved', 'cancelled'])) {
            return response()->json([
                'success' => false,
                'message' => 'Huwezi kufuta oda iliyothibitishwa au kulipwa!'
            ], 400);
        }

        $order->delete();
        return response()->json([
            'success' => true,
            'message' => 'Oda imefutwa kikamilifu!'
        ]);
    }

public function sendToKikapu($id)
{
    $companyId = $this->getCompanyId();
    $order = Order::where('company_id', $companyId)
        ->where('status', 'confirmed')
        ->findOrFail($id);

    // Format items for cart exactly like Mauzo expects
    $cartItems = [];
    foreach ($order->items as $item) {
        $bidhaa = Bidhaa::find($item['bidhaa_id']);
        $cartItems[] = [
            'bidhaa_id' => $item['bidhaa_id'],
            'jina' => $item['jina'],
            'bei' => $item['bei'],
            'idadi' => $item['idadi'],
            'punguzo' => $item['punguzo'],
            'punguzo_aina' => 'bidhaa',
            'actual_discount' => $item['punguzo'] * $item['idadi'],
            'jumla' => $item['total'],
            'barcode' => $bidhaa ? $bidhaa->barcode : '',
            'bei_type' => 'rejareja',
            'timestamp' => now()->toISOString(),
            'company_id' => $companyId,
            'company_name' => optional(Company::find($companyId))->company_name ?? ''
        ];
    }

    return response()->json([
        'success' => true,
        'message' => 'Oda imetumwa kwenye Kikapu!',
        'items' => $cartItems,
        'order_number' => $order->order_number,
        'customer' => [
            'id' => $order->customer_id,
            'name' => $order->customer_name,
            'phone' => $order->customer_phone
        ]
    ]);
}

    public function generateInvoice($id)
    {
        $companyId = $this->getCompanyId();
        $order = Order::where('company_id', $companyId)->findOrFail($id);
        $company = Company::find($companyId);
        
        $html = $this->generateInvoiceHtml($order, $company);
        return response($html)->header('Content-Type', 'text/html');
    }

    private function generateInvoiceHtml($order, $company)
    {
        $statusText = $order->status == 'saved' ? 'Imehifadhiwa' : ($order->status == 'confirmed' ? 'Imethibitishwa' : ($order->status == 'paid' ? 'Imelipwa' : 'Imefutwa'));
        
        $itemsHtml = '';
        foreach ($order->items as $index => $item) {
            $itemsHtml .= '
            <tr>
                <td style="border:1px solid #ddd;padding:8px;">' . ($index + 1) . '</td>
                <td style="border:1px solid #ddd;padding:8px;">' . htmlspecialchars($item['jina']) . '</td>
                <td style="border:1px solid #ddd;padding:8px;text-align:right;">' . number_format($item['idadi'], 2) . '</td>
                <td style="border:1px solid #ddd;padding:8px;text-align:right;">' . number_format($item['bei'], 0) . ' TZS</td>
                <td style="border:1px solid #ddd;padding:8px;text-align:right;">' . number_format($item['punguzo'] ?? 0, 0) . ' TZS</td>
                <td style="border:1px solid #ddd;padding:8px;text-align:right;">' . number_format($item['total'], 0) . ' TZS</td>
            </tr>';
        }
        
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Invoice #' . $order->order_number . '</title>
            <style>
                body { font-family: Arial, sans-serif; font-size: 12px; padding: 20px; }
                .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
                .company-name { font-size: 18px; font-weight: bold; }
                .order-number { font-size: 14px; font-weight: bold; margin: 10px 0; }
                .info-section { margin-bottom: 20px; }
                .info-row { margin-bottom: 5px; }
                table { width: 100%; border-collapse: collapse; margin: 15px 0; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f5f5f5; }
                .text-right { text-align: right; }
                .total-section { margin-top: 20px; text-align: right; }
                .footer { text-align: center; margin-top: 30px; border-top: 1px solid #ddd; padding-top: 10px; font-size: 10px; }
                @media print { body { padding: 0; } .no-print { display: none; } }
            </style>
        </head>
        <body>
            <div class="header">
                <div class="company-name">' . htmlspecialchars($company->company_name ?? 'MAUZO SYSTEM') . '</div>
                <div>TAARIFA YA ODA</div>
                <div class="order-number">' . $order->order_number . '</div>
            </div>
            
            <div class="info-section">
                <div class="info-row"><strong>Tarehe:</strong> ' . $order->created_at->format('d/m/Y H:i') . '</div>
                <div class="info-row"><strong>Mteja:</strong> ' . htmlspecialchars($order->customer_name ?? '-') . '</div>
                <div class="info-row"><strong>Simu:</strong> ' . htmlspecialchars($order->customer_phone ?? '-') . '</div>
                <div class="info-row"><strong>Hali:</strong> ' . $statusText . '</div>
            </div>
            
            <table>
                <thead>
                    <tr><th>#</th><th>Bidhaa</th><th class="text-right">Idadi</th><th class="text-right">Bei</th><th class="text-right">Punguzo</th><th class="text-right">Jumla</th></tr>
                </thead>
                <tbody>' . $itemsHtml . '</tbody>
            </table>
            
            <div class="total-section">
                <div><strong>Jumla Ndogo:</strong> ' . number_format($order->subtotal, 0) . ' TZS</div>
                ' . ($order->discount > 0 ? '<div><strong>Punguzo:</strong> -' . number_format($order->discount, 0) . ' TZS</div>' : '') . '
                <div style="font-size:16px;margin-top:10px;"><strong>JUMLA:</strong> ' . number_format($order->total, 0) . ' TZS</div>
            </div>
            
            ' . ($order->notes ? '<div style="margin-top:20px;"><strong>Maelezo:</strong> ' . htmlspecialchars($order->notes) . '</div>' : '') . '
            
            <div class="footer">
                <div>Asante kwa kununua!</div>
                <div>Imechapishwa: ' . now()->format('d/m/Y H:i') . '</div>
            </div>
            
            <div class="no-print" style="text-align:center;margin-top:20px;">
                <button onclick="window.print()" style="padding:8px 16px;margin:0 5px;">Chapisha</button>
                <button onclick="window.close()" style="padding:8px 16px;margin:0 5px;">Funga</button>
            </div>
            <script>setTimeout(function(){ window.print(); }, 500);<\/script>
        </body>
        </html>';
    }

    public function shareWhatsApp($id)
    {
        $companyId = $this->getCompanyId();
        $order = Order::where('company_id', $companyId)->findOrFail($id);
        
        $statusText = $order->status == 'saved' ? 'Imehifadhiwa' : ($order->status == 'confirmed' ? 'Imethibitishwa' : ($order->status == 'paid' ? 'Imelipwa' : 'Imefutwa'));
        
        $message = "🏪 *ODA DETAILS*\n";
        $message .= "━━━━━━━━━━━━━━━\n";
        $message .= "*Oda No:* {$order->order_number}\n";
        $message .= "*Tarehe:* " . $order->created_at->format('d/m/Y H:i') . "\n";
        $message .= "*Mteja:* {$order->customer_name}\n";
        $message .= "*Simu:* {$order->customer_phone}\n";
        $message .= "*Status:* {$statusText}\n";
        $message .= "━━━━━━━━━━━━━━━\n";
        $message .= "*BIDHAA*\n";
        
        foreach ($order->items as $index => $item) {
            $num = $index + 1;
            $message .= "{$num}. {$item['jina']}\n";
            $message .= "   → {$item['idadi']} x " . number_format($item['bei'], 0) . " = " . number_format($item['total'], 0) . "/=\n";
        }
        
        $message .= "━━━━━━━━━━━━━━━\n";
        $message .= "*Jumla:* " . number_format($order->total, 0) . "/=\n";
        $message .= "━━━━━━━━━━━━━━━\n";
        $message .= "Asante kwa kununua!\n";
        
        $encodedMessage = urlencode($message);
        $phoneNumber = $order->customer_phone;
        if ($phoneNumber && !str_starts_with($phoneNumber, '255')) {
            if (str_starts_with($phoneNumber, '0')) {
                $phoneNumber = '255' . substr($phoneNumber, 1);
            } elseif (strlen($phoneNumber) === 9) {
                $phoneNumber = '255' . $phoneNumber;
            }
        }
        
        $whatsappUrl = $phoneNumber 
            ? "https://wa.me/{$phoneNumber}?text={$encodedMessage}"
            : "https://wa.me/?text={$encodedMessage}";

        return response()->json([
            'success' => true,
            'whatsapp_url' => $whatsappUrl,
            'message' => $message
        ]);
    }
}