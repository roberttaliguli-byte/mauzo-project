<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MauzoSheetAI | Sajili Kampuni</title>
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

        .btn-outline-amber {
            border: 1.5px solid #d97706;
            background: transparent;
            transition: all 0.2s;
        }

        .btn-outline-amber:hover {
            background: #d97706;
            color: white;
            transform: translateY(-1px);
        }

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

        /* Step indicators */
        .step-indicator-active {
            background: #d97706;
            border-color: #d97706;
            color: white;
            box-shadow: 0 2px 8px rgba(217, 119, 6, 0.3);
        }

        .step-indicator-inactive {
            background: #FEF3C7;
            border-color: #FDE68A;
            color: #B45309;
        }

        .form-input {
            transition: all 0.2s ease;
        }

        .form-input:focus {
            border-color: #d97706;
            outline: none;
            box-shadow: 0 0 0 2px #fed7aa;
        }

        .step-transition {
            transition: all 0.3s ease;
        }

        /* Mobile-optimized top bar - fixed */
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

        /* Bottom fixed bar */
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

        /* Brand styling */
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

        /* Home link */
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

        /* Bottom badges */
        .copyright-badge, .tech-badge {
            display: flex;
            align-items: center;
            gap: 6px;
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

        /* Main card container */
        .register-card-wrapper {
            width: 100%;
            max-width: 28rem;
            margin: 4.5rem auto 5rem;
            position: relative;
            z-index: 10;
        }

        /* Error styling */
        input.border-red-400, select.border-red-400 {
            border-color: #f87171 !important;
            background-color: #fef2f2 !important;
        }

        /* Responsive tweaks */
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

            .register-card-wrapper {
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
    </style>
</head>
<body>

    <!-- ========================================= -->
    <!-- FIXED MOBILE-OPTIMIZED TOP BAR            -->
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
    <!-- FIXED BOTTOM BAR (copyright + tech)       -->
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
    <!-- MAIN REGISTRATION CARD                    -->
    <!-- ========================================= -->
    <div class="register-card-wrapper">
        <div class="glass-card rounded-3xl p-6 md:p-7 shadow-xl animate-fade-in">
            
            <!-- Header with logo -->
            <div class="text-center mb-5">
                <div class="w-20 h-20 bg-gradient-to-br from-amber-100 to-orange-100 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-md border border-amber-200">
                    <img src="{{ asset('logo11.jpg') }}" alt="MauzoSheetAI" class="h-14 w-auto rounded-xl" onerror="this.src='https://placehold.co/80x80/d97706/white?text=M'">
                </div>
                <h1 class="text-2xl font-extrabold text-gray-800 tracking-tight">Sajili Kampuni</h1>
                <p class="text-gray-500 text-sm mt-1">Anza kutumia MauzoSheet leo</p>
            </div>

            <!-- Success Notification -->
            @if(session('success'))
                <div id="success-notification" class="mb-4 p-3 rounded-xl bg-gradient-to-r from-emerald-50 to-green-50 border border-emerald-200 text-emerald-800 text-sm animate-fade-in">
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2"><i class="fas fa-check-circle text-emerald-600"></i><p class="leading-relaxed">{{ session('success') }}</p></div>
                        <button onclick="this.parentElement.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 transition"><i class="fas fa-times"></i></button>
                    </div>
                </div>
                <script>setTimeout(() => { let el = document.getElementById('success-notification'); if(el) el.remove(); }, 5000);</script>
            @endif

            <!-- Error Notification -->
            @if(session('error'))
                <div class="mb-4 p-3 rounded-xl bg-gradient-to-r from-red-50 to-rose-50 border border-red-200 text-red-700 text-sm animate-fade-in">
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2"><i class="fas fa-exclamation-circle text-red-500"></i><p class="leading-relaxed">{{ session('error') }}</p></div>
                        <button onclick="this.parentElement.parentElement.remove()" class="text-red-600 hover:text-red-800 transition"><i class="fas fa-times"></i></button>
                    </div>
                </div>
            @endif

            <!-- Validation Errors -->
            @if ($errors->any())
                <div id="validation-errors" class="mb-4 p-3 rounded-xl bg-gradient-to-r from-red-50 to-rose-50 border border-red-200 text-red-700 text-sm animate-fade-in">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-start gap-2 flex-1"><i class="fas fa-exclamation-triangle text-red-500 mt-0.5"></i><div>@foreach ($errors->all() as $error)<p class="mb-1 last:mb-0">{{ $error }}</p>@endforeach</div></div>
                        <button onclick="this.parentElement.parentElement.remove()" class="text-red-600 ml-2 hover:text-red-800"><i class="fas fa-times"></i></button>
                    </div>
                </div>
                <script>setTimeout(() => { let el = document.getElementById('validation-errors'); if(el) el.remove(); }, 8000);</script>
            @endif

            <!-- Progress Steps - refined for mobile -->
            <div class="flex justify-center mb-6">
                <div class="flex items-center space-x-2">
                    @foreach([1, 2, 3] as $step)
                        <div class="flex items-center">
                            <div class="step-indicator w-8 h-8 rounded-full flex items-center justify-center border-2 text-sm font-bold transition-all duration-300
                                @if($step == ($currentStep ?? 1)) step-indicator-active @else step-indicator-inactive @endif">
                                {{ $step }}
                            </div>
                            @if($step < 3)
                                <div class="w-6 h-0.5 bg-amber-200 mx-1"></div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Multi-step Form -->
            <form id="multiStepForm" method="POST" action="{{ route('register.post') }}" class="space-y-5">
                @csrf

                <!-- STEP 1 - Company & Owner -->
                <div class="step step-transition @if(($currentStep ?? 1) != 1) hidden @endif" data-step="1">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-building text-amber-600 mr-2"></i>Jina la Kampuni</label>
                            <input name="company_name" value="{{ old('company_name') }}" required placeholder="Mfano: MauzoShop Ltd" class="form-input w-full rounded-xl border border-gray-200 bg-white/90 text-gray-800 px-4 py-3 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 outline-none shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-user-tie text-amber-600 mr-2"></i>Jina la Mmiliki</label>
                            <input name="owner_name" value="{{ old('owner_name') }}" required placeholder="Jina kamili la mmiliki" class="form-input w-full rounded-xl border border-gray-200 bg-white/90 text-gray-800 px-4 py-3 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 outline-none shadow-sm">
                        </div>
                        <input type="hidden" name="owner_gender" value="male">
                        <input type="hidden" name="owner_dob" value="2000-01-01">
                    </div>
                    <div class="mt-6 flex justify-between items-center">
                        <div class="text-gray-400 text-xs"><i class="fas fa-info-circle mr-1"></i>Hatua <span class="text-amber-600 font-semibold">1</span>/3</div>
                        <button type="button" data-action="next" class="btn-gold py-2.5 px-6 rounded-xl text-white font-semibold text-sm shadow-md flex items-center gap-2">Endelea <i class="fas fa-arrow-right"></i></button>
                    </div>
                </div>

                <!-- STEP 2 - Contact & Business Details -->
                <div class="step step-transition @if(($currentStep ?? 1) != 2) hidden @endif" data-step="2">
                    <div class="space-y-3">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1"><i class="fas fa-map-marker-alt text-amber-600 mr-1"></i>Mahali</label>
                                <input name="location" value="{{ old('location') }}" required placeholder="Eneo" class="form-input w-full rounded-xl border border-gray-200 bg-white/90 text-gray-800 px-3 py-2.5 text-sm focus:ring-2 focus:ring-amber-400">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1"><i class="fas fa-map-pin text-amber-600 mr-1"></i>Mkoa</label>
                                <select name="region" required class="form-input w-full rounded-xl border border-gray-200 bg-white/90 text-gray-800 px-3 py-2.5 text-sm focus:ring-2 focus:ring-amber-400">
                                    <option value="">Chagua</option>
                                    @php $regions = ["Arusha","Dar es Salaam","Dodoma","Geita","Iringa","Kagera","Katavi","Kigoma","Kilimanjaro","Lindi","Manyara","Mara","Mwanza","Mbeya","Morogoro","Mtwara","Njombe","Pwani","Ruvuma","Rukwa","Shinyanga","Simiyu","Singida","Tabora","Tanga","Zanzibar North","Zanzibar South","Zanzibar Urban/West"]; @endphp
                                    @foreach($regions as $region)<option value="{{ $region }}" {{ old('region')==$region ? 'selected' : '' }}>{{ $region }}</option>@endforeach
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1"><i class="fas fa-phone-alt text-amber-600 mr-1"></i>Simu</label>
                                <input name="phone" value="{{ old('phone') }}" required placeholder="07XXXXXXXX" class="form-input w-full rounded-xl border border-gray-200 bg-white/90 text-gray-800 px-3 py-2.5 text-sm focus:ring-2 focus:ring-amber-400">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1"><i class="fas fa-envelope text-amber-600 mr-1"></i>Barua Pepe</label>
                                <input name="company_email" type="email" value="{{ old('company_email') }}" required placeholder="info@kampuni.com" class="form-input w-full rounded-xl border border-gray-200 bg-white/90 text-gray-800 px-3 py-2.5 text-sm focus:ring-2 focus:ring-amber-400">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1"><i class="fas fa-store text-amber-600 mr-1"></i>Aina ya Biashara</label>
                            <select name="business_type" required class="form-input w-full rounded-xl border border-gray-200 bg-white/90 text-gray-800 px-3 py-2.5 text-sm focus:ring-2 focus:ring-amber-400">
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
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1"><i class="fas fa-bullhorn text-amber-600 mr-1"></i>Umetusikia Wapi?</label>
                            <select name="hear_about_us" required class="form-input w-full rounded-xl border border-gray-200 bg-white/90 text-gray-800 px-3 py-2.5 text-sm focus:ring-2 focus:ring-amber-400">
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
                        </div>
                    </div>
                    <div class="mt-6 flex justify-between items-center">
                        <button type="button" data-action="prev" class="btn-outline-amber py-2.5 px-5 rounded-xl text-amber-700 font-semibold text-sm flex items-center gap-2 hover:bg-amber-600 hover:text-white transition"><i class="fas fa-arrow-left"></i> Rudi</button>
                        <div class="text-gray-400 text-xs"><i class="fas fa-info-circle mr-1"></i>Hatua <span class="text-amber-600 font-semibold">2</span>/3</div>
                        <button type="button" data-action="next" class="btn-gold py-2.5 px-6 rounded-xl text-white font-semibold text-sm shadow-md flex items-center gap-2">Endelea <i class="fas fa-arrow-right"></i></button>
                    </div>
                </div>

                <!-- STEP 3 - Account Credentials -->
                <div class="step step-transition @if(($currentStep ?? 1) != 3) hidden @endif" data-step="3">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-user-circle text-amber-600 mr-2"></i>Jina la Mtumiaji</label>
                            <input name="username" value="{{ old('username') }}" required placeholder="Jina la kuingia mfumo" class="form-input w-full rounded-xl border border-gray-200 bg-white/90 text-gray-800 px-4 py-3 focus:ring-2 focus:ring-amber-400 outline-none shadow-sm">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1"><i class="fas fa-lock text-amber-600 mr-1"></i>Neno la Siri</label>
                                <input type="password" name="password" required placeholder="Angalau herufi 6" class="form-input w-full rounded-xl border border-gray-200 bg-white/90 text-gray-800 px-3 py-2.5 text-sm focus:ring-2 focus:ring-amber-400">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1"><i class="fas fa-check-circle text-amber-600 mr-1"></i>Thibitisha</label>
                                <input type="password" name="password_confirmation" required placeholder="Andika tena" class="form-input w-full rounded-xl border border-gray-200 bg-white/90 text-gray-800 px-3 py-2.5 text-sm focus:ring-2 focus:ring-amber-400">
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-between items-center">
                        <button type="button" data-action="prev" class="btn-outline-amber py-2.5 px-5 rounded-xl text-amber-700 font-semibold text-sm flex items-center gap-2 hover:bg-amber-600 hover:text-white transition"><i class="fas fa-arrow-left"></i> Rudi</button>
                        <div class="text-gray-400 text-xs"><i class="fas fa-info-circle mr-1"></i>Hatua <span class="text-amber-600 font-semibold">3</span>/3</div>
                        <button type="submit" class="btn-gold py-2.5 px-6 rounded-xl text-white font-semibold text-sm shadow-md flex items-center gap-2"><i class="fas fa-check-circle"></i> Sajili</button>
                    </div>
                </div>
            </form>

            <!-- Login redirect link -->
            <div class="text-center mt-5 pt-4 border-t border-gray-100">
                <p class="text-gray-500 text-sm">Una akaunti tayari? <a href="{{ route('login') }}" class="text-amber-600 font-semibold hover:text-amber-700 transition">Ingia hapa</a></p>
            </div>
        </div>
    </div>

    <!-- Multi-step Navigation & Validation Script -->
    <script>
        (function() {
            const form = document.getElementById('multiStepForm');
            const steps = Array.from(form.querySelectorAll('.step'));
            let currentStep = {{ $currentStep ?? 1 }} - 1;
            const stepIndicators = document.querySelectorAll('.step-indicator');
            
            function updateSteps() {
                steps.forEach((step, idx) => {
                    if (idx === currentStep) {
                        step.classList.remove('hidden');
                    } else {
                        step.classList.add('hidden');
                    }
                });
                stepIndicators.forEach((indicator, idx) => {
                    if (idx === currentStep) {
                        indicator.classList.add('step-indicator-active');
                        indicator.classList.remove('step-indicator-inactive');
                    } else {
                        indicator.classList.remove('step-indicator-active');
                        indicator.classList.add('step-indicator-inactive');
                    }
                });
            }
            
            function validateStep(stepIndex) {
                const currentStepDiv = steps[stepIndex];
                const inputs = currentStepDiv.querySelectorAll('input[required], select[required]');
                let isValid = true;
                inputs.forEach(input => {
                    if (!input.value || input.value.trim() === '') {
                        input.classList.add('border-red-400', 'bg-red-50');
                        isValid = false;
                    } else {
                        input.classList.remove('border-red-400', 'bg-red-50');
                    }
                });
                // Password match validation on step 3
                if (stepIndex === 2) {
                    const password = currentStepDiv.querySelector('input[name="password"]');
                    const confirm = currentStepDiv.querySelector('input[name="password_confirmation"]');
                    if (password && confirm && password.value !== confirm.value) {
                        confirm.classList.add('border-red-400', 'bg-red-50');
                        isValid = false;
                    } else if (confirm) {
                        confirm.classList.remove('border-red-400', 'bg-red-50');
                    }
                }
                return isValid;
            }
            
            updateSteps();
            
            // Live password match check
            const step3Div = document.querySelector('.step[data-step="3"]');
            if (step3Div) {
                const pwd = step3Div.querySelector('input[name="password"]');
                const confirmPwd = step3Div.querySelector('input[name="password_confirmation"]');
                if (pwd && confirmPwd) {
                    const clearMatchError = () => {
                        if (pwd.value === confirmPwd.value && confirmPwd.value !== '') {
                            confirmPwd.classList.remove('border-red-400', 'bg-red-50');
                        } else if (confirmPwd.value !== '') {
                            confirmPwd.classList.add('border-red-400', 'bg-red-50');
                        } else {
                            confirmPwd.classList.remove('border-red-400', 'bg-red-50');
                        }
                    };
                    pwd.addEventListener('input', clearMatchError);
                    confirmPwd.addEventListener('input', clearMatchError);
                }
            }
            
            form.addEventListener('click', function(e) {
                const btn = e.target.closest('button[data-action]');
                if (!btn) return;
                const action = btn.dataset.action;
                if (action === 'next') {
                    if (validateStep(currentStep)) {
                        if (currentStep < steps.length - 1) {
                            currentStep++;
                            updateSteps();
                        }
                    } else {
                        const firstInvalid = steps[currentStep].querySelector('input[required]:invalid, select[required]:invalid, input.border-red-400');
                        if (firstInvalid) firstInvalid.focus();
                    }
                } else if (action === 'prev' && currentStep > 0) {
                    currentStep--;
                    updateSteps();
                }
            });
            
            form.addEventListener('input', function(e) {
                if (e.target.hasAttribute('required') || e.target.matches('select')) {
                    e.target.classList.remove('border-red-400', 'bg-red-50');
                }
            });
            
            form.addEventListener('submit', function(e) {
                if (!validateStep(currentStep)) {
                    e.preventDefault();
                    const firstInvalid = steps[currentStep].querySelector('input[required]:invalid, select[required]:invalid, input.border-red-400');
                    if (firstInvalid) firstInvalid.focus();
                }
            });
        })();
    </script>
</body>
</html>