<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('histories', function (Blueprint $table) {
            $table->id();
            $table->string('user');          // who did the action
            $table->string('action');        // what action was done
            $table->text('details')->nullable(); // optional extra info
            $table->timestamps();            // automatic created_at, updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('histories');
    }
};
