<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, check if Dickson's company exists (ID 11)
        $company = DB::table('companies')->where('id', 11)->first();
        
        if ($company) {
            // Update existing company to be approved
            DB::table('companies')
                ->where('id', 11)
                ->update([
                    'is_verified' => 1,
                    'is_user_approved' => 1,
                    'updated_at' => now(),
                ]);
        }
        
        // Update Dickson user
        DB::table('users')
            ->where('username', 'Dickson')
            ->update([
                'company_id' => 11,
                'is_approved' => 1,
                'updated_at' => now(),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert changes
        DB::table('users')
            ->where('username', 'Dickson')
            ->update([
                'company_id' => null,
                'is_approved' => 0,
                'updated_at' => now(),
            ]);
            
        DB::table('companies')
            ->where('id', 11)
            ->update([
                'is_verified' => 0,
                'is_user_approved' => 0,
                'updated_at' => now(),
            ]);
    }
};
