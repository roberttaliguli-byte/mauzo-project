<?php

namespace App\Http\Controllers;

use App\Models\Mengineyo;
use App\Models\Banking;
use App\Models\Mauzo;
use App\Models\Marejesho;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class MengineyoController extends Controller
{
    /**
     * Get the authenticated user's company ID
     */
    private function getCompanyId()
    {
        $user = null;
        
        if (Auth::guard('mfanyakazi')->check()) {
            $user = Auth::guard('mfanyakazi')->user();
        } elseif (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
        }
        
        return $user->company_id ?? null;
    }
    
    public function index(Request $request)
    {
        $companyId = $this->getCompanyId();
        
        if (!$companyId) {
            return redirect()->back()->with('error', 'Hakuna kampuni iliyopatikana');
        }
        
        $search = $request->get('search');
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $bank_filter = $request->get('bank_filter');
        $tab = $request->get('tab', 'mapato');
        
        // ========== MENGINEYO (Other Income) - Company Specific ==========
        $mengineyo = Mengineyo::query()
            ->where('company_id', $companyId)
            ->when($search, fn($q, $s) => $q->where('chanzo', 'like', "%{$s}%")->orWhere('maelezo', 'like', "%{$s}%"))
            ->when($start_date && $end_date, fn($q) => $q->whereBetween('tarehe', [$start_date, $end_date]))
            ->orderBy('tarehe', 'desc')
            ->paginate(20);
        
        // Calculate TOTAL Mengineyo (filtered by company and dates)
        $mapatoMengine = Mengineyo::where('company_id', $companyId)
            ->when($start_date && $end_date, fn($q) => $q->whereBetween('tarehe', [$start_date, $end_date]))
            ->sum('kiasi');
        
        // ========== BANKING - Company Specific ==========
        $banking = Banking::query()
            ->where('company_id', $companyId)
            ->when($start_date && $end_date, fn($q) => $q->whereBetween('tarehe', [$start_date, $end_date]))
            ->when($bank_filter, fn($q) => $q->where('benki', $bank_filter))
            ->when($search, fn($q) => $q->where('maelezo', 'like', "%{$search}%"))
            ->orderBy('tarehe', 'desc')
            ->paginate(20);
        
        // Calculate TOTAL Banked (company specific)
        $totalBanked = Banking::where('company_id', $companyId)
            ->when($start_date && $end_date, fn($q) => $q->whereBetween('tarehe', [$start_date, $end_date]))
            ->sum('kiasi');
        
        // Get unique banks for filter (company specific)
        $banks = Banking::where('company_id', $companyId)
            ->select('benki')
            ->distinct()
            ->pluck('benki');
        
        // ========== CALCULATIONS FROM MAUZO AND MAREJESHO - Company Specific ==========
        
        // 1. Jumla ya Mauzo (from sales) - Company specific
        $totalMauzo = Mauzo::where('company_id', $companyId)
            ->when($start_date && $end_date, function($q) use ($start_date, $end_date) {
                $q->whereDate('created_at', '>=', $start_date)
                  ->whereDate('created_at', '<=', $end_date);
            })
            ->sum('jumla');
        
        // If no date filter, get all time total for this company
        if (!$start_date && !$end_date) {
            $totalMauzo = Mauzo::where('company_id', $companyId)->sum('jumla');
        }
        
        // 2. Mapato ya Madeni (debt repayments) - Company specific
        $totalMadeni = Marejesho::where('company_id', $companyId)
            ->when($start_date && $end_date, function($q) use ($start_date, $end_date) {
                $q->whereDate('tarehe', '>=', $start_date)
                  ->whereDate('tarehe', '<=', $end_date);
            })
            ->sum('kiasi');
        
        // If no date filter, get all time total for this company
        if (!$start_date && !$end_date) {
            $totalMadeni = Marejesho::where('company_id', $companyId)->sum('kiasi');
        }
        
        // 3. Jumla ya Mapato = Mauzo + Madeni
        $totalMapato = $totalMauzo + $totalMadeni;
        
        // 4. Jumla Kuu (Mapato + Mengineyo)
        $jumlaKuu = $totalMapato + $mapatoMengine;
        
        // Calculate percentages
        $percentageFromMengineyo = $totalMapato > 0 ? round(($mapatoMengine / $totalMapato) * 100, 1) : 0;
        $percentageBanked = $totalMauzo > 0 ? round(($totalBanked / $totalMauzo) * 100, 1) : 0;
        
        // Debug: Log values to see what's happening
        \Log::info('Mengineyo Report Data - Company ID: ' . $companyId, [
            'totalMauzo' => $totalMauzo,
            'totalMadeni' => $totalMadeni,
            'totalMapato' => $totalMapato,
            'mapatoMengine' => $mapatoMengine,
            'totalBanked' => $totalBanked,
            'jumlaKuu' => $jumlaKuu,
        ]);
        
        return view('mengineyo.index', compact(
            'mengineyo', 
            'banking', 
            'totalMapato', 
            'totalMadeni',
            'mapatoMengine', 
            'jumlaKuu', 
            'totalBanked', 
            'totalMauzo',
            'banks', 
            'search', 
            'start_date', 
            'end_date', 
            'bank_filter', 
            'tab',
            'percentageFromMengineyo', 
            'percentageBanked'
        ));
    }
    
    // Store Mengineyo with company_id
    public function storeMengineyo(Request $request)
    {
        $companyId = $this->getCompanyId();
        
        if (!$companyId) {
            return redirect()->back()->with('error', 'Hakuna kampuni iliyopatikana');
        }
        
        $validated = $request->validate([
            'chanzo' => 'required|string|max:255',
            'kiasi' => 'required|numeric|min:0.01',
            'maelezo' => 'nullable|string',
            'tarehe' => 'required|date',
        ]);
        
        $validated['company_id'] = $companyId;
        $validated['mfanyakazi_id'] = Auth::guard('mfanyakazi')->id() ?? Auth::id();
        
        Mengineyo::create($validated);
        
        return redirect()->route('mengineyo.index', ['tab' => 'mapato'])
            ->with('success', 'Mapato mengine yameongezwa kikamilifu!');
    }
    
    public function updateMengineyo(Request $request, $id)
    {
        $companyId = $this->getCompanyId();
        
        $mengineyo = Mengineyo::where('company_id', $companyId)->findOrFail($id);
        
        $validated = $request->validate([
            'chanzo' => 'required|string|max:255',
            'kiasi' => 'required|numeric|min:0.01',
            'maelezo' => 'nullable|string',
            'tarehe' => 'required|date',
        ]);
        
        $mengineyo->update($validated);
        
        return redirect()->route('mengineyo.index', ['tab' => 'mapato'])
            ->with('success', 'Mapato mengine yamerekebishwa!');
    }
    
    public function destroyMengineyo($id)
    {
        $companyId = $this->getCompanyId();
        
        $mengineyo = Mengineyo::where('company_id', $companyId)->findOrFail($id);
        $mengineyo->delete();
        
        return redirect()->route('mengineyo.index', ['tab' => 'mapato'])
            ->with('success', 'Mapato mengine yamefutwa!');
    }
    
    // Store Banking with company_id
    public function storeBanking(Request $request)
    {
        $companyId = $this->getCompanyId();
        
        if (!$companyId) {
            return redirect()->back()->with('error', 'Hakuna kampuni iliyopatikana');
        }
        
        $validated = $request->validate([
            'benki' => 'required|string|max:255',
            'kiasi' => 'required|numeric|min:0.01',
            'maelezo' => 'nullable|string',
            'tarehe' => 'required|date',
        ]);
        
        $validated['company_id'] = $companyId;
        $validated['mfanyakazi_id'] = Auth::guard('mfanyakazi')->id() ?? Auth::id();
        $validated['status'] = 'completed';
        
        Banking::create($validated);
        
        return redirect()->route('mengineyo.index', ['tab' => 'banking'])
            ->with('success', 'Taarifa za benki zimehifadhiwa!');
    }
    
    public function updateBanking(Request $request, $id)
    {
        $companyId = $this->getCompanyId();
        
        $transaction = Banking::where('company_id', $companyId)->findOrFail($id);
        
        $validated = $request->validate([
            'benki' => 'required|string|max:255',
            'kiasi' => 'required|numeric|min:0.01',
            'maelezo' => 'nullable|string',
            'tarehe' => 'required|date',
        ]);
        
        $transaction->update($validated);
        
        return redirect()->route('mengineyo.index', ['tab' => 'banking'])
            ->with('success', 'Taarifa za benki zimerekebishwa!');
    }
    
    public function destroyBanking($id)
    {
        $companyId = $this->getCompanyId();
        
        $transaction = Banking::where('company_id', $companyId)->findOrFail($id);
        $transaction->delete();
        
        return redirect()->route('mengineyo.index', ['tab' => 'banking'])
            ->with('success', 'Taarifa za benki zimefutwa!');
    }
    
    // Export Mengineyo PDF (Company Specific)
    public function exportMengineyoPDF(Request $request)
    {
        $companyId = $this->getCompanyId();
        
        $data = Mengineyo::where('company_id', $companyId)
            ->when($request->search, fn($q, $s) => $q->where('chanzo', 'like', "%{$s}%"))
            ->when($request->start_date && $request->end_date, 
                fn($q) => $q->whereBetween('tarehe', [$request->start_date, $request->end_date]))
            ->orderBy('tarehe', 'desc')
            ->get();
        
        $total = $data->sum('kiasi');
        
        $pdf = Pdf::loadView('mengineyo.pdf-mapato', compact('data', 'total'));
        return $pdf->download('mapato_mengineyo_' . date('Y-m-d') . '.pdf');
    }
    
    // Export Banking PDF (Company Specific)
    public function exportBankingPDF(Request $request)
    {
        $companyId = $this->getCompanyId();
        
        $data = Banking::where('company_id', $companyId)
            ->when($request->start_date && $request->end_date, 
                fn($q) => $q->whereBetween('tarehe', [$request->start_date, $request->end_date]))
            ->when($request->bank_filter, fn($q) => $q->where('benki', $request->bank_filter))
            ->orderBy('tarehe', 'desc')
            ->get();
        
        $total = $data->sum('kiasi');
        
        $pdf = Pdf::loadView('mengineyo.pdf-banking', compact('data', 'total'));
        return $pdf->download('taarifa_benki_' . date('Y-m-d') . '.pdf');
    }
}