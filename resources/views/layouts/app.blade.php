<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        }
        
        body {
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
            overflow-x: hidden;
            width: 100vw;
            min-height: 100vh;
        }
        
        /* Color Modes */
        .color-mode-default {
            --bg-primary: #f9fafb;
            --bg-secondary: #ffffff;
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --border-color: #e5e7eb;
        }
        
        .color-mode-dark {
            --bg-primary: #1f2937;
            --bg-secondary: #374151;
            --text-primary: #f9fafb;
            --text-secondary: #d1d5db;
            --border-color: #4b5563;
        }
        
        .color-mode-light {
            --bg-primary: #ffffff;
            --bg-secondary: #f8fafc;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border-color: #e2e8f0;
        }
        
        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 280px;
            z-index: 40;
            transform: translateX(-100%);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar.open {
            transform: translateX(0);
        }
        
        /* Main Content Container */
        .main-container {
            width: 100%;
            min-height: 100vh;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            left: 0;
        }
        
        /* When sidebar is open, push content to right */
        .sidebar-open .main-container {
            transform: translateX(280px);
            width: calc(100% - 280px);
        }
        
        /* Ensure content fits within viewport */
        .main-content {
            width: 100%;
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        /* Mobile specific adjustments */
        @media (max-width: 1023px) {
            .sidebar {
                width: 260px;
                max-width: 85%;
            }
            
            .sidebar-open .main-container {
                transform: translateX(260px);
                width: calc(100% - 260px);
            }
        }
        
        /* Hamburger Menu - Always visible */
        .hamburger-menu {
            width: 30px;
            height: 20px;
            position: relative;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .hamburger-menu span {
            display: block;
            height: 3px;
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
        
        /* Sidebar items */
        .sidebar-item {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
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
        
        .gradient-bg {
            background: linear-gradient(135deg, #065f46 0%, #047857 100%);
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
            width: 4px;
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
        
        /* Color Mode Toggle */
        .color-mode-toggle {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 50;
        }
        
        .color-mode-btn {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            background: white;
            color: #065f46;
        }
        
        .color-mode-menu {
            position: absolute;
            bottom: 60px;
            right: 0;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            padding: 12px;
            min-width: 160px;
            z-index: 51;
        }
        
        /* Header adjustments */
        .header-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        /* Content width adjustment */
        .content-inner {
            max-width: 100%;
            overflow-x: hidden;
        }
        
        /* Responsive adjustments */
        @media (max-width: 640px) {
            .color-mode-toggle {
                bottom: 10px;
                right: 10px;
            }
            
            .color-mode-btn {
                width: 45px;
                height: 45px;
            }
            
            .sidebar-open .main-container {
                transform: translateX(260px);
            }
        }
    </style>
    
    @stack('styles')
</head>

<body class="color-mode-default no-scroll-x" x-data="app()" :class="[colorMode, sidebarOpen ? 'sidebar-open' : '']" x-init="init()">
    
    <!-- Sidebar -->
    <aside class="sidebar gradient-bg text-white flex flex-col shadow-xl"
           :class="{'open': sidebarOpen}">
        <!-- Logo Section -->
        <div class="p-6 text-center border-b border-green-700">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-3">
                    <img src="https://test.mauzosheet.com/assets/images/apple-icon.gif" 
                         alt="Mauzo Logo" 
                         class="w-12 h-12 rounded-lg">
                    <div>
                        <div class="text-xl font-bold text-left">MAUZO</div>
                        <div class="text-xs text-green-200 text-left">Boss System</div>
                    </div>
                </div>
                <button @click="closeSidebar()" 
                        class="text-green-200 hover:text-white transition p-2">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 p-4 space-y-2 overflow-y-auto scrollbar-thin">
            @include('partials.navigation')
        </nav>
        
        <!-- Sidebar Footer -->
        <div class="p-4 border-t border-green-700">
            <div class="text-xs text-green-200 text-center">
                {{ now()->format('d/m/Y H:i') }}
            </div>
            <div class="mt-2 text-center">
                <button @click="closeSidebar()" 
                        class="text-green-200 hover:text-white text-sm transition">
                    <i class="fas fa-chevron-left mr-1"></i> Funga Menu
                </button>
            </div>
        </div>
    </aside>

    <!-- Main Content Container -->
    <div class="main-container">
        <div class="main-content flex flex-col min-h-screen" :class="{
            'bg-gray-50 text-gray-800': colorMode === 'color-mode-default',
            'bg-gray-900 text-white': colorMode === 'color-mode-dark', 
            'bg-white text-gray-900': colorMode === 'color-mode-light'
        }">
            <!-- Header with Hamburger Menu -->
            <header class="sticky top-0 z-30 shadow-sm border-b px-4 sm:px-6 py-4" :class="{
                'bg-white border-gray-200': colorMode === 'color-mode-default',
                'bg-gray-800 border-gray-700': colorMode === 'color-mode-dark',
                'bg-white border-gray-300': colorMode === 'color-mode-light'
            }">
                <div class="flex justify-between items-center">
                    <div class="header-left">
                        <!-- Hamburger Menu - Always visible -->
                        <div class="hamburger-menu" 
                             :class="{'active': sidebarOpen}"
                             @click="toggleSidebar()">
                            <span :class="{
                                'bg-gray-800': colorMode === 'color-mode-default',
                                'bg-white': colorMode === 'color-mode-dark',
                                'bg-gray-800': colorMode === 'color-mode-light'
                            }"></span>
                            <span :class="{
                                'bg-gray-800': colorMode === 'color-mode-default',
                                'bg-white': colorMode === 'color-mode-dark',
                                'bg-gray-800': colorMode === 'color-mode-light'
                            }"></span>
                            <span :class="{
                                'bg-gray-800': colorMode === 'color-mode-default',
                                'bg-white': colorMode === 'color-mode-dark',
                                'bg-gray-800': colorMode === 'color-mode-light'
                            }"></span>
                        </div>
                        
                        <!-- Page Title -->
                        <div>
                            <h1 class="text-xl sm:text-2xl font-bold">@yield('page-title', 'Dashboard')</h1>
                            <p class="text-xs sm:text-sm mt-1" :class="{
                                'text-gray-600': colorMode === 'color-mode-default',
                                'text-gray-300': colorMode === 'color-mode-dark',
                                'text-gray-500': colorMode === 'color-mode-light'
                            }">@yield('page-subtitle', 'Karibu tena, Meneja!')</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3 sm:space-x-4">
                        <!-- Alert Dropdown -->
                        <div x-data="{ openPro: false }" class="relative">
                            <button @click="openPro = !openPro"
                                class="relative p-2 text-gray-600 hover:text-green-600 transition">
                                <i class="fas fa-bell text-lg"></i>
                            </button>

                            <div x-show="openPro"
                                 @click.away="openPro = false"
                                 x-transition
                                 class="absolute right-0 mt-2 w-72 bg-white rounded-xl shadow-xl p-4 z-40">
                                <div class="px-4 py-3 bg-green-600 text-white rounded-lg text-sm shadow">
                                    Tumia Mauzo Sheet Pro kwa sasa
                                </div>
                            </div>
                        </div>
                        
                        <!-- User Profile -->
                        <div class="relative" x-data="{ profileOpen: false }">
                            <button @click="profileOpen = !profileOpen" 
                                    class="flex items-center space-x-2 focus:outline-none">
                                <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-r from-green-600 to-green-500 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-md">
                                    B
                                </div>
                                <div class="hidden md:block text-left">
                                    <div class="text-sm font-medium" :class="{
                                        'text-gray-700': colorMode === 'color-mode-default',
                                        'text-gray-200': colorMode === 'color-mode-dark',
                                        'text-gray-800': colorMode === 'color-mode-light'
                                    }">Boss</div>
                                    <div class="text-xs" :class="{
                                        'text-gray-500': colorMode === 'color-mode-default',
                                        'text-gray-400': colorMode === 'color-mode-dark',
                                        'text-gray-600': colorMode === 'color-mode-light'
                                    }">Meneja</div>
                                </div>
                                <i class="fas fa-chevron-down text-sm hidden md:block"></i>
                            </button>
                            
                            <!-- Profile Dropdown -->
                            <div x-show="profileOpen" 
                                 @click.away="profileOpen = false" 
                                 x-cloak
                                 x-transition
                                 class="absolute right-0 mt-2 w-48 rounded-lg shadow-lg z-50 py-1" :class="{
                                    'bg-white border border-gray-200': colorMode === 'color-mode-default',
                                    'bg-gray-800 border border-gray-700': colorMode === 'color-mode-dark',
                                    'bg-white border border-gray-300': colorMode === 'color-mode-light'
                                 }">
                                <a href="{{ route('password.change') }}" 
                                   class="block px-4 py-2 text-sm transition-colors" :class="{
                                    'text-gray-700 hover:bg-green-50 hover:text-green-700': colorMode === 'color-mode-default',
                                    'text-gray-200 hover:bg-gray-700 hover:text-green-400': colorMode === 'color-mode-dark',
                                    'text-gray-700 hover:bg-gray-100 hover:text-green-600': colorMode === 'color-mode-light'
                                   }">
                                    <i class="fas fa-key mr-2"></i>Badili Neno Siri
                                </a>
                                <a href="{{ route('company.info') }}" 
                                   class="block px-4 py-2 text-sm transition-colors" :class="{
                                    'text-gray-700 hover:bg-green-50 hover:text-green-700': colorMode === 'color-mode-default',
                                    'text-gray-200 hover:bg-gray-700 hover:text-green-400': colorMode === 'color-mode-dark',
                                    'text-gray-700 hover:bg-gray-100 hover:text-green-600': colorMode === 'color-mode-light'
                                   }">
                                    <i class="fas fa-building mr-2"></i>Taarifa ya Kampuni
                                </a>
                                <div class="border-t my-1" :class="{
                                    'border-gray-100': colorMode === 'color-mode-default',
                                    'border-gray-700': colorMode === 'color-mode-dark',
                                    'border-gray-200': colorMode === 'color-mode-light'
                                }"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" 
                                            class="w-full text-left px-4 py-2 text-sm transition-colors" :class="{
                                            'text-gray-700 hover:bg-green-50 hover:text-green-700': colorMode === 'color-mode-default',
                                            'text-gray-200 hover:bg-gray-700 hover:text-green-400': colorMode === 'color-mode-dark',
                                            'text-gray-700 hover:bg-gray-100 hover:text-green-600': colorMode === 'color-mode-light'
                                           }">
                                        <i class="fas fa-sign-out-alt mr-2"></i>Toka
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 scrollbar-thin content-inner" :class="{
                'bg-gray-50': colorMode === 'color-mode-default',
                'bg-gray-900': colorMode === 'color-mode-dark',
                'bg-white': colorMode === 'color-mode-light'
            }">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Color Mode Toggle -->
    <div class="color-mode-toggle" x-data="{ colorMenuOpen: false }">
        <button 
            @click="colorMenuOpen = !colorMenuOpen"
            class="color-mode-btn shadow-lg"
            title="Badili Mwonekano"
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
                class="color-mode-option flex items-center gap-3 p-3 hover:bg-gray-100 rounded-lg cursor-pointer"
                @click="changeColorMode('default'); colorMenuOpen = false"
            >
                <div class="w-6 h-6 rounded-full bg-gradient-to-r from-blue-400 to-purple-500"></div>
                <span class="text-sm font-medium">Rangi Za Kawaida</span>
            </div>
            <div 
                class="color-mode-option flex items-center gap-3 p-3 hover:bg-gray-100 rounded-lg cursor-pointer"
                @click="changeColorMode('dark'); colorMenuOpen = false"
            >
                <div class="w-6 h-6 rounded-full bg-gray-800"></div>
                <span class="text-sm font-medium">Giza</span>
            </div>
            <div 
                class="color-mode-option flex items-center gap-3 p-3 hover:bg-gray-100 rounded-lg cursor-pointer"
                @click="changeColorMode('light'); colorMenuOpen = false"
            >
                <div class="w-6 h-6 rounded-full bg-white border border-gray-300"></div>
                <span class="text-sm font-medium">Mwanga</span>
            </div>
        </div>
    </div>

    <script>
    function app() {
        return {
            sidebarOpen: false,
            colorMode: 'color-mode-default',
            
            init() {
                // Load saved color mode
                const savedMode = localStorage.getItem('colorMode');
                if (savedMode) {
                    this.colorMode = savedMode;
                }
                
                // Load sidebar state if previously opened
                const savedSidebarState = localStorage.getItem('sidebarOpen');
                if (savedSidebarState !== null) {
                    this.sidebarOpen = JSON.parse(savedSidebarState);
                }
                
                // Save sidebar state when changed
                this.$watch('sidebarOpen', (value) => {
                    localStorage.setItem('sidebarOpen', value);
                    
                    // Ensure no horizontal scroll when sidebar opens
                    if (value) {
                        document.body.classList.add('no-scroll-x');
                    } else {
                        document.body.classList.remove('no-scroll-x');
                    }
                });
                
                // Close sidebar when clicking escape key
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && this.sidebarOpen) {
                        this.closeSidebar();
                    }
                });
                
                // Initial scroll prevention if sidebar is open
                if (this.sidebarOpen) {
                    document.body.classList.add('no-scroll-x');
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
                this.colorMode = `color-mode-${mode}`;
                localStorage.setItem('colorMode', this.colorMode);
            }
        }
    }
    </script>

    @stack('scripts')
</body>
</html>