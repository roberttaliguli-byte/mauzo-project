<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\User;

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
     * Approve the boss user connected to a company.
     */
    public function approveUser($id)
    {
        $company = Company::findOrFail($id);

        // Mark user as approved
        $company->is_user_approved = true;
        $company->save();

        return redirect()->back()->with('success', 'Mtumiaji ameidhinishwa kikamilifu!');
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

        // Verify company
        $company->is_verified = true;
        $company->is_user_approved = true;
        $company->save();

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
