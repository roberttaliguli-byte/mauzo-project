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
// Create aina_za_matumizi table migration
Schema::create('aina_za_matumizi', function (Blueprint $table) {
    $table->id();
    $table->string('jina')->unique();
    $table->text('maelezo')->nullable();
    $table->string('rangi')->nullable();
    $table->string('kategoria')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aina_za_matumizi');
    }
};
