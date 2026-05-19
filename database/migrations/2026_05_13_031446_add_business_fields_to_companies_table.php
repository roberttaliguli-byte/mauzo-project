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

        if (!Schema::hasColumn('companies', 'business_type')) {
            $table->string('business_type')
                ->nullable()
                ->default('retail_shop')
                ->after('phone');
        }

        if (!Schema::hasColumn('companies', 'hear_about_us')) {
            $table->string('hear_about_us')
                ->nullable()
                ->after('business_type');
        }

        $table->string('owner_gender')
            ->nullable()
            ->default('male')
            ->change();

        $table->date('owner_dob')
            ->nullable()
            ->default('2000-01-01')
            ->change();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['business_type', 'hear_about_us']);
            
            // Revert to required fields
            $table->string('owner_gender')->nullable(false)->change();
            $table->date('owner_dob')->nullable(false)->change();
        });
    }
};