@extends('layouts.app')

@section('title', 'Mauzosheetai')

@section('page-title', 'Mauzo')
@section('page-subtitle', 'Usimamizi wa mauzo - ' . now()->format('d/m/Y'))

@section('content')
@php
    // Check user type and uwezo
    $isMfanyakazi = Auth::guard('mfanyakazi')->check();
    $isBoss = Auth::guard('web')->check();
    $hasFullAccess = true;
    
    if(Auth::guard('mfanyakazi')->check()) {
        $user = Auth::guard('mfanyakazi')->user();
        $hasFullAccess = ($user->uwezo ?? 'mdogo') === 'mkubwa';
    }
    
    // Get user role text
    $userRoleText = '';
    if(Auth::guard('mfanyakazi')->check()) {
        $user = Auth::guard('mfanyakazi')->user();
        $userRoleText = $user->uwezo === 'mkubwa' ? 'Mfanyakazi Mkubwa' : 'Mfanyakazi';
    } elseif(Auth::guard('web')->check()) {
        $user = Auth::guard('web')->user();
        $userRoleText = $user->role === 'boss' ? 'Mmiliki' : 'Msimamizi';
    }
@endphp
@php
    // Check which guard is active and get company info accordingly
    $companyId = null;
    $companyName = 'My Business';
    
    if(Auth::guard('mfanyakazi')->check()) {
        $user = Auth::guard('mfanyakazi')->user();
        $companyId = $user->company_id ?? null;
        
        if($user->company) {
            $companyName = $user->company->company_name ?? 'My Business';
        }
    } elseif(Auth::guard('web')->check()) {
        $user = Auth::guard('web')->user();
        $companyId = $user->company_id ?? null;
        
        if($user->company) {
            $companyName = $user->company->company_name ?? 'My Business';
        }
    }
@endphp

@if($companyId)
    <meta name="company-id" content="{{ $companyId }}">
    <meta name="company-name" content="{{ $companyName }}">
@else
    <meta name="company-id" content="0">
    <meta name="company-name" content="{{ $companyName }}">
@endif
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="can-backdate" content="{{ 
    (Auth::guard('web')->check()) || 
    (Auth::guard('mfanyakazi')->check() && (Auth::guard('mfanyakazi')->user()->uwezo ?? 'mdogo') === 'mkubwa') 
    ? 'true' : 'false' 
}}">

<!-- Main Container -->
<div class="space-y-4">
    <!-- Notification System -->
    <div id="notification" class="fixed top-4 lg:top-6 inset-x-0 flex justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-lg p-4 max-w-sm mx-4 border transform transition-all duration-300 scale-95">
            <div class="flex flex-col items-center text-center">
                <div id="notification-icon" class="text-3xl mb-3"></div>
                <p id="notification-message" class="text-base font-semibold text-gray-800"></p>
                <div id="notification-buttons" class="mt-4 space-x-2 hidden">
                    <button id="notification-confirm" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm">Ndio, Futa</button>
                    <button id="notification-cancel" class="px-4 py-2 bg-gray-500 text-white rounded-lg text-sm">Ghairi</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-lg p-2 mb-4">
        <div class="flex flex-wrap gap-1" id="tab-nav">
            <button id="sehemu-tab" class="tab-button pb-2 px-3 transition-colors flex items-center border-b-2 border-white text-white font-semibold whitespace-nowrap text-sm" data-tab="sehemu">
                <i class="fas fa-cash-register mr-2 text-xs"></i>Sehemu ya Mauzo
            </button>
            
            <button id="weka-order-tab" class="tab-button pb-2 px-3 transition-colors flex items-center text-green-100 hover:text-white whitespace-nowrap text-sm" data-tab="weka-order">
                <i class="fas fa-clipboard-list mr-2 text-xs"></i>Order
                <span id="new-order-badge-tab" class="ml-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">0</span>
            </button>
            
            <button id="barcode-tab" class="tab-button pb-2 px-3 transition-colors flex items-center text-green-100 hover:text-white whitespace-nowrap text-sm" data-tab="barcode">
                <i class="fas fa-barcode mr-2 text-xs"></i>Barcode
            </button>
            
            <button id="taarifa-tab" class="tab-button pb-2 px-3 transition-colors flex items-center text-green-100 hover:text-white whitespace-nowrap text-sm" data-tab="taarifa">
                <i class="fas fa-file-alt mr-2 text-xs"></i>Taarifa
            </button>
            
            <button id="jumla-tab" class="tab-button pb-2 px-3 transition-colors flex items-center text-green-100 hover:text-white whitespace-nowrap text-sm" data-tab="jumla">
                <i class="fas fa-chart-bar mr-2 text-xs"></i>Jumla
            </button>
            
            <button id="risiti-tab" class="tab-button pb-2 px-3 transition-colors flex items-center text-green-100 hover:text-white whitespace-nowrap text-sm" data-tab="risiti">
                <i class="fas fa-receipt mr-2 text-xs"></i>Risiti
            </button>
        </div>
    </div>

    <!-- TAB 1: Sehemu ya Mauzo (with integrated Kikapu) -->
    <div id="sehemu-tab-content" class="tab-content active">
        
        <!-- ============================================================ -->
        <!-- NORMAL SALES VIEW (visible by default)                        -->
        <!-- ============================================================ -->
        <div id="normal-sales-view">
            <!-- Sales Form -->
            <div class="bg-white rounded-lg shadow border border-gray-200 p-4 mb-4">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-cash-register mr-2 text-green-600"></i>
                        Rekodi Mauzo
                    </h2>
                    
                    <!-- Kikapu toggle button with badge -->
                    <button id="toggle-kikapu-view" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 text-sm transition shadow hover:shadow-md">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Kikapu</span>
                        <span id="kikapu-badge" class="bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">0</span>
                    </button>
                </div>

                <form method="POST" action="{{ route('mauzo.store') }}" class="space-y-4" id="sales-form">
                    @csrf

                    <!-- Backdate Sale Checkbox -->
                    @php
                        $canBackdate = (Auth::guard('web')->check()) || 
                                       (Auth::guard('mfanyakazi')->check() && (Auth::guard('mfanyakazi')->user()->uwezo ?? 'mdogo') === 'mkubwa');
                    @endphp

                    @if($canBackdate)
                    <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg border border-yellow-200 mb-3">
                        <div class="flex items-center">
                            <input type="checkbox" id="backdate_checkbox" name="is_backdate" value="1" class="w-5 h-5 text-yellow-600 bg-gray-100 border-gray-300 rounded focus:ring-yellow-500">
                            <label for="backdate_checkbox" class="ml-3 text-sm font-semibold text-gray-700 cursor-pointer">
                                <i class="fas fa-calendar-alt mr-1 text-yellow-600"></i> 
                                Rekodi kwa Tarehe Iliyopita
                            </label>
                        </div>
                        <div id="backdate_container" class="hidden">
                            <div class="flex items-center space-x-2">
                                <span class="text-xs text-gray-600">Chagua tarehe:</span>
                                <input type="date" name="backdate" id="backdate_date" class="border border-gray-300 rounded-lg p-1.5 text-sm focus:ring-2 focus:ring-yellow-200">
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Product Selection - Redesigned with better dropdown -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3">
                        <!-- Product Selection - 4 columns -->
                        <div class="sm:col-span-2 lg:col-span-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Bidhaa</label>
                            <div class="relative" id="product-search-container">
                                <div class="relative">
                                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                    <input type="text" id="bidhaaSearch" 
                                           placeholder="Tafuta bidhaa kwa jina, barcode, aina..." 
                                           class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-3 text-sm focus:ring-2 focus:ring-green-200 focus:border-green-500 transition"
                                           autocomplete="off">
                                </div>
                                
                                <!-- Product Dropdown - Redesigned with card-style results -->
                                <div id="product-dropdown" class="hidden absolute top-full left-0 right-0 z-20 mt-1 bg-white border border-gray-200 rounded-lg shadow-xl max-h-80 overflow-y-auto">
                                    <div id="product-list" class="divide-y divide-gray-100">
                                        @foreach($bidhaa as $item)
                                        <div class="product-item px-4 py-3 hover:bg-green-50 cursor-pointer transition duration-150" 
                                             data-id="{{ $item->id }}"
                                             data-bei-rejareja="{{ $item->bei_kuuza }}"
                                             data-bei-jumla="{{ $item->bei_uzo_jumla ?? 0 }}"
                                             data-stock="{{ $item->idadi }}"
                                             data-jina="{{ e($item->jina) }}"
                                             data-aina="{{ e($item->aina) }}"
                                             data-kipimo="{{ e($item->kipimo) }}"
                                             data-bei-nunua="{{ $item->bei_nunua }}"
                                             data-barcode="{{ $item->barcode }}">
                                            <div class="flex items-start">
                                                <div class="flex-1 min-w-0">
                                                    <div class="font-semibold text-gray-800 text-base">{{ $item->jina }}</div>
                                                    <div class="flex flex-wrap items-center gap-2 mt-1">
                                                        @if($item->aina)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-blue-100 text-blue-700">
                                                            <i class="fas fa-tag mr-1"></i>{{ $item->aina }}
                                                        </span>
                                                        @endif
                                                        @if($item->kipimo)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-green-100 text-green-700">
                                                            <i class="fas fa-ruler mr-1"></i>{{ $item->kipimo }}
                                                        </span>
                                                        @endif
                                                        @if($item->barcode)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-600">
                                                            <i class="fas fa-barcode mr-1"></i>{{ $item->barcode }}
                                                        </span>
                                                        @endif
                                                    </div>
                                                    <div class="flex flex-wrap items-center gap-3 mt-2 text-sm">
                                                        <span class="font-medium text-green-700">
                                                            Rejareja: Tsh {{ number_format($item->bei_kuuza, 0) }}
                                                        </span>
                                                        @if($item->bei_uzo_jumla && $item->bei_uzo_jumla > 0)
                                                        <span class="font-medium text-blue-700">
                                                            Jumla: Tsh {{ number_format($item->bei_uzo_jumla, 0) }}
                                                        </span>
                                                        @endif
                                                        <span class="text-gray-600">
                                                            <i class="fas fa-boxes mr-1"></i>Stock: {{ number_format($item->idadi, 2) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-2 flex-shrink-0">
                                                    <i class="fas fa-chevron-right text-gray-300"></i>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    <div id="no-products-found" class="hidden px-4 py-6 text-center text-gray-500">
                                        <i class="fas fa-search text-2xl block mb-2 text-gray-300"></i>
                                        Hakuna bidhaa iliyopatikana
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="bidhaaSelect" name="bidhaa_id" value="">
                        </div>

                        <!-- Quantity - Leave blank by default -->
                        <div class="sm:col-span-1 lg:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Idadi</label>
                            <input type="number" name="idadi" id="quantity-input" placeholder="0.00" min="0.01" step="0.01" 
                                   class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-green-200" value="">
                        </div>

                        <!-- Price Type -->
                        <div class="sm:col-span-1 lg:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Aina ya Bei</label>
                            <select id="price-type-select" name="bei_type" class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-green-200">
                                <option value="rejareja">Rejareja</option>
                                <option value="jumla">Jumla</option>
                            </select>
                        </div>

                        <!-- Price -->
                        <div class="sm:col-span-1 lg:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Bei (Tsh)</label>
                            <input type="number" name="bei" id="price-input" readonly 
                                   class="w-full bg-gray-100 border border-gray-300 rounded-lg p-2 text-sm">
                        </div>

                        <!-- Stock -->
                        <div class="sm:col-span-2 lg:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Stock</label>
                            <input type="number" id="stock-input" readonly step="0.01" 
                                   class="w-full bg-gray-100 border border-gray-300 rounded-lg p-2 text-sm">
                        </div>
                    </div>

                    <!-- Row 2: Discount Type, Discount Amount, Total, Payment Method -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3">
                        <!-- Discount Type -->
                        <div class="sm:col-span-1 lg:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Aina ya Punguzo</label>
                            <select id="punguzo-type" class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-green-200">
                                <option value="bidhaa">kwa bidhaa</option>
                                <option value="jumla">Jumla</option>
                            </select>
                        </div>

                        <!-- Discount Amount -->
                        <div class="sm:col-span-1 lg:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Punguzo (Tsh)</label>
                            <input type="number" name="punguzo" id="punguzo-input" min="0" value="0" step="0.01" 
                                   class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-green-200">
                        </div>

                        <!-- Total -->
                        <div class="sm:col-span-1 lg:col-span-3">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Jumla (Tsh)</label>
                            <input type="number" name="jumla" id="total-input" readonly 
                                   class="w-full bg-green-50 border border-green-300 rounded-lg p-2 text-sm font-bold text-gray-800">
                        </div>
                        <input type="hidden" name="punguzo_aina" id="punguzo-aina-input" value="bidhaa">

                        <!-- Payment Method -->
                        <div class="sm:col-span-2 lg:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Njia ya Malipo</label>
                            <select name="lipa_kwa" id="lipa_kwa_select" class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-green-200">
                                <option value="cash">💰 Cash</option>
                                <option value="lipa_namba">📱 Lipa Namba</option>
                                <option value="bank">🏦 Bank</option>
                            </select>
                        </div>

                        <!-- Lipa Namba Type -->
                        <div id="lipa_namba_type_container" class="sm:col-span-2 lg:col-span-2 hidden">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Aina ya Lipa Namba</label>
                            <select name="lipa_kwa_type" id="lipa_namba_type_select" class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-green-200">
                                <option value="">-- Chagua Aina --</option>
                                <option value="mpesa">📱 M-Pesa</option>
                                <option value="mixx_by_yas">🎮 Mixx by Yas</option>
                                <option value="airtel_money">📱 Airtel Money</option>
                                <option value="halopesa">📱 HaloPesa</option>
                                <option value="other">🔄 Nyingine</option>
                            </select>
                        </div>

                        <!-- Bank Type -->
                        <div id="bank_type_container" class="sm:col-span-2 lg:col-span-2 hidden">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Aina ya Benki</label>
                            <select name="lipa_kwa_type" id="bank_type_select" class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-green-200">
                                <option value="">-- Chagua Benki --</option>
                                <option value="crdb">🏦 CRDB</option>
                                <option value="nmb">🏦 NMB</option>
                                <option value="nbc">🏦 NBC</option>
                                <option value="other">🔄 Nyingine</option>
                            </select>
                        </div>

                        <!-- Customer Selection -->
                        <div class="sm:col-span-2 lg:col-span-3">
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-sm font-semibold text-gray-700">
                                    <i class="fas fa-user mr-1 text-gray-500"></i> Mteja
                                </label>
                                <div class="flex items-center">
                                    <input type="checkbox" id="send_receipt_checkbox" class="w-4 h-4 text-green-600 bg-gray-100 border-gray-300 rounded focus:ring-green-500">
                                    <label for="send_receipt_checkbox" class="ml-2 text-xs font-medium text-gray-600 cursor-pointer whitespace-nowrap">
                                        <i class="fas fa-envelope mr-1 text-green-600"></i> Tuma risiti kwa SMS
                                    </label>
                                </div>
                            </div>
                            
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400 text-sm"></i>
                                </div>
                                <input type="text" id="customer_search" 
                                       placeholder="Tafuta mteja kwa jina, au weka namba mpya..." 
                                       class="w-full border border-gray-300 rounded-lg p-2 pl-10 pr-10 text-sm focus:ring-2 focus:ring-green-200 focus:border-green-500"
                                       autocomplete="off">
                            </div>
                            
                            <select id="customer_select" size="5" 
                                    class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-green-200 hidden absolute z-10 bg-white shadow-lg max-h-60 overflow-y-auto mt-1">
                                <option value="">Tafuta au Chagua Mteja</option>
                                @foreach($wateja as $mteja)
                                    <option value="{{ $mteja->id }}" data-simu="{{ $mteja->simu }}" data-jina="{{ $mteja->jina }}">
                                        {{ $mteja->jina }} - {{ $mteja->simu }}
                                    </option>
                                @endforeach
                            </select>
                            
                            <input type="hidden" name="mteja_id" id="selected_customer_id" value="">
                            <input type="hidden" name="send_receipt" id="send_receipt_hidden" value="0">
                            <input type="hidden" name="send_to_phone" id="send_to_phone_hidden" value="">
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-2 pt-2">
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white p-3 rounded-lg font-semibold text-sm flex items-center justify-center gap-2 transition shadow hover:shadow-md">
                            <i class="fas fa-cash-register"></i>
                            Uza
                        </button>

                        <button type="button" id="kopesha-btn" class="bg-yellow-600 hover:bg-yellow-700 text-white p-3 rounded-lg font-semibold text-sm flex items-center justify-center gap-2 transition shadow hover:shadow-md">
                            <i class="fas fa-hand-holding-usd"></i>
                            Kopesha
                        </button>

                        <button type="button" id="add-to-cart-btn" class="bg-emerald-600 hover:bg-emerald-700 text-white p-3 rounded-lg font-semibold text-sm flex items-center justify-center gap-2 transition shadow hover:shadow-md">
                            <i class="fas fa-cart-plus"></i>
                            Add Kikapu
                        </button>
                        
                        <button type="button" id="show-kikapu-btn" class="bg-yellow-600 hover:bg-blue-700 text-white p-3 rounded-lg font-semibold text-sm flex items-center justify-center gap-2 transition shadow hover:shadow-md">
                            <i class="fas fa-shopping-cart"></i>
                            Show Kikapu
                            <span id="kikapu-badge-btn" class="bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">0</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Financial Overview -->
            <div class="bg-white rounded-lg shadow border border-gray-200 p-4">
                <h2 class="text-base font-bold text-gray-800 mb-3">
                    Taarifa Fupi ya Mapato na Matumizi
                </h2>

                @if($isMfanyakazi && !$hasFullAccess)
                    <div class="grid grid-cols-1 gap-3">
                        <div class="bg-gray-200 p-4 rounded-lg shadow">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="p-3 bg-white/20 rounded-lg">
                                        <i class="fas fa-money-bill-wave text-green text-lg"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-green uppercase tracking-wide mb-1">
                                            Mapato ya leo
                                        </div>
                                        @php
                                            $finTmp = $financial ?? [];
                                            $mapatoMauzoTmp = $finTmp['mauzo_leo_sum'] ?? $todaysMauzos->sum(fn($m) => $m->jumla);
                                            $mapatoMadeniTmp = $finTmp['marejesho_leo_sum'] ?? $todaysMarejeshos->sum('kiasi');
                                            $jumlaMapatoTmp = $finTmp['mapato_leo'] ?? $mapatoMauzoTmp + $mapatoMadeniTmp;
                                        @endphp
                                        <div class="text-green text-lg font-bold" id="fin-mapato-simple">
                                            {{ number_format($jumlaMapatoTmp, 2) }} Tsh
                                        </div>
                                    </div>
                                </div>
                                <div class="text-green/80 text-xs">
                                    <i class="fas fa-calendar-day mr-1"></i> {{ now()->format('d/m/Y') }}
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3" id="financial-overview">
                        <!-- Mapato -->
                        <div class="bg-gradient-to-br from-amber-500 to-amber-600 p-3 rounded-lg shadow">
                            <div class="flex justify-between items-start mb-2">
                                <div class="p-2 bg-white/20 rounded-lg">
                                    <i class="fas fa-money-bill-wave text-white"></i>
                                </div>
                            </div>
                            <div class="text-xs font-semibold text-white uppercase tracking-wide mb-1">
                                Mapato ya leo
                            </div>
                            @php
                                $finTmp = $financial ?? [];
                                $mapatoMauzo = $finTmp['mauzo_leo_sum'] ?? $todaysMauzos->sum(fn($m) => $m->jumla);
                                $mapatoMadeni = $finTmp['marejesho_leo_sum'] ?? $todaysMarejeshos->sum('kiasi');
                                $jumlaMapato = $finTmp['mapato_leo'] ?? $mapatoMauzo + $mapatoMadeni;
                            @endphp
                            <div class="space-y-1 text-white text-xs">
                                <div class="flex justify-between items-center">
                                    <span class="text-blue-100">Mauzo:</span>
                                    <span class="font-semibold" id="fin-mauzo-leo">{{ number_format($mapatoMauzo, 2) }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-blue-100">Madeni:</span>
                                    <span class="font-semibold" id="fin-marejesho-leo">{{ number_format($mapatoMadeni, 2) }}</span>
                                </div>
                                <div class="flex justify-between items-center border-t border-white/20 pt-1.5 mt-1.5">
                                    <span class="font-semibold">Jumla:</span>
                                    <span class="font-bold" id="fin-mapato-leo">{{ number_format($jumlaMapato, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Faida ya leo -->
                        <div class="bg-gradient-to-br from-green-500 to-emerald-600 p-3 rounded-lg shadow">
                            <div class="flex justify-between items-start mb-2">
                                <div class="p-2 bg-white/20 rounded-lg">
                                    <i class="fas fa-chart-line text-white"></i>
                                </div>
                            </div>
                            <div class="text-xs font-semibold text-white uppercase tracking-wide mb-1">
                                Faida ya leo
                            </div>
                            @php
                                $finTmp2 = $financial ?? [];
                                if(isset($finTmp2['faida_leo'])){
                                    $faidaMauzo = $finTmp2['faida_mauzo'] ?? 0;
                                    $faidaMarejesho = $finTmp2['faida_marejesho'] ?? 0;
                                    $jumlaFaida = $finTmp2['faida_leo'];
                                } else {
                                    $faidaMauzo = 0;
                                    foreach($todaysMauzos as $mauzo) {
                                        if ($mauzo->bidhaa) {
                                            $buyingPrice = $mauzo->bidhaa->bei_nunua ?? 0;
                                            $sellingPrice = $mauzo->bei;
                                            $quantity = $mauzo->idadi;
                                            $totalDiscount = $mauzo->punguzo_aina === 'bidhaa' ? $mauzo->punguzo * $quantity : $mauzo->punguzo;
                                            $totalRevenueBeforeDiscount = $sellingPrice * $quantity;
                                            $totalRevenueAfterDiscount = $totalRevenueBeforeDiscount - $totalDiscount;
                                            $totalBuyingCost = $buyingPrice * $quantity;
                                            $profit = $totalRevenueAfterDiscount - $totalBuyingCost;
                                            $faidaMauzo += $profit;
                                        }
                                    }
                                    $faidaMarejesho = 0;
                                    $debtProgress = [];
                                    $sortedMarejeshos = $todaysMarejeshos->sortBy('tarehe');
                                    foreach($sortedMarejeshos as $marejesho) {
                                        if(isset($marejesho->madeni) && isset($marejesho->madeni->bidhaa)) {
                                            $debt = $marejesho->madeni;
                                            $debtId = $debt->id;
                                            $repaymentAmount = $marejesho->kiasi;
                                            if (!isset($debtProgress[$debtId])) {
                                                $buyingPrice = $debt->bidhaa->bei_nunua ?? 0;
                                                $quantity = $debt->idadi;
                                                $totalCost = $buyingPrice * $quantity;
                                                $totalSellingPrice = $debt->jumla;
                                                $debtProgress[$debtId] = ['total_cost' => $totalCost,'total_selling' => $totalSellingPrice,'recovered_so_far' => 0,'is_cost_recovered' => false];
                                            }
                                            $progress = &$debtProgress[$debtId];
                                            $remainingAmount = $repaymentAmount;
                                            if (!$progress['is_cost_recovered']) {
                                                $remainingToRecover = $progress['total_cost'] - $progress['recovered_so_far'];
                                                if ($remainingAmount <= $remainingToRecover) { $progress['recovered_so_far'] += $remainingAmount; $remainingAmount = 0; } else { $costPortion = $remainingToRecover; $progress['recovered_so_far'] += $costPortion; $progress['is_cost_recovered'] = true; $profitPortion = $remainingAmount - $costPortion; $faidaMarejesho += $profitPortion; $remainingAmount = 0; }
                                            }
                                            if ($progress['is_cost_recovered'] && $remainingAmount > 0) { $faidaMarejesho += $remainingAmount; }
                                        }
                                    }
                                    $jumlaFaida = $faidaMauzo + $faidaMarejesho;
                                }
                            @endphp
                            <div class="space-y-1 text-white text-xs">
                                <div class="flex justify-between items-center">
                                    <span class="text-green-100">Mauzo:</span>
                                    <span class="font-semibold" id="fin-faida-mauzo">{{ number_format($faidaMauzo, 2) }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-green-100">Marejesho:</span>
                                    <span class="font-semibold" id="fin-faida-marejesho">{{ number_format($faidaMarejesho, 2) }}</span>
                                </div>
                                <div class="flex justify-between items-center border-t border-white/20 pt-1.5 mt-1.5">
                                    <span class="font-semibold">Jumla:</span>
                                    <span class="font-bold" id="fin-faida-jumla">{{ number_format($jumlaFaida, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Matumizi -->
                        <div class="bg-gradient-to-br from-yellow-500 to-amber-600 p-3 rounded-lg shadow">
                            <div class="flex justify-between items-start mb-2">
                                <div class="p-2 bg-white/20 rounded-lg">
                                    <i class="fas fa-receipt text-white"></i>
                                </div>
                            </div>
                            <div class="text-xs font-semibold text-white uppercase tracking-wide mb-2">Matumizi</div>
                            @php
                                $finTmp = $financial ?? [];
                                $matumiziLeo = $finTmp['matumizi_leo_sum'] ?? $todaysMatumizi->sum('gharama');
                                $matumiziJumla = $finTmp['matumizi_total'] ?? $todaysMatumizi->sum('gharama');
                            @endphp
                            <div class="text-white text-xs">
                                <div class="flex justify-between items-center mb-3">
                                    <span>Leo:</span>
                                    <span class="font-semibold" id="fin-matumizi-leo">{{ number_format($matumiziLeo, 2) }}</span>
                                </div>
                                <div class="flex justify-between items-center border-t border-white/20 pt-3">
                                    <span class="font-semibold">Jumla:</span>
                                    <span class="font-bold text-sm" id="fin-matumizi-jumla">{{ number_format($matumiziJumla, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Fedha Leo -->
                        <div class="bg-gradient-to-br from-green-500 to-emerald-600 p-3 rounded-lg shadow">
                            <div class="flex justify-between items-start mb-2">
                                <div class="p-2 bg-white/20 rounded-lg">
                                    <i class="fas fa-wallet text-white"></i>
                                </div>
                            </div>
                            <div class="text-xs font-semibold text-white uppercase tracking-wide mb-1">Fedha Leo</div>
                            @php
                                $finTmp = $financial ?? [];
                                $mauzoLeo = $finTmp['mauzo_leo_sum'] ?? $todaysMauzos->sum('jumla');
                                $mapatoMadeni = $finTmp['marejesho_leo_sum'] ?? $todaysMarejeshos->sum('kiasi');
                                $matumiziLeo = $finTmp['matumizi_leo_sum'] ?? $todaysMatumizi->sum('gharama');
                                $fedhaLeo = $finTmp['fedha_leo'] ?? ($mauzoLeo + $mapatoMadeni) - $matumiziLeo;
                            @endphp
                            <div class="space-y-1 text-white text-xs">
                                <div class="flex justify-between items-center">
                                    <span>Mapato:</span>
                                    <span class="font-semibold" id="fin-fedha-mapato">{{ number_format($mauzoLeo + $mapatoMadeni, 2) }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span>Matumizi:</span>
                                    <span class="font-semibold" id="fin-fedha-matumizi">{{ number_format($matumiziLeo, 2) }}</span>
                                </div>
                                <div class="flex justify-between items-center border-t border-white/20 pt-1.5 mt-1.5">
                                    <span class="font-semibold">Jumla:</span>
                                    <span class="font-bold" id="fin-fedha-leo">{{ number_format($fedhaLeo, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Faida Halisi -->
                        <div class="bg-gradient-to-br from-amber-500 to-orange-600 p-3 rounded-lg shadow">
                            <div class="flex justify-between items-start mb-2">
                                <div class="p-2 bg-white/20 rounded-lg">
                                    <i class="fas fa-chart-pie text-white"></i>
                                </div>
                            </div>
                            <div class="text-xs font-semibold text-white uppercase tracking-wide mb-1">Faida Halisi</div>
                            @php
                                $finTmp = $financial ?? [];
                                $matumiziLeo = $finTmp['matumizi_leo_sum'] ?? $todaysMatumizi->sum('gharama');
                                $faidaHalisi = $finTmp['faida_halisi'] ?? $jumlaFaida - $matumiziLeo;
                            @endphp
                            <div class="space-y-1 text-white text-xs">
                                <div class="flex justify-between items-center">
                                    <span>Faida:</span>
                                    <span class="font-semibold" id="fin-faida-halisi-faida">{{ number_format($jumlaFaida, 2) }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span>Matumizi:</span>
                                    <span class="font-semibold" id="fin-faida-halisi-matumizi">{{ number_format($matumiziLeo, 2) }}</span>
                                </div>
                                <div class="flex justify-between items-center border-t border-white/20 pt-1.5 mt-1.5">
                                    <span class="font-semibold">Halisi:</span>
                                    <span class="font-bold" id="fin-faida-halisi">{{ number_format($faidaHalisi, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Jumla Kuu -->
                        <div class="bg-gradient-to-br from-green-500 to-emerald-600 p-3 rounded-lg shadow">
                            <div class="flex justify-between items-start mb-2">
                                <div class="p-2 bg-white/20 rounded-lg">
                                    <i class="fas fa-chart-bar text-white"></i>
                                </div>
                            </div>
                            <div class="text-xs font-semibold text-white uppercase tracking-wide mb-1">Jumla Kuu</div>
                            @php
                                $finTmp = $financial ?? [];
                                $totalMapato = isset($finTmp['mauzo_total_sum']) ? $finTmp['mauzo_total_sum'] + ($finTmp['marejesho_total'] ?? 0) : $allTimeMauzos->sum('jumla') + $allTimeMarejeshos->sum('kiasi');
                                $totalMatumizi = $finTmp['matumizi_total'] ?? $allMatumizi->sum('gharama');
                                $jumlaKuu = $finTmp['jumla_kuu'] ?? $totalMapato - $totalMatumizi;
                            @endphp
                            <div class="space-y-1 text-white text-xs">
                                <div class="flex justify-between items-center">
                                    <span>Mapato:</span>
                                    <span class="font-semibold" id="fin-jumla-mapato">{{ number_format($totalMapato, 2) }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span>Matumizi:</span>
                                    <span class="font-semibold" id="fin-jumla-matumizi">{{ number_format($totalMatumizi, 2) }}</span>
                                </div>
                                <div class="flex justify-between items-center border-t border-white/20 pt-1.5 mt-1.5">
                                    <span class="font-semibold">Jumla:</span>
                                    <span class="font-bold" id="fin-jumla-kuu">{{ number_format($jumlaKuu, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <!-- END NORMAL SALES VIEW -->

        <!-- ============================================================ -->
        <!-- KIKAPU WORKSPACE VIEW (hidden by default)                     -->
        <!-- ============================================================ -->
        <div id="kikapu-workspace" class="hidden">
            <div class="bg-white rounded-lg shadow border border-gray-200 p-4">
                <!-- Kikapu Header -->
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="bg-blue-600 text-white p-2 rounded-lg">
                            <i class="fas fa-shopping-cart text-lg"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-800">Kikapu</h2>
                            <p class="text-xs text-gray-500" id="kikapu-active-info">Hakuna kikapu kilichochaguliwa</p>
                        </div>
                    </div>
                    <button id="close-kikapu-workspace" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg flex items-center gap-2 text-sm transition">
                        <i class="fas fa-arrow-left"></i>
                        Rudi Mauzo
                    </button>
                </div>

                <!-- Cart Switcher -->
                <div id="cart-switcher" class="flex flex-wrap gap-2 mb-4">
                    <!-- Cart tabs will be dynamically generated -->
                </div>

                <!-- Kikapu Content -->
                <div id="kikapu-content-area">
                    <!-- Empty state -->
                    <div id="kikapu-empty-state" class="text-center py-8">
                        <i class="fas fa-shopping-cart text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500 text-sm">Hakuna Kikapu</p>
                        <p class="text-gray-400 text-xs">Ongeza bidhaa kutoka Sehemu ya Mauzo</p>
                    </div>

                    <!-- Cart items -->
                    <div id="kikapu-cart-content" class="hidden">
                        <!-- Cart items will be rendered here -->
                    </div>
                </div>
            </div>
        </div>
        <!-- END KIKAPU WORKSPACE VIEW -->

    </div>
    <!-- END TAB 1: Sehemu ya Mauzo -->

    <!-- TAB 2: Barcode Sales -->
    <div id="barcode-tab-content" class="tab-content hidden">
        <div class="bg-white rounded-lg shadow border border-gray-200 p-4">
            <div class="flex items-center mb-4">
                <div class="bg-green-600 text-white p-3 rounded-full shadow">
                    <i class="fas fa-barcode"></i>
                </div>
                <h2 class="ml-3 text-lg font-bold text-gray-800">
                    Mauzo kwa Barcode
                </h2>
            </div>

            <form id="barcode-form" class="space-y-4">
                @csrf
                
                @if($canBackdate)
                <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg border border-yellow-200 mb-3">
                    <div class="flex items-center">
                        <input type="checkbox" id="barcode_backdate_checkbox" class="w-5 h-5 text-yellow-600 bg-gray-100 border-gray-300 rounded focus:ring-yellow-500">
                        <label for="barcode_backdate_checkbox" class="ml-3 text-sm font-semibold text-gray-700 cursor-pointer">
                            <i class="fas fa-calendar-alt mr-1 text-yellow-600"></i> 
                            Rekodi kwa Tarehe Iliyopita
                        </label>
                    </div>
                    <div id="barcode_backdate_container" class="hidden">
                        <div class="flex items-center space-x-2">
                            <span class="text-xs text-gray-600">Chagua tarehe:</span>
                            <input type="date" id="barcode_backdate_date" name="backdate" class="border border-gray-300 rounded-lg p-1.5 text-sm focus:ring-2 focus:ring-yellow-200">
                            <span class="text-xs text-gray-500 ml-2">
                                <i class="fas fa-info-circle"></i> Siku za nyuma tu
                            </span>
                        </div>
                    </div>
                </div>
                @endif

                <div class="mb-3">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Njia ya Malipo</label>
                    <select name="lipa_kwa" id="barcode_lipa_kwa_select" class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-green-200">
                        <option value="cash">💰 Cash</option>
                        <option value="lipa_namba">📱 Lipa Namba</option>
                        <option value="bank">🏦 Bank</option>
                    </select>
                </div>

                <div id="barcode_lipa_namba_container" class="mb-3 hidden">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Aina ya Lipa Namba</label>
                    <select name="lipa_kwa_type" id="barcode_lipa_namba_select" class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-green-200">
                        <option value="">-- Chagua Aina --</option>
                        <option value="mpesa">📱 M-Pesa</option>
                        <option value="mixx_by_yas">🎮 Mixx by Yas</option>
                        <option value="airtel_money">📱 Airtel Money</option>
                        <option value="halopesa">📱 HaloPesa</option>
                        <option value="other">🔄 Nyingine</option>
                    </select>
                </div>

                <div id="barcode_bank_container" class="mb-3 hidden">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Aina ya Benki</label>
                    <select name="lipa_kwa_type" id="barcode_bank_select" class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-green-200">
                        <option value="">-- Chagua Benki --</option>
                        <option value="crdb">🏦 CRDB</option>
                        <option value="nmb">🏦 NMB</option>
                        <option value="nbc">🏦 NBC</option>
                        <option value="other">🔄 Nyingine</option>
                    </select>
                </div>

                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="w-full border-collapse text-sm">
                        <thead class="bg-gray-100 text-gray-700 text-xs uppercase">
                            <tr>
                                <th class="border px-3 py-2 text-left">Barcode</th>
                                <th class="border px-3 py-2 text-left">Bidhaa</th>
                                <th class="border px-3 py-2 text-left">Bei</th>
                                <th class="border px-3 py-2 text-left">Idadi</th>
                                <th class="border px-3 py-2 text-left">Baki</th>
                                <th class="border px-3 py-2 text-left">Punguzo</th>
                                <th class="border px-3 py-2 text-left">Jumla</th>
                                <th class="border px-3 py-2 text-center">Futa</th>
                            </tr>
                        </thead>
                        <tbody id="barcode-tbody">
                            <tr class="barcode-row">
                                <td class="px-3 py-2">
                                    <input type="text" name="barcode[]" placeholder="Scan barcode" 
                                           class="barcode-input border border-gray-300 focus:border-green-500 focus:ring-2 focus:ring-green-200 rounded-lg p-2 w-full text-sm" 
                                           autofocus />
                                </td>
                                <td class="px-3 py-2">
                                    <input type="text" name="jina[]" readonly placeholder="Jina la Bidhaa" 
                                           class="product-name border border-gray-200 bg-gray-50 text-gray-700 rounded-lg p-2 w-full text-sm" />
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" name="bei[]" readonly placeholder="Bei" 
                                           class="product-price border border-gray-200 bg-gray-50 text-gray-700 rounded-lg p-2 w-full text-sm" />
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" name="idadi[]" min="0.01" step="0.01" value="1.00" placeholder="Idadi" 
                                           class="quantity-input border border-gray-300 focus:border-green-500 focus:ring-2 focus:ring-green-200 rounded-lg p-2 w-full text-sm" />
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" name="stock[]" readonly placeholder="Baki" step="0.01"
                                           class="stock-input border border-gray-200 bg-gray-50 text-gray-700 rounded-lg p-2 w-full text-sm" />
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" name="punguzo[]" min="0" value="0" placeholder="Punguzo" step="0.01"
                                           class="punguzo-input border border-gray-300 rounded-lg p-2 w-full text-sm" />
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" name="jumla[]" readonly placeholder="Jumla" step="0.01"
                                           class="total-input border border-gray-200 bg-gray-50 text-gray-700 rounded-lg p-2 w-full text-sm" />
                                </td>
                                <td class="px-3 py-2 text-center">
                                    <button type="button" class="remove-barcode-row text-red-500 hover:text-red-700 p-2 rounded-full transition">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex flex-wrap gap-2">
                    <button type="button" id="add-barcode-row" class="bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded-lg flex items-center transition text-sm">
                        <i class="fas fa-plus mr-2"></i>
                        <span>Ongeza Safu Mpya</span>
                    </button>
                    
                    <button type="button" id="kopesha-barcode-btn" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-2 rounded-lg flex items-center transition text-sm">
                        <i class="fas fa-hand-holding-usd mr-2"></i>
                        <span>Kopesha Bidhaa</span>
                    </button>
                    
                    <button type="button" id="clear-barcode-form" class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-2 rounded-lg flex items-center transition text-sm">
                        <i class="fas fa-times mr-2"></i>
                        <span>Futa Yote</span>
                    </button>
                </div>

                <div class="flex justify-between items-center pt-2 border-t border-gray-200">
                    <div class="text-sm font-semibold text-gray-700">
                        Jumla ya Mauzo: 
                        <span class="text-green-700 font-bold" id="barcode-total">0.00</span>
                    </div>

                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold flex items-center transition text-sm">
                        <i class="fas fa-check mr-2"></i>
                        Uza Bidhaa
                    </button>
                </div>
            </form>
        </div>
    </div>
<!-- TAB 3: Taarifa Fupi -->
<div id="taarifa-tab-content" class="tab-content hidden">
    <div class="bg-white rounded-lg shadow border border-gray-200 p-4">
        <h2 class="text-lg font-semibold mb-3 flex items-center text-gray-800">
            <i class="fas fa-file-alt mr-2 text-green-600"></i>
            Taarifa Fupi ya Mauzo
        </h2>

        <div class="grid grid-cols-1 lg:grid-cols-6 gap-3 mb-4">
            <div class="relative lg:col-span-2">
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                <input type="text" id="search-sales" placeholder="Tafuta kwa jina la bidhaa, risiti, malipo..."
                       class="pl-10 w-full border border-gray-300 rounded-lg p-2 text-sm">
            </div>

            <div class="relative">
                <i class="fas fa-calendar absolute left-3 top-3 text-gray-400"></i>
                <input type="date" id="filter-start-date"
                       class="pl-10 w-full border border-gray-300 rounded-lg p-2 text-sm">
            </div>

            <div class="relative">
                <i class="fas fa-calendar absolute left-3 top-3 text-gray-400"></i>
                <input type="date" id="filter-end-date"
                       class="pl-10 w-full border border-gray-300 rounded-lg p-2 text-sm">
            </div>

            <div>
                <button id="reset-filters"
                        class="w-full bg-gray-500 hover:bg-gray-600 text-white px-3 py-2 rounded-lg text-sm flex items-center justify-center gap-2 transition">
                    <i class="fas fa-redo-alt"></i> Safisha
                </button>
            </div>

            @unless($isMfanyakazi)
            <div>
                <a href="{{ route('daily_reports.index') }}"
                   class="w-full bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-2 rounded-lg text-sm flex items-center justify-center gap-2 transition">
                    <i class="fas fa-calendar-week"></i>
                    Ripoti kwa Siku
                </a>
            </div>
            @endunless
        </div>

        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="w-full border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border px-3 py-2 text-left text-gray-700">Tarehe & Muda</th>
                        <th class="border px-3 py-2 text-left text-gray-700">Risiti</th>
                        <th class="border px-3 py-2 text-left text-gray-700">Bidhaa</th>
                        <th class="border px-3 py-2 text-left text-gray-700">Idadi</th>
                        <th class="border px-3 py-2 text-left text-gray-700">Bei</th>
                        <th class="border px-3 py-2 text-left text-gray-700">Punguzo</th>
                        @unless($isMfanyakazi)
                        <th class="border px-3 py-2 text-left text-gray-700">Faida</th>
                        @endunless
                        <th class="border px-3 py-2 text-left text-gray-700">Malipo</th>
                        <th class="border px-3 py-2 text-left text-gray-700">Jumla</th>
                        <th class="border px-3 py-2 text-left text-gray-700">Vitendo</th>
                    </tr>
                </thead>
                <tbody id="sales-tbody">
                    @php 
                        $today = \Carbon\Carbon::today()->format('Y-m-d'); 
                        
                        function actualDiscount($sale) {
                            return $sale->punguzo_aina === 'bidhaa'
                                ? $sale->punguzo * $sale->idadi
                                : $sale->punguzo;
                        }
                    @endphp
                    
                    @forelse($mauzos as $item)
                    @php 
                        $itemDate = $item->created_at->format('Y-m-d');
                        $buyingPrice = $item->bidhaa->bei_nunua ?? 0;
                        $actualDiscount = actualDiscount($item);
                        $faida = (($item->bei - $buyingPrice) * $item->idadi) - $actualDiscount;
                        $total = $item->jumla;
                        
                        $paymentMethod = '';
                        if($item->lipa_kwa === 'cash') {
                            $paymentMethod = '<span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">💰 Cash</span>';
                        } elseif($item->lipa_kwa === 'lipa_namba') {
                            $typeLabel = match($item->lipa_kwa_type) {
                                'mpesa' => 'M-Pesa',
                                'mixx_by_yas' => 'Mixx by Yas',
                                'airtel_money' => 'Airtel Money',
                                'halopesa' => 'HaloPesa',
                                'other' => 'Nyingine',
                                default => $item->lipa_kwa_type ?? ''
                            };
                            $paymentMethod = '<span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">📱 Lipa Namba<br><small class="font-bold">' . e($typeLabel) . '</small></span>';
                        } elseif($item->lipa_kwa === 'bank') {
                            $typeLabel = match($item->lipa_kwa_type) {
                                'crdb' => 'CRDB',
                                'nmb' => 'NMB',
                                'nbc' => 'NBC',
                                'other' => 'Nyingine',
                                default => $item->lipa_kwa_type ?? ''
                            };
                            $paymentMethod = '<span class="bg-purple-100 text-purple-800 px-2 py-1 rounded text-xs">🏦 Bank<br><small class="font-bold">' . e($typeLabel) . '</small></span>';
                        } else {
                            $paymentMethod = '<span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs">' . e($item->lipa_kwa) . '</span>';
                        }
                    @endphp
                    <tr class="sales-row" data-product="{{ strtolower($item->bidhaa->jina) }}" data-date="{{ $itemDate }}" data-id="{{ $item->id }}">
                        <td class="border px-3 py-2">
                            @if($itemDate === $today)
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded font-semibold text-xs whitespace-nowrap">
                                    Leo {{ $item->created_at->format('H:i:s') }}
                                </span>
                            @else
                                <span class="whitespace-nowrap">
                                    {{ $item->created_at->format('d/m/Y H:i:s') }}
                                </span>
                            @endif
                        </td>
                        <td class="border px-3 py-2 font-mono">
                            @if($item->receipt_no)
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs copy-receipt cursor-pointer hover:bg-blue-200 transition" 
                                      data-receipt="{{ $item->receipt_no }}" 
                                      onclick="copyReceiptNumber('{{ $item->receipt_no }}')"
                                      title="Bonyeza kunakili">
                                    <i class="fas fa-copy mr-1 text-blue-600"></i>
                                    {{ substr($item->receipt_no, -8) }}
                                </span>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>
                        <td class="border px-3 py-2">
                            <div class="flex flex-col">
                                <span class="font-medium">{{ $item->bidhaa->jina }}</span>
                                <div class="flex flex-wrap gap-1 mt-1">
                                    @if($item->bidhaa->aina)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-blue-100 text-blue-700">
                                        <i class="fas fa-tag mr-1"></i>{{ $item->bidhaa->aina }}
                                    </span>
                                    @endif
                                    @if($item->bidhaa->kipimo)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-green-100 text-green-700">
                                        <i class="fas fa-ruler mr-1"></i>{{ $item->bidhaa->kipimo }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="border px-3 py-2 text-center">{{ number_format($item->idadi, 2) }}</td>
                        <td class="border px-3 py-2 text-right">{{ number_format($item->bei, 2) }}</td>
                        <td class="border px-3 py-2 text-right">{{ number_format($actualDiscount, 2) }}</td>
                        @unless($isMfanyakazi)
                        <td class="border px-3 py-2 text-right">{{ number_format($faida, 2) }}</td>
                        @endunless
                        <td class="border px-3 py-2 text-center">{!! $paymentMethod !!}</td>
                        <td class="border px-3 py-2 text-right">{{ number_format($total, 2) }}</td>
                        <td class="border px-3 py-2 text-center">
                            <div class="flex gap-1 justify-center">
                                @if($item->receipt_no)
                                <button type="button" 
                                        class="print-single-receipt bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded-lg flex items-center justify-center transition text-xs" 
                                        data-receipt-no="{{ $item->receipt_no }}"
                                        onclick="printReceipt('{{ $item->receipt_no }}')">
                                    <i class="fas fa-print mr-1"></i>
                                    Chapisha
                                </button>
                                @endif
                                
                                @if($hasFullAccess)
                                <button type="button" class="delete-sale-btn bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded-lg flex items-center justify-center transition text-xs" 
                                        data-id="{{ $item->id }}" 
                                        data-product-name="{{ $item->bidhaa->jina }}" 
                                        data-quantity="{{ $item->idadi }}">
                                    <i class="fas fa-trash mr-1"></i>
                                    Futa
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $isMfanyakazi ? '9' : '10' }}" class="text-center py-4 text-gray-500">Hakuna mauzo yaliyorekodiwa bado.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($mauzos->hasPages())
        <div class="mt-4">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="text-sm text-gray-600">
                    @php
                        $start = ($mauzos->currentPage() - 1) * $mauzos->perPage() + 1;
                        $end = min($mauzos->currentPage() * $mauzos->perPage(), $mauzos->total());
                    @endphp
                    Onyesha {{ $start }} - {{ $end }} ya {{ $mauzos->total() }} mauzo
                </div>

                <nav class="flex items-center space-x-1">
                    @if($mauzos->onFirstPage())
                        <span class="px-3 py-1 rounded-lg border text-gray-400 text-sm cursor-not-allowed">
                            <i class="fas fa-chevron-left mr-1"></i> Nyuma
                        </span>
                    @else
                        <a href="{{ $mauzos->previousPageUrl() }}" class="px-3 py-1 rounded-lg border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm transition flex items-center">
                            <i class="fas fa-chevron-left mr-1"></i> Nyuma
                        </a>
                    @endif

                    <div class="flex items-center space-x-1">
                        @foreach($mauzos->getUrlRange(1, $mauzos->lastPage()) as $page => $url)
                            @if($page == $mauzos->currentPage())
                                <span class="px-3 py-1 rounded-lg bg-green-600 text-white font-semibold text-sm">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" class="px-3 py-1 rounded-lg border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm transition">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    </div>

                    @if($mauzos->hasMorePages())
                        <a href="{{ $mauzos->nextPageUrl() }}" class="px-3 py-1 rounded-lg border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm transition flex items-center">
                            Mbele <i class="fas fa-chevron-right ml-1"></i>
                        </a>
                    @else
                        <span class="px-3 py-1 rounded-lg border text-gray-400 text-sm cursor-not-allowed">
                            Mbele <i class="fas fa-chevron-right ml-1"></i>
                        </span>
                    @endif
                </nav>
            </div>
        </div>
        @endif
    </div>
</div>

    <!-- TAB 4: Mauzo ya Jumla -->
    <div id="jumla-tab-content" class="tab-content hidden">
        <div class="bg-white rounded-lg shadow border border-gray-200 p-4">
            <h2 class="text-lg font-bold mb-3 flex items-center text-gray-800">
                <i class="fas fa-chart-bar mr-2 text-green-600"></i>
                Mauzo ya Jumla
            </h2>

            <div class="mb-3">
                <input type="text" id="search-product" placeholder="Tafuta bidhaa..." class="w-full border border-gray-300 rounded-lg p-2 text-sm">
            </div>

            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="w-full border-collapse text-sm" id="grouped-sales-table">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-3 py-2 text-left text-gray-700 text-sm">Tarehe</th>
                            <th class="border px-3 py-2 text-left text-gray-700 text-sm">Bidhaa</th>
                            <th class="border px-3 py-2 text-left text-gray-700 text-sm">Idadi</th>
                            <th class="border px-3 py-2 text-left text-gray-700 text-sm">Punguzo</th>
                            <th class="border px-3 py-2 text-left text-gray-700 text-sm">Jumla</th>
                            @unless($isMfanyakazi)
                            <th class="border px-3 py-2 text-left text-gray-700 text-sm">Faida</th>
                            @endunless
                        </tr>
                    </thead>
                    <tbody id="grouped-sales-tbody">
                        @php
                            $groupedSales = [];
                            foreach($allMauzos as $sale) {
                                $date = $sale->created_at->format('Y-m-d');
                                $product = $sale->bidhaa->jina;
                                $aina = $sale->bidhaa->aina ?? '';
                                $kipimo = $sale->bidhaa->kipimo ?? '';
                                $key = $date . '|' . $product . '|' . $aina . '|' . $kipimo;
                                
                                if (!isset($groupedSales[$key])) {
                                    $groupedSales[$key] = [
                                        'tarehe' => $date,
                                        'jina' => $product,
                                        'aina' => $aina,
                                        'kipimo' => $kipimo,
                                        'idadi' => 0,
                                        'punguzo' => 0,
                                        'jumla' => 0,
                                        'faida' => 0
                                    ];
                                }
                                
                                $groupedSales[$key]['idadi'] += $sale->idadi;
                                $saleActualDiscount = $sale->punguzo_aina === 'bidhaa'
                                    ? $sale->punguzo * $sale->idadi
                                    : $sale->punguzo;
                                $groupedSales[$key]['punguzo'] += $saleActualDiscount;
                                $groupedSales[$key]['jumla'] += $sale->jumla;
                                $buyingPrice = $sale->bidhaa->bei_nunua ?? 0;
                                $saleProfit = (($sale->bei - $buyingPrice) * $sale->idadi) - $saleActualDiscount;
                                $groupedSales[$key]['faida'] += $saleProfit;
                            }
                            
                            krsort($groupedSales);
                        @endphp
                        
                        @foreach($groupedSales as $sale)
                        <tr class="grouped-sales-row" data-product="{{ strtolower($sale['jina']) }}">
                            <td class="border px-3 py-2 text-sm">{{ $sale['tarehe'] }}</td>
                            <td class="border px-3 py-2 text-sm">
                                <div class="flex flex-col">
                                    <span class="font-medium">{{ $sale['jina'] }}</span>
                                    <div class="flex flex-wrap gap-1 mt-1">
                                        @if($sale['aina'])
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-blue-100 text-blue-700">
                                            <i class="fas fa-tag mr-1"></i>{{ $sale['aina'] }}
                                        </span>
                                        @endif
                                        @if($sale['kipimo'])
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-green-100 text-green-700">
                                            <i class="fas fa-ruler mr-1"></i>{{ $sale['kipimo'] }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="border px-3 py-2 text-center text-sm">{{ number_format($sale['idadi'], 2) }}</td>
                            <td class="border px-3 py-2 text-right text-sm">{{ number_format($sale['punguzo'], 2) }}</td>
                            <td class="border px-3 py-2 text-right text-sm">{{ number_format($sale['jumla'], 2) }}</td>
                            @unless($isMfanyakazi)
                            <td class="border px-3 py-2 text-right text-sm">{{ number_format($sale['faida'], 2) }}</td>
                            @endunless
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 5: Risiti -->
    <div id="risiti-tab-content" class="tab-content hidden">
        <div class="bg-white rounded-lg shadow border border-gray-200 p-4">
            <h2 class="text-lg font-bold mb-3 flex items-center text-gray-800">
                <i class="fas fa-receipt mr-2 text-green-600"></i>
                Chapisha Risiti
            </h2>

            <div class="mb-4">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    <input type="text" id="search-receipt-input" placeholder="Weka namba ya risiti (MS-20260110-0001)..." class="pl-10 w-full border border-gray-300 rounded-lg p-2 text-sm">
                </div>
                <div class="mt-1 text-sm text-gray-500 flex items-center">
                    <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                    Namba ya risiti inapatikana kwenye tab ya "Taarifa" - Bonyeza risiti kunakili
                </div>
            </div>

            <div id="receipt-details" class="hidden">
                <div class="bg-gray-50 rounded-lg border border-gray-200 p-3 mb-3">
                    <div class="flex justify-between items-center mb-1">
                        <span class="font-semibold text-gray-800 text-sm">Risiti No:</span>
                        <span id="receipt-no-display" class="font-mono font-bold text-green-700 text-sm"></span>
                    </div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="font-semibold text-gray-800 text-sm">Tarehe:</span>
                        <span id="receipt-date-display" class="text-sm"></span>
                    </div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="font-semibold text-gray-800 text-sm">Idadi ya Bidhaa:</span>
                        <span id="receipt-items-count" class="text-sm"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-semibold text-gray-800 text-sm">Jumla:</span>
                        <span id="receipt-total-display" class="font-bold text-sm"></span>
                    </div>
                </div>

                <div class="mb-3">
                    <h3 class="font-semibold text-gray-800 text-sm mb-2">Bidhaa:</h3>
                    <div id="receipt-items-list" class="space-y-2 max-h-40 overflow-y-auto">
                        <!-- Items will be populated here -->
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-2 justify-center mt-4">
                    <button id="print-thermal-receipt" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold flex items-center justify-center gap-2 text-sm">
                        <i class="fas fa-print"></i>
                        Chapisha Risiti
                    </button>
                    <button id="share-receipt-btn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold flex items-center justify-center gap-2 text-sm">
                        <i class="fas fa-share-alt"></i>
                        Shiriki Risiti
                    </button>
                    <button id="sms-receipt-btn" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-semibold flex items-center justify-center gap-2 text-sm">
                        <i class="fas fa-envelope"></i>
                        Tuma kwa SMS
                    </button>
                </div>
            </div>

            <div id="no-receipt-found" class="hidden text-center py-6">
                <i class="fas fa-receipt text-3xl text-gray-400 mb-3"></i>
                <p class="text-gray-600 text-sm">Hakuna risiti iliyopatikana.</p>
                <p class="text-sm text-gray-500 mt-1">Ingiza namba ya risiti ili kuona taarifa</p>
            </div>

            <div id="receipt-loading" class="hidden text-center py-6">
                <i class="fas fa-spinner fa-spin text-xl text-green-600 mb-3"></i>
                <p class="text-gray-600 text-sm">Inatafuta taarifa...</p>
            </div>
        </div>
    </div>

</div>

<!-- ============================================ -->
<!-- TAB 2: Weka Order (Order Management)         -->
<!-- ============================================ -->
<div id="weka-order-tab-content" class="tab-content hidden">
    @include('mauzo.partial-order')
</div>
<!-- ============================================================ -->
<!-- MODALS                                                         -->
<!-- ============================================================ -->

<!-- Delete Sale Modal -->
<div id="delete-sale-modal" class="modal fixed inset-0 z-50 flex items-center justify-center hidden p-2">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-lg shadow-lg w-full max-w-sm mx-2 z-50">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-800 text-center">Thibitisha Ufutaji</h3>
        </div>
        <div class="p-4">
            <div class="text-center mb-4">
                <i class="fas fa-exclamation-triangle text-yellow-500 text-2xl mb-3"></i>
                <p class="text-gray-800 text-sm mb-1" id="delete-sale-message"></p>
                <div class="bg-red-50 border-l-4 border-red-400 p-3 mt-2 hidden" id="stock-warning">
                    <div class="flex">
                        <i class="fas fa-info-circle text-red-400 mt-0.5"></i>
                        <div class="ml-3">
                            <p class="text-red-700 text-sm" id="stock-warning-text"></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex justify-center space-x-2">
                <button id="cancel-delete-sale" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm">
                    Ghairi
                </button>
                <button id="confirm-delete-sale" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm">
                    Ndio, Futa
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Kopesha Modal -->
<div id="kopesha-modal" class="modal fixed inset-0 z-50 flex items-center justify-center hidden p-2">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-lg shadow-lg w-full max-w-sm mx-2 z-50">
        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 p-3 text-white flex items-center">
            <i class="fas fa-hand-holding-usd mr-2"></i>
            <h2 class="text-base font-semibold">Kopesha Bidhaa</h2>
            <button type="button" id="close-kopesha-modal" class="ml-auto text-white hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('mauzo.store') }}" class="p-4 space-y-3" id="kopesha-form">
            @csrf
            <input type="hidden" name="bidhaa_id" id="kopesha-bidhaa-id">
            <input type="hidden" name="idadi" id="kopesha-idadi">
            <input type="hidden" name="jumla" id="kopesha-jumla">
            <input type="hidden" name="baki" id="kopesha-baki">
            <input type="hidden" name="bei" id="kopesha-bei">
            <input type="hidden" name="punguzo" id="kopesha-punguzo">
            <input type="hidden" name="punguzo_aina" id="kopesha-punguzo-aina">
            <input type="hidden" name="kopesha" value="1">
            <input type="hidden" name="bei_type" id="kopesha-bei-type" value="rejareja">

            <div>
                <label class="block text-sm font-semibold mb-1 text-gray-700">Mteja Aliyesajiliwa</label>
                <select id="kopesha-mteja-select" class="w-full border border-gray-300 rounded-lg p-2 text-sm">
                    <option value="">-- Mteja Mpya --</option>
                    @foreach($wateja as $m)
                        <option value="{{ $m->id }}" data-jina="{{ $m->jina }}" data-simu="{{ $m->simu }}" data-barua_pepe="{{ $m->barua_pepe }}" data-anapoishi="{{ $m->anapoishi }}">
                            {{ $m->jina }} - {{ $m->simu }}
                        </option>
                    @endforeach
                </select>
                <input type="hidden" name="mteja_id" id="kopesha-mteja-id" value="">
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-1">Jina la Mkopaji *</label>
                <input type="text" name="jina_mkopaji" id="kopesha-jina" class="w-full border border-gray-300 rounded-lg p-2 text-sm" required>
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-1">Namba ya Simu *</label>
                <input type="text" name="simu" id="kopesha-simu" class="w-full border border-gray-300 rounded-lg p-2 text-sm" required>
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-1">Barua Pepe</label>
                <input type="email" name="barua_pepe" id="kopesha-barua-pepe" class="w-full border border-gray-300 rounded-lg p-2 text-sm">
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-1">Anapoishi</label>
                <input type="text" name="anapoishi" id="kopesha-anapoishi" class="w-full border border-gray-300 rounded-lg p-2 text-sm">
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-1">Tarehe ya Malipo *</label>
                <input type="date" name="tarehe_malipo" class="w-full border border-gray-300 rounded-lg p-2 text-sm" required>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-gray-200">
                <button type="button" id="cancel-kopesha" class="bg-gray-400 hover:bg-gray-500 px-4 py-2 rounded-lg text-white font-semibold transition text-sm">
                    Funga
                </button>
                <button type="submit" class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded-lg text-white font-semibold flex items-center gap-1 transition text-sm">
                    <i class="fas fa-check mr-1"></i>
                    Hifadhi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Kopesha Barcode Modal -->
<div id="kopesha-barcode-modal" class="modal fixed inset-0 z-50 flex items-center justify-center hidden p-2">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-lg shadow-lg w-full max-w-sm mx-2 z-50">
        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 p-3 text-white flex items-center">
            <i class="fas fa-hand-holding-usd mr-2"></i>
            <h2 class="text-base font-semibold">Kopesha Bidhaa za Barcode</h2>
            <button type="button" id="close-kopesha-barcode-modal" class="ml-auto text-white hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="kopesha-barcode-form" action="{{ route('mauzo.store.kopesha') }}" method="POST" class="p-4 space-y-3">
            @csrf
            
            <div>
                <label class="block text-sm font-semibold mb-1 text-gray-700">Mteja Aliyesajiliwa</label>
                <select id="barcode-mteja-select" class="w-full border border-gray-300 rounded-lg p-2 text-sm">
                    <option value="">-- Mteja Mpya --</option>
                    @foreach($wateja as $m)
                        <option value="{{ $m->id }}" data-jina="{{ $m->jina }}" data-simu="{{ $m->simu }}" data-barua_pepe="{{ $m->barua_pepe }}" data-anapoishi="{{ $m->anapoishi }}">
                            {{ $m->jina }} - {{ $m->simu }}
                        </option>
                    @endforeach
                </select>
                <input type="hidden" name="mteja_id" id="barcode-kopesha-mteja-id" value="">
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-1">Jina la Mkopaji *</label>
                <input type="text" name="jina_mkopaji" id="barcode-kopesha-jina" class="w-full border border-gray-300 rounded-lg p-2 text-sm" required>
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-1">Namba ya Simu *</label>
                <input type="text" name="simu" id="barcode-kopesha-simu" class="w-full border border-gray-300 rounded-lg p-2 text-sm" required>
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-1">Barua Pepe</label>
                <input type="email" name="barua_pepe" id="barcode-kopesha-barua-pepe" class="w-full border border-gray-300 rounded-lg p-2 text-sm">
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-1">Anapoishi</label>
                <input type="text" name="anapoishi" id="barcode-kopesha-anapoishi" class="w-full border border-gray-300 rounded-lg p-2 text-sm">
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-1">Tarehe ya Malipo *</label>
                <input type="date" name="tarehe_malipo" class="w-full border border-gray-300 rounded-lg p-2 text-sm" required>
            </div>

            <input type="hidden" name="items" id="barcode-items-data">

            <div class="flex justify-end gap-2 pt-3 border-t border-gray-200">
                <button type="button" id="cancel-kopesha-barcode" class="bg-gray-400 hover:bg-gray-500 px-4 py-2 rounded-lg text-white font-semibold transition text-sm">
                    Funga
                </button>
                <button type="submit" class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded-lg text-white font-semibold flex items-center gap-1 transition text-sm">
                    <i class="fas fa-check mr-1"></i>
                    Thibitisha Kopesha
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Kikapu Kopesha Modal -->
<div id="kikapu-kopesha-modal" class="modal fixed inset-0 z-50 flex items-center justify-center hidden p-2">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-lg shadow-lg w-full max-w-sm mx-2 z-50">
        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 p-3 text-white flex items-center">
            <i class="fas fa-hand-holding-usd mr-2"></i>
            <h2 class="text-base font-semibold">Kopesha Bidhaa za Kikapu</h2>
            <button type="button" id="close-kikapu-kopesha-modal" class="ml-auto text-white hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="kikapu-kopesha-form" action="{{ route('mauzo.store.kikapu.loan') }}" method="POST" class="p-4 space-y-3">
            @csrf
            
            <div>
                <label class="block text-sm font-semibold mb-1 text-gray-700">Mteja Aliyesajiliwa</label>
                <select id="kikapu-mteja-select" class="w-full border border-gray-300 rounded-lg p-2 text-sm">
                    <option value="">-- Mteja Mpya --</option>
                    @foreach($wateja as $m)
                        <option value="{{ $m->id }}" data-jina="{{ $m->jina }}" data-simu="{{ $m->simu }}" data-barua_pepe="{{ $m->barua_pepe }}" data-anapoishi="{{ $m->anapoishi }}">
                            {{ $m->jina }} - {{ $m->simu }}
                        </option>
                    @endforeach
                </select>
                <input type="hidden" name="mteja_id" id="kikapu-kopesha-mteja-id" value="">
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-1">Jina la Mkopaji *</label>
                <input type="text" name="jina_mkopaji" id="kikapu-kopesha-jina" class="w-full border border-gray-300 rounded-lg p-2 text-sm" required>
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-1">Namba ya Simu *</label>
                <input type="text" name="simu" id="kikapu-kopesha-simu" class="w-full border border-gray-300 rounded-lg p-2 text-sm" required>
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-1">Barua Pepe</label>
                <input type="email" name="barua_pepe" id="kikapu-kopesha-barua-pepe" class="w-full border border-gray-300 rounded-lg p-2 text-sm">
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-1">Anapoishi</label>
                <input type="text" name="anapoishi" id="kikapu-kopesha-anapoishi" class="w-full border border-gray-300 rounded-lg p-2 text-sm">
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-1">Tarehe ya Malipo *</label>
                <input type="date" name="tarehe_malipo" class="w-full border border-gray-300 rounded-lg p-2 text-sm" required>
            </div>

            <input type="hidden" name="items" id="kikapu-items-data">

            <div class="flex justify-end gap-2 pt-3 border-t border-gray-200">
                <button type="button" id="cancel-kikapu-kopesha" class="bg-gray-400 hover:bg-gray-500 px-4 py-2 rounded-lg text-white font-semibold transition text-sm">
                    Funga
                </button>
                <button type="submit" class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded-lg text-white font-semibold flex items-center gap-1 transition text-sm">
                    <i class="fas fa-check mr-1"></i>
                    Thibitisha Kopesha
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Confirm Sale Modal -->
<div id="confirm-sale-modal" class="modal fixed inset-0 z-50 flex items-center justify-center hidden p-2">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-lg shadow-lg w-full max-w-md mx-2 z-50">
        <div class="bg-gradient-to-r from-green-600 to-green-700 p-3 text-white flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            <h2 class="text-base font-semibold">Thibitisha Mauzo</h2>
            <button type="button" id="close-confirm-sale-modal" class="ml-auto text-white hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="p-4 space-y-3">
            <div id="confirm-sale-details">
                <!-- Dynamic content -->
            </div>
            <div class="flex justify-end gap-2 pt-3 border-t border-gray-200">
                <button type="button" id="cancel-confirm-sale" class="bg-gray-400 hover:bg-gray-500 px-4 py-2 rounded-lg text-white font-semibold transition text-sm">
                    Ghairi
                </button>
                <button type="button" id="proceed-confirm-sale" class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded-lg text-white font-semibold flex items-center gap-1 transition text-sm">
                    <i class="fas fa-check mr-1"></i>
                    Thibitisha
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Double Sale Warning Modal -->
<div id="double-sale-modal" class="modal fixed inset-0 z-50 flex items-center justify-center hidden p-2">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-lg shadow-lg w-full max-w-sm mx-2 z-50">
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 p-3 text-white flex items-center">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <h2 class="text-base font-semibold">Onana tena</h2>
            <button type="button" id="close-double-sale-modal" class="ml-auto text-white hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="p-4 space-y-3 text-center">
            <div class="flex items-center justify-center text-orange-600 mb-2">
                <i class="fas fa-exclamation-circle text-2xl mr-2"></i>
            </div>
            <p class="text-sm text-gray-800 mb-3">
                Unataka kuuza tena "<span id="double-sale-product-name" class="font-semibold"></span>"?
            </p>
            <div class="flex justify-center gap-3 pt-3">
                <button type="button" id="cancel-double-sale" class="bg-gray-400 hover:bg-gray-500 px-4 py-2 rounded-lg text-white font-semibold transition text-sm">
                    Ghairi
                </button>
                <button type="button" id="confirm-double-sale" class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded-lg text-white font-semibold flex items-center gap-1 transition text-sm">
                    <i class="fas fa-check mr-1"></i>
                    Uza
                </button>
            </div>
        </div>
    </div>
</div>

<!-- SMS Receipt Modal -->
<div id="sms-receipt-modal" class="modal fixed inset-0 z-50 flex items-center justify-center hidden p-2">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-lg shadow-lg w-full max-w-sm mx-2 z-50">
        <div class="bg-gradient-to-r from-purple-600 to-purple-700 p-3 text-white flex items-center">
            <i class="fas fa-envelope mr-2"></i>
            <h2 class="text-base font-semibold">Tuma Risiti kwa SMS</h2>
            <button type="button" id="close-sms-receipt-modal" class="ml-auto text-white hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="p-4 space-y-3">
            <div class="mb-3">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Namba ya Simu ya Mpokeaji
                </label>
                <input type="tel" id="sms-receipt-phone" 
                       class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                       placeholder="Mfano: 255712345678"
                       autocomplete="off">
                <p class="text-xs text-gray-500 mt-1">
                    <i class="fas fa-info-circle mr-1"></i>
                    Tumia muundo: 255XXXXXXXXX (Anza na 255)
                </p>
            </div>
            
            <div class="bg-gray-50 rounded-lg p-3 max-h-40 overflow-y-auto">
                <p class="text-xs text-gray-600 font-medium mb-1">Muhtasari wa Risiti:</p>
                <p id="sms-receipt-preview" class="text-xs text-gray-500 whitespace-pre-line"></p>
            </div>
            
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" id="cancel-sms-receipt" 
                        class="px-4 py-2 bg-gray-400 hover:bg-gray-500 text-white rounded-lg text-sm font-semibold transition">
                    Ghairi
                </button>
                <button type="button" id="confirm-send-sms" 
                        class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm font-semibold flex items-center gap-1 transition">
                    <i class="fas fa-paper-plane mr-1"></i>
                    Tuma SMS
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Remove from Cart Confirmation Modal -->
<div id="remove-cart-item-modal" class="modal fixed inset-0 z-50 flex items-center justify-center hidden p-2">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-lg shadow-lg w-full max-w-sm mx-2 z-50">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-800 text-center">Ondoa Bidhaa</h3>
        </div>
        <div class="p-4">
            <div class="text-center mb-4">
                <i class="fas fa-exclamation-triangle text-yellow-500 text-2xl mb-3"></i>
                <p class="text-gray-800 text-sm">Unahakika unataka kuondoa <span id="remove-item-name" class="font-semibold"></span> kwenye kikapu?</p>
            </div>
            <div class="flex justify-center space-x-2">
                <button id="cancel-remove-item" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm">
                    Ghairi
                </button>
                <button id="confirm-remove-item" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm">
                    Ndio, Ondoa
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Clear Cart Confirmation Modal -->
<div id="clear-cart-modal" class="modal fixed inset-0 z-50 flex items-center justify-center hidden p-2">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-lg shadow-lg w-full max-w-sm mx-2 z-50">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-800 text-center">Futa Kikapu</h3>
        </div>
        <div class="p-4">
            <div class="text-center mb-4">
                <i class="fas fa-exclamation-triangle text-yellow-500 text-2xl mb-3"></i>
                <p class="text-gray-800 text-sm">Unahakika unataka kufuta bidhaa zote kwenye kikapu hiki?</p>
            </div>
            <div class="flex justify-center space-x-2">
                <button id="cancel-clear-cart" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm">
                    Ghairi
                </button>
                <button id="confirm-clear-cart" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm">
                    Ndio, Futa
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Cart Confirmation Modal -->
<div id="delete-cart-modal" class="modal fixed inset-0 z-50 flex items-center justify-center hidden p-2">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-lg shadow-lg w-full max-w-sm mx-2 z-50">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-800 text-center">Futa Kikapu</h3>
        </div>
        <div class="p-4">
            <div class="text-center mb-4">
                <i class="fas fa-exclamation-triangle text-yellow-500 text-2xl mb-3"></i>
                <p class="text-gray-800 text-sm">Unahakika unataka kufuta kikapu hiki? Bidhaa zote zitaondolewa.</p>
            </div>
            <div class="flex justify-center space-x-2">
                <button id="cancel-delete-cart" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm">
                    Ghairi
                </button>
                <button id="confirm-delete-cart" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm">
                    Ndio, Futa
                </button>
            </div>
        </div>
    </div>
</div>

<!-- In-page print (no new window) - invisible speed improvement -->
<div id="mauzo-print-modal" class="fixed inset-0 bg-black/50 z-[70] hidden items-center justify-center p-4">
    <div class="bg-white rounded-lg w-full max-w-[420px] max-h-[90vh] flex flex-col overflow-hidden">
        <div class="px-4 py-3 border-b flex items-center justify-between"><h3 class="font-bold text-sm">Chapisha</h3><button onclick="document.getElementById('mauzo-print-modal').classList.add('hidden');document.getElementById('mauzo-print-modal').classList.remove('flex');document.getElementById('mauzo-print-iframe').src='about:blank';document.body.style.overflow=''" class="w-8 h-8 flex items-center justify-center hover:bg-gray-100 rounded"><i class="fas fa-times"></i></button></div>
        <div class="flex-1 bg-gray-50 p-2"><iframe id="mauzo-print-iframe" class="w-full h-[58vh] bg-white rounded border" title="Print"></iframe></div>
        <div class="p-3 border-t flex gap-2"><button onclick="document.getElementById('mauzo-print-modal').classList.add('hidden');document.getElementById('mauzo-print-modal').classList.remove('flex');document.getElementById('mauzo-print-iframe').src='about:blank';document.body.style.overflow=''" class="flex-1 py-2 border rounded text-sm">Funga</button><button onclick="try{var f=document.getElementById('mauzo-print-iframe');f.contentWindow.focus();f.contentWindow.print();}catch(e){}" class="flex-1 py-2 bg-green-600 text-white rounded text-sm">Print</button></div>
    </div>
</div>
@endsection

@push('styles')
<style>
.tab-content {
    transition: opacity 0.3s ease;
}
.tab-content.active {
    display: block;
}
.tab-content.hidden {
    display: none;
}
.tab-button.active {
    border-bottom: 2px solid white !important;
    color: white !important;
    font-weight: 600 !important;
}
.tab-button:not(.active) {
    border-bottom: 2px solid transparent !important;
    color: rgba(255, 255, 255, 0.8) !important;
}
.tab-button:not(.active):hover {
    color: white !important;
    border-bottom-color: rgba(255, 255, 255, 0.5) !important;
}
.modal {
    animation: fadeIn 0.3s ease;
}
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
.hidden {
    display: none !important;
}

#product-dropdown {
    min-width: 100%;
    max-height: 400px;
}
.product-item {
    transition: all 0.15s ease;
}
.product-item:hover {
    background-color: #f0fdf4;
}
.product-item.selected {
    background-color: #dcfce7;
    border-left: 3px solid #22c55e;
}
#product-dropdown::-webkit-scrollbar {
    width: 6px;
}
#product-dropdown::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}
#product-dropdown::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}
#product-dropdown::-webkit-scrollbar-thumb:hover {
    background: #555;
}

#kikapu-workspace {
    animation: slideDown 0.3s ease;
}
@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.cart-tab {
    transition: all 0.2s ease;
}
.cart-tab.active {
    background-color: #2563eb;
    color: white;
}
.cart-tab:hover:not(.active) {
    background-color: #eff6ff;
}

.cart-item-enter {
    animation: fadeInItem 0.2s ease;
}
@keyframes fadeInItem {
    from {
        opacity: 0;
        transform: translateY(-5px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 640px) {
    .modal-content {
        width: 95% !important;
        margin: 0.5rem !important;
    }
    #product-dropdown {
        max-height: 300px;
    }
    .product-item {
        padding: 12px 16px;
    }
}
</style>
@endpush


@push('scripts')
<script>
class MauzoManager {
    constructor() {
        this.companyId = document.querySelector('meta[name="company-id"]')?.getAttribute('content') 
                       || document.body.dataset.companyId
                       || 'default';
        
        this.cartKey = `mauzo_cart_${this.companyId}`;
        this.carts = JSON.parse(localStorage.getItem(this.cartKey)) || [];
        this.activeCartIndex = this.carts.length > 0 ? 0 : -1;
        this.bidhaaList = @json($bidhaa);
        this.barcodeScanTimeout = null;
        this.currentReceiptNo = null;
        this.pendingSaleData = null;
        this.currentKopeshaType = null;
        this.currentSaleToDelete = null;
        this.searchTimer = null;
        this.pendingSmsData = null;
        this.pendingSmsReceiptNo = null;
        this.isKikapuViewOpen = false;
        this.pendingCartItemRemove = null;
        this.pendingCartClear = null;
        this.pendingCartDelete = null;
        this.pendingConfirmAction = null;
        this.pendingCartKopesha = null;
        
        this.isMfanyakazi = document.body.hasAttribute('data-mfanyakazi') || false;
        
        this.restoreTabState();
        this.init();
        
        setTimeout(() => {
            this.setupSmsModalEvents();
        }, 100);
    }

    init() {
        this.bindEvents();
        this.updateCartBadges();
        this.setTodayDate();
        this.initBidhaaSearch();
        this.initBarcodeRows();
        this.initCustomerSelection();
        this.clearOtherCompanyCarts();
        this.bindDeleteSaleEvents();
        this.initReceiptLookup();
        this.bindSearchEvents();
        this.bindPriceTypeChange();
        this.bindSmsReceiptEvents();
        this.bindPaymentTypeEvents();
        this.initBackdateToggle();
        this.renderCartTabs();
        
        if (this.carts.length > 0) {
            this.activeCartIndex = 0;
            this.renderActiveCart();
        }
        
        // Bind Taarifa tab receipt events
        this.bindTaarifaReceiptEvents();
    }

    // ================================================================
    // TAARIFA TAB - RECEIPT COPY AND PRINT EVENTS
    // ================================================================

    bindTaarifaReceiptEvents() {
        // Handle copy receipt - using event delegation for dynamic content
        document.addEventListener('click', (e) => {
            const copyBtn = e.target.closest('.copy-receipt');
            if (copyBtn) {
                e.preventDefault();
                const receiptNo = copyBtn.dataset.receipt;
                if (receiptNo) {
                    this.copyReceiptNumber(receiptNo);
                }
            }
        });

        // Handle print receipt - using event delegation for dynamic content
        document.addEventListener('click', (e) => {
            const printBtn = e.target.closest('.print-single-receipt');
            if (printBtn) {
                e.preventDefault();
                e.stopPropagation();
                const receiptNo = printBtn.dataset.receiptNo;
                if (receiptNo) {
                    this.printSingleReceipt(receiptNo);
                } else {
                    this.showNotification('Namba ya risiti haipatikani', 'error');
                }
            }
        });
    }

    /**
     * Copy receipt number to clipboard
     */
    copyReceiptNumber(receiptNo) {
        if (!receiptNo) {
            this.showNotification('Hakuna namba ya risiti', 'error');
            return;
        }

        // Try using Clipboard API
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(receiptNo).then(() => {
                this.showNotification('✓ Namba ya risiti imenakiliwa!', 'success');
            }).catch(() => {
                // Fallback to manual copy
                this.copyToClipboardFallback(receiptNo);
            });
        } else {
            this.copyToClipboardFallback(receiptNo);
        }
    }

    /**
     * Fallback method to copy text to clipboard
     */
    copyToClipboardFallback(text) {
        try {
            const textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.style.position = 'fixed';
            textarea.style.left = '-9999px';
            textarea.style.top = '-9999px';
            textarea.style.width = '1px';
            textarea.style.height = '1px';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.focus();
            textarea.select();
            
            let success = false;
            try {
                success = document.execCommand('copy');
            } catch (err) {
                console.warn('execCommand copy failed:', err);
            }
            
            textarea.remove();
            
            if (success) {
                this.showNotification('✓ Namba ya risiti imenakiliwa!', 'success');
            } else {
                // Last resort - select and prompt user to copy manually
                this.showNotification('Tafadhali nakili manually: ' + text, 'warning');
                // Copy to a temporary input and select it
                const tempInput = document.createElement('input');
                tempInput.value = text;
                tempInput.style.position = 'fixed';
                tempInput.style.left = '-9999px';
                tempInput.style.top = '-9999px';
                document.body.appendChild(tempInput);
                tempInput.focus();
                tempInput.select();
                setTimeout(() => {
                    tempInput.remove();
                }, 500);
            }
        } catch (err) {
            console.error('Copy fallback error:', err);
            this.showNotification('Imeshindwa kunakili. Tafadhali nakili manually.', 'error');
        }
    }

    /**
     * Print a single receipt
     */
    printSingleReceipt(receiptNo) {
        if (!receiptNo) {
            this.showNotification('Hakuna namba ya risiti', 'error');
            return;
        }

        // Show loading notification
        this.showNotification('Inaandaa risiti kwa chapisho...', 'warning');

        // Open print window with thermal receipt
        const printWindow = window.open(
            `/mauzo/thermal-receipt/${encodeURIComponent(receiptNo)}`,
            '_blank',
            'width=400,height=600,scrollbars=yes,resizable=yes'
        );
        
        if (printWindow) {
            printWindow.focus();
            // Notification will be shown after a short delay
            setTimeout(() => {
                this.showNotification('Risiti inachapishwa...', 'success');
            }, 500);
        } else {
            this.showNotification('Tafadhali ruhusu popups kwa chapisho la risiti', 'error');
        }
    }

    // ================================================================
    // CART MANAGEMENT
    // ================================================================

    saveCarts() {
        localStorage.setItem(this.cartKey, JSON.stringify(this.carts));
    }

    getActiveCart() {
        if (this.activeCartIndex >= 0 && this.activeCartIndex < this.carts.length) {
            return this.carts[this.activeCartIndex];
        }
        return null;
    }

    getCartTotal(cart) {
        if (!cart || !cart.items) return 0;
        return cart.items.reduce((sum, item) => sum + (item.jumla || 0), 0);
    }

    getCartItemCount(cart) {
        if (!cart || !cart.items) return 0;
        return cart.items.length;
    }

    createNewCart() {
        const newCart = {
            id: Date.now(),
            items: [],
            customer_id: null,
            customer_name: '',
            customer_phone: '',
            created_at: new Date().toISOString(),
            updated_at: new Date().toISOString()
        };
        this.carts.push(newCart);
        this.activeCartIndex = this.carts.length - 1;
        this.saveCarts();
        this.updateCartBadges();
        this.renderCartTabs();
        this.renderActiveCart();
        return newCart;
    }

    addToCart() {
        const bidhaaId = document.getElementById('bidhaaSelect')?.value;
        const quantity = parseFloat(document.getElementById('quantity-input')?.value) || 0;
        const price = parseFloat(document.getElementById('price-input')?.value) || 0;
        const discount = parseFloat(document.getElementById('punguzo-input')?.value) || 0;
        const discountType = document.getElementById('punguzo-type')?.value || 'bidhaa';
        const total = parseFloat(document.getElementById('total-input')?.value) || 0;
        const priceType = document.getElementById('price-type-select')?.value || 'rejareja';

        if (!bidhaaId || quantity < 0.01) {
            this.showNotification('Tafadhali chagua bidhaa na idadi sahihi!', 'error');
            return;
        }

        const selectedProduct = this.bidhaaList.find(p => p.id == bidhaaId);
        if (!selectedProduct) {
            this.showNotification('Bidhaa haipatikani!', 'error');
            return;
        }

        let cart = this.getActiveCart();
        if (!cart) {
            cart = this.createNewCart();
        }

        const existingItemIndex = cart.items.findIndex(item => item.bidhaa_id == bidhaaId);
        
        if (existingItemIndex >= 0) {
            const existingItem = cart.items[existingItemIndex];
            existingItem.idadi += quantity;
            existingItem.jumla += total;
            existingItem.punguzo = discount;
            existingItem.punguzo_aina = discountType;
            existingItem.bei = price;
            cart.items[existingItemIndex] = existingItem;
        } else {
            const actualDiscount = discountType === 'bidhaa' ? discount * quantity : discount;
            cart.items.push({
                bidhaa_id: parseInt(bidhaaId),
                jina: selectedProduct.jina,
                aina: selectedProduct.aina || '',
                kipimo: selectedProduct.kipimo || '',
                idadi: quantity,
                bei: price,
                bei_type: priceType,
                punguzo: discount,
                punguzo_aina: discountType,
                actual_discount: actualDiscount,
                jumla: total,
                barcode: selectedProduct.barcode || ''
            });
        }

        cart.updated_at = new Date().toISOString();
        this.saveCarts();
        this.updateCartBadges();
        this.renderCartTabs();
        this.renderActiveCart();
        
        this.showNotification(`"${selectedProduct.jina}" imeongezwa kwenye Kikapu!`, 'success');
        this.resetForm();
    }

    removeFromCart(itemIndex) {
        const cart = this.getActiveCart();
        if (!cart) return;
        
        const item = cart.items[itemIndex];
        if (!item) return;
        
        this.pendingCartItemRemove = { cartIndex: this.activeCartIndex, itemIndex: itemIndex };
        document.getElementById('remove-item-name').textContent = item.jina || 'bidhaa';
        document.getElementById('remove-cart-item-modal').classList.remove('hidden');
    }

    confirmRemoveFromCart() {
        if (!this.pendingCartItemRemove) return;
        
        const { cartIndex, itemIndex } = this.pendingCartItemRemove;
        if (cartIndex >= 0 && cartIndex < this.carts.length) {
            const cart = this.carts[cartIndex];
            cart.items.splice(itemIndex, 1);
            if (cart.items.length === 0 && this.carts.length > 1) {
                this.carts.splice(cartIndex, 1);
                if (this.activeCartIndex >= this.carts.length) {
                    this.activeCartIndex = this.carts.length - 1;
                }
            }
            this.saveCarts();
            this.updateCartBadges();
            this.renderCartTabs();
            this.renderActiveCart();
            this.showNotification('Bidhaa imeondolewa kwenye kikapu!', 'success');
        }
        this.pendingCartItemRemove = null;
        document.getElementById('remove-cart-item-modal').classList.add('hidden');
    }

    clearCart() {
        const cart = this.getActiveCart();
        if (!cart || cart.items.length === 0) {
            this.showNotification('Kikapu tayari hakina bidhaa!', 'warning');
            return;
        }
        this.pendingCartClear = this.activeCartIndex;
        document.getElementById('clear-cart-modal').classList.remove('hidden');
    }

    confirmClearCart() {
        if (this.pendingCartClear !== null && this.pendingCartClear < this.carts.length) {
            this.carts[this.pendingCartClear].items = [];
            this.saveCarts();
            this.updateCartBadges();
            this.renderCartTabs();
            this.renderActiveCart();
            this.showNotification('Kikapu kimefutwa!', 'success');
        }
        this.pendingCartClear = null;
        document.getElementById('clear-cart-modal').classList.add('hidden');
    }

    deleteCart() {
        if (this.carts.length <= 1) {
            this.showNotification('Huwezi kufuta kikapu cha mwisho. Badala yake, futa bidhaa zote.', 'warning');
            return;
        }
        this.pendingCartDelete = this.activeCartIndex;
        document.getElementById('delete-cart-modal').classList.remove('hidden');
    }

    confirmDeleteCart() {
        if (this.pendingCartDelete !== null && this.pendingCartDelete < this.carts.length) {
            this.carts.splice(this.pendingCartDelete, 1);
            if (this.activeCartIndex >= this.carts.length) {
                this.activeCartIndex = this.carts.length - 1;
            }
            this.saveCarts();
            this.updateCartBadges();
            this.renderCartTabs();
            this.renderActiveCart();
            this.showNotification('Kikapu kimefutwa!', 'success');
        }
        this.pendingCartDelete = null;
        document.getElementById('delete-cart-modal').classList.add('hidden');
    }

    switchCart(index) {
        if (index >= 0 && index < this.carts.length) {
            this.activeCartIndex = index;
            this.renderCartTabs();
            this.renderActiveCart();
        }
    }

    // ================================================================
    // RENDER KIKAPU UI
    // ================================================================

    renderCartTabs() {
        const container = document.getElementById('cart-switcher');
        if (!container) return;

        if (this.carts.length === 0) {
            container.innerHTML = `
                <div class="text-sm text-gray-500 py-2">
                    <i class="fas fa-info-circle mr-1"></i>
                    Hakuna Kikapu. Ongeza bidhaa kutoka Sehemu ya Mauzo.
                </div>
            `;
            return;
        }

        let html = '';
        this.carts.forEach((cart, index) => {
            const isActive = index === this.activeCartIndex;
            const itemCount = this.getCartItemCount(cart);
            const total = this.getCartTotal(cart);
            const customerName = cart.customer_name || `Kikapu ${index + 1}`;
            
            html += `
                <button class="cart-tab px-3 py-2 rounded-lg text-sm font-medium transition ${isActive ? 'bg-blue-600 text-white shadow' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'}"
                        data-index="${index}">
                    <span class="truncate max-w-[120px] inline-block">${customerName}</span>
                    <span class="ml-1 text-xs ${isActive ? 'bg-white/20' : 'bg-gray-300'} px-1.5 py-0.5 rounded-full">
                        ${itemCount}
                    </span>
                    <span class="ml-1 text-xs font-bold">Tsh ${total.toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 0})}</span>
                </button>
            `;
        });

        html += `
            <button id="create-new-cart-btn" class="px-3 py-2 rounded-lg text-sm font-medium transition bg-green-100 text-green-700 hover:bg-green-200">
                <i class="fas fa-plus mr-1"></i> Mpya
            </button>
        `;

        container.innerHTML = html;

        container.querySelectorAll('.cart-tab').forEach(btn => {
            btn.addEventListener('click', () => {
                this.switchCart(parseInt(btn.dataset.index));
            });
        });

        const newBtn = document.getElementById('create-new-cart-btn');
        if (newBtn) {
            newBtn.addEventListener('click', () => {
                this.createNewCart();
            });
        }
    }

    renderActiveCart() {
        const cartContent = document.getElementById('kikapu-cart-content');
        const emptyState = document.getElementById('kikapu-empty-state');
        const activeInfo = document.getElementById('kikapu-active-info');
        
        if (!cartContent || !emptyState) return;

        const cart = this.getActiveCart();

        if (!cart || cart.items.length === 0) {
            cartContent.classList.add('hidden');
            emptyState.classList.remove('hidden');
            if (activeInfo) activeInfo.textContent = 'Hakuna kikapu kilichochaguliwa';
            return;
        }

        emptyState.classList.add('hidden');
        cartContent.classList.remove('hidden');

        if (activeInfo) {
            const customerName = cart.customer_name || 'Bila Mteja';
            const itemCount = this.getCartItemCount(cart);
            const total = this.getCartTotal(cart);
            activeInfo.textContent = `${customerName} • ${itemCount} bidhaa • Tsh ${total.toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 0})}`;
        }

        let itemsHtml = '';
        let subtotal = 0;
        let totalDiscount = 0;

        cart.items.forEach((item, index) => {
            const itemTotal = item.jumla || (item.bei * item.idadi);
            const discountAmount = item.punguzo_aina === 'bidhaa' 
                ? (item.punguzo || 0) * item.idadi 
                : (item.punguzo || 0);
            
            subtotal += item.bei * item.idadi;
            totalDiscount += discountAmount;

            itemsHtml += `
                <div class="cart-item-enter bg-white border border-gray-200 rounded-lg p-3 mb-2 hover:shadow transition">
                    <div class="flex flex-wrap items-start justify-between gap-2">
                        <div class="flex-1 min-w-0">
                            <div class="font-semibold text-gray-800">${item.jina}</div>
                            <div class="flex flex-wrap gap-1 mt-0.5">
                                ${item.aina ? `<span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded">${item.aina}</span>` : ''}
                                ${item.kipimo ? `<span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded">${item.kipimo}</span>` : ''}
                            </div>
                            <div class="flex flex-wrap items-center gap-3 mt-1 text-sm text-gray-600">
                                <span>Bei: Tsh ${item.bei.toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 0})}</span>
                                <span>Idadi: ${item.idadi.toFixed(2)}</span>
                                ${item.punguzo > 0 ? `<span class="text-red-600">Punguzo: -${discountAmount.toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 0})}</span>` : ''}
                                <span class="font-semibold text-green-700">Jumla: Tsh ${itemTotal.toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 0})}</span>
                            </div>
                        </div>
                        <div class="flex gap-1 flex-shrink-0">
                            <button class="cart-edit-qty px-2 py-1 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded text-xs transition" data-index="${index}" data-action="decrease">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button class="cart-edit-qty px-2 py-1 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded text-xs transition" data-index="${index}" data-action="increase">
                                <i class="fas fa-plus"></i>
                            </button>
                            <button class="cart-remove-item px-2 py-1 bg-red-100 hover:bg-red-200 text-red-700 rounded text-xs transition" data-index="${index}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });

        const total = subtotal - totalDiscount;

        cartContent.innerHTML = `
            <div class="space-y-3">
                <div class="max-h-80 overflow-y-auto pr-1">
                    ${itemsHtml}
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-3 border-t border-gray-200">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            <i class="fas fa-user mr-1"></i> Mteja
                        </label>
                        <div class="relative">
                            <input type="text" id="kikapu_customer_search" 
                                   placeholder="Tafuta au weka jina/mteja..." 
                                   value="${cart.customer_name || ''}"
                                   class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-200">
                            <select id="kikapu_customer_select" size="4" 
                                    class="w-full border border-gray-300 rounded-lg p-2 text-sm hidden absolute z-10 bg-white shadow-lg max-h-48 overflow-y-auto mt-1">
                                <option value="">-- Tafuta au Chagua Mteja --</option>
                                @foreach($wateja as $mteja)
                                    <option value="{{ $mteja->id }}" data-simu="{{ $mteja->simu }}" data-jina="{{ $mteja->jina }}">
                                        {{ $mteja->jina }} - {{ $mteja->simu }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" id="kikapu_selected_customer_id" value="${cart.customer_id || ''}">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            <i class="fas fa-phone mr-1"></i> Simu
                        </label>
                        <input type="text" id="kikapu_customer_phone" 
                               placeholder="Namba ya simu..." 
                               value="${cart.customer_phone || ''}"
                               class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-200">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Njia ya Malipo</label>
                        <select id="kikapu-lipa-kwa" class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-green-200">
                            <option value="cash">💰 Cash</option>
                            <option value="lipa_namba">📱 Lipa Namba</option>
                            <option value="bank">🏦 Bank</option>
                        </select>
                    </div>
                    <div id="kikapu-lipa-namba-container" class="hidden">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Aina ya Lipa Namba</label>
                        <select id="kikapu-lipa-namba-type" class="w-full border border-gray-300 rounded-lg p-2 text-sm">
                            <option value="">-- Chagua Aina --</option>
                            <option value="mpesa">📱 M-Pesa</option>
                            <option value="mixx_by_yas">🎮 Mixx by Yas</option>
                            <option value="airtel_money">📱 Airtel Money</option>
                            <option value="halopesa">📱 HaloPesa</option>
                            <option value="other">🔄 Nyingine</option>
                        </select>
                    </div>
                    <div id="kikapu-bank-container" class="hidden">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Aina ya Benki</label>
                        <select id="kikapu-bank-type" class="w-full border border-gray-300 rounded-lg p-2 text-sm">
                            <option value="">-- Chagua Benki --</option>
                            <option value="crdb">🏦 CRDB</option>
                            <option value="nmb">🏦 NMB</option>
                            <option value="nbc">🏦 NBC</option>
                            <option value="other">🔄 Nyingine</option>
                        </select>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                    <div class="flex flex-wrap justify-between gap-2 text-sm">
                        <span>Bidhaa: <strong>${cart.items.length}</strong></span>
                        <span>Jumla Ndogo: <strong>Tsh ${subtotal.toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 0})}</strong></span>
                        ${totalDiscount > 0 ? `<span class="text-red-600">Punguzo: -Tsh ${totalDiscount.toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 0})}</span>` : ''}
                        <span class="text-lg font-bold text-green-700">JUMLA: Tsh ${total.toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 0})}</span>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2 pt-2 border-t border-gray-200">
                    <button id="kikapu-uza-btn" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 text-sm font-semibold transition shadow hover:shadow-md">
                        <i class="fas fa-cash-register"></i> Uza
                    </button>
                    <button id="kikapu-kopesha-btn" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 text-sm font-semibold transition shadow hover:shadow-md">
                        <i class="fas fa-hand-holding-usd"></i> Kopesha
                    </button>
                    <button id="kikapu-order-btn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 text-sm font-semibold transition shadow hover:shadow-md">
                        <i class="fas fa-clipboard-list"></i> Hifadhi Order
                    </button>
                    <button id="kikapu-print-btn" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 text-sm font-semibold transition shadow hover:shadow-md">
                        <i class="fas fa-print"></i> Print Kikapu
                    </button>
                    <button id="kikapu-clear-btn" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 text-sm font-semibold transition shadow hover:shadow-md">
                        <i class="fas fa-trash"></i> Futa Kikapu
                    </button>
                    <button id="kikapu-delete-btn" class="bg-red-700 hover:bg-red-800 text-white px-4 py-2 rounded-lg flex items-center gap-2 text-sm font-semibold transition shadow hover:shadow-md">
                        <i class="fas fa-times"></i> Futa Kikapu
                    </button>
                </div>
            </div>
        `;

        this.bindCartItemEvents();
        this.bindCartActionEvents();
        this.bindKikapuCustomerSearch();
        this.bindKikapuPaymentTypeEvents();
    }

    bindKikapuPaymentTypeEvents() {
        const lipaKwaSelect = document.getElementById('kikapu-lipa-kwa');
        const lipaNambaContainer = document.getElementById('kikapu-lipa-namba-container');
        const bankContainer = document.getElementById('kikapu-bank-container');
        
        if (lipaKwaSelect) {
            lipaKwaSelect.addEventListener('change', (e) => {
                const value = e.target.value;
                if (lipaNambaContainer) lipaNambaContainer.classList.add('hidden');
                if (bankContainer) bankContainer.classList.add('hidden');
                
                if (value === 'lipa_namba') {
                    if (lipaNambaContainer) lipaNambaContainer.classList.remove('hidden');
                } else if (value === 'bank') {
                    if (bankContainer) bankContainer.classList.remove('hidden');
                }
            });
        }
    }

    bindCartItemEvents() {
        document.querySelectorAll('.cart-remove-item').forEach(btn => {
            btn.addEventListener('click', () => {
                this.removeFromCart(parseInt(btn.dataset.index));
            });
        });

        document.querySelectorAll('.cart-edit-qty').forEach(btn => {
            btn.addEventListener('click', () => {
                const index = parseInt(btn.dataset.index);
                const action = btn.dataset.action;
                this.editCartItemQuantity(index, action);
            });
        });
    }

    editCartItemQuantity(index, action) {
        const cart = this.getActiveCart();
        if (!cart || index >= cart.items.length) return;

        const item = cart.items[index];
        const step = action === 'increase' ? 1 : -1;
        const newQty = Math.max(0.01, item.idadi + step);
        
        const product = this.bidhaaList.find(p => p.id == item.bidhaa_id);
        if (product && newQty > product.idadi) {
            this.showNotification(`Stock haitoshi! Baki ni ${product.idadi.toFixed(2)}`, 'warning');
            return;
        }

        const baseTotal = item.bei * newQty;
        const discountAmount = item.punguzo_aina === 'bidhaa' 
            ? (item.punguzo || 0) * newQty 
            : (item.punguzo || 0);
        
        item.idadi = newQty;
        item.jumla = baseTotal - discountAmount;
        item.actual_discount = discountAmount;

        cart.updated_at = new Date().toISOString();
        this.saveCarts();
        this.updateCartBadges();
        this.renderCartTabs();
        this.renderActiveCart();
    }

    bindCartActionEvents() {
        const uzaBtn = document.getElementById('kikapu-uza-btn');
        const kopeshaBtn = document.getElementById('kikapu-kopesha-btn');
        const orderBtn = document.getElementById('kikapu-order-btn');
        const printBtn = document.getElementById('kikapu-print-btn');
        const clearBtn = document.getElementById('kikapu-clear-btn');
        const deleteBtn = document.getElementById('kikapu-delete-btn');

        if (uzaBtn) uzaBtn.addEventListener('click', () => this.checkoutCart());
        if (kopeshaBtn) kopeshaBtn.addEventListener('click', () => this.kopeshaCart());
        if (orderBtn) orderBtn.addEventListener('click', () => this.saveCartAsOrder());
        if (printBtn) printBtn.addEventListener('click', () => this.printCart());
        if (clearBtn) clearBtn.addEventListener('click', () => this.clearCart());
        if (deleteBtn) deleteBtn.addEventListener('click', () => this.deleteCart());
    }

    // ================================================================
    // CART ACTIONS
    // ================================================================

    async checkoutCart() {
        const cart = this.getActiveCart();
        if (!cart || cart.items.length === 0) {
            this.showNotification('Kikapu hakina bidhaa!', 'error');
            return;
        }

        for (const item of cart.items) {
            const product = this.bidhaaList.find(p => p.id == item.bidhaa_id);
            if (product && item.idadi > product.idadi) {
                this.showNotification(`Stock haitoshi kwa ${item.jina}! Baki ni ${product.idadi.toFixed(2)}`, 'error');
                return;
            }
        }

        const total = this.getCartTotal(cart);
        const itemCount = cart.items.length;
        const customerName = cart.customer_name || 'Bila Mteja';

        const lipaKwa = document.getElementById('kikapu-lipa-kwa')?.value || 'cash';
        let paymentLabel = 'Cash';
        if (lipaKwa === 'lipa_namba') {
            const typeSelect = document.getElementById('kikapu-lipa-namba-type');
            const typeMap = { 'mpesa': 'M-Pesa', 'mixx_by_yas': 'Mixx by Yas', 'airtel_money': 'Airtel Money', 'halopesa': 'HaloPesa', 'other': 'Nyingine' };
            paymentLabel = typeSelect?.value ? typeMap[typeSelect.value] || typeSelect.value : 'Lipa Namba';
        } else if (lipaKwa === 'bank') {
            const typeSelect = document.getElementById('kikapu-bank-type');
            const typeMap = { 'crdb': 'CRDB', 'nmb': 'NMB', 'nbc': 'NBC', 'other': 'Nyingine' };
            paymentLabel = typeSelect?.value ? typeMap[typeSelect.value] || typeSelect.value : 'Benki';
        }

        const detailsHtml = `
            <div class="space-y-2 text-sm">
                <div class="flex justify-between"><span>Mteja:</span> <strong>${customerName}</strong></div>
                <div class="flex justify-between"><span>Bidhaa:</span> <strong>${itemCount}</strong></div>
                <div class="flex justify-between"><span>Malipo:</span> <strong>${paymentLabel}</strong></div>
                <div class="flex justify-between"><span>Jumla:</span> <strong class="text-green-700">Tsh ${total.toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 0})}</strong></div>
            </div>
        `;

        document.getElementById('confirm-sale-details').innerHTML = detailsHtml;
        this.pendingConfirmAction = 'checkout';
        document.getElementById('confirm-sale-modal').classList.remove('hidden');
    }

    async proceedCheckout() {
        const cart = this.getActiveCart();
        if (!cart) return;

        const lipaKwa = document.getElementById('kikapu-lipa-kwa')?.value || 'cash';
        let lipaKwaType = null;
        
        if (lipaKwa === 'lipa_namba') {
            const typeSelect = document.getElementById('kikapu-lipa-namba-type');
            if (typeSelect && typeSelect.value) {
                lipaKwaType = typeSelect.value;
            } else {
                this.showNotification('Tafadhali chagua aina ya Lipa Namba', 'error');
                document.getElementById('confirm-sale-modal').classList.add('hidden');
                return;
            }
        } else if (lipaKwa === 'bank') {
            const typeSelect = document.getElementById('kikapu-bank-type');
            if (typeSelect && typeSelect.value) {
                lipaKwaType = typeSelect.value;
            } else {
                this.showNotification('Tafadhali chagua aina ya Benki', 'error');
                document.getElementById('confirm-sale-modal').classList.add('hidden');
                return;
            }
        }

        const items = cart.items.map(item => ({
            bidhaa_id: item.bidhaa_id,
            idadi: item.idadi,
            bei: item.bei,
            bei_type: item.bei_type || 'rejareja',
            punguzo: item.punguzo || 0,
            punguzo_aina: item.punguzo_aina || 'bidhaa',
            jina: item.jina
        }));

        const requestData = {
            items: items,
            lipa_kwa: lipaKwa,
            lipa_kwa_type: lipaKwaType,
            mteja_id: cart.customer_id || null,
            send_receipt: document.getElementById('send_receipt_checkbox')?.checked ? '1' : '0',
            send_to_phone: cart.customer_phone || null
        };

        try {
            const response = await fetch("{{ route('mauzo.store.kikapu') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Company-ID': this.companyId
                },
                body: JSON.stringify(requestData)
            });

            const data = await response.json();
            
            if (data.success) {
                this.showNotification('Mauzo yamehifadhiwa kikamilifu! Namba ya risiti: ' + data.receipt_no, 'success');
                this.syncStocksForItems(items);
                this.carts.splice(this.activeCartIndex, 1);
                if (this.carts.length === 0) {
                    this.activeCartIndex = -1;
                } else if (this.activeCartIndex >= this.carts.length) {
                    this.activeCartIndex = this.carts.length - 1;
                }
                this.saveCarts();
                this.updateCartBadges();
                this.renderCartTabs();
                this.renderActiveCart();
                
                if (this.carts.length === 0 && this.isKikapuViewOpen) {
                    this.closeKikapuView();
                }
                
                setTimeout(() => { refreshFinancialSoft(); refreshSalesTableSoft(); }, 400);
            } else {
                this.showNotification(data.message || 'Kuna tatizo kwenye kuhifadhi!', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            this.showNotification('Kuna tatizo kwenye kuhifadhi!', 'error');
        }

        document.getElementById('confirm-sale-modal').classList.add('hidden');
        this.pendingConfirmAction = null;
    }

    async kopeshaCart() {
        const cart = this.getActiveCart();
        if (!cart || cart.items.length === 0) {
            this.showNotification('Kikapu hakina bidhaa!', 'error');
            return;
        }

        for (const item of cart.items) {
            const product = this.bidhaaList.find(p => p.id == item.bidhaa_id);
            if (product && item.idadi > product.idadi) {
                this.showNotification(`Stock haitoshi kwa ${item.jina}! Baki ni ${product.idadi.toFixed(2)}`, 'error');
                return;
            }
        }

        const items = cart.items.map(item => ({
            bidhaa_id: item.bidhaa_id,
            idadi: item.idadi,
            bei: item.bei,
            bei_type: item.bei_type || 'rejareja',
            punguzo: item.punguzo || 0,
            punguzo_aina: item.punguzo_aina || 'bidhaa',
            jina: item.jina,
            jumla: item.jumla
        }));

        document.getElementById('kikapu-items-data').value = JSON.stringify(items);

        if (cart.customer_name) {
            document.getElementById('kikapu-kopesha-jina').value = cart.customer_name;
        }
        if (cart.customer_phone) {
            document.getElementById('kikapu-kopesha-simu').value = cart.customer_phone;
        }

        this.pendingCartKopesha = this.activeCartIndex;
        document.getElementById('kikapu-kopesha-modal').classList.remove('hidden');
    }

    submitKikapuKopeshaForm(form) {
        const formData = new FormData(form);
        const itemsData = document.getElementById('kikapu-items-data').value;
        if (itemsData) {
            formData.append('items', itemsData);
        }

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.showNotification('Deni limerekodiwa kikamilifu!', 'success');
                document.getElementById('kikapu-kopesha-modal').classList.add('hidden');
                
                if (this.pendingCartKopesha !== null && this.pendingCartKopesha < this.carts.length) {
                    this.carts.splice(this.pendingCartKopesha, 1);
                    if (this.carts.length === 0) {
                        this.activeCartIndex = -1;
                    } else if (this.activeCartIndex >= this.carts.length) {
                        this.activeCartIndex = this.carts.length - 1;
                    }
                    this.saveCarts();
                    this.updateCartBadges();
                    this.renderCartTabs();
                    this.renderActiveCart();
                    this.updateFinancialData();
                    
                    if (this.carts.length === 0 && this.isKikapuViewOpen) {
                        this.closeKikapuView();
                    }
                }
                this.pendingCartKopesha = null;
                setTimeout(() => { refreshFinancialSoft(); refreshSalesTableSoft(); }, 600);
            } else {
                this.showNotification(data.message || 'Kuna tatizo kwenye kuhifadhi!', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            this.showNotification('Kuna tatizo kwenye kuhifadhi!', 'error');
        });
    }

    async saveCartAsOrder() {
        const cart = this.getActiveCart();
        if (!cart || cart.items.length === 0) {
            this.showNotification('Kikapu hakina bidhaa!', 'error');
            return;
        }

        for (const item of cart.items) {
            const product = this.bidhaaList.find(p => p.id == item.bidhaa_id);
            if (product && item.idadi > product.idadi) {
                this.showNotification(`Stock haitoshi kwa ${item.jina}! Baki ni ${product.idadi.toFixed(2)}`, 'error');
                return;
            }
        }

        let subtotal = 0;
        let totalDiscount = 0;
        cart.items.forEach(item => {
            subtotal += item.bei * item.idadi;
            const discountAmount = item.punguzo_aina === 'bidhaa' 
                ? (item.punguzo || 0) * item.idadi 
                : (item.punguzo || 0);
            totalDiscount += discountAmount;
        });
        const total = subtotal - totalDiscount;

        const orderData = {
            items: cart.items.map(item => ({
                id: item.bidhaa_id,
                name: item.jina,
                price: item.bei,
                qty: item.idadi,
                total: item.jumla
            })),
            customer_name: cart.customer_name || '',
            customer_phone: cart.customer_phone || '',
            subtotal: subtotal,
            discount: totalDiscount,
            total: total,
            status: 'saved'
        };

        try {
            const response = await fetch("{{ route('orders.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Company-ID': this.companyId
                },
                body: JSON.stringify(orderData)
            });

            const data = await response.json();
            
            if (data.success) {
                this.showNotification('Order imehifadhiwa kikamilifu! Namba: ' + data.order_number, 'success');
                this.carts.splice(this.activeCartIndex, 1);
                if (this.carts.length === 0) {
                    this.activeCartIndex = -1;
                } else if (this.activeCartIndex >= this.carts.length) {
                    this.activeCartIndex = this.carts.length - 1;
                }
                this.saveCarts();
                this.updateCartBadges();
                this.renderCartTabs();
                this.renderActiveCart();
                
                const badge = document.getElementById('new-order-badge-tab');
                if (badge) {
                    const count = parseInt(badge.textContent) || 0;
                    badge.textContent = count + 1;
                    badge.classList.remove('hidden');
                }
                
                if (this.carts.length === 0 && this.isKikapuViewOpen) {
                    this.closeKikapuView();
                }
            } else {
                this.showNotification(data.message || 'Kuna tatizo kwenye kuhifadhi Order!', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            this.showNotification('Kuna tatizo kwenye kuhifadhi!', 'error');
        }
    }

    printCart() {
        const cart = this.getActiveCart();
        if (!cart || cart.items.length === 0) {
            this.showNotification('Kikapu hakina bidhaa!', 'error');
            return;
        }

        const companyName = document.querySelector('meta[name="company-name"]')?.getAttribute('content') || 'BIASHARA YANGU';
        const cartNumber = this.activeCartIndex + 1;
        const total = this.getCartTotal(cart);
        const itemCount = cart.items.length;
        const customerName = cart.customer_name || 'Bila Mteja';

        let printContent = `
            <div style="font-family: monospace; padding: 20px; max-width: 300px; margin: 0 auto;">
                <h2 style="text-align: center; font-size: 18px; margin-bottom: 5px;">${companyName}</h2>
                <p style="text-align: center; font-size: 12px; margin: 0;">KIKAPU #${cartNumber}</p>
                <p style="text-align: center; font-size: 12px; margin: 0;">${new Date().toLocaleString('sw-TZ')}</p>
                <p style="text-align: center; font-size: 12px; margin-bottom: 10px;">Mteja: ${customerName}</p>
                <hr style="border: 1px dashed #000; margin: 10px 0;">
                <table style="width: 100%; font-size: 12px; border-collapse: collapse;">
        `;

        cart.items.forEach((item, index) => {
            const itemTotal = item.jumla || (item.bei * item.idadi);
            const discountAmount = item.punguzo_aina === 'bidhaa' 
                ? (item.punguzo || 0) * item.idadi 
                : (item.punguzo || 0);
            
            printContent += `
                <tr>
                    <td style="padding: 3px 0;" colspan="2"><strong>${item.jina}</strong></td>
                </tr>
                <tr>
                    <td style="padding: 0 0 3px 0;">${item.idadi.toFixed(2)} x Tsh ${item.bei.toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 0})}</td>
                    <td style="text-align: right; padding: 0 0 3px 0;">Tsh ${itemTotal.toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 0})}</td>
                </tr>
                ${discountAmount > 0 ? `
                <tr>
                    <td style="padding: 0 0 3px 0; color: red;">Punguzo:</td>
                    <td style="text-align: right; padding: 0 0 3px 0; color: red;">-Tsh ${discountAmount.toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 0})}</td>
                </tr>
                ` : ''}
                ${index < cart.items.length - 1 ? '<tr><td colspan="2" style="padding: 0; border-bottom: 1px dotted #ccc;"></td></tr>' : ''}
            `;
        });

        printContent += `
                </table>
                <hr style="border: 1px dashed #000; margin: 10px 0;">
                <div style="font-size: 14px; font-weight: bold; text-align: right;">
                    JUMLA: Tsh ${total.toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 0})}
                </div>
                <hr style="border: 1px dashed #000; margin: 10px 0;">
                <p style="text-align: center; font-size: 11px; margin: 5px 0;">Kikapu hiki sio risiti rasmi</p>
                <p style="text-align: center; font-size: 11px; margin: 5px 0;">Bidhaa: ${itemCount}</p>
            </div>
        `;

        const printWindow = window.open('', '_blank', 'width=400,height=600');
        if (printWindow) {
            printWindow.document.write(`
                <html>
                    <head><title>Kikapu #${cartNumber}</title></head>
                    <body>${printContent}</body>
                </html>
            `);
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
        }
    }

    // ================================================================
    // KIKAPU VIEW TOGGLE
    // ================================================================

    openKikapuView() {
        const normalView = document.getElementById('normal-sales-view');
        const kikapuWorkspace = document.getElementById('kikapu-workspace');
        
        if (normalView) normalView.classList.add('hidden');
        if (kikapuWorkspace) {
            kikapuWorkspace.classList.remove('hidden');
            kikapuWorkspace.style.animation = 'none';
            setTimeout(() => {
                kikapuWorkspace.style.animation = 'slideDown 0.3s ease';
            }, 10);
        }
        this.isKikapuViewOpen = true;
        
        this.renderCartTabs();
        this.renderActiveCart();
    }

    closeKikapuView() {
        const normalView = document.getElementById('normal-sales-view');
        const kikapuWorkspace = document.getElementById('kikapu-workspace');
        
        if (normalView) normalView.classList.remove('hidden');
        if (kikapuWorkspace) kikapuWorkspace.classList.add('hidden');
        this.isKikapuViewOpen = false;
    }

    // ================================================================
    // CUSTOMER SEARCH FOR KIKAPU
    // ================================================================

    bindKikapuCustomerSearch() {
        const searchInput = document.getElementById('kikapu_customer_search');
        const selectDropdown = document.getElementById('kikapu_customer_select');
        const hiddenId = document.getElementById('kikapu_selected_customer_id');
        const phoneInput = document.getElementById('kikapu_customer_phone');
        
        if (!searchInput) return;

        const filterOptions = (searchText) => {
            const filter = searchText.toLowerCase();
            const options = selectDropdown.getElementsByTagName('option');
            for (let i = 1; i < options.length; i++) {
                const jina = options[i].dataset.jina?.toLowerCase() || '';
                const simu = options[i].dataset.simu?.toLowerCase() || '';
                const searchableText = `${jina} ${simu}`;
                options[i].style.display = (filter === '' || searchableText.includes(filter)) ? '' : 'none';
            }
        };

        searchInput.addEventListener('focus', () => {
            selectDropdown.classList.remove('hidden');
            filterOptions(searchInput.value);
        });

        searchInput.addEventListener('input', (e) => {
            filterOptions(e.target.value);
            selectDropdown.classList.remove('hidden');
            
            const cart = this.getActiveCart();
            if (cart) {
                cart.customer_name = e.target.value;
                cart.customer_id = null;
                if (hiddenId) hiddenId.value = '';
                this.saveCarts();
                this.updateCartBadges();
                this.renderCartTabs();
            }
        });

        selectDropdown.addEventListener('change', () => {
            if (!selectDropdown.value) return;
            
            const selectedOption = selectDropdown.options[selectDropdown.selectedIndex];
            const customerName = selectedOption.dataset.jina || '';
            const customerPhone = selectedOption.dataset.simu || '';
            
            searchInput.value = customerName;
            if (hiddenId) hiddenId.value = selectDropdown.value;
            if (phoneInput) phoneInput.value = customerPhone;
            
            selectDropdown.classList.add('hidden');
            
            const cart = this.getActiveCart();
            if (cart) {
                cart.customer_name = customerName;
                cart.customer_id = parseInt(selectDropdown.value);
                cart.customer_phone = customerPhone;
                this.saveCarts();
                this.updateCartBadges();
                this.renderCartTabs();
            }
        });

        if (phoneInput) {
            phoneInput.addEventListener('input', (e) => {
                const cart = this.getActiveCart();
                if (cart) {
                    cart.customer_phone = e.target.value;
                    this.saveCarts();
                }
            });
        }

        document.addEventListener('click', (e) => {
            if (!e.target.closest('#kikapu_customer_search') && !e.target.closest('#kikapu_customer_select')) {
                selectDropdown.classList.add('hidden');
            }
        });

        searchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                selectDropdown.classList.add('hidden');
            }
        });
    }

    // ================================================================
    // BIDHAA SEARCH
    // ================================================================

    initBidhaaSearch() {
        const searchInput = document.getElementById('bidhaaSearch');
        const dropdown = document.getElementById('product-dropdown');
        const productList = document.getElementById('product-list');
        const noResults = document.getElementById('no-products-found');

        if (!searchInput || !dropdown || !productList) return;

        searchInput.addEventListener('focus', () => {
            this.filterProductOptions(searchInput.value);
            dropdown.classList.remove('hidden');
        });

        searchInput.addEventListener('input', (e) => {
            this.filterProductOptions(e.target.value);
            dropdown.classList.remove('hidden');
        });

        productList.addEventListener('click', (e) => {
            const item = e.target.closest('.product-item');
            if (!item) return;
            
            const id = item.dataset.id;
            const jina = item.dataset.jina;
            const aina = item.dataset.aina || '';
            const kipimo = item.dataset.kipimo || '';
            
            searchInput.value = `${jina}${aina ? ` - ${aina}` : ''}${kipimo ? ` - ${kipimo}` : ''}`;
            document.getElementById('bidhaaSelect').value = id;
            dropdown.classList.add('hidden');
            this.updateProductDetailsFromData(item.dataset);
            
            productList.querySelectorAll('.product-item').forEach(el => el.classList.remove('selected'));
            item.classList.add('selected');
        });

        document.addEventListener('click', (e) => {
            if (!e.target.closest('#product-search-container')) {
                dropdown.classList.add('hidden');
            }
        });

        searchInput.addEventListener('keydown', (e) => {
            const visibleItems = productList.querySelectorAll('.product-item:not([style*="display: none"])');
            const selected = productList.querySelector('.product-item.selected');
            
            if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                e.preventDefault();
                let currentIndex = -1;
                if (selected) {
                    currentIndex = Array.from(visibleItems).indexOf(selected);
                }
                let newIndex = e.key === 'ArrowDown' ? currentIndex + 1 : currentIndex - 1;
                if (newIndex < 0) newIndex = visibleItems.length - 1;
                if (newIndex >= visibleItems.length) newIndex = 0;
                
                visibleItems.forEach(el => el.classList.remove('selected'));
                if (visibleItems[newIndex]) {
                    visibleItems[newIndex].classList.add('selected');
                    visibleItems[newIndex].scrollIntoView({ block: 'nearest' });
                }
            }
            
            if (e.key === 'Enter') {
                e.preventDefault();
                if (selected) {
                    selected.click();
                } else if (visibleItems.length > 0) {
                    visibleItems[0].click();
                }
            }
            
            if (e.key === 'Escape') {
                dropdown.classList.add('hidden');
            }
        });
    }

    filterProductOptions(searchText) {
        const productList = document.getElementById('product-list');
        const noResults = document.getElementById('no-products-found');
        const items = productList.querySelectorAll('.product-item');
        
        const filter = searchText.toLowerCase().trim();
        let hasResults = false;
        
        items.forEach(item => {
            const jina = (item.dataset.jina || '').toLowerCase();
            const aina = (item.dataset.aina || '').toLowerCase();
            const kipimo = (item.dataset.kipimo || '').toLowerCase();
            const barcode = (item.dataset.barcode || '').toLowerCase();
            
            const matches = filter === '' || 
                           jina.includes(filter) || 
                           aina.includes(filter) || 
                           kipimo.includes(filter) || 
                           barcode.includes(filter);
            
            item.style.display = matches ? '' : 'none';
            if (matches) hasResults = true;
        });
        
        if (noResults) {
            noResults.classList.toggle('hidden', hasResults);
        }
        
        items.forEach(el => el.classList.remove('selected'));
    }

    updateProductDetailsFromData(data) {
        const retailPrice = parseFloat(data.beiRejareja) || 0;
        const wholesalePrice = parseFloat(data.beiJumla) || 0;
        const stock = parseFloat(data.stock) || 0;
        
        const priceInput = document.getElementById('price-input');
        const stockInput = document.getElementById('stock-input');
        const quantityInput = document.getElementById('quantity-input');
        const priceTypeSelect = document.getElementById('price-type-select');
        
        if (priceInput) priceInput.value = retailPrice.toFixed(2);
        if (stockInput) stockInput.value = stock.toFixed(2);
        if (quantityInput) quantityInput.value = '';
        
        const hasWholesale = wholesalePrice > 0 && wholesalePrice !== retailPrice;
        const jumlaOption = priceTypeSelect?.querySelector('option[value="jumla"]');
        if (jumlaOption) {
            jumlaOption.disabled = !hasWholesale;
            jumlaOption.title = hasWholesale ? '' : 'Bei ya jumla haijapatikana kwa bidhaa hii';
        }
        
        if (priceTypeSelect) {
            if (retailPrice === 0 && wholesalePrice > 0) {
                priceTypeSelect.value = 'jumla';
                if (priceInput) priceInput.value = wholesalePrice.toFixed(2);
            } else {
                priceTypeSelect.value = 'rejareja';
            }
        }
        
        const kopeshaBidhaaId = document.getElementById('kopesha-bidhaa-id');
        const kopeshaBei = document.getElementById('kopesha-bei');
        if (kopeshaBidhaaId) kopeshaBidhaaId.value = data.id;
        if (kopeshaBei) kopeshaBei.value = priceInput?.value || '0';
        
        this.updateTotals();
    }

    // ================================================================
    // CART BADGES
    // ================================================================

    updateCartBadges() {
        const totalItems = this.carts.reduce((sum, cart) => sum + this.getCartItemCount(cart), 0);
        
        document.querySelectorAll('#kikapu-badge, #kikapu-badge-btn').forEach(badge => {
            if (totalItems > 0) {
                badge.textContent = totalItems;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        });
    }

    // ================================================================
    // EXISTING METHODS
    // ================================================================

    clearOtherCompanyCarts() {
        const keysToRemove = [];
        for (let i = 0; i < localStorage.length; i++) {
            const key = localStorage.key(i);
            if (key.startsWith('mauzo_cart_') && key !== this.cartKey) {
                keysToRemove.push(key);
            }
        }
        keysToRemove.forEach(key => localStorage.removeItem(key));
    }

    restoreTabState() {
        const savedTab = localStorage.getItem('currentMauzoTab');
        this.currentTab = savedTab || 'sehemu';
        setTimeout(() => {
            this.showTab(this.currentTab, true);
        }, 50);
    }

    showTab(tabName, isRestore = false) {
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('active');
        });
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.remove('active');
            content.classList.add('hidden');
        });
        
        const activeTab = document.getElementById(`${tabName}-tab`);
        const activeContent = document.getElementById(`${tabName}-tab-content`);
        
        if (activeTab && activeContent) {
            activeTab.classList.add('active');
            activeContent.classList.add('active');
            activeContent.classList.remove('hidden');
        }

        if (!isRestore) {
            localStorage.setItem('currentMauzoTab', tabName);
            this.currentTab = tabName;
        }
        
        if (tabName === 'barcode') {
            setTimeout(() => {
                const firstBarcodeInput = document.querySelector('.barcode-input');
                if (firstBarcodeInput) firstBarcodeInput.focus();
            }, 100);
        }
        
        if (tabName === 'risiti') {
            setTimeout(() => {
                const searchInput = document.getElementById('search-receipt-input');
                if (searchInput) searchInput.focus();
            }, 100);
        }
        
        if (this.isKikapuViewOpen) {
            this.closeKikapuView();
        }
    }

    bindEvents() {
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', (e) => {
                const tab = e.target.closest('.tab-button').dataset.tab;
                if (tab) this.showTab(tab);
            });
        });

        document.getElementById('toggle-kikapu-view')?.addEventListener('click', () => {
            if (this.isKikapuViewOpen) {
                this.closeKikapuView();
            } else {
                this.openKikapuView();
            }
        });

        document.getElementById('show-kikapu-btn')?.addEventListener('click', () => {
            this.openKikapuView();
        });

        document.getElementById('close-kikapu-workspace')?.addEventListener('click', () => {
            this.closeKikapuView();
        });

        document.getElementById('add-to-cart-btn')?.addEventListener('click', () => {
            this.addToCart();
        });

        document.getElementById('kopesha-btn')?.addEventListener('click', () => {
            const priceTypeSelect = document.getElementById('price-type-select');
            const beiTypeInput = document.getElementById('kopesha-bei-type');
            if (beiTypeInput && priceTypeSelect) {
                beiTypeInput.value = priceTypeSelect.value;
            }
            this.openKopeshaModal('regular');
        });

        const salesForm = document.getElementById('sales-form');
        if (salesForm) {
            salesForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                
                const hasDoubleSale = await this.checkDoubleSale();
                if (hasDoubleSale) {
                    this.pendingSaleData = { form: salesForm, type: 'regular' };
                    this.showDoubleSaleModal();
                    return;
                }
                
                await this.processSale(salesForm, 'regular');
            });
        }

        this.bindBarcodeEvents();
        this.bindModalEvents();
        this.bindSearchEvents();
        this.bindDeleteSaleEvents();
        this.initReceiptLookup();
        this.bindSmsReceiptEvents();
        this.bindPaymentTypeEvents();
        this.initBackdateToggle();

        document.getElementById('quantity-input')?.addEventListener('input', () => this.updateTotals());
        document.getElementById('punguzo-input')?.addEventListener('input', () => this.updateTotals());
        document.getElementById('punguzo-type')?.addEventListener('change', () => this.updateTotals());

        document.getElementById('cancel-confirm-sale')?.addEventListener('click', () => {
            document.getElementById('confirm-sale-modal').classList.add('hidden');
            this.pendingConfirmAction = null;
        });
        document.getElementById('close-confirm-sale-modal')?.addEventListener('click', () => {
            document.getElementById('confirm-sale-modal').classList.add('hidden');
            this.pendingConfirmAction = null;
        });
        document.getElementById('proceed-confirm-sale')?.addEventListener('click', () => {
            if (this.pendingConfirmAction === 'checkout') {
                this.proceedCheckout();
            }
        });

        document.getElementById('cancel-remove-item')?.addEventListener('click', () => {
            document.getElementById('remove-cart-item-modal').classList.add('hidden');
            this.pendingCartItemRemove = null;
        });
        document.getElementById('confirm-remove-item')?.addEventListener('click', () => {
            this.confirmRemoveFromCart();
        });

        document.getElementById('cancel-clear-cart')?.addEventListener('click', () => {
            document.getElementById('clear-cart-modal').classList.add('hidden');
            this.pendingCartClear = null;
        });
        document.getElementById('confirm-clear-cart')?.addEventListener('click', () => {
            this.confirmClearCart();
        });

        document.getElementById('cancel-delete-cart')?.addEventListener('click', () => {
            document.getElementById('delete-cart-modal').classList.add('hidden');
            this.pendingCartDelete = null;
        });
        document.getElementById('confirm-delete-cart')?.addEventListener('click', () => {
            this.confirmDeleteCart();
        });

        document.getElementById('close-kikapu-kopesha-modal')?.addEventListener('click', () => {
            document.getElementById('kikapu-kopesha-modal').classList.add('hidden');
        });
        document.getElementById('cancel-kikapu-kopesha')?.addEventListener('click', () => {
            document.getElementById('kikapu-kopesha-modal').classList.add('hidden');
        });
        document.getElementById('kikapu-kopesha-form')?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.submitKikapuKopeshaForm(e.target);
        });
    }

    updateProductDetails() {
        const bidhaaId = document.getElementById('bidhaaSelect').value;
        if (!bidhaaId) return;
        
        const product = this.bidhaaList.find(p => p.id == bidhaaId);
        if (!product) return;
        
        const retailPrice = parseFloat(product.bei_kuuza) || 0;
        const wholesalePrice = parseFloat(product.bei_uzo_jumla) || 0;
        const stock = parseFloat(product.idadi) || 0;
        
        const priceInput = document.getElementById('price-input');
        const stockInput = document.getElementById('stock-input');
        const priceTypeSelect = document.getElementById('price-type-select');
        
        if (priceInput) priceInput.value = retailPrice.toFixed(2);
        if (stockInput) stockInput.value = stock.toFixed(2);
        
        const hasWholesale = wholesalePrice > 0 && wholesalePrice !== retailPrice;
        const jumlaOption = priceTypeSelect?.querySelector('option[value="jumla"]');
        if (jumlaOption) {
            jumlaOption.disabled = !hasWholesale;
            jumlaOption.title = hasWholesale ? '' : 'Bei ya jumla haijapatikana kwa bidhaa hii';
        }
        
        if (priceTypeSelect) {
            if (retailPrice === 0 && wholesalePrice > 0) {
                priceTypeSelect.value = 'jumla';
                if (priceInput) priceInput.value = wholesalePrice.toFixed(2);
            } else {
                priceTypeSelect.value = 'rejareja';
            }
        }
        
        this.updateTotals();
    }

    updateTotals() {
        const quantity = parseFloat(document.getElementById('quantity-input')?.value) || 0;
        const price = parseFloat(document.getElementById('price-input')?.value) || 0;
        const discount = parseFloat(document.getElementById('punguzo-input')?.value) || 0;
        const discountType = document.getElementById('punguzo-type')?.value || 'bidhaa';
        const totalInput = document.getElementById('total-input');
        const punguzoAinaInput = document.getElementById('punguzo-aina-input');
        
        const baseTotal = quantity * price;
        const actualDiscount = discountType === 'bidhaa' ? discount * quantity : discount;
        const finalTotal = Math.max(0, baseTotal - actualDiscount);
        
        if (totalInput) totalInput.value = finalTotal.toFixed(2);
        if (punguzoAinaInput) punguzoAinaInput.value = discountType;
        
        document.getElementById('kopesha-idadi').value = quantity.toFixed(2);
        document.getElementById('kopesha-jumla').value = finalTotal.toFixed(2);
        document.getElementById('kopesha-baki').value = finalTotal.toFixed(2);
        document.getElementById('kopesha-punguzo').value = discount.toFixed(2);
        document.getElementById('kopesha-punguzo-aina').value = discountType;
        document.getElementById('kopesha-bei').value = price.toFixed(2);
    }

    resetForm() {
        const salesForm = document.getElementById('sales-form');
        if (!salesForm) return;

        salesForm.reset();
        
        document.getElementById('bidhaaSearch').value = '';
        document.getElementById('bidhaaSelect').value = '';
        document.getElementById('price-input').value = '';
        document.getElementById('stock-input').value = '';
        document.getElementById('total-input').value = '';
        document.getElementById('quantity-input').value = '';
        document.getElementById('punguzo-input').value = 0;
        document.getElementById('punguzo-type').value = 'bidhaa';
        document.getElementById('customer_search').value = '';
        document.getElementById('customer_select').value = '';
        document.getElementById('selected_customer_id').value = '';
        document.getElementById('send_receipt_checkbox').checked = false;
        document.getElementById('send_receipt_hidden').value = '0';
        document.getElementById('send_to_phone_hidden').value = '';
        
        document.querySelectorAll('#product-list .product-item').forEach(el => el.classList.remove('selected'));
    }

    bindPriceTypeChange() {
        const priceTypeSelect = document.getElementById('price-type-select');
        if (!priceTypeSelect) return;
        
        priceTypeSelect.addEventListener('change', () => {
            const bidhaaId = document.getElementById('bidhaaSelect').value;
            if (!bidhaaId) return;
            
            const product = this.bidhaaList.find(p => p.id == bidhaaId);
            if (!product) return;
            
            const retailPrice = parseFloat(product.bei_kuuza) || 0;
            const wholesalePrice = parseFloat(product.bei_uzo_jumla) || 0;
            const priceInput = document.getElementById('price-input');
            
            if (priceTypeSelect.value === 'jumla' && wholesalePrice > 0) {
                priceInput.value = wholesalePrice.toFixed(2);
            } else {
                priceInput.value = retailPrice.toFixed(2);
            }
            
            document.getElementById('kopesha-bei').value = priceInput.value;
            document.getElementById('kopesha-bei-type').value = priceTypeSelect.value;
            this.updateTotals();
        });
    }

    bindPaymentTypeEvents() {
        const lipaKwaSelect = document.getElementById('lipa_kwa_select');
        const lipaNambaContainer = document.getElementById('lipa_namba_type_container');
        const bankContainer = document.getElementById('bank_type_container');
        const lipaNambaSelect = document.getElementById('lipa_namba_type_select');
        const bankSelect = document.getElementById('bank_type_select');
        
        if (lipaKwaSelect) {
            lipaKwaSelect.addEventListener('change', (e) => {
                const value = e.target.value;
                if (lipaNambaContainer) lipaNambaContainer.classList.add('hidden');
                if (bankContainer) bankContainer.classList.add('hidden');
                
                if (value === 'lipa_namba') {
                    if (lipaNambaContainer) lipaNambaContainer.classList.remove('hidden');
                    if (lipaNambaSelect) lipaNambaSelect.required = true;
                    if (bankSelect) bankSelect.required = false;
                } else if (value === 'bank') {
                    if (bankContainer) bankContainer.classList.remove('hidden');
                    if (bankSelect) bankSelect.required = true;
                    if (lipaNambaSelect) lipaNambaSelect.required = false;
                } else {
                    if (lipaNambaSelect) lipaNambaSelect.required = false;
                    if (bankSelect) bankSelect.required = false;
                }
            });
        }
        
        const barcodeLipaKwaSelect = document.getElementById('barcode_lipa_kwa_select');
        const barcodeLipaNambaContainer = document.getElementById('barcode_lipa_namba_container');
        const barcodeBankContainer = document.getElementById('barcode_bank_container');
        const barcodeLipaNambaSelect = document.getElementById('barcode_lipa_namba_select');
        const barcodeBankSelect = document.getElementById('barcode_bank_select');
        
        if (barcodeLipaKwaSelect) {
            barcodeLipaKwaSelect.addEventListener('change', (e) => {
                const value = e.target.value;
                if (barcodeLipaNambaContainer) barcodeLipaNambaContainer.classList.add('hidden');
                if (barcodeBankContainer) barcodeBankContainer.classList.add('hidden');
                
                if (value === 'lipa_namba') {
                    if (barcodeLipaNambaContainer) barcodeLipaNambaContainer.classList.remove('hidden');
                    if (barcodeLipaNambaSelect) barcodeLipaNambaSelect.required = true;
                    if (barcodeBankSelect) barcodeBankSelect.required = false;
                } else if (value === 'bank') {
                    if (barcodeBankContainer) barcodeBankContainer.classList.remove('hidden');
                    if (barcodeBankSelect) barcodeBankSelect.required = true;
                    if (barcodeLipaNambaSelect) barcodeLipaNambaSelect.required = false;
                } else {
                    if (barcodeLipaNambaSelect) barcodeLipaNambaSelect.required = false;
                    if (barcodeBankSelect) barcodeBankSelect.required = false;
                }
            });
        }
    }

    initBackdateToggle() {
        const backdateCheckbox = document.getElementById('backdate_checkbox');
        const backdateContainer = document.getElementById('backdate_container');
        const backdateDate = document.getElementById('backdate_date');
        
        if (backdateCheckbox && backdateContainer) {
            backdateCheckbox.addEventListener('change', (e) => {
                if (e.target.checked) {
                    backdateContainer.classList.remove('hidden');
                    if (backdateDate && !backdateDate.value) {
                        const yesterday = new Date();
                        yesterday.setDate(yesterday.getDate() - 1);
                        backdateDate.value = yesterday.toISOString().split('T')[0];
                    }
                } else {
                    backdateContainer.classList.add('hidden');
                    if (backdateDate) backdateDate.value = '';
                }
            });
        }
        
        const barcodeBackdateCheckbox = document.getElementById('barcode_backdate_checkbox');
        const barcodeBackdateContainer = document.getElementById('barcode_backdate_container');
        const barcodeBackdateDate = document.getElementById('barcode_backdate_date');
        
        if (barcodeBackdateCheckbox && barcodeBackdateContainer) {
            barcodeBackdateCheckbox.addEventListener('change', (e) => {
                if (e.target.checked) {
                    barcodeBackdateContainer.classList.remove('hidden');
                    if (barcodeBackdateDate && !barcodeBackdateDate.value) {
                        const yesterday = new Date();
                        yesterday.setDate(yesterday.getDate() - 1);
                        barcodeBackdateDate.value = yesterday.toISOString().split('T')[0];
                    }
                } else {
                    barcodeBackdateContainer.classList.add('hidden');
                    if (barcodeBackdateDate) barcodeBackdateDate.value = '';
                }
            });
        }
    }

    initCustomerSelection() {
        const kopeshaSelect = document.getElementById('kopesha-mteja-select');
        const barcodeSelect = document.getElementById('barcode-mteja-select');
        
        if (kopeshaSelect) {
            kopeshaSelect.addEventListener('change', (e) => {
                this.handleCustomerSelection(e.target, 'kopesha-mteja-select');
            });
        }
        
        if (barcodeSelect) {
            barcodeSelect.addEventListener('change', (e) => {
                this.handleCustomerSelection(e.target, 'barcode-mteja-select');
            });
        }
    }

    handleCustomerSelection(selectElement, modalType) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        let prefix = '';
        let hiddenFieldId = '';
        
        if (modalType === 'kopesha-mteja-select') {
            prefix = 'kopesha-';
            hiddenFieldId = 'kopesha-mteja-id';
        } else if (modalType === 'barcode-mteja-select') {
            prefix = 'barcode-kopesha-';
            hiddenFieldId = 'barcode-kopesha-mteja-id';
        }
        
        const hiddenField = document.getElementById(hiddenFieldId);
        if (hiddenField) {
            hiddenField.value = selectedOption.value === "" ? "" : selectedOption.value;
        }
        
        if (selectedOption.value === "") {
            this.clearCustomerFields(modalType);
        } else {
            const jina = selectedOption.dataset.jina || '';
            const simu = selectedOption.dataset.simu || '';
            const baruaPepe = selectedOption.dataset.barua_pepe || '';
            const anapoishi = selectedOption.dataset.anapoishi || '';
            this.populateCustomerFields(modalType, jina, simu, baruaPepe, anapoishi);
        }
    }

    clearCustomerFields(modalType) {
        let prefix = '';
        if (modalType === 'kopesha-mteja-select') prefix = 'kopesha-';
        else if (modalType === 'barcode-mteja-select') prefix = 'barcode-kopesha-';
        
        ['jina', 'simu', 'barua-pepe', 'anapoishi'].forEach(field => {
            const element = document.getElementById(`${prefix}${field}`);
            if (element) {
                element.value = '';
                if (field === 'jina' || field === 'simu') element.required = true;
            }
        });
    }

    populateCustomerFields(modalType, jina, simu, baruaPepe, anapoishi) {
        let prefix = '';
        if (modalType === 'kopesha-mteja-select') prefix = 'kopesha-';
        else if (modalType === 'barcode-mteja-select') prefix = 'barcode-kopesha-';
        
        const jinaField = document.getElementById(`${prefix}jina`);
        const simuField = document.getElementById(`${prefix}simu`);
        const baruaPepeField = document.getElementById(`${prefix}barua-pepe`);
        const anapoishiField = document.getElementById(`${prefix}anapoishi`);
        
        if (jinaField) { jinaField.value = jina; jinaField.required = false; }
        if (simuField) { simuField.value = simu; simuField.required = false; }
        if (baruaPepeField) baruaPepeField.value = baruaPepe;
        if (anapoishiField) anapoishiField.value = anapoishi;
    }

    openKopeshaModal(type) {
        this.currentKopeshaType = type;
        
        let isValid = false;
        let modalId = '';
        
        if (type === 'regular') {
            modalId = 'kopesha-modal';
            const bidhaaId = document.getElementById('bidhaaSelect')?.value;
            const quantity = document.getElementById('quantity-input')?.value;
            
            if (bidhaaId && quantity && parseFloat(quantity) > 0) {
                isValid = true;
                this.clearCustomerFields('kopesha-mteja-select');
            } else {
                this.showNotification('Tafadhali chagua bidhaa na idadi sahihi!', 'error');
                return;
            }
        } else if (type === 'barcode') {
            modalId = 'kopesha-barcode-modal';
            const rows = document.querySelectorAll('.barcode-row');
            let validRows = false;
            
            rows.forEach(row => {
                const barcode = row.querySelector('.barcode-input')?.value.trim();
                const productName = row.querySelector('.product-name')?.value.trim();
                const quantity = row.querySelector('.quantity-input')?.value;
                
                if (barcode && productName && quantity && parseFloat(quantity) > 0) {
                    validRows = true;
                }
            });
            
            if (validRows) {
                isValid = true;
                this.prepareBarcodeKopeshaData();
                this.clearCustomerFields('barcode-mteja-select');
            } else {
                this.showNotification('Tafadhali ingiza angalau bidhaa moja kwa usahihi!', 'error');
                return;
            }
        }

        if (isValid && modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                const tomorrow = new Date();
                tomorrow.setDate(tomorrow.getDate() + 1);
                const dateInput = modal.querySelector('input[name="tarehe_malipo"]');
                if (dateInput) {
                    dateInput.value = tomorrow.toISOString().split('T')[0];
                }
                modal.classList.remove('hidden');
            }
        }
    }

    prepareBarcodeKopeshaData() {
        const items = [];
        const punguzoType = document.getElementById('punguzo-type');
        const discountType = punguzoType ? punguzoType.value : 'bidhaa';
        
        document.querySelectorAll('.barcode-row').forEach(row => {
            const barcodeInput = row.querySelector('.barcode-input');
            const quantityInput = row.querySelector('.quantity-input');
            const productName = row.querySelector('.product-name');
            const productPrice = row.querySelector('.product-price');
            const punguzoInput = row.querySelector('.punguzo-input');
            const totalInput = row.querySelector('.total-input');
            
            if (barcodeInput && quantityInput && productName && productPrice && totalInput) {
                const barcode = barcodeInput.value.trim();
                const quantity = parseFloat(quantityInput.value) || 0;
                const product = productName.value.trim();
                const price = parseFloat(productPrice.value) || 0;
                const punguzo = parseFloat(punguzoInput.value) || 0;
                const jumla = parseFloat(totalInput.value) || 0;
                
                if (barcode && quantity > 0 && product && price > 0) {
                    const bidhaa = this.bidhaaList.find(b => b.barcode === barcode);
                    
                    items.push({
                        barcode: barcode,
                        bidhaa_id: bidhaa ? bidhaa.id : null,
                        idadi: quantity,
                        jina: product,
                        bei: price,
                        punguzo: punguzo,
                        punguzo_aina: discountType,
                        jumla: jumla,
                        total_before_discount: (price * quantity),
                        company_id: this.companyId
                    });
                }
            }
        });
        
        if (items.length > 0) {
            document.getElementById('barcode-items-data').value = JSON.stringify(items);
        }
    }

    submitKopeshaForm(form, type) {
        const formData = new FormData(form);
        
        if (type === 'barcode') {
            const itemsData = document.getElementById('barcode-items-data').value;
            if (itemsData) {
                formData.append('items', itemsData);
            }
        }
        
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.showNotification('Deni limerekodiwa kikamilifu!', 'success');
                this.closeModal(type === 'regular' ? 'kopesha-modal' : 'kopesha-barcode-modal');
                
                if (type === 'regular') {
                    this.resetForm();
                } else if (type === 'barcode') {
                    this.clearBarcodeRows();
                }
                
                this.updateFinancialData();
                setTimeout(() => { refreshFinancialSoft(); refreshSalesTableSoft(); }, 600);
            } else {
                this.showNotification(data.message || 'Kuna tatizo kwenye kuhifadhi!', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            this.showNotification('Kuna tatizo kwenye kuhifadhi!', 'error');
        });
    }

    bindModalEvents() {
        const closeModal = (modalId) => {
            const modal = document.getElementById(modalId);
            if (modal) modal.classList.add('hidden');
        };

        document.getElementById('close-kopesha-modal')?.addEventListener('click', () => closeModal('kopesha-modal'));
        document.getElementById('cancel-kopesha')?.addEventListener('click', () => closeModal('kopesha-modal'));
        document.getElementById('kopesha-form')?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.submitKopeshaForm(e.target, 'regular');
        });

        document.getElementById('close-kopesha-barcode-modal')?.addEventListener('click', () => closeModal('kopesha-barcode-modal'));
        document.getElementById('cancel-kopesha-barcode')?.addEventListener('click', () => closeModal('kopesha-barcode-modal'));
        document.getElementById('kopesha-barcode-form')?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.submitKopeshaForm(e.target, 'barcode');
        });

        document.getElementById('close-double-sale-modal')?.addEventListener('click', () => {
            document.getElementById('double-sale-modal').classList.add('hidden');
            this.pendingSaleData = null;
        });
        document.getElementById('cancel-double-sale')?.addEventListener('click', () => {
            document.getElementById('double-sale-modal').classList.add('hidden');
            this.pendingSaleData = null;
        });
        document.getElementById('confirm-double-sale')?.addEventListener('click', async () => {
            document.getElementById('double-sale-modal').classList.add('hidden');
            if (this.pendingSaleData) {
                await this.processSale(this.pendingSaleData.form, this.pendingSaleData.type);
                this.pendingSaleData = null;
            }
        });

        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal || e.target.classList.contains('modal-overlay')) {
                    modal.classList.add('hidden');
                    if (modal.id === 'double-sale-modal') this.pendingSaleData = null;
                    if (modal.id === 'confirm-sale-modal') this.pendingConfirmAction = null;
                    if (modal.id === 'kikapu-kopesha-modal') this.pendingCartKopesha = null;
                }
            });
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal:not(.hidden)').forEach(modal => {
                    modal.classList.add('hidden');
                    if (modal.id === 'double-sale-modal') this.pendingSaleData = null;
                    if (modal.id === 'confirm-sale-modal') this.pendingConfirmAction = null;
                    if (modal.id === 'kikapu-kopesha-modal') this.pendingCartKopesha = null;
                });
            }
        });
    }

    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) modal.classList.add('hidden');
    }

    async processSale(form, type) {
        const formData = new FormData(form);
        
        const backdateCheckbox = document.getElementById('backdate_checkbox');
        const backdateDate = document.getElementById('backdate_date');
        if (backdateCheckbox && backdateCheckbox.checked && backdateDate && backdateDate.value) {
            formData.append('is_backdate', '1');
            formData.append('backdate', backdateDate.value);
        }
        
        const punguzoType = document.getElementById('punguzo-type');
        if (punguzoType) formData.append('punguzo_aina', punguzoType.value);
        
        const priceTypeSelect = document.getElementById('price-type-select');
        if (priceTypeSelect) formData.append('bei_type', priceTypeSelect.value);
        
        const lipaKwaSelect = document.getElementById('lipa_kwa_select');
        const paymentMethod = lipaKwaSelect ? lipaKwaSelect.value : 'cash';
        
        if (paymentMethod === 'lipa_namba') {
            const typeSelect = document.getElementById('lipa_namba_type_select');
            if (typeSelect && typeSelect.value) formData.append('lipa_kwa_type', typeSelect.value);
        } else if (paymentMethod === 'bank') {
            const typeSelect = document.getElementById('bank_type_select');
            if (typeSelect && typeSelect.value) formData.append('lipa_kwa_type', typeSelect.value);
        }

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            });

            let data = {};
            try {
                data = await response.json();
            } catch (jsonError) {
                console.warn("Response is not JSON");
            }

            if (response.ok) {
                this.showNotification('Mauzo yamehifadhiwa kikamilifu! Namba ya risiti: ' + (data.receipt_no || 'N/A'), 'success');
                this.syncStock(formData.get('bidhaa_id'), formData.get('idadi'));
                this.resetForm();
                setTimeout(() => { refreshFinancialSoft(); refreshSalesTableSoft(); }, 400);
            } else {
                this.showNotification(data.message || 'Kuna tatizo kwenye kuhifadhi!', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            this.showNotification('Kuna tatizo kwenye kuhifadhi!', 'error');
        }
    }

    async checkDoubleSale() {
        const bidhaaId = document.getElementById('bidhaaSelect').value;
        if (!bidhaaId) return false;

        try {
            const response = await fetch(`/mauzo/check-double-sale/${bidhaaId}`);
            const data = await response.json();
            return data.success && data.recent_sale;
        } catch (error) {
            console.error('Error checking double sale:', error);
            return false;
        }
    }

    showDoubleSaleModal() {
        const modal = document.getElementById('double-sale-modal');
        const productNameSpan = document.getElementById('double-sale-product-name');
        const searchValue = document.getElementById('bidhaaSearch').value;
        const productName = searchValue.split(' - ')[0] || 'bidhaa hii';
        
        if (!modal) return;
        productNameSpan.textContent = productName;
        modal.classList.remove('hidden');
    }

    updateStockDisplay(bidhaaId) {
        const quantity = parseFloat(document.getElementById('quantity-input').value) || 0;
        const stockInput = document.getElementById('stock-input');
        const currentStock = parseFloat(stockInput.value) || 0;
        stockInput.value = (currentStock - quantity).toFixed(2);
    }

    // Smooth stock sync — update bidhaaList and dropdown without reload
    syncStock(bidhaaId, qtySold){
        const prod = this.bidhaaList.find(p=> String(p.id)===String(bidhaaId));
        if(prod){
            prod.idadi = Math.max(0, (parseFloat(prod.idadi)||0) - (parseFloat(qtySold)||0));
            // update dropdown item data-stock
            const item = document.querySelector(`#product-list .product-item[data-id="${bidhaaId}"]`);
            if(item) item.dataset.stock = prod.idadi;
        }
        // if currently selected product is the sold one, update stock input
        const selId = document.getElementById('bidhaaSelect')?.value;
        if(String(selId)===String(bidhaaId)){
            const stockInput=document.getElementById('stock-input');
            if(stockInput && prod) stockInput.value = (parseFloat(prod.idadi)||0).toFixed(2);
        }
    }
    syncStocksForItems(items){
        // items: array of {bidhaa_id, idadi}
        (items||[]).forEach(it=>{
            const id = it.bidhaa_id || it.id || it.bidhaaId;
            const qty = it.idadi || it.qty || it.quantity || 0;
            if(id) this.syncStock(id, qty);
        });
    }

    updateFinancialData() {
        fetch('/mauzo/financial-data', {
            method: 'GET',
            headers: { 'Accept': 'application/json' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Page will be reloaded anyway
            }
        })
        .catch(error => console.error('Error updating financial data:', error));
    }

    // ================================================================
    // BARCODE FUNCTIONS
    // ================================================================

    initBarcodeRows() {
        document.querySelectorAll('.barcode-row').forEach(row => {
            this.addBarcodeRowEvents(row);
        });
    }

    addBarcodeRow() {
        const tbody = document.getElementById('barcode-tbody');
        if (!tbody) return;

        const newRow = document.createElement('tr');
        newRow.className = 'barcode-row hover:bg-gray-50 transition-colors';
        newRow.innerHTML = `
            <td class="px-3 py-2">
                <input type="text" name="barcode[]" placeholder="Scan barcode" 
                       class="barcode-input border border-gray-300 focus:border-green-500 focus:ring-2 focus:ring-green-200 rounded-lg p-2 w-full text-sm transition-all" 
                       autocomplete="off" />
            </td>
            <td class="px-3 py-2">
                <input type="text" name="jina[]" readonly placeholder="Jina la Bidhaa" 
                       class="product-name border border-gray-200 bg-gray-50 text-gray-700 rounded-lg p-2 w-full text-sm" />
            </td>
            <td class="px-3 py-2">
                <input type="number" name="bei[]" readonly placeholder="Bei" step="0.01" value="0.00"
                       class="product-price border border-gray-200 bg-gray-50 text-gray-700 rounded-lg p-2 w-full text-sm" />
            </td>
            <td class="px-3 py-2">
                <input type="number" name="idadi[]" min="0.01" step="0.01" value="1.00" placeholder="Idadi" 
                       class="quantity-input border border-gray-300 focus:border-green-500 focus:ring-2 focus:ring-green-200 rounded-lg p-2 w-full text-sm" />
            </td>
            <td class="px-3 py-2">
                <input type="number" name="stock[]" readonly placeholder="Baki" step="0.01" value="0.00"
                       class="stock-input border border-gray-200 bg-gray-50 text-gray-700 rounded-lg p-2 w-full text-sm" />
            </td>
            <td class="px-3 py-2">
                <input type="number" name="punguzo[]" min="0" value="0.00" placeholder="Punguzo" step="0.01"
                       class="punguzo-input border border-gray-300 rounded-lg p-2 w-full text-sm" />
            </td>
            <td class="px-3 py-2">
                <input type="number" name="jumla[]" readonly placeholder="Jumla" step="0.01" value="0.00"
                       class="total-input border border-gray-200 bg-gray-50 text-gray-700 rounded-lg p-2 w-full text-sm font-semibold" />
            </td>
            <td class="px-3 py-2 text-center">
                <button type="button" class="remove-barcode-row text-red-500 hover:text-red-700 p-2 rounded-full transition transform hover:scale-110" title="Futa bidhaa">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        
        tbody.appendChild(newRow);
        this.addBarcodeRowEvents(newRow);
        
        const newBarcodeInput = newRow.querySelector('.barcode-input');
        if (newBarcodeInput) {
            setTimeout(() => newBarcodeInput.focus(), 100);
        }
    }

    addBarcodeRowEvents(row) {
        const barcodeInput = row.querySelector('.barcode-input');
        const quantityInput = row.querySelector('.quantity-input');
        const punguzoInput = row.querySelector('.punguzo-input');
        const removeBtn = row.querySelector('.remove-barcode-row');

        if (barcodeInput) {
            barcodeInput.addEventListener('input', (e) => {
                if (this.barcodeScanTimeout) clearTimeout(this.barcodeScanTimeout);
                this.barcodeScanTimeout = setTimeout(() => {
                    const value = e.target.value.trim();
                    if (value.length >= 8) this.fetchBidhaaByBarcode(e.target);
                }, 300);
            });
            
            barcodeInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const value = barcodeInput.value.trim();
                    if (value.length >= 8) this.fetchBidhaaByBarcode(barcodeInput);
                }
            });
        }

        if (quantityInput) {
            quantityInput.addEventListener('input', () => this.updateBarcodeRowTotal(row));
            quantityInput.addEventListener('blur', () => {
                const val = parseFloat(quantityInput.value);
                if (isNaN(val) || val < 0.01) {
                    quantityInput.value = '1.00';
                    this.updateBarcodeRowTotal(row);
                }
            });
        }

        if (punguzoInput) {
            punguzoInput.addEventListener('input', () => this.updateBarcodeRowTotal(row));
            punguzoInput.addEventListener('blur', () => {
                const val = parseFloat(punguzoInput.value);
                if (isNaN(val) || val < 0) {
                    punguzoInput.value = '0.00';
                    this.updateBarcodeRowTotal(row);
                }
            });
        }

        if (removeBtn) {
            removeBtn.addEventListener('click', () => {
                const rows = document.querySelectorAll('.barcode-row');
                if (rows.length > 1) {
                    this.showNotification('Bidhaa imeondolewa!', 'success');
                    row.remove();
                    this.updateBarcodeTotal();
                } else {
                    this.clearBarcodeRow(row);
                    this.showNotification('Huwezi kufuta safu ya mwisho!', 'warning');
                }
            });
        }
    }

    clearBarcodeRow(row) {
        row.querySelector('.barcode-input').value = '';
        row.querySelector('.product-name').value = '';
        row.querySelector('.product-price').value = '0.00';
        row.querySelector('.stock-input').value = '0.00';
        row.querySelector('.quantity-input').value = '1.00';
        row.querySelector('.punguzo-input').value = '0.00';
        row.querySelector('.total-input').value = '0.00';
        this.updateBarcodeTotal();
        setTimeout(() => row.querySelector('.barcode-input').focus(), 100);
    }

    clearBarcodeRows() {
        const tbody = document.getElementById('barcode-tbody');
        if (!tbody) return;
        
        const rows = tbody.querySelectorAll('.barcode-row');
        for (let i = rows.length - 1; i > 0; i--) rows[i].remove();
        const firstRow = tbody.querySelector('.barcode-row');
        if (firstRow) this.clearBarcodeRow(firstRow);
        this.updateBarcodeTotal();
        setTimeout(() => document.querySelector('.barcode-input')?.focus(), 100);
    }

    updateBarcodeRowTotal(row) {
        const productPrice = row.querySelector('.product-price');
        const quantityInput = row.querySelector('.quantity-input');
        const punguzoInput = row.querySelector('.punguzo-input');
        const stockInput = row.querySelector('.stock-input');
        const totalInput = row.querySelector('.total-input');
        const punguzoType = document.getElementById('punguzo-type');

        if (!productPrice || !quantityInput || !totalInput) return;

        const price = parseFloat(productPrice.value) || 0;
        let quantity = parseFloat(quantityInput.value) || 0;
        const punguzo = parseFloat(punguzoInput?.value) || 0;
        const stock = stockInput ? parseFloat(stockInput.value) || 0 : 0;
        const discountType = punguzoType ? punguzoType.value : 'bidhaa';
        
        if (quantity < 0.01) {
            quantity = 0.01;
            quantityInput.value = quantity.toFixed(2);
        }
        
        if (stock > 0 && quantity > stock) {
            this.showNotification(`Idadi inazidi stock (${stock.toFixed(2)})!`, 'warning');
            quantity = stock;
            quantityInput.value = stock.toFixed(2);
        }
        
        const baseTotal = price * quantity;
        let actualDiscount = discountType === 'bidhaa' ? punguzo * quantity : punguzo;
        
        if (actualDiscount > baseTotal) {
            actualDiscount = baseTotal;
            if (discountType === 'jumla') {
                punguzoInput.value = baseTotal.toFixed(2);
            } else {
                punguzoInput.value = (baseTotal / quantity).toFixed(2);
            }
        }
        
        const total = baseTotal - actualDiscount;
        totalInput.value = Math.max(0, total).toFixed(2);
        this.updateBarcodeTotal();
    }

    updateBarcodeTotal() {
        const barcodeTotalElement = document.getElementById('barcode-total');
        if (!barcodeTotalElement) return;

        let grandTotal = 0;
        document.querySelectorAll('.barcode-row').forEach(row => {
            const totalInput = row.querySelector('.total-input');
            if (totalInput) grandTotal += parseFloat(totalInput.value) || 0;
        });
        
        barcodeTotalElement.textContent = grandTotal.toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }) + '/=';
    }

    fetchBidhaaByBarcode(input) {
        const barcode = input.value.trim();
        if (!barcode) return;

        const row = input.closest('.barcode-row');
        const productName = row.querySelector('.product-name');
        const productPrice = row.querySelector('.product-price');
        const stockInput = row.querySelector('.stock-input');
        const quantityInput = row.querySelector('.quantity-input');
        
        const priceTypeSelect = document.getElementById('price-type-select');
        const useWholesale = priceTypeSelect && priceTypeSelect.value === 'jumla';

        row.classList.add('opacity-50', 'pointer-events-none');
        
        fetch(`/mauzo/product-by-barcode/${encodeURIComponent(barcode)}`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            row.classList.remove('opacity-50', 'pointer-events-none');
            
            if (data.success && data.product) {
                const product = data.product;
                if (productName) productName.value = product.jina || '';
                
                let priceToUse = parseFloat(product.bei_kuuza) || 0;
                if (useWholesale && product.bei_uzo_jumla && parseFloat(product.bei_uzo_jumla) > 0) {
                    priceToUse = parseFloat(product.bei_uzo_jumla);
                }
                
                if (productPrice) productPrice.value = priceToUse.toFixed(2);
                if (stockInput) stockInput.value = (parseFloat(product.idadi) || 0).toFixed(2);
                if (quantityInput) {
                    const maxQty = parseFloat(product.idadi) || 1;
                    quantityInput.value = Math.min(1, maxQty).toFixed(2);
                }
                row.querySelector('.punguzo-input').value = '0.00';
                this.updateBarcodeRowTotal(row);
                
                row.classList.add('bg-green-50');
                setTimeout(() => row.classList.remove('bg-green-50'), 500);
                
                this.showNotification(`Bidhaa: ${product.jina} imeongezwa!`, 'success');
                setTimeout(() => this.addBarcodeRow(), 200);
            } else {
                this.clearBarcodeRow(row);
                this.showNotification(data.message || 'Bidhaa haipatikani!', 'error');
                row.classList.add('bg-red-50');
                setTimeout(() => row.classList.remove('bg-red-50'), 1000);
            }
        })
        .catch(error => {
            row.classList.remove('opacity-50', 'pointer-events-none');
            console.error('Error fetching product:', error);
            this.showNotification('Kuna tatizo kwenye kutafuta bidhaa!', 'error');
            this.clearBarcodeRow(row);
        });
    }

    bindBarcodeEvents() {
        document.getElementById('add-barcode-row')?.addEventListener('click', () => this.addBarcodeRow());
        document.getElementById('clear-barcode-form')?.addEventListener('click', () => {
            this.showNotification('Bidhaa zote zimefutwa!', 'success');
            this.clearBarcodeRows();
        });
        document.getElementById('kopesha-barcode-btn')?.addEventListener('click', () => this.openKopeshaModal('barcode'));
        
        document.getElementById('barcode-form')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            await this.submitBarcodeSales();
        });
        
        document.getElementById('punguzo-type')?.addEventListener('change', () => {
            document.querySelectorAll('.barcode-row').forEach(row => this.updateBarcodeRowTotal(row));
        });
    }

    async submitBarcodeSales() {
        const items = [];
        let hasValidItems = false;
        const punguzoType = document.getElementById('punguzo-type');
        const paymentMethodSelect = document.querySelector('#barcode-form select[name="lipa_kwa"]');
        const paymentMethod = paymentMethodSelect ? paymentMethodSelect.value : 'cash';
        const discountType = punguzoType ? punguzoType.value : 'bidhaa';

        let paymentType = null;
        if (paymentMethod === 'lipa_namba') {
            const typeSelect = document.getElementById('barcode_lipa_namba_select');
            if (typeSelect && typeSelect.value) {
                paymentType = typeSelect.value;
            } else {
                this.showNotification('Tafadhali chagua aina ya Lipa Namba', 'error');
                return;
            }
        } else if (paymentMethod === 'bank') {
            const typeSelect = document.getElementById('barcode_bank_select');
            if (typeSelect && typeSelect.value) {
                paymentType = typeSelect.value;
            } else {
                this.showNotification('Tafadhali chagua aina ya Benki', 'error');
                return;
            }
        }

        document.querySelectorAll('.barcode-row').forEach(row => {
            const barcodeInput = row.querySelector('.barcode-input');
            const quantityInput = row.querySelector('.quantity-input');
            const productName = row.querySelector('.product-name');
            const productPrice = row.querySelector('.product-price');
            const punguzoInput = row.querySelector('.punguzo-input');
            const totalInput = row.querySelector('.total-input');
            
            if (barcodeInput && quantityInput && productName && productPrice && totalInput) {
                const barcode = barcodeInput.value.trim();
                const quantity = parseFloat(quantityInput.value) || 0;
                const product = productName.value.trim();
                const price = parseFloat(productPrice.value) || 0;
                const punguzo = parseFloat(punguzoInput.value) || 0;
                const jumla = parseFloat(totalInput.value) || 0;
                
                if (barcode && quantity > 0 && product && price > 0) {
                    const bidhaa = this.bidhaaList.find(b => b.barcode === barcode);
                    items.push({
                        barcode: barcode,
                        bidhaa_id: bidhaa ? bidhaa.id : null,
                        idadi: quantity,
                        bei: price,
                        punguzo: punguzo,
                        punguzo_aina: discountType,
                        jumla: jumla,
                        total_before_discount: (price * quantity)
                    });
                    hasValidItems = true;
                }
            }
        });

        if (!hasValidItems) {
            this.showNotification('Tafadhali angalau bidhaa moja iwe na barcode na idadi sahihi!', 'error');
            return;
        }

        const backdateCheckbox = document.getElementById('barcode_backdate_checkbox');
        const backdateDate = document.getElementById('barcode_backdate_date');
        
        const requestBody = { 
            items: items, 
            punguzo_aina: discountType,
            lipa_kwa: paymentMethod,
            lipa_kwa_type: paymentType
        };
        
        if (backdateCheckbox && backdateCheckbox.checked && backdateDate && backdateDate.value) {
            requestBody.is_backdate = '1';
            requestBody.backdate = backdateDate.value;
        }

        try {
            const response = await fetch("{{ route('mauzo.store.barcode') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(requestBody)
            });

            const data = await response.json();
            
            if (data.success) {
                this.showNotification('Mauzo yamehifadhiwa! Namba ya risiti: ' + data.receipt_no, 'success');
                this.syncStocksForItems(items);
                this.clearBarcodeRows();
                setTimeout(() => { refreshFinancialSoft(); refreshSalesTableSoft(); }, 400);
            } else {
                this.showNotification(data.message || 'Hitilafu katika kuhifadhi mauzo!', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            this.showNotification('Kuna tatizo kwenye kuhifadhi mauzo!', 'error');
        }
    }

    // ================================================================
    // CUSTOMER SEARCH
    // ================================================================

    initCustomerSearch(searchInput, selectDropdown, hiddenId, onSelectCallback, onNewCallback) {
        if (!searchInput || !selectDropdown) return;
        
        searchInput.addEventListener('focus', () => {
            selectDropdown.classList.remove('hidden');
            this.filterCustomerOptions(searchInput.value);
        });
        
        searchInput.addEventListener('input', (e) => {
            this.filterCustomerOptions(e.target.value);
            selectDropdown.classList.remove('hidden');
        });
        
        selectDropdown.addEventListener('change', () => {
            if (!selectDropdown.value) return;
            const selectedOption = selectDropdown.options[selectDropdown.selectedIndex];
            const customerName = selectedOption.dataset.jina || '';
            const customerPhone = selectedOption.dataset.simu || '';
            searchInput.value = `${customerName} - ${customerPhone}`;
            hiddenId.value = selectDropdown.value;
            selectDropdown.classList.add('hidden');
            if (onSelectCallback) onSelectCallback();
        });
        
        document.addEventListener('click', (e) => {
            if (!e.target.closest('#customer_search') && !e.target.closest('#customer_select')) {
                selectDropdown.classList.add('hidden');
                if (searchInput.value.trim() && !hiddenId.value) {
                    if (onNewCallback) onNewCallback();
                }
            }
        });
        
        searchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                selectDropdown.classList.add('hidden');
                if (searchInput.value.trim() && !hiddenId.value) {
                    if (onNewCallback) onNewCallback();
                }
            }
        });
    }

    filterCustomerOptions(searchText) {
        const selectDropdown = document.getElementById('customer_select');
        if (!selectDropdown) return;
        
        const filter = searchText.toLowerCase();
        const options = selectDropdown.getElementsByTagName('option');
        
        for (let i = 1; i < options.length; i++) {
            const jina = options[i].dataset.jina?.toLowerCase() || '';
            const simu = options[i].dataset.simu?.toLowerCase() || '';
            const searchableText = `${jina} ${simu}`;
            options[i].style.display = (filter === '' || searchableText.includes(filter)) ? '' : 'none';
        }
    }

    // ================================================================
    // SMS RECEIPT
    // ================================================================

    bindSmsReceiptEvents() {
        const sendReceiptCheckbox = document.getElementById('send_receipt_checkbox');
        const sendReceiptHidden = document.getElementById('send_receipt_hidden');
        const sendToPhoneHidden = document.getElementById('send_to_phone_hidden');
        const customerSearch = document.getElementById('customer_search');
        const customerSelect = document.getElementById('customer_select');
        const selectedCustomerId = document.getElementById('selected_customer_id');
        
        if (!sendReceiptCheckbox || !customerSearch) return;
        
        let isNewCustomerMode = false;
        
        const updatePhoneNumber = () => {
            const isChecked = sendReceiptCheckbox.checked;
            if (!isChecked) { sendToPhoneHidden.value = ''; return; }
            
            if (isNewCustomerMode) {
                const phoneValue = customerSearch.value.trim();
                if (phoneValue) {
                    let formattedPhone = phoneValue.replace(/\D/g, '');
                    if (formattedPhone.startsWith('0')) formattedPhone = '255' + formattedPhone.substring(1);
                    else if (formattedPhone.startsWith('255')) formattedPhone = formattedPhone;
                    else if (formattedPhone.length === 9 && formattedPhone.startsWith('7')) formattedPhone = '255' + formattedPhone;
                    sendToPhoneHidden.value = formattedPhone;
                } else {
                    sendToPhoneHidden.value = '';
                }
            } else {
                const selectedOption = customerSelect.options[customerSelect.selectedIndex];
                if (selectedOption && selectedOption.value) {
                    const customerPhone = selectedOption.dataset.simu || '';
                    let formattedPhone = customerPhone.replace(/\D/g, '');
                    if (formattedPhone.startsWith('0')) formattedPhone = '255' + formattedPhone.substring(1);
                    else if (formattedPhone.startsWith('255')) formattedPhone = formattedPhone;
                    sendToPhoneHidden.value = formattedPhone;
                } else {
                    sendToPhoneHidden.value = '';
                }
            }
        };
        
        this.initCustomerSearch(customerSearch, customerSelect, selectedCustomerId, 
            () => { isNewCustomerMode = false; updatePhoneNumber(); },
            () => { isNewCustomerMode = true; updatePhoneNumber(); }
        );
        
        sendReceiptCheckbox.addEventListener('change', (e) => {
            sendReceiptHidden.value = e.target.checked ? '1' : '0';
            updatePhoneNumber();
        });
        
        customerSearch.addEventListener('input', (e) => {
            if (!customerSelect.classList.contains('hidden')) return;
            const hasValue = e.target.value.trim().length > 0;
            if (hasValue && !selectedCustomerId.value) {
                isNewCustomerMode = true;
                updatePhoneNumber();
            } else if (!hasValue) {
                isNewCustomerMode = false;
                selectedCustomerId.value = '';
                updatePhoneNumber();
            }
        });
        
        updatePhoneNumber();
    }

    // ================================================================
    // SEARCH & FILTER
    // ================================================================

    bindSearchEvents() {
        const searchSales = document.getElementById('search-sales');
        const filterStartDate = document.getElementById('filter-start-date');
        const filterEndDate = document.getElementById('filter-end-date');
        const resetFilters = document.getElementById('reset-filters');
        
        if (searchSales) {
            searchSales.addEventListener('input', (e) => {
                if (this.searchTimer) clearTimeout(this.searchTimer);
                this.searchTimer = setTimeout(() => this.filterSalesTable(), 500);
            });
        }
        if (filterStartDate) filterStartDate.addEventListener('change', () => this.filterSalesTable());
        if (filterEndDate) filterEndDate.addEventListener('change', () => this.filterSalesTable());
        
        if (resetFilters) {
            resetFilters.addEventListener('click', () => {
                if (searchSales) searchSales.value = '';
                if (filterStartDate) filterStartDate.value = '';
                if (filterEndDate) filterEndDate.value = '';
                this.filterSalesTable();
            });
        }

        const searchProduct = document.getElementById('search-product');
        if (searchProduct) {
            searchProduct.addEventListener('input', (e) => {
                const searchTerm = e.target.value.toLowerCase();
                document.querySelectorAll('.grouped-sales-row').forEach(row => {
                    const product = row.dataset.product;
                    row.classList.toggle('hidden', !product.includes(searchTerm));
                });
            });
        }
    }

    filterSalesTable() {
        const searchTerm = document.getElementById('search-sales')?.value.toLowerCase() || '';
        const startDate = document.getElementById('filter-start-date')?.value || '';
        const endDate = document.getElementById('filter-end-date')?.value || '';
        const tbody = document.getElementById('sales-tbody');
        
        tbody.innerHTML = '<tr><td colspan="10" class="text-center py-4 text-gray-500">Inapakia...</td></tr>';
        
        fetch('/mauzo/filtered-sales', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ search: searchTerm, start_date: startDate, end_date: endDate })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('sales-tbody').innerHTML = data.html;
                this.bindReceiptEvents();
                this.bindDeleteSaleEvents();
            } else {
                this.showNotification('Kuna tatizo kwenye kupata taarifa!', 'error');
                tbody.innerHTML = '<tr><td colspan="10" class="text-center py-4 text-gray-500">Kuna tatizo kwenye kupata taarifa.</td></tr>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            this.showNotification('Kuna tatizo kwenye kupata taarifa!', 'error');
            tbody.innerHTML = '<tr><td colspan="10" class="text-center py-4 text-gray-500">Kuna tatizo kwenye kupata taarifa.</td></tr>';
        });
    }

    bindReceiptEvents() {
        // Copy receipt - using the class method
        document.querySelectorAll('.copy-receipt').forEach(element => {
            element.addEventListener('click', (e) => {
                const receiptNo = e.target.dataset.receipt || e.target.closest('.copy-receipt')?.dataset.receipt;
                if (receiptNo) {
                    this.copyReceiptNumber(receiptNo);
                }
            });
        });
        
        // Print single receipt
        document.querySelectorAll('.print-single-receipt').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const receiptNo = button.dataset.receiptNo;
                if (receiptNo) {
                    this.printSingleReceipt(receiptNo);
                } else {
                    this.showNotification('Namba ya risiti haipatikani', 'error');
                }
            });
        });
    }

    // ================================================================
    // DELETE SALE
    // ================================================================

    bindDeleteSaleEvents() {
        document.addEventListener('click', (e) => {
            if (e.target.closest('.delete-sale-btn')) {
                const btn = e.target.closest('.delete-sale-btn');
                const saleId = btn.dataset.id;
                const productName = btn.dataset.productName;
                const quantity = btn.dataset.quantity;
                this.showDeleteSaleConfirmation(saleId, productName, quantity);
            }
        });
    }

    showDeleteSaleConfirmation(saleId, productName, quantity) {
        this.currentSaleToDelete = saleId;
        const modal = document.getElementById('delete-sale-modal');
        const message = document.getElementById('delete-sale-message');
        const stockWarning = document.getElementById('stock-warning');
        const warningText = document.getElementById('stock-warning-text');
        
        if (!modal || !message) return;
        
        message.textContent = `Una uhakika unataka kufuta mauzo ya "${productName}"?`;
        if (stockWarning && warningText) {
            warningText.textContent = `Idadi ya ${parseFloat(quantity).toFixed(2)} itarudishwa kwenye stok ya bidhaa.`;
            stockWarning.classList.remove('hidden');
        }
        
        modal.classList.remove('hidden');
        
        document.getElementById('cancel-delete-sale').onclick = () => {
            modal.classList.add('hidden');
            this.currentSaleToDelete = null;
        };
        document.getElementById('confirm-delete-sale').onclick = async () => {
            await this.deleteSale(this.currentSaleToDelete);
            modal.classList.add('hidden');
            this.currentSaleToDelete = null;
        };
    }

    async deleteSale(saleId) {
        if (!saleId) return;

        try {
            const response = await fetch(`/mauzo/${saleId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            
            if (data.success) {
                this.showNotification('Mauzo yamefutwa kikamilifu! Stock imerudishwa.', 'success');
                setTimeout(() => { refreshFinancialSoft(); refreshSalesTableSoft(); }, 600);
            } else {
                this.showNotification(data.message || 'Kuna tatizo kufuta mauzo!', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            this.showNotification('Kuna tatizo kufuta mauzo!', 'error');
        }
    }

    // ================================================================
    // RECEIPT PRINTING
    // ================================================================

    initReceiptLookup() {
        const searchInput = document.getElementById('search-receipt-input');
        
        if (searchInput) {
            searchInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const receiptNo = searchInput.value.trim();
                    if (receiptNo) this.lookupReceipt(receiptNo);
                    else this.showNotification('Tafadhali ingiza namba ya risiti', 'error');
                }
            });
        }
        
        document.getElementById('print-thermal-receipt')?.addEventListener('click', () => {
            if (this.currentReceiptNo) {
                this.printThermalReceipt();
            } else {
                this.showNotification('Hakuna risiti iliyochaguliwa. Tafuta risiti kwanza.', 'error');
            }
        });
        
        document.getElementById('share-receipt-btn')?.addEventListener('click', () => {
            if (this.currentReceiptNo) this.shareReceipt(this.currentReceiptNo);
            else this.showNotification('Hakuna risiti iliyochaguliwa. Tafuta risiti kwanza.', 'error');
        });
        
        document.getElementById('sms-receipt-btn')?.addEventListener('click', () => {
            if (this.currentReceiptNo) this.sendReceiptSms(this.currentReceiptNo);
            else this.showNotification('Hakuna risiti iliyochaguliwa. Tafuta risiti kwanza.', 'error');
        });
    }

    lookupReceipt(receiptNo) {
        if (!receiptNo) {
            this.showNotification('Tafadhali ingiza namba ya risiti', 'error');
            return;
        }

        const detailsDiv = document.getElementById('receipt-details');
        const noResultsDiv = document.getElementById('no-receipt-found');
        const loadingDiv = document.getElementById('receipt-loading');
        const searchInput = document.getElementById('search-receipt-input');

        detailsDiv.classList.add('hidden');
        noResultsDiv.classList.add('hidden');
        loadingDiv.classList.remove('hidden');
        if (searchInput) searchInput.disabled = true;

        receiptNo = receiptNo.toString().trim();

        fetch(`/mauzo/receipt-data/${encodeURIComponent(receiptNo)}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) throw new Error(response.status === 404 ? 'Risiti haipatikani' : `HTTP error! status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            loadingDiv.classList.add('hidden');
            if (searchInput) searchInput.disabled = false;
            
            if (data.success) {
                this.displayReceiptDetails(data);
                detailsDiv.classList.remove('hidden');
                noResultsDiv.classList.add('hidden');
            } else {
                noResultsDiv.classList.remove('hidden');
                detailsDiv.classList.add('hidden');
                this.showNotification(data.message || 'Risiti haipatikani', 'error');
            }
        })
        .catch(error => {
            loadingDiv.classList.add('hidden');
            if (searchInput) searchInput.disabled = false;
            console.error('Error fetching receipt:', error);
            noResultsDiv.classList.remove('hidden');
            detailsDiv.classList.add('hidden');
            this.showNotification(error.message || 'Kuna tatizo kwenye kupata taarifa za risiti', 'error');
        });
    }

    displayReceiptDetails(data) {
        document.getElementById('receipt-no-display').textContent = data.receipt_no || 'N/A';
        this.currentReceiptNo = data.receipt_no;
        document.getElementById('receipt-date-display').textContent = data.date || new Date().toLocaleDateString('sw-TZ');
        
        const itemCount = data.items ? data.items.length : 0;
        document.getElementById('receipt-items-count').textContent = itemCount + (itemCount === 1 ? ' bidhaa' : ' bidhaa');
        
        const total = parseFloat(data.total) || 0;
        document.getElementById('receipt-total-display').textContent = total.toLocaleString('sw-TZ', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }) + '/=';

        const itemsList = document.getElementById('receipt-items-list');
        if (itemsList) {
            itemsList.innerHTML = '';
            if (data.items && data.items.length > 0) {
                data.items.forEach((item) => {
                    const itemDiv = document.createElement('div');
                    itemDiv.className = 'bg-white border border-gray-200 rounded-lg p-3 mb-2 hover:bg-gray-50 transition';
                    const quantity = parseFloat(item.idadi) || 0;
                    const price = parseFloat(item.bei) || 0;
                    const jumla = parseFloat(item.jumla) || 0;
                    const punguzo = parseFloat(item.punguzo) || 0;
                    
                    let discountHtml = punguzo > 0 ? `
                        <div class="flex justify-between items-center mt-1 text-xs text-red-600">
                            <span>Punguzo:</span>
                            <span>-${punguzo.toFixed(2)}/=</span>
                        </div>
                    ` : '';
                    
                    itemDiv.innerHTML = `
                        <div class="flex justify-between items-start">
                            <div class="flex-1"><span class="font-medium text-gray-800 text-sm">${item.bidhaa || 'Bidhaa'}</span></div>
                            <span class="text-sm font-semibold text-green-700">${jumla.toFixed(2)}/=</span>
                        </div>
                        <div class="flex justify-between items-center mt-1 text-xs text-gray-600">
                            <span>${quantity.toFixed(2)} x ${price.toFixed(2)}/=</span>
                            <span>Jumla ndogo: ${(price * quantity).toFixed(2)}/=</span>
                        </div>
                        ${discountHtml}
                    `;
                    itemsList.appendChild(itemDiv);
                });
            } else {
                itemsList.innerHTML = '<div class="text-center py-4 text-gray-500"><i class="fas fa-box-open text-2xl mb-2"></i><p>Hakuna bidhaa kwenye risiti hii</p></div>';
            }
        }
    }

    printThermalReceipt() {
        if (!this.currentReceiptNo) {
            this.showNotification('Hakuna risiti iliyochaguliwa', 'error');
            return;
        }
        const printWindow = window.open(`/mauzo/thermal-receipt/${encodeURIComponent(this.currentReceiptNo)}`, '_blank', 'width=400,height=600');
        if (printWindow) {
            printWindow.focus();
        } else {
            this.showNotification('Tafadhali ruhusu popups kwa chapisho', 'error');
        }
    }

    printSingleReceipt(receiptNo) {
        if (!receiptNo) {
            this.showNotification('Hakuna namba ya risiti', 'error');
            return;
        }
        const printWindow = window.open(`/mauzo/thermal-receipt/${encodeURIComponent(receiptNo)}`, '_blank', 'width=400,height=600');
        if (printWindow) {
            printWindow.focus();
        } else {
            this.showNotification('Tafadhali ruhusu popups kwa chapisho', 'error');
        }
    }

    async shareReceipt(receiptNo) {
        try {
            const response = await fetch(`/mauzo/receipt-data/${encodeURIComponent(receiptNo)}`);
            const data = await response.json();
            
            if (!data.success) {
                this.showNotification('Hitilafu: ' + data.message, 'error');
                return;
            }
            
            let receiptText = `*RISITI YA MALIPO*\n---\nNamba: ${data.receipt_no}\nTarehe: ${data.date}\n---\n`;
            data.items.forEach(item => {
                receiptText += `${item.bidhaa}\n  ${item.idadi} x ${item.bei} = ${item.jumla}\n`;
            });
            receiptText += `---\nJumla Kuu: ${data.total}\n---\nAsante kwa kununua!\n`;
            
            if (navigator.share) {
                await navigator.share({ title: 'Risiti ya Mauzo', text: receiptText });
            } else {
                await navigator.clipboard.writeText(receiptText);
                this.showNotification('Risiti imenakiliwa kwenye clipboard!', 'success');
            }
        } catch (error) {
            console.error('Error sharing receipt:', error);
            this.showNotification('Hitilafu wakati wa kushare risiti', 'error');
        }
    }

    // ================================================================
    // SMS RECEIPT MODAL
    // ================================================================

    sendReceiptSms(receiptNo) {
        if (!receiptNo) {
            this.showNotification('Hakuna risiti iliyochaguliwa', 'error');
            return;
        }
        
        this.pendingSmsReceiptNo = receiptNo;
        const previewDiv = document.getElementById('sms-receipt-preview');
        if (previewDiv) previewDiv.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Inataarisha risiti...';
        
        fetch(`/mauzo/receipt-data/${encodeURIComponent(receiptNo)}`)
            .then(response => response.json())
            .then(data => {
                if (!data.success) throw new Error(data.message);
                let previewText = `Risiti: ${data.receipt_no}\nTarehe: ${data.date}\nJumla: ${data.total}/=\nBidhaa: ${data.items.length}`;
                if (previewDiv) previewDiv.textContent = previewText;
                this.pendingSmsData = data;
            })
            .catch(error => {
                console.error('Error fetching receipt:', error);
                if (previewDiv) previewDiv.innerHTML = '<span class="text-red-500">Imeshindwa kupata taarifa za risiti</span>';
            });
        
        const modal = document.getElementById('sms-receipt-modal');
        if (modal) {
            modal.classList.remove('hidden');
            const phoneInput = document.getElementById('sms-receipt-phone');
            if (phoneInput) {
                setTimeout(() => phoneInput.focus(), 100);
                phoneInput.value = '';
            }
        }
    }

    setupSmsModalEvents() {
        const modal = document.getElementById('sms-receipt-modal');
        if (!modal) return;
        
        const closeModal = () => {
            modal.classList.add('hidden');
            const phoneInput = document.getElementById('sms-receipt-phone');
            if (phoneInput) phoneInput.value = '';
        };
        
        document.getElementById('close-sms-receipt-modal')?.addEventListener('click', closeModal);
        document.getElementById('cancel-sms-receipt')?.addEventListener('click', closeModal);
        modal.addEventListener('click', (e) => {
            if (e.target === modal || e.target.classList.contains('modal-overlay')) closeModal();
        });
        
        document.getElementById('confirm-send-sms')?.addEventListener('click', async () => {
            const phoneInput = document.getElementById('sms-receipt-phone');
            const phone = phoneInput?.value.trim() || '';
            
            if (!phone) {
                this.showNotification('Tafadhali ingiza namba ya simu', 'warning');
                phoneInput?.focus();
                return;
            }
            
            if (!this.pendingSmsReceiptNo) {
                this.showNotification('Hitilafu: Hakuna risiti iliyochaguliwa', 'error');
                closeModal();
                return;
            }
            
            const btn = document.getElementById('confirm-send-sms');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Inatuma...';
            btn.disabled = true;
            
            try {
                const response = await fetch('/send-receipt-sms-simple', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ phone: phone, receipt_no: this.pendingSmsReceiptNo })
                });
                
                const data = await response.json();
                if (data.success) {
                    this.showNotification('✓ Risiti imetumwa kikamilifu kwa ' + phone, 'success');
                    closeModal();
                } else {
                    this.showNotification('✗ Hitilafu: ' + (data.message || 'Imeshindwa kutuma SMS'), 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                this.showNotification('Hitilafu ya mtandao. Tafadhali jaribu tena.', 'error');
            } finally {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        });
    }

    // ================================================================
    // UTILITY
    // ================================================================

    setTodayDate() {
        const today = new Date().toISOString().split('T')[0];
        document.querySelectorAll('input[type="date"]').forEach(input => {
            if (!input.value) input.value = today;
        });
    }

    showNotification(message, type = 'success', showButtons = false) {
        const notification = document.getElementById('notification');
        const notificationIcon = document.getElementById('notification-icon');
        const notificationMessage = document.getElementById('notification-message');
        const notificationButtons = document.getElementById('notification-buttons');
        
        if (!notification || !notificationIcon || !notificationMessage) return;

        let iconClass, borderColor;
        switch(type) {
            case 'success':
                iconClass = 'fas fa-check-circle text-green-500';
                borderColor = 'border-green-200';
                break;
            case 'error':
                iconClass = 'fas fa-times-circle text-red-500';
                borderColor = 'border-red-200';
                break;
            case 'warning':
                iconClass = 'fas fa-exclamation-triangle text-amber-500';
                borderColor = 'border-amber-200';
                break;
            default:
                iconClass = 'fas fa-info-circle text-blue-500';
                borderColor = 'border-blue-200';
        }
        
        notificationIcon.className = iconClass;
        const notificationContent = notification.querySelector('.bg-white');
        if (notificationContent) {
            notificationContent.className = `bg-white rounded-2xl shadow-2xl p-6 max-w-sm mx-4 border-2 ${borderColor} transform transition-all duration-300 scale-95`;
        }
        notificationMessage.textContent = message;
        
        if (showButtons) {
            notificationButtons?.classList.remove('hidden');
        } else {
            notificationButtons?.classList.add('hidden');
        }
        
        notification.classList.remove('hidden');
        
        if (!showButtons) {
            setTimeout(() => notification.classList.add('hidden'), 3000);
        }
    }
}

// --- Invisible speed helpers (no visual change) ---
MauzoManager.prototype._openPrintIframe = function(url){
    const modal=document.getElementById('mauzo-print-modal'), iframe=document.getElementById('mauzo-print-iframe');
    if(!modal||!iframe){ window.open(url,'_blank'); return; }
    iframe.src=url; modal.classList.remove('hidden'); modal.classList.add('flex'); document.body.style.overflow='hidden';
    iframe.onload=()=>{ try{ iframe.contentWindow.focus(); }catch(e){} };
    modal.onclick=(e)=>{ if(e.target===modal){ modal.classList.add('hidden'); modal.classList.remove('flex'); iframe.src='about:blank'; document.body.style.overflow=''; } };
};
const _origPrintSingle = MauzoManager.prototype.printSingleReceipt;
MauzoManager.prototype.printSingleReceipt = function(receiptNo){
    if(!receiptNo){ this.showNotification('Hakuna namba ya risiti','error'); return; }
    this.showNotification('Inaandaa risiti...','warning');
    this._openPrintIframe(`/mauzo/thermal-receipt/${encodeURIComponent(receiptNo)}`);
    setTimeout(()=> this.showNotification('Risiti inachapishwa...','success'),400);
};
const _origPrintThermal = MauzoManager.prototype.printThermalReceipt;
MauzoManager.prototype.printThermalReceipt = function(){
    if(!this.currentReceiptNo){ this.showNotification('Hakuna risiti iliyochaguliwa','error'); return; }
    this._openPrintIframe(`/mauzo/thermal-receipt/${encodeURIComponent(this.currentReceiptNo)}`);
};
function refreshFinancialSoft(){
    fetch('/mauzo/financial-data',{headers:{'Accept':'application/json'}})
    .then(r=>r.json()).then(j=>{
        if(!j.success||!j.data) return;
        const d=j.data.raw||j.data;
        const fmt=v=>Number(v||0).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2});
        const set=(id,v)=>{const el=document.getElementById(id); if(el) el.textContent=fmt(v);};
        set('fin-mauzo-leo', d.mauzo_leo_sum);
        set('fin-marejesho-leo', d.marejesho_leo_sum);
        set('fin-mapato-leo', d.mapato_leo);
        set('fin-mapato-simple', d.mapato_leo);
        set('fin-faida-mauzo', d.faida_mauzo);
        set('fin-faida-marejesho', d.faida_marejesho);
        set('fin-faida-jumla', d.faida_leo);
        set('fin-matumizi-leo', d.matumizi_leo_sum);
        set('fin-matumizi-jumla', d.matumizi_total);
        set('fin-fedha-mapato', d.mapato_leo);
        set('fin-fedha-matumizi', d.matumizi_leo_sum);
        set('fin-fedha-leo', d.fedha_leo);
        set('fin-faida-halisi-faida', d.faida_leo);
        set('fin-faida-halisi-matumizi', d.matumizi_leo_sum);
        set('fin-faida-halisi', d.faida_halisi);
        set('fin-jumla-mapato', (d.mauzo_total_sum||0)+(d.marejesho_total||0));
        set('fin-jumla-matumizi', d.matumizi_total);
        set('fin-jumla-kuu', d.jumla_kuu);
    }).catch(()=>{});
}
function refreshSalesTableSoft(){
    const tbody=document.getElementById('sales-tbody'); if(!tbody) return;
    fetch('/mauzo/filtered-sales',{method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'Accept':'application/json'}, body:JSON.stringify({})})
    .then(r=>r.json()).then(j=>{ if(j.success&&j.html) tbody.innerHTML=j.html; }).catch(()=>{});
}
const _origFilterProd = MauzoManager.prototype.filterProductOptions;
MauzoManager.prototype.filterProductOptions = function(searchText){
    const productList=document.getElementById('product-list');
    const noResults=document.getElementById('no-products-found');
    const items=productList.querySelectorAll('.product-item');
    const filter=searchText.toLowerCase().trim();
    let hasResults=false, shown=0; const LIMIT=50;
    items.forEach(item=>{
        const jina=(item.dataset.jina||'').toLowerCase();
        const aina=(item.dataset.aina||'').toLowerCase();
        const kipimo=(item.dataset.kipimo||'').toLowerCase();
        const barcode=(item.dataset.barcode||'').toLowerCase();
        const matches=filter===''||jina.includes(filter)||aina.includes(filter)||kipimo.includes(filter)||barcode.includes(filter);
        const show=matches && shown < LIMIT; if(show) shown++;
        item.style.display=show?'':'none';
        if(show) hasResults=true;
    });
    if(noResults) noResults.classList.toggle('hidden', hasResults);
    items.forEach(el=>el.classList.remove('selected'));
};
// Keep original product search (selection shows in search like previous) — filter already limited to 50 for smoothness
// Smooth data refresh on EVERY successful transaction (sale, kopesha, order, debt)
function refreshAllMauzoData(){
    refreshFinancialSoft();
    refreshSalesTableSoft();
    // also refresh product stock display if needed
    if(window.mauzoManager && typeof window.mauzoManager.updateFinancialData==='function'){
        try{ window.mauzoManager.updateFinancialData(); }catch(e){}
    }
}
// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.mauzoManager = new MauzoManager();
    setInterval(refreshFinancialSoft, 60000);
    const origShow = MauzoManager.prototype.showNotification;
    MauzoManager.prototype.showNotification = function(msg,type){
        origShow.call(this,msg,type);
        if(type==='success'){
            setTimeout(()=>{ refreshFinancialSoft(); refreshSalesTableSoft(); }, 600);
        }
    };
    // Ensure product search shows selected value like previous — no clone, keep original listeners
});
</script>
@endpush