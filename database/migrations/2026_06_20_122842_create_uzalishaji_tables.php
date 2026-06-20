<?php
// database/migrations/2024_01_01_000000_create_uzalishaji_tables.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Main production table
        Schema::create('uzalishajis', function (Blueprint $table) {
            $table->id();
            $table->date('tarehe');
            $table->string('jina');
            $table->string('aina_bidhaa');
            $table->text('maelezo')->nullable();
            
            // Cost calculations
            $table->decimal('jumla_gharama', 15, 2)->default(0);
            $table->decimal('idadi_iliyozalishwa', 15, 2)->default(0);
            $table->string('kipimo')->nullable();
            $table->decimal('gharama_kwa_moja', 15, 2)->default(0);
            $table->decimal('bei_kununua_ilipendekezwa', 15, 2)->default(0);
            
            // Profit simulation
            $table->decimal('bei_kuuza_ilichaguliwa', 15, 2)->nullable();
            $table->decimal('faida_kwa_moja', 15, 2)->nullable();
            $table->decimal('asilimia_faida', 15, 2)->nullable();
            $table->decimal('faida_ya_jumla', 15, 2)->nullable();
            
            // Links
            $table->foreignId('bidhaa_id')->nullable()->constrained('bidhaas')->onDelete('set null');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('mtumiaji_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('mfanyakazi_id')->nullable()->constrained('wafanyakazis')->onDelete('set null');
            
            $table->boolean('imekamilika')->default(false);
            $table->string('status')->default('in_progress');
            $table->timestamps();
            
            $table->index(['company_id', 'tarehe']);
            $table->index(['company_id', 'imekamilika']);
        });

        // Production costs table
        Schema::create('uzalishaji_gharama', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uzalishaji_id')->constrained('uzalishajis')->onDelete('cascade');
            $table->string('jina');
            $table->string('kundi');
            $table->decimal('kiasi', 15, 2)->default(1);
            $table->decimal('gharama', 15, 2)->default(0);
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->index(['uzalishaji_id', 'kundi']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('uzalishaji_gharama');
        Schema::dropIfExists('uzalishajis');
    }
};