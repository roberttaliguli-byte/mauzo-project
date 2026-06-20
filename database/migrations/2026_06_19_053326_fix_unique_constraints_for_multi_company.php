<?php
// database/migrations/2026_06_19_000005_fix_unique_constraints_for_multi_company.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
       
        // 1. Fix orders table - remove unique on order_number
   
        Schema::table('orders', function (Blueprint $table) {
            // Check if the unique constraint exists and drop it
            $this->dropIndexIfExists('orders', 'orders_order_number_unique');
        });

        // Add composite unique key for company_id + order_number
        Schema::table('orders', function (Blueprint $table) {
            $table->unique(['company_id', 'order_number'], 'orders_company_order_unique');
        });

 
        // 2. Fix mtejas table - remove unique on customer_code
     
        Schema::table('mtejas', function (Blueprint $table) {
            // Check if the unique constraint exists and drop it
            $this->dropIndexIfExists('mtejas', 'mtejas_customer_code_unique');
        });

        // Add composite unique key for company_id + customer_code
        Schema::table('mtejas', function (Blueprint $table) {
            $table->unique(['company_id', 'customer_code'], 'mtejas_company_customer_unique');
        });

        
        // 3. Clean up any existing duplicate data
        
        $this->cleanDuplicateOrders();
        $this->cleanDuplicateCustomers();
    }

    public function down(): void
    {
        // Rollback orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique('orders_company_order_unique');
            $table->unique('order_number', 'orders_order_number_unique');
        });

        // Rollback mtejas table
        Schema::table('mtejas', function (Blueprint $table) {
            $table->dropUnique('mtejas_company_customer_unique');
            $table->unique('customer_code', 'mtejas_customer_code_unique');
        });
    }

    /**
     * Drop index if it exists
     */
    private function dropIndexIfExists($table, $indexName)
    {
        try {
            Schema::table($table, function (Blueprint $table) use ($indexName) {
                $table->dropUnique($indexName);
            });
        } catch (\Exception $e) {
            // Index might not exist, continue
        }
    }

    /**
     * Clean duplicate order numbers (keep the oldest)
     */
    private function cleanDuplicateOrders()
    {
        // Find duplicates
        $duplicates = DB::table('orders')
            ->select('company_id', 'order_number', DB::raw('COUNT(*) as count'))
            ->groupBy('company_id', 'order_number')
            ->having('count', '>', 1)
            ->get();

        foreach ($duplicates as $dup) {
            // Get all IDs for this duplicate group, keep the smallest ID
            $ids = DB::table('orders')
                ->where('company_id', $dup->company_id)
                ->where('order_number', $dup->order_number)
                ->orderBy('id')
                ->pluck('id')
                ->toArray();

            // Keep first, delete rest
            $keepId = array_shift($ids);
            if (!empty($ids)) {
                DB::table('orders')
                    ->whereIn('id', $ids)
                    ->delete();
            }
        }
    }

    /**
     * Clean duplicate customer codes (keep the oldest)
     */
    private function cleanDuplicateCustomers()
    {
        // Find duplicates
        $duplicates = DB::table('mtejas')
            ->select('company_id', 'customer_code', DB::raw('COUNT(*) as count'))
            ->whereNotNull('customer_code')
            ->groupBy('company_id', 'customer_code')
            ->having('count', '>', 1)
            ->get();

        foreach ($duplicates as $dup) {
            // Get all IDs for this duplicate group, keep the smallest ID
            $ids = DB::table('mtejas')
                ->where('company_id', $dup->company_id)
                ->where('customer_code', $dup->customer_code)
                ->orderBy('id')
                ->pluck('id')
                ->toArray();

            // Keep first, delete rest
            $keepId = array_shift($ids);
            if (!empty($ids)) {
                DB::table('mtejas')
                    ->whereIn('id', $ids)
                    ->delete();
            }
        }
    }
};