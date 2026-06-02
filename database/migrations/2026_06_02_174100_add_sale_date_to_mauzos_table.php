<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('mauzos', function (Blueprint $table) {
            $table->timestamp('sale_date')->nullable()->after('created_at');
            $table->index('sale_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mauzos', function (Blueprint $table) {
            $table->dropIndex(['sale_date']);
            $table->dropColumn('sale_date');
        });
    }
};