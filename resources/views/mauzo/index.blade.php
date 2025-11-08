<!DOCTYPE html>
<html lang="sw">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mauzo</title>

    <style>
        /* Custom Styles */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
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
            background: #03f54c;
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
    </style>

<script src="https://cdn.tailwindcss.com"></script>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="bg-gray-50 font-sans antialiased">

<div x-data="mauzoApp()" class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside 
        :class="sidebarOpen ? 'w-64' : 'w-20'" 
        class="gradient-bg text-white flex flex-col transition-all duration-300 shadow-xl z-20"
    >
        <!-- Logo Section -->
        <div class="p-5 text-center border-b border-green-700 flex flex-col items-center">
            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-white text-green-800 font-bold text-lg mb-2">
                D
            </div>
            <div x-show="sidebarOpen" x-transition class="text-xl font-bold mb-1">DEMODAY</div>
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

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">

       <!-- Header -->
<header class="bg-white-600 shadow-sm border-b px-6 py-4">
    <div class="flex items-center justify-between">
        <!-- Title & Date -->
        <div class="text-black-600">
            <div class="font-bold text-md">Mauzo</div>
            <div class="text-l">Usimamizi wa mazuo - {{ now()->format('d/m/Y H:i') }}</div>
        </div>

        <!-- Tabs -->
        <nav class="flex space-x-4 text-md">
            <!-- Sehemu ya Mauzo -->
            <button @click="activeTab='sehemu'" 
                    :class="activeTab==='sehemu' ? 'text-blue-600 border-b-2 border-blue-600 pb-1' : 'text-black-600 hover:text-blue-800'" 
                    class="transition flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Sehemu ya Mauzo
            </button>

            <!-- Mauzo kwa Barcode -->
            <button @click="activeTab='barcode'" 
                    :class="activeTab==='barcode' ? 'text-blue-600 border-b-2 border-blue-600 pb-1' : 'text-black-600 hover:text-blue-500'" 
                    class="transition flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                </svg>
                Mauzo kwa Barcode
            </button>

            <!-- Taarifa Fupi -->
            <button @click="activeTab='taarifa'" 
                    :class="activeTab==='taarifa' ? 'text-blue-600 border-b-2 border-blue-600 pb-1' : 'text-black-600 hover:text-blue-500'" 
                    class="transition flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Taarifa Fupi
            </button>

            <!-- Mauzo ya Jumla -->
            <button @click="activeTab='jumla'" 
                    :class="activeTab==='jumla' ? 'text-blue-600 border-b-2 border-blue-600 pb-1' : 'text-black-600 hover:text-blue-500'" 
                    class="transition flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Mauzo ya Jumla
            </button>

            <!-- Kikapu -->
            <button @click="showKikapu = true" 
                    class="text-gray-600 hover:text-blue-500 transition flex items-center gap-1 relative" 
                    title="Fungua Kikapu">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span class="text-sm font-medium">Kikapu</span>
                <template x-if="cart.length > 0">
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center" x-text="cart.length"></span>
                </template>
            </button>
        </nav>
    </div>
</header>


        <!-- Page Content -->
        <main class="flex-1 p-6 overflow-auto bg-white-500">

<!-- Success/Error Messages -->
<div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)">
    @if(session('success'))
    <div x-show="show" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform -translate-y-4"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform -translate-y-4"
         class="fixed top-4 left-1/2 transform -translate-x-1/2 bg-emerald-800 border-l-4 border-green-500 text-white p-4 rounded shadow-lg z-50"
         role="alert">
        <p>{{ session('success') }}</p>
    </div>
    @endif

    @if($errors->any())
    <div x-show="show" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform -translate-y-4"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform -translate-y-4"
         class="fixed top-4 left-1/2 transform -translate-x-1/2 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-lg z-50"
         role="alert">
        <p class="font-bold">Hitilafu zifuatazo zimetokea:</p>
        <ul class="list-disc list-inside mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
</div>

            
          <!-- 🌿 Sehemu ya Mauzo (Modern Green Redesign) -->
<div 
    x-show="activeTab==='sehemu'" 
    x-data="{ showMadeniModal: false }" 
    class="bg-gradient-to-br from-green-100 to-green-200 rounded-2xl shadow-lg border border-green-200 p-8 mb-8 transition-all duration-500 hover:shadow-2xl hover:border-green-300"
>

    <!-- Title -->
    <h2 class="text-2xl font-bold text-black-800 mb-6 flex items-center border-b border-emerald-700 pb-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 mr-2 text-emerald-600 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
        Rekodi Mauzo Mapya
    </h2>

<!-- Sales Form -->
<form 
    method="POST" 
    action="{{ route('mauzo.store') }}" 
    class="mb-8 bg-gradient-to-br from-amber-400 to-amber-500 p-6 rounded-2xl border border-emerald-100 shadow-sm"
    @submit.prevent="if(decreaseStock()) $el.submit()"
>
    @csrf

    <!-- First Line -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-4">
        <!-- Bidhaa -->
        <div class="md:col-span-2">
            <label for="bidhaaSelect" class="block mb-2 font-bold text-emerald-800 flex items-center gap-2">
                <i class="fas fa-box text-emerald-600 text-sm"></i>
                Chagua Bidhaa
            </label>
            <select id="bidhaaSelect"
                    name="bidhaa_id" 
                    x-model="bidhaaSelected" 
                    @change="updateStock()"
                    class="w-full rounded-lg border-2 border-emerald-200 bg-white p-3 text-sm shadow-sm focus:ring-2 focus:ring-emerald-200 focus:border-emerald-500 transition-all duration-300 hover:border-emerald-300">
                <option value="">Chagua Bidhaa...</option>
                @foreach($bidhaa as $item)
                    <option value="{{ $item->id }}" data-bei="{{ $item->bei_kuuza }}">
                        {{ $item->jina }} ({{ $item->aina }})
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Idadi -->
        <div>
            <label class="block mb-2 font-bold text-emerald-800 flex items-center gap-2">
                <i class="fas fa-hashtag text-emerald-600 text-sm"></i>
                Idadi
            </label>
            <input type="number" name="idadi" min="1" :max="stock" 
                   x-model.number="idadi"
                   class="w-full border-2 border-emerald-200 rounded-lg p-3 text-sm shadow-sm focus:ring-2 focus:ring-emerald-200 focus:border-emerald-500 hover:border-emerald-300 transition-all duration-300">
        </div>

        <!-- Bei -->
        <div>
            <label class="block mb-2 font-bold text-emerald-800 flex items-center gap-2">
                <i class="fas fa-tag text-emerald-600 text-sm"></i>
                Bei (Tsh)
            </label>
            <input type="number" name="bei" readonly
                   :value="bei"
                   class="w-full bg-gradient-to-r from-emerald-50 to-green-50 border-2 border-emerald-200 rounded-lg p-3 text-sm shadow-inner text-emerald-800 font-semibold cursor-not-allowed">
        </div>

        <!-- Jumla -->
        <div>
            <label class="block mb-2 font-bold text-emerald-800 flex items-center gap-2">
                <i class="fas fa-calculator text-emerald-600 text-sm"></i>
                Jumla (Tsh)
            </label>
            <input type="number" name="jumla" readonly
                   :value="jumla"
                   class="w-full bg-gradient-to-r from-emerald-50 to-green-50 border-2 border-emerald-200 rounded-lg p-3 text-sm shadow-inner text-emerald-800 font-semibold cursor-not-allowed">
        </div>
    </div>

    <!-- Second Line -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <!-- Stock Ipo -->
        <div>
            <label class="block mb-2 font-bold text-emerald-800 flex items-center gap-2">
                <i class="fas fa-boxes text-emerald-600 text-sm"></i>
                Stock Ipo
            </label>
            <input type="number" readonly
                   :value="stock"
                   class="w-full bg-gradient-to-r from-emerald-50 to-green-50 border-2 border-emerald-200 rounded-lg p-3 text-sm shadow-inner text-emerald-800 font-semibold cursor-not-allowed">
        </div>

        <!-- Punguzo -->
        <div class="md:col-span-2">
            <label class="block mb-2 font-bold text-emerald-800 flex items-center gap-2">
                <i class="fas fa-percentage text-emerald-600 text-sm"></i>
                Punguzo
            </label>
            <div class="flex items-center gap-2 mb-2">
                <input type="checkbox" id="punguzoCheck" x-model="punguzoCheck" 
                       class="h-4 w-4 text-amber-600 focus:ring-amber-400 rounded border-amber-300">
                <label for="punguzoCheck" class="text-sm font-medium text-amber-800">Toa punguzo</label>
            </div>
            <input type="number" name="punguzo" min="0" placeholder="Kiasi..."
                   :disabled="!punguzoCheck"
                   x-model.number="punguzo"
                   class="w-full border-2 border-amber-200 rounded-lg p-3 text-sm focus:ring-2 focus:ring-amber-200 focus:border-amber-500 hover:border-amber-300 transition-all duration-300 disabled:bg-gray-100 disabled:border-gray-200">
        </div>

        <!-- Actions -->
        <div class="md:col-span-2">
            <label class="block mb-2 font-bold text-emerald-800 flex items-center gap-2">
                <i class="fas fa-bolt text-emerald-600 text-sm"></i>
                Vitendo
            </label>
            <div class="grid grid-cols-3 gap-2">
                <button type="submit" 
                    class="bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 text-white px-3 py-3 rounded-lg font-semibold flex items-center justify-center gap-2 transition-all duration-300 transform hover:-translate-y-0.5 shadow-md hover:shadow-lg text-xs">
                    <i class="fas fa-cash-register text-white text-xs"></i>
                    Uza
                </button>

                <button type="button" 
                    @click="showMadeniModal = true"
                    class="bg-gradient-to-r from-amber-700 to-amber-800 hover:from-amber-600 hover:to-yellow-600 text-white px-3 py-3 rounded-lg font-semibold flex items-center justify-center gap-2 transition-all duration-300 transform hover:-translate-y-0.5 shadow-md hover:shadow-lg text-xs">
                    <i class="fas fa-hand-holding-usd text-white text-xs"></i>
                    Kopesha
                </button>

                <button type="button" 
                    @click="addToCart"
                    class="bg-gradient-to-r from-green-500 to-green-700 hover:from-brown-600 hover:to-brown-600 text-white px-3 py-3 rounded-lg font-semibold flex items-center justify-center gap-2 transition-all duration-300 transform hover:-translate-y-0.5 shadow-md hover:shadow-lg text-xs">
                    <i class="fas fa-cart-plus text-white text-xs"></i>
                    Kikapu
                </button>
            </div>
        </div>
    </div>
</form>

    <!-- 💰 Kopesha Modal -->
    <div 
        x-show="showMadeniModal" 
        x-cloak 
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
    >
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all scale-95 hover:scale-100">
            <div class="bg-gradient-to-r from-amber-500 to-amber-600 p-5 text-white flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                </svg>
                <h2 class="text-lg font-semibold">Kopesha Bidhaa</h2>
            </div>

            <form 
                method="POST" 
                action="{{ route('madeni.store') }}"
                @submit.prevent="if(decreaseStock()) { showMadeniModal = false; $el.submit(); }"
                class="p-6 space-y-4"
            >
                @csrf

                <input type="hidden" name="bidhaa_id" :value="bidhaaSelected">
                <input type="hidden" name="idadi" :value="idadi">
                <input type="hidden" name="jumla" :value="jumla">
                <input type="hidden" name="baki" :value="jumla">
                <input type="hidden" name="bei" :value="bei">
                <input type="hidden" name="kopesha" value="1">

                <!-- Mteja Select -->
                <div>
                    <label class="block font-semibold mb-1 text-gray-700">Mteja Aliyesajiliwa</label>
                    <select 
                        x-model="selectedMtejaId" 
                        @change="fillMtejaDetails"
                        class="w-full border border-amber-300 rounded-xl p-3 focus:ring-4 focus:ring-amber-300 focus:border-amber-400 hover:border-amber-400 transition">
                        <option value="">-- Mteja Mpya --</option>
                        @foreach($wateja as $m)
                            <option value="{{ $m->id }}" 
                                data-jina="{{ $m->jina }}" 
                                data-simu="{{ $m->simu }}"
                                data-barua_pepe="{{ $m->barua_pepe }}"
                                data-anapoishi="{{ $m->anapoishi }}">
                                {{ $m->jina }} - {{ $m->simu }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- New Customer Info -->
                <div>
                    <label class="block text-gray-700 mb-1 font-medium">Jina la Mkopaji</label>
                    <input type="text" name="jina_mkopaji" x-model="mteja.jina"
                        class="w-full border border-amber-300 rounded-xl p-3 focus:ring-4 focus:ring-amber-300 focus:border-amber-400 transition" required>
                </div>

                <div>
                    <label class="block text-gray-700 mb-1 font-medium">Namba ya Simu</label>
                    <input type="text" name="simu" x-model="mteja.simu"
                        class="w-full border border-amber-300 rounded-xl p-3 focus:ring-4 focus:ring-amber-300 focus:border-amber-400 transition" required>
                </div>

                <div>
                    <label class="block text-gray-700 mb-1 font-medium">Barua Pepe</label>
                    <input type="email" name="barua_pepe" x-model="mteja.barua_pepe"
                        class="w-full border border-amber-300 rounded-xl p-3 focus:ring-4 focus:ring-amber-300 focus:border-amber-400 transition">
                </div>

                <div>
                    <label class="block text-gray-700 mb-1 font-medium">Anapoishi</label>
                    <input type="text" name="anapoishi" x-model="mteja.anapoishi"
                        class="w-full border border-amber-300 rounded-xl p-3 focus:ring-4 focus:ring-amber-300 focus:border-amber-400 transition">
                </div>

                <div>
                    <label class="block text-gray-700 mb-1 font-medium">Tarehe ya Malipo</label>
                    <input type="date" name="tarehe_malipo"
                        class="w-full border border-amber-300 rounded-xl p-3 focus:ring-4 focus:ring-amber-300 focus:border-amber-400 transition" required>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" 
                        @click="showMadeniModal = false"
                        class="bg-gray-400 hover:bg-gray-500 px-5 py-2 rounded-xl text-white font-semibold transition">
                        Funga
                    </button>
                    <button type="submit" 
                        class="bg-emerald-600 hover:bg-emerald-700 px-5 py-2 rounded-xl text-white font-semibold flex items-center gap-1 transition shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Hifadhi
                    </button>
                </div>
            </form>
        </div>
    </div>

<!-- Taarifa Fupi ya Fedha -->
<div class="mt-8">
    <div class="flex items-center justify-between mb-6">
        <h2 class="bg-blue text-2xl font-bold text-gray-800 flex items-center">
            <div class="relative mr-3">
                <div class="w-3 h-8 bg-gradient-to-b from-blue-500 to-purple-600 rounded-full"></div>
                <div class="absolute top-1 -right-1 w-2 h-6 bg-gradient-to-b from-emerald-400 to-teal-600 rounded-full"></div>
            </div>
            Taarifa Fupi ya Mapato (Mauzo & Marejesho) na Matumizi ya Leo
        </h2>

    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
        <!-- Mapato -->
        <div class="group relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl blur-md opacity-60 group-hover:opacity-80 transition duration-500"></div>
            <div class="relative bg-gradient-to-br from-amber-500 via-amber-600 to-amber-700 p-4 rounded-2xl shadow-xl border border-blue-400/30 transform transition duration-300 group-hover:scale-105 group-hover:shadow-2xl cursor-pointer">
                <div class="flex justify-between items-start mb-3">
                    <div class="p-2 bg-white/20 rounded-xl backdrop-blur-sm">
                        <i class="fas fa-money-bill-wave text-white text-lg"></i>
                    </div>
                    <i class="fas fa-arrow-right text-white/60 text-sm group-hover:translate-x-1 transition-transform"></i>
                </div>
                <div class="text-xs font-semibold text-blue-100 uppercase tracking-wide mb-1">Mapato</div>
                <div class="font-bold text-2xl text-white mb-1" x-text="totalMapato()"></div>
                <div class="text-xs text-blue-100 flex items-center">
                    <i class="fas fa-trending-up mr-1"></i>
                    Jumla ya mauzo
                </div>
            </div>
        </div>

        <!-- Faida -->
        <div class="group relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-green-500 to-green-600 rounded-2xl blur-md opacity-60 group-hover:opacity-80 transition duration-500"></div>
            <div class="relative bg-gradient-to-br from-green-500 via-green-600 to-emerald-700 p-4 rounded-2xl shadow-xl border border-green-400/30 transform transition duration-300 group-hover:scale-105 group-hover:shadow-2xl cursor-pointer">
                <div class="flex justify-between items-start mb-3">
                    <div class="p-2 bg-white/20 rounded-xl backdrop-blur-sm">
                        <i class="fas fa-chart-line text-white text-lg"></i>
                    </div>
                    <i class="fas fa-arrow-right text-white/60 text-sm group-hover:translate-x-1 transition-transform"></i>
                </div>
                <div class="text-xs font-semibold text-green-100 uppercase tracking-wide mb-1">Faida</div>
                <div class="font-bold text-2xl text-white mb-1" x-text="totalFaida()"></div>
                <div class="text-xs text-green-100 flex items-center">
                    <i class="fas fa-calculator mr-1"></i>
                    Mapato - Gharama
                </div>
            </div>
        </div>

        <!-- Matumizi -->
        <div class="group relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-amber-500 to-amber-600 rounded-2xl blur-md opacity-60 group-hover:opacity-80 transition duration-500"></div>
            <div class="relative bg-gradient-to-br from-amber-500 via-amber-600 to-orange-700 p-4 rounded-2xl shadow-xl border border-amber-400/30 transform transition duration-300 group-hover:scale-105 group-hover:shadow-2xl cursor-pointer">
                <div class="flex justify-between items-start mb-3">
                    <div class="p-2 bg-white/20 rounded-xl backdrop-blur-sm">
                        <i class="fas fa-receipt text-white text-lg"></i>
                    </div>
                    <i class="fas fa-arrow-right text-white/60 text-sm group-hover:translate-x-1 transition-transform"></i>
                </div>
                <div class="text-xs font-semibold text-amber-100 uppercase tracking-wide mb-1">Matumizi</div>
                <div class="font-bold text-2xl text-white mb-1" x-text="totalMatumizi()"></div>
                <div class="text-xs text-amber-100 flex items-center">
                    <i class="fas fa-hand-holding-usd mr-1"></i>
                    Gharama zote
                </div>
            </div>
        </div>

        <!-- Fedha Leo -->
        <div class="group relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-cyan-500 to-cyan-700 rounded-2xl blur-md opacity-60 group-hover:opacity-80 transition duration-500"></div>
            <div class="relative bg-gradient-to-br from-emerald-500 via-emerald-600 to-emerald-700 p-4 rounded-2xl shadow-xl border border-purple-400/30 transform transition duration-300 group-hover:scale-105 group-hover:shadow-2xl cursor-pointer">
                <div class="flex justify-between items-start mb-3">
                    <div class="p-2 bg-white/20 rounded-xl backdrop-blur-sm">
                        <i class="fas fa-wallet text-white text-lg"></i>
                    </div>
                    <i class="fas fa-arrow-right text-white/60 text-sm group-hover:translate-x-1 transition-transform"></i>
                </div>
                <div class="text-xs font-semibold text-purple-100 uppercase tracking-wide mb-1">Fedha Leo</div>
                <div class="font-bold text-2xl text-white mb-1" x-text="totalFedhaLeo()"></div>
                <div class="text-xs text-purple-100 flex items-center">
                    <i class="fas fa-cash-register mr-1"></i>
                    Fedha taslimu
                </div>
            </div>
        </div>

        <!-- Faida Halisi Leo -->
        <div class="group relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-teal-500 to-teal-600 rounded-2xl blur-md opacity-60 group-hover:opacity-80 transition duration-500"></div>
            <div class="relative bg-gradient-to-br from-amber-500 via-amber-600 to-amber-700 p-4 rounded-2xl shadow-xl border border-teal-400/30 transform transition duration-300 group-hover:scale-105 group-hover:shadow-2xl cursor-pointer">
                <div class="flex justify-between items-start mb-3">
                    <div class="p-2 bg-white/20 rounded-xl backdrop-blur-sm">
                        <i class="fas fa-chart-pie text-white text-lg"></i>
                    </div>
                    <i class="fas fa-arrow-right text-white/60 text-sm group-hover:translate-x-1 transition-transform"></i>
                </div>
                <div class="text-xs font-semibold text-teal-100 uppercase tracking-wide mb-1">Faida Halisi</div>
                <div class="font-bold text-2xl text-white mb-1" x-text="totalFaidaHalisi()"></div>
                <div class="text-xs text-teal-100 flex items-center">
                    <i class="fas fa-filter mr-1"></i>
                    Baada ya matumizi
                </div>
            </div>
        </div>

        <!-- Jumla Kuu -->
        <div class="group relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-rose-500 to-rose-600 rounded-2xl blur-md opacity-60 group-hover:opacity-80 transition duration-500"></div>
            <div class="relative bg-gradient-to-br from-green-500 via-green-600 to-green-700 p-4 rounded-2xl shadow-xl border border-rose-400/30 transform transition duration-300 group-hover:scale-105 group-hover:shadow-2xl cursor-pointer">
                <div class="flex justify-between items-start mb-3">
                    <div class="p-2 bg-white/20 rounded-xl backdrop-blur-sm">
                        <i class="fas fa-chart-bar text-white text-lg"></i>
                    </div>
                    <i class="fas fa-arrow-right text-white/60 text-sm group-hover:translate-x-1 transition-transform"></i>
                </div>
                <div class="text-xs font-semibold text-rose-100 uppercase tracking-wide mb-1">Jumla Kuu</div>
                <div class="font-bold text-2xl text-white mb-1" x-text="totalJumlaKuu()"></div>
                <div class="text-xs text-rose-100 flex items-center">
                    <i class="fas fa-balance-scale mr-1"></i>
                    Muhtasari mkuu
                </div>
            </div>
        </div>
    </div>

</div>
</div>

<!-- Mauzo kwa Barcode Tab -->
<div x-show="activeTab==='barcode'" 
     class="rounded-2xl shadow-md border border-green-100 bg-gradient-to-b from-amber-400 to-amber-500 p-8 mb-8 transition-all duration-300">

    <!-- Header -->
    <div class="flex items-center mb-6">
        <div class="bg-green-600 text-white p-2 rounded-full shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
            </svg>
        </div>
        <h2 class="ml-3 text-xl font-bold text-black tracking-wide">
            Mauzo kwa Barcode
        </h2>
    </div>

    <!-- Instructions -->
    <p class="text-black-600 mb-5 text-sm">
        Ingiza au scan bidhaa kwa kutumia <span class="font-semibold text-green-700">Barcode</span> ili kuongeza moja kwa moja kwenye mauzo.
    </p>

    <!-- Barcode Form -->
    <form @submit.prevent="submitBarcodeMauzo" class="space-y-6">

        <!-- Table -->
        <div class="overflow-x-auto rounded-xl shadow-sm border border-green-100 bg-white/80 backdrop-blur-sm">
            <table class="w-full table-auto border-collapse">
                <thead class="bg-green-400/70 text-black-800 text-sm uppercase tracking-wide">
                    <tr>
                        <th class="border px-3 py-2 text-left">Barcode</th>
                        <th class="border px-3 py-2 text-left">Bidhaa</th>
                        <th class="border px-3 py-2 text-left">Bei</th>
                        <th class="border px-3 py-2 text-left">Idadi</th>
                        <th class="border px-3 py-2 text-left">Baki</th>
                        <th class="border px-3 py-2 text-left">Jumla</th>
                        <th class="border px-3 py-2 text-center">Futa</th>
                    </tr>
                </thead>

                <tbody>
                    <template x-for="(item, index) in barcodeCart" :key="index">
                        <tr class="border-b hover:bg-green-50 transition-colors duration-200">
                            <!-- Barcode -->
                            <td class="px-3 py-2">
                                <input type="text" 
                                       x-model="item.barcode" 
                                       @change="fetchBidhaa(item, index)" 
                                       placeholder="Ingiza Barcode hapa" 
                                       class="border border-green-200 focus:border-green-500 focus:ring-2 focus:ring-green-300 rounded-lg p-2 w-full text-sm transition-all" />
                            </td>

                            <!-- Bidhaa -->
                            <td class="px-3 py-2">
                                <input type="text" 
                                       x-model="item.jina" readonly 
                                       placeholder="Jina la Bidhaa" 
                                       class="border border-green-100 bg-gray-50 text-gray-700 rounded-lg p-2 w-full text-sm" />
                            </td>

                            <!-- Bei -->
                            <td class="px-3 py-2">
                                <input type="number" 
                                       x-model="item.bei" readonly 
                                       placeholder="Bei" 
                                       class="border border-green-100 bg-gray-50 text-gray-700 rounded-lg p-2 w-full text-sm" />
                            </td>

                            <!-- Idadi -->
                            <td class="px-3 py-2">
                                <input type="number" 
                                       x-model.number="item.idadi" 
                                       min="1" 
                                       :max="item.stock" 
                                       @input="updateJumla(item)" 
                                       placeholder="Idadi" 
                                       class="border border-green-200 focus:border-green-500 focus:ring-2 focus:ring-green-300 rounded-lg p-2 w-full text-sm transition-all" />
                            </td>

                            <!-- Baki -->
                            <td class="px-3 py-2">
                                <input type="number" 
                                       x-model="item.stock" readonly 
                                       placeholder="Baki" 
                                       class="border border-green-100 bg-gray-50 text-gray-700 rounded-lg p-2 w-full text-sm" />
                            </td>

                            <!-- Jumla -->
                            <td class="px-3 py-2">
                                <input type="number" 
                                       x-model="item.jumla" readonly 
                                       placeholder="Jumla" 
                                       class="border border-green-100 bg-gray-50 text-gray-700 rounded-lg p-2 w-full text-sm" />
                            </td>

                            <!-- Delete Button -->
                            <td class="px-3 py-2 text-center">
                                <button type="button" 
                                        @click="removeItem(index)" 
                                        class="text-red-500 hover:text-red-700 p-1.5 rounded-full transition transform hover:scale-110" 
                                        title="Futa bidhaa">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" 
                                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Total & Action -->
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 mt-6">
            <div class="text-lg font-semibold text-gray-800">
                Jumla ya Mauzo: 
                <span class="text-green-700 font-bold" x-text="barcodeCartTotal()"></span>
            </div>

            <button type="submit" 
                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-2.5 rounded-lg font-semibold shadow-md flex items-center transition-all duration-300 hover:scale-[1.02]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" 
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M5 13l4 4L19 7" />
                </svg>
                Uza Bidhaa
            </button>
        </div>
    </form>
</div>

            <!-- Taarifa Fupi Tab -->
            <div x-show="activeTab==='taarifa'" x-data="{ search:'', deleteId:null }" class="bg-green-50 rounded-lg shadow-sm border p-6 mb-6">
                <h2 class="text-lg font-semibold mb-4 flex items-center text-black-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Taarifa Fupi ya Mauzo
                </h2>

                <!-- Search and Print in single line -->
                <div class="flex items-center gap-2 mb-4">
                    <div class="relative flex-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-3 top-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" x-model="search" placeholder="Tafuta kwa jina la bidhaa au tarehe..." class="pl-10 w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>
                    <button @click="window.print()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center transition shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Print
                    </button>
                </div>

                <div class="overflow-x-auto rounded-lg shadow-sm border">
                    <table class="w-full table-auto border-collapse">
                        <thead>
                            <tr class="bg-amber-600">
                                <th class="border px-3 py-2 text-left text-white-700">Tarehe</th>
                                <th class="border px-3 py-2 text-left text-white-700">Bidhaa</th>
                                <th class="border px-3 py-2 text-left text-black-700">Idadi</th>
                                <th class="border px-3 py-2 text-left text-black-700">Bei</th>
                                <th class="border px-3 py-2 text-left text-black-700">Punguzo</th>
                                <th class="border px-3 py-2 text-left text-black-700">Jumla</th>
                                <th class="border px-3 py-2 text-left text-black-700">Futa</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $today = \Carbon\Carbon::today()->format('Y-m-d'); @endphp
                            @forelse($mauzos as $item)
                            @php $itemDate = $item->created_at->format('Y-m-d'); @endphp
                            <tr x-show="'{{ strtolower($item->bidhaa->jina) }}'.includes(search.toLowerCase()) || '{{ $itemDate }}'.includes(search)" class="hover:bg-gray-50 transition">
                                <td class="border px-3 py-2">
                                    @if($itemDate === $today)
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded font-semibold text-xs">Leo ({{ $itemDate }})</span>
                                    @else
                                        {{ $itemDate }}
                                    @endif
                                </td>
                                <td class="border px-3 py-2">{{ $item->bidhaa->jina }}</td>
                                <td class="border px-3 py-2">{{ $item->idadi }}</td>
                                <td class="border px-3 py-2">{{ number_format($item->bei) }}</td>
                                <td class="border px-3 py-2">{{ number_format($item->punguzo) }}</td>
                                <td class="border px-3 py-2">{{ number_format($item->jumla) }}</td>
                                <td class="border px-3 py-2 text-center">
                                    <button type="button"
                                        @click="deleteId={{ $item->id }}"
                                        class="bg-green-200 hover:bg-green-400 text-black-700 px-3 py-1 rounded-lg flex items-center transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Futa
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-gray-500">Hakuna mauzo yaliyorekodiwa bado.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Delete Confirmation Modal (Taarifa Fupi tu) -->
                <div x-show="deleteId" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
                    <div class="bg-white p-6 rounded-xl shadow-2xl w-full max-w-md text-center">
                        <h4 class="text-lg font-semibold mb-4 text-gray-800">Futa Mauzo</h4>
                        <p class="mb-4 text-gray-600">Una uhakika unataka kufuta mauzo haya?</p>
                        <div class="flex justify-center gap-4">
                            <button type="button" @click="deleteId=null"
                                    class="bg-gray-400 hover:bg-green-500 px-4 py-2 rounded-lg text-white transition">
                                Funga
                            </button>
                            <form :action="`/mauzo/${deleteId}`" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-green-500 hover:bg-green-700 px-4 py-2 rounded-lg text-white flex items-center transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Futa
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mauzo ya Jumla Tab -->
            <div x-show="activeTab==='jumla'" class="bg-green-100 rounded-lg shadow-sm border p-6 mb-6">
                <h2 class="text-lg font-bold mb-4 flex items-center text-black-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Mauzo ya Jumla (Kwa Tarehe & Bidhaa)
                </h2>

     <!-- Search Area with Labels -->
 <!-- Search Area with Live Filter and Print -->
<div class="flex flex-wrap items-end gap-3 mb-4">

    <div class="flex flex-col flex-1">
        <label class="text-sm text-gray-700 font-medium mb-1">Tafuta Bidhaa / Mauzo</label>
        <input type="text" 
               x-model.debounce.500ms="searchProduct" 
               placeholder="Andika jina la bidhaa au mauzo..." 
               class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-green-600 focus:border-green-600 transition">
    </div>

    <!-- Buttons -->
    <div class="flex items-center gap-2 mb-1">
        <button @click="printTable"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" 
                 class="h-5 w-5 mr-2" 
                 fill="none" 
                 viewBox="0 0 24 24" 
                 stroke="currentColor">
                <path stroke-linecap="round" 
                      stroke-linejoin="round" 
                      stroke-width="2"
                      d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Print
        </button>
    </div>
</div>


                <div class="overflow-x-auto rounded-lg shadow-sm border">
                    <table class="w-full border-collapse">
                        <thead class="bg-green-600">
                            <tr>
                                <th class="border px-3 py-2 text-left text-black-700">Tarehe</th>
                                <th class="border px-3 py-2 text-left text-black-700">Bidhaa</th>
                                <th class="border px-3 py-2 text-left text-black-700">Idadi</th>
                                <th class="border px-3 py-2 text-left text-black-700">Bei ya Jumla</th>
                                <th class="border px-3 py-2 text-left text-black-700">Punguzo</th>
                                <th class="border px-3 py-2 text-left text-black-700">Faida</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="item in groupedMauzoByDate()" :key="item.tarehe + '-' + item.jina">
                                <tr class="border-b hover:bg-gray-50 transition">
                                    <td class="border px-3 py-2" x-text="item.tarehe"></td>
                                    <td class="border px-3 py-2" x-text="item.jina"></td>
                                    <td class="border px-3 py-2 text-center" x-text="item.idadi"></td>
                                    <td class="border px-3 py-2 text-right" x-text="item.jumla.toLocaleString()"></td>
                                    <td class="border px-3 py-2 text-right" x-text="item.punguzo.toLocaleString()"></td>
                                    <td class="border px-3 py-2 text-right" x-text="item.faida.toLocaleString()"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 🧺 Kikapu Modal -->
            <div 
                x-show="showKikapu" 
                x-cloak 
                class="fixed inset-y-0 left-64 right-0 bg-black bg-opacity-40 flex items-center justify-center z-40 p-4"
            >
                <div class="bg-white w-full max-w-4xl p-6 rounded-2xl shadow-2xl max-h-[85vh] overflow-auto relative">

                    <!-- Header -->
                    <div class="flex justify-between items-center mb-4 border-b pb-2">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Bidhaa Zilizo Kwenye Kikapu
                        </h2>
                        <button 
                            @click="showKikapu = false" 
                            class="text-red-600 hover:text-red-800 font-bold text-2xl leading-none transition"
                            title="Funga"
                        >&times;</button>
                    </div>

                    <!-- Empty Kikapu -->
                    <template x-if="cart.length === 0">
                        <div class="text-center text-gray-600 py-10">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Hakuna bidhaa kwenye kikapu kwa sasa.
                        </div>
                    </template>

                    <!-- Kikapu Items -->
                    <template x-if="cart.length > 0">
                        <div>
                            <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                                <table class="w-full text-sm">
                                    <thead class="bg-gray-100 text-gray-700">
                                        <tr>
                                            <th class="border px-3 py-2 text-left">S/N</th>
                                            <th class="border px-3 py-2 text-left">Bidhaa</th>
                                            <th class="border px-3 py-2 text-left">Idadi</th>
                                            <th class="border px-3 py-2 text-left">Bei</th>
                                            <th class="border px-3 py-2 text-left">Punguzo</th>
                                            <th class="border px-3 py-2 text-left">Faida</th>
                                            <th class="border px-3 py-2 text-left">Muda</th>
                                            <th class="border px-3 py-2 text-left">Ondoa</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(item, index) in cart" :key="index">
                                            <tr class="border-b hover:bg-gray-50 transition">
                                                <td class="border px-3 py-2 text-center" x-text="index + 1"></td>
                                                <td class="border px-3 py-2" x-text="item.jina"></td>
                                                <td class="border px-3 py-2 text-center" x-text="item.idadi"></td>
                                                <td class="border px-3 py-2 text-right" x-text="item.bei.toLocaleString()"></td>
                                                <td class="border px-3 py-2 text-right" x-text="item.punguzo.toLocaleString()"></td>
                                                <td class="border px-3 py-2 text-right" x-text="item.faida.toLocaleString()"></td>
                                                <td class="border px-3 py-2 text-center" x-text="item.muda"></td>
                                                <td class="border px-3 py-2 text-center">
                                                    <button 
                                                        @click="removeFromCart(index)" 
                                                        class="text-red-500 hover:text-red-700 transition"
                                                        title="Ondoa Bidhaa">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Total -->
                            <div class="text-right font-semibold text-gray-800 my-4 text-lg">
                                Jumla ya gharama: 
                                <span class="text-green-600" x-text="cartTotal()"></span>
                            </div>

                            <!-- Actions -->
                            <div class="flex flex-wrap justify-end gap-3">
                                <button 
                                    @click="clearCart" 
                                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center transition shadow-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Futa Kikapu
                                </button>

                                <button 
                                    @click="checkoutCart" 
                                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center transition shadow-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Funga Kapu
                                </button>

                                <button 
                                    @click="openKopeshaModal" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition shadow-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                    </svg>
                                    Kopesha Mteja
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- 💳 Kopesha Mteja Modal -->
            <div 
                x-show="showKopesha" 
                x-cloak 
                class="fixed inset-y-0 left-64 right-0 bg-black bg-opacity-40 flex items-center justify-center z-50 p-4"
            >
                <div 
                    class="bg-white w-full max-w-md p-6 rounded-2xl shadow-2xl max-h-[85vh] overflow-auto"
                    x-transition
                >
                    <!-- Header -->
                    <div class="flex justify-between items-center mb-4 border-b pb-2">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                            </svg>
                            Kopesha Mteja
                        </h2>
                        <button 
                            @click="showKopesha = false" 
                            class="text-red-600 hover:text-red-800 font-bold text-2xl leading-none transition"
                            title="Funga"
                        >&times;</button>
                    </div>

                    <!-- Form -->
                    <form @submit.prevent="submitLoan" class="space-y-4">
                        <!-- Mteja -->
                        <div>
                            <label class="font-medium text-gray-700">Chagua Mteja</label>
                            <select 
                                x-model="selectedMteja" 
                                class="w-full border border-gray-300 rounded-lg p-3 mt-1 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                required
                            >
                                <option value="">-- Chagua Mteja --</option>
                                <template x-for="m in wateja" :key="m.id">
                                    <option :value="m.jina" x-text="m.jina"></option>
                                </template>
                            </select>
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-end gap-3 pt-4">
                            <button 
                                type="button" 
                                @click="showKopesha = false"
                                class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center transition shadow-sm"
                            >
                                Ghairi
                            </button>

                            <button 
                                type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition shadow-sm"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Thibitisha Kopesha
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </main>
    </div>
</div>

<script>
function mauzoApp() {
    return {
        // Sidebar & Tabs
        sidebarOpen: true,
        activeTab: 'sehemu',

        // Modals & customer data
        showMadeniModal: false,
        showKikapu: false,
        showKopesha: false,
        selectedMtejaId: '',
        selectedMteja: '',
        newMteja: '',
        wateja: @json($wateja),
        mteja: { jina: '', simu: '', barua_pepe: '', anapoishi: '' },
        
        groupedMauzoByDate() {
            const grouped = {};
            this.mapatoList.forEach(item => {
                const date = item.created_at.split('T')[0]; // YYYY-MM-DD
                const key = date + '|' + item.bidhaa.jina; // group by date + product
                if (!grouped[key]) {
                    grouped[key] = {
                        tarehe: date,
                        jina: item.bidhaa.jina,
                        idadi: 0,
                        jumla: 0,
                        punguzo: 0,
                        faida: 0,
                    };
                }
                grouped[key].idadi += item.idadi;
                grouped[key].jumla += parseFloat(item.jumla);
                grouped[key].punguzo += parseFloat(item.punguzo || 0);
                const beiKuuza = parseFloat(item.bidhaa.bei_kuuza || 0);
                const beiNunua = parseFloat(item.bidhaa.bei_nunua || 0);
                grouped[key].faida += (beiKuuza - beiNunua) * item.idadi;
            });
            return Object.values(grouped).sort((a, b) => b.tarehe.localeCompare(a.tarehe));
        },

        // Fill customer details
        fillMtejaDetails() {
            const select = document.querySelector('select[x-model="selectedMtejaId"]');
            const option = select?.options[select.selectedIndex];
            if (!option || !option.value) {
                this.mteja = { jina: '', simu: '', barua_pepe: '', anapoishi: '' };
                return;
            }
            this.mteja = {
                jina: option.dataset.jina || '',
                simu: option.dataset.simu || '',
                barua_pepe: option.dataset.barua_pepe || '',
                anapoishi: option.dataset.anapoishi || '',
            };
        },

        // --- Kikapu (Cart) ---
        cart: [],

        addToCart() {
            if (!this.bidhaaSelected || this.idadi < 1) {
                alert('Tafadhali chagua bidhaa na idadi sahihi!');
                return;
            }

            const product = this.bidhaaList.find(b => b.id == this.bidhaaSelected);
            if (!product) return alert('Bidhaa haipo!');

            this.cart.push({
                jina: product.jina,
                bei: this.bei,
                idadi: this.idadi,
                punguzo: this.punguzoCheck ? this.punguzo : 0,
                faida: (this.bei - (product.bei_nunua || 0)) * this.idadi,
                muda: new Date().toLocaleTimeString(),
            });

            this.bidhaaSelected = '';
            this.idadi = 1;
            this.punguzo = 0;
            alert('Bidhaa imeongezwa kwenye kikapu!');
        },

        removeFromCart(index) {
            this.cart.splice(index, 1);
        },

        clearCart() {
            if (confirm('Una uhakika unataka kufuta bidhaa zote kwenye kikapu?')) {
                this.cart = [];
            }
        },

        cartTotal() {
            return this.cart.reduce(
                (sum, item) => sum + (item.bei * item.idadi - item.punguzo),
                0
            ).toLocaleString() + '/=';
        },

        // --- Mauzo ya Kikapu ---
        checkoutCart() {
            if (this.cart.length === 0) return alert('Kikapu hakina bidhaa!');

            fetch("{{ route('mauzo.store.kikapu') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content
                },
                body: JSON.stringify({ items: this.cart })
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message || 'Mauzo yamehifadhiwa kwa mafanikio!');
                this.cart = [];
                this.showKikapu = false;
                window.location.reload();
            })
            .catch(() => alert('Kuna tatizo kwenye kuhifadhi mauzo ya kikapu!'));
        },

        // --- Kopesha (Loan Sale) ---
        openKopeshaModal() {
            if (this.cart.length === 0) {
                alert('Hakuna bidhaa kwenye kikapu.');
                return;
            }
            this.showKopesha = true;
        },

        submitLoan() {
            if (!this.selectedMteja) {
                alert('Tafadhali chagua mteja.');
                return;
            }

            fetch("{{ route('mauzo.store.kopesha') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    jina: this.selectedMteja,
                    items: this.cart
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    alert(data.message || 'Mauzo yamekopeshwa kwa mafanikio!');
                    this.cart = [];
                    this.showKopesha = false;
                    this.showKikapu = false;
                    // 🔁 Refresh page automatically
                    window.location.reload();
                } else {
                    alert('Kuna tatizo: ' + (data.message || 'Jaribu tena.'));
                }
            })
            .catch(err => console.error(err));
        },

        kopeshaCart() {
            if (this.cart.length === 0) return alert('Kikapu hakina bidhaa!');
            const jina = prompt('Ingiza jina la mteja anayekopeshwa:');
            if (!jina) return;

            fetch("{{ route('mauzo.store.kopesha') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content
                },
                body: JSON.stringify({ jina, items: this.cart })
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message || 'Bidhaa zimekopeshwa kwa mafanikio!');
                this.cart = [];
                this.showKikapu = false;
                window.location.reload();
            })
            .catch(() => alert('Kuna tatizo kwenye kukopesha bidhaa!'));
        },

        // --- Sehemu ya Mauzo ---
        bidhaaSelected: '',
        idadi: 1,
        punguzo: 0,
        punguzoCheck: false,
        stock: 0,
        mapatoList: @json($mauzos),
        matumiziList: @json($matumizi),
        bidhaaList: @json($bidhaa),
        today: "{{ \Carbon\Carbon::today()->format('Y-m-d') }}",

        updateStock() {
            if (!this.bidhaaSelected) {
                this.stock = 0;
                return;
            }
            const product = this.bidhaaList.find(b => b.id == this.bidhaaSelected);
            this.stock = product ? product.idadi : 0;
            if (this.idadi > this.stock) this.idadi = this.stock;
        },

        get bei() {
            if (!this.bidhaaSelected) return 0;
            const option = document.querySelector(
                `select[name=\"bidhaa_id\"] option[value='${this.bidhaaSelected}']`
            );
            return parseFloat(option?.dataset.bei) || 0;
        },

        get jumla() {
            return (this.idadi * this.bei) - (this.punguzoCheck ? this.punguzo : 0);
        },

        decreaseStock() {
            if (this.idadi > this.stock) {
                alert('Idadi uliyoiingiza inazidi idadi iliyopo!');
                return false;
            }
            this.stock -= this.idadi;
            return true;
        },

        // --- Barcode Mauzo ---
        barcodeCart: [{ barcode: '', jina: '', bei: 0, idadi: 1, stock: 0, jumla: 0 }],

        addBarcodeRow() {
            this.barcodeCart.push({ barcode: '', jina: '', bei: 0, idadi: 1, stock: 0, jumla: 0 });
        },

        fetchBidhaa(item, index) {
            if (!item || !item.barcode) return;
            const product = this.bidhaaList.find(b => b.barcode === item.barcode);
            if (!product) {
                alert('Bidhaa haipatikani kwa barcode hii!');
                Object.assign(item, { jina: '', bei: 0, stock: 0, idadi: 1, jumla: 0 });
                return;
            }
            item.jina = product.jina;
            item.bei = parseFloat(product.bei_kuuza);
            item.stock = parseInt(product.idadi);
            item.idadi = 1;
            item.jumla = item.bei * item.idadi;
            if (index === this.barcodeCart.length - 1) this.addBarcodeRow();
        },

        updateJumla(item) {
            if (item.idadi > item.stock) {
                alert('Idadi uliyoiingiza inazidi idadi iliyopo!');
                item.idadi = item.stock;
            }
            item.jumla = item.bei * item.idadi;
        },

        removeItem(index) {
            this.barcodeCart.splice(index, 1);
        },

        barcodeCartTotal() {
            return this.barcodeCart
                .reduce((sum, item) => sum + parseFloat(item.jumla || 0), 0)
                .toLocaleString() + '/=';
        },

        submitBarcodeMauzo() {
            const payload = this.barcodeCart
                .filter(item => item.barcode)
                .map(item => ({
                    barcode: item.barcode,
                    idadi: item.idadi,
                    punguzo: 0
                }));

            if (payload.length === 0) {
                alert('Tafadhali ingiza angalau barcode moja!');
                return;
            }

            fetch("{{ route('mauzo.store.barcode') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content
                },
                body: JSON.stringify({ items: payload })
            })
            .then(async res => {
                if (!res.ok) {
                    const errData = await res.json().catch(() => ({ message: 'Kuna tatizo kwenye uhifadhi!' }));
                    throw errData;
                }
                return res.json();
            })
            .then(data => {
                alert(data.message);
                window.location.reload();
            })
            .catch(err => {
                console.error(err);
                alert(err.message || 'Kuna tatizo kwenye uhifadhi!');
            });
        },

        // --- Summary Functions ---
        filterToday(list) {
            return list.filter(item => item.created_at?.startsWith(this.today));
        },

        totalMapato() {
            return this.filterToday(this.mapatoList)
                .reduce((sum, item) => sum + parseFloat(item.jumla), 0)
                .toLocaleString() + '/=';
        },
        totalMatumizi() {
            return this.filterToday(this.matumiziList)
                .reduce((sum, item) => sum + parseFloat(item.gharama), 0)
                .toLocaleString() + '/=';
        },
        totalFaida() {
            return this.filterToday(this.mapatoList)
                .reduce((sum, item) => {
                    const beiKuuza = parseFloat(item.bidhaa.bei_kuuza || 0);
                    const beiNunua = parseFloat(item.bidhaa.bei_nunua || 0);
                    return sum + ((beiKuuza - beiNunua) * item.idadi);
                }, 0)
                .toLocaleString() + '/=';
        },
        totalFedhaLeo() {
            const mapato = this.filterToday(this.mapatoList)
                .reduce((sum, item) => sum + parseFloat(item.jumla), 0);
            const matumizi = this.filterToday(this.matumiziList)
                .reduce((sum, item) => sum + parseFloat(item.gharama), 0);
            return (mapato - matumizi).toLocaleString() + '/=';
        },
        totalFaidaHalisi() {
            const faida = this.filterToday(this.mapatoList)
                .reduce((sum, item) => {
                    const beiKuuza = parseFloat(item.bidhaa.bei_kuuza || 0);
                    const beiNunua = parseFloat(item.bidhaa.bei_nunua || 0);
                    return sum + ((beiKuuza - beiNunua) * item.idadi);
                }, 0);
            const matumizi = this.filterToday(this.matumiziList)
                .reduce((sum, item) => sum + parseFloat(item.gharama), 0);
            return (faida - matumizi).toLocaleString() + '/=';
        },
        totalJumlaKuu() {
            return this.mapatoList
                .reduce((sum, item) => sum + parseFloat(item.jumla), 0)
                .toLocaleString() + '/=';
        }
    };
}
</script>

</body>
</html>