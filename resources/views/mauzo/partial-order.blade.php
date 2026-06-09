<!-- resources/views/mauzo/partial-order.blade.php -->
<style>
.order-stat-card {
    transition: all 0.2s ease;
    cursor: pointer;
}
.order-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}
.order-status-badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 10px;
    border-radius: 9999px;
    font-size: 11px;
    font-weight: 600;
}

/* Modal Styling matching Masaplaya */
.modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
}
/* Pagination Styles */
#orderPagination .orderPageBtn {
    transition: all 0.2s ease;
    min-width: 36px;
}

#orderPagination .orderPageBtn:hover {
    background-color: #f3f4f6;
    transform: translateY(-1px);
}

#orderPagination .orderPageBtn:active {
    transform: translateY(0);
}

/* Mobile responsive pagination */
@media (max-width: 640px) {
    #orderPagination .orderPageBtn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    
    #orderPagination .flex {
        gap: 0.25rem;
    }
}
.modal-content {
    background: white;
    border-radius: 12px;
    width: 50%;
    max-width: 900px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}
.modal-content::-webkit-scrollbar {
    width: 4px;
}
.modal-content::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}
.modal-content::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}
@media (max-width: 640px) {
    .modal-content {
        width: 95%;
        margin: 10px;
    }
}
</style>

<!-- Orders Tab Content -->
<div id="weka-order-tab-content" class="tab-content hidden">
    <div class="space-y-4">
        <!-- Header -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
            <div class="flex justify-between items-center">
                <h2 class="text-sm font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-clipboard-list mr-2 text-amber-600"></i>
                    Usimamizi wa Oda
                </h2>
                <button id="orderCreateBtn" class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded text-sm font-medium transition flex items-center gap-2">
                    <i class="fas fa-plus"></i> Oda Mpya
                </button>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div class="bg-white p-3 rounded-lg border border-gray-200 shadow-sm order-stat-card" data-status="all">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Jumla ya Oda</p>
                        <p class="text-xl font-bold text-gray-800" id="orderStatTotal">0</p>
                    </div>
                    <i class="fas fa-clipboard-list text-gray-400 text-lg"></i>
                </div>
            </div>
            <div class="bg-white p-3 rounded-lg border border-amber-200 shadow-sm order-stat-card" data-status="saved">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-xs text-amber-600 mb-1">Zilizohifadhiwa</p>
                        <p class="text-xl font-bold text-amber-700" id="orderStatSaved">0</p>
                    </div>
                    <i class="fas fa-save text-amber-500 text-lg"></i>
                </div>
            </div>

            <div class="bg-white p-3 rounded-lg border border-green-200 shadow-sm order-stat-card" data-status="confirmed">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-xs text-green-600 mb-1">Zilizothibitishwa</p>
                        <p class="text-xl font-bold text-green-700" id="orderStatConfirmed">0</p>
                    </div>
                    <i class="fas fa-check-circle text-green-500 text-lg"></i>
                </div>
            </div>
        

 
            <div class="bg-white p-3 rounded-lg border border-orange-200 shadow-sm">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-xs text-orange-600 mb-1">Mapato Yanayosubiri</p>
                        <p class="text-lg font-bold text-orange-700" id="orderStatPending">0 TZS</p>
                    </div>
                    <i class="fas fa-clock text-orange-500 text-lg"></i>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white p-3 rounded-lg border border-gray-200 shadow-sm">
            <div class="flex flex-col sm:flex-row gap-2">
                <div class="flex-1 relative">
                    <input type="text" id="orderSearch" placeholder="Tafuta oda, mteja au simu..." 
                           class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                <div class="w-full sm:w-40">
                    <select id="orderStatusFilter" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-amber-500">
                        <option value="all">Hali zote</option>
                        <option value="saved">Zilizohifadhiwa</option>
                        <option value="confirmed">Zilizothibitishwa</option>
                        <option value="paid">Zilizolipwa</option>
                    </select>
                </div>
                <div class="w-full sm:w-36">
                    <input type="date" id="orderStartDate" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-amber-500">
                </div>
                <div class="w-full sm:w-36">
                    <input type="date" id="orderEndDate" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-amber-500">
                </div>
                <button id="orderResetFilters" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded text-sm font-medium transition flex items-center justify-center gap-1">
                    <i class="fas fa-redo-alt"></i> Safisha
                </button>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-amber-50">
                            <th class="px-4 py-2 text-left font-medium text-amber-800">Namba</th>
                            <th class="px-4 py-2 text-left font-medium text-amber-800">Mteja</th>
                            <th class="px-4 py-2 text-left font-medium text-amber-800 hidden sm:table-cell">Simu</th>
                            <th class="px-4 py-2 text-left font-medium text-amber-800 hidden md:table-cell">Tarehe</th>
                            <th class="px-4 py-2 text-right font-medium text-amber-800">Jumla</th>
                            <th class="px-4 py-2 text-center font-medium text-amber-800">Hali</th>
                            <th class="px-4 py-2 text-center font-medium text-amber-800 print:hidden">Vitendo</th>
                        </tr>
                    </thead>
                    <tbody id="orderTableBody" class="divide-y divide-gray-100">
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                <i class="fas fa-spinner fa-spin text-2xl mb-2 text-gray-300"></i>
                                <p>Inapakia...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div id="orderPagination" class="px-4 py-3 border-t border-gray-200"></div>
        </div>
    </div>
</div>

<!-- Order Modal (Create/Edit) -->
<div id="orderModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-800">Oda Mpya</h3>
        </div>
        <div class="p-4">
            <form id="orderForm">
                @csrf
                
                <!-- Customer Info -->
  <!-- Customer Info -->
<div class="bg-gray-50 p-3 rounded-lg border border-gray-200 mb-4">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">

        <!-- Customer Search -->
        <div class="relative">
            <label class="block text-xs font-medium text-gray-700 mb-1">
                Tafuta Mteja
            </label>
            <input
                type="text"
                id="orderCustomerSearch"
                placeholder="Tafuta kwa jina au simu..."
                class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-amber-500"
            >
            <i class="fas fa-search absolute left-3 top-8 text-gray-400 text-xs"></i>

            <div
                id="orderCustomerDropdown"
                class="hidden absolute z-10 w-full bg-white border rounded-lg shadow-lg max-h-48 overflow-y-auto"
            ></div>
        </div>

        <input type="hidden" id="orderCustomerId">

        <!-- Customer Name -->
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">
                Jina Kamili <span class="text-red-500">*</span>
            </label>
            <input
                type="text"
                id="orderCustomerName"
                class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-amber-500"
                required
            >
        </div>

        <!-- Phone -->
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">
                Namba ya Simu
            </label>
            <input
                type="text"
                id="orderCustomerPhone"
                class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-amber-500"
            >
        </div>

    </div>
</div>

                <!-- Products Section -->
                <div class="bg-gray-50 p-3 rounded-lg border border-gray-200 mb-4">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="text-xs font-semibold text-gray-700 flex items-center gap-1">
                            <i class="fas fa-boxes text-amber-600"></i> Bidhaa
                        </h3>
                        <button type="button" id="orderAddProduct" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded text-xs font-medium transition flex items-center gap-1">
                            <i class="fas fa-plus"></i> Ongeza
                        </button>
                    </div>
                    
                    <div id="orderProductsContainer" class="space-y-2">
                        <div class="orderProductRow bg-white p-2 rounded-lg border border-gray-200">
                            <div class="grid grid-cols-12 gap-2">
                                <div class="col-span-12 md:col-span-5">
                                    <input type="text" class="orderProductSearch w-full px-3 py-1.5 border border-gray-300 rounded text-sm" placeholder="Tafuta bidhaa...">
                                  <select class="orderProductSelect hidden absolute z-10 w-80 bg-white border border-gray-300 rounded-lg shadow-lg text-base font-medium px-3 py-2">
                                        <option value="">Chagua Bidhaa</option>
                                        @foreach($bidhaa as $item)
                                        <option value="{{ $item->id }}" data-price="{{ $item->bei_kuuza }}">{{ $item->jina }} - {{ number_format($item->bei_kuuza, 0) }} TZS</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-span-3 md:col-span-2">
                                    <input type="number" class="orderProductQty w-full px-2 py-1.5 border border-gray-300 rounded text-sm" placeholder="Idadi" min="0.01" step="0.01" value="1">
                                </div>
                                <div class="col-span-3 md:col-span-2">
                                    <input type="number" class="orderProductPrice w-full px-2 py-1.5 border border-gray-300 rounded text-sm bg-gray-100" readonly placeholder="Bei">
                                </div>
                                <div class="col-span-2 md:col-span-2">
                                    <input type="number" class="orderProductDiscount w-full px-2 py-1.5 border border-gray-300 rounded text-sm" placeholder="Punguzo" min="0" step="0.01" value="0">
                                </div>
                                <div class="col-span-1 text-center flex items-center justify-center">
                                    <button type="button" class="orderRemoveProduct text-red-500 hover:text-red-700 transition">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="flex justify-end mt-2 pt-1 border-t">
                                <span class="text-xs font-medium text-gray-600">Jumla:</span>
                                <span class="orderProductTotal text-xs font-bold text-green-700 ml-2">0 TZS</span>
                            </div>
                        </div>
                    </div>
                </div>

         <!-- Totals Section -->
<div class="bg-gray-50 p-3 rounded-lg border border-gray-200 mb-4">
    <div class="flex flex-wrap items-center justify-end gap-6 text-sm">

        <div class="flex items-center gap-2">
            <span class="text-gray-600">Jumla Ndogo:</span>
            <span id="orderSummarySubtotal" class="font-semibold text-gray-800">0 TZS</span>
        </div>

        <div class="flex items-center gap-2">
            <span class="text-gray-600">Punguzo la Jumla:</span>
            <input
                type="number"
                id="orderGlobalDiscount"
                class="w-24 border border-gray-300 rounded px-2 py-1 text-right text-sm focus:outline-none focus:ring-1 focus:ring-amber-500"
                value="0"
                step="0.01"
            >
        </div>

        <div class="flex items-center gap-2 border-l pl-4">
            <span class="font-bold text-gray-800">JUMLA KUU:</span>
            <span id="orderSummaryTotal" class="font-bold text-green-700 text-lg">0 TZS</span>
        </div>

    </div>
</div>

                <!-- Notes -->
                <div class="mb-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Maelezo</label>
                    <textarea id="orderNotes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-amber-500" placeholder="Maelezo ya ziada..."></textarea>
                </div> 

                <!-- Action Buttons -->
                <div class="flex gap-2 pt-4 border-t border-gray-200">
                    <button type="button" id="orderCancelBtn" class="flex-1 px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 text-sm font-medium transition">
                        Ghairi
                    </button>
                    <button type="button" id="orderSaveDraft" class="flex-1 px-4 py-2 bg-amber-600 text-white rounded hover:bg-amber-700 text-sm font-medium transition flex items-center justify-center gap-1">
                        <i class="fas fa-save"></i> Hifadhi
                    </button>
                    <button type="submit" id="orderConfirmSubmit" class="flex-1 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm font-medium transition flex items-center justify-center gap-1">
                        <i class="fas fa-check-circle"></i> Thibitisha
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Order Details Modal -->
<div id="orderDetailsModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-800">Maelezo ya Oda</h3>
        </div>
        <div class="p-4" id="orderDetailsContent"></div>
        <div class="flex gap-2 p-4 border-t border-gray-200">
            <button id="orderPrint" class="flex-1 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm font-medium transition flex items-center justify-center gap-1">
                <i class="fas fa-print"></i> Chapisha
            </button>
            <button id="orderWhatsApp" class="flex-1 px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 text-sm font-medium transition flex items-center justify-center gap-1">
                <i class="fab fa-whatsapp"></i> WhatsApp
            </button>
            <button id="orderCopy" class="flex-1 px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 text-sm font-medium transition flex items-center justify-center gap-1">
                <i class="fas fa-copy"></i> Nakili
            </button>
            <button id="orderCloseDetails" class="flex-1 px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500 text-sm font-medium transition">
                Funga
            </button>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="orderDeleteModal" class="modal" style="display: none;">
    <div class="modal-content bg-white rounded-lg shadow-lg w-full max-w-sm mx-2">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-800">Thibitisha Kufuta</h3>
        </div>
        <div class="p-4 text-center">
            <i class="fas fa-exclamation-triangle text-amber-500 text-2xl mb-2"></i>
            <p class="text-gray-700 text-sm mb-2" id="orderDeleteMessage">Una uhakika unataka kufuta oda hii?</p>
            <p class="text-gray-500 text-xs">Hatua hii haiwezi kutenduliwa</p>
        </div>
        <div class="flex gap-2 p-4 border-t border-gray-200">
            <button id="orderCancelDelete" class="flex-1 px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 text-sm font-medium">
                Ghairi
            </button>
            <button id="orderConfirmDelete" class="flex-1 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm font-medium">
                Futa
            </button>
        </div>
    </div>
</div>
<script>
// Orders Module
(function() {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initOrders);
    } else {
        initOrders();
    }
    
    function initOrders() {
        let currentOrderId = null;
        let deleteOrderId = null;
        let customers = [];
        
        try {
            customers = @json($wateja);
        } catch(e) { 
            customers = []; 
        }
        
        // Helper Functions
        function showToast(msg, type = 'success') {
            let container = document.getElementById('notification-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'notification-container';
                container.className = 'fixed top-4 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-sm px-4 pointer-events-none';
                document.body.appendChild(container);
            }
            
            const colors = {
                success: 'bg-emerald-50 border-emerald-200 text-emerald-800',
                error: 'bg-red-50 border-red-200 text-red-800',
                warning: 'bg-amber-50 border-amber-200 text-amber-800',
                info: 'bg-blue-50 border-blue-200 text-blue-800'
            };
            
            const notification = document.createElement('div');
            notification.className = `rounded-lg border px-4 py-3 text-sm font-medium mb-2 shadow-lg transition-all duration-300`;
            notification.classList.add(...colors[type].split(' '));
            notification.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'} mr-2"></i>${msg}`;
            
            container.appendChild(notification);
            
            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transform = 'translateY(-10px)';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }
        
        function formatNumber(n) { 
            return parseFloat(n || 0).toLocaleString(undefined, {minimumFractionDigits: 0}); 
        }
        
        function escapeHtml(s) { 
            if (!s) return ''; 
            return s.replace(/[&<>]/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;'})[m]); 
        }
        
        function calculateItemTotal(row) {
            const qty = parseFloat(row.querySelector('.orderProductQty')?.value) || 0;
            const price = parseFloat(row.querySelector('.orderProductPrice')?.value) || 0;
            const discount = parseFloat(row.querySelector('.orderProductDiscount')?.value) || 0;
            let total = (qty * price) - discount;
            if (total < 0) total = 0;
            
            const totalSpan = row.querySelector('.orderProductTotal');
            if (totalSpan) totalSpan.textContent = formatNumber(total) + ' TZS';
            return total;
        }
        
        function updateRowTotal(row) {
            calculateItemTotal(row);
            calculateOrderTotal();
        }
        
        function calculateOrderTotal() {
            let subtotal = 0;
            document.querySelectorAll('.orderProductRow').forEach(row => {
                const qty = parseFloat(row.querySelector('.orderProductQty')?.value) || 0;
                const price = parseFloat(row.querySelector('.orderProductPrice')?.value) || 0;
                const discount = parseFloat(row.querySelector('.orderProductDiscount')?.value) || 0;
                subtotal += (qty * price) - discount;
            });
            
            const globalDisc = parseFloat(document.getElementById('orderGlobalDiscount')?.value) || 0;
            let total = subtotal - globalDisc;
            if (total < 0) total = 0;
            
            const subtotalEl = document.getElementById('orderSummarySubtotal');
            const totalEl = document.getElementById('orderSummaryTotal');
            if (subtotalEl) subtotalEl.textContent = formatNumber(subtotal) + ' TZS';
            if (totalEl) totalEl.textContent = formatNumber(total) + ' TZS';
        }
        
        function initProductSearch(row) {
            const searchInput = row.querySelector('.orderProductSearch');
            const selectDropdown = row.querySelector('.orderProductSelect');
            const priceField = row.querySelector('.orderProductPrice');
            const qtyField = row.querySelector('.orderProductQty');
            const discountField = row.querySelector('.orderProductDiscount');
            
            if (!searchInput || !selectDropdown) return;
            
            const filterOptions = (searchText) => {
                const filter = searchText.toLowerCase();
                Array.from(selectDropdown.options).forEach((opt, i) => {
                    if (i > 0) {
                        opt.style.display = opt.textContent.toLowerCase().includes(filter) ? '' : 'none';
                    }
                });
            };
            
            const selectProduct = (option) => {
                if (!option.value) return;
                const price = parseFloat(option.dataset.price || 0);
                if (priceField) priceField.value = price.toFixed(2);
                if (searchInput) searchInput.value = option.textContent.split(' - ')[0];
                if (selectDropdown) selectDropdown.classList.add('hidden');
                if (qtyField && !qtyField.value) qtyField.value = '1';
                calculateItemTotal(row);
                calculateOrderTotal();
            };
            
            if (searchInput) {
                searchInput.addEventListener('focus', () => { 
                    if (selectDropdown) selectDropdown.classList.remove('hidden'); 
                    filterOptions(searchInput.value); 
                });
                searchInput.addEventListener('input', (e) => { 
                    filterOptions(e.target.value); 
                    if (selectDropdown) selectDropdown.classList.remove('hidden'); 
                });
            }
            
            if (selectDropdown) {
                selectDropdown.addEventListener('change', () => { 
                    const opt = selectDropdown.options[selectDropdown.selectedIndex]; 
                    if (opt?.value) selectProduct(opt); 
                });
            }
            
            if (qtyField) {
                qtyField.addEventListener('input', () => updateRowTotal(row));
                qtyField.addEventListener('blur', () => {
                    if (!qtyField.value || parseFloat(qtyField.value) <= 0) {
                        qtyField.value = '1';
                        updateRowTotal(row);
                    }
                });
            }
            
            if (discountField) {
                discountField.addEventListener('input', () => updateRowTotal(row));
            }
            
            document.addEventListener('click', (e) => { 
                if (searchInput && selectDropdown && !searchInput.contains(e.target) && !selectDropdown.contains(e.target)) 
                    selectDropdown.classList.add('hidden'); 
            });
        }
        
        function addProductRow() {
            const container = document.getElementById('orderProductsContainer');
            if (!container) return;
            const template = container.children[0];
            const newRow = template.cloneNode(true);
            
            const searchInput = newRow.querySelector('.orderProductSearch');
            const selectDropdown = newRow.querySelector('.orderProductSelect');
            const qtyField = newRow.querySelector('.orderProductQty');
            const priceField = newRow.querySelector('.orderProductPrice');
            const discountField = newRow.querySelector('.orderProductDiscount');
            const totalSpan = newRow.querySelector('.orderProductTotal');
            
            if (searchInput) searchInput.value = '';
            if (selectDropdown) selectDropdown.value = '';
            if (qtyField) qtyField.value = '1';
            if (priceField) priceField.value = '';
            if (discountField) discountField.value = '0';
            if (totalSpan) totalSpan.textContent = '0 TZS';
            
            container.appendChild(newRow);
            initProductSearch(newRow);
            
            const removeBtn = newRow.querySelector('.orderRemoveProduct');
            if (removeBtn) {
                removeBtn.addEventListener('click', () => {
                    if (container.children.length > 1) {
                        newRow.remove();
                        calculateOrderTotal();
                    } else {
                        showToast('Angalau bidhaa moja inahitajika!', 'error');
                    }
                });
            }
        }
        
        function initCustomerSearch() {
            const searchInput = document.getElementById('orderCustomerSearch');
            const dropdown = document.getElementById('orderCustomerDropdown');
            if (!searchInput || !dropdown) return;
            
            searchInput.addEventListener('input', function() {
                const search = this.value.toLowerCase();
                if (search.length < 1) { 
                    dropdown.classList.add('hidden'); 
                    return; 
                }
                const filtered = customers.filter(c => 
                    c.jina.toLowerCase().includes(search) || 
                    (c.simu && c.simu.toLowerCase().includes(search))
                );
                if (filtered.length > 0) {
                    dropdown.innerHTML = filtered.map(c => `
                        <div class="orderCustomerOption p-2 hover:bg-gray-100 cursor-pointer border-b" 
                             data-id="${c.id}" 
                             data-name="${escapeHtml(c.jina)}" 
                             data-phone="${c.simu || ''}">
                            <div class="font-semibold text-sm">${escapeHtml(c.jina)}</div>
                            <div class="text-xs text-gray-500">${c.simu || 'Hakuna simu'}</div>
                        </div>
                    `).join('');
                    dropdown.classList.remove('hidden');
                    document.querySelectorAll('.orderCustomerOption').forEach(opt => 
                        opt.addEventListener('click', function() {
                            const idField = document.getElementById('orderCustomerId');
                            const nameField = document.getElementById('orderCustomerName');
                            const phoneField = document.getElementById('orderCustomerPhone');
                            if (idField) idField.value = this.dataset.id;
                            if (nameField) nameField.value = this.dataset.name;
                            if (phoneField) phoneField.value = this.dataset.phone;
                            searchInput.value = this.dataset.name;
                            dropdown.classList.add('hidden');
                        })
                    );
                } else {
                    dropdown.classList.add('hidden');
                }
            });
            document.addEventListener('click', (e) => { 
                if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) 
                    dropdown.classList.add('hidden'); 
            });
        }
        
        function resetOrderForm() {
            const customerId = document.getElementById('orderCustomerId');
            const customerSearch = document.getElementById('orderCustomerSearch');
            const customerName = document.getElementById('orderCustomerName');
            const customerPhone = document.getElementById('orderCustomerPhone');
            const notes = document.getElementById('orderNotes');
            const globalDiscount = document.getElementById('orderGlobalDiscount');
            
            if (customerId) customerId.value = '';
            if (customerSearch) customerSearch.value = '';
            if (customerName) customerName.value = '';
            if (customerPhone) customerPhone.value = '';
            if (notes) notes.value = '';
            if (globalDiscount) globalDiscount.value = '0';
            
            const container = document.getElementById('orderProductsContainer');
            if (container) {
                while (container.children.length > 1) container.removeChild(container.lastChild);
                const firstRow = container.children[0];
                const searchInput = firstRow?.querySelector('.orderProductSearch');
                const selectDropdown = firstRow?.querySelector('.orderProductSelect');
                const qtyField = firstRow?.querySelector('.orderProductQty');
                const priceField = firstRow?.querySelector('.orderProductPrice');
                const discountField = firstRow?.querySelector('.orderProductDiscount');
                const totalSpan = firstRow?.querySelector('.orderProductTotal');
                
                if (searchInput) searchInput.value = '';
                if (selectDropdown) selectDropdown.value = '';
                if (qtyField) qtyField.value = '1';
                if (priceField) priceField.value = '';
                if (discountField) discountField.value = '0';
                if (totalSpan) totalSpan.textContent = '0 TZS';
            }
            calculateOrderTotal();
        }
        
        async function saveOrder(status) {
            const items = [];
            let hasError = false;
            
            document.querySelectorAll('.orderProductRow').forEach((row, idx) => {
                const select = row.querySelector('.orderProductSelect');
                const qty = row.querySelector('.orderProductQty')?.value;
                const price = row.querySelector('.orderProductPrice')?.value;
                const discount = row.querySelector('.orderProductDiscount')?.value || 0;
                
                if (!select?.value) { 
                    showToast(`Tafadhali chagua bidhaa kwenye safu ${idx + 1}`, 'error'); 
                    hasError = true; 
                    return; 
                }
                if (!qty || parseFloat(qty) <= 0) { 
                    showToast(`Ingiza idadi sahihi kwenye safu ${idx + 1}`, 'error'); 
                    hasError = true; 
                    return; 
                }
                
                items.push({ 
                    bidhaa_id: select.value, 
                    idadi: parseFloat(qty), 
                    bei: parseFloat(price), 
                    punguzo: parseFloat(discount) 
                });
            });
            
            if (hasError || items.length === 0) return;
            
            const customerName = document.getElementById('orderCustomerName')?.value;
            if (!customerName) { 
                showToast('Tafadhali ingiza jina la mteja!', 'error'); 
                return; 
            }
            
            const formData = {
                items, 
                status,
                customer_id: document.getElementById('orderCustomerId')?.value || null,
                customer_name: customerName,
                customer_phone: document.getElementById('orderCustomerPhone')?.value,
                discount: parseFloat(document.getElementById('orderGlobalDiscount')?.value) || 0,
                notes: document.getElementById('orderNotes')?.value
            };
            
            try {
                const response = await fetch('/orders', {
                    method: 'POST', 
                    headers: { 
                        'Content-Type': 'application/json', 
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content 
                    },
                    body: JSON.stringify(formData)
                });
                const data = await response.json();
                if (data.success) { 
                    showToast(data.message, 'success'); 
                    const modal = document.getElementById('orderModal');
                    if (modal) modal.style.display = 'none'; 
                    resetOrderForm(); 
                    loadOrders(); 
                } else {
                    showToast(data.message || 'Kuna tatizo!', 'error');
                }
            } catch (error) { 
                console.error('Save order error:', error);
                showToast('Kuna tatizo katika kuhifadhi oda!', 'error'); 
            }
        }
        
        async function loadOrders(page = 1) {
            const status = document.getElementById('orderStatusFilter')?.value || 'all';
            const search = document.getElementById('orderSearch')?.value || '';
            const startDate = document.getElementById('orderStartDate')?.value || '';
            const endDate = document.getElementById('orderEndDate')?.value || '';
            
            // Show loading state
            const tableBody = document.getElementById('orderTableBody');
            if (tableBody) {
                tableBody.innerHTML = '<tr><td colspan="7" class="px-4 py-8 text-center text-gray-500"><i class="fas fa-spinner fa-spin text-2xl mb-2 text-gray-300"></i><p>Inapakia...</p></td></tr>';
            }
            
            try {
                const params = new URLSearchParams({ status, search, start_date: startDate, end_date: endDate, page });
                const response = await fetch(`/orders?${params.toString()}`);
                const data = await response.json();
                
                if (data.success) { 
                    // Handle Laravel pagination structure
                    let orders = [];
                    let paginationData = null;
                    
                    if (data.orders && data.orders.data) {
                        orders = data.orders.data;
                        paginationData = {
                            current_page: data.orders.current_page,
                            last_page: data.orders.last_page,
                            total: data.orders.total,
                            from: data.orders.from,
                            to: data.orders.to,
                            per_page: data.orders.per_page
                        };
                    } else if (Array.isArray(data.orders)) {
                        orders = data.orders;
                    }
                    
                    renderOrders(orders); 
                    updateStats(data.stats); 
                    renderPagination(paginationData); 
                } else {
                    showToast(data.message || 'Kuna tatizo!', 'error');
                }
            } catch (error) { 
                console.error('Load orders error:', error);
                showToast('Kuna tatizo katika kupakia oda!', 'error'); 
                if (tableBody) {
                    tableBody.innerHTML = '<tr><td colspan="7" class="px-4 py-8 text-center text-red-500"><i class="fas fa-exclamation-triangle text-2xl mb-2"></i><p>Hitilafu katika kupakia data!</p></td></tr>';
                }
            }
        }
        
        function renderOrders(orders) {
            const tableBody = document.getElementById('orderTableBody');
            if (!tableBody) return;
            
            if (!orders || orders.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="7" class="px-4 py-8 text-center text-gray-500"><i class="fas fa-clipboard-list text-2xl mb-2 text-gray-300"></i><p>Hakuna oda zilizopatikana</p></td></tr>';
                return;
            }
            
            tableBody.innerHTML = '';
            orders.forEach(order => {
                const statusConfig = getStatusConfig(order.status);
                const row = document.createElement('tr');
                row.className = 'hover:bg-gray-50 transition';
                row.innerHTML = `
                    <td class="px-4 py-2 font-mono text-xs">${escapeHtml(order.order_number)}</td>
                    <td class="px-4 py-2">${escapeHtml(order.customer_name || '-')}</td>
                    <td class="px-4 py-2 hidden sm:table-cell">${escapeHtml(order.customer_phone || '-')}</td>
                    <td class="px-4 py-2 hidden md:table-cell">${new Date(order.created_at).toLocaleDateString()}</td>
                    <td class="px-4 py-2 text-right font-semibold">${formatNumber(order.total)} TZS</td>
                    <td class="px-4 py-2 text-center">
                        <span class="order-status-badge ${statusConfig.bg} ${statusConfig.text}">
                            <i class="fas ${statusConfig.icon} mr-1"></i>${statusConfig.label}
                        </span>
                    </td>
                    <td class="px-4 py-2 text-center print:hidden">
                        <div class="flex justify-center space-x-2">
                            <button class="orderViewBtn text-blue-600 hover:text-blue-800 transition" data-id="${order.id}" title="Angalia">
                                <i class="fas fa-eye"></i>
                            </button>
                            ${order.status === 'saved' ? `
                            <button class="orderConfirmBtn text-green-600 hover:text-green-800 transition" data-id="${order.id}" title="Thibitisha">
                                <i class="fas fa-check-circle"></i>
                            </button>` : ''}
                            ${order.status === 'confirmed' ? `
                            <button class="orderSendToCart text-amber-600 hover:text-amber-800 transition" data-id="${order.id}" title="Tuma Kikapu">
                                <i class="fas fa-shopping-cart"></i>
                            </button>` : ''}
                            <button class="orderDeleteBtn text-red-600 hover:text-red-800 transition" data-id="${order.id}" data-name="${escapeHtml(order.customer_name)}" title="Futa">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                `;
                tableBody.appendChild(row);
            });
            
            // Attach event listeners
            document.querySelectorAll('.orderViewBtn').forEach(btn => btn.addEventListener('click', () => viewOrderDetails(btn.dataset.id)));
            document.querySelectorAll('.orderConfirmBtn').forEach(btn => btn.addEventListener('click', () => updateOrderStatus(btn.dataset.id, 'confirmed')));
            document.querySelectorAll('.orderSendToCart').forEach(btn => btn.addEventListener('click', () => sendToCart(btn.dataset.id)));
            document.querySelectorAll('.orderDeleteBtn').forEach(btn => btn.addEventListener('click', () => showDeleteConfirm(btn.dataset.id, btn.dataset.name)));
        }
        
        function getStatusConfig(status) {
            const configs = {
                saved: { label: 'Imehifadhiwa', bg: 'bg-amber-100', text: 'text-amber-800', icon: 'fa-save' },
                confirmed: { label: 'Imethibitishwa', bg: 'bg-green-100', text: 'text-green-800', icon: 'fa-check-circle' },
                paid: { label: 'Imelipwa', bg: 'bg-blue-100', text: 'text-blue-800', icon: 'fa-money-bill-wave' }
            };
            return configs[status] || configs.saved;
        }
        
        function updateStats(stats) {
            if (stats) {
                const total = (stats.saved || 0) + (stats.confirmed || 0) + (stats.paid || 0);
                const statTotal = document.getElementById('orderStatTotal');
                const statSaved = document.getElementById('orderStatSaved');
                const statConfirmed = document.getElementById('orderStatConfirmed');
                const statPending = document.getElementById('orderStatPending');
                
                if (statTotal) statTotal.textContent = total;
                if (statSaved) statSaved.textContent = stats.saved || 0;
                if (statConfirmed) statConfirmed.textContent = stats.confirmed || 0;
                if (statPending) statPending.textContent = formatNumber(stats.pending_revenue || 0) + ' TZS';
            }
        }
        
        function renderPagination(pagination) {
            const container = document.getElementById('orderPagination');
            if (!container) return;
            
            if (!pagination || !pagination.last_page || pagination.last_page <= 1) {
                container.innerHTML = '';
                return;
            }
            
            let html = '<div class="flex justify-center space-x-1 flex-wrap gap-1">';
            
            // Previous button
            if (pagination.current_page > 1) {
                html += `<button class="orderPageBtn px-3 py-1 border rounded-lg hover:bg-gray-50 text-sm transition" data-page="${pagination.current_page - 1}">
                            <i class="fas fa-chevron-left"></i> Nyuma
                        </button>`;
            }
            
            // First page
            if (pagination.current_page > 3) {
                html += `<button class="orderPageBtn px-3 py-1 border rounded-lg hover:bg-gray-50 text-sm transition" data-page="1">1</button>`;
                if (pagination.current_page > 4) {
                    html += `<span class="px-2 py-1 text-gray-500">...</span>`;
                }
            }
            
            // Page numbers around current page
            for (let i = Math.max(1, pagination.current_page - 2); i <= Math.min(pagination.last_page, pagination.current_page + 2); i++) {
                if (i === pagination.current_page) {
                    html += `<span class="px-3 py-1 rounded-lg bg-amber-600 text-white text-sm font-semibold">${i}</span>`;
                } else {
                    html += `<button class="orderPageBtn px-3 py-1 border rounded-lg hover:bg-gray-50 text-sm transition" data-page="${i}">${i}</button>`;
                }
            }
            
            // Last page
            if (pagination.current_page < pagination.last_page - 2) {
                if (pagination.current_page < pagination.last_page - 3) {
                    html += `<span class="px-2 py-1 text-gray-500">...</span>`;
                }
                html += `<button class="orderPageBtn px-3 py-1 border rounded-lg hover:bg-gray-50 text-sm transition" data-page="${pagination.last_page}">${pagination.last_page}</button>`;
            }
            
            // Next button
            if (pagination.current_page < pagination.last_page) {
                html += `<button class="orderPageBtn px-3 py-1 border rounded-lg hover:bg-gray-50 text-sm transition" data-page="${pagination.current_page + 1}">
                            Mbele <i class="fas fa-chevron-right"></i>
                        </button>`;
            }
            
            // Showing entries info
            html += `</div>
            <div class="text-center text-xs text-gray-500 mt-3">
                Inaonyesha ${pagination.from || 0} - ${pagination.to || 0} kati ya ${pagination.total || 0} oda
            </div>`;
            
            container.innerHTML = html;
            
            // Attach event listeners
            document.querySelectorAll('.orderPageBtn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const page = this.dataset.page;
                    if (page) loadOrders(parseInt(page));
                });
            });
        }
        
        async function viewOrderDetails(id) {
            try {
                const response = await fetch(`/orders/${id}`);
                const data = await response.json();
                if (data.success) {
                    const order = data.order;
                    const statusConfig = getStatusConfig(order.status);
                    let itemsHtml = '';
                    order.items.forEach((item, idx) => {
                        itemsHtml += `
                            <tr class="border-b">
                                <td class="py-1 px-2">${idx + 1}</td>
                                <td class="py-1 px-2">${escapeHtml(item.jina)}</td>
                                <td class="py-1 px-2 text-right">${formatNumber(item.idadi)}</td>
                                <td class="py-1 px-2 text-right">${formatNumber(item.bei)}</td>
                                <td class="py-1 px-2 text-right">${formatNumber(item.punguzo || 0)}</td>
                                <td class="py-1 px-2 text-right font-semibold">${formatNumber(item.total)} TZS</td>
                            </tr>
                        `;
                    });
                    
                    const detailsContent = document.getElementById('orderDetailsContent');
                    if (detailsContent) {
                        detailsContent.innerHTML = `
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4">
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <h3 class="text-xs font-semibold text-gray-700 mb-2">Taarifa za Oda</h3>
                                    <div class="space-y-1 text-sm">
                                        <p><strong>Namba:</strong> ${escapeHtml(order.order_number)}</p>
                                        <p><strong>Tarehe:</strong> ${new Date(order.created_at).toLocaleString()}</p>
                                        <p><strong>Hali:</strong> <span class="order-status-badge ${statusConfig.bg} ${statusConfig.text}"><i class="fas ${statusConfig.icon} mr-1"></i>${statusConfig.label}</span></p>
                                        ${order.notes ? `<p><strong>Maelezo:</strong> ${escapeHtml(order.notes)}</p>` : ''}
                                    </div>
                                </div>
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <h3 class="text-xs font-semibold text-gray-700 mb-2">Taarifa za Mteja</h3>
                                    <div class="space-y-1 text-sm">
                                        <p><strong>Jina:</strong> ${escapeHtml(order.customer_name || '-')}</p>
                                        ${order.customer_phone ? `<p><strong>Simu:</strong> ${escapeHtml(order.customer_phone)}</p>` : ''}
                                    </div>
                                </div>
                            </div>
                            <h3 class="text-xs font-semibold text-gray-700 mb-2">Bidhaa</h3>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm border">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="py-1 px-2">#</th>
                                            <th>Bidhaa</th>
                                            <th class="text-right">Idadi</th>
                                            <th class="text-right">Bei</th>
                                            <th class="text-right">Punguzo</th>
                                            <th class="text-right">Jumla</th>
                                        </tr>
                                    </thead>
                                    <tbody>${itemsHtml}</tbody>
                                </table>
                            </div>
                            <div class="text-right mt-3 pt-2 border-t">
                                <strong>Jumla Ndogo:</strong> ${formatNumber(order.subtotal)} TZS<br>
                                ${order.discount > 0 ? `<strong>Punguzo:</strong> -${formatNumber(order.discount)} TZS<br>` : ''}
                                <strong class="text-lg">JUMLA: ${formatNumber(order.total)} TZS</strong>
                            </div>
                        `;
                    }
                    
                    const detailsModal = document.getElementById('orderDetailsModal');
                    if (detailsModal) detailsModal.style.display = 'flex';
                    currentOrderId = order.id;
                }
            } catch (error) { 
                console.error('View order details error:', error);
                showToast('Kuna tatizo katika kuona maelezo!', 'error'); 
            }
        }
        
        async function updateOrderStatus(id, newStatus) {
            try {
                const response = await fetch(`/orders/${id}/status`, {
                    method: 'PUT', 
                    headers: { 
                        'Content-Type': 'application/json', 
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content 
                    },
                    body: JSON.stringify({ status: newStatus })
                });
                const data = await response.json();
                if (data.success) { 
                    showToast(data.message, 'success'); 
                    loadOrders(); 
                } else {
                    showToast(data.message || 'Kuna tatizo!', 'error');
                }
            } catch (error) { 
                console.error('Update order status error:', error);
                showToast('Kuna tatizo katika kubadilisha hali!', 'error'); 
            }
        }
        
        function showDeleteConfirm(id, name) {
            const deleteMessage = document.getElementById('orderDeleteMessage');
            if (deleteMessage) deleteMessage.textContent = `Una uhakika unataka kufuta oda ya "${escapeHtml(name)}"?`;
            const deleteModal = document.getElementById('orderDeleteModal');
            if (deleteModal) deleteModal.style.display = 'flex';
            deleteOrderId = id;
        }
        
        async function deleteOrder(id) {
            try {
                const response = await fetch(`/orders/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content }
                });
                const data = await response.json();
                if (data.success) { 
                    showToast(data.message, 'success'); 
                    loadOrders(); 
                } else {
                    showToast(data.message || 'Kuna tatizo!', 'error');
                }
            } catch (error) { 
                console.error('Delete order error:', error);
                showToast('Kuna tatizo katika kufuta oda!', 'error'); 
            }
        }
        
        async function sendToCart(id) {
            try {
                showToast('Inatuma bidhaa kwenye Kikapu...', 'info');
                const response = await fetch(`/orders/${id}/send-to-kikapu`, {
                    method: 'POST', 
                    headers: { 
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content, 
                        'Content-Type': 'application/json' 
                    }
                });
                const data = await response.json();
                if (data.success && window.mauzoManager) {
                    data.items.forEach(item => {
                        window.mauzoManager.cart.push({
                            jina: item.jina, 
                            bei: item.bei, 
                            idadi: item.idadi, 
                            punguzo: item.punguzo,
                            punguzo_aina: 'bidhaa', 
                            actual_discount: (item.punguzo || 0) * item.idadi, 
                            jumla: item.jumla,
                            bidhaa_id: item.bidhaa_id, 
                            timestamp: new Date().toISOString(),
                            company_id: window.mauzoManager.companyId
                        });
                    });
                    window.mauzoManager.saveCart();
                    window.mauzoManager.updateCartCount();
                    window.mauzoManager.updateCartDisplay();
                    showToast(`${data.items.length} bidhaa zimeongezwa kwenye Kikapu!`, 'success');
                    setTimeout(() => document.getElementById('kikapu-tab')?.click(), 1500);
                    loadOrders();
                } else {
                    showToast(data.message || 'Kuna tatizo katika kutuma kwenye Kikapu!', 'error');
                }
            } catch (error) { 
                console.error('Send to cart error:', error);
                showToast('Kuna tatizo katika kutuma kwenye Kikapu!', 'error'); 
            }
        }
        
        async function shareWhatsApp(orderId) {
            try {
                const response = await fetch(`/orders/${orderId}/share-whatsapp`);
                const data = await response.json();
                if (data.success && data.whatsapp_url) {
                    window.open(data.whatsapp_url, '_blank');
                } else {
                    showToast('Kuna tatizo katika kushiriki!', 'error');
                }
            } catch (error) { 
                console.error('Share WhatsApp error:', error);
                showToast('Kuna tatizo katika kushiriki!', 'error'); 
            }
        }
        
        async function copyOrder(orderId) {
            try {
                const response = await fetch(`/orders/${orderId}`);
                const data = await response.json();
                if (data.success) {
                    const order = data.order;
                    let text = `ODA #${order.order_number}\nMteja: ${order.customer_name}\nSimu: ${order.customer_phone || '-'}\nJumla: ${formatNumber(order.total)} TZS\n\nBidhaa:\n`;
                    order.items.forEach(item => { 
                        text += `${item.jina}: ${item.idadi} x ${formatNumber(item.bei)} = ${formatNumber(item.total)} TZS\n`; 
                    });
                    await navigator.clipboard.writeText(text);
                    showToast('Maelezo ya oda yamenakiliwa!', 'success');
                }
            } catch (error) { 
                console.error('Copy order error:', error);
                showToast('Kuna tatizo katika kunakili!', 'error'); 
            }
        }
        
        // Event Listeners
        const orderTab = document.getElementById('weka-order-tab');
        if (orderTab) orderTab.addEventListener('click', () => loadOrders());
        
        const createBtn = document.getElementById('orderCreateBtn');
        if (createBtn) {
            createBtn.addEventListener('click', () => { 
                resetOrderForm(); 
                const modal = document.getElementById('orderModal');
                if (modal) modal.style.display = 'flex'; 
            });
        }
        
        const closeModalBtn = document.getElementById('closeOrderModal');
        if (closeModalBtn) closeModalBtn.addEventListener('click', () => {
            const modal = document.getElementById('orderModal');
            if (modal) modal.style.display = 'none';
        });
        
        const cancelBtn = document.getElementById('orderCancelBtn');
        if (cancelBtn) cancelBtn.addEventListener('click', () => {
            const modal = document.getElementById('orderModal');
            if (modal) modal.style.display = 'none';
        });
        
        const addProductBtn = document.getElementById('orderAddProduct');
        if (addProductBtn) addProductBtn.addEventListener('click', addProductRow);
        
        const saveDraftBtn = document.getElementById('orderSaveDraft');
        if (saveDraftBtn) saveDraftBtn.addEventListener('click', () => saveOrder('saved'));
        
        const confirmSubmitBtn = document.getElementById('orderConfirmSubmit');
        if (confirmSubmitBtn) {
            confirmSubmitBtn.addEventListener('click', (e) => { 
                e.preventDefault(); 
                saveOrder('confirmed'); 
            });
        }
        
        const globalDiscount = document.getElementById('orderGlobalDiscount');
        if (globalDiscount) globalDiscount.addEventListener('input', calculateOrderTotal);
        
        const statusFilter = document.getElementById('orderStatusFilter');
        if (statusFilter) statusFilter.addEventListener('change', () => loadOrders());
        
        const searchInput = document.getElementById('orderSearch');
        if (searchInput) {
            searchInput.addEventListener('input', () => setTimeout(() => loadOrders(), 500));
        }
        
        const resetFilters = document.getElementById('orderResetFilters');
        if (resetFilters) {
            resetFilters.addEventListener('click', () => {
                const search = document.getElementById('orderSearch');
                const status = document.getElementById('orderStatusFilter');
                const startDate = document.getElementById('orderStartDate');
                const endDate = document.getElementById('orderEndDate');
                
                if (search) search.value = '';
                if (status) status.value = 'all';
                if (startDate) startDate.value = '';
                if (endDate) endDate.value = '';
                loadOrders();
            });
        }
        
        const closeDetailsModal = document.getElementById('closeOrderDetailsModal');
        if (closeDetailsModal) closeDetailsModal.addEventListener('click', () => {
            const modal = document.getElementById('orderDetailsModal');
            if (modal) modal.style.display = 'none';
        });
        
        const orderCloseDetails = document.getElementById('orderCloseDetails');
        if (orderCloseDetails) orderCloseDetails.addEventListener('click', () => {
            const modal = document.getElementById('orderDetailsModal');
            if (modal) modal.style.display = 'none';
        });
        
        const closeDeleteModal = document.getElementById('closeOrderDeleteModal');
        if (closeDeleteModal) closeDeleteModal.addEventListener('click', () => {
            const modal = document.getElementById('orderDeleteModal');
            if (modal) modal.style.display = 'none';
        });
        
        const cancelDelete = document.getElementById('orderCancelDelete');
        if (cancelDelete) cancelDelete.addEventListener('click', () => {
            const modal = document.getElementById('orderDeleteModal');
            if (modal) modal.style.display = 'none';
        });
        
        const confirmDelete = document.getElementById('orderConfirmDelete');
        if (confirmDelete) {
            confirmDelete.addEventListener('click', () => { 
                const modal = document.getElementById('orderDeleteModal');
                if (modal) modal.style.display = 'none'; 
                if (deleteOrderId) deleteOrder(deleteOrderId); 
            });
        }
        
        const printBtn = document.getElementById('orderPrint');
        if (printBtn) {
            printBtn.addEventListener('click', () => { 
                if (currentOrderId) window.open(`/orders/${currentOrderId}/generate-invoice`, '_blank'); 
            });
        }
        
        const whatsappBtn = document.getElementById('orderWhatsApp');
        if (whatsappBtn) {
            whatsappBtn.addEventListener('click', () => { 
                if (currentOrderId) shareWhatsApp(currentOrderId); 
            });
        }
        
        const copyBtn = document.getElementById('orderCopy');
        if (copyBtn) {
            copyBtn.addEventListener('click', () => { 
                if (currentOrderId) copyOrder(currentOrderId); 
            });
        }
        
        // Statistics cards
        document.querySelectorAll('.order-stat-card').forEach(card => {
            card.addEventListener('click', () => {
                const status = card.dataset.status;
                const statusFilter = document.getElementById('orderStatusFilter');
                if (status && status !== 'all' && statusFilter) {
                    statusFilter.value = status;
                }
                loadOrders();
            });
        });
        
        // Initialize
        initCustomerSearch();
        document.querySelectorAll('.orderProductRow').forEach(row => initProductSearch(row));
        loadOrders();
    }
})();
</script>