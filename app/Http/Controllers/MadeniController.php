<?php

namespace App\Http\Controllers;

use App\Models\Madeni;
use App\Models\Marejesho;
use App\Models\Mauzo;
use App\Models\Bidhaa;
use App\Models\Mteja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MadeniController extends Controller
{
    /**
     * Show debts and history (company specific).
     */
    public function index()
    {
        $companyId = Auth::user()->company_id;

        // Fetch debts for this company only
        $madeni = Madeni::with('bidhaa', 'marejeshos')
            ->where('company_id', $companyId)
            ->latest()
            ->get();

        // Fetch repayment history summary
        $historia = Marejesho::selectRaw("
                MAX(marejeshos.tarehe) as tarehe,
                madeni.bidhaa_id,
                madeni.jina_mkopaji,
                madeni.simu,
                SUM(marejeshos.kiasi) as jumla_rejeshwa,
                MAX(madeni.jumla) as deni_lote,
                MAX(madeni.idadi) as idadi,
                MAX(marejeshos.id) as last_rejesho_id
            ")
            ->join('madenis as madeni', 'marejeshos.madeni_id', '=', 'madeni.id')
            ->where('madeni.company_id', $companyId)
            ->groupBy('madeni.bidhaa_id', 'madeni.jina_mkopaji', 'madeni.simu')
            ->get()
            ->map(function ($item) {
                $lastRejesho = Marejesho::find($item->last_rejesho_id);
                return [
                    'tarehe'         => $item->tarehe,
                    'bidhaa'         => Bidhaa::find($item->bidhaa_id)->jina ?? '-',
                    'idadi'          => $item->idadi,
                    'deni_lote'      => $item->deni_lote,
                    'jumla_rejeshwa' => $item->jumla_rejeshwa,
                    'rejesho_leo'    => $lastRejesho ? $lastRejesho->kiasi : 0,
                    'baki'           => max($item->deni_lote - $item->jumla_rejeshwa, 0),
                    'mkopaji'        => $item->jina_mkopaji,
                    'simu'           => $item->simu,
                    'status'         => $item->deni_lote - $item->jumla_rejeshwa <= 0 ? 'Amemaliza' : 'Anaendelea',
                ];
            });

        return view('madeni.index', compact('madeni', 'historia'));
    }

    /**
     * Store a new debt (loan sale) (company specific).
     */
    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;

        $request->validate([
            'bidhaa_id'     => 'required|exists:bidhaas,id',
            'idadi'         => 'required|integer|min:1',
            'bei'           => 'required|numeric|min:0',
            'jumla'         => 'required|numeric|min:0',
            'tarehe_malipo' => 'required|date',
            'jina_mkopaji'  => 'required|string|max:255',
            'simu'          => 'required|string|max:20',
        ]);

        return DB::transaction(function () use ($request, $companyId) {
            $bidhaa = Bidhaa::where('id', $request->bidhaa_id)
                            ->where('company_id', $companyId)
                            ->firstOrFail();

            // Ensure sufficient stock
            if ($request->idadi > $bidhaa->idadi) {
                return back()->withErrors(['idadi' => "Idadi imezidi stock iliyopo ({$bidhaa->idadi})."]);
            }

            // Reduce stock
            $bidhaa->decrement('idadi', $request->idadi);

            // Find or create customer (mteja)
            $mteja = Mteja::where('simu', $request->simu)
                          ->where('company_id', $companyId)
                          ->first();

            if (!$mteja) {
                $mteja = Mteja::create([
                    'jina'        => $request->jina_mkopaji,
                    'simu'        => $request->simu,
                    'barua_pepe'  => $request->barua_pepe ?? null,
                    'anapoishi'   => $request->anapoishi ?? null,
                    'maelezo'     => 'Mkopaji mpya kutoka mauzo ya mkopo',
                    'company_id'  => $companyId,
                ]);
            }

            // Save debt
            Madeni::create([
                'company_id'    => $companyId,
                'mteja_id'      => $mteja->id,
                'bidhaa_id'     => $bidhaa->id,
                'idadi'         => $request->idadi,
                'bei'           => $request->bei,
                'jumla'         => $request->jumla,
                'baki'          => $request->jumla,
                'jina_mkopaji'  => $mteja->jina,
                'simu'          => $mteja->simu,
                'tarehe_malipo' => $request->tarehe_malipo,
            ]);

            return redirect()->route('madeni.index')
                ->with('success', 'Deni jipya limehifadhiwa kikamilifu kwa kampuni yako!');
        });
    }

    /**
     * Record a repayment for a debt (company specific).
     */
    public function rejesha(Request $request, Madeni $madeni)
    {
        $companyId = Auth::user()->company_id;

        abort_unless($madeni->company_id === $companyId, 403, 'Huna ruhusa ya kurejesha deni hili.');

        $request->validate([
            'kiasi'  => 'required|numeric|min:1',
            'tarehe' => 'required|date',
        ]);

        if ($request->kiasi > $madeni->baki) {
            return back()->withErrors(['kiasi' => "Kiasi kimezidi baki lililopo ({$madeni->baki})."]);
        }

        return DB::transaction(function () use ($request, $madeni, $companyId) {
            // Save repayment
            $madeni->marejeshos()->create([
                'kiasi'       => $request->kiasi,
                'tarehe'      => $request->tarehe,
                'company_id'  => $companyId,
            ]);

            // Update debt balance
            $madeni->decrement('baki', $request->kiasi);

            // Add sale record
            Mauzo::create([
                'company_id' => $companyId,
                'bidhaa_id'  => $madeni->bidhaa_id,
                'idadi'      => 0,
                'bei'        => $madeni->bei,
                'punguzo'    => 0,
                'jumla'      => $request->kiasi,
            ]);

            return redirect()->route('madeni.index')
                ->with('success', 'Rejesho limehifadhiwa kikamilifu kwa kampuni yako!');
        });
    }

    /**
     * Delete a debt (company specific).
     */
    public function destroy(Madeni $madeni)
    {
        $companyId = Auth::user()->company_id;

        abort_unless($madeni->company_id === $companyId, 403, 'Huna ruhusa ya kufuta deni hili.');

        return DB::transaction(function () use ($madeni) {
            $bidhaa = $madeni->bidhaa;

            if ($bidhaa && $madeni->baki > 0) {
                $bidhaa->increment('idadi', $madeni->idadi);
            }

            $madeni->delete();

            return redirect()->route('madeni.index')
                ->with('success', 'Deni limefutwa kikamilifu na stock imeboreshwa.');
        });
    }

    public function edit($id)
{
    $deni = Madeni::findOrFail($id);
    // return your edit view with the data
    return view('madeni.edit', compact('deni'));
}

}
