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
        Schema::table('madenis', function (Blueprint $table) {
            // Add new columns if they don’t exist
            if (!Schema::hasColumn('madenis', 'company_id')) {
                $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
            }

            if (!Schema::hasColumn('madenis', 'mteja_id')) {
                $table->foreignId('mteja_id')->nullable()->constrained('mtejas')->onDelete('set null');
            }

            // Ensure bidhaa_id has foreign key
            $table->foreign('bidhaa_id')->references('id')->on('bidhaas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('madenis', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropForeign(['mteja_id']);
            $table->dropForeign(['bidhaa_id']);

            $table->dropColumn(['company_id', 'mteja_id']);
        });
    }
};
