<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Matumizi;
use App\Models\AinaZaMatumizi;
use App\Models\Mauzo;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class ExpensesApiController extends Controller
{
    // ─────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────

    private function companyId(Request $request)
    {
        return $request->user()?->company_id;
    }

    /**
     * Get summary totals for this company (matches web controller logic).
     */
    private function summaryFor(int $companyId): array
    {
        $totalExpenses = (float) Matumizi::where('company_id', $companyId)
            ->sum('gharama');

        $todayExpenses = (float) Matumizi::where('company_id', $companyId)
            ->whereDate('created_at', today())
            ->sum('gharama');

        $monthExpenses = (float) Matumizi::where('company_id', $companyId)
            ->whereBetween('created_at', [
                now()->startOfMonth(),
                now()->endOfMonth(),
            ])
            ->sum('gharama');

        $expensesCount = (int) Matumizi::where('company_id', $companyId)->count();
        $average = $expensesCount > 0 ? $totalExpenses / $expensesCount : 0;

        $totalMapato = (float) Mauzo::where('company_id', $companyId)->sum('jumla');
        $remaining = $totalMapato - $totalExpenses;

        return [
            'totalExpenses'    => $totalExpenses,
            'todayExpenses'    => $todayExpenses,
            'monthExpenses'    => $monthExpenses,
            'expensesCount'    => $expensesCount,
            'averageExpense'   => round($average, 2),
            'totalMapato'      => $totalMapato,
            'remainingBalance' => $remaining,
        ];
    }

    /**
     * Shape one expense for the mobile client.
     */
    private function expensePayload(Matumizi $m): array
    {
        return [
            'id'        => (int) $m->id,
            'type'      => $m->aina,
            'notes'     => $m->maelezo,
            'amount'    => (float) $m->gharama,
            'createdAt' => $m->created_at?->toIso8601String(),
            'updatedAt' => $m->updated_at?->toIso8601String(),
        ];
    }

    private function typePayload(AinaZaMatumizi $t): array
    {
        return [
            'id'        => (int) $t->id,
            'name'      => $t->jina,
            'notes'     => $t->maelezo,
            'color'     => $t->rangi,
            'category'  => $t->kategoria,
            'usageCount' => (int) ($t->matumizi_count ?? 0),
            'createdAt' => $t->created_at?->toIso8601String(),
        ];
    }

    // ─────────────────────────────────────────────────────────
    // GET /api/matumizi
    // ─────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $companyId = $this->companyId($request);
        if (!$companyId) {
            return response()->json([
                'success' => false,
                'message' => 'Akaunti yako haijaunganishwa na kampuni.',
            ], 403);
        }

        $search  = trim((string) $request->input('search', ''));
        $filter  = $request->input('type');          // single expense type name
        $perPage = (int) ($request->input('per_page', 20));
        $perPage = max(1, min($perPage, 100));
        $page    = (int) $request->input('page', 1);

        $query = Matumizi::query()->where('company_id', $companyId);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('aina', 'like', "%{$search}%")
                    ->orWhere('maelezo', 'like', "%{$search}%");
            });
        }

        if ($filter) {
            $query->where('aina', $filter);
        }

        // Summary computed from an unfiltered clone (total, today, month, avg)
        $summaryQuery = Matumizi::query()->where('company_id', $companyId);
        $summary = $this->summaryFor($companyId);

        $paginator = $query
            ->latest()
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'data' => collect($paginator->items())
                ->map(fn ($m) => $this->expensePayload($m))
                ->values(),
            'summary' => $summary,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
                'from'         => $paginator->firstItem(),
                'to'           => $paginator->lastItem(),
            ],
            'filters' => [
                'search' => $search,
                'type'   => $filter,
            ],
        ]);
    }

    // ─────────────────────────────────────────────────────────
    // POST /api/matumizi
    // ─────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $companyId = $this->companyId($request);
        if (!$companyId) {
            return response()->json([
                'success' => false,
                'message' => 'Akaunti yako haijaunganishwa na kampuni.',
            ], 403);
        }

        $data = $request->validate([
            'gharama'  => 'required|numeric|min:0',
            'maelezo'  => 'nullable|string|max:500',
            'aina'     => 'nullable|string|max:255',
            'aina_mpya' => 'nullable|string|max:255',
            'tarehe'   => 'nullable|date',
        ]);

        // Resolve type: new custom type OR existing
        $typeName = !empty($data['aina_mpya'])
            ? $data['aina_mpya']
            : ($data['aina'] ?? 'Mengineyo');

        // Auto-register a new expense type if needed
        if (!empty($data['aina_mpya'])) {
            $exists = AinaZaMatumizi::where('company_id', $companyId)
                ->where('jina', $data['aina_mpya'])
                ->exists();
            if (!$exists) {
                AinaZaMatumizi::create([
                    'jina'       => $data['aina_mpya'],
                    'maelezo'    => 'Auto-generated from expense entry',
                    'rangi'      => 'bg-gray-100 text-gray-800 border border-gray-200',
                    'kategoria'  => 'mengineyo',
                    'company_id' => $companyId,
                ]);
            }
        }

        $expenseDate = !empty($data['tarehe'])
            ? Carbon::parse($data['tarehe'])
            : now();

        $expense = Matumizi::create([
            'company_id' => $companyId,
            'aina'       => $typeName,
            'maelezo'    => $data['maelezo'] ?? null,
            'gharama'    => $data['gharama'],
            'created_at' => $expenseDate,
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => '✅ Matumizi yamehifadhiwa!',
            'data'    => $this->expensePayload($expense),
            'summary' => $this->summaryFor($companyId),
        ], 201);
    }

    // ─────────────────────────────────────────────────────────
    // PUT /api/matumizi/{id}
    // ─────────────────────────────────────────────────────────

    public function update(Request $request, $id)
    {
        $companyId = $this->companyId($request);
        if (!$companyId) {
            return response()->json([
                'success' => false,
                'message' => 'Akaunti yako haijaunganishwa na kampuni.',
            ], 403);
        }

        $expense = Matumizi::find($id);
        if (!$expense) {
            return response()->json([
                'success' => false,
                'message' => 'Matumizi hayo hayapatikani.',
            ], 404);
        }
        if ((int) $expense->company_id !== (int) $companyId) {
            return response()->json([
                'success' => false,
                'message' => 'Huna ruhusa ya kubadilisha matumizi haya.',
            ], 403);
        }

        $data = $request->validate([
            'aina'    => 'required|string|max:255',
            'maelezo' => 'nullable|string|max:500',
            'gharama' => 'required|numeric|min:0',
        ]);

        $expense->update([
            'aina'    => $data['aina'],
            'maelezo' => $data['maelezo'] ?? null,
            'gharama' => $data['gharama'],
        ]);

        return response()->json([
            'success' => true,
            'message' => '✅ Matumizi yamerekebishwa!',
            'data'    => $this->expensePayload($expense->fresh()),
            'summary' => $this->summaryFor($companyId),
        ]);
    }

    // ─────────────────────────────────────────────────────────
    // DELETE /api/matumizi/{id}
    // ─────────────────────────────────────────────────────────

    public function destroy(Request $request, $id)
    {
        $companyId = $this->companyId($request);
        if (!$companyId) {
            return response()->json([
                'success' => false,
                'message' => 'Akaunti yako haijaunganishwa na kampuni.',
            ], 403);
        }

        $expense = Matumizi::find($id);
        if (!$expense) {
            return response()->json([
                'success' => false,
                'message' => 'Matumizi hayo hayapatikani.',
            ], 404);
        }
        if ((int) $expense->company_id !== (int) $companyId) {
            return response()->json([
                'success' => false,
                'message' => 'Huna ruhusa ya kufuta matumizi haya.',
            ], 403);
        }

        $expense->delete();

        return response()->json([
            'success' => true,
            'message' => '✅ Matumizi yamefutwa!',
            'summary' => $this->summaryFor($companyId),
        ]);
    }

    // ─────────────────────────────────────────────────────────
    // GET /api/matumizi/aina      — list expense types
    // POST /api/matumizi/aina     — create new expense type
    // ─────────────────────────────────────────────────────────

    public function listTypes(Request $request)
    {
        $companyId = $this->companyId($request);
        if (!$companyId) {
            return response()->json([
                'success' => false,
                'message' => 'Akaunti yako haijaunganishwa na kampuni.',
            ], 403);
        }

        $types = AinaZaMatumizi::where('company_id', $companyId)
            ->withCount('matumizi')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $types->map(fn ($t) => $this->typePayload($t))->values(),
        ]);
    }

    public function storeType(Request $request)
    {
        $companyId = $this->companyId($request);
        if (!$companyId) {
            return response()->json([
                'success' => false,
                'message' => 'Akaunti yako haijaunganishwa na kampuni.',
            ], 403);
        }

        $data = $request->validate([
            'jina' => [
                'required',
                'string',
                'max:255',
                Rule::unique('aina_za_matumizi')->where(
                    fn ($q) => $q->where('company_id', $companyId)
                ),
            ],
            'maelezo'  => 'nullable|string',
            'rangi'    => 'nullable|string',
            'kategoria' => 'nullable|string',
        ]);

        $type = AinaZaMatumizi::create([
            'jina'       => $data['jina'],
            'maelezo'    => $data['maelezo'] ?? null,
            'rangi'      => $data['rangi']
                ?? 'bg-green-100 text-green-800 border border-green-200',
            'kategoria'  => $data['kategoria'] ?? 'mengineyo',
            'company_id' => $companyId,
        ]);

        return response()->json([
            'success' => true,
            'message' => '✅ Aina mpya ya matumizi imesajiliwa!',
            'data'    => $this->typePayload($type),
        ], 201);
    }

    // ─────────────────────────────────────────────────────────
    // DELETE /api/matumizi/aina/{id}
    // ─────────────────────────────────────────────────────────

    public function destroyType(Request $request, $id)
    {
        $companyId = $this->companyId($request);
        if (!$companyId) {
            return response()->json([
                'success' => false,
                'message' => 'Akaunti yako haijaunganishwa na kampuni.',
            ], 403);
        }

        $type = AinaZaMatumizi::find($id);
        if (!$type) {
            return response()->json([
                'success' => false,
                'message' => 'Aina hiyo haipatikani.',
            ], 404);
        }
        if ((int) $type->company_id !== (int) $companyId) {
            return response()->json([
                'success' => false,
                'message' => 'Huna ruhusa ya kufuta aina hii.',
            ], 403);
        }

        $inUse = Matumizi::where('company_id', $companyId)
            ->where('aina', $type->jina)
            ->count();

        if ($inUse > 0) {
            return response()->json([
                'success' => false,
                'message' => "❌ Huwezi kufuta aina hii! Inatumika kwenye matumizi {$inUse}.",
            ], 422);
        }

        $type->delete();

        return response()->json([
            'success' => true,
            'message' => '✅ Aina ya matumizi imefutwa!',
        ]);
    }

    // ─────────────────────────────────────────────────────────
    // GET /api/matumizi/report
    // Used by the mobile Reports screen / chart views.
    // ─────────────────────────────────────────────────────────

    public function report(Request $request)
    {
        $companyId = $this->companyId($request);
        if (!$companyId) {
            return response()->json([
                'success' => false,
                'message' => 'Akaunti yako haijaunganishwa na kampuni.',
            ], 403);
        }

        $startDate = $request->input('start_date')
            ?? now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->input('end_date')
            ?? now()->format('Y-m-d');

        $expenses = Matumizi::where('company_id', $companyId)
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->orderBy('created_at', 'desc')
            ->get();

        $total   = (float) $expenses->sum('gharama');
        $count   = $expenses->count();
        $average = $count > 0 ? $total / $count : 0;
        $max     = (float) ($expenses->max('gharama') ?? 0);

        // Group by date
        $daily = [];
        foreach ($expenses as $e) {
            $d = Carbon::parse($e->created_at)->format('Y-m-d');
            $daily[$d] = ($daily[$d] ?? 0) + (float) $e->gharama;
        }

        // Group by type
        $byType = [];
        foreach ($expenses as $e) {
            $byType[$e->aina] = ($byType[$e->aina] ?? 0) + (float) $e->gharama;
        }
        arsort($byType);

        return response()->json([
            'success' => true,
            'data' => [
                'expenses' => $expenses->map(fn ($e) => $this->expensePayload($e))->values(),
                'total'    => $total,
                'count'    => $count,
                'average'  => round($average, 2),
                'max'      => $max,
                'dailyData'    => $daily,
                'categoryData' => $byType,
                'range' => [
                    'start_date' => $startDate,
                    'end_date'   => $endDate,
                ],
            ],
        ]);
    }
}