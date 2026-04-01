<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('recipient');
            $table->text('message');
            $table->string('status');
            $table->text('status_description')->nullable();
            $table->integer('sms_count')->default(1);
            $table->string('reference')->nullable();
            $table->timestamp('sent_at');
            $table->timestamps();
            
            $table->index('recipient');
            $table->index('sent_at');
            $table->index('company_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('sms_logs');
    }
};