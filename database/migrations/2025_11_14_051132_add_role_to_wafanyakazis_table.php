<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wafanyakazis', function (Blueprint $table) {
            if (!Schema::hasColumn('wafanyakazis', 'role')) {
                $table->string('role')->default('mfanyakazi')->after('password');
            }
        });
    }

    public function down(): void
    {
        Schema::table('wafanyakazis', function (Blueprint $table) {
            if (Schema::hasColumn('wafanyakazis', 'role')) {
                $table->dropColumn('role');
            }
        });
    }
};
