<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Archive columns on the existing table (nothing existing is changed or reset)
        Schema::table('bidhaas', function (Blueprint $t) {
            if (!Schema::hasColumn('bidhaas', 'archived_at')) {
                $t->timestamp('archived_at')->nullable()->index();
                $t->string('archived_by')->nullable();
                $t->string('archive_reason')->nullable();
            }
        });

        Schema::create('stock_takes', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('company_id')->index();
            $t->string('reference')->index();
            $t->string('title')->nullable();
            $t->string('status', 20)->default('counting')->index(); // counting | closed | cancelled
            $t->unsignedBigInteger('previous_take_id')->nullable();
            $t->unsignedBigInteger('started_by_id')->nullable();
            $t->string('started_by_guard', 20)->nullable();
            $t->string('started_by_name')->nullable();
            $t->timestamp('started_at')->nullable();
            $t->string('closed_by_name')->nullable();
            $t->timestamp('closed_at')->nullable();
            $t->text('notes')->nullable();
            $t->json('summary')->nullable(); // frozen snapshot written at close
            $t->timestamps();
        });

        Schema::create('stock_take_items', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('stock_take_id')->index();
            $t->unsignedBigInteger('company_id')->index();
            $t->unsignedBigInteger('bidhaa_id')->index();
            // snapshots so history survives later product edits
            $t->string('product_name');
            $t->string('unit')->nullable();
            $t->string('category')->nullable();
            $t->string('barcode')->nullable();
            $t->decimal('purchase_price', 14, 2)->default(0);
            $t->decimal('sell_price', 14, 2)->default(0);

            $t->decimal('system_qty', 14, 2)->default(0);   // system qty at the moment of counting
            $t->decimal('physical_qty', 14, 2)->nullable(); // null = NOT COUNTED
            $t->boolean('is_unavailable')->default(false);
            $t->decimal('previous_qty', 14, 2)->nullable();  // last verified physical qty
            $t->timestamp('previous_counted_at')->nullable();
            $t->decimal('received_qty', 14, 2)->default(0);
            $t->decimal('sold_qty', 14, 2)->default(0);
            $t->decimal('returns_in_qty', 14, 2)->default(0);
            $t->decimal('returns_out_qty', 14, 2)->default(0);
            $t->decimal('adjustments_qty', 14, 2)->default(0);
            $t->decimal('expected_qty', 14, 2)->nullable();
            $t->decimal('diff_system', 14, 2)->nullable();   // physical - system
            $t->decimal('diff_expected', 14, 2)->nullable(); // physical - expected
            $t->decimal('variance', 14, 2)->nullable();      // diff_expected ?? diff_system
            $t->string('status', 30)->default('NOT_COUNTED')->index();
            $t->timestamp('counted_at')->nullable();
            $t->string('counted_by')->nullable();

            $t->string('resolution', 30)->nullable();
            $t->text('resolution_note')->nullable();
            $t->string('resolved_by')->nullable();
            $t->timestamp('resolved_at')->nullable();
            $t->unsignedBigInteger('adjustment_id')->nullable();
            $t->timestamps();

            $t->unique(['stock_take_id', 'bidhaa_id']);
        });

        Schema::create('stock_take_unmatched', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('stock_take_id')->index();
            $t->unsignedBigInteger('company_id')->index();
            $t->string('name');
            $t->string('unit')->nullable();
            $t->string('category')->nullable();
            $t->string('barcode')->nullable();
            $t->decimal('quantity', 14, 2)->default(0);
            $t->text('notes')->nullable();
            $t->string('created_by')->nullable();
            $t->timestamps();
        });

        Schema::create('stock_adjustments', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('company_id')->index();
            $t->unsignedBigInteger('bidhaa_id')->index();
            $t->unsignedBigInteger('stock_take_id')->nullable()->index();
            $t->unsignedBigInteger('stock_take_item_id')->nullable();
            $t->string('type', 30);                 // count_correction | receipt_correction | write_off | other
            $t->boolean('affects_expected')->default(false);
            $t->decimal('old_qty', 14, 2);
            $t->decimal('new_qty', 14, 2);
            $t->decimal('difference', 14, 2);
            $t->text('reason');
            $t->unsignedBigInteger('user_id')->nullable();
            $t->string('user_guard', 20)->nullable();
            $t->string('user_name')->nullable();
            $t->timestamps();
        });

        Schema::create('product_cleanup_logs', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('company_id')->index();
            $t->unsignedBigInteger('user_id')->nullable();
            $t->string('user_name')->nullable();
            $t->string('action', 20);               // delete | archive | restore
            $t->json('criteria')->nullable();
            $t->unsignedInteger('product_count')->default(0);
            $t->json('products')->nullable();       // id, name, barcode, qty snapshot
            $t->string('ip_address', 45)->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_cleanup_logs');
        Schema::dropIfExists('stock_adjustments');
        Schema::dropIfExists('stock_take_unmatched');
        Schema::dropIfExists('stock_take_items');
        Schema::dropIfExists('stock_takes');
        Schema::table('bidhaas', function (Blueprint $t) {
            $t->dropColumn(['archived_at', 'archived_by', 'archive_reason']);
        });
    }
};
