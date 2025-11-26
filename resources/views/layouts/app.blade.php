<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'DEMODAY')</title>
    <style>[x-cloak]{display:none!important}</style>
    <!-- Tailwind CSS -->
    <script src="//unpkg.com/alpinejs" defer></script>
<script src="https://cdn.tailwindcss.com"></script>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js for interactivity -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Font Awesome for icons -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js" crossorigin="anonymous"></script>
    
    <style>
        /* Custom Styles */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
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
        
        .sidebar-item {
            position: relative;
            overflow: hidden;
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
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .notification-dot {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
        
        .scrollbar-thin::-webkit-scrollbar {
            width: 6px;
        }
        
        .scrollbar-thin::-webkit-scrollbar-track {
            background: #01723d;
            border-radius: 10px;
        }
        
        .scrollbar-thin::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }
        
        .scrollbar-thin::-webkit-scrollbar-thumb:hover {
            background: #00632d;
        }
        
        .active-nav-item {
            background-color: rgba(255, 255, 255, 0.15);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        
        .modal-overlay {
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
        }
        
        [x-cloak] { display: none !important; }
        
        /* Color Mode Toggle */
        .color-mode-toggle {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1000;
        }
        
        .color-mode-btn {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid;
        }
        
        .color-mode-btn:hover {
            transform: scale(1.1);
        }
        
        .color-mode-default .color-mode-btn {
            background: linear-gradient(45deg, #ff6b6b, #4ecdc4, #45b7d1);
            border-color: #e2e8f0;
        }
        
        .color-mode-dark .color-mode-btn {
            background: #374151;
            border-color: #4b5563;
            color: #f9fafb;
        }
        
        .color-mode-light .color-mode-btn {
            background: #ffffff;
            border-color: #e2e8f0;
            color: #1e293b;
        }
        
        .color-mode-menu {
            position: absolute;
            top: 50px;
            left: 0;
            background: white;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            padding: 8px;
            min-width: 120px;
            z-index: 1001;
        }
        
        .color-mode-option {
            display: flex;
            align-items: center;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-bottom: 4px;
        }
        
        .color-mode-option:hover {
            background: #f1f5f9;
        }
        
        .color-mode-option:last-child {
            margin-bottom: 0;
        }
        
        .color-mode-indicator {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            margin-right: 8px;
            border: 2px solid #e2e8f0;
        }
        
        .color-mode-default .color-mode-indicator.default {
            background: linear-gradient(45deg, #ff6b6b, #4ecdc4, #45b7d1);
        }
        
        .color-mode-dark .color-mode-indicator.dark {
            background: #374151;
        }
        
        .color-mode-light .color-mode-indicator.light {
            background: #ffffff;
            border-color: #cbd5e1;
        }
    </style>
    
    @stack('styles')
</head>
<body class="color-mode-default" x-data="app()" :class="colorMode">
<!-- Alpine.js -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<!-- Color Mode Toggle -->
<div class="color-mode-toggle" x-data="{ colorMenuOpen: false }">
    <button 
        @click="colorMenuOpen = !colorMenuOpen"
        class="color-mode-btn shadow-lg"
        title="Badili Mwonekano"
    >
        <i class="fas fa-palette text-sm"></i>
    </button>
    
    <div 
    x-cloak
        x-show="colorMenuOpen" 
        @click.away="colorMenuOpen = false"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="color-mode-menu"
    >
        <div 
            class="color-mode-option"
            @click="changeColorMode('default'); colorMenuOpen = false"
        >
            <div class="color-mode-indicator default"></div>
            <span class="text-sm">Rangi Za Kawaida</span>
        </div>
        <div 
            class="color-mode-option"
            @click="changeColorMode('dark'); colorMenuOpen = false"
        >
            <div class="color-mode-indicator dark"></div>
            <span class="text-sm">Giza</span>
        </div>
        <div 
            class="color-mode-option"
            @click="changeColorMode('light'); colorMenuOpen = false"
        >
            <div class="color-mode-indicator light"></div>
            <span class="text-sm">Mwanga</span>
        </div>
    </div>
</div>

<div class="flex h-screen overflow-hidden">
    <!-- Enhanced Sidebar -->
    <aside 
        :class="sidebarOpen ? 'w-64' : 'w-20'" 
        class="gradient-bg text-white flex flex-col transition-all duration-300 shadow-xl z-20"
    >
        <!-- Logo Section -->
        <div class="p-5 text-center border-b border-green-700 flex flex-col items-center">
 <img src="https://test.mauzosheet.com/assets/images/apple-icon.gif" alt="Mauzo Logo" class="logo">
          <div x-show="sidebarOpen" class="text-xl font-bold tracking-wide">MAUZO</div>
            <div x-show="sidebarOpen" x-transition class="text-xs text-green-200">Boss System</div>
            <button 
                @click="sidebarOpen = !sidebarOpen" 
                class="mt-3 p-2 rounded-full bg-green-700 hover:bg-green-600 transition-all duration-200"
            >
                <span x-show="sidebarOpen">◀</span>
                <span x-show="!sidebarOpen">▶</span>
            </button>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 p-4 space-y-2 overflow-y-auto scrollbar-thin">
            @include('partials.navigation')
        </nav>
        
        <!-- Sidebar Footer -->
        <div class="p-4 border-t border-green-700 text-center">
            <div x-show="sidebarOpen" x-transition class="text-xs text-green-200">
                {{ now()->format('d/m/Y H:i') }}
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col overflow-hidden" :class="{
        'bg-gray-50 text-gray-800': colorMode === 'color-mode-default',
        'bg-gray-900 text-white': colorMode === 'color-mode-dark', 
        'bg-white text-gray-900': colorMode === 'color-mode-light'
    }">

        <!-- Enhanced Header -->
        <header class="shadow-sm border-b px-6 py-4" :class="{
            'bg-white border-gray-200': colorMode === 'color-mode-default',
            'bg-gray-800 border-gray-700': colorMode === 'color-mode-dark',
            'bg-white border-gray-300': colorMode === 'color-mode-light'
        }">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold">@yield('page-title', 'Dashboard')</h1>
                    <p class="text-sm mt-1" :class="{
                        'text-gray-600': colorMode === 'color-mode-default',
                        'text-gray-300': colorMode === 'color-mode-dark',
                        'text-gray-500': colorMode === 'color-mode-light'
                    }">@yield('page-subtitle', 'Karibu tena, Meneja!')</p>
                </div>
                
                <div class="flex items-center space-x-4">
                    <!-- Search Bar -->

<!-- SINGLE ALERT MESSAGE DROPDOWN -->
<div x-data="{ openPro: false }" class="relative">

    <!-- Alert Icon -->
    <button @click="openPro = !openPro"
        class="relative p-2 text-gray-600 hover:text-green-600 transition">
        <span class="text-xl">🔔</span>
    </button>

    <!-- Dropdown -->
    <div x-show="openPro"
         @click.away="openPro = false"
         x-transition
         class="absolute right-0 mt-2 w-72 bg-white rounded-xl shadow-xl p-4 z-40">

        <div class="px-4 py-3 bg-green-600 text-white rounded-lg text-sm shadow">
            Tumia Mauzo Sheet Pro kwa sasa
        </div>

    </div>
</div>


                    
                    <!-- User profile -->
                    <div class="relative" x-data="{ profileOpen: false }">
                        <button @click="profileOpen = !profileOpen" 
                                class="flex items-center space-x-2 focus:outline-none">
                            <div class="w-10 h-10 bg-gradient-to-r from-green-600 to-green-500 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-md">
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
                            <svg class="w-4 h-4" :class="{
                                'text-gray-500': colorMode === 'color-mode-default',
                                'text-gray-400': colorMode === 'color-mode-dark',
                                'text-gray-600': colorMode === 'color-mode-light'
                            }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        
                        <!-- Profile Dropdown -->
                        <div x-show="profileOpen" @click.away="profileOpen = false" 
                        x-cloak
                             x-transition:enter="transition ease-out duration-100" 
                             x-transition:enter-start="transform opacity-0 scale-95" 
                             x-transition:enter-end="transform opacity-100 scale-100" 
                             x-transition:leave="transition ease-in duration-75" 
                             x-transition:leave-start="transform opacity-100 scale-100" 
                             x-transition:leave-end="transform opacity-0 scale-95"
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
                                🔐 Badili Neno Siri
                            </a>
                            <a href="{{ route('company.info') }}" 
                               class="block px-4 py-2 text-sm transition-colors" :class="{
                                'text-gray-700 hover:bg-green-50 hover:text-green-700': colorMode === 'color-mode-default',
                                'text-gray-200 hover:bg-gray-700 hover:text-green-400': colorMode === 'color-mode-dark',
                                'text-gray-700 hover:bg-gray-100 hover:text-green-600': colorMode === 'color-mode-light'
                               }">
                                🏢 Taarifa ya Kampuni
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
                                    🚪 Toka
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto p-6 scrollbar-thin" :class="{
            'bg-gray-50': colorMode === 'color-mode-default',
            'bg-gray-900': colorMode === 'color-mode-dark',
            'bg-white': colorMode === 'color-mode-light'
        }">
            @yield('content')
        </main>
    </div>
</div>

<script>
function app() {
    return {
        sidebarOpen: true,
        searchQuery: '',
        colorMode: 'color-mode-default',
        
        init() {
            // Load saved color mode from localStorage
            const savedMode = localStorage.getItem('colorMode');
            if (savedMode) {
                this.colorMode = savedMode;
            }
        },
        
        changeColorMode(mode) {
            this.colorMode = `color-mode-${mode}`;
            // Save to localStorage
            localStorage.setItem('colorMode', this.colorMode);
        }
    }
}
</script>

@stack('scripts')
</body>
</html>