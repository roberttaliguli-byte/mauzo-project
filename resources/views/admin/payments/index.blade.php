```php
@extends('layouts.admin')

@section('title', 'Transactions - Payment Management')

@section('page-title', 'Payment Transactions')
@section('page-subtitle', 'Monitor and Manage All Payment Activities')

@section('content')
<div class="min-h-screen bg-gray-50/30">
    <!-- Header Section -->
    <div class="mb-4 md:mb-8 px-4 md:px-0">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div class="mb-3 md:mb-0">
                <h1 class="text-lg md:text-2xl font-bold text-gray-900">💰 Payment Transactions</h1>
                <p class="text-gray-600 text-xs md:text-base mt-1">Complete overview of all payment activities and transaction statuses</p>
            </div>
            <div class="flex items-center space-x-2 md:space-x-4">
                <a href="{{ route('admin.payments.export') }}?{{ http_build_query(request()->all()) }}" 
                   class="bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white px-4 md:px-6 py-2 md:py-2.5 rounded-lg md:rounded-xl transition-all duration-200 shadow-sm hover:shadow-md flex items-center space-x-2 text-sm md:text-base font-medium">
                    <i class="fas fa-download text-xs md:text-sm"></i>
                    <span>Export Data</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-6 gap-3 md:gap-4 mb-4 md:mb-6 px-4 md:px-0">
        <!-- Total Transactions -->
        <div class="bg-white rounded-lg md:rounded-xl shadow-sm border border-gray-100 p-3 md:p-4 hover-lift transition-all duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-gray-500 mb-1">Jumla</p>
                    <p class="text-lg md:text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                </div>
                <div class="w-8 h-8 md:w-10 md:h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-credit-card text-blue-600 text-sm md:text-base"></i>
                </div>
            </div>
            <div class="mt-2 text-xs text-gray-500">Malipo yote</div>
        </div>

        <!-- Completed -->
        <div class="bg-white rounded-lg md:rounded-xl shadow-sm border border-gray-100 p-3 md:p-4 hover-lift transition-all duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-gray-500 mb-1">Imekamilika</p>
                    <p class="text-lg md:text-2xl font-bold text-green-600">{{ $stats['completed'] }}</p>
                </div>
                <div class="w-8 h-8 md:w-10 md:h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-sm md:text-base"></i>
                </div>
            </div>
            <div class="mt-2 text-xs text-green-600">+{{ $stats['completed'] }} zimefaulu</div>
        </div>

        <!-- Pending -->
        <div class="bg-white rounded-lg md:rounded-xl shadow-sm border border-gray-100 p-3 md:p-4 hover-lift transition-all duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-gray-500 mb-1">Inasubiri</p>
                    <p class="text-lg md:text-2xl font-bold text-orange-600">{{ $stats['pending'] }}</p>
                </div>
                <div class="w-8 h-8 md:w-10 md:h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-orange-600 text-sm md:text-base"></i>
                </div>
            </div>
            <div class="mt-2 text-xs text-orange-600">Zinahitaji uthibitisho</div>
        </div>

        <!-- Failed -->
        <div class="bg-white rounded-lg md:rounded-xl shadow-sm border border-gray-100 p-3 md:p-4 hover-lift transition-all duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-gray-500 mb-1">Zimeshindikana</p>
                    <p class="text-lg md:text-2xl font-bold text-red-600">{{ $stats['failed'] }}</p>
                </div>
                <div class="w-8 h-8 md:w-10 md:h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation-circle text-red-600 text-sm md:text-base"></i>
                </div>
            </div>
            <div class="mt-2 text-xs text-red-600">Hitilafu za malipo</div>
        </div>

        <!-- Total Amount -->
        <div class="bg-white rounded-lg md:rounded-xl shadow-sm border border-gray-100 p-3 md:p-4 hover-lift transition-all duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-gray-500 mb-1">Jumla ya Kiasi</p>
                    <p class="text-lg md:text-xl font-bold text-purple-600 truncate">{{ number_format($stats['total_amount']) }}</p>
                </div>
                <div class="w-8 h-8 md:w-10 md:h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-coins text-purple-600 text-sm md:text-base"></i>
                </div>
            </div>
            <div class="mt-2 text-xs text-purple-600">TZS zote</div>
        </div>

        <!-- Today's Amount -->
        <div class="bg-white rounded-lg md:rounded-xl shadow-sm border border-gray-100 p-3 md:p-4 hover-lift transition-all duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-gray-500 mb-1">Ya Leo</p>
                    <p class="text-lg md:text-xl font-bold text-emerald-600 truncate">{{ number_format($stats['today_amount']) }}</p>
                </div>
                <div class="w-8 h-8 md:w-10 md:h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-day text-emerald-600 text-sm md:text-base"></i>
                </div>
            </div>
            <div class="mt-2 text-xs text-emerald-600">Malipo ya leo</div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="mb-4 md:mb-6 px-4 md:px-0">
        <div class="bg-white rounded-lg md:rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
            <form method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3 md:gap-4">
                    <!-- Status Filter -->
                    <div>
                        <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-1 md:mb-2">
                            <i class="fas fa-filter text-emerald-600 mr-1 text-xs md:text-sm"></i>
                            Hali ya Malipo
                        </label>
                        <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 md:px-4 md:py-2.5 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white text-sm">
                            <option value="">Hali Zote</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ Inasubiri</option>
                            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>⚙️ Inachakatwa</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>✅ Imekamilika</option>
                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>❌ Imeshindikana</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>🚫 Imeghairiwa</option>
                        </select>
                    </div>

                    <!-- From Date -->
                    <div>
                        <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-1 md:mb-2">
                            <i class="fas fa-calendar-alt text-emerald-600 mr-1 text-xs md:text-sm"></i>
                            Kuanzia Tarehe
                        </label>
                        <input type="date" name="from_date" value="{{ request('from_date') }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 md:px-4 md:py-2.5 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                    </div>

                    <!-- To Date -->
                    <div>
                        <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-1 md:mb-2">
                            <i class="fas fa-calendar-alt text-emerald-600 mr-1 text-xs md:text-sm"></i>
                            Mpaka Tarehe
                        </label>
                        <input type="date" name="to_date" value="{{ request('to_date') }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 md:px-4 md:py-2.5 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                    </div>

                    <!-- Search -->
                    <div>
                        <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-1 md:mb-2">
                            <i class="fas fa-search text-emerald-600 mr-1 text-xs md:text-sm"></i>
                            Tafuta
                        </label>
                        <div class="flex">
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Kampuni au Rejea..." 
                                   class="flex-1 border border-gray-300 rounded-l-lg px-3 py-2 md:px-4 md:py-2.5 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 md:px-6 py-2 md:py-2.5 rounded-r-lg transition-colors">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Filter Actions -->
                <div class="flex justify-end space-x-2">
                    <a href="{{ route('admin.payments.index') }}" 
                       class="px-4 py-2 md:px-6 md:py-2.5 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                        <i class="fas fa-times mr-1"></i>
                        Futa Filters
                    </a>
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 md:px-6 md:py-2.5 rounded-lg transition-colors text-sm">
                        <i class="fas fa-filter mr-1"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="px-4 md:px-0">
        <div class="bg-white rounded-lg md:rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 md:px-6 py-3 md:py-4 text-left">
                                <span class="text-xs md:text-sm font-semibold text-gray-600">ID</span>
                            </th>
                            <th class="px-4 md:px-6 py-3 md:py-4 text-left">
                                <span class="text-xs md:text-sm font-semibold text-gray-600">Kampuni</span>
                            </th>
                            <th class="px-4 md:px-6 py-3 md:py-4 text-left">
                                <span class="text-xs md:text-sm font-semibold text-gray-600">Rejea</span>
                            </th>
                            <th class="px-4 md:px-6 py-3 md:py-4 text-left">
                                <span class="text-xs md:text-sm font-semibold text-gray-600">Kifurushi</span>
                            </th>
                            <th class="px-4 md:px-6 py-3 md:py-4 text-right">
                                <span class="text-xs md:text-sm font-semibold text-gray-600">Kiasi</span>
                            </th>
                            <th class="px-4 md:px-6 py-3 md:py-4 text-left">
                                <span class="text-xs md:text-sm font-semibold text-gray-600">Simu</span>
                            </th>
                            <th class="px-4 md:px-6 py-3 md:py-4 text-left">
                                <span class="text-xs md:text-sm font-semibold text-gray-600">Hali</span>
                            </th>
                            <th class="px-4 md:px-6 py-3 md:py-4 text-left">
                                <span class="text-xs md:text-sm font-semibold text-gray-600">Tarehe</span>
                            </th>
                            <th class="px-4 md:px-6 py-3 md:py-4 text-center">
                                <span class="text-xs md:text-sm font-semibold text-gray-600">Kitendo</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($payments as $payment)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 md:px-6 py-3 md:py-4 text-xs md:text-sm font-mono text-gray-600">
                                #{{ $payment->id }}
                            </td>
                            <td class="px-4 md:px-6 py-3 md:py-4">
                                <a href="{{ route('admin.company.show', $payment->company_id) }}" class="text-emerald-600 hover:text-emerald-700 font-medium text-xs md:text-sm hover:underline">
                                    {{ $payment->company->company_name ?? 'N/A' }}
                                </a>
                            </td>
                            <td class="px-4 md:px-6 py-3 md:py-4">
                                <span class="font-mono text-xs md:text-sm text-gray-600 bg-gray-50 px-2 py-1 rounded">
                                    {{ $payment->transaction_reference }}
                                </span>
                            </td>
                            <td class="px-4 md:px-6 py-3 md:py-4">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ ucfirst($payment->package_type) }}
                                </span>
                            </td>
                            <td class="px-4 md:px-6 py-3 md:py-4 text-right">
                                <span class="font-bold text-gray-900 text-xs md:text-sm">
                                    {{ number_format($payment->amount) }}
                                </span>
                                <span class="text-xs text-gray-500 ml-1">{{ $payment->currency }}</span>
                            </td>
                            <td class="px-4 md:px-6 py-3 md:py-4 text-xs md:text-sm text-gray-600">
                                {{ $payment->phone_number ?? '—' }}
                            </td>
                            <td class="px-4 md:px-6 py-3 md:py-4">
                                @if($payment->status == 'completed')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1 text-xs"></i> Imekamilika
                                    </span>
                                @elseif($payment->status == 'pending')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                        <i class="fas fa-clock mr-1 text-xs"></i> Inasubiri
                                    </span>
                                @elseif($payment->status == 'processing')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-sync-alt mr-1 text-xs"></i> Inachakatwa
                                    </span>
                                @elseif($payment->status == 'failed')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-exclamation-circle mr-1 text-xs"></i> Imeshindikana
                                    </span>
                                @elseif($payment->status == 'cancelled')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <i class="fas fa-ban mr-1 text-xs"></i> Imeghairiwa
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $payment->status }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 md:px-6 py-3 md:py-4 text-xs md:text-sm text-gray-600">
                                <div>{{ $payment->created_at->format('d/m/Y') }}</div>
                                <div class="text-xs text-gray-400">{{ $payment->created_at->format('H:i') }}</div>
                            </td>
                            <td class="px-4 md:px-6 py-3 md:py-4 text-center">
                                <a href="{{ route('admin.payments.show', $payment->id) }}" 
                                   class="inline-flex items-center justify-center w-8 h-8 md:w-10 md:h-10 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 rounded-lg transition-colors">
                                    <i class="fas fa-eye text-xs md:text-sm"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-4 md:px-6 py-8 md:py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-12 h-12 md:w-16 md:h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3 md:mb-4">
                                        <i class="fas fa-credit-card text-gray-400 text-xl md:text-2xl"></i>
                                    </div>
                                    <p class="text-gray-500 text-sm md:text-base font-medium">Hakuna malipo yaliyopatikana</p>
                                    <p class="text-gray-400 text-xs md:text-sm mt-1">Jaribu kubadilisha vigezo vya utafutaji</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-4 md:px-6 py-3 md:py-4 bg-gray-50 border-t border-gray-200">
                {{ $payments->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Styles -->
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

.animate-slide-down {
    animation: slideDown 0.3s ease-out;
}

.hover-lift:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Mobile optimizations */
@media (max-width: 768px) {
    .hover-lift:hover {
        transform: none;
    }
    
    button, 
    a, 
    select, 
    .clickable {
        min-height: 44px;
        min-width: 44px;
    }
    
    .text-xs {
        line-height: 1.25;
    }
    
    .text-sm {
        line-height: 1.375;
    }
}
</style>
@endsection
