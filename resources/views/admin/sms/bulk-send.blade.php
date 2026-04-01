{{-- resources/views/admin/sms/bulk-send.blade.php --}}
@extends('layouts.admin')
@section('page-title', 'Sms - makampuni mengi')
@section('page-subtitle', 'Tuma ujumbe kwa makampuni mengi')

@section('content')
<div class="container mx-auto px-3 sm:px-4 py-4 sm:py-6">
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 sm:p-6">
            <form id="bulk-sms-form">
                @csrf
                
                <!-- Companies Selection -->
                <div class="mb-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-3">
                        <label class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                            <i class="fas fa-building text-purple-500"></i>
                            <span>Chagua Makampuni <span class="text-red-500">*</span></span>
                        </label>
                        <div class="flex gap-3 text-xs">
                            <a href="{{ route('admin.sms.dashboard') }}" class="inline-flex items-center gap-2 text-purple-600 hover:text-purple-800 transition text-sm">
                                <i class="fas fa-arrow-left text-xs"></i>
                                <span>Rudi kwenye Dashboard</span>
                            </a>
                            <button type="button" onclick="selectAll()" class="text-blue-600 hover:text-blue-800 font-medium">
                                <i class="fas fa-check-square mr-1"></i> Chagua Zote
                            </button>
                            <button type="button" onclick="deselectAll()" class="text-gray-500 hover:text-gray-700 font-medium">
                                <i class="fas fa-square mr-1"></i> Ondoa Zote
                            </button>
                        </div>
                    </div>
                    
                    <div class="border border-gray-200 rounded-lg bg-gray-50 max-h-80 overflow-y-auto">
                        <div class="p-3 space-y-2">
                            @forelse($companies ?? [] as $company)
                            <div class="flex items-start sm:items-center p-2 hover:bg-white rounded-lg transition group">
                                <input type="checkbox" name="company_ids[]" value="{{ $company->id }}" 
                                       id="company_{{ $company->id }}" 
                                       class="company-checkbox mt-1 sm:mt-0 mr-3 rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                <label for="company_{{ $company->id }}" class="flex-1 cursor-pointer">
                                    <div class="font-medium text-gray-800 text-sm sm:text-base">{{ $company->company_name }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5">
                                        <i class="fas fa-phone-alt mr-1"></i>
                                        {{ $company->phone ?? 'Hakuna namba ya simu' }}
                                    </div>
                                </label>
                            </div>
                            @empty
                            <div class="text-center py-8 text-gray-400">
                                <i class="fas fa-building text-3xl mb-2 opacity-30"></i>
                                <p class="text-sm">Hakuna makampuni yaliyopatikana</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                    <div class="mt-2 text-xs text-gray-500" id="selected-count">
                        <i class="fas fa-check-circle mr-1"></i>
                        <span>0</span> kampuni zimechaguliwa
                    </div>
                </div>
                
                <!-- Message Input -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-2 text-purple-500"></i>
                        Ujumbe <span class="text-red-500">*</span>
                    </label>
                    <textarea id="message" rows="5" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-none"
                        placeholder="Andika ujumbe wako hapa..."></textarea>
                    <div class="flex justify-between items-center mt-2 text-xs text-gray-500">
                        <div class="flex items-center gap-3">
                            <span>
                                <i class="fas fa-font mr-1"></i>
                                <span id="char-count">0</span> herufi
                            </span>
                            <span class="hidden sm:inline">|</span>
                            <span class="hidden sm:inline">
                                <i class="fas fa-layer-group mr-1"></i>
                                Sehemu: <span id="parts-count">1</span>
                            </span>
                        </div>
                        <div class="text-right">
                            <span class="text-xs text-gray-400">Upeo: 1600 herufi</span>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <button type="button" id="submit-btn"
                        class="flex-1 bg-gradient-to-r from-purple-600 to-purple-700 text-white px-4 py-2.5 rounded-lg hover:from-purple-700 hover:to-purple-800 font-medium transition shadow-sm">
                        <i class="fas fa-paper-plane mr-2"></i> Tuma kwa Makampuni Yaliyochaguliwa
                    </button>
                    <button type="button" onclick="clearForm()"
                        class="px-4 py-2.5 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition font-medium">
                        <i class="fas fa-eraser mr-2"></i> Safisha
                    </button>
                </div>
            </form>
            
            <!-- Progress Bar -->
            <div id="progress" class="mt-6 hidden">
                <div class="bg-gray-100 rounded-full h-2 overflow-hidden">
                    <div id="progress-bar" class="bg-gradient-to-r from-green-500 to-green-600 h-2 transition-all duration-300 rounded-full" style="width: 0%"></div>
                </div>
                <p id="progress-text" class="text-xs text-gray-600 mt-2 text-center"></p>
            </div>
            
            <!-- Results Section -->
            <div id="results" class="mt-6 hidden">
                <div class="border-t border-gray-100 pt-4">
                    <h3 class="font-semibold text-gray-800 mb-3 text-sm flex items-center gap-2">
                        <i class="fas fa-chart-line text-purple-500"></i>
                        Matokeo ya Kutuma
                    </h3>
                    <div id="results-list" class="max-h-80 overflow-y-auto space-y-2"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Confirmation Modal -->
<div id="confirmModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4 transform transition-all">
        <div class="p-6">
            <div class="flex items-center justify-center mb-4">
                <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-2xl"></i>
                </div>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 text-center mb-2">Thibitisha Kutuma Ujumbe</h3>
            <p class="text-sm text-gray-600 text-center mb-4" id="modalConfirmText">
                Je, una uhakika unataka kutuma ujumbe kwa kampuni <span id="selectedCountDisplay" class="font-semibold text-purple-600">0</span>?
            </p>
            <div class="border-t border-gray-100 pt-4 mt-2">
                <div class="bg-gray-50 p-3 rounded-lg mb-4">
                    <p class="text-xs text-gray-500 mb-1">Muhtasari wa Ujumbe:</p>
                    <p id="modalMessagePreview" class="text-sm text-gray-700 line-clamp-3"></p>
                </div>
                <div class="bg-purple-50 p-2 rounded-lg">
                    <p class="text-xs text-purple-600 flex items-center gap-1">
                        <i class="fas fa-building"></i>
                        <span>Idadi ya makampuni: <span id="modalCompanyCount" class="font-bold">0</span></span>
                    </p>
                </div>
            </div>
            <div class="flex gap-3 mt-4">
                <button id="cancelBtn" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition font-medium">
                    Ghairi
                </button>
                <button id="confirmBtn" class="flex-1 bg-gradient-to-r from-purple-600 to-purple-700 text-white px-4 py-2 rounded-lg hover:from-purple-700 hover:to-purple-800 transition font-medium">
                    Thibitisha
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const messageInput = document.getElementById('message');
const charCount = document.getElementById('char-count');
const partsCount = document.getElementById('parts-count');
const submitBtn = document.getElementById('submit-btn');
const form = document.getElementById('bulk-sms-form');
const selectedCountSpan = document.getElementById('selected-count').querySelector('span');

// Modal elements
const modal = document.getElementById('confirmModal');
const modalConfirmText = document.getElementById('modalConfirmText');
const modalMessagePreview = document.getElementById('modalMessagePreview');
const modalCompanyCount = document.getElementById('modalCompanyCount');
const selectedCountDisplay = document.getElementById('selectedCountDisplay');
const confirmBtn = document.getElementById('confirmBtn');
const cancelBtn = document.getElementById('cancelBtn');

let pendingSelectedCount = 0;

// Update character count
messageInput.addEventListener('input', function() {
    const length = this.value.length;
    charCount.textContent = length;
    const parts = Math.ceil(length / 153);
    partsCount.textContent = parts;
    
    // Warning when approaching limit
    if (length > 1400) {
        charCount.classList.add('text-red-500');
    } else {
        charCount.classList.remove('text-red-500');
    }
});

// Update selected companies count
function updateSelectedCount() {
    const selected = document.querySelectorAll('.company-checkbox:checked').length;
    selectedCountSpan.textContent = selected;
    pendingSelectedCount = selected;
}

document.querySelectorAll('.company-checkbox').forEach(cb => {
    cb.addEventListener('change', updateSelectedCount);
});

function selectAll() {
    document.querySelectorAll('.company-checkbox').forEach(cb => cb.checked = true);
    updateSelectedCount();
}

function deselectAll() {
    document.querySelectorAll('.company-checkbox').forEach(cb => cb.checked = false);
    updateSelectedCount();
}

function clearForm() {
    messageInput.value = '';
    charCount.textContent = '0';
    partsCount.textContent = '1';
    deselectAll();
    document.getElementById('results').classList.add('hidden');
    document.getElementById('progress').classList.add('hidden');
    charCount.classList.remove('text-red-500');
}

// Function to show modal
function showConfirmModal() {
    const selected = document.querySelectorAll('.company-checkbox:checked').length;
    const message = messageInput.value.trim();
    
    modalMessagePreview.textContent = message;
    modalCompanyCount.textContent = selected;
    selectedCountDisplay.textContent = selected;
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

// Function to hide modal
function hideModal() {
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// Function to send bulk SMS
async function sendBulkSMS() {
    const selected = document.querySelectorAll('.company-checkbox:checked');
    const message = messageInput.value.trim();
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Inatuma... Tafadhali subiri';
    
    // Show progress
    const progressDiv = document.getElementById('progress');
    const progressBar = document.getElementById('progress-bar');
    const progressText = document.getElementById('progress-text');
    progressDiv.classList.remove('hidden');
    progressBar.style.width = '0%';
    progressText.textContent = 'Inaanza kutuma...';
    
    const formData = new FormData();
    selected.forEach(cb => {
        formData.append('company_ids[]', cb.value);
    });
    formData.append('message', message);
    
    try {
        const response = await fetch('{{ route("admin.sms.bulk.post") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const data = await response.json();
        
        // Update progress to 100%
        progressBar.style.width = '100%';
        progressText.textContent = 'Ujumbe umetumwa kikamilifu!';
        
        // Show results
        if (data.details && data.details.length) {
            const resultsDiv = document.getElementById('results');
            const resultsList = document.getElementById('results-list');
            resultsList.innerHTML = '';
            
            let successCount = 0;
            let failCount = 0;
            
            data.details.forEach(detail => {
                const success = detail.success;
                if (success) successCount++;
                else failCount++;
                
                const resultItem = document.createElement('div');
                resultItem.className = `p-2 rounded-lg text-xs sm:text-sm ${success ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'}`;
                resultItem.innerHTML = `
                    <div class="flex items-start gap-2">
                        <i class="fas ${success ? 'fa-check-circle' : 'fa-times-circle'} mt-0.5"></i>
                        <div>
                            <span class="font-medium">${escapeHtml(detail.company)}</span>
                            <span class="text-xs opacity-75 block">${escapeHtml(detail.message)}</span>
                        </div>
                    </div>
                `;
                resultsList.appendChild(resultItem);
            });
            
            progressText.innerHTML = `<i class="fas fa-chart-line mr-1"></i> Imefanikiwa: ${successCount} | Imeshindwa: ${failCount}`;
            resultsDiv.classList.remove('hidden');
        }
        
        if (data.success) {
            setTimeout(() => {
                // Show success alert and ask to clear form
                const shouldClear = confirm('✓ ' + data.message + '\n\nJe, unataka kusafisha fomu na kuanza tena?');
                if (shouldClear) {
                    clearForm();
                }
            }, 500);
        } else {
            alert('✗ ' + data.message);
        }
    } catch (error) {
        alert('❌ Hitilafu: ' + error.message);
        progressDiv.classList.add('hidden');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-paper-plane mr-2"></i> Tuma kwa Makampuni Yaliyochaguliwa';
        hideModal();
    }
}

// Helper function to escape HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Handle send button click
if (submitBtn) {
    submitBtn.addEventListener('click', function(e) {
        e.preventDefault();
        
        const selected = document.querySelectorAll('.company-checkbox:checked');
        if (selected.length === 0) {
            alert('⚠️ Tafadhali chagua angalau kampuni moja');
            return;
        }
        
        if (!messageInput.value.trim()) {
            alert('⚠️ Tafadhali andika ujumbe');
            return;
        }
        
        // Show custom modal instead of browser confirm
        showConfirmModal();
    });
}

// Handle confirm button click
if (confirmBtn) {
    confirmBtn.addEventListener('click', sendBulkSMS);
}

// Handle cancel button click
if (cancelBtn) {
    cancelBtn.addEventListener('click', hideModal);
}

// Close modal when clicking outside
if (modal) {
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            hideModal();
        }
    });
}

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
        hideModal();
    }
});

// Initialize
updateSelectedCount();
</script>
@endpush
@endsection