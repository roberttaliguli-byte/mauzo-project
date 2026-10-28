<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('shift_sessions') && !Schema::hasColumn('shift_sessions', 'report_snapshot')) {
            Schema::table('shift_sessions', function (Blueprint $table) {
                $table->longText('report_snapshot')->nullable()->after('notes');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('shift_sessions') && Schema::hasColumn('shift_sessions', 'report_snapshot')) {
            Schema::table('shift_sessions', function (Blueprint $table) {
                $table->dropColumn('report_snapshot');
            });
        }
    }
};
