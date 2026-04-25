<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('mauzos', function (Blueprint $table) {
            if (!Schema::hasColumn('mauzos', 'mteja_id')) {
                $table->unsignedBigInteger('mteja_id')->nullable()->after('company_id');
                $table->index('mteja_id');
            }
        });
    }

    public function down()
    {
        Schema::table('mauzos', function (Blueprint $table) {
            $table->dropColumn('mteja_id');
        });
    }
};