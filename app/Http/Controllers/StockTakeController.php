<?php

namespace App\Http\Controllers;

use App\Models\Bidhaa;
use App\Models\StockTake;
use App\Models\StockTakeItem;
use App\Models\StockTakeUnmatched;
use App\Services\StockTakeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class StockTakeController extends Controller
{
    public function __construct(private StockTakeService $svc) {}

    /** Boss (web) or employee with uwezo = mkubwa (same rule as BidhaaController::canEditDelete) */
    private function actor(): array
    {
        $guard = 'web';
        $u = Auth::guard('web')->user();
        if (!$u) { $guard = 'mfanyakazi'; $u = Auth::guard('mfanyakazi')->user(); }
        abort_unless($u, 403, 'Please login');

        if ($guard === 'mfanyakazi') {
            abort_unless(strtolower(trim($u->uwezo ?? '')) === 'mkubwa', 403, 'You do not have permission for stock taking.');
        }
        abort_unless($u->company_id, 403);

        return [
            'user' => $u, 'guard' => $guard, 'id' => $u->id, 'company_id' => $u->company_id,
            'name' => $u->name ?? $u->jina ?? $u->username ?? $u->email ?? 'User',
            'is_boss' => $guard === 'web',
        ];
    }

    private function take(array $a, $id): StockTake
    {
        return StockTake::where('company_id', $a['company_id'])->findOrFail($id);
    }

    private function openOrFail(StockTake $t): void
    {
        abort_unless($t->isOpen(), 422, 'This stock taking is closed and can no longer be changed.');
    }

    /* ---------------- pages ---------------- */

    public function index()
    {
        $a = $this->actor();
        $open = $this->svc->openTake($a['company_id']);
        $history = StockTake::where('company_id', $a['company_id'])->whereIn('status', ['closed', 'cancelled'])->latest('id')->limit(30)->get();
        $productCount = Bidhaa::active()->where('company_id', $a['company_id'])->count();

        return view('stock-take.index', [
            'open' => $open, 'openCounts' => $open ? $this->svc->counts($open) : null,
            'history' => $history, 'productCount' => $productCount, 'isBoss' => $a['is_boss'],
            'valueRows' => $this->svc->valueComparisonFor($history),
        ]);
    }

    public function start(Request $request)
    {
        $a = $this->actor();
        $request->validate(['title' => 'nullable|string|max:120', 'notes' => 'nullable|string|max:1000']);

        if ($open = $this->svc->openTake($a['company_id'])) {
            return redirect()->route('stocktake.show', $open->id)->with('error', 'A stock taking is already in progress. Finish or cancel it first.');
        }
        $take = $this->svc->start($a, $request->title, $request->notes);

        return redirect()->route('stocktake.show', $take->id)->with('success', 'Stock taking started. All products were loaded.');
    }

    public function show($id)
    {
        $a = $this->actor();
        $take = $this->take($a, $id);

        return view('stock-take.show', [
            'take' => $take, 'counts' => $this->svc->counts($take),
            'summary' => $this->svc->summary($take), 'isBoss' => $a['is_boss'],
            'readonly' => !$take->isOpen(),
            'valueComparison' => $take->isClosed() ? $this->svc->valueComparison($take) : null,
        ]);
    }

    public function compare($id, $with = null)
    {
        $a = $this->actor();
        $cur = $this->take($a, $id);
        $prevId = $with ?: $cur->previous_take_id;
        abort_unless($prevId, 404, 'There is no earlier stock taking to compare with.');
        $prev = $this->take($a, $prevId);

        $others = StockTake::where('company_id', $a['company_id'])->where('status', 'closed')->where('id', '!=', $cur->id)->latest('id')->get(['id', 'reference', 'title', 'closed_at']);

        return view('stock-take.compare', [
            'cur' => $cur, 'prev' => $prev, 'others' => $others, 'cmp' => $this->svc->compare($cur, $prev),
        ]);
    }

    /* ---------------- counting (JSON) ---------------- */

    public function items(Request $request, $id)
    {
        $a = $this->actor();
        $take = $this->take($a, $id);

        $q = StockTakeItem::where('stock_take_id', $take->id);

        if ($s = trim((string) $request->search)) {
            $q->where(fn ($w) => $w->where('product_name', 'like', "%{$s}%")->orWhere('barcode', 'like', "%{$s}%")
                ->orWhere('category', 'like', "%{$s}%")->orWhere('unit', 'like', "%{$s}%"));
        }
        $f = $request->filter;
        if (in_array($f, ['NOT_COUNTED', 'MATCHED', 'SHORTAGE', 'EXCESS', 'NOT_FOUND'], true)) $q->where('status', $f);
        if ($f === 'zero_system') $q->where('system_qty', 0);

        $page = $q->orderBy('product_name')->paginate(40);

        return response()->json([
            'success' => true,
            'data' => $page->getCollection()->map(fn ($i) => $this->svc->itemRow($i))->values(),
            'meta' => ['page' => $page->currentPage(), 'last' => $page->lastPage(), 'total' => $page->total()],
            'counts' => $this->svc->counts($take),
        ]);
    }

    public function count(Request $request, $id, $itemId)
    {
        $a = $this->actor();
        $take = $this->take($a, $id);
        $this->openOrFail($take);
        $item = StockTakeItem::where('stock_take_id', $take->id)->findOrFail($itemId);

        $v = Validator::make($request->all(), [
            'physical_qty' => ['nullable', 'numeric', 'min:0', 'max:99999999', 'regex:/^\d+(\.\d{1,2})?$/'],
            'unavailable'  => 'nullable|boolean',
        ]);
        if ($v->fails()) return response()->json(['success' => false, 'message' => $v->errors()->first()], 422);

        $physical = $request->filled('physical_qty') ? (float) $request->physical_qty : null;

        try {
            $saved = $this->svc->saveCount($item->id, $physical, (bool) $request->unavailable, $a);
        } catch (\DomainException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json(['success' => true, 'item' => $this->svc->itemRow($saved), 'counts' => $this->svc->counts($take)]);
    }

    public function sync($id)
    {
        $a = $this->actor();
        $take = $this->take($a, $id);
        $this->openOrFail($take);
        $added = $this->svc->syncProducts($take);

        return response()->json(['success' => true, 'message' => $added ? "{$added} new product(s) added." : 'No new products.', 'counts' => $this->svc->counts($take)]);
    }

    /* ---------------- unmatched (NOT IN SYSTEM) ---------------- */

    public function storeUnmatched(Request $request, $id)
    {
        $a = $this->actor();
        $take = $this->take($a, $id);
        $this->openOrFail($take);

        $data = $request->validate([
            'name' => 'required|string|max:255', 'unit' => 'nullable|string|max:100', 'category' => 'nullable|string|max:255',
            'barcode' => 'nullable|string|max:255', 'quantity' => 'required|numeric|min:0', 'notes' => 'nullable|string|max:500',
        ]);

        // Use barcode / name+unit to stop duplicates of products that DO exist
        $q = Bidhaa::where('company_id', $a['company_id']);
        $existing = null;
        if (!empty($data['barcode'])) $existing = (clone $q)->where('barcode', $data['barcode'])->first();
        if (!$existing) {
            $unit = trim((string) ($data['unit'] ?? ''));
            $existing = (clone $q)->where('jina', $data['name'])
                ->where(fn ($w) => $unit !== '' ? $w->where('kipimo', $unit) : $w->whereNull('kipimo')->orWhere('kipimo', ''))
                ->first();
        }

        if ($existing) {
            return response()->json(['success' => false,
                'message' => "This product already exists in the system as \"{$existing->jina}\" ({$existing->kipimo}). Count it in the list instead."], 422);
        }

        $row = StockTakeUnmatched::create($data + ['stock_take_id' => $take->id, 'company_id' => $take->company_id, 'created_by' => $a['name']]);

        return response()->json(['success' => true, 'row' => $row]);
    }

    public function destroyUnmatched($id, $rowId)
    {
        $a = $this->actor();
        $take = $this->take($a, $id);
        $this->openOrFail($take);
        StockTakeUnmatched::where('stock_take_id', $take->id)->where('id', $rowId)->delete();

        return response()->json(['success' => true]);
    }

    /* ---------------- reconciliation (JSON) ---------------- */

    public function report($id)
    {
        $a = $this->actor();
        $take = $this->take($a, $id);

        return response()->json(['success' => true, 'closed' => !$take->isOpen()] + $this->svc->report($take));
    }

    public function resolve(Request $request, $id)
    {
        $a = $this->actor();
        $take = $this->take($a, $id);
        $this->openOrFail($take);

        $data = $request->validate([
            'item_ids' => 'required|array|min:1|max:500', 'item_ids.*' => 'integer',
            'action' => 'required|string', 'note' => 'nullable|string|max:1000',
        ]);

        try {
            $res = $this->svc->resolve($take, $data['item_ids'], $data['action'], $data['note'] ?? null, $a);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json(['success' => true, 'message' => "{$res['done']} product(s) updated.", 'skipped' => $res['skipped']]);
    }

    public function close(Request $request, $id)
    {
        $a = $this->actor();
        $take = $this->take($a, $id);
        $this->openOrFail($take);

        try {
            $this->svc->close($take, $a, (bool) $request->force);
        } catch (\DomainException $e) {
            if (str_starts_with($e->getMessage(), 'uncounted:')) {
                return response()->json(['success' => false, 'needs_force' => true,
                    'message' => substr($e->getMessage(), 10) . ' product(s) have not been counted yet. Close anyway? They will stay NOT COUNTED in the history.'], 422);
            }
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json(['success' => true, 'message' => 'Stock taking closed and saved to history.', 'redirect' => route('stocktake.index')]);
    }

    /**
     * "Update Stock to Current Count" — explicit, confirmed replacement of system
     * quantities with the counted quantities of a CLOSED session. Never automatic.
     */
    public function applyStock($id)
    {
        $a = $this->actor();
        $take = $this->take($a, $id);

        try {
            $res = $this->svc->applyToStock($take, $a);
        } catch (\DomainException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json(['success' => true,
            'message' => "System stock updated to the counted quantities for {$res['updated']} product(s)." . ($res['missing'] ? " {$res['missing']} product(s) no longer exist and were skipped." : ''),
            'redirect' => route('stocktake.index')]);
    }

    public function cancel($id)
    {
        $a = $this->actor();
        $take = $this->take($a, $id);
        $this->openOrFail($take);

        if (StockTakeItem::where('stock_take_id', $take->id)->whereNotNull('adjustment_id')->exists()) {
            return response()->json(['success' => false, 'message' => 'Adjustments were already approved in this session. Close it instead of cancelling.'], 422);
        }
        $take->update(['status' => 'cancelled', 'closed_at' => now(), 'closed_by_name' => $a['name']]);

        return response()->json(['success' => true, 'redirect' => route('stocktake.index')]);
    }
}
