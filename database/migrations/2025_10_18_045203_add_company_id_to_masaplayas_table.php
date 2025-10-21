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
        Schema::table('masaplayas', function (Blueprint $table) {
            if (!Schema::hasColumn('masaplayas', 'company_id')) {
                $table->foreignId('company_id')
                      ->nullable()
                      ->constrained('companies')
                      ->onDelete('cascade')
                      ->after('id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('masaplayas', function (Blueprint $table) {
            if (Schema::hasColumn('masaplayas', 'company_id')) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            }
        });
    }
};
