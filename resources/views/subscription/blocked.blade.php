@extends('layouts.app')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4 py-12 bg-gradient-to-br from-white to-emerald-50">
    <div class="w-full max-w-lg">
        <div class="bg-white rounded-2xl shadow-xl border border-emerald-100 p-8 md:p-10 text-center">
            
            <!-- Status Icon -->
            <div class="mb-8">
                @if($status === 'trial')
                    <div class="w-20 h-20 bg-gradient-to-br from-amber-100 to-emerald-100 rounded-full flex items-center justify-center mx-auto mb-5 p-4 ring-4 ring-amber-50">
                        <svg class="w-10 h-10 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                @elseif($status === 'expired')
                    <div class="w-20 h-20 bg-gradient-to-br from-rose-100 to-emerald-100 rounded-full flex items-center justify-center mx-auto mb-5 p-4 ring-4 ring-rose-50">
                        <svg class="w-10 h-10 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.196 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                @else
                    <div class="w-20 h-20 bg-gradient-to-br from-emerald-100 to-emerald-50 rounded-full flex items-center justify-center mx-auto mb-5 p-4 ring-4 ring-emerald-50">
                        <svg class="w-10 h-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                @endif
            </div>

            <!-- Status Message -->
            <div class="mb-10">
                <h1 class="text-3xl font-bold text-gray-900 mb-4">
                    @if($status === 'trial')
                        Muda wa Majaribio Umekwisha
                    @elseif($status === 'expired')
                        Kifurushi Chamekoma
                    @else
                        Hakuna Kifurushi Kinachotumika
                    @endif
                </h1>
                
                <div class="space-y-3">
                    <p class="text-gray-600 text-lg leading-relaxed">
                        @if($status === 'trial')
                            Muda wako wa bure wa majaribio umekwisha. Sasa unahitaji kuchagua kifurushi cha kudumu.
                        @elseif($status === 'expired')
                            Kifurushi chako cha sasa kimeisha muda wake. Tafadhali rejesha ili kuendelea.
                        @else
                            Hujachagua kifurushi chochote cha kutumia. Tafadhali chagua kifurushi ili kuanza.
                        @endif
                    </p>
                    
                    <div class="inline-flex items-center bg-emerald-50 text-emerald-700 px-4 py-2 rounded-lg">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="font-medium">Tafadhali lipa sasa ili kuendelea kutumia mfumo</span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="space-y-6">
                <!-- Primary Action: Pay Now -->
                <a href="{{ route('package.choose') }}" 
                   class="block w-full px-8 py-4 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white font-semibold rounded-xl transition-all duration-300 transform hover:-translate-y-0.5 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                    <div class="flex items-center justify-center">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-lg">Chagua Kifurushi &amp; Lipa Sasa</span>
                    </div>
                </a>

                <!-- Secondary Action: Logout -->
                <form method="POST" action="{{ route('logout') }}" class="pt-6 border-t border-emerald-100">
                    @csrf
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-3 text-gray-600 hover:text-gray-800 hover:bg-emerald-50 font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-emerald-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Ondoka kwenye Mfumo
                    </button>
                </form>
            </div>

            <!-- Additional Help Text -->
            @if($status === 'trial')
                <div class="mt-10 p-5 bg-gradient-to-r from-amber-50 to-emerald-50 rounded-xl border border-amber-100">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-amber-600 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                        <div class="text-left">
                            <p class="text-sm font-semibold text-gray-800 mb-1">Kumbuka:</p>
                            <p class="text-sm text-gray-600">
                                Baada ya malipo, utaweza kutumia mfumo kwa kipindi chote cha kifurushi chako. 
                                Una uhakika wa usalama wa malipo na msaada wa kitaalamu.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Support Link -->
            <div class="mt-8 pt-6 border-t border-emerald-100">
                <p class="text-sm text-gray-500">
                    Una swali? 
                    <a href="#" class="text-emerald-600 hover:text-emerald-700 font-medium hover:underline">
                        Wasiliana na msaada wa wateja
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection