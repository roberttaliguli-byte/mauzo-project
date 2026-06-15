<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('mauzos', function (Blueprint $table) {
            // Add user tracking columns if they don't exist
            if (!Schema::hasColumn('mauzos', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('company_id')->constrained('users')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('mauzos', 'mfanyakazi_id')) {
                $table->foreignId('mfanyakazi_id')->nullable()->after('user_id')->constrained('wafanyakazis')->onDelete('set null');
            }
            
            // Add indexes
            $table->index('user_id');
            $table->index('mfanyakazi_id');
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
};