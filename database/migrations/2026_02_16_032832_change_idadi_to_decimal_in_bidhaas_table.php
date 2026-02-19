<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bidhaas', function (Blueprint $table) {
            // Change from integer to decimal with 10 total digits, 2 decimal places
            $table->decimal('idadi', 10, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bidhaas', function (Blueprint $table) {
            // Change back to integer if we need to rollback
            $table->integer('idadi')->change();
        });
    }
};