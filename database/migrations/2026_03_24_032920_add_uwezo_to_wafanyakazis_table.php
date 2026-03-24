<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wafanyakazis', function (Blueprint $table) {
            $table->enum('uwezo', ['mdogo', 'mkubwa'])->default('mdogo')->after('getini');
        });
    }

    public function down(): void
    {
        Schema::table('wafanyakazis', function (Blueprint $table) {
            $table->dropColumn('uwezo');
        });
    }
};