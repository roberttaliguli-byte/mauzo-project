<?php
// database/migrations/2024_01_01_000001_create_bidhaa_histories_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBidhaaHistoriesTable extends Migration
{
    public function up()
    {
        Schema::create('bidhaa_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bidhaa_id');
            $table->unsignedBigInteger('company_id');
            $table->decimal('idadi_iliyoingizwa', 10, 2)->default(0);
            $table->decimal('idadi_iliyouzwa', 10, 2)->default(0);
            $table->decimal('idadi_iliyobaki', 10, 2)->default(0);
            $table->decimal('bei_nunua', 10, 2)->default(0);
            $table->decimal('bei_kuuza', 10, 2)->default(0);
            $table->string('aina_ya_shughuli')->nullable(); // ingizo, mauzo, marekebisho
            $table->text('maelezo')->nullable();
            $table->unsignedBigInteger('mtumiaji_id')->nullable();
            $table->timestamps();

            $table->foreign('bidhaa_id')->references('id')->on('bidhaas')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->index(['bidhaa_id', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('bidhaa_histories');
    }
}