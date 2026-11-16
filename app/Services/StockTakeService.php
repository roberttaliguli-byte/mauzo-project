<?php

namespace App\Services;

use App\Models\Bidhaa;
use App\Models\Manunuzi;
use App\Models\Mauzo;
use App\Models\StockAdjustment;
use App\Models\StockTake;
use App\Models\StockTakeItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * All stock-taking maths lives here.
 *
 * Nothing in this service ever writes to bidhaas.idadi except resolve() with an
 * explicit, authorised "adjusted" / "receipt_corrected" action. Counting a
 * product only writes to stock_take_items.
 *
 * Expected = Previous verified + Received + Customer returns - Sales - Supplier returns +/- Adjustments
 * Variance = Physical - (Expected ?? System)
 */
class StockTakeService
{
    public const TOL = 0.005;

    public const RESOLUTIONS = [
        'confirmed', 'investigating', 'sale_investigation',
        'recount', 'unresolved', 'adjusted', 'receipt_corrected',
    ];

    /* ------------------------------------------------------------------ */
    /* Sessions                                                            */
    /* ------------------------------------------------------------------ */

    public function openTake(int $companyId): ?StockTake
    {
        return StockTake::where('company_id', $companyId)->where('status', 'counting')->latest('id')->first();
    }

    public function start(array $actor, ?string $title, ?string $notes): StockTake
    {
        return DB::transaction(function () use ($actor, $title, $notes) {
            $cid  = $actor['company_id'];
            $prev = StockTake::where('company_id', $cid)->where('status', 'closed')->latest('id')->first();
            $seq  = StockTake::where('company_id', $cid)->count() + 1;

            $take = StockTake::create([
                'company_id'       => $cid,
                'reference'        => 'ST-' . now()->format('Ymd') . '-' . str_pad((string) $seq, 3, '0', STR_PAD_LEFT),
                'title'            => $title ?: 'Stock taking ' . now()->format('d/m/Y'),
                'status'           => 'counting',
                'previous_take_id' => $prev?->id,
                'started_by_id'    => $actor['id'],
                'started_by_guard' => $actor['guard'],
                'started_by_name'  => $actor['name'],
                'started_at'       => now(),
                'notes'            => $notes,
            ]);

            $this->syncProducts($take);

            return $take;
        });
    }

    /** Load every active product (including zero stock). Safe to call again to pick up new products. */
    public function syncProducts(StockTake $take): int
    {
        $existing = array_flip($take->items()->pluck('bidhaa_id')->all());
        $added = 0;
        $now = now();

        Bidhaa::active()
            ->where('company_id', $take->company_id)
            ->select('id', 'jina', 'aina', 'kipimo', 'barcode', 'idadi', 'bei_nunua', 'bei_kuuza')
            ->orderBy('id')
            ->chunkById(500, function ($chunk) use ($take, &$existing, &$added, $now) {
                $rows = [];
                foreach ($chunk as $p) {
                    if (isset($existing[$p->id])) continue;
                    $rows[] = [
                        'stock_take_id'  => $take->id,
                        'company_id'     => $take->company_id,
                        'bidhaa_id'      => $p->id,
                        'product_name'   => $p->jina,
                        'unit'           => $p->kipimo,
                        'category'       => $p->aina,
                        'barcode'        => $p->barcode,
                        'purchase_price' => (float) $p->bei_nunua,
                        'sell_price'     => (float) $p->bei_kuuza,
                        'system_qty'     => (float) $p->idadi,
                        'status'         => 'NOT_COUNTED',
                        'created_at'     => $now,
                        'updated_at'     => $now,
                    ];
                }
                if ($rows) {
                    StockTakeItem::insert($rows);
                    $added += count($rows);
                }
            });

        return $added;
    }

    /* ------------------------------------------------------------------ */
    /* Counting                                                            */
    /* ------------------------------------------------------------------ */

    public function classify(float $system, ?float $physical, bool $unavailable): string
    {
        if ($unavailable) return $system > self::TOL ? 'NOT_FOUND' : 'MATCHED';
        if ($physical === null) return 'NOT_COUNTED';
        $d = $physical - $system;
        if (abs($d) <= self::TOL) return 'MATCHED';
        return $d < 0 ? 'SHORTAGE' : 'EXCESS';
    }

    /** Last verified count of this product in an earlier CLOSED session. */
    public function baseline(int $companyId, int $bidhaaId, int $beforeTakeId): ?StockTakeItem
    {
        return StockTakeItem::query()
            ->join('stock_takes', 'stock_takes.id', '=', 'stock_take_items.stock_take_id')
            ->where('stock_take_items.company_id', $companyId)
            ->where('stock_take_items.bidhaa_id', $bidhaaId)
            ->where('stock_takes.status', 'closed')
            ->where('stock_takes.id', '<', $beforeTakeId)
            ->whereNotNull('stock_take_items.counted_at')
            ->orderByDesc('stock_takes.id')
            ->select('stock_take_items.*')
            ->first();
    }

    /**
     * Movements already recorded by the existing modules between two moments.
     * Reuses Manunuzi and Mauzo (cash AND credit sales) exactly like BidhaaController@taarifa.
     *
     * Returns / supplier returns: this project has no returns model that was provided, so those
     * two values are 0 until you plug your returns table in below.
     */
    public function movements(int $companyId, int $bidhaaId, ?Carbon $from, Carbon $to): array
    {
        $zero = ['received' => 0.0, 'sold' => 0.0, 'returns_in' => 0.0, 'returns_out' => 0.0, 'adjustments' => 0.0];
        if (!$from) return $zero;

        $received = (float) Manunuzi::where('company_id', $companyId)->where('bidhaa_id', $bidhaaId)
            ->where('created_at', '>', $from)->where('created_at', '<=', $to)->sum('idadi');

        $sold = (float) Mauzo::where('company_id', $companyId)->where('bidhaa_id', $bidhaaId)
            ->where('created_at', '>', $from)->where('created_at', '<=', $to)->sum('idadi');

        $adj = (float) StockAdjustment::where('company_id', $companyId)->where('bidhaa_id', $bidhaaId)
            ->where('affects_expected', true)
            ->where('created_at', '>', $from)->where('created_at', '<=', $to)->sum('difference');

        // EXTENSION POINT: customer returns (+) and supplier returns (-)
        $returnsIn = 0.0;
        $returnsOut = 0.0;

        return [
            'received' => $received, 'sold' => $sold,
            'returns_in' => $returnsIn, 'returns_out' => $returnsOut, 'adjustments' => $adj,
        ];
    }

    /**
     * Save (or clear) one physical count. Does NOT touch bidhaas.idadi.
     * Pass $physical = null and $unavailable = false to clear the count.
     */
    public function saveCount(int $itemId, ?float $physical, bool $unavailable, array $actor): StockTakeItem
    {
        return DB::transaction(function () use ($itemId, $physical, $unavailable, $actor) {
            $item = StockTakeItem::whereKey($itemId)->lockForUpdate()->firstOrFail();

            if ($item->adjustment_id) {
                throw new \DomainException('This product already has an approved adjustment and cannot be recounted.');
            }

            if ($physical === null && !$unavailable) {
                return $this->resetItem($item);
            }

            $bidhaa = Bidhaa::where('id', $item->bidhaa_id)->where('company_id', $item->company_id)->first();
            $system = $bidhaa ? (float) $bidhaa->idadi : (float) $item->system_qty;
            $now    = now();
            $phys   = $unavailable ? 0.0 : round($physical, 2);

            $prev = $this->baseline($item->company_id, $item->bidhaa_id, $item->stock_take_id);
            $m = $this->movements($item->company_id, $item->bidhaa_id, $prev?->counted_at, $now);
            $expected = $prev
                ? round($prev->physical_qty + $m['received'] + $m['returns_in'] - $m['sold'] - $m['returns_out'] + $m['adjustments'], 2)
                : null;

            $diffSystem   = round($phys - $system, 2);
            $diffExpected = $expected !== null ? round($phys - $expected, 2) : null;

            $item->update([
                'purchase_price'      => $bidhaa ? (float) $bidhaa->bei_nunua : $item->purchase_price,
                'sell_price'          => $bidhaa ? (float) $bidhaa->bei_kuuza : $item->sell_price,
                'system_qty'          => $system,
                'physical_qty'        => $phys,
                'is_unavailable'      => $unavailable,
                'previous_qty'        => $prev?->physical_qty,
                'previous_counted_at' => $prev?->counted_at,
                'received_qty'        => $m['received'],
                'sold_qty'            => $m['sold'],
                'returns_in_qty'      => $m['returns_in'],
                'returns_out_qty'     => $m['returns_out'],
                'adjustments_qty'     => $m['adjustments'],
                'expected_qty'        => $expected,
                'diff_system'         => $diffSystem,
                'diff_expected'       => $diffExpected,
                'variance'            => $diffExpected ?? $diffSystem,
                'status'              => $this->classify($system, $phys, $unavailable),
                'counted_at'          => $now,
                'counted_by'          => $actor['name'],
                'resolution'          => null,
                'resolution_note'     => null,
            ]);

            return $item->fresh();
        });
    }

    private function resetItem(StockTakeItem $item): StockTakeItem
    {
        $item->update([
            'physical_qty' => null, 'is_unavailable' => false, 'previous_qty' => null, 'previous_counted_at' => null,
            'received_qty' => 0, 'sold_qty' => 0, 'returns_in_qty' => 0, 'returns_out_qty' => 0, 'adjustments_qty' => 0,
            'expected_qty' => null, 'diff_system' => null, 'diff_expected' => null, 'variance' => null,
            'status' => 'NOT_COUNTED', 'counted_at' => null, 'counted_by' => null,
            'resolution' => null, 'resolution_note' => null,
        ]);
        return $item->fresh();
    }

    public function counts(StockTake $take): array
    {
        $by = $take->items()->select('status', DB::raw('count(*) c'))->groupBy('status')->pluck('c', 'status');
        $total = (int) $by->sum();
        $notCounted = (int) ($by['NOT_COUNTED'] ?? 0);
        return [
            'total' => $total,
            'counted' => $total - $notCounted,
            'remaining' => $notCounted,
            'percent' => $total ? round(($total - $notCounted) / $total * 100) : 0,
            'by_status' => $by,
        ];
    }

    /* ------------------------------------------------------------------ */
    /* Report                                                              */
    /* ------------------------------------------------------------------ */

    public function summary(StockTake $take): array
    {
        $items = $take->items()->get(['status', 'variance', 'purchase_price', 'resolution']);

        $s = [
            'total' => $items->count(), 'counted' => 0, 'matched' => 0, 'shortage' => 0, 'excess' => 0,
            'not_found' => 0, 'not_counted' => 0, 'unmatched' => $take->unmatched()->count(),
            'shortage_qty' => 0.0, 'excess_qty' => 0.0, 'shortage_value' => 0.0, 'excess_value' => 0.0,
            'hidden_variance' => 0, 'resolved' => 0,
        ];

        foreach ($items as $i) {
            if ($i->status !== 'NOT_COUNTED') $s['counted']++;
            $key = ['MATCHED' => 'matched', 'SHORTAGE' => 'shortage', 'EXCESS' => 'excess',
                    'NOT_FOUND' => 'not_found', 'NOT_COUNTED' => 'not_counted'][$i->status] ?? null;
            if ($key) $s[$key]++;

            if ($i->variance !== null) {
                if ($i->variance < -self::TOL) {
                    $s['shortage_qty'] += abs($i->variance);
                    $s['shortage_value'] += abs($i->variance) * $i->purchase_price;
                    if ($i->status === 'MATCHED') $s['hidden_variance']++;
                } elseif ($i->variance > self::TOL) {
                    $s['excess_qty'] += $i->variance;
                    $s['excess_value'] += $i->variance * $i->purchase_price;
                }
            }
            if (in_array($i->resolution, ['confirmed', 'adjusted', 'receipt_corrected'], true)) $s['resolved']++;
        }

        // shortages and excess are reported separately and NEVER netted off
        foreach (['shortage_qty', 'excess_qty', 'shortage_value', 'excess_value'] as $k) $s[$k] = round($s[$k], 2);
        $s['percent'] = $s['total'] ? round($s['counted'] / $s['total'] * 100) : 0;

        return $s;
    }

    public function group(StockTakeItem $i): ?string
    {
        if ($i->status === 'NOT_COUNTED') return 'not_counted';
        if ($i->status === 'NOT_FOUND') return 'not_found';
        if ($i->variance !== null && $i->variance < -self::TOL) return 'shortage';
        if ($i->variance !== null && $i->variance > self::TOL) return 'excess';
        if (in_array($i->status, ['SHORTAGE', 'EXCESS'], true)) return 'system_drift'; // shelf matches expected, system does not
        return null; // fully matched
    }

    public function report(StockTake $take): array
    {
        $rows = [];
        foreach ($take->items()->orderBy('product_name')->get() as $i) {
            $g = $this->group($i);
            if ($g) $rows[] = $this->itemRow($i, $g);
        }

        usort($rows, fn ($a, $b) => abs($b['value']) <=> abs($a['value']));

        return [
            'summary'   => $this->summary($take),
            'rows'      => $rows,
            'unmatched' => $take->unmatched()->orderBy('name')->get(),
        ];
    }

    public function itemRow(StockTakeItem $i, ?string $group = null): array
    {
        $rec = $this->recommend($i);
        return [
            'id' => $i->id, 'bidhaa_id' => $i->bidhaa_id, 'name' => $i->product_name, 'unit' => $i->unit,
            'category' => $i->category, 'barcode' => $i->barcode,
            'previous_qty' => $i->previous_qty, 'previous_counted_at' => $i->previous_counted_at?->format('d/m/Y'),
            'received' => $i->received_qty, 'sold' => $i->sold_qty,
            'returns_in' => $i->returns_in_qty, 'returns_out' => $i->returns_out_qty, 'adjustments' => $i->adjustments_qty,
            'system' => $i->system_qty, 'expected' => $i->expected_qty, 'physical' => $i->physical_qty,
            'is_unavailable' => $i->is_unavailable,
            'diff_system' => $i->diff_system, 'diff_expected' => $i->diff_expected, 'variance' => $i->variance,
            'purchase_price' => $i->purchase_price,
            'value' => $i->variance !== null ? round($i->variance * $i->purchase_price, 2) : 0, // negative = shortage
            'status' => $i->status, 'group' => $group ?? $this->group($i),
            'resolution' => $i->resolution, 'resolution_note' => $i->resolution_note,
            'counted_at' => $i->counted_at?->format('d/m/Y H:i'), 'counted_by' => $i->counted_by,
            'adjusted' => (bool) $i->adjustment_id,
            'action' => $rec['action'], 'recommendation' => $rec['text'], 'cause' => $rec['cause'],
        ];
    }

    /** Intelligent, plain-language explanation + suggested next step. */
    public function recommend(StockTakeItem $i): array
    {
        $f = fn ($n) => rtrim(rtrim(number_format((float) $n, 2, '.', ''), '0'), '.');
        $u = $i->unit ? ' ' . $i->unit : '';
        $sys = (float) $i->system_qty;
        $ph = $i->physical_qty;
        $exp = $i->expected_qty;
        $v = $i->variance;

        if ($i->status === 'NOT_COUNTED') {
            return ['action' => 'count', 'cause' => null,
                'text' => 'Not counted yet. Complete its physical verification before closing the exercise.'];
        }

        // WHY does the difference exist?
        if ($exp === null) {
            $cause = 'No earlier verified count exists, so only system vs physical can be compared. This count becomes the baseline for the next session.';
        } elseif (abs($ph - $exp) <= self::TOL && abs($sys - $ph) > self::TOL) {
            $cause = "The shelf matches what the last verified count plus recorded purchases and sales predicts ({$f($exp)}{$u}). The system quantity ({$f($sys)}) has drifted, for example through a manual edit, an import or an unrecorded adjustment. This is probably not a stock loss.";
        } elseif (abs($sys - $exp) <= self::TOL && abs($ph - $sys) > self::TOL) {
            $cause = "System and recorded movements agree ({$f($exp)}{$u}) but the shelf does not. The gap is on the physical side: unrecorded sale, damage, theft or a counting error.";
        } elseif (abs($ph - $exp) > self::TOL) {
            $cause = "Neither the system ({$f($sys)}) nor the shelf ({$f($ph)}) matches the expected quantity ({$f($exp)}{$u}). Check sales, purchases, returns and manual edits since the last count on " . ($i->previous_counted_at?->format('d/m/Y') ?? 'the previous date') . '.';
        } else {
            $cause = null;
        }

        if ($i->status === 'NOT_FOUND') {
            return ['action' => 'investigate', 'cause' => $cause,
                'text' => "Recorded as {$f($sys)}{$u} but not found physically. Investigate a possible loss, unrecorded sale or stock movement, and recount the store room before concluding."];
        }
        if ($ph > self::TOL && $sys <= self::TOL) {
            return ['action' => 'correct_receipt', 'cause' => $cause,
                'text' => "{$f($ph)}{$u} found physically but the system shows zero. Investigate unrecorded purchases or stock receipts."];
        }
        if ($ph <= self::TOL && $sys > self::TOL) {
            return ['action' => 'investigate', 'cause' => $cause,
                'text' => "Recorded as {$f($sys)}{$u} but physical stock is zero. Investigate a possible loss, unrecorded sale or stock movement."];
        }
        if ($i->status === 'SHORTAGE' || $i->status === 'EXCESS') {
            $d = $f(abs($i->diff_system));
            $verb = $i->status === 'SHORTAGE' ? 'short' : 'over';
            return ['action' => $v !== null && abs($v) <= self::TOL ? 'adjust' : 'review', 'cause' => $cause,
                'text' => "Difference of {$d}{$u} ({$verb}). Review sales, purchases, returns and adjustments since the previous stock taking."];
        }
        if ($v !== null && $v < -self::TOL) {
            return ['action' => 'investigate', 'cause' => $cause,
                'text' => 'Matches the system, but is ' . $f(abs($v)) . "{$u} below the expected quantity. The system may be hiding a variance; review movements since the previous count."];
        }
        if ($v !== null && $v > self::TOL) {
            return ['action' => 'review', 'cause' => $cause,
                'text' => 'Matches the system, but is ' . $f($v) . "{$u} above the expected quantity. Look for unrecorded receipts or sales recorded against the wrong product."];
        }
        return ['action' => 'confirm', 'cause' => $cause, 'text' => 'Matches. Confirm the count.'];
    }

    /* ------------------------------------------------------------------ */
    /* Reconciliation / approval                                           */
    /* ------------------------------------------------------------------ */

    /**
     * Apply a reconciliation decision to one or more items.
     * Only 'adjusted' and 'receipt_corrected' change stock, and they require a note.
     *
     * The adjustment applies the COUNT DELTA (physical - system at the moment of counting)
     * to the live quantity, so sales made after the count are never overwritten.
     */
    public function resolve(StockTake $take, array $itemIds, string $action, ?string $note, array $actor): array
    {
        if (!in_array($action, self::RESOLUTIONS, true)) {
            throw new \InvalidArgumentException('Unknown action.');
        }
        if (in_array($action, ['adjusted', 'receipt_corrected'], true) && !trim((string) $note)) {
            throw new \InvalidArgumentException('A reason is required for a stock adjustment.');
        }

        $done = 0;
        $skipped = [];

        DB::transaction(function () use ($take, $itemIds, $action, $note, $actor, &$done, &$skipped) {
            $items = StockTakeItem::where('stock_take_id', $take->id)->whereIn('id', $itemIds)->lockForUpdate()->get();

            foreach ($items as $item) {
                if ($action === 'recount') {
                    if ($item->adjustment_id) { $skipped[] = "{$item->product_name}: already adjusted."; continue; }
                    $this->resetItem($item);
                    $item->update(['resolution' => 'recount', 'resolution_note' => $note,
                                   'resolved_by' => $actor['name'], 'resolved_at' => now()]);
                    $done++;
                    continue;
                }

                if (in_array($action, ['adjusted', 'receipt_corrected'], true)) {
                    if ($item->adjustment_id) { $skipped[] = "{$item->product_name}: already adjusted."; continue; }
                    if ($item->status === 'NOT_COUNTED' || $item->physical_qty === null) { $skipped[] = "{$item->product_name}: not counted."; continue; }

                    $delta = round($item->physical_qty - $item->system_qty, 2);
                    if (abs($delta) <= self::TOL) { $skipped[] = "{$item->product_name}: nothing to adjust."; continue; }
                    if ($action === 'receipt_corrected' && $delta < 0) { $skipped[] = "{$item->product_name}: a receipt correction needs an excess."; continue; }

                    $bidhaa = Bidhaa::where('id', $item->bidhaa_id)->where('company_id', $take->company_id)->lockForUpdate()->first();
                    if (!$bidhaa) { $skipped[] = "{$item->product_name}: product no longer exists."; continue; }

                    $old = (float) $bidhaa->idadi;
                    $new = max(0, round($old + $delta, 2));

                    $bidhaa->update(['idadi' => $new, 'updated_by' => $actor['name']]); // model hook writes stock history

                    $adj = StockAdjustment::create([
                        'company_id' => $take->company_id, 'bidhaa_id' => $bidhaa->id,
                        'stock_take_id' => $take->id, 'stock_take_item_id' => $item->id,
                        'type' => $action === 'receipt_corrected' ? 'receipt_correction' : 'count_correction',
                        'affects_expected' => false,
                        'old_qty' => $old, 'new_qty' => $new, 'difference' => round($new - $old, 2),
                        'reason' => $note, 'user_id' => $actor['id'], 'user_guard' => $actor['guard'], 'user_name' => $actor['name'],
                    ]);

                    $item->update(['resolution' => $action, 'resolution_note' => $note, 'adjustment_id' => $adj->id,
                                   'resolved_by' => $actor['name'], 'resolved_at' => now()]);
                    $done++;
                    continue;
                }

                // confirmed | investigating | sale_investigation | unresolved  (no stock change)
                $item->update(['resolution' => $action, 'resolution_note' => $note,
                               'resolved_by' => $actor['name'], 'resolved_at' => now()]);
                $done++;
            }
        });

        return ['done' => $done, 'skipped' => $skipped];
    }

    public function close(StockTake $take, array $actor, bool $force): StockTake
    {
        $summary = $this->summary($take);
        if ($summary['counted'] === 0) {
            throw new \DomainException('Nothing has been counted yet.');
        }
        if ($summary['not_counted'] > 0 && !$force) {
            throw new \DomainException('uncounted:' . $summary['not_counted']);
        }

        // Freeze the stock value comparison (system vs counted, purchase price) into the history snapshot
        $summary['value_comparison'] = $this->valueComparison($take);

        $take->update([
            'status' => 'closed', 'closed_at' => now(), 'closed_by_name' => $actor['name'], 'summary' => $summary,
        ]);

        return $take->fresh();
    }

    /* ------------------------------------------------------------------ */
    /* Stock value comparison + "Update Stock to Current Count"            */
    /* ------------------------------------------------------------------ */

    /**
     * Value comparison for one session, computed from the frozen item snapshots
     * (system_qty / physical_qty at count time), NEVER from live bidhaas.idadi.
     * Counted items only (physical_qty not null); purchase price, not selling price.
     */
    public function valueComparison(StockTake $take): array
    {
        $rows = $take->items()->get(['system_qty', 'physical_qty', 'purchase_price']);

        $sys = 0.0; $cnt = 0.0; $uncountedValue = 0.0;
        $counted = 0; $uncounted = 0;

        foreach ($rows as $i) {
            $pp = (float) $i->purchase_price;
            if ($i->physical_qty === null) {
                $uncounted++;
                $uncountedValue += (float) $i->system_qty * $pp;
                continue;
            }
            $counted++;
            $sys += (float) $i->system_qty * $pp;
            $cnt += (float) $i->physical_qty * $pp;
        }

        $diff = round($cnt - $sys, 2);

        return [
            'system_value'         => round($sys, 2),
            'counted_value'        => round($cnt, 2),
            'difference'           => $diff,
            'is_loss'              => $diff < 0,
            'is_gain'              => $diff > 0,
            'counted_items'        => $counted,
            'uncounted_items'      => $uncounted,
            'uncounted_system_value' => round($uncountedValue, 2),
        ];
    }

    /** Batch version for the history list: take_id => comparison array. */
    public function valueComparisonFor($takes): array
    {
        $ids = collect($takes)->pluck('id')->all();
        if (empty($ids)) return [];

        $agg = StockTakeItem::whereIn('stock_take_id', $ids)
            ->groupBy('stock_take_id')
            ->selectRaw('stock_take_id,
                SUM(CASE WHEN physical_qty IS NULL THEN 0 ELSE system_qty * purchase_price END) AS sys_val,
                SUM(CASE WHEN physical_qty IS NULL THEN 0 ELSE physical_qty * purchase_price END) AS cnt_val,
                SUM(CASE WHEN physical_qty IS NULL THEN system_qty * purchase_price ELSE 0 END) AS unc_val,
                SUM(CASE WHEN physical_qty IS NULL THEN 1 ELSE 0 END) AS unc_n,
                SUM(CASE WHEN physical_qty IS NULL THEN 0 ELSE 1 END) AS cnt_n')
            ->get()->keyBy('stock_take_id');

        $out = [];
        foreach ($ids as $id) {
            $a = $agg->get($id);
            $sys = round((float) ($a->sys_val ?? 0), 2);
            $cnt = round((float) ($a->cnt_val ?? 0), 2);
            $diff = round($cnt - $sys, 2);
            $out[$id] = [
                'system_value'           => $sys,
                'counted_value'          => $cnt,
                'difference'             => $diff,
                'is_loss'                => $diff < 0,
                'is_gain'                => $diff > 0,
                'counted_items'          => (int) ($a->cnt_n ?? 0),
                'uncounted_items'        => (int) ($a->unc_n ?? 0),
                'uncounted_system_value' => round((float) ($a->unc_val ?? 0), 2),
            ];
        }

        return $out;
    }

    /**
     * "Update Stock to Current Count": replace live bidhaas.idadi with the counted
     * quantities of a CLOSED session (including zeros). Never automatic — only via
     * this explicit, confirmed action. Freezes the session as applied afterwards.
     *
     * The item snapshots keep the previous system quantities and the comparison
     * results, so the complete history is preserved.
     */
    public function applyToStock(StockTake $take, array $actor): array
    {
        return DB::transaction(function () use ($take, $actor) {
            $locked = StockTake::whereKey($take->id)->lockForUpdate()->firstOrFail();

            if ($locked->status !== 'closed') {
                throw new \DomainException('Only a closed stock taking can be applied to stock.');
            }
            if ($locked->applied_at) {
                throw new \DomainException('This stock taking was already applied to stock on ' . $locked->applied_at->format('d/m/Y H:i') . ' by ' . $locked->applied_by_name . '.');
            }

            $items = $locked->items()->whereNotNull('physical_qty')->lockForUpdate()->get();
            if ($items->isEmpty()) {
                throw new \DomainException('No counted products to apply.');
            }

            $updated = 0; $missing = 0;
            foreach ($items as $item) {
                $bidhaa = Bidhaa::where('id', $item->bidhaa_id)->where('company_id', $locked->company_id)->lockForUpdate()->first();
                if (!$bidhaa) { $missing++; continue; }

                $new = max(0, round((float) $item->physical_qty, 2));
                $bidhaa->update(['idadi' => $new, 'updated_by' => $actor['name']]); // model hook writes stock history
                $updated++;
            }

            $locked->update(['applied_at' => now(), 'applied_by_name' => $actor['name']]);

            return ['updated' => $updated, 'missing' => $missing];
        });
    }

    /* ------------------------------------------------------------------ */
    /* Comparison with an earlier session                                  */
    /* ------------------------------------------------------------------ */

    public function compare(StockTake $cur, StockTake $prev): array
    {
        $old = $prev->items()->get()->keyBy('bidhaa_id');
        $rows = [];
        $groups = ['resolved' => 0, 'improved' => 0, 'worsened' => 0, 'new_shortage' => 0, 'now_excess' => 0,
                   'now_missing' => 0, 'unchanged' => 0, 'not_comparable' => 0];

        foreach ($cur->items()->get() as $c) {
            $p = $old->get($c->bidhaa_id);
            if (!$p) continue; // product did not exist in the earlier session

            $pv = $p->counted_at ? $p->variance : null;
            $cv = $c->counted_at ? $c->variance : null;
            $T = self::TOL;
            $cat = 'unchanged';

            if ($pv === null || $cv === null) {
                $cat = 'not_comparable';
            } else {
                $ps = $pv < -$T; $cs = $cv < -$T;
                if ($ps && abs($cv) <= $T)       $cat = 'resolved';
                elseif ($ps && $cv > $T)         $cat = 'now_excess';
                elseif ($ps && $cs)              $cat = $cv > $pv + $T ? 'improved' : ($cv < $pv - $T ? 'worsened' : 'unchanged');
                elseif (!$ps && $cs)             $cat = 'new_shortage';
                elseif ($cv > $T && $pv <= $T)   $cat = 'now_excess';
            }

            $nowMissing = $p->counted_at && $p->physical_qty > $T && ($c->is_unavailable || ($c->counted_at && $c->physical_qty <= $T));

            $groups[$cat]++;
            if ($nowMissing) $groups['now_missing']++;

            $rows[] = [
                'name' => $c->product_name, 'unit' => $c->unit, 'category' => $cat, 'now_missing' => $nowMissing,
                'prev_physical' => $p->physical_qty, 'prev_variance' => $pv, 'cur_physical' => $c->physical_qty,
                'cur_variance' => $cv, 'received' => $c->received_qty, 'sold' => $c->sold_qty,
                'expected' => $c->expected_qty,
                'change' => ($pv !== null && $cv !== null) ? round($cv - $pv, 2) : null,
                'purchase_price' => $c->purchase_price,
            ];
        }

        $ps = $prev->summary ?: $this->summary($prev);
        $cs = $this->summary($cur);
        $delta = $cs['shortage_value'] - $ps['shortage_value'];
        $base = max($ps['shortage_value'], 1);
        $verdict = abs($delta) / $base <= 0.02 ? 'unchanged' : ($delta < 0 ? 'improved' : 'deteriorated');

        return ['rows' => $rows, 'groups' => $groups, 'previous' => $ps, 'current' => $cs,
                'shortage_value_change' => round($delta, 2), 'verdict' => $verdict];
    }
}
