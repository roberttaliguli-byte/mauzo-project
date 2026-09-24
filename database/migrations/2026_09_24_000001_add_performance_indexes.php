<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Add composite indexes for high-volume companies.
     * Safe: only adds indexes, no data mutation, idempotent via hasIndex check.
     */
    public function up(): void
    {
        // Helper to avoid duplicate index creation (MySQL)
        $hasIndex = function (string $table, string $index) : bool {
            try {
                $db = DB::getDatabaseName();
                $exists = DB::select(
                    "SELECT 1 FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ? LIMIT 1",
                    [$db, $table, $index]
                );
                return !empty($exists);
            } catch (\Throwable $e) {
                return false;
            }
        };

        // mauzos: company_id + created_at (today dashboards), company_id+bidaa_id, company_id+receipt_no, company_id+mteja_id
        Schema::table('mauzos', function (Blueprint $table) use ($hasIndex) {
            if (Schema::hasTable('mauzos')) {
                if (!$hasIndex('mauzos', 'mauzos_company_created_idx')) {
                    $table->index(['company_id', 'created_at'], 'mauzos_company_created_idx');
                }
                if (!$hasIndex('mauzos', 'mauzos_company_bidhaa_idx')) {
                    $table->index(['company_id', 'bidhaa_id'], 'mauzos_company_bidhaa_idx');
                }
                if (!$hasIndex('mauzos', 'mauzos_company_receipt_idx')) {
                    $table->index(['company_id', 'receipt_no'], 'mauzos_company_receipt_idx');
                }
                if (Schema::hasColumn('mauzos', 'mteja_id') && !$hasIndex('mauzos', 'mauzos_company_mteja_idx')) {
                    $table->index(['company_id', 'mteja_id'], 'mauzos_company_mteja_idx');
                }
                // For whereDate optimization via range
                if (Schema::hasColumn('mauzos', 'sale_date') && !$hasIndex('mauzos', 'mauzos_company_sale_date_idx')) {
                    $table->index(['company_id', 'sale_date'], 'mauzos_company_sale_date_idx');
                }
            }
        });

        // manunuzis: company_id + created_at (range filters, today stats)
        Schema::table('manunuzis', function (Blueprint $table) use ($hasIndex) {
            if (Schema::hasTable('manunuzis') && Schema::hasColumn('manunuzis', 'company_id')) {
                if (!$hasIndex('manunuzis', 'manunuzis_company_created_idx')) {
                    $table->index(['company_id', 'created_at'], 'manunuzis_company_created_idx');
                }
                if (!$hasIndex('manunuzis', 'manunuzis_company_bidhaa_idx')) {
                    $table->index(['company_id', 'bidhaa_id'], 'manunuzis_company_bidhaa_idx');
                }
            }
        });

        // matumizis: company_id + created_at, company_id+aina (grouping)
        Schema::table('matumizis', function (Blueprint $table) use ($hasIndex) {
            if (Schema::hasTable('matumizis')) {
                if (!$hasIndex('matumizis', 'matumizis_company_created_idx')) {
                    $table->index(['company_id', 'created_at'], 'matumizis_company_created_idx');
                }
                if (!$hasIndex('matumizis', 'matumizis_company_aina_idx')) {
                    $table->index(['company_id', 'aina'], 'matumizis_company_aina_idx');
                }
            }
        });

        // madenis: company_id + baki (active/paid filter), company_id+created_at
        Schema::table('madenis', function (Blueprint $table) use ($hasIndex) {
            if (Schema::hasTable('madenis')) {
                if (!$hasIndex('madenis', 'madenis_company_baki_idx')) {
                    $table->index(['company_id', 'baki'], 'madenis_company_baki_idx');
                }
                if (!$hasIndex('madenis', 'madenis_company_created_idx')) {
                    $table->index(['company_id', 'created_at'], 'madenis_company_created_idx');
                }
                if (Schema::hasColumn('madenis', 'mteja_id') && !$hasIndex('madenis', 'madenis_company_mteja_idx')) {
                    $table->index(['company_id', 'mteja_id'], 'madenis_company_mteja_idx');
                }
            }
        });

        // marejeshos: company_id + tarehe (daily aggregates), company_id+lipa_kwa
        Schema::table('marejeshos', function (Blueprint $table) use ($hasIndex) {
            if (Schema::hasTable('marejeshos')) {
                if (!$hasIndex('marejeshos', 'marejeshos_company_tarehe_idx')) {
                    $table->index(['company_id', 'tarehe'], 'marejeshos_company_tarehe_idx');
                }
                if (!$hasIndex('marejeshos', 'marejeshos_company_created_idx')) {
                    $table->index(['company_id', 'created_at'], 'marejeshos_company_created_idx');
                }
                if (Schema::hasColumn('marejeshos', 'lipa_kwa') && !$hasIndex('marejeshos', 'marejeshos_company_lipa_idx')) {
                    $table->index(['company_id', 'lipa_kwa'], 'marejeshos_company_lipa_idx');
                }
            }
        });

        // bidhaas: company_id + idadi (stock filters), company_id+jina (search)
        Schema::table('bidhaas', function (Blueprint $table) use ($hasIndex) {
            if (Schema::hasTable('bidhaas')) {
                if (!$hasIndex('bidhaas', 'bidhaas_company_idadi_idx')) {
                    $table->index(['company_id', 'idadi'], 'bidhaas_company_idadi_idx');
                }
                if (!$hasIndex('bidhaas', 'bidhaas_company_jina_idx')) {
                    $table->index(['company_id', 'jina'], 'bidhaas_company_jina_idx');
                }
            }
        });

        // orders: company_id + created_at
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) use ($hasIndex) {
                if (!$hasIndex('orders', 'orders_company_created_idx')) {
                    $table->index(['company_id', 'created_at'], 'orders_company_created_idx');
                }
                if (!$hasIndex('orders', 'orders_company_status_idx')) {
                    $table->index(['company_id', 'status'], 'orders_company_status_idx');
                }
            });
        }

        // activity_logs & login_histories
        if (Schema::hasTable('activity_logs')) {
            Schema::table('activity_logs', function (Blueprint $table) use ($hasIndex) {
                if (!$hasIndex('activity_logs', 'activity_logs_company_created_idx')) {
                    $table->index(['company_id', 'created_at'], 'activity_logs_company_created_idx');
                }
            });
        }
        if (Schema::hasTable('login_histories')) {
            Schema::table('login_histories', function (Blueprint $table) use ($hasIndex) {
                if (!$hasIndex('login_histories', 'login_histories_company_login_idx')) {
                    $table->index(['company_id', 'login_at'], 'login_histories_company_login_idx');
                }
            });
        }
    }

    public function down(): void
    {
        // Safe rollback: drop if exists
        $drop = function (string $table, string $index) {
            try {
                Schema::table($table, function (Blueprint $t) use ($index) {
                    $t->dropIndex($index);
                });
            } catch (\Throwable $e) {
                // ignore
            }
        };
        $drop('mauzos', 'mauzos_company_created_idx');
        $drop('mauzos', 'mauzos_company_bidhaa_idx');
        $drop('mauzos', 'mauzos_company_receipt_idx');
        $drop('mauzos', 'mauzos_company_mteja_idx');
        $drop('mauzos', 'mauzos_company_sale_date_idx');
        $drop('manunuzis', 'manunuzis_company_created_idx');
        $drop('manunuzis', 'manunuzis_company_bidhaa_idx');
        $drop('matumizis', 'matumizis_company_created_idx');
        $drop('matumizis', 'matumizis_company_aina_idx');
        $drop('madenis', 'madenis_company_baki_idx');
        $drop('madenis', 'madenis_company_created_idx');
        $drop('madenis', 'madenis_company_mteja_idx');
        $drop('marejeshos', 'marejeshos_company_tarehe_idx');
        $drop('marejeshos', 'marejeshos_company_created_idx');
        $drop('marejeshos', 'marejeshos_company_lipa_idx');
        $drop('bidhaas', 'bidhaas_company_idadi_idx');
        $drop('bidhaas', 'bidhaas_company_jina_idx');
        $drop('orders', 'orders_company_created_idx');
        $drop('orders', 'orders_company_status_idx');
        $drop('activity_logs', 'activity_logs_company_created_idx');
        $drop('login_histories', 'login_histories_company_login_idx');
    }
};
