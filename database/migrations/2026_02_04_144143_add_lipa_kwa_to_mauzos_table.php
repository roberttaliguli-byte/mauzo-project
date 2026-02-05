<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLipaKwaToMauzosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mauzos', function (Blueprint $table) {
            $table->string('lipa_kwa')->default('cash')->after('jumla');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mauzos', function (Blueprint $table) {
            $table->dropColumn('lipa_kwa');
        });
    }
}