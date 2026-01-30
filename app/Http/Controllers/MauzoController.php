<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Mauzo;
use App\Models\Bidhaa;
use App\Models\Matumizi;
use App\Models\Madeni;
use App\Models\Mteja;
use App\Models\Marejesho;
use Carbon\Carbon;

class MauzoController extends Controller
{
    // Helper method to get authenticated user from any guard
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
    
    // Helper method to get company ID
    private function getCompanyId()
    {
        $user = $this->getAuthUser();
        return $user ? $user->company_id : null;
    }

    // Generate receipt number - ALWAYS generate
    private function generateReceiptNo($companyId)
    {
        $date = Carbon::now()->format('Ymd');
        $prefix = "MS-{$date}-";
        
        $lastReceipt = Mauzo::where('company_id', $companyId)
            ->where('receipt_no', 'like', $prefix . '%')
            ->orderBy('receipt_no', 'desc')
            ->first();
        
        if ($lastReceipt) {
            $lastNumber = intval(substr($lastReceipt->receipt_no, strlen($prefix)));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return $prefix . $newNumber;
    }

// Display main sales page
public function index()
{
    $user = $this->getAuthUser();
    
    if (!$user) {
        return redirect()->route('login')->withErrors(['Unauthorized access']);
    }
    
    $companyId = $user->company_id;
    
    $bidhaa = Bidhaa::where('company_id', $companyId)
        ->select('id', 'jina', 'bei_kuuza', 'bei_nunua', 'idadi', 'barcode', 'aina', 'kipimo')
        ->orderBy('jina')
        ->get();

    // Paginated sales for the Taarifa tab
    $mauzos = Mauzo::with('bidhaa')
        ->where('company_id', $companyId)
        ->orderBy('created_at', 'desc')
        ->paginate(10);

    $matumizi = Matumizi::where('company_id', $companyId)
        ->latest()
        ->get();

    $wateja = Mteja::where('company_id', $companyId)
        ->orderBy('jina')
        ->get();

    $madeni = Madeni::with('bidhaa')
        ->where('company_id', $companyId)
        ->latest()
        ->get();

    $marejeshos = Marejesho::with(['madeni.bidhaa'])
        ->where('company_id', $companyId)
        ->get();
    
    // NEW VARIABLES FOR FINANCIAL OVERVIEW
    // Today's data
    $todaysMauzos = Mauzo::with('bidhaa')
        ->where('company_id', $companyId)
        ->whereDate('created_at', today())
        ->get();
        
    $todaysMarejeshos = Marejesho::with(['madeni.bidhaa'])
        ->where('company_id', $companyId)
        ->whereDate('tarehe', today())
        ->get();
        
    $todaysMatumizi = Matumizi::where('company_id', $companyId)
        ->whereDate('created_at', today())
        ->get();
    
    // Weekly expenses (this week)
    $weeklyMatumizi = Matumizi::where('company_id', $companyId)
        ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
        ->get();
    
    // All-time data for Jumla Kuu
    $allTimeMauzos = Mauzo::where('company_id', $companyId)->get();
    $allTimeMarejeshos = Marejesho::where('company_id', $companyId)->get();
    $allMatumizi = Matumizi::where('company_id', $companyId)->get();
    
    // For grouped sales tab - use all sales, not paginated
    $allMauzos = Mauzo::with('bidhaa')
        ->where('company_id', $companyId)
        ->orderBy('created_at', 'desc')
        ->get();
        
    return view('mauzo.index', compact(
        'bidhaa', 
        'mauzos', 
        'matumizi', 
        'wateja', 
        'madeni',
        'marejeshos',
        'todaysMauzos',
        'todaysMarejeshos',
        'todaysMatumizi',
        'weeklyMatumizi',
        'allTimeMauzos',
        'allTimeMarejeshos',
        'allMatumizi',
        'allMauzos'
    ));
}

    // Get financial data via AJAX
    public function getFinancialData(Request $request)
    {
        $user = $this->getAuthUser();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }
        
        $companyId = $user->company_id;
        $today = Carbon::today();
        
        // Get today's sales
        $todayMauzos = Mauzo::with('bidhaa')
            ->where('company_id', $companyId)
            ->where('created_at', '>=', $today)
            ->get();
            
        // Get today's debt returns
        $todayMarejeshos = Marejesho::with(['madeni.bidhaa'])
            ->where('company_id', $companyId)
            ->where('tarehe', $today->toDateString())
            ->get();
            
        // Get today's expenses
        $todayMatumizi = Matumizi::where('company_id', $companyId)
            ->where('created_at', '>=', $today)
            ->get();
            
        // Get all expenses for totals
        $matumizi = Matumizi::where('company_id', $companyId)->get();
        
        // Get all sales for totals
        $allMauzos = Mauzo::with('bidhaa')->where('company_id', $companyId)->get();
        
        // Calculate today's data
        $totalSalesRaw = $todayMauzos->sum('jumla');
        $totalDiscounts = $todayMauzos->sum('punguzo');
        $totalBuyingCost = 0;
        $totalFaida = 0;
        
        foreach($todayMauzos as $mauzo) {
            $buyingPrice = $mauzo->bidhaa->bei_nunua ?? 0;
            $sellingPrice = $mauzo->bei;
            $totalBuyingCost += $buyingPrice * $mauzo->idadi;
            $totalFaida += ($sellingPrice - $buyingPrice) * $mauzo->idadi - $mauzo->punguzo;
        }
        
        $mapatoMauzo = $totalSalesRaw - $totalDiscounts;
        $mapatoMadeni = $todayMarejeshos->sum('kiasi');
        $mapatoLeo = $mapatoMauzo + $mapatoMadeni;
        
        $faidaMauzo = $mapatoMauzo - $totalBuyingCost;
        
        $faidaMarejesho = 0;
        foreach($todayMarejeshos as $marejesho) {
            if(isset($marejesho->madeni) && isset($marejesho->madeni->bidhaa)) {
                $buyingPrice = $marejesho->madeni->bidhaa->bei_nunua ?? 0;
                $sellingPrice = $marejesho->madeni->bidhaa->bei_kuuza ?? 0;
                $profitPerUnit = $sellingPrice - $buyingPrice;
                $paymentRatio = $marejesho->kiasi / $marejesho->madeni->jumla;
                $faidaMarejesho += $profitPerUnit * $marejesho->madeni->idadi * $paymentRatio;
            }
        }
        
        $faidaLeo = $faidaMauzo + $faidaMarejesho;
        
        $matumiziLeo = $todayMatumizi->sum('gharama');
        $matumiziWiki = $matumizi->where('created_at', '>=', now()->startOfWeek())->sum('gharama');
        $matumiziJumla = $matumizi->sum('gharama');
        
        $fedhaLeo = $mapatoMauzo - $matumiziLeo;
        $faidaHalisi = $faidaLeo - $matumiziLeo;
        
        // Calculate all-time totals
        $allDiscounts = $allMauzos->sum('punguzo');
        $allBuyingCost = 0;
        $allFaida = 0;
        
        foreach($allMauzos as $mauzo) {
            $buyingPrice = $mauzo->bidhaa->bei_nunua ?? 0;
            $sellingPrice = $mauzo->bei;
            $allBuyingCost += $buyingPrice * $mauzo->idadi;
            $allFaida += ($sellingPrice - $buyingPrice) * $mauzo->idadi - $mauzo->punguzo;
        }
        
        $totalSalesAll = $allMauzos->sum('jumla');
        $totalMapato = $totalSalesAll - $allDiscounts;
        $totalMatumizi = $matumizi->sum('gharama');
        $totalProfit = $totalMapato - $allBuyingCost;
        $jumlaKuu = $totalProfit - $totalMatumizi;
        
        return response()->json([
            'success' => true,
            'data' => [
                'mapato_leo' => number_format($mapatoLeo),
                'faida_leo' => number_format($totalFaida + $faidaMarejesho),
                'matumizi_leo' => number_format($matumiziLeo),
                'fedha_leo' => number_format($fedhaLeo),
                'faida_halisi' => number_format($faidaHalisi),
                'jumla_kuu' => number_format($jumlaKuu),
                'mapato_mauzo' => number_format($mapatoMauzo),
                'faida_mauzo' => number_format($totalFaida),
                'matumizi_jumla' => number_format($matumiziJumla),
                'total_mapato' => number_format($totalMapato),
                'total_matumizi' => number_format($totalMatumizi)
            ]
        ]);
    }

    // Check for double sales - UPDATED to be more strict
    public function checkDoubleSale($bidhaaId)
    {
        $user = $this->getAuthUser();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }
        
        $companyId = $user->company_id;
        
        // Check for recent sales of the same product within last 3 minutes
        $recentSale = Mauzo::where('company_id', $companyId)
            ->where('bidhaa_id', $bidhaaId)
            ->where('created_at', '>=', Carbon::now()->subMinutes(3))
            ->orderBy('created_at', 'desc')
            ->first();
        
        return response()->json([
            'success' => true,
            'recent_sale' => $recentSale,
            'has_double_sale' => $recentSale ? true : false
        ]);
    }

    // Store sale (normal or loan) - UPDATED to check double sale
    public function store(Request $request)
    {
        $user = $this->getAuthUser();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
                'notification' => 'Unauthorized!'
            ], 401);
        }
        
        // Check if this is a loan sale
        if ($request->has('kopesha') && $request->kopesha == '1') {
            return $this->storeLoan($request);
        }

        // For regular sale, check for double sale if requested
        if ($request->has('check_double_sale') && $request->check_double_sale == '1') {
            $hasDoubleSale = $this->checkDoubleSaleInController($request->bidhaa_id);
            if ($hasDoubleSale) {
                return response()->json([
                    'success' => false,
                    'message' => 'Double sale detected! Please confirm you want to proceed.',
                    'notification' => 'Double sale detected!',
                    'double_sale' => true
                ], 422);
            }
        }

        return $this->storeRegularSale($request);
    }

    // Helper method to check double sale in controller
    private function checkDoubleSaleInController($bidhaaId)
    {
        $companyId = $this->getCompanyId();
        
        $recentSale = Mauzo::where('company_id', $companyId)
            ->where('bidhaa_id', $bidhaaId)
            ->where('created_at', '>=', Carbon::now()->subMinutes(3))
            ->first();
        
        return $recentSale ? true : false;
    }

    // Store sale via barcode - UPDATED to check double sale
    public function storeBarcode(Request $request)
    {
        $user = $this->getAuthUser();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
                'notification' => 'Unauthorized!'
            ], 401);
        }
        
        $companyId = $user->company_id;
        $items = $request->input('items', []);

        // Check for double sales in barcode items
        if ($request->has('check_double_sale') && $request->check_double_sale == '1') {
            foreach ($items as $item) {
                if (isset($item['bidhaa_id'])) {
                    $hasDoubleSale = $this->checkDoubleSaleInController($item['bidhaa_id']);
                    if ($hasDoubleSale) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Double sale detected for product!',
                            'notification' => 'Double sale detected!',
                            'double_sale' => true
                        ], 422);
                    }
                }
            }
        }

        return DB::transaction(function () use ($items, $companyId, $request) {
            $results = [];
            $receiptNo = $this->generateReceiptNo($companyId);
            
            foreach ($items as $item) {
                $validated = Validator::make($item, [
                    'bidhaa_id' => 'required|exists:bidhaas,id',
                    'idadi'     => 'required|integer|min:1',
                    'punguzo'   => 'nullable|numeric|min:0',
                    'punguzo_aina' => 'nullable|in:bidhaa,jumla',
                ])->validate();

                $bidhaa = Bidhaa::where('id', $validated['bidhaa_id'])
                    ->where('company_id', $companyId)
                    ->firstOrFail();

                // Check expiry
                if ($bidhaa->expiry && $bidhaa->expiry < now()->toDateString()) {
                    return response()->json([
                        'success' => false,
                        'message' => "Bidhaa {$bidhaa->jina} ime-expire.",
                        'notification' => 'Bidhaa ime-expire!'
                    ], 422);
                }

                // Check stock
                if ($validated['idadi'] > $bidhaa->idadi) {
                    return response()->json([
                        'success' => false,
                        'message' => "Stock haijatosha kwa {$bidhaa->jina}, baki ni {$bidhaa->idadi}.",
                        'notification' => 'Stock haitoshi!'
                    ], 422);
                }

                // Calculate totals based on discount type
                $baseTotal = $bidhaa->bei_kuuza * $validated['idadi'];
                $discount = $validated['punguzo'] ?? 0;
                $discountType = $validated['punguzo_aina'] ?? 'bidhaa';
                
                // Validate discount
                if ($discountType === 'jumla' && $discount > $baseTotal) {
                    return response()->json([
                        'success' => false,
                        'message' => "Punguzo haliruhusiwi kuzidi jumla ya " . number_format($baseTotal) . " Tsh",
                        'notification' => 'Punguzo limepita kiasi!'
                    ], 422);
                }
                
                if ($discountType === 'bidhaa') {
                    $maxAllowedDiscount = ($bidhaa->bei_kuuza - $bidhaa->bei_nunua) * $validated['idadi'];
                    if ($discount > $maxAllowedDiscount) {
                        return response()->json([
                            'success' => false,
                            'message' => "Punguzo haliruhusiwi kuzidi faida ya " . number_format($maxAllowedDiscount) . " Tsh kwa {$bidhaa->jina}",
                            'notification' => 'Punguzo limepita kiasi!'
                        ], 422);
                    }
                }

                $jumla = $baseTotal - $discount;
                
                // Update stock
                $bidhaa->decrement('idadi', $validated['idadi']);

                // Create sale record
                $mauzo = Mauzo::create([
                    'company_id' => $companyId,
                    'receipt_no' => $receiptNo,
                    'bidhaa_id'  => $bidhaa->id,
                    'idadi'      => $validated['idadi'],
                    'bei'        => $bidhaa->bei_kuuza,
                    'punguzo'    => $discount,
                    'punguzo_aina' => $discountType,
                    'jumla'      => $jumla,
                ]);

                $results[] = $mauzo;
            }

            return response()->json([
                'success' => true,
                'message' => 'Mauzo yamerekodiwa kikamilifu!',
                'notification' => 'Mauzo yamefanikiwa!',
                'receipt_no' => $receiptNo,
            ]);

        });
    }

    // Delete sale - UPDATED to restore stock
    public function destroy($id)
    {
        $user = $this->getAuthUser();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
                'notification' => 'Unauthorized!'
            ], 401);
        }
        
        $companyId = $user->company_id;
        $mauzo = Mauzo::where('id', $id)
            ->where('company_id', $companyId)
            ->first();

        if (!$mauzo) {
            return response()->json([
                'success' => false,
                'message' => 'Rekodi ya mauzo haipatikani',
                'notification' => 'Rekodi haipo!'
            ], 404);
        }

        try {
            DB::beginTransaction();
            
            // Restore stock
            $bidhaa = $mauzo->bidhaa;
            if ($bidhaa) {
                $bidhaa->increment('idadi', $mauzo->idadi);
            }

            // Delete sale
            $mauzo->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Rekodi ya mauzo imefutwa kikamilifu! Stock imerudishwa.',
                'notification' => 'Rekodi imefutwa! Stock imerudishwa.',
                'stock_restored' => $mauzo->idadi,
                'product_name' => $bidhaa->jina ?? 'Unknown'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Kuna tatizo katika kufuta mauzo: ' . $e->getMessage(),
                'notification' => 'Kuna tatizo!'
            ], 500);
        }
    }

    // Handle loan sales
    private function storeLoan(Request $request)
    {
        $user = $this->getAuthUser();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
                'notification' => 'Unauthorized!'
            ], 401);
        }
        
        $companyId = $user->company_id;

        return DB::transaction(function () use ($request, $companyId) {
            $validator = Validator::make($request->all(), [
                'bidhaa_id'      => 'required|exists:bidhaas,id',
                'idadi'          => 'required|integer|min:1',
                'bei'            => 'required|numeric',
                'jumla'          => 'required|numeric',
                'punguzo'        => 'nullable|numeric|min:0',
                'punguzo_aina'   => 'nullable|in:bidhaa,jumla',
                'jina_mkopaji'   => 'required|string|max:255',
                'simu'           => 'required|string|max:20',
                'tarehe_malipo'  => 'required|date',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'notification' => 'Kosa katika taarifa!'
                ], 422);
            }

            $bidhaa = Bidhaa::where('id', $request->bidhaa_id)
                ->where('company_id', $companyId)
                ->first();

            if (!$bidhaa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bidhaa haipatikani',
                    'notification' => 'Bidhaa haipo!'
                ], 404);
            }

            // Check expiry
            if ($bidhaa->expiry && $bidhaa->expiry < now()->toDateString()) {
                return response()->json([
                    'success' => false,
                    'message' => "Bidhaa hii ime-expire.",
                    'notification' => 'Bidhaa ime-expire!'
                ], 422);
            }

            // Check stock
            if ($request->idadi > $bidhaa->idadi) {
                return response()->json([
                    'success' => false,
                    'message' => "Stock haijatosha, baki ni {$bidhaa->idadi}.",
                    'notification' => 'Stock haitoshi!'
                ], 422);
            }

            // Calculate discount validation
            $baseTotal = $request->bei * $request->idadi;
            $discount = $request->punguzo ?? 0;
            $discountType = $request->punguzo_aina ?? 'bidhaa';
            
            if ($discountType === 'jumla' && $discount > $baseTotal) {
                return response()->json([
                    'success' => false,
                    'message' => "Punguzo haliruhusiwi kuzidi jumla ya " . number_format($baseTotal) . " Tsh",
                    'notification' => 'Punguzo limepita kiasi!'
                ], 422);
            }
            
            if ($discountType === 'bidhaa') {
                $maxAllowedDiscount = ($request->bei - $bidhaa->bei_nunua) * $request->idadi;
                if ($discount > $maxAllowedDiscount) {
                    return response()->json([
                        'success' => false,
                        'message' => "Punguzo haliruhusiwi kuzidi faida ya " . number_format($maxAllowedDiscount) . " Tsh",
                        'notification' => 'Punguzo limepita kiasi!'
                    ], 422);
                }
            }

            // Update stock
            $bidhaa->decrement('idadi', $request->idadi);

            // Create debt record
            $deni = Madeni::create([
                'company_id'    => $companyId,
                'bidhaa_id'     => $request->bidhaa_id,
                'idadi'         => $request->idadi,
                'bei'           => $request->bei,
                'punguzo'       => $discount,
                'punguzo_aina'  => $discountType,
                'jumla'         => $request->jumla,
                'jina_mkopaji'  => $request->jina_mkopaji,
                'simu'          => $request->simu,
                'barua_pepe'    => $request->barua_pepe ?? null,
                'anapoishi'     => $request->anapoishi ?? null,
                'tarehe_malipo' => $request->tarehe_malipo,
                'baki'          => $request->jumla,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Deni limerekodiwa kikamilifu!',
                'notification' => 'Deni limefanikiwa!',
                'data' => $deni
            ]);

        });
    }

    // Handle regular sales - UPDATED with proper discount calculation
    private function storeRegularSale(Request $request)
    {
        $user = $this->getAuthUser();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
                'notification' => 'Unauthorized!'
            ], 401);
        }
        
        $companyId = $user->company_id;

        return DB::transaction(function () use ($request, $companyId) {
            $validator = Validator::make($request->all(), [
                'bidhaa_id'    => 'required|exists:bidhaas,id',
                'idadi'        => 'required|integer|min:1',
                'bei'          => 'required|numeric',
                'punguzo'      => 'nullable|numeric|min:0',
                'punguzo_aina' => 'nullable|in:bidhaa,jumla',
                'jumla'        => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'notification' => 'Kosa katika taarifa!'
                ], 422);
            }

            $bidhaa = Bidhaa::where('id', $request->bidhaa_id)
                ->where('company_id', $companyId)
                ->first();

            if (!$bidhaa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bidhaa haipatikani',
                    'notification' => 'Bidhaa haipo!'
                ], 404);
            }

            // Check expiry
            if ($bidhaa->expiry && $bidhaa->expiry < now()->toDateString()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bidhaa hii ime-expire',
                    'notification' => 'Bidhaa ime-expire!'
                ], 422);
            }

            // Check stock
            if ($request->idadi > $bidhaa->idadi) {
                return response()->json([
                    'success' => false,
                    'message' => "Stock haijatosha, baki ni {$bidhaa->idadi}",
                    'notification' => 'Stock haitoshi!'
                ], 422);
            }

            // Calculate totals based on discount type
            $baseTotal = $request->bei * $request->idadi; // Total before discount
            $discount = $request->punguzo ?? 0;
            $discountType = $request->punguzo_aina ?? 'bidhaa';
            
            // Calculate ACTUAL discount based on type
            $actualDiscount = $discount;
            if ($discountType === 'bidhaa') {
                // For per-item discount, multiply by quantity
                $actualDiscount = $discount * $request->idadi;
            }
            
            // Validate discount based on type
            if ($discountType === 'jumla') {
                // For total discount, check it doesn't exceed base total
                if ($discount > $baseTotal) {
                    return response()->json([
                        'success' => false,
                        'message' => "Punguzo haliruhusiwi kuzidi jumla ya " . number_format($baseTotal) . " Tsh",
                        'notification' => 'Punguzo limepita kiasi!'
                    ], 422);
                }
            } else {
                // For per-item discount, check it doesn't exceed profit per item
                $maxAllowedDiscountPerItem = $request->bei - $bidhaa->bei_nunua;
                if ($discount > $maxAllowedDiscountPerItem) {
                    return response()->json([
                        'success' => false,
                        'message' => "Punguzo haliruhusiwi kuzidi faida ya " . number_format($maxAllowedDiscountPerItem) . " Tsh kwa kila bidhaa",
                        'notification' => 'Punguzo limepita kiasi!'
                    ], 422);
                }
            }

            // Final total calculation - subtract the ACTUAL discount
            $finalTotal = $baseTotal - $actualDiscount;
            
            // Verify the calculated total matches the submitted total
            $submittedTotal = $request->jumla;
            if (abs($finalTotal - $submittedTotal) > 0.01) {
                return response()->json([
                    'success' => false,
                    'message' => "Jumla iliyoingizwa (" . number_format($submittedTotal, 2) . ") si sahihi. Jumla sahihi ni " . number_format($finalTotal, 2),
                    'notification' => 'Jumla si sahihi!'
                ], 422);
            }
            
            // Update stock
            $bidhaa->decrement('idadi', $request->idadi);

            // Generate receipt number
            $receiptNo = $this->generateReceiptNo($companyId);

            // Create sale record - store the per-item discount in punguzo field
            $mauzo = Mauzo::create([
                'company_id'   => $companyId,
                'receipt_no'   => $receiptNo,
                'bidhaa_id'    => $request->bidhaa_id,
                'idadi'        => $request->idadi,
                'bei'          => $request->bei,
                'punguzo'      => $discount, // Store per-item discount amount
                'punguzo_aina' => $discountType,
                'jumla'        => $finalTotal,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Mauzo yamerekodiwa kikamilifu!',
                'notification' => 'Mauzo yamefanikiwa!',
                'receipt_no' => $receiptNo,
                'data' => $mauzo
            ]);
        });
    }

    // Handle basket (kikapu) sales - UPDATED to check double sale
    public function storeKikapu(Request $request)
    {
        $user = $this->getAuthUser();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
                'notification' => 'Unauthorized!'
            ], 401);
        }
        
        $companyId = $user->company_id;

        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.jina' => 'required|string',
            'items.*.bei' => 'required|numeric',
            'items.*.idadi' => 'required|integer|min:1',
            'items.*.punguzo' => 'nullable|numeric|min:0',
            'items.*.punguzo_aina' => 'nullable|in:bidhaa,jumla',
            'items.*.bidhaa_id' => 'required|exists:bidhaas,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'notification' => 'Kosa katika taarifa!'
            ], 422);
        }

        // Check for double sales in cart items
        if ($request->has('check_double_sale') && $request->check_double_sale == '1') {
            foreach ($request->items as $item) {
                $hasDoubleSale = $this->checkDoubleSaleInController($item['bidhaa_id']);
                if ($hasDoubleSale) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Double sale detected for product!',
                        'notification' => 'Double sale detected!',
                        'double_sale' => true
                    ], 422);
                }
            }
        }

        $saleIds = [];
        $receiptNo = $this->generateReceiptNo($companyId);
        $itemsData = [];
        
        try {
            DB::beginTransaction();
            
            foreach ($request->items as $item) {
                $bidhaa = Bidhaa::where('id', $item['bidhaa_id'])
                    ->where('company_id', $companyId)
                    ->first();

                if ($bidhaa) {
                    // Check stock
                    if ($item['idadi'] > $bidhaa->idadi) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => "Stock haitoshi kwa {$bidhaa->jina}, baki ni {$bidhaa->idadi}",
                            'notification' => 'Stock haitoshi!'
                        ], 422);
                    }

                    // Calculate discount properly
                    $baseTotal = $item['bei'] * $item['idadi'];
                    $discount = $item['punguzo'] ?? 0;
                    $discountType = $item['punguzo_aina'] ?? 'bidhaa';
                    
                    // Calculate ACTUAL discount based on type
                    $actualDiscount = $discount;
                    if ($discountType === 'bidhaa') {
                        $actualDiscount = $discount * $item['idadi'];
                    }
                    
                    // Validate discount based on type
                    if ($discountType === 'jumla' && $discount > $baseTotal) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => "Punguzo haliruhusiwi kuzidi jumla ya " . number_format($baseTotal) . " Tsh kwa {$bidhaa->jina}",
                            'notification' => 'Punguzo limepita kiasi!'
                        ], 422);
                    }
                    
                    if ($discountType === 'bidhaa') {
                        $maxAllowedDiscountPerItem = $item['bei'] - $bidhaa->bei_nunua;
                        if ($discount > $maxAllowedDiscountPerItem) {
                            DB::rollBack();
                            return response()->json([
                                'success' => false,
                                'message' => "Punguzo haliruhusiwi kuzidi faida ya " . number_format($maxAllowedDiscountPerItem) . " Tsh kwa kila {$bidhaa->jina}",
                                'notification' => 'Punguzo limepita kiasi!'
                            ], 422);
                        }
                    }

                    $jumla = $baseTotal - $actualDiscount;

                    // Create sale record
                    $mauzo = Mauzo::create([
                        'company_id'   => $companyId,
                        'receipt_no'   => $receiptNo,
                        'bidhaa_id'    => $bidhaa->id,
                        'idadi'        => $item['idadi'],
                        'bei'          => $item['bei'],
                        'punguzo'      => $discount, // Store per-item discount
                        'punguzo_aina' => $discountType,
                        'jumla'        => $jumla,
                    ]);

                    // Update stock
                    $bidhaa->decrement('idadi', $item['idadi']);

                    $saleIds[] = $mauzo->id;
                    $itemsData[] = [
                        'bidhaa' => $bidhaa->jina,
                        'idadi' => $item['idadi'],
                        'bei' => $item['bei'],
                        'punguzo' => $discount, // Per-item discount
                        'punguzo_aina' => $discountType,
                        'actual_discount' => $actualDiscount, // Actual discount applied
                        'jumla' => $jumla
                    ];
                } else {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Bidhaa {$item['jina']} haipatikani",
                        'notification' => 'Bidhaa haipo!'
                    ], 404);
                }
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Mauzo ya kikapu yamehifadhiwa kikamilifu!',
                'notification' => 'Kikapu kimefanikiwa!',
                'receipt_no' => $receiptNo,
                'data' => $itemsData
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Kuna tatizo kwenye kuhifadhi mauzo: ' . $e->getMessage(),
                'notification' => 'Kuna tatizo!'
            ], 500);
        }
    }

    // Handle basket loans
    public function storeKikapuLoan(Request $request)
    {
        $user = $this->getAuthUser();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
                'notification' => 'Unauthorized!'
            ], 401);
        }
        
        $companyId = $user->company_id;

        // Get items from JSON string
        $items = json_decode($request->items, true);
        
        $validator = Validator::make($request->all(), [
            'jina_mkopaji' => 'required|string|max:255',
            'simu' => 'required|string|max:20',
            'tarehe_malipo' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'notification' => 'Kosa katika taarifa!'
            ], 422);
        }

        $loanIds = [];
        
        try {
            DB::beginTransaction();

            // Find or create customer
            $mteja = Mteja::firstOrCreate([
                'jina' => $request->jina_mkopaji,
                'simu' => $request->simu,
                'company_id' => $companyId,
            ], [
                'barua_pepe' => $request->barua_pepe ?? null,
                'anapoishi' => $request->anapoishi ?? null,
            ]);

            foreach ($items as $item) {
                // Handle both barcode and regular items
                if (isset($item['bidhaa_id'])) {
                    $bidhaa = Bidhaa::where('id', $item['bidhaa_id'])
                        ->where('company_id', $companyId)
                        ->first();
                } else {
                    $bidhaa = Bidhaa::where('jina', $item['jina'])
                        ->where('company_id', $companyId)
                        ->first();
                }

                if ($bidhaa) {
                    $quantity = $item['idadi'] ?? 1;
                    $price = $item['bei'] ?? $bidhaa->bei_kuuza;
                    $discount = $item['punguzo'] ?? 0;
                    $discountType = $item['punguzo_aina'] ?? 'bidhaa';
                    
                    // Check stock
                    if ($quantity > $bidhaa->idadi) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => "Stock haitoshi kwa {$bidhaa->jina}, baki ni {$bidhaa->idadi}",
                            'notification' => 'Stock haitoshi!'
                        ], 422);
                    }

                    // Calculate total with discount
                    $baseTotal = $price * $quantity;
                    $jumla = $baseTotal - $discount;

                    // Update stock
                    $bidhaa->decrement('idadi', $quantity);

                    // Create debt record
                    $deni = Madeni::create([
                        'company_id'    => $companyId,
                        'bidhaa_id'     => $bidhaa->id,
                        'idadi'         => $quantity,
                        'bei'           => $price,
                        'punguzo'       => $discount,
                        'punguzo_aina'  => $discountType,
                        'jumla'         => $jumla,
                        'jina_mkopaji'  => $request->jina_mkopaji,
                        'simu'          => $request->simu,
                        'barua_pepe'    => $request->barua_pepe ?? null,
                        'anapoishi'     => $request->anapoishi ?? null,
                        'tarehe_malipo' => $request->tarehe_malipo,
                        'baki'          => $jumla,
                    ]);

                    $loanIds[] = $deni->id;
                } else {
                    DB::rollBack();
                    $itemName = $item['jina'] ?? 'Bidhaa';
                    return response()->json([
                        'success' => false,
                        'message' => "Bidhaa {$itemName} haipatikani",
                        'notification' => 'Bidhaa haipo!'
                    ], 404);
                }
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Bidhaa zimekopeshwa kwa mafanikio!',
                'notification' => 'Mikopo imefanikiwa!',
                'data' => $loanIds
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Kuna tatizo kwenye kukopesha bidhaa: ' . $e->getMessage(),
                'notification' => 'Kuna tatizo!'
            ], 500);
        }
    }

    // Get receipt for thermal printing
    public function getReceiptForPrint($receiptNo)
    {
        $user = $this->getAuthUser();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        
        $companyId = $user->company_id;

        $sales = Mauzo::with('bidhaa')
            ->where('company_id', $companyId)
            ->where('receipt_no', $receiptNo)
            ->get();

        if ($sales->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Hakuna taarifa za risiti hii'
            ], 404);
        }

        // Increment reprint count
        Mauzo::where('receipt_no', $receiptNo)
            ->where('company_id', $companyId)
            ->increment('reprint_count');

        $items = $sales->map(function($sale) {
            return [
                'bidhaa' => $sale->bidhaa->jina ?? 'Unknown',
                'idadi' => $sale->idadi,
                'bei' => $sale->bei,
                'punguzo' => $sale->punguzo,
                'punguzo_aina' => $sale->punguzo_aina,
                'jumla' => $sale->jumla
            ];
        });

        $total = $sales->sum('jumla');
        $totalPunguzo = $sales->sum('punguzo');
        $subtotal = $total + $totalPunguzo;

        return response()->json([
            'success' => true,
            'receipt_no' => $receiptNo,
            'items' => $items,
            'subtotal' => $subtotal,
            'punguzo' => $totalPunguzo,
            'total' => $total,
            'date' => $sales->first()->created_at->format('d/m/Y H:i'),
            'reprint_count' => $sales->first()->reprint_count + 1
        ]);
    }

    // Generate thermal receipt HTML
    public function printThermalReceipt($receiptNo)
    {
        $user = $this->getAuthUser();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        
        $companyId = $user->company_id;

        // Get company data
        $company = \App\Models\Company::find($companyId);
        
        // If company not found, create a default object
        if (!$company) {
            $company = (object) [
                'company_name' => $user->company_name ?? 'BIASHARA YANGU',
                'location' => $user->location ?? '',
                'region' => $user->region ?? '',
                'phone' => $user->phone ?? '',
                'email' => $user->email ?? '',
                'owner_name' => $user->name ?? '',
            ];
        }

        $sales = Mauzo::with('bidhaa')
            ->where('company_id', $companyId)
            ->where('receipt_no', $receiptNo)
            ->get();

        if ($sales->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Hakuna taarifa za risiti hii'
            ], 404);
        }

        $date = $sales->first()->created_at->format('d/m/Y H:i');
        $total = $sales->sum('jumla');
        $totalPunguzo = $sales->sum('punguzo');
        $subtotal = $total + $totalPunguzo;

        // Pass company variable to the view
        return view('mauzo.thermal-receipt', compact('receiptNo', 'sales', 'date', 'subtotal', 'totalPunguzo', 'total', 'company'));
    }

    // Search receipts
    public function searchReceipts(Request $request)
    {
        $user = $this->getAuthUser();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        
        $companyId = $user->company_id;
        $searchTerm = $request->input('search', '');

        $receipts = Mauzo::where('company_id', $companyId)
            ->whereNotNull('receipt_no')
            ->where('receipt_no', 'like', "%{$searchTerm}%")
            ->select('receipt_no', DB::raw('count(*) as item_count'), DB::raw('sum(jumla) as total'), DB::raw('max(created_at) as last_date'))
            ->groupBy('receipt_no')
            ->orderBy('last_date', 'desc')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'receipts' => $receipts
        ]);
    }

    // Get product by barcode for AJAX
    public function getProductByBarcode($barcode)
    {
        $user = $this->getAuthUser();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }
        
        $companyId = $user->company_id;
        
        $product = Bidhaa::where('barcode', $barcode)
            ->where('company_id', $companyId)
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Bidhaa haipatikani kwa barcode hii'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'product' => [
                'id' => $product->id,
                'jina' => $product->jina,
                'bei_kuuza' => $product->bei_kuuza,
                'bei_nunua' => $product->bei_nunua,
                'idadi' => $product->idadi,
                'aina' => $product->aina,
                'kipimo' => $product->kipimo,
                'barcode' => $product->barcode
            ]
        ]);
    }

    // Get receipt data for display
    public function getReceiptData($receiptNo)
    {
        $user = $this->getAuthUser();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }
        
        $companyId = $user->company_id;
        
        // Decode URL encoded receipt number
        $receiptNo = urldecode($receiptNo);
        
        // Clean up receipt number
        $receiptNo = trim($receiptNo);
        
        $sales = Mauzo::with('bidhaa')
            ->where('company_id', $companyId)
            ->where('receipt_no', $receiptNo)
            ->get();

        if ($sales->isEmpty()) {
            // Try without MS- prefix if it exists
            if (strpos($receiptNo, 'MS-') === 0) {
                $receiptNo = substr($receiptNo, 3);
                $sales = Mauzo::with('bidhaa')
                    ->where('company_id', $companyId)
                    ->where('receipt_no', 'like', '%' . $receiptNo . '%')
                    ->get();
            }
            
            if ($sales->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Receipt not found'], 404);
            }
        }

        $items = $sales->map(function($sale) {
            return [
                'bidhaa' => $sale->bidhaa->jina ?? 'Unknown',
                'idadi' => $sale->idadi,
                'bei' => $sale->bei,
                'punguzo' => $sale->punguzo,
                'jumla' => $sale->jumla
            ];
        });

        $total = $sales->sum('jumla');
        $totalPunguzo = $sales->sum('punguzo');

        return response()->json([
            'success' => true,
            'receipt_no' => $sales->first()->receipt_no,
            'items' => $items,
            'total' => $total,
            'punguzo' => $totalPunguzo,
            'date' => $sales->first()->created_at->format('d/m/Y H:i'),
            'item_count' => $sales->count()
        ]);
    }

    // Special kopesha endpoint for barcode
    public function storeKopesha(Request $request)
    {
        $user = $this->getAuthUser();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
                'notification' => 'Unauthorized!'
            ], 401);
        }
        
        $companyId = $user->company_id;
        
        return $this->storeKikapuLoan($request);
    }
}