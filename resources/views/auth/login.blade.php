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
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Inter', system-ui, -apple-system, sans-serif; 
            background: linear-gradient(135deg, #b45309 0%, #4d2401 100%);
            min-height: 100vh;
            position: relative;
        }
        .glass-card {
            background: rgba(250, 248, 248, 0.95);
            backdrop-filter: blur(4px);
            border: 1px solid rgba(245, 243, 241, 0.2);
            box-shadow: 0 20px 35px -12px rgba(0, 0, 0, 0.08);
        }
        .btn-gold {
            background: linear-gradient(100deg, #d97706, #b45309);
            transition: all 0.25s;
        }
        .btn-gold:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -8px rgba(217, 119, 6, 0.4);
        }
        .input-focus:focus {
            ring: 2px solid #f59e0b;
            border-color: #f59e0b;
        }
        .animate-fade-in {
            animation: fadeIn 0.4s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .form-input:focus {
            border-color: #d97706;
            ring: 2px solid #fef3c7;
        }
        /* Custom utility for absolute positioned corners */
        .corner-top-left { position: absolute; top: 24px; left: 28px; z-index: 20; }
        .corner-top-right { position: absolute; top: 24px; right: 28px; z-index: 20; }
        .corner-bottom-left { position: absolute; bottom: 24px; left: 28px; z-index: 20; }
        .corner-bottom-right { position: absolute; bottom: 24px; right: 28px; z-index: 20; }
        
        /* responsive adjustments for small screens */
        @media (max-width: 640px) {
            .corner-top-left { top: 16px; left: 16px; }
            .corner-top-right { top: 16px; right: 16px; }
            .corner-bottom-left { bottom: 16px; left: 16px; }
            .corner-bottom-right { bottom: 16px; right: 16px; }
            .brand-text { font-size: 0.9rem; }
            .logo-img { height: 32px; width: auto; }
        }
        
        /* smooth hover effect for rudi nyumbani */
        .rudi-link {
            transition: all 0.2s ease;
        }
        .rudi-link:hover {
            color: #f59e0b;
            transform: translateX(-2px);
        }
    </style>
</head>
<body class="relative flex items-center justify-center p-4 min-h-screen">

    <!-- ========================================= -->
    <!-- TOP LEFT: Mauzo Sheet AI na Logo -->
    <!-- ========================================= -->
    <div class="corner-top-left flex items-center gap-3 bg-water backdrop-blur-sm rounded-2xl px-4 py-2">
          <img src="{{ asset('logo11.jpg') }}" alt="MauzoSheetAI Logo" class="h-7 w-auto rounded-lg" onerror="this.src='https://placehold.co/40x40/d97706/white?text=M'; this.onerror=null;">
       
            <span class="text-bold-white">Mauzosheetai</span>
     
    </div>

    <!-- ========================================= -->
    <!-- TOP RIGHT: Rudi Nyumbani (home link) -->
    <!-- ========================================= -->
    <div class="corner-top-right">
        <a href="{{ route('landing') }}" class="rudi-link flex items-center gap-2 bg-white/80 backdrop-blur-sm rounded-2xl px-5 py-2.5 shadow-md border border-amber-200/60 text-gray-700 font-semibold text-sm transition-all hover:bg-white hover:shadow-lg">
            <i class="fas fa-home text-amber-600"></i>
            <span>Rudi Nyumbani</span>
        </a>
    </div>

    <!-- ========================================= -->
    <!-- BOTTOM LEFT: footer word / footer credit   -->
    <!-- ========================================= -->
    <div class="corner-bottom-left">
        <div class="bg-white/70 backdrop-blur-sm rounded-xl px-4 py-2 shadow-sm border border-amber-100/50 text-gray-600 text-xs font-medium flex items-center gap-2">
            <i class="far fa-copyright text-amber-600"></i>
            <span>2025 MauzoSheetAI | Haki zote zimehifadhiwa</span>
        </div>
    </div>

    <!-- ========================================= -->
    <!-- BOTTOM RIGHT: Black Sciences Technology     -->
    <!-- ========================================= -->
    <div class="corner-bottom-right">
        <div class="bg-black/80 backdrop-blur-sm rounded-xl px-4 py-2 shadow-md border border-amber-500/30 flex items-center gap-2">
            <i class="fas fa-microchip text-amber-400 text-xs"></i>
            <span class="text-white text-xs font-medium tracking-wide">imetengenezwa na : Black Sciences Technology</span>
        </div>
    </div>

    <!-- ========================================= -->
    <!-- CENTERED LOGIN CARD                        -->
    <!-- ========================================= -->
    <div class="w-full max-w-md relative z-10">
        <div class="glass-card rounded-3xl p-7 shadow-xl animate-fade-in">
            <div class="text-center mb-6">
                <div class="w-20 h-20 bg-water flex items-center justify-center mx-auto mb-4 shadow-md border border-water">
                    <img src="{{ asset('logo11.jpg') }}" alt="MauzoSheetAI" class="h-14 w-auto rounded-xl" onerror="this.src='https://placehold.co/80x80/d97706/white?text=M'">
                </div>
                <h1 class="text-2xl font-bold text-gray-800">Karibu Tena</h1>
                <p class="text-gray-500 text-sm mt-1">Ingia kwenye akaunti yako</p>
            </div>

            <!-- Success Alert -->
            @if(session('success'))
            <div id="success-alert" class="mb-4 p-3 rounded-xl bg-gradient-to-r from-emerald-50 to-green-50 border border-emerald-200 text-emerald-800 text-sm animate-fade-in">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-emerald-600"></i>
                        <p>{{ session('success') }}</p>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="text-emerald-600"><i class="fas fa-times"></i></button>
                </div>
            </div>
            <script>setTimeout(() => { let el = document.getElementById('success-alert'); if(el) el.remove(); }, 5000);</script>
            @endif

            @if($errors->has('login') || session('error'))
            <div class="mb-4 p-3 rounded-xl bg-gradient-to-r from-red-50 to-rose-50 border border-red-200 text-red-700 text-sm animate-fade-in">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-exclamation-triangle text-red-500"></i>
                        <p>{{ $errors->first('login') ?? session('error') }}</p>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="text-red-600"><i class="fas fa-times"></i></button>
                </div>
            </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-5" autocomplete="off">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user text-amber-600 mr-2"></i>Jina la Mtumiaji
                    </label>
                    <input type="text" name="username" value="{{ old('username') }}" required autofocus
                           placeholder="Weka jina lako"
                           class="w-full rounded-xl border border-gray-200 bg-white/80 text-gray-800 px-4 py-3 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition-all">
                </div>

                <div>
                    <div class="flex justify-between mb-2">
                        <label class="text-sm font-semibold text-gray-700">
                            <i class="fas fa-key text-amber-600 mr-2"></i>Neno la Siri
                        </label>
                        <a href="{{ route('password.request') }}" class="text-xs text-amber-600 hover:text-amber-700 font-medium">Umesahau?</a>
                    </div>
                    <input type="password" name="password" required
                           placeholder="Weka neno la siri"
                           class="w-full rounded-xl border border-gray-200 bg-white/80 text-gray-800 px-4 py-3 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition-all">
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                        <span class="text-sm text-gray-600">Kumbuka mimi</span>
                    </label>
                    <a href="{{ route('register') }}" class="text-sm text-amber-600 font-semibold hover:text-amber-700">
                        Sajili <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>

                <button type="submit" class="btn-gold w-full py-3 rounded-xl text-white font-bold text-lg flex items-center justify-center gap-2 shadow-md">
                    <i class="fas fa-sign-in-alt"></i> Ingia Sasa
                </button>
            </form>

            <div class="text-center mt-6 pt-4 border-t border-gray-100">
                <a href="{{ route('landing') }}" class="text-gray-500 hover:text-amber-600 text-sm transition flex items-center justify-center gap-1">
                    <i class="fas fa-arrow-left"></i> Rudi kwenye Mwanzo
                </a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const usernameInput = document.querySelector('input[name="username"]');
            const passwordInput = document.querySelector('input[name="password"]');
            if(usernameInput) usernameInput.value = '';
            if(passwordInput) passwordInput.value = '';
            if (window.history.replaceState) {
                window.history.replaceState(null, null, window.location.href);
            }
        });
    </script>
</body>
</html>