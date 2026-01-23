i want some changes  1, no any window refresh to be done replaced by smoothly data changes within the system but in going in another refresh should be done for data to update 2. 
all pop modal shall their box be small and smart fit well according to screen 3.language notification should be in swahili 4.text changes should be accordong to screen also
 window should adjust acording to screen eg phone/desktop to fit exactly, replace gray text with black 5.in taarifa tab include punguzo that must be working when it is used 
 
 also make the label to be small its width to good fit, also a user i not allowed to punguza to exceed faida as bei nunua either kwa bidhaa or kwa jumla 6.
  idadi label shall be empty untill filled with user 7. in kopesha user detail email not must be filled but kopesha button also shall be given in barcode where a user can 
  choose a registered one or make enter new one if not registered 8.  punguzo shall be reduce  in faida so calculation to match with 9. resize rekodi ya mauo to fit i
  
  n two line only as the sehemu ya mauzo to fit in one page in desktop without scrolling , 10. in mapato and matumizi need all box to have the same size now matumizi
   box not resemble the rest 11. kopesha doe not return any notofication  12. the logic is that if bidhaa is bei nunua is 1000 and 
   bei uza is 2000 so when punguzo of 500 made the mapato leo will be = 1500, faida leo = 500, fedha leo 1500, faida halisi = 500 but 
   when matumizi of 500 made mapato leo = 1500, faida leo = 500, matumizi = 500, faida halisi 0 jumla kuu 1500 ,,,,,,,,,,,,,,,remember cureent logic is working dont change it to afffect but the structure shall change to look professional and logic understand and ealy view to use and deterrmine. the blade @extends('layouts.app')

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
                <p id="notification-message" class="text-base lg:text-lg font-semibold"></p>
            </div>
        </div>
    </div>

    <!-- Page Navigation Tabs -->
    <div class="bg-white rounded-xl lg:rounded-2xl shadow-lg border border-gray-100 p-3 lg:p-4 card-hover">
        <div class="flex overflow-x-auto pb-2 lg:pb-0" id="tab-nav">
            <div class="flex space-x-2 lg:space-x-6">
                <button id="sehemu-tab" class="tab-button active pb-3 px-3 lg:px-4 transition-colors flex items-center border-b-2 border-green-500 text-green-600 font-semibold whitespace-nowrap text-sm lg:text-base" data-tab="sehemu">
                    <i class="fas fa-cash-register mr-2 text-xs lg:text-sm"></i>Sehemu
                </button>
                <button id="barcode-tab" class="tab-button pb-3 px-3 lg:px-4 transition-colors flex items-center text-gray-500 hover:text-gray-700 whitespace-nowrap text-sm lg:text-base" data-tab="barcode">
                    <i class="fas fa-barcode mr-2 text-xs lg:text-sm"></i>Barcode
                </button>
                <button id="taarifa-tab" class="tab-button pb-3 px-3 lg:px-4 transition-colors flex items-center text-gray-500 hover:text-gray-700 whitespace-nowrap text-sm lg:text-base" data-tab="taarifa">
                    <i class="fas fa-file-alt mr-2 text-xs lg:text-sm"></i>Taarifa
                </button>
                <button id="jumla-tab" class="tab-button pb-3 px-3 lg:px-4 transition-colors flex items-center text-gray-500 hover:text-gray-700 whitespace-nowrap text-sm lg:text-base" data-tab="jumla">
                    <i class="fas fa-chart-bar mr-2 text-xs lg:text-sm"></i>Jumla
                </button>
                <button id="kikapu-btn" class="pb-3 px-3 lg:px-4 transition-colors flex items-center text-gray-500 hover:text-gray-700 relative whitespace-nowrap text-sm lg:text-base">
                    <i class="fas fa-shopping-cart mr-2 text-xs lg:text-sm"></i>Kikapu
                    <span id="cart-count" class="absolute -top-1 -right-1 lg:-top-2 lg:-right-2 bg-red-500 text-white text-xs rounded-full h-4 w-4 lg:h-5 lg:w-5 flex items-center justify-center hidden">0</span>
                </button>
                <button id="risiti-tab" class="tab-button pb-3 px-3 lg:px-4 transition-colors flex items-center text-gray-500 hover:text-gray-700 whitespace-nowrap text-sm lg:text-base" data-tab="risiti">
                    <i class="fas fa-receipt mr-2 text-xs lg:text-sm"></i>Risiti
                </button>
            </div>
        </div>
    </div>

    <!-- TAB 1: Sehemu ya Mauzo -->
    <div id="sehemu-tab-content" class="space-y-4 lg:space-y-6 tab-content active">
        <!-- Sales Form -->
        <div class="bg-white rounded-xl lg:rounded-2xl shadow-lg border border-green-200 p-4 lg:p-6">
            <h2 class="text-lg lg:text-xl font-bold text-emerald-800 mb-3 lg:mb-4 flex items-center">
                <i class="fas fa-cash-register mr-2 text-emerald-600 text-sm lg:text-base"></i>
                Rekodi Mauzo Mapya
            </h2>

            <form method="POST" action="{{ route('mauzo.store') }}" class="space-y-4" id="sales-form">
                @csrf

                <!-- Product and Basic Info -->
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-3 lg:gap-4">
                    <!-- Bidhaa with Search -->
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-semibold text-emerald-800 mb-1">Bidhaa</label>
                        <div class="relative">
                            <input type="text" id="bidhaaSearch" placeholder="Tafuta bidhaa..." class="w-full border border-emerald-200 rounded-lg p-2 lg:p-3 text-sm focus:ring-2 focus:ring-emerald-200">
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
                                >
                                    {{ $item->jina }} ({{ $item->aina }}) - {{ $item->kipimo }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Idadi -->
                    <div>
                        <label class="block text-sm font-semibold text-emerald-800 mb-1">Idadi</label>
                        <input type="number" name="idadi" id="quantity-input" min="1" value="1" class="w-full border border-emerald-200 rounded-lg p-2 lg:p-3 text-sm focus:ring-2 focus:ring-emerald-200">
                    </div>

                    <!-- Bei -->
                    <div>
                        <label class="block text-sm font-semibold text-emerald-800 mb-1">Bei (Tsh)</label>
                        <input type="number" name="bei" id="price-input" readonly class="w-full bg-gray-100 border border-emerald-200 rounded-lg p-2 lg:p-3 text-sm">
                    </div>
                </div>

                <!-- Stock and Discount -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 lg:gap-4">
                    <!-- Stock -->
                    <div>
                        <label class="block text-sm font-semibold text-emerald-800 mb-1">Stock Ipo</label>
                        <input type="number" id="stock-input" readonly class="w-full bg-gray-100 border border-emerald-200 rounded-lg p-2 lg:p-3 text-sm">
                    </div>

                    <!-- Discount -->
                    <div class="space-y-2 lg:col-span-2">
                        <div class="flex flex-col lg:flex-row lg:items-center gap-2 lg:gap-4">
                            <div class="flex items-center gap-2">
                                <input type="checkbox" id="punguzo-bidhaa-check" class="h-4 w-4 text-amber-600">
                                <label class="text-sm font-semibold text-amber-800">Punguzo kwa Bidhaa</label>
                            </div>
                            <div class="flex gap-2 flex-1">
                                <input type="number" name="punguzo_bidhaa" id="discount-bidhaa-input" min="0" placeholder="Kiasi" disabled class="flex-1 border border-amber-200 rounded-lg p-2 lg:p-3 text-sm disabled:bg-gray-100">
                                <select id="discount-bidhaa-type" disabled class="w-20 lg:w-24 border border-amber-200 rounded-lg p-2 lg:p-3 text-sm disabled:bg-gray-100">
                                    <option value="fixed">Tsh</option>
                                    <option value="percent">%</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex flex-col lg:flex-row lg:items-center gap-2 lg:gap-4">
                            <div class="flex items-center gap-2">
                                <input type="checkbox" id="punguzo-jumla-check" class="h-4 w-4 text-green-600">
                                <label class="text-sm font-semibold text-green-800">Punguzo kwa Jumla</label>
                            </div>
                            <div class="flex gap-2 flex-1">
                                <input type="number" name="punguzo_jumla" id="discount-jumla-input" min="0" placeholder="Kiasi" disabled class="flex-1 border border-green-200 rounded-lg p-2 lg:p-3 text-sm disabled:bg-gray-100">
                                <select id="discount-jumla-type" disabled class="w-20 lg:w-24 border border-green-200 rounded-lg p-2 lg:p-3 text-sm disabled:bg-gray-100">
                                    <option value="fixed">Tsh</option>
                                    <option value="percent">%</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total and Actions -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 lg:gap-4">
                    <!-- Jumla -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-emerald-800">Jumla (Tsh)</label>
                        <input type="number" name="jumla" id="total-input" readonly class="w-full bg-green-50 border border-green-300 rounded-lg p-2 lg:p-3 text-sm font-bold text-green-800">
                    </div>

                    <!-- Action Buttons -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 lg:gap-3 pt-2 lg:pt-6">
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white p-3 rounded-lg font-semibold text-sm flex items-center justify-center gap-2">
                            <i class="fas fa-cash-register"></i>
                            Uza
                        </button>

                        <button type="button" id="kopesha-btn" class="bg-amber-600 hover:bg-amber-700 text-white p-3 rounded-lg font-semibold text-sm flex items-center justify-center gap-2">
                            <i class="fas fa-hand-holding-usd"></i>
                            Kopesha
                        </button>

                        <button type="button" id="add-to-cart-btn" class="bg-blue-600 hover:bg-blue-700 text-white p-3 rounded-lg font-semibold text-sm flex items-center justify-center gap-2">
                            <i class="fas fa-cart-plus"></i>
                            Kikapu
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Financial Overview -->
        <div class="mt-6 lg:mt-8">
            <h2 class="text-lg lg:text-2xl font-bold text-gray-800 flex items-center mb-4 lg:mb-6">
                <div class="relative mr-2 lg:mr-3">
                    <div class="w-2 h-6 lg:w-3 lg:h-8 bg-gradient-to-b from-blue-500 to-purple-600 rounded-full"></div>
                    <div class="absolute top-1 -right-1 w-1.5 h-4 lg:w-2 lg:h-6 bg-gradient-to-b from-emerald-400 to-teal-600 rounded-full"></div>
                </div>
                <span>Taarifa Fupi ya Mapato na Matumizi</span>
            </h2>

            <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3 lg:gap-4">
                <!-- Mapato -->
                <div class="col-span-2 lg:col-span-1 group relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl lg:rounded-2xl blur-md opacity-60 group-hover:opacity-80 transition duration-500"></div>
                    <div class="relative bg-gradient-to-br from-amber-500 via-amber-600 to-amber-700 p-3 lg:p-4 rounded-xl lg:rounded-2xl shadow-lg lg:shadow-xl border border-blue-400/30 transform transition duration-300 group-hover:scale-105 group-hover:shadow-xl lg:group-hover:shadow-2xl cursor-pointer">
                        <div class="flex justify-between items-start mb-2 lg:mb-3">
                            <div class="p-1.5 lg:p-2 bg-white/20 rounded-lg lg:rounded-xl backdrop-blur-sm">
                                <i class="fas fa-money-bill-wave text-white text-base lg:text-lg"></i>
                            </div>
                            <i class="fas fa-arrow-right text-white/60 text-xs lg:text-sm group-hover:translate-x-1 transition-transform"></i>
                        </div>
                        <div class="text-xs font-semibold text-blue-100 uppercase tracking-wide mb-1 lg:mb-2">
                            Mapato ya leo
                        </div>
                        @php
                            $mapatoMauzo = $mauzos->where('created_at', '>=', today())->sum('jumla');
                            $mapatoMadeni = $marejeshos->where('tarehe', today()->toDateString())->sum('kiasi');
                            $jumlaMapato = $mapatoMauzo + $mapatoMadeni;
                        @endphp
                        <div class="space-y-1 lg:space-y-2 text-white text-xs lg:text-sm">
                            <div class="flex justify-between items-center">
                                <span class="text-blue-100">Mauzo:</span>
                                <span class="font-semibold">{{ number_format($mapatoMauzo) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-blue-100">Madeni:</span>
                                <span class="font-semibold">{{ number_format($mapatoMadeni) }}</span>
                            </div>
                            <div class="flex justify-between items-center border-t border-white/20 pt-1.5 lg:pt-2 mt-1.5 lg:mt-2">
                                <span class="text-blue-50 font-semibold">Jumla:</span>
                                <span class="font-bold text-sm lg:text-lg">{{ number_format($jumlaMapato) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Faida -->
                <div class="col-span-2 lg:col-span-1 group relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-green-500 to-green-600 rounded-xl lg:rounded-2xl blur-md opacity-60 group-hover:opacity-80 transition duration-500"></div>
                    <div class="relative bg-gradient-to-br from-green-500 via-green-600 to-emerald-700 p-3 lg:p-4 rounded-xl lg:rounded-2xl shadow-lg lg:shadow-xl border border-green-400/30 transform transition duration-300 group-hover:scale-105 group-hover:shadow-xl lg:group-hover:shadow-2xl cursor-pointer">
                        <div class="flex justify-between items-start mb-2 lg:mb-3">
                            <div class="p-1.5 lg:p-2 bg-white/20 rounded-lg lg:rounded-xl backdrop-blur-sm">
                                <i class="fas fa-chart-line text-white text-base lg:text-lg"></i>
                            </div>
                            <i class="fas fa-arrow-right text-white/60 text-xs lg:text-sm group-hover:translate-x-1 transition-transform"></i>
                        </div>
                        <div class="text-xs font-semibold text-green-100 uppercase tracking-wide mb-1 lg:mb-2">
                            Faida ya leo
                        </div>
                        @php
                            $faidaMauzo = $mauzos->where('created_at', '>=', today())
                                ->sum(fn($m) => ($m->bei - ($m->bidhaa->bei_nunua ?? 0)) * $m->idadi);
                            $faidaMarejesho = 0;
                            $todayMarejeshos = $marejeshos->where('tarehe', today()->toDateString());
                            foreach($todayMarejeshos as $marejesho) {
                                if(isset($marejesho->madeni) && isset($marejesho->madeni->bidhaa)) {
                                    $profitMargin = $marejesho->madeni->bidhaa->bei_kuuza - $marejesho->madeni->bidhaa->bei_nunua;
                                    $paymentRatio = $marejesho->kiasi / $marejesho->madeni->jumla;
                                    $faidaMarejesho += $profitMargin * $marejesho->madeni->idadi * $paymentRatio;
                                }
                            }
                            $jumlaFaida = $faidaMauzo + $faidaMarejesho;
                        @endphp
                        <div class="space-y-1 lg:space-y-2 text-white text-xs lg:text-sm">
                            <div class="flex justify-between items-center">
                                <span class="text-green-100">Mauzo:</span>
                                <span class="font-semibold">{{ number_format($faidaMauzo) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-green-100">Marejesho:</span>
                                <span class="font-semibold">{{ number_format($faidaMarejesho) }}</span>
                            </div>
                            <div class="flex justify-between items-center border-t border-white/20 pt-1.5 lg:pt-2 mt-1.5 lg:mt-2">
                                <span class="text-green-50 font-semibold">Jumla:</span>
                                <span class="font-bold text-sm lg:text-lg">{{ number_format($jumlaFaida) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Matumizi -->
                <div class="col-span-2 lg:col-span-1 group relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-amber-500 to-amber-600 rounded-xl lg:rounded-2xl blur-md opacity-60 group-hover:opacity-80 transition duration-500"></div>
                    <div class="relative bg-gradient-to-br from-amber-500 via-amber-600 to-orange-700 p-3 lg:p-4 rounded-xl lg:rounded-2xl shadow-lg lg:shadow-xl border border-amber-400/30 transform transition duration-300 group-hover:scale-105 group-hover:shadow-xl lg:group-hover:shadow-2xl cursor-pointer">
                        <div class="flex justify-between items-start mb-2 lg:mb-3">
                            <div class="p-1.5 lg:p-2 bg-white/20 rounded-lg lg:rounded-xl backdrop-blur-sm">
                                <i class="fas fa-receipt text-white text-base lg:text-lg"></i>
                            </div>
                            <i class="fas fa-arrow-right text-white/60 text-xs lg:text-sm group-hover:translate-x-1 transition-transform"></i>
                        </div>
                        <div class="text-xs font-semibold text-amber-100 uppercase tracking-wide mb-1 lg:mb-2">Matumizi</div>
                        @php
                            $matumiziLeo = $matumizi->where('created_at', '>=', today())->sum('gharama');
                            $matumiziWiki = $matumizi->where('created_at', '>=', now()->startOfWeek())->sum('gharama');
                            $matumiziJumla = $matumizi->sum('gharama');
                        @endphp
                        <div class="space-y-1 lg:space-y-2 text-white text-xs lg:text-sm">
                            <div class="flex justify-between items-center">
                                <span class="text-amber-100">Leo:</span>
                                <span class="font-semibold">{{ number_format($matumiziLeo) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-amber-100">Wiki hii:</span>
                                <span class="font-semibold">{{ number_format($matumiziWiki) }}</span>
                            </div>
                            <div class="flex justify-between items-center border-t border-white/20 pt-1.5 lg:pt-2 mt-1.5 lg:mt-2">
                                <span class="text-amber-50 font-semibold">Jumla:</span>
                                <span class="font-bold text-sm lg:text-lg">{{ number_format($matumiziJumla) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Fedha Leo -->
                <div class="col-span-2 lg:col-span-1 group relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-cyan-500 to-cyan-700 rounded-xl lg:rounded-2xl blur-md opacity-60 group-hover:opacity-80 transition duration-500"></div>
                    <div class="relative bg-gradient-to-br from-emerald-500 via-emerald-600 to-emerald-700 p-3 lg:p-4 rounded-xl lg:rounded-2xl shadow-lg lg:shadow-xl border border-cyan-400/30 transform transition duration-300 group-hover:scale-105 group-hover:shadow-xl lg:group-hover:shadow-2xl cursor-pointer">
                        <div class="flex justify-between items-start mb-2 lg:mb-3">
                            <div class="p-1.5 lg:p-2 bg-white/20 rounded-lg lg:rounded-xl backdrop-blur-sm">
                                <i class="fas fa-wallet text-white text-base lg:text-lg"></i>
                            </div>
                            <i class="fas fa-arrow-right text-white/60 text-xs lg:text-sm group-hover:translate-x-1 transition-transform"></i>
                        </div>
                        <div class="text-xs font-semibold text-cyan-100 uppercase tracking-wide mb-1 lg:mb-2">Fedha Leo</div>
                        @php
                            $mauzoLeo = $mauzos->where('created_at', '>=', today())->sum('jumla');
                            $matumiziLeo = $matumizi->where('created_at', '>=', today())->sum('gharama');
                            $fedhaLeo = $mauzoLeo - $matumiziLeo;
                        @endphp
                        <div class="space-y-1 lg:space-y-2 text-white text-xs lg:text-sm">
                            <div class="flex justify-between items-center">
                                <span class="text-cyan-100">Mapato:</span>
                                <span class="font-semibold">{{ number_format($mauzoLeo) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-cyan-100">Matumizi:</span>
                                <span class="font-semibold">{{ number_format($matumiziLeo) }}</span>
                            </div>
                            <div class="flex justify-between items-center border-t border-white/20 pt-1.5 lg:pt-2 mt-1.5 lg:mt-2">
                                <span class="text-cyan-50 font-semibold">Jumla:</span>
                                <span class="font-bold text-sm lg:text-lg">{{ number_format($fedhaLeo) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Faida Halisi -->
                <div class="col-span-2 lg:col-span-1 group relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-amber-500 to-amber-600 rounded-xl lg:rounded-2xl blur-md opacity-60 group-hover:opacity-80 transition duration-500"></div>
                    <div class="relative bg-gradient-to-br from-amber-500 via-amber-600 to-orange-700 p-3 lg:p-4 rounded-xl lg:rounded-2xl shadow-lg lg:shadow-xl border border-amber-400/30 transform transition duration-300 group-hover:scale-105 group-hover:shadow-xl lg:group-hover:shadow-2xl cursor-pointer">
                        <div class="flex justify-between items-start mb-2 lg:mb-3">
                            <div class="p-1.5 lg:p-2 bg-white/20 rounded-lg lg:rounded-xl backdrop-blur-sm">
                                <i class="fas fa-chart-pie text-white text-base lg:text-lg"></i>
                            </div>
                            <i class="fas fa-arrow-right text-white/60 text-xs lg:text-sm group-hover:translate-x-1 transition-transform"></i>
                        </div>
                        <div class="text-xs font-semibold text-teal-100 uppercase tracking-wide mb-1 lg:mb-2">Faida Halisi</div>
                        @php
                            $faidaMauzo = $mauzos->where('created_at', '>=', today())
                                ->sum(fn($m) => ($m->bei - ($m->bidhaa->bei_nunua ?? 0)) * $m->idadi);
                            $matumiziLeo = $matumizi->where('created_at', '>=', today())->sum('gharama');
                            $faidaHalisi = $faidaMauzo - $matumiziLeo;
                        @endphp
                        <div class="space-y-1 lg:space-y-2 text-white text-xs lg:text-sm">
                            <div class="flex justify-between items-center">
                                <span class="text-teal-100">Faida:</span>
                                <span class="font-semibold">{{ number_format($faidaMauzo) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-teal-100">Matumizi:</span>
                                <span class="font-semibold">{{ number_format($matumiziLeo) }}</span>
                            </div>
                            <div class="flex justify-between items-center border-t border-white/20 pt-1.5 lg:pt-2 mt-1.5 lg:mt-2">
                                <span class="text-teal-50 font-semibold">Halisi:</span>
                                <span class="font-bold text-sm lg:text-lg">{{ number_format($faidaHalisi) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Jumla Kuu -->
                <div class="col-span-2 lg:col-span-1 group relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-rose-500 to-rose-600 rounded-xl lg:rounded-2xl blur-md opacity-60 group-hover:opacity-80 transition duration-500"></div>
                    <div class="relative bg-gradient-to-br from-green-500 via-green-600 to-green-700 p-3 lg:p-4 rounded-xl lg:rounded-2xl shadow-lg lg:shadow-xl border border-rose-400/30 transform transition duration-300 group-hover:scale-105 group-hover:shadow-xl lg:group-hover:shadow-2xl cursor-pointer">
                        <div class="flex justify-between items-start mb-2 lg:mb-3">
                            <div class="p-1.5 lg:p-2 bg-white/20 rounded-lg lg:rounded-xl backdrop-blur-sm">
                                <i class="fas fa-chart-bar text-white text-base lg:text-lg"></i>
                            </div>
                            <i class="fas fa-arrow-right text-white/60 text-xs lg:text-sm group-hover:translate-x-1 transition-transform"></i>
                        </div>
                        <div class="text-xs font-semibold text-rose-100 uppercase tracking-wide mb-1 lg:mb-2">Jumla Kuu</div>
                        @php
                            $jumlaMauzo = $mauzos->sum('jumla');
                            $jumlaMatumizi = $matumizi->sum('gharama');
                            $jumlaFaida = $jumlaMauzo - $jumlaMatumizi;
                        @endphp
                        <div class="space-y-1 lg:space-y-2 text-white text-xs lg:text-sm">
                            <div class="flex justify-between items-center">
                                <span class="text-rose-100">Mapato:</span>
                                <span class="font-semibold">{{ number_format($jumlaMauzo) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-rose-100">Matumizi:</span>
                                <span class="font-semibold">{{ number_format($jumlaMatumizi) }}</span>
                            </div>
                            <div class="flex justify-between items-center border-t border-white/20 pt-1.5 lg:pt-2 mt-1.5 lg:mt-2">
                                <span class="text-rose-50 font-semibold">Jumla:</span>
                                <span class="font-bold text-sm lg:text-lg">{{ number_format($jumlaFaida) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 2: Barcode Sales -->
    <div id="barcode-tab-content" class="tab-content hidden">
        <div class="rounded-2xl shadow-md border border-green-100 bg-white p-4 lg:p-8 card-hover">
            <!-- Header -->
            <div class="flex items-center mb-6">
                <div class="bg-green-600 text-white p-3 rounded-full shadow-md">
                    <i class="fas fa-barcode"></i>
                </div>
                <h2 class="ml-3 text-lg lg:text-xl font-bold text-black tracking-wide">
                    Mauzo kwa Barcode
                </h2>
            </div>

            <!-- Barcode Form -->
            <form id="barcode-form" class="space-y-4 lg:space-y-6">
                @csrf
                
                <!-- Table -->
                <div class="overflow-x-auto rounded-xl shadow-sm border border-green-200 bg-white/80">
                    <table class="w-full table-auto border-collapse">
                        <thead class="bg-green-400/70 text-black-800 text-xs lg:text-sm uppercase">
                            <tr>
                                <th class="border px-3 lg:px-4 py-2 lg:py-3 text-left">Barcode</th>
                                <th class="border px-3 lg:px-4 py-2 lg:py-3 text-left">Bidhaa</th>
                                <th class="border px-3 lg:px-4 py-2 lg:py-3 text-left">Bei</th>
                                <th class="border px-3 lg:px-4 py-2 lg:py-3 text-left">Idadi</th>
                                <th class="border px-3 lg:px-4 py-2 lg:py-3 text-left">Baki</th>
                                <th class="border px-3 lg:px-4 py-2 lg:py-3 text-left">Jumla</th>
                                <th class="border px-3 lg:px-4 py-2 lg:py-3 text-center">Futa</th>
                            </tr>
                        </thead>
                        <tbody id="barcode-tbody">
                            <tr class="barcode-row">
                                <td class="px-3 lg:px-4 py-2 lg:py-3">
                                    <input type="text" name="barcode[]" placeholder="Scan barcode" 
                                           class="barcode-input border border-green-200 focus:border-green-500 focus:ring-2 focus:ring-green-300 rounded-lg p-2 lg:p-3 w-full text-sm transition-all" 
                                           autofocus />
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3">
                                    <input type="text" name="jina[]" readonly placeholder="Jina la Bidhaa" 
                                           class="product-name border border-green-100 bg-gray-50 text-gray-700 rounded-lg p-2 lg:p-3 w-full text-sm" />
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3">
                                    <input type="number" name="bei[]" readonly placeholder="Bei" 
                                           class="product-price border border-green-100 bg-gray-50 text-gray-700 rounded-lg p-2 lg:p-3 w-full text-sm" />
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3">
                                    <input type="number" name="idadi[]" min="1" value="1" placeholder="Idadi" 
                                           class="quantity-input border border-green-200 focus:border-green-500 focus:ring-2 focus:ring-green-300 rounded-lg p-2 lg:p-3 w-full text-sm transition-all" />
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3">
                                    <input type="number" name="stock[]" readonly placeholder="Baki" 
                                           class="stock-input border border-green-100 bg-gray-50 text-gray-700 rounded-lg p-2 lg:p-3 w-full text-sm" />
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3">
                                    <input type="number" name="jumla[]" readonly placeholder="Jumla" 
                                           class="total-input border border-green-100 bg-gray-50 text-gray-700 rounded-lg p-2 lg:p-3 w-full text-sm" />
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 text-center">
                                    <button type="button" class="remove-barcode-row text-red-500 hover:text-red-700 p-2 rounded-full transition transform hover:scale-110" title="Futa bidhaa">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Receipt Info -->
                <div class="bg-green-50 p-3 lg:p-4 rounded-lg border border-green-200">
                    <div class="flex items-center text-green-700">
                        <i class="fas fa-info-circle mr-2"></i>
                        <span class="text-xs lg:text-sm font-semibold">Risiti inatolewa moja kwa moja!</span>
                    </div>
                </div>

                <!-- Add Row Button -->
                <div class="flex justify-center">
                    <button type="button" id="add-barcode-row" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 lg:px-4 lg:py-3 rounded-lg flex items-center transition">
                        <i class="fas fa-plus mr-2"></i>
                        <span class="text-xs lg:text-sm">Ongeza Safu Mpya</span>
                    </button>
                </div>

                <!-- Total & Action -->
                <div class="flex flex-col lg:flex-row justify-between items-center gap-4 mt-4 lg:mt-6">
                    <div class="text-base lg:text-lg font-semibold text-gray-800">
                        Jumla ya Mauzo: 
                        <span class="text-green-700 font-bold text-lg lg:text-xl" id="barcode-total">0</span>
                    </div>

                    <div class="flex gap-3">
                        <button type="button" id="clear-barcode-form" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 lg:px-4 lg:py-3 rounded-lg font-semibold shadow-md flex items-center transition-all duration-300 text-xs lg:text-sm">
                            <i class="fas fa-times mr-2"></i>
                            Futa Yote
                        </button>
                        
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 lg:px-6 lg:py-3 rounded-lg font-semibold shadow-md flex items-center transition-all duration-300 hover:scale-[1.02] text-xs lg:text-sm">
                            <i class="fas fa-check mr-2"></i>
                            Uza Bidhaa
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- TAB 3: Taarifa Fupi -->
    <div id="taarifa-tab-content" class="tab-content hidden">
        <div class="bg-green-50 rounded-lg lg:rounded-xl shadow-sm border p-4 lg:p-6 card-hover">
            <h2 class="text-base lg:text-lg font-semibold mb-3 lg:mb-4 flex items-center text-gray-800">
                <i class="fas fa-file-alt mr-2 text-blue-600 text-sm lg:text-base"></i>
                Taarifa Fupi ya Mauzo
            </h2>

            <!-- Search -->
            <div class="mb-3 lg:mb-4">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400 text-sm"></i>
                    <input type="text" id="search-sales" placeholder="Tafuta kwa jina la bidhaa..." class="pl-10 w-full border border-gray-300 rounded-lg p-2 lg:p-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                </div>
            </div>

            <!-- Sales Table -->
            <div class="overflow-x-auto rounded-lg shadow-sm border">
                <table class="w-full table-auto border-collapse">
                    <thead>
                        <tr class="bg-amber-600">
                            <th class="border px-3 py-2 lg:px-4 lg:py-3 text-left text-white text-xs lg:text-sm">Tarehe</th>
                            <th class="border px-3 py-2 lg:px-4 lg:py-3 text-left text-white text-xs lg:text-sm">Risiti No</th>
                            <th class="border px-3 py-2 lg:px-4 lg:py-3 text-left text-white text-xs lg:text-sm">Bidhaa</th>
                            <th class="border px-3 py-2 lg:px-4 lg:py-3 text-left text-white text-xs lg:text-sm">Idadi</th>
                            <th class="border px-3 py-2 lg:px-4 lg:py-3 text-left text-white text-xs lg:text-sm">Bei</th>
                            <th class="border px-3 py-2 lg:px-4 lg:py-3 text-left text-white text-xs lg:text-sm">Jumla</th>
                            <th class="border px-3 py-2 lg:px-4 lg:py-3 text-left text-white text-xs lg:text-sm">Vitendo</th>
                        </tr>
                    </thead>
                    <tbody id="sales-tbody">
                        @php $today = \Carbon\Carbon::today()->format('Y-m-d'); @endphp
                        @forelse($mauzos as $item)
                            @php $itemDate = $item->created_at->format('Y-m-d'); @endphp
                            <tr class="sales-row" data-product="{{ strtolower($item->bidhaa->jina) }}" data-date="{{ $itemDate }}">
                                <td class="border px-3 py-2 lg:px-4 lg:py-3 text-xs lg:text-sm">
                                    @if($itemDate === $today)
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded font-semibold text-xs">Leo</span>
                                    @else
                                        {{ $itemDate }}
                                    @endif
                                </td>
                                <td class="border px-3 py-2 lg:px-4 lg:py-3 text-xs lg:text-sm font-mono">
                                    @if($item->receipt_no)
                                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs copy-receipt cursor-pointer" data-receipt="{{ $item->receipt_no }}" title="Bonyeza kunakili">{{ substr($item->receipt_no, -8) }}</span>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>
                                <td class="border px-3 py-2 lg:px-4 lg:py-3 text-xs lg:text-sm">{{ $item->bidhaa->jina }}</td>
                                <td class="border px-3 py-2 lg:px-4 lg:py-3 text-xs lg:text-sm text-center">{{ $item->idadi }}</td>
                                <td class="border px-3 py-2 lg:px-4 lg:py-3 text-xs lg:text-sm">{{ number_format($item->bei) }}</td>
                                <td class="border px-3 py-2 lg:px-4 lg:py-3 text-xs lg:text-sm">{{ number_format($item->jumla) }}</td>
                                <td class="border px-3 py-2 lg:px-4 lg:py-3 text-center">
                                    <div class="flex flex-col lg:flex-row gap-1 justify-center">
                                        @if($item->receipt_no)
                                        <button type="button" class="print-single-receipt bg-blue-200 hover:bg-blue-400 text-gray-700 px-2 py-1 lg:px-3 lg:py-1 rounded-lg flex items-center justify-center transition text-xs" data-receipt-no="{{ $item->receipt_no }}">
                                            <i class="fas fa-print mr-1 text-xs"></i>
                                            Print
                                        </button>
                                        @endif
                                        <button type="button" class="delete-sale-btn bg-red-200 hover:bg-red-400 text-gray-700 px-2 py-1 lg:px-3 lg:py-1 rounded-lg flex items-center justify-center transition text-xs" data-id="{{ $item->id }}">
                                            <i class="fas fa-trash mr-1 text-xs"></i>
                                            Futa
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-gray-500 text-xs lg:text-sm">Hakuna mauzo yaliyorekodiwa bado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($mauzos->hasPages())
            <div class="mt-4 lg:mt-6">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                    <!-- Pagination Info -->
                    <div class="text-xs lg:text-sm text-gray-600">
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
                            <span class="px-3 py-1 lg:px-4 lg:py-2 rounded-lg border text-gray-400 text-xs lg:text-sm cursor-not-allowed">
                                <i class="fas fa-chevron-left mr-1"></i> Nyuma
                            </span>
                        @else
                            <a href="{{ $mauzos->previousPageUrl() }}" class="px-3 py-1 lg:px-4 lg:py-2 rounded-lg border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs lg:text-sm transition flex items-center">
                                <i class="fas fa-chevron-left mr-1"></i> Nyuma
                            </a>
                        @endif

                        <!-- Page Numbers -->
                        <div class="flex items-center space-x-1">
                            @foreach($mauzos->getUrlRange(1, $mauzos->lastPage()) as $page => $url)
                                @if($page == $mauzos->currentPage())
                                    <span class="px-3 py-1 lg:px-4 lg:py-2 rounded-lg bg-amber-600 text-white font-semibold text-xs lg:text-sm">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="px-3 py-1 lg:px-4 lg:py-2 rounded-lg border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs lg:text-sm transition">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        </div>

                        <!-- Next Button -->
                        @if($mauzos->hasMorePages())
                            <a href="{{ $mauzos->nextPageUrl() }}" class="px-3 py-1 lg:px-4 lg:py-2 rounded-lg border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs lg:text-sm transition flex items-center">
                                Mbele <i class="fas fa-chevron-right ml-1"></i>
                            </a>
                        @else
                            <span class="px-3 py-1 lg:px-4 lg:py-2 rounded-lg border text-gray-400 text-xs lg:text-sm cursor-not-allowed">
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
        <div class="bg-green-100 rounded-lg shadow-sm border p-4 lg:p-6 card-hover">
            <h2 class="text-lg font-bold mb-4 flex items-center text-black-800">
                <i class="fas fa-chart-bar mr-2 text-blue-600"></i>
                Mauzo ya Jumla
            </h2>

            <!-- Search Area -->
            <div class="mb-4">
                <input type="text" id="search-product" placeholder="Tafuta bidhaa..." class="w-full border border-gray-300 rounded-lg p-3 text-sm focus:ring-2 focus:ring-green-600 focus:border-green-600 transition">
            </div>

            <!-- Sales Summary Table -->
            <div class="overflow-x-auto rounded-lg shadow-sm border">
                <table class="w-full border-collapse" id="grouped-sales-table">
                    <thead class="bg-green-600">
                        <tr>
                            <th class="border px-4 py-3 text-left text-white text-sm">Tarehe</th>
                            <th class="border px-4 py-3 text-left text-white text-sm">Bidhaa</th>
                            <th class="border px-4 py-3 text-left text-white text-sm">Idadi</th>
                            <th class="border px-4 py-3 text-left text-white text-sm">Jumla</th>
                            <th class="border px-4 py-3 text-left text-white text-sm">Faida</th>
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
                                        'jumla' => 0,
                                        'faida' => 0
                                    ];
                                }
                                
                                $groupedSales[$key]['idadi'] += $sale->idadi;
                                $groupedSales[$key]['jumla'] += $sale->jumla;
                                $groupedSales[$key]['faida'] += ($sale->bei - ($sale->bidhaa->bei_nunua ?? 0)) * $sale->idadi;
                            }
                        @endphp
                        
                        @foreach($groupedSales as $sale)
                        <tr class="grouped-sales-row" data-product="{{ strtolower($sale['jina']) }}">
                            <td class="border px-4 py-3 text-sm">{{ $sale['tarehe'] }}</td>
                            <td class="border px-4 py-3 text-sm">{{ $sale['jina'] }}</td>
                            <td class="border px-4 py-3 text-center text-sm">{{ $sale['idadi'] }}</td>
                            <td class="border px-4 py-3 text-right text-sm">{{ number_format($sale['jumla']) }}</td>
                            <td class="border px-4 py-3 text-right text-sm">{{ number_format($sale['faida']) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 5: Risiti (NEW TAB) -->
    <div id="risiti-tab-content" class="tab-content hidden">
        <div class="bg-white rounded-xl shadow-lg border p-4 lg:p-6 card-hover">
            <h2 class="text-lg font-bold mb-4 flex items-center text-gray-800">
                <i class="fas fa-receipt mr-2 text-green-600"></i>
                Chapisha Risiti
            </h2>

            <!-- Search Receipt -->
            <div class="mb-6">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    <input type="text" id="search-receipt-input" placeholder="Weka namba ya risiti (MS-20260110-0001)..." class="pl-10 w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                </div>
                <div class="mt-2 text-sm text-gray-500 flex items-center">
                    <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                    Namba ya risiti inapatikana kwenye tab ya "Taarifa"
                </div>
            </div>

            <!-- Receipt Details -->
            <div id="receipt-details" class="hidden">
                <div class="bg-gray-50 rounded-lg border border-gray-200 p-4 mb-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="font-semibold text-gray-700">Risiti No:</span>
                        <span id="receipt-no-display" class="font-mono font-bold text-green-700"></span>
                    </div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="font-semibold text-gray-700">Tarehe:</span>
                        <span id="receipt-date-display"></span>
                    </div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="font-semibold text-gray-700">Idadi ya Bidhaa:</span>
                        <span id="receipt-items-count"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-semibold text-gray-700">Jumla:</span>
                        <span id="receipt-total-display" class="font-bold text-lg"></span>
                    </div>
                </div>

                <!-- Items List -->
                <div class="mb-4">
                    <h3 class="font-semibold text-gray-800 mb-2">Bidhaa:</h3>
                    <div id="receipt-items-list" class="space-y-2 max-h-60 overflow-y-auto">
                        <!-- Items will be populated here -->
                    </div>
                </div>

                <!-- Print Button -->
                <div class="flex justify-center">
                    <button id="print-thermal-receipt" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold flex items-center gap-2 shadow-md">
                        <i class="fas fa-print"></i>
                        Chapisha Risiti
                    </button>
                </div>
            </div>

            <!-- No Results Message -->
            <div id="no-receipt-found" class="hidden text-center py-8">
                <i class="fas fa-receipt text-4xl text-gray-400 mb-4"></i>
                <p class="text-gray-600">Hakuna risiti iliyopatikana.</p>
                <p class="text-sm text-gray-500 mt-2">Ingiza namba ya risiti ili kuona taarifa</p>
            </div>

            <!-- Loading -->
            <div id="receipt-loading" class="hidden text-center py-8">
                <i class="fas fa-spinner fa-spin text-2xl text-green-600 mb-4"></i>
                <p class="text-gray-600">Inatafuta taarifa...</p>
            </div>
        </div>
    </div>
</div>

<!-- Kikapu Modal -->
<div id="kikapu-modal" class="modal fixed inset-0 z-50 flex items-center justify-center hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-2xl shadow-xl w-full max-w-4xl mx-4 z-50 max-h-[85vh] overflow-auto">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center sticky top-0 bg-white">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-shopping-cart mr-2"></i>
                Bidhaa Zilizo Kwenye Kikapu
            </h3>
            <button type="button" id="close-kikapu-modal" class="text-gray-500 hover:text-gray-700 text-xl">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="p-6">
            <div id="empty-cart-message" class="text-center text-gray-600 py-10">
                <i class="fas fa-shopping-cart text-4xl text-gray-400 mb-4"></i>
                <p class="text-base">Hakuna bidhaa kwenye kikapu kwa sasa.</p>
            </div>
            
            <div id="cart-content" class="hidden">
                <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th class="border px-4 py-3 text-left">S/N</th>
                                <th class="border px-4 py-3 text-left">Bidhaa</th>
                                <th class="border px-4 py-3 text-left">Idadi</th>
                                <th class="border px-4 py-3 text-left">Bei</th>
                                <th class="border px-4 py-3 text-left">Punguzo</th>
                                <th class="border px-4 py-3 text-left">Jumla</th>
                                <th class="border px-4 py-3 text-left">Ondoa</th>
                            </tr>
                        </thead>
                        <tbody id="cart-tbody">
                            <!-- Cart items will be populated here -->
                        </tbody>
                    </table>
                </div>

                <!-- Total -->
                <div class="flex justify-between items-center my-4">
                    <div class="text-left font-semibold text-gray-800 text-lg">
                        Jumla ya gharama: 
                        <span class="text-green-600 text-xl" id="cart-total">0</span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-3">
                    <button id="clear-cart" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-3 rounded-lg flex items-center transition shadow-sm text-base">
                        <i class="fas fa-trash mr-2"></i>
                        Futa Kikapu
                    </button>
                    <button id="kopesha-kikapu-btn" class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-3 rounded-lg flex items-center transition shadow-sm text-base">
                        <i class="fas fa-hand-holding-usd mr-2"></i>
                        Kopesha
                    </button>
                    <button id="checkout-cart" class="bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg flex items-center transition shadow-sm text-base">
                        <i class="fas fa-check mr-2"></i>
                        Funga Kapu
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Kopesha Modal -->
<div id="kopesha-modal" class="modal fixed inset-0 z-50 flex items-center justify-center hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 z-50 max-h-[90vh] overflow-y-auto">
        <div class="bg-gradient-to-r from-amber-500 to-amber-600 p-5 text-white flex items-center sticky top-0">
            <i class="fas fa-hand-holding-usd mr-2"></i>
            <h2 class="text-lg font-semibold">Kopesha Bidhaa</h2>
        </div>

        <form method="POST" action="{{ route('madeni.store') }}" class="p-6 space-y-4" id="kopesha-form">
            @csrf

            <input type="hidden" name="bidhaa_id" id="kopesha-bidhaa-id">
            <input type="hidden" name="idadi" id="kopesha-idadi">
            <input type="hidden" name="jumla" id="kopesha-jumla">
            <input type="hidden" name="baki" id="kopesha-baki">
            <input type="hidden" name="bei" id="kopesha-bei">
            <input type="hidden" name="kopesha" value="1">

            <!-- Mteja Select -->
            <div>
                <label class="block font-semibold mb-1 text-gray-700">Mteja Aliyesajiliwa</label>
                <select id="mteja-select" class="w-full border border-amber-300 rounded-xl p-3 focus:ring-4 focus:ring-amber-300 focus:border-amber-400 hover:border-amber-400 transition text-base">
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
                <label class="block text-gray-700 mb-1 font-medium">Jina la Mkopaji</label>
                <input type="text" name="jina_mkopaji" id="kopesha-jina" class="w-full border border-amber-300 rounded-xl p-3 focus:ring-4 focus:ring-amber-300 focus:border-amber-400 transition text-base" required>
            </div>

            <div>
                <label class="block text-gray-700 mb-1 font-medium">Namba ya Simu</label>
                <input type="text" name="simu" id="kopesha-simu" class="w-full border border-amber-300 rounded-xl p-3 focus:ring-4 focus:ring-amber-300 focus:border-amber-400 transition text-base" required>
            </div>

            <div>
                <label class="block text-gray-700 mb-1 font-medium">Barua Pepe</label>
                <input type="email" name="barua_pepe" id="kopesha-barua-pepe" class="w-full border border-amber-300 rounded-xl p-3 focus:ring-4 focus:ring-amber-300 focus:border-amber-400 transition text-base">
            </div>

            <div>
                <label class="block text-gray-700 mb-1 font-medium">Anapoishi</label>
                <input type="text" name="anapoishi" id="kopesha-anapoishi" class="w-full border border-amber-300 rounded-xl p-3 focus:ring-4 focus:ring-amber-300 focus:border-amber-400 transition text-base">
            </div>

            <div>
                <label class="block text-gray-700 mb-1 font-medium">Tarehe ya Malipo</label>
                <input type="date" name="tarehe_malipo" class="w-full border border-amber-300 rounded-xl p-3 focus:ring-4 focus:ring-amber-300 focus:border-amber-400 transition text-base" required>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-3 pt-4">
                <button type="button" id="close-kopesha-modal" class="bg-gray-400 hover:bg-gray-500 px-5 py-3 rounded-xl text-white font-semibold transition text-base">
                    Funga
                </button>
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 px-5 py-3 rounded-xl text-white font-semibold flex items-center gap-1 transition shadow-md text-base">
                    <i class="fas fa-check mr-1"></i>
                    Hifadhi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Kopesha Kikapu Modal -->
<div id="kopesha-kikapu-modal" class="modal fixed inset-0 z-50 flex items-center justify-center hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 z-50">
        <div class="bg-gradient-to-r from-amber-500 to-amber-600 p-5 text-white flex items-center">
            <i class="fas fa-hand-holding-usd mr-2"></i>
            <h2 class="text-lg font-semibold">Kopesha Bidhaa za Kikapu</h2>
        </div>

        <form id="kopesha-kikapu-form" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block font-semibold mb-1 text-gray-700 text-base">Chagua Mteja</label>
                <select id="kikapu-mteja-select" class="w-full border border-amber-300 rounded-xl p-3 focus:ring-4 focus:ring-amber-300 focus:border-amber-400 transition text-base" required>
                    <option value="">-- Chagua Mteja --</option>
                    @foreach($wateja as $mteja)
                        <option value="{{ $mteja->jina }}">{{ $mteja->jina }} 
                            @if($mteja->simu)
                                - {{ $mteja->simu }}
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <button type="button" id="close-kopesha-kikapu-modal" class="bg-gray-400 hover:bg-gray-500 px-5 py-3 rounded-xl text-white font-semibold transition text-base">
                    Funga
                </button>
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 px-5 py-3 rounded-xl text-white font-semibold flex items-center gap-1 transition shadow-md text-base">
                    <i class="fas fa-check mr-1"></i>
                    Thibitisha Kopesha
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Sale Modal -->
<div id="delete-sale-modal" class="modal fixed inset-0 z-50 flex items-center justify-center hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 z-50">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Futa Mauzo</h3>
        </div>
        <div class="p-6">
            <p class="text-gray-700 mb-6 text-center text-base">
                Una uhakika unataka kufuta mauzo haya?
                <br>Hatua hii haiwezi kutenduliwa.
            </p>
            <div class="flex justify-center space-x-3">
                <button id="cancel-delete-sale" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-base">
                    Ghairi
                </button>
                <form id="delete-sale-form" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 text-base">
                        Ndio, Futa
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Desktop optimization */
@media (min-width: 1024px) {
    .container {
        max-width: 100%;
        padding: 0 1rem;
    }
    
    .space-y-6 > div {
        margin-bottom: 1.5rem;
    }
    
    .grid-cols-6 {
        grid-template-columns: repeat(6, minmax(0, 1fr));
    }
    
    .overflow-x-auto {
        overflow-x: visible;
    }
}

.modal {
    transition: opacity 0.3s ease;
}

.tab-content {
    transition: opacity 0.3s ease;
}

.hidden {
    display: none !important;
}

.tab-content.active {
    display: block;
}

.tab-button.active {
    border-bottom: 2px solid #10B981;
    color: #059669;
    font-weight: 600;
}

.sales-row.hidden, .barcode-row.hidden, .grouped-sales-row.hidden {
    display: none;
}

.card-hover {
    transition: all 0.3s ease;
}

.card-hover:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
}

.notification-success {
    border-left-color: #10B981;
}

.notification-error {
    border-left-color: #EF4444;
}

.notification-warning {
    border-left-color: #F59E0B;
}

/* Barcode row highlight */
.barcode-row.highlight {
    animation: highlight-row 0.5s ease;
    background-color: rgba(34, 197, 94, 0.1);
}

@keyframes highlight-row {
    0% { background-color: rgba(34, 197, 94, 0.3); }
    100% { background-color: rgba(34, 197, 94, 0.1); }
}

/* Tab persistence styling */
.tab-persist {
    background-color: #f0f9ff;
}

/* Copy receipt cursor */
.copy-receipt:hover {
    background-color: #dbeafe !important;
    cursor: copy;
}

/* Scrollbar styling */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
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
        this.currentTab = localStorage.getItem('currentMauzoTab') || 'sehemu';
        this.cart = JSON.parse(localStorage.getItem('mauzo_cart')) || [];
        this.bidhaaList = @json($bidhaa);
        this.barcodeScanTimeout = null;
        this.currentReceiptNo = null;
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
    }

    restoreTabState() {
        if (this.currentTab && this.currentTab !== 'sehemu') {
            this.showTab(this.currentTab);
        }
    }

    initReceiptLookup() {
        const searchInput = document.getElementById('search-receipt-input');
        const printButton = document.getElementById('print-thermal-receipt');
        
        if (searchInput) {
            // Handle Enter key press
            searchInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    this.lookupReceipt(searchInput.value.trim());
                }
            });
            
            // Handle input for real-time search (optional)
            searchInput.addEventListener('input', (e) => {
                const value = e.target.value.trim();
                if (value.length >= 10) {
                    this.lookupReceipt(value);
                }
            });
        }
        
        if (printButton) {
            printButton.addEventListener('click', () => {
                this.printThermalReceipt();
            });
        }
        
        // Add copy functionality to receipt numbers in Taarifa tab
        document.querySelectorAll('.copy-receipt').forEach(element => {
            element.addEventListener('click', (e) => {
                const receiptNo = e.target.dataset.receipt;
                navigator.clipboard.writeText(receiptNo).then(() => {
                    this.showNotification('Namba ya risiti imenakiliwa!', 'success');
                });
            });
        });
        
        // Add print functionality to individual receipt buttons
        document.querySelectorAll('.print-single-receipt').forEach(button => {
            button.addEventListener('click', (e) => {
                const receiptNo = e.target.closest('.print-single-receipt').dataset.receiptNo;
                if (receiptNo) {
                    this.printSingleReceipt(receiptNo);
                }
            });
        });
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

        // Kikapu button
        document.getElementById('kikapu-btn').addEventListener('click', () => {
            this.showKikapuModal();
        });

        // Sales form events
        this.bindSalesFormEvents();
        
        // Barcode events
        this.bindBarcodeEvents();
        
        // Search events
        this.bindSearchEvents();
        
        // Modal events
        this.bindModalEvents();
        
        // Cart events
        this.bindCartEvents();

        // Delete sale events
        this.bindDeleteEvents();

        // Handle page refresh
        window.addEventListener('beforeunload', () => {
            localStorage.setItem('currentMauzoTab', this.currentTab);
        });
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
        const discountBidhaaCheck = document.getElementById('punguzo-bidhaa-check');
        const discountJumlaCheck = document.getElementById('punguzo-jumla-check');
        const discountBidhaaInput = document.getElementById('discount-bidhaa-input');
        const discountJumlaInput = document.getElementById('discount-jumla-input');
        const discountBidhaaType = document.getElementById('discount-bidhaa-type');
        const discountJumlaType = document.getElementById('discount-jumla-type');
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

        if (discountBidhaaCheck) {
            discountBidhaaCheck.addEventListener('change', () => {
                discountBidhaaInput.disabled = !discountBidhaaCheck.checked;
                if (discountBidhaaType) discountBidhaaType.disabled = !discountBidhaaCheck.checked;
                this.updateTotals();
            });
        }

        if (discountJumlaCheck) {
            discountJumlaCheck.addEventListener('change', () => {
                discountJumlaInput.disabled = !discountJumlaCheck.checked;
                if (discountJumlaType) discountJumlaType.disabled = !discountJumlaCheck.checked;
                this.updateTotals();
            });
        }

        if (discountBidhaaInput) {
            discountBidhaaInput.addEventListener('input', () => this.updateTotals());
        }
        if (discountJumlaInput) {
            discountJumlaInput.addEventListener('input', () => this.updateTotals());
        }
        if (discountBidhaaType) {
            discountBidhaaType.addEventListener('change', () => this.updateTotals());
        }
        if (discountJumlaType) {
            discountJumlaType.addEventListener('change', () => this.updateTotals());
        }

        if (kopeshaBtn) {
            kopeshaBtn.addEventListener('click', () => {
                this.openKopeshaModal();
            });
        }

        if (addToCartBtn) {
            addToCartBtn.addEventListener('click', () => {
                this.addToCart();
            });
        }

        salesForm.addEventListener('submit', (e) => {
            e.preventDefault();
            this.submitForm(e.target);
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
        }
        
        this.updateTotals();
    }

    updateTotals() {
        const quantityInput = document.getElementById('quantity-input');
        const priceInput = document.getElementById('price-input');
        const totalInput = document.getElementById('total-input');
        
        if (!quantityInput || !priceInput || !totalInput) return;

        const quantity = parseInt(quantityInput.value) || 0;
        const price = parseFloat(priceInput.value) || 0;
        
        let baseTotal = quantity * price;
        let finalTotal = baseTotal;

        const discountBidhaaCheck = document.getElementById('punguzo-bidhaa-check');
        if (discountBidhaaCheck && discountBidhaaCheck.checked) {
            const discountBidhaaInput = document.getElementById('discount-bidhaa-input');
            const discountBidhaaType = document.getElementById('discount-bidhaa-type');
            
            if (discountBidhaaInput && discountBidhaaType) {
                const discountValue = parseFloat(discountBidhaaInput.value) || 0;
                const discountType = discountBidhaaType.value;
                
                if (discountType === 'percent') {
                    const discountPerItem = (price * discountValue) / 100;
                    finalTotal = (price - discountPerItem) * quantity;
                } else {
                    finalTotal = (price - discountValue) * quantity;
                }
            }
        }
        
        const discountJumlaCheck = document.getElementById('punguzo-jumla-check');
        if (discountJumlaCheck && discountJumlaCheck.checked) {
            const discountJumlaInput = document.getElementById('discount-jumla-input');
            const discountJumlaType = document.getElementById('discount-jumla-type');
            
            if (discountJumlaInput && discountJumlaType) {
                const discountValue = parseFloat(discountJumlaInput.value) || 0;
                const discountType = discountJumlaType.value;
                
                if (discountType === 'percent') {
                    finalTotal -= (finalTotal * discountValue) / 100;
                } else {
                    finalTotal -= discountValue;
                }
            }
        }
        
        finalTotal = Math.max(0, finalTotal);
        totalInput.value = finalTotal.toFixed(2);
        
        const kopeshaIdadi = document.getElementById('kopesha-idadi');
        const kopeshaJumla = document.getElementById('kopesha-jumla');
        const kopeshaBaki = document.getElementById('kopesha-baki');
        
        if (kopeshaIdadi) kopeshaIdadi.value = quantity;
        if (kopeshaJumla) kopeshaJumla.value = finalTotal;
        if (kopeshaBaki) kopeshaBaki.value = finalTotal;
    }

    async submitForm(form) {
        const bidhaaId = document.getElementById('bidhaaSelect').value;
        const quantity = document.getElementById('quantity-input').value;

        if (!bidhaaId || !quantity || quantity < 1) {
            this.showNotification('Tafadhali chagua bidhaa na idadi sahihi!', 'error');
            return;
        }

        const formData = new FormData(form);

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
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                this.showNotification(data.message || 'Kuna tatizo kwenye kuhifadhi!', 'error');
            }

        } catch (error) {
            console.error('Error:', error);
            this.showNotification('Kuna tatizo kwenye kuhifadhi!', 'error');
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
        
        if (bidhaaSearch) bidhaaSearch.value = '';
        if (priceInput) priceInput.value = '';
        if (stockInput) stockInput.value = '';
        if (totalInput) totalInput.value = '';
        if (quantityInput) quantityInput.value = 1;
        
        const discountBidhaaCheck = document.getElementById('punguzo-bidhaa-check');
        const discountJumlaCheck = document.getElementById('punguzo-jumla-check');
        const discountBidhaaInput = document.getElementById('discount-bidhaa-input');
        const discountJumlaInput = document.getElementById('discount-jumla-input');
        const discountBidhaaType = document.getElementById('discount-bidhaa-type');
        const discountJumlaType = document.getElementById('discount-jumla-type');
        
        if (discountBidhaaCheck) discountBidhaaCheck.checked = false;
        if (discountJumlaCheck) discountJumlaCheck.checked = false;
        if (discountBidhaaInput) {
            discountBidhaaInput.value = '';
            discountBidhaaInput.disabled = true;
        }
        if (discountJumlaInput) {
            discountJumlaInput.value = '';
            discountJumlaInput.disabled = true;
        }
        if (discountBidhaaType) discountBidhaaType.disabled = true;
        if (discountJumlaType) discountJumlaType.disabled = true;
    }

    openKopeshaModal() {
        const bidhaaId = document.getElementById('bidhaaSelect').value;
        const quantity = document.getElementById('quantity-input').value;
        
        if (!bidhaaId || !quantity || quantity < 1) {
            this.showNotification('Tafadhali chagua bidhaa na idadi sahihi!', 'error');
            return;
        }

        const mtejaSelect = document.getElementById('mteja-select');
        if (mtejaSelect) {
            mtejaSelect.addEventListener('change', this.fillMtejaDetails.bind(this));
        }

        const kopeshaModal = document.getElementById('kopesha-modal');
        if (kopeshaModal) {
            kopeshaModal.classList.remove('hidden');
        }
    }

    fillMtejaDetails() {
        const select = document.getElementById('mteja-select');
        if (!select) return;

        const selectedOption = select.options[select.selectedIndex];
        const kopeshaJina = document.getElementById('kopesha-jina');
        const kopeshaSimu = document.getElementById('kopesha-simu');
        const kopeshaBaruaPepe = document.getElementById('kopesha-barua-pepe');
        const kopeshaAnapoishi = document.getElementById('kopesha-anapoishi');
        
        if (selectedOption.value) {
            if (kopeshaJina) kopeshaJina.value = selectedOption.dataset.jina || '';
            if (kopeshaSimu) kopeshaSimu.value = selectedOption.dataset.simu || '';
            if (kopeshaBaruaPepe) kopeshaBaruaPepe.value = selectedOption.dataset.barua_pepe || '';
            if (kopeshaAnapoishi) kopeshaAnapoishi.value = selectedOption.dataset.anapoishi || '';
        } else {
            if (kopeshaJina) kopeshaJina.value = '';
            if (kopeshaSimu) kopeshaSimu.value = '';
            if (kopeshaBaruaPepe) kopeshaBaruaPepe.value = '';
            if (kopeshaAnapoishi) kopeshaAnapoishi.value = '';
        }
    }

    addToCart() {
        const bidhaaSelect = document.getElementById('bidhaaSelect');
        const quantityInput = document.getElementById('quantity-input');
        const priceInput = document.getElementById('price-input');
        
        if (!bidhaaSelect || !quantityInput || !priceInput) return;

        const selectedOption = bidhaaSelect.options[bidhaaSelect.selectedIndex];
        const quantity = parseInt(quantityInput.value) || 0;
        const price = parseFloat(priceInput.value) || 0;
        
        const discountBidhaaCheck = document.getElementById('punguzo-bidhaa-check');
        const discountJumlaCheck = document.getElementById('punguzo-jumla-check');
        const discountBidhaaInput = document.getElementById('discount-bidhaa-input');
        const discountJumlaInput = document.getElementById('discount-jumla-input');
        const discountBidhaaType = document.getElementById('discount-bidhaa-type');
        const discountJumlaType = document.getElementById('discount-jumla-type');

        if (!selectedOption.value || quantity < 1) {
            this.showNotification('Tafadhali chagua bidhaa na idadi sahihi!', 'error');
            return;
        }

        const productName = selectedOption.text.split(' (')[0];
        const total = parseFloat(document.getElementById('total-input').value) || 0;

        const cartItem = {
            jina: productName,
            bei: price,
            idadi: quantity,
            punguzo_bidhaa: discountBidhaaCheck && discountBidhaaCheck.checked ? parseFloat(discountBidhaaInput.value) || 0 : 0,
            punguzo_jumla: discountJumlaCheck && discountJumlaCheck.checked ? parseFloat(discountJumlaInput.value) || 0 : 0,
            discount_bidhaa_type: discountBidhaaType ? discountBidhaaType.value : 'fixed',
            discount_jumla_type: discountJumlaType ? discountJumlaType.value : 'fixed',
            jumla: total,
            bidhaa_id: selectedOption.value,
            timestamp: new Date().toISOString()
        };

        this.cart.push(cartItem);
        this.saveCart();
        this.updateCartCount();
        
        this.showNotification('Bidhaa imeongezwa kwenye kikapu!', 'success');
        this.resetForm();
    }

    bindBarcodeEvents() {
        const addBarcodeRowBtn = document.getElementById('add-barcode-row');
        const barcodeForm = document.getElementById('barcode-form');
        const clearBarcodeFormBtn = document.getElementById('clear-barcode-form');

        if (addBarcodeRowBtn) {
            addBarcodeRowBtn.addEventListener('click', () => {
                this.addBarcodeRow();
            });
        }

        if (barcodeForm) {
            barcodeForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.submitBarcodeSales();
            });
        }

        if (clearBarcodeFormBtn) {
            clearBarcodeFormBtn.addEventListener('click', () => {
                if (confirm('Unahakika unataka kufuta bidhaa zote zilizoscan?')) {
                    this.clearBarcodeRows();
                    this.showNotification('Bidhaa zote zimefutwa!', 'success');
                }
            });
        }
    }

    addBarcodeRow() {
        const tbody = document.getElementById('barcode-tbody');
        if (!tbody) return;

        const newRow = document.createElement('tr');
        newRow.className = 'barcode-row';
        newRow.innerHTML = `
            <td class="px-3 lg:px-4 py-2 lg:py-3">
                <input type="text" name="barcode[]" placeholder="Scan barcode" 
                       class="barcode-input border border-green-200 focus:border-green-500 focus:ring-2 focus:ring-green-300 rounded-lg p-2 lg:p-3 w-full text-sm transition-all" />
            </td>
            <td class="px-3 lg:px-4 py-2 lg:py-3">
                <input type="text" name="jina[]" readonly placeholder="Jina la Bidhaa" 
                       class="product-name border border-green-100 bg-gray-50 text-gray-700 rounded-lg p-2 lg:p-3 w-full text-sm" />
            </td>
            <td class="px-3 lg:px-4 py-2 lg:py-3">
                <input type="number" name="bei[]" readonly placeholder="Bei" 
                       class="product-price border border-green-100 bg-gray-50 text-gray-700 rounded-lg p-2 lg:p-3 w-full text-sm" />
            </td>
            <td class="px-3 lg:px-4 py-2 lg:py-3">
                <input type="number" name="idadi[]" min="1" value="1" placeholder="Idadi" 
                       class="quantity-input border border-green-200 focus:border-green-500 focus:ring-2 focus:ring-green-300 rounded-lg p-2 lg:p-3 w-full text-sm transition-all" />
            </td>
            <td class="px-3 lg:px-4 py-2 lg:py-3">
                <input type="number" name="stock[]" readonly placeholder="Baki" 
                       class="stock-input border border-green-100 bg-gray-50 text-gray-700 rounded-lg p-2 lg:p-3 w-full text-sm" />
            </td>
            <td class="px-3 lg:px-4 py-2 lg:py-3">
                <input type="number" name="jumla[]" readonly placeholder="Jumla" 
                       class="total-input border border-green-100 bg-gray-50 text-gray-700 rounded-lg p-2 lg:p-3 w-full text-sm" />
            </td>
            <td class="px-3 lg:px-4 py-2 lg:py-3 text-center">
                <button type="button" class="remove-barcode-row text-red-500 hover:text-red-700 p-2 rounded-full transition transform hover:scale-110" title="Futa bidhaa">
                    <i class="fas fa-trash text-sm"></i>
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

        if (removeBtn) {
            removeBtn.addEventListener('click', () => {
                const rows = document.querySelectorAll('.barcode-row');
                if (rows.length > 1) {
                    row.remove();
                    this.updateBarcodeTotal();
                    this.showNotification('Bidhaa imeondolewa!', 'success');
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
            
            this.addBarcodeRow();
            
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
            
            if (productName) productName.value = '';
            if (productPrice) productPrice.value = '';
            if (stockInput) stockInput.value = '';
            if (totalInput) totalInput.value = '';
            if (quantityInput) quantityInput.value = 1;
        }
        
        this.updateBarcodeTotal();
        
        const firstBarcodeInput = document.querySelector('.barcode-input');
        if (firstBarcodeInput) {
            firstBarcodeInput.focus();
        }
    }

    updateBarcodeRowTotal(row) {
        const productPrice = row.querySelector('.product-price');
        const quantityInput = row.querySelector('.quantity-input');
        const stockInput = row.querySelector('.stock-input');
        const totalInput = row.querySelector('.total-input');

        if (!productPrice || !quantityInput || !totalInput) return;

        const price = parseFloat(productPrice.value) || 0;
        const quantity = parseInt(quantityInput.value) || 0;
        const stock = stockInput ? parseInt(stockInput.value) || 0 : 0;
        
        if (stock > 0 && quantity > stock) {
            this.showNotification('Idadi uliyoiingiza inazidi idadi iliyopo!', 'error');
            quantityInput.value = stock;
            return;
        }
        
        const total = price * quantity;
        totalInput.value = total;
        
        this.updateBarcodeTotal();
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

    submitBarcodeSales() {
        const items = [];
        let hasValidItems = false;
        let totalItems = 0;

        document.querySelectorAll('.barcode-row').forEach(row => {
            const barcodeInput = row.querySelector('.barcode-input');
            const quantityInput = row.querySelector('.quantity-input');
            const productName = row.querySelector('.product-name');
            const productPrice = row.querySelector('.product-price');
            
            if (barcodeInput && quantityInput && productName && productPrice) {
                const barcode = barcodeInput.value.trim();
                const quantity = parseInt(quantityInput.value) || 0;
                const product = productName.value.trim();
                const price = parseFloat(productPrice.value) || 0;
                
                if (barcode && quantity > 0 && product && price > 0) {
                    items.push({
                        barcode: barcode,
                        idadi: quantity,
                        product_name: product,
                        bei: price,
                        punguzo: 0,
                        jumla: price * quantity
                    });
                    hasValidItems = true;
                    totalItems++;
                }
            }
        });

        if (!hasValidItems) {
            this.showNotification('Tafadhali angalau bidhaa moja iwe na barcode na idadi sahihi!', 'error');
            return;
        }

        if (confirm(`Unahitaji kuuza bidhaa ${totalItems} zilizopatikana?\nJumla: ${document.getElementById('barcode-total').textContent}`)) {
            fetch("{{ route('mauzo.store.barcode') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ 
                    items: items
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.showNotification('Mauzo yamehifadhiwa! Namba ya risiti: ' + data.receipt_no, 'success');
                    
                    this.clearBarcodeRows();
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else if (data.errors) {
                    this.showNotification('Hitilafu: ' + Object.values(data.errors).join(', '), 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                this.showNotification('Kuna tatizo kwenye kuhifadhi mauzo!', 'error');
            });
        }
    }

    bindSearchEvents() {
        const searchSales = document.getElementById('search-sales');
        if (searchSales) {
            searchSales.addEventListener('input', (e) => {
                const searchTerm = e.target.value.toLowerCase();
                document.querySelectorAll('.sales-row').forEach(row => {
                    const product = row.dataset.product;
                    const date = row.dataset.date;
                    if (product.includes(searchTerm) || date.includes(searchTerm)) {
                        row.classList.remove('hidden');
                    } else {
                        row.classList.add('hidden');
                    }
                });
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

    bindModalEvents() {
        const closeModal = (modalId) => {
            const modal = document.getElementById(modalId);
            if (modal) modal.classList.add('hidden');
        };

        const closeKopeshaBtn = document.getElementById('close-kopesha-modal');
        const kopeshaModal = document.getElementById('kopesha-modal');
        
        if (closeKopeshaBtn && kopeshaModal) {
            closeKopeshaBtn.addEventListener('click', () => closeModal('kopesha-modal'));
            kopeshaModal.addEventListener('click', (e) => {
                if (e.target === kopeshaModal || e.target.classList.contains('modal-overlay')) {
                    closeModal('kopesha-modal');
                }
            });
        }

        const closeKikapuBtn = document.getElementById('close-kikapu-modal');
        const kikapuModal = document.getElementById('kikapu-modal');
        
        if (closeKikapuBtn && kikapuModal) {
            closeKikapuBtn.addEventListener('click', () => closeModal('kikapu-modal'));
            kikapuModal.addEventListener('click', (e) => {
                if (e.target === kikapuModal || e.target.classList.contains('modal-overlay')) {
                    closeModal('kikapu-modal');
                }
            });
        }

        const closeKopeshaKikapuBtn = document.getElementById('close-kopesha-kikapu-modal');
        const kopeshaKikapuModal = document.getElementById('kopesha-kikapu-modal');
        
        if (closeKopeshaKikapuBtn && kopeshaKikapuModal) {
            closeKopeshaKikapuBtn.addEventListener('click', () => closeModal('kopesha-kikapu-modal'));
            kopeshaKikapuModal.addEventListener('click', (e) => {
                if (e.target === kopeshaKikapuModal || e.target.classList.contains('modal-overlay')) {
                    closeModal('kopesha-kikapu-modal');
                }
            });
        }

        const cancelDeleteSale = document.getElementById('cancel-delete-sale');
        const deleteSaleModal = document.getElementById('delete-sale-modal');
        
        if (cancelDeleteSale && deleteSaleModal) {
            cancelDeleteSale.addEventListener('click', () => closeModal('delete-sale-modal'));
            deleteSaleModal.addEventListener('click', (e) => {
                if (e.target === deleteSaleModal || e.target.classList.contains('modal-overlay')) {
                    closeModal('delete-sale-modal');
                }
            });
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeModal('kopesha-modal');
                closeModal('kikapu-modal');
                closeModal('kopesha-kikapu-modal');
                closeModal('delete-sale-modal');
            }
        });
    }

    bindCartEvents() {
        const clearCartBtn = document.getElementById('clear-cart');
        const checkoutCartBtn = document.getElementById('checkout-cart');
        const kopeshaKikapuBtn = document.getElementById('kopesha-kikapu-btn');
        const kopeshaKikapuForm = document.getElementById('kopesha-kikapu-form');

        if (clearCartBtn) {
            clearCartBtn.addEventListener('click', () => {
                this.clearCart();
            });
        }

        if (checkoutCartBtn) {
            checkoutCartBtn.addEventListener('click', () => {
                this.checkoutCart();
            });
        }

        if (kopeshaKikapuBtn) {
            kopeshaKikapuBtn.addEventListener('click', () => {
                this.openKopeshaKikapuModal();
            });
        }

        if (kopeshaKikapuForm) {
            kopeshaKikapuForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.submitKopeshaKikapu();
            });
        }
    }

    bindDeleteEvents() {
        document.querySelectorAll('.delete-sale-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const saleId = this.dataset.id;
                const deleteModal = document.getElementById('delete-sale-modal');
                const deleteForm = document.getElementById('delete-sale-form');
                
                if (deleteForm && deleteModal) {
                    deleteForm.action = `/mauzo/${saleId}`;
                    deleteModal.classList.remove('hidden');
                }
            });
        });
    }

    showKikapuModal() {
        this.updateCartDisplay();
        const kikapuModal = document.getElementById('kikapu-modal');
        if (kikapuModal) {
            kikapuModal.classList.remove('hidden');
        }
    }

    openKopeshaKikapuModal() {
        if (this.cart.length === 0) {
            this.showNotification('Kikapu hakina bidhaa!', 'error');
            return;
        }
        const kopeshaKikapuModal = document.getElementById('kopesha-kikapu-modal');
        if (kopeshaKikapuModal) {
            kopeshaKikapuModal.classList.remove('hidden');
        }
    }

    submitKopeshaKikapu() {
        const mtejaSelect = document.getElementById('kikapu-mteja-select');
        if (!mtejaSelect) return;

        const selectedMteja = mtejaSelect.value;
        
        if (!selectedMteja) {
            this.showNotification('Tafadhali chagua mteja!', 'error');
            return;
        }

        fetch("{{ route('mauzo.store.kikapu.loan') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ 
                jina: selectedMteja,
                items: this.cart 
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                this.showNotification(data.message, 'success');
                this.clearCart();
                this.closeModal('kikapu-modal');
                this.closeModal('kopesha-kikapu-modal');
                
                mtejaSelect.value = '';
                
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                this.showNotification(data.message || 'Kuna tatizo kwenye kukopesha bidhaa!', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            this.showNotification('Kuna tatizo kwenye kukopesha bidhaa!', 'error');
        });
    }

    updateCartDisplay() {
        const cartTbody = document.getElementById('cart-tbody');
        const emptyMessage = document.getElementById('empty-cart-message');
        const cartContent = document.getElementById('cart-content');
        const cartTotal = document.getElementById('cart-total');

        if (!cartTbody || !emptyMessage || !cartContent || !cartTotal) return;

        if (this.cart.length === 0) {
            emptyMessage.classList.remove('hidden');
            cartContent.classList.add('hidden');
        } else {
            emptyMessage.classList.add('hidden');
            cartContent.classList.remove('hidden');

            cartTbody.innerHTML = '';
            let total = 0;

            this.cart.forEach((item, index) => {
                total += item.jumla;
                
                const row = document.createElement('tr');
                row.className = 'border-b hover:bg-gray-50 transition';
                row.innerHTML = `
                    <td class="border px-4 py-3 text-center text-sm">${index + 1}</td>
                    <td class="border px-4 py-3 text-sm">${item.jina}</td>
                    <td class="border px-4 py-3 text-center text-sm">${item.idadi}</td>
                    <td class="border px-4 py-3 text-right text-sm">${item.bei.toLocaleString()}</td>
                    <td class="border px-4 py-3 text-right text-sm">${(item.punguzo_bidhaa + item.punguzo_jumla).toLocaleString()}</td>
                    <td class="border px-4 py-3 text-right text-sm">${item.jumla.toLocaleString()}</td>
                    <td class="border px-4 py-3 text-center">
                        <button type="button" class="remove-cart-item text-red-500 hover:text-red-700 transition text-sm" data-index="${index}" title="Ondoa Bidhaa">
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
                    this.removeFromCart(index);
                });
            });
        }
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
        this.showNotification('Kikapu limefutwa!', 'success');
    }

    checkoutCart() {
        if (this.cart.length === 0) {
            this.showNotification('Kikapu hakina bidhaa!', 'error');
            return;
        }

        fetch("{{ route('mauzo.store.kikapu') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ 
                items: this.cart
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                this.showNotification('Mauzo ya kikapu yamehifadhiwa! Namba ya risiti: ' + data.receipt_no, 'success');
                
                this.clearCart();
                this.closeModal('kikapu-modal');
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                this.showNotification('Kuna tatizo kwenye kuhifadhi mauzo ya kikapu!', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            this.showNotification('Kuna tatizo kwenye kuhifadhi mauzo ya kikapu!', 'error');
        });
    }

    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) modal.classList.add('hidden');
    }

    saveCart() {
        localStorage.setItem('mauzo_cart', JSON.stringify(this.cart));
    }

    updateCartCount() {
        const cartCount = document.getElementById('cart-count');
        if (cartCount) {
            if (this.cart.length > 0) {
                cartCount.textContent = this.cart.length;
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

    showNotification(message, type = 'success') {
        const notification = document.getElementById('notification');
        const notificationIcon = document.getElementById('notification-icon');
        const notificationMessage = document.getElementById('notification-message');
        
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
        
        notification.classList.remove('hidden');
        
        setTimeout(() => {
            notification.classList.add('hidden');
        }, 3000);
    }

    lookupReceipt(receiptNo) {
        if (!receiptNo) {
            this.showNotification('Tafadhali ingiza namba ya risiti', 'error');
            return;
        }

        const detailsDiv = document.getElementById('receipt-details');
        const noResultsDiv = document.getElementById('no-receipt-found');
        const loadingDiv = document.getElementById('receipt-loading');

        // Show loading
        detailsDiv.classList.add('hidden');
        noResultsDiv.classList.add('hidden');
        loadingDiv.classList.remove('hidden');

        fetch(`/mauzo/receipt-print/${receiptNo}`)
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
            itemDiv.className = 'bg-white border border-gray-200 rounded-lg p-3';
            itemDiv.innerHTML = `
                <div class="flex justify-between items-center mb-1">
                    <span class="font-medium text-gray-800">${item.bidhaa}</span>
                    <span class="text-sm text-gray-600">${item.idadi} x ${item.bei.toLocaleString()}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">Jumla:</span>
                    <span class="font-semibold">${item.jumla.toLocaleString()}/=</span>
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
@endpush,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,controller  of it <?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Mauzo;
use App\Models\Bidhaa;
use App\Models\Matumizi;
use App\Models\Madeni;
use App\Models\Mteja;
use App\Models\Marejesho;
use Carbon\Carbon;

class MauzoController extends Controller
{
    // Helper method to get authenticated user from any guard
    private function getAuthUser()
    {
        if (Auth::guard('admin')->check()) {
            return Auth::guard('admin')->user();
        }
        
        if (Auth::guard('web')->check()) {
            return Auth::guard('web')->user();
        }
        
        if (Auth::guard('mfanyakazi')->check()) {
            return Auth::guard('mfanyakazi')->user();
        }
        
        return null;
    }
    
    // Helper method to get company ID
    private function getCompanyId()
    {
        $user = $this->getAuthUser();
        return $user ? $user->company_id : null;
    }

    // Generate receipt number - ALWAYS generate
    private function generateReceiptNo($companyId)
    {
        $date = Carbon::now()->format('Ymd');
        $prefix = "MS-{$date}-";
        
        $lastReceipt = Mauzo::where('company_id', $companyId)
            ->where('receipt_no', 'like', $prefix . '%')
            ->orderBy('receipt_no', 'desc')
            ->first();
        
        if ($lastReceipt) {
            $lastNumber = intval(substr($lastReceipt->receipt_no, strlen($prefix)));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return $prefix . $newNumber;
    }

    // Display main sales page
    public function index()
    {
        $user = $this->getAuthUser();
        
        if (!$user) {
            return redirect()->route('login')->withErrors(['Unauthorized access']);
        }
        
        $companyId = $user->company_id;
        
        $bidhaa = Bidhaa::where('company_id', $companyId)
            ->select('id', 'jina', 'bei_kuuza', 'idadi', 'barcode', 'aina', 'kipimo')
            ->orderBy('jina')
            ->get();

        $mauzos = Mauzo::with('bidhaa')
            ->where('company_id', $companyId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $matumizi = Matumizi::where('company_id', $companyId)
            ->latest()
            ->get();

        $wateja = Mteja::where('company_id', $companyId)
            ->orderBy('jina')
            ->get();

        $madeni = Madeni::with('bidhaa')
            ->where('company_id', $companyId)
            ->latest()
            ->get();

        $marejeshos = Marejesho::with(['madeni.bidhaa'])
            ->where('company_id', $companyId)
            ->get();
            
        return view('mauzo.index', compact('bidhaa', 'mauzos', 'matumizi', 'wateja', 'madeni','marejeshos'));
    }

    // Store sale (normal or loan)
    public function store(Request $request)
    {
        $user = $this->getAuthUser();
        if (!$user) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                    'notification' => 'Unauthorized!'
                ], 401);
            }
            return redirect()->back()->withErrors(['Unauthorized access']);
        }
        
        if ($request->has('kopesha') && $request->kopesha == '1') {
            return $this->storeLoan($request);
        }

        return $this->storeRegularSale($request);
    }

    // Store sale via barcode
    public function storeBarcode(Request $request)
    {
        $user = $this->getAuthUser();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
                'notification' => 'Unauthorized!'
            ], 401);
        }
        
        $companyId = $user->company_id;
        $items = $request->input('items', []);

        return DB::transaction(function () use ($items, $companyId, $request) {
            $results = [];
            $receiptNo = $this->generateReceiptNo($companyId);
            
            foreach ($items as $item) {
                $validated = Validator::make($item, [
                    'barcode' => 'required|string|exists:bidhaas,barcode',
                    'idadi'   => 'required|integer|min:1',
                    'punguzo' => 'nullable|numeric',
                ])->validate();

                $bidhaa = Bidhaa::where('barcode', $validated['barcode'])
                    ->where('company_id', $companyId)
                    ->firstOrFail();

                if ($bidhaa->expiry < now()->toDateString()) {
                    return response()->json([
                        'message' => "Bidhaa {$bidhaa->jina} ime-expire.",
                        'notification' => 'Bidhaa ime-expire!'
                    ], 422);
                }

                if ($validated['idadi'] > $bidhaa->idadi) {
                    return response()->json([
                        'message' => "Stock haijatosha kwa {$bidhaa->jina}, baki ni {$bidhaa->idadi}.",
                        'notification' => 'Stock haitoshi!'
                    ], 422);
                }

                $jumla = ($bidhaa->bei_kuuza * $validated['idadi']) - ($validated['punguzo'] ?? 0);
                $bidhaa->decrement('idadi', $validated['idadi']);

                $mauzo = Mauzo::create([
                    'company_id' => $companyId,
                    'receipt_no' => $receiptNo,
                    'bidhaa_id'  => $bidhaa->id,
                    'idadi'      => $validated['idadi'],
                    'bei'        => $bidhaa->bei_kuuza,
                    'punguzo'    => $validated['punguzo'] ?? 0,
                    'jumla'      => $jumla,
                ]);

                $results[] = $mauzo;
            }

            $response = [
                'success' => true,
                'message' => 'Mauzo yamerekodiwa kikamilifu!',
                'notification' => 'Mauzo yamefanikiwa!',
                'receipt_no' => $receiptNo,
            ];

            return response()->json($response);

        });
    }

    // Delete sale
    public function destroy($id)
    {
        $user = $this->getAuthUser();
        if (!$user) {
            return redirect()->back()->withErrors(['Unauthorized access']);
        }
        
        $companyId = $user->company_id;
        $mauzo = Mauzo::where('id', $id)
            ->where('company_id', $companyId)
            ->firstOrFail();

        DB::transaction(function () use ($mauzo) {
            $bidhaa = $mauzo->bidhaa;
            if ($bidhaa) {
                $bidhaa->increment('idadi', $mauzo->idadi);
            }

            $mauzo->delete();
        });

        return redirect()->back()->with([
            'success' => 'Rekodi ya mauzo imefutwa kikamilifu!',
            'notification' => 'Rekodi imefutwa!'
        ]);
    }

    // Handle loan sales
    private function storeLoan(Request $request)
    {
        $user = $this->getAuthUser();
        if (!$user) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                    'notification' => 'Unauthorized!'
                ], 401);
            }
            return redirect()->back()->withErrors(['Unauthorized access']);
        }
        
        $companyId = $user->company_id;

        return DB::transaction(function () use ($request, $companyId) {
            $request->validate([
                'bidhaa_id'      => 'required|exists:bidhaas,id',
                'idadi'          => 'required|integer|min:1',
                'bei'            => 'required|numeric',
                'jumla'          => 'required|numeric',
                'jina_mkopaji'   => 'required|string|max:255',
                'simu'           => 'required|string|max:20',
                'tarehe_malipo'  => 'required|date',
            ]);

            $bidhaa = Bidhaa::where('id', $request->bidhaa_id)
                ->where('company_id', $companyId)
                ->firstOrFail();

            if ($bidhaa->expiry < now()->toDateString()) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => "Bidhaa hii ime-expire.",
                        'notification' => 'Bidhaa ime-expire!'
                    ], 422);
                }
                return redirect()->back()->withErrors([
                    'bidhaa_id' => "Bidhaa hii ime-expire.",
                    'notification' => 'Bidhaa ime-expire!'
                ]);
            }

            if ($request->idadi > $bidhaa->idadi) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => "Stock haijatosha, baki ni {$bidhaa->idadi}.",
                        'notification' => 'Stock haitoshi!'
                    ], 422);
                }
                return redirect()->back()->withErrors([
                    'idadi' => "Stock haijatosha, baki ni {$bidhaa->idadi}.",
                    'notification' => 'Stock haitoshi!'
                ]);
            }

            $bidhaa->decrement('idadi', $request->idadi);

            $deni = Madeni::create([
                'company_id'    => $companyId,
                'bidhaa_id'     => $request->bidhaa_id,
                'idadi'         => $request->idadi,
                'bei'           => $request->bei,
                'jumla'         => $request->jumla,
                'jina_mkopaji'  => $request->jina_mkopaji,
                'simu'          => $request->simu,
                'tarehe_malipo' => $request->tarehe_malipo,
                'baki'          => $request->jumla,
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Deni limerekodiwa kikamilifu!',
                    'notification' => 'Deni limefanikiwa!'
                ]);
            }

            return redirect()->back()->with([
                'success' => 'Deni limerekodiwa kikamilifu!',
                'notification' => 'Deni limefanikiwa!'
            ]);
        });
    }

    // Handle regular sales
    private function storeRegularSale(Request $request)
    {
        $user = $this->getAuthUser();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
                'notification' => 'Unauthorized!'
            ], 401);
        }
        
        $companyId = $user->company_id;

        return DB::transaction(function () use ($request, $companyId) {

            $validator = Validator::make($request->all(), [
                'bidhaa_id' => 'required|exists:bidhaas,id',
                'idadi'     => 'required|integer|min:1',
                'bei'       => 'required|numeric',
                'punguzo'   => 'nullable|numeric',
                'jumla'     => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'notification' => 'Kosa katika taarifa!'
                ], 422);
            }

            $bidhaa = Bidhaa::where('id', $request->bidhaa_id)
                ->where('company_id', $companyId)
                ->first();

            if (!$bidhaa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bidhaa haipatikani',
                    'notification' => 'Bidhaa haipo!'
                ], 404);
            }

            if ($bidhaa->expiry < now()->toDateString()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bidhaa hii ime-expire',
                    'notification' => 'Bidhaa ime-expire!'
                ], 422);
            }

            if ($request->idadi > $bidhaa->idadi) {
                return response()->json([
                    'success' => false,
                    'message' => "Stock haijatosha, baki ni {$bidhaa->idadi}",
                    'notification' => 'Stock haitoshi!'
                ], 422);
            }

            $bidhaa->decrement('idadi', $request->idadi);

            $receiptNo = $this->generateReceiptNo($companyId);

            $mauzo = Mauzo::create([
                'company_id' => $companyId,
                'receipt_no' => $receiptNo,
                'bidhaa_id'  => $request->bidhaa_id,
                'idadi'      => $request->idadi,
                'bei'        => $request->bei,
                'punguzo'    => $request->punguzo ?? 0,
                'jumla'      => $request->jumla,
            ]);

            $response = [
                'success' => true,
                'message' => 'Mauzo yamerekodiwa kikamilifu!',
                'notification' => 'Mauzo yamefanikiwa!',
                'receipt_no' => $receiptNo,
            ];

            return response()->json($response);

        });
    }

    // Handle basket (kikapu) sales
    public function storeKikapu(Request $request)
    {
        $user = $this->getAuthUser();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access',
                'notification' => 'Unauthorized!'
            ], 401);
        }
        
        $companyId = $user->company_id;

        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.jina' => 'required|string',
            'items.*.bei' => 'required|numeric',
            'items.*.idadi' => 'required|integer|min:1',
            'items.*.punguzo' => 'nullable|numeric',
            'items.*.faida' => 'nullable|numeric',
        ]);

        $saleIds = [];
        $receiptNo = $this->generateReceiptNo($companyId);
        $itemsData = [];
        
        DB::transaction(function () use ($validated, $companyId, $receiptNo, &$saleIds, &$itemsData) {
            foreach ($validated['items'] as $item) {
                $bidhaa = Bidhaa::where('jina', $item['jina'])
                    ->where('company_id', $companyId)
                    ->first();

                if ($bidhaa) {
                    if ($item['idadi'] > $bidhaa->idadi) {
                        throw new \Exception("Stock haitoshi kwa {$bidhaa->jina}");
                    }
                    
                    $jumla = ($item['bei'] * $item['idadi']) - ($item['punguzo'] ?? 0);

                    $mauzo = Mauzo::create([
                        'company_id' => $companyId,
                        'receipt_no' => $receiptNo,
                        'bidhaa_id'  => $bidhaa->id,
                        'idadi'      => $item['idadi'],
                        'bei'        => $item['bei'],
                        'punguzo'    => $item['punguzo'] ?? 0,
                        'jumla'      => $jumla,
                    ]);

                    $bidhaa->decrement('idadi', $item['idadi']);

                    $saleIds[] = $mauzo->id;
                    $itemsData[] = [
                        'bidhaa' => $bidhaa->jina,
                        'idadi' => $item['idadi'],
                        'bei' => $item['bei'],
                        'punguzo' => $item['punguzo'] ?? 0,
                        'jumla' => $jumla
                    ];
                }
            }
        });

        $response = [
            'status' => 'success',
            'message' => 'Mauzo ya kikapu yamehifadhiwa kikamilifu!',
            'notification' => 'Kikapu kimefanikiwa!',
            'receipt_no' => $receiptNo,
        ];

        return response()->json($response);
    }

    // Handle basket loans
    public function storeKikapuLoan(Request $request)
    {
        $user = $this->getAuthUser();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access',
                'notification' => 'Unauthorized!'
            ], 401);
        }
        
        $companyId = $user->company_id;

        $validated = $request->validate([
            'jina' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.jina' => 'required|string',
            'items.*.bei' => 'required|numeric',
            'items.*.idadi' => 'required|integer|min:1',
            'items.*.punguzo' => 'nullable|numeric',
        ]);

        $mteja = Mteja::firstOrCreate([
            'jina' => $validated['jina'],
            'company_id' => $companyId,
        ]);

        $loanIds = [];

        foreach ($validated['items'] as $item) {
            $bidhaa = Bidhaa::where('jina', $item['jina'])
                ->where('company_id', $companyId)
                ->first();

            if ($bidhaa) {
                if ($item['idadi'] > $bidhaa->idadi) {
                    return response()->json([
                        'status' => 'error',
                        'message' => "Stock haitoshi kwa {$bidhaa->jina}",
                        'notification' => 'Stock haitoshi!'
                    ], 422);
                }
                
                $jumla = ($item['bei'] * $item['idadi']) - ($item['punguzo'] ?? 0);

                $bidhaa->decrement('idadi', $item['idadi']);

                $deni = Madeni::create([
                    'company_id'    => $companyId,
                    'bidhaa_id'     => $bidhaa->id,
                    'idadi'         => $item['idadi'],
                    'bei'           => $item['bei'],
                    'jumla'         => $jumla,
                    'jina_mkopaji'  => $validated['jina'],
                    'simu'          => '',
                    'tarehe_malipo' => now()->addDays(7),
                    'baki'          => $jumla,
                ]);

                $loanIds[] = $deni->id;
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Bidhaa zimekopeshwa kwa mafanikio!',
            'notification' => 'Mikopo imefanikiwa!',
        ]);
    }

    // Get receipt for thermal printing
    public function getReceiptForPrint($receiptNo)
    {
        $user = $this->getAuthUser();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        
        $companyId = $user->company_id;

        $sales = Mauzo::with('bidhaa')
            ->where('company_id', $companyId)
            ->where('receipt_no', $receiptNo)
            ->get();

        if ($sales->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Hakuna taarifa za risiti hii'
            ], 404);
        }

        // Increment reprint count
        Mauzo::where('receipt_no', $receiptNo)
            ->where('company_id', $companyId)
            ->increment('reprint_count');

        $items = $sales->map(function($sale) {
            return [
                'bidhaa' => $sale->bidhaa->jina,
                'idadi' => $sale->idadi,
                'bei' => $sale->bei,
                'punguzo' => $sale->punguzo,
                'jumla' => $sale->jumla
            ];
        });

        $total = $sales->sum('jumla');
        $totalPunguzo = $sales->sum('punguzo');
        $subtotal = $total + $totalPunguzo;

        return response()->json([
            'success' => true,
            'receipt_no' => $receiptNo,
            'items' => $items,
            'subtotal' => $subtotal,
            'punguzo' => $totalPunguzo,
            'total' => $total,
            'date' => $sales->first()->created_at->format('d/m/Y H:i'),
            'reprint_count' => $sales->first()->reprint_count + 1
        ]);
    }

    // Generate thermal receipt HTML
// Generate thermal receipt HTML
public function printThermalReceipt($receiptNo)
{
    $user = $this->getAuthUser();
    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized access'
        ], 401);
    }
    
    $companyId = $user->company_id;

    // Fetch company data - FIXED
    // Since Company model uses 'id' as primary key, find the company by id
    $company = \App\Models\Company::find($companyId);
    
    // If company not found, create a default object
    if (!$company) {
        $company = (object) [
            'company_name' => $user->company_name ?? 'BIASHARA YANGU',
            'location' => $user->location ?? '',
            'region' => $user->region ?? '',
            'phone' => $user->phone ?? '',
            'email' => $user->email ?? '',
            'owner_name' => $user->name ?? '',
        ];
    }

    $sales = Mauzo::with('bidhaa')
        ->where('company_id', $companyId)
        ->where('receipt_no', $receiptNo)
        ->get();

    if ($sales->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'Hakuna taarifa za risiti hii'
        ], 404);
    }

    $date = $sales->first()->created_at->format('d/m/Y H:i');
    $total = $sales->sum('jumla');
    $totalPunguzo = $sales->sum('punguzo');
    $subtotal = $total + $totalPunguzo;

    // Pass company variable to the view
    return view('mauzo.thermal-receipt', compact('receiptNo', 'sales', 'date', 'subtotal', 'totalPunguzo', 'total', 'company'));
}
    // Search receipts
    public function searchReceipts(Request $request)
    {
        $user = $this->getAuthUser();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        
        $companyId = $user->company_id;
        $searchTerm = $request->input('search', '');

        $receipts = Mauzo::where('company_id', $companyId)
            ->whereNotNull('receipt_no')
            ->where('receipt_no', 'like', "%{$searchTerm}%")
            ->select('receipt_no', DB::raw('count(*) as item_count'), DB::raw('sum(jumla) as total'), DB::raw('max(created_at) as last_date'))
            ->groupBy('receipt_no')
            ->orderBy('last_date', 'desc')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'receipts' => $receipts
        ]);
    }
}.....rewrite complete code and keep smart....for both blade and controller for copy and paste