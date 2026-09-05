<?php
// database/migrations/2026_09_05_102250_add_salary_fields_to_wafanyakazis_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('wafanyakazis', function (Blueprint $table) {
            // Check if column exists before adding
            if (!Schema::hasColumn('wafanyakazis', 'salary')) {
                $table->decimal('salary', 15, 2)->default(0)->after('uwezo');
            }
            
            if (!Schema::hasColumn('wafanyakazis', 'salary_currency')) {
                $table->string('salary_currency', 10)->default('TZS')->after('salary');
            }
            
            if (!Schema::hasColumn('wafanyakazis', 'salary_frequency')) {
                $table->string('salary_frequency', 20)->default('monthly')->after('salary_currency');
            }
            
            if (!Schema::hasColumn('wafanyakazis', 'allow_counter_access')) {
                $table->boolean('allow_counter_access')->default(false)->after('salary_frequency');
            }
            
            if (!Schema::hasColumn('wafanyakazis', 'remarks')) {
                $table->text('remarks')->nullable()->after('allow_counter_access');
            }
            
            // 'last_login_at' already exists - skip it
            
            if (!Schema::hasColumn('wafanyakazis', 'login_count')) {
                $table->integer('login_count')->default(0)->after('last_login_at');
            }
            
            if (!Schema::hasColumn('wafanyakazis', 'active')) {
                $table->boolean('active')->default(true)->after('login_count');
            }
        });
    }

    public function down()
    {
        Schema::table('wafanyakazis', function (Blueprint $table) {
            $columns = ['salary', 'salary_currency', 'salary_frequency', 'allow_counter_access', 'remarks', 'login_count', 'active'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('wafanyakazis', $column)) {
                    $table->dropColumn($column);
                }
            }
            // Don't drop last_login_at as it existed before
        });
    }
};