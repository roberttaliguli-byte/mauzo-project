@extends('layouts.app')

@section('title', 'Uchambuzi wa Biashara')
@section('page-title', 'Dashibodi ya Taarifa')
@section('page-subtitle', 'Uchambuzi wa Biashara kwa Grafu na Takwimu')

@push('styles')
<style>
    /* modern font system - Poppins for modern look with excellent readability */
    @import url('https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&family=Poppins:wght@300;400;500;600;700;800&display=swap');
    
    * {
        font-family: 'Poppins', 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, Helvetica, sans-serif;
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
    
    /* Smartphone optimizations */
    @media (max-width: 640px) {
        .graph-container {
            min-height: 280px;
        }
        
        .stat-card p.text-base {
            font-size: 0.85rem;
        }
        
        .stat-card p.text-lg {
            font-size: 0.95rem;
        }
        
        button, .btn-like {
            touch-action: manipulation;
        }
        
        /* Better touch targets for mobile */
        .px-4.py-2 {
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }
        
        /* Improved mobile spacing for date picker */
        .date-picker-container {
            padding: 0.75rem;
        }
        
        .date-input-group {
            margin-bottom: 0.75rem;
        }
    }
    
    /* Improved font weights for better readability */
    .font-semibold {
        font-weight: 600;
    }
    
    .font-bold {
        font-weight: 700;
    }
    
    .font-medium {
        font-weight: 500;
    }
    
    /* Chart text improvements */
    canvas {
        font-family: 'Poppins', 'Inter', system-ui, sans-serif !important;
    }
    
    /* Enhanced date picker styling */
    .date-picker-card {
        background: linear-gradient(135deg, #fef9e6 0%, #ffffff 100%);
        border-radius: 1rem;
        transition: all 0.3s ease;
    }
    
    .date-input-modern {
        border: 2px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 0.75rem 1rem;
        font-size: 0.9rem;
        font-weight: 500;
        transition: all 0.2s ease;
        background-color: white;
    }
    
    .date-input-modern:focus {
        border-color: #f59e0b;
        outline: none;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
    }
    
    .action-button {
        border-radius: 0.75rem;
        padding: 0.75rem 1rem;
        font-weight: 600;
        transition: all 0.2s ease;
    }
    
    .action-button-primary {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
    }
    
    .action-button-primary:active {
        transform: scale(0.98);
    }
    
    .action-button-secondary {
        background-color: #f3f4f6;
        color: #374151;
    }
    
    .action-button-secondary:active {
        transform: scale(0.98);
    }
    
    /* Result card styling */
    .result-card {
        background: linear-gradient(135deg, #ffffff 0%, #fef9e6 100%);
        border-radius: 1rem;
        border: 1px solid #fde68a;
        padding: 1rem;
    }
    
    .result-stat-item {
        background: white;
        border-radius: 0.75rem;
        padding: 0.75rem;
        transition: all 0.2s ease;
        border-left: 3px solid #f59e0b;
    }
    
    .result-stat-item:hover {
        transform: translateX(4px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
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

        <!-- MWENENDO TAB - Full original data preserved with improved date picker UI -->
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
                        <label class="block text-xs font-bold text-gray-700 mb-2">⏳ Chagua Muda:</label>
                        <select x-model="viewType" @change="toggleDatePicker(false)" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl text-sm font-semibold focus:ring-2 focus:ring-amber-400 focus:border-amber-400 bg-white transition-all">
                            <option value="Siku">📅 Siku</option>
                            <option value="Wiki">📆 Wiki</option>
                            <option value="Mwezi">📊 Mwezi</option>
                            <option value="Mwaka">🎯 Mwaka</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button @click="toggleDatePicker(true)" class="w-full bg-gradient-to-r from-amber-600 to-amber-500 text-white px-4 py-2.5 rounded-xl hover:from-amber-700 shadow-md text-sm font-bold transition-all transform active:scale-95">
                            <i class="fas fa-calendar-alt mr-2"></i> Chagua Tarehe Maalum
                        </button>
                    </div>
                </div>

                <!-- Auto Summary - Full data with high contrast -->
                <div x-show="!manualDateSelect" class="bg-gray-50 border border-gray-200 rounded-xl p-4 space-y-3">
                    <h3 class="font-bold text-gray-800 text-sm flex items-center">
                        <i class="fas fa-chart-simple text-amber-500 mr-2"></i>
                        Muhtasari wa <span x-text="viewType" class="text-amber-600 mx-1"></span>
                        <span x-show="summary?.date" class="text-xs text-gray-500 font-normal">(<span x-text="summary?.date"></span>)</span>
                    </h3>
                    <template x-if="summary">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                            <div class="bg-white border-l-4 border-amber-500 rounded-xl p-3 shadow-sm stat-card"><p class="text-xs text-gray-500 mb-1">Mapato ya Mauzo</p><p class="text-base font-bold text-gray-900" x-text="formatCurrency(summary.mapato_mauzo)"></p></div>
                            <div class="bg-white border-l-4 border-amber-500 rounded-xl p-3 shadow-sm stat-card"><p class="text-xs text-gray-500 mb-1">Mapato ya Madeni</p><p class="text-base font-bold text-gray-900" x-text="formatCurrency(summary.mapato_madeni)"></p></div>
                            <div class="bg-white border-l-4 border-amber-500 rounded-xl p-3 shadow-sm stat-card"><p class="text-xs text-gray-500 mb-1">Jumla ya Mapato</p><p class="text-base font-bold text-gray-900" x-text="formatCurrency(summary.jumla_mapato)"></p></div>
                            <div class="bg-white border-l-4 border-amber-500 rounded-xl p-3 shadow-sm stat-card"><p class="text-xs text-gray-500 mb-1">Jumla ya Matumizi</p><p class="text-base font-bold text-gray-900" x-text="formatCurrency(summary.jumla_mat)"></p></div>
                            <div class="bg-white border-l-4 border-amber-500 rounded-xl p-3 shadow-sm stat-card"><p class="text-xs text-gray-500 mb-1">Gharama ya Bidhaa</p><p class="text-base font-bold text-gray-900" x-text="formatCurrency(summary.gharama_bidhaa)"></p></div>
                            <div class="bg-white border-l-4 border-amber-500 rounded-xl p-3 shadow-sm stat-card"><p class="text-xs text-gray-500 mb-1">Faida ya Mauzo</p><p class="text-base font-bold text-gray-900" x-text="formatCurrency(summary.faida_mauzo)"></p></div>
                            <div class="bg-white border-l-4 border-amber-500 rounded-xl p-3 shadow-sm stat-card"><p class="text-xs text-gray-500 mb-1">Faida ya Marejesho</p><p class="text-base font-bold text-gray-900" x-text="formatCurrency(summary.faida_marejesho)"></p></div>
                            <div class="bg-white border-l-4 border-amber-500 rounded-xl p-3 shadow-sm stat-card"><p class="text-xs text-gray-500 mb-1">Fedha Dukani</p><p class="text-base font-bold text-gray-900" x-text="formatCurrency(summary.fedha_droo)"></p></div>
                            <div class="bg-white border-l-4 border-emerald-500 rounded-xl p-3 shadow-sm lg:col-span-2 stat-card"><p class="text-xs text-gray-500 mb-1">Faida Halisi</p><p class="text-lg font-bold text-emerald-700" x-text="formatCurrency(summary.faida_halisi)"></p></div>
                        </div>
                    </template>
                    <template x-if="!summary">
                        <div class="text-center py-6 text-gray-500 text-sm">Hakuna data inayopatikana kwa kipindi hiki.</div>
                    </template>
                </div>

                <!-- IMPROVED Manual Date Selection with better mobile alignment -->
                <div x-show="manualDateSelect" class="date-picker-card p-5 bg-gradient-to-br from-amber-50 to-white border-2 border-amber-100 rounded-2xl shadow-lg">
                    <div class="flex items-center gap-2 mb-4 pb-2 border-b border-amber-200">
                        <i class="fas fa-calendar-alt text-amber-600 text-lg"></i>
                        <h3 class="font-bold text-gray-800 text-base">📅 Angalia Mwenendo kwa Tarehe Maalum</h3>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="date-input-group">
                                <label class="block text-xs font-bold text-gray-600 mb-2 flex items-center gap-1">
                                    <i class="fas fa-calendar-day text-amber-500"></i>
                                    Kuanzia Tarehe:
                                </label>
                                <input type="date" x-model="dateFrom" 
                                       class="date-input-modern w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm font-medium focus:border-amber-400 focus:ring-2 focus:ring-amber-200 transition-all">
                            </div>
                            <div class="date-input-group">
                                <label class="block text-xs font-bold text-gray-600 mb-2 flex items-center gap-1">
                                    <i class="fas fa-calendar-week text-amber-500"></i>
                                    Mpaka Tarehe:
                                </label>
                                <input type="date" x-model="dateTo" 
                                       class="date-input-modern w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm font-medium focus:border-amber-400 focus:ring-2 focus:ring-amber-200 transition-all">
                            </div>
                        </div>
                        
                        <div class="flex flex-col sm:flex-row gap-3 pt-2">
                            <button @click="fetchCustomSummary()" 
                                    class="action-button action-button-primary flex-1 flex items-center justify-center gap-2 bg-gradient-to-r from-amber-600 to-amber-500 text-white px-5 py-3 rounded-xl hover:from-amber-700 hover:to-amber-600 transition-all font-bold shadow-md transform active:scale-95">
                                <i class="fas fa-search"></i>
                                <span>Angalia Takwimu</span>
                            </button>
                            <button @click="toggleDatePicker(false)" 
                                    class="action-button action-button-secondary flex-1 flex items-center justify-center gap-2 bg-gray-100 text-gray-700 px-5 py-3 rounded-xl hover:bg-gray-200 transition-all font-semibold border border-gray-200">
                                <i class="fas fa-arrow-left"></i>
                                <span>Rudi kwa Muhtasari</span>
                            </button>
                        </div>
                    </div>
                    
                    <template x-if="customSummary">
                        <div class="result-card mt-5 animate-fade-in">
                            <div class="flex items-center gap-2 mb-3 pb-2 border-b border-amber-200">
                                <i class="fas fa-chart-line text-amber-600"></i>
                                <h4 class="text-amber-700 font-bold text-sm">Matokeo ya kipindi maalum</h4>
                                <span class="text-xs text-gray-500 ml-auto" x-text="dateFrom + ' → ' + dateTo"></span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="result-stat-item"><p class="text-xs text-gray-500 mb-1">Mapato ya Mauzo</p><p class="text-sm font-bold text-gray-800" x-text="formatCurrency(customSummary.mapato_mauzo)"></p></div>
                                <div class="result-stat-item"><p class="text-xs text-gray-500 mb-1">Mapato ya Madeni</p><p class="text-sm font-bold text-gray-800" x-text="formatCurrency(customSummary.mapato_madeni)"></p></div>
                                <div class="result-stat-item"><p class="text-xs text-gray-500 mb-1">Jumla ya Mapato</p><p class="text-sm font-bold text-gray-800" x-text="formatCurrency(customSummary.jumla_mapato)"></p></div>
                                <div class="result-stat-item"><p class="text-xs text-gray-500 mb-1">Jumla ya Matumizi</p><p class="text-sm font-bold text-gray-800" x-text="formatCurrency(customSummary.jumla_mat)"></p></div>
                                <div class="result-stat-item"><p class="text-xs text-gray-500 mb-1">Gharama ya Bidhaa</p><p class="text-sm font-bold text-gray-800" x-text="formatCurrency(customSummary.gharama_bidhaa)"></p></div>
                                <div class="result-stat-item"><p class="text-xs text-gray-500 mb-1">Faida ya Mauzo</p><p class="text-sm font-bold text-gray-800" x-text="formatCurrency(customSummary.faida_mauzo)"></p></div>
                                <div class="result-stat-item"><p class="text-xs text-gray-500 mb-1">Faida ya Marejesho</p><p class="text-sm font-bold text-gray-800" x-text="formatCurrency(customSummary.faida_marejesho)"></p></div>
                                <div class="result-stat-item"><p class="text-xs text-gray-500 mb-1">Fedha Dukani</p><p class="text-sm font-bold text-gray-800" x-text="formatCurrency(customSummary.fedha_droo)"></p></div>
                                <div class="result-stat-item col-span-1 sm:col-span-2 bg-gradient-to-r from-emerald-50 to-white border-l-4 border-emerald-500"><p class="text-xs text-gray-500 mb-1">Faida Halisi</p><p class="text-base font-bold text-emerald-700" x-text="formatCurrency(customSummary.faida_halisi)"></p></div>
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

// Generate dynamic colors for each product based on algorithm
function generateDynamicColors(count) {
    const colors = [];
    for (let i = 0; i < count; i++) {
        // Algorithm: Use golden ratio to generate distinct colors
        const hue = (i * 0.618033988749895 * 360) % 360;
        colors.push(`hsla(${hue}, 70%, 55%, 0.85)`);
    }
    return colors;
}

function generateBorderColors(baseColors) {
    return baseColors.map(color => color.replace('0.85', '1').replace('hsla', 'hsl'));
}

// Chart storage
let chartInstances = {};

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
            
            switch(name) {
                case 'faidaBidhaa': 
                    config = this.barChartWithDynamicColors(this.faidaByBidhaa, 'jina', 'faida', 'Faida kwa Bidhaa');
                    break;
                case 'faidaSiku': 
                    config = this.lineChart(this.faidaBySiku, 'day', 'faida', '#10b981', 'Faida kwa Siku');
                    break;
                case 'mauzoSiku': 
                    config = this.lineChart(this.mauzoBySiku, 'day', 'total', '#3b82f6', 'Mauzo kwa Siku');
                    break;
                case 'marejesho': 
                    config = this.barChartWithDynamicColors(this.marejeshoByBidhaa, 'jina', 'total', 'Faida ya Marejesho');
                    break;
                case 'mauzo': 
                    config = this.barChartWithDynamicColors(this.mauzoByBidhaa, 'jina', 'total', 'Mauzo Jumla');
                    break;
            }

            try {
                chartInstances[name] = new Chart(ctx, config);
            } catch (e) {
                console.error('Error creating chart:', e);
            }
        },

        barChartWithDynamicColors(data, labelKey, valueKey, label) {
            const labels = data.map(i => i[labelKey] || 'N/A');
            const values = data.map(i => Math.max(0, Number(i[valueKey]) || 0));
            const dynamicColors = generateDynamicColors(labels.length);
            const borderColors = generateBorderColors(dynamicColors);
            
            return {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{ 
                        label: label, 
                        data: values, 
                        backgroundColor: dynamicColors,
                        borderColor: borderColors,
                        borderWidth: 2,
                        borderRadius: 8,
                        hoverOffset: 4
                    }]
                },
                options: this.chartOptions()
            };
        },

        lineChart(data, labelKey, valueKey, color, label) {
            const labels = data.map(i => i[labelKey] || 'N/A');
            const values = data.map(i => Math.max(0, Number(i[valueKey]) || 0));
            
            return {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{ 
                        label: label, 
                        data: values, 
                        borderColor: color,
                        backgroundColor: color + '20',
                        borderWidth: 3,
                        tension: 0.3,
                        fill: true,
                        pointBackgroundColor: color,
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        pointStyle: 'circle'
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
                                family: "'Poppins', 'Inter', system-ui",
                                size: window.innerWidth < 768 ? 11 : 12,
                                weight: 'bold'
                            },
                            padding: 15,
                            usePointStyle: true,
                            pointStyle: 'rectRounded'
                        }
                    }, 
                    tooltip: { 
                        enabled: true,
                        backgroundColor: 'rgba(0, 0, 0, 0.85)',
                        titleColor: '#fbbf24',
                        bodyColor: '#fff',
                        titleFont: { family: "'Poppins'", size: 12, weight: 'bold' },
                        bodyFont: { family: "'Poppins'", size: 11 },
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
                        grid: { color: 'rgba(0, 0, 0, 0.05)', drawBorder: true },
                        ticks: {
                            color: '#374151',
                            font: { family: "'Poppins'", size: window.innerWidth < 768 ? 10 : 11, weight: '500' },
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
                            font: { family: "'Poppins'", size: window.innerWidth < 768 ? 9 : 10, weight: '500' },
                            maxRotation: 45,
                            minRotation: 35,
                            autoSkip: true,
                            maxTicksLimit: 8
                        }
                    }
                },
                animation: {
                    duration: 750,
                    easing: 'easeInOutQuart'
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