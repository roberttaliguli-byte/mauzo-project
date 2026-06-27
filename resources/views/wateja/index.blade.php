@extends('layouts.app')

@section('title', 'Wateja')

@section('page-title', 'Wateja')
@section('page-subtitle', now()->format('d/m/Y'))

@section('content')
<div class="space-y-4" id="app-container" data-current-page="{{ request()->get('page', 1) }}">
    <!-- Notifications -->
    <div id="notification-container" class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-sm px-4 pointer-events-none">
        @if(session('success'))
        <div class="rounded border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 mb-2 shadow-sm">
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="rounded border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800 mb-2 shadow-sm">
            {{ session('error') }}
        </div>
        @endif
    </div>

    <!-- Stats with Links -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
        <div class="bg-white p-3 rounded-lg border border-emerald-200 shadow-sm">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Jumla ya Wateja</p>
                    <p class="text-xl font-bold text-emerald-700">{{ $totalWateja }}</p>
                </div>
                <i class="fas fa-users text-emerald-500 text-lg"></i>
            </div>
        </div>
        <div class="bg-white p-3 rounded-lg border border-blue-200 shadow-sm">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Wateja wa Mwezi</p>
                    <p class="text-xl font-bold text-blue-700">{{ $newThisMonth }}</p>
                </div>
                <i class="fas fa-calendar-star text-blue-500 text-lg"></i>
            </div>
        </div>
        <div class="bg-white p-3 rounded-lg border border-purple-200 shadow-sm">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Wateja wa Leo</p>
                    <p class="text-xl font-bold text-purple-700">{{ $newToday }}</p>
                </div>
                <i class="fas fa-calendar-day text-purple-500 text-lg"></i>
            </div>
        </div>
        <div class="bg-white p-3 rounded-lg border border-amber-200 shadow-sm">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Wateja kwa Ukurasa</p>
                    <p class="text-xl font-bold text-amber-700">{{ $wateja->count() }}</p>
                </div>
                <i class="fas fa-user-plus text-amber-500 text-lg"></i>
            </div>
        </div>
        <div class="bg-white p-3 rounded-lg border border-emerald-200 shadow-sm">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Wateja Waliosajiliwa</p>
                    <p class="text-xl font-bold text-emerald-700">{{ $totalWateja }}</p>
                </div>
                <i class="fas fa-id-card text-emerald-500 text-lg"></i>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm">
        <div class="flex">
            <button data-tab="taarifa" class="tab-button flex-1 py-3 px-4 text-sm font-medium border-r border-gray-200 bg-emerald-50 text-emerald-700">
                <i class="fas fa-table mr-2"></i> Orodha
            </button>
            <button data-tab="sajili" class="tab-button flex-1 py-3 px-4 text-sm font-medium text-gray-600 hover:bg-gray-50">
                <i class="fas fa-plus mr-2"></i> Sajili
            </button>
            <button data-tab="sms" class="tab-button flex-1 py-3 px-4 text-sm font-medium text-gray-600 hover:bg-gray-50">
                <i class="fas fa-envelope mr-2"></i> Tuma SMS
            </button>
         
        </div>
    </div>

    <!-- TAB 1: Orodha -->
    <div id="taarifa-tab-content" class="tab-content space-y-3">
        <!-- Search Bar -->
        <div class="bg-white p-3 rounded-lg border border-gray-200 shadow-sm">
            <div class="flex flex-col sm:flex-row gap-2 sm:items-center">
                <div class="flex-1 relative">
                    <input 
                        type="text" 
                        id="search-input"
                        placeholder="Tafuta mteja, simu, msimbo, anuani..." 
                        class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500"
                        value="{{ request()->search }}"
                    >
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                <div class="flex gap-2">
                    <button onclick="printWateja()" class="px-3 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 text-sm font-medium">
                        <i class="fas fa-print mr-1"></i> Print
                    </button>
                    <button onclick="exportPDF()" class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm font-medium">
                        <i class="fas fa-file-pdf mr-1"></i> PDF
                    </button>
                    <a href="{{ route('wateja.ripoti') }}" class="px-3 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 text-sm font-medium">
                        <i class="fas fa-chart-pie mr-1"></i> Ripoti
                    </a>
                    <!-- Add this button near the search/export buttons -->
<form method="POST" action="{{ route('wateja.generate-missing-codes') }}">
    @csrf
    <button type="submit" class="px-3 py-2 bg-amber-600 text-white rounded hover:bg-amber-700 text-sm font-medium">
        <i class="fas fa-qrcode mr-1"></i> Tengeneza Misimbo
    </button>
</form>
                </div>
            </div>
        </div>

        <!-- Wateja Table -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-emerald-50">
                            <th class="px-3 py-2 text-left font-medium text-emerald-800 text-xs">Msimbo</th>
                            <th class="px-3 py-2 text-left font-medium text-emerald-800">Mteja</th>
                            <th class="px-3 py-2 text-left font-medium text-emerald-800 hidden sm:table-cell">Simu</th>
                            <th class="px-3 py-2 text-left font-medium text-emerald-800 hidden md:table-cell">Email</th>
                            <th class="px-3 py-2 text-left font-medium text-emerald-800 hidden lg:table-cell">Anuani</th>
                            <th class="px-3 py-2 text-left font-medium text-emerald-800 hidden xl:table-cell">Tarehe</th>
                            <th class="px-3 py-2 text-center font-medium text-emerald-800 print:hidden">Vitendo</th>
                        </tr>
                    </thead>
                    <tbody id="wateja-tbody" class="divide-y divide-gray-100">
                        @forelse($wateja as $item)
                            <tr class="mteja-row hover:bg-gray-50" data-mteja='@json($item)'>
                                <td class="px-3 py-2">
                                    <div class="flex items-center gap-1">
                                        <span class="text-xs font-mono bg-gray-100 px-2 py-0.5 rounded text-emerald-700 font-bold">
                                            {{ $item->customer_code ?? '--' }}
                                        </span>
                                        @if($item->customer_code)
                                        <button class="copy-code-btn text-emerald-600 hover:text-emerald-800" 
                                                data-code="{{ $item->customer_code }}" 
                                                title="Nakili Msimbo"
                                                onclick="event.stopPropagation(); copyCode('{{ $item->customer_code }}')">
                                            <i class="fas fa-copy text-xs"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-3 py-2">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-800 font-bold text-sm mr-2 flex-shrink-0">
                                            {{ substr($item->jina, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900 text-sm">{{ $item->jina }}</div>
                                            <div class="text-xs text-gray-500 sm:hidden">{{ $item->simu }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-2 hidden sm:table-cell">
                                    <div class="text-sm text-gray-700">{{ $item->simu }}</div>
                                    @if($item->barua_pepe)
                                    <div class="text-xs text-gray-500 truncate max-w-[150px]">{{ $item->barua_pepe }}</div>
                                    @endif
                                </td>
                                <td class="px-3 py-2 hidden md:table-cell">
                                    <div class="text-sm text-gray-700">{{ $item->barua_pepe ?? '--' }}</div>
                                </td>
                                <td class="px-3 py-2 hidden lg:table-cell">
                                    <div class="text-xs text-gray-700 truncate max-w-[150px]">{{ $item->anapoishi ?? '--' }}</div>
                                </td>
                                <td class="px-3 py-2 hidden xl:table-cell">
                                    <div class="text-xs text-gray-500">{{ $item->created_at->format('d/m/Y') }}</div>
                                </td>
                                <td class="px-3 py-2 text-center print:hidden">
                                    <div class="flex justify-center space-x-1">
                                        <button class="view-mteja-btn text-blue-600 hover:text-blue-800"
                                                data-id="{{ $item->id }}" title="Angalia Maelezo">
                                            <i class="fas fa-eye text-sm"></i>
                                        </button>
                                        <button class="edit-mteja-btn text-emerald-600 hover:text-emerald-800"
                                                data-id="{{ $item->id }}" title="Badili">
                                            <i class="fas fa-edit text-sm"></i>
                                        </button>
                                        @if($item->simu)
                                        <button class="sms-mteja-btn text-purple-600 hover:text-purple-800"
                                                data-id="{{ $item->id }}" data-phone="{{ $item->simu }}" data-name="{{ $item->jina }}" title="Tuma SMS">
                                            <i class="fas fa-envelope text-sm"></i>
                                        </button>
                                        <a href="tel:{{ $item->simu }}" class="text-green-600 hover:text-green-800" title="Piga Simu">
                                            <i class="fas fa-phone text-sm"></i>
                                        </a>
                                        @endif
                                        <button class="delete-mteja-btn text-red-600 hover:text-red-800"
                                                data-id="{{ $item->id }}" data-name="{{ $item->jina }}" title="Futa">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                    <i class="fas fa-users text-3xl mb-2 text-gray-300"></i>
                                    <p>Hakuna wateja bado</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($wateja->hasPages())
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $wateja->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- TAB 2: Sajili -->
    <div id="sajili-tab-content" class="tab-content hidden">
        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
            <h2 class="text-sm font-semibold text-gray-800 mb-4">Sajili Mteja Mpya</h2>
            <div class="bg-emerald-50 border border-emerald-200 p-3 rounded-lg mb-4 text-xs text-emerald-800">
                <i class="fas fa-info-circle mr-1"></i> Kila mteja atapewa msimbo wa kipekee (Customer Code) ambao atatumia kuweka order kwenye duka lako
            </div>
            <form method="POST" action="{{ route('wateja.store') }}" id="mteja-form" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <!-- Jina -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Jina Kamili *</label>
                        <input type="text" name="jina" 
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                               placeholder="Jina la mteja" required>
                    </div>

                    <!-- Simu -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Namba ya Simu *</label>
                        <input type="text" name="simu" id="customer-phone"
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                               placeholder="Mf. 255712345678" required>
                        <p class="text-xs text-gray-500 mt-1">Tumia muundo: 255XXXXXXXXX</p>
                    </div>

                    <!-- Barua Pepe -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Barua Pepe</label>
                        <input type="email" name="barua_pepe" 
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                               placeholder="barua@pepe.com">
                    </div>

                    <!-- Anapoishi -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Anuani ya Makazi</label>
                        <input type="text" name="anapoishi" 
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                               placeholder="Anuani ya mteja">
                    </div>

                    <!-- Maelezo -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Maelezo ya Ziada</label>
                        <textarea name="maelezo" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                                  placeholder="Maelezo ya ziada kuhusu mteja..."></textarea>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex gap-2 pt-4 border-t border-gray-200">
                    <button type="submit" 
                            class="flex-1 bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700 text-sm font-medium">
                        <i class="fas fa-save mr-1"></i> Hifadhi
                    </button>
                    <button type="reset" 
                            class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 text-sm">
                        <i class="fas fa-redo mr-1"></i> Safisha
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- TAB 3: Tuma SMS -->
    <div id="sms-tab-content" class="tab-content hidden">
        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
            <h2 class="text-sm font-semibold text-gray-800 mb-4">Tuma SMS kwa Wateja</h2>
            
            <!-- SMS Stats Mini -->
            <div class="mb-4 p-3 bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg border border-purple-200">
                <div class="grid grid-cols-3 gap-3 text-center">
                    <div>
                        <p class="text-xs text-gray-600">Jumla ya SMS</p>
                        <p id="stat-total" class="text-lg font-bold text-purple-700">0</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600">Leo</p>
                        <p id="stat-today" class="text-lg font-bold text-pink-700">0</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600">Mwezi</p>
                        <p id="stat-month" class="text-lg font-bold text-indigo-700">0</p>
                    </div>
                </div>
            </div>

            <form id="sms-form" method="POST" action="{{ route('sms.send') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Namba za Simu *</label>
                    <textarea name="recipients" id="sms-recipients" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-purple-500"
                              placeholder="255712345678&#10;255758483019&#10;Tenganisha kwa kutumia mstari mpya au koma"></textarea>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-info-circle"></i> Tenganisha namba kwa kutumia comma (,) au mstari mpya. Muundo: 255XXXXXXXXX
                    </p>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Ujumbe *</label>
                    <textarea name="message" id="sms-message" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-purple-500"
                              placeholder="Andika ujumbe wako hapa..." required></textarea>
                    <div class="flex justify-between mt-1">
                        <p class="text-xs text-gray-500">
                            <span id="message-chars">0</span> herufi | 
                            <span id="message-parts-info">Sehemu: <span id="parts-count">1</span></span>
                        </p>
                        <button type="button" onclick="clearSmsForm()" class="text-xs text-gray-500 hover:text-gray-700">
                            <i class="fas fa-eraser"></i> Safisha
                        </button>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="submit" id="send-sms-btn"
                            class="flex-1 bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 text-sm font-medium">
                        <i class="fas fa-paper-plane mr-1"></i> Tuma SMS
                    </button>
                    <button type="button" 
                            onclick="selectAllCustomers()"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 text-sm">
                        <i class="fas fa-users mr-1"></i> Chagua Wote
                    </button>
                    <button type="button" 
                            onclick="testSmsConnection()"
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                        <i class="fas fa-vial mr-1"></i> Test
                    </button>
                </div>
            </form>

            <!-- SMS History -->
            <div class="mt-6 pt-4 border-t border-gray-200">
                <button onclick="toggleSmsHistory()" 
                        class="w-full flex items-center justify-between px-4 py-2 bg-gray-50 hover:bg-gray-100 rounded-lg border border-gray-200 transition-colors">
                    <span class="text-sm font-semibold text-gray-700">
                        <i class="fas fa-history mr-2"></i> Historia ya SMS
                    </span>
                    <span class="text-gray-500">
                        <i id="history-icon" class="fas fa-chevron-down"></i>
                        <span id="history-count" class="ml-2 text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full">0</span>
                    </span>
                </button>
                <div id="sms-history-container" class="hidden mt-3">
                    <div id="sms-history" class="space-y-2 max-h-96 overflow-y-auto">
                        <p class="text-xs text-gray-500 text-center py-4">Hakuna historia ya SMS</p>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>

<!-- View Details Modal -->
<div id="view-modal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-lg shadow-lg w-full max-w-md mx-auto z-50 max-h-[90vh] overflow-y-auto">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-800">Taarifa za Mteja</h3>
        </div>
        <div class="p-4 space-y-3">
            <div class="flex items-center space-x-3 mb-4">
                <div class="h-12 w-12 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-800 font-bold text-lg">
                    <span id="view-initial">M</span>
                </div>
                <div>
                    <h4 id="view-jina" class="font-medium text-gray-900 text-sm"></h4>
                    <p id="view-simu" class="text-xs text-gray-600"></p>
                </div>
            </div>

            <div class="space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-500">Msimbo wa Mteja:</span>
                    <span id="view-customer-code" class="text-xs font-mono font-bold text-emerald-700"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-xs text-gray-500">Simu:</span>
                    <span id="view-phone" class="text-xs text-gray-900 font-medium"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-xs text-gray-500">Email:</span>
                    <span id="view-email" class="text-xs text-gray-900"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-xs text-gray-500">Anuani:</span>
                    <span id="view-address" class="text-xs text-gray-900"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-xs text-gray-500">Tarehe ya Usajili:</span>
                    <span id="view-date" class="text-xs text-gray-900"></span>
                </div>
                <div>
                    <span class="text-xs text-gray-500 mb-1 block">Maelezo:</span>
                    <p id="view-notes" class="text-xs text-gray-900 bg-gray-50 p-2 rounded"></p>
                </div>
            </div>
        </div>
        <div class="flex gap-2 p-4 border-t border-gray-200">
            <button id="close-view-modal"
                    class="flex-1 px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 text-sm">
                Funga
            </button>
            <a id="call-button" href="#" 
               class="flex-1 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm font-medium text-center hidden">
                <i class="fas fa-phone mr-1"></i> Piga Simu
            </a>
            <button id="sms-from-view" 
                    class="flex-1 px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 text-sm font-medium text-center hidden">
                <i class="fas fa-envelope mr-1"></i> Tuma SMS
            </button>
        </div>
    </div>
</div>

<!-- SMS Modal (For Single Customer) -->
<div id="single-sms-modal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-lg shadow-lg w-full max-w-md mx-auto z-50">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-800">Tuma SMS kwa Mteja</h3>
        </div>
        <form id="single-sms-form" method="POST" action="{{ route('sms.send') }}" class="p-4">
            @csrf
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Mteja</label>
                    <p id="sms-customer-name" class="text-sm font-medium text-gray-900"></p>
                    <p id="sms-customer-phone" class="text-xs text-gray-500"></p>
                    <p id="sms-customer-code" class="text-xs font-mono text-emerald-600"></p>
                    <input type="hidden" name="recipients" id="sms-recipient-single">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Ujumbe *</label>
                    <textarea name="message" id="single-sms-message" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-purple-500"
                              placeholder="Andika ujumbe wako hapa..." required></textarea>
                    <div class="flex justify-between mt-1">
                        <p class="text-xs text-gray-500">
                            <span id="single-message-chars">0</span> herufi
                        </p>
                    </div>
                </div>
            </div>
            <div class="flex gap-2 pt-4 border-t border-gray-200 mt-4">
                <button type="button" id="close-sms-modal"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 text-sm">
                    Ghairi
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 text-sm font-medium">
                    <i class="fas fa-paper-plane mr-1"></i> Tuma SMS
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="edit-modal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-lg shadow-lg w-full max-w-md mx-auto z-50 max-h-[90vh] overflow-y-auto">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-800">Rekebisha Mteja</h3>
        </div>
        <form id="edit-form" method="POST" class="p-4">
            @csrf
            @method('PUT')
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Jina Kamili *</label>
                    <input type="text" name="jina" id="edit-jina"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                           required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Namba ya Simu *</label>
                    <input type="text" name="simu" id="edit-simu"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                           required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Barua Pepe</label>
                    <input type="email" name="barua_pepe" id="edit-email"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Anuani ya Makazi</label>
                    <input type="text" name="anapoishi" id="edit-address"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Maelezo ya Ziada</label>
                    <textarea name="maelezo" id="edit-notes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"></textarea>
                </div>
                <div class="bg-gray-50 p-2 rounded">
                    <p class="text-xs text-gray-500">Msimbo wa Mteja: <span id="edit-customer-code" class="font-mono font-bold text-emerald-700"></span></p>
                </div>
            </div>
            <div class="flex gap-2 pt-4 border-t border-gray-200 mt-4">
                <button type="button" id="close-edit-modal"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 text-sm">
                    Ghairi
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 text-sm font-medium">
                    Hifadhi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Modal -->
<div id="delete-modal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-lg shadow-lg w-full max-w-sm mx-auto z-50">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-800">Thibitisha Kufuta</h3>
        </div>
        <div class="p-4">
            <div class="text-center mb-4">
                <i class="fas fa-exclamation-triangle text-amber-500 text-2xl mb-2"></i>
                <p class="text-gray-700 text-sm mb-1">Una uhakika unataka kufuta?</p>
                <p class="text-gray-900 font-medium" id="delete-mteja-name"></p>
                <p class="text-gray-500 text-xs mt-2">Hatua hii haiwezi kutenduliwa</p>
            </div>
            <div class="flex gap-2">
                <button id="cancel-delete"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 text-sm">
                    Ghairi
                </button>
                <form id="delete-form" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="w-full px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm font-medium">
                        Futa
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ============================================
// WATEJA MANAGER - Full Controller
// ============================================

class SmartWatejaManager {
    constructor() {
        this.currentTab = this.getSavedTab() || 'taarifa';
        this.searchTimeout = null;
        this.customerSearchTimeout = null;
        this.selectedCustomer = null;
        this.init();
    }

    init() {
        this.bindEvents();
        this.showTab(this.currentTab);
        this.setupAjaxForms();
        this.initSmsFeatures();
        this.refreshSmsStats();
        this.initCustomerSearch();
    }

    getSavedTab() {
        return sessionStorage.getItem('wateja_tab') || 'taarifa';
    }

    saveTab(tab) {
        sessionStorage.setItem('wateja_tab', tab);
    }

    // ===== SMS FEATURES =====
    initSmsFeatures() {
        const smsMessage = document.getElementById('sms-message');
        if (smsMessage) {
            smsMessage.addEventListener('input', (e) => {
                const length = e.target.value.length;
                document.getElementById('message-chars').textContent = length;
                const parts = Math.ceil(length / 153);
                document.getElementById('parts-count').textContent = parts;
            });
        }

        const singleMessage = document.getElementById('single-sms-message');
        if (singleMessage) {
            singleMessage.addEventListener('input', (e) => {
                document.getElementById('single-message-chars').textContent = e.target.value.length;
            });
        }

        const smsForm = document.getElementById('sms-form');
        if (smsForm) {
            smsForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.sendSms(smsForm);
            });
        }

        const singleSmsForm = document.getElementById('single-sms-form');
        if (singleSmsForm) {
            singleSmsForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.sendSms(singleSmsForm);
                document.getElementById('single-sms-modal').classList.add('hidden');
            });
        }
    }

    async sendSms(form) {
        const formData = new FormData(form);
        const submitButton = form.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        
        try {
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Inatuma...';
            
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();

            if (response.ok && data.success) {
                this.showNotification(data.message || 'SMS imetumwa kikamilifu!', 'success');
                if (form.id === 'sms-form') {
                    document.getElementById('sms-recipients').value = '';
                    document.getElementById('sms-message').value = '';
                    document.getElementById('message-chars').textContent = '0';
                    document.getElementById('parts-count').textContent = '1';
                } else {
                    document.getElementById('single-sms-message').value = '';
                    document.getElementById('single-message-chars').textContent = '0';
                }
                await this.refreshSmsStats();
                if (historyVisible) {
                    await this.loadSmsHistory();
                }
            } else {
                const error = data.message || 'Hitilafu imetokea wakati wa kutuma SMS';
                this.showNotification(error, 'error');
            }
        } catch (error) {
            this.showNotification('Hitilafu ya mtandao', 'error');
        } finally {
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
        }
    }

    async refreshSmsStats() {
        try {
            const response = await fetch('{{ route("sms.stats") }}', {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            const data = await response.json();
            if (data.success) {
                document.getElementById('stat-total').textContent = data.total_sent;
                document.getElementById('stat-today').textContent = data.today_sent;
                document.getElementById('stat-month').textContent = data.month_sent;
                const historyCount = document.getElementById('history-count');
                if (historyCount && data.total_sent) historyCount.textContent = data.total_sent;
            }
        } catch (error) {
            console.error('Failed to fetch SMS stats:', error);
        }
    }

    async loadSmsHistory() {
        try {
            const response = await fetch('{{ route("sms.logs") }}?limit=20', {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            const data = await response.json();
            const historyDiv = document.getElementById('sms-history');
            const historyCount = document.getElementById('history-count');
            
            if (data.logs && data.logs.data && data.logs.data.length > 0) {
                historyCount.textContent = data.logs.total || data.logs.data.length;
                historyDiv.innerHTML = data.logs.data.map(log => `
                    <div class="bg-gray-50 rounded p-3 text-xs border border-gray-200 hover:shadow-sm transition-shadow">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex justify-between mb-2">
                                    <span class="font-medium text-gray-900">${log.recipient}</span>
                                    <span class="px-2 py-0.5 text-xs rounded-full 
                                        ${log.status === 'PENDING_ENROUTE' || log.status === 'DELIVERED' ? 'bg-green-100 text-green-800' : 
                                          log.status === 'REJECTED' || log.status.includes('FAILED') ? 'bg-red-100 text-red-800' : 
                                          'bg-blue-100 text-blue-800'}">
                                        ${log.status}
                                    </span>
                                </div>
                                <p class="text-gray-700 mt-1 leading-relaxed">${this.escapeHtml(log.message)}</p>
                                <div class="flex justify-between mt-2 text-gray-400">
                                    <span><i class="fas fa-envelope mr-1"></i> Sehemu: ${log.sms_count}</span>
                                    <span><i class="far fa-clock mr-1"></i> ${new Date(log.sent_at).toLocaleString()}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                `).join('');
            } else {
                historyCount.textContent = '0';
                historyDiv.innerHTML = '<p class="text-xs text-gray-500 text-center py-4"><i class="fas fa-inbox mr-2"></i> Hakuna historia ya SMS</p>';
            }
        } catch (error) {
            console.error('Failed to load SMS history', error);
        }
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // ===== CUSTOMER SEARCH (Dukani Tab) =====
    initCustomerSearch() {
        const searchInput = document.getElementById('customer-search-input');
        if (!searchInput) return;

        searchInput.addEventListener('input', (e) => {
            clearTimeout(this.customerSearchTimeout);
            const query = e.target.value.trim();
            const resultsContainer = document.getElementById('customer-search-results');
            const spinner = document.getElementById('customer-search-spinner');

            if (query.length < 2) {
                resultsContainer.classList.add('hidden');
                return;
            }

            spinner.classList.remove('hidden');
            this.customerSearchTimeout = setTimeout(() => {
                this.searchCustomers(query);
            }, 300);
        });
    }

    async searchCustomers(query) {
        const resultsContainer = document.getElementById('customer-search-results');
        const spinner = document.getElementById('customer-search-spinner');

        try {
            const response = await fetch(`/wateja/search?query=${encodeURIComponent(query)}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            const data = await response.json();

            spinner.classList.add('hidden');

            if (data.success && data.wateja && data.wateja.length > 0) {
                resultsContainer.innerHTML = data.wateja.map(customer => `
                    <div class="customer-search-item px-4 py-3 border-b border-gray-100 hover:bg-emerald-50 cursor-pointer transition"
                         onclick="window.watejaManager.selectCustomer(${customer.id})"
                         data-id="${customer.id}">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-medium text-gray-900 text-sm">${customer.jina}</div>
                                <div class="text-xs text-gray-500">
                                    ${customer.customer_code ? `<span class="font-mono text-emerald-600">${customer.customer_code}</span> • ` : ''}
                                    ${customer.simu || ''}
                                </div>
                            </div>
                            <div class="text-xs text-gray-400">
                                ${customer.anapoishi || ''}
                            </div>
                        </div>
                    </div>
                `).join('');
                resultsContainer.classList.remove('hidden');
            } else {
                resultsContainer.innerHTML = `
                    <div class="px-4 py-3 text-center text-gray-500 text-sm">
                        <i class="fas fa-search text-gray-300 mb-1"></i>
                        <p>Hakuna mteja anayelingana na "${query}"</p>
                    </div>
                `;
                resultsContainer.classList.remove('hidden');
            }
        } catch (error) {
            spinner.classList.add('hidden');
            resultsContainer.innerHTML = `
                <div class="px-4 py-3 text-center text-red-500 text-sm">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>Hitilafu ya kutafuta</p>
                </div>
            `;
            resultsContainer.classList.remove('hidden');
        }
    }

    async selectCustomer(customerId) {
        const resultsContainer = document.getElementById('customer-search-results');
        resultsContainer.classList.add('hidden');
        document.getElementById('customer-search-input').value = '';

        try {
            const response = await fetch(`/wateja/${customerId}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            const data = await response.json();

            if (data.success) {
                this.selectedCustomer = data.data;
                this.displayCustomerDetails(data.data);
                await this.loadCustomerOrders(data.data.id);
            }
        } catch (error) {
            this.showNotification('Hitilafu ya kupata maelezo', 'error');
        }
    }

    displayCustomerDetails(customer) {
        document.getElementById('no-customer-selected').classList.add('hidden');
        document.getElementById('customer-details-container').classList.remove('hidden');

        document.getElementById('customer-detail-initial').textContent = customer.jina ? customer.jina.charAt(0).toUpperCase() : 'M';
        document.getElementById('customer-detail-name').textContent = customer.jina || '-';
        document.getElementById('customer-detail-code').textContent = customer.customer_code || '--';
        document.getElementById('customer-detail-phone').textContent = customer.simu || '-';
        document.getElementById('customer-detail-email').textContent = customer.barua_pepe || '-';
        document.getElementById('customer-detail-address').textContent = customer.anapoishi || '-';
        document.getElementById('customer-detail-date').textContent = customer.created_at ? new Date(customer.created_at).toLocaleDateString() : '-';

        const callLink = document.getElementById('customer-detail-call');
        if (customer.simu) {
            callLink.href = `tel:${customer.simu}`;
            callLink.classList.remove('hidden');
        } else {
            callLink.classList.add('hidden');
        }

        const smsButton = document.getElementById('customer-detail-sms');
        if (customer.simu) {
            smsButton.onclick = () => this.showSingleSmsModal(customer.simu, customer.jina, customer.customer_code);
            smsButton.classList.remove('hidden');
        } else {
            smsButton.classList.add('hidden');
        }

        // Store for orders
        this.selectedCustomer = customer;
    }

    async loadCustomerOrders(customerId) {
        try {
            const response = await fetch(`/orders/customer/${customerId}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            const data = await response.json();

            const container = document.getElementById('customer-orders-container');
            const list = document.getElementById('customer-orders-list');
            const count = document.getElementById('customer-orders-count');

            if (data.success && data.orders && data.orders.length > 0) {
                container.classList.remove('hidden');
                count.textContent = `${data.orders.length} oda`;

                list.innerHTML = data.orders.map(order => `
                    <div class="px-4 py-3 hover:bg-gray-50 flex justify-between items-center">
                        <div>
                            <div class="text-sm font-medium text-gray-800">${order.order_number}</div>
                            <div class="text-xs text-gray-500">${new Date(order.created_at).toLocaleString()}</div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-medium text-emerald-600">${this.formatCurrency(order.total)}</span>
                            <span class="text-xs px-2 py-0.5 rounded-full 
                                ${order.status === 'paid' ? 'bg-green-100 text-green-800' : 
                                  order.status === 'confirmed' ? 'bg-blue-100 text-blue-800' :
                                  order.status === 'cancelled' ? 'bg-red-100 text-red-800' :
                                  'bg-yellow-100 text-yellow-800'}">
                                ${order.status}
                            </span>
                        </div>
                    </div>
                `).join('');
            } else {
                container.classList.remove('hidden');
                count.textContent = '0 oda';
                list.innerHTML = `<div class="px-4 py-3 text-center text-gray-500 text-sm">Hakuna oda za mteja huyu</div>`;
            }
        } catch (error) {
            console.error('Error loading orders:', error);
        }
    }

    formatCurrency(amount) {
        return amount.toLocaleString('en-TZ', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) + ' TZS';
    }

    openCustomerOrders() {
        const container = document.getElementById('customer-orders-container');
        if (container.classList.contains('hidden')) {
            container.classList.remove('hidden');
        } else {
            container.classList.toggle('hidden');
        }
    }

    // ===== TAB MANAGEMENT =====
    showTab(tabName) {
        document.querySelectorAll('.tab-button').forEach(button => {
            if (button.dataset.tab === tabName) {
                button.classList.add('bg-emerald-50', 'text-emerald-700');
                button.classList.remove('text-gray-600', 'hover:bg-gray-50');
            } else {
                button.classList.remove('bg-emerald-50', 'text-emerald-700');
                button.classList.add('text-gray-600', 'hover:bg-gray-50');
            }
        });

        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        
        const tabContent = document.getElementById(`${tabName}-tab-content`);
        if (tabContent) tabContent.classList.remove('hidden');
        this.currentTab = tabName;
        this.saveTab(tabName);
        
        if (tabName === 'sms') this.refreshSmsStats();
        if (tabName === 'dukani') {
            document.getElementById('no-customer-selected').classList.remove('hidden');
            document.getElementById('customer-details-container').classList.add('hidden');
            document.getElementById('customer-search-input').value = '';
            document.getElementById('customer-search-results').classList.add('hidden');
        }
    }

    // ===== EVENT BINDING =====
    bindEvents() {
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', (e) => {
                const tab = e.target.closest('.tab-button').dataset.tab;
                this.showTab(tab);
            });
        });

        const searchInput = document.getElementById('search-input');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                clearTimeout(this.searchTimeout);
                this.searchTimeout = setTimeout(() => {
                    this.filterWateja(e.target.value.toLowerCase().trim());
                }, 300);
            });
        }

        this.bindMtejaActions();
        this.bindModalEvents();
    }

    // ===== MTEGA ACTIONS =====
    bindMtejaActions() {
        document.querySelectorAll('.view-mteja-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                const row = e.target.closest('.mteja-row');
                const mteja = JSON.parse(row.dataset.mteja);
                this.viewMteja(mteja);
            });
        });

        document.querySelectorAll('.edit-mteja-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                const row = e.target.closest('.mteja-row');
                const mteja = JSON.parse(row.dataset.mteja);
                this.editMteja(mteja);
            });
        });

        document.querySelectorAll('.sms-mteja-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                const phone = button.dataset.phone;
                const name = button.dataset.name;
                // Get customer code from the row
                const row = button.closest('.mteja-row');
                const mteja = row ? JSON.parse(row.dataset.mteja) : null;
                const code = mteja ? mteja.customer_code : null;
                this.showSingleSmsModal(phone, name, code);
            });
        });

        document.querySelectorAll('.delete-mteja-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                const mtejaId = button.dataset.id;
                const mtejaName = button.dataset.name;
                this.deleteMteja(mtejaId, mtejaName);
            });
        });
    }

    // ===== MODALS =====
    bindModalEvents() {
        const viewModal = document.getElementById('view-modal');
        const closeViewBtn = document.getElementById('close-view-modal');
        if (closeViewBtn) closeViewBtn.addEventListener('click', () => viewModal.classList.add('hidden'));
        
        const editModal = document.getElementById('edit-modal');
        const closeEditBtn = document.getElementById('close-edit-modal');
        if (closeEditBtn) closeEditBtn.addEventListener('click', () => editModal.classList.add('hidden'));
        
        const smsModal = document.getElementById('single-sms-modal');
        const closeSmsBtn = document.getElementById('close-sms-modal');
        if (closeSmsBtn) closeSmsBtn.addEventListener('click', () => smsModal.classList.add('hidden'));
        
        const deleteModal = document.getElementById('delete-modal');
        const cancelDeleteBtn = document.getElementById('cancel-delete');
        if (cancelDeleteBtn) cancelDeleteBtn.addEventListener('click', () => deleteModal.classList.add('hidden'));
        
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                if (viewModal) viewModal.classList.add('hidden');
                if (editModal) editModal.classList.add('hidden');
                if (smsModal) smsModal.classList.add('hidden');
                if (deleteModal) deleteModal.classList.add('hidden');
            }
        });
    }

    viewMteja(mteja) {
        const viewModal = document.getElementById('view-modal');
        if (!viewModal) return;

        document.getElementById('view-initial').textContent = mteja.jina ? mteja.jina.charAt(0).toUpperCase() : 'M';
        document.getElementById('view-jina').textContent = mteja.jina || '--';
        document.getElementById('view-simu').textContent = mteja.simu || '--';
        document.getElementById('view-phone').textContent = mteja.simu || '--';
        document.getElementById('view-email').textContent = mteja.barua_pepe || '--';
        document.getElementById('view-address').textContent = mteja.anapoishi || '--';
        document.getElementById('view-date').textContent = mteja.created_at ? new Date(mteja.created_at).toLocaleDateString() : '--';
        document.getElementById('view-notes').textContent = mteja.maelezo || 'Hakuna maelezo';
        document.getElementById('view-customer-code').textContent = mteja.customer_code || 'Hakuna msimbo';

        const callButton = document.getElementById('call-button');
        if (mteja.simu) {
            callButton.href = `tel:${mteja.simu}`;
            callButton.classList.remove('hidden');
        } else {
            callButton.classList.add('hidden');
        }

        const smsButton = document.getElementById('sms-from-view');
        if (mteja.simu) {
            smsButton.onclick = () => {
                viewModal.classList.add('hidden');
                this.showSingleSmsModal(mteja.simu, mteja.jina, mteja.customer_code);
            };
            smsButton.classList.remove('hidden');
        } else {
            smsButton.classList.add('hidden');
        }

        viewModal.classList.remove('hidden');
    }

    editMteja(mteja) {
        const editForm = document.getElementById('edit-form');
        if (!editForm) return;

        document.getElementById('edit-jina').value = mteja.jina || '';
        document.getElementById('edit-simu').value = mteja.simu || '';
        document.getElementById('edit-email').value = mteja.barua_pepe || '';
        document.getElementById('edit-address').value = mteja.anapoishi || '';
        document.getElementById('edit-notes').value = mteja.maelezo || '';
        document.getElementById('edit-customer-code').textContent = mteja.customer_code || 'Hakuna msimbo';
        
        editForm.action = `/wateja/${mteja.id}`;
        document.getElementById('edit-modal').classList.remove('hidden');
    }

    showSingleSmsModal(phone, name, code) {
        const smsModal = document.getElementById('single-sms-modal');
        if (!smsModal) return;

        document.getElementById('sms-customer-name').textContent = name;
        document.getElementById('sms-customer-phone').textContent = `Namba: ${phone}`;
        document.getElementById('sms-customer-code').textContent = code ? `Msimbo: ${code}` : '';
        document.getElementById('sms-recipient-single').value = phone;
        document.getElementById('single-sms-message').value = '';
        document.getElementById('single-message-chars').textContent = '0';
        
        smsModal.classList.remove('hidden');
    }

    deleteMteja(mtejaId, mtejaName) {
        const deleteForm = document.getElementById('delete-form');
        const deleteModal = document.getElementById('delete-modal');
        const deleteMtejaName = document.getElementById('delete-mteja-name');
        
        deleteMtejaName.textContent = mtejaName;
        deleteForm.action = `/wateja/${mtejaId}`;
        deleteModal.classList.remove('hidden');
    }

    // ===== FILTER =====
    filterWateja(searchTerm) {
        const rows = document.querySelectorAll('.mteja-row');
        let found = false;
        
        rows.forEach(row => {
            const mteja = JSON.parse(row.dataset.mteja);
            const searchText = `${mteja.jina} ${mteja.simu} ${mteja.barua_pepe} ${mteja.anapoishi} ${mteja.customer_code || ''}`.toLowerCase();
            
            if (searchText.includes(searchTerm) || !searchTerm) {
                row.classList.remove('hidden');
                found = true;
            } else {
                row.classList.add('hidden');
            }
        });

        if (!found && searchTerm) {
            this.showNotification('Hakuna wateja wanaolingana', 'info');
        }
    }

    // ===== AJAX FORMS =====
    setupAjaxForms() {
        const mtejaForm = document.getElementById('mteja-form');
        if (mtejaForm) {
            mtejaForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.submitForm(mtejaForm, 'Mteja ameongezwa! Msimbo wake umetengenezwa.');
                mtejaForm.reset();
            });
        }

        const editForm = document.getElementById('edit-form');
        if (editForm) {
            editForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.submitForm(editForm, 'Mteja imerekebishwa!');
                document.getElementById('edit-modal').classList.add('hidden');
            });
        }

        const deleteForm = document.getElementById('delete-form');
        if (deleteForm) {
            deleteForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.submitForm(deleteForm, 'Mteja amefutwa!');
                document.getElementById('delete-modal').classList.add('hidden');
            });
        }
    }

    async submitForm(form, successMessage) {
        const formData = new FormData(form);
        const submitButton = form.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        
        try {
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Inatumwa...';
            
            const response = await fetch(form.action, {
                method: form.method,
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();

            if (response.ok && data.success) {
                this.showNotification(data.message || successMessage, 'success');
                setTimeout(() => window.location.reload(), 1500);
            } else {
                const error = data.errors ? Object.values(data.errors)[0][0] : data.message;
                this.showNotification(error || 'Hitilafu imetokea', 'error');
            }
        } catch (error) {
            this.showNotification('Hitilafu ya mtandao', 'error');
        } finally {
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
        }
    }

    // ===== NOTIFICATIONS =====
    showNotification(message, type = 'info') {
        const container = document.getElementById('notification-container');
        if (!container) return;
        
        const colors = {
            success: 'bg-emerald-50 border-emerald-200 text-emerald-800',
            error: 'bg-red-50 border-red-200 text-red-800',
            warning: 'bg-amber-50 border-amber-200 text-amber-800',
            info: 'bg-blue-50 border-blue-200 text-blue-800'
        };

        const notification = document.createElement('div');
        notification.className = `rounded border px-4 py-3 text-sm font-medium mb-2 ${colors[type]} shadow-sm`;
        notification.textContent = message;

        container.appendChild(notification);

        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
}

// ============================================
// GLOBAL FUNCTIONS
// ============================================

let historyVisible = false;
let watejaManager = null;

function toggleSmsHistory() {
    const container = document.getElementById('sms-history-container');
    const icon = document.getElementById('history-icon');
    
    if (historyVisible) {
        container.classList.add('hidden');
        icon.classList.remove('fa-chevron-up');
        icon.classList.add('fa-chevron-down');
        historyVisible = false;
    } else {
        container.classList.remove('hidden');
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-up');
        historyVisible = true;
        if (watejaManager) watejaManager.loadSmsHistory();
    }
}

function copyCode(code) {
    if (!code || code === '--') {
        watejaManager.showNotification('Hakuna msimbo wa kunakili', 'warning');
        return;
    }
    
    if (navigator.clipboard) {
        navigator.clipboard.writeText(code).then(() => {
            watejaManager.showNotification('Msimbo umenakiliwa!', 'success');
        }).catch(() => fallbackCopyCode(code));
    } else {
        fallbackCopyCode(code);
    }
}

function fallbackCopyCode(text) {
    const textarea = document.createElement('textarea');
    textarea.value = text;
    textarea.style.position = 'fixed';
    textarea.style.opacity = '0';
    document.body.appendChild(textarea);
    textarea.select();
    try {
        document.execCommand('copy');
        watejaManager.showNotification('Msimbo umenakiliwa!', 'success');
    } catch (err) {
        watejaManager.showNotification('Imeshindwa kunakili', 'error');
    }
    document.body.removeChild(textarea);
}

async function refreshSmsStats() {
    if (watejaManager) await watejaManager.refreshSmsStats();
}

async function testSmsConnection() {
    try {
        const response = await fetch('{{ route("sms.test") }}', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        const data = await response.json();
        if (data.success) {
            watejaManager.showNotification(data.message, 'success');
        } else {
            watejaManager.showNotification(data.message, 'error');
        }
    } catch (error) {
        watejaManager.showNotification('Test failed: ' + error.message, 'error');
    }
}

function selectAllCustomers() {
    const phoneNumbers = [];
    document.querySelectorAll('.mteja-row').forEach(row => {
        const mteja = JSON.parse(row.dataset.mteja);
        if (mteja.simu) phoneNumbers.push(mteja.simu);
    });
    
    if (phoneNumbers.length > 0) {
        document.getElementById('sms-recipients').value = phoneNumbers.join('\n');
        watejaManager.showNotification(`${phoneNumbers.length} wateja wamechaguliwa`, 'success');
    } else {
        watejaManager.showNotification('Hakuna wateja wenye namba za simu', 'warning');
    }
}

function clearSmsForm() {
    document.getElementById('sms-recipients').value = '';
    document.getElementById('sms-message').value = '';
    document.getElementById('message-chars').textContent = '0';
    document.getElementById('parts-count').textContent = '1';
}

function openCustomerOrders() {
    if (watejaManager) watejaManager.openCustomerOrders();
}

function printWateja() {
    const printWindow = window.open('', '_blank');
    const rows = document.querySelectorAll('.mteja-row');
    
    let tableRows = '';
    rows.forEach(row => {
        const mteja = JSON.parse(row.dataset.mteja);
        tableRows += `
            <tr>
                <td style="border: 1px solid #ddd; padding: 8px;">${mteja.customer_code || '--'}</td>
                <td style="border: 1px solid #ddd; padding: 8px;">${mteja.jina}</td>
                <td style="border: 1px solid #ddd; padding: 8px;">${mteja.simu}</td>
                <td style="border: 1px solid #ddd; padding: 8px;">${mteja.barua_pepe || '--'}</td>
                <td style="border: 1px solid #ddd; padding: 8px;">${mteja.anapoishi || '--'}</td>
                <td style="border: 1px solid #ddd; padding: 8px;">${mteja.created_at ? new Date(mteja.created_at).toLocaleDateString() : ''}</td>
            </tr>
        `;
    });
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Orodha ya Wateja - ${new Date().toLocaleDateString()}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; font-size: 12px; }
                th { background-color: #f3f4f6; font-weight: bold; }
                .header { text-align: center; margin-bottom: 30px; }
                .header h2 { margin: 0; color: #047857; }
                .header p { margin: 5px 0 0 0; color: #6b7280; }
                .code { font-family: monospace; color: #047857; font-weight: bold; }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>Orodha ya Wateja</h2>
                <p>${new Date().toLocaleDateString()}</p>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Msimbo</th>
                        <th>Jina</th>
                        <th>Simu</th>
                        <th>Email</th>
                        <th>Anuani</th>
                        <th>Tarehe</th>
                    </tr>
                </thead>
                <tbody>${tableRows}</tbody>
            </table>
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

function exportPDF() {
    const search = new URLSearchParams(window.location.search);
    search.set('export', 'pdf');
    window.open(`${window.location.pathname}?${search.toString()}`, '_blank');
}


// ============================================
// INITIALIZE
// ============================================
document.addEventListener('DOMContentLoaded', () => {
    watejaManager = new SmartWatejaManager();
    window.watejaManager = watejaManager;
});
</script>
@endpush