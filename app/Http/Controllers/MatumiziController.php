<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Matumizi;
use App\Models\AinaZaMatumizi;
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
     * Display all expenses
     */
    public function index()
    {
        $company = $this->getCompany();
        
        if (!$company) {
            abort(403, 'Company not found for this user');
        }

        // Get expenses with pagination
        $matumizi = Matumizi::where('company_id', $company->id)
            ->latest()
            ->paginate(20)
            ->withQueryString();

        // Get all registered expense types for this company
        $aina_za_matumizi = AinaZaMatumizi::where('company_id', $company->id)
            ->withCount(['matumizi' => function($query) use ($company) {
                $query->where('company_id', $company->id);
            }])
            ->latest()
            ->get();

        // Calculate statistics
        $totalExpenses = Matumizi::where('company_id', $company->id)->sum('gharama');
        $todayExpenses = Matumizi::where('company_id', $company->id)
            ->whereDate('created_at', today())
            ->sum('gharama');
        $expensesCount = Matumizi::where('company_id', $company->id)->count();
        $averageExpense = $expensesCount > 0 ? $totalExpenses / $expensesCount : 0;

        return view('matumizi.index', compact(
            'matumizi', 
            'aina_za_matumizi',
            'totalExpenses',
            'todayExpenses',
            'expensesCount',
            'averageExpense'
        ));
    }

    /**
     * Store new expense
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

        // Use custom type if provided
        $aina = $request->filled('aina_mpya')
            ? $request->input('aina_mpya')
            : $request->input('aina');

        // If using a custom type, check if it exists in aina_za_matumizi
        if ($request->filled('aina_mpya')) {
            $existingAina = AinaZaMatumizi::where('company_id', $company->id)
                ->where('jina', $request->aina_mpya)
                ->first();

            if (!$existingAina) {
                // Auto-register the new expense type
                AinaZaMatumizi::create([
                    'jina' => $request->aina_mpya,
                    'maelezo' => 'Auto-generated from expense entry',
                    'rangi' => 'bg-gray-100 text-gray-800 border border-gray-200',
                    'kategoria' => 'mengineyo',
                    'company_id' => $company->id,
                ]);
            }
        }

        $matumizi = $company->matumizi()->create([
            'aina' => $aina,
            'maelezo' => $request->maelezo,
            'gharama' => $request->gharama,
            'created_at' => $request->tarehe ?: now(),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Matumizi yamehifadhiwa kwa mafanikio!',
                'data' => $matumizi
            ]);
        }

        return redirect()->route('matumizi.index')->with('success', 'Matumizi yamehifadhiwa kwa mafanikio!');
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
                'message' => 'Aina mpya ya matumizi imesajiliwa kikamilifu!',
                'data' => $aina
            ]);
        }

        return redirect()->route('matumizi.index')->with('success', 'Aina mpya ya matumizi imesajiliwa kikamilifu!');
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
                'message' => 'Matumizi yamerekebishwa!',
                'data' => $matumizi
            ]);
        }

        return redirect()->route('matumizi.index')->with('success', 'Matumizi yamerekebishwa!');
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
                'message' => 'Matumizi yamefutwa kikamilifu!'
            ]);
        }

        return redirect()->route('matumizi.index')->with('success', 'Matumizi yamefutwa kikamilifu!');
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
                    'message' => 'Huwezi kufuta aina hii ya matumizi kwa sababu inatumika kwenye matumizi ' . $matumiziCount . '.'
                ], 422);
            }
            return redirect()->back()->with('error', 'Huwezi kufuta aina hii ya matumizi kwa sababu inatumika kwenye matumizi ' . $matumiziCount . '.');
        }

        $aina->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Aina ya matumizi imefutwa kikamilifu!'
            ]);
        }

        return redirect()->route('matumizi.index')->with('success', 'Aina ya matumizi imefutwa kikamilifu!');
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
     * Export filtered expenses as PDF (for report tab)
     */
    public function exportReportPDF(Request $request)
    {
        $companyId = $this->getCompanyId();
        $company = $this->getCompany();
        
        $startDate = $request->start_date ?? now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->end_date ?? now()->format('Y-m-d');
        $reportType = $request->report_type ?? 'all';
        
        $query = Matumizi::where('company_id', $companyId)
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);
        
        $matumizi = $query->orderBy('created_at', 'desc')->get();
        
        // Calculate statistics
        $total = $matumizi->sum('gharama');
        $count = $matumizi->count();
        $average = $count > 0 ? $total / $count : 0;
        $max = $matumizi->max('gharama') ?? 0;
        
        // Group by date for chart
        $dailyData = [];
        foreach ($matumizi as $item) {
            $date = Carbon::parse($item->created_at)->format('d/m/Y');
            if (!isset($dailyData[$date])) {
                $dailyData[$date] = 0;
            }
            $dailyData[$date] += $item->gharama;
        }
        
        // Group by category
        $categoryData = [];
        foreach ($matumizi as $item) {
            if (!isset($categoryData[$item->aina])) {
                $categoryData[$item->aina] = 0;
            }
            $categoryData[$item->aina] += $item->gharama;
        }
        
        $pdf = Pdf::loadView('matumizi.report-pdf', compact(
            'matumizi', 
            'total', 
            'count', 
            'average', 
            'max',
            'dailyData',
            'categoryData',
            'startDate', 
            'endDate', 
            'reportType', 
            'company'
        ));
        
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('matumizi-report-' . date('Y-m-d') . '.pdf');
    }
}