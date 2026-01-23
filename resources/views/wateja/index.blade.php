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
                                        <button class="delete-mteja-btn text-red-600 hover:text-red-800"
                                                data-id="{{ $item->id }}" data-name="{{ $item->jina }}" title="Futa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @if($item->simu)
                                        <a href="tel:{{ $item->simu }}" class="text-green-600 hover:text-green-800" title="Piga Simu">
                                            <i class="fas fa-phone"></i>
                                        </a>
                                        @endif
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
                        <input type="text" name="simu" 
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                               placeholder="Mf. 0712345678" required>
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
        </div>
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
                <!-- Jina -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Jina Kamili *</label>
                    <input type="text" name="jina" id="edit-jina"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                           required>
                </div>

                <!-- Simu -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Namba ya Simu *</label>
                    <input type="text" name="simu" id="edit-simu"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                           required>
                </div>

                <!-- Barua Pepe -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Barua Pepe</label>
                    <input type="email" name="barua_pepe" id="edit-email"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                </div>

                <!-- Anapoishi -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Anuani ya Makazi</label>
                    <input type="text" name="anapoishi" id="edit-address"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                </div>

                <!-- Maelezo -->
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
    }

    getSavedTab() {
        return sessionStorage.getItem('wateja_tab') || 'taarifa';
    }

    saveTab(tab) {
        sessionStorage.setItem('wateja_tab', tab);
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
                    this.filterWateja(e.target.value.toLowerCase().trim());
                }, 300);
            });
        }

        // Mteja action buttons
        this.bindMtejaActions();

        // Modal events
        this.bindModalEvents();
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

    bindMtejaActions() {
        // View buttons
        document.querySelectorAll('.view-mteja-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                const row = e.target.closest('.mteja-row');
                const mteja = JSON.parse(row.dataset.mteja);
                this.viewMteja(mteja);
            });
        });

        // Edit buttons
        document.querySelectorAll('.edit-mteja-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                const row = e.target.closest('.mteja-row');
                const mteja = JSON.parse(row.dataset.mteja);
                this.editMteja(mteja);
            });
        });

        // Delete buttons
        document.querySelectorAll('.delete-mteja-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                const mtejaId = e.target.closest('.delete-mteja-btn').dataset.id;
                const mtejaName = e.target.closest('.delete-mteja-btn').dataset.name;
                this.deleteMteja(mtejaId, mtejaName);
            });
        });
    }

    bindModalEvents() {
        // View modal
        const viewModal = document.getElementById('view-modal');
        const closeViewBtn = document.getElementById('close-view-modal');

        if (closeViewBtn) {
            closeViewBtn.addEventListener('click', () => viewModal.classList.add('hidden'));
        }
        
        if (viewModal) {
            viewModal.addEventListener('click', (e) => {
                if (e.target === viewModal || e.target.classList.contains('modal-overlay')) {
                    viewModal.classList.add('hidden');
                }
            });
        }

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

        // Delete modal
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

        // Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                if (viewModal) viewModal.classList.add('hidden');
                if (editModal) editModal.classList.add('hidden');
                if (deleteModal) deleteModal.classList.add('hidden');
            }
        });
    }

    filterWateja(searchTerm) {
        const rows = document.querySelectorAll('.mteja-row');
        let found = false;
        
        rows.forEach(row => {
            const mteja = JSON.parse(row.dataset.mteja);
            const searchText = `
                ${mteja.jina || ''}
                ${mteja.simu || ''}
                ${mteja.barua_pepe || ''}
                ${mteja.anapoishi || ''}
            `.toLowerCase();
            
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

        // Populate view modal
        document.getElementById('view-initial').textContent = mteja.jina ? mteja.jina.charAt(0).toUpperCase() : 'M';
        document.getElementById('view-jina').textContent = mteja.jina || '--';
        document.getElementById('view-simu').textContent = mteja.simu ? `+${mteja.simu}` : '--';
        document.getElementById('view-phone').textContent = mteja.simu || '--';
        document.getElementById('view-email').textContent = mteja.barua_pepe || '--';
        document.getElementById('view-address').textContent = mteja.anapoishi || '--';
        document.getElementById('view-date').textContent = mteja.created_at ? new Date(mteja.created_at).toLocaleDateString() : '--';
        document.getElementById('view-notes').textContent = mteja.maelezo || 'Hakuna maelezo ya ziada';

        // Show/hide call button
        const callButton = document.getElementById('call-button');
        if (mteja.simu) {
            callButton.href = `tel:${mteja.simu}`;
            callButton.classList.remove('hidden');
        } else {
            callButton.classList.add('hidden');
        }

        viewModal.classList.remove('hidden');
    }

    editMteja(mteja) {
        const editForm = document.getElementById('edit-form');
        if (!editForm) return;

        // Populate edit form
        document.getElementById('edit-jina').value = mteja.jina || '';
        document.getElementById('edit-simu').value = mteja.simu || '';
        document.getElementById('edit-email').value = mteja.barua_pepe || '';
        document.getElementById('edit-address').value = mteja.anapoishi || '';
        document.getElementById('edit-notes').value = mteja.maelezo || '';
        
        editForm.action = `/wateja/${mteja.id}`;
        
        const editModal = document.getElementById('edit-modal');
        if (editModal) editModal.classList.remove('hidden');
    }

    deleteMteja(mtejaId, mtejaName) {
        const deleteForm = document.getElementById('delete-form');
        const deleteModal = document.getElementById('delete-modal');
        const deleteMtejaName = document.getElementById('delete-mteja-name');
        
        if (!deleteForm || !deleteModal || !deleteMtejaName) return;
        
        deleteMtejaName.textContent = mtejaName;
        deleteForm.action = `/wateja/${mtejaId}`;
        deleteModal.classList.remove('hidden');
    }

    setupAjaxForms() {
        // Mteja form
        const mtejaForm = document.getElementById('mteja-form');
        if (mtejaForm) {
            mtejaForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.submitForm(mtejaForm, 'Mteja ameongezwa!');
                mtejaForm.reset();
            });
        }

        // Edit form
        const editForm = document.getElementById('edit-form');
        if (editForm) {
            editForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.submitForm(editForm, 'Mteja imerekebishwa!');
                document.getElementById('edit-modal').classList.add('hidden');
            });
        }

        // Delete form
        const deleteForm = document.getElementById('delete-form');
        if (deleteForm) {
            deleteForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.submitForm(deleteForm, 'Mteja amefutwa!');
                document.getElementById('delete-modal').classList.add('hidden');
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
            </tr>`;
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
                @media print {
                    body { margin: 0; }
                    .no-print { display: none; }
                }
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
                <tbody>
                    ${tableRows}
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

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    window.watejaManager = new SmartWatejaManager();
    
    // Save tab state
    window.addEventListener('beforeunload', () => {
        if (window.watejaManager) {
            window.watejaManager.saveTab(window.watejaManager.currentTab);
        }
    });
});
</script>
@endpush