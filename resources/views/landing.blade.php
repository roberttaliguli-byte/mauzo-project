<!DOCTYPE html>
<html lang="sw">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>MauzoSheetAI | Mfumo wa Kisasa wa POS na Biashara</title>
  <!-- Google Fonts & Font Awesome -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <script src="https://cdn.tailwindcss.com"></script>
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
  <div class="max-w-7x2 mx-auto px-6 md:px-10 py-4 flex justify-between items-center">
    <div class="flex items-center gap-3">
      <!-- Logo image from mauzo/public/logo11.jpg -->
      <img src="{{ asset('logo11.jpg') }}" alt="MauzoSheetAI Logo" class="h-12 w-auto logo-img" onerror="this.onerror=null; this.src='https://placehold.co/200x60/d97706/white?text=MauzoSheetAI';">
      <span class="font-extrabold text-2xl tracking-tight bg-gradient-to-r from-amber-700 to-emerald-800 bg-clip-text text-transparent">MauzoSheet<span class="text-gray-800">AI</span></span>
    </div>
    <div class="hidden md:flex items-center gap-7">
      <a href="{{ route('landing') }}#home" class="font-medium text-gray-700 hover:text-amber-600 transition-all">Nyumbani</a>
      <a href="{{ route('landing') }}#features" class="font-medium text-gray-700 hover:text-amber-600">Vipengele</a>
      <a href="{{ route('landing') }}#how-it-works" class="font-medium text-gray-700 hover:text-amber-600">Jinsi Inavyofanya Kazi</a>
      <a href="{{ route('landing') }}#pricing" class="font-medium text-gray-700 hover:text-amber-600">Vifurushi</a>
      <a href="{{ route('landing') }}#contact" class="font-medium text-gray-700 hover:text-amber-600">Wasiliana</a>
    </div>
    <div class="hidden md:flex gap-3">
      <a href="{{ route('login') }}" class="px-5 py-2 font-semibold text-gray-700 hover:text-amber-600">Ingia</a>
      <a href="{{ route('register') }}" class="btn-gold text-white px-6 py-2.5 rounded-xl font-semibold shadow-md">Anza Bure</a>
    </div>
    <button id="mobileMenuToggle" class="md:hidden text-2xl text-gray-700 focus:outline-none"><i class="fas fa-bars"></i></button>
  </div>
  <div id="mobileMenu" class="md:hidden max-h-0 overflow-hidden transition-all duration-300 bg-white/95 backdrop-blur-lg border-t border-gray-100">
    <div class="flex flex-col space-y-4 px-6 py-5">
      <a href="{{ route('landing') }}#home" class="font-medium text-gray-800">Nyumbani</a>
      <a href="{{ route('landing') }}#features" class="font-medium text-gray-800">Vipengele</a>
      <a href="{{ route('landing') }}#how-it-works" class="font-medium text-gray-800">Jinsi Inavyofanya Kazi</a>
      <a href="{{ route('landing') }}#pricing" class="font-medium text-gray-800">Vifurushi</a>
      <a href="{{ route('landing') }}#contact" class="font-medium text-gray-800">Wasiliana</a>
      <div class="pt-3 flex flex-col gap-2">
        <a href="{{ route('login') }}" class="text-center bg-gray-100 py-3 rounded-xl font-semibold">Ingia</a>
        <a href="{{ route('register') }}" class="text-center btn-gold text-white py-3 rounded-xl">Anza Bure</a>
      </div>
    </div>
  </div>
</nav>

<main class="relative z-10">
  <!-- HERO SECTION with dashboard image p1.jpg -->
  <section id="home" class="relative pt-20 pb-20 md:pt-28 md:pb-28 overflow-hidden">
    <div class="absolute inset-0 z-0 bg-amber-50/40"></div>
    <div class="relative z-10 max-w-8x2 mx-auto px-6 md:px-10 flex flex-col lg:flex-row items-center gap-14">
      <div class="flex-1 text-center lg:text-left">
        <h1 class="text-5xl md:text-6xl lg:text-7xl font-extrabold tracking-tight leading-[1.2]"><span class="text-gradient-primary">Simamia Biashara Yako Kisasa Zaidi</span></h1>
        <p class="text-gray-700 text-lg md:text-xl mt-5 max-w-xl mx-auto lg:mx-0 leading-relaxed">Uza, dhibiti stoku, fuatilia faida, na simamia biashara yako kwa mfumo wa kisasa wa MauzoSheetAI.</p>
        <div class="flex items-center justify-center lg:justify-start gap-2 mt-6 text-xl md:text-2xl font-semibold">
          <span>Perfect for:</span>
          <span id="typingWord" class="text-amber-600 min-w-[180px] text-center"></span>
          <span class="cursor-blink-premium"></span>
        </div>
  <div class="flex flex-col sm:flex-row gap-5 mt-9 justify-center lg:justify-start items-stretch">
    
    <!-- Main CTA -->
    <a href="{{ route('register') }}"
       class="btn-gold text-white px-6 py-4 rounded-xl flex items-center justify-center gap-2 font-bold shadow-xl text-center flex-1 min-h-[56px]">
        Anza Bure
        <i class="fas fa-rocket"></i>
    </a>

    <!-- Secondary Actions -->
    <div class="flex gap-4 flex-1">
        
        <a href="#"
           class="border border-gray-300 bg-white/70 backdrop-blur-sm text-gray-800 px-6 py-4 rounded-xl font-semibold flex items-center justify-center gap-2 transition hover:shadow-md flex-1 min-h-[56px]">
            Pakua App
            <i class="fab fa-android"></i>
        </a>

        <a href="{{ route('login') }}"
           class="border border-amber-500 text-amber-700 px-6 py-4 rounded-xl font-semibold hover:bg-amber-50 transition flex items-center justify-center flex-1 min-h-[56px]">
            Ingia
        </a>

    </div>
</div>
        <div class="flex flex-wrap gap-6 justify-center lg:justify-start mt-10 text-center">
          <div><span class="font-black text-amber-600 text-xl">500+</span><p class="text-xs text-gray-500">Businesses</p></div>
          <div><span class="font-black text-emerald-700 text-xl">24/7</span><p class="text-xs text-gray-500">Support</p></div>
        <div><i class="fas fa-chart-simple text-amber-600 text-xl">100%</i><p class="text-xs text-gray-500">Fast Reports</p></div>
          <div><i class="fas fa-globe text-emerald-700 text-xl"></i><p class="text-xs text-gray-500">Online POS</p></div>
        </div>
      </div>
      <div class="flex-1 floating-element">
        <!-- Dashboard image from mauzo/public/p1.jpg -->
        <img src="{{ asset('p1.jpg') }}" alt="MauzoSheetAI POS Dashboard Preview" class="dashboard-img w-full max-w-4xl mx-auto" onerror="this.onerror=null; this.src='https://placehold.co/800x600/fef3e4/d97706?text=POS+Dashboard+Preview';">
        <div class="absolute hidden md:flex -top-4 -right-6 bg-white/90 backdrop-blur-md p-3 rounded-2xl shadow-xl border border-amber-100"><i class="fas fa-chart-line text-amber-600 text-2xl"></i><span class="ml-2 font-bold">+42% mauzo</span></div>
      </div>
    </div>
  </section>

  <!-- FEATURES SECTION -->
  <section id="features" class="py-24 bg-white/40">
    <div class="max-w-7x2 mx-auto px-6 md:px-10">
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
    <div class="max-w-7x2 mx-auto px-6 text-center"><span class="text-amber-600 font-bold">MWONGOZO RAHISI</span><div class="section-divider my-3"></div><h2 class="text-4xl md:text-5xl font-bold mt-2">Jinsi Inavyofanya Kazi</h2>
      <div class="grid md:grid-cols-3 gap-12 mt-16">
        <div class="reveal-on-scroll"><div class="bg-amber-100 w-20 h-20 rounded-3xl flex items-center justify-center mx-auto text-3xl font-black text-amber-700 shadow-md">1</div><h3 class="text-2xl font-bold mt-5">Jiunge Bure</h3><p class="text-gray-500 mt-2">Jaza maelezo yako na uanze jaribio la siku 14.</p></div>
        <div class="reveal-on-scroll"><div class="bg-emerald-100 w-20 h-20 rounded-3xl flex items-center justify-center mx-auto text-3xl font-black text-emerald-800 shadow-md">2</div><h3 class="text-2xl font-bold mt-5">Sanidi Bidhaa/Stoku</h3><p class="text-gray-500 mt-2">Ongeza au hamisha bidhaa kwa barcode, bei, na kiasi.</p></div>
        <div class="reveal-on-scroll"><div class="bg-amber-100 w-20 h-20 rounded-3xl flex items-center justify-center mx-auto text-3xl font-black text-amber-700 shadow-md">3</div><h3 class="text-2xl font-bold mt-5">Anza Kuuza & Kuchambua</h3><p class="text-gray-500 mt-2">Fanya mauzo, fuatilia faida, na upate maarifa ya AI.</p></div>
      </div>
    </div>
  </section>

  <!-- PRICING PLANS -->
  <section id="pricing" class="py-24 bg-amber-50/20">
    <div class="max-w-5x2 mx-auto px-6">
      <div class="text-center mb-12"><span class="text-emerald-700 font-semibold">CHAGUA MPANGO</span><h2 class="text-4xl md:text-5xl font-bold mt-2">Vifurushi vya Bei Nafuu</h2></div>
      <div class="grid md:grid-cols-3 gap-8">
        <div class="bg-white rounded-3xl p-8 shadow-xl border border-gray-100 hover:shadow-2xl transition reveal-on-scroll"><div class="text-center"><span class="bg-gray-100 px-4 py-1 rounded-full text-sm font-bold text-gray-700">Mwezi mmoja</span><h3 class="text-3xl font-extrabold mt-4">TZS 15,000</h3><p class="text-gray-500">/mwezi</p><ul class="mt-6 space-y-3 text-left"><li><i class="fas fa-check-circle text-emerald-600"></i> POS kamili</li><li><i class="fas fa-check-circle text-emerald-600"></i> Usimamizi stoku</li><li><i class="fas fa-check-circle text-emerald-600"></i> Ripoti za kimsingi</li></ul><a href="{{ route('register') }}" class="btn-outline-emerald mt-8 w-full py-3 rounded-xl block text-center font-semibold">Anza Bure</a></div></div>
        <div class="bg-white rounded-3xl p-8 shadow-xl border border-gray-100 hover:shadow-2xl transition reveal-on-scroll"><div class="text-center"><span class="bg-gray-100 px-4 py-1 rounded-full text-sm font-bold text-gray-700">Miezi 6</span><h3 class="text-3xl font-extrabold mt-4">TZS 80,000</h3><p class="text-gray-500">kwa miezi 6 (Akiba)</p><ul class="mt-6 space-y-3 text-left"><li><i class="fas fa-check-circle text-emerald-600"></i> Vipengele vyote vya mwezi</li><li><i class="fas fa-check-circle text-emerald-600"></i> Ripoti za kina</li><li><i class="fas fa-check-circle text-emerald-600"></i> Msaada wa haraka</li></ul><a href="{{ route('register') }}" class="btn-outline-emerald mt-8 w-full py-3 rounded-xl block text-center font-semibold">Chagua</a></div></div>
        <div class="relative bg-white rounded-3xl p-9 shadow-2xl border-2 border-amber-400 transform md:scale-105 yearly-premium reveal-on-scroll"><div class="absolute -top-5 left-1/2 -translate-x-1/2 bg-gradient-to-r from-amber-600 to-emerald-700 text-white px-6 py-1.5 rounded-full text-sm font-bold shadow-lg">BEST VALUE</div><div class="text-center"><span class="text-amber-600 font-bold">MOST POPULAR</span><h3 class="text-3xl font-extrabold mt-2">Mwaka Mzima</h3><div class="text-5xl font-black text-amber-600 mt-2">TZS 150,000</div><p class="text-gray-400 line-through">TZS 180,000</p><p class="text-emerald-700 font-semibold bg-emerald-50 inline-block px-3 py-1 rounded-full">Okoa TZS 30,000</p><ul class="mt-6 space-y-3 text-left"><li><i class="fas fa-check-circle text-amber-600"></i> Analyics za AI na upendeleo</li><li><i class="fas fa-check-circle text-amber-600"></i> Usaidizi wa kipaumbele 24/7</li><li><i class="fas fa-check-circle text-amber-600"></i> Mafunzo ya biashara bure</li><li><i class="fas fa-check-circle text-amber-600"></i> Nyongeza za siku zijazo</li></ul><a href="{{ route('register') }}" class="btn-gold mt-8 w-full py-4 rounded-xl text-white font-bold block text-center">Chagua Mwaka Whole</a></div></div>
      </div>
    </div>
  </section>

  <!-- CONTACT SECTION with social handles: Instagram icon + firefox_designer removed, only icon remains + facebook icon only -->
  <section id="contact" class="py-24 bg-white">
    <div class="max-w-7x2 mx-auto px-6 text-center">
      <span class="text-amber-600 font-bold tracking-wide">MAWASILIANO YETU</span>
      <div class="section-divider my-3"></div>
      <h2 class="text-7x2 md:text-5x2 font-bold mt-2 mb-6">Wasiliana Nasi Sasa</h2>
      <p class="text-gray-600 text-lg max-w-2xl mx-auto mb-12">Timu yetu iko tayari kukusaidia kwa maswali yoyote, usaidizi wa kiufundi au maoni.</p>
      <div class="grid md:grid-cols-3 gap-8 max-w-7xl mx-auto">
        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all reveal-on-scroll">
          <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4"><i class="fab fa-whatsapp text-4xl text-green-600"></i></div>
          <h3 class="text-xl font-bold text-gray-800">WhatsApp</h3>
          <p class="text-gray-500 mt-2">Pata msaada wa haraka kupitia WhatsApp</p>
          <a href="https://wa.me/255685496334" target="_blank" class="inline-block mt-4 bg-green-600 text-white px-6 py-2 rounded-full font-semibold hover:bg-green-700 transition whatsapp-link">+255 685 496 334</a>
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
      <!-- Social handles: Instagram only icon (no text), Facebook icon only -->

    </div>
  </section>

  <!-- FINAL CTA -->
  <section class="py-20 bg-gradient-to-r from-gray-900 to-emerald-900 text-white">
    <div class="max-w-5xl mx-auto text-center px-6"><h2 class="text-4xl md:text-6xl font-bold">Tayari Kuinua Biashara Yako?</h2><p class="text-gray-200 text-xl mt-4">Jiunge na zaidi ya wafanyabiashara 500 wanaotumia MauzoSheetAI</p><div class="flex flex-wrap gap-6 justify-center mt-8"><a href="{{ route('register') }}" class="btn-gold text-white px-8 py-4 rounded-xl text-lg font-bold">Anza Bure Sasa</a><a href="{{ route('login') }}" class="border-2 border-white px-8 py-4 rounded-xl font-semibold hover:bg-white hover:text-gray-900 transition">Ingia</a></div></div>
  </section>

  <!-- FOOTER with logo image, Instagram icon only, Facebook icon only (no text) -->
  <footer class="bg-gray-900 text-gray-400 pt-14 pb-8">
    <div class="max-w-7x2 mx-auto px-6 grid md:grid-cols-4 gap-10">
      <div>
        <img src="{{ asset('logo11.jpg') }}" alt="MauzoSheetAI Logo" class="h-12 w-auto mb-3 logo-img" onerror="this.onerror=null; this.src='https://placehold.co/200x60/d97706/white?text=MauzoSheetAI';">
        <p class="text-sm mt-3">MauzoSheetAI — Mfumo wa kisasa wa POS na usimamizi wa biashara Tanzania.</p>
        <div class="flex gap-5 mt-4 text-xl">
          <a href="https://www.instagram.com/firefox_designer" target="_blank" class="hover:text-amber-400 transition"><i class="fab fa-instagram"></i></a>
          <a href="https://www.facebook.com/mauzosheetai" target="_blank" class="hover:text-amber-400 transition"><i class="fab fa-facebook-f"></i></a>
          <a href="https://wa.me/255685496334" target="_blank" class="hover:text-amber-400 transition"><i class="fab fa-whatsapp"></i></a>
        </div>
      </div>
      <div><h5 class="text-white font-semibold">Viungo</h5><ul class="space-y-2 mt-3"><li><a href="{{ route('landing') }}#home" class="hover:text-amber-400">Nyumbani</a></li><li><a href="{{ route('landing') }}#features" class="hover:text-amber-400">Vipengele</a></li><li><a href="{{ route('landing') }}#pricing" class="hover:text-amber-400">Bei</a></li></ul></div>
      <div><h5 class="text-white font-semibold">Msaada</h5><ul class="space-y-2 mt-3"><li><a href="#">Sera ya Faragha</a></li><li><a href="#">Masharti ya Matumizi</a></li><li><a href="{{ route('login') }}">Ingia kwenye akaunti</a></li></ul></div>
      <div><h5 class="text-white font-semibold">Wasiliana Nasi</h5><p class="mt-2"><i class="fas fa-phone-alt mr-2"></i> +255 685 496 334</p><p><i class="fas fa-envelope mr-2"></i> mauzosheet9@gmail.com</p><p><i class="fas fa-map-marker-alt mr-2"></i> Dar es Salaam, Tanzania</p><a href="#" class="inline-block mt-3 bg-gray-800 px-5 py-2 rounded-lg"><i class="fab fa-google-play"></i> Pakua App</a></div>
    </div>
    <div class="text-center border-t border-gray-800 mt-12 pt-6 text-sm">© 2025 MauzoSheetAI. Hakimilikina. Imetengenezwa kwa ajili ya wajasiriamali wa Tanzania.</div>
  </footer>
</main>

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
</body>
</html>