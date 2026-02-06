@extends('layouts.app')

@section('title', 'Mauzo - DEMODAY')

@section('page-title', 'Mauzo')
@section('page-subtitle', 'Usimamizi wa mauzo - ' . now()->format('d/m/Y'))

@section('content')
@php
    // Check which guard is active and get company info accordingly
    $companyId = null;
    $companyName = 'My Business';
    
    if(Auth::guard('mfanyakazi')->check()) {
        // Employee is logged in
        $user = Auth::guard('mfanyakazi')->user();
        $companyId = $user->company_id ?? null;
        
        // If employee has company relation, get company name
        if($user->company) {
            $companyName = $user->company->company_name ?? 'My Business';
        }
    } elseif(Auth::guard('web')->check()) {
        // Boss/Admin is logged in
        $user = Auth::guard('web')->user();
        $companyId = $user->company_id ?? null;
        
        // If user has company relation, get company name
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
    <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-lg p-1 mb-4">
        <div class="flex flex-wrap gap-1" id="tab-nav">
            <button id="sehemu-tab" class="tab-button pb-2 px-3 transition-colors flex items-center border-b-2 border-white text-white font-semibold whitespace-nowrap text-sm" data-tab="sehemu">
                <i class="fas fa-cash-register mr-2 text-xs"></i>Sehemu ya Mauzo
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
            <button id="kikapu-tab" class="tab-button pb-2 px-3 transition-colors flex items-center text-green-100 hover:text-white relative whitespace-nowrap text-sm" data-tab="kikapu">
                <i class="fas fa-shopping-cart mr-2 text-xs"></i>Kikapu
                <span id="cart-count" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center hidden">0</span>
            </button>
            <button id="risiti-tab" class="tab-button pb-2 px-3 transition-colors flex items-center text-green-100 hover:text-white whitespace-nowrap text-sm" data-tab="risiti">
                <i class="fas fa-receipt mr-2 text-xs"></i>Risiti
            </button>
        </div>
    </div>

    <!-- TAB 1: Sehemu ya Mauzo -->
    <div id="sehemu-tab-content" class="tab-content active">
        <!-- Sales Form -->
        <div class="bg-white rounded-lg shadow border border-gray-200 p-4 mb-4">
            <h2 class="text-lg font-bold text-gray-800 mb-3 flex items-center">
                <i class="fas fa-cash-register mr-2 text-green-600"></i>
                Rekodi Mauzo
            </h2>

            <form method="POST" action="{{ route('mauzo.store') }}" class="space-y-4" id="sales-form">
                @csrf

                <!-- Product Selection Row -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-3">
                    <!-- Product Selection -->
                    <div class="lg:col-span-5">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Bidhaa</label>
                        <div class="relative">
                            <input type="text" id="bidhaaSearch" placeholder="Tafuta bidhaa..." class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-green-200">
                            <select id="bidhaaSelect" name="bidhaa_id" size="5" class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-green-200 hidden absolute top-full left-0 right-0 z-10 bg-white shadow-lg max-h-60 overflow-y-auto">
                                <option value="">Chagua Bidhaa...</option>
                                @foreach($bidhaa as $item)
                                <option
                                    value="{{ $item->id }}"
                                    data-bei="{{ $item->bei_kuuza }}"
                                    data-stock="{{ $item->idadi }}"
                                    data-jina="{{ e($item->jina) }}"
                                    data-aina="{{ e($item->aina) }}"
                                    data-kipimo="{{ e($item->kipimo) }}"
                                    data-bei-nunua="{{ $item->bei_nunua }}"
                                    data-barcode="{{ $item->barcode }}"
                                >
                                    {{ $item->jina }} ({{ $item->aina }}) - {{ $item->kipimo }} - Stock: {{ $item->idadi }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Quantity -->
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Idadi</label>
                        <input type="number" name="idadi" id="quantity-input" placeholder="0" min="1" class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-green-200">
                    </div>

                    <!-- Price -->
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Bei (Tsh)</label>
                        <input type="number" name="bei" id="price-input" readonly class="w-full bg-gray-100 border border-gray-300 rounded-lg p-2 text-sm">
                    </div>

                    <!-- Stock -->
                    <div class="lg:col-span-3">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Stock</label>
                        <input type="number" id="stock-input" readonly class="w-full bg-gray-100 border border-gray-300 rounded-lg p-2 text-sm">
                    </div>
                </div>

                <!-- Discount and Payment Row -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-3">
                    <!-- Discount Type -->
                    <div class="lg:col-span-3">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Aina ya Punguzo</label>
                        <select id="punguzo-type" class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-green-200">
                            <option value="bidhaa">kwa bidhaa</option>
                            <option value="jumla">Jumla</option>
                        </select>
                    </div>

                    <!-- Discount Amount -->
                    <div class="lg:col-span-3">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Punguzo (Tsh)</label>
                        <input type="number" name="punguzo" id="punguzo-input" min="0" value="0" class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-green-200">
                    </div>

                    <!-- Total -->
                    <div class="lg:col-span-3">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Jumla (Tsh)</label>
                        <input type="number" name="jumla" id="total-input" readonly class="w-full bg-green-50 border border-green-300 rounded-lg p-2 text-sm font-bold text-gray-800">
                    </div>

                    <!-- Payment Method -->
                    <div class="lg:col-span-3">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Njia ya Malipo</label>
                        <select name="lipa_kwa" id="lipa_kwa_select" class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-green-200">
                            <option value="cash">Cash</option>
                            <option value="lipa_namba">Lipa Namba</option>
                            <option value="bank">Bank</option>
                        </select>
                    </div>
                </div>

                <!-- Hidden fields -->
                <input type="hidden" name="baki" id="baki-input" value="0">
                <input type="hidden" name="punguzo_aina" id="punguzo-aina-input" value="bidhaa">
                <input type="hidden" name="check_double_sale" id="check-double-sale-input" value="1">
                
                <!-- Action Buttons -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 pt-2">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white p-3 rounded-lg font-semibold text-sm flex items-center justify-center gap-2">
                        <i class="fas fa-cash-register"></i>
                        Uza
                    </button>

                    <button type="button" id="kopesha-btn" class="bg-yellow-600 hover:bg-yellow-700 text-white p-3 rounded-lg font-semibold text-sm flex items-center justify-center gap-2">
                        <i class="fas fa-hand-holding-usd"></i>
                        Kopesha
                    </button>

                    <button type="button" id="add-to-cart-btn" class="bg-emerald-600 hover:bg-blue-700 text-white p-3 rounded-lg font-semibold text-sm flex items-center justify-center gap-2">
                        <i class="fas fa-cart-plus"></i>
                        Kikapu
                    </button>
                </div>
            </form>
        </div>

        <!-- Financial Overview -->
        <div class="bg-white rounded-lg shadow border border-gray-200 p-4">
            <h2 class="text-base font-bold text-gray-800 mb-3">
                Taarifa Fupi ya Mapato na Matumizi
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3" id="financial-overview">
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
                        $mapatoMauzo = $todaysMauzos->sum(fn($m) => $m->jumla);
                        $mapatoMadeni = $todaysMarejeshos->sum('kiasi');
                        $jumlaMapato = $mapatoMauzo + $mapatoMadeni;
                    @endphp
                    <div class="space-y-1 text-white text-xs">
                        <div class="flex justify-between items-center">
                            <span class="text-blue-100">Mauzo:</span>
                            <span class="font-semibold">{{ number_format($mapatoMauzo) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-blue-100">Madeni:</span>
                            <span class="font-semibold">{{ number_format($mapatoMadeni) }}</span>
                        </div>
                        <div class="flex justify-between items-center border-t border-white/20 pt-1.5 mt-1.5">
                            <span class="font-semibold">Jumla:</span>
                            <span class="font-bold">{{ number_format($jumlaMapato) }}</span>
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
                        $faidaMauzo = 0;
                        foreach($todaysMauzos as $mauzo) {
                            $buyingPrice = $mauzo->bidhaa->bei_nunua ?? 0;
                            $sellingPrice = $mauzo->bei;
                            $quantity = $mauzo->idadi;
                            
                            $totalDiscount = 0;
                            if ($mauzo->punguzo_aina === 'bidhaa') {
                                $totalDiscount = $mauzo->punguzo * $quantity;
                            } else {
                                $totalDiscount = $mauzo->punguzo;
                            }
                            
                            $totalRevenueBeforeDiscount = $sellingPrice * $quantity;
                            $totalRevenueAfterDiscount = $totalRevenueBeforeDiscount - $totalDiscount;
                            $totalBuyingCost = $buyingPrice * $quantity;
                            $profit = $totalRevenueAfterDiscount - $totalBuyingCost;
                            $faidaMauzo += $profit;
                        }
                        
                        $faidaMarejesho = 0;
                        foreach($todaysMarejeshos as $marejesho) {
                            if(isset($marejesho->madeni) && isset($marejesho->madeni->bidhaa)) {
                                $debt = $marejesho->madeni;
                                $buyingPrice = $debt->bidhaa->bei_nunua ?? 0;
                                $quantity = $debt->idadi;
                                $totalBuyingCost = $buyingPrice * $quantity;
                                $actualSellingPrice = $debt->jumla;
                                $profit = $actualSellingPrice - $totalBuyingCost;
                                $faidaMarejesho += $profit;
                            }
                        }
                        
                        $jumlaFaida = $faidaMauzo + $faidaMarejesho;
                    @endphp
                    <div class="space-y-1 text-white text-xs">
                        <div class="flex justify-between items-center">
                            <span class="text-green-100">Mauzo:</span>
                            <span class="font-semibold">{{ number_format($faidaMauzo) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-green-100">Marejesho:</span>
                            <span class="font-semibold">{{ number_format($faidaMarejesho) }}</span>
                        </div>
                        <div class="flex justify-between items-center border-t border-white/20 pt-1.5 mt-1.5">
                            <span class="font-semibold">Jumla:</span>
                            <span class="font-bold">{{ number_format($jumlaFaida) }}</span>
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
                    <div class="text-xs font-semibold text-white uppercase tracking-wide mb-1">Matumizi</div>
                    @php
                        $matumiziLeo = $todaysMatumizi->sum('gharama');
                        $matumiziWiki = $weeklyMatumizi->sum('gharama');
                        $matumiziJumla = $allMatumizi->sum('gharama');
                    @endphp
                    <div class="space-y-1 text-white text-xs">
                        <div class="flex justify-between items-center">
                            <span>Leo:</span>
                            <span class="font-semibold">{{ number_format($matumiziLeo) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span>Wiki hii:</span>
                            <span class="font-semibold">{{ number_format($matumiziWiki) }}</span>
                        </div>
                        <div class="flex justify-between items-center border-t border-white/20 pt-1.5 mt-1.5">
                            <span class="font-semibold">Jumla:</span>
                            <span class="font-bold">{{ number_format($matumiziJumla) }}</span>
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
                        $mauzoLeo = $todaysMauzos->sum('jumla');
                        $mapatoMadeni = $todaysMarejeshos->sum('kiasi');
                        $matumiziLeo = $todaysMatumizi->sum('gharama');
                        $fedhaLeo = ($mauzoLeo + $mapatoMadeni) - $matumiziLeo;
                    @endphp
                    <div class="space-y-1 text-white text-xs">
                        <div class="flex justify-between items-center">
                            <span>Mapato:</span>
                            <span class="font-semibold">{{ number_format($mauzoLeo + $mapatoMadeni) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span>Matumizi:</span>
                            <span class="font-semibold">{{ number_format($matumiziLeo) }}</span>
                        </div>
                        <div class="flex justify-between items-center border-t border-white/20 pt-1.5 mt-1.5">
                            <span class="font-semibold">Jumla:</span>
                            <span class="font-bold">{{ number_format($fedhaLeo) }}</span>
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
                        $matumiziLeo = $todaysMatumizi->sum('gharama');
                        $faidaHalisi = $jumlaFaida - $matumiziLeo;
                    @endphp
                    <div class="space-y-1 text-white text-xs">
                        <div class="flex justify-between items-center">
                            <span>Faida:</span>
                            <span class="font-semibold">{{ number_format($jumlaFaida) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span>Matumizi:</span>
                            <span class="font-semibold">{{ number_format($matumiziLeo) }}</span>
                        </div>
                        <div class="flex justify-between items-center border-t border-white/20 pt-1.5 mt-1.5">
                            <span class="font-semibold">Halisi:</span>
                            <span class="font-bold">{{ number_format($faidaHalisi) }}</span>
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
                        $totalMapato = $allTimeMauzos->sum('jumla') + $allTimeMarejeshos->sum('kiasi');
                        $totalMatumizi = $allMatumizi->sum('gharama');
                        $jumlaKuu = $totalMapato - $totalMatumizi;
                    @endphp
                    <div class="space-y-1 text-white text-xs">
                        <div class="flex justify-between items-center">
                            <span>Mapato:</span>
                            <span class="font-semibold">{{ number_format($totalMapato) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span>Matumizi:</span>
                            <span class="font-semibold">{{ number_format($totalMatumizi) }}</span>
                        </div>
                        <div class="flex justify-between items-center border-t border-white/20 pt-1.5 mt-1.5">
                            <span class="font-semibold">Jumla:</span>
                            <span class="font-bold">{{ number_format($jumlaKuu) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                <div class="mb-3">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Njia ya Malipo</label>
                    <select name="lipa_kwa" class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-green-200">
                        <option value="cash">Cash</option>
                        <option value="lipa_namba">Lipa Namba</option>
                        <option value="bank">Bank</option>
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
                                    <input type="number" name="idadi[]" min="1" value="1" placeholder="Idadi" 
                                           class="quantity-input border border-gray-300 focus:border-green-500 focus:ring-2 focus:ring-green-200 rounded-lg p-2 w-full text-sm" />
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" name="stock[]" readonly placeholder="Baki" 
                                           class="stock-input border border-gray-200 bg-gray-50 text-gray-700 rounded-lg p-2 w-full text-sm" />
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" name="punguzo[]" min="0" value="0" placeholder="Punguzo" 
                                           class="punguzo-input border border-gray-300 rounded-lg p-2 w-full text-sm" />
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" name="jumla[]" readonly placeholder="Jumla" 
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
                        <span class="text-green-700 font-bold" id="barcode-total">0</span>
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

            <!-- Search and Filter -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 mb-4">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    <input type="text" id="search-sales" placeholder="Tafuta kwa jina la bidhaa..." class="pl-10 w-full border border-gray-300 rounded-lg p-2 text-sm">
                </div>
                
                <div class="relative">
                    <i class="fas fa-calendar absolute left-3 top-3 text-gray-400"></i>
                    <input type="date" id="filter-date" class="pl-10 w-full border border-gray-300 rounded-lg p-2 text-sm">
                </div>
                
                <button id="reset-filters" class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-2 rounded-lg text-sm">
                    <i class="fas fa-redo mr-1"></i> Safisha Filter
                </button>
            </div>

            <!-- Sales Table -->
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="w-full border-collapse text-sm">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border px-3 py-2 text-left text-gray-700">Tarehe</th>
                            <th class="border px-3 py-2 text-left text-gray-700">Risiti</th>
                            <th class="border px-3 py-2 text-left text-gray-700">Bidhaa</th>
                            <th class="border px-3 py-2 text-left text-gray-700">Idadi</th>
                            <th class="border px-3 py-2 text-left text-gray-700">Bei</th>
                            <th class="border px-3 py-2 text-left text-gray-700">Punguzo</th>
                            <th class="border px-3 py-2 text-left text-gray-700">Faida</th>
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
                                switch($item->lipa_kwa) {
                                    case 'cash':
                                        $paymentMethod = '<span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Cash</span>';
                                        break;
                                    case 'lipa_namba':
                                        $paymentMethod = '<span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">Lipa Namba</span>';
                                        break;
                                    case 'bank':
                                        $paymentMethod = '<span class="bg-purple-100 text-purple-800 px-2 py-1 rounded text-xs">Bank</span>';
                                        break;
                                    default:
                                        $paymentMethod = '<span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs">Cash</span>';
                                }
                            @endphp
                            <tr class="sales-row" data-product="{{ strtolower($item->bidhaa->jina) }}" data-date="{{ $itemDate }}" data-id="{{ $item->id }}">
                                <td class="border px-3 py-2">
                                    @if($itemDate === $today)
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded font-semibold text-xs">Leo</span>
                                    @else
                                        {{ $item->created_at->format('d/m/Y') }}
                                    @endif
                                </td>
                                <td class="border px-3 py-2 font-mono">
                                    @if($item->receipt_no)
                                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs copy-receipt cursor-pointer" data-receipt="{{ $item->receipt_no }}" title="Bonyeza kunakili">{{ substr($item->receipt_no, -8) }}</span>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>
                                <td class="border px-3 py-2">{{ $item->bidhaa->jina }}</td>
                                <td class="border px-3 py-2 text-center">{{ $item->idadi }}</td>
                                <td class="border px-3 py-2 text-right">{{ number_format($item->bei) }}</td>
                                <td class="border px-3 py-2 text-right">{{ number_format($actualDiscount) }}</td>
                                <td class="border px-3 py-2 text-right">{{ number_format($faida) }}</td>
                                <td class="border px-3 py-2 text-center">{!! $paymentMethod !!}</td>
                                <td class="border px-3 py-2 text-right">{{ number_format($total) }}</td>
                                <td class="border px-3 py-2 text-center">
                                    <div class="flex gap-1 justify-center">
                                        @if($item->receipt_no)
                                        <button type="button" class="print-single-receipt bg-blue-200 hover:bg-blue-400 text-gray-700 px-2 py-1 rounded-lg flex items-center justify-center transition text-xs" data-receipt-no="{{ $item->receipt_no }}">
                                            <i class="fas fa-print mr-1"></i>
                                        </button>
                                        @endif
                                        <button type="button" class="delete-sale-btn bg-red-200 hover:bg-red-400 text-gray-700 px-2 py-1 rounded-lg flex items-center justify-center transition text-xs" data-id="{{ $item->id }}" data-product-name="{{ $item->bidhaa->jina }}" data-quantity="{{ $item->idadi }}">
                                            <i class="fas fa-trash mr-1"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4 text-gray-500">Hakuna mauzo yaliyorekodiwa bado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
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
                            <th class="border px-3 py-2 text-left text-gray-700 text-sm">Faida</th>
                        </tr>
                    </thead>
                    <tbody id="grouped-sales-tbody">
                        @php
                            $groupedSales = [];
                            foreach($allMauzos as $sale) {
                                $date = $sale->created_at->format('Y-m-d');
                                $product = $sale->bidhaa->jina;
                                $key = $date . '|' . $product;
                                
                                if (!isset($groupedSales[$key])) {
                                    $groupedSales[$key] = [
                                        'tarehe' => $date,
                                        'jina' => $product,
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
                        @endphp
                        
                        @foreach($groupedSales as $sale)
                        <tr class="grouped-sales-row" data-product="{{ strtolower($sale['jina']) }}">
                            <td class="border px-3 py-2 text-sm">{{ $sale['tarehe'] }}</td>
                            <td class="border px-3 py-2 text-sm">{{ $sale['jina'] }}</td>
                            <td class="border px-3 py-2 text-center text-sm">{{ $sale['idadi'] }}</td>
                            <td class="border px-3 py-2 text-right text-sm">{{ number_format($sale['punguzo']) }}</td>
                            <td class="border px-3 py-2 text-right text-sm">{{ number_format($sale['jumla']) }}</td>
                            <td class="border px-3 py-2 text-right text-sm">{{ number_format($sale['faida']) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 5: Kikapu -->
    <div id="kikapu-tab-content" class="tab-content hidden">
        <div class="bg-white rounded-lg shadow border border-gray-200 p-4">
            <h2 class="text-lg font-bold mb-3 flex items-center text-gray-800">
                <i class="fas fa-shopping-cart mr-2 text-blue-600"></i>
                Bidhaa Zilizo Kwenye Kikapu
            </h2>

            <div id="company-cart-warning" class="bg-yellow-50 border-l-4 border-yellow-400 p-3 mb-3 hidden">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            Bidhaa zilizokuwa kwenye kikapu zimeondolewa kwa sababu hazikuwa za kampuni yako.
                        </p>
                    </div>
                </div>
            </div>

            <div id="empty-cart-message" class="text-center text-gray-600 py-8">
                <i class="fas fa-shopping-cart text-3xl text-gray-400 mb-3"></i>
                <p class="text-sm">Hakuna bidhaa kwenye kikapu kwa sasa.</p>
            </div>
            
            <div id="cart-content" class="hidden">
                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th class="border px-3 py-2 text-left">S/N</th>
                                <th class="border px-3 py-2 text-left">Bidhaa</th>
                                <th class="border px-3 py-2 text-left">Idadi</th>
                                <th class="border px-3 py-2 text-left">Bei</th>
                                <th class="border px-3 py-2 text-left">Punguzo</th>
                                <th class="border px-3 py-2 text-left">Jumla</th>
                                <th class="border px-3 py-2 text-left">Ondoa</th>
                            </tr>
                        </thead>
                        <tbody id="cart-tbody">
                            <!-- Cart items will be populated here by JavaScript -->
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-between items-center my-3">
                    <div class="font-semibold text-gray-800 text-base">
                        Jumla ya gharama: 
                        <span class="text-green-600" id="cart-total">0</span>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Njia ya Malipo</label>
                    <select id="kikapu-lipa-kwa" class="w-full border border-gray-300 rounded-lg p-2 text-sm">
                        <option value="cash">Cash</option>
                        <option value="lipa_namba">Lipa Namba</option>
                        <option value="bank">Bank</option>
                    </select>
                </div>

                <div class="flex flex-wrap gap-2">
                    <button id="clear-cart" class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-2 rounded-lg flex items-center transition text-sm">
                        <i class="fas fa-trash mr-2"></i>
                        Futa Kikapu
                    </button>
                    <button id="kikapu-kopesha-btn" class="bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-2 rounded-lg flex items-center transition text-sm">
                        <i class="fas fa-hand-holding-usd mr-2"></i>
                        Kopesha
                    </button>
                    <button id="checkout-cart" class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg flex items-center transition text-sm">
                        <i class="fas fa-check mr-2"></i>
                        Funga Kapu
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 6: Risiti -->
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
                    Namba ya risiti inapatikana kwenye tab ya "Taarifa"
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

                <div class="flex justify-center">
                    <button id="print-thermal-receipt" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold flex items-center gap-2 text-sm">
                        <i class="fas fa-print"></i>
                        Chapisha Risiti
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

<!-- Modals -->
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

            <div>
                <label class="block text-sm font-semibold mb-1 text-gray-700">Mteja Aliyesajiliwa</label>
                <select id="mteja-select" class="w-full border border-gray-300 rounded-lg p-2 text-sm">
                    <option value="">-- Mteja Mpya --</option>
                    @foreach($wateja as $m)
                        <option value="{{ $m->id }}" data-jina="{{ $m->jina }}" data-simu="{{ $m->simu }}" data-barua_pepe="{{ $m->barua_pepe }}" data-anapoishi="{{ $m->anapoishi }}">
                            {{ $m->jina }} - {{ $m->simu }}
                        </option>
                    @endforeach
                </select>
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
                <label class="block text-sm font-semibold mb-1 text-gray-700">Chagua Mteja</label>
                <select id="barcode-mteja-select" class="w-full border border-gray-300 rounded-lg p-2 text-sm">
                    <option value="">-- Mteja Mpya --</option>
                    @foreach($wateja as $mteja)
                        <option value="{{ $mteja->id }}" data-jina="{{ $mteja->jina }}" data-simu="{{ $mteja->simu }}" data-barua_pepe="{{ $mteja->barua_pepe }}" data-anapoishi="{{ $mteja->anapoishi }}">
                            {{ $mteja->jina }} 
                            @if($mteja->simu)
                                - {{ $mteja->simu }}
                            @endif
                        </option>
                    @endforeach
                </select>
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

<!-- Kopesha Kikapu Modal -->
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
                <label class="block text-sm font-semibold mb-1 text-gray-700">Chagua Mteja</label>
                <select id="kikapu-mteja-select" class="w-full border border-gray-300 rounded-lg p-2 text-sm">
                    <option value="">-- Mteja Mpya --</option>
                    @foreach($wateja as $mteja)
                        <option value="{{ $mteja->id }}" data-jina="{{ $mteja->jina }}" data-simu="{{ $mteja->simu }}" data-barua_pepe="{{ $mteja->barua_pepe }}" data-anapoishi="{{ $mteja->anapoishi }}">
                            {{ $mteja->jina }} 
                            @if($mteja->simu)
                                - {{ $mteja->simu }}
                            @endif
                        </option>
                    @endforeach
                </select>
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
    border-bottom-color: white !important;
    color: white !important;
}

.card-hover {
    transition: all 0.3s ease;
}

.card-hover:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
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

@media (max-width: 640px) {
    .modal-content {
        width: 95% !important;
        margin: 0.5rem !important;
    }
}

::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>
@endpush

@push('scripts')
<script>
class MauzoManager {


    constructor() {
        // Get company ID from meta tag or data attribute
        this.companyId = document.querySelector('meta[name="company-id"]')?.getAttribute('content') 
                       || document.body.dataset.companyId
                       || 'default';
        
        // Create unique cart key for this company
        this.cartKey = `mauzo_cart_${this.companyId}`;
        this.cart = JSON.parse(localStorage.getItem(this.cartKey)) || [];
        this.bidhaaList = @json($bidhaa);
        this.barcodeScanTimeout = null;
        this.currentReceiptNo = null;
        this.deleteCallback = null;
        this.pendingSaleData = null;
        this.currentKopeshaType = null;
        this.currentSaleToDelete = null;
        
        // Restore tab state BEFORE init to ensure correct tab is shown
        this.restoreTabState();
        this.init();
    }

    init() {
        this.bindEvents();
        this.updateCartCount();
        this.setTodayDate();
        this.initBidhaaSearch();
        this.initBarcodeRows();
        this.initCartDisplay();
        this.initCustomerSelection();
        this.clearOtherCompanyCarts();
        this.bindDeleteSaleEvents();
        this.initReceiptLookup();
    }

    // ADD THIS HELPER FUNCTION AT THE BEGINNING OF THE MauzoManager CLASS:
calculateActualDiscount(discount, discountType, quantity) {
    if (discountType === 'bidhaa') {
        return discount * quantity;
    }
    return discount;
}
    // Modified: Save and restore tab state properly
    restoreTabState() {
        // Get saved tab from localStorage
        const savedTab = localStorage.getItem('currentMauzoTab');
        
        // If no saved tab, default to 'sehemu' (sales tab)
        this.currentTab = savedTab || 'sehemu';
        
        // Show the saved tab immediately
        setTimeout(() => {
            this.showTab(this.currentTab, true); // true = initial restore
        }, 50); // Small delay to ensure DOM is ready
    }

    // Modified: Show tab with save option
    showTab(tabName, isRestore = false) {
        // Hide all tab buttons active state
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('active');
        });
        
        // Hide all tab content
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.remove('active');
            content.classList.add('hidden');
        });
        
        // Show selected tab
        const activeTab = document.getElementById(`${tabName}-tab`);
        const activeContent = document.getElementById(`${tabName}-tab-content`);
        
        if (activeTab && activeContent) {
            activeTab.classList.add('active');
            activeContent.classList.add('active');
            activeContent.classList.remove('hidden');
        }

        // Save to localStorage (only if not restoring)
        if (!isRestore) {
            localStorage.setItem('currentMauzoTab', tabName);
            this.currentTab = tabName;
        }
        
        // Handle tab-specific focus
        if (tabName === 'barcode') {
            setTimeout(() => {
                const firstBarcodeInput = document.querySelector('.barcode-input');
                if (firstBarcodeInput) {
                    firstBarcodeInput.focus();
                }
            }, 100);
        }
        
        if (tabName === 'risiti') {
            setTimeout(() => {
                const searchInput = document.getElementById('search-receipt-input');
                if (searchInput) {
                    searchInput.focus();
                }
            }, 100);
        }
        
        if (tabName === 'kikapu') {
            this.updateCartDisplay();
        }
    }

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
        const cancelBtn = document.getElementById('cancel-delete-sale');
        const confirmBtn = document.getElementById('confirm-delete-sale');
        const closeBtn = document.querySelector('#delete-sale-modal .modal-overlay');
        
        if (!modal || !message) return;
        
        message.textContent = `Una uhakika unataka kufuta mauzo ya "${productName}"?`;
        
        if (stockWarning && warningText) {
            warningText.textContent = `Idadi ya ${quantity} itarudishwa kwenye stok ya bidhaa.`;
            stockWarning.classList.remove('hidden');
        }
        
        modal.classList.remove('hidden');
        
        const closeModal = () => {
            modal.classList.add('hidden');
            this.currentSaleToDelete = null;
        };
        
        cancelBtn.onclick = closeModal;
        if (closeBtn) closeBtn.onclick = closeModal;
        
        confirmBtn.onclick = async () => {
            await this.deleteSale(this.currentSaleToDelete);
            closeModal();
        };
        
        modal.addEventListener('click', (e) => {
            if (e.target === modal || e.target.classList.contains('modal-overlay')) {
                closeModal();
            }
        });
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
                
                // Remove the row from the table
                const row = document.querySelector(`.sales-row[data-id="${saleId}"]`);
                if (row) {
                    row.remove();
                    
                    // Check if table is empty
                    const rows = document.querySelectorAll('.sales-row');
                    if (rows.length === 0) {
                        const tbody = document.getElementById('sales-tbody');
                        if (tbody) {
                            tbody.innerHTML = `
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-gray-500 text-xs">
                                        Hakuna mauzo yaliyorekodiwa bado.
                                    </td>
                                </tr>
                            `;
                        }
                    }
                }
                
                this.updateFinancialData();
            } else {
                this.showNotification(data.message || 'Kuna tatizo kufuta mauzo!', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            this.showNotification('Kuna tatizo kufuta mauzo!', 'error');
        }
    }

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

// REPLACE THE updateTotals() FUNCTION:
updateTotals() {
    const quantityInput = document.getElementById('quantity-input');
    const priceInput = document.getElementById('price-input');
    const discountInput = document.getElementById('punguzo-input');
    const totalInput = document.getElementById('total-input');
    const punguzoAinaInput = document.getElementById('punguzo-aina-input');
    const punguzoType = document.getElementById('punguzo-type');
    
    if (!quantityInput || !priceInput || !totalInput || !punguzoAinaInput || !punguzoType) return;

    const quantity = parseInt(quantityInput.value) || 0;
    const price = parseFloat(priceInput.value) || 0;
    const discount = parseFloat(discountInput.value) || 0;
    const discountType = punguzoType.value;
    
    const baseTotal = quantity * price;
    
    // ✅ CORRECT: Calculate actual discount
    const actualDiscount = this.calculateActualDiscount(discount, discountType, quantity);
    const finalTotal = baseTotal - actualDiscount;
    
    totalInput.value = Math.max(0, finalTotal);
    punguzoAinaInput.value = discountType;
    
    const kopeshaIdadi = document.getElementById('kopesha-idadi');
    const kopeshaJumla = document.getElementById('kopesha-jumla');
    const kopeshaBaki = document.getElementById('kopesha-baki');
    const kopeshaPunguzo = document.getElementById('kopesha-punguzo');
    const kopeshaPunguzoAina = document.getElementById('kopesha-punguzo-aina');
    
    if (kopeshaIdadi) kopeshaIdadi.value = quantity;
    if (kopeshaJumla) kopeshaJumla.value = finalTotal;
    if (kopeshaBaki) kopeshaBaki.value = finalTotal;
    if (kopeshaPunguzo) kopeshaPunguzo.value = discount; // Store per-item discount
    if (kopeshaPunguzoAina) kopeshaPunguzoAina.value = discountType;
}


// REPLACE THE addToCart() FUNCTION:
addToCart() {
    const bidhaaSelect = document.getElementById('bidhaaSelect');
    const quantityInput = document.getElementById('quantity-input');
    const priceInput = document.getElementById('price-input');
    const discountInput = document.getElementById('punguzo-input');
    const punguzoType = document.getElementById('punguzo-type');
    const totalInput = document.getElementById('total-input');
    
    if (!bidhaaSelect || !quantityInput || !priceInput || !totalInput) return;

    const selectedOption = bidhaaSelect.options[bidhaaSelect.selectedIndex];
    const quantity = parseInt(quantityInput.value) || 0;
    const price = parseFloat(priceInput.value) || 0;
    const discount = parseFloat(discountInput.value) || 0;
    const discountType = punguzoType ? punguzoType.value : 'bidhaa';
    const total = parseFloat(totalInput.value) || 0;
    
    if (!selectedOption.value || quantity < 1) {
        this.showNotification('Tafadhali chagua bidhaa na idadi sahihi!', 'error');
        return;
    }

    const buyingPrice = parseFloat(selectedOption.dataset.beiNunua) || 0;
    const baseTotal = price * quantity;
    
    // ✅ CORRECT: Calculate actual discount
    const actualDiscount = this.calculateActualDiscount(discount, discountType, quantity);
    
    // ✅ CORRECT: Profit calculation
    const profit = ((price - buyingPrice) * quantity) - actualDiscount;
    
    const calculatedTotal = baseTotal - actualDiscount;
    if (Math.abs(calculatedTotal - total) > 0.01) {
        this.showNotification(`Hesabu si sahihi! Inatarajiwa: ${calculatedTotal}, Ilioingizwa: ${total}`, 'error');
        return;
    }

    const productName = selectedOption.dataset.jina;
    const barcode = selectedOption.dataset.barcode || '';
    const companyName = document.querySelector('meta[name="company-name"]')?.getAttribute('content') || '';

    const cartItem = {
        jina: productName,
        bei: price,
        idadi: quantity,
        punguzo: discount, // Store the per-item discount
        punguzo_aina: discountType,
        actual_discount: actualDiscount, // Store the actual total discount
        jumla: total,
        profit: profit,
        bidhaa_id: selectedOption.value,
        barcode: barcode,
        timestamp: new Date().toISOString(),
        company_id: this.companyId,
        company_name: companyName
    };

    this.cart.push(cartItem);
    this.saveCart();
    this.updateCartCount();
    this.updateCartDisplay();
    
    this.showNotification('Bidhaa imeongezwa kwenye kikapu!', 'success');
    this.resetForm();
}

    updateCartDisplay() {
        const emptyMessage = document.getElementById('empty-cart-message');
        const cartContent = document.getElementById('cart-content');
        const cartTbody = document.getElementById('cart-tbody');
        const cartTotal = document.getElementById('cart-total');
        const companyWarning = document.getElementById('company-cart-warning');

        if (!emptyMessage || !cartContent || !cartTbody || !cartTotal) return;

        const companyCart = this.cart.filter(item => item.company_id === this.companyId);
        
        if (companyCart.length !== this.cart.length && companyWarning) {
            companyWarning.classList.remove('hidden');
            
            if (companyCart.length === 0) {
                this.cart = [];
                this.saveCart();
            }
        } else if (companyWarning) {
            companyWarning.classList.add('hidden');
        }

        if (companyCart.length === 0) {
            emptyMessage.classList.remove('hidden');
            cartContent.classList.add('hidden');
        } else {
            emptyMessage.classList.add('hidden');
            cartContent.classList.remove('hidden');

            cartTbody.innerHTML = '';
            let total = 0;

            companyCart.forEach((item, index) => {
                total += item.jumla;
                
                let displayedDiscount = item.punguzo;
                let discountLabel = item.punguzo_aina === 'bidhaa' ? 'k/bidhaa' : 'jumla';
                
                if (item.punguzo_aina === 'bidhaa') {
                    displayedDiscount = item.punguzo * item.idadi;
                }
                
                const baseTotal = item.bei * item.idadi;
                
                const row = document.createElement('tr');
                row.className = 'border-b hover:bg-gray-50 transition';
                row.innerHTML = `
                    <td class="border px-3 py-2 text-center text-xs">${index + 1}</td>
                    <td class="border px-3 py-2 text-xs">
                        ${item.jina}
                        ${item.company_name ? `<br><span class="text-xs text-blue-600">${item.company_name}</span>` : ''}
                    </td>
                    <td class="border px-3 py-2 text-center text-xs">${item.idadi}</td>
                    <td class="border px-3 py-2 text-right text-xs">${item.bei.toLocaleString()}</td>
                    <td class="border px-3 py-2 text-right text-xs">
                        ${displayedDiscount.toLocaleString()} (${discountLabel})
                    </td>
                    <td class="border px-3 py-2 text-right text-xs">${item.jumla.toLocaleString()}</td>
                    <td class="border px-3 py-2 text-center">
                        <button type="button" class="remove-cart-item text-red-500 hover:text-red-700 transition text-xs" data-index="${this.cart.findIndex(cartItem => 
                            cartItem.timestamp === item.timestamp && cartItem.bidhaa_id === item.bidhaa_id
                        )}" title="Ondoa Bidhaa">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;
                cartTbody.appendChild(row);
            });

            cartTotal.textContent = total.toLocaleString() + '/=';

            document.querySelectorAll('.remove-cart-item').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const index = parseInt(e.target.closest('.remove-cart-item').dataset.index);
                    this.showConfirmation(
                        'Unahakika unataka kuondoa bidhaa hii kwenye kikapu?',
                        () => {
                            this.removeFromCart(index);
                        }
                    );
                });
            });
        }
    }

// REPLACE THE updateBarcodeRowTotal() FUNCTION:
updateBarcodeRowTotal(row) {
    const productPrice = row.querySelector('.product-price');
    const quantityInput = row.querySelector('.quantity-input');
    const punguzoInput = row.querySelector('.punguzo-input');
    const stockInput = row.querySelector('.stock-input');
    const totalInput = row.querySelector('.total-input');
    const punguzoType = document.getElementById('punguzo-type');

    if (!productPrice || !quantityInput || !totalInput || !punguzoType) return;

    const price = parseFloat(productPrice.value) || 0;
    const quantity = parseInt(quantityInput.value) || 0;
    const punguzo = parseFloat(punguzoInput.value) || 0;
    const stock = stockInput ? parseInt(stockInput.value) || 0 : 0;
    const discountType = punguzoType.value;
    
    if (stock > 0 && quantity > stock) {
        this.showNotification('Idadi uliyoiingiza inazidi idadi iliyopo!', 'error');
        quantityInput.value = stock;
        return;
    }
    
    const baseTotal = price * quantity;
    
    // ✅ CORRECT: Calculate actual discount
    const actualDiscount = this.calculateActualDiscount(punguzo, discountType, quantity);
    const total = baseTotal - actualDiscount;
    
    totalInput.value = Math.max(0, total);
    
    this.updateBarcodeTotal();
}

// Update submitBarcodeSales method
async submitBarcodeSales() {
    const items = [];
    let hasValidItems = false;
    const punguzoType = document.getElementById('punguzo-type');
    const paymentMethodSelect = document.querySelector('#barcode-form select[name="lipa_kwa"]');
    const paymentMethod = paymentMethodSelect ? paymentMethodSelect.value : 'cash';

    if (!punguzoType) return;

    const discountType = punguzoType.value;

    document.querySelectorAll('.barcode-row').forEach(row => {
        const barcodeInput = row.querySelector('.barcode-input');
        const quantityInput = row.querySelector('.quantity-input');
        const productName = row.querySelector('.product-name');
        const productPrice = row.querySelector('.product-price');
        const punguzoInput = row.querySelector('.punguzo-input');
        const totalInput = row.querySelector('.total-input');
        
        if (barcodeInput && quantityInput && productName && productPrice && totalInput) {
            const barcode = barcodeInput.value.trim();
            const quantity = parseInt(quantityInput.value) || 0;
            const product = productName.value.trim();
            const price = parseFloat(productPrice.value) || 0;
            const punguzo = parseFloat(punguzoInput.value) || 0;
            const jumla = parseFloat(totalInput.value) || 0;
            
            if (barcode && quantity > 0 && product && price > 0) {
                const bidhaa = this.bidhaaList.find(b => b.barcode === barcode);
                
                let actualDiscount = punguzo;
                if (discountType === 'bidhaa') {
                    actualDiscount = punguzo * quantity;
                }
                
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

    try {
        const response = await fetch("{{ route('mauzo.store.barcode') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ 
                items: items, 
                punguzo_aina: discountType,
                lipa_kwa: paymentMethod // ✅ Send payment method
            })
        });

        const data = await response.json();
        
        if (data.success) {
            this.showNotification('Mauzo yamehifadhiwa! Namba ya risiti: ' + data.receipt_no, 'success');
            
            this.clearBarcodeRows();
            this.updateFinancialData();
            
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            this.showNotification(data.message || 'Hitilafu katika kuhifadhi mauzo!', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        this.showNotification('Kuna tatizo kwenye kuhifadhi mauzo!', 'error');
    }
}

async checkoutCart() {
    const companyCart = this.cart.filter(item => item.company_id === this.companyId);
    
    if (companyCart.length === 0) {
        this.showNotification('Kikapu hakina bidhaa za kampuni yako!', 'error');
        return;
    }

    // Get payment method from select
    const paymentMethodSelect = document.getElementById('kikapu-lipa-kwa');
    const paymentMethod = paymentMethodSelect ? paymentMethodSelect.value : 'cash';

    try {
        const response = await fetch("{{ route('mauzo.store.kikapu') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Company-ID': this.companyId
            },
            body: JSON.stringify({ 
                items: companyCart,
                company_id: this.companyId,
                lipa_kwa: paymentMethod // ✅ Send payment method
            })
        });

        const data = await response.json();
        
        if (data.success) {
            this.showNotification('Mauzo ya kikapu yamehifadhiwa! Namba ya risiti: ' + data.receipt_no, 'success');
            
            this.clearCart();
            this.updateFinancialData();
            
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            this.showNotification(data.message || 'Kuna tatizo kwenye kuhifadhi mauzo ya kikapu!', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        this.showNotification('Kuna tatizo kwenye kuhifadhi mauzo ya kikapu!', 'error');
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
                const quantity = parseInt(quantityInput.value) || 0;
                const product = productName.value.trim();
                const price = parseFloat(productPrice.value) || 0;
                const punguzo = parseFloat(punguzoInput.value) || 0;
                const jumla = parseFloat(totalInput.value) || 0;
                
                if (barcode && quantity > 0 && product && price > 0) {
                    const bidhaa = this.bidhaaList.find(b => b.barcode === barcode);
                    
                    let actualDiscount = punguzo;
                    if (discountType === 'bidhaa') {
                        actualDiscount = punguzo * quantity;
                    }
                    
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

    prepareKikapuKopeshaData() {
        const companyCart = this.cart.filter(item => item.company_id === this.companyId);
        
        if (companyCart.length > 0) {
            const items = companyCart.map(item => ({
                bidhaa_id: item.bidhaa_id,
                barcode: item.barcode,
                idadi: item.idadi,
                jina: item.jina,
                bei: item.bei,
                punguzo: item.punguzo,
                punguzo_aina: item.punguzo_aina,
                jumla: item.jumla,
                profit: item.profit || 0,
                company_id: this.companyId
            }));
            
            document.getElementById('kikapu-items-data').value = JSON.stringify(items);
        }
    }

    initCartDisplay() {
        this.updateCartDisplay();
    }

    initReceiptLookup() {
        const searchInput = document.getElementById('search-receipt-input');
        const printButton = document.getElementById('print-thermal-receipt');
        
        if (searchInput) {
            searchInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    this.lookupReceipt(searchInput.value.trim());
                }
            });
        }
        
        if (printButton) {
            printButton.addEventListener('click', () => {
                this.printThermalReceipt();
            });
        }
        
        document.querySelectorAll('.copy-receipt').forEach(element => {
            element.addEventListener('click', (e) => {
                const receiptNo = e.target.dataset.receipt;
                navigator.clipboard.writeText(receiptNo).then(() => {
                    this.showNotification('Namba ya risiti imenakiliwa!', 'success');
                });
            });
        });
        
        document.querySelectorAll('.print-single-receipt').forEach(button => {
            button.addEventListener('click', (e) => {
                const receiptNo = e.target.closest('.print-single-receipt').dataset.receiptNo;
                if (receiptNo) {
                    this.printSingleReceipt(receiptNo);
                }
            });
        });
    }

    initCustomerSelection() {
        const customerSelectors = ['mteja-select', 'barcode-mteja-select', 'kikapu-mteja-select'];
        
        customerSelectors.forEach(selectorId => {
            const selectElement = document.getElementById(selectorId);
            if (selectElement) {
                selectElement.addEventListener('change', (e) => {
                    this.handleCustomerSelection(e.target, selectorId);
                });
            }
        });
    }

    handleCustomerSelection(selectElement, modalType) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        
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
        if (modalType === 'mteja-select') prefix = 'kopesha-';
        else if (modalType === 'barcode-mteja-select') prefix = 'barcode-kopesha-';
        else if (modalType === 'kikapu-mteja-select') prefix = 'kikapu-kopesha-';
        
        const fields = ['jina', 'simu', 'barua-pepe', 'anapoishi'];
        fields.forEach(field => {
            const element = document.getElementById(`${prefix}${field}`);
            if (element) {
                element.value = '';
                if (field === 'jina' || field === 'simu') {
                    element.required = true;
                }
            }
        });
    }

    populateCustomerFields(modalType, jina, simu, baruaPepe, anapoishi) {
        let prefix = '';
        if (modalType === 'mteja-select') prefix = 'kopesha-';
        else if (modalType === 'barcode-mteja-select') prefix = 'barcode-kopesha-';
        else if (modalType === 'kikapu-mteja-select') prefix = 'kikapu-kopesha-';
        
        const jinaField = document.getElementById(`${prefix}jina`);
        const simuField = document.getElementById(`${prefix}simu`);
        const baruaPepeField = document.getElementById(`${prefix}barua-pepe`);
        const anapoishiField = document.getElementById(`${prefix}anapoishi`);
        
        if (jinaField) {
            jinaField.value = jina;
            jinaField.required = false;
        }
        if (simuField) {
            simuField.value = simu;
            simuField.required = false;
        }
        if (baruaPepeField) baruaPepeField.value = baruaPepe;
        if (anapoishiField) anapoishiField.value = anapoishi;
    }

    bindEvents() {
        // Modified: Save tab on click
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', (e) => {
                const tab = e.target.closest('.tab-button').dataset.tab;
                if (tab) {
                    this.showTab(tab);
                }
            });
        });

        this.bindSalesFormEvents();
        this.bindBarcodeEvents();
        this.bindSearchEvents();
        this.bindModalEvents();
        this.bindCartEvents();
        this.bindDeleteEvents();
    }

    initBidhaaSearch() {
        const bidhaaSearch = document.getElementById('bidhaaSearch');
        const bidhaaSelect = document.getElementById('bidhaaSelect');

        if (!bidhaaSearch || !bidhaaSelect) return;

        bidhaaSearch.addEventListener('focus', () => {
            bidhaaSelect.classList.remove('hidden');
        });

        bidhaaSearch.addEventListener('input', (e) => {
            const filter = e.target.value.toLowerCase();
            const options = bidhaaSelect.getElementsByTagName('option');

            for (let i = 0; i < options.length; i++) {
                const jina = options[i].dataset.jina?.toLowerCase() || '';
                const aina = options[i].dataset.aina?.toLowerCase() || '';
                const kipimo = options[i].dataset.kipimo?.toLowerCase() || '';
                const searchText = `${jina} ${aina} ${kipimo}`;

                options[i].style.display = searchText.includes(filter)
                    ? ''
                    : 'none';
            }
        });

        bidhaaSelect.addEventListener('change', () => {
            if (!bidhaaSelect.value) return;

            const selectedOption = bidhaaSelect.options[bidhaaSelect.selectedIndex];
            bidhaaSearch.value =
                `${selectedOption.dataset.jina} - ` +
                `${selectedOption.dataset.aina} - ` +
                `${selectedOption.dataset.kipimo}`;

            bidhaaSelect.classList.add('hidden');
            this.updateProductDetails();
        });

        document.addEventListener('click', (e) => {
            if (!e.target.closest('#bidhaaSearch') && !e.target.closest('#bidhaaSelect')) {
                bidhaaSelect.classList.add('hidden');
            }
        });
    }

    initBarcodeRows() {
        document.querySelectorAll('.barcode-row').forEach(row => {
            this.addBarcodeRowEvents(row);
        });
        
        const barcodeTab = document.getElementById('barcode-tab');
        if (barcodeTab) {
            barcodeTab.addEventListener('click', () => {
                setTimeout(() => {
                    const firstBarcodeInput = document.querySelector('.barcode-input');
                    if (firstBarcodeInput) {
                        firstBarcodeInput.focus();
                    }
                }, 100);
            });
        }
    }

    bindSalesFormEvents() {
        const bidhaaSelect = document.getElementById('bidhaaSelect');
        const quantityInput = document.getElementById('quantity-input');
        const discountInput = document.getElementById('punguzo-input');
        const punguzoType = document.getElementById('punguzo-type');
        const kopeshaBtn = document.getElementById('kopesha-btn');
        const addToCartBtn = document.getElementById('add-to-cart-btn');
        const salesForm = document.getElementById('sales-form');

        if (!salesForm) return;

        if (bidhaaSelect) {
            bidhaaSelect.addEventListener('change', () => {
                this.updateProductDetails();
            });
        }

        if (quantityInput) {
            quantityInput.addEventListener('input', () => {
                this.updateTotals();
            });
        }

        if (discountInput) {
            discountInput.addEventListener('input', () => {
                this.updateTotals();
            });
        }

        if (punguzoType) {
            punguzoType.addEventListener('change', () => {
                this.updateTotals();
            });
        }

        if (kopeshaBtn) {
            kopeshaBtn.addEventListener('click', () => {
                this.openKopeshaModal('regular');
            });
        }

        if (addToCartBtn) {
            addToCartBtn.addEventListener('click', () => {
                this.addToCart();
            });
        }

        salesForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const hasDoubleSale = await this.checkDoubleSale();
            
            if (hasDoubleSale) {
                this.pendingSaleData = { 
                    form: salesForm, 
                    type: 'regular'
                };
                this.showDoubleSaleModal();
                return;
            }
            
            await this.processSale(salesForm, 'regular');
        });
    }

    updateProductDetails() {
        const select = document.getElementById('bidhaaSelect');
        const priceInput = document.getElementById('price-input');
        const stockInput = document.getElementById('stock-input');
        const quantityInput = document.getElementById('quantity-input');
        
        if (!select || !priceInput || !stockInput) return;

        const selectedOption = select.options[select.selectedIndex];
        
        if (selectedOption.value) {
            const price = parseFloat(selectedOption.dataset.bei) || 0;
            const stock = parseInt(selectedOption.dataset.stock) || 0;
            const buyingPrice = parseFloat(selectedOption.dataset.beiNunua) || 0;
            
            priceInput.value = price;
            stockInput.value = stock;
            
            const kopeshaBidhaaId = document.getElementById('kopesha-bidhaa-id');
            const kopeshaBei = document.getElementById('kopesha-bei');
            if (kopeshaBidhaaId) kopeshaBidhaaId.value = selectedOption.value;
            if (kopeshaBei) kopeshaBei.value = price;
            
            if (quantityInput && parseInt(quantityInput.value) > stock) {
                quantityInput.value = stock;
            }
        } else {
            priceInput.value = '';
            stockInput.value = '';
            quantityInput.value = '';
        }
        
        this.updateTotals();
    }

    async checkDoubleSale() {
        const bidhaaId = document.getElementById('bidhaaSelect').value;
        
        if (!bidhaaId) {
            return false;
        }

        try {
            const response = await fetch(`/mauzo/check-double-sale/${bidhaaId}`);
            const data = await response.json();
            
            if (data.success && data.recent_sale) {
                return true;
            }
            return false;
        } catch (error) {
            console.error('Error checking double sale:', error);
            return false;
        }
    }

    showDoubleSaleModal() {
        const modal = document.getElementById('double-sale-modal');
        const productName = document.getElementById('bidhaaSearch').value.split(' - ')[0];
        const productNameSpan = document.getElementById('double-sale-product-name');
        const ghairiBtn = document.getElementById('cancel-double-sale');
        const uzaBtn = document.getElementById('confirm-double-sale');
        const closeBtn = document.getElementById('close-double-sale-modal');
        
        if (!modal) return;
        
        productNameSpan.textContent = productName || 'bidhaa hii';
        modal.classList.remove('hidden');
        
        const closeModal = () => {
            modal.classList.add('hidden');
            this.pendingSaleData = null;
        };
        
        ghairiBtn.onclick = closeModal;
        closeBtn.onclick = closeModal;
        
        uzaBtn.onclick = async () => {
            modal.classList.add('hidden');
            
            if (this.pendingSaleData) {
                await this.processSale(this.pendingSaleData.form, this.pendingSaleData.type);
                this.pendingSaleData = null;
            }
        };
        
        modal.addEventListener('click', (e) => {
            if (e.target === modal || e.target.classList.contains('modal-overlay')) {
                closeModal();
            }
        });
    }

    async processSale(form, type) {
        const formData = new FormData(form);
        
        const punguzoType = document.getElementById('punguzo-type');
        if (punguzoType) {
            formData.append('punguzo_aina', punguzoType.value);
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
                this.showNotification('Mauzo yamehifadhiwa kikamilifu! Namba ya risiti: ' + data.receipt_no, 'success');
                
                this.resetForm();
                this.updateStockDisplay(formData.get('bidhaa_id'));
                this.updateFinancialData();
                
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                this.showNotification(data.message || 'Kuna tatizo kwenye kuhifadhi!', 'error');
            }

        } catch (error) {
            console.error('Error:', error);
            this.showNotification('Kuna tatizo kwenye kuhifadhi!', 'error');
        }
    }

    updateStockDisplay(bidhaaId) {
        const quantity = parseInt(document.getElementById('quantity-input').value) || 0;
        const stockInput = document.getElementById('stock-input');
        const currentStock = parseInt(stockInput.value) || 0;
        stockInput.value = currentStock - quantity;
    }

    updateFinancialData() {
        fetch('/mauzo/financial-data', {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.updateFinancialDisplay(data.data);
            }
        })
        .catch(error => console.error('Error updating financial data:', error));
    }

    updateFinancialDisplay(data) {
        const elements = {
            'mapato_leo': document.getElementById('mapato-jumla'),
            'mapato_mauzo': document.getElementById('mapato-mauzo'),
            'mapato_madeni': document.getElementById('mapato-madeni'),
            'faida_leo': document.getElementById('faida-jumla'),
            'faida_mauzo': document.getElementById('faida-mauzo'),
            'faida_marejesho': document.getElementById('faida-marejesho'),
            'matumizi_leo': document.getElementById('matumizi-leo'),
            'matumizi_wiki': document.getElementById('matumizi-wiki'),
            'matumizi_jumla': document.getElementById('matumizi-jumla'),
            'fedha_leo': document.getElementById('fedha-jumla'),
            'fedha_mapato': document.getElementById('fedha-mapato'),
            'fedha_matumizi': document.getElementById('fedha-matumizi'),
            'faida_halisi': document.getElementById('faida-halisi-jumla'),
            'faida_halisi_faida': document.getElementById('faida-halisi-faida'),
            'faida_halisi_matumizi': document.getElementById('faida-halisi-matumizi'),
            'jumla_kuu': document.getElementById('jumla-kuu-jumla'),
            'jumla_kuu_mapato': document.getElementById('jumla-kuu-mapato'),
            'jumla_kuu_matumizi': document.getElementById('jumla-kuu-matumizi')
        };
        
        for (const [key, element] of Object.entries(elements)) {
            if (element && data[key]) {
                element.textContent = data[key];
            }
        }
    }

    resetForm() {
        const salesForm = document.getElementById('sales-form');
        if (!salesForm) return;

        salesForm.reset();
        
        const bidhaaSearch = document.getElementById('bidhaaSearch');
        const priceInput = document.getElementById('price-input');
        const stockInput = document.getElementById('stock-input');
        const totalInput = document.getElementById('total-input');
        const quantityInput = document.getElementById('quantity-input');
        const discountInput = document.getElementById('punguzo-input');
        const punguzoType = document.getElementById('punguzo-type');
        
        if (bidhaaSearch) bidhaaSearch.value = '';
        if (priceInput) priceInput.value = '';
        if (stockInput) stockInput.value = '';
        if (totalInput) totalInput.value = '';
        if (quantityInput) quantityInput.value = '';
        if (discountInput) discountInput.value = 0;
        if (punguzoType) punguzoType.value = 'bidhaa';
    }

    openKopeshaModal(type) {
        this.currentKopeshaType = type;
        
        let isValid = false;
        let modalId = '';
        
        if (type === 'regular') {
            modalId = 'kopesha-modal';
            const bidhaaId = document.getElementById('bidhaaSelect').value;
            const quantity = document.getElementById('quantity-input').value;
            
            if (bidhaaId && quantity && quantity > 0) {
                isValid = true;
                
                document.getElementById('kopesha-bidhaa-id').value = bidhaaId;
                document.getElementById('kopesha-idadi').value = quantity;
                document.getElementById('kopesha-jumla').value = document.getElementById('total-input').value;
                document.getElementById('kopesha-baki').value = document.getElementById('total-input').value;
                document.getElementById('kopesha-bei').value = document.getElementById('price-input').value;
                document.getElementById('kopesha-punguzo').value = document.getElementById('punguzo-input').value || 0;
                document.getElementById('kopesha-punguzo-aina').value = document.getElementById('punguzo-type').value;
            } else {
                this.showNotification('Tafadhali chagua bidhaa na idadi sahihi!', 'error');
            }
        } else if (type === 'barcode') {
            modalId = 'kopesha-barcode-modal';
            const rows = document.querySelectorAll('.barcode-row');
            let validRows = false;
            
            rows.forEach(row => {
                const barcode = row.querySelector('.barcode-input').value.trim();
                const quantity = row.querySelector('.quantity-input').value;
                if (barcode && quantity > 0) {
                    validRows = true;
                }
            });
            
            if (validRows) {
                isValid = true;
                this.prepareBarcodeKopeshaData();
            } else {
                this.showNotification('Tafadhali ingiza angalau bidhaa moja!', 'error');
            }
        } else if (type === 'kikapu') {
            modalId = 'kikapu-kopesha-modal';
            const companyCart = this.cart.filter(item => item.company_id === this.companyId);
            if (companyCart.length > 0) {
                isValid = true;
                this.prepareKikapuKopeshaData();
            } else {
                this.showNotification('Kikapu hakina bidhaa za kampuni yako!', 'error');
            }
        }

        if (isValid && modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                if (type === 'regular') this.clearCustomerFields('mteja-select');
                else if (type === 'barcode') this.clearCustomerFields('barcode-mteja-select');
                else if (type === 'kikapu') this.clearCustomerFields('kikapu-mteja-select');
                
                modal.classList.remove('hidden');
            }
        }
    }

    bindBarcodeEvents() {
        const addBarcodeRowBtn = document.getElementById('add-barcode-row');
        const barcodeForm = document.getElementById('barcode-form');
        const clearBarcodeFormBtn = document.getElementById('clear-barcode-form');
        const kopeshaBarcodeBtn = document.getElementById('kopesha-barcode-btn');

        if (addBarcodeRowBtn) {
            addBarcodeRowBtn.addEventListener('click', () => {
                this.addBarcodeRow();
            });
        }

        if (barcodeForm) {
            barcodeForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.submitBarcodeSales();
            });
        }

        if (clearBarcodeFormBtn) {
            clearBarcodeFormBtn.addEventListener('click', () => {
                this.showConfirmation(
                    'Unahakika unataka kufuta bidhaa zote zilizoscan?',
                    () => {
                        this.clearBarcodeRows();
                        this.showNotification('Bidhaa zote zimefutwa!', 'success');
                    }
                );
            });
        }

        if (kopeshaBarcodeBtn) {
            kopeshaBarcodeBtn.addEventListener('click', () => {
                this.openKopeshaModal('barcode');
            });
        }
    }

    addBarcodeRow() {
        const tbody = document.getElementById('barcode-tbody');
        if (!tbody) return;

        const newRow = document.createElement('tr');
        newRow.className = 'barcode-row';
        newRow.innerHTML = `
            <td class="px-3 py-2">
                <input type="text" name="barcode[]" placeholder="Scan barcode" 
                       class="barcode-input border border-green-200 focus:border-green-500 focus:ring-2 focus:ring-green-300 rounded-lg p-2 w-full text-xs transition-all" />
            </td>
            <td class="px-3 py-2">
                <input type="text" name="jina[]" readonly placeholder="Jina la Bidhaa" 
                       class="product-name border border-green-100 bg-gray-50 text-gray-700 rounded-lg p-2 w-full text-xs" />
            </td>
            <td class="px-3 py-2">
                <input type="number" name="bei[]" readonly placeholder="Bei" 
                       class="product-price border border-green-100 bg-gray-50 text-gray-700 rounded-lg p-2 w-full text-xs" />
            </td>
            <td class="px-3 py-2">
                <input type="number" name="idadi[]" min="1" value="1" placeholder="Idadi" 
                       class="quantity-input border border-green-200 focus:border-green-500 focus:ring-2 focus:ring-green-300 rounded-lg p-2 w-full text-xs transition-all" />
            </td>
            <td class="px-3 py-2">
                <input type="number" name="stock[]" readonly placeholder="Baki" 
                       class="stock-input border border-green-100 bg-gray-50 text-gray-700 rounded-lg p-2 w-full text-xs" />
            </td>
            <td class="px-3 py-2">
                <input type="number" name="punguzo[]" min="0" value="0" placeholder="Punguzo" 
                       class="punguzo-input border border-green-200 rounded-lg p-2 w-full text-xs" />
            </td>
            <td class="px-3 py-2">
                <input type="number" name="jumla[]" readonly placeholder="Jumla" 
                       class="total-input border border-green-100 bg-gray-50 text-gray-700 rounded-lg p-2 w-full text-xs" />
            </td>
            <td class="px-3 py-2 text-center">
                <button type="button" class="remove-barcode-row text-red-500 hover:text-red-700 p-2 rounded-full transition transform hover:scale-110" title="Futa bidhaa">
                    <i class="fas fa-trash text-xs"></i>
                </button>
            </td>
        `;
        
        tbody.appendChild(newRow);
        this.addBarcodeRowEvents(newRow);
        
        newRow.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    addBarcodeRowEvents(row) {
        const barcodeInput = row.querySelector('.barcode-input');
        const quantityInput = row.querySelector('.quantity-input');
        const punguzoInput = row.querySelector('.punguzo-input');
        const removeBtn = row.querySelector('.remove-barcode-row');

        if (barcodeInput) {
            barcodeInput.addEventListener('input', (e) => {
                if (this.barcodeScanTimeout) {
                    clearTimeout(this.barcodeScanTimeout);
                }
                
                this.barcodeScanTimeout = setTimeout(() => {
                    const value = e.target.value.trim();
                    if (value.length >= 8) {
                        this.fetchBidhaaByBarcode(e.target);
                    }
                }, 300);
            });
            
            setTimeout(() => {
                barcodeInput.focus();
            }, 100);
        }

        if (quantityInput) {
            quantityInput.addEventListener('input', () => {
                this.updateBarcodeRowTotal(row);
            });
        }

        if (punguzoInput) {
            punguzoInput.addEventListener('input', () => {
                this.updateBarcodeRowTotal(row);
            });
        }

        if (removeBtn) {
            removeBtn.addEventListener('click', () => {
                const rows = document.querySelectorAll('.barcode-row');
                if (rows.length > 1) {
                    this.showConfirmation(
                        'Unahakika unataka kuondoa bidhaa hii?',
                        () => {
                            row.remove();
                            this.updateBarcodeTotal();
                            this.showNotification('Bidhaa imeondolewa!', 'success');
                        }
                    );
                } else {
                    this.showNotification('Huwezi kufuta safu ya mwisho!', 'warning');
                }
            });
        }
    }

    fetchBidhaaByBarcode(input) {
        const barcode = input.value.trim();
        if (!barcode) return;

        const row = input.closest('.barcode-row');
        const productName = row.querySelector('.product-name');
        const productPrice = row.querySelector('.product-price');
        const stockInput = row.querySelector('.stock-input');
        const quantityInput = row.querySelector('.quantity-input');

        const product = this.bidhaaList.find(b => b.barcode === barcode);
        
        if (product) {
            if (productName) productName.value = product.jina;
            if (productPrice) productPrice.value = product.bei_kuuza;
            if (stockInput) stockInput.value = product.idadi;
            
            if (quantityInput && parseInt(quantityInput.value) > product.idadi) {
                quantityInput.value = product.idadi;
            }
            
            this.updateBarcodeRowTotal(row);
            
            row.classList.add('highlight');
            setTimeout(() => {
                row.classList.remove('highlight');
            }, 500);
            
            setTimeout(() => {
                this.addBarcodeRow();
            }, 200);
            
            this.showNotification('Bidhaa: ' + product.jina + ' imeongezwa!', 'success');
        } else {
            if (productName) productName.value = '';
            if (productPrice) productPrice.value = '';
            if (stockInput) stockInput.value = '';
            this.showNotification('Bidhaa haipatikani kwa barcode hii!', 'error');
        }
    }

    clearBarcodeRows() {
        const tbody = document.getElementById('barcode-tbody');
        if (!tbody) return;
        
        const rows = tbody.querySelectorAll('.barcode-row');
        
        for (let i = rows.length - 1; i > 0; i--) {
            rows[i].remove();
        }
        
        const firstRow = tbody.querySelector('.barcode-row');
        if (firstRow) {
            const productName = firstRow.querySelector('.product-name');
            const productPrice = firstRow.querySelector('.product-price');
            const stockInput = firstRow.querySelector('.stock-input');
            const totalInput = firstRow.querySelector('.total-input');
            const quantityInput = firstRow.querySelector('.quantity-input');
            const punguzoInput = firstRow.querySelector('.punguzo-input');
            
            if (productName) productName.value = '';
            if (productPrice) productPrice.value = '';
            if (stockInput) stockInput.value = '';
            if (totalInput) totalInput.value = '';
            if (quantityInput) quantityInput.value = 1;
            if (punguzoInput) punguzoInput.value = 0;
        }
        
        this.updateBarcodeTotal();
        
        const firstBarcodeInput = document.querySelector('.barcode-input');
        if (firstBarcodeInput) {
            firstBarcodeInput.focus();
        }
    }

    updateBarcodeTotal() {
        const barcodeTotal = document.getElementById('barcode-total');
        if (!barcodeTotal) return;

        let total = 0;
        document.querySelectorAll('.barcode-row').forEach(row => {
            const totalInput = row.querySelector('.total-input');
            if (totalInput) {
                const rowTotal = parseFloat(totalInput.value) || 0;
                total += rowTotal;
            }
        });
        
        barcodeTotal.textContent = total.toLocaleString() + '/=';
    }

    bindSearchEvents() {
        const searchSales = document.getElementById('search-sales');
        const filterDate = document.getElementById('filter-date');
        const resetFilters = document.getElementById('reset-filters');
        
        if (searchSales) {
            searchSales.addEventListener('input', (e) => {
                this.filterSalesTable();
            });
        }
        
        if (filterDate) {
            filterDate.addEventListener('change', () => {
                this.filterSalesTable();
            });
        }
        
        if (resetFilters) {
            resetFilters.addEventListener('click', () => {
                searchSales.value = '';
                filterDate.value = '';
                this.filterSalesTable();
            });
        }

        const searchProduct = document.getElementById('search-product');
        if (searchProduct) {
            searchProduct.addEventListener('input', (e) => {
                const searchTerm = e.target.value.toLowerCase();
                document.querySelectorAll('.grouped-sales-row').forEach(row => {
                    const product = row.dataset.product;
                    if (product.includes(searchTerm)) {
                        row.classList.remove('hidden');
                    } else {
                        row.classList.add('hidden');
                    }
                });
            });
        }
    }

    filterSalesTable() {
        const searchTerm = document.getElementById('search-sales').value.toLowerCase();
        const filterDate = document.getElementById('filter-date').value;
        
        document.querySelectorAll('.sales-row').forEach(row => {
            const product = row.dataset.product;
            const date = row.dataset.date;
            const rowDate = new Date(date).toISOString().split('T')[0];
            
            const matchesSearch = product.includes(searchTerm);
            const matchesDate = !filterDate || rowDate === filterDate;
            
            if (matchesSearch && matchesDate) {
                row.classList.remove('hidden');
            } else {
                row.classList.add('hidden');
            }
        });
    }

    bindModalEvents() {
        const closeModal = (modalId) => {
            const modal = document.getElementById(modalId);
            if (modal) modal.classList.add('hidden');
        };

        const closeKopeshaBtn = document.getElementById('close-kopesha-modal');
        const cancelKopeshaBtn = document.getElementById('cancel-kopesha');
        const kopeshaForm = document.getElementById('kopesha-form');
        
        if (closeKopeshaBtn) {
            closeKopeshaBtn.addEventListener('click', () => closeModal('kopesha-modal'));
        }
        if (cancelKopeshaBtn) {
            cancelKopeshaBtn.addEventListener('click', () => closeModal('kopesha-modal'));
        }
        
        if (kopeshaForm) {
            kopeshaForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.submitKopeshaForm(kopeshaForm, 'regular');
            });
        }
        
        const closeKopeshaBarcodeBtn = document.getElementById('close-kopesha-barcode-modal');
        const cancelKopeshaBarcodeBtn = document.getElementById('cancel-kopesha-barcode');
        const kopeshaBarcodeForm = document.getElementById('kopesha-barcode-form');
        
        if (closeKopeshaBarcodeBtn) {
            closeKopeshaBarcodeBtn.addEventListener('click', () => closeModal('kopesha-barcode-modal'));
        }
        if (cancelKopeshaBarcodeBtn) {
            cancelKopeshaBarcodeBtn.addEventListener('click', () => closeModal('kopesha-barcode-modal'));
        }
        
        if (kopeshaBarcodeForm) {
            kopeshaBarcodeForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.submitKopeshaForm(kopeshaBarcodeForm, 'barcode');
            });
        }
        
        const closeKikapuKopeshaBtn = document.getElementById('close-kikapu-kopesha-modal');
        const cancelKikapuKopeshaBtn = document.getElementById('cancel-kikapu-kopesha');
        const kikapuKopeshaForm = document.getElementById('kikapu-kopesha-form');
        
        if (closeKikapuKopeshaBtn) {
            closeKikapuKopeshaBtn.addEventListener('click', () => closeModal('kikapu-kopesha-modal'));
        }
        if (cancelKikapuKopeshaBtn) {
            cancelKikapuKopeshaBtn.addEventListener('click', () => closeModal('kikapu-kopesha-modal'));
        }
        
        if (kikapuKopeshaForm) {
            kikapuKopeshaForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.submitKopeshaForm(kikapuKopeshaForm, 'kikapu');
            });
        }

        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal || e.target.classList.contains('modal-overlay')) {
                    modal.classList.add('hidden');
                    
                    if (modal.id === 'double-sale-modal') {
                        this.pendingSaleData = null;
                    }
                }
            });
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal').forEach(modal => {
                    modal.classList.add('hidden');
                    
                    if (modal.id === 'double-sale-modal') {
                        this.pendingSaleData = null;
                    }
                });
            }
        });
    }

    bindCartEvents() {
        const clearCartBtn = document.getElementById('clear-cart');
        const checkoutCartBtn = document.getElementById('checkout-cart');
        const kikapuKopeshaBtn = document.getElementById('kikapu-kopesha-btn');

        if (clearCartBtn) {
            clearCartBtn.addEventListener('click', () => {
                this.showConfirmation(
                    'Unahakika unataka kufuta bidhaa zote kwenye kikapu?',
                    () => {
                        this.clearCart();
                    }
                );
            });
        }

        if (checkoutCartBtn) {
            checkoutCartBtn.addEventListener('click', async () => {
                await this.checkoutCart();
            });
        }

        if (kikapuKopeshaBtn) {
            kikapuKopeshaBtn.addEventListener('click', () => {
                this.openKopeshaModal('kikapu');
            });
        }
    }

    bindDeleteEvents() {
        // Handled by bindDeleteSaleEvents
    }

    submitKopeshaForm(form, type) {
        const formData = new FormData(form);
        
        if (type === 'barcode') {
            const itemsData = document.getElementById('barcode-items-data').value;
            if (itemsData) {
                formData.append('items', itemsData);
            }
        } else if (type === 'kikapu') {
            const itemsData = document.getElementById('kikapu-items-data').value;
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
                this.closeModal(type === 'regular' ? 'kopesha-modal' : 
                              type === 'barcode' ? 'kopesha-barcode-modal' : 
                              'kikapu-kopesha-modal');
                
                if (type === 'regular') {
                    this.resetForm();
                } else if (type === 'barcode') {
                    this.clearBarcodeRows();
                } else if (type === 'kikapu') {
                    this.clearCart();
                }
                
                this.updateFinancialData();
                
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                this.showNotification(data.message || 'Kuna tatizo kwenye kuhifadhi!', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            this.showNotification('Kuna tatizo kwenye kuhifadhi!', 'error');
        });
    }

    removeFromCart(index) {
        this.cart.splice(index, 1);
        this.saveCart();
        this.updateCartCount();
        this.updateCartDisplay();
        this.showNotification('Bidhaa imeondolewa kwenye kikapu!', 'success');
    }

    clearCart() {
        this.cart = [];
        this.saveCart();
        this.updateCartCount();
        this.updateCartDisplay();
        this.showNotification('kikapu kimefutwa!', 'success');
    }

    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) modal.classList.add('hidden');
    }

    saveCart() {
        localStorage.setItem(this.cartKey, JSON.stringify(this.cart));
    }

    updateCartCount() {
        const cartCount = document.getElementById('cart-count');
        if (cartCount) {
            const companyCartCount = this.cart.filter(item => item.company_id === this.companyId).length;
            if (companyCartCount > 0) {
                cartCount.textContent = companyCartCount;
                cartCount.classList.remove('hidden');
            } else {
                cartCount.classList.add('hidden');
            }
        }
    }

    setTodayDate() {
        const today = new Date().toISOString().split('T')[0];
        const dateInputs = document.querySelectorAll('input[type="date"]');
        dateInputs.forEach(input => {
            if (!input.value) {
                input.value = today;
            }
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
        notification.querySelector('.bg-white').className = `bg-white rounded-2xl shadow-2xl p-6 max-w-sm mx-4 border-2 ${borderColor} transform transition-all duration-300 scale-95`;
        notificationMessage.textContent = message;
        
        if (showButtons) {
            notificationButtons.classList.remove('hidden');
        } else {
            notificationButtons.classList.add('hidden');
        }
        
        notification.classList.remove('hidden');
        
        if (!showButtons) {
            setTimeout(() => {
                notification.classList.add('hidden');
            }, 3000);
        }
    }

    showConfirmation(message, confirmCallback) {
        if (confirm(message)) {
            confirmCallback();
        }
    }

    lookupReceipt(receiptNo) {
        if (!receiptNo) {
            this.showNotification('Tafadhali ingiza namba ya risiti', 'error');
            return;
        }

        const detailsDiv = document.getElementById('receipt-details');
        const noResultsDiv = document.getElementById('no-receipt-found');
        const loadingDiv = document.getElementById('receipt-loading');

        detailsDiv.classList.add('hidden');
        noResultsDiv.classList.add('hidden');
        loadingDiv.classList.remove('hidden');

        fetch(`/mauzo/receipt-data/${receiptNo}`)
            .then(response => response.json())
            .then(data => {
                loadingDiv.classList.add('hidden');

                if (data.success) {
                    this.displayReceiptDetails(data);
                    detailsDiv.classList.remove('hidden');
                } else {
                    noResultsDiv.classList.remove('hidden');
                    this.showNotification(data.message || 'Risiti haipatikani', 'error');
                }
            })
            .catch(error => {
                loadingDiv.classList.add('hidden');
                noResultsDiv.classList.remove('hidden');
                console.error('Error:', error);
                this.showNotification('Kuna tatizo kwenye kupata taarifa za risiti', 'error');
            });
    }

    displayReceiptDetails(data) {
        document.getElementById('receipt-no-display').textContent = data.receipt_no;
        document.getElementById('receipt-date-display').textContent = data.date;
        document.getElementById('receipt-items-count').textContent = data.items.length + ' bidhaa';
        document.getElementById('receipt-total-display').textContent = data.total.toLocaleString() + '/=';

        const itemsList = document.getElementById('receipt-items-list');
        itemsList.innerHTML = '';

        data.items.forEach((item, index) => {
            const itemDiv = document.createElement('div');
            itemDiv.className = 'bg-white border border-gray-200 rounded-lg p-2';
            itemDiv.innerHTML = `
                <div class="flex justify-between items-center mb-1">
                    <span class="font-medium text-black text-xs">${item.bidhaa}</span>
                    <span class="text-xs text-gray-600">${item.idadi} x ${item.bei.toLocaleString()}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-500">Jumla:</span>
                    <span class="font-semibold text-xs">${item.jumla.toLocaleString()}/=</span>
                </div>
            `;
            itemsList.appendChild(itemDiv);
        });

        this.currentReceiptNo = data.receipt_no;
    }

    printThermalReceipt() {
        if (!this.currentReceiptNo) {
            this.showNotification('Hakuna risiti iliyochaguliwa', 'error');
            return;
        }

        const printWindow = window.open(`/mauzo/thermal-receipt/${this.currentReceiptNo}`, '_blank', 'width=400,height=600');
        
        if (printWindow) {
            printWindow.focus();
        }
    }

    printSingleReceipt(receiptNo) {
        const printWindow = window.open(`/mauzo/thermal-receipt/${receiptNo}`, '_blank', 'width=400,height=600');
        
        if (printWindow) {
            printWindow.focus();
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.mauzoManager = new MauzoManager();
});
</script>
@endpush