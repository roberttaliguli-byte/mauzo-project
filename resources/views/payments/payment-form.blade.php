{{-- resources/views/payments/payment-form.blade.php --}}
@extends('layouts.app')

@section('title', 'Kamilisha Malipo - Payment')

@section('page-title')
    <div class="text-xl md:text-3xl font-bold text-black px-2">
        Kamilisha Malipo Yako
    </div>
@endsection

@section('content')
<div class="space-y-3 md:space-y-6 px-2 md:px-0">


    <!-- Main Content -->
    <div class="bg-white rounded-lg md:rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <div class="p-3 md:p-6">
            <!-- Package Summary Card - Updated with Amber/Orange -->
            <div class="bg-gradient-to-r from-amber-500 to-orange-600 rounded-lg p-3 md:p-5 mb-4 md:mb-6 shadow-md">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 md:gap-0">
                    <div class="flex items-start space-x-2 md:space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 md:w-10 md:h-10 bg-white/20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                                <i class="fas fa-box-open text-white text-xs md:text-sm"></i>
                            </div>
                        </div>
                        <div>
                            <p class="text-[10px] md:text-xs text-amber-100">Kifurushi Kilichochaguliwa</p>
                            <p class="font-bold text-white text-sm md:text-lg">{{ $package }}</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-2 md:space-x-3 md:justify-end">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 md:w-10 md:h-10 bg-white/20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                                <i class="fas fa-coins text-white text-xs md:text-sm"></i>
                            </div>
                        </div>
                        <div>
                            <p class="text-[10px] md:text-xs text-amber-100">Kiasi cha Malipo</p>
                            <p class="font-bold text-white text-sm md:text-lg">{{ number_format($amount) }} TZS</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Form -->
            <form action="{{ route('payment.process') }}" method="POST" id="paymentForm">
                @csrf
                <input type="hidden" name="package" value="{{ $package }}">

                <div class="space-y-4 md:space-y-5">
                    <!-- Phone Number Input -->
                    <div>
                        <label for="phone_number" class="block text-[10px] md:text-sm font-semibold text-gray-700 mb-1 md:mb-2">
                            <i class="fas fa-phone-alt text-amber-600 mr-1 text-[8px] md:text-xs"></i>
                            Namba ya Simu ya Malipo
                        </label>
                        <input type="text" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 md:px-4 md:py-3 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-white text-sm @error('phone_number') border-red-500 @enderror" 
                               id="phone_number" 
                               name="phone_number" 
                               placeholder="Mfano: 0712345678 au 255712345678"
                               value="{{ old('phone_number') }}"
                               required>
                        <p class="text-[8px] md:text-xs text-gray-500 mt-1">
                            <i class="fas fa-info-circle mr-1 text-amber-600"></i>
                            Weka namba ya mixx by yas au Airtel
                        </p>
                        @error('phone_number')
                            <p class="text-[8px] md:text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Payment Method Selection - Updated with Mixx by Yas, Airtel, Visa & Mastercard -->
                    <div>
                        <label for="payment_method" class="block text-[10px] md:text-sm font-semibold text-gray-700 mb-1 md:mb-2">
                            <i class="fas fa-credit-card text-amber-600 mr-1 text-[8px] md:text-xs"></i>
                            Chagua Njia ya Malipo
                        </label>
                        <select class="w-full border border-gray-300 rounded-lg px-3 py-2 md:px-4 md:py-3 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-white text-sm @error('payment_method') border-red-500 @enderror" 
                                id="payment_method" 
                                name="payment_method" 
                                required>
                            <option value="">-- Chagua Njia ya Malipo --</option>
                            <option value="MIXBY_YAS" {{ old('payment_method') == 'MIXBY_YAS' ? 'selected' : '' }} class="py-1">
                                ⚡ Mixx by Yas
                            </option>
                            <option value="AIRTEL" {{ old('payment_method') == 'AIRTEL' ? 'selected' : '' }} class="py-1">
                                📱 Airtel Money
                            </option>
                            <option value="VISA" {{ old('payment_method') == 'VISA' ? 'selected' : '' }} class="py-1">
                                💳 Visa Card
                            </option>
                            <option value="MASTERCARD" {{ old('payment_method') == 'MASTERCARD' ? 'selected' : '' }} class="py-1">
                                💳 Mastercard
                            </option>
                        </select>
                        @error('payment_method')
                            <p class="text-[8px] md:text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Security Notice - Updated with Amber/Orange theme -->
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-2 md:p-4">
                        <div class="flex items-start space-x-2 md:space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-5 h-5 md:w-7 md:h-7 bg-amber-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-shield-alt text-amber-600 text-[8px] md:text-xs"></i>
                                </div>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 text-[10px] md:text-sm mb-0.5">Taarifa za Usalama</p>
                                <p class="text-[8px] md:text-xs text-gray-600">Hatuwezi kuhifadhi au kuona taarifa zako za malipo. Malipo yanachakatwa kwa njia salama kupitia PesaPal.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button - Updated with Amber/Orange -->
                    <button type="submit" class="w-full bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-bold py-3 md:py-4 px-4 rounded-lg transition-all duration-200 transform hover:scale-105 hover:shadow-lg text-sm md:text-base flex items-center justify-center space-x-2" id="submitBtn">
                        <i class="fas fa-lock text-xs md:text-sm"></i>
                        <span>Lipa {{ number_format($amount) }} TZS</span>
                    </button>

                    <!-- Back Link - Updated with Amber -->
                    <div class="text-center mt-3">
                        <a href="{{ route('payment.package.selection') }}" class="text-[8px] md:text-xs text-gray-500 hover:text-amber-600 transition-colors">
                            <i class="fas fa-arrow-left mr-1"></i>
                            Rudi kwenye uteuzi wa vifurushi
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Payment Methods Info - Updated with new payment methods -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 md:gap-4">
        <!-- Mixx by Yas -->
        <div class="bg-white rounded-lg p-2 md:p-4 shadow-sm border border-gray-100 hover:border-amber-300 transition-colors">
            <div class="flex flex-col items-center text-center space-y-1 md:space-y-2">
                <div class="w-8 h-8 md:w-12 md:h-12 bg-gradient-to-r from-purple-500 to-purple-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-bolt text-white text-xs md:text-lg"></i>
                </div>
                <div>
                    <p class="font-semibold text-gray-800 text-[10px] md:text-sm">Mixx by Yas</p>
                    <p class="text-[8px] md:text-xs text-gray-500">Haraka na rahisi</p>
                </div>
            </div>
        </div>

        <!-- Airtel Money -->
        <div class="bg-white rounded-lg p-2 md:p-4 shadow-sm border border-gray-100 hover:border-amber-300 transition-colors">
            <div class="flex flex-col items-center text-center space-y-1 md:space-y-2">
                <div class="w-8 h-8 md:w-12 md:h-12 bg-gradient-to-r from-red-500 to-red-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-mobile-alt text-white text-xs md:text-lg"></i>
                </div>
                <div>
                    <p class="font-semibold text-gray-800 text-[10px] md:text-sm">Airtel Money</p>
                    <p class="text-[8px] md:text-xs text-gray-500">Salama na imara</p>
                </div>
            </div>
        </div>

        <!-- Visa Card -->
        <div class="bg-white rounded-lg p-2 md:p-4 shadow-sm border border-gray-100 hover:border-amber-300 transition-colors">
            <div class="flex flex-col items-center text-center space-y-1 md:space-y-2">
                <div class="w-8 h-8 md:w-12 md:h-12 bg-gradient-to-r from-blue-600 to-blue-700 rounded-full flex items-center justify-center">
                    <i class="fab fa-cc-visa text-white text-xs md:text-lg"></i>
                </div>
                <div>
                    <p class="font-semibold text-gray-800 text-[10px] md:text-sm">Visa Card</p>
                    <p class="text-[8px] md:text-xs text-gray-500">Kadi za kimataifa</p>
                </div>
            </div>
        </div>

        <!-- Mastercard -->
        <div class="bg-white rounded-lg p-2 md:p-4 shadow-sm border border-gray-100 hover:border-amber-300 transition-colors">
            <div class="flex flex-col items-center text-center space-y-1 md:space-y-2">
                <div class="w-8 h-8 md:w-12 md:h-12 bg-gradient-to-r from-orange-500 to-orange-600 rounded-full flex items-center justify-center">
                    <i class="fab fa-cc-mastercard text-white text-xs md:text-lg"></i>
                </div>
                <div>
                    <p class="font-semibold text-gray-800 text-[10px] md:text-sm">Mastercard</p>
                    <p class="text-[8px] md:text-xs text-gray-500">Inakubaliwa duniani</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Info -->
    <div class="text-center">
        <p class="text-[8px] md:text-xs text-gray-500">
            <i class="fas fa-lock text-amber-500 mr-1"></i>
            Malipo yako yanalindwa kwa kiwango cha juu cha usalama
        </p>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('paymentForm');
    const submitBtn = document.getElementById('submitBtn');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            // Prevent double submission
            if (submitBtn.disabled) {
                e.preventDefault();
                return;
            }
            
            // Disable button and show loading
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <i class="fas fa-spinner fa-spin text-xs md:text-sm"></i>
                <span>Inachakata malipo yako...</span>
            `;
            
            // Add loading class
            submitBtn.classList.remove('hover:scale-105');
            submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
        });
    }
    
    // Phone number formatting
    const phoneInput = document.getElementById('phone_number');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            // Remove any non-numeric characters
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    }
});
</script>
@endpush

<!-- Styles -->
<style>
    /* Amber/Orange theme */
    .bg-gradient-to-r.from-amber-500.to-orange-600 {
        background: linear-gradient(to right, #f59e0b, #ea580c);
    }
    
    .bg-gradient-to-r.from-amber-500.to-orange-600:hover {
        background: linear-gradient(to right, #d97706, #c2410c);
    }
    
    /* Mobile optimizations */
    @media (max-width: 640px) {
        button, [role="button"] {
            min-height: 48px;
            min-width: 48px;
        }
        
        input, select {
            font-size: 16px !important; /* Prevent zoom on iOS */
            min-height: 44px;
        }
        
        .text-\[8px\] {
            line-height: 1.2;
        }
        
        .text-\[10px\] {
            line-height: 1.3;
        }
        
        .group:active {
            transform: scale(0.98);
            transition: transform 0.1s ease;
        }
        
        .hover\:scale-105:hover {
            transform: none;
        }
        
        .space-y-4 > * + * {
            margin-top: 1rem;
        }
        
        /* Better grid for mobile */
        .grid-cols-2 {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    /* Smooth transitions */
    .transition-all {
        transition: all 0.2s ease;
    }
    
    /* Desktop hover effects */
    @media (min-width: 768px) {
        button:hover:not(:disabled) {
            transform: scale(1.02);
            transition: transform 0.2s ease;
        }
        
        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        /* Payment method cards hover */
        .grid-cols-4 > div:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(245, 158, 11, 0.2);
            transition: all 0.3s ease;
        }
    }
    
    /* Loading spinner animation */
    @keyframes spin {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }
    
    .fa-spinner {
        animation: spin 1s linear infinite;
    }
    
    /* Focus styles for accessibility */
    input:focus, select:focus {
        outline: none;
        ring: 2px solid #f59e0b;
        ring-offset: 2px;
    }
    
    /* Disabled button styles */
    button:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }
    
    /* Card hover effects */
    .hover\:border-amber-300:hover {
        border-color: #fcd34d;
        transition: border-color 0.2s ease;
    }
</style>
@endsection