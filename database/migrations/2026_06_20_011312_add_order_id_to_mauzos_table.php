<?php
// database/migrations/2026_06_20_000000_add_order_id_to_mauzos.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mauzos', function (Blueprint $table) {
            if (!Schema::hasColumn('mauzos', 'order_id')) {
                $table->foreignId('order_id')->nullable()->after('company_id')
                    ->constrained('orders')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('mauzos', 'order_number')) {
                $table->string('order_number')->nullable()->after('order_id');
            }
            
            $table->index('order_id');
            $table->index('order_number');
        });
    }

    public function down(): void
    {
        Schema::table('mauzos', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropColumn(['order_id', 'order_number']);
            $table->dropIndex(['order_id']);
            $table->dropIndex(['order_number']);
        });
    }
};