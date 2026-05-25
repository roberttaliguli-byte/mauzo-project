{{-- resources/views/user/daily_reports/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Ripoti kwa Siku - Ripoti za Biashara')
@section('page-title', 'Ripoti kwa Siku')
@section('page-subtitle', 'Tazama ripoti kwa siku, wiki au mwezi')

@section('content')
<div class="space-y-4" id="app-container">
    <!-- Notifications -->
    <div id="notification-container" class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-sm px-4 pointer-events-none"></div>

    <!-- Main Report Card -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm">
        <div class="p-4 border-b border-gray-200 bg-emerald-50">
            <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-calendar-week text-emerald-600 mr-2"></i>
                Chagua Vigezo vya Ripoti
            </h2>
            <p class="text-sm text-gray-600 mt-1">Chagua aina ya ripoti, muda na kipindi cha kuonyesha</p>
        </div>
        
        <div class="p-4">
            <form id="dailyReportForm" class="space-y-4">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Report Sub Type -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Aina ya Ripoti *</label>
                        <select name="report_sub_type" id="report_sub_type" 
                                class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="mauzo">💰 Mauzo, Mapato na Faida</option>
                            <option value="faida">📈 Faida</option>
                            <option value="biashara">🏢 Muhtasari wa Biashara</option>
                            <option value="matumizi">💸 Matumizi</option>
                        </select>
                    </div>

                    <!-- Group By -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Onyesha Kwa *</label>
                        <select name="group_by" id="group_by" 
                                class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="day">📅 Kwa Siku</option>
                            <option value="week">📆 Kwa Wiki</option>
                            <option value="month">🗓️ Kwa Mwezi</option>
                        </select>
                    </div>

                    <!-- Time Period -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Muda wa Ripoti *</label>
                        <select name="date_range" id="date_range" 
                                class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="today">Leo</option>
                            <option value="yesterday">Jana</option>
                            <option value="week">Wiki hii</option>
                            <option value="two_days">Siku 2 zilizopita</option>
                            <option value="three_days">Siku 3 zilizopita</option>
                            <option value="month" selected>Mwezi huu</option>
                            <option value="year">Mwaka huu</option>
                            <option value="custom">Tarehe Maalum</option>
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-end gap-2">
                        <button type="button" id="generateBtn" 
                                class="flex-1 px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 text-sm font-medium">
                            <i class="fas fa-chart-line mr-1"></i> Tengeneza
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

                <!-- Custom Date Range -->
                <div id="custom-dates" class="hidden grid grid-cols-1 md:grid-cols-2 gap-4 pt-2 border-t border-gray-200">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Kuanzia Tarehe</label>
                        <input type="date" name="from" id="dateFrom" 
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Mpaka Tarehe</label>
                        <input type="date" name="to" id="dateTo" 
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
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

    <!-- Report Results Section -->
    <div id="report-results" class="hidden space-y-4">
        <!-- Report Header -->
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

        <!-- Grouped Data Table -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm" id="report-table">
                    <thead id="report-table-header"></thead>
                    <tbody id="report-table-body" class="divide-y divide-gray-100"></tbody>
                </table>
            </div>
        </div>

        <!-- Grand Total -->
        <div id="grand-total" class="hidden p-4 bg-emerald-50 border border-emerald-200 rounded-lg">
            <div class="flex justify-between items-center">
                <span class="text-sm font-medium text-gray-700">JUMLA KUU:</span>
                <span class="text-lg font-bold text-emerald-700" id="grand-total-value">0.00 TZS</span>
            </div>
        </div>
    </div>
</div>

<!-- Loading Spinner -->
<div id="loading-spinner" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white p-5 rounded-lg shadow-xl flex items-center space-x-3">
        <svg class="animate-spin h-6 w-6 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
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

.table-row-group:hover {
    background-color: #f3f4f6;
}

.week-separator {
    background-color: #fef3c7 !important;
    border-top: 2px solid #f59e0b !important;
    border-bottom: 2px solid #f59e0b !important;
}

.week-separator td {
    font-weight: bold;
    color: #b45309;
}

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
class DailyReportManager {
    constructor() {
        this.init();
    }

    init() {
        this.bindEvents();
        this.setDefaultDates();
    }

    bindEvents() {
        document.getElementById('date_range').addEventListener('change', (e) => {
            this.toggleCustomDates(e.target.value);
        });

        document.getElementById('generateBtn').addEventListener('click', () => {
            this.generateReport();
        });

        document.getElementById('resetBtn').addEventListener('click', () => {
            this.resetForm();
        });
    }

    setDefaultDates() {
        const today = new Date();
        const weekAgo = new Date();
        weekAgo.setDate(weekAgo.getDate() - 7);
        
        const dateFrom = document.getElementById('dateFrom');
        const dateTo = document.getElementById('dateTo');
        
        if (dateFrom) dateFrom.value = weekAgo.toISOString().split('T')[0];
        if (dateTo) dateTo.value = today.toISOString().split('T')[0];
    }

    toggleCustomDates(value) {
        const customDates = document.getElementById('custom-dates');
        if (value === 'custom') {
            customDates.classList.remove('hidden');
        } else {
            customDates.classList.add('hidden');
        }
    }

    async generateReport() {
        if (!this.validateForm()) return;

        document.getElementById('loading-spinner').classList.remove('hidden');

        const formData = new FormData(document.getElementById('dailyReportForm'));
        
        try {
           const response = await fetch('{{ route("daily_reports.generate") }}', {
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
                this.showNotification(result.message || 'Hitilafu imetokea', 'error');
            }
        } catch (error) {
            this.showNotification('Hitilafu ya mtandao', 'error');
        } finally {
            document.getElementById('loading-spinner').classList.add('hidden');
        }
    }

    validateForm() {
        const reportSubType = document.getElementById('report_sub_type').value;
        const groupBy = document.getElementById('group_by').value;
        const dateRange = document.getElementById('date_range').value;
        const errorDiv = document.getElementById('errorMessage');
        const errorText = document.getElementById('errorText');

        errorDiv.classList.add('hidden');

        if (!reportSubType || !groupBy) {
            errorText.textContent = 'Tafadhali chagua aina ya ripoti na kipindi cha kuonyesha';
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
        document.getElementById('report-results').classList.remove('hidden');
        
        document.getElementById('report-title').textContent = data.report_title;
        document.getElementById('report-subtitle').innerHTML = `<i class="fas fa-${this.getIconForReport(data.report_sub_type)} mr-1"></i> ${data.group_by_label}`;
        document.getElementById('report-period').innerHTML = `<i class="fas fa-calendar-alt mr-1"></i> Kipindi: ${data.date_range_label}`;

        // Build table based on report type
        switch (data.report_sub_type) {
            case 'mauzo':
                this.displaySalesTable(data);
                break;
            case 'faida':
                this.displayProfitTable(data);
                break;
            case 'biashara':
                this.displayBusinessTable(data);
                break;
            case 'matumizi':
                this.displayExpensesTable(data);
                break;
        }
    }

    displaySalesTable(data) {
        const headers = `
            <tr class="bg-emerald-50">
                <th class="px-4 py-3 text-left font-semibold text-emerald-800">Kipindi / Tarehe</th>
                <th class="px-4 py-3 text-right font-semibold text-emerald-800">Jumla ya Mauzo</th>
                <th class="px-4 py-3 text-right font-semibold text-emerald-800">Mapato ya Madeni</th>
                <th class="px-4 py-3 text-right font-semibold text-emerald-800">Jumla ya Mapato</th>
                <th class="px-4 py-3 text-right font-semibold text-emerald-800">Faida</th>
                <th class="px-4 py-3 text-center font-semibold text-emerald-800">Idadi ya Mauzo</th>
            </tr>
        `;
        document.getElementById('report-table-header').innerHTML = headers;

        let body = '';
        let previousWeekKey = '';
        
        for (const group of data.grouped_data) {
            // Add week separator if needed
            if (group.week_separator && data.group_by === 'week') {
                body += `
                    <tr class="week-separator bg-amber-50">
                        <td colspan="6" class="px-4 py-2 text-center font-bold text-amber-700">
                            <i class="fas fa-arrow-down mr-2"></i> MWANZO WA WIKI ${group.week_number} <i class="fas fa-arrow-down ml-2"></i>
                        </td>
                    </tr>
                `;
            }
            
            const d = group.data;
            body += `
                <tr class="border-b border-gray-100 hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-800">${group.period_label}</div>
                        <div class="text-xs text-gray-500">${group.display_date}</div>
                    </td>
                    <td class="px-4 py-3 text-right">${this.formatNumber(d.total_sales)}</td>
                    <td class="px-4 py-3 text-right">${this.formatNumber(d.total_repayments)}</td>
                    <td class="px-4 py-3 text-right font-semibold text-emerald-700">${this.formatNumber(d.total_income)}</td>
                    <td class="px-4 py-3 text-right font-semibold">${this.formatNumber(d.total_profit)}</td>
                    <td class="px-4 py-3 text-center">${d.sales_count}</td>
                </tr>
            `;
        }
        document.getElementById('report-table-body').innerHTML = body;

        document.getElementById('grand-total').classList.remove('hidden');
        document.getElementById('grand-total-value').innerHTML = `
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 w-full">
                <div>Jumla ya Mauzo: ${this.formatNumber(data.grand_totals.total_sales || 0)} TZS</div>
                <div>Jumla ya Mapato ya Madeni: ${this.formatNumber(data.grand_totals.total_repayments || 0)} TZS</div>
                <div>Jumla ya Mapato: ${this.formatNumber(data.grand_totals.total_income || 0)} TZS</div>
                <div>Jumla ya Faida: ${this.formatNumber(data.grand_totals.total_profit || 0)} TZS</div>
            </div>
        `;
    }

    displayProfitTable(data) {
        const headers = `
            <tr class="bg-emerald-50">
                <th class="px-4 py-3 text-left font-semibold text-emerald-800">Kipindi / Tarehe</th>
                <th class="px-4 py-3 text-right font-semibold text-emerald-800">Faida ya Mauzo</th>
                <th class="px-4 py-3 text-right font-semibold text-emerald-800">Faida ya Marejesho</th>
                <th class="px-4 py-3 text-right font-semibold text-emerald-800">Jumla ya Faida</th>
                <th class="px-4 py-3 text-right font-semibold text-red-600">Matumizi</th>
                <th class="px-4 py-3 text-right font-semibold text-emerald-800">Faida Halisi</th>
            </tr>
        `;
        document.getElementById('report-table-header').innerHTML = headers;

        let body = '';
        for (const group of data.grouped_data) {
            const d = group.data;
            const netProfitClass = d.net_profit >= 0 ? 'text-emerald-700' : 'text-red-600';
            body += `
                <tr class="border-b border-gray-100 hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-800">${group.period_label}</div>
                        <div class="text-xs text-gray-500">${group.display_date}</div>
                    </td>
                    <td class="px-4 py-3 text-right">${this.formatNumber(d.sales_profit)}</td>
                    <td class="px-4 py-3 text-right">${this.formatNumber(d.repayment_profit)}</td>
                    <td class="px-4 py-3 text-right font-semibold">${this.formatNumber(d.total_profit)}</td>
                    <td class="px-4 py-3 text-right text-red-600">${this.formatNumber(d.expenses)}</td>
                    <td class="px-4 py-3 text-right font-bold ${netProfitClass}">${this.formatNumber(d.net_profit)}</td>
                </tr>
            `;
        }
        document.getElementById('report-table-body').innerHTML = body;

        document.getElementById('grand-total').classList.remove('hidden');
        document.getElementById('grand-total-value').innerHTML = `
            <div class="grid grid-cols-3 gap-4 w-full">
                <div>Jumla ya Faida: ${this.formatNumber(data.grand_totals.total_profit || 0)} TZS</div>
                <div>Jumla ya Matumizi: ${this.formatNumber(data.grand_totals.expenses || 0)} TZS</div>
                <div>Jumla ya Faida Halisi: ${this.formatNumber(data.grand_totals.net_profit || 0)} TZS</div>
            </div>
        `;
    }

    displayBusinessTable(data) {
        const headers = `
            <tr class="bg-emerald-50">
                <th class="px-4 py-3 text-left font-semibold text-emerald-800">Kipindi / Tarehe</th>
                <th class="px-4 py-3 text-right font-semibold text-emerald-800">Mapato ya Mauzo</th>
                <th class="px-4 py-3 text-right font-semibold text-emerald-800">Mapato ya Madeni</th>
                <th class="px-4 py-3 text-right font-semibold text-emerald-800">Jumla ya Mapato</th>
                <th class="px-4 py-3 text-right font-semibold text-emerald-800">Idadi ya Mauzo</th>
                <th class="px-4 py-3 text-right font-semibold text-emerald-800">Faida</th>
                <th class="px-4 py-3 text-right font-semibold text-red-600">Matumizi</th>
                <th class="px-4 py-3 text-right font-semibold text-emerald-800">Faida Halisi</th>
            </tr>
        `;
        document.getElementById('report-table-header').innerHTML = headers;

        let body = '';
        for (const group of data.grouped_data) {
            const d = group.data;
            const netProfitClass = d.net_profit >= 0 ? 'text-emerald-700' : 'text-red-600';
            body += `
                <tr class="border-b border-gray-100 hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-800">${group.period_label}</div>
                        <div class="text-xs text-gray-500">${group.display_date}</div>
                    </td>
                    <td class="px-4 py-3 text-right">${this.formatNumber(d.total_sales)}</td>
                    <td class="px-4 py-3 text-right">${this.formatNumber(d.total_repayments)}</td>
                    <td class="px-4 py-3 text-right font-semibold text-emerald-700">${this.formatNumber(d.total_income)}</td>
                    <td class="px-4 py-3 text-center">${d.sales_count}</td>
                    <td class="px-4 py-3 text-right">${this.formatNumber(d.total_profit)}</td>
                    <td class="px-4 py-3 text-right text-red-600">${this.formatNumber(d.expenses)}</td>
                    <td class="px-4 py-3 text-right font-bold ${netProfitClass}">${this.formatNumber(d.net_profit)}</td>
                </tr>
            `;
        }
        document.getElementById('report-table-body').innerHTML = body;

        document.getElementById('grand-total').classList.remove('hidden');
        document.getElementById('grand-total-value').innerHTML = `
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 w-full">
                <div>Mapato ya Mauzo: ${this.formatNumber(data.grand_totals.total_sales || 0)} TZS</div>
                <div>Mapato ya Madeni: ${this.formatNumber(data.grand_totals.total_repayments || 0)} TZS</div>
                <div>Jumla ya Mapato: ${this.formatNumber(data.grand_totals.total_income || 0)} TZS</div>
                <div>Jumla ya Faida Halisi: ${this.formatNumber(data.grand_totals.net_profit || 0)} TZS</div>
            </div>
        `;
    }

    displayExpensesTable(data) {
        const headers = `
            <tr class="bg-emerald-50">
                <th class="px-4 py-3 text-left font-semibold text-emerald-800">Kipindi / Tarehe</th>
                <th class="px-4 py-3 text-right font-semibold text-emerald-800">Jumla ya Matumizi</th>
                <th class="px-4 py-3 text-center font-semibold text-emerald-800">Idadi ya Matumizi</th>
                <th class="px-4 py-3 text-left font-semibold text-emerald-800">Matumizi kwa Aina</th>
            </tr>
        `;
        document.getElementById('report-table-header').innerHTML = headers;

        let body = '';
        for (const group of data.grouped_data) {
            const d = group.data;
            let categoriesHtml = '';
            for (const [category, amount] of Object.entries(d.expenses_by_category || {})) {
                categoriesHtml += `<span class="inline-block bg-gray-100 rounded px-2 py-1 mr-1 mb-1 text-xs">${category}: ${this.formatNumber(amount)}</span>`;
            }
            if (!categoriesHtml) categoriesHtml = '<span class="text-gray-400 text-xs">Hakuna data</span>';
            
            body += `
                <tr class="border-b border-gray-100 hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-800">${group.period_label}</div>
                        <div class="text-xs text-gray-500">${group.display_date}</div>
                    </td>
                    <td class="px-4 py-3 text-right font-semibold text-red-600">${this.formatNumber(d.total_expenses)}</td>
                    <td class="px-4 py-3 text-center">${d.expenses_count}</td>
                    <td class="px-4 py-3">${categoriesHtml}</td>
                </tr>
            `;
        }
        document.getElementById('report-table-body').innerHTML = body;

        document.getElementById('grand-total').classList.remove('hidden');
        document.getElementById('grand-total-value').textContent = this.formatNumber(data.grand_totals.total_expenses || 0) + ' TZS';
    }

    getIconForReport(type) {
        const icons = {
            'mauzo': 'money-bill-wave',
            'faida': 'chart-line',
            'biashara': 'building',
            'matumizi': 'wallet'
        };
        return icons[type] || 'file-alt';
    }

    formatNumber(value) {
        return new Intl.NumberFormat('sw-TZ', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(value || 0);
    }

    resetForm() {
        document.getElementById('dailyReportForm').reset();
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

function downloadReportPDF() {
    const form = document.getElementById('dailyReportForm');
    form.action = '{{ route("daily_reports.download") }}';
    form.method = 'POST';
    form.target = '_blank';
    form.submit();
    
    setTimeout(() => {
        form.action = '';
        form.method = 'GET';
        form.target = '';
    }, 100);
}

function downloadReportExcel() {
    alert('Feature ya Excel inakuja hivi karibuni');
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    window.dailyReportManager = new DailyReportManager();
});
</script>
@endpush