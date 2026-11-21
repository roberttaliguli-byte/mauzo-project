<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bidhaa;
use App\Models\Manunuzi;
use App\Models\Mauzo;
use App\Models\Madeni;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductsApiController extends Controller
{
    /* ─────────────────────────────────────────────────────
     | GET /api/bidhaa
     | List + search + filter + summary.
     ───────────────────────────────────────────────────── */
    public function index(Request $request)
    {
        $companyId = $request->user()?->company_id;
        if (!$companyId) return $this->err403();

        $perPage = max(1, (int) $request->input('per_page', 20));
        $search  = trim((string) $request->input('search', ''));
        $filter  = $request->input('filter');

        $query = Bidhaa::where('company_id', $companyId);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('jina', 'like', "%{$search}%")
                  ->orWhere('aina', 'like', "%{$search}%")
                  ->orWhere('kipimo', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        if ($filter === 'available') {
            $query->where('idadi', '>', 0);
        } elseif ($filter === 'low_stock') {
            $query->where('idadi', '>', 0)->where('idadi', '<', 10);
        } elseif ($filter === 'out_of_stock') {
            $query->where('idadi', '<=', 0);
        } elseif ($filter === 'expired') {
            $query->whereNotNull('expiry')->where('expiry', '<', now());
        }

        $paginator = $query->orderBy('jina')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => collect($paginator->items())
                ->map(fn ($p) => $this->payload($p))
                ->values(),
            'summary' => $this->summary($companyId),
            'meta'    => $this->meta($paginator),
        ]);
    }

    /* ─────────────────────────────────────────────────────
     | GET /api/bidhaa/{id}
     | Single product + history (for the Taarifa tab).
     ───────────────────────────────────────────────────── */
    public function show(Request $request, $id)
    {
        $companyId = $request->user()?->company_id;
        if (!$companyId) return $this->err403();

        $product = Bidhaa::where('company_id', $companyId)->find($id);
        if (!$product) return $this->err('Bidhaa haipatikani.', 404);

        return response()->json([
            'success' => true,
            'data'    => $this->payload($product),
            'history' => $this->history($companyId, $id),
        ]);
    }

    /* ─────────────────────────────────────────────────────
     | POST /api/bidhaa
     | Create product (with optional image upload).
     ───────────────────────────────────────────────────── */
    public function store(Request $request)
    {
        $companyId = $request->user()?->company_id;
        if (!$companyId) return $this->err403();

        $v = validator($request->all(), [
            'jina'           => 'required|string|max:255',
            'aina'           => 'required|string|max:255',
            'kipimo'         => 'nullable|string|max:100',
            'idadi'          => 'required|numeric|min:0',
            'bei_nunua'      => 'required|numeric|min:0',
            'bei_kuuza'      => 'required|numeric|min:0',
            'bei_uzo_jumla'  => 'nullable|numeric|min:0',
            'bei_kiasi_cha_chaguo' => 'nullable|in:rejareja,jumla',
            'expiry'         => 'nullable|date',
            'barcode'        => 'nullable|string|max:100',
            'image'          => 'nullable|image|max:10240',
        ]);
        if ($v->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $v->errors(),
                'message' => $v->errors()->first(),
            ], 422);
        }

        if ((float) $request->bei_nunua > (float) $request->bei_kuuza) {
            return $this->err('Bei ya kununua haiwezi kuwa kubwa kuliko bei ya kuuza.', 422);
        }

        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
        }

        $product = Bidhaa::create([
            'company_id'   => $companyId,
            'jina'         => $request->jina,
            'aina'         => $request->aina,
            'kipimo'       => $request->kipimo,
            'idadi'        => $request->idadi,
            'bei_nunua'    => $request->bei_nunua,
            'bei_kuuza'    => $request->bei_kuuza,
            'bei_uzo_jumla'=> $request->bei_uzo_jumla,
            'bei_kiasi_cha_chaguo' => $request->bei_kiasi_cha_chaguo ?? 'rejareja',
            'expiry'       => $request->expiry,
            'barcode'      => $request->barcode,
            'image_path'   => $path,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bidhaa imehifadhiwa!',
            'data'    => $this->payload($product),
        ], 201);
    }

    /* ─────────────────────────────────────────────────────
     | PUT /api/bidhaa/{id}
     ───────────────────────────────────────────────────── */
    public function update(Request $request, $id)
    {
        $companyId = $request->user()?->company_id;
        if (!$companyId) return $this->err403();
        if (!$this->canManageProducts($request)) {
            return $this->err('Huna ruhusa ya kubadiliza bidhaa.', 403);
        }

        $product = Bidhaa::where('company_id', $companyId)->find($id);
        if (!$product) return $this->err('Bidhaa haipatikani.', 404);

        $v = validator($request->all(), [
            'jina'      => 'required|string|max:255',
            'aina'      => 'required|string|max:255',
            'idadi'     => 'required|numeric|min:0',
            'bei_nunua' => 'required|numeric|min:0',
            'bei_kuuza' => 'required|numeric|min:0',
            'image'     => 'nullable|image|max:10240',
        ]);
        if ($v->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $v->errors(),
                'message' => $v->errors()->first(),
            ], 422);
        }

        if ($request->hasFile('image')) {
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $product->image_path = $request->file('image')->store('products', 'public');
        }

        $product->fill($request->only([
            'jina', 'aina', 'kipimo', 'idadi', 'bei_nunua', 'bei_kuuza',
            'bei_uzo_jumla', 'bei_kiasi_cha_chaguo', 'expiry', 'barcode',
        ]));
        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Bidhaa imebadilishwa!',
            'data'    => $this->payload($product),
        ]);
    }

    /* ─────────────────────────────────────────────────────
     | DELETE /api/bidhaa/{id}
     ───────────────────────────────────────────────────── */
    public function destroy(Request $request, $id)
    {
        $companyId = $request->user()?->company_id;
        if (!$companyId) return $this->err403();
        if (!$this->canManageProducts($request)) {
            return $this->err('Huna ruhusa ya kufuta bidhaa.', 403);
        }

        $product = Bidhaa::where('company_id', $companyId)->find($id);
        if (!$product) return $this->err('Bidhaa haipatikani.', 404);

        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bidhaa imefutwa!',
        ]);
    }

    /* ─────────────────────────────────────────────────────
     | POST /api/bidhaa/upload-excel
     | CSV/Excel batch upsert.
     ───────────────────────────────────────────────────── */
    public function uploadExcel(Request $request)
    {
        $companyId = $request->user()?->company_id;
        if (!$companyId) return $this->err403();

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        // Use PhpSpreadsheet (or Laravel Excel) to parse; simplified here.
        $rows = $this->parseExcel($request->file('file'));

        $created = 0;
        $updated = 0;
        $failed  = 0;
        $errors  = [];

        DB::beginTransaction();
        try {
            foreach ($rows as $i => $row) {
                $nama = trim((string) ($row['jina'] ?? ''));
                $aina = trim((string) ($row['aina'] ?? ''));
                if ($nama === '' || $aina === '') {
                    $failed++;
                    $errors[] = 'Mstari ' . ($i + 2) . ': jina au aina missing';
                    continue;
                }
                $existing = Bidhaa::where('company_id', $companyId)
                    ->where('jina', $nama)
                    ->where('aina', $aina)
                    ->first();

                $data = [
                    'company_id' => $companyId,
                    'jina'       => $nama,
                    'aina'       => $aina,
                    'kipimo'     => $row['kipimo'] ?? null,
                    'idadi'      => (float) ($row['idadi'] ?? 0),
                    'bei_nunua'  => (float) ($row['bei_nunua'] ?? 0),
                    'bei_kuuza'  => (float) ($row['bei_kuuza'] ?? 0),
                    'bei_uzo_jumla' => isset($row['bei_uzo_jumla']) && $row['bei_uzo_jumla'] !== ''
                        ? (float) $row['bei_uzo_jumla'] : null,
                    'expiry'     => $row['expiry'] ?? null,
                    'barcode'    => $row['barcode'] ?? null,
                ];

                if ($existing) {
                    $existing->update($data);
                    $updated++;
                } else {
                    Bidhaa::create($data);
                    $created++;
                }
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->err('Hitilafu katika upakiaji: ' . $e->getMessage(), 500);
        }

        return response()->json([
            'success' => true,
            'message' => "Imepakiwa: $created mpya, $updated zilizosasishwa, $failed zilizoshindikana",
            'created' => $created,
            'updated' => $updated,
            'failed'  => $failed,
            'errors'  => array_slice($errors, 0, 20),
        ]);
    }

    /* ─────────────────────────────────────────────────────
     | Helpers
     ───────────────────────────────────────────────────── */
    private function payload(Bidhaa $p): array
    {
        return [
            'id'             => (int) $p->id,
            'name'           => $p->jina,
            'category'       => $p->aina,
            'unit'           => $p->kipimo,
            'stock'          => (float) $p->idadi,
            'sellingPrice'   => (float) $p->bei_kuuza,
            'wholesalePrice' => $p->bei_uzo_jumla !== null ? (float) $p->bei_uzo_jumla : null,
            'priceMode'      => $p->bei_kiasi_cha_chaguo,
            'purchasePrice'  => (float) $p->bei_nunua,
            'expiry'         => $p->expiry,
            'barcode'        => $p->barcode,
            'stockStatus'    => $p->idadi <= 0 ? 'out_of_stock'
                              : ($p->idadi < 10 ? 'low_stock' : 'in_stock'),
            'isExpired'      => $p->expiry && strtotime($p->expiry) < time(),
            'hasImage'       => (bool) $p->image_path,
            'imageUrl'       => $p->image_path
                ? Storage::disk('public')->url($p->image_path)
                : null,
        ];
    }

    private function summary(int $companyId): array
    {
        $today = now()->startOfDay();
        $base = Bidhaa::where('company_id', $companyId);

        return [
            'total'      => (clone $base)->count(),
            'inStock'    => (clone $base)->where('idadi', '>', 0)->count(),
            'lowStock'   => (clone $base)->where('idadi', '>', 0)->where('idadi', '<', 10)->count(),
            'outOfStock' => (clone $base)->where('idadi', '<=', 0)->count(),
            'expired'    => (clone $base)->whereNotNull('expiry')->where('expiry', '<', now())->count(),
        ];
    }

    private function history(int $companyId, int $productId): array
    {
        $purchases = Manunuzi::where('company_id', $companyId)
            ->where('bidhaa_id', $productId)
            ->where('status', 'final')
            ->orderByDesc('created_at')
            ->limit(50)
            ->get()
            ->map(fn ($p) => [
                'type'      => 'purchase',
                'date'      => $p->created_at?->toIso8601String(),
                'quantity'  => (float) $p->idadi,
                'amount'    => (float) $p->bei,
                'note'      => 'Manunuzi',
            ]);

        $sales = Mauzo::where('company_id', $companyId)
            ->where('bidhaa_id', $productId)
            ->orderByDesc('created_at')
            ->limit(50)
            ->get()
            ->map(fn ($s) => [
                'type'      => 'sale',
                'date'      => $s->created_at?->toIso8601String(),
                'quantity'  => (float) $s->idadi,
                'amount'    => (float) $s->jumla,
                'note'      => $s->status ?? '',
            ]);

        $debts = Madeni::where('company_id', $companyId)
            ->where('bidhaa_id', $productId)
            ->orderByDesc('created_at')
            ->limit(50)
            ->get()
            ->map(fn ($d) => [
                'type'      => 'debt',
                'date'      => $d->created_at?->toIso8601String(),
                'quantity'  => (float) $d->idadi,
                'amount'    => (float) $d->jumla,
                'note'      => $d->jina_mkopaji ?? '',
            ]);

        $all = collect()
            ->merge($purchases)
            ->merge($sales)
            ->merge($debts)
            ->sortByDesc('date')
            ->values()
            ->take(100);

        return $all->toArray();
    }

    /**
     * Parse an Excel/CSV file into rows.
     * Requires `maatwebsite/excel` or `phpoffice/phpspreadsheet`.
     * Adjust to whatever you already have installed.
     */
    private function parseExcel($file): array
    {
        $ext = strtolower($file->getClientOriginalExtension());
        if ($ext === 'csv') {
            $rows = [];
            $handle = fopen($file->getRealPath(), 'r');
            $header = fgetcsv($handle);
            if (!$header) return [];
            while (($data = fgetcsv($handle)) !== false) {
                $row = [];
                foreach ($header as $i => $key) {
                    $row[strtolower(str_replace(' ', '_', $key))] = $data[$i] ?? null;
                }
                $rows[] = $row;
            }
            fclose($handle);
            return $rows;
        }
        // Excel handled by maatwebsite/excel if installed — placeholder
        abort(422, 'CSV pekee kwa sasa. Sakinisha maatwebsite/excel kwa XLSX.');
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

    /**
     * Bosses/admins and employees with uwezo 'mkubwa' may add, edit or delete
     * products. An employee with uwezo 'mdogo' is read-only on this endpoint.
     */
    private function canManageProducts(Request $request): bool
    {
        $user = $request->user();
        if (!$user) return false;

        $role = strtolower((string) ($user->role ?? ''));
        if ($role === 'boss' || $role === 'admin') return true;

        // Wafanyakazi carry `uwezo`; a normal User does not.
        return isset($user->uwezo)
            && strtolower((string) $user->uwezo) === 'mkubwa';
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