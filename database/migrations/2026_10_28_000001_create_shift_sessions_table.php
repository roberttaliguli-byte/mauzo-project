<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('shift_sessions')) {
            Schema::create('shift_sessions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('mfanyakazi_id')->nullable()->constrained('wafanyakazis')->nullOnDelete();

                $table->enum('status', ['open', 'closed'])->default('open');
                $table->dateTime('started_at');
                $table->dateTime('closed_at')->nullable();

                // Cash reconciliation
                $table->decimal('opening_balance', 15, 2)->default(0);
                $table->decimal('closing_cash', 15, 2)->nullable();
                $table->decimal('expected_cash', 15, 2)->nullable();
                $table->decimal('cash_difference', 15, 2)->nullable();

                // Snapshot saved when the shift is closed
                $table->decimal('total_sales', 15, 2)->default(0);
                $table->decimal('total_items', 15, 3)->default(0);
                $table->decimal('total_cost', 15, 2)->default(0);
                $table->decimal('total_profit', 15, 2)->default(0);
                $table->decimal('total_expenses', 15, 2)->default(0);
                $table->decimal('cash_sales', 15, 2)->default(0);
                $table->decimal('total_returns', 15, 2)->default(0);

                $table->text('notes')->nullable();
                $table->dateTime('report_sent_at')->nullable();
                $table->string('report_sent_to')->nullable();

                $table->timestamps();

                $table->index(['company_id', 'status']);
                $table->index(['company_id', 'started_at']);
                $table->index(['user_id', 'status']);
                $table->index(['mfanyakazi_id', 'status']);
            });
        }

        // Tie every sale to the shift it was recorded in.
        if (Schema::hasTable('mauzos') && !Schema::hasColumn('mauzos', 'shift_session_id')) {
            Schema::table('mauzos', function (Blueprint $table) {
                $table->foreignId('shift_session_id')
                    ->nullable()
                    ->after('company_id')
                    ->constrained('shift_sessions')
                    ->nullOnDelete();
            });
        }

        // Tie expenses to the shift + the user who spent the money.
        if (Schema::hasTable('matumizis')) {
            Schema::table('matumizis', function (Blueprint $table) {
                if (!Schema::hasColumn('matumizis', 'shift_session_id')) {
                    $table->foreignId('shift_session_id')
                        ->nullable()
                        ->after('company_id')
                        ->constrained('shift_sessions')
                        ->nullOnDelete();
                }
                if (!Schema::hasColumn('matumizis', 'user_id')) {
                    $table->foreignId('user_id')->nullable()->after('shift_session_id')->constrained('users')->nullOnDelete();
                }
                if (!Schema::hasColumn('matumizis', 'mfanyakazi_id')) {
                    $table->foreignId('mfanyakazi_id')->nullable()->after('user_id')->constrained('wafanyakazis')->nullOnDelete();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('matumizis')) {
            Schema::table('matumizis', function (Blueprint $table) {
                if (Schema::hasColumn('matumizis', 'mfanyakazi_id')) {
                    $table->dropConstrainedForeignId('mfanyakazi_id');
                }
                if (Schema::hasColumn('matumizis', 'user_id')) {
                    $table->dropConstrainedForeignId('user_id');
                }
                if (Schema::hasColumn('matumizis', 'shift_session_id')) {
                    $table->dropConstrainedForeignId('shift_session_id');
                }
            });
        }

        if (Schema::hasTable('mauzos') && Schema::hasColumn('mauzos', 'shift_session_id')) {
            Schema::table('mauzos', function (Blueprint $table) {
                $table->dropConstrainedForeignId('shift_session_id');
            });
        }

        Schema::dropIfExists('shift_sessions');
    }
};
