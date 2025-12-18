@extends('layouts.app')

@section('title', 'Mfanyakazi - Dashboard')
@section('page-title', 'Mfanyakazi Dashboard')
@section('page-subtitle', 'Muhtasari wa leo - ' . now()->format('d/m/Y'))

@section('content')

<!-- Financial Metrics Grid (Mfanyakazi Allowed Sections) -->
<div class="mb-8">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
            <div class="w-2 h-6 bg-gradient-to-b from-blue-500 to-purple-500 rounded-full"></div>
            Muhtasari wa Mauzo (Leo)
        </h2>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Mauzo -->
        <div class="group relative">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl blur opacity-25 group-hover:opacity-50 transition duration-300"></div>
            <div class="relative bg-gradient-to-br from-amber-500 to-amber-700 p-6 rounded-2xl shadow-xl text-white transform transition-transform duration-300 group-hover:scale-105">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-white/20 rounded-2xl">
                        <i class="fas fa-shopping-cart text-xl"></i>
                    </div>
                    <div class="text-white/70 text-sm font-medium">Mauzo</div>
                </div>
                <div class="font-bold text-3xl mb-2">{{ number_format($mauzoLeo, 0) }} <span class="text-sm">Tsh</span></div>
                <div class="flex items-center text-white/80 text-sm">
                    <i class="fas fa-calendar-day mr-2"></i>
                    <span>Leo</span>
                </div>
            </div>
        </div>

        <!-- Madeni -->
        <div class="group relative">
            <div class="absolute inset-0 bg-gradient-to-r from-red-600 to-rose-600 rounded-2xl blur opacity-25 group-hover:opacity-50 transition duration-300"></div>
            <div class="relative bg-gradient-to-br from-red-500 to-red-600 p-6 rounded-2xl shadow-xl text-white transform transition-transform duration-300 group-hover:scale-105">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-white/20 rounded-2xl">
                        <i class="fas fa-money-bill-wave text-xl"></i>
                    </div>
                    <div class="text-white/70 text-sm font-medium">Madeni</div>
                </div>
                <div class="font-bold text-3xl mb-2">{{ number_format($jumlaMadeni, 0)  }} <span class="text-sm">Tsh</span></div>
                <div class="flex items-center text-white/80 text-sm">
                    <i class="fas fa-calendar-day mr-2"></i>
                    <span>Leo</span>
                </div>
            </div>
        </div>

        <!-- Matumizi -->
        <div class="group relative">
            <div class="absolute inset-0 bg-gradient-to-r from-amber-600 to-orange-600 rounded-2xl blur opacity-25 group-hover:opacity-50 transition duration-300"></div>
            <div class="relative bg-gradient-to-br from-amber-500 to-amber-600 p-6 rounded-2xl shadow-xl text-white transform transition-transform duration-300 group-hover:scale-105">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-white/20 rounded-2xl">
                        <i class="fas fa-wallet text-xl"></i>
                    </div>
                    <div class="text-white/70 text-sm font-medium">Matumizi</div>
                </div>
                <div class="font-bold text-3xl mb-2">{{ number_format($matumiziLeo, 0) }} <span class="text-sm">Tsh</span></div>
                <div class="flex items-center text-white/80 text-sm">
                    <i class="fas fa-calendar-day mr-2"></i>
                    <span>Leo</span>
                </div>
            </div>
        </div>

        <!-- Bidhaa (Stock Summary) -->
        <div class="group relative">
            <div class="absolute inset-0 bg-gradient-to-r from-purple-600 to-pink-600 rounded-2xl blur opacity-25 group-hover:opacity-50 transition duration-300"></div>
            <div class="relative bg-gradient-to-br from-purple-500 to-purple-600 p-6 rounded-2xl shadow-xl text-white transform transition-transform duration-300 group-hover:scale-105">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-white/20 rounded-2xl">
                        <i class="fas fa-boxes text-xl"></i>
                    </div>
                    <div class="text-white/70 text-sm font-medium">Bidhaa</div>
                </div>
                <div class="font-bold text-3xl mb-2">{{ number_format($jumlaBidhaa, 0)  }}</div>
                <div class="flex items-center text-white/80 text-sm">
                    <i class="fas fa-calendar-day mr-2"></i>
                    <span>Leo</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Top Products -->
<div class="bg-purple-200 rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <div class="w-2 h-5 bg-gradient-to-b from-indigo-500 to-purple-500 rounded-full"></div>
            Bidhaa Zenye Mauzo Makubwa
        </h3>
    </div>

    <div class="space-y-4">
        @foreach($bidhaaTopSales as $index => $bidhaa)
            <div class="flex items-center justify-between p-4 rounded-xl border border-gray-100 hover:bg-gray-50 transition-colors duration-200">
                <div class="flex items-center gap-4">
                    <span class="w-10 h-10 flex items-center justify-center rounded-full 
                        @if($index === 0) bg-gradient-to-br from-amber-400 to-amber-500 text-white
                        @elseif($index === 1) bg-gradient-to-br from-gray-400 to-gray-500 text-white
                        @elseif($index === 2) bg-gradient-to-br from-amber-700 to-amber-800 text-white
                        @else bg-gray-100 text-gray-600 @endif
                        text-sm font-bold">
                        {{ $index + 1 }}
                    </span>
                    <div>
                        <div class="font-semibold text-gray-800">{{ $bidhaa->jina }}</div>
                        <div class="text-sm text-gray-500">Bidhaa</div>
                    </div>
                </div>
                <div class="text-right">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                        @if($index === 0) bg-green-100 text-green-800
                        @elseif($index === 1) bg-blue-100 text-blue-800
                        @else bg-purple-100 text-purple-800 @endif">
                        {{ $bidhaa->mauzos_sum_idadi ?? 0 }} mauzo
                    </span>
                </div>
            </div>
        @endforeach
        @if(count($bidhaaTopSales) === 0)
            <div class="text-center py-8">
                <div class="inline-flex p-4 rounded-2xl bg-gray-100 text-gray-400 mb-4">
                    <i class="fas fa-chart-bar text-2xl"></i>
                </div>
                <h4 class="text-lg font-medium text-gray-600 mb-2">Hakuna mauzo kwa sasa</h4>
                <p class="text-gray-500 text-sm">Mauzo yanaonekana hapo baadaye</p>
            </div>
        @endif
    </div>
</div>

@endsection

