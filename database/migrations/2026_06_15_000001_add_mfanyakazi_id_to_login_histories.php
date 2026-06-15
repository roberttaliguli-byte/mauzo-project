<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('login_histories', function (Blueprint $table) {
            // Check if column doesn't exist before adding
            if (!Schema::hasColumn('login_histories', 'mfanyakazi_id')) {
                $table->foreignId('mfanyakazi_id')->nullable()->after('user_id')->constrained('wafanyakazis')->onDelete('cascade');
            }
            
            // Make user_id nullable since employees will use mfanyakazi_id
            $table->foreignId('user_id')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('login_histories', function (Blueprint $table) {
            $table->dropForeign(['mfanyakazi_id']);
            $table->dropColumn('mfanyakazi_id');
            $table->foreignId('user_id')->nullable(false)->change();
        });
    }
};