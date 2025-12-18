<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('madenis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bidhaa_id')->constrained('bidhaas')->onDelete('cascade');
            $table->integer('idadi');
            $table->decimal('bei', 10, 2);
            $table->decimal('jumla', 10, 2);
            $table->decimal('baki', 10, 2)->default(0); // ✅ correct way
            $table->string('jina_mkopaji');
            $table->string('simu');
            $table->date('tarehe_malipo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('madenis'); // ✅ table name should match
    }
};
