<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;

class ProfileController extends Controller
{
    /**
     * Show the change password form
     */
    public function editPassword()
    {
        return view('auth.change-password');
    }

    /**
     * Display company information page with proper error handling
     */
    public function companyInfo()
    {
        // Get authenticated user
        $user = Auth::user();
        
        // Initialize company variable
        $company = null;
        
        // Check if user has company relation
        if ($user && method_exists($user, 'company') && $user->company) {
            $company = $user->company;
        }
        
        // If no company found via relation, try to get by company_id
        if (!$company && $user && $user->company_id) {
            $company = Company::find($user->company_id);
        }
        
        // If still no company, create empty instance to avoid null errors
        if (!$company) {
            $company = new Company();
        }

        // Complete list of Tanzanian regions
        $regions = [
            "Arusha", "Dar es Salaam", "Dodoma", "Geita", "Iringa", "Kagera", "Katavi",
            "Kigoma", "Kilimanjaro", "Lindi", "Manyara", "Mara", "Mbeya", "Morogoro",
            "Mtwara", "Njombe", "Pwani", "Ruvuma", "Rukwa", "Shinyanga", "Singida",
            "Simiyu", "Songwe", "Tabora", "Tanga", "Zanzibar North", "Zanzibar South", 
            "Zanzibar Urban/West"
        ];

        return view('company.info', compact('company', 'regions'));
    }

    /**
     * Update profile information (if needed)
     */
    public function update(Request $request)
    {
        // Add profile update logic here if needed
    }
}