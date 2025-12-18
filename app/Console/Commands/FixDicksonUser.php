<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Company;

class FixDicksonUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:dickson-user';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Fix Dickson user authentication issue by assigning company and approving user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Starting Dickson user fix...');
        
        try {
            // Find Dickson user
            $dickson = User::where('username', 'Dickson')->first();
            
            if (!$dickson) {
                $this->error('❌ User "Dickson" not found');
                return 1;
            }
            
            $this->info('✅ Found user: ' . $dickson->username);
            
            // Check if company exists
            if (!$dickson->company_id) {
                $this->warn('⚠️  User has no company assigned');
                
                // Try to find or create company
                $company = Company::find(11);
                
                if (!$company) {
                    $this->info('📝 Creating company for Dickson...');
                    $company = Company::create([
                        'company_name' => 'Dickson Shop',
                        'owner_name' => 'Dickson',
                        'owner_gender' => 'male',
                        'owner_dob' => '2025-11-27',
                        'location' => 'Kariakoo',
                        'region' => 'Dar es Salaam',
                        'phone' => '0750731387',
                        'email' => 'dickson@gmail.com',
                        'is_verified' => 1,
                        'is_user_approved' => 1,
                    ]);
                    $this->info('✅ Company created with ID: ' . $company->id);
                } else {
                    $this->info('✅ Found existing company with ID: ' . $company->id);
                }
                
                $dickson->company_id = $company->id;
            } else {
                $this->info('✅ User already has company_id: ' . $dickson->company_id);
                $company = $dickson->company;
            }
            
            // Approve user
            if (!$dickson->is_approved) {
                $dickson->is_approved = 1;
                $this->info('✅ User approved');
            } else {
                $this->info('✅ User already approved');
            }
            
            // Approve company
            if (!$company->is_user_approved) {
                $company->is_user_approved = 1;
                $company->save();
                $this->info('✅ Company approved');
            } else {
                $this->info('✅ Company already approved');
            }
            
            // Save user
            $dickson->save();
            
            // Display final status
            $this->newLine();
            $this->info('📊 Final Status:');
            $this->table(
                ['Property', 'Value'],
                [
                    ['User ID', $dickson->id],
                    ['Username', $dickson->username],
                    ['Email', $dickson->email],
                    ['Company ID', $dickson->company_id],
                    ['User Approved', $dickson->is_approved ? 'Yes ✅' : 'No ❌'],
                    ['Company Name', $company->company_name],
                    ['Company Approved', $company->is_user_approved ? 'Yes ✅' : 'No ❌'],
                ]
            );
            
            $this->newLine();
            $this->info('✅ Dickson user fix completed successfully!');
            $this->info('You can now login with:');
            $this->info('  Username: Dickson');
            $this->info('  Password: (your password)');
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }
    }
}
