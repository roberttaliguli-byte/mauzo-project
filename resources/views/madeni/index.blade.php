@extends('layouts.app')

@section('title', 'Madeni - DEMODAY')

@section('page-title', 'Madeni')
@section('page-subtitle', 'Usimamizi wa mikopo na malipo - ' . now()->format('d/m/Y'))

@section('content')
<div class="space-y-6">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow-lg border border-green-200 p-6 card-hover">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-green-100 text-green-600 mr-4">
                    <i class="fas fa-money-bill-wave text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-black font-medium">Jumla ya Madeni</p>
                    <h3 class="text-2xl font-bold text-gray-800">TZS {{ number_format($madeni->sum('baki'), 2) }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl shadow-lg border border-red-100 p-6 card-hover">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-red-100 text-red-600 mr-4">
                    <i class="fas fa-hand-holding-usd text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-black font-medium">Mikopo yote</p>
                    <h3 class="text-2xl font-bold text-black">{{ $madeni->count() }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl shadow-lg border border-green-100 p-6 card-hover">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-blue-100 text-blue-600 mr-4">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-black font-medium">Wakopaji</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $madeni->pluck('jina_mkopaji')->unique()->count() }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl shadow-lg border border-yellow-400 p-6 card-hover">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-amber-100 text-amber-600 mr-4">
                    <i class="fas fa-boxes text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-black-500 font-medium">Bidhaa Zilizokopwa</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $madeni->sum('idadi') }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Page Navigation Tabs -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 card-hover">
        <div class="flex space-x-6 border-b border-amber-600">
            <button 
                id="madeni-tab" 
                class="tab-button pb-3 px-1 transition-colors flex items-center border-b-2 border-green-500 text-green-600 font-semibold"
                data-tab="madeni"
            >
                <i class="fas fa-list mr-2"></i>Orodha ya Madeni
            </button>
            <button 
                id="marejesho-tab" 
                class="tab-button pb-3 px-1 transition-colors flex items-center text-gray-500 hover:text-gray-700"
                data-tab="marejesho"
            >
                <i class="fas fa-history mr-2"></i>Historia ya Marejesho
            </button>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-300 rounded-xl p-4">
            <div class="flex items-center">
                <div class="p-2 rounded-lg bg-green-100 text-green-600 mr-3">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <p class="text-green-800 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-gradient-to-r from-red-50 to-pink-50 border border-red-300 rounded-xl p-4">
            <div class="flex items-center">
                <div class="p-2 rounded-lg bg-red-100 text-red-600 mr-3">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div>
                    <h4 class="text-red-800 font-medium">Hitilafu katika Uwasilishaji</h4>
                    <ul class="list-disc list-inside text-red-700 mt-1">
                        @foreach($errors->all() as $error)
                            <li class="text-sm">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- TAB 1: Orodha ya Madeni -->
    <div id="madeni-tab-content" class="space-y-6 tab-content">
        <!-- Search and Actions -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 card-hover">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-800">Orodha ya Madeni Yanayongoza</h2>
                <div class="flex space-x-3">
                    <div class="relative">
                        <input 
                            type="text" 
                            id="search-input"
                            placeholder="Tafuta deni..." 
                            class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        >
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                    <button 
                        onclick="window.print()" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center"
                    >
                        <i class="fas fa-print mr-2"></i> Print
                    </button>
                </div>
            </div>

            <!-- Data Table -->
            <div class="overflow-x-auto">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="bg-gradient-to-r from-green-600 to-green-700">
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white">Tarehe ya Mwisho</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white">Bidhaa</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-white">Idadi</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-white">Bei (TZS)</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-white">Baki (TZS)</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white">Mkopaji</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white">Mawasiliano</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-white print:hidden">Vitendo</th>
                        </tr>
                    </thead>
                    <tbody id="debts-tbody" class="divide-y divide-gray-100">
                        @forelse($madeni as $deni)
                            <tr class="debt-row hover:bg-green-50 transition-all duration-200 
                                @if($deni->baki <= 0) bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-400 @endif"
                                data-debt='@json($deni)'>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">
                                        {{ \Carbon\Carbon::parse($deni->tarehe_malipo)->format('d/m/Y') }}
                                    </div>
                                    <div class="text-xs text-green-600 font-medium">
                                        {{ \Carbon\Carbon::parse($deni->tarehe_malipo)->diffForHumans() }}
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center text-green-800 font-semibold">
                                            {{ substr($deni->bidhaa->jina, 0, 1) }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-semibold text-gray-900 debt-product">{{ $deni->bidhaa->jina }}</div>
                                            <div class="text-xs text-green-600">{{ $deni->bidhaa->aina }}</div>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="text-sm font-bold text-green-700">{{ $deni->idadi }}</div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="text-sm font-semibold text-gray-900">{{ number_format($deni->bei, 2) }}</div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="text-sm font-bold 
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
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900 debt-borrower">{{ $deni->jina_mkopaji }}</div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($deni->simu)
                                    <div class="flex items-center text-sm text-gray-700">
                                        <i class="fas fa-phone text-green-500 mr-2"></i>
                                        <span class="debt-phone">{{ $deni->simu }}</span>
                                    </div>
                                    @else
                                    <div class="text-sm text-gray-500 italic">--</div>
                                    @endif
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium print:hidden">
                                    <div class="flex justify-center space-x-2">
                                        @if($deni->baki > 0)
                                        <button 
                                            class="pay-debt-btn text-green-600 hover:text-green-800 transition-colors transform hover:scale-110"
                                            title="Lipa Deni"
                                            data-id="{{ $deni->id }}"
                                        >
                                            <i class="fas fa-money-bill-wave"></i>
                                        </button>
                                        @endif
                                        <button 
                                            class="edit-debt-btn text-amber-600 hover:text-amber-800 transition-colors transform hover:scale-110"
                                            title="Badili Deni"
                                            data-id="{{ $deni->id }}"
                                        >
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button 
                                            class="delete-debt-btn text-red-500 hover:text-red-700 transition-colors transform hover:scale-110"
                                            title="Futa Deni"
                                            data-id="{{ $deni->id }}"
                                            data-name="{{ $deni->jina_mkopaji }}"
                                        >
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @if($deni->simu)
                                        <a 
                                            href="tel:{{ $deni->simu }}" 
                                            class="text-blue-600 hover:text-blue-800 transition-colors transform hover:scale-110"
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
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fas fa-hand-holding-usd text-5xl text-green-300 mb-4"></i>
                                        <p class="text-lg font-semibold text-gray-600 mb-2">Hakuna madeni yaliyorekodiwa bado.</p>
                                        <p class="text-sm text-gray-500 mb-4">Hakuna mikopo inayongoza kwa sasa</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($madeni->count() > 0)
                    <tfoot>
                        <tr class="bg-gradient-to-r from-green-800 to-green-900">
                            <td colspan="3" class="px-6 py-4 text-sm font-bold text-white text-right">Jumla:</td>
                            <td class="px-6 py-4 text-right text-sm font-bold text-white">
                                TZS {{ number_format($madeni->sum('bei'), 2) }}
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-bold text-white">
                                TZS {{ number_format($madeni->sum('baki'), 2) }}
                            </td>
                            <td colspan="3" class="print:hidden"></td>
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
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 card-hover">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-800">Historia ya Marejesho ya Madeni</h2>
                <div class="flex space-x-3">
                    <div class="relative">
                        <input 
                            type="text" 
                            id="history-search-input"
                            placeholder="Tafuta marejesho..." 
                            class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        >
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                    <button 
                        onclick="window.print()" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center"
                    >
                        <i class="fas fa-print mr-2"></i> Print
                    </button>
                </div>
            </div>

            <!-- Data Table -->
            <div class="overflow-x-auto">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="bg-gradient-to-r from-green-600 to-green-700">
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white">Tarehe ya Mwisho</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white">Bidhaa</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-white">Idadi</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-white">Deni Lote</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-white">Jumla Rejeshwa</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-white">Rejesho la Mwisho</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-white">Baki</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white">Mkopaji</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white">Mawasiliano</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-white">Hali</th>
                        </tr>
                    </thead>
                    <tbody id="history-tbody" class="divide-y divide-gray-100">
                        @forelse($historia as $h)
                            <tr class="history-row hover:bg-green-50 transition-all duration-200">
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">{{ $h['tarehe'] }}</div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900 history-product">{{ $h['bidhaa'] }}</div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="text-sm font-bold text-green-700">{{ $h['idadi'] }}</div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="text-sm font-semibold text-gray-900">{{ number_format($h['deni_lote'], 2) }}</div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="text-sm font-bold text-green-700">{{ number_format($h['jumla_rejeshwa'], 2) }}</div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="text-sm font-semibold text-blue-700">{{ number_format($h['rejesho_leo'], 2) }}</div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="text-sm font-bold 
                                        @if($h['baki'] == 0) text-green-700
                                        @else text-red-700 @endif">
                                        {{ number_format($h['baki'], 2) }}
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900 history-borrower">{{ $h['mkopaji'] }}</div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-700">{{ $h['simu'] ?: '--' }}</div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold 
                                        @if($h['baki'] == 0) bg-green-100 text-green-800 border border-green-200
                                        @elseif($h['status'] === 'Imelipwa') bg-blue-100 text-blue-800 border border-blue-200
                                        @else bg-amber-100 text-amber-800 border border-amber-200 @endif">
                                        <i class="fas 
                                            @if($h['baki'] == 0) fa-check-circle
                                            @elseif($h['status'] === 'Imelipwa') fa-money-bill-wave
                                            @else fa-clock @endif mr-1">
                                        </i>
                                        {{ $h['status'] }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fas fa-history text-5xl text-green-300 mb-4"></i>
                                        <p class="text-lg font-semibold text-gray-600 mb-2">Hakuna marejesho yaliyorekodiwa bado.</p>
                                        <p class="text-sm text-gray-500 mb-4">Hakuna historia ya malipo ya mikopo</p>
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
<div id="pay-modal" class="modal fixed inset-0 z-50 flex items-center justify-center hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 z-50">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Lipa Deni</h3>
        </div>
        <form id="pay-form" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Mkopaji</label>
                <p id="pay-borrower-name" class="text-lg font-semibold text-gray-900"></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Bidhaa</label>
                <p id="pay-product-name" class="text-base text-gray-700"></p>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Baki Lililobaki</label>
                    <p id="pay-remaining-balance" class="text-lg font-bold text-red-600"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kiasi cha Kulipa</label>
                    <input 
                        type="number" 
                        name="kiasi" 
                        id="pay-amount"
                        step="0.01"
                        min="0.01"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 text-right" 
                        required
                    >
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tarehe ya Malipo</label>
                <input 
                    type="date" 
                    name="tarehe" 
                    id="pay-date"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500" 
                    required
                >
            </div>
            <div class="flex justify-end space-x-3 pt-4">
                <button 
                    type="button" 
                    id="close-pay-modal"
                    class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
                >
                    Ghairi
                </button>
                <button 
                    type="submit" 
                    class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700"
                >
                    <i class="fas fa-money-bill-wave mr-2"></i>Thibitisha Malipo
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="edit-modal" class="modal fixed inset-0 z-50 flex items-center justify-center hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 z-50">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Badilisha Taarifa za Deni</h3>
        </div>
        <form id="edit-form" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Bidhaa ID</label>
                <input 
                    type="number" 
                    name="bidhaa_id" 
                    id="edit-bidhaa-id"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500" 
                    required
                >
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Idadi</label>
                <input 
                    type="number" 
                    name="idadi" 
                    id="edit-quantity"
                    min="1"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500" 
                    required
                >
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Bei (TZS)</label>
                <input 
                    type="number" 
                    name="bei" 
                    id="edit-price"
                    step="0.01"
                    min="0.01"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500" 
                    required
                >
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Mkopaji</label>
                <input 
                    type="text" 
                    name="jina_mkopaji" 
                    id="edit-borrower-name"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500" 
                    required
                >
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Simu</label>
                <input 
                    type="text" 
                    name="simu" 
                    id="edit-phone"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500"
                >
            </div>
            <div class="flex justify-end space-x-3 pt-4">
                <button 
                    type="button" 
                    id="close-edit-modal"
                    class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
                >
                    Ghairi
                </button>
                <button 
                    type="submit" 
                    class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700"
                >
                    Hifadhi Mabadiliko
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Modal -->
<div id="delete-modal" class="modal fixed inset-0 z-50 flex items-center justify-center hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 z-50">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Thibitisha Kufuta Deni</h3>
        </div>
        <div class="p-6">
            <p class="text-gray-700 mb-6 text-center">
                Una uhakika unataka kufuta deni la 
                "<span id="delete-debt-name" class="font-semibold"></span>"?
                <br>Hatua hii haiwezi kutenduliwa.
            </p>
            <div class="flex justify-center space-x-3">
                <button 
                    id="cancel-delete"
                    class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
                >
                    Ghairi
                </button>
                <form id="delete-form" method="POST">
                    @csrf
                    @method('DELETE')
                    <button 
                        type="submit" 
                        class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700"
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