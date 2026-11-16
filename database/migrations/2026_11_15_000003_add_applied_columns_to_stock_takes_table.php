<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_takes', function (Blueprint $t) {
            if (!Schema::hasColumn('stock_takes', 'applied_at')) {
                $t->timestamp('applied_at')->nullable()->after('closed_at');
            }
            if (!Schema::hasColumn('stock_takes', 'applied_by_name')) {
                $t->string('applied_by_name')->nullable()->after('applied_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('stock_takes', function (Blueprint $t) {
            $t->dropColumn(['applied_at', 'applied_by_name']);
        });
    }
};
