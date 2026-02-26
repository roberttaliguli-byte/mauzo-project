<?php

namespace App\Http\Controllers;

use App\Models\Madeni;
use App\Models\Marejesho;
use App\Models\Bidhaa;
use App\Models\Mteja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

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
     * Get today's date range
     */
    private function getTodayRange()
    {
        return [
            now()->startOfDay(),
            now()->endOfDay()
        ];
    }
    
    /**
     * Main index page
     */
    public function index(Request $request)
    {
        $companyId = $this->getCompanyId();
        $today = $this->getTodayRange();
        
        // Today's statistics
        $todayDebts = Madeni::where('company_id', $companyId)
            ->whereBetween('created_at', $today)
            ->sum('jumla');
            
        $todayRepayments = Marejesho::where('company_id', $companyId)
            ->whereBetween('tarehe', $today)
            ->sum('kiasi');
            
        $todayNewBorrowers = Madeni::where('company_id', $companyId)
            ->whereBetween('created_at', $today)
            ->distinct('jina_mkopaji')
            ->count('jina_mkopaji');
            
        $todayPending = Madeni::where('company_id', $companyId)
            ->whereBetween('created_at', $today)
            ->where('baki', '>', 0)
            ->count();
        
        // Get all borrowers with their total debts (for grouping)
        $borrowers = Madeni::where('company_id', $companyId)
            ->select('jina_mkopaji', 'simu', DB::raw('COUNT(*) as total_debts'), DB::raw('SUM(baki) as total_balance'))
            ->where('baki', '>', 0)
            ->groupBy('jina_mkopaji', 'simu')
            ->orderBy('total_balance', 'desc')
            ->get();
        
        // Get all products for dropdown
        $bidhaa = Bidhaa::where('company_id', $companyId)
            ->where('idadi', '>', 0)
            ->orderBy('jina')
            ->get();
        
        // Get paginated debts (for initial load)
        $query = Madeni::with('bidhaa')
            ->where('company_id', $companyId);
        
        if ($request->filter === 'active') {
            $query->where('baki', '>', 0);
        } elseif ($request->filter === 'paid') {
            $query->where('baki', '<=', 0);
        }
        
        $madeni = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Get recent repayments
        $marejesho = Marejesho::with('madeni.bidhaa')
            ->where('company_id', $companyId)
            ->orderBy('tarehe', 'desc')
            ->paginate(15, ['*'], 'history_page');
        
        return view('madeni.index', compact(
            'madeni',
            'marejesho',
            'bidhaa',
            'borrowers',
            'todayDebts',
            'todayRepayments',
            'todayNewBorrowers',
            'todayPending'
        ));
    }
    
    /**
     * AJAX search for debts (unpaginated)
     */
    public function search(Request $request)
    {
        $companyId = $this->getCompanyId();
        
        $query = Madeni::with('bidhaa')
            ->where('company_id', $companyId);
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('jina_mkopaji', 'like', "%{$search}%")
                  ->orWhere('simu', 'like', "%{$search}%")
                  ->orWhereHas('bidhaa', function($q2) use ($search) {
                      $q2->where('jina', 'like', "%{$search}%");
                  });
            });
        }
        
        if ($request->filled('filter')) {
            if ($request->filter === 'active') {
                $query->where('baki', '>', 0);
            } elseif ($request->filter === 'paid') {
                $query->where('baki', '<=', 0);
            }
        }
        
        if ($request->filled('borrower')) {
            $query->where('jina_mkopaji', $request->borrower);
        }
        
        $debts = $query->orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'debts' => $debts
        ]);
    }
    
    /**
     * Get debts for a specific borrower
     */
    public function borrowerDebts(Request $request)
    {
        $companyId = $this->getCompanyId();
        
        $debts = Madeni::with('bidhaa')
            ->where('company_id', $companyId)
            ->where('jina_mkopaji', $request->borrower)
            ->where('baki', '>', 0)
            ->orderBy('created_at', 'desc')
            ->get();
        
        $totalBalance = $debts->sum('baki');
        $totalDebts = $debts->count();
        
        return response()->json([
            'success' => true,
            'debts' => $debts,
            'total_balance' => $totalBalance,
            'total_debts' => $totalDebts,
            'borrower' => $request->borrower
        ]);
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
            'idadi' => 'required|numeric|min:0.01',
            'bei' => 'required|numeric|min:0',
            'punguzo' => 'nullable|numeric|min:0',
            'punguzo_aina' => 'nullable|in:bidhaa,jumla',
            'tarehe_malipo' => 'required|date',
        ]);
        
        return DB::transaction(function () use ($request, $companyId) {
            $bidhaa = Bidhaa::where('id', $request->bidhaa_id)
                ->where('company_id', $companyId)
                ->firstOrFail();
            
            if ($request->idadi > $bidhaa->idadi) {
                return back()->withErrors(['idadi' => "Idadi imezidi stock iliyopo ({$bidhaa->idadi})."]);
            }
            
            // Calculate total with discount
            $baseTotal = $request->idadi * $request->bei;
            $punguzo = $request->punguzo ?? 0;
            
            if ($request->punguzo_aina === 'bidhaa') {
                $punguzo = $punguzo * $request->idadi;
            }
            
            $jumla = max($baseTotal - $punguzo, 0);
            
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
            
            Madeni::create([
                'company_id' => $companyId,
                'mteja_id' => $mteja->id,
                'bidhaa_id' => $bidhaa->id,
                'idadi' => $request->idadi,
                'bei' => $request->bei,
                'punguzo' => $punguzo,
                'punguzo_aina' => $request->punguzo_aina ?? 'bidhaa',
                'jumla' => $jumla,
                'baki' => $jumla,
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
        
        abort_unless($madeni->company_id === $companyId, 403);
        
        $request->validate([
            'kiasi' => 'required|numeric|min:0.01|max:' . $madeni->baki,
            'tarehe' => 'required|date',
            'lipa_kwa' => 'required|in:cash,lipa_namba,bank',
        ]);
        
        return DB::transaction(function () use ($request, $madeni, $companyId) {
            Marejesho::create([
                'madeni_id' => $madeni->id,
                'kiasi' => $request->kiasi,
                'tarehe' => $request->tarehe,
                'lipa_kwa' => $request->lipa_kwa,
                'company_id' => $companyId,
            ]);
            
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
        
        abort_unless($madeni->company_id === $companyId, 403);
        
        $request->validate([
            'jina_mkopaji' => 'required|string|max:255',
            'simu' => 'nullable|string|max:20',
            'bidhaa_id' => 'required|exists:bidhaas,id',
            'idadi' => 'required|numeric|min:0.01',
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
            
            // Handle stock changes
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
                $difference = $newIdadi - $oldIdadi;
                
                if ($difference > 0) {
                    if ($difference > $newBidhaa->idadi) {
                        return response()->json([
                            'errors' => ['idadi' => ["Idadi imezidi stock iliyopo ({$newBidhaa->idadi})."]]
                        ], 422);
                    }
                    $newBidhaa->decrement('idadi', $difference);
                } elseif ($difference < 0) {
                    $newBidhaa->increment('idadi', abs($difference));
                }
            }
            
            // Calculate new totals
            $baseTotal = $request->idadi * $request->bei;
            $punguzo = $request->punguzo ?? $madeni->punguzo;
            
            if ($request->punguzo_aina === 'bidhaa') {
                $punguzo = ($request->punguzo ?? 0) * $request->idadi;
            }
            
            $newJumla = max($baseTotal - $punguzo, 0);
            $amountPaid = $madeni->jumla - $madeni->baki;
            $newBaki = max($newJumla - $amountPaid, 0);
            
            $madeni->update([
                'bidhaa_id' => $request->bidhaa_id,
                'idadi' => $request->idadi,
                'bei' => $request->bei,
                'punguzo' => $punguzo,
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
        
        abort_unless($madeni->company_id === $companyId, 403);
        
        return DB::transaction(function () use ($madeni) {
            if ($madeni->baki > 0) {
                $madeni->bidhaa->increment('idadi', $madeni->idadi);
            }
            
            $madeni->marejeshos()->delete();
            $madeni->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Deni limefutwa!'
            ]);
        });
    }
    
    /**
     * Export to Excel
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
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('jina_mkopaji', 'like', "%{$search}%")
                  ->orWhere('simu', 'like', "%{$search}%");
            });
        }
        
        $debts = $query->orderBy('created_at', 'desc')->get();
        
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
                    number_format($debt->jumla, 2),
                    number_format($debt->punguzo, 2),
                    number_format($debt->baki, 2),
                    $status
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Generate PDF report
     */
    public function reportPdf(Request $request)
    {
        $companyId = $this->getCompanyId();
        
        $company = (object) [
            'company_name' => auth()->user()->company_name ?? 'N/A',
            'owner_name' => auth()->user()->name ?? 'N/A',
            'location' => auth()->user()->location ?? 'N/A',
            'phone' => auth()->user()->phone ?? 'N/A',
        ];
        
        $startDate = $request->start_date ?? now()->startOfMonth();
        $endDate = $request->end_date ?? now();
        $reportType = $request->report_type ?? 'detailed';
        $status = $request->status ?? 'all';
        
        $query = Madeni::with('bidhaa')
            ->where('company_id', $companyId)
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        
        if ($status === 'active') {
            $query->where('baki', '>', 0);
        } elseif ($status === 'paid') {
            $query->where('baki', '<=', 0);
        }
        
        $debts = $query->orderBy('created_at', 'desc')->get();
        
        $totalAmount = $debts->sum('jumla');
        $totalPaid = $debts->sum(function($d) { 
            return $d->jumla - $d->baki; 
        });
        $totalBalance = $totalAmount - $totalPaid;
        
        $pdf = Pdf::loadView('madeni.report-pdf', compact(
            'debts', 'startDate', 'endDate', 'reportType', 
            'totalAmount', 'totalPaid', 'totalBalance', 'status', 'company'
        ));
        
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('madeni-report-'.date('Y-m-d').'.pdf');
    }
}