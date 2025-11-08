now pop stoped but lipa. edit and futa not working @extends('layouts.app')

@section('title', 'Madeni - DEMODAY')

@section('page-title', 'Madeni')
@section('page-subtitle', 'Usimamizi wa mikopo na malipo - ' . now()->format('d/m/Y'))

@section('content')
<div x-data="madeniApp()" class="space-y-6">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-green-300 rounded-2xl shadow-lg border border-green-200 p-6 card-hover">
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
        
        <div class="bg-amber-300 rounded-2xl shadow-lg border border-amber-400 p-6 card-hover">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-red-100 text-red-600 mr-4">
                    <i class="fas fa-hand-holding-usd text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-black-500 font-medium">Mikopo Inayongoza</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $madeni->count() }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-emerald-300 rounded-2xl shadow-lg border border-green-100 p-6 card-hover">
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
        
        <div class="bg-yellow-300 rounded-2xl shadow-lg border border-yellow4700 p-6 card-hover">
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
                @click="activeTab = 'madeni'" 
                :class="activeTab === 'madeni' ? 'border-b-2 border-green-500 text-green-600 font-semibold' : 'text-gray-500 hover:text-gray-700'" 
                class="pb-3 px-1 transition-colors flex items-center"
            >
                <i class="fas fa-list mr-2"></i>Orodha ya Madeni
            </button>
            <button 
                @click="activeTab = 'marejesho'" 
                :class="activeTab === 'marejesho' ? 'border-b-2 border-green-500 text-green-600 font-semibold' : 'text-gray-500 hover:text-gray-700'" 
                class="pb-3 px-1 transition-colors flex items-center"
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
    <div x-show="activeTab === 'madeni'" class="space-y-6">
        <!-- Search and Actions -->
        <div class="bg-green-100 rounded-2xl shadow-lg border border-gray-100 p-6 card-hover">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-800">Orodha ya Madeni Yanayongoza</h2>
                <div class="flex space-x-3">
                    <div class="relative">
                        <input 
                            type="text" 
                            placeholder="Tafuta deni..." 
                            x-model="searchQuery"
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
                    <tbody class="divide-y divide-gray-100">
                        @forelse($madeni as $deni)
                            <tr class="hover:bg-green-50 transition-all duration-200 
                                @if($deni->baki <= 0) bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-400 @endif"
                                x-show="searchQuery === '' || 
                                       '{{ strtolower($deni->jina_mkopaji) }}'.includes(searchQuery.toLowerCase()) || 
                                       '{{ strtolower($deni->bidhaa->jina) }}'.includes(searchQuery.toLowerCase())">
                                
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
                                            <div class="text-sm font-semibold text-gray-900">{{ $deni->bidhaa->jina }}</div>
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
                                    <div class="text-sm font-semibold text-gray-900">{{ $deni->jina_mkopaji }}</div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($deni->simu)
                                    <div class="flex items-center text-sm text-gray-700">
                                        <i class="fas fa-phone text-green-500 mr-2"></i>
                                        {{ $deni->simu }}
                                    </div>
                                    @else
                                    <div class="text-sm text-gray-500 italic">--</div>
                                    @endif
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium print:hidden">
                                    <div class="flex justify-center space-x-2">
                                        @if($deni->baki > 0)
                                        <button 
                                            @click="openPayModal({{ $deni->toJson() }})" 
                                            class="text-green-600 hover:text-green-800 transition-colors transform hover:scale-110"
                                            title="Lipa Deni"
                                        >
                                            <i class="fas fa-money-bill-wave"></i>
                                        </button>
                                        @endif
                                        <button 
                                            @click="openEditModal({{ $deni->toJson() }})" 
                                            class="text-amber-600 hover:text-amber-800 transition-colors transform hover:scale-110"
                                            title="Badili Deni"
                                        >
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button 
                                            @click="openDeleteModal({{ $deni->id }}, '{{ $deni->jina_mkopaji }}')" 
                                            class="text-red-500 hover:text-red-700 transition-colors transform hover:scale-110"
                                            title="Futa Deni"
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
    <div x-show="activeTab === 'marejesho'" class="space-y-6">
        <!-- Search and Actions -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 card-hover">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-800">Historia ya Marejesho ya Madeni</h2>
                <div class="flex space-x-3">
                    <div class="relative">
                        <input 
                            type="text" 
                            placeholder="Tafuta marejesho..." 
                            x-model="searchQuery"
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
                    <tbody class="divide-y divide-gray-100">
                        @forelse($historia as $h)
                            <tr class="hover:bg-green-50 transition-all duration-200"
                                x-show="searchQuery === '' || 
                                       '{{ strtolower($h['mkopaji']) }}'.includes(searchQuery.toLowerCase()) || 
                                       '{{ strtolower($h['bidhaa']) }}'.includes(searchQuery.toLowerCase())">
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">{{ $h['tarehe'] }}</div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">{{ $h['bidhaa'] }}</div>
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
                                    <div class="text-sm font-semibold text-gray-900">{{ $h['mkopaji'] }}</div>
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
<div 
    x-show="showPayModal" 
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 scale-95"
    x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-95"
>
    <div 
        class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4"
        @click.outside="showPayModal = false"
    >
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Lipa Deni</h3>
        </div>
        <form :action="'/madeni/' + payingDebt.id + '/rejesha'" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Mkopaji</label>
                <p class="text-lg font-semibold text-gray-900" x-text="payingDebt.jina_mkopaji"></p>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Baki Lililobaki</label>
                    <p class="text-lg font-bold text-red-600" x-text="'TZS ' + payingDebt.baki?.toLocaleString()"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kiasi cha Kulipa</label>
                    <input 
                        type="number" 
                        name="kiasi" 
                        x-model="payAmount"
                        :max="payingDebt.baki"
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
                    x-model="payDate"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500" 
                    required
                >
            </div>
            <div class="flex justify-end space-x-3 pt-4">
                <button 
                    type="button" 
                    @click="showPayModal = false"
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
<div 
    x-show="showEditModal" 
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 scale-95"
    x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-95"
>
    <div 
        class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4"
        @click.outside="showEditModal = false"
    >
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Badilisha Taarifa za Deni</h3>
        </div>
        <form :action="'/madeni/' + editingDebt.id" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Bidhaa</label>
                <input 
                    type="text" 
                    name="bidhaa" 
                    x-model="editingDebt.bidhaa?.jina"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500" 
                    required
                >
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Idadi</label>
                <input 
                    type="number" 
                    name="idadi" 
                    x-model="editingDebt.idadi"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500" 
                    required
                >
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Bei (TZS)</label>
                <input 
                    type="number" 
                    name="bei" 
                    x-model="editingDebt.bei"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500" 
                    required
                >
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Mkopaji</label>
                <input 
                    type="text" 
                    name="jina_mkopaji" 
                    x-model="editingDebt.jina_mkopaji"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500" 
                    required
                >
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Simu</label>
                <input 
                    type="text" 
                    name="simu" 
                    x-model="editingDebt.simu"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500"
                >
            </div>
            <div class="flex justify-end space-x-3 pt-4">
                <button 
                    type="button" 
                    @click="showEditModal = false"
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
<div 
    x-show="showDeleteModal" 
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 scale-95"
    x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-95"
>
    <div 
        class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4"
        @click.outside="showDeleteModal = false"
    >
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Thibitisha Kufuta Deni</h3>
        </div>
        <div class="p-6">
            <p class="text-gray-700 mb-6 text-center">
                Una uhakika unataka kufuta deni la 
                "<span x-text="deletingDebtName" class="font-semibold"></span>"?
                <br>Hatua hii haiwezi kutenduliwa.
            </p>
            <div class="flex justify-center space-x-3">
                <button 
                    @click="showDeleteModal = false"
                    class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
                >
                    Ghairi
                </button>
                <form :action="'/madeni/' + deletingDebtId" method="POST">
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

@push('scripts')
<script>
function madeniApp() {
    return {
        activeTab: 'madeni',
        searchQuery: '',
        showPayModal: false,
        showEditModal: false,
        showDeleteModal: false,
        payingDebt: {},
        editingDebt: {},
        deletingDebtId: null,
        deletingDebtName: '',
        payAmount: 0,
        payDate: new Date().toISOString().split('T')[0],

        openPayModal(debt) {
            this.payingDebt = { ...debt };
            this.payAmount = debt.baki;
            this.showPayModal = true;
        },
        openEditModal(debt) {
            this.editingDebt = { ...debt };
            this.showEditModal = true;
        },
        openDeleteModal(id, name) {
            this.deletingDebtId = id;
            this.deletingDebtName = name;
            this.showDeleteModal = true;
        }
    }
}

</script>
@endpush
