@extends('layouts.app')

@section('title', 'Wafanyakazi - DEMODAY')

@section('page-title', 'Wafanyakazi')
@section('page-subtitle', 'Usimamizi wa wafanyakazi wote - ' . now()->format('d/m/Y'))

@section('content')
<div x-data="wafanyakaziApp()" class="space-y-6">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 card-hover">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-green-100 text-green-600 mr-4">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Jumla ya Wafanyakazi</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $wafanyakazi->count() }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 card-hover">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-emerald-100 text-emerald-600 mr-4">
                    <i class="fas fa-user-check text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Wanaoweza Kuingia</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $wafanyakazi->where('getini', 'ingia')->count() }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 card-hover">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-blue-100 text-blue-600 mr-4">
                    <i class="fas fa-venus-mars text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Wanaume</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $wafanyakazi->where('jinsia', 'Mwanaume')->count() }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 card-hover">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-purple-100 text-purple-600 mr-4">
                    <i class="fas fa-user-clock text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Wamesimamishwa</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $wafanyakazi->where('getini', 'simama')->count() }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Page Navigation Tabs -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 card-hover">
        <div class="flex space-x-6 border-b border-gray-200">
            <button 
                @click="activeTab = 'taarifa'" 
                :class="activeTab === 'taarifa' ? 'border-b-2 border-green-500 text-green-600 font-semibold' : 'text-gray-500 hover:text-gray-700'" 
                class="pb-3 px-1 transition-colors flex items-center"
            >
                <i class="fas fa-table mr-2"></i>Taarifa za Wafanyakazi
            </button>
            <button 
                @click="activeTab = 'sajili'" 
                :class="activeTab === 'sajili' ? 'border-b-2 border-green-500 text-green-600 font-semibold' : 'text-gray-500 hover:text-gray-700'" 
                class="pb-3 px-1 transition-colors flex items-center"
            >
                <i class="fas fa-plus-circle mr-2"></i>Sajili Mfanyakazi Mpya
            </button>
        </div>
    </div>

    <!-- Wafanyakazi Information Tab -->
    <div x-show="activeTab === 'taarifa'" class="space-y-6">
        <!-- Search and Actions -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 card-hover">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-800">Orodha ya Wafanyakazi</h2>
                <div class="flex space-x-3">
                    <div class="relative">
                        <input 
                            type="text" 
                            placeholder="Tafuta mfanyakazi..." 
                            x-model="searchQuery"
                            class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        >
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                    <button 
                        onclick="window.print()" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center"
                    >
                        <i class="fas fa-print mr-2"></i> Print
                    </button>
                </div>
            </div>

            <!-- Data Table -->
            <div class="overflow-x-auto">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="bg-gradient-to-r from-green-600 to-green-700">
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white">Mfanyakazi</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white">Mawasiliano</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white">Jinsia</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white">Anuani</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-white">Hali</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-white print:hidden">Vitendo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($wafanyakazi as $mfanyakazi)
                            <tr class="hover:bg-green-50 transition-all duration-200"
                                x-show="searchQuery === '' || 
                                       '{{ strtolower($mfanyakazi->jina) }}'.includes(searchQuery.toLowerCase()) || 
                                       '{{ strtolower($mfanyakazi->simu) }}'.includes(searchQuery.toLowerCase())">
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-12 w-12 bg-gradient-to-r from-green-500 to-green-600 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-lg">
                                            {{ substr($mfanyakazi->jina, 0, 1) }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-semibold text-gray-900">{{ $mfanyakazi->jina }}</div>
                                            <div class="text-xs text-green-600 font-medium">
                                                {{ $mfanyakazi->tarehe_kuzaliwa ? \Carbon\Carbon::parse($mfanyakazi->tarehe_kuzaliwa)->age . ' years' : '--' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4">
                                    <div class="space-y-1">
                                        @if($mfanyakazi->simu)
                                        <div class="flex items-center text-sm text-gray-700">
                                            <i class="fas fa-phone text-green-500 mr-2 w-4"></i>
                                            {{ $mfanyakazi->simu }}
                                        </div>
                                        @endif
                                        @if($mfanyakazi->barua_pepe)
                                        <div class="flex items-center text-sm text-gray-700">
                                            <i class="fas fa-envelope text-blue-500 mr-2 w-4"></i>
                                            <span class="truncate max-w-xs">{{ $mfanyakazi->barua_pepe }}</span>
                                        </div>
                                        @endif
                                        @if($mfanyakazi->username)
                                        <div class="flex items-center text-sm text-gray-700">
                                            <i class="fas fa-user text-purple-500 mr-2 w-4"></i>
                                            {{ $mfanyakazi->username }}
                                        </div>
                                        @endif
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold 
                                        @if($mfanyakazi->jinsia === 'Mwanaume') bg-blue-100 text-blue-800 border border-blue-200
                                        @else bg-pink-100 text-pink-800 border border-pink-200 @endif">
                                        <i class="fas @if($mfanyakazi->jinsia === 'Mwanaume') fa-mars @else fa-venus @endif mr-1"></i>
                                        {{ $mfanyakazi->jinsia }}
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-700 max-w-xs">
                                        {{ $mfanyakazi->anuani ?: '--' }}
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold 
                                        @if($mfanyakazi->getini === 'ingia') bg-green-100 text-green-800 border border-green-200
                                        @else bg-red-100 text-red-800 border border-red-200 @endif">
                                        <i class="fas @if($mfanyakazi->getini === 'ingia') fa-check-circle @else fa-pause-circle @endif mr-1"></i>
                                        {{ ucfirst($mfanyakazi->getini) }}
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium print:hidden">
                                    <div class="flex justify-center space-x-3">
                                        <button 
                                            @click="showDetails({{ $mfanyakazi->toJson() }})" 
                                            class="text-blue-600 hover:text-blue-800 transition-colors transform hover:scale-110"
                                            title="Angalia Maelezo"
                                        >
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button 
                                            @click="editEmployee({{ $mfanyakazi->toJson() }})" 
                                            class="text-amber-600 hover:text-amber-800 transition-colors transform hover:scale-110"
                                            title="Badili"
                                        >
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button 
                                            @click="deleteEmployee({{ $mfanyakazi->id }}, '{{ $mfanyakazi->jina }}')" 
                                            class="text-red-500 hover:text-red-700 transition-colors transform hover:scale-110"
                                            title="Futa"
                                        >
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @if($mfanyakazi->simu)
                                        <a 
                                            href="tel:{{ $mfanyakazi->simu }}" 
                                            class="text-green-600 hover:text-green-800 transition-colors transform hover:scale-110"
                                            title="Piga Simu"
                                        >
                                            <i class="fas fa-phone"></i>
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fas fa-users text-5xl text-green-300 mb-4"></i>
                                        <p class="text-lg font-semibold text-gray-600 mb-2">Hakuna wafanyakazi bado.</p>
                                        <p class="text-sm text-gray-500 mb-4">Anza kwa kusajili mfanyakazi wako wa kwanza</p>
                                        <button 
                                            @click="activeTab = 'sajili'" 
                                            class="bg-gradient-to-r from-green-600 to-green-700 text-white px-6 py-3 rounded-lg hover:from-green-700 hover:to-green-800 transition-all duration-300 transform hover:scale-105 flex items-center shadow-lg"
                                        >
                                            <i class="fas fa-plus-circle mr-2"></i> Sajili Mfanyakazi wa Kwanza
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <!-- Sajili Wafanyakazi Tab -->
    <div x-show="activeTab === 'sajili'" class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 card-hover">
        <h2 class="text-xl font-bold text-gray-800 mb-6">Sajili Mfanyakazi Mpya</h2>
        <form method="POST" action="{{ route('wafanyakazi.store') }}" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Jina -->
                <div class="relative">
                    <input 
                        type="text" 
                        name="jina" 
                        placeholder=" " 
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                        required
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Jina Kamili *
                    </label>
                </div>

                <!-- Jinsia -->
                <div class="relative">
                    <select 
                        name="jinsia" 
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent appearance-none bg-white" 
                        required
                    >
                        <option value=""> </option>
                        <option value="Mwanaume">Mwanaume</option>
                        <option value="Mwanamke">Mwanamke</option>
                    </select>
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Jinsia *
                    </label>
                    <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                        <i class="fas fa-chevron-down text-gray-400"></i>
                    </div>
                </div>

                <!-- Tarehe ya Kuzaliwa -->
                <div class="relative">
                    <input 
                        type="date" 
                        name="tarehe_kuzaliwa" 
                        placeholder=" " 
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Tarehe ya Kuzaliwa
                    </label>
                </div>

                <!-- Anuani -->
                <div class="relative">
                    <input 
                        type="text" 
                        name="anuani" 
                        placeholder=" " 
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Anuani ya Makazi
                    </label>
                </div>

                <!-- Simu -->
                <div class="relative">
                    <input 
                        type="text" 
                        name="simu" 
                        placeholder=" " 
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Namba ya Simu
                    </label>
                </div>

                <!-- Barua Pepe -->
                <div class="relative">
                    <input 
                        type="email" 
                        name="barua_pepe" 
                        placeholder=" " 
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Barua Pepe
                    </label>
                </div>

                <!-- Ndugu -->
                <div class="relative">
                    <input 
                        type="text" 
                        name="ndugu" 
                        placeholder=" " 
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Jina la Ndugu
                    </label>
                </div>

                <!-- Simu ya Ndugu -->
                <div class="relative">
                    <input 
                        type="text" 
                        name="simu_ndugu" 
                        placeholder=" " 
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Simu ya Ndugu
                    </label>
                </div>

                <!-- Username -->
                <div class="relative">
                    <input 
                        type="text" 
                        name="username" 
                        placeholder=" " 
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                        required
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Neno la Kuingia (Username) *
                    </label>
                </div>

                <!-- Password -->
                <div class="relative">
                    <input 
                        type="password" 
                        name="password" 
                        placeholder=" " 
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                        required
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Neno la Siri (Password) *
                    </label>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4 pt-6 border-t border-gray-200">
                <button 
                    type="submit" 
                    class="bg-gradient-to-r from-green-600 to-green-700 text-white px-8 py-3 rounded-lg hover:from-green-700 hover:to-green-800 transition-all duration-300 transform hover:scale-105 flex items-center shadow-lg"
                >
                    <i class="fas fa-save mr-2"></i> Hifadhi Mfanyakazi
                </button>
                <button 
                    type="reset" 
                    class="bg-gray-300 text-gray-700 px-8 py-3 rounded-lg hover:bg-gray-400 transition-colors flex items-center"
                >
                    <i class="fas fa-redo mr-2"></i> Safisha Fomu
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Details Modal -->
<div 
    x-show="showDetailsModal"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center modal-overlay"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
>
    <div 
        class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4"
        @click.outside="showDetailsModal = false"
    >
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Taarifa Kamili za Mfanyakazi</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Jina Kamili:</label>
                        <p class="text-gray-900 font-semibold" x-text="selectedEmployee?.jina"></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Jinsia:</label>
                        <p class="text-gray-900" x-text="selectedEmployee?.jinsia"></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Tarehe ya Kuzaliwa:</label>
                        <p class="text-gray-900" x-text="selectedEmployee?.tarehe_kuzaliwa"></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Anuani:</label>
                        <p class="text-gray-900" x-text="selectedEmployee?.anuani || '--'"></p>
                    </div>
                </div>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Simu:</label>
                        <p class="text-gray-900" x-text="selectedEmployee?.simu || '--'"></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Barua Pepe:</label>
                        <p class="text-gray-900" x-text="selectedEmployee?.barua_pepe || '--'"></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Ndugu:</label>
                        <p class="text-gray-900" x-text="selectedEmployee?.ndugu || '--'"></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Simu ya Ndugu:</label>
                        <p class="text-gray-900" x-text="selectedEmployee?.simu_ndugu || '--'"></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Neno la Kuingia:</label>
                        <p class="text-gray-900 font-semibold" x-text="selectedEmployee?.username"></p>
                    </div>
                </div>
            </div>
            <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-sm text-green-700">
                    <i class="fas fa-info-circle mr-2"></i>
                    Leo tarehe {{ \Carbon\Carbon::now()->translatedFormat('d F, Y') }}, 
                    Una jumla ya wafanyakazi {{ count($wafanyakazi) }}.
                </p>
            </div>
        </div>
        <div class="flex justify-end p-6 border-t border-gray-200">
            <button 
                @click="showDetailsModal = false"
                class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors"
            >
                Funga
            </button>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div 
    x-show="showEditModal"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center modal-overlay"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
>
    <div 
        class="bg-white rounded-2xl shadow-xl w-full max-w-4xl mx-4 max-h-[90vh] overflow-y-auto"
        @click.outside="showEditModal = false"
    >
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Badili Taarifa za Mfanyakazi</h3>
        </div>
        <form :action="'/wafanyakazi/' + editingEmployee?.id" method="POST" class="p-6">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Jina -->
                <div class="relative">
                    <input 
                        type="text" 
                        name="jina" 
                        x-model="editingEmployee.jina"
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                        required
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Jina Kamili *
                    </label>
                </div>

                <!-- Jinsia -->
                <div class="relative">
                    <select 
                        name="jinsia" 
                        x-model="editingEmployee.jinsia"
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent appearance-none bg-white" 
                        required
                    >
                        <option value="Mwanaume">Mwanaume</option>
                        <option value="Mwanamke">Mwanamke</option>
                    </select>
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Jinsia *
                    </label>
                    <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                        <i class="fas fa-chevron-down text-gray-400"></i>
                    </div>
                </div>

                <!-- Tarehe ya Kuzaliwa -->
                <div class="relative">
                    <input 
                        type="date" 
                        name="tarehe_kuzaliwa" 
                        x-model="editingEmployee.tarehe_kuzaliwa"
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Tarehe ya Kuzaliwa
                    </label>
                </div>

                <!-- Anuani -->
                <div class="relative">
                    <input 
                        type="text" 
                        name="anuani" 
                        x-model="editingEmployee.anuani"
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Anuani
                    </label>
                </div>

                <!-- Simu -->
                <div class="relative">
                    <input 
                        type="text" 
                        name="simu" 
                        x-model="editingEmployee.simu"
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Simu
                    </label>
                </div>

                <!-- Barua Pepe -->
                <div class="relative">
                    <input 
                        type="email" 
                        name="barua_pepe" 
                        x-model="editingEmployee.barua_pepe"
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Barua Pepe
                    </label>
                </div>

                <!-- Getini -->
                <div class="relative">
                    <select 
                        name="getini" 
                        x-model="editingEmployee.getini"
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent appearance-none bg-white"
                    >
                        <option value="simama">Simama</option>
                        <option value="ingia">Ingia</option>
                    </select>
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Hali ya Kuingia
                    </label>
                    <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                        <i class="fas fa-chevron-down text-gray-400"></i>
                    </div>
                </div>

                <!-- Ndugu -->
                <div class="relative">
                    <input 
                        type="text" 
                        name="ndugu" 
                        x-model="editingEmployee.ndugu"
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Ndugu
                    </label>
                </div>

                <!-- Simu ya Ndugu -->
                <div class="relative">
                    <input 
                        type="text" 
                        name="simu_ndugu" 
                        x-model="editingEmployee.simu_ndugu"
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Simu ya Ndugu
                    </label>
                </div>

                <!-- Username -->
                <div class="relative">
                    <input 
                        type="text" 
                        name="username" 
                        x-model="editingEmployee.username"
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                        required
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Username *
                    </label>
                </div>

                <!-- Password -->
                <div class="relative">
                    <input 
                        type="password" 
                        name="password" 
                        placeholder=" " 
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Neno la Siri (Acha tupu kama hutaki kubadilisha)
                    </label>
                </div>
            </div>
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 mt-6">
                <button 
                    type="button" 
                    @click="showEditModal = false"
                    class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors"
                >
                    Ghairi
                </button>
                <button 
                    type="submit" 
                    class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
                >
                    Hifadhi Mabadiliko
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div 
    x-show="showDeleteModal"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center modal-overlay"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
>
    <div 
        class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4"
        @click.outside="showDeleteModal = false"
    >
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Thibitisha Kufuta Mfanyakazi</h3>
        </div>
        <div class="p-6">
            <div class="flex items-center justify-center mb-4">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
            </div>
            <p class="text-gray-700 mb-6 text-center">
                Una uhakika unataka kufuta mfanyakazi "<span x-text="deletingEmployeeName" class="font-semibold"></span>"?
                <br>Hatua hii haiwezi kutenduliwa.
            </p>
            <div class="flex justify-center space-x-3">
                <button 
                    @click="showDeleteModal = false"
                    class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors"
                >
                    Ghairi
                </button>
                <form :action="'/wafanyakazi/' + deletingEmployeeId" method="POST">
                    @csrf
                    @method('DELETE')
                    <button 
                        type="submit" 
                        class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
                    >
                        Ndio, Futa
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function wafanyakaziApp() {
    return {
        activeTab: 'taarifa',
        searchQuery: '',
        showDetailsModal: false,
        showEditModal: false,
        showDeleteModal: false,
        selectedEmployee: {},
        editingEmployee: {},
        deletingEmployeeId: null,
        deletingEmployeeName: '',
        
        showDetails(employee) {
            this.selectedEmployee = { ...employee };
            this.showDetailsModal = true;
        },
        
        editEmployee(employee) {
            this.editingEmployee = { ...employee };
            this.showEditModal = true;
        },
        
        deleteEmployee(id, name) {
            this.deletingEmployeeId = id;
            this.deletingEmployeeName = name;
            this.showDeleteModal = true;
        }
    }
}
</script>
@endpush