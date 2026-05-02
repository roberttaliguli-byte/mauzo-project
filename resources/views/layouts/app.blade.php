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
    /* Custom Styles */
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
        background-color: #f8fafc;
        color: #1e293b;
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
    
    /* Sidebar Styles */
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
    
    /* Mobile specific */
    @media (max-width: 1023px) {
        .sidebar {
            width: 280px;
            max-width: 85vw;
        }
        
        .sidebar-open .main-container {
            margin-left: 0 !important;
            width: 100% !important;
        }
        
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
    
    /* Header styles */
    .app-header {
        background-color: #ffffff;
        border-bottom: 1px solid #e2e8f0;
        position: sticky;
        top: 0;
        z-index: 50;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
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
        background: #1e293b;
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
    
    /* Sidebar items */
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
    
    /* Active navigation item */
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
    
    /* Package indicator */
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
    
    /* Dropdown menus */
    .dropdown-solid {
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2);
    }
    
    /* Auto-notification styles */
    .auto-notification {
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%) translateY(-100px);
        z-index: 9999;
        transition: transform 0.3s ease;
        min-width: 300px;
        max-width: 500px;
        width: auto;
    }
    
    .auto-notification.show {
        transform: translateX(-50%) translateY(0);
    }
    
    @media (max-width: 640px) {
        .auto-notification {
            min-width: 280px;
            max-width: 90vw;
            top: 10px;
        }
    }
    
    /* Utility classes */
    .text-primary {
        color: #1e293b;
    }
    
    .text-secondary {
        color: #475569;
    }
    
    .border-color {
        border-color: #e2e8f0;
    }
    
    .bg-secondary {
        background-color: #f8fafc;
    }
    
    .hover-bg:hover {
        background-color: #f1f5f9;
    }
    
    .card-bg {
        background-color: #ffffff;
    }
    
    .header-bg {
        background-color: #ffffff;
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
    $isExpired = $currentCompany && $currentCompany->package_end ? \Carbon\Carbon::parse($currentCompany->package_end)->isPast() : true;
@endphp

<body class="no-scroll-x" 
      x-data="app()" 
      x-init="init()">
    
<!-- Auto Notification - Shows every 10 minutes -->
<div x-data="autoNotification()" 
     x-init="initAutoNotification()"
     class="auto-notification"
     :class="{'show': isVisible}"
     x-show="isVisible"
     x-cloak>
    <div class="bg-white rounded-lg shadow-2xl border-l-4 overflow-hidden" 
         :class="{
             'border-red-500': daysLeft <= 5,
             'border-orange-500': daysLeft > 5 && daysLeft <= 10,
             'border-green-500': daysLeft > 10
         }">
        <div class="p-4">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0">
                    <i class="fas text-xl"
                       :class="{
                           'fa-exclamation-triangle text-red-500': daysLeft <= 5,
                           'fa-clock text-orange-500': daysLeft > 5 && daysLeft <= 10,
                           'fa-check-circle text-green-500': daysLeft > 10
                       }"></i>
                </div>
                <div class="flex-1">
                    <h4 class="font-semibold text-gray-800 text-sm sm:text-base">
                        Kumbusho la Kifurushi
                    </h4>
                    <p class="text-gray-600 text-xs sm:text-sm mt-1">
                        Kifurushi chako cha <span class="font-medium" x-text="packageName"></span> 
                        kina siku <span class="font-bold" :class="{'text-red-600': daysLeft <= 5, 'text-orange-600': daysLeft > 5 && daysLeft <= 10}" x-text="daysLeft"></span> 
                        zimesalia kabla ya kuisha
                    </p>
                    <div class="mt-3 flex gap-2">
                        <button @click="goToPayment()" 
                                class="flex-1 bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-700 hover:to-orange-700 text-white font-medium py-2 px-3 rounded-lg transition text-xs sm:text-sm">
                            <i class="fas fa-shopping-cart mr-1"></i>
                            Renew / Lipa Sasa
                        </button>
                        <button @click="dismiss()" 
                                class="px-3 py-2 text-gray-500 hover:text-gray-700 text-xs sm:text-sm">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Progress bar -->
        <div class="h-1 bg-gray-100">
            <div class="h-full transition-all duration-[5000ms] linear"
                 :class="{
                     'bg-red-500': daysLeft <= 5,
                     'bg-orange-500': daysLeft > 5 && daysLeft <= 10,
                     'bg-green-500': daysLeft > 10
                 }"
                 x-bind:style="{ width: progressWidth }"></div>
        </div>
    </div>
</div>

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
                                <span></span>
                                <span></span>
                                <span></span>
                            </button>
                            
                            <!-- Page Title -->
                            <div>
                                <h1 class="text-lg sm:text-xl font-bold text-primary">@yield('page-title', 'Dashboard')</h1>
                                <p class="text-xs sm:text-sm text-secondary">@yield('page-subtitle', 'Karibu tena, Meneja!')</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-1 sm:gap-2">
                            <!-- Package Days Remaining Indicator -->
                            <div x-data="packageRemaining()" x-init="initPackageRemaining()" class="relative">
                                <button @click="fetchPackageInfo()" 
                                        class="relative p-2 transition text-secondary hover:text-green-600 rounded-full hover:bg-gray-100"
                                        :class="{
                                            'blink-critical': daysLeft <= 5 && daysLeft > 0,
                                            'blink-warning': daysLeft > 5 && daysLeft <= 10
                                        }"
                                        aria-label="Package Days Remaining">
                                    <i class="fas fa-calendar-alt text-lg"></i>
                                    <span class="absolute -top-1 -right-1 text-[10px] font-bold bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center"
                                          x-show="daysLeft > 0 && daysLeft <= 30"
                                          x-text="daysLeft"></span>
                                    <span class="absolute -top-1 -right-1 w-2 h-2 bg-red-500 rounded-full animate-ping"
                                          x-show="daysLeft <= 5 && daysLeft > 0"></span>
                                </button>

                                <!-- Package Info Tooltip -->
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
                                        <div class="mt-2 pt-2 border-t border-color">
                                            <a href="{{ route('payment.package.selection') }}" 
                                               class="block w-full bg-gradient-to-r from-amber-500 to-orange-500 text-white text-xs font-medium py-2 px-3 rounded hover:from-amber-600 hover:to-orange-600 transition">
                                                <i class="fas fa-shopping-cart mr-1"></i>
                                                Lipa / Renew Sasa
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Alert Dropdown -->
                            <div x-data="alertDropdown()" x-init="initAlert()" class="relative">
                                <button @click="toggleAlert()"
                                    class="relative p-2 transition text-secondary hover:text-green-600 rounded-full hover:bg-gray-100"
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
                                
                                <!-- Profile Dropdown -->
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

    <script>
    function app() {
        return {
            sidebarOpen: false,
            
            init() {
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
            }
        }
    }
 // Auto Notification Component - Shows on first 5 page refreshes, respects 2-minute cooldown
function autoNotification() {
    return {
        isVisible: false,
        daysLeft: {{ $daysLeft }},
        packageName: '{{ $packageName }}',
        packageEndDate: '{{ $packageEndDate }}',
        progressWidth: '100%',
        timeoutId: null,
        refreshCount: 0,
        maxRefreshes: 5,
        lastRefreshTime: null,
        cooldownMinutes: 2,
        
        initAutoNotification() {
            this.calculateDaysLeft();
            
            // Load saved state from localStorage
            this.loadSavedState();
            
            // Check if we should show notification on this refresh
            if (this.daysLeft <= 30 && this.daysLeft > 0) {
                this.checkAndShowOnRefresh();
            }
            
            // Update days left every minute
            setInterval(() => {
                this.calculateDaysLeft();
            }, 60 * 1000);
        },
        
        loadSavedState() {
            // Load refresh count from localStorage
            const savedCount = localStorage.getItem('package_notification_refresh_count');
            if (savedCount !== null) {
                this.refreshCount = parseInt(savedCount);
            } else {
                this.refreshCount = 0;
            }
            
            // Load last refresh time
            const savedLastTime = localStorage.getItem('package_notification_last_refresh');
            if (savedLastTime !== null) {
                this.lastRefreshTime = parseInt(savedLastTime);
            } else {
                this.lastRefreshTime = null;
            }
            
            console.log(`Current refresh count: ${this.refreshCount} of ${this.maxRefreshes}`);
        },
        
        saveState() {
            localStorage.setItem('package_notification_refresh_count', this.refreshCount.toString());
            localStorage.setItem('package_notification_last_refresh', this.lastRefreshTime.toString());
            localStorage.setItem('package_notification_days_left', this.daysLeft.toString());
        },
        
        resetState() {
            this.refreshCount = 0;
            this.lastRefreshTime = null;
            localStorage.removeItem('package_notification_refresh_count');
            localStorage.removeItem('package_notification_last_refresh');
            this.saveState();
            console.log('Notification state reset');
        },
        
        checkAndShowOnRefresh() {
            const now = Date.now();
            const twoMinutesInMs = this.cooldownMinutes * 60 * 1000;
            
            // Check if cooldown has passed since last refresh
            let canShow = false;
            
            if (this.lastRefreshTime === null) {
                // First refresh ever
                canShow = true;
            } else {
                const timeSinceLastRefresh = now - this.lastRefreshTime;
                
                if (timeSinceLastRefresh >= twoMinutesInMs) {
                    // Cooldown passed, reset counter and show
                    console.log('Cooldown passed (2 minutes), resetting counter');
                    this.refreshCount = 0;
                    canShow = true;
                } else if (this.refreshCount < this.maxRefreshes) {
                    // Still within first 5 refreshes and within cooldown
                    console.log(`Refresh ${this.refreshCount + 1} of ${this.maxRefreshes} within cooldown period`);
                    canShow = true;
                } else {
                    // Exceeded max refreshes within cooldown period
                    console.log(`Maximum refreshes (${this.maxRefreshes}) reached within 2 minutes. Waiting for cooldown.`);
                    canShow = false;
                }
            }
            
            if (canShow) {
                // Increment refresh count
                this.refreshCount++;
                this.lastRefreshTime = now;
                this.saveState();
                
                console.log(`Showing notification on refresh ${this.refreshCount} of ${this.maxRefreshes}`);
                
                // Show the notification
                setTimeout(() => {
                    this.showNotification();
                }, 500); // Small delay to ensure page is loaded
            } else {
                console.log('Notification not shown - cooldown active or max refreshes reached');
            }
        },
        
        calculateDaysLeft() {
            // Fetch latest package info
            fetch('/api/package-info')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const oldDaysLeft = this.daysLeft;
                        this.daysLeft = data.days_left;
                        this.packageName = data.package_name;
                        this.packageEndDate = data.end_date;
                        
                        // If package was renewed (days left increased significantly), reset state
                        if (oldDaysLeft > 0 && this.daysLeft > oldDaysLeft + 10) {
                            console.log('Package renewed, resetting notification state');
                            this.resetState();
                        }
                        
                        // If package expired, reset state
                        if (this.daysLeft <= 0) {
                            this.resetState();
                            this.dismiss();
                        }
                        
                        this.saveState();
                    }
                })
                .catch(error => console.error('Error fetching package info:', error));
        },
        
        showNotification() {
            // Don't show if already showing
            if (this.isVisible) return;
            
            // Don't show if days left > 30 or <= 0
            if (this.daysLeft > 30 || this.daysLeft <= 0) return;
            
            this.isVisible = true;
            this.progressWidth = '100%';
            
            // Start progress bar animation
            setTimeout(() => {
                this.progressWidth = '0%';
            }, 100);
            
            // Auto hide after 5 seconds
            if (this.timeoutId) {
                clearTimeout(this.timeoutId);
            }
            this.timeoutId = setTimeout(() => {
                this.dismiss();
            }, 5000);
        },
        
        dismiss() {
            this.isVisible = false;
            if (this.timeoutId) {
                clearTimeout(this.timeoutId);
                this.timeoutId = null;
            }
        },
        
        goToPayment() {
            this.dismiss();
            // Reset state after payment
            this.resetState();
            window.location.href = '{{ route("payment.package.selection") }}';
        }
    }
}
    
    // Package remaining functionality
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