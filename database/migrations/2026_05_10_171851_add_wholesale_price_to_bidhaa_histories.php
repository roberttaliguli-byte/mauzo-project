<?php
// database/migrations/2026_01_15_000002_add_wholesale_price_to_bidhaa_histories.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bidhaa_histories', function (Blueprint $table) {
            if (!Schema::hasColumn('bidhaa_histories', 'bei_uzo_jumla')) {
                $table->decimal('bei_uzo_jumla', 15, 2)->nullable()->after('bei_kuuza');
            }
        });
    }

    public function down()
    {
        Schema::table('bidhaa_histories', function (Blueprint $table) {
            $table->dropColumn('bei_uzo_jumla');
        });
    }
};