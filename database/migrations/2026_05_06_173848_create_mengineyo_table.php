<?php
// database/migrations/2024_01_01_000001_create_mengineyo_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('mengineyo', function (Blueprint $table) {
            $table->id();
            $table->string('chanzo'); // Income source
            $table->decimal('kiasi', 12, 2); // Amount
            $table->text('maelezo')->nullable(); // Description
            $table->date('tarehe'); // Date of income
            $table->string('aina')->default('mapato');
            
            // FIXED: Changed from 'wafanyakazi' to 'wafanyakazis' to match actual table name
            $table->foreignId('mfanyakazi_id')
                  ->nullable()
                  ->constrained('wafanyakazis')  // ← Changed here
                  ->onDelete('set null');
            
            $table->timestamps();
            
            $table->index('tarehe');
            $table->index('chanzo');
        });
    }

    public function down()
    {
        Schema::dropIfExists('mengineyo');
    }
};