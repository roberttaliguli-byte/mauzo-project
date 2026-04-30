@extends('layouts.admin')

@section('title', 'Orodha ya Makampuni')
@section('page-title', 'Orodha ya Makampuni')
@section('page-subtitle', 'Makampuni yote yaliyosajiliwa ndani ya mfumo')

@section('content')
<div class="min-h-screen bg-gray-50/30">
    <!-- Header Section -->
    <div class="mb-4 md:mb-6 px-4 md:px-0">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div class="mb-3 md:mb-0">
                <h1 class="text-xl md:text-2xl font-bold text-gray-900">📋 Orodha ya Makampuni</h1>
                <p class="text-gray-600 text-sm md:text-base mt-1">Dumisha na udhibiti makampuni yote yaliyosajiliwa</p>
            </div>
            <div class="flex items-center space-x-3 md:space-x-4">
                <div class="bg-white px-3 py-2 md:px-4 md:py-2 rounded-lg shadow-sm border w-full md:w-auto">
                    <div class="text-xs md:text-sm text-gray-500">Jumla ya Makampuni</div>
                    <div class="text-xl md:text-2xl font-bold text-emerald-600">{{ $totalCompanies }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Boxes Section -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-2 md:gap-3 mb-4 md:mb-6 px-4 md:px-0">
        <div class="bg-white rounded-lg md:rounded-xl shadow-sm border border-gray-100 p-2 md:p-3 hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between mb-1 md:mb-2">
                <div class="w-6 h-6 md:w-8 md:h-8 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xs md:text-sm"></i>
                </div>
                <span class="text-xs md:text-sm font-medium text-green-600">{{ $verifiedCompanies }}</span>
            </div>
            <h3 class="text-xs md:text-sm font-medium text-gray-600">Imethibitishwa</h3>
            <p class="text-base md:text-xl font-bold text-gray-900">{{ $verifiedCompanies }}</p>
        </div>

        <div class="bg-white rounded-lg md:rounded-xl shadow-sm border border-gray-100 p-2 md:p-3 hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between mb-1 md:mb-2">
                <div class="w-6 h-6 md:w-8 md:h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-check text-blue-600 text-xs md:text-sm"></i>
                </div>
                <span class="text-xs md:text-sm font-medium text-blue-600">{{ $approvedUsers }}</span>
            </div>
            <h3 class="text-xs md:text-sm font-medium text-gray-600">Watumiaji Walioidhinishwa</h3>
            <p class="text-base md:text-xl font-bold text-gray-900">{{ $approvedUsers }}</p>
        </div>

        <div class="bg-white rounded-lg md:rounded-xl shadow-sm border border-gray-100 p-2 md:p-3 hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between mb-1 md:mb-2">
                <div class="w-6 h-6 md:w-8 md:h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-emerald-600 text-xs md:text-sm"></i>
                </div>
                <span class="text-xs md:text-sm font-medium text-emerald-600">{{ $activePackages }}</span>
            </div>
            <h3 class="text-xs md:text-sm font-medium text-gray-600">Vifurushi Vinavyotumika</h3>
            <p class="text-base md:text-xl font-bold text-gray-900">{{ $activePackages }}</p>
        </div>

        <div class="bg-white rounded-lg md:rounded-xl shadow-sm border border-gray-100 p-2 md:p-3 hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between mb-1 md:mb-2">
                <div class="w-6 h-6 md:w-8 md:h-8 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation-circle text-red-600 text-xs md:text-sm"></i>
                </div>
                <span class="text-xs md:text-sm font-medium text-red-600">{{ $expiredPackages }}</span>
            </div>
            <h3 class="text-xs md:text-sm font-medium text-gray-600">Vifurushi Vilivyokwisha</h3>
            <p class="text-base md:text-xl font-bold text-gray-900">{{ $expiredPackages }}</p>
        </div>

        <div class="bg-white rounded-lg md:rounded-xl shadow-sm border border-gray-100 p-2 md:p-3 hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between mb-1 md:mb-2">
                <div class="w-6 h-6 md:w-8 md:h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-gift text-purple-600 text-xs md:text-sm"></i>
                </div>
                <span class="text-xs md:text-sm font-medium text-purple-600">{{ $freeTrialCompanies }}</span>
            </div>
            <h3 class="text-xs md:text-sm font-medium text-gray-600">Free Trial</h3>
            <p class="text-base md:text-xl font-bold text-gray-900">{{ $freeTrialCompanies }}</p>
        </div>

        <div class="bg-white rounded-lg md:rounded-xl shadow-sm border border-gray-100 p-2 md:p-3 hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between mb-1 md:mb-2">
                <div class="w-6 h-6 md:w-8 md:h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-hourglass-half text-yellow-600 text-xs md:text-sm"></i>
                </div>
                <span class="text-xs md:text-sm font-medium text-yellow-600">{{ $totalCompanies - $verifiedCompanies }}</span>
            </div>
            <h3 class="text-xs md:text-sm font-medium text-gray-600">Zinazosubiri</h3>
            <p class="text-base md:text-xl font-bold text-gray-900">{{ $totalCompanies - $verifiedCompanies }}</p>
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

    <!-- Search Bar with Filter Options - SUBMIT FORM -->
    <div class="mb-4 md:mb-6 px-4 md:px-0">
        <form method="GET" action="{{ route('admin.dashboard') }}" id="searchForm">
            <div class="flex flex-col md:flex-row gap-3">
                <div class="flex-1 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400 text-sm"></i>
                    </div>
                    <input type="text" 
                           name="search"
                           id="searchInput"
                           value="{{ request('search') }}"
                           placeholder="Tafuta kwa jina la kampuni, mmiliki, barua pepe, namba ya simu, au kifurushi..." 
                           class="w-full pl-10 pr-4 py-2 md:py-3 border border-gray-300 rounded-lg md:rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 text-sm">
                </div>
                <div class="flex gap-2">
                    <select name="status" id="statusFilter" class="px-3 py-2 md:py-3 border border-gray-300 rounded-lg md:rounded-xl focus:ring-2 focus:ring-emerald-500 text-sm bg-white">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Zote</option>
                        <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Imethibitishwa</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Inasubiri</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Mtumiaji Ameidhinishwa</option>
                        <option value="free_trial" {{ request('status') == 'free_trial' ? 'selected' : '' }}>Free Trial</option>
                        <option value="active_package" {{ request('status') == 'active_package' ? 'selected' : '' }}>Kifurushi Kinachotumika</option>
                        <option value="expired_package" {{ request('status') == 'expired_package' ? 'selected' : '' }}>Kifurushi Kimeisha</option>
                    </select>
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 md:px-6 py-2 md:py-3 rounded-lg md:rounded-xl transition-all duration-200 flex items-center space-x-2">
                        <i class="fas fa-search text-sm"></i>
                        <span>Tafuta</span>
                    </button>
                    @if(request('search') || request('status'))
                    <a href="{{ route('admin.dashboard') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 md:px-6 py-2 md:py-3 rounded-lg md:rounded-xl transition-all duration-200 flex items-center space-x-2">
                        <i class="fas fa-times text-sm"></i>
                        <span>Futa</span>
                    </a>
                    @endif
                </div>
            </div>
        </form>
        
        @if(request('search') || request('status'))
        <div class="mt-2 text-sm text-emerald-600">
            <i class="fas fa-info-circle mr-1"></i>
            Matokeo: {{ $companies->total() }} kampuni zimepatikana
        </div>
        @endif
    </div>

    <!-- Main Content Card -->
    <div class="bg-white rounded-lg md:rounded-2xl shadow-sm border border-gray-100 overflow-hidden mx-4 md:mx-0">
        <!-- Table Header -->
        <div class="px-4 md:px-6 py-3 md:py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <h3 class="text-base md:text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-building mr-2 md:mr-3 text-emerald-600 text-sm md:text-base"></i>
                    Makampuni Yaliyosajiliwa
                </h3>
                <div class="text-xs md:text-sm text-gray-500">
                    {{ $companies->firstItem() }} - {{ $companies->lastItem() }} kati ya {{ $companies->total() }}
                </div>
            </div>
        </div>

        <!-- Mobile Cards View -->
        <div class="md:hidden">
            @forelse ($companies as $index => $company)
            <div class="company-item border-b border-gray-100 p-4 hover:bg-gray-50/50 transition-all duration-200">
                <!-- Header -->
                <div class="flex justify-between items-start mb-3">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-emerald-100 to-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-building text-emerald-600 text-xs"></i>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-gray-900">{{ $company->company_name }}</div>
                            <div class="text-xs text-gray-500">{{ $company->owner_name }}</div>
                        </div>
                    </div>
                </div>

                <!-- Details -->
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-600">Simu:</span>
                        <div class="flex items-center space-x-2">
                            <span class="text-xs font-medium">{{ $company->phone }}</span>
                            <a href="tel:{{ $company->phone }}" class="text-emerald-600 hover:text-emerald-700 transition-colors">
                                <i class="fas fa-phone-alt text-xs"></i>
                            </a>
                        </div>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-600">Kifurushi:</span>
                        <button class="text-xs px-2 py-1 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 set-package-btn" data-target="package-modal-{{ $company->id }}">
                            {{ $company->package ?? 'Weka' }}
                        </button>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-600">Muda:</span>
                        @if($company->package && $company->package_start && $company->package_end)
                            @php $remaining = floor(now()->diffInDays(\Carbon\Carbon::parse($company->package_end), false)); @endphp
                            @if($remaining > 0)
                                <span class="text-xs px-2 py-1 bg-blue-50 text-blue-700 rounded border border-blue-200">Siku {{ $remaining }}</span>
                            @elseif($remaining === 0)
                                <span class="text-xs px-2 py-1 bg-amber-50 text-amber-700 rounded border border-amber-200">Inaisha Leo</span>
                            @else
                                <span class="text-xs px-2 py-1 bg-red-50 text-red-700 rounded border border-red-200">Kimeisha</span>
                            @endif
                        @else
                            <span class="text-xs text-gray-500 italic">-</span>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                <div class="space-y-2">
                    @if(!$company->is_user_approved)
                        <form action="{{ route('admin.approveUser', $company->id) }}" method="POST" class="w-full">
                            @csrf
                            <button class="w-full bg-gradient-to-r from-yellow-500 to-yellow-600 text-white text-xs px-3 py-2 rounded-lg transition-all duration-200 flex items-center justify-center space-x-2">
                                <i class="fas fa-user-check text-xs"></i>
                                <span>Idhinisha Mtumiaji</span>
                            </button>
                        </form>
                    @elseif(!$company->is_verified)
                        <form action="{{ route('admin.verifyCompany', $company->id) }}" method="POST" class="w-full">
                            @csrf
                            <button class="w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white text-xs px-3 py-2 rounded-lg transition-all duration-200 flex items-center justify-center space-x-2">
                                <i class="fas fa-shield-alt text-xs"></i>
                                <span>Thibitisha Kampuni</span>
                            </button>
                        </form>
                    @else
                        <div class="w-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs px-3 py-2 rounded-lg flex items-center justify-center space-x-2">
                            <i class="fas fa-check-circle text-xs"></i>
                            <span>Kamilika</span>
                        </div>
                    @endif

                    <div class="flex space-x-2">
                        <a href="tel:{{ $company->phone }}" class="flex-1 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-xs px-2 py-2 rounded-lg flex items-center justify-center space-x-1 hover:from-blue-600 hover:to-blue-700 transition-all">
                            <i class="fas fa-phone-alt text-xs"></i>
                            <span>Piga Simu</span>
                        </a>
                        <button class="flex-1 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white text-xs px-2 py-2 rounded-lg info-btn flex items-center justify-center space-x-1" data-target="modal-{{ $company->id }}">
                            <i class="fas fa-info-circle text-xs"></i>
                            <span>Taarifa</span>
                        </button>
                        <button class="flex-1 bg-gradient-to-r from-red-500 to-red-600 text-white text-xs px-2 py-2 rounded-lg delete-btn flex items-center justify-center space-x-1" data-company="{{ $company->company_name }}" data-id="{{ $company->id }}">
                            <i class="fas fa-trash text-xs"></i>
                            <span>Futa</span>
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-8 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-building text-gray-400 text-xl"></i>
                </div>
                <h3 class="text-gray-700 font-medium mb-2">Hakuna makampuni yaliyopatikana</h3>
                <p class="text-gray-500 text-sm">Jaribu kubadilisha vigezo vyako vya utafutaji</p>
            </div>
            @endforelse
        </div>

        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-100">
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">#</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Kampuni</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Mmiliki</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Simu</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Kifurushi</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Muda Uliobaki</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Usajili</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Vitendo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($companies as $index => $company)
                    <tr class="hover:bg-gray-50/50 transition-all duration-200 group">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-600 font-medium">{{ $companies->firstItem() + $index }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-emerald-100 to-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-building text-emerald-600 text-sm"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900">{{ $company->company_name }}</div>
                                    <div class="text-xs text-gray-500 flex items-center space-x-1 mt-1">
                                        <i class="fas fa-map-marker-alt text-gray-400 text-xs"></i>
                                        <span>{{ $company->region ?? 'Mkoa haujabainishwa' }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 font-medium">{{ $company->owner_name }}</div>
                            <div class="text-xs text-gray-500">{{ $company->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-900">{{ $company->phone }}</span>
                                <a href="tel:{{ $company->phone }}" class="text-emerald-600 hover:text-emerald-700 transition-colors" title="Piga simu">
                                    <i class="fas fa-phone-alt text-sm"></i>
                                </a>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <button class="inline-flex items-center space-x-2 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 set-package-btn
                                @if($company->package) bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200
                                @else bg-gray-100 text-gray-600 hover:bg-gray-200 border border-gray-200 @endif"
                                data-target="package-modal-{{ $company->id }}">
                                <i class="fas fa-box-open text-xs"></i>
                                <span>{{ $company->package ?? 'Hakuna' }}</span>
                                <i class="fas fa-edit text-xs opacity-60"></i>
                            </button>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($company->package && $company->package_start && $company->package_end)
                                @php $remaining = floor(now()->diffInDays(\Carbon\Carbon::parse($company->package_end), false)); @endphp
                                @if($remaining > 0)
                                    <span class="inline-flex items-center space-x-1 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-full border border-blue-200 text-xs font-medium">
                                        <i class="fas fa-clock text-xs"></i>
                                        <span>Siku {{ $remaining }}</span>
                                    </span>
                                @elseif($remaining === 0)
                                    <span class="inline-flex items-center space-x-1 px-3 py-1.5 bg-amber-50 text-amber-700 rounded-full border border-amber-200 text-xs font-medium">
                                        <i class="fas fa-exclamation-circle text-xs"></i>
                                        <span>Inaisha Leo</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center space-x-1 px-3 py-1.5 bg-red-50 text-red-700 rounded-full border border-red-200 text-xs font-medium">
                                        <i class="fas fa-times-circle text-xs"></i>
                                        <span>Kimeisha ({{ abs($remaining) }} siku)</span>
                                    </span>
                                @endif
                            @else
                                <span class="text-sm text-gray-500 italic">Hakuna kifurushi</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 font-medium">
                                {{ $company->created_at->format('d M, Y') }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $company->created_at->format('H:i') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col space-y-2">
                                @if(!$company->is_user_approved)
                                    <form action="{{ route('admin.approveUser', $company->id) }}" method="POST">
                                        @csrf
                                        <button class="w-full bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white text-xs px-3 py-2 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md flex items-center justify-center space-x-2">
                                            <i class="fas fa-user-check text-xs"></i>
                                            <span>Idhinisha Mtumiaji</span>
                                        </button>
                                    </form>
                                @elseif(!$company->is_verified)
                                    <form action="{{ route('admin.verifyCompany', $company->id) }}" method="POST">
                                        @csrf
                                        <button class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white text-xs px-3 py-2 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md flex items-center justify-center space-x-2">
                                            <i class="fas fa-shield-alt text-xs"></i>
                                            <span>Thibitisha</span>
                                        </button>
                                    </form>
                                @else
                                    <div class="w-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs px-3 py-2 rounded-lg flex items-center justify-center space-x-2">
                                        <i class="fas fa-check-circle text-xs"></i>
                                        <span>Kamilika</span>
                                    </div>
                                @endif

                                <div class="flex space-x-2">
                                    <a href="tel:{{ $company->phone }}" class="flex-1 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white text-xs px-3 py-2 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md flex items-center justify-center space-x-2">
                                        <i class="fas fa-phone-alt text-xs"></i>
                                        <span>Piga</span>
                                    </a>
                                    <button class="flex-1 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white text-xs px-3 py-2 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md info-btn flex items-center justify-center space-x-2" data-target="modal-{{ $company->id }}">
                                        <i class="fas fa-info-circle text-xs"></i>
                                        <span>Taarifa</span>
                                    </button>
                                    <button class="flex-1 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white text-xs px-3 py-2 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md delete-btn flex items-center justify-center space-x-2" data-company="{{ $company->company_name }}" data-id="{{ $company->id }}">
                                        <i class="fas fa-trash text-xs"></i>
                                        <span>Futa</span>
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-building text-gray-400 text-2xl"></i>
                                </div>
                                <h3 class="text-gray-700 font-medium mb-2">Hakuna makampuni yaliyopatikana</h3>
                                <p class="text-gray-500 text-sm">Jaribu kubadilisha vigezo vyako vya utafutaji</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-4 md:px-6 py-3 md:py-4 border-t border-gray-100 bg-gray-50/50">
            @if($companies->hasPages())
            <div class="flex flex-col md:flex-row md:items-center justify-between space-y-2 md:space-y-0">
                <div class="text-sm text-gray-600">
                    {{ $companies->firstItem() }} - {{ $companies->lastItem() }} ya {{ $companies->total() }}
                </div>
                <div class="flex space-x-2 overflow-x-auto pb-1 md:pb-0">
                    {{ $companies->onEachSide(1)->withQueryString()->links('vendor.pagination.tailwind') }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Company Details Modal -->
@foreach ($companies as $company)
<div id="modal-{{ $company->id }}" class="fixed inset-0 hidden items-center justify-center bg-black/50 z-50 px-4">
    <div class="bg-white rounded-lg md:rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] md:max-h-[80vh] overflow-hidden animate-scale-in">
        <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 px-4 md:px-6 py-3 md:py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2 md:space-x-3">
                    <div class="w-8 h-8 md:w-10 md:h-10 bg-white/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-building text-white text-sm md:text-base"></i>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white text-sm md:text-lg">Taarifa za Kampuni</h5>
                        <p class="text-emerald-100 text-xs md:text-sm">{{ $company->company_name }}</p>
                    </div>
                </div>
                <button class="text-white/80 hover:text-white transition-colors close-btn" data-target="modal-{{ $company->id }}">
                    <i class="fas fa-times text-lg md:text-xl"></i>
                </button>
            </div>
        </div>

        <div class="p-4 md:p-6 overflow-y-auto max-h-[calc(90vh-120px)] md:max-h-[calc(80vh-120px)]">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                <div class="space-y-3 md:space-y-4">
                    <h6 class="font-semibold text-gray-900 text-xs md:text-sm uppercase tracking-wide flex items-center space-x-2">
                        <i class="fas fa-info-circle text-emerald-600"></i>
                        <span>Taarifa za Msingi</span>
                    </h6>
                    <div class="space-y-2">
                        <div class="flex justify-between py-2 border-b">
                            <span class="text-sm text-gray-600">Jina la Kampuni:</span>
                            <span class="text-sm font-medium">{{ $company->company_name }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b">
                            <span class="text-sm text-gray-600">Jina la Mmiliki:</span>
                            <span class="text-sm font-medium">{{ $company->owner_name }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b">
                            <span class="text-sm text-gray-600">Namba ya Simu:</span>
                            <span class="text-sm font-medium">{{ $company->phone }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b">
                            <span class="text-sm text-gray-600">Barua Pepe:</span>
                            <span class="text-sm font-medium">{{ $company->email }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b">
                            <span class="text-sm text-gray-600">Mkoa:</span>
                            <span class="text-sm font-medium">{{ $company->region ?? 'Haijabainishwa' }}</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-3 md:space-y-4">
                    <h6 class="font-semibold text-gray-900 text-xs md:text-sm uppercase tracking-wide flex items-center space-x-2">
                        <i class="fas fa-cog text-emerald-600"></i>
                        <span>Mfumo wa Kitaalamu</span>
                    </h6>
                    <div class="space-y-2">
                        <div class="flex justify-between py-2 border-b">
                            <span class="text-sm text-gray-600">Kifurushi:</span>
                            <span class="text-sm font-medium">{{ $company->package ?? 'Haijawekwa' }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b">
                            <span class="text-sm text-gray-600">Tarehe ya Usajili:</span>
                            <span class="text-sm font-medium">{{ $company->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        @if($company->package && $company->package_start && $company->package_end)
                        <div class="flex justify-between py-2 border-b">
                            <span class="text-sm text-gray-600">Muda Uliobaki:</span>
                            @php $remaining = floor(now()->diffInDays(\Carbon\Carbon::parse($company->package_end), false)); @endphp
                            <span class="text-sm font-medium">
                                @if($remaining > 0) Siku {{ $remaining }} zimebaki
                                @elseif($remaining === 0) Inaisha Leo
                                @else Kimeisha ({{ abs($remaining) }} siku)
                                @endif
                            </span>
                        </div>
                        @endif
                        <div class="flex justify-between py-2 border-b">
                            <span class="text-sm text-gray-600">Hali ya Kampuni:</span>
                            @if($company->is_verified)
                            <span class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs">Imethibitishwa</span>
                            @else
                            <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs">Haijathibitishwa</span>
                            @endif
                        </div>
                        <div class="flex justify-between py-2 border-b">
                            <span class="text-sm text-gray-600">Hali ya Mtumiaji:</span>
                            @if($company->is_user_approved)
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs">Ameidhinishwa</span>
                            @else
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs">Inasubiri</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t">
                <a href="tel:{{ $company->phone }}" class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-4 py-3 rounded-lg transition-all duration-200 flex items-center justify-center space-x-2">
                    <i class="fas fa-phone-alt"></i>
                    <span>Piga Simu kwa {{ $company->owner_name }}</span>
                </a>
            </div>
        </div>

        <div class="bg-gray-50 px-4 md:px-6 py-3 md:py-4 border-t">
            <div class="flex justify-end">
                <button class="px-4 md:px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors close-btn" data-target="modal-{{ $company->id }}">
                    Funga
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Package Assignment Modal -->
<div id="package-modal-{{ $company->id }}" class="fixed inset-0 hidden items-center justify-center bg-black/50 z-50 px-4">
    <div class="bg-white rounded-lg md:rounded-2xl shadow-2xl w-full max-w-md overflow-hidden animate-scale-in">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-4 md:px-6 py-3 md:py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2 md:space-x-3">
                    <div class="w-8 h-8 md:w-10 md:h-10 bg-white/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-box-open text-white text-sm md:text-base"></i>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white text-sm md:text-lg">Weka Kifurushi</h5>
                        <p class="text-blue-100 text-xs md:text-sm">{{ $company->company_name }}</p>
                    </div>
                </div>
                <button class="text-white/80 hover:text-white transition-colors close-package-btn" data-target="package-modal-{{ $company->id }}">
                    <i class="fas fa-times text-lg md:text-xl"></i>
                </button>
            </div>
        </div>

        <form action="{{ route('admin.setPackageTime', $company->id) }}" method="POST" class="p-4 md:p-6 space-y-4 md:space-y-6">
            @csrf
            <select name="package" class="w-full border border-gray-300 rounded-lg px-3 md:px-4 py-2 md:py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="Free Trial 14 days" {{ $company->package == 'Free Trial 14 days' ? 'selected' : '' }}>📅 Free Trial - Siku 14 (TZS 0)</option>
                <option value="30 days" {{ $company->package == '30 days' ? 'selected' : '' }}>🚀 Siku 30 - TZS 15,000</option>
                <option value="180 days" {{ $company->package == '180 days' ? 'selected' : '' }}>⭐ Siku 180 - TZS 75,000</option>
                <option value="366 days" {{ $company->package == '366 days' ? 'selected' : '' }}>👑 Siku 366 - TZS 150,000</option>
            </select>

            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-2">Tarehe ya Kuanza</label>
                <input type="date" name="start_date" 
                       class="w-full border border-gray-300 rounded-lg px-3 md:px-4 py-2 md:py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       value="{{ $company->package_start ? \Carbon\Carbon::parse($company->package_start)->format('Y-m-d') : \Carbon\Carbon::today()->format('Y-m-d') }}">
            </div>

            <div class="flex space-x-3 pt-4">
                <button type="button" class="flex-1 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors close-package-btn" data-target="package-modal-{{ $company->id }}">
                    Ghairi
                </button>
                <button type="submit" class="flex-1 px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                    Wasilisha
                </button>
            </div>
        </form>
    </div>
</div>
@endforeach

<!-- Delete Confirmation Modal -->
<div id="delete-modal" class="fixed inset-0 hidden items-center justify-center bg-black/50 z-50 px-4">
    <div class="bg-white rounded-lg md:rounded-2xl shadow-2xl w-full max-w-md overflow-hidden animate-scale-in">
        <div class="bg-gradient-to-r from-red-600 to-red-700 px-4 md:px-6 py-3 md:py-4">
            <div class="flex items-center space-x-2 md:space-x-3">
                <div class="w-8 h-8 md:w-10 md:h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-white"></i>
                </div>
                <div>
                    <h5 class="font-semibold text-white text-sm md:text-lg">Thibitisha Ufutaji</h5>
                    <p class="text-red-100 text-xs md:text-sm">Hatua hii haiwezi kutenduliwa</p>
                </div>
            </div>
        </div>

        <div class="p-4 md:p-6">
            <div class="text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-trash text-red-600 text-xl"></i>
                </div>
                <h4 class="text-lg font-semibold text-gray-900 mb-2">Unafuta Kampuni</h4>
                <p class="text-gray-600 mb-4">Una uhakika unataka kufuta kampuni ifuatayo?</p>
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                    <p class="font-bold text-red-700 text-lg" id="delete-company-name"></p>
                </div>
                <p class="text-sm text-red-600">Taarifa zote za kampuni hii zitafutwa kabisa</p>
            </div>
        </div>

        <div class="bg-gray-50 px-4 md:px-6 py-3 md:py-4 border-t">
            <div class="flex space-x-3">
                <button id="cancel-delete" class="flex-1 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors">
                    Ghairi
                </button>
                <form id="delete-form" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white rounded-lg transition-all duration-200">
                        Futa Kampuni
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

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
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const notification = document.getElementById('success-notification');
    if (notification) {
        setTimeout(() => closeNotification(), 5000);
    }

    initializeModalHandlers();
    initializePackageModalHandlers();
    initializeDeleteHandlers();
});

function initializeModalHandlers() {
    document.querySelectorAll('.info-btn').forEach(btn => {
        btn.addEventListener('click', () => showModal(btn.dataset.target));
    });
    document.querySelectorAll('.close-btn').forEach(btn => {
        btn.addEventListener('click', () => hideModal(btn.dataset.target));
    });
}

function initializePackageModalHandlers() {
    document.querySelectorAll('.set-package-btn').forEach(btn => {
        btn.addEventListener('click', () => showModal(btn.dataset.target));
    });
    document.querySelectorAll('.close-package-btn').forEach(btn => {
        btn.addEventListener('click', () => hideModal(btn.dataset.target));
    });
}

function initializeDeleteHandlers() {
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('delete-company-name').textContent = btn.dataset.company;
            // Fix: Use the full URL directly instead of trying to generate it with Blade
            document.getElementById('delete-form').action = '/admin/company/' + btn.dataset.id;
            showModal('delete-modal');
        });
    });
    
    document.getElementById('cancel-delete')?.addEventListener('click', () => hideModal('delete-modal'));
    
    document.querySelectorAll('[id^="modal-"], #delete-modal, [id^="package-modal-"]').forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) hideModal(modal.id);
        });
    });
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
        notification.remove();
    }
}

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        document.querySelectorAll('[id^="modal-"]:not(.hidden), #delete-modal:not(.hidden), [id^="package-modal-"]:not(.hidden)')
            .forEach(modal => hideModal(modal.id));
    }
});
</script>
@endsection