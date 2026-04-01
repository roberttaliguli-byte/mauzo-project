{{-- resources/views/admin/sms/send-to-company.blade.php --}}
@extends('layouts.admin')
@section('page-title', 'Tuma SMS - ' . ($company->company_name ?? 'Kampuni'))
@section('page-subtitle', 'Tuma ujumbe wa SMS kwa mwenye kampuni')

@section('content')
<div class="container mx-auto px-3 sm:px-4 py-4 sm:py-6">
    
    <!-- Header with Back Button -->
    <div class="mb-5 sm:mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-paper-plane text-purple-600 text-lg sm:text-xl"></i>
                    <span>Tuma SMS kwa <span class="text-purple-600">{{ $company->company_name ?? 'Kampuni' }}</span></span>
                </h1>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">Tuma ujumbe wa moja kwa moja kwa mwenye kampuni</p>
            </div>
            <a href="{{ route('admin.sms.dashboard') }}" class="inline-flex items-center gap-2 text-purple-600 hover:text-purple-800 transition text-sm">
                <i class="fas fa-arrow-left text-xs"></i>
                <span>Rudi kwenye Dashboard</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 sm:gap-6">
        <!-- Send SMS Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-100 bg-gradient-to-r from-purple-50 to-transparent">
                    <h2 class="text-base sm:text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-envelope text-purple-600"></i>
                        <span>Andika Ujumbe</span>
                    </h2>
                </div>
                
                <form id="sms-form" method="POST" action="{{ route('admin.sms.send-to-company.post', $company->id) }}" class="p-4 sm:p-6">
                    @csrf
                    
                    <!-- Recipient Section -->
                    <div class="mb-5">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Namba za Kupokea</label>
                        <div class="bg-gray-50 rounded-lg border border-gray-200 p-3 sm:p-4">
                            <div class="flex items-start sm:items-center gap-2">
                                <input type="checkbox" name="send_to_owner" value="1" id="send_to_owner" 
                                       class="mt-1 sm:mt-0 rounded border-gray-300 text-purple-600 focus:ring-purple-500" checked
                                       {{ !$company->phone ? 'disabled' : '' }}>
                                <label for="send_to_owner" class="flex-1 cursor-pointer">
                                    <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-2">
                                        <span class="text-sm font-medium text-gray-700">Mwenye Kampuni:</span>
                                        <span class="text-sm font-mono {{ $company->phone ? 'text-gray-900' : 'text-red-500' }}">
                                            {{ $company->phone ?? 'Hakuna namba iliyosajiliwa' }}
                                        </span>
                                    </div>
                                </label>
                            </div>
                            @if($company->phone)
                            <p class="text-xs text-gray-500 mt-2 flex items-center gap-1">
                                <i class="fas fa-info-circle text-blue-400"></i>
                                Ujumbe utatumwa kwa namba ya mwenye kampuni
                            </p>
                            @else
                            <p class="text-xs text-red-500 mt-2 flex items-center gap-1">
                                <i class="fas fa-exclamation-triangle"></i>
                                Kampuni haina namba ya simu iliyosajiliwa. Wasiliana na kampuni kuongeza namba.
                            </p>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Message Section -->
                    <div class="mb-5">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Ujumbe <span class="text-red-500">*</span>
                        </label>
                        <textarea name="message" id="message" rows="5" required
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
                            <span class="text-xs text-gray-400">Upeo: 1600 herufi</span>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button type="button" id="submit-btn"
                            class="flex-1 bg-gradient-to-r from-purple-600 to-purple-700 text-white px-4 py-2.5 rounded-lg hover:from-purple-700 hover:to-purple-800 font-medium transition shadow-sm"
                            {{ !$company->phone ? 'disabled' : '' }}>
                            <i class="fas fa-paper-plane mr-2"></i> Tuma Ujumbe
                        </button>
                        <button type="button" onclick="clearForm()"
                            class="px-4 py-2.5 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition font-medium">
                            <i class="fas fa-eraser mr-2"></i> Safisha
                        </button>
                    </div>
                    @if(!$company->phone)
                    <p class="text-xs text-red-500 mt-3 text-center">
                        <i class="fas fa-ban mr-1"></i> Hutuma ujumbe kwa sababu kampuni haina namba ya simu
                    </p>
                    @endif
                </form>
            </div>
        </div>
        
        <!-- Company Info & Stats -->
        <div class="space-y-5">
            <!-- Company Info Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-4 sm:px-5 py-3 sm:py-4 border-b border-gray-100 bg-gradient-to-r from-purple-50 to-pink-50">
                    <h2 class="text-base sm:text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-building text-purple-600"></i>
                        <span>Taarifa za Kampuni</span>
                    </h2>
                </div>
                <div class="p-4 sm:p-5 space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-xs sm:text-sm text-gray-500">Jina la Kampuni</span>
                        <span class="text-xs sm:text-sm font-medium text-gray-900 text-right">{{ $company->company_name }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-xs sm:text-sm text-gray-500">Mwenye Kampuni</span>
                        <span class="text-xs sm:text-sm font-medium text-gray-900 text-right">{{ $company->owner_name ?? '--' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-xs sm:text-sm text-gray-500">Namba ya Simu</span>
                        <span class="text-xs sm:text-sm font-mono {{ $company->phone ? 'text-gray-900' : 'text-red-500' }} text-right">
                            {{ $company->phone ?? 'Hakuna' }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-xs sm:text-sm text-gray-500">Barua pepe</span>
                        <span class="text-xs sm:text-sm text-gray-900 text-right">{{ $company->email ?? '--' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-xs sm:text-sm text-gray-500">Pakiti</span>
                        <span class="text-xs sm:text-sm font-medium text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">
                            {{ $company->package ?? 'Free Trial' }}
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- SMS Stats Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-4 sm:px-5 py-3 sm:py-4 border-b border-gray-100">
                    <h2 class="text-base sm:text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-chart-line text-blue-600"></i>
                        <span>Takwimu za SMS</span>
                    </h2>
                </div>
                <div class="p-4 sm:p-5">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center p-3 bg-blue-50 rounded-lg">
                            <p class="text-xs text-gray-500 mb-1">Jumla ya SMS</p>
                            <p class="text-2xl sm:text-3xl font-bold text-blue-600">{{ number_format($totalSms ?? 0) }}</p>
                        </div>
                        <div class="text-center p-3 bg-green-50 rounded-lg">
                            <p class="text-xs text-gray-500 mb-1">SMS Mwezi Huu</p>
                            <p class="text-2xl sm:text-3xl font-bold text-green-600">{{ number_format($monthSms ?? 0) }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent SMS Preview -->
            @if(isset($recentSms) && $recentSms->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-4 sm:px-5 py-3 sm:py-4 border-b border-gray-100">
                    <h2 class="text-base sm:text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-history text-gray-500"></i>
                        <span>Ujumbe wa Hivi Punde</span>
                    </h2>
                </div>
                <div class="p-4 sm:p-5 space-y-2 max-h-48 overflow-y-auto">
                    @foreach($recentSms as $sms)
                    <div class="text-xs p-2 bg-gray-50 rounded border-l-2 {{ $sms->status == 'DELIVERED' ? 'border-green-500' : 'border-red-500' }}">
                        <div class="flex justify-between mb-1">
                            <span class="font-mono text-gray-500">{{ \Carbon\Carbon::parse($sms->sent_at)->format('d/m/Y H:i') }}</span>
                            <span class="text-{{ $sms->status == 'DELIVERED' ? 'green' : 'red' }}-600 text-xs">
                                {{ $sms->status }}
                            </span>
                        </div>
                        <p class="text-gray-600 line-clamp-2">{{ Str::limit($sms->message, 80) }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
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
            <p class="text-sm text-gray-600 text-center mb-4">
                Je, una uhakika unataka kutuma ujumbe huu kwa <span class="font-semibold text-purple-600">{{ $company->company_name }}</span>?
            </p>
            <div class="border-t border-gray-100 pt-4 mt-2">
                <div class="bg-gray-50 p-3 rounded-lg mb-4">
                    <p class="text-xs text-gray-500 mb-1">Muhtasari wa Ujumbe:</p>
                    <p id="modalMessagePreview" class="text-sm text-gray-700 line-clamp-3"></p>
                </div>
            </div>
            <div class="flex gap-3">
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
const form = document.getElementById('sms-form');

// Modal elements
const modal = document.getElementById('confirmModal');
const modalMessagePreview = document.getElementById('modalMessagePreview');
const confirmBtn = document.getElementById('confirmBtn');
const cancelBtn = document.getElementById('cancelBtn');

let pendingSubmit = false;

if (messageInput) {
    messageInput.addEventListener('input', function() {
        const length = this.value.length;
        charCount.textContent = length;
        const parts = Math.ceil(length / 153);
        partsCount.textContent = parts;
        
        if (length > 1400) {
            charCount.classList.add('text-red-500');
        } else {
            charCount.classList.remove('text-red-500');
        }
    });
}

function clearForm() {
    if (messageInput) {
        messageInput.value = '';
        charCount.textContent = '0';
        partsCount.textContent = '1';
        charCount.classList.remove('text-red-500');
    }
}

// Function to show modal
function showConfirmModal(messageText) {
    modalMessagePreview.textContent = messageText;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

// Function to hide modal
function hideModal() {
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// Function to send SMS
async function sendSMS() {
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Inatuma... Tafadhali subiri';
    
    try {
        const formData = new FormData(form);
        
        const response = await fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('✓ ' + data.message);
            clearForm();
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            alert('✗ ' + (data.message || 'Hitilafu ilitokea wakati wa kutuma'));
        }
    } catch (error) {
        alert('❌ Hitilafu: ' + error.message);
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-paper-plane mr-2"></i> Tuma Ujumbe';
        hideModal();
    }
}

// Handle send button click
if (submitBtn) {
    submitBtn.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Check if company has phone
        @if(!$company->phone)
        alert('❌ Haiwezi kutuma ujumbe kwa sababu kampuni haina namba ya simu');
        return;
        @endif
        
        const message = messageInput.value.trim();
        if (!message) {
            alert('⚠️ Tafadhali andika ujumbe');
            return;
        }
        
        // Show custom modal instead of browser confirm
        showConfirmModal(message);
    });
}

// Handle confirm button click
if (confirmBtn) {
    confirmBtn.addEventListener('click', sendSMS);
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
</script>
@endpush
@endsection