<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bidhaa;
use App\Models\Wafanyakazi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Read-only product (Bidhaa) endpoints for the mobile app.
 *
 * Every query is scoped to the authenticated token owner's company_id so one
 * company can never read another company's products. Purchase price is only
 * exposed to owners/managers (web users or employees with uwezo "mkubwa"),
 * matching the existing web permission logic.
 */
class CatalogApiController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $this->companyId($request);
        if (!$companyId) {
            return $this->noCompanyResponse();
        }

        $data = $request->validate([
            'search'   => 'nullable|string|max:100',
            'filter'   => 'nullable|in:available,low_stock,out_of_stock,expired',
            'per_page' => 'nullable|integer|min:1|max:100',
            'page'     => 'nullable|integer|min:1',
        ]);

        $perPage = (int) ($data['per_page'] ?? 20);
        $search  = trim((string) ($data['search'] ?? ''));
        $filter  = $data['filter'] ?? null;

        $query = Bidhaa::active()->where('company_id', $companyId);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('jina', 'like', "%{$search}%")
                    ->orWhere('aina', 'like', "%{$search}%")
                    ->orWhere('kipimo', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        $this->applyStockFilter($query, $filter);

        // Company-wide stock summary. The filtered result count is already in
        // `meta.total`, so these counters stay stable while the user filters.
        $summary = $this->summaryFor(
            Bidhaa::active()->where('company_id', $companyId)
        );

        $paginator = $query
            ->select([
                'id',
                'company_id',
                'jina',
                'aina',
                'kipimo',
                'idadi',
                'bei_nunua',
                'bei_kuuza',
                'bei_uzo_jumla',
                'bei_kiasi_cha_chaguo',
                'expiry',
                'barcode',
                'image_path',
                'image_mime_type',
                'image_size',
                'created_at',
                'updated_at',
            ])
            ->selectRaw('(image IS NOT NULL) as has_blob_image')
            ->orderByDesc('created_at')
            ->paginate($perPage);

        $canSeeCost = $this->canSeePurchasePrice($request);

        return response()->json([
            'success' => true,
            'data'    => collect($paginator->items())
                ->map(fn (Bidhaa $product) => $this->productPayload($product, $request, $canSeeCost))
                ->values(),
            'summary' => $summary,
            'meta'    => $this->meta($paginator),
            'filters' => [
                'search' => $search,
                'filter' => $filter,
            ],
        ]);
    }

    public function show(Request $request, int $id)
    {
        $companyId = $this->companyId($request);
        if (!$companyId) {
            return $this->noCompanyResponse();
        }

        $product = Bidhaa::active()
            ->where('company_id', $companyId)
            ->where('id', $id)
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Bidhaa haijapatikana.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $this->productPayload(
                $product,
                $request,
                $this->canSeePurchasePrice($request)
            ),
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

    private function canSeePurchasePrice(Request $request): bool
    {
        $user = $request->user();

        if ($user instanceof Wafanyakazi) {
            return strtolower(trim((string) $user->uwezo)) === 'mkubwa';
        }

        // Web users (boss/admin) keep full product visibility.
        return true;
    }

    private function applyStockFilter($query, ?string $filter): void
    {
        switch ($filter) {
            case 'available':
                $query->where('idadi', '>', 0);
                break;
            case 'low_stock':
                $query->where('idadi', '>', 0)->where('idadi', '<', 10);
                break;
            case 'out_of_stock':
                $query->where('idadi', '<=', 0);
                break;
            case 'expired':
                $query->whereNotNull('expiry')->whereDate('expiry', '<', Carbon::today());
                break;
        }
    }

    private function summaryFor($query): array
    {
        $row = $query->selectRaw(
            'COUNT(*) as total, ' .
            'SUM(CASE WHEN idadi > 0 THEN 1 ELSE 0 END) as in_stock, ' .
            'SUM(CASE WHEN idadi > 0 AND idadi < 10 THEN 1 ELSE 0 END) as low_stock, ' .
            'SUM(CASE WHEN idadi <= 0 THEN 1 ELSE 0 END) as out_of_stock, ' .
            'SUM(CASE WHEN expiry IS NOT NULL AND expiry < ? THEN 1 ELSE 0 END) as expired',
            [Carbon::today()->toDateString()]
        )->first();

        return [
            'total'        => (int) ($row->total ?? 0),
            'in_stock'     => (int) ($row->in_stock ?? 0),
            'low_stock'    => (int) ($row->low_stock ?? 0),
            'out_of_stock' => (int) ($row->out_of_stock ?? 0),
            'expired'      => (int) ($row->expired ?? 0),
        ];
    }

    private function productPayload(Bidhaa $product, Request $request, bool $canSeeCost): array
    {
        $stock = (float) $product->idadi;
        $stockStatus = $stock <= 0
            ? 'out_of_stock'
            : ($stock < 10 ? 'low_stock' : 'in_stock');

        $expiry = $product->expiry
            ? Carbon::parse($product->expiry)->startOfDay()
            : null;
        $isExpired = $expiry !== null && $expiry->lt(Carbon::today());

        $imagePath = $product->image_path;
        $hasImage = !empty($imagePath) || (bool) ($product->has_blob_image ?? false);

        return [
            'id'                      => (int) $product->id,
            'jina'                    => (string) $product->jina,
            'aina'                    => (string) $product->aina,
            'kipimo'                  => $product->kipimo,
            'idadi'                   => $stock,
            'bei_kuuza'               => (float) $product->bei_kuuza,
            'bei_uzo_jumla'           => $product->bei_uzo_jumla !== null
                ? (float) $product->bei_uzo_jumla
                : null,
            'bei_kiasi_cha_chaguo'    => $product->bei_kiasi_cha_chaguo,
            'bei_nunua'               => $canSeeCost ? (float) $product->bei_nunua : null,
            'expiry'                  => $expiry?->toDateString(),
            'barcode'                 => $product->barcode,
            'stock_status'            => $stockStatus,
            'is_expired'              => $isExpired,
            'has_image'               => $hasImage,
            'image_url'               => $this->imageUrl($request, $imagePath),
            'image_mime_type'         => $product->image_mime_type,
            'image_size'              => $product->image_size !== null
                ? (int) $product->image_size
                : null,
            'created_at'              => $product->created_at?->toIso8601String(),
            'updated_at'              => $product->updated_at?->toIso8601String(),
        ];
    }

    /**
     * Build an absolute URL from the host the request actually arrived on.
     * asset() would use APP_URL, which is often localhost and unreachable from
     * a physical phone, so the request host is used instead.
     */
    private function imageUrl(Request $request, ?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        if (!Storage::disk('public')->exists($path)) {
            return null;
        }

        return rtrim($request->getSchemeAndHttpHost(), '/')
            . '/storage/' . ltrim($path, '/');
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
}
