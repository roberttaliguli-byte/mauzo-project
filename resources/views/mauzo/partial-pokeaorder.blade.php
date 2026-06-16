{{-- resources/views/mauzo/partial-pokeaorder.blade.php --}}

<!-- Pokea Order Tab Content -->
<div id="pokeaorder-tab-content" class="tab-content hidden">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        
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
                    <button onclick="refreshPokeaOrders()" class="px-3 py-1.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium transition">
                        <i class="fas fa-sync-alt mr-1"></i> Refresh
                    </button>
                    <button onclick="filterPokeaOrders('all')" class="px-3 py-1.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm font-medium transition">
                        <i class="fas fa-list mr-1"></i> All
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-2 px-4 py-3 bg-gray-50 border-b border-gray-200">
            <div class="text-center p-2 bg-white rounded-lg border border-gray-200">
                <p class="text-xs text-gray-500">Jumla</p>
                <p class="text-lg font-bold text-gray-700" id="pokea-total">0</p>
            </div>
            <div class="text-center p-2 bg-white rounded-lg border border-yellow-200">
                <p class="text-xs text-gray-500">Inasubiri</p>
                <p class="text-lg font-bold text-yellow-600" id="pokea-pending">0</p>
            </div>
            <div class="text-center p-2 bg-white rounded-lg border border-blue-200">
                <p class="text-xs text-gray-500">Imethibitishwa</p>
                <p class="text-lg font-bold text-blue-600" id="pokea-confirmed">0</p>
            </div>
            <div class="text-center p-2 bg-white rounded-lg border border-green-200">
                <p class="text-xs text-gray-500">Imelipwa</p>
                <p class="text-lg font-bold text-green-600" id="pokea-paid">0</p>
            </div>
            <div class="text-center p-2 bg-white rounded-lg border border-red-200">
                <p class="text-xs text-gray-500">Imefutwa</p>
                <p class="text-lg font-bold text-red-600" id="pokea-cancelled">0</p>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="px-4 py-2 bg-gray-50 border-b border-gray-200 flex flex-wrap gap-2 items-center">
            <span class="text-xs font-medium text-gray-600">Filter:</span>
            <button onclick="filterPokeaOrders('all')" class="filter-btn px-3 py-1 rounded-full text-xs font-medium bg-emerald-600 text-white" data-filter="all">
                All
            </button>
            <button onclick="filterPokeaOrders('saved')" class="filter-btn px-3 py-1 rounded-full text-xs font-medium bg-gray-200 text-gray-700 hover:bg-yellow-100" data-filter="saved">
                <i class="fas fa-clock mr-1"></i> Inasubiri
            </button>
            <button onclick="filterPokeaOrders('confirmed')" class="filter-btn px-3 py-1 rounded-full text-xs font-medium bg-gray-200 text-gray-700 hover:bg-blue-100" data-filter="confirmed">
                <i class="fas fa-check-circle mr-1"></i> Imethibitishwa
            </button>
            <button onclick="filterPokeaOrders('paid')" class="filter-btn px-3 py-1 rounded-full text-xs font-medium bg-gray-200 text-gray-700 hover:bg-green-100" data-filter="paid">
                <i class="fas fa-money-bill-wave mr-1"></i> Imelipwa
            </button>
            <button onclick="filterPokeaOrders('cancelled')" class="filter-btn px-3 py-1 rounded-full text-xs font-medium bg-gray-200 text-gray-700 hover:bg-red-100" data-filter="cancelled">
                <i class="fas fa-times-circle mr-1"></i> Imefutwa
            </button>
        </div>

        <!-- Orders Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#Order</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mteja</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Simu</th>
                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Bidhaa</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Jumla</th>
                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Hali</th>
                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tarehe</th>
                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Vitendo</th>
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
                    <button onclick="loadPokeaOrders('prev')" class="px-3 py-1 border border-gray-300 rounded text-xs hover:bg-gray-50">←</button>
                    <span id="pokea-page-info" class="px-3 py-1 text-xs">Page 1</span>
                    <button onclick="loadPokeaOrders('next')" class="px-3 py-1 border border-gray-300 rounded text-xs hover:bg-gray-50">→</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Order Detail Modal -->
<div id="pokea-order-modal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-lg shadow-lg w-full max-w-2xl mx-auto z-50 max-h-[90vh] overflow-y-auto">
        <div class="p-4 border-b border-gray-200 sticky top-0 bg-white z-10 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-clipboard-list text-emerald-600 mr-2"></i>
                Order Details
            </h3>
            <button onclick="closePokeaModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div id="pokea-modal-body" class="p-4">
            <!-- Order details will be loaded here -->
        </div>
    </div>
</div>

<script>
// ============================================
// POKEA ORDER - JavaScript Functions
// ============================================

let pokeaOrders = [];
let pokeaCurrentFilter = 'all';
let pokeaCurrentPage = 1;
let pokeaPerPage = 20;
let pokeaSelectedOrder = null;

// ===== LOAD ORDERS =====
function loadPokeaOrders() {
    const tbody = document.getElementById('pokea-orders-tbody');
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
        if (data.success) {
            pokeaOrders = data.data;
            displayPokeaOrders(pokeaOrders);
            updatePokeaStats(pokeaOrders);
        } else {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center text-red-500">
                        <i class="fas fa-exclamation-triangle text-3xl mb-2"></i>
                        <p>${data.message || 'Failed to load orders'}</p>
                    </td>
                </tr>
            `;
        }
    })
    .catch(error => {
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
                <td class="px-4 py-2 font-medium text-gray-800">${order.order_number}</td>
                <td class="px-4 py-2">
                    <div class="font-medium text-gray-800">${order.customer_name || 'Walk-in'}</div>
                    <div class="text-xs text-gray-500">${order.order_type || 'delivery'}</div>
                </td>
                <td class="px-4 py-2 text-sm">${order.customer_phone || '-'}</td>
                <td class="px-4 py-2 text-center">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-700">
                        ${itemCount} items
                    </span>
                </td>
                <td class="px-4 py-2 text-right font-semibold text-emerald-600">
                    ${formatCurrency(order.total)}
                </td>
                <td class="px-4 py-2 text-center">
                    <span class="status-badge ${statusBadge.class}">
                        ${statusBadge.icon} ${statusBadge.label}
                    </span>
                </td>
                <td class="px-4 py-2 text-center text-xs text-gray-500">${createdDate}</td>
                <td class="px-4 py-2 text-center">
                    <div class="flex items-center justify-center gap-1">
                        <button onclick="event.stopPropagation(); viewPokeaOrder('${order.id}')" 
                                class="text-blue-600 hover:text-blue-800 p-1" title="View">
                            <i class="fas fa-eye text-sm"></i>
                        </button>
                        ${order.status === 'saved' ? `
                        <button onclick="event.stopPropagation(); confirmPokeaOrder('${order.id}')" 
                                class="text-green-600 hover:text-green-800 p-1" title="Confirm">
                            <i class="fas fa-check-circle text-sm"></i>
                        </button>
                        <button onclick="event.stopPropagation(); cancelPokeaOrder('${order.id}')" 
                                class="text-red-600 hover:text-red-800 p-1" title="Cancel">
                            <i class="fas fa-times-circle text-sm"></i>
                        </button>
                        ` : ''}
                        ${order.status === 'confirmed' ? `
                        <button onclick="event.stopPropagation(); markPokeaOrderPaid('${order.id}')" 
                                class="text-emerald-600 hover:text-emerald-800 p-1" title="Mark as Paid">
                            <i class="fas fa-money-bill-wave text-sm"></i>
                        </button>
                        ` : ''}
                        <button onclick="event.stopPropagation(); sharePokeaOrderWhatsApp('${order.id}')" 
                                class="text-[#25D366] hover:text-[#1DA851] p-1" title="Share via WhatsApp">
                            <i class="fab fa-whatsapp text-sm"></i>
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
        'saved': { label: 'Inasubiri', class: 'bg-yellow-100 text-yellow-800', icon: '<i class="fas fa-clock mr-1"></i>' },
        'confirmed': { label: 'Imethibitishwa', class: 'bg-blue-100 text-blue-800', icon: '<i class="fas fa-check-circle mr-1"></i>' },
        'paid': { label: 'Imelipwa', class: 'bg-green-100 text-green-800', icon: '<i class="fas fa-money-bill-wave mr-1"></i>' },
        'cancelled': { label: 'Imefutwa', class: 'bg-red-100 text-red-800', icon: '<i class="fas fa-times-circle mr-1"></i>' }
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
        showing.textContent = `Showing ${pokeaOrders.length} orders`;
        pageInfo.textContent = `Page ${pokeaCurrentPage}`;
    } else {
        pagination.classList.add('hidden');
    }
}

// ===== VIEW ORDER DETAIL =====
function viewPokeaOrder(orderId) {
    const order = pokeaOrders.find(o => o.id == orderId);
    if (!order) {
        showNotification('Order not found', 'error');
        return;
    }

    pokeaSelectedOrder = order;
    const modalBody = document.getElementById('pokea-modal-body');
    
    // Get items from order
    const items = order.items || [];
    let itemsHtml = '';
    let subtotal = 0;
    
    if (items.length > 0) {
        items.forEach((item, index) => {
            // Handle different item formats
            const itemName = item.jina || item.name || 'Bidhaa';
            const itemAina = item.aina || '';
            const itemKipimo = item.kipimo || '';
            const itemQty = item.idadi || item.qty || 0;
            const itemPrice = item.bei || item.price || 0;
            const itemTotal = item.total || (itemQty * itemPrice);
            subtotal += itemTotal;
            
            itemsHtml += `
                <div class="flex justify-between py-2 border-b border-gray-100 text-sm hover:bg-gray-50 px-1 rounded">
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
                    <p class="text-xs text-gray-500">Order Number</p>
                    <p class="font-semibold text-gray-800 text-sm">${order.order_number}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Status</p>
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
                    <i class="fas fa-user mr-2"></i> Customer Details
                </h4>
                <div class="grid grid-cols-2 gap-2 text-sm">
                    <div>
                        <span class="text-gray-500">Jina:</span>
                        <span class="font-medium">${order.customer_name || 'Walk-in'}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Simu:</span>
                        <span class="font-medium">${order.customer_phone || '-'}</span>
                    </div>
                    <div class="col-span-2">
                        <span class="text-gray-500">Anwani:</span>
                        <span class="font-medium">${order.customer_address || order.delivery_address || '-'}</span>
                    </div>
                    ${order.special_instructions ? `
                    <div class="col-span-2">
                        <span class="text-gray-500">Maelekezo:</span>
                        <span class="font-medium">${order.special_instructions}</span>
                    </div>
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
                    <span class="text-gray-500">Subtotal</span>
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
                    <span class="text-gray-500">Delivery Fee</span>
                    <span>${formatCurrency(order.delivery_fee)}</span>
                </div>
                ` : ''}
                <div class="flex justify-between text-lg font-bold border-t border-gray-200 pt-2 mt-2">
                    <span>Jumla</span>
                    <span class="text-emerald-600">${formatCurrency(total)}</span>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-wrap gap-2 pt-2 border-t border-gray-200">
                ${order.status === 'saved' ? `
                <button onclick="confirmPokeaOrder('${order.id}')" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium transition">
                    <i class="fas fa-check-circle mr-1"></i> Thibitisha
                </button>
                <button onclick="markPokeaOrderPaid('${order.id}')" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium transition">
                    <i class="fas fa-money-bill-wave mr-1"></i> Lipa
                </button>
                ` : ''}
                ${order.status === 'confirmed' ? `
                <button onclick="markPokeaOrderPaid('${order.id}')" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium transition">
                    <i class="fas fa-money-bill-wave mr-1"></i> Lipa
                </button>
                ` : ''}
                ${order.status !== 'cancelled' ? `
                <button onclick="cancelPokeaOrder('${order.id}')" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-medium transition">
                    <i class="fas fa-times-circle mr-1"></i> Ghairi
                </button>
                ` : ''}
                <button onclick="sharePokeaOrderWhatsApp('${order.id}')" class="px-4 py-2 bg-[#25D366] text-white rounded-lg hover:bg-[#1DA851] text-sm font-medium transition">
                    <i class="fab fa-whatsapp mr-1"></i> WhatsApp
                </button>
                <button onclick="printPokeaOrder('${order.id}')" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-sm font-medium transition">
                    <i class="fas fa-print mr-1"></i> Chapisha
                </button>
                <button onclick="closePokeaModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm font-medium transition">
                    <i class="fas fa-times mr-1"></i> Funga
                </button>
            </div>
        </div>
    `;

    document.getElementById('pokea-order-modal').classList.remove('hidden');
}

// ===== CONFIRM ORDER =====
function confirmPokeaOrder(orderId) {
    if (!confirm('Thibitisha order hii?')) return;
    
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
            showNotification(data.message || 'Failed to confirm order', 'error');
        }
    })
    .catch(error => {
        showNotification('Hitilafu ya mtandao', 'error');
        console.error('Error:', error);
    });
}

// ===== MARK ORDER AS PAID =====
function markPokeaOrderPaid(orderId) {
    if (!confirm('Lipa order hii?')) return;
    
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
            showNotification('Order imelipwa kikamilifu!', 'success');
            closePokeaModal();
            loadPokeaOrders();
        } else {
            showNotification(data.message || 'Failed to process payment', 'error');
        }
    })
    .catch(error => {
        showNotification('Hitilafu ya mtandao', 'error');
        console.error('Error:', error);
    });
}

// ===== CANCEL ORDER =====
function cancelPokeaOrder(orderId) {
    if (!confirm('Ghairi order hii?')) return;
    
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
            showNotification(data.message || 'Failed to cancel order', 'error');
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
        showNotification('Order not found', 'error');
        return;
    }

    const companyName = document.querySelector('meta[name="company-name"]')?.content || 'Our Shop';
    const items = order.items || [];
    
    let message = `🏪 *${companyName}*\n`;
    message += `━━━━━━━━━━━━━━━━━━━━━━━━\n`;
    message += `*ORDER DETAILS*\n`;
    message += `Order: ${order.order_number}\n`;
    message += `Date: ${new Date(order.created_at).toLocaleString()}\n`;
    message += `Customer: ${order.customer_name || 'Walk-in Customer'}\n`;
    if (order.customer_phone) {
        message += `Phone: ${order.customer_phone}\n`;
    }
    message += `Status: ${order.status === 'paid' ? '✅ Paid' : order.status === 'cancelled' ? '❌ Cancelled' : '⏳ Pending'}\n`;
    message += `━━━━━━━━━━━━━━━━━━━━━━━━\n`;
    message += `*ITEMS*\n`;
    
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
        message += `Subtotal: ${formatCurrency(order.subtotal || total + order.discount)}\n`;
        message += `Discount: -${formatCurrency(order.discount)}\n`;
    }
    message += `*TOTAL: ${formatCurrency(total)}*\n`;
    message += `━━━━━━━━━━━━━━━━━━━━━━━━\n`;
    message += `Asante kwa kununua! 🛍️`;
    
    const encodedMessage = encodeURIComponent(message);
    
    // Get customer phone
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
        showNotification('Order not found', 'error');
        return;
    }

    const companyName = document.querySelector('meta[name="company-name"]')?.content || 'Our Shop';
    const items = order.items || [];
    const total = order.total || 0;
    const subtotal = order.subtotal || total;
    
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
            <title>Order #${order.order_number}</title>
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
                <p>Order Receipt</p>
                <p>${order.order_number}</p>
            </div>
            
            <div class="divider"></div>
            
            <div class="info"><strong>Date:</strong> ${new Date(order.created_at).toLocaleString()}</div>
            <div class="info"><strong>Customer:</strong> ${order.customer_name || 'Walk-in'}</div>
            <div class="info"><strong>Phone:</strong> ${order.customer_phone || '-'}</div>
            <div class="info"><strong>Status:</strong> <span class="status status-${order.status}">${getStatusBadge(order.status).label}</span></div>
            ${order.delivery_address ? `<div class="info"><strong>Address:</strong> ${order.delivery_address}</div>` : ''}
            
            <div class="divider"></div>
            
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th style="text-align:center;">Qty</th>
                        <th style="text-align:right;">Price</th>
                        <th style="text-align:right;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    ${itemsHtml}
                </tbody>
            </table>
            
            <div class="total">
                <div style="display:flex;justify-content:space-between;font-size:14px;">
                    <span>Total:</span>
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
    showNotification('Orders refreshed!', 'info');
}

// ===== FORMAT CURRENCY =====
function formatCurrency(amount) {
    return amount.toLocaleString('en-TZ', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) + ' TZS';
}

// ===== SHOW NOTIFICATION =====
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    const bgColor = type === 'success' ? 'bg-emerald-500' : 
                    type === 'error' ? 'bg-red-500' : 
                    type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500';
    notification.className = `fixed top-4 right-4 z-50 px-4 py-2 rounded-lg shadow-lg text-white ${bgColor} text-sm max-w-sm`;
    notification.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} mr-2"></i>${message}`;
    document.body.appendChild(notification);
    setTimeout(() => { 
        notification.style.opacity = '0'; 
        notification.style.transition = 'opacity 0.3s ease';
        setTimeout(() => notification.remove(), 300); 
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
    
    // Load orders when tab is shown
    const observer = new MutationObserver(function() {
        const tabContent = document.getElementById('pokeaorder-tab-content');
        if (tabContent && !tabContent.classList.contains('hidden')) {
            loadPokeaOrders();
        }
    });
    
    // Observe the tab content for class changes
    const target = document.getElementById('pokeaorder-tab-content');
    if (target) {
        observer.observe(target, { attributes: true, attributeFilter: ['class'] });
    }
});

// ===== LOAD ORDERS ON FIRST VISIT =====
document.addEventListener('DOMContentLoaded', function() {
    const tabContent = document.getElementById('pokeaorder-tab-content');
    if (tabContent && !tabContent.classList.contains('hidden')) {
        loadPokeaOrders();
    }
});
</script>

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

/* Status Badges */
.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 2px 10px;
    border-radius: 9999px;
    font-size: 11px;
    font-weight: 500;
}

/* Table row hover */
#pokea-orders-tbody tr:hover {
    background-color: #f9fafb;
    cursor: pointer;
}

/* Filter buttons */
.filter-btn {
    transition: all 0.2s ease;
}

.filter-btn:hover {
    transform: translateY(-1px);
}

/* Scroll for items */
#pokea-modal-body .max-h-60 {
    max-height: 250px;
    overflow-y: auto;
}

/* Responsive */
@media (max-width: 640px) {
    #pokea-orders-tbody td {
        padding: 6px 8px;
        font-size: 12px;
    }
    
    .modal-content {
        margin: 8px;
        padding: 12px;
    }
}
</style>