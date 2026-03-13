@extends('layouts.app')

@section('title', 'Uchambuzi wa Biashara')
@section('page-title', 'Dashibodi ya Taarifa')
@section('page-subtitle', 'Uchambuzi wa Biashara kwa Grafu na Takwimu')

@section('content')
<div class="space-y-4" x-data="dashboardApp()" x-init="init()">
    <!-- Notifications -->
    <div id="notification-container" class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-sm px-4 pointer-events-none"></div>

    <!-- 🔹 Header - Mobile Optimized -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm">
        <div class="p-4 border-b border-gray-200 bg-emerald-50">
            <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-chart-bar text-amber-600 mr-2"></i>
                TAARIFA KATIKA MAUZO
            </h2>
            <p class="text-sm text-gray-600 mt-1">Chagua aina ya taarifa kuona uchambuzi kamili</p>
        </div>
        
        <div class="p-4">
            <div class="flex flex-wrap gap-2">
                <!-- Graphs Button - Default Active -->
                <button @click="setTab('graphs')" 
                        :class="tab === 'graphs' ? 'bg-amber-600 text-white shadow-md' : 'bg-gray-100 text-gray-800 hover:bg-gray-200'" 
                        class="px-4 py-2 rounded-lg font-medium transition-all duration-200 text-sm border border-transparent focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <i class="fas fa-chart-bar mr-1"></i> Grafu
                </button>

                <!-- Mwenendo Button -->
                <button @click="setTab('mwenendo')" 
                        :class="tab === 'mwenendo' ? 'bg-amber-600 text-white shadow-md' : 'bg-gray-100 text-gray-800 hover:bg-gray-200'" 
                        class="px-4 py-2 rounded-lg font-medium transition-all duration-200 text-sm border border-transparent focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <i class="fas fa-chart-line mr-1"></i> Mwenendo
                </button>

                <!-- Kampuni Button -->
                <button @click="setTab('kampuni')" 
                        :class="tab === 'kampuni' ? 'bg-amber-600 text-white shadow-md' : 'bg-gray-100 text-gray-800 hover:bg-gray-200'" 
                        class="px-4 py-2 rounded-lg font-medium transition-all duration-200 text-sm border border-transparent focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <i class="fas fa-building mr-1"></i> Kampuni
                </button>

                <!-- Ripoti Button -->
                <a href="{{ route('user.reports.select') }}" 
                   class="px-4 py-2 rounded-lg font-medium transition-all duration-200 bg-gray-100 text-gray-800 hover:bg-gray-200 text-sm border border-transparent focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <i class="fas fa-file-alt mr-1"></i> Ripoti
                </a>

                <!-- Historia Button -->
                <button @click="setTab('historia')" 
                        :class="tab === 'historia' ? 'bg-amber-600 text-white shadow-md' : 'bg-gray-100 text-gray-800 hover:bg-gray-200'" 
                        class="px-4 py-2 rounded-lg font-medium transition-all duration-200 text-sm border border-transparent focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <i class="fas fa-history mr-1"></i> Historia
                </button>
            </div>
        </div>
    </div>

    <!-- 🔹 MAIN CONTENT -->
    <div class="space-y-4">
        <!-- ✅ GRAPHS TAB (Default) -->
        <div x-show="tab === 'graphs'" 
             class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm"
             x-cloak 
             x-transition:enter.duration.300ms>
            
            <div class="p-4 border-b border-gray-200 bg-amber-50">
                <h2 class="text-sm font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-chart-pie text-amber-600 mr-2"></i>
                    Chagua Aina ya Grafu
                </h2>
                <p class="text-xs text-gray-600 mt-1">Bonyeza kwenye kitufe kuona uchambuzi tofauti</p>
            </div>
            
            <div class="p-4">
                <!-- Graph tabs with different colors -->
                <div class="flex flex-wrap gap-2 mb-4 border-b border-gray-200 pb-3 overflow-x-auto">
                    <template x-for="(item, key) in graphTabs" :key="key">
                        <button @click="setGraph(key)"
                            :class="graph === key ? getActiveClass(key) : 'bg-gray-100 text-gray-800 hover:bg-gray-200'"
                            class="px-4 py-2 rounded-lg transition-all duration-200 text-xs md:text-sm whitespace-nowrap border border-transparent focus:outline-none focus:ring-2 focus:ring-amber-500"
                            x-text="item">
                        </button>
                    </template>
                </div>

                <!-- Chart containers with guaranteed rendering -->
                <div class="h-64 md:h-96 relative">
                    <div x-show="graph === 'faidaBidhaa'" x-cloak>
                        <canvas id="faidaBidhaaChart" class="w-full h-full"></canvas>
                    </div>
                    <div x-show="graph === 'faidaSiku'" x-cloak>
                        <canvas id="faidaSikuChart" class="w-full h-full"></canvas>
                    </div>
                    <div x-show="graph === 'mauzoSiku'" x-cloak>
                        <canvas id="mauzoSikuChart" class="w-full h-full"></canvas>
                    </div>
                    <div x-show="graph === 'marejesho'" x-cloak>
                        <canvas id="marejeshoChart" class="w-full h-full"></canvas>
                    </div>
                    <div x-show="graph === 'mauzo'" x-cloak>
                        <canvas id="mauzoChart" class="w-full h-full"></canvas>
                    </div>
                </div>
            </div>
        </div>
<!-- ✅ MWENENDO TAB -->
<div x-show="tab === 'mwenendo'" 
     class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm" 
     x-cloak
     x-data="mwenendoApp()"
     x-init="initMwenendo()"
     x-transition:enter.duration.300ms>

    <div class="p-4 border-b border-gray-200 bg-amber-50">
        <h2 class="text-sm font-semibold text-gray-800 flex items-center">
            <i class="fas fa-chart-line text-amber-600 mr-2"></i>
            Mwenendo wa Biashara
        </h2>
        <p class="text-xs text-gray-600 mt-1">Chagua kipindi au tarehe kuona takwimu</p>
    </div>
    
    <div class="p-4">
        <!-- Header with refresh button -->
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-3 mb-4">
            <div class="flex items-center gap-2">
                <button @click="refresh()" 
                        class="flex items-center gap-2 bg-amber-600 text-white px-3 py-2 rounded-lg hover:bg-amber-700 transition-all duration-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <i class="fas fa-sync-alt text-xs"></i>
                    <span class="text-xs">Pakua Upya</span>
                </button>
            </div>
        </div>

        <!-- Selection Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">⏳ Chagua Muda:</label>
                <select x-model="viewType" 
                        @change="toggleDatePicker(false)" 
                        class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500">
                    <option value="Siku">Siku</option>
                    <option value="Wiki">Wiki</option>
                    <option value="Mwezi">Mwezi</option>
                    <option value="Mwaka">Mwaka</option>
                </select>
            </div>

            <div class="flex items-end">
                <button @click="toggleDatePicker(true)" 
                        class="w-full bg-gradient-to-r from-amber-500 to-amber-400 text-white px-4 py-2 rounded-lg hover:from-amber-600 hover:to-amber-500 shadow-sm transition-all duration-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <i class="fas fa-calendar-alt mr-1"></i> Chagua Tarehe
                </button>
            </div>
        </div>

        <!-- Auto Summary -->
        <div x-show="!manualDateSelect" 
             class="bg-gray-50 border border-gray-200 rounded p-3 space-y-3 transition-all duration-300">

            <h3 class="font-semibold text-gray-800 text-sm">
                Muhtasari wa <span x-text="viewType"></span>
                <span x-show="summary?.date" class="text-xs text-gray-600 font-normal">
                    (<span x-text="summary?.date"></span>)
                </span>
            </h3>
            
            <template x-if="summary">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div class="bg-white border border-gray-200 rounded p-2 shadow-sm">
                        <p class="text-xs text-gray-500 mb-1">Mapato ya Mauzo</p>
                        <p class="text-sm font-bold text-gray-900" x-text="formatCurrency(summary.mapato_mauzo)"></p>
                    </div>
                    
                    <div class="bg-white border border-gray-200 rounded p-2 shadow-sm">
                        <p class="text-xs text-gray-500 mb-1">Mapato ya Madeni</p>
                        <p class="text-sm font-bold text-gray-900" x-text="formatCurrency(summary.mapato_madeni)"></p>
                    </div>
                    
                    <div class="bg-white border border-gray-200 rounded p-2 shadow-sm">
                        <p class="text-xs text-gray-500 mb-1">Jumla ya Mapato</p>
                        <p class="text-sm font-bold text-gray-900" x-text="formatCurrency(summary.jumla_mapato)"></p>
                    </div>
                    
                    <div class="bg-white border border-gray-200 rounded p-2 shadow-sm">
                        <p class="text-xs text-gray-500 mb-1">Jumla ya Matumizi</p>
                        <p class="text-sm font-bold text-gray-900" x-text="formatCurrency(summary.jumla_mat)"></p>
                    </div>
                    
                    <div class="bg-white border border-gray-200 rounded p-2 shadow-sm">
                        <p class="text-xs text-gray-500 mb-1">Gharama ya Bidhaa</p>
                        <p class="text-sm font-bold text-gray-900" x-text="formatCurrency(summary.gharama_bidhaa)"></p>
                    </div>
                    
                    <div class="bg-white border border-gray-200 rounded p-2 shadow-sm">
                        <p class="text-xs text-gray-500 mb-1">Faida ya Mauzo</p>
                        <p class="text-sm font-bold text-gray-900" x-text="formatCurrency(summary.faida_mauzo)"></p>
                    </div>
                    
                    <div class="bg-white border border-gray-200 rounded p-2 shadow-sm">
                        <p class="text-xs text-gray-500 mb-1">Faida ya Marejesho</p>
                        <p class="text-sm font-bold text-gray-900" x-text="formatCurrency(summary.faida_marejesho)"></p>
                    </div>
                    
                    <div class="bg-white border border-gray-200 rounded p-2 shadow-sm">
                        <p class="text-xs text-gray-500 mb-1">Fedha Dukani</p>
                        <p class="text-sm font-bold text-gray-900" x-text="formatCurrency(summary.fedha_droo)"></p>
                    </div>
                    
                    <div class="bg-white border border-gray-200 rounded p-2 shadow-sm lg:col-span-2">
                        <p class="text-xs text-gray-500 mb-1">Faida Halisi</p>
                        <p class="text-sm font-bold text-emerald-600" x-text="formatCurrency(summary.faida_halisi)"></p>
                    </div>
                </div>
            </template>

            <template x-if="!summary">
                <div class="text-center py-4 text-gray-500 text-xs">
                    Hakuna data inayopatikana kwa kipindi hiki.
                </div>
            </template>
        </div>

        <!-- Manual Date Selection -->
        <div x-show="manualDateSelect" 
             class="border border-gray-200 rounded p-3 bg-gray-50 space-y-3 transition-all duration-300">

            <h3 class="font-semibold text-gray-800 text-sm">📅 Angalia Mwenendo kwa Tarehe</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Kuanzia Tarehe:</label>
                    <input type="date" x-model="dateFrom" 
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-amber-500">
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Mpaka Tarehe:</label>
                    <input type="date" x-model="dateTo" 
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-amber-500">
                </div>
            </div>

            <div class="flex gap-2">
                <button @click="fetchCustomSummary()" 
                        class="bg-amber-600 text-white px-4 py-2 rounded-lg hover:bg-amber-700 shadow-sm transition-all duration-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <i class="fas fa-search mr-1"></i> Angalia
                </button>
                <button @click="toggleDatePicker(false)" 
                        class="text-gray-800 bg-gray-200 px-4 py-2 rounded-lg hover:bg-gray-300 transition-all duration-200 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                    <i class="fas fa-arrow-left mr-1"></i> Rudi
                </button>
            </div>

            <template x-if="customSummary">
                <div class="mt-3 bg-white border border-gray-200 rounded p-3 shadow-sm">
                    <h4 class="text-amber-600 font-semibold mb-2 text-xs">
                        Matokeo ya kipindi: <span x-text="dateFrom + ' mpaka ' + dateTo"></span>
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-2">
                        <div class="border border-gray-200 rounded p-2">
                            <p class="text-xs text-gray-500">Mapato ya Mauzo</p>
                            <p class="text-sm font-bold text-gray-900" x-text="formatCurrency(customSummary.mapato_mauzo)"></p>
                        </div>
                        <div class="border border-gray-200 rounded p-2">
                            <p class="text-xs text-gray-500">Mapato ya Madeni</p>
                            <p class="text-sm font-bold text-gray-900" x-text="formatCurrency(customSummary.mapato_madeni)"></p>
                        </div>
                        <div class="border border-gray-200 rounded p-2">
                            <p class="text-xs text-gray-500">Jumla ya Mapato</p>
                            <p class="text-sm font-bold text-gray-900" x-text="formatCurrency(customSummary.jumla_mapato)"></p>
                        </div>
                        <div class="border border-gray-200 rounded p-2">
                            <p class="text-xs text-gray-500">Jumla ya Matumizi</p>
                            <p class="text-sm font-bold text-gray-900" x-text="formatCurrency(customSummary.jumla_mat)"></p>
                        </div>
                        <div class="border border-gray-200 rounded p-2">
                            <p class="text-xs text-gray-500">Gharama ya Bidhaa</p>
                            <p class="text-sm font-bold text-gray-900" x-text="formatCurrency(customSummary.gharama_bidhaa)"></p>
                        </div>
                        <div class="border border-gray-200 rounded p-2">
                            <p class="text-xs text-gray-500">Faida ya Mauzo</p>
                            <p class="text-sm font-bold text-gray-900" x-text="formatCurrency(customSummary.faida_mauzo)"></p>
                        </div>
                        <div class="border border-gray-200 rounded p-2">
                            <p class="text-xs text-gray-500">Faida ya Marejesho</p>
                            <p class="text-sm font-bold text-gray-900" x-text="formatCurrency(customSummary.faida_marejesho)"></p>
                        </div>
                        <div class="border border-gray-200 rounded p-2">
                            <p class="text-xs text-gray-500">Fedha Dukani</p>
                            <p class="text-sm font-bold text-gray-900" x-text="formatCurrency(customSummary.fedha_droo)"></p>
                        </div>
                        <div class="border border-gray-200 rounded p-2 lg:col-span-2">
                            <p class="text-xs text-gray-500">Faida Halisi</p>
                            <p class="text-sm font-bold text-emerald-600" x-text="formatCurrency(customSummary.faida_halisi)"></p>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
    function mwenendoApp() {
        return {
            viewType: 'Siku',
            summary: null,
            customSummary: null,
            manualDateSelect: false,
            dateFrom: '',
            dateTo: '',
            
            initMwenendo() {
                // Load initial data
                this.fetchSummary();
            },
            
            async fetchSummary() {
                try {
                    // Determine date range based on viewType
                    let from, to;
                    const today = new Date();
                    
                    switch(this.viewType) {
                        case 'Siku':
                            from = this.formatDate(today);
                            to = this.formatDate(today);
                            break;
                        case 'Wiki':
                            const weekStart = new Date(today);
                            weekStart.setDate(today.getDate() - today.getDay() + 1); // Monday
                            const weekEnd = new Date(weekStart);
                            weekEnd.setDate(weekStart.getDate() + 6); // Sunday
                            from = this.formatDate(weekStart);
                            to = this.formatDate(weekEnd);
                            break;
                        case 'Mwezi':
                            const monthStart = new Date(today.getFullYear(), today.getMonth(), 1);
                            const monthEnd = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                            from = this.formatDate(monthStart);
                            to = this.formatDate(monthEnd);
                            break;
                        case 'Mwaka':
                            const yearStart = new Date(today.getFullYear(), 0, 1);
                            const yearEnd = new Date(today.getFullYear(), 11, 31);
                            from = this.formatDate(yearStart);
                            to = this.formatDate(yearEnd);
                            break;
                    }
                    
                    const response = await fetch(`/uchambuzi/mwenendo?from=${from}&to=${to}`);
                    const data = await response.json();
                    
                    this.summary = {
                        ...data,
                        date: this.getDisplayDate(from, to)
                    };
                } catch (error) {
                    console.error('Error fetching summary:', error);
                }
            },
            
            async fetchCustomSummary() {
                if (!this.dateFrom || !this.dateTo) {
                    alert('Tafadhali chagua tarehe zote mbili');
                    return;
                }
                
                try {
                    const response = await fetch(`/uchambuzi/mwenendo?from=${this.dateFrom}&to=${this.dateTo}`);
                    const data = await response.json();
                    this.customSummary = data;
                } catch (error) {
                    console.error('Error fetching custom summary:', error);
                }
            },
            
            refresh() {
                this.summary = null;
                this.customSummary = null;
                this.fetchSummary();
            },
            
            toggleDatePicker(show) {
                this.manualDateSelect = show;
                if (!show) {
                    this.customSummary = null;
                    this.fetchSummary();
                }
            },
            
            formatDate(date) {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            },
            
            getDisplayDate(from, to) {
                if (from === to) {
                    return new Date(from).toLocaleDateString('sw-TZ');
                }
                return `${new Date(from).toLocaleDateString('sw-TZ')} - ${new Date(to).toLocaleDateString('sw-TZ')}`;
            },
            
            formatCurrency(value) {
                return new Intl.NumberFormat('sw-TZ', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }).format(value || 0) + ' TZS';
            }
        };
    }
</script>

        <!-- ✅ KAMPUNI TAB -->
        <div x-show="tab === 'kampuni'" 
             x-data="kampuniData()"
             class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm"
             x-cloak
             x-transition:enter.duration.300ms>
            
            <div class="p-4 border-b border-gray-200 bg-amber-50">
                <h2 class="text-sm font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-building text-amber-600 mr-2"></i>
                    Thamani ya Kampuni
                </h2>
                <p class="text-xs text-gray-600 mt-1">Muhtasari wa thamani ya kampuni na faida</p>
            </div>
            
            <div class="p-4">
                <!-- Summary Boxes -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
                    <div class="bg-white border border-gray-200 rounded p-3 shadow-sm">
                        <p class="text-xs text-gray-500 mb-1">Thamani Kabla (Bei ya Nunua)</p>
                        <p class="text-base font-bold text-gray-900" x-text="formatCurrency(beforeSales)"></p>
                        <p class="text-xs text-gray-400 mt-1">Bei ya ununuzi ya bidhaa zilizopo</p>
                    </div>

                    <div class="bg-white border border-gray-200 rounded p-3 shadow-sm">
                        <p class="text-xs text-gray-500 mb-1">Thamani Baada (Bei ya Kuuza)</p>
                        <p class="text-base font-bold text-gray-900" x-text="formatCurrency(afterSales)"></p>
                        <p class="text-xs text-gray-400 mt-1">Bei ya kuuzia ya bidhaa zilizopo</p>
                    </div>

                    <div class="bg-white border border-gray-200 rounded p-3 shadow-sm">
                        <p class="text-xs text-gray-500 mb-1">Faida ya Uwezo</p>
                        <p class="text-base font-bold text-amber-600" x-text="formatCurrency(faida)"></p>
                        <p class="text-xs text-gray-400 mt-1">Faida inayoweza kupatikana</p>
                    </div>
                </div>

                <!-- Company Total Value -->
                <div class="bg-gray-50 border border-gray-200 rounded p-3 mb-4">
                    <p class="text-xs text-gray-600 mb-1">Thamani ya Jumla ya Kampuni</p>
                    <p class="text-lg font-bold text-amber-600">{{ $thamaniKampuniFormatted }}</p>
                    <p class="text-xs text-gray-400 mt-1">Thamani ya sasa ya hisa zote (kwa bei ya kuuzia)</p>
                </div>

                <!-- Table -->
                <div>
                    <h3 class="font-semibold text-gray-800 mb-2 text-sm">Orodha ya Bidhaa</h3>
                    <div class="overflow-x-auto border border-gray-200 rounded">
                        <table class="w-full text-xs">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-3 py-2 text-left font-medium text-gray-700">Bidhaa</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-700 hidden sm:table-cell">Aina</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-700 hidden md:table-cell">Kipimo</th>
                                    <th class="px-3 py-2 text-right font-medium text-gray-700">Idadi</th>
                                    <th class="px-3 py-2 text-right font-medium text-gray-700 hidden lg:table-cell">Nunua</th>
                                    <th class="px-3 py-2 text-right font-medium text-gray-700">Kuuza</th>
                                    <th class="px-3 py-2 text-right font-medium text-gray-700">Faida</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($bidhaaList as $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2 text-gray-900 truncate max-w-[120px]">{{ $item->jina }}</td>
                                        <td class="px-3 py-2 text-gray-700 hidden sm:table-cell truncate max-w-[80px]">{{ $item->aina }}</td>
                                        <td class="px-3 py-2 text-gray-700 hidden md:table-cell truncate max-w-[60px]">{{ $item->kipimo ?? '-' }}</td>
                                        <td class="px-3 py-2 text-right text-gray-900">{{ number_format($item->idadi) }}</td>
                                        <td class="px-3 py-2 text-right text-gray-700 hidden lg:table-cell">Tsh {{ number_format($item->bei_nunua, 0) }}</td>
                                        <td class="px-3 py-2 text-right text-gray-700">Tsh {{ number_format($item->bei_kuuza, 0) }}</td>
                                        <td class="px-3 py-2 text-right font-semibold text-amber-600">
                                            Tsh {{ number_format($item->faida, 0) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- ✅ HISTORIA TAB -->
        <div x-show="tab === 'historia'" 
             class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm" 
             x-cloak
             x-transition:enter.duration.300ms>
            
            <div class="p-4 border-b border-gray-200 bg-amber-50">
                <h2 class="text-sm font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-history text-amber-600 mr-2"></i>
                    Historia ya Mfumo
                </h2>
                <p class="text-xs text-gray-600 mt-1">Taarifa kuhusu historia ya mfumo na takwimu</p>
            </div>
            
            <div class="p-4">
                <div class="p-6 text-center border-2 border-dashed border-gray-300 rounded">
                    <p class="text-gray-500 text-sm">Inafanya kazi - Ukurasa wa historia utaanza hivi karibuni</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Spinner (Hidden initially) -->
<div id="loading-spinner" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white p-4 rounded-lg shadow-xl flex items-center space-x-3">
        <svg class="animate-spin h-5 w-5 text-amber-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="text-gray-700 text-sm">Inapakia...</span>
    </div>
</div>
@endsection

@push('styles')
<style>
    [x-cloak] { display: none !important; }
    
    .animate-fade-in {
        animation: fadeIn 0.3s ease-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    /* Chart container */
    canvas {
        max-height: 100%;
        width: 100% !important;
        height: auto !important;
    }
    
    /* Mobile responsive */
    @media (max-width: 640px) {
        .table-responsive {
            display: block;
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
    }
</style>
@endpush

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

// Graph colors - different for each chart type
const graphColors = {
    faidaBidhaa: {
        bg: 'rgba(245, 158, 11, 0.7)',  // amber
        border: '#f59e0b'
    },
    faidaSiku: {
        bg: 'rgba(16, 185, 129, 0.7)',   // green
        border: '#10b981'
    },
    mauzoSiku: {
        bg: 'rgba(59, 130, 246, 0.7)',   // blue
        border: '#3b82f6'
    },
    marejesho: {
        bg: 'rgba(139, 92, 246, 0.7)',   // purple
        border: '#8b5cf6'
    },
    mauzo: {
        bg: 'rgba(236, 72, 153, 0.7)',   // pink
        border: '#ec4899'
    }
};

document.addEventListener('alpine:init', () => {
    Alpine.data('dashboardApp', () => ({
        // Default to 'graphs' tab
        tab: 'graphs',
        graph: 'faidaBidhaa',
        
        graphTabs: {
            faidaBidhaa: 'Faida kwa Bidhaa',
            faidaSiku: 'Faida kwa Siku',
            mauzoSiku: 'Mauzo kwa Siku',
            marejesho: 'Faida ya Marejesho',
            mauzo: 'Mauzo Jumla'
        },

        // Data from Laravel
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
                }, 50);
            }
        },

        setGraph(name) {
            this.graph = name;
            setTimeout(() => {
                this.drawChart(name);
            }, 50);
        },

        drawChart(name) {
            const canvasId = name + 'Chart';
            const canvas = document.getElementById(canvasId);
            
            if(!canvas) {
                console.log('Canvas not found:', canvasId);
                return;
            }

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
                        borderWidth: 1,
                        borderRadius: 4
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
                        backgroundColor: colors.bg.replace('0.7', '0.1'),
                        borderWidth: 3,
                        tension: 0.3,
                        fill: true,
                        pointBackgroundColor: colors.border,
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4
                    }]
                },
                options: this.chartOptions()
            };
        },

        chartOptions() {
            return {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { 
                        display: true,
                        position: 'top',
                        labels: {
                            color: '#1f2937',
                            font: {
                                size: window.innerWidth < 768 ? 10 : 11,
                                weight: '500'
                            },
                            padding: 15
                        }
                    }, 
                    tooltip: { 
                        enabled: true,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: 'white',
                        bodyColor: 'white',
                        titleFont: {
                            size: window.innerWidth < 768 ? 10 : 11
                        },
                        bodyFont: {
                            size: window.innerWidth < 768 ? 10 : 11
                        },
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += formatCurrency(context.parsed.y);
                                return label;
                            }
                        }
                    } 
                },
                scales: { 
                    y: { 
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            color: '#4b5563',
                            font: {
                                size: window.innerWidth < 768 ? 9 : 10
                            },
                            callback: function(value) {
                                if (value >= 1000000) {
                                    return 'Tsh ' + (value / 1000000).toFixed(1) + 'M';
                                } else if (value >= 1000) {
                                    return 'Tsh ' + (value / 1000).toFixed(0) + 'K';
                                }
                                return 'Tsh ' + value;
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#4b5563',
                            font: {
                                size: window.innerWidth < 768 ? 9 : 10
                            },
                            maxRotation: 45,
                            minRotation: 45
                        }
                    }
                }
            };
        },

        handleResize() {
            if (this.tab === 'graphs' && chartInstances[this.graph]) {
                setTimeout(() => {
                    if (chartInstances[this.graph]) {
                        chartInstances[this.graph].resize();
                    }
                }, 100);
            }
        },

        init() {
            // Force 'graphs' as default tab
            this.tab = 'graphs';
            
            // Draw the saved chart on init
            setTimeout(() => {
                if (this.tab === 'graphs') {
                    this.drawChart(this.graph);
                }
            }, 100);
            
            // Redraw charts on window resize
            window.addEventListener('resize', () => {
                this.handleResize();
            });
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
            
            if (fromDate > today) {
                this.showNotification('Tarehe ya kuanzia haiwezi kuwa baada ya leo', 'error');
                return;
            }
            
            if (toDate > today) {
                this.showNotification('Tarehe ya mwisho haiwezi kuwa baada ya leo', 'error');
                return;
            }
            
            if (fromDate > toDate) {
                this.showNotification('Tarehe ya kuanzia haiwezi kuwa baada ya tarehe ya mwisho', 'error');
                return;
            }
            
            try {
                document.getElementById('loading-spinner').classList.remove('hidden');
                this.customSummary = null;
                
                const response = await fetch(`/uchambuzi/mwenendo?from=${this.dateFrom}&to=${this.dateTo}`);
                
                if (!response.ok) {
                    throw new Error(`Server returned ${response.status}`);
                }
                
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
                success: 'bg-emerald-50 border-emerald-200 text-emerald-800',
                error: 'bg-red-50 border-red-200 text-red-800',
                warning: 'bg-amber-50 border-amber-200 text-amber-800',
                info: 'bg-blue-50 border-blue-200 text-blue-800'
            };

            const notification = document.createElement('div');
            notification.className = `rounded border px-3 py-2 text-xs font-medium mb-2 ${colors[type]} shadow-sm animate-fade-in`;
            notification.textContent = message;

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

// Ensure charts are drawn when page loads
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