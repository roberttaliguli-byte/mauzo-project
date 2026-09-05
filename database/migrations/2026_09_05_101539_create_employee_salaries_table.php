<?php
// database/migrations/2025_01_01_000000_create_employee_salaries_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('employee_salaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('mfanyakazi_id');
            $table->decimal('salary_amount', 15, 2)->default(0);
            $table->string('currency', 10)->default('TZS');
            $table->string('frequency', 20)->default('monthly');
            $table->date('effective_date');
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('mfanyakazi_id')->references('id')->on('wafanyakazis')->onDelete('cascade');
        });

        Schema::create('employee_salary_deductions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('mfanyakazi_id');
            $table->unsignedBigInteger('order_id')->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('reason');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('mfanyakazi_id')->references('id')->on('wafanyakazis')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('employee_salary_deductions');
        Schema::dropIfExists('employee_salaries');
    }
};