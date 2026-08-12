<!doctype html>
<html lang="sw">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'MauzoSheetAI')</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body { 
      font-family: 'Inter', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial; 
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem 1.25rem;
      position: relative;
      background: #0f0d0b;
      color: #f0ebe5;
      overflow-x: hidden;
    }

    /* Dark background with blur - matches login page style */
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
      filter: blur(8px) saturate(1.1);
      transform: scale(1.05);
      z-index: -2;
    }

    body::after {
      content: "";
      position: fixed;
      inset: 0;
      background: radial-gradient(ellipse at 50% 50%, rgba(0,0,0,0) 50%, rgba(0,0,0,0.50) 100%);
      z-index: -1;
    }

    /* Glass effect - matches login card */
    .glass { 
      width: 100%;
      background: rgba(20, 18, 16, 0.82);
      backdrop-filter: blur(14px) saturate(1.1);
      -webkit-backdrop-filter: blur(14px) saturate(1.1);
      border: 1px solid rgba(255, 215, 160, 0.25);
      border-radius: 28px;
      box-shadow: 0 30px 70px -20px rgba(0,0,0,0.8), 0 2px 0 rgba(255, 215, 160, 0.15) inset;
      transition: all 0.2s ease;
      padding: 2.5rem 3rem 2.3rem;
    }

    /* Responsive glass padding */
    @media (max-width: 640px) {
      .glass { padding: 1.7rem 1.25rem 1.5rem; border-radius: 22px; }
    }

    /* Brand - matches login */
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
      box-shadow: 0 4px 20px rgba(0,0,0,0.6);
    }
    .brand-name {
      font-size: 0.85rem;
      font-weight: 600;
      letter-spacing: 0.03em;
      color: #eae3db;
      text-shadow: 0 2px 6px rgba(0,0,0,0.6);
    }

    @keyframes riseIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* Home link - inside card */
    .home-link {
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
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
      margin-bottom: 1rem;
      width: fit-content;
    }
    .home-link:hover {
      background: rgba(255, 215, 160, 0.12);
      border-color: #d97706;
      color: #fff;
      box-shadow: 0 0 20px rgba(217, 119, 6, 0.15);
    }
    .home-link svg {
      width: 13px;
      height: 13px;
      stroke: currentColor;
      stroke-width: 2.2;
      fill: none;
    }

    /* Page title */
    .auth-title {
      font-size: 1.5rem;
      font-weight: 700;
      letter-spacing: -0.02em;
      margin-bottom: 1.4rem;
      color: #f5efe9;
      text-shadow: 0 2px 10px rgba(0,0,0,0.3);
    }

    /* Form fields */
    .field { margin-bottom: 1rem; }
    .field:last-of-type { margin-bottom: 0; }

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
    .form-input::placeholder { color: #887e74; }
    .form-input:focus {
      border-color: #d97706;
      background: rgba(28, 25, 22, 0.85);
      box-shadow: 0 0 0 4px rgba(217, 119, 6, 0.20), 0 6px 20px rgba(0,0,0,0.3);
    }
    .form-input.invalid {
      border-color: #dc7a5a;
    }
    .form-input.invalid:focus {
      box-shadow: 0 0 0 4px rgba(194, 65, 12, 0.20);
    }

    .field-error {
      font-size: 0.68rem;
      color: #fca5a5;
      margin-top: 0.25rem;
      display: none;
    }
    .field-error.show { display: block; }

    .input-wrapper { position: relative; }
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
    .pw-toggle:hover { color: #e8e0d8; }
    .pw-toggle svg { width: 16px; height: 16px; stroke: currentColor; stroke-width: 1.8; fill: none; }

    /* Buttons */
    .btn-primary {
      width: 100%;
      padding: 0.75rem 1.5rem;
      font-size: 0.92rem;
      font-weight: 700;
      border-radius: 14px;
      background: #d97706;
      color: #fff;
      border: none;
      box-shadow: 0 12px 30px -8px rgba(217, 119, 6, 0.45);
      transition: all 0.2s ease;
      cursor: pointer;
      font-family: inherit;
      letter-spacing: 0.01em;
      position: relative;
    }
    .btn-primary:hover:not(:disabled) {
      background: #b45309;
      transform: translateY(-2px);
      box-shadow: 0 18px 36px -10px rgba(217, 119, 6, 0.6);
    }
    .btn-primary:disabled {
      opacity: 0.5;
      cursor: not-allowed;
      transform: none;
      box-shadow: none;
    }
    .btn .spinner {
      display: none;
      width: 15px;
      height: 15px;
      border: 2px solid rgba(255,255,255,0.3);
      border-top-color: #fff;
      border-radius: 50%;
      animation: spin 0.7s linear infinite;
      margin: 0 auto;
    }
    .btn.loading .btn-label { visibility: hidden; position: absolute; }
    .btn.loading { position: relative; }
    .btn.loading .spinner { display: inline-block; }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* Footer links */
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
    .auth-footer a:hover { color: #f0c78a; text-decoration: underline; }

    /* Alerts */
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
      background: rgba(0,0,0,0.5);
      backdrop-filter: blur(4px);
      border: 1px solid rgba(255,255,255,0.06);
    }
    .alert-ok { color: #86efac; border-color: rgba(134, 239, 172, 0.2); }
    .alert-bad { color: #fca5a5; border-color: rgba(252, 165, 165, 0.15); }
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
    .alert button:hover { opacity: 1; }

    /* Responsive */
    @media (max-width: 640px) {
      body { padding: 1.25rem 0.8rem; }
      .auth-title { font-size: 1.25rem; }
    }

    /* Loading screen */
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
      border-top-color: #d97706;
      animation: spin 0.9s linear infinite;
    }
    @keyframes markIn {
      from { opacity: 0; transform: scale(0.85); }
      to { opacity: 1; transform: scale(1); }
    }

    @media (prefers-reduced-motion: reduce) {
      *, *::before, *::after { animation-duration: 0.01ms !important; transition-duration: 0.01ms !important; }
    }
  </style>
  @stack('head')
</head>
<body>

  <!-- Loading Screen -->
  <div id="loadingScreen">
    <div class="loading-mark">
      <img src="{{ asset('logo11.png') }}" alt="MauzoSheetAI" onerror="this.src='https://placehold.co/40x40/d97706/white?text=M'">
    </div>
  </div>

  <!-- Main Content -->
  <div>
    <!-- Brand -->
    <div class="brand">
      <img src="{{ asset('logo11.png') }}" alt="MauzoSheetAI" onerror="this.src='https://placehold.co/48x48/d97706/white?text=M'">
      <span class="brand-name">MauzoSheetAI</span>
    </div>

    <div class="auth-wrapper" style="width:440px;max-width:440px;margin:0 auto;opacity:0;animation:riseIn 0.5s ease 0.22s forwards;">
      <div class="glass">
        @yield('content')
      </div>
    </div>
  </div>

  <script>
    (function() {
      'use strict';

      // Hide loading screen
      window.addEventListener('load', function() {
        setTimeout(function() {
          const loader = document.getElementById('loadingScreen');
          if (loader) loader.classList.add('hide');
        }, 350);
      });

      // Auto-hide alerts after 5 seconds
      setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
          alert.style.opacity = '0';
          alert.style.transition = 'opacity 0.5s ease';
          setTimeout(() => alert.remove(), 500);
        });
      }, 5000);

      console.log('✅ MauzoSheetAI Guest Layout loaded');
    })();
  </script>

  @stack('scripts')
</body>
</html>