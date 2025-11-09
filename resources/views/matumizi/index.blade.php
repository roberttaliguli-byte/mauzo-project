@extends('layouts.app')

@section('title', 'Matumizi - DEMODAY')
@section('page-title', 'Matumizi')
@section('page-subtitle', 'Usimamizi wa matumizi yote - ' . now()->format('d/m/Y'))

@section('content')
<div class="space-y-6">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 card-hover">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-red-100 text-red-600 mr-4">
                    <i class="fas fa-money-bill-wave text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Jumla ya Matumizi (Mwezi)</p>
                    <h3 class="text-2xl font-bold text-gray-800">TZS @php echo number_format($matumizi->sum('gharama'), 2); @endphp</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 card-hover">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-blue-100 text-blue-600 mr-4">
                    <i class="fas fa-calendar-day text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Matumizi Ya Leo</p>
                    <h3 class="text-2xl font-bold text-gray-800">TZS @php 
                        $todayTotal = $matumizi->where('created_at', '>=', today())->sum('gharama');
                        echo number_format($todayTotal, 2);
                    @endphp</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 card-hover">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-green-100 text-green-600 mr-4">
                    <i class="fas fa-list text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Idadi ya Matumizi</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $matumizi->count() }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 card-hover">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-purple-100 text-purple-600 mr-4">
                    <i class="fas fa-chart-pie text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Wastani wa Matumizi</p>
                    <h3 class="text-2xl font-bold text-gray-800">TZS @php 
                        $average = $matumizi->count() > 0 ? $matumizi->avg('gharama') : 0;
                        echo number_format($average, 2);
                    @endphp</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Page Navigation Tabs -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 card-hover">
        <div class="flex space-x-6 border-b border-gray-200">
            <button 
                id="taarifa-tab" 
                class="tab-button pb-3 px-1 transition-colors flex items-center border-b-2 border-green-500 text-green-600 font-semibold"
                data-tab="taarifa"
            >
                <i class="fas fa-table mr-2"></i>Taarifa za Matumizi
            </button>
            <button 
                id="ingiza-tab" 
                class="tab-button pb-3 px-1 transition-colors flex items-center text-gray-500 hover:text-gray-700"
                data-tab="ingiza"
            >
                <i class="fas fa-plus-circle mr-2"></i>Ingiza Matumizi Mpya
            </button>
            <button 
                id="sajili-tab" 
                class="tab-button pb-3 px-1 transition-colors flex items-center text-gray-500 hover:text-gray-700"
                data-tab="sajili"
            >
                <i class="fas fa-tags mr-2"></i>Sajili Matumizi
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

    <!-- TAB 1: Taarifa za Matumizi -->
    <div id="taarifa-tab-content" class="space-y-6 tab-content">
        <!-- Search and Actions -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 card-hover">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-800">Orodha ya Matumizi</h2>
                <div class="flex space-x-3">
                    <div class="relative">
                        <input 
                            type="text" 
                            id="search-input"
                            placeholder="Tafuta matumizi..." 
                            class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        >
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                    <button 
                        onclick="window.print()" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors flex items-center"
                    >
                        <i class="fas fa-print mr-2"></i> Print
                    </button>
                </div>
            </div>

            <!-- Data Table -->
            <div class="overflow-x-auto">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="bg-gradient-to-r from-green-600 to-green-700 border-b">
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white">Tarehe & Muda</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white">Aina ya Matumizi</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white">Maelezo</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-white">Gharama (TZS)</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-white print:hidden">Vitendo</th>
                        </tr>
                    </thead>
                    <tbody id="expenses-tbody" class="divide-y divide-gray-100">
                        @forelse($matumizi as $item)
                            <tr class="expense-row hover:bg-green-50 transition-all duration-200 
                                @if($item->created_at->format('Y-m-d') === now()->format('Y-m-d')) bg-gradient-to-r from-amber-50 to-yellow-50 border-l-4 border-amber-400 @else bg-white @endif"
                                data-expense='@json($item)'>
                            
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-800">{{ $item->created_at->format('d/m/Y') }}</div>
                                    <div class="text-xs text-green-600 font-medium">{{ $item->created_at->format('H:i') }}</div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold expense-type
                                        @if($item->aina === 'Mshahara') bg-green-100 text-green-800 border border-green-200
                                        @elseif($item->aina === 'Bank') bg-emerald-100 text-emerald-800 border border-emerald-200
                                        @elseif($item->aina === 'Kodi TRA') bg-teal-100 text-teal-800 border border-teal-200
                                        @elseif($item->aina === 'Kodi Pango') bg-lime-100 text-lime-800 border border-lime-200
                                        @else bg-green-50 text-green-700 border border-green-100 @endif">
                                        {{ $item->aina }}
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-700 expense-description">{{ $item->maelezo ?: '--' }}</div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="text-sm font-bold text-green-700">{{ number_format($item->gharama, 2) }}</div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium print:hidden">
                                    <div class="flex justify-center space-x-3">
                                        <button 
                                            class="edit-expense-btn text-amber-600 hover:text-amber-800 transition-colors transform hover:scale-110"
                                            title="Badili"
                                            data-id="{{ $item->id }}"
                                        >
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button 
                                            class="delete-expense-btn text-red-500 hover:text-red-700 transition-colors transform hover:scale-110"
                                            title="Futa"
                                            data-id="{{ $item->id }}"
                                            data-name="{{ $item->aina }}"
                                        >
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fas fa-receipt text-5xl text-green-300 mb-4"></i>
                                        <p class="text-lg font-semibold text-gray-600 mb-2">Hakuna matumizi bado.</p>
                                        <p class="text-sm text-gray-500 mb-4">Anza kwa kuongeza matumizi yako ya kwanza</p>
                                        <button 
                                            id="go-to-add-expense"
                                            class="bg-gradient-to-r from-green-600 to-green-700 text-white px-6 py-3 rounded-lg hover:from-green-700 hover:to-green-800 transition-all duration-300 transform hover:scale-105 flex items-center shadow-lg"
                                        >
                                            <i class="fas fa-plus-circle mr-2"></i> Ingiza Matumizi ya Kwanza
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($matumizi->count() > 0)
                    <tfoot>
                        <tr class="bg-gradient-to-r from-green-800 to-green-900">
                            <td colspan="3" class="px-6 py-4 text-sm font-bold text-white text-right">Jumla ya Matumizi:</td>
                            <td class="px-6 py-4 text-right">
                                <div class="text-sm font-bold text-white text-lg">
                                    TZS {{ number_format($matumizi->sum('gharama'), 2) }}
                                </div>
                            </td>
                            <td class="print:hidden"></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 2: Ingiza Matumizi Mpya -->
    <div id="ingiza-tab-content" class="tab-content hidden">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 card-hover">
            <h2 class="text-xl font-bold text-gray-800 mb-6">Ingiza Matumizi Mpya</h2>
            <form method="POST" action="{{ route('matumizi.store') }}" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Aina ya Matumizi</label>
                        <select name="aina" id="expense-type" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" required>
                            <option value="">-- Chagua Aina ya Matumizi --</option>
                            <option value="Bank">Bank</option>
                            <option value="Mshahara">Mshahara</option>
                            <option value="Kodi TRA">Kodi TRA</option>
                            <option value="Kodi Pango">Kodi Pango</option>
                            @if(isset($aina_za_matumizi) && count($aina_za_matumizi) > 0)
                                @foreach($aina_za_matumizi as $aina)
                                    <option value="{{ $aina->jina }}">{{ $aina->jina }}</option>
                                @endforeach
                            @endif
                        
                        </select>

                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Maelezo</label>
                        <input 
                            type="text" 
                            name="maelezo" 
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                            placeholder="Maelezo ya ziada kuhusu matumizi..."
                            value="{{ old('maelezo') }}"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kiasi cha Gharama (TZS)</label>
                        <input 
                            type="number" 
                            name="gharama" 
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                            placeholder="Ingiza kiasi cha matumizi" 
                            step="0.01"
                            min="0"
                            value="{{ old('gharama') }}"
                            required
                        >
                    </div>

                    <div class="flex items-end">
                        <div class="w-full">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tarehe</label>
                            <input 
                                type="date" 
                                name="tarehe" 
                                value="{{ old('tarehe', now()->format('Y-m-d')) }}"
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            >
                        </div>
                    </div>
                </div>

                <div class="flex gap-4 pt-4">
                    <button 
                        type="submit" 
                        class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors flex items-center"
                    >
                        <i class="fas fa-save mr-2"></i> Hifadhi Matumizi
                    </button>
                    <button 
                        type="reset" 
                        class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 transition-colors flex items-center"
                    >
                        <i class="fas fa-redo mr-2"></i> Safisha Fomu
                    </button>
                </div>
            </form>
        </div>
    </div>
<!-- TAB 3: Sajili Matumizi -->
<div id="sajili-tab-content" class="tab-content hidden">
    <div class="bg-white rounded-xl shadow border border-gray-100 p-4 card-hover">
        <h2 class="text-lg font-bold text-gray-800 mb-4">Sajili Aina Mpya ya Matumizi</h2>
        <form method="POST" action="{{ route('matumizi.sajili-aina') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jina la Aina ya Matumizi</label>
                    <input 
                        type="text" 
                        name="jina" 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                        placeholder="Mfano: Umeme, Maji, Usafiri..." 
                        value="{{ old('jina') }}"
                        required
                    >
                    <p class="text-xs text-gray-500 mt-1">Jina la matumizi utakayoweza kutumia baadaye</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rangi ya Kipekee</label>
                    <select name="rangi" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="">-- Chagua Rangi --</option>
                        <option value="bg-blue-100 text-blue-800 border-blue-200">Bluu</option>
                        <option value="bg-green-100 text-green-800 border-green-200">Kijani</option>
                        <option value="bg-red-100 text-red-800 border-red-200">Nyekundu</option>
                        <option value="bg-yellow-100 text-yellow-800 border-yellow-200">Manjano</option>
                        <option value="bg-purple-100 text-purple-800 border-purple-200">Zambarau</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Aina ya Kategoria</label>
                    <select name="kategoria" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="">-- Chagua Kategoria --</option>
                        <option value="msharahara">Mshahara</option>
                        <option value="kodi">Kodi</option>
                        <option value="utawala">Utawala</option>
                        <option value="usafiri">Usafiri</option>
                        <option value="lishe">Lishe</option>
                        <option value="matengenezo">Matengenezo</option>
                        <option value="mengineyo">Mengineyo</option>
                    </select>
                </div>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-500 mr-2 mt-0.5 text-sm"></i>
                    <p class="text-xs text-blue-700 leading-relaxed">
                        Aina mpya ya matumizi itaonekana kwenye orodha ya aina za matumizi wakati wa kuongeza matumizi mapya.
                    </p>
                </div>
            </div>

            <div class="flex gap-3 pt-3">
                <button 
                    type="submit" 
                    class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors flex items-center text-sm"
                >
                    <i class="fas fa-save mr-2 text-xs"></i> Sajili
                </button>
                <button 
                    type="reset" 
                    class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition-colors flex items-center text-sm"
                >
                    <i class="fas fa-redo mr-2 text-xs"></i> Safisha
                </button>
            </div>
        </form>
    </div>

    <!-- List of Registered Expense Types -->
    @if(isset($aina_za_matumizi) && count($aina_za_matumizi) > 0)
    <div class="bg-white rounded-xl shadow border border-gray-100 p-4 card-hover mt-4">
        <h2 class="text-lg font-bold text-gray-800 mb-4">Aina za Matumizi Zilizosajiliwa</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 max-h-64 overflow-y-auto pr-2">
            @foreach($aina_za_matumizi as $aina)
                <div class="bg-amber-500 border border-gray-200 rounded-lg p-3 hover:shadow-sm transition-shadow text-sm">
                    <div class="flex items-center justify-between mb-2">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold {{ $aina->rangi ?? 'bg-gray-100 text-gray-800 border border-gray-200' }}">
                            {{ $aina->jina }}
                        </span>
                        <span class="text-xs text-blak bg-white px-2 py-1 rounded">
                            {{ $aina->matumizi_count ?? 0 }}
                        </span>
                    </div>
                    @if($aina->maelezo)
                        <p class="text-xs text-gray-600 mb-2 line-clamp-2">{{ $aina->maelezo }}</p>
                    @endif
                    <div class="flex justify-between items-center text-xs text-black">
                        <span class="truncate">{{ $aina->kategoria ?? '-' }}</span>
                        <span>{{ $aina->created_at->format('d/m') }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
</div>

<!-- Edit Modal -->
<div id="edit-modal" class="modal fixed inset-0 z-50 flex items-center justify-center hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 z-50">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Badili Taarifa za Matumizi</h3>
        </div>
        <form id="edit-form" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Aina ya Matumizi</label>
                <input 
                    type="text" 
                    name="aina" 
                    id="edit-aina"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    required
                >
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Maelezo</label>
                <input 
                    type="text" 
                    name="maelezo" 
                    id="edit-maelezo"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                >
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kiasi cha Gharama (TZS)</label>
                <input 
                    type="number" 
                    name="gharama" 
                    id="edit-gharama"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    step="0.01"
                    min="0"
                    required
                >
            </div>
            <div class="flex justify-end space-x-3 pt-4">
                <button 
                    type="button" 
                    id="close-edit-modal"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors"
                >
                    Ghairi
                </button>
                <button 
                    type="submit" 
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
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
            <h3 class="text-lg font-semibold text-gray-800">Thibitisha Kufuta Matumizi</h3>
        </div>
        <div class="p-6">
            <p class="text-gray-700 mb-6">
                Una uhakika unataka kufuta matumizi ya <span id="delete-expense-name" class="font-semibold"></span>?
                Hatua hii haiwezi kutenduliwa.
            </p>
            <div class="flex justify-end space-x-3">
                <button 
                    id="cancel-delete"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors"
                >
                    Ghairi
                </button>
                <form id="delete-form" method="POST">
                    @csrf
                    @method('DELETE')
                    <button 
                        type="submit" 
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
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

.expense-row.hidden {
    display: none;
}
</style>
@endpush

@push('scripts')
<script>
class MatumiziManager {
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
                this.filterExpenses();
            });
        }

        // Expense type selection
        const expenseTypeSelect = document.getElementById('expense-type');
        if (expenseTypeSelect) {
            expenseTypeSelect.addEventListener('change', (e) => {
                this.toggleCustomExpenseType(e.target.value);
            });
        }

        // Go to add expense button
        const goToAddExpenseBtn = document.getElementById('go-to-add-expense');
        if (goToAddExpenseBtn) {
            goToAddExpenseBtn.addEventListener('click', () => {
                this.showTab('ingiza');
            });
        }

        // Expense actions
        this.bindExpenseActions();

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

    toggleCustomExpenseType(selectedValue) {
        const customExpenseTypeDiv = document.getElementById('custom-expense-type');
        if (selectedValue === 'Mengineyo') {
            customExpenseTypeDiv.classList.remove('hidden');
            customExpenseTypeDiv.querySelector('input').required = true;
        } else {
            customExpenseTypeDiv.classList.add('hidden');
            customExpenseTypeDiv.querySelector('input').required = false;
        }
    }

    bindExpenseActions() {
        // Edit buttons
        document.querySelectorAll('.edit-expense-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                const expenseId = e.target.closest('.edit-expense-btn').dataset.id;
                const row = e.target.closest('.expense-row');
                const expenseData = JSON.parse(row.dataset.expense);
                this.editExpense(expenseData);
            });
        });

        // Delete buttons
        document.querySelectorAll('.delete-expense-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                const expenseId = e.target.closest('.delete-expense-btn').dataset.id;
                const expenseName = e.target.closest('.delete-expense-btn').dataset.name;
                this.deleteExpense(expenseId, expenseName);
            });
        });
    }

    bindModalEvents() {
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
                editModal.classList.add('hidden');
                deleteModal.classList.add('hidden');
            }
        });
    }

    filterExpenses() {
        const rows = document.querySelectorAll('.expense-row');
        
        rows.forEach(row => {
            const type = row.querySelector('.expense-type').textContent.toLowerCase();
            const description = row.querySelector('.expense-description').textContent.toLowerCase();
            
            const matches = type.includes(this.searchQuery) || 
                           description.includes(this.searchQuery);
            
            if (matches || this.searchQuery === '') {
                row.classList.remove('hidden');
            } else {
                row.classList.add('hidden');
            }
        });
    }

    editExpense(expense) {
        // Populate edit form
        document.getElementById('edit-aina').value = expense.aina;
        document.getElementById('edit-maelezo').value = expense.maelezo || '';
        document.getElementById('edit-gharama').value = expense.gharama;

        // Set form action
        document.getElementById('edit-form').action = `/matumizi/${expense.id}`;

        // Show modal
        document.getElementById('edit-modal').classList.remove('hidden');
    }

    deleteExpense(expenseId, expenseName) {
        // Populate delete modal
        document.getElementById('delete-expense-name').textContent = expenseName;
        document.getElementById('delete-form').action = `/matumizi/${expenseId}`;

        // Show modal
        document.getElementById('delete-modal').classList.remove('hidden');
    }
}

// Initialize the application when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    new MatumiziManager();
});
</script>
@endpush