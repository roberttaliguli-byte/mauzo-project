<!DOCTYPE html>
<html lang="sw">
<head>
    <!-- Meta Tags - Updated to handle both guards -->
    <meta name="company-id" content="{{ 
        (Auth::check() ? Auth::user()->company_id : 
        (Auth::guard('mfanyakazi')->check() ? Auth::guard('mfanyakazi')->user()->company_id : 'default')) 
    }}">
    <meta name="company-name" content="{{ 
        (Auth::check() && Auth::user()->company ? Auth::user()->company->company_name : 
        (Auth::guard('mfanyakazi')->check() && Auth::guard('mfanyakazi')->user()->company ? Auth::guard('mfanyakazi')->user()->company->company_name : 'Default Company')) 
    }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'MAUZO SHEET')</title>
    <style>[x-cloak]{display:none!important}</style>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
<style>
    /* Custom Styles - keep your existing styles */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
    
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        -webkit-tap-highlight-color: transparent;
    }
    
    html {
        font-size: 16px;
    }
    
    body {
        font-family: 'Inter', sans-serif;
        transition: background-color 0.3s ease, color 0.3s ease;
        overflow-x: hidden;
        width: 100vw;
        min-height: 100vh;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        touch-action: manipulation;
    }
    
    /* Mobile Font Size Adjustments */
    @media (max-width: 640px) {
        html {
            font-size: 14px;
        }
    }
    
    @media (max-width: 360px) {
        html {
            font-size: 13px;
        }
    }
    
    /* Color Modes */
    .color-mode-light {
        --bg-primary: #ffffff;
        --bg-secondary: #f8fafc;
        --text-primary: #1e293b;
        --text-secondary: #64748b;
        --text-muted: #6b7280;
        --border-color: #e2e8f0;
        --sidebar-bg: #065f46;
        --sidebar-text: #ffffff;
        --hover-bg: #f3f4f6;
        --card-bg: #ffffff;
        --header-bg: #ffffff;
        --input-bg: #ffffff;
        --input-border: #d1d5db;
    }
    
    .color-mode-dark {
        --bg-primary: #111827;
        --bg-secondary: #1f2937;
        --text-primary: #f9fafb;
        --text-secondary: #d1d5db;
        --text-muted: #9ca3af;
        --border-color: #374151;
        --sidebar-bg: #065f46;
        --sidebar-text: #ffffff;
        --hover-bg: #374151;
        --card-bg: #1f2937;
        --header-bg: #1f2937;
        --input-bg: #374151;
        --input-border: #4b5563;
    }
    
    /* Apply color variables */
    .color-mode-light,
    .color-mode-dark {
        background-color: var(--bg-primary);
        color: var(--text-primary);
    }
    
    /* Sidebar Styles */
    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: 280px;
        max-width: 80vw;
        z-index: 1000;
        transform: translateX(-100%);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        background: linear-gradient(135deg, #065f46 0%, #047857 100%);
        color: white;
    }
    
    .sidebar.open {
        transform: translateX(0);
    }
    
    /* Main Content Container */
    .main-container {
        width: 100%;
        min-height: 100vh;
        position: relative;
        transition: all 0.3s ease;
    }
    
    /* Desktop sidebar open */
    .sidebar-open .main-container {
        margin-left: 280px;
        width: calc(100% - 280px);
    }
    
    /* Main content */
    .main-content {
        width: 100%;
        min-height: 100vh;
        overflow-x: hidden;
        padding: 0.5rem;
    }
    
    /* Mobile specific */
    @media (max-width: 1023px) {
        .sidebar {
            width: 280px;
            max-width: 85vw;
        }
        
        .sidebar-open .main-container {
            margin-left: 0 !important;
            width: 100% !important;
            transform: translateX(280px);
        }
        
        .sidebar-open .main-container::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(3px);
            z-index: 999;
            pointer-events: auto;
        }
        
        body.sidebar-open {
            overflow: hidden;
        }
        
        .main-content {
            padding: 0.5rem;
        }
    }
    
    /* Animation */
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    .notification-slide {
        animation: slideInRight 0.3s ease-out;
    }
    
    /* Form input focus effects */
    input:focus, select:focus, textarea:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.1);
    }
    
    /* Smooth transitions */
    .tab-content {
        transition: opacity 0.3s ease-in-out;
    }
    
    /* Desktop */
    @media (min-width: 1024px) {
        .sidebar {
            transform: translateX(0);
            width: 280px;
        }
        
        .main-container {
            margin-left: 280px;
            width: calc(100% - 280px);
        }
        
        .hamburger-menu {
            display: none !important;
        }
        
        .main-content {
            padding: 1rem 1.5rem;
        }
    }
    
    /* Hamburger Menu - FIXED */
    .hamburger-menu {
        width: 30px;
        height: 24px;
        position: relative;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 0;
        background: none;
        border: none;
        margin: 0;
    }
    
    .hamburger-menu span {
        display: block;
        height: 3px;
        width: 100%;
        background: currentColor;
        border-radius: 3px;
        transition: all 0.3s ease;
        margin: 0;
    }
    
    .hamburger-menu.active span:nth-child(1) {
        transform: translateY(10px) rotate(45deg);
    }
    
    .hamburger-menu.active span:nth-child(2) {
        opacity: 0;
    }
    
    .hamburger-menu.active span:nth-child(3) {
        transform: translateY(-11px) rotate(-45deg);
    }
    
    /* Sidebar Header - FIXED close button position */
    .sidebar-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem;
        border-bottom: 1px solid rgba(255,255,255,0.2);
    }
    
    .logo-container {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .close-sidebar-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: rgba(255,255,255,0.1);
        color: white;
        transition: all 0.2s ease;
    }
    
    .close-sidebar-btn:hover {
        background: rgba(255,255,255,0.2);
    }
    
    @media (min-width: 1024px) {
        .close-sidebar-btn {
            display: none;
        }
    }
    
    /* Sidebar items */
    .sidebar-item {
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
        padding: 0.75rem 1rem;
        margin: 0.25rem 0;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        color: white;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .sidebar-item i {
        width: 20px;
        text-align: center;
    }
    
    .sidebar-item:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }
    
    /* Active navigation item */
    .active-nav-item {
        background-color: rgba(255, 255, 255, 0.15);
        position: relative;
    }
    
    .active-nav-item::after {
        content: '';
        position: absolute;
        right: 0;
        top: 0;
        height: 100%;
        width: 3px;
        background: white;
    }
    
    /* Blinking animation for package expiry */
    @keyframes blink {
        0% { opacity: 1; }
        50% { opacity: 0.3; }
        100% { opacity: 1; }
    }
    
    .blink-warning {
        animation: blink 1.5s infinite;
    }
    
    .blink-critical {
        animation: blink 0.8s infinite;
    }
    
    /* Package indicator */
    .package-indicator {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.5rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    .package-critical {
        background-color: #fee2e2;
        color: #b91c1c;
    }
    
    .package-warning {
        background-color: #fef3c7;
        color: #92400e;
    }
    
    .package-good {
        background-color: #d1fae5;
        color: #065f46;
    }
    
    .color-mode-dark .package-critical {
        background-color: #7f1d1d;
        color: #fecaca;
    }
    
    .color-mode-dark .package-warning {
        background-color: #78350f;
        color: #fde68a;
    }
    
    .color-mode-dark .package-good {
        background-color: #064e3b;
        color: #a7f3d0;
    }
</style>
    @stack('styles')
</head>

@php
    // Helper function to get current user from any guard
    function getCurrentUser() {
        if (Auth::check()) {
            return Auth::user();
        }
        if (Auth::guard('mfanyakazi')->check()) {
            return Auth::guard('mfanyakazi')->user();
        }
        return null;
    }
    
    // Helper function to get current company
    function getCurrentCompany() {
        $user = getCurrentUser();
        if ($user && method_exists($user, 'company')) {
            return $user->company;
        }
        return null;
    }
    
    // Helper function to get package days left
    function getPackageDaysLeft() {
        $company = getCurrentCompany();
        if (!$company || !$company->package_end) {
            return 0;
        }
        $daysLeft = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($company->package_end), false);
        return max(0, ceil($daysLeft));
    }
    
    // Helper function to get package name
    function getPackageName() {
        $company = getCurrentCompany();
        return $company ? ($company->package ?? 'Free Trial') : 'Free Trial';
    }
    
    // Helper function to get package end date
    function getPackageEndDate() {
        $company = getCurrentCompany();
        if ($company && $company->package_end) {
            return \Carbon\Carbon::parse($company->package_end)->format('d/m/Y');
        }
        return 'N/A';
    }
    
    // Get current user and company
    $currentUser = getCurrentUser();
    $currentCompany = getCurrentCompany();
    $daysLeft = getPackageDaysLeft();
    $packageName = getPackageName();
    $packageEndDate = getPackageEndDate();
    $userName = $currentUser ? $currentUser->name : 'Boss';
    $userRole = $currentUser ? ($currentUser->role ?? 'Mfanyakazi') : 'Meneja';
    $userInitial = $userName ? substr($userName, 0, 1) : 'B';
    $companyId = $currentCompany ? $currentCompany->id : 'default';
    $companyName = $currentCompany ? $currentCompany->company_name : 'Default Company';
@endphp

<body class="no-scroll-x" 
      :class="[colorMode, sidebarOpen ? 'sidebar-open' : '']" 
      x-data="app()" 
      x-init="init()">
    
    <!-- Sidebar -->
    <aside class="sidebar flex flex-col shadow-xl"
           :class="{'open': sidebarOpen}">
        <!-- Logo Section - FIXED -->
        <div class="sidebar-header">
            <div class="logo-container">
                <img src="https://test.mauzosheet.com/assets/images/apple-icon.gif" 
                     alt="Mauzo Logo" 
                     class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg">
                <div>
                    <div class="font-bold text-base sm:text-lg">MAUZO</div>
                    <div class="text-xs text-green-200">Boss System</div>
                </div>
            </div>
            <button @click="closeSidebar()" 
                    class="close-sidebar-btn lg:hidden">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 p-2 sm:p-3 space-y-1 overflow-y-auto scrollbar-thin">
            @include('partials.navigation')
        </nav>
        
        <!-- Sidebar Footer -->
        <div class="p-3 border-t border-green-700">
            <div class="text-xs text-green-200 text-center">
                {{ now()->format('d/m/Y H:i') }}
            </div>
        </div>
    </aside>

    <!-- Main Content Container -->
    <div class="main-container">
        <div class="main-content flex flex-col min-h-screen">
            <!-- Header with Hamburger Menu - FIXED -->
            <header class="sticky top-0 z-30 shadow-sm border-b px-3 sm:px-4 py-3 header-bg border-color">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <!-- Hamburger Menu - FIXED lines -->
                        <button class="hamburger-menu lg:hidden" 
                                :class="{'active': sidebarOpen}"
                                @click="toggleSidebar()"
                                aria-label="Toggle Menu">
                            <span :class="colorMode === 'color-mode-light' ? 'bg-gray-800' : 'bg-white'"></span>
                            <span :class="colorMode === 'color-mode-light' ? 'bg-gray-800' : 'bg-white'"></span>
                            <span :class="colorMode === 'color-mode-light' ? 'bg-gray-800' : 'bg-white'"></span>
                        </button>
                        
                        <!-- Page Title -->
                        <div>
                            <h1 class="text-lg sm:text-xl font-bold text-primary">@yield('page-title', 'Dashboard')</h1>
                            <p class="text-xs sm:text-sm text-secondary">@yield('page-subtitle', 'Karibu tena, Meneja!')</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-1 sm:gap-2">
                        <!-- Package Days Remaining with Email Icon -->
                        <div x-data="packageRemaining()" x-init="initPackageRemaining()" class="relative">
                            <button @click="fetchPackageInfo()" 
                                    class="relative p-2 transition text-secondary hover:text-green-600 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700"
                                    :class="{
                                        'blink-critical': daysLeft <= 5 && daysLeft > 0,
                                        'blink-warning': daysLeft > 5 && daysLeft <= 10
                                    }"
                                    aria-label="Package Days Remaining">
                                <!-- Email Icon -->
                                <i class="fas fa-envelope text-lg"></i>
                                <template x-if="daysLeft <= 5 && daysLeft > 0">
                                    <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full animate-ping"></span>
                                </template>
                            </button>

                            <!-- Package Info Tooltip -->
                            <div x-show="showPackageInfo"
                                 @click.away="showPackageInfo = false"
                                 x-cloak
                                 x-transition
                                 class="absolute right-0 mt-2 w-64 rounded-lg shadow-lg p-3 z-40 card-bg border border-color">
                                <div class="text-center">
                                    <div class="text-sm font-medium text-primary mb-2">Taarifa ya Package</div>
                                    <div class="mb-2">
                                        <span class="text-xs text-secondary">Package:</span>
                                        <span class="ml-1 text-sm font-semibold text-primary" x-text="packageName"></span>
                                    </div>
                                    <div class="mb-3">
                                        <span class="text-xs text-secondary">Siku Zilizobaki:</span>
                                        <div class="mt-1">
                                            <span class="text-2xl font-bold" 
                                                  :class="{
                                                      'text-red-600': daysLeft <= 5,
                                                      'text-amber-600': daysLeft > 5 && daysLeft <= 10,
                                                      'text-green-600': daysLeft > 10
                                                  }" 
                                                  x-text="daysLeft"></span>
                                            <span class="text-xs text-secondary ml-1">siku</span>
                                        </div>
                                    </div>
                                    <div class="text-xs p-2 rounded mb-2" 
                                         :class="{
                                             'package-critical': daysLeft <= 5,
                                             'package-warning': daysLeft > 5 && daysLeft <= 10,
                                             'package-good': daysLeft > 10
                                         }">
                                        <i class="fas mr-1" :class="{
                                            'fa-exclamation-triangle': daysLeft <= 5,
                                            'fa-clock': daysLeft > 5 && daysLeft <= 10,
                                            'fa-check-circle': daysLeft > 10
                                        }"></i>
                                        <span x-text="getPackageMessage()"></span>
                                    </div>
                                    <div class="text-xs text-secondary border-t border-color pt-2">
                                        Itaisha: <span class="font-medium" x-text="packageEndDate"></span>
                                    </div>
                                    <template x-if="daysLeft <= 5 && daysLeft > 0">
                                        <div class="mt-2 pt-2 border-t border-color">
                                            <a href="{{ route('payment.package.selection') }}" 
                                               class="block w-full bg-gradient-to-r from-amber-500 to-orange-500 text-white text-xs font-medium py-2 px-3 rounded hover:from-amber-600 hover:to-orange-600 transition">
                                                <i class="fas fa-shopping-cart mr-1"></i>
                                                Lipa Sasa
                                            </a>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Alert Dropdown -->
                        <div x-data="alertDropdown()" x-init="initAlert()" class="relative">
                            <button @click="toggleAlert()"
                                class="relative p-2 transition text-secondary hover:text-green-600 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700"
                                aria-label="Notifications">
                                <i class="fas fa-bell text-lg"></i>
                                <template x-if="lowStockCount > 0">
                                    <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                                </template>
                            </button>

                            <div x-show="openPro"
                                 @click.away="openPro = false"
                                 x-cloak
                                 x-transition
                                 class="absolute right-0 mt-2 w-72 sm:w-80 rounded-lg shadow-lg p-0 overflow-hidden z-40 card-bg border border-color">
                                <!-- Alert header -->
                                <div class="px-3 py-2 bg-gradient-to-r from-green-600 to-green-500 text-white font-medium">
                                    <div class="flex justify-between items-center">
                                        <span>Bidhaa Zinazokaribia Kuisha</span>
                                        <span x-text="lowStockCount" class="bg-white text-green-600 text-xs px-2 py-1 rounded-full"></span>
                                    </div>
                                </div>
                                
                                <!-- Low stock products list -->
                                <div class="max-h-64 overflow-y-auto">
                                    <template x-if="lowStockProducts.length > 0">
                                        <div>
                                            <template x-for="product in lowStockProducts" :key="product.id">
                                                <div class="p-3 border-b border-color hover-bg">
                                                    <div class="flex justify-between items-start">
                                                        <div>
                                                            <div class="font-medium text-primary" x-text="product.jina"></div>
                                                            <div class="text-xs text-secondary mt-1">
                                                                <span x-text="product.aina"></span>
                                                                <span x-show="product.kipimo" class="ml-1">• <span x-text="product.kipimo"></span></span>
                                                            </div>
                                                        </div>
                                                        <div class="text-right">
                                                            <div class="text-sm font-bold"
                                                                 :class="product.idadi <= 3 ? 'text-red-600' : 'text-amber-600'"
                                                                 x-text="product.idadi"></div>
                                                        </div>
                                                    </div>
                                                    <div class="mt-1 text-xs" 
                                                         :class="product.idadi <= 3 ? 'text-red-600' : 'text-amber-600'">
                                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                                        <span x-text="getStockMessage(product.idadi)"></span>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                    <template x-if="lowStockProducts.length === 0">
                                        <div class="py-6 text-center">
                                            <i class="fas fa-check-circle text-green-500 text-2xl mb-2"></i>
                                            <p class="text-sm text-primary">Hakuna bidhaa zinazokaribia kuisha</p>
                                            <p class="text-xs text-secondary mt-1">Bidhaa zote zina idadi ya kutosha</p>
                                        </div>
                                    </template>
                                </div>
                                
                                <!-- Footer -->
                                <div class="px-3 py-2 border-t border-color bg-secondary">
                                    <a href="{{ route('bidhaa.index') }}" 
                                       class="block text-center text-sm font-medium text-green-600 hover:text-green-700">
                                        <i class="fas fa-list mr-1"></i> Angalia Bidhaa Zote
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- User Profile -->
                        <div class="relative" x-data="{ profileOpen: false }">
                            <button @click="profileOpen = !profileOpen" 
                                    class="flex items-center gap-2 focus:outline-none">
                                <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-r from-green-600 to-green-500 rounded-full flex items-center justify-center text-white font-bold shadow-md">
                                    {{ $userInitial }}
                                </div>
                                <div class="hidden sm:block text-left">
                                    <div class="text-sm font-medium text-primary">{{ $userName }}</div>
                                    <div class="text-xs text-secondary">{{ $userRole }}</div>
                                </div>
                                <i class="fas fa-chevron-down text-xs text-secondary hidden sm:block"></i>
                            </button>
                            
                            <!-- Profile Dropdown -->
                            <div x-show="profileOpen" 
                                 @click.away="profileOpen = false" 
                                 x-cloak
                                 x-transition
                                 class="absolute right-0 mt-2 w-40 sm:w-48 rounded-lg shadow-lg z-50 py-1 card-bg border border-color">
                                <a href="{{ route('password.change') }}" 
                                   class="block px-3 py-2 text-xs sm:text-sm text-primary hover-bg">
                                    <i class="fas fa-key mr-2 w-4"></i>Badili Neno Siri
                                </a>
                                <a href="{{ route('company.info') }}" 
                                   class="block px-3 py-2 text-xs sm:text-sm text-primary hover-bg">
                                    <i class="fas fa-building mr-2 w-4"></i>Taarifa ya Kampuni
                                </a>
                                <div class="border-t my-1 border-color"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" 
                                            class="w-full text-left px-3 py-2 text-xs sm:text-sm text-primary hover-bg">
                                        <i class="fas fa-sign-out-alt mr-2 w-4"></i>Toka
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto p-2 sm:p-4 scrollbar-thin content-inner bg-secondary">
                <div class="max-w-full overflow-x-hidden">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Color Mode Toggle -->
    <div class="color-mode-toggle" x-data="{ colorMenuOpen: false }">
        <button 
            @click="colorMenuOpen = !colorMenuOpen"
            class="color-mode-btn shadow-lg"
            title="Badili Mwonekano"
            aria-label="Change Appearance"
        >
            <i class="fas" :class="colorMode === 'color-mode-light' ? 'fa-sun' : 'fa-moon'"></i>
        </button>
        
        <div 
            x-show="colorMenuOpen" 
            @click.away="colorMenuOpen = false"
            x-transition
            x-cloak
            class="color-mode-menu"
        >
            <div 
                class="color-mode-option"
                @click="changeColorMode('light'); colorMenuOpen = false"
            >
                <div class="w-5 h-5 rounded-full bg-white border-2 border-gray-300"></div>
                <span>Mwanga</span>
                <i x-show="colorMode === 'color-mode-light'" class="fas fa-check ml-auto text-green-600"></i>
            </div>
            <div 
                class="color-mode-option"
                @click="changeColorMode('dark'); colorMenuOpen = false"
            >
                <div class="w-5 h-5 rounded-full bg-gray-800 border-2 border-gray-600"></div>
                <span>Giza</span>
                <i x-show="colorMode === 'color-mode-dark'" class="fas fa-check ml-auto text-green-600"></i>
            </div>
        </div>
    </div>

    <script>
    function app() {
        return {
            sidebarOpen: false,
            colorMode: 'color-mode-light',
            
            init() {
                // Load saved color mode
                const savedMode = localStorage.getItem('colorMode');
                if (savedMode && (savedMode === 'color-mode-light' || savedMode === 'color-mode-dark')) {
                    this.colorMode = savedMode;
                } else {
                    this.colorMode = 'color-mode-light';
                    localStorage.setItem('colorMode', this.colorMode);
                }
                
                // Apply color mode to body
                document.body.className = document.body.className
                    .replace(/color-mode-(light|dark)/g, '')
                    .trim();
                document.body.classList.add(this.colorMode);
                
                // Set sidebar state based on screen size
                const isMobile = window.innerWidth < 1024;
                this.sidebarOpen = !isMobile;
                
                // Handle resize
                window.addEventListener('resize', () => {
                    const newIsMobile = window.innerWidth < 1024;
                    if (!newIsMobile && !this.sidebarOpen) {
                        this.sidebarOpen = true;
                    } else if (newIsMobile && this.sidebarOpen) {
                        this.sidebarOpen = false;
                    }
                });
                
                // Close sidebar with Escape key
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && this.sidebarOpen && window.innerWidth < 1024) {
                        this.closeSidebar();
                    }
                });
            },
            
            toggleSidebar() {
                this.sidebarOpen = !this.sidebarOpen;
            },
            
            closeSidebar() {
                this.sidebarOpen = false;
            },
            
            changeColorMode(mode) {
                const newMode = `color-mode-${mode}`;
                this.colorMode = newMode;
                localStorage.setItem('colorMode', newMode);
                
                // Update body class
                document.body.className = document.body.className
                    .replace(/color-mode-(light|dark)/g, '')
                    .trim();
                document.body.classList.add(newMode);
            }
        }
    }
    
    // Package remaining functionality with email notifications
    function packageRemaining() {
        return {
            showPackageInfo: false,
            daysLeft: {{ $daysLeft }},
            packageName: '{{ $packageName }}',
            packageEndDate: '{{ $packageEndDate }}',
            notificationsSent: false,
            companyId: '{{ $companyId }}',
            
            initPackageRemaining() {
                this.calculateDaysLeft();
                
                // Check if we need to send email notification
                if (this.daysLeft <= 5 && this.daysLeft > 0 && !this.notificationsSent) {
                    // Check if we haven't sent notification in last 24 hours
                    this.sendEmailNotification();
                }
                
                // Refresh every hour
                setInterval(() => {
                    this.calculateDaysLeft();
                }, 60 * 60 * 1000);
            },
            
            calculateDaysLeft() {
                // Days left is already calculated from PHP
                // This method is kept for future real-time updates
            },
            
            fetchPackageInfo() {
                fetch('/api/package-info')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.daysLeft = data.days_left;
                            this.packageName = data.package_name;
                            this.packageEndDate = data.end_date;
                        }
                        this.showPackageInfo = true;
                        
                        // Auto-hide after 5 seconds
                        setTimeout(() => {
                            this.showPackageInfo = false;
                        }, 5000);
                    })
                    .catch(error => {
                        console.error('Error fetching package info:', error);
                        this.showPackageInfo = true;
                    });
            },
            
            sendEmailNotification() {
                fetch('/api/send-package-email', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        days_left: this.daysLeft,
                        package_name: this.packageName,
                        company_id: this.companyId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('Email notification sent successfully');
                        this.notificationsSent = true;
                        
                        // Show success message in tooltip
                        this.showNotification('Email imetumwa!', 'success');
                    }
                })
                .catch(error => console.error('Error sending email:', error));
            },
            
            getPackageMessage() {
                if (this.daysLeft <= 0) {
                    return 'Package imekwisha! Bonyeza kulipa.';
                } else if (this.daysLeft <= 5) {
                    return `Siku ${this.daysLeft} zimesalia! Lipa sasa.`;
                } else if (this.daysLeft <= 10) {
                    return `Siku ${this.daysLeft} zimesalia.`;
                } else {
                    return 'Package inafanya kazi vizuri.';
                }
            },
            
            showNotification(message, type) {
                // You can implement a toast notification here
                console.log(message, type);
            }
        }
    }
    
    // Alert dropdown functionality
    function alertDropdown() {
        return {
            openPro: false,
            lowStockProducts: [],
            lowStockCount: 0,
            companyId: '{{ $companyId }}',
            
            initAlert() {
                this.fetchLowStockProducts();
                
                // Refresh every 5 minutes
                setInterval(() => {
                    this.fetchLowStockProducts();
                }, 5 * 60 * 1000);
            },
            
            fetchLowStockProducts() {
                fetch(`/api/low-stock-products?company_id=${this.companyId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.lowStockProducts = data.products || [];
                            this.lowStockCount = data.count || 0;
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching low stock products:', error);
                        this.lowStockProducts = [];
                        this.lowStockCount = 0;
                    });
            },
            
            toggleAlert() {
                this.openPro = !this.openPro;
                if (this.openPro) {
                    this.fetchLowStockProducts();
                }
            },
            
            getStockMessage(idadi) {
                if (idadi <= 1) {
                    return 'Bidhaa karibu imekwisha kabisa!';
                } else if (idadi <= 5) {
                    return 'Bidhaa imekaribia kuisha!';
                } else {
                    return 'Bidhaa inakaribia kuisha';
                }
            }
        }
    }
    </script>

    @stack('scripts')
</body>
</html>