<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js for sidebar toggle -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 font-sans">

<div x-data="{ sidebarOpen: true }" class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside 
        :class="sidebarOpen ? 'w-64' : 'w-16'" 
        class="bg-green-800 text-white flex flex-col transition-all duration-300"
    >
        <div class="p-6 text-center border-b border-gray-700 flex flex-col items-center">
            <div x-show="sidebarOpen" x-transition class="text-2xl font-bold mb-1">DEMODAY</div>
            <div x-show="sidebarOpen" x-transition class="text-sm">Boss</div>
            <button 
                @click="sidebarOpen = !sidebarOpen" 
                class="mt-2 text-gray-300 hover:text-white text-xl focus:outline-none"
            >
                ☰
            </button>
        </div>

        <nav class="flex-1 p-4 space-y-2">
            <a href="{{ url('/dashboard') }}" class="flex items-center px-4 py-2 hover:bg-yellow-600 rounded transition" :class="sidebarOpen ? 'justify-start' : 'justify-center'">
                <span>🏠</span>
                <span x-show="sidebarOpen" x-transition class="ml-3">Dashboard</span>
            </a>
            <a href="{{ url('/mauzo') }}" class="flex items-center px-4 py-2 hover:bg-yellow-600 rounded transition" :class="sidebarOpen ? 'justify-start' : 'justify-center'">
                <span>🛒</span>
                <span x-show="sidebarOpen" x-transition class="ml-3">Mauzo</span>
            </a>
            <a href="{{ url('/madeni') }}" class="flex items-center px-4 py-2 hover:bg-yellow-600 rounded transition" :class="sidebarOpen ? 'justify-start' : 'justify-center'">
                <span>💳</span>
                <span x-show="sidebarOpen" x-transition class="ml-3">Madeni</span>
            </a>
            <a href="{{ url('/matumizi') }}" class="flex items-center px-4 py-2 hover:bg-yellow-600 rounded transition" :class="sidebarOpen ? 'justify-start' : 'justify-center'">
                <span>💰</span>
                <span x-show="sidebarOpen" x-transition class="ml-3">Matumizi</span>
            </a>
            <a href="{{ url('/bidhaa') }}" class="flex items-center px-4 py-2 hover:bg-yellow-600 rounded transition" :class="sidebarOpen ? 'justify-start' : 'justify-center'">
                <span>📦</span>
                <span x-show="sidebarOpen" x-transition class="ml-3">Bidhaa</span>
            </a>
            <a href="{{ url('/manunuzi') }}" class="flex items-center px-4 py-2 hover:bg-yellow-600 rounded transition" :class="sidebarOpen ? 'justify-start' : 'justify-center'">
                <span>🚚</span>
                <span x-show="sidebarOpen" x-transition class="ml-3">Manunuzi</span>
            </a>
            <a href="{{ url('/wafanyakazi') }}" class="flex items-center px-4 py-2 hover:bg-yellow-600 rounded transition" :class="sidebarOpen ? 'justify-start' : 'justify-center'">
                <span>👔</span>
                <span x-show="sidebarOpen" x-transition class="ml-3">Wafanyakazi</span>
            </a>
            <a href="{{ url('/masaplaya') }}" class="flex items-center px-4 py-2 hover:bg-yellow-600 rounded transition" :class="sidebarOpen ? 'justify-start' : 'justify-center'">
                <span>🏆</span>
                <span x-show="sidebarOpen" x-transition class="ml-3">Masaplaya</span>
            </a>
            <a href="{{ url('/wateja') }}" class="flex items-center px-4 py-2 hover:bg-yellow-600 rounded transition" :class="sidebarOpen ? 'justify-start' : 'justify-center'">
                <span>👥</span>
                <span x-show="sidebarOpen" x-transition class="ml-3">Wateja</span>
            </a>
            <a href="{{ url('/uchambuzi') }}" class="flex items-center px-4 py-2 hover:bg-yellow-600 rounded transition" :class="sidebarOpen ? 'justify-start' : 'justify-center'">
                <span>📊</span>
                <span x-show="sidebarOpen" x-transition class="ml-3">Uchambuzi</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col transition-all duration-300">

        <!-- Header -->
        <header class="bg-white shadow px-6 py-4 flex justify-end items-center space-x-6 text-gray-600">
            <!-- Email Icon -->
            <button title="Barua Pepe" class="hover:text-green-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </button>

            <!-- Alert Icon -->
            <button title="Arifa" class="hover:text-green-700 relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V4a2 2 0 10-4 0v1.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
            </button>

            <!-- Profile Dropdown -->
            <div class="relative">
                <button id="profileMenuButton" title="Profaili"
                        class="hover:text-green-700 focus:outline-none focus:ring-2 focus:ring-green-400 rounded-full p-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M5.121 17.804A7 7 0 0112 15a7 7 0 016.879 2.804M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </button>

                <div id="profileDropdown"
                     class="hidden absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
                    <a href="{{ route('password.change') }}"
                       class="block px-4 py-2 text-gray-700 hover:bg-green-50 hover:text-green-700">
                        🔐 Badili Neno Siri
                    </a>
                    <a href="{{ route('company.info') }}"
                       class="block px-4 py-2 text-gray-700 hover:bg-green-50 hover:text-green-700">
                        🏢 Taarifa ya Kampuni
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="w-full text-left px-4 py-2 text-gray-700 hover:bg-green-50 hover:text-green-700">
                            🚪 Toka
                        </button>
                    </form>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const button = document.getElementById('profileMenuButton');
                    const dropdown = document.getElementById('profileDropdown');

                    button.addEventListener('click', () => {
                        dropdown.classList.toggle('hidden');
                    });

                    document.addEventListener('click', (e) => {
                        if (!button.contains(e.target) && !dropdown.contains(e.target)) {
                            dropdown.classList.add('hidden');
                        }
                    });
                });
            </script>
        </header>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto p-6">
            @yield('content')
        </main>
    </div>
</div>

</body>
</html>
