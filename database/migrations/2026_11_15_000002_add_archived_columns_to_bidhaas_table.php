<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bidhaas', function (Blueprint $table) {
            if (!Schema::hasColumn('bidhaas', 'archived_at')) {
                $table->timestamp('archived_at')->nullable()->after('updated_at');
            }
            if (!Schema::hasColumn('bidhaas', 'archived_by')) {
                $table->string('archived_by')->nullable()->after('archived_at');
            }
            if (!Schema::hasColumn('bidhaas', 'archive_reason')) {
                $table->string('archive_reason')->nullable()->after('archived_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bidhaas', function (Blueprint $table) {
            $table->dropColumn(['archived_at', 'archived_by', 'archive_reason']);
        });
    }
};