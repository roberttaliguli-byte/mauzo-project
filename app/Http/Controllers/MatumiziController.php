<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Matumizi;
use App\Models\AinaZaMatumizi;
use App\Models\Mauzo;
use App\Models\Bidhaa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class MatumiziController extends Controller
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
     * Get current authenticated user
     */
    private function getAuthUser()
    {
        if (Auth::guard('mfanyakazi')->check()) {
            return Auth::guard('mfanyakazi')->user();
        }
        
        if (Auth::guard('web')->check()) {
            return Auth::guard('web')->user();
        }
        
        return null;
    }
    
    /**
     * Get company for current user
     */
    private function getCompany()
    {
        $user = $this->getAuthUser();
        
        if (!$user) {
            abort(403, 'Unauthorized - Please login first');
        }
        
        return $user->company;
    }

    /**
     * Calculate Faida ya Mauzo (Sales Profit)
     */
    private function getSalesProfit($companyId, $fromDate = null, $toDate = null)
    {
        $query = Mauzo::where('company_id', $companyId);
        
        if ($fromDate && $toDate) {
            $query->whereBetween('created_at', [$fromDate, $toDate]);
        }
        
        $sales = $query->with('bidhaa')->get();
        
        $totalRevenue = $sales->sum('jumla');
        $totalCost = $sales->sum(function($sale) {
            return $sale->idadi * ($sale->bidhaa->bei_nunua ?? 0);
        });
        
        return $totalRevenue - $totalCost;
    }

    /**
     * Calculate Faida ya Marejesho (Returns Profit)
     */
    private function getReturnsProfit($companyId, $fromDate = null, $toDate = null)
    {
        $query = \App\Models\Marejesho::where('company_id', $companyId);
        
        if ($fromDate && $toDate) {
            $query->whereBetween('created_at', [$fromDate, $toDate]);
        }
        
        $repayments = $query->with(['madeni' => function($q) {
                $q->with('bidhaa');
            }])
            ->orderBy('tarehe', 'asc')
            ->get()
            ->filter(fn($r) => $r->madeni && $r->madeni->bidhaa);

        $debtProgress = [];
        $totalProfit = 0;

        foreach ($repayments as $marejesho) {
            $debt = $marejesho->madeni;
            $debtId = $debt->id;
            $amount = $marejesho->kiasi;

            if (!isset($debtProgress[$debtId])) {
                $buyingPrice = $debt->bidhaa->bei_nunua ?? 0;
                $quantity = $debt->idadi;
                $totalCost = $buyingPrice * $quantity;
                $debtProgress[$debtId] = [
                    'total_cost' => $totalCost,
                    'recovered_so_far' => 0,
                    'is_cost_recovered' => false
                ];
            }

            $progress = &$debtProgress[$debtId];
            $remaining = $amount;

            if (!$progress['is_cost_recovered']) {
                $toRecover = $progress['total_cost'] - $progress['recovered_so_far'];
                if ($remaining <= $toRecover) {
                    $progress['recovered_so_far'] += $remaining;
                } else {
                    $costPortion = $toRecover;
                    $progress['recovered_so_far'] += $costPortion;
                    $progress['is_cost_recovered'] = true;
                    $totalProfit += ($remaining - $costPortion);
                }
            } else {
                $totalProfit += $remaining;
            }
        }
        
        return $totalProfit;
    }

    /**
     * Get total MAPATO (Revenue/Income) - Total Sales Revenue
     */
    private function getTotalMapato($companyId)
    {
        $totalRevenue = Mauzo::where('company_id', $companyId)->sum('jumla');
        return $totalRevenue;
    }

    /**
     * Display all expenses with pagination (10 per page)
     */
    public function index()
    {
        $company = $this->getCompany();
        
        if (!$company) {
            abort(403, 'Company not found for this user');
        }

        // Get expenses with pagination (10 per page)
        $matumizi = Matumizi::where('company_id', $company->id)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Get all registered expense types for this company
        $aina_za_matumizi = AinaZaMatumizi::where('company_id', $company->id)
            ->withCount('matumizi')
            ->latest()
            ->get();

        // Calculate statistics
        $totalExpenses = Matumizi::where('company_id', $company->id)->sum('gharama');
        $todayExpenses = Matumizi::where('company_id', $company->id)
            ->whereDate('created_at', today())
            ->sum('gharama');
        $expensesCount = Matumizi::where('company_id', $company->id)->count();
        $averageExpense = $expensesCount > 0 ? $totalExpenses / $expensesCount : 0;
        
        // Get MAPATO (Total Revenue)
        $totalMapato = $this->getTotalMapato($company->id);
        $remainingBalance = $totalMapato - $totalExpenses;
        
        // For reference - profits
        $salesProfit = $this->getSalesProfit($company->id);
        $returnsProfit = $this->getReturnsProfit($company->id);

        return view('matumizi.index', compact(
            'matumizi', 
            'aina_za_matumizi',
            'totalExpenses',
            'todayExpenses',
            'expensesCount',
            'averageExpense',
            'totalMapato',
            'remainingBalance',
            'salesProfit',
            'returnsProfit'
        ));
    }

    /**
     * Store new expense - respects the date entered by user
     */
    public function store(Request $request)
    {
        $request->validate([
            'gharama' => 'required|numeric|min:0',
            'maelezo' => 'nullable|string|max:500',
            'aina' => 'nullable|string|max:255',
            'aina_mpya' => 'nullable|string|max:255',
            'tarehe' => 'nullable|date',
        ]);

        $company = $this->getCompany();
        $companyId = $company->id;

        // Determine the expense type (custom or existing)
        $aina = $request->filled('aina_mpya')
            ? $request->input('aina_mpya')
            : $request->input('aina');

        // If using a custom type, auto-register it
        if ($request->filled('aina_mpya')) {
            $existingAina = AinaZaMatumizi::where('company_id', $companyId)
                ->where('jina', $request->aina_mpya)
                ->first();

            if (!$existingAina) {
                AinaZaMatumizi::create([
                    'jina' => $request->aina_mpya,
                    'maelezo' => 'Auto-generated from expense entry',
                    'rangi' => 'bg-gray-100 text-gray-800 border border-gray-200',
                    'kategoria' => 'mengineyo',
                    'company_id' => $companyId,
                ]);
            }
        }

        // Use the date provided by user, or current date if not provided
        $expenseDate = $request->tarehe ? Carbon::parse($request->tarehe) : now();

        $matumizi = $company->matumizi()->create([
            'aina' => $aina,
            'maelezo' => $request->maelezo,
            'gharama' => $request->gharama,
            'created_at' => $expenseDate,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => '✅ Matumizi yamehifadhiwa!',
                'data' => $matumizi
            ]);
        }

        return redirect()->route('matumizi.index')->with('success', '✅ Matumizi yamehifadhiwa!');
    }

    /**
     * Register new expense type
     */
    public function sajiliAina(Request $request)
    {
        $companyId = $this->getCompanyId();
        
        $request->validate([
            'jina' => [
                'required',
                'string',
                'max:255',
                Rule::unique('aina_za_matumizi')->where(function ($query) use ($companyId) {
                    return $query->where('company_id', $companyId);
                })
            ],
            'maelezo' => 'nullable|string',
            'rangi' => 'nullable|string',
            'kategoria' => 'nullable|string'
        ]);

        $company = $this->getCompany();

        $aina = AinaZaMatumizi::create([
            'jina' => $request->jina,
            'maelezo' => $request->maelezo,
            'rangi' => $request->rangi ?: 'bg-green-100 text-green-800 border border-green-200',
            'kategoria' => $request->kategoria ?: 'mengineyo',
            'company_id' => $company->id,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => '✅ Aina mpya ya matumizi imesajiliwa!',
                'data' => $aina
            ]);
        }

        return redirect()->route('matumizi.index')->with('success', '✅ Aina mpya ya matumizi imesajiliwa!');
    }

    /**
     * Update expense
     */
    public function update(Request $request, $id)
    {
        $companyId = $this->getCompanyId();
        
        $matumizi = Matumizi::findOrFail($id);

        if ($matumizi->company_id !== $companyId) {
            abort(403, 'Huna ruhusa ya kubadilisha matumizi haya.');
        }

        $request->validate([
            'aina' => 'required|string|max:255',
            'maelezo' => 'nullable|string|max:500',
            'gharama' => 'required|numeric|min:0',
        ]);

        $matumizi->update($request->only('aina', 'maelezo', 'gharama'));

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => '✅ Matumizi yamerekebishwa!',
                'data' => $matumizi
            ]);
        }

        return redirect()->route('matumizi.index')->with('success', '✅ Matumizi yamerekebishwa!');
    }

    /**
     * Delete expense
     */
    public function destroy($id)
    {
        $companyId = $this->getCompanyId();
        
        $matumizi = Matumizi::findOrFail($id);

        if ($matumizi->company_id !== $companyId) {
            abort(403, 'Huna ruhusa ya kufuta matumizi haya.');
        }

        $matumizi->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => '✅ Matumizi yamefutwa!'
            ]);
        }

        return redirect()->route('matumizi.index')->with('success', '✅ Matumizi yamefutwa!');
    }

    /**
     * Delete expense type
     */
    public function destroyAina($id)
    {
        $companyId = $this->getCompanyId();
        
        $aina = AinaZaMatumizi::findOrFail($id);

        if ($aina->company_id !== $companyId) {
            abort(403, 'Huna ruhusa ya kufuta aina hii ya matumizi.');
        }

        // Check if this expense type is being used
        $matumiziCount = Matumizi::where('company_id', $companyId)
            ->where('aina', $aina->jina)
            ->count();

        if ($matumiziCount > 0) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Huwezi kufuta aina hii! Inatumika kwenye matumizi ' . $matumiziCount . '.'
                ], 422);
            }
            return redirect()->back()->with('error', '❌ Huwezi kufuta aina hii! Inatumika kwenye matumizi ' . $matumiziCount . '.');
        }

        $aina->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => '✅ Aina ya matumizi imefutwa!'
            ]);
        }

        return redirect()->route('matumizi.index')->with('success', '✅ Aina ya matumizi imefutwa!');
    }

    /**
     * Export all expenses as PDF
     */
    public function exportPDF(Request $request)
    {
        $companyId = $this->getCompanyId();
        $company = $this->getCompany();
        
        $matumizi = Matumizi::where('company_id', $companyId)
            ->latest()
            ->get();
            
        $total = $matumizi->sum('gharama');
        $today = Carbon::now()->format('d/m/Y H:i:s');
        
        $pdf = Pdf::loadView('matumizi.pdf', compact('matumizi', 'total', 'company', 'today'));
        
        return $pdf->download('matumizi-' . date('Y-m-d') . '.pdf');
    }
    
    /**
     * Export filtered expenses as PDF with grouping by type
     */
    public function exportReportPDF(Request $request)
    {
        $companyId = $this->getCompanyId();
        $company = $this->getCompany();
        
        $startDate = $request->start_date ?? now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->end_date ?? now()->format('Y-m-d');
        
        $query = Matumizi::where('company_id', $companyId)
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);
        
        $matumizi = $query->orderBy('created_at', 'desc')->get();
        
        $total = $matumizi->sum('gharama');
        $count = $matumizi->count();
        $average = $count > 0 ? $total / $count : 0;
        $max = $matumizi->max('gharama') ?? 0;
        
        // Group by category/type for report
        $groupedByType = [];
        foreach ($matumizi as $item) {
            $type = $item->aina;
            if (!isset($groupedByType[$type])) {
                $groupedByType[$type] = [
                    'total' => 0,
                    'count' => 0,
                    'items' => []
                ];
            }
            $groupedByType[$type]['total'] += $item->gharama;
            $groupedByType[$type]['count']++;
            $groupedByType[$type]['items'][] = $item;
        }
        
        // Sort by total descending
        arsort($groupedByType);
        
        $pdf = Pdf::loadView('matumizi.report-pdf', compact(
            'matumizi', 
            'total', 
            'count', 
            'average', 
            'max',
            'groupedByType',
            'startDate', 
            'endDate', 
            'company'
        ));
        
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('matumizi-report-' . date('Y-m-d') . '.pdf');
    }
    
    /**
     * Get report data for AJAX
     */
    public function getReportData(Request $request)
    {
        $companyId = $this->getCompanyId();
        
        $startDate = $request->start_date ?? now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->end_date ?? now()->format('Y-m-d');
        
        $expenses = Matumizi::where('company_id', $companyId)
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->orderBy('created_at', 'desc')
            ->get();
        
        $total = $expenses->sum('gharama');
        $count = $expenses->count();
        $average = $count > 0 ? $total / $count : 0;
        $max = $expenses->max('gharama') ?? 0;
        
        // Group by date
        $dailyData = [];
        foreach ($expenses as $item) {
            $date = Carbon::parse($item->created_at)->format('Y-m-d');
            if (!isset($dailyData[$date])) {
                $dailyData[$date] = 0;
            }
            $dailyData[$date] += $item->gharama;
        }
        
        // Group by category/type
        $categoryData = [];
        foreach ($expenses as $item) {
            if (!isset($categoryData[$item->aina])) {
                $categoryData[$item->aina] = 0;
            }
            $categoryData[$item->aina] += $item->gharama;
        }
        
        // Sort categories by total descending
        arsort($categoryData);
        
        return response()->json([
            'success' => true,
            'data' => [
                'expenses' => $expenses,
                'total' => $total,
                'count' => $count,
                'average' => $average,
                'max' => $max,
                'dailyData' => $dailyData,
                'categoryData' => $categoryData
            ]
        ]);
    }
}