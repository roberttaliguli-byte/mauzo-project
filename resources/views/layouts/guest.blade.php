<!doctype html>
<html lang="sw">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>@yield('title', 'MauzoSheet')</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    body { 
      font-family: 'Inter', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial; 
      background: linear-gradient(135deg, #b45309, #d97706, #1a1a1a); /* deep/dark amber to subtle black */
      background-size: 400% 400%;
      animation: gradientBG 20s ease infinite;
      color: #fff;
    }

    @keyframes gradientBG {
      0% { background-position: 0% 50%; }
      25% { background-position: 50% 50%; }
      50% { background-position: 100% 50%; }
      75% { background-position: 50% 50%; }
      100% { background-position: 0% 50%; }
    }

    /* Glass effect container */
    .glass { 
      background: rgba(0, 0, 0, 0.3); 
      backdrop-filter: blur(18px); 
      border: 1px solid rgba(255, 255, 255, 0.05);
    }

    /* Animated gradient text */
    .animated-text {
      background: linear-gradient(90deg, #b45309, #d97706, #1a1a1a);
      background-size: 400% 400%;
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      animation: textGradient 20s ease infinite;
    }

    @keyframes textGradient {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    /* Floating background circles */
    .bg-circle {
      position: absolute;
      border-radius: 50%;
      filter: blur(120px);
      opacity: 0.3;
      animation: float 15s ease-in-out infinite;
      background: linear-gradient(135deg, #b45309, #d97706);
      background-size: 200% 200%;
    }

    @keyframes float {
      0%,100% { transform: translateY(0) translateX(0); }
      50% { transform: translateY(-25px) translateX(25px); }
    }
  </style>
  @stack('head')
</head>
<body class="min-h-screen relative">

  <!-- Floating Amber Circles -->
  <div class="fixed inset-0 overflow-hidden pointer-events-none">
    <div class="bg-circle w-80 h-80 top-10 left-10"></div>
    <div class="bg-circle w-96 h-96 bottom-20 right-20"></div>
    <div class="bg-circle w-72 h-72 top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2"></div>
  </div>

  <div class="min-h-screen flex items-center justify-center px-4 py-8 relative z-10">
    <div class="w-full max-w-6xl mx-auto">
      <div class="flex flex-col lg:flex-row gap-8 items-stretch">

        <!-- Glass Container -->
        <div class="flex-1 glass rounded-3xl p-8 lg:p-12 shadow-2xl relative overflow-hidden">
          <div class="relative z-10">
            @yield('content')
          </div>
        </div>

      </div>
    </div>
  </div>

  @stack('scripts')
</body>
</html>