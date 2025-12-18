<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('masaplayas', function (Blueprint $table) {
            $table->id();
            $table->string('jina');
            $table->string('simu')->nullable();
            $table->string('barua_pepe')->nullable();
            $table->string('anaopoishi')->nullable();
            $table->string('ofisi')->nullable();
            $table->text('maelezo')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('masaplayas');
    }
};
