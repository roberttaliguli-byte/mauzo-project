<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bidhaas', function (Blueprint $table) {
            if (!Schema::hasColumn('bidhaas', 'company_id')) {
                $table->unsignedBigInteger('company_id')->nullable()->after('id');

                // Foreign key relationship
                $table->foreign('company_id')
                      ->references('id')
                      ->on('companies')
                      ->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bidhaas', function (Blueprint $table) {
            if (Schema::hasColumn('bidhaas', 'company_id')) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            }
        });
    }
};
