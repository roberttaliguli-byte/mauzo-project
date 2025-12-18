<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('madenis', function (Blueprint $table) {
            // Add company_id if it doesn't exist
            if (!Schema::hasColumn('madenis', 'company_id')) {
                $table->foreignId('company_id')
                      ->nullable()
                      ->after('id')
                      ->constrained()
                      ->onDelete('cascade');
            }

            // Add mteja_id if it doesn't exist
            if (!Schema::hasColumn('madenis', 'mteja_id')) {
                $table->foreignId('mteja_id')
                      ->nullable()
                      ->after('bidhaa_id')
                      ->constrained('mtejas')
                      ->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('madenis', function (Blueprint $table) {
            // Drop foreign keys and columns if rolled back
            if (Schema::hasColumn('madenis', 'company_id')) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            }

            if (Schema::hasColumn('madenis', 'mteja_id')) {
                $table->dropForeign(['mteja_id']);
                $table->dropColumn('mteja_id');
            }
        });
    }
};
