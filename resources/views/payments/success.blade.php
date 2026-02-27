@extends('layouts.app')

@section('title', 'Malipo Yamekamilika - Success')

@section('page-title')
    <div class="text-xl md:text-3xl font-bold text-white px-2">
        Malipo Yamekamilika Kwa Mafanikio
    </div>
@endsection

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4 py-8">
    <div class="max-w-xl w-full bg-white rounded-xl md:rounded-2xl shadow-xl border border-gray-100 p-6 md:p-10 text-center">
        <!-- Success Animation -->
        <div class="mb-6 md:mb-8 relative">
            <div class="w-20 h-20 md:w-28 md:h-28 bg-gradient-to-r from-green-400 to-green-500 rounded-full flex items-center justify-center mx-auto shadow-lg animate-bounce-slow">
                <i class="fas fa-check-circle text-white text-3xl md:text-5xl"></i>
            </div>
            <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 w-24 h-1 bg-gradient-to-r from-green-400 to-green-500 rounded-full animate-pulse opacity-50"></div>
        </div>

        <!-- Success Message -->
        <h1 class="text-xl md:text-3xl font-bold text-gray-800 mb-3">
            Imefanikiwa! 🎉
        </h1>
        
        <p class="text-gray-600 text-xs md:text-sm mb-6 md:mb-8">
            Malipo yako yamekamilika kwa mafanikio. Asante kwa kuchagua huduma zetu.
        </p>

        <!-- Payment Details Card -->
        <div class="bg-gradient-to-br from-green-50 to-emerald-50 border border-green-200 rounded-xl p-4 md:p-6 mb-6 md:mb-8 text-left">
            <h3 class="font-semibold text-gray-800 text-xs md:text-sm mb-3 md:mb-4 flex items-center">
                <div class="w-1 h-3 md:w-2 md:h-4 bg-gradient-to-b from-green-500 to-emerald-600 rounded-full mr-2"></div>
                Maelezo ya Malipo
            </h3>

            <div class="space-y-2 md:space-y-3">
                <!-- Transaction Reference -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between py-2 border-b border-green-100">
                    <span class="text-[10px] md:text-xs text-gray-600 mb-1 md:mb-0">
                        <i class="fas fa-hashtag text-green-500 mr-1"></i>
                        Namba ya Rejea:
                    </span>
                    <span class="font-mono font-bold text-gray-800 text-xs md:text-sm bg-white px-3 py-1.5 rounded-lg border border-green-200">
                        {{ $payment->transaction_reference }}
                    </span>
                </div>

                <!-- Amount -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between py-2 border-b border-green-100">
                    <span class="text-[10px] md:text-xs text-gray-600 mb-1 md:mb-0">
                        <i class="fas fa-coins text-green-500 mr-1"></i>
                        Kiasi Kilicholipwa:
                    </span>
                    <span class="font-bold text-green-600 text-sm md:text-lg">
                        {{ number_format($payment->amount, 0) }} TZS
                    </span>
                </div>

                <!-- Package Type (if available) -->
                @if(isset($payment->package_type))
                <div class="flex flex-col md:flex-row md:items-center md:justify-between py-2 border-b border-green-100">
                    <span class="text-[10px] md:text-xs text-gray-600 mb-1 md:mb-0">
                        <i class="fas fa-box-open text-green-500 mr-1"></i>
                        Kifurushi:
                    </span>
                    <span class="font-semibold text-gray-800 text-xs md:text-sm">
                        {{ $payment->package_type }}
                    </span>
                </div>
                @endif

                <!-- Payment Method (if available) -->
                @if(isset($payment->payment_method))
                <div class="flex flex-col md:flex-row md:items-center md:justify-between py-2 border-b border-green-100">
                    <span class="text-[10px] md:text-xs text-gray-600 mb-1 md:mb-0">
                        <i class="fas fa-credit-card text-green-500 mr-1"></i>
                        Njia ya Malipo:
                    </span>
                    <span class="font-semibold text-gray-800 text-xs md:text-sm">
                        @if($payment->payment_method == 'MIXBY_YAS')
                            ⚡ Mixx by Yas
                        @elseif($payment->payment_method == 'AIRTEL')
                            📱 Airtel Money
                        @elseif($payment->payment_method == 'VISA')
                            💳 Visa Card
                        @elseif($payment->payment_method == 'MASTERCARD')
                            💳 Mastercard
                        @else
                            {{ $payment->payment_method }}
                        @endif
                    </span>
                </div>
                @endif

                <!-- Phone Number (if available) -->
                @if(isset($payment->phone_number))
                <div class="flex flex-col md:flex-row md:items-center md:justify-between py-2 border-b border-green-100">
                    <span class="text-[10px] md:text-xs text-gray-600 mb-1 md:mb-0">
                        <i class="fas fa-phone-alt text-green-500 mr-1"></i>
                        Namba ya Simu:
                    </span>
                    <span class="font-semibold text-gray-800 text-xs md:text-sm">
                        {{ $payment->phone_number }}
                    </span>
                </div>
                @endif

                <!-- Date & Time -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between py-2">
                    <span class="text-[10px] md:text-xs text-gray-600 mb-1 md:mb-0">
                        <i class="fas fa-calendar-alt text-green-500 mr-1"></i>
                        Tarehe na Muda:
                    </span>
                    <span class="font-semibold text-gray-800 text-xs md:text-sm">
                        {{ $payment->created_at->format('d/m/Y H:i') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Status Badge -->
        <div class="mb-6 md:mb-8">
            <span class="inline-flex items-center px-3 py-1.5 md:px-4 md:py-2 bg-green-100 text-green-700 rounded-full text-xs md:text-sm font-medium">
                <i class="fas fa-check-circle mr-1.5 text-xs"></i>
                Hali: Imekamilika
            </span>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('dashboard') }}" 
               class="inline-flex items-center justify-center bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-semibold py-2.5 md:py-3 px-6 md:px-8 rounded-lg transition-all duration-200 transform hover:scale-105 hover:shadow-lg text-xs md:text-sm">
                <i class="fas fa-tachometer-alt mr-2"></i>
                Nenda kwenye Dashibodi
            </a>
            
            <a href="{{ route('payment.package.selection') }}" 
               class="inline-flex items-center justify-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2.5 md:py-3 px-6 md:px-8 rounded-lg transition-all duration-200 text-xs md:text-sm">
                <i class="fas fa-box-open mr-2"></i>
                Angalia Vifurushi
            </a>
        </div>

        <!-- Receipt Note -->
        <div class="mt-6 pt-4 border-t border-gray-200">
            <p class="text-[8px] md:text-xs text-gray-400">
                <i class="fas fa-receipt mr-1"></i>
                Risiti ya malipo imetumwa kwa barua pepe yako. Unaweza pia kuipakia kupitia dashibodi.
            </p>
        </div>
    </div>
</div>

<!-- Styles -->
<style>
    /* Custom animations */
    @keyframes bounce-slow {
        0%, 100% {
            transform: translateY(0);
            animation-timing-function: cubic-bezier(0.8, 0, 1, 1);
        }
        50% {
            transform: translateY(-10px);
            animation-timing-function: cubic-bezier(0, 0, 0.2, 1);
        }
    }

    .animate-bounce-slow {
        animation: bounce-slow 2s infinite;
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
        
        .hover\:scale-105:hover {
            transform: none;
        }
        
        .flex-col.sm\:flex-row {
            flex-direction: column;
        }
        
        .gap-3 > * + * {
            margin-top: 0.75rem;
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
    
    /* Loading spinner animation if needed */
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
    a:focus {
        outline: none;
        ring: 2px solid #10b981;
        ring-offset: 2px;
    }
    
    /* Card hover effect */
    .max-w-xl {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .max-w-xl:hover {
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
</style>
@endsection