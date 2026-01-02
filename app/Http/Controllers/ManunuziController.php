<?php

namespace App\Http\Controllers;

use App\Models\Manunuzi;
use App\Models\Bidhaa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ManunuziController extends Controller
{
    /**
     * Show list of manunuzi and form data (company specific).
     */
public function index()
{
    $companyId = Auth::user()->company_id;

    $perPage = request()->get('per_page', 10);

    // Only show manunuzi for this company
    $manunuzi = Manunuzi::with('bidhaa')
        ->where('company_id', $companyId)
        ->orderBy('created_at', 'desc')
        ->paginate($perPage);

    // Show only products belonging to this company
    $bidhaa = Bidhaa::where('company_id', $companyId)->get();

    return view('manunuzi.index', compact('manunuzi', 'bidhaa'));
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
            'bei' => 'required|numeric|min:0',
            'expiry' => 'nullable|date',
            'saplaya' => 'nullable|string|max:255',
            'simu' => 'nullable|string|max:20',
            'mengineyo' => 'nullable|string|max:500',
        ]);

        return DB::transaction(function () use ($request, $companyId) {
            $bidhaa = Bidhaa::where('id', $request->bidhaa_id)
                            ->where('company_id', $companyId)
                            ->firstOrFail();

            // Hifadhi manunuzi
            $manunuzi = Manunuzi::create([
                'company_id' => $companyId,
                'bidhaa_id' => $bidhaa->id,
                'idadi' => $request->idadi,
                'bei' => $request->bei,
                'expiry' => $request->expiry,
                'saplaya' => $request->saplaya,
                'simu' => $request->simu,
                'mengineyo' => $request->mengineyo,
            ]);

            // Update stock and purchase price in Bidhaa
            $bidhaa->increment('idadi', $request->idadi);
            $bidhaa->bei_nunua = $request->bei;
            $bidhaa->save();

            return redirect()->route('manunuzi.index')
                ->with('success', 'Manunuzi yamehifadhiwa, stock na bei zimeboreshwa kwa kampuni yako!');
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
            'bei' => 'required|numeric|min:0',
            'expiry' => 'nullable|date',
            'saplaya' => 'nullable|string|max:255',
            'simu' => 'nullable|string|max:20',
            'mengineyo' => 'nullable|string|max:500',
        ]);

        return DB::transaction(function () use ($request, $manunuzi, $companyId) {
            $oldIdadi = $manunuzi->idadi;

            $bidhaa = Bidhaa::where('id', $request->bidhaa_id)
                            ->where('company_id', $companyId)
                            ->firstOrFail();

            // Update manunuzi
            $manunuzi->update([
                'bidhaa_id' => $bidhaa->id,
                'idadi' => $request->idadi,
                'bei' => $request->bei,
                'expiry' => $request->expiry,
                'saplaya' => $request->saplaya,
                'simu' => $request->simu,
                'mengineyo' => $request->mengineyo,
            ]);

            // Adjust stock difference
            $difference = $request->idadi - $oldIdadi;
            $bidhaa->increment('idadi', $difference);

            // Update purchase price
            $bidhaa->bei_nunua = $request->bei;
            $bidhaa->save();

            return redirect()->route('manunuzi.index')
                ->with('success', 'Manunuzi yamebadilishwa kikamilifu kwa kampuni yako.');
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

            return redirect()->route('manunuzi.index')
                ->with('success', 'Manunuzi yamefutwa kikamilifu na stock imepunguzwa.');
        });
    }
}
