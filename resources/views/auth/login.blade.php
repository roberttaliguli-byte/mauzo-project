<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            background: radial-gradient(ellipse at 50% 0%, #fef3c7 0%, #fde68a 20%, #d97706 50%, #78350f 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated background blobs */
        body::before {
            content: '';
            position: fixed;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(251, 191, 36, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            top: -200px;
            right: -200px;
            pointer-events: none;
            animation: floatBlob 20s ease-in-out infinite;
        }

        body::after {
            content: '';
            position: fixed;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(251, 191, 36, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            bottom: -150px;
            left: -150px;
            pointer-events: none;
            animation: floatBlob 25s ease-in-out infinite reverse;
        }

        @keyframes floatBlob {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -30px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
        }

        /* Premium glass card */
        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px) saturate(1.2);
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 
                0 20px 60px -20px rgba(0, 0, 0, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.6);
            border-radius: 24px;
            transition: all 0.3s ease;
        }

        .glass-card:hover {
            box-shadow: 
                0 30px 80px -20px rgba(0, 0, 0, 0.35),
                inset 0 1px 0 rgba(255, 255, 255, 0.6);
        }

        .login-card-wrapper {
            width: 100%;
            max-width: 460px;
            margin: 0 auto;
            position: relative;
            z-index: 10;
            animation: cardFadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes cardFadeIn {
            from {
                opacity: 0;
                transform: translateY(24px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .glass-card > *:not(:last-child) {
            padding-left: 2rem;
            padding-right: 2rem;
        }

        .glass-card > *:last-child {
            padding-left: 2rem;
            padding-right: 2rem;
        }

        @media (max-width: 640px) {
            .glass-card > *:not(:last-child) {
                padding-left: 1.25rem;
                padding-right: 1.25rem;
            }
            .glass-card > *:last-child {
                padding-left: 1.25rem;
                padding-right: 1.25rem;
            }
            .login-card-wrapper {
                max-width: 100%;
            }
            body {
                padding: 0.75rem;
            }
        }

        /* Premium header */
        .logo-circle {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 2px solid rgba(251, 191, 36, 0.3);
            box-shadow: 0 8px 24px rgba(217, 119, 6, 0.15);
            transition: all 0.3s ease;
        }

        .logo-circle:hover {
            transform: scale(1.05);
            box-shadow: 0 12px 32px rgba(217, 119, 6, 0.25);
        }

        .logo-img-small {
            height: 32px;
            width: auto;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.05));
        }

        h1 {
            font-size: 1.75rem !important;
            letter-spacing: -0.02em;
            background: linear-gradient(135deg, #78350f, #d97706);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .subtitle {
            font-size: 0.9rem !important;
            color: #6b7280;
            font-weight: 400;
        }

        /* Premium form fields */
        .form-group {
            margin-bottom: 1.25rem;
            position: relative;
        }

        .form-group:last-of-type {
            margin-bottom: 0;
        }

        .form-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.35rem;
            letter-spacing: 0.01em;
            transition: color 0.2s ease;
        }

        .form-label .label-icon {
            margin-right: 0.4rem;
            color: #d97706;
            font-size: 0.85rem;
        }

        .input-wrapper {
            position: relative;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
            border: 2px solid #e5e7eb;
            border-radius: 14px;
            background: #fafbfc;
            color: #111827;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            outline: none;
            font-family: inherit;
            appearance: none;
        }

        .form-input:hover {
            border-color: #d1d5db;
            background: #ffffff;
        }

        .form-input:focus {
            border-color: #d97706;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(217, 119, 6, 0.1), 0 4px 12px rgba(0, 0, 0, 0.04);
            transform: translateY(-1px);
        }

        .form-input.error {
            border-color: #ef4444;
            background: #fef2f2;
        }

        .form-input.error:focus {
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
        }

        .form-input:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* Password toggle */
        .password-toggle {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            padding: 0.25rem;
            transition: all 0.2s ease;
            font-size: 1rem;
        }

        .password-toggle:hover {
            color: #6b7280;
        }

        .password-toggle:focus {
            outline: none;
            color: #d97706;
        }

        /* Error message */
        .error-message {
            font-size: 0.75rem;
            margin-top: 0.35rem;
            display: flex;
            align-items: center;
            gap: 0.3rem;
            color: #ef4444;
            animation: slideDown 0.3s ease forwards;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-4px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .error-message .error-icon {
            font-size: 0.7rem;
        }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, #d97706, #b45309);
            border: none;
            padding: 0.75rem 1.75rem;
            border-radius: 14px;
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            box-shadow: 0 4px 14px rgba(217, 119, 6, 0.3);
            position: relative;
            overflow: hidden;
            font-family: inherit;
            width: 100%;
        }

        .btn-primary:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(217, 119, 6, 0.35);
        }

        .btn-primary:active:not(:disabled) {
            transform: translateY(0px);
            box-shadow: 0 2px 8px rgba(217, 119, 6, 0.2);
        }

        .btn-primary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .btn-primary .spinner {
            display: none;
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 0.7s linear infinite;
        }

        .btn-primary.loading .btn-text {
            visibility: hidden;
        }

        .btn-primary.loading .spinner {
            display: inline-block;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Links */
        .link-primary {
            color: #d97706;
            font-weight: 600;
            transition: color 0.2s ease;
            text-decoration: none;
            font-size: 0.8rem;
        }

        .link-primary:hover {
            color: #b45309;
        }

        .link-secondary {
            color: #6b7280;
            transition: color 0.2s ease;
            text-decoration: none;
            font-size: 0.75rem;
        }

        .link-secondary:hover {
            color: #d97706;
        }

        /* Checkbox */
        .checkbox-custom {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            font-size: 0.8rem;
            color: #4b5563;
            font-weight: 500;
        }

        .checkbox-custom input[type="checkbox"] {
            width: 16px;
            height: 16px;
            border-radius: 4px;
            border: 2px solid #d1d5db;
            accent-color: #d97706;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .checkbox-custom input[type="checkbox"]:checked {
            border-color: #d97706;
        }

        .checkbox-custom input[type="checkbox"]:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.15);
        }

        /* Alert cards */
        .alert-card {
            border-radius: 16px;
            padding: 0.9rem 1.25rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            animation: slideInAlert 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes slideInAlert {
            from {
                opacity: 0;
                transform: translateY(-12px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .alert-success {
            background: #f0fdf4;
            border: 1px solid #86efac;
        }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fca5a5;
        }

        .alert-icon {
            font-size: 1rem;
            flex-shrink: 0;
            margin-top: 0.1rem;
        }

        .alert-success .alert-icon { color: #22c55e; }
        .alert-error .alert-icon { color: #ef4444; }

        .alert-content {
            flex: 1;
        }

        .alert-title {
            font-weight: 600;
            font-size: 0.85rem;
            margin-bottom: 0.1rem;
        }

        .alert-success .alert-title { color: #166534; }
        .alert-error .alert-title { color: #991b1b; }

        .alert-message {
            font-size: 0.8rem;
            color: #4b5563;
        }

        .alert-close {
            background: none;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            padding: 0.25rem;
            transition: color 0.2s ease;
            font-size: 0.85rem;
        }

        .alert-close:hover {
            color: #6b7280;
        }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin: 1.25rem 0;
        }

        .divider-line {
            flex: 1;
            height: 1px;
            background: #e5e7eb;
        }

        .divider-text {
            font-size: 0.7rem;
            color: #9ca3af;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Top bar */
        .top-bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 50;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.04);
            padding: 0.6rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        @media (max-width: 640px) {
            .top-bar {
                padding: 0.5rem 1rem;
            }
        }

        .brand-container {
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .brand-logo {
            height: 30px;
            width: auto;
            border-radius: 8px;
        }

        .brand-text {
            font-weight: 700;
            font-size: 0.9rem;
            color: #78350f;
            letter-spacing: -0.01em;
        }

        .home-link {
            background: rgba(217, 119, 6, 0.08);
            border-radius: 40px;
            padding: 0.35rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: #78350f;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .home-link:hover {
            background: rgba(217, 119, 6, 0.15);
            transform: translateY(-1px);
        }

        .home-link i {
            font-size: 0.7rem;
            color: #d97706;
        }

        /* Scrollable content */
        .card-content {
            padding: 2rem 2rem 1.5rem;
        }

        @media (max-width: 640px) {
            .card-content {
                padding: 1.25rem 1.25rem 1rem;
            }
        }

        /* Form footer */
        .form-footer {
            margin-top: 1.5rem;
            padding-top: 1.25rem;
            border-top: 1px solid #f3f4f6;
            text-align: center;
        }

        .form-footer .footer-text {
            font-size: 0.8rem;
            color: #6b7280;
        }

        /* Animation for form */
        .form-group {
            animation: fadeUp 0.5s ease forwards;
            opacity: 0;
        }

        .form-group:nth-child(1) { animation-delay: 0.05s; }
        .form-group:nth-child(2) { animation-delay: 0.1s; }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive fine-tuning */
        @media (max-width: 400px) {
            .flex-between-mobile {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
        }
    </style>
</head>
<body>

    <!-- TOP BAR -->
    <div class="top-bar">
        <div class="brand-container">
            <img src="{{ asset('logo11.jpg') }}" alt="MauzoSheetAI" class="brand-logo" onerror="this.src='https://placehold.co/60x60/d97706/white?text=M'">
            <span class="brand-text">MauzoSheetAI</span>
        </div>
        <a href="{{ route('landing') }}" class="home-link">
            <i class="fas fa-home"></i>
            <span>Nyumbani</span>
        </a>
    </div>

    <!-- LOGIN CARD -->
    <div class="login-card-wrapper">
        <div class="glass-card">

            <!-- HEADER -->
            <div class="text-center pt-6 pb-4 border-b border-gray-100/50">
                <div class="logo-circle rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <img src="{{ asset('logo11.jpg') }}" alt="MauzoSheetAI" class="logo-img-small" onerror="this.src='https://placehold.co/50x50/d97706/white?text=M'">
                </div>
                <h1 class="font-extrabold tracking-tight">Karibu Tena</h1>
                <p class="subtitle mt-0.5">Ingia kwenye akaunti yako</p>
            </div>

            <!-- CARD CONTENT -->
            <div class="card-content">

                <!-- ALERTS -->
                @if(session('success'))
                    <div class="alert-card alert-success" id="successAlert">
                        <i class="fas fa-check-circle alert-icon"></i>
                        <div class="alert-content">
                            <div class="alert-title">Imefanikiwa!</div>
                            <div class="alert-message">{{ session('success') }}</div>
                        </div>
                        <button class="alert-close" onclick="this.closest('.alert-card').remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <script>setTimeout(() => { let el = document.getElementById('successAlert'); if(el) el.remove(); }, 5000);</script>
                @endif

                @if($errors->has('login') || session('error'))
                    <div class="alert-card alert-error" id="errorAlert">
                        <i class="fas fa-exclamation-circle alert-icon"></i>
                        <div class="alert-content">
                            <div class="alert-title">Hitilafu!</div>
                            <div class="alert-message">{{ $errors->first('login') ?? session('error') }}</div>
                        </div>
                        <button class="alert-close" onclick="this.closest('.alert-card').remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                <!-- LOGIN FORM -->
                <form method="POST" action="{{ route('login.post') }}" id="loginForm" autocomplete="off">
                    @csrf

                    <!-- Username -->
                    <div class="form-group">
                        <label class="form-label" for="username">
                            <i class="fas fa-user label-icon"></i>Jina la Mtumiaji
                        </label>
                        <div class="input-wrapper">
                            <input type="text" name="username" id="username" value="{{ old('username') }}" required autofocus
                                placeholder="Weka jina lako"
                                class="form-input @error('username') error @enderror"
                                autocomplete="username">
                        </div>
                        @error('username')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle error-icon"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <div class="flex justify-between items-center mb-1">
                            <label class="form-label" for="password" style="margin-bottom:0;">
                                <i class="fas fa-key label-icon"></i>Neno la Siri
                            </label>
                            <a href="{{ route('password.request') }}" class="link-primary text-xs">
                                Umesahau?
                            </a>
                        </div>
                        <div class="input-wrapper">
                            <input type="password" name="password" id="password" required
                                placeholder="Weka neno la siri"
                                class="form-input @error('password') error @enderror"
                                autocomplete="current-password">
                            <button type="button" class="password-toggle" id="togglePassword" aria-label="Show password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle error-icon"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Remember Me & Register -->
                    <div class="flex items-center justify-between flex-wrap gap-3 mt-4">
                        <label class="checkbox-custom">
                            <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <span>Kumbuka mimi</span>
                        </label>
                        <a href="{{ route('register') }}" class="link-primary flex items-center gap-1 text-sm">
                            Sajili <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" id="submitBtn" class="btn-primary mt-5">
                        <span class="btn-text"><i class="fas fa-sign-in-alt mr-1"></i> Ingia Sasa</span>
                        <span class="spinner"></span>
                    </button>

                </form>

                <!-- FOOTER -->
                <div class="form-footer">
                    <p class="footer-text">
                        <a href="{{ route('landing') }}" class="link-secondary">
                            <i class="fas fa-arrow-left mr-1"></i> Rudi kwenye Mwanzo
                        </a>
                    </p>
                </div>

            </div><!-- /card-content -->
        </div><!-- /glass-card -->
    </div><!-- /login-card-wrapper -->

    <!-- ========================================= -->
    <!-- JAVASCRIPT                               -->
    <!-- ========================================= -->
    <script>
        (function() {
            'use strict';

            // Password toggle
            const toggleBtn = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');

            if (toggleBtn && passwordInput) {
                toggleBtn.addEventListener('click', function() {
                    const icon = this.querySelector('i');
                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        icon.className = 'fas fa-eye-slash';
                    } else {
                        passwordInput.type = 'password';
                        icon.className = 'fas fa-eye';
                    }
                });
            }

            // Form submission loading state
            const form = document.getElementById('loginForm');
            const submitBtn = document.getElementById('submitBtn');

            if (form && submitBtn) {
                form.addEventListener('submit', function(e) {
                    // Simple validation
                    const username = document.getElementById('username');
                    const password = document.getElementById('password');

                    let hasError = false;

                    if (!username.value.trim()) {
                        username.classList.add('error');
                        hasError = true;
                    } else {
                        username.classList.remove('error');
                    }

                    if (!password.value.trim()) {
                        password.classList.add('error');
                        hasError = true;
                    } else {
                        password.classList.remove('error');
                    }

                    if (hasError) {
                        e.preventDefault();
                        return;
                    }

                    // Show loading state
                    submitBtn.classList.add('loading');
                    submitBtn.disabled = true;
                });
            }

            // Clear error state on input
            document.querySelectorAll('.form-input').forEach(input => {
                input.addEventListener('input', function() {
                    this.classList.remove('error');
                });
            });

            // Prevent form resubmission on refresh
            if (window.history.replaceState) {
                window.history.replaceState(null, null, window.location.href);
            }

            // Touch feedback for buttons on mobile
            document.querySelectorAll('button, .home-link, .link-primary, .link-secondary').forEach(el => {
                el.addEventListener('touchstart', function() {
                    this.style.opacity = '0.8';
                }, { passive: true });
                el.addEventListener('touchend', function() {
                    this.style.opacity = '1';
                }, { passive: true });
            });

            console.log('✅ Premium login UI loaded');

        })();
    </script>
</body>
</html>