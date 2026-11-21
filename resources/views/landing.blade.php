<!DOCTYPE html>
<html lang="sw">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>MauzoSheetAI | Mfumo wa Kisasa wa POS na Biashara</title>

{{-- ============ FAVICON & BRANDING ============ --}}

{{-- Browser favicon --}}
<link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
<link rel="icon" type="image/png" sizes="96x96" href="{{ asset('favicon-96x96.png') }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">

{{-- Browser theme --}}
<meta name="theme-color" content="#065f46">
<meta name="application-name" content="MauzoSheetAI">

{{-- SEO --}}
<meta name="description" content="MauzoSheetAI — Mfumo wa kisasa wa POS, usimamizi wa stoku, mauzo, na ripoti za biashara Tanzania.">
<meta name="author" content="MauzoSheetAI">
<link rel="canonical" href="https://www.mauzosheetai.co.tz/">

{{-- Open Graph --}}
<meta property="og:type" content="website">
<meta property="og:site_name" content="MauzoSheetAI">
<meta property="og:title" content="MauzoSheetAI | Mfumo wa Kisasa wa POS na Biashara">
<meta property="og:description" content="Uza, dhibiti stoku, fuatilia faida na simamia biashara yako kwa mfumo wa kisasa wa POS Tanzania.">
<meta property="og:url" content="https://www.mauzosheetai.co.tz/">
<meta property="og:image" content="{{ asset('apple-touch-icon.png') }}">
<meta property="og:image:alt" content="MauzoSheetAI official logo">

{{-- Twitter / X --}}
<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="MauzoSheetAI | POS na Biashara">
<meta name="twitter:description" content="Mfumo wa kisasa wa POS, mauzo na usimamizi wa stoku kwa biashara Tanzania.">
<meta name="twitter:image" content="{{ asset('apple-touch-icon.png') }}">

{{-- Organization structured data --}}
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "Organization",
  "name": "MauzoSheetAI",
  "url": "https://www.mauzosheetai.co.tz/",
  "logo": "{{ asset('apple-touch-icon.png') }}"
}
</script>

{{-- ============ END FAVICON & BRANDING ============ --}}

  <!-- Google Fonts & Font Awesome -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <script src="https://cdn.tailwindcss.com"></script>

  @verbatim
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Inter', system-ui, -apple-system, sans-serif; background-color: #FFFBF6; color: #1e2a2a; overflow-x: hidden; }
    .text-gradient-primary { background: linear-gradient(125deg, #d97706 0%, #065f46 100%); background-clip: text; -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    .glass-nav { background: rgba(255, 255, 245, 0.92); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(217, 119, 6, 0.15); }
    .glass-card { background: rgba(255, 255, 245, 0.85); backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.7); box-shadow: 0 20px 35px -12px rgba(0, 0, 0, 0.05); }
    @keyframes floatSoft { 0% { transform: translateY(0px); } 50% { transform: translateY(-12px); } 100% { transform: translateY(0px); } }
    .floating-element { animation: floatSoft 5s ease-in-out infinite; }
    .hover-lift { transition: transform 0.25s ease, box-shadow 0.3s ease; }
    .hover-lift:hover { transform: translateY(-6px); box-shadow: 0 25px 35px -12px rgba(0, 0, 0, 0.12); }
    .btn-gold { background: linear-gradient(100deg, #d97706, #b45309); box-shadow: 0 8px 18px -8px rgba(217, 119, 6, 0.4); transition: all 0.25s; }
    .btn-gold:hover { transform: translateY(-3px); box-shadow: 0 14px 26px -8px rgba(217, 119, 6, 0.5); }
    .btn-outline-emerald { border: 1.5px solid #065f46; background: transparent; transition: all 0.2s; }
    .btn-outline-emerald:hover { background: #065f46; color: white; transform: translateY(-2px); }
    .yearly-premium { box-shadow: 0 0 0 0 rgba(217, 119, 6, 0.4), 0 25px 40px -12px rgba(0, 0, 0, 0.25); animation: gentlePulse 2.2s infinite ease-out; border: 1px solid rgba(217, 119, 6, 0.5); }
    @keyframes gentlePulse { 0% { box-shadow: 0 0 0 0 rgba(217, 119, 6, 0.35), 0 25px 40px -12px rgba(0, 0, 0, 0.2); } 70% { box-shadow: 0 0 0 12px rgba(217, 119, 6, 0), 0 25px 40px -12px rgba(0, 0, 0, 0.2); } 100% { box-shadow: 0 0 0 0 rgba(217, 119, 6, 0), 0 25px 40px -12px rgba(0, 0, 0, 0.2); } }
    .cursor-blink-premium { display: inline-block; width: 3px; height: 1.3em; background-color: #d97706; margin-left: 6px; animation: blinkCursor 1s step-end infinite; vertical-align: middle; border-radius: 4px; }
    @keyframes blinkCursor { 0%,100%{opacity:1;} 50%{opacity:0;} }
    .reveal-on-scroll { opacity: 0; transform: translateY(32px); transition: opacity 0.75s cubic-bezier(0.2, 0.9, 0.4, 1.1), transform 0.7s ease; }
    .reveal-on-scroll.revealed { opacity: 1; transform: translateY(0); }
    .section-divider { background: linear-gradient(90deg, #fef3e4 0%, #ffffff 100%); height: 4px; width: 80px; margin: 0 auto; border-radius: 4px; }
    .navbar-transition { transition: all 0.3s ease; }
    .hover\:shadow-2xl:hover { box-shadow: 0 25px 40px -12px rgba(0, 0, 0, 0.15); }
    .whatsapp-link, .call-link, .email-link { transition: all 0.2s ease; }
    .social-icon-only { transition: transform 0.2s ease, color 0.2s ease; }
    .social-icon-only:hover { transform: translateY(-3px); color: #f59e0b !important; }
    .logo-img { object-fit: contain; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
    .dashboard-img { border-radius: 24px; box-shadow: 0 25px 45px -12px rgba(0,0,0,0.25); border: 4px solid white; transition: all 0.3s; }
    .dashboard-img:hover { transform: scale(1.01); box-shadow: 0 30px 55px -15px rgba(0,0,0,0.3); }
  </style>
  @endverbatim
</head>
<body>

<!-- ambient blobs -->
<div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
  <div class="absolute top-[10%] -left-[10%] w-[30rem] h-[30rem] bg-amber-300/25 rounded-full blur-[100px]"></div>
  <div class="absolute bottom-[5%] right-[0%] w-[35rem] h-[35rem] bg-emerald-300/20 rounded-full blur-[110px]"></div>
  <div class="absolute top-[50%] left-[40%] w-[25rem] h-[25rem] bg-amber-200/15 rounded-full blur-[90px]"></div>
</div>

<!-- NAVBAR -->
<nav id="navbar" class="sticky top-0 z-50 w-full glass-nav navbar-transition">
  <div class="max-w-7xl mx-auto px-6 md:px-10 py-4 flex justify-between items-center">
    <div class="flex items-center gap-3">
      <img src="{{ asset('logo11.png') }}" alt="MauzoSheetAI Logo" class="h-12 w-auto logo-img" onerror="this.onerror=null; this.src='https://placehold.co/200x60/d97706/white?text=MauzoSheetAI';">
      <span class="font-extrabold text-2xl tracking-tight bg-gradient-to-r from-amber-700 to-emerald-800 bg-clip-text text-transparent">MauzoSheet<span class="text-gray-800">AI</span></span>
    </div>
    <div class="hidden md:flex items-center gap-7">
      <a href="#home" class="font-medium text-gray-700 hover:text-amber-600 transition-all">Nyumbani</a>
      <a href="#features" class="font-medium text-gray-700 hover:text-amber-600">Vipengele</a>
      <a href="#how-it-works" class="font-medium text-gray-700 hover:text-amber-600">Jinsi Inavyofanya Kazi</a>
      <a href="#pricing" class="font-medium text-gray-700 hover:text-amber-600">Vifurushi</a>
      <a href="#contact" class="font-medium text-gray-700 hover:text-amber-600">Wasiliana</a>
    </div>
    <div class="hidden md:flex gap-3">
      <a href="{{ route('login') }}" class="px-5 py-2 font-semibold text-gray-700 hover:text-amber-600">Ingia</a>
      <a href="{{ route('register') }}" class="btn-gold text-white px-6 py-2.5 rounded-xl font-semibold shadow-md">Anza Bure</a>
    </div>
    <button id="mobileMenuToggle" class="md:hidden text-2xl text-gray-700 focus:outline-none" aria-label="Fungua menyu"><i class="fas fa-bars"></i></button>
  </div>
  <div id="mobileMenu" class="md:hidden max-h-0 overflow-hidden transition-all duration-300 bg-white/95 backdrop-blur-lg border-t border-gray-100">
    <div class="flex flex-col space-y-4 px-6 py-5">
      <a href="#home" class="font-medium text-gray-800">Nyumbani</a>
      <a href="#features" class="font-medium text-gray-800">Vipengele</a>
      <a href="#how-it-works" class="font-medium text-gray-800">Jinsi Inavyofanya Kazi</a>
      <a href="#pricing" class="font-medium text-gray-800">Vifurushi</a>
      <a href="#contact" class="font-medium text-gray-800">Wasiliana</a>
      <div class="pt-3 flex flex-col gap-2">
        <a href="{{ route('login') }}" class="text-center bg-gray-100 py-3 rounded-xl font-semibold">Ingia</a>
        <a href="{{ route('register') }}" class="text-center btn-gold text-white py-3 rounded-xl">Anza Bure</a>
      </div>
    </div>
  </div>
</nav>

<main class="relative z-10">
  <!-- HERO SECTION -->
  <section id="home" class="relative pt-20 pb-20 md:pt-28 md:pb-28 overflow-hidden">
    <div class="absolute inset-0 z-0 bg-amber-50/40"></div>
    <div class="relative z-10 max-w-7xl mx-auto px-6 md:px-10 flex flex-col lg:flex-row items-center gap-14">
      <div class="flex-1 text-center lg:text-left">
        <h1 class="text-5xl md:text-6xl lg:text-7xl font-extrabold tracking-tight leading-[1.2]"><span class="text-gradient-primary">Simamia Biashara Yako Kisasa Zaidi</span></h1>
        <p class="text-gray-700 text-lg md:text-xl mt-5 max-w-xl mx-auto lg:mx-0 leading-relaxed">Uza, dhibiti stoku, fuatilia faida, na simamia biashara yako kwa mfumo wa kisasa wa MauzoSheetAI.</p>
        <div class="flex items-center justify-center lg:justify-start gap-2 mt-6 text-xl md:text-2xl font-semibold">
          <span>Perfect for:</span>
          <span id="typingWord" class="text-amber-600 min-w-[180px] text-center"></span>
          <span class="cursor-blink-premium"></span>
        </div>
        <div class="flex flex-col sm:flex-row gap-5 mt-9 justify-center lg:justify-start items-stretch">
          <a href="{{ route('register') }}" class="btn-gold text-white px-6 py-4 rounded-xl flex items-center justify-center gap-2 font-bold shadow-xl text-center flex-1 min-h-[56px]">
            Anza Bure
            <i class="fas fa-rocket"></i>
          </a>
          <div class="flex gap-4 flex-1">
            <a href="{{ route('landing.download-apk') }}" download class="border border-gray-300 bg-white/70 backdrop-blur-sm text-gray-800 px-6 py-4 rounded-xl font-semibold flex items-center justify-center gap-2 transition hover:shadow-md flex-1 min-h-[56px]">
              Pakua App
              <i class="fab fa-android"></i>
            </a>
            <a href="{{ route('login') }}" class="border border-amber-500 text-amber-700 px-6 py-4 rounded-xl font-semibold hover:bg-amber-50 transition flex items-center justify-center flex-1 min-h-[56px]">
              Ingia
            </a>
          </div>
        </div>
        <div class="flex flex-wrap gap-6 justify-center lg:justify-start mt-10 text-center">
          <div><span class="font-black text-amber-600 text-xl">500+</span><p class="text-xs text-gray-500">Businesses</p></div>
          <div><span class="font-black text-emerald-700 text-xl">24/7</span><p class="text-xs text-gray-500">Support</p></div>
          <div><span class="font-black text-amber-600 text-xl">100%</span><p class="text-xs text-gray-500">Fast Reports</p></div>
          <div><i class="fas fa-globe text-emerald-700 text-xl"></i><p class="text-xs text-gray-500">Online POS</p></div>
        </div>
      </div>
      <div class="flex-1 floating-element relative">
        <img src="{{ asset('p1.jpg') }}" alt="MauzoSheetAI POS Dashboard Preview" class="dashboard-img w-full max-w-4xl mx-auto" onerror="this.onerror=null; this.src='https://placehold.co/800x600/fef3e4/d97706?text=POS+Dashboard+Preview';">
        <div class="absolute hidden md:flex -top-4 -right-6 bg-white/90 backdrop-blur-md p-3 rounded-2xl shadow-xl border border-amber-100"><i class="fas fa-chart-line text-amber-600 text-2xl"></i><span class="ml-2 font-bold">+42% mauzo</span></div>
      </div>
    </div>
  </section>

  <!-- FEATURES SECTION -->
  <section id="features" class="py-24 bg-white/40">
    <div class="max-w-7xl mx-auto px-6 md:px-10">
      <div class="text-center mb-14"><span class="text-amber-600 font-bold tracking-wider">VIPENGELE VYA KISASA</span><div class="section-divider my-3"></div><h2 class="text-4xl md:text-5xl font-bold mt-2">Kila Unachohitaji Kwa Uendeshaji Bora</h2></div>
      <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-7">
        <div class="bg-white rounded-2xl p-7 shadow-md hover:shadow-2xl transition border border-gray-100 reveal-on-scroll"><div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center mb-4"><i class="fas fa-cash-register text-amber-700 text-xl"></i></div><h3 class="text-xl font-bold">POS Sales</h3><p class="text-gray-500 mt-1">Uendeshaji wa mauzo kwa kasi na ankara za kitaalamu.</p></div>
        <div class="bg-white rounded-2xl p-7 shadow-md hover:shadow-2xl transition border border-gray-100 reveal-on-scroll"><div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center mb-4"><i class="fas fa-boxes text-emerald-700 text-xl"></i></div><h3 class="text-xl font-bold">Inventory Management</h3><p class="text-gray-500">Dhibiti stoku kwa wakati halisi, arifa za bidhaa chache.</p></div>
        <div class="bg-white rounded-2xl p-7 shadow-md hover:shadow-2xl transition border border-gray-100 reveal-on-scroll"><div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center mb-4"><i class="fas fa-barcode text-amber-700 text-xl"></i></div><h3 class="text-xl font-bold">Barcode Support</h3><p class="text-gray-500">Soma bidhaa haraka kwa barcode au QR.</p></div>
        <div class="bg-white rounded-2xl p-7 shadow-md hover:shadow-2xl transition border border-gray-100 reveal-on-scroll"><div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center mb-4"><i class="fas fa-chart-pie text-emerald-700 text-xl"></i></div><h3 class="text-xl font-bold">Profit Reports</h3><p class="text-gray-500">Ripoti za faida na hasara kwa kina kirefu.</p></div>
        <div class="bg-white rounded-2xl p-7 shadow-md hover:shadow-2xl transition border border-gray-100 reveal-on-scroll"><div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center mb-4"><i class="fas fa-wallet text-amber-700 text-xl"></i></div><h3 class="text-xl font-bold">Expense Tracking</h3><p class="text-gray-500">Fuatilia gharama ili kuongeza ufanisi.</p></div>
        <div class="bg-white rounded-2xl p-7 shadow-md hover:shadow-2xl transition border border-gray-100 reveal-on-scroll"><div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center mb-4"><i class="fas fa-microchip text-emerald-700 text-xl"></i></div><h3 class="text-xl font-bold">AI Business Insights</h3><p class="text-gray-500">Uchambuzi wa mauzo na utabiri wa akili bandia.</p></div>
        <div class="bg-white rounded-2xl p-7 shadow-md hover:shadow-2xl transition border border-gray-100 reveal-on-scroll"><div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center mb-4"><i class="fas fa-users text-amber-700 text-xl"></i></div><h3 class="text-xl font-bold">Multi User Access</h3><p class="text-gray-500">Wafanyakazi wengi, viwango tofauti vya ruhusa.</p></div>
        <div class="bg-white rounded-2xl p-7 shadow-md hover:shadow-2xl transition border border-gray-100 reveal-on-scroll"><div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center mb-4"><i class="fas fa-mobile-alt text-emerald-700 text-xl"></i></div><h3 class="text-xl font-bold">Mobile App Support</h3><p class="text-gray-500">Endesha biashara yako kutoka Android au iOS.</p></div>
      </div>
    </div>
  </section>

  <!-- HOW IT WORKS -->
  <section id="how-it-works" class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-6 text-center"><span class="text-amber-600 font-bold">MWONGOZO RAHISI</span><div class="section-divider my-3"></div><h2 class="text-4xl md:text-5xl font-bold mt-2">Jinsi Inavyofanya Kazi</h2>
      <div class="grid md:grid-cols-3 gap-12 mt-16">
        <div class="reveal-on-scroll"><div class="bg-amber-100 w-20 h-20 rounded-3xl flex items-center justify-center mx-auto text-3xl font-black text-amber-700 shadow-md">1</div><h3 class="text-2xl font-bold mt-5">Jiunge Bure</h3><p class="text-gray-500 mt-2">Jaza maelezo yako na uanze jaribio la siku 14.</p></div>
        <div class="reveal-on-scroll"><div class="bg-emerald-100 w-20 h-20 rounded-3xl flex items-center justify-center mx-auto text-3xl font-black text-emerald-800 shadow-md">2</div><h3 class="text-2xl font-bold mt-5">Sanidi Bidhaa/Stoku</h3><p class="text-gray-500 mt-2">Ongeza au hamisha bidhaa kwa barcode, bei, na kiasi.</p></div>
        <div class="reveal-on-scroll"><div class="bg-amber-100 w-20 h-20 rounded-3xl flex items-center justify-center mx-auto text-3xl font-black text-amber-700 shadow-md">3</div><h3 class="text-2xl font-bold mt-5">Anza Kuuza & Kuchambua</h3><p class="text-gray-500 mt-2">Fanya mauzo, fuatilia faida, na upate maarifa ya AI.</p></div>
      </div>
    </div>
  </section>

  <!-- PRICING PLANS -->
  <section id="pricing" class="py-24 bg-amber-50/20">
    <div class="max-w-5xl mx-auto px-6">
      <div class="text-center mb-12"><span class="text-emerald-700 font-semibold">CHAGUA MPANGO</span><h2 class="text-4xl md:text-5xl font-bold mt-2">Vifurushi vya Bei Nafuu</h2></div>
      <div class="grid md:grid-cols-3 gap-8">
        <div class="bg-white rounded-3xl p-8 shadow-xl border border-gray-100 hover:shadow-2xl transition reveal-on-scroll">
          <div class="text-center">
            <span class="bg-gray-100 px-4 py-1 rounded-full text-sm font-bold text-gray-700">Mwezi mmoja</span>
            <h3 class="text-3xl font-extrabold mt-4">TZS 15,000</h3>
            <p class="text-gray-500">/mwezi</p>
            <ul class="mt-6 space-y-3 text-left">
              <li><i class="fas fa-check-circle text-emerald-600"></i> POS kamili</li>
              <li><i class="fas fa-check-circle text-emerald-600"></i> Usimamizi stoku</li>
              <li><i class="fas fa-check-circle text-emerald-600"></i> Ripoti za kimsingi</li>
            </ul>
            <a href="{{ route('register') }}" class="btn-outline-emerald mt-8 w-full py-3 rounded-xl block text-center font-semibold">Anza Bure</a>
          </div>
        </div>
        <div class="bg-white rounded-3xl p-8 shadow-xl border border-gray-100 hover:shadow-2xl transition reveal-on-scroll">
          <div class="text-center">
            <span class="bg-gray-100 px-4 py-1 rounded-full text-sm font-bold text-gray-700">Miezi 6</span>
            <h3 class="text-3xl font-extrabold mt-4">TZS 80,000</h3>
            <p class="text-gray-500">kwa miezi 6 (Akiba)</p>
            <ul class="mt-6 space-y-3 text-left">
              <li><i class="fas fa-check-circle text-emerald-600"></i> Vipengele vyote vya mwezi</li>
              <li><i class="fas fa-check-circle text-emerald-600"></i> Ripoti za kina</li>
              <li><i class="fas fa-check-circle text-emerald-600"></i> Msaada wa haraka</li>
            </ul>
            <a href="{{ route('register') }}" class="btn-outline-emerald mt-8 w-full py-3 rounded-xl block text-center font-semibold">Chagua</a>
          </div>
        </div>
        <div class="relative bg-white rounded-3xl p-9 shadow-2xl border-2 border-amber-400 transform md:scale-105 yearly-premium reveal-on-scroll">
          <div class="absolute -top-5 left-1/2 -translate-x-1/2 bg-gradient-to-r from-amber-600 to-emerald-700 text-white px-6 py-1.5 rounded-full text-sm font-bold shadow-lg">BEST VALUE</div>
          <div class="text-center">
            <span class="text-amber-600 font-bold">MOST POPULAR</span>
            <h3 class="text-3xl font-extrabold mt-2">Mwaka Mzima</h3>
            <div class="text-5xl font-black text-amber-600 mt-2">TZS 150,000</div>
            <p class="text-gray-400 line-through">TZS 180,000</p>
            <p class="text-emerald-700 font-semibold bg-emerald-50 inline-block px-3 py-1 rounded-full">Okoa TZS 30,000</p>
            <ul class="mt-6 space-y-3 text-left">
              <li><i class="fas fa-check-circle text-amber-600"></i> Analytics za AI na upendeleo</li>
              <li><i class="fas fa-check-circle text-amber-600"></i> Usaidizi wa kipaumbele 24/7</li>
              <li><i class="fas fa-check-circle text-amber-600"></i> Mafunzo ya biashara bure</li>
              <li><i class="fas fa-check-circle text-amber-600"></i> Nyongeza za siku zijazo</li>
            </ul>
            <a href="{{ route('register') }}" class="btn-gold mt-8 w-full py-4 rounded-xl text-white font-bold block text-center">Chagua Mwaka Mzima</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CONTACT SECTION -->
  <section id="contact" class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-6 text-center">
      <span class="text-amber-600 font-bold tracking-wide">MAWASILIANO YETU</span>
      <div class="section-divider my-3"></div>
      <h2 class="text-5xl md:text-6xl font-bold mt-2 mb-6">Wasiliana Nasi Sasa</h2>
      <p class="text-gray-600 text-lg max-w-2xl mx-auto mb-12">Timu yetu iko tayari kukusaidia kwa maswali yoyote, usaidizi wa kiufundi au maoni.</p>
      <div class="grid md:grid-cols-3 gap-8 max-w-7xl mx-auto">
        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all reveal-on-scroll">
          <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4"><i class="fab fa-whatsapp text-4xl text-green-600"></i></div>
          <h3 class="text-xl font-bold text-gray-800">WhatsApp</h3>
          <p class="text-gray-500 mt-2">Pata msaada wa haraka kupitia WhatsApp</p>
          <a href="https://wa.me/255685496334" target="_blank" rel="noopener" class="inline-block mt-4 bg-green-600 text-white px-6 py-2 rounded-full font-semibold hover:bg-green-700 transition whatsapp-link">+255 685 496 334</a>
        </div>
        <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all reveal-on-scroll">
          <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4"><i class="fas fa-phone-alt text-4xl text-amber-600"></i></div>
          <h3 class="text-xl font-bold text-gray-800">Simu / Call</h3>
          <p class="text-gray-500 mt-2">Wasiliana nasi kwa simu masaa yote</p>
          <a href="tel:+255685496334" class="inline-block mt-4 bg-amber-600 text-white px-6 py-2 rounded-full font-semibold hover:bg-amber-700 transition call-link">+255 685 496 334</a>
          <p class="text-xs text-gray-500 mt-2">Pia: +255 714 019 466</p>
        </div>
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all reveal-on-scroll">
          <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4"><i class="fas fa-envelope text-4xl text-blue-600"></i></div>
          <h3 class="text-xl font-bold text-gray-800">Barua Pepe</h3>
          <p class="text-gray-500 mt-2">Tuandikie kwa masuala rasmi</p>
          <a href="mailto:mauzosheet9@gmail.com" class="inline-block mt-4 bg-blue-600 text-white px-6 py-2 rounded-full font-semibold hover:bg-blue-700 transition email-link">mauzosheet9@gmail.com</a>
        </div>
      </div>
    </div>
  </section>

  <!-- FINAL CTA -->
  <section class="py-20 bg-gradient-to-r from-gray-900 to-emerald-900 text-white">
    <div class="max-w-5xl mx-auto text-center px-6"><h2 class="text-4xl md:text-6xl font-bold">Tayari Kuinua Biashara Yako?</h2><p class="text-gray-200 text-xl mt-4">Jiunge na zaidi ya wafanyabiashara 500 wanaotumia MauzoSheetAI</p><div class="flex flex-wrap gap-6 justify-center mt-8"><a href="{{ route('register') }}" class="btn-gold text-white px-8 py-4 rounded-xl text-lg font-bold">Anza Bure Sasa</a><a href="{{ route('login') }}" class="border-2 border-white px-8 py-4 rounded-xl font-semibold hover:bg-white hover:text-gray-900 transition">Ingia</a></div></div>
  </section>

  <!-- FOOTER -->
  <footer class="bg-gray-900 text-gray-400 pt-14 pb-8">
    <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-4 gap-10">
      <div>
        <img src="{{ asset('logo11.png') }}" alt="MauzoSheetAI Logo" class="h-12 w-auto mb-3 logo-img" onerror="this.onerror=null; this.src='https://placehold.co/200x60/d97706/white?text=MauzoSheetAI';">
        <p class="text-sm mt-3">MauzoSheetAI — Mfumo wa kisasa wa POS na usimamizi wa biashara Tanzania.</p>
        <div class="flex gap-5 mt-4 text-xl">
          <a href="https://www.instagram.com/firefox_designer" target="_blank" rel="noopener" class="hover:text-amber-400 transition social-icon-only" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
          <a href="https://www.facebook.com/mauzosheetai" target="_blank" rel="noopener" class="hover:text-amber-400 transition social-icon-only" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
          <a href="https://wa.me/255685496334" target="_blank" rel="noopener" class="hover:text-amber-400 transition social-icon-only" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
        </div>
      </div>
      <div><h5 class="text-white font-semibold">Viungo</h5><ul class="space-y-2 mt-3"><li><a href="#home" class="hover:text-amber-400">Nyumbani</a></li><li><a href="#features" class="hover:text-amber-400">Vipengele</a></li><li><a href="#pricing" class="hover:text-amber-400">Bei</a></li></ul></div>
      <div><h5 class="text-white font-semibold">Msaada</h5><ul class="space-y-2 mt-3"><li><a href="#" class="hover:text-amber-400">Sera ya Faragha</a></li><li><a href="#" class="hover:text-amber-400">Masharti ya Matumizi</a></li><li><a href="{{ route('login') }}" class="hover:text-amber-400">Ingia kwenye akaunti</a></li></ul></div>
      <div><h5 class="text-white font-semibold">Wasiliana Nasi</h5><p class="mt-2"><i class="fas fa-phone-alt mr-2"></i> +255 685 496 334</p><p><i class="fas fa-envelope mr-2"></i> mauzosheet9@gmail.com</p><p><i class="fas fa-map-marker-alt mr-2"></i> Dar es Salaam, Tanzania</p><a href="{{ route('landing.download-apk') }}" download class="inline-block mt-3 bg-gray-800 px-5 py-2 rounded-lg hover:bg-gray-700 transition"><i class="fab fa-google-play"></i> Pakua App</a></div>
    </div>
    <div class="text-center border-t border-gray-800 mt-12 pt-6 text-sm">© 2025 MauzoSheetAI. Haki zote zimehifadhiwa. Imetengenezwa kwa ajili ya wajasiriamali wa Tanzania.</div>
  </footer>
</main>

@verbatim
<script>
  // Mobile menu toggle
  const toggleBtn = document.getElementById('mobileMenuToggle');
  const mobileMenu = document.getElementById('mobileMenu');
  if(toggleBtn) {
    toggleBtn.addEventListener('click', () => {
      if (mobileMenu.style.maxHeight && mobileMenu.style.maxHeight !== '0px') mobileMenu.style.maxHeight = '0px';
      else mobileMenu.style.maxHeight = mobileMenu.scrollHeight + 'px';
    });
  }

  // Dynamic typing
  const words = ["Pharmacy", "Hardware", "Restaurant", "Boutique", "Supermarket", "Stationery", "Retail Shop", "Salon", "Electronics", "Mini Market", "Butchery", "Spare Parts"];
  let wordIndex = 0, letterIndex = 0, isDeletingFlag = false;
  const typedSpan = document.getElementById('typingWord');
  function animateTyping() {
    if(!typedSpan) return;
    const currentWord = words[wordIndex];
    if (isDeletingFlag) {
      typedSpan.innerText = currentWord.substring(0, letterIndex-1);
      letterIndex--;
      if (letterIndex === 0) {
        isDeletingFlag = false;
        wordIndex = (wordIndex + 1) % words.length;
      }
    } else {
      typedSpan.innerText = currentWord.substring(0, letterIndex+1);
      letterIndex++;
      if (letterIndex === currentWord.length) isDeletingFlag = true;
    }
    setTimeout(animateTyping, isDeletingFlag ? 60 : 100);
  }
  animateTyping();

  // Scroll reveal
  const revealElements = document.querySelectorAll('.reveal-on-scroll');
  const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => { if (entry.isIntersecting) entry.target.classList.add('revealed'); });
  }, { threshold: 0.2 });
  revealElements.forEach(el => revealObserver.observe(el));

  // Navbar background effect
  window.addEventListener('scroll', () => {
    const nav = document.getElementById('navbar');
    if (window.scrollY > 20) nav.classList.add('shadow-md', 'bg-white/90');
    else nav.classList.remove('shadow-md', 'bg-white/90');
  });
</script>
@endverbatim

{{-- Inline WhatsApp button — no @include, no external file --}}
@verbatim
<a href="https://wa.me/255614356830?text=Hello%20boss%20nahitaji%20msaada"
   target="_blank" rel="noopener"
   aria-label="Wasiliana nasi WhatsApp"
   title="Una swali? Wasiliana nasi WhatsApp"
   style="position:fixed;bottom:1.25rem;right:1.25rem;z-index:9999;width:3.5rem;height:3.5rem;border-radius:9999px;background:#25D366;display:flex;align-items:center;justify-content:center;box-shadow:0 8px 24px rgba(0,0,0,.30);transition:transform .2s ease;"
   onmouseover="this.style.transform='scale(1.08)'" onmouseout="this.style.transform='scale(1)'">
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="30" height="30" fill="#ffffff" aria-hidden="true"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-32.4 3.2-7.2 1.6-13.4-.6-18.6-2.1-4.9-12.5-30.1-17.1-41-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/></svg>
</a>
@endverbatim
</body>
</html>