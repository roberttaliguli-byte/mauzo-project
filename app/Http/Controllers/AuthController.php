<?php

namespace App\Http\Controllers;

use App\Mail\VerifyEmail;
use App\Mail\NewUserRegistrationNotification;
use App\Models\Company;
use App\Models\User;
use App\Models\Wafanyakazi;
use DateTime;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    /**
     * Show registration form
     */
    public function showRegister(Request $request)
    {
        $regions = [
            "Arusha","Dar es Salaam","Dodoma","Geita","Iringa","Kagera","Katavi",
            "Kigoma","Kilimanjaro","Lindi","Manyara","Mara","Mwanza","Mbeya","Morogoro",
            "Mtwara","Njombe","Pwani","Ruvuma","Rukwa","Shinyanga","simiyu","Singida",
            "Tabora","Tanga","Zanzibar North","Zanzibar South","Zanzibar Urban/West"
        ];

        // Preserve old input for returning to specific step
        $oldInput = $request->old();
        $currentStep = $oldInput ? 1 : 1; // Default to step 1
        
        // Determine which step to return to based on validation errors
        if (session()->has('errors')) {
            $errors = session()->get('errors')->getBag('default');
            
            // Check which fields have errors to determine step
            $step1Fields = ['company_name', 'owner_name', 'owner_gender', 'owner_dob'];
            $step2Fields = ['location', 'region', 'phone', 'company_email'];
            $step3Fields = ['username', 'password'];
            
            foreach ($errors->keys() as $field) {
                if (in_array($field, $step1Fields)) {
                    $currentStep = 1;
                    break;
                } elseif (in_array($field, $step2Fields)) {
                    $currentStep = 2;
                    break;
                } elseif (in_array($field, $step3Fields)) {
                    $currentStep = 3;
                    break;
                }
            }
        }

        return view('auth.register', compact('regions', 'currentStep'));
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
            'company_email' => 'required|email|max:255|unique:users,email',

            'username'     => 'required|string|max:50|unique:users,username',
            'password'     => 'required|string|min:6|confirmed',
        ], [
            'company_email.unique' => 'Barua pepe hii tayari imesajiliwa.',
            'username.unique' => 'Jina la mtumiaji tayari limetumika.',
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
            'email'        => $validated['company_email'],
            'is_user_approved' => 0,
        ]);

        // Generate email verification token
        $token = Str::random(40);

        // Create user
        $user = User::create([
            'company_id'  => $company->id,
            'username'    => $validated['username'],
            'name'        => $validated['owner_name'],
            'email'       => $validated['company_email'],
            'password'    => Hash::make($validated['password']),
            'is_approved' => 0,
            'role'        => 'boss',
            'email_verification_token' => $token,
        ]);

        // Send email verification to user
        if ($user->email) {
            Mail::to($user->email)->send(new VerifyEmail($user));
        }

        // Find admin users and send notification
        $adminUsers = User::where('role', 'admin')->where('is_approved', 1)->get();
        foreach ($adminUsers as $admin) {
            if ($admin->email) {
                Mail::to($admin->email)->send(new NewUserRegistrationNotification($user));
            }
        }

        return redirect()->route('login')
            ->with('success', 'Usajili umekamilika! Tafadhali angalia barua pepe yako kuthibitisha akaunti.');
    }

    /**
     * Handle email verification & auto-approval
     */
    public function verifyEmail($token)
    {
        \Log::info('Verification token received:', ['token' => $token]);
        
        // Find user with this token
        $user = User::where('email_verification_token', $token)->first();
        
        if (!$user) {
            \Log::error('User not found for token:', ['token' => $token]);
            return redirect()->route('login')
                ->with('error', 'Token ya uthibitisho sio sahihi.');
        }
        
        \Log::info('User found:', ['user_id' => $user->id, 'email' => $user->email]);
        
        $user->email_verified_at = now();
        $user->email_verification_token = null;
        $user->is_approved = 1;
        $user->save();

        if ($user->company) {
            $user->company->is_user_approved = 1;
            $user->company->save();
        }

        return redirect()->route('login')
            ->with('success', 'Barua pepe imethibitishwa! Akaunti yako na kampuni yako zimeidhinishwa kiotomatiki.');
    }

    /**
     * Show login form
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    public function loginPost(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // 1️⃣ Admin login (role = 'admin')
        if (Auth::guard('web')->attempt([
            'username' => $credentials['username'],
            'password' => $credentials['password'],
            'role' => 'admin',
            'is_approved' => 1
        ], $request->boolean('remember'))) {

            $user = Auth::guard('web')->user();
            
            // Update login tracking
            $user->update([
                'last_login_at' => now(),
                'last_activity_at' => now(),
                'login_count' => $user->login_count + 1
            ]);

            // Create login history for admin too (for analytics)
            \App\Models\LoginHistory::create([
                'user_id' => $user->id,
                'company_id' => $user->company_id, // Admin may have null company_id
                'login_at' => now(),
                'ip_address' => $request->ip()
            ]);

            $request->session()->regenerate();
            return redirect()->route('admin.dashboard')
                ->with('success', 'Umeingia kama Msimamizi!');
        }

        // 2️⃣ Boss/Owner login (role = 'boss')
        if (Auth::guard('web')->attempt([
            'username' => $credentials['username'],
            'password' => $credentials['password']
        ], $request->boolean('remember'))) {

            $user = Auth::guard('web')->user();

            // Check if user is boss (not admin)
            if ($user->role !== 'boss') {
                Auth::guard('web')->logout();
                return back()->withErrors(['login' => 'Huna ruhusa ya kuingia hapa.'])
                    ->onlyInput('username');
            }

            if (!$user->company_id || !$user->company) {
                Auth::guard('web')->logout();
                return back()->withErrors(['login' => 'Akaunti yako haijaunganishwa na kampuni yoyote. Wasiliana na msimamizi.'])
                    ->onlyInput('username');
            }

            if (!$user->company->is_user_approved) {
                Auth::guard('web')->logout();
                return back()->withErrors(['login' => 'Kampuni yako haijaidhinishwa bado.'])
                    ->onlyInput('username');
            }

            if (!$user->is_approved) {
                Auth::guard('web')->logout();
                return back()->withErrors(['login' => 'Akaunti yako haijaidhinishwa bado.'])
                    ->onlyInput('username');
            }

            // Update login tracking
            $user->update([
                'last_login_at' => now(),
                'last_activity_at' => now(),
                'login_count' => $user->login_count + 1
            ]);

            // Create login history
            \App\Models\LoginHistory::create([
                'user_id' => $user->id,
                'company_id' => $user->company_id,
                'login_at' => now(),
                'ip_address' => $request->ip()
            ]);

            $request->session()->regenerate();
            return redirect()->route('dashboard')
                ->with('success', 'Umeingia kama Mmiliki!');
        }

        // 3️⃣ Employee login (role = 'mfanyakazi')
        if (Auth::guard('mfanyakazi')->attempt([
            'username' => $credentials['username'],
            'password' => $credentials['password']
        ], $request->boolean('remember'))) {

            $mfanyakazi = Auth::guard('mfanyakazi')->user();

            if ($mfanyakazi->getini !== 'ingia') {
                Auth::guard('mfanyakazi')->logout();
                return back()->withErrors(['login' => 'Hauruhusiwi kuingia kwa sasa.'])
                    ->onlyInput('username');
            }

            // Check if employee has company_id
            if (!$mfanyakazi->company_id) {
                Auth::guard('mfanyakazi')->logout();
                return back()->withErrors(['login' => 'Mfanyakazi hana kampuni iliyounganishwa.'])
                    ->onlyInput('username');
            }

            // Update employee login tracking in wafanyakazis table
            $mfanyakazi->update([
                'last_login_at' => now(),
                'last_activity_at' => now(),
                'login_count' => ($mfanyakazi->login_count ?? 0) + 1
            ]);

            // NOTE: We don't create login_history for employees because it references users table
            // If you need employee login history, create a separate table

            $request->session()->regenerate();
            return redirect()->route('mauzo.index')
                ->with('success', 'Umeingia kama Mfanyakazi!');
        }

        // 4️⃣ Login failed
        return back()->withErrors(['login' => 'Jina la mtumiaji au nenosiri sio sahihi.'])
            ->onlyInput('username');
    }

    /**
     * Show forgot password form
     */
    public function showEmailForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send reset link to email
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.exists' => 'Barua pepe haipo kwenye mfumo.'
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', 'Link ya kubadilisha neno la siri imetumwa kwenye barua pepe yako.')
            : back()->withErrors(['email' => 'Imeshindikana kutuma link. Tafadhali jaribu tena baadaye.']);
    }

    /**
     * Show reset password form
     */
    public function showResetForm(string $token)
    {
        return view('auth.reset-password', [
            'token' => $token
        ]);
    }

    /**
     * Reset the password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->password = Hash::make($password);
                $user->setRememberToken(Str::random(60));
                $user->save();

                event(new PasswordReset($user));

                // Optional: auto-login after reset
                Auth::login($user);
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('dashboard')
                            ->with('success', 'Neno la siri limebadilishwa kwa mafanikio! Unaingia sasa.');
        }

        return back()->withErrors(['email' => 'Link ya kubadilisha neno la siri si sahihi au imeisha muda wake.']);
    }

    public function logout(Request $request)
    {
        // Update logout time if user was logged in
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            
            // Update last activity before logout
            $user->update(['last_activity_at' => now()]);
            
            // Update logout time in login history (only for users, not employees)
            \App\Models\LoginHistory::where('user_id', $user->id)
                ->whereNull('logout_at')
                ->latest()
                ->first()
                ?->update(['logout_at' => now()]);
            
            Auth::guard('web')->logout();
        } 
        elseif (Auth::guard('mfanyakazi')->check()) {
            $mfanyakazi = Auth::guard('mfanyakazi')->user();
            
            // Update employee last activity in wafanyakazis table
            if (isset($mfanyakazi->last_activity_at)) {
                $mfanyakazi->update(['last_activity_at' => now()]);
            }
            
            // DO NOT update login_history for employees - it doesn't exist there
            // Remove the LoginHistory query for employees
            
            Auth::guard('mfanyakazi')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Umetoka kwenye mfumo kwa mafanikio.');
    }
}