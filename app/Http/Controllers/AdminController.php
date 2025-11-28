<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\User;
use Carbon\Carbon; // ← MUST import Carbon

class AdminController extends Controller
{
    /**
     * Show admin dashboard with all registered companies.
     */
public function dashboard()
{
    // Use paginate() instead of get()
    $companies = Company::latest()->paginate(10); // 10 per page, adjust as needed
    $total = Company::count(); // total companies

    return view('admin.dashboard', compact('companies', 'total'));
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
     * Set package and time for a company.
     */
    public function setPackageTime(Request $request, $id)
    {
        $company = Company::findOrFail($id);
        
        $package = $request->package;
        $start_date = Carbon::parse($request->start_date);

        // Determine end date based on package
        switch($package){
            case 'Free Trial 14 days':
                $end_date = $start_date->copy()->addDays(14);
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

        $company->package = $package;
        $company->package_start = $start_date;
        $company->package_end = $end_date;
        $company->save();

        return redirect()->back()->with('success', "Kifurushi cha {$company->company_name} kimewekwa!");
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
    public function destroy($id)
    {
        $company = Company::findOrFail($id);
        $company->delete();

        return redirect()->back()->with('success', 'Kampuni imefutwa kikamilifu!');
    }
}
