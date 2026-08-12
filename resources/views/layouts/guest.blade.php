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
      min-height: 100dvh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1.5rem 1rem;
      position: relative;
      background: #0f0d0b;
      color: #f0ebe5;
      overflow-x: hidden;
    }

    /* Dark background with blur */
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

    /* Loading Screen */
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
    @keyframes spin { to { transform: rotate(360deg); } }

    /* Brand */
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
      font-size: clamp(0.75rem, 2vw, 0.85rem);
      font-weight: 600;
      letter-spacing: 0.03em;
      color: #eae3db;
      text-shadow: 0 2px 6px rgba(0,0,0,0.6);
    }

    @keyframes riseIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* Auth Wrapper - Responsive */
    .auth-wrapper {
      width: 100%;
      max-width: 460px;
      margin: 0 auto;
      opacity: 0;
      animation: riseIn 0.5s ease 0.22s forwards;
    }

    /* Glass Card */
    .glass {
      width: 100%;
      background: rgba(20, 18, 16, 0.82);
      backdrop-filter: blur(14px) saturate(1.1);
      -webkit-backdrop-filter: blur(14px) saturate(1.1);
      border: 1px solid rgba(255, 215, 160, 0.25);
      border-radius: 28px;
      padding: 2.5rem 3rem 2.3rem;
      box-shadow: 0 30px 70px -20px rgba(0,0,0,0.8), 0 2px 0 rgba(255, 215, 160, 0.15) inset;
      transition: all 0.2s ease;
    }

    /* Tablet */
    @media (max-width: 900px) {
      .auth-wrapper { max-width: 440px; }
      .glass { padding: 2rem 2.5rem 1.8rem; }
    }

    /* Mobile */
    @media (max-width: 640px) {
      body { padding: 1.25rem 0.75rem; }
      .auth-wrapper { max-width: 100%; }
      .glass { 
        padding: 1.5rem 1.25rem 1.25rem; 
        border-radius: 22px;
        box-shadow: 0 20px 50px -15px rgba(0,0,0,0.7);
      }
      .brand img { width: 40px; height: 40px; }
      .brand { margin-bottom: 1rem; }
    }

    /* Very small phones */
    @media (max-width: 380px) {
      body { padding: 1rem 0.6rem; }
      .glass { padding: 1.2rem 1rem 1rem; border-radius: 18px; }
      .brand img { width: 36px; height: 36px; }
      .brand { margin-bottom: 0.8rem; gap: 0.3rem; }
      .brand-name { font-size: 0.7rem; }
    }

    /* Title */
    .auth-title {
      font-size: clamp(1.25rem, 3.5vw, 1.5rem);
      font-weight: 700;
      letter-spacing: -0.02em;
      margin-bottom: 1.4rem;
      color: #f5efe9;
      text-shadow: 0 2px 10px rgba(0,0,0,0.3);
    }
    @media (max-width: 640px) {
      .auth-title { margin-bottom: 1.1rem; }
    }
    @media (max-width: 380px) {
      .auth-title { font-size: 1.1rem; margin-bottom: 0.8rem; }
    }

    /* Home Link */
    .home-link {
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
      font-size: clamp(0.7rem, 1.2vw, 0.76rem);
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
      min-height: 36px;
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
      flex-shrink: 0;
    }
    @media (max-width: 640px) {
      .home-link { min-height: 34px; font-size: 0.7rem; padding: 0.2rem 0.8rem 0.2rem 0.6rem; }
      .home-link svg { width: 12px; height: 12px; }
    }
    @media (max-width: 380px) {
      .home-link { min-height: 30px; font-size: 0.65rem; padding: 0.15rem 0.6rem 0.15rem 0.5rem; margin-bottom: 0.7rem; }
      .home-link svg { width: 10px; height: 10px; }
    }

    /* Form Fields */
    .field { margin-bottom: 1rem; }
    .field:last-of-type { margin-bottom: 0; }
    @media (max-width: 640px) { .field { margin-bottom: 0.85rem; } }
    @media (max-width: 380px) { .field { margin-bottom: 0.7rem; } }

    label {
      display: block;
      font-size: clamp(0.7rem, 1.2vw, 0.76rem);
      font-weight: 600;
      color: #d6cec4;
      margin-bottom: 0.3rem;
      letter-spacing: 0.01em;
    }

    .form-input {
      width: 100%;
      padding: 0.65rem 0.85rem;
      font-size: clamp(0.85rem, 1.8vw, 0.88rem);
      border: 1px solid rgba(255, 215, 160, 0.20);
      border-radius: 12px;
      background: rgba(20, 18, 16, 0.70);
      backdrop-filter: blur(2px);
      color: #f0ebe5;
      outline: none;
      font-family: inherit;
      appearance: none;
      transition: border-color 0.18s ease, box-shadow 0.18s ease, background 0.18s ease;
      min-height: 48px;
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
    @media (max-width: 640px) {
      .form-input { font-size: 16px; min-height: 46px; padding: 0.55rem 0.75rem; } /* Prevents iOS zoom */
    }
    @media (max-width: 380px) {
      .form-input { min-height: 42px; font-size: 15px; padding: 0.45rem 0.65rem; border-radius: 10px; }
    }

    .field-error {
      font-size: clamp(0.6rem, 1vw, 0.68rem);
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
      min-height: 32px;
      min-width: 32px;
      align-items: center;
      justify-content: center;
    }
    .pw-toggle:hover { color: #e8e0d8; }
    .pw-toggle svg { width: 18px; height: 18px; stroke: currentColor; stroke-width: 1.8; fill: none; flex-shrink: 0; }
    @media (max-width: 640px) {
      .pw-toggle { right: 0.5rem; min-height: 28px; min-width: 28px; }
      .pw-toggle svg { width: 16px; height: 16px; }
    }
    @media (max-width: 380px) {
      .pw-toggle { right: 0.4rem; min-height: 24px; min-width: 24px; }
      .pw-toggle svg { width: 14px; height: 14px; }
    }

    .pw-hint {
      font-size: clamp(0.6rem, 1vw, 0.68rem);
      color: #a09589;
      margin-top: 0.35rem;
      display: none;
    }
    .pw-hint.show { display: block; }
    .pw-hint.bad { color: #fca5a5; }

    /* Buttons */
    .actions { margin-top: 1.5rem; }
    @media (max-width: 640px) { .actions { margin-top: 1.25rem; } }
    @media (max-width: 380px) { .actions { margin-top: 1rem; } }

    .btn-primary {
      width: 100%;
      padding: 0.75rem 1.5rem;
      font-size: clamp(0.85rem, 1.8vw, 0.92rem);
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
      min-height: 50px;
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
      width: 18px;
      height: 18px;
      border: 2px solid rgba(255,255,255,0.3);
      border-top-color: #fff;
      border-radius: 50%;
      animation: spin 0.7s linear infinite;
      margin: 0 auto;
    }
    .btn.loading .btn-label { visibility: hidden; position: absolute; }
    .btn.loading { position: relative; }
    .btn.loading .spinner { display: inline-block; }

    @media (max-width: 640px) {
      .btn-primary { min-height: 48px; padding: 0.65rem 1.2rem; border-radius: 12px; font-size: 0.9rem; }
    }
    @media (max-width: 380px) {
      .btn-primary { min-height: 44px; padding: 0.55rem 1rem; border-radius: 10px; font-size: 0.85rem; }
    }

    /* Alerts */
    .alert {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 0.6rem;
      font-size: clamp(0.7rem, 1.2vw, 0.76rem);
      padding: 0.5rem 0.7rem;
      border-radius: 12px;
      margin-bottom: 1rem;
      line-height: 1.4;
      background: rgba(0,0,0,0.5);
      backdrop-filter: blur(4px);
      border: 1px solid rgba(255,255,255,0.06);
      word-break: break-word;
    }
    .alert-ok { color: #86efac; border-color: rgba(134, 239, 172, 0.2); }
    .alert-bad { color: #fca5a5; border-color: rgba(252, 165, 165, 0.15); }
    .alert button {
      background: none;
      border: none;
      color: inherit;
      opacity: 0.5;
      cursor: pointer;
      font-size: clamp(0.8rem, 1.5vw, 0.85rem);
      line-height: 1;
      padding: 0 0.2rem;
      flex-shrink: 0;
      min-height: 24px;
      min-width: 24px;
    }
    .alert button:hover { opacity: 1; }
    @media (max-width: 640px) {
      .alert { padding: 0.45rem 0.6rem; border-radius: 10px; font-size: 0.7rem; }
    }

    /* Footer */
    .auth-footer {
      text-align: center;
      margin-top: 1.5rem;
      font-size: clamp(0.7rem, 1.2vw, 0.78rem);
      color: #b0a79c;
    }
    .auth-footer a {
      color: #dba459;
      font-weight: 600;
      text-decoration: none;
      transition: color 0.15s ease;
    }
    .auth-footer a:hover { color: #f0c78a; text-decoration: underline; }
    @media (max-width: 640px) { .auth-footer { margin-top: 1.25rem; } }
    @media (max-width: 380px) { .auth-footer { margin-top: 1rem; font-size: 0.7rem; } }

    /* Progress bar for multi-step forms (register) */
    .progress-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 1.1rem;
      gap: 0.75rem;
    }
    .progress-text {
      font-size: 0.72rem;
      font-weight: 600;
      color: #c4bdb3;
      white-space: nowrap;
    }
    .progress-track {
      flex: 1;
      height: 4px;
      background: rgba(255,215,160,0.15);
      border-radius: 4px;
      overflow: hidden;
    }
    .progress-fill {
      height: 100%;
      background: #d97706;
      border-radius: 4px;
      width: 33.33%;
      transition: width 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }

    /* Grid for register form */
    .grid-2 {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 0.75rem;
    }
    @media (max-width: 480px) {
      .grid-2 { grid-template-columns: 1fr; gap: 0.95rem; }
    }

    /* Select dropdown */
    select.form-input {
      padding-right: 2.3rem;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23887e74' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 0.9rem center;
      background-size: 11px;
      cursor: pointer;
      color: #f0ebe5;
    }
    select.form-input option { background: #1a1816; color: #f0ebe5; }

    /* Step panels */
    .step-panel { display: none; }
    .step-panel.active { display: block; }

    /* Secondary button */
    .btn-secondary {
      background: rgba(255,255,255,0.06);
      color: #c4bdb3;
      padding: 0.7rem 1.2rem;
      border: 1px solid rgba(255,215,160,0.08);
      border-radius: 12px;
      font-family: inherit;
      font-size: 0.85rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s ease;
      min-height: 48px;
    }
    .btn-secondary:hover { background: rgba(255,255,255,0.12); color: #fff; }

    .actions .btn-secondary { flex: 0 0 auto; }
    .actions .btn-primary { flex: 1; }
    .actions.single .btn-primary { flex: 1; }

    /* Honeypot */
    #website-wrapper {
      position: absolute !important;
      left: -9999px !important;
      top: -9999px !important;
      height: 0 !important;
      width: 0 !important;
      overflow: hidden !important;
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

    <div class="auth-wrapper">
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

      // Toggle password visibility helper (for pages that use it)
      document.querySelectorAll('.pw-toggle').forEach(btn => {
        btn.addEventListener('click', function() {
          const input = this.closest('.input-wrapper').querySelector('input');
          if (input) {
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            const svg = this.querySelector('svg');
            if (svg) {
              if (isPassword) {
                svg.innerHTML = `
                  <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                  <line x1="1" y1="1" x2="23" y2="23"/>
                `;
              } else {
                svg.innerHTML = `
                  <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/>
                  <circle cx="12" cy="12" r="3"/>
                `;
              }
            }
          }
        });
      });

      console.log('✅ MauzoSheetAI Guest Layout loaded');
    })();
  </script>

  @stack('scripts')
</body>
</html>