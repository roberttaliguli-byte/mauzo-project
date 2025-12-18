<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('manunuzis', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('bidhaa_id'); // foreign key
        $table->integer('idadi');
        $table->decimal('bei', 12, 2);
        $table->date('expiry')->nullable();
        $table->string('saplaya')->nullable();
        $table->string('simu')->nullable();
        $table->text('mengineyo')->nullable();
        $table->timestamps();

        $table->foreign('bidhaa_id')->references('id')->on('bidhaas')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manunuzis');
    }
};
