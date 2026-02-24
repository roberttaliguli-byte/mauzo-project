{{-- resources/views/admin/company-activity.blade.php --}}
@extends('layouts.admin')

@section('title', 'Shughuli za Makampuni')
@section('page-title', 'Shughuli za Makampuni')


@section('content')
<div class="min-h-screen bg-gray-50/30">
    <!-- Header -->
    <div class="mb-4 md:mb-8 px-4 md:px-0">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div class="mb-3 md:mb-0">
                <h1 class="text-lg md:text-2xl font-bold text-gray-900">📊 Shughuli za Makampuni</h1>
                <p class="text-gray-600 text-xs md:text-base mt-1">Fuatilia shughuli na hali ya makampuni kwa wakati halisi</p>
            </div>
            <div class="flex items-center space-x-2 md:space-x-4">
                <div class="bg-white px-3 py-2 md:px-4 md:py-2 rounded-lg shadow-sm border">
                    <div class="text-sm md:text-base font-bold text-emerald-600 flex items-center">
                        <i class="fas fa-clock mr-1 text-xs"></i>
                        <span id="current-time">{{ now()->setTimezone('Africa/Nairobi')->format('H:i:s') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                    <p class="text-xs md:text-sm font-semibold text-gray-600 mb-1 md:mb-2">Kampuni Active Sasa</p>
                    <p class="text-lg md:text-2xl font-bold text-gray-900">{{ $stats['active_companies'] ?? 0 }}</p>
                </div>
                <div class="flex-shrink-0">
                    <span class="text-xs font-semibold px-2 py-0.5 md:px-3 md:py-1 rounded-full bg-green-100 text-green-800">
                        <i class="fas fa-circle text-2xs mr-1"></i> Online
                    </span>
                </div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-1.5 md:h-2">
                <div class="h-1.5 md:h-2 rounded-full transition-all duration-1000 bg-green-500" 
                    style="width: {{ $stats['active_percentage'] ?? 0 }}%">
                </div>
            </div>
            <div class="mt-2 md:mt-3 text-xs text-gray-500">
                <i class="fas fa-clock mr-1"></i> Dakika 10 za mwisho
            </div>
        </div>

        <!-- Total Companies -->
        <div class="bg-white rounded-lg md:rounded-2xl shadow-sm border border-gray-100 p-3 md:p-6 hover-lift transition-all duration-200">
            <div class="flex justify-between items-start mb-2 md:mb-4">
                <div class="flex-1">
                    <p class="text-xs md:text-sm font-semibold text-gray-600 mb-1 md:mb-2">Jumla ya Kampuni</p>
                    <p class="text-lg md:text-2xl font-bold text-gray-900">{{ $stats['total_companies'] ?? 0 }}</p>
                </div>
                <div class="flex-shrink-0">
                    <span class="text-xs font-semibold px-2 py-0.5 md:px-3 md:py-1 rounded-full bg-blue-100 text-blue-800">
                        Zote
                    </span>
                </div>
            </div>
            <div class="mt-2 md:mt-3 text-xs text-gray-500">
                <i class="fas fa-building mr-1"></i> {{ $stats['active_companies'] ?? 0 }} Active, {{ $stats['inactive_companies'] ?? 0 }} Inactive
            </div>
        </div>

        <!-- Active Users -->
        <div class="bg-white rounded-lg md:rounded-2xl shadow-sm border border-gray-100 p-3 md:p-6 hover-lift transition-all duration-200">
            <div class="flex justify-between items-start mb-2 md:mb-4">
                <div class="flex-1">
                    <p class="text-xs md:text-sm font-semibold text-gray-600 mb-1 md:mb-2">Watumiaji Active Sasa</p>
                    <p class="text-lg md:text-2xl font-bold text-gray-900">{{ $stats['active_users_now'] ?? 0 }}</p>
                </div>
                <div class="flex-shrink-0">
                    <span class="text-xs font-semibold px-2 py-0.5 md:px-3 md:py-1 rounded-full bg-purple-100 text-purple-800">
                        <i class="fas fa-user-check text-2xs mr-1"></i> Online
                    </span>
                </div>
            </div>
            <div class="mt-2 md:mt-3 text-xs text-gray-500">
                <i class="fas fa-users mr-1"></i> Wanaotumia sasa hivi
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
                    <span class="text-xs font-semibold px-2 py-0.5 md:px-3 md:py-1 rounded-full bg-amber-100 text-amber-800">
                        {{ now()->setTimezone('Africa/Nairobi')->format('d M') }}
                    </span>
                </div>
            </div>
            <div class="mt-2 md:mt-3 text-xs text-gray-500">
                <i class="fas fa-calendar mr-1"></i> {{ now()->setTimezone('Africa/Nairobi')->format('d M, Y') }}
            </div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm mx-4 md:mx-0 mb-4">
        <div class="flex">
            <button data-tab="taarifa" class="tab-button flex-1 py-3 px-4 text-sm font-medium border-r border-gray-200 bg-emerald-50 text-emerald-700">
                <i class="fas fa-table mr-2"></i> Hali ya Kampuni
            </button>
            <button data-tab="ripoti" class="tab-button flex-1 py-3 px-4 text-sm font-medium text-gray-600 hover:bg-gray-50">
                <i class="fas fa-calendar-check mr-2"></i> Kampuni Active Leo
            </button>
        </div>
    </div>

    <!-- TAB 1: Taarifa (Companies Status) -->
    <div id="taarifa-tab-content" class="tab-content">
        <!-- Companies Table -->
        <div class="bg-white rounded-lg md:rounded-2xl shadow-sm border border-gray-100 overflow-hidden mx-4 md:mx-0">
            <!-- Table Header with Search -->
            <div class="px-4 md:px-6 py-3 md:py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <h3 class="text-base md:text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-building mr-2 md:mr-3 text-emerald-600 text-sm md:text-base"></i>
                        Hali ya Makampuni kwa Wakati Halisi
                    </h3>
                    
                    <!-- Search Bar -->
                    <div class="flex items-center space-x-2 w-full md:w-auto">
                        <div class="relative flex-1 md:flex-none md:w-64">
                            <input type="text" 
                                   id="searchInput" 
                                   placeholder="Tafuta kwa jina la kampuni au mmiliki..." 
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
                                    <i class="fas fa-circle text-2xs mr-1"></i>Active Sasa
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
                            <div class="text-2xs text-gray-500">Active Sasa</div>
                            <div class="text-sm font-bold text-emerald-600">{{ $company->getActiveUsersCount() }}</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-2">
                            <div class="text-2xs text-gray-500">Jumla Watumiaji</div>
                            <div class="text-sm font-bold text-gray-900">{{ $company->users->count() + ($company->wafanyakazi_count ?? 0) }}</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-2">
                            <div class="text-2xs text-gray-500">Walioingia Leo</div>
                            <div class="text-sm font-bold text-blue-600">{{ $company->today_login_count ?? 0 }}</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-2">
                            <div class="text-2xs text-gray-500">Jumla Walioingia</div>
                            <div class="text-sm font-bold text-gray-900">{{ $company->getTotalLoginCount() }}</div>
                        </div>
                    </div>

                    <!-- Last Login Info -->
                    <div class="mb-3 text-xs text-gray-600 bg-gray-50 p-2 rounded-lg">
                        <i class="fas fa-clock mr-1 text-emerald-600"></i>
                        @if($lastLogin = $company->getLastLoginDate())
                            @php
                                $lastLoginDate = \Carbon\Carbon::parse($lastLogin)->setTimezone('Africa/Nairobi');
                            @endphp
                            @if($lastLoginDate->isToday())
                                <span class="text-green-600 font-medium">Leo, {{ $lastLoginDate->format('H:i:s') }}</span>
                            @elseif($lastLoginDate->isYesterday())
                                <span class="text-blue-600 font-medium">Jana, {{ $lastLoginDate->format('H:i:s') }}</span>
                            @else
                                {{ $lastLoginDate->format('d/m/Y H:i:s') }}
                            @endif
                        @else
                            Hajawahi kuingia
                        @endif
                    </div>

                    <!-- View Details Button -->
                    <button onclick="showCompanyDetails({{ $company->id }}, '{{ $company->company_name }}')"
                            class="w-full bg-gradient-to-r from-emerald-500 to-emerald-600 text-white text-xs px-3 py-2.5 rounded-lg transition-all duration-200 flex items-center justify-center space-x-2 hover:from-emerald-600 hover:to-emerald-700">
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
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Mmiliki</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Hali Sasa</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Active Sasa</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Leo</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Jumla</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Mara ya Mwisho Kuingia</th>
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
                                        <button onclick="showCompanyDetails({{ $company->id }}, '{{ $company->company_name }}')" 
                                                class="text-sm font-semibold text-gray-900 company-name hover:text-emerald-600 hover:underline text-left">
                                            {{ $company->company_name }}
                                        </button>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-700">{{ $company->owner_name }}</div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-center">
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
                                <span class="inline-flex items-center justify-center px-2.5 py-1.5 bg-emerald-100 text-emerald-700 rounded-lg text-xs font-medium">
                                    {{ $company->getActiveUsersCount() }}
                                </span>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center justify-center px-2.5 py-1.5 {{ ($company->today_login_count ?? 0) > 0 ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-700' }} rounded-lg text-xs font-medium">
                                    {{ $company->today_login_count ?? 0 }}
                                </span>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-sm text-gray-900">{{ $company->getTotalLoginCount() }}</span>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($lastLogin = $company->getLastLoginDate())
                                    @php
                                        $lastLoginDate = \Carbon\Carbon::parse($lastLogin)->setTimezone('Africa/Nairobi');
                                    @endphp
                                    <div class="text-sm" title="{{ $lastLoginDate->format('d/m/Y H:i:s') }}">
                                        @if($lastLoginDate->isToday())
                                            <span class="text-green-600">Leo, {{ $lastLoginDate->format('H:i:s') }}</span>
                                        @elseif($lastLoginDate->isYesterday())
                                            <span class="text-blue-600">Jana, {{ $lastLoginDate->format('H:i:s') }}</span>
                                        @else
                                            <span class="text-gray-600">{{ $lastLoginDate->format('d/m/Y H:i:s') }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-sm text-gray-500">-</span>
                                @endif
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

    <!-- TAB 2: Ripoti (Active Companies Today) -->
    <div id="ripoti-tab-content" class="tab-content hidden">
        <div class="grid grid-cols-1 gap-4 px-4 md:px-0">
            <!-- Report Header -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-4 md:px-6 py-3 md:py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                    <h3 class="text-base md:text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-calendar-check mr-2 md:mr-3 text-emerald-600 text-sm md:text-base"></i>
                        Kampuni Zilizoingia Leo - {{ now()->setTimezone('Africa/Nairobi')->format('d M, Y') }}
                    </h3>
                </div>
                
                <div class="p-4 md:p-6">
                    <!-- Summary Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6">
                        <div class="bg-emerald-50 p-4 rounded-lg border border-emerald-200">
                            <p class="text-xs text-gray-600 mb-1">Kampuni Zilizoingia Leo</p>
                            <p class="text-2xl font-bold text-emerald-700" id="today-active-companies">0</p>
                        </div>
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                            <p class="text-xs text-gray-600 mb-1">Watumiaji Walioingia Leo</p>
                            <p class="text-2xl font-bold text-blue-700" id="today-active-users">{{ $stats['today_unique_users'] ?? 0 }}</p>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                            <p class="text-xs text-gray-600 mb-1">Jumla ya Kuingia Leo</p>
                            <p class="text-2xl font-bold text-purple-700" id="today-total-logins">{{ $stats['today_logins'] ?? 0 }}</p>
                        </div>
                    </div>

                    <!-- Companies that logged in today -->
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Orodha ya Kampuni Zilizoingia Leo</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm" id="today-companies-table">
                            <thead class="bg-emerald-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-emerald-800">#</th>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-emerald-800">Kampuni</th>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-emerald-800">Mmiliki</th>
                                    <th class="px-4 py-2 text-center text-xs font-semibold text-emerald-800">Mara Walioingia Leo</th>
                                    <th class="px-4 py-2 text-center text-xs font-semibold text-emerald-800">Active Sasa</th>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-emerald-800">Mara ya Mwisho (EAT)</th>
                                </tr>
                            </thead>
                            <tbody id="today-companies-tbody" class="divide-y divide-gray-100">
                                <!-- Will be filled by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
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
    padding: 0 2px;
    border-radius: 2px;
}
</style>

<!-- JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide success notification
    const notification = document.getElementById('success-notification');
    if (notification) {
        setTimeout(closeNotification, 5000);
    }
    
    // Update East African time every second
    updateEastAfricanTime();
    setInterval(updateEastAfricanTime, 1000);
    
    // Tab functionality
    initializeTabs();
    
    // Search functionality
    initializeSearch();
    
    // Load today's active companies
    loadTodayActiveCompanies();
});

function updateEastAfricanTime() {
    const timeElement = document.getElementById('current-time');
    if (timeElement) {
        const now = new Date();
        // Convert to East African Time (UTC+3)
        const utc = now.getTime() + (now.getTimezoneOffset() * 60000);
        const eastAfricaTime = new Date(utc + (3600000 * 3));
        
        const hours = eastAfricaTime.getHours().toString().padStart(2, '0');
        const minutes = eastAfricaTime.getMinutes().toString().padStart(2, '0');
        const seconds = eastAfricaTime.getSeconds().toString().padStart(2, '0');
        
        timeElement.textContent = `${hours}:${minutes}:${seconds}`;
    }
}

function initializeTabs() {
    const tabs = document.querySelectorAll('.tab-button');
    const savedTab = sessionStorage.getItem('company_activity_tab') || 'taarifa';
    
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const tabName = this.dataset.tab;
            
            // Update tab styles
            tabs.forEach(t => {
                t.classList.remove('bg-emerald-50', 'text-emerald-700');
                t.classList.add('text-gray-600', 'hover:bg-gray-50');
            });
            this.classList.add('bg-emerald-50', 'text-emerald-700');
            this.classList.remove('text-gray-600', 'hover:bg-gray-50');
            
            // Show corresponding content
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            document.getElementById(`${tabName}-tab-content`).classList.remove('hidden');
            
            // If switching to reports tab, refresh data
            if (tabName === 'ripoti') {
                loadTodayActiveCompanies();
            }
            
            // Save to session
            sessionStorage.setItem('company_activity_tab', tabName);
        });
    });
    
    // Activate saved tab
    const activeTab = document.querySelector(`.tab-button[data-tab="${savedTab}"]`);
    if (activeTab) {
        activeTab.click();
    }
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
    
    let searchTimeout;
    
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            const searchTerm = this.value.toLowerCase().trim();
            
            if (searchTerm === '') {
                // Show all
                mobileCards.forEach(card => card.style.display = 'block');
                tableRows.forEach(row => row.style.display = '');
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
        }, 300);
    });
}

function highlightText(element, searchTerm) {
    if (!element) return;
    const originalText = element.textContent;
    const regex = new RegExp(`(${searchTerm})`, 'gi');
    element.innerHTML = originalText.replace(regex, '<span class="highlight">$1</span>');
}

function loadTodayActiveCompanies() {
    console.log('Loading today\'s active companies...');
    
    // Get companies data from PHP
    const companies = @json($companies->items());
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    // Filter companies that have logged in today (using today_login_count)
    const todayCompanies = companies.filter(company => 
        company.today_login_count && company.today_login_count > 0
    );
    
    console.log('Today companies found:', todayCompanies.length);
    
    // Calculate total unique users who logged in today
    // This would come from the controller ideally
    const totalUniqueUsers = @json($stats['today_unique_users'] ?? 0);
    const totalLogins = @json($stats['today_logins'] ?? 0);
    
    // Update summary cards
    document.getElementById('today-active-companies').textContent = todayCompanies.length;
    document.getElementById('today-active-users').textContent = totalUniqueUsers;
    document.getElementById('today-total-logins').textContent = totalLogins;
    
    // Generate table rows
    let tableHtml = '';
    if (todayCompanies.length > 0) {
        todayCompanies.forEach((company, index) => {
            // Format last login time in East African Time
            const lastLogin = company.last_login_date ? new Date(company.last_login_date) : null;
            let lastLoginStr = '-';
            
            if (lastLogin) {
                // Convert to East African Time
                const eatDate = new Date(lastLogin.getTime() + (3 * 60 * 60 * 1000));
                lastLoginStr = eatDate.toLocaleString('sw-TZ', {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });
            }
            
            // Check if company is currently active (last 10 minutes)
            const isActiveNow = company.is_active || false;
            const activeStatus = isActiveNow ? 
                '<span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs"><i class="fas fa-circle text-2xs mr-1"></i>Active</span>' : 
                '<span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs"><i class="fas fa-circle text-2xs mr-1"></i>Inactive</span>';
            
            tableHtml += `
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2">${index + 1}</td>
                    <td class="px-4 py-2 font-medium">
                        <button onclick="showCompanyDetails(${company.id}, '${company.company_name}')" 
                                class="text-emerald-600 hover:text-emerald-800 hover:underline text-left">
                            ${company.company_name}
                        </button>
                    </td>
                    <td class="px-4 py-2">${company.owner_name || '-'}</td>
                    <td class="px-4 py-2 text-center font-bold text-emerald-700">${company.today_login_count || 0}</td>
                    <td class="px-4 py-2 text-center">${activeStatus}</td>
                    <td class="px-4 py-2">${lastLoginStr}</td>
                </tr>
            `;
        });
    } else {
        tableHtml = '<tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">Hakuna kampuni zilizoingia leo</td></tr>';
    }
    
    document.getElementById('today-companies-tbody').innerHTML = tableHtml;
}

function showCompanyDetails(companyId, companyName) {
    document.getElementById('modalCompanyName').textContent = companyName;
    showModal('companyDetailsModal');
    
    // Show loading state
    document.getElementById('companyDetailsContent').innerHTML = `
        <div class="text-center py-12">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-emerald-600 border-t-transparent"></div>
            <p class="text-gray-500 text-sm mt-3">Inapakia taarifa za ${companyName}...</p>
        </div>
    `;

    const url = `/admin/reports/company-activity/${companyId}/details`;
    
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
                <button onclick="showCompanyDetails(${companyId}, '${companyName}')" 
                        class="px-4 py-2 bg-emerald-600 text-white text-sm rounded-lg hover:bg-emerald-700 transition-colors">
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
                timeZone: 'Africa/Nairobi',
                dateStyle: 'short', 
                timeStyle: 'short' 
            }) : '-';
            
            const isActive = user.is_active ? 
                '<span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium"><i class="fas fa-circle text-2xs mr-1"></i>Active</span>' : 
                '<span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs font-medium"><i class="fas fa-circle text-2xs mr-1"></i>Inactive</span>';
            
            usersHtml += `
                <tr class="border-b border-gray-100 hover:bg-gray-50/50">
                    <td class="px-3 py-2 text-sm">${user.name || '-'}</td>
                    <td class="px-3 py-2 text-sm">${user.username || '-'}</td>
                    <td class="px-3 py-2 text-center">${isActive}</td>
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

    const company = data.company || {};
    const weeklyActivity = company.weekly_activity || [];
    
    let weeklyChartHtml = '';
    if (weeklyActivity.length > 0) {
        const labels = JSON.stringify(weeklyActivity.map(item => item.day || ''));
        const values = JSON.stringify(weeklyActivity.map(item => item.active_users || 0));
        
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
                                labels: ${labels},
                                datasets: [{
                                    label: 'Watumiaji Active',
                                    data: ${values},
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
        timeZone: 'Africa/Nairobi',
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
                    Shughuli za Wiki (Saa za Afrika Mashariki)
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
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700">Shughuli ya Mwisho (EAT)</th>
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

// Modal functions
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
        setTimeout(() => notification.remove(), 300);
    }
}

// Escape key to close modal
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        const openModal = document.querySelector('[id$="Modal"]:not(.hidden)');
        if (openModal) hideModal(openModal.id);
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