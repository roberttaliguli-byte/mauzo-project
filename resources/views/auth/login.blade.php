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
            padding: 1rem;
        }

        /* Glass card effect */
        .glass-card {
            background: rgba(250, 248, 248, 0.96);
            backdrop-filter: blur(4px);
            border: 1px solid rgba(245, 243, 241, 0.3);
            box-shadow: 0 20px 35px -12px rgba(0, 0, 0, 0.15);
        }

        /* Gold gradient button */
        .btn-gold {
            background: linear-gradient(100deg, #d97706, #b45309);
            transition: all 0.25s ease;
        }

        .btn-gold:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -8px rgba(217, 119, 6, 0.5);
        }

        /* Input focus styling */
        .input-focus:focus {
            ring: 2px solid #f59e0b;
            border-color: #f59e0b;
        }

        /* Fade in animation */
        .animate-fade-in {
            animation: fadeIn 0.4s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Form input styling */
        .form-input:focus {
            border-color: #d97706;
            ring: 2px solid #fef3c7;
        }

        /* Mobile-optimized header bar - unified top row */
        .mobile-top-bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 30;
            background: rgba(0, 0, 0, 0.65);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* Bottom info bar - fixed on mobile for easy access */
        .mobile-bottom-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 30;
            background: rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(12px);
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            padding: 0.6rem 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.5rem;
            font-size: 0.7rem;
        }

        /* Logo + brand area */
        .brand-container {
            display: flex;
            align-items: center;
            gap: 0.7rem;
        }

        .brand-logo {
            height: 36px;
            width: auto;
            border-radius: 10px;
            background: white;
            padding: 2px;
        }

        .brand-text {
            font-weight: 700;
            color: white;
            letter-spacing: -0.2px;
            font-size: 1rem;
        }

        /* Home link button refined */
        .home-link {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 40px;
            padding: 0.4rem 0.9rem;
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #78350f;
            transition: all 0.2s ease;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .home-link i {
            font-size: 0.8rem;
            color: #d97706;
        }

        .home-link:hover {
            background: white;
            transform: scale(1.02);
        }

        /* Bottom elements styling */
        .copyright-badge, .tech-badge {
            display: flex;
            align-items: center;
            gap: 6px;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            border-radius: 40px;
            padding: 0.3rem 0.9rem;
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

        /* Card container spacing */
        .login-card-wrapper {
            width: 100%;
            max-width: 28rem;
            margin: 4.5rem auto 5rem;
            position: relative;
            z-index: 10;
        }

        /* Responsive tweaks for very small screens */
        @media (max-width: 500px) {
            .mobile-bottom-bar {
                flex-direction: column;
                text-align: center;
                gap: 0.4rem;
                padding: 0.5rem 0.8rem;
            }

            .brand-text {
                font-size: 0.85rem;
            }

            .home-link span {
                display: inline;
            }

            .login-card-wrapper {
                margin: 5rem auto 5.5rem;
            }

            .glass-card {
                padding: 1.5rem;
            }
        }

        @media (max-width: 380px) {
            .home-link span {
                display: none;
            }

            .home-link {
                padding: 0.4rem 0.7rem;
            }

            .brand-text {
                font-size: 0.8rem;
            }
        }

        /* hover effect for rudi link inside card */
        .rudi-link-inline {
            transition: all 0.2s ease;
        }

        .rudi-link-inline:hover {
            color: #d97706;
            transform: translateX(-2px);
        }
    </style>
</head>
<body>

    <!-- ========================================= -->
    <!-- UNIFIED MOBILE-FIRST TOP BAR              -->
    <!-- ========================================= -->
    <div class="mobile-top-bar">
        <div class="brand-container">
            <img src="{{ asset('logo11.jpg') }}" alt="MauzoSheetAI Logo" class="brand-logo" onerror="this.src='https://placehold.co/80x80/d97706/white?text=M'; this.onerror=null;">
            <span class="brand-text">MauzoSheetAI</span>
        </div>
        <a href="{{ route('landing') }}" class="home-link">
            <i class="fas fa-home"></i>
            <span>Rudi Nyumbani</span>
        </a>
    </div>

    <!-- ========================================= -->
    <!-- BOTTOM FIXED BAR (copyright + tech)       -->
    <!-- ========================================= -->
    <div class="mobile-bottom-bar">
        <div class="copyright-badge">
            <i class="far fa-copyright text-amber-600 text-xs"></i>
            <span>2025 MauzoSheetAI | Haki zote zimehifadhiwa</span>
        </div>
        <div class="tech-badge">
            <i class="fas fa-microchip text-amber-400 text-xs"></i>
            <span>imetengenezwa na : Black Sciences Technology</span>
        </div>
    </div>

    <!-- ========================================= -->
    <!-- CENTERED LOGIN CARD (MAIN CONTENT)        -->
    <!-- ========================================= -->
    <div class="login-card-wrapper">
        <div class="glass-card rounded-3xl p-6 md:p-7 shadow-xl animate-fade-in">
            
            <!-- Icon & title section with better spacing -->
            <div class="text-center mb-6">
                <div class="w-20 h-20 bg-amber-50 flex items-center justify-center mx-auto mb-4 rounded-2xl shadow-sm border border-amber-100">
                    <img src="{{ asset('logo11.jpg') }}" alt="MauzoSheetAI" class="h-14 w-auto rounded-xl" onerror="this.src='https://placehold.co/80x80/d97706/white?text=M'">
                </div>
                <h1 class="text-2xl font-extrabold text-gray-800 tracking-tight">Karibu Tena</h1>
                <p class="text-gray-500 text-sm mt-1">Ingia kwenye akaunti yako</p>
            </div>

            <!-- Success Alert with better readability -->
            @if(session('success'))
            <div id="success-alert" class="mb-5 p-3 rounded-xl bg-gradient-to-r from-emerald-50 to-green-50 border border-emerald-200 text-emerald-800 text-sm animate-fade-in">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-emerald-600"></i>
                        <p class="leading-relaxed">{{ session('success') }}</p>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 transition"><i class="fas fa-times"></i></button>
                </div>
            </div>
            <script>setTimeout(() => { let el = document.getElementById('success-alert'); if(el) el.remove(); }, 5000);</script>
            @endif

            <!-- Error Alert block -->
            @if($errors->has('login') || session('error'))
            <div class="mb-5 p-3 rounded-xl bg-gradient-to-r from-red-50 to-rose-50 border border-red-200 text-red-700 text-sm animate-fade-in">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-exclamation-triangle text-red-500"></i>
                        <p class="leading-relaxed">{{ $errors->first('login') ?? session('error') }}</p>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="text-red-600 hover:text-red-800 transition"><i class="fas fa-times"></i></button>
                </div>
            </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('login.post') }}" class="space-y-5" autocomplete="off">
                @csrf

                <!-- Username field -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user text-amber-600 mr-2"></i>Jina la Mtumiaji
                    </label>
                    <input type="text" name="username" value="{{ old('username') }}" required autofocus
                           placeholder="Weka jina lako"
                           class="w-full rounded-xl border border-gray-200 bg-white/90 text-gray-800 px-4 py-3 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition-all outline-none shadow-sm">
                    @error('username')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password field -->
                <div>
                    <div class="flex justify-between mb-2">
                        <label class="text-sm font-semibold text-gray-700">
                            <i class="fas fa-key text-amber-600 mr-2"></i>Neno la Siri
                        </label>
                        <a href="{{ route('password.request') }}" class="text-xs text-amber-600 hover:text-amber-700 font-medium transition">Umesahau?</a>
                    </div>
                    <input type="password" name="password" required
                           placeholder="Weka neno la siri"
                           class="w-full rounded-xl border border-gray-200 bg-white/90 text-gray-800 px-4 py-3 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition-all outline-none shadow-sm">
                </div>

                <!-- Remember me & register link row -->
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                        <span class="text-sm text-gray-700">Kumbuka mimi</span>
                    </label>
                    <a href="{{ route('register') }}" class="text-sm text-amber-600 font-semibold hover:text-amber-700 transition flex items-center gap-1">
                        Sajili <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>

                <!-- Submit button -->
                <button type="submit" class="btn-gold w-full py-3 rounded-xl text-white font-bold text-lg flex items-center justify-center gap-2 shadow-md mt-2">
                    <i class="fas fa-sign-in-alt"></i> Ingia Sasa
                </button>
            </form>

            <!-- Back to landing helper link (clean and compact) -->
            <div class="text-center mt-6 pt-4 border-t border-gray-100">
                <a href="{{ route('landing') }}" class="rudi-link-inline inline-flex items-center gap-1 text-gray-500 hover:text-amber-600 text-sm font-medium transition">
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
                // ensure no stray values
                usernameInput.value = '';
            }
            if (passwordInput) {
                passwordInput.value = '';
            }
            // Prevent resubmission on refresh using replaceState
            if (window.history.replaceState) {
                window.history.replaceState(null, null, window.location.href);
            }

            // Add touch-friendly feedback for buttons on mobile (optional)
            const btns = document.querySelectorAll('button, .home-link, .rudi-link-inline');
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