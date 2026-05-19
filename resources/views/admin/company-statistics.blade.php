{{-- resources/views/admin/company-statistics.blade.php --}}
@extends('layouts.admin')

@section('title', 'Takwimu za Makampuni')
@section('page-title', 'Takwimu za Makampuni')
@section('page-subtitle', 'Angalia takwimu za makampuni kwa vyanzo na aina za biashara')

@section('content')
<div class="min-h-screen" x-data="companyStatistics()" x-init="init()">
    <!-- Loading Overlay -->
    <div x-show="loading" x-cloak class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-6 flex flex-col items-center gap-3 shadow-2xl">
            <div class="w-12 h-12 border-4 border-emerald-200 border-t-emerald-600 rounded-full animate-spin"></div>
            <span class="text-gray-700 font-medium">Inapakia data...</span>
        </div>
    </div>

    <!-- Error Alert -->
    <div x-show="error" x-cloak class="mb-6 bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-500 rounded-xl p-4 shadow-sm">
        <div class="flex items-start gap-3">
            <i class="fas fa-exclamation-triangle text-red-500 mt-0.5"></i>
            <div class="flex-1">
                <p class="text-red-700 font-medium" x-text="errorMessage"></p>
                <button @click="fetchSourcesData(); fetchBusinessData()" class="mt-2 text-sm text-red-600 hover:text-red-800 font-medium">
                    <i class="fas fa-redo-alt mr-1"></i> Jaribu tena
                </button>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <!-- Tab Buttons - Modern Design -->
        <div class="grid grid-cols-2 gap-2 p-3 bg-gray-50/50 border-b border-gray-100">
            <button 
                @click="activeTab = 'sources'; fetchSourcesData()"
                :class="activeTab === 'sources' 
                    ? 'bg-white text-emerald-700 shadow-md border-emerald-200' 
                    : 'text-gray-600 hover:bg-gray-100 border-transparent'"
                class="relative py-3 px-4 rounded-xl font-semibold text-sm sm:text-base transition-all duration-300 border">
                <div class="flex items-center justify-center gap-2">
                    <i class="fas fa-chart-line text-base"></i>
                    <span>Vyanzo vya Usajili</span>
                    <span x-show="activeTab === 'sources'" class="absolute -top-1 -right-1 w-2 h-2 bg-emerald-500 rounded-full"></span>
                </div>
            </button>
            <button 
                @click="activeTab = 'businessTypes'; fetchBusinessData()"
                :class="activeTab === 'businessTypes' 
                    ? 'bg-white text-emerald-700 shadow-md border-emerald-200' 
                    : 'text-gray-600 hover:bg-gray-100 border-transparent'"
                class="relative py-3 px-4 rounded-xl font-semibold text-sm sm:text-base transition-all duration-300 border">
                <div class="flex items-center justify-center gap-2">
                    <i class="fas fa-chart-pie text-base"></i>
                    <span>Aina za Biashara</span>
                    <span x-show="activeTab === 'businessTypes'" class="absolute -top-1 -right-1 w-2 h-2 bg-emerald-500 rounded-full"></span>
                </div>
            </button>
        </div>

        <!-- SOURCES TAB -->
        <div x-show="activeTab === 'sources'" x-cloak>
            <!-- Stats Grid - Responsive Cards -->
            <div class="p-4 sm:p-6 border-b border-gray-100 bg-gradient-to-br from-amber-50/30 to-transparent">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-chart-simple text-emerald-600"></i>
                            Muhtasari wa Vyanzo
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">Jumla ya makampuni: <span x-text="sourceCompanies.length" class="font-bold text-emerald-600"></span></p>
                    </div>
                </div>
                
                <!-- Responsive Stats Cards -->
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-3">
                    <template x-for="source in sourcesSummary" :key="source.name">
                        <div 
                            @click="filterBySource(source.name)"
                            class="group cursor-pointer bg-white rounded-xl p-4 border-2 transition-all duration-300 hover:shadow-lg hover:-translate-y-1"
                            :class="sourceFilter === source.name ? 'border-emerald-500 bg-emerald-50/50 shadow-md' : 'border-gray-200 hover:border-emerald-300'">
                            <div class="flex items-start justify-between mb-2">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center" 
                                    :class="sourceFilter === source.name ? 'bg-emerald-500' : 'bg-gradient-to-br from-amber-400 to-orange-400 group-hover:scale-110 transition-transform'">
                                    <i class="fas fa-chart-simple text-white text-sm"></i>
                                </div>
                                <span class="text-sm font-bold" :class="sourceFilter === source.name ? 'text-emerald-600' : 'text-gray-500'" x-text="source.percentage + '%'"></span>
                            </div>
                            <p class="text-xs text-gray-600 font-medium truncate mb-1" x-text="source.label"></p>
                            <p class="text-2xl font-bold text-gray-800" x-text="source.count"></p>
                        </div>
                    </template>
                    <div x-show="sourcesSummary.length === 0 && !loading" class="col-span-full text-center py-8 text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2 block"></i>
                        Hakuna data ya vyanzo
                    </div>
                </div>
            </div>

            <!-- Filter Section - Mobile Optimized -->
            <div class="p-4 sm:p-6 bg-gray-50/50 border-b border-gray-100">
                <!-- Mobile Filter Toggle -->
                <div class="sm:hidden mb-3">
                    <button @click="showMobileFilters = !showMobileFilters" class="w-full py-2 px-4 bg-white rounded-lg border border-gray-300 text-gray-700 font-medium">
                        <i class="fas fa-filter mr-2"></i>
                        <span x-text="showMobileFilters ? 'Funga Vichujio' : 'Fungua Vichujio'"></span>
                    </button>
                </div>
                
                <!-- Filters Container -->
                <div x-show="showMobileFilters" x-cloak class="sm:!block space-y-3">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">Chagua Chanzo</label>
                            <select x-model="sourceFilter" @change="applySourceFilters()" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:border-transparent bg-white">
                                <option value="">✅ Vyanzo Vyote</option>
                                <template x-for="source in sourcesSummary" :key="source.name">
                                    <option :value="source.name" x-text="source.label + ' (' + source.count + ')'"></option>
                                </template>
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">Tafuta</label>
                            <div class="relative">
                                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                <input type="text" x-model="sourceSearch" @input="applySourceFilters()" placeholder="Jina la kampuni au mmiliki..." 
                                    class="w-full rounded-lg border border-gray-300 pl-10 pr-3 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                            </div>
                        </div>
                        <div class="flex gap-2 items-end">
                            <button @click="resetSourceFilters" class="flex-1 px-4 py-2.5 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition font-medium">
                                <i class="fas fa-undo-alt mr-1"></i> Weka Upya
                            </button>
                            <button @click="exportSourcesData" class="flex-1 px-4 py-2.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition font-medium">
                                <i class="fas fa-download mr-1"></i> Pakua
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Section - Responsive -->
            <div class="overflow-x-auto">
                <!-- Desktop Table -->
                <div class="hidden md:block">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b-2 border-gray-200 sticky top-0">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">#</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition" @click="sortSourcesBy('company_name')">
                                    Kampuni <i class="fas fa-sort ml-1 text-gray-400"></i>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition" @click="sortSourcesBy('owner_name')">
                                    Mmiliki <i class="fas fa-sort ml-1 text-gray-400"></i>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Chanzo</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition" @click="sortSourcesBy('created_at')">
                                    Tarehe <i class="fas fa-sort ml-1 text-gray-400"></i>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Simu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="(company, index) in paginatedSourceCompanies" :key="company.id">
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 text-gray-500 text-center" x-text="(sourceCurrentPage - 1) * sourceItemsPerPage + index + 1"></td>
                                    <td class="px-6 py-4 font-semibold text-gray-800" x-text="company.company_name"></td>
                                    <td class="px-6 py-4 text-gray-600" x-text="company.owner_name || '-'"></td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-medium" :class="getSourceColorClass(company.hear_about_us)" x-text="company.hear_about_us_label"></span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-500 text-xs" x-text="formatDate(company.created_at)"></td>
                                    <td class="px-6 py-4 text-gray-500 text-sm" x-text="company.phone || '-'"></td>
                                </tr>
                            </template>
                            <tr x-show="filteredSourceCompanies.length === 0 && !loading">
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-search text-4xl mb-3 block"></i>
                                    <p>Hakuna makampuni yanayolingana na vigezo vyako</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards View -->
                <div class="block md:hidden divide-y divide-gray-100">
                    <template x-for="(company, index) in paginatedSourceCompanies" :key="company.id">
                        <div class="p-4 hover:bg-gray-50 transition-colors">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex-1">
                                    <h4 class="font-bold text-gray-800 text-base" x-text="company.company_name"></h4>
                                    <p class="text-sm text-gray-600 mt-1" x-text="company.owner_name || 'Mmiliki hajaainishwa'"></p>
                                </div>
                                <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium" :class="getSourceColorClass(company.hear_about_us)" x-text="company.hear_about_us_label"></span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 mt-3 text-sm">
                                <div>
                                    <span class="text-gray-500 text-xs">Simu:</span>
                                    <p class="text-gray-700" x-text="company.phone || '-'"></p>
                                </div>
                                <div>
                                    <span class="text-gray-500 text-xs">Tarehe:</span>
                                    <p class="text-gray-700 text-xs" x-text="formatDate(company.created_at)"></p>
                                </div>
                            </div>
                        </div>
                    </template>
                    <div x-show="filteredSourceCompanies.length === 0 && !loading" class="p-8 text-center text-gray-500">
                        <i class="fas fa-search text-3xl mb-2 block"></i>
                        <p>Hakuna makampuni</p>
                    </div>
                </div>
            </div>

            <!-- Pagination - Optimized -->
            <div class="p-4 sm:p-6 border-t border-gray-200 bg-gray-50/50" x-show="filteredSourceCompanies.length > 0">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="text-sm text-gray-600 text-center sm:text-left">
                        Inaonyesha <span x-text="((sourceCurrentPage - 1) * sourceItemsPerPage) + 1"></span> - 
                        <span x-text="Math.min(sourceCurrentPage * sourceItemsPerPage, filteredSourceCompanies.length)"></span> 
                        kati ya <span x-text="filteredSourceCompanies.length"></span>
                    </div>
                    <div class="flex items-center justify-center gap-2">
                        <button @click="sourceCurrentPage--" :disabled="sourceCurrentPage === 1" 
                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed hover:bg-white transition">
                            <i class="fas fa-chevron-left mr-1"></i> Awali
                        </button>
                        <div class="flex gap-1">
                            <span class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-bold" x-text="sourceCurrentPage"></span>
                            <span class="px-4 py-2 text-gray-600 text-sm">/</span>
                            <span class="px-4 py-2 text-gray-600 text-sm" x-text="sourceTotalPages"></span>
                        </div>
                        <button @click="sourceCurrentPage++" :disabled="sourceCurrentPage >= sourceTotalPages" 
                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed hover:bg-white transition">
                            Ifuatayo <i class="fas fa-chevron-right ml-1"></i>
                        </button>
                    </div>
                    <div class="flex justify-center sm:justify-end">
                        <select x-model="sourceItemsPerPage" @change="sourceCurrentPage = 1" class="px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white">
                            <option value="10">10 kwa ukurasa</option>
                            <option value="25">25 kwa ukurasa</option>
                            <option value="50">50 kwa ukurasa</option>
                            <option value="100">100 kwa ukurasa</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- BUSINESS TYPES TAB - Similar responsive structure -->
        <div x-show="activeTab === 'businessTypes'" x-cloak>
            <!-- Business Stats Grid -->
            <div class="p-4 sm:p-6 border-b border-gray-100 bg-gradient-to-br from-emerald-50/30 to-transparent">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-store text-emerald-600"></i>
                            Muhtasari wa Aina za Biashara
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">Jumla ya makampuni: <span x-text="businessCompanies.length" class="font-bold text-emerald-600"></span></p>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
                    <template x-for="type in businessSummary" :key="type.name">
                        <div 
                            @click="filterByBusinessType(type.name)"
                            class="group cursor-pointer bg-white rounded-xl p-4 border-2 transition-all duration-300 hover:shadow-lg hover:-translate-y-1"
                            :class="businessTypeFilter === type.name ? 'border-emerald-500 bg-emerald-50/50 shadow-md' : 'border-gray-200 hover:border-emerald-300'">
                            <div class="flex items-start justify-between mb-2">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center" 
                                    :class="businessTypeFilter === type.name ? 'bg-emerald-500' : 'bg-gradient-to-br from-emerald-400 to-green-400 group-hover:scale-110 transition-transform'">
                                    <i class="fas fa-store text-white text-sm"></i>
                                </div>
                                <span class="text-sm font-bold" :class="businessTypeFilter === type.name ? 'text-emerald-600' : 'text-gray-500'" x-text="type.percentage + '%'"></span>
                            </div>
                            <p class="text-xs text-gray-600 font-medium truncate mb-1" x-text="type.label"></p>
                            <p class="text-2xl font-bold text-gray-800" x-text="type.count"></p>
                        </div>
                    </template>
                    <div x-show="businessSummary.length === 0 && !loading" class="col-span-full text-center py-8 text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2 block"></i>
                        Hakuna data ya aina za biashara
                    </div>
                </div>
            </div>

            <!-- Business Filters -->
            <div class="p-4 sm:p-6 bg-gray-50/50 border-b border-gray-100">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">Chagua Aina</label>
                        <select x-model="businessTypeFilter" @change="applyBusinessFilters()" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 focus:ring-2 focus:ring-emerald-500 bg-white">
                            <option value="">✅ Aina Zote</option>
                            <template x-for="type in businessSummary" :key="type.name">
                                <option :value="type.name" x-text="type.label + ' (' + type.count + ')'"></option>
                            </template>
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">Tafuta</label>
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" x-model="businessSearch" @input="applyBusinessFilters()" placeholder="Jina la kampuni au mmiliki..." 
                                class="w-full rounded-lg border border-gray-300 pl-10 pr-3 py-2.5 focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>
                    <div class="flex gap-2 items-end">
                        <button @click="resetBusinessFilters" class="flex-1 px-4 py-2.5 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition font-medium">
                            <i class="fas fa-undo-alt mr-1"></i> Weka Upya
                        </button>
                        <button @click="exportBusinessData" class="flex-1 px-4 py-2.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition font-medium">
                            <i class="fas fa-download mr-1"></i> Pakua
                        </button>
                    </div>
                </div>
            </div>

            <!-- Business Table/Mobile View -->
            <div class="overflow-x-auto">
                <div class="hidden md:block">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b-2 border-gray-200">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">#</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase cursor-pointer hover:bg-gray-100" @click="sortBusinessBy('company_name')">Kampuni</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase cursor-pointer hover:bg-gray-100" @click="sortBusinessBy('owner_name')">Mmiliki</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Aina</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase cursor-pointer" @click="sortBusinessBy('created_at')">Tarehe</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Simu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="(company, index) in paginatedBusinessCompanies" :key="company.id">
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-gray-500" x-text="(businessCurrentPage - 1) * businessItemsPerPage + index + 1"></td>
                                    <td class="px-6 py-4 font-semibold text-gray-800" x-text="company.company_name"></td>
                                    <td class="px-6 py-4 text-gray-600" x-text="company.owner_name || '-'"></td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-medium" :class="getBusinessTypeColorClass(company.business_type)" x-text="company.business_type_label"></span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-500 text-xs" x-text="formatDate(company.created_at)"></td>
                                    <td class="px-6 py-4 text-gray-500" x-text="company.phone || '-'"></td>
                                </tr>
                            </template>
                            <tr x-show="filteredBusinessCompanies.length === 0 && !loading">
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-search text-4xl mb-3 block"></i>
                                    <p>Hakuna makampuni yanayolingana</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards for Business -->
                <div class="block md:hidden divide-y divide-gray-100">
                    <template x-for="(company, index) in paginatedBusinessCompanies" :key="company.id">
                        <div class="p-4 hover:bg-gray-50">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex-1">
                                    <h4 class="font-bold text-gray-800" x-text="company.company_name"></h4>
                                    <p class="text-sm text-gray-600 mt-1" x-text="company.owner_name || 'Mmiliki hajaainishwa'"></p>
                                </div>
                                <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium" :class="getBusinessTypeColorClass(company.business_type)" x-text="company.business_type_label"></span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 mt-3 text-sm">
                                <div>
                                    <span class="text-gray-500 text-xs">Simu:</span>
                                    <p class="text-gray-700" x-text="company.phone || '-'"></p>
                                </div>
                                <div>
                                    <span class="text-gray-500 text-xs">Tarehe:</span>
                                    <p class="text-gray-700 text-xs" x-text="formatDate(company.created_at)"></p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Business Pagination -->
            <div class="p-4 sm:p-6 border-t border-gray-200 bg-gray-50/50" x-show="filteredBusinessCompanies.length > 0">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="text-sm text-gray-600 text-center sm:text-left">
                        Inaonyesha <span x-text="((businessCurrentPage - 1) * businessItemsPerPage) + 1"></span> - 
                        <span x-text="Math.min(businessCurrentPage * businessItemsPerPage, filteredBusinessCompanies.length)"></span> 
                        kati ya <span x-text="filteredBusinessCompanies.length"></span>
                    </div>
                    <div class="flex items-center justify-center gap-2">
                        <button @click="businessCurrentPage--" :disabled="businessCurrentPage === 1" 
                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm disabled:opacity-50 hover:bg-white transition">
                            <i class="fas fa-chevron-left mr-1"></i> Awali
                        </button>
                        <div class="flex gap-1">
                            <span class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm" x-text="businessCurrentPage"></span>
                            <span class="px-4 py-2 text-gray-600 text-sm">/</span>
                            <span class="px-4 py-2 text-gray-600 text-sm" x-text="businessTotalPages"></span>
                        </div>
                        <button @click="businessCurrentPage++" :disabled="businessCurrentPage >= businessTotalPages" 
                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm disabled:opacity-50 hover:bg-white transition">
                            Ifuatayo <i class="fas fa-chevron-right ml-1"></i>
                        </button>
                    </div>
                    <div class="flex justify-center sm:justify-end">
                        <select x-model="businessItemsPerPage" @change="businessCurrentPage = 1" class="px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white">
                            <option value="10">10 kwa ukurasa</option>
                            <option value="25">25 kwa ukurasa</option>
                            <option value="50">50 kwa ukurasa</option>
                            <option value="100">100 kwa ukurasa</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
    .transition-all { transition-property: all; }
    .duration-300 { transition-duration: 300ms; }
</style>
@endsection

@push('scripts')
<script>
    const sourceLabelsData = {
        'friend': 'Rafiki', 'social_media': 'Social Media', 'facebook': 'Facebook',
        'instagram': 'Instagram', 'tiktok': 'TikTok', 'youtube': 'YouTube',
        'google': 'Google Search', 'whatsapp': 'WhatsApp', 'old_system': 'Mfumo Mwingine',
        'invited': 'Nimealikwa', 'advertisement': 'Tangazo', 'website': 'Website',
        'customer_referral': 'Mteja Aliyenielekeza', 'event': 'Event / Maonesho', 'other': 'Nyingine'
    };

    const businessLabelsData = {
        'retail_shop': 'Duka', 'mini_market': 'Mini Market', 'supermarket': 'Supermarket',
        'pharmacy': 'Dawa', 'hardware': 'Hardware', 'stationery': 'Stationery',
        'restaurant': 'Restaurant', 'hotel': 'Hotel', 'bar': 'Bar',
        'clothes_shop': 'Nguo', 'shoes_shop': 'Viatu', 'furniture': 'Samani',
        'cosmetics': 'Cosmetics', 'electronics': 'Electronics', 'salon': 'Salon',
        'spare_parts': 'Spare Parts', 'wholesale': 'Jumla', 'bakery': 'Mkate',
        'grocery': 'Grocery', 'other': 'Nyingine'
    };

    function companyStatistics() {
        return {
            activeTab: 'sources',
            loading: false,
            error: false,
            errorMessage: '',
            showMobileFilters: false,
            
            sourceCompanies: [],
            filteredSourceCompanies: [],
            sourceFilter: '',
            sourceSearch: '',
            sourceSortField: 'created_at',
            sourceSortDirection: 'desc',
            sourceCurrentPage: 1,
            sourceItemsPerPage: 10,
            sourcesSummary: [],
            
            businessCompanies: [],
            filteredBusinessCompanies: [],
            businessTypeFilter: '',
            businessSearch: '',
            businessSortField: 'created_at',
            businessSortDirection: 'desc',
            businessCurrentPage: 1,
            businessItemsPerPage: 10,
            businessSummary: [],
            
            sourceLabels: sourceLabelsData,
            businessLabels: businessLabelsData,
            
            init() {
                this.fetchSourcesData();
                this.fetchBusinessData();
            },
            
            async fetchSourcesData() {
                this.loading = true;
                this.error = false;
                
                try {
                    const response = await fetch('/admin/api/companies-data', {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        credentials: 'same-origin'
                    });
                    
                    if (!response.ok) throw new Error(`HTTP ${response.status}`);
                    
                    const data = await response.json();
                    if (data && data.companies) {
                        this.sourceCompanies = data.companies.map(c => ({
                            ...c,
                            hear_about_us_label: this.sourceLabels[c.hear_about_us] || c.hear_about_us || '-'
                        }));
                        this.updateSourcesSummary();
                        this.applySourceFilters();
                    }
                } catch (error) {
                    this.error = true;
                    this.errorMessage = 'Imeshindwa kupakia data. Tafadhali hakikisha umeingia kama Admin.';
                } finally {
                    this.loading = false;
                }
            },
            
            async fetchBusinessData() {
                try {
                    const response = await fetch('/admin/api/companies-data', {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        credentials: 'same-origin'
                    });
                    
                    const data = await response.json();
                    if (data && data.companies) {
                        this.businessCompanies = data.companies.map(c => ({
                            ...c,
                            business_type_label: this.businessLabels[c.business_type] || c.business_type || '-'
                        }));
                        this.updateBusinessSummary();
                        this.applyBusinessFilters();
                    }
                } catch (error) {
                    console.error('Error fetching business types:', error);
                }
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
                if (this.sourceFilter) filtered = filtered.filter(c => (c.hear_about_us || 'other') === this.sourceFilter);
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
                if (this.businessTypeFilter) filtered = filtered.filter(c => (c.business_type || 'other') === this.businessTypeFilter);
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
                    'google': 'bg-red-100 text-red-700'
                };
                return colors[source] || 'bg-amber-100 text-amber-700';
            },
            
            getBusinessTypeColorClass(type) {
                const colors = {
                    'pharmacy': 'bg-blue-100 text-blue-700',
                    'supermarket': 'bg-emerald-100 text-emerald-700',
                    'restaurant': 'bg-amber-100 text-amber-700',
                    'hardware': 'bg-gray-100 text-gray-700',
                    'salon': 'bg-pink-100 text-pink-700'
                };
                return colors[type] || 'bg-emerald-100 text-emerald-700';
            },
            
            formatDate(dateString) {
                if (!dateString) return '';
                const date = new Date(dateString);
                return date.toLocaleDateString('sw-TZ', { day: '2-digit', month: '2-digit', year: 'numeric' });
            },
            
            exportSourcesData() {
                const headers = ['Jina la Kampuni', 'Jina la Mmiliki', 'Simu', 'Barua Pepe', 'Chanzo', 'Tarehe'];
                const rows = this.filteredSourceCompanies.map(c => [
                    c.company_name, c.owner_name || '', c.phone || '', c.email || '',
                    this.sourceLabels[c.hear_about_us] || c.hear_about_us || '-',
                    this.formatDate(c.created_at)
                ]);
                this.downloadCSV(headers, rows, 'vyanzo_vya_usajili');
            },
            
            exportBusinessData() {
                const headers = ['Jina la Kampuni', 'Jina la Mmiliki', 'Simu', 'Barua Pepe', 'Aina ya Biashara', 'Tarehe'];
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
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                URL.revokeObjectURL(url);
            }
        }
    }
</script>
@endpush