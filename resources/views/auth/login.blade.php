<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MauzoSheetAI · Ingia</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --amber: #d97706;
            --amber-dark: #b45309;
            --amber-soft: rgba(217, 119, 6, 0.20);
            --ink: #0b0a09;
            --muted: #5a5550;
            --border: #d6d0c8;
            --paper: #fffcf7;
            --glass-bg: rgba(20, 18, 16, 0.82);
            --glass-border: rgba(255, 215, 160, 0.25);
            --danger: #c2410c;
            --ok: #15803d;
        }

        html, body { height: 100%; }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: #f0ebe5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.25rem;
            position: relative;
            background: #0f0d0b;
            overflow-x: hidden;
        }

        /* 
            BACKGROUND IMAGE WITH BLUR
            The key is using filter: blur() on the background layer
            while keeping the overlay for readability
        */
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background: 
                linear-gradient(145deg, rgba(0, 0, 0, 0.60) 0%, rgba(0, 0, 0, 0.45) 100%),
                url("/bg.jpg");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            /* 
                BLUR EFFECT — this blurs the background image
                Higher value = more blur (e.g., blur(8px) or blur(12px))
            */
            filter: blur(12px) saturate(1.1);
            transform: scale(1.05); /* prevents edge artifacts from blur */
            z-index: -2;
        }

        /* 
            Optional: additional soft gradient overlay for depth
            This sits on top of the blurred background
        */
        body::after {
            content: "";
            position: fixed;
            inset: 0;
            background: radial-gradient(ellipse at 50% 50%, rgba(0,0,0,0) 50%, rgba(0,0,0,0.50) 100%);
            z-index: -1;
        }

        /* loading */
        #loadingScreen {
            position: fixed;
            inset: 0;
            z-index: 100;
            background: #0f0d0b;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            transition: opacity 0.45s ease, visibility 0.45s ease;
        }
        #loadingScreen.hide {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }
        .loading-mark {
            position: relative;
            width: 56px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .loading-mark img {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            object-fit: cover;
            animation: markIn 0.5s ease forwards;
        }
        .loading-mark::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 50%;
            border: 2px solid #3d3a36;
            border-top-color: var(--amber);
            animation: spin 0.9s linear infinite;
        }
        @keyframes markIn {
            from { opacity: 0; transform: scale(0.85); }
            to { opacity: 1; transform: scale(1); }
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* brand */
        .brand {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.4rem;
            opacity: 0;
            animation: riseIn 0.5s ease 0.15s forwards;
        }
        .brand img {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            object-fit: cover;
            box-shadow: 0 4px 20px rgba(0,0,0,0.6);
        }
        .brand-name {
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 0.03em;
            color: #eae3db;
            text-shadow: 0 2px 6px rgba(0,0,0,0.6);
        }

        @keyframes riseIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* CARD */
        .auth-wrapper {
            width: 440px;
            max-width: 440px;
            margin: 0 auto;
            opacity: 0;
            animation: riseIn 0.5s ease 0.22s forwards;
        }

        .glass {
            width: 100%;
            background: var(--glass-bg);
            backdrop-filter: blur(14px) saturate(1.1);
            -webkit-backdrop-filter: blur(14px) saturate(1.1);
            border: 1px solid var(--glass-border);
            border-radius: 28px;
            padding: 2.2rem 2.5rem 2rem;
            box-shadow:
                0 30px 70px -20px rgba(0,0,0,0.8),
                0 2px 0 rgba(255, 215, 160, 0.15) inset;
            transition: all 0.2s ease;
        }

        @media (min-width: 1200px) {
            .auth-wrapper { width: 460px; max-width: 460px; }
            .glass { padding: 2.5rem 2.8rem 2.2rem; }
        }

        @media (max-width: 900px) {
            .auth-wrapper { width: min(92vw, 440px); }
            .glass { padding: 2rem 2rem 1.8rem; }
        }

        @media (max-width: 640px) {
            body { padding: 1.25rem 0.8rem; }
            .auth-wrapper { width: 100%; max-width: 100%; }
            .glass { padding: 1.7rem 1.25rem 1.5rem; border-radius: 22px; }
        }

        /* title */
        .auth-title {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            margin-bottom: 1.4rem;
            color: #f5efe9;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }

        /* home link */
        .home-link {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.76rem;
            font-weight: 600;
            color: #d4cdc4;
            background: rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(4px);
            padding: 0.25rem 0.9rem 0.25rem 0.7rem;
            border-radius: 40px;
            text-decoration: none;
            border: 1px solid rgba(255, 215, 160, 0.15);
            transition: all 0.2s ease;
            margin-bottom: 1rem;
            width: fit-content;
        }
        .home-link:hover {
            background: rgba(255, 215, 160, 0.12);
            border-color: var(--amber);
            color: #fff;
            box-shadow: 0 0 20px rgba(217, 119, 6, 0.15);
        }
        .home-link svg {
            width: 13px;
            height: 13px;
            stroke: currentColor;
            stroke-width: 2.2;
            fill: none;
        }

        /* alerts */
        .alert {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 0.6rem;
            font-size: 0.76rem;
            padding: 0.5rem 0.7rem;
            border-radius: 12px;
            margin-bottom: 1rem;
            line-height: 1.4;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(4px);
            border: 1px solid rgba(255,255,255,0.06);
        }
        .alert-ok { color: #86efac; border-color: rgba(134, 239, 172, 0.2); }
        .alert-bad { color: #fca5a5; border-color: rgba(252, 165, 165, 0.15); }
        .alert button {
            background: none;
            border: none;
            color: inherit;
            opacity: 0.5;
            cursor: pointer;
            font-size: 0.85rem;
            line-height: 1;
            padding: 0 0.2rem;
        }
        .alert button:hover { opacity: 1; }

        /* fields */
        .field { margin-bottom: 1rem; }
        .field:last-of-type { margin-bottom: 0; }

        label {
            display: block;
            font-size: 0.76rem;
            font-weight: 600;
            color: #d6cec4;
            margin-bottom: 0.3rem;
            letter-spacing: 0.01em;
        }

        .form-input {
            width: 100%;
            padding: 0.65rem 0.85rem;
            font-size: 0.88rem;
            border: 1px solid rgba(255, 215, 160, 0.20);
            border-radius: 12px;
            background: rgba(20, 18, 16, 0.70);
            backdrop-filter: blur(2px);
            color: #f0ebe5;
            outline: none;
            font-family: inherit;
            appearance: none;
            transition: border-color 0.18s ease, box-shadow 0.18s ease, background 0.18s ease;
        }
        .form-input::placeholder { color: #887e74; }
        .form-input:focus {
            border-color: var(--amber);
            background: rgba(28, 25, 22, 0.85);
            box-shadow: 0 0 0 4px var(--amber-soft), 0 6px 20px rgba(0,0,0,0.3);
        }
        .form-input.invalid {
            border-color: #dc7a5a;
        }
        .form-input.invalid:focus {
            box-shadow: 0 0 0 4px rgba(194, 65, 12, 0.20);
        }

        .field-error {
            font-size: 0.68rem;
            color: #fca5a5;
            margin-top: 0.25rem;
            display: none;
        }
        .field-error.show { display: block; }

        .input-wrapper { position: relative; }
        .pw-toggle {
            position: absolute;
            right: 0.7rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            padding: 0.2rem;
            cursor: pointer;
            color: #a09589;
            display: flex;
            transition: color 0.15s ease;
        }
        .pw-toggle:hover { color: #e8e0d8; }
        .pw-toggle svg { width: 16px; height: 16px; stroke: currentColor; stroke-width: 1.8; fill: none; }

        /* remember & forgot */
        .remember-wrapper {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 0.6rem;
        }
        .remember-left {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .remember-left input[type="checkbox"] {
            width: 15px;
            height: 15px;
            accent-color: var(--amber);
            cursor: pointer;
            border-radius: 4px;
            border: 2px solid rgba(255, 215, 160, 0.3);
            background: transparent;
        }
        .remember-left label {
            margin-bottom: 0;
            font-weight: 500;
            font-size: 0.78rem;
            color: #c4bdb3;
            cursor: pointer;
        }

        .forgot-link {
            font-size: 0.76rem;
            color: #dba459;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.15s ease;
        }
        .forgot-link:hover { color: #f0c78a; text-decoration: underline; }

        /* button */
        .actions {
            margin-top: 1.5rem;
        }
        .btn-primary {
            width: 100%;
            padding: 0.75rem 1.5rem;
            font-size: 0.92rem;
            font-weight: 700;
            border-radius: 14px;
            background: var(--amber);
            color: #fff;
            border: none;
            box-shadow: 0 12px 30px -8px rgba(217, 119, 6, 0.45);
            transition: all 0.2s ease;
            cursor: pointer;
            font-family: inherit;
            letter-spacing: 0.01em;
            position: relative;
        }
        .btn-primary:hover:not(:disabled) {
            background: var(--amber-dark);
            transform: translateY(-2px);
            box-shadow: 0 18px 36px -10px rgba(217, 119, 6, 0.6);
        }
        .btn-primary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        .btn .spinner {
            display: none;
            width: 15px;
            height: 15px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
            margin: 0 auto;
        }
        .btn.loading .btn-label { visibility: hidden; position: absolute; }
        .btn.loading { position: relative; }
        .btn.loading .spinner { display: inline-block; }

        /* footer */
        .auth-footer {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.78rem;
            color: #b0a79c;
        }
        .auth-footer a {
            color: #dba459;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.15s ease;
        }
        .auth-footer a:hover { color: #f0c78a; text-decoration: underline; }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation-duration: 0.01ms !important; transition-duration: 0.01ms !important; }
        }

        @media (max-width: 480px) {
            .auth-title { font-size: 1.25rem; }
            .glass { padding: 1.4rem 1rem 1.2rem; }
            .home-link { font-size: 0.7rem; padding: 0.2rem 0.7rem; }
        }
    </style>
</head>
<body>

    <!-- LOADING -->
    <div id="loadingScreen">
        <div class="loading-mark">
            <img src="{{ asset('logo11.png') }}" alt="MauzoSheetAI" onerror="this.src='https://placehold.co/40x40/d97706/white?text=M'">
        </div>
    </div>

    <div>
        <!-- Brand -->
        <div class="brand">
            <img src="{{ asset('logo11.png') }}" alt="MauzoSheetAI" onerror="this.src='https://placehold.co/48x48/d97706/white?text=M'">
            <span class="brand-name">MauzoSheetAI</span>
        </div>

        <div class="auth-wrapper">
            <div class="glass">

                <!-- Home link inside card -->
                <a href="{{ route('landing') }}" class="home-link">
                    <svg viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1"/></svg>
                    Nyumbani
                </a>

                <h1 class="auth-title">Ingia kwenye Mfumo</h1>

                <!-- Alerts -->
                @if(session('success'))
                    <div class="alert alert-ok" id="successAlert">
                        <span>{{ session('success') }}</span>
                        <button type="button" onclick="this.closest('.alert').remove()">&times;</button>
                    </div>
                    <script>setTimeout(() => { document.getElementById('successAlert')?.remove(); }, 5000);</script>
                @endif

                @if(session('error') || $errors->has('login'))
                    <div class="alert alert-bad" id="errorAlert">
                        <span>{{ $errors->first('login') ?? session('error') }}</span>
                        <button type="button" onclick="this.closest('.alert').remove()">&times;</button>
                    </div>
                @endif

                @if ($errors->any() && !$errors->has('login'))
                    <div class="alert alert-bad" id="validationAlert">
                        <span>
                            @foreach ($errors->all() as $error)
                                {{ $error }}@if(!$loop->last)<br>@endif
                            @endforeach
                        </span>
                        <button type="button" onclick="this.closest('.alert').remove()">&times;</button>
                    </div>
                @endif

                <form id="loginForm" method="POST" action="{{ route('login.post') }}">
                    @csrf

                    <!-- Username -->
                    <div class="field">
                        <label for="username">Jina la Mtumiaji</label>
                        <input name="username" id="username" value="{{ old('username') }}" required
                            placeholder="Weka jina lako la mtumiaji"
                            class="form-input @error('username') invalid @enderror"
                            autocomplete="username" autofocus>
                        <div class="field-error" id="username_message">@error('username'){{ $message }}@enderror</div>
                    </div>

                    <!-- Password -->
                    <div class="field">
                        <label for="password">Neno la Siri</label>
                        <div class="input-wrapper">
                            <input type="password" name="password" id="password" required
                                placeholder="Weka neno la siri"
                                class="form-input @error('password') invalid @enderror"
                                autocomplete="current-password">
                            <button type="button" class="pw-toggle" id="togglePassword" aria-label="Onyesha nenosiri">
                                <svg id="eyeIcon" viewBox="0 0 24 24">
                                    <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>
                        </div>
                        <div class="field-error" id="password_message">@error('password'){{ $message }}@enderror</div>
                    </div>

                    <!-- Remember & Forgot -->
                    <div class="remember-wrapper">
                        <div class="remember-left">
                            <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label for="remember">Kumbuka mimi</label>
                        </div>
                        <a href="{{ route('password.request') }}" class="forgot-link">Umesahau?</a>
                    </div>

                    <!-- Submit -->
                    <div class="actions">
                        <button type="submit" id="submitBtn" class="btn-primary">
                            <span class="btn-label">Ingia</span>
                            <span class="spinner"></span>
                        </button>
                    </div>
                </form>

                <div class="auth-footer">
                    Huwa na akaunti? <a href="{{ route('register') }}">Jisajili</a>
                </div>

            </div>
        </div>
    </div>

    <script>
        (function() {
            'use strict';

            window.addEventListener('load', function() {
                setTimeout(function() {
                    document.getElementById('loadingScreen').classList.add('hide');
                }, 350);
            });

            const toggleBtn = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            if (toggleBtn && passwordInput) {
                toggleBtn.addEventListener('click', function() {
                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        if (eyeIcon) {
                            eyeIcon.innerHTML = `
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                                <line x1="1" y1="1" x2="23" y2="23"/>
                            `;
                        }
                    } else {
                        passwordInput.type = 'password';
                        if (eyeIcon) {
                            eyeIcon.innerHTML = `
                                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/>
                                <circle cx="12" cy="12" r="3"/>
                            `;
                        }
                    }
                });
            }

            const form = document.getElementById('loginForm');
            const submitBtn = document.getElementById('submitBtn');

            if (form && submitBtn) {
                form.addEventListener('submit', function(e) {
                    const username = document.getElementById('username');
                    const password = document.getElementById('password');
                    let hasError = false;

                    document.querySelectorAll('.form-input').forEach(el => el.classList.remove('invalid'));
                    document.querySelectorAll('.field-error').forEach(el => el.classList.remove('show'));

                    if (!username.value.trim()) {
                        username.classList.add('invalid');
                        const msg = document.getElementById('username_message');
                        if (msg) { msg.textContent = 'Tafadhali weka jina la mtumiaji.'; msg.classList.add('show'); }
                        hasError = true;
                    }

                    if (!password.value.trim()) {
                        password.classList.add('invalid');
                        const msg = document.getElementById('password_message');
                        if (msg) { msg.textContent = 'Tafadhali weka neno la siri.'; msg.classList.add('show'); }
                        hasError = true;
                    }

                    if (hasError) {
                        e.preventDefault();
                        return;
                    }

                    submitBtn.classList.add('loading');
                    submitBtn.disabled = true;
                });
            }

            document.querySelectorAll('.form-input').forEach(input => {
                input.addEventListener('input', function() {
                    this.classList.remove('invalid');
                    const msgId = this.id + '_message';
                    const msg = document.getElementById(msgId);
                    if (msg) { msg.textContent = ''; msg.classList.remove('show'); }
                });
            });

            if (window.history.replaceState) {
                window.history.replaceState(null, null, window.location.href);
            }

            console.log('✅ MauzoSheetAI Login · with blurred background');
        })();
    </script>
</body>
</html>