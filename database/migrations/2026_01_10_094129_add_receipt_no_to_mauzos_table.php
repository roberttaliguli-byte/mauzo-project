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
    Schema::table('mauzos', function (Blueprint $table) {
        $table->string('receipt_no')->nullable()->after('company_id');
        $table->integer('reprint_count')->default(0)->after('receipt_no');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mauzos', function (Blueprint $table) {
            //
        });
    }
};
