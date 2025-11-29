@extends('layouts.admin')

@section('title', 'Ripoti - MAUZO')

@section('page-title', 'Ripoti')
@section('page-subtitle', 'Uchambuzi wa Takwimu na Taarifa')

@section('content')
<div class="min-h-screen bg-gray-50/30">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl font-bold text-gray-900">📊 Ripoti za Mfumo</h1>
                <p class="text-gray-600 mt-1">Uchambuzi wa kina wa takwimu na shughuli za mfumo</p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="bg-white px-4 py-2 rounded-lg shadow-sm border">
                    <div class="text-sm text-gray-500">Jumla ya Makampuni</div>
                    <div class="text-2xl font-bold text-emerald-600">{{ $totalCompanies ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Notification -->
    @if(session('success'))
    <div id="success-notification" class="fixed top-6 left-1/2 transform -translate-x-1/2 z-50 animate-slide-down">
        <div class="bg-white border border-emerald-200 rounded-xl shadow-2xl px-6 py-4 max-w-md">
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check text-emerald-600 text-sm"></i>
                    </div>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-gray-900">Imefanikiwa!</p>
                    <p class="text-sm text-gray-600">{{ session('success') }}</p>
                </div>
                <button onclick="closeNotification()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Total Companies -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover-lift transition-all duration-200">
            <div class="flex justify-between items-start mb-4">
                <div class="flex-1">
                    <p class="text-sm font-semibold text-gray-600 mb-2">Jumla ya Makampuni</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalCompanies ?? 0 }}</p>
                </div>
                <div class="flex-shrink-0">
                    <span class="text-xs font-semibold px-3 py-1 rounded-full bg-blue-100 text-blue-800">
                        +{{ $newCompaniesThisWeek ?? 5 }} wiki hii
                    </span>
                </div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="h-2 rounded-full transition-all duration-1000 bg-blue-500" 
                    style="width: {{ min(($totalCompanies / 100) * 100, 100) }}%">
                </div>
            </div>
        </div>

        <!-- Free Trial Companies -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover-lift transition-all duration-200">
            <div class="flex justify-between items-start mb-4">
                <div class="flex-1">
                    <p class="text-sm font-semibold text-gray-600 mb-2">Free Trial</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $freeTrialCompanies ?? 0 }}</p>
                </div>
                <div class="flex-shrink-0">
                    <span class="text-xs font-semibold px-3 py-1 rounded-full bg-orange-100 text-orange-800">
                        {{ $freeTrialPercentage ?? 0 }}%
                    </span>
                </div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="h-2 rounded-full transition-all duration-1000 bg-orange-500" 
                    style="width: {{ $freeTrialPercentage ?? 0 }}%">
                </div>
            </div>
        </div>

        <!-- Paid Package Companies -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover-lift transition-all duration-200">
            <div class="flex justify-between items-start mb-4">
                <div class="flex-1">
                    <p class="text-sm font-semibold text-gray-600 mb-2">Wenye Kifurushi</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $paidPackageCompanies ?? 0 }}</p>
                </div>
                <div class="flex-shrink-0">
                    <span class="text-xs font-semibold px-3 py-1 rounded-full bg-green-100 text-green-800">
                        +{{ $newPaidThisMonth ?? 2 }} mwezi
                    </span>
                </div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="h-2 rounded-full transition-all duration-1000 bg-green-500" 
                    style="width: {{ $paidPackagePercentage ?? 0 }}%">
                </div>
            </div>
        </div>

        <!-- Companies Registered Today -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover-lift transition-all duration-200">
            <div class="flex justify-between items-start mb-4">
                <div class="flex-1">
                    <p class="text-sm font-semibold text-gray-600 mb-2">Usajili wa Leo</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $todayRegistrations ?? 0 }}</p>
                </div>
                <div class="flex-shrink-0">
                    <span class="text-xs font-semibold px-3 py-1 rounded-full bg-purple-100 text-purple-800">
                        {{ $todayGrowth ?? 0 }}%
                    </span>
                </div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="h-2 rounded-full transition-all duration-1000 bg-purple-500" 
                    style="width: {{ min(($todayRegistrations / 10) * 100, 100) }}%">
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Recent Registrations -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-history mr-3 text-emerald-600"></i>
                        Usajili wa Hivi Karibuni
                    </h3>
                    <span class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-full">Saa 24 zilizopita</span>
                </div>
            </div>
            
            <div class="p-6">
                <div class="space-y-4">
                    @forelse($recentRegistrations as $registration)
                    <div class="flex items-center justify-between p-4 hover:bg-gray-50 rounded-xl transition-all duration-200 group">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-emerald-100 to-blue-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                                <span class="text-emerald-600 font-bold text-lg">
                                    {{ substr($registration->company_name, 0, 1) }}
                                </span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $registration->company_name }}</p>
                                <p class="text-sm text-gray-500">{{ $registration->owner_name }}</p>
                                <p class="text-xs text-gray-400">{{ $registration->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                @if($registration->package == 'Free Trial 14 days') bg-orange-100 text-orange-800
                                @elseif($registration->package) bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ $registration->package ?? 'Hakuna' }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-building text-gray-400 text-xl"></i>
                        </div>
                        <p class="text-gray-500">Hakuna usajili wa hivi karibuni</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Package Distribution -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-chart-pie mr-3 text-emerald-600"></i>
                    Usambazaji wa Vifurushi
                </h3>
            </div>
            
            <div class="p-6">
                <div class="space-y-6">
                    <!-- Free Trial -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-clock text-orange-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">Free Trial</p>
                                <p class="text-sm text-gray-500">{{ $freeTrialCompanies ?? 0 }} kampuni</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-orange-600">{{ $freeTrialPercentage ?? 0 }}%</p>
                        </div>
                    </div>

                    <!-- 180 Days Package -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-rocket text-green-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">Siku 180</p>
                                <p class="text-sm text-gray-500">{{ $package180Companies ?? 0 }} kampuni</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-green-600">{{ $package180Percentage ?? 0 }}%</p>
                        </div>
                    </div>

                    <!-- 366 Days Package -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-crown text-blue-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">Siku 366</p>
                                <p class="text-sm text-gray-500">{{ $package366Companies ?? 0 }} kampuni</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-blue-600">{{ $package366Percentage ?? 0 }}%</p>
                        </div>
                    </div>

                    <!-- No Package -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-times text-gray-400 text-sm"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">Hakuna Kifurushi</p>
                                <p class="text-sm text-gray-500">{{ $noPackageCompanies ?? 0 }} kampuni</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-gray-600">{{ $noPackagePercentage ?? 0 }}%</p>
                        </div>
                    </div>
                </div>

                <!-- Progress Chart -->
                <div class="mt-6 bg-gray-50 rounded-xl p-4">
                    <div class="flex h-4 rounded-full overflow-hidden">
                        <div class="bg-orange-500" style="width: {{ $freeTrialPercentage ?? 0 }}%"></div>
                        <div class="bg-green-500" style="width: {{ $package180Percentage ?? 0 }}%"></div>
                        <div class="bg-blue-500" style="width: {{ $package366Percentage ?? 0 }}%"></div>
                        <div class="bg-gray-400" style="width: {{ $noPackagePercentage ?? 0 }}%"></div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500 mt-2">
                        <span>Free Trial</span>
                        <span>Siku 180</span>
                        <span>Siku 366</span>
                        <span>Hakuna</span>
                    </div>
                </div>
            </div>
        </div>

        
    </div>
<!-- Download Reports Section -->
<div class="mt-6 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
            <i class="fas fa-file-download mr-3 text-emerald-600"></i>
            Pakua Ripoti za Makampuni
        </h3>
    </div>
    
    <div class="p-6">
        <form action="{{ route('admin.reports.download-companies') }}" method="GET" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Period Selection -->
                <div>
                    <label for="period" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center space-x-2">
                        <i class="fas fa-calendar-alt text-emerald-600 text-sm"></i>
                        <span>Chagua Kipindi</span>
                    </label>
                    <select name="period" id="period" 
                            class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 bg-white">
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
                    <label for="type" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center space-x-2">
                        <i class="fas fa-file-export text-emerald-600 text-sm"></i>
                        <span>Chagua Muundo</span>
                    </label>
                    <select name="type" id="type" 
                            class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 bg-white">
                        <option value="pdf">📄 PDF (.pdf)</option>
                            <option value="excel">📊 Excel (.xlsx)</option>
                        
                    </select>
                </div>

                <!-- Download Button -->
                <div class="flex items-end">
                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white px-6 py-3 rounded-xl transition-all duration-200 shadow-sm hover:shadow-md flex items-center justify-center space-x-2 font-medium">
                        <i class="fas fa-download text-sm"></i>
                        <span>Pakua Ripoti</span>
                    </button>
                </div>
            </div>

            <!-- Quick Download Buttons -->
            <div class="border-t border-gray-200 pt-6">
                <p class="text-sm font-semibold text-gray-700 mb-4">Pakua Haraka:</p>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <a href="{{ route('admin.reports.download-companies', ['period' => 'today', 'type' => 'pdf']) }}" 
                       class="bg-blue-50 hover:bg-blue-100 border border-blue-200 text-blue-700 px-4 py-3 rounded-xl transition-all duration-200 text-center group">
                        <div class="flex items-center justify-center space-x-2">
                            <i class="fas fa-file-excel text-blue-600"></i>
                            <span class="font-medium">Leo (PDF)</span>
                        </div>
                    </a>
                    
                    <a href="{{ route('admin.reports.download-companies', ['period' => 'this_week', 'type' => 'pdf']) }}" 
                       class="bg-green-50 hover:bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-xl transition-all duration-200 text-center group">
                        <div class="flex items-center justify-center space-x-2">
                            <i class="fas fa-file-excel text-green-600"></i>
                            <span class="font-medium">Wiki Hii (PDF)</span>
                        </div>
                    </a>
                    
                    <a href="{{ route('admin.reports.download-companies', ['period' => 'this_month', 'type' => 'pdf']) }}" 
                       class="bg-red-50 hover:bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-xl transition-all duration-200 text-center group">
                        <div class="flex items-center justify-center space-x-2">
                            <i class="fas fa-file-pdf text-red-600"></i>
                            <span class="font-medium">Mwezi Huu (PDF)</span>
                        </div>
                    </a>
                    
                    <a href="{{ route('admin.reports.download-companies', ['period' => 'all', 'type' => 'pdf']) }}" 
                       class="bg-purple-50 hover:bg-purple-100 border border-purple-200 text-purple-700 px-4 py-3 rounded-xl transition-all duration-200 text-center group">
                        <div class="flex items-center justify-center space-x-2">
                            <i class="fas fa-file-excel text-purple-600"></i>
                            <span class="font-medium">Yote (PDF)</span>
                        </div>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
    <!-- Monthly Registration Trends -->
    <div class="mt-6 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-chart-line mr-3 text-emerald-600"></i>
                Mienendo ya Usajili (Miezi 6 Iliyopita)
            </h3>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
                @foreach($monthlyRegistrations as $month)
                <div class="text-center">
                    <div class="bg-emerald-50 rounded-lg p-4 mb-2">
                        <p class="text-2xl font-bold text-emerald-600">{{ $month['count'] }}</p>
                    </div>
                    <p class="text-sm font-medium text-gray-600">{{ $month['month'] }}</p>
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
});

function closeNotification() {
    const notification = document.getElementById('success-notification');
    if (notification) {
        notification.style.animation = 'slideDown 0.3s ease-out reverse';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }
}
</script>
@endsection