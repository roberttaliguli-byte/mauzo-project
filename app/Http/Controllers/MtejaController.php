<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mteja;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class MtejaController extends Controller
{
    /**
     * Get company ID for current user (works for both guards)
     */
    private function getCompanyId()
    {
        // Check mfanyakazi guard first
        if (Auth::guard('mfanyakazi')->check()) {
            return Auth::guard('mfanyakazi')->user()->company_id;
        }
        
        // Then check web guard for boss/admin
        if (Auth::guard('web')->check()) {
            return Auth::guard('web')->user()->company_id;
        }
        
        // If neither guard is authenticated
        abort(403, 'Unauthorized - Tafadhali ingia kwanza');
    }
    
    /**
     * Get current authenticated user from any guard
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
     * Display all customers belonging to the logged-in user's company.
     */
    public function index(Request $request)
    {
        $companyId = $this->getCompanyId();
        $perPage = $request->input('per_page', 10);

        $query = Mteja::where('company_id', $companyId);

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('jina', 'LIKE', "%{$search}%")
                  ->orWhere('simu', 'LIKE', "%{$search}%")
                  ->orWhere('barua_pepe', 'LIKE', "%{$search}%")
                  ->orWhere('anapoishi', 'LIKE', "%{$search}%");
            });
        }

        $wateja = $query->latest()
                       ->paginate($perPage)
                       ->appends($request->except('page'));

        // Get statistics
        $totalWateja = Mteja::where('company_id', $companyId)->count();
        $newThisMonth = Mteja::where('company_id', $companyId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $newToday = Mteja::where('company_id', $companyId)
            ->whereDate('created_at', today())
            ->count();

        // PDF Export
        if ($request->has('export') && $request->export === 'pdf') {
            $data = [
                'wateja' => Mteja::where('company_id', $companyId)->latest()->get(),
                'title' => 'Orodha ya Wateja',
                'date' => now()->format('d/m/Y'),
                'total_wateja' => $totalWateja,
                'new_this_month' => $newThisMonth,
                'new_today' => $newToday,
            ];
            
            $pdf = Pdf::loadView('wateja.pdf', $data);
            return $pdf->download('wateja-' . date('Y-m-d') . '.pdf');
        }

        return view('wateja.index', compact(
            'wateja', 
            'totalWateja', 
            'newThisMonth', 
            'newToday'
        ));
    }

    /**
     * Store a new customer.
     */
    public function store(Request $request)
    {
        $request->validate([
            'jina' => 'required|string|max:255',
            'simu' => 'required|string|max:20',
            'barua_pepe' => 'nullable|email|max:255',
            'anapoishi' => 'nullable|string|max:500',
            'maelezo' => 'nullable|string',
        ]);

        $companyId = $this->getCompanyId();

        // Check if phone number already exists for this company
        $existingMteja = Mteja::where('company_id', $companyId)
            ->where('simu', $request->simu)
            ->first();

        if ($existingMteja) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Namba ya simu tayari ipo kwenye mfumo'
                ], 422);
            }
            return redirect()->back()
                ->with('error', 'Namba ya simu tayari ipo kwenye mfumo')
                ->withInput();
        }

        $mteja = Mteja::create([
            'jina' => $request->jina,
            'simu' => $request->simu,
            'barua_pepe' => $request->barua_pepe,
            'anapoishi' => $request->anapoishi,
            'maelezo' => $request->maelezo,
            'company_id' => $companyId,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Mteja ameongezwa kikamilifu!',
                'data' => $mteja
            ], 201);
        }

        return redirect()->route('wateja.index')
            ->with('success', 'Mteja ameongezwa kikamilifu!');
    }

    /**
     * Update customer details.
     */
    public function update(Request $request, $id)
    {
        $companyId = $this->getCompanyId();

        $mteja = Mteja::where('company_id', $companyId)->findOrFail($id);

        $request->validate([
            'jina' => 'required|string|max:255',
            'simu' => 'required|string|max:20',
            'barua_pepe' => 'nullable|email|max:255',
            'anapoishi' => 'nullable|string|max:500',
            'maelezo' => 'nullable|string',
        ]);

        // Check if phone number already exists for another customer
        if ($mteja->simu !== $request->simu) {
            $existingMteja = Mteja::where('company_id', $companyId)
                ->where('simu', $request->simu)
                ->where('id', '!=', $id)
                ->first();

            if ($existingMteja) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Namba ya simu tayari ipo kwenye mfumo'
                    ], 422);
                }
                return redirect()->back()
                    ->with('error', 'Namba ya simu tayari ipo kwenye mfumo')
                    ->withInput();
            }
        }

        $mteja->update($request->only('jina', 'simu', 'barua_pepe', 'anapoishi', 'maelezo'));

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Taarifa za mteja zimebadilishwa kikamilifu!',
                'data' => $mteja
            ]);
        }

        return redirect()->route('wateja.index')
            ->with('success', 'Taarifa za mteja zimebadilishwa kikamilifu!');
    }

    /**
     * Delete a customer.
     */
    public function destroy($id, Request $request)
    {
        $companyId = $this->getCompanyId();

        $mteja = Mteja::where('company_id', $companyId)->findOrFail($id);
        $mteja->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Mteja amefutwa kikamilifu!'
            ]);
        }

        return redirect()->route('wateja.index')
            ->with('success', 'Mteja amefutwa kikamilifu!');
    }

    /**
     * Search customers (for AJAX requests)
     */
    public function search(Request $request)
    {
        $companyId = $this->getCompanyId();
        $query = $request->input('query', '');

        if (empty($query)) {
            return response()->json([
                'success' => true,
                'wateja' => [],
                'count' => 0
            ]);
        }

        $wateja = Mteja::where('company_id', $companyId)
            ->where(function($q) use ($query) {
                $q->where('jina', 'LIKE', "%{$query}%")
                  ->orWhere('simu', 'LIKE', "%{$query}%")
                  ->orWhere('barua_pepe', 'LIKE', "%{$query}%")
                  ->orWhere('anapoishi', 'LIKE', "%{$query}%");
            })
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'wateja' => $wateja,
            'count' => $wateja->count(),
        ]);
    }

    /**
     * Get single customer details
     */
    public function show($id, Request $request)
    {
        $companyId = $this->getCompanyId();
        
        $mteja = Mteja::where('company_id', $companyId)->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $mteja
        ]);
    }
}