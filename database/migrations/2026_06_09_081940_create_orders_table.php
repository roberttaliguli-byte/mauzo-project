<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->string('order_number')->unique();
            $table->index('order_number');

            $table->unsignedBigInteger('customer_id')->nullable()->index();

            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('customer_email')->nullable();
            $table->text('customer_address')->nullable();

            $table->json('items');

            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);

            $table->enum('discount_type', ['bidhaa', 'jumla'])
                  ->default('jumla');

            $table->decimal('total', 15, 2)->default(0);

            $table->enum('status', [
                'saved',
                'confirmed',
                'paid',
                'cancelled'
            ])->default('saved')->index();

            $table->string('payment_method')->nullable();
            $table->string('payment_type')->nullable();

            $table->text('notes')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('sale_id')->nullable();

            $table->timestamp('paid_at')->nullable();

            $table->boolean('transferred_to_cart')->default(false);
            $table->timestamp('transferred_at')->nullable();

            $table->string('payment_reference')->nullable();

            $table->timestamps();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};