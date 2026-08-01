<?php

namespace App\Http\Controllers;

use App\Mail\VerifyEmail;
use App\Mail\NewUserRegistrationNotification;
use App\Models\Company;
use App\Models\User;
use App\Models\Wafanyakazi;
use App\Models\LoginHistory;
use DateTime;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Cache\RateLimiter;
use Illuminate\Support\Facades\Http;

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

        $businessTypes = [
            'retail_shop' => 'Retail Shop / Duka',
            'mini_market' => 'Mini Market',
            'supermarket' => 'Supermarket',
            'pharmacy' => 'Pharmacy / Dawa',
            'hardware' => 'Hardware',
            'stationery' => 'Stationery',
            'restaurant' => 'Restaurant',
            'hotel' => 'Hotel',
            'bar' => 'Bar / Vinywaji',
            'clothes_shop' => 'Duka la Nguo',
            'shoes_shop' => 'Duka la Viatu',
            'furniture' => 'Furniture',
            'cosmetics' => 'Cosmetics',
            'electronics' => 'Electronics',
            'salon' => 'Salon / Kinyozi',
            'spare_parts' => 'Spare Parts',
            'wholesale' => 'Jumla / Wholesale',
            'bakery' => 'Bakery',
            'grocery' => 'Grocery',
            'other' => 'Nyingine'
        ];

        $hearAboutUsOptions = [
            'friend' => 'Rafiki',
            'social_media' => 'Social Media',
            'facebook' => 'Facebook',
            'instagram' => 'Instagram',
            'tiktok' => 'TikTok',
            'youtube' => 'YouTube',
            'google' => 'Google Search',
            'whatsapp' => 'WhatsApp',
            'old_system' => 'Nilitumia Mfumo Mwingine',
            'invited' => 'Nimealikwa',
            'advertisement' => 'Tangazo',
            'website' => 'Website',
            'customer_referral' => 'Mteja Aliyenielekeza',
            'event' => 'Event / Maonesho',
            'other' => 'Nyingine'
        ];

        // Preserve old input for returning to specific step
        $oldInput = $request->old();
        $currentStep = $oldInput ? 1 : 1; // Default to step 1
        
        // Determine which step to return to based on validation errors
        if (session()->has('errors')) {
            $errors = session()->get('errors')->getBag('default');
            
            // Check which fields have errors to determine step
            $step1Fields = ['company_name', 'owner_name'];
            $step2Fields = ['location', 'region', 'phone', 'company_email', 'business_type', 'hear_about_us'];
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

        // Generate CSRF token for the form
        $csrfToken = csrf_token();

        return view('auth.register', compact('regions', 'businessTypes', 'hearAboutUsOptions', 'currentStep', 'csrfToken'));
    }

    /**
     * Handle company + user registration with enhanced security
     * Removed time-based restrictions - only shows validation errors
     */
    public function registerPost(Request $request)
    {
        // 1. Honeypot check - Hidden field that bots fill but humans don't see
        if (!empty($request->input('website'))) {
            \Log::warning('Bot detected via honeypot', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            // Return success to confuse bot but don't create account
            return redirect()->route('login')
                ->with('success', 'Usajili umekamilika! Tafadhali angalia barua pepe yako.');
        }
        
        // 2. Enhanced validation with stricter rules - Shows specific errors
        $validated = $request->validate([
            // Step 1
            'company_name' => 'required|string|max:255|min:2',
            'owner_name'   => 'required|string|max:255|min:2|regex:/^[a-zA-Z\s\.\-]+$/',
            // Step 2
            'location'     => 'required|string|max:255|min:2',
            'region'       => 'required|string|max:255|in:Arusha,Dar es Salaam,Dodoma,Geita,Iringa,Kagera,Katavi,Kigoma,Kilimanjaro,Lindi,Manyara,Mara,Mwanza,Mbeya,Morogoro,Mtwara,Njombe,Pwani,Ruvuma,Rukwa,Shinyanga,simiyu,Singida,Tabora,Tanga,Zanzibar North,Zanzibar South,Zanzibar Urban/West',
            'phone'        => [
                'required',
                'string',
                'max:50',
                'regex:/^0[0-9]{9}$/',
                function ($attribute, $value, $fail) {
                    // Block obviously fake numbers
                    if (preg_match('/(.)\1{8,}/', $value)) {
                        $fail('Nambari ya simu sio sahihi.');
                    }
                    // Check for common fake patterns
                    $fakePatterns = ['123456789', '000000000', '111111111', '222222222'];
                    $cleanNumber = preg_replace('/[^0-9]/', '', $value);
                    foreach ($fakePatterns as $pattern) {
                        if (strpos($cleanNumber, $pattern) !== false) {
                            $fail('Nambari ya simu sio sahihi.');
                            return;
                        }
                    }
                }
            ],
            'company_email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
                function ($attribute, $value, $fail) {
                    // Block disposable email domains
                    $domain = substr(strrchr($value, "@"), 1);
                    $disposableDomains = [
                        'mailinator.com', 'guerrillamail.com', '10minutemail.com',
                        'temp-mail.org', 'yopmail.com', 'throwawaymail.com',
                        'fakeinbox.com', 'emailondeck.com', 'e4ward.com',
                        'mailnator.com', 'trashmail.com', 'spambog.com',
                        'spamgourmet.com', 'spamspam.com', 'spamhere.com',
                        'spamherelots.com', 'spamhereplease.com', 'spamhole.com',
                        'spamify.com', 'spam.la', 'spammotel.com', 'spamspot.com',
                        'spamtrail.com', 'spamfree24.com', 'spamfree24.org',
                        'spamfree24.net', 'spamfree24.info', 'spamfree24.de',
                        'spamfree24.eu', 'spamfree24.pl', 'spamfree24.xyz',
                        'guerrillamail.net', 'guerrillamail.biz', 'guerrillamail.org',
                        'mailnator.com', 'trash2009.com', 'trashymail.com',
                        'tyldd.com', 'uggsrock.com', 'wegwerfmail.de',
                        'wegwerfmail.net', 'wegwerfmail.org', 'wh4f.org',
                        'whyspam.me', 'willselfdestruct.com', 'winemaven.info',
                        'wronghead.com', 'wuzup.net', 'xagloo.com',
                        'xemaps.com', 'xents.com', 'xmaily.com',
                        'xoxy.net', 'yep.it', 'yogamaven.com',
                        'yopmail.fr', 'yopmail.net', 'ypmail.webarnak.fr.eu.org',
                        'yuurok.com', 'zehnminutenmail.de', 'zippymail.info',
                        'zoaxe.com', 'zoemail.org', 'zomg.info',
                        'spam4.me', 'spamdecoy.net', 'spamfree.eu',
                        'spamgourmet.com', 'spamhole.com', 'spamify.com',
                        'spam.la', 'spammotel.com', 'spamspot.com',
                        'spamtrail.com', 'spamthis.co.uk', 'spamthisplease.com'
                    ];
                    
                    if (in_array(strtolower($domain), array_map('strtolower', $disposableDomains))) {
                        $fail('Tafadhali tumia barua pepe halisi, si za muda.');
                    }
                    
                    // Check if domain has valid MX records - but only for real domains
                    // Skip MX check for common providers to avoid false positives
                    $commonProviders = ['gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com', 
                                       'icloud.com', 'mail.com', 'protonmail.com', 'zoho.com',
                                       'aol.com', 'live.com', 'msn.com'];
                    
                    if (!in_array(strtolower($domain), $commonProviders)) {
                        // Only check MX for non-common providers
                        if (!checkdnsrr($domain, 'MX')) {
                            $fail('Barua pepe haijathibitishwa. Tafadhali tumia barua pepe halisi.');
                        }
                    }
                }
            ],
            'business_type' => 'required|string|max:100',
            'hear_about_us' => 'required|string|max:100',
            // Step 3
            'username'     => 'required|string|max:50|min:3|unique:users,username|regex:/^[a-zA-Z0-9_]+$/',
            'password'     => 'required|string|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@#$%^&+=!]).+$/',
        ], [
            'company_email.unique' => 'Barua pepe hii tayari imesajiliwa.',
            'username.unique' => 'Jina la mtumiaji tayari limetumika.',
            'business_type.required' => 'Tafadhali chagua aina ya biashara.',
            'hear_about_us.required' => 'Tafadhali chagua umetusikia wapi.',
            'owner_name.regex' => 'Jina linapaswa kuwa na herufi tu.',
            'username.regex' => 'Jina la mtumiaji linapaswa kuwa na herufi, namba au underscore tu.',
            'phone.regex' => 'Nambari ya simu inapaswa kuwa tarakimu 10 kuanzia 0 (Mfano: 0712345678).',
            'password.regex' => 'Neno la siri linapaswa kuwa na angalau herufi kubwa, herufi ndogo, namba na alama maalum (@,#,$,etc).',
            'password.min' => 'Neno la siri linapaswa kuwa na angalau herufi 8.',
            'password.confirmed' => 'Nenosiri halilingani.',
        ]);

        // 3. Check for suspicious input patterns - ONLY for obvious spam/fake names
        if ($this->isSuspiciousInput($validated['owner_name'])) {
            \Log::warning('Suspicious name detected in registration', [
                'ip' => $request->ip(),
                'name' => $validated['owner_name'],
                'username' => $validated['username']
            ]);
            return back()->withErrors([
                'owner_name' => 'Jina halisi linahitajika. Tafadhali jaribu tena.'
            ])->withInput();
        }

        if ($this->isSuspiciousInput($validated['company_name'])) {
            \Log::warning('Suspicious company name detected', [
                'ip' => $request->ip(),
                'company' => $validated['company_name']
            ]);
            return back()->withErrors([
                'company_name' => 'Jina halisi la kampuni linahitajika.'
            ])->withInput();
        }

        // 4. Check for spam emails - ONLY obvious spam, NOT legitimate emails
        if ($this->isSpamEmail($validated['company_email'])) {
            \Log::warning('Spam email detected', [
                'ip' => $request->ip(),
                'email' => $validated['company_email']
            ]);
            return back()->withErrors([
                'company_email' => 'Barua pepe hii inaonekana kuwa ya taka. Tafadhali tumia barua pepe halisi.'
            ])->withInput();
        }

        // Set default package dates for free trial
        $now = Carbon::now();
        $packageEnd = $now->copy()->addDays(14); // 14 days free trial

        // Create the company with default values for gender and dob
        $company = Company::create([
            'company_name' => $validated['company_name'],
            'owner_name'   => $validated['owner_name'],
            'owner_gender' => 'male', // Default value for existing schema
            'owner_dob'    => '2000-01-01', // Default value for existing schema
            'location'     => $validated['location'],
            'region'       => $validated['region'],
            'phone'        => $validated['phone'],
            'email'        => $validated['company_email'],
            'business_type' => $validated['business_type'],
            'hear_about_us' => $validated['hear_about_us'],
            'is_user_approved' => 0,
            'package' => 'Free Trial 14 days',
            'package_start' => $now,
            'package_end' => $packageEnd,
        ]);

        // Generate email verification token
        $token = Str::random(60);

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

        // Log successful registration
        \Log::info('New user registered successfully', [
            'user_id' => $user->id,
            'company_id' => $company->id,
            'email' => $user->email,
            'ip' => $request->ip()
        ]);

        // Send email verification to user
        if ($user->email) {
            try {
                Mail::to($user->email)->send(new VerifyEmail($user));
            } catch (\Exception $e) {
                \Log::error('Failed to send verification email', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $e->getMessage()
                ]);
                // Continue even if email fails - user can request new verification
            }
        }

        // Find admin users and send notification
        $adminUsers = User::where('role', 'admin')->where('is_approved', 1)->get();
        foreach ($adminUsers as $admin) {
            if ($admin->email) {
                try {
                    Mail::to($admin->email)->send(new NewUserRegistrationNotification($user));
                } catch (\Exception $e) {
                    \Log::error('Failed to send admin notification', [
                        'admin_id' => $admin->id,
                        'user_id' => $user->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        return redirect()->route('login')
            ->with('success', 'Usajili umekamilika! Tafadhali angalia barua pepe yako kuthibitisha akaunti.');
    }

    /**
     * Check if input contains suspicious patterns
     * MODIFIED: Less aggressive, only blocks obvious spam
     */
    private function isSuspiciousInput($string)
    {
        $string = strtolower(trim($string));
        
        // If empty or too short
        if (strlen($string) < 2) {
            return true;
        }
        
        // Check for repeated characters (more than 4 in a row)
        if (preg_match('/(.)\1{5,}/', $string)) {
            return true;
        }
        
        // Check for keyboard patterns (qwerty, asdfgh, etc.)
        $keyboardPatterns = ['qwerty', 'asdfgh', 'zxcvbn', 'qwertyuiop', 'asdfghjkl'];
        foreach ($keyboardPatterns as $pattern) {
            if (strpos($string, $pattern) !== false) {
                return true;
            }
        }
        
        // Check for obvious spam terms - but ONLY exact matches or clear spam
        $spamTerms = ['test', 'demo', 'sample', 'example', 'fake', 'dummy'];
        
        // Only block if the string is exactly or mostly the spam term
        foreach ($spamTerms as $term) {
            if ($string === $term) {
                return true;
            }
            // If the string contains the term and is short (likely just the term with minor variation)
            if (strpos($string, $term) !== false && strlen($string) <= strlen($term) + 2) {
                return true;
            }
        }
        
        // Check if string is mostly numbers (more than 80% numbers)
        $digitCount = preg_match_all('/[0-9]/', $string);
        if ($digitCount > 0 && ($digitCount / strlen($string)) > 0.8) {
            return true;
        }
        
        return false;
    }

    /**
     * Check if email is spam
     * MODIFIED: Only blocks obvious spam emails, not legitimate ones
     */
    private function isSpamEmail($email)
    {
        $email = strtolower($email);
        
        // Check for disposable email domains (already handled in validation)
        // This is just an extra check for domains that might have been missed
        
        // Check for obvious spam patterns in the local part
        $localPart = explode('@', $email)[0];
        
        // Block if local part is all numbers and more than 10 digits
        if (preg_match('/^[0-9]{10,}$/', $localPart)) {
            return true;
        }
        
        // Block if local part is pure random gibberish (no vowels, all consonants, longer than 8 chars)
        // This is what blocks "fhhdjskaj" - but we need to be more specific
        // Check if it's truly random gibberish with no recognizable pattern
        if (strlen($localPart) >= 8) {
            // Check if it has at least one vowel
            $vowels = ['a', 'e', 'i', 'o', 'u'];
            $hasVowel = false;
            foreach ($vowels as $vowel) {
                if (strpos($localPart, $vowel) !== false) {
                    $hasVowel = true;
                    break;
                }
            }
            
            // If no vowels AND it's not a common word pattern, it's likely gibberish
            if (!$hasVowel) {
                // Check if it matches common name patterns (has recognizable syllables)
                // This is a simplified check - real names usually have vowels
                return true;
            }
            
            // Check for repeated patterns (like "fhhdjskaj" - has 'h' repeated)
            if (preg_match('/(.)\1{2,}/', $localPart)) {
                return true;
            }
        }
        
        // Block emails from known spammy domains (already in validation, but double-check)
        $spamDomains = [
            'mailinator.com', 'guerrillamail.com', '10minutemail.com',
            'temp-mail.org', 'yopmail.com', 'throwawaymail.com',
            'fakeinbox.com', 'emailondeck.com'
        ];
        
        $domain = substr(strrchr($email, "@"), 1);
        if (in_array($domain, $spamDomains)) {
            return true;
        }
        
        return false;
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
     * Show login form - Don't clear session to preserve flash messages
     */
    public function showLogin()
    {
        // Don't clear session here - this preserves success messages from registration
        // Session clearing happens only during login/logout
        
        return view('auth.login');
    }

    /**
     * Handle login attempts - MODIFIED: Username persists on error
     */
    public function loginPost(Request $request)
    {
        // Clear session before login attempt to prevent data leakage
        // This is safe because we're about to create a new session anyway
        $this->forceClearSession($request);
        
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Store username temporarily in case of failure (for persistence)
        $enteredUsername = $credentials['username'];

        // 1️⃣ Admin login (role = 'admin') - NO TRACKING
        if (Auth::guard('web')->attempt([
            'username' => $credentials['username'],
            'password' => $credentials['password'],
            'role' => 'admin',
            'is_approved' => 1
        ], $request->boolean('remember'))) {

            $user = Auth::guard('web')->user();
            
            // Clear any company-specific cache
            $this->clearAllCompanyCaches();
            
            // Update login tracking (only basic fields, no login history)
            $user->update([
                'last_login_at' => now(),
                'last_activity_at' => now(),
                'login_count' => $user->login_count + 1
            ]);

            $request->session()->regenerate();
            return redirect()->route('admin.dashboard')
                ->with('success', 'Umeingia kama Msimamizi!');
        }

        // 2️⃣ Boss/Owner login (role = 'boss') - WITH TRACKING
        if (Auth::guard('web')->attempt([
            'username' => $credentials['username'],
            'password' => $credentials['password']
        ], $request->boolean('remember'))) {

            $user = Auth::guard('web')->user();

            // Check if user is boss (not admin)
            if ($user->role !== 'boss') {
                Auth::guard('web')->logout();
                return back()->withErrors(['login' => 'Huna ruhusa ya kuingia hapa.'])
                    ->withInput(['username' => $enteredUsername]);
            }

            // Validate boss has company and is approved
            if (!$user->company_id || !$user->company) {
                Auth::guard('web')->logout();
                return back()->withErrors(['login' => 'Akaunti yako haijaunganishwa na kampuni yoyote. Wasiliana na msimamizi.'])
                    ->withInput(['username' => $enteredUsername]);
            }

            if (!$user->company->is_user_approved) {
                Auth::guard('web')->logout();
                return back()->withErrors(['login' => 'Kampuni yako haijaidhinishwa bado.'])
                    ->withInput(['username' => $enteredUsername]);
            }

            if (!$user->is_approved) {
                Auth::guard('web')->logout();
                return back()->withErrors(['login' => 'Akaunti yako haijaidhinishwa bado.'])
                    ->withInput(['username' => $enteredUsername]);
            }

            // Clear company-specific cache for this new login
            $this->clearCompanyCache($user->company_id);
            
            // Update login tracking
            $user->update([
                'last_login_at' => now(),
                'last_activity_at' => now(),
                'login_count' => $user->login_count + 1
            ]);

            // ✅ Create login history for boss
            LoginHistory::create([
                'user_id' => $user->id,
                'mfanyakazi_id' => null,
                'company_id' => $user->company_id,
                'login_at' => now(),
                'ip_address' => $request->ip()
            ]);
            

            $request->session()->regenerate();
            return redirect()->route('dashboard')
                ->with('success', 'Umeingia kama Mmiliki!');
        }

        // 3️⃣ Employee login (role = 'mfanyakazi') - WITH TRACKING AND PACKAGE CHECK
        if (Auth::guard('mfanyakazi')->attempt([
            'username' => $credentials['username'],
            'password' => $credentials['password']
        ], $request->boolean('remember'))) {

            $mfanyakazi = Auth::guard('mfanyakazi')->user();

            if ($mfanyakazi->getini !== 'ingia') {
                Auth::guard('mfanyakazi')->logout();
                return back()->withErrors(['login' => 'Hauruhusiwi kuingia kwa sasa.'])
                    ->withInput(['username' => $enteredUsername]);
            }

            // Check if employee has company_id
            if (!$mfanyakazi->company_id) {
                Auth::guard('mfanyakazi')->logout();
                return back()->withErrors(['login' => 'Mfanyakazi hana kampuni iliyounganishwa.'])
                    ->withInput(['username' => $enteredUsername]);
            }

            // ✅ CHECK COMPANY PACKAGE EXPIRY
            $company = $mfanyakazi->company;
            
            // Check if package_end is null or expired
            if (!$company || is_null($company->package_end) || \Carbon\Carbon::parse($company->package_end)->isPast()) {
                Auth::guard('mfanyakazi')->logout();
                
                return back()->withErrors([
                    'login' => 'Samahani, kifurushi cha kampuni kimeisha muda. Wasiliana na mwajiri wako.'
                ])->withInput(['username' => $enteredUsername]);
            }

            // Clear company-specific cache for this new login
            $this->clearCompanyCache($company->id);

            // Update employee login tracking in wafanyakazis table
            $mfanyakazi->update([
                'last_login_at' => now(),
                'last_activity_at' => now(),
                'login_count' => ($mfanyakazi->login_count ?? 0) + 1
            ]);

            // ✅ Create login history for employee
            LoginHistory::create([
                'user_id' => null,
                'mfanyakazi_id' => $mfanyakazi->id,
                'company_id' => $mfanyakazi->company_id,
                'login_at' => now(),
                'ip_address' => $request->ip()
            ]);

            // Optional: Show warning if package expires soon
            $daysLeft = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($company->package_end), false);
            if ($daysLeft <= 7 && $daysLeft > 0) {
                session()->flash('warning', "Tahadhari: Kifurushi cha kampuni kitaisha muda baada ya siku {$daysLeft}. Tafadhali mjulishe mwajiri wako.");
            }

            $request->session()->regenerate();
            return redirect()->route('mauzo.index')
                ->with('success', 'Umeingia kama Mfanyakazi!');
        }

        // 4️⃣ Login failed - Return with username persisted
        return back()->withErrors(['login' => 'Jina la mtumiaji au nenosiri sio sahihi.'])
            ->withInput(['username' => $enteredUsername]);
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
            'password' => 'required|string|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@#$%^&+=!]).+$/',
        ], [
            'password.regex' => 'Neno la siri linapaswa kuwa na angalau herufi kubwa, herufi ndogo, namba na alama maalum.'
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->password = Hash::make($password);
                $user->setRememberToken(Str::random(60));
                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')
                ->with('success', 'Neno la siri limebadilishwa kwa mafanikio! Tafadhali ingia tena.');
        }

        return back()->withErrors(['email' => 'Link ya kubadilisha neno la siri si sahihi au imeisha muda wake.']);
    }

    /**
     * Handle logout - COMPLETE session and cache clearing
     */
    public function logout(Request $request)
    {
        $companyId = null;
        $userId = null;
        $userRole = null;
        
        // Get user info before logout for cache clearing
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            $userId = $user->id;
            $userRole = $user->role;
            $companyId = $user->company_id;
            
            // Only update login history for bosses (not admins)
            if ($user->role === 'boss') {
                $user->update(['last_activity_at' => now()]);
                
                // ✅ Update logout time in login history
                LoginHistory::where('user_id', $user->id)
                    ->whereNull('logout_at')
                    ->latest('login_at')
                    ->first()
                    ?->update(['logout_at' => now()]);
            } else {
                // Admin - just update basic tracking
                $user->update(['last_activity_at' => now()]);
            }
            
            Auth::guard('web')->logout();
        } 
        // Update logout time for employee if logged in
        elseif (Auth::guard('mfanyakazi')->check()) {
            $mfanyakazi = Auth::guard('mfanyakazi')->user();
            $companyId = $mfanyakazi->company_id;
            $userId = $mfanyakazi->id;
            $userRole = 'mfanyakazi';
            
            // Update employee last activity
            $mfanyakazi->update(['last_activity_at' => now()]);
            
            // ✅ Update logout time in login history for employee
            LoginHistory::where('mfanyakazi_id', $mfanyakazi->id)
                ->whereNull('logout_at')
                ->latest('login_at')
                ->first()
                ?->update(['logout_at' => now()]);
            
            Auth::guard('mfanyakazi')->logout();
        }

        // COMPLETE session cleanup
        $this->completeSessionCleanup($request, $companyId, $userId);
        
        // Clear company-specific cache
        if ($companyId) {
            $this->clearCompanyCache($companyId);
        }
        
        // Clear all user-specific cache
        if ($userId) {
            Cache::forget("user_{$userId}_*");
            Cache::forget("user_permissions_{$userId}");
        }

        return redirect()->route('login')->with('success', 'Umetoka kwenye mfumo kwa mafanikio.');
    }

    /**
     * Force clear all session data - used during login and logout
     */
    private function forceClearSession(Request $request)
    {
        // Logout from all guards if somehow still authenticated
        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
        }
        if (Auth::guard('mfanyakazi')->check()) {
            Auth::guard('mfanyakazi')->logout();
        }
        
        // Clear all session data
        Session::flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Clear all cookies except essential Laravel cookies
        foreach ($_COOKIE as $key => $value) {
            if (!in_array($key, ['XSRF-TOKEN', 'laravel_session'])) {
                setcookie($key, '', time() - 3600, '/');
            }
        }
    }

    /**
     * Complete session cleanup for logout
     */
    private function completeSessionCleanup(Request $request, $companyId = null, $userId = null)
    {
        // Get all session keys
        $session = $request->session();
        
        // System keys to keep (minimal)
        $keepKeys = ['_token'];
        
        // Get all session keys
        $allKeys = $session->all();
        
        // Remove all user/company-specific data
        foreach (array_keys($allKeys) as $key) {
            if (!in_array($key, $keepKeys) && !str_starts_with($key, '_')) {
                $session->forget($key);
            }
        }
        
        // Clear specific session keys that might contain company data
        $keysToClear = [
            'company_name', 'company_id', 'user_role', 'permissions',
            'cart', 'kikapu', 'temp_data', 'last_activity',
            'url', 'previous_url', 'intended'
        ];
        
        foreach ($keysToClear as $key) {
            if ($session->has($key)) {
                $session->forget($key);
            }
        }
        
        // Clear any company-specific flash data
        $session->flash('company_data', null);
    }

    /**
     * Clear company-specific cache
     */
    private function clearCompanyCache($companyId)
    {
        if (!$companyId) return;
        
        // Clear common company cache keys
        $cachePatterns = [
            "company_{$companyId}_*",
            "company_data_{$companyId}",
            "company_products_{$companyId}",
            "company_customers_{$companyId}",
            "company_sales_{$companyId}",
            "company_expenses_{$companyId}",
            "company_reports_{$companyId}",
            "company_dashboard_{$companyId}"
        ];
        
        foreach ($cachePatterns as $pattern) {
            Cache::forget($pattern);
        }
    }

    /**
     * Clear all company caches (for admin login)
     */
    private function clearAllCompanyCaches()
    {
        // Don't clear everything, just admin-related caches
        Cache::forget('admin_dashboard_stats');
        Cache::forget('admin_companies_list');
        Cache::forget('admin_reports_data');
    }

    /**
     * Check session status (AJAX endpoint)
     */
    public function checkSession(Request $request)
    {
        return response()->json([
            'authenticated' => Auth::check() || Auth::guard('mfanyakazi')->check(),
            'timestamp' => now()->toIso8601String()
        ]);
    }

    /**
     * Cleanup session (AJAX endpoint for beforeunload)
     */
    public function cleanupSession(Request $request)
    {
        // This is called via beacon on page unload
        // Just log or do minimal cleanup
        if (Auth::check()) {
            $user = Auth::user();
            $user->update(['last_activity_at' => now()]);
        }
        
        return response()->json(['success' => true]);
    }
}