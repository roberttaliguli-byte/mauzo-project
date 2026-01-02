@extends('layouts.app')

@section('title', 'Masaplaya - DEMODAY')

@section('page-title', 'Masaplaya')
@section('page-subtitle', 'Usimamizi wa wasaplaya wote - ' . now()->format('d/m/Y'))

@section('content')
<div class="space-y-6">
    <!-- Statistics Cards - Made responsive -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 md:p-6 card-hover">
            <div class="flex items-center">
                <div class="p-2 md:p-3 rounded-lg bg-green-100 text-green-600 mr-3 md:mr-4">
                    <i class="fas fa-truck text-lg md:text-xl"></i>
                </div>
                <div>
                    <p class="text-xs md:text-sm text-gray-500 font-medium">Jumla ya Masaplaya</p>
                    <h3 class="text-xl md:text-2xl font-bold text-gray-800">{{ $masaplaya->count() }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 md:p-6 card-hover">
            <div class="flex items-center">
                <div class="p-2 md:p-3 rounded-lg bg-blue-100 text-blue-600 mr-3 md:mr-4">
                    <i class="fas fa-phone text-lg md:text-xl"></i>
                </div>
                <div>
                    <p class="text-xs md:text-sm text-gray-500 font-medium">Wenye Simu</p>
                    <h3 class="text-xl md:text-2xl font-bold text-gray-800">{{ $masaplaya->whereNotNull('simu')->count() }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 md:p-6 card-hover">
            <div class="flex items-center">
                <div class="p-2 md:p-3 rounded-lg bg-purple-100 text-purple-600 mr-3 md:mr-4">
                    <i class="fas fa-envelope text-lg md:text-xl"></i>
                </div>
                <div>
                    <p class="text-xs md:text-sm text-gray-500 font-medium">Wenye Email</p>
                    <h3 class="text-xl md:text-2xl font-bold text-gray-800">{{ $masaplaya->whereNotNull('barua_pepe')->count() }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 md:p-6 card-hover">
            <div class="flex items-center">
                <div class="p-2 md:p-3 rounded-lg bg-amber-100 text-amber-600 mr-3 md:mr-4">
                    <i class="fas fa-building text-lg md:text-xl"></i>
                </div>
                <div>
                    <p class="text-xs md:text-sm text-gray-500 font-medium">Wenye Ofisi</p>
                    <h3 class="text-xl md:text-2xl font-bold text-gray-800">{{ $masaplaya->whereNotNull('ofisi')->count() }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-200 text-green-800 p-3 md:p-4 rounded-lg flex items-center text-sm md:text-base">
            <i class="fas fa-check-circle mr-2 md:mr-3 text-lg md:text-xl"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Page Navigation Tabs - Made responsive -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-3 md:p-4 card-hover">
        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4 md:space-x-6 border-b border-gray-200 pb-4 sm:pb-0 sm:border-b-0">
            <button 
                onclick="switchTab('taarifa')" 
                id="tab-taarifa"
                class="tab-button py-2 sm:pb-3 px-2 sm:px-1 transition-colors flex items-center justify-center sm:justify-start border-b-2 sm:border-b-0 border-green-500 text-green-600 font-semibold text-sm md:text-base"
            >
                <i class="fas fa-table mr-2 text-sm md:text-base"></i>Taarifa za Masaplaya
            </button>
            <button 
                onclick="switchTab('sajili')" 
                id="tab-sajili"
                class="tab-button py-2 sm:pb-3 px-2 sm:px-1 transition-colors flex items-center justify-center sm:justify-start text-gray-500 hover:text-gray-700 text-sm md:text-base"
            >
                <i class="fas fa-plus-circle mr-2 text-sm md:text-base"></i>Sajili Msaplaya Mpya
            </button>
        </div>
    </div>

    <!-- Masaplaya Information Tab -->
    <div id="content-taarifa" class="tab-content space-y-6">
        <!-- Search and Actions - Made responsive -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 md:p-6 card-hover">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-4 md:mb-6 space-y-4 md:space-y-0">
                <h2 class="text-lg md:text-xl font-bold text-gray-800">Orodha ya Masaplaya</h2>
                <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3 w-full md:w-auto">
                    <div class="relative w-full sm:w-auto">
                        <input 
                            type="text" 
                            id="searchQuery"
                            placeholder="Tafuta msaplaya..." 
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm md:text-base"
                            oninput="filterTable()"
                        >
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                    <button 
                        onclick="window.print()" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center text-sm md:text-base"
                    >
                        <i class="fas fa-print mr-2"></i> Print
                    </button>
                </div>
            </div>

            <!-- Data Table - Made responsive -->
            <div class="overflow-x-auto -mx-4 md:mx-0">
                <table class="w-full table-auto min-w-[800px] md:min-w-0">
                    <thead>
                        <tr class="bg-gradient-to-r from-green-600 to-green-700">
                            <th class="px-4 md:px-6 py-3 md:py-4 text-left text-xs md:text-sm font-semibold text-white">Msaplaya</th>
                            <th class="px-4 md:px-6 py-3 md:py-4 text-left text-xs md:text-sm font-semibold text-white">Mawasiliano</th>
                            <th class="px-4 md:px-6 py-3 md:py-4 text-left text-xs md:text-sm font-semibold text-white">Ofisi</th>
                            <th class="px-4 md:px-6 py-3 md:py-4 text-left text-xs md:text-sm font-semibold text-white">Makazi</th>
                            <th class="px-4 md:px-6 py-3 md:py-4 text-center text-xs md:text-sm font-semibold text-white print:hidden">Vitendo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($masaplaya as $sap)
                            <tr class="hover:bg-green-50 transition-all duration-200 table-row" data-searchable="{{ strtolower($sap->jina . ' ' . $sap->simu . ' ' . $sap->barua_pepe) }}">
                                
                                <td class="px-4 md:px-6 py-3 md:py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 md:h-12 md:w-12 bg-gradient-to-r from-green-500 to-green-600 rounded-full flex items-center justify-center text-white font-bold text-base md:text-lg shadow-lg">
                                            {{ substr($sap->jina, 0, 1) }}
                                        </div>
                                        <div class="ml-3 md:ml-4">
                                            <div class="text-xs md:text-sm font-semibold text-gray-900">{{ $sap->jina }}</div>
                                            <div class="text-xs text-green-600 font-medium">Msaplaya</div>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="px-4 md:px-6 py-3 md:py-4">
                                    <div class="space-y-1">
                                        @if($sap->simu)
                                        <div class="flex items-center text-xs md:text-sm text-gray-700">
                                            <i class="fas fa-phone text-green-500 mr-2 w-4"></i>
                                            {{ $sap->simu }}
                                        </div>
                                        @endif
                                        @if($sap->barua_pepe)
                                        <div class="flex items-center text-xs md:text-sm text-gray-700">
                                            <i class="fas fa-envelope text-blue-500 mr-2 w-4"></i>
                                            <span class="truncate max-w-xs">{{ $sap->barua_pepe }}</span>
                                        </div>
                                        @endif
                                    </div>
                                </td>
                                
                                <td class="px-4 md:px-6 py-3 md:py-4">
                                    <div class="text-xs md:text-sm text-gray-700">
                                        {{ $sap->ofisi ?: '--' }}
                                    </div>
                                </td>
                                
                                <td class="px-4 md:px-6 py-3 md:py-4">
                                    <div class="text-xs md:text-sm text-gray-700 max-w-xs">
                                        {{ $sap->anaopoishi ?: '--' }}
                                    </div>
                                </td>
                                
                                <td class="px-4 md:px-6 py-3 md:py-4 whitespace-nowrap text-xs md:text-sm font-medium print:hidden">
                                    <div class="flex justify-center space-x-2 md:space-x-3">
                                        <button 
                                            onclick='showDetails(@json($sap))' 
                                            class="text-blue-600 hover:text-blue-800 transition-colors transform hover:scale-110 p-1"
                                            title="Angalia Maelezo"
                                        >
                                            <i class="fas fa-eye text-sm md:text-base"></i>
                                        </button>
                                        <button 
                                            onclick='editSaplaya(@json($sap))' 
                                            class="text-amber-600 hover:text-amber-800 transition-colors transform hover:scale-110 p-1"
                                            title="Badili"
                                        >
                                            <i class="fas fa-edit text-sm md:text-base"></i>
                                        </button>
                                        <button 
                                            onclick="deleteSaplaya({{ $sap->id }}, '{{ $sap->jina }}')" 
                                            class="text-red-500 hover:text-red-700 transition-colors transform hover:scale-110 p-1"
                                            title="Futa"
                                        >
                                            <i class="fas fa-trash text-sm md:text-base"></i>
                                        </button>
                                        @if($sap->simu)
                                        <a 
                                            href="tel:{{ $sap->simu }}" 
                                            class="text-green-600 hover:text-green-800 transition-colors transform hover:scale-110 p-1"
                                            title="Piga Simu"
                                        >
                                            <i class="fas fa-phone text-sm md:text-base"></i>
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 md:px-6 py-8 md:py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fas fa-truck text-4xl md:text-5xl text-green-300 mb-3 md:mb-4"></i>
                                        <p class="text-base md:text-lg font-semibold text-gray-600 mb-2">Hakuna masaplaya bado.</p>
                                        <p class="text-xs md:text-sm text-gray-500 mb-3 md:mb-4">Anza kwa kusajili msaplaya wako wa kwanza</p>
                                        <button 
                                            onclick="switchTab('sajili')" 
                                            class="bg-gradient-to-r from-green-600 to-green-700 text-white px-4 md:px-6 py-2 md:py-3 rounded-lg hover:from-green-700 hover:to-green-800 transition-all duration-300 transform hover:scale-105 flex items-center shadow-lg text-sm md:text-base"
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
    <div id="content-sajili" class="tab-content bg-white rounded-2xl shadow-lg border border-gray-100 p-4 md:p-6 card-hover" style="display: none;">
        <h2 class="text-lg md:text-xl font-bold text-gray-800 mb-4 md:mb-6">Sajili Msaplaya Mpya</h2>
        <form method="POST" action="{{ route('masaplaya.store') }}" class="space-y-4 md:space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                <!-- Jina -->
                <div class="relative">
                    <input 
                        type="text" 
                        name="jina" 
                        placeholder=" " 
                        class="peer border border-gray-300 rounded-lg w-full p-3 md:p-4 pt-5 md:pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm md:text-base" 
                        required
                    >
                    <label class="absolute left-3 md:left-4 top-1.5 md:top-2 text-gray-400 text-xs md:text-sm transition-all
                                  peer-placeholder-shown:top-3 md:peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-sm md:peer-placeholder-shown:text-base peer-focus:top-1.5 md:peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-xs md:peer-focus:text-sm font-medium">
                        Jina la Msaplaya *
                    </label>
                </div>

                <!-- Simu -->
                <div class="relative">
                    <input 
                        type="text" 
                        name="simu" 
                        placeholder=" " 
                        class="peer border border-gray-300 rounded-lg w-full p-3 md:p-4 pt-5 md:pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm md:text-base"
                    >
                    <label class="absolute left-3 md:left-4 top-1.5 md:top-2 text-gray-400 text-xs md:text-sm transition-all
                                  peer-placeholder-shown:top-3 md:peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-sm md:peer-placeholder-shown:text-base peer-focus:top-1.5 md:peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-xs md:peer-focus:text-sm font-medium">
                        Namba ya Simu
                    </label>
                </div>

                <!-- Barua Pepe -->
                <div class="relative">
                    <input 
                        type="email" 
                        name="barua_pepe" 
                        placeholder=" " 
                        class="peer border border-gray-300 rounded-lg w-full p-3 md:p-4 pt-5 md:pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm md:text-base"
                    >
                    <label class="absolute left-3 md:left-4 top-1.5 md:top-2 text-gray-400 text-xs md:text-sm transition-all
                                  peer-placeholder-shown:top-3 md:peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-sm md:peer-placeholder-shown:text-base peer-focus:top-1.5 md:peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-xs md:peer-focus:text-sm font-medium">
                        Barua Pepe
                    </label>
                </div>

                <!-- Ofisi -->
                <div class="relative">
                    <input 
                        type="text" 
                        name="ofisi" 
                        placeholder=" " 
                        class="peer border border-gray-300 rounded-lg w-full p-3 md:p-4 pt-5 md:pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm md:text-base"
                    >
                    <label class="absolute left-3 md:left-4 top-1.5 md:top-2 text-gray-400 text-xs md:text-sm transition-all
                                  peer-placeholder-shown:top-3 md:peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-sm md:peer-placeholder-shown:text-base peer-focus:top-1.5 md:peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-xs md:peer-focus:text-sm font-medium">
                        Ofisi
                    </label>
                </div>

                <!-- Anaopoishi -->
                <div class="relative">
                    <input 
                        type="text" 
                        name="anaopoishi" 
                        placeholder=" " 
                        class="peer border border-gray-300 rounded-lg w-full p-3 md:p-4 pt-5 md:pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm md:text-base"
                    >
                    <label class="absolute left-3 md:left-4 top-1.5 md:top-2 text-gray-400 text-xs md:text-sm transition-all
                                  peer-placeholder-shown:top-3 md:peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-sm md:peer-placeholder-shown:text-base peer-focus:top-1.5 md:peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-xs md:peer-focus:text-sm font-medium">
                        Anaopoishi
                    </label>
                </div>

                <!-- Maelezo -->
                <div class="relative md:col-span-2">
                    <textarea 
                        name="maelezo" 
                        placeholder=" " 
                        rows="3"
                        class="peer border border-gray-300 rounded-lg w-full p-3 md:p-4 pt-5 md:pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm md:text-base"
                    ></textarea>
                    <label class="absolute left-3 md:left-4 top-1.5 md:top-2 text-gray-400 text-xs md:text-sm transition-all
                                  peer-placeholder-shown:top-3 md:peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-sm md:peer-placeholder-shown:text-base peer-focus:top-1.5 md:peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-xs md:peer-focus:text-sm font-medium">
                        Maelezo
                    </label>
                </div>
            </div>

            <!-- Buttons - Made responsive -->
            <div class="flex flex-col sm:flex-row gap-3 md:gap-4 pt-4 md:pt-6 border-t border-gray-200">
                <button 
                    type="submit" 
                    class="bg-gradient-to-r from-green-600 to-green-700 text-white px-6 md:px-8 py-2.5 md:py-3 rounded-lg hover:from-green-700 hover:to-green-800 transition-all duration-300 transform hover:scale-105 flex items-center justify-center shadow-lg text-sm md:text-base order-2 sm:order-1"
                >
                    <i class="fas fa-save mr-2"></i> Hifadhi Msaplaya
                </button>
                <button 
                    type="reset" 
                    class="bg-gray-300 text-gray-700 px-6 md:px-8 py-2.5 md:py-3 rounded-lg hover:bg-gray-400 transition-colors flex items-center justify-center text-sm md:text-base order-1 sm:order-2"
                >
                    <i class="fas fa-redo mr-2"></i> Safisha Fomu
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Details Modal - Made responsive -->
<div id="detailsModal" class="hidden fixed inset-0 z-50 flex items-center justify-center modal-overlay p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
        <div class="p-4 md:p-6 border-b border-gray-200">
            <h3 class="text-base md:text-lg font-semibold text-gray-800">Taarifa Kamili za Msaplaya</h3>
        </div>
        <div class="p-4 md:p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-3">
                    <div>
                        <label class="text-xs md:text-sm font-medium text-gray-500">Jina la Msaplaya:</label>
                        <p id="detail_jina" class="text-gray-900 font-semibold text-sm md:text-base"></p>
                    </div>
                    <div>
                        <label class="text-xs md:text-sm font-medium text-gray-500">Simu:</label>
                        <p id="detail_simu" class="text-gray-900 text-sm md:text-base"></p>
                    </div>
                    <div>
                        <label class="text-xs md:text-sm font-medium text-gray-500">Barua Pepe:</label>
                        <p id="detail_barua_pepe" class="text-gray-900 text-sm md:text-base"></p>
                    </div>
                </div>
                <div class="space-y-3">
                    <div>
                        <label class="text-xs md:text-sm font-medium text-gray-500">Ofisi:</label>
                        <p id="detail_ofisi" class="text-gray-900 text-sm md:text-base"></p>
                    </div>
                    <div>
                        <label class="text-xs md:text-sm font-medium text-gray-500">Makazi:</label>
                        <p id="detail_anaopoishi" class="text-gray-900 text-sm md:text-base"></p>
                    </div>
                    <div>
                        <label class="text-xs md:text-sm font-medium text-gray-500">Maelezo:</label>
                        <p id="detail_maelezo" class="text-gray-900 text-sm md:text-base"></p>
                    </div>
                </div>
            </div>
            <div class="mt-4 md:mt-6 p-3 md:p-4 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-xs md:text-sm text-green-700">
                    <i class="fas fa-info-circle mr-2"></i>
                    Leo tarehe {{ \Carbon\Carbon::now()->translatedFormat('d F, Y') }}, 
                    Una jumla ya masaplaya {{ count($masaplaya) }}.
                </p>
            </div>
        </div>
        <div class="flex justify-end p-4 md:p-6 border-t border-gray-200">
            <button 
                onclick="closeDetailsModal()"
                class="px-4 md:px-6 py-2 md:py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors text-sm md:text-base"
            >
                Funga
            </button>
        </div>
    </div>
</div>

<!-- Edit Modal - Made responsive -->
<div id="editModal" class="hidden fixed inset-0 z-50 flex items-center justify-center modal-overlay p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-4xl mx-4 max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
        <div class="p-4 md:p-6 border-b border-gray-200">
            <h3 class="text-base md:text-lg font-semibold text-gray-800">Badili Taarifa za Msaplaya</h3>
        </div>
        <form id="editForm" method="POST" class="p-4 md:p-6">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                <!-- Jina -->
                <div class="relative">
                    <input 
                        type="text" 
                        name="jina" 
                        id="edit_jina"
                        class="peer border border-gray-300 rounded-lg w-full p-3 md:p-4 pt-5 md:pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm md:text-base" 
                        required
                    >
                    <label class="absolute left-3 md:left-4 top-1.5 md:top-2 text-gray-400 text-xs md:text-sm transition-all
                                  peer-placeholder-shown:top-3 md:peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-sm md:peer-placeholder-shown:text-base peer-focus:top-1.5 md:peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-xs md:peer-focus:text-sm font-medium">
                        Jina la Msaplaya *
                    </label>
                </div>

                <!-- Simu -->
                <div class="relative">
                    <input 
                        type="text" 
                        name="simu" 
                        id="edit_simu"
                        class="peer border border-gray-300 rounded-lg w-full p-3 md:p-4 pt-5 md:pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm md:text-base"
                    >
                    <label class="absolute left-3 md:left-4 top-1.5 md:top-2 text-gray-400 text-xs md:text-sm transition-all
                                  peer-placeholder-shown:top-3 md:peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-sm md:peer-placeholder-shown:text-base peer-focus:top-1.5 md:peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-xs md:peer-focus:text-sm font-medium">
                        Simu
                    </label>
                </div>

                <!-- Barua Pepe -->
                <div class="relative">
                    <input 
                        type="email" 
                        name="barua_pepe" 
                        id="edit_barua_pepe"
                        class="peer border border-gray-300 rounded-lg w-full p-3 md:p-4 pt-5 md:pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm md:text-base"
                    >
                    <label class="absolute left-3 md:left-4 top-1.5 md:top-2 text-gray-400 text-xs md:text-sm transition-all
                                  peer-placeholder-shown:top-3 md:peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-sm md:peer-placeholder-shown:text-base peer-focus:top-1.5 md:peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-xs md:peer-focus:text-sm font-medium">
                        Barua Pepe
                    </label>
                </div>

                <!-- Ofisi -->
                <div class="relative">
                    <input 
                        type="text" 
                        name="ofisi" 
                        id="edit_ofisi"
                        class="peer border border-gray-300 rounded-lg w-full p-3 md:p-4 pt-5 md:pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm md:text-base"
                    >
                    <label class="absolute left-3 md:left-4 top-1.5 md:top-2 text-gray-400 text-xs md:text-sm transition-all
                                  peer-placeholder-shown:top-3 md:peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-sm md:peer-placeholder-shown:text-base peer-focus:top-1.5 md:peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-xs md:peer-focus:text-sm font-medium">
                        Ofisi
                    </label>
                </div>

                <!-- Anaopoishi -->
                <div class="relative">
                    <input 
                        type="text" 
                        name="anaopoishi" 
                        id="edit_anaopoishi"
                        class="peer border border-gray-300 rounded-lg w-full p-3 md:p-4 pt-5 md:pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm md:text-base"
                    >
                    <label class="absolute left-3 md:left-4 top-1.5 md:top-2 text-gray-400 text-xs md:text-sm transition-all
                                  peer-placeholder-shown:top-3 md:peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-sm md:peer-placeholder-shown:text-base peer-focus:top-1.5 md:peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-xs md:peer-focus:text-sm font-medium">
                        Anaopoishi
                    </label>
                </div>

                <!-- Maelezo -->
                <div class="relative md:col-span-2">
                    <textarea 
                        name="maelezo" 
                        id="edit_maelezo"
                        rows="3"
                        class="peer border border-gray-300 rounded-lg w-full p-3 md:p-4 pt-5 md:pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm md:text-base"
                    ></textarea>
                    <label class="absolute left-3 md:left-4 top-1.5 md:top-2 text-gray-400 text-xs md:text-sm transition-all
                                  peer-placeholder-shown:top-3 md:peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-sm md:peer-placeholder-shown:text-base peer-focus:top-1.5 md:peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-xs md:peer-focus:text-sm font-medium">
                        Maelezo
                    </label>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3 pt-4 md:pt-6 border-t border-gray-200 mt-4 md:mt-6">
                <button 
                    type="button" 
                    onclick="closeEditModal()"
                    class="px-4 md:px-6 py-2 md:py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors text-sm md:text-base"
                >
                    Ghairi
                </button>
                <button 
                    type="submit" 
                    class="px-4 md:px-6 py-2 md:py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm md:text-base"
                >
                    Hifadhi Mabadiliko
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal - Made responsive -->
<div id="deleteModal" class="hidden fixed inset-0 z-50 flex items-center justify-center modal-overlay p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4" onclick="event.stopPropagation()">
        <div class="p-4 md:p-6 border-b border-gray-200">
            <h3 class="text-base md:text-lg font-semibold text-gray-800">Thibitisha Kufuta Msaplaya</h3>
        </div>
        <div class="p-4 md:p-6">
            <div class="flex items-center justify-center mb-3 md:mb-4">
                <div class="p-2 md:p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-exclamation-triangle text-lg md:text-xl"></i>
                </div>
            </div>
            <p class="text-gray-700 mb-4 md:mb-6 text-center text-sm md:text-base">
                Una uhakika unataka kufuta msaplaya "<span id="delete_name" class="font-semibold"></span>"?
                <br>Hatua hii haiwezi kutenduliwa.
            </p>
            <div class="flex flex-col sm:flex-row justify-center space-y-3 sm:space-y-0 sm:space-x-3">
                <button 
                    onclick="closeDeleteModal()"
                    class="px-4 md:px-6 py-2 md:py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors text-sm md:text-base"
                >
                    Ghairi
                </button>
                <form id="deleteForm" method="POST" class="w-full sm:w-auto">
                    @csrf
                    @method('DELETE')
                    <button 
                        type="submit" 
                        class="w-full px-4 md:px-6 py-2 md:py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm md:text-base"
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
// Tab Switching
function switchTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.style.display = 'none';
    });
    
    // Remove active class from all tab buttons
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('border-b-2', 'border-green-500', 'text-green-600', 'font-semibold');
        button.classList.add('text-gray-500', 'hover:text-gray-700');
    });
    
    // Show selected tab content
    document.getElementById('content-' + tabName).style.display = 'block';
    
    // Add active class to selected tab button
    const activeButton = document.getElementById('tab-' + tabName);
    activeButton.classList.remove('text-gray-500', 'hover:text-gray-700');
    activeButton.classList.add('border-b-2', 'border-green-500', 'text-green-600', 'font-semibold');
}

// Search/Filter Table
function filterTable() {
    const searchQuery = document.getElementById('searchQuery').value.toLowerCase();
    const rows = document.querySelectorAll('.table-row');
    
    rows.forEach(row => {
        const searchableText = row.getAttribute('data-searchable');
        if (searchableText.includes(searchQuery)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Show Details Modal
function showDetails(saplaya) {
    document.getElementById('detail_jina').textContent = saplaya.jina || '--';
    document.getElementById('detail_simu').textContent = saplaya.simu || '--';
    document.getElementById('detail_barua_pepe').textContent = saplaya.barua_pepe || '--';
    document.getElementById('detail_ofisi').textContent = saplaya.ofisi || '--';
    document.getElementById('detail_anaopoishi').textContent = saplaya.anaopoishi || '--';
    document.getElementById('detail_maelezo').textContent = saplaya.maelezo || '--';
    
    document.getElementById('detailsModal').classList.remove('hidden');
}

function closeDetailsModal() {
    document.getElementById('detailsModal').classList.add('hidden');
}

// Edit Saplaya
function editSaplaya(saplaya) {
    document.getElementById('edit_jina').value = saplaya.jina || '';
    document.getElementById('edit_simu').value = saplaya.simu || '';
    document.getElementById('edit_barua_pepe').value = saplaya.barua_pepe || '';
    document.getElementById('edit_ofisi').value = saplaya.ofisi || '';
    document.getElementById('edit_anaopoishi').value = saplaya.anaopoishi || '';
    document.getElementById('edit_maelezo').value = saplaya.maelezo || '';
    
    document.getElementById('editForm').action = '/masaplaya/' + saplaya.id;
    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

// Delete Saplaya
function deleteSaplaya(id, name) {
    document.getElementById('delete_name').textContent = name;
    document.getElementById('deleteForm').action = '/masaplaya/' + id;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

// Close modals when clicking outside
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('modal-overlay')) {
        closeDetailsModal();
        closeEditModal();
        closeDeleteModal();
    }
});

// Close modals with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeDetailsModal();
        closeEditModal();
        closeDeleteModal();
    }
});

// Prevent zoom on iOS inputs
document.addEventListener('DOMContentLoaded', function() {
    if (/iPhone|iPad|iPod/.test(navigator.userAgent)) {
        const inputs = document.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.style.fontSize = '16px';
        });
    }
});
</script>

<style>
.modal-overlay {
    background-color: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
}

.card-hover {
    transition: all 0.3s ease;
}

.card-hover:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

@media print {
    .print\:hidden {
        display: none !important;
    }
    
    .modal-overlay {
        display: none !important;
    }
    
    .tab-content {
        display: block !important;
    }
    
    #content-sajili {
        display: none !important;
    }
}

/* Mobile optimizations */
@media (max-width: 768px) {
    .overflow-x-auto {
        -webkit-overflow-scrolling: touch;
    }
    
    button, a {
        min-height: 44px;
        min-width: 44px;
    }
}
</style>
@endpush