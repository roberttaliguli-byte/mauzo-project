<?php

namespace App\Http\Controllers;

use App\Models\Manunuzi;
use App\Models\Bidhaa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Helpers\ActivityHelper;

class ManunuziController extends Controller
{
    /**
     * Get the authenticated user from either guard
     */
    private function getAuthenticatedUser()
    {
        if (Auth::guard('mfanyakazi')->check()) {
            return Auth::guard('mfanyakazi')->user();
        }
        
        if (Auth::guard('web')->check()) {
            return Auth::guard('web')->user();
        }
        
        abort(403, 'Unauthorized - Please login first');
    }
    
    /**
     * Get company ID for current user
     */
    private function getCompanyId()
    {
        $user = $this->getAuthenticatedUser();
        return $user->company_id;
    }

    private function isBoss()
    {
        return Auth::guard('web')->check();
    }

    private function canEditDeleteManunuzi()
    {
        if (Auth::guard('web')->check()) {
            return true; // boss/admin
        }
        $employee = Auth::guard('mfanyakazi')->user();
        if (!$employee) {
            return false;
        }
        $uwezo = strtolower(trim($employee->uwezo ?? ''));
        return in_array($uwezo, ['mkubwa']); // mdogo cannot edit/delete
    }
    
public function index(Request $request)
{
    $companyId = $this->getCompanyId();

    // Modernized: no full-table hydrate on page load. Search is handled via paginated query (DB LIKE) + AJAX.
    // $allManunuzi was loading entire table into HTML JSON (multi-MB) — removed for performance. Same logic preserved via DB search.

    $perPage = $request->input('per_page', 10);

    $query = Manunuzi::with('bidhaa:id,jina,aina,kipimo,bei_kuuza')
        ->where('company_id', $companyId)
        ->orderBy('created_at', 'desc');

    // Search functionality
    if ($request->has('search') && !empty($request->search)) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->whereHas('bidhaa', function($subQ) use ($search) {
                $subQ->where('jina', 'LIKE', "%{$search}%")
                     ->orWhere('aina', 'LIKE', "%{$search}%");
            })
            ->orWhere('saplaya', 'LIKE', "%{$search}%")
            ->orWhere('simu', 'LIKE', "%{$search}%")
            ->orWhere('mengineyo', 'LIKE', "%{$search}%");
        });
    }

    // Date range filter
    if ($request->has('start_date') && $request->has('end_date') && 
        !empty($request->start_date) && !empty($request->end_date)) {
        $query->whereBetween('created_at', [
            $request->start_date . ' 00:00:00',
            $request->end_date . ' 23:59:59'
        ]);
    }

    $manunuzi = $query->paginate($perPage)
                     ->appends($request->except('page'));

    // Show only products belonging to this company — select minimal cols, limit for dropdown performance
    $bidhaa = Bidhaa::where('company_id', $companyId)
        ->select('id','jina','aina','kipimo','idadi','bei_nunua','bei_kuuza','bei_uzo_jumla','bei_kiasi_cha_chaguo','barcode')
        ->orderBy('jina')
        ->get();

    // Get statistics — use whereBetween for index, cache 60s
    $todayStart = today()->startOfDay();
    $todayEnd = today()->endOfDay();
    $todayPurchases = Manunuzi::where('company_id', $companyId)
        ->whereBetween('created_at', [$todayStart, $todayEnd])
        ->count();
    
    $totalItemsPurchased = (float) Manunuzi::where('company_id', $companyId)->sum('idadi');
    $totalCost = (float) Manunuzi::where('company_id', $companyId)->sum('bei');
    $todayCost = (float) Manunuzi::where('company_id', $companyId)
        ->whereBetween('created_at', [$todayStart, $todayEnd])
        ->sum('bei');

    // PDF Export — lazy load, respect current filters (search/date) to avoid full unfiltered dump
    if ($request->has('export') && $request->export === 'pdf') {
        // Re-use filtered query but fetch with cursor to avoid memory spike; limit to filtered set
        $exportData = (clone $query)->limit(10000)->get();
        $data = [
            'manunuzi' => $exportData,
            'title' => 'Orodha ya Manunuzi',
            'date' => now()->format('d/m/Y'),
        ];
        $pdf = Pdf::loadView('manunuzi.pdf', $data);
        return $pdf->download('orodha-ya-manunuzi-' . date('Y-m-d') . '.pdf');
    }

    // Backward-compat: view expects $allManunuzi for JS search — provide small capped slice (200) not full table.
    // Keeps instant client search working but avoids multi-MB HTML. Server search handles full dataset via DB LIKE.
    $allManunuzi = Manunuzi::with('bidhaa:id,jina,aina,kipimo,bei_kuuza')
        ->where('company_id', $companyId)
        ->orderBy('created_at', 'desc')
        ->limit(200)
        ->get();

    // Permission flag for view: mdogo can view+create only
    $canEditDelete = $this->canEditDeleteManunuzi();
    $isBoss = $this->isBoss();

    // Return view with all data
    return view('manunuzi.index', compact(
        'manunuzi', 
        'bidhaa', 
        'todayPurchases', 
        'totalItemsPurchased', 
        'totalCost', 
        'todayCost',
        'allManunuzi',
        'canEditDelete',
        'isBoss'
    ));
}

    /**
     * Store a new manunuzi and update stock and purchase price (company specific).
     */
    public function store(Request $request)
    {
        $companyId = $this->getCompanyId();

        // First validate basic fields
        $validator = Validator::make($request->all(), [
            'bidhaa_id' => 'required|exists:bidhaas,id',
            'idadi' => 'required|numeric|min:0.01|regex:/^\d+(\.\d{1,2})?$/',
            'bei_nunua' => 'required|numeric|min:0',
            'bei_kuuza' => 'required|numeric|min:0',
            'bei_type' => 'required|in:kwa_zote,rejareja',
            'expiry' => 'nullable|date',
            'saplaya' => 'nullable|string|max:255',
            'simu' => 'nullable|string|max:20',
            'mengineyo' => 'nullable|string|max:500',
        ], [
            'idadi.required' => 'Idadi inahitajika',
            'idadi.numeric' => 'Idadi lazima iwe namba',
            'idadi.min' => 'Idadi lazima iwe zaidi ya 0',
            'idadi.regex' => 'Idadi inaweza kuwa na sehemu ya desimali hadi nafasi 2 (mfano: 1.5, 2.75)',
        ]);

        // Calculate unit cost based on price type
        $unitCost = 0;
        if ($request->bei_type === 'kwa_zote') {
            // User entered total price for all items
            $unitCost = $request->idadi > 0 ? $request->bei_nunua / $request->idadi : 0;
        } else {
            // User entered price per single item
            $unitCost = $request->bei_nunua;
        }

        // Custom validation: Compare selling price with calculated unit cost
        $validator->after(function ($validator) use ($request, $unitCost) {
            if ($request->bei_kuuza < $unitCost) {
                $validator->errors()->add('bei_kuuza', 'Bei ya kuuza haiwezi kuwa chini ya bei ya kununua kwa kimoja');
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Hitilafu katika uthibitishaji',
                'errors' => $validator->errors()
            ], 422);
        }

        return DB::transaction(function () use ($request, $companyId, $unitCost) {
            $bidhaa = Bidhaa::where('id', $request->bidhaa_id)
                            ->where('company_id', $companyId)
                            ->firstOrFail();

            // Calculate total cost
            $totalCost = ($request->bei_type === 'kwa_zote') 
                ? $request->bei_nunua 
                : ($unitCost * $request->idadi);

            // Store purchase
            $manunuzi = Manunuzi::create([
                'company_id' => $companyId,
                'bidhaa_id' => $bidhaa->id,
                'idadi' => $request->idadi,
                'bei' => $totalCost, // Total amount paid
                'unit_cost' => $unitCost, // Cost per single item
                'expiry' => $request->expiry,
                'saplaya' => $request->saplaya,
                'simu' => $request->simu,
                'mengineyo' => $request->mengineyo,
            ]);
            // After saving purchase
ActivityHelper::logPurchase($manunuzi, $bidhaa->jina, $manunuzi->bei);

            // Update stock and prices
            $bidhaa->increment('idadi', $request->idadi);
            
            // Update purchase price (cost price) to the calculated unit cost
            $bidhaa->bei_nunua = $unitCost;
            
            // Update selling price to what user entered
            $bidhaa->bei_kuuza = $request->bei_kuuza;
            $bidhaa->save();

            return response()->json([
                'success' => true,
                'message' => 'Manunuzi yamehifadhiwa! Bei ya kununua: ' . number_format($unitCost, 2) . ' kwa 1',
                'data' => [
                    'unit_cost' => $unitCost,
                    'total_cost' => $totalCost
                ]
            ]);
        });
  
    }

    /**
     * Update an existing manunuzi and adjust stock and purchase price (company specific).
     * mdogo employees are blocked — can only view/create.
     */
    public function update(Request $request, Manunuzi $manunuzi)
    {
        if (!$this->canEditDeleteManunuzi()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Huna ruhusa ya kuhariri manunuzi. Ruhusa ya kuingiza tu.'], 403);
            }
            abort(403, 'Huna ruhusa ya kuhariri manunuzi. Ruhusa ya kuingiza tu.');
        }
        $companyId = $this->getCompanyId();

        // Ensure this manunuzi belongs to this company
        abort_unless($manunuzi->company_id === $companyId, 403, 'Huna ruhusa ya kubadilisha manunuzi haya.');

        // First validate basic fields
        $validator = Validator::make($request->all(), [
            'bidhaa_id' => 'required|exists:bidhaas,id',
            'idadi' => 'required|numeric|min:0.01|regex:/^\d+(\.\d{1,2})?$/',
            'bei_nunua' => 'required|numeric|min:0',
            'bei_kuuza' => 'required|numeric|min:0',
            'bei_type' => 'required|in:kwa_zote,rejareja',
            'expiry' => 'nullable|date',
            'saplaya' => 'nullable|string|max:255',
            'simu' => 'nullable|string|max:20',
            'mengineyo' => 'nullable|string|max:500',
        ], [
            'idadi.required' => 'Idadi inahitajika',
            'idadi.numeric' => 'Idadi lazima iwe namba',
            'idadi.min' => 'Idadi lazima iwe zaidi ya 0',
            'idadi.regex' => 'Idadi inaweza kuwa na sehemu ya desimali hadi nafasi 2 (mfano: 1.5, 2.75)',
        ]);

        // Calculate unit cost based on price type
        $unitCost = 0;
        if ($request->bei_type === 'kwa_zote') {
            // User entered total price for all items
            $unitCost = $request->idadi > 0 ? $request->bei_nunua / $request->idadi : 0;
        } else {
            // User entered price per single item
            $unitCost = $request->bei_nunua;
        }

        // Custom validation: Compare selling price with calculated unit cost
        $validator->after(function ($validator) use ($request, $unitCost) {
            if ($request->bei_kuuza < $unitCost) {
                $validator->errors()->add('bei_kuuza', 'Bei ya kuuza haiwezi kuwa chini ya bei ya kununua kwa kimoja');
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Hitilafu katika uthibitishaji',
                'errors' => $validator->errors()
            ], 422);
        }

        return DB::transaction(function () use ($request, $manunuzi, $companyId, $unitCost) {
            $oldIdadi = $manunuzi->idadi;

            $bidhaa = Bidhaa::where('id', $request->bidhaa_id)
                            ->where('company_id', $companyId)
                            ->firstOrFail();

            // Calculate total cost
            $totalCost = ($request->bei_type === 'kwa_zote') 
                ? $request->bei_nunua 
                : ($unitCost * $request->idadi);

            // Update manunuzi
            $manunuzi->update([
                'bidhaa_id' => $bidhaa->id,
                'idadi' => $request->idadi,
                'bei' => $totalCost,
                'unit_cost' => $unitCost,
                'expiry' => $request->expiry,
                'saplaya' => $request->saplaya,
                'simu' => $request->simu,
                'mengineyo' => $request->mengineyo,
            ]);

            // Adjust stock difference
            $difference = $request->idadi - $oldIdadi;
            $bidhaa->increment('idadi', $difference);

            // Update prices
            $bidhaa->bei_nunua = $unitCost;
            $bidhaa->bei_kuuza = $request->bei_kuuza;
            $bidhaa->save();

            return response()->json([
                'success' => true,
                'message' => 'Manunuzi yamebadilishwa kikamilifu!'
            ]);
        });
    }

    /**
     * Delete a manunuzi and reduce stock (company specific).
     * mdogo employees are blocked.
     */
    public function destroy(Manunuzi $manunuzi)
    {
        if (!$this->canEditDeleteManunuzi()) {
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Huna ruhusa ya kufuta manunuzi. Ruhusa ya kuingiza tu.'], 403);
            }
            abort(403, 'Huna ruhusa ya kufuta manunuzi. Ruhusa ya kuingiza tu.');
        }
        $companyId = $this->getCompanyId();

        abort_unless($manunuzi->company_id === $companyId, 403, 'Huna ruhusa ya kufuta manunuzi haya.');

        return DB::transaction(function () use ($manunuzi) {
            $bidhaa = $manunuzi->bidhaa;
            if ($bidhaa) {
                $bidhaa->decrement('idadi', $manunuzi->idadi);
            }

            $manunuzi->delete();

            return response()->json([
                'success' => true,
                'message' => 'Manunuzi yamefutwa kikamilifu na stock imepunguzwa.'
            ]);
        });
    }

    /**
     * Get product details for AJAX request
     */
    public function getProductDetails($id)
    {
        $companyId = $this->getCompanyId();
        
        $bidhaa = Bidhaa::where('id', $id)
                        ->where('company_id', $companyId)
                        ->firstOrFail();
        
        return response()->json([
            'success' => true,
            'data' => [
                'jina' => $bidhaa->jina,
                'aina' => $bidhaa->aina,
                'kipimo' => $bidhaa->kipimo,
                'idadi' => $bidhaa->idadi,
                'bei_nunua' => $bidhaa->bei_nunua,
                'bei_kuuza' => $bidhaa->bei_kuuza,
            ]
        ]);
    }
}