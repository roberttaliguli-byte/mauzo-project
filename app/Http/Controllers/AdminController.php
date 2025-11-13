<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;

class AdminController extends Controller
{
    /**
     * Show admin dashboard with all registered companies.
     */
    public function dashboard()
    {
        $companies = Company::latest()->get();
        $total = $companies->count();

        return view('admin.dashboard', compact('companies', 'total'));
    }

    /**
     * Verify a company registration.
     */
 

public function verify($id)
{
    $company = Company::findOrFail($id);

    // Verify company
    $company->update(['is_verified' => true]);

    // Approve the user linked to the company
    $user = $company->user ?? \App\Models\User::where('company_id', $company->id)->first();
    if ($user) {
        $user->update(['is_approved' => true]);
    }

    return back()->with('success', 'Kampuni na akaunti yake vimeidhinishwa kikamilifu!');
}

public function approveUser($id)
{
    $company = Company::findOrFail($id);
    $company->is_user_approved = true;
    $company->save();

    return redirect()->back()->with('success', 'User approved successfully!');
}


    /**
     * Delete a company.
     */
    public function destroy($id)
    {
        $company = Company::findOrFail($id);
        $company->delete();

        return back()->with('success', 'Kampuni imefutwa kikamilifu!');
    }
}
