@extends('layouts.admin')

@section('title', 'Orodha ya Makampuni')
@section('page-title', 'Orodha ya Makampuni')
@section('page-subtitle', 'Makampuni yote yaliyosajiliwa ndani ya mfumo')

@section('content')
<div class="min-h-screen bg-gray-50/30">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl font-bold text-gray-900">📋 Orodha ya Makampuni</h1>
                <p class="text-gray-600 mt-1">Dumisha na udhibiti makampuni yote yaliyosajiliwa</p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="bg-white px-4 py-2 rounded-lg shadow-sm border">
                    <div class="text-sm text-gray-500">Jumla ya Makampuni</div>
                    <div class="text-2xl font-bold text-emerald-600">{{ $total }}</div>
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

    <!-- Main Content Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Table Header -->
        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-building mr-3 text-emerald-600"></i>
                    Makampuni Yaliyosajiliwa
                </h3>
                <div class="flex items-center space-x-4 mt-3 sm:mt-0">
                    <div class="text-sm text-gray-500">
                        <span class="font-medium">{{ $companies->count() }}</span> kati ya <span class="font-medium">{{ $total }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Container -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-100">
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            <div class="flex items-center space-x-1">
                                <span>#</span>
                            </div>
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
        @php
            // Use floor() to get whole days only
            $remaining = floor(now()->diffInDays(\Carbon\Carbon::parse($company->package_end), false));
        @endphp

        <div class="text-sm">
            @if($remaining > 0)
                <span class="inline-flex items-center space-x-1 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-full border border-blue-200 text-xs font-medium">
                    <i class="fas fa-clock text-xs"></i>
                    <span>Siku {{ $remaining }} </span>
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
        </div>
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

                                <!-- ONE SMART BUTTON -->
                                @if(!$company->is_user_approved)
                                    <!-- Approve User -->
                                    <form action="{{ route('admin.approveUser', $company->id) }}" method="POST">
                                        @csrf
                                        <button class="w-full bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white text-xs px-3 py-2 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md flex items-center justify-center space-x-2">
                                            <i class="fas fa-user-check text-xs"></i>
                                            <span>Idhinisha Mtumiaji</span>
                                        </button>
                                    </form>

                                @elseif(!$company->is_verified)
                                    <!-- Verify Company -->
                                    <form action="{{ route('admin.verifyCompany', $company->id) }}" method="POST">
                                        @csrf
                                        <button class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white text-xs px-3 py-2 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md flex items-center justify-center space-x-2">
                                            <i class="fas fa-shield-alt text-xs"></i>
                                            <span>Thibitisha</span>
                                        </button>
                                    </form>

                                @else
                                    <!-- Completed -->
                                    <div class="w-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs px-3 py-2 rounded-lg flex items-center justify-center space-x-2">
                                        <i class="fas fa-check-circle text-xs"></i>
                                        <span>Kamilika</span>
                                    </div>
                                @endif

                                <!-- OTHER ACTIONS (Info + Delete) -->
                                <div class="flex space-x-2">
                                    <button
                                        class="flex-1 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white text-xs px-3 py-2 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md info-btn flex items-center justify-center space-x-2"
                                        data-target="modal-{{ $company->id }}">
                                        <i class="fas fa-info-circle text-xs"></i>
                                        <span>Taarifa</span>
                                    </button>

                                    <button
                                        class="flex-1 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white text-xs px-3 py-2 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md delete-btn flex items-center justify-center space-x-2"
                                        data-company="{{ $company->company_name }}"
                                        data-id="{{ $company->id }}">
                                        <i class="fas fa-trash text-xs"></i>
                                        <span>Futa</span>
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>

                    <!-- Company Details Modal -->
                    <div id="modal-{{ $company->id }}" class="fixed inset-0 hidden items-center justify-center bg-black/50 z-50 px-4">
                        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden animate-scale-in">
                            <!-- Header -->
                            <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 px-6 py-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-building text-white"></i>
                                        </div>
                                        <div>
                                            <h5 class="font-semibold text-white text-lg">Taarifa za Kampuni</h5>
                                            <p class="text-emerald-100 text-sm">{{ $company->company_name }}</p>
                                        </div>
                                    </div>
                                    <button class="text-white/80 hover:text-white transition-colors close-btn" data-target="modal-{{ $company->id }}">
                                        <i class="fas fa-times text-xl"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="p-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Company Information -->
                                    <div class="space-y-4">
                                        <h6 class="font-semibold text-gray-900 text-sm uppercase tracking-wide flex items-center space-x-2">
                                            <i class="fas fa-info-circle text-emerald-600"></i>
                                            <span>Taarifa za Msingi</span>
                                        </h6>
                                        <div class="space-y-3">
                                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                                <span class="text-sm text-gray-600">Jina la Kampuni:</span>
                                                <span class="text-sm font-medium text-gray-900">{{ $company->company_name }}</span>
                                            </div>
                                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                                <span class="text-sm text-gray-600">Jina la Mmiliki:</span>
                                                <span class="text-sm font-medium text-gray-900">{{ $company->owner_name }}</span>
                                            </div>
                                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                                <span class="text-sm text-gray-600">Namba ya Simu:</span>
                                                <span class="text-sm font-medium text-gray-900">{{ $company->phone }}</span>
                                            </div>
                                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                                <span class="text-sm text-gray-600">Barua Pepe:</span>
                                                <span class="text-sm font-medium text-gray-900">{{ $company->email }}</span>
                                            </div>
                                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                                <span class="text-sm text-gray-600">Mkoa:</span>
                                                <span class="text-sm font-medium text-gray-900">{{ $company->region }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- System Information -->
                                    <div class="space-y-4">
                                        <h6 class="font-semibold text-gray-900 text-sm uppercase tracking-wide flex items-center space-x-2">
                                            <i class="fas fa-cog text-emerald-600"></i>
                                            <span>Mfumo wa Kitaalamu</span>
                                        </h6>
                                        <div class="space-y-3">
                                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                                <span class="text-sm text-gray-600">Kifurushi:</span>
                                                <span class="text-sm font-medium text-gray-900">{{ $company->package ?? 'Haijawekwa' }}</span>
                                            </div>
                                      
                                                @if($company->package && $company->package_start && $company->package_end)
                                                    @php
                                                        $remaining = floor(now()->diffInDays(\Carbon\Carbon::parse($company->package_end), false));
                                                    @endphp
                                                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                                        <span class="text-sm text-gray-600">Muda Uliobaki:</span>
                                                        <span class="text-sm font-medium text-gray-900">
                                                            @if($remaining > 0)
                                                                Siku {{ $remaining }} zimebaki
                                                            @elseif($remaining === 0)
                                                                Inaisha Leo
                                                            @else
                                                                Kimeisha ({{ abs($remaining) }} siku)
                                                            @endif
                                                        </span>
                                                    </div>
                                                @endif                                                                      

                                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                                <span class="text-sm text-gray-600">Tarehe ya Usajili:</span>
                                                <span class="text-sm font-medium text-gray-900">{{ $company->created_at->format('d/m/Y H:i') }}</span>
                                            </div>
                                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                                <span class="text-sm text-gray-600">Hali ya Kampuni:</span>
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
                                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                                <span class="text-sm text-gray-600">Hali ya Mtumiaji:</span>
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
                            <div class="bg-gray-50 px-6 py-4 border-t border-gray-100">
                                <div class="flex justify-end">
                                    <button
                                        class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors close-btn flex items-center space-x-2"
                                        data-target="modal-{{ $company->id }}">
                                        <i class="fas fa-times text-xs"></i>
                                        <span>Funga</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Package Assignment Modal -->
                    <div id="package-modal-{{ $company->id }}" class="fixed inset-0 hidden items-center justify-center bg-black/50 z-50 px-4">
                        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden animate-scale-in">
                            <!-- Header -->
                            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-box-open text-white"></i>
                                        </div>
                                        <div>
                                            <h5 class="font-semibold text-white text-lg">Weka Kifurushi</h5>
                                            <p class="text-blue-100 text-sm">{{ $company->company_name }}</p>
                                        </div>
                                    </div>
                                    <button class="text-white/80 hover:text-white transition-colors close-package-btn" data-target="package-modal-{{ $company->id }}">
                                        <i class="fas fa-times text-xl"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Form -->
                            <form action="{{ route('admin.setPackageTime', $company->id) }}" method="POST" class="p-6 space-y-6">
                                @csrf
                                
                                <!-- Package Selection -->
                                <div class="space-y-3">
                                    <label for="package" class="block text-sm font-semibold text-gray-900 flex items-center space-x-2">
                                        <i class="fas fa-box text-blue-600 text-sm"></i>
                                        <span>Chagua Kifurushi</span>
                                    </label>
                                    <select name="package" id="package" class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white">
                                        <option value="Free Trial 14 days">📅 Free Trial - Siku 14</option>
                                        <option value="180 days">🚀 Kifurushi cha Siku 180</option>
                                        <option value="366 days">⭐ Kifurushi cha Siku 366</option>
                                    </select>
                                </div>

                                <!-- Start Date -->
                                <div class="space-y-3">
                                    <label for="start_date" class="block text-sm font-semibold text-gray-900 flex items-center space-x-2">
                                        <i class="fas fa-calendar-alt text-blue-600 text-sm"></i>
                                        <span>Tarehe ya Kuanza</span>
                                    </label>
                                    <input type="date" name="start_date" id="start_date" 
                                           class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white"
                                           value="{{ \Carbon\Carbon::today()->format('Y-m-d') }}">
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex space-x-3 pt-4">
                                    <button type="button" 
                                            class="flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl transition-all duration-200 close-package-btn flex items-center justify-center space-x-2 font-medium"
                                            data-target="package-modal-{{ $company->id }}">
                                        <i class="fas fa-times text-xs"></i>
                                        <span>Ghairi</span>
                                    </button>
                                    <button type="submit" 
                                            class="flex-1 px-4 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-xl transition-all duration-200 shadow-sm hover:shadow-md flex items-center justify-center space-x-2 font-medium">
                                        <i class="fas fa-check text-xs"></i>
                                        <span>Wasilisha</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Table Footer -->
        @if($companies->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-600">
                    {{ $companies->firstItem() }} - {{ $companies->lastItem() }} ya {{ $total }}
                </div>
                <div class="flex space-x-2">
                    {{ $companies->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="delete-modal" class="fixed inset-0 hidden items-center justify-center bg-black/50 z-50 px-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden animate-scale-in">
        <!-- Header -->
        <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-white"></i>
                </div>
                <div>
                    <h5 class="font-semibold text-white text-lg">Thibitisha Ufutaji</h5>
                    <p class="text-red-100 text-sm">Hatua hii haiwezi kutenduliwa</p>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6">
            <div class="text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-trash text-red-600 text-xl"></i>
                </div>
                <h4 class="text-lg font-semibold text-gray-900 mb-2">Unafuta Kampuni</h4>
                <p class="text-gray-600 mb-4">Una uhakika unataka kufuta kampuni ifuatayo?</p>
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-4">
                    <p class="font-bold text-red-700 text-lg" id="delete-company-name"></p>
                </div>
                <p class="text-sm text-red-600 flex items-center justify-center space-x-1">
                    <i class="fas fa-info-circle text-xs"></i>
                    <span>Taarifi zote za kampuni hii zitafutwa kabisa</span>
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-100">
            <div class="flex space-x-3">
                <button
                    id="cancel-delete"
                    class="flex-1 px-4 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl transition-all duration-200 flex items-center justify-center space-x-2 font-medium">
                    <i class="fas fa-times text-xs"></i>
                    <span>Ghairi</span>
                </button>
                <form id="delete-form" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button
                        type="submit"
                        class="w-full px-4 py-3 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white rounded-xl transition-all duration-200 shadow-sm hover:shadow-md flex items-center justify-center space-x-2 font-medium">
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

/* Custom scrollbar for table */
.overflow-x-auto::-webkit-scrollbar {
    height: 6px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Hover effects */
.hover-lift:hover {
    transform: translateY(-2px);
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
</script>
@endsection