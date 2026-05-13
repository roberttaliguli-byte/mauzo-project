{{-- resources/views/admin/company-statistics.blade.php --}}
@extends('layouts.admin')

@section('title', 'Takwimu za Makampuni')
@section('page-title', 'Takwimu za Makampuni')
@section('page-subtitle', 'Angalia takwimu za makampuni kwa vyanzo na aina za biashara')

@section('content')
<div class="space-y-6" x-data="companyStatistics()" x-init="init()">
    <!-- Loading Indicator -->
    <div x-show="loading" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl p-6 flex items-center gap-3">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-emerald-600"></div>
            <span>Inapakia data...</span>
        </div>
    </div>

    <!-- Error Alert -->
    <div x-show="error" class="bg-red-50 border border-red-200 rounded-xl p-4 text-red-700">
        <i class="fas fa-exclamation-circle mr-2"></i>
        <span x-text="errorMessage"></span>
    </div>

    <!-- Tabs Navigation -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex border-b border-gray-200">
            <button 
                @click="activeTab = 'sources'; fetchSourcesData()"
                :class="{'border-emerald-600 text-emerald-700 bg-emerald-50': activeTab === 'sources', 'border-transparent text-gray-600 hover:text-gray-800 hover:border-gray-300': activeTab !== 'sources'}"
                class="flex-1 px-4 py-3 md:px-6 md:py-4 text-sm md:text-base font-medium border-b-2 transition-all duration-200">
                <i class="fas fa-bullhorn mr-2"></i>
                Vyanzo vya Usajili
            </button>
            <button 
                @click="activeTab = 'businessTypes'; fetchBusinessData()"
                :class="{'border-emerald-600 text-emerald-700 bg-emerald-50': activeTab === 'businessTypes', 'border-transparent text-gray-600 hover:text-gray-800 hover:border-gray-300': activeTab !== 'businessTypes'}"
                class="flex-1 px-4 py-3 md:px-6 md:py-4 text-sm md:text-base font-medium border-b-2 transition-all duration-200">
                <i class="fas fa-store mr-2"></i>
                Aina za Biashara
            </button>
        </div>

        <!-- SOURCES TAB -->
        <div x-show="activeTab === 'sources'" x-cloak class="p-4 md:p-6">
            <!-- Summary Cards for Sources -->
            <div class="mb-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-chart-pie text-emerald-600 mr-2"></i>
                    Muhtasari wa Vyanzo
                </h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                    <template x-for="source in sourcesSummary" :key="source.name">
                        <div class="bg-gradient-to-br from-amber-50 to-yellow-50 rounded-xl p-3 border border-amber-200 hover:shadow-md transition-all cursor-pointer" 
                             @click="filterBySource(source.name)">
                            <div class="flex items-center justify-between mb-1">
                                <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-bullhorn text-amber-600 text-sm"></i>
                                </div>
                                <span class="text-xs font-bold text-amber-600" x-text="source.percentage + '%'"></span>
                            </div>
                            <p class="text-xs text-gray-600 truncate" x-text="source.label"></p>
                            <p class="text-xl font-bold text-gray-800" x-text="source.count"></p>
                        </div>
                    </template>
                    <div x-show="sourcesSummary.length === 0 && !loading" class="col-span-full text-center text-gray-500 py-4">
                        Hakuna data ya vyanzo
                    </div>
                </div>
            </div>

            <!-- Filter Bar for Sources -->
            <div class="bg-gray-50 rounded-xl p-4 mb-6">
                <div class="flex flex-col md:flex-row gap-3">
                    <div class="flex-1">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Chagua Chanzo</label>
                        <select x-model="sourceFilter" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-emerald-500">
                            <option value="">Vyanzo Vyote</option>
                            <template x-for="source in sourcesSummary" :key="source.name">
                                <option :value="source.name" x-text="source.label + ' (' + source.count + ')'"></option>
                            </template>
                        </select>
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Tafuta Kampuni</label>
                        <input type="text" x-model="sourceSearch" placeholder="Tafuta kwa jina la kampuni au mmiliki..." 
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div class="flex items-end gap-2">
                        <button @click="resetSourceFilters" class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-100 transition">
                            <i class="fas fa-undo-alt mr-1"></i> Weka Upya
                        </button>
                        <button @click="exportSourcesData" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition">
                            <i class="fas fa-download mr-1"></i> Pakua CSV
                        </button>
                    </div>
                </div>
            </div>

            <!-- Sources Companies Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">#</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 cursor-pointer hover:bg-gray-100" @click="sortSourcesBy('company_name')">Kampuni</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 cursor-pointer hover:bg-gray-100" @click="sortSourcesBy('owner_name')">Mmiliki</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Chanzo</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 cursor-pointer hover:bg-gray-100" @click="sortSourcesBy('created_at')">Tarehe</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Simu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-for="(company, index) in paginatedSourceCompanies" :key="company.id">
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-gray-500" x-text="(sourceCurrentPage - 1) * sourceItemsPerPage + index + 1"></td>
                                <td class="px-4 py-3 font-medium text-gray-800" x-text="company.company_name"></td>
                                <td class="px-4 py-3 text-gray-600" x-text="company.owner_name"></td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-full text-xs" :class="getSourceColorClass(company.hear_about_us)" x-text="company.hear_about_us_label"></span>
                                </td>
                                <td class="px-4 py-3 text-gray-500 text-xs" x-text="formatDate(company.created_at)"></td>
                                <td class="px-4 py-3 text-gray-500 text-xs" x-text="company.phone"></td>
                            </tr>
                        </template>
                        <tr x-show="filteredSourceCompanies.length === 0 && !loading">
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                <i class="fas fa-search text-3xl mb-2 block"></i>
                                Hakuna makampuni yanayolingana
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Sources Pagination -->
            <div class="mt-4 flex flex-col md:flex-row justify-between items-center gap-3" x-show="filteredSourceCompanies.length > 0">
                <div class="text-sm text-gray-600">Inaonyesha <span x-text="((sourceCurrentPage - 1) * sourceItemsPerPage) + 1"></span> - <span x-text="Math.min(sourceCurrentPage * sourceItemsPerPage, filteredSourceCompanies.length)"></span> kati ya <span x-text="filteredSourceCompanies.length"></span></div>
                <div class="flex gap-2">
                    <button @click="sourceCurrentPage--" :disabled="sourceCurrentPage === 1" class="px-3 py-1 border border-gray-300 rounded-lg text-sm disabled:opacity-50">Awali</button>
                    <span class="px-3 py-1 bg-emerald-600 text-white rounded-lg text-sm" x-text="sourceCurrentPage"></span>
                    <button @click="sourceCurrentPage++" :disabled="sourceCurrentPage >= sourceTotalPages" class="px-3 py-1 border border-gray-300 rounded-lg text-sm disabled:opacity-50">Ifuatayo</button>
                </div>
                <select x-model="sourceItemsPerPage" class="px-2 py-1 border border-gray-300 rounded-lg text-sm">
                    <option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option>
                </select>
            </div>
        </div>

        <!-- BUSINESS TYPES TAB (similar structure) -->
        <div x-show="activeTab === 'businessTypes'" x-cloak class="p-4 md:p-6">
            <div class="mb-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-chart-bar text-emerald-600 mr-2"></i>
                    Muhtasari wa Aina za Biashara
                </h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
                    <template x-for="type in businessSummary" :key="type.name">
                        <div class="bg-gradient-to-br from-emerald-50 to-green-50 rounded-xl p-3 border border-emerald-200 hover:shadow-md transition-all cursor-pointer" @click="filterByBusinessType(type.name)">
                            <div class="flex items-center justify-between mb-1">
                                <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center"><i class="fas fa-store text-emerald-600 text-sm"></i></div>
                                <span class="text-xs font-bold text-emerald-600" x-text="type.percentage + '%'"></span>
                            </div>
                            <p class="text-xs text-gray-600 truncate" x-text="type.label"></p>
                            <p class="text-xl font-bold text-gray-800" x-text="type.count"></p>
                        </div>
                    </template>
                    <div x-show="businessSummary.length === 0 && !loading" class="col-span-full text-center text-gray-500 py-4">
                        Hakuna data ya aina za biashara
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 rounded-xl p-4 mb-6">
                <div class="flex flex-col md:flex-row gap-3">
                    <div class="flex-1">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Chagua Aina ya Biashara</label>
                        <select x-model="businessTypeFilter" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-emerald-500">
                            <option value="">Aina Zote</option>
                            <template x-for="type in businessSummary" :key="type.name">
                                <option :value="type.name" x-text="type.label + ' (' + type.count + ')'"></option>
                            </template>
                        </select>
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Tafuta Kampuni</label>
                        <input type="text" x-model="businessSearch" placeholder="Tafuta kwa jina la kampuni au mmiliki..." class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div class="flex items-end gap-2">
                        <button @click="resetBusinessFilters" class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-100 transition">Weka Upya</button>
                        <button @click="exportBusinessData" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition">Pakua CSV</button>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr><th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">#</th><th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 cursor-pointer" @click="sortBusinessBy('company_name')">Kampuni</th><th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 cursor-pointer" @click="sortBusinessBy('owner_name')">Mmiliki</th><th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Aina</th><th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 cursor-pointer" @click="sortBusinessBy('created_at')">Tarehe</th><th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Simu</th></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-for="(company, index) in paginatedBusinessCompanies" :key="company.id">
                            <tr><td class="px-4 py-3 text-gray-500" x-text="(businessCurrentPage - 1) * businessItemsPerPage + index + 1"></td><td class="px-4 py-3 font-medium text-gray-800" x-text="company.company_name"></td><td class="px-4 py-3 text-gray-600" x-text="company.owner_name"></td><td class="px-4 py-3"><span class="px-2 py-1 rounded-full text-xs" :class="getBusinessTypeColorClass(company.business_type)" x-text="company.business_type_label"></span></td><td class="px-4 py-3 text-gray-500 text-xs" x-text="formatDate(company.created_at)"></td><td class="px-4 py-3 text-gray-500 text-xs" x-text="company.phone"></td></tr>
                        </template>
                        <tr x-show="filteredBusinessCompanies.length === 0 && !loading">
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                <i class="fas fa-search text-3xl mb-2 block"></i>
                                Hakuna makampuni yanayolingana
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-4 flex flex-col md:flex-row justify-between items-center gap-3" x-show="filteredBusinessCompanies.length > 0">
                <div class="text-sm text-gray-600">Inaonyesha <span x-text="((businessCurrentPage - 1) * businessItemsPerPage) + 1"></span> - <span x-text="Math.min(businessCurrentPage * businessItemsPerPage, filteredBusinessCompanies.length)"></span> kati ya <span x-text="filteredBusinessCompanies.length"></span></div>
                <div class="flex gap-2">
                    <button @click="businessCurrentPage--" :disabled="businessCurrentPage === 1" class="px-3 py-1 border border-gray-300 rounded-lg text-sm disabled:opacity-50">Awali</button>
                    <span class="px-3 py-1 bg-emerald-600 text-white rounded-lg text-sm" x-text="businessCurrentPage"></span>
                    <button @click="businessCurrentPage++" :disabled="businessCurrentPage >= businessTotalPages" class="px-3 py-1 border border-gray-300 rounded-lg text-sm disabled:opacity-50">Ifuatayo</button>
                </div>
                <select x-model="businessItemsPerPage" class="px-2 py-1 border border-gray-300 rounded-lg text-sm">
                    <option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option>
                </select>
            </div>
        </div>
    </div>
</div>

<style>[x-cloak] { display: none !important; }</style>
@endsection

@push('scripts')
<script>
    // Define source labels as JavaScript object
    const sourceLabelsData = {
        'friend': 'Rafiki',
        'social_media': 'Social Media',
        'facebook': 'Facebook',
        'instagram': 'Instagram',
        'tiktok': 'TikTok',
        'youtube': 'YouTube',
        'google': 'Google Search',
        'whatsapp': 'WhatsApp',
        'old_system': 'Nilitumia Mfumo Mwingine',
        'invited': 'Nimealikwa',
        'advertisement': 'Tangazo',
        'website': 'Website',
        'customer_referral': 'Mteja Aliyenielekeza',
        'event': 'Event / Maonesho',
        'other': 'Nyingine'
    };

    // Define business type labels as JavaScript object
    const businessLabelsData = {
        'retail_shop': 'Retail Shop / Duka',
        'mini_market': 'Mini Market',
        'supermarket': 'Supermarket',
        'pharmacy': 'Pharmacy / Dawa',
        'hardware': 'Hardware',
        'stationery': 'Stationery',
        'restaurant': 'Restaurant',
        'hotel': 'Hotel',
        'bar': 'Bar / Vinywaji',
        'clothes_shop': 'Duka la Nguo',
        'shoes_shop': 'Duka la Viatu',
        'furniture': 'Furniture',
        'cosmetics': 'Cosmetics',
        'electronics': 'Electronics',
        'salon': 'Salon / Kinyozi',
        'spare_parts': 'Spare Parts',
        'wholesale': 'Jumla / Wholesale',
        'bakery': 'Bakery',
        'grocery': 'Grocery',
        'other': 'Nyingine'
    };

    function companyStatistics() {
        return {
            // Tab state
            activeTab: 'sources',
            loading: false,
            error: false,
            errorMessage: '',
            
            // Sources data
            sourceCompanies: [],
            filteredSourceCompanies: [],
            sourceFilter: '',
            sourceSearch: '',
            sourceSortField: 'created_at',
            sourceSortDirection: 'desc',
            sourceCurrentPage: 1,
            sourceItemsPerPage: 10,
            sourcesSummary: [],
            
            // Business types data
            businessCompanies: [],
            filteredBusinessCompanies: [],
            businessTypeFilter: '',
            businessSearch: '',
            businessSortField: 'created_at',
            businessSortDirection: 'desc',
            businessCurrentPage: 1,
            businessItemsPerPage: 10,
            businessSummary: [],
            
            // Labels
            sourceLabels: sourceLabelsData,
            businessLabels: businessLabelsData,
            
            init() {
                console.log('Initializing...');
                this.fetchSourcesData();
                this.fetchBusinessData();
            },
            
            fetchSourcesData() {
                this.loading = true;
                this.error = false;
                
                console.log('Fetching sources data from /admin/api/companies-data');
                
                fetch('/admin/api/companies-data', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Sources data received:', data);
                        if (data && data.companies) {
                            this.sourceCompanies = data.companies.map(c => ({
                                ...c,
                                hear_about_us_label: this.sourceLabels[c.hear_about_us] || c.hear_about_us || '-'
                            }));
                            this.updateSourcesSummary();
                            this.applySourceFilters();
                        } else {
                            this.sourceCompanies = [];
                            this.sourcesSummary = [];
                        }
                        this.loading = false;
                    })
                    .catch(error => {
                        console.error('Error fetching sources:', error);
                        this.error = true;
                        this.errorMessage = 'Imeshindwa kupakia data. Tafadhali hakikisha umeingia kama Admin.';
                        this.loading = false;
                    });
            },
            
            fetchBusinessData() {
                fetch('/admin/api/companies-data', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Business data received:', data);
                        if (data && data.companies) {
                            this.businessCompanies = data.companies.map(c => ({
                                ...c,
                                business_type_label: this.businessLabels[c.business_type] || c.business_type || '-'
                            }));
                            this.updateBusinessSummary();
                            this.applyBusinessFilters();
                        } else {
                            this.businessCompanies = [];
                            this.businessSummary = [];
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching business types:', error);
                    });
            },
            
            updateSourcesSummary() {
                const summary = {};
                this.sourceCompanies.forEach(company => {
                    const source = company.hear_about_us || 'other';
                    if (!summary[source]) {
                        summary[source] = { count: 0, name: source, label: this.sourceLabels[source] || source };
                    }
                    summary[source].count++;
                });
                const total = this.sourceCompanies.length;
                this.sourcesSummary = Object.values(summary)
                    .map(s => ({ ...s, percentage: total > 0 ? ((s.count / total) * 100).toFixed(1) : '0' }))
                    .sort((a, b) => b.count - a.count);
            },
            
            updateBusinessSummary() {
                const summary = {};
                this.businessCompanies.forEach(company => {
                    const type = company.business_type || 'other';
                    if (!summary[type]) {
                        summary[type] = { count: 0, name: type, label: this.businessLabels[type] || type };
                    }
                    summary[type].count++;
                });
                const total = this.businessCompanies.length;
                this.businessSummary = Object.values(summary)
                    .map(t => ({ ...t, percentage: total > 0 ? ((t.count / total) * 100).toFixed(1) : '0' }))
                    .sort((a, b) => b.count - a.count);
            },
            
            applySourceFilters() {
                let filtered = [...this.sourceCompanies];
                
                if (this.sourceFilter) {
                    filtered = filtered.filter(c => (c.hear_about_us || 'other') === this.sourceFilter);
                }
                
                if (this.sourceSearch) {
                    const search = this.sourceSearch.toLowerCase();
                    filtered = filtered.filter(c => 
                        c.company_name.toLowerCase().includes(search) ||
                        (c.owner_name && c.owner_name.toLowerCase().includes(search))
                    );
                }
                
                filtered.sort((a, b) => {
                    let aVal = a[this.sourceSortField];
                    let bVal = b[this.sourceSortField];
                    if (this.sourceSortField === 'created_at') {
                        aVal = new Date(aVal);
                        bVal = new Date(bVal);
                    }
                    if (aVal < bVal) return this.sourceSortDirection === 'asc' ? -1 : 1;
                    if (aVal > bVal) return this.sourceSortDirection === 'asc' ? 1 : -1;
                    return 0;
                });
                
                this.filteredSourceCompanies = filtered;
                this.sourceCurrentPage = 1;
            },
            
            applyBusinessFilters() {
                let filtered = [...this.businessCompanies];
                
                if (this.businessTypeFilter) {
                    filtered = filtered.filter(c => (c.business_type || 'other') === this.businessTypeFilter);
                }
                
                if (this.businessSearch) {
                    const search = this.businessSearch.toLowerCase();
                    filtered = filtered.filter(c => 
                        c.company_name.toLowerCase().includes(search) ||
                        (c.owner_name && c.owner_name.toLowerCase().includes(search))
                    );
                }
                
                filtered.sort((a, b) => {
                    let aVal = a[this.businessSortField];
                    let bVal = b[this.businessSortField];
                    if (this.businessSortField === 'created_at') {
                        aVal = new Date(aVal);
                        bVal = new Date(bVal);
                    }
                    if (aVal < bVal) return this.businessSortDirection === 'asc' ? -1 : 1;
                    if (aVal > bVal) return this.businessSortDirection === 'asc' ? 1 : -1;
                    return 0;
                });
                
                this.filteredBusinessCompanies = filtered;
                this.businessCurrentPage = 1;
            },
            
            filterBySource(sourceName) {
                this.sourceFilter = sourceName;
                this.applySourceFilters();
            },
            
            filterByBusinessType(typeName) {
                this.businessTypeFilter = typeName;
                this.applyBusinessFilters();
            },
            
            resetSourceFilters() {
                this.sourceFilter = '';
                this.sourceSearch = '';
                this.sourceSortField = 'created_at';
                this.sourceSortDirection = 'desc';
                this.applySourceFilters();
            },
            
            resetBusinessFilters() {
                this.businessTypeFilter = '';
                this.businessSearch = '';
                this.businessSortField = 'created_at';
                this.businessSortDirection = 'desc';
                this.applyBusinessFilters();
            },
            
            sortSourcesBy(field) {
                if (this.sourceSortField === field) {
                    this.sourceSortDirection = this.sourceSortDirection === 'asc' ? 'desc' : 'asc';
                } else {
                    this.sourceSortField = field;
                    this.sourceSortDirection = 'asc';
                }
                this.applySourceFilters();
            },
            
            sortBusinessBy(field) {
                if (this.businessSortField === field) {
                    this.businessSortDirection = this.businessSortDirection === 'asc' ? 'desc' : 'asc';
                } else {
                    this.businessSortField = field;
                    this.businessSortDirection = 'asc';
                }
                this.applyBusinessFilters();
            },
            
            get paginatedSourceCompanies() {
                const start = (this.sourceCurrentPage - 1) * this.sourceItemsPerPage;
                return this.filteredSourceCompanies.slice(start, start + this.sourceItemsPerPage);
            },
            
            get sourceTotalPages() {
                return Math.ceil(this.filteredSourceCompanies.length / this.sourceItemsPerPage);
            },
            
            get paginatedBusinessCompanies() {
                const start = (this.businessCurrentPage - 1) * this.businessItemsPerPage;
                return this.filteredBusinessCompanies.slice(start, start + this.businessItemsPerPage);
            },
            
            get businessTotalPages() {
                return Math.ceil(this.filteredBusinessCompanies.length / this.businessItemsPerPage);
            },
            
            getSourceColorClass(source) {
                const colors = {
                    'facebook': 'bg-blue-100 text-blue-700',
                    'instagram': 'bg-pink-100 text-pink-700',
                    'whatsapp': 'bg-green-100 text-green-700',
                    'friend': 'bg-purple-100 text-purple-700',
                    'google': 'bg-red-100 text-red-700',
                    'tiktok': 'bg-gray-100 text-gray-700',
                    'youtube': 'bg-red-100 text-red-700'
                };
                return colors[source] || 'bg-amber-100 text-amber-700';
            },
            
            getBusinessTypeColorClass(type) {
                const colors = {
                    'pharmacy': 'bg-blue-100 text-blue-700',
                    'supermarket': 'bg-emerald-100 text-emerald-700',
                    'restaurant': 'bg-amber-100 text-amber-700',
                    'hardware': 'bg-gray-100 text-gray-700',
                    'salon': 'bg-pink-100 text-pink-700',
                    'electronics': 'bg-purple-100 text-purple-700'
                };
                return colors[type] || 'bg-emerald-100 text-emerald-700';
            },
            
            formatDate(dateString) {
                if (!dateString) return '';
                const date = new Date(dateString);
                return date.toLocaleDateString('sw-TZ', { day: '2-digit', month: '2-digit', year: 'numeric' });
            },
            
            exportSourcesData() {
                const headers = ['Jina la Kampuni', 'Jina la Mmiliki', 'Simu', 'Barua Pepe', 'Chanzo', 'Tarehe ya Usajili'];
                const rows = this.filteredSourceCompanies.map(c => [
                    c.company_name, c.owner_name || '', c.phone || '', c.email || '',
                    this.sourceLabels[c.hear_about_us] || c.hear_about_us || '-',
                    this.formatDate(c.created_at)
                ]);
                this.downloadCSV(headers, rows, 'vyanzo_vya_usajili');
            },
            
            exportBusinessData() {
                const headers = ['Jina la Kampuni', 'Jina la Mmiliki', 'Simu', 'Barua Pepe', 'Aina ya Biashara', 'Tarehe ya Usajili'];
                const rows = this.filteredBusinessCompanies.map(c => [
                    c.company_name, c.owner_name || '', c.phone || '', c.email || '',
                    this.businessLabels[c.business_type] || c.business_type || '-',
                    this.formatDate(c.created_at)
                ]);
                this.downloadCSV(headers, rows, 'aina_za_biashara');
            },
            
            downloadCSV(headers, rows, filename) {
                const csvContent = [headers, ...rows].map(row => row.map(cell => `"${String(cell).replace(/"/g, '""')}"`).join(',')).join('\n');
                const blob = new Blob(["\uFEFF" + csvContent], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement('a');
                const url = URL.createObjectURL(blob);
                link.setAttribute('href', url);
                link.setAttribute('download', `${filename}_${new Date().toISOString().split('T')[0]}.csv`);
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                URL.revokeObjectURL(url);
            }
        }
    }
</script>
@endpush