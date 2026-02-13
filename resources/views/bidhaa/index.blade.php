@extends('layouts.app')

@section('title', 'Bidhaa')

@section('page-title', 'Bidhaa')
@section('page-subtitle', now()->format('d/m/Y'))

@section('content')
<div class="space-y-4" id="app-container" data-current-page="{{ request()->get('page', 1) }}" data-is-boss="{{ $isBoss ? 'true' : 'false' }}">
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

    <!-- Zilizoisha Button -->
    <div class="bg-white p-3 rounded-lg border border-gray-200 shadow-sm">
        <div class="flex flex-wrap gap-2 items-center justify-between">
            <div class="flex gap-2">
                <a href="{{ route('bidhaa.index') }}?filter=out_of_stock" 
                   class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded hover:bg-gray-700 text-sm font-medium">
                    <i class="fas fa-ban mr-2"></i> Zilizoisha
                    <span class="ml-2 bg-white text-gray-800 px-2 py-0.5 rounded-full text-xs font-bold">{{ $outOfStockProducts }}</span>
                </a>
                
                @if(request('filter') == 'out_of_stock')
                <a href="{{ route('bidhaa.index') }}" 
                   class="inline-flex items-center px-3 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 text-sm">
                    <i class="fas fa-times mr-1"></i> Ondoa Filter
                </a>
                @endif
            </div>
            
            <!-- Download Buttons -->
            <div class="flex gap-2">
                <button onclick="exportPDF()" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm font-medium">
                    <i class="fas fa-file-pdf mr-1"></i> PDF (Zote)
                </button>
                <button onclick="exportExcel()" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm font-medium">
                    <i class="fas fa-file-excel mr-1"></i> Excel (Zote)
                </button>
            </div>
        </div>
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
                <i class="fas fa-file-excel mr-2"></i> Excel/CSV
            </button>
        </div>
    </div>

    <!-- TAB 1: Orodha -->
    <div id="taarifa-tab-content" class="tab-content space-y-3">
        <!-- Search Bar -->
        <div class="bg-white p-3 rounded-lg border border-gray-200 shadow-sm">
            <div class="flex flex-col sm:flex-row gap-2 sm:items-center">
                <div class="flex-1 relative">
                    <input 
                        type="text" 
                        id="search-input"
                        placeholder="Tafuta bidhaa, aina, barcode, kipimo... (tafuta kwenye bidhaa zote)" 
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
                    <button onclick="printCurrentView()" class="px-3 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 text-sm font-medium">
                        <i class="fas fa-print mr-1"></i> Print
                    </button>
                </div>
            </div>
        </div>

        <!-- Search Status -->
        <div id="search-status" class="text-center text-sm text-gray-600 hidden">
            <p id="search-result-count"></p>
            <button onclick="clearSearch()" class="mt-1 text-xs text-emerald-600 hover:text-emerald-800">
                <i class="fas fa-times mr-1"></i> Ondoa utafutaji
            </button>
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
                                        <div class="h-8 w-8 bg-emerald-100 rounded flex items-center justify-center text-emerald-800 font-bold text-sm mr-2 flex-shrink-0">
                                            {{ substr($item->jina, 0, 1) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-medium text-gray-900 text-sm truncate" title="{{ $item->jina }}">{{ $item->jina }}</div>
                                            <div class="text-xs text-gray-500 sm:hidden truncate">{{ $item->aina }}</div>
                                            @if($item->barcode)
                                            <div class="text-xs text-emerald-600 font-mono" title="Barcode">#{{ $item->barcode }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-2 hidden sm:table-cell">
                                    <span class="text-sm text-gray-700">{{ $item->aina }}</span>
                                    @if($item->kipimo)
                                    <span class="text-xs text-gray-500 block">{{ $item->kipimo }}</span>
                                    @endif
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
                                    <div class="text-sm font-bold text-emerald-700">{{ number_format($item->bei_kuuza, 0) }} TZS</div>
                                    <div class="text-xs text-gray-500">Nunua: {{ number_format($item->bei_nunua, 0) }} TZS</div>
                                </td>
                                <td class="px-4 py-2 hidden lg:table-cell">
                                    @if($item->expiry)
                                        @php
                                            $expiryDate = \Carbon\Carbon::parse($item->expiry);
                                            $today = \Carbon\Carbon::today();
                                            $diffDays = $today->diffInDays($expiryDate, false);
                                        @endphp
                                        <div class="text-xs 
                                            @if($expiryDate < $today) text-red-600 font-medium
                                            @elseif($diffDays <= 30) text-amber-600 font-medium
                                            @else text-gray-600 @endif"
                                            title="Tarehe ya kuisha: {{ $expiryDate->format('d/m/Y') }}">
                                            {{ $expiryDate->format('d/m/Y') }}
                                            @if($expiryDate >= $today && $diffDays <= 30)
                                                (Siku {{ $diffDays }})
                                            @elseif($expiryDate < $today)
                                                (Imepita)
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400">--</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-center print:hidden">
                                    <div class="flex justify-center space-x-2">
                                        @if($isBoss)
                                            <button class="edit-product-btn text-emerald-600 hover:text-emerald-800"
                                                    data-id="{{ $item->id }}" title="Badili">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="delete-product-btn text-red-600 hover:text-red-800"
                                                    data-id="{{ $item->id }}" data-name="{{ $item->jina }}" title="Futa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @else
                                            <span class="text-gray-400 cursor-not-allowed" title="Huwezi kurekebisha au kufuta">
                                                <i class="fas fa-edit mr-2"></i>
                                                <i class="fas fa-trash"></i>
                                            </span>
                                        @endif
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
            <div id="pagination-container" class="px-4 py-3 border-t border-gray-200">
                {{ $bidhaa->links() }}
            </div>
            @endif
        </div>
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
                        <h3 class="text-sm font-medium text-gray-900">Pakia Excel</h3>
                        <p class="text-xs text-gray-500">Pakia faili la Excel (XLSX, XLS)</p>
                    </div>
                </div>
                
                <form method="POST" action="{{ route('bidhaa.uploadExcel') }}" enctype="multipart/form-data" id="excel-upload-form" class="space-y-3">
                    @csrf
                    <div>
                        <input type="file" name="excel_file" accept=".xlsx,.xls" 
                               class="block w-full text-sm text-gray-700 border border-gray-300 rounded p-2 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                               required id="excel-file-input">
                        <p class="text-xs text-gray-500 mt-1">.xlsx, .xls (Max: 10MB)</p>
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

        <!-- Download Sample Card -->
        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
            <div class="mb-4">
                <div class="flex items-center mb-3">
                    <div class="h-10 w-10 bg-amber-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-download text-amber-600"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-900">Sampuli ya Faili</h3>
                        <p class="text-xs text-gray-500">Pakua mfano wa faili la Excel</p>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <a href="{{ route('bidhaa.downloadSample') }}" 
                       class="block w-full bg-amber-600 text-white px-4 py-2 rounded hover:bg-amber-700 text-sm font-medium text-center">
                        <i class="fas fa-file-excel mr-1"></i> Pakua Sampuli (XLSX)
                    </a>
                    
                    <!-- Format Info -->
                    <div class="border border-emerald-200 rounded p-3 bg-emerald-50">
                        <h4 class="text-xs font-medium text-emerald-800 mb-2 flex items-center">
                            <i class="fas fa-info-circle mr-1"></i> 
                            JINSI INAVYOFANYA KAZI:
                        </h4>
                        <div class="space-y-2 text-xs">
                            <p class="flex items-start">
                                <span class="text-emerald-600 font-bold mr-2">✓</span>
                                <span><span class="font-bold">Kama bidhaa ipo</span> (Jina, Aina na Kipimo vinalingana) - ita<b>BORESHA</b> (idadi itaongezwa)</span>
                            </p>
                            <p class="flex items-start">
                                <span class="text-emerald-600 font-bold mr-2">✓</span>
                                <span><span class="font-bold">Kama bidhaa haipo</span> - ita<b>ONGEEWA</b> mpya</span>
                            </p>
                        </div>
                    </div>
                    
                    <!-- Required Fields Info -->
                    <div class="border border-blue-200 rounded p-3 bg-blue-50">
                        <h4 class="text-xs font-medium text-blue-800 mb-2 flex items-center">
                            <i class="fas fa-asterisk text-red-500 mr-1"></i> 
                            SAFU ZINAZOHITAJIKA:
                        </h4>
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs border border-blue-200">
                                <thead>
                                    <tr class="bg-blue-100">
                                        <th class="px-2 py-1 border border-blue-200 text-left">Jina la Bidhaa *</th>
                                        <th class="px-2 py-1 border border-blue-200 text-left">Aina *</th>
                                        <th class="px-2 py-1 border border-blue-200 text-left">Kipimo</th>
                                        <th class="px-2 py-1 border border-blue-200 text-left">Idadi *</th>
                                        <th class="px-2 py-1 border border-blue-200 text-left">Bei Nunua *</th>
                                        <th class="px-2 py-1 border border-blue-200 text-left">Bei Kuuza *</th>
                                        <th class="px-2 py-1 border border-blue-200 text-left">Expiry</th>
                                        <th class="px-2 py-1 border border-blue-200 text-left">Barcode</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="px-2 py-1 border border-blue-200">Soda</td>
                                        <td class="px-2 py-1 border border-blue-200">Vinywaji</td>
                                        <td class="px-2 py-1 border border-blue-200">500ml</td>
                                        <td class="px-2 py-1 border border-blue-200">100</td>
                                        <td class="px-2 py-1 border border-blue-200">600</td>
                                        <td class="px-2 py-1 border border-blue-200">1000</td>
                                        <td class="px-2 py-1 border border-blue-200">2025-12-31</td>
                                        <td class="px-2 py-1 border border-blue-200">123456789</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <p class="text-xs text-gray-600 mt-2">
                            <i class="fas fa-info-circle mr-1 text-blue-600"></i>
                            Expiry format: <span class="font-mono">YYYY-MM-DD</span> (mfano: 2025-12-31)
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Upload Results -->
    <div id="upload-results" class="hidden mt-4">
        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-medium text-gray-900 flex items-center">
                    <i class="fas fa-check-circle text-emerald-600 mr-2"></i>
                    Matokeo ya Upakiaji
                </h3>
                <button onclick="document.getElementById('upload-results').classList.add('hidden')" 
                        class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-3">
                <div class="bg-emerald-50 p-2 rounded text-center">
                    <p class="text-xs text-gray-600">Zilizofanikiwa</p>
                    <p class="text-lg font-bold text-emerald-700" id="success-count">0</p>
                </div>
                <div class="bg-green-50 p-2 rounded text-center">
                    <p class="text-xs text-gray-600">Zilizoboreshwa</p>
                    <p class="text-lg font-bold text-green-700" id="updated-count">0</p>
                </div>
                <div class="bg-blue-50 p-2 rounded text-center">
                    <p class="text-xs text-gray-600">Zilizoongezwa</p>
                    <p class="text-lg font-bold text-blue-700" id="created-count">0</p>
                </div>
                <div class="bg-red-50 p-2 rounded text-center">
                    <p class="text-xs text-gray-600">Hitilafu</p>
                    <p class="text-lg font-bold text-red-700" id="error-count">0</p>
                </div>
                <div class="bg-gray-50 p-2 rounded text-center">
                    <p class="text-xs text-gray-600">Jumla</p>
                    <p class="text-lg font-bold text-gray-700" id="total-count">0</p>
                </div>
            </div>
            <div id="error-list" class="hidden">
                <p class="text-xs font-medium text-red-700 mb-1">Hitilafu:</p>
                <div id="error-items" class="text-xs text-red-600 max-h-40 overflow-y-auto bg-red-50 p-2 rounded"></div>
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
@endsection

@push('styles')
<style>
/* Force search input to be usable */
#search-input {
    pointer-events: auto !important;
    opacity: 1 !important;
    background-color: white !important;
    color: black !important;
    border: 1px solid #d1d5db !important;
    z-index: 10 !important;
}

#search-input:focus {
    border-color: #10b981 !important;
    box-shadow: 0 0 0 1px #10b981 !important;
    outline: none !important;
}

/* Modal styles */
.modal {
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Animation */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
    animation: fadeIn 0.3s ease-out;
}
</style>
@endpush

@push('scripts')
<script>
// Global variables
let searchTimeout = null;
let allSearchResults = [];
let isSearchActive = false;
let currentSearchTerm = '';
let isBoss = document.getElementById('app-container')?.dataset.isBoss === 'true';
let originalRows = [];

// ========== SEARCH FUNCTIONALITY ==========
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-input');
    const clearSearchBtn = document.getElementById('clear-search');
    
    if (!searchInput) return;
    
    // Store original rows
    storeOriginalRows();
    
    // Force input to be editable
    searchInput.removeAttribute('readonly');
    searchInput.removeAttribute('disabled');
    searchInput.style.pointerEvents = 'auto';
    searchInput.style.opacity = '1';
    searchInput.style.backgroundColor = 'white';
    
    // Search input handler
    searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.trim();
        currentSearchTerm = searchTerm;
        
        // Show/hide clear button
        if (clearSearchBtn) {
            clearSearchBtn.classList.toggle('hidden', !searchTerm);
        }
        
        // Clear previous timeout
        clearTimeout(searchTimeout);
        
        if (searchTerm.length >= 2) {
            // Show loading
            const tbody = document.getElementById('products-tbody');
            if (tbody) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                            <i class="fas fa-spinner fa-spin text-3xl mb-2 text-emerald-500"></i>
                            <p>Inatafuta kwenye bidhaa zote...</p>
                        </td>
                    </tr>
                `;
            }
            
            // Hide pagination
            const pagination = document.getElementById('pagination-container');
            if (pagination) pagination.classList.add('hidden');
            
            searchTimeout = setTimeout(() => {
                searchAllProducts(searchTerm);
            }, 500);
            isSearchActive = true;
        } else if (searchTerm.length === 0) {
            resetToOriginalProducts();
            isSearchActive = false;
            
            document.getElementById('search-status')?.classList.add('hidden');
            
            const pagination = document.getElementById('pagination-container');
            if (pagination) pagination.classList.remove('hidden');
        }
    });
    
    // Clear search button
    if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', function() {
            searchInput.value = '';
            currentSearchTerm = '';
            clearSearchBtn.classList.add('hidden');
            resetToOriginalProducts();
            isSearchActive = false;
            allSearchResults = [];
            
            document.getElementById('search-status')?.classList.add('hidden');
            
            const pagination = document.getElementById('pagination-container');
            if (pagination) pagination.classList.remove('hidden');
        });
    }
});

// Store original rows
function storeOriginalRows() {
    originalRows = [];
    const rows = document.querySelectorAll('.product-row');
    rows.forEach(row => {
        try {
            const productData = JSON.parse(row.dataset.product);
            originalRows.push(productData);
        } catch (e) {
            console.error('Error parsing row data', e);
        }
    });
}

// Reset to original products
function resetToOriginalProducts() {
    const tbody = document.getElementById('products-tbody');
    if (!tbody) return;
    
    tbody.innerHTML = '';
    
    if (originalRows.length > 0) {
        originalRows.forEach(product => {
            const row = createProductRow(product);
            tbody.appendChild(row);
        });
    } else {
        window.location.reload();
    }
    
    attachProductEvents();
}

// Search all products
function searchAllProducts(searchTerm) {
    const searchStatus = document.getElementById('search-status');
    const searchResultCount = document.getElementById('search-result-count');
    const tbody = document.getElementById('products-tbody');
    const pagination = document.getElementById('pagination-container');
    
    if (!tbody) return;
    
    fetch(`/bidhaa/search?search=${encodeURIComponent(searchTerm)}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.data && data.data.length > 0) {
            allSearchResults = data.data;
            
            tbody.innerHTML = '';
            data.data.forEach(product => {
                const row = createProductRow(product);
                tbody.appendChild(row);
            });
            
            if (searchStatus && searchResultCount) {
                searchStatus.classList.remove('hidden');
                searchResultCount.innerHTML = `
                    <span class="font-bold">${data.data.length}</span> bidhaa zinaonyeshwa kutoka kwenye bidhaa zote
                    <button onclick="clearSearch()" class="ml-2 text-xs text-emerald-600 hover:text-emerald-800">
                        <i class="fas fa-times mr-1"></i> Ondoa
                    </button>
                `;
            }
            
            if (pagination) pagination.classList.add('hidden');
            attachProductEvents();
        } else {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                        <i class="fas fa-search text-3xl mb-2 text-gray-300"></i>
                        <p>Hakuna bidhaa zinazolingana na "${searchTerm}"</p>
                    </td>
                </tr>
            `;
            
            if (searchStatus && searchResultCount) {
                searchStatus.classList.remove('hidden');
                searchResultCount.innerHTML = `
                    Hakuna bidhaa zinazolingana na "${searchTerm}"
                    <button onclick="clearSearch()" class="ml-2 text-xs text-emerald-600 hover:text-emerald-800">
                        <i class="fas fa-times mr-1"></i> Ondoa
                    </button>
                `;
            }
            
            if (pagination) pagination.classList.add('hidden');
        }
    })
    .catch(error => {
        console.error('Search error:', error);
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="px-4 py-8 text-center text-red-500">
                    <i class="fas fa-exclamation-triangle text-3xl mb-2"></i>
                    <p>Hitilafu ya utafutaji. Jaribu tena.</p>
                </td>
            </tr>
        `;
    });
}

// Create product row
function createProductRow(product) {
    const row = document.createElement('tr');
    row.className = 'product-row hover:bg-gray-50';
    row.dataset.product = JSON.stringify(product);
    
    let stockClass = 'bg-emerald-100 text-emerald-800';
    if (product.idadi == 0) {
        stockClass = 'bg-gray-100 text-gray-800';
    } else if (product.idadi < 10) {
        stockClass = 'bg-amber-100 text-amber-800';
    }
    
    let expiryHtml = '<span class="text-xs text-gray-400">--</span>';
    if (product.expiry) {
        try {
            const expiryDate = new Date(product.expiry);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            const diffTime = expiryDate - today;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            let expiryClass = 'text-gray-600';
            let expiryText = expiryDate.toLocaleDateString('en-GB');
            
            if (diffDays < 0) {
                expiryClass = 'text-red-600 font-medium';
                expiryText += ' (Imepita)';
            } else if (diffDays <= 30) {
                expiryClass = 'text-amber-600 font-medium';
                expiryText += ` (Siku ${diffDays})`;
            }
            
            expiryHtml = `<div class="text-xs ${expiryClass}">${expiryText}</div>`;
        } catch (e) {
            expiryHtml = `<span class="text-xs text-gray-400">${product.expiry}</span>`;
        }
    }
    
    let actionButtons = '';
    if (isBoss) {
        actionButtons = `
            <div class="flex justify-center space-x-2">
                <button class="edit-product-btn text-emerald-600 hover:text-emerald-800" data-id="${product.id}" title="Badili">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="delete-product-btn text-red-600 hover:text-red-800" data-id="${product.id}" data-name="${(product.jina || '').replace(/'/g, "\\'")}" title="Futa">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
    } else {
        actionButtons = `
            <span class="text-gray-400 cursor-not-allowed" title="Huwezi kurekebisha au kufuta">
                <i class="fas fa-edit mr-2"></i>
                <i class="fas fa-trash"></i>
            </span>
        `;
    }
    
    row.innerHTML = `
        <td class="px-4 py-2">
            <div class="flex items-center">
                <div class="h-8 w-8 bg-emerald-100 rounded flex items-center justify-center text-emerald-800 font-bold text-sm mr-2 flex-shrink-0">
                    ${(product.jina || '?').charAt(0)}
                </div>
                <div class="min-w-0">
                    <div class="font-medium text-gray-900 text-sm truncate">${product.jina || ''}</div>
                    <div class="text-xs text-gray-500 sm:hidden truncate">${product.aina || ''}</div>
                    ${product.barcode ? `<div class="text-xs text-emerald-600 font-mono">#${product.barcode}</div>` : ''}
                </div>
            </div>
        </td>
        <td class="px-4 py-2 hidden sm:table-cell">
            <span class="text-sm text-gray-700">${product.aina || ''}</span>
            ${product.kipimo ? `<span class="text-xs text-gray-500 block">${product.kipimo}</span>` : ''}
        </td>
        <td class="px-4 py-2 text-center">
            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium ${stockClass}">
                ${product.idadi || 0}
            </span>
        </td>
        <td class="px-4 py-2 text-right">
            <div class="text-sm font-bold text-emerald-700">${parseFloat(product.bei_kuuza || 0).toLocaleString()} TZS</div>
            <div class="text-xs text-gray-500">Nunua: ${parseFloat(product.bei_nunua || 0).toLocaleString()} TZS</div>
        </td>
        <td class="px-4 py-2 hidden lg:table-cell">
            ${expiryHtml}
        </td>
        <td class="px-4 py-2 text-center print:hidden">
            ${actionButtons}
        </td>
    `;
    
    return row;
}

// Clear search
function clearSearch() {
    const searchInput = document.getElementById('search-input');
    const clearSearchBtn = document.getElementById('clear-search');
    
    if (searchInput) {
        searchInput.value = '';
        currentSearchTerm = '';
    }
    
    if (clearSearchBtn) {
        clearSearchBtn.classList.add('hidden');
    }
    
    resetToOriginalProducts();
    isSearchActive = false;
    allSearchResults = [];
    
    document.getElementById('search-status')?.classList.add('hidden');
    
    const pagination = document.getElementById('pagination-container');
    if (pagination) pagination.classList.remove('hidden');
}

// Attach product events
function attachProductEvents() {
    document.querySelectorAll('.edit-product-btn').forEach(button => {
        button.removeEventListener('click', handleEditClick);
        button.addEventListener('click', handleEditClick);
    });
    
    document.querySelectorAll('.delete-product-btn').forEach(button => {
        button.removeEventListener('click', handleDeleteClick);
        button.addEventListener('click', handleDeleteClick);
    });
}

// Handle edit click
function handleEditClick(e) {
    e.preventDefault();
    const productId = this.dataset.id;
    
    if (!isBoss) {
        showNotification('Hurumia, wewe huna ruhusa ya kurekebisha bidhaa', 'error');
        return;
    }
    
    const row = this.closest('.product-row');
    if (row && row.dataset.product) {
        try {
            const product = JSON.parse(row.dataset.product);
            editProduct(product);
        } catch (error) {
            loadAndEditProduct(productId);
        }
    } else {
        loadAndEditProduct(productId);
    }
}

// Handle delete click
function handleDeleteClick(e) {
    e.preventDefault();
    const productId = this.dataset.id;
    const productName = this.dataset.name;
    
    if (!isBoss) {
        showNotification('Hurumia, wewe huna ruhusa ya kufuta bidhaa', 'error');
        return;
    }
    
    deleteProduct(productId, productName);
}

// Edit product
function editProduct(product) {
    if (!isBoss) return;
    
    document.getElementById('edit-jina').value = product.jina || '';
    document.getElementById('edit-aina').value = product.aina || '';
    document.getElementById('edit-kipimo').value = product.kipimo || '';
    document.getElementById('edit-idadi').value = product.idadi || 0;
    document.getElementById('edit-bei-nunua').value = product.bei_nunua || 0;
    document.getElementById('edit-bei-kuuza').value = product.bei_kuuza || 0;
    document.getElementById('edit-expiry').value = product.expiry || '';
    document.getElementById('edit-barcode').value = product.barcode || '';
    document.getElementById('edit-form').action = `/bidhaa/${product.id}`;
    document.getElementById('edit-modal').classList.remove('hidden');
}

// Load and edit product
function loadAndEditProduct(productId) {
    fetch(`/bidhaa/${productId}/edit-product`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            editProduct(data.data);
        } else {
            showNotification('Imeshindwa kupakua taarifa', 'error');
        }
    })
    .catch(error => {
        showNotification('Hitilafu ya mtandao', 'error');
    });
}

// Delete product
function deleteProduct(productId, productName) {
    if (!isBoss) return;
    
    document.getElementById('delete-product-name').textContent = productName;
    document.getElementById('delete-form').action = `/bidhaa/${productId}`;
    document.getElementById('delete-modal').classList.remove('hidden');
}

// Modal handling
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('close-edit-modal')?.addEventListener('click', function() {
        document.getElementById('edit-modal').classList.add('hidden');
    });
    
    document.getElementById('cancel-delete')?.addEventListener('click', function() {
        document.getElementById('delete-modal').classList.add('hidden');
    });
    
    const modals = ['edit-modal', 'delete-modal'];
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal || e.target.classList.contains('modal-overlay')) {
                    modal.classList.add('hidden');
                }
            });
        }
    });
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.getElementById('edit-modal')?.classList.add('hidden');
            document.getElementById('delete-modal')?.classList.add('hidden');
        }
    });
});

// Form handling
document.addEventListener('DOMContentLoaded', function() {
    // Product form
    const productForm = document.getElementById('product-form');
    if (productForm) {
        productForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            await submitForm(this, 'Bidhaa imehifadhiwa!');
        });
    }
    
    // Edit form
    const editForm = document.getElementById('edit-form');
    if (editForm) {
        editForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const buyPrice = parseFloat(document.getElementById('edit-bei-nunua').value);
            const sellPrice = parseFloat(document.getElementById('edit-bei-kuuza').value);
            
            if (buyPrice > sellPrice) {
                showNotification('Bei ya kununua haiwezi kuwa kubwa kuliko bei ya kuuza!', 'error');
                return;
            }
            
            await submitForm(this, 'Bidhaa imerekebishwa!');
            document.getElementById('edit-modal').classList.add('hidden');
        });
    }
    
    // Delete form
    const deleteForm = document.getElementById('delete-form');
    if (deleteForm) {
        deleteForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            await submitForm(this, 'Bidhaa imefutwa!');
            document.getElementById('delete-modal').classList.add('hidden');
        });
    }
    
    // Excel form
    const excelForm = document.getElementById('excel-upload-form');
    if (excelForm) {
        excelForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            await handleExcelUpload(this);
        });
    }
    
    // Price validation
    const buyPrice = document.getElementById('buy-price');
    const sellPrice = document.getElementById('sell-price');
    const priceError = document.getElementById('price-error');
    
    if (buyPrice && sellPrice && priceError) {
        const validatePrices = function() {
            if (parseFloat(buyPrice.value) > parseFloat(sellPrice.value)) {
                priceError.textContent = 'Bei ya kununua haiwezi kuwa kubwa kuliko bei ya kuuza!';
                priceError.classList.remove('hidden');
                return false;
            }
            priceError.classList.add('hidden');
            return true;
        };
        
        buyPrice.addEventListener('input', validatePrices);
        sellPrice.addEventListener('input', validatePrices);
        
        if (productForm) {
            productForm.addEventListener('submit', function(e) {
                if (!validatePrices()) e.preventDefault();
            });
        }
    }
    
    attachProductEvents();
});

// Submit form
async function submitForm(form, successMessage) {
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn ? submitBtn.innerHTML : 'Submit';
    
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Inatumwa...';
    }
    
    try {
        const response = await fetch(form.action, {
            method: form.method,
            body: new FormData(form),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const data = await response.json();

        if (response.ok) {
            showNotification(data.message || successMessage, 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            const error = data.errors ? Object.values(data.errors)[0][0] : (data.message || 'Hitilafu imetokea');
            showNotification(error, 'error');
        }
    } catch (error) {
        showNotification('Hitilafu ya mtandao', 'error');
    } finally {
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    }
}

// Handle Excel upload
async function handleExcelUpload(form) {
    const fileInput = document.getElementById('excel-file-input');
    if (!fileInput?.files.length) {
        showNotification('Tafadhali chagua faili', 'error');
        return;
    }
    
    document.getElementById('upload-progress')?.classList.remove('hidden');
    updateProgress(10, 'Inaanza upakiaji...');
    
    const formData = new FormData(form);
    
    try {
        updateProgress(30, 'Inapakia faili...');
        
        const response = await fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        updateProgress(70, 'Inachambua faili...');
        
        const data = await response.json();
        
        updateProgress(90, 'Inahifadhi data...');
        
        setTimeout(() => {
            updateProgress(100, 'Imekamilika!');
            
            if (response.ok) {
                showUploadResults(data.data || data);
                showNotification(
                    data.data?.successCount > 0 
                        ? `Bidhaa ${data.data.successCount} zimeongezwa!` 
                        : (data.message || 'Upakiaji umekamilika'), 
                    data.success ? 'success' : 'warning'
                );
            } else {
                showNotification(data.message || 'Hitilafu katika upakiaji', 'error');
            }
            
            form.reset();
            setTimeout(() => document.getElementById('upload-progress')?.classList.add('hidden'), 2000);
        }, 500);
        
    } catch (error) {
        updateProgress(0, 'Hitilafu ya mtandao');
        showNotification('Hitilafu ya mtandao', 'error');
        setTimeout(() => document.getElementById('upload-progress')?.classList.add('hidden'), 1000);
    }
}

// Update progress
function updateProgress(percent, status) {
    const bar = document.getElementById('progress-bar');
    const percentage = document.getElementById('progress-percentage');
    const statusEl = document.getElementById('upload-status');
    
    if (bar) bar.style.width = `${percent}%`;
    if (percentage) percentage.textContent = `${percent}%`;
    if (statusEl) statusEl.textContent = status;
}


// Show upload results
function showUploadResults(data) {
    const resultsDiv = document.getElementById('upload-results');
    if (!resultsDiv) return;
    
    document.getElementById('success-count').textContent = data.successCount || 0;
    document.getElementById('updated-count').textContent = data.updatedCount || 0;
    document.getElementById('created-count').textContent = data.createdCount || 0;
    document.getElementById('error-count').textContent = data.errorCount || 0;
    document.getElementById('total-count').textContent = data.totalRows || 0;
    
    const errorList = document.getElementById('error-list');
    const errorItems = document.getElementById('error-items');
    
    if (data.errors && data.errors.length > 0) {
        if (errorList) errorList.classList.remove('hidden');
        if (errorItems) {
            errorItems.innerHTML = data.errors.slice(0, 20).map(err => 
                `<div class="flex items-start border-b border-red-100 pb-1 mb-1">
                    <i class="fas fa-times text-red-500 mr-1 mt-0.5 text-xs"></i>
                    <span>${err}</span>
                </div>`
            ).join('');
            
            if (data.errors.length > 20) {
                errorItems.innerHTML += `<div class="text-red-400 text-xs text-center pt-1">+ ${data.errors.length - 20} zaidi...</div>`;
            }
        }
    } else {
        if (errorList) errorList.classList.add('hidden');
    }
    
    resultsDiv.classList.remove('hidden');
}

// Show notification
function showNotification(message, type = 'info') {
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
        notification.style.transition = 'opacity 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Print current view
function printCurrentView() {
    let products = [];
    
    if (isSearchActive && allSearchResults.length > 0) {
        products = allSearchResults;
    } else {
        const rows = document.querySelectorAll('.product-row');
        rows.forEach(row => {
            try {
                if (row.dataset.product) {
                    products.push(JSON.parse(row.dataset.product));
                }
            } catch (e) {
                console.error('Error parsing product data', e);
            }
        });
    }
    
    if (products.length === 0) {
        showNotification('Hakuna bidhaa za kuchapisha', 'warning');
        return;
    }
    
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
        <head>
            <title>Orodha ya Bidhaa - ${new Date().toLocaleDateString()}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f3f4f6; font-weight: bold; }
                .header { text-align: center; margin-bottom: 30px; }
                .header h2 { margin: 0; color: #047857; }
                .header p { margin: 5px 0 0 0; color: #6b7280; }
                .text-center { text-align: center; }
                .text-right { text-align: right; }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>Orodha ya Bidhaa</h2>
                <p>Tarehe: ${new Date().toLocaleDateString()} | Jumla: ${products.length}</p>
                ${isSearchActive ? `<p>Matokeo ya utafutaji: "${currentSearchTerm}"</p>` : ''}
            </div>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Bidhaa</th>
                        <th>Aina</th>
                        <th>Kipimo</th>
                        <th class="text-center">Idadi</th>
                        <th class="text-right">Bei Nunua</th>
                        <th class="text-right">Bei Kuuza</th>
                        <th>Barcode</th>
                        <th>Expiry</th>
                    </tr>
                </thead>
                <tbody>
                    ${products.map((p, index) => `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${p.jina || ''}</td>
                            <td>${p.aina || ''}</td>
                            <td>${p.kipimo || '--'}</td>
                            <td class="text-center">${p.idadi || 0}</td>
                            <td class="text-right">${parseFloat(p.bei_nunua || 0).toLocaleString()} TZS</td>
                            <td class="text-right">${parseFloat(p.bei_kuuza || 0).toLocaleString()} TZS</td>
                            <td>${p.barcode || '--'}</td>
                            <td>${p.expiry ? new Date(p.expiry).toLocaleDateString() : '--'}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

// Export functions
function exportPDF() {
    window.location.href = `${window.location.pathname}?export=pdf`;
}

function exportExcel() {
    window.location.href = `${window.location.pathname}?export=excel`;
}

// Tab handling
document.addEventListener('DOMContentLoaded', function() {
    const savedTab = sessionStorage.getItem('bidhaa_tab') || 'taarifa';
    showTab(savedTab);
    
    document.querySelectorAll('.tab-button').forEach(button => {
        button.addEventListener('click', function(e) {
            const tab = e.target.closest('.tab-button').dataset.tab;
            showTab(tab);
            sessionStorage.setItem('bidhaa_tab', tab);
        });
    });
});

function showTab(tabName) {
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
}

// Force search input fix
(function() {
    function fixSearchInput() {
        const searchInput = document.getElementById('search-input');
        if (searchInput) {
            searchInput.removeAttribute('readonly');
            searchInput.removeAttribute('disabled');
            searchInput.style.pointerEvents = 'auto';
            searchInput.style.opacity = '1';
            searchInput.style.backgroundColor = 'white';
            searchInput.style.color = 'black';
            searchInput.readOnly = false;
            searchInput.disabled = false;
        }
    }
    
    fixSearchInput();
    setTimeout(fixSearchInput, 100);
    setTimeout(fixSearchInput, 500);
    setTimeout(fixSearchInput, 1000);
})();
</script>
@endpush