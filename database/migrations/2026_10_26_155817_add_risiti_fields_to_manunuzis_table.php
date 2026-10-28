<?php
// database/migrations/xxxx_add_risiti_fields_to_manunuzis_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('manunuzis', function (Blueprint $table) {
            // Risiti number - unique per company
            $table->string('risiti_number', 50)->nullable()->after('id');
            
            // Who created this entry
            $table->foreignId('created_by')->nullable()->after('company_id');
            
            // Source of purchase
            $table->enum('chanzo', ['saplaya', 'duka', 'soko', 'mengineyo'])->default('saplaya')->after('simu');
            
            // Payment status
            $table->enum('hali_ya_malipo', ['imekamilika', 'deni', 'nusu'])->default('imekamilika')->after('mengineyo');
            
            // Amount paid (for partial payments)
            $table->decimal('kiasi_kilicholipwa', 15, 2)->default(0)->after('hali_ya_malipo');
            
            // Balance remaining
            $table->decimal('deni_lililobaki', 15, 2)->default(0)->after('kiasi_kilicholipwa');
            
            // Original price before any adjustments
            $table->decimal('bei_ya_awali', 15, 2)->nullable()->after('bei');
            
            // Index for faster queries
            $table->index(['company_id', 'risiti_number']);
            $table->index(['company_id', 'created_at']);
            $table->index(['company_id', 'hali_ya_malipo']);
        });
    }

    public function down(): void
    {
        Schema::table('manunuzis', function (Blueprint $table) {
            $table->dropIndex(['company_id', 'risiti_number']);
            $table->dropIndex(['company_id', 'created_at']);
            $table->dropIndex(['company_id', 'hali_ya_malipo']);
            $table->dropColumn([
                'risiti_number',
                'created_by',
                'chanzo',
                'hali_ya_malipo',
                'kiasi_kilicholipwa',
                'deni_lililobaki',
                'bei_ya_awali',
            ]);
        });
    }
};