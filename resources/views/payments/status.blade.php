{{-- resources/views/payments/status.blade.php --}}
@extends('layouts.app')

@section('title', 'Hali ya Malipo')

@section('page-title')
    <div class="text-xl md:text-3xl font-bold text-white px-2">
        Hali ya Malipo Yako
    </div>
@endsection

@section('content')
<div class="space-y-3 md:space-y-6 px-2 md:px-0">
    <!-- Header Card with Amber/Orange Gradient -->
    <div class="bg-gradient-to-r from-amber-500 to-orange-600 rounded-lg md:rounded-xl shadow-md overflow-hidden">
        <div class="px-3 md:px-6 py-3 md:py-5">
            <div class="flex items-start md:items-center space-x-2 md:space-x-4">
                <div class="w-8 h-8 md:w-12 md:h-12 bg-white/20 rounded-lg md:rounded-xl flex items-center justify-center backdrop-blur-sm flex-shrink-0">
                    @if($payment->status == 'completed')
                        <i class="fas fa-check-circle text-white text-sm md:text-xl"></i>
                    @elseif($payment->status == 'pending' || $payment->status == 'processing')
                        <i class="fas fa-clock text-white text-sm md:text-xl"></i>
                    @elseif($payment->status == 'failed')
                        <i class="fas fa-exclamation-circle text-white text-sm md:text-xl"></i>
                    @else
                        <i class="fas fa-credit-card text-white text-sm md:text-xl"></i>
                    @endif
                </div>
                <div>
                    <h2 class="text-sm md:text-xl font-bold text-white mb-0.5 md:mb-1">
                        @if($payment->status == 'completed')
                            Malipo Yamekamilika
                        @elseif($payment->status == 'pending' || $payment->status == 'processing')
                            Malipo Yanasubiri Uthibitisho
                        @elseif($payment->status == 'failed')
                            Malipo Yameshindikana
                        @else
                            Hali ya Malipo
                        @endif
                    </h2>
                    <p class="text-amber-100 text-[10px] md:text-sm">
                        @if($payment->status == 'completed')
                            Umefanikiwa kulipa kifurushi chako
                        @elseif($payment->status == 'pending' || $payment->status == 'processing')
                            Tafadhali subiri wakati malipo yanachakatwa
                        @elseif($payment->status == 'failed')
                            Tumeshindwa kuchakata malipo yako
                        @else
                            Angalia hali ya malipo yako
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="bg-white rounded-lg md:rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <div class="p-4 md:p-8">
            <!-- Status Icon -->
            <div class="text-center mb-4 md:mb-6">
                @if($payment->status == 'completed')
                    <div class="w-20 h-20 md:w-28 md:h-28 bg-gradient-to-r from-green-400 to-green-500 rounded-full flex items-center justify-center mx-auto shadow-lg mb-3 md:mb-4">
                        <i class="fas fa-check-circle text-white text-3xl md:text-5xl"></i>
                    </div>
                    <h3 class="text-lg md:text-2xl font-bold text-green-600 mb-1">Imefanikiwa!</h3>
                    <p class="text-gray-600 text-xs md:text-sm">Malipo yako yamekamilika kwa mafanikio</p>
                @elseif($payment->status == 'pending')
                    <div class="w-20 h-20 md:w-28 md:h-28 bg-gradient-to-r from-amber-400 to-amber-500 rounded-full flex items-center justify-center mx-auto shadow-lg mb-3 md:mb-4 animate-pulse">
                        <i class="fas fa-clock text-white text-3xl md:text-5xl"></i>
                    </div>
                    <h3 class="text-lg md:text-2xl font-bold text-amber-600 mb-1">Inasubiri</h3>
                    <p class="text-gray-600 text-xs md:text-sm">Malipo yako yanasubiri kuchakatwa</p>
                @elseif($payment->status == 'processing')
                    <div class="w-20 h-20 md:w-28 md:h-28 bg-gradient-to-r from-blue-400 to-blue-500 rounded-full flex items-center justify-center mx-auto shadow-lg mb-3 md:mb-4">
                        <i class="fas fa-sync-alt fa-spin text-white text-3xl md:text-5xl"></i>
                    </div>
                    <h3 class="text-lg md:text-2xl font-bold text-blue-600 mb-1">Inachakatwa</h3>
                    <p class="text-gray-600 text-xs md:text-sm">Tafadhali subiri malipo yanachakatwa</p>
                @elseif($payment->status == 'failed')
                    <div class="w-20 h-20 md:w-28 md:h-28 bg-gradient-to-r from-red-400 to-red-500 rounded-full flex items-center justify-center mx-auto shadow-lg mb-3 md:mb-4">
                        <i class="fas fa-exclamation-circle text-white text-3xl md:text-5xl"></i>
                    </div>
                    <h3 class="text-lg md:text-2xl font-bold text-red-600 mb-1">Imeshindikana</h3>
                    <p class="text-gray-600 text-xs md:text-sm">Malipo yameshindikana, tafadhali jaribu tena</p>
                @else
                    <div class="w-20 h-20 md:w-28 md:h-28 bg-gradient-to-r from-gray-400 to-gray-500 rounded-full flex items-center justify-center mx-auto shadow-lg mb-3 md:mb-4">
                        <i class="fas fa-credit-card text-white text-3xl md:text-5xl"></i>
                    </div>
                    <h3 class="text-lg md:text-2xl font-bold text-gray-600 mb-1">{{ ucfirst($payment->status) }}</h3>
                @endif
            </div>

            <!-- Payment Details Card -->
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 md:p-6 mb-4 md:mb-6">
                <h4 class="font-semibold text-gray-800 text-xs md:text-sm mb-3 md:mb-4 flex items-center">
                    <div class="w-1 h-3 md:w-2 md:h-4 bg-gradient-to-b from-amber-500 to-orange-600 rounded-full mr-2"></div>
                    Maelezo ya Malipo
                </h4>

                <div class="space-y-2 md:space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] md:text-xs text-gray-600">Rejea:</span>
                        <span class="font-mono font-bold text-gray-800 text-[10px] md:text-sm">{{ $payment->transaction_reference }}</span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-[10px] md:text-xs text-gray-600">Kifurushi:</span>
                        <span class="font-semibold text-gray-800 text-[10px] md:text-sm">{{ $payment->package_type }}</span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-[10px] md:text-xs text-gray-600">Kiasi:</span>
                        <span class="font-bold text-amber-600 text-xs md:text-lg">{{ number_format($payment->amount) }} TZS</span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-[10px] md:text-xs text-gray-600">Namba ya Simu:</span>
                        <span class="font-semibold text-gray-800 text-[10px] md:text-sm">{{ $payment->phone_number ?? 'N/A' }}</span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-[10px] md:text-xs text-gray-600">Tarehe:</span>
                        <span class="text-gray-800 text-[10px] md:text-sm">{{ $payment->created_at->format('d/m/Y H:i') }}</span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-[10px] md:text-xs text-gray-600">Hali:</span>
                        @if($payment->status == 'completed')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[8px] md:text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i> Imekamilika
                            </span>
                        @elseif($payment->status == 'pending')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[8px] md:text-xs font-medium bg-amber-100 text-amber-800">
                                <i class="fas fa-clock mr-1"></i> Inasubiri
                            </span>
                        @elseif($payment->status == 'processing')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[8px] md:text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-sync-alt mr-1 fa-spin"></i> Inachakatwa
                            </span>
                        @elseif($payment->status == 'failed')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[8px] md:text-xs font-medium bg-red-100 text-red-800">
                                <i class="fas fa-exclamation-circle mr-1"></i> Imeshindikana
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[8px] md:text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $payment->status }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col md:flex-row gap-2 md:gap-3 justify-center">
                @if($payment->status == 'completed')
                    <a href="{{ route('dashboard') }}" class="bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white font-semibold py-2.5 md:py-3 px-4 md:px-6 rounded-lg transition-all duration-200 transform hover:scale-105 hover:shadow-lg text-xs md:text-sm flex items-center justify-center">
                        <i class="fas fa-tachometer-alt mr-2"></i>
                        Nenda kwenye Dashibodi
                    </a>
                @elseif($payment->status == 'pending' || $payment->status == 'processing')
                    <a href="{{ route('payment.status', ['reference' => $payment->transaction_reference]) }}" class="bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-semibold py-2.5 md:py-3 px-4 md:px-6 rounded-lg transition-all duration-200 transform hover:scale-105 hover:shadow-lg text-xs md:text-sm flex items-center justify-center">
                        <i class="fas fa-sync-alt mr-2 fa-spin"></i>
                        Angalia Tena
                    </a>
                    <a href="{{ route('payment.package.selection') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2.5 md:py-3 px-4 md:px-6 rounded-lg transition-all duration-200 text-xs md:text-sm flex items-center justify-center">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Rudi kwenye Vifurushi
                    </a>
                @elseif($payment->status == 'failed')
                    <a href="{{ route('payment.retry', ['id' => $payment->id]) }}" class="bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-semibold py-2.5 md:py-3 px-4 md:px-6 rounded-lg transition-all duration-200 transform hover:scale-105 hover:shadow-lg text-xs md:text-sm flex items-center justify-center">
                        <i class="fas fa-redo-alt mr-2"></i>
                        Jaribu Tena
                    </a>
                    <a href="{{ route('payment.package.selection') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2.5 md:py-3 px-4 md:px-6 rounded-lg transition-all duration-200 text-xs md:text-sm flex items-center justify-center">
                        <i class="fas fa-box-open mr-2"></i>
                        Chagua Kifurushi Kingine
                    </a>
                @else
                    <a href="{{ route('payment.package.selection') }}" class="bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-semibold py-2.5 md:py-3 px-4 md:px-6 rounded-lg transition-all duration-200 text-xs md:text-sm flex items-center justify-center">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Rudi
                    </a>
                @endif
            </div>

            <!-- Help Section for Pending/Failed -->
            @if($payment->status == 'pending' || $payment->status == 'processing')
            <div class="mt-4 md:mt-6 bg-blue-50 border border-blue-200 rounded-lg p-3 md:p-4">
                <div class="flex items-start space-x-2">
                    <i class="fas fa-info-circle text-blue-500 text-xs md:text-sm mt-0.5"></i>
                    <div>
                        <p class="font-semibold text-gray-800 text-[10px] md:text-sm mb-1">Malipo yanaweza kuchukua muda kidogo</p>
                        <p class="text-[8px] md:text-xs text-gray-600">Kawaida malipo huchakatwa ndani ya dakika 1-2. Ukisubiri zaidi ya dakika 5, tafadhali wasiliana na msaada.</p>
                    </div>
                </div>
            </div>
            @endif

            @if($payment->status == 'failed')
            <div class="mt-4 md:mt-6 bg-red-50 border border-red-200 rounded-lg p-3 md:p-4">
                <div class="flex items-start space-x-2">
                    <i class="fas fa-exclamation-triangle text-red-500 text-xs md:text-sm mt-0.5"></i>
                    <div>
                        <p class="font-semibold text-gray-800 text-[10px] md:text-sm mb-1">Sababu za kushindikana:</p>
                        <ul class="list-disc list-inside text-[8px] md:text-xs text-gray-600 space-y-1">
                            <li>Salio lisilotosha kwenye simu yako</li>
                            <li>Namba ya simu isiyo sahihi</li>
                            <li>Muda wa kuingiza namba ya siri umeisha</li>
                            <li>Hitilafu ya mtandao</li>
                        </ul>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Auto-refresh for pending/processing -->
    @if($payment->status == 'pending' || $payment->status == 'processing')
    <div class="text-center">
        <div class="inline-flex items-center space-x-2 bg-gray-100 rounded-full px-3 py-1.5 md:px-4 md:py-2">
            <i class="fas fa-sync-alt text-amber-500 text-[8px] md:text-xs fa-spin"></i>
            <span class="text-[8px] md:text-xs text-gray-600">Inafanya upya kiotomatiki...</span>
        </div>
    </div>

    <script>
        // Auto-refresh status every 10 seconds for pending/processing payments
        setTimeout(function() {
            window.location.reload();
        }, 10000);
    </script>
    @endif
</div>

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
        
        .inline-block {
            width: 100%;
        }
        
        a.inline-block {
            width: 100%;
            text-align: center;
        }
        
        .flex-col {
            flex-direction: column;
        }
        
        .gap-2 > * + * {
            margin-top: 0.5rem;
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
    
    /* List styles */
    .list-inside {
        list-style-position: inside;
    }
    
    .list-disc {
        list-style-type: disc;
    }
    
    /* Focus styles */
    a:focus {
        outline: none;
        ring: 2px solid #f59e0b;
        ring-offset: 2px;
    }
</style>
@endsection