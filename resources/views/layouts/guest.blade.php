<!doctype html>
<html lang="sw">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>@yield('title', 'MauzoSheet')</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    body { 
      font-family: 'Inter', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial; 
    }
    .glass { 
      background: rgba(255, 255, 255, 0.08); 
      backdrop-filter: blur(20px); 
      border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .gradient-text {
      background: linear-gradient(135deg, #f59e0b 0%, #eab308 50%, #fbbf24 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    .floating {
      animation: floating 3s ease-in-out infinite;
    }
    @keyframes floating {
      0%, 100% { transform: translateY(0px); }
      50% { transform: translateY(-10px); }
    }
  </style>
  @stack('head')
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-900 via-gray-900 to-slate-800 text-white">

  <!-- Animated Background Elements -->
  <div class="fixed inset-0 overflow-hidden pointer-events-none">
    <div class="absolute -top-40 -right-32 w-80 h-80 bg-yellow-500/10 rounded-full blur-3xl"></div>
    <div class="absolute -bottom-40 -left-32 w-80 h-80 bg-amber-500/10 rounded-full blur-3xl"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-orange-500/5 rounded-full blur-3xl"></div>
  </div>

  <div class="min-h-screen flex items-center justify-center px-4 py-8 relative z-10">
    <div class="w-full max-w-6xl mx-auto">
      <div class="flex flex-col lg:flex-row gap-8 items-stretch">

        {{-- Left Brand Column - Enhanced --}}
        <div class="hidden lg:flex lg:flex-1 items-center justify-center">
          <div class="text-center space-y-8">
            {{-- Animated Logo Container --}}
            <div class="floating">
              <div class="relative">
                {{-- Glow Effect --}}
                <div class="absolute inset-0 bg-yellow-400/30 rounded-full blur-xl"></div>
                {{-- Logo Circle --}}
                <div class="relative h-40 w-40 rounded-full bg-gradient-to-br from-yellow-400 to-amber-500 flex items-center justify-center text-black mx-auto border-4 border-white/20 shadow-2xl overflow-hidden">
                  {{-- Your Logo --}}
                  <img src="{{ asset('logo11.jpg') }}" alt="MauzoSheet Logo" class="h-full w-full object-cover">
                </div>
              </div>
            </div>

            {{-- Brand Text --}}
            <div class="space-y-4">
              <h1 class="text-4xl font-black gradient-text">MauzoSheet</h1>
              <p class="text-xl text-gray-300 font-medium">Usimamizi Bora wa Biashara</p>
              
              {{-- Features List --}}
              <div class="space-y-3 mt-6">
                <div class="flex items-center gap-3 text-gray-300">
                  <div class="w-2 h-2 bg-yellow-400 rounded-full"></div>
                  <span class="text-sm">Usimamizi wa Mauzo</span>
                </div>
                <div class="flex items-center gap-3 text-gray-300">
                  <div class="w-2 h-2 bg-yellow-400 rounded-full"></div>
                  <span class="text-sm">Kudhibiti Bidhaa</span>
                </div>
                <div class="flex items-center gap-3 text-gray-300">
                  <div class="w-2 h-2 bg-yellow-400 rounded-full"></div>
                  <span class="text-sm">Ufuatiliaji wa Wafanyakazi</span>
                </div>
                <div class="flex items-center gap-3 text-gray-300">
                  <div class="w-2 h-2 bg-yellow-400 rounded-full"></div>
                  <span class="text-sm">Ripoti za Kinachotokea</span>
                </div>
              </div>
            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-3 gap-4 mt-8">
              <div class="text-center">
                <div class="text-2xl font-bold text-yellow-400">99%</div>
                <div class="text-xs text-gray-400">Ya Ufanisi</div>
              </div>
              <div class="text-center">
                <div class="text-2xl font-bold text-yellow-400">24/7</div>
                <div class="text-xs text-gray-400">Msaada</div>
              </div>
              <div class="text-center">
                <div class="text-2xl font-bold text-yellow-400">100+</div>
                <div class="text-xs text-gray-400">Wateja</div>
              </div>
            </div>
          </div>
        </div>

        {{-- Mobile Logo --}}
        <div class="lg:hidden flex justify-center mb-6">
          <div class="h-20 w-20 rounded-full bg-gradient-to-br from-yellow-400 to-amber-500 flex items-center justify-center text-black font-bold text-xl border-4 border-white/20 shadow-lg overflow-hidden">
            <img src="{{ asset('logo11.jpg') }}" alt="MauzoSheet Logo" class="h-full w-full object-cover">
          </div>
        </div>

        {{-- Right Form Column - Enhanced --}}
        <div class="flex-1 glass rounded-3xl p-8 lg:p-12 shadow-2xl relative overflow-hidden">
          {{-- Decorative Elements --}}
          <div class="absolute top-0 right-0 w-32 h-32 bg-yellow-400/10 rounded-full -translate-y-16 translate-x-16"></div>
          <div class="absolute bottom-0 left-0 w-24 h-24 bg-amber-400/10 rounded-full translate-y-12 -translate-x-12"></div>
          
          {{-- Content --}}
          <div class="relative z-10">
            @yield('content')
          </div>
        </div>
      </div>

      {{-- Enhanced Footer --}}
      <div class="mt-12 text-center space-y-2">
        <div class="flex items-center justify-center gap-6 text-sm text-gray-400">
          <a href="#" class="hover:text-yellow-400 transition-colors duration-300">
            <i class="fas fa-shield-alt mr-1"></i> Usalama
          </a>
          <a href="#" class="hover:text-yellow-400 transition-colors duration-300">
            <i class="fas fa-question-circle mr-1"></i> Usaidizi
          </a>
          <a href="#" class="hover:text-yellow-400 transition-colors duration-300">
            <i class="fas fa-file-contract mr-1"></i> Masharti
          </a>
        </div>
        <div class="text-gray-500 text-sm">
          &copy; 2024 MauzoSheet. Haki zote zimehifadhiwa.
        </div>
      </div>
    </div>
  </div>

  @stack('scripts')
</body>
</html>