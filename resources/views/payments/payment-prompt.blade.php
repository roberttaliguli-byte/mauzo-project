{{-- resources/views/payments/payment-prompt.blade.php --}}
@extends('layouts.app')

@section('title', 'Malipo - Subiri Uthibitisho')

@section('page-title')
    <div class="text-xl md:text-3xl font-bold text-white px-2">
        Malipo Yameanzishwa
    </div>
@endsection

@section('content')
<div class="space-y-3 md:space-y-6 px-2 md:px-0">
    <!-- Header Card with Amber/Orange Gradient -->
    <div class="bg-gradient-to-r from-amber-500 to-orange-600 rounded-lg md:rounded-xl shadow-md overflow-hidden">
        <div class="px-3 md:px-6 py-3 md:py-5">
            <div class="flex items-start md:items-center space-x-2 md:space-x-4">
                <div class="w-8 h-8 md:w-12 md:h-12 bg-white/20 rounded-lg md:rounded-xl flex items-center justify-center backdrop-blur-sm flex-shrink-0">
                    <i class="fas fa-mobile-alt text-white text-sm md:text-xl"></i>
                </div>
                <div>
                    <h2 class="text-sm md:text-xl font-bold text-white mb-0.5 md:mb-1">Kamilisha Malipo Kwenye Simu Yako</h2>
                    <p class="text-amber-100 text-[10px] md:text-sm">Utaombwa kuingiza namba ya siri ili kukamilisha</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="bg-white rounded-lg md:rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <div class="p-4 md:p-8 text-center">
            <!-- Animated Phone Icon -->
            <div class="mb-4 md:mb-6 relative">
                <div class="w-16 h-16 md:w-24 md:h-24 bg-gradient-to-r from-amber-500 to-orange-600 rounded-full flex items-center justify-center mx-auto animate-pulse shadow-lg">
                    <i class="fas fa-mobile-alt text-white text-2xl md:text-4xl"></i>
                </div>
                <div class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-20 h-1 bg-gradient-to-r from-amber-500 to-orange-600 rounded-full animate-pulse opacity-50"></div>
            </div>

            <h3 class="text-base md:text-xl font-bold text-gray-800 mb-2">Malipo Yameanzishwa</h3>
            
            <p class="text-gray-600 text-xs md:text-sm mb-4 md:mb-6">
                Ombi la malipo limetumwa kwa namba yako ya simu:
            </p>

            <!-- Phone Number Display -->
            <div class="bg-gradient-to-r from-amber-500 to-orange-600 text-white px-4 md:px-6 py-3 md:py-4 rounded-lg inline-block mb-4 md:mb-6 shadow-md">
                <div class="flex items-center space-x-2 md:space-x-3">
                    <i class="fas fa-phone-alt text-white/80 text-xs md:text-sm"></i>
                    <span class="font-bold text-sm md:text-xl tracking-wider">{{ $payment->phone_number }}</span>
                </div>
            </div>

            <!-- Payment Details Card -->
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 md:p-5 mb-4 md:mb-6">
                <div class="grid grid-cols-2 gap-2 md:gap-4">
                    <div>
                        <p class="text-[8px] md:text-xs text-gray-500 mb-1">Kiasi</p>
                        <p class="font-bold text-gray-800 text-sm md:text-lg">{{ number_format($payment->amount) }}</p>
                        <p class="text-[8px] md:text-xs text-gray-400">TZS</p>
                    </div>
                    <div>
                        <p class="text-[8px] md:text-xs text-gray-500 mb-1">Rejea</p>
                        <p class="font-mono font-bold text-amber-600 text-xs md:text-base break-all">{{ $payment->transaction_reference }}</p>
                    </div>
                </div>
            </div>

            <!-- Instruction -->
            <div class="mb-4 md:mb-6">
                <div class="flex items-center justify-center space-x-2 text-gray-600 mb-2">
                    <i class="fas fa-info-circle text-amber-500 text-xs md:text-sm"></i>
                    <p class="text-xs md:text-sm">Tafadhali angalia simu yako na weka namba ya siri ili kukamilisha malipo.</p>
                </div>
                
                <!-- Loading Spinner -->
                <div class="flex items-center justify-center space-x-2 text-amber-600">
                    <i class="fas fa-spinner fa-spin text-xs md:text-sm"></i>
                    <span class="text-[8px] md:text-xs">Inasubiri uthibitisho wako...</span>
                </div>
            </div>

            <!-- Check Status Button -->
            <div class="mb-4 md:mb-6">
                <a href="{{ route('payment.status', ['reference' => $payment->transaction_reference]) }}" 
                   class="inline-block bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-semibold py-2.5 md:py-3 px-6 md:px-8 rounded-lg transition-all duration-200 transform hover:scale-105 hover:shadow-lg text-xs md:text-sm">
                    <i class="fas fa-sync-alt mr-2 text-xs md:text-sm"></i>
                    Angalia Hali ya Malipo
                </a>
            </div>

            <!-- Help Section -->
            <div class="border-t border-gray-200 pt-4 md:pt-6">
                <h4 class="font-semibold text-gray-800 text-xs md:text-sm mb-2 md:mb-3 flex items-center justify-center">
                    <i class="fas fa-question-circle text-amber-500 mr-1 text-xs md:text-sm"></i>
                    Unakumbana na changamoto?
                </h4>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-2 md:gap-4 text-left">
                    <div class="flex items-start space-x-1.5 md:space-x-2">
                        <i class="fas fa-check-circle text-amber-500 text-[8px] md:text-xs mt-0.5"></i>
                        <p class="text-[8px] md:text-xs text-gray-600">Hakikisha una salio la kutosha</p>
                    </div>
                    <div class="flex items-start space-x-1.5 md:space-x-2">
                        <i class="fas fa-check-circle text-amber-500 text-[8px] md:text-xs mt-0.5"></i>
                        <p class="text-[8px] md:text-xs text-gray-600">Angalia umeandika namba ya simu sahihi</p>
                    </div>
                    <div class="flex items-start space-x-1.5 md:space-x-2">
                        <i class="fas fa-check-circle text-amber-500 text-[8px] md:text-xs mt-0.5"></i>
                        <p class="text-[8px] md:text-xs text-gray-600">Subiri kidogo (dakika 1-2) ujumbe wa USSD</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Auto-refresh Notice -->
    <div class="text-center">
        <div class="inline-flex items-center space-x-2 bg-gray-100 rounded-full px-3 py-1.5 md:px-4 md:py-2">
            <i class="fas fa-sync-alt text-amber-500 text-[8px] md:text-xs fa-spin"></i>
            <span class="text-[8px] md:text-xs text-gray-600">Ukurasa utabadilishwa kiotomatiki baada ya sekunde 10</span>
        </div>
    </div>
</div>

<!-- Auto-refresh Script -->
<script>
// Auto-refresh status every 10 seconds
setTimeout(function() {
    window.location.reload();
}, 10000);

// Optional: Show countdown
let timeLeft = 10;
const countdownElement = document.createElement('span');
countdownElement.className = 'text-amber-600 font-bold ml-1';

setInterval(function() {
    timeLeft--;
    if (timeLeft > 0) {
        countdownElement.textContent = `(${timeLeft}s)`;
    }
}, 1000);
</script>

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
        button, a, [role="button"] {
            min-height: 44px;
            min-width: 44px;
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
        
        /* Better spacing for mobile */
        .space-y-4 > * + * {
            margin-top: 1rem;
        }
        
        /* Full width on mobile */
        .inline-block {
            width: 100%;
        }
        
        a.inline-block {
            width: 100%;
            text-align: center;
        }
    }
    
    /* Smooth transitions */
    .transition-all {
        transition: all 0.2s ease;
    }
    
    /* Desktop hover effects */
    @media (min-width: 768px) {
        a:hover {
            transform: scale(1.05);
            transition: transform 0.2s ease;
        }
        
        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
    }
    
    /* Animations */
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
            transform: scale(1);
        }
        50% {
            opacity: 0.8;
            transform: scale(1.05);
        }
    }
    
    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    
    @keyframes spin {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }
    
    .fa-spin {
        animation: spin 1s linear infinite;
    }
    
    /* Loading spinner animation */
    .fa-spinner {
        animation: spin 1s linear infinite;
    }
    
    /* Focus styles */
    a:focus {
        outline: none;
        ring: 2px solid #f59e0b;
        ring-offset: 2px;
    }
    
    /* Card hover effects */
    .hover-lift:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    
    /* Gradient text support */
    .text-gradient {
        background: linear-gradient(to right, #f59e0b, #ea580c);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    
    /* Break long words */
    .break-all {
        word-break: break-all;
    }
</style>
@endsection