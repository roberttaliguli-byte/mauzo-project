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
        Schema::create('aina_za_matumizi', function (Blueprint $table) {
            $table->id();
            $table->string('jina');
            $table->text('maelezo')->nullable();
            $table->string('rangi')->nullable();
            $table->string('kategoria')->nullable();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            // Ensure unique expense type names per company
            $table->unique(['jina', 'company_id']);
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