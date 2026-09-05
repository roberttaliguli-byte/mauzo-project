<!-- Wauzaji (Sales Staff) Report Section -->
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
    <div class="p-4 border-b border-gray-200 bg-amber-soft">
        <h2 class="text-sm font-bold flex items-center text-gray-800">
            <i class="fas fa-users text-amber-600 mr-2"></i>
            <span class="amber-label">Ripoti ya Wauzaji / Watumiaji</span>
        </h2>
        <p class="text-xs text-gray-700 mt-1 font-medium">Tazama ripoti ya mauzo, madeni, orders na faida kwa kila mtumiaji</p>
    </div>
    
    <div class="p-4">
        <!-- Filter Section -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
            <!-- Select User -->
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Chagua Mtumiaji *</label>
                <select id="userSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500">
                    <option value="">-- Chagua Mtumiaji --</option>
                    <optgroup label="Wakuu (Boss)">
                        @foreach($bossUsers ?? [] as $boss)
                            <option value="boss_{{ $boss->id }}" data-type="boss" data-name="{{ $boss->name ?? $boss->username }}" data-id="{{ $boss->id }}">
                                👑 {{ $boss->name ?? $boss->username }} (Boss)
                            </option>
                        @endforeach
                    </optgroup>
                    <optgroup label="Wafanyakazi">
                        @foreach($employeeUsers ?? [] as $employee)
                            <option value="employee_{{ $employee->id }}" data-type="employee" data-name="{{ $employee->jina }}" data-id="{{ $employee->id }}">
                                👤 {{ $employee->jina }} ({{ $employee->role ?? 'Mfanyakazi' }})
                            </option>
                        @endforeach
                    </optgroup>
                </select>
            </div>

            <!-- Time Period -->
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Muda wa Ripoti *</label>
                <select id="reportDateRange" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500">
                    <option value="today">Leo</option>
                    <option value="yesterday">Jana</option>
                    <option value="week">Wiki hii</option>
                    <option value="month" selected>Mwezi huu</option>
                    <option value="year">Mwaka huu</option>
                    <option value="custom">Tarehe Maalum</option>
                </select>
            </div>

            <!-- Custom Date Range -->
            <div id="customDateRange" class="hidden col-span-2">
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Kuanzia Tarehe</label>
                        <input type="date" id="dateFrom" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Mpaka Tarehe</label>
                        <input type="date" id="dateTo" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-amber-500">
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap gap-2 mb-4">
            <button onclick="generateUserReport()" id="generateUserReportBtn" class="px-4 py-2 bg-amber-600 text-white rounded-lg text-sm font-semibold hover:bg-amber-700 transition-all duration-200">
                <i class="fas fa-chart-line mr-1"></i> Tengeneza Ripoti
            </button>
            <button onclick="resetUserReport()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-300 transition-all duration-200">
                <i class="fas fa-redo mr-1"></i> Weka Upya
            </button>
            <button onclick="exportUserReportPDF()" id="exportPDFBtn" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-semibold hover:bg-red-700 transition-all duration-200 hidden">
                <i class="fas fa-file-pdf mr-1"></i> Pakua PDF
            </button>
        </div>

        <!-- Error Message -->
        <div id="userReportError" class="hidden p-3 mb-4 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
            <i class="fas fa-exclamation-circle mr-1"></i>
            <span id="userReportErrorText"></span>
        </div>

        <!-- Report Results -->
        <div id="userReportResults" class="hidden space-y-4">
            <!-- User Info Card -->
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 p-4 rounded-lg border border-amber-200">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-amber-600 rounded-full flex items-center justify-center text-white text-xl font-bold" id="userAvatar">
                        👤
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800 text-lg" id="userReportName">-</h3>
                        <p class="text-xs text-gray-600" id="userReportType">-</p>
                        <p class="text-xs text-gray-500 mt-1" id="userReportPeriod">-</p>
                    </div>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3" id="userSummaryCards">
                <!-- Will be populated by JavaScript -->
            </div>

            <!-- Tabs for different report types -->
            <div class="flex border-b border-gray-200">
                <button onclick="switchUserReportTab('sales', this)" class="user-report-tab px-4 py-2 text-sm font-medium text-amber-700 border-b-2 border-amber-600">
                    <i class="fas fa-shopping-cart mr-1"></i> Mauzo
                </button>
                <button onclick="switchUserReportTab('debts', this)" class="user-report-tab px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">
                    <i class="fas fa-hand-holding-usd mr-1"></i> Madeni
                </button>
                <button onclick="switchUserReportTab('orders', this)" class="user-report-tab px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">
                    <i class="fas fa-clipboard-list mr-1"></i> Orders
                </button>
            </div>

            <!-- Sales Table -->
            <div id="userSalesTab" class="user-report-tab-content bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                    <h4 class="font-semibold text-gray-800 text-sm">
                        <i class="fas fa-shopping-cart text-emerald-600 mr-2"></i> Mauzo Yaliyofanywa
                    </h4>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-3 py-2 text-left">#</th>
                                <th class="px-3 py-2 text-left">Tarehe</th>
                                <th class="px-3 py-2 text-left">Bidhaa</th>
                                <th class="px-3 py-2 text-center">Idadi</th>
                                <th class="px-3 py-2 text-right">Bei (Tsh)</th>
                                <th class="px-3 py-2 text-right">Punguzo</th>
                                <th class="px-3 py-2 text-right">Jumla (Tsh)</th>
                                <th class="px-3 py-2 text-left">Njia ya Malipo</th>
                            </tr>
                        </thead>
                        <tbody id="userSalesTableBody">
                            <tr><td colspan="8" class="text-center py-4 text-gray-500">Chagua mtumiaji na ubonyeze "Tengeneza Ripoti"</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Debts Table -->
            <div id="userDebtsTab" class="user-report-tab-content hidden bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                    <h4 class="font-semibold text-gray-800 text-sm">
                        <i class="fas fa-hand-holding-usd text-red-600 mr-2"></i> Madeni / Mikopo
                    </h4>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-3 py-2 text-left">#</th>
                                <th class="px-3 py-2 text-left">Tarehe</th>
                                <th class="px-3 py-2 text-left">Mkopaji</th>
                                <th class="px-3 py-2 text-left">Bidhaa</th>
                                <th class="px-3 py-2 text-center">Idadi</th>
                                <th class="px-3 py-2 text-right">Jumla (Tsh)</th>
                                <th class="px-3 py-2 text-right">Baki (Tsh)</th>
                                <th class="px-3 py-2 text-left">Hali</th>
                            </tr>
                        </thead>
                        <tbody id="userDebtsTableBody">
                            <tr><td colspan="8" class="text-center py-4 text-gray-500">Chagua mtumiaji na ubonyeze "Tengeneza Ripoti"</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Orders Table -->
            <div id="userOrdersTab" class="user-report-tab-content hidden bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                    <h4 class="font-semibold text-gray-800 text-sm">
                        <i class="fas fa-clipboard-list text-blue-600 mr-2"></i> Orders
                    </h4>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-3 py-2 text-left">#</th>
                                <th class="px-3 py-2 text-left">Tarehe</th>
                                <th class="px-3 py-2 text-left">Mteja</th>
                                <th class="px-3 py-2 text-left">Bidhaa</th>
                                <th class="px-3 py-2 text-right">Jumla (Tsh)</th>
                                <th class="px-3 py-2 text-left">Hali</th>
                            </tr>
                        </thead>
                        <tbody id="userOrdersTableBody">
                            <tr><td colspan="6" class="text-center py-4 text-gray-500">Chagua mtumiaji na ubonyeze "Tengeneza Ripoti"</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// User Report Manager
let currentUserReportData = null;
let currentUserReportTab = 'sales';

// Initialize date inputs
function initUserReportDates() {
    const today = new Date();
    const firstDayMonth = new Date(today.getFullYear(), today.getMonth(), 1);
    
    document.getElementById('dateFrom').value = firstDayMonth.toISOString().split('T')[0];
    document.getElementById('dateTo').value = today.toISOString().split('T')[0];
}

// Toggle custom date range visibility
function toggleCustomDateRange() {
    const dateRange = document.getElementById('reportDateRange').value;
    const customDiv = document.getElementById('customDateRange');
    if (dateRange === 'custom') {
        customDiv.classList.remove('hidden');
    } else {
        customDiv.classList.add('hidden');
    }
}

// Get date range based on selection
function getDateRange() {
    const dateRange = document.getElementById('reportDateRange').value;
    const today = new Date();
    let from, to;
    
    today.setHours(23, 59, 59, 999);
    
    switch(dateRange) {
        case 'today':
            from = new Date(today);
            from.setHours(0, 0, 0, 0);
            to = new Date(today);
            to.setHours(23, 59, 59, 999);
            break;
        case 'yesterday':
            from = new Date(today);
            from.setDate(from.getDate() - 1);
            from.setHours(0, 0, 0, 0);
            to = new Date(from);
            to.setHours(23, 59, 59, 999);
            break;
        case 'week':
            from = new Date(today);
            from.setDate(from.getDate() - from.getDay());
            from.setHours(0, 0, 0, 0);
            to = new Date(today);
            to.setHours(23, 59, 59, 999);
            break;
        case 'month':
            from = new Date(today.getFullYear(), today.getMonth(), 1);
            from.setHours(0, 0, 0, 0);
            to = new Date(today);
            to.setHours(23, 59, 59, 999);
            break;
        case 'year':
            from = new Date(today.getFullYear(), 0, 1);
            from.setHours(0, 0, 0, 0);
            to = new Date(today);
            to.setHours(23, 59, 59, 999);
            break;
        case 'custom':
            from = new Date(document.getElementById('dateFrom').value);
            from.setHours(0, 0, 0, 0);
            to = new Date(document.getElementById('dateTo').value);
            to.setHours(23, 59, 59, 999);
            break;
        default:
            from = new Date(today.getFullYear(), today.getMonth(), 1);
            from.setHours(0, 0, 0, 0);
            to = new Date(today);
            to.setHours(23, 59, 59, 999);
    }
    
    return { from, to };
}

// Format date for display
function formatDate(date) {
    return date.toLocaleDateString('sw-TZ', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
}

// Format datetime
function formatDateTime(date) {
    return date.toLocaleDateString('sw-TZ', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Format currency
function formatCurrency(value) {
    return new Intl.NumberFormat('sw-TZ', {
        style: 'currency',
        currency: 'TZS',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(value || 0);
}

// Format number with 2 decimals
function formatNumber(value) {
    return new Intl.NumberFormat('sw-TZ', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(value || 0);
}

// Show notification
function showUserReportNotification(message, type = 'info') {
    const container = document.getElementById('notification-container');
    if (!container) return;
    
    const colors = {
        success: 'bg-emerald-50 border-emerald-200 text-emerald-800',
        error: 'bg-red-50 border-red-200 text-red-800',
        warning: 'bg-amber-50 border-amber-200 text-amber-800',
        info: 'bg-blue-50 border-blue-200 text-blue-800'
    };

    const notification = document.createElement('div');
    notification.className = `rounded border px-4 py-3 text-sm font-medium mb-2 ${colors[type]} shadow-sm`;
    notification.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'} mr-2"></i>${message}`;
    container.appendChild(notification);

    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateY(-10px)';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Switch tabs
function switchUserReportTab(tab, btn) {
    currentUserReportTab = tab;
    
    // Update tab buttons
    document.querySelectorAll('.user-report-tab').forEach(el => {
        el.className = 'user-report-tab px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700';
    });
    if (btn) {
        btn.className = 'user-report-tab px-4 py-2 text-sm font-medium text-amber-700 border-b-2 border-amber-600';
    }
    
    // Hide all tab contents
    document.querySelectorAll('.user-report-tab-content').forEach(el => {
        el.classList.add('hidden');
    });
    
    // Show selected tab
    const tabMap = {
        'sales': 'userSalesTab',
        'debts': 'userDebtsTab',
        'orders': 'userOrdersTab'
    };
    document.getElementById(tabMap[tab]).classList.remove('hidden');
}

// Generate user report
async function generateUserReport() {
    const userSelect = document.getElementById('userSelect');
    const selectedOption = userSelect.options[userSelect.selectedIndex];
    const userId = selectedOption.value;
    
    if (!userId) {
        document.getElementById('userReportErrorText').innerText = 'Tafadhali chagua mtumiaji';
        document.getElementById('userReportError').classList.remove('hidden');
        return;
    }
    
    // Hide error
    document.getElementById('userReportError').classList.add('hidden');
    
    // Get date range
    const { from, to } = getDateRange();
    const userType = selectedOption.dataset.type;
    const userName = selectedOption.dataset.name;
    
    // Show loading on button
    const btn = document.getElementById('generateUserReportBtn');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Inatengeneza...';
    btn.disabled = true;
    
    try {
        const response = await fetch('{{ route("uchambuzi.user.report") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                user_id: userId.split('_')[1],
                user_type: userType,
                from: from.toISOString(),
                to: to.toISOString()
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            currentUserReportData = result.data;
            displayUserReport(result.data, userName, userType, from, to);
            showUserReportNotification('Ripoti imetengenezwa kwa mafanikio', 'success');
            
            // Show export button
            document.getElementById('exportPDFBtn').classList.remove('hidden');
        } else {
            showUserReportNotification(result.message || 'Hitilafu imetokea', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showUserReportNotification('Hitilafu ya mtandao: ' + error.message, 'error');
    } finally {
        btn.innerHTML = originalText;
        btn.disabled = false;
    }
}

function displayUserReport(data, userName, userType, from, to) {
    const resultsDiv = document.getElementById('userReportResults');
    resultsDiv.classList.remove('hidden');
    
    // Update user info
    document.getElementById('userReportName').textContent = data.user_name || userName;
    document.getElementById('userReportType').textContent = userType === 'boss' ? 'Mmiliki / Boss' : 'Mfanyakazi';
    document.getElementById('userReportPeriod').innerHTML = `<i class="fas fa-calendar-alt mr-1"></i> Kipindi: ${formatDate(from)} - ${formatDate(to)}`;
    
    // Set avatar
    const avatar = document.getElementById('userAvatar');
    avatar.textContent = (data.user_name || userName).charAt(0).toUpperCase();
    
    // Summary Cards - including debt and order stats
    const summaryHtml = `
        <div class="bg-white p-3 rounded-lg border border-emerald-200 shadow-sm">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Jumla ya Mauzo</p>
                    <p class="text-xl font-bold text-emerald-700">${formatCurrency(data.total_sales_amount || 0)}</p>
                </div>
                <i class="fas fa-shopping-cart text-emerald-500 text-lg"></i>
            </div>
            <div class="mt-1 text-xs text-gray-500">Mauzo: ${data.total_sales_count || 0}</div>
        </div>
        <div class="bg-white p-3 rounded-lg border border-blue-200 shadow-sm">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Bidhaa Zilizouzwa</p>
                    <p class="text-xl font-bold text-blue-700">${formatNumber(data.total_items_sold || 0)}</p>
                </div>
                <i class="fas fa-boxes text-blue-500 text-lg"></i>
            </div>
        </div>
        <div class="bg-white p-3 rounded-lg border border-purple-200 shadow-sm">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Faida</p>
                    <p class="text-xl font-bold text-purple-700">${formatCurrency(data.total_profit || 0)}</p>
                </div>
                <i class="fas fa-chart-line text-purple-500 text-lg"></i>
            </div>
        </div>
        <div class="bg-white p-3 rounded-lg border border-amber-200 shadow-sm">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Wastani wa Mauzo</p>
                    <p class="text-xl font-bold text-amber-700">${formatCurrency(data.average_sale_value || 0)}</p>
                </div>
                <i class="fas fa-calculator text-amber-500 text-lg"></i>
            </div>
        </div>
        <div class="bg-white p-3 rounded-lg border border-red-200 shadow-sm">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Jumla ya Madeni</p>
                    <p class="text-xl font-bold text-red-700">${formatCurrency(data.total_debts || 0)}</p>
                </div>
                <i class="fas fa-hand-holding-usd text-red-500 text-lg"></i>
            </div>
            <div class="mt-1 text-xs text-gray-500">Madeni: ${data.total_debts_count || 0}</div>
        </div>
    `;
    document.getElementById('userSummaryCards').innerHTML = summaryHtml;
    
    // Sales Table - Filter by date range
    let salesHtml = '';
    const salesData = data.sales || [];
    if (salesData.length > 0) {
        salesData.forEach((sale, index) => {
            const saleDate = new Date(sale.created_at);
            salesHtml += `
                <tr class="hover:bg-gray-50 border-b">
                    <td class="px-3 py-2">${index + 1}</td>
                    <td class="px-3 py-2">${formatDateTime(saleDate)}</td>
                    <td class="px-3 py-2">${sale.bidhaa?.jina || 'N/A'}</td>
                    <td class="px-3 py-2 text-center">${formatNumber(sale.idadi)}</td>
                    <td class="px-3 py-2 text-right">${formatCurrency(sale.bei)}</td>
                    <td class="px-3 py-2 text-right text-red-600">${formatCurrency(sale.discount_amount || 0)}</td>
                    <td class="px-3 py-2 text-right font-semibold">${formatCurrency(sale.jumla)}</td>
                    <td class="px-3 py-2">${sale.lipa_kwa === 'cash' ? '💰 Cash' : (sale.lipa_kwa === 'lipa_namba' ? '📱 Lipa Namba' : '🏦 Benki')}</td>
                </tr>
            `;
        });
    } else {
        salesHtml = '<tr><td colspan="8" class="text-center py-4 text-gray-500">Hakuna mauzo katika kipindi hiki</td></tr>';
    }
    document.getElementById('userSalesTableBody').innerHTML = salesHtml;
    
    // Debts Table
    let debtsHtml = '';
    const debtsData = data.debts || [];
    if (debtsData.length > 0) {
        debtsData.forEach((debt, index) => {
            const debtDate = new Date(debt.created_at);
            const statusClass = debt.baki > 0 ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700';
            const statusText = debt.baki > 0 ? 'Inasubiri' : 'Imelipwa';
            debtsHtml += `
                <tr class="hover:bg-gray-50 border-b">
                    <td class="px-3 py-2">${index + 1}</td>
                    <td class="px-3 py-2">${formatDateTime(debtDate)}</td>
                    <td class="px-3 py-2">${debt.jina_mkopaji || 'N/A'}</td>
                    <td class="px-3 py-2">${debt.bidhaa?.jina || 'N/A'}</td>
                    <td class="px-3 py-2 text-center">${formatNumber(debt.idadi)}</td>
                    <td class="px-3 py-2 text-right">${formatCurrency(debt.jumla)}</td>
                    <td class="px-3 py-2 text-right font-semibold">${formatCurrency(debt.baki)}</td>
                    <td class="px-3 py-2"><span class="px-2 py-0.5 rounded text-xs font-medium ${statusClass}">${statusText}</span></td>
                </tr>
            `;
        });
    } else {
        debtsHtml = '<tr><td colspan="8" class="text-center py-4 text-gray-500">Hakuna madeni katika kipindi hiki</td></tr>';
    }
    document.getElementById('userDebtsTableBody').innerHTML = debtsHtml;
    
    // Orders Table
    let ordersHtml = '';
    const ordersData = data.orders || [];
    if (ordersData.length > 0) {
        ordersData.forEach((order, index) => {
            const orderDate = new Date(order.created_at);
            const statusColors = {
                'saved': 'bg-amber-100 text-amber-700',
                'confirmed': 'bg-blue-100 text-blue-700',
                'paid': 'bg-green-100 text-green-700',
                'cancelled': 'bg-red-100 text-red-700'
            };
            const items = order.items || [];
            const itemNames = items.slice(0, 2).map(i => i.jina || i.name).join(', ');
            const more = items.length > 2 ? ` +${items.length - 2}` : '';
            
            ordersHtml += `
                <tr class="hover:bg-gray-50 border-b">
                    <td class="px-3 py-2">${index + 1}</td>
                    <td class="px-3 py-2">${formatDateTime(orderDate)}</td>
                    <td class="px-3 py-2">${order.customer_name || 'Walk-in'}</td>
                    <td class="px-3 py-2">${itemNames}${more}</td>
                    <td class="px-3 py-2 text-right font-semibold">${formatCurrency(order.total)}</td>
                    <td class="px-3 py-2"><span class="px-2 py-0.5 rounded text-xs font-medium ${statusColors[order.status] || 'bg-gray-100 text-gray-700'}">${order.status || 'N/A'}</span></td>
                </tr>
            `;
        });
    } else {
        ordersHtml = '<tr><td colspan="6" class="text-center py-4 text-gray-500">Hakuna orders katika kipindi hiki</td></tr>';
    }
    document.getElementById('userOrdersTableBody').innerHTML = ordersHtml;
    
    // Switch to sales tab by default
    switchUserReportTab('sales', document.querySelector('.user-report-tab'));
}

// Reset user report form
function resetUserReport() {
    document.getElementById('userSelect').value = '';
    document.getElementById('reportDateRange').value = 'month';
    initUserReportDates();
    toggleCustomDateRange();
    document.getElementById('userReportResults').classList.add('hidden');
    document.getElementById('userReportError').classList.add('hidden');
    document.getElementById('exportPDFBtn').classList.add('hidden');
    currentUserReportData = null;
    showUserReportNotification('Fomu imeanzishwa upya', 'info');
}

// Export PDF
function exportUserReportPDF() {
    if (!currentUserReportData) {
        showUserReportNotification('Hakuna data ya kuchapisha. Tengeneza ripoti kwanza.', 'warning');
        return;
    }
    
    const userSelect = document.getElementById('userSelect');
    const selectedOption = userSelect.options[userSelect.selectedIndex];
    const userName = selectedOption.dataset.name || 'Mtumiaji';
    const { from, to } = getDateRange();
    
    // Open print window
    const printWindow = window.open('', '_blank', 'width=800,height=600');
    if (!printWindow) {
        showUserReportNotification('Tafadhali ruhusu pop-ups kwa chapisho', 'error');
        return;
    }
    
    const data = currentUserReportData;
    
    // Build sales table rows
    let salesRows = '';
    (data.sales || []).forEach((sale, index) => {
        salesRows += `
            <tr>
                <td style="border: 1px solid #ddd; padding: 6px;">${index + 1}</td>
                <td style="border: 1px solid #ddd; padding: 6px;">${new Date(sale.created_at).toLocaleDateString('sw-TZ')}</td>
                <td style="border: 1px solid #ddd; padding: 6px;">${sale.bidhaa?.jina || 'N/A'}</td>
                <td style="border: 1px solid #ddd; padding: 6px; text-align: center;">${sale.idadi}</td>
                <td style="border: 1px solid #ddd; padding: 6px; text-align: right;">${formatCurrency(sale.bei)}</td>
                <td style="border: 1px solid #ddd; padding: 6px; text-align: right;">${formatCurrency(sale.jumla)}</td>
            </tr>
        `;
    });
    
    // Build debts table rows
    let debtsRows = '';
    (data.debts || []).forEach((debt, index) => {
        debtsRows += `
            <tr>
                <td style="border: 1px solid #ddd; padding: 6px;">${index + 1}</td>
                <td style="border: 1px solid #ddd; padding: 6px;">${new Date(debt.created_at).toLocaleDateString('sw-TZ')}</td>
                <td style="border: 1px solid #ddd; padding: 6px;">${debt.jina_mkopaji || 'N/A'}</td>
                <td style="border: 1px solid #ddd; padding: 6px; text-align: right;">${formatCurrency(debt.jumla)}</td>
                <td style="border: 1px solid #ddd; padding: 6px; text-align: right;">${formatCurrency(debt.baki)}</td>
                <td style="border: 1px solid #ddd; padding: 6px;">${debt.baki > 0 ? 'Inasubiri' : 'Imelipwa'}</td>
            </tr>
        `;
    });
    
    // Build orders table rows
    let ordersRows = '';
    (data.orders || []).forEach((order, index) => {
        const items = order.items || [];
        const itemNames = items.slice(0, 2).map(i => i.jina || i.name).join(', ');
        const more = items.length > 2 ? ` +${items.length - 2}` : '';
        ordersRows += `
            <tr>
                <td style="border: 1px solid #ddd; padding: 6px;">${index + 1}</td>
                <td style="border: 1px solid #ddd; padding: 6px;">${new Date(order.created_at).toLocaleDateString('sw-TZ')}</td>
                <td style="border: 1px solid #ddd; padding: 6px;">${order.customer_name || 'Walk-in'}</td>
                <td style="border: 1px solid #ddd; padding: 6px;">${itemNames}${more}</td>
                <td style="border: 1px solid #ddd; padding: 6px; text-align: right;">${formatCurrency(order.total)}</td>
                <td style="border: 1px solid #ddd; padding: 6px;">${order.status || 'N/A'}</td>
            </tr>
        `;
    });
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Ripoti ya ${userName} - ${formatDate(from)} hadi ${formatDate(to)}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #f59e0b; padding-bottom: 10px; }
                .header h1 { margin: 0; color: #1a1a1a; font-size: 20px; }
                .header p { margin: 5px 0 0 0; color: #6b7280; font-size: 12px; }
                table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 11px; }
                th { background-color: #f3f4f6; font-weight: bold; border: 1px solid #ddd; padding: 6px; text-align: left; }
                td { border: 1px solid #ddd; padding: 6px; }
                .section-title { background-color: #f59e0b; color: white; padding: 8px; margin-top: 20px; font-weight: bold; font-size: 13px; }
                .summary { display: flex; gap: 10px; margin: 10px 0; flex-wrap: wrap; }
                .summary-card { background: #f9fafb; border: 1px solid #e5e7eb; padding: 10px; border-radius: 6px; flex: 1; min-width: 120px; }
                .summary-card .label { font-size: 10px; color: #6b7280; }
                .summary-card .value { font-size: 14px; font-weight: bold; }
                .footer { text-align: center; margin-top: 20px; font-size: 10px; color: #6b7280; border-top: 1px solid #e5e7eb; padding-top: 10px; }
                @media print {
                    body { margin: 10px; }
                    .no-print { display: none; }
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>RIPOTI YA ${userName.toUpperCase()}</h1>
                <p>Kipindi: ${formatDate(from)} hadi ${formatDate(to)}</p>
                <p>Tarehe ya Chapisho: ${new Date().toLocaleDateString('sw-TZ')}</p>
            </div>
            
            <div class="summary">
                <div class="summary-card">
                    <div class="label">Jumla ya Mauzo</div>
                    <div class="value">${formatCurrency(data.total_sales_amount || 0)}</div>
                </div>
                <div class="summary-card">
                    <div class="label">Bidhaa Zilizouzwa</div>
                    <div class="value">${formatNumber(data.total_items_sold || 0)}</div>
                </div>
                <div class="summary-card">
                    <div class="label">Faida</div>
                    <div class="value">${formatCurrency(data.total_profit || 0)}</div>
                </div>
                <div class="summary-card">
                    <div class="label">Jumla ya Madeni</div>
                    <div class="value">${formatCurrency(data.total_debts || 0)}</div>
                </div>
            </div>
            
            <div class="section-title">📋 MAUZO (${(data.sales || []).length})</div>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tarehe</th>
                        <th>Bidhaa</th>
                        <th>Idadi</th>
                        <th>Bei</th>
                        <th>Jumla</th>
                    </tr>
                </thead>
                <tbody>
                    ${salesRows || '<tr><td colspan="6" style="text-align:center;">Hakuna mauzo</td></tr>'}
                </tbody>
            </table>
            
            <div class="section-title">💳 MADENI (${(data.debts || []).length})</div>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tarehe</th>
                        <th>Mkopaji</th>
                        <th>Jumla</th>
                        <th>Baki</th>
                        <th>Hali</th>
                    </tr>
                </thead>
                <tbody>
                    ${debtsRows || '<tr><td colspan="6" style="text-align:center;">Hakuna madeni</td></tr>'}
                </tbody>
            </table>
            
            <div class="section-title">📋 ORDERS (${(data.orders || []).length})</div>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tarehe</th>
                        <th>Mteja</th>
                        <th>Bidhaa</th>
                        <th>Jumla</th>
                        <th>Hali</th>
                    </tr>
                </thead>
                <tbody>
                    ${ordersRows || '<tr><td colspan="6" style="text-align:center;">Hakuna orders</td></tr>'}
                </tbody>
            </table>
            
            <div class="footer">
                Ripoti hii imetengenezwa na Mfumo wa Mauzo Sheet AI | ${new Date().toLocaleDateString('sw-TZ')}
            </div>
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    initUserReportDates();
    document.getElementById('reportDateRange').addEventListener('change', toggleCustomDateRange);
    
    // Enter key to generate report
    document.getElementById('userSelect').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            generateUserReport();
        }
    });
});

// Add CSS if not present
if (!document.querySelector('#userReportStyles')) {
    const style = document.createElement('style');
    style.id = 'userReportStyles';
    style.textContent = `
        .user-report-tab { transition: all 0.2s ease; cursor: pointer; }
        .user-report-tab:hover { color: #374151; }
    `;
    document.head.appendChild(style);
}
</script>