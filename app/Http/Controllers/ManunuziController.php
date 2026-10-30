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
        $userId = $this->getAuthenticatedUser()->id;

        $query = Manunuzi::with(['bidhaa:id,jina,aina,kipimo,bei_kuuza', 'creator:id,name'])
            ->where('company_id', $companyId)
            ->where('status', 'final')
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
        $todayPurchases = Manunuzi::where('company_id', $companyId)->where('status', 'final')->whereBetween('created_at', [$todayStart, $todayEnd])->count();
        $totalItemsPurchased = (float) Manunuzi::where('company_id', $companyId)->where('status', 'final')->sum('idadi');
        $totalCost = (float) Manunuzi::where('company_id', $companyId)->where('status', 'final')->sum('bei');
        $todayCost = (float) Manunuzi::where('company_id', $companyId)->where('status', 'final')->whereBetween('created_at', [$todayStart, $todayEnd])->sum('bei');

        $allManunuzi = Manunuzi::with(['bidhaa:id,jina,aina,kipimo,bei_kuuza', 'creator:id,name'])
            ->where('company_id', $companyId)
            ->where('status', 'final')
            ->orderBy('created_at', 'desc')
            ->limit(200)
            ->get();

        $drafts = Manunuzi::with(['bidhaa:id,jina,aina,kipimo,bei_kuuza'])
            ->where('company_id', $companyId)
            ->where('status', 'draft')
            ->where('draft_owner_id', $userId)
            ->orderBy('draft_updated_at', 'desc')
            ->get()
            ->groupBy('draft_token');

        $canEditDelete = $this->canEditDeleteManunuzi();
        $isBoss = $this->isBoss();

        return view('manunuzi.index', compact(
            'manunuzi', 'bidhaa', 'todayPurchases', 'totalItemsPurchased', 'totalCost', 'todayCost',
            'allManunuzi', 'canEditDelete', 'isBoss', 'drafts'
        ));
    }

    public function store(Request $request)
    {
        $companyId = $this->getCompanyId();

        if ($request->has('batch_items') && is_array($request->batch_items)) {
            return $this->storeBatch($request, $companyId);
        }

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
                'status' => 'final',
            ]);

            ActivityHelper::logPurchase($manunuzi, $bidhaa->jina, $manunuzi->bei);

            $bidhaa->increment('idadi', $request->idadi);
            $bidhaa->bei_nunua = $unitCost;
            $bidhaa->bei_kuuza = $request->bei_kuuza;
            $bidhaa->save();

            return response()->json(['success' => true, 'message' => 'Manunuzi yamehifadhiwa!', 'redirect' => route('manunuzi.risiti', $manunuzi)]);
        });
    }

    /* ============================================================
     * HELPERS FOR NEW PRODUCTS
     * ============================================================ */

    /** Find an existing product that matches the "new" product, or create it (stock 0). */
    private function findOrCreateBidhaaFromNew(array $np, $companyId, float $unitCost, float $beiKuuza): Bidhaa
    {
        $jina    = trim($np['jina'] ?? '');
        $aina    = trim($np['aina'] ?? '');
        $kipimo  = trim($np['kipimo'] ?? '') ?: null;
        $barcode = trim($np['barcode'] ?? '') ?: null;

        if ($jina === '' || $aina === '') {
            throw new \Exception('Jina na aina ya bidhaa mpya vinahitajika');
        }

        if ($barcode) {
            $byBarcode = Bidhaa::where('company_id', $companyId)->where('barcode', $barcode)->first();
            if ($byBarcode) return $byBarcode;
        }

        $q = Bidhaa::where('company_id', $companyId)->where('jina', $jina)->where('aina', $aina);
        if ($kipimo) {
            $q->where('kipimo', $kipimo);
        } else {
            $q->where(fn($x) => $x->whereNull('kipimo')->orWhere('kipimo', ''));
        }
        if ($existing = $q->first()) return $existing;

        return Bidhaa::create([
            'company_id' => $companyId,
            'jina' => $jina,
            'aina' => $aina,
            'kipimo' => $kipimo,
            'idadi' => 0,
            'bei_nunua' => $unitCost,
            'bei_kuuza' => $beiKuuza,
            'bei_kiasi_cha_chaguo' => 'rejareja',
            'expiry' => !empty($np['expiry']) ? $np['expiry'] : null,
            'barcode' => $barcode,
        ]);
    }

    private function cleanNewBidhaa(array $np): array
    {
        return [
            'jina' => trim($np['jina'] ?? ''),
            'aina' => trim($np['aina'] ?? ''),
            'kipimo' => trim($np['kipimo'] ?? ''),
            'barcode' => trim($np['barcode'] ?? ''),
            'expiry' => $np['expiry'] ?? null,
        ];
    }

    /* ============================================================
     * BATCH STORE (supports NEW products + closes draft)
     * ============================================================ */
    private function storeBatch(Request $request, $companyId)
    {
        $items = $request->input('batch_items');
        if (empty($items)) return response()->json(['success' => false, 'message' => 'Hakuna bidhaa'], 422);

        // Validate everything BEFORE touching the database
        foreach ($items as $i => $item) {
            $n = $i + 1;
            $isNew = !empty($item['is_new']);
            $idadi = (float) ($item['idadi'] ?? 0);
            $total = (float) ($item['bei_nunua'] ?? 0);
            $kuuza = (float) ($item['bei_kuuza'] ?? 0);

            if (!$isNew && empty($item['bidhaa_id'])) return response()->json(['success' => false, 'message' => "Bidhaa #{$n}: bidhaa haijachaguliwa"], 422);
            if ($isNew && empty($item['new_bidhaa']['jina'])) return response()->json(['success' => false, 'message' => "Bidhaa #{$n}: jina la bidhaa mpya linakosekana"], 422);
            if ($idadi <= 0 || $total <= 0) return response()->json(['success' => false, 'message' => "Bidhaa #{$n}: idadi au bei si sahihi"], 422);
            if ($kuuza < ($total / $idadi)) return response()->json(['success' => false, 'message' => "Bidhaa #{$n}: bei ya kuuza ni chini ya bei ya kununua"], 422);
        }

        try {
            return DB::transaction(function () use ($request, $items, $companyId) {
                $batchId = 'RIS-' . date('Ymd') . '-' . strtoupper(Str::random(4));
                $userId = $this->getAuthenticatedUser()->id;
                $firstId = null;

                foreach ($items as $item) {
                    $idadi = (float) $item['idadi'];
                    $totalCost = (float) $item['bei_nunua'];
                    $unitCost = $idadi > 0 ? $totalCost / $idadi : 0;
                    $beiKuuza = (float) $item['bei_kuuza'];

                    if (!empty($item['is_new'])) {
                        $bidhaa = $this->findOrCreateBidhaaFromNew($item['new_bidhaa'], $companyId, $unitCost, $beiKuuza);
                    } else {
                        $bidhaa = Bidhaa::where('id', $item['bidhaa_id'])->where('company_id', $companyId)->firstOrFail();
                    }

                    $manunuzi = Manunuzi::create([
                        'company_id' => $companyId,
                        'batch_id' => $batchId,
                        'bidhaa_id' => $bidhaa->id,
                        'idadi' => $idadi,
                        'bei' => $totalCost,
                        'unit_cost' => $unitCost,
                        'expiry' => $item['new_bidhaa']['expiry'] ?? null,
                        'saplaya' => $request->saplaya,
                        'simu' => $request->simu,
                        'mengineyo' => $request->mengineyo,
                        'hali_ya_malipo' => 'imekamilika',
                        'kiasi_kilicholipwa' => $totalCost,
                        'deni_lililobaki' => 0,
                        'created_by' => $userId,
                        'status' => 'final',
                    ]);

                    if (!$firstId) $firstId = $manunuzi->id;

                    $bidhaa->increment('idadi', $idadi);
                    $bidhaa->bei_nunua = $unitCost;
                    $bidhaa->bei_kuuza = $beiKuuza;
                    $bidhaa->save();

                    ActivityHelper::logPurchase($manunuzi, $bidhaa->jina, $manunuzi->bei);
                }

                // If this receipt came from a draft, remove the draft
                if ($request->filled('draft_token')) {
                    Manunuzi::where('company_id', $companyId)
                        ->where('draft_owner_id', $userId)
                        ->where('status', 'draft')
                        ->where('draft_token', $request->draft_token)
                        ->delete();
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Risiti imehifadhiwa!',
                    'redirect' => route('manunuzi.risiti', $firstId),
                ]);
            });
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /* ============================================================
     * DRAFT MANAGEMENT
     * ============================================================ */

    /**
     * List all active drafts for current user (for batch tab UI)
     */
    public function listDrafts(Request $request)
    {
        $companyId = $this->getCompanyId();
        $userId = $this->getAuthenticatedUser()->id;

        $drafts = Manunuzi::where('company_id', $companyId)
            ->where('draft_owner_id', $userId)
            ->where('status', 'draft')
            ->orderBy('draft_updated_at', 'desc')
            ->get()
            ->groupBy('draft_token');

        $result = [];
        foreach ($drafts as $token => $items) {
            $first = $items->first();
            $result[] = [
                'draft_token' => $token,
                'saplaya'     => $first->saplaya,
                'simu'        => $first->simu,
                'mengineyo'   => $first->mengineyo,
                'item_count'  => $items->count(),
                'updated_at'  => optional($first->draft_updated_at)->format('d/m/Y H:i') ?? '-',
            ];
        }

        return response()->json(['success' => true, 'drafts' => $result]);
    }

    /* ============================================================
     * SAVE DRAFT (existing OR new products, no stock change)
     * ============================================================ */
    public function saveDraft(Request $request)
    {
        $companyId = $this->getCompanyId();
        $userId = $this->getAuthenticatedUser()->id;
        $items = $request->input('batch_items', []);
        $draftToken = $request->input('draft_token');

        if (empty($items)) {
            return response()->json(['success' => false, 'message' => 'Hakuna bidhaa za kuhifadhi'], 422);
        }

        foreach ($items as $index => $item) {
            $isNew = !empty($item['is_new']);
            $ok = !empty($item['idadi']) && ($isNew ? !empty($item['new_bidhaa']['jina']) : !empty($item['bidhaa_id']));
            if (!$ok) {
                return response()->json(['success' => false, 'message' => "Bidhaa #" . ($index + 1) . " haina taarifa kamili"], 422);
            }
        }

        return DB::transaction(function () use ($request, $items, $companyId, $userId, $draftToken) {
            if ($draftToken) {
                Manunuzi::where('company_id', $companyId)
                    ->where('draft_owner_id', $userId)
                    ->where('status', 'draft')
                    ->where('draft_token', $draftToken)
                    ->delete();
            } else {
                $draftToken = 'DRAFT-' . date('Ymd') . '-' . strtoupper(Str::random(8));
            }

            $saved = 0;
            foreach ($items as $item) {
                $isNew = !empty($item['is_new']);
                $bidhaaId = null;

                if (!$isNew) {
                    $bidhaa = Bidhaa::where('id', $item['bidhaa_id'])->where('company_id', $companyId)->first();
                    if (!$bidhaa) continue;
                    $bidhaaId = $bidhaa->id;
                }

                $idadi = (float) ($item['idadi'] ?? 0);
                $totalCost = (float) ($item['bei_nunua'] ?? 0);
                $unitCost = $idadi > 0 ? $totalCost / $idadi : 0;

                Manunuzi::create([
                    'company_id' => $companyId,
                    'batch_id' => null,
                    'risiti_number' => null,
                    'bidhaa_id' => $bidhaaId,
                    'new_bidhaa' => $isNew ? $this->cleanNewBidhaa($item['new_bidhaa']) : null,
                    'idadi' => $idadi,
                    'bei' => $totalCost,
                    'unit_cost' => $unitCost,
                    'bei_kuuza_draft' => isset($item['bei_kuuza']) ? (float) $item['bei_kuuza'] : null,
                    'saplaya' => $request->saplaya,
                    'simu' => $request->simu,
                    'mengineyo' => $request->mengineyo,
                    'hali_ya_malipo' => 'imekamilika',
                    'kiasi_kilicholipwa' => $totalCost,
                    'deni_lililobaki' => 0,
                    'created_by' => $userId,
                    'status' => 'draft',
                    'draft_token' => $draftToken,
                    'draft_owner_id' => $userId,
                    'draft_updated_at' => now(),
                ]);
                $saved++;
            }

            return response()->json([
                'success' => true,
                'message' => 'Draft imehifadhiwa! Unaweza kuendelea baadaye.',
                'draft_token' => $draftToken,
                'count' => $saved,
            ]);
        });
    }

    public function updateDraft(Request $request, $draftToken)
    {
        $companyId = $this->getCompanyId();
        $userId = $this->getAuthenticatedUser()->id;

        $draft = Manunuzi::where('company_id', $companyId)
            ->where('draft_owner_id', $userId)
            ->where('status', 'draft')
            ->where('draft_token', $draftToken)
            ->first();

        if (!$draft) {
            return response()->json(['success' => false, 'message' => 'Draft haipatikani'], 404);
        }

        $request->merge(['draft_token' => $draftToken]);
        return $this->saveDraft($request);
    }

    public function loadDraft(Request $request, $draftToken)
    {
        $companyId = $this->getCompanyId();
        $userId = $this->getAuthenticatedUser()->id;

        $draftItems = Manunuzi::with(['bidhaa:id,jina,aina,kipimo,bei_kuuza,bei_nunua,barcode'])
            ->where('company_id', $companyId)
            ->where('draft_owner_id', $userId)
            ->where('status', 'draft')
            ->where('draft_token', $draftToken)
            ->get();

        if ($draftItems->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Draft haipatikani'], 404);
        }

        $first = $draftItems->first();

        $items = $draftItems->map(function ($item) {
            $np = $item->new_bidhaa;
            $isNew = empty($item->bidhaa_id) && !empty($np);
            return [
                'id' => $item->id,
                'bidhaa_id' => $item->bidhaa_id,
                'is_new' => $isNew,
                'new_bidhaa' => $isNew ? $np : null,
                'jina' => $isNew ? ($np['jina'] ?? '') : ($item->bidhaa->jina ?? ''),
                'aina' => $isNew ? ($np['aina'] ?? '') : ($item->bidhaa->aina ?? ''),
                'kipimo' => $isNew ? ($np['kipimo'] ?? '') : ($item->bidhaa->kipimo ?? ''),
                'idadi' => (float) $item->idadi,
                'bei_nunua' => (float) $item->bei,
                'bei_kuuza' => (float) ($item->bei_kuuza_draft ?? ($item->bidhaa->bei_kuuza ?? 0)),
            ];
        });

        return response()->json([
            'success' => true,
            'draft_token' => $draftToken,
            'saplaya' => $first->saplaya,
            'simu' => $first->simu,
            'mengineyo' => $first->mengineyo,
            'items' => $items,
            'updated_at' => $first->draft_updated_at?->toISOString(),
        ]);
    }

    public function deleteDraft(Request $request, $draftToken)
    {
        $companyId = $this->getCompanyId();
        $userId = $this->getAuthenticatedUser()->id;

        $deleted = Manunuzi::where('company_id', $companyId)
            ->where('draft_owner_id', $userId)
            ->where('status', 'draft')
            ->where('draft_token', $draftToken)
            ->delete();

        if (!$deleted) {
            return response()->json(['success' => false, 'message' => 'Draft haipatikani'], 404);
        }

        return response()->json(['success' => true, 'message' => 'Draft imefutwa!']);
    }

    public function finalizeDraft(Request $request, $draftToken)
    {
        $companyId = $this->getCompanyId();
        $userId = $this->getAuthenticatedUser()->id;

        $draftItems = Manunuzi::with('bidhaa')
            ->where('company_id', $companyId)
            ->where('draft_owner_id', $userId)
            ->where('status', 'draft')
            ->where('draft_token', $draftToken)
            ->get();

        if ($draftItems->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Draft haipatikani au tayari imekamilika'], 404);
        }

        try {
            return DB::transaction(function () use ($draftItems, $companyId) {
                $batchId = 'RIS-' . date('Ymd') . '-' . strtoupper(Str::random(4));
                $firstId = null;

                foreach ($draftItems as $item) {
                    $bidhaa = $item->bidhaa;
                    $beiKuuza = $item->bei_kuuza_draft;

                    if (!$bidhaa && !empty($item->new_bidhaa)) {
                        $bidhaa = $this->findOrCreateBidhaaFromNew(
                            $item->new_bidhaa, $companyId, (float) $item->unit_cost, (float) ($beiKuuza ?? 0)
                        );
                    }
                    if (!$bidhaa) continue;

                    $item->update([
                        'bidhaa_id' => $bidhaa->id,
                        'new_bidhaa' => null,
                        'bei_kuuza_draft' => null,
                        'status' => 'final',
                        'batch_id' => $batchId,
                        'draft_token' => null,
                        'draft_owner_id' => null,
                        'draft_updated_at' => null,
                        'created_at' => now(),
                    ]);

                    if (!$firstId) $firstId = $item->id;

                    $bidhaa->increment('idadi', $item->idadi);
                    $bidhaa->bei_nunua = $item->unit_cost;
                    if ($beiKuuza !== null) $bidhaa->bei_kuuza = $beiKuuza;
                    $bidhaa->save();

                    ActivityHelper::logPurchase($item, $bidhaa->jina, $item->bei);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Draft imekamilika na stock imeongezwa!',
                    'redirect' => route('manunuzi.risiti', $firstId),
                    'batch_id' => $batchId,
                ]);
            });
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /* ============================================================
     * SINGLE-ROW UPDATE / DELETE
     * ============================================================ */

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

        if ($manunuzi->status === 'draft') {
            abort(404, 'Risiti haijakamilika bado');
        }

        if ($manunuzi->batch_id) {
            $items = Manunuzi::with(['bidhaa', 'creator', 'company'])
                ->where('company_id', $companyId)
                ->where('batch_id', $manunuzi->batch_id)
                ->where('status', 'final')
                ->get();
        } else {
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
        $manunuzi = Manunuzi::with(['bidhaa', 'creator'])
            ->where('company_id', $companyId)
            ->where('status', 'final')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->orderBy('created_at', 'asc')
            ->get();
        $totalItems = $manunuzi->sum('idadi');
        $totalCost = $manunuzi->sum('bei');
        $groupedManunuzi = $manunuzi->groupBy(fn($item) => $item->created_at->format('Y-m-d'));
        return view('manunuzi.risiti-register', compact('groupedManunuzi', 'startDate', 'endDate', 'totalItems', 'totalCost'));
    }
}