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


class MauzoController extends Controller
{
    // -----------------------------
    //  Display main sales page
    // -----------------------------
public function index()
{
    
    
   $companyId = Auth::user()->company_id;

    $bidhaa = Bidhaa::where('company_id', $companyId)
        ->orderBy('jina')
        ->get();

    $mauzos = Mauzo::with('bidhaa')
        ->where('company_id', $companyId)
        ->latest()
        ->get();

    $matumizi = Matumizi::where('company_id', $companyId)
        ->latest()
        ->get();

    $wateja = Mteja::where('company_id', $companyId)
        ->orderBy('jina')
        ->get();

    // 🔥 Add this line
    $madeni = Madeni::with('bidhaa')
        ->where('company_id', $companyId)
        ->latest()
        ->get();

     $marejeshos = DB::table('marejeshos')
        ->join('madenis', 'marejeshos.madeni_id', '=', 'madenis.id')
        ->where('madenis.company_id', $companyId)
        ->select('marejeshos.*')
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

        return DB::transaction(function () use ($items, $companyId) {
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

                $results[] = $mauzo;
            }
             return response()->json([ 'message' => 'Mauzo yamerekodiwa kikamilifu!', 'mauzo' => $results ]);

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

            Madeni::create([
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
            $request->validate([
                'bidhaa_id' => 'required|exists:bidhaas,id',
                'idadi'     => 'required|integer|min:1',
                'bei'       => 'required|numeric',
                'punguzo'   => 'nullable|numeric',
                'jumla'     => 'required|numeric',
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

            Mauzo::create([
                'company_id' => $companyId,
                'bidhaa_id'  => $request->bidhaa_id,
                'idadi'      => $request->idadi,
                'bei'        => $request->bei,
                'punguzo'    => $request->punguzo ?? 0,
                'jumla'      => $request->jumla,
            ]);

            return redirect()->back()->with('success', 'Mauzo yamerekodiwa kikamilifu!');
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

        foreach ($validated['items'] as $item) {
            $bidhaa = Bidhaa::where('jina', $item['jina'])
                ->where('company_id', $companyId)
                ->first();

            if ($bidhaa) {
                $jumla = ($item['bei'] * $item['idadi']) - ($item['punguzo'] ?? 0);

                Mauzo::create([
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
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Mauzo ya kikapu yamehifadhiwa kikamilifu!',
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

        $mteja = Mteja::firstOrCreate([
            'jina' => $validated['jina'],
            'company_id' => $companyId,
        ]);

        foreach ($validated['items'] as $item) {
            $bidhaa = Bidhaa::where('jina', $item['jina'])
                ->where('company_id', $companyId)
                ->first();

            if ($bidhaa) {
                $jumla = ($item['bei'] * $item['idadi']) - ($item['punguzo'] ?? 0);

                $bidhaa->update([
                    'idadi' => max(0, $bidhaa->idadi - $item['idadi']),
                ]);

                Madeni::create([
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
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Bidhaa zimekopeshwa kwa mafanikio!',
        ]);
    }
    
}
