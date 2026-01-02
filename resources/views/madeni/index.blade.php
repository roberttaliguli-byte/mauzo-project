@extends('layouts.app')

@section('title', 'Madeni - DEMODAY')

@section('page-title', 'Madeni')
@section('page-subtitle', 'Usimamizi wa mikopo na malipo - ' . now()->format('d/m/Y'))

@section('content')
<div class="space-y-6">
    <!-- Statistics Cards - Responsive Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
        <div class="bg-white rounded-xl md:rounded-2xl shadow-lg border border-green-200 p-4 md:p-6 card-hover">
            <div class="flex items-center">
                <div class="p-2 md:p-3 rounded-lg bg-green-100 text-green-600 mr-3 md:mr-4">
                    <i class="fas fa-money-bill-wave text-lg md:text-xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs md:text-sm text-black font-medium truncate">Jumla ya Madeni</p>
                    <h3 class="text-lg md:text-2xl font-bold text-gray-800 truncate">TZS {{ number_format($madeni->sum('baki'), 2) }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl md:rounded-2xl shadow-lg border border-red-100 p-4 md:p-6 card-hover">
            <div class="flex items-center">
                <div class="p-2 md:p-3 rounded-lg bg-red-100 text-red-600 mr-3 md:mr-4">
                    <i class="fas fa-hand-holding-usd text-lg md:text-xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs md:text-sm text-black font-medium truncate">Mikopo yote</p>
                    <h3 class="text-lg md:text-2xl font-bold text-black">{{ $madeni->count() }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl md:rounded-2xl shadow-lg border border-green-100 p-4 md:p-6 card-hover">
            <div class="flex items-center">
                <div class="p-2 md:p-3 rounded-lg bg-blue-100 text-blue-600 mr-3 md:mr-4">
                    <i class="fas fa-users text-lg md:text-xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs md:text-sm text-black font-medium truncate">Wakopaji</p>
                    <h3 class="text-lg md:text-2xl font-bold text-gray-800">{{ $madeni->pluck('jina_mkopaji')->unique()->count() }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl md:rounded-2xl shadow-lg border border-yellow-400 p-4 md:p-6 card-hover">
            <div class="flex items-center">
                <div class="p-2 md:p-3 rounded-lg bg-amber-100 text-amber-600 mr-3 md:mr-4">
                    <i class="fas fa-boxes text-lg md:text-xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs md:text-sm text-black-500 font-medium truncate">Bidhaa Zilizokopwa</p>
                    <h3 class="text-lg md:text-2xl font-bold text-gray-800">{{ $madeni->sum('idadi') }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Page Navigation Tabs - Mobile Friendly -->
    <div class="bg-white rounded-xl md:rounded-2xl shadow-lg border border-gray-100 p-2 md:p-4 card-hover">
        <div class="flex space-x-2 md:space-x-6 overflow-x-auto scrollbar-hide">
            <button 
                id="madeni-tab" 
                class="tab-button flex-shrink-0 pb-2 md:pb-3 px-2 md:px-4 transition-colors flex items-center border-b-2 border-green-500 text-green-600 font-medium md:font-semibold whitespace-nowrap"
                data-tab="madeni"
            >
                <i class="fas fa-list mr-1 md:mr-2 text-sm md:text-base"></i>
                <span class="text-xs md:text-sm">Orodha ya Madeni</span>
            </button>
            <button 
                id="marejesho-tab" 
                class="tab-button flex-shrink-0 pb-2 md:pb-3 px-2 md:px-4 transition-colors flex items-center text-gray-500 hover:text-gray-700 whitespace-nowrap"
                data-tab="marejesho"
            >
                <i class="fas fa-history mr-1 md:mr-2 text-sm md:text-base"></i>
                <span class="text-xs md:text-sm">Historia ya Marejesho</span>
            </button>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-300 rounded-xl p-3 md:p-4">
            <div class="flex items-start md:items-center">
                <div class="p-1 md:p-2 rounded-lg bg-green-100 text-green-600 mr-2 md:mr-3 flex-shrink-0">
                    <i class="fas fa-check-circle text-sm md:text-base"></i>
                </div>
                <div class="flex-1">
                    <p class="text-green-800 font-medium text-xs md:text-sm">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-gradient-to-r from-red-50 to-pink-50 border border-red-300 rounded-xl p-3 md:p-4">
            <div class="flex items-start md:items-center">
                <div class="p-1 md:p-2 rounded-lg bg-red-100 text-red-600 mr-2 md:mr-3 flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-sm md:text-base"></i>
                </div>
                <div class="flex-1">
                    <h4 class="text-red-800 font-medium text-xs md:text-sm">Hitilafu katika Uwasilishaji</h4>
                    <ul class="text-red-700 mt-1 space-y-1">
                        @foreach($errors->all() as $error)
                            <li class="text-xs md:text-sm">• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- TAB 1: Orodha ya Madeni -->
    <div id="madeni-tab-content" class="space-y-6 tab-content">
        <!-- Search and Actions - Mobile Optimized -->
        <div class="bg-white rounded-xl md:rounded-2xl shadow-lg border border-gray-100 p-4 md:p-6 card-hover">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-4 md:mb-6 space-y-4 md:space-y-0">
                <h2 class="text-lg md:text-xl font-bold text-gray-800">Orodha ya Madeni Yanayongoza</h2>
                <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3">
                    <div class="relative">
                        <input 
                            type="text" 
                            id="search-input"
                            placeholder="Tafuta deni..." 
                            class="w-full pl-10 pr-4 py-2 md:py-2 text-sm md:text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        >
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400 text-sm md:text-base"></i>
                        </div>
                    </div>
                    <button 
                        onclick="window.print()" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center text-sm md:text-base"
                    >
                        <i class="fas fa-print mr-1 md:mr-2 text-sm md:text-base"></i>
                        <span>Print</span>
                    </button>
                </div>
            </div>

            <!-- Data Table - Responsive -->
            <div class="overflow-x-auto -mx-4 md:mx-0">
                <table class="w-full min-w-full table-auto">
                    <thead>
                        <tr class="bg-gradient-to-r from-green-600 to-green-700">
                            <th class="px-3 md:px-6 py-3 md:py-4 text-left text-xs md:text-sm font-semibold text-white">Tarehe</th>
                            <th class="px-3 md:px-6 py-3 md:py-4 text-left text-xs md:text-sm font-semibold text-white">Bidhaa</th>
                            <th class="px-3 md:px-6 py-3 md:py-4 text-center text-xs md:text-sm font-semibold text-white">Idadi</th>
                            <th class="px-3 md:px-6 py-3 md:py-4 text-right text-xs md:text-sm font-semibold text-white">Baki</th>
                            <th class="px-3 md:px-6 py-3 md:py-4 text-left text-xs md:text-sm font-semibold text-white">Mkopaji</th>
                            <th class="px-3 md:px-6 py-3 md:py-4 text-center text-xs md:text-sm font-semibold text-white print:hidden">Vitendo</th>
                        </tr>
                    </thead>
                    <tbody id="debts-tbody" class="divide-y divide-gray-100">
                        @forelse($madeni as $deni)
                            <tr class="debt-row hover:bg-green-50 transition-all duration-200 
                                @if($deni->baki <= 0) bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-400 @endif"
                                data-debt='@json($deni)'>
                                
                                <td class="px-3 md:px-6 py-3 md:py-4 whitespace-nowrap">
                                    <div class="text-xs md:text-sm font-semibold text-gray-900">
                                        {{ \Carbon\Carbon::parse($deni->tarehe_malipo)->format('d/m/Y') }}
                                    </div>
                                    <div class="text-xs text-green-600 font-medium">
                                        {{ \Carbon\Carbon::parse($deni->tarehe_malipo)->diffForHumans() }}
                                    </div>
                                </td>
                                
                                <td class="px-3 md:px-6 py-3 md:py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8 md:h-10 md:w-10 bg-green-100 rounded-lg flex items-center justify-center text-green-800 font-semibold text-sm md:text-base">
                                            {{ substr($deni->bidhaa->jina, 0, 1) }}
                                        </div>
                                        <div class="ml-2 md:ml-4">
                                            <div class="text-xs md:text-sm font-semibold text-gray-900 debt-product truncate max-w-[100px] md:max-w-none">{{ $deni->bidhaa->jina }}</div>
                                            <div class="text-xs text-green-600 truncate max-w-[100px] md:max-w-none">{{ $deni->bidhaa->aina }}</div>
                                            <div class="text-xs text-gray-500 md:hidden">Bei: {{ number_format($deni->bei, 2) }}</div>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="px-3 md:px-6 py-3 md:py-4 whitespace-nowrap text-center">
                                    <div class="text-xs md:text-sm font-bold text-green-700">{{ $deni->idadi }}</div>
                                </td>
                                
                                <td class="px-3 md:px-6 py-3 md:py-4 whitespace-nowrap text-right">
                                    <div class="text-xs md:text-sm font-bold 
                                        @if($deni->baki <= 0) text-green-700
                                        @elseif($deni->baki > 0) text-red-700 @endif">
                                        {{ number_format($deni->baki, 2) }}
                                    </div>
                                    @if($deni->baki > 0)
                                    <div class="text-xs text-red-600 font-medium">Inayongoza</div>
                                    @else
                                    <div class="text-xs text-green-600 font-medium">Imelipwa</div>
                                    @endif
                                </td>
                                
                                <td class="px-3 md:px-6 py-3 md:py-4 whitespace-nowrap">
                                    <div class="text-xs md:text-sm font-semibold text-gray-900 debt-borrower truncate max-w-[100px] md:max-w-none">{{ $deni->jina_mkopaji }}</div>
                                    @if($deni->simu)
                                    <div class="text-xs text-gray-700 flex items-center md:hidden">
                                        <i class="fas fa-phone text-green-500 mr-1 text-xs"></i>
                                        <span class="debt-phone truncate max-w-[80px]">{{ $deni->simu }}</span>
                                    </div>
                                    @endif
                                </td>
                                
                                <td class="px-3 md:px-6 py-3 md:py-4 whitespace-nowrap text-xs md:text-sm font-medium print:hidden">
                                    <div class="flex justify-center space-x-1 md:space-x-2">
                                        @if($deni->baki > 0)
                                        <button 
                                            class="pay-debt-btn text-green-600 hover:text-green-800 transition-colors transform hover:scale-110 p-1 md:p-0"
                                            title="Lipa Deni"
                                            data-id="{{ $deni->id }}"
                                        >
                                            <i class="fas fa-money-bill-wave text-sm md:text-base"></i>
                                        </button>
                                        @endif
                                        <button 
                                            class="edit-debt-btn text-amber-600 hover:text-amber-800 transition-colors transform hover:scale-110 p-1 md:p-0"
                                            title="Badili Deni"
                                            data-id="{{ $deni->id }}"
                                        >
                                            <i class="fas fa-edit text-sm md:text-base"></i>
                                        </button>
                                        <button 
                                            class="delete-debt-btn text-red-500 hover:text-red-700 transition-colors transform hover:scale-110 p-1 md:p-0"
                                            title="Futa Deni"
                                            data-id="{{ $deni->id }}"
                                            data-name="{{ $deni->jina_mkopaji }}"
                                        >
                                            <i class="fas fa-trash text-sm md:text-base"></i>
                                        </button>
                                        @if($deni->simu)
                                        <a 
                                            href="tel:{{ $deni->simu }}" 
                                            class="text-blue-600 hover:text-blue-800 transition-colors transform hover:scale-110 p-1 md:p-0"
                                            title="Piga Simu"
                                        >
                                            <i class="fas fa-phone text-sm md:text-base"></i>
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-3 md:px-6 py-8 md:py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fas fa-hand-holding-usd text-4xl md:text-5xl text-green-300 mb-3 md:mb-4"></i>
                                        <p class="text-base md:text-lg font-semibold text-gray-600 mb-1 md:mb-2">Hakuna madeni yaliyorekodiwa bado.</p>
                                        <p class="text-xs md:text-sm text-gray-500">Hakuna mikopo inayongoza kwa sasa</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($madeni->count() > 0)
                    <tfoot>
                        <tr class="bg-gradient-to-r from-green-800 to-green-900">
                            <td colspan="2" class="px-3 md:px-6 py-3 md:py-4 text-xs md:text-sm font-bold text-white text-right">Jumla:</td>
                            <td class="px-3 md:px-6 py-3 md:py-4 text-center text-xs md:text-sm font-bold text-white">{{ $madeni->sum('idadi') }}</td>
                            <td class="px-3 md:px-6 py-3 md:py-4 text-right text-xs md:text-sm font-bold text-white">
                                TZS {{ number_format($madeni->sum('baki'), 2) }}
                            </td>
                            <td colspan="2" class="print:hidden"></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 2: Historia ya Marejesho -->
    <div id="marejesho-tab-content" class="tab-content hidden">
        <!-- Search and Actions -->
        <div class="bg-white rounded-xl md:rounded-2xl shadow-lg border border-gray-100 p-4 md:p-6 card-hover">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-4 md:mb-6 space-y-4 md:space-y-0">
                <h2 class="text-lg md:text-xl font-bold text-gray-800">Historia ya Marejesho ya Madeni</h2>
                <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3">
                    <div class="relative">
                        <input 
                            type="text" 
                            id="history-search-input"
                            placeholder="Tafuta marejesho..." 
                            class="w-full pl-10 pr-4 py-2 md:py-2 text-sm md:text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        >
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400 text-sm md:text-base"></i>
                        </div>
                    </div>
                    <button 
                        onclick="window.print()" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center text-sm md:text-base"
                    >
                        <i class="fas fa-print mr-1 md:mr-2 text-sm md:text-base"></i>
                        <span>Print</span>
                    </button>
                </div>
            </div>

            <!-- Data Table - Responsive -->
            <div class="overflow-x-auto -mx-4 md:mx-0">
                <table class="w-full min-w-full table-auto">
                    <thead>
                        <tr class="bg-gradient-to-r from-green-600 to-green-700">
                            <th class="px-3 md:px-6 py-3 md:py-4 text-left text-xs md:text-sm font-semibold text-white">Tarehe</th>
                            <th class="px-3 md:px-6 py-3 md:py-4 text-left text-xs md:text-sm font-semibold text-white">Bidhaa</th>
                            <th class="px-3 md:px-6 py-3 md:py-4 text-center text-xs md:text-sm font-semibold text-white">Idadi</th>
                            <th class="px-3 md:px-6 py-3 md:py-4 text-right text-xs md:text-sm font-semibold text-white">Rejesho</th>
                            <th class="px-3 md:px-6 py-3 md:py-4 text-right text-xs md:text-sm font-semibold text-white">Baki</th>
                            <th class="px-3 md:px-6 py-3 md:py-4 text-left text-xs md:text-sm font-semibold text-white">Mkopaji</th>
                            <th class="px-3 md:px-6 py-3 md:py-4 text-center text-xs md:text-sm font-semibold text-white">Hali</th>
                        </tr>
                    </thead>
                    <tbody id="history-tbody" class="divide-y divide-gray-100">
                        @forelse($historia as $h)
                            <tr class="history-row hover:bg-green-50 transition-all duration-200">
                                
                                <td class="px-3 md:px-6 py-3 md:py-4 whitespace-nowrap">
                                    <div class="text-xs md:text-sm font-semibold text-gray-900">{{ $h['tarehe'] }}</div>
                                </td>
                                
                                <td class="px-3 md:px-6 py-3 md:py-4 whitespace-nowrap">
                                    <div class="text-xs md:text-sm font-semibold text-gray-900 history-product truncate max-w-[100px] md:max-w-none">{{ $h['bidhaa'] }}</div>
                                </td>
                                
                                <td class="px-3 md:px-6 py-3 md:py-4 whitespace-nowrap text-center">
                                    <div class="text-xs md:text-sm font-bold text-green-700">{{ $h['idadi'] }}</div>
                                </td>
                                
                                <td class="px-3 md:px-6 py-3 md:py-4 whitespace-nowrap text-right">
                                    <div class="text-xs md:text-sm font-bold text-green-700">{{ number_format($h['rejesho_leo'], 2) }}</div>
                                    <div class="text-xs text-gray-600">Jumla: {{ number_format($h['jumla_rejeshwa'], 2) }}</div>
                                </td>
                                
                                <td class="px-3 md:px-6 py-3 md:py-4 whitespace-nowrap text-right">
                                    <div class="text-xs md:text-sm font-bold 
                                        @if($h['baki'] == 0) text-green-700
                                        @else text-red-700 @endif">
                                        {{ number_format($h['baki'], 2) }}
                                    </div>
                                </td>
                                
                                <td class="px-3 md:px-6 py-3 md:py-4 whitespace-nowrap">
                                    <div class="text-xs md:text-sm font-semibold text-gray-900 history-borrower truncate max-w-[100px] md:max-w-none">{{ $h['mkopaji'] }}</div>
                                    @if($h['simu'])
                                    <div class="text-xs text-gray-700 md:hidden">{{ $h['simu'] }}</div>
                                    @endif
                                </td>
                                
                                <td class="px-3 md:px-6 py-3 md:py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold 
                                        @if($h['baki'] == 0) bg-green-100 text-green-800 border border-green-200
                                        @elseif($h['status'] === 'Imelipwa') bg-blue-100 text-blue-800 border border-blue-200
                                        @else bg-amber-100 text-amber-800 border border-amber-200 @endif">
                                        <i class="fas 
                                            @if($h['baki'] == 0) fa-check-circle
                                            @elseif($h['status'] === 'Imelipwa') fa-money-bill-wave
                                            @else fa-clock @endif mr-1 text-xs">
                                        </i>
                                        <span class="hidden sm:inline">{{ $h['status'] }}</span>
                                        <span class="sm:hidden">{{ $h['baki'] == 0 ? '✓' : '●' }}</span>
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-3 md:px-6 py-8 md:py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fas fa-history text-4xl md:text-5xl text-green-300 mb-3 md:mb-4"></i>
                                        <p class="text-base md:text-lg font-semibold text-gray-600 mb-1 md:mb-2">Hakuna marejesho yaliyorekodiwa bado.</p>
                                        <p class="text-xs md:text-sm text-gray-500">Hakuna historia ya malipo ya mikopo</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Pay Modal -->
<div id="pay-modal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-xl md:rounded-2xl shadow-xl w-full max-w-md mx-auto z-50 max-h-[90vh] overflow-y-auto">
        <div class="p-4 md:p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Lipa Deni</h3>
        </div>
        <form id="pay-form" method="POST" class="p-4 md:p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1 md:mb-2">Mkopaji</label>
                <p id="pay-borrower-name" class="text-base md:text-lg font-semibold text-gray-900 truncate"></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1 md:mb-2">Bidhaa</label>
                <p id="pay-product-name" class="text-sm md:text-base text-gray-700 truncate"></p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 md:mb-2">Baki Lililobaki</label>
                    <p id="pay-remaining-balance" class="text-base md:text-lg font-bold text-red-600 truncate"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 md:mb-2">Kiasi cha Kulipa</label>
                    <input 
                        type="number" 
                        name="kiasi" 
                        id="pay-amount"
                        step="0.01"
                        min="0.01"
                        class="w-full border border-gray-300 rounded-lg px-3 md:px-4 py-2 md:py-3 focus:outline-none focus:ring-2 focus:ring-green-500 text-right text-sm md:text-base" 
                        required
                    >
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1 md:mb-2">Tarehe ya Malipo</label>
                <input 
                    type="date" 
                    name="tarehe" 
                    id="pay-date"
                    class="w-full border border-gray-300 rounded-lg px-3 md:px-4 py-2 md:py-3 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm md:text-base" 
                    required
                >
            </div>
            <div class="flex justify-end space-x-2 md:space-x-3 pt-4">
                <button 
                    type="button" 
                    id="close-pay-modal"
                    class="px-4 md:px-6 py-2 md:py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm md:text-base"
                >
                    Ghairi
                </button>
                <button 
                    type="submit" 
                    class="px-4 md:px-6 py-2 md:py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm md:text-base"
                >
                    <i class="fas fa-money-bill-wave mr-1 md:mr-2"></i>Thibitisha
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="edit-modal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-xl md:rounded-2xl shadow-xl w-full max-w-md mx-auto z-50 max-h-[90vh] overflow-y-auto">
        <div class="p-4 md:p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Badilisha Taarifa za Deni</h3>
        </div>
        <form id="edit-form" method="POST" class="p-4 md:p-6 space-y-3 md:space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1 md:mb-2">Bidhaa ID</label>
                <input 
                    type="number" 
                    name="bidhaa_id" 
                    id="edit-bidhaa-id"
                    class="w-full border border-gray-300 rounded-lg px-3 md:px-4 py-2 md:py-3 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm md:text-base" 
                    required
                >
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1 md:mb-2">Idadi</label>
                <input 
                    type="number" 
                    name="idadi" 
                    id="edit-quantity"
                    min="1"
                    class="w-full border border-gray-300 rounded-lg px-3 md:px-4 py-2 md:py-3 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm md:text-base" 
                    required
                >
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1 md:mb-2">Bei (TZS)</label>
                <input 
                    type="number" 
                    name="bei" 
                    id="edit-price"
                    step="0.01"
                    min="0.01"
                    class="w-full border border-gray-300 rounded-lg px-3 md:px-4 py-2 md:py-3 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm md:text-base" 
                    required
                >
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1 md:mb-2">Mkopaji</label>
                <input 
                    type="text" 
                    name="jina_mkopaji" 
                    id="edit-borrower-name"
                    class="w-full border border-gray-300 rounded-lg px-3 md:px-4 py-2 md:py-3 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm md:text-base" 
                    required
                >
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1 md:mb-2">Simu</label>
                <input 
                    type="text" 
                    name="simu" 
                    id="edit-phone"
                    class="w-full border border-gray-300 rounded-lg px-3 md:px-4 py-2 md:py-3 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm md:text-base"
                >
            </div>
            <div class="flex justify-end space-x-2 md:space-x-3 pt-4">
                <button 
                    type="button" 
                    id="close-edit-modal"
                    class="px-4 md:px-6 py-2 md:py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm md:text-base"
                >
                    Ghairi
                </button>
                <button 
                    type="submit" 
                    class="px-4 md:px-6 py-2 md:py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm md:text-base"
                >
                    Hifadhi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Modal -->
<div id="delete-modal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-xl md:rounded-2xl shadow-xl w-full max-w-md mx-auto z-50">
        <div class="p-4 md:p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Thibitisha Kufuta Deni</h3>
        </div>
        <div class="p-4 md:p-6">
            <p class="text-gray-700 mb-4 md:mb-6 text-center text-sm md:text-base">
                Una uhakika unataka kufuta deni la 
                "<span id="delete-debt-name" class="font-semibold"></span>"?
                <br class="hidden sm:block">Hatua hii haiwezi kutenduliwa.
            </p>
            <div class="flex justify-center space-x-2 md:space-x-3">
                <button 
                    id="cancel-delete"
                    class="px-4 md:px-6 py-2 md:py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm md:text-base"
                >
                    Ghairi
                </button>
                <form id="delete-form" method="POST">
                    @csrf
                    @method('DELETE')
                    <button 
                        type="submit" 
                        class="px-4 md:px-6 py-2 md:py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm md:text-base"
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
@media (max-width: 640px) {
    .modal-content {
        margin: 0;
        border-radius: 0.75rem;
    }
}

.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

.modal {
    transition: opacity 0.3s ease;
}

.tab-content {
    transition: opacity 0.3s ease;
}

.hidden {
    display: none !important;
}

.debt-row.hidden, .history-row.hidden {
    display: none;
}
</style>
@endpush

@push('scripts')
<script>
class MadeniManager {
    constructor() {
        this.currentTab = 'madeni';
        this.searchQuery = '';
        this.init();
    }

    init() {
        this.bindEvents();
        this.showTab('madeni');
        this.setTodayDate();
    }

    bindEvents() {
        // Tab navigation
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', (e) => {
                const tab = e.target.closest('.tab-button').dataset.tab;
                this.showTab(tab);
            });
        });

        // Search functionality for debts
        const searchInput = document.getElementById('search-input');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                this.searchQuery = e.target.value.toLowerCase();
                this.filterDebts();
            });
        }

        // Search functionality for history
        const historySearchInput = document.getElementById('history-search-input');
        if (historySearchInput) {
            historySearchInput.addEventListener('input', (e) => {
                this.searchQuery = e.target.value.toLowerCase();
                this.filterHistory();
            });
        }

        // Debt actions
        this.bindDebtActions();

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

    bindDebtActions() {
        // Pay buttons
        document.querySelectorAll('.pay-debt-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                const debtId = e.target.closest('.pay-debt-btn').dataset.id;
                const row = e.target.closest('.debt-row');
                const debtData = JSON.parse(row.dataset.debt);
                this.openPayModal(debtData);
            });
        });

        // Edit buttons
        document.querySelectorAll('.edit-debt-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                const debtId = e.target.closest('.edit-debt-btn').dataset.id;
                const row = e.target.closest('.debt-row');
                const debtData = JSON.parse(row.dataset.debt);
                this.openEditModal(debtData);
            });
        });

        // Delete buttons
        document.querySelectorAll('.delete-debt-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                const debtId = e.target.closest('.delete-debt-btn').dataset.id;
                const debtName = e.target.closest('.delete-debt-btn').dataset.name;
                this.openDeleteModal(debtId, debtName);
            });
        });
    }

    bindModalEvents() {
        // Pay modal
        const payModal = document.getElementById('pay-modal');
        const closePayBtn = document.getElementById('close-pay-modal');

        closePayBtn.addEventListener('click', () => {
            payModal.classList.add('hidden');
        });

        payModal.addEventListener('click', (e) => {
            if (e.target === payModal || e.target.classList.contains('modal-overlay')) {
                payModal.classList.add('hidden');
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
                payModal.classList.add('hidden');
                editModal.classList.add('hidden');
                deleteModal.classList.add('hidden');
            }
        });
    }

    filterDebts() {
        const rows = document.querySelectorAll('.debt-row');
        
        rows.forEach(row => {
            const borrower = row.querySelector('.debt-borrower').textContent.toLowerCase();
            const product = row.querySelector('.debt-product').textContent.toLowerCase();
            const phone = row.querySelector('.debt-phone')?.textContent.toLowerCase() || '';
            
            const matches = borrower.includes(this.searchQuery) || 
                           product.includes(this.searchQuery) || 
                           phone.includes(this.searchQuery);
            
            if (matches || this.searchQuery === '') {
                row.classList.remove('hidden');
            } else {
                row.classList.add('hidden');
            }
        });
    }

    filterHistory() {
        const rows = document.querySelectorAll('.history-row');
        
        rows.forEach(row => {
            const borrower = row.querySelector('.history-borrower').textContent.toLowerCase();
            const product = row.querySelector('.history-product').textContent.toLowerCase();
            
            const matches = borrower.includes(this.searchQuery) || 
                           product.includes(this.searchQuery);
            
            if (matches || this.searchQuery === '') {
                row.classList.remove('hidden');
            } else {
                row.classList.add('hidden');
            }
        });
    }

    openPayModal(debt) {
        // Populate pay modal
        document.getElementById('pay-borrower-name').textContent = debt.jina_mkopaji;
        document.getElementById('pay-product-name').textContent = debt.bidhaa.jina;
        document.getElementById('pay-remaining-balance').textContent = `TZS ${parseFloat(debt.baki).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
        
        // Set form action and max amount
        document.getElementById('pay-form').action = `/madeni/${debt.id}/rejesha`;
        document.getElementById('pay-amount').value = parseFloat(debt.baki);
        document.getElementById('pay-amount').max = parseFloat(debt.baki);

        // Show modal
        document.getElementById('pay-modal').classList.remove('hidden');
    }

    openEditModal(debt) {
        // Populate edit form
        document.getElementById('edit-bidhaa-id').value = debt.bidhaa_id;
        document.getElementById('edit-quantity').value = debt.idadi;
        document.getElementById('edit-price').value = debt.bei;
        document.getElementById('edit-borrower-name').value = debt.jina_mkopaji;
        document.getElementById('edit-phone').value = debt.simu || '';

        // Set form action
        document.getElementById('edit-form').action = `/madeni/${debt.id}`;

        // Show modal
        document.getElementById('edit-modal').classList.remove('hidden');
    }

    openDeleteModal(debtId, debtName) {
        // Populate delete modal
        document.getElementById('delete-debt-name').textContent = debtName;
        document.getElementById('delete-form').action = `/madeni/${debtId}`;

        // Show modal
        document.getElementById('delete-modal').classList.remove('hidden');
    }

    setTodayDate() {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('pay-date').value = today;
    }
}

// Initialize the application when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    new MadeniManager();
});
</script>
@endpush