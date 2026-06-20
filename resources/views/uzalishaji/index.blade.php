{{-- resources/views/uzalishaji/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Uzalishaji')
@section('page-title', 'Uzalishaji')
@section('page-subtitle', now()->format('d/m/Y'))

@section('content')
<div class="space-y-4" id="app-container">
    <!-- Notifications -->
    <div id="notification-container" class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-sm px-4 pointer-events-none"></div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="bg-white p-3 rounded-lg border border-amber-200 shadow-sm">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Jumla ya Uzalishaji</p>
                    <p class="text-xl font-bold text-amber-700">{{ number_format($statistics['total'] ?? 0) }}</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center">
                    <i class="fas fa-industry text-amber-500"></i>
                </div>
            </div>
        </div>
        <div class="bg-white p-3 rounded-lg border border-green-200 shadow-sm">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Imekamilika</p>
                    <p class="text-xl font-bold text-green-700">{{ number_format($statistics['completed'] ?? 0) }}</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-500"></i>
                </div>
            </div>
        </div>
        <div class="bg-white p-3 rounded-lg border border-amber-200 shadow-sm">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Inaendelea</p>
                    <p class="text-xl font-bold text-amber-700">{{ number_format($statistics['pending'] ?? 0) }}</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center">
                    <i class="fas fa-clock text-amber-500"></i>
                </div>
            </div>
        </div>
        <div class="bg-white p-3 rounded-lg border border-purple-200 shadow-sm">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Jumla Gharama</p>
                    <p class="text-xl font-bold text-purple-700">Tsh {{ number_format($statistics['total_cost'] ?? 0, 0) }}</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                    <i class="fas fa-money-bill-wave text-purple-500"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm">
        <div class="flex overflow-x-auto">
            <button data-tab="orodha" class="tab-btn flex-1 py-3 px-4 text-sm font-medium border-r border-gray-200 bg-amber-50 text-amber-700 whitespace-nowrap active">
                <i class="fas fa-list mr-2"></i> Orodha
            </button>
            <button data-tab="ingiza" class="tab-btn flex-1 py-3 px-4 text-sm font-medium border-r border-gray-200 text-gray-600 hover:bg-gray-50 whitespace-nowrap">
                <i class="fas fa-plus mr-2"></i> Ingiza
            </button>
            <button data-tab="ripoti" class="tab-btn flex-1 py-3 px-4 text-sm font-medium text-gray-600 hover:bg-gray-50 whitespace-nowrap">
                <i class="fas fa-chart-bar mr-2"></i> Ripoti
            </button>
        </div>
    </div>

    <!-- TAB 1: Orodha -->
    <div id="tab-orodha" class="tab-content">
        <div class="bg-white p-3 rounded-lg border border-gray-200 shadow-sm">
            <div class="flex flex-col sm:flex-row gap-2">
                <div class="flex-1 relative">
                    <input type="text" id="search-input" placeholder="Tafuta uzalishaji..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                <select id="status-filter" class="px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="">Hali yote</option>
                    <option value="completed">Imekamilika</option>
                    <option value="pending">Inaendelea</option>
                </select>
                <button onclick="applyFilters()" class="px-4 py-2 bg-amber-600 text-white rounded hover:bg-amber-700 text-sm font-medium">
                    <i class="fas fa-filter mr-1"></i> Chuja
                </button>
                <a href="{{ route('uzalishaji.index') }}" class="px-3 py-2 text-gray-600 hover:text-gray-800 text-sm">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-amber-50 border-b border-gray-200">
                            <th class="px-3 py-2 text-left font-medium text-amber-800">Jina</th>
                            <th class="px-3 py-2 text-left font-medium text-amber-800 hidden sm:table-cell">Aina</th>
                            <th class="px-3 py-2 text-center font-medium text-amber-800 hidden sm:table-cell">Idadi</th>
                            <th class="px-3 py-2 text-right font-medium text-amber-800">Gharama</th>
                            <th class="px-3 py-2 text-center font-medium text-amber-800 hidden md:table-cell">Hali</th>
                            <th class="px-3 py-2 text-center font-medium text-amber-800">Vitendo</th>
                        </tr>
                    </thead>
                    <tbody id="productions-tbody">
                        @forelse($uzalishaji as $item)
                        <tr data-id="{{ $item->id }}" class="hover:bg-gray-50 border-b border-gray-100">
                            <td class="px-3 py-2">
                                <div class="font-medium text-gray-900">{{ $item->jina }}</div>
                                <div class="text-xs text-gray-500 sm:hidden">{{ $item->aina_bidhaa }}</div>
                                <div class="text-xs text-gray-400">{{ $item->tarehe->format('d/m/Y') }}</div>
                            </td>
                            <td class="px-3 py-2 hidden sm:table-cell">
                                <span class="text-sm text-gray-700">{{ $item->aina_bidhaa }}</span>
                                <div class="text-xs text-gray-400">{{ $item->kipimo }}</div>
                            </td>
                            <td class="px-3 py-2 text-center hidden sm:table-cell">
                                <span class="font-medium">{{ number_format($item->idadi_iliyozalishwa) }}</span>
                            </td>
                            <td class="px-3 py-2 text-right">
                                <div class="font-bold text-amber-700">Tsh {{ number_format($item->jumla_gharama, 0) }}</div>
                                <div class="text-xs text-gray-500">Tsh {{ number_format($item->gharama_kwa_moja, 0) }}/moja</div>
                            </td>
                            <td class="px-3 py-2 text-center hidden md:table-cell">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ $item->imekamilika ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $item->imekamilika ? '✅ Imekamilika' : '⏳ Inaendelea' }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-center">
                                <div class="flex justify-center space-x-1 flex-wrap">
                                    <button onclick="viewProduction({{ $item->id }})" class="text-blue-600 hover:text-blue-800 p-1" title="Tazama"><i class="fas fa-eye"></i></button>
                                    @if(!$item->imekamilika)
                                    <button onclick="editProduction({{ $item->id }})" class="text-amber-600 hover:text-amber-800 p-1" title="Hariri"><i class="fas fa-edit"></i></button>
                                    <button onclick="completeProduction({{ $item->id }})" class="text-green-600 hover:text-green-800 p-1" title="Kamilisha"><i class="fas fa-check"></i></button>
                                    <button onclick="deleteProduction({{ $item->id }})" class="text-red-600 hover:text-red-800 p-1" title="Futa"><i class="fas fa-trash"></i></button>
                                    @endif
                                    <button onclick="duplicateProduction({{ $item->id }})" class="text-purple-600 hover:text-purple-800 p-1" title="Nakili"><i class="fas fa-copy"></i></button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                <i class="fas fa-industry text-3xl mb-2 block text-gray-300"></i>
                                <p>Hakuna uzalishaji bado</p>
                                <button onclick="switchTab('ingiza')" class="mt-2 px-4 py-2 bg-amber-600 text-white rounded hover:bg-amber-700 text-sm">
                                    <i class="fas fa-plus mr-1"></i> Anzisha Uzalishaji
                                </button>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($uzalishaji->hasPages())
            <div class="px-3 py-2 border-t border-gray-200 bg-gray-50">
                {{ $uzalishaji->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- TAB 2: Ingiza -->
    <div id="tab-ingiza" class="tab-content hidden">
        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
            <form id="productionForm" class="space-y-4">
                @csrf
                <input type="hidden" id="edit_id" value="">
                
                <!-- Step 1: Production Details -->
                <div class="border-b border-gray-200 pb-3">
                    <h4 class="text-sm font-semibold text-amber-700 mb-3 flex items-center">
                        <span class="bg-amber-100 text-amber-700 rounded-full w-6 h-6 flex items-center justify-center text-xs mr-2">1</span>
                        Taarifa za Uzalishaji
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Tarehe *</label>
                            <input type="date" id="f_tarehe" required value="{{ date('Y-m-d') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Jina la Uzalishaji *</label>
                            <input type="text" id="f_jina" required placeholder="Ej. Kutengeneza Mandazi"
                                   class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Aina ya Bidhaa *</label>
                            <input type="text" id="f_aina_bidhaa" required placeholder="Mandazi, Tofali, Sabuni..."
                                   class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Maelezo (hiari)</label>
                            <input type="text" id="f_maelezo" placeholder="Maelezo ya ziada..."
                                   class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                    </div>
                </div>

                <!-- Step 2: COSTS - CART SYSTEM (FIXED) -->
                <div class="border-b border-gray-200 pb-3">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-sm font-semibold text-amber-700 flex items-center">
                            <span class="bg-amber-100 text-amber-700 rounded-full w-6 h-6 flex items-center justify-center text-xs mr-2">2</span>
                            Gharama za Uzalishaji
                            <span class="ml-2 text-xs bg-amber-100 px-2 py-0.5 rounded-full text-amber-700" id="costCount">0</span>
                        </h4>
                        <button type="button" onclick="openCostModal()"
                                class="px-3 py-1.5 bg-amber-600 text-white rounded hover:bg-amber-700 text-xs font-medium flex items-center">
                            <i class="fas fa-plus mr-1"></i> Ongeza Gharama
                        </button>
                    </div>

                    <!-- COST CART DISPLAY -->
                    <div id="costCartContainer" class="space-y-2 max-h-60 overflow-y-auto border border-gray-200 rounded-lg p-2 min-h-[60px]">
                        <div id="emptyCartMessage" class="text-center py-4 text-gray-500">
                            <i class="fas fa-shopping-cart text-2xl mb-1 block text-gray-300"></i>
                            <p class="text-xs">Bado hakuna gharama. Bonyeza "Ongeza Gharama"</p>
                        </div>
                    </div>

                    <!-- CART SUMMARY -->
                    <div id="cartSummary" class="hidden mt-2 pt-2 border-t border-gray-200">
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-600">Jumla ya Gharama:</span>
                            <span class="text-sm font-bold text-amber-700" id="cartTotalCost">Tsh 0</span>
                        </div>
                        <div class="flex justify-between items-center mt-1">
                            <span class="text-xs text-gray-600">Idadi ya Gharama:</span>
                            <span class="text-sm font-medium text-gray-700" id="cartTotalItems">0</span>
                        </div>
                    </div>
                    
                    <!-- Quick Add Buttons -->
                    <div class="flex flex-wrap gap-1 mt-2 pt-2 border-t border-gray-200">
                        <span class="text-xs text-gray-500 mr-1">Haraka:</span>
                        @foreach($categories as $key => $category)
                        <button type="button" onclick="quickAddCost('{{ $key }}')"
                                class="text-xs px-2 py-0.5 rounded-full border border-gray-200 hover:bg-amber-50 hover:border-amber-300 transition">
                            {{ $category['label'] }}
                        </button>
                        @endforeach
                    </div>
                </div>

                <!-- Step 3: Production Result -->
                <div class="border-b border-gray-200 pb-3">
                    <h4 class="text-sm font-semibold text-amber-700 mb-3 flex items-center">
                        <span class="bg-amber-100 text-amber-700 rounded-full w-6 h-6 flex items-center justify-center text-xs mr-2">3</span>
                        Matokeo ya Uzalishaji
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Jumla ya Bidhaa *</label>
                            <input type="number" id="f_idadi" required step="0.01" min="0.01" oninput="updateTotals()"
                                   class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Kipimo *</label>
                            <input type="text" id="f_kipimo" required placeholder="Pieces, Bottles, KG..."
                                   class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                    </div>
                </div>

                <!-- Summary -->
                <div class="bg-amber-50 p-3 rounded-lg border border-amber-200">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div>
                            <div class="text-xs text-amber-700 font-medium">Jumla Gharama</div>
                            <div class="text-lg font-bold text-amber-800" id="totalCostDisplay">Tsh 0</div>
                        </div>
                        <div>
                            <div class="text-xs text-amber-700 font-medium">Gharama kwa Moja</div>
                            <div class="text-lg font-bold text-amber-800" id="costPerUnitDisplay">Tsh 0</div>
                        </div>
                        <div>
                            <div class="text-xs text-amber-700 font-medium">Bei Pendekezwa</div>
                            <div class="text-lg font-bold text-green-700" id="suggestedPriceDisplay">Tsh 0</div>
                        </div>
                        <div>
                            <div class="text-xs text-amber-700 font-medium">Idadi</div>
                            <div class="text-lg font-bold text-amber-800" id="quantityDisplay">0</div>
                        </div>
                    </div>
                    <div id="costBreakdownDisplay" class="mt-2 pt-2 border-t border-amber-200 text-xs text-amber-700 flex flex-wrap gap-2"></div>
                </div>

                <!-- Profit Simulation -->
                <div class="pt-2">
                    <h4 class="text-sm font-semibold text-amber-700 mb-2 flex items-center">
                        <span class="bg-amber-100 text-amber-700 rounded-full w-6 h-6 flex items-center justify-center text-xs mr-2">4</span>
                        Uigaji wa Faida
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Bei ya Kuuza (kwa moja)</label>
                            <input type="number" id="f_selling_price" step="0.01" min="0" oninput="simulateProfit()"
                                   class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div>
                            <button type="button" onclick="simulateProfit()"
                                    class="w-full px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm font-medium">
                                <i class="fas fa-calculator mr-1"></i> Kokotoa Faida
                            </button>
                        </div>
                        <div>
                            <button type="button" onclick="resetForm()"
                                    class="w-full px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 text-sm font-medium">
                                <i class="fas fa-redo mr-1"></i> Safisha
                            </button>
                        </div>
                    </div>
                    <div id="profitResults" class="hidden mt-3 grid grid-cols-2 sm:grid-cols-4 gap-2">
                        <div class="bg-green-50 p-2 rounded text-center border border-green-200">
                            <div class="text-xs text-gray-600">Faida kwa Moja</div>
                            <div class="text-sm font-bold text-green-600" id="profitPerItem">Tsh 0</div>
                        </div>
                        <div class="bg-blue-50 p-2 rounded text-center border border-blue-200">
                            <div class="text-xs text-gray-600">Asilimia Faida</div>
                            <div class="text-sm font-bold text-blue-600" id="profitPercentage">0%</div>
                        </div>
                        <div class="bg-purple-50 p-2 rounded text-center border border-purple-200">
                            <div class="text-xs text-gray-600">Jumla Faida</div>
                            <div class="text-sm font-bold text-purple-600" id="totalProfit">Tsh 0</div>
                        </div>
                        <div class="bg-amber-50 p-2 rounded text-center border border-amber-200">
                            <div class="text-xs text-gray-600">Jumla Mapato</div>
                            <div class="text-sm font-bold text-amber-600" id="totalRevenue">Tsh 0</div>
                        </div>
                    </div>
                </div>

                <div class="flex gap-2 pt-4 border-t border-gray-200">
                    <button type="submit" id="submitBtn"
                            class="flex-1 bg-amber-600 text-white px-4 py-2 rounded hover:bg-amber-700 text-sm font-medium">
                        <i class="fas fa-save mr-1"></i> <span id="submitText">Hifadhi Uzalishaji</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- TAB 3: Ripoti -->
    <div id="tab-ripoti" class="tab-content hidden">
        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
            <div class="flex flex-wrap gap-2 mb-4">
                <button onclick="loadReport('daily')" class="px-4 py-2 bg-amber-100 text-amber-700 rounded hover:bg-amber-200 text-sm font-medium">📅 Leo</button>
                <button onclick="loadReport('weekly')" class="px-4 py-2 bg-amber-100 text-amber-700 rounded hover:bg-amber-200 text-sm font-medium">📆 Wiki Hii</button>
                <button onclick="loadReport('monthly')" class="px-4 py-2 bg-amber-100 text-amber-700 rounded hover:bg-amber-200 text-sm font-medium">🗓️ Mwezi Huu</button>
                <button onclick="loadReport('all')" class="px-4 py-2 bg-amber-100 text-amber-700 rounded hover:bg-amber-200 text-sm font-medium">📊 Zote</button>
            </div>
            <div id="reportContent">
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-chart-bar text-3xl mb-2 block text-gray-300"></i>
                    <p>Chagua kipindi cha ripoti</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===== COST MODAL (FIXED) ===== -->
<div id="costModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="absolute inset-0 bg-black bg-opacity-50" onclick="closeCostModal()"></div>
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-auto z-50 relative">
        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-sm font-semibold text-gray-800">
                <i class="fas fa-plus-circle text-amber-600 mr-2"></i>Ongeza Gharama
            </h3>
            <button onclick="closeCostModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-4">
            <form id="costForm" class="space-y-3" onsubmit="return false;">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Jina la Gharama *</label>
                    <input type="text" id="cost_jina" required placeholder="Ej. Unga, Mafuta, Mishahara..."
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Kundi la Gharama *</label>
                    <select id="cost_kundi" required class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                        <option value="">Chagua Kundi</option>
                        @foreach($categories as $key => $category)
                        <option value="{{ $key }}">{{ $category['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Kiasi</label>
                    <input type="number" id="cost_kiasi" step="0.01" min="0.01" value="1"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Gharama (TZS) *</label>
                    <input type="number" id="cost_gharama" required step="0.01" min="0" placeholder="0.00"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
                
                <!-- Current cart status -->
                <div class="text-xs text-gray-500 bg-gray-50 p-2 rounded">
                    <i class="fas fa-shopping-cart mr-1"></i>
                    Gharama zilizoongezwa: <span id="modalCostCounter" class="font-bold text-amber-600">0</span>
                </div>
                
                <div class="flex gap-2 pt-2 border-t border-gray-200">
                    <button type="button" onclick="addCostAndStayOpen()" 
                            class="flex-1 bg-amber-600 text-white px-4 py-2 rounded hover:bg-amber-700 text-sm font-medium">
                        <i class="fas fa-plus mr-1"></i> Ongeza
                    </button>
                    <button type="button" onclick="addCostAndClose()" 
                            class="flex-1 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm font-medium">
                        <i class="fas fa-check mr-1"></i> Ongeza & Funga
                    </button>
                    <button type="button" onclick="closeCostModal()" 
                            class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 text-sm">
                        Ghairi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ===== VIEW MODAL ===== -->
<div id="viewModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="absolute inset-0 bg-black bg-opacity-50" onclick="closeModal('viewModal')"></div>
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-auto z-50 max-h-[90vh] overflow-y-auto">
        <div class="p-4 border-b border-gray-200 flex justify-between items-center sticky top-0 bg-white">
            <h3 class="text-sm font-semibold text-gray-800">Taarifa za Uzalishaji</h3>
            <button onclick="closeModal('viewModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-4" id="viewContent">
            <div class="text-center py-8">
                <i class="fas fa-spinner fa-spin text-2xl text-amber-600"></i>
                <p class="mt-2 text-gray-500">Inapakia...</p>
            </div>
        </div>
    </div>
</div>

<!-- ===== COMPLETE MODAL ===== -->
<div id="completeModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="absolute inset-0 bg-black bg-opacity-50" onclick="closeModal('completeModal')"></div>
    <div class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-auto z-50 max-h-[90vh] overflow-y-auto">
        <div class="p-4 border-b border-gray-200 flex justify-between items-center sticky top-0 bg-white">
            <h3 class="text-sm font-semibold text-gray-800">Kamilisha Uzalishaji</h3>
            <button onclick="closeModal('completeModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-4" id="completeContent">
            <div class="text-center py-8">
                <i class="fas fa-spinner fa-spin text-2xl text-amber-600"></i>
                <p class="mt-2 text-gray-500">Inapakia...</p>
            </div>
        </div>
    </div>
</div>

<!-- ===== DELETE MODAL ===== -->
<div id="deleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="absolute inset-0 bg-black bg-opacity-50" onclick="closeModal('deleteModal')"></div>
    <div class="bg-white rounded-lg shadow-xl w-full max-w-sm mx-auto z-50">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-800">Thibitisha Kufuta</h3>
        </div>
        <div class="p-4">
            <div class="text-center mb-4">
                <i class="fas fa-exclamation-triangle text-amber-500 text-3xl mb-2"></i>
                <p class="text-gray-700 text-sm mb-1">Una uhakika unataka kufuta uzalishaji huu?</p>
                <p class="text-gray-900 font-medium" id="deleteItemName"></p>
                <p class="text-gray-500 text-xs mt-2">Hatua hii haiwezi kutenduliwa</p>
            </div>
            <div class="flex gap-2">
                <button onclick="closeModal('deleteModal')" class="flex-1 px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 text-sm">Ghairi</button>
                <button id="confirmDeleteBtn" class="flex-1 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm font-medium">Futa</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.tab-btn.active {
    background: #fef3c7;
    color: #92400e;
}
.tab-btn {
    transition: all 0.2s ease;
}
.tab-btn:hover:not(.active) {
    background: #f9fafb;
}

.category-badge {
    display: inline-block;
    padding: 0.15rem 0.6rem;
    border-radius: 9999px;
    font-size: 0.6rem;
    font-weight: 600;
    text-transform: uppercase;
}
.category-badge.malighafi { background: #fcd34d; color: #78350f; }
.category-badge.mishahara { background: #93c5fd; color: #1e40af; }
.category-badge.usafiri { background: #6ee7b7; color: #065f46; }
.category-badge.ufungashaji { background: #c4b5fd; color: #4c1d95; }
.category-badge.umeme_na_nishati { background: #fde047; color: #713f12; }
.category-badge.gharama_nyingine { background: #d1d5db; color: #1f2937; }

.cart-item {
    background: #f8fafc;
    border-radius: 0.5rem;
    padding: 0.6rem 0.75rem;
    border: 1px solid #e2e8f0;
    margin-bottom: 0.4rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: all 0.2s ease;
}
.cart-item:hover {
    background: #fef3c7;
    border-color: #fcd34d;
}
.cart-item:last-child {
    margin-bottom: 0;
}
.cart-item .item-info {
    flex: 1;
}
.cart-item .item-info .name {
    font-weight: 500;
    font-size: 0.875rem;
}
.cart-item .item-info .details {
    font-size: 0.75rem;
    color: #6b7280;
}

@keyframes slideIn {
    from { opacity: 0; transform: translateX(-10px); }
    to { opacity: 1; transform: translateX(0); }
}
.cart-item {
    animation: slideIn 0.25s ease-out;
}

#costCartContainer::-webkit-scrollbar {
    width: 4px;
}
#costCartContainer::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}
#costCartContainer::-webkit-scrollbar-thumb {
    background: #fcd34d;
    border-radius: 4px;
}

.animate-fade-in {
    animation: fadeIn 0.3s ease-out;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
@endpush

@push('scripts')
<script>
// ============================================
// STATE - CART SYSTEM
// ============================================
let costCart = [];
let currentDeleteId = null;

// ============================================
// TAB HANDLING
// ============================================
function switchTab(tabName) {
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active', 'bg-amber-50', 'text-amber-700');
        if (btn.dataset.tab === tabName) {
            btn.classList.add('active', 'bg-amber-50', 'text-amber-700');
        }
    });
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.getElementById('tab-' + tabName).classList.remove('hidden');
    sessionStorage.setItem('uzalishaji_tab', tabName);
}

// ============================================
// COST MODAL - FIXED VERSION
// ============================================
function openCostModal() {
    document.getElementById('costModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Reset form
    document.getElementById('cost_jina').value = '';
    document.getElementById('cost_kundi').value = '';
    document.getElementById('cost_kiasi').value = 1;
    document.getElementById('cost_gharama').value = '';
    
    // Update counter
    document.getElementById('modalCostCounter').textContent = costCart.length;
    
    // Focus on first field
    setTimeout(() => {
        document.getElementById('cost_jina').focus();
    }, 100);
}

function closeCostModal() {
    document.getElementById('costModal').classList.add('hidden');
    document.body.style.overflow = '';
}

// ============================================
// ADD COST FUNCTIONS - FIXED
// ============================================
function addCostAndStayOpen() {
    // Get values
    const jina = document.getElementById('cost_jina').value.trim();
    const kundi = document.getElementById('cost_kundi').value;
    const kiasi = parseFloat(document.getElementById('cost_kiasi').value) || 1;
    const gharama = parseFloat(document.getElementById('cost_gharama').value) || 0;
    
    // Validate
    if (!jina) { 
        showNotification('Tafadhali ingiza jina la gharama.', 'error');
        document.getElementById('cost_jina').focus();
        return; 
    }
    if (!kundi) { 
        showNotification('Tafadhali chagua kundi la gharama.', 'error');
        document.getElementById('cost_kundi').focus();
        return; 
    }
    if (gharama <= 0) { 
        showNotification('Gharama lazima iwe kubwa kuliko sifuri.', 'error');
        document.getElementById('cost_gharama').focus();
        return; 
    }
    
    // Add to cart
    costCart.push({
        jina: jina,
        kundi: kundi,
        kiasi: kiasi,
        gharama: gharama
    });
    
    // Update UI
    renderCart();
    updateTotals();
    
    // Clear form for next entry - STAY OPEN
    document.getElementById('cost_jina').value = '';
    document.getElementById('cost_kundi').value = '';
    document.getElementById('cost_kiasi').value = 1;
    document.getElementById('cost_gharama').value = '';
    document.getElementById('modalCostCounter').textContent = costCart.length;
    document.getElementById('cost_jina').focus();
    
    showNotification('✅ Gharama imeongezwa! (' + costCart.length + ' total)', 'success');
}

function addCostAndClose() {
    // Get values
    const jina = document.getElementById('cost_jina').value.trim();
    const kundi = document.getElementById('cost_kundi').value;
    const kiasi = parseFloat(document.getElementById('cost_kiasi').value) || 1;
    const gharama = parseFloat(document.getElementById('cost_gharama').value) || 0;
    
    // Validate
    if (!jina) { 
        showNotification('Tafadhali ingiza jina la gharama.', 'error');
        document.getElementById('cost_jina').focus();
        return; 
    }
    if (!kundi) { 
        showNotification('Tafadhali chagua kundi la gharama.', 'error');
        document.getElementById('cost_kundi').focus();
        return; 
    }
    if (gharama <= 0) { 
        showNotification('Gharama lazima iwe kubwa kuliko sifuri.', 'error');
        document.getElementById('cost_gharama').focus();
        return; 
    }
    
    // Add to cart
    costCart.push({
        jina: jina,
        kundi: kundi,
        kiasi: kiasi,
        gharama: gharama
    });
    
    // Update UI
    renderCart();
    updateTotals();
    
    // Close modal
    closeCostModal();
    showNotification('✅ Gharama imeongezwa! Jumla: ' + costCart.length, 'success');
}

// Quick add from category buttons
function quickAddCost(kundi) {
    openCostModal();
    document.getElementById('cost_kundi').value = kundi;
    const labels = {
        malighafi: 'Malighafi', mishahara: 'Mishahara', usafiri: 'Usafiri',
        ufungashaji: 'Ufungashaji', umeme_na_nishati: 'Nishati', gharama_nyingine: 'Nyingine'
    };
    document.getElementById('cost_jina').placeholder = 'Ej. ' + (labels[kundi] || 'Gharama');
    document.getElementById('cost_jina').focus();
}

function renderCart() {
    const container = document.getElementById('costCartContainer');
    const summary = document.getElementById('cartSummary');

    if (costCart.length === 0) {
        container.innerHTML = `
            <div id="emptyCartMessage" class="text-center py-4 text-gray-500">
                <i class="fas fa-shopping-cart text-2xl mb-1 block text-gray-300"></i>
                <p class="text-xs">Bado hakuna gharama. Bonyeza "Ongeza Gharama"</p>
            </div>
        `;
        summary.classList.add('hidden');
        document.getElementById('costCount').textContent = '0';
        return;
    }

    summary.classList.remove('hidden');

    container.innerHTML = costCart.map((cost, index) => `
        <div class="cart-item">
            <div class="item-info">
                <div class="name">${escapeHtml(cost.jina)}</div>
                <div class="details">
                    <span class="category-badge ${cost.kundi}">${getCategoryLabel(cost.kundi)}</span>
                    <span class="ml-2">Kiasi: ${cost.kiasi}</span>
                    <span class="ml-2 font-medium text-amber-700">Tsh ${formatNumber(cost.gharama)}</span>
                </div>
            </div>
            <button type="button" onclick="removeFromCart(${index})"
                    class="text-red-500 hover:text-red-700 p-1 transition" title="Ondoa">
                <i class="fas fa-trash-alt"></i>
            </button>
        </div>
    `).join('');

    document.getElementById('costCount').textContent = costCart.length;
    document.getElementById('cartTotalItems').textContent = costCart.length;

    const total = costCart.reduce((sum, c) => sum + (c.gharama || 0), 0);
    document.getElementById('cartTotalCost').textContent = 'Tsh ' + formatNumber(total);
}
function removeFromCart(index) {
    if (confirm('Je, una uhakika unataka kuondoa gharama hii?')) {
        costCart.splice(index, 1);
        renderCart();
        updateTotals();
        showNotification('Gharama imeondolewa.', 'info');
    }
}

// ============================================
// HELPER FUNCTIONS
// ============================================
function getCategoryLabel(kundi) {
    const labels = {
        malighafi: 'Malighafi', mishahara: 'Mishahara', usafiri: 'Usafiri',
        ufungashaji: 'Ufungashaji', umeme_na_nishati: 'Nishati', gharama_nyingine: 'Nyingine'
    };
    return labels[kundi] || kundi;
}

function formatNumber(value) {
    if (value === null || value === undefined || isNaN(value)) return '0';
    return Number(value).toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// ============================================
// TOTALS & CALCULATIONS
// ============================================
function updateTotals() {
    const totalCost = costCart.reduce((sum, c) => sum + (parseFloat(c.gharama) || 0), 0);
    const quantity = parseFloat(document.getElementById('f_idadi').value) || 0;
    const costPerUnit = quantity > 0 ? totalCost / quantity : 0;
    
    document.getElementById('totalCostDisplay').textContent = 'Tsh ' + formatNumber(totalCost);
    document.getElementById('costPerUnitDisplay').textContent = 'Tsh ' + formatNumber(costPerUnit);
    document.getElementById('suggestedPriceDisplay').textContent = 'Tsh ' + formatNumber(costPerUnit);
    document.getElementById('quantityDisplay').textContent = quantity || 0;
    
    // Auto-suggest selling price
    const sellInput = document.getElementById('f_selling_price');
    if (costPerUnit > 0 && (!sellInput.value || parseFloat(sellInput.value) === 0)) {
        sellInput.value = Math.round(costPerUnit * 1.2);
        simulateProfit();
    } else if (costPerUnit > 0) {
        simulateProfit();
    }
    
    // Cost breakdown
    const breakdown = {};
    costCart.forEach(c => {
        if (c.kundi && c.gharama > 0) {
            breakdown[c.kundi] = (breakdown[c.kundi] || 0) + parseFloat(c.gharama);
        }
    });
    const container = document.getElementById('costBreakdownDisplay');
    if (Object.keys(breakdown).length === 0) {
        container.innerHTML = '';
    } else {
        container.innerHTML = '<span class="font-medium">Mgawanyo:</span> ' + 
            Object.entries(breakdown).map(([k, v]) => 
                `<span class="bg-white/50 px-2 py-0.5 rounded-full">${getCategoryLabel(k)}: Tsh ${formatNumber(v)}</span>`
            ).join(' ');
    }
}

function simulateProfit() {
    const sellingPrice = parseFloat(document.getElementById('f_selling_price').value) || 0;
    const totalCost = costCart.reduce((sum, c) => sum + (parseFloat(c.gharama) || 0), 0);
    const quantity = parseFloat(document.getElementById('f_idadi').value) || 0;
    const costPerUnit = quantity > 0 ? totalCost / quantity : 0;
    const results = document.getElementById('profitResults');
    
    if (sellingPrice <= 0 || costPerUnit <= 0 || quantity <= 0) {
        results.classList.add('hidden');
        return;
    }
    
    const profitPerItem = sellingPrice - costPerUnit;
    const profitPercentage = costPerUnit > 0 ? (profitPerItem / costPerUnit) * 100 : 0;
    
    document.getElementById('profitPerItem').textContent = 'Tsh ' + formatNumber(profitPerItem);
    document.getElementById('profitPercentage').textContent = profitPercentage.toFixed(1) + '%';
    document.getElementById('totalProfit').textContent = 'Tsh ' + formatNumber(profitPerItem * quantity);
    document.getElementById('totalRevenue').textContent = 'Tsh ' + formatNumber(sellingPrice * quantity);
    results.classList.remove('hidden');
}

// ============================================
// FORM SUBMISSION
// ============================================
document.getElementById('productionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (costCart.length === 0) { 
        showNotification('Tafadhali ongeza angalau gharama moja.', 'error'); 
        return; 
    }
    
    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Inahifadhi...';
    
    const data = {
        tarehe: document.getElementById('f_tarehe').value,
        jina: document.getElementById('f_jina').value,
        aina_bidhaa: document.getElementById('f_aina_bidhaa').value,
        maelezo: document.getElementById('f_maelezo').value,
        gharama: costCart,
        idadi: parseFloat(document.getElementById('f_idadi').value) || 0,
        kipimo: document.getElementById('f_kipimo').value
    };
    
    const editId = document.getElementById('edit_id').value;
    const url = editId ? `/uzalishaji/${editId}` : '{{ route("uzalishaji.store") }}';
    const method = editId ? 'PUT' : 'POST';
    
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        if (data.success) {
            showNotification('✅ ' + data.message, 'success');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            let msg = data.message || 'Kuna hitilafu.';
            if (data.errors) msg = Object.values(data.errors).flat().join('\n');
            showNotification('❌ ' + msg, 'error');
        }
    })
    .catch(error => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        showNotification('Kuna hitilafu ya mtandao.', 'error');
    });
});

function resetForm() {
    document.getElementById('edit_id').value = '';
    document.getElementById('f_tarehe').value = new Date().toISOString().split('T')[0];
    document.getElementById('f_jina').value = '';
    document.getElementById('f_aina_bidhaa').value = '';
    document.getElementById('f_maelezo').value = '';
    document.getElementById('f_idadi').value = '';
    document.getElementById('f_kipimo').value = '';
    document.getElementById('f_selling_price').value = '';
    costCart = [];
    renderCart();
    updateTotals();
    document.getElementById('profitResults').classList.add('hidden');
    document.getElementById('submitText').textContent = 'Hifadhi Uzalishaji';
    document.getElementById('cartSummary').classList.add('hidden');
    showNotification('Fomu imesafishwa.', 'info');
}

// ============================================
// CRUD OPERATIONS
// ============================================
function viewProduction(id) {
    openModal('viewModal');
    document.getElementById('viewContent').innerHTML = '<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-2xl text-amber-600"></i><p class="mt-2 text-gray-500">Inapakia...</p></div>';
    
    fetch(`/uzalishaji/${id}/summary`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const p = data.data;
            document.getElementById('viewContent').innerHTML = `
                <div class="space-y-3">
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <div><span class="text-gray-500">Jina:</span> <span class="font-medium">${escapeHtml(p.jina)}</span></div>
                        <div><span class="text-gray-500">Aina:</span> <span class="font-medium">${escapeHtml(p.aina_bidhaa)}</span></div>
                        <div><span class="text-gray-500">Tarehe:</span> <span class="font-medium">${p.tarehe}</span></div>
                        <div><span class="text-gray-500">Hali:</span> <span class="font-medium ${p.is_completed ? 'text-green-600' : 'text-amber-600'}">${p.is_completed ? '✅ Imekamilika' : '⏳ Inaendelea'}</span></div>
                        <div><span class="text-gray-500">Idadi:</span> <span class="font-medium">${formatNumber(p.quantity)} ${escapeHtml(p.unit)}</span></div>
                        <div><span class="text-gray-500">Gharama kwa Moja:</span> <span class="font-medium text-amber-600">Tsh ${formatNumber(p.cost_per_unit)}</span></div>
                        <div><span class="text-gray-500">Jumla Gharama:</span> <span class="font-bold text-amber-700">Tsh ${formatNumber(p.total_cost)}</span></div>
                        <div><span class="text-gray-500">Bei Pendekezwa:</span> <span class="font-medium text-green-600">Tsh ${formatNumber(p.suggested_price)}</span></div>
                    </div>
                    ${p.product_id ? `<div class="bg-green-50 p-2 rounded text-center text-sm text-green-700"><i class="fas fa-check-circle mr-1"></i>Bidhaa imeongezwa kwenye stoo</div>` : ''}
                    ${Object.keys(p.costs_by_category || {}).length > 0 ? `
                    <div class="border-t border-gray-200 pt-2">
                        <h5 class="text-xs font-semibold text-gray-600 mb-1">Gharama kwa Kundi:</h5>
                        <div class="flex flex-wrap gap-1">${Object.entries(p.costs_by_category || {}).map(([k, v]) => 
                            `<span class="category-badge ${k}">${getCategoryLabel(k)}: Tsh ${formatNumber(v)}</span>`
                        ).join(' ')}</div>
                    </div>` : ''}
                </div>
            `;
        } else {
            document.getElementById('viewContent').innerHTML = `<p class="text-red-500">${data.message || 'Hitilafu'}</p>`;
        }
    })
    .catch(() => {
        document.getElementById('viewContent').innerHTML = `<p class="text-red-500">Hitilafu ya mtandao</p>`;
    });
}

function editProduction(id) {
    switchTab('ingiza');
    showNotification('Inapakia data ya uzalishaji...', 'info');
    
    fetch(`/uzalishaji/${id}/summary`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const p = data.data;
            document.getElementById('edit_id').value = p.id;
            document.getElementById('f_tarehe').value = p.tarehe;
            document.getElementById('f_jina').value = p.jina;
            document.getElementById('f_aina_bidhaa').value = p.aina_bidhaa;
            document.getElementById('f_maelezo').value = p.maelezo || '';
            document.getElementById('f_idadi').value = p.quantity;
            document.getElementById('f_kipimo').value = p.unit;
            document.getElementById('f_selling_price').value = Math.round(p.suggested_price * 1.2) || '';
            document.getElementById('submitText').textContent = 'Sasisha Uzalishaji';
            
            if (p.costs_by_category && Object.keys(p.costs_by_category).length > 0) {
                costCart = Object.entries(p.costs_by_category).map(([kundi, total]) => 
                    ({ jina: kundi.charAt(0).toUpperCase() + kundi.slice(1), kundi: kundi, kiasi: 1, gharama: total })
                );
            } else {
                costCart = [];
            }
            renderCart();
            updateTotals();
            simulateProfit();
            showNotification('Data imepakiwa!', 'success');
        } else {
            showNotification(data.message || 'Hitilafu', 'error');
        }
    })
    .catch(() => {
        showNotification('Hitilafu ya mtandao', 'error');
    });
}

function completeProduction(id) {
    openModal('completeModal');
    document.getElementById('completeContent').innerHTML = '<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-2xl text-amber-600"></i><p class="mt-2 text-gray-500">Inapakia...</p></div>';
    
    fetch(`/uzalishaji/${id}/summary`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const p = data.data;
            document.getElementById('completeContent').innerHTML = `
                <form id="completeForm" class="space-y-3">
                    <input type="hidden" id="comp_production_id" value="${p.id}">
                    <div class="bg-amber-50 p-3 rounded-lg text-sm border border-amber-200">
                        <p class="font-medium text-amber-800">Muhtasari</p>
                        <div class="grid grid-cols-2 gap-1 mt-1 text-xs">
                            <span class="text-gray-600">Jina:</span><span class="font-medium">${escapeHtml(p.jina)}</span>
                            <span class="text-gray-600">Aina:</span><span class="font-medium">${escapeHtml(p.aina_bidhaa)}</span>
                            <span class="text-gray-600">Idadi:</span><span class="font-medium">${formatNumber(p.quantity)} ${escapeHtml(p.unit)}</span>
                            <span class="text-gray-600">Gharama kwa Moja:</span><span class="font-medium text-amber-600">Tsh ${formatNumber(p.cost_per_unit)}</span>
                            <span class="text-gray-600">Bei Pendekezwa:</span><span class="font-medium text-green-600">Tsh ${formatNumber(p.suggested_price)}</span>
                        </div>
                    </div>
                    <div><label class="block text-xs font-medium text-gray-700 mb-1">Jina la Bidhaa *</label>
                        <input type="text" id="comp_jina_bidhaa" required value="${escapeHtml(p.jina)}"
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"></div>
                    <div><label class="block text-xs font-medium text-gray-700 mb-1">Kipimo *</label>
                        <input type="text" id="comp_kipimo" required value="${escapeHtml(p.unit)}"
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"></div>
                    <div><label class="block text-xs font-medium text-gray-700 mb-1">Idadi *</label>
                        <input type="number" id="comp_idadi" required step="0.01" min="0.01" value="${p.quantity}"
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"></div>
                    <div><label class="block text-xs font-medium text-gray-700 mb-1">Bei ya Kununua *</label>
                        <input type="number" id="comp_bei_nunua" required step="0.01" min="0" value="${p.suggested_price}"
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"></div>
                    <div><label class="block text-xs font-medium text-gray-700 mb-1">Bei ya Kuuza *</label>
                        <input type="number" id="comp_bei_kuuza" required step="0.01" min="0" value="${Math.round(p.suggested_price * 1.2)}"
                               oninput="updateCompleteProfit()"
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                        <div class="mt-1 text-xs" id="compProfitPreview">Faida: Tsh 0 (0%)</div>
                    </div>
                    <div><label class="block text-xs font-medium text-gray-700 mb-1">Bei ya Jumla (hiari)</label>
                        <input type="number" id="comp_bei_jumla" step="0.01" min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"></div>
                    <div><label class="block text-xs font-medium text-gray-700 mb-1">Barcode (hiari)</label>
                        <input type="text" id="comp_barcode"
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"></div>
                    <div><label class="block text-xs font-medium text-gray-700 mb-1">Tarehe ya Mwisho (hiari)</label>
                        <input type="date" id="comp_expiry"
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"></div>
                    <div class="flex gap-2 pt-2 border-t border-gray-200">
                        <button type="submit" id="completeSubmitBtn"
                                class="flex-1 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm font-medium">
                            <i class="fas fa-save mr-1"></i> Hifadhi Bidhaa
                        </button>
                        <button type="button" onclick="closeModal('completeModal')"
                                class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 text-sm">Ghairi</button>
                    </div>
                </form>
            `;
            
            document.getElementById('completeForm').addEventListener('submit', function(e) {
                e.preventDefault();
                submitComplete();
            });
            
            updateCompleteProfit();
        } else {
            document.getElementById('completeContent').innerHTML = `<p class="text-red-500">${data.message || 'Hitilafu'}</p>`;
        }
    })
    .catch(() => {
        document.getElementById('completeContent').innerHTML = '<p class="text-red-500">Hitilafu ya mtandao</p>';
    });
}

function updateCompleteProfit() {
    const beiNunua = parseFloat(document.getElementById('comp_bei_nunua')?.value) || 0;
    const beiKuuza = parseFloat(document.getElementById('comp_bei_kuuza')?.value) || 0;
    const profit = beiKuuza - beiNunua;
    const pct = beiNunua > 0 ? (profit / beiNunua * 100) : 0;
    const preview = document.getElementById('compProfitPreview');
    if (preview) {
        preview.innerHTML = beiKuuza > 0 && beiNunua > 0 ?
            `Faida: <span class="font-medium text-green-600">Tsh ${formatNumber(profit)}</span> (${pct.toFixed(0)}%)` :
            'Faida: -';
    }
}

function submitComplete() {
    const id = document.getElementById('comp_production_id').value;
    const submitBtn = document.getElementById('completeSubmitBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Inahifadhi...';
    
    const formData = new FormData();
    formData.append('jina_bidhaa', document.getElementById('comp_jina_bidhaa').value);
    formData.append('kipimo', document.getElementById('comp_kipimo').value);
    formData.append('idadi', document.getElementById('comp_idadi').value);
    formData.append('bei_nunua', document.getElementById('comp_bei_nunua').value);
    formData.append('bei_kuuza', document.getElementById('comp_bei_kuuza').value);
    const beiJumla = document.getElementById('comp_bei_jumla').value;
    if (beiJumla) formData.append('bei_jumla', beiJumla);
    const barcode = document.getElementById('comp_barcode').value;
    if (barcode) formData.append('barcode', barcode);
    const expiry = document.getElementById('comp_expiry').value;
    if (expiry) formData.append('expiry', expiry);
    
    fetch(`/uzalishaji/${id}/complete`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        if (data.success) {
            showNotification('✅ ' + data.message, 'success');
            closeModal('completeModal');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            let msg = data.message || 'Hitilafu.';
            if (data.errors) msg = Object.values(data.errors).flat().join('\n');
            showNotification('❌ ' + msg, 'error');
        }
    })
    .catch(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        showNotification('Kuna hitilafu.', 'error');
    });
}

function deleteProduction(id) {
    currentDeleteId = id;
    const row = document.querySelector(`tr[data-id="${id}"]`);
    const name = row ? row.querySelector('.font-medium')?.textContent || 'Uzalishaji' : 'Uzalishaji';
    document.getElementById('deleteItemName').textContent = name;
    document.getElementById('confirmDeleteBtn').onclick = function() {
        fetch(`/uzalishaji/${currentDeleteId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showNotification('✅ ' + data.message, 'success');
                closeModal('deleteModal');
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showNotification('❌ ' + data.message, 'error');
            }
        })
        .catch(() => showNotification('Kuna hitilafu.', 'error'));
    };
    openModal('deleteModal');
}

function duplicateProduction(id) {
    if (!confirm('Je, unataka kunakili uzalishaji huu?')) return;
    fetch(`/uzalishaji/${id}/duplicate`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showNotification('✅ ' + data.message, 'success');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showNotification('❌ ' + data.message, 'error');
        }
    })
    .catch(() => showNotification('Kuna hitilafu.', 'error'));
}

// ============================================
// MODAL HELPERS
// ============================================
function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
    document.body.style.overflow = '';
}

// ============================================
// FILTERS
// ============================================
function applyFilters() {
    const params = new URLSearchParams();
    const search = document.getElementById('search-input')?.value;
    const status = document.getElementById('status-filter')?.value;
    if (search) params.append('search', search);
    if (status) params.append('status', status);
    window.location.href = '?' + params.toString();
}

// ============================================
// REPORTS
// ============================================
function loadReport(period) {
    const content = document.getElementById('reportContent');
    content.innerHTML = '<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-2xl text-amber-600"></i><p class="mt-2 text-gray-500">Inapakia...</p></div>';
    
    fetch(`/uzalishaji/reports?period=${period}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const d = data.data;
            
            if (!d.productions || d.productions.length === 0) {
                content.innerHTML = `
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-chart-bar text-3xl mb-2 block text-gray-300"></i>
                        <p>Hakuna uzalishaji uliokamilika kwa kipindi hiki</p>
                    </div>
                `;
                return;
            }
            
            content.innerHTML = `
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
                    <div class="bg-amber-50 p-3 rounded text-center border border-amber-200">
                        <div class="text-xs text-gray-600">Jumla ya Uzalishaji</div>
                        <div class="font-bold text-amber-700 text-lg">${d.total_productions}</div>
                    </div>
                    <div class="bg-blue-50 p-3 rounded text-center border border-blue-200">
                        <div class="text-xs text-gray-600">Jumla ya Bidhaa</div>
                        <div class="font-bold text-blue-700 text-lg">${formatNumber(d.total_quantity)}</div>
                    </div>
                    <div class="bg-purple-50 p-3 rounded text-center border border-purple-200">
                        <div class="text-xs text-gray-600">Jumla ya Gharama</div>
                        <div class="font-bold text-purple-700 text-lg">Tsh ${formatNumber(d.total_cost)}</div>
                    </div>
                    <div class="bg-green-50 p-3 rounded text-center border border-green-200">
                        <div class="text-xs text-gray-600">Jumla ya Faida</div>
                        <div class="font-bold text-green-700 text-lg">Tsh ${formatNumber(d.total_profit)}</div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-amber-50 border-b border-gray-200">
                                <th class="px-3 py-2 text-left font-medium text-amber-800">Jina</th>
                                <th class="px-3 py-2 text-left font-medium text-amber-800">Aina</th>
                                <th class="px-3 py-2 text-right font-medium text-amber-800">Idadi</th>
                                <th class="px-3 py-2 text-right font-medium text-amber-800">Gharama</th>
                                <th class="px-3 py-2 text-right font-medium text-amber-800">Faida</th>
                                <th class="px-3 py-2 text-center font-medium text-amber-800">Tarehe</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${d.productions.map(p => `
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="px-3 py-2 font-medium">${escapeHtml(p.jina)}</td>
                                    <td class="px-3 py-2">${escapeHtml(p.aina_bidhaa)}</td>
                                    <td class="px-3 py-2 text-right">${formatNumber(p.idadi_iliyozalishwa)}</td>
                                    <td class="px-3 py-2 text-right text-purple-700 font-medium">Tsh ${formatNumber(p.jumla_gharama)}</td>
                                    <td class="px-3 py-2 text-right text-green-600 font-medium">Tsh ${formatNumber(p.faida_ya_jumla || 0)}</td>
                                    <td class="px-3 py-2 text-center text-gray-500 text-xs">${new Date(p.tarehe).toLocaleDateString('sw')}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            `;
        } else {
            content.innerHTML = `<p class="text-red-500 text-center py-4">${data.message || 'Hitilafu'}</p>`;
        }
    })
    .catch(() => {
        content.innerHTML = '<p class="text-red-500 text-center py-4">Hitilafu ya mtandao</p>';
    });
}

// ============================================
// NOTIFICATIONS
// ============================================
function showNotification(message, type = 'info') {
    const container = document.getElementById('notification-container');
    if (!container) return;
    const colors = {
        success: 'bg-emerald-50 border-emerald-200 text-emerald-800',
        error: 'bg-red-50 border-red-200 text-red-800',
        warning: 'bg-amber-50 border-amber-200 text-amber-800',
        info: 'bg-blue-50 border-blue-200 text-blue-800'
    };
    const div = document.createElement('div');
    div.className = `rounded border px-4 py-3 text-sm font-medium mb-2 ${colors[type] || colors.info} shadow-sm animate-fade-in`;
    div.textContent = message;
    container.appendChild(div);
    setTimeout(() => {
        div.style.opacity = '0';
        div.style.transition = 'opacity 0.3s';
        setTimeout(() => div.remove(), 300);
    }, 4000);
}

// ============================================
// INITIALIZATION
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    const savedTab = sessionStorage.getItem('uzalishaji_tab') || 'orodha';
    switchTab(savedTab);
    
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            switchTab(this.dataset.tab);
        });
    });
    
    document.getElementById('search-input')?.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') applyFilters();
    });
    
    // Initialize empty cart
    renderCart();
    updateTotals();
    
    // Close modals on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            ['viewModal', 'completeModal', 'deleteModal', 'costModal'].forEach(id => {
                const el = document.getElementById(id);
                if (el && !el.classList.contains('hidden')) {
                    el.classList.add('hidden');
                    document.body.style.overflow = '';
                }
            });
        }
    });
});
</script>
@endpush