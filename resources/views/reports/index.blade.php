@extends('layouts.app')

@section('title', 'Ripoti za Biashara')
@section('page-title', 'Ripoti za Biashara')
@section('page-subtitle', 'Pakua ripoti za mauzo, faida na mwenendo')

@section('content')
<div class="bg-gray-50 min-h-screen" x-data="reportApp()">
  <!-- Header -->
  <div class="bg-white shadow-sm border-b px-6 py-4 mb-6">
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">📥 RIPOTI ZA BIASHARA</h1>
        <p class="text-gray-600 mt-1">Chagua aina ya ripoti na muda wake</p>
      </div>
      <a href="{{ route('uchambuzi.index') }}" 
         class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 font-medium">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Rudi kwenye Uchambuzi
      </a>
    </div>
  </div>

  <div class="max-w-4xl mx-auto p-6">
    <!-- Selection Card -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-8">
      <h2 class="text-xl font-bold text-gray-800 mb-6">Chagua Ripoti</h2>
      
      <!-- Report Type Selection -->
      <div class="mb-8">
        <label class="block text-sm font-medium text-gray-700 mb-3">Aina ya Ripoti:</label>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          @foreach($reportTypes as $key => $type)
            <label class="relative">
              <input type="radio" name="report_type" value="{{ $key }}" 
                     class="peer sr-only" @change="updatePreview()"
                     x-model="reportType">
              <div class="cursor-pointer p-4 border-2 rounded-lg border-gray-200 
                         hover:border-blue-400 peer-checked:border-blue-500 
                         peer-checked:bg-blue-50 transition-all duration-200">
                <div class="font-medium text-gray-800">{{ $type['title'] }}</div>
                <div class="text-sm text-gray-600 mt-1">{{ $type['description'] }}</div>
              </div>
            </label>
          @endforeach
        </div>
      </div>

      <!-- Period Selection -->
      <div class="mb-8">
        <label class="block text-sm font-medium text-gray-700 mb-3">Muda wa Ripoti:</label>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
          <template x-for="period in periods" :key="period.value">
            <label class="relative">
              <input type="radio" name="period" :value="period.value" 
                     class="peer sr-only" @change="updatePreview()"
                     x-model="selectedPeriod">
              <div class="cursor-pointer p-3 text-center border-2 rounded-lg border-gray-200 
                         hover:border-green-400 peer-checked:border-green-500 
                         peer-checked:bg-green-50 transition-all duration-200">
                <div class="font-medium" x-text="period.label"></div>
              </div>
            </label>
          </template>
        </div>
      </div>

      <!-- Custom Date Range -->
      <div x-show="selectedPeriod === 'custom'" class="mb-8 border border-gray-200 rounded-lg p-6 bg-gray-50">
        <h3 class="font-medium text-gray-700 mb-4">Chagua Tarehe Mwenyewe:</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-600 mb-2">Kuanzia Tarehe:</label>
            <input type="date" x-model="dateFrom" @change="updatePreview()"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-600 mb-2">Mpaka Tarehe:</label>
            <input type="date" x-model="dateTo" @change="updatePreview()"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
          </div>
        </div>
      </div>

      <!-- Report Preview -->
      <div x-show="previewData" class="mb-8 border border-gray-200 rounded-lg p-6 bg-gray-50">
        <h3 class="font-medium text-gray-700 mb-4">Onyesho la Ripoti:</h3>
        <div class="space-y-3">
          <div class="flex justify-between">
            <span class="text-gray-600">Aina ya Ripoti:</span>
            <span class="font-medium" x-text="getReportTypeTitle()"></span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-600">Muda:</span>
            <span class="font-medium" x-text="getPeriodLabel()"></span>
          </div>
          <div class="flex justify-between" x-show="selectedPeriod === 'custom'">
            <span class="text-gray-600">Tarehe:</span>
            <span class="font-medium" x-text="dateFrom + ' - ' + dateTo"></span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-600">Tarehe ya Uundaji:</span>
            <span class="font-medium" x-text="new Date().toLocaleDateString('sw-TZ')"></span>
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex flex-col sm:flex-row gap-4">
        <button @click="generateReport()" 
                :disabled="loading"
                class="flex-1 bg-gradient-to-r from-blue-600 to-blue-500 text-white px-6 py-3 rounded-lg 
                       hover:from-blue-700 hover:to-blue-600 shadow-md transition-all duration-200 
                       flex items-center justify-center gap-2"
                :class="{'opacity-50 cursor-not-allowed': loading}">
          <svg x-show="!loading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
          <svg x-show="loading" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" 
                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
          </svg>
          <span x-text="loading ? 'Inatengeneza...' : 'Tengeneza & Pakua'"></span>
        </button>
        
        <button @click="viewOnScreen()" 
                class="flex-1 border border-blue-600 text-blue-600 px-6 py-3 rounded-lg 
                       hover:bg-blue-50 transition-all duration-200">
          Angalia kwenye Skrini
        </button>
      </div>
    </div>

    <!-- Recent Reports -->
    <div class="bg-white rounded-xl shadow-md p-6">
      <h2 class="text-xl font-bold text-gray-800 mb-6">Ripoti Zilizopakuliwa Hivi Karibuni</h2>
      <div x-show="recentReports.length === 0" class="text-center py-8 text-gray-500">
        <p>Hujapakua ripoti yoyote bado.</p>
      </div>
      <div x-show="recentReports.length > 0" class="space-y-3">
        <template x-for="report in recentReports" :key="report.id">
          <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
            <div>
              <div class="font-medium text-gray-800" x-text="report.name"></div>
              <div class="text-sm text-gray-600" x-text="report.date"></div>
            </div>
            <a :href="report.url" class="text-blue-600 hover:text-blue-800 font-medium">
              Pakua Tena
            </a>
          </div>
        </template>
      </div>
    </div>
  </div>
</div>

<!-- View Report Modal -->
<div x-show="viewModalOpen" 
     class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
     x-cloak>
  <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col">
    <div class="flex justify-between items-center border-b px-6 py-4">
      <h3 class="text-xl font-bold text-gray-800">Ripoti: <span x-text="getReportTypeTitle()"></span></h3>
      <button @click="viewModalOpen = false" class="text-gray-400 hover:text-gray-600">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>
    
    <div class="flex-1 overflow-auto p-6">
      <div class="bg-white p-8 border border-gray-200 rounded-lg" x-html="reportContent"></div>
    </div>
    
    <div class="border-t px-6 py-4 flex justify-end gap-3">
      <button @click="viewModalOpen = false" class="px-4 py-2 text-gray-600 hover:text-gray-800">
        Funga
      </button>
      <button @click="downloadCurrentReport()" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
        Pakua PDF
      </button>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
document.addEventListener('alpine:init', () => {
  Alpine.data('reportApp', () => ({
    reportType: 'sales',
    selectedPeriod: 'today',
    dateFrom: new Date().toISOString().split('T')[0],
    dateTo: new Date().toISOString().split('T')[0],
    loading: false,
    previewData: true,
    viewModalOpen: false,
    reportContent: '',
    recentReports: [],
    
    periods: [
      { value: 'today', label: 'Leo' },
      { value: 'yesterday', label: 'Jana' },
      { value: 'week', label: 'Wiki' },
      { value: 'month', label: 'Mwezi' },
      { value: 'year', label: 'Mwaka' },
      { value: 'all', label: 'Yote' },
      { value: 'custom', label: 'Chagua Tarehe' }
    ],

    init() {
      this.updatePreview();
      this.loadRecentReports();
    },

    getReportTypeTitle() {
      const types = @json($reportTypes);
      return types[this.reportType]?.title || '';
    },

    getPeriodLabel() {
      const period = this.periods.find(p => p.value === this.selectedPeriod);
      return period ? period.label : '';
    },

    updatePreview() {
      // You can add logic to fetch preview data from server
      this.previewData = true;
    },

    async generateReport() {
      this.loading = true;
      
      try {
        const params = new URLSearchParams({
          type: this.reportType,
          period: this.selectedPeriod,
          from: this.dateFrom,
          to: this.dateTo,
          format: 'pdf'
        });

        const response = await fetch(`/api/reports/generate?${params}`);
        
        if (!response.ok) throw new Error('Failed to generate report');
        
        // Create a blob from the PDF response
        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        
        // Create download link
        const a = document.createElement('a');
        a.href = url;
        a.download = this.getFileName();
        document.body.appendChild(a);
        a.click();
        
        // Cleanup
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
        
        // Save to recent reports
        this.saveToRecentReports();
        
      } catch (error) {
        console.error('Error generating report:', error);
        alert('Kumetokea hitilafu wakati wa kutengeneza ripoti. Tafadhali jaribu tena.');
      } finally {
        this.loading = false;
      }
    },

    async viewOnScreen() {
      this.loading = true;
      
      try {
        const params = new URLSearchParams({
          type: this.reportType,
          period: this.selectedPeriod,
          from: this.dateFrom,
          to: this.dateTo,
          format: 'html'
        });

        const response = await fetch(`/api/reports/generate?${params}`);
        this.reportContent = await response.text();
        this.viewModalOpen = true;
        
      } catch (error) {
        console.error('Error loading report:', error);
        alert('Kumetokea hitilafu wakati wa kuona ripoti.');
      } finally {
        this.loading = false;
      }
    },

    async downloadCurrentReport() {
      // Logic to download the currently viewed report as PDF
      await this.generateReport();
      this.viewModalOpen = false;
    },

    getFileName() {
      const typeMap = {
        'sales': 'mauzo',
        'profit': 'faida',
        'expenses': 'gharama',
        'inventory': 'hisabati',
        'trends': 'mwenendo'
      };
      
      const periodMap = {
        'today': 'leo',
        'yesterday': 'jana',
        'week': 'wiki',
        'month': 'mwezi',
        'year': 'mwaka',
        'all': 'yote',
        'custom': 'tarehe'
      };
      
      const type = typeMap[this.reportType] || 'ripoti';
      const period = periodMap[this.selectedPeriod] || 'muda';
      const date = new Date().toISOString().split('T')[0];
      
      return `${type}_${period}_${date}.pdf`;
    },

    saveToRecentReports() {
      const report = {
        id: Date.now(),
        name: `${this.getReportTypeTitle()} - ${this.getPeriodLabel()}`,
        date: new Date().toLocaleDateString('sw-TZ', {
          year: 'numeric',
          month: 'long',
          day: 'numeric',
          hour: '2-digit',
          minute: '2-digit'
        }),
        url: `#`
      };
      
      this.recentReports.unshift(report);
      
      // Keep only last 5 reports
      if (this.recentReports.length > 5) {
        this.recentReports.pop();
      }
      
      // Save to localStorage
      localStorage.setItem('recentReports', JSON.stringify(this.recentReports));
    },

    loadRecentReports() {
      const saved = localStorage.getItem('recentReports');
      if (saved) {
        this.recentReports = JSON.parse(saved);
      }
    }
  }));
});
</script>
@endpush