<?php
// database/migrations/2026_02_23_145516_add_activity_columns_to_wafanyakazi_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Fix: Change 'wafanyakazi' to 'wafanyakazis' to match your actual table name
        Schema::table('wafanyakazis', function (Blueprint $table) {
            $table->timestamp('last_login_at')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->integer('login_count')->default(0);
        });
    }

    public function down()
    {
        Schema::table('wafanyakazis', function (Blueprint $table) {
            $table->dropColumn(['last_login_at', 'last_activity_at', 'login_count']);
        });
    }
};