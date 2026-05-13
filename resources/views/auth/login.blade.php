<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MauzoSheetAI | Ingia kwenye Mfumo</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #b45309 0%, #4d2401 100%);
            min-height: 100vh;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem;
        }

        /* Glass card effect - compact */
        .glass-card {
            background: rgba(250, 248, 248, 0.97);
            backdrop-filter: blur(4px);
            border: 1px solid rgba(245, 243, 241, 0.3);
            box-shadow: 0 12px 25px -10px rgba(0, 0, 0, 0.12);
        }

        /* Gold gradient button */
        .btn-gold {
            background: linear-gradient(100deg, #d97706, #b45309);
            transition: all 0.25s ease;
        }

        .btn-gold:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 18px -6px rgba(217, 119, 6, 0.4);
        }

        .animate-fade-in {
            animation: fadeIn 0.35s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-6px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Form input styling */
        .form-input {
            transition: all 0.2s ease;
        }

        .form-input:focus {
            border-color: #d97706;
            outline: none;
            box-shadow: 0 0 0 2px #fed7aa;
        }

        /* Mobile-optimized top bar - compact */
        .mobile-top-bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 30;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
            padding: 0.6rem 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* Bottom fixed bar - compact */
        .mobile-bottom-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 30;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(12px);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: 0.5rem 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.4rem;
            font-size: 0.65rem;
        }

        /* Brand styling - compact */
        .brand-container {
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .brand-logo {
            height: 32px;
            width: auto;
            border-radius: 8px;
            background: white;
            padding: 2px;
        }

        .brand-text {
            font-weight: 700;
            color: white;
            letter-spacing: -0.2px;
            font-size: 0.9rem;
        }

        /* Home link - compact */
        .home-link {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 40px;
            padding: 0.35rem 0.85rem;
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.75rem;
            font-weight: 600;
            color: #78350f;
            transition: all 0.2s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .home-link i {
            font-size: 0.75rem;
            color: #d97706;
        }

        /* Bottom badges - compact */
        .copyright-badge, .tech-badge {
            display: flex;
            align-items: center;
            gap: 5px;
            border-radius: 40px;
            padding: 0.25rem 0.75rem;
        }

        .copyright-badge {
            background: rgba(255, 255, 255, 0.85);
            color: #451a03;
        }

        .tech-badge {
            background: #1c1917e6;
            border: 1px solid #f59e0b30;
        }

        .tech-badge span {
            color: #fde68a;
            font-weight: 500;
        }

        /* Main card container - COMPACT SIZE for mobile */
        .login-card-wrapper {
            width: 100%;
            max-width: 20rem;
            margin: 4rem auto 4.8rem;
            position: relative;
            z-index: 10;
        }

        /* Card inner padding reduced */
        .glass-card {
            padding: 1.2rem !important;
        }

        /* Logo area compact */
        .logo-area {
            margin-bottom: 0.75rem !important;
        }
        
        .logo-circle {
            width: 3rem;
            height: 3rem;
            margin-bottom: 0.5rem !important;
        }
        
        .logo-img-small {
            height: 2rem;
            width: auto;
        }

        h1 {
            font-size: 1.35rem !important;
        }
        
        .subtitle {
            font-size: 0.7rem !important;
            margin-top: 0.2rem !important;
        }

        /* Form spacing reduced */
        label {
            font-size: 0.7rem !important;
            margin-bottom: 0.25rem !important;
        }
        
        input {
            padding: 0.6rem 0.75rem !important;
            font-size: 0.8rem !important;
        }
        
        .btn-gold {
            padding: 0.5rem 1rem !important;
            font-size: 0.8rem !important;
        }
        
        .footer-link {
            margin-top: 0.75rem !important;
            padding-top: 0.75rem !important;
            font-size: 0.7rem !important;
        }
        
        /* Alert messages compact */
        .alert-message {
            padding: 0.5rem 0.75rem !important;
            margin-bottom: 0.75rem !important;
            font-size: 0.7rem !important;
        }
        
        /* Icons slightly smaller */
        .fa, .fas, .far {
            font-size: 0.75rem;
        }
        
        label i {
            margin-right: 0.25rem;
        }
        
        .checkbox-label {
            font-size: 0.7rem !important;
        }
        
        .forgot-link, .register-link {
            font-size: 0.7rem !important;
        }

        /* Responsive adjustments */
        @media (max-width: 480px) {
            .login-card-wrapper {
                max-width: 18.5rem;
            }
            
            .glass-card {
                padding: 1rem !important;
            }
            
            .brand-text {
                font-size: 0.8rem;
            }
            
            .home-link span {
                display: inline;
            }
            
            .mobile-bottom-bar {
                flex-direction: row;
                justify-content: space-between;
            }
            
            .copyright-badge, .tech-badge {
                padding: 0.2rem 0.6rem;
                font-size: 0.6rem;
            }
        }
        
        @media (max-width: 380px) {
            .login-card-wrapper {
                max-width: 17rem;
            }
            
            .home-link span {
                display: none;
            }
            
            .home-link {
                padding: 0.35rem 0.7rem;
            }
            
            .brand-text {
                font-size: 0.75rem;
            }
        }
        
        /* Link hover effect */
        .hover-link:hover {
            color: #d97706;
        }
    </style>
</head>
<body>

    <!-- ========================================= -->
    <!-- FIXED MOBILE-OPTIMIZED TOP BAR            -->
    <!-- ========================================= -->
    <div class="mobile-top-bar">
        <div class="brand-container">
            <img src="{{ asset('logo11.jpg') }}" alt="MauzoSheetAI Logo" class="brand-logo" onerror="this.src='https://placehold.co/60x60/d97706/white?text=M'; this.onerror=null;">
            <span class="brand-text">MauzoSheetAI</span>
        </div>
        <a href="{{ route('landing') }}" class="home-link">
            <i class="fas fa-home"></i>
            <span>Nyumbani</span>
        </a>
    </div>

    <!-- ========================================= -->
    <!-- FIXED BOTTOM BAR (copyright + tech)       -->
    <!-- ========================================= -->
    <div class="mobile-bottom-bar">
        <div class="copyright-badge">
            <i class="far fa-copyright text-amber-600 text-xs"></i>
            <span>2025 MauzoSheetAI</span>
        </div>
        <div class="tech-badge">
            <i class="fas fa-microchip text-amber-400 text-xs"></i>
            <span>Black Sciences Technology</span>
        </div>
    </div>

    <!-- ========================================= -->
    <!-- COMPACT LOGIN CARD                        -->
    <!-- ========================================= -->
    <div class="login-card-wrapper">
        <div class="glass-card rounded-2xl shadow-xl animate-fade-in">
            
            <!-- Header with logo - compact -->
            <div class="text-center logo-area">
                <div class="logo-circle w-12 h-12 bg-gradient-to-br from-amber-100 to-orange-100 rounded-xl flex items-center justify-center mx-auto shadow-sm border border-amber-200">
                    <img src="{{ asset('logo11.jpg') }}" alt="MauzoSheetAI" class="logo-img-small h-8 w-auto rounded-lg" onerror="this.src='https://placehold.co/50x50/d97706/white?text=M'">
                </div>
                <h1 class="text-xl font-extrabold text-gray-800 tracking-tight mt-1">Karibu Tena</h1>
                <p class="subtitle text-gray-500 text-xs">Ingia kwenye akaunti yako</p>
            </div>

            <!-- Success Alert - compact -->
            @if(session('success'))
                <div id="success-alert" class="alert-message mb-3 p-2 rounded-lg bg-gradient-to-r from-emerald-50 to-green-50 border border-emerald-200 text-emerald-800 text-xs animate-fade-in">
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-1.5">
                            <i class="fas fa-check-circle text-emerald-600 text-xs"></i>
                            <p class="leading-relaxed">{{ session('success') }}</p>
                        </div>
                        <button onclick="this.parentElement.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 transition"><i class="fas fa-times text-xs"></i></button>
                    </div>
                </div>
                <script>setTimeout(() => { let el = document.getElementById('success-alert'); if(el) el.remove(); }, 5000);</script>
            @endif

            <!-- Error Alert - compact -->
            @if($errors->has('login') || session('error'))
                <div class="alert-message mb-3 p-2 rounded-lg bg-gradient-to-r from-red-50 to-rose-50 border border-red-200 text-red-700 text-xs animate-fade-in">
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-1.5">
                            <i class="fas fa-exclamation-triangle text-red-500 text-xs"></i>
                            <p class="leading-relaxed">{{ $errors->first('login') ?? session('error') }}</p>
                        </div>
                        <button onclick="this.parentElement.parentElement.remove()" class="text-red-600 hover:text-red-800 transition"><i class="fas fa-times text-xs"></i></button>
                    </div>
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('login.post') }}" class="space-y-3" autocomplete="off">
                @csrf

                <!-- Username field -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">
                        <i class="fas fa-user text-amber-600 mr-1"></i>Jina la Mtumiaji
                    </label>
                    <input type="text" name="username" value="{{ old('username') }}" required autofocus
                           placeholder="Weka jina lako"
                           class="form-input w-full rounded-lg border border-gray-200 bg-white/90 text-gray-800 px-3 py-2 text-sm focus:ring-2 focus:ring-amber-400 focus:border-amber-400 outline-none shadow-sm">
                    @error('username')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password field -->
                <div>
                    <div class="flex justify-between mb-1">
                        <label class="text-xs font-semibold text-gray-700">
                            <i class="fas fa-key text-amber-600 mr-1"></i>Neno la Siri
                        </label>
                        <a href="{{ route('password.request') }}" class="forgot-link text-xs text-amber-600 hover:text-amber-700 font-medium transition">Umesahau?</a>
                    </div>
                    <input type="password" name="password" required
                           placeholder="Weka neno la siri"
                           class="form-input w-full rounded-lg border border-gray-200 bg-white/90 text-gray-800 px-3 py-2 text-sm focus:ring-2 focus:ring-amber-400 focus:border-amber-400 outline-none shadow-sm">
                </div>

                <!-- Remember me & register link row -->
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <label class="checkbox-label flex items-center gap-1.5 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-3.5 h-3.5 rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                        <span class="text-xs text-gray-700">Kumbuka mimi</span>
                    </label>
                    <a href="{{ route('register') }}" class="register-link text-xs text-amber-600 font-semibold hover:text-amber-700 transition flex items-center gap-1">
                        Sajili <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>

                <!-- Submit button -->
                <button type="submit" class="btn-gold w-full py-2 rounded-lg text-white font-bold text-sm flex items-center justify-center gap-2 shadow-md mt-1">
                    <i class="fas fa-sign-in-alt text-xs"></i> Ingia Sasa
                </button>
            </form>

            <!-- Back to landing helper link - compact -->
            <div class="footer-link text-center mt-2 pt-2 border-t border-gray-100">
                <a href="{{ route('landing') }}" class="text-gray-500 hover:text-amber-600 text-xs transition flex items-center justify-center gap-1">
                    <i class="fas fa-arrow-left text-xs"></i> Rudi kwenye Mwanzo
                </a>
            </div>
        </div>
    </div>

    <!-- Script for clearing form autofill + replaceState fix -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Clear any lingering pre-filled values on username/password for security
            const usernameInput = document.querySelector('input[name="username"]');
            const passwordInput = document.querySelector('input[name="password"]');
            if (usernameInput && !usernameInput.value.trim()) {
                usernameInput.value = '';
            }
            if (passwordInput) {
                passwordInput.value = '';
            }
            // Prevent resubmission on refresh using replaceState
            if (window.history.replaceState) {
                window.history.replaceState(null, null, window.location.href);
            }

            // Add touch-friendly feedback for buttons on mobile
            const btns = document.querySelectorAll('button, .home-link, .footer-link a');
            btns.forEach(btn => {
                btn.addEventListener('touchstart', function() {
                    this.style.opacity = '0.8';
                }, { passive: true });
                btn.addEventListener('touchend', function() {
                    this.style.opacity = '1';
                });
            });
        });
    </script>
</body>
</html>