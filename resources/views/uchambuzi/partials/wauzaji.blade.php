<!-- Wauzaji (Sales Staff) Report Section -->
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
    <div class="p-4 border-b border-gray-200 bg-amber-soft">
        <h2 class="text-sm font-bold flex items-center text-gray-800">
            <i class="fas fa-users text-amber-600 mr-2"></i>
            <span class="amber-label">Ripoti ya Wauzaji / Watumiaji</span>
        </h2>
        <p class="text-xs text-gray-700 mt-1 font-medium">Tazama ripoti ya mauzo, faida na shughuli kwa kila mtumiaji</p>
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
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3" id="userSummaryCards">
                <!-- Will be populated by JavaScript -->
            </div>

            <!-- Sales Table -->
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
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

            <!-- Activity Log Table -->
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                    <h4 class="font-semibold text-gray-800 text-sm">
                        <i class="fas fa-history text-amber-600 mr-2"></i> Historia ya Shughuli
                    </h4>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-3 py-2 text-left">#</th>
                                <th class="px-3 py-2 text-left">Tarehe</th>
                                <th class="px-3 py-2 text-left">Shughuli</th>
                                <th class="px-3 py-2 text-right">Kiasi (Tsh)</th>
                            </tr>
                        </thead>
                        <tbody id="userActivitiesTableBody">
                            <tr><td colspan="4" class="text-center py-4 text-gray-500">Chagua mtumiaji na ubonyeze "Tengeneza Ripoti"</td></tr>
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

// Initialize date inputs
function initUserReportDates() {
    const today = new Date();
    const weekAgo = new Date();
    weekAgo.setDate(weekAgo.getDate() - 7);
    
    document.getElementById('dateFrom').value = weekAgo.toISOString().split('T')[0];
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
    
    switch(dateRange) {
        case 'today':
            from = new Date(today);
            to = new Date(today);
            break;
        case 'yesterday':
            from = new Date(today);
            from.setDate(from.getDate() - 1);
            to = new Date(from);
            break;
        case 'week':
            from = new Date(today);
            from.setDate(from.getDate() - from.getDay());
            to = new Date(today);
            break;
        case 'month':
            from = new Date(today.getFullYear(), today.getMonth(), 1);
            to = new Date(today);
            break;
        case 'year':
            from = new Date(today.getFullYear(), 0, 1);
            to = new Date(today);
            break;
        case 'custom':
            from = new Date(document.getElementById('dateFrom').value);
            to = new Date(document.getElementById('dateTo').value);
            break;
        default:
            from = new Date(today.getFullYear(), today.getMonth(), 1);
            to = new Date(today);
    }
    
    // Set time to start/end of day
    from.setHours(0, 0, 0, 0);
    to.setHours(23, 59, 59, 999);
    
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
    notification.className = `rounded border px-4 py-3 text-sm font-medium mb-2 ${colors[type]} shadow-sm animate-fade-in`;
    notification.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'} mr-2"></i>${message}`;
    container.appendChild(notification);

    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateY(-10px)';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
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
    
    // Show loading spinner
    const spinner = document.getElementById('loading-spinner');
    if (spinner) spinner.classList.remove('hidden');
    
    // Get date range
    const { from, to } = getDateRange();
    const userType = selectedOption.dataset.type;
    const userName = selectedOption.dataset.name;
    
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
            displayUserReport(result.data, userName, userType, from, to);
            showUserReportNotification('Ripoti imetengenezwa kwa mafanikio', 'success');
        } else {
            showUserReportNotification(result.message || 'Hitilafu imetokea', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showUserReportNotification('Hitilafu ya mtandao', 'error');
    } finally {
        if (spinner) spinner.classList.add('hidden');
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
    
    // Summary Cards - Make sure values are displayed correctly
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
    `;
    document.getElementById('userSummaryCards').innerHTML = summaryHtml;
    
    // Sales Table
    let salesHtml = '';
    if (data.sales && data.sales.length > 0) {
        data.sales.forEach((sale, index) => {
            salesHtml += `
                <tr class="hover:bg-gray-50 border-b">
                    <td class="px-3 py-2">${index + 1}</td>
                    <td class="px-3 py-2">${new Date(sale.created_at).toLocaleDateString('sw-TZ')} ${new Date(sale.created_at).toLocaleTimeString('sw-TZ')}</td>
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
    
    // Activities Table
    let activitiesHtml = '';
    if (data.activities && data.activities.length > 0) {
        data.activities.forEach((activity, index) => {
            activitiesHtml += `
                <tr class="hover:bg-gray-50 border-b">
                    <td class="px-3 py-2">${index + 1}</td>
                    <td class="px-3 py-2">${new Date(activity.created_at).toLocaleDateString('sw-TZ')} ${new Date(activity.created_at).toLocaleTimeString('sw-TZ')}</td>
                    <td class="px-3 py-2">${activity.description}</td>
                    <td class="px-3 py-2 text-right">${activity.amount ? formatCurrency(activity.amount) : '-'}</td>
                </tr>
            `;
        });
    } else {
        activitiesHtml = '<tr><td colspan="4" class="text-center py-4 text-gray-500">Hakuna shughuli katika kipindi hiki</td></tr>';
    }
    document.getElementById('userActivitiesTableBody').innerHTML = activitiesHtml;
}

// Reset user report form
function resetUserReport() {
    document.getElementById('userSelect').value = '';
    document.getElementById('reportDateRange').value = 'month';
    initUserReportDates();
    toggleCustomDateRange();
    document.getElementById('userReportResults').classList.add('hidden');
    document.getElementById('userReportError').classList.add('hidden');
    showUserReportNotification('Fomu imeanzishwa upya', 'info');
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    initUserReportDates();
    document.getElementById('reportDateRange').addEventListener('change', toggleCustomDateRange);
});

// Add CSS animation if not present
if (!document.querySelector('#userReportStyles')) {
    const style = document.createElement('style');
    style.id = 'userReportStyles';
    style.textContent = `
        .animate-fade-in {
            animation: fadeIn 0.3s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    `;
    document.head.appendChild(style);
}
</script>