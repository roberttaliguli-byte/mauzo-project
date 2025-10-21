<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\User;
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

            // User login info
            'username'     => 'required|string|max:50|unique:users,username',
            'password'     => 'required|string|min:6|confirmed',
        ]);

        // ✅ Step 1: Create the company record
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

        // ✅ Step 2: Create the user associated with that company
        $user = User::create([
            'company_id' => $company->id,
            'username'   => $validated['username'],
            'name'       => $validated['owner_name'],
            'email'      => $validated['company_email'] ?? null,
            'password'   => Hash::make($validated['password']),
        ]);

        // ✅ Step 3: Log in the new user automatically
        Auth::login($user);

        // Redirect to dashboard instead of login (user is already logged in)
        return redirect()->route('login')->with('success', 'Umejisajili kwa mafanikio!');
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

        // ✅ Authenticate using username
        if (Auth::attempt([
            'username' => $credentials['username'],
            'password' => $credentials['password']
        ], $request->boolean('remember'))) {

            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Umeingia kwa mafanikio!');
        }

        return back()->withErrors([
            'login' => 'Jina la kuingia au neno la siri si sahihi.',
        ])->onlyInput('username');
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Umetoka kwa mafanikio.');
    }
}
