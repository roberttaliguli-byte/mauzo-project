<?php
// database/migrations/2024_06_16_000000_add_columns_to_orders_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Add missing columns
            $table->decimal('delivery_fee', 15, 2)->default(0)->after('discount_type');
            $table->decimal('tax', 15, 2)->default(0)->after('delivery_fee');
            $table->string('order_type')->default('delivery')->after('status');
            $table->string('table_number')->nullable()->after('order_type');
            $table->text('special_instructions')->nullable()->after('table_number');
            $table->text('delivery_address')->nullable()->after('special_instructions');
            
            // Add status tracking timestamps
            $table->timestamp('confirmed_at')->nullable()->after('paid_at');
            $table->timestamp('processing_at')->nullable()->after('confirmed_at');
            $table->timestamp('ready_at')->nullable()->after('processing_at');
            $table->timestamp('shipped_at')->nullable()->after('ready_at');
            $table->timestamp('delivered_at')->nullable()->after('shipped_at');
            $table->timestamp('cancelled_at')->nullable()->after('delivered_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'delivery_fee',
                'tax',
                'order_type',
                'table_number',
                'special_instructions',
                'delivery_address',
                'confirmed_at',
                'processing_at',
                'ready_at',
                'shipped_at',
                'delivered_at',
                'cancelled_at'
            ]);
        });
    }
};