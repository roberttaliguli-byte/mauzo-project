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
            // Add company_id column if not exists
            if (!Schema::hasColumn('mauzos', 'company_id')) {
                $table->foreignId('company_id')
                      ->after('id')
                      ->constrained()
                      ->onDelete('cascade')
                      ->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mauzos', function (Blueprint $table) {
            if (Schema::hasColumn('mauzos', 'company_id')) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            }
        });
    }
};
