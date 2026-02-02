<!DOCTYPE html>
<html lang="sw">
<head>
    <!-- Add these meta tags in your head section (outside this tab) -->
    <meta name="company-id" content="{{ auth()->user()->company_id ?? 'default' }}">
    <meta name="company-name" content="{{ auth()->user()->company->company_name ?? 'Default Company' }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'DEMODAY')</title>
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
        transition: all 0.3s ease;
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
    
    /* Color Modes - Only Light and Dark */
    .color-mode-light {
        --bg-primary: #ffffff;
        --bg-secondary: #f8fafc;
        --text-primary: #1e293b;
        --text-secondary: #64748b;
        --border-color: #e2e8f0;
        --sidebar-bg: #065f46;
        --sidebar-text: #ffffff;
    }
    
    .color-mode-dark {
        --bg-primary: #1f2937;
        --bg-secondary: #374151;
        --text-primary: #f9fafb;
        --text-secondary: #d1d5db;
        --border-color: #4b5563;
        --sidebar-bg: #065f46;
        --sidebar-text: #ffffff;
    }
    
    /* Apply color variables */
    .color-mode-light {
        background-color: var(--bg-primary);
        color: var(--text-primary);
    }
    
    .color-mode-dark {
        background-color: var(--bg-primary);
        color: var(--text-primary);
    }
    
    /* Sidebar Styles - Mobile Optimized (Half screen) */
    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: 280px;
        max-width: 50vw; /* Changed to half screen on mobile */
        z-index: 1000;
        transform: translateX(-100%);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        background: linear-gradient(135deg, var(--sidebar-bg) 0%, #047857 100%);
        color: var(--sidebar-text);
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
    
    /* When sidebar is open on desktop */
    .sidebar-open .main-container {
        margin-left: 280px;
        width: calc(100% - 280px);
    }
    
    /* Ensure content fits within viewport */
    .main-content {
        width: 100%;
        min-height: 100vh;
        overflow-x: hidden;
        padding: 0.5rem;
    }
    
    /* Mobile specific adjustments (Half screen sidebar) */
    @media (max-width: 1023px) {
        .sidebar {
            width: 200px; /* Reduced width for half screen */
            max-width: 50vw;
        }
        
        .sidebar-open .main-container {
            margin-left: 0 !important;
            width: 100% !important;
            transform: translateX(50vw); /* Move content by half screen */
        }
        
        /* On mobile, overlay content when sidebar is open (lighter for half screen) */
        .sidebar-open .main-container::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.3); /* Lighter overlay */
            backdrop-filter: blur(2px); /* Slight blur effect */
            z-index: 999;
            pointer-events: auto;
        }
        
        /* Prevent body scroll when sidebar is open on mobile */
        body.sidebar-open {
            overflow: hidden;
        }
        
        .main-content {
            padding: 0.5rem;
        }
        
        /* Compact sidebar items for half screen */
        .sidebar-item {
            padding: 0.4rem 0.75rem !important;
            margin: 0.125rem 0;
            border-radius: 0.375rem;
            min-height: 36px; /* Smaller touch target for compact sidebar */
        }
        
        /* Reduce logo size for half screen */
        .logo-img {
            width: 28px !important;
            height: 28px !important;
        }
        
        .logo-text {
            font-size: 0.875rem !important;
        }
        
        .logo-subtext {
            font-size: 0.5rem !important;
        }
        
        /* Adjust sidebar footer for mobile */
        .sidebar .border-t {
            padding: 0.5rem;
        }
        
        .sidebar .text-xs {
            font-size: 0.625rem;
        }
        
        /* Smaller active dot for mobile */
        .active-nav-item::after {
            width: 2px !important;
        }
    }
    
    /* Tablet adjustments (Full sidebar) */
    @media (min-width: 641px) and (max-width: 1023px) {
        .sidebar {
            width: 280px;
            max-width: 300px;
        }
        
        .sidebar-open .main-container {
            transform: translateX(280px);
        }
        
        /* Restore normal sidebar items on tablet */
        .sidebar-item {
            padding: 0.75rem 1rem !important;
            min-height: 44px;
        }
        
        .logo-img {
            width: 32px !important;
            height: 32px !important;
        }
        
        .logo-text {
            font-size: 1rem !important;
        }
        
        .logo-subtext {
            font-size: 0.625rem !important;
        }
    }
    
    @media (min-width: 768px) and (max-width: 1023px) {
        .main-content {
            padding: 1rem;
        }
    }
    
    /* Desktop specific adjustments */
    @media (min-width: 1024px) {
        .sidebar {
            transform: translateX(0);
            max-width: 280px;
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
        
        /* Hide close button on desktop */
        .sidebar .fa-times {
            display: none;
        }
    }
    
    /* Large desktop */
    @media (min-width: 1536px) {
        .main-content {
            max-width: 1536px;
            margin: 0 auto;
        }
    }
    
    /* Extra small mobile devices (very small phones) */
    @media (max-width: 360px) {
        .sidebar {
            width: 180px; /* Even narrower for very small phones */
            max-width: 50vw;
        }
        
        .sidebar-open .main-container {
            transform: translateX(50vw);
        }
        
        .header-left {
            gap: 0.5rem;
        }
        
        .sidebar-item {
            padding: 0.35rem 0.5rem !important;
            font-size: 0.75rem;
            min-height: 32px;
        }
        
        .color-mode-btn {
            width: 40px;
            height: 40px;
            font-size: 0.9rem;
        }
        
        .color-mode-menu {
            min-width: 120px;
            font-size: 0.75rem;
        }
        
        /* Hide logo subtext on very small screens */
        .logo-subtext {
            display: none;
        }
    }
    
    /* Table responsive styles */
    .table-responsive {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        font-size: 0.875rem;
    }
    
    /* Mobile table adjustments */
    @media (max-width: 640px) {
        .table-responsive {
            font-size: 0.75rem;
        }
        
        .table-responsive td,
        .table-responsive th {
            padding: 0.25rem 0.5rem !important;
        }
    }
    
    /* Card responsive styles */
    .responsive-card {
        width: 100%;
        margin-bottom: 0.75rem;
        padding: 0.75rem;
    }
    
    @media (min-width: 640px) {
        .responsive-card {
            margin-bottom: 1rem;
            padding: 1rem;
        }
    }
    
    @media (min-width: 768px) {
        .responsive-card {
            margin-bottom: 1.5rem;
            padding: 1.5rem;
        }
    }
    
    /* Form responsive styles */
    .form-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
    
    @media (min-width: 640px) {
        .form-grid {
            gap: 1rem;
        }
    }
    
    @media (min-width: 768px) {
        .form-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    @media (min-width: 1024px) {
        .form-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    
    /* Hamburger Menu - Mobile optimized */
    .hamburger-menu {
        width: 24px;
        height: 18px;
        position: relative;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 0;
        background: none;
        border: none;
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
        background: currentColor;
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
    
    /* Sidebar items - Mobile optimized */
    .sidebar-item {
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
        padding: 0.75rem 1rem;
        margin: 0.25rem 0;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        color: white;
    }
    
    @media (min-width: 768px) {
        .sidebar-item {
            padding: 0.875rem 1rem;
            font-size: 0.9rem;
        }
    }
    
    @media (min-width: 1024px) {
        .sidebar-item {
            padding: 1rem 1.25rem;
            font-size: 0.95rem;
        }
    }
    
    .sidebar-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }
    
    .sidebar-item:hover::before {
        left: 100%;
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
    
    /* Scrollbar */
    .scrollbar-thin::-webkit-scrollbar {
        width: 3px;
    }
    
    .scrollbar-thin::-webkit-scrollbar-track {
        background: transparent;
    }
    
    .scrollbar-thin::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.3);
        border-radius: 2px;
    }
    
    .scrollbar-thin::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.5);
    }
    
    /* Ensure no horizontal scroll */
    .no-scroll-x {
        overflow-x: hidden !important;
    }
    
    /* Color Mode Toggle - Mobile optimized */
    .color-mode-toggle {
        position: fixed;
        bottom: 16px;
        right: 16px;
        z-index: 50;
    }
    
    @media (min-width: 768px) {
        .color-mode-toggle {
            bottom: 20px;
            right: 20px;
        }
    }
    
    .color-mode-btn {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        background: white;
        color: #065f46;
        font-size: 1rem;
    }
    
    @media (min-width: 768px) {
        .color-mode-btn {
            width: 50px;
            height: 50px;
            font-size: 1.1rem;
        }
    }
    
    .color-mode-menu {
        position: absolute;
        bottom: 52px;
        right: 0;
        background: white;
        border-radius: 10px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        padding: 10px;
        min-width: 140px;
        z-index: 51;
        font-size: 0.875rem;
    }
    
    /* Header adjustments - Mobile optimized */
    .header-left {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    /* Page title adjustments */
    .page-title {
        font-size: 1.125rem;
        font-weight: 700;
        line-height: 1.2;
    }
    
    .page-subtitle {
        font-size: 0.75rem;
        line-height: 1.2;
        margin-top: 0.125rem;
    }
    
    @media (min-width: 640px) {
        .page-title {
            font-size: 1.25rem;
        }
        
        .page-subtitle {
            font-size: 0.875rem;
        }
    }
    
    @media (min-width: 768px) {
        .page-title {
            font-size: 1.5rem;
        }
        
        .page-subtitle {
            font-size: 0.9rem;
        }
    }
    
    @media (min-width: 1024px) {
        .page-title {
            font-size: 1.75rem;
        }
        
        .page-subtitle {
            font-size: 1rem;
        }
    }
    
    /* Content width adjustment */
    .content-inner {
        width: 100%;
        max-width: 100%;
        overflow-x: hidden;
    }
    
    /* Button sizes for mobile */
    .btn-mobile {
        padding: 0.375rem 0.75rem;
        font-size: 0.75rem;
    }
    
    @media (min-width: 640px) {
        .btn-mobile {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }
    }
    
    /* Alert notification */
    .alert-dot {
        position: absolute;
        top: 2px;
        right: 2px;
        width: 6px;
        height: 6px;
        background-color: #ef4444;
        border-radius: 50%;
    }
    
    @media (min-width: 640px) {
        .alert-dot {
            top: 4px;
            right: 4px;
            width: 8px;
            height: 8px;
        }
    }
    
    /* Profile image size */
    .profile-img {
        width: 32px;
        height: 32px;
        font-size: 0.75rem;
    }
    
    @media (min-width: 640px) {
        .profile-img {
            width: 40px;
            height: 40px;
            font-size: 0.875rem;
        }
    }
    
    /* Logo size adjustments */
    .logo-img {
        width: 32px;
        height: 32px;
    }
    
    .logo-text {
        font-size: 1rem;
    }
    
    .logo-subtext {
        font-size: 0.625rem;
    }
    
    @media (min-width: 640px) {
        .logo-img {
            width: 40px;
            height: 40px;
        }
        
        .logo-text {
            font-size: 1.25rem;
        }
        
        .logo-subtext {
            font-size: 0.75rem;
        }
    }
    
    /* Input field sizes */
    input, select, textarea {
        font-size: 0.875rem !important;
        padding: 0.5rem 0.75rem !important;
    }
    
    @media (min-width: 640px) {
        input, select, textarea {
            font-size: 0.9rem !important;
            padding: 0.625rem 0.875rem !important;
        }
    }
    
    /* Icon sizes */
    .icon-sm {
        font-size: 0.875rem;
    }
    
    .icon-md {
        font-size: 1rem;
    }
    
    .icon-lg {
        font-size: 1.125rem;
    }
    
    /* Hide elements on very small screens */
    @media (max-width: 400px) {
        .hide-xs {
            display: none !important;
        }
    }
    
    /* Better touch targets */
    button, 
    a, 
    input[type="submit"],
    input[type="button"] {
        min-height: 44px;
        min-width: 44px;
    }
    
    /* Prevent text selection on interactive elements */
    button, 
    a {
        user-select: none;
        -webkit-user-select: none;
    }
    
    /* Smooth transitions for all interactive elements */
    * {
        transition: background-color 0.2s ease, 
                   color 0.2s ease, 
                   border-color 0.2s ease, 
                   transform 0.2s ease,
                   opacity 0.2s ease;
    }
    
    /* Additional improvements for mobile */
    @media (max-width: 640px) {
        /* Ensure sidebar items fit in half screen */
        .sidebar-item {
            max-width: 100%;
            overflow: hidden;
        }
        
        /* Prevent horizontal overflow */
        .sidebar {
            overflow-x: hidden;
        }
        
        /* Better touch targets for mobile */
        button.sidebar-item,
        a.sidebar-item {
            min-height: 36px; /* Slightly smaller for compact sidebar */
        }
    }
    
    /* Tablet specific adjustments */
    @media (min-width: 641px) and (max-width: 768px) {
        .sidebar {
            width: 250px; /* Slightly narrower on small tablets */
        }
        
        .sidebar-open .main-container {
            transform: translateX(250px);
        }
    }
    
    /* Alert Styles */
    .alert-low-stock {
        max-height: 300px;
        overflow-y: auto;
    }
    
    .low-stock-item {
        padding: 0.5rem;
        border-bottom: 1px solid #e5e7eb;
        transition: background-color 0.2s ease;
    }
    
    .low-stock-item:hover {
        background-color: #f9fafb;
    }
    
    .low-stock-item.dark:hover {
        background-color: #374151;
    }
    
    .stock-critical {
        color: #dc2626;
        font-weight: 600;
    }
    
    .stock-warning {
        color: #d97706;
        font-weight: 500;
    }
    
    .no-alerts {
        padding: 1rem;
        text-align: center;
        color: #6b7280;
        font-style: italic;
    }
</style>
    @stack('styles')
</head>

<body class="color-mode-light no-scroll-x" x-data="app()" :class="[colorMode, sidebarOpen ? 'sidebar-open' : '']" x-init="init()">
    
    <!-- Sidebar -->
    <aside class="sidebar flex flex-col shadow-xl"
           :class="{'open': sidebarOpen}">
        <!-- Logo Section -->
        <div class="p-3 sm:p-4 text-center border-b border-green-700">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <img src="https://test.mauzosheet.com/assets/images/apple-icon.gif" 
                         alt="Mauzo Logo" 
                         class="logo-img rounded-lg">
                    <div>
                        <div class="logo-text font-bold text-left">MAUZO</div>
                        <div class="logo-subtext text-green-200 text-left">Boss System</div>
                    </div>
                </div>
                <button @click="closeSidebar()" 
                        class="text-green-200 hover:text-white transition p-1 lg:hidden">
                    <i class="fas fa-times icon-lg"></i>
                </button>
            </div>
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
            <div class="mt-1 text-center lg:hidden">
                <button @click="closeSidebar()" 
                        class="text-green-200 hover:text-white text-xs transition px-2 py-1">
                    <i class="fas fa-chevron-left mr-1"></i> Funga Menu
                </button>
            </div>
        </div>
    </aside>

    <!-- Main Content Container -->
    <div class="main-container">
        <div class="main-content flex flex-col min-h-screen">
            <!-- Header with Hamburger Menu -->
            <header class="sticky top-0 z-30 shadow-sm border-b px-3 sm:px-4 py-2 sm:py-3" :class="{
                'bg-white border-gray-200': colorMode === 'color-mode-light',
                'bg-gray-800 border-gray-700': colorMode === 'color-mode-dark'
            }">
                <div class="flex justify-between items-center">
                    <div class="header-left">
                        <!-- Hamburger Menu - Visible only on mobile/tablet -->
                        <button class="hamburger-menu lg:hidden" 
                             :class="{'active': sidebarOpen}"
                             @click="toggleSidebar()"
                             aria-label="Toggle Menu">
                            <span :class="{
                                'bg-gray-800': colorMode === 'color-mode-light',
                                'bg-white': colorMode === 'color-mode-dark'
                            }"></span>
                            <span :class="{
                                'bg-gray-800': colorMode === 'color-mode-light',
                                'bg-white': colorMode === 'color-mode-dark'
                            }"></span>
                            <span :class="{
                                'bg-gray-800': colorMode === 'color-mode-light',
                                'bg-white': colorMode === 'color-mode-dark'
                            }"></span>
                        </button>
                        
                        <!-- Page Title -->
                        <div>
                            <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
                            <p class="page-subtitle" :class="{
                                'text-gray-600': colorMode === 'color-mode-light',
                                'text-gray-300': colorMode === 'color-mode-dark'
                            }">@yield('page-subtitle', 'Karibu tena, Meneja!')</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-1 sm:space-x-2">
                        <!-- Alert Dropdown -->
                        <div x-data="alertDropdown()" x-init="init()" class="relative">
                            <button @click="toggleAlert()"
                                class="relative p-1 sm:p-2 transition"
                                :class="{
                                    'text-gray-600 hover:text-green-600': colorMode === 'color-mode-light',
                                    'text-gray-300 hover:text-green-400': colorMode === 'color-mode-dark'
                                }"
                                aria-label="Notifications">
                                <i class="fas fa-bell icon-md"></i>
                                <!-- Show dot if there are low stock products -->
                                <template x-if="lowStockCount > 0">
                                    <span class="alert-dot"></span>
                                </template>
                            </button>

                            <div x-show="openPro"
                                 @click.away="openPro = false"
                                 x-cloak
                                 x-transition
                                 class="absolute right-0 mt-2 w-72 sm:w-80 rounded-lg shadow-lg p-0 overflow-hidden z-40"
                                 :class="{
                                    'bg-white border border-gray-200': colorMode === 'color-mode-light',
                                    'bg-gray-800 border border-gray-700': colorMode === 'color-mode-dark'
                                 }">
                                <!-- Alert header -->
                                <div class="px-3 py-2 bg-gradient-to-r from-green-600 to-green-500 text-white font-medium">
                                    <div class="flex justify-between items-center">
                                        <span>Bidhaa Zinazokaribia Kuisha</span>
                                        <span x-text="lowStockCount" class="bg-white text-green-600 text-xs px-2 py-1 rounded-full"></span>
                                    </div>
                                </div>
                                
                                <!-- Low stock products list -->
                                <div class="alert-low-stock max-h-64 overflow-y-auto">
                                    <template x-if="lowStockProducts.length > 0">
                                        <div>
                                            <template x-for="product in lowStockProducts" :key="product.id">
                                                <div class="low-stock-item px-3 py-2 border-b"
                                                     :class="{
                                                        'border-gray-100 hover:bg-gray-50': colorMode === 'color-mode-light',
                                                        'border-gray-700 hover:bg-gray-700': colorMode === 'color-mode-dark'
                                                     }">
                                                    <div class="flex justify-between items-start">
                                                        <div class="flex-1">
                                                            <div class="font-medium" x-text="product.jina"></div>
                                                            <div class="text-xs opacity-75 mt-1">
                                                                <span x-text="product.aina"></span> • 
                                                                <span x-text="product.kipimo"></span>
                                                            </div>
                                                        </div>
                                                        <div class="ml-2 text-right">
                                                            <div class="text-sm font-bold"
                                                                 :class="{
                                                                    'stock-critical': product.idadi <= 3,
                                                                    'stock-warning': product.idadi > 3 && product.idadi <= 5
                                                                 }"
                                                                 x-text="product.idadi + ' ' + (product.kipimo || '')"></div>
                                                            <div class="text-xs opacity-75 mt-1">
                                                                Imebaki: <span x-text="product.idadi"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mt-1 text-xs" 
                                                         :class="{
                                                            'text-red-600': product.idadi <= 3,
                                                            'text-amber-600': product.idadi > 3 && product.idadi <= 5
                                                         }">
                                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                                        <span x-text="getStockMessage(product.idadi)"></span>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                    <template x-if="lowStockProducts.length === 0">
                                        <div class="no-alerts py-4 text-center">
                                            <i class="fas fa-check-circle text-green-500 text-xl mb-2"></i>
                                            <p class="text-sm">Hakuna bidhaa zinazokaribia kuisha</p>
                                            <p class="text-xs opacity-75 mt-1">Bidhaa zote zina idadi ya kutosha</p>
                                        </div>
                                    </template>
                                </div>
                                
                                <!-- Footer with view all link -->
                                <div class="px-3 py-2 border-t"
                                     :class="{
                                        'border-gray-100 bg-gray-50': colorMode === 'color-mode-light',
                                        'border-gray-700 bg-gray-800': colorMode === 'color-mode-dark'
                                     }">
                                    <a href="{{ route('bidhaa.index') }}" 
                                       class="block text-center text-sm font-medium transition-colors"
                                       :class="{
                                        'text-green-600 hover:text-green-700': colorMode === 'color-mode-light',
                                        'text-green-400 hover:text-green-300': colorMode === 'color-mode-dark'
                                       }">
                                        <i class="fas fa-list mr-1"></i> Angalia Bidhaa Zote
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- User Profile -->
                        <div class="relative" x-data="{ profileOpen: false }">
                            <button @click="profileOpen = !profileOpen" 
                                    class="flex items-center space-x-1 sm:space-x-2 focus:outline-none">
                                <div class="profile-img bg-gradient-to-r from-green-600 to-green-500 rounded-full flex items-center justify-center text-white font-bold shadow-md">
                                    B
                                </div>
                                <div class="hidden sm:block text-left">
                                    <div class="text-sm font-medium" :class="{
                                        'text-gray-700': colorMode === 'color-mode-light',
                                        'text-gray-200': colorMode === 'color-mode-dark'
                                    }">Boss</div>
                                    <div class="text-xs" :class="{
                                        'text-gray-500': colorMode === 'color-mode-light',
                                        'text-gray-400': colorMode === 'color-mode-dark'
                                    }">Meneja</div>
                                </div>
                                <i class="fas fa-chevron-down text-xs sm:text-sm hidden sm:block"></i>
                            </button>
                            
                            <!-- Profile Dropdown -->
                            <div x-show="profileOpen" 
                                 @click.away="profileOpen = false" 
                                 x-cloak
                                 x-transition
                                 class="absolute right-0 mt-2 w-40 sm:w-48 rounded-lg shadow-lg z-50 py-1" :class="{
                                    'bg-white border border-gray-200': colorMode === 'color-mode-light',
                                    'bg-gray-800 border border-gray-700': colorMode === 'color-mode-dark'
                                 }">
                                <a href="{{ route('password.change') }}" 
                                   class="block px-3 py-1.5 text-xs sm:text-sm transition-colors" :class="{
                                    'text-gray-700 hover:bg-green-50 hover:text-green-700': colorMode === 'color-mode-light',
                                    'text-gray-200 hover:bg-gray-700 hover:text-green-400': colorMode === 'color-mode-dark'
                                   }">
                                    <i class="fas fa-key mr-1 sm:mr-2 icon-sm"></i>Badili Neno Siri
                                </a>
                                <a href="{{ route('company.info') }}" 
                                   class="block px-3 py-1.5 text-xs sm:text-sm transition-colors" :class="{
                                    'text-gray-700 hover:bg-green-50 hover:text-green-700': colorMode === 'color-mode-light',
                                    'text-gray-200 hover:bg-gray-700 hover:text-green-400': colorMode === 'color-mode-dark'
                                   }">
                                    <i class="fas fa-building mr-1 sm:mr-2 icon-sm"></i>Taarifa ya Kampuni
                                </a>
                                <div class="border-t my-1" :class="{
                                    'border-gray-100': colorMode === 'color-mode-light',
                                    'border-gray-700': colorMode === 'color-mode-dark'
                                }"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" 
                                            class="w-full text-left px-3 py-1.5 text-xs sm:text-sm transition-colors" :class="{
                                            'text-gray-700 hover:bg-green-50 hover:text-green-700': colorMode === 'color-mode-light',
                                            'text-gray-200 hover:bg-gray-700 hover:text-green-400': colorMode === 'color-mode-dark'
                                           }">
                                        <i class="fas fa-sign-out-alt mr-1 sm:mr-2 icon-sm"></i>Toka
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto p-2 sm:p-4 scrollbar-thin content-inner" :class="{
                'bg-gray-50': colorMode === 'color-mode-light',
                'bg-gray-900': colorMode === 'color-mode-dark'
            }">
                <!-- Responsive wrapper for content -->
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
            <i class="fas fa-palette"></i>
        </button>
        
        <div 
            x-show="colorMenuOpen" 
            @click.away="colorMenuOpen = false"
            x-transition
            x-cloak
            class="color-mode-menu"
        >
            <div 
                class="color-mode-option flex items-center gap-2 p-2 hover:bg-gray-100 rounded-lg cursor-pointer"
                @click="changeColorMode('light'); colorMenuOpen = false"
            >
                <div class="w-5 h-5 rounded-full bg-white border border-gray-300"></div>
                <span class="text-xs sm:text-sm">Mwanga</span>
            </div>
            <div 
                class="color-mode-option flex items-center gap-2 p-2 hover:bg-gray-100 rounded-lg cursor-pointer"
                @click="changeColorMode('dark'); colorMenuOpen = false"
            >
                <div class="w-5 h-5 rounded-full bg-gray-800"></div>
                <span class="text-xs sm:text-sm">Giza</span>
            </div>
        </div>
    </div>

    <script>
    function app() {
        return {
            sidebarOpen: false,
            colorMode: 'color-mode-light', // Default to light mode only
            
            init() {
                // Load saved color mode
                const savedMode = localStorage.getItem('colorMode');
                if (savedMode && (savedMode === 'color-mode-light' || savedMode === 'color-mode-dark')) {
                    this.colorMode = savedMode;
                } else {
                    // Default to light mode if no valid mode saved
                    this.colorMode = 'color-mode-light';
                    localStorage.setItem('colorMode', this.colorMode);
                }
                
                // Auto-detect if mobile/desktop and set sidebar state
                const isMobile = window.innerWidth < 1024;
                if (isMobile) {
                    this.sidebarOpen = false;
                } else {
                    // On desktop, sidebar is open by default
                    this.sidebarOpen = true;
                    
                    // Load saved sidebar state only on desktop
                    const savedSidebarState = localStorage.getItem('sidebarOpen');
                    if (savedSidebarState !== null) {
                        this.sidebarOpen = JSON.parse(savedSidebarState);
                    }
                }
                
                // Save sidebar state when changed (desktop only)
                this.$watch('sidebarOpen', (value) => {
                    if (!isMobile) {
                        localStorage.setItem('sidebarOpen', value);
                    }
                    
                    // Ensure no horizontal scroll when sidebar opens
                    if (value && isMobile) {
                        document.body.classList.add('no-scroll-x');
                        document.body.style.overflow = 'hidden';
                    } else {
                        document.body.classList.remove('no-scroll-x');
                        document.body.style.overflow = '';
                    }
                });
                
                // Close sidebar when clicking escape key
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && this.sidebarOpen) {
                        this.closeSidebar();
                    }
                });
                
                // Handle window resize
                window.addEventListener('resize', () => {
                    const newIsMobile = window.innerWidth < 1024;
                    if (!newIsMobile && !this.sidebarOpen) {
                        // On desktop resize, ensure sidebar is open
                        this.sidebarOpen = true;
                    } else if (newIsMobile && this.sidebarOpen) {
                        // On mobile resize, close sidebar
                        this.sidebarOpen = false;
                    }
                });
                
                // Initial scroll prevention if sidebar is open on mobile
                if (this.sidebarOpen && isMobile) {
                    document.body.classList.add('no-scroll-x');
                    document.body.style.overflow = 'hidden';
                }
            },
            
            toggleSidebar() {
                this.sidebarOpen = !this.sidebarOpen;
            },
            
            openSidebar() {
                this.sidebarOpen = true;
            },
            
            closeSidebar() {
                this.sidebarOpen = false;
            },
            
            changeColorMode(mode) {
                // Only allow 'light' or 'dark' modes
                if (mode === 'light' || mode === 'dark') {
                    this.colorMode = `color-mode-${mode}`;
                    localStorage.setItem('colorMode', this.colorMode);
                }
            }
        }
    }
    
    // Separate function for alert dropdown with low stock functionality
    function alertDropdown() {
        return {
            openPro: false,
            lowStockProducts: [],
            lowStockCount: 0,
            
            init() {
                // Fetch low stock products on initialization
                this.fetchLowStockProducts();
                
                // Set up periodic refresh (every 5 minutes)
                setInterval(() => {
                    this.fetchLowStockProducts();
                }, 5 * 60 * 1000);
            },
            
            fetchLowStockProducts() {
                // Fetch products with low stock (idadi <= 5)
                fetch(`/api/low-stock-products?company_id={{ auth()->user()->company_id ?? '' }}`)
                    .then(response => response.json())
                    .then(data => {
                        this.lowStockProducts = data.products || [];
                        this.lowStockCount = data.count || 0;
                    })
                    .catch(error => {
                        console.error('Error fetching low stock products:', error);
                        this.lowStockProducts = [];
                        this.lowStockCount = 0;
                    });
            },
            
            toggleAlert() {
                this.openPro = !this.openPro;
                // Refresh data when opening alert
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