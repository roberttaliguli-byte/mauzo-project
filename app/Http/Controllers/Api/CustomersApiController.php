<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mteja;
use App\Models\Order;
use Illuminate\Http\Request;

class CustomersApiController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $request->user()?->company_id;
        if (!$companyId) {
            return response()->json([
                'success' => false,
                'message' => 'Akaunti yako haijaunganishwa na kampuni.',
            ], 403);
        }

        $search  = trim($request->input('search', ''));
        $perPage = (int) ($request->input('per_page', 20));

        // Build the search filter once, reuse for both count & pagination
        $base = Mteja::query()->where('company_id', $companyId);

        if ($search !== '') {
            $base->where(function ($q) use ($search) {
                $q->where('jina', 'like', "%{$search}%")
                  ->orWhere('simu', 'like', "%{$search}%")
                  ->orWhere('customer_code', 'like', "%{$search}%");
            });
        }

        // Summary from a clean clone (before we touch select/order)
        $summary = $this->summaryFor(clone $base);

        // Paginate the filtered, selected, ordered set
        $paginator = $base
            ->select([
                'id',
                'jina',
                'simu',
                'customer_code',
                'company_id',
                'created_at',
                'updated_at',
            ])
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => collect($paginator->items())
                ->map(fn (Mteja $customer) => $this->customerPayload($customer))
                ->values(),
            'summary' => $summary,
            'meta'    => $this->meta($paginator),
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    private function customerPayload(Mteja $customer)
    {
        return [
            'id'           => (int) $customer->id,
            'name'         => $customer->jina,
            'phone'        => $customer->simu,
            'email'        => $customer->barua_pepe,
            'address'      => $customer->anapoishi,
            'customerCode' => $customer->customer_code,
            'createdAt'    => $customer->created_at?->toIso8601String(),
            'updatedAt'    => $customer->updated_at?->toIso8601String(),
        ];
    }

    private function summaryFor($query)
    {
        $row = $query
            ->reorder()
            ->selectRaw('
                COUNT(*) as total,
                COUNT(CASE WHEN created_at >= DATE_FORMAT(CURDATE(), "%Y-%m-01") THEN 1 ELSE 0 END) as newThisMonth,
                COUNT(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as newToday
            ')
            ->first();

        return [
            'total'        => (int) ($row->total ?? 0),
            'newThisMonth' => (int) ($row->newThisMonth ?? 0),
            'newToday'     => (int) ($row->newToday ?? 0),
        ];
    }

    private function meta($paginator)
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

    /* ───────────────────────────────────────────────────────
     |  GET /api/wateja/{id}
     ─────────────────────────────────────────────────────── */
    public function show(Request $request, $id)
    {
        $companyId = $request->user()?->company_id;
        if (!$companyId) {
            return $this->error('Akaunti yako haijaunganishwa na kampuni.', 403);
        }

        $customer = Mteja::where('company_id', $companyId)->find($id);
        if (!$customer) {
            return $this->error('Mteja hajapatikana.', 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $this->customerPayload($customer),
        ]);
    }

    /* ───────────────────────────────────────────────────────
     |  POST /api/wateja
     ─────────────────────────────────────────────────────── */
    public function store(Request $request)
    {
        $companyId = $request->user()?->company_id;
        if (!$companyId) {
            return $this->error('Akaunti yako haijaunganishwa na kampuni.', 403);
        }

        $input = $this->normaliseInput($request->all());
        $v = validator($input, $this->rules());

        if ($v->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $v->errors(),
                'message' => $v->errors()->first(),
            ], 422);
        }

        $data = $v->validated();
        // mtejas.simu is NOT NULL in the database — an omitted phone must be
        // stored as an empty string, never as null.
        $data['simu']          = $data['simu'] ?? '';
        $data['company_id']    = $companyId;
        $data['customer_code'] = Mteja::generateCustomerCode($companyId);

        $customer = Mteja::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Mteja amesajiliwa!',
            'data'    => $this->customerPayload($customer),
        ], 201);
    }

    /* ───────────────────────────────────────────────────────
     |  PUT /api/wateja/{id}
     ─────────────────────────────────────────────────────── */
    public function update(Request $request, $id)
    {
        $companyId = $request->user()?->company_id;
        if (!$companyId) {
            return $this->error('Akaunti yako haijaunganishwa na kampuni.', 403);
        }

        $customer = Mteja::where('company_id', $companyId)->find($id);
        if (!$customer) {
            return $this->error('Mteja hajapatikana.', 404);
        }

        $input = $this->normaliseInput($request->all());
        $v = validator($input, $this->rules());

        if ($v->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $v->errors(),
                'message' => $v->errors()->first(),
            ], 422);
        }

        $data = $v->validated();
        if (array_key_exists('simu', $data) && $data['simu'] === null) {
            // Keep the existing phone when the field was not supplied at all.
            unset($data['simu']);
        }

        $customer->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Taarifa za mteja zimesasishwa!',
            'data'    => $this->customerPayload($customer->fresh()),
        ]);
    }

    /* ───────────────────────────────────────────────────────
     |  DELETE /api/wateja/{id}
     ─────────────────────────────────────────────────────── */
    public function destroy(Request $request, $id)
    {
        $companyId = $request->user()?->company_id;
        if (!$companyId) {
            return $this->error('Akaunti yako haijaunganishwa na kampuni.', 403);
        }

        $customer = Mteja::where('company_id', $companyId)->find($id);
        if (!$customer) {
            return $this->error('Mteja hajapatikana.', 404);
        }

        $orderCount = Order::where('company_id', $companyId)
            ->where('customer_id', $id)
            ->count();
        if ($orderCount > 0) {
            return $this->error(
                "Haiwezi kufuta mteja huyu kwa sababu ana {$orderCount} maagizo yaliyorekodiwa.",
                422
            );
        }

        $customer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mteja amefutwa!',
        ]);
    }

    /** Validation shared by store() and update(). */
    private function rules(): array
    {
        return [
            'jina'       => 'required|string|max:255',
            'simu'       => 'nullable|string|max:20',
            'barua_pepe' => 'nullable|email|max:255',
            'anapoishi'  => 'nullable|string|max:500',
            'maelezo'    => 'nullable|string|max:1000',
        ];
    }

    /**
     * The Flutter client sends empty strings for optional fields. Convert
     * those to null (except `simu`, which is a NOT NULL column) so an empty
     * email does not fail the `email` validation rule.
     */
    private function normaliseInput(array $input): array
    {
        foreach (['barua_pepe', 'anapoishi', 'maelezo'] as $field) {
            if (array_key_exists($field, $input)
                && is_string($input[$field])
                && trim($input[$field]) === '') {
                $input[$field] = null;
            }
        }

        if (array_key_exists('simu', $input) && is_string($input['simu'])) {
            $input['simu'] = trim($input['simu']) === ''
                ? ''
                : trim($input['simu']);
        }

        return $input;
    }

    private function error(string $message, int $status): \Illuminate\Http\JsonResponse
    {
        return response()->json(['success' => false, 'message' => $message], $status);
    }
}