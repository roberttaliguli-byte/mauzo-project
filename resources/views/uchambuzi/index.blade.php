@extends('layouts.app')

@section('title', 'Uchambuzi')
@section('page-title', 'Dashibodi ya Taarifa')
@section('page-subtitle', 'Uchambuzi wa Biashara kwa Grafu na Takwimu')

@section('content')
<div class="bg-gray-100 min-h-screen p-4" x-data="dashboardApp()" x-init="init()">
  <!-- 🔹 Header -->
  <header class="bg-amber-500 shadow px-6 py-4 flex justify-between ">
    <h1 class="text-xl font-semibold text-black">TAARIFA KATIKA MAUZO</h1>
    <div class="flex items-center space-x-4">
      <button @click="setTab('graphs')" :class="tab==='graphs' ? 'font-bold underline text-green-900' : ''">Graphs</button>
      <button @click="setTab('mwenendo')" :class="tab==='mwenendo' ? 'font-bold underline text-green-800' : ''">Mwenendo</button>
      <button @click="setTab('kampuni')" :class="tab==='kampuni' ? 'font-bold underline text-green-800' : ''">Thamani ya Kampuni</button>
      <button @click="setTab('historia')" :class="tab==='historia' ? 'font-bold underline text-green-800' : ''">Historia</button>
    </div>
  </header>

  <!-- 🔹 MAIN CONTENT -->
  <main class="p-6 space-y-6">
    <!-- ✅ GRAPHS TAB -->
    <div x-show="tab==='graphs'" class="bg-white rounded shadow p-6" x-cloak>
      <div class="flex flex-wrap mb-6 border-b pb-2 space-x-2">
        <template x-for="(item, key) in graphTabs" :key="key">
          <button @click="setGraph(key)"
            :class="graph===key ? 'bg-green-500 text-white font-semibold border-b-4 border-green-700' : 'bg-green-100 text-black-700 hover:bg-green-300'"
            class="px-4 py-2 rounded-t transition-all duration-200"
            x-text="item"></button>
        </template>
      </div>

      <div class="h-96">
        <canvas x-show="graph==='faidaBidhaa'" id="faidaBidhaaChart" x-cloak></canvas>
        <canvas x-show="graph==='faidaSiku'" id="faidaSikuChart" x-cloak></canvas>
        <canvas x-show="graph==='mauzoSiku'" id="mauzoSikuChart" x-cloak></canvas>
        <canvas x-show="graph==='gharama'" id="gharamaChart" x-cloak></canvas>
        <canvas x-show="graph==='mauzo'" id="mauzoChart" x-cloak></canvas>
      </div>
    </div>

<!-- ✅ MWENENDO TAB -->
<div x-show="tab === 'mwenendo'" 
     class="bg-white rounded-2xl shadow-lg p-8 transition-all duration-500" 
     x-cloak 
     x-data="mwenendoApp()">

  <!-- Header -->
  <div class="flex justify-between items-center border-b pb-4 mb-6">
    <div>
      <h2 class="text-2xl font-semibold text-gray-800">📈 Mwenendo wa Biashara</h2>
      <p class="text-sm text-gray-500">Chagua kipindi au tarehe ili kuona takwimu za biashara zako.</p>
    </div>
    <button @click="refresh()" 
            class="flex items-center gap-2 text-blue-600 hover:text-blue-800 transition">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M4 4v6h6M20 20v-6h-6M5 19A9 9 0 0119 5"/>
      </svg>
      Refresh
    </button>
  </div>

  <!-- Selection -->
  <div class="flex flex-wrap items-center gap-4 mb-6">
    <div>
      <label class="font-medium text-gray-700">⏳ Chagua Muda:</label>
      <select x-model="viewType" 
              @change="toggleDatePicker(false)" 
              class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300 focus:outline-none">
        <option disabled selected>Chagua muda</option>
        <option>Siku</option>
        <option>Wiki</option>
        <option>Mwezi</option>
        <option>Mwaka</option>
      </select>
    </div>

    <button @click="toggleDatePicker(true)" 
            class="bg-gradient-to-r from-green-600 to-green-400 text-white px-4 py-2 rounded-lg hover:opacity-90 shadow-md transition-all">
      Au chagua tarehe mwenyewe
    </button>
  </div>

  <!-- Auto Summary -->
  <div x-show="!manualDateSelect" 
       class="bg-amber-500 border border-blue-100 rounded-xl p-6 shadow-inner space-y-3 transition-all duration-500">

    <h3 class="font-bold text-black text-lg">Muhtasari wa <span x-text="viewType"></span></h3>
    <template x-if="summary">
      <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 text-sm text-gray-700 mt-2">
        <template x-for="[label, value] of Object.entries(summary)" :key="label">
          <div class="bg-white border rounded-lg px-3 py-2 flex justify-between shadow-sm hover:shadow-md transition">
            <span class="font-medium" x-text="label"></span>
            <span class="text-green-600 font-semibold" x-text="format(value)"></span>
          </div>
        </template>
      </div>
    </template>
  </div>

  <!-- Manual Date Selection -->
  <div x-show="manualDateSelect" 
       class="border rounded-xl p-6 bg-gray-50 space-y-4 shadow-inner transition-all duration-500">

    <h3 class="font-semibold text-gray-700 text-lg">📅 Angalia Mwenendo kwa Tarehe</h3>
    <div class="flex flex-wrap gap-4 items-end">
      <div>
        <label class="block text-sm font-medium text-gray-600">Kuanzia Tarehe:</label>
        <input type="date" x-model="dateFrom" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200 focus:outline-none">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-600">Mpaka Tarehe:</label>
        <input type="date" x-model="dateTo" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200 focus:outline-none">
      </div>

      <div class="flex gap-2">
        <button @click="fetchCustomSummary()" 
                class="bg-green-600 text-white px-5 py-2 rounded-lg hover:bg-green-700 shadow-md transition">
          Angalia
        </button>
        <button @click="toggleDatePicker(false)" 
                class="text-gray-500 underline text-sm hover:text-gray-700">
          Rudi kwa chaguo la muda
        </button>
      </div>
    </div>

    <template x-if="customSummary">
      <div class="mt-6 bg-white border rounded-lg p-4 shadow">
        <h4 class="text-blue-700 font-semibold mb-3">Matokeo ya kipindi kilichochaguliwa:</h4>
        <template x-for="[label, value] of Object.entries(customSummary)" :key="label">
          <p class="flex justify-between text-sm"><strong x-text="label"></strong> <span x-text="format(value)" class="text-green-600 font-semibold"></span></p>
        </template>
      </div>
    </template>
  </div>
</div>

<!-- ✅ KAMPUNI TAB -->
<div 
  x-show="tab==='kampuni'" 
  x-data="{
    beforeSales: {{ $thamaniBefore }},
    afterSales: {{ $thamaniAfter }},
    faida: {{ $faida }},
    format(num) { return 'Tsh ' + new Intl.NumberFormat().format(num); }
  }"
  class="bg-gradient-to-br from-white to-amber-50 rounded-2xl shadow-lg p-6 mt-6 space-y-6"
  x-cloak
>
  <!-- Title -->
  <h2 class="text-2xl font-bold text-green-800 mb-2">💼 Thamani ya Kampuni</h2>

  <!-- Summary Boxes -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Before Sales -->
    <div class="bg-amber-500 text-black rounded-2xl p-5 shadow-md transform hover:scale-105 transition">
      <h3 class="text-lg font-semibold mb-1">Thamani Kabla ya mauzo</h3>
      <p class="text-2xl font-bold" x-text="format(beforeSales)"></p>
    </div>

    <!-- After Sales -->
    <div class="bg-amber-400 text-black rounded-2xl p-5 shadow-md transform hover:scale-105 transition">
      <h3 class="text-lg font-semibold mb-1">Thamani Baadaya mauzo</h3>
      <p class="text-2xl font-bold" x-text="format(afterSales)"></p>
    </div>

    <!-- Profit -->
    <div class="bg-amber-500 text-black rounded-2xl p-5 shadow-md transform hover:scale-105 transition">
      <h3 class="text-lg font-semibold mb-1">Faida (Profit)</h3>
      <p class="text-2xl font-bold" x-text="format(faida)"></p>
    </div>
  </div>

  <!-- Company Total Value -->
  <div class="mt-6">
    <h3 class="font-semibold text-md mb-2 text-black">Thamani ya Jumla ya Kampuni</h3>
    <div class="text-3xl font-bold text-green-800">
      {{ $thamaniKampuniFormatted }}
    </div>
  </div>

  <!-- Table -->
  <div class="mt-8">
    <h3 class="font-semibold text-md mb-3 text-green-700">Orodha ya Bidhaa</h3>
    <div class="overflow-x-auto bg-white rounded-xl shadow">
      <table class="min-w-full text-sm text-left border border-gray-100 rounded-lg">
        <thead class="bg-amber-600 text-white">
          <tr>
            <th class="px-4 py-2 border-b">Bidhaa</th>
            <th class="px-4 py-2 border-b">Aina</th>
            <th class="px-4 py-2 border-b">Kipimo</th>
            <th class="px-4 py-2 border-b text-right">Idadi</th>
            <th class="px-4 py-2 border-b text-right">Bei Nunua</th>
            <th class="px-4 py-2 border-b text-right">Bei Kuuza</th>
            <th class="px-4 py-2 border-b text-right">Faida</th>
          </tr>
        </thead>
        <tbody>
          @foreach($bidhaaList as $item)
            <tr class="hover:bg-green-50">
              <td class="px-4 py-2 border-b">{{ $item->jina }}</td>
              <td class="px-4 py-2 border-b">{{ $item->aina }}</td>
              <td class="px-4 py-2 border-b">{{ $item->kipimo ?? '-' }}</td>
              <td class="px-4 py-2 border-b text-right">{{ number_format($item->idadi) }}</td>
              <td class="px-4 py-2 border-b text-right">Tsh {{ number_format($item->bei_nunua, 2) }}</td>
              <td class="px-4 py-2 border-b text-right">Tsh {{ number_format($item->bei_kuuza, 2) }}</td>
              <td class="px-4 py-2 border-b text-right text-green-700 font-semibold">
                Tsh {{ number_format($item->faida, 2) }}
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>


    <!-- ✅ HISTORIA TAB -->
    <div x-show="tab==='historia'" class="bg-white rounded shadow p-6" x-cloak>
      <h2 class="font-semibold mb-4">Historia ya Mfumo</h2>
      <p class="text-gray-600">Taarifa kuhusu historia ya mfumo, mabadiliko, na takwimu zitapatikana hapa.</p>
    </div>
  </main>
</div>
@endsection

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
        case 'faidaBidhaa': config = this.barChart(this.faidaByBidhaa, 'jina', 'faida', 'rgba(255,99,132,0.7)', 'Faida'); break;
        case 'faidaSiku': config = this.lineChart(this.faidaBySiku, 'day', 'faida', 'rgba(54,162,235,0.9)', 'Faida Siku'); break;
        case 'mauzoSiku': config = this.lineChart(this.mauzoBySiku, 'day', 'total', 'rgba(255,206,86,0.9)', 'Mauzo Siku'); break;
        case 'gharama': config = this.barChart(this.gharamaByBidhaa, 'jina', 'total', 'rgba(75,192,192,0.7)', 'Gharama'); break;
        case 'mauzo': config = this.barChart(this.mauzoByBidhaa, 'jina', 'total', 'rgba(153,102,255,0.7)', 'Mauzo Jumla'); break;
      }

      this.charts[name] = new Chart(ctx, config);
    },

    barChart(data, labelKey, valueKey, color, label) {
      return {
        type: 'bar',
        data: {
          labels: data.map(i => i[labelKey]),
          datasets: [{ label, data: data.map(i => Number(i[valueKey])), backgroundColor: color }]
        },
        options: this.chartOptions()
      };
    },

    lineChart(data, labelKey, valueKey, color, label) {
      return {
        type: 'line',
        data: {
          labels: data.map(i => i[labelKey]),
          datasets: [{ label, data: data.map(i => Number(i[valueKey])), borderColor: color, backgroundColor: color + '33', tension: 0.3 }]
        },
        options: this.chartOptions()
      };
    },

    chartOptions() {
      return {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: true }, tooltip: { enabled: true } },
        scales: { y: { beginAtZero: true } }
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
    mwenendoSummary: @json($mwenendoSummary ?: []),

    toggleDatePicker(val) { this.manualDateSelect = val; if(!val) this.updateSummary(); },
    format(v) { return new Intl.NumberFormat('sw-TZ',{style:'currency',currency:'TZS'}).format(v || 0); },

    updateSummary() {
      const key = this.viewType.toLowerCase();
      const data = this.mwenendoSummary[key] || null;
      this.summary = data ? data : null;
    },

    async fetchCustomSummary() {
      if(!this.dateFrom || !this.dateTo) return;
      try {
        const res = await fetch(`/api/mwenendo?from=${this.dateFrom}&to=${this.dateTo}`);
        this.summary = await res.json();
      } catch(e) { console.error('Hitilafu:', e); }
    },

    init() { this.updateSummary(); }
  }));
});
</script>
@endpush
