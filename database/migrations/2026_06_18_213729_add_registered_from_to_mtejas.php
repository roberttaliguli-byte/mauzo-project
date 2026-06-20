<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mtejas', function (Blueprint $table) {
            $table->string('registered_from')
                  ->nullable()
                  ->default('boss')
                  ->after('customer_code');
        });
    }

    public function down(): void
    {
        Schema::table('mtejas', function (Blueprint $table) {
            $table->dropColumn('registered_from');
        });
    }
};