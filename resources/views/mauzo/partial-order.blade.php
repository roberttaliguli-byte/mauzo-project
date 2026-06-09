<!-- resources/views/mauzo/partial-order.blade.php -->
<style>
.order-stat-card { transition: all 0.2s ease; cursor: pointer; }
.order-stat-card:hover { transform: scale(1.02); }
.order-status-badge { display: inline-flex; align-items: center; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; }
.order-action-btn { transition: all 0.2s ease; }
.order-action-btn:hover { transform: translateY(-1px); }

.modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
}
.modal-content {
    background: white;
    border-radius: 12px;
    width: 95%;
    max-width: 1100px;
    max-height: 90vh;
    overflow-y: auto;
}
@media (max-width: 640px) {
    .modal-content { width: 95%; margin: 10px; }
}
</style>

<!-- Orders Tab Content -->
<div id="weka-order-tab-content" class="tab-content hidden">
    <div class="bg-white rounded-lg shadow border border-gray-200 p-4">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-bold flex items-center text-gray-800">
                <i class="fas fa-clipboard-list mr-2 text-amber-600"></i>
                Usimamizi wa Oda
            </h2>
            <button id="orderCreateBtn" class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 text-sm">
                <i class="fas fa-plus"></i> Oda Mpya
            </button>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-4">
            <div class="order-stat-card bg-gray-50 rounded-lg p-3 border border-gray-200" data-status="all">
                <div><p class="text-xs text-gray-500">Jumla ya Oda</p><p class="text-xl font-bold text-gray-800" id="orderStatTotal">0</p></div>
            </div>
            <div class="order-stat-card bg-amber-50 rounded-lg p-3 border border-amber-200" data-status="saved">
                <div><p class="text-xs text-amber-600">Zilizohifadhiwa</p><p class="text-xl font-bold text-amber-700" id="orderStatSaved">0</p></div>
            </div>
            <div class="order-stat-card bg-blue-50 rounded-lg p-3 border border-blue-200" data-status="confirmed">
                <div><p class="text-xs text-blue-600">Zilizothibitishwa</p><p class="text-xl font-bold text-blue-700" id="orderStatConfirmed">0</p></div>
            </div>
            <div class="order-stat-card bg-green-50 rounded-lg p-3 border border-green-200" data-status="paid">
                <div><p class="text-xs text-green-600">Zilizolipwa</p><p class="text-xl font-bold text-green-700" id="orderStatPaid">0</p></div>
            </div>
            <div class="order-stat-card bg-purple-50 rounded-lg p-3 border border-purple-200">
                <div><p class="text-xs text-purple-600">Mapato Jumla</p><p class="text-xl font-bold text-purple-700" id="orderStatRevenue">0</p></div>
            </div>
            <div class="order-stat-card bg-orange-50 rounded-lg p-3 border border-orange-200">
                <div><p class="text-xs text-orange-600">Mapato Yanayosubiri</p><p class="text-xl font-bold text-orange-700" id="orderStatPending">0</p></div>
            </div>
        </div>

        <!-- Filters -->
        <div class="grid grid-cols-1 sm:grid-cols-5 gap-3 mb-4">
            <div class="relative sm:col-span-2">
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                <input type="text" id="orderSearch" placeholder="Tafuta kwa namba, jina au simu..." class="pl-9 w-full border border-gray-300 rounded-lg p-2 text-sm">
            </div>
            <div>
                <select id="orderStatusFilter" class="w-full border border-gray-300 rounded-lg p-2 text-sm">
                    <option value="all">Hali zote</option>
                    <option value="saved">Zilizohifadhiwa</option>
                    <option value="confirmed">Zilizothibitishwa</option>
                    <option value="paid">Zilizolipwa</option>
                </select>
            </div>
            <div><input type="date" id="orderStartDate" class="w-full border border-gray-300 rounded-lg p-2 text-sm"></div>
            <div><input type="date" id="orderEndDate" class="w-full border border-gray-300 rounded-lg p-2 text-sm"></div>
            <div><button id="orderResetFilters" class="w-full bg-gray-500 hover:bg-gray-600 text-white px-3 py-2 rounded-lg text-sm"><i class="fas fa-redo-alt"></i> Safisha</button></div>
        </div>

        <!-- Orders Table -->
        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="w-full text-sm">
                <thead class="bg-gray-100">
                    <tr><th class="border px-3 py-2 text-left">Namba ya Oda</th><th class="border px-3 py-2 text-left">Mteja</th><th class="border px-3 py-2 text-left">Simu</th><th class="border px-3 py-2 text-left">Tarehe</th><th class="border px-3 py-2 text-right">Jumla</th><th class="border px-3 py-2 text-center">Hali</th><th class="border px-3 py-2 text-center">Vitendo</th></tr>
                </thead>
                <tbody id="orderTableBody">
                    <tr><td colspan="7" class="text-center py-6"><i class="fas fa-spinner fa-spin mr-2"></i>Inapakia...</i></td></tr>
                </tbody>
            </table>
        </div>
        <div id="orderPagination" class="mt-4 flex justify-center"></div>
    </div>
</div>

<!-- Order Modal -->
<div id="orderModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="bg-gradient-to-r from-amber-600 to-amber-700 p-3 text-white flex items-center justify-between sticky top-0 rounded-t-lg">
            <h2 class="text-base font-semibold">Oda Mpya</h2>
            <button id="closeOrderModal" class="text-white hover:text-gray-200 text-xl">&times;</button>
        </div>
        <div class="p-4">
            <form id="orderForm">
                @csrf
                
                <div class="bg-gray-50 rounded-lg p-3 border border-gray-200 mb-4">
                    <h3 class="font-semibold text-gray-800 mb-2 text-sm">Taarifa za Mteja</h3>
                    <div class="relative mb-3">
                        <input type="text" id="orderCustomerSearch" placeholder="Tafuta mteja kwa jina au simu..." class="w-full border border-gray-300 rounded-lg p-2 pl-9 text-sm">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        <div id="orderCustomerDropdown" class="hidden absolute z-10 w-full bg-white border rounded-lg shadow-lg max-h-48 overflow-y-auto"></div>
                    </div>
                    <input type="hidden" id="orderCustomerId">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div><label class="block text-xs font-semibold text-gray-700 mb-1">Jina Kamili *</label><input type="text" id="orderCustomerName" class="w-full border border-gray-300 rounded-lg p-2 text-sm" required></div>
                        <div><label class="block text-xs font-semibold text-gray-700 mb-1">Namba ya Simu</label><input type="text" id="orderCustomerPhone" class="w-full border border-gray-300 rounded-lg p-2 text-sm"></div>
                        <div><label class="block text-xs font-semibold text-gray-700 mb-1">Barua Pepe</label><input type="email" id="orderCustomerEmail" class="w-full border border-gray-300 rounded-lg p-2 text-sm"></div>
                        <div><label class="block text-xs font-semibold text-gray-700 mb-1">Anapoishi</label><input type="text" id="orderCustomerAddress" class="w-full border border-gray-300 rounded-lg p-2 text-sm"></div>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-3 border border-gray-200 mb-4">
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="font-semibold text-gray-800 text-sm">Bidhaa</h3>
                        <button type="button" id="orderAddProduct" class="bg-green-600 hover:bg-green-700 text-white px-2 py-1 rounded text-xs"><i class="fas fa-plus"></i> Ongeza</button>
                    </div>
                    <div id="orderProductsContainer" class="space-y-2">
                        <div class="orderProductRow bg-white p-2 rounded border">
                            <div class="grid grid-cols-12 gap-2 items-center">
                                <div class="col-span-12 md:col-span-5 mb-2 md:mb-0">
                                    <div class="relative">
                                        <input type="text" class="orderProductSearch w-full border border-gray-300 rounded-lg p-1 text-sm" placeholder="Tafuta bidhaa...">
                                        <select class="orderProductSelect hidden absolute top-full left-0 right-0 z-10 bg-white border rounded-lg shadow-lg max-h-40 overflow-y-auto w-full">
                                            <option value="">Chagua Bidhaa</option>
                                            @foreach($bidhaa as $item)
                                            <option value="{{ $item->id }}" data-price="{{ $item->bei_kuuza }}">{{ $item->jina }} - {{ number_format($item->bei_kuuza, 0) }} TZS</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-span-4 md:col-span-2">
                                    <input type="number" class="orderProductQty w-full border border-gray-300 rounded-lg p-1 text-sm" placeholder="Idadi" min="0.01" step="0.01" value="1">
                                </div>
                                <div class="col-span-4 md:col-span-2">
                                    <input type="number" class="orderProductPrice w-full border border-gray-300 rounded-lg p-1 text-sm bg-gray-100" readonly placeholder="Bei">
                                </div>
                                <div class="col-span-3 md:col-span-2">
                                    <input type="number" class="orderProductDiscount w-full border border-gray-300 rounded-lg p-1 text-sm" placeholder="Punguzo" min="0" step="0.01" value="0">
                                </div>
                                <div class="col-span-1 text-center">
                                    <button type="button" class="orderRemoveProduct text-red-500"><i class="fas fa-trash"></i></button>
                                </div>
                            </div>
                            <div class="flex justify-end mt-1 pt-1 border-t">
                                <span class="text-xs font-semibold">Jumla: </span>
                                <span class="orderProductTotal text-xs font-bold text-green-700 ml-2">0 TZS</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-3 border border-gray-200 mb-4">
                    <div class="flex justify-end">
                        <div class="w-full md:w-80 space-y-1">
                            <div class="flex justify-between text-sm"><span>Jumla Ndogo:</span><span id="orderSummarySubtotal" class="font-semibold">0 TZS</span></div>
                            <div class="flex justify-between items-center text-sm"><span>Punguzo:</span><input type="number" id="orderGlobalDiscount" class="w-24 border rounded p-1 text-right text-sm" value="0" step="0.01"></div>
                            <div class="border-t pt-1 flex justify-between"><span class="font-bold">JUMLA:</span><span id="orderSummaryTotal" class="font-bold text-green-700">0 TZS</span></div>
                        </div>
                    </div>
                </div>

                <div><label class="block text-xs font-semibold text-gray-700 mb-1">Maelezo</label><textarea id="orderNotes" rows="2" class="w-full border border-gray-300 rounded-lg p-2 text-sm"></textarea></div>

                <div class="flex justify-end gap-2 pt-2 border-t mt-4">
                    <button type="button" id="orderCancelBtn" class="bg-gray-400 hover:bg-gray-500 px-4 py-2 rounded-lg text-white font-semibold text-sm">Ghairi</button>
                    <button type="button" id="orderSaveDraft" class="bg-amber-600 hover:bg-amber-700 px-4 py-2 rounded-lg text-white font-semibold text-sm"><i class="fas fa-save mr-1"></i> Hifadhi</button>
                    <button type="submit" id="orderConfirmSubmit" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg text-white font-semibold text-sm"><i class="fas fa-check-circle mr-1"></i> Thibitisha</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Order Details Modal -->
<div id="orderDetailsModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 p-3 text-white flex items-center justify-between sticky top-0 rounded-t-lg">
            <div class="flex items-center"><i class="fas fa-receipt mr-2"></i><h2 class="text-base font-semibold">Maelezo ya Oda</h2></div>
            <button id="closeOrderDetailsModal" class="text-white hover:text-gray-200"><i class="fas fa-times"></i></button>
        </div>
        <div class="p-4" id="orderDetailsContent"></div>
        <div class="flex flex-wrap gap-2 p-3 border-t bg-gray-50">
            <button id="orderPrint" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm"><i class="fas fa-print mr-1"></i> Chapisha</button>
            <button id="orderWhatsApp" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm"><i class="fab fa-whatsapp mr-1"></i> WhatsApp</button>
            <button id="orderCopy" class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-1 rounded text-sm"><i class="fas fa-copy mr-1"></i> Nakili</button>
            <button id="orderPDF" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm"><i class="fas fa-file-pdf mr-1"></i> PDF</button>
            <button id="orderCloseDetails" class="bg-gray-400 hover:bg-gray-500 text-white px-3 py-1 rounded text-sm ml-auto"><i class="fas fa-times mr-1"></i> Funga</button>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="orderDeleteModal" class="modal" style="display: none;">
    <div class="modal-content bg-white rounded-lg shadow-lg w-full max-w-sm mx-2">
        <div class="bg-gradient-to-r from-red-600 to-red-700 p-3 text-white flex items-center">
            <i class="fas fa-trash mr-2"></i>
            <h2 class="text-base font-semibold">Futa Oda</h2>
            <button id="closeOrderDeleteModal" class="ml-auto text-white hover:text-gray-200"><i class="fas fa-times"></i></button>
        </div>
        <div class="p-4 text-center">
            <i class="fas fa-exclamation-triangle text-red-500 text-3xl mb-3"></i>
            <p class="text-gray-800 mb-2" id="orderDeleteMessage">Una uhakika unataka kufuta oda hii?</p>
            <div class="flex justify-center gap-3 mt-4">
                <button id="orderCancelDelete" class="bg-gray-400 hover:bg-gray-500 px-4 py-2 rounded-lg text-white text-sm">Ghairi</button>
                <button id="orderConfirmDelete" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-lg text-white text-sm">Futa</button>
            </div>
        </div>
    </div>
</div>

<!-- Custom Confirmation Modal -->
<div id="orderConfirmModal" class="modal" style="display: none;">
    <div class="modal-content bg-white rounded-lg shadow-lg w-full max-w-sm mx-2">
        <div class="bg-gradient-to-r from-amber-600 to-amber-700 p-3 text-white flex items-center">
            <i class="fas fa-question-circle mr-2"></i>
            <h2 class="text-base font-semibold" id="orderConfirmTitle">Thibitisha</h2>
            <button id="closeOrderConfirmModal" class="ml-auto text-white hover:text-gray-200"><i class="fas fa-times"></i></button>
        </div>
        <div class="p-4 text-center">
            <p class="text-gray-800 mb-4" id="orderConfirmMessage">Una uhakika unataka kuendelea?</p>
            <div class="flex justify-center gap-3">
                <button id="orderConfirmNo" class="bg-gray-400 hover:bg-gray-500 px-4 py-2 rounded-lg text-white text-sm">Hapana</button>
                <button id="orderConfirmYes" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg text-white text-sm">Ndio</button>
            </div>
        </div>
    </div>
</div>

<!-- Action Modal Container -->
<div id="orderActionModal" class="modal" style="display: none;">
    <div class="modal-content bg-white rounded-lg shadow-lg w-full max-w-sm mx-2">
        <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 p-3 text-white flex items-center justify-between">
            <h2 class="text-base font-semibold">Vitendo</h2>
            <button id="closeOrderActionModal" class="text-white hover:text-gray-200"><i class="fas fa-times"></i></button>
        </div>
        <div class="p-2 space-y-1" id="orderActionContent"></div>
    </div>
</div>

<script>
// Orders Module with Quantity × Price = Total calculation
(function() {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initOrders);
    } else {
        initOrders();
    }
    
    function initOrders() {
        console.log('Orders module initializing...');
        
        let currentOrderId = null;
        let deleteOrderId = null;
        let confirmCallback = null;
        let customers = [];
        
        try {
            customers = @json($wateja);
        } catch(e) { customers = []; }
        
        // Helper Functions
        function showToast(msg, type) {
            let toast = document.getElementById('orderToastMsg');
            if (!toast) {
                toast = document.createElement('div');
                toast.id = 'orderToastMsg';
                toast.className = 'fixed bottom-4 right-4 z-50 bg-white rounded-lg shadow-lg p-3 border-l-4 min-w-[280px]';
                document.body.appendChild(toast);
            }
            toast.className = `fixed bottom-4 right-4 z-50 bg-white rounded-lg shadow-lg p-3 border-l-4 ${type === 'error' ? 'border-red-500' : 'border-green-500'}`;
            toast.innerHTML = `<div class="flex items-center"><i class="fas ${type === 'error' ? 'fa-times-circle text-red-500' : 'fa-check-circle text-green-500'} mr-2"></i><span class="text-sm">${msg}</span></div>`;
            toast.style.display = 'block';
            setTimeout(() => toast.style.display = 'none', 3000);
        }
        
        function formatNumber(n) { return parseFloat(n || 0).toLocaleString(undefined, {minimumFractionDigits: 0}); }
        function escapeHtml(s) { if (!s) return ''; return s.replace(/[&<>]/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;'})[m]); }
        
        function showConfirm(title, msg, callback) {
            document.getElementById('orderConfirmTitle').textContent = title;
            document.getElementById('orderConfirmMessage').textContent = msg;
            document.getElementById('orderConfirmModal').style.display = 'flex';
            confirmCallback = callback;
        }
        
        // Calculate item total: Quantity × Price - Discount
        function calculateItemTotal(row) {
            const qty = parseFloat(row.querySelector('.orderProductQty')?.value) || 0;
            const price = parseFloat(row.querySelector('.orderProductPrice')?.value) || 0;
            const discount = parseFloat(row.querySelector('.orderProductDiscount')?.value) || 0;
            
            // Calculate: (Quantity × Price) - Discount
            let total = (qty * price) - discount;
            if (total < 0) total = 0;
            
            // Update the total display
            const totalSpan = row.querySelector('.orderProductTotal');
            if (totalSpan) {
                totalSpan.textContent = formatNumber(total) + ' TZS';
            }
            
            return total;
        }
        
        // Update row total (called when quantity, price, or discount changes)
        function updateRowTotal(row) {
            calculateItemTotal(row);
            calculateOrderTotal();
        }
        
        // Calculate overall order totals
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
            
            document.getElementById('orderSummarySubtotal').textContent = formatNumber(subtotal) + ' TZS';
            document.getElementById('orderSummaryTotal').textContent = formatNumber(total) + ' TZS';
        }
        
        // Product Search (like Mauzo)
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
                    if (i > 0) opt.style.display = opt.textContent.toLowerCase().includes(filter) ? '' : 'none';
                });
            };
            
            const selectProduct = (option) => {
                if (!option.value) return;
                const price = parseFloat(option.dataset.price || 0);
                priceField.value = price.toFixed(2);
                searchInput.value = option.textContent.split(' - ')[0];
                selectDropdown.classList.add('hidden');
                
                // Set default quantity if empty
                if (qtyField && !qtyField.value) {
                    qtyField.value = '1';
                }
                
                // Calculate total with new price
                calculateItemTotal(row);
                calculateOrderTotal();
            };
            
            searchInput.addEventListener('focus', () => { 
                selectDropdown.classList.remove('hidden'); 
                filterOptions(searchInput.value); 
            });
            
            searchInput.addEventListener('input', (e) => { 
                filterOptions(e.target.value); 
                selectDropdown.classList.remove('hidden'); 
            });
            
            selectDropdown.addEventListener('change', () => { 
                const opt = selectDropdown.options[selectDropdown.selectedIndex]; 
                if (opt?.value) selectProduct(opt); 
            });
            
            // Quantity change - update total
            if (qtyField) {
                qtyField.addEventListener('input', () => {
                    updateRowTotal(row);
                });
                qtyField.addEventListener('blur', () => {
                    if (!qtyField.value || parseFloat(qtyField.value) <= 0) {
                        qtyField.value = '1';
                        updateRowTotal(row);
                    }
                });
            }
            
            // Discount change - update total
            if (discountField) {
                discountField.addEventListener('input', () => {
                    updateRowTotal(row);
                });
                discountField.addEventListener('blur', () => {
                    const qty = parseFloat(qtyField?.value) || 0;
                    const price = parseFloat(priceField?.value) || 0;
                    let maxDiscount = qty * price;
                    if (parseFloat(discountField.value) > maxDiscount) {
                        discountField.value = maxDiscount.toFixed(2);
                        updateRowTotal(row);
                    }
                });
            }
            
            document.addEventListener('click', (e) => { 
                if (!searchInput.contains(e.target) && !selectDropdown.contains(e.target)) 
                    selectDropdown.classList.add('hidden'); 
            });
        }
        
        function addProductRow() {
            const container = document.getElementById('orderProductsContainer');
            const template = container.children[0];
            const newRow = template.cloneNode(true);
            
            // Clear values
            newRow.querySelector('.orderProductSearch').value = '';
            newRow.querySelector('.orderProductSelect').value = '';
            newRow.querySelector('.orderProductQty').value = '1';
            newRow.querySelector('.orderProductPrice').value = '';
            newRow.querySelector('.orderProductDiscount').value = '0';
            newRow.querySelector('.orderProductTotal').textContent = '0 TZS';
            
            container.appendChild(newRow);
            initProductSearch(newRow);
            
            // Add remove button event
            newRow.querySelector('.orderRemoveProduct').addEventListener('click', () => {
                if (container.children.length > 1) {
                    newRow.remove();
                    calculateOrderTotal();
                } else {
                    showToast('Angalau bidhaa moja inahitajika!', 'warning');
                }
            });
        }
        
        // Customer Search
        function initCustomerSearch() {
            const searchInput = document.getElementById('orderCustomerSearch');
            const dropdown = document.getElementById('orderCustomerDropdown');
            if (!searchInput) return;
            
            searchInput.addEventListener('input', function() {
                const search = this.value.toLowerCase();
                if (search.length < 1) { dropdown.classList.add('hidden'); return; }
                const filtered = customers.filter(c => c.jina.toLowerCase().includes(search) || (c.simu && c.simu.toLowerCase().includes(search)));
                if (filtered.length > 0) {
                    dropdown.innerHTML = filtered.map(c => `<div class="orderCustomerOption p-2 hover:bg-gray-100 cursor-pointer border-b" data-id="${c.id}" data-name="${c.jina}" data-phone="${c.simu || ''}" data-email="${c.barua_pepe || ''}" data-address="${c.anapoishi || ''}"><div class="font-semibold text-sm">${c.jina}</div><div class="text-xs text-gray-500">${c.simu || 'Hakuna simu'}</div></div>`).join('');
                    dropdown.classList.remove('hidden');
                    document.querySelectorAll('.orderCustomerOption').forEach(opt => opt.addEventListener('click', function() {
                        document.getElementById('orderCustomerId').value = this.dataset.id;
                        document.getElementById('orderCustomerName').value = this.dataset.name;
                        document.getElementById('orderCustomerPhone').value = this.dataset.phone;
                        document.getElementById('orderCustomerEmail').value = this.dataset.email;
                        document.getElementById('orderCustomerAddress').value = this.dataset.address;
                        searchInput.value = this.dataset.name;
                        dropdown.classList.add('hidden');
                    }));
                } else dropdown.classList.add('hidden');
            });
            document.addEventListener('click', (e) => { if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) dropdown.classList.add('hidden'); });
        }
        
        function resetOrderForm() {
            document.getElementById('orderCustomerId').value = '';
            document.getElementById('orderCustomerSearch').value = '';
            document.getElementById('orderCustomerName').value = '';
            document.getElementById('orderCustomerPhone').value = '';
            document.getElementById('orderCustomerEmail').value = '';
            document.getElementById('orderCustomerAddress').value = '';
            document.getElementById('orderNotes').value = '';
            document.getElementById('orderGlobalDiscount').value = '0';
            
            const container = document.getElementById('orderProductsContainer');
            while (container.children.length > 1) container.removeChild(container.lastChild);
            const firstRow = container.children[0];
            firstRow.querySelector('.orderProductSearch').value = '';
            firstRow.querySelector('.orderProductSelect').value = '';
            firstRow.querySelector('.orderProductQty').value = '1';
            firstRow.querySelector('.orderProductPrice').value = '';
            firstRow.querySelector('.orderProductDiscount').value = '0';
            firstRow.querySelector('.orderProductTotal').textContent = '0 TZS';
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
                customer_email: document.getElementById('orderCustomerEmail')?.value,
                customer_address: document.getElementById('orderCustomerAddress')?.value,
                discount: parseFloat(document.getElementById('orderGlobalDiscount')?.value) || 0,
                notes: document.getElementById('orderNotes')?.value
            };
            
            const statusText = status === 'saved' ? 'Rasimu' : 'Imethibitishwa';
            showConfirm('Thibitisha Oda', `Je, una uhakika unataka kuhifadhi oda hii kama "${statusText}"?`, async (confirmed) => {
                if (!confirmed) return;
                try {
                    const response = await fetch('/orders', {
                        method: 'POST', 
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: JSON.stringify(formData)
                    });
                    const data = await response.json();
                    if (data.success) { 
                        showToast(data.message, 'success'); 
                        document.getElementById('orderModal').style.display = 'none'; 
                        resetOrderForm(); 
                        loadOrders(); 
                    } else {
                        showToast(data.message || 'Kuna tatizo!', 'error');
                    }
                } catch (error) { 
                    showToast('Kuna tatizo katika kuhifadhi oda!', 'error'); 
                }
            });
        }
        
        async function loadOrders() {
            const status = document.getElementById('orderStatusFilter')?.value || 'all';
            const search = document.getElementById('orderSearch')?.value || '';
            const startDate = document.getElementById('orderStartDate')?.value || '';
            const endDate = document.getElementById('orderEndDate')?.value || '';
            
            try {
                const params = new URLSearchParams({ status, search, start_date: startDate, end_date: endDate });
                const response = await fetch(`/orders?${params.toString()}`);
                const data = await response.json();
                if (data.success) { 
                    renderOrders(data.orders.data); 
                    updateStats(data.stats); 
                    renderPagination(data.orders); 
                } else {
                    showToast(data.message || 'Kuna tatizo!', 'error');
                }
            } catch (error) { 
                showToast('Kuna tatizo katika kupakia oda!', 'error'); 
            }
        }
        
        function renderOrders(orders) {
            const tableBody = document.getElementById('orderTableBody');
            if (!tableBody) return;
            
            if (!orders || orders.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="7" class="text-center py-6 text-gray-500">Hakuna oda zilizopatikana</td></tr>';
                return;
            }
            
            tableBody.innerHTML = '';
            orders.forEach(order => {
                const statusConfig = getStatusConfig(order.status);
                const row = document.createElement('tr');
                row.className = 'border-b hover:bg-gray-50';
                row.innerHTML = `
                    <td class="border px-3 py-2 font-mono text-xs">${order.order_number}</td>
                    <td class="border px-3 py-2">${escapeHtml(order.customer_name || '-')}</td>
                    <td class="border px-3 py-2">${order.customer_phone || '-'}</td>
                    <td class="border px-3 py-2">${new Date(order.created_at).toLocaleDateString()}</td>
                    <td class="border px-3 py-2 text-right font-semibold">${formatNumber(order.total)} TZS</td>
                    <td class="border px-3 py-2 text-center"><span class="order-status-badge ${statusConfig.bg} ${statusConfig.text} px-2 py-1 rounded-full text-xs"><i class="fas ${statusConfig.icon} mr-1"></i>${statusConfig.label}</span></td>
                    <td class="border px-3 py-2 text-center">
                        <div class="flex justify-center gap-1">
                            <button class="orderViewBtn bg-blue-100 hover:bg-blue-200 text-blue-700 p-1 rounded" data-id="${order.id}" title="Angalia"><i class="fas fa-eye text-xs"></i></button>
                            ${order.status === 'saved' ? `<button class="orderConfirmBtn bg-green-100 hover:bg-green-200 text-green-700 p-1 rounded" data-id="${order.id}" title="Thibitisha"><i class="fas fa-check-circle text-xs"></i></button>` : ''}
                            ${order.status === 'confirmed' ? `<button class="orderSendToCart bg-amber-100 hover:bg-amber-200 text-amber-700 p-1 rounded" data-id="${order.id}" title="Tuma Kikapu"><i class="fas fa-shopping-cart text-xs"></i></button>` : ''}
                            <button class="orderDeleteBtn bg-red-100 hover:bg-red-200 text-red-700 p-1 rounded" data-id="${order.id}" data-name="${order.customer_name}" title="Futa"><i class="fas fa-trash text-xs"></i></button>
                            <button class="orderActionsBtn bg-gray-100 hover:bg-gray-200 text-gray-700 p-1 rounded" data-id="${order.id}" title="Zaidi"><i class="fas fa-ellipsis-v text-xs"></i></button>
                        </div>
                    </td>
                `;
                tableBody.appendChild(row);
            });
            
            document.querySelectorAll('.orderViewBtn').forEach(btn => btn.addEventListener('click', () => viewOrderDetails(btn.dataset.id)));
            document.querySelectorAll('.orderConfirmBtn').forEach(btn => btn.addEventListener('click', () => updateOrderStatus(btn.dataset.id, 'confirmed')));
            document.querySelectorAll('.orderSendToCart').forEach(btn => btn.addEventListener('click', () => sendToCart(btn.dataset.id)));
            document.querySelectorAll('.orderDeleteBtn').forEach(btn => btn.addEventListener('click', () => showDeleteConfirm(btn.dataset.id, btn.dataset.name)));
            document.querySelectorAll('.orderActionsBtn').forEach(btn => btn.addEventListener('click', () => showOrderActions(btn.dataset.id)));
        }
        
        function getStatusConfig(status) {
            const configs = {
                saved: { label: 'Imehifadhiwa', bg: 'bg-amber-100', text: 'text-amber-800', icon: 'fa-save' },
                confirmed: { label: 'Imethibitishwa', bg: 'bg-blue-100', text: 'text-blue-800', icon: 'fa-check-circle' },
                paid: { label: 'Imelipwa', bg: 'bg-green-100', text: 'text-green-800', icon: 'fa-money-bill-wave' },
                cancelled: { label: 'Imefutwa', bg: 'bg-red-100', text: 'text-red-800', icon: 'fa-times-circle' }
            };
            return configs[status] || configs.saved;
        }
        
        function updateStats(stats) {
            if (stats) {
                document.getElementById('orderStatTotal') && (document.getElementById('orderStatTotal').textContent = (stats.saved + stats.confirmed + stats.paid));
                document.getElementById('orderStatSaved') && (document.getElementById('orderStatSaved').textContent = stats.saved || 0);
                document.getElementById('orderStatConfirmed') && (document.getElementById('orderStatConfirmed').textContent = stats.confirmed || 0);
                document.getElementById('orderStatPaid') && (document.getElementById('orderStatPaid').textContent = stats.paid || 0);
                document.getElementById('orderStatRevenue') && (document.getElementById('orderStatRevenue').textContent = formatNumber(stats.total_revenue || 0));
                document.getElementById('orderStatPending') && (document.getElementById('orderStatPending').textContent = formatNumber(stats.pending_revenue || 0));
            }
        }
        
        function renderPagination(pagination) {
            const container = document.getElementById('orderPagination');
            if (!container || !pagination || pagination.last_page <= 1) { 
                if (container) container.innerHTML = ''; 
                return; 
            }
            let html = '<div class="flex space-x-1">';
            if (pagination.current_page > 1) html += `<button class="orderPageBtn px-3 py-1 rounded border hover:bg-gray-50 text-sm" data-page="${pagination.current_page - 1}">← Nyuma</button>`;
            for (let i = 1; i <= pagination.last_page; i++) {
                if (i === pagination.current_page) html += `<span class="px-3 py-1 rounded bg-amber-600 text-white text-sm">${i}</span>`;
                else if (Math.abs(i - pagination.current_page) <= 2 || i === 1 || i === pagination.last_page) html += `<button class="orderPageBtn px-3 py-1 rounded border hover:bg-gray-50 text-sm" data-page="${i}">${i}</button>`;
            }
            if (pagination.current_page < pagination.last_page) html += `<button class="orderPageBtn px-3 py-1 rounded border hover:bg-gray-50 text-sm" data-page="${pagination.current_page + 1}">Mbele →</button>`;
            html += '</div>';
            container.innerHTML = html;
            document.querySelectorAll('.orderPageBtn').forEach(btn => btn.addEventListener('click', () => loadOrdersPage(btn.dataset.page)));
        }
        
        async function loadOrdersPage(page) {
            const status = document.getElementById('orderStatusFilter')?.value || 'all';
            const search = document.getElementById('orderSearch')?.value || '';
            const startDate = document.getElementById('orderStartDate')?.value || '';
            const endDate = document.getElementById('orderEndDate')?.value || '';
            try {
                const params = new URLSearchParams({ status, search, start_date: startDate, end_date: endDate, page });
                const response = await fetch(`/orders?${params.toString()}`);
                const data = await response.json();
                if (data.success) { renderOrders(data.orders.data); renderPagination(data.orders); }
            } catch (error) { console.error('Error:', error); }
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
                        itemsHtml += `<tr class="border-b"><td class="py-1 px-2">${idx + 1}</td><td class="py-1 px-2">${escapeHtml(item.jina)}</td><td class="py-1 px-2 text-right">${formatNumber(item.idadi)}</td><td class="py-1 px-2 text-right">${formatNumber(item.bei)}</td><td class="py-1 px-2 text-right">${formatNumber(item.punguzo || 0)}</td><td class="py-1 px-2 text-right font-semibold">${formatNumber(item.total)} TZS</td></tr>`;
                    });
                    document.getElementById('orderDetailsContent').innerHTML = `
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4">
                            <div class="bg-gray-50 p-3 rounded-lg"><h3 class="font-semibold text-sm mb-2">Taarifa za Oda</h3><div class="space-y-1 text-sm"><p><strong>Namba:</strong> ${order.order_number}</p><p><strong>Tarehe:</strong> ${new Date(order.created_at).toLocaleString()}</p><p><strong>Hali:</strong> <span class="order-status-badge ${statusConfig.bg} ${statusConfig.text} px-2 py-1 rounded-full text-xs"><i class="fas ${statusConfig.icon} mr-1"></i>${statusConfig.label}</span></p><p><strong>Maelezo:</strong> ${escapeHtml(order.notes || '-')}</p></div></div>
                            <div class="bg-gray-50 p-3 rounded-lg"><h3 class="font-semibold text-sm mb-2">Taarifa za Mteja</h3><div class="space-y-1 text-sm"><p><strong>Jina:</strong> ${escapeHtml(order.customer_name || '-')}</p><p><strong>Simu:</strong> ${order.customer_phone || '-'}</p><p><strong>Barua Pepe:</strong> ${order.customer_email || '-'}</p><p><strong>Anapoishi:</strong> ${escapeHtml(order.customer_address || '-')}</p></div></div>
                        </div>
                        <h3 class="font-semibold text-sm mb-2">Bidhaa</h3>
                        <div class="overflow-x-auto"><table class="w-full text-sm border"><thead class="bg-gray-50"><tr><th class="py-1 px-2">#</th><th class="py-1 px-2">Bidhaa</th><th class="py-1 px-2 text-right">Idadi</th><th class="py-1 px-2 text-right">Bei</th><th class="py-1 px-2 text-right">Punguzo</th><th class="py-1 px-2 text-right">Jumla</th></tr></thead><tbody>${itemsHtml}</tbody></table></div>
                        <div class="text-right mt-3 pt-2 border-t"><strong>Jumla Ndogo:</strong> ${formatNumber(order.subtotal)} TZS<br>${order.discount > 0 ? `<strong>Punguzo:</strong> -${formatNumber(order.discount)} TZS<br>` : ''}<strong class="text-lg">JUMLA: ${formatNumber(order.total)} TZS</strong></div>
                    `;
                    document.getElementById('orderDetailsModal').style.display = 'flex';
                    currentOrderId = order.id;
                }
            } catch (error) { showToast('Kuna tatizo katika kuona maelezo!', 'error'); }
        }
        
        async function updateOrderStatus(id, newStatus) {
            showConfirm('Badilisha Hali', `Je, una uhakika unataka kubadilisha hali ya oda hii?`, async (confirmed) => {
                if (!confirmed) return;
                try {
                    const response = await fetch(`/orders/${id}/status`, {
                        method: 'PUT', 
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: JSON.stringify({ status: newStatus })
                    });
                    const data = await response.json();
                    if (data.success) { 
                        showToast(data.message, 'success'); 
                        loadOrders(); 
                    } else {
                        showToast(data.message || 'Kuna tatizo!', 'error');
                    }
                } catch (error) { showToast('Kuna tatizo katika kubadilisha hali!', 'error'); }
            });
        }
        
        function showDeleteConfirm(id, name) {
            document.getElementById('orderDeleteMessage').textContent = `Una uhakika unataka kufuta oda ya "${escapeHtml(name)}"?`;
            document.getElementById('orderDeleteModal').style.display = 'flex';
            deleteOrderId = id;
        }
        
        async function deleteOrder(id) {
            try {
                const response = await fetch(`/orders/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                const data = await response.json();
                if (data.success) { 
                    showToast(data.message, 'success'); 
                    loadOrders(); 
                } else {
                    showToast(data.message || 'Kuna tatizo!', 'error');
                }
            } catch (error) { showToast('Kuna tatizo katika kufuta oda!', 'error'); }
        }
        
        async function sendToCart(id) {
            showConfirm('Tuma Kwenye Kikapu', 'Je, una uhakika unataka kutuma oda hii kwenye Kikapu?', async (confirmed) => {
                if (!confirmed) return;
                try {
                    showToast('Inatuma bidhaa kwenye Kikapu...', 'info');
                    const response = await fetch(`/orders/${id}/send-to-kikapu`, {
                        method: 'POST', 
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Content-Type': 'application/json' }
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
                                actual_discount: item.punguzo * item.idadi, 
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
                } catch (error) { showToast('Kuna tatizo katika kutuma kwenye Kikapu!', 'error'); }
            });
        }
        
        function showOrderActions(orderId) {
            const content = document.getElementById('orderActionContent');
            document.getElementById('orderActionModal').style.display = 'flex';
            content.innerHTML = `
                <button class="orderActionView w-full text-left p-2 hover:bg-gray-100 rounded text-sm" data-id="${orderId}"><i class="fas fa-eye text-blue-500 mr-2"></i>Angalia Maelezo</button>
                <button class="orderActionWhatsapp w-full text-left p-2 hover:bg-gray-100 rounded text-sm" data-id="${orderId}"><i class="fab fa-whatsapp text-green-500 mr-2"></i>Shiriki WhatsApp</button>
                <button class="orderActionPrint w-full text-left p-2 hover:bg-gray-100 rounded text-sm" data-id="${orderId}"><i class="fas fa-print text-purple-500 mr-2"></i>Chapisha</button>
                <button class="orderActionCopy w-full text-left p-2 hover:bg-gray-100 rounded text-sm" data-id="${orderId}"><i class="fas fa-copy text-gray-500 mr-2"></i>Nakili</button>
            `;
            content.querySelector('.orderActionView')?.addEventListener('click', () => { 
                document.getElementById('orderActionModal').style.display = 'none'; 
                viewOrderDetails(orderId); 
            });
            content.querySelector('.orderActionWhatsapp')?.addEventListener('click', () => { 
                document.getElementById('orderActionModal').style.display = 'none'; 
                shareWhatsApp(orderId); 
            });
            content.querySelector('.orderActionPrint')?.addEventListener('click', () => { 
                document.getElementById('orderActionModal').style.display = 'none'; 
                window.open(`/orders/${orderId}/generate-invoice`, '_blank'); 
            });
            content.querySelector('.orderActionCopy')?.addEventListener('click', () => { 
                document.getElementById('orderActionModal').style.display = 'none'; 
                copyOrder(orderId); 
            });
        }
        
        async function shareWhatsApp(orderId) {
            try {
                const response = await fetch(`/orders/${orderId}/share-whatsapp`);
                const data = await response.json();
                if (data.success && data.whatsapp_url) window.open(data.whatsapp_url, '_blank');
                else showToast('Kuna tatizo katika kushiriki!', 'error');
            } catch (error) { showToast('Kuna tatizo katika kushiriki!', 'error'); }
        }
        
        async function copyOrder(orderId) {
            try {
                const response = await fetch(`/orders/${orderId}`);
                const data = await response.json();
                if (data.success) {
                    const order = data.order;
                    let text = `ODA #${order.order_number}\nMteja: ${order.customer_name}\nSimu: ${order.customer_phone}\nJumla: ${formatNumber(order.total)} TZS\n\nBidhaa:\n`;
                    order.items.forEach(item => { 
                        text += `${item.jina}: ${item.idadi} x ${formatNumber(item.bei)} = ${formatNumber(item.total)} TZS\n`; 
                    });
                    await navigator.clipboard.writeText(text);
                    showToast('Maelezo ya oda yamenakiliwa!', 'success');
                }
            } catch (error) { showToast('Kuna tatizo katika kunakili!', 'error'); }
        }
        
        // Event Listeners
        document.getElementById('weka-order-tab')?.addEventListener('click', loadOrders);
        document.getElementById('orderCreateBtn')?.addEventListener('click', () => { resetOrderForm(); document.getElementById('orderModal').style.display = 'flex'; });
        document.getElementById('closeOrderModal')?.addEventListener('click', () => document.getElementById('orderModal').style.display = 'none');
        document.getElementById('orderCancelBtn')?.addEventListener('click', () => document.getElementById('orderModal').style.display = 'none');
        document.getElementById('orderAddProduct')?.addEventListener('click', addProductRow);
        document.getElementById('orderSaveDraft')?.addEventListener('click', () => saveOrder('saved'));
        document.getElementById('orderConfirmSubmit')?.addEventListener('click', (e) => { e.preventDefault(); saveOrder('confirmed'); });
        document.getElementById('orderGlobalDiscount')?.addEventListener('input', calculateOrderTotal);
        document.getElementById('orderStatusFilter')?.addEventListener('change', loadOrders);
        document.getElementById('orderSearch')?.addEventListener('input', () => setTimeout(loadOrders, 500));
        document.getElementById('orderResetFilters')?.addEventListener('click', () => {
            document.getElementById('orderSearch').value = '';
            document.getElementById('orderStatusFilter').value = 'all';
            document.getElementById('orderStartDate').value = '';
            document.getElementById('orderEndDate').value = '';
            loadOrders();
        });
        document.getElementById('closeOrderDetailsModal')?.addEventListener('click', () => document.getElementById('orderDetailsModal').style.display = 'none');
        document.getElementById('orderCloseDetails')?.addEventListener('click', () => document.getElementById('orderDetailsModal').style.display = 'none');
        document.getElementById('closeOrderDeleteModal')?.addEventListener('click', () => document.getElementById('orderDeleteModal').style.display = 'none');
        document.getElementById('orderCancelDelete')?.addEventListener('click', () => document.getElementById('orderDeleteModal').style.display = 'none');
        document.getElementById('orderConfirmDelete')?.addEventListener('click', () => { 
            document.getElementById('orderDeleteModal').style.display = 'none'; 
            deleteOrder(deleteOrderId); 
        });
        document.getElementById('closeOrderConfirmModal')?.addEventListener('click', () => document.getElementById('orderConfirmModal').style.display = 'none');
        document.getElementById('orderConfirmNo')?.addEventListener('click', () => document.getElementById('orderConfirmModal').style.display = 'none');
        document.getElementById('orderConfirmYes')?.addEventListener('click', () => { 
            document.getElementById('orderConfirmModal').style.display = 'none'; 
            if(confirmCallback) confirmCallback(true); 
        });
        document.getElementById('closeOrderActionModal')?.addEventListener('click', () => document.getElementById('orderActionModal').style.display = 'none');
        
        document.getElementById('orderPrint')?.addEventListener('click', () => { if (currentOrderId) window.open(`/orders/${currentOrderId}/generate-invoice`, '_blank'); });
        document.getElementById('orderWhatsApp')?.addEventListener('click', () => { if (currentOrderId) shareWhatsApp(currentOrderId); });
        document.getElementById('orderCopy')?.addEventListener('click', () => { if (currentOrderId) copyOrder(currentOrderId); });
        document.getElementById('orderPDF')?.addEventListener('click', () => { if (currentOrderId) window.open(`/orders/${currentOrderId}/generate-invoice?pdf=1`, '_blank'); });
        
        // Stat card filters
        document.querySelectorAll('.order-stat-card').forEach(card => {
            card.addEventListener('click', () => {
                const status = card.dataset.status;
                if (status && status !== 'all') document.getElementById('orderStatusFilter').value = status;
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