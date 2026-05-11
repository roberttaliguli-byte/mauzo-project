<?php
// database/migrations/2026_01_15_000001_add_wholesale_price_to_bidhaas.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bidhaas', function (Blueprint $table) {
            if (!Schema::hasColumn('bidhaas', 'bei_uzo_jumla')) {
                $table->decimal('bei_uzo_jumla', 15, 2)->nullable()->after('bei_kuuza');
            }
            if (!Schema::hasColumn('bidhaas', 'bei_kiasi_cha_chaguo')) {
                $table->enum('bei_kiasi_cha_chaguo', ['rejareja', 'jumla'])->default('rejareja')->after('bei_uzo_jumla');
            }
        });
    }

    public function down()
    {
        Schema::table('bidhaas', function (Blueprint $table) {
            $table->dropColumn(['bei_uzo_jumla', 'bei_kiasi_cha_chaguo']);
        });
    }
};