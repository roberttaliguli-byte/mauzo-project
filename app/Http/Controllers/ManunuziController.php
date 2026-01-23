<?php

namespace App\Http\Controllers;

use App\Models\Manunuzi;
use App\Models\Bidhaa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class ManunuziController extends Controller
{
    /**
     * Show list of manunuzi and form data (company specific).
     */
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;

        $perPage = $request->input('per_page', 10);

        // Only show manunuzi for this company
        $query = Manunuzi::with('bidhaa')
            ->where('company_id', $companyId)
            ->orderBy('created_at', 'desc');

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('bidhaa', function($q) use ($search) {
                $q->where('jina', 'LIKE', "%{$search}%")
                  ->orWhere('aina', 'LIKE', "%{$search}%");
            })->orWhere('saplaya', 'LIKE', "%{$search}%")
              ->orWhere('simu', 'LIKE', "%{$search}%");
        }

        $manunuzi = $query->paginate($perPage)
                         ->appends($request->except('page'));

        // Show only products belonging to this company
        $bidhaa = Bidhaa::where('company_id', $companyId)->get();

        // Get statistics
        $todayPurchases = Manunuzi::where('company_id', $companyId)
            ->whereDate('created_at', today())
            ->count();
        
        $totalItemsPurchased = Manunuzi::where('company_id', $companyId)
            ->sum('idadi');
        
        $totalCost = Manunuzi::where('company_id', $companyId)
            ->sum('bei');
        
        $todayCost = Manunuzi::where('company_id', $companyId)
            ->whereDate('created_at', today())
            ->sum('bei');

        // PDF Export
// In ManunuziController index method
if ($request->has('export') && $request->export === 'pdf') {
    $data = [
        'manunuzi' => Manunuzi::with('bidhaa')
            ->where('company_id', $companyId)
            ->orderBy('created_at', 'desc')
            ->get(),
        'title' => 'Orodha ya Manunuzi',
        'date' => now()->format('d/m/Y'),
    ];
    
    $pdf = Pdf::loadView('manunuzi.pdf', $data);
    return $pdf->download('orodha-ya-manunuzi-' . date('Y-m-d') . '.pdf');
}

        return view('manunuzi.index', compact(
            'manunuzi', 
            'bidhaa', 
            'todayPurchases', 
            'totalItemsPurchased', 
            'totalCost', 
            'todayCost'
        ));
    }

    /**
     * Store a new manunuzi and update stock and purchase price (company specific).
     */
    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;

        $request->validate([
            'bidhaa_id' => 'required|exists:bidhaas,id',
            'idadi' => 'required|integer|min:1',
            'bei_nunua' => 'required|numeric|min:0',
            'bei_kuuza' => 'required|numeric|min:0|gte:bei_nunua',
            'bei_type' => 'required|in:kwa_zote,rejareja',
            'expiry' => 'nullable|date',
            'saplaya' => 'nullable|string|max:255',
            'simu' => 'nullable|string|max:20',
            'mengineyo' => 'nullable|string|max:500',
        ], [
            'bei_kuuza.gte' => 'Bei ya kuuza haiwezi kuwa chini ya bei ya kununua',
        ]);

        return DB::transaction(function () use ($request, $companyId) {
            $bidhaa = Bidhaa::where('id', $request->bidhaa_id)
                            ->where('company_id', $companyId)
                            ->firstOrFail();

            // Calculate actual purchase price based on type
            if ($request->bei_type === 'kwa_zote') {
                $totalCost = $request->bei_nunua;
                $unitCost = $totalCost / $request->idadi;
            } else {
                $unitCost = $request->bei_nunua;
                $totalCost = $unitCost * $request->idadi;
            }

            // Hifadhi manunuzi
            $manunuzi = Manunuzi::create([
                'company_id' => $companyId,
                'bidhaa_id' => $bidhaa->id,
                'idadi' => $request->idadi,
                'bei' => $totalCost,
                'unit_cost' => $unitCost,
                'expiry' => $request->expiry,
                'saplaya' => $request->saplaya,
                'simu' => $request->simu,
                'mengineyo' => $request->mengineyo,
            ]);

            // Update stock and prices in Bidhaa
            $bidhaa->increment('idadi', $request->idadi);
            $bidhaa->bei_nunua = $unitCost;
            $bidhaa->bei_kuuza = $request->bei_kuuza;
            $bidhaa->save();

            return response()->json([
                'success' => true,
                'message' => 'Manunuzi yamehifadhiwa, stock na bei zimeboreshwa!'
            ]);
        });
    }

    /**
     * Update an existing manunuzi and adjust stock and purchase price (company specific).
     */
    public function update(Request $request, Manunuzi $manunuzi)
    {
        $companyId = Auth::user()->company_id;

        // Ensure this manunuzi belongs to this company
        abort_unless($manunuzi->company_id === $companyId, 403, 'Huna ruhusa ya kubadilisha manunuzi haya.');

        $request->validate([
            'bidhaa_id' => 'required|exists:bidhaas,id',
            'idadi' => 'required|integer|min:1',
            'bei_nunua' => 'required|numeric|min:0',
            'bei_kuuza' => 'required|numeric|min:0|gte:bei_nunua',
            'bei_type' => 'required|in:kwa_zote,rejareja',
            'expiry' => 'nullable|date',
            'saplaya' => 'nullable|string|max:255',
            'simu' => 'nullable|string|max:20',
            'mengineyo' => 'nullable|string|max:500',
        ], [
            'bei_kuuza.gte' => 'Bei ya kuuza haiwezi kuwa chini ya bei ya kununua',
        ]);

        return DB::transaction(function () use ($request, $manunuzi, $companyId) {
            $oldIdadi = $manunuzi->idadi;

            $bidhaa = Bidhaa::where('id', $request->bidhaa_id)
                            ->where('company_id', $companyId)
                            ->firstOrFail();

            // Calculate actual purchase price based on type
            if ($request->bei_type === 'kwa_zote') {
                $totalCost = $request->bei_nunua;
                $unitCost = $totalCost / $request->idadi;
            } else {
                $unitCost = $request->bei_nunua;
                $totalCost = $unitCost * $request->idadi;
            }

            // Update manunuzi
            $manunuzi->update([
                'bidhaa_id' => $bidhaa->id,
                'idadi' => $request->idadi,
                'bei' => $totalCost,
                'unit_cost' => $unitCost,
                'expiry' => $request->expiry,
                'saplaya' => $request->saplaya,
                'simu' => $request->simu,
                'mengineyo' => $request->mengineyo,
            ]);

            // Adjust stock difference
            $difference = $request->idadi - $oldIdadi;
            $bidhaa->increment('idadi', $difference);

            // Update prices
            $bidhaa->bei_nunua = $unitCost;
            $bidhaa->bei_kuuza = $request->bei_kuuza;
            $bidhaa->save();

            return response()->json([
                'success' => true,
                'message' => 'Manunuzi yamebadilishwa kikamilifu!'
            ]);
        });
    }

    /**
     * Delete a manunuzi and reduce stock (company specific).
     */
    public function destroy(Manunuzi $manunuzi)
    {
        $companyId = Auth::user()->company_id;

        abort_unless($manunuzi->company_id === $companyId, 403, 'Huna ruhusa ya kufuta manunuzi haya.');

        return DB::transaction(function () use ($manunuzi) {
            $bidhaa = $manunuzi->bidhaa;
            if ($bidhaa) {
                $bidhaa->decrement('idadi', $manunuzi->idadi);
            }

            $manunuzi->delete();

            return response()->json([
                'success' => true,
                'message' => 'Manunuzi yamefutwa kikamilifu na stock imepunguzwa.'
            ]);
        });
    }

    /**
     * Get product details for AJAX request
     */
    public function getProductDetails($id)
    {
        $companyId = Auth::user()->company_id;
        
        $bidhaa = Bidhaa::where('id', $id)
                        ->where('company_id', $companyId)
                        ->firstOrFail();
        
        return response()->json([
            'success' => true,
            'data' => [
                'jina' => $bidhaa->jina,
                'aina' => $bidhaa->aina,
                'kipimo' => $bidhaa->kipimo,
                'idadi' => $bidhaa->idadi,
                'bei_nunua' => $bidhaa->bei_nunua,
                'bei_kuuza' => $bidhaa->bei_kuuza,
            ]
        ]);
    }
}