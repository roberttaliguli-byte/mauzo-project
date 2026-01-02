@extends('layouts.app')

@section('title', 'Matumizi - DEMODAY')
@section('page-title', 'Matumizi')
@section('page-subtitle', 'Usimamizi wa matumizi yote - ' . now()->format('d/m/Y'))

@section('content')
<div class="space-y-4 md:space-y-6">
    <!-- Statistics Cards - Responsive Grid -->
    <div class="grid grid-cols-1 xs:grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 lg:gap-6">
        <div class="bg-white rounded-xl md:rounded-2xl shadow border border-gray-100 p-4 md:p-6 card-hover">
            <div class="flex items-center">
                <div class="p-2 md:p-3 rounded-lg bg-red-100 text-red-600 mr-3 md:mr-4">
                    <i class="fas fa-money-bill-wave text-lg md:text-xl"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs md:text-sm text-gray-500 font-medium truncate">Jumla ya Matumizi (Mwezi)</p>
                    <h3 class="text-lg md:text-2xl font-bold text-gray-800 truncate">TZS @php echo number_format($matumizi->sum('gharama'), 2); @endphp</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl md:rounded-2xl shadow border border-gray-100 p-4 md:p-6 card-hover">
            <div class="flex items-center">
                <div class="p-2 md:p-3 rounded-lg bg-blue-100 text-blue-600 mr-3 md:mr-4">
                    <i class="fas fa-calendar-day text-lg md:text-xl"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs md:text-sm text-gray-500 font-medium truncate">Matumizi Ya Leo</p>
                    <h3 class="text-lg md:text-2xl font-bold text-gray-800 truncate">TZS @php 
                        $todayTotal = $matumizi->where('created_at', '>=', today())->sum('gharama');
                        echo number_format($todayTotal, 2);
                    @endphp</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl md:rounded-2xl shadow border border-gray-100 p-4 md:p-6 card-hover">
            <div class="flex items-center">
                <div class="p-2 md:p-3 rounded-lg bg-green-100 text-green-600 mr-3 md:mr-4">
                    <i class="fas fa-list text-lg md:text-xl"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs md:text-sm text-gray-500 font-medium truncate">Idadi ya Matumizi</p>
                    <h3 class="text-lg md:text-2xl font-bold text-gray-800 truncate">{{ $matumizi->count() }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl md:rounded-2xl shadow border border-gray-100 p-4 md:p-6 card-hover">
            <div class="flex items-center">
                <div class="p-2 md:p-3 rounded-lg bg-purple-100 text-purple-600 mr-3 md:mr-4">
                    <i class="fas fa-chart-pie text-lg md:text-xl"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs md:text-sm text-gray-500 font-medium truncate">Wastani wa Matumizi</p>
                    <h3 class="text-lg md:text-2xl font-bold text-gray-800 truncate">TZS @php 
                        $average = $matumizi->count() > 0 ? $matumizi->avg('gharama') : 0;
                        echo number_format($average, 2);
                    @endphp</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Page Navigation Tabs - Mobile Scrollable -->
    <div class="bg-white rounded-xl md:rounded-2xl shadow border border-gray-100 p-3 md:p-4 card-hover">
        <div class="flex space-x-4 md:space-x-6 overflow-x-auto pb-2 scrollbar-hide">
            <button 
                id="taarifa-tab" 
                class="tab-button whitespace-nowrap pb-2 md:pb-3 px-1 transition-colors flex items-center border-b-2 border-green-500 text-green-600 font-semibold"
                data-tab="taarifa"
            >
                <i class="fas fa-table mr-2 text-sm md:text-base"></i>
                <span class="text-sm md:text-base">Taarifa</span>
            </button>
            <button 
                id="ingiza-tab" 
                class="tab-button whitespace-nowrap pb-2 md:pb-3 px-1 transition-colors flex items-center text-gray-500 hover:text-gray-700"
                data-tab="ingiza"
            >
                <i class="fas fa-plus-circle mr-2 text-sm md:text-base"></i>
                <span class="text-sm md:text-base">Ingiza</span>
            </button>
            <button 
                id="sajili-tab" 
                class="tab-button whitespace-nowrap pb-2 md:pb-3 px-1 transition-colors flex items-center text-gray-500 hover:text-gray-700"
                data-tab="sajili"
            >
                <i class="fas fa-tags mr-2 text-sm md:text-base"></i>
                <span class="text-sm md:text-base">Sajili</span>
            </button>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-300 rounded-lg md:rounded-xl p-3 md:p-4">
            <div class="flex items-start md:items-center">
                <div class="p-1.5 md:p-2 rounded-lg bg-green-100 text-green-600 mr-2 md:mr-3 flex-shrink-0 mt-0.5 md:mt-0">
                    <i class="fas fa-check-circle text-sm md:text-base"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm md:text-base text-green-800 font-medium break-words">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-gradient-to-r from-red-50 to-pink-50 border border-red-300 rounded-lg md:rounded-xl p-3 md:p-4">
            <div class="flex items-start md:items-center">
                <div class="p-1.5 md:p-2 rounded-lg bg-red-100 text-red-600 mr-2 md:mr-3 flex-shrink-0 mt-0.5 md:mt-0">
                    <i class="fas fa-exclamation-triangle text-sm md:text-base"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <h4 class="text-sm md:text-base text-red-800 font-medium">Hitilafu katika Uwasilishaji</h4>
                    <ul class="list-disc list-inside text-red-700 mt-1 space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li class="text-xs md:text-sm break-words">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- TAB 1: Taarifa za Matumizi -->
    <div id="taarifa-tab-content" class="space-y-4 md:space-y-6 tab-content">
        <!-- Search and Actions -->
        <div class="bg-white rounded-xl md:rounded-2xl shadow border border-gray-100 p-4 md:p-6 card-hover">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-4 md:mb-6">
                <h2 class="text-lg md:text-xl font-bold text-gray-800">Orodha ya Matumizi</h2>
                <div class="flex flex-col xs:flex-row gap-3">
                    <div class="relative w-full xs:w-auto">
                        <input 
                            type="text" 
                            id="search-input"
                            placeholder="Tafuta matumizi..." 
                            class="w-full xs:w-64 pl-9 pr-3 md:pl-10 md:pr-4 py-2 text-sm md:text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        >
                        <div class="absolute inset-y-0 left-0 pl-2 md:pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400 text-sm md:text-base"></i>
                        </div>
                    </div>
                    <button 
                        onclick="window.print()" 
                        class="w-full xs:w-auto bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center text-sm md:text-base"
                    >
                        <i class="fas fa-print mr-2"></i> Print
                    </button>
                </div>
            </div>

            <!-- Data Table -->
            <div class="overflow-x-auto -mx-2 md:mx-0">
                <div class="inline-block min-w-full align-middle">
                    <div class="overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="bg-gradient-to-r from-green-600 to-green-700">
                                    <th class="px-3 md:px-6 py-3 text-left text-xs md:text-sm font-semibold text-white whitespace-nowrap">Tarehe & Muda</th>
                                    <th class="px-3 md:px-6 py-3 text-left text-xs md:text-sm font-semibold text-white whitespace-nowrap">Aina</th>
                                    <th class="px-3 md:px-6 py-3 text-left text-xs md:text-sm font-semibold text-white whitespace-nowrap hidden md:table-cell">Maelezo</th>
                                    <th class="px-3 md:px-6 py-3 text-right text-xs md:text-sm font-semibold text-white whitespace-nowrap">Gharama</th>
                                    <th class="px-3 md:px-6 py-3 text-center text-xs md:text-sm font-semibold text-white whitespace-nowrap print:hidden">Vitendo</th>
                                </tr>
                            </thead>
                            <tbody id="expenses-tbody" class="bg-white divide-y divide-gray-100">
                                @forelse($matumizi as $item)
                                    <tr class="expense-row hover:bg-green-50 transition-all duration-200 
                                        @if($item->created_at->format('Y-m-d') === now()->format('Y-m-d')) bg-gradient-to-r from-amber-50 to-yellow-50 border-l-4 border-amber-400 @endif"
                                        data-expense='@json($item)'>
                                    
                                        <td class="px-3 md:px-6 py-3 md:py-4 whitespace-nowrap">
                                            <div class="text-xs md:text-sm font-semibold text-gray-800">{{ $item->created_at->format('d/m/Y') }}</div>
                                            <div class="text-xs text-green-600 font-medium">{{ $item->created_at->format('H:i') }}</div>
                                        </td>
                                        
                                        <td class="px-3 md:px-6 py-3 md:py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold expense-type
                                                @if($item->aina === 'Mshahara') bg-green-100 text-green-800 border border-green-200
                                                @elseif($item->aina === 'Bank') bg-emerald-100 text-emerald-800 border border-emerald-200
                                                @elseif($item->aina === 'Kodi TRA') bg-teal-100 text-teal-800 border border-teal-200
                                                @elseif($item->aina === 'Kodi Pango') bg-lime-100 text-lime-800 border border-lime-200
                                                @else bg-green-50 text-green-700 border border-green-100 @endif">
                                                {{ $item->aina }}
                                            </span>
                                        </td>
                                        
                                        <td class="px-3 md:px-6 py-3 md:py-4 hidden md:table-cell">
                                            <div class="text-sm text-gray-700 expense-description truncate max-w-xs">{{ $item->maelezo ?: '--' }}</div>
                                        </td>
                                        
                                        <td class="px-3 md:px-6 py-3 md:py-4 whitespace-nowrap text-right">
                                            <div class="text-xs md:text-sm font-bold text-green-700">{{ number_format($item->gharama, 2) }}</div>
                                        </td>
                                        
                                        <td class="px-3 md:px-6 py-3 md:py-4 whitespace-nowrap text-sm font-medium print:hidden">
                                            <div class="flex justify-center space-x-2 md:space-x-3">
                                                <button 
                                                    class="edit-expense-btn text-amber-600 hover:text-amber-800 transition-colors transform hover:scale-110 p-1"
                                                    title="Badili"
                                                    data-id="{{ $item->id }}"
                                                >
                                                    <i class="fas fa-edit text-sm md:text-base"></i>
                                                </button>
                                                <button 
                                                    class="delete-expense-btn text-red-500 hover:text-red-700 transition-colors transform hover:scale-110 p-1"
                                                    title="Futa"
                                                    data-id="{{ $item->id }}"
                                                    data-name="{{ $item->aina }}"
                                                >
                                                    <i class="fas fa-trash text-sm md:text-base"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 md:py-12 text-center">
                                            <div class="flex flex-col items-center justify-center">
                                                <i class="fas fa-receipt text-4xl md:text-5xl text-green-300 mb-3 md:mb-4"></i>
                                                <p class="text-base md:text-lg font-semibold text-gray-600 mb-2">Hakuna matumizi bado.</p>
                                                <p class="text-sm text-gray-500 mb-4 text-center px-4">Anza kwa kuongeza matumizi yako ya kwanza</p>
                                                <button 
                                                    id="go-to-add-expense"
                                                    class="bg-gradient-to-r from-green-600 to-green-700 text-white px-4 py-2 md:px-6 md:py-3 rounded-lg hover:from-green-700 hover:to-green-800 transition-all duration-300 transform hover:scale-105 flex items-center shadow-lg text-sm md:text-base"
                                                >
                                                    <i class="fas fa-plus-circle mr-2"></i> Ingiza Matumizi
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($matumizi->count() > 0)
                            <tfoot>
                                <tr class="bg-gradient-to-r from-green-800 to-green-900">
                                    <td colspan="3" class="px-3 md:px-6 py-3 md:py-4 text-xs md:text-sm font-bold text-white text-right hidden md:table-cell">Jumla ya Matumizi:</td>
                                    <td class="px-3 md:px-6 py-3 md:py-4 text-right md:hidden" colspan="2">
                                        <div class="text-xs font-bold text-white">Jumla: TZS {{ number_format($matumizi->sum('gharama'), 2) }}</div>
                                    </td>
                                    <td class="px-3 md:px-6 py-3 md:py-4 text-right hidden md:table-cell">
                                        <div class="text-sm md:text-base font-bold text-white">
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
        </div>
    </div>

    <!-- TAB 2: Ingiza Matumizi Mpya -->
    <div id="ingiza-tab-content" class="tab-content hidden">
        <div class="bg-white rounded-xl md:rounded-2xl shadow border border-gray-100 p-4 md:p-6 card-hover">
            <h2 class="text-lg md:text-xl font-bold text-gray-800 mb-4 md:mb-6">Ingiza Matumizi Mpya</h2>
            <form method="POST" action="{{ route('matumizi.store') }}" class="space-y-4 md:space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1 md:mb-2">Aina ya Matumizi</label>
                        <select name="aina" id="expense-type" class="w-full border border-gray-300 rounded-lg px-3 md:px-4 py-2 md:py-3 text-sm md:text-base focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" required>
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
                        <label class="block text-sm font-medium text-gray-700 mb-1 md:mb-2">Maelezo</label>
                        <input 
                            type="text" 
                            name="maelezo" 
                            class="w-full border border-gray-300 rounded-lg px-3 md:px-4 py-2 md:py-3 text-sm md:text-base focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                            placeholder="Maelezo ya ziada kuhusu matumizi..."
                            value="{{ old('maelezo') }}"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1 md:mb-2">Kiasi cha Gharama (TZS)</label>
                        <input 
                            type="number" 
                            name="gharama" 
                            class="w-full border border-gray-300 rounded-lg px-3 md:px-4 py-2 md:py-3 text-sm md:text-base focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                            placeholder="Ingiza kiasi cha matumizi" 
                            step="0.01"
                            min="0"
                            value="{{ old('gharama') }}"
                            required
                        >
                    </div>

                    <div class="flex items-end">
                        <div class="w-full">
                            <label class="block text-sm font-medium text-gray-700 mb-1 md:mb-2">Tarehe</label>
                            <input 
                                type="date" 
                                name="tarehe" 
                                value="{{ old('tarehe', now()->format('Y-m-d')) }}"
                                class="w-full border border-gray-300 rounded-lg px-3 md:px-4 py-2 md:py-3 text-sm md:text-base focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            >
                        </div>
                    </div>
                </div>

                <div class="flex flex-col xs:flex-row gap-3 pt-4 md:pt-6">
                    <button 
                        type="submit" 
                        class="bg-green-600 text-white px-4 md:px-6 py-2 md:py-3 rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center text-sm md:text-base"
                    >
                        <i class="fas fa-save mr-2"></i> Hifadhi Matumizi
                    </button>
                    <button 
                        type="reset" 
                        class="bg-gray-300 text-gray-700 px-4 md:px-6 py-2 md:py-3 rounded-lg hover:bg-gray-400 transition-colors flex items-center justify-center text-sm md:text-base"
                    >
                        <i class="fas fa-redo mr-2"></i> Safisha Fomu
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- TAB 3: Sajili Matumizi - Ultra Minimal -->
    <div id="sajili-tab-content" class="tab-content hidden">
        <!-- Register New Expense Type - Minimal -->
        <div class="bg-white rounded-xl md:rounded-2xl shadow border border-gray-100 p-4 md:p-6 card-hover">
            <h2 class="text-lg md:text-xl font-bold text-gray-800 mb-4 md:mb-6">Sajili Aina Mpya ya Matumizi</h2>
            
            <form method="POST" action="{{ route('matumizi.sajili-aina') }}" class="space-y-4 md:space-y-6">
                @csrf
                
                <div class="flex flex-col md:flex-row gap-3 md:gap-4">
                    <!-- Input field -->
                    <div class="flex-1">
                        <label class="sr-only">Jina la Aina ya Matumizi</label>
                        <input 
                            type="text" 
                            name="jina" 
                            class="w-full border border-gray-300 rounded-lg px-3 md:px-4 py-2 md:py-3 text-sm md:text-base focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                            placeholder="Ingiza jina la aina ya matumizi..." 
                            value="{{ old('jina') }}"
                            required
                            autofocus
                        >
                    </div>

                    <!-- Hidden fields -->
                    <div class="hidden">
                        <input type="hidden" name="rangi" value="bg-green-50 text-green-700 border border-green-100">
                        <input type="hidden" name="kategoria" value="mengineyo">
                        <input type="hidden" name="maelezo" value="">
                    </div>

                    <!-- Button -->
                    <div class="md:w-auto">
                        <button 
                            type="submit" 
                            class="w-full md:w-auto bg-green-600 text-white px-4 md:px-6 py-2 md:py-3 rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center text-sm md:text-base font-medium shadow-md hover:shadow-lg whitespace-nowrap"
                        >
                            <i class="fas fa-save mr-2"></i> Sajili
                        </button>
                    </div>
                </div>

                <!-- Help text -->
                <p class="text-xs text-gray-500">
                    Jina litaonekana kwenye orodha ya aina za matumizi wakati wa kuongeza matumizi mapya.
                </p>
            </form>
        </div>

        <!-- List of Registered Expense Types -->
        @if(isset($aina_za_matumizi) && count($aina_za_matumizi) > 0)
        <div class="bg-white rounded-xl shadow border border-gray-100 p-4 md:p-6 card-hover mt-4 md:mt-6">
            <h2 class="text-lg md:text-xl font-bold text-gray-800 mb-4">Aina za Matumizi Zilizosajiliwa ({{ count($aina_za_matumizi) }})</h2>
            <div class="flex flex-wrap gap-2">
                @foreach($aina_za_matumizi as $aina)
                    <div class="inline-flex items-center bg-green-50 border border-green-200 rounded-full px-3 py-1.5">
                        <span class="text-sm font-medium text-green-700 mr-2">{{ $aina->jina }}</span>
                        <span class="text-xs bg-green-100 text-green-800 rounded-full px-2 py-0.5 font-semibold">
                            {{ $aina->matumizi_count ?? 0 }}
                        </span>
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
    <div class="modal-content bg-white rounded-xl md:rounded-2xl shadow-xl w-full max-w-md mx-4 z-50">
        <div class="p-4 md:p-6 border-b border-gray-200">
            <h3 class="text-base md:text-lg font-semibold text-gray-800">Badili Taarifa za Matumizi</h3>
        </div>
        <form id="edit-form" method="POST" class="p-4 md:p-6 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1 md:mb-2">Aina ya Matumizi</label>
                <input 
                    type="text" 
                    name="aina" 
                    id="edit-aina"
                    class="w-full border border-gray-300 rounded-lg px-3 md:px-4 py-2 md:py-3 text-sm md:text-base focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    required
                >
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1 md:mb-2">Maelezo</label>
                <input 
                    type="text" 
                    name="maelezo" 
                    id="edit-maelezo"
                    class="w-full border border-gray-300 rounded-lg px-3 md:px-4 py-2 md:py-3 text-sm md:text-base focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                >
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1 md:mb-2">Kiasi cha Gharama (TZS)</label>
                <input 
                    type="number" 
                    name="gharama" 
                    id="edit-gharama"
                    class="w-full border border-gray-300 rounded-lg px-3 md:px-4 py-2 md:py-3 text-sm md:text-base focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    step="0.01"
                    min="0"
                    required
                >
            </div>
            <div class="flex flex-col xs:flex-row justify-end space-y-2 xs:space-y-0 xs:space-x-3 pt-4">
                <button 
                    type="button" 
                    id="close-edit-modal"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors text-sm md:text-base"
                >
                    Ghairi
                </button>
                <button 
                    type="submit" 
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm md:text-base"
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
    <div class="modal-content bg-white rounded-xl md:rounded-2xl shadow-xl w-full max-w-md mx-4 z-50">
        <div class="p-4 md:p-6 border-b border-gray-200">
            <h3 class="text-base md:text-lg font-semibold text-gray-800">Thibitisha Kufuta Matumizi</h3>
        </div>
        <div class="p-4 md:p-6">
            <p class="text-sm md:text-base text-gray-700 mb-4 md:mb-6">
                Una uhakika unataka kufuta matumizi ya <span id="delete-expense-name" class="font-semibold"></span>?
                Hatua hii haiwezi kutenduliwa.
            </p>
            <div class="flex flex-col xs:flex-row justify-end space-y-2 xs:space-y-0 xs:space-x-3">
                <button 
                    id="cancel-delete"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors text-sm md:text-base"
                >
                    Ghairi
                </button>
                <form id="delete-form" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button 
                        type="submit" 
                        class="w-full xs:w-auto px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm md:text-base"
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

/* Hide scrollbar but allow scrolling */
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

/* Responsive text truncation */
.truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.line-clamp-2 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
}

/* Mobile-first breakpoints */
@media (min-width: 475px) {
    .xs\:grid-cols-2 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (min-width: 640px) {
    .sm\:grid-cols-2 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (min-width: 768px) {
    .md\:table-cell {
        display: table-cell;
    }
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
        this.setupResponsiveTables();
    }

    bindEvents() {
        // Tab navigation
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', (e) => {
                const tab = e.target.closest('.tab-button').dataset.tab;
                this.showTab(tab);
                this.adjustTabScroll();
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

        // Window resize handling
        window.addEventListener('resize', this.debounce(() => {
            this.setupResponsiveTables();
        }, 250));
    }

    adjustTabScroll() {
        const tabsContainer = document.querySelector('.scrollbar-hide');
        const activeTab = document.querySelector('.tab-button.border-green-500');
        if (tabsContainer && activeTab) {
            tabsContainer.scrollLeft = activeTab.offsetLeft - tabsContainer.offsetLeft - 20;
        }
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

    setupResponsiveTables() {
        // Add mobile description toggle
        if (window.innerWidth < 768) {
            this.addMobileDescriptionToggle();
        } else {
            this.removeMobileDescriptionToggle();
        }
    }

    addMobileDescriptionToggle() {
        const expenseRows = document.querySelectorAll('.expense-row');
        expenseRows.forEach(row => {
            if (!row.dataset.hasMobileToggle) {
                row.addEventListener('click', (e) => {
                    // Don't trigger on button clicks
                    if (!e.target.closest('button')) {
                        row.classList.toggle('show-description');
                    }
                });
                row.dataset.hasMobileToggle = true;
            }
        });
    }

    removeMobileDescriptionToggle() {
        const expenseRows = document.querySelectorAll('.expense-row');
        expenseRows.forEach(row => {
            row.removeEventListener('click', () => {});
            row.classList.remove('show-description');
        });
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

    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
}

// Initialize the application when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    new MatumiziManager();
});
</script>
@endpush