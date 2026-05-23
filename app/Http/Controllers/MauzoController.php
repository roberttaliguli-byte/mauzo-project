<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Services\SMSService;
use App\Models\Mauzo;
use App\Models\Bidhaa;
use App\Models\Matumizi;
use App\Models\Madeni;
use App\Models\Mteja;
use App\Models\Marejesho;
use Carbon\Carbon;

class MauzoController extends Controller
{
    protected $smsService;

    public function __construct(SMSService $smsService)
    {
        $this->smsService = $smsService;
    }

    // ------------------- HELPER METHODS -------------------
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

    private function checkDoubleSaleInController($bidhaaId, $companyId)
    {
        $recentSale = Mauzo::where('company_id', $companyId)
            ->where('bidhaa_id', $bidhaaId)
            ->where('created_at', '>=', Carbon::now()->subSeconds(10))
            ->first();
        return $recentSale ? true : false;
    }

    private function actualDiscount($sale)
    {
        return $sale->punguzo_aina === 'bidhaa'
            ? $sale->punguzo * $sale->idadi
            : $sale->punguzo;
    }

// ------------------- DISPLAY PAGE -------------------
public function index()
{
    $user = $this->getAuthUser();
    if (!$user) {
        return redirect()->route('login')->withErrors(['Unauthorized access']);
    }
    $companyId = $user->company_id;

    // FIX: Add bei_uzo_jumla to the select query
    $bidhaa = Bidhaa::where('company_id', $companyId)
        ->select('id', 'jina', 'bei_kuuza', 'bei_uzo_jumla', 'bei_nunua', 'idadi', 'barcode', 'aina', 'kipimo')
        ->orderBy('jina')
        ->get();

    $mauzos = Mauzo::with('bidhaa')
        ->where('company_id', $companyId)
        ->orderBy('created_at', 'desc')
        ->paginate(10);

    $matumizi = Matumizi::where('company_id', $companyId)->latest()->get();
    $wateja = Mteja::where('company_id', $companyId)->orderBy('jina')->get();
    $madeni = Madeni::with('bidhaa')->where('company_id', $companyId)->latest()->get();
    $marejeshos = Marejesho::with(['madeni.bidhaa'])->where('company_id', $companyId)->get();

    $todaysMauzos = Mauzo::with('bidhaa')->where('company_id', $companyId)->whereDate('created_at', today())->get();
    $todaysMarejeshos = Marejesho::with(['madeni.bidhaa'])->where('company_id', $companyId)->whereDate('tarehe', today())->get();
    $todaysMatumizi = Matumizi::where('company_id', $companyId)->whereDate('created_at', today())->get();
    $weeklyMatumizi = Matumizi::where('company_id', $companyId)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->get();
    $allTimeMauzos = Mauzo::where('company_id', $companyId)->get();
    $allTimeMarejeshos = Marejesho::where('company_id', $companyId)->get();
    $allMatumizi = Matumizi::where('company_id', $companyId)->get();
    $allMauzos = Mauzo::with('bidhaa')->where('company_id', $companyId)->orderBy('created_at', 'desc')->get();

    return view('mauzo.index', compact(
        'bidhaa', 'mauzos', 'matumizi', 'wateja', 'madeni', 'marejeshos',
        'todaysMauzos', 'todaysMarejeshos', 'todaysMatumizi', 'weeklyMatumizi',
        'allTimeMauzos', 'allTimeMarejeshos', 'allMatumizi', 'allMauzos'
    ));
}
    // ------------------- FINANCIAL DATA (AJAX) -------------------
    public function getFinancialData(Request $request)
    {
        $user = $this->getAuthUser();
        if (!$user) return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        $companyId = $user->company_id;
        $today = Carbon::today();

        $todayMauzos = Mauzo::with('bidhaa')->where('company_id', $companyId)->where('created_at', '>=', $today)->get();
        $todayMarejeshos = Marejesho::with(['madeni.bidhaa'])->where('company_id', $companyId)->where('tarehe', $today->toDateString())->get();
        $todayMatumizi = Matumizi::where('company_id', $companyId)->where('created_at', '>=', $today)->get();
        $matumizi = Matumizi::where('company_id', $companyId)->get();
        $allMauzos = Mauzo::with('bidhaa')->where('company_id', $companyId)->get();

        $totalSalesRaw = $todayMauzos->sum('jumla');
        $totalDiscounts = $todayMauzos->sum('punguzo');
        $totalBuyingCost = 0;
        $totalFaida = 0;
        foreach ($todayMauzos as $mauzo) {
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
        foreach ($todayMarejeshos as $marejesho) {
            if (isset($marejesho->madeni) && isset($marejesho->madeni->bidhaa)) {
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

        $allDiscounts = $allMauzos->sum('punguzo');
        $allBuyingCost = 0;
        $allFaida = 0;
        foreach ($allMauzos as $mauzo) {
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
                'mapato_leo' => number_format($mapatoLeo, 2),
                'faida_leo' => number_format($totalFaida + $faidaMarejesho, 2),
                'matumizi_leo' => number_format($matumiziLeo, 2),
                'fedha_leo' => number_format($fedhaLeo, 2),
                'faida_halisi' => number_format($faidaHalisi, 2),
                'jumla_kuu' => number_format($jumlaKuu, 2),
                'mapato_mauzo' => number_format($mapatoMauzo, 2),
                'faida_mauzo' => number_format($totalFaida, 2),
                'matumizi_jumla' => number_format($matumiziJumla, 2),
                'total_mapato' => number_format($totalMapato, 2),
                'total_matumizi' => number_format($totalMatumizi, 2)
            ]
        ]);
    }

    // ------------------- CHECK DOUBLE SALE -------------------
    public function checkDoubleSale($bidhaaId)
    {
        $user = $this->getAuthUser();
        if (!$user) return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        $companyId = $user->company_id;
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

    // ------------------- MAIN STORE ENTRY -------------------
    public function store(Request $request)
    {
        $user = $this->getAuthUser();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access', 'notification' => 'Unauthorized!'], 401);
        }
        $companyId = $user->company_id;
        if ($request->has('kopesha') && $request->kopesha == '1') {
            return $this->storeLoan($request);
        }
        if ($request->has('check_double_sale') && $request->check_double_sale == '1') {
            if ($this->checkDoubleSaleInController($request->bidhaa_id, $companyId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Double sale detected! Please confirm you want to proceed.',
                    'notification' => 'Double sale detected!',
                    'double_sale' => true
                ], 422);
            }
        }
        return $this->storeRegularSale($request, $companyId);
    }

private function storeRegularSale(Request $request, $companyId)
{
    return DB::transaction(function () use ($request, $companyId) {
        $validator = Validator::make($request->all(), [
            'bidhaa_id'    => 'required|exists:bidhaas,id',
            'idadi'        => 'required|numeric|min:0.01',
            'bei'          => 'required|numeric',
            'punguzo'      => 'nullable|numeric|min:0',
            'punguzo_aina' => 'nullable|in:bidhaa,jumla',
            'jumla'        => 'required|numeric',
            'lipa_kwa'     => 'nullable|in:cash,lipa_namba,bank',
                'lipa_kwa_type' => 'nullable|required_if:lipa_kwa,lipa_namba,bank',
            'mteja_id'     => 'nullable|exists:mtejas,id',
            'bei_type'     => 'nullable|in:rejareja,jumla',
            // ADD THESE NEW RULES
            'send_receipt' => 'nullable|boolean',
            'send_to_phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first(), 'notification' => 'Kosa katika taarifa!'], 422);
        }

        // Validate lipa_kwa_type based on lipa_kwa
        if ($request->lipa_kwa === 'lipa_namba') {
            $validLipaLipaTypes = ['mpesa', 'mixx_by_yas', 'airtel_money', 'halopesa', 'other'];
            if (!in_array($request->lipa_kwa_type, $validLipaLipaTypes)) {
                return response()->json(['success' => false, 'message' => 'Tafadhali chagua aina sahihi ya Lipa Namba', 'notification' => 'Aina ya Lipa Namba si sahihi!'], 422);
            }
        }
        
        if ($request->lipa_kwa === 'bank') {
            $validBankTypes = ['crdb', 'nmb', 'nbc', 'other'];
            if (!in_array($request->lipa_kwa_type, $validBankTypes)) {
              return response()->json(['success' => false, 'message' => 'Tafadhali chagua aina sahihi ya Benki', 'notification' => 'Aina ya Benki si sahihi!'], 422);
                }
        }

        $bidhaa = Bidhaa::where('id', $request->bidhaa_id)->where('company_id', $companyId)->first();
        if (!$bidhaa) {
            return response()->json(['success' => false, 'message' => 'Bidhaa haipatikani', 'notification' => 'Bidhaa haipo!'], 404);
        }
        
        if ($bidhaa->expiry && $bidhaa->expiry < now()->toDateString()) {
            return response()->json(['success' => false, 'message' => 'Bidhaa hii ime-expire', 'notification' => 'Bidhaa ime-expire!'], 422);
        }
        
        // Determine which price to use based on bei_type selection
        $priceType = $request->bei_type ?? 'rejareja';
        $priceToUse = $request->bei;
        
        // Validate that the price matches the selected type
        if ($priceType === 'rejareja') {
            $expectedPrice = $bidhaa->bei_kuuza;
            if (abs($priceToUse - $expectedPrice) > 0.01 && $expectedPrice > 0) {
                $priceToUse = $expectedPrice;
            }
        } elseif ($priceType === 'jumla' && $bidhaa->bei_uzo_jumla && $bidhaa->bei_uzo_jumla > 0) {
            $expectedPrice = $bidhaa->bei_uzo_jumla;
            if (abs($priceToUse - $expectedPrice) > 0.01 && $expectedPrice > 0) {
                $priceToUse = $expectedPrice;
            }
        }
        
        if ($request->idadi > $bidhaa->idadi) {
            return response()->json(['success' => false, 'message' => "Stock haijatosha, baki ni {$bidhaa->idadi}", 'notification' => 'Stock haitoshi!'], 422);
        }

        $baseTotal = $priceToUse * $request->idadi;
        $discount = $request->punguzo ?? 0;
        $discountType = $request->punguzo_aina ?? 'bidhaa';
        $actualDiscount = ($discountType === 'bidhaa') ? $discount * $request->idadi : $discount;
        $profit = ($priceToUse - $bidhaa->bei_nunua) * $request->idadi;

        // Validate discount based on type
        if ($discountType === 'jumla') {
            if ($discount > $baseTotal) {
                return response()->json(['success' => false, 'message' => "Punguzo haliruhusiwi kuzidi jumla ya " . number_format($baseTotal, 2) . " Tsh", 'notification' => 'Punguzo limepita kiasi!'], 422);
            }
            if ($discount > $profit) {
                return response()->json(['success' => false, 'message' => "Punguzo la jumla haliruhusiwi kuzidi faida ya " . number_format($profit, 2) . " Tsh", 'notification' => 'Punguzo limezidi faida!'], 422);
            }
        } else {
            $maxDiscountPerItem = $priceToUse - $bidhaa->bei_nunua;
            if ($discount > $maxDiscountPerItem) {
                return response()->json(['success' => false, 'message' => "Punguzo haliruhusiwi kuzidi faida ya " . number_format($maxDiscountPerItem, 2) . " Tsh kwa kila bidhaa", 'notification' => 'Punguzo limepita kiasi!'], 422);
            }
        }

        $finalTotal = $baseTotal - $actualDiscount;
        if (abs($finalTotal - $request->jumla) > 0.01) {
            return response()->json(['success' => false, 'message' => "Jumla iliyoingizwa (" . number_format($request->jumla, 2) . ") si sahihi. Jumla sahihi ni " . number_format($finalTotal, 2), 'notification' => 'Jumla si sahihi!'], 422);
        }

        $bidhaa->decrement('idadi', $request->idadi);
        $receiptNo = $this->generateReceiptNo($companyId);

        $mauzo = Mauzo::create([
            'company_id'    => $companyId,
            'receipt_no'    => $receiptNo,
            'bidhaa_id'     => $request->bidhaa_id,
            'idadi'         => $request->idadi,
            'bei'           => $priceToUse,
            'bei_type_used' => $priceType,
            'punguzo'       => $discount,
            'punguzo_aina'  => $discountType,
            'jumla'         => $finalTotal,
            'lipa_kwa'      => $request->lipa_kwa ?? 'cash',
             'lipa_kwa_type' => ($request->lipa_kwa === 'cash') ? null : $request->lipa_kwa_type, // ADD THIS
            'mteja_id'      => $request->mteja_id,
        ]);

        // === NEW CODE: Send SMS receipt if requested ===
        $sendReceipt = $request->input('send_receipt', false);
        if ($sendReceipt == '1' || $sendReceipt === true || $sendReceipt === 'true') {
            $phoneNumber = null;
            
            // Determine phone number to send to
            if ($request->mteja_id) {
                // Get phone from registered customer
                $mteja = Mteja::find($request->mteja_id);
                if ($mteja && $mteja->simu) {
                    $phoneNumber = $mteja->simu;
                }
            } else {
                // Use the manually entered phone number
                $phoneNumber = $request->input('send_to_phone');
            }
            
            // Send SMS if we have a valid phone number
            if ($phoneNumber && !empty(trim($phoneNumber))) {
                $this->sendReceiptAfterSale($mauzo, $phoneNumber);
            }
        }

        return response()->json([
            'success' => true, 
            'message' => 'Mauzo yamerekodiwa kikamilifu!', 
            'notification' => 'Mauzo yamefanikiwa!', 
            'receipt_no' => $receiptNo, 
            'data' => $mauzo
        ]);
    });
}

/**
 * Send receipt SMS after successful sale
 */
private function sendReceiptAfterSale($sale, $phoneNumber)
{
    try {
        $company = \App\Models\Company::find($sale->company_id);
        $user = $this->getAuthUser();
        
        // Get all items in same receipt
        $sales = Mauzo::with('bidhaa')
            ->where('company_id', $sale->company_id)
            ->where('receipt_no', $sale->receipt_no)
            ->get();
        
        // Format phone number
        $phone = preg_replace('/[^0-9]/', '', $phoneNumber);
        if (substr($phone, 0, 1) == '0') $phone = '255' . substr($phone, 1);
        if (substr($phone, 0, 1) == '7') $phone = '255' . $phone;
        if (strlen($phone) != 12) {
            \Log::warning("Invalid phone number format: {$phone}");
            return false;
        }
        
        // Define website URL
        $websiteUrl = 'www.mauzosheetai.co.tz';
        
        // Build SMS message
        $message = "";
        if ($company && $company->company_name) {
            $message .= strtoupper($company->company_name) . "\n";
        }
        $message .= "RISITI: " . $sale->receipt_no . "\n";
        $message .= "Tarehe: " . now()->format('d/m/Y H:i') . "\n";
        $message .= str_repeat("-", 25) . "\n";
        
        foreach ($sales as $item) {
            $message .= $item->bidhaa->jina . "\n";
            $message .= "  " . number_format($item->idadi, 2) . " x " . number_format($item->bei, 0) . " = " . number_format($item->jumla, 0) . "\n";
            if ($item->punguzo > 0) {
                $discountAmount = $item->punguzo_aina === 'bidhaa' ? $item->punguzo * $item->idadi : $item->punguzo;
                $message .= "  Punguzo: -" . number_format($discountAmount, 0) . "\n";
            }
        }
        
        $message .= str_repeat("-", 25) . "\n";
        $totalPunguzo = $sales->sum(function($item) {
            return $item->punguzo_aina === 'bidhaa' ? $item->punguzo * $item->idadi : $item->punguzo;
        });
        $subtotal = $sales->sum('jumla') + $totalPunguzo;
        
        if ($totalPunguzo > 0) {
            $message .= "Jumla Ndogo: " . number_format($subtotal, 0) . "\n";
            $message .= "Punguzo: -" . number_format($totalPunguzo, 0) . "\n";
            $message .= str_repeat("-", 25) . "\n";
        }
        
        $message .= "JUMLA: " . number_format($sales->sum('jumla'), 0) . "/=\n";
        $paymentText = match($sale->lipa_kwa) {
            'cash' => 'CASH',
            'lipa_namba' => 'LIPA NAMBA',
            'bank' => 'BENKI',
            default => strtoupper($sale->lipa_kwa)
        };
        $message .= "Malipo: " . $paymentText . "\n";
        $message .= str_repeat("-", 25) . "\n";
        $message .= "ASANTE KWA KUNUNUA!\n";
        $message .= $websiteUrl . "\n";  // Fixed: Use variable, not Blade syntax
        
        // Send SMS
        $result = $this->smsService->sendSms($phone, $message, 'RECEIPT_' . $sale->receipt_no);
        
        \Log::info("SMS sent for receipt {$sale->receipt_no} to {$phone}: " . ($result['success'] ? 'Success' : 'Failed'));
        
        return $result['success'] ?? false;
        
    } catch (\Exception $e) {
        \Log::error('Error sending receipt SMS after sale: ' . $e->getMessage());
        return false;
    }
}

// In storeLoan method, add bei_type handling
private function storeLoan(Request $request)
{
    $user = $this->getAuthUser();
    if (!$user) {
        return response()->json(['success' => false, 'message' => 'Unauthorized access', 'notification' => 'Unauthorized!'], 401);
    }
    $companyId = $user->company_id;

    return DB::transaction(function () use ($request, $companyId) {
        $validator = Validator::make($request->all(), [
            'bidhaa_id'      => 'required|exists:bidhaas,id',
            'idadi'          => 'required|numeric|min:0.01',
            'bei'            => 'required|numeric',
            'jumla'          => 'required|numeric',
            'punguzo'        => 'nullable|numeric|min:0',
            'punguzo_aina'   => 'nullable|in:bidhaa,jumla',
            'jina_mkopaji'   => 'required|string|max:255',
            'simu'           => 'required|string|max:20',
            'tarehe_malipo'  => 'required|date',
            'mteja_id'       => 'nullable|exists:mtejas,id',
            'bei_type'       => 'nullable|in:rejareja,jumla', // ADD THIS LINE
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first(), 'notification' => 'Kosa katika taarifa!'], 422);
        }

        $bidhaa = Bidhaa::where('id', $request->bidhaa_id)->where('company_id', $companyId)->first();
        if (!$bidhaa) {
            return response()->json(['success' => false, 'message' => 'Bidhaa haipatikani', 'notification' => 'Bidhaa haipo!'], 404);
        }
        
        // Determine which price to use
        $priceToUse = $request->bei;
        $priceType = $request->bei_type ?? 'rejareja';
        if ($priceType === 'jumla' && $bidhaa->bei_uzo_jumla && $bidhaa->bei_uzo_jumla > 0) {
            $priceToUse = $bidhaa->bei_uzo_jumla;
        }
        
        if ($bidhaa->expiry && $bidhaa->expiry < now()->toDateString()) {
            return response()->json(['success' => false, 'message' => "Bidhaa hii ime-expire.", 'notification' => 'Bidhaa ime-expire!'], 422);
        }
        if ($request->idadi > $bidhaa->idadi) {
            return response()->json(['success' => false, 'message' => "Stock haijatosha, baki ni {$bidhaa->idadi}.", 'notification' => 'Stock haitoshi!'], 422);
        }

        $baseTotal = $priceToUse * $request->idadi;
        $discount = $request->punguzo ?? 0;
        $discountType = $request->punguzo_aina ?? 'bidhaa';
        $actualDiscount = ($discountType === 'bidhaa') ? $discount * $request->idadi : $discount;
        $profit = ($priceToUse - $bidhaa->bei_nunua) * $request->idadi;

        // Validate discount (same as regular sale)
        if ($discountType === 'jumla') {
            if ($discount > $baseTotal) {
                return response()->json(['success' => false, 'message' => "Punguzo haliruhusiwi kuzidi jumla ya " . number_format($baseTotal, 2) . " Tsh", 'notification' => 'Punguzo limepita kiasi!'], 422);
            }
            if ($discount > $profit) {
                return response()->json(['success' => false, 'message' => "Punguzo la jumla haliruhusiwi kuzidi faida ya " . number_format($profit, 2) . " Tsh", 'notification' => 'Punguzo limezidi faida!'], 422);
            }
        } else {
            $maxDiscountPerItem = $priceToUse - $bidhaa->bei_nunua;
            if ($discount > $maxDiscountPerItem) {
                return response()->json(['success' => false, 'message' => "Punguzo haliruhusiwi kuzidi faida ya " . number_format($maxDiscountPerItem, 2) . " Tsh kwa kila bidhaa", 'notification' => 'Punguzo limepita kiasi!'], 422);
            }
        }

        $finalTotal = $baseTotal - $actualDiscount;
        if (abs($finalTotal - $request->jumla) > 0.01) {
            return response()->json(['success' => false, 'message' => "Jumla iliyoingizwa (" . number_format($request->jumla, 2) . ") si sahihi. Jumla sahihi ni " . number_format($finalTotal, 2), 'notification' => 'Jumla si sahihi!'], 422);
        }

        $bidhaa->decrement('idadi', $request->idadi);

        // Customer handling (existing code)...
        $mtejaName = $request->jina_mkopaji;
        $mtejaPhone = $request->simu;
        $mtejaEmail = $request->barua_pepe ?? null;
        $mtejaAddress = $request->anapoishi ?? null;
        $actualMtejaId = $request->mteja_id;
        
        if ($actualMtejaId) {
            $mteja = Mteja::where('id', $actualMtejaId)->where('company_id', $companyId)->first();
            if ($mteja) {
                $mtejaName = $mteja->jina;
                $mtejaPhone = $mteja->simu;
                $mtejaEmail = $mteja->barua_pepe;
                $mtejaAddress = $mteja->anapoishi;
            }
        } else {
            $existingMteja = Mteja::where('simu', $request->simu)
                ->where('company_id', $companyId)
                ->first();
            
            if ($existingMteja) {
                $actualMtejaId = $existingMteja->id;
                $mtejaName = $existingMteja->jina;
                $mtejaEmail = $existingMteja->barua_pepe;
                $mtejaAddress = $existingMteja->anapoishi;
            } else {
                $newMteja = Mteja::create([
                    'company_id' => $companyId,
                    'jina' => $request->jina_mkopaji,
                    'simu' => $request->simu,
                    'barua_pepe' => $request->barua_pepe ?? null,
                    'anapoishi' => $request->anapoishi ?? null
                ]);
                $actualMtejaId = $newMteja->id;
            }
        }

        $deni = Madeni::create([
            'company_id'    => $companyId,
            'bidhaa_id'     => $request->bidhaa_id,
            'idadi'         => $request->idadi,
            'bei'           => $priceToUse, // Store actual price used
            'punguzo'       => $discount,
            'punguzo_aina'  => $discountType,
            'jumla'         => $finalTotal,
            'jina_mkopaji'  => $mtejaName,
            'simu'          => $mtejaPhone,
            'barua_pepe'    => $mtejaEmail,
            'anapoishi'     => $mtejaAddress,
            'tarehe_malipo' => $request->tarehe_malipo,
            'baki'          => $finalTotal,
            'mteja_id'      => $actualMtejaId,
        ]);

        return response()->json(['success' => true, 'message' => 'Deni limerekodiwa kikamilifu!', 'notification' => 'Deni limefanikiwa!', 'data' => $deni]);
    });
}
// ------------------- BARCODE SALE (multiple rows) -------------------
public function storeBarcode(Request $request)
{
    $user = $this->getAuthUser();
    if (!$user) {
        return response()->json(['success' => false, 'message' => 'Unauthorized access', 'notification' => 'Unauthorized!'], 401);
    }
    $companyId = $user->company_id;
    $items = $request->input('items', []);
    $paymentMethod = $request->input('lipa_kwa', 'cash');
    $paymentType = $request->input('lipa_kwa_type', null);
    $priceType = $request->input('bei_type', 'rejareja'); // ADD THIS


    // Validate payment type
    if ($paymentMethod === 'lipa_namba') {
        $validTypes = ['mpesa', 'mixx_by_yas', 'airtel_money', 'halopesa', 'other'];
        if (!in_array($paymentType, $validTypes)) {
            return response()->json(['success' => false, 'message' => 'Tafadhali chagua aina sahihi ya Lipa Namba', 'notification' => 'Aina ya Lipa Namba si sahihi!'], 422);
        }
    }
    
    if ($paymentMethod === 'bank') {
        $validTypes = ['crdb', 'nmb', 'nbc', 'other'];
        if (!in_array($paymentType, $validTypes)) {
            return response()->json(['success' => false, 'message' => 'Tafadhali chagua aina sahihi ya Benki', 'notification' => 'Aina ya Benki si sahihi!'], 422);
        }
    }

    return DB::transaction(function () use ($items, $companyId, $paymentMethod, $paymentType, $priceType) {
        $receiptNo = $this->generateReceiptNo($companyId);
        foreach ($items as $item) {
            $validated = Validator::make($item, [
                'bidhaa_id' => 'required|exists:bidhaas,id',
                'idadi'     => 'required|numeric|min:0.01',
                'punguzo'   => 'nullable|numeric|min:0',
                'punguzo_aina' => 'nullable|in:bidhaa,jumla',
            ])->validate();

            $bidhaa = Bidhaa::where('id', $validated['bidhaa_id'])->where('company_id', $companyId)->first();
            if (!$bidhaa) {
                return response()->json(['success' => false, 'message' => "Bidhaa haipatikani!", 'notification' => 'Bidhaa haipo!'], 404);
            }
            if ($bidhaa->expiry && $bidhaa->expiry < now()->toDateString()) {
                return response()->json(['success' => false, 'message' => "Bidhaa {$bidhaa->jina} ime-expire.", 'notification' => 'Bidhaa ime-expire!'], 422);
            }
            if ($validated['idadi'] > $bidhaa->idadi) {
                return response()->json(['success' => false, 'message' => "Stock haijatosha kwa {$bidhaa->jina}, baki ni {$bidhaa->idadi}.", 'notification' => 'Stock haitoshi!'], 422);
            }

            // Determine which price to use based on price type
            $priceToUse = $bidhaa->bei_kuuza;
            if ($priceType === 'jumla' && $bidhaa->bei_uzo_jumla && $bidhaa->bei_uzo_jumla > 0) {
                $priceToUse = $bidhaa->bei_uzo_jumla;
            }

            $baseTotal = $priceToUse * $validated['idadi'];
            $discount = $validated['punguzo'] ?? 0;
            $discountType = $validated['punguzo_aina'] ?? 'bidhaa';
            $actualDiscount = ($discountType === 'bidhaa') ? $discount * $validated['idadi'] : $discount;
            $profit = ($priceToUse - $bidhaa->bei_nunua) * $validated['idadi'];

            // Validate discount
            if ($discountType === 'jumla') {
                if ($discount > $baseTotal) {
                    return response()->json(['success' => false, 'message' => "Punguzo haliruhusiwi kuzidi jumla ya " . number_format($baseTotal, 2) . " Tsh", 'notification' => 'Punguzo limepita kiasi!'], 422);
                }
                if ($discount > $profit) {
                    return response()->json(['success' => false, 'message' => "Punguzo la jumla haliruhusiwi kuzidi faida ya " . number_format($profit, 2) . " Tsh kwa {$bidhaa->jina}", 'notification' => 'Punguzo limezidi faida!'], 422);
                }
            } else {
                $maxDiscount = ($priceToUse - $bidhaa->bei_nunua) * $validated['idadi'];
                if ($actualDiscount > $maxDiscount) {
                    return response()->json(['success' => false, 'message' => "Punguzo haliruhusiwi kuzidi faida ya " . number_format($maxDiscount, 2) . " Tsh kwa {$bidhaa->jina}", 'notification' => 'Punguzo limepita kiasi!'], 422);
                }
            }

            $jumla = $baseTotal - $actualDiscount;
            $bidhaa->decrement('idadi', $validated['idadi']);

            Mauzo::create([
                'company_id'    => $companyId,
                'receipt_no'    => $receiptNo,
                'bidhaa_id'     => $bidhaa->id,
                'idadi'         => $validated['idadi'],
                'bei'           => $priceToUse,
                'bei_type_used' => $priceType, // RECORD THE PRICE TYPE USED
                'punguzo'       => $discount,
                'punguzo_aina'  => $discountType,
                'jumla'         => $jumla,
                'lipa_kwa'      => $paymentMethod,
                        'lipa_kwa_type' => ($paymentMethod === 'cash') ? null : $paymentType, // ADD THIS
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Mauzo yamerekodiwa kikamilifu!', 'notification' => 'Mauzo yamefanikiwa!', 'receipt_no' => $receiptNo]);
    });
}

// ------------------- KIKAPU SALE (multiple products) -------------------
public function storeKikapu(Request $request)
{
    $user = $this->getAuthUser();
    if (!$user) {
        return response()->json(['success' => false, 'message' => 'Unauthorized access', 'notification' => 'Unauthorized!'], 401);
    }
    $companyId = $user->company_id;

    $validator = Validator::make($request->all(), [
        'items' => 'required|array|min:1',
        'items.*.jina' => 'required|string',
        'items.*.bei' => 'required|numeric',
        'items.*.idadi' => 'required|numeric|min:0.01',
        'items.*.punguzo' => 'nullable|numeric|min:0',
        'items.*.punguzo_aina' => 'nullable|in:bidhaa,jumla',
        'items.*.bidhaa_id' => 'required|exists:bidhaas,id',
        'lipa_kwa' => 'nullable|in:cash,lipa_namba,bank',
           'lipa_kwa_type' => 'nullable|required_if:lipa_kwa,lipa_namba,bank', // ADD THIS
        'mteja_id' => 'nullable|exists:mtejas,id',
        'send_receipt' => 'nullable|boolean',
        'send_to_phone' => 'nullable|string|max:20',
    ]);

    if ($validator->fails()) {
        return response()->json(['success' => false, 'message' => $validator->errors()->first(), 'notification' => 'Kosa katika taarifa!'], 422);
    }
    // Validate payment type
    if ($request->lipa_kwa === 'lipa_namba') {
        $validTypes = ['mpesa', 'mixx_by_yas', 'airtel_money', 'halopesa', 'other'];
        if (!in_array($request->lipa_kwa_type, $validTypes)) {
            return response()->json(['success' => false, 'message' => 'Tafadhali chagua aina sahihi ya Lipa Namba', 'notification' => 'Aina ya Lipa Namba si sahihi!'], 422);
        }
    }
    
    if ($request->lipa_kwa === 'bank') {
        $validTypes = ['crdb', 'nmb', 'nbc', 'other'];
        if (!in_array($request->lipa_kwa_type, $validTypes)) {
            return response()->json(['success' => false, 'message' => 'Tafadhali chagua aina sahihi ya Benki', 'notification' => 'Aina ya Benki si sahihi!'], 422);
        }
    }

    $receiptNo = $this->generateReceiptNo($companyId);
    $paymentMethod = $request->input('lipa_kwa', 'cash');
    $paymentType = $request->input('lipa_kwa_type', null);
    $sendReceipt = $request->input('send_receipt', false);
    $sendToPhone = $request->input('send_to_phone');

    try {
        DB::beginTransaction();

        foreach ($request->items as $item) {
            $bidhaa = Bidhaa::where('id', $item['bidhaa_id'])->where('company_id', $companyId)->first();
            if (!$bidhaa) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => "Bidhaa {$item['jina']} haipatikani", 'notification' => 'Bidhaa haipo!'], 404);
            }

            if ($item['idadi'] > $bidhaa->idadi) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => "Stock haitoshi kwa {$bidhaa->jina}, baki ni {$bidhaa->idadi}", 'notification' => 'Stock haitoshi!'], 422);
            }

            $baseTotal = $item['bei'] * $item['idadi'];
            $discount = $item['punguzo'] ?? 0;
            $discountType = $item['punguzo_aina'] ?? 'bidhaa';
            $actualDiscount = ($discountType === 'bidhaa') ? $discount * $item['idadi'] : $discount;
            $profit = ($item['bei'] - $bidhaa->bei_nunua) * $item['idadi'];

            // Validate discount
            if ($discountType === 'jumla') {
                if ($discount > $baseTotal) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => "Punguzo haliruhusiwi kuzidi jumla ya " . number_format($baseTotal, 2) . " Tsh kwa {$bidhaa->jina}", 'notification' => 'Punguzo limepita kiasi!'], 422);
                }
                if ($discount > $profit) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => "Punguzo la jumla haliruhusiwi kuzidi faida ya " . number_format($profit, 2) . " Tsh kwa {$bidhaa->jina}", 'notification' => 'Punguzo limezidi faida!'], 422);
                }
            } else {
                $maxDiscountPerItem = $item['bei'] - $bidhaa->bei_nunua;
                if ($discount > $maxDiscountPerItem) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => "Punguzo haliruhusiwi kuzidi faida ya " . number_format($maxDiscountPerItem, 2) . " Tsh kwa kila {$bidhaa->jina}", 'notification' => 'Punguzo limepita kiasi!'], 422);
                }
            }

            $jumla = $baseTotal - $actualDiscount;

            Mauzo::create([
                'company_id'   => $companyId,
                'receipt_no'   => $receiptNo,
                'bidhaa_id'    => $bidhaa->id,
                'idadi'        => $item['idadi'],
                'bei'          => $item['bei'],
                'punguzo'      => $discount,
                'punguzo_aina' => $discountType,
                'jumla'        => $jumla,
                'lipa_kwa'     => $paymentMethod,
                'lipa_kwa_type' => ($paymentMethod === 'cash') ? null : $paymentType, // ADD THIS
                'mteja_id'     => $request->mteja_id,
            ]);

            $bidhaa->decrement('idadi', $item['idadi']);
        }

        DB::commit();
        
        // Send SMS receipt if requested
        if ($sendReceipt && $sendToPhone) {
            // Get all sales with this receipt number to send full receipt
            $sales = Mauzo::with('bidhaa')
                ->where('company_id', $companyId)
                ->where('receipt_no', $receiptNo)
                ->get();
            
            if ($sales->isNotEmpty()) {
                $this->sendKikapuReceiptSms($sales, $sendToPhone, $companyId);
            }
        }
        
        return response()->json(['success' => true, 'message' => 'Mauzo ya kikapu yamehifadhiwa kikamilifu!', 'notification' => 'Kikapu kimefanikiwa!', 'receipt_no' => $receiptNo]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['success' => false, 'message' => 'Kuna tatizo kwenye kuhifadhi mauzo: ' . $e->getMessage(), 'notification' => 'Kuna tatizo!'], 500);
    }
}

/**
 * Send SMS receipt for kikapu sales
 */
private function sendKikapuReceiptSms($sales, $phoneNumber, $companyId)
{
    try {
        $company = \App\Models\Company::find($companyId);
        
        // Format phone number
        $phone = preg_replace('/[^0-9]/', '', $phoneNumber);
        if (substr($phone, 0, 1) == '0') $phone = '255' . substr($phone, 1);
        if (substr($phone, 0, 1) == '7') $phone = '255' . $phone;
        if (strlen($phone) != 12) {
            \Log::warning("Invalid phone number format for kikapu receipt: {$phone}");
            return false;
        }
        
        $receiptNo = $sales->first()->receipt_no;
        $websiteUrl = 'www.mauzosheetai.co.tz';
        
        // Build SMS message
        $message = "";
        if ($company && $company->company_name) {
            $message .= strtoupper($company->company_name) . "\n";
        }
        $message .= "RISITI: " . $receiptNo . "\n";
        $message .= "Tarehe: " . now()->format('d/m/Y H:i') . "\n";
        $message .= str_repeat("-", 25) . "\n";
        
        $subtotal = 0;
        $totalPunguzo = 0;
        
        foreach ($sales as $item) {
            $message .= $item->bidhaa->jina . "\n";
            $message .= "  " . number_format($item->idadi, 2) . " x " . number_format($item->bei, 0) . " = " . number_format($item->jumla, 0) . "\n";
            
            $discountAmount = ($item->punguzo_aina === 'bidhaa') 
                ? $item->punguzo * $item->idadi 
                : $item->punguzo;
            
            if ($discountAmount > 0) {
                $message .= "  Punguzo: -" . number_format($discountAmount, 0) . "\n";
                $totalPunguzo += $discountAmount;
            }
            
            $subtotal += $item->bei * $item->idadi;
        }
        
        $message .= str_repeat("-", 25) . "\n";
        
        if ($totalPunguzo > 0) {
            $message .= "Jumla Ndogo: " . number_format($subtotal, 0) . "\n";
            $message .= "Punguzo: -" . number_format($totalPunguzo, 0) . "\n";
            $message .= str_repeat("-", 25) . "\n";
        }
        
        $total = $sales->sum('jumla');
        $message .= "JUMLA: " . number_format($total, 0) . "/=\n";
        
        $paymentMethod = $sales->first()->lipa_kwa ?? 'cash';
        $paymentText = match($paymentMethod) {
            'cash' => 'CASH',
            'lipa_namba' => 'LIPA NAMBA',
            'bank' => 'BENKI',
            default => strtoupper($paymentMethod)
        };
        $message .= "Malipo: " . $paymentText . "\n";
        $message .= str_repeat("-", 25) . "\n";
        $message .= "ASANTE KWA KUNUNUA!\n";
        $message .= $websiteUrl . "\n";
        
        // Send SMS using your SMSService
        $result = $this->smsService->sendSms($phone, $message, 'RECEIPT_' . $receiptNo);
        
        \Log::info("Kikapu SMS sent for receipt {$receiptNo} to {$phone}: " . ($result['success'] ? 'Success' : 'Failed'));
        
        return $result['success'] ?? false;
        
    } catch (\Exception $e) {
        \Log::error('Error sending kikapu receipt SMS: ' . $e->getMessage());
        return false;
    }
}

    // ------------------- KIKAPU LOAN (multiple products) -------------------
    public function storeKikapuLoan(Request $request)
    {
        $user = $this->getAuthUser();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access', 'notification' => 'Unauthorized!'], 401);
        }
        $companyId = $user->company_id;
        $items = json_decode($request->items, true);

        $validator = Validator::make($request->all(), [
            'jina_mkopaji' => 'required|string|max:255',
            'simu' => 'required|string|max:20',
            'tarehe_malipo' => 'required|date',
            'mteja_id' => 'nullable|exists:mtejas,id', 
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first(), 'notification' => 'Kosa katika taarifa!'], 422);
        }

        try {
            DB::beginTransaction();

            $mteja = Mteja::firstOrCreate([
                'jina' => $request->jina_mkopaji,
                'simu' => $request->simu,
                'company_id' => $companyId,
            ], [
                'barua_pepe' => $request->barua_pepe ?? null,
                'anapoishi' => $request->anapoishi ?? null,
            ]);

            foreach ($items as $item) {
                $bidhaa = Bidhaa::where('id', $item['bidhaa_id'])->where('company_id', $companyId)->first();
                if (!$bidhaa) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => "Bidhaa {$item['jina']} haipatikani", 'notification' => 'Bidhaa haipo!'], 404);
                }

                $quantity = $item['idadi'];
                $price = $item['bei'];
                $discount = $item['punguzo'] ?? 0;
                $discountType = $item['punguzo_aina'] ?? 'bidhaa';

                if ($quantity > $bidhaa->idadi) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => "Stock haitoshi kwa {$bidhaa->jina}, baki ni {$bidhaa->idadi}", 'notification' => 'Stock haitoshi!'], 422);
                }

                $baseTotal = $price * $quantity;
                $actualDiscount = ($discountType === 'bidhaa') ? $discount * $quantity : $discount;
                $profit = ($price - $bidhaa->bei_nunua) * $quantity;

                // Validate discount
                if ($discountType === 'jumla') {
                    if ($discount > $baseTotal) {
                        DB::rollBack();
                        return response()->json(['success' => false, 'message' => "Punguzo haliruhusiwi kuzidi jumla ya " . number_format($baseTotal, 2) . " Tsh", 'notification' => 'Punguzo limepita kiasi!'], 422);
                    }
                    if ($discount > $profit) {
                        DB::rollBack();
                        return response()->json(['success' => false, 'message' => "Punguzo la jumla haliruhusiwi kuzidi faida ya " . number_format($profit, 2) . " Tsh", 'notification' => 'Punguzo limezidi faida!'], 422);
                    }
                } else {
                    $maxDiscountPerItem = $price - $bidhaa->bei_nunua;
                    if ($discount > $maxDiscountPerItem) {
                        DB::rollBack();
                        return response()->json(['success' => false, 'message' => "Punguzo haliruhusiwi kuzidi faida ya " . number_format($maxDiscountPerItem, 2) . " Tsh kwa kila bidhaa", 'notification' => 'Punguzo limepita kiasi!'], 422);
                    }
                }

                $jumla = $baseTotal - $actualDiscount;
                $bidhaa->decrement('idadi', $quantity);

                Madeni::create([
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
                    'mteja_id' => $request->mteja_id, 
                ]);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Bidhaa zimekopeshwa kwa mafanikio!', 'notification' => 'Mikopo imefanikiwa!']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Kuna tatizo kwenye kukopesha bidhaa: ' . $e->getMessage(), 'notification' => 'Kuna tatizo!'], 500);
        }
    }

// ------------------- DELETE SALE -------------------
public function destroy($id)
{
    $user = $this->getAuthUser();
    if (!$user) {
        return response()->json(['success' => false, 'message' => 'Unauthorized access', 'notification' => 'Unauthorized!'], 401);
    }
    
    // ADD PERMISSION CHECK HERE
    $isMfanyakazi = Auth::guard('mfanyakazi')->check();
    $hasFullAccess = true;
    
    if($isMfanyakazi) {
        $mfanyakaziUser = Auth::guard('mfanyakazi')->user();
        $hasFullAccess = ($mfanyakaziUser->uwezo ?? 'mdogo') === 'mkubwa';
        
        // Block delete if mfanyakazi mdogo
        if(!$hasFullAccess) {
            return response()->json([
                'success' => false, 
                'message' => 'Huna ruhusa ya kufuta mauzo! Wasiliana na msimamizi wako.', 
                'notification' => 'Huna ruhusa!'
            ], 403);
        }
    }
    
    $companyId = $user->company_id;
    $mauzo = Mauzo::where('id', $id)->where('company_id', $companyId)->first();
    if (!$mauzo) {
        return response()->json(['success' => false, 'message' => 'Rekodi ya mauzo haipatikani', 'notification' => 'Rekodi haipo!'], 404);
    }

    try {
        DB::beginTransaction();
        $bidhaa = $mauzo->bidhaa;
        if ($bidhaa) $bidhaa->increment('idadi', $mauzo->idadi);
        $mauzo->delete();
        DB::commit();
        return response()->json(['success' => true, 'message' => 'Rekodi ya mauzo imefutwa kikamilifu! Stock imerudishwa.', 'notification' => 'Rekodi imefutwa! Stock imerudishwa.', 'stock_restored' => $mauzo->idadi, 'product_name' => $bidhaa->jina ?? 'Unknown']);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['success' => false, 'message' => 'Kuna tatizo katika kufuta mauzo: ' . $e->getMessage(), 'notification' => 'Kuna tatizo!'], 500);
    }
}

    // ------------------- RECEIPT PRINTING / DATA -------------------
    public function getReceiptForPrint($receiptNo)
    {
        $user = $this->getAuthUser();
        if (!$user) return response()->json(['success' => false, 'message' => 'Unauthorized access'], 401);
        $companyId = $user->company_id;
        $sales = Mauzo::with('bidhaa')->where('company_id', $companyId)->where('receipt_no', $receiptNo)->get();
        if ($sales->isEmpty()) return response()->json(['success' => false, 'message' => 'Hakuna taarifa za risiti hii'], 404);
        Mauzo::where('receipt_no', $receiptNo)->where('company_id', $companyId)->increment('reprint_count');
        $items = $sales->map(fn($sale) => [
            'bidhaa' => $sale->bidhaa->jina ?? 'Unknown',
            'idadi' => $sale->idadi,
            'bei' => $sale->bei,
            'punguzo' => $sale->punguzo,
            'punguzo_aina' => $sale->punguzo_aina,
            'jumla' => $sale->jumla
        ]);
        $total = $sales->sum('jumla');
        $totalPunguzo = $sales->sum('punguzo');
        $subtotal = $total + $totalPunguzo;
        return response()->json(['success' => true, 'receipt_no' => $receiptNo, 'items' => $items, 'subtotal' => $subtotal, 'punguzo' => $totalPunguzo, 'total' => $total, 'date' => $sales->first()->created_at->format('d/m/Y H:i'), 'reprint_count' => $sales->first()->reprint_count + 1]);
    }

    public function printThermalReceipt($receiptNo)
    {
        $user = $this->getAuthUser();
        if (!$user) return response()->json(['success' => false, 'message' => 'Unauthorized access'], 401);
        $companyId = $user->company_id;
        $company = \App\Models\Company::find($companyId);
        if (!$company) {
            $company = (object) ['company_name' => $user->company_name ?? 'BIASHARA YANGU', 'location' => $user->location ?? '', 'region' => $user->region ?? '', 'phone' => $user->phone ?? '', 'email' => $user->email ?? '', 'owner_name' => $user->name ?? ''];
        }
        $sales = Mauzo::with('bidhaa')->where('company_id', $companyId)->where('receipt_no', $receiptNo)->get();
        if ($sales->isEmpty()) return response()->json(['success' => false, 'message' => 'Hakuna taarifa za risiti hii'], 404);
        $date = $sales->first()->created_at->format('d/m/Y H:i');
        $total = $sales->sum('jumla');
        $totalPunguzo = $sales->sum('punguzo');
        $subtotal = $total + $totalPunguzo;
        return view('mauzo.thermal-receipt', compact('receiptNo', 'sales', 'date', 'subtotal', 'totalPunguzo', 'total', 'company'));
    }

    public function getReceiptData($receiptNo)
    {
        $user = $this->getAuthUser();
        if (!$user) return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        $companyId = $user->company_id;
        $receiptNo = urldecode(trim($receiptNo));
        $sales = Mauzo::with('bidhaa')->where('company_id', $companyId)->where('receipt_no', $receiptNo)->get();
        if ($sales->isEmpty()) {
            if (strpos($receiptNo, 'MS-') === 0) {
                $receiptNo = substr($receiptNo, 3);
                $sales = Mauzo::with('bidhaa')->where('company_id', $companyId)->where('receipt_no', 'like', '%' . $receiptNo . '%')->get();
            }
            if ($sales->isEmpty()) return response()->json(['success' => false, 'message' => 'Receipt not found'], 404);
        }
        $items = $sales->map(fn($sale) => ['bidhaa' => $sale->bidhaa->jina ?? 'Unknown', 'idadi' => $sale->idadi, 'bei' => $sale->bei, 'punguzo' => $sale->punguzo, 'jumla' => $sale->jumla]);
        $total = $sales->sum('jumla');
        $totalPunguzo = $sales->sum('punguzo');
        return response()->json(['success' => true, 'receipt_no' => $sales->first()->receipt_no, 'items' => $items, 'total' => $total, 'punguzo' => $totalPunguzo, 'date' => $sales->first()->created_at->format('d/m/Y H:i'), 'item_count' => $sales->count()]);
    }

    public function searchReceipts(Request $request)
    {
        $user = $this->getAuthUser();
        if (!$user) return response()->json(['success' => false, 'message' => 'Unauthorized access'], 401);
        $companyId = $user->company_id;
        $searchTerm = $request->input('search', '');
        $receipts = Mauzo::where('company_id', $companyId)->whereNotNull('receipt_no')->where('receipt_no', 'like', "%{$searchTerm}%")
            ->select('receipt_no', DB::raw('count(*) as item_count'), DB::raw('sum(jumla) as total'), DB::raw('max(created_at) as last_date'))
            ->groupBy('receipt_no')->orderBy('last_date', 'desc')->limit(20)->get();
        return response()->json(['success' => true, 'receipts' => $receipts]);
    }

    // ------------------- PRODUCT BY BARCODE -------------------
    public function getProductByBarcode($barcode)
    {
        $user = $this->getAuthUser();
        if (!$user) return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        $companyId = $user->company_id;
        $product = Bidhaa::where('barcode', $barcode)->where('company_id', $companyId)->first();
        if (!$product) return response()->json(['success' => false, 'message' => 'Bidhaa haipatikani kwa barcode hii'], 404);
        return response()->json(['success' => true, 'product' => ['id' => $product->id, 'jina' => $product->jina, 'bei_kuuza' => $product->bei_kuuza, 'bei_nunua' => $product->bei_nunua, 'idadi' => $product->idadi, 'aina' => $product->aina, 'kipimo' => $product->kipimo, 'barcode' => $product->barcode]]);
    }

// ------------------- FILTERED SALES (AJAX) -------------------
public function getFilteredSales(Request $request)
{
    $user = $this->getAuthUser();
    if (!$user) return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
    $companyId = $user->company_id;
    
    // ADD PERMISSION CHECK HERE
    $isMfanyakazi = Auth::guard('mfanyakazi')->check();
    $hasFullAccess = true;
    
    if($isMfanyakazi) {
        $mfanyakaziUser = Auth::guard('mfanyakazi')->user();
        $hasFullAccess = ($mfanyakaziUser->uwezo ?? 'mdogo') === 'mkubwa';
    }
    
    $query = Mauzo::with('bidhaa')->where('company_id', $companyId);
    
    if ($request->search) {
        $search = $request->search;
        $query->whereHas('bidhaa', fn($q) => $q->where('jina', 'like', "%{$search}%"))
              ->orWhere('receipt_no', 'like', "%{$search}%")
              ->orWhere('lipa_kwa', 'like', "%{$search}%");
    }
    if ($request->start_date) $query->whereDate('created_at', '>=', $request->start_date);
    if ($request->end_date) $query->whereDate('created_at', '<=', $request->end_date);
    
    $sales = $query->orderBy('created_at', 'desc')->get();
    $html = '';
    $today = Carbon::today()->format('Y-m-d');
    
    foreach ($sales as $item) {
        $itemDate = $item->created_at->format('Y-m-d');
        $buyingPrice = $item->bidhaa->bei_nunua ?? 0;
        $actualDiscount = $this->actualDiscount($item);
        $faida = (($item->bei - $buyingPrice) * $item->idadi) - $actualDiscount;
        $total = $item->jumla;
        
        $paymentMethod = match($item->lipa_kwa) {
            'cash' => '<span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Cash</span>',
            'lipa_namba' => '<span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">Lipa Namba</span>',
            'bank' => '<span class="bg-purple-100 text-purple-800 px-2 py-1 rounded text-xs">Bank</span>',
            default => '<span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs">Cash</span>',
        };
        
        // Build product column with aina and kipimo
        $productColumn = '<div class="flex flex-col">
            <span class="font-medium">' . e($item->bidhaa->jina) . '</span>';
        if($item->bidhaa->aina || $item->bidhaa->kipimo) {
            $productColumn .= '<div class="flex flex-wrap gap-1 mt-1">';
            if($item->bidhaa->aina) {
                $productColumn .= '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-blue-100 text-blue-700">
                    <i class="fas fa-tag mr-1"></i>' . e($item->bidhaa->aina) . '</span>';
            }
            if($item->bidhaa->kipimo) {
                $productColumn .= '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-green-100 text-green-700">
                    <i class="fas fa-ruler mr-1"></i>' . e($item->bidhaa->kipimo) . '</span>';
            }
            $productColumn .= '</div>';
        }
        $productColumn .= '</div>';
        
        // Build actions column - MODIFIED: Only show delete button if hasFullAccess
        $actionsColumn = '<div class="flex gap-1 justify-center">';
        if($item->receipt_no) {
            $actionsColumn .= '<button type="button" class="print-single-receipt bg-blue-200 hover:bg-blue-400 text-gray-700 px-2 py-1 rounded-lg text-xs" data-receipt-no="' . $item->receipt_no . '">
                <i class="fas fa-print mr-1"></i>
            </button>';
        }
        
        // ONLY ADD DELETE BUTTON IF USER HAS FULL ACCESS
        if($hasFullAccess) {
            $actionsColumn .= '<button type="button" class="delete-sale-btn bg-red-200 hover:bg-red-400 text-gray-700 px-2 py-1 rounded-lg text-xs" 
                data-id="' . $item->id . '" 
                data-product-name="' . e($item->bidhaa->jina) . '" 
                data-quantity="' . $item->idadi . '">
                <i class="fas fa-trash mr-1"></i>
            </button>';
        } else {
            // Show disabled lock button for mfanyakazi mdogo
            $actionsColumn .= '<button type="button" class="bg-gray-200 text-gray-500 px-2 py-1 rounded-lg text-xs cursor-not-allowed" disabled title="Huna ruhusa ya kufuta">
                <i class="fas fa-lock mr-1"></i>
            </button>';
        }
        $actionsColumn .= '</div>';
        
        $html .= '<tr class="sales-row" data-product="'.strtolower($item->bidhaa->jina).'" data-date="'.$itemDate.'" data-id="'.$item->id.'">
            <td class="border px-3 py-2">'.($itemDate === $today ? '<span class="bg-green-100 text-green-800 px-2 py-1 rounded font-semibold text-xs whitespace-nowrap">Leo '.$item->created_at->format('H:i:s').'</span>' : '<span class="whitespace-nowrap">'.$item->created_at->format('d/m/Y H:i:s').'</span>').'</td>
            <td class="border px-3 py-2 font-mono">'.($item->receipt_no ? '<span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs copy-receipt cursor-pointer" data-receipt="'.$item->receipt_no.'">'.substr($item->receipt_no, -8).'</span>' : '<span class="text-gray-400 text-xs">-</span>').'</td>
            <td class="border px-3 py-2">'.$productColumn.'</td>
            <td class="border px-3 py-2 text-center">'.number_format($item->idadi, 2).'</td>
            <td class="border px-3 py-2 text-right">'.number_format($item->bei, 2).'</td>
            <td class="border px-3 py-2 text-right">'.number_format($actualDiscount, 2).'</td>
            <td class="border px-3 py-2 text-right">'.number_format($faida, 2).'</td>
            <td class="border px-3 py-2 text-center">'.$paymentMethod.'</td>
            <td class="border px-3 py-2 text-right">'.number_format($total, 2).'</td>
            <td class="border px-3 py-2 text-center">'.$actionsColumn.'</td>
        <tr>';
    }
    
    if (!$html) {
        $colspan = 10;
        $html = '<tr><td colspan="'.$colspan.'" class="text-center py-4 text-gray-500">Hakuna mauzo yaliyopatikana kwenye filter hii.</td></tr>';
    }
    
    return response()->json(['success' => true, 'html' => $html]);
}

    // ------------------- SEND RECEIPT SMS -------------------
    public function sendReceiptSmsSimple(Request $request)
    {
        try {
            $phone = $request->input('phone');
            $receiptNo = $request->input('receipt_no');
            if (empty($phone) || empty($receiptNo)) {
                return response()->json(['success' => false, 'message' => 'Namba ya simu na namba ya risiti inahitajika']);
            }
            $phone = preg_replace('/[^0-9]/', '', $phone);
            if (substr($phone, 0, 1) == '0') $phone = '255' . substr($phone, 1);
            if (strlen($phone) != 12) {
                return response()->json(['success' => false, 'message' => 'Namba ya simu si sahihi. Tumia 255XXXXXXXXX']);
            }
            $user = $this->getAuthUser();
            if (!$user) return response()->json(['success' => false, 'message' => 'Unauthorized access'], 401);
            $companyId = $user->company_id;
            $company = \App\Models\Company::find($companyId);
            $sales = Mauzo::with('bidhaa')->where('company_id', $companyId)->where('receipt_no', $receiptNo)->get();
            if ($sales->isEmpty()) return response()->json(['success' => false, 'message' => 'Risiti haipatikani']);
            $subtotal = $sales->sum('jumla') + $sales->sum('punguzo');
            $totalPunguzo = $sales->sum(fn($sale) => $sale->punguzo_aina === 'bidhaa' ? $sale->punguzo * $sale->idadi : $sale->punguzo);
            $total = $sales->sum('jumla');
            $paymentMethod = $sales->first()->lipa_kwa ?? 'cash';
            $message = "";
            if ($company && $company->company_name) $message .= strtoupper($company->company_name) . "\n";
            $message .= "RISITI: " . $receiptNo . "\n";
            $message .= "Tarehe: " . now()->format('d/m/Y H:i') . "\n";
            $message .= str_repeat("-", 25) . "\n";
            foreach ($sales as $sale) {
                $message .= $sale->bidhaa->jina . "\n";
                $message .= "  " . number_format($sale->idadi, 2) . " x " . number_format($sale->bei, 0) . " = " . number_format($sale->jumla, 0) . "\n";
                if ($sale->punguzo > 0) {
                    $discountAmount = $sale->punguzo_aina === 'bidhaa' ? $sale->punguzo * $sale->idadi : $sale->punguzo;
                    $message .= "  Punguzo: -" . number_format($discountAmount, 0) . "\n";
                }
            }
            $message .= str_repeat("-", 25) . "\n";
            if ($totalPunguzo > 0) {
                $message .= "Jumla Ndogo: " . number_format($subtotal, 0) . "\n";
                $message .= "Punguzo: -" . number_format($totalPunguzo, 0) . "\n";
                $message .= str_repeat("-", 25) . "\n";
            }
            $message .= "JUMLA: " . number_format($total, 0) . "/=\n";
            $paymentText = match($paymentMethod) { 'cash' => 'CASH', 'lipa_namba' => 'LIPA NAMBA', 'bank' => 'BENKI', default => strtoupper($paymentMethod) };
            $message .= "Malipo: " . $paymentText . "\n";
            $message .= str_repeat("-", 25) . "\n";
            $message .= "ASANTE KWA KUNUNUA!\n";
            $result = $this->smsService->sendSms($phone, $message, 'RECEIPT_' . $receiptNo);
            return response()->json($result);
        } catch (\Exception $e) {
            \Log::error('Send receipt SMS error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Hitilafu: ' . $e->getMessage()]);
        }
    }
}