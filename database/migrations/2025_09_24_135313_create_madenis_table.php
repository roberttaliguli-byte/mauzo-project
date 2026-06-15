<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('madenis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('bidhaa_id')->constrained('bidhaas')->onDelete('cascade');
            $table->foreignId('mteja_id')->nullable()->constrained('mtejas')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('mfanyakazi_id')->nullable()->constrained('wafanyakazis')->onDelete('set null');
            
            $table->integer('idadi');
            $table->decimal('bei', 10, 2);
            $table->decimal('punguzo', 10, 2)->default(0);
            $table->enum('punguzo_aina', ['bidhaa', 'jumla'])->default('bidhaa');
            $table->decimal('jumla', 10, 2);
            $table->decimal('baki', 10, 2)->default(0);
            
            $table->string('jina_mkopaji');
            $table->string('simu');
            $table->date('tarehe_malipo');
            
            $table->timestamps();
            
            // Indexes for better performance
            $table->index('company_id');
            $table->index('baki');
            $table->index('jina_mkopaji');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('madenis');
    }
};