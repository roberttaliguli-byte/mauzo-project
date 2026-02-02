<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mauzos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade'); // ✅ ADD
            $table->foreignId('bidhaa_id')->constrained('bidhaas')->onDelete('cascade');
            $table->foreignId('madeni_id')->nullable()->constrained()->onDelete('cascade'); // ✅ ADD (nullable for non-debt sales)
            $table->string('receipt_no'); // ✅ ADD receipt number
            
            // Product details
            $table->integer('idadi');
            $table->decimal('bei', 10, 2);
            
            // Discount handling - CRITICAL
            $table->decimal('punguzo', 10, 2)->default(0);
            $table->enum('punguzo_aina', ['bidhaa', 'jumla'])->default('bidhaa'); // ✅ MUST ADD THIS
            
            // Totals
            $table->decimal('jumla', 10, 2);
            
            // Debt tracking
            $table->boolean('is_debt_repayment')->default(false); // ✅ ADD
            
            // Optional: For reprints tracking
            $table->integer('reprint_count')->default(0);
            
            $table->timestamps();
            
            // Indexes for better performance
            $table->index('receipt_no');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mauzos');
    }
};