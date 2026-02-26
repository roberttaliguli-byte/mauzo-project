@extends('layouts.app')

@section('title', 'Madeni')

@section('page-title', 'Madeni')
@section('page-subtitle', now()->format('d/m/Y'))

@push('styles')
<style>
/* Tab active state */
.tab-button.active {
    background-color: #f0fdf4;
    color: #059669;
    font-weight: 600;
}

.tab-button:not(.active) {
    background-color: transparent;
    color: #6b7280;
}

/* Borrower dropdown */
.borrower-item {
    cursor: pointer;
    transition: all 0.2s;
}

.borrower-item:hover {
    background-color: #f0fdf4;
}

.borrower-item.active {
    background-color: #059669;
    color: white;
}

/* Modal animations */
.modal {
    transition: opacity 0.3s ease;
}

.modal.hidden {
    opacity: 0;
    pointer-events: none;
}

/* Loading spinner */
.loading-spinner {
    border: 3px solid #f3f3f3;
    border-top: 3px solid #059669;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Notification auto-dismiss */
.notification-auto-dismiss {
    animation: slideIn 0.3s ease-out;
    transition: opacity 0.5s ease-out, transform 0.5s ease-out;
}

@keyframes slideIn {
    from { opacity: 0; transform: translateY(-20px) translateX(-50%); }
    to { opacity: 1; transform: translateY(0) translateX(-50%); }
}

/* Payment method badges */
.payment-badge-cash {
    background-color: #d1fae5;
    color: #065f46;
    border-radius: 0.25rem;
    padding: 0.25rem 0.75rem;
    font-size: 0.75rem;
    font-weight: 500;
}

.payment-badge-lipa_namba {
    background-color: #dbeafe;
    color: #1e40af;
    border-radius: 0.25rem;
    padding: 0.25rem 0.75rem;
    font-size: 0.75rem;
    font-weight: 500;
}

.payment-badge-bank {
    background-color: #f3e8ff;
    color: #6b21a8;
    border-radius: 0.25rem;
    padding: 0.25rem 0.75rem;
    font-size: 0.75rem;
    font-weight: 500;
}

/* Hover effects */
.hover-lift {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.hover-lift:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Report chart container */
.report-chart-container {
    height: 300px;
    margin-bottom: 20px;
}

/* Search results counter */
.search-results-counter {
    font-size: 0.875rem;
    color: #6b7280;
    padding: 0.5rem 1rem;
    background-color: #f9fafb;
    border-top: 1px solid #e5e7eb;
}
</style>
@endpush

@section('content')
<div class="space-y-4">
    <!-- Notifications Container -->
    <div id="notification-container" class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-sm px-4 pointer-events-none">
        @if(session('success'))
        <div class="rounded border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 mb-2 shadow-sm notification-auto-dismiss">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="rounded border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800 mb-2 shadow-sm notification-auto-dismiss">
            <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
        </div>
        @endif
        @if($errors->any())
            @foreach($errors->all() as $error)
            <div class="rounded border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800 mb-2 shadow-sm notification-auto-dismiss">
                <i class="fas fa-exclamation-triangle mr-2"></i> {{ $error }}
            </div>
            @endforeach
        @endif
    </div>

    <!-- Today's Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="bg-white p-3 rounded-lg border border-emerald-200 shadow-sm hover-lift">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Madeni ya Leo</p>
                    <p class="text-xl font-bold text-emerald-700">TZS {{ number_format($todayDebts, 2) }}</p>
                </div>
                <i class="fas fa-money-bill-wave text-emerald-500 text-lg"></i>
            </div>
        </div>
        
        <div class="bg-white p-3 rounded-lg border border-red-200 shadow-sm hover-lift">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Marejesho ya Leo</p>
                    <p class="text-xl font-bold text-red-700">TZS {{ number_format($todayRepayments, 2) }}</p>
                </div>
                <i class="fas fa-hand-holding-usd text-red-500 text-lg"></i>
            </div>
        </div>
        
        <div class="bg-white p-3 rounded-lg border border-green-200 shadow-sm hover-lift">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Wakopaji Wapya Leo</p>
                    <p class="text-xl font-bold text-green-700">{{ $todayNewBorrowers }}</p>
                </div>
                <i class="fas fa-user-plus text-green-500 text-lg"></i>
            </div>
        </div>
        
        <div class="bg-white p-3 rounded-lg border border-blue-200 shadow-sm hover-lift">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Madeni Yanayongoza Leo</p>
                    <p class="text-xl font-bold text-blue-700">{{ $todayPending }}</p>
                </div>
                <i class="fas fa-clock text-blue-500 text-lg"></i>
            </div>
        </div>
    </div>

    <!-- Borrowers Summary (Grouped) -->
    @if($borrowers->count() > 0)
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-3 bg-emerald-50 border-b border-emerald-200">
            <h3 class="text-sm font-semibold text-emerald-800 flex items-center">
                <i class="fas fa-users mr-2"></i>
                Muhtasari wa Wakopaji (Waliopo na Madeni)
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-2 text-left font-medium text-gray-600">Mkopaji</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-600">Simu</th>
                        <th class="px-4 py-2 text-center font-medium text-gray-600">Idadi ya Madeni</th>
                        <th class="px-4 py-2 text-right font-medium text-gray-600">Jumla ya Baki</th>
                        <th class="px-4 py-2 text-center font-medium text-gray-600">Tazama</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($borrowers as $borrower)
                    <tr class="hover:bg-gray-50 borrower-row" data-borrower="{{ $borrower->jina_mkopaji }}">
                        <td class="px-4 py-2">
                            <span class="font-medium text-gray-900">{{ $borrower->jina_mkopaji }}</span>
                        </td>
                        <td class="px-4 py-2">
                            @if($borrower->simu)
                            <span class="text-emerald-600">{{ $borrower->simu }}</span>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-center">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $borrower->total_debts }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-right">
                            <span class="font-bold text-red-600">{{ number_format($borrower->total_balance, 2) }} TZS</span>
                        </td>
                        <td class="px-4 py-2 text-center">
                            <button onclick="filterByBorrower('{{ $borrower->jina_mkopaji }}')" 
                                    class="text-emerald-600 hover:text-emerald-800 p-1 rounded-full hover:bg-emerald-50"
                                    title="Onyesha madeni ya {{ $borrower->jina_mkopaji }}">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Tabs -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm">
        <div class="flex overflow-x-auto">
            <button data-tab="madeni" class="tab-button flex-shrink-0 py-3 px-4 text-sm font-medium border-r border-gray-200 bg-emerald-50 text-emerald-700 whitespace-nowrap">
                <i class="fas fa-list mr-2"></i> Orodha ya Madeni
            </button>
            <button data-tab="marejesho" class="tab-button flex-shrink-0 py-3 px-4 text-sm font-medium border-r border-gray-200 text-gray-600 hover:bg-gray-50 whitespace-nowrap">
                <i class="fas fa-history mr-2"></i> Historia ya Marejesho
            </button>
            <button data-tab="ripoti" class="tab-button flex-shrink-0 py-3 px-4 text-sm font-medium text-gray-600 hover:bg-gray-50 whitespace-nowrap">
                <i class="fas fa-chart-bar mr-2"></i> Ripoti
            </button>
        </div>
    </div>

    <!-- Loading Indicator for Search -->
    <div id="search-loading" class="hidden fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-40">
        <div class="bg-white rounded-lg p-4 flex items-center space-x-3">
            <div class="loading-spinner"></div>
            <p class="text-sm text-gray-600">Inatafuta...</p>
        </div>
    </div>

    <!-- TAB 1: Orodha ya Madeni -->
    <div id="madeni-tab-content" class="tab-content space-y-3">
        <!-- Search and Filter Bar -->
        <div class="bg-white p-3 rounded-lg border border-gray-200 shadow-sm">
            <div class="flex flex-col sm:flex-row gap-2 sm:items-center">
                <div class="flex-1 relative">
                    <input 
                        type="text" 
                        id="search-input"
                        placeholder="Tafuta mkopaji, bidhaa, simu (inatafuta kwenye madeni yote)..." 
                        class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500"
                    >
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                <div class="flex gap-2">
                    <select id="filter-status" class="px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                        <option value="all">Madeni Yote</option>
                        <option value="active" {{ request()->filter === 'active' ? 'selected' : '' }}>Yanayongoza</option>
                        <option value="paid" {{ request()->filter === 'paid' ? 'selected' : '' }}>Yaliyolipwa</option>
                    </select>
                    <button onclick="printDebts()" class="px-3 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 text-sm font-medium whitespace-nowrap">
                        <i class="fas fa-print mr-1"></i> Print
                    </button>
                    <a href="{{ route('madeni.export', ['filter' => request()->filter]) }}" class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm font-medium whitespace-nowrap">
                        <i class="fas fa-file-excel mr-1"></i> Excel
                    </a>
                </div>
            </div>
            <div id="search-info" class="mt-2 text-xs text-gray-500 hidden">
                <span id="search-count">0</span> matokeo yamepatikana
            </div>
        </div>

        <!-- Debts Table Container (Dynamic) -->
        <div id="debts-container" class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            @include('madeni.partials.debts-table', ['madeni' => $madeni])
        </div>

        <!-- Clear Filter Button (Hidden by default) -->
        <div id="clear-filter-container" class="text-center {{ request('filter') || request('search') ? '' : 'hidden' }}">
            <button onclick="clearFilters()" class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-700 rounded text-sm hover:bg-gray-200">
                <i class="fas fa-times mr-1"></i> Ondoa Filter
            </button>
        </div>
    </div>

    <!-- TAB 2: Historia ya Marejesho -->
    <div id="marejesho-tab-content" class="tab-content hidden">
        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
            @if($marejesho->count() > 0)
            <div class="mb-3">
                <h3 class="text-sm font-medium text-gray-700 mb-2">Jumla ya Marejesho: 
                    <span class="font-bold text-green-600">TZS {{ number_format($marejesho->sum('kiasi'), 2) }}</span>
                </h3>
            </div>
            @endif
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-emerald-50">
                            <th class="px-4 py-2 text-left font-medium text-emerald-800">Tarehe</th>
                            <th class="px-4 py-2 text-left font-medium text-emerald-800">Mkopaji</th>
                            <th class="px-4 py-2 text-left font-medium text-emerald-800">Bidhaa</th>
                            <th class="px-4 py-2 text-center font-medium text-emerald-800">Idadi</th>
                            <th class="px-4 py-2 text-right font-medium text-emerald-800">Rejesho</th>
                            <th class="px-4 py-2 text-center font-medium text-emerald-800">Njia ya Malipo</th>
                            <th class="px-4 py-2 text-right font-medium text-emerald-800">Baki baada ya Malipo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($marejesho as $rejesho)
                            @php
                                $madeni = $rejesho->madeni;
                                $paymentMethod = $rejesho->lipa_kwa ?? 'cash';
                                $badgeClass = [
                                    'cash' => 'payment-badge-cash',
                                    'lipa_namba' => 'payment-badge-lipa_namba',
                                    'bank' => 'payment-badge-bank'
                                ][$paymentMethod];
                                
                                $methodText = [
                                    'cash' => 'Cash',
                                    'lipa_namba' => 'Lipa Namba',
                                    'bank' => 'Bank'
                                ][$paymentMethod];
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2">
                                    <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($rejesho->tarehe)->format('d/m/Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($rejesho->tarehe)->format('H:i') }}</div>
                                </td>
                                <td class="px-4 py-2">
                                    <div class="font-medium text-gray-900 text-sm">{{ $madeni->jina_mkopaji }}</div>
                                    @if($madeni->simu)
                                    <div class="text-xs text-emerald-600">{{ $madeni->simu }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-2">
                                    <span class="text-sm text-gray-700">{{ $madeni->bidhaa->jina ?? 'N/A' }}</span>
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <span class="text-sm">{{ $madeni->idadi }}</span>
                                </td>
                                <td class="px-4 py-2 text-right">
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-money-bill-wave mr-1"></i>
                                        {{ number_format($rejesho->kiasi, 2) }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <span class="{{ $badgeClass }}">
                                        {{ $methodText }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-right">
                                    <span class="text-sm font-bold 
                                        @if($madeni->baki <= 0) text-green-700
                                        @else text-red-700 @endif">
                                        {{ number_format($madeni->baki, 2) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                    <i class="fas fa-history text-3xl mb-2 text-gray-300"></i>
                                    <p>Hakuna historia ya marejesho bado</p>
                                    <p class="text-xs text-gray-500 mt-1">Rejesho la kwanza litakuonyesha hapa</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($marejesho->hasPages())
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $marejesho->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- TAB 3: Ripoti -->
    <div id="ripoti-tab-content" class="tab-content hidden">
        <div class="grid grid-cols-1 gap-4">
            <!-- Report Filters -->
            <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                <h3 class="text-sm font-medium text-gray-900 mb-3 flex items-center">
                    <i class="fas fa-filter text-emerald-600 mr-2"></i>
                    Chuja Ripoti
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Kuanzia Tarehe</label>
                        <input type="date" id="report-start-date" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                               value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Mpaka Tarehe</label>
                        <input type="date" id="report-end-date" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                               value="{{ now()->format('Y-m-d') }}">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Aina ya Ripoti</label>
                        <select id="report-type" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                            <option value="summary">Muhtasari</option>
                            <option value="detailed">Kina</option>
                            <option value="by_borrower">Kwa Mkopaji</option>
                            <option value="by_product">Kwa Bidhaa</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Hali ya Deni</label>
                        <select id="report-status" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                            <option value="all">Yote</option>
                            <option value="active">Yanayongoza</option>
                            <option value="paid">Yaliyolipwa</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button onclick="generateDebtReport()" class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 text-sm font-medium">
                            <i class="fas fa-chart-bar mr-1"></i> Generate
                        </button>
                        <button onclick="exportDebtReportPDF()" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm font-medium">
                            <i class="fas fa-file-pdf mr-1"></i> PDF
                        </button>
                        <button onclick="exportDebtReportExcel()" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm font-medium">
                            <i class="fas fa-file-excel mr-1"></i> Excel
                        </button>
                    </div>
                </div>
            </div>

            <!-- Report Results -->
            <div id="report-results" class="hidden">
                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-5 gap-3 mb-4">
                    <div class="bg-gradient-to-br from-emerald-500 to-emerald-700 p-3 rounded-lg shadow-sm">
                        <p class="text-xs text-white opacity-90 mb-1">Jumla ya Madeni</p>
                        <p class="text-xl font-bold text-white" id="report-total-debts">0</p>
                    </div>
                    <div class="bg-gradient-to-br from-blue-500 to-blue-700 p-3 rounded-lg shadow-sm">
                        <p class="text-xs text-white opacity-90 mb-1">Jumla ya Kiasi</p>
                        <p class="text-xl font-bold text-white" id="report-total-amount">0</p>
                    </div>
                    <div class="bg-gradient-to-br from-purple-500 to-purple-700 p-3 rounded-lg shadow-sm">
                        <p class="text-xs text-white opacity-90 mb-1">Jumla ya Marejesho</p>
                        <p class="text-xl font-bold text-white" id="report-total-repayments">0</p>
                    </div>
                    <div class="bg-gradient-to-br from-amber-500 to-amber-700 p-3 rounded-lg shadow-sm">
                        <p class="text-xs text-white opacity-90 mb-1">Baki</p>
                        <p class="text-xl font-bold text-white" id="report-remaining-balance">0</p>
                    </div>
                    <div class="bg-gradient-to-br from-red-500 to-red-700 p-3 rounded-lg shadow-sm">
                        <p class="text-xs text-white opacity-90 mb-1">Wastani wa Malipo</p>
                        <p class="text-xl font-bold text-white" id="report-avg-repayment">0</p>
                    </div>
                </div>

                <!-- Chart Section -->
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 mb-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Muhtasari wa Grafu</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <canvas id="debtStatusChart" class="w-full h-64"></canvas>
                        </div>
                        <div>
                            <canvas id="paymentMethodChart" class="w-full h-64"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Report Table -->
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm" id="report-table">
                            <thead>
                                <tr class="bg-emerald-50">
                                    <th class="px-4 py-2 text-left font-medium text-emerald-800">#</th>
                                    <th class="px-4 py-2 text-left font-medium text-emerald-800">Mkopaji</th>
                                    <th class="px-4 py-2 text-left font-medium text-emerald-800">Bidhaa</th>
                                    <th class="px-4 py-2 text-center font-medium text-emerald-800">Idadi</th>
                                    <th class="px-4 py-2 text-right font-medium text-emerald-800">Deni</th>
                                    <th class="px-4 py-2 text-right font-medium text-emerald-800">Malipo</th>
                                    <th class="px-4 py-2 text-right font-medium text-emerald-800">Baki</th>
                                    <th class="px-4 py-2 text-center font-medium text-emerald-800">Hali</th>
                                </tr>
                            </thead>
                            <tbody id="report-tbody" class="divide-y divide-gray-100">
                                <!-- Will be filled by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pay Modal -->
<div id="pay-modal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-lg shadow-xl w-full max-w-md mx-auto z-50">
        <div class="p-4 border-b border-gray-200 bg-emerald-50 rounded-t-lg">
            <h3 class="text-sm font-semibold text-emerald-800 flex items-center">
                <i class="fas fa-money-bill-wave mr-2"></i>
                Lipa Deni
            </h3>
        </div>
        <form id="pay-form" method="POST" class="p-4">
            @csrf
            <div class="space-y-4">
                <!-- Debt Info -->
                <div class="bg-gray-50 p-3 rounded-lg">
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <div>
                            <span class="text-gray-500">Mkopaji:</span>
                            <p id="pay-borrower-name" class="font-medium text-gray-900"></p>
                        </div>
                        <div>
                            <span class="text-gray-500">Bidhaa:</span>
                            <p id="pay-product-name" class="font-medium text-gray-900"></p>
                        </div>
                        <div>
                            <span class="text-gray-500">Idadi:</span>
                            <p id="pay-product-qty" class="font-medium text-gray-900"></p>
                        </div>
                        <div>
                            <span class="text-gray-500">Jumla ya Deni:</span>
                            <p id="pay-total-debt" class="font-medium text-gray-900"></p>
                        </div>
                    </div>
                </div>
                
                <!-- Current Balance -->
                <div class="text-center p-3 bg-red-50 rounded-lg border border-red-100">
                    <p class="text-xs text-gray-600 mb-1">Baki Lililobaki</p>
                    <p id="pay-remaining-balance" class="text-xl font-bold text-red-600"></p>
                </div>
                
                <!-- Payment Details -->
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">
                            <span class="text-red-500">*</span> Kiasi cha Kulipa (TZS)
                        </label>
                        <input type="number" step="0.01" name="kiasi" id="pay-amount"
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 transition"
                               required>
                        <div class="mt-1 flex justify-between text-xs">
                            <span class="text-gray-500">Kiasi cha chini: 0.01</span>
                            <span id="pay-max-amount" class="text-emerald-600 font-medium"></span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">
                            <span class="text-red-500">*</span> Njia ya Malipo
                        </label>
                        <select name="lipa_kwa" id="pay-method"
                                class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 transition"
                                required>
                            <option value="cash">💵 Cash (Fedha Taslimu)</option>
                            <option value="lipa_namba">📱 Lipa Namba (M-Pesa, Tigo Pesa, Airtel Money)</option>
                            <option value="bank">🏦 Bank (Benki)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">
                            <span class="text-red-500">*</span> Tarehe ya Malipo
                        </label>
                        <input type="date" name="tarehe" id="pay-date"
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 transition"
                               required>
                    </div>
                </div>
            </div>
            <div class="flex gap-2 pt-4 border-t border-gray-200 mt-4">
                <button type="button" id="close-pay-modal"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 text-sm font-medium transition">
                    Ghairi
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 text-sm font-medium transition flex items-center justify-center">
                    <i class="fas fa-check-circle mr-2"></i> Thibitisha Malipo
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="edit-modal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-lg shadow-xl w-full max-w-md mx-auto z-50 max-h-[90vh] overflow-y-auto">
        <div class="p-4 border-b border-gray-200 bg-amber-50 rounded-t-lg">
            <h3 class="text-sm font-semibold text-amber-800 flex items-center">
                <i class="fas fa-edit mr-2"></i>
                Badilisha Deni
            </h3>
        </div>
        <form id="edit-form" method="POST" class="p-4">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">
                        <span class="text-red-500">*</span> Jina la Mkopaji
                    </label>
                    <input type="text" name="jina_mkopaji" id="edit-jina-mkopaji"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 transition"
                           required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Namba ya Simu</label>
                    <input type="text" name="simu" id="edit-simu"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 transition">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">
                        <span class="text-red-500">*</span> Bidhaa
                    </label>
                    <select name="bidhaa_id" id="edit-bidhaa-id"
                            class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 transition"
                            required>
                        @foreach($bidhaa as $product)
                            <option value="{{ $product->id }}" 
                                    data-price="{{ $product->bei_kuuza }}"
                                    data-stock="{{ $product->idadi }}">
                                {{ $product->jina }} (Stock: {{ $product->idadi }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">
                        <span class="text-red-500">*</span> Idadi
                    </label>
                    <input type="number" name="idadi" id="edit-idadi" min="1"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 transition"
                           required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">
                        <span class="text-red-500">*</span> Bei (TZS)
                    </label>
                    <input type="number" step="0.01" name="bei" id="edit-bei"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 transition"
                           required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Aina ya Punguzo</label>
                    <select name="punguzo_aina" id="edit-punguzo-aina" 
                            class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 transition">
                        <option value="bidhaa">Kwa Bidhaa</option>
                        <option value="jumla">Jumla</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Punguzo (TZS)</label>
                    <input type="number" step="0.01" name="punguzo" id="edit-punguzo" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 transition"
                           value="0">
                </div>
            </div>
            <div class="flex gap-2 pt-4 border-t border-gray-200 mt-4">
                <button type="button" id="close-edit-modal"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 text-sm font-medium transition">
                    Ghairi
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 text-sm font-medium transition">
                    Hifadhi Mabadiliko
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Modal -->
<div id="delete-modal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-lg shadow-xl w-full max-w-sm mx-auto z-50">
        <div class="p-4 border-b border-gray-200 bg-red-50 rounded-t-lg">
            <h3 class="text-sm font-semibold text-red-800 flex items-center">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Thibitisha Kufuta
            </h3>
        </div>
        <div class="p-4">
            <div class="text-center mb-4">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-trash text-red-500 text-xl"></i>
                </div>
                <p class="text-gray-700 text-sm mb-1">Una uhakika unataka kufuta deni?</p>
                <p class="text-gray-900 font-medium text-base" id="delete-debt-name"></p>
                <div class="mt-3 p-3 bg-amber-50 rounded-lg border border-amber-100">
                    <p class="text-amber-700 text-xs">
                        <i class="fas fa-info-circle mr-1"></i>
                        Deni litafutwa pamoja na historia yake ya malipo.
                        Stock itarudishwa kama deni halijalipwa kamili.
                    </p>
                </div>
            </div>
            <div class="flex gap-2">
                <button id="cancel-delete"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 text-sm font-medium transition">
                    Ghairi
                </button>
                <form id="delete-form" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="w-full px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm font-medium transition">
                        Ndio, Futa
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Borrower Details Modal -->
<div id="borrower-modal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-lg shadow-xl w-full max-w-2xl mx-auto z-50 max-h-[90vh] overflow-y-auto">
        <div class="p-4 border-b border-gray-200 bg-emerald-50 rounded-t-lg sticky top-0">
            <h3 class="text-sm font-semibold text-emerald-800 flex items-center">
                <i class="fas fa-user mr-2"></i>
                Madeni ya <span id="borrower-name" class="ml-1"></span>
            </h3>
        </div>
        <div class="p-4">
            <div class="mb-4 p-3 bg-gray-50 rounded-lg flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-600">Jumla ya Madeni: <span id="borrower-total-debts" class="font-bold">0</span></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Jumla ya Baki: <span id="borrower-total-balance" class="font-bold text-red-600">0 TZS</span></p>
                </div>
            </div>
            
            <div id="borrower-debts-list" class="space-y-2">
                <!-- Will be filled by JavaScript -->
            </div>
            
            <div class="mt-4 text-right">
                <button onclick="closeBorrowerModal()" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 text-sm font-medium">
                    Funga
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
class MadeniManager {
    constructor() {
        this.currentTab = this.getSavedTab() || 'madeni';
        this.searchTimeout = null;
        this.currentDebtId = null;
        this.charts = {};
        this.allDebts = []; // Cache for search
        this.currentBorrower = null;
        this.init();
    }

    init() {
        this.bindEvents();
        this.showTab(this.currentTab);
        this.setupAutoCalculations();
        this.setTodayDate();
        this.autoDismissNotifications();
        this.setupAjaxForms();
    }

    getSavedTab() {
        return sessionStorage.getItem('madeni_tab') || 'madeni';
    }

    saveTab(tab) {
        sessionStorage.setItem('madeni_tab', tab);
    }

    bindEvents() {
        // Tab navigation
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', (e) => {
                const tab = e.target.closest('.tab-button').dataset.tab;
                this.showTab(tab);
                this.saveTab(tab);
            });
        });

        // Search with debounce (AJAX search across all records)
        const searchInput = document.getElementById('search-input');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                clearTimeout(this.searchTimeout);
                const searchTerm = e.target.value.trim();
                
                // Show loading
                document.getElementById('search-loading').classList.remove('hidden');
                
                this.searchTimeout = setTimeout(() => {
                    this.performSearch(searchTerm);
                }, 500);
            });
        }

        // Status filter
        const filterStatus = document.getElementById('filter-status');
        if (filterStatus) {
            filterStatus.addEventListener('change', (e) => {
                const filter = e.target.value;
                this.performSearch(document.getElementById('search-input').value, filter);
            });
        }

        // Debt actions
        this.bindDebtActions();

        // Modal events
        this.bindModalEvents();

        // Escape key for modals
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeAllModals();
            }
        });
    }

    async performSearch(searchTerm, filter = null) {
        try {
            const params = new URLSearchParams();
            if (searchTerm) params.append('search', searchTerm);
            if (filter) params.append('filter', filter);
            
            const response = await fetch(`{{ route('madeni.search') }}?${params.toString()}`);
            const data = await response.json();
            
            if (data.success) {
                this.renderSearchResults(data.debts, searchTerm);
            }
        } catch (error) {
            console.error('Search error:', error);
            this.showNotification('Hitilafu katika utafutaji', 'error');
        } finally {
            document.getElementById('search-loading').classList.add('hidden');
        }
    }

    renderSearchResults(debts, searchTerm) {
        const container = document.getElementById('debts-container');
        const searchInfo = document.getElementById('search-info');
        const searchCount = document.getElementById('search-count');
        
        if (debts.length === 0) {
            container.innerHTML = `
                <div class="p-8 text-center text-gray-500">
                    <i class="fas fa-search text-3xl mb-2 text-gray-300"></i>
                    <p>Hakuna madeni yanayolingana na utafutaji wako</p>
                    <p class="text-xs text-gray-400 mt-1">Jaribu maneno mengine</p>
                </div>
            `;
            searchInfo.classList.remove('hidden');
            searchCount.textContent = '0';
        } else {
            let tableHtml = `
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-emerald-50">
                                <th class="px-4 py-2 text-left font-medium text-emerald-800">Tarehe</th>
                                <th class="px-4 py-2 text-left font-medium text-emerald-800">Mkopaji</th>
                                <th class="px-4 py-2 text-left font-medium text-emerald-800 hidden md:table-cell">Bidhaa</th>
                                <th class="px-4 py-2 text-center font-medium text-emerald-800">Idadi</th>
                                <th class="px-4 py-2 text-right font-medium text-emerald-800">Deni</th>
                                <th class="px-4 py-2 text-right font-medium text-emerald-800">Baki</th>
                                <th class="px-4 py-2 text-center font-medium text-emerald-800 print:hidden">Vitendo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
            `;
            
            debts.forEach(debt => {
                const statusClass = debt.baki <= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                const statusIcon = debt.baki <= 0 ? '<i class="fas fa-check ml-1 text-xs"></i>' : '';
                
                tableHtml += `
                    <tr class="debt-row hover:bg-gray-50" data-debt='${JSON.stringify(debt).replace(/'/g, "&apos;")}'>
                        <td class="px-4 py-2">
                            <div class="text-sm text-gray-900">${new Date(debt.created_at).toLocaleDateString()}</div>
                            <div class="text-xs text-gray-500">Kutakiwa: ${new Date(debt.tarehe_malipo).toLocaleDateString()}</div>
                        </td>
                        <td class="px-4 py-2">
                            <div class="font-medium text-gray-900 text-sm">${debt.jina_mkopaji}</div>
                            ${debt.simu ? `<div class="text-xs text-emerald-600">${debt.simu}</div>` : ''}
                        </td>
                        <td class="px-4 py-2 hidden md:table-cell">
                            <span class="text-sm text-gray-700">${debt.bidhaa?.jina || 'N/A'}</span>
                            ${debt.punguzo > 0 ? `<div class="text-xs text-gray-500">Punguzo: ${parseFloat(debt.punguzo).toLocaleString()}</div>` : ''}
                        </td>
                        <td class="px-4 py-2 text-center">
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                ${debt.idadi}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-right">
                            <div class="text-sm font-bold text-gray-900">${parseFloat(debt.jumla).toLocaleString()}</div>
                        </td>
                        <td class="px-4 py-2 text-right">
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-bold ${statusClass}">
                                ${parseFloat(debt.baki).toLocaleString()}
                                ${statusIcon}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-center print:hidden">
                            <div class="flex justify-center space-x-2">
                                ${debt.baki > 0 ? `
                                <button class="pay-debt-btn text-green-600 hover:text-green-800 p-1 rounded-full hover:bg-green-50"
                                        data-id="${debt.id}" title="Lipa Deni">
                                    <i class="fas fa-money-bill-wave"></i>
                                </button>
                                ` : ''}
                                <button class="edit-debt-btn text-amber-600 hover:text-amber-800 p-1 rounded-full hover:bg-amber-50"
                                        data-id="${debt.id}" title="Badili Deni">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="delete-debt-btn text-red-600 hover:text-red-800 p-1 rounded-full hover:bg-red-50"
                                        data-id="${debt.id}" data-name="${debt.jina_mkopaji}" title="Futa Deni">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
            
            tableHtml += `
                        </tbody>
                    </table>
                </div>
                <div class="search-results-counter">
                    <i class="fas fa-search mr-1"></i> Imepatikana matokeo ${debts.length} kwa utafutaji "${searchTerm}"
                </div>
            `;
            
            container.innerHTML = tableHtml;
            searchInfo.classList.remove('hidden');
            searchCount.textContent = debts.length;
            
            // Re-bind debt actions for new rows
            this.bindDebtActions();
        }
        
        document.getElementById('clear-filter-container').classList.remove('hidden');
    }

    filterByBorrower(borrowerName) {
        document.getElementById('search-input').value = borrowerName;
        this.performSearch(borrowerName, document.getElementById('filter-status').value);
        this.showTab('madeni');
    }

    async showBorrowerDetails(borrowerName) {
        try {
            document.getElementById('search-loading').classList.remove('hidden');
            
            const response = await fetch(`{{ route('madeni.borrower.debts') }}?borrower=${encodeURIComponent(borrowerName)}`);
            const data = await response.json();
            
            if (data.success) {
                document.getElementById('borrower-name').textContent = data.borrower;
                document.getElementById('borrower-total-debts').textContent = data.total_debts;
                document.getElementById('borrower-total-balance').textContent = parseFloat(data.total_balance).toLocaleString() + ' TZS';
                
                let debtsHtml = '';
                data.debts.forEach(debt => {
                    debtsHtml += `
                        <div class="p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">${debt.bidhaa?.jina || 'N/A'}</p>
                                    <p class="text-xs text-gray-500">Idadi: ${debt.idadi} | Bei: ${parseFloat(debt.bei).toLocaleString()} TZS</p>
                                    <p class="text-xs text-gray-500">Tarehe: ${new Date(debt.created_at).toLocaleDateString()}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-bold text-red-600">Baki: ${parseFloat(debt.baki).toLocaleString()} TZS</p>
                                    <p class="text-xs text-gray-500">Deni: ${parseFloat(debt.jumla).toLocaleString()} TZS</p>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                document.getElementById('borrower-debts-list').innerHTML = debtsHtml;
                document.getElementById('borrower-modal').classList.remove('hidden');
            }
        } catch (error) {
            console.error('Error fetching borrower details:', error);
            this.showNotification('Hitilafu katika kupata maelezo ya mkopaji', 'error');
        } finally {
            document.getElementById('search-loading').classList.add('hidden');
        }
    }

    closeBorrowerModal() {
        document.getElementById('borrower-modal').classList.add('hidden');
    }

    clearFilters() {
        document.getElementById('search-input').value = '';
        document.getElementById('filter-status').value = 'all';
        window.location.href = '{{ route('madeni.index') }}';
    }

    setupAutoCalculations() {
        const editIdadi = document.getElementById('edit-idadi');
        const editBei = document.getElementById('edit-bei');
        const editPunguzo = document.getElementById('edit-punguzo');
        const editPunguzoAina = document.getElementById('edit-punguzo-aina');

        if (editIdadi && editBei && editPunguzo && editPunguzoAina) {
            [editIdadi, editBei, editPunguzo].forEach(input => {
                if (input) {
                    input.addEventListener('input', () => this.calculateEditTotal());
                }
            });

            if (editPunguzoAina) {
                editPunguzoAina.addEventListener('change', () => this.calculateEditTotal());
            }
        }
    }

    calculateEditTotal() {
        const idadiInput = document.getElementById('edit-idadi');
        const beiInput = document.getElementById('edit-bei');
        const punguzoInput = document.getElementById('edit-punguzo');
        const punguzoAina = document.getElementById('edit-punguzo-aina');

        if (!idadiInput || !beiInput || !punguzoInput) return;

        const idadi = parseFloat(idadiInput.value) || 0;
        const bei = parseFloat(beiInput.value) || 0;
        const punguzo = parseFloat(punguzoInput.value) || 0;
        const discountType = punguzoAina ? punguzoAina.value : 'bidhaa';

        const baseTotal = idadi * bei;
        let actualDiscount = punguzo;
        
        if (discountType === 'bidhaa') {
            actualDiscount = punguzo * idadi;
        }

        if (actualDiscount > baseTotal) {
            actualDiscount = baseTotal;
            punguzoInput.value = discountType === 'bidhaa' ? (baseTotal / idadi).toFixed(2) : baseTotal.toFixed(2);
        }
    }

    showTab(tabName) {
        document.querySelectorAll('.tab-button').forEach(button => {
            if (button.dataset.tab === tabName) {
                button.classList.add('bg-emerald-50', 'text-emerald-700');
                button.classList.remove('text-gray-600', 'hover:bg-gray-50');
            } else {
                button.classList.remove('bg-emerald-50', 'text-emerald-700');
                button.classList.add('text-gray-600', 'hover:bg-gray-50');
            }
        });

        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        
        document.getElementById(`${tabName}-tab-content`).classList.remove('hidden');
        this.currentTab = tabName;
    }

    bindDebtActions() {
        // Pay buttons
        document.querySelectorAll('.pay-debt-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                const row = e.target.closest('.debt-row');
                if (!row) return;
                
                try {
                    const debt = JSON.parse(row.dataset.debt);
                    this.openPayModal(debt);
                } catch (error) {
                    console.error('Error parsing debt data:', error);
                }
            });
        });

        // Edit buttons
        document.querySelectorAll('.edit-debt-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                const row = e.target.closest('.debt-row');
                if (!row) return;
                
                try {
                    const debt = JSON.parse(row.dataset.debt);
                    this.openEditModal(debt);
                } catch (error) {
                    console.error('Error parsing debt data:', error);
                }
            });
        });

        // Delete buttons
        document.querySelectorAll('.delete-debt-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                const debtId = e.target.closest('.delete-debt-btn').dataset.id;
                const debtName = e.target.closest('.delete-debt-btn').dataset.name;
                this.openDeleteModal(debtId, debtName);
            });
        });
    }

    bindModalEvents() {
        // Pay modal
        const payModal = document.getElementById('pay-modal');
        const closePayBtn = document.getElementById('close-pay-modal');

        if (closePayBtn) {
            closePayBtn.addEventListener('click', () => this.closeModal(payModal));
        }
        
        if (payModal) {
            payModal.addEventListener('click', (e) => {
                if (e.target === payModal || e.target.classList.contains('modal-overlay')) {
                    this.closeModal(payModal);
                }
            });
        }

        // Edit modal
        const editModal = document.getElementById('edit-modal');
        const closeEditBtn = document.getElementById('close-edit-modal');

        if (closeEditBtn) {
            closeEditBtn.addEventListener('click', () => this.closeModal(editModal));
        }
        
        if (editModal) {
            editModal.addEventListener('click', (e) => {
                if (e.target === editModal || e.target.classList.contains('modal-overlay')) {
                    this.closeModal(editModal);
                }
            });
        }

        // Delete modal
        const deleteModal = document.getElementById('delete-modal');
        const cancelDeleteBtn = document.getElementById('cancel-delete');

        if (cancelDeleteBtn) {
            cancelDeleteBtn.addEventListener('click', () => this.closeModal(deleteModal));
        }
        
        if (deleteModal) {
            deleteModal.addEventListener('click', (e) => {
                if (e.target === deleteModal || e.target.classList.contains('modal-overlay')) {
                    this.closeModal(deleteModal);
                }
            });
        }

        // Borrower modal
        const borrowerModal = document.getElementById('borrower-modal');
        if (borrowerModal) {
            borrowerModal.addEventListener('click', (e) => {
                if (e.target === borrowerModal || e.target.classList.contains('modal-overlay')) {
                    this.closeModal(borrowerModal);
                }
            });
        }
    }

    closeModal(modal) {
        if (modal) modal.classList.add('hidden');
    }

    closeAllModals() {
        ['pay-modal', 'edit-modal', 'delete-modal', 'borrower-modal'].forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (modal) modal.classList.add('hidden');
        });
    }

    openPayModal(debt) {
        document.getElementById('pay-borrower-name').textContent = debt.jina_mkopaji;
        document.getElementById('pay-product-name').textContent = debt.bidhaa?.jina || 'N/A';
        document.getElementById('pay-product-qty').textContent = debt.idadi;
        document.getElementById('pay-total-debt').textContent = `${parseFloat(debt.jumla).toLocaleString()} TZS`;
        document.getElementById('pay-remaining-balance').textContent = `${parseFloat(debt.baki).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})} TZS`;
        
        const payForm = document.getElementById('pay-form');
        if (payForm) {
            payForm.action = `/madeni/${debt.id}/rejesha`;
        }
        
        const payAmount = document.getElementById('pay-amount');
        if (payAmount) {
            payAmount.value = parseFloat(debt.baki).toFixed(2);
            payAmount.max = parseFloat(debt.baki);
            payAmount.min = 0.01;
        }
        
        const payMaxAmount = document.getElementById('pay-max-amount');
        if (payMaxAmount) {
            payMaxAmount.textContent = `Kiasi cha juu: ${parseFloat(debt.baki).toLocaleString()} TZS`;
        }
        
        const payDate = document.getElementById('pay-date');
        if (payDate) {
            payDate.value = new Date().toISOString().split('T')[0];
        }
        
        const payMethod = document.getElementById('pay-method');
        if (payMethod) {
            payMethod.value = 'cash';
        }
        
        const payModal = document.getElementById('pay-modal');
        if (payModal) payModal.classList.remove('hidden');
        
        setTimeout(() => {
            if (payAmount) payAmount.focus();
        }, 100);
    }

    openEditModal(debt) {
        const editForm = document.getElementById('edit-form');
        if (!editForm) return;

        document.getElementById('edit-jina-mkopaji').value = debt.jina_mkopaji;
        document.getElementById('edit-simu').value = debt.simu || '';
        document.getElementById('edit-bidhaa-id').value = debt.bidhaa_id;
        document.getElementById('edit-idadi').value = debt.idadi;
        document.getElementById('edit-bei').value = debt.bei;
        document.getElementById('edit-punguzo').value = debt.punguzo || 0;
        document.getElementById('edit-punguzo-aina').value = debt.punguzo_aina || 'bidhaa';
        
        editForm.action = `/madeni/${debt.id}`;
        
        const editModal = document.getElementById('edit-modal');
        if (editModal) editModal.classList.remove('hidden');
        
        setTimeout(() => {
            const firstInput = editForm.querySelector('input, select');
            if (firstInput) firstInput.focus();
        }, 100);
    }

    openDeleteModal(debtId, debtName) {
        const deleteForm = document.getElementById('delete-form');
        const deleteModal = document.getElementById('delete-modal');
        const deleteDebtName = document.getElementById('delete-debt-name');
        
        if (!deleteForm || !deleteModal || !deleteDebtName) return;
        
        deleteDebtName.textContent = debtName;
        deleteForm.action = `/madeni/${debtId}`;
        deleteModal.classList.remove('hidden');
    }

    setupAjaxForms() {
        const payForm = document.getElementById('pay-form');
        if (payForm) {
            payForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                
                const amountInput = document.getElementById('pay-amount');
                const maxAmount = parseFloat(amountInput.max);
                const amount = parseFloat(amountInput.value);
                
                if (amount > maxAmount) {
                    this.showNotification(`Kiasi kimezidi baki lililobaki (Kiwango cha juu: ${maxAmount.toLocaleString()} TZS)`, 'error');
                    return;
                }
                
                if (amount <= 0) {
                    this.showNotification('Kiasi cha kulipa lazima kiwe zaidi ya sifuri', 'error');
                    return;
                }
                
                await this.submitForm(payForm, 'Malipo yamehifadhiwa kikamilifu!');
                this.closeModal(document.getElementById('pay-modal'));
            });
        }

        const editForm = document.getElementById('edit-form');
        if (editForm) {
            editForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.submitForm(editForm, 'Deni limebadilishwa kikamilifu!');
                this.closeModal(document.getElementById('edit-modal'));
            });
        }

        const deleteForm = document.getElementById('delete-form');
        if (deleteForm) {
            deleteForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.submitForm(deleteForm, 'Deni limefutwa kikamilifu!');
                this.closeModal(document.getElementById('delete-modal'));
            });
        }
    }

    async submitForm(form, successMessage = 'Operesheni imekamilika!') {
        const formData = new FormData(form);
        const submitButton = form.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        
        try {
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Inatumwa...';
            
            const response = await fetch(form.action, {
                method: form.method,
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();

            if (response.ok) {
                const message = data.message || successMessage;
                this.showNotification(message, 'success');
                setTimeout(() => window.location.reload(), 1500);
            } else {
                const error = data.errors ? Object.values(data.errors)[0][0] : data.message;
                this.showNotification(error || 'Hitilafu imetokea katika kuhifadhi', 'error');
            }
        } catch (error) {
            this.showNotification('Hitilafu ya mtandao. Tafadhali angalia muunganisho wako', 'error');
            console.error('Form submission error:', error);
        } finally {
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
        }
    }

    setTodayDate() {
        const today = new Date().toISOString().split('T')[0];
        const payDate = document.getElementById('pay-date');
        if (payDate) {
            payDate.value = today;
        }
    }

    autoDismissNotifications() {
        setTimeout(() => {
            document.querySelectorAll('.notification-auto-dismiss').forEach(notification => {
                notification.style.opacity = '0';
                notification.style.transform = 'translateY(-10px) translateX(-50%)';
                setTimeout(() => notification.remove(), 300);
            });
        }, 3000);
    }

    showNotification(message, type = 'info') {
        const container = document.getElementById('notification-container');
        if (!container) return;
        
        const icons = {
            success: 'fa-check-circle',
            error: 'fa-exclamation-circle',
            warning: 'fa-exclamation-triangle',
            info: 'fa-info-circle'
        };

        const colors = {
            success: 'bg-emerald-50 border-emerald-200 text-emerald-800',
            error: 'bg-red-50 border-red-200 text-red-800',
            warning: 'bg-amber-50 border-amber-200 text-amber-800',
            info: 'bg-blue-50 border-blue-200 text-blue-800'
        };

        const notification = document.createElement('div');
        notification.className = `rounded border px-4 py-3 text-sm font-medium mb-2 ${colors[type]} shadow-sm animate-fade-in notification-auto-dismiss flex items-center`;
        notification.innerHTML = `
            <i class="fas ${icons[type]} mr-2"></i>
            <span>${message}</span>
        `;

        container.appendChild(notification);

        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateY(-10px) translateX(-50%)';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
}

// Global functions
function filterByBorrower(borrowerName) {
    if (window.madeniManager) {
        window.madeniManager.filterByBorrower(borrowerName);
    }
}

function showBorrowerDetails(borrowerName) {
    if (window.madeniManager) {
        window.madeniManager.showBorrowerDetails(borrowerName);
    }
}

function closeBorrowerModal() {
    if (window.madeniManager) {
        window.madeniManager.closeBorrowerModal();
    }
}

function clearFilters() {
    if (window.madeniManager) {
        window.madeniManager.clearFilters();
    }
}

function printDebts() {
    const printWindow = window.open('', '_blank');
    const rows = document.querySelectorAll('.debt-row');
    
    let tableRows = '';
    rows.forEach(row => {
        const debt = JSON.parse(row.dataset.debt);
        const status = debt.baki <= 0 ? 'Imelipwa' : 'Inayongoza';
        tableRows += `
            <tr>
                <td style="border: 1px solid #ddd; padding: 8px;">${new Date(debt.created_at).toLocaleDateString()}</td>
                <td style="border: 1px solid #ddd; padding: 8px;">${debt.jina_mkopaji}</td>
                <td style="border: 1px solid #ddd; padding: 8px;">${debt.bidhaa?.jina || 'N/A'}</td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">${debt.idadi}</td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">${parseFloat(debt.jumla).toLocaleString()}</td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">${parseFloat(debt.baki).toLocaleString()}</td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: center; color: ${debt.baki <= 0 ? 'green' : 'red'}">${status}</td>
            </tr>`;
    });
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Orodha ya Madeni - ${new Date().toLocaleDateString()}</title>
            <style>
                @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
                body { font-family: 'Inter', sans-serif; margin: 20px; background-color: #fff; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 12px; }
                th, td { border: 1px solid #e5e7eb; padding: 10px; }
                th { background-color: #f0fdf4; font-weight: 600; color: #047857; }
                .header { text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #047857; }
                .header h2 { margin: 0; color: #047857; font-size: 24px; }
                .header p { margin: 5px 0 0 0; color: #6b7280; font-size: 14px; }
                @media print {
                    body { margin: 0; }
                    .no-print { display: none; }
                }
                .footer { margin-top: 30px; text-align: right; font-size: 11px; color: #6b7280; border-top: 1px solid #e5e7eb; padding-top: 10px; }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>ORODHA YA MADENI</h2>
                <p>${new Date().toLocaleDateString()}</p>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>Tarehe</th>
                        <th>Mkopaji</th>
                        <th>Bidhaa</th>
                        <th>Idadi</th>
                        <th>Jumla ya Deni</th>
                        <th>Baki</th>
                        <th>Hali</th>
                    </tr>
                </thead>
                <tbody>
                    ${tableRows}
                </tbody>
            </table>
            
            <div class="footer">
                Ilichapishwa: ${new Date().toLocaleString()}
            </div>
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
}

function showTab(tabName) {
    if (window.madeniManager) {
        window.madeniManager.showTab(tabName);
        window.madeniManager.saveTab(tabName);
    }
}

function generateDebtReport() {
    const startDate = document.getElementById('report-start-date').value;
    const endDate = document.getElementById('report-end-date').value;
    const reportType = document.getElementById('report-type').value;
    const status = document.getElementById('report-status').value;
    
    if (!startDate || !endDate) {
        window.madeniManager.showNotification('Tafadhali chagua tarehe zote mbili', 'warning');
        return;
    }
    
    // Get all debt rows
    const rows = document.querySelectorAll('.debt-row');
    let filteredData = [];
    
    rows.forEach(row => {
        const debt = JSON.parse(row.dataset.debt);
        const debtDate = new Date(debt.created_at);
        const start = new Date(startDate);
        const end = new Date(endDate);
        
        start.setHours(0, 0, 0, 0);
        end.setHours(23, 59, 59, 999);
        
        if (debtDate >= start && debtDate <= end) {
            if (status === 'all' || 
                (status === 'active' && debt.baki > 0) || 
                (status === 'paid' && debt.baki <= 0)) {
                filteredData.push(debt);
            }
        }
    });
    
    if (filteredData.length === 0) {
        window.madeniManager.showNotification('Hakuna data katika kipindi hiki', 'info');
        return;
    }
    
    // Calculate totals
    const totalDebts = filteredData.length;
    const totalAmount = filteredData.reduce((sum, debt) => sum + parseFloat(debt.jumla), 0);
    const totalRepayments = filteredData.reduce((sum, debt) => sum + (parseFloat(debt.jumla) - parseFloat(debt.baki)), 0);
    const remainingBalance = totalAmount - totalRepayments;
    const avgRepayment = totalDebts > 0 ? totalRepayments / totalDebts : 0;
    
    // Update summary cards
    document.getElementById('report-total-debts').textContent = totalDebts;
    document.getElementById('report-total-amount').textContent = totalAmount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' TZS';
    document.getElementById('report-total-repayments').textContent = totalRepayments.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' TZS';
    document.getElementById('report-remaining-balance').textContent = remainingBalance.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' TZS';
    document.getElementById('report-avg-repayment').textContent = avgRepayment.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' TZS';
    
    // Generate table
    let tableHtml = '';
    let counter = 1;
    
    if (reportType === 'summary') {
        const grouped = {};
        filteredData.forEach(debt => {
            const date = new Date(debt.created_at).toLocaleDateString();
            if (!grouped[date]) {
                grouped[date] = {
                    count: 0,
                    amount: 0,
                    repaid: 0
                };
            }
            grouped[date].count++;
            grouped[date].amount += parseFloat(debt.jumla);
            grouped[date].repaid += (parseFloat(debt.jumla) - parseFloat(debt.baki));
        });
        
        Object.keys(grouped).sort().forEach(date => {
            const repaid = grouped[date].repaid;
            const remaining = grouped[date].amount - repaid;
            const status = remaining <= 0 ? '<span class="bg-green-100 text-green-800 px-2 py-1 rounded">Imelipwa</span>' : 
                                            '<span class="bg-red-100 text-red-800 px-2 py-1 rounded">Inayongoza</span>';
            
            tableHtml += `
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2">${counter++}</td>
                    <td class="px-4 py-2">Madeni ${grouped[date].count} - ${date}</td>
                    <td class="px-4 py-2">-</td>
                    <td class="px-4 py-2 text-center">-</td>
                    <td class="px-4 py-2 text-right">${grouped[date].amount.toLocaleString()}</td>
                    <td class="px-4 py-2 text-right">${repaid.toLocaleString()}</td>
                    <td class="px-4 py-2 text-right">${remaining.toLocaleString()}</td>
                    <td class="px-4 py-2 text-center">${status}</td>
                </tr>
            `;
        });
    } else if (reportType === 'by_borrower') {
        const grouped = {};
        filteredData.forEach(debt => {
            const borrower = debt.jina_mkopaji;
            if (!grouped[borrower]) {
                grouped[borrower] = {
                    count: 0,
                    amount: 0,
                    repaid: 0
                };
            }
            grouped[borrower].count++;
            grouped[borrower].amount += parseFloat(debt.jumla);
            grouped[borrower].repaid += (parseFloat(debt.jumla) - parseFloat(debt.baki));
        });
        
        Object.keys(grouped).sort().forEach(borrower => {
            const repaid = grouped[borrower].repaid;
            const remaining = grouped[borrower].amount - repaid;
            const status = remaining <= 0 ? '<span class="bg-green-100 text-green-800 px-2 py-1 rounded">Imelipwa</span>' : 
                                            '<span class="bg-red-100 text-red-800 px-2 py-1 rounded">Inayongoza</span>';
            
            tableHtml += `
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2">${counter++}</td>
                    <td class="px-4 py-2">${borrower}</td>
                    <td class="px-4 py-2">Madeni ${grouped[borrower].count}</td>
                    <td class="px-4 py-2 text-center">-</td>
                    <td class="px-4 py-2 text-right">${grouped[borrower].amount.toLocaleString()}</td>
                    <td class="px-4 py-2 text-right">${repaid.toLocaleString()}</td>
                    <td class="px-4 py-2 text-right">${remaining.toLocaleString()}</td>
                    <td class="px-4 py-2 text-center">${status}</td>
                </tr>
            `;
        });
    } else if (reportType === 'by_product') {
        const grouped = {};
        filteredData.forEach(debt => {
            const product = debt.bidhaa?.jina || 'N/A';
            if (!grouped[product]) {
                grouped[product] = {
                    count: 0,
                    amount: 0,
                    repaid: 0
                };
            }
            grouped[product].count++;
            grouped[product].amount += parseFloat(debt.jumla);
            grouped[product].repaid += (parseFloat(debt.jumla) - parseFloat(debt.baki));
        });
        
        Object.keys(grouped).sort().forEach(product => {
            const repaid = grouped[product].repaid;
            const remaining = grouped[product].amount - repaid;
            const status = remaining <= 0 ? '<span class="bg-green-100 text-green-800 px-2 py-1 rounded">Imelipwa</span>' : 
                                            '<span class="bg-red-100 text-red-800 px-2 py-1 rounded">Inayongoza</span>';
            
            tableHtml += `
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2">${counter++}</td>
                    <td class="px-4 py-2">-</td>
                    <td class="px-4 py-2">${product}</td>
                    <td class="px-4 py-2 text-center">${grouped[product].count}</td>
                    <td class="px-4 py-2 text-right">${grouped[product].amount.toLocaleString()}</td>
                    <td class="px-4 py-2 text-right">${repaid.toLocaleString()}</td>
                    <td class="px-4 py-2 text-right">${remaining.toLocaleString()}</td>
                    <td class="px-4 py-2 text-center">${status}</td>
                </tr>
            `;
        });
    } else {
        filteredData.forEach(debt => {
            const paid = parseFloat(debt.jumla) - parseFloat(debt.baki);
            const status = debt.baki <= 0 ? '<span class="bg-green-100 text-green-800 px-2 py-1 rounded">Imelipwa</span>' : 
                                            '<span class="bg-red-100 text-red-800 px-2 py-1 rounded">Inayongoza</span>';
            
            tableHtml += `
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2">${counter++}</td>
                    <td class="px-4 py-2">${debt.jina_mkopaji}</td>
                    <td class="px-4 py-2">${debt.bidhaa?.jina || 'N/A'}</td>
                    <td class="px-4 py-2 text-center">${debt.idadi}</td>
                    <td class="px-4 py-2 text-right">${parseFloat(debt.jumla).toLocaleString()}</td>
                    <td class="px-4 py-2 text-right">${paid.toLocaleString()}</td>
                    <td class="px-4 py-2 text-right">${parseFloat(debt.baki).toLocaleString()}</td>
                    <td class="px-4 py-2 text-center">${status}</td>
                </tr>
            `;
        });
    }
    
    document.getElementById('report-tbody').innerHTML = tableHtml;
    document.getElementById('report-results').classList.remove('hidden');
    
    // Update charts
    updateDebtCharts(filteredData);
}

function updateDebtCharts(data) {
    if (window.madeniManager.charts.debtStatus) {
        window.madeniManager.charts.debtStatus.destroy();
    }
    if (window.madeniManager.charts.paymentMethod) {
        window.madeniManager.charts.paymentMethod.destroy();
    }
    
    const activeDebts = data.filter(d => d.baki > 0).length;
    const paidDebts = data.filter(d => d.baki <= 0).length;
    
    const statusCtx = document.getElementById('debtStatusChart').getContext('2d');
    window.madeniManager.charts.debtStatus = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Yanayongoza', 'Yaliyolipwa'],
            datasets: [{
                data: [activeDebts, paidDebts],
                backgroundColor: ['#ef4444', '#10b981'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    
    // For payment methods, you would need actual data from repayments
    // This is a placeholder - you might want to fetch real data
    const paymentCtx = document.getElementById('paymentMethodChart').getContext('2d');
    window.madeniManager.charts.paymentMethod = new Chart(paymentCtx, {
        type: 'pie',
        data: {
            labels: ['Cash', 'Lipa Namba', 'Bank'],
            datasets: [{
                data: [60, 30, 10],
                backgroundColor: ['#10b981', '#3b82f6', '#8b5cf6'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

function exportDebtReportPDF() {
    const startDate = document.getElementById('report-start-date').value;
    const endDate = document.getElementById('report-end-date').value;
    const reportType = document.getElementById('report-type').value;
    const status = document.getElementById('report-status').value;
    
    if (!startDate || !endDate) {
        window.madeniManager.showNotification('Tafadhali chagua tarehe', 'warning');
        return;
    }
    
    const url = `${window.location.pathname}/report/pdf?start_date=${startDate}&end_date=${endDate}&report_type=${reportType}&status=${status}`;
    window.open(url, '_blank');
}

function exportDebtReportExcel() {
    const startDate = document.getElementById('report-start-date').value;
    const endDate = document.getElementById('report-end-date').value;
    const reportType = document.getElementById('report-type').value;
    const status = document.getElementById('report-status').value;
    
    if (!startDate || !endDate) {
        window.madeniManager.showNotification('Tafadhali chagua tarehe', 'warning');
        return;
    }
    
    const url = `${window.location.pathname}/report/excel?start_date=${startDate}&end_date=${endDate}&report_type=${reportType}&status=${status}`;
    window.location.href = url;
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    window.madeniManager = new MadeniManager();
    window.madeniManager.charts = {};
    
    // Add click handlers for borrower rows
    document.querySelectorAll('.borrower-row').forEach(row => {
        row.addEventListener('click', (e) => {
            const borrower = row.dataset.borrower;
            showBorrowerDetails(borrower);
        });
    });
    
    window.addEventListener('beforeunload', () => {
        if (window.madeniManager) {
            window.madeniManager.saveTab(window.madeniManager.currentTab);
        }
    });
});
</script>
@endpush