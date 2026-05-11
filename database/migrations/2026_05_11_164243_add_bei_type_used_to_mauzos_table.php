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
            if (!Schema::hasColumn('mauzos', 'bei_type_used')) {
                $table->enum('bei_type_used', ['rejareja', 'jumla'])
                      ->default('rejareja')
                      ->after('bei')
                      ->comment('Aina ya bei iliyotumika kwenye mauzo');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mauzos', function (Blueprint $table) {
            if (Schema::hasColumn('mauzos', 'bei_type_used')) {
                $table->dropColumn('bei_type_used');
            }
        });
    }
};