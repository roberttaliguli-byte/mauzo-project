<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\User;
use App\Models\Wafanyakazi; // ✅ Added for employee login
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    /**
     * Show registration form
     */
    public function showRegister()
    {
        $regions = [
            "Arusha","Dar es Salaam","Dodoma","Geita","Iringa","Kagera","Katavi",
            "Kigoma","Kilimanjaro","Lindi","Manyara","Mara","Mbeya","Morogoro",
            "Mtwara","Njombe","Pwani","Ruvuma","Rukwa","Shinyanga","Singida",
            "Tabora","Tanga","Zanzibar North","Zanzibar South","Zanzibar Urban/West"
        ];

        return view('auth.register', compact('regions'));
    }

    /**
     * Handle company + user registration
     */
    public function registerPost(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'owner_name'   => 'required|string|max:255',
            'owner_gender' => ['required', Rule::in(['male', 'female', 'other'])],
            'owner_dob'    => 'required|date',
            'location'     => 'required|string|max:255',
            'region'       => 'required|string|max:255',
            'phone'        => 'required|string|max:50',
            'company_email'=> 'nullable|email|max:255',
            'username'     => 'required|string|max:50|unique:users,username',
            'password'     => 'required|string|min:6|confirmed',
        ]);

        // Create the company
        $company = Company::create([
            'company_name' => $validated['company_name'],
            'owner_name'   => $validated['owner_name'],
            'owner_gender' => $validated['owner_gender'],
            'owner_dob'    => $validated['owner_dob'],
            'location'     => $validated['location'],
            'region'       => $validated['region'],
            'phone'        => $validated['phone'],
            'email'        => $validated['company_email'] ?? null,
        ]);

        // Create the user (must be approved by admin)
        $user = User::create([
            'company_id'  => $company->id,
            'username'    => $validated['username'],
            'name'        => $validated['owner_name'],
            'email'       => $validated['company_email'] ?? null,
            'password'    => Hash::make($validated['password']),
            'is_approved' => false,
            'role'        => 'boss', // ✅ Set role
        ]);

        return redirect()->route('login')->with('success', 'Umejisajili kwa mafanikio! Subiri admin kuthibitisha akaunti yako.');
    }

    /**
     * Show login form
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle login using username & password
     */
    public function loginPost(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Hardcoded admin login
        if ($credentials['username'] === 'admin' && $credentials['password'] === 'admin123') {
            session(['is_admin' => true]);
            return redirect()->route('admin.dashboard')
                ->with('success', 'Umeingia kama Admin!');
        }

        // 1️⃣ Normal boss user login
        if (Auth::attempt([
            'username' => $credentials['username'],
            'password' => $credentials['password']
        ], $request->boolean('remember'))) {

            $user = Auth::user();

            // Check if user has a company
            if (!$user->company_id) {
                Auth::logout();

                return back()->withErrors([
                    'login' => 'Akaunti yako haina kampuni iliyohusishwa. Tafadhali wasiliana na admin.'
                ])->onlyInput('username');
            }

            // Check if user is approved
            if (!$user->company->is_user_approved) {
                Auth::logout();

                return back()->withErrors([
                    'login' => 'Akaunti yako bado haijathibitishwa. Tafadhali subiri admin akamilishe mchakato wa uidhinishaji wa kampuni yako.'
                ])->onlyInput('username');
            }
            

            $request->session()->regenerate();
            session()->forget('is_admin');

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Umeingia kwa mafanikio!');
        }
// mfanyakazi login

$mfanyakazi = Wafanyakazi::where('username', $credentials['username'])->first();

if ($mfanyakazi && Hash::check($credentials['password'], $mfanyakazi->password)) {

    // Only allow login if getini = 'ingia'
    if ($mfanyakazi->getini !== 'ingia') {
        return back()->withErrors([
            'login' => 'Hujaruhusiwa kuingia kwa sasa.'
        ])->onlyInput('username');
    }

    // Login mfanyakazi
    
    Auth::guard('mfanyakazi')->login($mfanyakazi);
    


    return redirect()->route('mfanyakazi.dashboard')
        ->with('success', 'Umeingia kama Mfanyakazi!');

        
}

// If login fails
return back()->withErrors([
    'login' => 'Jina la kuingia au neno la siri si sahihi.'
])->onlyInput('username');
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        session()->forget('is_admin');

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Umetoka kwa mafanikio.');
    }
}
