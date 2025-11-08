@extends('layouts.app')

@section('title', 'Masaplaya - DEMODAY')

@section('page-title', 'Masaplaya')
@section('page-subtitle', 'Usimamizi wa wasaplaya wote - ' . now()->format('d/m/Y'))

@section('content')
<div x-data="masaplayaApp()" class="space-y-6">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 card-hover">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-green-100 text-green-600 mr-4">
                    <i class="fas fa-truck text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Jumla ya Masaplaya</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $masaplaya->count() }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 card-hover">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-emerald-100 text-emerald-600 mr-4">
                    <i class="fas fa-phone text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Wana Simu</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $masaplaya->where('simu', '!=', '')->count() }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 card-hover">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-blue-100 text-blue-600 mr-4">
                    <i class="fas fa-envelope text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Wana Barua Pepe</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $masaplaya->where('barua_pepe', '!=', '')->count() }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 card-hover">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-purple-100 text-purple-600 mr-4">
                    <i class="fas fa-building text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Wana Ofisi</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $masaplaya->where('ofisi', '!=', '')->count() }}</h3>
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
                <i class="fas fa-table mr-2"></i>Taarifa za Masaplaya
            </button>
            <button 
                @click="activeTab = 'sajili'" 
                :class="activeTab === 'sajili' ? 'border-b-2 border-green-500 text-green-600 font-semibold' : 'text-gray-500 hover:text-gray-700'" 
                class="pb-3 px-1 transition-colors flex items-center"
            >
                <i class="fas fa-plus-circle mr-2"></i>Sajili Masaplaya Mpya
            </button>
        </div>
    </div>

    <!-- Masaplaya Information Tab -->
    <div x-show="activeTab === 'taarifa'" class="space-y-6">
        <!-- Search and Actions -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 card-hover">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-800">Orodha ya Masaplaya</h2>
                <div class="flex space-x-3">
                    <div class="relative">
                        <input 
                            type="text" 
                            placeholder="Tafuta masaplaya..." 
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
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white">Jina la Msaplaya</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white">Mawasiliano</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white">Ofisi</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white">Makazi</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white">Maelezo</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-white print:hidden">Vitendo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($masaplaya as $sap)
                            <tr class="hover:bg-green-50 transition-all duration-200"
                                x-show="searchQuery === '' || 
                                       '{{ strtolower($sap->jina) }}'.includes(searchQuery.toLowerCase()) || 
                                       '{{ strtolower($sap->simu) }}'.includes(searchQuery.toLowerCase()) || 
                                       '{{ strtolower($sap->barua_pepe) }}'.includes(searchQuery.toLowerCase())">
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-12 w-12 bg-gradient-to-r from-green-500 to-green-600 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-lg">
                                            {{ substr($sap->jina, 0, 1) }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-semibold text-gray-900">{{ $sap->jina }}</div>
                                            <div class="text-xs text-green-600 font-medium">Msaplaya</div>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4">
                                    <div class="space-y-1">
                                        @if($sap->simu)
                                        <div class="flex items-center text-sm text-gray-700">
                                            <i class="fas fa-phone text-green-500 mr-2 w-4"></i>
                                            {{ $sap->simu }}
                                        </div>
                                        @endif
                                        @if($sap->barua_pepe)
                                        <div class="flex items-center text-sm text-gray-700">
                                            <i class="fas fa-envelope text-blue-500 mr-2 w-4"></i>
                                            <span class="truncate max-w-xs">{{ $sap->barua_pepe }}</span>
                                        </div>
                                        @endif
                                        @if(!$sap->simu && !$sap->barua_pepe)
                                        <div class="text-sm text-gray-500 italic">Hakuna mawasiliano</div>
                                        @endif
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4">
                                    @if($sap->ofisi)
                                    <div class="flex items-center text-sm text-gray-700">
                                        <i class="fas fa-building text-purple-500 mr-2"></i>
                                        <span class="truncate max-w-xs">{{ $sap->ofisi }}</span>
                                    </div>
                                    @else
                                    <div class="text-sm text-gray-500 italic">--</div>
                                    @endif
                                </td>
                                
                                <td class="px-6 py-4">
                                    @if($sap->anaopoishi)
                                    <div class="flex items-center text-sm text-gray-700">
                                        <i class="fas fa-home text-amber-500 mr-2"></i>
                                        <span class="truncate max-w-xs">{{ $sap->anaopoishi }}</span>
                                    </div>
                                    @else
                                    <div class="text-sm text-gray-500 italic">--</div>
                                    @endif
                                </td>
                                
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-700 max-w-xs">
                                        {{ $sap->maelezo ?: '--' }}
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium print:hidden">
                                    <div class="flex justify-center space-x-3">
                                        <button 
                                            @click="editItem({{ $sap->toJson() }})" 
                                            class="text-amber-600 hover:text-amber-800 transition-colors transform hover:scale-110"
                                            title="Badili"
                                        >
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button 
                                            @click="deleteItem({{ $sap->id }}, '{{ $sap->jina }}')" 
                                            class="text-red-500 hover:text-red-700 transition-colors transform hover:scale-110"
                                            title="Futa"
                                        >
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @if($sap->simu)
                                        <a 
                                            href="tel:{{ $sap->simu }}" 
                                            class="text-green-600 hover:text-green-800 transition-colors transform hover:scale-110"
                                            title="Piga Simu"
                                        >
                                            <i class="fas fa-phone"></i>
                                        </a>
                                        @endif
                                        @if($sap->barua_pepe)
                                        <a 
                                            href="mailto:{{ $sap->barua_pepe }}" 
                                            class="text-blue-600 hover:text-blue-800 transition-colors transform hover:scale-110"
                                            title="Tuma Barua Pepe"
                                        >
                                            <i class="fas fa-envelope"></i>
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fas fa-truck text-5xl text-green-300 mb-4"></i>
                                        <p class="text-lg font-semibold text-gray-600 mb-2">Hakuna masaplaya bado.</p>
                                        <p class="text-sm text-gray-500 mb-4">Anza kwa kusajili msaplaya wako wa kwanza</p>
                                        <button 
                                            @click="activeTab = 'sajili'" 
                                            class="bg-gradient-to-r from-green-600 to-green-700 text-white px-6 py-3 rounded-lg hover:from-green-700 hover:to-green-800 transition-all duration-300 transform hover:scale-105 flex items-center shadow-lg"
                                        >
                                            <i class="fas fa-plus-circle mr-2"></i> Sajili Msaplaya wa Kwanza
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

    <!-- Sajili Masaplaya Tab -->
    <div x-show="activeTab === 'sajili'" class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 card-hover">
        <h2 class="text-xl font-bold text-gray-800 mb-6">Sajili Msaplaya Mpya</h2>
        <form method="POST" action="{{ route('masaplaya.store') }}" class="space-y-6">
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
                        Jina la Msaplaya *
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

                <!-- Ofisi -->
                <div class="relative">
                    <input 
                        type="text" 
                        name="ofisi" 
                        placeholder=" " 
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Mahali Ofisi Ilipo
                    </label>
                </div>

                <!-- Anaopoishi -->
                <div class="relative">
                    <input 
                        type="text" 
                        name="anaopoishi" 
                        placeholder=" " 
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Mahali Anaopoishi
                    </label>
                </div>

                <!-- Maelezo -->
                <div class="relative col-span-2">
                    <textarea 
                        name="maelezo" 
                        placeholder=" " 
                        rows="4"
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-8 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    ></textarea>
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-6 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Maelezo ya Ziada (Hiari)
                    </label>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4 pt-6 border-t border-gray-200">
                <button 
                    type="submit" 
                    class="bg-gradient-to-r from-green-600 to-green-700 text-white px-8 py-3 rounded-lg hover:from-green-700 hover:to-green-800 transition-all duration-300 transform hover:scale-105 flex items-center shadow-lg"
                >
                    <i class="fas fa-save mr-2"></i> Hifadhi Msaplaya
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
        class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4"
        @click.outside="showEditModal = false"
    >
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Badili Taarifa za Msaplaya</h3>
        </div>
        <form :action="'/masaplaya/' + editingItem.id" method="POST" class="p-6">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Jina -->
                <div class="relative">
                    <input 
                        type="text" 
                        name="jina" 
                        x-model="editingItem.jina"
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                        required
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Jina la Msaplaya *
                    </label>
                </div>

                <!-- Simu -->
                <div class="relative">
                    <input 
                        type="text" 
                        name="simu" 
                        x-model="editingItem.simu"
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
                        x-model="editingItem.barua_pepe"
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Barua Pepe
                    </label>
                </div>

                <!-- Ofisi -->
                <div class="relative">
                    <input 
                        type="text" 
                        name="ofisi" 
                        x-model="editingItem.ofisi"
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Mahali Ofisi Ilipo
                    </label>
                </div>

                <!-- Anaopoishi -->
                <div class="relative">
                    <input 
                        type="text" 
                        name="anaopoishi" 
                        x-model="editingItem.anaopoishi"
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Mahali Anaopoishi
                    </label>
                </div>

                <!-- Maelezo -->
                <div class="relative col-span-2">
                    <textarea 
                        name="maelezo" 
                        x-model="editingItem.maelezo"
                        rows="3"
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-8 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    ></textarea>
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-6 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Maelezo ya Ziada
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
            <h3 class="text-lg font-semibold text-gray-800">Thibitisha Kufuta Msaplaya</h3>
        </div>
        <div class="p-6">
            <div class="flex items-center justify-center mb-4">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
            </div>
            <p class="text-gray-700 mb-6 text-center">
                Una uhakika unataka kufuta msaplaya "<span x-text="deletingItemName" class="font-semibold"></span>"?
                <br>Hatua hii haiwezi kutenduliwa.
            </p>
            <div class="flex justify-center space-x-3">
                <button 
                    @click="showDeleteModal = false"
                    class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors"
                >
                    Ghairi
                </button>
                <form :action="'/masaplaya/' + deletingItemId" method="POST">
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
function masaplayaApp() {
    return {
        activeTab: 'taarifa',
        searchQuery: '',
        showEditModal: false,
        showDeleteModal: false,
        editingItem: {},
        deletingItemId: null,
        deletingItemName: '',
        
        editItem(item) {
            this.editingItem = { ...item };
            this.showEditModal = true;
        },
        
        deleteItem(id, name) {
            this.deletingItemId = id;
            this.deletingItemName = name;
            this.showDeleteModal = true;
        }
    }
}
</script>
@endpush