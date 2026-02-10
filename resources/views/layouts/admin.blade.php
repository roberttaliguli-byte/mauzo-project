<!DOCTYPE html>
<html lang="sw">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
  <title>@yield('title', 'MauzoSheet Admin')</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

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

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      -webkit-tap-highlight-color: transparent;
    }

    body {
      font-family: 'Inter', sans-serif;
      background-color: var(--bg-light);
      color: var(--text-dark);
      line-height: 1.5;
      overflow-x: hidden;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
      display: flex; /* ADD THIS */
      min-height: 100vh; /* ADD THIS */
    }

    /* Mobile First Styles */
    @media (max-width: 767px) {
      html {
        font-size: 14px;
      }
    }

    /* Sidebar Styles - FIXED POSITION */
    .sidebar {
      background: linear-gradient(180deg, var(--primary-color) 0%, var(--primary-dark) 100%);
      color: var(--white);
      position: fixed;
      top: 0;
      left: 0;
      height: 100vh;
      z-index: 1000;
      transform: translateX(-100%);
      transition: transform 0.3s ease;
      overflow-y: auto;
      box-shadow: 4px 0 12px rgba(0, 0, 0, 0.1);
      width: 280px;
    }

    .sidebar.open {
      transform: translateX(0);
    }

    @media (min-width: 1024px) {
      .sidebar {
        transform: translateX(0);
        position: fixed; /* Keep it fixed on desktop too */
      }
    }

    /* Main Content Area - FIXED */
    .main-container {
      transition: all 0.3s ease;
      width: 100%;
      min-height: 100vh;
      margin-left: 0; /* Reset margin */
    }

    @media (min-width: 1024px) {
      .main-container {
        width: calc(100% - 280px);
        margin-left: 280px; /* Push content when sidebar is visible */
      }
    }

    /* Overlay for mobile sidebar */
    .overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: rgba(0, 0, 0, 0.5);
      z-index: 999;
      backdrop-filter: blur(2px);
    }

    .sidebar.open ~ .overlay { /* Changed selector */
      display: block;
    }

    @media (min-width: 1024px) {
      .overlay {
        display: none !important;
      }
    }

    /* Sidebar Items */
    .sidebar-item {
      transition: all 0.2s ease;
      padding: 0.75rem 1rem;
      margin: 0.25rem 0.5rem;
      border-radius: 0.5rem;
      display: flex;
      align-items: center;
      gap: 0.75rem;
      text-decoration: none;
      color: inherit;
    }

    .sidebar-item:hover {
      background-color: rgba(255, 255, 255, 0.15);
    }

    .active-nav-item {
      background-color: rgba(255, 255, 255, 0.2);
      font-weight: 500;
      position: relative;
    }

    .active-nav-item::after {
      content: '';
      position: absolute;
      right: 0;
      top: 50%;
      transform: translateY(-50%);
      width: 3px;
      height: 60%;
      background-color: white;
      border-radius: 2px;
    }

    /* Logo Styling */
    .logo {
      width: 40px;
      height: 40px;
      object-fit: contain;
      border-radius: 10px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
    }

    @media (min-width: 768px) {
      .logo {
        width: 50px;
        height: 50px;
      }
    }

    /* Header Styles */
    .header {
      background-color: var(--white);
      border-bottom: 1px solid var(--border-color);
      position: sticky;
      top: 0;
      z-index: 50;
    }

    /* Hamburger Menu */
    .hamburger-menu {
      width: 24px;
      height: 18px;
      position: relative;
      cursor: pointer;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    @media (min-width: 1024px) {
      .hamburger-menu {
        display: none;
      }
    }

    .hamburger-menu span {
      display: block;
      height: 2px;
      width: 100%;
      background-color: var(--text-dark);
      border-radius: 3px;
      transition: all 0.3s ease;
    }

    .hamburger-menu.active span:nth-child(1) {
      transform: translateY(8px) rotate(45deg);
    }

    .hamburger-menu.active span:nth-child(2) {
      opacity: 0;
    }

    .hamburger-menu.active span:nth-child(3) {
      transform: translateY(-8px) rotate(-45deg);
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
      font-weight: 600;
    }

    /* Dropdown Menu */
    .dropdown-menu {
      position: absolute;
      right: 0;
      margin-top: 0.5rem;
      min-width: 12rem;
      background-color: var(--white);
      border: 1px solid var(--border-color);
      border-radius: 0.5rem;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
      z-index: 100;
      overflow: hidden;
    }

    /* Profile Dropdown Item */
    .profile-dropdown-item {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      padding: 0.75rem 1rem;
      width: 100%;
      text-align: left;
      background: none;
      border: none;
      color: var(--text-dark);
      transition: background-color 0.2s;
      font-size: 0.875rem;
      cursor: pointer;
    }

    .profile-dropdown-item:hover {
      background-color: #f3f4f6;
    }

    /* New Company Alert Styles */
    .company-alert-item {
      padding: 0.75rem 1rem;
      border-bottom: 1px solid var(--border-color);
      transition: background-color 0.2s;
      display: block;
      text-decoration: none;
      color: inherit;
    }

    .company-alert-item:hover {
      background-color: #f9fafb;
    }

    .company-alert-item:last-child {
      border-bottom: none;
    }

    /* Alert Status Badges */
    .status-badge {
      padding: 0.25rem 0.5rem;
      border-radius: 0.25rem;
      font-size: 0.75rem;
      font-weight: 500;
      display: inline-block;
    }

    .status-pending {
      background-color: #fef3c7;
      color: #92400e;
    }

    .status-verified {
      background-color: #d1fae5;
      color: #065f46;
    }

    .status-user-pending {
      background-color: #fee2e2;
      color: #991b1b;
    }

    .status-user-approved {
      background-color: #dbeafe;
      color: #1e40af;
    }

    /* Package Status Badges */
    .package-badge {
      padding: 0.25rem 0.5rem;
      border-radius: 0.25rem;
      font-size: 0.75rem;
      font-weight: 500;
      display: inline-block;
      margin-top: 0.25rem;
    }

    .package-trial {
      background-color: #fef3c7;
      color: #92400e;
    }

    .package-180 {
      background-color: #d1fae5;
      color: #065f46;
    }

    .package-366 {
      background-color: #dbeafe;
      color: #1e40af;
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

    /* Mobile optimizations */
    @media (max-width: 640px) {
      .sidebar-item {
        padding: 0.625rem 0.875rem;
        font-size: 0.875rem;
      }

      .header {
        padding-left: 1rem;
        padding-right: 1rem;
      }

      .main-content {
        padding: 1rem;
      }

      .dropdown-menu {
        min-width: 10rem;
        font-size: 0.875rem;
      }
    }

    /* Touch-friendly buttons */
    button, a.btn-like {
      min-height: 44px;
      min-width: 44px;
    }

    /* Prevent text selection on interactive elements */
    button, .sidebar-item {
      user-select: none;
      -webkit-user-select: none;
    }

    /* Scrollbar styling */
    ::-webkit-scrollbar {
      width: 6px;
    }

    ::-webkit-scrollbar-track {
      background: #f1f1f1;
    }

    ::-webkit-scrollbar-thumb {
      background: #c1c1c1;
      border-radius: 3px;
    }

    ::-webkit-scrollbar-thumb:hover {
      background: #a1a1a1;
    }

    /* Ensure main content is full width */
    .main-content {
      width: 100%;
    }
  </style>
</head>

<body x-data="adminApp()" x-init="init()">
  <!-- Sidebar -->
  <aside 
    class="sidebar flex flex-col"
    :class="{'open': sidebarOpen}"
  >
    <!-- Logo Section -->
    <div class="p-4 border-b border-emerald-700">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <img 
            src="https://test.mauzosheet.com/assets/images/apple-icon.gif" 
            alt="Mauzo Logo" 
            class="logo"
          >
          <div>
            <div class="text-lg font-bold tracking-wide">MAUZO</div>
            <div class="text-xs text-emerald-100">Admin Panel</div>
          </div>
        </div>

        <!-- Close button for mobile -->
        <button 
          @click="sidebarOpen = false" 
          class="text-emerald-100 hover:text-white transition p-1 lg:hidden"
          aria-label="Close menu"
        >
          <i class="fas fa-times"></i>
        </button>
      </div>
    </div>

    <!-- Navigation -->
    @php
      $navItems = [
          ['route' => 'admin.dashboard', 'icon' => 'fas fa-building', 'label' => 'Makampuni'],
          ['route' => 'admin.reports', 'icon' => 'fas fa-chart-bar', 'label' => 'Ripoti'],
            ['route' => 'password.change', 'icon' => 'fas fa-key', 'label' => 'Badili Neno Siri'], // CHANGE
          ['route' => 'logout', 'icon' => 'fas fa-sign-out-alt', 'label' => 'Toka', 'logout' => true],
      ];
      $currentRoute = request()->route()->getName();
    @endphp

    <nav class="flex-1 py-4">
      @foreach($navItems as $item)
        @if(isset($item['logout']) && $item['logout'])
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button 
              type="submit"
              class="sidebar-item w-full text-left"
            >
              <i class="{{ $item['icon'] }}"></i>
              <span class="font-medium">{{ $item['label'] }}</span>
            </button>
          </form>
        @else
          @php
            $routeExists = Route::has($item['route']);
            $href = $routeExists ? route($item['route']) : '#';
            
            // Check if current route matches (supporting nested routes)
            $isActive = false;
            if ($routeExists) {
              $routeName = str_replace('admin.', '', $item['route']);
              $currentRouteName = str_replace('admin.', '', $currentRoute);
              $isActive = strpos($currentRouteName, $routeName) === 0;
            }
          @endphp
          <a 
            href="{{ $href }}"
            class="sidebar-item {{ $isActive ? 'active-nav-item' : '' }}"
            @if(!$routeExists)
              onclick="event.preventDefault(); alert('Ukurasa unatayarishiwa!');"
            @endif
          >
            <i class="{{ $item['icon'] }}"></i>
            <span class="font-medium">{{ $item['label'] }}</span>
          </a>
        @endif
      @endforeach
    </nav>

    <!-- Footer -->
    <div class="p-4 border-t border-emerald-700">
      <div class="text-xs text-emerald-100 text-center">
        {{ now()->format('d/m/Y H:i') }}
      </div>
      <div class="mt-2 text-center text-xs text-emerald-200">
        &copy; {{ date('Y') }} MauzoSheet Admin
      </div>
    </div>
  </aside>

  <!-- Mobile Overlay - MOVED AFTER SIDEBAR -->
  <div class="overlay" @click="sidebarOpen = false"></div>

  <!-- Main Area -->
  <div class="main-container">
    <!-- Header -->
    <header class="header px-4 sm:px-6 py-3 sm:py-4 flex justify-between items-center">
      <div class="flex items-center gap-3">
        <!-- Hamburger Menu for Mobile -->
        <button 
          class="hamburger-menu lg:hidden"
          :class="{'active': sidebarOpen}"
          @click="sidebarOpen = !sidebarOpen"
          aria-label="Toggle menu"
        >
          <span></span>
          <span></span>
          <span></span>
        </button>

        <div>
          <h1 class="text-lg sm:text-xl md:text-2xl font-bold text-emerald-700">
            @yield('page-title', 'Dashboard ya Admin')
          </h1>
          <p class="text-xs sm:text-sm text-gray-600 mt-1">
            @yield('page-subtitle', 'Karibu tena, Msimamizi!')
          </p>
        </div>
      </div>

      <div class="flex items-center space-x-3 sm:space-x-4">
        <!-- Notification Bell with New Companies Alert -->
        <div class="relative" x-data="notificationDropdown()" x-init="init()">
          <button 
            @click="toggleNotifications()"
            class="relative p-2 text-gray-600 hover:text-gray-900 rounded-full hover:bg-gray-100 transition-colors"
            aria-label="Notifications"
          >
            <i class="fas fa-bell text-lg"></i>
            <template x-if="newCompaniesCount > 0">
              <span class="notification-badge" x-text="newCompaniesCount"></span>
            </template>
          </button>

<!-- Notifications Dropdown -->
<div 
  x-show="openNotifications" 
  @click.away="openNotifications = false" 
  x-cloak
  x-transition
  class="dropdown-menu"
  style="width: 350px; max-width: calc(100vw - 2rem);"
  x-bind:style="window.innerWidth <= 640 ? 'position: fixed; left: 50%; top: 4rem; transform: translateX(-50%); right: auto;' : ''"
>
            <!-- Header -->
            <div class="px-4 py-3 bg-gradient-to-r from-emerald-600 to-emerald-500 text-white">
              <div class="flex justify-between items-center">
                <div class="font-medium">Arifa Mpya</div>
                <div class="text-sm" x-show="newCompaniesCount > 0">
                  <span x-text="newCompaniesCount"></span> mpya
                </div>
              </div>
            </div>

            <!-- New Companies List -->
            <div class="max-h-64 overflow-y-auto">
              <template x-if="newCompanies.length > 0">
                <div>
                  <div class="px-4 py-2 text-xs text-gray-500 bg-gray-50">
                    Kampuni Zilizosajiliwa Hivi Karibuni
                  </div>
                  <template x-for="company in newCompanies" :key="company.id">
                    <a 
                      :href="`/admin/dashboard#company-${company.id}`"
                      class="company-alert-item block"
                      @click="markAsSeen(company.id)"
                    >
                      <div class="flex justify-between items-start">
                        <div class="flex-1">
                          <div class="font-medium" x-text="company.company_name"></div>
                          <div class="text-xs text-gray-500 mt-1">
                            <i class="fas fa-user mr-1"></i>
                            <span x-text="company.owner_name"></span>
                          </div>
                          <div class="text-xs text-gray-500">
                            <i class="fas fa-phone mr-1"></i>
                            <span x-text="company.phone"></span>
                          </div>
                          
                          <!-- Status Badges -->
                          <div class="flex flex-wrap gap-1 mt-2">
                            <span 
                              class="status-badge"
                              :class="company.is_verified ? 'status-verified' : 'status-pending'"
                              x-text="company.is_verified ? 'Kampuni Imethibitishwa' : 'Kampuni Inasubiri'"
                            ></span>
                            
                            <span 
                              class="status-badge"
                              :class="company.is_user_approved ? 'status-user-approved' : 'status-user-pending'"
                              x-text="company.is_user_approved ? 'Mtumiaji Amethibitishwa' : 'Mtumiaji Anasubiri'"
                            ></span>
                          </div>
                          
                          <!-- Package Info -->
                          <div x-show="company.package" class="mt-1">
                            <span class="package-badge" 
                                  :class="getPackageClass(company.package)"
                                  x-text="company.package"></span>
                          </div>
                        </div>
                        <div class="ml-2 text-right">
                          <div class="text-xs text-gray-400" x-text="formatDate(company.created_at)"></div>
                          <div x-show="company.package_end" class="text-xs text-gray-500 mt-1">
                            Mwisho: <span x-text="formatDate(company.package_end)"></span>
                          </div>
                        </div>
                      </div>
                      <div class="mt-2 text-xs text-emerald-600" x-show="!company.seen">
                        <i class="fas fa-circle mr-1 text-xs"></i> Mpya
                      </div>
                    </a>
                  </template>
                </div>
              </template>

              <template x-if="newCompanies.length === 0">
                <div class="px-4 py-8 text-center">
                  <i class="fas fa-check-circle text-emerald-500 text-2xl mb-2"></i>
                  <p class="text-gray-600">Hakuna arifa mpya</p>
                  <p class="text-xs text-gray-400 mt-1">Hakuna kampuni mpya zilizosajiliwa</p>
                </div>
              </template>
            </div>

            <!-- Footer -->
            <div class="px-4 py-2 border-t border-gray-100 bg-gray-50">
              <div class="flex justify-between items-center">
                <a 
                  href="{{ route('admin.dashboard') }}" 
                  class="text-sm text-emerald-600 hover:text-emerald-700 font-medium"
                >
                  <i class="fas fa-list mr-1"></i> Angalia Kampuni Zote
                </a>
                <button 
                  @click="markAllAsSeen()" 
                  class="text-sm text-gray-600 hover:text-gray-800"
                  :disabled="newCompaniesCount === 0"
                >
                  <i class="fas fa-check mr-1"></i> Weka Zote Zimesomwa
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Profile Dropdown -->
        <div class="relative" x-data="{ profileDropdownOpen: false }">
          <button 
            @click="profileDropdownOpen = !profileDropdownOpen"
            class="flex items-center space-x-2 p-1 rounded-md hover:bg-gray-100 transition-colors"
            aria-label="User menu"
            :aria-expanded="profileDropdownOpen.toString()"
          >
            <div class="w-8 h-8 bg-emerald-600 rounded-full flex items-center justify-center text-white font-bold">
              A
            </div>
            <div class="hidden sm:block text-left">
              <div class="text-sm font-medium">Admin</div>
              <div class="text-xs text-gray-500">Administrator</div>
            </div>
            <i 
              class="fas fa-chevron-down text-gray-500 transition-transform" 
              :class="{'rotate-180': profileDropdownOpen}"
            ></i>
          </button>

          <!-- Dropdown Menu -->
          <div 
            x-show="profileDropdownOpen" 
            @click.away="profileDropdownOpen = false" 
            x-cloak
            x-transition
            class="dropdown-menu"
          >
            <div class="py-2">
              <div class="px-4 py-2 border-b border-gray-100">
                <div class="font-medium">Admin</div>
                <div class="text-xs text-gray-500">mauzoheet9@gmail.com</div>
              </div>
              
      
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="profile-dropdown-item w-full">
                  <i class="fas fa-sign-out-alt text-gray-400"></i>
                  <span>Toka</span>
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="main-content flex-1 p-4 sm:p-6">
      @yield('content')
    </main>
  </div>

  <script>
    function adminApp() {
      return {
        sidebarOpen: false,
        
        init() {
          // Set initial sidebar state based on screen size
          this.handleResize();
          window.addEventListener('resize', () => this.handleResize());
          
          // Close sidebar when pressing escape key
          document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.sidebarOpen) {
              this.sidebarOpen = false;
            }
          });
        },
        
        handleResize() {
          if (window.innerWidth >= 1024) {
            // On desktop, sidebar is always open
            this.sidebarOpen = true;
          } else {
            // On mobile, sidebar is closed by default
            this.sidebarOpen = false;
          }
        },
        
        toggleSidebar() {
          this.sidebarOpen = !this.sidebarOpen;
        }
      }
    }

    function notificationDropdown() {
      return {
        openNotifications: false,
        newCompanies: [],
        newCompaniesCount: 0,
        seenCompanies: new Set(),
        
        init() {
          // Load seen companies from localStorage
          const savedSeen = localStorage.getItem('seenCompanies');
          if (savedSeen) {
            this.seenCompanies = new Set(JSON.parse(savedSeen));
          }
          
          // Fetch new companies
          this.fetchNewCompanies();
          
          // Set up periodic refresh (every 2 minutes)
          setInterval(() => {
            this.fetchNewCompanies();
          }, 2 * 60 * 1000);
        },
        
        fetchNewCompanies() {
          // Fetch companies registered in the last 7 days
          const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
          
          fetch('/api/admin/new-companies', {
            method: 'GET',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'X-CSRF-TOKEN': csrfToken
            },
            credentials: 'same-origin'
          })
            .then(response => {
              if (!response.ok) throw new Error('Network response was not ok');
              return response.json();
            })
            .then(data => {
              this.newCompanies = data.companies || [];
              
              // Filter to show only unseen companies
              const unseenCompanies = this.newCompanies.filter(company => 
                !this.seenCompanies.has(company.id)
              );
              
              this.newCompaniesCount = unseenCompanies.length;
            })
            .catch(error => {
              console.error('Error fetching new companies:', error);
              this.newCompanies = [];
              this.newCompaniesCount = 0;
            });
        },
        
toggleNotifications() {
  this.openNotifications = !this.openNotifications;
  if (this.openNotifications) {
    this.fetchNewCompanies();
    
    // On mobile, prevent body scrolling when dropdown is open
    if (window.innerWidth <= 640) {
      document.body.style.overflow = 'hidden';
    }
  } else {
    // Restore scrolling
    document.body.style.overflow = '';
  }
},
        
        markAsSeen(companyId) {
          this.seenCompanies.add(companyId);
          this.saveSeenCompanies();
          this.updateCount();
        },
        
        markAllAsSeen() {
          this.newCompanies.forEach(company => {
            this.seenCompanies.add(company.id);
          });
          this.saveSeenCompanies();
          this.newCompaniesCount = 0;
          this.openNotifications = false;
        },
        
        saveSeenCompanies() {
          localStorage.setItem('seenCompanies', JSON.stringify([...this.seenCompanies]));
        },
        
        updateCount() {
          const unseenCompanies = this.newCompanies.filter(company => 
            !this.seenCompanies.has(company.id)
          );
          this.newCompaniesCount = unseenCompanies.length;
        },
        
        formatDate(dateString) {
          if (!dateString) return '';
          
          const date = new Date(dateString);
          const now = new Date();
          const diffMs = now - date;
          const diffMins = Math.floor(diffMs / 60000);
          const diffHours = Math.floor(diffMs / 3600000);
          const diffDays = Math.floor(diffMs / 86400000);
          
          if (diffMins < 60) {
            return `Dakika ${diffMins} zilizopita`;
          } else if (diffHours < 24) {
            return `Saa ${diffHours} zilizopita`;
          } else if (diffDays < 7) {
            return `Siku ${diffDays} zilizopita`;
          } else {
            return date.toLocaleDateString('sw-TZ', {
              day: '2-digit',
              month: '2-digit',
              year: 'numeric'
            });
          }
        },
        
        getPackageClass(packageType) {
          if (!packageType) return '';
          
          if (packageType.includes('Free Trial') || packageType.includes('Trial')) {
            return 'package-trial';
          } else if (packageType.includes('180')) {
            return 'package-180';
          } else if (packageType.includes('366')) {
            return 'package-366';
          }
          return '';
        }
      }
    }
  </script>

  @stack('scripts')
</body>
</html>