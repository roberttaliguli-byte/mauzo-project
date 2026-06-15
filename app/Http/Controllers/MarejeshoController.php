<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Marejesho;
use App\Models\Madeni;
use App\Models\Mauzo;
use App\Models\Bidhaa;
use App\Helpers\ActivityHelper;

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
            'lipa_kwa' => 'required|in:cash,lipa_namba,bank',
            'lipa_kwa_type' => 'nullable|required_if:lipa_kwa,lipa_namba,bank',
        ]);

        // Get company ID based on authenticated user type
        $companyId = null;
        $userId = null;
        $mfanyakaziId = null;
        
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            $companyId = $user->company_id;
            $userId = $user->id;
        } elseif (Auth::guard('mfanyakazi')->check()) {
            $employee = Auth::guard('mfanyakazi')->user();
            $companyId = $employee->company_id;
            $mfanyakaziId = $employee->id;
        } else {
            return redirect()->back()->withErrors(['error' => 'Unauthorized access']);
        }

        // Fetch deni and ensure it belongs to the same company
        $deni = Madeni::where('id', $request->deni_id)
            ->where('company_id', $companyId)
            ->firstOrFail();

        // Validate payment type
        if ($request->lipa_kwa === 'lipa_namba') {
            $validTypes = ['mpesa', 'mixx_by_yas', 'airtel_money', 'halopesa', 'other'];
            if (!in_array($request->lipa_kwa_type, $validTypes)) {
                return redirect()->back()
                    ->withErrors(['lipa_kwa_type' => 'Tafadhali chagua aina sahihi ya Lipa Namba']);
            }
        }
        
        if ($request->lipa_kwa === 'bank') {
            $validTypes = ['crdb', 'nmb', 'nbc', 'other'];
            if (!in_array($request->lipa_kwa_type, $validTypes)) {
                return redirect()->back()
                    ->withErrors(['lipa_kwa_type' => 'Tafadhali chagua aina sahihi ya Benki']);
            }
        }

        // Prevent overpayment
        if ($request->kiasi > $deni->baki) {
            return redirect()->back()
                ->withErrors(['kiasi' => "Kiasi kina zidi baki la deni ({$deni->baki})."]);
        }

        // Use transaction to ensure data consistency
        DB::transaction(function () use ($request, $deni, $companyId, $userId, $mfanyakaziId) {
            // Create the repayment and store it in a variable
            $repayment = $deni->marejeshos()->create([
                'kiasi'      => $request->kiasi,
                'tarehe'     => $request->tarehe,
                'lipa_kwa'   => $request->lipa_kwa,
                'lipa_kwa_type' => ($request->lipa_kwa === 'cash') ? null : $request->lipa_kwa_type,
                'company_id' => $companyId,
                'user_id'    => $userId,
                'mfanyakazi_id' => $mfanyakaziId,
            ]);
            
            // Then update the remaining balance
            $deni->baki -= $request->kiasi;
            $deni->save();
            
            // Log the repayment activity
            $bidhaaJina = $deni->bidhaa->jina ?? 'Deni';
            ActivityHelper::logRepayment($repayment, $bidhaaJina, $repayment->kiasi);
        });

        return redirect()->back()->with('success', 'Rejesho limehifadhiwa kikamilifu!');
    }
}