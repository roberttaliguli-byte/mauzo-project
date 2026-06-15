<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('mfanyakazi_id')->nullable()->constrained('wafanyakazis')->onDelete('set null');
            $table->string('user_name');
            $table->string('user_role'); // Boss, Employee
            $table->string('activity_type'); // login, logout, sale, purchase, expense, repayment
            $table->text('description');
            $table->string('model_type')->nullable(); // Mauzo, Manunuzi, Matumizi, Marejesho
            $table->unsignedBigInteger('model_id')->nullable();
            $table->decimal('amount', 12, 2)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at');
            
            // Indexes
            $table->index('company_id');
            $table->index('activity_type');
            $table->index('created_at');
            $table->index(['company_id', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('activity_logs');
    }
};