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
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
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
                        placeholder="Tafuta mteja, simu, anuani..." 
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
                </div>
            </div>
        </div>

        <!-- Wateja Table -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-emerald-50">
                            <th class="px-4 py-2 text-left font-medium text-emerald-800">Mteja</th>
                            <th class="px-4 py-2 text-left font-medium text-emerald-800 hidden sm:table-cell">Simu</th>
                            <th class="px-4 py-2 text-left font-medium text-emerald-800 hidden md:table-cell">Email</th>
                            <th class="px-4 py-2 text-left font-medium text-emerald-800">Anuani</th>
                            <th class="px-4 py-2 text-left font-medium text-emerald-800 hidden lg:table-cell">Tarehe</th>
                            <th class="px-4 py-2 text-center font-medium text-emerald-800 print:hidden">Vitendo</th>
                        </tr>
                    </thead>
                    <tbody id="wateja-tbody" class="divide-y divide-gray-100">
                        @forelse($wateja as $item)
                            <tr class="mteja-row hover:bg-gray-50" data-mteja='@json($item)'>
                                <td class="px-4 py-2">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-800 font-bold text-sm mr-2">
                                            {{ substr($item->jina, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900 text-sm">{{ $item->jina }}</div>
                                            <div class="text-xs text-gray-500 sm:hidden">{{ $item->simu }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-2 hidden sm:table-cell">
                                    <div class="text-sm text-gray-700">{{ $item->simu }}</div>
                                    @if($item->barua_pepe)
                                    <div class="text-xs text-gray-500 truncate max-w-[150px]">{{ $item->barua_pepe }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-2 hidden md:table-cell">
                                    <div class="text-sm text-gray-700">{{ $item->barua_pepe ?? '--' }}</div>
                                </td>
                                <td class="px-4 py-2">
                                    <div class="text-xs text-gray-700 truncate max-w-[150px]">{{ $item->anapoishi ?? '--' }}</div>
                                </td>
                                <td class="px-4 py-2 hidden lg:table-cell">
                                    <div class="text-xs text-gray-500">{{ $item->created_at->format('d/m/Y') }}</div>
                                </td>
                                <td class="px-4 py-2 text-center print:hidden">
                                    <div class="flex justify-center space-x-2">
                                        <button class="view-mteja-btn text-blue-600 hover:text-blue-800"
                                                data-id="{{ $item->id }}" title="Angalia Maelezo">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="edit-mteja-btn text-emerald-600 hover:text-emerald-800"
                                                data-id="{{ $item->id }}" title="Badili">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        @if($item->simu)
                                        <button class="sms-mteja-btn text-purple-600 hover:text-purple-800"
                                                data-id="{{ $item->id }}" data-phone="{{ $item->simu }}" data-name="{{ $item->jina }}" title="Tuma SMS">
                                            <i class="fas fa-envelope"></i>
                                        </button>
                                        <a href="tel:{{ $item->simu }}" class="text-green-600 hover:text-green-800" title="Piga Simu">
                                            <i class="fas fa-phone"></i>
                                        </a>
                                        @endif
                                        <button class="delete-mteja-btn text-red-600 hover:text-red-800"
                                                data-id="{{ $item->id }}" data-name="{{ $item->jina }}" title="Futa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
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

            <!-- SMS History - Hidden by default, shows on click -->
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
// Global variable to track history visibility
let historyVisible = false;

// Toggle SMS History visibility
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
        
        // Load history only when first opened
        if (window.watejaManager && document.getElementById('sms-history').innerHTML.includes('Hakuna historia')) {
            window.watejaManager.loadSmsHistory();
        }
    }
}

class SmartWatejaManager {
    constructor() {
        this.currentTab = this.getSavedTab() || 'taarifa';
        this.searchTimeout = null;
        this.init();
    }

    init() {
        this.bindEvents();
        this.showTab(this.currentTab);
        this.setupAjaxForms();
        this.initSmsFeatures();
        this.refreshSmsStats();
    }

    getSavedTab() {
        return sessionStorage.getItem('wateja_tab') || 'taarifa';
    }

    saveTab(tab) {
        sessionStorage.setItem('wateja_tab', tab);
    }

    initSmsFeatures() {
        // SMS message character counter
        const smsMessage = document.getElementById('sms-message');
        if (smsMessage) {
            smsMessage.addEventListener('input', (e) => {
                const length = e.target.value.length;
                document.getElementById('message-chars').textContent = length;
                
                const parts = Math.ceil(length / 153);
                document.getElementById('parts-count').textContent = parts;
            });
        }

        // Single SMS message counter
        const singleMessage = document.getElementById('single-sms-message');
        if (singleMessage) {
            singleMessage.addEventListener('input', (e) => {
                document.getElementById('single-message-chars').textContent = e.target.value.length;
            });
        }

        // SMS form submission
        const smsForm = document.getElementById('sms-form');
        if (smsForm) {
            smsForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.sendSms(smsForm);
            });
        }

        // Single SMS form submission
        const singleSmsForm = document.getElementById('single-sms-form');
        if (singleSmsForm) {
            singleSmsForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.sendSms(singleSmsForm);
                document.getElementById('single-sms-modal').classList.add('hidden');
            });
        }
    }

    async loadSmsHistory() {
        try {
            const response = await fetch('{{ route("sms.logs") }}?limit=20', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
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
            document.getElementById('sms-history').innerHTML = '<p class="text-xs text-red-500 text-center py-4">Hitilafu katika kupakia historia</p>';
        }
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    async refreshSmsStats() {
        try {
            const response = await fetch('{{ route("sms.stats") }}', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Update stats in SMS tab
                document.getElementById('stat-total').textContent = data.total_sent;
                document.getElementById('stat-today').textContent = data.today_sent;
                document.getElementById('stat-month').textContent = data.month_sent;
                
                // Update history count badge
                const historyCount = document.getElementById('history-count');
                if (historyCount && data.total_sent) {
                    historyCount.textContent = data.total_sent;
                }
            }
        } catch (error) {
            console.error('Failed to fetch SMS stats:', error);
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
                
                // Refresh stats and update history count
                await this.refreshSmsStats();
                
                // If history is visible, reload it
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
        
        document.getElementById(`${tabName}-tab-content`).classList.remove('hidden');
        this.currentTab = tabName;
        
        if (tabName === 'sms') {
            this.refreshSmsStats();
        }
    }

    bindEvents() {
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', (e) => {
                const tab = e.target.closest('.tab-button').dataset.tab;
                this.showTab(tab);
                this.saveTab(tab);
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
                this.showSingleSmsModal(phone, name);
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

    filterWateja(searchTerm) {
        const rows = document.querySelectorAll('.mteja-row');
        let found = false;
        
        rows.forEach(row => {
            const mteja = JSON.parse(row.dataset.mteja);
            const searchText = `${mteja.jina} ${mteja.simu} ${mteja.barua_pepe} ${mteja.anapoishi}`.toLowerCase();
            
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
                this.showSingleSmsModal(mteja.simu, mteja.jina);
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
        
        editForm.action = `/wateja/${mteja.id}`;
        document.getElementById('edit-modal').classList.remove('hidden');
    }

    showSingleSmsModal(phone, name) {
        const smsModal = document.getElementById('single-sms-modal');
        if (!smsModal) return;

        document.getElementById('sms-customer-name').textContent = name;
        document.getElementById('sms-customer-phone').textContent = `Namba: ${phone}`;
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

    setupAjaxForms() {
        const mtejaForm = document.getElementById('mteja-form');
        if (mtejaForm) {
            mtejaForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.submitForm(mtejaForm, 'Mteja ameongezwa!');
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
                setTimeout(() => window.location.reload(), 1000);
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

// Global functions
async function refreshSmsStats() {
    if (window.watejaManager) {
        await window.watejaManager.refreshSmsStats();
    }
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
            window.watejaManager.showNotification(data.message, 'success');
        } else {
            window.watejaManager.showNotification(data.message, 'error');
        }
    } catch (error) {
        window.watejaManager.showNotification('Test failed: ' + error.message, 'error');
    }
}

function selectAllCustomers() {
    const phoneNumbers = [];
    document.querySelectorAll('.mteja-row').forEach(row => {
        const mteja = JSON.parse(row.dataset.mteja);
        if (mteja.simu) {
            phoneNumbers.push(mteja.simu);
        }
    });
    
    if (phoneNumbers.length > 0) {
        const recipientsField = document.getElementById('sms-recipients');
        recipientsField.value = phoneNumbers.join('\n');
        window.watejaManager.showNotification(`${phoneNumbers.length} wateja wamechaguliwa`, 'success');
    } else {
        window.watejaManager.showNotification('Hakuna wateja wenye namba za simu', 'warning');
    }
}

function clearSmsForm() {
    document.getElementById('sms-recipients').value = '';
    document.getElementById('sms-message').value = '';
    document.getElementById('message-chars').textContent = '0';
    document.getElementById('parts-count').textContent = '1';
}

function printWateja() {
    const printWindow = window.open('', '_blank');
    const rows = document.querySelectorAll('.mteja-row');
    
    let tableRows = '';
    rows.forEach(row => {
        const mteja = JSON.parse(row.dataset.mteja);
        tableRows += `
            <tr>
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
                th, td { border: 1px solid #ddd; padding: 8px; }
                th { background-color: #f3f4f6; font-weight: bold; }
                .header { text-align: center; margin-bottom: 30px; }
                .header h2 { margin: 0; color: #047857; }
                .header p { margin: 5px 0 0 0; color: #6b7280; }
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

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    window.watejaManager = new SmartWatejaManager();
});
</script>
@endpush