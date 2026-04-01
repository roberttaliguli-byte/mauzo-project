<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminSmsLogsTable extends Migration
{
    public function up()
    {
        Schema::create('admin_sms_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('set null');
            $table->text('message');
            $table->string('recipient');
            $table->string('status');
            $table->text('response')->nullable();
            $table->string('reference')->nullable();
            $table->timestamp('sent_at');
            $table->timestamps();
            
            $table->index(['company_id', 'sent_at']);
            $table->index(['status', 'sent_at']);
            $table->index('reference');
        });
    }

    public function down()
    {
        Schema::dropIfExists('admin_sms_logs');
    }
}