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
        radial-gradient(circle at 20% 80%, rgba(217, 119, 6, 0.03) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(6, 95, 70, 0.03) 0%, transparent 50%),
        radial-gradient(circle at 40% 40%, rgba(255, 255, 255, 0.05) 0%, transparent 50%);
      z-index: -1;
    }

    .hero-gradient {
      background: linear-gradient(135deg, rgba(217, 119, 6, 0.1) 0%, rgba(6, 95, 70, 0.1) 100%);
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
      border: none;
      font-weight: 600;
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 16px rgba(217, 119, 6, 0.25);
    }

    .btn-secondary {
      background: linear-gradient(90deg, var(--dark-green), var(--dark-green-light));
      color: white;
      transition: all 0.3s ease;
      border: none;
      font-weight: 600;
    }

    .btn-secondary:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 16px rgba(6, 95, 70, 0.25);
    }

    .feature-card {
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
      transition: all 0.3s ease;
      border: 1px solid rgba(0, 0, 0, 0.05);
      height: 100%;
    }

    .feature-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
    }

    .nav-link {
      position: relative;
      color: #374151;
      font-weight: 500;
      padding: 4px 0;
    }

    .nav-link::after {
      content: '';
      position: absolute;
      width: 0;
      height: 2px;
      bottom: 0;
      left: 0;
      background: linear-gradient(90deg, var(--amber), var(--dark-green));
      transition: width 0.3s ease;
    }

    .nav-link:hover::after {
      width: 100%;
    }

    .nav-link:hover {
      color: var(--amber);
    }

    .section-divider {
      height: 1px;
      background: linear-gradient(90deg, transparent, rgba(217, 119, 6, 0.2), transparent);
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
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
      transition: all 0.3s ease;
      overflow: hidden;
      height: 100%;
    }
    
    .pricing-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 16px 32px rgba(0, 0, 0, 0.12);
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
      padding: 6px 20px;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 600;
      z-index: 10;
    }
    
    .section-header {
      position: relative;
      display: inline-block;
      margin-bottom: 2rem;
    }
    
    .section-header::after {
      content: '';
      position: absolute;
      width: 60%;
      height: 4px;
      bottom: -8px;
      left: 20%;
      background: linear-gradient(90deg, var(--amber), var(--dark-green));
      border-radius: 2px;
    }
    
    /* Image container for responsive sizing */
    .responsive-img {
      width: 100%;
      height: auto;
      max-width: 100%;
      border-radius: 12px;
    }
    
    .hero-img-container {
      max-width: 100%;
      margin: 0 auto;
    }
    
    @media (min-width: 768px) {
      .hero-img-container {
        max-width: 600px;
      }
    }
    
    @media (min-width: 1024px) {
      .hero-img-container {
        max-width: 700px;
      }
    }
    
    /* Mobile optimization */
    .mobile-stats {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 0.75rem;
      max-width: 400px;
      margin: 0 auto;
    }
    
    .mobile-stats-item {
      text-align: center;
      padding: 0.5rem;
    }
    
    /* Pricing grid improvements */
    .pricing-grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 1.5rem;
      max-width: 1200px;
      margin: 0 auto;
    }
    
    @media (min-width: 768px) {
      .pricing-grid {
        grid-template-columns: repeat(2, 1fr);
      }
    }
    
    @media (min-width: 1024px) {
      .pricing-grid {
        grid-template-columns: repeat(3, 1fr);
      }
    }
    
    /* FIXED: Mobile menu dropdown */
    #mobile-nav {
      transition: all 0.3s ease;
      max-height: 0;
      overflow: hidden;
      opacity: 0;
    }
    
    #mobile-nav.active {
      max-height: 500px;
      opacity: 1;
      padding: 1rem 0;
    }
    
    /* UPDATED: Hero buttons layout - Pakua & Ingia on first line, Jisajili on second line on mobile */
    .hero-buttons {
      display: flex;
      flex-direction: row;
      flex-wrap: wrap;
      gap: 0.75rem;
      justify-content: center;
      align-items: center;
    }
    
    /* On mobile: First line - Pakua Programu & Ingia */
    /* On mobile: Second line - Jisajili (full width) */
    @media (max-width: 767px) {
      .hero-buttons {
        flex-direction: column;
        align-items: stretch;
        max-width: 320px;
        margin-left: auto;
        margin-right: auto;
      }
      
      .hero-buttons .button-row {
        display: flex;
        flex-direction: row;
        gap: 0.75rem;
        width: 100%;
      }
      
      .hero-buttons .button-row a {
        flex: 1;
        min-width: 0;
      }
      
      .hero-buttons .jisajili-btn {
        width: 100%;
        margin-top: 0.5rem;
      }
    }
    
    @media (min-width: 768px) {
      .hero-buttons {
        flex-direction: row;
        flex-wrap: nowrap;
      }
      
      .hero-buttons .button-row {
        display: flex;
        flex-direction: row;
        gap: 0.75rem;
      }
      
      .hero-buttons .jisajili-btn {
        margin-left: 0.75rem;
      }
    }
    
    /* Section background colors */
    .section-about {
      background: linear-gradient(135deg, rgba(217, 119, 6, 0.05) 0%, rgba(6, 95, 70, 0.05) 100%);
    }
    
    .section-features {
      background: linear-gradient(135deg, rgba(217, 119, 6, 0.03) 0%, rgba(6, 95, 70, 0.03) 100%);
    }
    
    .section-pricing {
      background: linear-gradient(135deg, rgba(217, 119, 6, 0.05) 0%, rgba(6, 95, 70, 0.05) 100%);
    }
    
    .section-contact {
      background: linear-gradient(135deg, rgba(217, 119, 6, 0.03) 0%, rgba(6, 95, 70, 0.03) 100%);
    }
    
    .section-cta {
      background: linear-gradient(135deg, rgba(217, 119, 6, 0.08) 0%, rgba(6, 95, 70, 0.08) 100%);
    }
    
    /* Better touch targets for mobile */
    .touch-target {
      min-height: 44px;
      min-width: 44px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    /* Portrait phone specific styles */
    @media (max-width: 480px) and (orientation: portrait) {
      .hero-buttons {
        max-width: 280px;
      }
      
      .hero-buttons .button-row {
        gap: 0.5rem;
      }
      
      .hero-buttons .button-row a,
      .hero-buttons .jisajili-btn {
        font-size: 0.85rem;
        padding: 0.75rem 0.5rem;
      }
      
      /* Mobile menu fix for portrait */
      #mobile-nav.active {
        padding: 1rem;
      }
      
      #mobile-nav a {
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
      }
    }
  </style>
</head>

<body class="relative min-h-screen">

  <!-- Header - Fixed -->
  <header class="sticky top-0 z-50 bg-white shadow-sm">
    <div class="max-w-7xl mx-auto flex justify-between items-center py-3 px-4 sm:py-4 sm:px-6">
      
      <!-- Logo -->
      <a href="#home" class="flex items-center space-x-2 shrink-0" aria-label="MauzoSheet Nyumbani">
        <img src="https://test.mauzosheet.com/assets/images/apple-icon.gif" 
             alt="MauzoSheet logo" 
             class="h-8 w-8 sm:h-9 sm:w-9 md:h-10 md:w-10 object-contain" />
        <span class="font-bold text-lg sm:text-xl md:text-2xl text-gradient whitespace-nowrap">
          Mauzosheetai
        </span>
      </a>

      <!-- Desktop Navigation -->
      <nav class="hidden md:flex items-center space-x-6 lg:space-x-8 text-gray-700">
        <a class="nav-link hover:text-amber-600 transition" href="#home">Nyumbani</a>
        <a class="nav-link hover:text-amber-600 transition" href="#about">Tufahamu</a>
        <a class="nav-link hover:text-amber-600 transition" href="#feature">Huduma</a>
        <a class="nav-link hover:text-amber-600 transition" href="#pricing">Vifurushi</a>
        <a class="nav-link hover:text-amber-600 transition" href="#testimonials">Shuhuda</a>
        <a class="nav-link hover:text-amber-600 transition" href="#contact">Mawasiliano</a>
      </nav>

      <!-- Mobile Menu Button -->
      <button id="mobile-menu-btn" 
              class="md:hidden text-gray-700 focus:outline-none touch-target p-2" 
              aria-label="Fungua menyu ya simu"
              aria-expanded="false">
        <svg id="menu-icon" xmlns="http://www.w3.org/2000/svg" 
             class="h-6 w-6" 
             fill="none"
             viewBox="0 0 24 24" 
             stroke="currentColor">
          <path stroke-linecap="round" 
                stroke-linejoin="round" 
                stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16" />
        </svg>
        <svg id="close-icon" xmlns="http://www.w3.org/2000/svg" 
             class="h-6 w-6 hidden" 
             fill="none"
             viewBox="0 0 24 24" 
             stroke="currentColor">
          <path stroke-linecap="round" 
                stroke-linejoin="round" 
                stroke-width="2"
                d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>

    <!-- Mobile Navigation - FIXED: Now shows properly on tap -->
    <nav id="mobile-nav" class="md:hidden bg-white border-t shadow-lg">
      <div class="flex flex-col space-y-1 px-4">
        <a href="#home" class="nav-link py-3 px-4 rounded-lg hover:bg-amber-50 transition touch-target">Nyumbani</a>
        <a href="#about" class="nav-link py-3 px-4 rounded-lg hover:bg-amber-50 transition touch-target">Tufahamu</a>
        <a href="#feature" class="nav-link py-3 px-4 rounded-lg hover:bg-amber-50 transition touch-target">Huduma Zetu</a>
        <a href="#pricing" class="nav-link py-3 px-4 rounded-lg hover:bg-amber-50 transition touch-target">Vifurushi</a>
        <a href="#testimonials" class="nav-link py-3 px-4 rounded-lg hover:bg-amber-50 transition touch-target">Shuhuda</a>
        <a href="#contact" class="nav-link py-3 px-4 rounded-lg hover:bg-amber-50 transition touch-target">Mawasiliano</a>
      </div>
    </nav>
  </header>

  <!-- Hero Section -->
  <section id="home" class="hero-gradient pt-10 pb-16 md:pt-16 md:pb-24 lg:pt-20 lg:pb-32">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 flex flex-col lg:flex-row items-center gap-8 lg:gap-12">
      
      <!-- Text Content -->
      <div class="lg:w-1/2 space-y-4 md:space-y-6 text-center lg:text-left">
        <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold leading-tight">
          <span class="text-gradient">Biashara Yako,</span><br>
          <span class="text-gray-800">Imerahisishwa</span>
        </h1>
        <p class="text-base sm:text-lg md:text-xl text-gray-700 max-w-lg mx-auto lg:mx-0">
          MauzoSheet hukusaidia kusimamia, kuchambua, na kuendesha biashara zako kwa urahisi, usalama, na ufanisi.
        </p>
        
        <!-- UPDATED: CTA Buttons - Pakua & Ingia on first line, Jisajili on second line on mobile -->
        <div class="hero-buttons mt-6 md:mt-8 justify-center lg:justify-start">
          <!-- First line: Pakua Programu & Ingia -->
          <div class="button-row">
            <a href="https://play.google.com/store/apps/details?id=io.android.MauzoSheet" 
               target="_blank" rel="noopener noreferrer"
               class="btn-primary py-3 px-4 sm:px-5 rounded-lg font-semibold text-center touch-target whitespace-nowrap">
              Pakua Programu
            </a>
            <a href="{{ route('login') }}" 
               class="btn-secondary py-3 px-4 sm:px-5 rounded-lg font-semibold text-center touch-target whitespace-nowrap">
              Ingia
            </a>
          </div>
          
          <!-- Second line: Jisajili (full width on mobile) -->
          <a href="{{ route('register') }}" 
             class="jisajili-btn btn-primary py-3 px-4 sm:px-5 rounded-lg font-semibold text-center touch-target whitespace-nowrap">
            Jisajili
          </a>
        </div>
        
        <!-- Stats -->
        <div class="pt-6 md:pt-8">
          <div class="mobile-stats mx-auto lg:mx-0">
            <div class="mobile-stats-item">
              <p class="text-xl sm:text-2xl md:text-3xl font-bold text-amber-600">500+</p>
              <p class="text-xs sm:text-sm text-gray-600">Wafanyabiashara</p>
            </div>
            <div class="mobile-stats-item">
              <p class="text-xl sm:text-2xl md:text-3xl font-bold text-dark-green">10K+</p>
              <p class="text-xs sm:text-sm text-gray-600">Mauzo Kila Mwezi</p>
            </div>
            <div class="mobile-stats-item">
              <p class="text-xl sm:text-2xl md:text-3xl font-bold text-amber-600">98%</p>
              <p class="text-xs sm:text-sm text-gray-600">Wateja Wameridhika</p>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Hero Image -->
      <div class="lg:w-1/2 mt-8 lg:mt-0">
        <div class="hero-img-container">
          <img src="p1.jpg" 
               alt="Mfumo wa MauzoSheet" 
               class="responsive-img shadow-xl floating" />
        </div>
      </div>
    </div>
  </section>

  <!-- About Section -->
  <section id="about" class="py-12 md:py-20 section-about">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
      <div class="text-center space-y-4 mb-8 md:mb-12">
        <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-800">
          <span class="section-header">Kuhusu MauzoSheet</span>
        </h2>
        <div class="section-divider w-24 mx-auto"></div>
        <p class="text-base md:text-lg text-gray-600 max-w-3xl mx-auto">
          MauzoSheet ni mfumo wa kimtandao unaosaidia wafanyabiashara wa rejareja na jumla kusimamia biashara zao kwa njia ya kisasa, salama, na yenye ufanisi.
        </p>
        <p class="text-base md:text-lg font-medium text-dark-green max-w-3xl mx-auto">
          Uza au kopesha bidhaa zako kwa urahisi kupitia MauzoSheet. Jiunge leo!
        </p>
      </div>
      
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
        <div class="feature-card p-5 md:p-6 text-center">
          <div class="w-14 h-14 md:w-16 md:h-16 mx-auto mb-4 rounded-full bg-amber-50 flex items-center justify-center">
            <i class="icofont-target text-xl md:text-2xl text-amber-600"></i>
          </div>
          <h3 class="text-lg md:text-xl font-bold text-gray-800 mb-2">Lengo Letu</h3>
          <p class="text-gray-600 text-sm md:text-base">Kurahisisha uendeshaji wa biashara kwa wajasiriamali wote Tanzania.</p>
        </div>
        
        <div class="feature-card p-5 md:p-6 text-center">
          <div class="w-14 h-14 md:w-16 md:h-16 mx-auto mb-4 rounded-full bg-green-50 flex items-center justify-center">
            <i class="icofont-eye text-xl md:text-2xl text-dark-green"></i>
          </div>
          <h3 class="text-lg md:text-xl font-bold text-gray-800 mb-2">Mtazamo Wetu</h3>
          <p class="text-gray-600 text-sm md:text-base">Kuwa chombo kikuu cha uongozi wa kidijitali kwa wafanyabiashara Tanzania.</p>
        </div>
        
        <div class="feature-card p-5 md:p-6 text-center">
          <div class="w-14 h-14 md:w-16 md:h-16 mx-auto mb-4 rounded-full bg-amber-50 flex items-center justify-center">
            <i class="icofont-check-circled text-xl md:text-2xl text-amber-600"></i>
          </div>
          <h3 class="text-lg md:text-xl font-bold text-gray-800 mb-2">Thamani Zetu</h3>
          <p class="text-gray-600 text-sm md:text-base">Kuwapa wateja wetu urahisi, usalama na ufanisi katika shughuli zao za kila siku.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Features Section -->
  <section id="feature" class="py-12 md:py-20 section-features">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
      <div class="text-center space-y-4 mb-8 md:mb-16">
        <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-800">
          <span class="section-header">Huduma Zetu</span>
        </h2>
        <div class="section-divider w-24 mx-auto"></div>
        <p class="text-base md:text-lg text-gray-600 max-w-2xl mx-auto">Vipengele vyetu vya kipekee vinavyowafanya wafanyabiashara kukua na kufanikiwa</p>
      </div>
      
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 md:gap-12 items-center">
        <div class="space-y-6 md:space-y-8">
          <div class="feature-card p-4 sm:p-5 md:p-6 flex items-start gap-3 sm:gap-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
              <i class="icofont-company text-lg sm:text-xl text-amber-600"></i>
            </div>
            <div>
              <h4 class="font-bold text-base sm:text-lg text-gray-800 mb-1 sm:mb-2">Uendeshaji wa Biashara</h4>
              <p class="text-gray-600 text-sm sm:text-base">Simamia biashara zako zote kwa akaunti moja, popote ulipo. Fuatilia mauzo, gharama na faida kwa urahisi.</p>
            </div>
          </div>
          
          <div class="feature-card p-4 sm:p-5 md:p-6 flex items-start gap-3 sm:gap-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0">
              <i class="icofont-computer text-lg sm:text-xl text-dark-green"></i>
            </div>
            <div>
              <h4 class="font-bold text-base sm:text-lg text-gray-800 mb-1 sm:mb-2">Taarifa za Stoku</h4>
              <p class="text-gray-600 text-sm sm:text-base">Fuatilia ukuaji na matumizi ya stoku zako kwa urahisi. Pata taarifa za wakati halisi kuhusu bidhaa zako.</p>
            </div>
          </div>
          
          <div class="feature-card p-4 sm:p-5 md:p-6 flex items-start gap-3 sm:gap-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
              <i class="icofont-pie-chart text-lg sm:text-xl text-amber-600"></i>
            </div>
            <div>
              <h4 class="font-bold text-base sm:text-lg text-gray-800 mb-1 sm:mb-2">Takwimu na Ripoti</h4>
              <p class="text-gray-600 text-sm sm:text-base">Pata takwimu na ripoti za mauzo kwa grafu na chati. Chambua mienendo ya biashara yako kwa urahisi.</p>
            </div>
          </div>
          
          <div class="feature-card p-4 sm:p-5 md:p-6 flex items-start gap-3 sm:gap-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0">
              <i class="icofont-ssl-security text-lg sm:text-xl text-dark-green"></i>
            </div>
            <div>
              <h4 class="font-bold text-base sm:text-lg text-gray-800 mb-1 sm:mb-2">Usalama wa Juu</h4>
              <p class="text-gray-600 text-sm sm:text-base">Taarifa zako zinalindwa dhidi ya vitisho vya kimtandao. Tunaweka usalama wako wa kifedha na biashara mbele.</p>
            </div>
          </div>
        </div>
        
        <div class="max-w-sm mx-auto lg:max-w-lg">
          <img src="https://test.mauzosheet.com/assets11092021/img/feature.png"
               alt="Vipengele vya MauzoSheet"
               class="w-full h-auto shadow-lg" />
        </div>
      </div>
    </div>
  </section>

  <!-- Pricing Section -->
  <section id="pricing" class="py-12 md:py-20 section-pricing">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
      <div class="text-center space-y-4 mb-8 md:mb-12">
        <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-800">
          <span class="section-header">Vifurushi Vya Bei</span>
        </h2>
        <div class="section-divider w-24 mx-auto"></div>
        <p class="text-base md:text-lg text-gray-600 max-w-2xl mx-auto">Chagua kifurushi kinachokufaa zaidi kwa mahitaji ya biashara yako</p>
      </div>
      
      <!-- CORRECTED PRICING: 15,000/month, 75,000/5 months, 150,000/year -->
      <div class="pricing-grid">
        <!-- Monthly Plan -->
        <div class="pricing-card p-5 md:p-6 lg:p-8 text-center flex flex-col">
          <h3 class="text-lg md:text-xl lg:text-2xl font-bold text-gray-800 mb-2">Mwezi Mmoja</h3>
          <div class="my-4 md:my-6">
            <span class="text-2xl sm:text-3xl md:text-4xl font-bold text-amber-600">TZS 15,000</span>
            <span class="text-gray-600 block mt-1 md:mt-2 text-sm md:text-base">/ mwezi</span>
          </div>
          <ul class="space-y-2 md:space-y-3 mb-6 md:mb-8 text-left flex-grow">
            <li class="flex items-start">
              <i class="icofont-check text-green-500 mr-2 mt-1 flex-shrink-0"></i>
              <span class="text-sm md:text-base">Uendeshaji wa biashara</span>
            </li>
            <li class="flex items-start">
              <i class="icofont-check text-green-500 mr-2 mt-1 flex-shrink-0"></i>
              <span class="text-sm md:text-base">Usimamizi wa stoku</span>
            </li>
            <li class="flex items-start">
              <i class="icofont-check text-green-500 mr-2 mt-1 flex-shrink-0"></i>
              <span class="text-sm md:text-base">Ripoti za msingi</span>
            </li>
            <li class="flex items-start">
              <i class="icofont-check text-green-500 mr-2 mt-1 flex-shrink-0"></i>
              <span class="text-sm md:text-base">Msaada wa kiufundi</span>
            </li>
          </ul>
          <a href="{{ route('register') }}" class="btn-secondary py-3 px-4 md:px-6 rounded-lg font-semibold w-full block text-sm md:text-base touch-target">
            Chagua Kifurushi
          </a>
        </div>
        
        <!-- 5-Month Plan -->
        <div class="pricing-card popular p-5 md:p-6 lg:p-8 text-center flex flex-col">
          <div class="popular-badge">Inayopendwa</div>
          <h3 class="text-lg md:text-xl lg:text-2xl font-bold text-gray-800 mb-2">Miezi 5</h3>
          <div class="my-4 md:my-6">
            <span class="text-2xl sm:text-3xl md:text-4xl font-bold text-amber-600">TZS 75,000</span>
            <span class="text-gray-600 block mt-1 md:mt-2 text-sm md:text-base">/ miezi 5</span>
            <p class="text-xs sm:text-sm text-green-600 font-medium mt-1">Akiba ya 25,000</p>
          </div>
          <ul class="space-y-2 md:space-y-3 mb-6 md:mb-8 text-left flex-grow">
            <li class="flex items-start">
              <i class="icofont-check text-green-500 mr-2 mt-1 flex-shrink-0"></i>
              <span class="text-sm md:text-base">Yote yaliyomo kwenye mwezi mmoja</span>
            </li>
            <li class="flex items-start">
              <i class="icofont-check text-green-500 mr-2 mt-1 flex-shrink-0"></i>
              <span class="text-sm md:text-base">Ripoti za kina zaidi</span>
            </li>
            <li class="flex items-start">
              <i class="icofont-check text-green-500 mr-2 mt-1 flex-shrink-0"></i>
              <span class="text-sm md:text-base">Usaidizi wa haraka</span>
            </li>
            <li class="flex items-start">
              <i class="icofont-check text-green-500 mr-2 mt-1 flex-shrink-0"></i>
              <span class="text-sm md:text-base">Matengenezo ya kipekee</span>
            </li>
          </ul>
          <a href="{{ route('register') }}" class="btn-primary py-3 px-4 md:px-6 rounded-lg font-semibold w-full block text-sm md:text-base touch-target">
            Chagua Kifurushi
          </a>
        </div>
        
        <!-- 1-Year Plan -->
        <div class="pricing-card p-5 md:p-6 lg:p-8 text-center flex flex-col">
          <h3 class="text-lg md:text-xl lg:text-2xl font-bold text-gray-800 mb-2">Mwaka 1</h3>
          <div class="my-4 md:my-6">
            <span class="text-2xl sm:text-3xl md:text-4xl font-bold text-amber-600">TZS 150,000</span>
            <span class="text-gray-600 block mt-1 md:mt-2 text-sm md:text-base">/ mwaka 1</span>
            <p class="text-xs sm:text-sm text-green-600 font-medium mt-1">Akiba ya 30,000</p>
          </div>
          <ul class="space-y-2 md:space-y-3 mb-6 md:mb-8 text-left flex-grow">
            <li class="flex items-start">
              <i class="icofont-check text-green-500 mr-2 mt-1 flex-shrink-0"></i>
              <span class="text-sm md:text-base">Yote yaliyomo kwenye miezi 5</span>
            </li>
            <li class="flex items-start">
              <i class="icofont-check text-green-500 mr-2 mt-1 flex-shrink-0"></i>
              <span class="text-sm md:text-base">Ripoti za kina na uchambuzi</span>
            </li>
            <li class="flex items-start">
              <i class="icofont-check text-green-500 mr-2 mt-1 flex-shrink-0"></i>
              <span class="text-sm md:text-base">Usaidizi wa moja kwa moja 24/7</span>
            </li>
            <li class="flex items-start">
              <i class="icofont-check text-green-500 mr-2 mt-1 flex-shrink-0"></i>
              <span class="text-sm md:text-base">Vipengele vya hali ya juu</span>
            </li>
            <li class="flex items-start">
              <i class="icofont-check text-green-500 mr-2 mt-1 flex-shrink-0"></i>
              <span class="text-sm md:text-base">Masomo ya biashara</span>
            </li>
          </ul>
          <a href="{{ route('register') }}" class="btn-secondary py-3 px-4 md:px-6 rounded-lg font-semibold w-full block text-sm md:text-base touch-target">
            Chagua Kifurushi
          </a>
        </div>
      </div>
      
      <div class="text-center mt-6 md:mt-8">
        <p class="text-gray-600 text-sm md:text-base">Bei zote zimejumlisha kodi. Unaweza kubadilisha kifurushi wakati wowote.</p>
      </div>
    </div>
  </section>

  <!-- Contact Section -->
  <section id="contact" class="py-12 md:py-20 section-contact">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
      <div class="text-center space-y-4 mb-8 md:mb-12">
        <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-800">
          <span class="section-header">Wasiliana Nasi</span>
        </h2>
        <div class="section-divider w-24 mx-auto"></div>
        <p class="text-base md:text-lg text-gray-600 max-w-2xl mx-auto">Tupigie simu, tutumie barua pepe au tuwasiliane kupitia WhatsApp</p>
      </div>
      
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
        <div class="feature-card p-5 md:p-6 text-center">
          <div class="w-14 h-14 md:w-16 md:h-16 mx-auto mb-4 rounded-full bg-amber-50 flex items-center justify-center">
            <i class="icofont-phone text-xl md:text-2xl text-amber-600"></i>
          </div>
          <h3 class="text-lg md:text-xl font-bold text-gray-800 mb-2">Piga Simu</h3>
          <p class="text-gray-600 text-sm md:text-base">+255 (0) 685 496 334</p>
          <p class="text-gray-600 text-sm md:text-base">+255 (0) 714 019 466</p>
          <a href="tel:+255685496334" class="inline-block mt-3 px-4 py-2 btn-primary rounded-lg font-medium w-full text-sm md:text-base touch-target">
            Piga Sasa
          </a>
        </div>
        
        <div class="feature-card p-5 md:p-6 text-center">
          <div class="w-14 h-14 md:w-16 md:h-16 mx-auto mb-4 rounded-full bg-green-50 flex items-center justify-center">
            <i class="icofont-email text-xl md:text-2xl text-dark-green"></i>
          </div>
          <h3 class="text-lg md:text-xl font-bold text-gray-800 mb-2">Tuma Barua Pepe</h3>
          <p class="text-gray-600 text-sm md:text-base">mauzosheet9@gmail.com</p>
          <a href="mailto:mauzosheet9@gmail.com" 
             class="inline-block mt-3 px-4 py-2 btn-secondary rounded-lg font-medium w-full text-sm md:text-base touch-target">
             Tuma Barua
          </a>
        </div>
        
        <div class="feature-card p-5 md:p-6 text-center">
          <div class="w-14 h-14 md:w-16 md:h-16 mx-auto mb-4 rounded-full bg-amber-50 flex items-center justify-center">
            <i class="icofont-brand-whatsapp text-xl md:text-2xl text-amber-600"></i>
          </div>
          <h3 class="text-lg md:text-xl font-bold text-gray-800 mb-2">WhatsApp</h3>
          <p class="text-gray-600 text-sm md:text-base">+255 (0) 685 496 334</p>
          <p class="text-gray-600 text-sm md:text-base">+255 (0) 714 019 466</p>
          <a href="https://wa.me/255685496334" target="_blank" 
             class="inline-block mt-3 px-4 py-2 bg-green-500 text-white rounded-lg font-medium hover:bg-green-600 transition w-full text-sm md:text-base touch-target">
             Tuma Ujumbe
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA Section -->
  <section class="py-12 md:py-20 section-cta bg-gray-50">
    <!-- Feature Card -->
    <div class="max-w-2xl mx-auto mb-12">
      <div class="feature-card p-6 md:p-8 text-center bg-white shadow-md rounded-xl">
        <h3 class="text-lg md:text-xl font-bold text-gray-800 mb-2">Ofisi Zetu</h3>
        <p class="text-gray-600 text-sm md:text-base">Dar es Salaam, Tanzania</p>
      </div>
    </div>

    <!-- Main CTA Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 text-center space-y-6 md:space-y-8">
      <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-800">
        Jiunge na Wafanyabiashara 500+ Leo
      </h2>
      <p class="text-base md:text-lg text-gray-600 max-w-2xl mx-auto">
        Anza kutumia MauzoSheet leo na ujionee jinsi biashara yako inavyoweza kukua kwa kasi na ufanisi zaidi.
      </p>

      <!-- Buttons -->
      <div class="flex flex-col sm:flex-row justify-center gap-4 md:gap-6 pt-4">
        <a href="{{ route('register') }}" 
           class="bg-amber-600 text-white py-3 px-6 md:px-8 rounded-lg font-semibold hover:bg-amber-700 transition shadow-md touch-target">
          Anza Kujaribu
        </a>
        <a href="#contact" 
           class="bg-white border-2 border-amber-600 text-amber-600 py-3 px-6 md:px-8 rounded-lg font-semibold hover:bg-amber-50 transition shadow-md touch-target">
          Wasiliana Nasi
        </a>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-gray-900 text-gray-300 py-8 md:py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 md:gap-8">
        <div class="md:col-span-2">
          <a href="#home" class="flex items-center mb-3 md:mb-4" aria-label="MauzoSheet Nyumbani">
            <img src="https://test.mauzosheet.com/assets/images/apple-icon.gif" alt="MauzoSheet logo" class="h-8 w-8 md:h-10 md:w-10 mr-2" />
            <span class="font-bold text-lg md:text-xl text-white">Mauzo<span class="text-gradient">Sheet</span></span>
          </a>
          <p class="mb-3 md:mb-4 max-w-md text-sm md:text-base">
            Mfumo wa kisasa wa kusimamia biashara zako kwa urahisi, usalama, na ufanisi.
          </p>
          <div class="flex space-x-3 md:space-x-4">
            <a href="#" class="text-gray-400 hover:text-white transition touch-target p-1">
              <i class="icofont-facebook text-lg md:text-xl"></i>
            </a>
            <a href="#" class="text-gray-400 hover:text-white transition touch-target p-1">
              <i class="icofont-twitter text-lg md:text-xl"></i>
            </a>
            <a href="#" class="text-gray-400 hover:text-white transition touch-target p-1">
              <i class="icofont-instagram text-lg md:text-xl"></i>
            </a>
          </div>
        </div>
        
        <div>
          <h3 class="text-white font-semibold mb-3 md:mb-4 text-base md:text-lg">Menyu</h3>
          <ul class="space-y-2">
            <li><a href="#home" class="hover:text-white transition text-sm md:text-base touch-target py-1 block">Nyumbani</a></li>
            <li><a href="#about" class="hover:text-white transition text-sm md:text-base touch-target py-1 block">Tufahamu</a></li>
            <li><a href="#feature" class="hover:text-white transition text-sm md:text-base touch-target py-1 block">Huduma Zetu</a></li>
            <li><a href="#pricing" class="hover:text-white transition text-sm md:text-base touch-target py-1 block">Vifurushi</a></li>
          </ul>
        </div>
        
        <div>
          <h3 class="text-white font-semibold mb-3 md:mb-4 text-base md:text-lg">Msaada</h3>
          <ul class="space-y-2">
            <li><a href="#contact" class="hover:text-white transition text-sm md:text-base touch-target py-1 block">Mawasiliano</a></li>
            <li><a href="#" class="hover:text-white transition text-sm md:text-base touch-target py-1 block">Maswali Yanayoulizwa</a></li>
            <li><a href="#" class="hover:text-white transition text-sm md:text-base touch-target py-1 block">Maelekezo ya Matumizi</a></li>
            <li><a href="#" class="hover:text-white transition text-sm md:text-base touch-target py-1 block">Masharti ya Matumizi</a></li>
          </ul>
        </div>
      </div>
      
      <div class="border-t border-gray-800 mt-6 md:mt-8 pt-6 md:pt-8 text-center">
        <p class="text-sm md:text-base">&copy; <span id="year"></span> MauzoSheet. Haki zote zimehifadhiwa.</p>
      </div>
    </div>
  </footer>

  <!-- Scripts -->
  <script>
    // Mobile menu toggle with animation - FIXED
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileNav = document.getElementById('mobile-nav');
    const menuIcon = document.getElementById('menu-icon');
    const closeIcon = document.getElementById('close-icon');
    
    mobileMenuBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      const isExpanded = mobileMenuBtn.getAttribute('aria-expanded') === 'true';
      
      if (isExpanded) {
        // Close menu
        mobileNav.classList.remove('active');
        setTimeout(() => {
          mobileNav.classList.add('hidden');
        }, 300);
        menuIcon.classList.remove('hidden');
        closeIcon.classList.add('hidden');
        mobileMenuBtn.setAttribute('aria-expanded', 'false');
      } else {
        // Open menu
        mobileNav.classList.remove('hidden');
        setTimeout(() => {
          mobileNav.classList.add('active');
        }, 10);
        menuIcon.classList.add('hidden');
        closeIcon.classList.remove('hidden');
        mobileMenuBtn.setAttribute('aria-expanded', 'true');
      }
    });
    
    // Close menu when clicking on mobile nav links
    document.querySelectorAll('#mobile-nav a').forEach(link => {
      link.addEventListener('click', () => {
        mobileNav.classList.remove('active');
        setTimeout(() => {
          mobileNav.classList.add('hidden');
        }, 300);
        menuIcon.classList.remove('hidden');
        closeIcon.classList.add('hidden');
        mobileMenuBtn.setAttribute('aria-expanded', 'false');
      });
    });
    
    // Set current year in footer
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
          if (mobileNav.classList.contains('active')) {
            mobileNav.classList.remove('active');
            setTimeout(() => {
              mobileNav.classList.add('hidden');
            }, 300);
            menuIcon.classList.remove('hidden');
            closeIcon.classList.add('hidden');
            mobileMenuBtn.setAttribute('aria-expanded', 'false');
          }
        }
      });
    });
    
    // Close mobile menu when clicking outside on mobile
    document.addEventListener('click', (e) => {
      if (window.innerWidth < 768 && 
          mobileNav.classList.contains('active') && 
          !mobileNav.contains(e.target) && 
          !mobileMenuBtn.contains(e.target)) {
        mobileNav.classList.remove('active');
        setTimeout(() => {
          mobileNav.classList.add('hidden');
        }, 300);
        menuIcon.classList.remove('hidden');
        closeIcon.classList.add('hidden');
        mobileMenuBtn.setAttribute('aria-expanded', 'false');
      }
    });
    
    // Handle window resize
    window.addEventListener('resize', () => {
      if (window.innerWidth >= 768 && mobileNav.classList.contains('active')) {
        mobileNav.classList.remove('active');
        mobileNav.classList.add('hidden');
        menuIcon.classList.remove('hidden');
        closeIcon.classList.add('hidden');
        mobileMenuBtn.setAttribute('aria-expanded', 'false');
      }
    });
  </script>
</body>
</html>