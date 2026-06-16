<!-- resources/views/mauzo/partial-order.blade.php -->
<style>
    /* Order Section Styling - Professional Compact */
    .order-section {
        background: #f8fafc;
        min-height: 100vh;
    }
    
    /* Compact Product Card */
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
        gap: 8px;
    }
    
    @media (min-width: 640px) {
        .product-grid {
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        }
    }
    
    @media (min-width: 1024px) {
        .product-grid {
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        }
    }
    
    .product-card {
        background: white;
        border-radius: 6px;
        padding: 8px;
        text-align: center;
        border: 1px solid #e5e7eb;
        transition: all 0.15s ease;
        cursor: pointer;
        position: relative;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    
    .product-card:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        border-color: #f59e0b;
    }
    
    .product-card .product-image {
        width: 50px;
        height: 50px;
        margin: 0 auto 4px;
        object-fit: contain;
        border-radius: 4px;
        background: #f9fafb;
    }
    
    .product-card .product-name {
        font-size: 11px;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 1px;
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .product-card .product-details {
        font-size: 9px;
        color: #6b7280;
        margin-bottom: 2px;
    }
    
    .product-card .product-details span {
        display: inline-block;
        margin: 0 1px;
        padding: 0 4px;
        background: #f3f4f6;
        border-radius: 3px;
        font-size: 8px;
    }
    
    .product-card .product-price {
        font-size: 12px;
        font-weight: 600;
        color: #f59e0b;
    }
    
    .product-card .add-btn {
        background: #f59e0b;
        color: white;
        border: none;
        border-radius: 4px;
        padding: 2px 8px;
        font-size: 10px;
        cursor: pointer;
        transition: all 0.15s ease;
        margin-top: 2px;
    }
    
    .product-card .add-btn:hover {
        background: #d97706;
    }
    
    .product-card .add-btn:disabled {
        background: #9ca3af;
        cursor: not-allowed;
    }
    
    .product-card .stock-badge {
        position: absolute;
        top: 4px;
        right: 4px;
        font-size: 8px;
        padding: 1px 5px;
        border-radius: 8px;
        font-weight: 600;
    }
    
    .stock-available { background: #d1fae5; color: #065f46; }
    .stock-low { background: #fef3c7; color: #92400e; }
    .stock-out { background: #fee2e2; color: #991b1b; }
    
    /* Search Bar */
    .search-bar {
        background: white;
        border-radius: 6px;
        border: 1px solid #e5e7eb;
        padding: 6px 12px;
        display: flex;
        align-items: center;
        gap: 8px;
        width: 100%;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    
    .search-bar input {
        flex: 1;
        border: none;
        outline: none;
        font-size: 13px;
        color: #1f2937;
        min-width: 0;
    }
    
    .search-bar input::placeholder {
        color: #9ca3af;
    }
    
    .search-bar i {
        color: #9ca3af;
    }
    
    /* Order Stats Bar */
    .order-stats-bar {
        display: flex;
        gap: 6px;
        overflow-x: auto;
        padding: 2px 0;
        margin-bottom: 8px;
        flex-wrap: nowrap;
    }
    
    .stat-item {
        background: white;
        padding: 4px 12px;
        border-radius: 16px;
        border: 1px solid #e5e7eb;
        font-size: 11px;
        font-weight: 500;
        white-space: nowrap;
        display: flex;
        align-items: center;
        gap: 4px;
        flex-shrink: 0;
    }
    
    .stat-item i { font-size: 10px; }
    .stat-total { color: #1f2937; border-color: #d1d5db; }
    .stat-saved { color: #f59e0b; border-color: #fde68a; background: #fffbeb; }
    .stat-confirmed { color: #3b82f6; border-color: #93c5fd; background: #eff6ff; }
    .stat-paid { color: #10b981; border-color: #a7f3d0; background: #ecfdf5; }
    .stat-cancelled { color: #ef4444; border-color: #fca5a5; background: #fef2f2; }
    
    /* Order Panel - Compact */
    .order-panel-wrapper { position: relative; }
    
    .order-panel {
        background: white;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        display: flex;
        flex-direction: column;
        height: auto;
        max-height: 70vh;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    
    @media (min-width: 1024px) {
        .order-panel {
            position: sticky;
            top: 85px;
            height: calc(100vh - 110px);
            max-height: calc(100vh - 110px);
        }
    }
    
    .order-header {
        padding: 8px 12px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-shrink: 0;
    }
    
    .order-header h3 {
        font-size: 14px;
        font-weight: 600;
        color: #1f2937;
    }
    
    .order-header .order-count {
        font-size: 10px;
        color: #6b7280;
        background: #f3f4f6;
        padding: 1px 8px;
        border-radius: 10px;
    }
    
    .order-items {
        flex: 1;
        overflow-y: auto;
        padding: 6px 10px;
        min-height: 60px;
        max-height: 250px;
    }
    
    @media (min-width: 1024px) {
        .order-items {
            max-height: none;
            min-height: 150px;
        }
    }
    
    .order-items::-webkit-scrollbar { width: 3px; }
    .order-items::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 2px; }
    
    .order-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 4px 0;
        border-bottom: 1px solid #f3f4f6;
    }
    
    .order-item:last-child { border-bottom: none; }
    
    .order-item .item-info { flex: 1; min-width: 0; }
    .order-item .item-name { font-size: 12px; font-weight: 500; color: #1f2937; }
    .order-item .item-details { font-size: 10px; color: #6b7280; }
    .order-item .item-price { font-size: 12px; font-weight: 600; color: #f59e0b; }
    
    .order-item .item-qty {
        display: flex;
        align-items: center;
        gap: 3px;
        margin: 0 4px;
    }
    
    .order-item .qty-btn {
        background: #f3f4f6;
        border: none;
        border-radius: 3px;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .order-item .qty-btn:hover { background: #e5e7eb; }
    .order-item .item-total { font-size: 12px; font-weight: 600; color: #1f2937; min-width: 50px; text-align: right; }
    .order-item .remove-btn { color: #9ca3af; transition: all 0.2s ease; background: none; border: none; padding: 1px; cursor: pointer; }
    .order-item .remove-btn:hover { color: #ef4444; }
    
    /* Order Footer - Compact */
    .order-footer {
        padding: 8px 12px;
        border-top: 1px solid #e5e7eb;
        background: #fafafa;
        border-radius: 0 0 8px 8px;
        flex-shrink: 0;
    }
    
    .order-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 4px;
    }
    
    .order-total .label { font-size: 12px; font-weight: 500; color: #4b5563; }
    .order-total .amount { font-size: 16px; font-weight: 700; color: #f59e0b; }
    
    .order-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 3px;
    }
    
    .order-actions button {
        flex: 1;
        min-width: 60px;
        padding: 4px 8px;
        border: none;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 3px;
    }
    
    .btn-primary { background: #f59e0b; color: white; }
    .btn-primary:hover { background: #d97706; }
    .btn-secondary { background: #f3f4f6; color: #4b5563; }
    .btn-secondary:hover { background: #e5e7eb; }
    .btn-success { background: #10b981; color: white; }
    .btn-success:hover { background: #059669; }
    .btn-danger { background: #ef4444; color: white; }
    .btn-danger:hover { background: #dc2626; }
    
    /* Placed Orders - Separate Section at Top */
    .placed-orders-section-top {
        background: white;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        overflow: hidden;
        margin-bottom: 12px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    
    .placed-orders-header-top {
        padding: 8px 12px;
        background: #fafafa;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .placed-orders-header-top h4 {
        font-size: 13px;
        font-weight: 600;
        color: #1f2937;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    
    .placed-orders-header-top .badge {
        font-size: 10px;
        background: #f59e0b;
        color: white;
        padding: 1px 6px;
        border-radius: 10px;
    }
    
    .placed-orders-list-top {
        max-height: 120px;
        overflow-y: auto;
        padding: 4px;
    }
    
    .placed-orders-list-top::-webkit-scrollbar { width: 3px; }
    .placed-orders-list-top::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 2px; }
    
    .placed-order-item-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 4px 8px;
        border-bottom: 1px solid #f3f4f6;
        transition: background 0.2s ease;
        cursor: pointer;
    }
    
    .placed-order-item-top:hover { background: #f9fafb; }
    .placed-order-item-top:last-child { border-bottom: none; }
    
    .placed-order-item-top .order-info { flex: 1; }
    .placed-order-item-top .order-id { font-size: 11px; font-weight: 500; color: #1f2937; }
    .placed-order-item-top .order-date { font-size: 10px; color: #6b7280; }
    
    .placed-order-item-top .order-status {
        font-size: 10px;
        padding: 1px 6px;
        border-radius: 8px;
        font-weight: 500;
    }
    
    .status-saved { background: #fffbeb; color: #f59e0b; border: 1px solid #fde68a; }
    .status-confirmed { background: #eff6ff; color: #3b82f6; border: 1px solid #93c5fd; }
    .status-paid { background: #ecfdf5; color: #10b981; border: 1px solid #a7f3d0; }
    .status-cancelled { background: #fef2f2; color: #ef4444; border: 1px solid #fca5a5; }
    
    /* Order Preview Modal - Professional */
    .order-preview-modal {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 1000;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 12px;
        backdrop-filter: blur(3px);
    }
    
    .order-preview-modal.active { display: flex; }
    
    .order-preview-content {
        background: white;
        border-radius: 10px;
        max-width: 480px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        padding: 16px;
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.15);
        position: relative;
    }
    
    .order-preview-content .close-btn {
        position: absolute;
        top: 8px;
        right: 12px;
        background: none;
        border: none;
        font-size: 18px;
        color: #9ca3af;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .order-preview-content .close-btn:hover { color: #1f2937; }
    
    .order-preview-content .preview-header {
        text-align: center;
        border-bottom: 2px solid #e5e7eb;
        padding-bottom: 8px;
        margin-bottom: 8px;
    }
    
    .order-preview-content .preview-header h3 {
        font-size: 16px;
        font-weight: 700;
        color: #1f2937;
    }
    
    .order-preview-content .preview-header p {
        font-size: 11px;
        color: #6b7280;
    }
    
    .order-preview-content .preview-order-info {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 4px;
        background: #f9fafb;
        padding: 8px;
        border-radius: 6px;
        margin-bottom: 8px;
    }
    
    .order-preview-content .preview-order-info .info-item {
        font-size: 11px;
        color: #4b5563;
    }
    
    .order-preview-content .preview-order-info .info-item strong {
        color: #1f2937;
    }
    
    .order-preview-content .preview-items {
        margin: 8px 0;
        padding: 6px 0;
        border-top: 1px solid #e5e7eb;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .order-preview-content .preview-item {
        display: flex;
        justify-content: space-between;
        padding: 3px 0;
        font-size: 12px;
    }
    
    .order-preview-content .preview-item .item-name {
        flex: 1;
    }
    
    .order-preview-content .preview-item .item-name small {
        color: #6b7280;
        font-size: 10px;
    }
    
    .order-preview-content .preview-total {
        display: flex;
        justify-content: space-between;
        font-size: 15px;
        font-weight: 700;
        color: #f59e0b;
        padding-top: 4px;
    }
    
    .order-preview-content .preview-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        margin-top: 8px;
        border-top: 1px solid #e5e7eb;
        padding-top: 8px;
    }
    
    .order-preview-content .preview-actions button {
        flex: 1;
        min-width: 70px;
        padding: 5px 10px;
        border: none;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 3px;
    }
    
    /* Receipt Styling */
    .receipt-print {
        font-family: 'Courier New', monospace;
        font-size: 11px;
        line-height: 1.5;
        color: #1f2937;
        background: white;
        padding: 8px;
        border: 1px solid #e5e7eb;
        border-radius: 4px;
        margin: 4px 0;
    }
    
    .receipt-print .company-name {
        font-size: 14px;
        font-weight: 700;
        text-align: center;
        margin-bottom: 2px;
    }
    
    .receipt-print .receipt-line {
        border-top: 1px dashed #d1d5db;
        margin: 3px 0;
    }
    
    .receipt-print .receipt-item {
        display: flex;
        justify-content: space-between;
        padding: 1px 0;
    }
    
    .receipt-print .receipt-total {
        display: flex;
        justify-content: space-between;
        font-weight: 700;
        font-size: 13px;
        padding-top: 3px;
        border-top: 1px dashed #d1d5db;
    }
    
    .receipt-print .receipt-footer {
        text-align: center;
        font-size: 10px;
        color: #6b7280;
        margin-top: 4px;
        border-top: 1px dashed #d1d5db;
        padding-top: 4px;
    }
    
    /* Responsive */
    @media (max-width: 640px) {
        .product-grid { grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 6px; }
        .product-card { padding: 6px; }
        .product-card .product-image { width: 40px; height: 40px; }
        .product-card .product-name { font-size: 10px; }
        .order-item .item-name { font-size: 11px; }
        .order-actions button { font-size: 10px; padding: 3px 6px; min-width: 50px; }
        .stat-item { font-size: 10px; padding: 2px 8px; }
        .order-preview-content { padding: 12px; }
        .order-preview-content .preview-order-info { grid-template-columns: 1fr; }
        .placed-orders-list-top { max-height: 80px; }
    }
</style>

<!-- Orders Tab Content -->
<div id="weka-order-tab-content" class="tab-content hidden order-section">
    <div class="container mx-auto px-2 sm:px-4 py-3">
        
        <!-- Order Stats Bar -->
        <div class="order-stats-bar" id="orderStatsBar">
            <div class="stat-item stat-total">
                <i class="fas fa-clipboard-list"></i>
                Total: <span id="totalOrders">0</span>
            </div>
            <div class="stat-item stat-saved">
                <i class="fas fa-save"></i>
                Saved: <span id="savedOrders">0</span>
            </div>
            <div class="stat-item stat-confirmed">
                <i class="fas fa-check-circle"></i>
                Confirmed: <span id="confirmedOrders">0</span>
            </div>
            <div class="stat-item stat-paid">
                <i class="fas fa-money-bill-wave"></i>
                Paid: <span id="paidOrders">0</span>
            </div>
            <div class="stat-item stat-cancelled">
                <i class="fas fa-times-circle"></i>
                Cancelled: <span id="cancelledOrders">0</span>
            </div>
        </div>
        
        <!-- Placed Orders Section - At Top -->
        <div class="placed-orders-section-top" id="placedOrdersSectionTop">
            <div class="placed-orders-header-top">
                <h4>
                    <i class="fas fa-clipboard-list mr-1"></i>
                    Placed Orders
                    <span class="badge" id="placedOrdersCountTop">0</span>
                </h4>
                <button class="text-gray-400 hover:text-gray-600 text-xs" onclick="refreshOrders()">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
            <div class="placed-orders-list-top" id="placedOrdersListTop">
                <div class="text-center text-gray-400 py-3">
                    <p class="text-xs">No placed orders yet</p>
                </div>
            </div>
        </div>
        
        <!-- Main Layout: Product Grid + Order Panel -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
            
            <!-- Left: Product Grid -->
            <div class="lg:col-span-2">
                <!-- Search Bar -->
                <div class="search-bar mb-2">
                    <i class="fas fa-search"></i>
                    <input type="text" id="productSearch" placeholder="Search products by name, type, or barcode...">
                    <i class="fas fa-barcode text-gray-400 cursor-pointer hover:text-gray-600" id="scanBarcodeBtn"></i>
                </div>
                
 <!-- Product Grid -->
<div class="product-grid" id="productGrid">
    @foreach($bidhaa as $product)
    <div class="product-card" 
         data-id="{{ $product->id }}" 
         data-name="{{ $product->jina }}" 
         data-price="{{ $product->bei_kuuza }}" 
         data-stock="{{ $product->idadi }}"
         data-image="{{ $product->image_data_url ?? '' }}">
        
        @php
            // Get image data directly from the product
            $imageData = null;
            if ($product->image) {
                try {
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mimeType = finfo_buffer($finfo, $product->image);
                    finfo_close($finfo);
                    $imageData = 'data:' . $mimeType . ';base64,' . base64_encode($product->image);
                } catch (\Exception $e) {
                    $imageData = null;
                }
            }
        @endphp
        
        @if($imageData)
            <img src="{{ $imageData }}" 
                 alt="{{ $product->jina }}" 
                 class="product-image"
                 loading="lazy"
                 onerror="this.src='https://via.placeholder.com/50x50?text={{ urlencode($product->jina) }}'">
        @else
            <img src="https://via.placeholder.com/50x50?text={{ urlencode($product->jina) }}" 
                 alt="{{ $product->jina }}" 
                 class="product-image"
                 loading="lazy">
        @endif
        
        <div class="product-name">{{ $product->jina }}</div>
        <div class="product-details">
            <span>{{ $product->aina }}</span>
            @if($product->kipimo)
                <span>{{ $product->kipimo }}</span>
            @endif
        </div>
        <div class="product-price">{{ number_format($product->bei_kuuza, 0) }} TZS</div>
        
        @if($product->idadi > 0)
            <button class="add-btn" onclick="addToOrder({{ $product->id }}, '{{ addslashes($product->jina) }}', '{{ addslashes($product->aina) }}', '{{ addslashes($product->kipimo) }}', {{ $product->bei_kuuza }}, {{ $product->idadi }})">
                <i class="fas fa-plus"></i> Add
            </button>
        @else
            <button class="add-btn" disabled>
                <i class="fas fa-times"></i> Out
            </button>
        @endif
        
        <span class="stock-badge {{ $product->idadi > 10 ? 'stock-available' : ($product->idadi > 0 ? 'stock-low' : 'stock-out') }}">
            {{ $product->idadi > 0 ? $product->idadi : '0' }}
        </span>
    </div>
    @endforeach
</div>
            </div>
            
            <!-- Right: Current Order Panel -->
            <div class="lg:col-span-1 order-panel-wrapper">
                <div class="order-panel">
                    <!-- Order Header -->
                    <div class="order-header">
                        <div class="flex items-center gap-2">
                            <h3>Current Order</h3>
                            <span class="order-count" id="orderItemCount">0</span>
                        </div>
                        <button class="text-gray-400 hover:text-red-500 transition text-xs" onclick="clearOrder()" title="Clear Order">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                    
                    <!-- Order Items -->
                    <div class="order-items" id="orderItems">
                        <div class="text-center text-gray-400 py-4">
                            <i class="fas fa-shopping-cart text-xl mb-1"></i>
                            <p class="text-xs">No items in order</p>
                        </div>
                    </div>
                    
                    <!-- Order Footer -->
                    <div class="order-footer">
                        <!-- Subtotal -->
                        <div class="order-total">
                            <span class="label">Subtotal</span>
                            <span class="amount" id="orderSubtotal">0 TZS</span>
                        </div>
                        
                        <!-- Discount -->
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-xs text-gray-600">Discount</span>
                            <div class="flex items-center gap-1">
                                <span class="text-xs text-gray-500">%</span>
                                <input type="number" id="orderDiscount" value="0" min="0" max="100" 
                                       class="w-12 px-1 py-0.5 border border-gray-200 rounded text-xs text-right focus:outline-none focus:ring-1 focus:ring-[#f59e0b]"
                                       onchange="calculateTotal()">
                            </div>
                        </div>
                        
                        <!-- Total -->
                        <div class="order-total mb-1">
                            <span class="label font-bold">Total</span>
                            <span class="amount" id="orderTotal">0 TZS</span>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="order-actions">
                            <button class="btn-success" onclick="processPayment()">
                                <i class="fas fa-money-bill-wave"></i> Pay & Generate
                            </button>
                            <button class="btn-secondary" onclick="saveOrder()">
                                <i class="fas fa-save"></i> Save
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Order Preview Modal -->
<div class="order-preview-modal" id="orderPreviewModal">
    <div class="order-preview-content">
        <button class="close-btn" onclick="closeOrderPreview()">
            <i class="fas fa-times"></i>
        </button>
        
        <div class="preview-header">
            <h3 id="previewOrderNumber">Order #ORD-20250101-0001</h3>
            <p id="previewOrderDate">Date: 01/01/2025 14:30</p>
        </div>
        
        <div class="preview-order-info">
            <div class="info-item">
                <strong>Customer:</strong> <span id="previewCustomerName">Walk-in Customer</span>
            </div>
            <div class="info-item">
                <strong>Phone:</strong> <span id="previewCustomerPhone">-</span>
            </div>
            <div class="info-item">
                <strong>Status:</strong> <span id="previewOrderStatus" class="order-status status-saved">Saved</span>
            </div>
            <div class="info-item">
                <strong>Items:</strong> <span id="previewItemCount">0</span>
            </div>
        </div>
        
        <!-- Order Receipt -->
        <div class="receipt-print" id="orderReceiptContent">
            <div class="company-name" id="receiptCompanyName">{{ $companyName ?? 'Mauzo Sheet' }}</div>
            <div style="text-align:center;font-size:10px;margin-bottom:2px;">Your Trusted Shop</div>
            <div style="text-align:center;font-size:10px;margin-bottom:2px;">Order #ORD-20250101-0001</div>
            <div style="text-align:center;font-size:10px;margin-bottom:4px;">Date: 01/01/2025 14:30</div>
            <div class="receipt-line"></div>
            <div style="font-size:10px;margin:2px 0;">
                <strong>Customer:</strong> Walk-in Customer
            </div>
            <div style="font-size:10px;margin:2px 0;">
                <strong>Phone:</strong> -
            </div>
            <div style="font-size:10px;margin:2px 0;">
                <strong>Status:</strong> Saved
            </div>
            <div class="receipt-line"></div>
            <div id="receiptItemsList"></div>
            <div class="receipt-line"></div>
            <div id="receiptTotals"></div>
            <div class="receipt-footer">
                Asante kwa kununua!
            </div>
        </div>
        
        <div class="preview-actions" id="previewActions">
            <button class="btn-success" onclick="previewPayOrder()">
                <i class="fas fa-money-bill-wave"></i> Pay
            </button>
            <button class="btn-success" style="background:#25D366;" onclick="previewWhatsAppOrder()">
                <i class="fab fa-whatsapp"></i> WhatsApp
            </button>
            <button class="btn-danger" onclick="previewDeleteOrder()">
                <i class="fas fa-trash"></i> Delete
            </button>
            <button class="btn-primary" onclick="previewDownloadOrder()">
                <i class="fas fa-download"></i> Download
            </button>
            <button class="btn-secondary" onclick="closeOrderPreview()">
                <i class="fas fa-times"></i> Close
            </button>
        </div>
    </div>
</div>

<script>
// Order Management System
let orderItems = [];
let orderTotal = 0;
let orderSubtotal = 0;
let placedOrders = [];
let currentPreviewOrder = null;
let companyName = '{{ $companyName ?? 'Mauzo Sheet' }}';

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    loadPlacedOrders();
    updateOrderStats();
});

// Real-time search
document.getElementById('productSearch').addEventListener('input', function() {
    const search = this.value.toLowerCase().trim();
    document.querySelectorAll('.product-card').forEach(card => {
        const name = card.dataset.name.toLowerCase();
        const aina = card.querySelector('.product-details span:first-child')?.textContent.toLowerCase() || '';
        const kipimo = card.querySelector('.product-details span:last-child')?.textContent.toLowerCase() || '';
        card.style.display = (search === '' || name.includes(search) || aina.includes(search) || kipimo.includes(search)) ? '' : 'none';
    });
});

// Add product to order
function addToOrder(id, name, aina, kipimo, price, stock) {
    if (stock <= 0) {
        showNotification('Product is out of stock!', 'error');
        return;
    }
    
    const existingItem = orderItems.find(item => item.id === id);
    
    if (existingItem) {
        if (existingItem.qty >= stock) {
            showNotification('Not enough stock available!', 'error');
            return;
        }
        existingItem.qty += 1;
        existingItem.total = existingItem.qty * existingItem.price;
    } else {
        orderItems.push({
            id: id,
            name: name,
            aina: aina,
            kipimo: kipimo,
            price: price,
            qty: 1,
            total: price
        });
    }
    
    updateOrderDisplay();
    showNotification(`Added ${name} to order`, 'success');
}

// Update order display
function updateOrderDisplay() {
    const container = document.getElementById('orderItems');
    const countDisplay = document.getElementById('orderItemCount');
    
    if (orderItems.length === 0) {
        container.innerHTML = `
            <div class="text-center text-gray-400 py-4">
                <i class="fas fa-shopping-cart text-xl mb-1"></i>
                <p class="text-xs">No items in order</p>
            </div>
        `;
        countDisplay.textContent = '0';
        document.getElementById('orderSubtotal').textContent = '0 TZS';
        document.getElementById('orderTotal').textContent = '0 TZS';
        return;
    }
    
    let html = '';
    orderSubtotal = 0;
    
    orderItems.forEach((item, index) => {
        orderSubtotal += item.total;
        html += `
            <div class="order-item">
                <div class="item-info">
                    <div class="item-name">${item.name}</div>
                    <div class="item-details">
                        ${item.aina ? item.aina : ''} ${item.kipimo ? '• ' + item.kipimo : ''}
                        <span class="ml-1">${item.qty} × ${formatCurrency(item.price)}</span>
                    </div>
                </div>
                <div class="flex items-center gap-1">
                    <div class="item-qty">
                        <button class="qty-btn" onclick="updateQty(${index}, -1)">
                            <i class="fas fa-minus"></i>
                        </button>
                        <span class="text-xs font-medium w-4 text-center">${item.qty}</span>
                        <button class="qty-btn" onclick="updateQty(${index}, 1)">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <div class="item-total">${formatCurrency(item.total)}</div>
                    <button class="remove-btn" onclick="removeItem(${index})">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
    countDisplay.textContent = orderItems.length;
    document.getElementById('orderSubtotal').textContent = formatCurrency(orderSubtotal);
    calculateTotal();
}

// Update quantity
function updateQty(index, change) {
    const item = orderItems[index];
    const newQty = item.qty + change;
    
    if (newQty <= 0) {
        orderItems.splice(index, 1);
    } else {
        const card = document.querySelector(`.product-card[data-id="${item.id}"]`);
        const stock = card ? parseInt(card.dataset.stock) : 999;
        if (newQty > stock) {
            showNotification('Not enough stock available!', 'error');
            return;
        }
        item.qty = newQty;
        item.total = item.qty * item.price;
    }
    
    updateOrderDisplay();
}

// Remove item
function removeItem(index) {
    orderItems.splice(index, 1);
    updateOrderDisplay();
}

// Calculate total with discount
function calculateTotal() {
    const discountPercent = parseFloat(document.getElementById('orderDiscount').value) || 0;
    const discountAmount = (orderSubtotal * discountPercent) / 100;
    orderTotal = orderSubtotal - discountAmount;
    
    document.getElementById('orderTotal').textContent = formatCurrency(orderTotal);
}

// Clear order
function clearOrder() {
    if (orderItems.length === 0) return;
    if (confirm('Clear all items from current order?')) {
        orderItems = [];
        updateOrderDisplay();
        showNotification('Order cleared', 'info');
    }
}

// Save order
function saveOrder() {
    if (orderItems.length === 0) {
        showNotification('No items to save', 'error');
        return;
    }
    
    const orderData = {
        items: orderItems.map(item => ({
            id: item.id,
            name: item.name,
            price: item.price,
            qty: item.qty,
            total: item.total
        })),
        subtotal: orderSubtotal,
        discount: parseFloat(document.getElementById('orderDiscount').value) || 0,
        total: orderTotal,
        status: 'saved',
        customer_name: 'Walk-in Customer'
    };
    
    fetch('/orders', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(orderData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            loadPlacedOrders();
            updateOrderStats();
            orderItems = [];
            updateOrderDisplay();
            document.getElementById('placedOrdersSectionTop').style.display = 'block';
        } else {
            showNotification(data.message || 'Failed to save order', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Failed to save order', 'error');
    });
}

// Process payment
function processPayment() {
    if (orderItems.length === 0) {
        showNotification('No items to process', 'error');
        return;
    }
    
    const orderData = {
        items: orderItems.map(item => ({
            id: item.id,
            name: item.name,
            price: item.price,
            qty: item.qty,
            total: item.total
        })),
        subtotal: orderSubtotal,
        discount: parseFloat(document.getElementById('orderDiscount').value) || 0,
        total: orderTotal,
        status: 'paid',
        customer_name: 'Walk-in Customer'
    };
    
    fetch('/orders', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(orderData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Order paid and generated successfully!', 'success');
            loadPlacedOrders();
            updateOrderStats();
            orderItems = [];
            updateOrderDisplay();
            document.getElementById('placedOrdersSectionTop').style.display = 'block';
        } else {
            showNotification(data.message || 'Failed to process payment', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Failed to process payment', 'error');
    });
}

// Load placed orders
function loadPlacedOrders() {
    fetch('/orders/placed', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            placedOrders = data.data;
            displayPlacedOrders();
            updateOrderStats();
        }
    })
    .catch(error => console.error('Error loading orders:', error));
}

// Display placed orders at top
function displayPlacedOrders() {
    const list = document.getElementById('placedOrdersListTop');
    const countBadge = document.getElementById('placedOrdersCountTop');
    
    countBadge.textContent = placedOrders.length;
    
    if (placedOrders.length === 0) {
        list.innerHTML = `<div class="text-center text-gray-400 py-3"><p class="text-xs">No placed orders yet</p></div>`;
        return;
    }
    
    let html = '';
    placedOrders.forEach(order => {
        let statusClass = 'status-saved';
        let statusLabel = 'Saved';
        
        if (order.status === 'paid') {
            statusClass = 'status-paid';
            statusLabel = 'Paid';
        } else if (order.status === 'confirmed') {
            statusClass = 'status-confirmed';
            statusLabel = 'Confirmed';
        } else if (order.status === 'cancelled') {
            statusClass = 'status-cancelled';
            statusLabel = 'Cancelled';
        }
        
        html += `
            <div class="placed-order-item-top">
                <div class="order-info" onclick="previewOrder('${order.id}')">
                    <div class="order-id">${order.order_number}</div>
                    <div class="order-date">${new Date(order.created_at).toLocaleString()} • ${order.items ? order.items.length : 0} items</div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="order-status ${statusClass}">${statusLabel}</span>
                    <span class="font-medium text-[#f59e0b] text-xs">${formatCurrency(order.total)}</span>
                    <button class="text-green-500 hover:text-green-700 transition text-xs" onclick="quickWhatsAppOrder('${order.id}')" title="Share via WhatsApp">
                        <i class="fab fa-whatsapp"></i>
                    </button>
                </div>
            </div>
        `;
    });
    
    list.innerHTML = html;
}

// Refresh orders
function refreshOrders() {
    loadPlacedOrders();
    showNotification('Orders refreshed', 'info');
}

// Preview order
function previewOrder(orderId) {
    const order = placedOrders.find(o => o.id == orderId || o.order_number === orderId);
    if (!order) {
        showNotification('Order not found', 'error');
        return;
    }
    
    currentPreviewOrder = order;
    
    // Populate preview
    document.getElementById('previewOrderNumber').textContent = `Order #${order.order_number}`;
    document.getElementById('previewOrderDate').textContent = `Date: ${new Date(order.created_at).toLocaleString()}`;
    document.getElementById('previewCustomerName').textContent = order.customer_name || 'Walk-in Customer';
    document.getElementById('previewCustomerPhone').textContent = order.customer_phone || '-';
    document.getElementById('previewItemCount').textContent = order.items ? order.items.length : 0;
    
    const statusEl = document.getElementById('previewOrderStatus');
    let statusLabel = 'Saved';
    let statusClass = 'status-saved';
    
    if (order.status === 'paid') {
        statusLabel = 'Paid';
        statusClass = 'status-paid';
    } else if (order.status === 'confirmed') {
        statusLabel = 'Confirmed';
        statusClass = 'status-confirmed';
    } else if (order.status === 'cancelled') {
        statusLabel = 'Cancelled';
        statusClass = 'status-cancelled';
    }
    
    statusEl.textContent = statusLabel;
    statusEl.className = `order-status ${statusClass}`;
    
    // Populate receipt
    generateOrderReceipt(order);
    
    // Show actions based on status
    const actionsContainer = document.getElementById('previewActions');
    if (order.status === 'saved' || order.status === 'confirmed') {
        actionsContainer.innerHTML = `
            <button class="btn-success" onclick="previewPayOrder()">
                <i class="fas fa-money-bill-wave"></i> Pay
            </button>
            <button class="btn-success" style="background:#25D366;" onclick="previewWhatsAppOrder()">
                <i class="fab fa-whatsapp"></i> WhatsApp
            </button>
            <button class="btn-danger" onclick="previewDeleteOrder()">
                <i class="fas fa-trash"></i> Delete
            </button>
            <button class="btn-primary" onclick="previewDownloadOrder()">
                <i class="fas fa-download"></i> Download
            </button>
            <button class="btn-secondary" onclick="closeOrderPreview()">
                <i class="fas fa-times"></i> Close
            </button>
        `;
    } else {
        actionsContainer.innerHTML = `
            <button class="btn-success" style="background:#25D366;" onclick="previewWhatsAppOrder()">
                <i class="fab fa-whatsapp"></i> WhatsApp
            </button>
            <button class="btn-primary" onclick="previewDownloadOrder()">
                <i class="fas fa-download"></i> Download
            </button>
            <button class="btn-danger" onclick="previewDeleteOrder()">
                <i class="fas fa-trash"></i> Delete
            </button>
            <button class="btn-secondary" onclick="closeOrderPreview()">
                <i class="fas fa-times"></i> Close
            </button>
        `;
    }
    
    document.getElementById('orderPreviewModal').classList.add('active');
}

// Share order via WhatsApp from preview
function previewWhatsAppOrder() {
    if (!currentPreviewOrder) return;
    
    const order = currentPreviewOrder;
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
        const total = item.total || (item.price * item.qty);
        message += `• ${item.jina || item.name}`;
        if (item.aina) message += ` (${item.aina})`;
        if (item.kipimo) message += ` - ${item.kipimo}`;
        message += `\n  ${item.qty || item.idadi} × ${formatCurrency(item.price)} = ${formatCurrency(total)}\n`;
    });
    
    const discount = order.discount || 0;
    const total = order.total || orderItems.reduce((sum, item) => sum + (item.total || (item.price * item.qty)), 0);
    
    message += `━━━━━━━━━━━━━━━━━━━━━━━━\n`;
    if (discount > 0) {
        message += `Subtotal: ${formatCurrency(total + discount)}\n`;
        message += `Discount: -${formatCurrency(discount)}\n`;
    }
    message += `*TOTAL: ${formatCurrency(total)}*\n`;
    message += `━━━━━━━━━━━━━━━━━━━━━━━━\n`;
    message += `Asante kwa kununua! 🛍️`;
    
    // Encode message for URL
    const encodedMessage = encodeURIComponent(message);
    
    // Get customer phone from order
    let phoneNumber = order.customer_phone || '';
    if (phoneNumber) {
        // Clean phone number
        phoneNumber = phoneNumber.replace(/[^0-9]/g, '');
        if (phoneNumber.startsWith('0')) {
            phoneNumber = '255' + phoneNumber.substring(1);
        } else if (phoneNumber.startsWith('7')) {
            phoneNumber = '255' + phoneNumber;
        } else if (!phoneNumber.startsWith('255')) {
            phoneNumber = '255' + phoneNumber;
        }
    }
    
    // Open WhatsApp
    let whatsappUrl;
    if (phoneNumber && phoneNumber.length === 12) {
        whatsappUrl = `https://wa.me/${phoneNumber}?text=${encodedMessage}`;
    } else {
        whatsappUrl = `https://wa.me/?text=${encodedMessage}`;
    }
    
    window.open(whatsappUrl, '_blank');
}

// Quick WhatsApp share from orders list
function quickWhatsAppOrder(orderId) {
    const order = placedOrders.find(o => o.id == orderId || o.order_number === orderId);
    if (!order) {
        showNotification('Order not found', 'error');
        return;
    }
    
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
        const total = item.total || (item.price * item.qty);
        message += `• ${item.jina || item.name}`;
        if (item.aina) message += ` (${item.aina})`;
        if (item.kipimo) message += ` - ${item.kipimo}`;
        message += `\n  ${item.qty || item.idadi} × ${formatCurrency(item.price)} = ${formatCurrency(total)}\n`;
    });
    
    const discount = order.discount || 0;
    const total = order.total || orderItems.reduce((sum, item) => sum + (item.total || (item.price * item.qty)), 0);
    
    message += `━━━━━━━━━━━━━━━━━━━━━━━━\n`;
    if (discount > 0) {
        message += `Subtotal: ${formatCurrency(total + discount)}\n`;
        message += `Discount: -${formatCurrency(discount)}\n`;
    }
    message += `*TOTAL: ${formatCurrency(total)}*\n`;
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
        } else if (!phoneNumber.startsWith('255')) {
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

// Generate order receipt
function generateOrderReceipt(order) {
    const receiptContent = document.getElementById('orderReceiptContent');
    const items = order.items || [];
    let subtotal = 0;
    
    // Update header
    document.getElementById('receiptCompanyName').textContent = companyName;
    
    // Items list
    let itemsHtml = '';
    items.forEach(item => {
        const total = item.total || (item.price * item.qty);
        subtotal += total;
        itemsHtml += `
            <div class="receipt-item">
                <span>${item.jina || item.name}</span>
                <span>${formatCurrency(total)}</span>
            </div>
            <div style="font-size:9px;color:#6b7280;padding-left:4px;">
                ${item.aina ? item.aina + ' ' : ''}${item.kipimo ? item.kipimo + ' ' : ''}× ${item.qty || item.idadi}
            </div>
        `;
    });
    document.getElementById('receiptItemsList').innerHTML = itemsHtml;
    
    // Totals
    const discount = order.discount || 0;
    const total = order.total || subtotal;
    let totalsHtml = '';
    if (discount > 0) {
        totalsHtml += `
            <div class="receipt-item">
                <span>Subtotal</span>
                <span>${formatCurrency(subtotal)}</span>
            </div>
            <div class="receipt-item">
                <span>Discount</span>
                <span>-${formatCurrency(discount)}</span>
            </div>
        `;
    }
    totalsHtml += `
        <div class="receipt-total">
            <span>TOTAL</span>
            <span>${formatCurrency(total)}</span>
        </div>
    `;
    document.getElementById('receiptTotals').innerHTML = totalsHtml;
}

// Pay order from preview
function previewPayOrder() {
    if (!currentPreviewOrder) return;
    
    fetch(`/orders/${currentPreviewOrder.id}/status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ status: 'paid' })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Order paid successfully!', 'success');
            closeOrderPreview();
            loadPlacedOrders();
            updateOrderStats();
        } else {
            showNotification(data.message || 'Failed to pay order', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Failed to pay order', 'error');
    });
}

// Delete order from preview
function previewDeleteOrder() {
    if (!currentPreviewOrder) return;
    if (!confirm('Delete this order?')) return;
    
    fetch(`/orders/${currentPreviewOrder.id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Order deleted successfully!', 'success');
            closeOrderPreview();
            loadPlacedOrders();
            updateOrderStats();
        } else {
            showNotification(data.message || 'Failed to delete order', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Failed to delete order', 'error');
    });
}

// Download order
function previewDownloadOrder() {
    if (!currentPreviewOrder) return;
    
    const content = document.getElementById('orderReceiptContent').innerHTML;
    const fullContent = `
        <div style="font-family:'Courier New',monospace;padding:16px;max-width:400px;margin:0 auto;background:white;">
            ${content}
        </div>
    `;
    
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
        <head>
            <title>Order #${currentPreviewOrder.order_number}</title>
            <style>
                body { font-family: 'Courier New', monospace; padding: 16px; }
                .company-name { font-size: 14px; font-weight: 700; text-align: center; margin-bottom: 2px; }
                .receipt-line { border-top: 1px dashed #d1d5db; margin: 3px 0; }
                .receipt-item { display: flex; justify-content: space-between; padding: 1px 0; font-size: 11px; }
                .receipt-total { display: flex; justify-content: space-between; font-weight: 700; font-size: 13px; padding-top: 3px; border-top: 1px dashed #d1d5db; }
                .receipt-footer { text-align: center; font-size: 10px; color: #6b7280; margin-top: 4px; border-top: 1px dashed #d1d5db; padding-top: 4px; }
            </style>
        </head>
        <body>
            ${fullContent}
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

// Close order preview
function closeOrderPreview() {
    document.getElementById('orderPreviewModal').classList.remove('active');
    currentPreviewOrder = null;
}

// Update order statistics
function updateOrderStats() {
    const total = placedOrders.length;
    const saved = placedOrders.filter(o => o.status === 'saved').length;
    const confirmed = placedOrders.filter(o => o.status === 'confirmed').length;
    const paid = placedOrders.filter(o => o.status === 'paid').length;
    const cancelled = placedOrders.filter(o => o.status === 'cancelled').length;
    
    document.getElementById('totalOrders').textContent = total;
    document.getElementById('savedOrders').textContent = saved;
    document.getElementById('confirmedOrders').textContent = confirmed;
    document.getElementById('paidOrders').textContent = paid;
    document.getElementById('cancelledOrders').textContent = cancelled;
}

// Format currency
function formatCurrency(amount) {
    return amount.toLocaleString('en-TZ', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) + ' TZS';
}

// Show notification
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
    notification.className = `fixed top-4 right-4 z-50 px-4 py-2 rounded-lg shadow-lg text-white ${bgColor} text-sm`;
    notification.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} mr-2"></i>${message}`;
    document.body.appendChild(notification);
    setTimeout(() => { notification.style.opacity = '0'; setTimeout(() => notification.remove(), 300); }, 3000);
}
</script>