<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up()
    {
        Schema::table('mauzos', function (Blueprint $table) {
            $table->decimal('bei', 15, 2)->change();
            $table->decimal('punguzo', 15, 2)->default(0)->change();
            $table->decimal('jumla', 18, 2)->change();
        });
    }

    public function down()
    {
        Schema::table('mauzos', function (Blueprint $table) {
            // revert to old types (adjust if different)
            $table->integer('bei')->change();
            $table->integer('punguzo')->change();
            $table->integer('jumla')->change();
        });
    }
};