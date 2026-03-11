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
                   class="inline-flex items-center px-2 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 text-sm">
                    <i class="fas fa-times mr-1"></i> Ondoa Filter
                </a>
                @endif
            </div>
            
            <!-- Download Buttons -->
<!-- Download Buttons -->
        <div class="flex gap-2">
            <button onclick="exportPDF()" class="px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm font-medium">
                <i class="fas fa-file-pdf mr-1"></i> PDF
            </button>
            <button onclick="exportExcel()" class="px-3 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm font-medium">
                <i class="fas fa-file-excel mr-1"></i> Excel 
            </button>
        </div>
        </div>
    </div>

    <!-- Tabs - FIXED: Changed data-tab values to match content IDs -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm">
        <div class="flex">
            <button data-tab="orodha" class="tab-button flex-1 py-3 px-4 text-sm font-medium border-r border-gray-200 bg-emerald-50 text-emerald-700">
                <i class="fas fa-table mr-2"></i> Orodha
            </button>
            <button data-tab="ingiza" class="tab-button flex-1 py-3 px-4 text-sm font-medium border-r border-gray-200 text-gray-600 hover:bg-gray-50">
                <i class="fas fa-plus mr-2"></i> Ingiza
            </button>
            <button data-tab="excel" class="tab-button flex-1 py-3 px-4 text-sm font-medium text-gray-600 hover:bg-gray-50">
                <i class="fas fa-file-excel mr-2"></i> Excel/CSV
            </button>
            <button data-tab="ripoti" class="tab-button flex-1 py-3 px-4 text-sm font-medium text-gray-600 hover:bg-gray-50">
                <i class="fas fa-chart-bar mr-2"></i> Taarifa
            </button>
        </div>
    </div>

    <!-- TAB 1: Orodha - FIXED: Changed ID to match data-tab -->
    <div id="orodha-tab-content" class="tab-content space-y-3">
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
                                    @php
                                        $formattedIdadi = $item->idadi;
                                        if (is_numeric($item->idadi)) {
                                            $formattedIdadi = $item->idadi % 1 == 0 ? (string)(int)$item->idadi : number_format($item->idadi, 2);
                                        }
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium 
                                        @if($item->idadi < 10 && $item->idadi > 0) bg-amber-100 text-amber-800
                                        @elseif($item->idadi == 0) bg-gray-100 text-gray-800
                                        @else bg-emerald-100 text-emerald-800 @endif">
                                        {{ $formattedIdadi }}
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
                        <input type="number" name="idadi" min="0" step="0.01"
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                               placeholder="Idadi (mfano: 1.5, 10.75)" required>
                        <p class="text-xs text-gray-500 mt-1">Unaweza kuweka desimali (mfano: 1.5, 2.75)</p>
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
                                    <span><span class="font-bold">Kama bidhaa ipo</span> (Jina, Aina na Kipimo vinalingana) - ita<b>BADILISHA</b> (idadi itachukua nafasi ya ile iliyopo)</span>
                                </p>
                                <p class="flex items-start">
                                    <span class="text-emerald-600 font-bold mr-2">✓</span>
                                    <span><span class="font-bold">Kama bidhaa haipo</span> - ita<b>ONGEEWA</b> mpya</span>
                                </p>
                                <p class="flex items-start">
                                    <span class="text-emerald-600 font-bold mr-2">✓</span>
                                    <span><span class="font-bold">Idadi inakubali desimali</span> - mfano: 1.5, 2.75, 10.00</span>
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
                                        <tr>
                                            <td class="px-2 py-1 border border-blue-200">Unga</td>
                                            <td class="px-2 py-1 border border-blue-200">Chakula</td>
                                            <td class="px-2 py-1 border border-blue-200">2kg</td>
                                            <td class="px-2 py-1 border border-blue-200">50.5</td>
                                            <td class="px-2 py-1 border border-blue-200">2500</td>
                                            <td class="px-2 py-1 border border-blue-200">3500</td>
                                            <td class="px-2 py-1 border border-blue-200">2026-06-30</td>
                                            <td class="px-2 py-1 border border-blue-200"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <p class="text-xs text-gray-600 mt-2">
                                <i class="fas fa-info-circle mr-1 text-blue-600"></i>
                                <span class="font-bold">Idadi inaweza kuwa na desimali:</span> 1.5, 2.75, 100.00<br>
                                <span class="font-bold">Expiry format:</span> <span class="font-mono">YYYY-MM-DD</span> (mfano: 2025-12-31)
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

    <!-- TAB 4: Taarifa za Bidhaa - Smart Stock Management - FIXED: Changed ID to ripoti-tab-content -->
    <div id="ripoti-tab-content" class="tab-content hidden">
        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
            <!-- Date Range Filter -->
            <div class="mb-4 p-3 bg-gray-50 rounded-lg border border-gray-200">
                <div class="flex items-center justify-between mb-2">
                    <label class="text-sm font-medium text-gray-700">
                        <i class="fas fa-calendar-alt mr-2 text-emerald-600"></i>
                        Chuja kwa Tarehe
                    </label>
                    <button id="clear-date-filter" class="text-xs text-red-600 hover:text-red-800">
                        <i class="fas fa-times mr-1"></i> Ondoa
                    </button>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Kuanzia</label>
                        <input type="date" id="start-date" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Mpaka</label>
                        <input type="date" id="end-date" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    </div>
                </div>
                <button id="apply-date-filter" class="mt-2 w-full px-3 py-1 bg-emerald-100 text-emerald-700 rounded text-sm hover:bg-emerald-200">
                    <i class="fas fa-filter mr-1"></i> Tumia Filter
                </button>
            </div>

            <!-- Smart Product Search - Searches ALL products -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-search mr-2 text-emerald-600"></i>
                    Tafuta Bidhaa
                </label>
                <div class="relative">
                    <input 
                        type="text" 
                        id="product-search-input"
                        placeholder="Andika jina, aina au barcode ya bidhaa..." 
                        class="w-full pl-10 pr-4 py-3 border-2 border-emerald-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        autocomplete="off"
                    >
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-emerald-500"></i>
                    <div id="search-spinner" class="absolute right-3 top-1/2 transform -translate-y-1/2 hidden">
                        <i class="fas fa-spinner fa-spin text-emerald-500"></i>
                    </div>
                </div>
                
                <!-- Search Results Dropdown (ALL products, not paginated) -->
                <div id="search-results-dropdown" class="hidden mt-1 border-2 border-emerald-200 rounded-lg bg-white shadow-xl max-h-80 overflow-y-auto absolute z-50 w-full md:w-2/3"></div>
                
                <!-- Selected Product Info -->
                <div id="selected-product-info" class="hidden mt-3 p-3 bg-emerald-50 rounded-lg border border-emerald-200">
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="text-xs text-emerald-700 font-medium">Bidhaa iliyochaguliwa:</span>
                            <span id="selected-product-name" class="text-sm font-bold text-emerald-800 ml-2"></span>
                            <span id="selected-product-details" class="text-xs text-gray-600 ml-2"></span>
                        </div>
                        <button id="clear-selected-product" class="text-xs text-red-600 hover:text-red-800">
                            <i class="fas fa-times"></i> Badilisha
                        </button>
                    </div>
                </div>
            </div>

            <!-- Loading Indicator -->
            <div id="details-loading" class="hidden text-center py-8">
                <i class="fas fa-spinner fa-spin text-3xl text-emerald-600 mb-2"></i>
                <p class="text-gray-600">Inapakia taarifa za bidhaa...</p>
            </div>

            <!-- Product Details Container -->
            <div id="product-details-container" class="hidden space-y-4">
                <!-- Product Header -->
                <div class="border-b border-gray-200 pb-3">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                        <div>
                            <h3 class="text-lg font-bold text-emerald-800" id="detail-jina">-</h3>
                            <p class="text-sm text-gray-600">
                                <span id="detail-aina">-</span> 
                                <span id="detail-kipimo" class="ml-2 px-2 py-0.5 bg-gray-100 rounded text-xs"></span>
                            </p>
                            <p class="text-xs text-emerald-600 font-mono mt-1" id="detail-barcode"></p>
                        </div>
                        <div class="mt-2 sm:mt-0 flex gap-2">
                            <button onclick="printProductDetails()" class="px-3 py-1 bg-emerald-600 text-white rounded hover:bg-emerald-700 text-xs font-medium">
                                <i class="fas fa-print mr-1"></i> Chapisha
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Simplified Stock Summary Cards -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                    <!-- Idadi Iliyopo Sasa -->
                    <div class="bg-emerald-50 p-3 rounded-lg border border-emerald-200">
                        <p class="text-xs text-gray-600 mb-1">Idadi Iliyopo Sasa</p>
                        <p class="text-2xl font-bold text-emerald-700" id="stat-idadi-sasa">0</p>
                        <p class="text-xs text-gray-500 mt-1">Kwenye stock</p>
                    </div>
                    
                    <!-- Jumla Iliyoingizwa -->
                    <div class="bg-blue-50 p-3 rounded-lg border border-blue-200">
                        <p class="text-xs text-gray-600 mb-1">Jumla Iliyoingizwa</p>
                        <p class="text-2xl font-bold text-blue-700" id="stat-jumla-ingizo">0</p>
                        <p class="text-xs text-gray-500 mt-1">Manunuzi yote</p>
                    </div>
                    
                    <!-- Jumla Iliyouzwa (Mauzo + Kopesha) -->
                    <div class="bg-amber-50 p-3 rounded-lg border border-amber-200">
                        <p class="text-xs text-gray-600 mb-1">Jumla Iliyouzwa</p>
                        <p class="text-2xl font-bold text-amber-700" id="stat-jumla-mauzo">0</p>
                        <p class="text-xs text-gray-500 mt-1">Mauzo + Kopesha</p>
                    </div>
                    
                    <!-- Zilizobaki (Hesabu) -->
                    <div class="bg-purple-50 p-3 rounded-lg border border-purple-200">
                        <p class="text-xs text-gray-600 mb-1">Zilizobaki (Hesabu)</p>
                        <p class="text-2xl font-bold text-purple-700" id="stat-zilizobaki">0</p>
                        <p class="text-xs text-gray-500 mt-1">Ingizo - Mauzo</p>
                    </div>
                </div>

                <!-- Stock Status & Expiry -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="border border-gray-200 rounded-lg p-3">
                        <p class="text-xs text-gray-500 mb-1">Hali ya Hisa</p>
                        <div id="stock-status-badge" class="inline-block px-2 py-1 rounded text-xs font-medium">-</div>
                        <div class="mt-2 text-sm">
                            <span class="text-gray-600">Tarehe ya Kuingia:</span>
                            <span class="font-medium ml-2" id="detail-tarehe-kwanza">-</span>
                        </div>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-3">
                        <p class="text-xs text-gray-500 mb-1">Tarehe ya Mwisho (Expiry)</p>
                        <div id="expiry-badge" class="inline-block px-2 py-1 rounded text-xs font-medium">-</div>
                        <div class="mt-2 text-sm">
                            <span class="text-gray-600">Imebaki:</span>
                            <span class="font-medium ml-2" id="expiry-days">-</span>
                        </div>
                    </div>
                </div>

                <!-- History Table -->
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <div class="bg-gray-50 px-3 py-2 border-b border-gray-200 flex justify-between items-center">
                        <p class="text-sm font-medium text-gray-700">
                            <i class="fas fa-history mr-2 text-emerald-600"></i>
                            Historia ya Shughuli
                        </p>
                        <div class="flex items-center gap-2">
                            <span class="text-xs bg-emerald-100 text-emerald-800 px-2 py-1 rounded" id="history-count">Jumla: 0</span>
                            <span class="text-xs text-gray-500" id="date-range-display"></span>
                        </div>
                    </div>
                    <div class="overflow-x-auto max-h-96 overflow-y-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 sticky top-0">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Tarehe</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Aina</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">Iliyoingizwa</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">Iliyouzwa</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">Iliyobaki</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Maelezo</th>
                                </tr>
                            </thead>
                            <tbody id="history-tbody" class="divide-y divide-gray-100">
                                <tr>
                                    <td colspan="6" class="px-3 py-4 text-center text-gray-500">
                                        Hakuna historia ya shughuli
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- No Product Selected -->
            <div id="no-product-selected" class="text-center py-8 text-gray-500">
                <i class="fas fa-chart-bar text-3xl mb-2 text-gray-300"></i>
                <p>Tafadhali tafuta na uchague bidhaa kuona taarifa zake</p>
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
                    <input type="number" name="idadi" id="edit-idadi" min="0" step="0.01"
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

/* Search results dropdown */
#search-results-dropdown {
    max-height: 320px;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: #10b981 #f3f4f6;
}

#search-results-dropdown::-webkit-scrollbar {
    width: 6px;
}

#search-results-dropdown::-webkit-scrollbar-track {
    background: #f3f4f6;
}

#search-results-dropdown::-webkit-scrollbar-thumb {
    background-color: #10b981;
    border-radius: 20px;
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

    // Initialize smart search for Taarifa tab
    initializeSmartSearch();
    
    // Date filter for Taarifa tab
    document.getElementById('apply-date-filter')?.addEventListener('click', function() {
        if (selectedProductId) {
            loadProductDetails(selectedProductId);
        } else {
            showNotification('Tafuta na uchague bidhaa kwanza', 'warning');
        }
    });
    
    document.getElementById('clear-date-filter')?.addEventListener('click', function() {
        document.getElementById('start-date').value = '';
        document.getElementById('end-date').value = '';
        if (selectedProductId) {
            loadProductDetails(selectedProductId);
        }
    });
    
    document.getElementById('clear-selected-product')?.addEventListener('click', function() {
        clearSelectedProduct();
    });

    // Check if product ID in URL
    const urlParams = new URLSearchParams(window.location.search);
    const productId = urlParams.get('product_id');
    
    if (productId) {
        loadProductById(productId);
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

// Search all products - FIXED to search ALL products
function searchAllProducts(searchTerm) {
    const searchStatus = document.getElementById('search-status');
    const searchResultCount = document.getElementById('search-result-count');
    const tbody = document.getElementById('products-tbody');
    const pagination = document.getElementById('pagination-container');
    
    if (!tbody) return;
    
    // Show loading
    tbody.innerHTML = `
        <tr>
            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                <i class="fas fa-spinner fa-spin text-3xl mb-2 text-emerald-500"></i>
                <p>Inatafuta "${searchTerm}" kwenye bidhaa zote...</p>
            </td>
        </tr>
    `;
    
    // Use the search endpoint which returns ALL results (no pagination)
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
                    <div class="bg-emerald-50 p-2 rounded">
                        <span class="font-bold text-emerald-700">${data.data.length}</span> 
                        <span class="text-gray-600">bidhaa zinaonyeshwa kutoka kwenye bidhaa zote</span>
                        <button onclick="clearSearch()" class="ml-2 text-xs bg-emerald-600 text-white px-2 py-1 rounded hover:bg-emerald-700">
                            <i class="fas fa-times mr-1"></i> Ondoa
                        </button>
                    </div>
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
                    <div class="bg-amber-50 p-2 rounded">
                        <span class="text-amber-700">Hakuna bidhaa zinazolingana na "${searchTerm}"</span>
                        <button onclick="clearSearch()" class="ml-2 text-xs bg-amber-600 text-white px-2 py-1 rounded hover:bg-amber-700">
                            <i class="fas fa-times mr-1"></i> Ondoa
                        </button>
                    </div>
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
    
    // Handle decimal idadi for stock status
    const idadi = parseFloat(product.idadi || 0);
    
    let stockClass = 'bg-emerald-100 text-emerald-800';
    if (idadi == 0) {
        stockClass = 'bg-gray-100 text-gray-800';
    } else if (idadi < 10) {
        stockClass = 'bg-amber-100 text-amber-800';
    }
    
    // Format idadi to show 2 decimal places if needed
    const formattedIdadi = idadi % 1 === 0 ? idadi.toString() : idadi.toFixed(2);
    
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
                ${formattedIdadi}
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
                            <td class="text-center">${parseFloat(p.idadi || 0).toFixed(2)}</td>
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
    // Get current URL parameters
    const url = new URL(window.location.href);
    const params = new URLSearchParams(url.search);
    
    // Remove pagination parameters
    params.delete('page');
    params.delete('per_page');
    
    // Add export parameter
    params.set('export', 'pdf');
    
    // Redirect to export URL
    window.location.href = `${url.pathname}?${params.toString()}`;
}

function exportExcel() {
    // Get current URL parameters
    const url = new URL(window.location.href);
    const params = new URLSearchParams(url.search);
    
    // Remove pagination parameters
    params.delete('page');
    params.delete('per_page');
    
    // Add export parameter
    params.set('export', 'excel');
    
    // Redirect to export URL
    window.location.href = `${url.pathname}?${params.toString()}`;
}

// Tab handling - FIXED: Updated to use correct tab IDs
document.addEventListener('DOMContentLoaded', function() {
    const savedTab = sessionStorage.getItem('bidhaa_tab') || 'orodha';
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

// ========== TAARIFA TAB - SMART SEARCH & STOCK MANAGEMENT ==========
let selectedProductId = null;
let taarifaSearchTimeout = null;

// Initialize smart search (searches ALL products)
function initializeSmartSearch() {
    const searchInput = document.getElementById('product-search-input');
    const resultsDropdown = document.getElementById('search-results-dropdown');
    const searchSpinner = document.getElementById('search-spinner');
    
    if (!searchInput) return;
    
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.trim();
        
        // Clear previous timeout
        clearTimeout(taarifaSearchTimeout);
        
        // Hide dropdown if search is too short
        if (searchTerm.length < 2) {
            resultsDropdown.classList.add('hidden');
            resultsDropdown.innerHTML = '';
            return;
        }
        
        // Show spinner
        searchSpinner.classList.remove('hidden');
        
        // Debounce search
        taarifaSearchTimeout = setTimeout(() => {
            searchAllProductsForTaarifa(searchTerm);
        }, 300);
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !resultsDropdown.contains(e.target)) {
            resultsDropdown.classList.add('hidden');
        }
    });
    
    // Handle keyboard navigation
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            const firstItem = resultsDropdown.querySelector('.search-result-item');
            if (firstItem) firstItem.focus();
        }
    });
}

// Search ALL products for Taarifa tab (no pagination)
function searchAllProductsForTaarifa(searchTerm) {
    const resultsDropdown = document.getElementById('search-results-dropdown');
    const searchSpinner = document.getElementById('search-spinner');
    
    fetch(`/bidhaa/search-products?q=${encodeURIComponent(searchTerm)}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        searchSpinner.classList.add('hidden');
        
        if (data.success && data.data.length > 0) {
            displaySearchResultsForTaarifa(data.data);
        } else {
            resultsDropdown.innerHTML = `
                <div class="p-4 text-center text-gray-500">
                    <i class="fas fa-search text-2xl mb-2 text-gray-300"></i>
                    <p>Hakuna bidhaa inayolingana na "${searchTerm}"</p>
                </div>
            `;
            resultsDropdown.classList.remove('hidden');
        }
    })
    .catch(error => {
        searchSpinner.classList.add('hidden');
        console.error('Search error:', error);
        resultsDropdown.innerHTML = `
            <div class="p-4 text-center text-red-500">
                <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
                <p>Hitilafu ya utafutaji. Jaribu tena.</p>
            </div>
        `;
        resultsDropdown.classList.remove('hidden');
    });
}

// Display search results for Taarifa tab
function displaySearchResultsForTaarifa(products) {
    const resultsDropdown = document.getElementById('search-results-dropdown');
    
    let html = '';
    products.forEach(product => {
        const stockClass = product.idadi <= 0 ? 'text-red-600' : (product.idadi < 10 ? 'text-amber-600' : 'text-emerald-600');
        
        html += `
            <div class="search-result-item p-3 border-b border-gray-100 hover:bg-emerald-50 cursor-pointer focus:bg-emerald-50 focus:outline-none"
                 tabindex="0"
                 role="button"
                 data-id="${product.id}"
                 data-jina="${product.jina}"
                 data-aina="${product.aina}"
                 data-barcode="${product.barcode || ''}"
                 data-idadi="${product.idadi}">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="font-medium text-gray-900">${product.jina}</div>
                        <div class="text-xs text-gray-600">
                            <span class="mr-2">${product.aina}</span>
                            ${product.barcode ? `<span class="text-emerald-600 font-mono">#${product.barcode}</span>` : ''}
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-medium ${stockClass}">${formatNumber(product.idadi, 2)}</span>
                    </div>
                </div>
            </div>
        `;
    });
    
    resultsDropdown.innerHTML = html;
    resultsDropdown.classList.remove('hidden');
    
    // Add click handlers
    document.querySelectorAll('.search-result-item').forEach(item => {
        item.addEventListener('click', function() {
            selectProductForTaarifa(this);
        });
        
        item.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                selectProductForTaarifa(this);
            } else if (e.key === 'ArrowDown') {
                e.preventDefault();
                const next = this.nextElementSibling;
                if (next) next.focus();
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                const prev = this.previousElementSibling;
                if (prev) {
                    prev.focus();
                } else {
                    document.getElementById('product-search-input').focus();
                }
            }
        });
    });
}

// Select a product from search results for Taarifa tab
function selectProductForTaarifa(element) {
    const productId = element.dataset.id;
    const productJina = element.dataset.jina;
    const productAina = element.dataset.aina;
    const productBarcode = element.dataset.barcode;
    const productIdadi = element.dataset.idadi;
    
    selectedProductId = productId;
    
    // Update UI
    document.getElementById('product-search-input').value = productJina;
    document.getElementById('search-results-dropdown').classList.add('hidden');
    
    // Show selected product info
    document.getElementById('selected-product-info').classList.remove('hidden');
    document.getElementById('selected-product-name').textContent = productJina;
    document.getElementById('selected-product-details').innerHTML = `
        ${productAina} | Idadi: ${formatNumber(productIdadi, 2)} ${productBarcode ? '| #' + productBarcode : ''}
    `;
    
    // Load product details
    loadProductDetails(productId);
}

// Clear selected product
function clearSelectedProduct() {
    selectedProductId = null;
    document.getElementById('product-search-input').value = '';
    document.getElementById('selected-product-info').classList.add('hidden');
    document.getElementById('product-details-container').classList.add('hidden');
    document.getElementById('no-product-selected').classList.remove('hidden');
    
    // Update URL
    const url = new URL(window.location);
    url.searchParams.delete('product_id');
    window.history.pushState({}, '', url);
}

// Load product by ID (from URL)
function loadProductById(productId) {
    fetch(`/bidhaa/search-products?q=&id=${productId}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.data.length > 0) {
            const product = data.data[0];
            selectedProductId = product.id;
            
            document.getElementById('product-search-input').value = product.jina;
            document.getElementById('selected-product-info').classList.remove('hidden');
            document.getElementById('selected-product-name').textContent = product.jina;
            document.getElementById('selected-product-details').innerHTML = `
                ${product.aina} | Idadi: ${formatNumber(product.idadi, 2)} ${product.barcode ? '| #' + product.barcode : ''}
            `;
            
            loadProductDetails(productId);
        }
    })
    .catch(error => console.error('Error loading product:', error));
}

// Load product details
function loadProductDetails(productId) {
    const loadingEl = document.getElementById('details-loading');
    const container = document.getElementById('product-details-container');
    const noProduct = document.getElementById('no-product-selected');
    
    // Get date filters
    const startDate = document.getElementById('start-date').value;
    const endDate = document.getElementById('end-date').value;
    
    // Build URL with filters
    let url = `/bidhaa/taarifa?bidhaa_id=${productId}`;
    if (startDate) url += `&start_date=${startDate}`;
    if (endDate) url += `&end_date=${endDate}`;
    
    // Show loading
    loadingEl.classList.remove('hidden');
    container.classList.add('hidden');
    noProduct.classList.add('hidden');
    
    // Update date range display
    updateDateRangeDisplay(startDate, endDate);
    
    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        loadingEl.classList.add('hidden');
        
        if (data.success) {
            displayProductDetails(data.data);
            container.classList.remove('hidden');
            
            // Update URL
            const url = new URL(window.location);
            url.searchParams.set('product_id', productId);
            if (startDate) url.searchParams.set('start_date', startDate);
            if (endDate) url.searchParams.set('end_date', endDate);
            window.history.pushState({}, '', url);
        } else {
            showNotification(data.message || 'Hitilafu katika kupakia taarifa', 'error');
            noProduct.classList.remove('hidden');
        }
    })
    .catch(error => {
        loadingEl.classList.add('hidden');
        noProduct.classList.remove('hidden');
        showNotification('Hitilafu ya mtandao', 'error');
        console.error('Error loading product details:', error);
    });
}

// Update date range display
function updateDateRangeDisplay(startDate, endDate) {
    const display = document.getElementById('date-range-display');
    if (!display) return;
    
    if (startDate && endDate) {
        display.textContent = `${startDate} mpaka ${endDate}`;
    } else if (startDate) {
        display.textContent = `Kuanzia ${startDate}`;
    } else if (endDate) {
        display.textContent = `Mpaka ${endDate}`;
    } else {
        display.textContent = 'Tarehe zote';
    }
}
// Display product details - FIXED
function displayProductDetails(data) {
    const p = data.bidhaa;
    const stats = data.statistics;
    
    // Basic info
    document.getElementById('detail-jina').textContent = p.jina || '-';
    document.getElementById('detail-aina').textContent = p.aina || '-';
    document.getElementById('detail-kipimo').textContent = p.kipimo ? `Kipimo: ${p.kipimo}` : '';
    document.getElementById('detail-barcode').textContent = p.barcode ? `Barcode: #${p.barcode}` : '';
    
    // SIMPLIFIED CARDS
    document.getElementById('stat-idadi-sasa').textContent = formatNumber(p.idadi_sasa, 2);
    document.getElementById('stat-jumla-ingizo').textContent = formatNumber(stats.jumlah_iliyoingizwa, 2);
    
    // Jumla Iliyouzwa (Mauzo Cash + Kopesha)
    document.getElementById('stat-jumla-mauzo').textContent = formatNumber(stats.jumlah_mauzo_jumla, 2);
    
    // Zilizobaki = Current Stock (actual)
    document.getElementById('stat-zilizobaki').textContent = formatNumber(p.idadi_sasa, 2);
    
    // Dates and initial
    document.getElementById('detail-tarehe-kwanza').textContent = stats.tarehe_ya_kwanza || p.imeundwa;
    
    // Stock status badge
    const stockBadge = document.getElementById('stock-status-badge');
    const idadi = parseFloat(p.idadi_sasa);
    if (idadi <= 0) {
        stockBadge.className = 'inline-block px-2 py-1 rounded text-xs font-medium bg-gray-200 text-gray-800';
        stockBadge.textContent = 'Imeisha';
    } else if (idadi < 10) {
        stockBadge.className = 'inline-block px-2 py-1 rounded text-xs font-medium bg-amber-200 text-amber-800';
        stockBadge.textContent = 'Inakaribia kuisha';
    } else {
        stockBadge.className = 'inline-block px-2 py-1 rounded text-xs font-medium bg-emerald-200 text-emerald-800';
        stockBadge.textContent = 'Inapatikana';
    }
    
    // Expiry badge
    const expiryBadge = document.getElementById('expiry-badge');
    const expiryDays = document.getElementById('expiry-days');
    
    if (p.expiry) {
        const expiryDate = new Date(p.expiry);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        const diffTime = expiryDate - today;
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        if (diffDays < 0) {
            expiryBadge.className = 'inline-block px-2 py-1 rounded text-xs font-medium bg-red-200 text-red-800';
            expiryBadge.textContent = `Imepita (${p.expiry})`;
            expiryDays.textContent = 'Imekwisha muda';
        } else if (diffDays <= 30) {
            expiryBadge.className = 'inline-block px-2 py-1 rounded text-xs font-medium bg-amber-200 text-amber-800';
            expiryBadge.textContent = `Inakaribia (${p.expiry})`;
            expiryDays.textContent = `${diffDays} siku zimesalia`;
        } else {
            expiryBadge.className = 'inline-block px-2 py-1 rounded text-xs font-medium bg-green-200 text-green-800';
            expiryBadge.textContent = `Bado (${p.expiry})`;
            expiryDays.textContent = `${diffDays} siku zimesalia`;
        }
    } else {
        expiryBadge.className = 'inline-block px-2 py-1 rounded text-xs font-medium bg-gray-200 text-gray-800';
        expiryBadge.textContent = 'Hakuna expiry';
        expiryDays.textContent = '-';
    }
    
    // History table
    const historyTbody = document.getElementById('history-tbody');
    document.getElementById('history-count').textContent = `Jumla: ${data.total_transactions || 0}`;
    
    if (data.histories && data.histories.length > 0) {
        historyTbody.innerHTML = data.histories.map(h => {
            // Determine badge color based on transaction type
            let badgeClass = 'bg-gray-100 text-gray-800';
            let typeText = h.aina;
            
            if (h.aina === 'manunuzi') {
                badgeClass = 'bg-emerald-100 text-emerald-800';
                typeText = 'Manunuzi';
            } else if (h.aina === 'mauzo') {
                badgeClass = 'bg-blue-100 text-blue-800';
                typeText = 'Mauzo (Cash)';
            } else if (h.aina === 'kopesha') {
                badgeClass = 'bg-amber-100 text-amber-800';
                typeText = 'Kopesha';
            } else if (h.aina === 'marejesho') {
                badgeClass = 'bg-purple-100 text-purple-800';
                typeText = 'Marejesho (Malipo)';
            }
            
            return `
                <tr class="hover:bg-gray-50">
                    <td class="px-3 py-2 text-xs">${h.tarehe}</td>
                    <td class="px-3 py-2">
                        <span class="px-2 py-0.5 rounded text-xs font-medium ${badgeClass}">
                            ${typeText}
                        </span>
                    </td>
                    <td class="px-3 py-2 text-right text-xs">${h.idadi_iliyoingizwa > 0 ? formatNumber(h.idadi_iliyoingizwa, 2) : '-'}</td>
                    <td class="px-3 py-2 text-right text-xs">${h.idadi_iliyouzwa > 0 ? formatNumber(h.idadi_iliyouzwa, 2) : '-'}</td>
                    <td class="px-3 py-2 text-right text-xs font-medium">${formatNumber(h.idadi_iliyobaki, 2)}</td>
                    <td class="px-3 py-2 text-xs text-gray-600">${h.maelezo || '-'}</td>
                </tr>
            `;
        }).join('');
    } else {
        historyTbody.innerHTML = `
            <tr>
                <td colspan="6" class="px-3 py-4 text-center text-gray-500">
                    Hakuna historia ya shughuli
                </td>
            </tr>
        `;
    }
}
// Print product details
function printProductDetails() {
    const container = document.getElementById('product-details-container');
    if (!container || container.classList.contains('hidden')) {
        showNotification('Hakuna bidhaa iliyochaguliwa', 'warning');
        return;
    }
    
    const jina = document.getElementById('detail-jina').textContent;
    const startDate = document.getElementById('start-date').value;
    const endDate = document.getElementById('end-date').value;
    const dateRangeText = (startDate && endDate) ? `${startDate} mpaka ${endDate}` : 'Tarehe zote';
    
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
        <head>
            <title>Taarifa za ${jina} - ${new Date().toLocaleDateString()}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                h1 { color: #047857; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f3f4f6; }
                .header { text-align: center; margin-bottom: 30px; }
                .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin: 20px 0; }
                .stat-card { border: 1px solid #ddd; padding: 10px; border-radius: 5px; }
                .stat-label { font-size: 12px; color: #666; }
                .stat-value { font-size: 18px; font-weight: bold; }
                .text-right { text-align: right; }
                .badge { display: inline-block; padding: 2px 6px; border-radius: 3px; font-size: 11px; }
                .badge-success { background: #d1fae5; color: #047857; }
                .badge-warning { background: #fef3c7; color: #92400e; }
                .badge-danger { background: #fee2e2; color: #b91c1c; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>Taarifa za Bidhaa</h1>
                <p>Tarehe: ${new Date().toLocaleDateString()}</p>
                <p>Kipindi: ${dateRangeText}</p>
            </div>
            
            <div style="margin-bottom: 20px;">
                <h2>${document.getElementById('detail-jina').textContent}</h2>
                <p>Aina: ${document.getElementById('detail-aina').textContent}</p>
                <p>Kipimo: ${document.getElementById('detail-kipimo').textContent || 'Hakuna'}</p>
                <p>Barcode: ${document.getElementById('detail-barcode').textContent || 'Hakuna'}</p>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Idadi Iliyopo Sasa</div>
                    <div class="stat-value">${document.getElementById('stat-idadi-sasa').textContent}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Jumla Iliyoingizwa</div>
                    <div class="stat-value">${document.getElementById('stat-jumla-ingizo').textContent}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Jumla Iliyouzwa</div>
                    <div class="stat-value">${document.getElementById('stat-jumla-mauzo').textContent}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Zilizobaki (Hesabu)</div>
                    <div class="stat-value">${document.getElementById('stat-zilizobaki').textContent}</div>
                </div>
            </div>
            
            <h3>Historia ya Shughuli</h3>
            <table>
                <thead>
                    <tr>
                        <th>Tarehe</th>
                        <th>Aina</th>
                        <th class="text-right">Iliyoingizwa</th>
                        <th class="text-right">Iliyouzwa</th>
                        <th class="text-right">Iliyobaki</th>
                        <th>Maelezo</th>
                    </tr>
                </thead>
                <tbody>
                    ${Array.from(document.querySelectorAll('#history-tbody tr')).map(row => row.outerHTML).join('')}
                </tbody>
            </table>
            
            <div style="margin-top: 30px; font-size: 10px; color: #666; text-align: center; border-top: 1px solid #ddd; padding-top: 10px;">
                Ripoti imetolewa na mfumo tarehe ${new Date().toLocaleString()}
            </div>
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

// Helper function to format numbers
function formatNumber(num, decimals = 2) {
    if (num === null || num === undefined) return '0';
    return parseFloat(num).toLocaleString(undefined, {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    });
}

function formatCurrency(amount) {
    if (amount === null || amount === undefined) return '0 TZS';
    return parseFloat(amount).toLocaleString() + ' TZS';
}

</script>
@endpush