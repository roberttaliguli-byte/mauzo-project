<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bidhaa;
use App\Models\Manunuzi;
use App\Helpers\ActivityHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchasesApiController extends Controller
{
    /* ─────────────────────────────────────────────────────
     | GET /api/manunuzi
     | Final purchases, paginated, search + date range.
     ───────────────────────────────────────────────────── */
    public function index(Request $request)
    {
        $companyId = $this->companyId($request);
        if (!$companyId) return $this->err403();

        $perPage = max(1, (int) $request->input('per_page', 20));
        $search  = trim((string) $request->input('search', ''));
        $from    = $request->input('start_date');
        $to      = $request->input('end_date');

        $query = Manunuzi::with([
                'bidhaa:id,jina,aina,kipimo,bei_nunua,bei_kuuza',
                'creator:id,name',
            ])
            ->where('company_id', $companyId)
            ->where('status', 'final')
            ->orderByDesc('created_at');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('risiti_number', 'like', "%{$search}%")
                  ->orWhere('batch_id', 'like', "%{$search}%")
                  ->orWhere('saplaya', 'like', "%{$search}%")
                  ->orWhere('simu', 'like', "%{$search}%")
                  ->orWhereHas('bidhaa', fn ($b) =>
                      $b->where('jina', 'like', "%{$search}%")
                        ->orWhere('aina', 'like', "%{$search}%"));
            });
        }

        if ($from && $to) {
            $query->whereBetween('created_at', [
                $from . ' 00:00:00',
                $to   . ' 23:59:59',
            ]);
        }

        $paginator = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => collect($paginator->items())
                ->map(fn ($m) => $this->payload($m))
                ->values(),
            'summary' => $this->summary($companyId),
            'meta'    => $this->meta($paginator),
        ]);
    }

    /* ─────────────────────────────────────────────────────
     | GET /api/manunuzi/{id}
     | Single purchase + its batch siblings (if any).
     ───────────────────────────────────────────────────── */
    public function show(Request $request, $id)
    {
        $companyId = $this->companyId($request);
        if (!$companyId) return $this->err403();

        $m = Manunuzi::with(['bidhaa', 'creator:id,name'])
            ->where('company_id', $companyId)
            ->where('status', 'final')
            ->find($id);

        if (!$m) return $this->err('Manunuzi haipatikani.', 404);

        $batch = $m->batch_id
            ? Manunuzi::with('bidhaa')
                ->where('company_id', $companyId)
                ->where('batch_id', $m->batch_id)
                ->where('status', 'final')
                ->get()
                ->map(fn ($b) => $this->payload($b))
            : collect([$this->payload($m)]);

        return response()->json([
            'success' => true,
            'data'    => $this->payload($m),
            'batch'   => $batch,
        ]);
    }

    /* ─────────────────────────────────────────────────────
     | POST /api/manunuzi
     | Single-line create (or batch via `items`).
     ───────────────────────────────────────────────────── */
    public function store(Request $request)
    {
        $companyId = $this->companyId($request);
        if (!$companyId) return $this->err403();
        $userId = $request->user()->id;

        // Batch mode
        if ($request->has('items') && is_array($request->items)) {
            return $this->storeBatch($request, $companyId, $userId);
        }

        // Single mode
        $v = validator($request->all(), [
            'bidhaa_id' => 'required|exists:bidhaas,id',
            'idadi'     => 'required|numeric|min:0.01',
            'bei_nunua' => 'required|numeric|min:0',
            'bei_kuuza' => 'required|numeric|min:0',
            'bei_type'  => 'required|in:kwa_zote,rejareja',
            'expiry'    => 'nullable|date',
            'saplaya'   => 'nullable|string|max:255',
            'simu'      => 'nullable|string|max:20',
            'mengineyo' => 'nullable|string|max:500',
        ]);

        if ($v->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $v->errors(),
                'message' => $v->errors()->first(),
            ], 422);
        }

        $unitCost = $request->bei_type === 'kwa_zote'
            ? ($request->idadi > 0 ? $request->bei_nunua / $request->idadi : 0)
            : $request->bei_nunua;

        if ($request->bei_kuuza < $unitCost) {
            return $this->err('Bei ya kuuza haiwezi kuwa chini ya bei ya kununua.', 422);
        }

        return DB::transaction(function () use ($request, $companyId, $userId, $unitCost) {
            $bidhaa = Bidhaa::where('id', $request->bidhaa_id)
                ->where('company_id', $companyId)
                ->firstOrFail();

            $totalCost = $request->bei_type === 'kwa_zote'
                ? $request->bei_nunua
                : ($unitCost * $request->idadi);

            $manunuzi = Manunuzi::create([
                'company_id'         => $companyId,
                'bidhaa_id'          => $bidhaa->id,
                'idadi'              => $request->idadi,
                'bei'                => $totalCost,
                'unit_cost'          => $unitCost,
                'expiry'             => $request->expiry,
                'saplaya'            => $request->saplaya,
                'simu'               => $request->simu,
                'mengineyo'          => $request->mengineyo,
                'hali_ya_malipo'     => 'imekamilika',
                'kiasi_kilicholipwa' => $totalCost,
                'deni_lililobaki'    => 0,
                'created_by'         => $userId,
                'status'             => 'final',
            ]);

            ActivityHelper::logPurchase($manunuzi, $bidhaa->jina, $manunuzi->bei);

            $bidhaa->increment('idadi', $request->idadi);
            $bidhaa->bei_nunua = $unitCost;
            $bidhaa->bei_kuuza = $request->bei_kuuza;
            $bidhaa->save();

            return response()->json([
                'success' => true,
                'message' => 'Manunuzi yamehifadhiwa!',
                'data'    => $this->payload($manunuzi->fresh(['bidhaa'])),
            ]);
        });
    }

    /* ─────────────────────────────────────────────────────
     | PUT /api/manunuzi/{id}
     ───────────────────────────────────────────────────── */
    public function update(Request $request, $id)
    {
        $companyId = $this->companyId($request);
        if (!$companyId) return $this->err403();

        $m = Manunuzi::where('company_id', $companyId)
            ->where('status', 'final')
            ->find($id);
        if (!$m) return $this->err('Manunuzi haipatikani.', 404);

        $v = validator($request->all(), [
            'bidhaa_id' => 'required|exists:bidhaas,id',
            'idadi'     => 'required|numeric|min:0.01',
            'bei_nunua' => 'required|numeric|min:0',
            'bei_kuuza' => 'required|numeric|min:0',
            'bei_type'  => 'required|in:kwa_zote,rejareja',
        ]);
        if ($v->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $v->errors(),
                'message' => $v->errors()->first(),
            ], 422);
        }

        return DB::transaction(function () use ($request, $m, $companyId) {
            $oldIdadi = (float) $m->idadi;
            $bidhaa = Bidhaa::where('id', $request->bidhaa_id)
                ->where('company_id', $companyId)
                ->firstOrFail();

            $unitCost = $request->bei_type === 'kwa_zote'
                ? ($request->idadi > 0 ? $request->bei_nunua / $request->idadi : 0)
                : $request->bei_nunua;
            $totalCost = $request->bei_type === 'kwa_zote'
                ? $request->bei_nunua
                : ($unitCost * $request->idadi);

            $m->update([
                'bidhaa_id' => $bidhaa->id,
                'idadi'     => $request->idadi,
                'bei'       => $totalCost,
                'unit_cost' => $unitCost,
                'expiry'    => $request->expiry,
                'saplaya'   => $request->saplaya,
                'simu'      => $request->simu,
                'mengineyo' => $request->mengineyo,
            ]);

            $bidhaa->increment('idadi', $request->idadi - $oldIdadi);
            $bidhaa->bei_nunua = $unitCost;
            $bidhaa->bei_kuuza = $request->bei_kuuza;
            $bidhaa->save();

            return response()->json([
                'success' => true,
                'message' => 'Manunuzi yamebadilishwa!',
                'data'    => $this->payload($m->fresh(['bidhaa'])),
            ]);
        });
    }

    /* ─────────────────────────────────────────────────────
     | DELETE /api/manunuzi/{id}
     ───────────────────────────────────────────────────── */
    public function destroy(Request $request, $id)
    {
        $companyId = $this->companyId($request);
        if (!$companyId) return $this->err403();

        $m = Manunuzi::where('company_id', $companyId)
            ->where('status', 'final')
            ->find($id);
        if (!$m) return $this->err('Manunuzi haipatikani.', 404);

        return DB::transaction(function () use ($m) {
            $bidhaa = $m->bidhaa;
            if ($bidhaa) $bidhaa->decrement('idadi', $m->idadi);
            $m->delete();
            return response()->json([
                'success' => true,
                'message' => 'Manunuzi yamefutwa!',
            ]);
        });
    }

    /* ─────────────────────────────────────────────────────
     | DRAFTS
     ───────────────────────────────────────────────────── */

    public function listDrafts(Request $request)
    {
        $companyId = $this->companyId($request);
        if (!$companyId) return $this->err403();
        $userId = $request->user()->id;

        $drafts = Manunuzi::where('company_id', $companyId)
            ->where('draft_owner_id', $userId)
            ->where('status', 'draft')
            ->orderByDesc('draft_updated_at')
            ->get()
            ->groupBy('draft_token');

        $out = [];
        foreach ($drafts as $token => $items) {
            $first = $items->first();
            $out[] = [
                'draftToken' => $token,
                'saplaya'    => $first->saplaya,
                'simu'       => $first->simu,
                'mengineyo'  => $first->mengineyo,
                'itemCount'  => $items->count(),
                'updatedAt'  => $first->draft_updated_at?->toIso8601String(),
            ];
        }

        return response()->json(['success' => true, 'data' => $out]);
    }

    public function saveDraft(Request $request)
    {
        $companyId = $this->companyId($request);
        if (!$companyId) return $this->err403();
        $userId = $request->user()->id;

        $items      = $request->input('items', []);
        $draftToken = $request->input('draft_token');

        if (empty($items)) {
            return $this->err('Hakuna bidhaa za kuhifadhi.', 422);
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
                    $bidhaa = Bidhaa::where('id', $item['bidhaa_id'] ?? 0)
                        ->where('company_id', $companyId)
                        ->first();
                    if (!$bidhaa) continue;
                    $bidhaaId = $bidhaa->id;
                }

                $idadi     = (float) ($item['idadi'] ?? 0);
                $totalCost = (float) ($item['bei_nunua'] ?? 0);
                $unitCost  = $idadi > 0 ? $totalCost / $idadi : 0;

                Manunuzi::create([
                    'company_id'      => $companyId,
                    'bidhaa_id'       => $bidhaaId,
                    'new_bidhaa'      => $isNew ? $item['new_bidhaa'] : null,
                    'idadi'           => $idadi,
                    'bei'             => $totalCost,
                    'unit_cost'       => $unitCost,
                    'bei_kuuza_draft' => isset($item['bei_kuuza']) ? (float) $item['bei_kuuza'] : null,
                    'saplaya'         => $request->saplaya,
                    'simu'            => $request->simu,
                    'mengineyo'       => $request->mengineyo,
                    'hali_ya_malipo'  => 'imekamilika',
                    'kiasi_kilicholipwa' => $totalCost,
                    'deni_lililobaki' => 0,
                    'created_by'      => $userId,
                    'status'          => 'draft',
                    'draft_token'     => $draftToken,
                    'draft_owner_id'  => $userId,
                    'draft_updated_at'=> now(),
                ]);
                $saved++;
            }

            return response()->json([
                'success'    => true,
                'message'    => 'Draft imehifadhiwa!',
                'draftToken' => $draftToken,
                'count'      => $saved,
            ]);
        });
    }

    public function loadDraft(Request $request, $token)
    {
        $companyId = $this->companyId($request);
        if (!$companyId) return $this->err403();
        $userId = $request->user()->id;

        $draftItems = Manunuzi::with(['bidhaa:id,jina,aina,kipimo,bei_kuuza,bei_nunua'])
            ->where('company_id', $companyId)
            ->where('draft_owner_id', $userId)
            ->where('status', 'draft')
            ->where('draft_token', $token)
            ->get();

        if ($draftItems->isEmpty()) return $this->err('Draft haipatikani.', 404);

        $first = $draftItems->first();

        $items = $draftItems->map(function ($item) {
            $np = $item->new_bidhaa;
            $isNew = empty($item->bidhaa_id) && !empty($np);
            return [
                'id'         => (int) $item->id,
                'bidhaaId'   => $item->bidhaa_id,
                'isNew'      => $isNew,
                'newProduct' => $isNew ? $np : null,
                'name'       => $isNew ? ($np['jina'] ?? '') : ($item->bidhaa->jina ?? ''),
                'type'       => $isNew ? ($np['aina'] ?? '') : ($item->bidhaa->aina ?? ''),
                'unit'       => $isNew ? ($np['kipimo'] ?? '') : ($item->bidhaa->kipimo ?? ''),
                'quantity'   => (float) $item->idadi,
                'buyTotal'   => (float) $item->bei,
                'sellPrice'  => (float) ($item->bei_kuuza_draft ?? ($item->bidhaa->bei_kuuza ?? 0)),
            ];
        });

        return response()->json([
            'success'    => true,
            'draftToken' => $token,
            'saplaya'    => $first->saplaya,
            'simu'       => $first->simu,
            'mengineyo'  => $first->mengineyo,
            'items'      => $items,
            'updatedAt'  => $first->draft_updated_at?->toIso8601String(),
        ]);
    }

    public function deleteDraft(Request $request, $token)
    {
        $companyId = $this->companyId($request);
        if (!$companyId) return $this->err403();
        $userId = $request->user()->id;

        $deleted = Manunuzi::where('company_id', $companyId)
            ->where('draft_owner_id', $userId)
            ->where('status', 'draft')
            ->where('draft_token', $token)
            ->delete();

        if (!$deleted) return $this->err('Draft haipatikani.', 404);

        return response()->json(['success' => true, 'message' => 'Draft imefutwa!']);
    }

    public function finalizeDraft(Request $request, $token)
    {
        $companyId = $this->companyId($request);
        if (!$companyId) return $this->err403();
        $userId = $request->user()->id;

        $draftItems = Manunuzi::with('bidhaa')
            ->where('company_id', $companyId)
            ->where('draft_owner_id', $userId)
            ->where('status', 'draft')
            ->where('draft_token', $token)
            ->get();

        if ($draftItems->isEmpty()) return $this->err('Draft haipatikani.', 404);

        return DB::transaction(function () use ($draftItems, $companyId) {
            $batchId = 'RIS-' . date('Ymd') . '-' . strtoupper(Str::random(4));
            $first   = null;

            foreach ($draftItems as $item) {
                $bidhaa = $item->bidhaa;
                $beiKuuza = $item->bei_kuuza_draft;

                if (!$bidhaa && !empty($item->new_bidhaa)) {
                    $np = $item->new_bidhaa;
                    $bidhaa = Bidhaa::create([
                        'company_id'  => $companyId,
                        'jina'        => $np['jina'],
                        'aina'        => $np['aina'],
                        'kipimo'      => $np['kipimo'] ?? null,
                        'barcode'     => $np['barcode'] ?? null,
                        'idadi'       => 0,
                        'bei_nunua'   => $item->unit_cost,
                        'bei_kuuza'   => $beiKuuza ?? 0,
                        'bei_kiasi_cha_chaguo' => 'rejareja',
                        'expiry'      => $np['expiry'] ?? null,
                    ]);
                }
                if (!$bidhaa) continue;

                $item->update([
                    'bidhaa_id'       => $bidhaa->id,
                    'new_bidhaa'      => null,
                    'bei_kuuza_draft' => null,
                    'status'          => 'final',
                    'batch_id'        => $batchId,
                    'draft_token'     => null,
                    'draft_owner_id'  => null,
                    'draft_updated_at'=> null,
                    'created_at'      => now(),
                ]);

                if (!$first) $first = $item;

                $bidhaa->increment('idadi', $item->idadi);
                $bidhaa->bei_nunua = $item->unit_cost;
                if ($beiKuuza !== null) $bidhaa->bei_kuuza = $beiKuuza;
                $bidhaa->save();

                ActivityHelper::logPurchase($item, $bidhaa->jina, $item->bei);
            }

            return response()->json([
                'success'  => true,
                'message'  => 'Draft imekamilika!',
                'data'     => $first ? $this->payload($first->fresh(['bidhaa'])) : null,
                'batchId'  => $batchId,
            ]);
        });
    }

    /* ─────────────────────────────────────────────────────
     | Helpers
     ───────────────────────────────────────────────────── */

    private function storeBatch(Request $request, $companyId, $userId)
    {
        $items = $request->input('items', []);
        if (empty($items)) return $this->err('Hakuna bidhaa.', 422);

        foreach ($items as $i => $item) {
            $n     = $i + 1;
            $isNew = !empty($item['is_new']);
            $qty   = (float) ($item['idadi'] ?? 0);
            $total = (float) ($item['bei_nunua'] ?? 0);
            $sell  = (float) ($item['bei_kuuza'] ?? 0);

            if (!$isNew && empty($item['bidhaa_id']))
                return $this->err("Bidhaa #{$n}: bidhaa haijachaguliwa", 422);
            if ($isNew && empty($item['new_bidhaa']['jina']))
                return $this->err("Bidhaa #{$n}: jina la bidhaa mpya linakosekana", 422);
            if ($qty <= 0 || $total <= 0)
                return $this->err("Bidhaa #{$n}: idadi au bei si sahihi", 422);
            if ($sell < $total / $qty)
                return $this->err("Bidhaa #{$n}: bei ya kuuza ni chini ya bei ya kununua", 422);
        }

        return DB::transaction(function () use ($items, $request, $companyId, $userId) {
            $batchId = 'RIS-' . date('Ymd') . '-' . strtoupper(Str::random(4));
            $first   = null;

            foreach ($items as $item) {
                $isNew     = !empty($item['is_new']);
                $qty       = (float) $item['idadi'];
                $totalCost = (float) $item['bei_nunua'];
                $unitCost  = $qty > 0 ? $totalCost / $qty : 0;
                $sellPrice = (float) $item['bei_kuuza'];

                if ($isNew) {
                    $np = $item['new_bidhaa'];
                    $bidhaa = Bidhaa::where('company_id', $companyId)
                        ->where('jina', $np['jina'])
                        ->where('aina', $np['aina'])
                        ->first();
                    if (!$bidhaa) {
                        $bidhaa = Bidhaa::create([
                            'company_id' => $companyId,
                            'jina'       => $np['jina'],
                            'aina'       => $np['aina'],
                            'kipimo'     => $np['kipimo'] ?? null,
                            'barcode'    => $np['barcode'] ?? null,
                            'idadi'      => 0,
                            'bei_nunua'  => $unitCost,
                            'bei_kuuza'  => $sellPrice,
                            'bei_kiasi_cha_chaguo' => 'rejareja',
                            'expiry'     => $np['expiry'] ?? null,
                        ]);
                    }
                } else {
                    $bidhaa = Bidhaa::where('id', $item['bidhaa_id'])
                        ->where('company_id', $companyId)
                        ->firstOrFail();
                }

                $m = Manunuzi::create([
                    'company_id'         => $companyId,
                    'batch_id'           => $batchId,
                    'bidhaa_id'          => $bidhaa->id,
                    'idadi'              => $qty,
                    'bei'                => $totalCost,
                    'unit_cost'          => $unitCost,
                    'expiry'             => $item['new_bidhaa']['expiry'] ?? null,
                    'saplaya'            => $request->saplaya,
                    'simu'               => $request->simu,
                    'mengineyo'          => $request->mengineyo,
                    'hali_ya_malipo'     => 'imekamilika',
                    'kiasi_kilicholipwa' => $totalCost,
                    'deni_lililobaki'    => 0,
                    'created_by'         => $userId,
                    'status'             => 'final',
                ]);

                if (!$first) $first = $m;

                $bidhaa->increment('idadi', $qty);
                $bidhaa->bei_nunua = $unitCost;
                $bidhaa->bei_kuuza = $sellPrice;
                $bidhaa->save();

                ActivityHelper::logPurchase($m, $bidhaa->jina, $m->bei);
            }

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
                'data'    => $first ? $this->payload($first->fresh(['bidhaa'])) : null,
                'batchId' => $batchId,
            ]);
        });
    }

    private function payload(Manunuzi $m): array
    {
        return [
            'id'              => (int) $m->id,
            'batchId'         => $m->batch_id,
            'receiptNumber'   => $m->batch_id ?? $m->risiti_number,
            'productId'       => (int) $m->bidhaa_id,
            'productName'     => $m->bidhaa->jina ?? '',
            'productType'     => $m->bidhaa->aina ?? '',
            'productUnit'     => $m->bidhaa->kipimo ?? null,
            'quantity'        => (float) $m->idadi,
            'buyTotal'        => (float) $m->bei,
            'unitCost'        => (float) $m->unit_cost,
            'sellPrice'       => (float) ($m->bidhaa->bei_kuuza ?? 0),
            'expiry'          => $m->expiry,
            'supplier'        => $m->saplaya,
            'supplierPhone'   => $m->simu,
            'notes'           => $m->mengineyo,
            'createdBy'       => $m->created_by,
            'createdAt'       => $m->created_at?->toIso8601String(),
        ];
    }

    private function summary(int $companyId): array
    {
        $today = today();
        return [
            'todayPurchases' => (int) Manunuzi::where('company_id', $companyId)
                ->where('status', 'final')
                ->whereBetween('created_at', [$today->copy()->startOfDay(), $today->copy()->endOfDay()])
                ->count(),
            'totalItems' => (float) Manunuzi::where('company_id', $companyId)
                ->where('status', 'final')
                ->sum('idadi'),
            'totalCost' => (float) Manunuzi::where('company_id', $companyId)
                ->where('status', 'final')
                ->sum('bei'),
            'todayCost' => (float) Manunuzi::where('company_id', $companyId)
                ->where('status', 'final')
                ->whereBetween('created_at', [$today->copy()->startOfDay(), $today->copy()->endOfDay()])
                ->sum('bei'),
        ];
    }

    private function meta($paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'last_page'    => $paginator->lastPage(),
            'per_page'     => $paginator->perPage(),
            'total'        => $paginator->total(),
            'from'         => $paginator->firstItem(),
            'to'           => $paginator->lastItem(),
        ];
    }

    private function companyId(Request $request): ?int
    {
        return $request->user()?->company_id;
    }

    private function err403()
    {
        return response()->json([
            'success' => false,
            'message' => 'Akaunti yako haijaunganishwa na kampuni.',
        ], 403);
    }

    private function err(string $msg, int $code = 422)
    {
        return response()->json(['success' => false, 'message' => $msg], $code);
    }
}