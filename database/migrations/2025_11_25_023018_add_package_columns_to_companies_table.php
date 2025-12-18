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
    Schema::table('companies', function (Blueprint $table) {
        // Only add missing columns
        $table->date('package_start')->nullable();
        $table->date('package_end')->nullable();
    });
}

public function down(): void
{
    Schema::table('companies', function (Blueprint $table) {
        $table->dropColumn(['package_start', 'package_end']);
    });
}


};
