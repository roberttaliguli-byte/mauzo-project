@extends('layouts.app')

@section('title', 'Matumizi')

@section('page-title', 'Matumizi')
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
        <div class="bg-white p-3 rounded-lg border border-red-200 shadow-sm">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Jumla ya Matumizi</p>
                    <p class="text-xl font-bold text-red-700">{{ number_format($totalExpenses, 0) }}</p>
                </div>
                <i class="fas fa-money-bill-wave text-red-500 text-lg"></i>
            </div>
        </div>
        <div class="bg-white p-3 rounded-lg border border-blue-200 shadow-sm">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Matumizi Ya Leo</p>
                    <p class="text-xl font-bold text-blue-700">{{ number_format($todayExpenses, 0) }}</p>
                </div>
                <i class="fas fa-calendar-day text-blue-500 text-lg"></i>
            </div>
        </div>
        <div class="bg-white p-3 rounded-lg border border-purple-200 shadow-sm">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Idadi ya Matumizi</p>
                    <p class="text-xl font-bold text-purple-700">{{ $expensesCount }}</p>
                </div>
                <i class="fas fa-list text-purple-500 text-lg"></i>
            </div>
        </div>
        <div class="bg-white p-3 rounded-lg border border-amber-200 shadow-sm">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Wastani wa Matumizi</p>
                    <p class="text-xl font-bold text-amber-700">{{ number_format($averageExpense, 0) }}</p>
                </div>
                <i class="fas fa-chart-pie text-amber-500 text-lg"></i>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm">
        <div class="flex">
            <button data-tab="taarifa" class="tab-button flex-1 py-3 px-4 text-sm font-medium border-r border-gray-200 bg-emerald-50 text-emerald-700">
                <i class="fas fa-table mr-2"></i> Taarifa
            </button>
            <button data-tab="ingiza" class="tab-button flex-1 py-3 px-4 text-sm font-medium border-r border-gray-200 text-gray-600 hover:bg-gray-50">
                <i class="fas fa-plus mr-2"></i> Ingiza
            </button>
            <button data-tab="sajili" class="tab-button flex-1 py-3 px-4 text-sm font-medium text-gray-600 hover:bg-gray-50">
                <i class="fas fa-tags mr-2"></i> Sajili
            </button>
        </div>
    </div>

    <!-- TAB 1: Taarifa -->
    <div id="taarifa-tab-content" class="tab-content space-y-3">
        <!-- Search Bar -->
        <div class="bg-white p-3 rounded-lg border border-gray-200 shadow-sm">
            <div class="flex flex-col sm:flex-row gap-2 sm:items-center">
                <div class="flex-1 relative">
                    <input 
                        type="text" 
                        id="search-input"
                        placeholder="Tafuta aina, maelezo..." 
                        class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500"
                        value="{{ request()->search }}"
                    >
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                <div class="flex gap-2">
                    <button onclick="printMatumizi()" class="px-3 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 text-sm font-medium">
                        <i class="fas fa-print mr-1"></i> Print
                    </button>
                    <button onclick="exportPDF()" class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm font-medium">
                        <i class="fas fa-file-pdf mr-1"></i> PDF
                    </button>
                </div>
            </div>
        </div>

        <!-- Matumizi Table -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-emerald-50">
                            <th class="px-4 py-2 text-left font-medium text-emerald-800">Tarehe & Muda</th>
                            <th class="px-4 py-2 text-left font-medium text-emerald-800">Aina</th>
                            <th class="px-4 py-2 text-left font-medium text-emerald-800 hidden md:table-cell">Maelezo</th>
                            <th class="px-4 py-2 text-right font-medium text-emerald-800">Gharama</th>
                            <th class="px-4 py-2 text-center font-medium text-emerald-800 print:hidden">Vitendo</th>
                        </tr>
                    </thead>
                    <tbody id="matumizi-tbody" class="divide-y divide-gray-100">
                        @forelse($matumizi as $item)
                            <tr class="matumizi-row hover:bg-gray-50" data-matumizi='@json($item)'>
                                <td class="px-4 py-2">
                                    <div class="text-xs text-gray-900">{{ $item->created_at->format('d/m/Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $item->created_at->format('H:i') }}</div>
                                </td>
                                <td class="px-4 py-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold
                                        @if($item->aina === 'Mshahara') bg-green-100 text-green-800 border border-green-200
                                        @elseif($item->aina === 'Bank') bg-blue-100 text-blue-800 border border-blue-200
                                        @elseif($item->aina === 'Kodi TRA') bg-red-100 text-red-800 border border-red-200
                                        @elseif($item->aina === 'Kodi Pango') bg-yellow-100 text-yellow-800 border border-yellow-200
                                        @else bg-gray-100 text-gray-800 border border-gray-200 @endif">
                                        {{ $item->aina }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 hidden md:table-cell">
                                    <div class="text-xs text-gray-700 truncate max-w-xs">{{ $item->maelezo ?: '--' }}</div>
                                </td>
                                <td class="px-4 py-2 text-right">
                                    <div class="text-sm font-bold text-red-700">{{ number_format($item->gharama, 0) }}</div>
                                </td>
                                <td class="px-4 py-2 text-center print:hidden">
                                    <div class="flex justify-center space-x-2">
                                        <button class="edit-matumizi-btn text-emerald-600 hover:text-emerald-800"
                                                data-id="{{ $item->id }}" title="Badili">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="delete-matumizi-btn text-red-600 hover:text-red-800"
                                                data-id="{{ $item->id }}" data-name="{{ $item->aina }}" title="Futa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                    <i class="fas fa-receipt text-3xl mb-2 text-gray-300"></i>
                                    <p>Hakuna matumizi bado</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($matumizi->count() > 0)
                    <tfoot>
                        <tr class="bg-gray-50">
                            <td colspan="3" class="px-4 py-3 text-right text-sm font-semibold text-gray-700 hidden md:table-cell">
                                Jumla ya Matumizi:
                            </td>
                            <td class="px-4 py-3 text-right text-sm font-bold text-red-700">
                                {{ number_format($matumizi->sum('gharama'), 0) }}
                            </td>
                            <td class="print:hidden"></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
            
            <!-- Pagination -->
            @if($matumizi->hasPages())
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $matumizi->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- TAB 2: Ingiza -->
    <div id="ingiza-tab-content" class="tab-content hidden">
        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
            <form method="POST" action="{{ route('matumizi.store') }}" id="matumizi-form" class="space-y-4">
                @csrf
                
                <!-- Aina Selection -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Aina ya Matumizi *</label>
                    <select name="aina" id="aina-select" 
                            class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500" 
                            required>
                        <option value="">-- Chagua Aina --</option>
                        @foreach(['Mshahara', 'Bank', 'Kodi TRA', 'Kodi Pango'] as $aina)
                            <option value="{{ $aina }}" {{ old('aina') == $aina ? 'selected' : '' }}>
                                {{ $aina }}
                            </option>
                        @endforeach
                        @if($aina_za_matumizi->count() > 0)
                            @foreach($aina_za_matumizi as $aina)
                                <option value="{{ $aina->jina }}">{{ $aina->jina }}</option>
                            @endforeach
                        @endif
                        <option value="mengineyo">Mengineyo...</option>
                    </select>
                    
                    <!-- Custom Type Input -->
                    <div id="custom-aina-container" class="mt-2 hidden">
                        <input type="text" 
                               name="aina_mpya" 
                               id="aina_mpya"
                               placeholder="Ingiza aina mpya ya matumizi..."
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    </div>
                </div>

                <!-- Form Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <!-- Gharama -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Kiasi cha Gharama (TZS) *</label>
                        <input type="number" step="0.01" name="gharama" id="gharama" 
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                               placeholder="0.00" required value="{{ old('gharama') }}">
                    </div>

                    <!-- Tarehe -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Tarehe</label>
                        <input type="date" name="tarehe" id="tarehe" 
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                               value="{{ old('tarehe', now()->format('Y-m-d')) }}">
                    </div>

                    <!-- Maelezo -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Maelezo</label>
                        <textarea name="maelezo" rows="2" id="maelezo"
                                  class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                                  placeholder="Maelezo ya ziada...">{{ old('maelezo') }}</textarea>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex gap-2 pt-4 border-t border-gray-200">
                    <button type="submit" 
                            class="flex-1 bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700 text-sm font-medium">
                        <i class="fas fa-save mr-1"></i> Hifadhi
                    </button>
                    <button type="reset" onclick="clearMatumiziForm()"
                            class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 text-sm">
                        <i class="fas fa-redo mr-1"></i> Safisha
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- TAB 3: Sajili -->
    <div id="sajili-tab-content" class="tab-content hidden">
        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
            <h3 class="text-sm font-semibold text-gray-800 mb-3">Sajili Aina Mpya ya Matumizi</h3>
            <form method="POST" action="{{ route('matumizi.sajili-aina') }}" id="sajili-form" class="space-y-4">
                @csrf
                
                <!-- Jina -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Jina la Aina *</label>
                    <input type="text" name="jina" id="jina-aina" 
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                           placeholder="Mfano: Mafuta ya Gari, Umeme, N.K." required>
                </div>

                <!-- Maelezo -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Maelezo</label>
                    <textarea name="maelezo" rows="2" 
                              class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                              placeholder="Maelezo ya aina hii ya matumizi..."></textarea>
                </div>

                <!-- Buttons -->
                <div class="flex gap-2 pt-4 border-t border-gray-200">
                    <button type="submit" 
                            class="flex-1 bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700 text-sm font-medium">
                        <i class="fas fa-save mr-1"></i> Sajili
                    </button>
                    <button type="reset" 
                            class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 text-sm">
                        <i class="fas fa-redo mr-1"></i> Safisha
                    </button>
                </div>
            </form>

            <!-- List of Registered Types -->
            @if($aina_za_matumizi->count() > 0)
            <div class="mt-6 pt-4 border-t border-gray-200">
                <h4 class="text-xs font-semibold text-gray-700 mb-3">Aina Zilizosajiliwa</h4>
                <div class="space-y-2">
                    @foreach($aina_za_matumizi as $aina)
                        <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                            <div class="flex items-center">
                                <span class="text-sm text-gray-800">{{ $aina->jina }}</span>
                                @if($aina->maelezo)
                                    <span class="text-xs text-gray-500 ml-2">- {{ $aina->maelezo }}</span>
                                @endif
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-xs bg-emerald-100 text-emerald-800 px-2 py-1 rounded-full">
                                    {{ $aina->matumizi_count ?? 0 }} matumizi
                                </span>
                                <button type="button" onclick="deleteAina('{{ $aina->id }}', '{{ $aina->jina }}')"
                                        class="text-red-500 hover:text-red-700 text-sm">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="edit-modal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-lg shadow-lg w-full max-w-md mx-auto z-50 max-h-[90vh] overflow-y-auto">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-800">Rekebisha Matumizi</h3>
        </div>
        <form id="edit-form" method="POST" class="p-4">
            @csrf
            @method('PUT')
            <div class="space-y-3">
                <!-- Aina -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Aina ya Matumizi *</label>
                    <select name="aina" id="edit-aina" 
                            class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500" 
                            required>
                        @foreach(['Mshahara', 'Bank', 'Kodi TRA', 'Kodi Pango'] as $aina)
                            <option value="{{ $aina }}">{{ $aina }}</option>
                        @endforeach
                        @if($aina_za_matumizi->count() > 0)
                            @foreach($aina_za_matumizi as $aina)
                                <option value="{{ $aina->jina }}">{{ $aina->jina }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <!-- Maelezo -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Maelezo</label>
                    <input type="text" name="maelezo" id="edit-maelezo"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                </div>

                <!-- Gharama -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Kiasi cha Gharama (TZS) *</label>
                    <input type="number" step="0.01" name="gharama" id="edit-gharama"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                           required>
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

<!-- Delete Expense Modal -->
<div id="delete-modal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-lg shadow-lg w-full max-w-sm mx-auto z-50">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-800">Thibitisha Kufuta</h3>
        </div>
        <div class="p-4">
            <div class="text-center mb-4">
                <i class="fas fa-exclamation-triangle text-amber-500 text-2xl mb-2"></i>
                <p class="text-gray-700 text-sm mb-1">Una uhakika unataka kufuta matumizi ya</p>
                <p class="text-gray-900 font-medium" id="delete-expense-name"></p>
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

<!-- Delete Aina Modal -->
<div id="delete-aina-modal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-lg shadow-lg w-full max-w-sm mx-auto z-50">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-800">Futa Aina ya Matumizi</h3>
        </div>
        <div class="p-4">
            <div class="text-center mb-4">
                <i class="fas fa-exclamation-triangle text-amber-500 text-2xl mb-2"></i>
                <p class="text-gray-700 text-sm mb-1">Una uhakika unataka kufuta aina ya matumizi</p>
                <p class="text-gray-900 font-medium" id="delete-aina-name"></p>
                <p class="text-gray-500 text-xs mt-2">Aina hii itaondolewa kwenye orodha ya uchaguzi</p>
            </div>
            <div class="flex gap-2">
                <button id="cancel-delete-aina"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 text-sm">
                    Ghairi
                </button>
                <form id="delete-aina-form" method="POST" class="flex-1">
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
class SmartMatumiziManager {
    constructor() {
        this.currentTab = this.getSavedTab() || 'taarifa';
        this.searchTimeout = null;
        this.init();
    }

    init() {
        this.bindEvents();
        this.showTab(this.currentTab);
        this.setupAjaxForms();
        this.setupAinaSelection();
    }

    getSavedTab() {
        return sessionStorage.getItem('matumizi_tab') || 'taarifa';
    }

    saveTab(tab) {
        sessionStorage.setItem('matumizi_tab', tab);
    }

    bindEvents() {
        // Tab navigation
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', (e) => {
                const tab = e.target.closest('.tab-button').dataset.tab;
                this.showTab(tab);
                this.saveTab(tab);
            });
        });

        // Search with debounce
        const searchInput = document.getElementById('search-input');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                clearTimeout(this.searchTimeout);
                this.searchTimeout = setTimeout(() => {
                    this.filterMatumizi(e.target.value.toLowerCase().trim());
                }, 300);
            });
        }

        // Edit/Delete buttons
        this.bindMatumiziActions();

        // Modal events
        this.bindModalEvents();

        // Form validation
        this.bindFormValidation();
    }

    showTab(tabName) {
        // Update tabs
        document.querySelectorAll('.tab-button').forEach(button => {
            if (button.dataset.tab === tabName) {
                button.classList.add('bg-emerald-50', 'text-emerald-700');
                button.classList.remove('text-gray-600', 'hover:bg-gray-50');
            } else {
                button.classList.remove('bg-emerald-50', 'text-emerald-700');
                button.classList.add('text-gray-600', 'hover:bg-gray-50');
            }
        });

        // Update tab content
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        
        document.getElementById(`${tabName}-tab-content`).classList.remove('hidden');
        this.currentTab = tabName;
    }

    bindMatumiziActions() {
        // Edit buttons
        document.querySelectorAll('.edit-matumizi-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                const row = e.target.closest('.matumizi-row');
                const matumizi = JSON.parse(row.dataset.matumizi);
                this.editMatumizi(matumizi);
            });
        });

        // Delete buttons
        document.querySelectorAll('.delete-matumizi-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                const matumiziId = e.target.closest('.delete-matumizi-btn').dataset.id;
                const expenseName = e.target.closest('.delete-matumizi-btn').dataset.name;
                this.deleteMatumizi(matumiziId, expenseName);
            });
        });
    }

    bindModalEvents() {
        // Edit modal
        const editModal = document.getElementById('edit-modal');
        const closeEditBtn = document.getElementById('close-edit-modal');

        if (closeEditBtn) {
            closeEditBtn.addEventListener('click', () => editModal.classList.add('hidden'));
        }
        
        if (editModal) {
            editModal.addEventListener('click', (e) => {
                if (e.target === editModal || e.target.classList.contains('modal-overlay')) {
                    editModal.classList.add('hidden');
                }
            });
        }

        // Delete modals
        const deleteModal = document.getElementById('delete-modal');
        const cancelDeleteBtn = document.getElementById('cancel-delete');

        if (cancelDeleteBtn) {
            cancelDeleteBtn.addEventListener('click', () => deleteModal.classList.add('hidden'));
        }
        
        if (deleteModal) {
            deleteModal.addEventListener('click', (e) => {
                if (e.target === deleteModal || e.target.classList.contains('modal-overlay')) {
                    deleteModal.classList.add('hidden');
                }
            });
        }

        // Delete aina modal
        const deleteAinaModal = document.getElementById('delete-aina-modal');
        const cancelDeleteAinaBtn = document.getElementById('cancel-delete-aina');

        if (cancelDeleteAinaBtn) {
            cancelDeleteAinaBtn.addEventListener('click', () => deleteAinaModal.classList.add('hidden'));
        }
        
        if (deleteAinaModal) {
            deleteAinaModal.addEventListener('click', (e) => {
                if (e.target === deleteAinaModal || e.target.classList.contains('modal-overlay')) {
                    deleteAinaModal.classList.add('hidden');
                }
            });
        }

        // Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                if (editModal) editModal.classList.add('hidden');
                if (deleteModal) deleteModal.classList.add('hidden');
                if (deleteAinaModal) deleteAinaModal.classList.add('hidden');
            }
        });
    }

    bindFormValidation() {
        const matumiziForm = document.getElementById('matumizi-form');
        const editForm = document.getElementById('edit-form');
        
        // Validation for main form
        if (matumiziForm) {
            const gharamaInput = document.getElementById('gharama');
            
            const validateGharama = () => {
                const gharama = parseFloat(gharamaInput.value);

                if (gharama <= 0) {
                    gharamaInput.classList.add('border-red-500');
                    return false;
                } else {
                    gharamaInput.classList.remove('border-red-500');
                    return true;
                }
            };

            gharamaInput.addEventListener('input', validateGharama);

            matumiziForm.addEventListener('submit', (e) => {
                if (!validateGharama()) {
                    e.preventDefault();
                    e.stopPropagation();
                    this.showNotification('⚠️ Gharama lazima iwe zaidi ya sifuri!', 'error');
                }
            });
        }

        // Validation for edit form
        if (editForm) {
            const editGharama = editForm.querySelector('[name="gharama"]');
            
            if (editGharama) {
                const editValidateGharama = () => {
                    const gharama = parseFloat(editGharama.value);
                    
                    if (gharama <= 0) {
                        editGharama.classList.add('border-red-500');
                        return false;
                    } else {
                        editGharama.classList.remove('border-red-500');
                        return true;
                    }
                };

                editGharama.addEventListener('input', editValidateGharama);

                editForm.addEventListener('submit', (e) => {
                    if (!editValidateGharama()) {
                        e.preventDefault();
                        e.stopPropagation();
                        this.showNotification('⚠️ Gharama lazima iwe zaidi ya sifuri!', 'error');
                    }
                });
            }
        }
    }

    setupAinaSelection() {
        const ainaSelect = document.getElementById('aina-select');
        const customAinaContainer = document.getElementById('custom-aina-container');
        
        if (ainaSelect) {
            ainaSelect.addEventListener('change', (e) => {
                if (e.target.value === 'mengineyo') {
                    customAinaContainer.classList.remove('hidden');
                    const customInput = document.getElementById('aina_mpya');
                    customInput.required = true;
                    customInput.focus();
                } else {
                    customAinaContainer.classList.add('hidden');
                    const customInput = document.getElementById('aina_mpya');
                    customInput.required = false;
                }
            });
        }
    }

    filterMatumizi(searchTerm) {
        const rows = document.querySelectorAll('.matumizi-row');
        let found = false;
        
        rows.forEach(row => {
            const matumizi = JSON.parse(row.dataset.matumizi);
            const searchText = `
                ${matumizi.aina || ''}
                ${matumizi.maelezo || ''}
            `.toLowerCase();
            
            if (searchText.includes(searchTerm) || !searchTerm) {
                row.classList.remove('hidden');
                found = true;
            } else {
                row.classList.add('hidden');
            }
        });

        if (!found && searchTerm) {
            this.showNotification('Hakuna matumizi zinazolingana', 'info');
        }
    }

    editMatumizi(matumizi) {
        const editForm = document.getElementById('edit-form');
        if (!editForm) return;

        document.getElementById('edit-aina').value = matumizi.aina || '';
        document.getElementById('edit-maelezo').value = matumizi.maelezo || '';
        document.getElementById('edit-gharama').value = matumizi.gharama || '';
        editForm.action = `/matumizi/${matumizi.id}`;
        
        const editModal = document.getElementById('edit-modal');
        if (editModal) editModal.classList.remove('hidden');
    }

    deleteMatumizi(matumiziId, expenseName) {
        const deleteForm = document.getElementById('delete-form');
        const deleteModal = document.getElementById('delete-modal');
        const deleteExpenseName = document.getElementById('delete-expense-name');
        
        if (!deleteForm || !deleteModal || !deleteExpenseName) return;
        
        deleteExpenseName.textContent = expenseName;
        deleteForm.action = `/matumizi/${matumiziId}`;
        deleteModal.classList.remove('hidden');
    }

    setupAjaxForms() {
        // Main form
        const matumiziForm = document.getElementById('matumizi-form');
        if (matumiziForm) {
            matumiziForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.submitForm(matumiziForm, 'Matumizi yamehifadhiwa!');
                this.clearMatumiziForm();
            });
        }

        // Sajili form
        const sajiliForm = document.getElementById('sajili-form');
        if (sajiliForm) {
            sajiliForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.submitForm(sajiliForm, 'Aina mpya ya matumizi imesajiliwa!');
                document.getElementById('jina-aina').value = '';
                document.getElementById('sajili-form').querySelector('textarea').value = '';
            });
        }

        // Edit form
        const editForm = document.getElementById('edit-form');
        if (editForm) {
            editForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.submitForm(editForm, 'Matumizi imerekebishwa!');
                document.getElementById('edit-modal').classList.add('hidden');
            });
        }

        // Delete form
        const deleteForm = document.getElementById('delete-form');
        if (deleteForm) {
            deleteForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.submitForm(deleteForm, 'Matumizi imefutwa!');
                document.getElementById('delete-modal').classList.add('hidden');
            });
        }

        // Delete aina form
        const deleteAinaForm = document.getElementById('delete-aina-form');
        if (deleteAinaForm) {
            deleteAinaForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.submitForm(deleteAinaForm, 'Aina ya matumizi imefutwa!');
                document.getElementById('delete-aina-modal').classList.add('hidden');
            });
        }
    }

    async submitForm(form, successMessage = 'Operesheni imekamilika!') {
        const formData = new FormData(form);
        const submitButton = form.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        
        try {
            // Disable submit button
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

            if (response.ok) {
                const message = data.message || successMessage;
                this.showNotification(message, 'success');
                
                // Reload after successful operation
                setTimeout(() => window.location.reload(), 1000);
            } else {
                const error = data.errors ? Object.values(data.errors)[0][0] : data.message;
                this.showNotification(error || 'Hitilafu imetokea', 'error');
            }
        } catch (error) {
            this.showNotification('Hitilafu ya mtandao', 'error');
        } finally {
            // Re-enable submit button
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
        }
    }

    clearMatumiziForm() {
        const form = document.getElementById('matumizi-form');
        if (form) {
            form.reset();
        }
        
        // Reset date to today
        const today = new Date().toISOString().split('T')[0];
        const tareheInput = document.querySelector('#matumizi-form input[name="tarehe"]');
        if (tareheInput) {
            tareheInput.value = today;
        }
        
        // Hide custom aina container
        const customContainer = document.getElementById('custom-aina-container');
        if (customContainer) {
            customContainer.classList.add('hidden');
        }
        
        // Reset select
        const ainaSelect = document.getElementById('aina-select');
        if (ainaSelect) {
            ainaSelect.value = '';
        }
    }

    deleteAina(ainaId, ainaName) {
        const deleteForm = document.getElementById('delete-aina-form');
        const deleteModal = document.getElementById('delete-aina-modal');
        const deleteAinaName = document.getElementById('delete-aina-name');
        
        if (!deleteForm || !deleteModal || !deleteAinaName) return;
        
        deleteAinaName.textContent = ainaName;
        deleteForm.action = `/matumizi/aina/${ainaId}/delete`;
        deleteModal.classList.remove('hidden');
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
        notification.className = `rounded border px-4 py-3 text-sm font-medium mb-2 ${colors[type]} shadow-sm animate-fade-in`;
        notification.textContent = message;

        container.appendChild(notification);

        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateY(-10px) translateX(-50%)';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
}

// Print function
function printMatumizi() {
    const printWindow = window.open('', '_blank');
    const rows = document.querySelectorAll('.matumizi-row');
    
    let tableRows = '';
    rows.forEach(row => {
        const matumizi = JSON.parse(row.dataset.matumizi);
        tableRows += `
            <tr>
                <td style="border: 1px solid #ddd; padding: 8px;">${matumizi.created_at ? new Date(matumizi.created_at).toLocaleDateString('en-GB') : ''}</td>
                <td style="border: 1px solid #ddd; padding: 8px;">${matumizi.aina || ''}</td>
                <td style="border: 1px solid #ddd; padding: 8px;">${matumizi.maelezo || '--'}</td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">${parseFloat(matumizi.gharama).toLocaleString()}</td>
            </tr>`;
    });
    
    const total = Array.from(rows).reduce((sum, row) => {
        const matumizi = JSON.parse(row.dataset.matumizi);
        return sum + parseFloat(matumizi.gharama);
    }, 0);
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Orodha ya Matumizi - ${new Date().toLocaleDateString()}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; }
                th { background-color: #f3f4f6; font-weight: bold; }
                .header { text-align: center; margin-bottom: 30px; }
                .header h2 { margin: 0; color: #047857; }
                .header p { margin: 5px 0 0 0; color: #6b7280; }
                .total-row { background-color: #f8f9fa; font-weight: bold; }
                @media print {
                    body { margin: 0; }
                    .no-print { display: none; }
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>Orodha ya Matumizi</h2>
                <p>${new Date().toLocaleDateString()}</p>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Tarehe</th>
                        <th>Aina</th>
                        <th>Maelezo</th>
                        <th>Gharama (TZS)</th>
                    </tr>
                </thead>
                <tbody>
                    ${tableRows}
                    <tr class="total-row">
                        <td colspan="3" style="text-align: right;">Jumla:</td>
                        <td style="text-align: right;">${total.toLocaleString()}</td>
                    </tr>
                </tbody>
            </table>
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

// PDF Export
function exportPDF() {
    const search = new URLSearchParams(window.location.search);
    search.set('export', 'pdf');
    window.open(`${window.location.pathname}?${search.toString()}`, '_blank');
}

// Helper function to clear form
function clearMatumiziForm() {
    window.matumiziManager.clearMatumiziForm();
}

// Helper function to delete aina
function deleteAina(ainaId, ainaName) {
    if (window.matumiziManager) {
        window.matumiziManager.deleteAina(ainaId, ainaName);
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    window.matumiziManager = new SmartMatumiziManager();
    
    // Save tab state
    window.addEventListener('beforeunload', () => {
        if (window.matumiziManager) {
            window.matumiziManager.saveTab(window.matumiziManager.currentTab);
        }
    });
});
</script>
@endpush