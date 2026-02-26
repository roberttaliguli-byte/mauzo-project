<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\User;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Show admin dashboard with all registered companies.
     */
    public function dashboard()
    {
        $companies = Company::latest()->paginate(10);
        $total = Company::count();

        return view('admin.dashboard', compact('companies', 'total'));
    }

    public function makampuni()
    {
        $companies = Company::all();
        $total = Company::count(); // ADD THIS LINE
        return view('admin.dashboard', compact('companies', 'total')); // ADD $total here
    }

    /**
     * Approve the boss user connected to a company.
     */
    public function approveUser($id)
    {
        $company = Company::findOrFail($id);

        // Approve company user
        $company->is_user_approved = true;
        $company->save();

        // Approve main user (boss)
        $user = User::where('company_id', $company->id)->first();
        if ($user) {
            $user->is_approved = true;
            $user->save();
        }

        return redirect()->back()->with('success', 'Mtumiaji ameidhinishwa kikamilifu!');
    }


/**
 * Set package and time for a company with pricing
 */
public function setPackageTime(Request $request, $id)
{
    $request->validate([
        'package' => 'required|string|in:Free Trial 14 days,30 days,180 days,366 days',
        'start_date' => 'required|date'
    ]);

    $company = Company::findOrFail($id);
    
    $package = $request->package;
    $start_date = Carbon::parse($request->start_date);

    // Define package prices
    $prices = [
        'Free Trial 14 days' => 0,
        '30 days' => 15000,
        '180 days' => 75000,
        '366 days' => 150000
    ];

    // Determine end date based on package
    switch($package){
        case 'Free Trial 14 days':
            $end_date = $start_date->copy()->addDays(14);
            break;
        case '30 days':
            $end_date = $start_date->copy()->addDays(30);
            break;
        case '180 days':
            $end_date = $start_date->copy()->addDays(180);
            break;
        case '366 days':
            $end_date = $start_date->copy()->addDays(366);
            break;
        default:
            $end_date = $start_date;
    }

    // Save package details
    $company->package = $package;
    $company->package_start = $start_date;
    $company->package_end = $end_date;
    $company->save();

    // Calculate discount percentage
    $discount = 0;
    if ($package === '180 days') {
        $monthlyPrice = 15000 * 6; // 6 months at monthly rate = 90,000
        $discount = 15000; // 75,000 vs 90,000 = 15,000 discount (16.67%)
    } elseif ($package === '366 days') {
        $monthlyPrice = 15000 * 12; // 12 months at monthly rate = 180,000
        $discount = 30000; // 150,000 vs 180,000 = 30,000 discount (16.67%)
    }

    $priceMessage = $package !== 'Free Trial 14 days' 
        ? " (Bei: TZS " . number_format($prices[$package]) . ")" 
        : "";
    
    $discountMessage = $discount > 0 
        ? " Umepata punguzo la TZS " . number_format($discount) . "!" 
        : "";

    return redirect()->back()->with('success', 
        "Kifurushi cha {$company->company_name} kimewekwa: {$package}{$priceMessage}.{$discountMessage}"
    );
}
    /**
     * Verify the company.
     */
    public function verifyCompany($id)
    {
        $company = Company::findOrFail($id);

        // Mark company as verified
        $company->is_verified = true;
        $company->save();

        return redirect()->back()->with('success', 'Kampuni imeidhinishwa kikamilifu!');
    }

    /**
     * One-Click Full Approval:
     * Verify company AND approve its user.
     */
    public function approveAll($id)
    {
        $company = Company::findOrFail($id);

        // Approve company
        $company->is_verified = true;
        $company->is_user_approved = true;
        $company->save();

        // Approve user connected to company
        $user = User::where('company_id', $company->id)->first();
        if ($user) {
            $user->is_approved = true;
            $user->save();
        }

        return redirect()->back()->with('success', 'Kampuni na mtumiaji vimeidhinishwa kikamilifu!');
    }

    /**
     * Delete a company.
     */
    public function destroyCompany($id)
    {
        $company = Company::findOrFail($id);
        
        // Delete associated user first (if needed)
        $user = User::where('company_id', $company->id)->first();
        if ($user) {
            $user->delete();
        }
        
        $company->delete();

        return redirect()->back()->with('success', 'Kampuni imefutwa kikamilifu!');
    }

    /**
     * Show change password form.
     */
    public function showChangePassword()
    {
        return view('admin.change-password');
    }

    /**
     * Update admin password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|confirmed|min:6',
        ]);

        $admin = Auth::user();

        // Check current password
        if (!Hash::check($request->current_password, $admin->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        // Update password
        $admin->password = Hash::make($request->new_password);
        $admin->save();

        return back()->with('success', 'Password changed successfully!');
    }

    /**
     * Show company details.
     */
    public function showCompany($id)
    {
        $company = Company::findOrFail($id);
        return view('admin.companies.show', compact('company'));
    }

    /**
     * Get companies with expired packages.
     */
    public function expiredPackages()
    {
        $expiredCompanies = Company::where('package_end_date', '<', Carbon::now())
            ->latest()
            ->paginate(10);

        return view('admin.expired-packages', compact('expiredCompanies'));
    }

    /**
     * Get companies with active packages.
     */
    public function activePackages()
    {
        $activeCompanies = Company::where('package_end_date', '>=', Carbon::now())
            ->latest()
            ->paginate(10);

        return view('admin.active-packages', compact('activeCompanies'));
    }
}