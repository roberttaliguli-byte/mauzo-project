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
                    return response()->json([
                        'success' => false,
                        'message' => "Bidhaa {$bidhaa->jina} ime-expire.",
                        'notification' => 'Bidhaa ime-expire!'
                    ], 422);
                }

                if ($validated['idadi'] > $bidhaa->idadi) {
                    return response()->json([
                        'success' => false,
                        'message' => "Stock haitoshi kwa {$bidhaa->jina}, baki ni {$bidhaa->idadi}.",
                        'notification' => 'Stock haitoshi!'
                    ], 422);
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

                $results[] = $mauzo;
            }

            return response()->json([
                'success' => true,
                'message' => 'Mauzo yamerekodiwa kikamilifu!',
                'notification' => 'Mauzo yamefanikiwa!',
                'mauzo' => $results
            ]);

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

        return redirect()->back()->with([
            'success' => 'Rekodi ya mauzo imefutwa kikamilifu!',
            'notification' => 'Rekodi imefutwa!'
        ]);
    }

    // -----------------------------
    //  Handle loan sales
    // -----------------------------
    private function storeLoan(Request $request)
    {
        $companyId = Auth::user()->company_id;

        return DB::transaction(function () use ($request, $companyId) {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'bidhaa_id'      => 'required|exists:bidhaas,id',
                'idadi'          => 'required|integer|min:1',
                'bei'            => 'required|numeric',
                'jumla'          => 'required|numeric',
                'jina_mkopaji'   => 'required|string|max:255',
                'simu'           => 'required|string|max:20',
                'tarehe_malipo'  => 'required|date',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tafadhali jaza taarifa zote kikamilifu.',
                    'notification' => 'Kosa katika taarifa!',
                    'errors' => $validator->errors()
                ], 422);
            }

            $bidhaa = Bidhaa::where('id', $request->bidhaa_id)
                ->where('company_id', $companyId)
                ->first();

            if (!$bidhaa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bidhaa haipatikani.',
                    'notification' => 'Bidhaa haipo!'
                ], 404);
            }

            // Check expiry
            if ($bidhaa->expiry < now()->toDateString()) {
                return response()->json([
                    'success' => false,
                    'message' => "Bidhaa {$bidhaa->jina} ime-expire.",
                    'notification' => 'Bidhaa ime-expire!'
                ], 422);
            }

            // Check stock
            if ($request->idadi > $bidhaa->idadi) {
                return response()->json([
                    'success' => false,
                    'message' => "Stock haitoshi kwa {$bidhaa->jina}, baki ni {$bidhaa->idadi}.",
                    'notification' => 'Stock haitoshi!'
                ], 422);
            }

            // Reduce stock
            $bidhaa->decrement('idadi', $request->idadi);

            // Create loan record
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

            return response()->json([
                'success' => true,
                'message' => 'Bidhaa zimekopeshwa kwa mafanikio kwa ' . $request->jina_mkopaji . '!',
                'notification' => 'Mikopo imefanikiwa!',
                'loan_id' => $deni->id,
                'customer_name' => $request->jina_mkopaji,
                'amount' => $request->jumla
            ]);
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
                    'message' => 'Tafadhali jaza taarifa zote kikamilifu.',
                    'notification' => 'Kosa katika taarifa!',
                    'errors' => $validator->errors()
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

            if ($bidhaa->expiry < now()->toDateString()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bidhaa hii ime-expire',
                    'notification' => 'Bidhaa ime-expire!'
                ], 422);
            }

            if ($request->idadi > $bidhaa->idadi) {
                return response()->json([
                    'success' => false,
                    'message' => "Stock haitoshi, baki ni {$bidhaa->idadi}",
                    'notification' => 'Stock haitoshi!'
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

            return response()->json([
                'success' => true,
                'message' => 'Mauzo yamerekodiwa kikamilifu!',
                'notification' => 'Mauzo yamefanikiwa!',
                'sale_id' => $mauzo->id
            ]);

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
        ]);

        $saleIds = [];
        $totalItems = 0;

        foreach ($validated['items'] as $item) {
            $bidhaa = Bidhaa::where('jina', $item['jina'])
                ->where('company_id', $companyId)
                ->first();

            if ($bidhaa) {
                // Check expiry
                if ($bidhaa->expiry < now()->toDateString()) {
                    return response()->json([
                        'success' => false,
                        'message' => "Bidhaa {$bidhaa->jina} ime-expire.",
                        'notification' => 'Bidhaa ime-expire!'
                    ], 422);
                }

                // Check stock
                if ($item['idadi'] > $bidhaa->idadi) {
                    return response()->json([
                        'success' => false,
                        'message' => "Stock haitoshi kwa {$bidhaa->jina}, baki ni {$bidhaa->idadi}.",
                        'notification' => 'Stock haitoshi!'
                    ], 422);
                }

                $jumla = ($item['bei'] * $item['idadi']) - ($item['punguzo'] ?? 0);

                $mauzo = Mauzo::create([
                    'company_id' => $companyId,
                    'bidhaa_id'  => $bidhaa->id,
                    'idadi'      => $item['idadi'],
                    'bei'        => $item['bei'],
                    'punguzo'    => $item['punguzo'] ?? 0,
                    'jumla'      => $jumla,
                ]);

                $bidhaa->decrement('idadi', $item['idadi']);

                $saleIds[] = $mauzo->id;
                $totalItems++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Mauzo ya kikapu ya bidhaa ' . $totalItems . ' zimehifadhiwa kikamilifu!',
            'notification' => 'Kikapu kimefanikiwa!',
            'total_items' => $totalItems
        ]);
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

        // Create or find customer
        $mteja = Mteja::firstOrCreate([
            'jina' => $validated['jina'],
            'company_id' => $companyId,
        ]);

        $loanIds = [];
        $totalItems = 0;

        foreach ($validated['items'] as $item) {
            $bidhaa = Bidhaa::where('jina', $item['jina'])
                ->where('company_id', $companyId)
                ->first();

            if ($bidhaa) {
                // Check expiry
                if ($bidhaa->expiry < now()->toDateString()) {
                    return response()->json([
                        'success' => false,
                        'message' => "Bidhaa {$bidhaa->jina} ime-expire.",
                        'notification' => 'Bidhaa ime-expire!'
                    ], 422);
                }

                // Check stock
                if ($item['idadi'] > $bidhaa->idadi) {
                    return response()->json([
                        'success' => false,
                        'message' => "Stock haitoshi kwa {$bidhaa->jina}, baki ni {$bidhaa->idadi}.",
                        'notification' => 'Stock haitoshi!'
                    ], 422);
                }

                $jumla = ($item['bei'] * $item['idadi']) - ($item['punguzo'] ?? 0);

                // Reduce stock
                $bidhaa->decrement('idadi', $item['idadi']);

                // Create loan record
                $deni = Madeni::create([
                    'company_id'    => $companyId,
                    'bidhaa_id'     => $bidhaa->id,
                    'idadi'         => $item['idadi'],
                    'bei'           => $item['bei'],
                    'jumla'         => $jumla,
                    'jina_mkopaji'  => $validated['jina'],
                    'simu'          => $mteja->simu ?? '',
                    'tarehe_malipo' => now()->addDays(7),
                    'baki'          => $jumla,
                ]);

                $loanIds[] = $deni->id;
                $totalItems++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Bidhaa ' . $totalItems . ' zimekopeshwa kwa mafanikio kwa ' . $validated['jina'] . '!',
            'notification' => 'Mikopo ya kikapu imefanikiwa!',
            'total_items' => $totalItems,
            'customer_name' => $validated['jina']
        ]);
    }
}