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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
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
    /* Custom Styles - NO TRANSPARENCY */
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
        width: 100%;
        min-height: 100vh;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
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
    
    /* Color Modes - SOLID BACKGROUNDS, NO TRANSPARENCY */
    .color-mode-light {
        --bg-primary: #ffffff;
        --bg-secondary: #f8fafc;
        --text-primary: #1e293b;
        --text-secondary: #475569;
        --text-muted: #64748b;
        --border-color: #e2e8f0;
        --sidebar-bg: #065f46;
        --sidebar-text: #ffffff;
        --sidebar-hover: #047857;
        --hover-bg: #f1f5f9;
        --card-bg: #ffffff;
        --header-bg: #ffffff;
        --input-bg: #ffffff;
        --input-border: #cbd5e1;
        --shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .color-mode-dark {
        --bg-primary: #0f172a;
        --bg-secondary: #1e293b;
        --text-primary: #f1f5f9;
        --text-secondary: #cbd5e6;
        --text-muted: #94a3b8;
        --border-color: #334155;
        --sidebar-bg: #065f46;
        --sidebar-text: #ffffff;
        --sidebar-hover: #047857;
        --hover-bg: #334155;
        --card-bg: #1e293b;
        --header-bg: #1e293b;
        --input-bg: #334155;
        --input-border: #475569;
        --shadow: 0 1px 3px rgba(0,0,0,0.3);
    }
    
    /* Apply color variables */
    .color-mode-light,
    .color-mode-dark {
        background-color: var(--bg-primary);
        color: var(--text-primary);
    }
    
    /* Sidebar Styles - SOLID BACKGROUND */
    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: 280px;
        max-width: 85vw;
        z-index: 1000;
        transform: translateX(-100%);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.15);
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        background: #065f46; /* Solid fallback */
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
        transition: margin-left 0.3s ease;
    }
    
    /* Desktop sidebar open */
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
        
        .close-sidebar-btn {
            display: none;
        }
    }
    
    /* Main content */
    .main-content {
        width: 100%;
        min-height: 100vh;
        overflow-x: hidden;
    }
    
    /* Mobile specific - SOLID OVERLAY (no transparency/blur) */
    @media (max-width: 1023px) {
        .sidebar {
            width: 280px;
            max-width: 85vw;
        }
        
        .sidebar-open .main-container {
            margin-left: 0 !important;
            width: 100% !important;
        }
        
        /* SOLID overlay - NO BLUR, NO TRANSPARENCY */
        .sidebar-open .main-container::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: #000000;
            opacity: 0.7;
            z-index: 999;
            pointer-events: auto;
        }
        
        body.sidebar-open {
            overflow: hidden;
        }
        
        .main-content {
            padding: 0;
        }
    }
    
    @media (min-width: 1024px) {
        .main-content {
            padding: 0;
        }
    }
    
    /* Header styles - SOLID */
    .app-header {
        background-color: var(--header-bg);
        border-bottom: 1px solid var(--border-color);
        position: sticky;
        top: 0;
        z-index: 50;
        box-shadow: var(--shadow);
    }
    
    /* Hamburger Menu */
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
    
    /* Sidebar Header */
    .sidebar-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem;
        border-bottom: 1px solid rgba(255,255,255,0.2);
        background: #065f46;
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
        background: rgba(255,255,255,0.15);
        color: white;
        transition: all 0.2s ease;
    }
    
    .close-sidebar-btn:hover {
        background: rgba(255,255,255,0.25);
    }
    
    /* Sidebar items - SOLID hover */
    .sidebar-item {
        position: relative;
        transition: all 0.2s ease;
        padding: 0.75rem 1rem;
        margin: 0.25rem 0.5rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        color: white;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        background: transparent;
    }
    
    .sidebar-item i {
        width: 20px;
        text-align: center;
    }
    
    .sidebar-item:hover {
        background-color: rgba(255, 255, 255, 0.12);
    }
    
    /* Active navigation item - SOLID */
    .active-nav-item {
        background-color: rgba(255, 255, 255, 0.18);
        position: relative;
    }
    
    .active-nav-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 3px;
        height: 60%;
        background: white;
        border-radius: 0 2px 2px 0;
    }
    
    /* Blinking animation for package expiry */
    @keyframes blink {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    
    .blink-warning {
        animation: blink 1.5s infinite;
    }
    
    .blink-critical {
        animation: blink 0.8s infinite;
    }
    
    /* Package indicator - SOLID COLORS */
    .package-critical {
        background-color: #dc2626;
        color: white;
    }
    
    .package-warning {
        background-color: #f59e0b;
        color: white;
    }
    
    .package-good {
        background-color: #10b981;
        color: white;
    }
    
    .color-mode-dark .package-critical {
        background-color: #b91c1c;
        color: #fee2e2;
    }
    
    .color-mode-dark .package-warning {
        background-color: #d97706;
        color: #fef3c7;
    }
    
    .color-mode-dark .package-good {
        background-color: #059669;
        color: #d1fae5;
    }
    
    /* Dropdown menus - SOLID */
    .dropdown-solid {
        background-color: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 0.75rem;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2);
    }
    
    /* Color mode toggle button */
    .color-mode-toggle {
        position: fixed;
        bottom: 1.5rem;
        right: 1.5rem;
        z-index: 100;
    }
    
    .color-mode-btn {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background-color: var(--card-bg);
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        color: var(--text-primary);
    }
    
    .color-mode-btn:hover {
        transform: scale(1.05);
    }
    
    .color-mode-menu {
        position: absolute;
        bottom: 60px;
        right: 0;
        background-color: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 0.75rem;
        padding: 0.5rem;
        min-width: 140px;
        box-shadow: var(--shadow);
        z-index: 101;
    }
    
    .color-mode-option {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.5rem 1rem;
        cursor: pointer;
        border-radius: 0.5rem;
        transition: background-color 0.2s;
        color: var(--text-primary);
    }
    
    .color-mode-option:hover {
        background-color: var(--hover-bg);
    }
    
    /* Utility classes */
    .text-primary {
        color: var(--text-primary);
    }
    
    .text-secondary {
        color: var(--text-secondary);
    }
    
    .border-color {
        border-color: var(--border-color);
    }
    
    .bg-secondary {
        background-color: var(--bg-secondary);
    }
    
    .hover-bg:hover {
        background-color: var(--hover-bg);
    }
    
    .card-bg {
        background-color: var(--card-bg);
    }
    
    .header-bg {
        background-color: var(--header-bg);
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
    <aside class="sidebar flex flex-col"
           :class="{'open': sidebarOpen}">
        <!-- Logo Section -->
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
                    class="close-sidebar-btn">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 p-2 sm:p-3 overflow-y-auto">
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
            <!-- Header -->
            <header class="app-header">
                <div class="px-3 sm:px-4 py-3">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <!-- Hamburger Menu -->
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
                                    <i class="fas fa-envelope text-lg"></i>
                                    <template x-if="daysLeft <= 5 && daysLeft > 0">
                                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full animate-ping"></span>
                                    </template>
                                </button>

                                <!-- Package Info Tooltip - SOLID -->
                                <div x-show="showPackageInfo"
                                     @click.away="showPackageInfo = false"
                                     x-cloak
                                     x-transition
                                     class="absolute right-0 mt-2 w-64 rounded-lg shadow-lg p-3 z-40 dropdown-solid">
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
                                     class="absolute right-0 mt-2 w-72 sm:w-80 rounded-lg shadow-lg p-0 overflow-hidden z-40 dropdown-solid">
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
                                
                                <!-- Profile Dropdown - SOLID -->
                                <div x-show="profileOpen" 
                                     @click.away="profileOpen = false" 
                                     x-cloak
                                     x-transition
                                     class="absolute right-0 mt-2 w-40 sm:w-48 rounded-lg shadow-lg z-50 py-1 dropdown-solid">
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
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto p-2 sm:p-4 bg-secondary">
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
                const isDesktop = window.innerWidth >= 1024;
                this.sidebarOpen = isDesktop;
                
                // Handle resize
                window.addEventListener('resize', () => {
                    const nowDesktop = window.innerWidth >= 1024;
                    if (nowDesktop && !this.sidebarOpen) {
                        this.sidebarOpen = true;
                    } else if (!nowDesktop && this.sidebarOpen) {
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
                    this.sendEmailNotification();
                }
                
                // Refresh every hour
                setInterval(() => {
                    this.calculateDaysLeft();
                }, 60 * 60 * 1000);
            },
            
            calculateDaysLeft() {
                // Days left is already calculated from PHP
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