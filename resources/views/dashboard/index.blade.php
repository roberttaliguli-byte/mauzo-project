@extends('layouts.app')

@section('title', 'Mfumo wa Mauzo - Dashboard')

@section('page-title')
    <div class="text-xl md:text-3xl font-bold text-gray-800 px-2">
        Karibu, {{ $company->company_name ?? 'Mfumo wako' }}
    </div>
@endsection

@section('content')
<div class="space-y-3 md:space-y-6 px-2 md:px-0">
    <!-- Financial Metrics Grid -->
    <div>
        <div class="flex items-center justify-between mb-2 md:mb-4">
            <h2 class="text-sm md:text-lg font-bold text-gray-800 flex items-center gap-2">
                <div class="w-1 h-4 md:w-2 md:h-5 bg-gradient-to-b from-blue-500 to-purple-500 rounded-full"></div>
                Muhtasari wa Leo
            </h2>
            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">
                {{ now()->format('d/m/Y') }}
            </span>
        </div>
        
        <!-- Mobile: 2 columns, Desktop: 4 columns -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 md:gap-4">
            <!-- Mapato Leo -->
            <div class="group relative">
                <div class="absolute inset-0 bg-gradient-to-r from-amber-500 to-amber-600 rounded-lg blur opacity-20 group-hover:opacity-30 transition duration-300"></div>
                <div class="relative bg-gradient-to-br from-amber-400 via-amber-500 to-amber-600 p-2 md:p-4 rounded-lg shadow-md text-white">
                    <div class="flex justify-between items-start mb-1 md:mb-3">
                        <div class="p-1 md:p-2 bg-white/20 rounded-lg">
                            <i class="fas fa-money-bill-wave text-xs md:text-base"></i>
                        </div>
                        <span class="text-[10px] md:text-xs text-white/70 font-medium">Mapato</span>
                    </div>
                    <div class="font-bold text-sm md:text-xl mb-0.5 md:mb-1">
                        <span class="text-xs md:text-base">{{ number_format($mapatoLeo, 0) }}</span>
                    </div>
                    <div class="flex items-center text-white/80 text-[9px] md:text-xs">
                        <i class="fas fa-calendar-day mr-1"></i>
                        <span>Leo</span>
                    </div>
                </div>
            </div>

            <!-- Matumizi Leo -->
            <div class="group relative">
                <div class="absolute inset-0 bg-gradient-to-r from-emerald-500 to-emerald-800 rounded-lg blur opacity-20 group-hover:opacity-30 transition duration-300"></div>
                <div class="relative bg-gradient-to-br from-emerald-400 via-emerald-500 to-emerald-600 p-2 md:p-4 rounded-lg shadow-md text-white">
                    <div class="flex justify-between items-start mb-1 md:mb-3">
                        <div class="p-1 md:p-2 bg-white/20 rounded-lg">
                            <i class="fas fa-receipt text-xs md:text-base"></i>
                        </div>
                        <span class="text-[10px] md:text-xs text-white/70 font-medium">Matumizi</span>
                    </div>
                    <div class="font-bold text-sm md:text-xl mb-0.5 md:mb-1">
                        <span class="text-xs md:text-base">{{ number_format($matumiziLeo, 0) }}</span>
                    </div>
                    <div class="flex items-center text-white/80 text-[9px] md:text-xs">
                        <i class="fas fa-calendar-day mr-1"></i>
                        <span>Leo</span>
                    </div>
                </div>
            </div>

            <!-- Fedha Leo -->
            <div class="group relative">
                <div class="absolute inset-0 bg-gradient-to-r from-amber-500 to-amber-600 rounded-lg blur opacity-20 group-hover:opacity-30 transition duration-300"></div>
                <div class="relative bg-gradient-to-br from-amber-400 via-amber-500 to-amber-600 p-2 md:p-4 rounded-lg shadow-md text-white">
                    <div class="flex justify-between items-start mb-1 md:mb-3">
                        <div class="p-1 md:p-2 bg-white/20 rounded-lg">
                            <i class="fas fa-wallet text-xs md:text-base"></i>
                        </div>
                        <span class="text-[10px] md:text-xs text-white/70 font-medium">Fedha</span>
                    </div>
                    <div class="font-bold text-sm md:text-xl mb-0.5 md:mb-1">
                        <span class="text-xs md:text-base">{{ number_format($fedhaLeo, 0) }}</span>
                    </div>
                    <div class="flex items-center text-white/80 text-[9px] md:text-xs">
                        <i class="fas fa-calendar-day mr-1"></i>
                        <span>Leo</span>
                    </div>
                </div>
            </div>

            <!-- Faida Halisi Leo -->
            <div class="group relative">
                <div class="absolute inset-0 bg-gradient-to-r from-emerald-500 to-emerald-700 rounded-lg blur opacity-20 group-hover:opacity-30 transition duration-300"></div>
                <div class="relative bg-gradient-to-br from-emerald-400 via-emerald-500 to-emerald-600 p-2 md:p-4 rounded-lg shadow-md text-white">
                    <div class="flex justify-between items-start mb-1 md:mb-3">
                        <div class="p-1 md:p-2 bg-white/20 rounded-lg">
                            <i class="fas fa-chart-pie text-xs md:text-base"></i>
                        </div>
                        <span class="text-[10px] md:text-xs text-white/70 font-medium">Faida</span>
                    </div>
                    @php
                        $faidaHalisiLeo = $jumlaFaida - $matumiziLeo;
                    @endphp
                    <div class="font-bold text-sm md:text-xl mb-0.5 md:mb-1">
                        <span class="text-xs md:text-base">{{ number_format($faidaHalisiLeo, 0) }}</span>
                    </div>
                    <div class="flex items-center text-white/80 text-[9px] md:text-xs">
                        <i class="fas fa-calendar-day mr-1"></i>
                        <span>Leo</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Overview & Top Products -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 md:gap-5">
        <!-- Left Side: Stock Overview -->
        <div class="bg-emerald-50 rounded-lg shadow-sm border border-gray-100 p-3 md:p-5">
            <div class="flex items-center justify-between mb-2 md:mb-4">
                <h3 class="text-sm md:text-base font-bold text-gray-800 flex items-center gap-2">
                    <div class="w-1 h-4 md:w-2 md:h-5 bg-gradient-to-b from-green-500 to-emerald-500 rounded-full"></div>
                    Muhtasari wa Bidhaa
                </h3>
                <span class="text-[10px] md:text-xs bg-white px-2 py-1 rounded-full text-emerald-600">
                    <i class="fas fa-boxes mr-1"></i> Jumla
                </span>
            </div>
            
            <div class="grid grid-cols-3 gap-1 md:gap-3">
                <div class="text-center p-1 md:p-3 rounded-lg bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200">
                    <div class="p-1 md:p-2 bg-white rounded-full shadow-sm inline-flex mb-1 md:mb-2">
                        <i class="fas fa-boxes text-blue-600 text-[10px] md:text-base"></i>
                    </div>
                    <div class="text-xs md:text-lg font-bold text-gray-800">{{ number_format($jumlaBidhaa) }}</div>
                    <div class="text-[8px] md:text-xs text-gray-600 truncate">Aina za Bidhaa</div>
                </div>
                
                <div class="text-center p-1 md:p-3 rounded-lg bg-gradient-to-br from-green-50 to-green-100 border border-green-200">
                    <div class="p-1 md:p-2 bg-white rounded-full shadow-sm inline-flex mb-1 md:mb-2">
                        <i class="fas fa-layer-group text-green-600 text-[10px] md:text-base"></i>
                    </div>
                    <div class="text-xs md:text-lg font-bold text-gray-800">{{ number_format($jumlaIdadi, 2) }}</div>
                    <div class="text-[8px] md:text-xs text-gray-600 truncate">Jumla Idadi</div>
                </div>
                
                <div class="text-center p-1 md:p-3 rounded-lg bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200">
                    <div class="p-1 md:p-2 bg-white rounded-full shadow-sm inline-flex mb-1 md:mb-2">
                        <i class="fas fa-money-bill text-purple-600 text-[10px] md:text-base"></i>
                    </div>
                    <div class="text-xs md:text-lg font-bold text-gray-800">{{ number_format($thamani, 0) }}</div>
                    <div class="text-[8px] md:text-xs text-gray-600 truncate">Thamani</div>
                </div>
            </div>
        </div>

        <!-- Right Side: Top Products -->
        <div class="bg-purple-50 rounded-lg shadow-sm border border-gray-100 p-3 md:p-5">
            <div class="flex items-center justify-between mb-2 md:mb-4">
                <h3 class="text-sm md:text-base font-bold text-gray-800 flex items-center gap-2">
                    <div class="w-1 h-4 md:w-2 md:h-5 bg-gradient-to-b from-indigo-500 to-purple-500 rounded-full"></div>
                    Mauzo Makubwa
                </h3>
                <span class="text-[10px] md:text-xs font-medium text-white bg-gradient-to-r from-indigo-500 to-purple-500 px-2 py-1 rounded-full">
                    <i class="fas fa-crown mr-1 text-[8px] md:text-xs"></i> Top 3
                </span>
            </div>
            
            <div class="space-y-2 md:space-y-3">
                @forelse($bidhaaTopSales as $index => $bidhaa)
                    <div class="flex items-center justify-between p-2 md:p-3 rounded-lg bg-white/50 hover:bg-white transition-colors duration-200 border border-gray-100">
                        <div class="flex items-center gap-2 md:gap-3">
                            <span class="w-6 h-6 md:w-8 md:h-8 flex items-center justify-center rounded-full 
                                @if($index === 0) bg-gradient-to-br from-amber-400 to-amber-500 text-white
                                @elseif($index === 1) bg-gradient-to-br from-gray-400 to-gray-500 text-white
                                @else bg-gradient-to-br from-amber-700 to-amber-800 text-white @endif
                                text-[10px] md:text-xs font-bold">
                                {{ $index + 1 }}
                            </span>
                            <div>
                                <div class="font-semibold text-gray-800 text-xs md:text-sm">{{ Str::limit($bidhaa->jina, 15) }}</div>
                                <div class="text-[8px] md:text-xs text-gray-500">Bidhaa</div>
                            </div>
                        </div>
                        <span class="text-[8px] md:text-xs bg-gradient-to-r from-indigo-500 to-purple-500 text-white px-2 py-1 rounded-full">
                            {{ number_format($bidhaa->mauzos_sum_idadi ?? 0) }}
                        </span>
                    </div>
                @empty
                    <div class="text-center py-4 md:py-5">
                        <div class="inline-flex p-2 md:p-3 rounded-lg bg-gray-100 text-gray-400 mb-2">
                            <i class="fas fa-chart-bar text-sm md:text-lg"></i>
                        </div>
                        <p class="text-xs text-gray-500">Hakuna mauzo kwa sasa</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Stock Status & Debt Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 md:gap-5">
        <!-- Stock Status -->
        <div class="bg-emerald-50 rounded-lg shadow-sm border border-gray-100 p-3 md:p-5">
            <h3 class="text-sm md:text-base font-bold text-gray-800 flex items-center gap-2 mb-2 md:mb-4">
                <div class="w-1 h-4 md:w-2 md:h-5 bg-gradient-to-b from-emerald-500 to-teal-500 rounded-full"></div>
                Hali ya Bidhaa
            </h3>
            
            <div class="grid grid-cols-3 gap-1 md:gap-3">
                <div class="text-center p-2 md:p-3 rounded-lg bg-gradient-to-br from-green-50 to-emerald-50 border border-green-200">
                    <div class="inline-flex p-1 md:p-2 rounded-full bg-green-100 text-green-600 mb-1">
                        <i class="fas fa-check-circle text-[10px] md:text-sm"></i>
                    </div>
                    <div class="text-xs md:text-lg font-bold text-gray-800">{{ number_format($bidhaaZilizopo) }}</div>
                    <div class="text-[8px] md:text-xs text-gray-600">Zilizopo</div>
                </div>
                
                <div class="text-center p-2 md:p-3 rounded-lg bg-gradient-to-br from-red-50 to-rose-50 border border-red-200">
                    <div class="inline-flex p-1 md:p-2 rounded-full bg-red-100 text-red-600 mb-1">
                        <i class="fas fa-times-circle text-[10px] md:text-sm"></i>
                    </div>
                    <div class="text-xs md:text-lg font-bold text-gray-800">{{ number_format($bidhaaZimeisha) }}</div>
                    <div class="text-[8px] md:text-xs text-gray-600">Zimeisha</div>
                </div>
                
                <div class="text-center p-2 md:p-3 rounded-lg bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-200">
                    <div class="inline-flex p-1 md:p-2 rounded-full bg-amber-100 text-amber-600 mb-1">
                        <i class="fas fa-exclamation-triangle text-[10px] md:text-sm"></i>
                    </div>
                    <div class="text-xs md:text-lg font-bold text-gray-800">{{ number_format($bidhaaKaribiaKuisha) }}</div>
                    <div class="text-[8px] md:text-xs text-gray-600">Karibia Kuisha</div>
                </div>
            </div>
        </div>
        
        <!-- Debt Overview -->
        <div class="bg-purple-50 rounded-lg shadow-sm border border-gray-100 p-3 md:p-5">
            <h3 class="text-sm md:text-base font-bold text-gray-800 flex items-center gap-2 mb-2 md:mb-4">
                <div class="w-1 h-4 md:w-2 md:h-5 bg-gradient-to-b from-rose-500 to-pink-500 rounded-full"></div>
                Muhtasari wa Madeni
            </h3>
            
            <div class="grid grid-cols-2 gap-2 md:gap-3">
                <div class="text-center p-2 md:p-4 rounded-lg bg-gradient-to-br from-red-50 to-rose-50 border border-red-200">
                    <div class="inline-flex p-1 md:p-2 rounded-full bg-red-100 text-red-600 mb-1">
                        <i class="fas fa-money-bill-wave text-[10px] md:text-sm"></i>
                    </div>
                    <div class="text-xs md:text-lg font-bold text-gray-800">
                        {{ number_format($jumlaMadeni, 0) }}
                    </div>
                    <div class="text-[8px] md:text-xs text-gray-600">Jumla ya Madeni</div>
                </div>
                
                <div class="text-center p-2 md:p-4 rounded-lg bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-200">
                    <div class="inline-flex p-1 md:p-2 rounded-full bg-amber-100 text-amber-600 mb-1">
                        <i class="fas fa-users text-[10px] md:text-sm"></i>
                    </div>
                    <div class="text-xs md:text-lg font-bold text-gray-800">{{ number_format($idadiMadeni) }}</div>
                    <div class="text-[8px] md:text-xs text-gray-600">Idadi ya Madeni</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Summary -->
    <div class="bg-gradient-to-r from-amber-500 to-amber-600 rounded-lg p-3 md:p-5 shadow-md text-white">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-2 md:gap-0">
            <div class="flex items-start gap-2 md:gap-3">
                <div class="p-1.5 md:p-3 rounded-lg bg-white/20">
                    <i class="fas fa-chart-pie text-xs md:text-lg"></i>
                </div>
                <div>
                    <h3 class="text-xs md:text-base font-bold mb-0.5">Muhtasari wa Utendaji</h3>
                    <p class="text-[10px] md:text-xs opacity-90">
                        @if($faidaHalisiLeo > 0)
                            <i class="fas fa-arrow-up mr-1"></i> Faida ya leo: {{ number_format($faidaHalisiLeo, 0) }} Tsh
                        @else 
                            <i class="fas fa-exclamation-circle mr-1"></i> Hakuna faida ya leo
                        @endif
                    </p>
                </div>
            </div>
            <div class="text-left md:text-right pl-8 md:pl-0">
                <div class="text-[8px] md:text-xs opacity-80">Hali ya Sasa</div>
                <div class="text-xs md:text-lg font-bold">
                    @if($faidaHalisiLeo > 0)
                        <span class="text-white">✓ Imefanikiwa</span>
                    @else
                        <span class="text-amber-200">⚠ Inahitaji Mkaguzi</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Mobile optimizations */
    @media (max-width: 640px) {
        /* Better touch targets */
        .group:active {
            transform: scale(0.98);
            transition: transform 0.1s ease;
        }
        
        /* Remove hover effects on mobile */
        .group:hover .absolute {
            opacity: 0.2 !important;
        }
        
        .group:hover .relative {
            transform: none !important;
        }
        
        /* Ensure text is readable */
        .text-\[8px\] {
            line-height: 1.2;
        }
        
        /* Better spacing for small screens */
        .gap-1 {
            gap: 0.25rem;
        }
    }
    
    /* Smooth transitions */
    .transition-all {
        transition: all 0.2s ease;
    }
    
    /* Ensure buttons are touch-friendly on mobile */
    button, [role="button"] {
        min-height: 44px;
        min-width: 44px;
    }
    
    /* Better hover effects on desktop */
    @media (min-width: 768px) {
        .group:hover .relative {
            transform: scale(1.05);
            transition: transform 0.3s ease;
        }
    }
</style>
@endsection