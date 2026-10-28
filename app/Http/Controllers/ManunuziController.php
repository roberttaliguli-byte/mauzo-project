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
use Illuminate\Support\Str;

class ManunuziController extends Controller
{
    private function getAuthenticatedUser()
    {
        if (Auth::guard('mfanyakazi')->check()) return Auth::guard('mfanyakazi')->user();
        if (Auth::guard('web')->check()) return Auth::guard('web')->user();
        abort(403, 'Unauthorized');
    }

    private function getCompanyId()
    {
        return $this->getAuthenticatedUser()->company_id;
    }

    private function isBoss()
    {
        return Auth::guard('web')->check();
    }

    private function canEditDeleteManunuzi()
    {
        if (Auth::guard('web')->check()) return true;
        $employee = Auth::guard('mfanyakazi')->user();
        if (!$employee) return false;
        return in_array(strtolower(trim($employee->uwezo ?? '')), ['mkubwa']);
    }

    public function index(Request $request)
    {
        $companyId = $this->getCompanyId();
        $perPage = $request->input('per_page', 10);

        $query = Manunuzi::with(['bidhaa:id,jina,aina,kipimo,bei_kuuza', 'creator:id,name'])
            ->where('company_id', $companyId)
            ->orderBy('created_at', 'desc');

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('risiti_number', 'LIKE', "%{$search}%")
                  ->orWhere('batch_id', 'LIKE', "%{$search}%")
                  ->orWhereHas('bidhaa', function($subQ) use ($search) {
                      $subQ->where('jina', 'LIKE', "%{$search}%")
                           ->orWhere('aina', 'LIKE', "%{$search}%");
                  })
                  ->orWhere('saplaya', 'LIKE', "%{$search}%")
                  ->orWhere('simu', 'LIKE', "%{$search}%");
            });
        }

        if ($request->has('start_date') && $request->has('end_date') && !empty($request->start_date) && !empty($request->end_date)) {
            $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }

        $manunuzi = $query->paginate($perPage)->appends($request->except('page'));
        $bidhaa = Bidhaa::where('company_id', $companyId)->orderBy('jina')->get();

        $todayStart = today()->startOfDay();
        $todayEnd = today()->endOfDay();
        $todayPurchases = Manunuzi::where('company_id', $companyId)->whereBetween('created_at', [$todayStart, $todayEnd])->count();
        $totalItemsPurchased = (float) Manunuzi::where('company_id', $companyId)->sum('idadi');
        $totalCost = (float) Manunuzi::where('company_id', $companyId)->sum('bei');
        $todayCost = (float) Manunuzi::where('company_id', $companyId)->whereBetween('created_at', [$todayStart, $todayEnd])->sum('bei');

        $allManunuzi = Manunuzi::with(['bidhaa:id,jina,aina,kipimo,bei_kuuza', 'creator:id,name'])->where('company_id', $companyId)->orderBy('created_at', 'desc')->limit(200)->get();
        $canEditDelete = $this->canEditDeleteManunuzi();
        $isBoss = $this->isBoss();

        return view('manunuzi.index', compact(
            'manunuzi', 'bidhaa', 'todayPurchases', 'totalItemsPurchased', 'totalCost', 'todayCost',
            'allManunuzi', 'canEditDelete', 'isBoss'
        ));
    }

    public function store(Request $request)
    {
        $companyId = $this->getCompanyId();

        // BATCH (RISITI) LOGIC
        if ($request->has('batch_items') && is_array($request->batch_items)) {
            return $this->storeBatch($request, $companyId);
        }

        // SINGLE ITEM LOGIC
        $validator = Validator::make($request->all(), [
            'bidhaa_id' => 'required|exists:bidhaas,id',
            'idadi' => 'required|numeric|min:0.01',
            'bei_nunua' => 'required|numeric|min:0',
            'bei_kuuza' => 'required|numeric|min:0',
            'bei_type' => 'required|in:kwa_zote,rejareja',
            'expiry' => 'nullable|date',
            'saplaya' => 'nullable|string|max:255',
            'simu' => 'nullable|string|max:20',
            'mengineyo' => 'nullable|string|max:500',
        ]);

        $unitCost = $request->bei_type === 'kwa_zote' ? ($request->idadi > 0 ? $request->bei_nunua / $request->idadi : 0) : $request->bei_nunua;

        $validator->after(function ($validator) use ($request, $unitCost) {
            if ($request->bei_kuuza < $unitCost) $validator->errors()->add('bei_kuuza', 'Bei ya kuuza haiwezi kuwa chini ya bei ya kununua');
        });

        if ($validator->fails()) return response()->json(['success' => false, 'message' => 'Hitilafu katika uthibitishaji', 'errors' => $validator->errors()], 422);

        return DB::transaction(function () use ($request, $companyId, $unitCost) {
            $bidhaa = Bidhaa::where('id', $request->bidhaa_id)->where('company_id', $companyId)->firstOrFail();
            $totalCost = $request->bei_type === 'kwa_zote' ? $request->bei_nunua : ($unitCost * $request->idadi);

            $manunuzi = Manunuzi::create([
                'company_id' => $companyId,
                'bidhaa_id' => $bidhaa->id,
                'idadi' => $request->idadi,
                'bei' => $totalCost,
                'unit_cost' => $unitCost,
                'expiry' => $request->expiry,
                'saplaya' => $request->saplaya,
                'simu' => $request->simu,
                'mengineyo' => $request->mengineyo,
                'hali_ya_malipo' => 'imekamilika',
                'kiasi_kilicholipwa' => $totalCost,
                'deni_lililobaki' => 0,
                'created_by' => $this->getAuthenticatedUser()->id,
            ]);

            ActivityHelper::logPurchase($manunuzi, $bidhaa->jina, $manunuzi->bei);

            $bidhaa->increment('idadi', $request->idadi);
            $bidhaa->bei_nunua = $unitCost;
            $bidhaa->bei_kuuza = $request->bei_kuuza;
            $bidhaa->save();

            return response()->json(['success' => true, 'message' => 'Manunuzi yamehifadhiwa!', 'redirect' => route('manunuzi.risiti', $manunuzi)]);
        });
    }

    private function storeBatch(Request $request, $companyId)
    {
        $items = $request->input('batch_items');
        if (empty($items)) return response()->json(['success' => false, 'message' => 'Hakuna bidhaa'], 422);

        return DB::transaction(function () use ($request, $items, $companyId) {
            // 1. GENERATE A SINGLE BATCH ID FOR ALL ITEMS
            $batchId = 'RIS-' . date('Ymd') . '-' . strtoupper(Str::random(4));
            $userId = $this->getAuthenticatedUser()->id;
            $firstId = null;

            foreach ($items as $item) {
                $bidhaa = Bidhaa::where('id', $item['bidhaa_id'])->where('company_id', $companyId)->firstOrFail();
                $idadi = (float) $item['idadi'];
                $totalCost = (float) $item['bei_nunua'];
                $unitCost = $idadi > 0 ? $totalCost / $idadi : 0;

                $manunuzi = Manunuzi::create([
                    'company_id' => $companyId,
                    'batch_id' => $batchId, // 2. ASSIGN THE SAME BATCH ID TO EVERY ITEM
                    'bidhaa_id' => $bidhaa->id,
                    'idadi' => $idadi,
                    'bei' => $totalCost,
                    'unit_cost' => $unitCost,
                    'saplaya' => $request->saplaya,
                    'simu' => $request->simu,
                    'mengineyo' => $request->mengineyo,
                    'hali_ya_malipo' => 'imekamilika',
                    'kiasi_kilicholipwa' => $totalCost,
                    'deni_lililobaki' => 0,
                    'created_by' => $userId,
                ]);

                if (!$firstId) $firstId = $manunuzi->id;

                $bidhaa->increment('idadi', $idadi);
                $bidhaa->bei_nunua = $unitCost;
                $bidhaa->bei_kuuza = $item['bei_kuuza'];
                $bidhaa->save();
            }

            return response()->json([
                'success' => true, 
                'message' => 'Risiti imehifadhiwa!',
                'redirect' => route('manunuzi.risiti', $firstId) // 3. REDIRECT TO ONE RECEIPT PAGE
            ]);
        });
    }

    public function update(Request $request, Manunuzi $manunuzi)
    {
        if (!$this->canEditDeleteManunuzi()) return response()->json(['success' => false, 'message' => 'Huna ruhusa'], 403);
        $companyId = $this->getCompanyId();
        abort_unless($manunuzi->company_id === $companyId, 403);

        return DB::transaction(function () use ($request, $manunuzi, $companyId) {
            $oldIdadi = $manunuzi->idadi;
            $bidhaa = Bidhaa::where('id', $request->bidhaa_id)->where('company_id', $companyId)->firstOrFail();
            $unitCost = $request->bei_type === 'kwa_zote' ? ($request->idadi > 0 ? $request->bei_nunua / $request->idadi : 0) : $request->bei_nunua;
            $totalCost = $request->bei_type === 'kwa_zote' ? $request->bei_nunua : ($unitCost * $request->idadi);

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

            $difference = $request->idadi - $oldIdadi;
            $bidhaa->increment('idadi', $difference);
            $bidhaa->bei_nunua = $unitCost;
            $bidhaa->bei_kuuza = $request->bei_kuuza;
            $bidhaa->save();

            return response()->json(['success' => true, 'message' => 'Manunuzi yamebadilishwa!']);
        });
    }

    public function destroy(Manunuzi $manunuzi)
    {
        if (!$this->canEditDeleteManunuzi()) return response()->json(['success' => false, 'message' => 'Huna ruhusa'], 403);
        $companyId = $this->getCompanyId();
        abort_unless($manunuzi->company_id === $companyId, 403);

        return DB::transaction(function () use ($manunuzi) {
            $bidhaa = $manunuzi->bidhaa;
            if ($bidhaa) $bidhaa->decrement('idadi', $manunuzi->idadi);
            $manunuzi->delete();
            return response()->json(['success' => true, 'message' => 'Manunuzi yamefutwa!']);
        });
    }

    public function showRisiti(Manunuzi $manunuzi)
    {
        $companyId = $this->getCompanyId();
        abort_unless($manunuzi->company_id === $companyId, 403);
        
        // If it has a batch_id, load ALL items in that batch
        if ($manunuzi->batch_id) {
            $items = Manunuzi::with(['bidhaa', 'creator', 'company'])
                ->where('company_id', $companyId)
                ->where('batch_id', $manunuzi->batch_id)
                ->get();
        } else {
            // Single item
            $manunuzi->load(['bidhaa', 'creator', 'company']);
            $items = collect([$manunuzi]);
        }

        $total = $items->sum('bei');
        $firstItem = $items->first();

        return view('manunuzi.risiti', [
            'items' => $items,
            'total' => $total,
            'supplier' => $firstItem->saplaya,
            'simu' => $firstItem->simu,
            'date' => $firstItem->created_at,
            'creator' => $firstItem->creator,
            'company' => $firstItem->company,
            'risiti_number' => $firstItem->batch_id ?? $firstItem->risiti_number,
            'manunuzi' => $manunuzi,
        ]);
    }

    public function risitiRegister(Request $request)
    {
        $companyId = $this->getCompanyId();
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        $manunuzi = Manunuzi::with(['bidhaa', 'creator'])->where('company_id', $companyId)->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])->orderBy('created_at', 'asc')->get();
        $totalItems = $manunuzi->sum('idadi');
        $totalCost = $manunuzi->sum('bei');
        $groupedManunuzi = $manunuzi->groupBy(fn($item) => $item->created_at->format('Y-m-d'));
        return view('manunuzi.risiti-register', compact('groupedManunuzi', 'startDate', 'endDate', 'totalItems', 'totalCost'));
    }
}