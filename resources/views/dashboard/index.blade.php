@extends('layouts.app')

@section('title', 'Mfumo wa Mauzo - Dashboard')

@section('page-title')
    <div class="text-2xl md:text-3xl font-bold text-gray-800">
        Welcome {{ $company->company_name ?? 'Company Name' }}
    </div>
@endsection

@section('content')
<div class="space-y-4 md:space-y-8">
    <!-- Financial Metrics Grid -->
    <div class="mb-4 md:mb-8">
        <div class="flex items-center justify-between mb-4 md:mb-6">
            <h2 class="text-lg md:text-xl font-bold text-gray-800 flex items-center gap-2">
                <div class="w-1.5 md:w-2 h-4 md:h-6 bg-gradient-to-b from-blue-500 to-purple-500 rounded-full"></div>
                Muhtasari wa mauzo(Leo)
            </h2>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6">
            <!-- Mauzo Leo -->
            <div class="group relative">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl md:rounded-2xl blur opacity-25 group-hover:opacity-50 transition duration-300"></div>
                <div class="relative bg-gradient-to-br from-amber-500 to-amber-700 p-4 md:p-6 rounded-xl md:rounded-2xl shadow-lg md:shadow-xl text-white transform transition-transform duration-300 group-hover:scale-105">
                    <div class="flex justify-between items-start mb-3 md:mb-4">
                        <div class="p-2 md:p-3 bg-white/20 rounded-xl md:rounded-2xl">
                            <i class="fas fa-shopping-cart text-lg md:text-xl"></i>
                        </div>
                        <div class="text-white/70 text-xs md:text-sm font-medium">Mauzo</div>
                    </div>
                    <div class="font-bold text-xl md:text-3xl mb-1 md:mb-2">
                        <span class="text-base md:text-lg">{{ number_format($mauzoLeo, 0) }}</span> 
                        <span class="text-xs md:text-sm ml-1">Tsh</span>
                    </div>
                    <div class="flex items-center text-white/80 text-xs md:text-sm">
                        <i class="fas fa-calendar-day mr-1 md:mr-2 text-xs"></i>
                        <span>Leo</span>
                    </div>
                </div>
            </div>

            <!-- Manunuzi Leo -->
            <div class="group relative">
                <div class="absolute inset-0 bg-gradient-to-r from-green-600 to-emerald-600 rounded-xl md:rounded-2xl blur opacity-25 group-hover:opacity-50 transition duration-300"></div>
                <div class="relative bg-gradient-to-br from-green-500 to-green-600 p-4 md:p-6 rounded-xl md:rounded-2xl shadow-lg md:shadow-xl text-white transform transition-transform duration-300 group-hover:scale-105">
                    <div class="flex justify-between items-start mb-3 md:mb-4">
                        <div class="p-2 md:p-3 bg-white/20 rounded-xl md:rounded-2xl">
                            <i class="fas fa-truck-loading text-lg md:text-xl"></i>
                        </div>
                        <div class="text-white/70 text-xs md:text-sm font-medium">Manunuzi</div>
                    </div>
                    <div class="font-bold text-xl md:text-3xl mb-1 md:mb-2">
                        <span class="text-base md:text-lg">{{ number_format($manunuziLeo, 0) }}</span> 
                        <span class="text-xs md:text-sm ml-1">Tsh</span>
                    </div>
                    <div class="flex items-center text-white/80 text-xs md:text-sm">
                        <i class="fas fa-calendar-day mr-1 md:mr-2 text-xs"></i>
                        <span>Leo</span>
                    </div>
                </div>
            </div>

            <!-- Matumizi Leo -->
            <div class="group relative">
                <div class="absolute inset-0 bg-gradient-to-r from-amber-600 to-orange-600 rounded-xl md:rounded-2xl blur opacity-25 group-hover:opacity-50 transition duration-300"></div>
                <div class="relative bg-gradient-to-br from-amber-500 to-amber-600 p-4 md:p-6 rounded-xl md:rounded-2xl shadow-lg md:shadow-xl text-white transform transition-transform duration-300 group-hover:scale-105">
                    <div class="flex justify-between items-start mb-3 md:mb-4">
                        <div class="p-2 md:p-3 bg-white/20 rounded-xl md:rounded-2xl">
                            <i class="fas fa-money-bill-wave text-lg md:text-xl"></i>
                        </div>
                        <div class="text-white/70 text-xs md:text-sm font-medium">Matumizi</div>
                    </div>
                    <div class="font-bold text-xl md:text-3xl mb-1 md:mb-2">
                        <span class="text-base md:text-lg">{{ number_format($matumiziLeo, 0) }}</span> 
                        <span class="text-xs md:text-sm ml-1">Tsh</span>
                    </div>
                    <div class="flex items-center text-white/80 text-xs md:text-sm">
                        <i class="fas fa-calendar-day mr-1 md:mr-2 text-xs"></i>
                        <span>Leo</span>
                    </div>
                </div>
            </div>

            <!-- Faida Halisi Leo -->
            <div class="group relative">
                <div class="absolute inset-0 bg-gradient-to-r from-purple-600 to-pink-600 rounded-xl md:rounded-2xl blur opacity-25 group-hover:opacity-50 transition duration-300"></div>
                <div class="relative bg-gradient-to-br from-purple-500 to-purple-600 p-4 md:p-6 rounded-xl md:rounded-2xl shadow-lg md:shadow-xl text-white transform transition-transform duration-300 group-hover:scale-105">
                    <div class="flex justify-between items-start mb-3 md:mb-4">
                        <div class="p-2 md:p-3 bg-white/20 rounded-xl md:rounded-2xl">
                            <i class="fas fa-chart-line text-lg md:text-xl"></i>
                        </div>
                        <div class="text-white/70 text-xs md:text-sm font-medium">Fedha halisi</div>
                    </div>
                    <div class="font-bold text-xl md:text-3xl mb-1 md:mb-2">
                        <span class="text-base md:text-lg">{{ number_format($faidaHalisiLeo, 0) }}</span> 
                        <span class="text-xs md:text-sm ml-1">Tsh</span>
                    </div>
                    <div class="flex items-center text-white/80 text-xs md:text-sm">
                        <i class="fas fa-calendar-day mr-1 md:mr-2 text-xs"></i>
                        <span>Leo</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Overview & Top Products Side by Side -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6 mb-4 md:mb-8">
        <!-- Left Side: Stock Overview -->
        <div class="bg-emerald-50 hover-emerald rounded-xl md:rounded-2xl shadow-sm border border-gray-100 p-4 md:p-6">
            <div class="flex items-center justify-between mb-4 md:mb-6">
                <h3 class="text-base md:text-lg font-bold text-gray-800 flex items-center gap-2">
                    <div class="w-1.5 md:w-2 h-4 md:h-5 bg-gradient-to-b from-green-500 to-emerald-500 rounded-full"></div>
                    Muhtasari wa Bidhaa
                </h3>
                <div class="text-xs md:text-sm text-gray-500">
                    <i class="fas fa-boxes mr-1"></i> Jumla
                </div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 md:gap-4">
                <div class="text-center p-3 md:p-4 rounded-lg md:rounded-xl bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200">
                    <div class="p-2 md:p-3 bg-white rounded-lg md:rounded-xl shadow-sm inline-flex mb-2 md:mb-3">
                        <i class="fas fa-boxes text-blue-600 text-lg md:text-xl"></i>
                    </div>
                    <div class="text-lg md:text-2xl font-bold text-gray-800">{{ number_format($jumlaBidhaa) }}</div>
                    <div class="text-xs md:text-sm text-gray-600 mt-1">Jumla ya Bidhaa</div>
                </div>
                
                <div class="text-center p-3 md:p-4 rounded-lg md:rounded-xl bg-gradient-to-br from-green-50 to-green-100 border border-green-200">
                    <div class="p-2 md:p-3 bg-white rounded-lg md:rounded-xl shadow-sm inline-flex mb-2 md:mb-3">
                        <i class="fas fa-layer-group text-green-600 text-lg md:text-xl"></i>
                    </div>
                    <div class="text-lg md:text-2xl font-bold text-gray-800">{{ number_format($jumlaIdadi) }}</div>
                    <div class="text-xs md:text-sm text-gray-600 mt-1">Jumla ya Idadi</div>
                </div>
                
                <div class="text-center p-3 md:p-4 rounded-lg md:rounded-xl bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200">
                    <div class="p-2 md:p-3 bg-white rounded-lg md:rounded-xl shadow-sm inline-flex mb-2 md:mb-3">
                        <i class="fas fa-money-bill text-purple-600 text-lg md:text-xl"></i>
                    </div>
                    <div class="text-lg md:text-2xl font-bold text-gray-800">{{ number_format($thamani, 0) }}</div>
                    <div class="text-xs md:text-sm text-gray-600 mt-1">Thamani ya Bidhaa</div>
                </div>
            </div>
        </div>

        <!-- Right Side: Top Products -->
        <div class="bg-purple-50 rounded-xl md:rounded-2xl shadow-sm border border-gray-100 p-4 md:p-6">
            <div class="flex items-center justify-between mb-4 md:mb-6">
                <h3 class="text-base md:text-lg font-bold text-gray-800 flex items-center gap-2">
                    <div class="w-1.5 md:w-2 h-4 md:h-5 bg-gradient-to-b from-indigo-500 to-purple-500 rounded-full"></div>
                    Bidhaa Zenye Mauzo Makubwa
                </h3>
                <span class="text-xs md:text-sm font-medium text-white bg-gradient-to-r from-indigo-500 to-purple-500 px-2 md:px-3 py-1 rounded-full">
                    <i class="fas fa-crown mr-1 text-xs"></i> Top 3
                </span>
            </div>
            
            <div class="space-y-3 md:space-y-4">
                @foreach($bidhaaTopSales as $index => $bidhaa)
                    <div class="flex items-center justify-between p-3 md:p-4 rounded-lg md:rounded-xl border border-gray-100 hover:bg-gray-50 transition-colors duration-200">
                        <div class="flex items-center gap-3 md:gap-4">
                            <span class="w-8 h-8 md:w-10 md:h-10 flex items-center justify-center rounded-full 
                                @if($index === 0) bg-gradient-to-br from-amber-400 to-amber-500 text-white
                                @elseif($index === 1) bg-gradient-to-br from-gray-400 to-gray-500 text-white
                                @elseif($index === 2) bg-gradient-to-br from-amber-700 to-amber-800 text-white
                                @else bg-gray-100 text-gray-600 @endif
                                text-xs md:text-sm font-bold">
                                {{ $index + 1 }}
                            </span>
                            <div class="min-w-0">
                                <div class="font-semibold text-gray-800 text-sm md:text-base truncate">{{ $bidhaa->jina }}</div>
                                <div class="text-xs md:text-sm text-gray-500">Bidhaa</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2 md:px-3 py-1 rounded-full text-xs md:text-sm font-medium 
                                @if($index === 0) bg-green-100 text-green-800
                                @elseif($index === 1) bg-blue-100 text-blue-800
                                @else bg-purple-100 text-purple-800 @endif">
                                {{ $bidhaa->mauzos_sum_idadi ?? 0 }} mauzo
                            </span>
                        </div>
                    </div>
                @endforeach
                
                @if(count($bidhaaTopSales) === 0)
                    <div class="text-center py-6 md:py-8">
                        <div class="inline-flex p-3 md:p-4 rounded-lg md:rounded-2xl bg-gray-100 text-gray-400 mb-3 md:mb-4">
                            <i class="fas fa-chart-bar text-xl md:text-2xl"></i>
                        </div>
                        <h4 class="text-base md:text-lg font-medium text-gray-600 mb-2">Hakuna mauzo kwa sasa</h4>
                        <p class="text-gray-500 text-xs md:text-sm">Mauzo yanaonekana hapo baadaye</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Stock Status & Debt Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6 mb-4 md:mb-8">
        <!-- Stock Status -->
        <div class="bg-emerald-50 rounded-xl md:rounded-2xl shadow-sm border border-gray-100 p-4 md:p-6">
            <h3 class="text-base md:text-lg font-bold text-gray-800 flex items-center gap-2 mb-4 md:mb-6">
                <div class="w-1.5 md:w-2 h-4 md:h-5 bg-gradient-to-b from-emerald-500 to-teal-500 rounded-full"></div>
                Hali ya Bidhaa
            </h3>
            
            <div class="grid grid-cols-3 gap-3 md:gap-4">
                <div class="text-center p-3 md:p-4 rounded-lg md:rounded-xl bg-gradient-to-br from-green-50 to-emerald-50 border border-green-200 transform transition-transform duration-300 hover:scale-105">
                    <div class="inline-flex p-2 md:p-3 rounded-lg md:rounded-xl bg-green-100 text-green-600 mb-2 md:mb-3">
                        <i class="fas fa-check-circle text-sm md:text-base"></i>
                    </div>
                    <div class="text-lg md:text-2xl font-bold text-gray-800">{{ number_format($bidhaaZilizopo) }}</div>
                    <div class="text-xs md:text-sm text-gray-600 mt-1">Zilizopo</div>
                </div>
                
                <div class="text-center p-3 md:p-4 rounded-lg md:rounded-xl bg-gradient-to-br from-red-50 to-rose-50 border border-red-200 transform transition-transform duration-300 hover:scale-105">
                    <div class="inline-flex p-2 md:p-3 rounded-lg md:rounded-xl bg-red-100 text-red-600 mb-2 md:mb-3">
                        <i class="fas fa-times-circle text-sm md:text-base"></i>
                    </div>
                    <div class="text-lg md:text-2xl font-bold text-gray-800">{{ number_format($bidhaaZimeisha) }}</div>
                    <div class="text-xs md:text-sm text-gray-600 mt-1">Zimeisha</div>
                </div>
                
                <div class="text-center p-3 md:p-4 rounded-lg md:rounded-xl bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-200 transform transition-transform duration-300 hover:scale-105">
                    <div class="inline-flex p-2 md:p-3 rounded-lg md:rounded-xl bg-amber-100 text-amber-600 mb-2 md:mb-3">
                        <i class="fas fa-exclamation-triangle text-sm md:text-base"></i>
                    </div>
                    <div class="text-lg md:text-2xl font-bold text-gray-800">{{ number_format($bidhaaKaribiaKuisha) }}</div>
                    <div class="text-xs md:text-sm text-gray-600 mt-1">Karibia Kuisha</div>
                </div>
            </div>
        </div>
        
        <!-- Debt Overview -->
        <div class="bg-purple-50 rounded-xl md:rounded-2xl shadow-sm border border-gray-100 p-4 md:p-6">
            <h3 class="text-base md:text-lg font-bold text-gray-800 flex items-center gap-2 mb-4 md:mb-6">
                <div class="w-1.5 md:w-2 h-4 md:h-5 bg-gradient-to-b from-rose-500 to-pink-500 rounded-full"></div>
                Muhtasari wa Madeni
            </h3>
            
            <div class="grid grid-cols-2 gap-3 md:gap-4">
                <div class="text-center p-4 md:p-5 rounded-lg md:rounded-xl bg-gradient-to-br from-red-50 to-rose-50 border border-red-200">
                    <div class="inline-flex p-2 md:p-3 rounded-lg md:rounded-xl bg-red-100 text-red-600 mb-2 md:mb-3">
                        <i class="fas fa-money-bill-wave text-sm md:text-base"></i>
                    </div>
                    <div class="text-lg md:text-2xl font-bold text-gray-800">
                        <span class="text-base md:text-lg">{{ number_format($jumlaMadeni, 0) }}</span> 
                        <span class="text-xs md:text-sm ml-1">Tsh</span>
                    </div>
                    <div class="text-xs md:text-sm text-gray-600 mt-1">Jumla ya Madeni</div>
                </div>
                
                <div class="text-center p-4 md:p-5 rounded-lg md:rounded-xl bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-200">
                    <div class="inline-flex p-2 md:p-3 rounded-lg md:rounded-xl bg-amber-100 text-amber-600 mb-2 md:mb-3">
                        <i class="fas fa-users text-sm md:text-base"></i>
                    </div>
                    <div class="text-lg md:text-2xl font-bold text-gray-800">{{ number_format($idadiMadeni) }}</div>
                    <div class="text-xs md:text-sm text-gray-600 mt-1">Idadi ya Madeni</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Summary -->
    <div class="bg-gradient-to-r from-amber-500 to-amber-600 rounded-xl md:rounded-2xl p-4 md:p-6 shadow-lg md:shadow-xl text-white">
        <div class="flex flex-col md:flex-row items-center justify-between">
            <div class="flex items-center mb-4 md:mb-0">
                <div class="p-3 md:p-4 rounded-lg md:rounded-2xl bg-white/30 mr-3 md:mr-4">
                    <i class="fas fa-chart-pie text-lg md:text-2xl"></i>
                </div>
                <div class="min-w-0">
                    <h3 class="text-base md:text-xl font-bold mb-1">Muhtasari wa Utendaji</h3>
                    <p class="text-xs md:text-sm opacity-90">
                        @if($faidaHalisiLeo > 0)
                            <i class="fas fa-arrow-up mr-1"></i> Biashara inaendelea vizuri leo kwa faida ya {{ number_format($faidaHalisiLeo, 0) }} Tsh.
                        @else 
                            <i class="fas fa-exclamation-circle mr-1"></i> Hakuna faida ya leo. Angalia gharama na mauzo.
                        @endif
                    </p>
                </div>
            </div>
            <div class="text-right">
                <div class="text-xs md:text-sm opacity-80">Hali ya Sasa</div>
                <div class="text-lg md:text-2xl font-bold">
                    @if($faidaHalisiLeo > 0)
                        <span class="text-white">Imefanikiwa</span>
                    @else
                        <span class="text-amber-200">Inahitaji Mkaguzi</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Update current time every second
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('en-US', { hour12: false });
        document.getElementById('current-time').textContent = timeString;
    }
    
    setInterval(updateTime, 1000);
</script>

<style>
    /* Responsive adjustments */
    @media (max-width: 768px) {
        /* Ensure proper spacing on mobile */
        .hover-emerald:hover {
            transform: translateY(-2px);
        }
        
        /* Adjust font sizes for better mobile readability */
        input, select, textarea, button {
            font-size: 16px !important;
        }
    }
</style>
@endsection