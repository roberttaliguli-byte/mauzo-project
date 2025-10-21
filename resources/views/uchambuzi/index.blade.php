<!DOCTYPE html>
<html lang="sw">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Uchambuzi</title>

  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- AlpineJS -->
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body class="bg-gray-100" x-data="dashboardApp()" x-init="init()">

  <div class="flex h-screen">

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'w-64' : 'w-16'" class="bg-green-800 text-white flex flex-col transition-all duration-300 h-screen fixed">
      <div class="p-6 text-center border-b border-gray-800 flex flex-col items-center">
        <div x-show="sidebarOpen" class="text-2xl font-bold mb-1">DEMODAY</div>
        <div x-show="sidebarOpen" class="text-sm">Boss</div>
        <button @click="sidebarOpen = !sidebarOpen" class="mt-2 text-gray-400 hover:text-white">☰</button>
      </div>
      <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
        <a href="{{ route('dashboard') }}" class="block px-4 py-2 hover:bg-yellow-600 rounded">🏠 Dashboard</a>
        <a href="{{ route('mauzo.index') }}" class="block px-4 py-2 bg-yellow-700 rounded">🛒 Mauzo</a>
        <a href="{{ route('madeni.index') }}" class="block px-4 py-2 hover:bg-yellow-700 rounded">💳 Madeni</a>
        <a href="{{ route('matumizi.index') }}" class="block px-4 py-2 hover:bg-yellow-700 rounded">💰 Matumizi</a>
        <a href="{{ route('bidhaa.index') }}" class="block px-4 py-2 hover:bg-yellow-700 rounded">📦 Bidhaa</a>
        <a href="{{ route('manunuzi.index') }}" class="block px-4 py-2 hover:bg-yellow-700 rounded">🚚 Manunuzi</a>
        <a href="{{ route('wafanyakazi.index') }}" class="block px-4 py-2 hover:bg-yellow-700 rounded">👔 Wafanyakazi</a>
        <a href="{{ route('masaplaya.index') }}" class="block px-4 py-2 hover:bg-yellow-700 rounded">🏆 Masaplaya</a>
        <a href="{{ route('wateja.index') }}" class="block px-4 py-2 hover:bg-yellow-700 rounded">👥 Wateja</a>
        <a href="{{ route('uchambuzi.index') }}" class="block px-4 py-2 hover:bg-yellow-700 rounded">📊 Uchambuzi</a>
      </nav>
    </aside>

    <!-- Main content -->
    <div :class="sidebarOpen ? 'ml-64' : 'ml-16'" class="flex-1 flex flex-col transition-all duration-300 h-screen overflow-auto">

      <!-- Header -->
      <header class="bg-white shadow px-6 py-4 flex justify-between items-center sticky top-0 z-10">
        <h1 class="text-xl font-semibold text-gray-700">TAARIFA KATIKA GRAFU</h1>
        <div class="flex items-center space-x-4">
          <button @click="setTab('graphs')" :class="tab==='graphs' ? 'font-bold underline text-blue-600' : ''">Graphs</button>
          <button @click="setTab('mwenendo')" :class="tab==='mwenendo' ? 'font-bold underline text-blue-600' : ''">Mwenendo</button>
          <button @click="setTab('kampuni')" :class="tab==='kampuni' ? 'font-bold underline text-blue-600' : ''">Thamani ya Kampuni</button>
          <button @click="setTab('historia')" :class="tab==='historia' ? 'font-bold underline text-blue-600' : ''">Historia</button>
        </div>
      </header>

      <!-- Content -->
      <main class="p-6 flex-1 space-y-6">

<!-- Graphs Tab -->
<div x-show="tab==='graphs'" class="bg-white rounded shadow p-6" x-cloak>

  <!-- Sub-tabs -->
  <div class="flex space-x-2 mb-6 border-b pb-2 flex-wrap">

    <button @click="setGraph('faidaBidhaa')"
            :class="graph==='faidaBidhaa' ? 'bg-blue-600 text-white font-semibold border-b-4 border-blue-800' : 'bg-gray-100 text-gray-700 hover:bg-blue-100'"
            class="px-4 py-2 rounded-t transition-all duration-200">
      Faida Bidhaa
    </button>

    <button @click="setGraph('faidaSiku')"
            :class="graph==='faidaSiku' ? 'bg-blue-600 text-white font-semibold border-b-4 border-blue-800' : 'bg-gray-100 text-gray-700 hover:bg-blue-100'"
            class="px-4 py-2 rounded-t transition-all duration-200">
      Faida Siku
    </button>

    <button @click="setGraph('mauzoSiku')"
            :class="graph==='mauzoSiku' ? 'bg-blue-600 text-white font-semibold border-b-4 border-blue-800' : 'bg-gray-100 text-gray-700 hover:bg-blue-100'"
            class="px-4 py-2 rounded-t transition-all duration-200">
      Mauzo Siku
    </button>

    <button @click="setGraph('gharama')"
            :class="graph==='gharama' ? 'bg-blue-600 text-white font-semibold border-b-4 border-blue-800' : 'bg-gray-100 text-gray-700 hover:bg-blue-100'"
            class="px-4 py-2 rounded-t transition-all duration-200">
      Gharama
    </button>

    <button @click="setGraph('mauzo')"
            :class="graph==='mauzo' ? 'bg-blue-600 text-white font-semibold border-b-4 border-blue-800' : 'bg-gray-100 text-gray-700 hover:bg-blue-100'"
            class="px-4 py-2 rounded-t transition-all duration-200">
      Mauzo
    </button>

  </div>

  <!-- Charts -->
  <div class="h-96">
    <canvas x-show="graph==='faidaBidhaa'" id="faidaBidhaaChart" x-cloak></canvas>
    <canvas x-show="graph==='faidaSiku'" id="faidaSikuChart" x-cloak></canvas>
    <canvas x-show="graph==='mauzoSiku'" id="mauzoSikuChart" x-cloak></canvas>
    <canvas x-show="graph==='gharama'" id="gharamaChart" x-cloak></canvas>
    <canvas x-show="graph==='mauzo'" id="mauzoChart" x-cloak></canvas>
  </div>

</div>

<!-- Mwenendo Tab -->
<div x-show="tab==='mwenendo'" class="bg-white rounded shadow p-6" x-cloak x-data="mwenendoApp()">
  <h2 class="font-semibold mb-4 text-lg text-gray-700">Mwenendo wa Biashara</h2>

  <!-- Selection controls -->
  <div class="flex flex-wrap items-center gap-4 mb-6">
    <div>
      <label class="font-medium">Chagua Muda:</label>
      <select x-model="viewType" @change="toggleDatePicker(false)" class="border rounded px-3 py-1 focus:ring focus:ring-blue-200">
        <option disabled selected>Chagua muda</option>
        <option>Siku</option>
        <option>Wiki</option>
        <option>Mwezi</option>
        <option>Mwaka</option>
      </select>
    </div>
    <button @click="toggleDatePicker(true)" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 transition">
      Au chagua tarehe mwenyewe
    </button>
  </div>

  <!-- Automatic Range Mode -->
  <div x-show="!manualDateSelect" class="bg-gray-50 border rounded p-4 space-y-2">
    <p><strong>Muda uliyochaguliwa:</strong> <span x-text="viewType"></span></p>
    <template x-if="summary">
      <div class="space-y-1 text-sm text-gray-700">
        <div><strong>Tarehe:</strong> <span x-text="summary.date"></span></div>
        <div><strong>Mapato Mauzo:</strong> <span x-text="format(summary.mapatoMauzo)"></span></div>
        <div><strong>Mapato Madeni:</strong> <span x-text="format(summary.mapatoMadeni)"></span></div>
        <div><strong>Jumla Mapato:</strong> <span x-text="format(summary.jumlaMapato)"></span></div>
        <div><strong>Jumla Matumizi:</strong> <span x-text="format(summary.jumlaMatumizi)"></span></div>
        <div><strong>Faida Mauzo:</strong> <span x-text="format(summary.faidaMauzo)"></span></div>
        <div><strong>Fedha Droo:</strong> <span x-text="format(summary.fedhaDroo)"></span></div>
        <div>
          <strong>Faida Halisi:</strong> 
          <span :class="summary.faidaHalisi >= 0 ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold'" 
                x-text="format(summary.faidaHalisi)">
          </span>
        </div>
      </div>
    </template>
  </div>

  <!-- Manual Date Range Mode -->
  <div x-show="manualDateSelect" class="border rounded p-4 bg-gray-50 space-y-4">
    <h3 class="font-semibold text-gray-700">Angalia Mwenendo kwa Tarehe</h3>
    <div class="flex flex-wrap gap-4 items-end">
      <div>
        <label class="block text-sm font-medium">Kuanzia Tarehe:</label>
        <input type="date" x-model="dateFrom" class="border rounded px-3 py-1">
      </div>
      <div>
        <label class="block text-sm font-medium">Mpaka Tarehe:</label>
        <input type="date" x-model="dateTo" class="border rounded px-3 py-1">
      </div>
      <button @click="fetchCustomSummary()" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
        Angalia
      </button>
      <button @click="toggleDatePicker(false)" class="text-gray-500 underline">Rudi kwa chaguo la muda</button>
    </div>

    <!-- Results -->
    <template x-if="summary">
      <div class="mt-4 bg-white border rounded p-4 shadow-sm text-sm text-gray-700 space-y-1">
        <div><strong>Tarehe:</strong> <span x-text="summary.dateRange"></span></div>
        <div><strong>Mapato Mauzo:</strong> <span x-text="format(summary.mapatoMauzo)"></span></div>
        <div><strong>Mapato Madeni:</strong> <span x-text="format(summary.mapatoMadeni)"></span></div>
        <div><strong>Jumla Mapato:</strong> <span x-text="format(summary.jumlaMapato)"></span></div>
        <div><strong>Jumla Matumizi:</strong> <span x-text="format(summary.jumlaMatumizi)"></span></div>
        <div><strong>Faida Mauzo:</strong> <span x-text="format(summary.faidaMauzo)"></span></div>
        <div><strong>Fedha Droo:</strong> <span x-text="format(summary.fedhaDroo)"></span></div>
        <div>
          <strong>Faida Halisi:</strong> 
          <span :class="summary.faidaHalisi >= 0 ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold'"
                x-text="format(summary.faidaHalisi)">
          </span>
        </div>
      </div>
    </template>
  </div>
</div>


  
<div x-show="tab==='kampuni'" class="bg-white rounded shadow p-6 mt-6" x-cloak>
  <h2 class="font-semibold text-lg mb-4">Thamani ya Kampuni</h2>
  <div class="text-xl font-bold text-green-700 mb-6">
    {{ $thamaniKampuniFormatted }}
  </div>

  <h3 class="font-semibold text-md mb-3">Orodha ya Bidhaa</h3>

  <div class="overflow-x-auto">
    <table class="min-w-full text-sm text-left border border-gray-200 rounded-lg">
      <thead class="bg-gray-100 font-medium text-gray-700">
        <tr>
          <th class="px-4 py-2 border-b">Bidhaa</th>
          <th class="px-4 py-2 border-b">Aina</th>
          <th class="px-4 py-2 border-b">Kipimo</th>
          <th class="px-4 py-2 border-b text-right">Idadi</th>
          <th class="px-4 py-2 border-b text-right">Thamani @</th>
        </tr>
      </thead>
      <tbody>
        @foreach($bidhaaList as $item)
          <tr class="hover:bg-gray-50">
            <td class="px-4 py-2 border-b">{{ $item->jina }}</td>
            <td class="px-4 py-2 border-b">{{ $item->aina }}</td>
            <td class="px-4 py-2 border-b">{{ $item->kipimo ?? '-' }}</td>
            <td class="px-4 py-2 border-b text-right">{{ number_format($item->idadi) }}</td>
            <td class="px-4 py-2 border-b text-right">Tsh {{ number_format($item->thamani, 2) }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>


  <!-- Historia Tab -->

       <div x-show="tab==='historia'" class="bg-white rounded shadow p-6" x-cloak>
  <h2 class="font-semibold mb-4">Historia ya Mfumo</h2>
  
  
</div>


      </main>
    </div>
  </div>

<script>
document.addEventListener('alpine:init', () => {
  Alpine.data('dashboardApp', () => ({
    sidebarOpen: true,
    tab: 'graphs',
    graph: 'faidaBidhaa',
    charts: {},

    // Laravel data
    faidaByBidhaa: @json($faidaBidhaa ?? []),
    faidaBySiku: @json($faidaSiku ?? []),
    mauzoBySiku: @json($mauzoSiku ?? []),
    gharamaByBidhaa: @json($gharama ?? []),
    mauzoByBidhaa: @json($mauzo ?? []),

    // 🏢 Thamani ya Kampuni
    thamaniKampuniFormatted: @json($thamaniKampuniFormatted ?? 'Tsh 0.00'),

    // 📦 Bidhaa data from controller
    bidhaaList: @json($bidhaaList ?? []),

    // 🧾 Mwenendo summary (from backend)
    mwenendoSummary: @json($mwenendoSummary ?: []),


    // --- Utility: currency formatting ---
    formatCurrency(value) {
      if (isNaN(value)) return 'Tsh 0.00';
      return new Intl.NumberFormat('sw-TZ', { style: 'currency', currency: 'TZS' }).format(value);
    },

    // Chart tab functions
    setTab(tabName) {
      this.tab = tabName;
      if(tabName === 'graphs') this.$nextTick(() => { this.drawChart(this.graph); });
    },

    setGraph(name) { 
      this.graph = name; 
      this.$nextTick(() => { this.drawChart(name); });
    },

    drawChart(name) {
      if(this.charts[name]) return;
      const ctx = document.getElementById(name + 'Chart');
      if (!ctx) return;

      let config = {};

      switch (name) {
        case 'faidaBidhaa':
          config = {
            type: 'bar',
            data: {
              labels: this.faidaByBidhaa.map(i => i.jina),
              datasets: [{
                label: 'Faida',
                data: this.faidaByBidhaa.map(i => Number(i.faida)),
                backgroundColor: 'rgba(255, 99, 132, 0.7)'
              }]
            }
          };
          break;
        case 'faidaSiku':
          config = {
            type: 'line',
            data: {
              labels: this.faidaBySiku.map(i => i.day),
              datasets: [{
                label: 'Faida Siku',
                data: this.faidaBySiku.map(i => Number(i.faida)),
                borderColor: 'rgba(54, 162, 235, 0.9)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                tension: 0.3
              }]
            }
          };
          break;
        case 'mauzoSiku':
          config = {
            type: 'line',
            data: {
              labels: this.mauzoBySiku.map(i => i.day),
              datasets: [{
                label: 'Mauzo Siku',
                data: this.mauzoBySiku.map(i => Number(i.total)),
                borderColor: 'rgba(255, 206, 86, 0.9)',
                backgroundColor: 'rgba(255, 206, 86, 0.2)',
                tension: 0.3
              }]
            }
          };
          break;
        case 'gharama':
          config = {
            type: 'bar',
            data: {
              labels: this.gharamaByBidhaa.map(i => i.jina),
              datasets: [{
                label: 'Gharama',
                data: this.gharamaByBidhaa.map(i => Number(i.total)),
                backgroundColor: 'rgba(75, 192, 192, 0.7)'
              }]
            }
          };
          break;
        case 'mauzo':
          config = {
            type: 'bar',
            data: {
              labels: this.mauzoByBidhaa.map(i => i.jina),
              datasets: [{
                label: 'Mauzo Jumla',
                data: this.mauzoByBidhaa.map(i => Number(i.total)),
                backgroundColor: 'rgba(153, 102, 255, 0.7)'
              }]
            }
          };
          break;
      }

      this.charts[name] = new Chart(ctx, {
        ...config,
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: true, position: 'top' },
            tooltip: { enabled: true }
          },
          scales: {
            x: { ticks: { autoSkip: false, maxRotation: 90, minRotation: 45 } },
            y: {
              beginAtZero: true,
              ticks: {
                callback: function(value) {
                  if(value >= 1000000) return (value/1000000)+'M';
                  if(value >= 1000) return (value/1000)+'K';
                  return value;
                }
              }
            }
          }
        }
      });
    },

    // --- 🧾 Display mwenendo summary text ---
    get mwenendoDetails() {
      if (!this.mwenendoSummary) return null;

      return {
        tarehe: this.mwenendoSummary.tarehe ?? '',
        mapatoMauzo: this.formatCurrency(this.mwenendoSummary.mapato_mauzo ?? 0),
        mapatoMadeni: this.formatCurrency(this.mwenendoSummary.mapato_madeni ?? 0),
        jumlaMapato: this.formatCurrency(this.mwenendoSummary.jumla_mapato ?? 0),
        matumizi: this.formatCurrency(this.mwenendoSummary.jumla_mat ?? 0),
        faidaMauzo: this.formatCurrency(this.mwenendoSummary.faida_mauzo ?? 0),
        fedhaDroo: this.formatCurrency(this.mwenendoSummary.fedha_droo ?? 0),
        faidaHalisi: this.formatCurrency(this.mwenendoSummary.faida_halisi ?? 0)
      };
    },

    init() {
      this.$nextTick(() => { this.drawChart(this.graph); });
    }
  }));
});
</script>
<script>
document.addEventListener('alpine:init', () => {
  Alpine.data('mwenendoApp', () => ({
    viewType: 'Siku',
    manualDateSelect: false,
    dateFrom: '',
    dateTo: '',
    summary: null,

    // Load Laravel data (server should pass an object with keys: siku, wiki, mwezi, mwaka)

    mwenendoSummary: @json($mwenendoSummary ?: []),


    // --- Toggle between automatic and manual date mode ---
    toggleDatePicker(val) {
      this.manualDateSelect = val;
      if (!val) this.updateSummary();
    },

    // --- Format currency (TZS) ---
    format(value) {
      if (isNaN(value)) return 'Tsh 0.00';
      return new Intl.NumberFormat('sw-TZ', {
        style: 'currency',
        currency: 'TZS'
      }).format(value || 0);
    },

    // --- Fetch summary based on selected period (Siku, Wiki, Mwezi, Mwaka) ---
    updateSummary() {
      const key = this.viewType.toLowerCase(); // 'siku', 'wiki', 'mwezi', 'mwaka'
      const data = this.mwenendoSummary[key] || null;

      if (!data) {
        this.summary = null;
        return;
      }

      // normalize to fields the template expects
      this.summary = {
        date: data.date ?? new Date().toLocaleDateString('sw-TZ'),
        mapatoMauzo: Number(data.mapato_mauzo ?? 0),
        mapatoMadeni: Number(data.mapato_madeni ?? 0),
        jumlaMapato: Number(data.jumla_mapato ?? 0),
        jumlaMatumizi: Number(data.jumla_mat ?? 0),
        faidaMauzo: Number(data.faida_mauzo ?? 0),
        fedhaDroo: Number(data.fedha_droo ?? 0),
        faidaHalisi: Number(data.faida_halisi ?? 0)
      };
    },

    // --- Fetch summary from API based on custom date range ---
    async fetchCustomSummary() {
      if (!this.dateFrom || !this.dateTo) return;

      try {
        const response = await fetch(`/api/mwenendo?from=${this.dateFrom}&to=${this.dateTo}`);
        const data = await response.json();

        this.summary = {
          dateRange: `${this.dateFrom} - ${this.dateTo}`,
          mapatoMauzo: Number(data.mapato_mauzo ?? 0),
          mapatoMadeni: Number(data.mapato_madeni ?? 0),
          jumlaMapato: Number(data.jumla_mapato ?? 0),
          jumlaMatumizi: Number(data.jumla_mat ?? 0),
          faidaMauzo: Number(data.faida_mauzo ?? 0),
          fedhaDroo: Number(data.fedha_droo ?? 0),
          faidaHalisi: Number(data.faida_halisi ?? 0)
        };
      } catch (error) {
        console.error('Hitilafu wakati wa kuchukua data ya mwenendo:', error);
      }
    },

    // --- Initialize with default summary ---
    init() {
      this.updateSummary();
    }
  }));
});
</script>


</body>
</html>