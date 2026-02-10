@extends('layouts.app')

@section('title', 'Bidhaa')

@section('page-title', 'Bidhaa')
@section('page-subtitle', now()->format('d/m/Y'))

@section('content')
<div class="space-y-4" id="app-container" data-current-page="{{ request()->get('page', 1) }}">
    <!-- Notifications -->
    <div id="notification-container" class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-sm px-4 pointer-events-none">
        @if(session('success'))
        <div class="rounded border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 mb-2 shadow-sm">
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="rounded border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800 mb-2 shadow-sm">
            {{ session('error') }}
        </div>
        @endif
    </div>

    <!-- Stats with Links -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <a href="{{ route('bidhaa.index') }}" class="block bg-white p-3 rounded-lg border border-emerald-200 shadow-sm hover:bg-emerald-50 transition-colors">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Jumla ya Bidhaa</p>
                    <p class="text-xl font-bold text-emerald-700">{{ $totalProducts }}</p>
                </div>
                <i class="fas fa-boxes text-emerald-500 text-lg"></i>
            </div>
        </a>
        <a href="{{ route('bidhaa.index') }}?filter=available" class="block bg-white p-3 rounded-lg border border-blue-200 shadow-sm hover:bg-blue-50 transition-colors">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Bidhaa Zilizopo</p>
                    <p class="text-xl font-bold text-blue-700">{{ $availableProducts }}</p>
                </div>
                <i class="fas fa-cubes text-blue-500 text-lg"></i>
            </div>
        </a>
        <a href="{{ route('bidhaa.index') }}?filter=low_stock" class="block bg-white p-3 rounded-lg border border-amber-200 shadow-sm hover:bg-amber-50 transition-colors">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Zinazokaribia Kuisha</p>
                    <p class="text-xl font-bold text-amber-700">{{ $lowStockProducts }}</p>
                </div>
                <i class="fas fa-exclamation-triangle text-amber-500 text-lg"></i>
            </div>
        </a>
        <a href="{{ route('bidhaa.index') }}?filter=expired" class="block bg-white p-3 rounded-lg border border-red-200 shadow-sm hover:bg-red-50 transition-colors">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Zilizo Expire</p>
                    <p class="text-xl font-bold text-red-700">{{ $expiredProducts }}</p>
                </div>
                <i class="fas fa-calendar-times text-red-500 text-lg"></i>
            </div>
        </a>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm">
        <div class="flex">
            <button data-tab="taarifa" class="tab-button flex-1 py-3 px-4 text-sm font-medium border-r border-gray-200 bg-emerald-50 text-emerald-700">
                <i class="fas fa-table mr-2"></i> Orodha
            </button>
            <button data-tab="ingiza" class="tab-button flex-1 py-3 px-4 text-sm font-medium border-r border-gray-200 text-gray-600 hover:bg-gray-50">
                <i class="fas fa-plus mr-2"></i> Ingiza
            </button>
            <button data-tab="excel" class="tab-button flex-1 py-3 px-4 text-sm font-medium text-gray-600 hover:bg-gray-50">
                <i class="fas fa-file-excel mr-2"></i> Excel
            </button>
        </div>
    </div>

    <!-- TAB 1: Orodha -->
    <div id="taarifa-tab-content" class="tab-content space-y-3">
        <!-- Search Bar -->
        <div class="bg-white p-3 rounded-lg border border-gray-200 shadow-sm relative">
            <div class="flex flex-col sm:flex-row gap-2 sm:items-center">
                <div class="flex-1 relative">
                    <input 
                        type="text" 
                        id="search-input"
                        placeholder="Tafuta bidhaa, aina, barcode... (tafuta kwenye bidhaa zote)" 
                        class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500"
                        value="{{ request()->search }}"
                        autocomplete="off"
                    >
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <button id="clear-search" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 hidden">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="flex gap-2">
                    <button onclick="printProducts()" class="px-3 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 text-sm font-medium">
                        <i class="fas fa-print mr-1"></i> Print
                    </button>
                    <button onclick="exportPDF()" class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm font-medium">
                        <i class="fas fa-file-pdf mr-1"></i> PDF
                    </button>
                </div>
            </div>
            
            <!-- Search Results Dropdown -->
            <div id="search-results-dropdown" class="hidden absolute z-50 mt-1 w-full bg-white rounded-lg shadow-lg border border-gray-200 max-h-96 overflow-y-auto" style="top: 100%;">
                <div id="search-results" class="py-2"></div>
                <div id="search-loading" class="hidden p-4 text-center text-gray-500">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Inatafuta kwenye bidhaa zote...
                </div>
            </div>
        </div>

        <!-- Products Table -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-emerald-50">
                            <th class="px-4 py-2 text-left font-medium text-emerald-800">Bidhaa</th>
                            <th class="px-4 py-2 text-left font-medium text-emerald-800 hidden sm:table-cell">Aina</th>
                            <th class="px-4 py-2 text-center font-medium text-emerald-800">Idadi</th>
                            <th class="px-4 py-2 text-right font-medium text-emerald-800">Bei</th>
                            <th class="px-4 py-2 text-left font-medium text-emerald-800 hidden lg:table-cell">Expiry</th>
                            <th class="px-4 py-2 text-center font-medium text-emerald-800 print:hidden">Vitendo</th>
                        </tr>
                    </thead>
                    <tbody id="products-tbody" class="divide-y divide-gray-100">
                        @forelse($bidhaa as $item)
                            <tr class="product-row hover:bg-gray-50" data-product='@json($item)'>
                                <td class="px-4 py-2">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 bg-emerald-100 rounded flex items-center justify-center text-emerald-800 font-bold text-sm mr-2">
                                            {{ substr($item->jina, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900 text-sm product-name">{{ $item->jina }}</div>
                                            <div class="text-xs text-gray-500 sm:hidden">{{ $item->aina }}</div>
                                            @if($item->barcode)
                                            <div class="text-xs text-emerald-600">#{{ $item->barcode }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-2 hidden sm:table-cell">
                                    <span class="text-sm text-gray-700">{{ $item->aina }}</span>
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium 
                                        @if($item->idadi < 10 && $item->idadi > 0) bg-amber-100 text-amber-800
                                        @elseif($item->idadi == 0) bg-gray-100 text-gray-800
                                        @else bg-emerald-100 text-emerald-800 @endif">
                                        {{ $item->idadi }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-right">
                                    <div class="text-sm font-bold text-emerald-700">{{ number_format($item->bei_kuuza, 0) }}</div>
                                    <div class="text-xs text-gray-500">Nunua: {{ number_format($item->bei_nunua, 0) }}</div>
                                </td>
                                <td class="px-4 py-2 hidden lg:table-cell">
                                    @if($item->expiry)
                                        <div class="text-xs 
                                            @if($item->expiry < now()) text-red-600
                                            @elseif(\Carbon\Carbon::parse($item->expiry)->diffInDays(now()) < 30) text-amber-600
                                            @else text-gray-600 @endif">
                                            {{ \Carbon\Carbon::parse($item->expiry)->format('d/m/Y') }}
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400">--</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-center print:hidden">
                                    <div class="flex justify-center space-x-2">
                                        <button class="edit-product-btn text-emerald-600 hover:text-emerald-800"
                                                data-id="{{ $item->id }}" title="Badili">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="delete-product-btn text-red-600 hover:text-red-800"
                                                data-id="{{ $item->id }}" data-name="{{ $item->jina }}" title="Futa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                    <i class="fas fa-boxes text-3xl mb-2 text-gray-300"></i>
                                    <p>Hakuna bidhaa bado</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($bidhaa->hasPages())
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $bidhaa->links() }}
            </div>
            @endif
        </div>

        <!-- Search Status -->
        <div id="search-status" class="text-center text-sm text-gray-600 hidden">
            <p id="search-result-count"></p>
        </div>

        <!-- Clear Filter Button -->
        @if(request('filter'))
        <div class="text-center">
            <a href="{{ route('bidhaa.index') }}" class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-700 rounded text-sm hover:bg-gray-200">
                <i class="fas fa-times mr-1"></i> Ondoa Filter
            </a>
        </div>
        @endif
    </div>

    <!-- TAB 2: Ingiza -->
    <div id="ingiza-tab-content" class="tab-content hidden">
        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
            <form method="POST" action="{{ route('bidhaa.store') }}" id="product-form" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Jina la Bidhaa *</label>
                        <input type="text" name="jina" 
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                               placeholder="Ingiza jina" required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Aina *</label>
                        <input type="text" name="aina" 
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                               placeholder="Aina ya bidhaa" required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Kipimo</label>
                        <input type="text" name="kipimo" 
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                               placeholder="Mf. 500ml, 1kg">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Idadi *</label>
                        <input type="number" name="idadi" min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                               placeholder="Idadi" required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Bei Nunua (TZS) *</label>
                        <input type="number" step="0.01" name="bei_nunua" id="buy-price"
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                               placeholder="0.00" required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Bei Kuuza (TZS) *</label>
                        <input type="number" step="0.01" name="bei_kuuza" id="sell-price"
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                               placeholder="0.00" required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Tarehe ya Mwisho</label>
                        <input type="date" name="expiry" 
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Barcode</label>
                        <input type="text" name="barcode" 
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                               placeholder="Barcode (hiari)">
                    </div>
                </div>
                <div id="price-error" class="text-red-600 text-xs font-medium hidden"></div>
                <div class="flex gap-2 pt-4 border-t border-gray-200">
                    <button type="submit" 
                            class="flex-1 bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700 text-sm font-medium">
                        <i class="fas fa-save mr-1"></i> Hifadhi
                    </button>
                    <button type="reset" 
                            class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 text-sm">
                        <i class="fas fa-redo mr-1"></i> Safisha
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- TAB 3: Excel Upload -->
    <div id="excel-tab-content" class="tab-content hidden">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <!-- Upload Card -->
            <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                <div class="mb-4">
                    <div class="flex items-center mb-3">
                        <div class="h-10 w-10 bg-emerald-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-upload text-emerald-600"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-900">Pakia Excel/CSV</h3>
                            <p class="text-xs text-gray-500">Pakia faili la Excel, CSV au TXT</p>
                        </div>
                    </div>
                    
                    <form method="POST" action="{{ route('bidhaa.uploadExcel') }}" enctype="multipart/form-data" id="excel-upload-form" class="space-y-3">
                        @csrf
                        <div>
                            <input type="file" name="excel_file" accept=".xlsx,.xls,.csv,.txt" 
                                   class="block w-full text-sm text-gray-700 border border-gray-300 rounded p-2 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                                   required id="excel-file-input">
                            <p class="text-xs text-gray-500 mt-1">.xlsx, .xls, .csv, .txt (Max: 10MB)</p>
                        </div>
                        <button type="submit" 
                                class="w-full bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700 text-sm font-medium flex items-center justify-center">
                            <i class="fas fa-upload mr-1"></i> Pakia & Hifadhi
                        </button>
                    </form>
                    
                    <!-- Upload Progress -->
                    <div id="upload-progress" class="hidden mt-3">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-medium text-emerald-700">Inapakia...</span>
                            <span class="text-xs text-emerald-700" id="progress-percentage">0%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                            <div id="progress-bar" class="bg-emerald-600 h-1.5 rounded-full" style="width: 0%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1" id="upload-status">Inaanza upakiaji...</p>
                    </div>
                </div>
            </div>

            <!-- Download & Results Card -->
            <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                <div class="mb-4">
                    <div class="flex items-center mb-3">
                        <div class="h-10 w-10 bg-amber-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-download text-amber-600"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-900">Matokeo & Sampuli</h3>
                            <p class="text-xs text-gray-500">Angalia matokeo na pakua sampuli</p>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <!-- Upload Results -->
                        <div id="upload-results" class="hidden space-y-2">
                            <div class="border border-emerald-200 rounded p-3 bg-emerald-50">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="text-xs font-medium text-emerald-800 flex items-center">
                                        <i class="fas fa-check-circle mr-1"></i> Matokeo ya Upakiaji
                                    </h4>
                                    <button onclick="document.getElementById('upload-results').classList.add('hidden')" 
                                            class="text-emerald-500 hover:text-emerald-700 text-xs">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div class="space-y-1">
                                    <div class="flex justify-between text-xs">
                                        <span class="text-emerald-700">Bidhaa Zilizoongezwa:</span>
                                        <span class="font-medium" id="success-count">0</span>
                                    </div>
                                    <div class="flex justify-between text-xs">
                                        <span class="text-amber-700">Hitilafu Zilizopatikana:</span>
                                        <span class="font-medium" id="error-count">0</span>
                                    </div>
                                    <div class="flex justify-between text-xs">
                                        <span class="text-gray-600">Mistari Iliyorudishwa:</span>
                                        <span class="font-medium" id="skipped-count">0</span>
                                    </div>
                                    <div class="flex justify-between text-xs">
                                        <span class="text-blue-700">Jumla ya Mistari:</span>
                                        <span class="font-medium" id="total-count">0</span>
                                    </div>
                                </div>
                                <div id="error-list" class="mt-2 hidden">
                                    <p class="text-xs font-medium text-red-700 mb-1">Hitilafu Zilizopatikana:</p>
                                    <div class="text-xs text-red-600 space-y-1 max-h-40 overflow-y-auto" id="error-items"></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Download Sample -->
                        <a href="{{ route('bidhaa.downloadSample') }}" 
                           class="block w-full bg-amber-600 text-white px-4 py-2 rounded hover:bg-amber-700 text-sm font-medium text-center">
                            <i class="fas fa-file-download mr-1"></i> Pakua Sampuli
                        </a>
                        
                        <!-- Format Info -->
                        <div class="border border-emerald-200 rounded p-3 bg-emerald-50">
                            <h4 class="text-xs font-medium text-emerald-800 mb-2">
                                <i class="fas fa-table mr-1"></i> Muundo wa Faili
                            </h4>
                            <div class="overflow-x-auto">
                                <table class="w-full text-xs border border-emerald-200">
                                    <thead>
                                        <tr class="bg-emerald-100">
                                            <th class="px-2 py-1 border border-emerald-200 text-emerald-800">Jina *</th>
                                            <th class="px-2 py-1 border border-emerald-200 text-emerald-800">Aina *</th>
                                            <th class="px-2 py-1 border border-emerald-200 text-emerald-800 text-emerald-500">Kipimo</th>
                                            <th class="px-2 py-1 border border-emerald-200 text-emerald-800">Idadi *</th>
                                            <th class="px-2 py-1 border border-emerald-200 text-emerald-800">Bei_Nunua *</th>
                                            <th class="px-2 py-1 border border-emerald-200 text-emerald-800">Bei_Kuuza *</th>
                                            <th class="px-2 py-1 border border-emerald-200 text-emerald-800 text-emerald-500">Expiry</th>
                                            <th class="px-2 py-1 border border-emerald-200 text-emerald-800 text-emerald-500">Barcode</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="px-2 py-1 border border-emerald-200">Soda</td>
                                            <td class="px-2 py-1 border border-emerald-200">Vinywaji</td>
                                            <td class="px-2 py-1 border border-emerald-200 text-emerald-500">500ml</td>
                                            <td class="px-2 py-1 border border-emerald-200">100</td>
                                            <td class="px-2 py-1 border border-emerald-200">600</td>
                                            <td class="px-2 py-1 border border-emerald-200">1000</td>
                                            <td class="px-2 py-1 border border-emerald-200 text-emerald-500">2025-12-31</td>
                                            <td class="px-2 py-1 border border-emerald-200 text-emerald-500">123456789</td>
                                        </tr>
                                        <tr>
                                            <td class="px-2 py-1 border border-emerald-200">Mchele</td>
                                            <td class="px-2 py-1 border border-emerald-200">Chakula</td>
                                            <td class="px-2 py-1 border border-emerald-200 text-emerald-500">1kg</td>
                                            <td class="px-2 py-1 border border-emerald-200">50</td>
                                            <td class="px-2 py-1 border border-emerald-200">2500</td>
                                            <td class="px-2 py-1 border border-emerald-200">3500</td>
                                            <td class="px-2 py-1 border border-emerald-200 text-emerald-500"></td>
                                            <td class="px-2 py-1 border border-emerald-200 text-emerald-500"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <p class="text-xs text-emerald-700 mt-2">
                                <i class="fas fa-info-circle mr-1"></i> * Inahitajika | Sehemu hiari - weka tupu au N/A
                            </p>
                            <p class="text-xs text-blue-700 mt-1">
                                <i class="fas fa-lightbulb mr-1"></i> Maelekezo: Bei ya kuuza lazima iwe kubwa kuliko bei ya kununua
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="edit-modal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-lg shadow-lg w-full max-w-md mx-auto z-50 max-h-[90vh] overflow-y-auto">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-800">Rekebisha Bidhaa</h3>
        </div>
        <form id="edit-form" method="POST" class="p-4">
            @csrf
            @method('PUT')
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Jina la Bidhaa *</label>
                    <input type="text" name="jina" id="edit-jina"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                           required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Aina *</label>
                    <input type="text" name="aina" id="edit-aina"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                           required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Kipimo</label>
                    <input type="text" name="kipimo" id="edit-kipimo"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Idadi *</label>
                    <input type="number" name="idadi" id="edit-idadi" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                           required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Bei Nunua (TZS) *</label>
                    <input type="number" step="0.01" name="bei_nunua" id="edit-bei-nunua"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                           required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Bei Kuuza (TZS) *</label>
                    <input type="number" step="0.01" name="bei_kuuza" id="edit-bei-kuuza"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                           required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tarehe ya Mwisho</label>
                    <input type="date" name="expiry" id="edit-expiry"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Barcode</label>
                    <input type="text" name="barcode" id="edit-barcode"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                </div>
            </div>
            <div class="flex gap-2 pt-4 border-t border-gray-200 mt-4">
                <button type="button" id="close-edit-modal"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 text-sm">
                    Ghairi
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 text-sm font-medium">
                    Hifadhi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Modal -->
<div id="delete-modal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-lg shadow-lg w-full max-w-sm mx-auto z-50">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-800">Thibitisha Kufuta</h3>
        </div>
        <div class="p-4">
            <div class="text-center mb-4">
                <i class="fas fa-exclamation-triangle text-amber-500 text-2xl mb-2"></i>
                <p class="text-gray-700 text-sm mb-1">Una uhakika unataka kufuta?</p>
                <p class="text-gray-900 font-medium" id="delete-product-name"></p>
                <p class="text-gray-500 text-xs mt-2">Hatua hii haiwezi kutenduliwa</p>
            </div>
            <div class="flex gap-2">
                <button id="cancel-delete"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 text-sm">
                    Ghairi
                </button>
                <form id="delete-form" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="w-full px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm font-medium">
                        Futa
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Product Details Modal -->
<div id="product-details-modal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-lg shadow-lg w-full max-w-md mx-auto z-50 max-h-[90vh] overflow-y-auto">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-800" id="modal-product-name">Maelezo ya Bidhaa</h3>
            <button onclick="document.getElementById('product-details-modal').classList.add('hidden')" 
                    class="absolute right-4 top-4 text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-4" id="product-details-content">
            <!-- Content will be loaded here -->
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Fix for sidebar navigation blur issue */
.sidebar-nav .nav-link {
    display: flex;
    align-items: center;
    transition: all 0.3s ease;
}

.sidebar-nav .nav-link .nav-text {
    opacity: 1;
    transition: opacity 0.3s ease;
}

/* When sidebar is collapsed */
.sidebar-collapsed .sidebar-nav .nav-link .nav-text {
    opacity: 1 !important;
    position: absolute;
    left: 100%;
    margin-left: 15px;
    background: white;
    padding: 8px 12px;
    border-radius: 4px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    white-space: nowrap;
    z-index: 9999;
    visibility: hidden;
    opacity: 0;
}

.sidebar-collapsed .sidebar-nav .nav-link:hover .nav-text {
    visibility: visible;
    opacity: 1;
}

/* Search dropdown styles */
#search-results-dropdown {
    scrollbar-width: thin;
    scrollbar-color: #d1d5db #f3f4f6;
}

#search-results-dropdown::-webkit-scrollbar {
    width: 6px;
}

#search-results-dropdown::-webkit-scrollbar-track {
    background: #f3f4f6;
    border-radius: 3px;
}

#search-results-dropdown::-webkit-scrollbar-thumb {
    background-color: #d1d5db;
    border-radius: 3px;
}

.search-result-item:hover {
    background-color: #f9fafb;
}

/* Animation for search results */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-5px); }
    to { opacity: 1; transform: translateY(0); }
}

#search-results-dropdown {
    animation: fadeIn 0.2s ease-out;
}
</style>
@endpush

@push('scripts')
<script>
class SmartBidhaaManager {
    constructor() {
        this.currentTab = this.getSavedTab() || 'taarifa';
        this.searchTimeout = null;
        this.allSearchResults = [];
        this.isSearchActive = false;
        this.currentSearchTerm = '';
        this.init();
    }

    init() {
        this.bindEvents();
        this.showTab(this.currentTab);
        this.setupAjaxForms();
        this.fixSidebarNavigation();
    }

    fixSidebarNavigation() {
        const sidebar = document.querySelector('.sidebar');
        if (sidebar) {
            const navTexts = document.querySelectorAll('.sidebar-nav .nav-text');
            navTexts.forEach(text => {
                text.style.opacity = '1';
                text.style.visibility = 'visible';
            });
        }
    }

    getSavedTab() {
        return sessionStorage.getItem('bidhaa_tab') || 'taarifa';
    }

    saveTab(tab) {
        sessionStorage.setItem('bidhaa_tab', tab);
    }

    bindEvents() {
        // Tab navigation
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', (e) => {
                const tab = e.target.closest('.tab-button').dataset.tab;
                this.showTab(tab);
                this.saveTab(tab);
            });
        });

        // Enhanced search with real-time results
        this.bindSearchEvents();

        // Edit/Delete buttons
        this.bindProductActions();

        // Form validation
        this.bindFormValidation();

        // Modal events
        this.bindModalEvents();
    }

    bindSearchEvents() {
        const searchInput = document.getElementById('search-input');
        const searchResultsDropdown = document.getElementById('search-results-dropdown');
        const clearSearchBtn = document.getElementById('clear-search');
        const searchResults = document.getElementById('search-results');
        const searchLoading = document.getElementById('search-loading');
        
        if (!searchInput) return;
        
        searchInput.addEventListener('input', (e) => {
            const searchTerm = e.target.value.trim();
            this.currentSearchTerm = searchTerm;
            
            // Show/hide clear button
            if (clearSearchBtn) {
                clearSearchBtn.classList.toggle('hidden', !searchTerm);
            }
            
            // Clear previous timeout
            clearTimeout(this.searchTimeout);
            
            if (searchTerm.length >= 2) {
                // Show dropdown with loading
                if (searchResultsDropdown) {
                    searchResultsDropdown.classList.remove('hidden');
                }
                if (searchLoading) {
                    searchLoading.classList.remove('hidden');
                }
                if (searchResults) {
                    searchResults.innerHTML = '';
                }
                
                // Debounce search
                this.searchTimeout = setTimeout(() => {
                    this.searchAllProducts(searchTerm);
                }, 500);
                
                this.isSearchActive = true;
            } else {
                // Hide dropdown and clear results
                if (searchResultsDropdown) {
                    searchResultsDropdown.classList.add('hidden');
                }
                if (searchLoading) {
                    searchLoading.classList.add('hidden');
                }
                
                // Reset table to show all products
                this.resetTableToAllProducts();
                this.isSearchActive = false;
            }
        });
        
        // Clear search
        if (clearSearchBtn) {
            clearSearchBtn.addEventListener('click', () => {
                searchInput.value = '';
                this.currentSearchTerm = '';
                clearSearchBtn.classList.add('hidden');
                
                if (searchResultsDropdown) {
                    searchResultsDropdown.classList.add('hidden');
                }
                if (searchLoading) {
                    searchLoading.classList.add('hidden');
                }
                
                // Reset table to show all paginated products
                this.resetTableToAllProducts();
                this.isSearchActive = false;
            });
        }
        
        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!searchInput.contains(e.target) && 
                !searchResultsDropdown?.contains(e.target)) {
                searchResultsDropdown?.classList.add('hidden');
            }
        });
        
        // Handle Enter key to view all results
        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && this.currentSearchTerm.length >= 2) {
                e.preventDefault();
                if (searchResultsDropdown) {
                    searchResultsDropdown.classList.add('hidden');
                }
                this.showAllSearchResults();
            }
        });
    }

    bindProductActions() {
        // Edit buttons
        document.querySelectorAll('.edit-product-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                const row = e.target.closest('.product-row');
                const product = JSON.parse(row.dataset.product);
                this.editProduct(product);
            });
        });

        // Delete buttons
        document.querySelectorAll('.delete-product-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                const productId = e.target.closest('.delete-product-btn').dataset.id;
                const productName = e.target.closest('.delete-product-btn').dataset.name;
                this.deleteProduct(productId, productName);
            });
        });
    }

    bindFormValidation() {
        const productForm = document.getElementById('product-form');
        const editForm = document.getElementById('edit-form');
        const buyPriceInput = document.getElementById('buy-price');
        const sellPriceInput = document.getElementById('sell-price');
        const priceError = document.getElementById('price-error');

        // Validation for product form
        if (productForm && buyPriceInput && sellPriceInput) {
            const validatePrices = () => {
                const buyPrice = parseFloat(buyPriceInput.value);
                const sellPrice = parseFloat(sellPriceInput.value);

                if (buyPrice > sellPrice) {
                    priceError.textContent = '⚠️ Bei ya kununua haiwezi kuwa kubwa kuliko bei ya kuuza!';
                    priceError.classList.remove('hidden');
                    return false;
                } else {
                    priceError.classList.add('hidden');
                    return true;
                }
            };

            buyPriceInput.addEventListener('input', validatePrices);
            sellPriceInput.addEventListener('input', validatePrices);

            productForm.addEventListener('submit', (e) => {
                if (!validatePrices()) {
                    e.preventDefault();
                    e.stopPropagation();
                }
            });
        }

        // Validation for edit form
        if (editForm) {
            const editBuyPrice = editForm.querySelector('[name="bei_nunua"]');
            const editSellPrice = editForm.querySelector('[name="bei_kuuza"]');
            
            if (editBuyPrice && editSellPrice) {
                const editValidatePrices = () => {
                    const buyPrice = parseFloat(editBuyPrice.value);
                    const sellPrice = parseFloat(editSellPrice.value);
                    
                    if (buyPrice > sellPrice) {
                        editSellPrice.classList.add('border-red-500');
                        return false;
                    } else {
                        editSellPrice.classList.remove('border-red-500');
                        return true;
                    }
                };

                editBuyPrice.addEventListener('input', editValidatePrices);
                editSellPrice.addEventListener('input', editValidatePrices);

                editForm.addEventListener('submit', (e) => {
                    if (!editValidatePrices()) {
                        e.preventDefault();
                        e.stopPropagation();
                        this.showNotification('⚠️ Bei ya kununua haiwezi kuwa kubwa kuliko bei ya kuuza!', 'error');
                    }
                });
            }
        }
    }

    bindModalEvents() {
        // Edit modal
        const editModal = document.getElementById('edit-modal');
        const closeEditBtn = document.getElementById('close-edit-modal');

        if (closeEditBtn) {
            closeEditBtn.addEventListener('click', () => editModal.classList.add('hidden'));
        }
        
        if (editModal) {
            editModal.addEventListener('click', (e) => {
                if (e.target === editModal || e.target.classList.contains('modal-overlay')) {
                    editModal.classList.add('hidden');
                }
            });
        }

        // Delete modal
        const deleteModal = document.getElementById('delete-modal');
        const cancelDeleteBtn = document.getElementById('cancel-delete');

        if (cancelDeleteBtn) {
            cancelDeleteBtn.addEventListener('click', () => deleteModal.classList.add('hidden'));
        }
        
        if (deleteModal) {
            deleteModal.addEventListener('click', (e) => {
                if (e.target === deleteModal || e.target.classList.contains('modal-overlay')) {
                    deleteModal.classList.add('hidden');
                }
            });
        }

        // Product details modal
        const productDetailsModal = document.getElementById('product-details-modal');
        if (productDetailsModal) {
            productDetailsModal.addEventListener('click', (e) => {
                if (e.target === productDetailsModal || e.target.classList.contains('modal-overlay')) {
                    productDetailsModal.classList.add('hidden');
                }
            });
        }

        // Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                if (editModal) editModal.classList.add('hidden');
                if (deleteModal) deleteModal.classList.add('hidden');
                if (productDetailsModal) productDetailsModal.classList.add('hidden');
            }
        });
    }

    showTab(tabName) {
        document.querySelectorAll('.tab-button').forEach(button => {
            if (button.dataset.tab === tabName) {
                button.classList.add('bg-emerald-50', 'text-emerald-700');
                button.classList.remove('text-gray-600', 'hover:bg-gray-50');
            } else {
                button.classList.remove('bg-emerald-50', 'text-emerald-700');
                button.classList.add('text-gray-600', 'hover:bg-gray-50');
            }
        });

        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        
        const tabContent = document.getElementById(`${tabName}-tab-content`);
        if (tabContent) {
            tabContent.classList.remove('hidden');
        }
        this.currentTab = tabName;
    }

    async searchAllProducts(searchTerm) {
        if (!searchTerm || searchTerm.length < 2) return;
        
        const searchResults = document.getElementById('search-results');
        const searchLoading = document.getElementById('search-loading');
        
        if (!searchResults || !searchLoading) return;
        
        try {
            const response = await fetch(`/bidhaa/search?search=${encodeURIComponent(searchTerm)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            
            // Hide loading
            if (searchLoading) {
                searchLoading.classList.add('hidden');
            }
            
            if (data.success && data.data && data.data.length > 0) {
                this.allSearchResults = data.data;
                this.renderSearchResults(data.data);
                this.updateTableWithSearchResults(data.data);
            } else {
                searchResults.innerHTML = `
                    <div class="p-4 text-center text-gray-500">
                        <i class="fas fa-search text-gray-300 mb-2"></i>
                        <p>Hakuna bidhaa zinazolingana na "${searchTerm}"</p>
                    </div>
                `;
                this.allSearchResults = [];
                this.hideTableProducts();
            }
        } catch (error) {
            if (searchLoading) {
                searchLoading.classList.add('hidden');
            }
            searchResults.innerHTML = `
                <div class="p-4 text-center text-red-500">
                    <i class="fas fa-exclamation-triangle mb-2"></i>
                    <p>Hitilafu ya mtandao. Jaribu tena.</p>
                </div>
            `;
            console.error('Search error:', error);
        }
    }

    renderSearchResults(results) {
        const searchResults = document.getElementById('search-results');
        if (!searchResults) return;
        
        searchResults.innerHTML = '';
        
        // Header
        const header = document.createElement('div');
        header.className = 'px-4 py-2 border-b border-gray-100 bg-gray-50';
        header.innerHTML = `
            <div class="flex justify-between items-center">
                <span class="text-xs font-medium text-gray-600">
                    Matokeo ya utafutaji (${results.length})
                </span>
                <button onclick="window.bidhaaManager.showAllSearchResults()" 
                        class="text-xs text-emerald-600 hover:text-emerald-800 font-medium">
                    <i class="fas fa-external-link-alt mr-1"></i> Ona Zote
                </button>
            </div>
        `;
        searchResults.appendChild(header);
        
        // Limit to 10 results in dropdown
        const limitedResults = results.slice(0, 10);
        
        limitedResults.forEach(product => {
            const item = document.createElement('div');
            item.className = 'px-4 py-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0 search-result-item';
            item.dataset.product = JSON.stringify(product);
            
            // Determine stock status color
            let stockClass = 'bg-emerald-100 text-emerald-800';
            if (product.stock_status === 'low-stock') {
                stockClass = 'bg-amber-100 text-amber-800';
            } else if (product.stock_status === 'out-of-stock') {
                stockClass = 'bg-red-100 text-red-800';
            }
            
            item.innerHTML = `
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center mb-1">
                            <div class="h-6 w-6 bg-emerald-100 rounded flex items-center justify-center text-emerald-800 font-bold text-xs mr-2">
                                ${product.jina.charAt(0)}
                            </div>
                            <div class="font-medium text-sm text-gray-900 truncate">${product.jina}</div>
                        </div>
                        <div class="text-xs text-gray-600 mb-1">${product.aina}</div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs px-2 py-0.5 rounded ${stockClass}">
                                ${product.idadi} ${product.kipimo || 'pcs'}
                            </span>
                            <span class="text-xs font-medium text-emerald-700">
                                ${parseFloat(product.bei_kuuza).toLocaleString()} TZS
                            </span>
                        </div>
                    </div>
                    <div class="ml-2">
                        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                    </div>
                </div>
            `;
            
            item.addEventListener('click', () => {
                this.showProductDetails(product);
                document.getElementById('search-results-dropdown').classList.add('hidden');
            });
            
            searchResults.appendChild(item);
        });
        
        // Show "View all" if there are more results
        if (results.length > 10) {
            const viewAll = document.createElement('div');
            viewAll.className = 'px-4 py-3 bg-gray-50 border-t border-gray-100 text-center';
            viewAll.innerHTML = `
                <button onclick="window.bidhaaManager.showAllSearchResults()" 
                        class="text-xs text-emerald-600 hover:text-emerald-800 font-medium">
                    <i class="fas fa-list mr-1"></i> Ona matokeo ${results.length} yote
                </button>
            `;
            searchResults.appendChild(viewAll);
        }
    }

    updateTableWithSearchResults(results) {
        const tbody = document.getElementById('products-tbody');
        if (!tbody) return;
        
        // Get current page products
        const currentRows = document.querySelectorAll('.product-row');
        
        // Hide all current rows
        currentRows.forEach(row => {
            row.classList.add('hidden');
        });
        
        // Create a map of result IDs for quick lookup
        const resultIds = new Set(results.map(p => p.id));
        
        // Show only rows that match search results
        currentRows.forEach(row => {
            const productData = JSON.parse(row.dataset.product);
            if (resultIds.has(productData.id)) {
                row.classList.remove('hidden');
            }
        });
        
        // Show search status
        const searchStatus = document.getElementById('search-status');
        const searchResultCount = document.getElementById('search-result-count');
        if (searchStatus && searchResultCount) {
            searchStatus.classList.remove('hidden');
            searchResultCount.textContent = `Imepatikana: ${results.length} bidhaa zinazolingana na utafutaji`;
        }
    }

    hideTableProducts() {
        const currentRows = document.querySelectorAll('.product-row');
        currentRows.forEach(row => {
            row.classList.add('hidden');
        });
        
        // Show empty state
        const tbody = document.getElementById('products-tbody');
        if (tbody) {
            const emptyRow = document.createElement('tr');
            emptyRow.innerHTML = `
                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                    <i class="fas fa-search text-3xl mb-2 text-gray-300"></i>
                    <p>Hakuna bidhaa zinazolingana na utafutaji</p>
                </td>
            `;
            tbody.appendChild(emptyRow);
        }
    }

    resetTableToAllProducts() {
        // Show all rows
        const currentRows = document.querySelectorAll('.product-row');
        currentRows.forEach(row => {
            row.classList.remove('hidden');
        });
        
        // Hide search status
        const searchStatus = document.getElementById('search-status');
        if (searchStatus) {
            searchStatus.classList.add('hidden');
        }
        
        // Remove any empty state rows
        const tbody = document.getElementById('products-tbody');
        if (tbody) {
            const emptyRows = tbody.querySelectorAll('tr:not(.product-row)');
            emptyRows.forEach(row => row.remove());
        }
    }

    showAllSearchResults() {
        if (this.allSearchResults.length === 0) return;
        
        // Hide dropdown
        const searchResultsDropdown = document.getElementById('search-results-dropdown');
        if (searchResultsDropdown) {
            searchResultsDropdown.classList.add('hidden');
        }
        
        // Clear current table content
        const tbody = document.getElementById('products-tbody');
        if (!tbody) return;
        
        tbody.innerHTML = '';
        
        // Populate table with all search results
        this.allSearchResults.forEach(product => {
            const row = document.createElement('tr');
            row.className = 'product-row hover:bg-gray-50';
            row.dataset.product = JSON.stringify(product);
            
            // Format expiry date if exists
            let expiryHtml = '<span class="text-xs text-gray-400">--</span>';
            if (product.expiry) {
                const expiryDate = new Date(product.expiry);
                const today = new Date();
                const diffDays = Math.floor((expiryDate - today) / (1000 * 60 * 60 * 24));
                
                let expiryClass = 'text-gray-600';
                if (expiryDate < today) {
                    expiryClass = 'text-red-600';
                } else if (diffDays < 30) {
                    expiryClass = 'text-amber-600';
                }
                
                const formattedDate = expiryDate.toLocaleDateString('en-GB');
                expiryHtml = `<div class="text-xs ${expiryClass}">${formattedDate}</div>`;
            }
            
            // Stock status badge
            let stockClass = 'bg-emerald-100 text-emerald-800';
            if (product.stock_status === 'low-stock') {
                stockClass = 'bg-amber-100 text-amber-800';
            } else if (product.stock_status === 'out-of-stock') {
                stockClass = 'bg-gray-100 text-gray-800';
            }
            
            row.innerHTML = `
                <td class="px-4 py-2">
                    <div class="flex items-center">
                        <div class="h-8 w-8 bg-emerald-100 rounded flex items-center justify-center text-emerald-800 font-bold text-sm mr-2">
                            ${product.jina.charAt(0)}
                        </div>
                        <div>
                            <div class="font-medium text-gray-900 text-sm">${product.jina}</div>
                            <div class="text-xs text-gray-500 sm:hidden">${product.aina}</div>
                            ${product.barcode ? `<div class="text-xs text-emerald-600">#${product.barcode}</div>` : ''}
                        </div>
                    </div>
                </td>
                <td class="px-4 py-2 hidden sm:table-cell">
                    <span class="text-sm text-gray-700">${product.aina}</span>
                </td>
                <td class="px-4 py-2 text-center">
                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium ${stockClass}">
                        ${product.idadi}
                    </span>
                </td>
                <td class="px-4 py-2 text-right">
                    <div class="text-sm font-bold text-emerald-700">${parseFloat(product.bei_kuuza).toLocaleString()}</div>
                    <div class="text-xs text-gray-500">Nunua: ${parseFloat(product.bei_nunua).toLocaleString()}</div>
                </td>
                <td class="px-4 py-2 hidden lg:table-cell">
                    ${expiryHtml}
                </td>
                <td class="px-4 py-2 text-center print:hidden">
                    <div class="flex justify-center space-x-2">
                        <button class="edit-search-product text-emerald-600 hover:text-emerald-800" data-id="${product.id}" title="Badili">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="delete-search-product text-red-600 hover:text-red-800" data-id="${product.id}" title="Futa">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            `;
            
            tbody.appendChild(row);
        });
        
        // Update pagination - hide it since we're showing all results
        const pagination = document.querySelector('.pagination');
        if (pagination) {
            pagination.classList.add('hidden');
        }
        
        // Show search status
        const searchStatus = document.getElementById('search-status');
        const searchResultCount = document.getElementById('search-result-count');
        if (searchStatus && searchResultCount) {
            searchStatus.classList.remove('hidden');
            searchResultCount.innerHTML = `
                Kuonyesha <span class="font-bold">${this.allSearchResults.length}</span> bidhaa kutoka kwenye utafutaji
                <button onclick="window.bidhaaManager.resetToOriginalTable()" class="ml-2 text-xs text-emerald-600 hover:text-emerald-800">
                    <i class="fas fa-times mr-1"></i> Rejea kwenye orodha ya kawaida
                </button>
            `;
        }
        
        // Bind actions for search result rows
        this.bindSearchResultActions();
    }

    resetToOriginalTable() {
        // Reload the page to show original paginated content
        window.location.href = window.location.pathname + window.location.search.split('?')[0];
    }

    bindSearchResultActions() {
        // Bind edit buttons for search results
        document.querySelectorAll('.edit-search-product').forEach(button => {
            button.addEventListener('click', async (e) => {
                const productId = e.target.closest('button').dataset.id;
                await this.loadAndEditProduct(productId);
            });
        });
        
        // Bind delete buttons for search results
        document.querySelectorAll('.delete-search-product').forEach(button => {
            button.addEventListener('click', async (e) => {
                const productId = e.target.closest('button').dataset.id;
                await this.loadAndDeleteProduct(productId);
            });
        });
    }

    async loadAndEditProduct(productId) {
        try {
            const response = await fetch(`/bidhaa/${productId}/edit`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            if (data.success && data.data) {
                this.editProduct(data.data);
            } else {
                this.showNotification('Imeshindwa kupakua taarifa za bidhaa', 'error');
            }
        } catch (error) {
            console.error('Error loading product:', error);
            this.showNotification('Hitilafu ya mtandao', 'error');
        }
    }

    async loadAndDeleteProduct(productId) {
        try {
            const response = await fetch(`/bidhaa/${productId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            if (data.success && data.data) {
                this.deleteProduct(productId, data.data.jina);
            } else {
                this.showNotification('Imeshindwa kupakua taarifa za bidhaa', 'error');
            }
        } catch (error) {
            console.error('Error loading product:', error);
            this.showNotification('Hitilafu ya mtandao', 'error');
        }
    }

    showProductDetails(product) {
        const modal = document.getElementById('product-details-modal');
        const title = document.getElementById('modal-product-name');
        const content = document.getElementById('product-details-content');
        
        if (!modal || !title || !content) return;
        
        // Format expiry date
        let expiryText = '--';
        if (product.expiry) {
            const expiryDate = new Date(product.expiry);
            expiryText = expiryDate.toLocaleDateString('sw-TZ', {
                day: '2-digit',
                month: 'long',
                year: 'numeric'
            });
            
            const today = new Date();
            if (expiryDate < today) {
                expiryText += ' <span class="text-red-600">(Imepita)</span>';
            } else if ((expiryDate - today) / (1000 * 60 * 60 * 24) < 30) {
                expiryText += ' <span class="text-amber-600">(Inakaribia kufika)</span>';
            }
        }
        
        title.textContent = product.jina;
        
        content.innerHTML = `
            <div class="space-y-4">
                <div class="flex items-center justify-center mb-4">
                    <div class="h-16 w-16 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-800 font-bold text-2xl">
                        ${product.jina.charAt(0)}
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Aina</p>
                        <p class="text-sm font-medium text-gray-900">${product.aina}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Kipimo</p>
                        <p class="text-sm font-medium text-gray-900">${product.kipimo || '--'}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Idadi</p>
                        <p class="text-sm font-medium text-gray-900">${product.idadi}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Hali ya Hisa</p>
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium 
                            ${product.stock_status === 'low-stock' ? 'bg-amber-100 text-amber-800' : 
                              product.stock_status === 'out-of-stock' ? 'bg-red-100 text-red-800' : 
                              'bg-emerald-100 text-emerald-800'}">
                            ${product.stock_status === 'low-stock' ? 'Inakaribia kuisha' : 
                              product.stock_status === 'out-of-stock' ? 'Imeisha' : 
                              'Inapatikana'}
                        </span>
                    </div>
                </div>
                
                <div class="pt-4 border-t border-gray-200">
                    <p class="text-xs text-gray-500 mb-2">Bei</p>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gray-50 p-3 rounded">
                            <p class="text-xs text-gray-500">Nunua</p>
                            <p class="text-lg font-bold text-gray-900">${parseFloat(product.bei_nunua).toLocaleString()} TZS</p>
                        </div>
                        <div class="bg-emerald-50 p-3 rounded">
                            <p class="text-xs text-gray-500">Kuuza</p>
                            <p class="text-lg font-bold text-emerald-700">${parseFloat(product.bei_kuuza).toLocaleString()} TZS</p>
                        </div>
                    </div>
                </div>
                
                <div class="pt-4 border-t border-gray-200">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Tarehe ya Mwisho</p>
                            <p class="text-sm font-medium text-gray-900">${expiryText}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Barcode</p>
                            <p class="text-sm font-medium text-gray-900">${product.barcode || '--'}</p>
                        </div>
                    </div>
                </div>
                
                <div class="pt-4 border-t border-gray-200">
                    <div class="flex gap-2">
                        <button onclick="window.bidhaaManager.editProduct(${JSON.stringify(product).replace(/"/g, '&quot;')})" 
                                class="flex-1 px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 text-sm font-medium">
                            <i class="fas fa-edit mr-1"></i> Rekebisha
                        </button>
                        <button onclick="window.bidhaaManager.deleteProduct('${product.id}', '${product.jina.replace(/'/g, "\\'")}')" 
                                class="flex-1 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm font-medium">
                            <i class="fas fa-trash mr-1"></i> Futa
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        modal.classList.remove('hidden');
    }

    editProduct(product) {
        const editForm = document.getElementById('edit-form');
        if (!editForm) return;

        document.getElementById('edit-jina').value = product.jina;
        document.getElementById('edit-aina').value = product.aina;
        document.getElementById('edit-kipimo').value = product.kipimo || '';
        document.getElementById('edit-idadi').value = product.idadi;
        document.getElementById('edit-bei-nunua').value = product.bei_nunua;
        document.getElementById('edit-bei-kuuza').value = product.bei_kuuza;
        document.getElementById('edit-expiry').value = product.expiry ? product.expiry.split('T')[0] : '';
        document.getElementById('edit-barcode').value = product.barcode || '';
        editForm.action = `/bidhaa/${product.id}`;
        
        const editModal = document.getElementById('edit-modal');
        if (editModal) editModal.classList.remove('hidden');
    }

    deleteProduct(productId, productName) {
        const deleteForm = document.getElementById('delete-form');
        const deleteModal = document.getElementById('delete-modal');
        const deleteProductName = document.getElementById('delete-product-name');
        
        if (!deleteForm || !deleteModal || !deleteProductName) return;
        
        deleteProductName.textContent = productName;
        deleteForm.action = `/bidhaa/${productId}`;
        deleteModal.classList.remove('hidden');
    }

    setupAjaxForms() {
        // Product form
        const productForm = document.getElementById('product-form');
        if (productForm) {
            productForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.submitForm(productForm, 'Bidhaa imehifadhiwa!');
            });
        }

        // Excel form
        const excelForm = document.getElementById('excel-upload-form');
        if (excelForm) {
            excelForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.handleExcelUpload(excelForm);
            });
        }

        // Edit form
        const editForm = document.getElementById('edit-form');
        if (editForm) {
            editForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.submitForm(editForm, 'Bidhaa imerekebishwa!');
                document.getElementById('edit-modal').classList.add('hidden');
            });
        }

        // Delete form
        const deleteForm = document.getElementById('delete-form');
        if (deleteForm) {
            deleteForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.submitForm(deleteForm, 'Bidhaa imefutwa!');
                document.getElementById('delete-modal').classList.add('hidden');
            });
        }
    }

    async submitForm(form, successMessage = 'Operesheni imekamilika!') {
        const formData = new FormData(form);
        const submitButton = form.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        
        try {
            // Disable submit button
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Inatumwa...';
            
            const response = await fetch(form.action, {
                method: form.method,
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();

            if (response.ok) {
                const message = data.message || successMessage;
                this.showNotification(message, 'success');
                
                // Reload page after success
                setTimeout(() => window.location.reload(), 1500);
            } else {
                const error = data.errors ? Object.values(data.errors)[0][0] : data.message;
                this.showNotification(error || 'Hitilafu imetokea', 'error');
            }
        } catch (error) {
            this.showNotification('Hitilafu ya mtandao', 'error');
        } finally {
            // Re-enable submit button
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
        }
    }

    async handleExcelUpload(form) {
        const fileInput = document.getElementById('excel-file-input');
        if (!fileInput || !fileInput.files.length) {
            this.showNotification('Tafadhali chagua faili', 'error');
            return;
        }
        
        const file = fileInput.files[0];
        const allowedExtensions = ['xlsx', 'xls', 'csv', 'txt'];
        const fileExt = file.name.split('.').pop().toLowerCase();
        
        if (!allowedExtensions.includes(fileExt)) {
            this.showNotification('Aina ya faili hairuhusiwi. Tumia .xlsx, .xls, .csv au .txt', 'error');
            return;
        }
        
        if (file.size > 10 * 1024 * 1024) {
            this.showNotification('Faili ni kubwa sana. Ukubwa upeo ni 10MB', 'error');
            return;
        }
        
        // Show progress
        const progressDiv = document.getElementById('upload-progress');
        const resultsDiv = document.getElementById('upload-results');
        
        if (progressDiv) progressDiv.classList.remove('hidden');
        if (resultsDiv) resultsDiv.classList.add('hidden');
        
        this.updateProgress(10, 'Inaanza upakiaji...');
        
        const formData = new FormData(form);
        
        try {
            this.updateProgress(30, 'Inapakia faili...');
            
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            this.updateProgress(70, 'Inachambua faili...');
            
            const data = await response.json();
            
            this.updateProgress(90, 'Inahifadhi data...');
            
            setTimeout(() => {
                this.updateProgress(100, 'Imekamilika!');
                
                if (response.ok) {
                    if (data.success) {
                        this.showUploadResults(data.data || data);
                        
                        if (data.data?.errorCount > 0) {
                            this.showNotification(
                                `Bidhaa ${data.data.successCount} zimeongezwa lakini kuna hitilafu ${data.data.errorCount}`,
                                'warning'
                            );
                        } else {
                            this.showNotification(
                                `Bidhaa ${data.data.successCount} zimeongezwa kikamilifu!`,
                                'success'
                            );
                        }
                    } else {
                        this.showNotification(data.message || 'Hitilafu katika upakiaji', 'error');
                    }
                } else {
                    let errorMessage = 'Hitilafu katika upakiaji';
                    if (data.message) {
                        errorMessage = data.message;
                    } else if (data.errors) {
                        const firstError = Object.values(data.errors)[0];
                        errorMessage = Array.isArray(firstError) ? firstError[0] : firstError;
                    }
                    
                    this.showNotification(errorMessage, 'error');
                    
                    if (data.data?.errors) {
                        this.showUploadResults(data.data);
                    }
                }
                
                // Reset form
                form.reset();
                
                // Auto-hide results after 15 seconds
                setTimeout(() => {
                    const resultsDiv = document.getElementById('upload-results');
                    if (resultsDiv) resultsDiv.classList.add('hidden');
                }, 15000);
            }, 500);
            
        } catch (error) {
            this.updateProgress(0, 'Hitilafu ya mtandao');
            setTimeout(() => {
                this.showNotification('Hitilafu ya mtandao. Hakikisha umeko kwenye mtandao', 'error');
                const progressDiv = document.getElementById('upload-progress');
                if (progressDiv) progressDiv.classList.add('hidden');
            }, 1000);
        }
    }

    updateProgress(percent, status) {
        const progressBar = document.getElementById('progress-bar');
        const progressPercentage = document.getElementById('progress-percentage');
        const uploadStatus = document.getElementById('upload-status');
        
        if (progressBar) progressBar.style.width = `${percent}%`;
        if (progressPercentage) progressPercentage.textContent = `${percent}%`;
        if (uploadStatus) uploadStatus.textContent = status;
        
        // Hide progress after completion
        if (percent === 100) {
            setTimeout(() => {
                const progressDiv = document.getElementById('upload-progress');
                if (progressDiv) progressDiv.classList.add('hidden');
                if (progressBar) progressBar.style.width = '0%';
                if (progressPercentage) progressPercentage.textContent = '0%';
            }, 2000);
        }
    }

    showUploadResults(data) {
        const resultsDiv = document.getElementById('upload-results');
        const successCount = document.getElementById('success-count');
        const errorCount = document.getElementById('error-count');
        const skippedCount = document.getElementById('skipped-count');
        const totalCount = document.getElementById('total-count');
        const errorList = document.getElementById('error-list');
        const errorItems = document.getElementById('error-items');
        
        if (!resultsDiv || !successCount || !errorCount || !skippedCount || !totalCount) return;
        
        successCount.textContent = data.successCount || 0;
        errorCount.textContent = data.errorCount || 0;
        skippedCount.textContent = data.skippedRows || 0;
        totalCount.textContent = data.totalRows || 0;
        
        // Show/hide error list
        if (data.errors && data.errors.length > 0) {
            if (errorList) errorList.classList.remove('hidden');
            if (errorItems) {
                errorItems.innerHTML = '';
                
                data.errors.forEach((error, index) => {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'flex items-start border-b border-red-100 pb-1 mb-1';
                    
                    let errorText = error;
                    let rowInfo = '';
                    
                    // Extract row number if present
                    const rowMatch = error.match(/mstari\s*(\d+)/i);
                    if (rowMatch && rowMatch[1]) {
                        rowInfo = ` (Mstari ${rowMatch[1]})`;
                    }
                    
                    errorDiv.innerHTML = `
                        <i class="fas fa-times text-red-500 mr-1 mt-0.5 text-xs"></i>
                        <div class="flex-1">
                            <span class="font-medium">${errorText}</span>
                            ${rowInfo ? `<span class="text-red-400 text-xs ml-1">${rowInfo}</span>` : ''}
                        </div>
                    `;
                    
                    errorItems.appendChild(errorDiv);
                    
                    // Limit to 20 errors
                    if (index >= 19 && data.errors.length > 20) {
                        const remaining = data.errors.length - 20;
                        const moreDiv = document.createElement('div');
                        moreDiv.className = 'text-red-400 text-xs text-center pt-1';
                        moreDiv.textContent = `+ ${remaining} zaidi...`;
                        errorItems.appendChild(moreDiv);
                        return;
                    }
                });
            }
        } else {
            if (errorList) errorList.classList.add('hidden');
        }
        
        resultsDiv.classList.remove('hidden');
    }

    showNotification(message, type = 'info') {
        const container = document.getElementById('notification-container');
        if (!container) return;
        
        const colors = {
            success: 'bg-emerald-50 border-emerald-200 text-emerald-800',
            error: 'bg-red-50 border-red-200 text-red-800',
            warning: 'bg-amber-50 border-amber-200 text-amber-800',
            info: 'bg-blue-50 border-blue-200 text-blue-800'
        };

        const notification = document.createElement('div');
        notification.className = `rounded border px-4 py-3 text-sm font-medium mb-2 ${colors[type]} shadow-sm animate-fade-in`;
        notification.textContent = message;

        container.appendChild(notification);

        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateY(-10px) translateX(-50%)';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
}

// Print function
function printProducts() {
    const printWindow = window.open('', '_blank');
    
    // Get all visible products (either from search results or table)
    let products = [];
    const manager = window.bidhaaManager;
    
    if (manager.isSearchActive && manager.allSearchResults.length > 0) {
        // Use search results
        products = manager.allSearchResults;
    } else {
        // Use current page products
        const rows = document.querySelectorAll('.product-row:not(.hidden)');
        rows.forEach(row => {
            products.push(JSON.parse(row.dataset.product));
        });
    }
    
    let tableRows = '';
    products.forEach(product => {
        tableRows += `
            <tr>
                <td style="border: 1px solid #ddd; padding: 8px;">${product.jina}</td>
                <td style="border: 1px solid #ddd; padding: 8px;">${product.aina}</td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">${product.idadi}</td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">${parseFloat(product.bei_nunua).toLocaleString()}</td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">${parseFloat(product.bei_kuuza).toLocaleString()}</td>
                <td style="border: 1px solid #ddd; padding: 8px;">${product.expiry ? new Date(product.expiry).toLocaleDateString() : '--'}</td>
            </tr>`;
    });
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Orodha ya Bidhaa - ${new Date().toLocaleDateString()}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; }
                th { background-color: #f3f4f6; font-weight: bold; }
                .header { text-align: center; margin-bottom: 30px; }
                .header h2 { margin: 0; color: #047857; }
                .header p { margin: 5px 0 0 0; color: #6b7280; }
                @media print {
                    body { margin: 0; }
                    .no-print { display: none; }
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>Orodha ya Bidhaa</h2>
                <p>${new Date().toLocaleDateString()}</p>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Bidhaa</th>
                        <th>Aina</th>
                        <th>Idadi</th>
                        <th>Bei Nunua</th>
                        <th>Bei Kuuza</th>
                        <th>Expiry</th>
                    </tr>
                </thead>
                <tbody>
                    ${tableRows}
                </tbody>
            </table>
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

// PDF Export
function exportPDF() {
    const search = new URLSearchParams(window.location.search);
    search.set('export', 'pdf');
    window.open(`${window.location.pathname}?${search.toString()}`, '_blank');
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    window.bidhaaManager = new SmartBidhaaManager();
    
    // Save tab state
    window.addEventListener('beforeunload', () => {
        if (window.bidhaaManager) {
            window.bidhaaManager.saveTab(window.bidhaaManager.currentTab);
        }
    });
    
    // Fix for sidebar on page load
    setTimeout(() => {
        if (window.bidhaaManager) {
            window.bidhaaManager.fixSidebarNavigation();
        }
    }, 100);
});
</script>
@endpush