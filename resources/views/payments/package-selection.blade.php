{{-- resources/views/payments/package-selection.blade.php --}}
@extends('layouts.app')

@section('title', 'Chagua Kifurushi - Malipo')

@section('page-title')
    <div class="text-xl md:text-3xl font-bold text-gray-800 px-2">
        Chagua Kifurushi Kinachokufaa
    </div>
@endsection

@section('content')
<div class="space-y-3 md:space-y-6 px-2 md:px-0">

    <!-- Main Content -->
    <div class="bg-white rounded-lg md:rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <!-- Company Info Alert -->
        <div class="mx-2 md:mx-5 mt-2 md:mt-5">
            <div class="bg-orange-100 border border-amber-200 rounded-lg p-2 md:p-4">
                <div class="flex items-start space-x-2 md:space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-6 h-6 md:w-8 md:h-8 bg-amber-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-building text-amber-600 text-xs md:text-sm"></i>
                        </div>
                    </div>
                    <div class="flex-1">
                        <p class="text-[10px] md:text-sm text-gray-800">
                            <span class="font-bold">{{ $company->company_name }}</span> 
                            <span class="text-gray-600">- Kifurushi chako kilimalizika tarehe</span>
                            <span class="font-bold text-amber-600 ml-1">
                                {{ $company->package_end ? \Carbon\Carbon::parse($company->package_end)->format('d M, Y') : 'N/A' }}
                            </span>
                        </p>
                        <p class="text-[9px] md:text-xs text-gray-600 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>
                            Chagua kifurushi kipya kuendelea na huduma
                        </p>
                    </div>
                </div>
            </div>
        </div>

        @if(session('error'))
        <div class="mx-2 md:mx-5 mt-2 md:mt-4">
            <div class="bg-red-50 border border-red-200 rounded-lg p-2 md:p-3">
                <div class="flex items-center space-x-2 md:space-x-3">
                    <i class="fas fa-exclamation-circle text-red-500 text-xs md:text-sm"></i>
                    <p class="text-red-700 text-[10px] md:text-sm">{{ session('error') }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Packages Grid -->
        <div class="p-3 md:p-6">
            <h2 class="text-xs md:text-base font-semibold text-gray-800 mb-3 md:mb-5 flex items-center">
                <div class="w-1 h-3 md:w-2 md:h-4 bg-gradient-to-b from-emerald-500 to-emerald-600 rounded-full mr-2"></div>
                Chagua Kifurushi Kinachokufaa
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 md:gap-5">
                @foreach($packages as $name => $details)
                <div class="group relative transform transition-all duration-300 hover:-translate-y-1">
                    <!-- Badge -->
                    @if($details['badge'])
                    <div class="absolute -top-2 -right-2 z-10">
                        <span class="bg-gradient-to-r from-amber-500 to-orange-500 text-white text-[8px] md:text-xs font-bold px-1.5 py-0.5 md:px-2 md:py-1 rounded-full shadow-lg">
                            {{ $details['badge'] }}
                        </span>
                    </div>
                    @endif

                    <!-- Package Card -->
                    <div class="h-full bg-white rounded-lg md:rounded-xl border-2 overflow-hidden transition-all duration-300 border-emerald-200 hover:border-emerald-400 hover:shadow-lg">
                        
                        <!-- Header -->
                        <div class="bg-gradient-to-r from-orange-600 to-amber-700 px-3 py-2 md:px-5 md:py-3">
                            <h3 class="text-sm md:text-lg font-bold text-white text-center">{{ $name }}</h3>
                        </div>

                        <!-- Price -->
                        <div class="px-3 py-2 md:px-5 md:py-4 text-center border-b border-gray-100">
                            <div class="flex items-center justify-center">
                                <span class="text-base md:text-2xl font-bold text-gray-800">
                                    {{ number_format($details['price']) }}
                                </span>
                                <span class="text-gray-500 text-[8px] md:text-xs ml-1">TZS</span>
                            </div>
                            <p class="text-[8px] md:text-xs text-gray-500 mt-1">Malipo ya mara moja</p>
                        </div>

                        <!-- Features -->
                        <div class="px-3 py-2 md:px-5 md:py-3 space-y-1.5 md:space-y-2">
                            <p class="text-[9px] md:text-xs text-gray-600 leading-relaxed">{{ $details['description'] }}</p>
                            
                            <div class="flex items-center text-gray-700">
                                <i class="fas fa-calendar-check text-emerald-600 mr-1.5 text-[8px] md:text-xs"></i>
                                <span class="text-[8px] md:text-xs font-medium">
                                    <strong>{{ $details['days'] }}</strong> Days of Access
                                </span>
                            </div>

                            <div class="flex items-center text-gray-700">
                                <i class="fas fa-check-circle text-emerald-600 mr-1.5 text-[8px] md:text-xs"></i>
                                <span class="text-[8px] md:text-xs">Full system features</span>
                            </div>

                            <div class="flex items-center text-gray-700">
                                <i class="fas fa-headset text-amber-600 mr-1.5 text-[8px] md:text-xs"></i>
                                <span class="text-[8px] md:text-xs">24/7 Support</span>
                            </div>
                        </div>

                        <!-- Action Button -->
                        <div class="px-3 py-2 md:px-5 md:py-4 bg-gray-50 border-t border-gray-100">
                            <form action="{{ route('payment.form') }}" method="POST">
                                @csrf
                                <input type="hidden" name="package" value="{{ $name }}">
                                <button type="submit" 
                                    class="w-full bg-gradient-to-r from-amber-600 to-orange-700 hover:from-amber-700 hover:to-orange-800 text-white font-semibold py-1.5 md:py-2.5 px-2 rounded-lg transition-all duration-200 transform hover:scale-105 hover:shadow-md text-[9px] md:text-xs flex items-center justify-center space-x-1.5">
                                    <i class="fas fa-shopping-cart text-[8px] md:text-xs"></i>
                                    <span>Chagua Kifurushi</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Price Comparison Table -->
            <div class="mt-6 md:mt-8 bg-gray-50 rounded-lg p-3 md:p-5">
                <h3 class="text-xs md:text-sm font-semibold text-gray-800 mb-3">Linganisha Vifurushi</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-[9px] md:text-xs">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-2">Kifurushi</th>
                                <th class="text-center py-2">Muda</th>
                                <th class="text-center py-2">Bei</th>
                                <th class="text-center py-2">Bei kwa Mwezi</th>
                                <th class="text-center py-2">Punguzo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b border-gray-200">
                                <td class="py-2">30 days</td>
                                <td class="text-center">Mwezi 1</td>
                                <td class="text-center font-medium">TZS 15,000</td>
                                <td class="text-center">TZS 15,000</td>
                                <td class="text-center">-</td>
                            </tr>
                            <tr class="border-b border-gray-200">
                                <td class="py-2">180 days</td>
                                <td class="text-center">Miezi 6</td>
                                <td class="text-center font-medium">TZS 75,000</td>
                                <td class="text-center">TZS 12,500</td>
                                <td class="text-center text-green-600">Punguzo TZS 15,000</td>
                            </tr>
                            <tr>
                                <td class="py-2">366 days</td>
                                <td class="text-center">Miezi 12</td>
                                <td class="text-center font-medium">TZS 150,000</td>
                                <td class="text-center">TZS 12,500</td>
                                <td class="text-center text-green-600">Punguzo TZS 30,000</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Support Link -->
            <div class="mt-3 md:mt-4 text-center">
                <p class="text-[8px] md:text-xs text-gray-500">
                    Una swali? 
                    <a href="#" class="text-emerald-600 hover:text-emerald-700 font-medium hover:underline">
                        Wasiliana na Msaada
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Styles -->
<style>
    /* Mobile optimizations */
    @media (max-width: 640px) {
        /* Better touch targets */
        .group:active {
            transform: scale(0.98);
            transition: transform 0.1s ease;
        }
        
        /* Remove hover effects on mobile */
        .group:hover .relative {
            transform: none !important;
        }
        
        .group:hover {
            transform: none !important;
        }
        
        button, [role="button"] {
            min-height: 44px;
            min-width: 44px;
        }
        
        /* Ensure text is readable */
        .text-\[8px\] {
            line-height: 1.2;
        }
        
        .text-\[9px\] {
            line-height: 1.3;
        }
        
        /* Better spacing for small screens */
        .gap-1 {
            gap: 0.25rem;
        }
        
        .space-y-1\.5 > * + * {
            margin-top: 0.375rem;
        }
    }
    
    /* Smooth transitions */
    .transition-all {
        transition: all 0.2s ease;
    }
    
    /* Desktop hover effects */
    @media (min-width: 768px) {
        .group:hover {
            transform: translateY(-4px);
            transition: transform 0.3s ease;
        }
        
        .group:hover .rounded-lg {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        button:hover {
            transform: scale(1.05);
            transition: transform 0.2s ease;
        }
    }
    
    /* Animation for notifications */
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translate(-50%, -20px);
        }
        to {
            opacity: 1;
            transform: translate(-50%, 0);
        }
    }
    
    .animate-slide-down {
        animation: slideDown 0.3s ease-out;
    }
    
    /* Progress bar animations */
    .rounded-full.bg-gradient-to-r {
        transition: width 1s ease-in-out;
    }
    
    /* Card hover effects */
    .hover-lift:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
</style>
@endsection