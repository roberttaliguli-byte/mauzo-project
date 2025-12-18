@extends('layouts.app')

@section('title', 'Usimamizi wa Ripoti')
@section('page-title', 'Usimamizi wa Ripoti')
@section('page-subtitle', 'Tengeneza na pakua ripoti mbalimbali - ' . now()->format('d/m/Y'))

@section('content')
<div class="container mx-auto px-4 py-6 md:py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-2">Usimamizi wa Ripoti</h1>
        <p class="text-gray-600">Chagua aina ya ripoti na kipindi cha muda kupata taarifa unazohitaji</p>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column: Report Selection Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                <!-- Form Header -->
                <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 px-6 py-5">
                    <div class="flex items-center">
                        <div class="p-3 bg-white/20 rounded-lg backdrop-blur-sm mr-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-white">Uundaji wa Ripoti</h2>
                            <p class="text-emerald-100 text-sm mt-1">Chagua mipangilio ya ripoti yako</p>
                        </div>
                    </div>
                </div>

                <!-- Form Content -->
                <div class="p-6 md:p-8">
                    <form action="{{ route('user.reports.download') }}" method="GET" target="_blank" id="reportForm">
                        <!-- Report Type Selection -->
                        <div class="mb-8">
                            <div class="flex items-center mb-4">
                                <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center mr-3">
                                    <span class="text-emerald-600 font-bold">1</span>
                                </div>
                                <label class="text-lg font-semibold text-gray-700">📊 Aina ya Ripoti</label>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4" role="radiogroup">
                                <div class="relative">
                                    <input type="radio" id="sales_report" name="report_type" value="sales" class="hidden peer" checked required>
                                    <label for="sales_report" class="flex flex-col items-center p-5 border-2 border-gray-200 rounded-xl cursor-pointer peer-checked:border-emerald-500 peer-checked:bg-emerald-50 hover:bg-gray-50 transition-all duration-200">
                                        <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center mb-3">
                                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        <span class="font-medium text-gray-700">Ripoti ya Mauzo</span>
                                        <span class="text-sm text-gray-500 text-center mt-1">Taarifa za mauzo na mapato</span>
                                    </label>
                                </div>

                                <div class="relative">
                                    <input type="radio" id="general_report" name="report_type" value="general" class="hidden peer" required>
                                    <label for="general_report" class="flex flex-col items-center p-5 border-2 border-gray-200 rounded-xl cursor-pointer peer-checked:border-emerald-500 peer-checked:bg-emerald-50 hover:bg-gray-50 transition-all duration-200">
                                        <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center mb-3">
                                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                            </svg>
                                        </div>
                                        <span class="font-medium text-gray-700">Ripoti ya Jumla</span>
                                        <span class="text-sm text-gray-500 text-center mt-1">Takwimu zote za mfumo</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Time Period Selection -->
                        <div class="mb-8">
                            <div class="flex items-center mb-4">
                                <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center mr-3">
                                    <span class="text-emerald-600 font-bold">2</span>
                                </div>
                                <label class="text-lg font-semibold text-gray-700">⏳ Kipindi cha Muda</label>
                            </div>
                            
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                @php
                                    $periods = [
                                        'leo' => ['label' => 'Leo', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                                        'jana' => ['label' => 'Jana', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                                        'week' => ['label' => 'Wiki Hii', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                                        'mwezi' => ['label' => 'Mwezi Huu', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                                        'yote' => ['label' => 'Yote', 'icon' => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z']
                                    ];
                                @endphp
                                
                                @foreach($periods as $value => $data)
                                    <div class="relative">
                                        <input type="radio" id="period_{{ $value }}" name="time_period" 
                                               value="{{ $value }}" 
                                               class="hidden peer" 
                                               {{ $value == 'leo' ? 'checked' : '' }} 
                                               required>
                                        <label for="period_{{ $value }}" 
                                               class="flex flex-col items-center justify-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer peer-checked:border-emerald-500 peer-checked:bg-emerald-50 hover:bg-gray-50 transition-all duration-200">
                                            <svg class="w-5 h-5 text-gray-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $data['icon'] }}"></path>
                                            </svg>
                                            <span class="text-sm font-medium text-gray-700 text-center">{{ $data['label'] }}</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-100">
                            <button type="submit" 
                                    class="flex-1 flex items-center justify-center gap-2 bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white font-semibold py-3 px-6 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 active:scale-[0.98]">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                Pakua Ripoti (PDF)
                            </button>
                            
                            <button type="button" 
                                    onclick="resetForm()"
                                    class="flex-1 flex items-center justify-center gap-2 bg-white border-2 border-gray-300 hover:border-gray-400 text-gray-700 font-medium py-3 px-6 rounded-xl hover:bg-gray-50 transition-all duration-200 active:scale-[0.98]">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Weka Upya
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Column: Information & Preview -->
        <div class="space-y-6">
            <!-- Preview Card -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 px-6 py-5">
                    <div class="flex items-center">
                        <div class="p-3 bg-white/20 rounded-lg backdrop-blur-sm mr-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-white">Kielelezo cha Ripoti</h3>
                            <p class="text-emerald-100 text-sm">Muonekano wa ripoti yako</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="bg-gray-50 rounded-xl p-6 border-2 border-dashed border-gray-200">
                        <div class="flex items-center justify-center mb-4">
                            <div class="w-16 h-20 bg-gradient-to-br from-emerald-100 to-emerald-200 rounded-lg flex items-center justify-center">
                                <span class="text-emerald-600 font-bold text-xl">PDF</span>
                            </div>
                        </div>
                        <div class="text-center">
                            <p class="font-medium text-gray-700 mb-1" id="previewType">Ripoti ya Mauzo</p>
                            <p class="text-sm text-gray-500 mb-3" id="previewPeriod">Leo</p>
                            <div class="text-xs text-gray-400 bg-gray-100 rounded-lg py-2 px-3 inline-block">
                                📊 Inajumuisha takwimu zote, meza na muhtasari
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tips Card -->
            <div class="bg-gradient-to-br from-emerald-50 to-emerald-50/50 rounded-2xl p-6 border border-emerald-100">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 text-emerald-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Vidokezo vya Ripoti
                </h3>
                <ul class="space-y-3">
                    <li class="flex items-start">
                        <span class="text-emerald-500 mr-2">✓</span>
                        <span class="text-sm text-gray-600">Ripoti ya Mauzo inaongeza kwa kina taarifa za mauzo</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-emerald-500 mr-2">✓</span>
                        <span class="text-sm text-gray-600">Chagua "Yote" kupata ripoti kamili ya mfumo</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-emerald-500 mr-2">✓</span>
                        <span class="text-sm text-gray-600">Ripoti zinaweza kuchapishwa au kuhifadhiwa kwa kumbukumbu</span>
                    </li>
                </ul>
            </div>

            <!-- Quick Stats -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <h3 class="font-bold text-gray-800 mb-4">Takwimu za Hivi Punde</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-emerald-50 rounded-lg">
                        <span class="text-sm text-gray-600">Ripoti Zilizopakuliwa</span>
                        <span class="font-bold text-emerald-600">12</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-emerald-50 rounded-lg">
                        <span class="text-sm text-gray-600">Ripoti ya Mwisho</span>
                        <span class="font-bold text-emerald-600">{{ now()->subDays(1)->format('d/m') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Update preview based on selections
    function updatePreview() {
        const reportType = document.querySelector('input[name="report_type"]:checked');
        const timePeriod = document.querySelector('input[name="time_period"]:checked');
        
        if (reportType) {
            const typeText = reportType.value === 'sales' ? 'Ripoti ya Mauzo' : 'Ripoti ya Jumla';
            document.getElementById('previewType').textContent = typeText;
        }
        
        if (timePeriod) {
            const periodLabels = {
                'leo': 'Leo',
                'jana': 'Jana',
                'week': 'Wiki Hii',
                'mwezi': 'Mwezi Huu',
                'yote': 'Yote'
            };
            document.getElementById('previewPeriod').textContent = periodLabels[timePeriod.value];
        }
    }

    // Reset form
    function resetForm() {
        document.getElementById('reportForm').reset();
        updatePreview();
    }

    // Initialize event listeners
    document.addEventListener('DOMContentLoaded', function() {
        // Update preview when selections change
        document.querySelectorAll('input[name="report_type"], input[name="time_period"]').forEach(input => {
            input.addEventListener('change', updatePreview);
        });
        
        // Initialize preview
        updatePreview();
    });
</script>
@endpush

<style>
    /* Smooth transitions */
    * {
        transition: background-color 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
    }
    
    /* Mobile optimizations */
    @media (max-width: 640px) {
        .container {
            padding-left: 1rem;
            padding-right: 1rem;
        }
        
        .grid-cols-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
</style>
@endsection