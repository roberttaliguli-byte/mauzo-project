@extends('layouts.app')

@section('title', 'Manunuzi - DEMODAY')

@section('page-title', 'Manunuzi')
@section('page-subtitle', 'Usimamizi wa manunuzi yote - ' . now()->format('d/m/Y'))

@section('content')
<div class="space-y-4 md:space-y-6">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6">
        <div class="bg-white rounded-xl md:rounded-2xl shadow-md md:shadow-lg border border-gray-100 p-4 md:p-6 card-hover">
            <div class="flex items-center">
                <div class="p-2 md:p-3 rounded-lg bg-green-100 text-green-600 mr-3 md:mr-4">
                    <i class="fas fa-shopping-cart text-lg md:text-xl"></i>
                </div>
                <div>
                    <p class="text-xs md:text-sm text-black font-medium">Jumla ya Manunuzi</p>
                    <h3 class="text-lg md:text-2xl font-bold text-gray-800">{{ $manunuzi->count() }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl md:rounded-2xl shadow-md md:shadow-lg border border-gray-100 p-4 md:p-6 card-hover">
            <div class="flex items-center">
                <div class="p-2 md:p-3 rounded-lg bg-emerald-100 text-emerald-600 mr-3 md:mr-4">
                    <i class="fas fa-boxes text-lg md:text-xl"></i>
                </div>
                <div>
                    <p class="text-xs md:text-sm text-black font-medium">Bidhaa Zilizonunuliwa</p>
                    <h3 class="text-lg md:text-2xl font-bold text-gray-800">{{ $manunuzi->sum('idadi') }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl md:rounded-2xl shadow-md md:shadow-lg border border-gray-100 p-4 md:p-6 card-hover">
            <div class="flex items-center">
                <div class="p-2 md:p-3 rounded-lg bg-blue-100 text-blue-600 mr-3 md:mr-4">
                    <i class="fas fa-money-bill-wave text-lg md:text-xl"></i>
                </div>
                <div>
                    <p class="text-xs md:text-sm text-black font-medium">Jumla ya Gharama</p>
                    <h3 class="text-lg md:text-2xl font-bold text-gray-800">TZS {{ number_format($manunuzi->sum('bei'), 2) }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl md:rounded-2xl shadow-md md:shadow-lg border border-gray-100 p-4 md:p-6 card-hover">
            <div class="flex items-center">
                <div class="p-2 md:p-3 rounded-lg bg-purple-100 text-purple-600 mr-3 md:mr-4">
                    <i class="fas fa-truck text-lg md:text-xl"></i>
                </div>
                <div>
                    <p class="text-xs md:text-sm text-black font-medium">Wasaplaya</p>
                    <h3 class="text-lg md:text-2xl font-bold text-gray-800">{{ $manunuzi->pluck('saplaya')->unique()->count() }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Page Navigation Tabs -->
    <div class="bg-white rounded-xl md:rounded-2xl shadow-md md:shadow-lg border border-gray-100 p-3 md:p-4 card-hover overflow-x-auto">
        <div class="flex space-x-3 md:space-x-6 border-b border-gray-200 min-w-max md:min-w-0">
            <button 
                onclick="switchTab('taarifa')" 
                id="tab-taarifa"
                class="tab-button pb-2 md:pb-3 px-1 md:px-1 transition-colors flex items-center border-b-2 border-green-500 text-green-600 font-semibold text-xs md:text-base whitespace-nowrap"
            >
                <i class="fas fa-table mr-1 md:mr-2"></i>Taarifa za Manunuzi
            </button>
            <button 
                onclick="switchTab('ingiza')" 
                id="tab-ingiza"
                class="tab-button pb-2 md:pb-3 px-1 md:px-1 transition-colors flex items-center text-gray-500 hover:text-gray-700 text-xs md:text-base whitespace-nowrap"
            >
                <i class="fas fa-plus-circle mr-1 md:mr-2"></i>Ingiza Manunuzi
            </button>
        </div>
    </div>

<!-- Manunuzi Information Tab -->
<div id="content-taarifa" class="tab-content space-y-4 lg:space-y-6">
    <!-- Search and Actions -->
    <div class="bg-white rounded-xl lg:rounded-2xl shadow-md lg:shadow-lg border border-gray-100 p-4 lg:p-6 card-hover">
        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-4 lg:mb-6 gap-3 lg:gap-0">
            <h2 class="text-base lg:text-lg xl:text-xl font-bold text-gray-800">Orodha ya Manunuzi</h2>
            <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3">
                <div class="relative flex-1">
                    <input 
                        type="text" 
                        id="searchQuery"
                        placeholder="Tafuta manunuzi..." 
                        class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent w-full text-sm lg:text-base"
                        oninput="filterTable()"
                    >
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400 text-sm"></i>
                    </div>
                </div>
                <button 
                    onclick="window.print()" 
                    class="bg-blue-600 text-white px-3 lg:px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center text-sm lg:text-base"
                >
                    <i class="fas fa-print mr-2 text-sm"></i> Print
                </button>
            </div>
        </div>

        <!-- Data Table -->
        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="w-full min-w-[800px] lg:min-w-0">
                <thead>
                    <tr class="bg-gradient-to-r from-green-600 to-green-700">
                        <th class="px-2 lg:px-4 xl:px-6 py-2 lg:py-3 xl:py-4 text-left text-xs lg:text-sm font-semibold text-white whitespace-nowrap">Tarehe & Muda</th>
                        <th class="px-2 lg:px-4 xl:px-6 py-2 lg:py-3 xl:py-4 text-left text-xs lg:text-sm font-semibold text-white whitespace-nowrap">Bidhaa</th>
                        <th class="px-2 lg:px-4 xl:px-6 py-2 lg:py-3 xl:py-4 text-center text-xs lg:text-sm font-semibold text-white whitespace-nowrap">Idadi</th>
                        <th class="px-2 lg:px-4 xl:px-6 py-2 lg:py-3 xl:py-4 text-right text-xs lg:text-sm font-semibold text-white whitespace-nowrap">Bei (TZS)</th>
                        <th class="px-2 lg:px-4 xl:px-6 py-2 lg:py-3 xl:py-4 text-left text-xs lg:text-sm font-semibold text-white whitespace-nowrap">Expiry</th>
                        <th class="px-2 lg:px-4 xl:px-6 py-2 lg:py-3 xl:py-4 text-left text-xs lg:text-sm font-semibold text-white whitespace-nowrap">Saplaya</th>
                        <th class="px-2 lg:px-4 xl:px-6 py-2 lg:py-3 xl:py-4 text-left text-xs lg:text-sm font-semibold text-white whitespace-nowrap">Simu</th>
                        <th class="px-2 lg:px-4 xl:px-6 py-2 lg:py-3 xl:py-4 text-left text-xs lg:text-sm font-semibold text-white whitespace-nowrap">Maelezo</th>
                        <th class="px-2 lg:px-4 xl:px-6 py-2 lg:py-3 xl:py-4 text-center text-xs lg:text-sm font-semibold text-white whitespace-nowrap print:hidden">Vitendo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($manunuzi as $item)
                        <tr class="table-row hover:bg-green-50 transition-all duration-200 
                            @if($item->created_at->format('Y-m-d') === now()->format('Y-m-d')) bg-gradient-to-r from-amber-50 to-yellow-50 border-l-4 border-amber-400 @endif"
                            data-searchable="{{ strtolower($item->bidhaa->jina . ' ' . $item->saplaya . ' ' . $item->simu) }}">
                            
                            <td class="px-2 lg:px-4 xl:px-6 py-2 lg:py-3 xl:py-4 whitespace-nowrap">
                                <div class="text-xs lg:text-sm font-semibold text-gray-800">{{ $item->created_at->format('d/m/Y') }}</div>
                                <div class="text-xs text-green-600 font-medium">{{ $item->created_at->format('H:i') }}</div>
                            </td>
                            
                            <td class="px-2 lg:px-4 xl:px-6 py-2 lg:py-3 xl:py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="hidden sm:flex flex-shrink-0 h-6 w-6 lg:h-8 lg:w-8 xl:h-10 xl:w-10 bg-green-100 rounded-lg items-center justify-center text-green-800 font-semibold text-xs lg:text-sm xl:text-base">
                                        {{ substr($item->bidhaa->jina, 0, 1) }}
                                    </div>
                                    <div class="sm:ml-2 lg:ml-3 xl:ml-4">
                                        <div class="text-xs lg:text-sm font-semibold text-gray-900 truncate max-w-[100px] lg:max-w-[150px] xl:max-w-none">{{ $item->bidhaa->jina }}</div>
                                        <div class="text-xs text-green-600 truncate max-w-[100px] lg:max-w-[150px] xl:max-w-none">{{ $item->bidhaa->aina }}</div>
                                    </div>
                                </div>
                            </td>
                            
                            <td class="px-2 lg:px-4 xl:px-6 py-2 lg:py-3 xl:py-4 whitespace-nowrap text-center">
                                <div class="text-xs lg:text-sm font-bold text-green-700">{{ $item->idadi }}</div>
                                <div class="text-xs text-gray-500">{{ $item->bidhaa->kipimo }}</div>
                            </td>
                            
                            <td class="px-2 lg:px-4 xl:px-6 py-2 lg:py-3 xl:py-4 whitespace-nowrap text-right">
                                <div class="text-xs lg:text-sm font-bold text-green-700">{{ number_format($item->bei, 0) }}</div>
                                <div class="text-xs text-gray-500">
                                    @ {{ number_format($item->bei / $item->idadi, 0) }}/kimoja
                                </div>
                            </td>
                            
                            <td class="px-2 lg:px-4 xl:px-6 py-2 lg:py-3 xl:py-4 whitespace-nowrap">
                                <div class="text-xs lg:text-sm 
                                    @if($item->expiry && \Carbon\Carbon::parse($item->expiry) < now()) text-red-700 font-semibold
                                    @elseif($item->expiry && \Carbon\Carbon::parse($item->expiry)->diffInDays(now()) < 30) text-amber-700
                                    @else text-gray-700 @endif">
                                    {{ $item->expiry ? \Carbon\Carbon::parse($item->expiry)->format('d/m/Y') : '--' }}
                                </div>
                            </td>
                            
                            <td class="px-2 lg:px-4 xl:px-6 py-2 lg:py-3 xl:py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 lg:py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200 truncate max-w-[60px] lg:max-w-[100px] xl:max-w-none">
                                    {{ $item->saplaya ?: '--' }}
                                </span>
                            </td>
                            
                            <td class="px-2 lg:px-4 xl:px-6 py-2 lg:py-3 xl:py-4 whitespace-nowrap">
                                <div class="text-xs lg:text-sm text-gray-700">{{ $item->simu ?: '--' }}</div>
                            </td>
                            
                            <td class="px-2 lg:px-4 xl:px-6 py-2 lg:py-3 xl:py-4">
                                <div class="text-xs lg:text-sm text-gray-700 truncate max-w-[100px] lg:max-w-[150px] xl:max-w-xs">{{ $item->mengineyo ?: '--' }}</div>
                            </td>
                            
                            <td class="px-2 lg:px-4 xl:px-6 py-2 lg:py-3 xl:py-4 whitespace-nowrap text-xs lg:text-sm font-medium print:hidden">
                                <div class="flex justify-center space-x-1 lg:space-x-2 xl:space-x-3">
                                    <button 
                                        onclick='editItem(@json($item))' 
                                        class="text-amber-600 hover:text-amber-800 transition-colors transform hover:scale-110 p-0.5 lg:p-1"
                                        title="Badili"
                                    >
                                        <i class="fas fa-edit text-xs lg:text-sm"></i>
                                    </button>
                                    <button 
                                        onclick="deleteItem({{ $item->id }}, '{{ addslashes($item->bidhaa->jina) }}')" 
                                        class="text-red-500 hover:text-red-700 transition-colors transform hover:scale-110 p-0.5 lg:p-1"
                                        title="Futa"
                                    >
                                        <i class="fas fa-trash text-xs lg:text-sm"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 lg:px-6 xl:px-8 py-6 lg:py-8 xl:py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-shopping-cart text-3xl lg:text-4xl xl:text-5xl text-green-300 mb-2 lg:mb-3 xl:mb-4"></i>
                                    <p class="text-sm lg:text-base xl:text-lg font-semibold text-gray-600 mb-1 lg:mb-2">Hakuna manunuzi bado.</p>
                                    <p class="text-xs lg:text-sm text-gray-500 mb-2 lg:mb-3 xl:mb-4">Anza kwa kuongeza manunuzi yako ya kwanza</p>
                                    <button 
                                        onclick="switchTab('ingiza')" 
                                        class="bg-gradient-to-r from-green-600 to-green-700 text-white px-3 lg:px-4 xl:px-6 py-1.5 lg:py-2 xl:py-3 rounded-lg hover:from-green-700 hover:to-green-800 transition-all duration-300 transform hover:scale-105 flex items-center shadow text-xs lg:text-sm xl:text-base"
                                    >
                                        <i class="fas fa-plus-circle mr-1 lg:mr-2 text-xs lg:text-sm xl:text-base"></i> 
                                        <span>Ingiza Manunuzi</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($manunuzi->count() > 0)
                <tfoot>
                    <tr class="bg-gradient-to-r from-green-800 to-green-900">
                        <td colspan="2" class="px-2 lg:px-4 xl:px-6 py-2 lg:py-3 xl:py-4 text-xs lg:text-sm font-bold text-white text-right">Jumla:</td>
                        <td class="px-2 lg:px-4 xl:px-6 py-2 lg:py-3 xl:py-4 text-center text-xs lg:text-sm font-bold text-white">{{ $manunuzi->sum('idadi') }}</td>
                        <td class="px-2 lg:px-4 xl:px-6 py-2 lg:py-3 xl:py-4 text-right text-xs lg:text-sm font-bold text-white">TZS {{ number_format($manunuzi->sum('bei'), 0) }}</td>
                        <td colspan="5" class="print:hidden"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>

        <!-- Pagination - Responsive -->
        @if($manunuzi->hasPages())
        <div class="mt-4 lg:mt-6">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                <!-- Pagination Info -->
                <div class="text-xs lg:text-sm text-gray-600">
                    @php
                        $start = ($manunuzi->currentPage() - 1) * $manunuzi->perPage() + 1;
                        $end = min($manunuzi->currentPage() * $manunuzi->perPage(), $manunuzi->total());
                    @endphp
                    Onyesha {{ $start }} - {{ $end }} ya {{ $manunuzi->total() }} manunuzi
                </div>

                <!-- Pagination Links -->
                <nav class="flex items-center space-x-1">
                    <!-- Previous Button -->
                    @if($manunuzi->onFirstPage())
                        <span class="px-2 lg:px-3 py-1 lg:py-1.5 rounded-lg border text-gray-400 text-xs lg:text-sm cursor-not-allowed flex items-center">
                            <i class="fas fa-chevron-left mr-1 text-xs"></i>
                            <span class="hidden sm:inline">Nyuma</span>
                        </span>
                    @else
                        <a href="{{ $manunuzi->previousPageUrl() }}" class="px-2 lg:px-3 py-1 lg:py-1.5 rounded-lg border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs lg:text-sm transition flex items-center">
                            <i class="fas fa-chevron-left mr-1 text-xs"></i>
                            <span class="hidden sm:inline">Nyuma</span>
                        </a>
                    @endif

                    <!-- Page Numbers -->
                    <div class="flex items-center space-x-1">
                        @php
                            // Smart pagination - show limited pages on mobile
                            $maxPages = 5;
                            $current = $manunuzi->currentPage();
                            $last = $manunuzi->lastPage();
                            
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
                            <a href="{{ $manunuzi->url(1) }}" class="px-2 lg:px-3 py-1 lg:py-1.5 rounded-lg border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs lg:text-sm transition">
                                1
                            </a>
                            @if($startPage > 2)
                                <span class="px-1 lg:px-2 text-gray-400 text-xs lg:text-sm">...</span>
                            @endif
                        @endif

                        @for($page = $startPage; $page <= $endPage; $page++)
                            @if($page == $manunuzi->currentPage())
                                <span class="px-2 lg:px-3 py-1 lg:py-1.5 rounded-lg bg-green-600 text-white font-semibold text-xs lg:text-sm">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $manunuzi->url($page) }}" class="px-2 lg:px-3 py-1 lg:py-1.5 rounded-lg border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs lg:text-sm transition">
                                    {{ $page }}
                                </a>
                            @endif
                        @endfor

                        @if($endPage < $last)
                            @if($endPage < $last - 1)
                                <span class="px-1 lg:px-2 text-gray-400 text-xs lg:text-sm">...</span>
                            @endif
                            <a href="{{ $manunuzi->url($last) }}" class="px-2 lg:px-3 py-1 lg:py-1.5 rounded-lg border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs lg:text-sm transition">
                                {{ $last }}
                            </a>
                        @endif
                    </div>

                    <!-- Next Button -->
                    @if($manunuzi->hasMorePages())
                        <a href="{{ $manunuzi->nextPageUrl() }}" class="px-2 lg:px-3 py-1 lg:py-1.5 rounded-lg border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs lg:text-sm transition flex items-center">
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
                    <select class="border border-gray-300 rounded-lg p-1 lg:p-1.5 text-xs lg:text-sm focus:ring-2 focus:ring-green-500" onchange="changePerPage(this)">
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
                    @if($manunuzi->onFirstPage())
                        <span class="px-3 py-1.5 rounded-lg border text-gray-400 text-xs cursor-not-allowed flex items-center">
                            <i class="fas fa-chevron-left mr-1"></i>
                        </span>
                    @else
                        <a href="{{ $manunuzi->previousPageUrl() }}" class="px-3 py-1.5 rounded-lg border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs transition flex items-center">
                            <i class="fas fa-chevron-left mr-1"></i> Nyuma
                        </a>
                    @endif

                    <span class="text-xs text-gray-600">
                        Uk. {{ $manunuzi->currentPage() }} / {{ $manunuzi->lastPage() }}
                    </span>

                    @if($manunuzi->hasMorePages())
                        <a href="{{ $manunuzi->nextPageUrl() }}" class="px-3 py-1.5 rounded-lg border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs transition flex items-center">
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

        <!-- Summary Cards -->
        @if($manunuzi->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 lg:gap-4 mt-4 lg:mt-6">
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-lg lg:rounded-xl p-3 lg:p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="p-1.5 lg:p-2 rounded-lg bg-green-100 text-green-600 mr-2 lg:mr-3">
                            <i class="fas fa-shopping-cart text-sm lg:text-base"></i>
                        </div>
                        <div>
                            <h4 class="text-xs lg:text-sm font-semibold text-green-800">Manunuzi Ya Leo</h4>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm lg:text-base xl:text-lg font-bold text-green-700">
                            {{ $manunuzi->where('created_at', '>=', today())->count() }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg lg:rounded-xl p-3 lg:p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="p-1.5 lg:p-2 rounded-lg bg-blue-100 text-blue-600 mr-2 lg:mr-3">
                            <i class="fas fa-boxes text-sm lg:text-base"></i>
                        </div>
                        <div>
                            <h4 class="text-xs lg:text-sm font-semibold text-blue-800">Bidhaa Zilizonunuliwa</h4>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm lg:text-base xl:text-lg font-bold text-blue-700">{{ $manunuzi->sum('idadi') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-purple-50 to-pink-50 border border-purple-200 rounded-lg lg:rounded-xl p-3 lg:p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="p-1.5 lg:p-2 rounded-lg bg-purple-100 text-purple-600 mr-2 lg:mr-3">
                            <i class="fas fa-money-bill-wave text-sm lg:text-base"></i>
                        </div>
                        <div>
                            <h4 class="text-xs lg:text-sm font-semibold text-purple-800">Gharama Ya Leo</h4>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm lg:text-base xl:text-lg font-bold text-purple-700">
                            TZS {{ number_format($manunuzi->where('created_at', '>=', today())->sum('bei'), 0) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
function changePerPage(select) {
    const perPage = select.value;
    const url = new URL(window.location.href);
    url.searchParams.set('per_page', perPage);
    window.location.href = url.toString();
}

function filterTable() {
    const searchTerm = document.getElementById('searchQuery').value.toLowerCase();
    const rows = document.querySelectorAll('.table-row');
    
    rows.forEach(row => {
        const searchableText = row.getAttribute('data-searchable') || '';
        if (searchableText.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>

<!-- Ingiza Manunuzi Tab -->
<div id="content-ingiza" class="tab-content bg-white rounded-xl md:rounded-2xl shadow-md md:shadow-lg border border-gray-100 p-3 md:p-6 card-hover" style="display: none;">
    <h2 class="text-lg md:text-xl font-bold text-gray-800 mb-3 md:mb-6">Ingiza Manunuzi Mpya</h2>
    <form method="POST" action="{{ route('manunuzi.store') }}" class="space-y-4" id="purchase-form">
        @csrf
        
        <!-- Row 1: Bidhaa with Search (Full Width) -->
        <div class="space-y-2">
            <label class="block text-xs md:text-sm font-semibold text-green-800">Chagua Bidhaa *</label>
            <div class="relative">
                <input 
                    type="text" 
                    id="bidhaa-search"
                    placeholder="Tafuta bidhaa..." 
                    class="w-full border border-gray-300 rounded-lg p-2.5 md:p-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm md:text-base"
                    autocomplete="off"
                >
                <select 
                    name="bidhaa_id" 
                    id="bidhaa-select"
                    class="hidden absolute top-full left-0 right-0 z-10 w-full border border-gray-300 rounded-lg bg-white shadow-lg max-h-40 md:max-h-48 overflow-y-auto text-sm md:text-base"
                    size="3"
                    required
                >
                    <option value="">Chagua bidhaa...</option>
                    @foreach($bidhaa as $b)
                    <option 
                        value="{{ $b->id }}"
                        data-jina="{{ e($b->jina) }}"
                        data-aina="{{ e($b->aina) }}"
                        data-kipimo="{{ e($b->kipimo) }}"
                    >
                        {{ $b->jina }} - {{ $b->aina }} - {{ $b->kipimo }}
                    </option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                    <i class="fas fa-chevron-down text-gray-400 text-sm"></i>
                </div>
            </div>
            <div id="selected-bidhaa" class="hidden mt-1">
                <div class="bg-green-50 border border-green-200 rounded-lg p-2">
                    <div class="flex justify-between items-center">
                        <div class="truncate">
                            <span class="font-semibold text-green-800 text-sm" id="selected-jina"></span>
                            <span class="text-green-600 text-xs ml-1" id="selected-aina"></span>
                        </div>
                        <button type="button" onclick="clearBidhaaSelection()" class="text-red-500 hover:text-red-700 text-sm">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 2-4: 3-column layout for form fields -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 md:gap-4">
            
            <!-- Column 1 -->
            <div class="space-y-3 md:space-y-4">
                <!-- Idadi -->
                <div class="space-y-1">
                    <label class="block text-xs md:text-sm font-semibold text-green-800">Idadi *</label>
                    <input 
                        type="number" 
                        name="idadi" 
                        placeholder="Idadi" 
                        class="w-full border border-gray-300 rounded-lg p-2.5 md:p-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm md:text-base" 
                        required
                        min="1"
                    >
                </div>

                <!-- Bei Type -->
                <div class="space-y-1">
                    <label class="block text-xs md:text-sm font-semibold text-green-800">Aina ya Bei</label>
                    <div class="relative">
                        <select 
                            name="bei_type" 
                            class="w-full border border-gray-300 rounded-lg p-2.5 md:p-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent appearance-none bg-white text-sm md:text-base"
                        >
                            <option value="kwa_zote">Kwa Zote</option>
                            <option value="rejareja">Rejareja</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400 text-sm"></i>
                        </div>
                    </div>
                </div>

                <!-- Bei Amount -->
                <div class="space-y-1">
                    <label class="block text-xs md:text-sm font-semibold text-green-800">Bei-nunua (TZS) *</label>
                    <input 
                        type="number" 
                        step="0.01" 
                        name="bei" 
                        placeholder="Kiasi" 
                        class="w-full border border-gray-300 rounded-lg p-2.5 md:p-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm md:text-base" 
                        required
                    >
                </div>
            </div>

            <!-- Column 2 -->
            <div class="space-y-3 md:space-y-4">
                <!-- Expiry -->
                <div class="space-y-1">
                    <label class="block text-xs md:text-sm font-semibold text-green-800">Tarehe ya Mwisho</label>
                    <input 
                        type="date" 
                        name="expiry" 
                        class="w-full border border-gray-300 rounded-lg p-2.5 md:p-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm md:text-base"
                    >
                </div>

                <!-- Saplaya -->
                <div class="space-y-1">
                    <label class="block text-xs md:text-sm font-semibold text-green-800">Jina la Msaplaya</label>
                    <input 
                        type="text" 
                        name="saplaya" 
                        placeholder="Jina la msaplaya" 
                        class="w-full border border-gray-300 rounded-lg p-2.5 md:p-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm md:text-base"
                    >
                </div>

                <!-- Simu -->
                <div class="space-y-1">
                    <label class="block text-xs md:text-sm font-semibold text-green-800">Namba ya Simu</label>
                    <input 
                        type="text" 
                        name="simu" 
                        placeholder="Namba ya simu" 
                        class="w-full border border-gray-300 rounded-lg p-2.5 md:p-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm md:text-base"
                    >
                </div>
            </div>

            <!-- Column 3 -->
            <div class="space-y-3 md:space-y-4">
                <!-- Maelezo (Taller) -->
                <div class="space-y-1">
                    <label class="block text-xs md:text-sm font-semibold text-green-800">Maelezo ya Ziada</label>
                    <textarea 
                        name="mengineyo" 
                        placeholder="Andika maelezo ya ziada..." 
                        rows="6"
                        class="w-full border border-gray-300 rounded-lg p-2.5 md:p-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm md:text-base resize-none"
                    ></textarea>
                </div>

                <!-- Buttons (Stacked in Column 3) -->
                <div class="pt-2">
                    <div class="space-y-2">
                        <button 
                            type="submit" 
                            class="w-full bg-gradient-to-r from-green-600 to-green-700 text-white px-4 py-2.5 rounded-lg hover:from-green-700 hover:to-green-800 transition-all duration-300 flex items-center justify-center shadow text-sm"
                        >
                            <i class="fas fa-save mr-2 text-sm"></i>
                            <span>Hifadhi Manunuzi</span>
                        </button>
                        <button 
                            type="reset" 
                            onclick="clearForm()"
                            class="w-full bg-gray-200 text-gray-700 px-4 py-2.5 rounded-lg hover:bg-gray-300 transition-colors flex items-center justify-center text-sm"
                        >
                            <i class="fas fa-redo mr-2 text-sm"></i>
                            <span>Safisha Fomu</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="hidden fixed inset-0 z-50 flex items-center justify-center modal-overlay p-3 md:p-0">
    <div class="bg-white rounded-xl md:rounded-2xl shadow-xl w-full max-w-2xl mx-auto max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
        <div class="p-4 md:p-6 border-b border-gray-200">
            <h3 class="text-base md:text-lg font-semibold text-gray-800">Badili Taarifa za Manunuzi</h3>
        </div>
        <form id="editForm" method="POST" class="p-4 md:p-6">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4">
                <!-- Bidhaa with Search -->
                <div class="relative col-span-2">
                    <label class="block text-sm font-semibold text-green-800 mb-2">Chagua Bidhaa *</label>
                    <div class="relative">
                        <input 
                            type="text" 
                            id="edit-bidhaa-search"
                            placeholder="Tafuta bidhaa..." 
                            class="w-full border border-gray-300 rounded-lg p-3 md:p-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm md:text-base"
                            autocomplete="off"
                        >
                        <select 
                            name="bidhaa_id" 
                            id="edit_bidhaa_id"
                            class="hidden absolute top-full left-0 right-0 z-10 w-full border border-gray-300 rounded-lg bg-white shadow-lg max-h-60 overflow-y-auto text-sm md:text-base"
                            size="5"
                            required
                        >
                            @foreach($bidhaa as $b)
                            <option 
                                value="{{ $b->id }}"
                                data-jina="{{ e($b->jina) }}"
                                data-aina="{{ e($b->aina) }}"
                                data-kipimo="{{ e($b->kipimo) }}"
                            >
                                {{ $b->jina }} - {{ $b->aina }} - {{ $b->kipimo }}
                            </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <!-- Idadi -->
                <div class="relative">
                    <label class="block text-sm font-semibold text-green-800 mb-2">Idadi *</label>
                    <input 
                        type="number" 
                        name="idadi" 
                        id="edit_idadi"
                        class="w-full border border-gray-300 rounded-lg p-3 md:p-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm md:text-base" 
                        required
                    >
                </div>

                <!-- Bei -->
                <div class="relative">
                    <label class="block text-sm font-semibold text-green-800 mb-2">Bei (TZS) *</label>
                    <input 
                        type="number" 
                        step="0.01" 
                        name="bei" 
                        id="edit_bei"
                        class="w-full border border-gray-300 rounded-lg p-3 md:p-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm md:text-base" 
                        required
                    >
                </div>

                <!-- Expiry -->
                <div class="relative">
                    <label class="block text-sm font-semibold text-green-800 mb-2">Expiry</label>
                    <input 
                        type="date" 
                        name="expiry" 
                        id="edit_expiry"
                        class="w-full border border-gray-300 rounded-lg p-3 md:p-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm md:text-base"
                    >
                </div>

                <!-- Saplaya -->
                <div class="relative">
                    <label class="block text-sm font-semibold text-green-800 mb-2">Saplaya</label>
                    <input 
                        type="text" 
                        name="saplaya" 
                        id="edit_saplaya"
                        class="w-full border border-gray-300 rounded-lg p-3 md:p-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm md:text-base"
                    >
                </div>

                <!-- Simu -->
                <div class="relative">
                    <label class="block text-sm font-semibold text-green-800 mb-2">Simu</label>
                    <input 
                        type="text" 
                        name="simu" 
                        id="edit_simu"
                        class="w-full border border-gray-300 rounded-lg p-3 md:p-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm md:text-base"
                    >
                </div>

                <!-- Maelezo -->
                <div class="relative col-span-2">
                    <label class="block text-sm font-semibold text-green-800 mb-2">Maelezo ya Ziada</label>
                    <textarea 
                        name="mengineyo" 
                        id="edit_mengineyo"
                        rows="3"
                        class="w-full border border-gray-300 rounded-lg p-3 md:p-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm md:text-base"
                    ></textarea>
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

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 z-50 flex items-center justify-center modal-overlay p-3 md:p-0">
    <div class="bg-white rounded-xl md:rounded-2xl shadow-xl w-full max-w-md mx-auto" onclick="event.stopPropagation()">
        <div class="p-4 md:p-6 border-b border-gray-200">
            <h3 class="text-base md:text-lg font-semibold text-gray-800">Thibitisha Kufuta Manunuzi</h3>
        </div>
        <div class="p-4 md:p-6">
            <div class="flex items-center justify-center mb-3 md:mb-4">
                <div class="p-2 md:p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-exclamation-triangle text-lg md:text-xl"></i>
                </div>
            </div>
            <p class="text-gray-700 mb-4 md:mb-6 text-center text-sm md:text-base">
                Una uhakika unataka kufuta manunuzi ya "<span id="delete_name" class="font-semibold"></span>"?
                <br>Hatua hii haiwezi kutenduliwa.
            </p>
            <div class="flex flex-col sm:flex-row justify-center space-y-3 sm:space-y-0 sm:space-x-3">
                <button 
                    onclick="closeDeleteModal()"
                    class="px-4 md:px-6 py-2 md:py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors text-sm md:text-base"
                >
                    Ghairi
                </button>
                <form id="deleteForm" method="POST">
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

@push('scripts')
<script>
class BidhaaSearchManager {
    constructor() {
        this.bidhaaList = @json($bidhaa);
        this.init();
    }

    init() {
        this.initMainForm();
        this.initEditForm();
        this.bindDateInputs();
    }

    initMainForm() {
        const searchInput = document.getElementById('bidhaa-search');
        const select = document.getElementById('bidhaa-select');
        const selectedDiv = document.getElementById('selected-bidhaa');
        
        if (!searchInput || !select) return;

        // Show dropdown when search input is focused
        searchInput.addEventListener('focus', () => {
            select.classList.remove('hidden');
            this.filterOptions(searchInput.value, select);
        });

        // Filter options based on search input
        searchInput.addEventListener('input', (e) => {
            this.filterOptions(e.target.value, select);
        });

        // Update search input when option is selected
        select.addEventListener('change', () => {
            if (!select.value) return;

            const selectedOption = select.options[select.selectedIndex];
            const jina = selectedOption.dataset.jina;
            const aina = selectedOption.dataset.aina;
            
            // Show selected product info
            document.getElementById('selected-jina').textContent = jina;
            document.getElementById('selected-aina').textContent = `(${aina})`;
            selectedDiv.classList.remove('hidden');
            
            // Hide dropdown
            select.classList.add('hidden');
            searchInput.value = `${jina} (${aina})`;
        });

        // Hide dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('#bidhaa-search') && !e.target.closest('#bidhaa-select')) {
                select.classList.add('hidden');
            }
        });
    }

    initEditForm() {
        const searchInput = document.getElementById('edit-bidhaa-search');
        const select = document.getElementById('edit_bidhaa_id');
        
        if (!searchInput || !select) return;

        // Show dropdown when search input is focused
        searchInput.addEventListener('focus', () => {
            select.classList.remove('hidden');
            this.filterOptions(searchInput.value, select);
        });

        // Filter options based on search input
        searchInput.addEventListener('input', (e) => {
            this.filterOptions(e.target.value, select);
        });

        // Update search input when option is selected
        select.addEventListener('change', () => {
            if (!select.value) return;

            const selectedOption = select.options[select.selectedIndex];
            const jina = selectedOption.dataset.jina;
            const aina = selectedOption.dataset.aina;
            
            searchInput.value = `${jina} (${aina})`;
            select.classList.add('hidden');
        });

        // Hide dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('#edit-bidhaa-search') && !e.target.closest('#edit_bidhaa_id')) {
                select.classList.add('hidden');
            }
        });
    }

    filterOptions(searchTerm, select) {
        const filter = searchTerm.toLowerCase();
        const options = select.getElementsByTagName('option');

        for (let i = 0; i < options.length; i++) {
            const jina = options[i].dataset.jina?.toLowerCase() || '';
            const aina = options[i].dataset.aina?.toLowerCase() || '';
            const searchText = `${jina} ${aina}`;

            options[i].style.display = searchText.includes(filter)
                ? ''
                : 'none';
        }
    }

    bindDateInputs() {
        // Set today as default for expiry date
        const today = new Date().toISOString().split('T')[0];
        const expiryInputs = document.querySelectorAll('input[name="expiry"]');
        expiryInputs.forEach(input => {
            if (!input.value) {
                input.value = today;
            }
        });
    }
}

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
    
    // Auto-focus on search input when switching to "ingiza" tab
    if (tabName === 'ingiza') {
        setTimeout(() => {
            const searchInput = document.getElementById('bidhaa-search');
            if (searchInput) searchInput.focus();
        }, 100);
    }
}

function clearBidhaaSelection() {
    const searchInput = document.getElementById('bidhaa-search');
    const select = document.getElementById('bidhaa-select');
    const selectedDiv = document.getElementById('selected-bidhaa');
    
    searchInput.value = '';
    select.value = '';
    selectedDiv.classList.add('hidden');
}

function clearForm() {
    document.getElementById('purchase-form').reset();
    clearBidhaaSelection();
    
    // Reset expiry date to today
    const today = new Date().toISOString().split('T')[0];
    const expiryInput = document.querySelector('input[name="expiry"]');
    if (expiryInput) {
        expiryInput.value = today;
    }
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

// Edit Item
function editItem(item) {
    const bidhaaSelect = document.getElementById('edit_bidhaa_id');
    const searchInput = document.getElementById('edit-bidhaa-search');
    
    // Find and select the bidhaa
    if (bidhaaSelect) {
        for (let i = 0; i < bidhaaSelect.options.length; i++) {
            if (bidhaaSelect.options[i].value == item.bidhaa_id) {
                bidhaaSelect.selectedIndex = i;
                const selectedOption = bidhaaSelect.options[i];
                searchInput.value = `${selectedOption.dataset.jina} (${selectedOption.dataset.aina})`;
                break;
            }
        }
    }
    
    document.getElementById('edit_idadi').value = item.idadi || '';
    document.getElementById('edit_bei').value = item.bei || '';
    document.getElementById('edit_expiry').value = item.expiry ? item.expiry.split('T')[0] : '';
    document.getElementById('edit_saplaya').value = item.saplaya || '';
    document.getElementById('edit_simu').value = item.simu || '';
    document.getElementById('edit_mengineyo').value = item.mengineyo || '';
    
    document.getElementById('editForm').action = '/manunuzi/' + item.id;
    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

// Delete Item
function deleteItem(id, name) {
    document.getElementById('delete_name').textContent = name;
    document.getElementById('deleteForm').action = '/manunuzi/' + id;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

// Close modals when clicking outside
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('modal-overlay')) {
        closeEditModal();
        closeDeleteModal();
    }
});

// Close modals with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeEditModal();
        closeDeleteModal();
    }
});

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    new BidhaaSearchManager();
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
    box-shadow: 0 5px 15px -5px rgba(0, 0, 0, 0.1);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .min-w-max {
        min-width: max-content;
    }
    
    .min-w-\[800px\] {
        min-width: 800px;
    }
    
    input, select, textarea, button {
        font-size: 16px !important;
    }
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
    
    #content-ingiza {
        display: none !important;
    }
}
</style>
@endpush