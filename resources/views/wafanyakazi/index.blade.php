@extends('layouts.app')

@section('title', 'Wafanyakazi - DEMODAY')

@section('page-title', 'Wafanyakazi')
@section('page-subtitle', 'Usimamizi wa wafanyakazi wote - ' . now()->format('d/m/Y'))

@section('content')
<div class="space-y-6">
    <!-- Success Messages -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Statistics Cards - Made responsive -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 md:p-6 card-hover">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-green-100 text-green-600 mr-4">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Jumla ya Wafanyakazi</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $wafanyakazi->count() }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 md:p-6 card-hover">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-emerald-100 text-emerald-600 mr-4">
                    <i class="fas fa-user-check text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Wanaoweza Kuingia</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $wafanyakazi->where('getini', 'ingia')->count() }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 md:p-6 card-hover">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-blue-100 text-blue-600 mr-4">
                    <i class="fas fa-venus-mars text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Wanaume</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $wafanyakazi->where('jinsia', 'Mwanaume')->count() }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 md:p-6 card-hover">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-purple-100 text-purple-600 mr-4">
                    <i class="fas fa-user-clock text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Wamesimamishwa</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $wafanyakazi->where('getini', 'simama')->count() }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Page Navigation Tabs - Made responsive -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 card-hover">
        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-6 border-b border-gray-200">
            <button 
                id="taarifa-tab" 
                class="tab-button pb-3 px-1 transition-colors flex items-center border-b-2 border-green-500 text-green-600 font-semibold text-sm sm:text-base"
                data-tab="taarifa"
            >
                <i class="fas fa-table mr-2"></i>Taarifa za Wafanyakazi
            </button>
            <button 
                id="sajili-tab" 
                class="tab-button pb-3 px-1 transition-colors flex items-center text-gray-500 hover:text-gray-700 text-sm sm:text-base"
                data-tab="sajili"
            >
                <i class="fas fa-plus-circle mr-2"></i>Sajili Mfanyakazi Mpya
            </button>
        </div>
    </div>

    <!-- Wafanyakazi Information Tab -->
    <div id="taarifa-tab-content" class="space-y-6 tab-content">
        <!-- Search and Actions - Made responsive -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 card-hover">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 space-y-4 md:space-y-0">
                <h2 class="text-xl font-bold text-gray-800">Orodha ya Wafanyakazi</h2>
                <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3 w-full md:w-auto">
                    <div class="relative w-full sm:w-auto">
                        <input 
                            type="text" 
                            id="search-input"
                            placeholder="Tafuta mfanyakazi..." 
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        >
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                    <button 
                        onclick="window.print()" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center"
                    >
                        <i class="fas fa-print mr-2"></i> Print
                    </button>
                </div>
            </div>

            <!-- Data Table - Made responsive with horizontal scroll -->
            <div class="overflow-x-auto -mx-6 px-6 md:mx-0 md:px-0">
                <table class="w-full table-auto min-w-[800px] md:min-w-0">
                    <thead>
                        <tr class="bg-gradient-to-r from-green-600 to-green-700">
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white">Mfanyakazi</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white">Mawasiliano</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white">Jinsia</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white">Anuani</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-white">Hali</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-white print:hidden">Vitendo</th>
                        </tr>
                    </thead>
                    <tbody id="employees-tbody" class="divide-y divide-gray-100">
                        @forelse($wafanyakazi as $mfanyakazi)
                            <tr class="employee-row hover:bg-green-50 transition-all duration-200" data-employee='@json($mfanyakazi)'>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-12 w-12 bg-gradient-to-r from-green-500 to-green-600 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-lg">
                                            {{ substr($mfanyakazi->jina, 0, 1) }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-semibold text-gray-900 employee-name">{{ $mfanyakazi->jina }}</div>
                                            <div class="text-xs text-green-600 font-medium">
                                                {{ $mfanyakazi->tarehe_kuzaliwa ? \Carbon\Carbon::parse($mfanyakazi->tarehe_kuzaliwa)->age . ' years' : '--' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4">
                                    <div class="space-y-1">
                                        @if($mfanyakazi->simu)
                                        <div class="flex items-center text-sm text-gray-700">
                                            <i class="fas fa-phone text-green-500 mr-2 w-4"></i>
                                            <span class="employee-phone">{{ $mfanyakazi->simu }}</span>
                                        </div>
                                        @endif
                                        @if($mfanyakazi->barua_pepe)
                                        <div class="flex items-center text-sm text-gray-700">
                                            <i class="fas fa-envelope text-blue-500 mr-2 w-4"></i>
                                            <span class="employee-email truncate max-w-xs">{{ $mfanyakazi->barua_pepe }}</span>
                                        </div>
                                        @endif
                                        @if($mfanyakazi->username)
                                        <div class="flex items-center text-sm text-gray-700">
                                            <i class="fas fa-user text-purple-500 mr-2 w-4"></i>
                                            <span class="employee-username">{{ $mfanyakazi->username }}</span>
                                        </div>
                                        @endif
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold employee-gender
                                        @if($mfanyakazi->jinsia === 'Mwanaume') bg-blue-100 text-blue-800 border border-blue-200
                                        @else bg-pink-100 text-pink-800 border border-pink-200 @endif">
                                        <i class="fas @if($mfanyakazi->jinsia === 'Mwanaume') fa-mars @else fa-venus @endif mr-1"></i>
                                        {{ $mfanyakazi->jinsia }}
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-700 max-w-xs employee-address">
                                        {{ $mfanyakazi->anuani ?: '--' }}
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold employee-status
                                        @if($mfanyakazi->getini === 'ingia') bg-green-100 text-green-800 border border-green-200
                                        @else bg-red-100 text-red-800 border border-red-200 @endif">
                                        <i class="fas @if($mfanyakazi->getini === 'ingia') fa-check-circle @else fa-pause-circle @endif mr-1"></i>
                                        {{ ucfirst($mfanyakazi->getini) }}
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium print:hidden">
                                    <div class="flex justify-center space-x-3">
                                        <button 
                                            class="view-details-btn text-blue-600 hover:text-blue-800 transition-colors transform hover:scale-110 p-2"
                                            title="Angalia Maelezo"
                                        >
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button 
                                            class="edit-employee-btn text-amber-600 hover:text-amber-800 transition-colors transform hover:scale-110 p-2"
                                            title="Badili"
                                            data-id="{{ $mfanyakazi->id }}"
                                        >
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button 
                                            class="delete-employee-btn text-red-500 hover:text-red-700 transition-colors transform hover:scale-110 p-2"
                                            title="Futa"
                                            data-id="{{ $mfanyakazi->id }}"
                                            data-name="{{ $mfanyakazi->jina }}"
                                        >
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @if($mfanyakazi->simu)
                                        <a 
                                            href="tel:{{ $mfanyakazi->simu }}" 
                                            class="text-green-600 hover:text-green-800 transition-colors transform hover:scale-110 p-2"
                                            title="Piga Simu"
                                        >
                                            <i class="fas fa-phone"></i>
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fas fa-users text-5xl text-green-300 mb-4"></i>
                                        <p class="text-lg font-semibold text-gray-600 mb-2">Hakuna wafanyakazi bado.</p>
                                        <p class="text-sm text-gray-500 mb-4">Anza kwa kusajili mfanyakazi wako wa kwanza</p>
                                        <button 
                                            id="go-to-register-btn"
                                            class="bg-gradient-to-r from-green-600 to-green-700 text-white px-6 py-3 rounded-lg hover:from-green-700 hover:to-green-800 transition-all duration-300 transform hover:scale-105 flex items-center shadow-lg"
                                        >
                                            <i class="fas fa-plus-circle mr-2"></i> Sajili Mfanyakazi wa Kwanza
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Sajili Wafanyakazi Tab -->
    <div id="sajili-tab-content" class="tab-content hidden">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 card-hover">
            <h2 class="text-xl font-bold text-gray-800 mb-6">Sajili Mfanyakazi Mpya</h2>
            <form method="POST" action="{{ route('wafanyakazi.store') }}" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Jina -->
                    <div class="relative">
                        <input 
                            type="text" 
                            name="jina" 
                            placeholder=" " 
                            value="{{ old('jina') }}"
                            class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('jina') border-red-500 @enderror" 
                            required
                        >
                        <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                      peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                      peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                      peer-focus:text-sm font-medium">
                            Jina Kamili *
                        </label>
                        @error('jina')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jinsia -->
                    <div class="relative">
                        <select 
                            name="jinsia" 
                            class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent appearance-none bg-white @error('jinsia') border-red-500 @enderror" 
                            required
                        >
                            <option value=""> </option>
                            <option value="Mwanaume" {{ old('jinsia') == 'Mwanaume' ? 'selected' : '' }}>Mwanaume</option>
                            <option value="Mwanamke" {{ old('jinsia') == 'Mwanamke' ? 'selected' : '' }}>Mwanamke</option>
                        </select>
                        <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                      peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                      peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                      peer-focus:text-sm font-medium">
                            Jinsia *
                        </label>
                        <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </div>
                        @error('jinsia')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tarehe ya Kuzaliwa -->
                    <div class="relative">
                        <input 
                            type="date" 
                            name="tarehe_kuzaliwa" 
                            placeholder=" " 
                            value="{{ old('tarehe_kuzaliwa') }}"
                            class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('tarehe_kuzaliwa') border-red-500 @enderror"
                        >
                        <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                      peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                      peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                      peer-focus:text-sm font-medium">
                            Tarehe ya Kuzaliwa
                        </label>
                        @error('tarehe_kuzaliwa')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Anuani -->
                    <div class="relative">
                        <input 
                            type="text" 
                            name="anuani" 
                            placeholder=" " 
                            value="{{ old('anuani') }}"
                            class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('anuani') border-red-500 @enderror"
                        >
                        <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                      peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                      peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                      peer-focus:text-sm font-medium">
                            Anuani ya Makazi
                        </label>
                        @error('anuani')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Simu -->
                    <div class="relative">
                        <input 
                            type="text" 
                            name="simu" 
                            placeholder=" " 
                            value="{{ old('simu') }}"
                            class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('simu') border-red-500 @enderror"
                        >
                        <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                      peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                      peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                      peer-focus:text-sm font-medium">
                            Namba ya Simu
                        </label>
                        @error('simu')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Barua Pepe -->
                    <div class="relative">
                        <input 
                            type="email" 
                            name="barua_pepe" 
                            placeholder=" " 
                            value="{{ old('barua_pepe') }}"
                            class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('barua_pepe') border-red-500 @enderror"
                        >
                        <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                      peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                      peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                      peer-focus:text-sm font-medium">
                            Barua Pepe
                        </label>
                        @error('barua_pepe')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Ndugu -->
                    <div class="relative">
                        <input 
                            type="text" 
                            name="ndugu" 
                            placeholder=" " 
                            value="{{ old('ndugu') }}"
                            class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('ndugu') border-red-500 @enderror"
                        >
                        <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                      peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                      peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                      peer-focus:text-sm font-medium">
                            Jina la Ndugu
                        </label>
                        @error('ndugu')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Simu ya Ndugu -->
                    <div class="relative">
                        <input 
                            type="text" 
                            name="simu_ndugu" 
                            placeholder=" " 
                            value="{{ old('simu_ndugu') }}"
                            class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('simu_ndugu') border-red-500 @enderror"
                        >
                        <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                      peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                      peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                      peer-focus:text-sm font-medium">
                            Simu ya Ndugu
                        </label>
                        @error('simu_ndugu')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Username -->
                    <div class="relative">
                        <input 
                            type="text" 
                            name="username" 
                            placeholder=" " 
                            value="{{ old('username') }}"
                            class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('username') border-red-500 @enderror"
                        >
                        <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                      peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                      peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                      peer-focus:text-sm font-medium">
                            Neno la Kuingia (Username)
                        </label>
                        @error('username')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="relative">
                        <input 
                            type="password" 
                            name="password" 
                            placeholder=" " 
                            class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('password') border-red-500 @enderror"
                        >
                        <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                      peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                      peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                      peer-focus:text-sm font-medium">
                            Neno la Siri (Password)
                        </label>
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Buttons - Made responsive -->
                <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                    <button 
                        type="submit" 
                        class="bg-gradient-to-r from-green-600 to-green-700 text-white px-8 py-3 rounded-lg hover:from-green-700 hover:to-green-800 transition-all duration-300 transform hover:scale-105 flex items-center justify-center shadow-lg"
                    >
                        <i class="fas fa-save mr-2"></i> Hifadhi Mfanyakazi
                    </button>
                    <button 
                        type="reset" 
                        class="bg-gray-300 text-gray-700 px-8 py-3 rounded-lg hover:bg-gray-400 transition-colors flex items-center justify-center"
                    >
                        <i class="fas fa-redo mr-2"></i> Safisha Fomu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Details Modal - Made responsive -->
<div id="details-modal" class="modal fixed inset-0 z-50 flex items-center justify-center hidden p-4">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 z-50 max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Taarifa Kamili za Mfanyakazi</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Jina Kamili:</label>
                        <p id="detail-jina" class="text-gray-900 font-semibold"></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Jinsia:</label>
                        <p id="detail-jinsia" class="text-gray-900"></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Tarehe ya Kuzaliwa:</label>
                        <p id="detail-tarehe" class="text-gray-900"></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Anuani:</label>
                        <p id="detail-anuani" class="text-gray-900"></p>
                    </div>
                </div>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Simu:</label>
                        <p id="detail-simu" class="text-gray-900"></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Barua Pepe:</label>
                        <p id="detail-barua-pepe" class="text-gray-900"></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Ndugu:</label>
                        <p id="detail-ndugu" class="text-gray-900"></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Simu ya Ndugu:</label>
                        <p id="detail-simu-ndugu" class="text-gray-900"></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Neno la Kuingia:</label>
                        <p id="detail-username" class="text-gray-900 font-semibold"></p>
                    </div>
                </div>
            </div>
            <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-sm text-green-700">
                    <i class="fas fa-info-circle mr-2"></i>
                    Leo tarehe {{ \Carbon\Carbon::now()->translatedFormat('d F, Y') }}, 
                    Una jumla ya wafanyakazi {{ count($wafanyakazi) }}.
                </p>
            </div>
        </div>
        <div class="flex justify-end p-6 border-t border-gray-200">
            <button 
                id="close-details-modal"
                class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors"
            >
                Funga
            </button>
        </div>
    </div>
</div>

<!-- Edit Modal - Made responsive -->
<div id="edit-modal" class="modal fixed inset-0 z-50 flex items-center justify-center hidden p-4">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-2xl shadow-xl w-full max-w-4xl mx-4 max-h-[90vh] overflow-y-auto z-50">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Badili Taarifa za Mfanyakazi</h3>
        </div>
        <form id="edit-form" method="POST" class="p-6">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Jina -->
                <div class="relative">
                    <input 
                        type="text" 
                        name="jina" 
                        id="edit-jina"
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                        required
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Jina Kamili *
                    </label>
                </div>

                <!-- Jinsia -->
                <div class="relative">
                    <select 
                        name="jinsia" 
                        id="edit-jinsia"
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent appearance-none bg-white" 
                        required
                    >
                        <option value="Mwanaume">Mwanaume</option>
                        <option value="Mwanamke">Mwanamke</option>
                    </select>
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Jinsia *
                    </label>
                    <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                        <i class="fas fa-chevron-down text-gray-400"></i>
                    </div>
                </div>

                <!-- Tarehe ya Kuzaliwa -->
                <div class="relative">
                    <input 
                        type="date" 
                        name="tarehe_kuzaliwa" 
                        id="edit-tarehe_kuzaliwa"
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Tarehe ya Kuzaliwa
                    </label>
                </div>

                <!-- Anuani -->
                <div class="relative">
                    <input 
                        type="text" 
                        name="anuani" 
                        id="edit-anuani"
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Anuani
                    </label>
                </div>

                <!-- Simu -->
                <div class="relative">
                    <input 
                        type="text" 
                        name="simu" 
                        id="edit-simu"
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Simu
                    </label>
                </div>

                <!-- Barua Pepe -->
                <div class="relative">
                    <input 
                        type="email" 
                        name="barua_pepe" 
                        id="edit-barua_pepe"
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Barua Pepe
                    </label>
                </div>

                <!-- Getini -->
                <div class="relative">
                    <select 
                        name="getini" 
                        id="edit-getini"
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent appearance-none bg-white"
                    >
                        <option value="simama">Simama</option>
                        <option value="ingia">Ingia</option>
                    </select>
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Hali ya Kuingia
                    </label>
                    <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                        <i class="fas fa-chevron-down text-gray-400"></i>
                    </div>
                </div>

                <!-- Ndugu -->
                <div class="relative">
                    <input 
                        type="text" 
                        name="ndugu" 
                        id="edit-ndugu"
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Ndugu
                    </label>
                </div>

                <!-- Simu ya Ndugu -->
                <div class="relative">
                    <input 
                        type="text" 
                        name="simu_ndugu" 
                        id="edit-simu_ndugu"
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Simu ya Ndugu
                    </label>
                </div>

                <!-- Username -->
                <div class="relative">
                    <input 
                        type="text" 
                        name="username" 
                        id="edit-username"
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                        required
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Username *
                    </label>
                </div>

                <!-- Password -->
                <div class="relative">
                    <input 
                        type="password" 
                        name="password" 
                        placeholder=" " 
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Neno la Siri (Acha tupu kama hutaki kubadilisha)
                    </label>
                </div>
            </div>
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 mt-6">
                <button 
                    type="button" 
                    id="close-edit-modal"
                    class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors"
                >
                    Ghairi
                </button>
                <button 
                    type="submit" 
                    class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
                >
                    Hifadhi Mabadiliko
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal - Made responsive -->
<div id="delete-modal" class="modal fixed inset-0 z-50 flex items-center justify-center hidden p-4">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 z-50">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Thibitisha Kufuta Mfanyakazi</h3>
        </div>
        <div class="p-6">
            <div class="flex items-center justify-center mb-4">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
            </div>
            <p class="text-gray-700 mb-6 text-center">
                Una uhakika unataka kufuta mfanyakazi "<span id="delete-employee-name" class="font-semibold"></span>"?
                <br>Hatua hii haiwezi kutenduliwa.
            </p>
            <div class="flex flex-col sm:flex-row justify-center space-y-3 sm:space-y-0 sm:space-x-3">
                <button 
                    id="cancel-delete"
                    class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors"
                >
                    Ghairi
                </button>
                <form id="delete-form" method="POST" class="sm:w-auto w-full">
                    @csrf
                    @method('DELETE')
                    <button 
                        type="submit" 
                        class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors w-full sm:w-auto"
                    >
                        Ndio, Futa
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.modal {
    transition: opacity 0.3s ease;
}

.tab-content {
    transition: opacity 0.3s ease;
}

.hidden {
    display: none !important;
}

.employee-row.hidden {
    display: none;
}

/* Ensure table is scrollable on mobile */
@media (max-width: 768px) {
    .overflow-x-auto {
        -webkit-overflow-scrolling: touch;
    }
}

/* Improve touch targets for mobile */
button, a {
    min-height: 44px;
    min-width: 44px;
}

/* Prevent zoom on iOS inputs */
@media (max-width: 768px) {
    input, select, textarea {
        font-size: 16px !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
class WafanyakaziManager {
    constructor() {
        this.currentTab = 'taarifa';
        this.searchQuery = '';
        this.init();
    }

    init() {
        this.bindEvents();
        this.showTab('taarifa');
    }

    bindEvents() {
        // Tab navigation
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', (e) => {
                const tab = e.target.closest('.tab-button').dataset.tab;
                this.showTab(tab);
            });
        });

        // Search functionality
        const searchInput = document.getElementById('search-input');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                this.searchQuery = e.target.value.toLowerCase();
                this.filterEmployees();
            });
        }

        // Go to register button
        const goToRegisterBtn = document.getElementById('go-to-register-btn');
        if (goToRegisterBtn) {
            goToRegisterBtn.addEventListener('click', () => {
                this.showTab('sajili');
            });
        }

        // Employee actions
        this.bindEmployeeActions();

        // Modal events
        this.bindModalEvents();
    }

    showTab(tabName) {
        // Update tabs
        document.querySelectorAll('.tab-button').forEach(button => {
            if (button.dataset.tab === tabName) {
                button.classList.add('border-b-2', 'border-green-500', 'text-green-600', 'font-semibold');
                button.classList.remove('text-gray-500');
            } else {
                button.classList.remove('border-b-2', 'border-green-500', 'text-green-600', 'font-semibold');
                button.classList.add('text-gray-500');
            }
        });

        // Update tab content
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        
        const activeContent = document.getElementById(`${tabName}-tab-content`);
        if (activeContent) {
            activeContent.classList.remove('hidden');
        }

        this.currentTab = tabName;
    }

    bindEmployeeActions() {
        // View details buttons
        document.querySelectorAll('.view-details-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                const row = e.target.closest('.employee-row');
                const employeeData = JSON.parse(row.dataset.employee);
                this.showDetails(employeeData);
            });
        });

        // Edit buttons
        document.querySelectorAll('.edit-employee-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                const employeeId = e.target.closest('.edit-employee-btn').dataset.id;
                const row = e.target.closest('.employee-row');
                const employeeData = JSON.parse(row.dataset.employee);
                this.editEmployee(employeeData);
            });
        });

        // Delete buttons
        document.querySelectorAll('.delete-employee-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                const employeeId = e.target.closest('.delete-employee-btn').dataset.id;
                const employeeName = e.target.closest('.delete-employee-btn').dataset.name;
                this.deleteEmployee(employeeId, employeeName);
            });
        });
    }

    bindModalEvents() {
        // Details modal
        const detailsModal = document.getElementById('details-modal');
        const closeDetailsBtn = document.getElementById('close-details-modal');

        closeDetailsBtn.addEventListener('click', () => {
            detailsModal.classList.add('hidden');
        });

        detailsModal.addEventListener('click', (e) => {
            if (e.target === detailsModal || e.target.classList.contains('modal-overlay')) {
                detailsModal.classList.add('hidden');
            }
        });

        // Edit modal
        const editModal = document.getElementById('edit-modal');
        const closeEditBtn = document.getElementById('close-edit-modal');

        closeEditBtn.addEventListener('click', () => {
            editModal.classList.add('hidden');
        });

        editModal.addEventListener('click', (e) => {
            if (e.target === editModal || e.target.classList.contains('modal-overlay')) {
                editModal.classList.add('hidden');
            }
        });

        // Delete modal
        const deleteModal = document.getElementById('delete-modal');
        const cancelDeleteBtn = document.getElementById('cancel-delete');

        cancelDeleteBtn.addEventListener('click', () => {
            deleteModal.classList.add('hidden');
        });

        deleteModal.addEventListener('click', (e) => {
            if (e.target === deleteModal || e.target.classList.contains('modal-overlay')) {
                deleteModal.classList.add('hidden');
            }
        });

        // Close modals on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                detailsModal.classList.add('hidden');
                editModal.classList.add('hidden');
                deleteModal.classList.add('hidden');
            }
        });
    }

    filterEmployees() {
        const rows = document.querySelectorAll('.employee-row');
        
        rows.forEach(row => {
            const name = row.querySelector('.employee-name').textContent.toLowerCase();
            const phone = row.querySelector('.employee-phone')?.textContent.toLowerCase() || '';
            const email = row.querySelector('.employee-email')?.textContent.toLowerCase() || '';
            const username = row.querySelector('.employee-username')?.textContent.toLowerCase() || '';
            
            const matches = name.includes(this.searchQuery) || 
                           phone.includes(this.searchQuery) || 
                           email.includes(this.searchQuery) || 
                           username.includes(this.searchQuery);
            
            if (matches || this.searchQuery === '') {
                row.classList.remove('hidden');
            } else {
                row.classList.add('hidden');
            }
        });
    }

    showDetails(employee) {
        // Populate details modal
        document.getElementById('detail-jina').textContent = employee.jina;
        document.getElementById('detail-jinsia').textContent = employee.jinsia;
        document.getElementById('detail-tarehe').textContent = employee.tarehe_kuzaliwa || '--';
        document.getElementById('detail-anuani').textContent = employee.anuani || '--';
        document.getElementById('detail-simu').textContent = employee.simu || '--';
        document.getElementById('detail-barua-pepe').textContent = employee.barua_pepe || '--';
        document.getElementById('detail-ndugu').textContent = employee.ndugu || '--';
        document.getElementById('detail-simu-ndugu').textContent = employee.simu_ndugu || '--';
        document.getElementById('detail-username').textContent = employee.username || '--';

        // Show modal
        document.getElementById('details-modal').classList.remove('hidden');
    }

    editEmployee(employee) {
        // Populate edit form
        document.getElementById('edit-jina').value = employee.jina;
        document.getElementById('edit-jinsia').value = employee.jinsia;
        document.getElementById('edit-tarehe_kuzaliwa').value = employee.tarehe_kuzaliwa || '';
        document.getElementById('edit-anuani').value = employee.anuani || '';
        document.getElementById('edit-simu').value = employee.simu || '';
        document.getElementById('edit-barua_pepe').value = employee.barua_pepe || '';
        document.getElementById('edit-getini').value = employee.getini || 'simama';
        document.getElementById('edit-ndugu').value = employee.ndugu || '';
        document.getElementById('edit-simu_ndugu').value = employee.simu_ndugu || '';
        document.getElementById('edit-username').value = employee.username || '';

        // Set form action
        document.getElementById('edit-form').action = `/wafanyakazi/${employee.id}`;

        // Show modal
        document.getElementById('edit-modal').classList.remove('hidden');
    }

    deleteEmployee(employeeId, employeeName) {
        // Populate delete modal
        document.getElementById('delete-employee-name').textContent = employeeName;
        document.getElementById('delete-form').action = `/wafanyakazi/${employeeId}`;

        // Show modal
        document.getElementById('delete-modal').classList.remove('hidden');
    }
}

// Initialize the application when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    new WafanyakaziManager();
});
</script>
@endpush