<?php
// database/migrations/2026_06_09_000001_update_orders_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // First, update existing status values to match new ENUM
        // Map old statuses to new ones
        DB::statement("UPDATE orders SET status = 'saved' WHERE status = 'draft' OR status = 'pending'");
        DB::statement("UPDATE orders SET status = 'confirmed' WHERE status = 'confirmed'");
        DB::statement("UPDATE orders SET status = 'paid' WHERE status = 'paid'");
        DB::statement("UPDATE orders SET status = 'cancelled' WHERE status = 'cancelled'");
        
        // Now modify the column - use raw SQL to avoid issues
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('saved', 'confirmed', 'paid', 'cancelled') NOT NULL DEFAULT 'saved'");
        
        // Add new columns
        if (!Schema::hasColumn('orders', 'transferred_to_cart')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->boolean('transferred_to_cart')->default(false);
            });
        }
        
        if (!Schema::hasColumn('orders', 'transferred_at')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->timestamp('transferred_at')->nullable();
            });
        }
        
        if (!Schema::hasColumn('orders', 'payment_reference')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('payment_reference')->nullable();
            });
        }
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['transferred_to_cart', 'transferred_at', 'payment_reference']);
        });
        
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('draft', 'confirmed', 'paid', 'cancelled') NOT NULL DEFAULT 'draft'");
    }
};