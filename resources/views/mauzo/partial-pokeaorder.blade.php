{{-- resources/views/mauzo/partial-pokeaorder.blade.php --}}

<!-- Pokea Order Tab Content -->
<div id="pokeaorder-tab-content" class="tab-content hidden">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        
        <!-- Header -->
        <div class="px-4 py-3 bg-gradient-to-r from-emerald-50 to-green-50 border-b border-gray-200">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-clipboard-list text-emerald-600 mr-2"></i>
                        Pokea Orders
                    </h3>
                    <p class="text-xs text-gray-500">Orders from customers via showcase page</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button onclick="refreshPokeaOrders()" class="px-3 py-1.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium transition flex items-center gap-1">
                        <i class="fas fa-sync-alt"></i> Fresha
                    </button>
                </div>
            </div>
        </div>

<!-- Stats - Simple & Clean Cards -->
<div class="grid grid-cols-3 md:grid-cols-5 gap-2 px-4 py-3 bg-gray-50 border-b border-gray-200">
    <div class="text-center p-2 bg-white rounded-lg border border-gray-200">
        <p class="text-[10px] text-gray-500">Jumla</p>
        <p class="text-base font-bold text-gray-700" id="pokea-total">0</p>
    </div>
    <div class="text-center p-2 bg-white rounded-lg border border-yellow-200">
        <p class="text-[10px] text-gray-500">Inasubiri</p>
        <p class="text-base font-bold text-yellow-600" id="pokea-pending">0</p>
    </div>
    <div class="text-center p-2 bg-white rounded-lg border border-blue-200">
        <p class="text-[10px] text-gray-500">Imethibitishwa</p>
        <p class="text-base font-bold text-blue-600" id="pokea-confirmed">0</p>
    </div>
    <div class="text-center p-2 bg-white rounded-lg border border-green-200">
        <p class="text-[10px] text-gray-500">Imelipwa</p>
        <p class="text-base font-bold text-green-600" id="pokea-paid">0</p>
    </div>
    <div class="text-center p-2 bg-white rounded-lg border border-red-200">
        <p class="text-[10px] text-gray-500">Imefutwa</p>
        <p class="text-base font-bold text-red-600" id="pokea-cancelled">0</p>
    </div>
</div>

        <!-- Filter Bar - Modern -->
        <div class="px-4 py-2 bg-gray-50 border-b border-gray-200 flex flex-wrap gap-2 items-center">
            <span class="text-xs font-medium text-gray-600">Chuja:</span>
            <button onclick="filterPokeaOrders('all')" class="filter-btn px-3 py-1 rounded-full text-xs font-medium bg-emerald-600 text-white transition" data-filter="all">
                Zote
            </button>
            <button onclick="filterPokeaOrders('saved')" class="filter-btn px-3 py-1 rounded-full text-xs font-medium bg-gray-200 text-gray-700 hover:bg-yellow-100 transition" data-filter="saved">
                <i class="fas fa-clock mr-1"></i> Inasubiri
            </button>
            <button onclick="filterPokeaOrders('confirmed')" class="filter-btn px-3 py-1 rounded-full text-xs font-medium bg-gray-200 text-gray-700 hover:bg-blue-100 transition" data-filter="confirmed">
                <i class="fas fa-check-circle mr-1"></i> Imethibitishwa
            </button>
            <button onclick="filterPokeaOrders('paid')" class="filter-btn px-3 py-1 rounded-full text-xs font-medium bg-gray-200 text-gray-700 hover:bg-green-100 transition" data-filter="paid">
                <i class="fas fa-money-bill-wave mr-1"></i> Imelipwa
            </button>
            <button onclick="filterPokeaOrders('cancelled')" class="filter-btn px-3 py-1 rounded-full text-xs font-medium bg-gray-200 text-gray-700 hover:bg-red-100 transition" data-filter="cancelled">
                <i class="fas fa-times-circle mr-1"></i> Imefutwa
            </button>
        </div>

        <!-- Orders Table - Modern -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#Order</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mteja</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Simu</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Bidhaa</th>
                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Jumla</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Hali</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider hidden xl:table-cell">Tarehe</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Vitendo</th>
                    </tr>
                </thead>
                <tbody id="pokea-orders-tbody" class="divide-y divide-gray-100">
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-3xl text-gray-300 mb-2"></i>
                            <p>Hakuna orders zilizopokelewa</p>
                            <p class="text-xs text-gray-400">Orders kutoka kwa wateja zitaonekana hapa</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div id="pokea-pagination" class="px-4 py-2 border-t border-gray-200 bg-gray-50 hidden">
            <div class="flex justify-between items-center">
                <span class="text-xs text-gray-500" id="pokea-showing">Showing 0 orders</span>
                <div class="flex gap-1">
                    <button onclick="loadPokeaOrders('prev')" class="px-3 py-1 border border-gray-300 rounded text-xs hover:bg-gray-50 transition">←</button>
                    <span id="pokea-page-info" class="px-3 py-1 text-xs">Ukurasa 1</span>
                    <button onclick="loadPokeaOrders('next')" class="px-3 py-1 border border-gray-300 rounded text-xs hover:bg-gray-50 transition">→</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Order Detail Modal - Modern -->
<div id="pokea-order-modal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="modal-overlay absolute inset-0 bg-black bg-opacity-50 backdrop-blur-sm"></div>
    <div class="modal-content bg-white rounded-xl shadow-2xl w-full max-w-2xl mx-auto z-50 max-h-[90vh] overflow-y-auto">
        <div class="p-4 border-b border-gray-200 sticky top-0 bg-white z-10 rounded-t-xl flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-clipboard-list text-emerald-600 mr-2"></i>
                Maelezo ya Order
            </h3>
            <button onclick="closePokeaModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-1 rounded transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div id="pokea-modal-body" class="p-4">
            <!-- Order details will be loaded here -->
        </div>
    </div>
</div>

<!-- Toast Notification Container -->
<div class="toast-container" id="toastContainerPokea"></div>

<style>
/* Modal Styles */
.modal {
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
}

.modal-content {
    position: relative;
    max-height: 90vh;
    overflow-y: auto;
}

/* Status Badges - Modern */
.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 3px 12px;
    border-radius: 9999px;
    font-size: 11px;
    font-weight: 600;
    gap: 4px;
}

.status-badge .badge-icon {
    font-size: 10px;
}

/* Table row hover */
#pokea-orders-tbody tr {
    transition: background-color 0.2s ease;
}

#pokea-orders-tbody tr:hover {
    background-color: #f9fafb;
    cursor: pointer;
}

/* Filter buttons - Modern */
.filter-btn {
    transition: all 0.2s ease;
    cursor: pointer;
}

.filter-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.filter-btn.active {
    background: #10b981;
    color: white;
}

/* Scroll for items */
#pokea-modal-body .max-h-60 {
    max-height: 250px;
    overflow-y: auto;
}

/* Toast Notification - Centered Top */
.toast-container {
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 9999;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    max-width: 90%;
    width: 400px;
    pointer-events: none;
}

.toast {
    padding: 12px 24px;
    border-radius: 10px;
    color: white;
    font-size: 14px;
    font-weight: 500;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    animation: slideDown 0.4s ease forwards;
    width: 100%;
    text-align: center;
    pointer-events: auto;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.toast-success { background: #10b981; }
.toast-error { background: #ef4444; }
.toast-info { background: #3b82f6; }
.toast-warning { background: #f59e0b; }

@keyframes slideDown {
    from { opacity: 0; transform: translateY(-30px) scale(0.95); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}

.toast-out {
    animation: slideUp 0.3s ease forwards;
}

@keyframes slideUp {
    from { opacity: 1; transform: translateY(0) scale(1); }
    to { opacity: 0; transform: translateY(-30px) scale(0.95); }
}

/* Action Buttons - Modern */
.action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 500;
    transition: all 0.2s ease;
    border: none;
    cursor: pointer;
    min-width: 36px;
}

.action-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.action-btn:active {
    transform: scale(0.95);
}

.action-btn-sm {
    padding: 4px 8px;
    font-size: 11px;
}

.action-btn-view { background: #dbeafe; color: #1e40af; }
.action-btn-view:hover { background: #bfdbfe; }

.action-btn-confirm { background: #d1fae5; color: #065f46; }
.action-btn-confirm:hover { background: #a7f3d0; }

.action-btn-pay { background: #d1fae5; color: #065f46; }
.action-btn-pay:hover { background: #a7f3d0; }

.action-btn-cancel { background: #fee2e2; color: #991b1b; }
.action-btn-cancel:hover { background: #fecaca; }

.action-btn-whatsapp { background: #d1fae5; color: #065f46; }
.action-btn-whatsapp:hover { background: #a7f3d0; }

/* Table action buttons container */
.table-actions {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 2px;
    flex-wrap: wrap;
}

/* Responsive */
@media (max-width: 640px) {
    #pokea-orders-tbody td {
        padding: 6px 8px;
        font-size: 11px;
    }
    
    .modal-content {
        margin: 8px;
        padding: 12px;
    }
    
    .status-badge {
        font-size: 9px;
        padding: 2px 8px;
    }
    
    .action-btn {
        padding: 4px 6px;
        font-size: 10px;
        min-width: 28px;
    }
    
    .action-btn span {
        display: none;
    }
}

@media (min-width: 641px) and (max-width: 1024px) {
    #pokea-orders-tbody td {
        padding: 8px 10px;
        font-size: 12px;
    }
}

/* Modern Scrollbar */
::-webkit-scrollbar {
    width: 4px;
    height: 4px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}

/* Modal body scroll */
#pokea-modal-body::-webkit-scrollbar {
    width: 4px;
}

#pokea-modal-body::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

#pokea-modal-body::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 4px;
}
</style>

<script>
// ============================================
// POKEA ORDER - JavaScript Functions
// ============================================

let pokeaOrders = [];
let pokeaCurrentFilter = 'all';
let pokeaCurrentPage = 1;
let pokeaPerPage = 20;
let pokeaSelectedOrder = null;
let pokeaAutoRefreshInterval = null;
let pokeaIsLoading = false;

// ===== INITIALIZE =====
document.addEventListener('DOMContentLoaded', function() {
    // Load orders when tab is shown
    const observer = new MutationObserver(function() {
        const tabContent = document.getElementById('pokeaorder-tab-content');
        if (tabContent && !tabContent.classList.contains('hidden')) {
            loadPokeaOrders();
            startPokeaAutoRefresh();
        } else {
            stopPokeaAutoRefresh();
        }
    });
    
    const target = document.getElementById('pokeaorder-tab-content');
    if (target) {
        observer.observe(target, { attributes: true, attributeFilter: ['class'] });
    }
    
    // Initial load if visible
    const tabContent = document.getElementById('pokeaorder-tab-content');
    if (tabContent && !tabContent.classList.contains('hidden')) {
        loadPokeaOrders();
        startPokeaAutoRefresh();
    }
    
    // Close modal on escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closePokeaModal();
        }
    });
});

// ===== AUTO REFRESH =====
function startPokeaAutoRefresh() {
    stopPokeaAutoRefresh();
    pokeaAutoRefreshInterval = setInterval(function() {
        if (!pokeaIsLoading) {
            loadPokeaOrders();
        }
    }, 15000);
}

function stopPokeaAutoRefresh() {
    if (pokeaAutoRefreshInterval) {
        clearInterval(pokeaAutoRefreshInterval);
        pokeaAutoRefreshInterval = null;
    }
}

// ===== LOAD ORDERS =====
function loadPokeaOrders(direction) {
    if (pokeaIsLoading) return;
    
    if (direction === 'prev' && pokeaCurrentPage > 1) {
        pokeaCurrentPage--;
    } else if (direction === 'next') {
        pokeaCurrentPage++;
    }
    
    pokeaIsLoading = true;
    const tbody = document.getElementById('pokea-orders-tbody');
    
    // Show loading state
    tbody.innerHTML = `
        <tr>
            <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                <i class="fas fa-spinner fa-spin text-3xl text-emerald-500 mb-2"></i>
                <p>Inapakia orders...</p>
            </td>
        </tr>
    `;

    let url = `/orders/placed?`;
    if (pokeaCurrentFilter !== 'all') {
        url += `status=${pokeaCurrentFilter}&`;
    }
    url += `page=${pokeaCurrentPage}&per_page=${pokeaPerPage}`;

    fetch(url, {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        pokeaIsLoading = false;
        if (data.success) {
            pokeaOrders = data.data;
            displayPokeaOrders(pokeaOrders);
            updatePokeaStats(pokeaOrders);
            updatePokeaTabBadge();
        } else {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center text-red-500">
                        <i class="fas fa-exclamation-triangle text-3xl mb-2"></i>
                        <p>${data.message || 'Imeshindwa kupakia orders'}</p>
                    </td>
                </tr>
            `;
        }
    })
    .catch(error => {
        pokeaIsLoading = false;
        console.error('Error:', error);
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="px-4 py-8 text-center text-red-500">
                    <i class="fas fa-exclamation-triangle text-3xl mb-2"></i>
                    <p>Hitilafu ya mtandao. Jaribu tena.</p>
                </td>
            </tr>
        `;
    });
}

// ===== DISPLAY ORDERS =====
function displayPokeaOrders(orders) {
    const tbody = document.getElementById('pokea-orders-tbody');
    
    if (!orders || orders.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                    <i class="fas fa-inbox text-3xl text-gray-300 mb-2"></i>
                    <p>Hakuna orders zilizopokelewa</p>
                    <p class="text-xs text-gray-400">Orders kutoka kwa wateja zitaonekana hapa</p>
                </td>
            </tr>
        `;
        return;
    }

    let html = '';
    orders.forEach(order => {
        const statusBadge = getStatusBadge(order.status);
        const itemCount = order.items ? order.items.length : 0;
        const createdDate = new Date(order.created_at).toLocaleString('sw-TZ');
        
        html += `
            <tr class="hover:bg-gray-50 cursor-pointer" onclick="viewPokeaOrder('${order.id}')">
                <td class="px-3 py-2 font-medium text-gray-800 text-xs">${order.order_number}</td>
                <td class="px-3 py-2">
                    <div class="font-medium text-gray-800 text-sm">${order.customer_name || 'Mteja wa Kutembea'}</div>
                    <div class="text-xs text-gray-400">${order.order_type || 'delivery'}</div>
                </td>
                <td class="px-3 py-2 text-sm hidden sm:table-cell">${order.customer_phone || '-'}</td>
                <td class="px-3 py-2 text-center hidden md:table-cell">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-700">
                        ${itemCount} bidhaa
                    </span>
                </td>
                <td class="px-3 py-2 text-right font-semibold text-emerald-600 text-sm">
                    ${formatCurrency(order.total)}
                </td>
                <td class="px-3 py-2 text-center hidden lg:table-cell">
                    <span class="status-badge ${statusBadge.class}">
                        ${statusBadge.icon} ${statusBadge.label}
                    </span>
                </td>
                <td class="px-3 py-2 text-center text-xs text-gray-500 hidden xl:table-cell">${createdDate}</td>
                <td class="px-3 py-2 text-center">
                    <div class="table-actions">
                        <button onclick="event.stopPropagation(); viewPokeaOrder('${order.id}')" 
                                class="action-btn action-btn-view action-btn-sm" title="Tazama">
                            <i class="fas fa-eye"></i>
                            <span class="hidden sm:inline">Tazama</span>
                        </button>
                        ${order.status === 'saved' ? `
                        <button onclick="event.stopPropagation(); confirmPokeaOrder('${order.id}')" 
                                class="action-btn action-btn-confirm action-btn-sm" title="Thibitisha">
                            <i class="fas fa-check-circle"></i>
                            <span class="hidden md:inline">Thibitisha</span>
                        </button>
                        <button onclick="event.stopPropagation(); cancelPokeaOrder('${order.id}')" 
                                class="action-btn action-btn-cancel action-btn-sm" title="Ghairi">
                            <i class="fas fa-times-circle"></i>
                            <span class="hidden md:inline">Ghairi</span>
                        </button>
                        ` : ''}
                        ${order.status === 'confirmed' ? `
                        <button onclick="event.stopPropagation(); markPokeaOrderPaid('${order.id}')" 
                                class="action-btn action-btn-pay action-btn-sm" title="Lipa">
                            <i class="fas fa-money-bill-wave"></i>
                            <span class="hidden md:inline">Lipa</span>
                        </button>
                        ` : ''}
                        <button onclick="event.stopPropagation(); sharePokeaOrderWhatsApp('${order.id}')" 
                                class="action-btn action-btn-whatsapp action-btn-sm" title="WhatsApp">
                            <i class="fab fa-whatsapp"></i>
                            <span class="hidden sm:inline">WhatsApp</span>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });

    tbody.innerHTML = html;
    updatePokeaPagination();
}

// ===== STATUS BADGE =====
function getStatusBadge(status) {
    const statusMap = {
        'saved': { label: 'Inasubiri', class: 'bg-yellow-100 text-yellow-800', icon: '<i class="fas fa-clock badge-icon"></i>' },
        'confirmed': { label: 'Imethibitishwa', class: 'bg-blue-100 text-blue-800', icon: '<i class="fas fa-check-circle badge-icon"></i>' },
        'paid': { label: 'Imelipwa', class: 'bg-green-100 text-green-800', icon: '<i class="fas fa-money-bill-wave badge-icon"></i>' },
        'cancelled': { label: 'Imefutwa', class: 'bg-red-100 text-red-800', icon: '<i class="fas fa-times-circle badge-icon"></i>' }
    };
    return statusMap[status] || statusMap['saved'];
}

// ===== UPDATE STATS =====
function updatePokeaStats(orders) {
    const total = orders.length;
    const pending = orders.filter(o => o.status === 'saved').length;
    const confirmed = orders.filter(o => o.status === 'confirmed').length;
    const paid = orders.filter(o => o.status === 'paid').length;
    const cancelled = orders.filter(o => o.status === 'cancelled').length;

    document.getElementById('pokea-total').textContent = total;
    document.getElementById('pokea-pending').textContent = pending;
    document.getElementById('pokea-confirmed').textContent = confirmed;
    document.getElementById('pokea-paid').textContent = paid;
    document.getElementById('pokea-cancelled').textContent = cancelled;
}

// ===== UPDATE TAB BADGE =====
function updatePokeaTabBadge() {
    const pending = pokeaOrders.filter(o => o.status === 'saved').length;
    const badge = document.getElementById('pokea-orders-count');
    
    if (badge) {
        if (pending > 0) {
            badge.textContent = pending;
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    }
}

// ===== FILTER ORDERS =====
function filterPokeaOrders(filter) {
    pokeaCurrentFilter = filter;
    pokeaCurrentPage = 1;
    
    // Update filter buttons
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('bg-emerald-600', 'text-white');
        btn.classList.add('bg-gray-200', 'text-gray-700');
        if (btn.dataset.filter === filter) {
            btn.classList.remove('bg-gray-200', 'text-gray-700');
            btn.classList.add('bg-emerald-600', 'text-white');
        }
    });
    
    loadPokeaOrders();
}

// ===== UPDATE PAGINATION =====
function updatePokeaPagination() {
    const pagination = document.getElementById('pokea-pagination');
    const showing = document.getElementById('pokea-showing');
    const pageInfo = document.getElementById('pokea-page-info');
    
    if (pokeaOrders.length > 0) {
        pagination.classList.remove('hidden');
        showing.textContent = `Inaonyesha ${pokeaOrders.length} orders`;
        pageInfo.textContent = `Ukurasa ${pokeaCurrentPage}`;
    } else {
        pagination.classList.add('hidden');
    }
}

// ===== VIEW ORDER DETAIL =====
function viewPokeaOrder(orderId) {
    const order = pokeaOrders.find(o => o.id == orderId);
    if (!order) {
        showNotification('Order haijapatikana', 'error');
        return;
    }

    pokeaSelectedOrder = order;
    const modalBody = document.getElementById('pokea-modal-body');
    
    const items = order.items || [];
    let itemsHtml = '';
    let subtotal = 0;
    
    if (items.length > 0) {
        items.forEach((item) => {
            const itemName = item.jina || item.name || 'Bidhaa';
            const itemAina = item.aina || '';
            const itemKipimo = item.kipimo || '';
            const itemQty = item.idadi || item.qty || 0;
            const itemPrice = item.bei || item.price || 0;
            const itemTotal = item.total || (itemQty * itemPrice);
            subtotal += itemTotal;
            
            itemsHtml += `
                <div class="flex justify-between py-2 border-b border-gray-100 text-sm hover:bg-gray-50 px-2 rounded">
                    <div class="flex-1">
                        <span class="font-medium text-gray-800">${itemName}</span>
                        ${itemAina ? `<span class="text-xs text-gray-500 ml-2">${itemAina}</span>` : ''}
                        ${itemKipimo ? `<span class="text-xs text-gray-400 ml-1">(${itemKipimo})</span>` : ''}
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-gray-500">${itemQty} × ${formatCurrency(itemPrice)}</div>
                        <div class="font-semibold text-emerald-600">${formatCurrency(itemTotal)}</div>
                    </div>
                </div>
            `;
        });
    } else {
        itemsHtml = `
            <div class="text-center py-4 text-gray-500">
                <i class="fas fa-box-open text-2xl text-gray-300 mb-1"></i>
                <p class="text-sm">Hakuna bidhaa katika order hii</p>
            </div>
        `;
    }

    const statusBadge = getStatusBadge(order.status);
    const createdDate = new Date(order.created_at).toLocaleString('sw-TZ');
    const total = order.total || subtotal;
    
    modalBody.innerHTML = `
        <div class="space-y-4">
            <!-- Order Info -->
            <div class="grid grid-cols-2 gap-3 bg-gray-50 p-3 rounded-lg">
                <div>
                    <p class="text-xs text-gray-500">Namba ya Order</p>
                    <p class="font-semibold text-gray-800 text-sm">${order.order_number}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Hali</p>
                    <span class="status-badge ${statusBadge.class} text-xs">${statusBadge.icon} ${statusBadge.label}</span>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Tarehe</p>
                    <p class="text-sm">${createdDate}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Aina ya Order</p>
                    <p class="text-sm capitalize">${order.order_type || 'Delivery'}</p>
                </div>
            </div>

            <!-- Customer Info -->
            <div class="bg-blue-50 p-3 rounded-lg border border-blue-200">
                <h4 class="text-sm font-semibold text-blue-800 mb-2">
                    <i class="fas fa-user mr-2"></i> Maelezo ya Mteja
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm">
                    <div><span class="text-gray-500">Jina:</span> <span class="font-medium">${order.customer_name || 'Mteja wa Kutembea'}</span></div>
                    <div><span class="text-gray-500">Simu:</span> <span class="font-medium">${order.customer_phone || '-'}</span></div>
                    <div class="sm:col-span-2"><span class="text-gray-500">Anwani:</span> <span class="font-medium">${order.customer_address || order.delivery_address || '-'}</span></div>
                    ${order.special_instructions ? `
                    <div class="sm:col-span-2"><span class="text-gray-500">Maelekezo:</span> <span class="font-medium">${order.special_instructions}</span></div>
                    ` : ''}
                </div>
            </div>

            <!-- Items -->
            <div>
                <h4 class="text-sm font-semibold text-gray-700 mb-2 flex items-center justify-between">
                    <span><i class="fas fa-box mr-2"></i> Bidhaa Zilizoagizwa (${items.length})</span>
                    <span class="text-xs text-gray-500">Qty × Bei = Jumla</span>
                </h4>
                <div class="bg-white border border-gray-200 rounded-lg p-2 max-h-60 overflow-y-auto">
                    ${itemsHtml}
                </div>
            </div>

            <!-- Totals -->
            <div class="bg-gray-50 p-3 rounded-lg">
                <div class="flex justify-between text-sm py-1">
                    <span class="text-gray-500">Jumla Ndogo</span>
                    <span>${formatCurrency(subtotal)}</span>
                </div>
                ${order.discount > 0 ? `
                <div class="flex justify-between text-sm py-1">
                    <span class="text-gray-500">Punguzo</span>
                    <span class="text-red-500">-${formatCurrency(order.discount)}</span>
                </div>
                ` : ''}
                ${order.delivery_fee > 0 ? `
                <div class="flex justify-between text-sm py-1">
                    <span class="text-gray-500">Gharama ya Usafirishaji</span>
                    <span>${formatCurrency(order.delivery_fee)}</span>
                </div>
                ` : ''}
                <div class="flex justify-between text-lg font-bold border-t border-gray-200 pt-2 mt-2">
                    <span>JUMLA</span>
                    <span class="text-emerald-600">${formatCurrency(total)}</span>
                </div>
            </div>

            <!-- Actions - Modern Single Line -->
            <div class="flex flex-wrap gap-2 pt-2 border-t border-gray-200">
                ${order.status === 'saved' ? `
                <button onclick="confirmPokeaOrder('${order.id}')" class="action-btn bg-blue-600 text-white hover:bg-blue-700 px-4 py-2">
                    <i class="fas fa-check-circle"></i> Thibitisha
                </button>
                <button onclick="markPokeaOrderPaid('${order.id}')" class="action-btn bg-emerald-600 text-white hover:bg-emerald-700 px-4 py-2">
                    <i class="fas fa-money-bill-wave"></i> Lipa
                </button>
                ` : ''}
                ${order.status === 'confirmed' ? `
                <button onclick="markPokeaOrderPaid('${order.id}')" class="action-btn bg-emerald-600 text-white hover:bg-emerald-700 px-4 py-2">
                    <i class="fas fa-money-bill-wave"></i> Lipa
                </button>
                ` : ''}
                ${order.status !== 'cancelled' ? `
                <button onclick="cancelPokeaOrder('${order.id}')" class="action-btn bg-red-600 text-white hover:bg-red-700 px-4 py-2">
                    <i class="fas fa-times-circle"></i> Ghairi
                </button>
                ` : ''}
                <button onclick="sharePokeaOrderWhatsApp('${order.id}')" class="action-btn bg-[#25D366] text-white hover:bg-[#1DA851] px-4 py-2">
                    <i class="fab fa-whatsapp"></i> WhatsApp
                </button>
                <button onclick="printPokeaOrder('${order.id}')" class="action-btn bg-gray-600 text-white hover:bg-gray-700 px-4 py-2">
                    <i class="fas fa-print"></i> Chapisha
                </button>
                <button onclick="closePokeaModal()" class="action-btn bg-gray-200 text-gray-700 hover:bg-gray-300 px-4 py-2">
                    <i class="fas fa-times"></i> Funga
                </button>
            </div>
        </div>
    `;

    document.getElementById('pokea-order-modal').classList.remove('hidden');
}

// ===== CONFIRM ORDER =====
function confirmPokeaOrder(orderId) {
    fetch(`/orders/${orderId}/status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status: 'confirmed' })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Order imethibitishwa!', 'success');
            closePokeaModal();
            loadPokeaOrders();
        } else {
            showNotification(data.message || 'Imeshindwa kuthibitisha order', 'error');
        }
    })
    .catch(error => {
        showNotification('Hitilafu ya mtandao', 'error');
        console.error('Error:', error);
    });
}

// ===== MARK ORDER AS PAID =====
function markPokeaOrderPaid(orderId) {
    fetch(`/orders/${orderId}/status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status: 'paid' })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Mauzo yamerekodiwa!', 'success');
            closePokeaModal();
            loadPokeaOrders();
        } else {
            showNotification(data.message || 'Imeshindwa kulipa order', 'error');
        }
    })
    .catch(error => {
        showNotification('Hitilafu ya mtandao', 'error');
        console.error('Error:', error);
    });
}

// ===== CANCEL ORDER =====
function cancelPokeaOrder(orderId) {
    fetch(`/orders/${orderId}/status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status: 'cancelled' })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Order imeghairiwa!', 'warning');
            closePokeaModal();
            loadPokeaOrders();
        } else {
            showNotification(data.message || 'Imeshindwa kughairi order', 'error');
        }
    })
    .catch(error => {
        showNotification('Hitilafu ya mtandao', 'error');
        console.error('Error:', error);
    });
}

// ===== SHARE ORDER VIA WHATSAPP =====
function sharePokeaOrderWhatsApp(orderId) {
    const order = pokeaOrders.find(o => o.id == orderId);
    if (!order) {
        showNotification('Order haijapatikana', 'error');
        return;
    }

    const companyName = document.querySelector('meta[name="company-name"]')?.content || 'Mauzo Sheet';
    const items = order.items || [];
    
    let message = `🏪 *${companyName}*\n`;
    message += `━━━━━━━━━━━━━━━━━━━━━━━━\n`;
    message += `*MAELEZO YA ODA*\n`;
    message += `Oda: ${order.order_number}\n`;
    message += `Tarehe: ${new Date(order.created_at).toLocaleString()}\n`;
    message += `Mteja: ${order.customer_name || 'Mteja wa Kutembea'}\n`;
    if (order.customer_phone) {
        message += `Simu: ${order.customer_phone}\n`;
    }
    message += `Hali: ${order.status === 'paid' ? '✅ Imelipwa' : order.status === 'cancelled' ? '❌ Imefutwa' : '⏳ Inasubiri'}\n`;
    message += `━━━━━━━━━━━━━━━━━━━━━━━━\n`;
    message += `*BIDHAA*\n`;
    
    items.forEach(item => {
        const itemName = item.jina || item.name || 'Bidhaa';
        const itemQty = item.idadi || item.qty || 0;
        const itemPrice = item.bei || item.price || 0;
        const itemTotal = item.total || (itemQty * itemPrice);
        message += `• ${itemName}`;
        if (item.aina) message += ` (${item.aina})`;
        if (item.kipimo) message += ` - ${item.kipimo}`;
        message += `\n  ${itemQty} × ${formatCurrency(itemPrice)} = ${formatCurrency(itemTotal)}\n`;
    });
    
    const total = order.total || 0;
    message += `━━━━━━━━━━━━━━━━━━━━━━━━\n`;
    if (order.discount > 0) {
        message += `Jumla Ndogo: ${formatCurrency(order.subtotal || total + order.discount)}\n`;
        message += `Punguzo: -${formatCurrency(order.discount)}\n`;
    }
    message += `*JUMLA: ${formatCurrency(total)}*\n`;
    message += `━━━━━━━━━━━━━━━━━━━━━━━━\n`;
    message += `Asante kwa kununua! 🛍️`;
    
    const encodedMessage = encodeURIComponent(message);
    
    let phoneNumber = order.customer_phone || '';
    if (phoneNumber) {
        phoneNumber = phoneNumber.replace(/[^0-9]/g, '');
        if (phoneNumber.startsWith('0')) {
            phoneNumber = '255' + phoneNumber.substring(1);
        } else if (phoneNumber.startsWith('7')) {
            phoneNumber = '255' + phoneNumber;
        } else if (!phoneNumber.startsWith('255') && phoneNumber.length === 9) {
            phoneNumber = '255' + phoneNumber;
        }
    }
    
    let whatsappUrl;
    if (phoneNumber && phoneNumber.length === 12) {
        whatsappUrl = `https://wa.me/${phoneNumber}?text=${encodedMessage}`;
    } else {
        whatsappUrl = `https://wa.me/?text=${encodedMessage}`;
    }
    
    window.open(whatsappUrl, '_blank');
}

// ===== PRINT ORDER =====
function printPokeaOrder(orderId) {
    const order = pokeaOrders.find(o => o.id == orderId);
    if (!order) {
        showNotification('Order haijapatikana', 'error');
        return;
    }

    const companyName = document.querySelector('meta[name="company-name"]')?.content || 'Mauzo Sheet';
    const items = order.items || [];
    const total = order.total || 0;
    
    let itemsHtml = '';
    items.forEach(item => {
        const itemName = item.jina || item.name || 'Bidhaa';
        const itemQty = item.idadi || item.qty || 0;
        const itemPrice = item.bei || item.price || 0;
        const itemTotal = item.total || (itemQty * itemPrice);
        itemsHtml += `
            <tr>
                <td style="padding:4px 8px;border-bottom:1px solid #e5e7eb;">${itemName}</td>
                <td style="padding:4px 8px;border-bottom:1px solid #e5e7eb;text-align:center;">${itemQty}</td>
                <td style="padding:4px 8px;border-bottom:1px solid #e5e7eb;text-align:right;">${formatCurrency(itemPrice)}</td>
                <td style="padding:4px 8px;border-bottom:1px solid #e5e7eb;text-align:right;font-weight:bold;">${formatCurrency(itemTotal)}</td>
            </tr>
        `;
    });

    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
        <head>
            <title>Oda #${order.order_number}</title>
            <style>
                body { font-family: 'Courier New', monospace; padding: 20px; max-width: 400px; margin: 0 auto; }
                .header { text-align: center; margin-bottom: 20px; }
                .header h1 { font-size: 18px; margin: 0; }
                .header p { font-size: 12px; color: #6b7280; margin: 2px 0; }
                .divider { border-top: 1px dashed #d1d5db; margin: 8px 0; }
                .info { font-size: 12px; margin: 4px 0; }
                .info strong { display: inline-block; width: 80px; }
                table { width: 100%; font-size: 12px; border-collapse: collapse; }
                th { text-align: left; padding: 4px 8px; background: #f3f4f6; }
                td { padding: 4px 8px; }
                .total { font-size: 16px; font-weight: bold; text-align: right; padding-top: 8px; border-top: 1px solid #d1d5db; margin-top: 8px; }
                .footer { text-align: center; font-size: 11px; color: #6b7280; margin-top: 16px; border-top: 1px dashed #d1d5db; padding-top: 8px; }
                .status { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; }
                .status-saved { background: #fef3c7; color: #92400e; }
                .status-confirmed { background: #dbeafe; color: #1e40af; }
                .status-paid { background: #d1fae5; color: #065f46; }
                .status-cancelled { background: #fee2e2; color: #991b1b; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>${companyName}</h1>
                <p>Stakabadhi ya Oda</p>
                <p>${order.order_number}</p>
            </div>
            
            <div class="divider"></div>
            
            <div class="info"><strong>Tarehe:</strong> ${new Date(order.created_at).toLocaleString()}</div>
            <div class="info"><strong>Mteja:</strong> ${order.customer_name || 'Mteja wa Kutembea'}</div>
            <div class="info"><strong>Simu:</strong> ${order.customer_phone || '-'}</div>
            <div class="info"><strong>Hali:</strong> <span class="status status-${order.status}">${getStatusBadge(order.status).label}</span></div>
            ${order.delivery_address ? `<div class="info"><strong>Anwani:</strong> ${order.delivery_address}</div>` : ''}
            
            <div class="divider"></div>
            
            <table>
                <thead>
                    <tr>
                        <th>Bidhaa</th>
                        <th style="text-align:center;">Qty</th>
                        <th style="text-align:right;">Bei</th>
                        <th style="text-align:right;">Jumla</th>
                    </tr>
                </thead>
                <tbody>
                    ${itemsHtml}
                </tbody>
            </table>
            
            <div class="total">
                <div style="display:flex;justify-content:space-between;font-size:14px;">
                    <span>JUMLA:</span>
                    <span>${formatCurrency(total)}</span>
                </div>
            </div>
            
            <div class="divider"></div>
            
            <div class="footer">
                Asante kwa kununua! 🛍️<br>
                Powered by Mauzo Sheet
            </div>
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

// ===== CLOSE MODAL =====
function closePokeaModal() {
    document.getElementById('pokea-order-modal').classList.add('hidden');
    pokeaSelectedOrder = null;
}

// ===== REFRESH ORDERS =====
function refreshPokeaOrders() {
    loadPokeaOrders();
    showNotification('Orders zimefresheshwa!', 'info');
}

// ===== FORMAT CURRENCY =====
function formatCurrency(amount) {
    return amount.toLocaleString('en-TZ', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) + ' TZS';
}

// ===== SHOW NOTIFICATION =====
function showNotification(message, type = 'info') {
    const container = document.getElementById('toastContainerPokea');
    if (!container) {
        const newContainer = document.createElement('div');
        newContainer.id = 'toastContainerPokea';
        newContainer.className = 'toast-container';
        document.body.appendChild(newContainer);
        return showNotification(message, type);
    }
    
    const toast = document.createElement('div');
    const iconMap = {
        'success': 'fa-check-circle',
        'error': 'fa-exclamation-circle',
        'info': 'fa-info-circle',
        'warning': 'fa-exclamation-triangle'
    };
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `<i class="fas ${iconMap[type] || 'fa-info-circle'}"></i> ${message}`;
    container.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.add('toast-out');
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}

// ===== CLOSE MODAL ON CLICK OUTSIDE =====
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('pokea-order-modal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal || e.target.classList.contains('modal-overlay')) {
                closePokeaModal();
            }
        });
    }
});

// ===== TAB VISIBILITY =====
document.addEventListener('visibilitychange', function() {
    const tabContent = document.getElementById('pokeaorder-tab-content');
    if (tabContent && !tabContent.classList.contains('hidden')) {
        if (!document.hidden) {
            loadPokeaOrders();
        }
    }
});
</script>