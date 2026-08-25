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
            // Add updated_by column - can be string or integer
            $table->string('updated_by')->nullable()->after('image_size');
            // Or if you prefer to store user ID:
            // $table->unsignedBigInteger('updated_by')->nullable()->after('image_size');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bidhaas', function (Blueprint $table) {
            $table->dropColumn('updated_by');
        });
    }
};