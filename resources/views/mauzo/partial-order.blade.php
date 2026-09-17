<!-- ============================================ -->
<!-- MODERN ORDER EXPERIENCE — Responsive, Fast, Professional -->
<!-- Preserves all IDs/logic, only UI + performance enhanced -->
<!-- ============================================ -->
<style>
  /* Order specific utilities */
  .order-segment { background: #f1f5f9; padding: 4px; border-radius: 14px; }
  .order-segment .order-main-tab { border-radius: 10px; position:relative; font-weight:600; color:#64748b; }
  .order-segment .order-main-tab.active { background:#fff; color:#065f46; box-shadow:0 1px 3px rgba(0,0,0,.08),0 1px 2px rgba(0,0,0,.06); }
  .order-segment .order-main-tab:not(.active):hover{ color:#334155; }
  .order-card-surface{ background:#fff; border:1px solid #e2e8f0; border-radius:16px; box-shadow:0 1px 3px rgba(0,0,0,.06); }
  .stat-card{ position:relative; overflow:hidden; transition: transform .18s ease, box-shadow .18s ease; }
  .stat-card:hover{ transform: translateY(-1px); box-shadow:0 8px 20px rgba(0,0,0,.06); }
  .product-card{ border-radius:12px; }
  .product-card:hover{ transform: translateY(-1px); }
  /* compact weka-order cards — smaller, showcase-like density */
  #order-product-grid .product-card .product-img{ height:100%; }
  #order-product-grid{ gap:.5rem; }
  .skeleton{ background:linear-gradient(90deg,#f1f5f9 25%,#e2e8f0 37%,#f1f5f9 63%); background-size:400% 100%; animation:shimmer 1.2s ease infinite; }
  @keyframes shimmer{0%{background-position:100% 0}100%{background-position:-100% 0}}
  .order-row-card{ border:1px solid #e2e8f0; border-radius:14px; background:#fff; padding:14px; }
  /* Scrollbar thin */
  .thin-scrollbar::-webkit-scrollbar{ height:6px; width:6px; } .thin-scrollbar::-webkit-scrollbar-thumb{ background:#cbd5e1; border-radius:6px; }
  .thin-scrollbar{ scrollbar-width:thin; scrollbar-color:#cbd5e1 transparent; }
  /* Mobile table->cards */
  @media(max-width:640px){
    .orders-table-wrap table thead{ display:none; }
    .orders-table-wrap table, .orders-table-wrap tbody, .orders-table-wrap tr, .orders-table-wrap td{ display:block; width:100%!important; }
    .orders-table-wrap tr{ margin-bottom:10px; border:1px solid #e2e8f0; border-radius:14px; overflow:hidden; background:#fff; }
    .orders-table-wrap td{ border:none!important; border-bottom:1px solid #f1f5f9!important; padding:10px 14px!important; display:flex; justify-content:space-between; align-items:center; text-align:left!important; }
    .orders-table-wrap td:last-child{ border-bottom:none!important; justify-content:center; padding-top:12px!important; }
    .orders-table-wrap td::before{ content: attr(data-label); font-size:11px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:.04em; margin-right:8px; }
    .orders-table-wrap td[data-label=""]::before{ display:none; }
    .orders-table-wrap td[data-label="Vitendo"]{ flex-wrap:wrap; }
  }
</style>

<!-- Tab Navigation — pill segmented, sticky -->
<div class="order-segment flex gap-1 mb-4 sticky top-[56px] z-30 backdrop-blur supports-[backdrop-filter]:bg-slate-100/80">
    <button id="order-list-tab-btn"
            class="order-main-tab active flex-1 py-2.5 px-4 text-sm flex items-center justify-center gap-2"
            data-tab="order-list"
            onclick="switchOrderMainTab('order-list', this)">
        <span class="w-7 h-7 rounded-full bg-emerald-50 text-emerald-700 flex items-center justify-center text-xs"><i class="fas fa-list-ul"></i></span>
        <span class="hidden xs:inline">Orders</span><span class="inline xs:hidden">ORDERS</span>
        <span id="orders-count-mini" class="hidden bg-amber-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full min-w-[18px] text-center"></span>
    </button>
    <button id="order-place-tab-btn"
            class="order-main-tab flex-1 py-2.5 px-4 text-sm flex items-center justify-center gap-2"
            data-tab="order-place"
            onclick="switchOrderMainTab('order-place', this)">
        <span class="w-7 h-7 rounded-full bg-white text-slate-600 flex items-center justify-center text-xs border border-slate-200"><i class="fas fa-plus"></i></span>
        WEKA ORDER
        <span id="cart-badge-tab" class="bg-red-500 text-white text-[10px] font-bold rounded-full min-w-[18px] h-[18px] px-1 flex items-center justify-center hidden">0</span>
    </button>
</div>

<!-- ============================================ -->
<!-- TAB 1: ORDER LIST — modern, responsive, fast -->
<!-- ============================================ -->
<div id="order-list-tab-content" class="order-main-content active">
    <div class="order-card-surface p-4 md:p-5">
        <!-- Header — title + actions -->
        <div class="flex flex-wrap items-start justify-between gap-3 mb-4">
            <div class="min-w-0">
                <h2 class="text-[18px] md:text-xl font-extrabold tracking-tight text-slate-900 flex items-center gap-2">
                    <span class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center"><i class="fas fa-receipt text-sm"></i></span>
                    Orders
                    <span class="hidden sm:inline-flex items-center gap-1 text-xs font-semibold bg-slate-900 text-white px-2.5 py-1 rounded-full"><span id="orders-total-label">0</span> total</span>
                </h2>
                <p class="text-[13px] text-slate-500 mt-1">Fuatilia, hariri na chapisha orders kwa haraka — inafanya kazi vizuri hata ukiwa na maelfu.</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <div class="hidden sm:flex items-center gap-1 text-xs text-slate-500 bg-slate-50 border border-slate-200 rounded-full px-3 py-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Live
                </div>
                <button onclick="oOrdersLoad()" class="inline-flex items-center gap-2 px-3.5 py-2 text-sm font-semibold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl transition shadow-sm">
                    <i class="fas fa-sync-alt text-slate-500 text-xs"></i><span class="hidden sm:inline">Refresh</span>
                </button>
            </div>
        </div>

        <!-- Stats — scrollable on mobile, grid on desktop -->
        <div class="flex gap-2.5 overflow-x-auto thin-scrollbar pb-1 -mx-1 px-1 sm:grid sm:grid-cols-3 lg:grid-cols-6 sm:overflow-visible mb-4" id="order-stats-container">
            <div class="stat-card min-w-[132px] sm:min-w-0 flex-1 bg-white rounded-xl border border-slate-200 p-3 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-slate-900 text-white flex items-center justify-center shrink-0"><i class="fas fa-layer-group text-xs"></i></div>
                <div class="min-w-0"><p class="text-[10px] font-bold tracking-widest text-slate-400 uppercase">Jumla</p><p class="text-lg font-extrabold text-slate-900 leading-none" id="stat-total">0</p></div>
            </div>
            <div class="stat-card min-w-[132px] sm:min-w-0 flex-1 bg-amber-50/70 rounded-xl border border-amber-200 p-3 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0"><i class="fas fa-clock text-xs"></i></div>
                <div class="min-w-0"><p class="text-[10px] font-bold tracking-widest text-amber-700 uppercase">Saved</p><p class="text-lg font-extrabold text-amber-800 leading-none" id="stat-saved">0</p></div>
            </div>
            <div class="stat-card min-w-[132px] sm:min-w-0 flex-1 bg-blue-50/70 rounded-xl border border-blue-200 p-3 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center shrink-0"><i class="fas fa-check text-xs"></i></div>
                <div class="min-w-0"><p class="text-[10px] font-bold tracking-widest text-blue-700 uppercase">Confirmed</p><p class="text-lg font-extrabold text-blue-800 leading-none" id="stat-confirmed">0</p></div>
            </div>
            <div class="stat-card min-w-[132px] sm:min-w-0 flex-1 bg-emerald-50/70 rounded-xl border border-emerald-200 p-3 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0"><i class="fas fa-check-double text-xs"></i></div>
                <div class="min-w-0"><p class="text-[10px] font-bold tracking-widest text-emerald-700 uppercase">Paid</p><p class="text-lg font-extrabold text-emerald-800 leading-none" id="stat-paid">0</p></div>
            </div>
            <div class="stat-card min-w-[132px] sm:min-w-0 flex-1 bg-red-50/70 rounded-xl border border-red-200 p-3 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-red-500 text-white flex items-center justify-center shrink-0"><i class="fas fa-ban text-xs"></i></div>
                <div class="min-w-0"><p class="text-[10px] font-bold tracking-widest text-red-700 uppercase">Cancelled</p><p class="text-lg font-extrabold text-red-800 leading-none" id="stat-cancelled">0</p></div>
            </div>
            <div class="stat-card min-w-[132px] sm:min-w-0 flex-1 bg-violet-50/70 rounded-xl border border-violet-200 p-3 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-violet-600 text-white flex items-center justify-center shrink-0"><i class="fas fa-hourglass-half text-xs"></i></div>
                <div class="min-w-0"><p class="text-[10px] font-bold tracking-widest text-violet-700 uppercase">Bado</p><p class="text-lg font-extrabold text-violet-800 leading-none" id="stat-unpaid">0</p></div>
            </div>
        </div>

        <!-- Search + Filters — sticky within card -->
        <div class="sticky top-[68px] z-20 bg-white/95 backdrop-blur supports-[backdrop-filter]:bg-white/80 -mx-4 md:-mx-5 px-4 md:px-5 py-3 border-y border-slate-100 mb-4 flex flex-col lg:flex-row gap-3">
            <div class="relative flex-1 min-w-0">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" id="orders-search"
                       placeholder="Tafuta order #, jina au simu ya mteja..."
                       class="w-full pl-10 pr-9 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition placeholder:text-slate-400"
                       oninput="oOrdersSearchDebounced(this.value)">
                <button onclick="document.getElementById('orders-search').value='';oOrdersSearch('');" class="absolute right-2 top-1/2 -translate-y-1/2 w-7 h-7 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500"><i class="fas fa-times text-xs"></i></button>
            </div>
            <div class="flex gap-1.5 overflow-x-auto thin-scrollbar pb-1 lg:pb-0 -mx-1 px-1">
                <button onclick="oOrdersFilter('all',this)" class="order-filter-btn shrink-0 px-4 py-2 text-xs font-bold rounded-full transition bg-slate-900 text-white">Zote</button>
                <button onclick="oOrdersFilter('saved',this)" class="order-filter-btn shrink-0 px-4 py-2 text-xs font-semibold rounded-full transition bg-white border border-slate-200 text-slate-700 hover:bg-slate-50">Saved</button>
                <button onclick="oOrdersFilter('confirmed',this)" class="order-filter-btn shrink-0 px-4 py-2 text-xs font-semibold rounded-full transition bg-white border border-slate-200 text-slate-700 hover:bg-slate-50">Confirmed</button>
                <button onclick="oOrdersFilter('paid',this)" class="order-filter-btn shrink-0 px-4 py-2 text-xs font-semibold rounded-full transition bg-white border border-slate-200 text-slate-700 hover:bg-slate-50">Paid</button>
                <button onclick="oOrdersFilter('cancelled',this)" class="order-filter-btn shrink-0 px-4 py-2 text-xs font-semibold rounded-full transition bg-white border border-slate-200 text-slate-700 hover:bg-slate-50">Cancelled</button>
            </div>
        </div>

        <!-- Orders Table — responsive: mobile cards via CSS -->
        <div class="orders-table-wrap overflow-x-auto rounded-xl border border-slate-200 bg-slate-50/50">
            <table class="w-full text-sm bg-white">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold tracking-widest text-slate-500 uppercase">
                        <th class="px-4 py-3 text-left whitespace-nowrap">Order</th>
                        <th class="px-4 py-3 text-left">Mteja</th>
                        <th class="px-4 py-3 text-left hidden md:table-cell">Bidhaa</th>
                        <th class="px-4 py-3 text-right whitespace-nowrap">Jumla</th>
                        <th class="px-4 py-3 text-left">Hali</th>
                        <th class="px-4 py-3 text-left hidden sm:table-cell">Tarehe</th>
                        <th class="px-4 py-3 text-center">Vitendo</th>
                    </tr>
                </thead>
                <tbody id="orders-tbody" class="divide-y divide-slate-100">
                    <tr>
                        <td colspan="7" class="text-center py-10 text-slate-500">
                            <div class="flex flex-col items-center gap-2">
                                <span class="w-8 h-8 rounded-full border-2 border-slate-200 border-t-emerald-600 animate-spin"></span>
                                <span class="text-sm">Inapakia orders…</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div id="orders-pagination" class="mt-4"></div>
    </div>
</div>

<!-- ============================================ -->
<!-- TAB 2: WEKA ORDER — fast add, polished grid -->
<!-- ============================================ -->
<div id="order-place-tab-content" class="order-main-content hidden">
    <div class="order-card-surface p-4 md:p-5">
        <!-- Header -->
        <div class="flex flex-wrap items-start justify-between gap-3 mb-4">
            <div class="min-w-0">
                <h2 class="text-[18px] md:text-xl font-extrabold tracking-tight text-slate-900 flex items-center gap-2">
                    <span class="w-9 h-9 rounded-xl bg-slate-900 text-white flex items-center justify-center"><i class="fas fa-plus text-xs"></i></span>
                    Weka Order
                    <span class="hidden sm:inline text-xs font-medium text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full border border-slate-200">Gusa bidhaa kuongeza</span>
                </h2>
                <p class="text-[13px] text-slate-500 mt-1">Chagua bidhaa, kiasi kitaongezeka kiotomatiki. Kikapu kina-save papo hapo — hata ukifunga ukurasa.</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button onclick="oOpenCart()"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl transition shadow-sm">
                    <i class="fas fa-shopping-cart text-xs"></i>
                    <span>Kikapu</span>
                    <span id="cart-badge" class="inline-flex items-center justify-center bg-white text-emerald-700 text-xs font-extrabold rounded-full min-w-[20px] h-5 px-1.5">0</span>
                </button>
                <button onclick="oClearCart()"
                        class="w-10 h-10 flex items-center justify-center text-slate-500 hover:text-red-600 hover:bg-red-50 rounded-xl border border-slate-200 bg-white transition"
                        title="Futa kikapu">
                    <i class="fas fa-trash-alt text-xs"></i>
                </button>
            </div>
        </div>

        <!-- Product Filters — sticky -->
        <div class="sticky top-[68px] z-20 bg-white/95 backdrop-blur supports-[backdrop-filter]:bg-white/80 -mx-4 md:-mx-5 px-4 md:px-5 py-3 border-y border-slate-100 mb-4 flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1 min-w-0">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" id="oprod-search"
                       placeholder="Tafuta bidhaa, aina au kipimo..."
                       class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition placeholder:text-slate-400"
                       oninput="oProdSearchDebounced(this.value)">
            </div>
            <div class="flex gap-1.5 overflow-x-auto thin-scrollbar">
                <button onclick="oProdFilter('all',this)" class="product-filter-btn shrink-0 px-4 py-2 text-xs font-bold rounded-full transition bg-slate-900 text-white">Zote</button>
                <button onclick="oProdFilter('jumla',this)" class="product-filter-btn shrink-0 px-4 py-2 text-xs font-semibold rounded-full transition bg-white border border-slate-200 text-slate-700 hover:bg-slate-50">Jumla</button>
                <button onclick="oProdFilter('low_stock',this)" class="product-filter-btn shrink-0 px-4 py-2 text-xs font-semibold rounded-full transition bg-white border border-slate-200 text-slate-700 hover:bg-slate-50"><i class="fas fa-exclamation-triangle mr-1 text-amber-500"></i>Hisa ndogo</button>
            </div>
        </div>

        <!-- Product Grid — compact, showcase-like, virtual pagination + lazy -->
        <div class="thin-scrollbar" style="max-height: min(50vh, 520px); overflow-y:auto; padding-right:4px;" id="product-scroll-wrap">
            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-7 gap-2" id="order-product-grid">
                @forelse($bidhaa as $product)
                    @php
                        $imgUrl = $product->image_data_url ?? null;
                        $oos    = $product->idadi <= 0;
                        $lowStock = $product->idadi > 0 && $product->idadi < 5;
                    @endphp
                    <div class="product-card group rounded-xl border border-slate-200 bg-white overflow-hidden transition-all duration-200 hover:shadow-md hover:border-emerald-200 cursor-pointer flex flex-col {{ $oos ? 'opacity-60' : '' }}"
                         data-id="{{ $product->id }}"
                         data-name="{{ $product->jina }}"
                         data-price="{{ $product->bei_kuuza }}"
                         data-wholesale="{{ $product->bei_uzo_jumla ?? 0 }}"
                         data-stock="{{ $product->idadi }}"
                         data-aina="{{ $product->aina ?? '' }}"
                         data-kipimo="{{ $product->kipimo ?? '' }}"
                         data-image="{{ $imgUrl ?? '' }}"
                         onclick="oAddToCart(this)">
                        <!-- Image — like showcase: cover, subtle, small -->
                        <div class="relative bg-slate-50 aspect-[4/3] flex items-center justify-center overflow-hidden">
                            @if($imgUrl)
                                <img data-src="{{ $imgUrl }}" src="{{ $imgUrl }}"
                                     alt="{{ $product->jina }}"
                                     class="product-img w-full h-full object-cover transition duration-300 group-hover:scale-[1.03]"
                                     loading="lazy"
                                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                                <div class="product-placeholder hidden absolute inset-0 flex items-center justify-center text-slate-400 bg-slate-50">
                                    <i class="fas fa-box text-lg"></i>
                                </div>
                            @else
                                <div class="flex items-center justify-center w-full h-full text-slate-300 bg-slate-50">
                                    <i class="fas fa-box text-xl"></i>
                                </div>
                            @endif
                            @if($oos)
                                <span class="absolute top-1.5 left-1.5 bg-slate-900 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full">Imeisha</span>
                            @elseif($lowStock)
                                <span class="absolute top-1.5 left-1.5 bg-amber-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full">{{ number_format($product->idadi,0) }} baki</span>
                            @endif
                            <span class="absolute bottom-1.5 right-1.5 w-6 h-6 rounded-full bg-white shadow border border-slate-200 flex items-center justify-center text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition"><i class="fas fa-plus text-[8px]"></i></span>
                        </div>
                        <!-- Info — compact, modern -->
                        <div class="p-2 flex flex-col flex-1">
                            <div class="text-[11px] font-bold text-slate-900 leading-tight line-clamp-2 min-h-[28px]" title="{{ $product->jina }}">{{ $product->jina }}</div>
                            @if($product->aina || $product->kipimo)
                                <div class="flex flex-wrap gap-0.5 mt-1">
                                    @if($product->aina)<span class="text-[9px] font-medium bg-slate-900 text-white px-1 py-0.5 rounded-full truncate max-w-[60px]">{{ $product->aina }}</span>@endif
                                    @if($product->kipimo)<span class="text-[9px] font-medium bg-white border border-slate-200 text-slate-600 px-1 py-0.5 rounded-full">{{ $product->kipimo }}</span>@endif
                                </div>
                            @endif
                            <div class="mt-1 flex flex-col gap-0.5">
                                <div class="text-xs font-extrabold text-emerald-700 leading-none">Tsh {{ number_format($product->bei_kuuza, 0) }}</div>
                                @if($product->bei_uzo_jumla && $product->bei_uzo_jumla > 0)<div class="text-[9px] text-slate-500 leading-none">Jumla {{ number_format($product->bei_uzo_jumla,0) }}</div>@endif
                            </div>
                            <div class="mt-1.5">
                                <button class="w-full py-1.5 bg-slate-900 group-hover:bg-emerald-600 text-white text-[11px] font-bold rounded-lg transition flex items-center justify-center gap-1 {{ $oos ? 'opacity-50 cursor-not-allowed' : '' }}">
                                    <i class="fas fa-plus text-[9px]"></i> Ongeza
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-10 text-slate-500">
                        <i class="fas fa-box-open text-3xl mb-2 block text-slate-300"></i>
                        <p class="text-sm">Hakuna bidhaa</p>
                    </div>
                @endforelse
            </div>
        </div>
        <!-- Product pagination (client-side, for speed) -->
        <div id="product-pagination" class="mt-4 flex items-center justify-between gap-3 flex-wrap"></div>
        <p class="text-[11px] text-slate-400 mt-2">Inaonyesha bidhaa kwa kurasa — haraka hata ukiwa na bidhaa 5,000+. Tafuta hapo juu kuchuja papo hapo.</p>
    </div>
</div>

<!-- ============================================ -->
<!-- CART — Bottom sheet on mobile, centered modal on desktop -->
<!-- ============================================ -->
<div id="o-cart-backdrop" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden items-end sm:items-center justify-center p-0 sm:p-4" onclick="oCartBackdropClick(event)">
    <div class="bg-white w-full sm:rounded-2xl rounded-t-2xl shadow-2xl sm:max-w-lg sm:max-h-[88vh] max-h-[92vh] flex flex-col overflow-hidden animate-[slideUp_.2s_ease]" onclick="event.stopPropagation()">
        <!-- Header -->
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center"><i class="fas fa-shopping-bag"></i></div>
                <div>
                    <h3 class="font-extrabold text-slate-900 leading-none">Kikapu chako</h3>
                    <p class="text-xs text-slate-500"><span id="cart-count">0</span> bidhaa • papo hapo saving</p>
                </div>
            </div>
            <button onclick="oCloseCart()" class="w-9 h-9 flex items-center justify-center text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition"><i class="fas fa-times"></i></button>
        </div>
        <!-- Items -->
        <div class="flex-1 overflow-y-auto px-4 py-3 space-y-0.5 thin-scrollbar" id="cart-items-wrap">
            <div class="text-center text-slate-500 py-10">
                <i class="fas fa-shopping-basket text-4xl mb-2 block text-slate-300"></i>
                <p class="text-sm font-medium">Kikapu ni tupu</p>
                <p class="text-xs mt-1">Gusa bidhaa ili kuongeza — haraka sana</p>
            </div>
        </div>
        <!-- Footer — sticky -->
        <div class="px-5 py-4 border-t border-slate-100 bg-slate-50 shrink-0">
            <div class="flex justify-between items-center mb-3">
                <span class="text-sm font-medium text-slate-600">Jumla ya malipo</span>
                <span id="cart-total" class="text-xl font-extrabold text-emerald-700">0 TZS</span>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <button onclick="oSaveFlow('saved')" class="py-3 bg-white border border-slate-200 hover:bg-slate-50 text-slate-900 text-sm font-bold rounded-xl transition flex items-center justify-center gap-2">
                    <i class="fas fa-bookmark text-amber-500"></i> Hifadhi
                </button>
                <button onclick="oSaveFlow('paid')" class="py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl transition flex items-center justify-center gap-2 shadow">
                    <i class="fas fa-bolt"></i> Lipa sasa
                </button>
            </div>
            <p class="text-[11px] text-center text-slate-400 mt-2">Hifadhi = order ya baadaye • Lipa = punguza stock & tengeneza mauzo</p>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- CUSTOMER MODAL — cleaner, larger tap targets -->
<!-- ============================================ -->
<div id="modal-customer" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-hidden flex flex-col">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between shrink-0">
            <h3 class="font-extrabold text-slate-900 flex items-center gap-2"><span class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-xs"><i class="fas fa-user"></i></span> Chagua Mteja</h3>
            <button onclick="oCustModalClose()" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition"><i class="fas fa-times"></i></button>
        </div>
        <div class="flex border-b border-slate-100 bg-slate-50 px-2 py-1 gap-1 shrink-0">
            <button class="customer-tab-btn flex-1 py-2.5 text-sm font-bold rounded-xl bg-white shadow text-emerald-700" onclick="oCustTab('existing',this)"><i class="fas fa-users mr-1"></i>Wateja</button>
            <button class="customer-tab-btn flex-1 py-2.5 text-sm font-medium rounded-xl text-slate-600 hover:bg-white" onclick="oCustTab('new',this)"><i class="fas fa-user-plus mr-1"></i>Mteja Mpya</button>
        </div>
        <div class="p-4 overflow-y-auto thin-scrollbar flex-1">
            <div id="ctab-existing" class="customer-tab-pane">
                <button onclick="oSelectCustomer(null,'Walk-in Customer','')"
                        class="w-full text-left px-4 py-3 rounded-xl border-2 border-slate-200 hover:border-emerald-300 hover:bg-emerald-50 transition flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-full bg-slate-900 text-white flex items-center justify-center flex-shrink-0"><i class="fas fa-user-secret text-xs"></i></div>
                    <div><div class="text-sm font-bold text-slate-800">Walk-in Customer</div><div class="text-xs text-slate-500">Mteja asiyesajiliwa</div></div>
                    <i class="fas fa-chevron-right ml-auto text-slate-300"></i>
                </button>
                <div class="relative mb-2">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input type="text" id="cust-search-input"
                           placeholder="Tafuta jina au simu..."
                           class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition"
                           oninput="oCustSearchDebounced(this.value)">
                </div>
                <div id="cust-list" class="space-y-1 max-h-56 overflow-y-auto thin-scrollbar pr-1">
                    @forelse($wateja ?? [] as $mteja)
                        <button onclick="oSelectCustomer('{{ $mteja->id }}','{{ addslashes($mteja->jina) }}','{{ $mteja->simu }}')"
                                class="customer-item w-full text-left px-3 py-2.5 rounded-xl border border-slate-100 hover:border-emerald-200 hover:bg-emerald-50 transition flex items-center gap-3"
                                data-name="{{ strtolower($mteja->jina) }}" data-simu="{{ $mteja->simu }}">
                            <div class="w-9 h-9 rounded-full bg-slate-900 text-white flex items-center justify-center font-extrabold text-sm shrink-0">{{ strtoupper(substr($mteja->jina,0,1)) }}</div>
                            <div class="min-w-0"><div class="text-sm font-semibold text-slate-800 truncate">{{ $mteja->jina }}</div><div class="text-xs text-slate-500">{{ $mteja->simu }}</div></div>
                        </button>
                    @empty
                        <p class="text-center text-slate-500 text-sm py-6">Hakuna wateja walioorodheshwa</p>
                    @endforelse
                </div>
            </div>
            <div id="ctab-new" class="customer-tab-pane hidden">
                <div class="space-y-3">
                    <div><label class="text-xs font-bold text-slate-600 block mb-1">Jina la Mteja *</label><input type="text" id="nc-name" placeholder="Jina kamili..." class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20"></div>
                    <div><label class="text-xs font-bold text-slate-600 block mb-1">Namba ya Simu *</label><input type="tel" id="nc-phone" placeholder="07… … …" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20"></div>
                    <div><label class="text-xs font-bold text-slate-600 block mb-1">Anapoishi <span class="text-slate-400 font-normal">(hiari)</span></label><input type="text" id="nc-address" placeholder="Mtaa, mji..." class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20"></div>
                    <button onclick="oSaveNewCustomer()" id="nc-save-btn" class="w-full py-3 bg-slate-900 hover:bg-black text-white text-sm font-bold rounded-xl transition flex items-center justify-center gap-2"><i class="fas fa-user-plus"></i> Ongeza na Endelea</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- VIEW / EDIT / DELETE — polished -->
<!-- ============================================ -->
<div id="modal-view-order" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-hidden flex flex-col">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between shrink-0">
            <h3 class="font-extrabold text-slate-900 flex items-center gap-2"><span class="w-8 h-8 rounded-xl bg-blue-600 text-white flex items-center justify-center text-xs"><i class="fas fa-eye"></i></span> Taarifa za Order</h3>
            <button onclick="oViewClose()" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition"><i class="fas fa-times"></i></button>
        </div>
        <div class="p-5 overflow-y-auto thin-scrollbar" id="view-order-body"></div>
    </div>
</div>

<div id="modal-edit-status" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-extrabold text-slate-900">Badilisha Hali</h3>
            <button onclick="oEditStatusClose()" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition"><i class="fas fa-times"></i></button>
        </div>
        <div class="p-5">
            <p class="text-sm text-slate-600 mb-4">Order: <strong id="edit-status-num" class="font-mono bg-slate-100 px-2 py-1 rounded-lg text-slate-800"></strong></p>
            <div class="grid grid-cols-2 gap-2" id="status-btn-group">
                <button onclick="oUpdateStatus('saved')" class="py-3 bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold rounded-xl transition">Saved</button>
                <button onclick="oUpdateStatus('confirmed')" class="py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl transition">Confirmed</button>
                <button onclick="oUpdateStatus('paid')" class="py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl transition">Paid</button>
                <button onclick="oUpdateStatus('cancelled')" class="py-3 bg-red-500 hover:bg-red-600 text-white text-sm font-bold rounded-xl transition">Cancelled</button>
            </div>
            <button onclick="oEditStatusClose()" class="mt-3 w-full py-2.5 border border-slate-200 rounded-xl text-sm text-slate-600 hover:bg-slate-50 transition">Ghairi</button>
        </div>
    </div>
</div>

<div id="modal-delete-order" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-red-100 text-red-600 flex items-center justify-center"><i class="fas fa-trash-alt text-xs"></i></div>
            <h3 class="font-extrabold text-slate-900">Futa Order</h3>
        </div>
        <div class="p-5">
            <p class="text-sm text-slate-600 mb-2">Una uhakika unataka kufuta order hii?</p>
            <p class="text-xs text-slate-400 mb-4">Haiwezi kurejeshwa — kama ilikuwa Paid, stock italipuliwa kurejeshwa automatically.</p>
            <div class="flex gap-2">
                <button onclick="oDeleteClose()" class="flex-1 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">Ghairi</button>
                <button onclick="oConfirmDelete()" id="delete-confirm-btn" class="flex-1 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-xl transition">Futa</button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- PRINT — in-page modal with hidden iframe (no new window) -->
<!-- ============================================ -->
<div id="print-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[60] hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-[420px] max-h-[90vh] flex flex-col overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between shrink-0">
            <h3 class="font-bold text-slate-900 flex items-center gap-2"><i class="fas fa-print text-emerald-600"></i> Chapisha Risiti</h3>
            <button onclick="closePrintModal()" class="w-8 h-8 flex items-center justify-center rounded-xl hover:bg-slate-100 text-slate-500"><i class="fas fa-times"></i></button>
        </div>
        <div class="flex-1 bg-slate-50 p-2 overflow-hidden">
            <iframe id="print-iframe" class="w-full h-[58vh] bg-white rounded-xl border border-slate-200" title="Print preview"></iframe>
        </div>
        <div class="p-3 border-t border-slate-100 flex gap-2 shrink-0">
            <button onclick="closePrintModal()" class="flex-1 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold hover:bg-slate-50">Funga</button>
            <button onclick="triggerIframePrint()" class="flex-1 py-2.5 rounded-xl bg-slate-900 hover:bg-black text-white text-sm font-bold flex items-center justify-center gap-2"><i class="fas fa-print text-xs"></i> Print</button>
        </div>
    </div>
</div>

<!-- Toast -->
<div id="o-toast-wrap" class="fixed top-4 left-1/2 -translate-x-1/2 z-[9999] flex flex-col gap-2 items-center pointer-events-none max-w-[90%] w-[min(420px,90%)]"></div>

<!-- ============================================ -->
<!-- JAVASCRIPT — same logic, faster & modern -->
<!-- ============================================ -->
<script>
(function(){
'use strict';

/* ── helpers: debounce, etc ── */
function debounce(fn, ms){ var t; return function(){ var a=arguments, ctx=this; clearTimeout(t); t=setTimeout(function(){ fn.apply(ctx,a); }, ms); }; }
function oEsc(str){ return String(str||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

/* ── state ── */
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
var oCurrentPage = 1, oLastPage = 1, oTotalOrders = 0, oPerPage = 15;

/* product pagination for speed */
var oProdPerPage = 24, oProdPage = 1, oProdFilteredIdx = [];

/* ── main tabs ── */
function switchOrderMainTab(tab, btn){
    document.querySelectorAll('.order-main-tab').forEach(function(b){ b.classList.remove('active'); });
    if(btn) btn.classList.add('active');
    document.querySelectorAll('.order-main-content').forEach(function(el){ el.classList.add('hidden'); });
    var content = document.getElementById(tab + '-tab-content'); if(content){ content.classList.remove('hidden'); if(tab==='order-list') oOrdersLoad(); }
    try{ localStorage.setItem('mauzo_order_tab', tab); }catch(e){}
    if(tab==='order-place') setTimeout(function(){ applyProdPagination(); }, 50);
}

/* ── toast — centered, stacked, fast ── */
function oToast(msg, type){
    type = type||'info'; var wrap=document.getElementById('o-toast-wrap'); if(!wrap) return;
    var d=document.createElement('div');
    var cls={success:'bg-emerald-600 text-white', error:'bg-red-600 text-white', info:'bg-slate-900 text-white', warning:'bg-amber-500 text-white'}[type]||'bg-slate-900 text-white';
    d.className='px-4 py-3 rounded-xl text-sm font-semibold shadow-xl pointer-events-auto flex items-center gap-2 '+cls+' w-full';
    var icon={success:'fa-check-circle', error:'fa-exclamation-circle', info:'fa-info-circle', warning:'fa-exclamation-triangle'}[type]||'fa-info-circle';
    d.innerHTML='<i class="fas '+icon+'"></i><span class="flex-1">'+oEsc(msg)+'</span>';
    wrap.appendChild(d);
    d.animate([{opacity:0, transform:'translateY(-8px)'},{opacity:1, transform:'translateY(0)'}],{duration:180, easing:'ease-out'});
    setTimeout(function(){ d.style.opacity='0'; d.style.transform='translateY(-8px)'; setTimeout(function(){ d.remove(); }, 240); }, 2600);
}

/* ── header mini counters ── */
function oUpdateHeaderCounters(){
    var totalEl=document.getElementById('orders-total-label'); if(totalEl) totalEl.textContent = oTotalOrders||0;
    var mini=document.getElementById('orders-count-mini');
    var unpaid=(oOrders||[]).filter(function(o){ return o.status==='saved'||o.status==='confirmed'; }).length;
    if(mini){ if(unpaid>0){ mini.textContent=unpaid; mini.classList.remove('hidden'); } else mini.classList.add('hidden'); }
    var tabBadge=document.getElementById('cart-badge-tab'); if(tabBadge){
        var c=oCart.reduce(function(s,i){ return s+i.qty;},0);
        if(c>0){ tabBadge.textContent=c; tabBadge.classList.remove('hidden'); } else tabBadge.classList.add('hidden');
    }
}
function oCheckNewOrders(fresh){ oKnownOrderCount=fresh.length; }

/* ── product search / filter + pagination (fast for 5k+ products) ── */
function collectProdFiltered(){
    var term=(document.getElementById('oprod-search')?.value||'').toLowerCase().trim();
    var cards=document.querySelectorAll('#order-product-grid .product-card');
    var idx=[]; cards.forEach(function(c,i){
        var name=(c.dataset.name||'').toLowerCase(), aina=(c.dataset.aina||'').toLowerCase(), kip=(c.dataset.kipimo||'').toLowerCase();
        var ms=!term || name.includes(term) || aina.includes(term) || kip.includes(term);
        var mt=oTypeMatch(c);
        if(ms&&mt) idx.push(i);
    });
    oProdFilteredIdx=idx; oProdPage=1;
}
function oTypeMatch(c){
    if(oProdType==='all') return true;
    var stock=parseFloat(c.dataset.stock)||0, ws=parseFloat(c.dataset.wholesale)||0;
    if(oProdType==='jumla') return ws>0;
    if(oProdType==='low_stock') return stock>0 && stock<10;
    return true;
}
function applyProdPagination(){
    var cards=document.querySelectorAll('#order-product-grid .product-card');
    if(!cards.length){ renderProdPagination(0); return; }
    if(!oProdFilteredIdx.length && (document.getElementById('oprod-search')?.value||'').trim()==='' && oProdType==='all'){
        // first time: collect all
        collectProdFiltered();
        if(!oProdFilteredIdx.length){ oProdFilteredIdx = Array.from(cards, function(_,i){return i;}); }
    }
    var total=oProdFilteredIdx.length, totalPages=Math.max(1, Math.ceil(total/oProdPerPage));
    if(oProdPage>totalPages) oProdPage=totalPages; if(oProdPage<1) oProdPage=1;
    var start=(oProdPage-1)*oProdPerPage, end=Math.min(start+oProdPerPage, total);
    var visibleSet=new Set(oProdFilteredIdx.slice(start,end));
    cards.forEach(function(c,i){ c.style.display = visibleSet.has(i) ? '' : 'none'; });
    // lazy: only observe visible
    observeVisibleImages();
    renderProdPagination(total);
}
function renderProdPagination(total){
    var el=document.getElementById('product-pagination'); if(!el) return;
    if(total<=oProdPerPage){ el.innerHTML = total>0 ? '<span class="text-xs text-slate-500">Inaonyesha '+total+' bidhaa</span><span></span>' : ''; return; }
    var totalPages=Math.ceil(total/oProdPerPage);
    var from=(oProdPage-1)*oProdPerPage+1, to=Math.min(oProdPage*oProdPerPage, total);
    var html='<span class="text-xs text-slate-500">'+from+'–'+to+' ya '+total+'</span><div class="flex gap-1">';
    html+='<button '+(oProdPage<=1?'disabled':'onclick="oProdPageGo('+(oProdPage-1)+')"')+' class="px-3 py-1.5 rounded-full border text-xs font-semibold '+(oProdPage<=1?'opacity-40 cursor-not-allowed border-slate-200 text-slate-400':'bg-white border-slate-200 hover:bg-slate-50')+'"><i class="fas fa-chevron-left mr-1"></i>Nyuma</button>';
    var max=5, s=Math.max(1,oProdPage-Math.floor(max/2)), e=Math.min(totalPages,s+max-1); if(e-s<max-1) s=Math.max(1,e-max+1);
    if(s>1){ html+='<button onclick="oProdPageGo(1)" class="px-3 py-1.5 rounded-full border bg-white text-xs">1</button>'; if(s>2) html+='<span class="px-1 text-slate-400">…</span>'; }
    for(var i=s;i<=e;i++){ html+='<button onclick="oProdPageGo('+i+')" class="px-3 py-1.5 rounded-full text-xs font-bold '+(i===oProdPage?'bg-slate-900 text-white':'bg-white border border-slate-200 hover:bg-slate-50')+'">'+i+'</button>'; }
    if(e<totalPages){ if(e<totalPages-1) html+='<span class="px-1 text-slate-400">…</span>'; html+='<button onclick="oProdPageGo('+totalPages+')" class="px-3 py-1.5 rounded-full border bg-white text-xs">'+totalPages+'</button>'; }
    html+='<button '+(oProdPage>=totalPages?'disabled':'onclick="oProdPageGo('+(oProdPage+1)+')"')+' class="px-3 py-1.5 rounded-full border text-xs font-semibold '+(oProdPage>=totalPages?'opacity-40 cursor-not-allowed border-slate-200 text-slate-400':'bg-white border-slate-200 hover:bg-slate-50')+'">Mbele<i class="fas fa-chevron-right ml-1"></i></button>';
    html+='</div>'; el.innerHTML=html;
}
function oProdSearch(val){
    oProdPage=1;
    collectProdFiltered();
    applyProdPagination();
}
var oProdSearchDebounced = debounce(oProdSearch, 180);
function oProdPageGo(p){ oProdPage=p; applyProdPagination(); var wrap=document.getElementById('product-scroll-wrap'); if(wrap) wrap.scrollTop=0; }
function oProdFilter(type, btn){
    oProdType=type;
    document.querySelectorAll('.product-filter-btn').forEach(function(b){ b.className='product-filter-btn shrink-0 px-4 py-2 text-xs font-semibold rounded-full transition bg-white border border-slate-200 text-slate-700 hover:bg-slate-50'; });
    if(btn) btn.className='product-filter-btn shrink-0 px-4 py-2 text-xs font-bold rounded-full transition bg-slate-900 text-white';
    collectProdFiltered(); applyProdPagination();
}

/* lazy image observer — keeps 60fps even with 1000+ base64 */
var imgObserver=null;
function observeVisibleImages(){
    var imgs=document.querySelectorAll('#order-product-grid .product-card:not([style*="display: none"]) .product-img[data-src]');
    if(!('IntersectionObserver' in window) || !imgs.length) return;
    if(!imgObserver){
        imgObserver=new IntersectionObserver(function(entries){
            entries.forEach(function(e){ if(e.isIntersecting){ var img=e.target; if(img.dataset.src && img.getAttribute('src')!==img.dataset.src) img.src=img.dataset.src; imgObserver.unobserve(img); }});
        }, {root: document.getElementById('product-scroll-wrap'), rootMargin:'300px 0px', threshold:0.01});
    }
    imgs.forEach(function(img){ imgObserver.observe(img); });
}

/* ── add to cart ── */
function oAddToCart(card){
    if(card.classList.contains('opacity-60')){ oToast('Bidhaa hii imeisha!','error'); return; }
    var id=card.dataset.id, name=card.dataset.name, price=parseFloat(card.dataset.price)||0, ws=parseFloat(card.dataset.wholesale)||0, stock=parseFloat(card.dataset.stock)||0, image=card.dataset.image||'', aina=card.dataset.aina||'', kip=card.dataset.kipimo||'';
    var useWs=oProdType==='jumla' && ws>0; var finalPr=useWs?ws:price;
    var existing=oCart.find(function(i){return i.id===id;});
    if(existing){ if(existing.qty>=stock){ oToast('Hisa haitoshi!','error'); return; } existing.qty++; }
    else oCart.push({id:id, name:name, price:finalPr, qty:1, stock:stock, image:image, aina:aina, kipimo:kip});
    oCartRender(); oToast(name+' imeongezwa ✓','success');
    // subtlee quick feedback on card
    card.animate([{transform:'scale(1)'},{transform:'scale(0.97)'},{transform:'scale(1)'}],{duration:160});
}

/* ── cart ── */
function oCartRemove(idx){ oCart.splice(idx,1); oCartRender(); }
function oCartQty(idx, delta){
    var item=oCart[idx]; if(!item) return; var nq=item.qty+delta;
    if(nq<=0) oCart.splice(idx,1);
    else if(nq>item.stock){ oToast('Hisa haitoshi!','error'); return; }
    else item.qty=nq;
    oCartRender();
}
function oCartRender(){
    var count=oCart.reduce(function(s,i){return s+i.qty;},0), total=oCart.reduce(function(s,i){return s+(i.price*i.qty);},0);
    var badge=document.getElementById('cart-badge'); if(badge){ badge.textContent=count; badge.style.display=count>0?'inline-flex':'none'; if(count>0) badge.classList.remove('hidden'); else badge.classList.add('hidden'); } // keep legacy hidden logic too
    var tabBadge=document.getElementById('cart-badge-tab'); if(tabBadge){ if(count>0){ tabBadge.textContent=count; tabBadge.classList.remove('hidden'); } else tabBadge.classList.add('hidden'); }
    var elCount=document.getElementById('cart-count'), elTotal=document.getElementById('cart-total');
    if(elCount) elCount.textContent=count;
    if(elTotal) elTotal.textContent=total.toLocaleString()+' TZS';
    var wrap=document.getElementById('cart-items-wrap'); if(!wrap) return;
    if(oCart.length===0){ wrap.innerHTML='<div class="text-center text-slate-500 py-10"><i class="fas fa-shopping-basket text-4xl mb-2 block text-slate-300"></i><p class="text-sm font-semibold">Kikapu ni tupu</p><p class="text-xs mt-1">Gusa bidhaa ili kuongeza</p></div>'; oUpdateHeaderCounters(); return; }
    wrap.innerHTML=oCart.map(function(item, idx){
        var imgHtml=item.image? '<img src="'+oEsc(item.image)+'" alt="" class="w-full h-full object-cover" loading="lazy" onerror="this.style.display=\'none\';this.parentElement.innerHTML=\'<i class=\\\'fas fa-box\\\'></i>\'">' : '<i class="fas fa-box text-slate-400 text-xl"></i>';
        var badges=''; if(item.aina) badges+='<span class="text-[10px] bg-slate-900 text-white px-2 py-0.5 rounded-full">'+oEsc(item.aina)+'</span> '; if(item.kipimo) badges+='<span class="text-[10px] bg-white border border-slate-200 text-slate-700 px-2 py-0.5 rounded-full">'+oEsc(item.kipimo)+'</span>';
        return '<div class="flex items-center gap-3 py-3 border-b border-slate-100 last:border-0">'
            +'<div class="w-12 h-12 rounded-xl bg-slate-50 flex items-center justify-center shrink-0 overflow-hidden border border-slate-100">'+imgHtml+'</div>'
            +'<div class="flex-1 min-w-0"><div class="text-sm font-bold text-slate-900 truncate">'+oEsc(item.name)+'</div><div class="text-xs text-slate-500">'+item.price.toLocaleString()+' TZS</div><div class="mt-1 flex flex-wrap gap-1">'+badges+'</div></div>'
            +'<div class="flex items-center gap-1 shrink-0"><button onclick="oCartQty('+idx+',-1)" class="w-8 h-8 rounded-full border border-slate-200 bg-white hover:bg-slate-50 flex items-center justify-center text-slate-700"><i class="fas fa-minus text-[10px]"></i></button><span class="text-sm font-extrabold w-6 text-center">'+item.qty+'</span><button onclick="oCartQty('+idx+',1)" class="w-8 h-8 rounded-full border border-slate-200 bg-white hover:bg-slate-50 flex items-center justify-center text-slate-700"><i class="fas fa-plus text-[10px]"></i></button></div>'
            +'<div class="text-sm font-extrabold min-w-[68px] text-right text-emerald-700 shrink-0">'+(item.price*item.qty).toLocaleString()+'</div>'
            +'<button onclick="oCartRemove('+idx+')" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-full shrink-0"><i class="fas fa-times text-xs"></i></button></div>';
    }).join('');
    oUpdateHeaderCounters();
    try{ localStorage.setItem('mauzo_oCart_'+(document.querySelector('meta[name="company-id"]')?.content||'0'), JSON.stringify(oCart)); }catch(e){}
}
function oOpenCart(){ var el=document.getElementById('o-cart-backdrop'); if(!el) return; el.classList.remove('hidden'); el.classList.add('flex'); document.body.style.overflow='hidden'; }
function oCloseCart(){ var el=document.getElementById('o-cart-backdrop'); if(!el) return; el.classList.add('hidden'); el.classList.remove('flex'); document.body.style.overflow=''; }
function oCartBackdropClick(e){ if(e.target===document.getElementById('o-cart-backdrop')) oCloseCart(); }
function oClearCart(){
    if(oCart.length===0){ oToast('Kikapu tayari ni tupu','info'); return; }
    if(!confirm('Futa bidhaa zote kwenye kikapu?')) return;
    oCart=[];
    try{
        var k='mauzo_oCart_'+(document.querySelector('meta[name="company-id"]')?.content||'0');
        localStorage.setItem(k, JSON.stringify(oCart));
        localStorage.removeItem(k+'_pending');
    }catch(e){}
    oCartRender();
    oCloseCart();
    oToast('Kikapu kimefutwa','info');
}

/* ── save flow ── */
function oSaveFlow(status){ if(oCart.length===0){ oToast('Ongeza bidhaa kwenye kikapu kwanza!','error'); return; } oPendingSt=status; oSelectedCust={id:null,name:'Walk-in Customer',phone:''}; oCloseCart(); oCustModalOpen(); }
function oSelectCustomer(id,name,phone){ oSelectedCust={id:id||null,name:name||'Walk-in Customer',phone:phone||''}; oCustModalClose(); oSubmitOrder(oPendingSt); }
async function oSaveNewCustomer(){
    var name=(document.getElementById('nc-name')?.value||'').trim(), phone=(document.getElementById('nc-phone')?.value||'').trim(), address=(document.getElementById('nc-address')?.value||'').trim();
    if(!name){ oToast('Jina la mteja linahitajika','error'); return; } if(!phone){ oToast('Namba ya simu inahitajika','error'); return; }
    var btn=document.getElementById('nc-save-btn'); if(btn){ btn.disabled=true; btn.innerHTML='<i class="fas fa-spinner fa-spin"></i> Inaongeza...'; }
    try{
        var res=await fetch('/wateja',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':oCsrf},body:JSON.stringify({jina:name,simu:phone,anapoishi:address})});
        var data=await res.json();
        if(data.success){ oToast('Mteja ameongezwa!','success'); oSelectedCust={id:data.data?.id||null,name:name,phone:phone}; oCustModalClose(); oSubmitOrder(oPendingSt); }
        else oToast(data.message||'Hitilafu katika kuongeza mteja','error');
    }catch(e){ oToast('Hitilafu ya mtandao: '+e.message,'error'); }
    finally{ if(btn){ btn.disabled=false; btn.innerHTML='<i class="fas fa-user-plus"></i> Ongeza na Endelea'; } }
}
async function oSubmitOrder(status){
    if(oCart.length===0) return;
    var items=oCart.map(function(i){return {id:i.id,name:i.name,price:i.price,qty:i.qty,total:i.price*i.qty};});
    var subtotal=oCart.reduce(function(s,i){return s+i.price*i.qty;},0);
    var soldItems = oCart.slice();
    var cartKey='mauzo_oCart_'+(document.querySelector('meta[name="company-id"]')?.content||'0');
    // Save pending and immediately clear UI+storage for smooth UX and to prevent return on hard refresh
    try{ localStorage.setItem(cartKey+'_pending', JSON.stringify(oCart)); localStorage.setItem(cartKey, JSON.stringify([])); }catch(e){}
    // Optimistically clear cart UI immediately
    oCart=[]; oCartRender(); oCloseCart();
    var btns=document.querySelectorAll('#o-cart-backdrop button'); btns.forEach(function(b){b.disabled=true;});
    try{
        var res=await fetch('/orders',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':oCsrf},body:JSON.stringify({items:items,subtotal:subtotal,discount:0,total:subtotal,status:status,customer_name:oSelectedCust.name,customer_phone:oSelectedCust.phone,customer_id:oSelectedCust.id})});
        var data=await res.json();
        if(data.success){
            oToast(data.message||'Order imehifadhiwa!','success');
            if(status==='paid'){
                soldItems.forEach(function(it){
                    var card=document.querySelector('#order-product-grid .product-card[data-id="'+it.id+'"]');
                    if(card){
                        var cur=parseFloat(card.dataset.stock)||0;
                        var next=Math.max(0, cur - it.qty);
                        card.dataset.stock = next;
                        if(next<=0) card.classList.add('opacity-60');
                    }
                });
                try{ if(typeof refreshFinancialSoft==='function') refreshFinancialSoft(); if(typeof refreshSalesTableSoft==='function') refreshSalesTableSoft(); }catch(e){}
            }
            try{ localStorage.removeItem(cartKey+'_pending'); localStorage.setItem(cartKey, JSON.stringify([])); }catch(e){}
            oOrdersLoad();
            var listTab=document.getElementById('order-list-tab-btn'); if(listTab) switchOrderMainTab('order-list', listTab);
        } else {
            // Restore cart on failure
            try{
                var p=localStorage.getItem(cartKey+'_pending');
                if(p){ oCart=JSON.parse(p); localStorage.setItem(cartKey, p); oCartRender(); }
                localStorage.removeItem(cartKey+'_pending');
            }catch(e){}
            oToast(data.message||'Hitilafu katika kuhifadhi order','error');
        }
    }catch(e){
        try{
            var p2=localStorage.getItem(cartKey+'_pending');
            if(p2){ oCart=JSON.parse(p2); localStorage.setItem(cartKey, p2); oCartRender(); }
        }catch(e2){}
        oToast('Hitilafu ya mtandao: '+e.message,'error');
    }
    finally{
        try{ localStorage.removeItem(cartKey+'_pending'); }catch(e){}
        btns.forEach(function(b){b.disabled=false;});
    }
}

/* ── customer modal ── */
function oCustModalOpen(){
    ['nc-name','nc-phone','nc-address'].forEach(function(id){ var el=document.getElementById(id); if(el) el.value=''; });
    var si=document.getElementById('cust-search-input'); if(si) si.value='';
    oCustSearch('');
    oCustTab('existing', document.querySelector('#modal-customer .customer-tab-btn'));
    var m=document.getElementById('modal-customer'); m.classList.remove('hidden'); m.classList.add('flex'); document.body.style.overflow='hidden';
}
function oCustModalClose(){ var m=document.getElementById('modal-customer'); m.classList.add('hidden'); m.classList.remove('flex'); document.body.style.overflow=''; }
function oCustTab(tab, btn){
    document.querySelectorAll('.customer-tab-pane').forEach(function(el){ el.classList.add('hidden'); });
    document.querySelectorAll('#modal-customer .customer-tab-btn').forEach(function(b){ b.className='customer-tab-btn flex-1 py-2.5 text-sm font-medium rounded-xl text-slate-600 hover:bg-white'; });
    var pane=document.getElementById('ctab-'+tab); if(pane) pane.classList.remove('hidden');
    if(btn) btn.className='customer-tab-btn flex-1 py-2.5 text-sm font-bold rounded-xl bg-white shadow text-emerald-700';
}
function oCustSearch(val){
    var term=(val||'').toLowerCase().trim();
    document.querySelectorAll('#cust-list .customer-item').forEach(function(el){
        var name=el.dataset.name||'', simu=el.dataset.simu||''; el.style.display=(!term||name.includes(term)||simu.includes(term))?'':'none';
    });
}
var oCustSearchDebounced = debounce(oCustSearch, 180);

/* ── orders list — paginated, fast ── */
async function oOrdersLoad(silent, page){
    page=page||oCurrentPage||1;
    var tbody=document.getElementById('orders-tbody');
    if(!silent && tbody){ tbody.innerHTML='<tr><td colspan="7" class="text-center py-10 text-slate-500"><div class="flex flex-col items-center gap-2"><span class="w-8 h-8 rounded-full border-2 border-slate-200 border-t-emerald-600 animate-spin"></span><span class="text-sm">Inapakia…</span></div></td></tr>'; }
    try{
        var url='/orders/placed?page='+page+'&per_page='+oPerPage;
        if(oFilter!=='all') url+='&status='+oFilter;
        if(oSearch) url+='&search='+encodeURIComponent(oSearch);
        var res=await fetch(url,{headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}});
        var data=await res.json();
        if(data.success){
            oCheckNewOrders(data.data||[]);
            oOrders=data.data||[]; oCurrentPage=data.pagination?.current_page||1; oLastPage=data.pagination?.last_page||1; oTotalOrders=data.pagination?.total||0; oPerPage=data.pagination?.per_page||15;
            oOrdersRender(); oOrdersStats(); oRenderPagination(); oUpdateHeaderCounters();
        }
    }catch(e){
        if(!silent && tbody) tbody.innerHTML='<tr><td colspan="7" class="text-center py-10 text-red-600 text-sm"><i class="fas fa-exclamation-circle mr-1"></i>Hitilafu katika kupakia orders</td></tr>';
    }
}
function oOrdersRender(){
    var tbody=document.getElementById('orders-tbody'); if(!tbody) return;
    if(oOrders.length===0){ tbody.innerHTML='<tr><td colspan="7" class="text-center py-10 text-slate-500"><i class="fas fa-inbox text-2xl mb-2 block text-slate-300"></i><div class="text-sm font-medium">Hakuna orders</div><div class="text-xs mt-1">Jaribu kubadilisha filter au tafuta neno lingine</div></td></tr>'; return; }
    var bClass={saved:'bg-amber-100 text-amber-700 border border-amber-200',confirmed:'bg-blue-100 text-blue-700 border border-blue-200',paid:'bg-emerald-100 text-emerald-700 border border-emerald-200',cancelled:'bg-red-100 text-red-700 border border-red-200'};
    var bLabel={saved:'Saved',confirmed:'Confirmed',paid:'Paid',cancelled:'Cancelled'};
    var bDot={saved:'#f59e0b',confirmed:'#3b82f6',paid:'#10b981',cancelled:'#ef4444'};
    // Use fragment for perf
    var frag=''; oOrders.forEach(function(o){
        var num=o.order_number||'#'+o.id, items=o.items||[], preview=items.slice(0,2).map(function(i){return i.jina||i.name;}).join(', '), more=items.length>2?' +'+(items.length-2):'';
        var date=o.created_at? new Date(o.created_at).toLocaleDateString('sw-TZ',{day:'2-digit',month:'short',hour:'2-digit',minute:'2-digit'}):'-';
        var canEdit=o.status!=='paid'&&o.status!=='cancelled';
        var editBtn=canEdit? '<button onclick="oEditStatus(\''+o.id+'\',\''+oEsc(num)+'\')" class="w-8 h-8 flex items-center justify-center text-amber-600 hover:bg-amber-50 rounded-xl transition" title="Badilisha Hali"><i class="fas fa-pen text-[11px]"></i></button>' : '';
        var creator=o.created_by_name||''; var creatorHtml=creator? '<div class="text-[10px] text-slate-400">'+oEsc(creator)+'</div>':'';
        frag+='<tr class="hover:bg-slate-50/70 transition">'
            +'<td data-label="Order" class="px-4 py-3 text-xs font-mono"><span class="bg-slate-900 text-white px-2 py-1 rounded-full font-bold text-[11px]">'+oEsc(num)+'</span></td>'
            +'<td data-label="Mteja" class="px-4 py-3"><div class="text-sm font-bold text-slate-900">'+oEsc(o.customer_name||'Walk-in')+'</div>'+(o.customer_phone?'<div class="text-xs text-slate-500">'+oEsc(o.customer_phone)+'</div>':'')+creatorHtml+'</td>'
            +'<td data-label="Bidhaa" class="px-4 py-3 text-sm text-slate-600 hidden md:table-cell"><div class="truncate max-w-[160px]">'+oEsc(preview||'—')+oEsc(more)+'</div></td>'
            +'<td data-label="Jumla" class="px-4 py-3 text-right text-sm font-extrabold text-emerald-700">'+((o.total||0).toLocaleString())+' TZS</td>'
            +'<td data-label="Hali" class="px-4 py-3"><span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold '+ (bClass[o.status]||'bg-slate-100 text-slate-700') +'"><span class="w-1.5 h-1.5 rounded-full" style="background:'+(bDot[o.status]||'#64748b')+'"></span>'+(bLabel[o.status]||o.status)+'</span></td>'
            +'<td data-label="Tarehe" class="px-4 py-3 text-xs text-slate-500 whitespace-nowrap hidden sm:table-cell">'+date+'</td>'
            +'<td data-label="Vitendo" class="px-4 py-3"><div class="flex items-center justify-center gap-1">'
            +'<button onclick="oView(\''+o.id+'\')" class="w-8 h-8 flex items-center justify-center text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl transition" title="Tazama"><i class="fas fa-eye text-[11px]"></i></button>'
            +'<button onclick="oShare(\''+o.id+'\')" class="w-8 h-8 flex items-center justify-center text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-xl transition" title="Shiriki"><i class="fas fa-share-nodes text-[11px]"></i></button>'
            +'<button onclick="oPrint(\''+o.id+'\')" class="w-8 h-8 flex items-center justify-center text-violet-700 bg-violet-50 hover:bg-violet-100 rounded-xl transition" title="Chapisha"><i class="fas fa-print text-[11px]"></i></button>'
            +editBtn
            +'<button onclick="oDeleteOpen(\''+o.id+'\')" class="w-8 h-8 flex items-center justify-center text-red-600 bg-red-50 hover:bg-red-100 rounded-xl transition" title="Futa"><i class="fas fa-trash text-[11px]"></i></button>'
            +'</div></td></tr>';
    });
    tbody.innerHTML=frag;
}
function oOrdersStats(){
    var s={total:oTotalOrders||0,saved:0,confirmed:0,paid:0,cancelled:0,unpaid:0};
    // counts for current page only for paid etc? Use fetched total for total; for others show page counts (or fetch stats endpoint optionally)
    oOrders.forEach(function(o){ if(s[o.status]!==undefined) s[o.status]++; if(o.status==='saved'||o.status==='confirmed') s.unpaid++; });
    // if server total differs, fetch stats async but don't block
    fetch('/orders/stats',{headers:{'Accept':'application/json'}}).then(function(r){return r.json();}).then(function(j){ if(j.success&&j.data){ ['total','saved','confirmed','paid','cancelled'].forEach(function(k){ var el=document.getElementById('stat-'+k); if(el) el.textContent=j.data[k]??el.textContent; var tl=document.getElementById('orders-total-label'); if(k==='total'&&tl) tl.textContent=j.data[k]; }); var unpaid=(j.data.saved||0)+(j.data.confirmed||0); var upEl=document.getElementById('stat-unpaid'); if(upEl) upEl.textContent=unpaid; }}).catch(function(){});
    // immediate page-level fallback
    ['saved','confirmed','paid','cancelled','unpaid'].forEach(function(k){ var el=document.getElementById('stat-'+k); if(el && el.textContent==='0') el.textContent=s[k]||0; });
    var totEl=document.getElementById('stat-total'); if(totEl) totEl.textContent = (oTotalOrders||s.total);
}
function oRenderPagination(){
    var c=document.getElementById('orders-pagination'); if(!c) return;
    if(oLastPage<=1){ c.innerHTML=''; return; }
    var from=((oCurrentPage-1)*oPerPage)+1, to=Math.min(oCurrentPage*oPerPage,oTotalOrders);
    var html='<div class="flex flex-wrap items-center justify-between gap-3 w-full bg-white border border-slate-200 rounded-xl px-3 py-2">';
    html+='<span class="text-xs text-slate-500 font-medium">'+from+'–'+to+' ya '+oTotalOrders+'</span><div class="flex gap-1">';
    html+='<button onclick="oOrdersLoad(false,'+(oCurrentPage-1)+')" class="px-3 py-1.5 text-xs font-semibold rounded-full border '+(oCurrentPage<=1?'opacity-40 cursor-not-allowed border-slate-200 text-slate-400':'bg-white border-slate-200 hover:bg-slate-50')+'" '+(oCurrentPage<=1?'disabled':'')+'><i class="fas fa-chevron-left mr-1"></i>Nyuma</button>';
    var max=5,s=Math.max(1,oCurrentPage-Math.floor(max/2)),e=Math.min(oLastPage,s+max-1); if(e-s<max-1) s=Math.max(1,e-max+1);
    if(s>1){ html+='<button onclick="oOrdersLoad(false,1)" class="px-3 py-1.5 text-xs rounded-full border bg-white hover:bg-slate-50">1</button>'; if(s>2) html+='<span class="px-1 text-slate-400 text-xs">…</span>'; }
    for(var i=s;i<=e;i++){ var a=i===oCurrentPage; html+='<button onclick="oOrdersLoad(false,'+i+')" class="px-3 py-1.5 text-xs rounded-full font-bold '+(a?'bg-slate-900 text-white':'bg-white border border-slate-200 hover:bg-slate-50')+'">'+i+'</button>'; }
    if(e<oLastPage){ if(e<oLastPage-1) html+='<span class="px-1 text-slate-400 text-xs">…</span>'; html+='<button onclick="oOrdersLoad(false,'+oLastPage+')" class="px-3 py-1.5 text-xs rounded-full border bg-white hover:bg-slate-50">'+oLastPage+'</button>'; }
    html+='<button onclick="oOrdersLoad(false,'+(oCurrentPage+1)+')" class="px-3 py-1.5 text-xs font-semibold rounded-full border '+(oCurrentPage>=oLastPage?'opacity-40 cursor-not-allowed border-slate-200 text-slate-400':'bg-white border-slate-200 hover:bg-slate-50')+'" '+(oCurrentPage>=oLastPage?'disabled':'')+'>Mbele<i class="fas fa-chevron-right ml-1"></i></button>';
    html+='</div></div>'; c.innerHTML=html;
}
function oOrdersFilter(filter, btn){
    oFilter=filter;
    document.querySelectorAll('.order-filter-btn').forEach(function(b){ b.className='order-filter-btn shrink-0 px-4 py-2 text-xs font-semibold rounded-full transition bg-white border border-slate-200 text-slate-700 hover:bg-slate-50'; });
    if(btn) btn.className='order-filter-btn shrink-0 px-4 py-2 text-xs font-bold rounded-full transition bg-slate-900 text-white';
    oCurrentPage=1; oOrdersLoad();
}
function oOrdersSearch(val){ oSearch=(val||'').trim(); oCurrentPage=1; oOrdersLoad(); }
var oOrdersSearchDebounced = debounce(oOrdersSearch, 280);

/* ── view ── */
function oView(id){
    var o=oOrders.find(function(x){return String(x.id)===String(id);});
    if(!o){ oToast('Order haipatikani','error'); return; }
    var bClass={saved:'bg-amber-100 text-amber-800 border border-amber-200',confirmed:'bg-blue-100 text-blue-800 border border-blue-200',paid:'bg-emerald-100 text-emerald-800 border border-emerald-200',cancelled:'bg-red-100 text-red-800 border border-red-200'};
    var bLabel={saved:'Saved',confirmed:'Confirmed',paid:'Paid',cancelled:'Cancelled'};
    var bDot={saved:'#f59e0b',confirmed:'#3b82f6',paid:'#10b981',cancelled:'#ef4444'};
    var itemsHtml=(o.items||[]).map(function(it){ var qty=it.idadi||it.qty||0, tot=it.total||(qty*(it.bei||it.price||0))||0; return '<div class="flex justify-between py-2.5 border-b border-slate-100 text-sm last:border-0"><span class="text-slate-700 font-medium">'+oEsc(it.jina||it.name)+' <span class="text-slate-400 font-normal">×'+qty+'</span></span><span class="font-extrabold text-emerald-700">'+tot.toLocaleString()+' TZS</span></div>';}).join('') || '<p class="text-sm text-slate-500 py-2">Hakuna bidhaa</p>';
    var body=document.getElementById('view-order-body');
    if(body){
        body.innerHTML='<div class="space-y-3">'
            +'<div class="bg-slate-50 rounded-2xl p-4 space-y-2 text-sm border border-slate-100">'
            +'<div class="flex justify-between"><span class="text-slate-500">Order #</span><span class="font-mono font-extrabold bg-slate-900 text-white px-2.5 py-1 rounded-full text-xs">'+oEsc(o.order_number||'#'+o.id)+'</span></div>'
            +'<div class="flex justify-between"><span class="text-slate-500">Mteja</span><span class="font-bold text-slate-900">'+oEsc(o.customer_name||'Walk-in')+'</span></div>'
            +(o.customer_phone?'<div class="flex justify-between"><span class="text-slate-500">Simu</span><span class="font-medium">'+oEsc(o.customer_phone)+'</span></div>':'')
            +'<div class="flex justify-between items-center"><span class="text-slate-500">Hali</span><span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold '+ (bClass[o.status]||'bg-slate-100') +'"><span class="w-1.5 h-1.5 rounded-full" style="background:'+(bDot[o.status]||'#64748b')+'"></span>'+(bLabel[o.status]||o.status)+'</span></div>'
            +'<div class="flex justify-between"><span class="text-slate-500">Tarehe</span><span class="text-slate-700 text-xs">'+new Date(o.created_at).toLocaleString('sw-TZ')+'</span></div>'
            +(o.created_by_name?'<div class="flex justify-between"><span class="text-slate-500">Imeundwa na</span><span class="text-slate-700 font-medium">'+oEsc(o.created_by_name)+'</span></div>':'')
            +'</div>'
            +'<div><h4 class="text-sm font-extrabold text-slate-900 mb-2">Bidhaa</h4><div class="bg-white rounded-2xl border border-slate-200 px-4">'+itemsHtml+'</div></div>'
            +'<div class="flex justify-between items-center bg-slate-900 text-white rounded-xl px-4 py-3"><span class="text-sm font-semibold">JUMLA</span><span class="text-lg font-extrabold">'+((o.total||0).toLocaleString())+' TZS</span></div>'
            +'<div class="grid grid-cols-2 gap-2 pt-1">'
            +'<button onclick="oShare(\''+o.id+'\')" class="py-2.5 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl text-sm font-bold"><i class="fas fa-share-nodes mr-1 text-emerald-600"></i>Shiriki</button>'
            +'<button onclick="oPrint(\''+o.id+'\')" class="py-2.5 bg-slate-900 hover:bg-black text-white rounded-xl text-sm font-bold"><i class="fas fa-print mr-1"></i>Chapisha</button>'
            +'</div></div>';
    }
    var m=document.getElementById('modal-view-order'); m.classList.remove('hidden'); m.classList.add('flex'); document.body.style.overflow='hidden';
}
function oViewClose(){ var m=document.getElementById('modal-view-order'); m.classList.add('hidden'); m.classList.remove('flex'); document.body.style.overflow=''; }

/* ── edit status ── */
function oEditStatus(id,num){ oCurrentId=id; document.getElementById('edit-status-num').textContent=num; var m=document.getElementById('modal-edit-status'); m.classList.remove('hidden'); m.classList.add('flex'); document.body.style.overflow='hidden'; }
function oEditStatusClose(){ var m=document.getElementById('modal-edit-status'); m.classList.add('hidden'); m.classList.remove('flex'); document.body.style.overflow=''; oCurrentId=null; }
async function oUpdateStatus(status){
    if(!oCurrentId){ oToast('Order ID haijulikani','error'); return; } var id=oCurrentId; oEditStatusClose();
    var grp=document.getElementById('status-btn-group'); if(grp) grp.querySelectorAll('button').forEach(function(b){b.disabled=true;});
    try{
        var res=await fetch('/orders/'+id+'/status',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':oCsrf,'Accept':'application/json'},body:JSON.stringify({status:status})});
        var data=await res.json();
        if(data.success){ oToast('Hali imebadilishwa: '+status,'success'); oOrdersLoad(); /* synced — no reload, stock & mauzo updated via API */ }
        else oToast(data.message||'Hitilafu katika kubadilisha hali','error');
    }catch(e){ oToast('Hitilafu ya mtandao: '+e.message,'error'); }
    finally{ if(grp) grp.querySelectorAll('button').forEach(function(b){b.disabled=false;}); }
}

/* ── delete ── */
function oDeleteOpen(id){ oCurrentId=id; var m=document.getElementById('modal-delete-order'); m.classList.remove('hidden'); m.classList.add('flex'); document.body.style.overflow='hidden'; }
function oDeleteClose(){ var m=document.getElementById('modal-delete-order'); m.classList.add('hidden'); m.classList.remove('flex'); document.body.style.overflow=''; oCurrentId=null; }
async function oConfirmDelete(){
    if(!oCurrentId) return; var id=oCurrentId; oDeleteClose();
    var btn=document.getElementById('delete-confirm-btn'); if(btn){ btn.disabled=true; btn.textContent='Inafuta...'; }
    try{
        var res=await fetch('/orders/'+id,{method:'DELETE',headers:{'X-CSRF-TOKEN':oCsrf,'Accept':'application/json','Content-Type':'application/json'}});
        var data=await res.json();
        if(data.success){ oToast('Order imefutwa!','success'); oOrdersLoad(); }
        else oToast(data.message||'Hitilafu katika kufuta order','error');
    }catch(e){ oToast('Hitilafu ya mtandao: '+e.message,'error'); }
    finally{ if(btn){ btn.disabled=false; btn.textContent='Futa'; } }
}

/* ── share ── */
function oShare(id){
    var o=oOrders.find(function(x){return String(x.id)===String(id);}); if(!o){ oToast('Order haipatikani','error'); return; }
    var text='*ORDER '+(o.order_number||'#'+o.id)+'*\nMteja: '+(o.customer_name||'Walk-in')+'\n'+(o.customer_phone?'Simu: '+o.customer_phone+'\n':'')+'Hali: '+o.status+'\n---\n';
    (o.items||[]).forEach(function(it){ var qty=it.idadi||it.qty||0, tot=it.total||(qty*(it.bei||it.price||0))||0; text+=(it.jina||it.name)+' ×'+qty+' = '+tot.toLocaleString()+' TZS\n'; });
    text+='---\nJUMLA: '+((o.total||0).toLocaleString())+' TZS';
    if(navigator.share){ navigator.share({title:'Order '+(o.order_number||'#'+o.id), text:text}).catch(function(){}); }
    else navigator.clipboard.writeText(text).then(function(){oToast('Imenakiliwa!','success');}).catch(function(){ prompt('Nakili:', text); });
}

/* ── print — IN PAGE iframe, no new window ── */
function oPrint(id){
    var url='/orders/'+id+'/print-thermal';
    var modal=document.getElementById('print-modal'), iframe=document.getElementById('print-iframe');
    if(!modal||!iframe){ window.open(url,'_blank'); return; }
    iframe.src=url;
    modal.classList.remove('hidden'); modal.classList.add('flex'); document.body.style.overflow='hidden';
    iframe.onload=function(){
        // auto focus, user can press Print
        try{ iframe.contentWindow.focus(); }catch(e){}
    };
}
function closePrintModal(){ var m=document.getElementById('print-modal'), f=document.getElementById('print-iframe'); if(m){m.classList.add('hidden'); m.classList.remove('flex');} if(f) f.src='about:blank'; document.body.style.overflow=''; }
function triggerIframePrint(){
    var iframe=document.getElementById('print-iframe');
    if(!iframe || !iframe.contentWindow){ oToast('Haijaandaliwa kuchapisha','error'); return; }
    try{ iframe.contentWindow.focus(); iframe.contentWindow.print(); }
    catch(e){ oToast('Tafadhali ruhusu print','error'); }
}
// close on backdrop
document.addEventListener('click', function(e){ var m=document.getElementById('print-modal'); if(m && e.target===m) closePrintModal(); });
document.addEventListener('keydown', function(e){ if(e.key==='Escape') closePrintModal(); });

/* ── expose + init ── */
Object.assign(window, {
    switchOrderMainTab: switchOrderMainTab,
    oProdSearch: oProdSearch, oProdSearchDebounced: oProdSearchDebounced, oProdFilter: oProdFilter, oProdPageGo: oProdPageGo,
    oAddToCart: oAddToCart, oOpenCart: oOpenCart, oCloseCart: oCloseCart, oCartBackdropClick: oCartBackdropClick, oCartQty: oCartQty, oCartRemove: oCartRemove, oClearCart: oClearCart,
    oSaveFlow: oSaveFlow, oSelectCustomer: oSelectCustomer, oSaveNewCustomer: oSaveNewCustomer, oCustModalClose: oCustModalClose, oCustTab: oCustTab, oCustSearch: oCustSearch, oCustSearchDebounced: oCustSearchDebounced,
    oOrdersLoad: oOrdersLoad, oOrdersFilter: oOrdersFilter, oOrdersSearch: oOrdersSearch, oOrdersSearchDebounced: oOrdersSearchDebounced,
    oView: oView, oViewClose: oViewClose, oEditStatus: oEditStatus, oEditStatusClose: oEditStatusClose, oUpdateStatus: oUpdateStatus,
    oDeleteOpen: oDeleteOpen, oDeleteClose: oDeleteClose, oConfirmDelete: oConfirmDelete, oShare: oShare, oPrint: oPrint,
    closePrintModal: closePrintModal, triggerIframePrint: triggerIframePrint,
    oEsc: oEsc
});

// init: restore tab, load orders, setup product pagination & cart from storage
document.addEventListener('DOMContentLoaded', function(){
    // restore last tab
    try{
        var last=localStorage.getItem('mauzo_order_tab');
        if(last==='order-place'){
            var btn=document.getElementById('order-place-tab-btn');
            if(btn) switchOrderMainTab('order-place', btn);
        }
    }catch(e){}
    // restore cart if saved
    try{
        var key='mauzo_oCart_'+(document.querySelector('meta[name="company-id"]')?.content||'0');
        var saved=JSON.parse(localStorage.getItem(key)||'null');
        if(Array.isArray(saved) && saved.length) { oCart=saved; oCartRender(); }
    }catch(e){}
    oOrdersLoad(true);
    collectProdFiltered(); applyProdPagination();
    // Esc closes modals
    document.addEventListener('keydown', function(ev){
        if(ev.key==='Escape'){
            oViewClose(); oEditStatusClose(); oDeleteClose(); oCustModalClose(); oCloseCart();
        }
    });
    // Close customer/modal on backdrop click
    ['modal-customer','modal-view-order','modal-edit-status','modal-delete-order','o-cart-backdrop'].forEach(function(id){
        var el=document.getElementById(id); if(!el) return;
        el.addEventListener('click', function(e){ if(e.target===el){ if(id==='modal-customer') oCustModalClose(); if(id==='modal-view-order') oViewClose(); if(id==='modal-edit-status') oEditStatusClose(); if(id==='modal-delete-order') oDeleteClose(); if(id==='o-cart-backdrop') oCloseCart(); }});
    });
});

})();
</script>
