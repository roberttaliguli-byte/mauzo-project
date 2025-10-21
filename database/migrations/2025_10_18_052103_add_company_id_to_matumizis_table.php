<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('matumizis', function (Blueprint $table) {
            // Add company_id only if it doesn’t already exist
            if (!Schema::hasColumn('matumizis', 'company_id')) {
                $table->unsignedBigInteger('company_id')->nullable()->after('id');

                // Add foreign key constraint
                $table->foreign('company_id')
                      ->references('id')
                      ->on('companies')
                      ->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('matumizis', function (Blueprint $table) {
            if (Schema::hasColumn('matumizis', 'company_id')) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            }
        });
    }
};
