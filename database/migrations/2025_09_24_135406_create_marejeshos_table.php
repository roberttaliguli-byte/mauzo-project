<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('marejeshos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('madeni_id')->constrained('madenis')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('mfanyakazi_id')->nullable()->constrained('wafanyakazis')->onDelete('set null');
            
            $table->decimal('kiasi', 12, 2);
            $table->date('tarehe');
            $table->enum('lipa_kwa', ['cash', 'lipa_namba', 'bank'])->default('cash');
            $table->string('lipa_kwa_type')->nullable(); // mpesa, crdb, nmb, etc.
            
            $table->timestamps();
            
            // Indexes for better performance
            $table->index('company_id');
            $table->index('madeni_id');
            $table->index('tarehe');
            $table->index('lipa_kwa');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marejeshos');
    }
};