<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('marejeshos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('madeni_id')->constrained('madenis')->onDelete('cascade'); // fixed table name
            $table->decimal('kiasi', 12, 2); // amount repaid
            $table->date('tarehe');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marejeshos');
    }
};
