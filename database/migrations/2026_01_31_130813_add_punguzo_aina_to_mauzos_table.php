<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mauzos', function (Blueprint $table) {
            // Add the missing punguzo_aina column
            if (!Schema::hasColumn('mauzos', 'punguzo_aina')) {
                $table->enum('punguzo_aina', ['bidhaa', 'jumla'])
                      ->default('bidhaa')
                      ->after('punguzo');
            }
        });
        
        // Update existing records to set default value
        // Assuming all previous discounts were per item (bidhaa)
        DB::statement("UPDATE mauzos SET punguzo_aina = 'bidhaa' WHERE punguzo > 0");
    }

    public function down(): void
    {
        Schema::table('mauzos', function (Blueprint $table) {
            $table->dropColumn('punguzo_aina');
        });
    }
};