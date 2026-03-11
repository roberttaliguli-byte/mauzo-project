<?php
// database/migrations/xxxx_xx_xx_add_last_package_notification_to_companies.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->timestamp('last_package_notification_sent')->nullable()->after('package_end');
        });
    }

    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('last_package_notification_sent');
        });
    }
};