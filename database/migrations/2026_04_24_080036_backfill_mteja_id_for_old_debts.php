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
    DB::statement("
        UPDATE madenis d
        JOIN mtejas m ON (d.simu = m.simu OR d.jina_mkopaji = m.jina)
        AND d.company_id = m.company_id
        SET d.mteja_id = m.id
        WHERE d.mteja_id IS NULL
    ");
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};