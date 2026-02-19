@extends('layouts.app')

@section('title', 'Ripoti za Biashara')
@section('page-title', 'Ripoti za Biashara')
@section('page-subtitle', 'Tengeneza ripoti kwa urahisi')

@section('content')
<div class="space-y-4" id="app-container">
    <!-- Notifications -->
    <div id="notification-container" class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-sm px-4 pointer-events-none"></div>

  
    <!-- Main Report Card -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm">
        <div class="p-4 border-b border-gray-200 bg-emerald-50">
            <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-chart-bar text-amber-600 mr-2"></i>
                Chagua Aina ya Ripoti na Muda
            </h2>
            <p class="text-sm text-gray-600 mt-1">Weka vigezo kisha bonyeza "Tengeneza Ripoti" kuona matokeo</p>
        </div>
        
        <div class="p-4">
            <form id="reportForm" class="space-y-4">
                @csrf
                
                <!-- Selection Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Report Type -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Aina ya Ripoti *</label>
                        <select name="report_type" id="report_type" 
                                class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500">
                            <option value="sales">📊 Mauzo</option>
                            <option value="manunuzi">🛒 Manunuzi</option>
                            <option value="matumizi">💰 Matumizi</option>
                            <option value="general">📋 Jumla ya Biashara</option>
                            <option value="mapato_by_method">💳 Mapato kwa Njia ya Malipo</option>
                        </select>
                    </div>

                    <!-- Time Period -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Muda wa Ripoti *</label>
                        <select name="date_range" id="date_range" 
                                class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500">
                            <option value="today">Leo</option>
                            <option value="yesterday">Jana</option>
                            <option value="week">Wiki hii</option>
                            <option value="month" selected>Mwezi huu</option>
                            <option value="year">Mwaka huu</option>
                            <option value="custom">Tarehe Maalum</option>
                        </select>
                    </div>

                    <!-- Quick Action Buttons -->
                    <div class="flex items-end gap-2">
                        <button type="button" id="generateBtn" 
                                class="flex-1 px-4 py-2 bg-amber-600 text-white rounded hover:bg-amber-700 text-sm font-medium">
                            <i class="fas fa-chart-bar mr-1"></i> Tengeneza
                        </button>
                        <button type="button" id="resetBtn" 
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 text-sm">
                            <i class="fas fa-redo"></i>
                        </button>
                        <a href="{{ route('uchambuzi.index') }}" 
                           class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm font-medium">
                            <i class="fas fa-arrow-left mr-1"></i> Rudi
                        </a>
                    </div>
                </div>

                <!-- Custom Date Range (Hidden by default) -->
                <div id="custom-dates" class="hidden grid grid-cols-1 md:grid-cols-2 gap-4 pt-2 border-t border-gray-200">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Kuanzia Tarehe</label>
                        <input type="date" name="from" id="dateFrom" 
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Mpaka Tarehe</label>
                        <input type="date" name="to" id="dateTo" 
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-amber-500">
                    </div>
                </div>

                <!-- Error Message -->
                <div id="errorMessage" class="hidden p-3 bg-red-50 border border-red-200 rounded text-sm text-red-700">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    <span id="errorText"></span>
                </div>
            </form>
        </div>
    </div>

    <!-- Report Results Section (Hidden initially) -->
    <div id="report-results" class="hidden space-y-4">
        <!-- Report Header with Export Buttons -->
        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <div>
                    <h3 class="text-sm font-semibold text-gray-800" id="report-title">Ripoti</h3>
                    <p class="text-xs text-gray-600" id="report-subtitle"></p>
                    <p class="text-xs text-gray-500 mt-1" id="report-period"></p>
                </div>
                <div class="flex gap-2">
                    <button onclick="downloadReportPDF()" class="px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm font-medium">
                        <i class="fas fa-file-pdf mr-1"></i> PDF
                    </button>
                    <button onclick="downloadReportExcel()" class="px-3 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm font-medium">
                        <i class="fas fa-file-excel mr-1"></i> Excel
                    </button>
                </div>
            </div>
        </div>

        <!-- Summary Cards Container -->
        <div id="summary-cards" class="grid grid-cols-1 md:grid-cols-4 gap-3"></div>

        <!-- Report Table Container -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead id="report-table-header"></thead>
                    <tbody id="report-table-body" class="divide-y divide-gray-100"></tbody>
                </table>
            </div>
        </div>

        <!-- Grand Total -->
        <div id="grand-total" class="hidden p-4 bg-amber-50 border border-amber-200 rounded-lg text-right">
            <span class="text-sm font-medium text-gray-700 mr-2">Jumla Kuu:</span>
            <span class="text-lg font-bold text-amber-700" id="grand-total-value">0.00 TZS</span>
        </div>
    </div>
</div>

<!-- Loading Spinner (Hidden initially) -->
<div id="loading-spinner" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white p-5 rounded-lg shadow-xl flex items-center space-x-3">
        <svg class="animate-spin h-6 w-6 text-amber-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="text-gray-700">Inatengeneza ripoti...</span>
    </div>
</div>
@endsection

@push('styles')
<style>
.animate-fade-in {
    animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Decimal formatting for idadi column */
.idadi-decimal {
    font-family: monospace;
    text-align: center;
}

/* Mobile responsive */
@media (max-width: 640px) {
    .table-responsive {
        display: block;
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
}
</style>
@endpush

@push('scripts')
<script>
class ReportManager {
    constructor() {
        this.init();
    }

    init() {
        this.bindEvents();
        this.setDefaultDates();
        this.loadTodayStats();
    }

    bindEvents() {
        // Date range toggle
        document.getElementById('date_range').addEventListener('change', (e) => {
            this.toggleCustomDates(e.target.value);
        });

        // Generate button
        document.getElementById('generateBtn').addEventListener('click', () => {
            this.generateReport();
        });

        // Reset button
        document.getElementById('resetBtn').addEventListener('click', () => {
            this.resetForm();
        });
    }

    setDefaultDates() {
        const today = new Date();
        const weekAgo = new Date();
        weekAgo.setDate(weekAgo.getDate() - 7);
        
        const todayStr = today.toISOString().split('T')[0];
        const weekAgoStr = weekAgo.toISOString().split('T')[0];
        
        const dateFrom = document.getElementById('dateFrom');
        const dateTo = document.getElementById('dateTo');
        
        if (dateFrom) dateFrom.value = weekAgoStr;
        if (dateTo) dateTo.value = todayStr;
    }

    toggleCustomDates(value) {
        const customDates = document.getElementById('custom-dates');
        if (value === 'custom') {
            customDates.classList.remove('hidden');
        } else {
            customDates.classList.add('hidden');
        }
    }

    async loadTodayStats() {
        try {
            // You can implement this to load today's stats via API
            // For now, we'll leave it as is
        } catch (error) {
            console.error('Error loading stats:', error);
        }
    }

    async generateReport() {
        // Validate form
        if (!this.validateForm()) {
            return;
        }

        // Show loading
        document.getElementById('loading-spinner').classList.remove('hidden');

        // Get form data
        const formData = new FormData(document.getElementById('reportForm'));
        
        try {
            const response = await fetch('{{ route("user.reports.generate") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const result = await response.json();

            if (response.ok && result.success) {
                this.displayReport(result.data);
                this.showNotification('Ripoti imetengenezwa kwa mafanikio', 'success');
            } else {
                const error = result.message || 'Hitilafu imetokea';
                this.showNotification(error, 'error');
            }
        } catch (error) {
            this.showNotification('Hitilafu ya mtandao', 'error');
        } finally {
            // Hide loading
            document.getElementById('loading-spinner').classList.add('hidden');
        }
    }

    validateForm() {
        const reportType = document.getElementById('report_type').value;
        const dateRange = document.getElementById('date_range').value;
        const errorDiv = document.getElementById('errorMessage');
        const errorText = document.getElementById('errorText');

        // Hide previous error
        errorDiv.classList.add('hidden');

        if (!reportType) {
            errorText.textContent = 'Tafadhali chagua aina ya ripoti';
            errorDiv.classList.remove('hidden');
            return false;
        }

        if (dateRange === 'custom') {
            const from = document.getElementById('dateFrom').value;
            const to = document.getElementById('dateTo').value;

            if (!from || !to) {
                errorText.textContent = 'Tafadhali chagua tarehe zote mbili';
                errorDiv.classList.remove('hidden');
                return false;
            }

            if (new Date(from) > new Date(to)) {
                errorText.textContent = 'Tarehe ya kuanzia haiwezi kuwa baada ya tarehe ya mwisho';
                errorDiv.classList.remove('hidden');
                return false;
            }
        }

        return true;
    }

    displayReport(data) {
        // Show results section
        document.getElementById('report-results').classList.remove('hidden');

        // Update report header
        document.getElementById('report-title').textContent = data.reportTitle || 'Ripoti';
        document.getElementById('report-subtitle').textContent = data.reportSubtitle || '';
        
        let periodText = '';
        if (data.displayFrom && data.displayTo) {
            periodText = `Kipindi: ${data.displayFrom} - ${data.displayTo}`;
        }
        document.getElementById('report-period').textContent = periodText;

        // Display based on report type
        switch (data.reportType) {
            case 'sales':
                this.displaySalesReport(data);
                break;
            case 'manunuzi':
                this.displayPurchasesReport(data);
                break;
            case 'matumizi':
                this.displayExpensesReport(data);
                break;
            case 'general':
                this.displayGeneralReport(data);
                break;
            case 'mapato_by_method':
                this.displayIncomeByMethodReport(data);
                break;
        }
    }

    displaySalesReport(data) {
        // Summary Cards
        const summaryHtml = `
            <div class="bg-white p-3 rounded-lg border border-amber-200 shadow-sm">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Jumla ya Mauzo</p>
                        <p class="text-xl font-bold text-amber-700">${this.formatNumber(data.totalSales || 0)}</p>
                    </div>
                    <i class="fas fa-shopping-cart text-amber-500 text-lg"></i>
                </div>
            </div>
            <div class="bg-white p-3 rounded-lg border border-blue-200 shadow-sm">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Marejesho ya Madeni</p>
                        <p class="text-xl font-bold text-blue-700">${this.formatNumber(data.totalDebtRepayments || 0)}</p>
                    </div>
                    <i class="fas fa-hand-holding-usd text-blue-500 text-lg"></i>
                </div>
            </div>
            <div class="bg-white p-3 rounded-lg border border-green-200 shadow-sm">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Jumla ya Mapato</p>
                        <p class="text-xl font-bold text-green-700">${this.formatNumber(data.grandTotal || 0)}</p>
                    </div>
                    <i class="fas fa-coins text-green-500 text-lg"></i>
                </div>
            </div>
            <div class="bg-white p-3 rounded-lg border border-purple-200 shadow-sm">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Idadi ya Mauzo</p>
                        <p class="text-xl font-bold text-purple-700">${data.sales ? data.sales.length : 0}</p>
                    </div>
                    <i class="fas fa-list text-purple-500 text-lg"></i>
                </div>
            </div>
        `;
        document.getElementById('summary-cards').innerHTML = summaryHtml;

        // Payment Method Summary (Additional cards)
        const paymentSummary = `
            <div class="col-span-4 grid grid-cols-3 gap-3 mt-2">
                <div class="bg-gray-50 p-2 rounded border border-gray-200">
                    <p class="text-xs text-gray-600">Cash</p>
                    <p class="text-sm font-bold text-gray-800">${this.formatNumber(data.totalCashIncome || 0)}</p>
                </div>
                <div class="bg-gray-50 p-2 rounded border border-gray-200">
                    <p class="text-xs text-gray-600">Lipa Namba</p>
                    <p class="text-sm font-bold text-gray-800">${this.formatNumber(data.totalMobileIncome || 0)}</p>
                </div>
                <div class="bg-gray-50 p-2 rounded border border-gray-200">
                    <p class="text-xs text-gray-600">Benki</p>
                    <p class="text-sm font-bold text-gray-800">${this.formatNumber(data.totalBankIncome || 0)}</p>
                </div>
            </div>
        `;
        document.getElementById('summary-cards').innerHTML += paymentSummary;

        // Table Header
        const tableHeader = `
            <tr class="bg-amber-50">
                <th class="px-4 py-2 text-left font-medium text-amber-800">#</th>
                <th class="px-4 py-2 text-left font-medium text-amber-800">Tarehe</th>
                <th class="px-4 py-2 text-left font-medium text-amber-800">Bidhaa</th>
                <th class="px-4 py-2 text-center font-medium text-amber-800">Idadi</th>
                <th class="px-4 py-2 text-center font-medium text-amber-800">Njia ya Malipo</th>
                <th class="px-4 py-2 text-right font-medium text-amber-800">Jumla (TZS)</th>
            </tr>
        `;
        document.getElementById('report-table-header').innerHTML = tableHeader;

        // Table Body
        let tableBody = '';
        if (data.sales && data.sales.length > 0) {
            data.sales.forEach((sale, index) => {
                const paymentMethod = sale.lipa_kwa || 'cash';
                const methodName = paymentMethod === 'cash' ? 'Cash' : 
                                  (paymentMethod === 'lipa_namba' ? 'Lipa Namba' : 'Benki');
                
                tableBody += `
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2">${index + 1}</td>
                        <td class="px-4 py-2">${sale.created_at ? new Date(sale.created_at).toLocaleDateString('sw-TZ') : ''}</td>
                        <td class="px-4 py-2">${sale.bidhaa?.jina || 'N/A'}</td>
                        <td class="px-4 py-2 text-center">${this.formatDecimal(sale.idadi)}</td>
                        <td class="px-4 py-2 text-center">${methodName}</td>
                        <td class="px-4 py-2 text-right">${this.formatNumber(sale.jumla)}</td>
                    </tr>
                `;
            });
        } else {
            tableBody = '<tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">Hakuna mauzo katika kipindi hiki</td></tr>';
        }
        document.getElementById('report-table-body').innerHTML = tableBody;

        // Grand Total
        document.getElementById('grand-total').classList.remove('hidden');
        document.getElementById('grand-total-value').textContent = this.formatNumber(data.grandTotal || 0) + ' TZS';
    }

    displayPurchasesReport(data) {
        // Summary Cards
        const summaryHtml = `
            <div class="bg-white p-3 rounded-lg border border-emerald-200 shadow-sm">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Jumla ya Gharama</p>
                        <p class="text-xl font-bold text-emerald-700">${this.formatNumber(data.totalCost || 0)}</p>
                    </div>
                    <i class="fas fa-money-bill-wave text-emerald-500 text-lg"></i>
                </div>
            </div>
            <div class="bg-white p-3 rounded-lg border border-blue-200 shadow-sm">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Jumla ya Bidhaa</p>
                        <p class="text-xl font-bold text-blue-700">${this.formatDecimal(data.totalItems || 0)}</p>
                    </div>
                    <i class="fas fa-boxes text-blue-500 text-lg"></i>
                </div>
            </div>
            <div class="bg-white p-3 rounded-lg border border-purple-200 shadow-sm">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Wastani wa Bei</p>
                        <p class="text-xl font-bold text-purple-700">${this.formatNumber(data.averageCost || 0)}</p>
                    </div>
                    <i class="fas fa-calculator text-purple-500 text-lg"></i>
                </div>
            </div>
            <div class="bg-white p-3 rounded-lg border border-amber-200 shadow-sm">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Idadi ya Manunuzi</p>
                        <p class="text-xl font-bold text-amber-700">${data.manunuzi ? data.manunuzi.length : 0}</p>
                    </div>
                    <i class="fas fa-shopping-cart text-amber-500 text-lg"></i>
                </div>
            </div>
        `;
        document.getElementById('summary-cards').innerHTML = summaryHtml;

        // Table Header
        const tableHeader = `
            <tr class="bg-emerald-50">
                <th class="px-4 py-2 text-left font-medium text-emerald-800">#</th>
                <th class="px-4 py-2 text-left font-medium text-emerald-800">Tarehe</th>
                <th class="px-4 py-2 text-left font-medium text-emerald-800">Bidhaa</th>
                <th class="px-4 py-2 text-center font-medium text-emerald-800">Idadi</th>
                <th class="px-4 py-2 text-right font-medium text-emerald-800">Bei (TZS)</th>
                <th class="px-4 py-2 text-right font-medium text-emerald-800">Bei kwa Kimoja</th>
            </tr>
        `;
        document.getElementById('report-table-header').innerHTML = tableHeader;

        // Table Body
        let tableBody = '';
        if (data.manunuzi && data.manunuzi.length > 0) {
            data.manunuzi.forEach((purchase, index) => {
                tableBody += `
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2">${index + 1}</td>
                        <td class="px-4 py-2">${purchase.created_at ? new Date(purchase.created_at).toLocaleDateString('sw-TZ') : ''}</td>
                        <td class="px-4 py-2">${purchase.bidhaa?.jina || 'N/A'}</td>
                        <td class="px-4 py-2 text-center"><span class="px-2 py-1 bg-emerald-100 text-emerald-800 rounded text-xs">${this.formatDecimal(purchase.idadi)}</span></td>
                        <td class="px-4 py-2 text-right">${this.formatNumber(purchase.bei)}</td>
                        <td class="px-4 py-2 text-right text-gray-600">${this.formatNumber(purchase.unit_cost)}</td>
                    </tr>
                `;
            });
        } else {
            tableBody = '<tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">Hakuna manunuzi katika kipindi hiki</td></tr>';
        }
        document.getElementById('report-table-body').innerHTML = tableBody;

        // Grand Total
        document.getElementById('grand-total').classList.remove('hidden');
        document.getElementById('grand-total-value').textContent = this.formatNumber(data.totalCost || 0) + ' TZS';
    }

    displayExpensesReport(data) {
        // Summary Cards
        const summaryHtml = `
            <div class="bg-white p-3 rounded-lg border border-blue-200 shadow-sm">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Jumla ya Matumizi</p>
                        <p class="text-xl font-bold text-blue-700">${this.formatNumber(data.totalExpenses || 0)}</p>
                    </div>
                    <i class="fas fa-wallet text-blue-500 text-lg"></i>
                </div>
            </div>
            <div class="bg-white p-3 rounded-lg border border-green-200 shadow-sm">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Idadi ya Matumizi</p>
                        <p class="text-xl font-bold text-green-700">${data.matumizi ? data.matumizi.length : 0}</p>
                    </div>
                    <i class="fas fa-list text-green-500 text-lg"></i>
                </div>
            </div>
            <div class="bg-white p-3 rounded-lg border border-purple-200 shadow-sm">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Aina za Matumizi</p>
                        <p class="text-xl font-bold text-purple-700">${Object.keys(data.totalsByCategory || {}).length}</p>
                    </div>
                    <i class="fas fa-tags text-purple-500 text-lg"></i>
                </div>
            </div>
        `;
        document.getElementById('summary-cards').innerHTML = summaryHtml;

        // Category Summary (if available)
        if (data.totalsByCategory && Object.keys(data.totalsByCategory).length > 0) {
            let categoryHtml = '<div class="col-span-4 grid grid-cols-2 md:grid-cols-4 gap-3 mt-2">';
            for (const [category, total] of Object.entries(data.totalsByCategory)) {
                categoryHtml += `
                    <div class="bg-gray-50 p-2 rounded border border-gray-200">
                        <p class="text-xs text-gray-600">${category}</p>
                        <p class="text-sm font-bold text-gray-800">${this.formatNumber(total)}</p>
                    </div>
                `;
            }
            categoryHtml += '</div>';
            document.getElementById('summary-cards').innerHTML += categoryHtml;
        }

        // Table Header
        const tableHeader = `
            <tr class="bg-blue-50">
                <th class="px-4 py-2 text-left font-medium text-blue-800">#</th>
                <th class="px-4 py-2 text-left font-medium text-blue-800">Tarehe</th>
                <th class="px-4 py-2 text-left font-medium text-blue-800">Aina</th>
                <th class="px-4 py-2 text-left font-medium text-blue-800">Maelezo</th>
                <th class="px-4 py-2 text-right font-medium text-blue-800">Gharama (TZS)</th>
            </tr>
        `;
        document.getElementById('report-table-header').innerHTML = tableHeader;

        // Table Body
        let tableBody = '';
        if (data.matumizi && data.matumizi.length > 0) {
            data.matumizi.forEach((expense, index) => {
                tableBody += `
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2">${index + 1}</td>
                        <td class="px-4 py-2">${expense.created_at ? new Date(expense.created_at).toLocaleDateString('sw-TZ') : ''}</td>
                        <td class="px-4 py-2">${expense.aina || 'Zingine'}</td>
                        <td class="px-4 py-2">${expense.maelezo || '--'}</td>
                        <td class="px-4 py-2 text-right">${this.formatNumber(expense.gharama)}</td>
                    </tr>
                `;
            });
        } else {
            tableBody = '<tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">Hakuna matumizi katika kipindi hiki</td></tr>';
        }
        document.getElementById('report-table-body').innerHTML = tableBody;

        // Grand Total
        document.getElementById('grand-total').classList.remove('hidden');
        document.getElementById('grand-total-value').textContent = this.formatNumber(data.totalExpenses || 0) + ' TZS';
    }

    displayGeneralReport(data) {
        // Summary Cards - Payment Methods
        const summaryHtml = `
            <div class="bg-white p-3 rounded-lg border border-amber-200 shadow-sm">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Cash</p>
                        <p class="text-xl font-bold text-amber-700">${this.formatNumber(data.jumlaMapatoCash || 0)}</p>
                    </div>
                    <i class="fas fa-money-bill-wave text-amber-500 text-lg"></i>
                </div>
            </div>
            <div class="bg-white p-3 rounded-lg border border-blue-200 shadow-sm">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Lipa Namba</p>
                        <p class="text-xl font-bold text-blue-700">${this.formatNumber(data.jumlaMapatoMobile || 0)}</p>
                    </div>
                    <i class="fas fa-mobile-alt text-blue-500 text-lg"></i>
                </div>
            </div>
            <div class="bg-white p-3 rounded-lg border border-green-200 shadow-sm">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Benki</p>
                        <p class="text-xl font-bold text-green-700">${this.formatNumber(data.jumlaMapatoBank || 0)}</p>
                    </div>
                    <i class="fas fa-university text-green-500 text-lg"></i>
                </div>
            </div>
            <div class="bg-white p-3 rounded-lg border border-purple-200 shadow-sm">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Jumla ya Mapato</p>
                        <p class="text-xl font-bold text-purple-700">${this.formatNumber(data.jumlaMapato || 0)}</p>
                    </div>
                    <i class="fas fa-chart-line text-purple-500 text-lg"></i>
                </div>
            </div>
        `;
        document.getElementById('summary-cards').innerHTML = summaryHtml;

        // Profit & Loss Summary
        const profitHtml = `
            <div class="col-span-4 grid grid-cols-2 md:grid-cols-4 gap-3 mt-2">
                <div class="bg-emerald-50 p-3 rounded-lg border border-emerald-200">
                    <p class="text-xs text-gray-600">Faida ya Mauzo</p>
                    <p class="text-lg font-bold text-emerald-700">${this.formatNumber(data.faidaMauzo || 0)}</p>
                </div>
                <div class="bg-emerald-50 p-3 rounded-lg border border-emerald-200">
                    <p class="text-xs text-gray-600">Faida ya Marejesho</p>
                    <p class="text-lg font-bold text-emerald-700">${this.formatNumber(data.faidaMarejesho || 0)}</p>
                </div>
                <div class="bg-red-50 p-3 rounded-lg border border-red-200">
                    <p class="text-xs text-gray-600">Matumizi</p>
                    <p class="text-lg font-bold text-red-700">${this.formatNumber(data.jumlaMatumizi || 0)}</p>
                </div>
                <div class="bg-blue-50 p-3 rounded-lg border border-blue-200">
                    <p class="text-xs text-gray-600">Fedha Dukani</p>
                    <p class="text-lg font-bold text-blue-700">${this.formatNumber(data.fedhaDukani || 0)}</p>
                </div>
                <div class="bg-purple-50 p-3 rounded-lg border border-purple-200 col-span-2">
                    <p class="text-xs text-gray-600">Faida Halisi</p>
                    <p class="text-lg font-bold text-purple-700">${this.formatNumber(data.faidaHalisi || 0)}</p>
                </div>
            </div>
        `;
        document.getElementById('summary-cards').innerHTML += profitHtml;

        // Table Header - Show Mauzo and Matumizi summary
        const tableHeader = `
            <tr class="bg-gray-50">
                <th class="px-4 py-2 text-left font-medium text-gray-800" colspan="2">Muhtasari wa Mapato</th>
                <th class="px-4 py-2 text-left font-medium text-gray-800" colspan="2">Muhtasari wa Matumizi</th>
            </tr>
            <tr class="bg-amber-50">
                <th class="px-4 py-2 text-left font-medium text-amber-800">Aina</th>
                <th class="px-4 py-2 text-right font-medium text-amber-800">Kiasi (TZS)</th>
                <th class="px-4 py-2 text-left font-medium text-amber-800">Aina</th>
                <th class="px-4 py-2 text-right font-medium text-amber-800">Kiasi (TZS)</th>
            </tr>
        `;
        document.getElementById('report-table-header').innerHTML = tableHeader;

        // Table Body
        let tableBody = `
            <tr>
                <td class="px-4 py-2">Mauzo ya Cash</td>
                <td class="px-4 py-2 text-right">${this.formatNumber(data.mapatoCashMauzo || 0)}</td>
                <td class="px-4 py-2">Matumizi</td>
                <td class="px-4 py-2 text-right">${this.formatNumber(data.jumlaMatumizi || 0)}</td>
            </tr>
            <tr>
                <td class="px-4 py-2">Mauzo ya Lipa Namba</td>
                <td class="px-4 py-2 text-right">${this.formatNumber(data.mapatoMobileMauzo || 0)}</td>
                <td class="px-4 py-2"></td>
                <td class="px-4 py-2 text-right"></td>
            </tr>
            <tr>
                <td class="px-4 py-2">Mauzo ya Benki</td>
                <td class="px-4 py-2 text-right">${this.formatNumber(data.mapatoBankMauzo || 0)}</td>
                <td class="px-4 py-2"></td>
                <td class="px-4 py-2 text-right"></td>
            </tr>
            <tr>
                <td class="px-4 py-2">Marejesho ya Madeni (Cash)</td>
                <td class="px-4 py-2 text-right">${this.formatNumber(data.mapatoCashMadeni || 0)}</td>
                <td class="px-4 py-2"></td>
                <td class="px-4 py-2 text-right"></td>
            </tr>
            <tr>
                <td class="px-4 py-2">Marejesho ya Madeni (Lipa Namba)</td>
                <td class="px-4 py-2 text-right">${this.formatNumber(data.mapatoMobileMadeni || 0)}</td>
                <td class="px-4 py-2"></td>
                <td class="px-4 py-2 text-right"></td>
            </tr>
            <tr>
                <td class="px-4 py-2">Marejesho ya Madeni (Benki)</td>
                <td class="px-4 py-2 text-right">${this.formatNumber(data.mapatoBankMadeni || 0)}</td>
                <td class="px-4 py-2"></td>
                <td class="px-4 py-2 text-right"></td>
            </tr>
            <tr class="bg-amber-50 font-bold">
                <td class="px-4 py-2">Jumla ya Mapato</td>
                <td class="px-4 py-2 text-right">${this.formatNumber(data.jumlaMapato || 0)}</td>
                <td class="px-4 py-2">Jumla ya Matumizi</td>
                <td class="px-4 py-2 text-right">${this.formatNumber(data.jumlaMatumizi || 0)}</td>
            </tr>
        `;
        document.getElementById('report-table-body').innerHTML = tableBody;

        // Grand Total
        document.getElementById('grand-total').classList.remove('hidden');
        document.getElementById('grand-total-value').textContent = this.formatNumber(data.fedhaDukani || 0) + ' TZS (Fedha Dukani)';
    }

    displayIncomeByMethodReport(data) {
        // Summary Cards
        const summaryHtml = `
            <div class="bg-white p-3 rounded-lg border border-amber-200 shadow-sm">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Cash</p>
                        <p class="text-xl font-bold text-amber-700">${this.formatNumber(data.totalCash || 0)}</p>
                    </div>
                    <i class="fas fa-money-bill-wave text-amber-500 text-lg"></i>
                </div>
            </div>
            <div class="bg-white p-3 rounded-lg border border-blue-200 shadow-sm">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Lipa Namba</p>
                        <p class="text-xl font-bold text-blue-700">${this.formatNumber(data.totalMobile || 0)}</p>
                    </div>
                    <i class="fas fa-mobile-alt text-blue-500 text-lg"></i>
                </div>
            </div>
            <div class="bg-white p-3 rounded-lg border border-green-200 shadow-sm">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Benki</p>
                        <p class="text-xl font-bold text-green-700">${this.formatNumber(data.totalBank || 0)}</p>
                    </div>
                    <i class="fas fa-university text-green-500 text-lg"></i>
                </div>
            </div>
            <div class="bg-white p-3 rounded-lg border border-purple-200 shadow-sm">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Jumla Kuu</p>
                        <p class="text-xl font-bold text-purple-700">${this.formatNumber(data.grandTotal || 0)}</p>
                    </div>
                    <i class="fas fa-chart-pie text-purple-500 text-lg"></i>
                </div>
            </div>
        `;
        document.getElementById('summary-cards').innerHTML = summaryHtml;

        // Table Header
        const tableHeader = `
            <tr class="bg-amber-50">
                <th class="px-4 py-2 text-left font-medium text-amber-800">#</th>
                <th class="px-4 py-2 text-left font-medium text-amber-800">Aina</th>
                <th class="px-4 py-2 text-left font-medium text-amber-800">Njia ya Malipo</th>
                <th class="px-4 py-2 text-center font-medium text-amber-800">Idadi</th>
                <th class="px-4 py-2 text-right font-medium text-amber-800">Jumla (TZS)</th>
            </tr>
        `;
        document.getElementById('report-table-header').innerHTML = tableHeader;

        // Table Body
        let tableBody = '';
        let index = 1;

        // Sales by method
        if (data.salesByMethod && data.salesByMethod.length > 0) {
            data.salesByMethod.forEach(item => {
                const methodName = item.lipa_kwa === 'cash' ? 'Cash' : 
                                  (item.lipa_kwa === 'lipa_namba' ? 'Lipa Namba' : 'Benki');
                tableBody += `
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2">${index++}</td>
                        <td class="px-4 py-2">Mauzo</td>
                        <td class="px-4 py-2">${methodName}</td>
                        <td class="px-4 py-2 text-center">${item.count}</td>
                        <td class="px-4 py-2 text-right">${this.formatNumber(item.total)}</td>
                    </tr>
                `;
            });
        }

        // Debt repayments by method
        if (data.debtsByMethod && data.debtsByMethod.length > 0) {
            data.debtsByMethod.forEach(item => {
                const methodName = item.lipa_kwa === 'cash' ? 'Cash' : 
                                  (item.lipa_kwa === 'lipa_namba' ? 'Lipa Namba' : 'Benki');
                tableBody += `
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2">${index++}</td>
                        <td class="px-4 py-2">Marejesho ya Madeni</td>
                        <td class="px-4 py-2">${methodName}</td>
                        <td class="px-4 py-2 text-center">${item.count}</td>
                        <td class="px-4 py-2 text-right">${this.formatNumber(item.total)}</td>
                    </tr>
                `;
            });
        }

        if (index === 1) {
            tableBody = '<tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">Hakuna data katika kipindi hiki</td></tr>';
        }
        document.getElementById('report-table-body').innerHTML = tableBody;

        // Grand Total
        document.getElementById('grand-total').classList.remove('hidden');
        document.getElementById('grand-total-value').textContent = this.formatNumber(data.grandTotal || 0) + ' TZS';
    }

    // Format number with 2 decimal places
    formatNumber(value) {
        return new Intl.NumberFormat('sw-TZ', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(value || 0);
    }

    // Format decimal for display (show as integer if whole number)
    formatDecimal(value) {
        if (!value && value !== 0) return '0';
        const num = parseFloat(value);
        if (isNaN(num)) return '0';
        
        if (num % 1 === 0) {
            return num.toString();
        }
        return num.toFixed(2);
    }

    resetForm() {
        document.getElementById('reportForm').reset();
        this.setDefaultDates();
        this.toggleCustomDates(document.getElementById('date_range').value);
        document.getElementById('report-results').classList.add('hidden');
        document.getElementById('errorMessage').classList.add('hidden');
        this.showNotification('Fomu imeanzishwa upya', 'info');
    }

    showNotification(message, type = 'info') {
        const container = document.getElementById('notification-container');
        if (!container) return;
        
        const colors = {
            success: 'bg-emerald-50 border-emerald-200 text-emerald-800',
            error: 'bg-red-50 border-red-200 text-red-800',
            warning: 'bg-amber-50 border-amber-200 text-amber-800',
            info: 'bg-blue-50 border-blue-200 text-blue-800'
        };

        const notification = document.createElement('div');
        notification.className = `rounded border px-4 py-3 text-sm font-medium mb-2 ${colors[type]} shadow-sm animate-fade-in`;
        notification.textContent = message;

        container.appendChild(notification);

        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateY(-10px)';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    window.reportManager = new ReportManager();
});

// Download functions
function downloadReportPDF() {
    const form = document.getElementById('reportForm');
    form.action = '{{ route("user.reports.download") }}';
    form.method = 'POST';
    form.target = '_blank';
    form.submit();
    
    // Reset form attributes
    setTimeout(() => {
        form.action = '';
        form.method = 'GET';
        form.target = '';
    }, 100);
}

function downloadReportExcel() {
    // Implement Excel download if needed
    alert('Excel download feature coming soon');
}
</script>
@endpush