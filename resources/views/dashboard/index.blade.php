@extends('layouts.app')

@section('title', 'Dashboard | Mfumo wa Mauzo')

@section('page-title')
    @php
        // Get authenticated user from either guard (same as original)
        $user = null;
        $userName = 'Mgeni';

        if (Auth::guard('mfanyakazi')->check()) {
            $user = Auth::guard('mfanyakazi')->user();
            $userName = $user->jina ?? $user->username ?? 'Mfanyakazi';
        } elseif (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            $userName = $user->name ?? $user->username ?? 'Mmiliki';
        }

        $formattedName = ucwords(strtolower($userName));
        
        // Calculate margin
        $profitMargin = isset($mapatoLeo) && $mapatoLeo > 0 ? round(($faidaHalisiLeo / $mapatoLeo) * 100, 1) : 0;
    @endphp

    {{-- HEADING WITH AMBER BACKGROUND LABEL --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="relative">
          <h1 class="text-xl md:text-2xl font-bold text-gray-900 pl-3">
                Karibu, {{ $formattedName }}
            </h1>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-4 md:space-y-6">
    
    {{-- 1. METRIC CARDS - ENHANCED CONTRAST --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
        
        {{-- Card 1: Revenue - Dark Emerald Gradient --}}
        <div class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-gray-700 to-gray-900 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-0.5">
            <div class="absolute inset-0 bg-gradient-to-br from-amber-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="relative p-3 md:p-4">
                <div class="flex justify-between items-start mb-2">
                    <div class="w-8 h-8 md:w-9 md:h-9 rounded-lg bg-gradient-to-br from-amber-400 to-amber-600 shadow-sm flex items-center justify-center text-white">
                        <i class="fas fa-chart-line text-xs md:text-sm"></i>
                    </div>
                    <div class="flex items-center gap-0.5 bg-black/30 px-1.5 py-0.5 rounded-full">
                        <i class="fas fa-arrow-up text-[8px] text-emerald-300"></i>
                        <span class="text-[9px] font-bold text-white">+12%</span>
                    </div>
                </div>
                <h3 class="text-emerald-200 text-[9px] font-semibold tracking-wide uppercase">Mapato Leo</h3>
                <div class="text-xl md:text-2xl font-bold text-white mt-0.5 font-mono tracking-tight">
                    TSh <span class="counter" data-target="{{ $mapatoLeo ?? 0 }}">0</span>
                </div>
                <div class="mt-2 flex items-center gap-1">
                    <div class="h-1 flex-1 bg-black/30 rounded-full overflow-hidden">
                        <div class="h-full w-3/4 bg-gradient-to-r from-amber-400 to-amber-500 rounded-full"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Expenses - Dark Amber Gradient --}}
        <div class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-amber-700 to-amber-900 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-0.5">
            <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-bl from-rose-900/20 to-transparent rounded-full blur-xl"></div>
            <div class="relative p-3 md:p-4">
                <div class="flex justify-between items-start mb-2">
                    <div class="w-8 h-8 md:w-9 md:h-9 rounded-lg bg-gradient-to-br from-rose-500 to-rose-700 shadow-sm flex items-center justify-center text-white">
                        <i class="fas fa-receipt text-xs md:text-sm"></i>
                    </div>
                    <span class="text-[9px] text-amber-300 font-semibold bg-black/30 px-1.5 py-0.5 rounded-full">Leo</span>
                </div>
                <h3 class="text-amber-200 text-[9px] font-semibold tracking-wide uppercase">Matumizi Leo</h3>
                <div class="text-xl md:text-2xl font-bold text-white mt-0.5 font-mono">
                    TSh <span class="counter" data-target="{{ $matumiziLeo ?? 0 }}">0</span>
                </div>
                <div class="mt-2 flex items-center gap-1 text-[9px] text-rose-300">
                    <i class="fas fa-exclamation-triangle text-[8px]"></i>
                    <span class="font-medium">+3% vs budget</span>
                </div>
            </div>
        </div>

        {{-- Card 3: Cash Balance - Dark Gray to Black Gradient --}}
        <div class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-gray-800 to-black shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-0.5">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-teal-500/5 opacity-0 group-hover:opacity-100 transition-all"></div>
            <div class="relative p-3 md:p-4">
                <div class="flex justify-between items-start mb-2">
                    <div class="w-8 h-8 md:w-9 md:h-9 rounded-lg bg-gradient-to-br from-emerald-500 to-teal-600 shadow-sm flex items-center justify-center text-white">
                        <i class="fas fa-wallet text-xs md:text-sm"></i>
                    </div>
                    <div class="flex gap-0.5">
                        <div class="w-1 h-1 rounded-full bg-emerald-400"></div>
                        <div class="w-1 h-1 rounded-full bg-gray-500"></div>
                    </div>
                </div>
                <h3 class="text-gray-400 text-[9px] font-semibold tracking-wide uppercase">Fedha Leo</h3>
                <div class="text-xl md:text-2xl font-bold text-white mt-0.5 font-mono tracking-tight">
                    TSh <span class="counter" data-target="{{ $fedhaLeo ?? 0 }}">0</span>
                </div>
                <div class="mt-2 flex items-center justify-between text-[9px]">
                    <span class="text-emerald-400 font-semibold">Available</span>
                    <span class="text-gray-400 font-medium">{{ $fedhaLeo > 0 ? 'Positive' : 'Low' }}</span>
                </div>
            </div>
        </div>

        {{-- Card 4: Net Profit - Dark Indigo Gradient --}}
        <div class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-indigo-800 to-purple-900 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-0.5">
            <div class="absolute -top-8 -right-8 w-24 h-24 bg-white/5 rounded-full blur-2xl"></div>
            <div class="relative p-3 md:p-4">
                <div class="flex justify-between items-start mb-2">
                    <div class="w-8 h-8 md:w-9 md:h-9 rounded-lg bg-white/15 backdrop-blur-sm flex items-center justify-center text-white">
                        <i class="fas fa-chart-pie text-xs md:text-sm"></i>
                    </div>
                    <span class="text-[8px] bg-indigo-500/40 text-indigo-200 px-1.5 py-0.5 rounded-full font-semibold">Net</span>
                </div>
                <h3 class="text-indigo-300 text-[9px] font-semibold tracking-wide uppercase">Faida Halisi Leo</h3>
                <div class="text-xl md:text-2xl font-bold text-white mt-0.5 font-mono">
                    TSh <span class="counter" data-target="{{ $faidaHalisiLeo ?? 0 }}">0</span>
                </div>
                <div class="mt-2 flex items-center gap-2 text-[9px] text-indigo-300">
                    <i class="fas fa-chart-simple text-[8px]"></i>
                    <span class="font-semibold">Margin: {{ $profitMargin }}%</span>
                </div>
                <div class="w-full h-1 bg-indigo-500/30 rounded-full mt-2 overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-amber-400 to-white rounded-full" style="width: {{ min($profitMargin, 100) }}%"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. STOCK OVERVIEW & TOP PRODUCTS --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-5">
        
        {{-- Stock Overview Card - Light Gray Background with Dark Text --}}
        <div class="lg:col-span-1 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl shadow-md border border-gray-300 overflow-hidden hover:shadow-lg transition-shadow">
            <div class="px-3 md:px-4 py-2.5 border-b border-gray-300 bg-gradient-to-r from-gray-50 to-gray-100">
                <div class="flex justify-between items-center">
                    <h3 class="font-bold text-gray-900 flex items-center gap-2 text-sm">
                        <span class="w-1 h-4 bg-gradient-to-b from-emerald-600 to-teal-600 rounded-full"></span>
                        Hali ya Bidhaa
                    </h3>
                    <span class="text-[9px] text-white bg-emerald-700 px-1.5 py-0.5 rounded-full font-bold">Live</span>
                </div>
            </div>
            <div class="p-3 md:p-4 space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-2xl md:text-3xl font-bold text-gray-900">{{ number_format($jumlaBidhaa ?? 0) }}</p>
                        <p class="text-[10px] text-gray-700 font-semibold">Aina za Bidhaa</p>
                    </div>
                    <div class="relative w-12 h-12">
                        @php
                            $stockPercentage = isset($jumlaIdadi) && $jumlaIdadi > 0 ? round((($bidhaaZilizopo ?? 0) / $jumlaIdadi) * 100) : 0;
                        @endphp
                        <svg class="w-full h-full transform -rotate-90">
                            <circle cx="24" cy="24" r="20" stroke="#d1d5db" stroke-width="3" fill="none"/>
                            <circle cx="24" cy="24" r="20" stroke="#059669" stroke-width="3" fill="none" 
                                    stroke-dasharray="125.66" 
                                    stroke-dashoffset="{{ 125.66 - (($stockPercentage / 100) * 125.66) }}" 
                                    class="transition-all duration-1000"/>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center text-[11px] font-bold text-emerald-700">{{ $stockPercentage }}%</div>
                    </div>
                </div>
                
                <div class="grid grid-cols-3 gap-2">
                    <div class="bg-white rounded-lg p-1.5 text-center shadow-sm border border-gray-200">
                        <i class="fas fa-check-circle text-emerald-600 text-[10px] mb-0.5"></i>
                        <p class="text-base font-bold text-gray-900">{{ number_format($bidhaaZilizopo ?? 0) }}</p>
                        <p class="text-[8px] text-gray-700 font-semibold">Zilizopo</p>
                    </div>
                    <div class="bg-white rounded-lg p-1.5 text-center shadow-sm border border-gray-200">
                        <i class="fas fa-times-circle text-red-600 text-[10px] mb-0.5"></i>
                        <p class="text-base font-bold text-gray-900">{{ number_format($bidhaaZimeisha ?? 0) }}</p>
                        <p class="text-[8px] text-gray-700 font-semibold">Zimeisha</p>
                    </div>
                    <div class="bg-white rounded-lg p-1.5 text-center shadow-sm border border-gray-200">
                        <i class="fas fa-exclamation-triangle text-amber-600 text-[10px] mb-0.5"></i>
                        <p class="text-base font-bold text-gray-900">{{ number_format($bidhaaKaribiaKuisha ?? 0) }}</p>
                        <p class="text-[8px] text-gray-700 font-semibold">Karibia Kuisha</p>
                    </div>
                </div>
                
                <div class="pt-2 border-t border-gray-300">
                    <div class="flex justify-between text-[10px]">
                        <span class="text-gray-800 font-bold">Thamani ya Bidhaa</span>
                        <span class="font-bold text-gray-900">TSh {{ number_format($thamani ?? 0, 0) }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top Products List --}}
        <div class="lg:col-span-2 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl shadow-md border border-gray-300">
            <div class="px-3 md:px-4 py-2.5 border-b border-gray-300 bg-gradient-to-r from-gray-50 to-gray-100 flex justify-between items-center">
                <h3 class="font-bold text-gray-900 flex items-center gap-2 text-sm">
                    <span class="w-1 h-4 bg-gradient-to-b from-amber-500 to-orange-600 rounded-full"></span>
                    Mauzo Makubwa
                </h3>
                <div class="flex gap-1">
                    <button class="px-2 py-0.5 text-[9px] rounded-full bg-amber-600 text-white font-bold">Wiki Hii</button>
                    <button class="px-2 py-0.5 text-[9px] rounded-full text-gray-700 bg-gray-100 hover:bg-gray-200 transition font-semibold">Mwezi</button>
                </div>
            </div>
            <div class="divide-y divide-gray-300">
                @forelse(($bidhaaTopSales ?? []) as $index => $bidhaa)
                    <div class="p-2.5 hover:bg-white/50 transition-all flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-6 h-6 rounded-lg flex items-center justify-center font-bold text-xs
                                {{ $index === 0 ? 'bg-amber-600 text-white' : ($index === 1 ? 'bg-gray-700 text-white' : 'bg-orange-700 text-white') }}">
                                {{ $index + 1 }}
                            </div>
                            <div>
                                <p class="font-bold text-gray-900 text-xs">{{ Str::limit($bidhaa->jina ?? 'Unknown', 18) }}</p>
                                <p class="text-[8px] text-gray-600 font-medium">ID: #{{ $bidhaa->id ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-bold text-gray-900">{{ number_format($bidhaa->mauzos_sum_idadi ?? 0) }}</span>
                            <div class="text-[8px] text-emerald-700 flex items-center gap-0.5 font-bold">
                                <i class="fas fa-arrow-up text-[7px]"></i>
                                <span>+{{ rand(5, 25) }}%</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center">
                        <div class="inline-flex p-3 rounded-full bg-gray-300 text-gray-700 mb-2">
                            <i class="fas fa-chart-bar text-base"></i>
                        </div>
                        <p class="text-sm text-gray-800 font-bold">Hakuna mauzo kwa sasa</p>
                        <p class="text-[10px] text-gray-600 mt-0.5 font-medium">Anza kuuza ili kuona mauzo makubwa</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
{{-- 3. DEBT + PERFORMANCE SECTION --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-5">
    
    {{-- Debt Overview --}}
    <div class="bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl shadow-lg overflow-hidden border border-gray-300">
        
        {{-- Header --}}
        <div class="px-3 md:px-4 py-2.5 border-b border-gray-300 bg-gradient-to-r from-amber-800 to-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="font-bold flex items-center gap-2 text-sm text-white">
                    <i class="fas fa-hand-holding-usd text-rose-400 text-xs"></i>
                    Madeni
                </h3>

                <span class="text-[9px] bg-rose-900 text-white px-2 py-0.5 rounded-full font-bold border border-emerald-200">
                    Pending
                </span>
            </div>
        </div>

        {{-- Body --}}
        <div class="p-3 md:p-4">
            
            {{-- Main Stats --}}
            <div class="flex justify-between items-end mb-3">
                
                <div>
                    <p class="text-xl md:text-2xl font-extrabold font-mono tracking-tight text-gray-900">
                        TSh {{ number_format($jumlaMadeni ?? 0, 0) }}
                    </p>

                    <p class="text-[10px] text-gray-600 mt-0.5 font-semibold uppercase tracking-wide">
                        Jumla
                    </p>
                </div>

                <div class="text-right">
                    <p class="text-base font-extrabold text-gray-900">
                        {{ number_format($idadiMadeni ?? 0) }}
                    </p>

                    <p class="text-[9px] text-amber-700 font-bold uppercase tracking-wide">
                        Wateja
                    </p>
                </div>
            </div>

            {{-- Progress --}}
            <div class="w-full bg-gray-300 rounded-full h-1.5 mb-3">
                @php
                    $debtPercentage = isset($jumlaMadeniDefault)
                        ? min(($jumlaMadeniDefault / 10000000) * 100, 100)
                        : 65;
                @endphp

                <div class="bg-gradient-to-r from-rose-500 to-rose-600 h-full rounded-full"
                     style="width: {{ $debtPercentage }}%">
                </div>
            </div>

            {{-- Buttons --}}
            <div class="flex gap-2">

                <a href="{{ route('madeni.index') }}"
                   class="flex-1 text-center text-[10px] bg-gray-800 hover:bg-gray-900 text-white px-2 py-1.5 rounded-lg transition font-bold shadow-sm">
                    <i class="fas fa-eye mr-1"></i>
                    Tazama
                </a>

                <button onclick="showDebtReminder()"
                        class="flex-1 text-center text-[10px] bg-rose-100 hover:bg-rose-200 text-rose-700 px-2 py-1.5 rounded-lg transition font-bold border border-rose-200">
                    <i class="fas fa-bell mr-1"></i>
                    Kumbusha
                </button>

            </div>
        </div>
    </div>


        {{-- AI-Style Performance Insight Card - Light Background with Dark Text --}}
        <div class="relative overflow-hidden bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl shadow-md border border-gray-300">
            <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-emerald-100/50 to-transparent rounded-full blur-xl"></div>
            <div class="p-3 md:p-4">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <div class="flex items-center gap-1.5 mb-1">
                            <div class="w-5 h-5 rounded-lg bg-gradient-to-br from-emerald-700 to-teal-700 flex items-center justify-center">
                                <i class="fas fa-robot text-white text-[8px]"></i>
                            </div>
                            <span class="text-[8px] font-mono bg-emerald-200 text-emerald-900 px-1.5 py-0.5 rounded-full font-bold">AI Insight</span>
                        </div>
                        <h3 class="font-bold text-gray-900 text-sm">Muhtasari wa Utendaji</h3>
                    </div>
                    <div class="w-7 h-7 rounded-full bg-amber-200 flex items-center justify-center">
                        <i class="fas fa-chart-line text-amber-800 text-xs"></i>
                    </div>
                </div>
                
                @php
                    $faidaHalisi = $faidaHalisiLeo ?? 0;
                    $message = '';
                    $statusClass = '';
                    $icon = '';
                    
                    if ($faidaHalisi > 500000) {
                        $message = 'Faida bora leo! Biashara inakwenda vizuri sana. Endelea na mwendo huu.';
                        $statusClass = 'text-emerald-800';
                        $icon = '🚀';
                    } elseif ($faidaHalisi > 0) {
                        $message = 'Utendaji mzuri. Unaweza kuongeza mauzo kwa bidhaa zinazouzika sana.';
                        $statusClass = 'text-amber-800';
                        $icon = '📈';
                    } else {
                        $message = 'Angalia bidhaa zilizokwisha na wadeni wako. Fuatilia matumizi yasiyo ya lazima.';
                        $statusClass = 'text-rose-800';
                        $icon = '⚠️';
                    }
                @endphp
                
                <div class="bg-white rounded-lg p-2.5 mb-3 shadow-sm border border-gray-200">
                    <p class="text-xs text-gray-900 leading-relaxed font-semibold">
                        <span class="text-base mr-1">{{ $icon }}</span>
                        {{ $message }}
                    </p>
                </div>
                
                <div class="flex items-center justify-between text-[9px]">
                    <div class="flex -space-x-1">
                        <div class="w-5 h-5 rounded-full bg-amber-500 border-2 border-white"></div>
                        <div class="w-5 h-5 rounded-full bg-emerald-500 border-2 border-white"></div>
                        <div class="w-5 h-5 rounded-full bg-indigo-500 border-2 border-white"></div>
                    </div>
                    <div class="flex items-center gap-1">
                        <i class="fas fa-chart-simple text-emerald-700 text-[8px]"></i>
                        <span class="text-gray-800 font-bold">Hali:</span>
                        <span class="font-extrabold {{ $statusClass }}">
                            {{ $faidaHalisi > 0 ? 'Nzuri' : 'Inahitaji Uangalizi' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Animated Counter Script --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animated Counters
    const counters = document.querySelectorAll('.counter');
    
    const animateCounter = (counter) => {
        const target = parseInt(counter.dataset.target);
        let current = 0;
        const duration = 800;
        const stepTime = 20;
        const steps = duration / stepTime;
        const increment = target / steps;
        
        const updateCounter = () => {
            current += increment;
            if (current < target) {
                counter.innerText = Math.floor(current).toLocaleString();
                setTimeout(updateCounter, stepTime);
            } else {
                counter.innerText = target.toLocaleString();
            }
        };
        
        updateCounter();
    };
    
    // Use Intersection Observer for counter animation
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const counter = entry.target;
                animateCounter(counter);
                observer.unobserve(counter);
            }
        });
    }, { threshold: 0.5 });
    
    counters.forEach(counter => observer.observe(counter));
});

// Debt reminder function - WITHOUT browser confirmation
function showDebtReminder() {
    // Direct navigation without confirmation popup
    window.location.href = '{{ route("madeni.index") }}';
}
</script>
@endpush

{{-- Additional Styles for Premium Feel --}}
@push('styles')
<style>
    /* Custom animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(15px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .group {
        animation: fadeInUp 0.4s ease-out forwards;
        animation-fill-mode: both;
    }
    
    .grid > div:nth-child(1) { animation-delay: 0.05s; }
    .grid > div:nth-child(2) { animation-delay: 0.1s; }
    .grid > div:nth-child(3) { animation-delay: 0.15s; }
    .grid > div:nth-child(4) { animation-delay: 0.2s; }
    
    /* Smooth number transitions */
    .counter {
        transition: all 0.2s ease;
    }
    
    /* Card hover effects */
    .group:hover .fa-chart-line,
    .group:hover .fa-receipt,
    .group:hover .fa-wallet,
    .group:hover .fa-chart-pie {
        transform: scale(1.05);
        transition: transform 0.2s ease;
    }
    
    /* Compact cards don't need large min-height on mobile */
    @media (max-width: 640px) {
        button, [role="button"] {
            min-height: auto;
        }
    }
    
    /* Custom scrollbar for the dashboard */
    ::-webkit-scrollbar {
        width: 4px;
    }
    
    ::-webkit-scrollbar-track {
        background: #e5e7eb;
        border-radius: 10px;
    }
    
    ::-webkit-scrollbar-thumb {
        background: #9ca3af;
        border-radius: 10px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: #6b7280;
    }
</style>
@endpush
@endsection