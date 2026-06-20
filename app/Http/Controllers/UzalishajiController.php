<?php
// app/Http/Controllers/UzalishajiController.php

namespace App\Http\Controllers;

use App\Models\Uzalishaji;
use App\Models\UzalishajiGharama;
use App\Models\Bidhaa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UzalishajiController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $this->getCompanyId();
        
        $query = Uzalishaji::where('company_id', $companyId);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('jina', 'like', "%{$search}%")
                  ->orWhere('aina_bidhaa', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'completed') {
                $query->where('imekamilika', true);
            } elseif ($request->status === 'pending') {
                $query->where('imekamilika', false);
            }
        }

        $uzalishaji = $query->with(['bidhaa', 'gharama'])
                           ->orderBy('created_at', 'desc')
                           ->paginate(20);

        $statistics = [
            'total' => Uzalishaji::where('company_id', $companyId)->count(),
            'completed' => Uzalishaji::where('company_id', $companyId)->where('imekamilika', true)->count(),
            'pending' => Uzalishaji::where('company_id', $companyId)->where('imekamilika', false)->count(),
            'total_cost' => Uzalishaji::where('company_id', $companyId)->sum('jumla_gharama'),
        ];

        $categories = UzalishajiGharama::getCategories();

        return view('uzalishaji.index', compact('uzalishaji', 'statistics', 'categories'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tarehe' => 'required|date',
            'jina' => 'required|string|max:255',
            'aina_bidhaa' => 'required|string|max:255',
            'maelezo' => 'nullable|string',
            'gharama' => 'required|array|min:1',
            'gharama.*.jina' => 'required|string|max:255',
            'gharama.*.kundi' => 'required|string|max:255',
            'gharama.*.kiasi' => 'required|numeric|min:0.01',
            'gharama.*.gharama' => 'required|numeric|min:0',
            'idadi' => 'required|numeric|min:0.01',
            'kipimo' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $companyId = $this->getCompanyId();

        DB::beginTransaction();
        try {
            $totalCost = 0;
            foreach ($request->gharama as $cost) {
                $totalCost += $cost['gharama'];
            }

            $uzalishaji = Uzalishaji::create([
                'tarehe' => $request->tarehe,
                'jina' => $request->jina,
                'aina_bidhaa' => $request->aina_bidhaa,
                'maelezo' => $request->maelezo,
                'jumla_gharama' => $totalCost,
                'idadi_iliyozalishwa' => $request->idadi,
                'kipimo' => $request->kipimo,
                'gharama_kwa_moja' => $request->idadi > 0 ? $totalCost / $request->idadi : 0,
                'bei_kununua_ilipendekezwa' => $request->idadi > 0 ? $totalCost / $request->idadi : 0,
                'company_id' => $companyId,
                'status' => 'in_progress'
            ]);

            foreach ($request->gharama as $cost) {
                UzalishajiGharama::create([
                    'uzalishaji_id' => $uzalishaji->id,
                    'jina' => $cost['jina'],
                    'kundi' => $cost['kundi'],
                    'kiasi' => $cost['kiasi'],
                    'gharama' => $cost['gharama'],
                    'company_id' => $companyId,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Uzalishaji umeanzishwa kwa mafanikio!',
                'data' => ['id' => $uzalishaji->id]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Kuna hitilafu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $companyId = $this->getCompanyId();
        $uzalishaji = Uzalishaji::where('company_id', $companyId)->findOrFail($id);

        if ($uzalishaji->imekamilika) {
            return response()->json([
                'success' => false,
                'message' => 'Uzalishaji huu tayari umekamilika.'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'tarehe' => 'required|date',
            'jina' => 'required|string|max:255',
            'aina_bidhaa' => 'required|string|max:255',
            'maelezo' => 'nullable|string',
            'gharama' => 'required|array|min:1',
            'gharama.*.jina' => 'required|string|max:255',
            'gharama.*.kundi' => 'required|string|max:255',
            'gharama.*.kiasi' => 'required|numeric|min:0.01',
            'gharama.*.gharama' => 'required|numeric|min:0',
            'idadi' => 'required|numeric|min:0.01',
            'kipimo' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $totalCost = 0;
            foreach ($request->gharama as $cost) {
                $totalCost += $cost['gharama'];
            }

            $uzalishaji->update([
                'tarehe' => $request->tarehe,
                'jina' => $request->jina,
                'aina_bidhaa' => $request->aina_bidhaa,
                'maelezo' => $request->maelezo,
                'jumla_gharama' => $totalCost,
                'idadi_iliyozalishwa' => $request->idadi,
                'kipimo' => $request->kipimo,
                'gharama_kwa_moja' => $request->idadi > 0 ? $totalCost / $request->idadi : 0,
                'bei_kununua_ilipendekezwa' => $request->idadi > 0 ? $totalCost / $request->idadi : 0,
            ]);

            // Delete old costs and add new ones
            $uzalishaji->gharama()->delete();
            
            foreach ($request->gharama as $cost) {
                UzalishajiGharama::create([
                    'uzalishaji_id' => $uzalishaji->id,
                    'jina' => $cost['jina'],
                    'kundi' => $cost['kundi'],
                    'kiasi' => $cost['kiasi'],
                    'gharama' => $cost['gharama'],
                    'company_id' => $companyId,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Uzalishaji umesasishwa kwa mafanikio!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Kuna hitilafu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function complete(Request $request, $id)
    {
        $companyId = $this->getCompanyId();
        $uzalishaji = Uzalishaji::where('company_id', $companyId)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'jina_bidhaa' => 'required|string|max:255',
            'kipimo' => 'required|string|max:50',
            'idadi' => 'required|numeric|min:0.01',
            'bei_nunua' => 'required|numeric|min:0',
            'bei_kuuza' => 'required|numeric|min:0',
            'bei_jumla' => 'nullable|numeric|min:0',
            'barcode' => 'nullable|string|max:100',
            'expiry' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Create product
            $bidhaa = Bidhaa::create([
                'jina' => $request->jina_bidhaa,
                'aina' => $uzalishaji->aina_bidhaa,
                'kipimo' => $request->kipimo,
                'idadi' => $request->idadi,
                'bei_nunua' => $request->bei_nunua,
                'bei_kuuza' => $request->bei_kuuza,
                'bei_uzo_jumla' => $request->bei_jumla,
                'barcode' => $request->barcode,
                'expiry' => $request->expiry,
                'company_id' => $companyId,
            ]);

            // Update production
            $uzalishaji->bidhaa_id = $bidhaa->id;
            $uzalishaji->imekamilika = true;
            $uzalishaji->status = 'completed';
            $uzalishaji->bei_kuuza_ilichaguliwa = $request->bei_kuuza;
            
            $profitPerItem = $request->bei_kuuza - $uzalishaji->gharama_kwa_moja;
            $uzalishaji->faida_kwa_moja = $profitPerItem;
            $uzalishaji->asilimia_faida = ($uzalishaji->gharama_kwa_moja > 0) ? ($profitPerItem / $uzalishaji->gharama_kwa_moja) * 100 : 0;
            $uzalishaji->faida_ya_jumla = $profitPerItem * $uzalishaji->idadi_iliyozalishwa;
            $uzalishaji->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Uzalishaji umekamilika. Bidhaa imeongezwa kwenye stoo!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Kuna hitilafu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $companyId = $this->getCompanyId();
        $uzalishaji = Uzalishaji::where('company_id', $companyId)->findOrFail($id);

        DB::beginTransaction();
        try {
            $uzalishaji->gharama()->delete();
            $uzalishaji->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Uzalishaji umefutwa kwa mafanikio!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Kuna hitilafu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function duplicate($id)
    {
        $companyId = $this->getCompanyId();
        $uzalishaji = Uzalishaji::where('company_id', $companyId)
                               ->with('gharama')
                               ->findOrFail($id);

        DB::beginTransaction();
        try {
            $newUzalishaji = $uzalishaji->replicate();
            $newUzalishaji->jina = $uzalishaji->jina . ' (Nakala)';
            $newUzalishaji->imekamilika = false;
            $newUzalishaji->bidhaa_id = null;
            $newUzalishaji->status = 'in_progress';
            $newUzalishaji->created_at = now();
            $newUzalishaji->save();

            foreach ($uzalishaji->gharama as $cost) {
                $newCost = $cost->replicate();
                $newCost->uzalishaji_id = $newUzalishaji->id;
                $newCost->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Uzalishaji umenakiliwa kwa mafanikio!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Kuna hitilafu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getSummary($id)
    {
        try {
            $companyId = $this->getCompanyId();
            $uzalishaji = Uzalishaji::where('company_id', $companyId)
                                   ->with('gharama')
                                   ->findOrFail($id);

            $costsByCategory = $uzalishaji->gharama->groupBy('kundi')->map(function($items) {
                return $items->sum('gharama');
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $uzalishaji->id,
                    'jina' => $uzalishaji->jina,
                    'tarehe' => $uzalishaji->tarehe->format('Y-m-d'),
                    'aina_bidhaa' => $uzalishaji->aina_bidhaa,
                    'maelezo' => $uzalishaji->maelezo,
                    'total_cost' => $uzalishaji->jumla_gharama,
                    'quantity' => $uzalishaji->idadi_iliyozalishwa,
                    'unit' => $uzalishaji->kipimo,
                    'cost_per_unit' => $uzalishaji->gharama_kwa_moja,
                    'suggested_price' => $uzalishaji->bei_kununua_ilipendekezwa,
                    'costs_by_category' => $costsByCategory,
                    'is_completed' => $uzalishaji->imekamilika,
                    'product_id' => $uzalishaji->bidhaa_id
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hitilafu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reports(Request $request)
    {
        try {
            $companyId = $this->getCompanyId();
            $period = $request->period ?? 'monthly';
            
            $query = Uzalishaji::where('company_id', $companyId);
            
            if ($period === 'daily') {
                $query->whereDate('tarehe', today());
            } elseif ($period === 'weekly') {
                $query->whereBetween('tarehe', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($period === 'monthly') {
                $query->whereMonth('tarehe', now()->month)
                      ->whereYear('tarehe', now()->year);
            }
            
            // For reports, we want completed productions with profit data
            $query->where('imekamilika', true);
            
            $productions = $query->with('bidhaa')->get();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'productions' => $productions,
                    'total_productions' => $productions->count(),
                    'total_quantity' => $productions->sum('idadi_iliyozalishwa'),
                    'total_cost' => $productions->sum('jumla_gharama'),
                    'total_profit' => $productions->sum('faida_ya_jumla'),
                    'period' => $period
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hitilafu: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getCompanyId()
    {
        if (Auth::check()) {
            return Auth::user()->company_id;
        } elseif (Auth::guard('mfanyakazi')->check()) {
            return Auth::guard('mfanyakazi')->user()->company_id;
        }
        return null;
    }
}