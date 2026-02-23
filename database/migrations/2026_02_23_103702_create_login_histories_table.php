<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('login_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->timestamp('login_at');
            $table->timestamp('logout_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            
            // Indexes for better performance
            $table->index('company_id');
            $table->index('login_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('login_histories');
    }
};