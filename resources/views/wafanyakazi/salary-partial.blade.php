<!-- Salary Management Partial -->
<div id="salary-tab-content" class="tab-content hidden">
    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
            <h2 class="text-sm font-semibold text-gray-800">Usimamizi wa Mishahara</h2>
            <div class="flex items-center gap-2 text-xs text-gray-500">
                <span class="inline-flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-green-500"></span> Imeidhinishwa
                </span>
                <span class="inline-flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-yellow-500"></span> Inasubiri
                </span>
                <span class="inline-flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-red-500"></span> Imekataliwa
                </span>
            </div>
        </div>

        <!-- Employee Selection -->
        <div class="mb-4">
            <label class="block text-xs font-medium text-gray-700 mb-1">Chagua Mfanyakazi</label>
            <select id="salary-employee-select" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                <option value="">-- Chagua Mfanyakazi --</option>
                @foreach($wafanyakazi as $emp)
                    <option value="{{ $emp->id }}" 
                            data-salary="{{ $emp->salary ?? 0 }}"
                            data-uwezo="{{ $emp->uwezo }}"
                            data-counter="{{ $emp->allow_counter_access ? 'Yes' : 'No' }}"
                            data-getini="{{ $emp->getini }}">
                        {{ $emp->jina }} - {{ isset($emp->salary) ? number_format($emp->salary, 0) : 0 }} TZS
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Tabs inside salary -->
        <div class="flex border-b border-gray-200 mb-4">
            <button onclick="switchSalaryTab('details', this)" id="salary-tab-details-btn" class="salary-tab-btn px-4 py-2 text-sm font-medium text-emerald-700 border-b-2 border-emerald-600">
                <i class="fas fa-user mr-1"></i> Maelezo
            </button>
            <button onclick="switchSalaryTab('orders', this)" id="salary-tab-orders-btn" class="salary-tab-btn px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">
                <i class="fas fa-clipboard-list mr-1"></i> Orders
            </button>
        </div>

        <!-- Tab 1: Employee Details -->
        <div id="salary-details-tab" class="salary-tab-content">
            <div id="salary-employee-details" class="hidden">
                <div class="bg-gray-50 p-3 rounded-lg border border-gray-200 mb-4">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div>
                            <h3 id="salary-employee-name" class="font-medium text-gray-900"></h3>
                            <p class="text-xs text-gray-500" id="salary-employee-info"></p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <button onclick="toggleCounterAccess()" 
                                    class="px-3 py-1.5 text-xs font-medium rounded transition bg-amber-500 hover:bg-amber-600 text-white" 
                                    id="counter-toggle-btn">
                                <i class="fas fa-exchange-alt mr-1"></i> Badili Counter
                            </button>
                            <button onclick="showAddSalaryModal()" 
                                    class="px-3 py-1.5 bg-emerald-600 text-white rounded hover:bg-emerald-700 text-xs font-medium transition">
                                <i class="fas fa-plus mr-1"></i> Ongeza Mshahara
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Salary Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
                    <div class="bg-green-50 p-3 rounded-lg border border-green-200">
                        <p class="text-xs text-gray-600">Mshahara</p>
                        <p class="text-lg font-bold text-green-700" id="total-salary-display">0 TZS</p>
                    </div>
                    <div class="bg-red-50 p-3 rounded-lg border border-red-200">
                        <p class="text-xs text-gray-600">Makato</p>
                        <p class="text-lg font-bold text-red-700" id="total-deductions-display">0 TZS</p>
                    </div>
                    <div class="bg-blue-50 p-3 rounded-lg border border-blue-200">
                        <p class="text-xs text-gray-600">Halisi</p>
                        <p class="text-lg font-bold text-blue-700" id="net-salary-display">0 TZS</p>
                    </div>
                    <div class="bg-purple-50 p-3 rounded-lg border border-purple-200">
                        <p class="text-xs text-gray-600">Orders</p>
                        <p class="text-lg font-bold text-purple-700" id="order-count-display">0</p>
                    </div>
                </div>

                <!-- Salary History -->
                <div class="mb-4">
                    <h4 class="text-sm font-semibold text-gray-800 mb-2">Historia ya Mishahara</h4>
                    <div class="overflow-x-auto rounded-lg border border-gray-200">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Tarehe</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Kiasi</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Muda</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Maelezo</th>
                                </tr>
                            </thead>
                            <tbody id="salary-history-body">
                                <tr><td colspan="4" class="px-3 py-4 text-center text-gray-500 text-sm">Hakuna historia</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Deductions -->
                <div>
                    <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                        <h4 class="text-sm font-semibold text-gray-800">Makato</h4>
                        <button onclick="showAddDeductionModal()" 
                                class="px-3 py-1.5 bg-red-600 text-white rounded hover:bg-red-700 text-xs font-medium transition">
                            <i class="fas fa-minus-circle mr-1"></i> Ongeza Kato
                        </button>
                    </div>
                    <div class="overflow-x-auto rounded-lg border border-gray-200">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Sababu</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Kiasi</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Hali</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Tarehe</th>
                                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500">Vitendo</th>
                                </tr>
                            </thead>
                            <tbody id="deductions-body">
                                <tr><td colspan="5" class="px-3 py-4 text-center text-gray-500 text-sm">Hakuna makato</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 2: Order Summary -->
        <div id="salary-orders-tab" class="salary-tab-content hidden">
            <div id="order-summary-container" class="hidden">
                <!-- Order Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                        <p class="text-xs text-gray-500">Jumla Orders</p>
                        <p class="text-lg font-bold text-gray-800" id="orders-total">0</p>
                    </div>
                    <div class="bg-green-50 p-3 rounded-lg border border-green-200">
                        <p class="text-xs text-green-600">Zilizolipwa</p>
                        <p class="text-lg font-bold text-green-700" id="orders-paid">0</p>
                    </div>
                    <div class="bg-amber-50 p-3 rounded-lg border border-amber-200">
                        <p class="text-xs text-amber-600">Zinazosubiri</p>
                        <p class="text-lg font-bold text-amber-700" id="orders-unpaid">0</p>
                    </div>
                    <div class="bg-red-50 p-3 rounded-lg border border-red-200">
                        <p class="text-xs text-red-600">Zilizofutwa</p>
                        <p class="text-lg font-bold text-red-700" id="orders-cancelled">0</p>
                    </div>
                </div>

                <!-- Loss Summary -->
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mb-4">
                    <div class="bg-blue-50 p-3 rounded-lg border border-blue-200">
                        <p class="text-xs text-blue-600">Mapato Jumla</p>
                        <p class="text-lg font-bold text-blue-700" id="orders-revenue">0 TZS</p>
                    </div>
                    <div class="bg-red-50 p-3 rounded-lg border border-red-200">
                        <p class="text-xs text-red-600">Hasara Jumla</p>
                        <p class="text-lg font-bold text-red-700" id="orders-loss">0 TZS</p>
                    </div>
                    <div class="bg-purple-50 p-3 rounded-lg border border-purple-200">
                        <p class="text-xs text-purple-600">Kiwango</p>
                        <p class="text-lg font-bold text-purple-700" id="orders-rate">0%</p>
                    </div>
                </div>

                <!-- Unpaid Orders List -->
                <div class="mb-4">
                    <h4 class="text-sm font-semibold text-amber-700 mb-2">
                        <i class="fas fa-clock mr-1"></i> Orders Zinazosubiri Malipo
                    </h4>
                    <div class="overflow-x-auto rounded-lg border border-gray-200" id="unpaid-orders-container">
                        <p class="text-center text-gray-500 text-sm py-4">Hakuna orders zinazosubiri</p>
                    </div>
                </div>

                <!-- Cancelled Orders List -->
                <div>
                    <h4 class="text-sm font-semibold text-red-700 mb-2">
                        <i class="fas fa-times-circle mr-1"></i> Orders Zilizofutwa (Hasara)
                    </h4>
                    <div class="overflow-x-auto rounded-lg border border-gray-200" id="cancelled-orders-container">
                        <p class="text-center text-gray-500 text-sm py-4">Hakuna orders zilizofutwa</p>
                    </div>
                </div>
            </div>
            <div id="order-summary-empty" class="text-center py-8 text-gray-500">
                <i class="fas fa-user mr-2"></i> Chagua mfanyakazi kuona taarifa za orders
            </div>
        </div>
    </div>
</div>

<!-- Add Salary Modal -->
<div id="add-salary-modal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-lg shadow-lg w-full max-w-md mx-auto z-50">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-800">Ongeza Mshahara</h3>
        </div>
        <form id="add-salary-form" class="p-4">
            @csrf
            <input type="hidden" id="salary-employee-id">
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Mfanyakazi</label>
                    <input type="text" id="salary-employee-name-display" class="w-full px-3 py-2 border border-gray-300 rounded text-sm bg-gray-50" readonly>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Kiasi (TZS) *</label>
                    <input type="number" name="salary" id="salary-amount" 
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                           placeholder="0" required min="0" step="1000">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Muda wa Malipo</label>
                    <select name="frequency" id="salary-frequency" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                        <option value="monthly">Kila Mwezi</option>
                        <option value="weekly">Kila Wiki</option>
                        <option value="daily">Kila Siku</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Maelezo</label>
                    <textarea name="remarks" id="salary-remarks" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                              placeholder="Maelezo ya ziada..."></textarea>
                </div>
            </div>
            <div class="flex gap-2 pt-4 border-t border-gray-200 mt-4">
                <button type="button" onclick="closeAddSalaryModal()"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 text-sm">
                    Ghairi
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 text-sm font-medium">
                    Hifadhi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Add Deduction Modal -->
<div id="add-deduction-modal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-lg shadow-lg w-full max-w-md mx-auto z-50">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-800">Ongeza Kato</h3>
        </div>
        <form id="add-deduction-form" class="p-4">
            @csrf
            <input type="hidden" id="deduction-employee-id">
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Mfanyakazi</label>
                    <input type="text" id="deduction-employee-name" class="w-full px-3 py-2 border border-gray-300 rounded text-sm bg-gray-50" readonly>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Sababu ya Kato *</label>
                    <input type="text" name="reason" id="deduction-reason" 
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                           placeholder="Mfano: Order iliyofutwa, Hasara..." required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Kiasi (TZS) *</label>
                    <input type="number" name="amount" id="deduction-amount" 
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                           placeholder="0" required min="0" step="100">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Maelezo</label>
                    <textarea name="remarks" id="deduction-remarks" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                              placeholder="Maelezo ya ziada..."></textarea>
                </div>
            </div>
            <div class="flex gap-2 pt-4 border-t border-gray-200 mt-4">
                <button type="button" onclick="closeAddDeductionModal()"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 text-sm">
                    Ghairi
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm font-medium">
                    Ongeza Kato
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// ============================================
// SALARY MANAGEMENT FUNCTIONS - WITH AUTO-LOAD
// ============================================

let selectedEmployeeId = null;
let selectedEmployeeData = null;
let currentSalaryTab = 'details';

// Tab switching with auto-load
function switchSalaryTab(tab, btn) {
    currentSalaryTab = tab;
    
    document.querySelectorAll('.salary-tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.salary-tab-btn').forEach(el => {
        el.className = 'salary-tab-btn px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700';
    });
    
    if (tab === 'details') {
        document.getElementById('salary-details-tab').classList.remove('hidden');
        if (btn) btn.className = 'salary-tab-btn px-4 py-2 text-sm font-medium text-emerald-700 border-b-2 border-emerald-600';
        // If employee is selected, load details
        if (selectedEmployeeId) {
            loadEmployeeSalaryDetails(selectedEmployeeId);
        }
    } else {
        document.getElementById('salary-orders-tab').classList.remove('hidden');
        if (btn) btn.className = 'salary-tab-btn px-4 py-2 text-sm font-medium text-emerald-700 border-b-2 border-emerald-600';
        // If employee is selected, load order summary
        if (selectedEmployeeId) {
            loadOrderSummary(selectedEmployeeId);
            document.getElementById('order-summary-container').classList.remove('hidden');
            document.getElementById('order-summary-empty').classList.add('hidden');
        }
    }
}

function getWafanyakaziUrl(path) {
    if (path.startsWith('/')) path = path.substring(1);
    if (path.includes('wafanyakazi')) return '/' + path;
    return '/wafanyakazi/' + path;
}

// Toggle Counter Access
function toggleCounterAccess() {
    const employeeId = window.selectedEmployeeId || selectedEmployeeId;
    if (!employeeId) {
        showNotification('Tafadhali chagua mfanyakazi kwanza', 'warning');
        return;
    }
    if (!confirm('Una uhakika unataka kubadilisha ruhusa ya Counter?')) return;
    
    const url = getWafanyakaziUrl(employeeId + '/toggle-counter');
    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            loadEmployeeSalaryDetails(employeeId);
        } else {
            showNotification(data.message || 'Hitilafu imetokea', 'error');
        }
    })
    .catch(error => {
        console.error('Toggle counter error:', error);
        showNotification('Hitilafu: ' + error.message, 'error');
    });
}

// Load Employee Salary Details
function loadEmployeeSalaryDetails(employeeId) {
    if (!employeeId) {
        showNotification('Tafadhali chagua mfanyakazi', 'warning');
        return;
    }
    
    window.selectedEmployeeId = employeeId;
    selectedEmployeeId = employeeId;
    
    const detailsDiv = document.getElementById('salary-employee-details');
    detailsDiv.classList.remove('hidden');
    document.getElementById('salary-employee-name').textContent = 'Inapakia...';
    
    const url = getWafanyakaziUrl(employeeId + '/salary-details');
    fetch(url, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayEmployeeSalaryDetails(data.data);
        } else {
            showNotification(data.message || 'Hitilafu katika kupata taarifa', 'error');
            detailsDiv.classList.add('hidden');
        }
    })
    .catch(error => {
        console.error('Load salary details error:', error);
        showNotification('Hitilafu: ' + error.message, 'error');
        detailsDiv.classList.add('hidden');
    });
}

function displayEmployeeSalaryDetails(data) {
    const detailsDiv = document.getElementById('salary-employee-details');
    detailsDiv.classList.remove('hidden');
    
    const employee = data.employee;
    selectedEmployeeData = data;
    
    document.getElementById('salary-employee-name').textContent = employee.jina || '--';
    document.getElementById('salary-employee-info').textContent = 
        `${employee.jinsia || '--'} • ${employee.uwezo === 'mkubwa' ? 'Mkubwa' : 'Mdogo'} • Counter: ${employee.allow_counter_access ? '✅ Inaruhusiwa' : '❌ Hairuhusiwi'}`;
    
    const toggleBtn = document.getElementById('counter-toggle-btn');
    if (employee.allow_counter_access) {
        toggleBtn.className = 'px-3 py-1.5 bg-red-600 text-white rounded hover:bg-red-700 text-xs font-medium transition';
        toggleBtn.innerHTML = '<i class="fas fa-times mr-1"></i> Ondoa Counter';
    } else {
        toggleBtn.className = 'px-3 py-1.5 bg-green-600 text-white rounded hover:bg-green-700 text-xs font-medium transition';
        toggleBtn.innerHTML = '<i class="fas fa-check mr-1"></i> Ruhusu Counter';
    }
    
    document.getElementById('total-salary-display').textContent = (data.current_salary || 0).toLocaleString() + ' TZS';
    document.getElementById('total-deductions-display').textContent = (data.total_deductions || 0).toLocaleString() + ' TZS';
    document.getElementById('net-salary-display').textContent = (data.net_salary || 0).toLocaleString() + ' TZS';
    document.getElementById('order-count-display').textContent = data.salaries?.length || 0;
    
    renderSalaryHistory(data.salaries || []);
    renderDeductions(data.deductions || [], data.pending_deductions || []);
}

function renderSalaryHistory(salaries) {
    const tbody = document.getElementById('salary-history-body');
    if (!salaries || salaries.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="px-3 py-4 text-center text-gray-500 text-sm">Hakuna historia</td></tr>';
        return;
    }
    tbody.innerHTML = salaries.map(s => `
        <tr>
            <td class="px-3 py-2 text-xs">${s.created_at ? new Date(s.created_at).toLocaleDateString('sw-TZ') : '--'}</td>
            <td class="px-3 py-2 text-xs font-medium text-green-700">${(s.salary_amount || 0).toLocaleString()} ${s.currency || 'TZS'}</td>
            <td class="px-3 py-2 text-xs">${s.frequency || 'monthly'}</td>
            <td class="px-3 py-2 text-xs text-gray-500">${s.remarks || '--'}</td>
        </tr>
    `).join('');
}

function renderDeductions(deductions, pendingDeductions) {
    const tbody = document.getElementById('deductions-body');
    const allDeductions = [...(deductions || []), ...(pendingDeductions || [])];
    if (allDeductions.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="px-3 py-4 text-center text-gray-500 text-sm">Hakuna makato</td></tr>';
        return;
    }
    tbody.innerHTML = allDeductions.map(d => {
        let statusClass = '', statusText = '', actionButtons = '';
        if (d.status === 'approved') {
            statusClass = 'bg-green-100 text-green-800';
            statusText = 'Imeidhinishwa';
        } else if (d.status === 'pending') {
            statusClass = 'bg-yellow-100 text-yellow-800';
            statusText = 'Inasubiri';
            actionButtons = `
                <button onclick="approveDeduction(${d.id})" class="text-green-600 hover:text-green-800 text-xs" title="Idhinisha">
                    <i class="fas fa-check-circle"></i>
                </button>
                <button onclick="rejectDeduction(${d.id})" class="text-red-600 hover:text-red-800 text-xs" title="Kataa">
                    <i class="fas fa-times-circle"></i>
                </button>
            `;
        } else {
            statusClass = 'bg-red-100 text-red-800';
            statusText = 'Imekataliwa';
        }
        return `
            <tr>
                <td class="px-3 py-2 text-xs">${d.reason || '--'}</td>
                <td class="px-3 py-2 text-xs font-medium text-red-700">${(d.amount || 0).toLocaleString()} TZS</td>
                <td class="px-3 py-2 text-xs"><span class="px-2 py-0.5 rounded text-xs font-medium ${statusClass}">${statusText}</span></td>
                <td class="px-3 py-2 text-xs">${d.created_at ? new Date(d.created_at).toLocaleDateString('sw-TZ') : '--'}</td>
                <td class="px-3 py-2 text-center">
                    <div class="flex items-center justify-center gap-1">${actionButtons}</div>
                </td>
            </tr>
        `;
    }).join('');
}

// Load Order Summary
function loadOrderSummary(employeeId) {
    const container = document.getElementById('order-summary-container');
    container.classList.remove('hidden');
    document.getElementById('order-summary-empty').classList.add('hidden');
    
    fetch(`/wafanyakazi/${employeeId}/order-summary`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayOrderSummary(data.data);
            }
        })
        .catch(error => {
            console.error('Error loading order summary:', error);
        });
}

function displayOrderSummary(data) {
    document.getElementById('orders-total').textContent = data.total_orders || 0;
    document.getElementById('orders-paid').textContent = data.total_paid || 0;
    document.getElementById('orders-unpaid').textContent = data.total_unpaid || 0;
    document.getElementById('orders-cancelled').textContent = data.total_cancelled || 0;
    document.getElementById('orders-revenue').textContent = (data.total_paid || 0).toLocaleString() + ' TZS';
    document.getElementById('orders-loss').textContent = (data.total_loss || 0).toLocaleString() + ' TZS';
    document.getElementById('orders-rate').textContent = (data.completion_rate || 0) + '%';
    
    // Render unpaid orders
    const unpaidContainer = document.getElementById('unpaid-orders-container');
    if (data.unpaid_orders && data.unpaid_orders.length > 0) {
        unpaidContainer.innerHTML = `
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">#</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Mteja</th>
                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">Jumla</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500">Hali</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500">Vitendo</th>
                    </tr>
                </thead>
                <tbody>
                    ${data.unpaid_orders.map(order => `
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="px-3 py-2 text-xs font-mono">${order.order_number}</td>
                            <td class="px-3 py-2 text-xs">${order.customer_name || 'Walk-in'}</td>
                            <td class="px-3 py-2 text-right text-xs font-semibold text-amber-700">${(order.total || 0).toLocaleString()} TZS</td>
                            <td class="px-3 py-2 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-700">${order.status}</span>
                            </td>
                            <td class="px-3 py-2 text-center">
                                <button onclick="viewOrderDetails('${order.id}')" class="text-blue-600 hover:text-blue-800 text-xs">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        `;
    } else {
        unpaidContainer.innerHTML = '<p class="text-center text-green-500 text-sm py-4"><i class="fas fa-check-circle mr-1"></i> Hakuna orders zinazosubiri</p>';
    }
    
    // Render cancelled orders
    const cancelledContainer = document.getElementById('cancelled-orders-container');
    if (data.cancelled_orders && data.cancelled_orders.length > 0) {
        cancelledContainer.innerHTML = `
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">#</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Mteja</th>
                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">Hasara</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500">Kato</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500">Vitendo</th>
                    </tr>
                </thead>
                <tbody>
                    ${data.cancelled_orders.map(order => `
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="px-3 py-2 text-xs font-mono">${order.order_number}</td>
                            <td class="px-3 py-2 text-xs">${order.customer_name || 'Walk-in'}</td>
                            <td class="px-3 py-2 text-right text-xs font-semibold text-red-700">${(order.total || 0).toLocaleString()} TZS</td>
                            <td class="px-3 py-2 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">Hakuna</span>
                            </td>
                            <td class="px-3 py-2 text-center">
                                <button onclick="addDeductionFromOrder('${order.id}', ${order.total}, '${order.customer_name || 'Mteja'}')" 
                                        class="px-2 py-1 bg-red-600 text-white rounded text-xs hover:bg-red-700">
                                    <i class="fas fa-minus-circle mr-1"></i> Ongeza Kato
                                </button>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        `;
    } else {
        cancelledContainer.innerHTML = '<p class="text-center text-green-500 text-sm py-4"><i class="fas fa-check-circle mr-1"></i> Hakuna orders zilizofutwa</p>';
    }
}

// View Order Details
function viewOrderDetails(orderId) {
    // Open in new tab with search
    window.open(`/mauzo?search=${orderId}`, '_blank');
}

// Add Deduction From Order
function addDeductionFromOrder(orderId, amount, customerName) {
    const employeeId = window.selectedEmployeeId || selectedEmployeeId;
    if (!employeeId) {
        showNotification('Tafadhali chagua mfanyakazi', 'warning');
        return;
    }
    
    const reason = prompt('Sababu ya kato (Mfano: Order iliyofutwa):', 'Order iliyofutwa');
    if (!reason) return;
    
    const remarks = prompt('Maelezo ya ziada (hiari):', '');
    
    // Show loading
    showNotification('Inaongeza kato...', 'info');
    
    fetch('/wafanyakazi/deduction-from-order', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            mfanyakazi_id: employeeId,
            order_id: orderId,
            amount: amount,
            reason: reason,
            remarks: remarks || ''
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Kato imeongezwa kikamilifu!', 'success');
            // Reload both tabs
            loadOrderSummary(employeeId);
            loadEmployeeSalaryDetails(employeeId);
        } else {
            showNotification(data.message || 'Hitilafu imetokea', 'error');
        }
    })
    .catch(error => {
        showNotification('Hitilafu ya mtandao', 'error');
    });
}

// Show Add Salary Modal
function showAddSalaryModal() {
    const employeeId = window.selectedEmployeeId || selectedEmployeeId;
    if (!employeeId) {
        showNotification('Tafadhali chagua mfanyakazi kwanza', 'warning');
        return;
    }
    document.getElementById('salary-employee-id').value = employeeId;
    document.getElementById('salary-employee-name-display').value = document.getElementById('salary-employee-name').textContent;
    document.getElementById('salary-amount').value = '';
    document.getElementById('salary-frequency').value = 'monthly';
    document.getElementById('salary-remarks').value = '';
    document.getElementById('add-salary-modal').classList.remove('hidden');
}

function closeAddSalaryModal() {
    document.getElementById('add-salary-modal').classList.add('hidden');
}

// Show Add Deduction Modal
function showAddDeductionModal() {
    const employeeId = window.selectedEmployeeId || selectedEmployeeId;
    if (!employeeId) {
        showNotification('Tafadhali chagua mfanyakazi kwanza', 'warning');
        return;
    }
    document.getElementById('deduction-employee-id').value = employeeId;
    document.getElementById('deduction-employee-name').value = document.getElementById('salary-employee-name').textContent;
    document.getElementById('deduction-reason').value = '';
    document.getElementById('deduction-amount').value = '';
    document.getElementById('deduction-remarks').value = '';
    document.getElementById('add-deduction-modal').classList.remove('hidden');
}

function closeAddDeductionModal() {
    document.getElementById('add-deduction-modal').classList.add('hidden');
}

// Approve Deduction
function approveDeduction(deductionId) {
    if (!confirm('Una uhakika unataka kuidhinisha kato hii?')) return;
    const employeeId = window.selectedEmployeeId || selectedEmployeeId;
    const url = getWafanyakaziUrl('deduction/' + deductionId + '/approve');
    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            loadEmployeeSalaryDetails(employeeId);
        } else {
            showNotification(data.message || 'Hitilafu imetokea', 'error');
        }
    })
    .catch(error => {
        showNotification('Hitilafu: ' + error.message, 'error');
    });
}

// Reject Deduction
function rejectDeduction(deductionId) {
    if (!confirm('Una uhakika unataka kukataa kato hii?')) return;
    const employeeId = window.selectedEmployeeId || selectedEmployeeId;
    const url = getWafanyakaziUrl('deduction/' + deductionId + '/reject');
    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            loadEmployeeSalaryDetails(employeeId);
        } else {
            showNotification(data.message || 'Hitilafu imetokea', 'error');
        }
    })
    .catch(error => {
        showNotification('Hitilafu: ' + error.message, 'error');
    });
}

// Submit Add Salary Form
document.getElementById('add-salary-form')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const employeeId = document.getElementById('salary-employee-id').value;
    const formData = new FormData(this);
    const url = getWafanyakaziUrl(employeeId + '/update-salary');
    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            closeAddSalaryModal();
            loadEmployeeSalaryDetails(employeeId);
        } else {
            showNotification(data.message || 'Hitilafu imetokea', 'error');
        }
    })
    .catch(error => {
        showNotification('Hitilafu: ' + error.message, 'error');
    });
});

// Submit Add Deduction Form
document.getElementById('add-deduction-form')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const employeeId = document.getElementById('deduction-employee-id').value;
    const formData = new FormData(this);
    const url = getWafanyakaziUrl(employeeId + '/add-deduction');
    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            closeAddDeductionModal();
            loadEmployeeSalaryDetails(employeeId);
        } else {
            showNotification(data.message || 'Hitilafu imetokea', 'error');
        }
    })
    .catch(error => {
        showNotification('Hitilafu: ' + error.message, 'error');
    });
});

// Employee Select Change - AUTO-LOAD
document.getElementById('salary-employee-select')?.addEventListener('change', function() {
    const employeeId = this.value;
    if (employeeId) {
        selectedEmployeeId = employeeId;
        window.selectedEmployeeId = employeeId;
        
        // Load based on current active tab
        if (currentSalaryTab === 'details') {
            loadEmployeeSalaryDetails(employeeId);
            document.getElementById('salary-employee-details').classList.remove('hidden');
        } else {
            loadOrderSummary(employeeId);
            document.getElementById('order-summary-container').classList.remove('hidden');
            document.getElementById('order-summary-empty').classList.add('hidden');
        }
    } else {
        document.getElementById('salary-employee-details').classList.add('hidden');
        document.getElementById('order-summary-container').classList.add('hidden');
        document.getElementById('order-summary-empty').classList.remove('hidden');
        selectedEmployeeId = null;
        window.selectedEmployeeId = null;
        selectedEmployeeData = null;
    }
});

// Show Notification
function showNotification(message, type = 'info') {
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
    notification.textContent = message;
    container.appendChild(notification);
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateY(-10px)';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Initialize - auto-load if employee already selected
document.addEventListener('DOMContentLoaded', function() {
    // If there's a pre-selected employee from session
    if (window.selectedEmployeeId) {
        selectedEmployeeId = window.selectedEmployeeId;
        const select = document.getElementById('salary-employee-select');
        if (select) {
            select.value = window.selectedEmployeeId;
            // Auto-load based on current tab
            if (currentSalaryTab === 'details') {
                loadEmployeeSalaryDetails(window.selectedEmployeeId);
                document.getElementById('salary-employee-details').classList.remove('hidden');
            } else {
                loadOrderSummary(window.selectedEmployeeId);
                document.getElementById('order-summary-container').classList.remove('hidden');
                document.getElementById('order-summary-empty').classList.add('hidden');
            }
        }
    }
});
</script>
@endpush