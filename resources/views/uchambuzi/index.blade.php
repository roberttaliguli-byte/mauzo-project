@extends('layouts.app')

@section('title', 'Uchambuzi wa Biashara')
@section('page-title', 'Dashibodi ya Taarifa')
@section('page-subtitle', 'Uchambuzi wa Biashara kwa Grafu na Takwimu')

@section('content')
<div class="bg-gray-50 min-h-screen" x-data="dashboardApp()" x-init="init()">
<!-- 🔹 Header - Mobile Optimized -->
<header class="bg-white shadow-sm border-b px-3 md:px-6 py-3 md:py-4">
  <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-3 md:gap-4">
    <h1 class="text-xl md:text-2xl font-bold text-gray-800">TAARIFA KATIKA MAUZO</h1>
    <div class="flex flex-wrap gap-2 md:gap-2">
      <button @click="setTab('graphs')" 
              :class="tab === 'graphs' ? 'bg-green-600 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'" 
              class="px-3 md:px-4 py-1.5 md:py-2 rounded-lg font-medium transition-all duration-200 text-sm md:text-base">
        📊 Grafu
      </button>

      <button @click="setTab('mwenendo')" 
              :class="tab === 'mwenendo' ? 'bg-green-600 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'" 
              class="px-3 md:px-4 py-1.5 md:py-2 rounded-lg font-medium transition-all duration-200 text-sm md:text-base">
        📈 Mwenendo
      </button>

      <button @click="setTab('kampuni')" 
              :class="tab === 'kampuni' ? 'bg-green-600 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'" 
              class="px-3 md:px-4 py-1.5 md:py-2 rounded-lg font-medium transition-all duration-200 text-sm md:text-base">
        💼 Kampuni
      </button>

      <!-- 📄 Ripoti button styled like others -->
      <a href="{{ route('user.reports.select') }}" 
         class="px-3 md:px-4 py-1.5 md:py-2 rounded-lg font-medium transition-all duration-200
                bg-gray-100 text-gray-700 hover:bg-gray-200 shadow-sm text-sm md:text-base">
        📄 Ripoti
      </a>

      <button @click="setTab('historia')" 
              :class="tab === 'historia' ? 'bg-green-600 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'" 
              class="px-3 md:px-4 py-1.5 md:py-2 rounded-lg font-medium transition-all duration-200 text-sm md:text-base">
        🕒 Historia
      </button>
    </div>
  </div>
</header>


  <!-- 🔹 MAIN CONTENT -->
  <main class="p-3 md:p-6 space-y-4 md:space-y-6">
    <!-- ✅ GRAPHS TAB -->
    <div x-show="tab === 'graphs'" class="bg-white rounded-xl shadow-sm p-4 md:p-6" x-cloak>
      <div class="flex flex-wrap gap-2 mb-4 md:mb-6 border-b pb-3 md:pb-4 overflow-x-auto">
        <template x-for="(item, key) in graphTabs" :key="key">
          <button @click="setGraph(key)"
            :class="graph === key ? 'bg-green-600 text-white font-semibold shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
            class="px-3 md:px-4 py-1.5 md:py-2 rounded-lg transition-all duration-200 text-xs md:text-sm whitespace-nowrap"
            x-text="item"></button>
        </template>
      </div>

      <div class="h-64 md:h-96">
        <canvas x-show="graph === 'faidaBidhaa'" id="faidaBidhaaChart" x-cloak></canvas>
        <canvas x-show="graph === 'faidaSiku'" id="faidaSikuChart" x-cloak></canvas>
        <canvas x-show="graph === 'mauzoSiku'" id="mauzoSikuChart" x-cloak></canvas>
        <canvas x-show="graph === 'gharama'" id="gharamaChart" x-cloak></canvas>
        <canvas x-show="graph === 'mauzo'" id="mauzoChart" x-cloak></canvas>
      </div>
    </div>

    <!-- ✅ MWENENDO TAB -->
    <div x-show="tab === 'mwenendo'" 
         class="bg-white rounded-xl shadow-sm p-4 md:p-6" 
         x-cloak 
         x-data="mwenendoApp()">

      <!-- Header -->
      <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-3 md:gap-4 border-b pb-3 md:pb-4 mb-4 md:mb-6">
        <div>
          <h2 class="text-lg md:text-2xl font-bold text-gray-800">📈 Mwenendo wa Biashara</h2>
          <p class="text-gray-600 text-sm md:text-base mt-1">Chagua kipindi au tarehe kuona takwimu</p>
        </div>
        <button @click="refresh()" 
                class="flex items-center gap-2 bg-blue-600 text-white px-3 md:px-4 py-1.5 md:py-2 rounded-lg hover:bg-blue-700 transition-all duration-200 mt-2 md:mt-0 text-sm md:text-base">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 md:w-5 md:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
          </svg>
          <span class="hidden sm:inline">Pakua Upya</span>
          <span class="sm:hidden">Refresh</span>
        </button>
      </div>

      <!-- Selection -->
      <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 md:gap-4 mb-4 md:mb-6">
        <div class="w-full sm:flex-1">
          <label class="block font-medium text-gray-700 mb-1 md:mb-2 text-sm md:text-base">⏳ Chagua Muda:</label>
          <select x-model="viewType" 
                  @change="toggleDatePicker(false)" 
                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 text-sm md:text-base">
            <option value="Siku">Siku</option>
            <option value="Wiki">Wiki</option>
            <option value="Mwezi">Mwezi</option>
            <option value="Mwaka">Mwaka</option>
          </select>
        </div>

        <button @click="toggleDatePicker(true)" 
                class="w-full sm:w-auto bg-gradient-to-r from-green-600 to-green-500 text-white px-4 md:px-6 py-2 md:py-3 rounded-lg hover:from-green-700 hover:to-green-600 shadow-md transition-all duration-200 text-sm md:text-base">
          📅 Chagua Tarehe
        </button>
      </div>

      <!-- Auto Summary -->
      <div x-show="!manualDateSelect" 
           class="bg-gray-50 border border-gray-200 rounded-xl p-4 md:p-6 space-y-3 md:space-y-4 transition-all duration-300">

        <h3 class="font-bold text-gray-800 text-base md:text-lg">Muhtasari wa <span x-text="viewType"></span></h3>
        
        <template x-if="summary">
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
            <template x-for="[label, value] of Object.entries(summary)" :key="label">
              <div class="bg-white border border-gray-200 rounded-lg p-3 md:p-4 shadow-sm hover:shadow-md transition-all duration-200">
                <div class="text-xs md:text-sm text-gray-600 font-medium truncate" x-text="label"></div>
                <div class="text-lg md:text-xl font-bold text-green-600 mt-1 truncate" x-text="format(value)"></div>
              </div>
            </template>
          </div>
        </template>

        <template x-if="!summary">
          <div class="text-center py-6 md:py-8 text-gray-500 text-sm md:text-base">
            Hakuna data inayopatikana kwa kipindi hiki.
          </div>
        </template>
      </div>

      <!-- Manual Date Selection -->
      <div x-show="manualDateSelect" 
           class="border border-gray-200 rounded-xl p-4 md:p-6 bg-gray-50 space-y-3 md:space-y-4 transition-all duration-300">

        <h3 class="font-semibold text-gray-800 text-base md:text-lg">📅 Angalia Mwenendo kwa Tarehe</h3>
        
        <div class="flex flex-col sm:flex-row gap-3 md:gap-4 items-end">
          <div class="w-full sm:flex-1">
            <label class="block text-xs md:text-sm font-medium text-gray-600 mb-1">Kuanzia Tarehe:</label>
            <input type="date" x-model="dateFrom" 
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 text-sm md:text-base">
          </div>
          
          <div class="w-full sm:flex-1">
            <label class="block text-xs md:text-sm font-medium text-gray-600 mb-1">Mpaka Tarehe:</label>
            <input type="date" x-model="dateTo" 
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 text-sm md:text-base">
          </div>

          <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            <button @click="fetchCustomSummary()" 
                    class="bg-green-600 text-white px-4 md:px-6 py-2 rounded-lg hover:bg-green-700 shadow-md transition-all duration-200 text-sm md:text-base">
              🔍 Angalia
            </button>
            <button @click="toggleDatePicker(false)" 
                    class="text-gray-600 bg-gray-200 px-4 py-2 rounded-lg hover:bg-gray-300 transition-all duration-200 text-sm md:text-base">
              ↩ Rudi
            </button>
          </div>
        </div>

        <template x-if="customSummary">
          <div class="mt-4 md:mt-6 bg-white border border-gray-200 rounded-lg p-4 md:p-6 shadow-sm">
            <h4 class="text-green-700 font-semibold mb-3 md:mb-4 text-sm md:text-lg">Matokeo ya kipindi kilichochaguliwa:</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
              <template x-for="[label, value] of Object.entries(customSummary)" :key="label">
                <div class="border border-gray-200 rounded-lg p-2 md:p-3">
                  <div class="text-xs md:text-sm font-medium text-gray-600 truncate" x-text="label"></div>
                  <div class="text-base md:text-lg font-bold text-green-600 truncate" x-text="format(value)"></div>
                </div>
              </template>
            </div>
          </div>
        </template>
      </div>
    </div>

    <!-- ✅ KAMPUNI TAB -->
    <div x-show="tab === 'kampuni'" 
         x-data="kampuniData()"
         class="bg-white rounded-xl shadow-sm p-4 md:p-6"
         x-cloak>
      
      <!-- Title -->
      <h2 class="text-lg md:text-2xl font-bold text-gray-800 mb-1 md:mb-2">💼 Thamani ya Kampuni</h2>
      <p class="text-gray-600 text-sm md:text-base mb-4 md:mb-6">Muhtasari wa thamani ya kampuni na faida</p>

      <!-- Summary Boxes -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 mb-6 md:mb-8">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-4 md:p-6 shadow-sm hover:shadow-md transition-all duration-200">
          <h3 class="text-sm md:text-lg font-semibold text-gray-700 mb-1 md:mb-2">Thamani Kabla</h3>
          <p class="text-xl md:text-2xl font-bold text-blue-600 truncate" x-text="format(beforeSales)"></p>
        </div>

        <div class="bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-xl p-4 md:p-6 shadow-sm hover:shadow-md transition-all duration-200">
          <h3 class="text-sm md:text-lg font-semibold text-gray-700 mb-1 md:mb-2">Thamani Baada</h3>
          <p class="text-xl md:text-2xl font-bold text-green-600 truncate" x-text="format(afterSales)"></p>
        </div>

        <div class="bg-gradient-to-br from-amber-50 to-amber-100 border border-amber-200 rounded-xl p-4 md:p-6 shadow-sm hover:shadow-md transition-all duration-200">
          <h3 class="text-sm md:text-lg font-semibold text-gray-700 mb-1 md:mb-2">Faida</h3>
          <p class="text-xl md:text-2xl font-bold text-amber-600 truncate" x-text="format(faida)"></p>
        </div>
      </div>

      <!-- Company Total Value -->
      <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 md:p-6 mb-6 md:mb-8">
        <h3 class="font-semibold text-gray-700 mb-2 md:mb-3 text-sm md:text-base">Thamani ya Jumla ya Kampuni</h3>
        <div class="text-xl md:text-3xl font-bold text-green-600">
          {{ $thamaniKampuniFormatted }}
        </div>
      </div>

      <!-- Table -->
      <div>
        <h3 class="font-semibold text-gray-800 mb-3 md:mb-4 text-base md:text-lg">Orodha ya Bidhaa</h3>
        <div class="overflow-x-auto -mx-4 md:mx-0 bg-white border border-gray-200 rounded-xl shadow-sm">
          <table class="min-w-full text-xs md:text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
              <tr>
                <th class="px-3 py-2 md:px-4 md:py-3 text-left font-semibold text-gray-700">Bidhaa</th>
                <th class="px-3 py-2 md:px-4 md:py-3 text-left font-semibold text-gray-700 hidden sm:table-cell">Aina</th>
                <th class="px-3 py-2 md:px-4 md:py-3 text-left font-semibold text-gray-700 hidden md:table-cell">Kipimo</th>
                <th class="px-3 py-2 md:px-4 md:py-3 text-right font-semibold text-gray-700">Idadi</th>
                <th class="px-3 py-2 md:px-4 md:py-3 text-right font-semibold text-gray-700 hidden lg:table-cell">Nunua</th>
                <th class="px-3 py-2 md:px-4 md:py-3 text-right font-semibold text-gray-700">Kuuza</th>
                <th class="px-3 py-2 md:px-4 md:py-3 text-right font-semibold text-gray-700">Faida</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              @foreach($bidhaaList as $item)
                <tr class="hover:bg-gray-50 transition-colors duration-150">
                  <td class="px-3 py-2 md:px-4 md:py-3 text-gray-900 truncate max-w-[120px]">{{ $item->jina }}</td>
                  <td class="px-3 py-2 md:px-4 md:py-3 text-gray-600 hidden sm:table-cell truncate max-w-[80px]">{{ $item->aina }}</td>
                  <td class="px-3 py-2 md:px-4 md:py-3 text-gray-600 hidden md:table-cell truncate max-w-[60px]">{{ $item->kipimo ?? '-' }}</td>
                  <td class="px-3 py-2 md:px-4 md:py-3 text-right text-gray-900">{{ number_format($item->idadi) }}</td>
                  <td class="px-3 py-2 md:px-4 md:py-3 text-right text-gray-600 hidden lg:table-cell">Tsh {{ number_format($item->bei_nunua, 0) }}</td>
                  <td class="px-3 py-2 md:px-4 md:py-3 text-right text-gray-600">Tsh {{ number_format($item->bei_kuuza, 0) }}</td>
                  <td class="px-3 py-2 md:px-4 md:py-3 text-right font-semibold text-green-600">
                    Tsh {{ number_format($item->faida, 0) }}
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ✅ HISTORIA TAB -->
    <div x-show="tab === 'historia'" class="bg-white rounded-xl shadow-sm p-4 md:p-6" x-cloak>
      <h2 class="text-lg md:text-2xl font-bold text-gray-800 mb-1 md:mb-2">🕒 Historia ya Mfumo</h2>
      <p class="text-gray-600 text-sm md:text-base">Taarifa kuhusu historia ya mfumo na takwimu</p>
      <div class="mt-4 md:mt-6 p-6 md:p-8 text-center border-2 border-dashed border-gray-300 rounded-xl">
        <p class="text-gray-500 text-sm md:text-base">Inafanya kazi - Ukurasa wa historia utaanza hivi karibuni</p>
      </div>
    </div>
  </main>
</div>
@endsection

@push('styles')
<style>
[x-cloak] { display: none !important; }

@media (max-width: 640px) {
  table {
    font-size: 0.75rem;
  }
}
</style>
@endpush

@push('scripts')
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('alpine:init', () => {
  Alpine.data('dashboardApp', () => ({
    sidebarOpen: true,
    tab: 'graphs',
    graph: 'faidaBidhaa',
    charts: {},
    graphTabs: {
      faidaBidhaa: 'Faida kwa Bidhaa',
      faidaSiku: 'Faida kwa Siku',
      mauzoSiku: 'Mauzo kwa Siku',
      gharama: 'Gharama',
      mauzo: 'Mauzo Jumla'
    },

    faidaByBidhaa: @json($faidaBidhaa ?? []),
    faidaBySiku: @json($faidaSiku ?? []),
    mauzoBySiku: @json($mauzoSiku ?? []),
    gharamaByBidhaa: @json($gharama ?? []),
    mauzoByBidhaa: @json($mauzo ?? []),

    setTab(tabName) {
      this.tab = tabName;
      if(tabName === 'graphs') this.$nextTick(() => this.drawChart(this.graph));
    },

    setGraph(name) {
      this.graph = name;
      this.$nextTick(() => this.drawChart(name));
    },

    drawChart(name) {
      if(this.charts[name]) return;
      const ctx = document.getElementById(name + 'Chart');
      if(!ctx) return;

      let config = {};
      switch(name) {
        case 'faidaBidhaa': 
          config = this.barChart(this.faidaByBidhaa, 'jina', 'faida', 'rgba(79, 70, 229, 0.8)', 'Faida kwa Bidhaa');
          break;
        case 'faidaSiku': 
          config = this.lineChart(this.faidaBySiku, 'day', 'faida', 'rgba(16, 185, 129, 0.8)', 'Faida kwa Siku');
          break;
        case 'mauzoSiku': 
          config = this.lineChart(this.mauzoBySiku, 'day', 'total', 'rgba(245, 158, 11, 0.8)', 'Mauzo kwa Siku');
          break;
        case 'gharama': 
          config = this.barChart(this.gharamaByBidhaa, 'jina', 'total', 'rgba(239, 68, 68, 0.8)', 'Gharama');
          break;
        case 'mauzo': 
          config = this.barChart(this.mauzoByBidhaa, 'jina', 'total', 'rgba(139, 92, 246, 0.8)', 'Mauzo Jumla');
          break;
      }

      this.charts[name] = new Chart(ctx, config);
    },

    barChart(data, labelKey, valueKey, color, label) {
      return {
        type: 'bar',
        data: {
          labels: data.map(i => i[labelKey]),
          datasets: [{ 
            label, 
            data: data.map(i => Number(i[valueKey])), 
            backgroundColor: color,
            borderColor: color.replace('0.8', '1'),
            borderWidth: 1,
            borderRadius: 4
          }]
        },
        options: this.chartOptions()
      };
    },

    lineChart(data, labelKey, valueKey, color, label) {
      return {
        type: 'line',
        data: {
          labels: data.map(i => i[labelKey]),
          datasets: [{ 
            label, 
            data: data.map(i => Number(i[valueKey])), 
            borderColor: color,
            backgroundColor: color.replace('0.8', '0.1'),
            borderWidth: 3,
            tension: 0.3,
            fill: true
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
              font: {
                size: window.innerWidth < 768 ? 10 : 12
              }
            }
          }, 
          tooltip: { 
            enabled: true,
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            titleColor: 'white',
            bodyColor: 'white',
            titleFont: {
              size: window.innerWidth < 768 ? 10 : 12
            },
            bodyFont: {
              size: window.innerWidth < 768 ? 10 : 12
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
              font: {
                size: window.innerWidth < 768 ? 8 : 10
              }
            }
          },
          x: {
            grid: {
              display: false
            },
            ticks: {
              font: {
                size: window.innerWidth < 768 ? 8 : 10
              },
              maxRotation: 45,
              minRotation: 45
            }
          }
        }
      };
    },

    init() {
      this.$nextTick(() => this.drawChart(this.graph));
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
    
    format(v) { 
      return new Intl.NumberFormat('sw-TZ', {
        style: 'currency', 
        currency: 'TZS',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
      }).format(v || 0); 
    },

    updateSummary() {
      const key = this.viewType.toLowerCase();
      const data = this.mwenendoSummary[key] || null;
      this.summary = data ? data : null;
    },

    async fetchCustomSummary() {
      if(!this.dateFrom || !this.dateTo) {
        alert('Tafadhali chagua tarehe zote za kuanzia na mpaka');
        return;
      }
      
      try {
        // Show loading state
        this.customSummary = null;
        
        const res = await fetch(`/api/mwenendo?from=${this.dateFrom}&to=${this.dateTo}`);
        if (!res.ok) throw new Error('Network response was not ok');
        
        this.customSummary = await res.json();
      } catch(e) { 
        console.error('Hitilafu:', e);
        alert('Kumetokea hitilafu wakati wa kupakua data. Tafadhali jaribu tena.');
      }
    },

    refresh() {
      this.updateSummary();
      this.customSummary = null;
      this.manualDateSelect = false;
    },

    init() { 
      this.updateSummary(); 
    }
  }));

  Alpine.data('kampuniData', () => ({
    beforeSales: {{ $thamaniBefore }},
    afterSales: {{ $thamaniAfter }},
    faida: {{ $faida }},
    
    format(num) { 
      return new Intl.NumberFormat('sw-TZ', {
        style: 'currency',
        currency: 'TZS',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
      }).format(num); 
    }
  }));
});
</script>
@endpush