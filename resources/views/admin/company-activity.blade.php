@extends('layouts.admin')

@section('title', 'Shughuli za Makampuni')
@section('page-title', 'Shughuli za Makampuni')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <!-- Header -->
    <div class="mb-4 md:mb-8 px-4 md:px-0">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div class="mb-3 md:mb-0">
                <h1 class="text-lg md:text-2xl font-bold bg-gradient-to-r from-emerald-700 to-teal-700 bg-clip-text text-transparent">
                    📊 Shughuli za Makampuni
                </h1>
                <p class="text-gray-600 text-xs md:text-base mt-1">Fuatilia shughuli na hali ya makampuni yote kwa wakati halisi</p>
            </div>
            <div class="flex items-center space-x-2 md:space-x-4">
                <div class="bg-white px-3 py-2 md:px-4 md:py-2 rounded-xl shadow-md border border-emerald-100">
                    <div class="text-sm md:text-base font-bold text-emerald-600 flex items-center">
                        <i class="fas fa-clock mr-1 text-xs"></i>
                        <span id="current-time">{{ now()->setTimezone('Africa/Nairobi')->format('H:i:s') }}</span>
                    </div>
                </div>
                <button onclick="refreshData()" class="bg-white hover:bg-gray-50 px-3 py-2 md:px-4 md:py-2 rounded-xl shadow-md border border-gray-200 transition-all duration-200">
                    <i class="fas fa-sync-alt text-emerald-600 text-sm"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Success/Error Notification -->
    @if(session('success'))
    <div id="success-notification" class="fixed top-4 md:top-6 left-1/2 transform -translate-x-1/2 z-50 animate-slide-down w-[95%] md:max-w-md">
        <div class="bg-gradient-to-r from-emerald-50 to-white border border-emerald-200 rounded-xl shadow-xl px-4 py-3 md:px-6 md:py-4">
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

    @if(session('error') || isset($error))
    <div id="error-notification" class="fixed top-4 md:top-6 left-1/2 transform -translate-x-1/2 z-50 animate-slide-down w-[95%] md:max-w-md">
        <div class="bg-gradient-to-r from-red-50 to-white border border-red-200 rounded-xl shadow-xl px-4 py-3 md:px-6 md:py-4">
            <div class="flex items-center space-x-2 md:space-x-3">
                <div class="flex-shrink-0">
                    <div class="w-6 h-6 md:w-8 md:h-8 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xs md:text-sm"></i>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-900 text-sm md:text-base">Hitilafu!</p>
                    <p class="text-xs md:text-sm text-gray-600 truncate">{{ session('error') ?? $error }}</p>
                </div>
                <button onclick="closeNotification('error-notification')" class="text-gray-400 hover:text-gray-600 transition-colors flex-shrink-0">
                    <i class="fas fa-times text-sm md:text-base"></i>
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6 mb-4 md:mb-6 px-4 md:px-0">
        <!-- Active Companies -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-3 md:p-6 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex justify-between items-start mb-2 md:mb-4">
                <div class="flex-1">
                    <p class="text-xs md:text-sm font-semibold text-gray-500 mb-1 md:mb-2">Kampuni Active Sasa</p>
                    <p class="text-xl md:text-3xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">
                        {{ $stats['active_companies'] ?? 0 }}
                    </p>
                </div>
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 md:w-10 md:h-10 bg-green-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-building text-green-600 text-sm md:text-base"></i>
                    </div>
                </div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-1.5 md:h-2">
                <div class="h-1.5 md:h-2 rounded-full transition-all duration-1000 bg-gradient-to-r from-green-500 to-emerald-500" 
                    style="width: {{ $stats['active_percentage'] ?? 0 }}%">
                </div>
            </div>
            <div class="mt-2 md:mt-3 text-xs text-gray-500">
                <i class="fas fa-clock mr-1"></i> Dakika 10 za mwisho
            </div>
        </div>

        <!-- Total Companies -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-3 md:p-6 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex justify-between items-start mb-2 md:mb-4">
                <div class="flex-1">
                    <p class="text-xs md:text-sm font-semibold text-gray-500 mb-1 md:mb-2">Jumla ya Kampuni</p>
                    <p class="text-xl md:text-3xl font-bold text-gray-800">
                        {{ $stats['total_companies'] ?? 0 }}
                    </p>
                </div>
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 md:w-10 md:h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-chart-line text-blue-600 text-sm md:text-base"></i>
                    </div>
                </div>
            </div>
            <div class="mt-2 md:mt-3 text-xs text-gray-500">
                <i class="fas fa-building mr-1"></i> 
                <span class="text-green-600 font-medium">{{ $stats['active_companies'] ?? 0 }}</span> Active, 
                <span class="text-gray-400">{{ $stats['inactive_companies'] ?? 0 }}</span> Inactive
            </div>
        </div>

        <!-- Active Users -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-3 md:p-6 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex justify-between items-start mb-2 md:mb-4">
                <div class="flex-1">
                    <p class="text-xs md:text-sm font-semibold text-gray-500 mb-1 md:mb-2">Watumiaji Active Sasa</p>
                    <p class="text-xl md:text-3xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
                        {{ $stats['active_users_now'] ?? 0 }}
                    </p>
                </div>
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 md:w-10 md:h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-users text-purple-600 text-sm md:text-base"></i>
                    </div>
                </div>
            </div>
            <div class="mt-2 md:mt-3 text-xs text-gray-500">
                <i class="fas fa-user-check mr-1"></i> Wanaotumia sasa hivi
            </div>
        </div>

        <!-- Today's Logins -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-3 md:p-6 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex justify-between items-start mb-2 md:mb-4">
                <div class="flex-1">
                    <p class="text-xs md:text-sm font-semibold text-gray-500 mb-1 md:mb-2">Walioingia Leo</p>
                    <p class="text-xl md:text-3xl font-bold bg-gradient-to-r from-amber-600 to-orange-600 bg-clip-text text-transparent">
                        {{ $stats['today_logins'] ?? 0 }}
                    </p>
                </div>
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 md:w-10 md:h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-calendar-check text-amber-600 text-sm md:text-base"></i>
                    </div>
                </div>
            </div>
            <div class="mt-2 md:mt-3 text-xs text-gray-500">
                <i class="fas fa-calendar mr-1"></i> {{ now()->setTimezone('Africa/Nairobi')->format('d M, Y') }}
            </div>
        </div>
    </div>

    <!-- Search and Filter Bar - Auto Filter -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden mx-4 md:mx-0 mb-4">
        <div class="px-4 md:px-6 py-3 md:py-4">
            <div class="flex flex-col md:flex-row md:items-center gap-3">
                <!-- Search Input with Auto-filter -->
                <div class="flex-1 relative">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" 
                           id="searchInput" 
                           value="{{ request('search') }}"
                           placeholder="🔍 Tafuta kwa jina la kampuni au mmiliki..." 
                           class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all bg-gray-50 focus:bg-white">
                </div>
                
                <!-- Status Filter - Auto-filter -->
                <div class="relative w-full md:w-48">
                    <i class="fas fa-filter absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                    <select id="statusFilter" class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-gray-50 focus:bg-white">
                        <option value="">📊 Hali Zote ({{ $stats['total_companies'] ?? 0 }})</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>🟢 Active Sasa ({{ $stats['active_companies'] ?? 0 }})</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>⚪ Inactive ({{ $stats['inactive_companies'] ?? 0 }})</option>
                    </select>
                </div>
                
                <!-- Clear Filters Button -->
                <a href="{{ route('admin.reports.company-activity') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm transition-colors">
                    <i class="fas fa-times mr-2"></i>
                    <span>Ondoa Vichujio</span>
                </a>
            </div>
            
            <!-- Search Stats -->
            @if(request('search'))
            <div class="text-xs text-gray-500 mt-3 pt-2 border-t border-gray-100">
                <i class="fas fa-info-circle mr-1"></i>
                <span class="font-medium text-emerald-600">{{ $companies->total() }}</span> matokeo kwa tafuta "<span class="font-medium">{{ request('search') }}</span>"
            </div>
            @endif
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden mx-4 md:mx-0 mb-4">
        <div class="flex">
            <button data-tab="taarifa" class="tab-button flex-1 py-3 md:py-4 px-4 text-sm font-medium border-r border-gray-200 bg-emerald-50 text-emerald-700 transition-all duration-200">
                <i class="fas fa-table mr-2"></i> Hali ya Kampuni ({{ $stats['total_companies'] ?? 0 }})
            </button>
            <button data-tab="ripoti" class="tab-button flex-1 py-3 md:py-4 px-4 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-all duration-200">
                <i class="fas fa-calendar-check mr-2"></i> Kampuni Active Leo ({{ $todayActiveCompanies->count() ?? 0 }})
            </button>
        </div>
    </div>

    <!-- TAB 1: Taarifa (Companies Status) -->
    <div id="taarifa-tab-content" class="tab-content">
        <!-- Companies Table - Desktop View (Hidden on mobile, visible on md and up) -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden mx-4 md:mx-0 hidden md:block">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[800px]">
                    <thead>
                        <tr class="bg-gradient-to-r from-emerald-50 to-teal-50 border-b border-gray-200">
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">#</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Kampuni</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Mmiliki</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Hali Sasa</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Active</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Leo</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Jumla</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Shughuli ya Mwisho</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Kitendo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" id="companiesTableBody">
                        @forelse($companies as $index => $company)
                        <tr class="hover:bg-gradient-to-r hover:from-gray-50 hover:to-transparent transition-all duration-200 company-row">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-600 font-medium">{{ $companies->firstItem() + $index }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-building text-emerald-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900 company-name">{{ $company->company_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $company->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-700">{{ $company->owner_name }}</div>
                                <div class="text-xs text-gray-500">{{ $company->phone }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($company->is_active)
                                    <span class="inline-flex items-center space-x-1 px-3 py-1.5 bg-green-100 text-green-700 rounded-full text-xs font-medium border border-green-200">
                                        <i class="fas fa-circle text-2xs text-green-500"></i>
                                        <span>Active</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center space-x-1 px-3 py-1.5 bg-gray-100 text-gray-600 rounded-full text-xs font-medium border border-gray-200">
                                        <i class="fas fa-circle text-2xs text-gray-400"></i>
                                        <span>Inactive</span>
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex flex-col items-center gap-1">
                                    @if(($company->active_users_count ?? 0) > 0)
                                    <span class="inline-flex items-center justify-center px-2.5 py-1.5 bg-emerald-100 text-emerald-700 rounded-lg text-xs font-medium">
                                        <i class="fas fa-user-check mr-1 text-2xs"></i>
                                        {{ $company->active_users_count ?? 0 }}
                                    </span>
                                    @endif
                                    @if(($company->active_employees_count ?? 0) > 0)
                                    <span class="inline-flex items-center justify-center px-2.5 py-1.5 bg-blue-100 text-blue-700 rounded-lg text-xs font-medium">
                                        <i class="fas fa-user-friends mr-1 text-2xs"></i>
                                        {{ $company->active_employees_count ?? 0 }}
                                    </span>
                                    @endif
                                    @if(($company->active_users_count ?? 0) == 0 && ($company->active_employees_count ?? 0) == 0)
                                    <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center justify-center px-2.5 py-1.5 {{ ($company->today_login_count ?? 0) > 0 ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-500' }} rounded-lg text-xs font-medium">
                                    {{ $company->today_login_count ?? 0 }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-sm font-semibold text-gray-800">{{ $company->total_login_count ?? 0 }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($company->last_login_date)
                                    @php
                                        $lastLoginDate = \Carbon\Carbon::parse($company->last_login_date)->setTimezone('Africa/Nairobi');
                                    @endphp
                                    <div class="text-sm" title="{{ $lastLoginDate->format('d/m/Y H:i:s') }}">
                                        @if($lastLoginDate->isToday())
                                            <span class="text-green-600 font-medium">{{ $lastLoginDate->format('H:i:s') }}</span>
                                            <div class="text-xs text-gray-400">Leo</div>
                                        @elseif($lastLoginDate->isYesterday())
                                            <span class="text-blue-600">{{ $lastLoginDate->format('d/m H:i') }}</span>
                                        @else
                                            <span class="text-gray-600">{{ $lastLoginDate->format('d/m/Y H:i') }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <button onclick="showCompanyDetails({{ $company->id }}, '{{ addslashes($company->company_name) }}')" 
                                        class="inline-flex items-center px-3 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg text-xs font-medium transition-all duration-200">
                                    <i class="fas fa-chart-line mr-1"></i>
                                    <span>Shughuli</span>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-building text-gray-400 text-xl"></i>
                                    </div>
                                    <p class="text-gray-500 text-sm">Hakuna makampuni yaliyopatikana</p>
                                    @if(request('search') || request('status'))
                                    <a href="{{ route('admin.reports.company-activity') }}" class="mt-2 text-emerald-600 hover:text-emerald-800 text-sm">
                                        <i class="fas fa-times mr-1"></i> Ondoa vichujio
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards View - Grid Layout for better column/row display -->
            <div class="md:hidden p-4" id="mobileCardsContainer">
                <div class="grid grid-cols-1 gap-4">
                    @forelse($companies as $index => $company)
                    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-200 company-card" 
                         data-name="{{ strtolower($company->company_name) }}"
                         data-owner="{{ strtolower($company->owner_name) }}">
                        <!-- Card Header -->
                        <div class="bg-gradient-to-r from-gray-50 to-white px-4 py-3 border-b border-gray-100">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-building text-emerald-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900 text-sm company-name">{{ $company->company_name }}</h3>
                                        <p class="text-xs text-gray-500">{{ $company->email }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs text-gray-400">#{{ $companies->firstItem() + $index }}</span>
                                    @if($company->is_active)
                                        <div class="mt-1">
                                            <span class="inline-flex items-center px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs">
                                                <i class="fas fa-circle text-2xs mr-1"></i>Active
                                            </span>
                                        </div>
                                    @else
                                        <div class="mt-1">
                                            <span class="inline-flex items-center px-2 py-0.5 bg-gray-100 text-gray-500 rounded-full text-xs">
                                                <i class="fas fa-circle text-2xs mr-1"></i>Inactive
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-4 space-y-3">
                            <!-- Owner Info Row -->
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-user-circle text-gray-400 text-sm w-4"></i>
                                    <span class="text-gray-600">Mmiliki:</span>
                                </div>
                                <div class="text-right">
                                    <span class="font-medium text-gray-800">{{ $company->owner_name }}</span>
                                    <div class="text-xs text-gray-500">{{ $company->phone }}</div>
                                </div>
                            </div>

                            <!-- Stats Grid - 2 columns for better layout -->
                            <div class="grid grid-cols-2 gap-3 pt-2">
                                <!-- Active Users Stats -->
                                <div class="bg-gradient-to-r from-emerald-50 to-transparent rounded-lg p-2">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-2xs text-gray-500">Active Sasa</span>
                                        @if(($company->active_users_count ?? 0) > 0 || ($company->active_employees_count ?? 0) > 0)
                                            <i class="fas fa-circle text-2xs text-emerald-500"></i>
                                        @endif
                                    </div>
                                    <div class="flex gap-2">
                                        @if(($company->active_users_count ?? 0) > 0)
                                        <div class="flex items-center space-x-1">
                                            <i class="fas fa-user-tie text-2xs text-emerald-600"></i>
                                            <span class="text-sm font-bold text-emerald-600">{{ $company->active_users_count }}</span>
                                        </div>
                                        @endif
                                        @if(($company->active_employees_count ?? 0) > 0)
                                        <div class="flex items-center space-x-1">
                                            <i class="fas fa-user text-2xs text-blue-600"></i>
                                            <span class="text-sm font-bold text-blue-600">{{ $company->active_employees_count }}</span>
                                        </div>
                                        @endif
                                        @if(($company->active_users_count ?? 0) == 0 && ($company->active_employees_count ?? 0) == 0)
                                        <span class="text-xs text-gray-400">Hakuna</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Login Stats -->
                                <div class="bg-gradient-to-r from-blue-50 to-transparent rounded-lg p-2">
                                    <div class="text-2xs text-gray-500 mb-1">Leo / Jumla</div>
                                    <div class="flex items-baseline gap-2">
                                        <span class="text-lg font-bold text-amber-600">{{ $company->today_login_count ?? 0 }}</span>
                                        <span class="text-xs text-gray-400">/</span>
                                        <span class="text-sm font-semibold text-gray-600">{{ $company->total_login_count ?? 0 }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Last Activity Row -->
                            <div class="bg-gray-50 rounded-lg p-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <i class="fas fa-clock text-emerald-600 text-xs"></i>
                                        <span class="text-2xs text-gray-500">Shughuli ya mwisho:</span>
                                    </div>
                                    <div class="text-right">
                                        @if($company->last_login_date)
                                            @php
                                                $lastLoginDate = \Carbon\Carbon::parse($company->last_login_date)->setTimezone('Africa/Nairobi');
                                            @endphp
                                            @if($lastLoginDate->isToday())
                                                <span class="text-green-600 font-medium text-xs">{{ $lastLoginDate->format('H:i:s') }}</span>
                                                <div class="text-2xs text-gray-400">Leo</div>
                                            @elseif($lastLoginDate->isYesterday())
                                                <span class="text-blue-600 text-xs">{{ $lastLoginDate->format('H:i') }}</span>
                                                <div class="text-2xs text-gray-400">Jana</div>
                                            @else
                                                <span class="text-gray-600 text-xs">{{ $lastLoginDate->format('d/m/Y') }}</span>
                                                <div class="text-2xs text-gray-400">{{ $lastLoginDate->format('H:i') }}</div>
                                            @endif
                                        @else
                                            <span class="text-xs text-gray-400">Hajawahi kuingia</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Action Button -->
                            <button onclick="showCompanyDetails({{ $company->id }}, '{{ addslashes($company->company_name) }}')"
                                    class="w-full bg-gradient-to-r from-emerald-500 to-emerald-600 text-white text-xs px-3 py-2.5 rounded-xl transition-all duration-200 flex items-center justify-center space-x-2 hover:from-emerald-600 hover:to-emerald-700">
                                <i class="fas fa-chart-line"></i>
                                <span>Angalia Shughuli za Kina</span>
                            </button>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 bg-white rounded-xl">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-building text-gray-400 text-xl"></i>
                        </div>
                        <p class="text-gray-500 text-sm">Hakuna makampuni yaliyopatikana</p>
                        @if(request('search') || request('status'))
                        <a href="{{ route('admin.reports.company-activity') }}" class="inline-block mt-3 text-emerald-600 hover:text-emerald-800 text-sm">
                            <i class="fas fa-times mr-1"></i> Ondoa vichujio
                        </a>
                        @endif
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Pagination -->
            @if(method_exists($companies, 'hasPages') && $companies->hasPages())
            <div class="px-4 md:px-6 py-3 md:py-4 border-t border-gray-100 bg-gray-50/50">
                <div class="flex flex-col md:flex-row md:items-center justify-between space-y-2 md:space-y-0">
                    <div class="text-sm text-gray-600">
                        {{ $companies->firstItem() }} - {{ $companies->lastItem() }} ya {{ $companies->total() }}
                        @if(request('search') || request('status'))
                        <span class="text-gray-400 ml-1">(imechujwa)</span>
                        @endif
                    </div>
                    <div class="flex space-x-2 overflow-x-auto pb-1 md:pb-0">
                        {{ $companies->onEachSide(1)->withQueryString()->links('vendor.pagination.tailwind') }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- TAB 2: Ripoti (Active Companies Today) -->
    <div id="ripoti-tab-content" class="tab-content hidden">
        <div class="grid grid-cols-1 gap-4 px-4 md:px-0">
            <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                <div class="px-4 md:px-6 py-3 md:py-4 border-b border-gray-100 bg-gradient-to-r from-emerald-50 to-teal-50">
                    <h3 class="text-base md:text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-calendar-check mr-2 md:mr-3 text-emerald-600 text-sm md:text-base"></i>
                        Kampuni Zilizoingia Leo - {{ now()->setTimezone('Africa/Nairobi')->format('d M, Y') }}
                    </h3>
                </div>
                
                <div class="p-4 md:p-6">
                    <!-- Summary Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6">
                        <div class="bg-gradient-to-r from-emerald-50 to-transparent p-4 rounded-xl border border-emerald-100">
                            <p class="text-xs text-gray-600 mb-1">Kampuni Zilizoingia Leo</p>
                            <p class="text-2xl font-bold text-emerald-700">{{ $todayActiveCompanies->count() }}</p>
                        </div>
                        <div class="bg-gradient-to-r from-blue-50 to-transparent p-4 rounded-xl border border-blue-100">
                            <p class="text-xs text-gray-600 mb-1">Watumiaji Walioingia Leo</p>
                            <p class="text-2xl font-bold text-blue-700">{{ $stats['today_unique_users'] ?? 0 }}</p>
                        </div>
                        <div class="bg-gradient-to-r from-purple-50 to-transparent p-4 rounded-xl border border-purple-100">
                            <p class="text-xs text-gray-600 mb-1">Jumla ya Kuingia Leo</p>
                            <p class="text-2xl font-bold text-purple-700">{{ $stats['today_logins'] ?? 0 }}</p>
                        </div>
                    </div>

                    <!-- Desktop Table View -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-sm min-w-[600px]">
                            <thead class="bg-gradient-to-r from-emerald-50 to-teal-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-emerald-800">#</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-emerald-800">Kampuni</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-emerald-800">Mmiliki</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-emerald-800">Mara Leo</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-emerald-800">Active Sasa</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-emerald-800">Mara ya Mwisho</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($todayActiveCompanies as $index => $company)
                                <tr class="hover:bg-gray-50 transition-all duration-200">
                                    <td class="px-4 py-3 text-gray-600">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3">
                                        <button onclick="showCompanyDetails({{ $company->id }}, '{{ addslashes($company->company_name) }}')" 
                                                class="text-emerald-600 hover:text-emerald-800 hover:underline font-medium text-left">
                                            {{ $company->company_name }}
                                        </button>
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">{{ $company->owner_name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center justify-center px-2.5 py-1.5 bg-amber-100 text-amber-700 rounded-lg text-xs font-medium">
                                            {{ $company->today_login_count ?? 0 }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($company->is_active)
                                            <span class="bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs">
                                                <i class="fas fa-circle text-2xs mr-1"></i>Active
                                            </span>
                                        @else
                                            <span class="bg-gray-100 text-gray-500 px-2 py-1 rounded-full text-xs">
                                                <i class="fas fa-circle text-2xs mr-1"></i>Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">
                                        @if($company->last_login_date)
                                            @php
                                                $lastLoginDate = \Carbon\Carbon::parse($company->last_login_date)->setTimezone('Africa/Nairobi');
                                            @endphp
                                            {{ $lastLoginDate->format('H:i:s') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                        Hakuna kampuni zilizoingia leo
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Cards View for Tab 2 -->
                    <div class="md:hidden space-y-3">
                        @forelse($todayActiveCompanies as $index => $company)
                        <div class="bg-gradient-to-r from-gray-50 to-white rounded-xl p-4 border border-gray-100">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <button onclick="showCompanyDetails({{ $company->id }}, '{{ addslashes($company->company_name) }}')" 
                                            class="text-emerald-600 font-semibold text-sm hover:underline text-left">
                                        {{ $company->company_name }}
                                    </button>
                                    <p class="text-xs text-gray-500 mt-1">{{ $company->owner_name ?? '-' }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center justify-center px-2 py-1 bg-amber-100 text-amber-700 rounded-lg text-xs font-medium">
                                        <i class="fas fa-sign-in-alt mr-1 text-2xs"></i>
                                        {{ $company->today_login_count ?? 0 }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                                <div>
                                    @if($company->is_active)
                                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded-full text-2xs">
                                            <i class="fas fa-circle text-2xs mr-1"></i>Active Sasa
                                        </span>
                                    @else
                                        <span class="bg-gray-100 text-gray-500 px-2 py-1 rounded-full text-2xs">
                                            <i class="fas fa-circle text-2xs mr-1"></i>Inactive
                                        </span>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500">
                                    <i class="fas fa-clock mr-1"></i>
                                    @if($company->last_login_date)
                                        @php
                                            $lastLoginDate = \Carbon\Carbon::parse($company->last_login_date)->setTimezone('Africa/Nairobi');
                                        @endphp
                                        {{ $lastLoginDate->format('H:i:s') }}
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8 bg-white rounded-xl">
                            <p class="text-gray-500 text-sm">Hakuna kampuni zilizoingia leo</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Company Details Modal -->
<div id="companyDetailsModal" class="fixed inset-0 hidden items-center justify-center bg-black/50 z-50 px-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] md:max-h-[85vh] overflow-hidden animate-scale-in">
        <div class="bg-gradient-to-r from-emerald-600 to-teal-700 px-4 md:px-6 py-3 md:py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2 md:space-x-3">
                    <div class="w-8 h-8 md:w-10 md:h-10 bg-white/20 rounded-xl flex items-center justify-center">
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

        <div class="p-4 md:p-6 overflow-y-auto max-h-[calc(90vh-120px)] md:max-h-[calc(85vh-120px)]" id="companyDetailsContent">
            <div class="text-center py-12">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-emerald-600 border-t-transparent"></div>
                <p class="text-gray-500 text-sm mt-3">Inapakia taarifa...</p>
            </div>
        </div>

        <div class="bg-gray-50 px-4 md:px-6 py-3 md:py-4 border-t border-gray-100">
            <div class="flex justify-end">
                <button onclick="hideModal('companyDetailsModal')" 
                        class="px-4 md:px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl transition-colors flex items-center space-x-2">
                    <i class="fas fa-times text-xs"></i>
                    <span>Funga</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Styles -->
<style>
@keyframes slideDown {
    from { opacity: 0; transform: translate(-50%, -20px); }
    to { opacity: 1; transform: translate(-50%, 0); }
}

@keyframes scaleIn {
    from { opacity: 0; transform: scale(0.95) translateY(-10px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.animate-slide-down { animation: slideDown 0.3s ease-out; }
.animate-scale-in { animation: scaleIn 0.2s ease-out; }
.pulse { animation: pulse 2s infinite; }

.text-2xs { font-size: 0.625rem; line-height: 0.875rem; }

/* Custom scrollbar */
.overflow-y-auto::-webkit-scrollbar { width: 6px; }
.overflow-y-auto::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 3px; }
.overflow-y-auto::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
.overflow-y-auto::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

/* Mobile optimizations */
@media (max-width: 768px) {
    button, .clickable { min-height: 44px; min-width: 44px; }
}
</style>

<!-- JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let autoRefreshInterval;

document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide notifications
    const successNotification = document.getElementById('success-notification');
    if (successNotification) setTimeout(() => closeNotification('success-notification'), 5000);
    
    const errorNotification = document.getElementById('error-notification');
    if (errorNotification) setTimeout(() => closeNotification('error-notification'), 5000);
    
    // Update time
    updateEastAfricanTime();
    setInterval(updateEastAfricanTime, 1000);
    
    // Initialize tabs
    initializeTabs();
    
    // Auto-filter on input change
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    
    if (searchInput) {
        searchInput.addEventListener('input', debounce(applyFilters, 500));
    }
    if (statusFilter) {
        statusFilter.addEventListener('change', applyFilters);
    }
    
    // Auto-refresh every 30 seconds
    startAutoRefresh();
});

function updateEastAfricanTime() {
    const timeElement = document.getElementById('current-time');
    if (timeElement) {
        const now = new Date();
        const utc = now.getTime() + (now.getTimezoneOffset() * 60000);
        const eastAfricaTime = new Date(utc + (3600000 * 3));
        timeElement.textContent = eastAfricaTime.toLocaleTimeString('en-US', { hour12: false });
    }
}

function startAutoRefresh() {
    if (autoRefreshInterval) clearInterval(autoRefreshInterval);
    autoRefreshInterval = setInterval(() => {
        refreshData();
    }, 30000);
}

function refreshData() {
    const url = new URL(window.location.href);
    fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(response => {
            if (response.ok) {
                window.location.reload();
            }
        })
        .catch(error => console.error('Auto-refresh failed:', error));
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function applyFilters() {
    const search = document.getElementById('searchInput').value;
    const status = document.getElementById('statusFilter').value;
    
    let url = new URL(window.location.href);
    if (search) url.searchParams.set('search', search);
    else url.searchParams.delete('search');
    
    if (status) url.searchParams.set('status', status);
    else url.searchParams.delete('status');
    
    url.searchParams.delete('page');
    window.location.href = url.toString();
}

function initializeTabs() {
    const tabs = document.querySelectorAll('.tab-button');
    const savedTab = sessionStorage.getItem('company_activity_tab') || 'taarifa';
    
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const tabName = this.dataset.tab;
            
            tabs.forEach(t => {
                t.classList.remove('bg-emerald-50', 'text-emerald-700');
                t.classList.add('text-gray-600', 'hover:bg-gray-50');
            });
            this.classList.add('bg-emerald-50', 'text-emerald-700');
            this.classList.remove('text-gray-600', 'hover:bg-gray-50');
            
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            document.getElementById(`${tabName}-tab-content`).classList.remove('hidden');
            sessionStorage.setItem('company_activity_tab', tabName);
        });
    });
    
    const activeTab = document.querySelector(`.tab-button[data-tab="${savedTab}"]`);
    if (activeTab) activeTab.click();
}

function showCompanyDetails(companyId, companyName) {
    document.getElementById('modalCompanyName').textContent = companyName;
    showModal('companyDetailsModal');
    
    document.getElementById('companyDetailsContent').innerHTML = `
        <div class="text-center py-12">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-emerald-600 border-t-transparent"></div>
            <p class="text-gray-500 text-sm mt-3">Inapakia taarifa za ${companyName}...</p>
        </div>
    `;

    fetch(`/admin/reports/company-activity/${companyId}/details`, {
        method: 'GET',
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) displayCompanyDetails(data);
        else throw new Error(data.message || 'Unknown error');
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('companyDetailsContent').innerHTML = `
            <div class="text-center py-8">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <p class="text-red-600 text-sm mb-2">Hitilafu wakati wa kupakia data.</p>
                <button onclick="showCompanyDetails(${companyId}, '${companyName}')" 
                        class="px-4 py-2 bg-emerald-600 text-white text-sm rounded-lg hover:bg-emerald-700">
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
                timeZone: 'Africa/Nairobi', dateStyle: 'short', timeStyle: 'short' 
            }) : '-';
            
            const isActive = user.is_active ? 
                '<span class="bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs font-medium"><i class="fas fa-circle text-2xs mr-1"></i>Active</span>' : 
                '<span class="bg-gray-100 text-gray-500 px-2 py-1 rounded-full text-xs font-medium"><i class="fas fa-circle text-2xs mr-1"></i>Inactive</span>';
            
            const roleIcon = user.type === 'Boss' ? 'fa-user-tie' : 'fa-user';
            const roleColor = user.type === 'Boss' ? 'text-emerald-600' : 'text-blue-600';
            
            usersHtml += `
                <tr class="border-b border-gray-100 hover:bg-gray-50">
                    <td class="px-3 py-2">
                        <div class="flex items-center gap-2">
                            <i class="fas ${roleIcon} ${roleColor} text-xs"></i>
                            <span class="text-sm font-medium">${user.name || '-'}</span>
                        </div>
                        <div class="text-xs text-gray-500 mt-0.5">${user.type}</div>
                    </td>
                    <td class="px-3 py-2 text-sm">${user.username || '-'}</td>
                    <td class="px-3 py-2 text-center">${isActive}</td>
                    <td class="px-3 py-2 text-sm text-center">${user.login_count || 0}</td>
                    <td class="px-3 py-2 text-sm">${lastActivity}</td>
                </tr>
            `;
        });
    } else {
        usersHtml = `<tr><td colspan="5" class="px-3 py-4 text-center text-gray-500">Hakuna watumiaji waliopatikana</td></tr>`;
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
                                plugins: { legend: { display: false } },
                                scales: {
                                    y: { beginAtZero: true, stepSize: 1, grid: { color: 'rgba(0,0,0,0.05)' } },
                                    x: { grid: { display: false } }
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
        timeZone: 'Africa/Nairobi', dateStyle: 'medium', timeStyle: 'short' 
    }) : '-';

    const html = `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 mb-6">
            <div class="bg-gradient-to-r from-gray-50 to-white rounded-xl p-4 border border-gray-100">
                <h6 class="font-semibold text-gray-900 text-sm mb-3 flex items-center">
                    <i class="fas fa-building mr-2 text-emerald-600"></i> Taarifa za Kampuni
                </h6>
                <div class="space-y-2">
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-xs text-gray-500">Jina:</span>
                        <span class="text-xs font-medium text-gray-800">${company.name || '-'}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-xs text-gray-500">Mmiliki:</span>
                        <span class="text-xs font-medium text-gray-800">${company.owner_name || '-'}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-xs text-gray-500">Simu:</span>
                        <span class="text-xs font-medium text-gray-800">${company.phone || '-'}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-xs text-gray-500">Barua Pepe:</span>
                        <span class="text-xs font-medium text-gray-800">${company.email || '-'}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-xs text-gray-500">Package:</span>
                        <span class="text-xs font-medium text-emerald-600">${company.package || 'Free Trial'}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-xs text-gray-500">Package Itaisha:</span>
                        <span class="text-xs font-medium text-amber-600">${company.package_end || 'N/A'}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-xs text-gray-500">Hali Sasa:</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${company.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'}">
                            <i class="fas fa-circle text-2xs mr-1"></i> ${company.is_active ? 'Active' : 'Inactive'}
                        </span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-xs text-gray-500">Active Sasa:</span>
                        <span class="text-xs font-bold text-emerald-600">${company.active_users || 0} Boss, ${company.active_employees || 0} Wafanyakazi</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-xs text-gray-500">Jumla Watumiaji:</span>
                        <span class="text-xs font-medium">${company.total_users || 0} Boss, ${company.total_employees || 0} Wafanyakazi</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-xs text-gray-500">Walioingia Leo:</span>
                        <span class="text-xs font-medium">${company.daily_active_users || 0}</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-xs text-gray-500">Mara ya Mwisho:</span>
                        <span class="text-xs font-medium">${lastLoginDate}</span>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-gray-50 to-white rounded-xl p-4 border border-gray-100">
                <h6 class="font-semibold text-gray-900 text-sm mb-3 flex items-center">
                    <i class="fas fa-chart-line mr-2 text-emerald-600"></i> Shughuli za Wiki
                </h6>
                ${weeklyChartHtml}
            </div>
        </div>

        <div class="bg-gradient-to-r from-gray-50 to-white rounded-xl p-4 border border-gray-100">
            <h6 class="font-semibold text-gray-900 text-sm mb-3 flex items-center">
                <i class="fas fa-users mr-2 text-emerald-600"></i> Watumiaji wa Kampuni
            </h6>
            <div class="overflow-x-auto">
                <table class="w-full text-sm min-w-[500px]">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700">Jina / Aina</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700">Mtumiaji</th>
                            <th class="px-3 py-2 text-center text-xs font-semibold text-gray-700">Hali</th>
                            <th class="px-3 py-2 text-center text-xs font-semibold text-gray-700">Mara</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700">Shughuli ya Mwisho</th>
                        </tr>
                    </thead>
                    <tbody>${usersHtml}</tbody>
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

function closeNotification(notificationId = 'success-notification') {
    const notification = document.getElementById(notificationId);
    if (notification) notification.remove();
}

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        const openModal = document.querySelector('[id$="Modal"]:not(.hidden)');
        if (openModal) hideModal(openModal.id);
    }
});

document.addEventListener('click', (e) => {
    const openModal = document.querySelector('[id$="Modal"]:not(.hidden)');
    if (openModal && e.target === openModal) hideModal(openModal.id);
});
</script>
@endsection