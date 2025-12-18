<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('mauzos', function (Blueprint $table) {
            $table->boolean('is_debt_repayment')->default(false);
            $table->foreignId('madeni_id')->nullable()->constrained('madenis')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('mauzos', function (Blueprint $table) {
            $table->dropForeign(['madeni_id']);
            $table->dropColumn(['is_debt_repayment', 'madeni_id']);
        });
    }
};