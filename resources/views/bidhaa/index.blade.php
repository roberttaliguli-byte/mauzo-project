@extends('layouts.app')

@section('title', 'Bidhaa - DEMODAY')

@section('page-title', 'Bidhaa')
@section('page-subtitle', 'Usimamizi wa bidhaa zote - ' . now()->format('d/m/Y'))

@section('content')
<div class="space-y-4 md:space-y-6">
    <!-- Statistics Cards - Responsive -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6">
        <div class="bg-white rounded-xl md:rounded-2xl shadow-lg border border-green-100 p-4 md:p-6 card-hover">
            <div class="flex items-center">
                <div class="p-2 md:p-3 rounded-lg bg-green-100 text-green-600 mr-3 md:mr-4">
                    <i class="fas fa-boxes text-lg md:text-xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs md:text-sm text-black-500 font-medium truncate">Jumla ya Bidhaa</p>
                    <h3 class="text-lg md:text-2xl font-bold text-gray-800">{{ $bidhaa->count() }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl md:rounded-2xl shadow-lg border border-green-100 p-4 md:p-6 card-hover">
            <div class="flex items-center">
                <div class="p-2 md:p-3 rounded-lg bg-emerald-100 text-emerald-600 mr-3 md:mr-4">
                    <i class="fas fa-cubes text-lg md:text-xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs md:text-sm text-black-500 font-medium truncate">Bidhaa Zilizopo</p>
                    <h3 class="text-lg md:text-2xl font-bold text-black-800">{{ $bidhaa->where('idadi', '>', 0)->count() }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl md:rounded-2xl shadow-lg border border-green-100 p-4 md:p-6 card-hover">
            <div class="flex items-center">
                <div class="p-2 md:p-3 rounded-lg bg-amber-100 text-amber-600 mr-3 md:mr-4">
                    <i class="fas fa-exclamation-triangle text-lg md:text-xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs md:text-sm text-black-500 font-medium truncate">Zinazokaribia Kuisha</p>
                    <h3 class="text-lg md:text-2xl font-bold text-black-800">{{ $bidhaa->where('idadi', '<', 10)->count() }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl md:rounded-2xl shadow-lg border border-green-100 p-4 md:p-6 card-hover">
            <div class="flex items-center">
                <div class="p-2 md:p-3 rounded-lg bg-red-100 text-red-600 mr-3 md:mr-4">
                    <i class="fas fa-calendar-times text-lg md:text-xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs md:text-sm text-black-500 font-medium truncate">Zilizo Expire</p>
                    <h3 class="text-lg md:text-2xl font-bold text-black-800">{{ $bidhaa->where('expiry', '<', now())->count() }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Page Navigation Tabs - Mobile Optimized -->
    <div class="bg-white rounded-xl md:rounded-2xl shadow-lg border border-gray-100 p-2 md:p-4 card-hover">
        <div class="flex space-x-1 md:space-x-6 overflow-x-auto scrollbar-hide">
            <button 
                id="taarifa-tab" 
                class="tab-button flex-shrink-0 pb-2 md:pb-3 px-2 md:px-4 transition-colors flex items-center border-b-2 border-green-500 text-green-600 font-medium md:font-semibold whitespace-nowrap"
                data-tab="taarifa"
            >
                <i class="fas fa-table mr-1 md:mr-2 text-sm md:text-base"></i>
                <span class="text-xs md:text-sm">Taarifa za Bidhaa</span>
            </button>
            <button 
                id="ingiza-tab" 
                class="tab-button flex-shrink-0 pb-2 md:pb-3 px-2 md:px-4 transition-colors flex items-center text-gray-500 hover:text-gray-700 whitespace-nowrap"
                data-tab="ingiza"
            >
                <i class="fas fa-plus-circle mr-1 md:mr-2 text-sm md:text-base"></i>
                <span class="text-xs md:text-sm">Ingiza Bidhaa Mpya</span>
            </button>
            <button 
                id="csv-tab" 
                class="tab-button flex-shrink-0 pb-2 md:pb-3 px-2 md:px-4 transition-colors flex items-center text-gray-500 hover:text-gray-700 whitespace-nowrap"
                data-tab="csv"
            >
                <i class="fas fa-file-csv mr-1 md:mr-2 text-sm md:text-base"></i>
                <span class="text-xs md:text-sm">Ingiza kwa CSV</span>
            </button>
        </div>
    </div>

    <!-- Flash Messages - Responsive -->
    @if(session('success'))
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-300 rounded-xl p-3 md:p-4">
            <div class="flex items-start md:items-center">
                <div class="p-1 md:p-2 rounded-lg bg-green-100 text-green-600 mr-2 md:mr-3 flex-shrink-0">
                    <i class="fas fa-check-circle text-sm md:text-base"></i>
                </div>
                <div class="flex-1">
                    <p class="text-green-800 font-medium text-xs md:text-sm">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-gradient-to-r from-red-50 to-pink-50 border border-red-300 rounded-xl p-3 md:p-4">
            <div class="flex items-start md:items-center">
                <div class="p-1 md:p-2 rounded-lg bg-red-100 text-red-600 mr-2 md:mr-3 flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-sm md:text-base"></i>
                </div>
                <div class="flex-1">
                    <h4 class="text-red-800 font-medium text-xs md:text-sm">Hitilafu katika Uwasilishaji</h4>
                    <ul class="text-red-700 mt-1 space-y-1">
                        @foreach($errors->all() as $error)
                            <li class="text-xs md:text-sm">• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

<!-- TAB 1: Taarifa za Bidhaa -->
<div id="taarifa-tab-content" class="space-y-4 lg:space-y-6 tab-content">
    <!-- Search and Actions - Mobile Optimized -->
    <div class="bg-gray-200 rounded-xl lg:rounded-2xl shadow-lg border border-gray-100 p-4 lg:p-6 card-hover">
        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-4 lg:mb-6 space-y-4 lg:space-y-0">
            <h2 class="text-base lg:text-lg xl:text-xl font-bold text-gray-800">Orodha ya Bidhaa</h2>
            <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3">
                <div class="relative">
                    <input 
                        type="text" 
                        id="search-input"
                        placeholder="Tafuta bidhaa..." 
                        class="w-full pl-10 pr-4 py-2 lg:py-2 text-sm lg:text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400 text-sm lg:text-base"></i>
                    </div>
                </div>
                <button 
                    onclick="window.print()" 
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center text-sm lg:text-base"
                >
                    <i class="fas fa-print mr-1 lg:mr-2 text-sm lg:text-base"></i>
                    <span>Print</span>
                </button>
            </div>
        </div>

        <!-- Data Table - Responsive -->
        <div class="overflow-x-auto -mx-2 lg:mx-0">
            <table class="w-full min-w-full table-auto">
                <thead>
                    <tr class="bg-gradient-to-r from-green-400 to-green-700">
                        <th class="px-2 lg:px-4 xl:px-6 py-2 lg:py-3 xl:py-4 text-left text-xs lg:text-sm font-semibold text-white">Bidhaa</th>
                        <th class="px-2 lg:px-4 xl:px-6 py-2 lg:py-3 xl:py-4 text-left text-xs lg:text-sm font-semibold text-white">Aina</th>
                        <th class="px-2 lg:px-4 xl:px-6 py-2 lg:py-3 xl:py-4 text-center text-xs lg:text-sm font-semibold text-white">Idadi</th>
                        <th class="px-2 lg:px-4 xl:px-6 py-2 lg:py-3 xl:py-4 text-right text-xs lg:text-sm font-semibold text-white">Bei</th>
                        <th class="px-2 lg:px-4 xl:px-6 py-2 lg:py-3 xl:py-4 text-left text-xs lg:text-sm font-semibold text-white">Expiry</th>
                        <th class="px-2 lg:px-4 xl:px-6 py-2 lg:py-3 xl:py-4 text-center text-xs lg:text-sm font-semibold text-white print:hidden">Vitendo</th>
                    </tr>
                </thead>
                <tbody id="products-tbody" class="divide-y divide-gray-200">
                    @forelse($bidhaa as $item)
                        <tr class="product-row hover:bg-green-50 transition-all duration-200 
                            @if($item->idadi < 10) bg-gradient-to-r from-amber-50 to-yellow-50 border-l-4 border-amber-400
                            @elseif($item->expiry < now()) bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-400
                            @else bg-white @endif"
                            data-product='@json($item)'>
                            
                            <td class="px-2 lg:px-4 xl:px-6 py-2 lg:py-3 xl:py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-6 w-6 lg:h-8 lg:w-8 xl:h-10 xl:w-10 bg-green-100 rounded-lg flex items-center justify-center text-green-800 font-semibold text-xs lg:text-sm xl:text-base">
                                        {{ substr($item->jina, 0, 1) }}
                                    </div>
                                    <div class="ml-2 lg:ml-3 xl:ml-4">
                                        <div class="text-xs lg:text-sm font-semibold text-gray-900 product-name truncate max-w-[80px] lg:max-w-[120px] xl:max-w-none">{{ $item->jina }}</div>
                                        @if($item->barcode)
                                        <div class="text-xs text-green-600 font-medium truncate max-w-[60px] lg:max-w-[100px] xl:max-w-none">#{{ $item->barcode }}</div>
                                        @endif
                                        <div class="text-xs text-gray-500 lg:hidden">{{ $item->kipimo ?: '--' }}</div>
                                    </div>
                                </div>
                            </td>
                            
                            <td class="px-2 lg:px-4 xl:px-6 py-2 lg:py-3 xl:py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 lg:py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 border border-green-200 product-type truncate max-w-[60px] lg:max-w-[100px] xl:max-w-none">
                                    {{ $item->aina }}
                                </span>
                            </td>
                            
                            <td class="px-2 lg:px-4 xl:px-6 py-2 lg:py-3 xl:py-4 whitespace-nowrap text-center">
                                <div class="text-xs lg:text-sm font-semibold 
                                    @if($item->idadi < 10) text-amber-700
                                    @elseif($item->idadi == 0) text-red-700
                                    @else text-green-700 @endif">
                                    {{ $item->idadi }}
                                </div>
                                @if($item->idadi < 10)
                                <div class="text-xs text-amber-600 font-medium hidden lg:block">Karibu Kwisha</div>
                                <div class="text-xs text-amber-600 font-medium lg:hidden">!</div>
                                @endif
                            </td>
                            
                            <td class="px-2 lg:px-4 xl:px-6 py-2 lg:py-3 xl:py-4 whitespace-nowrap text-right">
                                <div class="text-xs lg:text-sm font-bold text-green-700 truncate">{{ number_format($item->bei_kuuza, 0) }}</div>
                                <div class="text-xs text-gray-600 lg:hidden">Nunua: {{ number_format($item->bei_nunua, 0) }}</div>
                            </td>
                            
                            <td class="px-2 lg:px-4 xl:px-6 py-2 lg:py-3 xl:py-4 whitespace-nowrap">
                                <div class="text-xs lg:text-sm 
                                    @if($item->expiry < now()) text-red-700 font-semibold
                                    @elseif(\Carbon\Carbon::parse($item->expiry)->diffInDays(now()) < 30) text-amber-700
                                    @else text-gray-700 @endif truncate">
                                    {{ $item->expiry ? \Carbon\Carbon::parse($item->expiry)->format('d/m/Y') : '--' }}
                                </div>
                            </td>
                            
                            <td class="px-2 lg:px-4 xl:px-6 py-2 lg:py-3 xl:py-4 whitespace-nowrap text-xs lg:text-sm font-medium print:hidden">
                                <div class="flex justify-center space-x-1 lg:space-x-2 xl:space-x-3">
                                    <button 
                                        class="edit-product-btn text-amber-600 hover:text-amber-800 transition-colors transform hover:scale-110 p-0.5 lg:p-1 xl:p-0"
                                        title="Badili"
                                        data-id="{{ $item->id }}"
                                    >
                                        <i class="fas fa-edit text-xs lg:text-sm xl:text-base"></i>
                                    </button>
                                    <button 
                                        class="delete-product-btn text-red-500 hover:text-red-700 transition-colors transform hover:scale-110 p-0.5 lg:p-1 xl:p-0"
                                        title="Futa"
                                        data-id="{{ $item->id }}"
                                        data-name="{{ $item->jina }}"
                                    >
                                        <i class="fas fa-trash text-xs lg:text-sm xl:text-base"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 lg:px-6 xl:px-8 py-6 lg:py-8 xl:py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-boxes text-3xl lg:text-4xl xl:text-5xl text-green-300 mb-2 lg:mb-3 xl:mb-4"></i>
                                    <p class="text-sm lg:text-base xl:text-lg font-semibold text-gray-600 mb-1 lg:mb-2">Hakuna bidhaa bado.</p>
                                    <p class="text-xs lg:text-sm text-gray-500 mb-2 lg:mb-3 xl:mb-4">Anza kwa kuongeza bidhaa yako ya kwanza</p>
                                    <button 
                                        id="go-to-add-product"
                                        class="bg-gradient-to-r from-green-600 to-green-700 text-white px-3 lg:px-4 xl:px-6 py-1.5 lg:py-2 xl:py-3 rounded-lg hover:from-green-700 hover:to-green-800 transition-all duration-300 transform hover:scale-105 flex items-center shadow text-xs lg:text-sm xl:text-base"
                                    >
                                        <i class="fas fa-plus-circle mr-1 lg:mr-2 text-xs lg:text-sm xl:text-base"></i>
                                        <span>Ingiza Bidhaa ya Kwanza</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination - Responsive -->
        @if($bidhaa->hasPages())
        <div class="mt-4 lg:mt-6">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                <!-- Pagination Info -->
                <div class="text-xs lg:text-sm text-gray-600">
                    @php
                        $start = ($bidhaa->currentPage() - 1) * $bidhaa->perPage() + 1;
                        $end = min($bidhaa->currentPage() * $bidhaa->perPage(), $bidhaa->total());
                    @endphp
                    Onyesha {{ $start }} - {{ $end }} ya {{ $bidhaa->total() }} bidhaa
                </div>

                <!-- Pagination Links -->
                <nav class="flex items-center space-x-1">
                    <!-- Previous Button -->
                    @if($bidhaa->onFirstPage())
                        <span class="px-2 lg:px-3 py-1 lg:py-1.5 rounded-lg border text-gray-400 text-xs lg:text-sm cursor-not-allowed flex items-center">
                            <i class="fas fa-chevron-left mr-1 text-xs"></i>
                            <span class="hidden sm:inline">Nyuma</span>
                        </span>
                    @else
                        <a href="{{ $bidhaa->previousPageUrl() }}" class="px-2 lg:px-3 py-1 lg:py-1.5 rounded-lg border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs lg:text-sm transition flex items-center">
                            <i class="fas fa-chevron-left mr-1 text-xs"></i>
                            <span class="hidden sm:inline">Nyuma</span>
                        </a>
                    @endif

                    <!-- Page Numbers -->
                    <div class="flex items-center space-x-1">
                        @php
                            // Show limited page numbers on mobile, more on desktop
                            $maxPages = 5; // Show 5 page numbers
                            $current = $bidhaa->currentPage();
                            $last = $bidhaa->lastPage();
                            
                            if ($last <= $maxPages) {
                                $startPage = 1;
                                $endPage = $last;
                            } else {
                                $half = floor($maxPages / 2);
                                if ($current <= $half) {
                                    $startPage = 1;
                                    $endPage = $maxPages;
                                } elseif ($current >= ($last - $half)) {
                                    $startPage = $last - $maxPages + 1;
                                    $endPage = $last;
                                } else {
                                    $startPage = $current - $half;
                                    $endPage = $current + $half;
                                }
                            }
                        @endphp

                        @if($startPage > 1)
                            <a href="{{ $bidhaa->url(1) }}" class="px-2 lg:px-3 py-1 lg:py-1.5 rounded-lg border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs lg:text-sm transition">
                                1
                            </a>
                            @if($startPage > 2)
                                <span class="px-1 lg:px-2 text-gray-400 text-xs lg:text-sm">...</span>
                            @endif
                        @endif

                        @for($page = $startPage; $page <= $endPage; $page++)
                            @if($page == $bidhaa->currentPage())
                                <span class="px-2 lg:px-3 py-1 lg:py-1.5 rounded-lg bg-green-600 text-white font-semibold text-xs lg:text-sm">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $bidhaa->url($page) }}" class="px-2 lg:px-3 py-1 lg:py-1.5 rounded-lg border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs lg:text-sm transition">
                                    {{ $page }}
                                </a>
                            @endif
                        @endfor

                        @if($endPage < $last)
                            @if($endPage < $last - 1)
                                <span class="px-1 lg:px-2 text-gray-400 text-xs lg:text-sm">...</span>
                            @endif
                            <a href="{{ $bidhaa->url($last) }}" class="px-2 lg:px-3 py-1 lg:py-1.5 rounded-lg border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs lg:text-sm transition">
                                {{ $last }}
                            </a>
                        @endif
                    </div>

                    <!-- Next Button -->
                    @if($bidhaa->hasMorePages())
                        <a href="{{ $bidhaa->nextPageUrl() }}" class="px-2 lg:px-3 py-1 lg:py-1.5 rounded-lg border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs lg:text-sm transition flex items-center">
                            <span class="hidden sm:inline">Mbele</span>
                            <i class="fas fa-chevron-right ml-1 text-xs"></i>
                        </a>
                    @else
                        <span class="px-2 lg:px-3 py-1 lg:py-1.5 rounded-lg border text-gray-400 text-xs lg:text-sm cursor-not-allowed flex items-center">
                            <span class="hidden sm:inline">Mbele</span>
                            <i class="fas fa-chevron-right ml-1 text-xs"></i>
                        </span>
                    @endif
                </nav>

                <!-- Per Page Selector -->
                <div class="flex items-center space-x-2">
                    <span class="text-xs lg:text-sm text-gray-600 hidden sm:inline">Onyesha kwa:</span>
                    <span class="text-xs lg:text-sm text-gray-600 sm:hidden">Uk:</span>
                    <select class="border border-gray-300 rounded-lg p-1 lg:p-1.5 text-xs lg:text-sm focus:ring-2 focus:ring-green-500">
                        <option value="10" selected>10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>

            <!-- Mobile Simplified Pagination -->
            <div class="sm:hidden mt-3">
                <div class="flex items-center justify-between">
                    @if($bidhaa->onFirstPage())
                        <span class="px-3 py-1.5 rounded-lg border text-gray-400 text-xs cursor-not-allowed flex items-center">
                            <i class="fas fa-chevron-left mr-1"></i>
                        </span>
                    @else
                        <a href="{{ $bidhaa->previousPageUrl() }}" class="px-3 py-1.5 rounded-lg border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs transition flex items-center">
                            <i class="fas fa-chevron-left mr-1"></i> Nyuma
                        </a>
                    @endif

                    <span class="text-xs text-gray-600">
                        Uk. {{ $bidhaa->currentPage() }} / {{ $bidhaa->lastPage() }}
                    </span>

                    @if($bidhaa->hasMorePages())
                        <a href="{{ $bidhaa->nextPageUrl() }}" class="px-3 py-1.5 rounded-lg border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs transition flex items-center">
                            Mbele <i class="fas fa-chevron-right ml-1"></i>
                        </a>
                    @else
                        <span class="px-3 py-1.5 rounded-lg border text-gray-400 text-xs cursor-not-allowed flex items-center">
                            Mbele <i class="fas fa-chevron-right ml-1"></i>
                        </span>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- TAB 2: Ingiza Bidhaa Mpya -->
<div id="ingiza-tab-content" class="tab-content hidden">
    <div class="bg-white rounded-xl md:rounded-2xl shadow-lg border border-gray-100 p-4 md:p-6 card-hover">
        <h2 class="text-lg md:text-xl font-bold text-gray-800 mb-4 md:mb-6">Ingiza Bidhaa Mpya</h2>
        <form method="POST" action="{{ route('bidhaa.store') }}" id="product-form" class="space-y-4 md:space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4">
                <!-- Jina -->
                <div class="relative">
                    <input 
                        type="text" 
                        name="jina" 
                        placeholder=" " 
                        class="peer border border-gray-300 rounded-lg w-full p-3 md:p-3 pt-4 md:pt-5 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm md:text-base" 
                        required
                    >
                    <label class="absolute left-3 md:left-3 top-2 md:top-2 text-gray-500 text-xs md:text-xs transition-all
                                  peer-placeholder-shown:top-3 peer-placeholder-shown:md:top-3 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-xs peer-placeholder-shown:md:text-sm peer-focus:top-2 peer-focus:md:top-2 peer-focus:text-green-600
                                  peer-focus:text-xs peer-focus:md:text-xs font-medium truncate max-w-[90%]">
                        Jina la Bidhaa
                    </label>
                </div>

                <!-- Aina -->
                <div class="relative">
                    <input 
                        type="text" 
                        name="aina" 
                        placeholder=" " 
                        class="peer border border-gray-300 rounded-lg w-full p-3 md:p-3 pt-4 md:pt-5 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm md:text-base" 
                        required
                    >
                    <label class="absolute left-3 md:left-3 top-2 md:top-2 text-gray-500 text-xs md:text-xs transition-all
                                  peer-placeholder-shown:top-3 peer-placeholder-shown:md:top-3 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-xs peer-placeholder-shown:md:text-sm peer-focus:top-2 peer-focus:md:top-2 peer-focus:text-green-600
                                  peer-focus:text-xs peer-focus:md:text-xs font-medium truncate max-w-[90%]">
                        Aina
                    </label>
                </div>

                <!-- Kipimo -->
                <div class="relative">
                    <input 
                        type="text" 
                        name="kipimo" 
                        placeholder=" " 
                        class="peer border border-gray-300 rounded-lg w-full p-3 md:p-3 pt-4 md:pt-5 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm md:text-base"
                    >
                    <label class="absolute left-3 md:left-3 top-2 md:top-2 text-gray-500 text-xs md:text-xs transition-all
                                  peer-placeholder-shown:top-3 peer-placeholder-shown:md:top-3 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-xs peer-placeholder-shown:md:text-sm peer-focus:top-2 peer-focus:md:top-2 peer-focus:text-green-600
                                  peer-focus:text-xs peer-focus:md:text-xs font-medium truncate max-w-[90%]">
                        Kipimo
                    </label>
                </div>

                <!-- Idadi -->
                <div class="relative">
                    <input 
                        type="number" 
                        name="idadi" 
                        placeholder=" " 
                        class="peer border border-gray-300 rounded-lg w-full p-3 md:p-3 pt-4 md:pt-5 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm md:text-base" 
                        required
                    >
                    <label class="absolute left-3 md:left-3 top-2 md:top-2 text-gray-500 text-xs md:text-xs transition-all
                                  peer-placeholder-shown:top-3 peer-placeholder-shown:md:top-3 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-xs peer-placeholder-shown:md:text-sm peer-focus:top-2 peer-focus:md:top-2 peer-focus:text-green-600
                                  peer-focus:text-xs peer-focus:md:text-xs font-medium truncate max-w-[90%]">
                        Idadi
                    </label>
                </div>

                <!-- Bei Nunua -->
                <div class="relative">
                    <input 
                        type="number" 
                        step="0.01" 
                        name="bei_nunua" 
                        id="buy-price"
                        placeholder=" " 
                        class="peer border border-gray-300 rounded-lg w-full p-3 md:p-3 pt-4 md:pt-5 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm md:text-base" 
                        required
                    >
                    <label class="absolute left-3 md:left-3 top-2 md:top-2 text-gray-500 text-xs md:text-xs transition-all
                                  peer-placeholder-shown:top-3 peer-placeholder-shown:md:top-3 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-xs peer-placeholder-shown:md:text-sm peer-focus:top-2 peer-focus:md:top-2 peer-focus:text-green-600
                                  peer-focus:text-xs peer-focus:md:text-xs font-medium truncate max-w-[90%]">
                        Bei Nunua (TZS)
                    </label>
                </div>

                <!-- Bei Kuuza -->
                <div class="relative">
                    <input 
                        type="number" 
                        step="0.01" 
                        name="bei_kuuza" 
                        id="sell-price"
                        placeholder=" " 
                        class="peer border border-gray-300 rounded-lg w-full p-3 md:p-3 pt-4 md:pt-5 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm md:text-base" 
                        required
                    >
                    <label class="absolute left-3 md:left-3 top-2 md:top-2 text-gray-500 text-xs md:text-xs transition-all
                                  peer-placeholder-shown:top-3 peer-placeholder-shown:md:top-3 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-xs peer-placeholder-shown:md:text-sm peer-focus:top-2 peer-focus:md:top-2 peer-focus:text-green-600
                                  peer-focus:text-xs peer-focus:md:text-xs font-medium truncate max-w-[90%]">
                        Bei Kuuza (TZS)
                    </label>
                    <!-- Error Message -->
                    <p id="price-error" class="text-red-600 font-medium mt-1 md:mt-1 text-xs md:text-xs hidden"></p>
                </div>

                <!-- Expiry -->
                <div class="relative">
                    <input 
                        type="date" 
                        name="expiry" 
                        placeholder=" " 
                        class="peer border border-gray-300 rounded-lg w-full p-3 md:p-3 pt-4 md:pt-5 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm md:text-base" 
    
                    >
                    <label class="absolute left-3 md:left-3 top-2 md:top-2 text-gray-500 text-xs md:text-xs transition-all
                                  peer-placeholder-shown:top-3 peer-placeholder-shown:md:top-3 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-xs peer-placeholder-shown:md:text-sm peer-focus:top-2 peer-focus:md:top-2 peer-focus:text-green-600
                                  peer-focus:text-xs peer-focus:md:text-xs font-medium truncate max-w-[90%]">
                        Tarehe ya Mwisho
                    </label>
                </div>

                <!-- Barcode -->
                <div class="relative">
                    <input 
                        type="text" 
                        name="barcode" 
                        placeholder=" " 
                        class="peer border border-gray-300 rounded-lg w-full p-3 md:p-3 pt-4 md:pt-5 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm md:text-base"
                    >
                    <label class="absolute left-3 md:left-3 top-2 md:top-2 text-gray-500 text-xs md:text-xs transition-all
                                  peer-placeholder-shown:top-3 peer-placeholder-shown:md:top-3 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-xs peer-placeholder-shown:md:text-sm peer-focus:top-2 peer-focus:md:top-2 peer-focus:text-green-600
                                  peer-focus:text-xs peer-focus:md:text-xs font-medium truncate max-w-[90%]">
                        Barcode
                    </label>
                </div>
            </div>

            <!-- Buttons - Responsive -->
            <div class="flex flex-col sm:flex-row gap-3 md:gap-3 pt-4 md:pt-4 border-t border-gray-200 mt-4 md:mt-4">
                <button 
                    type="submit" 
                    class="bg-gradient-to-r from-green-600 to-green-700 text-white px-4 md:px-6 py-2 md:py-2.5 rounded-lg hover:from-green-700 hover:to-green-800 transition-all duration-300 transform hover:scale-105 flex items-center justify-center shadow-lg text-sm md:text-sm"
                >
                    <i class="fas fa-save mr-1 md:mr-1.5 text-sm md:text-sm"></i>
                    <span>Hifadhi Bidhaa</span>
                </button>
                <button 
                    type="reset" 
                    id="reset-form"
                    class="bg-gray-300 text-gray-700 px-4 md:px-6 py-2 md:py-2.5 rounded-lg hover:bg-gray-400 transition-colors flex items-center justify-center text-sm md:text-sm"
                >
                    <i class="fas fa-redo mr-1 md:mr-1.5 text-sm md:text-sm"></i>
                    <span>Safisha Fomu</span>
                </button>
            </div>
        </form>
    </div>
</div>
    <!-- TAB 3: CSV Upload -->
    <div id="csv-tab-content" class="tab-content hidden">
        <div class="bg-white rounded-xl md:rounded-2xl shadow-lg border border-gray-100 p-4 md:p-6 card-hover">
            <h2 class="text-lg md:text-xl font-bold text-gray-800 mb-4 md:mb-6">Ingiza Bidhaa kwa CSV</h2>

            <!-- Upload Section - Responsive Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6 mb-6 md:mb-8">
                <!-- Upload Form -->
                <div class="bg-gradient-to-br from-green-50 to-emerald-50 border border-green-200 rounded-xl p-4 md:p-6">
                    <div class="text-center mb-3 md:mb-4">
                        <i class="fas fa-file-csv text-3xl md:text-4xl text-green-500 mb-2 md:mb-3"></i>
                        <h3 class="text-base md:text-lg font-semibold text-green-800 mb-1 md:mb-2">Pakia Faili la CSV</h3>
                        <p class="text-xs md:text-sm text-green-600 mb-3 md:mb-4">Chagua faili lako la CSV</p>
                    </div>
                    
                    <form method="POST" action="{{ route('bidhaa.uploadCSV') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="space-y-3 md:space-y-4">
                            <div class="border-2 border-dashed border-green-300 rounded-lg p-3 md:p-4 bg-white text-center transition-all duration-200 hover:border-green-400">
                                <input type="file" name="csv_file" accept=".csv,.txt" 
                                       class="block w-full text-xs md:text-sm text-green-700 file:mr-2 md:file:mr-4 file:py-1 md:file:py-2 file:px-2 md:file:px-4 file:rounded-lg file:border-0 file:text-xs md:file:text-sm file:font-medium file:bg-green-500 file:text-white hover:file:bg-green-600 cursor-pointer" 
                                       required>
                                <p class="text-xs text-gray-500 mt-1 md:mt-2">Aina: .csv, .txt | Ukubwa: hadi 10MB</p>
                            </div>
                            <button type="submit" 
                                    class="w-full bg-gradient-to-r from-green-500 to-green-600 text-white px-3 md:px-4 py-2 md:py-3 rounded-lg hover:from-green-600 hover:to-green-700 transition-all duration-200 flex items-center justify-center shadow-md text-sm md:text-base">
                                <i class="fas fa-upload mr-1 md:mr-2 text-sm md:text-base"></i>
                                <span>Pakia Faili</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Download Sample -->
                <div class="bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-200 rounded-xl p-4 md:p-6">
                    <div class="text-center mb-3 md:mb-4">
                        <i class="fas fa-download text-3xl md:text-4xl text-amber-500 mb-2 md:mb-3"></i>
                        <h3 class="text-base md:text-lg font-semibold text-amber-800 mb-1 md:mb-2">Pakua Faili Sampuli</h3>
                        <p class="text-xs md:text-sm text-amber-600 mb-3 md:mb-4">Pakua faili la mfano</p>
                    </div>
                    
                    <div class="space-y-3 md:space-y-4">
                        <div class="bg-white border border-amber-200 rounded-lg p-3 md:p-4 text-center">
                            <i class="fas fa-table text-xl md:text-2xl text-amber-400 mb-1 md:mb-2"></i>
                            <p class="text-xs md:text-sm text-amber-700 font-medium">Muundo Sahihi</p>
                            <p class="text-xs text-gray-600 mt-1">Orodha kamili ya bidhaa</p>
                        </div>
                        <a href="{{ route('bidhaa.downloadSample') }}" 
                           class="w-full bg-gradient-to-r from-amber-500 to-orange-500 text-white px-3 md:px-4 py-2 md:py-3 rounded-lg hover:from-amber-600 hover:to-orange-600 transition-all duration-200 flex items-center justify-center shadow-md text-sm md:text-base">
                            <i class="fas fa-file-download mr-1 md:mr-2 text-sm md:text-base"></i>
                            <span>Pakua Sampuli</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Upload Results -->
            @if(session('successCount') > 0)
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-300 rounded-xl p-3 md:p-4 mb-4 md:mb-6">
                    <div class="flex items-start md:items-center">
                        <div class="p-1 md:p-2 rounded-lg bg-green-100 text-green-600 mr-2 md:mr-3 flex-shrink-0">
                            <i class="fas fa-check-circle text-sm md:text-base"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-xs md:text-sm font-semibold text-green-800">Upakiaji Umekamilika!</h4>
                            <p class="text-green-700 text-xs md:text-sm mt-1">Bidhaa {{ session('successCount') }} zimeongezwa</p>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('errorsList') && count(session('errorsList')) > 0)
                <div class="bg-gradient-to-r from-red-50 to-pink-50 border border-red-300 rounded-xl p-3 md:p-4 mb-4 md:mb-6">
                    <div class="flex items-start">
                        <div class="p-1 md:p-2 rounded-lg bg-red-100 text-red-600 mr-2 md:mr-3 mt-1 flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-sm md:text-base"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-xs md:text-sm font-semibold text-red-800 mb-1 md:mb-2">Hitilafu katika Upakiaji</h4>
                            <div class="max-h-24 md:max-h-32 overflow-y-auto pr-1 md:pr-2">
                                <ul class="space-y-1 text-xs md:text-sm">
                                    @foreach(session('errorsList') as $error)
                                        <li class="flex items-start text-red-700">
                                            <i class="fas fa-times-circle text-red-500 mr-1 md:mr-2 mt-0.5 text-xs"></i>
                                            <span class="flex-1">{{ $error }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Instructions -->
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 md:p-4 mb-4 md:mb-6">
                <div class="flex items-start">
                    <div class="p-1 md:p-2 rounded-lg bg-blue-100 text-blue-600 mr-2 md:mr-3 flex-shrink-0">
                        <i class="fas fa-info-circle text-sm md:text-base"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-xs md:text-sm font-semibold text-blue-800 mb-1">Maelekezo Muhimu</h4>
                        <ul class="text-xs md:text-sm text-blue-700 space-y-1">
                            <li class="flex items-start">
                                <span class="mr-1 md:mr-2 text-xs">•</span>
                                <span>Faili lazima liwjazwe kikamilifu</span>
                            </li>
                            <li class="flex items-start">
                                <span class="mr-1 md:mr-2 text-xs">•</span>
                                <span>Data ya tarehe: YYYY-MM-DD</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Sample File Structure -->
            <div class="border border-gray-200 rounded-xl overflow-hidden">
                <div class="bg-gradient-to-r from-green-600 to-green-700 px-3 md:px-4 py-2 md:py-3">
                    <h3 class="text-xs md:text-sm font-semibold text-white flex items-center">
                        <i class="fas fa-table mr-1 md:mr-2 text-xs md:text-sm"></i>
                        <span>Muundo wa Faili la CSV</span>
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="bg-green-50">
                                <th class="px-2 md:px-3 py-1 md:py-2 text-left font-semibold text-green-800 border-b border-green-200 truncate">Jina</th>
                                <th class="px-2 md:px-3 py-1 md:py-2 text-left font-semibold text-green-800 border-b border-green-200 truncate">Aina</th>
                                <th class="px-2 md:px-3 py-1 md:py-2 text-left font-semibold text-green-800 border-b border-green-200 truncate hidden md:table-cell">Kipimo</th>
                                <th class="px-2 md:px-3 py-1 md:py-2 text-center font-semibold text-green-800 border-b border-green-200 truncate">Idadi</th>
                                <th class="px-2 md:px-3 py-1 md:py-2 text-right font-semibold text-green-800 border-b border-green-200 truncate hidden sm:table-cell">Bei_Nunua</th>
                                <th class="px-2 md:px-3 py-1 md:py-2 text-right font-semibold text-green-800 border-b border-green-200 truncate">Bei_Kuuza</th>
                                <th class="px-2 md:px-3 py-1 md:py-2 text-left font-semibold text-green-800 border-b border-green-200 truncate hidden lg:table-cell">Expiry</th>
                                <th class="px-2 md:px-3 py-1 md:py-2 text-left font-semibold text-green-800 border-b border-green-200 truncate hidden xl:table-cell">Barcode</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            <tr class="hover:bg-gray-50">
                                <td class="px-2 md:px-3 py-1 md:py-2 text-green-700 font-medium truncate">Soda</td>
                                <td class="px-2 md:px-3 py-1 md:py-2 text-gray-600 truncate">Vinywaji</td>
                                <td class="px-2 md:px-3 py-1 md:py-2 text-gray-600 hidden md:table-cell">500ml</td>
                                <td class="px-2 md:px-3 py-1 md:py-2 text-center text-green-600 font-semibold">100</td>
                                <td class="px-2 md:px-3 py-1 md:py-2 text-right text-gray-600 hidden sm:table-cell">600.00</td>
                                <td class="px-2 md:px-3 py-1 md:py-2 text-right text-green-600 font-semibold">1000.00</td>
                                <td class="px-2 md:px-3 py-1 md:py-2 text-gray-600 hidden lg:table-cell">2025-12-31</td>
                                <td class="px-2 md:px-3 py-1 md:py-2 text-blue-600 font-mono hidden xl:table-cell">1234567890123</td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-2 md:px-3 py-1 md:py-2 text-green-700 font-medium truncate">Mchele</td>
                                <td class="px-2 md:px-3 py-1 md:py-2 text-gray-600 truncate">Chakula</td>
                                <td class="px-2 md:px-3 py-1 md:py-2 text-gray-600 hidden md:table-cell">1kg</td>
                                <td class="px-2 md:px-3 py-1 md:py-2 text-center text-green-600 font-semibold">50</td>
                                <td class="px-2 md:px-3 py-1 md:py-2 text-right text-gray-600 hidden sm:table-cell">2500.00</td>
                                <td class="px-2 md:px-3 py-1 md:py-2 text-right text-green-600 font-semibold">3500.00</td>
                                <td class="px-2 md:px-3 py-1 md:py-2 text-gray-600 hidden lg:table-cell">2026-06-30</td>
                                <td class="px-2 md:px-3 py-1 md:py-2 text-blue-600 font-mono hidden xl:table-cell">9876543210987</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="bg-gray-50 px-2 md:px-3 py-1 md:py-2 border-t border-gray-200">
                    <p class="text-xs text-gray-600 text-center">Faili lako lazima liwe na vichwa hivi</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="edit-modal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-xl md:rounded-2xl shadow-xl w-full max-w-2xl mx-auto z-50 max-h-[90vh] overflow-y-auto">
        <div class="p-4 md:p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Badili Taarifa za Bidhaa</h3>
        </div>
        <form id="edit-form" method="POST" class="p-4 md:p-6">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4">
                <div class="relative">
                    <input 
                        type="text" 
                        name="jina" 
                        id="edit-jina"
                        class="peer border border-gray-300 rounded-lg w-full p-3 md:p-4 pt-5 md:pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm md:text-base" 
                        required
                    >
                    <label class="absolute left-3 md:left-4 top-1 md:top-2 text-gray-400 text-xs md:text-sm transition-all
                                  peer-placeholder-shown:top-3 peer-placeholder-shown:md:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-sm peer-placeholder-shown:md:text-base peer-focus:top-1 peer-focus:md:top-2 peer-focus:text-green-600
                                  peer-focus:text-xs peer-focus:md:text-sm font-medium">
                        Jina la Bidhaa
                    </label>
                </div>

                <div class="relative">
                    <input 
                        type="text" 
                        name="aina" 
                        id="edit-aina"
                        class="peer border border-gray-300 rounded-lg w-full p-3 md:p-4 pt-5 md:pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm md:text-base" 
                        required
                    >
                    <label class="absolute left-3 md:left-4 top-1 md:top-2 text-gray-400 text-xs md:text-sm transition-all
                                  peer-placeholder-shown:top-3 peer-placeholder-shown:md:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-sm peer-placeholder-shown:md:text-base peer-focus:top-1 peer-focus:md:top-2 peer-focus:text-green-600
                                  peer-focus:text-xs peer-focus:md:text-sm font-medium">
                        Aina ya Bidhaa
                    </label>
                </div>

                <div class="relative">
                    <input 
                        type="text" 
                        name="kipimo" 
                        id="edit-kipimo"
                        class="peer border border-gray-300 rounded-lg w-full p-3 md:p-4 pt-5 md:pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm md:text-base"
                    >
                    <label class="absolute left-3 md:left-4 top-1 md:top-2 text-gray-400 text-xs md:text-sm transition-all
                                  peer-placeholder-shown:top-3 peer-placeholder-shown:md:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-sm peer-placeholder-shown:md:text-base peer-focus:top-1 peer-focus:md:top-2 peer-focus:text-green-600
                                  peer-focus:text-xs peer-focus:md:text-sm font-medium">
                        Kipimo
                    </label>
                </div>

                <div class="relative">
                    <input 
                        type="number" 
                        name="idadi" 
                        id="edit-idadi"
                        class="peer border border-gray-300 rounded-lg w-full p-3 md:p-4 pt-5 md:pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm md:text-base" 
                        required
                    >
                    <label class="absolute left-3 md:left-4 top-1 md:top-2 text-gray-400 text-xs md:text-sm transition-all
                                  peer-placeholder-shown:top-3 peer-placeholder-shown:md:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-sm peer-placeholder-shown:md:text-base peer-focus:top-1 peer-focus:md:top-2 peer-focus:text-green-600
                                  peer-focus:text-xs peer-focus:md:text-sm font-medium">
                        Idadi
                    </label>
                </div>

                <div class="relative">
                    <input 
                        type="number" 
                        step="0.01" 
                        name="bei_nunua" 
                        id="edit-bei-nunua"
                        class="peer border border-gray-300 rounded-lg w-full p-3 md:p-4 pt-5 md:pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm md:text-base" 
                        required
                    >
                    <label class="absolute left-3 md:left-4 top-1 md:top-2 text-gray-400 text-xs md:text-sm transition-all
                                  peer-placeholder-shown:top-3 peer-placeholder-shown:md:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-sm peer-placeholder-shown:md:text-base peer-focus:top-1 peer-focus:md:top-2 peer-focus:text-green-600
                                  peer-focus:text-xs peer-focus:md:text-sm font-medium">
                        Bei Nunua
                    </label>
                </div>

                <div class="relative">
                    <input 
                        type="number" 
                        step="0.01" 
                        name="bei_kuuza" 
                        id="edit-bei-kuuza"
                        class="peer border border-gray-300 rounded-lg w-full p-3 md:p-4 pt-5 md:pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm md:text-base" 
                        required
                    >
                    <label class="absolute left-3 md:left-4 top-1 md:top-2 text-gray-400 text-xs md:text-sm transition-all
                                  peer-placeholder-shown:top-3 peer-placeholder-shown:md:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-sm peer-placeholder-shown:md:text-base peer-focus:top-1 peer-focus:md:top-2 peer-focus:text-green-600
                                  peer-focus:text-xs peer-focus:md:text-sm font-medium">
                        Bei Kuuza
                    </label>
                </div>

                <div class="relative">
                    <input 
                        type="date" 
                        name="expiry" 
                        id="edit-expiry"
                        class="peer border border-gray-300 rounded-lg w-full p-3 md:p-4 pt-5 md:pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm md:text-base"
                    >
                    <label class="absolute left-3 md:left-4 top-1 md:top-2 text-gray-400 text-xs md:text-sm transition-all
                                  peer-placeholder-shown:top-3 peer-placeholder-shown:md:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-sm peer-placeholder-shown:md:text-base peer-focus:top-1 peer-focus:md:top-2 peer-focus:text-green-600
                                  peer-focus:text-xs peer-focus:md:text-sm font-medium">
                        Tarehe ya Expiry
                    </label>
                </div>

                <div class="relative">
                    <input 
                        type="text" 
                        name="barcode" 
                        id="edit-barcode"
                        class="peer border border-gray-300 rounded-lg w-full p-3 md:p-4 pt-5 md:pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm md:text-base"
                    >
                    <label class="absolute left-3 md:left-4 top-1 md:top-2 text-gray-400 text-xs md:text-sm transition-all
                                  peer-placeholder-shown:top-3 peer-placeholder-shown:md:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-sm peer-placeholder-shown:md:text-base peer-focus:top-1 peer-focus:md:top-2 peer-focus:text-green-600
                                  peer-focus:text-xs peer-focus:md:text-sm font-medium">
                        Barcode
                    </label>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-2 md:space-x-3 pt-4 md:pt-6 border-t border-gray-200 mt-4 md:mt-6">
                <button 
                    type="button" 
                    id="close-edit-modal"
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

<!-- Delete Confirmation Modal -->
<div id="delete-modal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-xl md:rounded-2xl shadow-xl w-full max-w-md mx-auto z-50">
        <div class="p-4 md:p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Thibitisha Kufuta Bidhaa</h3>
        </div>
        <div class="p-4 md:p-6">
            <div class="flex items-center justify-center mb-3 md:mb-4">
                <div class="p-2 md:p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-exclamation-triangle text-lg md:text-xl"></i>
                </div>
            </div>
            <p class="text-gray-700 mb-4 md:mb-6 text-center text-sm md:text-base">
                Una uhakika unataka kufuta bidhaa "<span id="delete-product-name" class="font-semibold"></span>"?
                <br class="hidden sm:block">Hatua hii haiwezi kutenduliwa.
            </p>
            <div class="flex flex-col sm:flex-row justify-center space-y-2 sm:space-y-0 sm:space-x-2 md:sm:space-x-3">
                <button 
                    id="cancel-delete"
                    class="px-4 md:px-6 py-2 md:py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors text-sm md:text-base"
                >
                    Ghairi
                </button>
                <form id="delete-form" method="POST">
                    @csrf
                    @method('DELETE')
                    <button 
                        type="submit" 
                        class="px-4 md:px-6 py-2 md:py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm md:text-base"
                    >
                        Ndio, Futa
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
@media (max-width: 640px) {
    .modal-content {
        margin: 0;
        border-radius: 0.75rem;
    }
}

.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

.modal {
    transition: opacity 0.3s ease;
}

.tab-content {
    transition: opacity 0.3s ease;
}

.hidden {
    display: none !important;
}

.product-row.hidden {
    display: none;
}
</style>
@endpush

@push('scripts')
<script>
class BidhaaManager {
    constructor() {
        this.currentTab = 'taarifa';
        this.searchQuery = '';
        this.init();
    }

    init() {
        this.bindEvents();
        this.showTab('taarifa');
    }

    bindEvents() {
        // Tab navigation
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', (e) => {
                const tab = e.target.closest('.tab-button').dataset.tab;
                this.showTab(tab);
            });
        });

        // Search functionality
        const searchInput = document.getElementById('search-input');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                this.searchQuery = e.target.value.toLowerCase();
                this.filterProducts();
            });
        }

        // Go to add product button
        const goToAddProductBtn = document.getElementById('go-to-add-product');
        if (goToAddProductBtn) {
            goToAddProductBtn.addEventListener('click', () => {
                this.showTab('ingiza');
            });
        }

        // Product actions
        this.bindProductActions();

        // Form validation
        this.bindFormValidation();

        // Modal events
        this.bindModalEvents();
    }

    showTab(tabName) {
        // Update tabs
        document.querySelectorAll('.tab-button').forEach(button => {
            if (button.dataset.tab === tabName) {
                button.classList.add('border-b-2', 'border-green-500', 'text-green-600', 'font-semibold');
                button.classList.remove('text-gray-500');
            } else {
                button.classList.remove('border-b-2', 'border-green-500', 'text-green-600', 'font-semibold');
                button.classList.add('text-gray-500');
            }
        });

        // Update tab content
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        
        const activeContent = document.getElementById(`${tabName}-tab-content`);
        if (activeContent) {
            activeContent.classList.remove('hidden');
        }

        this.currentTab = tabName;
    }

    bindProductActions() {
        // Edit buttons
        document.querySelectorAll('.edit-product-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                const productId = e.target.closest('.edit-product-btn').dataset.id;
                const row = e.target.closest('.product-row');
                const productData = JSON.parse(row.dataset.product);
                this.editProduct(productData);
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
        const buyPriceInput = document.getElementById('buy-price');
        const sellPriceInput = document.getElementById('sell-price');
        const priceError = document.getElementById('price-error');
        const resetButton = document.getElementById('reset-form');

        if (productForm) {
            productForm.addEventListener('submit', (e) => {
                const buyPrice = parseFloat(buyPriceInput.value);
                const sellPrice = parseFloat(sellPriceInput.value);

                if (buyPrice > sellPrice) {
                    e.preventDefault();
                    priceError.textContent = '⚠️ Bei ya kununua haiwezi kuwa kubwa kuliko bei ya kuuza!';
                    priceError.classList.remove('hidden');
                } else {
                    priceError.classList.add('hidden');
                }
            });
        }

        if (resetButton) {
            resetButton.addEventListener('click', () => {
                priceError.classList.add('hidden');
            });
        }
    }

    bindModalEvents() {
        // Edit modal
        const editModal = document.getElementById('edit-modal');
        const closeEditBtn = document.getElementById('close-edit-modal');

        closeEditBtn.addEventListener('click', () => {
            editModal.classList.add('hidden');
        });

        editModal.addEventListener('click', (e) => {
            if (e.target === editModal || e.target.classList.contains('modal-overlay')) {
                editModal.classList.add('hidden');
            }
        });

        // Delete modal
        const deleteModal = document.getElementById('delete-modal');
        const cancelDeleteBtn = document.getElementById('cancel-delete');

        cancelDeleteBtn.addEventListener('click', () => {
            deleteModal.classList.add('hidden');
        });

        deleteModal.addEventListener('click', (e) => {
            if (e.target === deleteModal || e.target.classList.contains('modal-overlay')) {
                deleteModal.classList.add('hidden');
            }
        });

        // Close modals on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                editModal.classList.add('hidden');
                deleteModal.classList.add('hidden');
            }
        });
    }

    filterProducts() {
        const rows = document.querySelectorAll('.product-row');
        
        rows.forEach(row => {
            const name = row.querySelector('.product-name').textContent.toLowerCase();
            const type = row.querySelector('.product-type').textContent.toLowerCase();
            
            const matches = name.includes(this.searchQuery) || 
                           type.includes(this.searchQuery);
            
            if (matches || this.searchQuery === '') {
                row.classList.remove('hidden');
            } else {
                row.classList.add('hidden');
            }
        });
    }

    editProduct(product) {
        // Populate edit form
        document.getElementById('edit-jina').value = product.jina;
        document.getElementById('edit-aina').value = product.aina;
        document.getElementById('edit-kipimo').value = product.kipimo || '';
        document.getElementById('edit-idadi').value = product.idadi;
        document.getElementById('edit-bei-nunua').value = product.bei_nunua;
        document.getElementById('edit-bei-kuuza').value = product.bei_kuuza;
        document.getElementById('edit-expiry').value = product.expiry || '';
        document.getElementById('edit-barcode').value = product.barcode || '';

        // Set form action
        document.getElementById('edit-form').action = `/bidhaa/${product.id}`;

        // Show modal
        document.getElementById('edit-modal').classList.remove('hidden');
    }

    deleteProduct(productId, productName) {
        // Populate delete modal
        document.getElementById('delete-product-name').textContent = productName;
        document.getElementById('delete-form').action = `/bidhaa/${productId}`;

        // Show modal
        document.getElementById('delete-modal').classList.remove('hidden');
    }
}

// Initialize the application when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    new BidhaaManager();
});
</script>
@endpush