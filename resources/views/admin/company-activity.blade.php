{{-- resources/views/admin/company-activity.blade.php --}}
@extends('layouts.admin')

@section('title', 'Shughuli za Makampuni')
@section('page-title', 'Shughuli za Makampuni')
@section('page-subtitle', 'Fuatilia shughuli na hali ya makampuni kwa wakati halisi')

@section('content')
<div class="min-h-screen bg-gray-50/30">
 
    <!-- Success Notification -->
    @if(session('success'))
    <div id="success-notification" class="fixed top-4 md:top-6 left-1/2 transform -translate-x-1/2 z-50 animate-slide-down w-[95%] md:max-w-md">
        <div class="bg-white border border-emerald-200 rounded-xl shadow-xl md:shadow-2xl px-4 py-3 md:px-6 md:py-4">
            <div class="flex items-center space-x-2 md:space-x-3">
                <div class="flex-shrink-0">
                    <div class="w-6 h-6 md:w-8 md:h-8 bg-emerald-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check text-emerald-600 text-xs md:text-sm"></i>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-900 text-sm md:text-base">Imefanikiwa!</p>
                    <p class="text-xs md:text-sm text-gray-600 truncate">{{ session('success') }}</p>
                </div>
                <button onclick="closeNotification()" class="text-gray-400 hover:text-gray-600 transition-colors flex-shrink-0">
                    <i class="fas fa-times text-sm md:text-base"></i>
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6 mb-4 md:mb-6 px-4 md:px-0">
        <!-- Active Companies -->
        <div class="bg-white rounded-lg md:rounded-2xl shadow-sm border border-gray-100 p-3 md:p-6 hover-lift transition-all duration-200">
            <div class="flex justify-between items-start mb-2 md:mb-4">
                <div class="flex-1">
                    <p class="text-xs md:text-sm font-semibold text-gray-600 mb-1 md:mb-2">Kampuni Active</p>
                    <p class="text-lg md:text-2xl font-bold text-gray-900">{{ $stats['active_companies'] ?? 0 }}</p>
                </div>
                <div class="flex-shrink-0">
                    <span class="text-xs font-semibold px-2 py-0.5 md:px-3 md:py-1 rounded-full bg-green-100 text-green-800">
                        {{ $stats['active_percentage'] ?? 0 }}%
                    </span>
                </div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-1.5 md:h-2">
                <div class="h-1.5 md:h-2 rounded-full transition-all duration-1000 bg-green-500" 
                    style="width: {{ $stats['active_percentage'] ?? 0 }}%">
                </div>
            </div>
            <div class="mt-2 md:mt-3 text-xs text-gray-500">
                <i class="fas fa-clock mr-1"></i> Sasa hivi
            </div>
        </div>

        <!-- Inactive Companies -->
        <div class="bg-white rounded-lg md:rounded-2xl shadow-sm border border-gray-100 p-3 md:p-6 hover-lift transition-all duration-200">
            <div class="flex justify-between items-start mb-2 md:mb-4">
                <div class="flex-1">
                    <p class="text-xs md:text-sm font-semibold text-gray-600 mb-1 md:mb-2">Kampuni Inactive</p>
                    <p class="text-lg md:text-2xl font-bold text-gray-900">{{ $stats['inactive_companies'] ?? 0 }}</p>
                </div>
                <div class="flex-shrink-0">
                    <span class="text-xs font-semibold px-2 py-0.5 md:px-3 md:py-1 rounded-full bg-gray-100 text-gray-800">
                        {{ round(($stats['inactive_companies'] ?? 0) / max(($stats['total_companies'] ?? 1), 1) * 100) }}%
                    </span>
                </div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-1.5 md:h-2">
                <div class="h-1.5 md:h-2 rounded-full transition-all duration-1000 bg-gray-400" 
                    style="width: {{ (($stats['inactive_companies'] ?? 0) / max(($stats['total_companies'] ?? 1), 1)) * 100 }}%">
                </div>
            </div>
            <div class="mt-2 md:mt-3 text-xs text-gray-500">
                <i class="fas fa-clock mr-1"></i> Dakika 10+
            </div>
        </div>

        <!-- Active Users -->
        <div class="bg-white rounded-lg md:rounded-2xl shadow-sm border border-gray-100 p-3 md:p-6 hover-lift transition-all duration-200">
            <div class="flex justify-between items-start mb-2 md:mb-4">
                <div class="flex-1">
                    <p class="text-xs md:text-sm font-semibold text-gray-600 mb-1 md:mb-2">Watumiaji Active</p>
                    <p class="text-lg md:text-2xl font-bold text-gray-900">{{ $stats['active_users_now'] ?? 0 }}</p>
                </div>
                <div class="flex-shrink-0">
                    <span class="text-xs font-semibold px-2 py-0.5 md:px-3 md:py-1 rounded-full bg-blue-100 text-blue-800">
                        <i class="fas fa-circle text-2xs mr-1"></i> Online
                    </span>
                </div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-1.5 md:h-2">
                <div class="h-1.5 md:h-2 rounded-full transition-all duration-1000 bg-blue-500" 
                    style="width: {{ min((($stats['active_users_now'] ?? 0) / 50) * 100, 100) }}%">
                </div>
            </div>
            <div class="mt-2 md:mt-3 text-xs text-gray-500">
                <i class="fas fa-users mr-1"></i> Wanaotumia sasa
            </div>
        </div>

        <!-- Today's Logins -->
        <div class="bg-white rounded-lg md:rounded-2xl shadow-sm border border-gray-100 p-3 md:p-6 hover-lift transition-all duration-200">
            <div class="flex justify-between items-start mb-2 md:mb-4">
                <div class="flex-1">
                    <p class="text-xs md:text-sm font-semibold text-gray-600 mb-1 md:mb-2">Walioingia Leo</p>
                    <p class="text-lg md:text-2xl font-bold text-gray-900">{{ $stats['today_logins'] ?? 0 }}</p>
                </div>
                <div class="flex-shrink-0">
                    <span class="text-xs font-semibold px-2 py-0.5 md:px-3 md:py-1 rounded-full bg-purple-100 text-purple-800">
                        {{ now()->format('d M') }}
                    </span>
                </div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-1.5 md:h-2">
                <div class="h-1.5 md:h-2 rounded-full transition-all duration-1000 bg-purple-500" 
                    style="width: {{ min((($stats['today_logins'] ?? 0) / 100) * 100, 100) }}%">
                </div>
            </div>
            <div class="mt-2 md:mt-3 text-xs text-gray-500">
                <i class="fas fa-calendar mr-1"></i> {{ now()->format('d M, Y') }}
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="bg-white rounded-lg md:rounded-2xl shadow-sm border border-gray-100 overflow-hidden mx-4 md:mx-0">
        <!-- Table Header with Search -->
        <div class="px-4 md:px-6 py-3 md:py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <h3 class="text-base md:text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-building mr-2 md:mr-3 text-emerald-600 text-sm md:text-base"></i>
                    Orodha ya Makampuni na Shughuli Zake
                </h3>
                
                <!-- Search Bar -->
                <div class="flex items-center space-x-2 w-full md:w-auto">
                    <div class="relative flex-1 md:flex-none md:w-64">
                        <input type="text" 
                               id="searchInput" 
                               placeholder="Tafuta kampuni..." 
                               class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                    </div>
                    <button onclick="location.reload()" class="text-emerald-600 hover:text-emerald-700 text-xs md:text-sm flex items-center space-x-1 bg-emerald-50 px-3 py-2 rounded-lg border border-emerald-200 whitespace-nowrap">
                        <i class="fas fa-sync-alt"></i>
                        <span class="hidden md:inline">Fanya upya</span>
                    </button>
                </div>
            </div>
            
            <!-- Search Stats -->
            <div id="searchStats" class="text-xs text-gray-500 mt-2 hidden">
                <span id="searchCount"></span> matokeo yamepatikana
            </div>
        </div>

        <!-- Mobile Cards View -->
        <div class="md:hidden" id="mobileCardsContainer">
            @forelse($companies as $index => $company)
            <div class="border-b border-gray-100 p-4 hover:bg-gray-50/50 transition-all duration-200 company-card" 
                 data-name="{{ strtolower($company->company_name) }}"
                 data-owner="{{ strtolower($company->owner_name) }}">
                <!-- Header -->
                <div class="flex justify-between items-start mb-3">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-emerald-100 to-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-building text-emerald-600 text-xs"></i>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-gray-900 company-name">{{ $company->company_name }}</div>
                            <div class="text-xs text-gray-500 owner-name">{{ $company->owner_name }}</div>
                        </div>
                    </div>
                    <div class="flex flex-col items-end">
                        <div class="text-xs text-gray-500 mb-1">#{{ $index + 1 }}</div>
                        @if($company->isActive())
                            <span class="badge bg-green-100 text-green-800 text-xs px-2 py-0.5 rounded-full">
                                <i class="fas fa-circle text-2xs mr-1"></i>Active
                            </span>
                        @else
                            <span class="badge bg-gray-100 text-gray-800 text-xs px-2 py-0.5 rounded-full">
                                <i class="fas fa-circle text-2xs mr-1"></i>Inactive
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-2 gap-2 mb-3">
                    <div class="bg-gray-50 rounded-lg p-2">
                        <div class="text-2xs text-gray-500">Active Users</div>
                        <div class="text-sm font-bold text-emerald-600">{{ $company->getActiveUsersCount() }}</div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-2">
                        <div class="text-2xs text-gray-500">Total Users</div>
                        <div class="text-sm font-bold text-gray-900">{{ $company->users->count() + ($company->wafanyakazi_count ?? 0) }}</div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-2">
                        <div class="text-2xs text-gray-500">Logins</div>
                        <div class="text-sm font-bold text-blue-600">{{ $company->getTotalLoginCount() }}</div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-2">
                        <div class="text-2xs text-gray-500">Last Login</div>
                        <div class="text-xs font-medium text-gray-700 last-login">
                            @if($lastLogin = $company->getLastLoginDate())
                                @php
                                    $lastLoginDate = \Carbon\Carbon::parse($lastLogin);
                                @endphp
                                @if($lastLoginDate->isToday())
                                    Leo, {{ $lastLoginDate->format('H:i') }}
                                @elseif($lastLoginDate->isYesterday())
                                    Jana, {{ $lastLoginDate->format('H:i') }}
                                @else
                                    {{ $lastLoginDate->diffForHumans(short: true) }}
                                @endif
                            @else
                                -
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <button class="w-full bg-gradient-to-r from-emerald-500 to-emerald-600 text-white text-xs px-3 py-2.5 rounded-lg transition-all duration-200 flex items-center justify-center space-x-2 view-details-btn" 
                        data-company-id="{{ $company->id }}"
                        data-company-name="{{ $company->company_name }}">
                    <i class="fas fa-chart-line"></i>
                    <span>Angalia Shughuli za Kina</span>
                </button>
            </div>
            @empty
            <div class="text-center py-8">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-building text-gray-400 text-xl"></i>
                </div>
                <p class="text-gray-500 text-sm">Hakuna makampuni yaliyopatikana</p>
            </div>
            @endforelse
            
            <!-- No results message for search -->
            <div id="mobileNoResults" class="text-center py-8 hidden">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-search text-gray-400 text-xl"></i>
                </div>
                <p class="text-gray-500 text-sm">Hakuna kampuni inayolingana na tafuta yako</p>
            </div>
        </div>

        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full" id="companiesTable">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-100">
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">#</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Kampuni</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Hali</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Active Users</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Jumla</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Walioingia</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Mara ya Mwisho</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Vitendo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" id="tableBody">
                    @forelse($companies as $index => $company)
                    <tr class="hover:bg-gray-50/50 transition-all duration-200 group company-row" 
                        data-name="{{ strtolower($company->company_name) }}"
                        data-owner="{{ strtolower($company->owner_name) }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-600 font-medium">{{ $index + 1 }}</div>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-emerald-100 to-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-building text-emerald-600 text-sm"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900 company-name">{{ $company->company_name }}</div>
                                    <div class="text-xs text-gray-500 owner-name">{{ $company->owner_name }}</div>
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($company->isActive())
                                <span class="inline-flex items-center space-x-1 px-3 py-1.5 bg-green-100 text-green-700 rounded-full text-xs font-medium border border-green-200">
                                    <i class="fas fa-circle text-2xs"></i>
                                    <span>Active</span>
                                </span>
                            @else
                                <span class="inline-flex items-center space-x-1 px-3 py-1.5 bg-gray-100 text-gray-700 rounded-full text-xs font-medium border border-gray-200">
                                    <i class="fas fa-circle text-2xs"></i>
                                    <span>Inactive</span>
                                </span>
                            @endif
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="inline-flex items-center justify-center px-2.5 py-1.5 bg-blue-100 text-blue-700 rounded-lg text-xs font-medium">
                                {{ $company->getActiveUsersCount() }}
                            </span>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="text-sm text-gray-900">{{ $company->users->count() + ($company->wafanyakazi_count ?? 0) }}</span>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="text-sm text-gray-900">{{ $company->getTotalLoginCount() }}</span>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap last-login">
                            @if($lastLogin = $company->getLastLoginDate())
                                @php
                                    $lastLoginDate = \Carbon\Carbon::parse($lastLogin);
                                @endphp
                                <div class="text-sm text-gray-900" title="{{ $lastLoginDate->format('d/m/Y H:i:s') }}">
                                    @if($lastLoginDate->isToday())
                                        <span class="text-green-600">Leo, {{ $lastLoginDate->format('H:i') }}</span>
                                    @elseif($lastLoginDate->isYesterday())
                                        <span class="text-blue-600">Jana, {{ $lastLoginDate->format('H:i') }}</span>
                                    @else
                                        {{ $lastLoginDate->diffForHumans() }}
                                    @endif
                                </div>
                            @else
                                <span class="text-sm text-gray-500">-</span>
                            @endif
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <button class="inline-flex items-center space-x-2 px-4 py-2 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white text-xs font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md view-details-btn"
                                    data-company-id="{{ $company->id }}"
                                    data-company-name="{{ $company->company_name }}">
                                <i class="fas fa-chart-line"></i>
                                <span>Angalia</span>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-building text-gray-400 text-xl"></i>
                                </div>
                                <p class="text-gray-500 text-sm">Hakuna makampuni yaliyopatikana</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            
            <!-- Desktop no results -->
            <div id="desktopNoResults" class="text-center py-12 hidden">
                <div class="flex flex-col items-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-search text-gray-400 text-xl"></i>
                    </div>
                    <p class="text-gray-500 text-sm">Hakuna kampuni inayolingana na tafuta yako</p>
                </div>
            </div>
        </div>

        <!-- Table Footer -->
        @if(method_exists($companies, 'hasPages') && $companies->hasPages())
        <div class="px-4 md:px-6 py-3 md:py-4 border-t border-gray-100 bg-gray-50/50">
            <div class="flex flex-col md:flex-row md:items-center justify-between space-y-2 md:space-y-0">
                <div class="text-sm text-gray-600">
                    {{ $companies->firstItem() }} - {{ $companies->lastItem() }} ya {{ $companies->total() }}
                </div>
                <div class="flex space-x-2 overflow-x-auto pb-1 md:pb-0">
                    {{ $companies->onEachSide(1)->links('vendor.pagination.tailwind') }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Company Details Modal -->
<div id="companyDetailsModal" class="fixed inset-0 hidden items-center justify-center bg-black/50 z-50 px-4">
    <div class="bg-white rounded-lg md:rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] md:max-h-[85vh] overflow-hidden animate-scale-in">
        <!-- Header -->
        <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 px-4 md:px-6 py-3 md:py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2 md:space-x-3">
                    <div class="w-8 h-8 md:w-10 md:h-10 bg-white/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-line text-white text-sm md:text-base"></i>
                    </div>
                    <div class="min-w-0">
                        <h5 class="font-semibold text-white text-sm md:text-lg truncate">Shughuli za Kampuni</h5>
                        <p class="text-emerald-100 text-xs md:text-sm truncate" id="modalCompanyName"></p>
                    </div>
                </div>
                <button onclick="hideModal('companyDetailsModal')" class="text-white/80 hover:text-white transition-colors">
                    <i class="fas fa-times text-lg md:text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Content -->
        <div class="p-4 md:p-6 overflow-y-auto max-h-[calc(90vh-120px)] md:max-h-[calc(85vh-120px)]" id="companyDetailsContent">
            <div class="text-center py-12">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-emerald-600 border-t-transparent"></div>
                <p class="text-gray-500 text-sm mt-3">Inapakia taarifa...</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 px-4 md:px-6 py-3 md:py-4 border-t border-gray-100">
            <div class="flex justify-end">
                <button onclick="hideModal('companyDetailsModal')" 
                        class="px-4 md:px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors flex items-center space-x-2">
                    <i class="fas fa-times text-xs"></i>
                    <span>Funga</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Styles -->
<style>
@keyframes slideDown {
    from {
        opacity: 0;
        transform: translate(-50%, -20px);
    }
    to {
        opacity: 1;
        transform: translate(-50%, 0);
    }
}

@keyframes scaleIn {
    from {
        opacity: 0;
        transform: scale(0.95) translateY(-10px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

.animate-slide-down {
    animation: slideDown 0.3s ease-out;
}

.animate-scale-in {
    animation: scaleIn 0.2s ease-out;
}

.hover-lift:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.text-2xs {
    font-size: 0.625rem;
    line-height: 0.875rem;
}

/* Custom scrollbar */
.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Mobile optimizations */
@media (max-width: 768px) {
    button, 
    .clickable {
        min-height: 44px;
        min-width: 44px;
    }
    
    .text-2xs {
        font-size: 0.6rem;
    }
}

/* Search highlight */
.highlight {
    background-color: #fef3c7;
    transition: background-color 0.3s;
}
</style>

<!-- JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Auto-hide success notification
    const notification = document.getElementById('success-notification');
    if (notification) {
        setTimeout(() => {
            closeNotification();
        }, 5000);
    }

    // Initialize view details buttons
    initializeViewDetails();
    
    // Initialize search functionality
    initializeSearch();
});

function initializeViewDetails() {
    document.querySelectorAll('.view-details-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const companyId = this.getAttribute('data-company-id');
            const companyName = this.getAttribute('data-company-name');
            
            document.getElementById('modalCompanyName').textContent = companyName;
            showModal('companyDetailsModal');
            
            // Load company details via AJAX
            loadCompanyDetails(companyId);
        });
    });
}

function initializeSearch() {
    const searchInput = document.getElementById('searchInput');
    if (!searchInput) return;
    
    const mobileCards = document.querySelectorAll('.company-card');
    const tableRows = document.querySelectorAll('.company-row');
    const mobileNoResults = document.getElementById('mobileNoResults');
    const desktopNoResults = document.getElementById('desktopNoResults');
    const searchStats = document.getElementById('searchStats');
    const searchCount = document.getElementById('searchCount');
    
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        
        if (searchTerm === '') {
            // Show all
            mobileCards.forEach(card => {
                card.style.display = 'block';
            });
            tableRows.forEach(row => {
                row.style.display = '';
            });
            if (mobileNoResults) mobileNoResults.classList.add('hidden');
            if (desktopNoResults) desktopNoResults.classList.add('hidden');
            if (searchStats) searchStats.classList.add('hidden');
            return;
        }
        
        let mobileMatchCount = 0;
        let desktopMatchCount = 0;
        
        // Filter mobile cards
        mobileCards.forEach(card => {
            const companyName = card.getAttribute('data-name') || '';
            const ownerName = card.getAttribute('data-owner') || '';
            
            if (companyName.includes(searchTerm) || ownerName.includes(searchTerm)) {
                card.style.display = 'block';
                mobileMatchCount++;
                
                // Highlight matching text (optional)
                highlightText(card.querySelector('.company-name'), searchTerm);
                highlightText(card.querySelector('.owner-name'), searchTerm);
            } else {
                card.style.display = 'none';
            }
        });
        
        // Filter table rows
        tableRows.forEach(row => {
            const companyName = row.getAttribute('data-name') || '';
            const ownerName = row.getAttribute('data-owner') || '';
            
            if (companyName.includes(searchTerm) || ownerName.includes(searchTerm)) {
                row.style.display = '';
                desktopMatchCount++;
                
                // Highlight matching text
                highlightText(row.querySelector('.company-name'), searchTerm);
                highlightText(row.querySelector('.owner-name'), searchTerm);
            } else {
                row.style.display = 'none';
            }
        });
        
        // Show/hide no results messages
        if (mobileMatchCount === 0 && mobileNoResults) {
            mobileNoResults.classList.remove('hidden');
        } else if (mobileNoResults) {
            mobileNoResults.classList.add('hidden');
        }
        
        if (desktopMatchCount === 0 && desktopNoResults) {
            desktopNoResults.classList.remove('hidden');
        } else if (desktopNoResults) {
            desktopNoResults.classList.add('hidden');
        }
        
        // Update search stats
        if (searchStats && searchCount) {
            const totalMatches = Math.max(mobileMatchCount, desktopMatchCount);
            if (totalMatches > 0) {
                searchCount.textContent = totalMatches;
                searchStats.classList.remove('hidden');
            } else {
                searchStats.classList.add('hidden');
            }
        }
    });
}

function highlightText(element, searchTerm) {
    if (!element) return;
    
    const originalText = element.textContent;
    const lowerText = originalText.toLowerCase();
    const index = lowerText.indexOf(searchTerm);
    
    if (index !== -1) {
        // Simple highlight by wrapping in span (can be enhanced)
        // This is a basic implementation
        element.innerHTML = originalText.replace(
            new RegExp(searchTerm, 'gi'),
            match => `<span class="highlight">${match}</span>`
        );
    } else {
        // Reset to original text
        element.innerHTML = originalText;
    }
}

function loadCompanyDetails(companyId) {
    // Show loading state
    document.getElementById('companyDetailsContent').innerHTML = `
        <div class="text-center py-12">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-emerald-600 border-t-transparent"></div>
            <p class="text-gray-500 text-sm mt-3">Inapakia taarifa...</p>
        </div>
    `;

    // Fix the URL - use absolute URL with proper base
    const url = `/admin/company-activity/${companyId}/details`;
    
    fetch(url, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            displayCompanyDetails(data);
        } else {
            throw new Error(data.message || 'Unknown error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('companyDetailsContent').innerHTML = `
            <div class="text-center py-8">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <p class="text-red-600 text-sm mb-2">Hitilafu wakati wa kupakia data.</p>
                <p class="text-gray-500 text-xs mb-4">${error.message}</p>
                <button onclick="loadCompanyDetails(${companyId})" class="px-4 py-2 bg-emerald-600 text-white text-sm rounded-lg hover:bg-emerald-700 transition-colors">
                    <i class="fas fa-sync-alt mr-1"></i> Jaribu tena
                </button>
            </div>
        `;
    });
}

function displayCompanyDetails(data) {
    let usersHtml = '';
    if (data.users && data.users.length > 0) {
        data.users.forEach(user => {
            const lastActivity = user.last_activity ? new Date(user.last_activity).toLocaleString('sw-TZ', { 
                dateStyle: 'short', 
                timeStyle: 'short' 
            }) : '-';
            
            usersHtml += `
                <tr class="border-b border-gray-100 hover:bg-gray-50/50">
                    <td class="px-3 py-2 text-sm">${user.name || '-'}</td>
                    <td class="px-3 py-2 text-sm">${user.username || '-'}</td>
                    <td class="px-3 py-2 text-center">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                            ${user.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}">
                            <i class="fas fa-circle text-2xs mr-1"></i>
                            ${user.is_active ? 'Active' : 'Inactive'}
                        </span>
                    </td>
                    <td class="px-3 py-2 text-sm text-center">${user.login_count || 0}</td>
                    <td class="px-3 py-2 text-sm">${lastActivity}</td>
                </tr>
            `;
        });
    } else {
        usersHtml = `
            <tr>
                <td colspan="5" class="px-3 py-4 text-center text-gray-500">
                    Hakuna watumiaji waliopatikana
                </td>
            </tr>
        `;
    }

    // Prepare company data with null checks
    const company = data.company || {};
    const weeklyActivity = company.weekly_activity || [];
    
    let weeklyChartHtml = '';
    if (weeklyActivity.length > 0) {
        const labels = weeklyActivity.map(item => item.day || '');
        const values = weeklyActivity.map(item => item.active_users || 0);
        
        weeklyChartHtml = `
            <div class="h-48 md:h-64">
                <canvas id="weeklyChart"></canvas>
            </div>
            <script>
                setTimeout(() => {
                    const ctx = document.getElementById('weeklyChart')?.getContext('2d');
                    if (ctx) {
                        new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: ${JSON.stringify(labels)},
                                datasets: [{
                                    label: 'Watumiaji Active',
                                    data: ${JSON.stringify(values)},
                                    borderColor: '#10b981',
                                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                    tension: 0.3,
                                    fill: true,
                                    pointBackgroundColor: '#10b981',
                                    pointBorderColor: '#fff',
                                    pointBorderWidth: 2,
                                    pointRadius: 4
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { display: false },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                return 'Watumiaji: ' + context.raw;
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        stepSize: 1,
                                        grid: { color: 'rgba(0,0,0,0.05)' }
                                    },
                                    x: { 
                                        grid: { display: false }
                                    }
                                }
                            }
                        });
                    }
                }, 100);
            <\/script>
        `;
    } else {
        weeklyChartHtml = '<p class="text-center text-gray-500 py-8">Hakuna data ya shughuli za wiki</p>';
    }

    const lastLoginDate = company.last_login ? new Date(company.last_login).toLocaleString('sw-TZ', { 
        dateStyle: 'medium', 
        timeStyle: 'short' 
    }) : '-';

    const html = `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 mb-6">
            <!-- Company Info Card -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h6 class="font-semibold text-gray-900 text-sm mb-3 flex items-center">
                    <i class="fas fa-building mr-2 text-emerald-600"></i>
                    Taarifa za Kampuni
                </h6>
                <div class="space-y-2">
                    <div class="flex justify-between py-2 border-b border-gray-200">
                        <span class="text-xs text-gray-600">Jina:</span>
                        <span class="text-xs font-medium text-gray-900">${company.name || '-'}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-200">
                        <span class="text-xs text-gray-600">Hali:</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                            ${company.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}">
                            <i class="fas fa-circle text-2xs mr-1"></i>
                            ${company.is_active ? 'Active' : 'Inactive'}
                        </span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-200">
                        <span class="text-xs text-gray-600">Active Sasa:</span>
                        <span class="text-xs font-bold text-emerald-600">${company.active_users || 0}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-200">
                        <span class="text-xs text-gray-600">Jumla Watumiaji:</span>
                        <span class="text-xs font-medium">${company.total_users || 0}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-200">
                        <span class="text-xs text-gray-600">Jumla Walioingia:</span>
                        <span class="text-xs font-medium">${company.total_logins || 0}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-200">
                        <span class="text-xs text-gray-600">Walioingia Leo:</span>
                        <span class="text-xs font-medium">${company.daily_active_users || 0}</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-xs text-gray-600">Mara ya Mwisho:</span>
                        <span class="text-xs font-medium">${lastLoginDate}</span>
                    </div>
                </div>
            </div>

            <!-- Weekly Activity Chart -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h6 class="font-semibold text-gray-900 text-sm mb-3 flex items-center">
                    <i class="fas fa-chart-line mr-2 text-emerald-600"></i>
                    Shughuli za Wiki
                </h6>
                ${weeklyChartHtml}
            </div>
        </div>

        <!-- Users Table -->
        <div class="bg-gray-50 rounded-lg p-4">
            <h6 class="font-semibold text-gray-900 text-sm mb-3 flex items-center">
                <i class="fas fa-users mr-2 text-emerald-600"></i>
                Watumiaji wa Kampuni
            </h6>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700">Jina</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700">Mtumiaji</th>
                            <th class="px-3 py-2 text-center text-xs font-semibold text-gray-700">Hali</th>
                            <th class="px-3 py-2 text-center text-xs font-semibold text-gray-700">Mara</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700">Shughuli ya Mwisho</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${usersHtml}
                    </tbody>
                </table>
            </div>
        </div>
    `;

    document.getElementById('companyDetailsContent').innerHTML = html;
}

function showModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
}

function hideModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
    }
}

function closeNotification() {
    const notification = document.getElementById('success-notification');
    if (notification) {
        notification.style.animation = 'slideDown 0.3s ease-out reverse';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }
}

// Escape key to close modal
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        const openModal = document.querySelector('[id$="Modal"]:not(.hidden)');
        if (openModal) {
            hideModal(openModal.id);
        }
    }
});

// Close modal when clicking outside
document.addEventListener('click', (e) => {
    const openModal = document.querySelector('[id$="Modal"]:not(.hidden)');
    if (openModal && e.target === openModal) {
        hideModal(openModal.id);
    }
});
</script>
@endsection