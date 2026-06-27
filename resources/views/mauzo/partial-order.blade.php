<style>
/* =============================================
   TAB CONTENT - Hidden by default
   ============================================= */
.order-tab-content { display: none; }
.order-tab-content.active {
    display: block;
    animation: oFadeIn 0.22s ease;
}
@keyframes oFadeIn {
    from { opacity:0; transform:translateY(6px); }
    to   { opacity:1; transform:translateY(0); }
}

/* =============================================
   INTERNAL TABS for Weka Order page
   ============================================= */
.o-internal-tabs {
    display: flex;
    gap: 4px;
    background: #f3f4f6;
    padding: 4px;
    border-radius: 10px;
    margin-bottom: 12px;
}
.o-internal-tab {
    flex: 1;
    padding: 8px 12px;
    border: none;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    color: #6b7280;
    background: transparent;
    cursor: pointer;
    transition: all 0.15s;
    text-align: center;
}
.o-internal-tab:hover {
    color: #374151;
}
.o-internal-tab.active {
    background: #fff;
    color: #065f46;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    font-weight: 600;
}
.o-internal-tab .badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #ef4444;
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    border-radius: 50%;
    min-width: 18px;
    height: 18px;
    padding: 0 4px;
    margin-left: 4px;
}

/* =============================================
   PRODUCT CARDS
   ============================================= */
.order-product-card {
    cursor: pointer;
    border: 2px solid #e5e7eb;
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    transition: transform 0.15s, box-shadow 0.15s, border-color 0.15s;
}
.order-product-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(0,0,0,0.1);
    border-color: #10b981;
}
.order-product-card.oos { opacity:0.5; pointer-events:none; }
.opc-img {
    width:100%; height:110px;
    object-fit:cover; display:block;
    background:#f3f4f6;
}
.opc-ph {
    width:100%; height:110px;
    display:flex; align-items:center; justify-content:center;
    background:#f3f4f6; color:#9ca3af;
}
@media (min-width:768px){
    .opc-img,.opc-ph { height:140px; }
}

/* =============================================
   PRODUCT GRID
   ============================================= */
.order-grid {
    display:grid;
    grid-template-columns: repeat(auto-fill, minmax(130px,1fr));
    gap:10px;
}
@media (max-width:640px){ .order-grid { grid-template-columns:repeat(2,1fr); gap:8px; } }
@media (min-width:768px){ .order-grid { grid-template-columns:repeat(auto-fill,minmax(160px,1fr)); } }
.order-products-scroll { max-height:45vh; overflow-y:auto; }
.order-products-scroll::-webkit-scrollbar{width:4px;}
.order-products-scroll::-webkit-scrollbar-thumb{background:#d1d5db;border-radius:2px;}

/* =============================================
   CART MODAL - Centered at top
   ============================================= */
.o-cart-backdrop {
    position:fixed; inset:0;
    background:rgba(0,0,0,0.45);
    z-index:400;
    display:none;
    align-items:flex-start;
    justify-content:center;
    padding:20px 12px;
    overflow-y:auto;
}
.o-cart-backdrop.open { display:flex; }
.o-cart-box {
    background:#fff;
    border-radius:16px;
    width:100%;
    max-width:520px;
    max-height:85vh;
    display:flex;
    flex-direction:column;
    box-shadow:0 24px 60px rgba(0,0,0,0.25);
    animation:oFadeIn 0.2s ease;
    margin-top:10px;
}
.o-cart-head {
    padding:14px 16px 12px;
    border-bottom:1px solid #f3f4f6;
    display:flex; align-items:center; justify-content:space-between;
    flex-shrink:0;
}
.o-cart-items { overflow-y:auto; flex:1; padding:0 16px; }
.o-cart-items::-webkit-scrollbar{width:4px;}
.o-cart-items::-webkit-scrollbar-thumb{background:#d1d5db;border-radius:2px;}
.o-cart-foot {
    padding:12px 16px 14px;
    border-top:1px solid #f3f4f6;
    flex-shrink:0;
}
.cart-row {
    display:flex; align-items:center; gap:8px;
    padding:8px 0; border-bottom:1px solid #f9fafb;
}
.cart-row:last-child{border:none;}
.cart-thumb {
    width:46px; height:46px; border-radius:8px;
    overflow:hidden; flex-shrink:0;
    background:#f3f4f6;
    display:flex; align-items:center; justify-content:center;
    color:#9ca3af;
}
.cart-thumb img{width:100%;height:100%;object-fit:cover;}
.qty-btn {
    width:26px;height:26px;border-radius:50%;
    border:1.5px solid #d1d5db;background:#fff;
    display:flex;align-items:center;justify-content:center;
    cursor:pointer;color:#374151;font-size:12px;
    transition:background 0.12s;
}
.qty-btn:hover{background:#f3f4f6;}

/* =============================================
   STATUS BADGES
   ============================================= */
.o-badge{padding:2px 10px;border-radius:9999px;font-size:11px;font-weight:600;display:inline-block;}
.b-saved    {background:#fef3c7;color:#92400e;}
.b-confirmed{background:#dbeafe;color:#1e40af;}
.b-paid     {background:#d1fae5;color:#065f46;}
.b-cancelled{background:#fee2e2;color:#991b1b;}

/* =============================================
   ORDERS TABLE
   ============================================= */
.order-table-wrap{overflow-x:auto;-webkit-overflow-scrolling:touch;}
.order-table-wrap table{min-width:580px;width:100%;}
@media (max-width:640px){
    .order-table-wrap table{min-width:480px;font-size:12px;}
    .order-table-wrap td,.order-table-wrap th{padding:5px 7px;}
}

/* =============================================
   FILTER BUTTONS
   ============================================= */
.o-fbtn {
    padding:5px 12px; border-radius:8px; font-size:12px; font-weight:500;
    border:2px solid #d1d5db; color:#6b7280; background:#fff;
    cursor:pointer; transition:all 0.13s; white-space:nowrap;
}
.o-fbtn:hover,.o-fbtn.on{ border-color:#10b981; color:#065f46; background:#f0fdf4; }
.o-fbtn.on{ font-weight:600; }

/* =============================================
   CUSTOMER MODAL TABS
   ============================================= */
.ctab-btn{
    flex:1; padding:9px; border:none; background:transparent;
    font-size:13px; font-weight:500; color:#6b7280;
    border-bottom:2px solid transparent; cursor:pointer; transition:all 0.13s;
}
.ctab-btn.on{color:#10b981;border-bottom-color:#10b981;font-weight:600;}
.ctab-pane{display:none;}
.ctab-pane.on{display:block;}

/* =============================================
   ORANGE ADD BUTTON
   ============================================= */
.btn-add-orange {
    background: #f59e0b !important;
    border-color: #d97706 !important;
    color: #fff !important;
}
.btn-add-orange:hover {
    background: #d97706 !important;
    border-color: #b45309 !important;
}
.btn-add-orange i {
    color: #fff;
}

/* =============================================
   TOASTS - Centered at top
   ============================================= */
#o-toast-wrap{
    position:fixed; top:20px; left:50%; transform:translateX(-50%);
    z-index:9999; display:flex; flex-direction:column; gap:8px;
    pointer-events:none; align-items:center;
    max-width:90%;
}
.o-toast{
    padding:12px 24px; border-radius:12px;
    font-size:14px; font-weight:500; color:#fff;
    box-shadow:0 6px 24px rgba(0,0,0,0.15);
    animation:oFadeIn 0.25s ease;
    max-width:400px;
    text-align:center;
    pointer-events:auto;
}
.ot-success{background:#10b981;}
.ot-error  {background:#ef4444;}
.ot-info   {background:#3b82f6;}
.ot-warning{background:#f59e0b;}

/* =============================================
   CART BADGE - Always visible with count
   ============================================= */
#cart-badge {
    display: inline-flex !important;
    align-items: center;
    justify-content: center;
    background: #ef4444;
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    border-radius: 50%;
    min-width: 20px;
    height: 20px;
    padding: 0 4px;
    line-height: 1;
}

/* =============================================
   RED ALARM BADGE - For unpaid orders count
   ============================================= */
.o-new-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #ef4444;
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    border-radius: 50%;
    min-width: 18px;
    height: 18px;
    padding: 0 4px;
    margin-left: 4px;
    animation: oPulse 1.2s infinite;
}
@keyframes oPulse{
    0%,100%{transform:scale(1);box-shadow:0 0 0 0 rgba(239,68,68,0.5);}
    50%{transform:scale(1.15);box-shadow:0 0 0 6px rgba(239,68,68,0);}
}

/* =============================================
   GENERAL MODALS - Centered at top
   ============================================= */
.o-modal-wrap {
    position:fixed; inset:0;
    z-index:500;
    display:flex; align-items:flex-start;
    justify-content:center;
    padding:20px 12px;
    background:rgba(0,0,0,0.5);
    overflow-y:auto;
}
.o-modal-wrap.hidden { display:none!important; }
.o-modal-box {
    background:#fff; border-radius:14px;
    width:100%; max-width:460px;
    max-height:85vh; overflow-y:auto;
    box-shadow:0 20px 60px rgba(0,0,0,0.2);
    animation:oFadeIn 0.2s ease;
    margin-top:10px;
}

/* =============================================
   MOBILE RESPONSIVE
   ============================================= */
@media (max-width: 640px) {
    .order-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 6px;
    }
    .opc-img, .opc-ph {
        height: 80px;
    }
    .order-product-card .p-2 {
        padding: 6px;
    }
    .order-product-card .text-xs {
        font-size: 10px;
    }
    .order-product-card .text-sm {
        font-size: 11px;
    }
    .o-cart-box, .o-modal-box {
        max-width: 100%;
        margin: 8px;
        max-height: 90vh;
    }
    .o-internal-tab {
        font-size: 11px;
        padding: 6px 8px;
    }
    .o-fbtn {
        padding: 4px 10px;
        font-size: 11px;
    }
    .order-table-wrap table {
        min-width: 400px;
        font-size: 10px;
    }
    .order-table-wrap td, .order-table-wrap th {
        padding: 4px 6px;
    }
    .flex-wrap.gap-1 > span {
        font-size: 10px !important;
        padding: 2px 6px !important;
    }
    .flex-wrap.gap-1 b {
        font-size: 11px !important;
    }
    .o-toast {
        font-size: 13px;
        padding: 10px 18px;
        max-width: 90%;
    }
}
</style>

<!-- Toast wrapper - Centered at top -->
<div id="o-toast-wrap"></div>

<!-- ============================================ -->
<!-- SINGLE TAB: Weka Order (contains both views) -->
<!-- ============================================ -->
<div id="weka-order-tab-content" class="order-tab-content">
    <div class="bg-white rounded-xl shadow border border-gray-200 p-3 md:p-4">

        <!-- Header row -->
        <div class="flex flex-wrap items-center justify-between mb-3 gap-2">
            <div>
                <h2 class="text-base md:text-lg font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-clipboard-list text-emerald-600"></i>
                    Weka Order
                </h2>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="oOpenCart()"
                        class="relative bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg text-sm font-semibold flex items-center gap-2 transition">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="hidden sm:inline">Kikapu</span>
                    <span id="cart-badge">0</span>
                </button>
                <button onclick="oClearCart()"
                        class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-2 py-1.5 rounded-lg text-xs transition" title="Futa kikapu">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- INTERNAL TABS: Weka Order | Orders         -->
        <!-- ========================================== -->
        <div class="o-internal-tabs">
            <button class="o-internal-tab active" onclick="oSwitchInternalTab('place', this)">
                <i class="fas fa-plus-circle mr-1"></i> Weka Order
            </button>
            <button class="o-internal-tab" onclick="oSwitchInternalTab('list', this)">
                <i class="fas fa-list-alt mr-1"></i> Orders
                <span id="new-order-badge-internal" class="badge hidden">0</span>
            </button>
        </div>

        <!-- ========================================== -->
        <!-- PANEL 1: Weka Order (Place Order)          -->
        <!-- ========================================== -->
        <div id="o-panel-place" class="o-internal-panel">

            <!-- Product filters -->
            <div class="flex flex-wrap gap-2 mb-3">
                <div class="relative flex-1 min-w-[140px]">
                    <i class="fas fa-search absolute left-3 top-2.5 text-gray-400 text-sm"></i>
                    <input type="text" id="oprod-search" placeholder="Tafuta bidhaa..."
                           class="w-full pl-9 pr-3 py-2 border border-gray-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-500"
                           oninput="oProdSearch(this.value)">
                </div>
                <button onclick="oProdFilter('all',this)" class="o-fbtn on">Zote</button>
                <button onclick="oProdFilter('jumla',this)" class="o-fbtn">Jumla</button>
                <button onclick="oProdFilter('low_stock',this)" class="o-fbtn border-yellow-400 text-yellow-600">
                    <i class="fas fa-exclamation-triangle"></i> Hisa ndogo
                </button>
            </div>

            <!-- Product grid -->
            <div class="order-products-scroll">
                <div class="order-grid" id="order-product-grid">
                    @forelse($bidhaa as $product)
                        @php
                            $imgUrl = $product->image_data_url ?? null;
                            $oos    = $product->idadi <= 0;
                        @endphp
                        <div class="order-product-card {{ $oos ? 'oos' : '' }}"
                             data-id="{{ $product->id }}"
                             data-name="{{ $product->jina }}"
                             data-price="{{ $product->bei_kuuza }}"
                             data-wholesale="{{ $product->bei_uzo_jumla ?? 0 }}"
                             data-stock="{{ $product->idadi }}"
                             data-aina="{{ $product->aina ?? '' }}"
                             data-kipimo="{{ $product->kipimo ?? '' }}"
                             data-image="{{ $imgUrl ?? '' }}"
                             onclick="oAddToCart(this)">

                            <!-- Image -->
                            <div class="relative">
                                @if($imgUrl)
                                    <img src="{{ $imgUrl }}"
                                         alt="{{ $product->jina }}"
                                         class="opc-img"
                                         loading="lazy"
                                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                                    <div class="opc-ph" style="display:none;">
                                        <i class="fas fa-box text-3xl"></i>
                                    </div>
                                @else
                                    <div class="opc-ph">
                                        <i class="fas fa-box text-3xl"></i>
                                    </div>
                                @endif
                                @if($oos)
                                    <span class="absolute top-1 right-1 bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full font-semibold">Imeisha</span>
                                @elseif($product->idadi < 5)
                                    <span class="absolute top-1 right-1 bg-yellow-500 text-white text-[10px] px-1.5 py-0.5 rounded-full font-semibold">{{ number_format($product->idadi,0) }} zimebaki</span>
                                @endif
                            </div>

                            <!-- Info -->
                            <div class="p-2">
                                <div class="text-xs md:text-sm font-semibold text-gray-800 truncate leading-tight" title="{{ $product->jina }}">
                                    {{ $product->jina }}
                                </div>
                                <div class="flex flex-wrap gap-1 mt-0.5 min-h-[18px]">
                                    @if($product->aina)
                                        <span class="text-[10px] bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded-full">{{ $product->aina }}</span>
                                    @endif
                                    @if($product->kipimo)
                                        <span class="text-[10px] bg-emerald-100 text-emerald-700 px-1.5 py-0.5 rounded-full">{{ $product->kipimo }}</span>
                                    @endif
                                </div>
                                <div class="text-sm font-bold text-emerald-600 mt-1">
                                    {{ number_format($product->bei_kuuza,0) }} TZS
                                </div>
                                @if($product->bei_uzo_jumla && $product->bei_uzo_jumla > 0)
                                    <div class="text-[10px] text-gray-400">Jumla: {{ number_format($product->bei_uzo_jumla,0) }}</div>
                                @endif
                                <!-- ORANGE ADD BUTTON -->
                                <div class="mt-1.5 w-full btn-add-orange py-1 rounded-lg flex items-center justify-center gap-1 text-[11px] font-semibold">
                                    <i class="fas fa-plus-circle"></i> Ongeza
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-10 text-gray-400">
                            <i class="fas fa-box-open text-4xl mb-2 block"></i>
                            <p class="text-sm">Hakuna bidhaa zilizopatikana</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- PANEL 2: Orders List                       -->
        <!-- ========================================== -->
        <div id="o-panel-list" class="o-internal-panel" style="display:none;">

            <!-- Stats -->
            <div class="flex flex-wrap gap-1 text-xs mb-3">
                <span class="bg-gray-100 px-2 py-1 rounded-lg">Jumla: <b id="stat-total">0</b></span>
                <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded-lg">Saved: <b id="stat-saved">0</b></span>
                <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-lg">Conf: <b id="stat-confirmed">0</b></span>
                <span class="bg-green-100 text-green-700 px-2 py-1 rounded-lg">Paid: <b id="stat-paid">0</b></span>
                <span class="bg-red-100 text-red-700 px-2 py-1 rounded-lg">Cancel: <b id="stat-cancelled">0</b></span>
                <!-- RED COUNT - Unpaid orders (saved + confirmed) -->
                <span class="bg-red-500 text-white px-2 py-1 rounded-lg">
                    <i class="fas fa-clock mr-1"></i> Bado: <b id="stat-unpaid">0</b>
                </span>
            </div>

            <!-- Filters -->
            <div class="flex flex-wrap gap-2 mb-3">
                <button onclick="oOrdersFilter('all',this)" class="o-fbtn on">Zote</button>
                <button onclick="oOrdersFilter('saved',this)" class="o-fbtn"><i class="fas fa-clock mr-1"></i>Saved</button>
                <button onclick="oOrdersFilter('confirmed',this)" class="o-fbtn"><i class="fas fa-check mr-1"></i>Confirmed</button>
                <button onclick="oOrdersFilter('paid',this)" class="o-fbtn"><i class="fas fa-check-circle mr-1"></i>Paid</button>
                <button onclick="oOrdersFilter('cancelled',this)" class="o-fbtn"><i class="fas fa-times-circle mr-1"></i>Cancelled</button>
                <div class="relative flex-1 min-w-[130px]">
                    <i class="fas fa-search absolute left-3 top-2.5 text-gray-400 text-sm"></i>
                    <input type="text" id="orders-search" placeholder="Tafuta order..."
                           class="w-full pl-9 pr-3 py-2 border border-gray-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-500"
                           oninput="oOrdersSearch(this.value)">
                </div>
                <button onclick="oOrdersLoad()"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition">
                    <i class="fas fa-sync-alt mr-1"></i>Refresh
                </button>
            </div>

            <!-- Table -->
            <div class="order-table-wrap rounded-lg border border-gray-200">
                <table class="w-full">
                    <thead class="bg-gray-50 text-gray-600 text-xs uppercase">
                        <tr>
                            <th class="border-b px-3 py-2 text-left">#</th>
                            <th class="border-b px-3 py-2 text-left">Mteja</th>
                            <th class="border-b px-3 py-2 text-left">Bidhaa</th>
                            <th class="border-b px-3 py-2 text-right">Jumla</th>
                            <th class="border-b px-3 py-2 text-left">Hali</th>
                            <th class="border-b px-3 py-2 text-left">Tarehe</th>
                            <th class="border-b px-3 py-2 text-center">Vitendo</th>
                        </tr>
                    </thead>
                    <tbody id="orders-tbody">
                        <tr>
                            <td colspan="7" class="text-center py-8 text-gray-400 text-sm">
                                <i class="fas fa-spinner fa-spin mr-2"></i>Inapakia orders...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- CART MODAL - Centered at top                -->
<!-- ============================================ -->
<div id="o-cart-backdrop" class="o-cart-backdrop" onclick="oCartBackdropClick(event)">
    <div class="o-cart-box" onclick="event.stopPropagation()">
        <div class="o-cart-head">
            <div class="flex items-center gap-2">
                <i class="fas fa-shopping-cart text-emerald-600 text-lg"></i>
                <span class="font-bold text-gray-800">Kikapu chako</span>
                <span class="text-xs text-gray-400">(<span id="cart-count">0</span> bidhaa)</span>
            </div>
            <button onclick="oCloseCart()" class="text-gray-400 hover:text-gray-600 text-xl leading-none w-8 h-8 flex items-center justify-center">&times;</button>
        </div>
        <div class="o-cart-items" id="cart-items-wrap"></div>
        <div class="o-cart-foot">
            <div class="flex justify-between items-center mb-3">
                <span class="text-sm text-gray-500 font-medium">Jumla ya malipo:</span>
                <span id="cart-total" class="text-xl font-bold text-emerald-700">0 TZS</span>
            </div>
            <div class="flex gap-2">
                <button onclick="oSaveFlow('saved')"
                        class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white py-2.5 rounded-lg text-sm font-semibold transition flex items-center justify-center gap-1">
                    <i class="fas fa-save"></i> Hifadhi
                </button>
                <button onclick="oSaveFlow('paid')"
                        class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white py-2.5 rounded-lg text-sm font-semibold transition flex items-center justify-center gap-1">
                    <i class="fas fa-check-circle"></i> Lipa
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- MODAL: Customer selection - Centered at top -->
<!-- ============================================ -->
<div id="modal-customer" class="o-modal-wrap hidden">
    <div class="o-modal-box">
        <div class="sticky top-0 bg-white z-10 px-4 pt-4 pb-2 border-b flex items-center justify-between">
            <h3 class="font-bold text-gray-800 text-base flex items-center gap-2">
                <i class="fas fa-user text-emerald-600"></i>Chagua Mteja
            </h3>
            <button onclick="oCustModalClose()" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
        </div>
        <div class="flex border-b bg-white px-4">
            <button class="ctab-btn on" onclick="oCustTab('existing',this)">
                <i class="fas fa-users mr-1"></i>Wateja Waliopo
            </button>
            <button class="ctab-btn" onclick="oCustTab('new',this)">
                <i class="fas fa-user-plus mr-1"></i>Mteja Mpya
            </button>
        </div>
        <div class="p-4">
            <div id="ctab-existing" class="ctab-pane on">
                <button onclick="oSelectCustomer(null,'Walk-in Customer','')"
                        class="w-full text-left px-3 py-2.5 rounded-lg border-2 border-gray-200 hover:border-emerald-500 hover:bg-emerald-50 mb-3 transition flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 flex-shrink-0">
                        <i class="fas fa-user-secret text-sm"></i>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-gray-700">Walk-in Customer</div>
                        <div class="text-xs text-gray-400">Mteja asiyesajiliwa</div>
                    </div>
                </button>
                <div class="relative mb-2">
                    <i class="fas fa-search absolute left-3 top-2.5 text-gray-400 text-sm"></i>
                    <input type="text" id="cust-search-input" placeholder="Tafuta jina au simu..."
                           class="w-full pl-9 pr-3 py-2 border border-gray-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-500"
                           oninput="oCustSearch(this.value)">
                </div>
                <div id="cust-list" class="space-y-1 max-h-52 overflow-y-auto">
                    @forelse($wateja ?? [] as $mteja)
                        <button onclick="oSelectCustomer('{{ $mteja->id }}','{{ addslashes($mteja->jina) }}','{{ $mteja->simu }}')"
                                class="cust-item w-full text-left px-3 py-2 rounded-lg border border-gray-100 hover:border-emerald-400 hover:bg-emerald-50 transition flex items-center gap-3"
                                data-name="{{ strtolower($mteja->jina) }}" data-simu="{{ $mteja->simu }}">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-sm flex-shrink-0">
                                {{ strtoupper(substr($mteja->jina,0,1)) }}
                            </div>
                            <div class="min-w-0">
                                <div class="text-sm font-medium text-gray-800 truncate">{{ $mteja->jina }}</div>
                                <div class="text-xs text-gray-400">{{ $mteja->simu }}</div>
                            </div>
                        </button>
                    @empty
                        <p class="text-center text-gray-400 text-sm py-4">Hakuna wateja walioorodheshwa bado</p>
                    @endforelse
                </div>
            </div>
            <div id="ctab-new" class="ctab-pane">
                <div class="space-y-3">
                    <div>
                        <label class="text-xs font-semibold text-gray-600 mb-1 block">Jina la Mteja *</label>
                        <input type="text" id="nc-name" placeholder="Jina kamili..."
                               class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600 mb-1 block">Namba ya Simu *</label>
                        <input type="tel" id="nc-phone" placeholder="0712 345 678..."
                               class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600 mb-1 block">Anapoishi (hiari)</label>
                        <input type="text" id="nc-address" placeholder="Mtaa, mji..."
                               class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-500">
                    </div>
                    <button onclick="oSaveNewCustomer()" id="nc-save-btn"
                            class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-2.5 rounded-lg text-sm font-semibold transition flex items-center justify-center gap-2">
                        <i class="fas fa-user-plus"></i>Ongeza na Endelea
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- MODAL: View Order - Centered at top          -->
<!-- ============================================ -->
<div id="modal-view-order" class="o-modal-wrap hidden">
    <div class="o-modal-box">
        <div class="sticky top-0 bg-white z-10 px-4 pt-4 pb-3 border-b flex items-center justify-between">
            <h3 class="font-bold text-gray-800 text-base flex items-center gap-2">
                <i class="fas fa-eye text-blue-600"></i>Taarifa za Order
            </h3>
            <button onclick="oViewClose()" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
        </div>
        <div class="p-4" id="view-order-body"></div>
    </div>
</div>

<!-- ============================================ -->
<!-- MODAL: Edit Status - Centered at top         -->
<!-- ============================================ -->
<div id="modal-edit-status" class="o-modal-wrap hidden">
    <div class="o-modal-box" style="max-width:360px;">
        <div class="px-4 pt-4 pb-3 border-b flex items-center justify-between">
            <h3 class="font-bold text-gray-800 text-base">Badilisha Hali</h3>
            <button onclick="oEditStatusClose()" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
        </div>
        <div class="p-4">
            <p class="text-sm text-gray-500 mb-4">Order: <strong id="edit-status-num" class="font-mono text-gray-800"></strong></p>
            <div class="grid grid-cols-2 gap-2" id="status-btn-group">
                <button onclick="oUpdateStatus('saved')" class="py-2.5 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg text-sm font-semibold transition">Saved</button>
                <button onclick="oUpdateStatus('confirmed')" class="py-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm font-semibold transition">Confirmed</button>
                <button onclick="oUpdateStatus('paid')" class="py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold transition">Paid</button>
                <button onclick="oUpdateStatus('cancelled')" class="py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm font-semibold transition">Cancelled</button>
            </div>
            <button onclick="oEditStatusClose()" class="mt-3 w-full py-2 border border-gray-200 rounded-lg text-sm text-gray-500 hover:bg-gray-50 transition">Ghairi</button>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- MODAL: Delete Confirm - Centered at top      -->
<!-- ============================================ -->
<div id="modal-delete-order" class="o-modal-wrap hidden">
    <div class="o-modal-box" style="max-width:360px;">
        <div class="px-4 pt-4 pb-3 border-b">
            <h3 class="font-bold text-red-600 text-base flex items-center gap-2">
                <i class="fas fa-exclamation-triangle"></i>Futa Order
            </h3>
        </div>
        <div class="p-4">
            <p class="text-sm text-gray-600 mb-2">Una uhakika unataka kufuta order hii?</p>
            <p class="text-xs text-gray-400 mb-4">Hatua hii haiwezi kurejeshwa.</p>
            <div class="flex gap-2">
                <button onclick="oDeleteClose()" class="flex-1 py-2 border border-gray-200 rounded-lg text-sm hover:bg-gray-50 transition">Ghairi</button>
                <button onclick="oConfirmDelete()" id="delete-confirm-btn" class="flex-1 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-semibold transition">Futa</button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- JAVASCRIPT                                   -->
<!-- ============================================ -->
<script>
(function(){
'use strict';

/* ─────────────────────────────────────────
   STATE
───────────────────────────────────────── */
var oCart       = [];
var oOrders     = [];
var oFilter     = 'all';
var oSearch     = '';
var oProdType   = 'all';
var oCurrentId  = null;
var oPendingSt  = null;
var oSelectedCust = { id:null, name:'Walk-in Customer', phone:'' };
var oKnownOrderCount = 0;
var oCsrf = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';

/* ─────────────────────────────────────────
   INIT
───────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', function(){
    oOrdersLoad();
    oCartRender();

    document.addEventListener('keydown', function(e){
        if(e.key === 'Escape'){
            oCloseCart();
            oViewClose();
            oEditStatusClose();
            oDeleteClose();
            oCustModalClose();
        }
    });

    setInterval(function(){ oOrdersLoad(true); }, 30000);
});

/* ─────────────────────────────────────────
   INTERNAL TAB SWITCHING
───────────────────────────────────────── */
function oSwitchInternalTab(tab, btn){
    // Hide both panels
    document.getElementById('o-panel-place').style.display = 'none';
    document.getElementById('o-panel-list').style.display = 'none';
    
    // Show selected
    if(tab === 'place'){
        document.getElementById('o-panel-place').style.display = 'block';
    } else {
        document.getElementById('o-panel-list').style.display = 'block';
        oOrdersLoad();
        oClearNewBadge();
    }
    
    // Update button styles
    document.querySelectorAll('.o-internal-tab').forEach(function(b){
        b.classList.remove('active');
    });
    if(btn) btn.classList.add('active');
}
window.oSwitchInternalTab = oSwitchInternalTab;

/* ─────────────────────────────────────────
   MAIN TAB NAVIGATION - Only ONE order tab
───────────────────────────────────────── */
document.addEventListener('click', function(e){
    var btn = e.target.closest('[data-tab]');
    if(!btn) return;
    var tab = btn.dataset.tab;
    
    // Hide all order tab contents first
    document.querySelectorAll('.order-tab-content').forEach(function(el){
        el.classList.remove('active');
    });
    
    // Only show order content for weka-order tab
    if(tab === 'weka-order'){
        var cel = document.getElementById('weka-order-tab-content');
        if(cel) cel.classList.add('active');
        oOrdersLoad();
        oClearNewBadge();
    }
});

/* ─────────────────────────────────────────
   TOAST - Centered at top
───────────────────────────────────────── */
function oToast(msg, type){
    type = type || 'info';
    var wrap = document.getElementById('o-toast-wrap');
    if(!wrap) return;
    var d = document.createElement('div');
    d.className = 'o-toast ot-' + type;
    d.textContent = msg;
    wrap.appendChild(d);
    setTimeout(function(){
        d.style.transition = 'opacity 0.3s, transform 0.3s';
        d.style.opacity = '0';
        d.style.transform = 'translateY(-20px)';
        setTimeout(function(){ d.remove(); }, 320);
    }, 3000);
}

/* ─────────────────────────────────────────
   NEW ORDER BADGE - Counts unpaid orders (saved + confirmed)
───────────────────────────────────────── */
function oCheckNewOrders(freshOrders){
    // Count unpaid orders (saved + confirmed)
    var unpaidCount = freshOrders.filter(function(o){
        return o.status === 'saved' || o.status === 'confirmed';
    }).length;
    
    // Update badge with unpaid count
    var internalBadge = document.getElementById('new-order-badge-internal');
    if(internalBadge){
        if(unpaidCount > 0){
            internalBadge.textContent = unpaidCount;
            internalBadge.classList.remove('hidden');
        } else {
            internalBadge.classList.add('hidden');
        }
    }
    
    // Update tab badge
    var tabBadge = document.getElementById('new-order-badge-tab');
    if(tabBadge){
        if(unpaidCount > 0){
            tabBadge.textContent = unpaidCount;
            tabBadge.classList.remove('hidden');
        } else {
            tabBadge.classList.add('hidden');
        }
    }
    
    oKnownOrderCount = freshOrders.length;
}
function oClearNewBadge(){
    document.querySelectorAll('.badge, #new-order-badge-tab').forEach(function(badge){
        badge.classList.add('hidden');
    });
}

/* ─────────────────────────────────────────
   PRODUCT FILTERS
───────────────────────────────────────── */
function oProdSearch(val){
    var term = (val||'').toLowerCase().trim();
    document.querySelectorAll('#order-product-grid .order-product-card').forEach(function(c){
        var name = (c.dataset.name||'').toLowerCase();
        var aina = (c.dataset.aina||'').toLowerCase();
        var kip  = (c.dataset.kipimo||'').toLowerCase();
        var ms   = !term || name.includes(term) || aina.includes(term) || kip.includes(term);
        var mt   = oTypeMatch(c);
        c.style.display = (ms && mt) ? '' : 'none';
    });
}
function oTypeMatch(c){
    if(oProdType === 'all') return true;
    var stock = parseFloat(c.dataset.stock)||0;
    var ws    = parseFloat(c.dataset.wholesale)||0;
    if(oProdType === 'jumla')     return ws > 0;
    if(oProdType === 'low_stock') return stock > 0 && stock < 10;
    return true;
}
function oProdFilter(type, btn){
    oProdType = type;
    document.querySelectorAll('.o-fbtn').forEach(function(b){ b.classList.remove('on'); });
    if(btn) btn.classList.add('on');
    oProdSearch(document.getElementById('oprod-search')?.value || '');
}

/* ─────────────────────────────────────────
   CART FUNCTIONS
───────────────────────────────────────── */
function oAddToCart(card){
    var id    = card.dataset.id;
    var name  = card.dataset.name;
    var price = parseFloat(card.dataset.price)||0;
    var ws    = parseFloat(card.dataset.wholesale)||0;
    var stock = parseFloat(card.dataset.stock)||0;
    var image = card.dataset.image||'';
    var aina  = card.dataset.aina||'';
    var kip   = card.dataset.kipimo||'';

    if(stock <= 0){ oToast('Bidhaa hii imeisha!','error'); return; }

    var useWs    = oProdType === 'jumla' && ws > 0;
    var finalPr  = useWs ? ws : price;
    var existing = oCart.find(function(i){ return i.id === id; });

    if(existing){
        if(existing.qty >= stock){ oToast('Hisa haitoshi!','error'); return; }
        existing.qty++;
    } else {
        oCart.push({ id:id, name:name, price:finalPr, qty:1, stock:stock, image:image, aina:aina, kipimo:kip });
    }
    oCartRender();
    oToast(name + ' imeongezwa ✓','success');
    oOpenCart();
}

function oCartRemove(idx){
    oCart.splice(idx,1);
    oCartRender();
}
function oCartQty(idx, delta){
    var item = oCart[idx];
    if(!item) return;
    var nq = item.qty + delta;
    if(nq <= 0){ oCart.splice(idx,1); }
    else if(nq > item.stock){ oToast('Hisa haitoshi!','error'); return; }
    else { item.qty = nq; }
    oCartRender();
}

function oCartRender(){
    var count = oCart.reduce(function(s,i){ return s+i.qty; },0);
    var total = oCart.reduce(function(s,i){ return s+(i.price*i.qty); },0);

    var badge = document.getElementById('cart-badge');
    if(badge){
        badge.textContent = count;
        badge.style.display = 'inline-flex';
    }
    var elCount = document.getElementById('cart-count');
    var elTotal = document.getElementById('cart-total');
    if(elCount) elCount.textContent = count;
    if(elTotal) elTotal.textContent = total.toLocaleString() + ' TZS';

    var wrap = document.getElementById('cart-items-wrap');
    if(!wrap) return;

    if(oCart.length === 0){
        wrap.innerHTML = '<div class="text-center text-gray-400 py-10"><i class="fas fa-shopping-cart text-4xl mb-2 block"></i><p class="text-sm">Kikapu ni tupu</p><p class="text-xs mt-1">Gusa bidhaa ili kuongeza</p></div>';
        return;
    }

    wrap.innerHTML = oCart.map(function(item, idx){
        var imgHtml = item.image
            ? '<img src="' + oEsc(item.image) + '" alt="" onerror="this.style.display=\'none\';this.parentElement.innerHTML=\'<i class=\\\"fas fa-box\\\"></i>\'">'
            : '<i class="fas fa-box"></i>';
        var badges = '';
        if(item.aina)   badges += '<span class="text-[10px] bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded-full">'+oEsc(item.aina)+'</span> ';
        if(item.kipimo) badges += '<span class="text-[10px] bg-emerald-100 text-emerald-700 px-1.5 py-0.5 rounded-full">'+oEsc(item.kipimo)+'</span>';
        return '<div class="cart-row">'
            + '<div class="cart-thumb">' + imgHtml + '</div>'
            + '<div class="flex-1 min-w-0">'
            +   '<div class="text-sm font-semibold text-gray-800 truncate">' + oEsc(item.name) + '</div>'
            +   '<div class="text-xs text-gray-400">' + item.price.toLocaleString() + ' TZS</div>'
            +   '<div class="mt-0.5">' + badges + '</div>'
            + '</div>'
            + '<div class="flex items-center gap-1.5 flex-shrink-0">'
            +   '<button onclick="oCartQty('+idx+',-1)" class="qty-btn"><i class="fas fa-minus text-xs"></i></button>'
            +   '<span class="text-sm font-bold w-5 text-center">' + item.qty + '</span>'
            +   '<button onclick="oCartQty('+idx+',1)" class="qty-btn"><i class="fas fa-plus text-xs"></i></button>'
            + '</div>'
            + '<div class="text-sm font-bold text-emerald-700 min-w-[56px] text-right flex-shrink-0">'
            +   (item.price * item.qty).toLocaleString()
            + '</div>'
            + '<button onclick="oCartRemove('+idx+')" class="text-red-400 hover:text-red-600 ml-1 text-sm flex-shrink-0" title="Ondoa">'
            +   '<i class="fas fa-times"></i>'
            + '</button>'
            + '</div>';
    }).join('');
}

function oOpenCart(){
    document.getElementById('o-cart-backdrop')?.classList.add('open');
    document.body.style.overflow = 'hidden';
}
function oCloseCart(){
    document.getElementById('o-cart-backdrop')?.classList.remove('open');
    document.body.style.overflow = '';
}
function oCartBackdropClick(e){
    if(e.target === document.getElementById('o-cart-backdrop')) oCloseCart();
}
function oClearCart(){
    if(oCart.length === 0){ oToast('Kikapu tayari ni tupu','info'); return; }
    if(!confirm('Futa bidhaa zote kwenye kikapu?')) return;
    oCart = [];
    oCartRender();
    oCloseCart();
    oToast('Kikapu kimefutwa','info');
}

/* ─────────────────────────────────────────
   SAVE FLOW
───────────────────────────────────────── */
function oSaveFlow(status){
    if(oCart.length === 0){ oToast('Ongeza bidhaa kwenye kikapu kwanza!','error'); return; }
    oPendingSt = status;
    oSelectedCust = { id:null, name:'Walk-in Customer', phone:'' };
    oCloseCart();
    oCustModalOpen();
}
function oSelectCustomer(id, name, phone){
    oSelectedCust = { id: id||null, name: name||'Walk-in Customer', phone: phone||'' };
    oCustModalClose();
    oSubmitOrder(oPendingSt);
}
async function oSaveNewCustomer(){
    var name    = (document.getElementById('nc-name')?.value||'').trim();
    var phone   = (document.getElementById('nc-phone')?.value||'').trim();
    var address = (document.getElementById('nc-address')?.value||'').trim();
    if(!name)  { oToast('Jina la mteja linahitajika','error'); return; }
    if(!phone) { oToast('Namba ya simu inahitajika','error'); return; }

    var btn = document.getElementById('nc-save-btn');
    if(btn){ btn.disabled=true; btn.innerHTML='<i class="fas fa-spinner fa-spin"></i> Inaongeza...'; }

    try{
        var res  = await fetch('/wateja', {
            method:'POST',
            headers:{ 'Content-Type':'application/json', 'X-CSRF-TOKEN': oCsrf },
            body: JSON.stringify({ jina:name, simu:phone, anapoishi:address })
        });
        var data = await res.json();
        if(data.success){
            oToast('Mteja ameongezwa!','success');
            oSelectedCust = { id: data.data?.id||null, name:name, phone:phone };
            oCustModalClose();
            oSubmitOrder(oPendingSt);
        } else {
            oToast(data.message||'Hitilafu katika kuongeza mteja','error');
        }
    } catch(e){
        oToast('Hitilafu ya mtandao: ' + e.message,'error');
    } finally {
        if(btn){ btn.disabled=false; btn.innerHTML='<i class="fas fa-user-plus"></i> Ongeza na Endelea'; }
    }
}

async function oSubmitOrder(status){
    if(oCart.length === 0) return;
    var items    = oCart.map(function(i){ return { id:i.id, name:i.name, price:i.price, qty:i.qty, total:i.price*i.qty }; });
    var subtotal = oCart.reduce(function(s,i){ return s + i.price*i.qty; }, 0);

    try{
        var res  = await fetch('/orders', {
            method:'POST',
            headers:{ 'Content-Type':'application/json', 'X-CSRF-TOKEN': oCsrf },
            body: JSON.stringify({
                items: items,
                subtotal: subtotal,
                discount: 0,
                total: subtotal,
                status: status,
                customer_name: oSelectedCust.name,
                customer_phone: oSelectedCust.phone,
                customer_id: oSelectedCust.id
            })
        });
        var data = await res.json();
        if(data.success){
            oToast(data.message||'Order imehifadhiwa!','success');
            oCart = [];
            oCartRender();
            oOrdersLoad();
            // Switch to orders list view
            document.querySelectorAll('.o-internal-tab').forEach(function(b){ b.classList.remove('active'); });
            var listTab = document.querySelector('.o-internal-tab:last-child');
            if(listTab) listTab.classList.add('active');
            document.getElementById('o-panel-place').style.display = 'none';
            document.getElementById('o-panel-list').style.display = 'block';
            if(status === 'paid'){
                oToast('Ukurasa unafufuliwa...','info');
                setTimeout(function(){ window.location.reload(); }, 1800);
            }
        } else {
            oToast(data.message||'Hitilafu katika kuhifadhi order','error');
        }
    } catch(e){
        oToast('Hitilafu ya mtandao: ' + e.message,'error');
    }
}

/* ─────────────────────────────────────────
   CUSTOMER MODAL
───────────────────────────────────────── */
function oCustModalOpen(){
    ['nc-name','nc-phone','nc-address'].forEach(function(id){
        var el = document.getElementById(id);
        if(el) el.value='';
    });
    var si = document.getElementById('cust-search-input');
    if(si) si.value='';
    oCustSearch('');
    oCustTab('existing', document.querySelector('.ctab-btn'));
    document.getElementById('modal-customer')?.classList.remove('hidden');
}
function oCustModalClose(){
    document.getElementById('modal-customer')?.classList.add('hidden');
}
function oCustTab(tab, btn){
    document.querySelectorAll('.ctab-pane').forEach(function(el){ el.classList.remove('on'); });
    document.querySelectorAll('.ctab-btn').forEach(function(b){ b.classList.remove('on'); });
    document.getElementById('ctab-'+tab)?.classList.add('on');
    if(btn) btn.classList.add('on');
}
function oCustSearch(val){
    var term = (val||'').toLowerCase().trim();
    document.querySelectorAll('#cust-list .cust-item').forEach(function(el){
        var name = el.dataset.name||'';
        var simu = el.dataset.simu||'';
        el.style.display = (!term || name.includes(term) || simu.includes(term)) ? '' : 'none';
    });
}

/* ─────────────────────────────────────────
   ORDERS LIST
───────────────────────────────────────── */
async function oOrdersLoad(silent){
    try{
        var res  = await fetch('/orders/placed', {
            headers:{ 'Accept':'application/json', 'X-Requested-With':'XMLHttpRequest' }
        });
        var data = await res.json();
        if(data.success){
            oCheckNewOrders(data.data || []);
            oOrders = data.data || [];
            oOrdersRender();
            oOrdersStats();
        }
    } catch(e){
        if(!silent){
            var tbody = document.getElementById('orders-tbody');
            if(tbody) tbody.innerHTML = '<tr><td colspan="7" class="text-center py-6 text-red-500 text-sm"><i class="fas fa-exclamation-circle mr-1"></i>Hitilafu katika kupakia orders</td></tr>';
        }
    }
}
function oOrdersRender(){
    var tbody = document.getElementById('orders-tbody');
    if(!tbody) return;
    var filtered = oOrders.filter(function(o){
        var mf = oFilter === 'all' || o.status === oFilter;
        var ms = !oSearch || [o.order_number,o.customer_name,o.customer_phone].some(function(v){ return (v||'').toLowerCase().includes(oSearch); });
        return mf && ms;
    });
    if(filtered.length === 0){
        tbody.innerHTML = '<tr><td colspan="7" class="text-center py-8 text-gray-400 text-sm"><i class="fas fa-inbox text-2xl mb-1 block"></i>Hakuna orders zilizopatikana</td></tr>';
        return;
    }
    var bClass = {saved:'b-saved',confirmed:'b-confirmed',paid:'b-paid',cancelled:'b-cancelled'};
    var bLabel = {saved:'Saved',confirmed:'Confirmed',paid:'Paid',cancelled:'Cancelled'};

    tbody.innerHTML = filtered.map(function(o){
        var num     = o.order_number || '#'+o.id;
        var items   = o.items || [];
        var preview = items.slice(0,2).map(function(i){ return i.jina||i.name; }).join(', ');
        var more    = items.length > 2 ? ' +' + (items.length-2) : '';
        var date    = o.created_at ? new Date(o.created_at).toLocaleDateString('sw-TZ',{day:'2-digit',month:'short',hour:'2-digit',minute:'2-digit'}) : '-';
        var canEdit = o.status !== 'paid' && o.status !== 'cancelled';
        var editBtn = canEdit
            ? '<button onclick="oEditStatus(\''+o.id+'\',\''+oEsc(num)+'\')" class="bg-yellow-100 hover:bg-yellow-200 text-yellow-700 p-1.5 rounded text-xs transition" title="Badilisha Hali"><i class="fas fa-edit"></i></button>'
            : '';
        return '<tr class="border-b hover:bg-gray-50 transition">'
            + '<td class="px-3 py-2 text-xs font-mono"><span class="bg-gray-100 px-1.5 py-0.5 rounded">'+oEsc(num)+'</span></td>'
            + '<td class="px-3 py-2"><div class="text-sm font-medium">'+oEsc(o.customer_name||'Walk-in')+'</div>'
            +   (o.customer_phone?'<div class="text-xs text-gray-400">'+oEsc(o.customer_phone)+'</div>':'')
            + '</td>'
            + '<td class="px-3 py-2 text-sm text-gray-500 max-w-[160px]"><div class="truncate">'+oEsc(preview||'—')+more+'</div></td>'
            + '<td class="px-3 py-2 text-right text-sm font-bold text-emerald-700">'+((o.total||0).toLocaleString())+' TZS</td>'
            + '<td class="px-3 py-2"><span class="o-badge '+(bClass[o.status]||'')+'">'+(bLabel[o.status]||o.status)+'</span></td>'
            + '<td class="px-3 py-2 text-xs text-gray-400 whitespace-nowrap">'+date+'</td>'
            + '<td class="px-3 py-2"><div class="flex flex-wrap gap-1 justify-center">'
            +   '<button onclick="oView(\''+o.id+'\')" class="bg-blue-100 hover:bg-blue-200 text-blue-700 p-1.5 rounded text-xs transition" title="Tazama"><i class="fas fa-eye"></i></button>'
            +   '<button onclick="oShare(\''+o.id+'\')" class="bg-green-100 hover:bg-green-200 text-green-700 p-1.5 rounded text-xs transition" title="Shiriki"><i class="fas fa-share-alt"></i></button>'
            +   '<button onclick="oPrint(\''+o.id+'\')" class="bg-purple-100 hover:bg-purple-200 text-purple-700 p-1.5 rounded text-xs transition" title="Chapisha"><i class="fas fa-print"></i></button>'
            +   editBtn
            +   '<button onclick="oDeleteOpen(\''+o.id+'\')" class="bg-red-100 hover:bg-red-200 text-red-700 p-1.5 rounded text-xs transition" title="Futa"><i class="fas fa-trash"></i></button>'
            + '</div></td></tr>';
    }).join('');
}
function oOrdersStats(){
    var s = {total:0,saved:0,confirmed:0,paid:0,cancelled:0,unpaid:0};
    oOrders.forEach(function(o){
        s.total++;
        if(s[o.status]!==undefined) s[o.status]++;
        if(o.status === 'saved' || o.status === 'confirmed') s.unpaid++;
    });
    ['total','saved','confirmed','paid','cancelled','unpaid'].forEach(function(k){
        var el = document.getElementById('stat-'+k);
        if(el) el.textContent = s[k];
    });
}
function oOrdersFilter(filter, btn){
    oFilter = filter;
    document.querySelectorAll('.o-fbtn').forEach(function(b){ b.classList.remove('on'); });
    if(btn) btn.classList.add('on');
    oOrdersRender();
}
function oOrdersSearch(val){
    oSearch = (val||'').toLowerCase().trim();
    oOrdersRender();
}

/* ─────────────────────────────────────────
   VIEW ORDER
───────────────────────────────────────── */
function oView(id){
    var o = oOrders.find(function(x){ return String(x.id) === String(id); });
    if(!o){ oToast('Order haipatikani','error'); return; }
    var bClass={saved:'b-saved',confirmed:'b-confirmed',paid:'b-paid',cancelled:'b-cancelled'};
    var bLabel={saved:'Saved',confirmed:'Confirmed',paid:'Paid',cancelled:'Cancelled'};
    var itemsHtml = (o.items||[]).map(function(item){
        var qty = item.idadi||item.qty||0;
        var tot = item.total||(qty*(item.bei||item.price||0))||0;
        return '<div class="flex justify-between py-1.5 border-b border-gray-100 text-sm last:border-0">'
            + '<span class="text-gray-700">'+oEsc(item.jina||item.name)+' <span class="text-gray-400">x'+qty+'</span></span>'
            + '<span class="font-semibold">'+tot.toLocaleString()+' TZS</span>'
            + '</div>';
    }).join('') || '<p class="text-sm text-gray-400 py-2">Hakuna bidhaa</p>';

    var body = document.getElementById('view-order-body');
    if(body){
        body.innerHTML = '<div class="space-y-3">'
            + '<div class="bg-gray-50 rounded-lg p-3 space-y-1.5 text-sm">'
            +   '<div class="flex justify-between"><span class="text-gray-500">Order #</span><span class="font-mono font-bold">' + oEsc(o.order_number||'#'+o.id) + '</span></div>'
            +   '<div class="flex justify-between"><span class="text-gray-500">Mteja</span><span class="font-medium">'+oEsc(o.customer_name||'Walk-in')+'</span></div>'
            +   (o.customer_phone?'<div class="flex justify-between"><span class="text-gray-500">Simu</span><span>'+oEsc(o.customer_phone)+'</span></div>':'')
            +   '<div class="flex justify-between"><span class="text-gray-500">Hali</span><span class="o-badge '+(bClass[o.status]||'')+'">'+(bLabel[o.status]||o.status)+'</span></div>'
            +   '<div class="flex justify-between"><span class="text-gray-500">Tarehe</span><span>'+new Date(o.created_at).toLocaleString('sw-TZ')+'</span></div>'
            + '</div>'
            + '<div><h4 class="text-sm font-semibold text-gray-700 mb-1">Bidhaa:</h4>'
            +   '<div class="bg-gray-50 rounded-lg px-3 py-1">'+itemsHtml+'</div>'
            + '</div>'
            + '<div class="flex justify-between items-center border-t pt-2 font-bold"><span>JUMLA:</span><span class="text-emerald-700">'+((o.total||0).toLocaleString())+' TZS</span></div>'
            + '<div class="flex gap-2 pt-1">'
            +   '<button onclick="oShare(\''+o.id+'\')" class="flex-1 bg-green-100 hover:bg-green-200 text-green-700 py-2 rounded-lg text-sm font-semibold transition"><i class="fas fa-share-alt mr-1"></i>Shiriki</button>'
            +   '<button onclick="oPrint(\''+o.id+'\')" class="flex-1 bg-purple-100 hover:bg-purple-200 text-purple-700 py-2 rounded-lg text-sm font-semibold transition"><i class="fas fa-print mr-1"></i>Chapisha</button>'
            + '</div>'
            + '</div>';
    }
    document.getElementById('modal-view-order')?.classList.remove('hidden');
}
function oViewClose(){
    document.getElementById('modal-view-order')?.classList.add('hidden');
}

/* ─────────────────────────────────────────
   EDIT STATUS
───────────────────────────────────────── */
function oEditStatus(id, num){
    oCurrentId = id;
    var el = document.getElementById('edit-status-num');
    if(el) el.textContent = num;
    document.getElementById('modal-edit-status')?.classList.remove('hidden');
}
function oEditStatusClose(){
    document.getElementById('modal-edit-status')?.classList.add('hidden');
    oCurrentId = null;
}
async function oUpdateStatus(status){
    if(!oCurrentId){ oToast('Order ID haijulikani','error'); return; }
    var id = oCurrentId;
    oEditStatusClose();

    var grp = document.getElementById('status-btn-group');
    if(grp) grp.querySelectorAll('button').forEach(function(b){ b.disabled=true; });

    try{
        var res = await fetch('/orders/' + id + '/status', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': oCsrf,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status: status })
        });

        var data;
        try{ data = await res.json(); } catch(je){ data = { success:false, message:'Server error ('+res.status+')' }; }

        if(data.success){
            oToast('Hali imebadilishwa: ' + status,'success');
            oOrdersLoad();
            if(status === 'paid'){
                oToast('Ukurasa unafufuliwa...','info');
                setTimeout(function(){ window.location.reload(); }, 1800);
            }
        } else {
            oToast(data.message||'Hitilafu katika kubadilisha hali','error');
        }
    } catch(e){
        oToast('Hitilafu ya mtandao: ' + e.message, 'error');
    } finally {
        if(grp) grp.querySelectorAll('button').forEach(function(b){ b.disabled=false; });
    }
}

/* ─────────────────────────────────────────
   DELETE
───────────────────────────────────────── */
function oDeleteOpen(id){
    oCurrentId = id;
    document.getElementById('modal-delete-order')?.classList.remove('hidden');
}
function oDeleteClose(){
    document.getElementById('modal-delete-order')?.classList.add('hidden');
    oCurrentId = null;
}
async function oConfirmDelete(){
    if(!oCurrentId) return;
    var id = oCurrentId;
    oDeleteClose();
    var btn = document.getElementById('delete-confirm-btn');
    if(btn){ btn.disabled=true; btn.textContent='Inafuta...'; }
    try{
        var res  = await fetch('/orders/' + id, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': oCsrf,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });
        var data;
        try{ data = await res.json(); } catch(je){ data={success:false,message:'Server error ('+res.status+')'}; }
        if(data.success){
            oToast('Order imefutwa!','success');
            oOrdersLoad();
        } else {
            oToast(data.message||'Hitilafu katika kufuta order','error');
        }
    } catch(e){
        oToast('Hitilafu ya mtandao: ' + e.message,'error');
    } finally {
        if(btn){ btn.disabled=false; btn.textContent='Futa'; }
    }
}

/* ─────────────────────────────────────────
   SHARE
───────────────────────────────────────── */
function oShare(id){
    var o = oOrders.find(function(x){ return String(x.id)===String(id); });
    if(!o){ oToast('Order haipatikani','error'); return; }
    var text = '*ORDER ' + (o.order_number||'#'+o.id) + '*\n'
        + 'Mteja: ' + (o.customer_name||'Walk-in') + '\n'
        + (o.customer_phone ? 'Simu: '+o.customer_phone+'\n' : '')
        + 'Hali: ' + o.status + '\n---\n';
    (o.items||[]).forEach(function(item){
        var qty = item.idadi||item.qty||0;
        var tot = item.total||(qty*(item.bei||item.price||0))||0;
        text += (item.jina||item.name)+' x'+qty+' = '+tot.toLocaleString()+' TZS\n';
    });
    text += '---\nJUMLA: '+((o.total||0).toLocaleString())+' TZS';
    if(navigator.share){
        navigator.share({title:'Order '+(o.order_number||'#'+o.id),text:text}).catch(function(){});
    } else {
        navigator.clipboard.writeText(text)
            .then(function(){ oToast('Imenakiliwa!','success'); })
            .catch(function(){ prompt('Nakili:',text); });
    }
}

/* ─────────────────────────────────────────
   PRINT
───────────────────────────────────────── */
function oPrint(id){
    var o = oOrders.find(function(x){ return String(x.id)===String(id); });
    if(!o){ oToast('Order haipatikani','error'); return; }
    var win = window.open('','_blank','width=420,height=650');
    if(!win){ oToast('Tafadhali ruhusu pop-ups','error'); return; }
    var rows = (o.items||[]).map(function(item){
        var qty = item.idadi||item.qty||0;
        var tot = item.total||(qty*(item.bei||item.price||0))||0;
        return '<tr><td>'+oEsc(item.jina||item.name)+'</td><td style="text-align:center">'+qty+'</td><td style="text-align:right">'+tot.toLocaleString()+' TZS</td></tr>';
    }).join('');
    win.document.write('<!DOCTYPE html><html><head><meta charset="utf-8"><title>Order</title>'
        +'<style>body{font-family:Arial,sans-serif;padding:16px;max-width:400px;margin:0 auto;font-size:13px}'
        +'h2{text-align:center;color:#10b981;margin:0 0 2px}.sub{text-align:center;color:#9ca3af;font-size:11px;margin-bottom:10px}'
        +'.row{display:flex;justify-content:space-between;padding:2px 0}'
        +'table{width:100%;border-collapse:collapse;margin:10px 0}'
        +'th,td{padding:5px 3px;border-bottom:1px solid #f3f4f6}th{background:#f9fafb;font-size:11px}'
        +'.tot{font-size:16px;font-weight:bold;display:flex;justify-content:space-between;border-top:2px solid #374151;padding-top:8px}'
        +'.foot{text-align:center;font-size:10px;color:#9ca3af;margin-top:12px}'
        +'@media print{.noprint{display:none}}</style></head><body>'
        +'<h2>ORDER</h2><div class="sub">'+(o.order_number||'#'+o.id)+'</div>'
        +'<div class="row"><span>Mteja:</span><span>'+(o.customer_name||'Walk-in')+'</span></div>'
        +(o.customer_phone?'<div class="row"><span>Simu:</span><span>'+o.customer_phone+'</span></div>':'')
        +'<div class="row"><span>Hali:</span><span>'+o.status+'</span></div>'
        +'<div class="row"><span>Tarehe:</span><span>'+new Date(o.created_at).toLocaleString('sw-TZ')+'</span></div>'
        +'<table><thead><tr><th>Bidhaa</th><th style="text-align:center">Idadi</th><th style="text-align:right">Jumla</th></tr></thead>'
        +'<tbody>'+rows+'</tbody></table>'
        +'<div class="tot"><span>JUMLA:</span><span style="color:#10b981">'+((o.total||0).toLocaleString())+' TZS</span></div>'
        +'<div class="foot">'+new Date().toLocaleString('sw-TZ')+'</div>'
        +'<div class="noprint" style="text-align:center;margin-top:14px">'
        +'<button onclick="window.print()" style="padding:8px 20px;background:#10b981;color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer">Chapisha</button>'
        +'</div>'
        +'<script>setTimeout(function(){window.print();},400);<\/script>'
        +'</body></html>');
    win.document.close();
}

/* ─────────────────────────────────────────
   HELPERS
───────────────────────────────────────── */
function oEsc(str){
    return String(str||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

/* ─────────────────────────────────────────
   GLOBAL EXPORTS
───────────────────────────────────────── */
Object.assign(window, {
    oProdSearch: oProdSearch,
    oProdFilter: oProdFilter,
    oAddToCart:  oAddToCart,
    oOpenCart:   oOpenCart,
    oCloseCart:  oCloseCart,
    oCartBackdropClick: oCartBackdropClick,
    oCartQty:    oCartQty,
    oCartRemove: oCartRemove,
    oClearCart:  oClearCart,
    oSaveFlow:   oSaveFlow,
    oSelectCustomer: oSelectCustomer,
    oSaveNewCustomer: oSaveNewCustomer,
    oCustModalClose: oCustModalClose,
    oCustTab:    oCustTab,
    oCustSearch: oCustSearch,
    oOrdersLoad: oOrdersLoad,
    oOrdersFilter: oOrdersFilter,
    oOrdersSearch: oOrdersSearch,
    oView:       oView,
    oViewClose:  oViewClose,
    oEditStatus: oEditStatus,
    oEditStatusClose: oEditStatusClose,
    oUpdateStatus: oUpdateStatus,
    oDeleteOpen: oDeleteOpen,
    oDeleteClose: oDeleteClose,
    oConfirmDelete: oConfirmDelete,
    oShare:      oShare,
    oPrint:      oPrint,
    oSwitchInternalTab: oSwitchInternalTab,
});

})();
</script>