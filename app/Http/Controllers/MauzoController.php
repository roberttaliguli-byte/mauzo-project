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
    // -----------------------------
    //  Display main sales page
    // -----------------------------
    public function index()
    {
    $user = Auth::user() ?? Auth::guard('mfanyakazi')->user();
    $companyId = $user->company_id;
    
        $bidhaa = Bidhaa::where('company_id', $companyId)
            ->select('id', 'jina', 'bei_kuuza', 'idadi', 'barcode', 'aina', 'kipimo')
            ->orderBy('jina')
            ->get();

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
            
        return view('mauzo.index', compact('bidhaa', 'mauzos', 'matumizi', 'wateja', 'madeni','marejeshos'));
    }

    // -----------------------------
    //  Store sale (normal or loan)
    // -----------------------------
    public function store(Request $request)
    {
        if ($request->has('kopesha')) {
            return $this->storeLoan($request);
        }

        return $this->storeRegularSale($request);
    }

    // -----------------------------
    //  Store sale via barcode
    // -----------------------------
    public function storeBarcode(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $items = $request->input('items', []);

        return DB::transaction(function () use ($items, $companyId, $request) {
            $results = [];
            $saleItems = [];

            foreach ($items as $item) {
                $validated = Validator::make($item, [
                    'barcode' => 'required|string|exists:bidhaas,barcode',
                    'idadi'   => 'required|integer|min:1',
                    'punguzo' => 'nullable|numeric',
                ])->validate();

                $bidhaa = Bidhaa::where('barcode', $validated['barcode'])
                    ->where('company_id', $companyId)
                    ->firstOrFail();

                if ($bidhaa->expiry < now()->toDateString()) {
                    return response()->json(['message' => "Bidhaa {$bidhaa->jina} ime-expire."], 422);
                }

                if ($validated['idadi'] > $bidhaa->idadi) {
                    return response()->json(['message' => "Stock haijatosha kwa {$bidhaa->jina}, baki ni {$bidhaa->idadi}."], 422);
                }

                $jumla = ($bidhaa->bei_kuuza * $validated['idadi']) - ($validated['punguzo'] ?? 0);
                $bidhaa->decrement('idadi', $validated['idadi']);

                $mauzo = Mauzo::create([
                    'company_id' => $companyId,
                    'bidhaa_id'  => $bidhaa->id,
                    'idadi'      => $validated['idadi'],
                    'bei'        => $bidhaa->bei_kuuza,
                    'punguzo'    => $validated['punguzo'] ?? 0,
                    'jumla'      => $jumla,
                ]);

                // Add item for receipt
                $saleItems[] = [
                    'jina' => $bidhaa->jina,
                    'idadi' => $validated['idadi'],
                    'bei' => $bidhaa->bei_kuuza,
                    'punguzo' => $validated['punguzo'] ?? 0,
                    'jumla' => $jumla
                ];

                $results[] = $mauzo;
            }

            // Prepare response with receipt data if needed
            $response = [
                'message' => 'Mauzo yamerekodiwa kikamilifu!',
                'mauzo' => $results,
                'success' => true
            ];

            // Add receipt data if print_receipt is requested
            if ($request->has('print_receipt') && $request->print_receipt == '1') {
                $totalAmount = array_sum(array_column($saleItems, 'jumla'));
                $response['receipt_data'] = [
                    'receipt_number' => 'RC' . str_pad($results[0]->id, 5, '0', STR_PAD_LEFT),
                    'items' => $saleItems,
                    'total_amount' => $totalAmount,
                    'date' => Carbon::now()->format('Y-m-d H:i:s'),
                    'user_name' => Auth::user()->name,
                    'company_name' => Auth::user()->company->name ?? 'DEMODAY STORE'
                ];
            }

            return response()->json($response);

        });
    }

    // -----------------------------
    //  Delete sale
    // -----------------------------
    public function destroy($id)
    {
        $companyId = Auth::user()->company_id;
        $mauzo = Mauzo::where('id', $id)
            ->where('company_id', $companyId)
            ->firstOrFail();

        DB::transaction(function () use ($mauzo) {
            $bidhaa = $mauzo->bidhaa;
            if ($bidhaa) {
                $bidhaa->increment('idadi', $mauzo->idadi);
            }

            $mauzo->delete();
        });

        return redirect()->back()->with('success', 'Rekodi ya mauzo imefutwa kikamilifu!');
    }

    // -----------------------------
    //  Handle loan sales
    // -----------------------------
    private function storeLoan(Request $request)
    {
        $companyId = Auth::user()->company_id;

        return DB::transaction(function () use ($request, $companyId) {
            $request->validate([
                'bidhaa_id'      => 'required|exists:bidhaas,id',
                'idadi'          => 'required|integer|min:1',
                'bei'            => 'required|numeric',
                'jumla'          => 'required|numeric',
                'jina_mkopaji'   => 'required|string|max:255',
                'simu'           => 'required|string|max:20',
                'tarehe_malipo'  => 'required|date',
            ]);

            $bidhaa = Bidhaa::where('id', $request->bidhaa_id)
                ->where('company_id', $companyId)
                ->firstOrFail();

            if ($bidhaa->expiry < now()->toDateString()) {
                return redirect()->back()->withErrors(['bidhaa_id' => "Bidhaa hii ime-expire."]);
            }

            if ($request->idadi > $bidhaa->idadi) {
                return redirect()->back()->withErrors(['idadi' => "Stock haijatosha, baki ni {$bidhaa->idadi}."]);
            }

            $bidhaa->decrement('idadi', $request->idadi);

            $deni = Madeni::create([
                'company_id'    => $companyId,
                'bidhaa_id'     => $request->bidhaa_id,
                'idadi'         => $request->idadi,
                'bei'           => $request->bei,
                'jumla'         => $request->jumla,
                'jina_mkopaji'  => $request->jina_mkopaji,
                'simu'          => $request->simu,
                'tarehe_malipo' => $request->tarehe_malipo,
                'baki'          => $request->jumla,
            ]);

            // Return JSON for AJAX if requested
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Deni limerekodiwa kikamilifu!',
                    'receipt_data' => $request->has('print_receipt') ? [
                        'receipt_number' => 'DL' . str_pad($deni->id, 5, '0', STR_PAD_LEFT),
                        'items' => [[
                            'jina' => $bidhaa->jina,
                            'idadi' => $request->idadi,
                            'bei' => $request->bei,
                            'jumla' => $request->jumla
                        ]],
                        'total_amount' => $request->jumla,
                        'date' => Carbon::now()->format('Y-m-d H:i:s'),
                        'user_name' => Auth::user()->name,
                        'company_name' => Auth::user()->company->name ?? 'DEMODAY STORE',
                        'customer_name' => $request->jina_mkopaji,
                        'customer_phone' => $request->simu,
                        'payment_date' => $request->tarehe_malipo,
                        'is_loan' => true
                    ] : null
                ]);
            }

            return redirect()->back()->with('success', 'Deni limerekodiwa kikamilifu!');
        });
    }

    // -----------------------------
    //  Handle regular sales
    // -----------------------------
    private function storeRegularSale(Request $request)
    {
        $companyId = Auth::user()->company_id;

        return DB::transaction(function () use ($request, $companyId) {

            $validator = Validator::make($request->all(), [
                'bidhaa_id' => 'required|exists:bidhaas,id',
                'idadi'     => 'required|integer|min:1',
                'bei'       => 'required|numeric',
                'punguzo'   => 'nullable|numeric',
                'jumla'     => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $bidhaa = Bidhaa::where('id', $request->bidhaa_id)
                ->where('company_id', $companyId)
                ->first();

            if (!$bidhaa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bidhaa haipatikani'
                ], 404);
            }

            if ($bidhaa->expiry < now()->toDateString()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bidhaa hii ime-expire'
                ], 422);
            }

            if ($request->idadi > $bidhaa->idadi) {
                return response()->json([
                    'success' => false,
                    'message' => "Stock haijatosha, baki ni {$bidhaa->idadi}"
                ], 422);
            }

            $bidhaa->decrement('idadi', $request->idadi);

            $mauzo = Mauzo::create([
                'company_id' => $companyId,
                'bidhaa_id'  => $request->bidhaa_id,
                'idadi'      => $request->idadi,
                'bei'        => $request->bei,
                'punguzo'    => $request->punguzo ?? 0,
                'jumla'      => $request->jumla,
            ]);

            $response = [
                'success' => true,
                'message' => 'Mauzo yamerekodiwa kikamilifu!',
                'sale_id' => $mauzo->id
            ];

            // Add receipt data if print_receipt is requested
            if ($request->has('print_receipt') && $request->print_receipt == '1') {
                $response['receipt_data'] = [
                    'receipt_number' => 'RC' . str_pad($mauzo->id, 5, '0', STR_PAD_LEFT),
                    'items' => [[
                        'jina' => $bidhaa->jina,
                        'idadi' => $request->idadi,
                        'bei' => $request->bei,
                        'punguzo' => $request->punguzo ?? 0,
                        'jumla' => $request->jumla
                    ]],
                    'total_amount' => $request->jumla,
                    'date' => Carbon::now()->format('Y-m-d H:i:s'),
                    'user_name' => Auth::user()->name,
                    'company_name' => Auth::user()->company->name ?? 'DEMODAY STORE'
                ];
            }

            return response()->json($response);
        });
    }

    // -----------------------------
    //  Handle basket (kikapu) sales
    // -----------------------------
    public function storeKikapu(Request $request)
    {
        $companyId = Auth::user()->company_id;

        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.jina' => 'required|string',
            'items.*.bei' => 'required|numeric',
            'items.*.idadi' => 'required|integer|min:1',
            'items.*.punguzo' => 'nullable|numeric',
            'items.*.faida' => 'nullable|numeric',
        ]);

        $saleItems = [];
        $totalAmount = 0;
        $saleIds = [];

        foreach ($validated['items'] as $item) {
            $bidhaa = Bidhaa::where('jina', $item['jina'])
                ->where('company_id', $companyId)
                ->first();

            if ($bidhaa) {
                $jumla = ($item['bei'] * $item['idadi']) - ($item['punguzo'] ?? 0);

                $mauzo = Mauzo::create([
                    'company_id' => $companyId,
                    'bidhaa_id'  => $bidhaa->id,
                    'idadi'      => $item['idadi'],
                    'bei'        => $item['bei'],
                    'punguzo'    => $item['punguzo'] ?? 0,
                    'jumla'      => $jumla,
                ]);

                $bidhaa->update([
                    'idadi' => max(0, $bidhaa->idadi - $item['idadi']),
                ]);

                // Collect items for receipt
                $saleItems[] = [
                    'jina' => $bidhaa->jina,
                    'idadi' => $item['idadi'],
                    'bei' => $item['bei'],
                    'punguzo' => $item['punguzo'] ?? 0,
                    'jumla' => $jumla
                ];

                $totalAmount += $jumla;
                $saleIds[] = $mauzo->id;
            }
        }

        $response = [
            'status' => 'success',
            'message' => 'Mauzo ya kikapu yamehifadhiwa kikamilifu!',
        ];

        // Add receipt data if print_receipt is requested
        if ($request->has('print_receipt') && $request->print_receipt == '1') {
            $response['receipt_data'] = [
                'receipt_number' => 'RC' . str_pad($saleIds[0], 5, '0', STR_PAD_LEFT),
                'items' => $saleItems,
                'total_amount' => $totalAmount,
                'date' => Carbon::now()->format('Y-m-d H:i:s'),
                'user_name' => Auth::user()->name,
                'company_name' => Auth::user()->company->name ?? 'DEMODAY STORE'
            ];
        }

        return response()->json($response);
    }

    // -----------------------------
    //  Handle basket loans
    // -----------------------------
    public function storeKikapuLoan(Request $request)
    {
        $companyId = Auth::user()->company_id;

        $validated = $request->validate([
            'jina' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.jina' => 'required|string',
            'items.*.bei' => 'required|numeric',
            'items.*.idadi' => 'required|integer|min:1',
            'items.*.punguzo' => 'nullable|numeric',
        ]);

        $mteja = Mteja::firstOrCreate([
            'jina' => $validated['jina'],
            'company_id' => $companyId,
        ]);

        $loanItems = [];
        $totalAmount = 0;
        $loanIds = [];

        foreach ($validated['items'] as $item) {
            $bidhaa = Bidhaa::where('jina', $item['jina'])
                ->where('company_id', $companyId)
                ->first();

            if ($bidhaa) {
                $jumla = ($item['bei'] * $item['idadi']) - ($item['punguzo'] ?? 0);

                $bidhaa->update([
                    'idadi' => max(0, $bidhaa->idadi - $item['idadi']),
                ]);

                $deni = Madeni::create([
                    'company_id'    => $companyId,
                    'bidhaa_id'     => $bidhaa->id,
                    'idadi'         => $item['idadi'],
                    'bei'           => $item['bei'],
                    'jumla'         => $jumla,
                    'jina_mkopaji'  => $validated['jina'],
                    'simu'          => '',
                    'tarehe_malipo' => now()->addDays(7),
                    'baki'          => $jumla,
                ]);

                // Collect items for receipt
                $loanItems[] = [
                    'jina' => $bidhaa->jina,
                    'idadi' => $item['idadi'],
                    'bei' => $item['bei'],
                    'punguzo' => $item['punguzo'] ?? 0,
                    'jumla' => $jumla
                ];

                $totalAmount += $jumla;
                $loanIds[] = $deni->id;
            }
        }

        $response = [
            'status' => 'success',
            'message' => 'Bidhaa zimekopeshwa kwa mafanikio!',
        ];

        // Add receipt data if print_receipt is requested
        if ($request->has('print_receipt') && $request->print_receipt == '1') {
            $response['receipt_data'] = [
                'receipt_number' => 'DL' . str_pad($loanIds[0], 5, '0', STR_PAD_LEFT),
                'items' => $loanItems,
                'total_amount' => $totalAmount,
                'date' => Carbon::now()->format('Y-m-d H:i:s'),
                'user_name' => Auth::user()->name,
                'company_name' => Auth::user()->company->name ?? 'DEMODAY STORE',
                'customer_name' => $validated['jina'],
                'is_loan' => true
            ];
        }

        return response()->json($response);
    }

    // -----------------------------
    //  Display receipt for printing
    // -----------------------------
    public function showReceipt($id)
    {
        $companyId = Auth::user()->company_id;
        
        $mauzo = Mauzo::with('bidhaa')
            ->where('id', $id)
            ->where('company_id', $companyId)
            ->firstOrFail();

        $receiptNumber = 'RC' . str_pad($mauzo->id, 5, '0', STR_PAD_LEFT);

        return view('mauzo.receipt-print', [
            'mauzo' => $mauzo,
            'receipt_number' => $receiptNumber,
            'date' => Carbon::parse($mauzo->created_at)->format('Y-m-d H:i:s'),
            'user_name' => Auth::user()->name,
            'company_name' => Auth::user()->company->name ?? 'DEMODAY STORE'
        ]);
    }

    // -----------------------------
    //  Print receipt (thermal printer)
    // -----------------------------
    public function printReceipt($id)
    {
        $companyId = Auth::user()->company_id;
        
        $mauzo = Mauzo::with('bidhaa')
            ->where('id', $id)
            ->where('company_id', $companyId)
            ->firstOrFail();

        $receiptNumber = 'RC' . str_pad($mauzo->id, 5, '0', STR_PAD_LEFT);

        // For thermal printer, return minimal HTML
        if (request()->expectsJson() || request()->has('thermal')) {
            return response()->json([
                'success' => true,
                'receipt_data' => [
                    'receipt_number' => $receiptNumber,
                    'items' => [[
                        'jina' => $mauzo->bidhaa->jina,
                        'idadi' => $mauzo->idadi,
                        'bei' => $mauzo->bei,
                        'punguzo' => $mauzo->punguzo,
                        'jumla' => $mauzo->jumla
                    ]],
                    'total_amount' => $mauzo->jumla,
                    'date' => Carbon::parse($mauzo->created_at)->format('Y-m-d H:i:s'),
                    'user_name' => Auth::user()->name,
                    'company_name' => Auth::user()->company->name ?? 'DEMODAY STORE'
                ]
            ]);
        }

        return view('mauzo.receipt-thermal', [
            'mauzo' => $mauzo,
            'receipt_number' => $receiptNumber,
            'date' => Carbon::parse($mauzo->created_at)->format('Y-m-d H:i:s'),
            'user_name' => Auth::user()->name,
            'company_name' => Auth::user()->company->name ?? 'DEMODAY STORE'
        ]);
    }
}