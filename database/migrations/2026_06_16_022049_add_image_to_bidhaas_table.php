<?php
// database/migrations/2026_06_16_022049_add_image_to_bidhaas_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImageToBidhaasTable extends Migration
{
    public function up()
    {
        Schema::table('bidhaas', function (Blueprint $table) {
            $table->binary('image')->nullable()->after('barcode');
        });
    }

    public function down()
    {
        Schema::table('bidhaas', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }
}