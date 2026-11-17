<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A barcode may be used by different companies, but only once inside the
     * same company. Replace the old global unique index with a composite one.
     */
    public function up(): void
    {
        $this->dropIndex('bidhaas', 'bidhaas_barcode_unique');

        if ($this->hasIndex('bidhaas', 'bidhaas_company_barcode_unique')) {
            return;
        }

        // The previous global unique index guarantees there are no duplicates.
        // Guard anyway so a manually edited database cannot break the deploy.
        $hasDuplicates = DB::table('bidhaas')
            ->select('company_id', 'barcode')
            ->whereNotNull('barcode')
            ->groupBy('company_id', 'barcode')
            ->havingRaw('COUNT(*) > 1')
            ->limit(1)
            ->get()
            ->isNotEmpty();

        if ($hasDuplicates) {
            Log::warning('Skipped bidhaas company/barcode unique index: duplicate barcodes exist inside a company.');
            return;
        }

        Schema::table('bidhaas', function (Blueprint $table) {
            $table->unique(['company_id', 'barcode'], 'bidhaas_company_barcode_unique');
        });
    }

    public function down(): void
    {
        $this->dropIndex('bidhaas', 'bidhaas_company_barcode_unique');

        if (!$this->hasIndex('bidhaas', 'bidhaas_barcode_unique')) {
            Schema::table('bidhaas', function (Blueprint $table) {
                $table->unique('barcode', 'bidhaas_barcode_unique');
            });
        }
    }

    private function hasIndex(string $table, string $indexName): bool
    {
        foreach (Schema::getIndexes($table) as $index) {
            if (($index['name'] ?? null) === $indexName) {
                return true;
            }
        }

        return false;
    }

    private function dropIndex(string $table, string $indexName): void
    {
        if (!$this->hasIndex($table, $indexName)) {
            return;
        }

        try {
            Schema::table($table, function (Blueprint $blueprint) use ($indexName) {
                $blueprint->dropUnique($indexName);
            });
        } catch (\Throwable $e) {
            // Fallback for MySQL variants that reject the fluent drop.
            DB::statement("ALTER TABLE `{$table}` DROP INDEX `{$indexName}`");
        }
    }
};
