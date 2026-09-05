<!-- ============================================ -->
<!-- MAIN ORDER TABS: ORDER | WEKA ORDER          -->
<!-- ============================================ -->

<!-- Tab Navigation -->
<div class="bg-gray rounded-lg shadow-sm border border-gray-200 p-1.5 mb-4">
    <div class="flex" role="tablist">
        <button id="order-list-tab-btn" 
                class="order-main-tab flex-1 py-2.5 px-4 text-sm font-medium rounded-lg transition-all duration-200 active"
                data-tab="order-list" 
                onclick="switchOrderMainTab('order-list', this)">
            <i class="fas fa-list-ul mr-2"></i>ORDER
        </button>
        <button id="order-place-tab-btn" 
                class="order-main-tab flex-1 py-2.5 px-4 text-sm font-medium rounded-lg transition-all duration-200"
                data-tab="order-place" 
                onclick="switchOrderMainTab('order-place', this)">
            <i class="fas fa-plus-circle mr-2"></i>WEKA ORDER
        </button>
    </div>
</div>

<!-- ============================================ -->
<!-- TAB 1: ORDER LIST                           -->
<!-- ============================================ -->
<div id="order-list-tab-content" class="order-main-content active">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-5">

        <!-- Header -->
        <div class="flex flex-wrap items-center justify-between mb-5 gap-3">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Orders</h2>
                <p class="text-sm text-gray-500 mt-0.5">Manage and track customer orders</p>
            </div>
            <button onclick="oOrdersLoad()" 
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                <i class="fas fa-sync-alt text-gray-500"></i>
                <span class="hidden sm:inline">Refresh</span>
            </button>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-5" id="order-stats-container">
            <div class="stat-card bg-white rounded-lg border border-gray-200 p-3 shadow-sm">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-gray-600">
                        <i class="fas fa-shopping-bag text-sm"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Jumla</p>
                        <p class="text-lg font-bold text-gray-900" id="stat-total">0</p>
                    </div>
                </div>
            </div>
            <div class="stat-card bg-white rounded-lg border border-amber-200 p-3 shadow-sm" style="border-left: 3px solid #f59e0b;">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600">
                        <i class="fas fa-clock text-sm"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-medium text-amber-600 uppercase tracking-wide">Saved</p>
                        <p class="text-lg font-bold text-amber-700" id="stat-saved">0</p>
                    </div>
                </div>
            </div>
            <div class="stat-card bg-white rounded-lg border border-blue-200 p-3 shadow-sm" style="border-left: 3px solid #3b82f6;">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                        <i class="fas fa-check-circle text-sm"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-medium text-blue-600 uppercase tracking-wide">Confirmed</p>
                        <p class="text-lg font-bold text-blue-700" id="stat-confirmed">0</p>
                    </div>
                </div>
            </div>
            <div class="stat-card bg-white rounded-lg border border-green-200 p-3 shadow-sm" style="border-left: 3px solid #22c55e;">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center text-green-600">
                        <i class="fas fa-check-double text-sm"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-medium text-green-600 uppercase tracking-wide">Paid</p>
                        <p class="text-lg font-bold text-green-700" id="stat-paid">0</p>
                    </div>
                </div>
            </div>
            <div class="stat-card bg-white rounded-lg border border-red-200 p-3 shadow-sm" style="border-left: 3px solid #ef4444;">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center text-red-600">
                        <i class="fas fa-times-circle text-sm"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-medium text-red-600 uppercase tracking-wide">Cancelled</p>
                        <p class="text-lg font-bold text-red-700" id="stat-cancelled">0</p>
                    </div>
                </div>
            </div>
            <div class="stat-card bg-white rounded-lg border border-purple-200 p-3 shadow-sm" style="border-left: 3px solid #8b5cf6;">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center text-purple-600">
                        <i class="fas fa-hourglass-half text-sm"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-medium text-purple-600 uppercase tracking-wide">Bado</p>
                        <p class="text-lg font-bold text-purple-700" id="stat-unpaid">0</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="flex flex-wrap items-center gap-2 mb-4">
            <div class="relative flex-1 min-w-[180px]">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" id="orders-search" 
                       placeholder="Tafuta order au mteja..." 
                       class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                       oninput="oOrdersSearch(this.value)">
            </div>
            <div class="flex flex-wrap gap-1.5">
                <button onclick="oOrdersFilter('all',this)" class="order-filter-btn px-3 py-1.5 text-xs font-medium rounded-lg transition bg-green-600 text-white">Zote</button>
                <button onclick="oOrdersFilter('saved',this)" class="order-filter-btn px-3 py-1.5 text-xs font-medium rounded-lg transition bg-gray-100 text-gray-700 hover:bg-gray-200">Saved</button>
                <button onclick="oOrdersFilter('confirmed',this)" class="order-filter-btn px-3 py-1.5 text-xs font-medium rounded-lg transition bg-gray-100 text-gray-700 hover:bg-gray-200">Confirmed</button>
                <button onclick="oOrdersFilter('paid',this)" class="order-filter-btn px-3 py-1.5 text-xs font-medium rounded-lg transition bg-gray-100 text-gray-700 hover:bg-gray-200">Paid</button>
                <button onclick="oOrdersFilter('cancelled',this)" class="order-filter-btn px-3 py-1.5 text-xs font-medium rounded-lg transition bg-gray-100 text-gray-700 hover:bg-gray-200">Cancelled</button>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Mteja</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Bidhaa</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Jumla</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Hali</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Imeundwa</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Vitendo</th>
                    </tr>
                </thead>
                <tbody id="orders-tbody" class="divide-y divide-gray-100">
                    <tr>
                        <td colspan="7" class="text-center py-10 text-gray-500">
                            <i class="fas fa-spinner fa-spin mr-2"></i>Inapakia orders...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div id="orders-pagination" class="mt-4 flex items-center justify-between flex-wrap gap-3">
            <!-- Pagination will be rendered here -->
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- TAB 2: WEKA ORDER                           -->
<!-- ============================================ -->
<div id="order-place-tab-content" class="order-main-content hidden">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-5">

        <!-- Header -->
        <div class="flex flex-wrap items-center justify-between mb-5 gap-3">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Weka Order</h2>
                <p class="text-sm text-gray-500 mt-0.5">Chagua bidhaa na tengeneza order mpya</p>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="oOpenCart()" 
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition shadow-sm">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="hidden sm:inline">Kikapu</span>
                    <span id="cart-badge" class="inline-flex items-center justify-center bg-white text-green-700 text-xs font-bold rounded-full min-w-[20px] h-5 px-1.5">0</span>
                </button>
                <button onclick="oClearCart()" 
                        class="inline-flex items-center gap-1.5 px-3 py-2.5 text-sm text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                        title="Futa kikapu">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        </div>

        <!-- Product Filters -->
        <div class="flex flex-wrap items-center gap-3 mb-4">
            <div class="relative flex-1 min-w-[200px]">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" id="oprod-search" 
                       placeholder="Tafuta bidhaa kwa jina, aina..." 
                       class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                       oninput="oProdSearch(this.value)">
            </div>
            <div class="flex flex-wrap gap-1.5">
                <button onclick="oProdFilter('all',this)" class="product-filter-btn px-3 py-1.5 text-xs font-medium rounded-lg transition bg-green-600 text-white">Zote</button>
                <button onclick="oProdFilter('jumla',this)" class="product-filter-btn px-3 py-1.5 text-xs font-medium rounded-lg transition bg-gray-100 text-gray-700 hover:bg-gray-200">Jumla</button>
                <button onclick="oProdFilter('low_stock',this)" class="product-filter-btn px-3 py-1.5 text-xs font-medium rounded-lg transition bg-gray-100 text-gray-700 hover:bg-gray-200">
                    <i class="fas fa-exclamation-triangle mr-1 text-amber-500"></i>Hisa Ndogo
                </button>
            </div>
        </div>

        <!-- Product Grid -->
        <div class="max-h-[55vh] overflow-y-auto pr-1 scrollbar-thin">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3" id="order-product-grid">
                @forelse($bidhaa as $product)
                    @php
                        $imgUrl = $product->image_data_url ?? null;
                        $oos    = $product->idadi <= 0;
                        $lowStock = $product->idadi > 0 && $product->idadi < 5;
                    @endphp
                    <div class="product-card rounded-xl border border-gray-200 bg-white overflow-hidden transition-all duration-200 hover:shadow-md hover:border-green-300 cursor-pointer {{ $oos ? 'opacity-60' : '' }}"
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
                        <div class="relative bg-gray-50 aspect-square flex items-center justify-center">
                            @if($imgUrl)
                                <img src="{{ $imgUrl }}" 
                                     alt="{{ $product->jina }}" 
                                     class="w-full h-full object-cover"
                                     loading="lazy"
                                     onerror="this.style.display='none';this.parentElement.querySelector('.product-placeholder').style.display='flex'">
                                <div class="product-placeholder hidden absolute inset-0 flex items-center justify-center text-gray-400">
                                    <i class="fas fa-box text-3xl"></i>
                                </div>
                            @else
                                <div class="flex items-center justify-center w-full h-full text-gray-300">
                                    <i class="fas fa-box text-4xl"></i>
                                </div>
                            @endif
                            
                            @if($oos)
                                <span class="absolute top-2 right-2 bg-red-500 text-white text-[10px] font-semibold px-2 py-0.5 rounded-lg">Imeisha</span>
                            @elseif($lowStock)
                                <span class="absolute top-2 right-2 bg-amber-500 text-white text-[10px] font-semibold px-2 py-0.5 rounded-lg">{{ number_format($product->idadi,0) }} zimebaki</span>
                            @endif
                        </div>

                        <!-- Info -->
                        <div class="p-3">
                            <div class="text-sm font-semibold text-gray-800 truncate" title="{{ $product->jina }}">
                                {{ $product->jina }}
                            </div>
                            @if($product->aina || $product->kipimo)
                                <div class="flex flex-wrap gap-1 mt-0.5">
                                    @if($product->aina)
                                        <span class="text-[10px] bg-blue-50 text-blue-700 px-1.5 py-0.5 rounded border border-blue-100">{{ $product->aina }}</span>
                                    @endif
                                    @if($product->kipimo)
                                        <span class="text-[10px] bg-green-50 text-green-700 px-1.5 py-0.5 rounded border border-green-100">{{ $product->kipimo }}</span>
                                    @endif
                                </div>
                            @endif
                            <div class="text-sm font-bold text-green-700 mt-1.5">
                                Tsh {{ number_format($product->bei_kuuza, 0) }}
                            </div>
                            @if($product->bei_uzo_jumla && $product->bei_uzo_jumla > 0)
                                <div class="text-[10px] text-gray-500">Jumla: Tsh {{ number_format($product->bei_uzo_jumla, 0) }}</div>
                            @endif
                            <div class="mt-2">
                                <button class="w-full py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-lg transition flex items-center justify-center gap-1.5 {{ $oos ? 'opacity-50 cursor-not-allowed' : '' }}">
                                    <i class="fas fa-plus-circle"></i> Ongeza
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12 text-gray-500">
                        <i class="fas fa-box-open text-4xl mb-3 block text-gray-300"></i>
                        <p class="text-sm">Hakuna bidhaa zilizopatikana</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- CART MODAL - Modern Design                   -->
<!-- ============================================ -->
<div id="o-cart-backdrop" class="fixed inset-0 bg-black/40 z-50 hidden items-center justify-center p-4" onclick="oCartBackdropClick(event)">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] flex flex-col overflow-hidden" onclick="event.stopPropagation()">
        <!-- Header -->
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center text-green-600">
                    <i class="fas fa-shopping-cart text-lg"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900">Kikapu chako</h3>
                    <p class="text-xs text-gray-500"><span id="cart-count">0</span> bidhaa</p>
                </div>
            </div>
            <button onclick="oCloseCart()" class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Items -->
        <div class="flex-1 overflow-y-auto px-4 py-3 space-y-0.5" id="cart-items-wrap">
            <div class="text-center text-gray-500 py-10">
                <i class="fas fa-shopping-cart text-4xl mb-2 block text-gray-300"></i>
                <p class="text-sm">Kikapu ni tupu</p>
                <p class="text-xs mt-1">Gusa bidhaa ili kuongeza</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="px-5 py-4 border-t border-gray-100 bg-gray-50">
            <div class="flex justify-between items-center mb-3">
                <span class="text-sm text-gray-600 font-medium">Jumla ya malipo:</span>
                <span id="cart-total" class="text-xl font-bold text-green-700">0 TZS</span>
            </div>
            <div class="flex gap-2">
                <button onclick="oSaveFlow('saved')" 
                        class="flex-1 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold rounded-xl transition flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i> Hifadhi
                </button>
                <button onclick="oSaveFlow('paid')" 
                        class="flex-1 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-xl transition flex items-center justify-center gap-2">
                    <i class="fas fa-check-circle"></i> Lipa
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- CUSTOMER MODAL                               -->
<!-- ============================================ -->
<div id="modal-customer" class="fixed inset-0 bg-black/40 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white z-10 px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-user text-green-600"></i> Chagua Mteja
            </h3>
            <button onclick="oCustModalClose()" class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="flex border-b border-gray-100 bg-gray-50 px-4">
            <button class="customer-tab-btn flex-1 py-3 text-sm font-medium text-green-700 border-b-2 border-green-600" onclick="oCustTab('existing',this)">
                <i class="fas fa-users mr-1"></i>Wateja
            </button>
            <button class="customer-tab-btn flex-1 py-3 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700" onclick="oCustTab('new',this)">
                <i class="fas fa-user-plus mr-1"></i>Mteja Mpya
            </button>
        </div>

        <div class="p-4">
            <!-- Existing Customers -->
            <div id="ctab-existing" class="customer-tab-pane">
                <button onclick="oSelectCustomer(null,'Walk-in Customer','')" 
                        class="w-full text-left px-4 py-3 rounded-xl border-2 border-gray-200 hover:border-green-300 transition flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 flex-shrink-0">
                        <i class="fas fa-user-secret"></i>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-gray-700">Walk-in Customer</div>
                        <div class="text-xs text-gray-500">Mteja asiyesajiliwa</div>
                    </div>
                </button>

                <div class="relative mb-2">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" id="cust-search-input" 
                           placeholder="Tafuta jina au simu..." 
                           class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                           oninput="oCustSearch(this.value)">
                </div>
                <div id="cust-list" class="space-y-1 max-h-52 overflow-y-auto">
                    @forelse($wateja ?? [] as $mteja)
                        <button onclick="oSelectCustomer('{{ $mteja->id }}','{{ addslashes($mteja->jina) }}','{{ $mteja->simu }}')" 
                                class="customer-item w-full text-left px-4 py-2.5 rounded-xl border border-gray-100 hover:border-green-200 hover:bg-green-50 transition flex items-center gap-3"
                                data-name="{{ strtolower($mteja->jina) }}" data-simu="{{ $mteja->simu }}">
                            <div class="w-9 h-9 rounded-full bg-green-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                                {{ strtoupper(substr($mteja->jina,0,1)) }}
                            </div>
                            <div class="min-w-0">
                                <div class="text-sm font-medium text-gray-800 truncate">{{ $mteja->jina }}</div>
                                <div class="text-xs text-gray-500">{{ $mteja->simu }}</div>
                            </div>
                        </button>
                    @empty
                        <p class="text-center text-gray-500 text-sm py-4">Hakuna wateja walioorodheshwa</p>
                    @endforelse
                </div>
            </div>

            <!-- New Customer -->
            <div id="ctab-new" class="customer-tab-pane hidden">
                <div class="space-y-3">
                    <div>
                        <label class="text-xs font-semibold text-gray-600 block mb-1">Jina la Mteja *</label>
                        <input type="text" id="nc-name" placeholder="Jina kamili..." 
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600 block mb-1">Namba ya Simu *</label>
                        <input type="tel" id="nc-phone" placeholder="0712 345 678..." 
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600 block mb-1">Anapoishi <span class="text-gray-400">(hiari)</span></label>
                        <input type="text" id="nc-address" placeholder="Mtaa, mji..." 
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                    </div>
                    <button onclick="oSaveNewCustomer()" id="nc-save-btn" 
                            class="w-full py-3 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-xl transition flex items-center justify-center gap-2">
                        <i class="fas fa-user-plus"></i> Ongeza na Endelea
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- VIEW ORDER MODAL                             -->
<!-- ============================================ -->
<div id="modal-view-order" class="fixed inset-0 bg-black/40 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white z-10 px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-eye text-blue-600"></i> Taarifa za Order
            </h3>
            <button onclick="oViewClose()" class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-5" id="view-order-body"></div>
    </div>
</div>

<!-- ============================================ -->
<!-- EDIT STATUS MODAL                            -->
<!-- ============================================ -->
<div id="modal-edit-status" class="fixed inset-0 bg-black/40 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-900">Badilisha Hali</h3>
            <button onclick="oEditStatusClose()" class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-5">
            <p class="text-sm text-gray-600 mb-4">Order: <strong id="edit-status-num" class="font-mono text-gray-800"></strong></p>
            <div class="grid grid-cols-2 gap-2" id="status-btn-group">
                <button onclick="oUpdateStatus('saved')" class="py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold rounded-xl transition">Saved</button>
                <button onclick="oUpdateStatus('confirmed')" class="py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold rounded-xl transition">Confirmed</button>
                <button onclick="oUpdateStatus('paid')" class="py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-xl transition">Paid</button>
                <button onclick="oUpdateStatus('cancelled')" class="py-2.5 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold rounded-xl transition">Cancelled</button>
            </div>
            <button onclick="oEditStatusClose()" class="mt-3 w-full py-2.5 border border-gray-200 rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition">Ghairi</button>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- DELETE ORDER MODAL                           -->
<!-- ============================================ -->
<div id="modal-delete-order" class="fixed inset-0 bg-black/40 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h3 class="font-bold text-gray-900">Futa Order</h3>
        </div>
        <div class="p-5">
            <p class="text-sm text-gray-600 mb-2">Una uhakika unataka kufuta order hii?</p>
            <p class="text-xs text-gray-400 mb-4">Hatua hii haiwezi kurejeshwa.</p>
            <div class="flex gap-2">
                <button onclick="oDeleteClose()" class="flex-1 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition">Ghairi</button>
                <button onclick="oConfirmDelete()" id="delete-confirm-btn" class="flex-1 py-2.5 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold rounded-xl transition">Futa</button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- TOAST CONTAINER                              -->
<!-- ============================================ -->
<div id="o-toast-wrap" class="fixed top-6 left-1/2 -translate-x-1/2 z-[9999] flex flex-col gap-2 items-center pointer-events-none max-w-[90%]"></div>

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

// Pagination state
var oCurrentPage = 1;
var oLastPage = 1;
var oTotalOrders = 0;
var oPerPage = 15;

/* ─────────────────────────────────────────
   MAIN TAB SWITCHING
───────────────────────────────────────── */
function switchOrderMainTab(tab, btn) {
    // Update button styles
    document.querySelectorAll('.order-main-tab').forEach(function(b) {
        b.classList.remove('active');
        b.className = 'order-main-tab flex-1 py-2.5 px-4 text-sm font-medium rounded-lg transition-all duration-200 text-gray-600 hover:bg-gray-50';
    });
    if (btn) {
        btn.className = 'order-main-tab flex-1 py-2.5 px-4 text-sm font-medium rounded-lg transition-all duration-200 bg-green-600 text-white shadow-sm';
        btn.classList.add('active');
    }

    // Hide all content
    document.querySelectorAll('.order-main-content').forEach(function(el) {
        el.classList.add('hidden');
    });

    // Show selected content
    var content = document.getElementById(tab + '-tab-content');
    if (content) {
        content.classList.remove('hidden');
        if (tab === 'order-list') {
            oOrdersLoad();
        }
    }
}

/* ─────────────────────────────────────────
   TOAST
───────────────────────────────────────── */
function oToast(msg, type) {
    type = type || 'info';
    var wrap = document.getElementById('o-toast-wrap');
    if (!wrap) return;
    var d = document.createElement('div');
    var colors = {
        success: 'bg-green-600 text-white',
        error: 'bg-red-500 text-white',
        info: 'bg-blue-600 text-white',
        warning: 'bg-amber-500 text-white'
    };
    d.className = 'px-5 py-3 rounded-xl text-sm font-medium shadow-lg pointer-events-auto transition-all duration-300 ' + (colors[type] || colors.info);
    d.textContent = msg;
    wrap.appendChild(d);
    setTimeout(function() {
        d.style.opacity = '0';
        d.style.transform = 'translateY(-12px)';
        setTimeout(function(){ d.remove(); }, 350);
    }, 2800);
}

/* ─────────────────────────────────────────
   ORDER BADGE - Unpaid count
───────────────────────────────────────── */
function oCheckNewOrders(freshOrders) {
    var unpaidCount = freshOrders.filter(function(o) {
        return o.status === 'saved' || o.status === 'confirmed';
    }).length;
    
    var badges = document.querySelectorAll('#new-order-badge-tab, #new-order-badge-internal');
    badges.forEach(function(badge) {
        if (unpaidCount > 0) {
            badge.textContent = unpaidCount;
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    });
    oKnownOrderCount = freshOrders.length;
}

/* ─────────────────────────────────────────
   PRODUCT FUNCTIONS
───────────────────────────────────────── */
function oProdSearch(val) {
    var term = (val||'').toLowerCase().trim();
    document.querySelectorAll('#order-product-grid .product-card').forEach(function(c) {
        var name = (c.dataset.name||'').toLowerCase();
        var aina = (c.dataset.aina||'').toLowerCase();
        var kip  = (c.dataset.kipimo||'').toLowerCase();
        var ms   = !term || name.includes(term) || aina.includes(term) || kip.includes(term);
        var mt   = oTypeMatch(c);
        c.style.display = (ms && mt) ? '' : 'none';
    });
}

function oTypeMatch(c) {
    if (oProdType === 'all') return true;
    var stock = parseFloat(c.dataset.stock)||0;
    var ws    = parseFloat(c.dataset.wholesale)||0;
    if (oProdType === 'jumla')     return ws > 0;
    if (oProdType === 'low_stock') return stock > 0 && stock < 10;
    return true;
}

function oProdFilter(type, btn) {
    oProdType = type;
    document.querySelectorAll('.product-filter-btn').forEach(function(b) {
        b.className = 'product-filter-btn px-3 py-1.5 text-xs font-medium rounded-lg transition bg-gray-100 text-gray-700 hover:bg-gray-200';
    });
    if (btn) {
        btn.className = 'product-filter-btn px-3 py-1.5 text-xs font-medium rounded-lg transition bg-green-600 text-white';
    }
    oProdSearch(document.getElementById('oprod-search')?.value || '');
}

/* ─────────────────────────────────────────
   ADD TO CART
───────────────────────────────────────── */
function oAddToCart(card) {
    if (card.classList.contains('opacity-60')) {
        oToast('Bidhaa hii imeisha!', 'error');
        return;
    }

    var id    = card.dataset.id;
    var name  = card.dataset.name;
    var price = parseFloat(card.dataset.price)||0;
    var ws    = parseFloat(card.dataset.wholesale)||0;
    var stock = parseFloat(card.dataset.stock)||0;
    var image = card.dataset.image||'';
    var aina  = card.dataset.aina||'';
    var kip   = card.dataset.kipimo||'';

    var useWs    = oProdType === 'jumla' && ws > 0;
    var finalPr  = useWs ? ws : price;
    var existing = oCart.find(function(i){ return i.id === id; });

    if (existing) {
        if (existing.qty >= stock) { oToast('Hisa haitoshi!','error'); return; }
        existing.qty++;
    } else {
        oCart.push({ id:id, name:name, price:finalPr, qty:1, stock:stock, image:image, aina:aina, kipimo:kip });
    }
    oCartRender();
    oToast(name + ' imeongezwa ✓', 'success');
}

/* ─────────────────────────────────────────
   CART FUNCTIONS
───────────────────────────────────────── */
function oCartRemove(idx) {
    oCart.splice(idx, 1);
    oCartRender();
}

function oCartQty(idx, delta) {
    var item = oCart[idx];
    if (!item) return;
    var nq = item.qty + delta;
    if (nq <= 0) { oCart.splice(idx, 1); }
    else if (nq > item.stock) { oToast('Hisa haitoshi!','error'); return; }
    else { item.qty = nq; }
    oCartRender();
}

function oCartRender() {
    var count = oCart.reduce(function(s,i){ return s+i.qty; }, 0);
    var total = oCart.reduce(function(s,i){ return s+(i.price*i.qty); }, 0);

    var badge = document.getElementById('cart-badge');
    if (badge) {
        badge.textContent = count;
        badge.style.display = 'inline-flex';
    }
    var elCount = document.getElementById('cart-count');
    var elTotal = document.getElementById('cart-total');
    if (elCount) elCount.textContent = count;
    if (elTotal) elTotal.textContent = total.toLocaleString() + ' TZS';

    var wrap = document.getElementById('cart-items-wrap');
    if (!wrap) return;

    if (oCart.length === 0) {
        wrap.innerHTML = '<div class="text-center text-gray-500 py-10"><i class="fas fa-shopping-cart text-4xl mb-2 block text-gray-300"></i><p class="text-sm">Kikapu ni tupu</p><p class="text-xs mt-1">Gusa bidhaa ili kuongeza</p></div>';
        return;
    }

    wrap.innerHTML = oCart.map(function(item, idx) {
        var imgHtml = item.image
            ? '<img src="' + oEsc(item.image) + '" alt="" class="w-full h-full object-cover" onerror="this.style.display=\'none\';this.parentElement.innerHTML=\'<i class=\\\"fas fa-box\\\"></i>\'">'
            : '<i class="fas fa-box text-gray-400 text-xl"></i>';
        var badges = '';
        if (item.aina) badges += '<span class="text-[10px] bg-blue-50 text-blue-700 px-1.5 py-0.5 rounded border border-blue-100">'+oEsc(item.aina)+'</span> ';
        if (item.kipimo) badges += '<span class="text-[10px] bg-green-50 text-green-700 px-1.5 py-0.5 rounded border border-green-100">'+oEsc(item.kipimo)+'</span>';
        return '<div class="flex items-center gap-3 py-2.5 border-b border-gray-50 last:border-0">'
            + '<div class="w-12 h-12 rounded-lg bg-gray-50 flex items-center justify-center flex-shrink-0 overflow-hidden">' + imgHtml + '</div>'
            + '<div class="flex-1 min-w-0">'
            +   '<div class="text-sm font-semibold text-gray-800 truncate">' + oEsc(item.name) + '</div>'
            +   '<div class="text-xs text-gray-500">' + item.price.toLocaleString() + ' TZS</div>'
            +   '<div class="mt-0.5 flex flex-wrap gap-0.5">' + badges + '</div>'
            + '</div>'
            + '<div class="flex items-center gap-1.5 flex-shrink-0">'
            +   '<button onclick="oCartQty('+idx+',-1)" class="w-7 h-7 rounded-lg border border-gray-200 hover:bg-gray-50 flex items-center justify-center text-gray-600 transition"><i class="fas fa-minus text-xs"></i></button>'
            +   '<span class="text-sm font-bold w-6 text-center">' + item.qty + '</span>'
            +   '<button onclick="oCartQty('+idx+',1)" class="w-7 h-7 rounded-lg border border-gray-200 hover:bg-gray-50 flex items-center justify-center text-gray-600 transition"><i class="fas fa-plus text-xs"></i></button>'
            + '</div>'
            + '<div class="text-sm font-bold min-w-[60px] text-right flex-shrink-0 text-green-700">'
            +   (item.price * item.qty).toLocaleString()
            + '</div>'
            + '<button onclick="oCartRemove('+idx+')" class="w-7 h-7 flex items-center justify-center text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition flex-shrink-0" title="Ondoa">'
            +   '<i class="fas fa-times"></i>'
            + '</button>'
            + '</div>';
    }).join('');
}

function oOpenCart() {
    document.getElementById('o-cart-backdrop').classList.add('flex');
    document.getElementById('o-cart-backdrop').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function oCloseCart() {
    document.getElementById('o-cart-backdrop').classList.remove('flex');
    document.getElementById('o-cart-backdrop').classList.add('hidden');
    document.body.style.overflow = '';
}

function oCartBackdropClick(e) {
    if (e.target === document.getElementById('o-cart-backdrop')) oCloseCart();
}

function oClearCart() {
    if (oCart.length === 0) { oToast('Kikapu tayari ni tupu','info'); return; }
    if (!confirm('Futa bidhaa zote kwenye kikapu?')) return;
    oCart = [];
    oCartRender();
    oCloseCart();
    oToast('Kikapu kimefutwa','info');
}

/* ─────────────────────────────────────────
   SAVE FLOW
───────────────────────────────────────── */
function oSaveFlow(status) {
    if (oCart.length === 0) { oToast('Ongeza bidhaa kwenye kikapu kwanza!','error'); return; }
    oPendingSt = status;
    oSelectedCust = { id:null, name:'Walk-in Customer', phone:'' };
    oCloseCart();
    oCustModalOpen();
}

function oSelectCustomer(id, name, phone) {
    oSelectedCust = { id: id||null, name: name||'Walk-in Customer', phone: phone||'' };
    oCustModalClose();
    oSubmitOrder(oPendingSt);
}

async function oSaveNewCustomer() {
    var name    = (document.getElementById('nc-name')?.value||'').trim();
    var phone   = (document.getElementById('nc-phone')?.value||'').trim();
    var address = (document.getElementById('nc-address')?.value||'').trim();
    if (!name)  { oToast('Jina la mteja linahitajika','error'); return; }
    if (!phone) { oToast('Namba ya simu inahitajika','error'); return; }

    var btn = document.getElementById('nc-save-btn');
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Inaongeza...'; }

    try {
        var res  = await fetch('/wateja', {
            method:'POST',
            headers:{ 'Content-Type':'application/json', 'X-CSRF-TOKEN': oCsrf },
            body: JSON.stringify({ jina:name, simu:phone, anapoishi:address })
        });
        var data = await res.json();
        if (data.success) {
            oToast('Mteja ameongezwa!','success');
            oSelectedCust = { id: data.data?.id||null, name:name, phone:phone };
            oCustModalClose();
            oSubmitOrder(oPendingSt);
        } else {
            oToast(data.message||'Hitilafu katika kuongeza mteja','error');
        }
    } catch (e) {
        oToast('Hitilafu ya mtandao: ' + e.message, 'error');
    } finally {
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-user-plus"></i> Ongeza na Endelea'; }
    }
}

async function oSubmitOrder(status) {
    if (oCart.length === 0) return;
    var items    = oCart.map(function(i){ return { id:i.id, name:i.name, price:i.price, qty:i.qty, total:i.price*i.qty }; });
    var subtotal = oCart.reduce(function(s,i){ return s + i.price*i.qty; }, 0);

    try {
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
        if (data.success) {
            oToast(data.message||'Order imehifadhiwa!','success');
            oCart = [];
            oCartRender();
            oOrdersLoad();
            // Switch to orders list view
            var listTab = document.querySelector('.order-main-tab:first-child');
            if (listTab) switchOrderMainTab('order-list', listTab);
            if (status === 'paid') {
                oToast('Ukurasa unafufuliwa...','info');
                setTimeout(function(){ window.location.reload(); }, 1800);
            }
        } else {
            oToast(data.message||'Hitilafu katika kuhifadhi order','error');
        }
    } catch (e) {
        oToast('Hitilafu ya mtandao: ' + e.message, 'error');
    }
}

/* ─────────────────────────────────────────
   CUSTOMER MODAL
───────────────────────────────────────── */
function oCustModalOpen() {
    ['nc-name','nc-phone','nc-address'].forEach(function(id) {
        var el = document.getElementById(id);
        if (el) el.value = '';
    });
    var si = document.getElementById('cust-search-input');
    if (si) si.value = '';
    oCustSearch('');
    oCustTab('existing', document.querySelector('.customer-tab-btn'));
    document.getElementById('modal-customer').classList.remove('hidden');
    document.getElementById('modal-customer').classList.add('flex');
}

function oCustModalClose() {
    document.getElementById('modal-customer').classList.add('hidden');
    document.getElementById('modal-customer').classList.remove('flex');
}

function oCustTab(tab, btn) {
    document.querySelectorAll('.customer-tab-pane').forEach(function(el) {
        el.classList.add('hidden');
    });
    document.querySelectorAll('.customer-tab-btn').forEach(function(b) {
        b.className = 'customer-tab-btn flex-1 py-3 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700';
    });
    var pane = document.getElementById('ctab-' + tab);
    if (pane) pane.classList.remove('hidden');
    if (btn) {
        btn.className = 'customer-tab-btn flex-1 py-3 text-sm font-medium text-green-700 border-b-2 border-green-600';
    }
}

function oCustSearch(val) {
    var term = (val||'').toLowerCase().trim();
    document.querySelectorAll('#cust-list .customer-item').forEach(function(el) {
        var name = el.dataset.name||'';
        var simu = el.dataset.simu||'';
        el.style.display = (!term || name.includes(term) || simu.includes(term)) ? '' : 'none';
    });
}

/* ─────────────────────────────────────────
   ORDERS LIST with Pagination
───────────────────────────────────────── */
async function oOrdersLoad(silent, page) {
    page = page || oCurrentPage || 1;
    try {
        var url = '/orders/placed?page=' + page + '&per_page=' + oPerPage;
        if (oFilter !== 'all') {
            url += '&status=' + oFilter;
        }
        if (oSearch) {
            url += '&search=' + encodeURIComponent(oSearch);
        }
        
        var res = await fetch(url, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        var data = await res.json();
        if (data.success) {
            oCheckNewOrders(data.data || []);
            oOrders = data.data || [];
            oCurrentPage = data.pagination?.current_page || 1;
            oLastPage = data.pagination?.last_page || 1;
            oTotalOrders = data.pagination?.total || 0;
            oPerPage = data.pagination?.per_page || 15;
            oOrdersRender();
            oOrdersStats();
            oRenderPagination();
        }
    } catch (e) {
        if (!silent) {
            var tbody = document.getElementById('orders-tbody');
            if (tbody) tbody.innerHTML = '<tr><td colspan="7" class="text-center py-10 text-red-600 text-sm"><i class="fas fa-exclamation-circle mr-1"></i>Hitilafu katika kupakia orders</td></tr>';
        }
    }
}

function oOrdersRender() {
    var tbody = document.getElementById('orders-tbody');
    if (!tbody) return;
    
    if (oOrders.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center py-10 text-gray-500"><i class="fas fa-inbox text-2xl mb-1 block text-gray-300"></i>Hakuna orders zilizopatikana</td></tr>';
        return;
    }
    
    var bClass = { saved: 'bg-amber-100 text-amber-700', confirmed: 'bg-blue-100 text-blue-700', paid: 'bg-green-100 text-green-700', cancelled: 'bg-red-100 text-red-700' };
    var bLabel = { saved: 'Saved', confirmed: 'Confirmed', paid: 'Paid', cancelled: 'Cancelled' };
    var bDot = { saved: '#f59e0b', confirmed: '#3b82f6', paid: '#22c55e', cancelled: '#ef4444' };

    tbody.innerHTML = oOrders.map(function(o) {
        var num     = o.order_number || '#' + o.id;
        var items   = o.items || [];
        var preview = items.slice(0,2).map(function(i){ return i.jina||i.name; }).join(', ');
        var more    = items.length > 2 ? ' +' + (items.length-2) : '';
        var date    = o.created_at ? new Date(o.created_at).toLocaleDateString('sw-TZ', {day:'2-digit',month:'short',hour:'2-digit',minute:'2-digit'}) : '-';
        var canEdit = o.status !== 'paid' && o.status !== 'cancelled';
        var editBtn = canEdit
            ? '<button onclick="oEditStatus(\''+o.id+'\',\''+oEsc(num)+'\')" class="w-8 h-8 flex items-center justify-center text-amber-600 hover:bg-amber-50 rounded-lg transition" title="Badilisha Hali"><i class="fas fa-edit text-xs"></i></button>'
            : '';
        var creator = o.created_by_name || '';
        var creatorHtml = creator ? '<div class="text-[10px] text-gray-400">' + oEsc(creator) + '</div>' : '';
        return '<tr class="border-b border-gray-50 hover:bg-gray-50 transition">'
            + '<td class="px-4 py-3 text-xs font-mono"><span class="bg-gray-100 px-2 py-0.5 rounded border border-gray-200">'+oEsc(num)+'</span></td>'
            + '<td class="px-4 py-3"><div class="text-sm font-medium text-gray-800">'+oEsc(o.customer_name||'Walk-in')+'</div>'
            +   (o.customer_phone ? '<div class="text-xs text-gray-500">'+oEsc(o.customer_phone)+'</div>' : '')
            +   creatorHtml
            + '</td>'
            + '<td class="px-4 py-3 text-sm text-gray-600 max-w-[160px]"><div class="truncate">'+oEsc(preview||'—')+more+'</div></td>'
            + '<td class="px-4 py-3 text-right text-sm font-bold text-green-700">'+((o.total||0).toLocaleString())+' TZS</td>'
            + '<td class="px-4 py-3"><span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium '+ (bClass[o.status]||'') +'"><span class="w-1.5 h-1.5 rounded-full" style="background:'+(bDot[o.status]||'#64748b')+'"></span>'+(bLabel[o.status]||o.status)+'</span></td>'
            + '<td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap">'+date+'</td>'
            + '<td class="px-4 py-3"><div class="flex items-center justify-center gap-0.5">'
            +   '<button onclick="oView(\''+o.id+'\')" class="w-8 h-8 flex items-center justify-center text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Tazama"><i class="fas fa-eye text-xs"></i></button>'
            +   '<button onclick="oShare(\''+o.id+'\')" class="w-8 h-8 flex items-center justify-center text-green-600 hover:bg-green-50 rounded-lg transition" title="Shiriki"><i class="fas fa-share-alt text-xs"></i></button>'
            +   '<button onclick="oPrint(\''+o.id+'\')" class="w-8 h-8 flex items-center justify-center text-purple-600 hover:bg-purple-50 rounded-lg transition" title="Chapisha"><i class="fas fa-print text-xs"></i></button>'
            +   editBtn
            +   '<button onclick="oDeleteOpen(\''+o.id+'\')" class="w-8 h-8 flex items-center justify-center text-red-600 hover:bg-red-50 rounded-lg transition" title="Futa"><i class="fas fa-trash-alt text-xs"></i></button>'
            + '</div></td></tr>';
    }).join('');
}

function oOrdersStats() {
    var s = { total: oTotalOrders || 0, saved: 0, confirmed: 0, paid: 0, cancelled: 0, unpaid: 0 };
    oOrders.forEach(function(o) {
        if (s[o.status] !== undefined) s[o.status]++;
        if (o.status === 'saved' || o.status === 'confirmed') s.unpaid++;
    });
    ['total','saved','confirmed','paid','cancelled','unpaid'].forEach(function(k) {
        var el = document.getElementById('stat-' + k);
        if (el) el.textContent = s[k] || 0;
    });
}

function oRenderPagination() {
    var container = document.getElementById('orders-pagination');
    if (!container) return;
    
    if (oLastPage <= 1) {
        container.innerHTML = '';
        return;
    }
    
    var from = ((oCurrentPage - 1) * oPerPage) + 1;
    var to = Math.min(oCurrentPage * oPerPage, oTotalOrders);
    
    var html = '<div class="flex flex-wrap items-center justify-between gap-3 w-full">';
    html += '<span class="text-sm text-gray-500">Onyesha ' + from + ' - ' + to + ' ya ' + oTotalOrders + ' orders</span>';
    html += '<div class="flex gap-1">';
    
    // Previous
    html += '<button onclick="oOrdersLoad(false, ' + (oCurrentPage - 1) + ')" class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 hover:bg-gray-50 transition ' + (oCurrentPage <= 1 ? 'opacity-50 cursor-not-allowed' : '') + '" ' + (oCurrentPage <= 1 ? 'disabled' : '') + '>';
    html += '<i class="fas fa-chevron-left mr-1"></i>Nyuma</button>';
    
    // Page numbers
    var maxPages = 5;
    var startPage = Math.max(1, oCurrentPage - Math.floor(maxPages / 2));
    var endPage = Math.min(oLastPage, startPage + maxPages - 1);
    if (endPage - startPage < maxPages - 1) {
        startPage = Math.max(1, endPage - maxPages + 1);
    }
    
    if (startPage > 1) {
        html += '<button onclick="oOrdersLoad(false, 1)" class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 hover:bg-gray-50 transition">1</button>';
        if (startPage > 2) {
            html += '<span class="px-2 py-1.5 text-sm text-gray-400">...</span>';
        }
    }
    
    for (var i = startPage; i <= endPage; i++) {
        var isActive = i === oCurrentPage;
        html += '<button onclick="oOrdersLoad(false, ' + i + ')" class="px-3 py-1.5 text-sm rounded-lg border ' + (isActive ? 'bg-green-600 text-white border-green-600' : 'border-gray-200 hover:bg-gray-50 transition') + '">' + i + '</button>';
    }
    
    if (endPage < oLastPage) {
        if (endPage < oLastPage - 1) {
            html += '<span class="px-2 py-1.5 text-sm text-gray-400">...</span>';
        }
        html += '<button onclick="oOrdersLoad(false, ' + oLastPage + ')" class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 hover:bg-gray-50 transition">' + oLastPage + '</button>';
    }
    
    // Next
    html += '<button onclick="oOrdersLoad(false, ' + (oCurrentPage + 1) + ')" class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 hover:bg-gray-50 transition ' + (oCurrentPage >= oLastPage ? 'opacity-50 cursor-not-allowed' : '') + '" ' + (oCurrentPage >= oLastPage ? 'disabled' : '') + '>';
    html += 'Mbele<i class="fas fa-chevron-right ml-1"></i></button>';
    
    html += '</div></div>';
    container.innerHTML = html;
}

function oOrdersFilter(filter, btn) {
    oFilter = filter;
    document.querySelectorAll('.order-filter-btn').forEach(function(b) {
        b.className = 'order-filter-btn px-3 py-1.5 text-xs font-medium rounded-lg transition bg-gray-100 text-gray-700 hover:bg-gray-200';
    });
    if (btn) {
        btn.className = 'order-filter-btn px-3 py-1.5 text-xs font-medium rounded-lg transition bg-green-600 text-white';
    }
    oCurrentPage = 1;
    oOrdersLoad();
}

function oOrdersSearch(val) {
    oSearch = (val||'').toLowerCase().trim();
    oCurrentPage = 1;
    oOrdersLoad();
}

/* ─────────────────────────────────────────
   VIEW ORDER
───────────────────────────────────────── */
function oView(id) {
    var o = oOrders.find(function(x){ return String(x.id) === String(id); });
    if (!o) { oToast('Order haipatikani','error'); return; }
    var bClass = { saved: 'bg-amber-100 text-amber-700', confirmed: 'bg-blue-100 text-blue-700', paid: 'bg-green-100 text-green-700', cancelled: 'bg-red-100 text-red-700' };
    var bLabel = { saved: 'Saved', confirmed: 'Confirmed', paid: 'Paid', cancelled: 'Cancelled' };
    var bDot = { saved: '#f59e0b', confirmed: '#3b82f6', paid: '#22c55e', cancelled: '#ef4444' };
    var itemsHtml = (o.items||[]).map(function(item) {
        var qty = item.idadi||item.qty||0;
        var tot = item.total||(qty*(item.bei||item.price||0))||0;
        return '<div class="flex justify-between py-2 border-b border-gray-50 text-sm last:border-0">'
            + '<span class="text-gray-700">'+oEsc(item.jina||item.name)+' <span class="text-gray-400">x'+qty+'</span></span>'
            + '<span class="font-semibold text-green-700">'+tot.toLocaleString()+' TZS</span>'
            + '</div>';
    }).join('') || '<p class="text-sm text-gray-500 py-2">Hakuna bidhaa</p>';

    var body = document.getElementById('view-order-body');
    if (body) {
        body.innerHTML = '<div class="space-y-3">'
            + '<div class="bg-gray-50 rounded-xl p-4 space-y-2 text-sm border border-gray-100">'
            +   '<div class="flex justify-between"><span class="text-gray-500">Order #</span><span class="font-mono font-bold text-gray-800">' + oEsc(o.order_number||'#'+o.id) + '</span></div>'
            +   '<div class="flex justify-between"><span class="text-gray-500">Mteja</span><span class="font-medium">'+oEsc(o.customer_name||'Walk-in')+'</span></div>'
            +   (o.customer_phone ? '<div class="flex justify-between"><span class="text-gray-500">Simu</span><span>'+oEsc(o.customer_phone)+'</span></div>' : '')
            +   '<div class="flex justify-between"><span class="text-gray-500">Hali</span><span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium '+ (bClass[o.status]||'') +'"><span class="w-1.5 h-1.5 rounded-full" style="background:'+(bDot[o.status]||'#64748b')+'"></span>'+(bLabel[o.status]||o.status)+'</span></div>'
            +   '<div class="flex justify-between"><span class="text-gray-500">Imeundwa</span><span class="text-gray-700">'+new Date(o.created_at).toLocaleString('sw-TZ')+'</span></div>'
            +   (o.created_by_name ? '<div class="flex justify-between"><span class="text-gray-500">Imeundwa na</span><span class="text-gray-700">'+oEsc(o.created_by_name)+'</span></div>' : '')
            + '</div>'
            + '<div><h4 class="text-sm font-semibold text-gray-700 mb-1">Bidhaa:</h4>'
            +   '<div class="bg-gray-50 rounded-xl px-4 py-2 border border-gray-100">'+itemsHtml+'</div>'
            + '</div>'
            + '<div class="flex justify-between items-center border-t border-gray-200 pt-3 font-bold"><span class="text-gray-700">JUMLA:</span><span class="text-lg text-green-700">'+((o.total||0).toLocaleString())+' TZS</span></div>'
            + '<div class="flex gap-2 pt-2">'
            +   '<button onclick="oShare(\''+o.id+'\')" class="flex-1 py-2.5 bg-green-50 hover:bg-green-100 text-green-700 rounded-xl text-sm font-medium transition"><i class="fas fa-share-alt mr-1"></i>Shiriki</button>'
            +   '<button onclick="oPrint(\''+o.id+'\')" class="flex-1 py-2.5 bg-purple-50 hover:bg-purple-100 text-purple-700 rounded-xl text-sm font-medium transition"><i class="fas fa-print mr-1"></i>Chapisha</button>'
            + '</div>'
            + '</div>';
    }
    document.getElementById('modal-view-order').classList.remove('hidden');
    document.getElementById('modal-view-order').classList.add('flex');
}

function oViewClose() {
    document.getElementById('modal-view-order').classList.add('hidden');
    document.getElementById('modal-view-order').classList.remove('flex');
}

/* ─────────────────────────────────────────
   EDIT STATUS
───────────────────────────────────────── */
function oEditStatus(id, num) {
    oCurrentId = id;
    document.getElementById('edit-status-num').textContent = num;
    document.getElementById('modal-edit-status').classList.remove('hidden');
    document.getElementById('modal-edit-status').classList.add('flex');
}

function oEditStatusClose() {
    document.getElementById('modal-edit-status').classList.add('hidden');
    document.getElementById('modal-edit-status').classList.remove('flex');
    oCurrentId = null;
}

async function oUpdateStatus(status) {
    if (!oCurrentId) { oToast('Order ID haijulikani','error'); return; }
    var id = oCurrentId;
    oEditStatusClose();

    var grp = document.getElementById('status-btn-group');
    if (grp) grp.querySelectorAll('button').forEach(function(b){ b.disabled = true; });

    try {
        var res = await fetch('/orders/' + id + '/status', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': oCsrf,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status: status })
        });
        var data = await res.json();
        if (data.success) {
            oToast('Hali imebadilishwa: ' + status, 'success');
            oOrdersLoad();
            if (status === 'paid') {
                oToast('Ukurasa unafufuliwa...','info');
                setTimeout(function(){ window.location.reload(); }, 1800);
            }
        } else {
            oToast(data.message||'Hitilafu katika kubadilisha hali','error');
        }
    } catch (e) {
        oToast('Hitilafu ya mtandao: ' + e.message, 'error');
    } finally {
        if (grp) grp.querySelectorAll('button').forEach(function(b){ b.disabled = false; });
    }
}

/* ─────────────────────────────────────────
   DELETE
───────────────────────────────────────── */
function oDeleteOpen(id) {
    oCurrentId = id;
    document.getElementById('modal-delete-order').classList.remove('hidden');
    document.getElementById('modal-delete-order').classList.add('flex');
}

function oDeleteClose() {
    document.getElementById('modal-delete-order').classList.add('hidden');
    document.getElementById('modal-delete-order').classList.remove('flex');
    oCurrentId = null;
}

async function oConfirmDelete() {
    if (!oCurrentId) return;
    var id = oCurrentId;
    oDeleteClose();
    var btn = document.getElementById('delete-confirm-btn');
    if (btn) { btn.disabled = true; btn.textContent = 'Inafuta...'; }
    try {
        var res = await fetch('/orders/' + id, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': oCsrf,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });
        var data = await res.json();
        if (data.success) {
            oToast('Order imefutwa!', 'success');
            oOrdersLoad();
        } else {
            oToast(data.message||'Hitilafu katika kufuta order','error');
        }
    } catch (e) {
        oToast('Hitilafu ya mtandao: ' + e.message, 'error');
    } finally {
        if (btn) { btn.disabled = false; btn.textContent = 'Futa'; }
    }
}

/* ─────────────────────────────────────────
   SHARE
───────────────────────────────────────── */
function oShare(id) {
    var o = oOrders.find(function(x){ return String(x.id) === String(id); });
    if (!o) { oToast('Order haipatikani','error'); return; }
    var text = '*ORDER ' + (o.order_number||'#'+o.id) + '*\n'
        + 'Mteja: ' + (o.customer_name||'Walk-in') + '\n'
        + (o.customer_phone ? 'Simu: '+o.customer_phone+'\n' : '')
        + 'Hali: ' + o.status + '\n---\n';
    (o.items||[]).forEach(function(item) {
        var qty = item.idadi||item.qty||0;
        var tot = item.total||(qty*(item.bei||item.price||0))||0;
        text += (item.jina||item.name)+' x'+qty+' = '+tot.toLocaleString()+' TZS\n';
    });
    text += '---\nJUMLA: '+((o.total||0).toLocaleString())+' TZS';
    if (navigator.share) {
        navigator.share({ title: 'Order '+(o.order_number||'#'+o.id), text: text }).catch(function(){});
    } else {
        navigator.clipboard.writeText(text)
            .then(function(){ oToast('Imenakiliwa!','success'); })
            .catch(function(){ prompt('Nakili:', text); });
    }
}

function oPrint(id) {
    var win = window.open('/orders/' + id + '/print-thermal', '_blank', 'width=420,height=650');
    if (!win) {
        oToast('Tafadhali ruhusu pop-ups', 'error');
    }
}

/* ─────────────────────────────────────────
   HELPERS
───────────────────────────────────────── */
function oEsc(str) {
    return String(str||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

/* ─────────────────────────────────────────
   EXPOSE GLOBALS
───────────────────────────────────────── */
Object.assign(window, {
    switchOrderMainTab: switchOrderMainTab,
    oProdSearch: oProdSearch,
    oProdFilter: oProdFilter,
    oAddToCart: oAddToCart,
    oOpenCart: oOpenCart,
    oCloseCart: oCloseCart,
    oCartBackdropClick: oCartBackdropClick,
    oCartQty: oCartQty,
    oCartRemove: oCartRemove,
    oClearCart: oClearCart,
    oSaveFlow: oSaveFlow,
    oSelectCustomer: oSelectCustomer,
    oSaveNewCustomer: oSaveNewCustomer,
    oCustModalClose: oCustModalClose,
    oCustTab: oCustTab,
    oCustSearch: oCustSearch,
    oOrdersLoad: oOrdersLoad,
    oOrdersFilter: oOrdersFilter,
    oOrdersSearch: oOrdersSearch,
    oView: oView,
    oViewClose: oViewClose,
    oEditStatus: oEditStatus,
    oEditStatusClose: oEditStatusClose,
    oUpdateStatus: oUpdateStatus,
    oDeleteOpen: oDeleteOpen,
    oDeleteClose: oDeleteClose,
    oConfirmDelete: oConfirmDelete,
    oShare: oShare,
    oPrint: oPrint,
});

})();
</script>