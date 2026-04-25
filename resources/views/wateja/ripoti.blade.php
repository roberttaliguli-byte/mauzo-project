@extends('layouts.app')

@section('title', 'Ripoti - Wateja')

@section('page-title', 'Ripoti ya Mauzo na Madeni')
@section('page-subtitle', now()->format('d/m/Y'))

@section('content')

<!-- Filter Form -->
<div class="bg-white rounded-lg shadow border border-gray-200 p-4 mb-4">
    <form method="GET" action="{{ route('wateja.ripoti') }}" class="space-y-3">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Chagua Mteja</label>
                <select name="mteja_id" class="w-full border border-gray-300 rounded-lg p-2 text-sm">
                    <option value="">-- Wateja Wote --</option>
                    @foreach($wateja as $mteja)
                        <option value="{{ $mteja->id }}" {{ $selectedCustomerId == $mteja->id ? 'selected' : '' }}>
                            {{ $mteja->jina }} - {{ $mteja->simu }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-semibold">
                    <i class="fas fa-search mr-1"></i> Tazama Ripoti
                </button>
                <a href="{{ route('wateja.ripoti') }}" class="ml-2 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-semibold">
                    <i class="fas fa-redo mr-1"></i> Weka Upya
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
    <div class="bg-white rounded-lg shadow border border-gray-200 p-3">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500">Jumla ya Mauzo</p>
                <p class="text-xl font-bold text-green-700">{{ number_format($totalSales, 2) }} Tsh</p>
            </div>
            <i class="fas fa-chart-line text-green-500 text-2xl"></i>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow border border-gray-200 p-3">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500">Jumla ya Madeni (Yaliyotolewa)</p>
                <p class="text-xl font-bold text-yellow-700">{{ number_format($totalDebts, 2) }} Tsh</p>
            </div>
            <i class="fas fa-hand-holding-usd text-yellow-500 text-2xl"></i>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow border border-gray-200 p-3">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500">Salio la Madeni (Bado)</p>
                <p class="text-xl font-bold text-red-700">{{ number_format($totalOutstanding, 2) }} Tsh</p>
            </div>
            <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
    <!-- Top Customers by Sales -->
    <div class="bg-white rounded-lg shadow border border-gray-200 p-3">
        <h3 class="text-md font-bold text-gray-800 mb-2 flex items-center">
            <i class="fas fa-trophy text-yellow-500 mr-2"></i> Wateja Wenye Mauzo Makubwa
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left">Mteja</th>
                        <th class="px-3 py-2 text-right">Jumla ya Mauzo</th>
                        <th class="px-3 py-2 text-center">Idadi ya Mauzo</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topSalesCustomers as $sale)
                        <tr class="border-b">
                            <td class="px-3 py-2">{{ $sale->mteja->jina ?? 'Mteja asiyejulikana' }}</td>
                            <td class="px-3 py-2 text-right font-semibold">{{ number_format($sale->total_sales, 2) }}</td>
                            <td class="px-3 py-2 text-center">{{ $sale->sale_count }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center py-4 text-gray-500">Hakuna data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Top Customers by Outstanding Debt -->
    <div class="bg-white rounded-lg shadow border border-gray-200 p-3">
        <h3 class="text-md font-bold text-gray-800 mb-2 flex items-center">
            <i class="fas fa-exclamation-circle text-red-500 mr-2"></i> Wateja Wenye Madeni Makubwa (Salio)
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left">Mteja</th>
                        <th class="px-3 py-2 text-right">Salio la Deni</th>
                        <th class="px-3 py-2 text-right">Jumla ya Deni</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topDebtCustomers as $debt)
                        <tr class="border-b">
                            <td class="px-3 py-2">{{ $debt->mteja->jina ?? 'Mteja asiyejulikana' }}</td>
                            <td class="px-3 py-2 text-right font-bold text-red-600">{{ number_format($debt->total_balance, 2) }}</td>
                            <td class="px-3 py-2 text-right">{{ number_format($debt->total_debt, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center py-4 text-gray-500">Hakuna data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if($customerDetails)
<div class="bg-white rounded-lg shadow border border-gray-200 p-4 mb-4">
    <div class="flex justify-between items-center mb-3">
        <h3 class="text-md font-bold text-gray-800">Taarifa za Mteja: {{ $customerDetails->jina }}</h3>
        <a href="{{ route('wateja.index') }}" class="text-sm bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded">
            <i class="fas fa-arrow-left mr-1"></i> Rudi kwa Wateja
        </a>
    </div>

    <!-- Tab Navigation -->
    <div class="border-b border-gray-200 mb-3">
        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
            <li class="mr-2">
                <button type="button" class="customer-tab-btn inline-block p-2 border-b-2 rounded-t-lg border-green-600 text-green-600" data-tab="sales-tab">Mauzo Yake</button>
            </li>
            <li>
                <button type="button" class="customer-tab-btn inline-block p-2 border-b-2 rounded-t-lg border-transparent text-gray-500 hover:text-gray-600" data-tab="debts-tab">Madeni Yake</button>
            </li>
        </ul>
    </div>

    <!-- Tab: Mauzo Yake -->
    <div id="sales-tab" class="customer-tab-content active">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2">Tarehe</th>
                        <th class="px-3 py-2">Risiti</th>
                        <th class="px-3 py-2">Bidhaa</th>
                        <th class="px-3 py-2 text-right">Idadi</th>
                        <th class="px-3 py-2 text-right">Jumla</th>
                        <th class="px-3 py-2 text-center">Malipo</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customerSales as $sale)
                        <tr class="border-b">
                            <td class="px-3 py-2">{{ $sale->created_at->format('d/m/Y') }}</td>
                            <td class="px-3 py-2 font-mono">{{ $sale->receipt_no ?? '-' }}</td>
                            <td class="px-3 py-2">{{ $sale->bidhaa->jina ?? '-' }}</td>
                            <td class="px-3 py-2 text-right">{{ number_format($sale->idadi, 2) }}</td>
                            <td class="px-3 py-2 text-right">{{ number_format($sale->jumla, 2) }}</td>
                            <td class="px-3 py-2 text-center">{{ ucfirst($sale->lipa_kwa ?? 'cash') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-4 text-gray-500">Hakuna mauzo kwa mteja huyu</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tab: Madeni Yake -->
    <div id="debts-tab" class="customer-tab-content hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2">Tarehe ya Deni</th>
                        <th class="px-3 py-2">Bidhaa</th>
                        <th class="px-3 py-2 text-right">Idadi</th>
                        <th class="px-3 py-2 text-right">Jumla</th>
                        <th class="px-3 py-2 text-right">Baki</th>
                        <th class="px-3 py-2">Tarehe ya Malipo</th>
                        <th class="px-3 py-2">Vitendo</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customerDebts as $debt)
                        <tr class="border-b">
                            <td class="px-3 py-2">{{ $debt->created_at->format('d/m/Y') }}</td>
                            <td class="px-3 py-2">{{ $debt->bidhaa->jina ?? '-' }}</td>
                            <td class="px-3 py-2 text-right">{{ number_format($debt->idadi, 2) }}</td>
                            <td class="px-3 py-2 text-right">{{ number_format($debt->jumla, 2) }}</td>
                            <td class="px-3 py-2 text-right font-semibold {{ $debt->baki > 0 ? 'text-red-600' : 'text-green-600' }}">
                                {{ number_format($debt->baki, 2) }}
                            </td>
                            <td class="px-3 py-2">{{ $debt->tarehe_malipo ? \Carbon\Carbon::parse($debt->tarehe_malipo)->format('d/m/Y') : '-' }}</td>
                            <td class="px-3 py-2">
                                <button class="view-debt-repayments bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs" data-debt-id="{{ $debt->id }}">
                                    <i class="fas fa-eye mr-1"></i> Marejesho
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-4 text-gray-500">Hakuna madeni kwa mteja huyu</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
<!-- Modal for viewing repayments -->
<div id="repayments-modal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-lg shadow-lg w-full max-w-md mx-auto z-50">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-800">Marejesho ya Deni</h3>
            <p id="debt-info" class="text-xs text-gray-500 mt-1"></p>
        </div>
        <div id="repayments-list" class="p-4 max-h-96 overflow-y-auto">
            <p class="text-center text-gray-500 text-sm">Inapakia...</p>
        </div>
        <div class="p-4 border-t border-gray-200">
            <button id="close-repayments-modal" class="w-full px-4 py-2 bg-gray-500 text-white rounded text-sm">Funga</button>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .customer-tab-btn.active {
        border-bottom-color: #10b981 !important;
        color: #10b981 !important;
    }
    .modal {
        transition: opacity 0.2s ease;
    }
</style>
@endpush
@push('scripts')
<script>
document.addEventListener('DOMContentLOADED', function() {
    // --- Tabs for customer details (Mauzo Yake / Madeni Yake) ---
    const tabBtns = document.querySelectorAll('.customer-tab-btn');
    const tabContents = {
        'sales-tab': document.getElementById('sales-tab'),
        'debts-tab': document.getElementById('debts-tab')
    };

    function switchCustomerTab(tabId) {
        // Hide all tab contents
        Object.values(tabContents).forEach(content => {
            if (content) content.classList.add('hidden');
        });
        // Show selected tab
        if (tabContents[tabId]) tabContents[tabId].classList.remove('hidden');
        // Update button styles
        tabBtns.forEach(btn => {
            btn.classList.remove('border-green-600', 'text-green-600');
            btn.classList.add('border-transparent', 'text-gray-500');
            if (btn.getAttribute('data-tab') === tabId) {
                btn.classList.add('border-green-600', 'text-green-600');
                btn.classList.remove('border-transparent', 'text-gray-500');
            }
        });
    }

    tabBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const tabId = btn.getAttribute('data-tab');
            if (tabId && tabContents[tabId]) {
                switchCustomerTab(tabId);
            }
        });
    });

    // Ensure default active tab is visible (sales-tab)
    if (tabContents['sales-tab'] && !tabContents['sales-tab'].classList.contains('active')) {
        switchCustomerTab('sales-tab');
    }

    // --- Repayments modal ---
    const modal = document.getElementById('repayments-modal');
    const closeBtn = document.getElementById('close-repayments-modal');
    const repaymentsList = document.getElementById('repayments-list');
    const debtInfo = document.getElementById('debt-info');

    if (closeBtn) {
        closeBtn.addEventListener('click', () => modal.classList.add('hidden'));
    }

    document.querySelectorAll('.view-debt-repayments').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            const debtId = btn.getAttribute('data-debt-id');
            modal.classList.remove('hidden');
            repaymentsList.innerHTML = '<p class="text-center text-gray-500 text-sm">Inapakia...</p>';
            debtInfo.textContent = '';

            try {
                const response = await fetch(`/madeni/${debtId}/repayments`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await response.json();

                if (data.success && data.repayments && data.repayments.length > 0) {
                    debtInfo.textContent = `Deni la ${data.bidhaa || 'bidhaa'} - Jumla: ${data.total}`;
                    repaymentsList.innerHTML = data.repayments.map(rep => `
                        <div class="border-b border-gray-100 py-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tarehe:</span>
                                <span>${new Date(rep.tarehe).toLocaleDateString()}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Kiasi Kilicholipwa:</span>
                                <span class="font-semibold text-green-700">${parseFloat(rep.kiasi).toLocaleString()} Tsh</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Njia ya Malipo:</span>
                                <span>${rep.lipa_kwa || 'cash'}</span>
                            </div>
                        </div>
                    `).join('');
                } else {
                    debtInfo.textContent = `Deni la ${data.bidhaa || 'bidhaa'}`;
                    repaymentsList.innerHTML = '<p class="text-center text-gray-500 text-sm">Hakuna marejesho bado</p>';
                }
            } catch (err) {
                console.error(err);
                repaymentsList.innerHTML = '<p class="text-center text-red-500 text-sm">Hitilafu katika kupakia marejesho</p>';
            }
        });
    });

    // Close modal on overlay click
    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal || e.target.classList.contains('modal-overlay')) {
                modal.classList.add('hidden');
            }
        });
    }
});
</script>
@endpush