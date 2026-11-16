<?php

namespace App\Services;

use App\Models\Bidhaa;
use App\Models\BidhaaHistory;
use App\Models\Madeni;
use App\Models\Manunuzi;
use App\Models\Mauzo;
use App\Models\ProductCleanupLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Finds unnecessary products and removes / archives them safely.
 * A product with ANY dependent record is never hard-deleted; it can only be archived.
 */
class ProductCleanupService
{
    /** SQL for each cleanup condition (evaluated against the bidhaas row) */
    private function conditions(): array
    {
        $mauzo = (new Mauzo)->getTable();
        return [
            'zero_stock' => 'bidhaas.idadi = 0',
            'zero_price' => '(bidhaas.bei_nunua = 0 OR bidhaas.bei_nunua IS NULL)',
            'no_sales'   => "NOT EXISTS (SELECT 1 FROM `{$mauzo}` WHERE `{$mauzo}`.bidhaa_id = bidhaas.id)",
        ];
    }

    /** table => [column, label]. Tables that do not exist in your DB are skipped automatically. */
    public function dependencies(): array
    {
        $deps = [
            (new Mauzo)->getTable()     => ['bidhaa_id', 'Sales'],
            (new Manunuzi)->getTable()  => ['bidhaa_id', 'Purchases'],
            (new Madeni)->getTable()    => ['bidhaa_id', 'Debts / credit (and their repayments)'],
            'stock_take_items'          => ['bidhaa_id', 'Stock-taking reports'],
            'stock_adjustments'         => ['bidhaa_id', 'Stock adjustments'],
            'order_items'               => ['bidhaa_id', 'Customer orders'],
            // add your returns table here, e.g. 'mauzo_returns' => ['bidhaa_id', 'Returns'],
        ];

        return array_filter($deps, fn ($d, $t) => Schema::hasTable($t) && Schema::hasColumn($t, $d[0]), ARRAY_FILTER_USE_BOTH);
    }

    public function baseQuery(int $companyId, bool $archived = false): Builder
    {
        $q = Bidhaa::query()->where('bidhaas.company_id', $companyId)->select('bidhaas.*');
        $archived ? $q->whereNotNull('bidhaas.archived_at') : $q->whereNull('bidhaas.archived_at');

        foreach ($this->conditions() as $k => $sql) {
            $q->selectRaw("({$sql}) as c_{$k}");
        }
        foreach ($this->dependencies() as $table => [$col]) {
            $q->selectRaw("(SELECT COUNT(*) FROM `{$table}` WHERE `{$table}`.`{$col}` = bidhaas.id) as dep_{$table}");
        }

        $mauzo = (new Mauzo)->getTable();
        $q->selectRaw("(SELECT COALESCE(SUM(jumla),0) FROM `{$mauzo}` WHERE `{$mauzo}`.bidhaa_id = bidhaas.id) as sales_total");

        if (class_exists(BidhaaHistory::class)) {
            $h = (new BidhaaHistory)->getTable();
            // every product has one automatic "created" history row; more than one means real stock movement
            $q->selectRaw("(SELECT COUNT(*) FROM `{$h}` WHERE `{$h}`.bidhaa_id = bidhaas.id) as dep_history");
        }

        return $q;
    }

    /** Apply criteria. $match: any | all | multi (at least 2 of the selected / all 3 conditions) */
    public function applyCriteria(Builder $q, array $criteria, string $match = 'any'): Builder
    {
        $all = $this->conditions();
        $criteria = array_values(array_intersect($criteria, array_keys($all)));

        if ($match === 'multi') {
            $sum = implode(' + ', array_map(fn ($c) => "({$all[$c]})", $criteria ?: array_keys($all)));
            return $q->whereRaw("({$sum}) >= 2");
        }
        if (!$criteria) return $q->whereRaw('1 = 0'); // nothing selected => nothing listed

        $glue = $match === 'all' ? ' AND ' : ' OR ';
        return $q->whereRaw('(' . implode($glue, array_map(fn ($c) => "({$all[$c]})", $criteria)) . ')');
    }

    public function counts(int $companyId): array
    {
        $c = $this->conditions();
        $out = [];
        foreach ($c as $k => $sql) {
            $out[$k] = Bidhaa::where('company_id', $companyId)->whereNull('archived_at')->whereRaw($sql)->count();
        }
        $sum = implode(' + ', array_map(fn ($s) => "({$s})", $c));
        $out['multiple'] = Bidhaa::where('company_id', $companyId)->whereNull('archived_at')->whereRaw("({$sum}) >= 2")->count();
        $out['archived'] = Bidhaa::where('company_id', $companyId)->whereNotNull('archived_at')->count();
        $out['total'] = Bidhaa::where('company_id', $companyId)->whereNull('archived_at')->count();
        return $out;
    }

    /** Human-readable list of why a product is protected, or [] if it can be deleted. */
    public function protectedReasons($row): array
    {
        $reasons = [];
        foreach ($this->dependencies() as $table => [$col, $label]) {
            $n = (int) ($row->{"dep_{$table}"} ?? 0);
            if ($n > 0) $reasons[] = "{$label} ({$n})";
        }
        if ((int) ($row->dep_history ?? 0) > 1) $reasons[] = 'Stock movement history (' . ((int) $row->dep_history - 1) . ')';
        return $reasons;
    }

    public function mapRow($row): array
    {
        $reasons = $this->protectedReasons($row);
        return [
            'id' => $row->id, 'name' => $row->jina, 'category' => $row->aina, 'unit' => $row->kipimo, 'barcode' => $row->barcode,
            'created_at' => optional($row->created_at)->format('d/m/Y'),
            'stock' => (float) $row->idadi, 'buy' => (float) $row->bei_nunua, 'sell' => (float) $row->bei_kuuza,
            'sales_count' => (int) ($row->{'dep_' . (new Mauzo)->getTable()} ?? 0),
            'sales_total' => (float) $row->sales_total,
            'purchase_count' => (int) ($row->{'dep_' . (new Manunuzi)->getTable()} ?? 0),
            'flags' => array_keys(array_filter(['zero_stock' => $row->c_zero_stock, 'zero_price' => $row->c_zero_price, 'no_sales' => $row->c_no_sales])),
            'protected' => (bool) $reasons, 'reasons' => $reasons,
            'archived_at' => optional($row->archived_at)->format('d/m/Y H:i'),
            'archive_reason' => $row->archive_reason,
        ];
    }

    /** Classify ids server-side (never trust the browser). */
    public function classify(int $companyId, array $ids): array
    {
        $rows = $this->baseQuery($companyId)->whereIn('bidhaas.id', $ids)->get();
        $deletable = $protected = [];
        foreach ($rows as $r) {
            $m = $this->mapRow($r);
            $m['protected'] ? $protected[] = $m : $deletable[] = $m;
        }
        return ['deletable' => $deletable, 'protected' => $protected, 'found' => $rows->count()];
    }

    /**
     * @param string $action 'delete' (only products without history) | 'archive'
     */
    public function execute(array $actor, array $ids, string $action, array $criteria, string $ip, string $reason = ''): array
    {
        return DB::transaction(function () use ($actor, $ids, $action, $criteria, $ip, $reason) {
            $cid = $actor['company_id'];
            $rows = $this->baseQuery($cid)->whereIn('bidhaas.id', $ids)->lockForUpdate()->get();

            $done = [];
            $skipped = [];

            foreach ($rows as $row) {
                $m = $this->mapRow($row);
                $snap = ['id' => $row->id, 'name' => $row->jina, 'barcode' => $row->barcode, 'stock' => (float) $row->idadi,
                         'buy' => (float) $row->bei_nunua, 'sell' => (float) $row->bei_kuuza];

                if ($action === 'delete') {
                    if ($m['protected']) { $skipped[] = $row->jina . ' - has ' . implode(', ', $m['reasons']); continue; }
                    $product = Bidhaa::where('id', $row->id)->where('company_id', $cid)->first();
                    if (class_exists(BidhaaHistory::class)) {
                        DB::table((new BidhaaHistory)->getTable())->where('bidhaa_id', $row->id)->delete(); // only the automatic "created" row
                    }
                    $product->delete(); // model hook removes the image; there are no sales/debts/purchases to cascade
                    $done[] = $snap;
                } else {
                    Bidhaa::where('id', $row->id)->where('company_id', $cid)->update([
                        'archived_at' => now(), 'archived_by' => $actor['name'], 'archive_reason' => $reason ?: 'Product cleanup',
                    ]);
                    $done[] = $snap;
                }
            }

            if ($done) {
                ProductCleanupLog::create([
                    'company_id' => $cid, 'user_id' => $actor['id'], 'user_name' => $actor['name'],
                    'action' => $action, 'criteria' => $criteria, 'product_count' => count($done),
                    'products' => $done, 'ip_address' => $ip,
                ]);
            }

            return ['done' => count($done), 'skipped' => $skipped];
        });
    }

    public function restore(array $actor, array $ids, string $ip): int
    {
        return DB::transaction(function () use ($actor, $ids, $ip) {
            $products = Bidhaa::where('company_id', $actor['company_id'])->whereNotNull('archived_at')->whereIn('id', $ids)->get();
            foreach ($products as $p) {
                Bidhaa::where('id', $p->id)->update(['archived_at' => null, 'archived_by' => null, 'archive_reason' => null]);
            }
            if ($products->count()) {
                ProductCleanupLog::create([
                    'company_id' => $actor['company_id'], 'user_id' => $actor['id'], 'user_name' => $actor['name'],
                    'action' => 'restore', 'product_count' => $products->count(), 'ip_address' => $ip,
                    'products' => $products->map(fn ($p) => ['id' => $p->id, 'name' => $p->jina])->all(),
                ]);
            }
            return $products->count();
        });
    }
}
