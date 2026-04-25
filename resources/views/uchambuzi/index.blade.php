@extends('layouts.app')

@section('title', 'Uchambuzi wa Biashara')
@section('page-title', 'Dashibodi ya Taarifa')
@section('page-subtitle', 'Uchambuzi wa Biashara kwa Grafu na Takwimu')

@push('styles')
<style>
    /* ========== MODERN FONT SYSTEM ========== */
    @import url('https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&family=Poppins:wght@300;400;500;600;700;800&display=swap');
    * { font-family: 'Poppins', 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, Helvetica, sans-serif; }
    .amber-label { color: #b45309 !important; font-weight: 700; letter-spacing: -0.01em; }
    .bg-amber-soft { background-color: #fffbeb; }
    .text-contrast-white { color: #ffffff; text-shadow: 0 1px 1px rgba(0,0,0,0.15); }
    .card-hover { transition: all 0.2s ease; }
    .card-hover:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.08); }
    .table-row-highlight:hover { background-color: #fef3c7; }
    .chart-wrapper canvas { max-height: 100%; width: 100% !important; }
    .graph-container { overflow-x: auto; overflow-y: visible; min-height: 320px; }
    @media (min-width: 768px) { .graph-container { min-height: 400px; } }
    .table-responsive { display: block; width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .custom-scroll-x { scrollbar-width: thin; }
    [x-cloak] { display: none !important; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fadeIn 0.25s ease-out; }
    .stat-card { transition: all 0.2s; border-left: 3px solid #f59e0b; }
    .stat-card:hover { background-color: #fef9e6; }
    @media (max-width: 640px) {
        .graph-container { min-height: 280px; }
        .stat-card p.text-base { font-size: 0.85rem; }
        .stat-card p.text-lg { font-size: 0.95rem; }
        button, .btn-like { touch-action: manipulation; }
        .px-4.py-2 { padding-left: 0.75rem; padding-right: 0.75rem; }
        .date-picker-container, .date-input-group { padding: 0.75rem; margin-bottom: 0.75rem; }
    }
    .font-semibold { font-weight: 600; }
    .font-bold { font-weight: 700; }
    .font-medium { font-weight: 500; }
    canvas { font-family: 'Poppins', 'Inter', system-ui, sans-serif !important; }
    .date-picker-card { background: linear-gradient(135deg, #fef9e6 0%, #ffffff 100%); border-radius: 1rem; transition: all 0.3s ease; }
    .date-input-modern { border: 2px solid #e5e7eb; border-radius: 0.75rem; padding: 0.75rem 1rem; font-size: 0.9rem; font-weight: 500; transition: all 0.2s ease; background-color: white; }
    .date-input-modern:focus { border-color: #f59e0b; outline: none; box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1); }
    .action-button { border-radius: 0.75rem; padding: 0.75rem 1rem; font-weight: 600; transition: all 0.2s ease; }
    .action-button-primary { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; }
    .action-button-primary:active { transform: scale(0.98); }
    .action-button-secondary { background-color: #f3f4f6; color: #374151; }
    .result-card { background: linear-gradient(135deg, #ffffff 0%, #fef9e6 100%); border-radius: 1rem; border: 1px solid #fde68a; padding: 1rem; }
    .result-stat-item { background: white; border-radius: 0.75rem; padding: 0.75rem; transition: all 0.2s ease; border-left: 3px solid #f59e0b; }
    .result-stat-item:hover { transform: translateX(4px); box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
</style>
@endpush

@section('content')
<div class="space-y-5" x-data="dashboardApp()" x-init="init()">
    <!-- Notifications container -->
    <div id="notification-container" class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-sm px-4 pointer-events-none"></div>

    <!-- Header -->
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
                <button @click="setTab('graphs')" :class="tab === 'graphs' ? 'bg-amber-600 text-white shadow-md' : 'bg-gray-100 text-gray-800 hover:bg-gray-200'" class="px-4 py-2 rounded-lg font-semibold transition-all duration-200 text-sm"><i class="fas fa-chart-bar mr-1"></i> Grafu</button>
                <button @click="setTab('mwenendo')" :class="tab === 'mwenendo' ? 'bg-amber-600 text-white shadow-md' : 'bg-gray-100 text-gray-800 hover:bg-gray-200'" class="px-4 py-2 rounded-lg font-semibold transition-all duration-200 text-sm"><i class="fas fa-chart-line mr-1"></i> Mwenendo</button>
                <button @click="setTab('kampuni')" :class="tab === 'kampuni' ? 'bg-amber-600 text-white shadow-md' : 'bg-gray-100 text-gray-800 hover:bg-gray-200'" class="px-4 py-2 rounded-lg font-semibold transition-all duration-200 text-sm"><i class="fas fa-building mr-1"></i> Kampuni</button>
                <a href="{{ route('user.reports.select') }}" class="px-4 py-2 rounded-lg font-semibold transition-all duration-200 bg-gray-100 text-gray-800 hover:bg-gray-200 text-sm"><i class="fas fa-file-alt mr-1"></i> Ripoti</a>
                <button @click="setTab('historia')" :class="tab === 'historia' ? 'bg-amber-600 text-white shadow-md' : 'bg-gray-100 text-gray-800 hover:bg-gray-200'" class="px-4 py-2 rounded-lg font-semibold transition-all duration-200 text-sm"><i class="fas fa-history mr-1"></i> Historia</button>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="space-y-5">

        <!-- ================= GRAPHS TAB ================= -->
        <div x-show="tab === 'graphs'" class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm" x-cloak x-transition:enter.duration.300ms>
            <div class="p-4 border-b border-gray-200 bg-amber-soft">
                <h2 class="text-sm font-bold flex items-center text-gray-800"><i class="fas fa-chart-pie text-amber-600 mr-2"></i><span class="amber-label">Chagua Aina ya Grafu</span></h2>
                <p class="text-xs text-gray-700 mt-1 font-medium">Bonyeza kwenye kitufe kuona uchambuzi tofauti | Majina yote yataonekana kwenye grafu</p>
            </div>
            <div class="p-4">
                <div class="flex flex-wrap gap-2 mb-5 border-b border-gray-200 pb-3 overflow-x-auto custom-scroll-x">
                    <template x-for="(item, key) in graphTabs" :key="key">
                        <button @click="setGraph(key)" :class="graph === key ? getActiveClass(key) : 'bg-gray-100 text-gray-800 hover:bg-gray-200'" class="px-4 py-2 rounded-lg transition-all duration-200 text-xs md:text-sm whitespace-nowrap font-semibold" x-text="item"></button>
                    </template>
                </div>
                <div class="graph-container w-full">
                    <div x-show="graph === 'faidaBidhaa'" x-cloak><canvas id="faidaBidhaaChart" class="w-full" style="max-height: 380px; width:100%"></canvas></div>
                    <div x-show="graph === 'faidaSiku'" x-cloak><canvas id="faidaSikuChart" class="w-full" style="max-height: 380px; width:100%"></canvas></div>
                    <div x-show="graph === 'mauzoSiku'" x-cloak><canvas id="mauzoSikuChart" class="w-full" style="max-height: 380px; width:100%"></canvas></div>
                    <div x-show="graph === 'marejesho'" x-cloak><canvas id="marejeshoChart" class="w-full" style="max-height: 380px; width:100%"></canvas></div>
                    <div x-show="graph === 'mauzo'" x-cloak><canvas id="mauzoChart" class="w-full" style="max-height: 380px; width:100%"></canvas></div>
                </div>
                <p class="text-[10px] text-gray-500 mt-3 text-center"><i class="fas fa-arrows-alt-h mr-1"></i> Scroll kulia/kushoto kuona majina yote ya bidhaa</p>
            </div>
        </div>

        <!-- ================= MWENENDO TAB ================= -->
        <div x-show="tab === 'mwenendo'" class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm" x-cloak x-data="mwenendoApp()" x-init="initMwenendo()" x-transition:enter.duration.300ms>
            <div class="p-4 border-b border-gray-200 bg-amber-soft">
                <h2 class="text-sm font-bold text-gray-800 flex items-center"><i class="fas fa-chart-line text-amber-600 mr-2"></i><span class="amber-label">Mwenendo wa Biashara</span></h2>
                <p class="text-xs text-gray-700 mt-1 font-medium">Chagua kipindi au tarehe kuona takwimu kamili</p>
            </div>
            <div class="p-4">
                <div class="flex justify-end mb-4"><button @click="refresh()" class="bg-amber-600 text-white px-3 py-2 rounded-lg text-sm"><i class="fas fa-sync-alt"></i> Pakua Upya</button></div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div><label class="block text-xs font-bold text-gray-700 mb-2">⏳ Chagua Muda:</label><select x-model="viewType" @change="toggleDatePicker(false)" class="w-full px-4 py-2.5 border-2 rounded-xl text-sm"><option value="Siku">📅 Siku</option><option value="Wiki">📆 Wiki</option><option value="Mwezi">📊 Mwezi</option><option value="Mwaka">🎯 Mwaka</option></select></div>
                    <div><button @click="toggleDatePicker(true)" class="w-full bg-gradient-to-r from-amber-600 to-amber-500 text-white px-4 py-2.5 rounded-xl text-sm font-bold"><i class="fas fa-calendar-alt mr-2"></i> Chagua Tarehe Maalum</button></div>
                </div>
                <div x-show="!manualDateSelect" class="bg-gray-50 border rounded-xl p-4">
                    <h3 class="font-bold text-sm">Muhtasari wa <span x-text="viewType"></span> <span x-show="summary?.date" class="text-xs">(<span x-text="summary?.date"></span>)</span></h3>
                    <template x-if="summary">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mt-3">
                            <div class="bg-white border-l-4 border-amber-500 rounded-xl p-3"><p class="text-xs text-gray-500">Mapato ya Mauzo</p><p class="text-base font-bold" x-text="formatCurrency(summary.mapato_mauzo)"></p></div>
                            <div class="bg-white border-l-4 border-amber-500 rounded-xl p-3"><p class="text-xs text-gray-500">Mapato ya Madeni</p><p class="text-base font-bold" x-text="formatCurrency(summary.mapato_madeni)"></p></div>
                            <div class="bg-white border-l-4 border-amber-500 rounded-xl p-3"><p class="text-xs text-gray-500">Jumla ya Mapato</p><p class="text-base font-bold" x-text="formatCurrency(summary.jumla_mapato)"></p></div>
                            <div class="bg-white border-l-4 border-amber-500 rounded-xl p-3"><p class="text-xs text-gray-500">Jumla ya Matumizi</p><p class="text-base font-bold" x-text="formatCurrency(summary.jumla_mat)"></p></div>
                            <div class="bg-white border-l-4 border-amber-500 rounded-xl p-3"><p class="text-xs text-gray-500">Gharama ya Bidhaa</p><p class="text-base font-bold" x-text="formatCurrency(summary.gharama_bidhaa)"></p></div>
                            <div class="bg-white border-l-4 border-amber-500 rounded-xl p-3"><p class="text-xs text-gray-500">Faida ya Mauzo</p><p class="text-base font-bold" x-text="formatCurrency(summary.faida_mauzo)"></p></div>
                            <div class="bg-white border-l-4 border-amber-500 rounded-xl p-3"><p class="text-xs text-gray-500">Faida ya Marejesho</p><p class="text-base font-bold" x-text="formatCurrency(summary.faida_marejesho)"></p></div>
                            <div class="bg-white border-l-4 border-amber-500 rounded-xl p-3"><p class="text-xs text-gray-500">Fedha Dukani</p><p class="text-base font-bold" x-text="formatCurrency(summary.fedha_droo)"></p></div>
                            <div class="bg-white border-l-4 border-emerald-500 rounded-xl p-3 lg:col-span-2"><p class="text-xs text-gray-500">Faida Halisi</p><p class="text-lg font-bold text-emerald-700" x-text="formatCurrency(summary.faida_halisi)"></p></div>
                        </div>
                    </template>
                </div>
                <div x-show="manualDateSelect" class="date-picker-card p-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <input type="date" x-model="dateFrom" class="date-input-modern">
                        <input type="date" x-model="dateTo" class="date-input-modern">
                    </div>
                    <div class="flex gap-3 mt-4">
                        <button @click="fetchCustomSummary()" class="action-button-primary flex-1 py-2 rounded-xl">Angalia Takwimu</button>
                        <button @click="toggleDatePicker(false)" class="action-button-secondary flex-1 py-2 rounded-xl">Rudi</button>
                    </div>
                    <template x-if="customSummary">
                        <div class="result-card mt-5">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="result-stat-item"><p class="text-xs">Mapato ya Mauzo</p><p class="text-sm font-bold" x-text="formatCurrency(customSummary.mapato_mauzo)"></p></div>
                                <div class="result-stat-item"><p class="text-xs">Mapato ya Madeni</p><p class="text-sm font-bold" x-text="formatCurrency(customSummary.mapato_madeni)"></p></div>
                                <div class="result-stat-item"><p class="text-xs">Jumla ya Mapato</p><p class="text-sm font-bold" x-text="formatCurrency(customSummary.jumla_mapato)"></p></div>
                                <div class="result-stat-item"><p class="text-xs">Jumla ya Matumizi</p><p class="text-sm font-bold" x-text="formatCurrency(customSummary.jumla_mat)"></p></div>
                                <div class="result-stat-item"><p class="text-xs">Gharama ya Bidhaa</p><p class="text-sm font-bold" x-text="formatCurrency(customSummary.gharama_bidhaa)"></p></div>
                                <div class="result-stat-item"><p class="text-xs">Faida ya Mauzo</p><p class="text-sm font-bold" x-text="formatCurrency(customSummary.faida_mauzo)"></p></div>
                                <div class="result-stat-item"><p class="text-xs">Faida ya Marejesho</p><p class="text-sm font-bold" x-text="formatCurrency(customSummary.faida_marejesho)"></p></div>
                                <div class="result-stat-item"><p class="text-xs">Fedha Dukani</p><p class="text-sm font-bold" x-text="formatCurrency(customSummary.fedha_droo)"></p></div>
                                <div class="result-stat-item col-span-2 bg-gradient-to-r from-emerald-50"><p class="text-xs">Faida Halisi</p><p class="text-base font-bold text-emerald-700" x-text="formatCurrency(customSummary.faida_halisi)"></p></div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- ================= KAMPUNI TAB ================= -->
        <div x-show="tab === 'kampuni'" x-data="kampuniData()" class="bg-white rounded-xl border border-gray-200 shadow-sm" x-cloak>
            <div class="p-4 border-b border-gray-200 bg-amber-soft"><h2 class="text-sm font-bold"><i class="fas fa-building text-amber-600 mr-2"></i><span class="amber-label">Thamani ya Kampuni</span></h2></div>
            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div class="stat-card bg-gray-50 p-3"><p class="text-xs">Thamani Kabla (Bei ya Nunua)</p><p class="text-lg font-bold" x-text="formatCurrency(beforeSales)"></p></div>
                    <div class="stat-card bg-gray-50 p-3"><p class="text-xs">Thamani Baada (Bei ya Kuuza)</p><p class="text-lg font-bold" x-text="formatCurrency(afterSales)"></p></div>
                    <div class="stat-card bg-amber-50 border-l-amber-700 p-3"><p class="text-xs text-amber-800 font-bold">Faida ya Uwezo</p><p class="text-xl font-bold text-amber-800" x-text="formatCurrency(faida)"></p></div>
                </div>
                <div class="bg-gradient-to-r from-amber-50 to-yellow-50 rounded-lg p-4 mb-4 border border-amber-200">
                    <p class="text-xs text-amber-800 font-bold">Thamani ya Jumla ya Kampuni</p>
                    <p class="text-2xl font-extrabold text-amber-800">{{ $thamaniKampuniFormatted }}</p>
                </div>
                <div><h3 class="font-bold mb-3">Orodha kamili ya Bidhaa</h3>
                    <div class="overflow-x-auto border rounded-lg">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-100">
                                <tr><th class="px-3 py-2 text-left">Bidhaa</th><th class="px-3 py-2 text-left hidden sm:table-cell">Aina</th><th class="px-3 py-2 text-left hidden md:table-cell">Kipimo</th><th class="px-3 py-2 text-right">Idadi</th><th class="px-3 py-2 text-right hidden lg:table-cell">Nunua</th><th class="px-3 py-2 text-right">Kuuza</th><th class="px-3 py-2 text-right">Faida</th></tr>
                            </thead>
                            <tbody>
                                @foreach($bidhaaList as $item)
                                <tr class="hover:bg-amber-50"><td class="px-3 py-2">{{ $item->jina }}</td><td class="px-3 py-2 hidden sm:table-cell">{{ $item->aina }}</td><td class="px-3 py-2 hidden md:table-cell">{{ $item->kipimo ?? '-' }}</td><td class="px-3 py-2 text-right">{{ number_format($item->idadi) }}</td><td class="px-3 py-2 text-right hidden lg:table-cell">Tsh {{ number_format($item->bei_nunua,0) }}</td><td class="px-3 py-2 text-right">Tsh {{ number_format($item->bei_kuuza,0) }}</td><td class="px-3 py-2 text-right font-bold text-amber-700">Tsh {{ number_format($item->faida,0) }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

<!-- HISTORIA TAB -->
<div x-show="tab === 'historia'" class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm" x-cloak x-transition:enter.duration.300ms">
    <div class="p-4 border-b border-gray-200 bg-amber-soft">
        <h2 class="text-sm font-bold flex items-center text-gray-800">
            <i class="fas fa-history text-amber-600 mr-2"></i>
            <span class="amber-label">Historia ya Mfumo</span>
        </h2>
        <p class="text-xs text-gray-700 mt-1 font-medium">Taarifa za kuingia mfumo na shughuli za hivi karibuni</p>
    </div>

    <div class="p-4 space-y-6">
        <!-- LOGIN HISTORY (last 5) -->
        <div>
            <div class="flex items-center gap-2 mb-3">
                <i class="fas fa-sign-in-alt text-amber-600 text-sm"></i>
                <h3 class="text-sm font-bold text-gray-800">Historia ya Kuingia Mfumo</h3>
                <span class="text-xs text-gray-500">(5 za hivi karibuni)</span>
            </div>
            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 border-b-2 border-gray-200">
                        <tr>
                            <th class="px-3 py-2 text-left font-bold text-gray-700">Mtumiaji</th>
                            <th class="px-3 py-2 text-left font-bold text-gray-700">Muda wa Kuingia</th>
                            <th class="px-3 py-2 text-left font-bold text-gray-700 hidden md:table-cell">Muda wa Kutoka</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($loginHistories as $login)
                            <tr class="hover:bg-amber-50">
                                <td class="px-3 py-2">{{ $login->user_name }}</td>
                                <td class="px-3 py-2">{{ $login->login_at ? $login->login_at->format('d/m/Y H:i:s') : '-' }}</td>
                                <td class="px-3 py-2 hidden md:table-cell">{{ $login->logout_at ? $login->logout_at->format('d/m/Y H:i:s') : '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-3 py-6 text-center text-gray-500">Hakuna historia ya kuingia bado</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MAUZO -->
        <div>
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <i class="fas fa-shopping-cart text-emerald-600 text-sm"></i>
                    <h3 class="text-sm font-bold text-gray-800">Mauzo</h3>
                    <span class="text-xs text-gray-500">(5 za hivi karibuni)</span>
                </div>
                <button @click="viewAllActivities('sales', 'Mauzo Yote')" class="text-xs text-amber-600 hover:text-amber-800 font-semibold">
                    <i class="fas fa-eye mr-1"></i> Tazama Zote
                </button>
            </div>
            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 border-b">
                        <tr><th class="px-3 py-2 text-left">Maelezo</th><th class="px-3 py-2 text-left hidden sm:table-cell">Mtumiaji</th><th class="px-3 py-2 text-left">Tarehe</th></tr>
                    </thead>
                    <tbody>
                        @forelse($recentSales as $sale)
                            <tr class="hover:bg-emerald-50">
                                <td class="px-3 py-2">{{ $sale->description }}</td>
                                <td class="px-3 py-2 hidden sm:table-cell">{{ $sale->user_name }}</td>
                                <td class="px-3 py-2 text-xs">{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-3 py-2 text-gray-500">Hakuna mauzo</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MANUNUZI -->
        <div>
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <i class="fas fa-truck text-blue-600 text-sm"></i>
                    <h3 class="text-sm font-bold text-gray-800">Manunuzi</h3>
                    <span class="text-xs text-gray-500">(5 za hivi karibuni)</span>
                </div>
                <button @click="viewAllActivities('purchases', 'Manunuzi Yote')" class="text-xs text-amber-600 hover:text-amber-800 font-semibold">
                    <i class="fas fa-eye mr-1"></i> Tazama Zote
                </button>
            </div>
            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 border-b">
                        <tr><th class="px-3 py-2 text-left">Maelezo</th><th class="px-3 py-2 text-left hidden sm:table-cell">Mtumiaji</th><th class="px-3 py-2 text-left">Tarehe</th></tr>
                    </thead>
                    <tbody>
                        @forelse($recentPurchases as $purchase)
                            <tr class="hover:bg-blue-50">
                                <td class="px-3 py-2">{{ $purchase->description }}</td>
                                <td class="px-3 py-2 hidden sm:table-cell">{{ $purchase->user_name }}</td>
                                <td class="px-3 py-2 text-xs">{{ $purchase->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-3 py-2 text-gray-500">Hakuna manunuzi</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MATUMIZI -->
        <div>
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <i class="fas fa-receipt text-red-600 text-sm"></i>
                    <h3 class="text-sm font-bold text-gray-800">Matumizi</h3>
                    <span class="text-xs text-gray-500">(5 ya hivi karibuni)</span>
                </div>
                <button @click="viewAllActivities('expenses', 'Matumizi Yote')" class="text-xs text-amber-600 hover:text-amber-800 font-semibold">
                    <i class="fas fa-eye mr-1"></i> Tazama Zote
                </button>
            </div>
            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 border-b">
                        <tr><th class="px-3 py-2 text-left">Maelezo</th><th class="px-3 py-2 text-left hidden sm:table-cell">Mtumiaji</th><th class="px-3 py-2 text-left">Tarehe</th></tr>
                    </thead>
                    <tbody>
                        @forelse($recentExpenses as $expense)
                            <tr class="hover:bg-red-50">
                                <td class="px-3 py-2">{{ $expense->description }}</td>
                                <td class="px-3 py-2 hidden sm:table-cell">{{ $expense->user_name }}</td>
                                <td class="px-3 py-2 text-xs">{{ $expense->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-3 py-2 text-gray-500">Hakuna matumizi</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MAREJESHO -->
        <div>
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <i class="fas fa-hand-holding-usd text-purple-600 text-sm"></i>
                    <h3 class="text-sm font-bold text-gray-800">Marejesho ya Deni</h3>
                    <span class="text-xs text-gray-500">(5 ya hivi karibuni)</span>
                </div>
                <button @click="viewAllActivities('repayments', 'Marejesho Yote')" class="text-xs text-amber-600 hover:text-amber-800 font-semibold">
                    <i class="fas fa-eye mr-1"></i> Tazama Zote
                </button>
            </div>
            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 border-b">
                        <tr><th class="px-3 py-2 text-left">Maelezo</th><th class="px-3 py-2 text-left hidden sm:table-cell">Mtumiaji</th><th class="px-3 py-2 text-left">Tarehe</th><tr>
                    </thead>
                    <tbody>
                        @forelse($recentRepayments as $repayment)
                            <tr class="hover:bg-purple-50">
                                <td class="px-3 py-2">{{ $repayment->description }}</td>
                                <td class="px-3 py-2 hidden sm:table-cell">{{ $repayment->user_name }}</td>
                                <td class="px-3 py-2 text-xs">{{ $repayment->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-3 py-2 text-gray-500">Hakuna marejesho</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODAL for View All - MUST BE INSIDE the main x-data="dashboardApp()" div -->
<div x-show="showModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" @click.away="showModal = false">
    <div class="bg-white rounded-xl max-w-3xl w-full max-h-[80vh] overflow-hidden shadow-xl">
        <div class="flex justify-between items-center p-4 border-b border-gray-200 bg-amber-soft">
            <h3 class="text-lg font-bold text-gray-800" id="modalTitle">Shughuli Zote</h3>
            <button @click="showModal = false" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="overflow-y-auto p-4">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-2">Maelezo</th>
                        <th class="text-left py-2 hidden sm:table-cell">Mtumiaji</th>
                        <th class="text-left py-2">Tarehe</th>
                    </tr>
                </thead>
                <tbody id="modalTableBody">
                    <tr><td colspan="3" class="text-center py-4">Inapakia...</td></tr>
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-200 text-right">
            <button @click="showModal = false" class="px-4 py-2 bg-gray-200 rounded-lg">Funga</button>
        </div>
    </div>
</div>
    </div>
</div>

<!-- MODAL for View All (inside Alpine scope to share showModal) -->
<div x-data="{ showModal: false }" x-show="showModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" @click.away="showModal = false">
    <div class="bg-white rounded-xl max-w-3xl w-full max-h-[80vh] overflow-hidden shadow-xl">
        <div class="flex justify-between items-center p-4 border-b bg-amber-soft"><h3 class="text-lg font-bold" id="modalTitle">Shughuli Zote</h3><button @click="showModal = false" class="text-gray-500"><i class="fas fa-times text-xl"></i></button></div>
        <div class="overflow-y-auto p-4"><table class="w-full text-sm"><thead><tr class="border-b"><th class="text-left py-2">Maelezo</th><th class="text-left py-2 hidden sm:table-cell">Mtumiaji</th><th class="text-left py-2">Tarehe</th></tr></thead><tbody id="modalTableBody"><tr><td colspan="3" class="text-center py-4">Inapakia...</td></tr></tbody></table></div>
        <div class="p-4 border-t text-right"><button @click="showModal = false" class="px-4 py-2 bg-gray-200 rounded-lg">Funga</button></div>
    </div>
</div>

<!-- Loading Spinner -->
<div id="loading-spinner" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white p-4 rounded-lg shadow-xl flex items-center space-x-3"><svg class="animate-spin h-5 w-5 text-amber-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span>Inapakia...</span></div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

<script>
// Global currency formatter
function formatCurrency(value) {
    return new Intl.NumberFormat('sw-TZ', { style: 'currency', currency: 'TZS', minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(value || 0);
}

// Color helpers
function generateDynamicColors(count) {
    let colors = [];
    for (let i = 0; i < count; i++) {
        let hue = (i * 0.618033988749895 * 360) % 360;
        colors.push(`hsla(${hue}, 70%, 55%, 0.85)`);
    }
    return colors;
}
function generateBorderColors(baseColors) {
    return baseColors.map(c => c.replace('0.85', '1').replace('hsla', 'hsl'));
}

let chartInstances = {};

document.addEventListener('alpine:init', () => {
    Alpine.data('dashboardApp', () => ({
        tab: 'graphs',
        graph: 'faidaBidhaa',
        showModal: false,
        graphTabs: { faidaBidhaa:'Faida kwa Bidhaa', faidaSiku:'Faida kwa Siku', mauzoSiku:'Mauzo kwa Siku', marejesho:'Faida ya Marejesho', mauzo:'Mauzo Jumla' },
        faidaByBidhaa: @json($faidaBidhaa ?? []),
        faidaBySiku: @json($faidaSiku ?? []),
        mauzoBySiku: @json($mauzoSiku ?? []),
        marejeshoByBidhaa: @json($marejesho ?? []),
        mauzoByBidhaa: @json($mauzo ?? []),

        getActiveClass(key) {
            let colors = { faidaBidhaa:'bg-amber-600 text-white', faidaSiku:'bg-emerald-600 text-white', mauzoSiku:'bg-blue-600 text-white', marejesho:'bg-purple-600 text-white', mauzo:'bg-pink-600 text-white' };
            return colors[key] || 'bg-amber-600 text-white';
        },
        setTab(tabName) { this.tab = tabName; if(tabName === 'graphs') setTimeout(() => this.drawChart(this.graph), 80); },
        setGraph(name) { this.graph = name; setTimeout(() => this.drawChart(name), 80); },
        drawChart(name) {
            let canvas = document.getElementById(name+'Chart');
            if(!canvas) return;
            if(chartInstances[name]) { chartInstances[name].destroy(); chartInstances[name]=null; }
            let ctx = canvas.getContext('2d');
            let config = {};
            switch(name) {
                case 'faidaBidhaa': config = this.barChartWithDynamicColors(this.faidaByBidhaa, 'jina', 'faida', 'Faida kwa Bidhaa'); break;
                case 'faidaSiku': config = this.lineChart(this.faidaBySiku, 'day', 'faida', '#10b981', 'Faida kwa Siku'); break;
                case 'mauzoSiku': config = this.lineChart(this.mauzoBySiku, 'day', 'total', '#3b82f6', 'Mauzo kwa Siku'); break;
                case 'marejesho': config = this.barChartWithDynamicColors(this.marejeshoByBidhaa, 'jina', 'total', 'Faida ya Marejesho'); break;
                case 'mauzo': config = this.barChartWithDynamicColors(this.mauzoByBidhaa, 'jina', 'total', 'Mauzo Jumla'); break;
            }
            try { chartInstances[name] = new Chart(ctx, config); } catch(e) { console.error(e); }
        },
        barChartWithDynamicColors(data, labelKey, valueKey, label) {
            let labels = data.map(i=>i[labelKey]||'N/A'), values = data.map(i=>Math.max(0,Number(i[valueKey])||0));
            let colors = generateDynamicColors(labels.length), borders = generateBorderColors(colors);
            return { type:'bar', data:{ labels, datasets:[{ label, data:values, backgroundColor:colors, borderColor:borders, borderWidth:2, borderRadius:8 }] }, options:this.chartOptions() };
        },
        lineChart(data, labelKey, valueKey, color, label) {
            let labels = data.map(i=>i[labelKey]||'N/A'), values = data.map(i=>Math.max(0,Number(i[valueKey])||0));
            return { type:'line', data:{ labels, datasets:[{ label, data:values, borderColor:color, backgroundColor:color+'20', borderWidth:3, tension:0.3, fill:true, pointBackgroundColor:color, pointBorderColor:'#fff', pointRadius:5 }] }, options:this.chartOptions() };
        },
        chartOptions() {
            return { responsive:true, maintainAspectRatio:true, plugins:{ legend:{ display:true, position:'top', labels:{ color:'#1f2937', font:{ size:window.innerWidth<768?11:12, weight:'bold' }, usePointStyle:true, pointStyle:'rectRounded' } }, tooltip:{ enabled:true, backgroundColor:'rgba(0,0,0,0.85)', titleColor:'#fbbf24', bodyColor:'#fff', callbacks:{ label:(ctx)=>`${ctx.dataset.label||''}: ${formatCurrency(ctx.parsed.y)}` } } }, scales:{ y:{ beginAtZero:true, grid:{ color:'rgba(0,0,0,0.05)' }, ticks:{ color:'#374151', callback:(v)=>v>=1e6?'Tsh '+(v/1e6).toFixed(1)+'M':(v>=1000?'Tsh '+(v/1000).toFixed(0)+'K':'Tsh '+v) } }, x:{ grid:{ display:false }, ticks:{ maxRotation:45, minRotation:35, autoSkip:true, maxTicksLimit:8 } } }, animation:{ duration:750, easing:'easeInOutQuart' } };
        },
        handleResize() { if(this.tab==='graphs' && chartInstances[this.graph]) setTimeout(()=>chartInstances[this.graph]?.resize(),100); },
        init() { this.tab='graphs'; setTimeout(()=>{ if(this.tab==='graphs') this.drawChart(this.graph); },150); window.addEventListener('resize',()=>this.handleResize()); },
        async viewAllActivities(type, title) {
            this.showModal = true;
            document.getElementById('modalTitle').innerText = title;
            let tbody = document.getElementById('modalTableBody');
            tbody.innerHTML = '<tr><td colspan="3" class="text-center py-4">Inapakia...</td></tr>';
            try {
                let resp = await fetch(`/uchambuzi/all-activities?type=${type}`);
                let data = await resp.json();
                if(!data.length) { tbody.innerHTML = '<tr><td colspan="3" class="text-center py-4">Hakuna data</td></tr>'; return; }
                let html = '';
                data.forEach(item => {
                    html += `<tr class="border-b hover:bg-amber-50"><td class="py-2">${item.description}</td><td class="py-2 hidden sm:table-cell">${item.user_name}</td><td class="py-2 text-xs">${new Date(item.created_at).toLocaleString('sw-TZ')}</td></tr>`;
                });
                tbody.innerHTML = html;
            } catch(e) { tbody.innerHTML = `<tr><td colspan="3" class="text-center py-4 text-red-500">Hitilafu: ${e.message}</td></tr>`; }
        }
    }));

    Alpine.data('mwenendoApp', () => ({
        viewType: 'Siku', manualDateSelect: false, dateFrom: '', dateTo: '', summary: null, customSummary: null, mwenendoSummary: @json($mwenendoSummary ?: []),
        toggleDatePicker(val) { this.manualDateSelect = val; if(!val) { this.customSummary = null; this.updateSummary(); } },
        formatCurrency,
        updateSummary() { let key = this.viewType.toLowerCase(); this.summary = this.mwenendoSummary[key] || null; },
        async fetchCustomSummary() {
            if(!this.dateFrom || !this.dateTo) { this.showNotification('Chagua tarehe zote','error'); return; }
            let fromDate=new Date(this.dateFrom), toDate=new Date(this.dateTo), today=new Date();
            if(fromDate>today || toDate>today || fromDate>toDate) { this.showNotification('Tarehe si sahihi','error'); return; }
            try {
                document.getElementById('loading-spinner').classList.remove('hidden');
                this.customSummary = null;
                let resp = await fetch(`/uchambuzi/mwenendo?from=${this.dateFrom}&to=${this.dateTo}`);
                if(!resp.ok) throw new Error();
                this.customSummary = await resp.json();
                this.showNotification('Data imepakuliwa','success');
            } catch(e) { this.showNotification('Hitilafu','error'); }
            finally { document.getElementById('loading-spinner').classList.add('hidden'); }
        },
        refresh() { this.updateSummary(); this.customSummary=null; this.manualDateSelect=false; this.setDefaultDates(); this.showNotification('Imeburushwa','info'); },
        setDefaultDates() { let today=new Date(), weekAgo=new Date(); weekAgo.setDate(today.getDate()-7); this.dateFrom=weekAgo.toISOString().split('T')[0]; this.dateTo=today.toISOString().split('T')[0]; },
        initMwenendo() { this.setDefaultDates(); this.updateSummary(); },
        showNotification(msg,type) { let container=document.getElementById('notification-container'); if(!container) return; let c={success:'bg-emerald-50 border-emerald-300',error:'bg-red-50 border-red-300',info:'bg-blue-50 border-blue-300'}; let n=document.createElement('div'); n.className=`rounded-lg border-l-4 px-4 py-2 text-xs mb-2 animate-fade-in ${c[type]||c.info}`; n.innerHTML=`<i class="fas ${type==='success'?'fa-check-circle':type==='error'?'fa-exclamation-circle':'fa-info-circle'} mr-2"></i>${msg}`; container.appendChild(n); setTimeout(()=>{n.style.opacity='0'; n.style.transform='translateY(-10px)'; setTimeout(()=>n.remove(),300); },3000); }
    }));

    Alpine.data('kampuniData', () => ({ beforeSales: {{ $thamaniBefore ?? 0 }}, afterSales: {{ $thamaniAfter ?? 0 }}, faida: {{ $faida ?? 0 }}, formatCurrency }));
});

document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        let alpineData = document.querySelector('[x-data="dashboardApp()"]')?.__x;
        if(alpineData && alpineData.$data.tab === 'graphs') alpineData.$data.drawChart(alpineData.$data.graph);
    }, 200);
});
</script>
@endpush