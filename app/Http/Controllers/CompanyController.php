<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;


class CompanyController extends Controller
{
    /**
     * Display the company info page.
     */
    public function info()
    {
        // Get first company or create a new instance to avoid null errors
        $company = Company::first() ?? new Company();

        // List of regions
        $regions = ['Dar es Salaam', 'Arusha', 'Mwanza', 'Kilimanjaro'];

        return view('company.info', compact('company', 'regions'));
    }

    /**
     * Update the company info.
     */
    public function update(Request $request)
    {
        // Get first company or create one if it doesn't exist
        $company = Company::first() ?? new Company();

        // Validate incoming data
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'owner_name'   => 'required|string|max:255',
            'owner_dob'    => 'required|date',
            'region'       => 'required|string',
            'location'     => 'required|string|max:255',
            'phone'        => 'required|string|max:20',
            'company_email'=> 'required|email|max:255',
            'tin'          => 'nullable|string|max:100',
            'logo'         => 'nullable|image|max:2048'
        ]);

        // Fill and save company data
        $company->fill($validated);

        // Handle logo upload if provided
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('public/company');
            $company->logo = Storage::url($path);
        }

        $company->save();

        return redirect()
            ->route('company.info')
            ->with('success', 'Taarifa za kampuni zimebadilishwa kwa mafanikio!');
    }
 

}
