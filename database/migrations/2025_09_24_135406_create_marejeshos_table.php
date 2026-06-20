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
    $table->foreignId('user_id')->nullable();
    $table->foreignId('mfanyakazi_id')->nullable();

    $table->decimal('kiasi', 12, 2);
    $table->date('tarehe');

    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('marejeshos');
    }
};