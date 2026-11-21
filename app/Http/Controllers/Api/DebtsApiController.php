<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bidhaa;
use App\Models\Madeni;
use App\Models\Marejesho;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DebtsApiController extends Controller
{
    /* ─────────────────────────────────────────────────────
     | GET /api/madeni
     | Debt list (paginated) + summary + borrowers.
     ───────────────────────────────────────────────────── */
    public function index(Request $request)
    {
        $companyId = $this->companyId($request);
        if (!$companyId) return $this->err403();

        $perPage = max(1, (int) $request->input('per_page', 20));
        $search  = trim((string) $request->input('search', ''));
        $filter  = $request->input('filter'); // all | active | paid

        $query = Madeni::with([
                'bidhaa:id,jina,aina,kipimo,bei_kuuza',
            ])
            ->where('company_id', $companyId);

        if ($filter === 'active') {
            $query->where('baki', '>', 0);
        } elseif ($filter === 'paid') {
            $query->where('baki', '<=', 0);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('jina_mkopaji', 'like', "%{$search}%")
                  ->orWhere('simu', 'like', "%{$search}%")
                  ->orWhereHas('bidhaa', fn ($b) =>
                      $b->where('jina', 'like', "%{$search}%")
                        ->orWhere('aina', 'like', "%{$search}%"));
            });
        }

        $paginator = $query->orderByDesc('created_at')->paginate($perPage);

        return response()->json([
            'success'   => true,
            'data'      => collect($paginator->items())
                ->map(fn ($d) => $this->payload($d))
                ->values(),
            'summary'   => $this->summary($companyId),
            'borrowers' => $this->borrowers($companyId),
            'meta'      => $this->meta($paginator),
        ]);
    }

    /* ─────────────────────────────────────────────────────
     | GET /api/madeni/{id}
     ───────────────────────────────────────────────────── */
    public function show(Request $request, $id)
    {
        $companyId = $this->companyId($request);
        if (!$companyId) return $this->err403();

        $d = Madeni::with(['bidhaa:id,jina,aina,kipimo,bei_kuuza'])
            ->where('company_id', $companyId)
            ->find($id);
        if (!$d) return $this->err('Deni haipatikani.', 404);

        $repayments = Marejesho::where('company_id', $companyId)
            ->where('madeni_id', $id)
            ->orderByDesc('tarehe')
            ->get()
            ->map(fn ($r) => $this->repaymentPayload($r));

        return response()->json([
            'success'    => true,
            'data'       => $this->payload($d),
            'repayments' => $repayments,
        ]);
    }

    /* ─────────────────────────────────────────────────────
     | POST /api/madeni/{id}/repay
     | Record a repayment.
     ───────────────────────────────────────────────────── */
    public function repay(Request $request, $id)
    {
        $companyId = $this->companyId($request);
        if (!$companyId) return $this->err403();
        $userId = $request->user()->id;

        $d = Madeni::where('company_id', $companyId)->find($id);
        if (!$d) return $this->err('Deni haipatikani.', 404);
        if ((float) $d->baki <= 0) return $this->err('Deni hili tayari limelipwa.', 422);

        $v = validator($request->all(), [
            'amount'    => 'required|numeric|min:0.01',
            'method'    => 'required|in:cash,lipa_namba,bank',
            'method_type' => 'nullable|string|max:30',
            'date'      => 'nullable|date',
        ]);
        if ($v->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $v->errors(),
                'message' => $v->errors()->first(),
            ], 422);
        }

        if ((float) $request->amount > (float) $d->baki) {
            return $this->err('Kiasi kimezidi baki lililobaki.', 422);
        }

        return DB::transaction(function () use ($request, $d, $companyId, $userId) {
            $repayment = Marejesho::create([
                'company_id'   => $companyId,
                'madeni_id'    => $d->id,
                'kiasi'        => $request->amount,
                'tarehe'       => $request->date ?? now(),
                'lipa_kwa'     => $request->method,
                'lipa_kwa_type'=> $request->method_type,
                'created_by'   => $userId,
            ]);

            $d->baki = max(0, (float) $d->baki - (float) $request->amount);
            $d->save();

            return response()->json([
                'success' => true,
                'message' => 'Malipo yamehifadhiwa!',
                'data'    => $this->repaymentPayload($repayment),
                'debt'    => $this->payload($d->fresh(['bidhaa'])),
            ]);
        });
    }

    /* ─────────────────────────────────────────────────────
     | GET /api/madeni/repayments
     | Recent repayments list (paginated).
     ───────────────────────────────────────────────────── */
    public function repayments(Request $request)
    {
        $companyId = $this->companyId($request);
        if (!$companyId) return $this->err403();

        $perPage = max(1, (int) $request->input('per_page', 20));

        $paginator = Marejesho::with([
                'madeni:id,jina_mkopaji,simu,bidhaa_id,idadi,jumla,baki',
                'madeni.bidhaa:id,jina,aina,kipimo',
            ])
            ->where('company_id', $companyId)
            ->orderByDesc('tarehe')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => collect($paginator->items())
                ->map(fn ($r) => $this->repaymentPayload($r, includeDebt: true))
                ->values(),
            'meta'    => $this->meta($paginator),
            'total'   => (float) Marejesho::where('company_id', $companyId)->sum('kiasi'),
        ]);
    }

    /* ─────────────────────────────────────────────────────
     | GET /api/madeni/borrowers/{name}/debts
     ───────────────────────────────────────────────────── */
    public function borrowerDebts(Request $request, $name)
    {
        $companyId = $this->companyId($request);
        if (!$companyId) return $this->err403();

        $debts = Madeni::with('bidhaa:id,jina,aina,kipimo')
            ->where('company_id', $companyId)
            ->where('jina_mkopaji', $name)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($d) => $this->payload($d));

        return response()->json([
            'success'       => true,
            'borrower'      => $name,
            'totalDebts'    => $debts->count(),
            'totalBalance'  => (float) $debts->sum(fn ($d) => $d['balance']),
            'debts'         => $debts,
        ]);
    }

    /* ─────────────────────────────────────────────────────
     | Helpers
     ───────────────────────────────────────────────────── */
    private function payload(Madeni $d): array
    {
        return [
            'id'          => (int) $d->id,
            'borrower'    => $d->jina_mkopaji,
            'phone'       => $d->simu,
            'productId'   => (int) $d->bidhaa_id,
            'productName' => $d->bidhaa->jina ?? '',
            'productType' => $d->bidhaa->aina ?? '',
            'productUnit' => $d->bidhaa->kipimo ?? null,
            'quantity'    => (float) $d->idadi,
            'price'       => (float) $d->bei,
            'total'       => (float) $d->jumla,
            'discount'    => (float) ($d->punguzo ?? 0),
            'discountType'=> $d->punguzo_aina,
            'paid'        => (float) $d->jumla - (float) $d->baki,
            'balance'     => (float) $d->baki,
            'status'      => (float) $d->baki <= 0 ? 'paid' : 'active',
            'dueDate'     => $d->tarehe_malipo,
            'createdAt'   => $d->created_at?->toIso8601String(),
        ];
    }

    private function repaymentPayload(Marejesho $r, bool $includeDebt = false): array
    {
        $base = [
            'id'         => (int) $r->id,
            'debtId'     => (int) $r->madeni_id,
            'amount'     => (float) $r->kiasi,
            'date'       => $r->tarehe?->toIso8601String(),
            'method'     => $r->lipa_kwa,
            'methodType' => $r->lipa_kwa_type,
            'createdAt'  => $r->created_at?->toIso8601String(),
        ];
        if ($includeDebt && $r->madeni) {
            $base['borrower']    = $r->madeni->jina_mkopaji;
            $base['phone']       = $r->madeni->simu;
            $base['productName'] = $r->madeni->bidhaa->jina ?? '';
            $base['productType'] = $r->madeni->bidhaa->aina ?? '';
            $base['productUnit'] = $r->madeni->bidhaa->kipimo ?? null;
            $base['quantity']    = (float) $r->madeni->idadi;
            $base['total']       = (float) $r->madeni->jumla;
            $base['balance']     = (float) $r->madeni->baki;
        }
        return $base;
    }

    private function summary(int $companyId): array
    {
        $today = Carbon::today();

        return [
            'todayDebts'    => (float) Madeni::where('company_id', $companyId)
                ->whereBetween('created_at', [$today->copy()->startOfDay(), $today->copy()->endOfDay()])
                ->sum('jumla'),
            'todayRepayments' => (float) Marejesho::where('company_id', $companyId)
                ->whereBetween('tarehe', [$today->copy()->startOfDay(), $today->copy()->endOfDay()])
                ->sum('kiasi'),
            'todayNewBorrowers' => (int) Madeni::where('company_id', $companyId)
                ->whereBetween('created_at', [$today->copy()->startOfDay(), $today->copy()->endOfDay()])
                ->distinct('jina_mkopaji')
                ->count('jina_mkopaji'),
            'todayPending'  => (int) Madeni::where('company_id', $companyId)
                ->whereBetween('created_at', [$today->copy()->startOfDay(), $today->copy()->endOfDay()])
                ->where('baki', '>', 0)
                ->count(),
        ];
    }

    private function borrowers(int $companyId): array
    {
        return Madeni::where('company_id', $companyId)
            ->select(
                'jina_mkopaji',
                'simu',
                DB::raw('COUNT(*) as total_debts'),
                DB::raw('SUM(baki) as total_balance')
            )
            ->where('baki', '>', 0)
            ->groupBy('jina_mkopaji', 'simu')
            ->orderByDesc('total_balance')
            ->get()
            ->map(fn ($b) => [
                'borrower'    => $b->jina_mkopaji,
                'phone'       => $b->simu,
                'totalDebts'  => (int) $b->total_debts,
                'totalBalance'=> (float) $b->total_balance,
            ])
            ->toArray();
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