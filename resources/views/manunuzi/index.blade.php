@extends('layouts.app')

@section('title', 'Manunuzi - DEMODAY')

@section('page-title', 'Manunuzi')
@section('page-subtitle', 'Usimamizi wa manunuzi yote - ' . now()->format('d/m/Y'))

@section('content')
<div class="space-y-6">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 card-hover">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-green-100 text-green-600 mr-4">
                    <i class="fas fa-shopping-cart text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-black font-medium">Jumla ya Manunuzi</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $manunuzi->count() }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 card-hover">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-emerald-100 text-emerald-600 mr-4">
                    <i class="fas fa-boxes text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-black font-medium">Bidhaa Zilizonunuliwa</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $manunuzi->sum('idadi') }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 card-hover">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-blue-100 text-blue-600 mr-4">
                    <i class="fas fa-money-bill-wave text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-black font-medium">Jumla ya Gharama</p>
                    <h3 class="text-2xl font-bold text-gray-800">TZS {{ number_format($manunuzi->sum('bei'), 2) }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 card-hover">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-purple-100 text-purple-600 mr-4">
                    <i class="fas fa-truck text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-black font-medium">Wasaplaya</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $manunuzi->pluck('saplaya')->unique()->count() }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Page Navigation Tabs -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 card-hover">
        <div class="flex space-x-6 border-b border-gray-200">
            <button 
                onclick="switchTab('taarifa')" 
                id="tab-taarifa"
                class="tab-button pb-3 px-1 transition-colors flex items-center border-b-2 border-green-500 text-green-600 font-semibold"
            >
                <i class="fas fa-table mr-2"></i>Taarifa za Manunuzi
            </button>
            <button 
                onclick="switchTab('ingiza')" 
                id="tab-ingiza"
                class="tab-button pb-3 px-1 transition-colors flex items-center text-gray-500 hover:text-gray-700"
            >
                <i class="fas fa-plus-circle mr-2"></i>Ingiza Manunuzi Mpya
            </button>
        </div>
    </div>

    <!-- Manunuzi Information Tab -->
    <div id="content-taarifa" class="tab-content space-y-6">
        <!-- Search and Actions -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 card-hover">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-800">Orodha ya Manunuzi</h2>
                <div class="flex space-x-3">
                    <div class="relative">
                        <input 
                            type="text" 
                            id="searchQuery"
                            placeholder="Tafuta manunuzi..." 
                            class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            oninput="filterTable()"
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
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white">Tarehe & Muda</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white">Bidhaa</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-white">Idadi</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-white">Bei (TZS)</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white">Expiry</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white">Saplaya</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white">Simu</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white">Maelezo</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-white print:hidden">Vitendo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($manunuzi as $item)
                            <tr class="table-row hover:bg-green-50 transition-all duration-200 
                                @if($item->created_at->format('Y-m-d') === now()->format('Y-m-d')) bg-gradient-to-r from-amber-50 to-yellow-50 border-l-4 border-amber-400 @endif"
                                data-searchable="{{ strtolower($item->bidhaa->jina . ' ' . $item->saplaya . ' ' . $item->simu) }}">
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-800">{{ $item->created_at->format('d/m/Y') }}</div>
                                    <div class="text-xs text-green-600 font-medium">{{ $item->created_at->format('H:i') }}</div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center text-green-800 font-semibold">
                                            {{ substr($item->bidhaa->jina, 0, 1) }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-semibold text-gray-900">{{ $item->bidhaa->jina }}</div>
                                            <div class="text-xs text-green-600">{{ $item->bidhaa->aina }}</div>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="text-sm font-bold text-green-700">{{ $item->idadi }}</div>
                                    <div class="text-xs text-gray-500">{{ $item->bidhaa->kipimo }}</div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="text-sm font-bold text-green-700">{{ number_format($item->bei, 2) }}</div>
                                    <div class="text-xs text-gray-500">
                                        @ {{ number_format($item->bei / $item->idadi, 2) }}/kimoja
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm 
                                        @if($item->expiry && \Carbon\Carbon::parse($item->expiry) < now()) text-red-700 font-semibold
                                        @elseif($item->expiry && \Carbon\Carbon::parse($item->expiry)->diffInDays(now()) < 30) text-amber-700
                                        @else text-gray-700 @endif">
                                        {{ $item->expiry ? \Carbon\Carbon::parse($item->expiry)->format('d/m/Y') : '--' }}
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200">
                                        {{ $item->saplaya ?: '--' }}
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-700">{{ $item->simu ?: '--' }}</div>
                                </td>
                                
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-700 max-w-xs truncate">{{ $item->mengineyo ?: '--' }}</div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium print:hidden">
                                    <div class="flex justify-center space-x-3">
                                        <button 
                                            onclick='editItem(@json($item))' 
                                            class="text-amber-600 hover:text-amber-800 transition-colors transform hover:scale-110"
                                            title="Badili"
                                        >
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button 
                                            onclick="deleteItem({{ $item->id }}, '{{ $item->bidhaa->jina }}')" 
                                            class="text-red-500 hover:text-red-700 transition-colors transform hover:scale-110"
                                            title="Futa"
                                        >
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fas fa-shopping-cart text-5xl text-green-300 mb-4"></i>
                                        <p class="text-lg font-semibold text-gray-600 mb-2">Hakuna manunuzi bado.</p>
                                        <p class="text-sm text-gray-500 mb-4">Anza kwa kuongeza manunuzi yako ya kwanza</p>
                                        <button 
                                            onclick="switchTab('ingiza')" 
                                            class="bg-gradient-to-r from-green-600 to-green-700 text-white px-6 py-3 rounded-lg hover:from-green-700 hover:to-green-800 transition-all duration-300 transform hover:scale-105 flex items-center shadow-lg"
                                        >
                                            <i class="fas fa-plus-circle mr-2"></i> Ingiza Manunuzi ya Kwanza
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($manunuzi->count() > 0)
                    <tfoot>
                        <tr class="bg-gradient-to-r from-green-800 to-green-900">
                            <td colspan="2" class="px-6 py-4 text-sm font-bold text-white text-right">Jumla:</td>
                            <td class="px-6 py-4 text-center text-sm font-bold text-white">{{ $manunuzi->sum('idadi') }}</td>
                            <td class="px-6 py-4 text-right text-sm font-bold text-white">TZS {{ number_format($manunuzi->sum('bei'), 2) }}</td>
                            <td colspan="5" class="print:hidden"></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>

            <!-- Summary Cards -->
            @if($manunuzi->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="p-2 rounded-lg bg-green-100 text-green-600 mr-3">
                                <i class="fas fa-shopping-cart text-lg"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-green-800">Manunuzi Ya Leo</h4>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-green-700">
                                {{ $manunuzi->where('created_at', '>=', today())->count() }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="p-2 rounded-lg bg-blue-100 text-blue-600 mr-3">
                                <i class="fas fa-boxes text-lg"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-blue-800">Bidhaa Zilizonunuliwa</h4>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-blue-700">{{ $manunuzi->sum('idadi') }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-purple-50 to-pink-50 border border-purple-200 rounded-xl p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="p-2 rounded-lg bg-purple-100 text-purple-600 mr-3">
                                <i class="fas fa-money-bill-wave text-lg"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-purple-800">Gharama Ya Leo</h4>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-purple-700">
                                TZS {{ number_format($manunuzi->where('created_at', '>=', today())->sum('bei'), 2) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Ingiza Manunuzi Tab -->
    <div id="content-ingiza" class="tab-content bg-white rounded-2xl shadow-lg border border-gray-100 p-6 card-hover" style="display: none;">
        <h2 class="text-xl font-bold text-gray-800 mb-6">Ingiza Manunuzi Mpya</h2>
        <form method="POST" action="{{ route('manunuzi.store') }}" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Bidhaa -->
                <div class="relative">
                    <select 
                        name="bidhaa_id" 
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent appearance-none bg-white" 
                        required
                    >
                        <option value=""> </option>
                        @foreach($bidhaa as $b)
                        <option value="{{ $b->id }}">{{ $b->jina }} - {{ $b->aina }}</option>
                        @endforeach
                    </select>
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Chagua Bidhaa *
                    </label>
                    <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                        <i class="fas fa-chevron-down text-gray-400"></i>
                    </div>
                </div>

                <!-- Idadi -->
                <div class="relative">
                    <input 
                        type="number" 
                        name="idadi" 
                        placeholder=" " 
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                        required
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Idadi ya Bidhaa *
                    </label>
                </div>

                <!-- Bei Type & Amount -->
                <div class="relative col-span-2">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="relative">
                            <select 
                                name="bei_type" 
                                class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent appearance-none bg-white"
                            >
                                <option value="kwa_zote"> </option>
                                <option value="kwa_zote">Kwa Zote</option>
                                <option value="rejareja">Rejareja</option>
                            </select>
                            <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                          peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                          peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                          peer-focus:text-sm font-medium">
                                Aina ya Bei
                            </label>
                            <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                <i class="fas fa-chevron-down text-gray-400"></i>
                            </div>
                        </div>
                        <div class="relative">
                            <input 
                                type="number" 
                                step="0.01" 
                                name="bei" 
                                placeholder=" " 
                                class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                                required
                            >
                            <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                          peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                          peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                          peer-focus:text-sm font-medium">
                                Kiasi cha Bei (TZS) *
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Expiry -->
                <div class="relative">
                    <input 
                        type="date" 
                        name="expiry" 
                        placeholder=" " 
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Tarehe ya Mwisho (Expiry)
                    </label>
                </div>

                <!-- Saplaya -->
                <div class="relative">
                    <input 
                        type="text" 
                        name="saplaya" 
                        placeholder=" " 
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Jina la Msaplaya
                    </label>
                </div>

                <!-- Simu -->
                <div class="relative">
                    <input 
                        type="text" 
                        name="simu" 
                        placeholder=" " 
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Namba ya Simu ya Msaplaya
                    </label>
                </div>

                <!-- Maelezo -->
                <div class="relative col-span-2">
                    <textarea 
                        name="mengineyo" 
                        placeholder=" " 
                        rows="4"
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-8 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    ></textarea>
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-6 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Maelezo ya Ziada (Hiari)
                    </label>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4 pt-6 border-t border-gray-200">
                <button 
                    type="submit" 
                    class="bg-gradient-to-r from-green-600 to-green-700 text-white px-8 py-3 rounded-lg hover:from-green-700 hover:to-green-800 transition-all duration-300 transform hover:scale-105 flex items-center shadow-lg"
                >
                    <i class="fas fa-save mr-2"></i> Hifadhi Manunuzi
                </button>
                <button 
                    type="reset" 
                    class="bg-gray-300 text-gray-700 px-8 py-3 rounded-lg hover:bg-gray-400 transition-colors flex items-center"
                >
                    <i class="fas fa-redo mr-2"></i> Safisha Fomu
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="hidden fixed inset-0 z-50 flex items-center justify-center modal-overlay">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Badili Taarifa za Manunuzi</h3>
        </div>
        <form id="editForm" method="POST" class="p-6">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Bidhaa -->
                <div class="relative">
                    <select 
                        name="bidhaa_id" 
                        id="edit_bidhaa_id"
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent appearance-none bg-white" 
                        required
                    >
                        @foreach($bidhaa as $b)
                        <option value="{{ $b->id }}">{{ $b->jina }} - {{ $b->aina }}</option>
                        @endforeach
                    </select>
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Bidhaa *
                    </label>
                    <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                        <i class="fas fa-chevron-down text-gray-400"></i>
                    </div>
                </div>

                <!-- Idadi -->
                <div class="relative">
                    <input 
                        type="number" 
                        name="idadi" 
                        id="edit_idadi"
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                        required
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Idadi *
                    </label>
                </div>

                <!-- Bei -->
                <div class="relative">
                    <input 
                        type="number" 
                        step="0.01" 
                        name="bei" 
                        id="edit_bei"
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                        required
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Bei (TZS) *
                    </label>
                </div>

                <!-- Expiry -->
                <div class="relative">
                    <input 
                        type="date" 
                        name="expiry" 
                        id="edit_expiry"
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Expiry
                    </label>
                </div>

                <!-- Saplaya -->
                <div class="relative">
                    <input 
                        type="text" 
                        name="saplaya" 
                        id="edit_saplaya"
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Saplaya
                    </label>
                </div>

                <!-- Simu -->
                <div class="relative">
                    <input 
                        type="text" 
                        name="simu" 
                        id="edit_simu"
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Simu
                    </label>
                </div>

                <!-- Maelezo -->
                <div class="relative col-span-2">
                    <textarea 
                        name="mengineyo" 
                        id="edit_mengineyo"
                        rows="3"
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-8 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    ></textarea>
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-6 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Maelezo ya Ziada
                    </label>
                </div>
            </div>
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 mt-6">
                <button 
                    type="button" 
                    onclick="closeEditModal()"
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

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 z-50 flex items-center justify-center modal-overlay">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4" onclick="event.stopPropagation()">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Thibitisha Kufuta Manunuzi</h3>
        </div>
        <div class="p-6">
            <div class="flex items-center justify-center mb-4">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
            </div>
            <p class="text-gray-700 mb-6 text-center">
                Una uhakika unataka kufuta manunuzi ya "<span id="delete_name" class="font-semibold"></span>"?
                <br>Hatua hii haiwezi kutenduliwa.
            </p>
            <div class="flex justify-center space-x-3">
                <button 
                    onclick="closeDeleteModal()"
                    class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors"
                >
                    Ghairi
                </button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button 
                        type="submit" 
                        class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
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
// Tab Switching
function switchTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.style.display = 'none';
    });
    
    // Remove active class from all tab buttons
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('border-b-2', 'border-green-500', 'text-green-600', 'font-semibold');
        button.classList.add('text-gray-500', 'hover:text-gray-700');
    });
    
    // Show selected tab content
    document.getElementById('content-' + tabName).style.display = 'block';
    
    // Add active class to selected tab button
    const activeButton = document.getElementById('tab-' + tabName);
    activeButton.classList.remove('text-gray-500', 'hover:text-gray-700');
    activeButton.classList.add('border-b-2', 'border-green-500', 'text-green-600', 'font-semibold');
}

// Search/Filter Table
function filterTable() {
    const searchQuery = document.getElementById('searchQuery').value.toLowerCase();
    const rows = document.querySelectorAll('.table-row');
    
    rows.forEach(row => {
        const searchableText = row.getAttribute('data-searchable');
        if (searchableText.includes(searchQuery)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Edit Item
function editItem(item) {
    document.getElementById('edit_bidhaa_id').value = item.bidhaa_id || '';
    document.getElementById('edit_idadi').value = item.idadi || '';
    document.getElementById('edit_bei').value = item.bei || '';
    document.getElementById('edit_expiry').value = item.expiry || '';
    document.getElementById('edit_saplaya').value = item.saplaya || '';
    document.getElementById('edit_simu').value = item.simu || '';
    document.getElementById('edit_mengineyo').value = item.mengineyo || '';
    
    document.getElementById('editForm').action = '/manunuzi/' + item.id;
    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

// Delete Item
function deleteItem(id, name) {
    document.getElementById('delete_name').textContent = name;
    document.getElementById('deleteForm').action = '/manunuzi/' + id;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

// Close modals when clicking outside
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('modal-overlay')) {
        closeEditModal();
        closeDeleteModal();
    }
});

// Close modals with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeEditModal();
        closeDeleteModal();
    }
});
</script>

<style>
.modal-overlay {
    background-color: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
}

.card-hover {
    transition: all 0.3s ease;
}

.card-hover:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

@media print {
    .print\:hidden {
        display: none !important;
    }
    
    .modal-overlay {
        display: none !important;
    }
    
    .tab-content {
        display: block !important;
    }
    
    #content-ingiza {
        display: none !important;
    }
}
</style>
@endpush