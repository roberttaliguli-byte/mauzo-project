<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
{
    Schema::table('wafanyakazis', function (Blueprint $table) {
        $table->string('getini')->default('simama');
    });
}

public function down()
{
    Schema::table('wafanyakazis', function (Blueprint $table) {
        $table->dropColumn('getini');
    });
}

};
