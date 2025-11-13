<!DOCTYPE html>
<html lang="sw">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'DEMODAY')</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="//unpkg.com/alpinejs" defer></script>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap');

    body {
      font-family: 'Inter', sans-serif;
      background-color: #f8fafc;
      color: #1f2937;
    }

    /* Sidebar Emerald Gradient */
    .sidebar {
      background: linear-gradient(180deg, #047857 0%, #065f46 100%);
      color: white;
      box-shadow: 4px 0 12px rgba(0, 0, 0, 0.1);
    }

    .sidebar button {
      background-color: rgba(255, 255, 255, 0.15);
    }

    .sidebar button:hover {
      background-color: rgba(255, 255, 255, 0.25);
    }

    header {
      background-color: #ffffff;
      border-bottom: 1px solid #e5e7eb;
    }

    main {
      background-color: #f9fafb;
    }

    /* Logo styling */
    .logo {
      width: 55px;
      height: 55px;
      object-fit: contain;
      border-radius: 10px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
    }

    /* Clean logout (Toka) style - no highlight */
    .logout-btn {
      display: block;
      width: 100%;
      padding: 8px 16px;
      font-size: 14px;
      color: #374151;
      background: white;
      border: none;
      text-align: left;
    }
  </style>
</head>

<body x-data="{ sidebarOpen: true }">
  <div class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'w-64' : 'w-20'" class="sidebar flex flex-col transition-all duration-300">
      <!-- Logo Section -->
      <div class="p-5 text-center border-b border-emerald-700">
        <div class="flex flex-col items-center space-y-2">
          <img src="https://test.mauzosheet.com/assets/images/apple-icon.gif" alt="Mauzo Logo" class="logo">
          <div x-show="sidebarOpen" class="text-xl font-bold tracking-wide">MAUZO</div>
          <div x-show="sidebarOpen" class="text-xs text-emerald-100">Admin Panel</div>
        </div>

        <button @click="sidebarOpen = !sidebarOpen" class="mt-3 p-2 rounded-full text-sm">
          <span x-show="sidebarOpen">◀</span>
          <span x-show="!sidebarOpen">▶</span>
        </button>
      </div>

      <!-- Navigation -->
      <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
        @include('partials.adminnavigation')
      </nav>

      <!-- Footer -->
      <div class="p-4 border-t border-emerald-700 text-center text-xs text-emerald-100">
        {{ now()->format('d/m/Y H:i') }}
      </div>
    </aside>

    <!-- Main Area -->
    <div class="flex-1 flex flex-col overflow-hidden bg-gray-50">

      <!-- Header -->
      <header class="px-6 py-4 flex justify-between items-center shadow-sm">
        <div>
          <h1 class="text-2xl font-bold text-emerald-700">@yield('page-title', 'Dashboard')</h1>
          <p class="text-sm text-gray-600 mt-1">@yield('page-subtitle', 'Karibu tena, Meneja!')</p>
        </div>

        <div class="flex items-center space-x-4">
          <!-- Notification bell -->
          <button class="relative p-2 text-gray-600">
            🔔
            <span class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-4 h-4 text-xs flex items-center justify-center">1</span>
          </button>

          <!-- Profile -->
          <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="flex items-center space-x-2">
              <div class="w-6 h-6 bg-emerald-600 rounded-full flex items-center justify-center text-white font-bold">
                A
              </div>
              <div class="hidden md:block text-left">
                <div class="text-sm font-medium">Admin</div>
            
              </div>
              <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
              </svg>
            </button>

            <!-- Dropdown -->
            <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-32 bg-emerald-600 border rounded shadow-md">
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">🚪 Toka</button>
              </form>
            </div>
          </div>
        </div>
      </header>

      <!-- Main Content -->
      <main class="flex-1 overflow-y-auto p-6">
        @yield('content')
      </main>
    </div>
  </div>

  @stack('scripts')
</body>
</html>
