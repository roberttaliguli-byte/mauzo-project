<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Marejesho;
use App\Models\Madeni;
use App\Models\Mauzo;
use App\Models\Bidhaa;

class MarejeshoController extends Controller
{
    /**
     * Store a new repayment (rejesho) for a debt (deni).
     */
    public function store(Request $request)
    {
        $request->validate([
            'deni_id' => 'required|exists:madenis,id',
            'kiasi'   => 'required|numeric|min:1',
            'tarehe'  => 'required|date',
        ]);

        
        $companyId = Auth::user()->company_id;


        // Fetch deni and ensure it belongs to the same company
        $deni = Madeni::where('id', $request->deni_id)
            ->where('company_id', $companyId)
            ->firstOrFail();

        // Prevent overpayment
        if ($request->kiasi > $deni->baki) {
            return redirect()->back()
                ->withErrors(['kiasi' => "Kiasi kina zidi baki la deni ({$deni->baki})."]);
        }

        // Use transaction to ensure data consistency
        DB::transaction(function () use ($request, $deni, $companyId) {
            // Save repayment
            $deni->marejeshos()->create([
                'kiasi'      => $request->kiasi,
                'tarehe'     => $request->tarehe,
                'company_id' => $companyId,
            ]);

            // Update remaining balance
            $deni->baki -= $request->kiasi;
            $deni->save();
        });

        return redirect()->back()->with('success', 'Rejesho limehifadhiwa kikamilifu!');
    }
}
