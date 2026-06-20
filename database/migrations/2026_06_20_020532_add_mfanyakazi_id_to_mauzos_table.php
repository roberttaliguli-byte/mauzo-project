<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('mauzos', function (Blueprint $table) {
            // Check and add user_id column if it doesn't exist
            if (!Schema::hasColumn('mauzos', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('company_id')->constrained('users')->onDelete('set null');
            }
            
            // Check and add mfanyakazi_id column if it doesn't exist
            if (!Schema::hasColumn('mauzos', 'mfanyakazi_id')) {
                $table->foreignId('mfanyakazi_id')->nullable()->after('user_id')->constrained('wafanyakazis')->onDelete('set null');
            }
            
            // Check if indexes exist before adding them
            $this->addIndexIfNotExists('mauzos', 'user_id');
            $this->addIndexIfNotExists('mauzos', 'mfanyakazi_id');
        });
    }

    public function down()
    {
        Schema::table('mauzos', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['mfanyakazi_id']);
            $table->dropColumn(['user_id', 'mfanyakazi_id']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['mfanyakazi_id']);
        });
    }

    /**
     * Add index only if it doesn't exist
     */
    private function addIndexIfNotExists($table, $column)
    {
        $indexName = $table . '_' . $column . '_index';
        
        // Check if index exists
        $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
        
        if (empty($indexes)) {
            Schema::table($table, function (Blueprint $table) use ($column) {
                $table->index($column);
            });
        }
    }
};