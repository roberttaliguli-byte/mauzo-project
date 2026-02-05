<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLipaKwaToMarejeshosTable extends Migration
{
    public function up()
    {
        Schema::table('marejeshos', function (Blueprint $table) {
            $table->enum('lipa_kwa', ['cash', 'lipa_namba', 'bank'])->default('cash')->after('tarehe');
        });
    }

    public function down()
    {
        Schema::table('marejeshos', function (Blueprint $table) {
            $table->dropColumn('lipa_kwa');
        });
    }
}