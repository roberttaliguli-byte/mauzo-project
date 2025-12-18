<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
    Schema::create('mtejas', function (Blueprint $table) {
        $table->id();
        $table->string('jina');
        $table->string('simu');
        $table->string('barua_pepe')->nullable();
        $table->string('anapoishi')->nullable();
        $table->text('maelezo')->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mtejas');
    }
};
