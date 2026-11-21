<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ActivityHelper;
use App\Http\Controllers\Controller;
use App\Models\Bidhaa;
use App\Models\Madeni;
use App\Models\Mauzo;
use App\Models\Mteja;
use App\Models\Wafanyakazi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Read-only sales (Mauzo) endpoints for the mobile app.
 *
 * All queries are scoped to the authenticated token owner's company_id.
 * With no filters the endpoint returns today's sales, matching the web app's
 * default view. Explicit dates/search/range reveal historical records.
 */
class SalesApiController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $this->companyId($request);
        if (!$companyId) {
            return $this->noCompanyResponse();
        }

        $data = $request->validate([
            'search'     => 'nullable|string|max:100',
            'start_date' => 'nullable|date_format:Y-m-d',
            'end_date'   => 'nullable|date_format:Y-m-d',
            'payment'    => 'nullable|in:cash,lipa_namba,bank',
            'range'      => 'nullable|in:today,week,month,all',
            'per_page'   => 'nullable|integer|min:1|max:100',
            'page'       => 'nullable|integer|min:1',
        ]);

        $startDate = $data['start_date'] ?? null;
        $endDate   = $data['end_date'] ?? null;

        if ($startDate && $endDate && $endDate < $startDate) {
            throw ValidationException::withMessages([
                'end_date' => ['Tarehe ya mwisho haiwezi kuwa kabla ya tarehe ya kuanza.'],
            ]);
        }

        $perPage = (int) ($data['per_page'] ?? 20);
        $search  = trim((string) ($data['search'] ?? ''));
        $payment = $data['payment'] ?? null;
        $range   = $data['range'] ?? null;

        $query = Mauzo::query()->where('company_id', $companyId);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->whereHas('bidhaa', fn ($b) => $b->where('jina', 'like', "%{$search}%"))
                    ->orWhere('receipt_no', 'like', "%{$search}%")
                    ->orWhere('lipa_kwa', 'like', "%{$search}%");
            });
        }

        if ($payment) {
            $query->where('lipa_kwa', $payment);
        }

        $this->applyDateScope($query, $range, $startDate, $endDate, $search !== '');

        $filtered = (clone $query)->selectRaw(
            'COUNT(*) as count, COALESCE(SUM(jumla), 0) as total'
        )->first();

        $paginator = $query
            ->with('bidhaa:id,jina,aina,kipimo,bei_nunua')
            ->select([
                'id',
                'company_id',
                'receipt_no',
                'bidhaa_id',
                'idadi',
                'bei',
                'punguzo',
                'punguzo_aina',
                'jumla',
                'lipa_kwa',
                'lipa_kwa_type',
                'created_at',
                'sale_date',
            ])
            ->orderByDesc('created_at')
            ->paginate($perPage);

        $canSeeProfit = $this->canSeeProfit($request);

        $todayStart = Carbon::today()->startOfDay();
        $todayEnd = Carbon::today()->endOfDay();

        $todayAgg = Mauzo::where('company_id', $companyId)
            ->whereBetween('created_at', [$todayStart, $todayEnd])
            ->selectRaw('COUNT(*) as count, COALESCE(SUM(jumla), 0) as total')
            ->first();

        return response()->json([
            'success' => true,
            'data'    => collect($paginator->items())
                ->map(fn (Mauzo $sale) => $this->salePayload($sale, $canSeeProfit))
                ->values(),
            'summary' => [
                'today_total'      => round((float) ($todayAgg->total ?? 0), 2),
                'today_count'      => (int) ($todayAgg->count ?? 0),
                'filtered_total'   => round((float) ($filtered->total ?? 0), 2),
                'filtered_count'   => (int) ($filtered->count ?? 0),
                'payment_breakdown' => $this->paymentBreakdown($companyId, $todayStart, $todayEnd),
            ],
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
                'from'         => $paginator->firstItem(),
                'to'           => $paginator->lastItem(),
            ],
            'filters' => [
                'search'     => $search,
                'start_date' => $startDate,
                'end_date'   => $endDate,
                'payment'    => $payment,
                'range'      => $range,
            ],
        ]);
    }

    private function companyId(Request $request): ?int
    {
        $id = $request->user()?->company_id;
        return $id ? (int) $id : null;
    }

    private function noCompanyResponse()
    {
        return response()->json([
            'success' => false,
            'message' => 'Akaunti yako haijaunganishwa na kampuni.',
        ], 403);
    }

    private function canSeeProfit(Request $request): bool
    {
        $user = $request->user();

        if ($user instanceof Wafanyakazi) {
            return strtolower(trim((string) $user->uwezo)) === 'mkubwa';
        }

        return true;
    }

    private function applyDateScope(
        $query,
        ?string $range,
        ?string $startDate,
        ?string $endDate,
        bool $hasSearch
    ): void {
        if ($startDate || $endDate) {
            if ($startDate) {
                $query->whereDate('created_at', '>=', $startDate);
            }
            if ($endDate) {
                $query->whereDate('created_at', '<=', $endDate);
            }
            return;
        }

        if ($range === 'all') {
            return;
        }

        // A free-text search without an explicit range should look through
        // history instead of only today's rows.
        if ($hasSearch && $range === null) {
            return;
        }

        switch ($range ?? 'today') {
            case 'week':
                $query->whereBetween('created_at', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek(),
                ]);
                break;
            case 'month':
                $query->whereBetween('created_at', [
                    Carbon::now()->startOfMonth(),
                    Carbon::now()->endOfMonth(),
                ]);
                break;
            case 'today':
            default:
                $query->whereBetween('created_at', [
                    Carbon::today()->startOfDay(),
                    Carbon::today()->endOfDay(),
                ]);
                break;
        }
    }

    private function salePayload(Mauzo $sale, bool $canSeeProfit): array
    {
        $product = $sale->bidhaa;
        $quantity = (float) $sale->idadi;

        $discount = $sale->punguzo_aina === 'bidhaa'
            ? (float) $sale->punguzo * $quantity
            : (float) $sale->punguzo;

        $profit = null;
        if ($canSeeProfit && $product) {
            $profit = round(
                (((float) $sale->bei - (float) $product->bei_nunua) * $quantity) - $discount,
                2
            );
        }

        $created = $sale->created_at;

        return [
            'id'              => (int) $sale->id,
            'receipt_no'      => $sale->receipt_no,
            'bidhaa_id'       => (int) $sale->bidhaa_id,
            'jina'            => $product?->jina ?? 'Bidhaa imeondolewa',
            'aina'            => $product?->aina,
            'kipimo'          => $product?->kipimo,
            'idadi'           => $quantity,
            'bei'             => (float) $sale->bei,
            'punguzo'         => round($discount, 2),
            'punguzo_aina'    => $sale->punguzo_aina,
            'jumla'           => (float) $sale->jumla,
            'lipa_kwa'        => $sale->lipa_kwa ?: 'cash',
            'lipa_kwa_type'   => $sale->lipa_kwa_type,
            'lipa_kwa_name'   => $sale->lipa_kwa_name,
            'faida'           => $profit,
            'created_at'      => $created?->toIso8601String(),
            'sale_date'       => $sale->sale_date
                ? Carbon::parse($sale->sale_date)->toIso8601String()
                : $created?->toIso8601String(),
        ];
    }

    private function paymentBreakdown(int $companyId, Carbon $start, Carbon $end): array
    {
        $labels = [
            'cash'       => 'Cash',
            'lipa_namba' => 'Lipa Namba',
            'bank'       => 'Benki',
        ];

        $rows = Mauzo::where('company_id', $companyId)
            ->whereBetween('created_at', [$start, $end])
            ->select(
                'lipa_kwa',
                DB::raw('COUNT(*) as count'),
                DB::raw('COALESCE(SUM(jumla), 0) as total')
            )
            ->groupBy('lipa_kwa')
            ->get()
            ->keyBy(fn ($row) => $row->lipa_kwa ?: 'cash');

        return collect($labels)
            ->map(function (string $label, string $method) use ($rows) {
                $row = $rows->get($method);
                return [
                    'method' => $method,
                    'label'  => $label,
                    'count'  => (int) ($row->count ?? 0),
                    'total'  => round((float) ($row->total ?? 0), 2),
                ];
            })
            ->values()
            ->all();
    }

    /* ─────────────────────────────────────────────────────
     | POST /api/mauzo
     | Record a sale (or a credit sale when `kopesha` = 1).
     | Mirrors the web MauzoController rules without touching
     | any of the web routes or blade logic.
     ───────────────────────────────────────────────────── */
    public function store(Request $request)
    {
        $companyId = $this->companyId($request);
        if (!$companyId) {
            return $this->noCompanyResponse();
        }

        if ($request->boolean('kopesha')) {
            return $this->storeLoan($request);
        }

        $data = $request->validate([
            'bidhaa_id'     => 'required|integer|exists:bidhaas,id',
            'idadi'         => 'required|numeric|min:0.01',
            'bei'           => 'required|numeric|min:0',
            'punguzo'       => 'nullable|numeric|min:0',
            'punguzo_aina'  => 'nullable|in:bidhaa,jumla',
            'jumla'         => 'nullable|numeric',
            'lipa_kwa'      => 'nullable|in:cash,lipa_namba,bank',
            'lipa_kwa_type' => 'nullable|string|max:30',
            'mteja_id'      => 'nullable|integer|exists:mtejas,id',
            'bei_type'      => 'nullable|in:rejareja,jumla',
            'is_backdate'   => 'nullable|boolean',
            'backdate'      => 'nullable|date',
        ]);

        $bidhaa = Bidhaa::where('company_id', $companyId)
            ->find($data['bidhaa_id']);

        if (!$bidhaa) {
            return $this->jsonError('Bidhaa haipatikani.', 404);
        }

        if (!empty($data['mteja_id'])) {
            $ownsCustomer = Mteja::where('company_id', $companyId)
                ->whereKey($data['mteja_id'])
                ->exists();
            if (!$ownsCustomer) {
                return $this->jsonError('Mteja haipatikani kwenye kampuni hii.', 404);
            }
        }

        try {
            $pricing = $this->pricedSale($bidhaa, $data);
            [$method, $type] = $this->paymentType($data);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first(),
                'errors'  => $e->errors(),
            ], 422);
        }

        $saleDate = $this->saleDate($request);
        $actor = $this->actorColumns($request);

        return DB::transaction(function () use (
            $request,
            $companyId,
            $bidhaa,
            $data,
            $pricing,
            $method,
            $type,
            $saleDate,
            $actor
        ) {
            $bidhaa->decrement('idadi', $pricing['quantity']);

            $mauzo = new Mauzo();
            $mauzo->company_id     = $companyId;
            $mauzo->receipt_no     = $this->nextReceiptNo($companyId);
            $mauzo->bidhaa_id      = $bidhaa->id;
            $mauzo->idadi          = $pricing['quantity'];
            $mauzo->bei            = $pricing['price'];
            $mauzo->bei_type_used  = $pricing['price_type'];
            $mauzo->punguzo        = $pricing['discount'];
            $mauzo->punguzo_aina   = $pricing['discount_type'];
            $mauzo->jumla          = $pricing['total'];
            $mauzo->lipa_kwa       = $method;
            $mauzo->lipa_kwa_type  = $type;
            $mauzo->mteja_id       = $data['mteja_id'] ?? null;
            $mauzo->sale_date      = $saleDate;
            $mauzo->created_at     = $saleDate;
            $mauzo->updated_at     = $saleDate;
            $mauzo->user_id        = $actor['user_id'];
            $mauzo->mfanyakazi_id  = $actor['mfanyakazi_id'];
            $mauzo->save();

            try {
                ActivityHelper::logSale($mauzo, $bidhaa->jina, $mauzo->jumla);
            } catch (\Throwable $e) {
                \Log::warning('Sale activity log failed: ' . $e->getMessage());
            }

            $mauzo->load('bidhaa:id,jina,aina,kipimo,bei_nunua');

            return response()->json([
                'success'    => true,
                'message'    => 'Mauzo yamerekodiwa kikamilifu!',
                'notification' => 'Mauzo yamefanikiwa!',
                'receipt_no' => $mauzo->receipt_no,
                'data'       => $this->salePayload(
                    $mauzo,
                    $this->canSeeProfit($request)
                ),
            ]);
        });
    }

    /* ─────────────────────────────────────────────────────
     | POST /api/mauzo/kopesha
     | Credit sale — creates a debt (Madeni) exactly like web.
     ───────────────────────────────────────────────────── */
    public function storeLoan(Request $request)
    {
        $companyId = $this->companyId($request);
        if (!$companyId) {
            return $this->noCompanyResponse();
        }

        $data = $request->validate([
            'bidhaa_id'     => 'required|integer|exists:bidhaas,id',
            'idadi'         => 'required|numeric|min:0.01',
            'bei'           => 'required|numeric|min:0',
            'punguzo'       => 'nullable|numeric|min:0',
            'punguzo_aina'  => 'nullable|in:bidhaa,jumla',
            'jumla'         => 'nullable|numeric',
            'jina_mkopaji'  => 'required|string|max:255',
            'simu'          => 'required|string|max:20',
            'tarehe_malipo' => 'nullable|date',
            'mteja_id'      => 'nullable|integer|exists:mtejas,id',
            'bei_type'      => 'nullable|in:rejareja,jumla',
            'is_backdate'   => 'nullable|boolean',
            'backdate'      => 'nullable|date',
        ]);

        $bidhaa = Bidhaa::where('company_id', $companyId)
            ->find($data['bidhaa_id']);

        if (!$bidhaa) {
            return $this->jsonError('Bidhaa haipatikani.', 404);
        }

        try {
            $pricing = $this->pricedSale($bidhaa, $data);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first(),
                'errors'  => $e->errors(),
            ], 422);
        }

        $saleDate = $this->saleDate($request);
        $dueDate  = $request->filled('tarehe_malipo')
            ? $request->input('tarehe_malipo')
            : now()->addDays(7)->toDateString();
        $actor = $this->actorColumns($request);

        return DB::transaction(function () use (
            $companyId,
            $bidhaa,
            $data,
            $pricing,
            $saleDate,
            $dueDate,
            $actor
        ) {
            $bidhaa->decrement('idadi', $pricing['quantity']);

            $mtejaId = $data['mteja_id'] ?? null;
            if (!$mtejaId) {
                // Match the web `firstOrCreate` lookup so the same customer
                // record is reused between the website and the mobile app.
                $mteja = Mteja::firstOrCreate(
                    [
                        'company_id' => $companyId,
                        'jina'       => $data['jina_mkopaji'],
                        'simu'       => $data['simu'],
                    ]
                );
                $mtejaId = $mteja->id;
            }

            $deni = Madeni::create([
                'company_id'    => $companyId,
                'bidhaa_id'     => $bidhaa->id,
                'mteja_id'      => $mtejaId,
                'idadi'         => $pricing['quantity'],
                'bei'           => $pricing['price'],
                'jumla'         => $pricing['total'],
                'punguzo'       => $pricing['discount'],
                'punguzo_aina'  => $pricing['discount_type'],
                'baki'          => $pricing['total'],
                'jina_mkopaji'  => $data['jina_mkopaji'],
                'simu'          => $data['simu'],
                'tarehe_malipo' => $dueDate,
                'user_id'       => $actor['user_id'],
                'mfanyakazi_id' => $actor['mfanyakazi_id'],
            ]);

            if (!$saleDate->isSameSecond(now())) {
                $deni->created_at = $saleDate;
                $deni->updated_at = $saleDate;
                $deni->save();
            }

            return response()->json([
                'success'    => true,
                'message'    => 'Deni limerekodiwa kikamilifu!',
                'notification' => 'Deni limefanikiwa!',
                'data'       => $deni,
            ]);
        });
    }

    private function pricedSale(Bidhaa $bidhaa, array $data): array
    {
        $quantity = (float) $data['idadi'];

        if ($bidhaa->expiry &&
            Carbon::parse($bidhaa->expiry)->startOfDay()->lt(Carbon::today())) {
            throw ValidationException::withMessages([
                'bidhaa_id' => ['Bidhaa hii ime-expire.'],
            ]);
        }

        if ($quantity > (float) $bidhaa->idadi) {
            throw ValidationException::withMessages([
                'idadi' => ["Stock haijatosha, baki ni {$bidhaa->idadi}."],
            ]);
        }

        $priceType = $data['bei_type'] ?? 'rejareja';
        $price     = (float) $data['bei'];
        $retail    = (float) $bidhaa->bei_kuuza;
        $wholesale = (float) ($bidhaa->bei_uzo_jumla ?? 0);

        if ($priceType === 'jumla' && $wholesale > 0) {
            if (abs($price - $wholesale) > 0.01) {
                $price = $wholesale;
            }
        } else {
            $priceType = 'rejareja';
            if ($retail > 0 && abs($price - $retail) > 0.01) {
                $price = $retail;
            }
        }

        $base         = $price * $quantity;
        $discount     = (float) ($data['punguzo'] ?? 0);
        $discountType = $data['punguzo_aina'] ?? 'bidhaa';
        $actual       = $discountType === 'bidhaa'
            ? $discount * $quantity
            : $discount;
        $profit       = ($price - (float) $bidhaa->bei_nunua) * $quantity;

        if ($discountType === 'jumla') {
            if ($discount > $base) {
                throw ValidationException::withMessages([
                    'punguzo' => [
                        'Punguzo haliruhusiwi kuzidi jumla ya ' .
                        number_format($base, 2) . ' Tsh',
                    ],
                ]);
            }
            if ($discount > $profit) {
                throw ValidationException::withMessages([
                    'punguzo' => [
                        'Punguzo la jumla haliruhusiwi kuzidi faida ya ' .
                        number_format($profit, 2) . ' Tsh',
                    ],
                ]);
            }
        } else {
            $maxPerItem = $price - (float) $bidhaa->bei_nunua;
            if ($discount > $maxPerItem) {
                throw ValidationException::withMessages([
                    'punguzo' => [
                        'Punguzo haliruhusiwi kuzidi faida ya ' .
                        number_format($maxPerItem, 2) .
                        ' Tsh kwa kila bidhaa',
                    ],
                ]);
            }
        }

        $total = $base - $actual;

        if ($total < -0.01) {
            throw ValidationException::withMessages([
                'jumla' => ['Jumla haiwezi kuwa chini ya sifuri.'],
            ]);
        }

        if (array_key_exists('jumla', $data) &&
            $data['jumla'] !== null &&
            abs($total - (float) $data['jumla']) > 0.01) {
            throw ValidationException::withMessages([
                'jumla' => [
                    'Jumla iliyoingizwa si sahihi. Jumla sahihi ni ' .
                    number_format($total, 2),
                ],
            ]);
        }

        return [
            'quantity'      => $quantity,
            'price'         => $price,
            'price_type'    => $priceType,
            'discount'      => $discount,
            'discount_type' => $discountType,
            'total'         => round($total, 2),
        ];
    }

    private function paymentType(array $data): array
    {
        $method = $data['lipa_kwa'] ?? 'cash';
        $type   = $data['lipa_kwa_type'] ?? null;

        if ($method === 'lipa_namba') {
            $valid = ['mpesa', 'mixx_by_yas', 'airtel_money', 'halopesa', 'other'];
            if (!in_array($type, $valid, true)) {
                throw ValidationException::withMessages([
                    'lipa_kwa_type' => ['Chagua aina sahihi ya Lipa Namba.'],
                ]);
            }
        }

        if ($method === 'bank') {
            $valid = ['crdb', 'nmb', 'nbc', 'other'];
            if (!in_array($type, $valid, true)) {
                throw ValidationException::withMessages([
                    'lipa_kwa_type' => ['Chagua aina sahihi ya Benki.'],
                ]);
            }
        }

        if ($method === 'cash') {
            $type = null;
        }

        return [$method, $type];
    }

    private function saleDate(Request $request): Carbon
    {
        $user = $request->user();
        $canBackdate = true;

        if ($user instanceof Wafanyakazi) {
            $canBackdate =
                strtolower(trim((string) $user->uwezo)) === 'mkubwa';
        }

        if ($canBackdate &&
            $request->boolean('is_backdate') &&
            $request->filled('backdate')) {
            $timestamp = strtotime((string) $request->input('backdate'));
            if ($timestamp !== false && $timestamp <= time()) {
                return Carbon::parse($request->input('backdate'));
            }
        }

        return now();
    }

    private function actorColumns(Request $request): array
    {
        $user = $request->user();
        $isEmployee = $user instanceof Wafanyakazi;

        return [
            'user_id'       => $isEmployee ? null : $user?->id,
            'mfanyakazi_id' => $isEmployee ? $user?->id : null,
        ];
    }

    private function nextReceiptNo(int $companyId): string
    {
        $date   = Carbon::now()->format('Ymd');
        $prefix = "MS-{$date}-";

        $last = Mauzo::where('company_id', $companyId)
            ->where('receipt_no', 'like', $prefix . '%')
            ->orderByDesc('receipt_no')
            ->first();

        $next = $last
            ? intval(substr($last->receipt_no, strlen($prefix))) + 1
            : 1;

        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    private function jsonError(string $message, int $status = 422)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $status);
    }
}
