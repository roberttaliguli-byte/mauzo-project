@extends('layouts.app')

@section('title', 'Taarifa za Kampuni')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 mt-4 sm:mt-6">
    @php
        $regions = [
            "Arusha","Dar es Salaam","Dodoma","Geita","Iringa","Kagera","Katavi",
            "Kigoma","Kilimanjaro","Lindi","Manyara","Mara","Mbeya","Morogoro",
            "Mtwara","Njombe","Pwani","Ruvuma","Rukwa","Shinyanga","Singida",
            "Tabora","Tanga","Zanzibar North","Zanzibar South","Zanzibar Urban/West"
        ];
    @endphp

    <!-- Success/Error Notifications with Amber/Orange theme -->
    @if(session('success'))
        <div id="success-notification" class="mb-4 bg-amber-50 border-l-4 border-amber-600 text-amber-800 p-3 sm:p-4 rounded-lg shadow-md flex justify-between items-start sm:items-center gap-3 animate-fade-in" role="alert">
            <div class="flex items-start sm:items-center">
                <svg class="h-5 w-5 text-amber-600 mr-2 sm:mr-3 flex-shrink-0 mt-0.5 sm:mt-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="font-medium text-sm sm:text-base">{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-amber-700 hover:text-amber-900 flex-shrink-0">
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div id="error-notification" class="mb-4 bg-orange-50 border-l-4 border-orange-600 text-orange-800 p-3 sm:p-4 rounded-lg shadow-md flex justify-between items-start sm:items-center gap-3 animate-fade-in" role="alert">
            <div class="flex items-start sm:items-center">
                <svg class="h-5 w-5 text-orange-600 mr-2 sm:mr-3 flex-shrink-0 mt-0.5 sm:mt-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <span class="font-medium text-sm sm:text-base">{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-orange-700 hover:text-orange-900 flex-shrink-0">
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>
    @endif

    <!-- Tabs with Amber/Orange theme -->
    <div class="border-b border-gray-200 overflow-x-auto pb-px">
        <nav class="flex space-x-2 sm:space-x-6 min-w-max sm:min-w-0" aria-label="Tabs">
            <button id="tab-info" class="tab-button py-2 px-3 sm:px-4 border-b-2 font-medium text-xs sm:text-sm transition-all duration-200 whitespace-nowrap flex items-center text-gray-700 border-amber-600">
                <span class="sm:hidden">📋</span>
                <span class="hidden sm:inline">📋 Taarifa za Kampuni</span>
            </button>
            <button id="tab-edit" class="tab-button py-2 px-3 sm:px-4 border-b-2 font-medium text-xs sm:text-sm transition-all duration-200 whitespace-nowrap flex items-center text-gray-500 border-transparent">
                <span class="sm:hidden">🛠️</span>
                <span class="hidden sm:inline">🛠️ Badili Taarifa</span>
            </button>
        </nav>
    </div>

    <!-- Tab Contents -->
    <div class="mt-4 sm:mt-6">
        <!-- Info Tab -->
        <div id="content-info" class="tab-content">
            <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden mb-6">
                <!-- Header with Amber gradient -->
                <div class="bg-gradient-to-r from-amber-700 to-amber-600 text-white px-4 sm:px-6 py-3 sm:py-4">
                    <h2 class="text-base sm:text-lg font-semibold flex items-center">
                        <span class="sm:hidden">📋</span>
                        <span class="hidden sm:inline">📋 Taarifa ya Kampuni</span>
                    </h2>
                    <p class="text-xs sm:text-sm text-amber-100 mt-1 hidden sm:block">Maelezo ya kampuni yako yaliyosajiliwa</p>
                </div>

                <!-- Mobile View: Single Column with Amber labels -->
                <div class="block sm:hidden divide-y divide-gray-100">
                    <div class="px-4 py-3">
                        <div class="text-xs font-medium text-amber-700 mb-1">Jina la Kampuni</div>
                        <div class="text-sm font-semibold text-gray-900">{{ $company->company_name ?? 'Hakuna' }}</div>
                    </div>
                    <div class="px-4 py-3">
                        <div class="text-xs font-medium text-amber-700 mb-1">Jina la Mmiliki</div>
                        <div class="text-sm font-semibold text-gray-900">{{ $company->owner_name ?? 'Hakuna' }}</div>
                    </div>
                    <div class="px-4 py-3">
                        <div class="text-xs font-medium text-amber-700 mb-1">Tarehe ya Kuzaliwa</div>
                        <div class="text-sm text-gray-900">
                            {{ $company && $company->owner_dob ? \Carbon\Carbon::parse($company->owner_dob)->format('d M, Y') : 'Hakuna' }}
                        </div>
                    </div>
                    <div class="px-4 py-3">
                        <div class="text-xs font-medium text-amber-700 mb-1">Jinsia</div>
                        <div class="text-sm">
                            @if($company->owner_gender ?? false)
                                <span class="px-2 py-1 bg-amber-100 text-amber-800 rounded-full text-xs font-medium">
                                    {{ strtoupper($company->owner_gender) }}
                                </span>
                            @else
                                <span class="text-gray-900">Hakuna</span>
                            @endif
                        </div>
                    </div>
                    <div class="px-4 py-3">
                        <div class="text-xs font-medium text-amber-700 mb-1">Mahali Ilipo</div>
                        <div class="text-sm text-gray-900">{{ $company->location ?? 'Hakuna' }}</div>
                    </div>
                    <div class="px-4 py-3">
                        <div class="text-xs font-medium text-amber-700 mb-1">Mkoa</div>
                        <div class="text-sm">
                            <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded-full text-xs font-medium">
                                {{ strtoupper($company->region ?? 'Hakuna') }}
                            </span>
                        </div>
                    </div>
                    <div class="px-4 py-3">
                        <div class="text-xs font-medium text-amber-700 mb-1">Namba ya Simu</div>
                        <div class="text-sm text-gray-900 flex items-center">
                            <svg class="w-4 h-4 mr-1 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            {{ $company->phone ?? 'Hakuna' }}
                        </div>
                    </div>
                    <div class="px-4 py-3">
                        <div class="text-xs font-medium text-amber-700 mb-1">Barua Pepe</div>
                        <div class="text-sm text-gray-900 flex items-center truncate">
                            <svg class="w-4 h-4 mr-1 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <span class="truncate">{{ $company->email ?? 'Hakuna' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Desktop View: Grid 2 Columns with Amber labels -->
                <div class="hidden sm:grid sm:grid-cols-2 gap-x-6 gap-y-4 px-6 py-6 text-gray-800 text-sm">
                    <!-- Left column labels - Amber -->
                    <div class="font-medium text-amber-700">Jina la Kampuni</div>
                    <div class="text-gray-900 font-semibold">{{ $company->company_name ?? 'Hakuna' }}</div>

                    <div class="font-medium text-amber-700">Jina la Mmiliki</div>
                    <div class="text-gray-900 font-semibold">{{ $company->owner_name ?? 'Hakuna' }}</div>

                    <div class="font-medium text-amber-700">Tarehe ya Kuzaliwa</div>
                    <div class="text-gray-900">
                        {{ $company && $company->owner_dob ? \Carbon\Carbon::parse($company->owner_dob)->format('d F, Y') : 'Hakuna' }}
                    </div>

                    <div class="font-medium text-amber-700">Jinsia</div>
                    <div class="text-gray-900">
                        @if($company->owner_gender ?? false)
                            <span class="px-2 py-1 bg-amber-100 text-amber-800 rounded-full text-xs font-medium">
                                {{ strtoupper($company->owner_gender) }}
                            </span>
                        @else
                            Hakuna
                        @endif
                    </div>

                    <div class="font-medium text-amber-700">Mahali Ilipo</div>
                    <div class="text-gray-900">{{ $company->location ?? 'Hakuna' }}</div>

                    <div class="font-medium text-amber-700">Mkoa</div>
                    <div class="text-gray-900">
                        <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded-full text-xs font-medium">
                            {{ strtoupper($company->region ?? 'Hakuna') }}
                        </span>
                    </div>

                    <div class="font-medium text-amber-700">Namba ya Simu</div>
                    <div class="text-gray-900 flex items-center">
                        <svg class="w-4 h-4 mr-1 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        {{ $company->phone ?? 'Hakuna' }}
                    </div>

                    <div class="font-medium text-amber-700">Barua Pepe</div>
                    <div class="text-gray-900 break-all flex items-center">
                        <svg class="w-4 h-4 mr-1 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        {{ $company->email ?? 'Hakuna' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Tab - NO LOGO FIELD -->
        <div id="content-edit" class="tab-content hidden">
            <div class="bg-white shadow rounded-lg border border-gray-200">
                <div class="bg-gradient-to-r from-amber-50 to-orange-50 px-4 sm:px-6 py-3 sm:py-4 border-b">
                    <h2 class="text-base sm:text-lg font-semibold text-gray-800 flex items-center">
                        <span class="sm:hidden">🛠️ Hariri</span>
                        <span class="hidden sm:inline">🛠️ Hariri Taarifa za Kampuni</span>
                    </h2>
                </div>
                
                <form action="{{ route('company.update') }}" method="POST" class="px-4 sm:px-6 py-4 sm:py-6" id="companyEditForm">
                    @csrf
                    @method('PUT')
                    
                    <!-- Mobile: Single Column, Desktop: Grid 2 Columns -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 text-sm text-gray-700">
                        <!-- Left column -->
                        <div class="space-y-3 sm:space-y-4">
                            <div>
                                <label class="block font-medium mb-1 text-xs sm:text-sm text-amber-700">
                                    Jina la Kampuni <span class="text-orange-600">*</span>
                                </label>
                                <input type="text" name="company_name" value="{{ old('company_name', $company->company_name ?? '') }}"
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @error('company_name') border-orange-500 @enderror" 
                                       required>
                                @error('company_name')
                                    <p class="text-orange-600 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block font-medium mb-1 text-xs sm:text-sm text-amber-700">
                                    Tarehe ya Kuzaliwa <span class="text-orange-600">*</span>
                                </label>
                                <input type="date" name="owner_dob" value="{{ old('owner_dob', $company->owner_dob ?? '') }}"
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @error('owner_dob') border-orange-500 @enderror" 
                                       required>
                                @error('owner_dob')
                                    <p class="text-orange-600 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block font-medium mb-1 text-xs sm:text-sm text-amber-700">Jinsia</label>
                                <select name="owner_gender" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                    <option value="">Chagua Jinsia</option>
                                    <option value="male" {{ old('owner_gender', $company->owner_gender ?? '') == 'male' ? 'selected' : '' }}>ME</option>
                                    <option value="female" {{ old('owner_gender', $company->owner_gender ?? '') == 'female' ? 'selected' : '' }}>KE</option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-medium mb-1 text-xs sm:text-sm text-amber-700">
                                    Mkoa <span class="text-orange-600">*</span>
                                </label>
                                <select name="region" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @error('region') border-orange-500 @enderror" required>
                                    <option value="">Chagua Mkoa</option>
                                    @foreach($regions as $region)
                                        <option value="{{ $region }}" {{ old('region', $company->region ?? '') == $region ? 'selected' : '' }}>
                                            {{ $region }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('region')
                                    <p class="text-orange-600 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Right column -->
                        <div class="space-y-3 sm:space-y-4">
                            <div>
                                <label class="block font-medium mb-1 text-xs sm:text-sm text-amber-700">
                                    Jina la Mmiliki <span class="text-orange-600">*</span>
                                </label>
                                <input type="text" name="owner_name" value="{{ old('owner_name', $company->owner_name ?? '') }}"
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @error('owner_name') border-orange-500 @enderror" 
                                       required>
                                @error('owner_name')
                                    <p class="text-orange-600 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block font-medium mb-1 text-xs sm:text-sm text-amber-700">
                                    Mahali Ilipo <span class="text-orange-600">*</span>
                                </label>
                                <input type="text" name="location" value="{{ old('location', $company->location ?? '') }}"
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @error('location') border-orange-500 @enderror" 
                                       required>
                                @error('location')
                                    <p class="text-orange-600 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block font-medium mb-1 text-xs sm:text-sm text-amber-700">
                                    Namba ya Simu <span class="text-orange-600">*</span>
                                </label>
                                <input type="tel" name="phone" value="{{ old('phone', $company->phone ?? '') }}"
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @error('phone') border-orange-500 @enderror" 
                                       required>
                                @error('phone')
                                    <p class="text-orange-600 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block font-medium mb-1 text-xs sm:text-sm text-amber-700">
                                    Barua Pepe <span class="text-orange-600">*</span>
                                </label>
                                <input type="email" name="email"
                                       value="{{ old('email', $company->email ?? '') }}"
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @error('email') border-orange-500 @enderror" 
                                       required>
                                @error('email')
                                    <p class="text-orange-600 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- LOGO FIELD COMPLETELY REMOVED -->
                        </div>
                    </div>

                    <!-- Buttons with Amber/Orange theme -->
                    <div class="mt-6 sm:mt-8 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                        <button type="button" onclick="window.location.href='{{ route('company.info') }}'#info"
                                class="w-full sm:w-auto px-4 sm:px-5 py-2 border border-amber-300 text-amber-700 rounded-full hover:bg-amber-50 transition text-sm font-medium">
                            Ghairi
                        </button>
                        <button type="submit" id="submitBtn"
                                class="w-full sm:w-auto px-4 sm:px-5 py-2 bg-gradient-to-r from-amber-700 to-orange-600 text-white rounded-full hover:from-amber-800 hover:to-orange-700 transition flex items-center justify-center text-sm font-medium shadow-md">
                            <span class="sm:hidden">💾 Hifadhi</span>
                            <span class="hidden sm:inline">💾 Hifadhi Mabadiliko</span>
                        </button>
                    </div>

                    @if($errors->any())
                        <div class="mt-4 text-orange-700 text-xs sm:text-sm bg-orange-50 p-3 sm:p-4 rounded-lg border-l-4 border-orange-600">
                            <p class="font-medium mb-2 flex items-center">
                                <svg class="w-4 h-4 mr-1 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                Tafadhali sahihisha makosa yafuatayo:
                            </p>
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .animate-fade-in {
        animation: fadeIn 0.3s ease-out;
    }
    
    /* Mobile optimizations */
    @media (max-width: 640px) {
        input, select, button {
            font-size: 16px !important; /* Prevents zoom on iOS */
        }
        
        .tab-button {
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
        }
    }
    
    /* Smooth transitions */
    .tab-content {
        transition: opacity 0.2s ease-in-out;
    }
    
    /* Custom scrollbar for tabs on mobile */
    .overflow-x-auto::-webkit-scrollbar {
        height: 3px;
    }
    
    .overflow-x-auto::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    
    .overflow-x-auto::-webkit-scrollbar-thumb {
        background: #c2410c; /* Orange-800 */
        border-radius: 3px;
    }
    
    /* Amber/Orange focus rings */
    input:focus, select:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.2);
    }
</style>

<!-- Tabs and Notifications JS -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabInfo = document.getElementById('tab-info');
        const tabEdit = document.getElementById('tab-edit');
        const contentInfo = document.getElementById('content-info');
        const contentEdit = document.getElementById('content-edit');
        
        function setTabStyles(activeTab) {
            if (activeTab === 'info') {
                // Info tab active
                tabInfo.classList.add('text-gray-700', 'border-amber-600');
                tabInfo.classList.remove('text-gray-500', 'border-transparent');
                tabEdit.classList.add('text-gray-500', 'border-transparent');
                tabEdit.classList.remove('text-gray-700', 'border-amber-600');
            } else {
                // Edit tab active
                tabEdit.classList.add('text-gray-700', 'border-amber-600');
                tabEdit.classList.remove('text-gray-500', 'border-transparent');
                tabInfo.classList.add('text-gray-500', 'border-transparent');
                tabInfo.classList.remove('text-gray-700', 'border-amber-600');
            }
        }
        
        // Auto-hide notifications
        ['success-notification', 'error-notification'].forEach(id => {
            const notification = document.getElementById(id);
            if (notification) {
                setTimeout(() => {
                    notification.style.transition = 'opacity 0.5s ease';
                    notification.style.opacity = '0';
                    setTimeout(() => notification.remove(), 500);
                }, 5000);
            }
        });

        // Check which tab to show
        const hash = window.location.hash;
        const activeTab = '{{ session('active_tab', 'info') }}';
        
        if (hash === '#edit' || activeTab === 'edit') {
            contentEdit.classList.remove('hidden');
            contentInfo.classList.add('hidden');
            setTabStyles('edit');
        } else {
            contentInfo.classList.remove('hidden');
            contentEdit.classList.add('hidden');
            setTabStyles('info');
        }

        // Tab click handlers
        tabInfo.addEventListener('click', () => {
            contentInfo.classList.remove('hidden');
            contentEdit.classList.add('hidden');
            setTabStyles('info');
            window.location.hash = 'info';
        });

        tabEdit.addEventListener('click', () => {
            contentEdit.classList.remove('hidden');
            contentInfo.classList.add('hidden');
            setTabStyles('edit');
            window.location.hash = 'edit';
        });

        // Form submission loading
        const form = document.getElementById('companyEditForm');
        const submitBtn = document.getElementById('submitBtn');
        
        if (form) {
            form.addEventListener('submit', function() {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="sm:hidden">⏳</span><span class="hidden sm:inline">⏳ Inahifadhi...</span>';
                submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
            });
        }
    });

    <!-- Add this before the closing </div> or in your script section -->

    // Real-time email validation
    const emailInput = document.querySelector('input[name="email"]');
    const emailError = document.createElement('p');
    emailError.className = 'text-orange-600 text-xs mt-1 hidden';
    emailInput.parentNode.appendChild(emailError);
    
    let emailCheckTimeout;
    
    emailInput.addEventListener('input', function() {
        clearTimeout(emailCheckTimeout);
        
        const email = this.value;
        
        // Basic email format validation
        if (!email.includes('@') || !email.includes('.')) {
            return;
        }
        
        emailCheckTimeout = setTimeout(() => {
            // Show checking indicator
            emailInput.classList.add('bg-gray-50');
            
            fetch('{{ route("company.check.email") }}?email=' + encodeURIComponent(email))
                .then(response => response.json())
                .then(data => {
                    emailInput.classList.remove('bg-gray-50');
                    
                    if (data.exists) {
                        emailError.textContent = data.message;
                        emailError.classList.remove('hidden');
                        emailInput.classList.add('border-orange-500', 'bg-orange-50');
                        emailInput.classList.remove('border-green-500');
                    } else {
                        emailError.classList.add('hidden');
                        emailInput.classList.remove('border-orange-500', 'bg-orange-50');
                        emailInput.classList.add('border-green-500');
                    }
                })
                .catch(error => {
                    emailInput.classList.remove('bg-gray-50');
                });
        }, 500); // Wait 500ms after user stops typing
    });

</script>
@endsection