<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('last_login_at')->nullable()->after('email_verified_at');
            $table->timestamp('last_activity_at')->nullable()->after('last_login_at');
            $table->integer('login_count')->default(0)->after('last_activity_at');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['last_login_at', 'last_activity_at', 'login_count']);
        });
    }
};