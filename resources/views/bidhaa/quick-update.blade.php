@extends('layouts.app')

@section('title', 'Haraka - Bidhaa')

@section('content')
<div class="w-full min-h-screen bg-gray-50" id="app-container" 
     data-is-boss="{{ $isBoss ? 'true' : 'false' }}"
     data-can-edit-delete="{{ $canEditDelete ? 'true' : 'false' }}">

    <!-- Toast Notification -->
    <div id="quick-toast" class="hidden fixed bottom-4 left-1/2 transform -translate-x-1/2 bg-emerald-600 text-white px-4 py-2 rounded-lg shadow-lg flex items-center gap-2 z-50 transition-all duration-300 translate-y-20 opacity-0 max-w-[90%] w-auto mx-4">
        <i class="fas fa-check-circle text-white text-sm"></i>
        <span id="quick-toast-message" class="flex-1 text-sm font-medium">Imefanikiwa!</span>
        <button onclick="hideQuickToast()" class="text-white/70 hover:text-white ml-2">
            <i class="fas fa-times text-sm"></i>
        </button>
    </div>

    <!-- Main Card - FULL WIDTH -->
    <div class="w-full bg-white shadow-sm min-h-screen">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 px-4 py-3 flex items-center justify-between sticky top-0 z-10 w-full">
            <div class="flex items-center gap-3">
                <div class="h-9 w-9 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-bolt text-white text-sm"></i>
                </div>
                <div>
                   
                    <p class="text-[10px] text-emerald-100">Sasisha au ongeza bidhaa</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span id="mode-indicator" class="text-[10px] text-emerald-100 bg-white/20 px-2 py-0.5 rounded-full">Tayari</span>
                <a href="{{ route('bidhaa.index') }}" class="text-white/80 hover:text-white text-sm" title="Rudi">
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>
        </div>



        <!-- Search Bar - Full Width -->
        <div class="p-3 bg-gray-50 border-b border-gray-200 w-full">
            <div class="relative w-full">
                <input 
                    type="text" 
                    id="quick-search-input"
                    placeholder="🔍 Tafuta jina, aina au barcode..." 
                    class="w-full pl-10 pr-12 py-3 border-2 border-gray-200 rounded-lg text-base focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white shadow-sm"
                    autocomplete="off"
                    autofocus
                    inputmode="search"
                >
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                <div id="quick-search-spinner" class="absolute right-12 top-1/2 transform -translate-y-1/2 hidden">
                    <i class="fas fa-spinner fa-spin text-emerald-500"></i>
                </div>
                <button id="clear-quick-search" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 hidden p-1">
                    <i class="fas fa-times-circle text-lg"></i>
                </button>
            </div>
            
            <!-- Search Results - Full Width -->
            <div id="quick-search-results" class="hidden mt-2 border-2 border-emerald-200 rounded-lg bg-white shadow-xl max-h-[50vh] overflow-y-auto absolute z-50 left-3 right-3"></div>
        </div>

        <!-- ============================================ -->
        <!-- PRODUCT DISPLAY / EDIT AREA - FULL WIDTH -->
        <!-- ============================================ -->
        <div id="product-display-area" class="hidden p-3 w-full">
        <!-- Compact Product Card -->
<div class="bg-emerald-50 rounded-md border border-emerald-200 px-2 py-1.5 mb-2 w-full">
    <div class="flex items-center gap-2 w-full">

        <!-- Image -->
        <div id="product-image-container"
             class="h-9 w-9 bg-white rounded-md flex items-center justify-center flex-shrink-0 overflow-hidden border border-gray-200">
            <i class="fas fa-box text-gray-300 text-base"></i>
        </div>

        <!-- Product Info -->
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2">
                <h3 id="product-name-display"
                    class="text-sm font-semibold text-gray-800 truncate">
                    -
                </h3>

                <span id="product-aina-display"
                      class="text-[10px] px-1.5 py-0.5 bg-white rounded text-gray-500 whitespace-nowrap">
                    -
                </span>

                <span id="product-barcode-display"
                      class="text-[10px] px-1.5 py-0.5 bg-white rounded text-gray-500 font-mono truncate">
                    -
                </span>
            </div>
        </div>

        <!-- Status -->
        <div class="flex-shrink-0 text-right flex items-center gap-2">
            <span id="quick-stock-status"
                  class="text-[10px] font-medium px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700">
                Ipo
            </span>

            <span id="product-last-updated"
                  class="text-[9px] text-gray-400 whitespace-nowrap">
                -
            </span>
        </div>

    </div>
</div>

            <!-- Edit Form - Full Width -->
            <form id="quick-update-form" class="space-y-3 w-full">
                @csrf
                @method('PUT')
                <input type="hidden" id="quick-product-id" name="product_id" value="">

                <!-- Row 1: Jina + Aina (Side by Side) -->
                <div class="grid grid-cols-2 gap-3 w-full">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Jina</label>
                        <input type="text" id="quick-jina" name="jina" 
                               class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white"
                               required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Aina</label>
                        <input type="text" id="quick-aina" name="aina" 
                               class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white"
                               placeholder="Aina ya bidhaa">
                    </div>
                </div>

                <!-- Row 2: Stock -->
                <div class="grid grid-cols-2 gap-3 w-full">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Iliyopo</label>
                        <div class="bg-gray-50 border border-gray-200 rounded-lg px-3 py-2.5 text-center">
                            <span id="quick-current-stock" class="text-lg font-bold text-emerald-700">0</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-emerald-700 mb-1">
                            <span class="text-emerald-600">+</span> Ongeza
                        </label>
                        <input type="number" id="quick-add-stock" name="add_stock" min="0" step="0.01"
                               class="w-full px-3 py-2.5 border-2 border-emerald-300 rounded-lg text-sm text-center focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white"
                               placeholder="0"
                               inputmode="decimal">
                    </div>
                </div>

                <!-- Row 3: Prices -->
                <div class="grid grid-cols-2 gap-3 w-full">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Bei Nunua (TZS)</label>
                        <input type="number" id="quick-bei-nunua" name="bei_nunua" min="0" step="0.01"
                               class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white"
                               placeholder="0"
                               inputmode="decimal">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Bei Kuuza (TZS)</label>
                        <input type="number" id="quick-bei-kuuza" name="bei_kuuza" min="0" step="0.01"
                               class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white"
                               placeholder="0"
                               inputmode="decimal">
                    </div>
                </div>

                <!-- Row 4: Expiry + Profit -->
                <div class="grid grid-cols-3 gap-3 w-full">
                    <div class="col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Tarehe ya Mwisho</label>
                        <input type="date" id="quick-expiry" name="expiry"
                               class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1 text-center">Faida</label>
                        <div class="bg-gray-50 rounded-lg py-2.5 border border-gray-200 text-center h-[42px] flex items-center justify-center">
                            <span id="quick-profit-preview" class="text-sm font-bold text-emerald-700">0 TZS</span>
                        </div>
                    </div>
                </div>

                <!-- Errors -->
                <div id="quick-form-errors" class="hidden bg-red-50 border border-red-200 rounded-lg p-2 text-red-600 text-xs space-y-0.5 w-full"></div>

                <!-- Buttons -->
                <div class="grid grid-cols-3 gap-3 pt-2 w-full">
                    <button type="submit" 
                            class="col-span-2 bg-emerald-600 text-white px-4 py-3 rounded-lg hover:bg-emerald-700 text-sm font-semibold transition-colors flex items-center justify-center gap-2 touch-manipulation active:scale-95">
                        <i class="fas fa-save text-sm"></i> HIFADHI
                    </button>
                    <button type="button" id="quick-clear-btn"
                            class="bg-gray-200 text-gray-700 px-4 py-3 rounded-lg hover:bg-gray-300 text-sm font-semibold transition-colors flex items-center justify-center touch-manipulation active:scale-95">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- ============================================ -->
        <!-- ADD NEW PRODUCT - FULL WIDTH -->
        <!-- ============================================ -->
        <div id="add-product-area" class="hidden p-3 w-full">
            <div class="bg-amber-50 rounded-lg border-2 border-amber-200 p-4 w-full">
                <div class="flex items-center gap-3 mb-3 w-full">
                    <div class="h-10 w-10 bg-amber-500 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-plus text-white text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm font-bold text-amber-800">Ingiza Bidhaa Mpya</h3>
                        <p class="text-xs text-amber-600">Bidhaa haijapatikana - jaza taarifa</p>
                    </div>
                    <button type="button" id="cancel-add-btn" class="text-amber-600 hover:text-amber-800 p-2 touch-manipulation">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <form id="add-new-form" class="space-y-3 w-full">
                    @csrf
                    <input type="hidden" id="add-search-term" name="search_term" value="">

                    <!-- Row: Jina + Aina -->
                    <div class="grid grid-cols-2 gap-3 w-full">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Jina *</label>
                            <input type="text" id="add-jina" name="jina" 
                                   class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white"
                                   placeholder="Soda, Unga..." required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Aina *</label>
                            <input type="text" id="add-aina" name="aina" 
                                   class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white"
                                   placeholder="Vinywaji..." required>
                        </div>
                    </div>

                    <!-- Row: Kipimo + Idadi -->
                    <div class="grid grid-cols-2 gap-3 w-full">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Kipimo</label>
                            <input type="text" id="add-kipimo" name="kipimo" 
                                   class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white"
                                   placeholder="500ml, 2kg">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Idadi *</label>
                            <input type="number" id="add-idadi" name="idadi" min="0" step="0.01"
                                   class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white"
                                   placeholder="0"
                                   inputmode="decimal" required>
                        </div>
                    </div>

                    <!-- Row: Prices -->
                    <div class="grid grid-cols-2 gap-3 w-full">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Bei Nunua (TZS) *</label>
                            <input type="number" id="add-bei-nunua" name="bei_nunua" min="0" step="0.01"
                                   class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white"
                                   placeholder="0"
                                   inputmode="decimal" required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Bei Kuuza (TZS) *</label>
                            <input type="number" id="add-bei-kuuza" name="bei_kuuza" min="0" step="0.01"
                                   class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white"
                                   placeholder="0"
                                   inputmode="decimal" required>
                        </div>
                    </div>

                    <!-- Expiry -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Tarehe ya Mwisho <span class="text-gray-400">(Hiari)</span></label>
                        <input type="date" id="add-expiry" name="expiry"
                               class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white">
                    </div>

                    <div id="add-form-errors" class="hidden bg-red-50 border border-red-200 rounded-lg p-2 text-red-600 text-xs space-y-0.5 w-full"></div>

                    <!-- Buttons -->
                    <div class="grid grid-cols-2 gap-3 pt-2 w-full">
                        <button type="submit" 
                                class="bg-emerald-600 text-white px-4 py-3 rounded-lg hover:bg-emerald-700 text-sm font-semibold transition-colors flex items-center justify-center gap-2 touch-manipulation active:scale-95">
                            <i class="fas fa-save text-sm"></i> ONGEZA
                        </button>
                        <button type="button" id="cancel-add-btn-bottom"
                                class="bg-gray-200 text-gray-700 px-4 py-3 rounded-lg hover:bg-gray-300 text-sm font-semibold transition-colors touch-manipulation active:scale-95">
                            Ghairi
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Empty State -->
        <div id="product-empty-state" class="flex flex-col items-center justify-center py-12 px-4 w-full">
            <div class="h-20 w-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-search text-gray-400 text-3xl"></i>
            </div>
            <p class="text-base font-medium text-gray-700">Tafuta bidhaa</p>
            <p class="text-sm text-gray-400 text-center">Andika jina, aina au barcode</p>
            <p class="text-xs text-gray-400 mt-1">Kisha bofya kwenye matokeo</p>
        </div>

        <!-- Not Found -->
        <div id="product-not-found" class="hidden flex flex-col items-center justify-center py-8 px-4 w-full">
            <div class="h-16 w-16 bg-amber-50 rounded-full flex items-center justify-center mb-3">
                <i class="fas fa-exclamation-triangle text-amber-500 text-2xl"></i>
            </div>
            <h3 class="text-base font-semibold text-amber-700 mb-1">⚠️ Haijapatikana</h3>
            <p class="text-sm text-gray-500 mb-4 text-center" id="not-found-search-term">-</p>
            <button onclick="showAddProductForm()" 
                    class="inline-flex items-center px-6 py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-base font-semibold touch-manipulation active:scale-95">
                <i class="fas fa-plus mr-2"></i> Ingiza Mpya
            </button>
        </div>

        <div class="h-2 w-full"></div>
    </div>
</div>

<style>
/* ============================================
   FULL WIDTH LAYOUT - NO CENTERING
   ============================================ */

#app-container {
    width: 100% !important;
    max-width: 100% !important;
    margin: 0 !important;
    padding: 0 !important;
}

#quick-search-results {
    max-height: 50vh;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
}
#quick-search-results::-webkit-scrollbar {
    width: 4px;
}
#quick-search-results::-webkit-scrollbar-thumb {
    background: #10b981;
    border-radius: 20px;
}

.search-result-item {
    transition: background 0.15s;
    cursor: pointer;
    -webkit-tap-highlight-color: transparent;
}
.search-result-item:active {
    background: #d1fae5;
}

#quick-toast.show {
    transform: translateY(0);
    opacity: 1;
}

input, button {
    -webkit-tap-highlight-color: transparent;
}
input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
input[type="number"] {
    -moz-appearance: textfield;
}
.touch-manipulation {
    touch-action: manipulation;
}

@keyframes flash-green {
    0%, 100% { background-color: #f0fdf4; }
    50% { background-color: #a7f3d0; }
}
.updated-flash {
    animation: flash-green 0.8s ease 2;
}

/* ============================================
   RESPONSIVE - FULL WIDTH ALWAYS
   ============================================ */

/* Desktop - Full Width */
@media (min-width: 768px) {
    #app-container {
        max-width: 100% !important;
        padding: 0 !important;
    }
    
    .px-4 {
        padding-left: 24px;
        padding-right: 24px;
    }
    
    .p-3 {
        padding: 16px;
    }
    
    .gap-3 {
        gap: 16px;
    }
    
    .grid-cols-2 {
        gap: 16px;
    }
    
    #quick-search-input {
        font-size: 15px;
        padding: 14px 18px;
    }
    
    #quick-search-input {
        padding-left: 44px;
        padding-right: 48px;
    }
}

/* Mobile - Full Width */
@media (max-width: 480px) {
    #app-container {
        max-width: 100% !important;
        padding: 0 !important;
    }
    
    .px-4 {
        padding-left: 12px;
        padding-right: 12px;
    }
    
    .p-3 {
        padding: 10px;
    }
    
    .grid-cols-2 {
        gap: 8px;
    }
    
    .gap-3 {
        gap: 8px;
    }
    
    .text-base {
        font-size: 14px;
    }
    
    .text-sm {
        font-size: 13px;
    }
    
    .h-14.w-14 {
        height: 48px;
        width: 48px;
    }
    
    button, .touch-manipulation {
        min-height: 44px;
    }
    
    #quick-search-input {
        font-size: 14px;
        padding: 12px 14px;
        padding-left: 36px;
        padding-right: 40px;
    }
    
    .search-result-item {
        padding: 10px 12px;
    }
    
    .search-result-item .text-sm {
        font-size: 13px;
    }
}

/* Extra Small */
@media (max-width: 360px) {
    .px-4 {
        padding-left: 8px;
        padding-right: 8px;
    }
    
    .p-3 {
        padding: 6px;
    }
    
    .grid-cols-2 {
        gap: 4px;
    }
    
    .text-sm {
        font-size: 12px;
    }
    
    .text-base {
        font-size: 13px;
    }
    
    .rounded-lg {
        border-radius: 8px;
    }
}

/* Safe Area */
@supports (padding: max(0px)) {
    .pb-safe {
        padding-bottom: max(12px, env(safe-area-inset-bottom));
    }
}
</style>

<script>
// ============================================
// COMPLETE JAVASCRIPT - FULL WIDTH VERSION
// ============================================

let selectedProduct = null;
let searchTimeout = null;
let updateInProgress = false;
let addInProgress = false;
let currentSearchTerm = '';

// DOM Elements
const searchInput = document.getElementById('quick-search-input');
const resultsDropdown = document.getElementById('quick-search-results');
const productDisplay = document.getElementById('product-display-area');
const addProductArea = document.getElementById('add-product-area');
const emptyState = document.getElementById('product-empty-state');
const notFoundState = document.getElementById('product-not-found');

// ============================================
// INITIALIZATION
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    if (searchInput) {
        setTimeout(() => { searchInput.focus(); }, 400);
        searchInput.addEventListener('input', handleQuickSearch);
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && this.value.trim().length >= 2) {
                e.preventDefault();
                performQuickSearch(this.value.trim());
            }
            if (e.key === 'Escape') {
                resultsDropdown.classList.add('hidden');
                this.blur();
            }
        });
    }

    // Clear search
    document.getElementById('clear-quick-search')?.addEventListener('click', function() {
        searchInput.value = '';
        searchInput.focus();
        this.classList.add('hidden');
        resultsDropdown.classList.add('hidden');
        if (!productDisplay.classList.contains('hidden')) clearQuickForm();
        if (!notFoundState.classList.contains('hidden')) notFoundState.classList.add('hidden');
        if (!addProductArea.classList.contains('hidden')) cancelAddProduct();
    });

    // Form submit
    document.getElementById('quick-update-form')?.addEventListener('submit', handleQuickUpdate);
    document.getElementById('quick-clear-btn')?.addEventListener('click', clearQuickForm);

    // Add form
    document.getElementById('add-new-form')?.addEventListener('submit', handleAddNewProduct);
    document.getElementById('cancel-add-btn')?.addEventListener('click', cancelAddProduct);
    document.getElementById('cancel-add-btn-bottom')?.addEventListener('click', cancelAddProduct);

    // Close dropdown on outside click
    document.addEventListener('click', function(e) {
        if (resultsDropdown && searchInput && 
            !resultsDropdown.contains(e.target) && e.target !== searchInput) {
            resultsDropdown.classList.add('hidden');
        }
    });

    // Price preview
    document.getElementById('quick-bei-nunua')?.addEventListener('input', updateProfitPreview);
    document.getElementById('quick-bei-kuuza')?.addEventListener('input', updateProfitPreview);

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.shiftKey && e.key === 'Q') {
            e.preventDefault();
            searchInput.focus();
            searchInput.select();
        }
        if (e.key === 'Escape') {
            if (!resultsDropdown.classList.contains('hidden')) {
                resultsDropdown.classList.add('hidden');
            } else if (!productDisplay.classList.contains('hidden')) {
                clearQuickForm();
            } else if (!addProductArea.classList.contains('hidden')) {
                cancelAddProduct();
            }
        }
    });
});

// ============================================
// SEARCH FUNCTIONS
// ============================================

function handleQuickSearch(e) {
    const searchTerm = e.target.value.trim();
    currentSearchTerm = searchTerm;
    const clearBtn = document.getElementById('clear-quick-search');
    const spinner = document.getElementById('quick-search-spinner');

    if (clearBtn) clearBtn.classList.toggle('hidden', !searchTerm);
    clearTimeout(searchTimeout);

    if (!notFoundState.classList.contains('hidden')) notFoundState.classList.add('hidden');
    if (!addProductArea.classList.contains('hidden')) addProductArea.classList.add('hidden');

    if (searchTerm.length < 2) {
        resultsDropdown.classList.add('hidden');
        resultsDropdown.innerHTML = '';
        return;
    }

    if (spinner) spinner.classList.remove('hidden');
    
    searchTimeout = setTimeout(() => {
        performQuickSearch(searchTerm);
    }, 300);
}

function performQuickSearch(searchTerm) {
    const spinner = document.getElementById('quick-search-spinner');

    fetch(`/bidhaa/quick-update/search?q=${encodeURIComponent(searchTerm)}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (spinner) spinner.classList.add('hidden');
        
        if (data.success && data.data && data.data.length > 0) {
            displayQuickSearchResults(data.data);
        } else {
            resultsDropdown.innerHTML = `
                <div class="p-4 text-center">
                    <div class="text-amber-600 mb-3">
                        <i class="fas fa-exclamation-triangle text-3xl block mb-2"></i>
                        <p class="text-base font-semibold">Haijapatikana</p>
                        <p class="text-sm text-gray-500">"${searchTerm}"</p>
                    </div>
                    <button onclick="showAddProductForm()" 
                            class="inline-flex items-center px-6 py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-base font-semibold touch-manipulation active:scale-95 w-full justify-center">
                        <i class="fas fa-plus mr-2"></i> Ingiza Mpya
                    </button>
                </div>
            `;
            resultsDropdown.classList.remove('hidden');
        }
    })
    .catch(error => {
        if (spinner) spinner.classList.add('hidden');
        resultsDropdown.innerHTML = `<div class="p-4 text-center text-red-500 text-base">Hitilafu. Jaribu tena.</div>`;
        resultsDropdown.classList.remove('hidden');
    });
}

function displayQuickSearchResults(products) {
    let html = '';

    products.forEach(product => {
        const stockStatus = product.idadi <= 0 ? 'text-red-600' : 
                           (product.idadi < 10 ? 'text-amber-600' : 'text-emerald-600');
        
        let imageHtml = '';
        if (product.image_url || product.image_base64) {
            const src = product.image_url || product.image_base64;
            imageHtml = `<img src="${src}" class="h-10 w-10 object-cover rounded-full border border-gray-200 flex-shrink-0">`;
        } else {
            imageHtml = `<div class="h-10 w-10 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-box text-gray-400 text-sm"></i>
            </div>`;
        }

        let timeText = 'Hivi punde';
        let timeClass = 'text-emerald-600';
        if (product.updated_at) {
            const updated = new Date(product.updated_at);
            const now = new Date();
            const diffMins = Math.floor((now - updated) / 60000);
            if (diffMins < 1) { timeText = 'Hivi punde'; timeClass = 'text-emerald-600'; }
            else if (diffMins < 60) { timeText = diffMins + 'd m'; timeClass = 'text-emerald-600'; }
            else if (diffMins < 1440) { timeText = Math.floor(diffMins/60) + 'h m'; timeClass = 'text-amber-600'; }
            else { timeText = Math.floor(diffMins/1440) + 'd'; timeClass = 'text-gray-500'; }
        }

        html += `<div class="search-result-item p-3 border-b border-gray-100 flex items-center gap-3 hover:bg-emerald-50 active:bg-emerald-100 w-full" 
            data-product='${JSON.stringify(product).replace(/'/g, "&#39;")}'
            onclick="selectQuickProduct(this)">
            ${imageHtml}
            <div class="flex-1 min-w-0">
                <div class="font-medium text-gray-800 text-base truncate">${product.jina}</div>
                <div class="flex flex-wrap gap-1 mt-0.5">
                    <span class="text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">${product.aina || '-'}</span>
                    ${product.barcode ? `<span class="text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full font-mono">#${product.barcode}</span>` : ''}
                    ${product.kipimo ? `<span class="text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">${product.kipimo}</span>` : ''}
                </div>
                <div class="flex items-center gap-1 mt-0.5">
                    <i class="fas fa-clock text-[10px] text-gray-400"></i>
                    <span class="text-[10px] ${timeClass}">${timeText}</span>
                </div>
            </div>
            <div class="text-right flex-shrink-0">
                <div class="text-sm font-bold ${stockStatus}">${parseFloat(product.idadi).toFixed(2)}</div>
                <div class="text-xs text-gray-500">${parseFloat(product.bei_kuuza).toLocaleString()} TZS</div>
            </div>
            <div class="text-gray-300 ml-1"><i class="fas fa-chevron-right text-sm"></i></div>
        </div>`;
    });

    resultsDropdown.innerHTML = html;
    resultsDropdown.classList.remove('hidden');
}

// ============================================
// SELECT PRODUCT
// ============================================

function selectQuickProduct(element) {
    try {
        const productData = JSON.parse(element.dataset.product);
        selectedProduct = productData;
        loadProductIntoQuickForm(productData);
        resultsDropdown.classList.add('hidden');
        searchInput.value = productData.jina;
        document.getElementById('mode-indicator').textContent = 'Kuhariri';
        searchInput.blur();
        setTimeout(() => { document.getElementById('quick-add-stock').focus(); }, 300);
    } catch (e) {
        showQuickToast('Hitilafu', 'error');
    }
}

function loadProductIntoQuickForm(product) {
    selectedProduct = product;

    emptyState.classList.add('hidden');
    notFoundState.classList.add('hidden');
    addProductArea.classList.add('hidden');
    productDisplay.classList.remove('hidden');

    document.getElementById('quick-product-id').value = product.id;
    document.getElementById('product-name-display').textContent = product.jina;
    document.getElementById('quick-jina').value = product.jina;
    document.getElementById('quick-aina').value = product.aina || '';
    document.getElementById('product-aina-display').textContent = product.aina || '-';
    document.getElementById('product-barcode-display').textContent = product.barcode ? '#' + product.barcode : '-';

    // Image
    const imgContainer = document.getElementById('product-image-container');
    if (product.image_url) {
        imgContainer.innerHTML = `<img src="${product.image_url}" class="h-14 w-14 object-cover rounded-lg">`;
    } else if (product.image_base64) {
        imgContainer.innerHTML = `<img src="${product.image_base64}" class="h-14 w-14 object-cover rounded-lg">`;
    } else {
        imgContainer.innerHTML = `<i class="fas fa-box text-gray-300 text-2xl"></i>`;
    }

    // Stock
    const currentStock = parseFloat(product.idadi) || 0;
    document.getElementById('quick-current-stock').textContent = currentStock.toFixed(2);
    updateStockStatus(currentStock);

    // Last Updated
    updateLastUpdatedDisplay(product.updated_at, product.updated_by);

    document.getElementById('quick-add-stock').value = '';
    document.getElementById('quick-bei-nunua').value = product.bei_nunua || '';
    document.getElementById('quick-bei-kuuza').value = product.bei_kuuza || '';
    document.getElementById('quick-expiry').value = product.expiry || '';

    updateProfitPreview();
    
    // Flash effect
    const card = document.querySelector('#product-display-area .bg-emerald-50');
    if (card) {
        card.classList.remove('updated-flash');
        void card.offsetWidth;
        card.classList.add('updated-flash');
    }
}

function updateLastUpdatedDisplay(updatedAt, updatedBy) {
    const el = document.getElementById('product-last-updated');
    
    if (updatedAt) {
        const date = new Date(updatedAt);
        const now = new Date();
        const diffMins = Math.floor((now - date) / 60000);
        let timeText;
        if (diffMins < 1) timeText = 'Hivi punde';
        else if (diffMins < 60) timeText = diffMins + 'd m';
        else if (diffMins < 1440) timeText = Math.floor(diffMins/60) + 'h m';
        else timeText = date.toLocaleDateString('sw-TZ', { day: '2-digit', month: 'short' });
        el.textContent = timeText;
    } else {
        el.textContent = 'Hivi punde';
    }
}

function updateStockStatus(stock) {
    const el = document.getElementById('quick-stock-status');
    if (stock <= 0) {
        el.textContent = 'Imeisha';
        el.className = 'text-xs font-medium px-2.5 py-1 rounded-full bg-red-100 text-red-700 block';
    } else if (stock < 10) {
        el.textContent = 'Inaisha';
        el.className = 'text-xs font-medium px-2.5 py-1 rounded-full bg-amber-100 text-amber-700 block';
    } else {
        el.textContent = 'Ipo';
        el.className = 'text-xs font-medium px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 block';
    }
}

function updateProfitPreview() {
    const buy = parseFloat(document.getElementById('quick-bei-nunua').value) || 0;
    const sell = parseFloat(document.getElementById('quick-bei-kuuza').value) || 0;
    const profit = sell - buy;
    document.getElementById('quick-profit-preview').textContent = profit.toFixed(0) + ' TZS';
    const el = document.getElementById('quick-profit-preview');
    el.className = 'text-sm font-bold ' + (profit < 0 ? 'text-red-700' : profit > 0 ? 'text-emerald-700' : 'text-gray-500');
}

function clearQuickForm() {
    selectedProduct = null;
    document.getElementById('quick-product-id').value = '';
    productDisplay.classList.add('hidden');
    addProductArea.classList.add('hidden');
    notFoundState.classList.add('hidden');
    emptyState.classList.remove('hidden');
    searchInput.value = '';
    document.getElementById('quick-form-errors').classList.add('hidden');
    resultsDropdown.classList.add('hidden');
    document.getElementById('mode-indicator').textContent = 'Tayari';
    setTimeout(() => { searchInput.focus(); }, 100);
}

// ============================================
// ADD NEW PRODUCT
// ============================================

function showAddProductForm() {
    resultsDropdown.classList.add('hidden');
    productDisplay.classList.add('hidden');
    emptyState.classList.add('hidden');
    notFoundState.classList.add('hidden');
    addProductArea.classList.remove('hidden');
    
    document.getElementById('add-search-term').value = currentSearchTerm;
    document.getElementById('add-jina').value = currentSearchTerm || '';
    document.getElementById('mode-indicator').textContent = 'Kuongeza';
    setTimeout(() => {
        document.getElementById('add-jina').focus();
        document.getElementById('add-jina').select();
    }, 300);
}

function cancelAddProduct() {
    addProductArea.classList.add('hidden');
    emptyState.classList.remove('hidden');
    document.getElementById('add-form-errors').classList.add('hidden');
    document.getElementById('add-new-form').reset();
    document.getElementById('mode-indicator').textContent = 'Tayari';
    setTimeout(() => { searchInput.focus(); }, 100);
}

// ============================================
// UPDATE PRODUCT (AJAX)
// ============================================

async function handleQuickUpdate(e) {
    e.preventDefault();
    if (updateInProgress) { showQuickToast('Inasubiri...', 'warning'); return; }

    const productId = document.getElementById('quick-product-id').value;
    if (!productId) { showQuickToast('Chagua bidhaa kwanza', 'error'); return; }

    const jina = document.getElementById('quick-jina').value.trim();
    const aina = document.getElementById('quick-aina').value.trim();
    const addStock = parseFloat(document.getElementById('quick-add-stock').value) || 0;
    const beiNunua = parseFloat(document.getElementById('quick-bei-nunua').value);
    const beiKuuza = parseFloat(document.getElementById('quick-bei-kuuza').value);
    const expiry = document.getElementById('quick-expiry').value;

    const errors = [];
    if (!jina) errors.push('Jina linahitajika');
    if (!aina) errors.push('Aina inahitajika');
    if (isNaN(beiNunua) || beiNunua < 0) errors.push('Bei nunua si sahihi');
    if (isNaN(beiKuuza) || beiKuuza < 0) errors.push('Bei kuuza si sahihi');
    if (addStock < 0) errors.push('Ongeza stock haiwezi kuwa hasi');
    if (beiNunua > beiKuuza && beiKuuza > 0) errors.push('Bei kuuza lazima iwe kubwa');

    if (errors.length > 0) {
        const errorDiv = document.getElementById('quick-form-errors');
        errorDiv.innerHTML = errors.map(e => `<div>• ${e}</div>`).join('');
        errorDiv.classList.remove('hidden');
        return;
    }

    document.getElementById('quick-form-errors').classList.add('hidden');

    const currentStock = parseFloat(document.getElementById('quick-current-stock').textContent) || 0;
    const idadi = addStock > 0 ? currentStock + addStock : null;

    const data = {
        jina: jina,
        aina: aina,
        bei_nunua: beiNunua,
        bei_kuuza: beiKuuza,
        expiry: expiry || null,
    };
    if (idadi !== null) { data.idadi = idadi; data.stock_update_type = 'add'; }

    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2 text-sm"></i> ...';
    updateInProgress = true;

    try {
        const response = await fetch(`/bidhaa/quick-update/${productId}`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            const newStock = result.data.idadi;
            document.getElementById('quick-current-stock').textContent = newStock.toFixed(2);
            updateStockStatus(newStock);
            document.getElementById('quick-add-stock').value = '';
            updateLastUpdatedDisplay(result.data.updated_at, result.data.updated_by);
            
            showQuickToast('✅ Imehifadhiwa!', 'success');
            document.getElementById('mode-indicator').textContent = 'Imehifadhiwa';
            
            // Flash effect
            const card = document.querySelector('#product-display-area .bg-emerald-50');
            if (card) { card.classList.remove('updated-flash'); void card.offsetWidth; card.classList.add('updated-flash'); }
            
            setTimeout(() => {
                clearQuickForm();
                searchInput.focus();
                document.getElementById('mode-indicator').textContent = 'Tayari';
            }, 500);
        } else {
            showQuickToast(result.message || 'Hitilafu', 'error');
        }
    } catch (error) {
        showQuickToast('Hitilafu ya mtandao', 'error');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        updateInProgress = false;
    }
}

// ============================================
// ADD NEW PRODUCT (AJAX)
// ============================================

async function handleAddNewProduct(e) {
    e.preventDefault();
    if (addInProgress) return;

    const jina = document.getElementById('add-jina').value.trim();
    const aina = document.getElementById('add-aina').value.trim();
    const kipimo = document.getElementById('add-kipimo').value.trim();
    const idadi = parseFloat(document.getElementById('add-idadi').value) || 0;
    const beiNunua = parseFloat(document.getElementById('add-bei-nunua').value);
    const beiKuuza = parseFloat(document.getElementById('add-bei-kuuza').value);
    const expiry = document.getElementById('add-expiry').value;

    const errors = [];
    if (!jina) errors.push('Jina linahitajika');
    if (!aina) errors.push('Aina inahitajika');
    if (isNaN(beiNunua) || beiNunua < 0) errors.push('Bei nunua si sahihi');
    if (isNaN(beiKuuza) || beiKuuza < 0) errors.push('Bei kuuza si sahihi');
    if (beiNunua > beiKuuza && beiKuuza > 0) errors.push('Bei kuuza lazima iwe kubwa');

    if (errors.length > 0) {
        const errorDiv = document.getElementById('add-form-errors');
        errorDiv.innerHTML = errors.map(e => `<div>• ${e}</div>`).join('');
        errorDiv.classList.remove('hidden');
        return;
    }

    document.getElementById('add-form-errors').classList.add('hidden');

    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2 text-sm"></i> ...';
    addInProgress = true;

    try {
        const response = await fetch('/bidhaa', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                jina, aina, kipimo: kipimo || null, idadi,
                bei_nunua: beiNunua, bei_kuuza: beiKuuza,
                expiry: expiry || null
            })
        });

        const result = await response.json();

        if (response.ok && result.success) {
            showQuickToast('✅ Bidhaa imeongezwa!', 'success');
            document.getElementById('mode-indicator').textContent = 'Imeongezwa';
            cancelAddProduct();
            setTimeout(() => {
                searchInput.focus();
                document.getElementById('mode-indicator').textContent = 'Tayari';
            }, 500);
        } else {
            const errorMsg = result.errors ? Object.values(result.errors)[0]?.[0] || 'Hitilafu' : (result.message || 'Hitilafu');
            showQuickToast(errorMsg, 'error');
        }
    } catch (error) {
        showQuickToast('Hitilafu ya mtandao', 'error');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        addInProgress = false;
    }
}

// ============================================
// TOAST
// ============================================

function showQuickToast(message, type = 'success') {
    const toast = document.getElementById('quick-toast');
    const toastMessage = document.getElementById('quick-toast-message');
    if (!toast || !toastMessage) return;
    
    toastMessage.textContent = message;
    const colors = {
        success: 'bg-emerald-600',
        error: 'bg-red-600',
        warning: 'bg-amber-600'
    };
    toast.className = `fixed bottom-4 left-1/2 transform -translate-x-1/2 ${colors[type] || colors.success} text-white px-4 py-2 rounded-lg shadow-lg flex items-center gap-2 z-50 transition-all duration-300 max-w-[90%] w-auto mx-4`;
    toast.classList.remove('hidden', 'translate-y-20', 'opacity-0');
    toast.classList.add('translate-y-0', 'opacity-100');
    
    clearTimeout(toast._timeout);
    toast._timeout = setTimeout(hideQuickToast, 2000);
}

function hideQuickToast() {
    const toast = document.getElementById('quick-toast');
    if (toast) {
        toast.classList.add('translate-y-20', 'opacity-0');
        toast.classList.remove('translate-y-0', 'opacity-100');
        setTimeout(() => toast.classList.add('hidden'), 300);
    }
}
</script>
@endsection