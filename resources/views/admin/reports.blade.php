@extends('layouts.admin')

@section('title', 'Ripoti - MAUZO')

@section('page-title', 'Ripoti')
@section('page-subtitle', 'Uchambuzi wa Takwimu na Taarifa')

@section('content')
<div class="min-h-screen bg-gray-50/30">
    <!-- Header Section -->
    <div class="mb-4 md:mb-8 px-4 md:px-0">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div class="mb-3 md:mb-0">
                <h1 class="text-lg md:text-2xl font-bold text-gray-900">📊 Ripoti za Mfumo</h1>
                <p class="text-gray-600 text-xs md:text-base mt-1">Uchambuzi wa kina wa takwimu na shughuli za mfumo</p>
            </div>
            <div class="flex items-center space-x-2 md:space-x-4">
                <div class="bg-white px-3 py-2 md:px-4 md:py-2 rounded-lg shadow-sm border w-full md:w-auto">
                    <div class="text-xs md:text-sm text-gray-500">Jumla ya Makampuni</div>
                    <div class="text-xl md:text-2xl font-bold text-emerald-600">{{ $totalCompanies ?? 0 }}</div>
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
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6 mb-4 md:mb-6 px-4 md:px-0">
        <!-- Total Companies -->
        <div class="bg-white rounded-lg md:rounded-2xl shadow-sm border border-gray-100 p-3 md:p-6 hover-lift transition-all duration-200">
            <div class="flex justify-between items-start mb-2 md:mb-4">
                <div class="flex-1">
                    <p class="text-xs md:text-sm font-semibold text-gray-600 mb-1 md:mb-2">Jumla ya Makampuni</p>
                    <p class="text-lg md:text-2xl font-bold text-gray-900">{{ $totalCompanies ?? 0 }}</p>
                </div>
                <div class="flex-shrink-0">
                    <span class="text-xs font-semibold px-2 py-0.5 md:px-3 md:py-1 rounded-full bg-blue-100 text-blue-800">
                        +{{ $newCompaniesThisWeek ?? 5 }} wiki
                    </span>
                </div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-1.5 md:h-2">
                <div class="h-1.5 md:h-2 rounded-full transition-all duration-1000 bg-blue-500" 
                    style="width: {{ min(($totalCompanies / 100) * 100, 100) }}%">
                </div>
            </div>
        </div>

        <!-- Free Trial Companies -->
        <div class="bg-white rounded-lg md:rounded-2xl shadow-sm border border-gray-100 p-3 md:p-6 hover-lift transition-all duration-200">
            <div class="flex justify-between items-start mb-2 md:mb-4">
                <div class="flex-1">
                    <p class="text-xs md:text-sm font-semibold text-gray-600 mb-1 md:mb-2">Free Trial</p>
                    <p class="text-lg md:text-2xl font-bold text-gray-900">{{ $freeTrialCompanies ?? 0 }}</p>
                </div>
                <div class="flex-shrink-0">
                    <span class="text-xs font-semibold px-2 py-0.5 md:px-3 md:py-1 rounded-full bg-orange-100 text-orange-800">
                        {{ $freeTrialPercentage ?? 0 }}%
                    </span>
                </div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-1.5 md:h-2">
                <div class="h-1.5 md:h-2 rounded-full transition-all duration-1000 bg-orange-500" 
                    style="width: {{ $freeTrialPercentage ?? 0 }}%">
                </div>
            </div>
        </div>

        <!-- Paid Package Companies -->
        <div class="bg-white rounded-lg md:rounded-2xl shadow-sm border border-gray-100 p-3 md:p-6 hover-lift transition-all duration-200">
            <div class="flex justify-between items-start mb-2 md:mb-4">
                <div class="flex-1">
                    <p class="text-xs md:text-sm font-semibold text-gray-600 mb-1 md:mb-2">Wenye Kifurushi</p>
                    <p class="text-lg md:text-2xl font-bold text-gray-900">{{ $paidPackageCompanies ?? 0 }}</p>
                </div>
                <div class="flex-shrink-0">
                    <span class="text-xs font-semibold px-2 py-0.5 md:px-3 md:py-1 rounded-full bg-green-100 text-green-800">
                        +{{ $newPaidThisMonth ?? 2 }} mwezi
                    </span>
                </div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-1.5 md:h-2">
                <div class="h-1.5 md:h-2 rounded-full transition-all duration-1000 bg-green-500" 
                    style="width: {{ $paidPackagePercentage ?? 0 }}%">
                </div>
            </div>
        </div>

        <!-- Companies Registered Today -->
        <div class="bg-white rounded-lg md:rounded-2xl shadow-sm border border-gray-100 p-3 md:p-6 hover-lift transition-all duration-200">
            <div class="flex justify-between items-start mb-2 md:mb-4">
                <div class="flex-1">
                    <p class="text-xs md:text-sm font-semibold text-gray-600 mb-1 md:mb-2">Usajili wa Leo</p>
                    <p class="text-lg md:text-2xl font-bold text-gray-900">{{ $todayRegistrations ?? 0 }}</p>
                </div>
                <div class="flex-shrink-0">
                    <span class="text-xs font-semibold px-2 py-0.5 md:px-3 md:py-1 rounded-full bg-purple-100 text-purple-800">
                        {{ $todayGrowth ?? 0 }}%
                    </span>
                </div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-1.5 md:h-2">
                <div class="h-1.5 md:h-2 rounded-full transition-all duration-1000 bg-purple-500" 
                    style="width: {{ min(($todayRegistrations / 10) * 100, 100) }}%">
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6 px-4 md:px-0">
        <!-- Recent Registrations -->
        <div class="bg-white rounded-lg md:rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-4 md:px-6 py-3 md:py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                <div class="flex flex-col md:flex-row md:items-center justify-between space-y-2 md:space-y-0">
                    <h3 class="text-base md:text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-history mr-2 md:mr-3 text-emerald-600 text-sm md:text-base"></i>
                        Usajili wa Hivi Karibuni
                    </h3>
                    <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 md:px-3 md:py-1 rounded-full">Saa 24 zilizopita</span>
                </div>
            </div>
            
            <div class="p-3 md:p-6">
                <div class="space-y-3 md:space-y-4">
                    @forelse($recentRegistrations as $registration)
                    <div class="flex items-center justify-between p-2 md:p-4 hover:bg-gray-50 rounded-lg md:rounded-xl transition-all duration-200 group">
                        <div class="flex items-center space-x-2 md:space-x-4 min-w-0 flex-1">
                            <div class="w-8 h-8 md:w-12 md:h-12 bg-gradient-to-br from-emerald-100 to-blue-100 rounded-lg md:rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-200 flex-shrink-0">
                                <span class="text-emerald-600 font-bold text-sm md:text-lg">
                                    {{ substr($registration->company_name, 0, 1) }}
                                </span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="font-semibold text-gray-900 text-sm md:text-base truncate">{{ $registration->company_name }}</p>
                                <p class="text-xs md:text-sm text-gray-500 truncate">{{ $registration->owner_name }}</p>
                                <p class="text-xs text-gray-400">{{ $registration->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0 ml-2">
                            <span class="inline-flex items-center px-2 py-0.5 md:px-3 md:py-1 rounded-full text-xs font-medium
                                @if($registration->package == 'Free Trial 14 days') bg-orange-100 text-orange-800
                                @elseif($registration->package) bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ $registration->package ?? 'Hakuna' }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 md:py-8">
                        <div class="w-10 h-10 md:w-16 md:h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-2 md:mb-4">
                            <i class="fas fa-building text-gray-400 text-base md:text-xl"></i>
                        </div>
                        <p class="text-gray-500 text-sm md:text-base">Hakuna usajili wa hivi karibuni</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Package Distribution -->
        <div class="bg-white rounded-lg md:rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-4 md:px-6 py-3 md:py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                <h3 class="text-base md:text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-chart-pie mr-2 md:mr-3 text-emerald-600 text-sm md:text-base"></i>
                    Usambazaji wa Vifurushi
                </h3>
            </div>
            
            <div class="p-3 md:p-6">
                <div class="space-y-3 md:space-y-6">
                    <!-- Free Trial -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2 md:space-x-3 min-w-0 flex-1">
                            <div class="w-8 h-8 md:w-10 md:h-10 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-clock text-orange-600 text-xs md:text-sm"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="font-semibold text-gray-900 text-sm md:text-base truncate">Free Trial</p>
                                <p class="text-xs md:text-sm text-gray-500">{{ $freeTrialCompanies ?? 0 }} kampuni</p>
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0 ml-2">
                            <p class="text-base md:text-lg font-bold text-orange-600">{{ $freeTrialPercentage ?? 0 }}%</p>
                        </div>
                    </div>

                    <!-- 180 Days Package -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2 md:space-x-3 min-w-0 flex-1">
                            <div class="w-8 h-8 md:w-10 md:h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-rocket text-green-600 text-xs md:text-sm"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="font-semibold text-gray-900 text-sm md:text-base truncate">Siku 180</p>
                                <p class="text-xs md:text-sm text-gray-500">{{ $package180Companies ?? 0 }} kampuni</p>
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0 ml-2">
                            <p class="text-base md:text-lg font-bold text-green-600">{{ $package180Percentage ?? 0 }}%</p>
                        </div>
                    </div>

                    <!-- 366 Days Package -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2 md:space-x-3 min-w-0 flex-1">
                            <div class="w-8 h-8 md:w-10 md:h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-crown text-blue-600 text-xs md:text-sm"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="font-semibold text-gray-900 text-sm md:text-base truncate">Siku 366</p>
                                <p class="text-xs md:text-sm text-gray-500">{{ $package366Companies ?? 0 }} kampuni</p>
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0 ml-2">
                            <p class="text-base md:text-lg font-bold text-blue-600">{{ $package366Percentage ?? 0 }}%</p>
                        </div>
                    </div>

                    <!-- No Package -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2 md:space-x-3 min-w-0 flex-1">
                            <div class="w-8 h-8 md:w-10 md:h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-times text-gray-400 text-xs md:text-sm"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="font-semibold text-gray-900 text-sm md:text-base truncate">Hakuna Kifurushi</p>
                                <p class="text-xs md:text-sm text-gray-500">{{ $noPackageCompanies ?? 0 }} kampuni</p>
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0 ml-2">
                            <p class="text-base md:text-lg font-bold text-gray-600">{{ $noPackagePercentage ?? 0 }}%</p>
                        </div>
                    </div>
                </div>

                <!-- Progress Chart -->
                <div class="mt-4 md:mt-6 bg-gray-50 rounded-lg md:rounded-xl p-3 md:p-4">
                    <div class="flex h-3 md:h-4 rounded-full overflow-hidden">
                        <div class="bg-orange-500" style="width: {{ $freeTrialPercentage ?? 0 }}%"></div>
                        <div class="bg-green-500" style="width: {{ $package180Percentage ?? 0 }}%"></div>
                        <div class="bg-blue-500" style="width: {{ $package366Percentage ?? 0 }}%"></div>
                        <div class="bg-gray-400" style="width: {{ $noPackagePercentage ?? 0 }}%"></div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500 mt-2">
                        <span class="truncate">Free Trial</span>
                        <span class="truncate px-1">Siku 180</span>
                        <span class="truncate px-1">Siku 366</span>
                        <span class="truncate">Hakuna</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Download Reports Section -->
    <div class="mt-4 md:mt-6 bg-white rounded-lg md:rounded-2xl shadow-sm border border-gray-100 overflow-hidden mx-4 md:mx-0">
        <div class="px-4 md:px-6 py-3 md:py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
            <h3 class="text-base md:text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-file-download mr-2 md:mr-3 text-emerald-600 text-sm md:text-base"></i>
                Pakua Ripoti za Makampuni
            </h3>
        </div>
        
        <div class="p-3 md:p-6">
            <form action="{{ route('admin.reports.download-companies') }}" method="GET" class="space-y-4 md:space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 md:gap-6">
                    <!-- Period Selection -->
                    <div>
                        <label for="period" class="block text-xs md:text-sm font-semibold text-gray-700 mb-1 md:mb-2 flex items-center space-x-2">
                            <i class="fas fa-calendar-alt text-emerald-600 text-xs md:text-sm"></i>
                            <span>Chagua Kipindi</span>
                        </label>
                        <select name="period" id="period" 
                                class="w-full border border-gray-300 rounded-lg md:rounded-xl px-3 md:px-4 py-2 md:py-3 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 bg-white text-sm md:text-base">
                            <option value="today">📅 Leo</option>
                            <option value="yesterday">📅 Jana</option>
                            <option value="this_week">📅 Wiki Hii</option>
                            <option value="last_week">📅 Wiki Iliyopita</option>
                            <option value="this_month">📅 Mwezi Huu</option>
                            <option value="last_month">📅 Mwezi Ulipita</option>
                            <option value="all">📊 Yote</option>
                        </select>
                    </div>

                    <!-- Format Selection -->
                    <div>
                        <label for="type" class="block text-xs md:text-sm font-semibold text-gray-700 mb-1 md:mb-2 flex items-center space-x-2">
                            <i class="fas fa-file-export text-emerald-600 text-xs md:text-sm"></i>
                            <span>Chagua Muundo</span>
                        </label>
                        <select name="type" id="type" 
                                class="w-full border border-gray-300 rounded-lg md:rounded-xl px-3 md:px-4 py-2 md:py-3 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 bg-white text-sm md:text-base">
                            <option value="pdf">📄 PDF (.pdf)</option>
                            <option value="excel">📊 Excel (.xlsx)</option>
                        </select>
                    </div>

                    <!-- Download Button -->
                    <div class="flex items-end">
                        <button type="submit" 
                                class="w-full bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white px-4 md:px-6 py-2.5 md:py-3 rounded-lg md:rounded-xl transition-all duration-200 shadow-sm hover:shadow-md flex items-center justify-center space-x-2 text-sm md:text-base font-medium">
                            <i class="fas fa-download text-xs md:text-sm"></i>
                            <span>Pakua Ripoti</span>
                        </button>
                    </div>
                </div>

                <!-- Quick Download Buttons -->
                <div class="border-t border-gray-200 pt-4 md:pt-6">
                    <p class="text-xs md:text-sm font-semibold text-gray-700 mb-2 md:mb-4">Pakua Haraka:</p>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 md:gap-4">
                        <a href="{{ route('admin.reports.download-companies', ['period' => 'today', 'type' => 'pdf']) }}" 
                           class="bg-blue-50 hover:bg-blue-100 border border-blue-200 text-blue-700 px-2 py-2 md:px-4 md:py-3 rounded-lg md:rounded-xl transition-all duration-200 text-center group">
                            <div class="flex flex-col md:flex-row md:items-center justify-center space-y-1 md:space-y-0 md:space-x-2">
                                <i class="fas fa-file-pdf text-blue-600 text-xs md:text-base"></i>
                                <span class="font-medium text-xs md:text-sm">Leo (PDF)</span>
                            </div>
                        </a>
                        
                        <a href="{{ route('admin.reports.download-companies', ['period' => 'this_week', 'type' => 'pdf']) }}" 
                           class="bg-green-50 hover:bg-green-100 border border-green-200 text-green-700 px-2 py-2 md:px-4 md:py-3 rounded-lg md:rounded-xl transition-all duration-200 text-center group">
                            <div class="flex flex-col md:flex-row md:items-center justify-center space-y-1 md:space-y-0 md:space-x-2">
                                <i class="fas fa-file-pdf text-green-600 text-xs md:text-base"></i>
                                <span class="font-medium text-xs md:text-sm">Wiki Hii (PDF)</span>
                            </div>
                        </a>
                        
                        <a href="{{ route('admin.reports.download-companies', ['period' => 'this_month', 'type' => 'pdf']) }}" 
                           class="bg-red-50 hover:bg-red-100 border border-red-200 text-red-700 px-2 py-2 md:px-4 md:py-3 rounded-lg md:rounded-xl transition-all duration-200 text-center group">
                            <div class="flex flex-col md:flex-row md:items-center justify-center space-y-1 md:space-y-0 md:space-x-2">
                                <i class="fas fa-file-pdf text-red-600 text-xs md:text-base"></i>
                                <span class="font-medium text-xs md:text-sm">Mwezi Huu (PDF)</span>
                            </div>
                        </a>
                        
                        <a href="{{ route('admin.reports.download-companies', ['period' => 'all', 'type' => 'pdf']) }}" 
                           class="bg-purple-50 hover:bg-purple-100 border border-purple-200 text-purple-700 px-2 py-2 md:px-4 md:py-3 rounded-lg md:rounded-xl transition-all duration-200 text-center group">
                            <div class="flex flex-col md:flex-row md:items-center justify-center space-y-1 md:space-y-0 md:space-x-2">
                                <i class="fas fa-file-pdf text-purple-600 text-xs md:text-base"></i>
                                <span class="font-medium text-xs md:text-sm">Yote (PDF)</span>
                            </div>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Monthly Registration Trends -->
    <div class="mt-4 md:mt-6 bg-white rounded-lg md:rounded-2xl shadow-sm border border-gray-100 overflow-hidden mx-4 md:mx-0">
        <div class="px-4 md:px-6 py-3 md:py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
            <h3 class="text-base md:text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-chart-line mr-2 md:mr-3 text-emerald-600 text-sm md:text-base"></i>
                Mienendo ya Usajili (Miezi 6 Iliyopita)
            </h3>
        </div>
        
        <div class="p-3 md:p-6">
            <div class="grid grid-cols-3 sm:grid-cols-6 gap-2 md:gap-4">
                @foreach($monthlyRegistrations as $month)
                <div class="text-center">
                    <div class="bg-emerald-50 rounded-lg p-2 md:p-4 mb-1 md:mb-2">
                        <p class="text-lg md:text-2xl font-bold text-emerald-600">{{ $month['count'] }}</p>
                    </div>
                    <p class="text-xs md:text-sm font-medium text-gray-600 truncate">{{ $month['month'] }}</p>
                    <p class="text-xs text-gray-500">{{ $month['year'] }}</p>
                </div>
                @endforeach
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

/* Mobile optimizations */
@media (max-width: 640px) {
    .grid-cols-3 {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
}

/* Better touch targets on mobile */
@media (max-width: 768px) {
    button, 
    a, 
    select, 
    .clickable {
        min-height: 44px;
        min-width: 44px;
    }
}

/* Improve text readability on mobile */
@media (max-width: 768px) {
    .text-xs {
        line-height: 1.25;
    }
    
    .text-sm {
        line-height: 1.375;
    }
}

/* Optimize progress bars on mobile */
@media (max-width: 768px) {
    .h-1\.5 {
        height: 6px;
    }
}

/* Better spacing for mobile */
@media (max-width: 768px) {
    .space-y-3 > * + * {
        margin-top: 0.75rem;
    }
}
</style>

<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Auto-hide success notification
    const notification = document.getElementById('success-notification');
    if (notification) {
        setTimeout(() => {
            closeNotification();
        }, 5000);
    }

    // Animate progress bars
    animateProgressBars();
});

function animateProgressBars() {
    const progressBars = document.querySelectorAll('.rounded-full.bg-blue-500, .rounded-full.bg-orange-500, .rounded-full.bg-green-500, .rounded-full.bg-purple-500');
    
    progressBars.forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0%';
        
        setTimeout(() => {
            bar.style.width = width;
        }, 100);
    });
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

// Mobile menu toggle (if needed in future)
function toggleMobileMenu() {
    const mobileMenu = document.getElementById('mobile-menu');
    if (mobileMenu) {
        mobileMenu.classList.toggle('hidden');
    }
}
</script>
@endsection