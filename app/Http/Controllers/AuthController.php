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
                ->with('success', 'Umeingia kama Msimamizi!');
        }

        // 2️⃣ Boss/Owner login
        if (Auth::attempt([
            'username' => $credentials['username'],
            'password' => $credentials['password']
        ], $request->boolean('remember'))) {

            $user = Auth::user();

            if (!$user->company_id || !$user->company) {
                Auth::logout();
                return back()->withErrors(['login' => 'Akaunti yako haijaunganishwa na kampuni yoyote. Wasiliana na msimamizi.'])
                    ->onlyInput('username');
            }

            if (!$user->company->is_user_approved) {
                Auth::logout();
                return back()->withErrors(['login' => 'Kampuni yako haijaidhinishwa bado.'])
                    ->onlyInput('username');
            }

            if (!$user->is_approved) {
                Auth::logout();
                return back()->withErrors(['login' => 'Akaunti yako haijaidhinishwa bado.'])
                    ->onlyInput('username');
            }

            $request->session()->regenerate();
            session()->forget('is_admin');

            return redirect()->route('dashboard')
                ->with('success', 'Umeingia kama Mmiliki!');
        }

        // 3️⃣ Employee login
        $mfanyakazi = Wafanyakazi::where('username', $credentials['username'])->first();

        if ($mfanyakazi && Hash::check($credentials['password'], $mfanyakazi->password)) {
            if ($mfanyakazi->getini !== 'ingia') {
                return back()->withErrors(['login' => 'Hauruhusiwi kuingia kwa sasa.'])
                    ->onlyInput('username');
            }

            Auth::guard('mfanyakazi')->login($mfanyakazi);

            // Redirect directly to Mauzo page
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
            return redirect()->route('dashboard') // redirect to dashboard directly
                            ->with('success', 'Neno la siri limebadilishwa kwa mafanikio! Unaingia sasa.');
        }

        return back()->withErrors(['email' => 'Link ya kubadilisha neno la siri si sahihi au imeisha muda wake.']);
    }

    // logout
    public function logout(Request $request)
    {
        Auth::logout();
        session()->forget('is_admin');

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Umetoka kwenye mfumo kwa mafanikio.');
    }
}