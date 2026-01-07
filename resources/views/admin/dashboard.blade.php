@extends('layouts.admin')

@section('title', 'Orodha ya Makampuni')
@section('page-title', 'Orodha ya Makampuni')
@section('page-subtitle', 'Makampuni yote yaliyosajiliwa ndani ya mfumo')

@section('content')
<div class="min-h-screen bg-gray-50/30">
    <!-- Header Section -->
    <div class="mb-6 md:mb-8 px-4 md:px-0">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div class="mb-4 md:mb-0">
                <h1 class="text-xl md:text-2xl font-bold text-gray-900">📋 Orodha ya Makampuni</h1>
                <p class="text-gray-600 text-sm md:text-base mt-1">Dumisha na udhibiti makampuni yote yaliyosajiliwa</p>
            </div>
            <div class="flex items-center space-x-3 md:space-x-4">
                <div class="bg-white px-3 py-2 md:px-4 md:py-2 rounded-lg shadow-sm border w-full md:w-auto">
                    <div class="text-xs md:text-sm text-gray-500">Jumla ya Makampuni</div>
                    <div class="text-xl md:text-2xl font-bold text-emerald-600">{{ $total }}</div>
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

    <!-- Main Content Card -->
    <div class="bg-white rounded-lg md:rounded-2xl shadow-sm border border-gray-100 overflow-hidden mx-4 md:mx-0">
        <!-- Table Header -->
        <div class="px-4 md:px-6 py-3 md:py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <h3 class="text-base md:text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-building mr-2 md:mr-3 text-emerald-600 text-sm md:text-base"></i>
                    Makampuni Yaliyosajiliwa
                </h3>
                <div class="flex items-center justify-between md:justify-normal space-x-4 mt-2 md:mt-0">
                    <div class="text-xs md:text-sm text-gray-500">
                        <span class="font-medium">{{ $companies->count() }}</span> kati ya <span class="font-medium">{{ $total }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Cards View -->
        <div class="md:hidden">
            @foreach ($companies as $index => $company)
            <div class="border-b border-gray-100 p-4 hover:bg-gray-50/50 transition-all duration-200">
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
                    <div class="text-xs text-gray-500">#{{ $index + 1 }}</div>
                </div>

                <!-- Details -->
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-600">Simu:</span>
                        <span class="text-xs font-medium">{{ $company->phone }}</span>
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
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-600">Usajili:</span>
                        <span class="text-xs font-medium">{{ $company->created_at->format('d/m/Y') }}</span>
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
            @endforeach
        </div>

        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-100">
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            #
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            Kampuni
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            Mmiliki
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            Kifurushi
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            Muda Uliobaki
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            Usajili
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            Vitendo
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($companies as $index => $company)
                    <tr class="hover:bg-gray-50/50 transition-all duration-200 group">
                        <!-- Serial Number -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-600 font-medium">{{ $index + 1 }}</div>
                        </td>

                        <!-- Company Information -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-emerald-100 to-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-building text-emerald-600 text-sm"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900">{{ $company->company_name }}</div>
                                    <div class="text-xs text-gray-500 flex items-center space-x-1 mt-1">
                                        <i class="fas fa-phone text-gray-400 text-xs"></i>
                                        <span>{{ $company->phone }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Owner Information -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 font-medium">{{ $company->owner_name }}</div>
                            <div class="text-xs text-gray-500">{{ $company->email }}</div>
                        </td>

                        <!-- Package -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <button
                                class="inline-flex items-center space-x-2 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 set-package-btn
                                    @if($company->package) bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200
                                    @else bg-gray-100 text-gray-600 hover:bg-gray-200 border border-gray-200 @endif"
                                data-target="package-modal-{{ $company->id }}">
                                <i class="fas fa-box-open text-xs"></i>
                                <span>{{ $company->package ?? 'Hakuna' }}</span>
                                <i class="fas fa-edit text-xs opacity-60"></i>
                            </button>
                        </td>

                        <!-- Remaining Days -->
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

                        <!-- Registration Date -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 font-medium">
                                {{ $company->created_at->format('d M, Y') }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $company->created_at->format('H:i') }}
                            </div>
                        </td>

                        <!-- Actions -->
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
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Table Footer -->
        @if($companies->hasPages())
        <div class="px-4 md:px-6 py-3 md:py-4 border-t border-gray-100 bg-gray-50/50">
            <div class="flex flex-col md:flex-row md:items-center justify-between space-y-2 md:space-y-0">
                <div class="text-sm text-gray-600">
                    {{ $companies->firstItem() }} - {{ $companies->lastItem() }} ya {{ $total }}
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
@foreach ($companies as $company)
<div id="modal-{{ $company->id }}" class="fixed inset-0 hidden items-center justify-center bg-black/50 z-50 px-4">
    <div class="bg-white rounded-lg md:rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] md:max-h-[80vh] overflow-hidden animate-scale-in">
        <!-- Header -->
        <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 px-4 md:px-6 py-3 md:py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2 md:space-x-3">
                    <div class="w-8 h-8 md:w-10 md:h-10 bg-white/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-building text-white text-sm md:text-base"></i>
                    </div>
                    <div class="min-w-0">
                        <h5 class="font-semibold text-white text-sm md:text-lg truncate">Taarifa za Kampuni</h5>
                        <p class="text-emerald-100 text-xs md:text-sm truncate">{{ $company->company_name }}</p>
                    </div>
                </div>
                <button class="text-white/80 hover:text-white transition-colors close-btn" data-target="modal-{{ $company->id }}">
                    <i class="fas fa-times text-lg md:text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Content -->
        <div class="p-4 md:p-6 overflow-y-auto max-h-[calc(90vh-120px)] md:max-h-[calc(80vh-120px)]">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                <!-- Company Information -->
                <div class="space-y-3 md:space-y-4">
                    <h6 class="font-semibold text-gray-900 text-xs md:text-sm uppercase tracking-wide flex items-center space-x-2">
                        <i class="fas fa-info-circle text-emerald-600 text-xs md:text-sm"></i>
                        <span>Taarifa za Msingi</span>
                    </h6>
                    <div class="space-y-2 md:space-y-3">
                        @foreach([
                            ['label' => 'Jina la Kampuni:', 'value' => $company->company_name],
                            ['label' => 'Jina la Mmiliki:', 'value' => $company->owner_name],
                            ['label' => 'Namba ya Simu:', 'value' => $company->phone],
                            ['label' => 'Barua Pepe:', 'value' => $company->email],
                            ['label' => 'Mkoa:', 'value' => $company->region]
                        ] as $item)
                        <div class="flex flex-col md:flex-row md:justify-between md:items-center py-2 border-b border-gray-100">
                            <span class="text-xs md:text-sm text-gray-600 mb-1 md:mb-0">{{ $item['label'] }}</span>
                            <span class="text-xs md:text-sm font-medium text-gray-900 text-right">{{ $item['value'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- System Information -->
                <div class="space-y-3 md:space-y-4">
                    <h6 class="font-semibold text-gray-900 text-xs md:text-sm uppercase tracking-wide flex items-center space-x-2">
                        <i class="fas fa-cog text-emerald-600 text-xs md:text-sm"></i>
                        <span>Mfumo wa Kitaalamu</span>
                    </h6>
                    <div class="space-y-2 md:space-y-3">
                        @foreach([
                            ['label' => 'Kifuruchi:', 'value' => $company->package ?? 'Haijawekwa'],
                            ['label' => 'Tarehe ya Usajili:', 'value' => $company->created_at->format('d/m/Y H:i')]
                        ] as $item)
                        <div class="flex flex-col md:flex-row md:justify-between md:items-center py-2 border-b border-gray-100">
                            <span class="text-xs md:text-sm text-gray-600 mb-1 md:mb-0">{{ $item['label'] }}</span>
                            <span class="text-xs md:text-sm font-medium text-gray-900 text-right">{{ $item['value'] }}</span>
                        </div>
                        @endforeach
                        
                        @if($company->package && $company->package_start && $company->package_end)
                            @php $remaining = floor(now()->diffInDays(\Carbon\Carbon::parse($company->package_end), false)); @endphp
                            <div class="flex flex-col md:flex-row md:justify-between md:items-center py-2 border-b border-gray-100">
                                <span class="text-xs md:text-sm text-gray-600 mb-1 md:mb-0">Muda Uliobaki:</span>
                                <span class="text-xs md:text-sm font-medium text-gray-900 text-right">
                                    @if($remaining > 0) Siku {{ $remaining }} zimebaki
                                    @elseif($remaining === 0) Inaisha Leo
                                    @else Kimeisha ({{ abs($remaining) }} siku)
                                    @endif
                                </span>
                            </div>
                        @endif

                        <!-- Status -->
                        <div class="flex flex-col md:flex-row md:justify-between md:items-center py-2 border-b border-gray-100">
                            <span class="text-xs md:text-sm text-gray-600 mb-1 md:mb-0">Hali ya Kampuni:</span>
                            @if($company->is_verified)
                            <span class="inline-flex items-center space-x-1 px-2 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-medium">
                                <i class="fas fa-check-circle text-xs"></i>
                                <span>Imethibitishwa</span>
                            </span>
                            @else
                            <span class="inline-flex items-center space-x-1 px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium">
                                <i class="fas fa-times-circle text-xs"></i>
                                <span>Haijathibitishwa</span>
                            </span>
                            @endif
                        </div>

                        <div class="flex flex-col md:flex-row md:justify-between md:items-center py-2 border-b border-gray-100">
                            <span class="text-xs md:text-sm text-gray-600 mb-1 md:mb-0">Hali ya Mtumiaji:</span>
                            @if($company->is_user_approved)
                            <span class="inline-flex items-center space-x-1 px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">
                                <i class="fas fa-check-circle text-xs"></i>
                                <span>Ameidhinishwa</span>
                            </span>
                            @else
                            <span class="inline-flex items-center space-x-1 px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium">
                                <i class="fas fa-clock text-xs"></i>
                                <span>Inasubiri</span>
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 px-4 md:px-6 py-3 md:py-4 border-t border-gray-100">
            <div class="flex justify-end">
                <button class="px-4 md:px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors close-btn flex items-center space-x-2" data-target="modal-{{ $company->id }}">
                    <i class="fas fa-times text-xs"></i>
                    <span>Funga</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Package Assignment Modal -->
<div id="package-modal-{{ $company->id }}" class="fixed inset-0 hidden items-center justify-center bg-black/50 z-50 px-4">
    <div class="bg-white rounded-lg md:rounded-2xl shadow-2xl w-full max-w-md overflow-hidden animate-scale-in">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-4 md:px-6 py-3 md:py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2 md:space-x-3">
                    <div class="w-8 h-8 md:w-10 md:h-10 bg-white/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-box-open text-white text-sm md:text-base"></i>
                    </div>
                    <div class="min-w-0">
                        <h5 class="font-semibold text-white text-sm md:text-lg truncate">Weka Kifurushi</h5>
                        <p class="text-blue-100 text-xs md:text-sm truncate">{{ $company->company_name }}</p>
                    </div>
                </div>
                <button class="text-white/80 hover:text-white transition-colors close-package-btn" data-target="package-modal-{{ $company->id }}">
                    <i class="fas fa-times text-lg md:text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('admin.setPackageTime', $company->id) }}" method="POST" class="p-4 md:p-6 space-y-4 md:space-y-6">
            @csrf
            
            <!-- Package Selection -->
            <div class="space-y-2 md:space-y-3">
                <label for="package" class="block text-xs md:text-sm font-semibold text-gray-900 flex items-center space-x-2">
                    <i class="fas fa-box text-blue-600 text-xs md:text-sm"></i>
                    <span>Chagua Kifurushi</span>
                </label>
                <select name="package" id="package" class="w-full border border-gray-300 rounded-lg md:rounded-xl px-3 md:px-4 py-2 md:py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white text-xs md:text-sm">
                    <option value="Free Trial 14 days">📅 Free Trial - Siku 14</option>
                    <option value="180 days">🚀 Kifurushi cha Siku 180</option>
                    <option value="366 days">⭐ Kifurushi cha Siku 366</option>
                </select>
            </div>

            <!-- Start Date -->
            <div class="space-y-2 md:space-y-3">
                <label for="start_date" class="block text-xs md:text-sm font-semibold text-gray-900 flex items-center space-x-2">
                    <i class="fas fa-calendar-alt text-blue-600 text-xs md:text-sm"></i>
                    <span>Tarehe ya Kuanza</span>
                </label>
                <input type="date" name="start_date" id="start_date" 
                       class="w-full border border-gray-300 rounded-lg md:rounded-xl px-3 md:px-4 py-2 md:py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white text-xs md:text-sm"
                       value="{{ \Carbon\Carbon::today()->format('Y-m-d') }}">
            </div>

            <!-- Action Buttons -->
            <div class="flex space-x-2 md:space-x-3 pt-4">
                <button type="button" 
                        class="flex-1 px-3 md:px-4 py-2 md:py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg md:rounded-xl transition-all duration-200 close-package-btn flex items-center justify-center space-x-1 md:space-x-2 text-xs md:text-sm font-medium"
                        data-target="package-modal-{{ $company->id }}">
                    <i class="fas fa-times text-xs"></i>
                    <span>Ghairi</span>
                </button>
                <button type="submit" 
                        class="flex-1 px-3 md:px-4 py-2 md:py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg md:rounded-xl transition-all duration-200 shadow-sm hover:shadow-md flex items-center justify-center space-x-1 md:space-x-2 text-xs md:text-sm font-medium">
                    <i class="fas fa-check text-xs"></i>
                    <span>Wasilisha</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endforeach

<!-- Delete Confirmation Modal -->
<div id="delete-modal" class="fixed inset-0 hidden items-center justify-center bg-black/50 z-50 px-4">
    <div class="bg-white rounded-lg md:rounded-2xl shadow-2xl w-full max-w-md overflow-hidden animate-scale-in">
        <!-- Header -->
        <div class="bg-gradient-to-r from-red-600 to-red-700 px-4 md:px-6 py-3 md:py-4">
            <div class="flex items-center space-x-2 md:space-x-3">
                <div class="w-8 h-8 md:w-10 md:h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-white text-sm md:text-base"></i>
                </div>
                <div class="min-w-0">
                    <h5 class="font-semibold text-white text-sm md:text-lg">Thibitisha Ufutaji</h5>
                    <p class="text-red-100 text-xs md:text-sm">Hatua hii haiwezi kutenduliwa</p>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="p-4 md:p-6">
            <div class="text-center">
                <div class="w-12 h-12 md:w-16 md:h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3 md:mb-4">
                    <i class="fas fa-trash text-red-600 text-base md:text-xl"></i>
                </div>
                <h4 class="text-base md:text-lg font-semibold text-gray-900 mb-2">Unafuta Kampuni</h4>
                <p class="text-gray-600 text-xs md:text-sm mb-3 md:mb-4">Una uhakika unataka kufuta kampuni ifuatayo?</p>
                <div class="bg-red-50 border border-red-200 rounded-lg md:rounded-xl p-3 md:p-4 mb-3 md:mb-4">
                    <p class="font-bold text-red-700 text-sm md:text-lg" id="delete-company-name"></p>
                </div>
                <p class="text-xs md:text-sm text-red-600 flex items-center justify-center space-x-1">
                    <i class="fas fa-info-circle text-xs"></i>
                    <span>Taarifi zote za kampuni hii zitafutwa kabisa</span>
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 px-4 md:px-6 py-3 md:py-4 border-t border-gray-100">
            <div class="flex space-x-2 md:space-x-3">
                <button id="cancel-delete" class="flex-1 px-3 md:px-4 py-2 md:py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg md:rounded-xl transition-all duration-200 flex items-center justify-center space-x-1 md:space-x-2 text-xs md:text-sm font-medium">
                    <i class="fas fa-times text-xs"></i>
                    <span>Ghairi</span>
                </button>
                <form id="delete-form" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full px-3 md:px-4 py-2 md:py-3 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white rounded-lg md:rounded-xl transition-all duration-200 shadow-sm hover:shadow-md flex items-center justify-center space-x-1 md:space-x-2 text-xs md:text-sm font-medium">
                        <i class="fas fa-trash text-xs"></i>
                        <span>Futa Kampuni</span>
                    </button>
                </form>
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

/* Custom scrollbar for modals */
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

/* Mobile responsive adjustments */
@media (max-width: 767px) {
    .text-\[10px\] {
        font-size: 10px;
    }
    
    .text-\[11px\] {
        font-size: 11px;
    }
}
</style>

<!-- Enhanced JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Auto-hide success notification
    const notification = document.getElementById('success-notification');
    if (notification) {
        setTimeout(() => {
            closeNotification();
        }, 5000);
    }

    // Initialize all modal handlers
    initializeModalHandlers();
    initializePackageModalHandlers();
    initializeDeleteHandlers();
});

function initializeModalHandlers() {
    // Info modal handlers
    document.querySelectorAll('.info-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const target = btn.getAttribute('data-target');
            showModal(target);
        });
    });

    document.querySelectorAll('.close-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const target = btn.getAttribute('data-target');
            hideModal(target);
        });
    });
}

function initializePackageModalHandlers() {
    // Package modal handlers
    document.querySelectorAll('.set-package-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const target = btn.getAttribute('data-target');
            showModal(target);
        });
    });

    document.querySelectorAll('.close-package-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const target = btn.getAttribute('data-target');
            hideModal(target);
        });
    });
}

function initializeDeleteHandlers() {
    // Delete confirmation handlers
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const companyName = btn.getAttribute('data-company');
            const companyId = btn.getAttribute('data-id');
            
            document.getElementById('delete-company-name').textContent = companyName;
            document.getElementById('delete-form').action = "{{ route('admin.destroyCompany', ':id') }}".replace(':id', companyId);
            
            showModal('delete-modal');
        });
    });

    // Cancel delete
    document.getElementById('cancel-delete').addEventListener('click', () => {
        hideModal('delete-modal');
    });

    // Backdrop click handlers for all modals
    document.querySelectorAll('[id^="modal-"], #delete-modal, [id^="package-modal-"]').forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                hideModal(modal.id);
            }
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
        notification.style.animation = 'slideDown 0.3s ease-out reverse';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }
}

// Escape key to close modals
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        const openModals = document.querySelectorAll('[id^="modal-"]:not(.hidden), #delete-modal:not(.hidden), [id^="package-modal-"]:not(.hidden)');
        openModals.forEach(modal => {
            hideModal(modal.id);
        });
    }
});

// Prevent form submission on enter key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA' && e.target.tagName !== 'INPUT') {
        e.preventDefault();
    }
});
</script>
@endsection