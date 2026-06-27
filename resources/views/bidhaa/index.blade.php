@extends('layouts.app')

@section('title', 'Bidhaa')

@section('page-title', 'Bidhaa')
@section('page-subtitle', now()->format('d/m/Y'))

@section('content')
<div class="space-y-4" id="app-container" data-current-page="{{ request()->get('page', 1) }}" data-is-boss="{{ $isBoss ? 'true' : 'false' }}" data-can-view-price="{{ $canViewPurchasePrice ? 'true' : 'false' }}" data-can-edit-delete="{{ $canEditDelete ? 'true' : 'false' }}"> <!-- Notifications -->
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
<!-- Add this after the stats cards and before the Zilizoisha Button section -->
<!-- Showcase Link Generator -->
<div class="bg-gradient-to-r from-emerald-50 to-amber-50 p-4 rounded-lg border border-emerald-200 shadow-sm">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="h-12 w-12 bg-emerald-100 rounded-full flex items-center justify-center">
                <i class="fas fa-store-alt text-emerald-600 text-xl"></i>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-800">Customer Showcase Page</h3>
                <p class="text-xs text-gray-600">Share this link with customers to browse and order products</p>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <button onclick="generateShowcaseLink()" 
                    class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-all text-sm font-medium shadow-sm hover:shadow-md">
                <i class="fas fa-link mr-2"></i> Generate Link
            </button>
            <button onclick="generateQRCode()" 
                    class="inline-flex items-center px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-all text-sm font-medium shadow-sm hover:shadow-md">
                <i class="fas fa-qrcode mr-2"></i> QR Code
            </button>
        </div>
    </div>
    
    <!-- Showcase Link Display (hidden initially) -->
    <div id="showcaseLinkContainer" class="hidden mt-3 p-3 bg-white rounded-lg border border-emerald-200">
        <div class="flex flex-wrap items-center gap-2">
            <div class="flex-1 min-w-0">
                <label class="text-xs font-medium text-gray-600 block mb-1">Your Showcase Link:</label>
                <div class="flex items-center gap-2">
                    <input type="text" id="showcaseLinkInput" 
                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 min-w-0"
                           readonly>
                    <button onclick="copyShowcaseLink()" 
                            class="px-3 py-2 bg-emerald-100 text-emerald-700 rounded-lg hover:bg-emerald-200 transition-all text-sm font-medium whitespace-nowrap">
                        <i class="fas fa-copy mr-1"></i> Copy
                    </button>
                    <button onclick="openShowcaseLink()" 
                            class="px-3 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-all text-sm font-medium whitespace-nowrap">
                        <i class="fas fa-external-link-alt mr-1"></i> Open
                    </button>
                </div>
            </div>
        </div>
        <div class="mt-2 flex flex-wrap gap-2">
            <button onclick="shareOnWhatsApp()" 
                    class="px-3 py-1.5 bg-[#25D366] text-white rounded-lg hover:bg-[#1DA851] transition-all text-xs font-medium flex items-center gap-1">
                <i class="fab fa-whatsapp"></i> Share on WhatsApp
            </button>
            <button onclick="shareViaEmail()" 
                    class="px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all text-xs font-medium flex items-center gap-1">
                <i class="fas fa-envelope"></i> Share via Email
            </button>
            <button onclick="printShowcaseLink()" 
                    class="px-3 py-1.5 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-all text-xs font-medium flex items-center gap-1">
                <i class="fas fa-print"></i> Print
            </button>
        </div>
    </div>
    
    <!-- QR Code Display (hidden initially) -->
    <div id="qrCodeContainer" class="hidden mt-3 p-3 bg-white rounded-lg border border-amber-200 text-center">
        <div class="flex flex-col items-center">
            <p class="text-xs font-medium text-gray-600 mb-2">Scan to open showcase page:</p>
            <div id="qrCodeDisplay" class="p-2 bg-white rounded-lg inline-block"></div>
            <div class="mt-2 flex gap-2">
                <button onclick="downloadQRCode()" 
                        class="px-3 py-1.5 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-all text-xs font-medium">
                    <i class="fas fa-download mr-1"></i> Download QR
                </button>
                <button onclick="document.getElementById('qrCodeContainer').classList.add('hidden')" 
                        class="px-3 py-1.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all text-xs font-medium">
                    Close
                </button>
            </div>
        </div>
    </div>
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
            
<!-- Delete All Button - Only for Boss -->
@if($isBoss && $totalProducts > 0)
<button onclick="confirmDeleteAll()" 
        class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm font-medium">
    <i class="fas fa-trash-alt mr-2"></i> Futa Zote
    <span class="ml-2 bg-white text-red-600 px-2 py-0.5 rounded-full text-xs font-bold">{{ $totalProducts }}</span>
</button>
@endif
        </div>
        
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

    <!-- Tabs -->
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

<!-- TAB 1: Orodha -->
<div id="orodha-tab-content" class="tab-content space-y-3">
    <!-- Search Bar -->
    <div class="bg-white p-3 rounded-lg border border-gray-200 shadow-sm">
        <div class="flex flex-col sm:flex-row gap-2 sm:items-center">
            <div class="flex-1 relative">
                <input 
                    type="text" 
                    id="search-input"
                    placeholder="Tafuta bidhaa, aina, barcode, kipimo..." 
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
            <table class="w-full text-sm table-fixed">
                <thead>
                    <tr class="bg-emerald-50 border-b border-gray-200">
                        <th class="w-12 px-3 py-2 text-center font-medium text-emerald-800 text-sm">Picha</th>
                        <th class="w-2/5 px-3 py-2 text-left font-medium text-emerald-800 text-sm">Bidhaa</th>
                        <th class="w-1/5 px-3 py-2 text-left font-medium text-emerald-800 text-sm hidden sm:table-cell">Aina</th>
                        <th class="w-16 px-3 py-2 text-center font-medium text-emerald-800 text-sm">Idadi</th>
                        <th class="w-1/4 px-3 py-2 text-right font-medium text-emerald-800 text-sm">Bei</th>
                        <th class="w-24 px-3 py-2 text-left font-medium text-emerald-800 text-sm hidden lg:table-cell">Expiry</th>
                        <th class="w-16 px-3 py-2 text-center font-medium text-emerald-800 text-sm print:hidden">Vitendo</th>
                    </tr>
                </thead>
                <tbody id="products-tbody" class="divide-y divide-gray-100">
                    @forelse($bidhaa as $item)
                    <tr class="product-row hover:bg-gray-50" data-product='@json($item)'>
                        <td class="px-3 py-2 text-center align-middle">
                            @if($item->has_image)
                                @if($item->image_url)
                                    <img src="{{ $item->image_url }}" 
                                         alt="{{ $item->jina }}" 
                                         class="h-10 w-10 object-cover rounded-full border border-gray-200 cursor-pointer hover:opacity-80" 
                                         onclick="showImageModal('{{ $item->image_url }}', '{{ addslashes($item->jina) }}')">
                                @elseif($item->image_base64)
                                    <img src="{{ $item->image_base64 }}" 
                                         alt="{{ $item->jina }}" 
                                         class="h-10 w-10 object-cover rounded-full border border-gray-200 cursor-pointer hover:opacity-80" 
                                         onclick="showImageModal('{{ $item->image_base64 }}', '{{ addslashes($item->jina) }}')">
                                @else
                                    <div class="h-10 w-10 bg-gray-100 rounded-full flex items-center justify-center mx-auto cursor-pointer" 
                                         onclick="showNotification('Hakuna picha ya bidhaa hii', 'info')">
                                        <i class="fas fa-image text-gray-400 text-sm"></i>
                                    </div>
                                @endif
                            @else
                                <div class="h-10 w-10 bg-gray-100 rounded-full flex items-center justify-center mx-auto cursor-pointer" 
                                     onclick="showNotification('Hakuna picha ya bidhaa hii', 'info')">
                                    <i class="fas fa-image text-gray-400 text-sm"></i>
                                </div>
                            @endif
                        </td>
                        <td class="px-3 py-2 align-middle">
                            <div class="flex items-center">
                                <div class="min-w-0">
                                    <div class="font-medium text-gray-900 text-sm truncate" title="{{ $item->jina }}">{{ $item->jina }}</div>
                                    <div class="text-xs text-gray-500 sm:hidden truncate">{{ $item->aina }}</div>
                                    @if($item->barcode)
                                    <div class="text-xs text-emerald-600 font-mono truncate">#{{ $item->barcode }}</div>
                                    @endif
                                    @if($item->image_size)
                                    <div class="text-xs text-gray-400 truncate">{{ $item->formatted_image_size }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-3 py-2 align-middle hidden sm:table-cell">
                            <div class="text-sm text-gray-700 truncate">{{ $item->aina }}</div>
                            @if($item->kipimo)
                            <div class="text-xs text-gray-400 truncate">{{ $item->kipimo }}</div>
                            @endif
                        </td>
                        <td class="px-3 py-2 text-center align-middle">
                            @php
                                $formattedIdadi = $item->idadi % 1 == 0 ? (string)(int)$item->idadi : number_format($item->idadi, 2);
                            @endphp
                            <span class="inline-flex items-center justify-center px-2 py-0.5 rounded text-xs font-medium whitespace-nowrap
                                @if($item->idadi < 10 && $item->idadi > 0) bg-amber-100 text-amber-800
                                @elseif($item->idadi == 0) bg-gray-100 text-gray-800
                                @else bg-emerald-100 text-emerald-800 @endif">
                                {{ $formattedIdadi }}
                            </span>
                        </td>
                        <td class="px-3 py-2 text-right align-middle">
                            <div class="text-sm font-bold text-emerald-700 whitespace-nowrap">{{ number_format($item->bei_kuuza, 0) }} TZS</div>
                            @if($item->bei_uzo_jumla)
                            <div class="text-xs text-blue-600 whitespace-nowrap">Jumla: {{ number_format($item->bei_uzo_jumla, 0) }} TZS</div>
                            @endif
                            @if($canViewPurchasePrice)
                            <div class="text-xs text-gray-500 whitespace-nowrap">Nunua: {{ number_format($item->bei_nunua, 0) }} TZS</div>
                            @endif
                        </td>
                        <td class="px-3 py-2 align-middle hidden lg:table-cell">
                            @if($item->expiry)
                                @php
                                    $expiryDate = \Carbon\Carbon::parse($item->expiry);
                                    $today = \Carbon\Carbon::today();
                                    $diffDays = $today->diffInDays($expiryDate, false);
                                @endphp
                                <div class="text-xs whitespace-nowrap
                                    @if($expiryDate < $today) text-red-600 font-medium
                                    @elseif($diffDays <= 30) text-amber-600 font-medium
                                    @else text-gray-600 @endif"
                                    title="Tarehe ya kuisha: {{ $expiryDate->format('d/m/Y') }}">
                                    {{ $expiryDate->format('d/m/Y') }}
                                    @if($expiryDate >= $today && $diffDays <= 30)
                                        <span class="text-xs">({{ $diffDays }}d)</span>
                                    @elseif($expiryDate < $today)
                                        <span class="text-xs">(Imepita)</span>
                                    @endif
                                </div>
                            @else
                                <span class="text-xs text-gray-400">--</span>
                            @endif
                        </td>
                        <td class="px-3 py-2 text-center align-middle print:hidden">
                            <div class="flex justify-center space-x-1">
                                @if($canEditDelete)
                                    <button class="edit-product-btn text-emerald-600 hover:text-emerald-800 p-1"
                                            data-id="{{ $item->id }}" title="Badili">
                                        <i class="fas fa-edit text-sm"></i>
                                    </button>
                                    <button class="delete-product-btn text-red-600 hover:text-red-800 p-1"
                                            data-id="{{ $item->id }}" data-name="{{ $item->jina }}" title="Futa">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                @else
                                    <span class="text-gray-400 cursor-not-allowed" title="Huwezi kurekebisha au kufuta">
                                        <i class="fas fa-lock text-sm"></i>
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
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
        <div id="pagination-container" class="px-3 py-2 border-t border-gray-200 bg-gray-50">
            <div class="flex justify-between items-center">
                <div class="text-xs text-gray-500">
                </div>
                <div class="pagination-wrapper">
                    {{ $bidhaa->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Image Preview Modal -->
<div id="image-modal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-75"></div>
    <div class="modal-content bg-white rounded-lg shadow-lg max-w-2xl w-full mx-auto z-50">
        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-sm font-semibold text-gray-800" id="image-modal-title">Picha ya Bidhaa</h3>
            <button onclick="closeImageModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-4 flex justify-center">
            <img id="image-modal-img" src="" alt="Product Image" class="max-w-full max-h-96 object-contain">
        </div>
    </div>
</div>

    <!-- TAB 2: Ingiza -->
<div id="ingiza-tab-content" class="tab-content hidden">
    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
        <form method="POST" action="{{ route('bidhaa.store') }}" id="product-form" class="space-y-4" enctype="multipart/form-data">
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
                    <label class="block text-xs font-medium text-gray-700 mb-1">Bei Kuuza - Rejareja (TZS) *</label>
                    <input type="number" step="0.01" name="bei_kuuza" id="sell-price"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                           placeholder="0.00" required>
                    <p class="text-xs text-gray-500 mt-1">Bei ya kawaida kwa wateja</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Bei Jumla (Wholesale) (TZS)</label>
                    <input type="number" step="0.01" name="bei_uzo_jumla" id="wholesale-price"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                           placeholder="Hiari">
                    <p class="text-xs text-gray-500 mt-1">Bei ya jumla kwa wateja wakubwa (Hiari)</p>
                </div>
                <div id="price-type-container" class="hidden">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Aina ya Bei ya Default</label>
                    <select name="bei_kiasi_cha_chaguo" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                        <option value="rejareja">Rejareja (Kawaida)</option>
                        <option value="jumla">Jumla (Pembeni)</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Chagua aina ya bei itakayotumika kwa default</p>
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
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Picha ya Bidhaa</label>
                    <input type="file" name="image" id="product-image" accept="image/*" 
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 file:mr-2 file:py-1 file:px-3 file:rounded file:border-0 file:text-sm file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                    <p class="text-xs text-gray-500 mt-1">Ukubwa: 1KB - 5,500KB (Maks: 5.5MB). Picha itapunguzwa kiotomatiki ikiwa kubwa</p>
                    <div id="image-preview" class="hidden mt-2">
                        <img id="image-preview-img" src="" alt="Preview" class="h-20 w-20 object-cover rounded border border-gray-200">
                        <button type="button" id="remove-image" class="text-xs text-red-600 mt-1 hover:text-red-800">Ondoa Picha</button>
                    </div>
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
                        
                        <div class="border border-emerald-200 rounded p-3 bg-emerald-50">
                            <h4 class="text-xs font-medium text-emerald-800 mb-2 flex items-center">
                                <i class="fas fa-info-circle mr-1"></i> 
                                JINSI INAVYOFANYA KAZI:
                            </h4>
                            <div class="space-y-2 text-xs">
                                <p class="flex items-start">
                                    <span class="text-emerald-600 font-bold mr-2">✓</span>
                                    <span><span class="font-bold">Kama bidhaa ipo</span> - ita<b>BADILISHA</b> (idadi itachukua nafasi)</span>
                                </p>
                                <p class="flex items-start">
                                    <span class="text-emerald-600 font-bold mr-2">✓</span>
                                    <span><span class="font-bold">Kama bidhaa haipo</span> - ita<b>ONGEEWA</b> mpya</span>
                                </p>
                                <p class="flex items-start">
                                    <span class="text-emerald-600 font-bold mr-2">✓</span>
                                    <span>Unaweza kuweka <span class="font-bold">Bei Jumla</span> (Hiari)</span>
                                </p>
                            </div>
                        </div>
                        
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
                                            <th class="px-2 py-1 border border-blue-200 text-left">Bei Rejareja *</th>
                                            <th class="px-2 py-1 border border-blue-200 text-left">Bei Jumla</th>
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
                                            <td class="px-2 py-1 border border-blue-200">800</td>
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
                                            <td class="px-2 py-1 border border-blue-200">3000</td>
                                            <td class="px-2 py-1 border border-blue-200">2026-06-30</td>
                                            <td class="px-2 py-1 border border-blue-200"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
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

    <!-- TAB 4: Taarifa -->
    <div id="ripoti-tab-content" class="tab-content hidden">
        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
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
                
                <div id="search-results-dropdown" class="hidden mt-1 border-2 border-emerald-200 rounded-lg bg-white shadow-xl max-h-80 overflow-y-auto absolute z-50 w-full md:w-2/3"></div>
                
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

            <div id="details-loading" class="hidden text-center py-8">
                <i class="fas fa-spinner fa-spin text-3xl text-emerald-600 mb-2"></i>
                <p class="text-gray-600">Inapakia taarifa za bidhaa...</p>
            </div>

            <div id="product-details-container" class="hidden space-y-4">
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

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                    <div class="bg-emerald-50 p-3 rounded-lg border border-emerald-200">
                        <p class="text-xs text-gray-600 mb-1">Idadi Iliyopo Sasa</p>
                        <p class="text-2xl font-bold text-emerald-700" id="stat-idadi-sasa">0</p>
                        <p class="text-xs text-gray-500 mt-1">Kwenye stock</p>
                    </div>
                    <div class="bg-blue-50 p-3 rounded-lg border border-blue-200">
                        <p class="text-xs text-gray-600 mb-1">Jumla Iliyoingizwa</p>
                        <p class="text-2xl font-bold text-blue-700" id="stat-jumla-ingizo">0</p>
                        <p class="text-xs text-gray-500 mt-1">Manunuzi yote</p>
                    </div>
                    <div class="bg-amber-50 p-3 rounded-lg border border-amber-200">
                        <p class="text-xs text-gray-600 mb-1">Jumla Iliyouzwa</p>
                        <p class="text-2xl font-bold text-amber-700" id="stat-jumla-mauzo">0</p>
                        <p class="text-xs text-gray-500 mt-1">Mauzo + Kopesha</p>
                    </div>
                    <div class="bg-purple-50 p-3 rounded-lg border border-purple-200">
                        <p class="text-xs text-gray-600 mb-1">Zilizobaki (Hesabu)</p>
                        <p class="text-2xl font-bold text-purple-700" id="stat-zilizobaki">0</p>
                        <p class="text-xs text-gray-500 mt-1">Ingizo - Mauzo</p>
                    </div>
                </div>

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
                                    <td colspan="6" class="px-3 py-4 text-center text-gray-500">Hakuna historia ya shughuli</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

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
        <form id="edit-form" method="POST" class="p-4" enctype="multipart/form-data">
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
                    <label class="block text-xs font-medium text-gray-700 mb-1">Bei Rejareja (TZS) *</label>
                    <input type="number" step="0.01" name="bei_kuuza" id="edit-bei-kuuza"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                           required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Bei Jumla (TZS)</label>
                    <input type="number" step="0.01" name="bei_uzo_jumla" id="edit-bei-uzo-jumla"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                           placeholder="Hiari">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Aina ya Bei Default</label>
                    <select name="bei_kiasi_cha_chaguo" id="edit-bei-kiasi-cha-chaguo"
                            class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                        <option value="rejareja">Rejareja (Kawaida)</option>
                        <option value="jumla">Jumla (Pembeni)</option>
                    </select>
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
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Picha ya Bidhaa</label>
                    <input type="file" name="image" id="edit-product-image" accept="image/*" 
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 file:mr-2 file:py-1 file:px-3 file:rounded file:border-0 file:text-sm file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                    <p class="text-xs text-gray-500 mt-1">Ukubwa: 1KB - 5,500KB. Picha itapunguzwa kiotomatiki</p>
                    <div id="edit-image-preview" class="hidden mt-2">
                        <img id="edit-image-preview-img" src="" alt="Preview" class="h-20 w-20 object-cover rounded border border-gray-200">
                        <button type="button" id="edit-remove-image" class="text-xs text-red-600 mt-1 hover:text-red-800">Ondoa Picha</button>
                    </div>
                    <div id="current-image-container" class="hidden mt-2">
                        <p class="text-xs text-gray-500 mb-1">Picha ya sasa:</p>
                        <img id="current-product-image" src="" alt="Current" class="h-20 w-20 object-cover rounded border border-gray-200">
                        <button type="button" id="delete-image-btn" class="text-xs text-red-600 mt-1 hover:text-red-800">Futa Picha</button>
                    </div>
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

<!-- Image Preview Modal -->
<div id="image-modal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-75"></div>
    <div class="modal-content bg-white rounded-lg shadow-lg max-w-2xl w-full mx-auto z-50">
        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-sm font-semibold text-gray-800" id="image-modal-title">Picha ya Bidhaa</h3>
            <button onclick="closeImageModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-4 flex justify-center">
            <img id="image-modal-img" src="" alt="Product Image" class="max-w-full max-h-96 object-contain">
        </div>
    </div>
</div>

<!-- Delete All Modal -->
<div id="delete-all-modal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-lg shadow-lg w-full max-w-md mx-auto z-50">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-800">Thibitisha Kufuta Bidhaa Zote</h3>
        </div>
        <div class="p-4">
            <div class="text-center mb-4">
                <i class="fas fa-exclamation-triangle text-red-500 text-3xl mb-3"></i>
                <p class="text-gray-700 text-sm mb-2">Una uhakika unataka kufuta <span id="delete-all-count" class="font-bold text-red-600"></span> bidhaa zote?</p>
                <p class="text-gray-500 text-xs">Hatua hii haiwezi kutenduliwa!</p>
                <p class="text-amber-600 text-xs mt-2 flex items-center justify-center gap-1">
                    <i class="fas fa-info-circle"></i> Taarifa zote za bidhaa zitaondolewa kabisa
                </p>
            </div>
            <div class="flex gap-2">
                <button id="cancel-delete-all"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 text-sm">
                    Ghairi
                </button>
                <button id="confirm-delete-all"
                        class="flex-1 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm font-medium">
                    Ndio, Futa Zote
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
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

.modal {
    display: flex;
    align-items: center;
    justify-content: center;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
    animation: fadeIn 0.3s ease-out;
}

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
@push('styles')
<style>
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

.modal {
    display: flex;
    align-items: center;
    justify-content: center;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
    animation: fadeIn 0.3s ease-out;
}

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

<style>
/* Table fixed layout for consistent heights */
.table-fixed {
    table-layout: fixed;
}

/* Better cell alignment */
.align-middle {
    vertical-align: middle;
}

/* Pagination styling */
.pagination-wrapper nav {
    display: inline-flex;
}

.pagination-wrapper .pagination {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    margin: 0;
    padding: 0;
}

.pagination-wrapper .page-item {
    display: inline-block;
    margin: 0;
}

.pagination-wrapper .page-link {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 32px;
    height: 32px;
    padding: 0 8px;
    font-size: 13px;
    font-weight: 500;
    color: #4b5563;
    background-color: white;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    transition: all 0.2s;
}

.pagination-wrapper .page-link:hover {
    background-color: #f3f4f6;
    border-color: #d1d5db;
    color: #1f2937;
}

.pagination-wrapper .active .page-link {
    background-color: #10b981;
    border-color: #10b981;
    color: white;
}

.pagination-wrapper .disabled .page-link {
    background-color: #f9fafb;
    border-color: #e5e7eb;
    color: #9ca3af;
    cursor: not-allowed;
}

/* For very small screens, make pagination wrap */
@media (max-width: 640px) {
    .pagination-wrapper .pagination {
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .pagination-wrapper .page-link {
        min-width: 28px;
        height: 28px;
        font-size: 11px;
        padding: 0 6px;
    }
}

/* Ensure all rows have same height */
#products-tbody tr {
    height: auto;
}

#products-tbody td {
    vertical-align: middle;
    padding-top: 8px;
    padding-bottom: 8px;
}

/* Price text wrapping */
.whitespace-nowrap {
    white-space: nowrap;
}

/* Responsive table */
@media (max-width: 768px) {
    .table-fixed {
        min-width: 600px;
    }
}
</style>
@endpush

@push('scripts')

<script>
let searchTimeout = null;
let allSearchResults = [];
let isSearchActive = false;
let currentSearchTerm = '';
let isBoss = document.getElementById('app-container')?.dataset.isBoss === 'true';
let canViewPrice = document.getElementById('app-container')?.dataset.canViewPrice === 'true';
let canEditDelete = document.getElementById('app-container')?.dataset.canEditDelete === 'true';
let originalRows = [];
let selectedProductId = null;
let taarifaSearchTimeout = null;

// Define formatNumber at the very top so it's available everywhere
function formatNumber(num, decimals = 2) {
    if (num === null || num === undefined) return '0';
    return parseFloat(num).toLocaleString(undefined, { minimumFractionDigits: decimals, maximumFractionDigits: decimals });
}

// ===== ENHANCED IMAGE HANDLING FUNCTIONS =====

/**
 * Show image modal with support for both URL and base64
 * @param {string} imageSrc - Image source (URL or base64)
 * @param {string} productName - Name of the product
 */
function showImageModal(imageSrc, productName) {
    const modal = document.getElementById('image-modal');
    const modalImg = document.getElementById('image-modal-img');
    const modalTitle = document.getElementById('image-modal-title');
    
    if (modal && modalImg) {
        modalImg.src = imageSrc;
        modalTitle.textContent = 'Picha: ' + productName;
        modal.classList.remove('hidden');
    }
}

/**
 * Show image modal from base64 data (legacy support)
 * @param {string} mimeType - MIME type of the image
 * @param {string} base64Data - Base64 encoded image data
 * @param {string} productName - Name of the product
 */
function showImageModalFromBase64(mimeType, base64Data, productName) {
    const modal = document.getElementById('image-modal');
    const modalImg = document.getElementById('image-modal-img');
    const modalTitle = document.getElementById('image-modal-title');
    
    if (modal && modalImg) {
        modalImg.src = 'data:' + mimeType + ';base64,' + base64Data;
        modalTitle.textContent = 'Picha: ' + productName;
        modal.classList.remove('hidden');
    }
}

/**
 * Close the image modal
 */
function closeImageModal() {
    document.getElementById('image-modal')?.classList.add('hidden');
}

/**
 * Format file size for display
 * @param {number} bytes - File size in bytes
 * @returns {string} Formatted file size
 */
function formatFileSize(bytes) {
    if (!bytes || bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

/**
 * Get image source from product data
 * @param {Object} product - Product object
 * @returns {string|null} Image source URL or null
 */
function getProductImageSrc(product) {
    if (!product) return null;
    
    // Priority 1: Image URL (filesystem storage)
    if (product.image_url) {
        return product.image_url;
    }
    
    // Priority 2: Image base64 (legacy BLOB storage)
    if (product.image_base64) {
        return product.image_base64;
    }
    
    // Priority 3: Raw image data (try to use as base64)
    if (product.image) {
        // Check if it's already a data URL
        if (product.image.startsWith('data:')) {
            return product.image;
        }
        
        // Check if it's a file path
        if (product.image.startsWith('/') || product.image.startsWith('http')) {
            return product.image;
        }
        
        // Try to use as base64
        try {
            atob(product.image);
            return 'data:image/jpeg;base64,' + product.image;
        } catch (e) {
            // Not valid base64
            return null;
        }
    }
    
    return null;
}

/**
 * Check if product has an image
 * @param {Object} product - Product object
 * @returns {boolean} True if product has image
 */
function productHasImage(product) {
    if (!product) return false;
    return !!(product.image_url || product.image_base64 || product.image);
}

// Image preview for add form
const productImageInput = document.getElementById('product-image');
if (productImageInput) {
    productImageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            validateAndPreviewImage(file, 'image-preview-img', 'image-preview');
        }
    });
    
    document.getElementById('remove-image')?.addEventListener('click', function() {
        productImageInput.value = '';
        document.getElementById('image-preview').classList.add('hidden');
        document.getElementById('image-preview-img').src = '';
    });
}

// Image preview for edit form
const editImageInput = document.getElementById('edit-product-image');
if (editImageInput) {
    editImageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            validateAndPreviewImage(file, 'edit-image-preview-img', 'edit-image-preview');
            document.getElementById('current-image-container')?.classList.add('hidden');
        }
    });
    
    document.getElementById('edit-remove-image')?.addEventListener('click', function() {
        editImageInput.value = '';
        document.getElementById('edit-image-preview').classList.add('hidden');
        document.getElementById('edit-image-preview-img').src = '';
        document.getElementById('current-image-container')?.classList.remove('hidden');
    });
}

/**
 * Validate and preview image file
 * @param {File} file - The image file
 * @param {string} previewImgId - ID of the preview image element
 * @param {string} previewContainerId - ID of the preview container
 * @returns {boolean} True if validation passes
 */
function validateAndPreviewImage(file, previewImgId, previewContainerId) {
    // Validate file size (1KB to 10MB for new system)
    const minSize = 1 * 1024; // 1KB in bytes
    const maxSize = 10240 * 1024; // 10MB in bytes (increased for compression handling)
    
    if (file.size < minSize) {
        showNotification('Picha ni ndogo sana. Ukubwa lazima uwe angalau 1KB', 'error');
        return false;
    }
    
    if (file.size > maxSize) {
        showNotification(`Picha ni kubwa sana. Ukubwa unaruhusiwa hadi 10MB. Faili yako: ${(file.size / 1024 / 1024).toFixed(2)}MB`, 'error');
        return false;
    }
    
    // Validate file type
    const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
    if (!allowedTypes.includes(file.type)) {
        showNotification('Aina ya faili haikubaliki. Tumia: JPG, PNG, GIF, au WEBP', 'error');
        return false;
    }
    
    // Preview image
    const reader = new FileReader();
    reader.onload = function(e) {
        const previewImg = document.getElementById(previewImgId);
        const previewContainer = document.getElementById(previewContainerId);
        if (previewImg && previewContainer) {
            previewImg.src = e.target.result;
            previewContainer.classList.remove('hidden');
        }
    };
    reader.readAsDataURL(file);
    return true;
}

document.addEventListener('DOMContentLoaded', function() {
    // Search input
    const searchInput = document.getElementById('search-input');
    const clearSearchBtn = document.getElementById('clear-search');
    
    if (searchInput) {
        storeOriginalRows();
        
        searchInput.removeAttribute('readonly');
        searchInput.removeAttribute('disabled');
        searchInput.style.pointerEvents = 'auto';
        searchInput.style.opacity = '1';
        searchInput.style.backgroundColor = 'white';
        
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.trim();
            currentSearchTerm = searchTerm;
            
            if (clearSearchBtn) {
                clearSearchBtn.classList.toggle('hidden', !searchTerm);
            }
            
            clearTimeout(searchTimeout);
            
            if (searchTerm.length >= 2) {
                const tbody = document.getElementById('products-tbody');
                if (tbody) {
                    tbody.innerHTML = `<tr><td colspan="7" class="px-4 py-8 text-center text-gray-500"><i class="fas fa-spinner fa-spin text-3xl mb-2 text-emerald-500"></i><p>Inatafuta kwenye bidhaa zote...</p></td></tr>`;
                }
                
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
    }
    
    // Wholesale price visibility
    const wholesalePriceInput = document.getElementById('wholesale-price');
    const priceTypeContainer = document.getElementById('price-type-container');
    
    if (wholesalePriceInput) {
        wholesalePriceInput.addEventListener('input', function() {
            if (this.value && parseFloat(this.value) > 0) {
                priceTypeContainer.classList.remove('hidden');
            } else {
                priceTypeContainer.classList.add('hidden');
                const select = document.querySelector('select[name="bei_kiasi_cha_chaguo"]');
                if (select) select.value = 'rejareja';
            }
        });
    }
    
    // Initialize smart search for Taarifa tab
    initializeSmartSearch();
    
    // Date filter
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
    
    // Tab handling
    const savedTab = sessionStorage.getItem('bidhaa_tab') || 'orodha';
    showTab(savedTab);
    
    document.querySelectorAll('.tab-button').forEach(button => {
        button.addEventListener('click', function(e) {
            const tab = e.target.closest('.tab-button').dataset.tab;
            showTab(tab);
            sessionStorage.setItem('bidhaa_tab', tab);
        });
    });
    
    // Modal handling
    document.getElementById('close-edit-modal')?.addEventListener('click', function() {
        document.getElementById('edit-modal').classList.add('hidden');
    });
    
    document.getElementById('cancel-delete')?.addEventListener('click', function() {
        document.getElementById('delete-modal').classList.add('hidden');
    });
    
    const modals = ['edit-modal', 'delete-modal', 'image-modal'];
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
            document.getElementById('image-modal')?.classList.add('hidden');
        }
    });
    
    // Form handling
    const productForm = document.getElementById('product-form');
    if (productForm) {
        productForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            await submitForm(this, 'Bidhaa imehifadhiwa!');
        });
    }
    
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
    
    const deleteForm = document.getElementById('delete-form');
    if (deleteForm) {
        deleteForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            await submitForm(this, 'Bidhaa imefutwa!');
            document.getElementById('delete-modal').classList.add('hidden');
        });
    }
    
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
    
    // Handle Delete All modal
    const cancelDeleteAll = document.getElementById('cancel-delete-all');
    const confirmDeleteAllBtn = document.getElementById('confirm-delete-all');
    const deleteAllModal = document.getElementById('delete-all-modal');
    
    if (cancelDeleteAll) {
        cancelDeleteAll.addEventListener('click', function() {
            deleteAllModal.classList.add('hidden');
        });
    }
    
    if (confirmDeleteAllBtn) {
        confirmDeleteAllBtn.addEventListener('click', async function() {
            const button = this;
            const originalText = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Inafuta...';
            
            try {
                const response = await fetch('{{ route("bidhaa.deleteAll") }}', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (response.ok && data.success) {
                    showNotification(data.message, 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showNotification(data.message || 'Hitilafu imetokea', 'error');
                    button.disabled = false;
                    button.innerHTML = originalText;
                }
            } catch (error) {
                showNotification('Hitilafu ya mtandao', 'error');
                button.disabled = false;
                button.innerHTML = originalText;
            }
            
            deleteAllModal.classList.add('hidden');
        });
    }
    
    // Close modal when clicking outside
    if (deleteAllModal) {
        deleteAllModal.addEventListener('click', function(e) {
            if (e.target === deleteAllModal || e.target.classList.contains('modal-overlay')) {
                deleteAllModal.classList.add('hidden');
            }
        });
    }
});

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

function searchAllProducts(searchTerm) {
    const searchStatus = document.getElementById('search-status');
    const searchResultCount = document.getElementById('search-result-count');
    const tbody = document.getElementById('products-tbody');
    const pagination = document.getElementById('pagination-container');
    
    if (!tbody) return;
    
    tbody.innerHTML = `<tr><td colspan="7" class="px-4 py-8 text-center text-gray-500"><i class="fas fa-spinner fa-spin text-3xl mb-2 text-emerald-500"></i><p>Inatafuta "${searchTerm}" kwenye bidhaa zote...</p></td></tr>`;
    
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
                searchResultCount.innerHTML = `<div class="bg-emerald-50 p-2 rounded"><span class="font-bold text-emerald-700">${data.data.length}</span> <span class="text-gray-600">bidhaa zinaonyeshwa</span><button onclick="clearSearch()" class="ml-2 text-xs bg-emerald-600 text-white px-2 py-1 rounded hover:bg-emerald-700"><i class="fas fa-times mr-1"></i> Ondoa</button></div>`;
            }
            if (pagination) pagination.classList.add('hidden');
            attachProductEvents();
        } else {
            tbody.innerHTML = `<tr><td colspan="7" class="px-4 py-8 text-center text-gray-500"><i class="fas fa-search text-3xl mb-2 text-gray-300"></i><p>Hakuna bidhaa zinazolingana na "${searchTerm}"</p></td></tr>`;
            if (searchStatus && searchResultCount) {
                searchStatus.classList.remove('hidden');
                searchResultCount.innerHTML = `<div class="bg-amber-50 p-2 rounded"><span class="text-amber-700">Hakuna bidhaa zinazolingana na "${searchTerm}"</span><button onclick="clearSearch()" class="ml-2 text-xs bg-amber-600 text-white px-2 py-1 rounded hover:bg-amber-700"><i class="fas fa-times mr-1"></i> Ondoa</button></div>`;
            }
            if (pagination) pagination.classList.add('hidden');
        }
    })
    .catch(error => {
        console.error('Search error:', error);
        tbody.innerHTML = `<tr><td colspan="7" class="px-4 py-8 text-center text-red-500"><i class="fas fa-exclamation-triangle text-3xl mb-2"></i><p>Hitilafu ya utafutaji. Jaribu tena.</p></td></tr>`;
    });
}

/**
 * Create a product row for the table with enhanced image handling
 */
function createProductRow(product) {
    const row = document.createElement('tr');
    row.className = 'product-row hover:bg-gray-50';
    row.dataset.product = JSON.stringify(product);
    
    const idadi = parseFloat(product.idadi || 0);
    let stockClass = 'bg-emerald-100 text-emerald-800';
    if (idadi == 0) {
        stockClass = 'bg-gray-100 text-gray-800';
    } else if (idadi < 10) {
        stockClass = 'bg-amber-100 text-amber-800';
    }
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
    if (isBoss || canEditDelete) {
        actionButtons = `<div class="flex justify-center space-x-2">
            <button class="edit-product-btn text-emerald-600 hover:text-emerald-800" data-id="${product.id}" title="Badili">
                <i class="fas fa-edit"></i>
            </button>
            <button class="delete-product-btn text-red-600 hover:text-red-800" data-id="${product.id}" data-name="${(product.jina || '').replace(/'/g, "\\'")}" title="Futa">
                <i class="fas fa-trash"></i>
            </button>
        </div>`;
    } else {
        actionButtons = `<span class="text-gray-400 cursor-not-allowed" title="Huwezi kurekebisha au kufuta">
            <i class="fas fa-lock mr-1"></i> Hakuna ruhusa
        </span>`;
    }
    
    const wholesaleHtml = product.bei_uzo_jumla ? 
        `<div><span class="text-xs text-gray-500">Jumla:</span><span class="text-sm font-bold text-blue-700">${parseFloat(product.bei_uzo_jumla).toLocaleString()} TZS</span></div>` : '';
    
    // Get image source using the helper function
    const imageSrc = getProductImageSrc(product);
    const hasImage = productHasImage(product);
    
    // Build image HTML
    let imageHtml = '';
    if (hasImage && imageSrc) {
        const escapedName = (product.jina || '').replace(/'/g, "\\'");
        imageHtml = `<img src="${imageSrc}" 
            alt="${product.jina || ''}" 
            class="h-10 w-10 object-cover rounded-full border border-gray-200 cursor-pointer hover:opacity-80" 
            onclick="event.stopPropagation(); showImageModal('${imageSrc}', '${escapedName}')">`;
    } else {
        imageHtml = `<div class="h-10 w-10 bg-gray-100 rounded-full flex items-center justify-center mx-auto">
            <i class="fas fa-image text-gray-400 text-sm"></i>
        </div>`;
    }
    
    // Image size display
    const imageSizeHtml = product.formatted_image_size ? 
        `<div class="text-xs text-gray-400 truncate">${product.formatted_image_size}</div>` : '';
    
    row.innerHTML = `
        <td class="px-3 py-2 text-center align-middle">${imageHtml}</td>
        <td class="px-3 py-2 align-middle">
            <div class="flex items-center">
                <div class="min-w-0">
                    <div class="font-medium text-gray-900 text-sm truncate" title="${product.jina || ''}">${product.jina || ''}</div>
                    <div class="text-xs text-gray-500 sm:hidden truncate">${product.aina || ''}</div>
                    ${product.barcode ? `<div class="text-xs text-emerald-600 font-mono">#${product.barcode}</div>` : ''}
                    ${imageSizeHtml}
                </div>
            </div>
        </td>
        <td class="px-3 py-2 hidden sm:table-cell">
            <span class="text-sm text-gray-700">${product.aina || ''}</span>
            ${product.kipimo ? `<span class="text-xs text-gray-500 block">${product.kipimo}</span>` : ''}
        </td>
        <td class="px-3 py-2 text-center">
            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium ${stockClass}">${formattedIdadi}</span>
        </td>
        <td class="px-3 py-2 text-right">
            <div class="space-y-1">
                <div>
                    <span class="text-xs text-gray-500">Rejareja:</span>
                    <span class="text-sm font-bold text-emerald-700">${parseFloat(product.bei_kuuza || 0).toLocaleString()} TZS</span>
                </div>
                ${wholesaleHtml}
            </div>
            ${canViewPrice ? `<div class="text-xs text-gray-500 mt-1">Nunua: ${parseFloat(product.bei_nunua || 0).toLocaleString()} TZS</div>` : ''}
        </td>
        <td class="px-3 py-2 hidden lg:table-cell">${expiryHtml}</td>
        <td class="px-3 py-2 text-center print:hidden">${actionButtons}</td>
    `;
    return row;
}

function clearSearch() {
    const searchInput = document.getElementById('search-input');
    const clearSearchBtn = document.getElementById('clear-search');
    if (searchInput) {
        searchInput.value = '';
        currentSearchTerm = '';
    }
    if (clearSearchBtn) clearSearchBtn.classList.add('hidden');
    resetToOriginalProducts();
    isSearchActive = false;
    allSearchResults = [];
    document.getElementById('search-status')?.classList.add('hidden');
    const pagination = document.getElementById('pagination-container');
    if (pagination) pagination.classList.remove('hidden');
}

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

function handleEditClick(e) {
    e.preventDefault();
    const productId = this.dataset.id;
    if (!isBoss && !canEditDelete) {
        showNotification('Huna ruhusa ya kurekebisha bidhaa', 'error');
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

function handleDeleteClick(e) {
    e.preventDefault();
    const productId = this.dataset.id;
    const productName = this.dataset.name;
    if (!isBoss && !canEditDelete) {
        showNotification('Huna ruhusa ya kufuta bidhaa', 'error');
        return;
    }
    deleteProduct(productId, productName);
}

/**
 * Edit product with enhanced image handling
 */
function editProduct(product) {
    if (!isBoss && !canEditDelete) return;
    
    document.getElementById('edit-jina').value = product.jina || '';
    document.getElementById('edit-aina').value = product.aina || '';
    document.getElementById('edit-kipimo').value = product.kipimo || '';
    
    const quantityInput = document.getElementById('edit-idadi');
    if (quantityInput) {
        const rawQuantity = product.idadi !== null && product.idadi !== undefined ? parseFloat(product.idadi) : 0;
        quantityInput.value = rawQuantity.toFixed(2);
    }
    
    document.getElementById('edit-bei-nunua').value = parseFloat(product.bei_nunua || 0).toFixed(2);
    document.getElementById('edit-bei-kuuza').value = parseFloat(product.bei_kuuza || 0).toFixed(2);
    
    const wholesalePrice = product.bei_uzo_jumla ? parseFloat(product.bei_uzo_jumla).toFixed(2) : '';
    document.getElementById('edit-bei-uzo-jumla').value = wholesalePrice;
    document.getElementById('edit-bei-kiasi-cha-chaguo').value = product.bei_kiasi_cha_chaguo || 'rejareja';
    document.getElementById('edit-expiry').value = product.expiry || '';
    document.getElementById('edit-barcode').value = product.barcode || '';
    document.getElementById('edit-form').action = `/bidhaa/${product.id}`;
    
    // Enhanced image handling in edit modal
    const currentImageContainer = document.getElementById('current-image-container');
    const currentImage = document.getElementById('current-product-image');
    const imageSizeDisplay = document.getElementById('current-image-size');
    
    // Check if product has image using the helper function
    const hasImage = productHasImage(product);
    const imageSrc = getProductImageSrc(product);
    
    if (hasImage && imageSrc) {
        if (currentImageContainer && currentImage) {
            currentImage.src = imageSrc;
            currentImageContainer.classList.remove('hidden');
            
            // Display image size if available
            if (imageSizeDisplay) {
                if (product.formatted_image_size) {
                    imageSizeDisplay.textContent = 'Ukubwa: ' + product.formatted_image_size;
                } else if (product.image_size) {
                    imageSizeDisplay.textContent = 'Ukubwa: ' + formatFileSize(product.image_size);
                } else {
                    imageSizeDisplay.textContent = '';
                }
            }
        }
    } else {
        if (currentImageContainer) {
            currentImageContainer.classList.add('hidden');
        }
    }
    
    // Add delete image button handler
    const deleteImageBtn = document.getElementById('delete-image-btn');
    if (deleteImageBtn) {
        deleteImageBtn.onclick = function() {
            if (confirm('Una uhakika unataka kufuta picha hii?')) {
                deleteProductImage(product.id);
            }
        };
    }
    
    document.getElementById('edit-image-preview')?.classList.add('hidden');
    document.getElementById('edit-modal').classList.remove('hidden');
}

function deleteProductImage(productId) {
    fetch(`/bidhaa/${productId}/delete-image`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Picha imefutwa kikamilifu', 'success');
            document.getElementById('current-image-container').classList.add('hidden');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showNotification(data.message || 'Hitilafu imetokea', 'error');
        }
    })
    .catch(error => {
        showNotification('Hitilafu ya mtandao', 'error');
    });
}

function loadAndEditProduct(productId) {
    if (!isBoss && !canEditDelete) {
        showNotification('Hurumia, wewe huna ruhusa ya kurekebisha bidhaa', 'error');
        return;
    }
    
    fetch(`/bidhaa/${productId}/edit-product`, {
        headers: { 
            'X-Requested-With': 'XMLHttpRequest', 
            'Accept': 'application/json' 
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.data.idadi !== undefined) {
                data.data.idadi = parseFloat(data.data.idadi).toFixed(2);
            }
            editProduct(data.data);
        } else {
            showNotification('Imeshindwa kupakua taarifa', 'error');
        }
    })
    .catch(error => {
        console.error('Error loading product:', error);
        showNotification('Hitilafu ya mtandao', 'error');
    });
}

function deleteProduct(productId, productName) {
    if (!isBoss && !canEditDelete) return;
    document.getElementById('delete-product-name').textContent = productName;
    document.getElementById('delete-form').action = `/bidhaa/${productId}`;
    document.getElementById('delete-modal').classList.remove('hidden');
}

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
                showNotification(data.data?.successCount > 0 ? `Bidhaa ${data.data.successCount} zimeongezwa!` : (data.message || 'Upakiaji umekamilika'), data.success ? 'success' : 'warning');
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

function updateProgress(percent, status) {
    const bar = document.getElementById('progress-bar');
    const percentage = document.getElementById('progress-percentage');
    const statusEl = document.getElementById('upload-status');
    if (bar) bar.style.width = `${percent}%`;
    if (percentage) percentage.textContent = `${percent}%`;
    if (statusEl) statusEl.textContent = status;
}

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
            errorItems.innerHTML = data.errors.slice(0, 20).map(err => `<div class="flex items-start border-b border-red-100 pb-1 mb-1"><i class="fas fa-times text-red-500 mr-1 mt-0.5 text-xs"></i><span>${err}</span></div>`).join('');
            if (data.errors.length > 20) errorItems.innerHTML += `<div class="text-red-400 text-xs text-center pt-1">+ ${data.errors.length - 20} zaidi...</div>`;
        }
    } else {
        if (errorList) errorList.classList.add('hidden');
    }
    resultsDiv.classList.remove('hidden');
}

function showNotification(message, type = 'info') {
    const container = document.getElementById('notification-container');
    if (!container) return;
    const colors = { success: 'bg-emerald-50 border-emerald-200 text-emerald-800', error: 'bg-red-50 border-red-200 text-red-800', warning: 'bg-amber-50 border-amber-200 text-amber-800', info: 'bg-blue-50 border-blue-200 text-blue-800' };
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

function printCurrentView() {
    let products = [];
    if (isSearchActive && allSearchResults.length > 0) {
        products = allSearchResults;
    } else {
        const rows = document.querySelectorAll('.product-row');
        rows.forEach(row => {
            try {
                if (row.dataset.product) products.push(JSON.parse(row.dataset.product));
            } catch (e) { console.error('Error parsing', e); }
        });
    }
    if (products.length === 0) {
        showNotification('Hakuna bidhaa za kuchapisha', 'warning');
        return;
    }
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`<html><head><title>Orodha ya Bidhaa - ${new Date().toLocaleDateString()}</title><style>body{font-family:Arial,sans-serif;margin:20px;}table{width:100%;border-collapse:collapse;margin-top:20px;}th,td{border:1px solid #ddd;padding:8px;text-align:left;}th{background-color:#f3f4f6;}.header{text-align:center;margin-bottom:30px;}.header h2{margin:0;color:#047857;}</style></head><body><div class="header"><h2>Orodha ya Bidhaa</h2><p>Tarehe: ${new Date().toLocaleDateString()} | Jumla: ${products.length}</p>${isSearchActive ? `<p>Matokeo ya utafutaji: "${currentSearchTerm}"</p>` : ''}</div><table><thead><tr><th>#</th><th>Bidhaa</th><th>Aina</th><th>Idadi</th><th>Bei Rejareja</th><th>Bei Jumla</th><th>Expiry</th></tr></thead><tbody>${products.map((p, i) => `<tr><td>${i+1}</td><td>${p.jina||''}</td><td>${p.aina||''}</td><td class="text-center">${parseFloat(p.idadi||0).toFixed(2)}</td><td>${parseFloat(p.bei_kuuza||0).toLocaleString()} TZS</td><td>${p.bei_uzo_jumla ? parseFloat(p.bei_uzo_jumla).toLocaleString() + ' TZS' : '--'}</td><td>${p.expiry ? new Date(p.expiry).toLocaleDateString() : '--'}</td></tr>`).join('')}</tbody>}</table></body></html>`);
    printWindow.document.close();
    printWindow.print();
}

function exportPDF() {
    const url = new URL(window.location.href);
    const params = new URLSearchParams(url.search);
    params.delete('page');
    params.delete('per_page');
    params.set('export', 'pdf');
    window.location.href = `${url.pathname}?${params.toString()}`;
}

function exportExcel() {
    const url = new URL(window.location.href);
    const params = new URLSearchParams(url.search);
    params.delete('page');
    params.delete('per_page');
    params.set('export', 'excel');
    window.location.href = `${url.pathname}?${params.toString()}`;
}

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
    document.querySelectorAll('.tab-content').forEach(content => content.classList.add('hidden'));
    const tabContent = document.getElementById(`${tabName}-tab-content`);
    if (tabContent) tabContent.classList.remove('hidden');
}

// Taarifa tab functions
function initializeSmartSearch() {
    const searchInput = document.getElementById('product-search-input');
    const resultsDropdown = document.getElementById('search-results-dropdown');
    const searchSpinner = document.getElementById('search-spinner');
    if (!searchInput) return;
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.trim();
        clearTimeout(taarifaSearchTimeout);
        if (searchTerm.length < 2) {
            resultsDropdown.classList.add('hidden');
            resultsDropdown.innerHTML = '';
            return;
        }
        searchSpinner.classList.remove('hidden');
        taarifaSearchTimeout = setTimeout(() => {
            searchAllProductsForTaarifa(searchTerm);
        }, 300);
    });
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !resultsDropdown.contains(e.target)) {
            resultsDropdown.classList.add('hidden');
        }
    });
}

function searchAllProductsForTaarifa(searchTerm) {
    const resultsDropdown = document.getElementById('search-results-dropdown');
    const searchSpinner = document.getElementById('search-spinner');
    fetch(`/bidhaa/search-products?q=${encodeURIComponent(searchTerm)}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        searchSpinner.classList.add('hidden');
        if (data.success && data.data.length > 0) {
            displaySearchResultsForTaarifa(data.data);
        } else {
            resultsDropdown.innerHTML = `<div class="p-4 text-center text-gray-500"><i class="fas fa-search text-2xl mb-2 text-gray-300"></i><p>Hakuna bidhaa inayolingana na "${searchTerm}"</p></div>`;
            resultsDropdown.classList.remove('hidden');
        }
    })
    .catch(error => {
        searchSpinner.classList.add('hidden');
        resultsDropdown.innerHTML = `<div class="p-4 text-center text-red-500"><i class="fas fa-exclamation-triangle text-2xl mb-2"></i><p>Hitilafu ya utafutaji</p></div>`;
        resultsDropdown.classList.remove('hidden');
    });
}

function displaySearchResultsForTaarifa(products) {
    const resultsDropdown = document.getElementById('search-results-dropdown');
    let html = '';
    products.forEach(product => {
        const stockClass = product.idadi <= 0 ? 'text-red-600' : (product.idadi < 10 ? 'text-amber-600' : 'text-emerald-600');
        // Show image in search results
        const imageSrc = product.image_url || product.image_base64 || '';
        const imageHtml = imageSrc ? 
            `<img src="${imageSrc}" class="h-8 w-8 object-cover rounded-full border border-gray-200 mr-2">` : 
            `<div class="h-8 w-8 bg-gray-100 rounded-full flex items-center justify-center mr-2"><i class="fas fa-image text-gray-400 text-xs"></i></div>`;
        
        html += `<div class="search-result-item p-3 border-b border-gray-100 hover:bg-emerald-50 cursor-pointer focus:bg-emerald-50 focus:outline-none flex items-center" tabindex="0" 
            data-id="${product.id}" 
            data-jina="${product.jina}" 
            data-aina="${product.aina}" 
            data-barcode="${product.barcode || ''}" 
            data-idadi="${product.idadi}">
            ${imageHtml}
            <div class="flex-1">
                <div class="font-medium text-gray-900 text-sm">${product.jina}</div>
                <div class="text-xs text-gray-600">${product.aina} ${product.barcode ? `#${product.barcode}` : ''}</div>
            </div>
            <div class="text-right">
                <span class="text-xs font-medium ${stockClass}">${formatNumber(product.idadi, 2)}</span>
            </div>
        </div>`;
    });
    resultsDropdown.innerHTML = html;
    resultsDropdown.classList.remove('hidden');
    document.querySelectorAll('.search-result-item').forEach(item => {
        item.addEventListener('click', function() { selectProductForTaarifa(this); });
        item.addEventListener('keydown', function(e) { if (e.key === 'Enter') selectProductForTaarifa(this); });
    });
}

function selectProductForTaarifa(element) {
    const productId = element.dataset.id;
    const productJina = element.dataset.jina;
    const productAina = element.dataset.aina;
    const productBarcode = element.dataset.barcode;
    const productIdadi = element.dataset.idadi;
    selectedProductId = productId;
    document.getElementById('product-search-input').value = productJina;
    document.getElementById('search-results-dropdown').classList.add('hidden');
    document.getElementById('selected-product-info').classList.remove('hidden');
    document.getElementById('selected-product-name').textContent = productJina;
    document.getElementById('selected-product-details').innerHTML = `${productAina} | Idadi: ${formatNumber(productIdadi, 2)} ${productBarcode ? '| #' + productBarcode : ''}`;
    loadProductDetails(productId);
}

function clearSelectedProduct() {
    selectedProductId = null;
    document.getElementById('product-search-input').value = '';
    document.getElementById('selected-product-info').classList.add('hidden');
    document.getElementById('product-details-container').classList.add('hidden');
    document.getElementById('no-product-selected').classList.remove('hidden');
    const url = new URL(window.location);
    url.searchParams.delete('product_id');
    window.history.pushState({}, '', url);
}

function loadProductDetails(productId) {
    const loadingEl = document.getElementById('details-loading');
    const container = document.getElementById('product-details-container');
    const noProduct = document.getElementById('no-product-selected');
    const startDate = document.getElementById('start-date').value;
    const endDate = document.getElementById('end-date').value;
    let url = `/bidhaa/taarifa?bidhaa_id=${productId}`;
    if (startDate) url += `&start_date=${startDate}`;
    if (endDate) url += `&end_date=${endDate}`;
    loadingEl.classList.remove('hidden');
    container.classList.add('hidden');
    noProduct.classList.add('hidden');
    fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        loadingEl.classList.add('hidden');
        if (data.success) {
            displayProductDetails(data.data);
            container.classList.remove('hidden');
            const url = new URL(window.location);
            url.searchParams.set('product_id', productId);
            window.history.pushState({}, '', url);
        } else {
            showNotification(data.message || 'Hitilafu', 'error');
            noProduct.classList.remove('hidden');
        }
    })
    .catch(error => {
        loadingEl.classList.add('hidden');
        noProduct.classList.remove('hidden');
        showNotification('Hitilafu ya mtandao', 'error');
    });
}

function displayProductDetails(data) {
    const p = data.bidhaa;
    const stats = data.statistics;
    document.getElementById('detail-jina').textContent = p.jina || '-';
    document.getElementById('detail-aina').textContent = p.aina || '-';
    document.getElementById('detail-kipimo').textContent = p.kipimo ? `Kipimo: ${p.kipimo}` : '';
    document.getElementById('detail-barcode').textContent = p.barcode ? `Barcode: #${p.barcode}` : '';
    document.getElementById('stat-idadi-sasa').textContent = formatNumber(p.idadi_sasa, 2);
    document.getElementById('stat-jumla-ingizo').textContent = formatNumber(stats.jumlah_iliyoingizwa, 2);
    document.getElementById('stat-jumla-mauzo').textContent = formatNumber(stats.jumlah_mauzo_jumla, 2);
    document.getElementById('stat-zilizobaki').textContent = formatNumber(p.idadi_sasa, 2);
    document.getElementById('history-count').textContent = `Jumla: ${data.total_transactions || 0}`;
    if (data.histories && data.histories.length > 0) {
        document.getElementById('history-tbody').innerHTML = data.histories.map(h => {
            let badgeClass = 'bg-gray-100 text-gray-800';
            let typeText = h.aina;
            if (h.aina === 'manunuzi') { badgeClass = 'bg-emerald-100 text-emerald-800'; typeText = 'Manunuzi'; }
            else if (h.aina === 'mauzo') { badgeClass = 'bg-blue-100 text-blue-800'; typeText = 'Mauzo (Cash)'; }
            else if (h.aina === 'kopesha') { badgeClass = 'bg-amber-100 text-amber-800'; typeText = 'Kopesha'; }
            return `<tr><td class="px-3 py-2 text-xs">${h.tarehe}</td><td class="px-3 py-2"><span class="px-2 py-0.5 rounded text-xs font-medium ${badgeClass}">${typeText}</span></td><td class="px-3 py-2 text-right text-xs">${h.idadi_iliyoingizwa > 0 ? formatNumber(h.idadi_iliyoingizwa, 2) : '-'}</td><td class="px-3 py-2 text-right text-xs">${h.idadi_iliyouzwa > 0 ? formatNumber(h.idadi_iliyouzwa, 2) : '-'}</td><td class="px-3 py-2 text-right text-xs font-medium">${formatNumber(h.idadi_iliyobaki, 2)}</td><td class="px-3 py-2 text-xs">${h.maelezo || '-'}</td></tr>`;
        }).join('');
    } else {
        document.getElementById('history-tbody').innerHTML = `<tr><td colspan="6" class="px-3 py-4 text-center text-gray-500">Hakuna historia</td></tr>`;
    }
}

// Delete All Products
function confirmDeleteAll() {
    const totalProducts = {{ $totalProducts }} || 0;
    
    if (totalProducts === 0) {
        showNotification('Hakuna bidhaa za kufuta', 'warning');
        return;
    }
    
    document.getElementById('delete-all-count').textContent = totalProducts;
    document.getElementById('delete-all-modal').classList.remove('hidden');
}

function printProductDetails() {
    const container = document.getElementById('product-details-container');
    if (!container || container.classList.contains('hidden')) {
        showNotification('Hakuna bidhaa iliyochaguliwa', 'warning');
        return;
    }
    const jina = document.getElementById('detail-jina').textContent;
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`<html><head><title>Taarifa za ${jina}</title><style>body{font-family:Arial;margin:20px;}h1{color:#047857;}table{width:100%;border-collapse:collapse;}th,td{border:1px solid #ddd;padding:8px;text-align:left;}th{background:#f3f4f6;}.stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin:20px 0;}.stat-card{border:1px solid #ddd;padding:10px;border-radius:5px;}</style></head><body><div class="header"><h1>Taarifa za Bidhaa</h1><p>Tarehe: ${new Date().toLocaleDateString()}</p></div><h2>${document.getElementById('detail-jina').textContent}</h2><p>Aina: ${document.getElementById('detail-aina').textContent}</p><div class="stats-grid"><div class="stat-card"><div>Idadi Iliyopo Sasa</div><div><b>${document.getElementById('stat-idadi-sasa').textContent}</b></div></div><div class="stat-card"><div>Jumla Iliyoingizwa</div><div><b>${document.getElementById('stat-jumla-ingizo').textContent}</b></div></div><div class="stat-card"><div>Jumla Iliyouzwa</div><div><b>${document.getElementById('stat-jumla-mauzo').textContent}</b></div></div><div class="stat-card"><div>Zilizobaki</div><div><b>${document.getElementById('stat-zilizobaki').textContent}</b></div></div></div><h3>Historia</h3><table><thead><tr><th>Tarehe</th><th>Aina</th><th>Iliyoingizwa</th><th>Iliyouzwa</th><th>Iliyobaki</th><th>Maelezo</th></tr></thead><tbody>${Array.from(document.querySelectorAll('#history-tbody tr')).map(row => row.outerHTML).join('')}</tbody></table></body></html>`);
    printWindow.document.close();
    printWindow.print();
}

// ===== SHOWCASE LINK GENERATION =====
let currentShowcaseLink = '';
let currentQRCodeData = '';

function generateShowcaseLink() {
    const companyId = getCompanyId();
    
    if (!companyId) {
        showNotification('Company ID not found. Please refresh the page.', 'error');
        return;
    }
    
    const baseUrl = window.location.origin;
    currentShowcaseLink = `${baseUrl}/shop/${companyId}`;
    
    document.getElementById('showcaseLinkInput').value = currentShowcaseLink;
    document.getElementById('showcaseLinkContainer').classList.remove('hidden');
    
    const container = document.getElementById('showcaseLinkContainer');
    container.style.animation = 'fadeIn 0.5s ease';
    
    showNotification('Showcase link generated successfully!', 'success');
}

function getCompanyId() {
    const appContainer = document.getElementById('app-container');
    if (appContainer && appContainer.dataset.companyId) {
        return appContainer.dataset.companyId;
    }
    
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('company')) {
        return urlParams.get('company');
    }
    
    const metaTag = document.querySelector('meta[name="company-id"]');
    if (metaTag) {
        return metaTag.content;
    }
    
    const companyIdElement = document.getElementById('company-id-data');
    if (companyIdElement) {
        return companyIdElement.value;
    }
    
    return 1;
}

function copyShowcaseLink() {
    const input = document.getElementById('showcaseLinkInput');
    input.select();
    input.setSelectionRange(0, 99999);
    
    try {
        navigator.clipboard.writeText(input.value);
        showNotification('Link copied to clipboard!', 'success');
    } catch (err) {
        document.execCommand('copy');
        showNotification('Link copied to clipboard!', 'success');
    }
}

function openShowcaseLink() {
    if (currentShowcaseLink) {
        window.open(currentShowcaseLink, '_blank');
    } else {
        showNotification('Please generate a link first.', 'warning');
    }
}

function shareOnWhatsApp() {
    if (!currentShowcaseLink) {
        showNotification('Please generate a link first.', 'warning');
        return;
    }
    
    const companyName = getCompanyName();
    const message = `🛍️ *${companyName}* - Order Online\n\nBrowse our products and place your order easily:\n${currentShowcaseLink}\n\n📱 Scan or click the link to start shopping!`;
    const encodedMessage = encodeURIComponent(message);
    const whatsappUrl = `https://wa.me/?text=${encodedMessage}`;
    window.open(whatsappUrl, '_blank');
}

function shareViaEmail() {
    if (!currentShowcaseLink) {
        showNotification('Please generate a link first.', 'warning');
        return;
    }
    
    const companyName = getCompanyName();
    const subject = encodeURIComponent(`Order from ${companyName}`);
    const body = encodeURIComponent(
        `Hello,\n\nYou can now browse and order products from ${companyName} using the link below:\n\n${currentShowcaseLink}\n\nThank you!`
    );
    window.location.href = `mailto:?subject=${subject}&body=${body}`;
}

function printShowcaseLink() {
    if (!currentShowcaseLink) {
        showNotification('Please generate a link first.', 'warning');
        return;
    }
    
    const companyName = getCompanyName();
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
        <head>
            <title>Showcase Link - ${companyName}</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 40px; text-align: center; }
                .container { max-width: 500px; margin: 0 auto; border: 2px solid #10b981; border-radius: 12px; padding: 30px; }
                .logo { font-size: 48px; color: #10b981; margin-bottom: 10px; }
                h2 { color: #1f2937; margin-bottom: 5px; }
                .link { background: #f3f4f6; padding: 12px; border-radius: 8px; margin: 15px 0; word-break: break-all; color: #059669; font-weight: 600; }
                .qr { margin: 15px 0; padding: 10px; background: white; border: 1px solid #e5e7eb; border-radius: 8px; }
                .footer { color: #6b7280; font-size: 12px; margin-top: 15px; border-top: 1px solid #e5e7eb; padding-top: 15px; }
                .btn { display: inline-block; background: #10b981; color: white; padding: 8px 20px; border-radius: 6px; text-decoration: none; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="logo">🛍️</div>
                <h2>${companyName}</h2>
                <p style="color:#6b7280;">Customer Showcase Page</p>
                <div class="link">${currentShowcaseLink}</div>
                <div class="qr">
                    <p style="font-size:14px;color:#6b7280;">Scan QR to open</p>
                    <div style="display:flex;justify-content:center;margin:10px 0;" id="qrPrintPlaceholder">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${encodeURIComponent(currentShowcaseLink)}" alt="QR Code">
                    </div>
                </div>
                <p style="font-size:14px;color:#6b7280;">Share this link with customers to browse and order products</p>
                <div class="footer">
                    Powered by Mauzo Sheet &bull; ${new Date().toLocaleDateString()}
                </div>
            </div>
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

function getCompanyName() {
    const companyNameElement = document.getElementById('company-name-data');
    if (companyNameElement) {
        return companyNameElement.value;
    }
    
    const titleElement = document.querySelector('title');
    if (titleElement) {
        const title = titleElement.textContent;
        if (title && !title.includes('Bidhaa')) {
            return title;
        }
    }
    
    return 'Our Shop';
}

function generateQRCode() {
    if (!currentShowcaseLink) {
        generateShowcaseLink();
        setTimeout(() => {
            generateQRCodeActual();
        }, 500);
    } else {
        generateQRCodeActual();
    }
}

function generateQRCodeActual() {
    const container = document.getElementById('qrCodeContainer');
    const display = document.getElementById('qrCodeDisplay');
    
    const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${encodeURIComponent(currentShowcaseLink)}&bgcolor=ffffff&color=059669&margin=10`;
    
    display.innerHTML = `<img src="${qrUrl}" alt="QR Code" class="w-48 h-48 object-contain" style="max-width:200px;height:200px;" id="qrImage">`;
    container.classList.remove('hidden');
    
    currentQRCodeData = qrUrl;
    
    showNotification('QR Code generated successfully!', 'success');
}

function downloadQRCode() {
    const qrImage = document.getElementById('qrImage');
    if (!qrImage) {
        showNotification('Please generate a QR code first.', 'warning');
        return;
    }
    
    const link = document.createElement('a');
    link.download = `showcase-qr-${getCompanyName().toLowerCase().replace(/\s/g, '-')}.png`;
    link.href = qrImage.src;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    showNotification('QR Code downloaded!', 'success');
}
</script>
@endpush