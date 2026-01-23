@extends('layouts.app')

@section('title', 'Mauzo - DEMODAY')

@section('page-title', 'Mauzo')
@section('page-subtitle', 'Usimamizi wa mauzo - ' . now()->format('d/m/Y'))

@section('content')
<div class="space-y-4 lg:space-y-6">
    
    <!-- Notification System -->
    <div id="notification" class="fixed top-4 lg:top-6 inset-x-0 flex justify-center z-50 hidden">
        <div class="bg-white rounded-xl lg:rounded-2xl shadow-xl lg:shadow-2xl p-4 lg:p-6 max-w-sm mx-4 border transform transition-all duration-300 scale-95">
            <div class="flex flex-col items-center text-center">
                <div id="notification-icon" class="text-3xl lg:text-4xl mb-3 lg:mb-4"></div>
                <p id="notification-message" class="text-base lg:text-lg font-semibold text-black"></p>
                <div id="notification-buttons" class="mt-4 space-x-2 hidden">
                    <button id="notification-confirm" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm">Ndio, Futa</button>
                    <button id="notification-cancel" class="px-4 py-2 bg-gray-500 text-white rounded-lg text-sm">Ghairi</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Container with All Tabs -->
    <div class="bg-white rounded-xl lg:rounded-2xl shadow-lg border border-gray-100 p-4 lg:p-6 card-hover">
        <!-- Tab Navigation -->
        <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 rounded-xl p-1 lg:p-2 mb-4 lg:mb-6 shadow-md">
            <div class="flex flex-wrap gap-1 lg:gap-2" id="tab-nav">
                <button id="sehemu-tab" class="tab-button pb-2 px-3 lg:px-4 transition-colors flex items-center border-b-2 border-white text-white font-semibold whitespace-nowrap text-xs lg:text-sm" data-tab="sehemu">
                    <i class="fas fa-cash-register mr-1 lg:mr-2 text-xs"></i>Sehemu ya Mauzo
                </button>
                <button id="barcode-tab" class="tab-button pb-2 px-3 lg:px-4 transition-colors flex items-center text-emerald-100 hover:text-white whitespace-nowrap text-xs lg:text-sm" data-tab="barcode">
                    <i class="fas fa-barcode mr-1 lg:mr-2 text-xs"></i>Barcode
                </button>
                <button id="taarifa-tab" class="tab-button pb-2 px-3 lg:px-4 transition-colors flex items-center text-emerald-100 hover:text-white whitespace-nowrap text-xs lg:text-sm" data-tab="taarifa">
                    <i class="fas fa-file-alt mr-1 lg:mr-2 text-xs"></i>Taarifa
                </button>
                <button id="jumla-tab" class="tab-button pb-2 px-3 lg:px-4 transition-colors flex items-center text-emerald-100 hover:text-white whitespace-nowrap text-xs lg:text-sm" data-tab="jumla">
                    <i class="fas fa-chart-bar mr-1 lg:mr-2 text-xs"></i>Jumla
                </button>
                <button id="kikapu-tab" class="tab-button pb-2 px-3 lg:px-4 transition-colors flex items-center text-emerald-100 hover:text-white relative whitespace-nowrap text-xs lg:text-sm" data-tab="kikapu">
                    <i class="fas fa-shopping-cart mr-1 lg:mr-2 text-xs"></i>Kikapu
                    <span id="cart-count" class="absolute -top-1 -right-1 lg:-top-2 lg:-right-2 bg-red-500 text-white text-xs rounded-full h-4 w-4 lg:h-5 lg:w-5 flex items-center justify-center hidden">0</span>
                </button>
                <button id="risiti-tab" class="tab-button pb-2 px-3 lg:px-4 transition-colors flex items-center text-emerald-100 hover:text-white whitespace-nowrap text-xs lg:text-sm" data-tab="risiti">
                    <i class="fas fa-receipt mr-1 lg:mr-2 text-xs"></i>Risiti
                </button>
            </div>
        </div>

        <!-- TAB 1: Sehemu ya Mauzo -->
        <div id="sehemu-tab-content" class="space-y-4 tab-content active">
            <!-- Sales Form - Compact Version -->
            <div class="bg-white rounded-xl shadow border border-emerald-100 p-4 card-hover">
                <h2 class="text-base lg:text-lg font-bold text-emerald-800 mb-3 flex items-center">
                    <i class="fas fa-cash-register mr-2 text-emerald-600 text-sm"></i>
                    Rekodi Mauzo
                </h2>

                <form method="POST" action="{{ route('mauzo.store') }}" class="space-y-3" id="sales-form">
                    @csrf

                    <!-- Compact Product and Basic Info -->
                    <div class="grid grid-cols-1 lg:grid-cols-5 gap-3">
                        <!-- Bidhaa with Search -->
                        <div class="lg:col-span-2">
                            <label class="block text-xs font-semibold text-emerald-800 mb-1">Bidhaa</label>
                            <div class="relative">
                                <input type="text" id="bidhaaSearch" placeholder="Tafuta bidhaa..." class="w-full border border-emerald-200 rounded-lg p-2 text-sm focus:ring-2 focus:ring-emerald-200">
                                <select id="bidhaaSelect" name="bidhaa_id" size="5" class="w-full border border-emerald-200 rounded-lg p-2 text-sm focus:ring-2 focus:ring-emerald-200 hidden absolute top-full left-0 right-0 z-10 bg-white shadow-lg max-h-60 overflow-y-auto">
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

                        <!-- Idadi -->
                        <div>
                            <label class="block text-xs font-semibold text-emerald-800 mb-1">Idadi</label>
                            <input type="number" name="idadi" id="quantity-input" placeholder="0" min="1" class="w-full border border-emerald-200 rounded-lg p-2 text-sm focus:ring-2 focus:ring-emerald-200">
                        </div>

                        <!-- Bei -->
                        <div>
                            <label class="block text-xs font-semibold text-emerald-800 mb-1">Bei (Tsh)</label>
                            <input type="number" name="bei" id="price-input" readonly class="w-full bg-gray-100 border border-emerald-200 rounded-lg p-2 text-sm">
                        </div>

                        <!-- Stock -->
                        <div>
                            <label class="block text-xs font-semibold text-emerald-800 mb-1">Stock</label>
                            <input type="number" id="stock-input" readonly class="w-full bg-gray-100 border border-emerald-200 rounded-lg p-2 text-sm">
                        </div>
                    </div>

                    <!-- Punguzo Type and Total -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
                        <!-- Punguzo Type -->
                        <div>
                            <label class="block text-xs font-semibold text-emerald-800 mb-1">Aina ya Punguzo</label>
                            <div class="flex gap-2">
                                <select id="punguzo-type" class="w-full border border-emerald-200 rounded-lg p-2 text-sm focus:ring-2 focus:ring-emerald-200">
                                    <option value="bidhaa">kwa bidhaa</option>
                                    <option value="jumla">Jumla</option>
                                </select>
                            </div>
                        </div>

                        <!-- Punguzo Amount -->
                        <div>
                            <label class="block text-xs font-semibold text-emerald-800 mb-1">Punguzo (Tsh)</label>
                            <input type="number" name="punguzo" id="punguzo-input" min="0" value="0" class="w-full border border-emerald-200 rounded-lg p-2 text-sm focus:ring-2 focus:ring-emerald-200">
                        </div>

                        <!-- Jumla -->
                        <div>
                            <label class="block text-xs font-semibold text-emerald-800 mb-1">Jumla (Tsh)</label>
                            <input type="number" name="jumla" id="total-input" readonly class="w-full bg-emerald-50 border border-emerald-300 rounded-lg p-2 text-sm font-bold text-emerald-800">
                        </div>
                    </div>

                    <!-- Hidden fields for kopesha -->
                    <input type="hidden" name="baki" id="baki-input" value="0">
                    <input type="hidden" name="punguzo_aina" id="punguzo-aina-input" value="bidhaa">
                    
                    <!-- Action Buttons -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 pt-2">
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white p-3 rounded-lg font-semibold text-sm flex items-center justify-center gap-2">
                            <i class="fas fa-cash-register"></i>
                            Uza
                        </button>

                        <button type="button" id="kopesha-btn" class="bg-amber-600 hover:bg-amber-700 text-white p-3 rounded-lg font-semibold text-sm flex items-center justify-center gap-2">
                            <i class="fas fa-hand-holding-usd"></i>
                            Kopesha
                        </button>

                        <button type="button" id="add-to-cart-btn" class="bg-emerald-600 hover:bg-emerald-400 text-white p-3 rounded-lg font-semibold text-sm flex items-center justify-center gap-2">
                            <i class="fas fa-cart-plus"></i>
                            Kikapu
                        </button>
                    </div>
                </form>
            </div>

<!-- Financial Overview -->
<div class="mt-4 lg:mt-6">
    <h2 class="text-sm lg:text-base font-bold text-gray-800 flex items-center mb-2 lg:mb-3">
        <div class="relative mr-1.5">
            <div class="w-1.5 h-4 lg:w-2 lg:h-5 bg-gradient-to-b from-blue-500 to-purple-600 rounded-full"></div>
            <div class="absolute top-0.5 -right-1 w-1 h-2.5 lg:w-1.5 lg:h-3 bg-gradient-to-b from-emerald-400 to-teal-600 rounded-full"></div>
        </div>
        <span>Taarifa Fupi ya Mapato na Matumizi</span>
    </h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3" id="financial-overview">
        <!-- Mapato -->
        <div class="sm:col-span-1 group relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg lg:rounded-xl blur-md opacity-60 group-hover:opacity-80 transition duration-500"></div>
            <div class="relative bg-gradient-to-br from-amber-500 via-amber-600 to-amber-700 p-3 rounded-lg lg:rounded-xl shadow-md border border-blue-400/30 transform transition duration-300 group-hover:scale-105 group-hover:shadow-lg cursor-pointer">
                <div class="flex justify-between items-start mb-2">
                    <div class="p-1.5 bg-white/20 rounded-lg backdrop-blur-sm">
                        <i class="fas fa-money-bill-wave text-white text-sm"></i>
                    </div>
                    <i class="fas fa-arrow-right text-white/60 text-xs group-hover:translate-x-1 transition-transform"></i>
                </div>
                <div class="text-xs font-semibold text-blue-100 uppercase tracking-wide mb-1">
                    Mapato ya leo
                </div>
                @php
                    // Sales income (cash sales)
                    $mapatoMauzo = $mauzos->where('created_at', '>=', today())
                        ->sum(fn($m) => $m->jumla);
                    
                    // Debt repayment income
                    $mapatoMadeni = $marejeshos->where('tarehe', today()->toDateString())->sum('kiasi');
                    
                    // Total income = cash sales + debt repayments
                    $jumlaMapato = $mapatoMauzo + $mapatoMadeni;
                @endphp
                <div class="space-y-1 text-white text-xs">
                    <div class="flex justify-between items-center">
                        <span class="text-blue-100">Mauzo:</span>
                        <span class="font-semibold" id="mapato-mauzo">{{ number_format($mapatoMauzo) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-blue-100">Madeni:</span>
                        <span class="font-semibold" id="mapato-madeni">{{ number_format($mapatoMadeni) }}</span>
                    </div>
                    <div class="flex justify-between items-center border-t border-white/20 pt-1.5 mt-1.5">
                        <span class="text-blue-50 font-semibold">Jumla:</span>
                        <span class="font-bold text-sm" id="mapato-jumla">{{ number_format($jumlaMapato) }}</span>
                    </div>
                </div>
            </div>
        </div>

<!-- Faida -->
<div class="sm:col-span-1 group relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-r from-green-500 to-green-600 rounded-lg lg:rounded-xl blur-md opacity-60 group-hover:opacity-80 transition duration-500"></div>
    <div class="relative bg-gradient-to-br from-green-500 via-green-600 to-emerald-700 p-3 rounded-lg lg:rounded-xl shadow-md border border-green-400/30 transform transition duration-300 group-hover:scale-105 group-hover:shadow-lg cursor-pointer">
        <div class="flex justify-between items-start mb-2">
            <div class="p-1.5 bg-white/20 rounded-lg backdrop-blur-sm">
                <i class="fas fa-chart-line text-white text-sm"></i>
            </div>
            <i class="fas fa-arrow-right text-white/60 text-xs group-hover:translate-x-1 transition-transform"></i>
        </div>
        <div class="text-xs font-semibold text-green-100 uppercase tracking-wide mb-1">
            Faida ya leo
        </div>
@php
    // Profit from cash sales
    $faidaMauzo = 0;
    $todayMauzos = $mauzos->where('created_at', '>=', today());
    foreach($todayMauzos as $mauzo) {
        $buyingPrice = $mauzo->bidhaa->bei_nunua ?? 0;
        $sellingPrice = $mauzo->bei;
        $quantity = $mauzo->idadi;
        
        $actualDiscount = $mauzo->punguzo;
        if ($mauzo->punguzo_aina === 'bidhaa') {
            $actualDiscount = $mauzo->punguzo * $quantity;
        }
        
        $faidaMauzo += ($sellingPrice - $buyingPrice) * $quantity - $actualDiscount;
    }
    
    // Profit from debt repayments - SIMPLE CALCULATION
    $faidaMarejesho = 0;
    $todayMarejeshos = $marejeshos->where('tarehe', today()->toDateString());
    
    foreach($todayMarejeshos as $marejesho) {
        if(isset($marejesho->madeni) && isset($marejesho->madeni->bidhaa)) {
            $debt = $marejesho->madeni;
            
            // SIMPLE: Profit = (Actual selling price - Buying price) per item
            // jumla should already be the discounted total price
            
            $buyingPrice = $debt->bidhaa->bei_nunua ?? 0;
            $quantity = $debt->idadi;
            
            // Total buying cost
            $totalBuyingCost = $buyingPrice * $quantity;
            
            // jumla is the actual selling price (after any discount)
            $actualSellingPrice = $debt->jumla;
            
            // Profit = Actual selling price - Total buying cost
            $profit = $actualSellingPrice - $totalBuyingCost;
            
            $faidaMarejesho += $profit;
        }
    }
    
    // Total profit = cash sales profit + debt repayment profit
    $jumlaFaida = $faidaMauzo + $faidaMarejesho;
@endphp
        <div class="space-y-1 text-white text-xs">
            <div class="flex justify-between items-center">
                <span class="text-green-100">Mauzo:</span>
                <span class="font-semibold" id="faida-mauzo">{{ number_format($faidaMauzo) }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-green-100">Marejesho:</span>
                <span class="font-semibold" id="faida-marejesho">{{ number_format($faidaMarejesho) }}</span>
            </div>
            <div class="flex justify-between items-center border-t border-white/20 pt-1.5 mt-1.5">
                <span class="text-green-50 font-semibold">Jumla:</span>
                <span class="font-bold text-sm" id="faida-jumla">{{ number_format($jumlaFaida) }}</span>
            </div>
        </div>
    </div>
</div>
        <!-- Matumizi -->
        <div class="sm:col-span-1 group relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-amber-500 to-amber-600 rounded-lg lg:rounded-xl blur-md opacity-60 group-hover:opacity-80 transition duration-500"></div>
            <div class="relative bg-gradient-to-br from-amber-500 via-amber-600 to-orange-700 p-3 rounded-lg lg:rounded-xl shadow-md border border-amber-400/30 transform transition duration-300 group-hover:scale-105 group-hover:shadow-lg cursor-pointer">
                <div class="flex justify-between items-start mb-2">
                    <div class="p-1.5 bg-white/20 rounded-lg backdrop-blur-sm">
                        <i class="fas fa-receipt text-white text-sm"></i>
                    </div>
                    <i class="fas fa-arrow-right text-white/60 text-xs group-hover:translate-x-1 transition-transform"></i>
                </div>
                <div class="text-xs font-semibold text-amber-100 uppercase tracking-wide mb-1">Matumizi</div>
                @php
                    $matumiziLeo = $matumizi->where('created_at', '>=', today())->sum('gharama');
                    $matumiziWiki = $matumizi->where('created_at', '>=', now()->startOfWeek())->sum('gharama');
                    $matumiziJumla = $matumizi->sum('gharama');
                @endphp
                <div class="space-y-1 text-white text-xs">
                    <div class="flex justify-between items-center">
                        <span class="text-amber-100">Leo:</span>
                        <span class="font-semibold" id="matumizi-leo">{{ number_format($matumiziLeo) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-amber-100">Wiki hii:</span>
                        <span class="font-semibold" id="matumizi-wiki">{{ number_format($matumiziWiki) }}</span>
                    </div>
                    <div class="flex justify-between items-center border-t border-white/20 pt-1.5 mt-1.5">
                        <span class="text-amber-50 font-semibold">Jumla:</span>
                        <span class="font-bold text-sm" id="matumizi-jumla">{{ number_format($matumiziJumla) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fedha Leo -->
        <div class="sm:col-span-1 group relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-cyan-500 to-cyan-700 rounded-lg lg:rounded-xl blur-md opacity-60 group-hover:opacity-80 transition duration-500"></div>
            <div class="relative bg-gradient-to-br from-emerald-500 via-emerald-600 to-emerald-700 p-3 rounded-lg lg:rounded-xl shadow-md border border-cyan-400/30 transform transition duration-300 group-hover:scale-105 group-hover:shadow-lg cursor-pointer">
                <div class="flex justify-between items-start mb-2">
                    <div class="p-1.5 bg-white/20 rounded-lg backdrop-blur-sm">
                        <i class="fas fa-wallet text-white text-sm"></i>
                    </div>
                    <i class="fas fa-arrow-right text-white/60 text-xs group-hover:translate-x-1 transition-transform"></i>
                </div>
                <div class="text-xs font-semibold text-cyan-100 uppercase tracking-wide mb-1">Fedha Leo</div>
                @php
                    // Cash in hand today = Cash sales + Debt repayments - Expenses
                    $mauzoLeo = $mauzos->where('created_at', '>=', today())->sum('jumla');
                    $mapatoMadeni = $marejeshos->where('tarehe', today()->toDateString())->sum('kiasi');
                    $matumiziLeo = $matumizi->where('created_at', '>=', today())->sum('gharama');
                    
                    $fedhaLeo = ($mauzoLeo + $mapatoMadeni) - $matumiziLeo;
                @endphp
                <div class="space-y-1 text-white text-xs">
                    <div class="flex justify-between items-center">
                        <span class="text-cyan-100">Mapato:</span>
                        <span class="font-semibold" id="fedha-mapato">{{ number_format($mauzoLeo + $mapatoMadeni) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-cyan-100">Matumizi:</span>
                        <span class="font-semibold" id="fedha-matumizi">{{ number_format($matumiziLeo) }}</span>
                    </div>
                    <div class="flex justify-between items-center border-t border-white/20 pt-1.5 mt-1.5">
                        <span class="text-cyan-50 font-semibold">Jumla:</span>
                        <span class="font-bold text-sm" id="fedha-jumla">{{ number_format($fedhaLeo) }}</span>
                    </div>
                </div>
            </div>
        </div>
<!-- Faida Halisi -->
<div class="sm:col-span-1 group relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-r from-amber-500 to-amber-600 rounded-lg lg:rounded-xl blur-md opacity-60 group-hover:opacity-80 transition duration-500"></div>
    <div class="relative bg-gradient-to-br from-amber-500 via-amber-600 to-orange-700 p-3 rounded-lg lg:rounded-xl shadow-md border border-amber-400/30 transform transition duration-300 group-hover:scale-105 group-hover:shadow-lg cursor-pointer">
        <div class="flex justify-between items-start mb-2">
            <div class="p-1.5 bg-white/20 rounded-lg backdrop-blur-sm">
                <i class="fas fa-chart-pie text-white text-sm"></i>
            </div>
            <i class="fas fa-arrow-right text-white/60 text-xs group-hover:translate-x-1 transition-transform"></i>
        </div>
        <div class="text-xs font-semibold text-teal-100 uppercase tracking-wide mb-1">Faida Halisi</div>
        @php
            // Use the SAME $jumlaFaida from "Faida ya leo" section
            $matumiziLeo = $matumizi->where('created_at', '>=', today())->sum('gharama');
            $faidaHalisi = $jumlaFaida - $matumiziLeo;
        @endphp
        <div class="space-y-1 text-white text-xs">
            <div class="flex justify-between items-center">
                <span class="text-teal-100">Faida:</span>
                <span class="font-semibold" id="faida-halisi-faida">{{ number_format($jumlaFaida) }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-teal-100">Matumizi:</span>
                <span class="font-semibold" id="faida-halisi-matumizi">{{ number_format($matumiziLeo) }}</span>
            </div>
            <div class="flex justify-between items-center border-t border-white/20 pt-1.5 mt-1.5">
                <span class="text-teal-50 font-semibold">Halisi:</span>
                <span class="font-bold text-sm" id="faida-halisi-jumla">{{ number_format($faidaHalisi) }}</span>
            </div>
        </div>
    </div>
</div>

        <!-- Jumla Kuu -->
        <div class="sm:col-span-1 group relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-rose-500 to-rose-600 rounded-lg lg:rounded-xl blur-md opacity-60 group-hover:opacity-80 transition duration-500"></div>
            <div class="relative bg-gradient-to-br from-green-500 via-green-600 to-green-700 p-3 rounded-lg lg:rounded-xl shadow-md border border-rose-400/30 transform transition duration-300 group-hover:scale-105 group-hover:shadow-lg cursor-pointer">
                <div class="flex justify-between items-start mb-2">
                    <div class="p-1.5 bg-white/20 rounded-lg backdrop-blur-sm">
                        <i class="fas fa-chart-bar text-white text-sm"></i>
                    </div>
                    <i class="fas fa-arrow-right text-white/60 text-xs group-hover:translate-x-1 transition-transform"></i>
                </div>
                <div class="text-xs font-semibold text-rose-100 uppercase tracking-wide mb-1">Jumla Kuu</div>
                @php
                    // All-time calculations: INCOME - EXPENSES
                    $allMauzos = $mauzos;
                    $allMarejeshos = $marejeshos;
                    
                    // Total income all time = Cash sales + All debt repayments
                    $totalCashSales = $allMauzos->sum('jumla');
                    $totalDebtRepayments = $allMarejeshos->sum('kiasi');
                    $totalMapato = $totalCashSales + $totalDebtRepayments;
                    
                    // Total expenses all time
                    $totalMatumizi = $matumizi->sum('gharama');
                    
                    // Net income all time = Total income - Total expenses
                    $jumlaKuu = $totalMapato - $totalMatumizi;
                @endphp
                <div class="space-y-1 text-white text-xs">
                    <div class="flex justify-between items-center">
                        <span class="text-rose-100">Mapato:</span>
                        <span class="font-semibold" id="jumla-kuu-mapato">{{ number_format($totalMapato) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-rose-100">Matumizi:</span>
                        <span class="font-semibold" id="jumla-kuu-matumizi">{{ number_format($totalMatumizi) }}</span>
                    </div>
                    <div class="flex justify-between items-center border-t border-white/20 pt-1.5 mt-1.5">
                        <span class="text-rose-50 font-semibold">Jumla:</span>
                        <span class="font-bold text-sm" id="jumla-kuu-jumla">{{ number_format($jumlaKuu) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
        </div>

        <!-- TAB 2: Barcode Sales -->
        <div id="barcode-tab-content" class="tab-content hidden">
            <div class="rounded-xl shadow border border-green-100 bg-white p-4 card-hover">
                <!-- Header -->
                <div class="flex items-center mb-4">
                    <div class="bg-green-600 text-white p-3 rounded-full shadow">
                        <i class="fas fa-barcode"></i>
                    </div>
                    <h2 class="ml-3 text-base lg:text-lg font-bold text-black tracking-wide">
                        Mauzo kwa Barcode
                    </h2>
                </div>

                <!-- Barcode Form -->
                <form id="barcode-form" class="space-y-3">
                    @csrf
                    
                    <!-- Table -->
                    <div class="overflow-x-auto rounded-lg shadow-sm border border-green-200 bg-white/80">
                        <table class="w-full table-auto border-collapse text-sm">
                            <thead class="bg-green-400/70 text-black text-xs uppercase">
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
                                               class="barcode-input border border-green-200 focus:border-green-500 focus:ring-2 focus:ring-green-300 rounded-lg p-2 w-full text-xs transition-all" 
                                               autofocus />
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
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Barcode Action Buttons -->
                    <div class="flex flex-wrap gap-2">
                        <button type="button" id="add-barcode-row" class="bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded-lg flex items-center transition text-xs">
                            <i class="fas fa-plus mr-2"></i>
                            <span>Ongeza Safu Mpya</span>
                        </button>
                        
                        <button type="button" id="kopesha-barcode-btn" class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-2 rounded-lg flex items-center transition text-xs">
                            <i class="fas fa-hand-holding-usd mr-2"></i>
                            <span>Kopesha Bidhaa</span>
                        </button>
                        
                        <button type="button" id="clear-barcode-form" class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-2 rounded-lg flex items-center transition text-xs">
                            <i class="fas fa-times mr-2"></i>
                            <span>Futa Yote</span>
                        </button>
                    </div>

                    <!-- Total & Action -->
                    <div class="flex flex-col lg:flex-row justify-between items-center gap-3 pt-2 border-t border-gray-200">
                        <div class="text-sm font-semibold text-black">
                            Jumla ya Mauzo: 
                            <span class="text-green-700 font-bold" id="barcode-total">0</span>
                        </div>

                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold shadow flex items-center transition-all duration-300 hover:scale-[1.02] text-sm">
                            <i class="fas fa-check mr-2"></i>
                            Uza Bidhaa
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- TAB 3: Taarifa Fupi -->
        <div id="taarifa-tab-content" class="tab-content hidden">
            <div class="bg-emerald-50 rounded-lg shadow border p-4 card-hover">
                <h2 class="text-base font-semibold mb-3 flex items-center text-black">
                    <i class="fas fa-file-alt mr-2 text-emerald-600 text-sm"></i>
                    Taarifa Fupi ya Mauzo
                </h2>

                <!-- Search and Filter -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 mb-3">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400 text-xs"></i>
                        <input type="text" id="search-sales" placeholder="Tafuta kwa jina la bidhaa..." class="pl-10 w-full border border-gray-300 rounded-lg p-2 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
                    </div>
                    
                    <div class="relative">
                        <i class="fas fa-calendar absolute left-3 top-3 text-gray-400 text-xs"></i>
                        <input type="date" id="filter-date" class="pl-10 w-full border border-gray-300 rounded-lg p-2 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
                    </div>
                    
                    <button id="reset-filters" class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-2 rounded-lg text-xs">
                        <i class="fas fa-redo mr-1"></i> Safisha Filter
                    </button>
                </div>

                <!-- Sales Table -->
                <div class="overflow-x-auto rounded-lg shadow-sm border">
                    <table class="w-full table-auto border-collapse text-xs">
                        <thead>
                            <tr class="bg-emerald-600">
                                <th class="border px-3 py-2 text-left text-white">Tarehe</th>
                                <th class="border px-3 py-2 text-left text-white">Risiti</th>
                                <th class="border px-3 py-2 text-left text-white">Bidhaa</th>
                                <th class="border px-3 py-2 text-left text-white">Idadi</th>
                                <th class="border px-3 py-2 text-left text-white">Bei</th>
                                <th class="border px-3 py-2 text-left text-white">Punguzo</th>
                                <th class="border px-3 py-2 text-left text-white">Faida</th>
                                <th class="border px-3 py-2 text-left text-white">Jumla</th>
                                <th class="border px-3 py-2 text-left text-white">Vitendo</th>
                            </tr>
                        </thead>
                        <tbody id="sales-tbody">
                            @php $today = \Carbon\Carbon::today()->format('Y-m-d'); @endphp
                            @forelse($mauzos as $item)
                                @php 
                                    $itemDate = $item->created_at->format('Y-m-d');
                                    $buyingPrice = $item->bidhaa->bei_nunua ?? 0;
                                    $faida = ($item->bei - $buyingPrice) * $item->idadi - $item->punguzo;
                                    $total = $item->jumla + $item->punguzo;
                                @endphp
                                <tr class="sales-row" data-product="{{ strtolower($item->bidhaa->jina) }}" data-date="{{ $itemDate }}">
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
                                    <td class="border px-3 py-2 text-right">{{ number_format($item->punguzo) }}</td>
                                    <td class="border px-3 py-2 text-right">{{ number_format($faida) }}</td>
                                    <td class="border px-3 py-2 text-right">{{ number_format($total) }}</td>
                                    <td class="border px-3 py-2 text-center">
                                        <div class="flex gap-1 justify-center">
                                            @if($item->receipt_no)
                                            <button type="button" class="print-single-receipt bg-blue-200 hover:bg-blue-400 text-gray-700 px-2 py-1 rounded-lg flex items-center justify-center transition text-xs" data-receipt-no="{{ $item->receipt_no }}">
                                                <i class="fas fa-print mr-1 text-xs"></i>
                                            </button>
                                            @endif
                                            <button type="button" class="delete-sale-btn bg-red-200 hover:bg-red-400 text-gray-700 px-2 py-1 rounded-lg flex items-center justify-center transition text-xs" data-id="{{ $item->id }}">
                                                <i class="fas fa-trash mr-1 text-xs"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-gray-500 text-xs">Hakuna mauzo yaliyorekodiwa bado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($mauzos->hasPages())
                <div class="mt-4">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                        <!-- Pagination Info -->
                        <div class="text-xs text-gray-600">
                            @php
                                $start = ($mauzos->currentPage() - 1) * $mauzos->perPage() + 1;
                                $end = min($mauzos->currentPage() * $mauzos->perPage(), $mauzos->total());
                            @endphp
                            Onyesha {{ $start }} - {{ $end }} ya {{ $mauzos->total() }} mauzo
                        </div>

                        <!-- Pagination Links -->
                        <nav class="flex items-center space-x-1">
                            <!-- Previous Button -->
                            @if($mauzos->onFirstPage())
                                <span class="px-3 py-1 rounded-lg border text-gray-400 text-xs cursor-not-allowed">
                                    <i class="fas fa-chevron-left mr-1"></i> Nyuma
                                </span>
                            @else
                                <a href="{{ $mauzos->previousPageUrl() }}" class="px-3 py-1 rounded-lg border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs transition flex items-center">
                                    <i class="fas fa-chevron-left mr-1"></i> Nyuma
                                </a>
                            @endif

                            <!-- Page Numbers -->
                            <div class="flex items-center space-x-1">
                                @foreach($mauzos->getUrlRange(1, $mauzos->lastPage()) as $page => $url)
                                    @if($page == $mauzos->currentPage())
                                        <span class="px-3 py-1 rounded-lg bg-emerald-600 text-white font-semibold text-xs">
                                            {{ $page }}
                                        </span>
                                    @else
                                        <a href="{{ $url }}" class="px-3 py-1 rounded-lg border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs transition">
                                            {{ $page }}
                                        </a>
                                    @endif
                                @endforeach
                            </div>

                            <!-- Next Button -->
                            @if($mauzos->hasMorePages())
                                <a href="{{ $mauzos->nextPageUrl() }}" class="px-3 py-1 rounded-lg border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs transition flex items-center">
                                    Mbele <i class="fas fa-chevron-right ml-1"></i>
                                </a>
                            @else
                                <span class="px-3 py-1 rounded-lg border text-gray-400 text-xs cursor-not-allowed">
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
            <div class="bg-emerald-50 rounded-lg shadow border p-4 card-hover">
                <h2 class="text-base font-bold mb-3 flex items-center text-black">
                    <i class="fas fa-chart-bar mr-2 text-emerald-600"></i>
                    Mauzo ya Jumla
                </h2>

                <!-- Search Area -->
                <div class="mb-3">
                    <input type="text" id="search-product" placeholder="Tafuta bidhaa..." class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 transition">
                </div>

                <!-- Sales Summary Table -->
                <div class="overflow-x-auto rounded-lg shadow-sm border">
                    <table class="w-full border-collapse text-sm" id="grouped-sales-table">
                        <thead class="bg-emerald-600">
                            <tr>
                                <th class="border px-3 py-2 text-left text-white text-xs">Tarehe</th>
                                <th class="border px-3 py-2 text-left text-white text-xs">Bidhaa</th>
                                <th class="border px-3 py-2 text-left text-white text-xs">Idadi</th>
                                <th class="border px-3 py-2 text-left text-white text-xs">Punguzo</th>
                                <th class="border px-3 py-2 text-left text-white text-xs">Jumla</th>
                                <th class="border px-3 py-2 text-left text-white text-xs">Faida</th>
                            </tr>
                        </thead>
                        <tbody id="grouped-sales-tbody">
                            @php
                                $groupedSales = [];
                                foreach($mauzos as $sale) {
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
                                    $groupedSales[$key]['punguzo'] += $sale->punguzo;
                                    $groupedSales[$key]['jumla'] += $sale->jumla + $sale->punguzo;
                                    $buyingPrice = $sale->bidhaa->bei_nunua ?? 0;
                                    $profitPerUnit = $sale->bei - $buyingPrice;
                                    $groupedSales[$key]['faida'] += $profitPerUnit * $sale->idadi - $sale->punguzo;
                                }
                            @endphp
                            
                            @foreach($groupedSales as $sale)
                            <tr class="grouped-sales-row" data-product="{{ strtolower($sale['jina']) }}">
                                <td class="border px-3 py-2 text-xs">{{ $sale['tarehe'] }}</td>
                                <td class="border px-3 py-2 text-xs">{{ $sale['jina'] }}</td>
                                <td class="border px-3 py-2 text-center text-xs">{{ $sale['idadi'] }}</td>
                                <td class="border px-3 py-2 text-right text-xs">{{ number_format($sale['punguzo']) }}</td>
                                <td class="border px-3 py-2 text-right text-xs">{{ number_format($sale['jumla']) }}</td>
                                <td class="border px-3 py-2 text-right text-xs">{{ number_format($sale['faida']) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

<!-- TAB 5: Kikapu -->
<div id="kikapu-tab-content" class="tab-content hidden">
    <div class="bg-white rounded-xl shadow border border-gray-100 p-4 card-hover">
        <h2 class="text-base font-bold mb-3 flex items-center text-black">
            <i class="fas fa-shopping-cart mr-2 text-blue-600"></i>
            Bidhaa Zilizo Kwenye Kikapu
        </h2>

        <!-- Company Cart Warning (hidden by default) -->
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
            <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                <table class="w-full text-xs">
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

            <!-- Total -->
            <div class="flex justify-between items-center my-3">
                <div class="text-left font-semibold text-black text-base">
                    Jumla ya gharama: 
                    <span class="text-emerald-600" id="cart-total">0</span>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-wrap gap-2">
                <button id="clear-cart" class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-2 rounded-lg flex items-center transition shadow-sm text-xs">
                    <i class="fas fa-trash mr-2"></i>
                    Futa Kikapu
                </button>
                <button id="kikapu-kopesha-btn" class="bg-amber-600 hover:bg-amber-700 text-white px-3 py-2 rounded-lg flex items-center transition shadow-sm text-xs">
                    <i class="fas fa-hand-holding-usd mr-2"></i>
                    Kopesha
                </button>
                <button id="checkout-cart" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-2 rounded-lg flex items-center transition shadow-sm text-xs">
                    <i class="fas fa-check mr-2"></i>
                    Funga Kapu
                </button>
            </div>
        </div>
    </div>
</div>

        <!-- TAB 6: Risiti -->
        <div id="risiti-tab-content" class="tab-content hidden">
            <div class="bg-white rounded-xl shadow border p-4 card-hover">
                <h2 class="text-base font-bold mb-3 flex items-center text-black">
                    <i class="fas fa-receipt mr-2 text-emerald-600"></i>
                    Chapisha Risiti
                </h2>

                <!-- Search Receipt -->
                <div class="mb-4">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        <input type="text" id="search-receipt-input" placeholder="Weka namba ya risiti (MS-20260110-0001)..." class="pl-10 w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                    <div class="mt-1 text-xs text-gray-500 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                        Namba ya risiti inapatikana kwenye tab ya "Taarifa"
                    </div>
                </div>

                <!-- Receipt Details -->
                <div id="receipt-details" class="hidden">
                    <div class="bg-gray-50 rounded-lg border border-gray-200 p-3 mb-3">
                        <div class="flex justify-between items-center mb-1">
                            <span class="font-semibold text-black text-xs">Risiti No:</span>
                            <span id="receipt-no-display" class="font-mono font-bold text-emerald-700 text-xs"></span>
                        </div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="font-semibold text-black text-xs">Tarehe:</span>
                            <span id="receipt-date-display" class="text-xs"></span>
                        </div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="font-semibold text-black text-xs">Idadi ya Bidhaa:</span>
                            <span id="receipt-items-count" class="text-xs"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="font-semibold text-black text-xs">Jumla:</span>
                            <span id="receipt-total-display" class="font-bold text-sm"></span>
                        </div>
                    </div>

                    <!-- Items List -->
                    <div class="mb-3">
                        <h3 class="font-semibold text-black text-xs mb-2">Bidhaa:</h3>
                        <div id="receipt-items-list" class="space-y-2 max-h-40 overflow-y-auto">
                            <!-- Items will be populated here -->
                        </div>
                    </div>

                    <!-- Print Button -->
                    <div class="flex justify-center">
                        <button id="print-thermal-receipt" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-semibold flex items-center gap-2 shadow text-sm">
                            <i class="fas fa-print"></i>
                            Chapisha Risiti
                        </button>
                    </div>
                </div>

                <!-- No Results Message -->
                <div id="no-receipt-found" class="hidden text-center py-6">
                    <i class="fas fa-receipt text-3xl text-gray-400 mb-3"></i>
                    <p class="text-gray-600 text-sm">Hakuna risiti iliyopatikana.</p>
                    <p class="text-xs text-gray-500 mt-1">Ingiza namba ya risiti ili kuona taarifa</p>
                </div>

                <!-- Loading -->
                <div id="receipt-loading" class="hidden text-center py-6">
                    <i class="fas fa-spinner fa-spin text-xl text-emerald-600 mb-3"></i>
                    <p class="text-gray-600 text-sm">Inatafuta taarifa...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Kopesha Modal -->
<div id="kopesha-modal" class="modal fixed inset-0 z-50 flex items-center justify-center hidden p-2">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-xl shadow-xl w-full max-w-sm mx-2 z-50 max-h-[85vh] overflow-y-auto">
        <div class="sticky top-0 bg-gradient-to-r from-amber-500 to-amber-600 p-3 text-white flex items-center">
            <i class="fas fa-hand-holding-usd mr-2"></i>
            <h2 class="text-base font-semibold">Kopesha Bidhaa</h2>
            <button type="button" id="close-kopesha-modal" class="ml-auto text-white hover:text-gray-200 text-lg">
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

            <!-- Mteja Select -->
            <div>
                <label class="block text-xs font-semibold mb-1 text-black">Mteja Aliyesajiliwa</label>
                <select id="mteja-select" class="w-full border border-amber-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-amber-300 focus:border-amber-400 hover:border-amber-400 transition">
                    <option value="">-- Mteja Mpya --</option>
                    @foreach($wateja as $m)
                        <option value="{{ $m->id }}" data-jina="{{ $m->jina }}" data-simu="{{ $m->simu }}" data-barua_pepe="{{ $m->barua_pepe }}" data-anapoishi="{{ $m->anapoishi }}">
                            {{ $m->jina }} - {{ $m->simu }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- New Customer Info -->
            <div>
                <label class="block text-black text-xs font-semibold mb-1">Jina la Mkopaji *</label>
                <input type="text" name="jina_mkopaji" id="kopesha-jina" class="w-full border border-amber-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-amber-300 focus:border-amber-400 transition" required>
            </div>

            <div>
                <label class="block text-black text-xs font-semibold mb-1">Namba ya Simu *</label>
                <input type="text" name="simu" id="kopesha-simu" class="w-full border border-amber-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-amber-300 focus:border-amber-400 transition" required>
            </div>

            <div>
                <label class="block text-black text-xs font-semibold mb-1">Barua Pepe</label>
                <input type="email" name="barua_pepe" id="kopesha-barua-pepe" class="w-full border border-amber-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-amber-300 focus:border-amber-400 transition">
            </div>

            <div>
                <label class="block text-black text-xs font-semibold mb-1">Anapoishi</label>
                <input type="text" name="anapoishi" id="kopesha-anapoishi" class="w-full border border-amber-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-amber-300 focus:border-amber-400 transition">
            </div>

            <div>
                <label class="block text-black text-xs font-semibold mb-1">Tarehe ya Malipo *</label>
                <input type="date" name="tarehe_malipo" class="w-full border border-amber-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-amber-300 focus:border-amber-400 transition" required>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-2 pt-3 border-t border-gray-200">
                <button type="button" id="cancel-kopesha" class="bg-gray-400 hover:bg-gray-500 px-4 py-2 rounded-lg text-white font-semibold transition text-sm">
                    Funga
                </button>
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 px-4 py-2 rounded-lg text-white font-semibold flex items-center gap-1 transition shadow text-sm">
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
    <div class="modal-content bg-white rounded-xl shadow-xl w-full max-w-sm mx-2 z-50">
        <div class="bg-gradient-to-r from-amber-500 to-amber-600 p-3 text-white flex items-center">
            <i class="fas fa-hand-holding-usd mr-2"></i>
            <h2 class="text-base font-semibold">Kopesha Bidhaa za Barcode</h2>
            <button type="button" id="close-kopesha-barcode-modal" class="ml-auto text-white hover:text-gray-200 text-lg">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="kopesha-barcode-form" action="{{ route('mauzo.store.kopesha') }}" method="POST" class="p-4 space-y-3">
            @csrf
            
            <div>
                <label class="block text-xs font-semibold mb-1 text-black">Chagua Mteja</label>
                <select id="barcode-mteja-select" class="w-full border border-amber-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-amber-300 focus:border-amber-400 transition">
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

            <!-- New Customer Info -->
            <div>
                <label class="block text-black text-xs font-semibold mb-1">Jina la Mkopaji *</label>
                <input type="text" name="jina_mkopaji" id="barcode-kopesha-jina" class="w-full border border-amber-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-amber-300 focus:border-amber-400 transition" required>
            </div>

            <div>
                <label class="block text-black text-xs font-semibold mb-1">Namba ya Simu *</label>
                <input type="text" name="simu" id="barcode-kopesha-simu" class="w-full border border-amber-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-amber-300 focus:border-amber-400 transition" required>
            </div>

            <div>
                <label class="block text-black text-xs font-semibold mb-1">Barua Pepe</label>
                <input type="email" name="barua_pepe" id="barcode-kopesha-barua-pepe" class="w-full border border-amber-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-amber-300 focus:border-amber-400 transition">
            </div>

            <div>
                <label class="block text-black text-xs font-semibold mb-1">Anapoishi</label>
                <input type="text" name="anapoishi" id="barcode-kopesha-anapoishi" class="w-full border border-amber-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-amber-300 focus:border-amber-400 transition">
            </div>

            <div>
                <label class="block text-black text-xs font-semibold mb-1">Tarehe ya Malipo *</label>
                <input type="date" name="tarehe_malipo" class="w-full border border-amber-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-amber-300 focus:border-amber-400 transition" required>
            </div>

            <input type="hidden" name="items" id="barcode-items-data">

            <div class="flex justify-end gap-2 pt-3 border-t border-gray-200">
                <button type="button" id="cancel-kopesha-barcode" class="bg-gray-400 hover:bg-gray-500 px-4 py-2 rounded-lg text-white font-semibold transition text-sm">
                    Funga
                </button>
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 px-4 py-2 rounded-lg text-white font-semibold flex items-center gap-1 transition shadow text-sm">
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
    <div class="modal-content bg-white rounded-xl shadow-xl w-full max-w-sm mx-2 z-50">
        <div class="bg-gradient-to-r from-amber-500 to-amber-600 p-3 text-white flex items-center">
            <i class="fas fa-hand-holding-usd mr-2"></i>
            <h2 class="text-base font-semibold">Kopesha Bidhaa za Kikapu</h2>
            <button type="button" id="close-kikapu-kopesha-modal" class="ml-auto text-white hover:text-gray-200 text-lg">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="kikapu-kopesha-form" action="{{ route('mauzo.store.kikapu.loan') }}" method="POST" class="p-4 space-y-3">
            @csrf
            
            <div>
                <label class="block text-xs font-semibold mb-1 text-black">Chagua Mteja</label>
                <select id="kikapu-mteja-select" class="w-full border border-amber-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-amber-300 focus:border-amber-400 transition">
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

            <!-- New Customer Info -->
            <div>
                <label class="block text-black text-xs font-semibold mb-1">Jina la Mkopaji *</label>
                <input type="text" name="jina_mkopaji" id="kikapu-kopesha-jina" class="w-full border border-amber-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-amber-300 focus:border-amber-400 transition" required>
            </div>

            <div>
                <label class="block text-black text-xs font-semibold mb-1">Namba ya Simu *</label>
                <input type="text" name="simu" id="kikapu-kopesha-simu" class="w-full border border-amber-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-amber-300 focus:border-amber-400 transition" required>
            </div>

            <div>
                <label class="block text-black text-xs font-semibold mb-1">Barua Pepe</label>
                <input type="email" name="barua_pepe" id="kikapu-kopesha-barua-pepe" class="w-full border border-amber-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-amber-300 focus:border-amber-400 transition">
            </div>

            <div>
                <label class="block text-black text-xs font-semibold mb-1">Anapoishi</label>
                <input type="text" name="anapoishi" id="kikapu-kopesha-anapoishi" class="w-full border border-amber-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-amber-300 focus:border-amber-400 transition">
            </div>

            <div>
                <label class="block text-black text-xs font-semibold mb-1">Tarehe ya Malipo *</label>
                <input type="date" name="tarehe_malipo" class="w-full border border-amber-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-amber-300 focus:border-amber-400 transition" required>
            </div>

            <input type="hidden" name="items" id="kikapu-items-data">

            <div class="flex justify-end gap-2 pt-3 border-t border-gray-200">
                <button type="button" id="cancel-kikapu-kopesha" class="bg-gray-400 hover:bg-gray-500 px-4 py-2 rounded-lg text-white font-semibold transition text-sm">
                    Funga
                </button>
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 px-4 py-2 rounded-lg text-white font-semibold flex items-center gap-1 transition shadow text-sm">
                    <i class="fas fa-check mr-1"></i>
                    Thibitisha Kopesha
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Simple Double Sale Warning Modal -->
<div id="double-sale-modal" class="modal fixed inset-0 z-50 flex items-center justify-center hidden p-2">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-xl shadow-xl w-full max-w-sm mx-2 z-50">
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 p-3 text-white flex items-center">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <h2 class="text-base font-semibold">Onana tena</h2>
            <button type="button" id="close-double-sale-modal" class="ml-auto text-white hover:text-gray-200 text-lg">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="p-4 space-y-3 text-center">
            <div class="flex items-center justify-center text-orange-600 mb-2">
                <i class="fas fa-exclamation-circle text-2xl mr-2"></i>
            </div>
            
            <p class="text-sm text-black mb-3">
                Unataka kuuza tena "<span id="double-sale-product-name" class="font-semibold"></span>"?
            </p>
            
            <div class="flex justify-center gap-3 pt-3">
                <button type="button" id="cancel-double-sale" class="bg-gray-400 hover:bg-gray-500 px-4 py-2 rounded-lg text-white font-semibold transition text-sm">
                    Ghairi
                </button>
                <button type="button" id="confirm-double-sale" class="bg-emerald-600 hover:bg-emerald-700 px-4 py-2 rounded-lg text-white font-semibold flex items-center gap-1 transition shadow text-sm">
                    <i class="fas fa-check mr-1"></i>
                    Uza
                </button>
            </div>
        </div>
    </div>
</div>
<!-- Delete Confirmation Modal -->
<div id="delete-confirmation-modal" class="modal fixed inset-0 z-50 flex items-center justify-center hidden p-2">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-xl shadow-xl w-full max-w-sm mx-2 z-50">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-base font-semibold text-black text-center">Thibitisha Ufutaji</h3>
        </div>
        <div class="p-4">
            <p class="text-black text-sm mb-4 text-center" id="delete-message">
                Una uhakika unataka kufuta mauzo haya?
            </p>
            <div class="flex justify-center space-x-2">
                <button id="cancel-delete" class="px-4 py-2 border border-gray-300 rounded-lg text-black hover:bg-gray-50 text-sm">
                    Ghairi
                </button>
                <button id="confirm-delete" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm">
                    Ndio, Futa
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Responsive Design */
@media (max-width: 640px) {
    .text-xs {
        font-size: 0.75rem;
    }
    .text-sm {
        font-size: 0.8rem;
    }
    .p-2 {
        padding: 0.4rem;
    }
    .p-3 {
        padding: 0.6rem;
    }
    .grid-cols-1 {
        grid-template-columns: repeat(1, minmax(0, 1fr));
    }
}

@media (min-width: 1024px) {
    .container {
        max-width: 100%;
        padding: 0 1rem;
    }
    
    #sehemu-tab-content {
        max-height: calc(100vh - 250px);
        overflow-y: auto;
    }
}

/* Modal Responsive */
@media (max-width: 640px) {
    .modal-content {
        width: 95% !important;
        margin: 0.5rem !important;
    }
}

/* Tab styles */
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

/* Card hover effect */
.card-hover {
    transition: all 0.3s ease;
}

.card-hover:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

/* Scrollbar styling */
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

/* Modal animations */
.modal {
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* Hide elements */
.hidden {
    display: none !important;
}

/* Text colors */
.text-black {
    color: #000000;
}

.text-emerald-800 {
    color: #065f46;
}

.text-emerald-600 {
    color: #059669;
}

/* Input focus styles */
input:focus, select:focus, textarea:focus {
    outline: none;
    ring: 2px;
}

/* Button transitions */
button {
    transition: all 0.2s ease;
}

button:hover {
    transform: translateY(-1px);
}

/* Table responsive */
.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

/* Print styles */
@media print {
    .no-print {
        display: none !important;
    }
}

/* Highlight animation */
@keyframes highlight {
    0% { background-color: rgba(34, 197, 94, 0.1); }
    100% { background-color: transparent; }
}

.highlight {
    animation: highlight 1s ease;
}

/* Barcode row highlight */
.barcode-row.highlight {
    background-color: rgba(34, 197, 94, 0.1);
    transition: background-color 0.5s ease;
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
        this.init();
    }

    init() {
        this.bindEvents();
        this.updateCartCount();
        this.setTodayDate();
        this.initBidhaaSearch();
        this.initBarcodeRows();
        this.restoreTabState();
        this.initReceiptLookup();
        this.initCartDisplay();
        this.initCustomerSelection();
        this.clearOtherCompanyCarts(); // Clear carts from other companies
    }

    // Add method to clear other company carts
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

    // Update: CORRECT DISCOUNT TYPE CALCULATIONS
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
        let finalTotal = 0;
        
        if (discountType === 'bidhaa') {
            // "k/bidhaa" = discount PER ITEM (multiply by quantity)
            const totalDiscount = discount * quantity;
            finalTotal = baseTotal - totalDiscount;
        } else {
            // "jumla" = discount ON TOTAL (apply once)
            finalTotal = baseTotal - discount;
        }
        
        finalTotal = Math.max(0, finalTotal);
        
        totalInput.value = finalTotal.toFixed(2);
        punguzoAinaInput.value = discountType;
        
        // Update kopesha form values
        const kopeshaIdadi = document.getElementById('kopesha-idadi');
        const kopeshaJumla = document.getElementById('kopesha-jumla');
        const kopeshaBaki = document.getElementById('kopesha-baki');
        const kopeshaPunguzo = document.getElementById('kopesha-punguzo');
        const kopeshaPunguzoAina = document.getElementById('kopesha-punguzo-aina');
        
        if (kopeshaIdadi) kopeshaIdadi.value = quantity;
        if (kopeshaJumla) kopeshaJumla.value = finalTotal;
        if (kopeshaBaki) kopeshaBaki.value = finalTotal;
        if (kopeshaPunguzo) kopeshaPunguzo.value = discount;
        if (kopeshaPunguzoAina) kopeshaPunguzoAina.value = discountType;
    }

    // Update: CORRECT CART ADDITION with company_id
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

        // Get buying price for profit calculation
        const buyingPrice = parseFloat(selectedOption.dataset.beiNunua) || 0;
        
        // Calculate base total and actual discount
        const baseTotal = price * quantity;
        let actualDiscount = discount;
        let profit = 0;
        
        if (discountType === 'bidhaa') {
            actualDiscount = discount * quantity;
            profit = ((price - buyingPrice) * quantity) - actualDiscount;
        } else {
            actualDiscount = discount;
            profit = ((price - buyingPrice) * quantity) - discount;
        }
        
        // Verify calculation matches displayed total
        const calculatedTotal = baseTotal - actualDiscount;
        if (Math.abs(calculatedTotal - total) > 0.01) {
            this.showNotification(`Hesabu si sahihi! Inatarajiwa: ${calculatedTotal}, Ilioingizwa: ${total}`, 'error');
            return;
        }

        const productName = selectedOption.dataset.jina;
        const barcode = selectedOption.dataset.barcode || '';

        // Get company name from meta tag
        const companyName = document.querySelector('meta[name="company-name"]')?.getAttribute('content') || '';

        const cartItem = {
            jina: productName,
            bei: price,
            idadi: quantity,
            punguzo: discount,
            punguzo_aina: discountType,
            jumla: total,
            profit: profit,
            bidhaa_id: selectedOption.value,
            barcode: barcode,
            timestamp: new Date().toISOString(),
            company_id: this.companyId, // Add company identifier
            company_name: companyName // Add company name for display
        };

        this.cart.push(cartItem);
        this.saveCart();
        this.updateCartCount();
        this.updateCartDisplay();
        
        this.showNotification('Bidhaa imeongezwa kwenye kikapu!', 'success');
        this.resetForm();
    }

    // Update: CORRECT CART DISPLAY with company filtering
    updateCartDisplay() {
        const emptyMessage = document.getElementById('empty-cart-message');
        const cartContent = document.getElementById('cart-content');
        const cartTbody = document.getElementById('cart-tbody');
        const cartTotal = document.getElementById('cart-total');
        const companyWarning = document.getElementById('company-cart-warning');

        if (!emptyMessage || !cartContent || !cartTbody || !cartTotal) return;

        // Filter cart items by current company
        const companyCart = this.cart.filter(item => item.company_id === this.companyId);
        
        // Show warning if cart contains items from other companies
        if (companyCart.length !== this.cart.length && companyWarning) {
            companyWarning.classList.remove('hidden');
            
            // Optionally remove non-company items from cart
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
                
                // Calculate displayed discount
                let displayedDiscount = item.punguzo;
                let discountLabel = item.punguzo_aina === 'bidhaa' ? 'k/bidhaa' : 'jumla';
                
                // If it's "kwa bidhaa", calculate total discount
                if (item.punguzo_aina === 'bidhaa') {
                    displayedDiscount = item.punguzo * item.idadi;
                }
                
                // Calculate base total for verification
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

    // Update: CORRECT BARCODE CALCULATION
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
        
        // Calculate total based on discount type
        let total = price * quantity;
        
        if (discountType === 'bidhaa') {
            // Discount per product (k/bidhaa)
            total = total - (punguzo * quantity);
        } else {
            // Discount on total (jumla)
            total = total - punguzo;
        }
        
        totalInput.value = Math.max(0, total);
        
        this.updateBarcodeTotal();
    }

    // Update: CORRECT BARCODE SALE SUBMISSION
    async submitBarcodeSales() {
        const items = [];
        let hasValidItems = false;
        const punguzoType = document.getElementById('punguzo-type');

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
                    // Find bidhaa_id from barcode
                    const bidhaa = this.bidhaaList.find(b => b.barcode === barcode);
                    
                    // Calculate actual discount based on type
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
                body: JSON.stringify({ items: items, punguzo_aina: discountType })
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

    // Update: CORRECT CART CHECKOUT with company validation
    async checkoutCart() {
        // Filter cart to current company only
        const companyCart = this.cart.filter(item => item.company_id === this.companyId);
        
        if (companyCart.length === 0) {
            this.showNotification('Kikapu hakina bidhaa za kampuni yako!', 'error');
            return;
        }

        try {
            const response = await fetch("{{ route('mauzo.store.kikapu') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Company-ID': this.companyId // Add company header
                },
                body: JSON.stringify({ 
                    items: companyCart,
                    company_id: this.companyId 
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

    // Update: CORRECT KOPESHA DATA PREPARATION
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
                    // Find bidhaa_id from barcode
                    const bidhaa = this.bidhaaList.find(b => b.barcode === barcode);
                    
                    // Calculate actual discount
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
                        company_id: this.companyId // Add company ID
                    });
                }
            }
        });
        
        if (items.length > 0) {
            document.getElementById('barcode-items-data').value = JSON.stringify(items);
        }
    }

    // Update: CORRECT KIKAPU KOPESHA DATA PREPARATION with company filter
    prepareKikapuKopeshaData() {
        // Filter cart to current company only
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
                company_id: this.companyId // Add company ID
            }));
            
            document.getElementById('kikapu-items-data').value = JSON.stringify(items);
        }
    }

    // Original methods from your script
    restoreTabState() {
        if (this.currentTab && this.currentTab !== 'sehemu') {
            this.showTab(this.currentTab);
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
        // Tab navigation
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', (e) => {
                const tab = e.target.closest('.tab-button').dataset.tab;
                if (tab) {
                    localStorage.setItem('currentMauzoTab', tab);
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

    showTab(tabName) {
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

        this.currentTab = tabName;
        
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
        
        const cancelDeleteBtn = document.getElementById('cancel-delete');
        const confirmDeleteBtn = document.getElementById('confirm-delete');
        
        if (cancelDeleteBtn) {
            cancelDeleteBtn.addEventListener('click', () => closeModal('delete-confirmation-modal'));
        }
        
        if (confirmDeleteBtn) {
            confirmDeleteBtn.addEventListener('click', () => {
                if (this.deleteCallback) {
                    this.deleteCallback();
                }
                closeModal('delete-confirmation-modal');
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
        document.querySelectorAll('.delete-sale-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const saleId = e.target.closest('.delete-sale-btn').dataset.id;
                
                this.showConfirmation(
                    'Una uhakika unataka kufuta mauzo haya? Hatua hii haiwezi kutenduliwa.',
                    () => {
                        this.deleteSale(saleId);
                    }
                );
            });
        });
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

    deleteSale(saleId) {
        fetch(`/mauzo/${saleId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.showNotification('Mauzo yamefutwa kikamilifu!', 'success');
                this.updateFinancialData();
            } else {
                this.showNotification(data.message || 'Kuna tatizo kufuta mauzo!', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            this.showNotification('Kuna tatizo kufuta mauzo!', 'error');
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
            // Count only items from current company
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
        const deleteMessage = document.getElementById('delete-message');
        const confirmBtn = document.getElementById('confirm-delete');
        const cancelBtn = document.getElementById('cancel-delete');
        const modal = document.getElementById('delete-confirmation-modal');
        
        if (deleteMessage && confirmBtn && cancelBtn && modal) {
            deleteMessage.textContent = message;
            this.deleteCallback = confirmCallback;
            modal.classList.remove('hidden');
        } else {
            if (confirm(message)) {
                confirmCallback();
            }
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
    new MauzoManager();
});
</script>

@endpush