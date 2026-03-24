@extends('layouts.app')

@section('title', 'Uchambuzi wa Biashara')
@section('page-title', 'Dashibodi ya Taarifa')
@section('page-subtitle', 'Uchambuzi wa Biashara kwa Grafu na Takwimu')

@push('styles')
<style>
    /* modern lowercase-friendly font + high contrast */
    * {
        font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, Helvetica, sans-serif;
    }
    
    /* strong contrast for amber labels - ensures readability */
    .amber-label {
        color: #b45309 !important;
        font-weight: 700;
        letter-spacing: -0.01em;
    }
    
    /* high contrast backgrounds for better visibility */
    .bg-amber-soft {
        background-color: #fffbeb;
    }
    
    .text-contrast-white {
        color: #ffffff;
        text-shadow: 0 1px 1px rgba(0,0,0,0.15);
    }
    
    .card-hover {
        transition: all 0.2s ease;
    }
    
    .card-hover:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    }
    
    /* improved table row contrast */
    .table-row-highlight:hover {
        background-color: #fef3c7;
    }
    
    /* smart scaling for charts - ensures long bidhaa names are visible */
    .chart-wrapper canvas {
        max-height: 100%;
        width: 100% !important;
    }
    
    .graph-container {
        overflow-x: auto;
        overflow-y: visible;
        min-height: 320px;
    }
    
    @media (min-width: 768px) {
        .graph-container {
            min-height: 400px;
        }
    }
    
    /* mobile responsive table */
    .table-responsive {
        display: block;
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    .custom-scroll-x {
        scrollbar-width: thin;
    }
    
    [x-cloak] { display: none !important; }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-8px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .animate-fade-in {
        animation: fadeIn 0.25s ease-out;
    }
    
    /* high contrast stat boxes */
    .stat-card {
        transition: all 0.2s;
        border-left: 3px solid #f59e0b;
    }
    
    .stat-card:hover {
        background-color: #fef9e6;
    }
</style>
@endpush

@section('content')
<div class="space-y-5" x-data="dashboardApp()" x-init="init()">
    <!-- Notifications container -->
    <div id="notification-container" class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-sm px-4 pointer-events-none"></div>

    <!-- Header - Mobile Optimized + improved contrast -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm card-hover">
        <div class="p-4 border-b border-gray-200 bg-amber-soft">
            <h2 class="text-base font-bold flex items-center text-gray-800">
                <i class="fas fa-chart-bar text-amber-600 mr-2 text-lg"></i>
                <span class="amber-label text-base">TAARIFA KATIKA MAUZO</span>
            </h2>
            <p class="text-xs text-gray-700 mt-1 font-medium">Chagua aina ya taarifa kuona uchambuzi kamili</p>
        </div>
        
        <div class="p-4">
            <div class="flex flex-wrap gap-2">
                <button @click="setTab('graphs')" 
                        :class="tab === 'graphs' ? 'bg-amber-600 text-white shadow-md' : 'bg-gray-100 text-gray-800 hover:bg-gray-200'" 
                        class="px-4 py-2 rounded-lg font-semibold transition-all duration-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <i class="fas fa-chart-bar mr-1"></i> Grafu
                </button>
                <button @click="setTab('mwenendo')" 
                        :class="tab === 'mwenendo' ? 'bg-amber-600 text-white shadow-md' : 'bg-gray-100 text-gray-800 hover:bg-gray-200'" 
                        class="px-4 py-2 rounded-lg font-semibold transition-all duration-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <i class="fas fa-chart-line mr-1"></i> Mwenendo
                </button>
                <button @click="setTab('kampuni')" 
                        :class="tab === 'kampuni' ? 'bg-amber-600 text-white shadow-md' : 'bg-gray-100 text-gray-800 hover:bg-gray-200'" 
                        class="px-4 py-2 rounded-lg font-semibold transition-all duration-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <i class="fas fa-building mr-1"></i> Kampuni
                </button>
                <a href="{{ route('user.reports.select') }}" 
                   class="px-4 py-2 rounded-lg font-semibold transition-all duration-200 bg-gray-100 text-gray-800 hover:bg-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <i class="fas fa-file-alt mr-1"></i> Ripoti
                </a>
                <button @click="setTab('historia')" 
                        :class="tab === 'historia' ? 'bg-amber-600 text-white shadow-md' : 'bg-gray-100 text-gray-800 hover:bg-gray-200'" 
                        class="px-4 py-2 rounded-lg font-semibold transition-all duration-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <i class="fas fa-history mr-1"></i> Historia
                </button>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="space-y-5">
        <!-- GRAPHS TAB with full data and improved scaling -->
        <div x-show="tab === 'graphs'" 
             class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm"
             x-cloak 
             x-transition:enter.duration.300ms>
            
            <div class="p-4 border-b border-gray-200 bg-amber-soft">
                <h2 class="text-sm font-bold flex items-center text-gray-800">
                    <i class="fas fa-chart-pie text-amber-600 mr-2"></i>
                    <span class="amber-label">Chagua Aina ya Grafu</span>
                </h2>
                <p class="text-xs text-gray-700 mt-1 font-medium">Bonyeza kwenye kitufe kuona uchambuzi tofauti | Majina yote yataonekana kwenye grafu</p>
            </div>
            
            <div class="p-4">
                <!-- Graph tabs with distinct colors -->
                <div class="flex flex-wrap gap-2 mb-5 border-b border-gray-200 pb-3 overflow-x-auto custom-scroll-x">
                    <template x-for="(item, key) in graphTabs" :key="key">
                        <button @click="setGraph(key)"
                            :class="graph === key ? getActiveClass(key) : 'bg-gray-100 text-gray-800 hover:bg-gray-200'"
                            class="px-4 py-2 rounded-lg transition-all duration-200 text-xs md:text-sm whitespace-nowrap font-semibold focus:outline-none focus:ring-2 focus:ring-amber-500"
                            x-text="item">
                        </button>
                    </template>
                </div>

                <!-- Chart containers with all original data -->
                <div class="graph-container w-full">
                    <div x-show="graph === 'faidaBidhaa'" x-cloak>
                        <canvas id="faidaBidhaaChart" class="w-full" style="max-height: 380px; width:100%"></canvas>
                    </div>
                    <div x-show="graph === 'faidaSiku'" x-cloak>
                        <canvas id="faidaSikuChart" class="w-full" style="max-height: 380px; width:100%"></canvas>
                    </div>
                    <div x-show="graph === 'mauzoSiku'" x-cloak>
                        <canvas id="mauzoSikuChart" class="w-full" style="max-height: 380px; width:100%"></canvas>
                    </div>
                    <div x-show="graph === 'marejesho'" x-cloak>
                        <canvas id="marejeshoChart" class="w-full" style="max-height: 380px; width:100%"></canvas>
                    </div>
                    <div x-show="graph === 'mauzo'" x-cloak>
                        <canvas id="mauzoChart" class="w-full" style="max-height: 380px; width:100%"></canvas>
                    </div>
                </div>
                <p class="text-[10px] text-gray-500 mt-3 text-center"><i class="fas fa-arrows-alt-h mr-1"></i> Scroll kulia/kushoto kuona majina yote ya bidhaa</p>
            </div>
        </div>

        <!-- MWENENDO TAB - Full original data preserved -->
        <div x-show="tab === 'mwenendo'" 
             class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm" 
             x-cloak
             x-data="mwenendoApp()"
             x-init="initMwenendo()"
             x-transition:enter.duration.300ms>
            <div class="p-4 border-b border-gray-200 bg-amber-soft">
                <h2 class="text-sm font-bold text-gray-800 flex items-center">
                    <i class="fas fa-chart-line text-amber-600 mr-2"></i>
                    <span class="amber-label">Mwenendo wa Biashara</span>
                </h2>
                <p class="text-xs text-gray-700 mt-1 font-medium">Chagua kipindi au tarehe kuona takwimu kamili</p>
            </div>
            <div class="p-4">
                <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-3 mb-4">
                    <div class="flex items-center gap-2">
                        <button @click="refresh()" class="flex items-center gap-2 bg-amber-600 text-white px-3 py-2 rounded-lg hover:bg-amber-700 transition-all text-sm font-medium shadow-sm">
                            <i class="fas fa-sync-alt text-xs"></i> <span>Pakua Upya</span>
                        </button>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">⏳ Chagua Muda:</label>
                        <select x-model="viewType" @change="toggleDatePicker(false)" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium focus:ring-2 focus:ring-amber-400 focus:border-amber-400">
                            <option value="Siku">Siku</option>
                            <option value="Wiki">Wiki</option>
                            <option value="Mwezi">Mwezi</option>
                            <option value="Mwaka">Mwaka</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button @click="toggleDatePicker(true)" class="w-full bg-gradient-to-r from-amber-600 to-amber-500 text-white px-4 py-2 rounded-lg hover:from-amber-700 shadow-sm text-sm font-semibold">
                            <i class="fas fa-calendar-alt mr-1"></i> Chagua Tarehe
                        </button>
                    </div>
                </div>

                <!-- Auto Summary - Full data with high contrast -->
                <div x-show="!manualDateSelect" class="bg-gray-50 border border-gray-200 rounded-lg p-4 space-y-3">
                    <h3 class="font-bold text-gray-800 text-sm">
                        Muhtasari wa <span x-text="viewType" class="text-amber-600"></span>
                        <span x-show="summary?.date" class="text-xs text-gray-500 font-normal">(<span x-text="summary?.date"></span>)</span>
                    </h3>
                    <template x-if="summary">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                            <div class="bg-white border-l-4 border-amber-500 rounded-lg p-3 shadow-sm stat-card">
                                <p class="text-xs text-gray-500 mb-1">Mapato ya Mauzo</p>
                                <p class="text-base font-bold text-gray-900" x-text="formatCurrency(summary.mapato_mauzo)"></p>
                            </div>
                            <div class="bg-white border-l-4 border-amber-500 rounded-lg p-3 shadow-sm stat-card">
                                <p class="text-xs text-gray-500 mb-1">Mapato ya Madeni</p>
                                <p class="text-base font-bold text-gray-900" x-text="formatCurrency(summary.mapato_madeni)"></p>
                            </div>
                            <div class="bg-white border-l-4 border-amber-500 rounded-lg p-3 shadow-sm stat-card">
                                <p class="text-xs text-gray-500 mb-1">Jumla ya Mapato</p>
                                <p class="text-base font-bold text-gray-900" x-text="formatCurrency(summary.jumla_mapato)"></p>
                            </div>
                            <div class="bg-white border-l-4 border-amber-500 rounded-lg p-3 shadow-sm stat-card">
                                <p class="text-xs text-gray-500 mb-1">Jumla ya Matumizi</p>
                                <p class="text-base font-bold text-gray-900" x-text="formatCurrency(summary.jumla_mat)"></p>
                            </div>
                            <div class="bg-white border-l-4 border-amber-500 rounded-lg p-3 shadow-sm stat-card">
                                <p class="text-xs text-gray-500 mb-1">Gharama ya Bidhaa</p>
                                <p class="text-base font-bold text-gray-900" x-text="formatCurrency(summary.gharama_bidhaa)"></p>
                            </div>
                            <div class="bg-white border-l-4 border-amber-500 rounded-lg p-3 shadow-sm stat-card">
                                <p class="text-xs text-gray-500 mb-1">Faida ya Mauzo</p>
                                <p class="text-base font-bold text-gray-900" x-text="formatCurrency(summary.faida_mauzo)"></p>
                            </div>
                            <div class="bg-white border-l-4 border-amber-500 rounded-lg p-3 shadow-sm stat-card">
                                <p class="text-xs text-gray-500 mb-1">Faida ya Marejesho</p>
                                <p class="text-base font-bold text-gray-900" x-text="formatCurrency(summary.faida_marejesho)"></p>
                            </div>
                            <div class="bg-white border-l-4 border-amber-500 rounded-lg p-3 shadow-sm stat-card">
                                <p class="text-xs text-gray-500 mb-1">Fedha Dukani</p>
                                <p class="text-base font-bold text-gray-900" x-text="formatCurrency(summary.fedha_droo)"></p>
                            </div>
                            <div class="bg-white border-l-4 border-emerald-500 rounded-lg p-3 shadow-sm lg:col-span-2 stat-card">
                                <p class="text-xs text-gray-500 mb-1">Faida Halisi</p>
                                <p class="text-lg font-bold text-emerald-700" x-text="formatCurrency(summary.faida_halisi)"></p>
                            </div>
                        </div>
                    </template>
                    <template x-if="!summary">
                        <div class="text-center py-6 text-gray-500 text-sm">Hakuna data inayopatikana kwa kipindi hiki.</div>
                    </template>
                </div>

                <!-- Manual Date Selection -->
                <div x-show="manualDateSelect" class="border border-gray-200 rounded-lg p-4 bg-gray-50 space-y-3">
                    <h3 class="font-bold text-gray-800 text-sm">📅 Angalia Mwenendo kwa Tarehe</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div><label class="block text-xs font-bold text-gray-600 mb-1">Kuanzia Tarehe:</label><input type="date" x-model="dateFrom" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-amber-400"></div>
                        <div><label class="block text-xs font-bold text-gray-600 mb-1">Mpaka Tarehe:</label><input type="date" x-model="dateTo" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-amber-400"></div>
                    </div>
                    <div class="flex gap-2"><button @click="fetchCustomSummary()" class="bg-amber-600 text-white px-4 py-2 rounded-lg hover:bg-amber-700 text-sm font-semibold"><i class="fas fa-search mr-1"></i> Angalia</button><button @click="toggleDatePicker(false)" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300 text-sm font-semibold"><i class="fas fa-arrow-left mr-1"></i> Rudi</button></div>
                    <template x-if="customSummary">
                        <div class="mt-3 bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                            <h4 class="text-amber-700 font-bold mb-3 text-sm">Matokeo ya kipindi: <span x-text="dateFrom + ' mpaka ' + dateTo"></span></h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-2">
                                <div class="border-l-2 border-amber-400 pl-2"><p class="text-xs text-gray-500">Mapato ya Mauzo</p><p class="text-sm font-bold" x-text="formatCurrency(customSummary.mapato_mauzo)"></p></div>
                                <div class="border-l-2 border-amber-400 pl-2"><p class="text-xs text-gray-500">Mapato ya Madeni</p><p class="text-sm font-bold" x-text="formatCurrency(customSummary.mapato_madeni)"></p></div>
                                <div class="border-l-2 border-amber-400 pl-2"><p class="text-xs text-gray-500">Jumla ya Mapato</p><p class="text-sm font-bold" x-text="formatCurrency(customSummary.jumla_mapato)"></p></div>
                                <div class="border-l-2 border-amber-400 pl-2"><p class="text-xs text-gray-500">Jumla ya Matumizi</p><p class="text-sm font-bold" x-text="formatCurrency(customSummary.jumla_mat)"></p></div>
                                <div class="border-l-2 border-amber-400 pl-2"><p class="text-xs text-gray-500">Gharama ya Bidhaa</p><p class="text-sm font-bold" x-text="formatCurrency(customSummary.gharama_bidhaa)"></p></div>
                                <div class="border-l-2 border-amber-400 pl-2"><p class="text-xs text-gray-500">Faida ya Mauzo</p><p class="text-sm font-bold" x-text="formatCurrency(customSummary.faida_mauzo)"></p></div>
                                <div class="border-l-2 border-amber-400 pl-2"><p class="text-xs text-gray-500">Faida ya Marejesho</p><p class="text-sm font-bold" x-text="formatCurrency(customSummary.faida_marejesho)"></p></div>
                                <div class="border-l-2 border-amber-400 pl-2"><p class="text-xs text-gray-500">Fedha Dukani</p><p class="text-sm font-bold" x-text="formatCurrency(customSummary.fedha_droo)"></p></div>
                                <div class="border-l-2 border-emerald-500 pl-2 lg:col-span-2"><p class="text-xs text-gray-500">Faida Halisi</p><p class="text-base font-bold text-emerald-700" x-text="formatCurrency(customSummary.faida_halisi)"></p></div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- KAMPUNI TAB - Full original data with high contrast -->
        <div x-show="tab === 'kampuni'" x-data="kampuniData()" class="bg-white rounded-xl border border-gray-200 shadow-sm" x-cloak>
            <div class="p-4 border-b border-gray-200 bg-amber-soft">
                <h2 class="text-sm font-bold flex items-center"><i class="fas fa-building text-amber-600 mr-2"></i><span class="amber-label">Thamani ya Kampuni</span></h2>
                <p class="text-xs text-gray-700 font-medium">Muhtasari wa thamani ya kampuni na faida</p>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div class="bg-gray-50 rounded-lg p-3 border-l-4 border-amber-500"><p class="text-xs text-gray-500 font-medium">Thamani Kabla (Bei ya Nunua)</p><p class="text-lg font-bold text-gray-900" x-text="formatCurrency(beforeSales)"></p><p class="text-xs text-gray-400 mt-1">Bei ya ununuzi ya bidhaa zilizopo</p></div>
                    <div class="bg-gray-50 rounded-lg p-3 border-l-4 border-amber-500"><p class="text-xs text-gray-500 font-medium">Thamani Baada (Bei ya Kuuza)</p><p class="text-lg font-bold text-gray-900" x-text="formatCurrency(afterSales)"></p><p class="text-xs text-gray-400 mt-1">Bei ya kuuzia ya bidhaa zilizopo</p></div>
                    <div class="bg-amber-50 rounded-lg p-3 border-l-4 border-amber-700"><p class="text-xs text-amber-800 font-bold">Faida ya Uwezo</p><p class="text-xl font-bold text-amber-800" x-text="formatCurrency(faida)"></p><p class="text-xs text-amber-600 mt-1">Faida inayoweza kupatikana</p></div>
                </div>
                <div class="bg-gradient-to-r from-amber-50 to-yellow-50 rounded-lg p-4 mb-4 border border-amber-200">
                    <p class="text-xs text-amber-800 font-bold mb-1">Thamani ya Jumla ya Kampuni</p>
                    <p class="text-2xl font-extrabold text-amber-800">{{ $thamaniKampuniFormatted }}</p>
                    <p class="text-xs text-amber-600 mt-1">Thamani ya sasa ya hisa zote (kwa bei ya kuuzia)</p>
                </div>
                <div><h3 class="font-bold text-gray-800 mb-3 text-sm">Orodha kamili ya Bidhaa</h3>
                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-100 border-b-2 border-gray-200">
                                <tr>
                                    <th class="px-3 py-3 text-left font-bold text-gray-700">Bidhaa</th>
                                    <th class="px-3 py-3 text-left font-bold text-gray-700 hidden sm:table-cell">Aina</th>
                                    <th class="px-3 py-3 text-left font-bold text-gray-700 hidden md:table-cell">Kipimo</th>
                                    <th class="px-3 py-3 text-right font-bold text-gray-700">Idadi</th>
                                    <th class="px-3 py-3 text-right font-bold text-gray-700 hidden lg:table-cell">Nunua</th>
                                    <th class="px-3 py-3 text-right font-bold text-gray-700">Kuuza</th>
                                    <th class="px-3 py-3 text-right font-bold text-gray-700">Faida</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($bidhaaList as $item)
                                    <tr class="hover:bg-amber-50 transition-colors table-row-highlight">
                                        <td class="px-3 py-3 text-gray-900 font-medium truncate max-w-[140px]">{{ $item->jina }}</td>
                                        <td class="px-3 py-3 text-gray-700 hidden sm:table-cell truncate max-w-[100px]">{{ $item->aina }}</td>
                                        <td class="px-3 py-3 text-gray-700 hidden md:table-cell truncate">{{ $item->kipimo ?? '-' }}</td>
                                        <td class="px-3 py-3 text-right text-gray-900 font-semibold">{{ number_format($item->idadi) }}</td>
                                        <td class="px-3 py-3 text-right text-gray-600 hidden lg:table-cell">Tsh {{ number_format($item->bei_nunua, 0) }}</td>
                                        <td class="px-3 py-3 text-right text-gray-800 font-medium">Tsh {{ number_format($item->bei_kuuza, 0) }}</td>
                                        <td class="px-3 py-3 text-right font-bold text-amber-700">Tsh {{ number_format($item->faida, 0) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- HISTORIA TAB -->
        <div x-show="tab === 'historia'" class="bg-white rounded-xl border border-gray-200 shadow-sm" x-cloak>
            <div class="p-4 border-b border-gray-200 bg-amber-soft">
                <h2 class="text-sm font-bold text-gray-800 flex items-center"><i class="fas fa-history text-amber-600 mr-2"></i><span class="amber-label">Historia ya Mfumo</span></h2>
                <p class="text-xs text-gray-700 font-medium">Taarifa kuhusu historia ya mfumo na takwimu</p>
            </div>
            <div class="p-8 text-center border-2 border-dashed border-gray-300 rounded-lg m-4">
                <i class="fas fa-chart-line text-3xl text-gray-400 mb-2"></i>
                <p class="text-gray-500 text-sm font-medium">Inafanya kazi - Ukurasa wa historia utaanza hivi karibuni</p>
            </div>
        </div>
    </div>
</div>

<!-- Loading Spinner -->
<div id="loading-spinner" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white p-4 rounded-lg shadow-xl flex items-center space-x-3">
        <svg class="animate-spin h-5 w-5 text-amber-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="text-gray-700 text-sm font-medium">Inapakia...</span>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

<script>
// Global utility function for formatting currency
function formatCurrency(value) {
    return new Intl.NumberFormat('sw-TZ', {
        style: 'currency',
        currency: 'TZS',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(value || 0);
}

// Chart storage
let chartInstances = {};

// Graph colors - high contrast variants
const graphColors = {
    faidaBidhaa: { bg: 'rgba(245, 158, 11, 0.8)', border: '#d97706' },
    faidaSiku: { bg: 'rgba(16, 185, 129, 0.75)', border: '#059669' },
    mauzoSiku: { bg: 'rgba(59, 130, 246, 0.75)', border: '#2563eb' },
    marejesho: { bg: 'rgba(139, 92, 246, 0.75)', border: '#7c3aed' },
    mauzo: { bg: 'rgba(236, 72, 153, 0.75)', border: '#db2777' }
};

document.addEventListener('alpine:init', () => {
    Alpine.data('dashboardApp', () => ({
        tab: 'graphs',
        graph: 'faidaBidhaa',
        
        graphTabs: {
            faidaBidhaa: 'Faida kwa Bidhaa',
            faidaSiku: 'Faida kwa Siku',
            mauzoSiku: 'Mauzo kwa Siku',
            marejesho: 'Faida ya Marejesho',
            mauzo: 'Mauzo Jumla'
        },

        // ALL ORIGINAL DATA FROM LARAVEL
        faidaByBidhaa: @json($faidaBidhaa ?? []),
        faidaBySiku: @json($faidaSiku ?? []),
        mauzoBySiku: @json($mauzoSiku ?? []),
        marejeshoByBidhaa: @json($marejesho ?? []),
        mauzoByBidhaa: @json($mauzo ?? []),

        getActiveClass(key) {
            const colors = {
                faidaBidhaa: 'bg-amber-600 text-white shadow-md',
                faidaSiku: 'bg-emerald-600 text-white shadow-md',
                mauzoSiku: 'bg-blue-600 text-white shadow-md',
                marejesho: 'bg-purple-600 text-white shadow-md',
                mauzo: 'bg-pink-600 text-white shadow-md'
            };
            return colors[key] || 'bg-amber-600 text-white shadow-md';
        },

        setTab(tabName) {
            this.tab = tabName;
            if(tabName === 'graphs') {
                setTimeout(() => {
                    this.drawChart(this.graph);
                }, 80);
            }
        },

        setGraph(name) {
            this.graph = name;
            setTimeout(() => {
                this.drawChart(name);
            }, 80);
        },

        drawChart(name) {
            const canvasId = name + 'Chart';
            const canvas = document.getElementById(canvasId);
            if(!canvas) return;
            
            if (chartInstances[name]) {
                chartInstances[name].destroy();
                chartInstances[name] = null;
            }

            const ctx = canvas.getContext('2d');
            if(!ctx) return;

            let config = {};
            const colorSet = graphColors[name] || graphColors.faidaBidhaa;
            
            switch(name) {
                case 'faidaBidhaa': 
                    config = this.barChart(this.faidaByBidhaa, 'jina', 'faida', colorSet, 'Faida kwa Bidhaa');
                    break;
                case 'faidaSiku': 
                    config = this.lineChart(this.faidaBySiku, 'day', 'faida', colorSet, 'Faida kwa Siku');
                    break;
                case 'mauzoSiku': 
                    config = this.lineChart(this.mauzoBySiku, 'day', 'total', colorSet, 'Mauzo kwa Siku');
                    break;
                case 'marejesho': 
                    config = this.barChart(this.marejeshoByBidhaa, 'jina', 'total', colorSet, 'Faida ya Marejesho');
                    break;
                case 'mauzo': 
                    config = this.barChart(this.mauzoByBidhaa, 'jina', 'total', colorSet, 'Mauzo Jumla');
                    break;
            }

            try {
                chartInstances[name] = new Chart(ctx, config);
            } catch (e) {
                console.error('Error creating chart:', e);
            }
        },

        barChart(data, labelKey, valueKey, colors, label) {
            const labels = data.map(i => i[labelKey] || 'N/A');
            const values = data.map(i => Math.max(0, Number(i[valueKey]) || 0));
            
            return {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{ 
                        label: label, 
                        data: values, 
                        backgroundColor: colors.bg,
                        borderColor: colors.border,
                        borderWidth: 2,
                        borderRadius: 6
                    }]
                },
                options: this.chartOptions()
            };
        },

        lineChart(data, labelKey, valueKey, colors, label) {
            const labels = data.map(i => i[labelKey] || 'N/A');
            const values = data.map(i => Math.max(0, Number(i[valueKey]) || 0));
            
            return {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{ 
                        label: label, 
                        data: values, 
                        borderColor: colors.border,
                        backgroundColor: colors.bg.replace('0.75', '0.15'),
                        borderWidth: 3,
                        tension: 0.3,
                        fill: true,
                        pointBackgroundColor: colors.border,
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: this.chartOptions()
            };
        },

        chartOptions() {
            return {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { 
                    legend: { 
                        display: true,
                        position: 'top',
                        labels: {
                            color: '#1f2937',
                            font: {
                                size: window.innerWidth < 768 ? 11 : 12,
                                weight: 'bold'
                            },
                            padding: 15
                        }
                    }, 
                    tooltip: { 
                        enabled: true,
                        backgroundColor: 'rgba(0, 0, 0, 0.85)',
                        titleColor: '#fbbf24',
                        bodyColor: '#fff',
                        titleFont: { size: 12, weight: 'bold' },
                        bodyFont: { size: 11 },
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) label += ': ';
                                label += formatCurrency(context.parsed.y);
                                return label;
                            }
                        }
                    } 
                },
                scales: { 
                    y: { 
                        beginAtZero: true,
                        grid: { color: 'rgba(0, 0, 0, 0.05)' },
                        ticks: {
                            color: '#374151',
                            font: { size: window.innerWidth < 768 ? 10 : 11, weight: '500' },
                            callback: function(value) {
                                if (value >= 1000000) return 'Tsh ' + (value / 1000000).toFixed(1) + 'M';
                                if (value >= 1000) return 'Tsh ' + (value / 1000).toFixed(0) + 'K';
                                return 'Tsh ' + value;
                            }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: '#374151',
                            font: { size: window.innerWidth < 768 ? 9 : 10, weight: '500' },
                            maxRotation: 45,
                            minRotation: 35,
                            autoSkip: true,
                            maxTicksLimit: 8
                        }
                    }
                }
            };
        },

        handleResize() {
            if (this.tab === 'graphs' && chartInstances[this.graph]) {
                setTimeout(() => {
                    if (chartInstances[this.graph]) chartInstances[this.graph].resize();
                }, 100);
            }
        },

        init() {
            this.tab = 'graphs';
            setTimeout(() => {
                if (this.tab === 'graphs') this.drawChart(this.graph);
            }, 150);
            window.addEventListener('resize', () => this.handleResize());
        }
    }));

    Alpine.data('mwenendoApp', () => ({
        viewType: 'Siku',
        manualDateSelect: false,
        dateFrom: '',
        dateTo: '',
        summary: null,
        customSummary: null,
        mwenendoSummary: @json($mwenendoSummary ?: []),

        toggleDatePicker(val) { 
            this.manualDateSelect = val; 
            if(!val) {
                this.customSummary = null;
                this.updateSummary(); 
            }
        },
        
        formatCurrency(value) {
            return formatCurrency(value);
        },

        updateSummary() {
            const key = this.viewType.toLowerCase();
            const data = this.mwenendoSummary[key] || null;
            this.summary = data ? data : null;
        },

        async fetchCustomSummary() {
            if(!this.dateFrom || !this.dateTo) {
                this.showNotification('Tafadhali chagua tarehe zote za kuanzia na mpaka', 'error');
                return;
            }
            const fromDate = new Date(this.dateFrom);
            const toDate = new Date(this.dateTo);
            const today = new Date();
            if (fromDate > today) { this.showNotification('Tarehe ya kuanzia haiwezi kuwa baada ya leo', 'error'); return; }
            if (toDate > today) { this.showNotification('Tarehe ya mwisho haiwezi kuwa baada ya leo', 'error'); return; }
            if (fromDate > toDate) { this.showNotification('Tarehe ya kuanzia haiwezi kuwa baada ya tarehe ya mwisho', 'error'); return; }
            
            try {
                document.getElementById('loading-spinner').classList.remove('hidden');
                this.customSummary = null;
                const response = await fetch(`/uchambuzi/mwenendo?from=${this.dateFrom}&to=${this.dateTo}`);
                if (!response.ok) throw new Error(`Server returned ${response.status}`);
                const data = await response.json();
                this.customSummary = data;
                this.showNotification('Data imepakuliwa kwa mafanikio', 'success');
            } catch(error) { 
                console.error('Hitilafu:', error);
                this.showNotification('Kumetokea hitilafu wakati wa kupakua data', 'error');
            } finally {
                document.getElementById('loading-spinner').classList.add('hidden');
            }
        },

        refresh() {
            this.updateSummary();
            this.customSummary = null;
            this.manualDateSelect = false;
            this.setDefaultDates();
            this.showNotification('Data imeburushwa upya', 'info');
        },

        setDefaultDates() {
            const today = new Date();
            const weekAgo = new Date();
            weekAgo.setDate(today.getDate() - 7);
            this.dateFrom = weekAgo.toISOString().split('T')[0];
            this.dateTo = today.toISOString().split('T')[0];
        },

        initMwenendo() { 
            this.setDefaultDates();
            this.updateSummary(); 
        },

        showNotification(message, type = 'info') {
            const container = document.getElementById('notification-container');
            if (!container) return;
            const colors = {
                success: 'bg-emerald-50 border-emerald-300 text-emerald-800',
                error: 'bg-red-50 border-red-300 text-red-800',
                warning: 'bg-amber-50 border-amber-300 text-amber-800',
                info: 'bg-blue-50 border-blue-300 text-blue-800'
            };
            const notification = document.createElement('div');
            notification.className = `rounded-lg border-l-4 px-4 py-2 text-xs font-semibold mb-2 shadow-sm animate-fade-in ${colors[type]}`;
            notification.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'} mr-2"></i>${message}`;
            container.appendChild(notification);
            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transform = 'translateY(-10px)';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }
    }));

    Alpine.data('kampuniData', () => ({
        beforeSales: {{ $thamaniBefore ?? 0 }},
        afterSales: {{ $thamaniAfter ?? 0 }},
        faida: {{ $faida ?? 0 }},
        formatCurrency(value) {
            return formatCurrency(value);
        }
    }));
});

document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        const alpineData = document.querySelector('[x-data="dashboardApp()"]')?.__x;
        if (alpineData && alpineData.$data.tab === 'graphs') {
            alpineData.$data.drawChart(alpineData.$data.graph);
        }
    }, 200);
});
</script>
@endpush