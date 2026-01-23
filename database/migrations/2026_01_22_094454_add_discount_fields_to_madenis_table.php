<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
// In the migration file
public function up()
{
    Schema::table('madenis', function (Blueprint $table) {
        $table->decimal('punguzo', 12, 2)->default(0)->after('jumla');
        $table->enum('punguzo_aina', ['jumla', 'bidhaa'])->default('jumla')->after('punguzo');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('madenis', function (Blueprint $table) {
            //
        });
    }
};
