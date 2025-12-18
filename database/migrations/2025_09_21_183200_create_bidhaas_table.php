<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bidhaas', function (Blueprint $table) {
    $table->id();
    $table->string('jina');
    $table->string('aina');
    $table->string('kipimo')->nullable();
    $table->integer('idadi');
    $table->decimal('bei_nunua', 10, 2);
    $table->decimal('bei_kuuza', 10, 2);
    $table->date('expiry')->nullable();
    $table->string('barcode')->nullable()->unique();
    $table->timestamps();
});

    }

    public function down(): void
    {
        Schema::dropIfExists('bidhaas');
    }
};
