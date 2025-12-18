<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\User;
use App\Models\Wafanyakazi;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use App\Mail\VerifyEmail;

class AuthController extends Controller
{
    /**
     * Show registration form
     */
    public function showRegister()
    {
        $regions = [
            "Arusha","Dar es Salaam","Dodoma","Geita","Iringa","Kagera","Katavi",
            "Kigoma","Kilimanjaro","Lindi","Manyara","Mara","mwanza","Mbeya","Morogoro",
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

        // 1️⃣ Create the company
        $company = Company::create([
            'company_name' => $validated['company_name'],
            'owner_name'   => $validated['owner_name'],
            'owner_gender' => $validated['owner_gender'],
            'owner_dob'    => $validated['owner_dob'],
            'location'     => $validated['location'],
            'region'       => $validated['region'],
            'phone'        => $validated['phone'],
            'email'        => $validated['company_email'] ?? null,
            'is_user_approved' => 0, // admin can override later
        ]);

        // 2️⃣ Generate email verification token
        $token = Str::random(40);

        // 3️⃣ Create user
        $user = User::create([
            'company_id'  => $company->id,
            'username'    => $validated['username'],
            'name'        => $validated['owner_name'],
            'email'       => $validated['company_email'] ?? null,
            'password'    => Hash::make($validated['password']),
            'is_approved' => 0, // will be approved on email verification
            'role'        => 'boss',
            'email_verification_token' => $token,
        ]);

        // 4️⃣ Send email verification if email exists
        if ($user->email) {
            Mail::to($user->email)->send(new VerifyEmail($user));
            
        }

        return redirect()->route('login')
            ->with('success', 'Registration successful! Please check your email to verify your account.');
    }

    /**
     * Handle email verification & auto-approval
     */
    public function verifyEmail($token)
    {
        $user = User::where('email_verification_token', $token)->firstOrFail();

        // 1️⃣ Mark email as verified
        $user->email_verified_at = now();
        $user->email_verification_token = null;

        // 2️⃣ Auto-approve user
        $user->is_approved = 1;
        $user->save();

        // 3️⃣ Auto-approve associated company
        if ($user->company) {
            $user->company->is_user_approved = 1;
            $user->company->save();
        }

        return redirect()->route('login')
            ->with('success', 'Email verified! Your account and company have been automatically approved.');
    }

    /**
     * Show login form
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle login for all types
     */
    public function loginPost(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
       // 1️⃣ Admin login
if (Auth::attempt([
    'username' => $credentials['username'],
    'password' => $credentials['password'],
    'role' => 'admin',
    'is_approved' => 1
], $request->boolean('remember'))) {

    session(['is_admin' => true]);

    return redirect()->route('admin.dashboard')
        ->with('success', 'Logged in as Admin!');
}

 
        // 2️⃣ Boss/Owner login
        if (Auth::attempt([
            'username' => $credentials['username'],
            'password' => $credentials['password']
        ], $request->boolean('remember'))) {

            $user = Auth::user();

            if (!$user->company_id || !$user->company) {
                Auth::logout();
                return back()->withErrors(['login' => 'Your account is not linked to any company. Contact admin.'])
                    ->onlyInput('username');
            }

            if (!$user->company->is_user_approved) {
                Auth::logout();
                return back()->withErrors(['login' => 'Your company is not approved yet.'])
                    ->onlyInput('username');
            }

            if (!$user->is_approved) {
                Auth::logout();
                return back()->withErrors(['login' => 'Your account is not approved yet.'])
                    ->onlyInput('username');
            }

            $request->session()->regenerate();
            session()->forget('is_admin');

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Login successful!');
        }

        // 3️⃣ Employee login
        $mfanyakazi = Wafanyakazi::where('username', $credentials['username'])->first();

        if ($mfanyakazi && Hash::check($credentials['password'], $mfanyakazi->password)) {
            if ($mfanyakazi->getini !== 'ingia') {
                return back()->withErrors(['login' => 'You are currently not allowed to login.'])
                    ->onlyInput('username');
            }

            Auth::guard('mfanyakazi')->login($mfanyakazi);
            return redirect()->route('mfanyakazi.dashboard')
                ->with('success', 'Logged in as Employee!');
        }

        // 4️⃣ Login failed
        return back()->withErrors(['login' => 'Incorrect username or password.'])
            ->onlyInput('username');
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        session()->forget('is_admin');

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Logged out successfully.');
    }
}




