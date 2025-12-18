<!DOCTYPE html>
<html lang="sw">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="MauzoSheet - Mfumo wa kisasa wa kusimamia biashara zako kwa urahisi, usalama, na ufanisi. Endesha mauzo, stoku, na taarifa zako popote ulipo." />
  <meta name="keywords" content="MauzoSheet, biashara, mauzo, mfumo wa biashara, stoku, uhasibu, mauzo app, POS Tanzania" />
  <meta name="author" content="MauzoSheet Team" />
  <meta name="theme-color" content="#d97706" />

  <!-- Open Graph / Social Meta -->
  <meta property="og:title" content="MauzoSheet - Endesha Biashara Yako Kisasa" />
  <meta property="og:description" content="Mfumo wa kimtandao wa kusaidia wafanya biashara kuendesha shughuli zao kwa urahisi, kisasa, na usalama." />
  <meta property="og:image" content="https://test.mauzosheet.com/assets/images/apple-icon.gif" />
  <meta property="og:url" content="https://mauzosheet.com" />
  <meta name="twitter:card" content="summary_large_image" />

  <title>MauzoSheet - Endesha Biashara Yako Kisasa</title>

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="https://test.mauzosheet.com/assets/images/apple-icon.gif" />

  <!-- Fonts & Styles -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/icofont@1.0.1/icofont.min.css" />

  <style>
    :root {
      --amber: #d97706;
      --amber-light: #f59e0b;
      --amber-dark: #b45309;
      --dark-green: #065f46;
      --dark-green-light: #047857;
    }
    
    body {
      font-family: 'Inter', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
      margin: 0;
      padding: 0;
      position: relative;
      z-index: 0;
      scroll-behavior: smooth;
      color: #1f2937;
    }

    /* Background pattern */
    body::before {
      content: '';
      position: fixed;
      inset: 0;
      background: 
        radial-gradient(circle at 20% 80%, rgba(217, 119, 6, 0.05) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(6, 95, 70, 0.05) 0%, transparent 50%),
        radial-gradient(circle at 40% 40%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
      z-index: -1;
    }

    .hero-gradient {
      background: linear-gradient(135deg, rgba(217, 119, 6, 0.446) 0%, rgba(6, 95, 70, 0.413) 100%);
    }

    .text-gradient {
      background: linear-gradient(90deg, var(--amber), var(--dark-green));
      background-clip: text;
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .btn-primary {
      background: linear-gradient(90deg, var(--amber), var(--amber-dark));
      color: white;
      transition: all 0.3s ease;
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 20px rgba(217, 119, 6, 0.3);
    }

    .btn-secondary {
      background: linear-gradient(90deg, var(--dark-green), var(--dark-green-light));
      color: white;
      transition: all 0.3s ease;
    }

    .btn-secondary:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 20px rgba(6, 95, 70, 0.3);
    }

    .feature-card {
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
      transition: all 0.3s ease;
      border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .feature-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.12);
    }

    .nav-link {
      position: relative;
    }

    .nav-link::after {
      content: '';
      position: absolute;
      width: 0;
      height: 2px;
      bottom: -4px;
      left: 0;
      background-color: var(--amber);
      transition: width 0.3s ease;
    }

    .nav-link:hover::after {
      width: 100%;
    }

    .section-divider {
      height: 1px;
      background: linear-gradient(90deg, transparent, rgba(217, 119, 6, 0.3), transparent);
    }

    .angled-section {
      clip-path: polygon(0 0, 100% 5%, 100% 100%, 0 95%);
    }

    .floating {
      animation: floating 3s ease-in-out infinite;
    }

    @keyframes floating {
      0% { transform: translate(0, 0px); }
      50% { transform: translate(0, -10px); }
      100% { transform: translate(0, 0px); }
    }
    
    .pricing-card {
      background: white;
      border-radius: 16px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease;
      overflow: hidden;
    }
    
    .pricing-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }
    
    .pricing-card.popular {
      border: 2px solid var(--amber);
      position: relative;
    }
    
    .popular-badge {
      position: absolute;
      top: -12px;
      right: 20px;
      background: var(--amber);
      color: white;
      padding: 4px 16px;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 600;
    }
  </style>
</head>

<body class="relative min-h-screen">

  <!-- Header -->
<header class="sticky top-0 z-50 bg-gradient-to-r from-amber-200 via-white to-green-200 shadow-sm">
  <div class="max-w-7xl mx-auto flex justify-between items-center py-4 px-6">
    
    <!-- Logo: aligned left and slightly compact -->
    <a href="#home" class="flex items-center space-x-2 shrink-0" aria-label="MauzoSheet Nyumbani">
      <img src="https://test.mauzosheet.com/assets/images/apple-icon.gif" 
           alt="MauzoSheet logo" 
           class="h-8 w-8 md:h-10 md:w-10 object-contain" />
      <span class="font-bold text-lg md:text-xl text-gradient whitespace-nowrap">
        MauzoSheet
      </span>
    </a>

    <!-- Desktop Navigation: aligned right -->
    <nav class="hidden md:flex items-center space-x-8 text-black font-medium">
      <a class="nav-link hover:text-amber-600 transition" href="#home">Nyumbani</a>
      <a class="nav-link hover:text-amber-600 transition" href="#about">Tufahamu</a>
      <a class="nav-link hover:text-amber-600 transition" href="#feature">Huduma Zetu</a>
      <a class="nav-link hover:text-amber-600 transition" href="#pricing">Vifurushi</a>
      <a class="nav-link hover:text-amber-600 transition" href="#testimonials">Shuhuda</a>
      <a class="nav-link hover:text-amber-600 transition" href="#contact">Mawasiliano</a>
    </nav>

    <!-- Mobile Menu Button -->
    <button id="mobile-menu-btn" 
            class="md:hidden text-black focus:outline-none" 
            aria-label="Fungua menyu ya simu">
      <svg xmlns="http://www.w3.org/2000/svg" 
           class="h-6 w-6" 
           fill="none"
           viewBox="0 0 24 24" 
           stroke="currentColor">
        <path stroke-linecap="round" 
              stroke-linejoin="round" 
              stroke-width="2"
              d="M4 6h16M4 12h16M4 18h16" />
      </svg>
    </button>

  </div>
</header>


    <!-- Mobile Nav -->
    <nav id="mobile-nav" class="hidden flex-col space-y-4 p-6 bg-white border-t md:hidden">
      <a href="#home" class="py-2 font-medium text-black hover:text-amber-600 transition">Nyumbani</a>
      <a href="#about" class="py-2 font-medium text-black hover:text-amber-600 transition">Tufahamu</a>
      <a href="#feature" class="py-2 font-medium text-black hover:text-amber-600 transition">Huduma Zetu</a>
      <a href="#pricing" class="py-2 font-medium text-black hover:text-amber-600 transition">Vifurushi</a>
      <a href="#testimonials" class="py-2 font-medium text-black hover:text-amber-600 transition">Shuhuda</a>
      <a href="#contact" class="py-2 font-medium text-black-700 hover:text-amber-600 transition">Mawasiliano</a>

    </nav>
  </header>

<!-- Hero Section -->
<section id="home" class="hero-gradient relative py-20 lg:py-28 overflow-hidden">
  <div class="max-w-7xl mx-auto px-6 flex flex-col lg:flex-row items-center gap-12 relative">

    <!-- Left Side: Text -->
    <div class="lg:w-1/2 space-y-6 text-left z-10">
      <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight">
        <span class="text-gradient">Biashara Yako,</span><br>
        <span class="text-gray-800">Imerahisishwa</span>
      </h1>
      <p class="text-lg md:text-xl text-black max-w-lg">
        MauzoSheet hukusaidia kusimamia, kuchambua, na kuendesha biashara zako kwa urahisi, usalama, na ufanisi.
      </p>
      <div class="flex flex-wrap gap-4 mt-8">
        <a href="https://play.google.com/store/apps/details?id=io.android.MauzoSheet" 
           target="_blank" rel="noopener noreferrer"
           class="btn-primary py-3 px-6 rounded-lg font-semibold text-center">
          Pakua Programu
        </a>
        <a href="{{ route('login') }}" 
           class="btn-secondary py-3 px-6 rounded-lg font-semibold text-center">
          Ingia
        </a>
        <a href="{{ route('register') }}" 
           class="btn-primary py-3 px-6 rounded-lg font-semibold text-center">
          Jisajili
        </a>
      </div>
      <div class="pt-6 flex items-center gap-6">
        <div class="text-center">
          <p class="text-2xl font-bold text-amber-600">500+</p>
          <p class="text-sm text-black">Wafanyabiashara</p>
        </div>
        <div class="h-8 w-px bg-gray-300"></div>
        <div class="text-center">
          <p class="text-2xl font-bold text-dark-green">10K+</p>
          <p class="text-sm text-black">Mauzo Kila Mwezi</p>
        </div>
        <div class="h-8 w-px bg-gray-300"></div>
        <div class="text-center">
          <p class="text-2xl font-bold text-amber-600">98%</p>
          <p class="text-sm text-black">Wateja Wameridhika</p>
        </div>
      </div>
    </div>

    <!-- Right Side: Large Image -->
    <div class="lg:w-1/2 relative flex justify-center items-center z-10">
      <img src="p1.jpg" 
           alt="Mfumo wa MauzoSheet" 
           class="rounded-3xl shadow-2xl w-[100%] md:w-[700px] lg:w-[850px] floating" />

      <!-- Decorative circles -->
      <div class="absolute -bottom-12 -left-12 w-44 h-44 bg-amber-100 rounded-full opacity-60 z-0"></div>
      <div class="absolute -top-12 -right-12 w-36 h-36 bg-green-100 rounded-full opacity-60 z-0"></div>

      <!-- Fog/Mvuke effect -->
      <div class="absolute inset-0 z-0 pointer-events-none">
        <div class="absolute w-full h-full bg-gradient-to-t from-white/20 via-transparent to-white/0 rounded-3xl animate-fade-slow"></div>
      </div>
    </div>

  </div>
</section>

<style>
/* Floating animation */
@keyframes floating {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-15px); }
}
.floating { animation: floating 4s ease-in-out infinite; }

/* Slow fading fog effect */
@keyframes fade-slow {
  0%, 100% { opacity: 0.3; }
  50% { opacity: 0.05; }
}
.animate-fade-slow { animation: fade-slow 6s ease-in-out infinite; }
</style>


  <!-- About Section -->
  <section id="about" class="py-20 bg-gradient-to-r from-amber-600 to-green-200">

    <div class="max-w-5xl mx-auto px-6 text-center space-y-8">
      <div class="space-y-4">
        <h2 class="text-3xl md:text-4xl font-bold text-gray-800">Kuhusu <span class="text-gradient">MauzoSheet</span></h2>
        <div class="section-divider w-24 mx-auto"></div>
      </div>
      <p class="text-lg text-black max-w-3xl mx-auto">
        MauzoSheet ni mfumo wa kimtandao unaosaidia wafanyabiashara wa rejareja na jumla kusimamia biashara zao kwa njia ya kisasa, salama, na yenye ufanisi.
      </p>
      <p class="text-lg font-medium text-dark-green max-w-3xl mx-auto">
        Uza au kopesha bidhaa zako kwa urahisi kupitia MauzoSheet. Jiunge leo!
      </p>
      
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8 pt-8">
        <div class="feature-card p-6 text-center">
          <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-amber-50 flex items-center justify-center">
            <i class="icofont-target text-2xl text-amber-600"></i>
          </div>
          <h3 class="text-xl font-bold text-gray-800 mb-2">Lengo Letu</h3>
          <p class="text-black">Kurahisisha uendeshaji wa biashara kwa wajasiriamali wote Tanzania.</p>
        </div>
        
        <div class="feature-card p-6 text-center">
          <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-green-50 flex items-center justify-center">
            <i class="icofont-eye text-2xl text-dark-green"></i>
          </div>
          <h3 class="text-xl font-bold text-gray-800 mb-2">Mtazamo Wetu</h3>
          <p class="text-black">Kuwa chombo kikuu cha uongozi wa kidijitali kwa wafanyabiashara Tanzania.</p>
        </div>
        
        <div class="feature-card p-6 text-center">
          <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-amber-50 flex items-center justify-center">
            <i class="icofont-check-circled text-2xl text-amber-600"></i>
          </div>
          <h3 class="text-xl font-bold text-gray-800 mb-2">Thamani Zetu</h3>
          <p class="text-black">Kuwapa wateja wetu urahisi, usalama na ufanisi katika shughuli zao za kila siku.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Features Section -->
  <section id="feature" class="py-20 bg-gradient-to-r from-amber-600 to-green-200 text-black">
    <div class="max-w-7xl mx-auto px-6">
      <div class="text-center space-y-4 mb-16">
        <h2 class="text-3xl md:text-4xl font-bold text-gray-800">Huduma <span class="text-gradient">Zetu</span></h2>
        <div class="section-divider w-24 mx-auto"></div>
        <p class="text-lg text-black max-w-2xl mx-auto">Vipengele vyetu vya kipekee vinavyowafanya wafanyabiashara kukua na kufanikiwa</p>
      </div>
      
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
        <div class="space-y-8">
          <div class="feature-card p-6 flex items-start gap-4">
            <div class="w-12 h-12 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
              <i class="icofont-company text-xl text-amber-600"></i>
            </div>
            <div>
              <h4 class="font-bold text-lg text-gray-800 mb-2">Uendeshaji wa Biashara</h4>
              <p class="text-gray-600">Simamia biashara zako zote kwa akaunti moja, popote ulipo. Fuatilia mauzo, gharama na faida kwa urahisi.</p>
            </div>
          </div>
          
          <div class="feature-card p-6 flex items-start gap-4">
            <div class="w-12 h-12 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0">
              <i class="icofont-computer text-xl text-dark-green"></i>
            </div>
            <div>
              <h4 class="font-bold text-lg text-gray-800 mb-2">Taarifa za Stoku</h4>
              <p class="text-gray-600">Fuatilia ukuaji na matumizi ya stoku zako kwa urahisi. Pata taarifa za wakati halisi kuhusu bidhaa zako.</p>
            </div>
          </div>
          
          <div class="feature-card p-6 flex items-start gap-4">
            <div class="w-12 h-12 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
              <i class="icofont-pie-chart text-xl text-amber-600"></i>
            </div>
            <div>
              <h4 class="font-bold text-lg text-gray-800 mb-2">Takwimu na Ripoti</h4>
              <p class="text-gray-600">Pata takwimu na ripoti za mauzo kwa grafu na chati. Chambua mienendo ya biashara yako kwa urahisi.</p>
            </div>
          </div>
          
          <div class="feature-card p-6 flex items-start gap-4">
            <div class="w-12 h-12 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0">
              <i class="icofont-ssl-security text-xl text-dark-green"></i>
            </div>
            <div>
              <h4 class="font-bold text-lg text-gray-800 mb-2">Usalama wa Juu</h4>
              <p class="text-gray-600">Taarifa zako zinalindwa dhidi ya vitisho vya kimtandao. Tunaweka usalama wako wa kifedha na biashara mbele.</p>
            </div>
          </div>
        </div>
        
        <div class="relative">
          <img src="https://test.mauzosheet.com/assets11092021/img/feature.png" alt="Vipengele vya MauzoSheet" class="rounded-2xl shadow-xl mx-auto" />
          <div class="absolute -bottom-6 -left-6 w-20 h-20 bg-amber-100 rounded-full opacity-70 z-0"></div>
          <div class="absolute -top-6 -right-6 w-16 h-16 bg-green-100 rounded-full opacity-70 z-0"></div>
        </div>
      </div>
    </div>
  </section>

  <!-- Pricing Section -->
  <section id="pricing" class="py-20 bg-gradient-to-r from-amber-600 to-green-200">
    <div class="max-w-5xl mx-auto px-6 text-center space-y-8">
      <div class="space-y-4">
        <h2 class="text-3xl md:text-4xl font-bold text-gray-800">Vifurushi <span class="text-gradient">Vya Bei</span></h2>
        <div class="section-divider w-24 mx-auto"></div>
        <p class="text-lg text-gray-900 max-w-2xl mx-auto">Chagua kifurushi kinachokufaa zaidi kwa mahitaji ya biashara yako</p>
      </div>
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-8">
        <!-- Basic Plan -->
        <div class="pricing-card p-8 text-center">
          <h3 class="text-2xl font-bold text-gray-800 mb-2">Kifurushi cha Msingi</h3>
          <div class="my-6">
            <span class="text-4xl font-bold text-amber-600">TZS 60,000</span>
            <span class="text-gray-600">/ miezi 6</span>
          </div>
          <ul class="space-y-4 mb-8 text-left">
            <li class="flex items-center">
              <i class="icofont-check text-green-500 mr-2"></i>
              <span>Uendeshaji wa biashara</span>
            </li>
            <li class="flex items-center">
              <i class="icofont-check text-green-500 mr-2"></i>
              <span>Usimamizi wa stoku</span>
            </li>
            <li class="flex items-center">
              <i class="icofont-check text-green-500 mr-2"></i>
              <span>Ripoti za msingi</span>
            </li>
            <li class="flex items-center">
              <i class="icofont-check text-green-500 mr-2"></i>
              <span>Msaada wa kiufundi</span>
            </li>
          </ul>
          <a href="{{ route('register') }}" class="btn-secondary py-3 px-8 rounded-lg font-semibold w-full block">Chagua Kifurushi</a>
        </div>
        
        <!-- Premium Plan -->
        <div class="pricing-card popular p-8 text-center">
          <div class="popular-badge">Inayopendwa</div>
          <h3 class="text-2xl font-bold text-gray-800 mb-2">Kifurushi cha Premium</h3>
          <div class="my-6">
            <span class="text-4xl font-bold text-amber-600">TZS 150,000</span>
            <span class="text-gray-600">/ mwaka 1</span>
          </div>
          <ul class="space-y-4 mb-8 text-left">
            <li class="flex items-center">
              <i class="icofont-check text-green-500 mr-2"></i>
              <span>Yote yaliyomo kwenye kifurushi cha msingi</span>
            </li>
            <li class="flex items-center">
              <i class="icofont-check text-green-500 mr-2"></i>
              <span>Ripoti za kina na uchambuzi</span>
            </li>
            <li class="flex items-center">
              <i class="icofont-check text-green-500 mr-2"></i>
              <span>Usaidizi wa moja kwa moja</span>
            </li>
            <li class="flex items-center">
              <i class="icofont-check text-green-500 mr-2"></i>
              <span>Matengenezo ya kipekee</span>
            </li>
            <li class="flex items-center">
              <i class="icofont-check text-green-500 mr-2"></i>
              <span>Vipengele vya hali ya juu</span>
            </li>
          </ul>
          <a href="{{ route('register') }}" class="btn-primary py-3 px-8 rounded-lg font-semibold w-full block">Chagua Kifurushi</a>
        </div>
      </div>
    </div>
  </section>

  <!-- Contact Section -->
  <section id="contact" class=" py-20 bg-gradient-to-r from-amber-600 to-green-200 text-black">
    <div class="max-w-5xl mx-auto px-6 text-center space-y-8">
      <div class="space-y-4">
        <h2 class="text-3xl md:text-4xl font-bold text-gray-800">Wasiliana <span class="text-gradient">Nasi</span></h2>
        <div class="section-divider w-24 mx-auto"></div>
        <p class="text-lg text-black max-w-2xl mx-auto">Tupigie simu, tutumie barua pepe au tuwasiliane kupitia WhatsApp</p>
      </div>
      
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8 pt-8">
        <div class="feature-card p-6 text-center">
          <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-amber-300 flex items-center justify-center">
            <i class="icofont-phone text-2xl text-amber-600"></i>
          </div>
          <h3 class="text-xl font-bold text-gray-800 mb-2">Piga Simu</h3>
          <p class="text-gray-600">+255 (0) 685 496 334</p>
          <p class="text-gray-600">+255 (0) 714 019 466</p>
          <a href="tel:+255713169114" class="inline-block mt-3 px-4 py-2 bg-amber-500 text-white rounded-lg font-medium hover:bg-amber-600 transition">Piga Sasa</a>
        </div>
        
<div class="feature-card p-6 text-center">
  <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-green-300 flex items-center justify-center">
    <i class="icofont-email text-2xl text-dark-green"></i>
  </div>

  <h3 class="text-xl font-bold text-gray-800 mb-2">Tuma Barua Pepe</h3>
  <p class="text-gray-600">mauzosheet9@gmail.com</p>

  <a href="mailto:mauzosheet9@gmail.com" 
     class="inline-block mt-3 px-4 py-2 bg-black text-white rounded-lg font-medium hover:bg-green-800 transition">
     Tuma Barua
  </a>
</div>

        
        <div class="feature-card p-6 text-center">
          <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-amber-200 flex items-center justify-center">
            <i class="icofont-brand-whatsapp text-2xl text-amber-600"></i>
          </div>
          <h3 class="text-xl font-bold text-gray-800 mb-2">WhatsApp</h3>
          <p class="text-gray-600">+255 (0) 685 496 334</p>
          <p class="text-gray-600">+255 (0) 714 019 466</p>
          <a href="https://wa.me/255685496334" target="_blank" class="inline-block mt-3 px-4 py-2 bg-green-500 text-white rounded-lg font-medium hover:bg-green-600 transition">Tuma Ujumbe</a>
        </div>
      </div>
      
      <div class="pt-8">
        <div class="feature-card p-6 text-center">
          <h3 class="text-xl font-bold text-gray-800 mb-4">Ofisi Zetu</h3>
          <p class="text-gray-600">Dar es Salaam, Tanzania</p>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA Section -->
  <section class="py-16 bg-gradient-to-r from-amber-600 to-green-200 text-black">
    <div class="max-w-4xl mx-auto px-6 text-center space-y-6">
      <h2 class="text-3xl md:text-4xl font-bold">Jiunge na Wafanyabiashara 500+ Leo</h2>
      <p class="text-lg max-w-2xl mx-auto">Anza kutumia MauzoSheet leo na ujionee jinsi biashara yako inavyoweza kukua kwa kasi na ufanisi zaidi.</p>
      <div class="flex flex-col sm:flex-row justify-center gap-4 pt-4">
        <a href="{{ route('register') }}" class="bg-white text-amber-600 py-3 px-8 rounded-lg font-semibold hover:bg-gray-100 transition">Anza Kujaribu</a>
        <a href="#contact" class="bg-transparent border-2 border-white py-3 px-8 rounded-lg font-semibold hover:bg-white/10 transition">Wasiliana Nasi</a>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-gray-900 text-gray-300 py-12">
    <div class="max-w-7xl mx-auto px-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        <div class="md:col-span-2">
          <a href="#home" class="flex items-center mb-4" aria-label="MauzoSheet Nyumbani">
            <img src="https://test.mauzosheet.com/assets/images/apple-icon.gif" alt="MauzoSheet logo" class="h-10 w-10 mr-2" />
            <span class="font-bold text-xl text-white">MauzoSheet</span>
          </a>
          <p class="mb-4 max-w-md">Mfumo wa kisasa wa kusimamia biashara zako kwa urahisi, usalama, na ufanisi.</p>
          <div class="flex space-x-4">
            <a href="#" class="text-gray-400 hover:text-white transition">
              <i class="icofont-facebook text-xl"></i>
            </a>
            <a href="#" class="text-gray-400 hover:text-white transition">
              <i class="icofont-twitter text-xl"></i>
            </a>
            <a href="#" class="text-gray-400 hover:text-white transition">
              <i class="icofont-instagram text-xl"></i>
            </a>
          </div>
        </div>
        
        <div>
          <h3 class="text-white font-semibold mb-4">Menyu</h3>
          <ul class="space-y-2">
            <li><a href="#home" class="hover:text-white transition">Nyumbani</a></li>
            <li><a href="#about" class="hover:text-white transition">Tufahamu</a></li>
            <li><a href="#feature" class="hover:text-white transition">Huduma Zetu</a></li>
            <li><a href="#pricing" class="hover:text-white transition">Vifurushi</a></li>
          </ul>
        </div>
        
        <div>
          <h3 class="text-white font-semibold mb-4">Msaada</h3>
          <ul class="space-y-2">
            <li><a href="#contact" class="hover:text-white transition">Mawasiliano</a></li>
            <li><a href="#" class="hover:text-white transition">Maswali Yanayoulizwa Mara Kwa Mara</a></li>
            <li><a href="#" class="hover:text-white transition">Maelekezo ya Matumizi</a></li>
            <li><a href="#" class="hover:text-white transition">Masharti ya Matumizi</a></li>
          </ul>
        </div>
      </div>
      
      <div class="border-t border-gray-800 mt-8 pt-8 text-center">
        <p>&copy; <span id="year"></span> MauzoSheet. Haki zote zimehifadhiwa.</p>
      </div>
    </div>
  </footer>

  <!-- Scripts -->
  <script>
    document.getElementById('mobile-menu-btn').addEventListener('click', () => {
      document.getElementById('mobile-nav').classList.toggle('hidden');
    });
    document.getElementById('year').textContent = new Date().getFullYear();
    
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const targetId = this.getAttribute('href');
        if (targetId === '#') return;
        
        const targetElement = document.querySelector(targetId);
        if (targetElement) {
          window.scrollTo({
            top: targetElement.offsetTop - 80,
            behavior: 'smooth'
          });
          
          // Close mobile menu if open
          document.getElementById('mobile-nav').classList.add('hidden');
        }
      });
    });
  </script>
</body>
</html>