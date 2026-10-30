<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('manunuzis', function (Blueprint $table) {
            $table->string('status', 20)->default('final')->after('hali_ya_malipo');

            // NOT unique — one draft = many rows sharing the same token
            $table->string('draft_token', 64)->nullable()->after('status');

            $table->unsignedBigInteger('draft_owner_id')->nullable()->after('draft_token');
            $table->timestamp('draft_updated_at')->nullable()->after('draft_owner_id');

            $table->index(['company_id', 'status']);
            $table->index(['draft_owner_id', 'status']);
            $table->index('draft_token');
        });
    }

    public function down(): void
    {
        Schema::table('manunuzis', function (Blueprint $table) {
            $table->dropIndex(['company_id', 'status']);
            $table->dropIndex(['draft_owner_id', 'status']);
            $table->dropIndex(['draft_token']);
            $table->dropColumn([
                'status',
                'draft_token',
                'draft_owner_id',
                'draft_updated_at',
            ]);
        });
    }
};