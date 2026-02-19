<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // List of tables to update
        $tables = ['bidhaas', 'mauzos', 'madenis', 'manunuzis'];
        
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::statement("ALTER TABLE {$table} MODIFY idadi DECIMAL(10, 2)");
            }
        }
        
        // Check marejeshos table separately (might not have idadi column)
        if (Schema::hasTable('marejeshos') && Schema::hasColumn('marejeshos', 'idadi')) {
            DB::statement('ALTER TABLE marejeshos MODIFY idadi DECIMAL(10, 2)');
        }
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        // List of tables to revert
        $tables = ['bidhaas', 'mauzos', 'madenis', 'manunuzis'];
        
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::statement("ALTER TABLE {$table} MODIFY idadi INT");
            }
        }
        
        // Check marejeshos table separately
        if (Schema::hasTable('marejeshos') && Schema::hasColumn('marejeshos', 'idadi')) {
            DB::statement('ALTER TABLE marejeshos MODIFY idadi INT');
        }
    }
};