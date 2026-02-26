<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('transaction_reference')->unique();
            $table->string('pesapal_transaction_tracking_id')->nullable();
            $table->string('merchant_reference')->unique();
            $table->string('package_type'); // Free Trial 14 days, 180 days, 366 days
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('TZS');
            $table->string('phone_number')->nullable();
            $table->string('payment_method')->nullable(); // TIGO, VODACOM, AIRTEL
            $table->enum('status', [
                'pending', 
                'processing', 
                'completed', 
                'failed', 
                'cancelled',
                'refunded'
            ])->default('pending');
            $table->json('payment_request_data')->nullable();
            $table->json('payment_response_data')->nullable();
            $table->json('ipn_data')->nullable();
            $table->timestamp('payment_date')->nullable();
            $table->timestamp('expiry_date')->nullable();
            $table->timestamps();
            
            $table->index('transaction_reference');
            $table->index('pesapal_transaction_tracking_id');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};