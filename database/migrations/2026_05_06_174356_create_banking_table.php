<?php
// database/migrations/2024_01_01_000002_create_banking_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('banking', function (Blueprint $table) {
            $table->id();
            $table->string('benki'); // Bank name
            $table->decimal('kiasi', 12, 2); // Amount deposited
            $table->decimal('saldo_baada_ya_banking', 12, 2); // Remaining balance
            $table->text('maelezo')->nullable();
            $table->date('tarehe');
            $table->string('status')->default('completed');
            
            // FIXED: Changed from 'wafanyakazi' to 'wafanyakazis' to match actual table name
            $table->foreignId('mfanyakazi_id')
                  ->nullable()
                  ->constrained('wafanyakazis')  // ← Changed here
                  ->onDelete('set null');
            
            $table->timestamps();
            
            $table->index('tarehe');
            $table->index('benki');
        });
    }

    public function down()
    {
        Schema::dropIfExists('banking');
    }
};