<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mauzos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bidhaa_id')->constrained('bidhaas')->onDelete('cascade');
            $table->integer('idadi');
            $table->decimal('bei', 10, 2);
            $table->decimal('punguzo', 10, 2)->default(0);
            $table->decimal('jumla', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mauzos');
    }
};

