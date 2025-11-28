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
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

    :root {
      --primary-color: #047857;
      --primary-dark: #065f46;
      --primary-light: #d1fae5;
      --text-dark: #1f2937;
      --text-light: #6b7280;
      --bg-light: #f9fafb;
      --white: #ffffff;
      --border-color: #e5e7eb;
    }

    body {
      font-family: 'Inter', sans-serif;
      background-color: var(--bg-light);
      color: var(--text-dark);
      line-height: 1.5;
    }

    /* Sidebar Styles */
    .sidebar {
      background: linear-gradient(180deg, var(--primary-color) 0%, var(--primary-dark) 100%);
      color: var(--white);
      box-shadow: 4px 0 12px rgba(0, 0, 0, 0.1);
    }

    .sidebar-item {
      transition: all 0.2s ease;
    }

    .sidebar-item:hover {
      background-color: rgba(255, 255, 255, 0.15);
    }

    .active-nav-item {
      background-color: rgba(255, 255, 255, 0.2);
      font-weight: 500;
    }

    /* Logo Styling */
    .logo {
      width: 55px;
      height: 55px;
      object-fit: contain;
      border-radius: 10px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
    }

    /* Header Styles */
    .header {
      background-color: var(--white);
      border-bottom: 1px solid var(--border-color);
    }

    /* Main Content Area */
    .main-content {
      background-color: var(--bg-light);
    }

    /* Notification Badge */
    .notification-badge {
      position: absolute;
      top: -0.25rem;
      right: -0.25rem;
      background-color: #ef4444;
      color: white;
      border-radius: 50%;
      width: 1.25rem;
      height: 1.25rem;
      font-size: 0.75rem;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    /* Dropdown Menu */
    .dropdown-menu {
      position: absolute;
      right: 0;
      margin-top: 0.5rem;
      width: 8rem;
      background-color: var(--white);
      border: 1px solid var(--border-color);
      border-radius: 0.375rem;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
      z-index: 10;
    }

    /* Logout Button */
    .logout-btn {
      display: block;
      width: 100%;
      padding: 0.5rem 1rem;
      font-size: 0.875rem;
      color: var(--text-dark);
      background: var(--white);
      border: none;
      text-align: left;
      transition: background-color 0.2s;
    }

    .logout-btn:hover {
      background-color: #f3f4f6;
    }

    /* Smooth Transitions */
    .transition-all {
      transition-property: all;
      transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
      transition-duration: 300ms;
    }

    /* Hide elements when Alpine.js is loading */
    [x-cloak] {
      display: none !important;
    }
  </style>
</head>

<body x-data="{ sidebarOpen: true, profileDropdownOpen: false }">
  <div class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside 
      :class="sidebarOpen ? 'w-64' : 'w-20'" 
      class="sidebar flex flex-col transition-all duration-300"
    >
      <!-- Logo Section -->
      <div class="p-5 text-center border-b border-emerald-700">
        <div class="flex flex-col items-center space-y-2">
          <img 
            src="https://test.mauzosheet.com/assets/images/apple-icon.gif" 
            alt="Mauzo Logo" 
            class="logo"
          >
          <div x-show="sidebarOpen" class="text-xl font-bold tracking-wide">MAUZO</div>
          <div x-show="sidebarOpen" class="text-xs text-emerald-100">Admin Panel</div>
        </div>

        <button 
          @click="sidebarOpen = !sidebarOpen" 
          class="mt-3 p-2 rounded-full text-sm hover:bg-emerald-700 transition-colors"
          :aria-label="sidebarOpen ? 'Collapse sidebar' : 'Expand sidebar'"
        >
          <span x-show="sidebarOpen">◀</span>
          <span x-show="!sidebarOpen">▶</span>
        </button>
      </div>

      <!-- Navigation -->
      @php
$navItems = [
    ['route' => 'admin.dashboard', 'icon' => '🏢', 'label' => 'Makampuni'],
    ['route' => 'admin.reports', 'icon' => '📄', 'label' => 'Ripoti'],
    ['route' => 'password.change', 'icon' => '🔒', 'label' => 'Neno la Siri'], 
    ['route' => 'logout', 'icon' => '⏻', 'label' => 'Toka', 'logout' => true],
];
      $currentRoute = request()->route()->getName();
      @endphp

      <nav class="flex-1 py-4 px-2 space-y-1">
        @foreach($navItems as $item)
          @if(isset($item['logout']) && $item['logout'])
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button 
                type="submit"
                class="sidebar-item flex items-center px-4 py-3 rounded-xl w-full"
                :class="sidebarOpen ? 'justify-start' : 'justify-center'"
              >
                {{ $item['icon'] }}
                <span x-show="sidebarOpen" x-transition class="ml-3 font-medium">
                  {{ $item['label'] }}
                </span>
              </button>
            </form>
          @else
            @php
              // Safe route checking with fallback to '#'
              $routeExists = Route::has($item['route']);
              $href = $routeExists ? route($item['route']) : '#';
              $isActive = $currentRoute === $item['route'] ? 'active-nav-item' : '';
            @endphp
            <a 
              href="{{ $href }}"
              class="sidebar-item flex items-center px-4 py-3 rounded-xl {{ $isActive }}"
              :class="sidebarOpen ? 'justify-start' : 'justify-center'"
              @if(!$routeExists)
                onclick="event.preventDefault(); alert('Ukurasa unatayarishiwa!');"
              @endif
            >
              {{ $item['icon'] }}
              <span x-show="sidebarOpen" x-transition class="ml-3 font-medium">
                {{ $item['label'] }}
              </span>
            </a>
          @endif
        @endforeach
      </nav>

      <!-- Footer -->
      <div class="p-4 border-t border-emerald-700 text-center text-xs text-emerald-100">
        {{ now()->format('d/m/Y H:i') }}
      </div>
    </aside>

    <!-- Main Area -->
    <div class="flex-1 flex flex-col overflow-hidden">

      <!-- Header -->
      <header class="header px-6 py-4 flex justify-between items-center">
        <div>
          <h1 class="text-2xl font-bold text-emerald-700">
            @yield('page-title', 'Dashboard')
          </h1>
          <p class="text-sm text-gray-600 mt-1">
            @yield('page-subtitle', 'Karibu tena, Meneja!')
          </p>
        </div>

        <div class="flex items-center space-x-4">
          <!-- Notification Bell -->
          <button 
            class="relative p-2 text-gray-600 hover:text-gray-900 rounded-full hover:bg-gray-100 transition-colors"
            aria-label="Notifications"
          >
            🔔
            <span class="notification-badge">1</span>
          </button>

          <!-- Profile Dropdown -->
          <div class="relative">
            <button 
              @click="profileDropdownOpen = !profileDropdownOpen"
              class="flex items-center space-x-2 p-1 rounded-md hover:bg-gray-100 transition-colors"
              aria-label="User menu"
              aria-expanded="false"
              :aria-expanded="profileDropdownOpen.toString()"
            >
              <div class="w-8 h-8 bg-emerald-600 rounded-full flex items-center justify-center text-white font-bold">
                A
              </div>
              <div class="hidden md:block text-left">
                <div class="text-sm font-medium">Admin</div>
              </div>
              <svg 
                class="w-4 h-4 text-gray-500 transition-transform" 
                :class="{'rotate-180': profileDropdownOpen}"
                fill="none" 
                stroke="currentColor" 
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
              </svg>
            </button>

            <!-- Dropdown Menu -->
            <div 
              x-show="profileDropdownOpen" 
              @click.away="profileDropdownOpen = false" 
              x-cloak
              class="dropdown-menu"
            >
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn flex items-center">
                  <span class="mr-2">🚪</span> Toka
                </button>
              </form>
            </div>
          </div>
        </div>
      </header>

      <!-- Main Content -->
      <main class="main-content flex-1 overflow-y-auto p-6">
        @yield('content')
      </main>
    </div>
  </div>

  @stack('scripts')
</body>
</html>