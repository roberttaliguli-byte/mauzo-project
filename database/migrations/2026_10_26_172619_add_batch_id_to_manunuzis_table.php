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
    Schema::table('manunuzis', function (Blueprint $table) {
        $table->string('batch_id')->nullable()->after('id'); // ID for the whole receipt
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('manunuzis', function (Blueprint $table) {
            //
        });
    }
};
