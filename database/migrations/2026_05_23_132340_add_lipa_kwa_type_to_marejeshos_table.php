<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('marejeshos', function (Blueprint $table) {
            $table->string('lipa_kwa_type')->nullable()->after('lipa_kwa');
        });
    }

    public function down()
    {
        Schema::table('marejeshos', function (Blueprint $table) {
            $table->dropColumn('lipa_kwa_type');
        });
    }
};