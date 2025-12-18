<?php
/**
 * Script to fix Dickson user authentication issue
 * This script:
 * 1. Creates a company for Dickson if needed
 * 2. Updates Dickson user with company_id and is_approved = 1
 */

// This is a helper script - run it via tinker or artisan command
// php artisan tinker
// Then paste the code below

use App\Models\User;
use App\Models\Company;

// Find Dickson user
$dickson = User::where('username', 'Dickson')->first();

if ($dickson) {
    // Check if company exists
    if (!$dickson->company_id) {
        // Create a company for Dickson
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
        
        $dickson->company_id = $company->id;
        echo "✅ Company created with ID: " . $company->id . "\n";
    } else {
        // Update existing company to be approved
        $company = $dickson->company;
        $company->is_user_approved = 1;
        $company->save();
        echo "✅ Company updated (ID: " . $company->id . ")\n";
    }
    
    // Update user
    $dickson->is_approved = 1;
    $dickson->save();
    
    echo "✅ User 'Dickson' updated successfully!\n";
    echo "   - company_id: " . $dickson->company_id . "\n";
    echo "   - is_approved: " . $dickson->is_approved . "\n";
    echo "   - Company is_user_approved: " . $dickson->company->is_user_approved . "\n";
} else {
    echo "❌ User 'Dickson' not found\n";
}
