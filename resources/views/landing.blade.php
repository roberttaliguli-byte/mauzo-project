<!DOCTYPE html>
<html lang="sw">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <meta name="description" content="MauzoSheetAI - Mfumo wa kisasa wa POS na usimamizi wa biashara kwa wafanyabiashara Tanzania. Rahisi, salama, na wa kisasa." />
  <meta name="keywords" content="MauzoSheetAI, POS Tanzania, biashara, usimamizi wa stoku, mauzo, SaaS" />
  <meta name="author" content="MauzoSheetAI Team" />
  <meta name="theme-color" content="#d97706" />

  <!-- Open Graph -->
  <meta property="og:title" content="MauzoSheetAI - Simamia Biashara Yako Kisasa" />
  <meta property="og:description" content="Mfumo wa kisasa wa POS na biashara. Uza, dhibiti stoku, na fuatilia faida kwa urahisi." />
  <meta property="og:image" content="https://test.mauzosheet.com/assets/images/apple-icon.gif" />
  <meta property="og:url" content="https://mauzosheet.com" />
  <meta name="twitter:card" content="summary_large_image" />

  <title>MauzoSheetAI | Simamia Biashara Yako Kwa Kisasa</title>
  <link rel="icon" type="image/png" href="https://test.mauzosheet.com/assets/images/apple-icon.gif" />

  <!-- Fonts & Tailwind -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/icofont@1.0.1/icofont.min.css" />

  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: 'Inter', system-ui, -apple-system, sans-serif;
      background-color: #fefcf7;
      color: #1f2937;
      scroll-behavior: smooth;
    }
    /* premium custom utilities */
    .text-gradient-primary {
      background: linear-gradient(120deg, #d97706, #065f46);
      background-clip: text;
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    .glass-card {
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(12px);
      border: 1px solid rgba(217, 119, 6, 0.15);
      box-shadow: 0 20px 35px -12px rgba(0,0,0,0.08);
    }
    .floating-soft {
      animation: floatSoft 5s ease-in-out infinite;
    }
    @keyframes floatSoft {
      0% { transform: translateY(0px); }
      50% { transform: translateY(-12px); }
      100% { transform: translateY(0px); }
    }
    .step-connector {
      position: relative;
    }
    .hover-lift {
      transition: transform 0.25s ease, box-shadow 0.3s ease;
    }
    .hover-lift:hover {
      transform: translateY(-8px);
      box-shadow: 0 25px 35px -12px rgba(0,0,0,0.15);
    }
    .scroll-reveal {
      opacity: 0;
      transform: translateY(24px);
      transition: opacity 0.7s cubic-bezier(0.2, 0.9, 0.4, 1.1), transform 0.7s ease;
    }
    .scroll-reveal.revealed {
      opacity: 1;
      transform: translateY(0);
    }
    .yearly-glow {
      box-shadow: 0 0 0 0 rgba(217, 119, 6, 0.4), 0 20px 35px -12px rgba(0,0,0,0.1);
      animation: pulseGlow 2s infinite;
    }
    @keyframes pulseGlow {
      0% { box-shadow: 0 0 0 0 rgba(217, 119, 6, 0.4), 0 20px 35px -12px rgba(0,0,0,0.1); }
      70% { box-shadow: 0 0 0 12px rgba(217, 119, 6, 0), 0 20px 35px -12px rgba(0,0,0,0.1); }
      100% { box-shadow: 0 0 0 0 rgba(217, 119, 6, 0), 0 20px 35px -12px rgba(0,0,0,0.1); }
    }
    .btn-premium {
      transition: all 0.2s ease;
      background: linear-gradient(95deg, #d97706, #b45309);
      border: none;
      font-weight: 600;
    }
    .btn-premium:hover {
      transform: scale(1.02) translateY(-2px);
      box-shadow: 0 12px 20px -8px rgba(217,119,6,0.5);
    }
    .btn-outline-premium {
      border: 1.5px solid #d97706;
      background: transparent;
      font-weight: 600;
      transition: all 0.2s;
    }
    .btn-outline-premium:hover {
      background: #d97706;
      color: white;
      transform: translateY(-2px);
      box-shadow: 0 8px 18px -6px rgba(217,119,6,0.4);
    }
    .nav-link-modern {
      position: relative;
      font-weight: 500;
    }
    .nav-link-modern::after {
      content: '';
      position: absolute;
      width: 0;
      height: 2px;
      bottom: -4px;
      left: 0;
      background: linear-gradient(90deg, #d97706, #065f46);
      transition: width 0.25s;
    }
    .nav-link-modern:hover::after { width: 100%; }
    .mobile-nav-open { max-height: 420px; opacity: 1; padding: 1rem 0; }
    .mobile-nav-closed { max-height: 0; opacity: 0; padding: 0; overflow: hidden; }
    .section-bg-soft { background: radial-gradient(ellipse at 80% 30%, rgba(217,119,6,0.02), rgba(6,95,70,0.02)); }
    .badge-premium {
      background: linear-gradient(135deg, #d97706, #f59e0b);
      color: white;
      font-size: 0.7rem;
      font-weight: 700;
      padding: 0.3rem 1rem;
      border-radius: 40px;
    }
    @media (max-width: 768px) {
      .hero-buttons-mobile-stack .flex-col-mobile { flex-direction: column; width: 100%; }
      .btn-premium, .btn-outline-premium { width: 100%; text-align: center; }
    }
  </style>
</head>
<body class="antialiased">

  <!-- Floating gradient blobs (premium background) -->
  <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden">
    <div class="absolute top-0 -left-40 w-96 h-96 bg-amber-200/20 rounded-full blur-3xl"></div>
    <div class="absolute bottom-0 right-0 w-[32rem] h-[32rem] bg-emerald-200/20 rounded-full blur-3xl"></div>
    <div class="absolute top-1/3 right-1/4 w-64 h-64 bg-amber-300/10 rounded-full blur-2xl"></div>
  </div>

  <!-- Header Premium -->
  <header class="sticky top-0 z-50 bg-white/80 backdrop-blur-lg border-b border-gray-100/60 shadow-sm">
    <div class="max-w-7x2 mx-auto flex justify-between items-center py-4 px-6 md:px-8">
      <a href="#home" class="flex items-center gap-2 group">
        <img src="https://test.mauzosheet.com/assets/images/apple-icon.gif" class="h-9 w-9 md:h-11 md:w-11 object-contain drop-shadow-sm" alt="MauzoSheetAI">
        <span class="font-extrabold text-xl md:text-2xl bg-gradient-to-r from-amber-600 to-emerald-700 bg-clip-text text-transparent">MauzoSheet<span class="text-gray-800">AI</span></span>
      </a>
      <nav class="hidden md:flex items-center gap-8">
        <a href="#home" class="nav-link-modern text-gray-700 hover:text-amber-600">Nyumbani</a>
        <a href="#about" class="nav-link-modern text-gray-700 hover:text-amber-600">Tufahamu</a>
        <a href="#features" class="nav-link-modern text-gray-700 hover:text-amber-600">Huduma</a>
        <a href="#how-it-works" class="nav-link-modern text-gray-700 hover:text-amber-600">Namna Inavyofanya Kazi</a>
        <a href="#pricing" class="nav-link-modern text-gray-700 hover:text-amber-600">Vifurushi</a>
        <a href="#contact" class="nav-link-modern text-gray-700 hover:text-amber-600">Wasiliana</a>
      </nav>
      <div class="hidden md:flex items-center gap-3">
        <a href="{{ route('login') }}" class="text-gray-700 font-medium hover:text-amber-600 transition px-3 py-2">Ingia</a>
        <a href="{{ route('register') }}" class="btn-premium text-white px-5 py-2.5 rounded-xl shadow-md">Jisajili Bure</a>
      </div>
      <button id="mobileMenuBtn" class="md:hidden p-2 rounded-lg focus:outline-none" aria-label="menu">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
      </button>
    </div>
    <!-- Mobile Nav -->
    <div id="mobileNav" class="md:hidden bg-white/95 backdrop-blur-md border-t border-gray-100 transition-all duration-300 mobile-nav-closed">
      <div class="flex flex-col px-6 py-2 space-y-3 pb-6">
        <a href="#home" class="py-3 text-gray-800 font-medium hover:text-amber-600 border-b border-gray-100">Nyumbani</a>
        <a href="#about" class="py-3 text-gray-800 font-medium hover:text-amber-600">Tufahamu</a>
        <a href="#features" class="py-3 text-gray-800 font-medium hover:text-amber-600">Huduma</a>
        <a href="#how-it-works" class="py-3 text-gray-800 font-medium hover:text-amber-600">Namna Inavyofanya Kazi</a>
        <a href="#pricing" class="py-3 text-gray-800 font-medium hover:text-amber-600">Vifurushi</a>
        <a href="#contact" class="py-3 text-gray-800 font-medium hover:text-amber-600">Wasiliana</a>
        <div class="flex flex-col gap-3 pt-2">
          <a href="{{ route('login') }}" class="text-center text-gray-700 font-semibold py-2">Ingia</a>
          <a href="{{ route('register') }}" class="btn-premium text-center text-white py-3 rounded-xl">Anza Sasa</a>
        </div>
      </div>
    </div>
  </header>

  <main class="relative z-10">
    <!-- Hero Section - Premium Redesign -->
    <section id="home" class="pt-12 pb-20 md:pt-20 md:pb-28 overflow-hidden">
      <div class="max-w-7x2 mx-auto px-6 md:px-8">
        <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-16">
          <div class="flex-1 text-center lg:text-left space-y-6">
        
            <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-extrabold tracking-tight leading-tight">
              <span class="text-gradient-primary">Simamia Biashara Yako</span><br>
              <span class="text-gray-800">Kisasa Zaidi</span>
            </h1>
            <p class="text-gray-600 text-lg md:text-xl max-w-xl mx-auto lg:mx-0 leading-relaxed">
              Uza, dhibiti stoku, na fuatilia faida kwa wakati halisi. MauzoSheetAI ni suluhisho linalomiminika kwa wafanyabiashara wanaotaka ufanisi na ukuaji.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start pt-4">
              <a href="{{ route('register') }}" class="btn-premium text-white px-8 py-4 rounded-xl text-lg shadow-xl flex items-center justify-center gap-2">Jisajili Bure <i class="icofont-arrow-right"></i></a>
              <a href="https://play.google.com/store/apps/details?id=io.android.MauzoSheet" target="_blank" class="bg-white border border-gray-200 text-gray-800 px-6 py-4 rounded-xl font-semibold flex items-center justify-center gap-2 shadow-sm hover:shadow-md transition">Pakua Programu <i class="icofont-android-tablet"></i></a>
              <a href="{{ route('login') }}" class="btn-outline-premium px-6 py-4 rounded-xl flex items-center justify-center gap-2">Ingia <i class="icofont-login"></i></a>
            </div>
            <div class="flex flex-wrap gap-6 justify-center lg:justify-start pt-8">
              <div class="flex items-center gap-2"><i class="icofont-check-circled text-amber-600 text-xl"></i><span class="text-sm font-medium">wafanyabiashara zaidi ya 500</span></div>
              <div class="flex items-center gap-2"><i class="icofont-lock text-emerald-700 text-xl"></i><span class="text-sm font-medium">Usalama wa hali ya juu</span></div>
              <div class="flex items-center gap-2"><i class="icofont-headphone-alt text-amber-600 text-xl"></i><span class="text-sm font-medium">Usaidizi 24/7</span></div>
            </div>
          </div>
          <div class="flex-1 relative">
            <div class="relative floating-soft">
              <img src="p1.jpg" alt="MauzoSheetAI Dashboard" class="rounded-2xl shadow-2xl border border-white/20 w-full max-w-lg mx-auto lg:max-w-none">
              <div class="absolute -top-6 -right-6 bg-white/70 backdrop-blur-md rounded-2xl p-3 shadow-xl hidden md:flex items-center gap-2"><i class="icofont-chart-line text-amber-600 text-2xl"></i><span class="font-bold">+35% ukuaji</span></div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-20 section-bg-soft">
      <div class="max-w-7x2 mx-auto px-6 md:px-8 text-center">
        <span class="text-amber-600 font-semibold tracking-wider">KUTUJUA</span>
        <h2 class="text-3xl md:text-5xl font-bold mt-2 mb-5">Tunasaidia Biashara Kukua Kwa Dijitali</h2>
        <p class="text-gray-600 text-lg max-w-3xl mx-auto">MauzoSheetAI imeundwa kwa ajili ya wajasiriamali wenye nia ya kuleta mageuzi katika usimamizi wa mauzo, stoku, na wateja. Urahisi + teknolojia = maendeleo.</p>
        <div class="grid md:grid-cols-3 gap-8 mt-16">
          <div class="glass-card rounded-2xl p-6 text-center hover-lift transition-all"><div class="w-16 h-16 bg-amber-50 rounded-2xl flex items-center justify-center mx-auto mb-4"><i class="icofont-bullseye text-3xl text-amber-600"></i></div><h3 class="text-xl font-bold">Lengo Letu</h3><p class="text-gray-500 mt-2">Kuhakikisha kila mmiliki wa biashara anapata zana za kisasa za usimamizi kwa uwezo wa kumudu.</p></div>
          <div class="glass-card rounded-2xl p-6 text-center hover-lift"><div class="w-16 h-16 bg-emerald-50 rounded-2xl flex items-center justify-center mx-auto mb-4"><i class="icofont-eye-alt text-3xl text-emerald-700"></i></div><h3 class="text-xl font-bold">Mtazamo Wetu</h3><p class="text-gray-500 mt-2">Kuwa kiongozi wa mifumo ya kidijitali ya biashara barani Afrika, kuleta urahisi na uwazi.</p></div>
          <div class="glass-card rounded-2xl p-6 text-center hover-lift"><div class="w-16 h-16 bg-amber-50 rounded-2xl flex items-center justify-center mx-auto mb-4"><i class="icofont-heart-alt text-3xl text-amber-600"></i></div><h3 class="text-xl font-bold">Thamani Zetu</h3><p class="text-gray-500 mt-2">Uaminifu, uvumbuzi, na mafanikio kwa wateja wetu. Usaidizi wa karibu na uboreshaji endelevu.</p></div>
        </div>
      </div>
    </section>

    <!-- New Section: Namna Inavyofanya Kazi (3 steps premium)-->
    <section id="how-it-works" class="py-20 bg-white">
      <div class="max-w-7x2 mx-auto px-6 md:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16">
          <span class="text-emerald-700 font-semibold">RAHISI KWA HATUA 3</span>
          <h2 class="text-3xl md:text-5xl font-bold mt-2">Namna Inavyofanya Kazi</h2>
          <div class="w-24 h-1 bg-gradient-to-r from-amber-500 to-emerald-600 mx-auto mt-4 rounded-full"></div>
        </div>
        <div class="grid md:grid-cols-3 gap-8 relative">
          <!-- step connectors (desktop) -->
          <div class="hidden md:block absolute top-1/3 left-[15%] right-[15%] h-0.5 bg-gradient-to-r from-amber-200/60 to-emerald-200/60 -translate-y-1/2 z-0"></div>
          <div class="relative bg-white rounded-3xl shadow-xl border border-gray-100 p-8 text-center hover-lift z-10">
            <div class="bg-gradient-to-br from-amber-100 to-amber-50 w-20 h-20 rounded-3xl flex items-center justify-center mx-auto mb-5 shadow-md"><span class="text-3xl font-black text-amber-700">1</span></div>
            <h3 class="text-2xl font-bold">Jisajili</h3>
            <p class="text-gray-500 mt-3">Fungua akaunti yako ndani ya dakika chache. Unachohitaji ni barua pepe au namba ya simu.</p>
          </div>
          <div class="relative bg-white rounded-3xl shadow-xl border border-gray-100 p-8 text-center hover-lift z-10">
            <div class="bg-gradient-to-br from-emerald-100 to-emerald-50 w-20 h-20 rounded-3xl flex items-center justify-center mx-auto mb-5 shadow-md"><span class="text-3xl font-black text-emerald-800">2</span></div>
            <h3 class="text-2xl font-bold">Ingiza Bidhaa</h3>
            <p class="text-gray-500 mt-3">Ongeza bidhaa, bei, stoku, na taarifa muhimu. Mfumo unaotambulika kwa urahisi.</p>
          </div>
          <div class="relative bg-white rounded-3xl shadow-xl border border-gray-100 p-8 text-center hover-lift z-10">
            <div class="bg-gradient-to-br from-amber-100 to-amber-50 w-20 h-20 rounded-3xl flex items-center justify-center mx-auto mb-5 shadow-md"><span class="text-3xl font-black text-amber-700">3</span></div>
            <h3 class="text-2xl font-bold">Fanya Mauzo</h3>
            <p class="text-gray-500 mt-3">Anza kuuza, toa ankra, na fuatilia faida zako kwa wakati halisi. Ukiwa na MauzoSheetAI, biashara yako inapaa.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Features section Modern SaaS cards -->
    <section id="features" class="py-20 bg-gray-50/70">
      <div class="max-w-7x2 mx-auto px-6 md:px-8">
        <div class="text-center mb-14"><span class="text-amber-600 font-semibold">VIPENGELE VYA KISASA</span><h2 class="text-4xl md:text-5xl font-bold mt-2">Huduma Zetu Zinazokufanya Ufanikiwe</h2></div>
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-7">
          <div class="bg-white rounded-2xl p-6 shadow-md hover:shadow-2xl transition-all border border-gray-100 group"><div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center mb-4 group-hover:bg-amber-200 transition"><i class="icofont-chart-pie-alt text-2xl text-amber-700"></i></div><h4 class="text-xl font-bold">Ripoti za Wakati Halisi</h4><p class="text-gray-500 mt-2">Chambua mauzo, faida na mwenendo wa biashara kwa chati za kisasa.</p></div>
          <div class="bg-white rounded-2xl p-6 shadow-md hover:shadow-2xl transition-all border border-gray-100 group"><div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center mb-4 group-hover:bg-emerald-200"><i class="icofont-stock-mobile text-2xl text-emerald-700"></i></div><h4 class="text-xl font-bold">Usimamizi Stoku</h4><p class="text-gray-500 mt-2">Fuatilia bidhaa zinazoisha na uweke maagizo mapya kwa urahisi.</p></div>
          <div class="bg-white rounded-2xl p-6 shadow-md hover:shadow-2xl transition-all border border-gray-100 group"><div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center mb-4"><i class="icofont-bag text-2xl text-amber-700"></i></div><h4 class="text-xl font-bold">Point of Sale (POS)</h4><p class="text-gray-500 mt-2">Mfumo wa kisasa wa POS unaounganisha mauzo ya rejareja na jumla.</p></div>
          <div class="bg-white rounded-2xl p-6 shadow-md hover:shadow-2xl transition-all border border-gray-100 group"><div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center mb-4"><i class="icofont-shield text-2xl text-emerald-700"></i></div><h4 class="text-xl font-bold">Usalama wa Data</h4><p class="text-gray-500 mt-2">Teknolojia za usimbaji fiche. Taarifa zako ziko salama karibu na wingu.</p></div>
        </div>
      </div>
    </section>

    <!-- Pricing Section : premium yearly dominance -->
    <section id="pricing" class="py-20">
      <div class="max-w-7x2 mx-auto px-6 md:px-8">
        <div class="text-center mb-14"><span class="text-amber-600 font-semibold">BEI RAHISI</span><h2 class="text-4xl md:text-5xl font-bold">Chagua Kifurushi Kinachokufaa</h2></div>
        <div class="grid md:grid-cols-3 gap-8 items-center">
          <!-- Monthly 15k -->
          <div class="bg-white rounded-3xl p-6 shadow-xl border border-gray-100 transition hover:scale-105"><div class="text-center"><p class="text-sm font-bold uppercase text-amber-600">Mwezi mmoja</p><h3 class="text-3xl font-extrabold mt-2">TZS 15,000</h3><p class="text-gray-500">kwa mwezi</p><ul class="mt-6 space-y-3 text-left"><li class="flex gap-2"><i class="icofont-check text-emerald-600"></i>Uendeshaji biashara</li><li class="flex gap-2"><i class="icofont-check text-emerald-600"></i>Usimamizi stoku</li><li class="flex gap-2"><i class="icofont-check text-emerald-600"></i>Ripoti za msingi</li><li class="flex gap-2"><i class="icofont-check text-emerald-600"></i>Msaada wa kawaida</li></ul><a href="{{ route('register') }}" class="btn-outline-premium mt-8 w-full py-3 rounded-xl block text-center">Anza Sasa</a></div></div>
          
          <!-- Yearly (PREMIUM BEST VALUE) - most highlighted -->
          <div class="relative bg-white rounded-3xl p-8 shadow-2xl border-2 border-amber-400 transform scale-105 yearly-glow">
            <div class="absolute -top-5 left-1/2 -translate-x-1/2 bg-amber-600 text-white px-5 py-1.5 rounded-full text-sm font-bold shadow-md">INAYOPENDEKEZWA · OKOA ZAIDI</div>
            <div class="text-center"><span class="bg-gradient-to-r from-amber-500 to-emerald-600 text-transparent bg-clip-text text-sm font-extrabold">BEST VALUE</span><h3 class="text-3xl font-extrabold mt-2">Mwaka 1</h3><div class="text-5xl font-black text-amber-600 mt-3">TZS 150,000</div><p class="text-gray-500 line-through text-sm">TZS 180,000</p><p class="text-emerald-700 font-bold bg-emerald-50 inline-block px-3 py-1 rounded-full text-sm">Akiba ya TZS 30,000</p><ul class="mt-6 space-y-3 text-left"><li class="flex gap-2"><i class="icofont-check-circle text-amber-600"></i><span class="font-medium">Vipengele vyote vya Miezi 5 + Zaidi</span></li><li class="flex gap-2"><i class="icofont-check-circle text-amber-600"></i>Ripoti za kina + Uchambuzi wa AI</li><li class="flex gap-2"><i class="icofont-check-circle text-amber-600"></i>Usaidizi wa kipaumbele 24/7</li><li class="flex gap-2"><i class="icofont-check-circle text-amber-600"></i>Masomo ya biashara na mafunzo</li><li class="flex gap-2"><i class="icofont-check-circle text-amber-600"></i>Vipengele vya hali ya juu vya mauzo</li></ul><a href="{{ route('register') }}" class="btn-premium mt-8 w-full py-4 rounded-xl text-white text-lg block text-center shadow-xl">Chagua Kifurushi Bora</a></div>
          </div>

          <!-- 5 months plan 75k -->
          <div class="bg-white rounded-3xl p-6 shadow-xl border border-gray-100 transition hover:scale-105"><div class="text-center"><p class="text-sm font-bold uppercase text-emerald-700">Miezi 5</p><h3 class="text-3xl font-extrabold">TZS 75,000</h3><p class="text-gray-500">kwa miezi 5</p><ul class="mt-6 space-y-3 text-left"><li class="flex gap-2"><i class="icofont-check text-emerald-600"></i>Yote ya mwezi mmoja</li><li class="flex gap-2"><i class="icofont-check text-emerald-600"></i>Ripoti za kina zaidi</li><li class="flex gap-2"><i class="icofont-check text-emerald-600"></i>Usaidizi wa haraka</li><li class="flex gap-2"><i class="icofont-check text-emerald-600"></i>Akiba ya 25,000</li></ul><a href="{{ route('register') }}" class="btn-outline-premium mt-8 w-full py-3 rounded-xl block text-center">Chagua</a></div></div>
        </div>
        <p class="text-center text-gray-500 mt-8 text-sm">Bei zote zimejumlisha kodi. Badilisha wakati wowote.</p>
      </div>
    </section>

    <!-- Contact Section with glass card emphasis -->
    <section id="contact" class="py-20 bg-gradient-to-br from-amber-50/40 to-emerald-50/40">
      <div class="max-w-7x2 mx-auto px-6 md:px-8 text-center"><span class="text-emerald-700 font-semibold">TUWASILIANE</span><h2 class="text-4xl font-bold mt-2">Tuko Hapa Kukusaidia</h2><div class="grid md:grid-cols-3 gap-8 mt-12">
        <div class="glass-card rounded-2xl p-6 hover:-translate-y-1 transition"><i class="icofont-phone-circle text-5xl text-amber-600"></i><h3 class="text-xl font-bold mt-3">Piga Simu</h3><p class="text-gray-600">+255 685 496 334 <br> +255 714 019 466</p><a href="tel:+255685496334" class="inline-block mt-4 btn-premium text-white px-6 py-2 rounded-full text-sm">Piga Sasa</a></div>
        <div class="glass-card rounded-2xl p-6 hover:-translate-y-1 transition"><i class="icofont-envelope text-5xl text-emerald-700"></i><h3 class="text-xl font-bold mt-3">Barua Pepe</h3><p class="text-gray-600">mauzosheet9@gmail.com</p><a href="mailto:mauzosheet9@gmail.com" class="inline-block mt-4 btn-outline-premium px-6 py-2 rounded-full">Tuma Ujumbe</a></div>
        <div class="glass-card rounded-2xl p-6 hover:-translate-y-1 transition"><i class="icofont-whatsapp text-5xl text-green-500"></i><h3 class="text-xl font-bold mt-3">WhatsApp</h3><p class="text-gray-600">+255 685 496 334</p><a href="https://wa.me/255685496334" target="_blank" class="inline-block mt-4 bg-green-500 text-white px-6 py-2 rounded-full shadow hover:bg-green-600">Tuma Ujumbe</a></div>
      </div></div>
    </section>

    <!-- Final CTA section -->
    <section class="py-20 bg-white relative"><div class="max-w-7x2 mx-auto text-center px-6"><div class="bg-gradient-to-r from-amber-600/10 via-emerald-600/10 to-amber-600/10 rounded-3xl p-10 md:p-16"><h2 class="text-3xl md:text-5xl font-bold">Jiunge na Wafanyabiashara 500+ Leo</h2><p class="text-gray-600 text-xl mt-4 max-w-2xl mx-auto">Anza safari yako na MauzoSheetAI, upate urahisi wa kidijitali na ukuaji wa haraka.</p><div class="flex flex-wrap gap-5 justify-center mt-10"><a href="{{ route('register') }}" class="btn-premium px-8 py-4 rounded-xl text-white text-lg">Anza Kujaribu Bure</a><a href="#contact" class="border border-amber-600 text-amber-700 px-8 py-4 rounded-xl font-medium hover:bg-amber-50">Wasiliana Nasi</a></div></div></div></section>
  </main>

  <!-- Footer Premium with socials @mauzosheetai -->
  <footer class="bg-gray-900 text-gray-300 py-12">
    <div class="max-w-7x2 mx-auto px-6 md:px-8 grid md:grid-cols-4 gap-8">
      <div><a href="#home" class="flex items-center gap-2"><img src="https://test.mauzosheet.com/assets/images/apple-icon.gif" class="h-8 w-8"><span class="font-bold text-white text-xl">MauzoSheetAI</span></a><p class="text-sm mt-3">Mfumo wa POS na usimamizi wa biashara wa kisasa, Tanzania.</p><div class="flex space-x-4 mt-4"><a href="#" class="hover:text-white"><i class="icofont-instagram text-xl"></i></a><a href="#" class="hover:text-white"><i class="icofont-facebook text-xl"></i></a><a href="#" class="hover:text-white"><i class="icofont-tiktok text-xl"></i></a><a href="#" class="hover:text-white"><i class="icofont-whatsapp text-xl"></i></a></div><p class="text-xs mt-2">@mauzosheetai</p></div>
      <div><h4 class="text-white font-semibold">Menyu</h4><ul class="space-y-2 mt-3 text-sm"><li><a href="#home" class="hover:text-amber-400">Nyumbani</a></li><li><a href="#about" class="hover:text-amber-400">Kuhusu</a></li><li><a href="#features" class="hover:text-amber-400">Huduma</a></li><li><a href="#pricing" class="hover:text-amber-400">Vifurushi</a></li></ul></div>
      <div><h4 class="text-white font-semibold">Msaada</h4><ul class="space-y-2 mt-3 text-sm"><li><a href="#" class="hover:text-amber-400">Maswali</a></li><li><a href="#" class="hover:text-amber-400">Sera ya faragha</a></li><li><a href="#" class="hover:text-amber-400">Masharti</a></li><li><a href="#contact" class="hover:text-amber-400">Wasiliana</a></li></ul></div>
      <div><h4 class="text-white font-semibold">Pakua App</h4><a href="https://play.google.com/store/apps/details?id=io.android.MauzoSheet" target="_blank" class="inline-flex items-center gap-2 bg-gray-800 px-4 py-2 rounded-lg mt-3 hover:bg-gray-700"><i class="icofont-android-tablet text-2xl"></i><span>Google Play</span></a></div>
    </div>
    <div class="text-center text-gray-500 text-sm border-t border-gray-800 mt-10 pt-6">&copy; <span id="year"></span> MauzoSheetAI. Haki zote zimehifadhiwa. <span class="text-amber-500">@mauzosheetai</span></div>
  </footer>

  <script>
    // Mobile menu toggle
    const btn = document.getElementById('mobileMenuBtn');
    const nav = document.getElementById('mobileNav');
    btn.addEventListener('click', () => {
      nav.classList.toggle('mobile-nav-closed');
      nav.classList.toggle('mobile-nav-open');
    });
    document.querySelectorAll('#mobileNav a').forEach(link => link.addEventListener('click',()=>{ nav.classList.add('mobile-nav-closed'); nav.classList.remove('mobile-nav-open');}));
    document.getElementById('year').innerText = new Date().getFullYear();

    // Scroll reveal animation observer
    const revealElements = document.querySelectorAll('.scroll-reveal');
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => { if(entry.isIntersecting) entry.target.classList.add('revealed'); });
    }, { threshold: 0.15 });
    document.querySelectorAll('.glass-card, .feature-card, .pricing-card, .step-card').forEach(el => { el.classList.add('scroll-reveal'); observer.observe(el); });
    document.querySelectorAll('.grid > div').forEach(el => { if(!el.classList.contains('scroll-reveal')) { el.classList.add('scroll-reveal'); observer.observe(el); } });
    // manual trigger for existing elements
    document.querySelectorAll('.bg-white.rounded-2xl, .relative.bg-white.rounded-3xl').forEach(c => { c.classList.add('scroll-reveal'); observer.observe(c); });
    window.dispatchEvent(new Event('scroll'));
  </script>
</body>
</html>