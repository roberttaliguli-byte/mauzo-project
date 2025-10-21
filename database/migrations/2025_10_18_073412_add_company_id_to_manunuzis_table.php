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
    Schema::table('manunuzis', function (Blueprint $table) {
        $table->unsignedBigInteger('company_id')->after('id');

        // (optional) foreign key if you have a companies table
        // $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
    });
}

public function down()
{
    Schema::table('manunuzis', function (Blueprint $table) {
        $table->dropColumn('company_id');
    });
}

};
