<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('manunuzis', function (Blueprint $table) {
            $table->json('new_bidhaa')->nullable()->after('bidhaa_id');
            $table->decimal('bei_kuuza_draft', 15, 2)->nullable()->after('unit_cost');
        });

        // Draft rows for brand-new products have no bidhaa_id yet
        Schema::table('manunuzis', function (Blueprint $table) {
            $table->unsignedBigInteger('bidhaa_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('manunuzis', function (Blueprint $table) {
            $table->dropColumn(['new_bidhaa', 'bei_kuuza_draft']);
        });
    }
};