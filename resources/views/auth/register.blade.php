<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>MauzoSheetAI · Jisajili</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;500;600;700;800&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --amber: #d97706;
            --amber-dark: #b45309;
            --amber-soft: rgba(217, 119, 6, 0.20);
            --glass-bg: rgba(20, 18, 16, 0.82);
            --glass-border: rgba(255, 215, 160, 0.25);
            --danger: #c2410c;
            --ok: #15803d;
        }

        html,
        body {
            height: 100%;
        }

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

        /* blurred background */
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
            filter: blur(12px) saturate(1.1);
            transform: scale(1.05);
            z-index: -2;
        }

        body::after {
            content: "";
            position: fixed;
            inset: 0;
            background: radial-gradient(ellipse at 50% 50%, rgba(0, 0, 0, 0) 50%, rgba(0, 0, 0, 0.50) 100%);
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
            from {
                opacity: 0;
                transform: scale(0.85);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

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
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.6);
        }
        .brand-name {
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 0.03em;
            color: #eae3db;
            text-shadow: 0 2px 6px rgba(0, 0, 0, 0.6);
        }

        @keyframes riseIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
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
                0 30px 70px -20px rgba(0, 0, 0, 0.8),
                0 2px 0 rgba(255, 215, 160, 0.15) inset;
            transition: all 0.2s ease;
        }

        @media (min-width: 1200px) {
            .auth-wrapper {
                width: 460px;
                max-width: 460px;
            }
            .glass {
                padding: 2.5rem 2.8rem 2.2rem;
            }
        }

        @media (max-width: 900px) {
            .auth-wrapper {
                width: min(92vw, 440px);
            }
            .glass {
                padding: 2rem 2rem 1.8rem;
            }
        }

        @media (max-width: 640px) {
            body {
                padding: 1.25rem 0.8rem;
            }
            .auth-wrapper {
                width: 100%;
                max-width: 100%;
            }
            .glass {
                padding: 1.7rem 1.5rem 1.5rem;
                border-radius: 22px;
            }
        }

        @media (max-width: 480px) {
            .auth-title {
                font-size: 1.25rem;
            }
            .glass {
                padding: 1.4rem 1.2rem 1.2rem;
            }
        }

        /* --- TITLE + HOME LINK: same as login --- */
        .title-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            margin-bottom: 1.4rem;
            flex-wrap: nowrap;
            min-width: 0;
        }

        .auth-title {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            color: #f5efe9;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            margin: 0;
            line-height: 1.2;
            white-space: nowrap;
            flex-shrink: 1;
            min-width: 0;
        }

        .home-link {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
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
            white-space: nowrap;
            flex-shrink: 0;
        }
        .home-link:hover {
            background: rgba(255, 215, 160, 0.12);
            border-color: var(--amber);
            color: #fff;
            box-shadow: 0 0 20px rgba(217, 119, 6, 0.15);
        }
        .home-link svg {
            width: 14px;
            height: 14px;
            stroke: currentColor;
            stroke-width: 2.2;
            fill: none;
            flex-shrink: 0;
        }

        @media (max-width: 420px) {
            .title-row {
                gap: 0.4rem;
            }
            .auth-title {
                font-size: 1.05rem;
            }
            .home-link {
                font-size: 0.65rem;
                padding: 0.2rem 0.6rem 0.2rem 0.5rem;
                gap: 0.2rem;
            }
            .home-link svg {
                width: 12px;
                height: 12px;
            }
        }

        @media (max-width: 360px) {
            .auth-title {
                font-size: 0.9rem;
            }
            .home-link {
                font-size: 0.55rem;
                padding: 0.15rem 0.4rem 0.15rem 0.35rem;
            }
            .home-link svg {
                width: 10px;
                height: 10px;
            }
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
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            border: 1px solid rgba(255, 255, 255, 0.06);
        }
        .alert-ok {
            color: #86efac;
            border-color: rgba(134, 239, 172, 0.2);
        }
        .alert-bad {
            color: #fca5a5;
            border-color: rgba(252, 165, 165, 0.15);
        }
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
        .alert button:hover {
            opacity: 1;
        }

        /* progress */
        .progress-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.1rem;
            gap: 0.75rem;
        }
        .progress-text {
            font-size: 0.72rem;
            font-weight: 600;
            color: #c4bdb3;
            white-space: nowrap;
        }
        .progress-track {
            flex: 1;
            height: 4px;
            background: rgba(255, 215, 160, 0.15);
            border-radius: 4px;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            background: var(--amber);
            border-radius: 4px;
            width: 33.33%;
            transition: width 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        /* fields */
        .field {
            margin-bottom: 0.95rem;
        }
        .field:last-child {
            margin-bottom: 0;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
        }
        @media (max-width: 480px) {
            .grid-2 {
                grid-template-columns: 1fr;
                gap: 0.95rem;
            }
        }

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
        .form-input::placeholder {
            color: #887e74;
        }
        .form-input:focus {
            border-color: var(--amber);
            background: rgba(28, 25, 22, 0.85);
            box-shadow: 0 0 0 4px var(--amber-soft), 0 6px 20px rgba(0, 0, 0, 0.3);
        }
        .form-input.invalid {
            border-color: #dc7a5a;
        }
        .form-input.invalid:focus {
            box-shadow: 0 0 0 4px rgba(194, 65, 12, 0.20);
        }

        select.form-input {
            padding-right: 2.3rem;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23887e74' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.9rem center;
            background-size: 11px;
            cursor: pointer;
            color: #f0ebe5;
        }
        select.form-input option {
            background: #1a1816;
            color: #f0ebe5;
        }

        .field-error {
            font-size: 0.68rem;
            color: #fca5a5;
            margin-top: 0.25rem;
            display: none;
        }
        .field-error.show {
            display: block;
        }

        .input-wrapper {
            position: relative;
        }
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
        .pw-toggle:hover {
            color: #e8e0d8;
        }
        .pw-toggle svg {
            width: 16px;
            height: 16px;
            stroke: currentColor;
            stroke-width: 1.8;
            fill: none;
        }

        .pw-hint {
            font-size: 0.68rem;
            color: #a09589;
            margin-top: 0.35rem;
            display: none;
        }
        .pw-hint.show {
            display: block;
        }
        .pw-hint.bad {
            color: #fca5a5;
        }

        /* buttons */
        .actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 1.4rem;
            gap: 0.75rem;
        }
        .actions.single {
            justify-content: flex-end;
        }

        .btn {
            font-family: inherit;
            font-size: 0.85rem;
            font-weight: 600;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
            padding: 0.7rem 1.5rem;
            min-height: 48px;
        }
        .btn-primary {
            background: var(--amber);
            color: #fff;
            box-shadow: 0 8px 24px -6px rgba(217, 119, 6, 0.45);
            flex: 1;
        }
        .btn-primary:hover:not(:disabled) {
            background: var(--amber-dark);
            transform: translateY(-2px);
            box-shadow: 0 12px 30px -8px rgba(217, 119, 6, 0.55);
        }
        .btn-primary:disabled {
            opacity: 0.4;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.06);
            color: #c4bdb3;
            padding: 0.7rem 1.2rem;
            border: 1px solid rgba(255, 215, 160, 0.08);
        }
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.12);
            color: #fff;
        }

        .btn .spinner {
            display: none;
            width: 15px;
            height: 15px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
            margin: 0 auto;
        }
        .btn.loading .btn-label {
            visibility: hidden;
            position: absolute;
        }
        .btn.loading {
            position: relative;
        }
        .btn.loading .spinner {
            display: inline-block;
        }

        /* steps */
        .step-panel {
            display: none;
        }
        .step-panel.active {
            display: block;
        }

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
        .auth-footer a:hover {
            color: #f0c78a;
            text-decoration: underline;
        }

        /* honeypot */
        #website-wrapper {
            position: absolute !important;
            left: -9999px !important;
            top: -9999px !important;
            height: 0 !important;
            width: 0 !important;
            overflow: hidden !important;
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>
<body>

    <!-- LOADING -->
    <div id="loadingScreen">
        <div class="loading-mark">
            <img src="{{ asset('logo11.png') }}" alt="MauzoSheetAI" onerror="this.src='https://placehold.co/40x40/d97706/white?text=M'" />
        </div>
    </div>

    <div>
        <div class="brand">
            <img src="{{ asset('logo11.png') }}" alt="MauzoSheetAI" onerror="this.src='https://placehold.co/48x48/d97706/white?text=M'" />
            <span class="brand-name">MauzoSheetAI</span>
        </div>

        <div class="auth-wrapper">
            <div class="glass">

                <!-- ✅ TITLE + HOME LINK: same as login -->
                <div class="title-row">
                    <h1 class="auth-title">Jisajili</h1>
                    <a href="{{ route('landing') }}" class="home-link">
                        <svg viewBox="0 0 24 24">
                            <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1" />
                        </svg>
                        Nyumbani
                    </a>
                </div>

                <!-- Alerts -->
                @if(session('success'))
                <div class="alert alert-ok" id="successAlert">
                    <span>{{ session('success') }}</span>
                    <button type="button" onclick="this.closest('.alert').remove()">&times;</button>
                </div>
                <script>
                    setTimeout(() => { document.getElementById('successAlert')?.remove(); }, 5000);
                </script>
                @endif

                @if(session('error'))
                <div class="alert alert-bad" id="errorAlert">
                    <span>{{ session('error') }}</span>
                    <button type="button" onclick="this.closest('.alert').remove()">&times;</button>
                </div>
                @endif

                @if ($errors->any())
                <div class="alert alert-bad" id="validationAlert">
                    <span>
                        @foreach ($errors->all() as $error)
                        {{ $error }}@if(!$loop->last)<br>@endif
                        @endforeach
                    </span>
                    <button type="button" onclick="this.closest('.alert').remove()">&times;</button>
                </div>
                @endif

                <div class="progress-row">
                    <span class="progress-text" id="progressText">Hatua 1 kati ya 3</span>
                    <div class="progress-track"><div class="progress-fill" id="progressFill"></div></div>
                </div>

                <form id="multiStepForm" method="POST" action="{{ route('register.post') }}">
                    @csrf

                    <div id="website-wrapper">
                        <input type="text" id="website" name="website" value="" tabindex="-1" autocomplete="off" />
                    </div>
                    <input type="hidden" name="form_start_time" id="form_start_time" value="" />

                    <!-- STEP 1 -->
                    <div class="step-panel active" data-step="1">
                        <div class="field">
                            <label for="company_name">Jina la Kampuni</label>
                            <input name="company_name" id="company_name" value="{{ old('company_name') }}" required
                            placeholder="Mfano: MauzoShop Ltd"
                            class="form-input" minlength="2" maxlength="255"
                            data-validate="company_name" autocomplete="organization" />
                            <div class="field-error" id="company_name_message"></div>
                        </div>

                        <div class="field">
                            <label for="owner_name">Jina la Mmiliki</label>
                            <input name="owner_name" id="owner_name" value="{{ old('owner_name') }}" required
                            placeholder="Jina kamili la mmiliki"
                            class="form-input" pattern="[a-zA-Z\s\.\-]+" minlength="2" maxlength="255"
                            data-validate="owner_name" autocomplete="name" />
                            <div class="field-error" id="owner_name_message"></div>
                        </div>
                        <div class="grid-2">
                            <div class="field">
                                <label for="location">Mahali</label>
                                <input name="location" id="location" value="{{ old('location') }}" required
                                placeholder="Eneo" class="form-input" minlength="2" maxlength="255"
                                data-validate="location" autocomplete="address-level2" />
                                <div class="field-error" id="location_message"></div>
                            </div>
                            <div class="field">
                                <label for="region">Mkoa</label>
                                <select name="region" id="region" required class="form-input" data-validate="region">
                                    <option value="">Chagua Mkoa</option>
                                    @php $regions = ["Arusha","Dar es Salaam","Dodoma","Geita","Iringa","Kagera","Katavi","Kigoma","Kilimanjaro","Lindi","Manyara","Mara","Mwanza","Mbeya","Morogoro","Mtwara","Njombe","Pwani","Ruvuma","Rukwa","Shinyanga","Simiyu","Singida","Tabora","Tanga","Zanzibar North","Zanzibar South","Zanzibar Urban/West"]; @endphp
                                    @foreach($regions as $region)
                                    <option value="{{ $region }}" {{ old('region')==$region ? 'selected' : '' }}>{{ $region }}</option>
                                    @endforeach
                                </select>
                                <div class="field-error" id="region_message"></div>
                            </div>
                        </div>
                        <input type="hidden" name="owner_gender" value="male" />
                        <input type="hidden" name="owner_dob" value="2000-01-01" />

                        <div class="actions single">
                            <button type="button" data-action="next" id="step1_next" class="btn btn-primary" disabled>
                                <span class="btn-label">Endelea</span><span class="spinner"></span>
                            </button>
                        </div>
                    </div>

                    <!-- STEP 2 -->
                    <div class="step-panel" data-step="2">
                        <div class="grid-2">
                            <div class="field">
                                <label for="phone">Simu</label>
                                <input name="phone" id="phone" value="{{ old('phone') }}" required
                                placeholder="07XXXXXXXX" class="form-input" pattern="^0[0-9]{9}$" maxlength="10" minlength="10"
                                data-validate="phone" autocomplete="tel" />
                                <div class="field-error" id="phone_message"></div>
                            </div>
                            <div class="field">
                                <label for="company_email">Barua Pepe</label>
                                <input name="company_email" id="company_email" type="email" value="{{ old('company_email') }}" required
                                placeholder="info@kampuni.com" class="form-input" maxlength="255"
                                data-validate="email" autocomplete="email" />
                                <div class="field-error" id="company_email_message"></div>
                            </div>
                        </div>

                        <div class="field">
                            <label for="business_type">Aina ya Biashara</label>
                            <select name="business_type" id="business_type" required class="form-input" data-validate="business_type">
                                <option value="">Chagua aina</option>
                                <option value="retail_shop" {{ old('business_type')=='retail_shop' ? 'selected' : '' }}>Retail Shop / Duka</option>
                                <option value="mini_market" {{ old('business_type')=='mini_market' ? 'selected' : '' }}>Mini Market</option>
                                <option value="supermarket" {{ old('business_type')=='supermarket' ? 'selected' : '' }}>Supermarket</option>
                                <option value="pharmacy" {{ old('business_type')=='pharmacy' ? 'selected' : '' }}>Pharmacy / Dawa</option>
                                <option value="hardware" {{ old('business_type')=='hardware' ? 'selected' : '' }}>Hardware</option>
                                <option value="stationery" {{ old('business_type')=='stationery' ? 'selected' : '' }}>Stationery</option>
                                <option value="restaurant" {{ old('business_type')=='restaurant' ? 'selected' : '' }}>Restaurant</option>
                                <option value="hotel" {{ old('business_type')=='hotel' ? 'selected' : '' }}>Hotel</option>
                                <option value="salon" {{ old('business_type')=='salon' ? 'selected' : '' }}>Salon / Kinyozi</option>
                                <option value="electronics" {{ old('business_type')=='electronics' ? 'selected' : '' }}>Electronics</option>
                                <option value="wholesale" {{ old('business_type')=='wholesale' ? 'selected' : '' }}>Jumla / Wholesale</option>
                                <option value="other" {{ old('business_type')=='other' ? 'selected' : '' }}>Nyingine</option>
                            </select>
                            <div class="field-error" id="business_type_message"></div>
                        </div>

                        <div class="field">
                            <label for="hear_about_us">Umetusikia Wapi?</label>
                            <select name="hear_about_us" id="hear_about_us" required class="form-input" data-validate="hear_about_us">
                                <option value="">Chagua</option>
                                <option value="friend" {{ old('hear_about_us')=='friend' ? 'selected' : '' }}>Rafiki</option>
                                <option value="facebook" {{ old('hear_about_us')=='facebook' ? 'selected' : '' }}>Facebook</option>
                                <option value="instagram" {{ old('hear_about_us')=='instagram' ? 'selected' : '' }}>Instagram</option>
                                <option value="tiktok" {{ old('hear_about_us')=='tiktok' ? 'selected' : '' }}>TikTok</option>
                                <option value="whatsapp" {{ old('hear_about_us')=='whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                                <option value="google" {{ old('hear_about_us')=='google' ? 'selected' : '' }}>Google Search</option>
                                <option value="invited" {{ old('hear_about_us')=='invited' ? 'selected' : '' }}>Nimealikwa</option>
                                <option value="advertisement" {{ old('hear_about_us')=='advertisement' ? 'selected' : '' }}>Tangazo</option>
                                <option value="other" {{ old('hear_about_us')=='other' ? 'selected' : '' }}>Nyingine</option>
                            </select>
                            <div class="field-error" id="hear_about_us_message"></div>
                        </div>

                        <div class="actions">
                            <button type="button" data-action="prev" class="btn btn-secondary">Rudi</button>
                            <button type="button" data-action="next" id="step2_next" class="btn btn-primary" disabled>
                                <span class="btn-label">Endelea</span><span class="spinner"></span>
                            </button>
                        </div>
                    </div>

                    <!-- STEP 3 -->
                    <div class="step-panel" data-step="3">
                        <div class="field">
                            <label for="username">Jina la Mtumiaji</label>
                            <input name="username" id="username" value="{{ old('username') }}" required
                            placeholder="Jina la kuingia mfumo" class="form-input"
                            pattern="^[a-zA-Z0-9_]+$" minlength="3" maxlength="50"
                            data-validate="username" autocomplete="username" />
                            <div class="field-error" id="username_message"></div>
                        </div>

                        <div class="grid-2">
                            <div class="field">
                                <label for="password">Neno la Siri</label>
                                <div class="input-wrapper">
                                    <input type="password" name="password" id="password" required
                                    placeholder="Neno la siri" class="form-input" minlength="6"
                                    data-validate="password" autocomplete="new-password" />
                                    <button type="button" class="pw-toggle" id="togglePassword" aria-label="Onyesha nenosiri">
                                        <svg id="eyeIconPw" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </button>
                                </div>
                                <div class="pw-hint" id="pwHint">Angalau herufi 6.</div>
                                <div class="field-error" id="password_message"></div>
                            </div>

                            <div class="field">
                                <label for="password_confirmation">Thibitisha</label>
                                <div class="input-wrapper">
                                    <input type="password" name="password_confirmation" id="password_confirmation" required
                                    placeholder="Andika tena" class="form-input"
                                    data-validate="password_confirm" autocomplete="new-password" />
                                    <button type="button" class="pw-toggle" id="toggleConfirm" aria-label="Onyesha nenosiri">
                                        <svg id="eyeIconConfirm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </button>
                                </div>
                                <div class="field-error" id="password_confirmation_message"></div>
                            </div>
                        </div>

                        <div class="actions">
                            <button type="button" data-action="prev" class="btn btn-secondary">Rudi</button>
                            <button type="submit" id="submitBtn" class="btn btn-primary" disabled>
                                <span class="btn-label">Sajili</span><span class="spinner"></span>
                            </button>
                        </div>
                    </div>

                </form>

                <div class="auth-footer">
                    Una akaunti tayari? <a href="{{ route('login') }}">Ingia</a>
                </div>

            </div>
        </div>
    </div>

    <script>
        (function() {
            'use strict';

            document.getElementById('form_start_time').value = Date.now();
            window.addEventListener('load', function() {
                setTimeout(function() {
                    document.getElementById('loadingScreen').classList.add('hide');
                }, 350);
            });

            const form = document.getElementById('multiStepForm');
            const panels = Array.from(document.querySelectorAll('.step-panel'));
            let currentStep = 0;
            const progressFill = document.getElementById('progressFill');
            const progressText = document.getElementById('progressText');

            const validations = {
                company_name: {
                    validate: (v) => v.trim().length >= 2,
                    msg: 'Jina la kampuni linahitajika (angalau herufi 2).'
                },
                owner_name: {
                    validate: (v) => /^[a-zA-Z\s\.\-]+$/.test(v) && v.trim().length >= 2,
                    msg: 'Jina liwe herufi tu, angalau herufi 2.'
                },
                location: {
                    validate: (v) => v.trim().length >= 2,
                    msg: 'Tafadhali weka eneo lako.'
                },
                region: {
                    validate: (v) => v !== '',
                    msg: 'Tafadhali chagua mkoa wako.'
                },
                phone: {
                    validate: (v) => /^0[0-9]{9}$/.test(v),
                    msg: 'Tafadhali weka namba sahihi ya simu.'
                },
                email: {
                    validate: (v) => {
                        if (!v) return false;
                        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (!re.test(v)) return false;
                        const domain = v.split('@')[1];
                        const disposable = ['mailinator.com', 'guerrillamail.com', '10minutemail.com',
                            'temp-mail.org', 'yopmail.com', 'throwawaymail.com', 'fakeinbox.com'
                        ];
                        return !disposable.includes(domain);
                    },
                    msg: 'Tafadhali tumia barua pepe halisi.'
                },
                business_type: {
                    validate: (v) => v !== '',
                    msg: 'Tafadhali chagua aina ya biashara.'
                },
                hear_about_us: {
                    validate: (v) => v !== '',
                    msg: 'Tafadhali chagua umetusikia wapi.'
                },
                username: {
                    validate: (v) => /^[a-zA-Z0-9_]{3,50}$/.test(v),
                    msg: 'Jina la mtumiaji: herufi, namba au underscore, angalau 3.'
                },
                password: {
                    validate: (v) => v.length >= 6,
                    msg: 'Nenosiri lazima liwe na angalau herufi 6.'
                },
                password_confirm: {
                    validate: (v) => {
                        const pwd = document.getElementById('password');
                        return pwd && v === pwd.value && v.length > 0;
                    },
                    msg: 'Nenosiri halilingani.'
                }
            };

            function setFieldError(field, show, customMsg) {
                const input = document.getElementById(field === 'password_confirm' ? 'password_confirmation' : field);
                const msgId = field === 'password_confirm' ? 'password_confirmation_message' : field + '_message';
                const msgEl = document.getElementById(msgId);
                if (!input) return;
                if (show) {
                    input.classList.add('invalid');
                    if (msgEl) {
                        msgEl.textContent = customMsg || (validations[field] && validations[field].msg) || '';
                        msgEl.classList.add('show');
                    }
                } else {
                    input.classList.remove('invalid');
                    if (msgEl) {
                        msgEl.textContent = '';
                        msgEl.classList.remove('show');
                    }
                }
            }

            function validateField(field, forceShow) {
                const input = document.getElementById(field);
                if (!input) return true;
                const rule = validations[field];
                if (!rule) return true;

                const value = input.value;
                const isValid = rule.validate(value);

                if (field === 'password') updatePasswordHint(value);

                if (value === '' || value === null || value === undefined) {
                    setFieldError(field, false);
                    return false;
                }

                setFieldError(field, !isValid && (forceShow || true));
                return isValid;
            }

            function updatePasswordHint(password) {
                const hint = document.getElementById('pwHint');
                if (password.length === 0) {
                    hint.classList.remove('show', 'bad');
                    return;
                }
                const isValid = validations.password.validate(password);
                if (isValid) {
                    hint.classList.remove('show');
                } else {
                    hint.classList.add('show');
                }
            }

            function getStepFields(stepIndex) {
                const panel = panels[stepIndex];
                if (!panel) return [];
                return Array.from(panel.querySelectorAll('[data-validate]')).map(el => el.id);
            }

            function validateStep(stepIndex, forceShow) {
                const fields = getStepFields(stepIndex);
                let allValid = true;
                fields.forEach(field => {
                    if (!validateField(field, forceShow)) allValid = false;
                });
                if (stepIndex === 2) {
                    const confirmInput = document.getElementById('password_confirmation');
                    const confirmValid = validations.password_confirm.validate(confirmInput.value);
                    if (confirmInput.value === '') {
                        allValid = false;
                        if (forceShow) setFieldError('password_confirm', false);
                    } else {
                        setFieldError('password_confirm', !confirmValid && forceShow);
                        if (!confirmValid) allValid = false;
                    }
                }
                return allValid;
            }

            function updateNextButtonState(stepIndex) {
                const btn = document.getElementById('step' + (stepIndex + 1) + '_next') || document.getElementById(
                'submitBtn');
                if (!btn) return;
                const fields = getStepFields(stepIndex);
                let allFilled = fields.every(f => document.getElementById(f).value.trim() !== '');
                if (stepIndex === 2) {
                    allFilled = allFilled && document.getElementById('password_confirmation').value.trim() !== '';
                }
                const isValid = allFilled && validateStep(stepIndex, false);
                btn.disabled = !isValid;
            }

            function updateProgress() {
                const pct = ((currentStep + 1) / panels.length) * 100;
                progressFill.style.width = pct + '%';
                progressText.textContent = 'Hatua ' + (currentStep + 1) + ' kati ya ' + panels.length;
            }

            function showStep(index) {
                panels.forEach((p, idx) => p.classList.toggle('active', idx === index));
                updateProgress();
                updateNextButtonState(index);
            }

            document.querySelectorAll('[data-validate]').forEach(input => {
                input.addEventListener('input', function() {
                    validateField(this.id, false);
                    if (this.id === 'password') validateField('password_confirm', false);
                    updateNextButtonState(currentStep);
                });
                input.addEventListener('blur', function() {
                    validateField(this.id, true);
                });
            });

            function wireToggle(btnId, inputId) {
                const btn = document.getElementById(btnId);
                const input = document.getElementById(inputId);
                if (btn && input) {
                    btn.addEventListener('click', function() {
                        input.type = input.type === 'password' ? 'text' : 'password';
                    });
                }
            }
            wireToggle('togglePassword', 'password');
            wireToggle('toggleConfirm', 'password_confirmation');

            document.querySelectorAll('[data-action]').forEach(btn => {
                btn.addEventListener('click', function() {
                    const action = this.dataset.action;
                    if (action === 'next') {
                        if (!validateStep(currentStep, true)) {
                            const panel = panels[currentStep];
                            const firstInvalid = panel.querySelector('.form-input.invalid');
                            if (firstInvalid) {
                                firstInvalid.focus();
                                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            }
                            return;
                        }
                        if (currentStep < panels.length - 1) {
                            currentStep++;
                            showStep(currentStep);
                            document.querySelector('.auth-wrapper').scrollIntoView({ behavior: 'smooth',
                                block: 'start' });
                        }
                    } else if (action === 'prev' && currentStep > 0) {
                        currentStep--;
                        showStep(currentStep);
                    }
                });
            });

            form.addEventListener('submit', function(e) {
                for (let i = 0; i < panels.length; i++) {
                    if (!validateStep(i, true)) {
                        e.preventDefault();
                        currentStep = i;
                        showStep(i);
                        const firstInvalid = panels[i].querySelector('.form-input.invalid');
                        if (firstInvalid) {
                            setTimeout(() => {
                                firstInvalid.focus();
                                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            }, 250);
                        }
                        return;
                    }
                }
                const submitBtn = document.getElementById('submitBtn');
                submitBtn.classList.add('loading');
                submitBtn.disabled = true;
            });

            // Init
            showStep(currentStep);
            setTimeout(() => {
                document.querySelectorAll('[data-validate]').forEach(input => {
                    if (input.value) validateField(input.id, false);
                });
                updateNextButtonState(currentStep);
            }, 100);
        })();
    </script>
</body>
</html>