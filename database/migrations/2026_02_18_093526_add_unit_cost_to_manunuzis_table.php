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
        Schema::table('manunuzis', function (Blueprint $table) {
            // Add unit_cost column after bei
            $table->decimal('unit_cost', 10, 2)->after('bei')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('manunuzis', function (Blueprint $table) {
            $table->dropColumn('unit_cost');
        });
    }
};