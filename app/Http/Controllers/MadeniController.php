<?php

namespace App\Http\Controllers;

use App\Models\Madeni;
use App\Models\Marejesho;
use App\Models\Bidhaa;
use App\Models\Mteja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MadeniController extends Controller
{
    /**
     * Get company ID for current user
     */
    private function getCompanyId()
    {
        if (Auth::guard('mfanyakazi')->check()) {
            return Auth::guard('mfanyakazi')->user()->company_id;
        }
        
        if (Auth::guard('web')->check()) {
            return Auth::guard('web')->user()->company_id;
        }
        
        abort(403, 'Unauthorized - Please login first');
    }
    
    /**
     * Get all data for the index page
     */
    public function index(Request $request)
    {
        $companyId = $this->getCompanyId();
        
        // Get statistics
        $totalDebts = Madeni::where('company_id', $companyId)->sum('jumla');
        $activeDebts = Madeni::where('company_id', $companyId)->where('baki', '>', 0)->count();
        $paidDebts = Madeni::where('company_id', $companyId)->where('baki', '<=', 0)->count();
        $totalBorrowers = Madeni::where('company_id', $companyId)->distinct('jina_mkopaji')->count('jina_mkopaji');
        
        // Get all products for dropdown
        $bidhaa = Bidhaa::where('company_id', $companyId)
                        ->where('idadi', '>', 0)
                        ->orderBy('jina')
                        ->get();
        
        // Filter debts
        $query = Madeni::with('bidhaa')
                        ->where('company_id', $companyId);
        
        if ($request->filter === 'active') {
            $query->where('baki', '>', 0);
        } elseif ($request->filter === 'paid') {
            $query->where('baki', '<=', 0);
        }
        
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('jina_mkopaji', 'like', "%{$search}%")
                  ->orWhere('simu', 'like', "%{$search}%")
                  ->orWhereHas('bidhaa', function($q2) use ($search) {
                      $q2->where('jina', 'like', "%{$search}%")
                         ->orWhere('aina', 'like', "%{$search}%");
                  });
            });
        }
        
        $madeni = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Get repayment history
        $marejesho = Marejesho::with('madeni.bidhaa')
                            ->whereHas('madeni', function($q) use ($companyId) {
                                $q->where('company_id', $companyId);
                            })
                            ->orderBy('tarehe', 'desc')
                            ->paginate(15, ['*'], 'history_page');
        
        return view('madeni.index', compact(
            'madeni',
            'marejesho',
            'bidhaa',
            'totalDebts',
            'activeDebts',
            'paidDebts',
            'totalBorrowers'
        ));
    }
    
    /**
     * Store a new debt
     */
    public function store(Request $request)
    {
        $companyId = $this->getCompanyId();
        
        $request->validate([
            'jina_mkopaji' => 'required|string|max:255',
            'simu' => 'required|string|max:20',
            'bidhaa_id' => 'required|exists:bidhaas,id',
            'idadi' => 'required|integer|min:1',
            'bei' => 'required|numeric|min:0',
            'punguzo' => 'nullable|numeric|min:0', // Add discount validation
            'punguzo_aina' => 'nullable|in:bidhaa,jumla', // Add discount type validation
            'jumla' => 'required|numeric|min:0',
            'tarehe_malipo' => 'required|date',
        ]);
        
        return DB::transaction(function () use ($request, $companyId) {
            // Check product availability
            $bidhaa = Bidhaa::where('id', $request->bidhaa_id)
                            ->where('company_id', $companyId)
                            ->firstOrFail();
            
            if ($request->idadi > $bidhaa->idadi) {
                return back()->withErrors(['idadi' => "Idadi imezidi stock iliyopo ({$bidhaa->idadi})."]);
            }
            
            // Reduce stock
            $bidhaa->decrement('idadi', $request->idadi);
            
            // Find or create customer
            $mteja = Mteja::firstOrCreate(
                ['simu' => $request->simu, 'company_id' => $companyId],
                [
                    'jina' => $request->jina_mkopaji,
                    'barua_pepe' => null,
                    'anapoishi' => null,
                    'maelezo' => 'Mkopaji mpya kutoka madeni',
                    'company_id' => $companyId,
                ]
            );
            
            // Create debt with discount fields
            Madeni::create([
                'company_id' => $companyId,
                'mteja_id' => $mteja->id,
                'bidhaa_id' => $bidhaa->id,
                'idadi' => $request->idadi,
                'bei' => $request->bei,
                'punguzo' => $request->punguzo ?? 0,
                'punguzo_aina' => $request->punguzo_aina ?? 'bidhaa',
                'jumla' => $request->jumla,
                'baki' => $request->jumla,
                'jina_mkopaji' => $request->jina_mkopaji,
                'simu' => $request->simu,
                'tarehe_malipo' => $request->tarehe_malipo,
            ]);
            
            return redirect()->route('madeni.index')
                ->with('success', 'Deni limehifadhiwa kikamilifu!');
        });
    }
    
    /**
     * Record a repayment
     */
    public function rejesha(Request $request, Madeni $madeni)
    {
        $companyId = $this->getCompanyId();
        
        abort_unless($madeni->company_id === $companyId, 403, 'Huna ruhusa ya kurejesha deni hili.');
        
        $request->validate([
            'kiasi' => 'required|numeric|min:0.01|max:' . $madeni->baki,
            'tarehe' => 'required|date',
            'lipa_kwa' => 'required|in:cash,lipa_namba,bank', // Add payment method validation
        ]);
        
        return DB::transaction(function () use ($request, $madeni, $companyId) {
            // Create repayment record with payment method
            Marejesho::create([
                'madeni_id' => $madeni->id,
                'kiasi' => $request->kiasi,
                'tarehe' => $request->tarehe,
                'lipa_kwa' => $request->lipa_kwa,
                'company_id' => $companyId,
            ]);
            
            // Update remaining balance
            $madeni->decrement('baki', $request->kiasi);
            
            return response()->json([
                'success' => true,
                'message' => 'Rejesho limehifadhiwa!'
            ]);
        });
    }
    
    /**
     * Update a debt
     */
    public function update(Request $request, Madeni $madeni)
    {
        $companyId = $this->getCompanyId();
        
        abort_unless($madeni->company_id === $companyId, 403, 'Huna ruhusa ya kubadilisha deni hili.');
        
        $request->validate([
            'jina_mkopaji' => 'required|string|max:255',
            'simu' => 'nullable|string|max:20',
            'bidhaa_id' => 'required|exists:bidhaas,id',
            'idadi' => 'required|integer|min:1',
            'bei' => 'required|numeric|min:0',
            'punguzo' => 'nullable|numeric|min:0',
            'punguzo_aina' => 'nullable|in:bidhaa,jumla',
        ]);
        
        return DB::transaction(function () use ($request, $madeni, $companyId) {
            $oldBidhaa = $madeni->bidhaa;
            $newBidhaa = Bidhaa::where('id', $request->bidhaa_id)
                               ->where('company_id', $companyId)
                               ->firstOrFail();
            
            $oldIdadi = $madeni->idadi;
            $newIdadi = $request->idadi;
            
            // Handle product change
            if ($oldBidhaa->id !== $newBidhaa->id) {
                // Return old stock
                $oldBidhaa->increment('idadi', $oldIdadi);
                
                // Check new stock
                if ($newIdadi > $newBidhaa->idadi) {
                    return response()->json([
                        'errors' => ['idadi' => ["Idadi imezidi stock iliyopo ({$newBidhaa->idadi})."]]
                    ], 422);
                }
                
                // Reduce new stock
                $newBidhaa->decrement('idadi', $newIdadi);
            } else {
                // Same product, adjust stock
                $difference = $newIdadi - $oldIdadi;
                
                if ($difference > 0) {
                    // Increased quantity
                    if ($difference > $newBidhaa->idadi) {
                        return response()->json([
                            'errors' => ['idadi' => ["Idadi imezidi stock iliyopo ({$newBidhaa->idadi})."]]
                        ], 422);
                    }
                    $newBidhaa->decrement('idadi', $difference);
                } elseif ($difference < 0) {
                    // Decreased quantity
                    $newBidhaa->increment('idadi', abs($difference));
                }
            }
            
            // Calculate new total and balance
            $newJumla = $request->idadi * $request->bei;
            $amountPaid = $madeni->jumla - $madeni->baki;
            $newBaki = max($newJumla - $amountPaid, 0);
            
            // Update customer info
            if ($madeni->mteja_id) {
                $madeni->mteja->update([
                    'jina' => $request->jina_mkopaji,
                    'simu' => $request->simu ?? $madeni->simu,
                ]);
            }
            
            // Update debt with discount fields
            $madeni->update([
                'bidhaa_id' => $request->bidhaa_id,
                'idadi' => $request->idadi,
                'bei' => $request->bei,
                'punguzo' => $request->punguzo ?? $madeni->punguzo,
                'punguzo_aina' => $request->punguzo_aina ?? $madeni->punguzo_aina,
                'jumla' => $newJumla,
                'baki' => $newBaki,
                'jina_mkopaji' => $request->jina_mkopaji,
                'simu' => $request->simu ?? $madeni->simu,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Deni limebadilishwa!'
            ]);
        });
    }
    
    /**
     * Delete a debt
     */
    public function destroy(Madeni $madeni)
    {
        $companyId = $this->getCompanyId();
        
        abort_unless($madeni->company_id === $companyId, 403, 'Huna ruhusa ya kufuta deni hili.');
        
        return DB::transaction(function () use ($madeni) {
            // Return stock if not fully paid
            if ($madeni->baki > 0) {
                $madeni->bidhaa->increment('idadi', $madeni->idadi);
            }
            
            // Delete repayments
            $madeni->marejeshos()->delete();
            
            // Delete debt
            $madeni->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Deni limefutwa!'
            ]);
        });
    }
    
    /**
     * Export debts to Excel
     */
    public function export(Request $request)
    {
        $companyId = $this->getCompanyId();
        
        $query = Madeni::with('bidhaa')
                        ->where('company_id', $companyId);
        
        if ($request->filter === 'active') {
            $query->where('baki', '>', 0);
        } elseif ($request->filter === 'paid') {
            $query->where('baki', '<=', 0);
        }
        
        $debts = $query->orderBy('created_at', 'desc')->get();
        
        // Create CSV
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=madeni_' . date('Y-m-d') . '.csv',
        ];
        
        $callback = function() use ($debts) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Tarehe', 'Mkopaji', 'Simu', 'Bidhaa', 'Idadi', 'Deni', 'Punguzo', 'Baki', 'Hali']);
            
            foreach ($debts as $debt) {
                $status = $debt->baki <= 0 ? 'Imelipwa' : 'Inayongoza';
                fputcsv($file, [
                    $debt->created_at->format('d/m/Y'),
                    $debt->jina_mkopaji,
                    $debt->simu,
                    $debt->bidhaa->jina ?? 'N/A',
                    $debt->idadi,
                    $debt->jumla,
                    $debt->punguzo,
                    $debt->baki,
                    $status
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Get repayment history for a specific debt
     */
    public function repaymentHistory(Madeni $madeni)
    {
        $companyId = $this->getCompanyId();
        
        abort_unless($madeni->company_id === $companyId, 403, 'Huna ruhusa ya kuona historia hii.');
        
        $repayments = $madeni->marejeshos()
                            ->orderBy('tarehe', 'desc')
                            ->get();
        
        return response()->json([
            'success' => true,
            'repayments' => $repayments
        ]);
    }
}