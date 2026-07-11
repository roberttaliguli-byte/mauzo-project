@extends('layouts.app')

@section('title', 'Manunuzi')

@section('page-title', 'Manunuzi')
@section('page-subtitle', now()->format('d/m/Y'))

@section('content')
<div class="space-y-4" id="app-container" data-current-page="{{ request()->get('page', 1) }}">
    <!-- Hidden data - bidhaa without images -->
    <div id="bidhaa-data" style="display:none;">{{ json_encode($bidhaa->map(function($b) {
        return [
            'id' => $b->id,
            'jina' => $b->jina,
            'aina' => $b->aina,
            'kipimo' => $b->kipimo,
            'idadi' => $b->idadi,
            'bei_nunua' => $b->bei_nunua,
            'bei_kuuza' => $b->bei_kuuza,
            'bei_uzo_jumla' => $b->bei_uzo_jumla,
            'bei_kiasi_cha_chaguo' => $b->bei_kiasi_cha_chaguo,
            'has_image' => $b->hasImage(),
        ];
    })) }}</div>

    <!-- Hidden data - all manunuzi for searching -->
    <div id="all-manunuzi-data" style="display:none;">{{ json_encode($allManunuzi->map(function($m) {
        return [
            'id' => $m->id,
            'bidhaa_id' => $m->bidhaa_id,
            'idadi' => $m->idadi,
            'bei' => $m->bei,
            'unit_cost' => $m->unit_cost,
            'expiry' => $m->expiry,
            'saplaya' => $m->saplaya,
            'simu' => $m->simu,
            'mengineyo' => $m->mengineyo,
            'created_at' => $m->created_at,
            'created_at_formatted' => $m->created_at ? $m->created_at->format('d/m/Y') : '',
            'created_time' => $m->created_at ? $m->created_at->format('H:i') : '',
            'bidhaa' => $m->bidhaa ? [
                'id' => $m->bidhaa->id,
                'jina' => $m->bidhaa->jina,
                'aina' => $m->bidhaa->aina,
                'kipimo' => $m->bidhaa->kipimo,
                'bei_kuuza' => $m->bidhaa->bei_kuuza,
            ] : null
        ];
    })) }}</div>

    <!-- Notifications -->
    <div id="notification-container" class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-sm px-4 pointer-events-none">
        @if(session('success'))
        <div class="rounded border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 mb-2 shadow-sm">
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="rounded border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800 mb-2 shadow-sm">
            {{ session('error') }}
        </div>
        @endif
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="bg-white p-3 rounded-lg border border-emerald-200 shadow-sm">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Manunuzi Ya Leo</p>
                    <p class="text-xl font-bold text-emerald-700">{{ $todayPurchases ?? 0 }}</p>
                </div>
                <i class="fas fa-shopping-cart text-emerald-500 text-lg"></i>
            </div>
        </div>
        <div class="bg-white p-3 rounded-lg border border-blue-200 shadow-sm">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Bidhaa Zilizonunuliwa</p>
                    <p class="text-xl font-bold text-blue-700">{{ number_format($totalItemsPurchased ?? 0, 2) }}</p>
                </div>
                <i class="fas fa-boxes text-blue-500 text-lg"></i>
            </div>
        </div>
        <div class="bg-white p-3 rounded-lg border border-purple-200 shadow-sm">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Jumla ya Gharama</p>
                    <p class="text-xl font-bold text-purple-700">{{ number_format($totalCost ?? 0, 2) }}</p>
                </div>
                <i class="fas fa-money-bill-wave text-purple-500 text-lg"></i>
            </div>
        </div>
        <div class="bg-white p-3 rounded-lg border border-amber-200 shadow-sm">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Gharama Ya Leo</p>
                    <p class="text-xl font-bold text-amber-700">{{ number_format($todayCost ?? 0, 2) }}</p>
                </div>
                <i class="fas fa-calendar-day text-amber-500 text-lg"></i>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm">
        <div class="flex">
            <button data-tab="taarifa" class="tab-button flex-1 py-3 px-4 text-sm font-medium border-r border-gray-200 bg-emerald-50 text-emerald-700">
                <i class="fas fa-table mr-2"></i> Orodha
            </button>
            <button data-tab="ingiza" class="tab-button flex-1 py-3 px-4 text-sm font-medium border-r border-gray-200 text-gray-600 hover:bg-gray-50">
                <i class="fas fa-plus mr-2"></i> Ingiza
            </button>
            <button data-tab="ripoti" class="tab-button flex-1 py-3 px-4 text-sm font-medium text-gray-600 hover:bg-gray-50">
                <i class="fas fa-chart-bar mr-2"></i> Ripoti
            </button>
        </div>
    </div>

    <!-- TAB 1: Orodha -->
    <div id="taarifa-tab-content" class="tab-content space-y-3">
        <div class="bg-white p-3 rounded-lg border border-gray-200 shadow-sm">
            <div class="flex flex-col sm:flex-row gap-2">
                <div class="flex-1 relative">
                    <input 
                        type="text" 
                        id="search-input"
                        placeholder="Tafuta bidhaa, saplaya, simu..." 
                        class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                        value="{{ request()->search }}"
                        autocomplete="off"
                    >
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <button id="clear-search" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 hidden">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="flex gap-2">
                    <button onclick="window.manunuziManager?.printManunuzi()" class="px-3 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 text-sm font-medium">
                        <i class="fas fa-print mr-1"></i> Print
                    </button>
                    <button onclick="window.manunuziManager?.exportPDF()" class="px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm font-medium">
                        <i class="fas fa-file-pdf mr-1"></i> PDF
                    </button>
                    <button onclick="window.manunuziManager?.exportExcel()" class="px-3 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm font-medium">
                        <i class="fas fa-file-excel mr-1"></i> Excel
                    </button>
                </div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-2 mt-3 items-end">
                <div class="sm:col-span-1">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Kuanzia Tarehe</label>
                    <input type="date" id="start-date" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                </div>
                <div class="sm:col-span-1">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Mpaka Tarehe</label>
                    <input type="date" id="end-date" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                </div>
                <div class="sm:col-span-2 flex gap-2">
                    <button onclick="window.manunuziManager?.filterByDateRange()" class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 text-sm font-medium">
                        <i class="fas fa-filter mr-1"></i> Chuja
                    </button>
                    <button onclick="window.manunuziManager?.clearDateFilter()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 text-sm">
                        <i class="fas fa-times mr-1"></i> Ondoa
                    </button>
                    <span id="date-range-info" class="hidden text-sm text-gray-600 ml-2 self-center">
                        <i class="fas fa-calendar-alt mr-1 text-emerald-600"></i>
                        <span id="date-range-text"></span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Search Status -->
        <div id="search-status" class="text-center text-sm text-gray-600 hidden">
            <p id="search-result-count"></p>
            <button onclick="clearManunuziSearch()" class="mt-1 text-xs text-emerald-600 hover:text-emerald-800">
                <i class="fas fa-times mr-1"></i> Ondoa utafutaji
            </button>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-emerald-50">
                            <th class="px-4 py-2 text-left font-medium text-emerald-800">Tarehe</th>
                            <th class="px-4 py-2 text-left font-medium text-emerald-800 hidden sm:table-cell">Bidhaa</th>
                            <th class="px-4 py-2 text-center font-medium text-emerald-800">Idadi</th>
                            <th class="px-4 py-2 text-right font-medium text-emerald-800">Bei Nunua</th>
                            <th class="px-4 py-2 text-right font-medium text-emerald-800">Bei Uza</th>
                            <th class="px-4 py-2 text-left font-medium text-emerald-800 hidden lg:table-cell">Saplaya</th>
                            <th class="px-4 py-2 text-center font-medium text-emerald-800 print:hidden">Vitendo</th>
                        </tr>
                    </thead>
                    <tbody id="manunuzi-tbody" class="divide-y divide-gray-100">
                        @forelse($manunuzi as $item)
                            @php
                                $cleanData = [
                                    'id' => $item->id,
                                    'bidhaa_id' => $item->bidhaa_id,
                                    'idadi' => $item->idadi,
                                    'bei' => $item->bei,
                                    'unit_cost' => $item->unit_cost,
                                    'expiry' => $item->expiry,
                                    'saplaya' => $item->saplaya,
                                    'simu' => $item->simu,
                                    'mengineyo' => $item->mengineyo,
                                    'created_at' => $item->created_at,
                                    'created_at_formatted' => $item->created_at ? $item->created_at->format('d/m/Y') : '',
                                    'created_time' => $item->created_at ? $item->created_at->format('H:i') : '',
                                    'bidhaa' => $item->bidhaa ? [
                                        'id' => $item->bidhaa->id,
                                        'jina' => $item->bidhaa->jina,
                                        'aina' => $item->bidhaa->aina,
                                        'kipimo' => $item->bidhaa->kipimo,
                                        'bei_kuuza' => $item->bidhaa->bei_kuuza,
                                    ] : null
                                ];
                            @endphp
                            <tr class="manunuzi-row hover:bg-gray-50" data-manunuzi='{{ json_encode($cleanData) }}'>
                                <td class="px-4 py-2">
                                    <div class="text-xs text-gray-900">{{ $item->created_at->format('d/m/Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $item->created_at->format('H:i') }}</div>
                                </td>
                                <td class="px-4 py-2 hidden sm:table-cell">
                                    <div class="font-medium text-gray-900 text-sm">{{ $item->bidhaa->jina ?? '--' }}</div>
                                    <div class="text-xs text-gray-500">{{ $item->bidhaa->aina ?? '--' }}</div>
                                    @if($item->bidhaa->kipimo ?? '')
                                    <div class="text-xs text-gray-400">{{ $item->bidhaa->kipimo }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-emerald-100 text-emerald-800">
                                        {{ is_numeric($item->idadi) ? ($item->idadi % 1 == 0 ? (string)(int)$item->idadi : number_format($item->idadi, 2)) : '0' }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-right">
                                    <div class="text-sm font-bold text-emerald-700">{{ number_format($item->bei ?? 0, 2) }}</div>
                                    <div class="text-xs text-gray-500">@ {{ number_format($item->unit_cost ?? 0, 2) }}</div>
                                </td>
                                <td class="px-4 py-2 text-right">
                                    <div class="text-sm font-bold text-green-700">{{ number_format($item->bidhaa->bei_kuuza ?? 0, 2) }}</div>
                                </td>
                                <td class="px-4 py-2 hidden lg:table-cell">
                                    <div class="text-xs text-gray-700">{{ $item->saplaya ?? '--' }}</div>
                                    @if($item->simu ?? '')
                                    <div class="text-xs text-gray-400">{{ $item->simu }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-center print:hidden">
                                    <div class="flex justify-center space-x-2">
                                        <button class="edit-manunuzi-btn text-emerald-600 hover:text-emerald-800"
                                                data-id="{{ $item->id }}" title="Badili">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="delete-manunuzi-btn text-red-600 hover:text-red-800"
                                                data-id="{{ $item->id }}" data-name="{{ $item->bidhaa->jina ?? 'Bidhaa' }}" title="Futa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                    <i class="fas fa-shopping-cart text-3xl mb-2 text-gray-300"></i>
                                    <p>Hakuna manunuzi bado</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="px-4 py-3 border-t border-gray-200 flex justify-between items-center">
                <span class="manunuzi-pagination-info text-sm text-gray-600">
                    Inaonyesha <span id="visible-count">{{ $manunuzi->count() }}</span> kati ya <span id="total-count">{{ $manunuzi->total() }}</span> manunuzi
                </span>
                <div id="pagination-links">
                    @if(isset($manunuzi) && $manunuzi->hasPages())
                        {{ $manunuzi->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 2: Ingiza -->
    <div id="ingiza-tab-content" class="tab-content hidden">
        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
            <form method="POST" action="{{ route('manunuzi.store') }}" id="manunuzi-form" class="space-y-4">
                @csrf
                
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Chagua Bidhaa *</label>
                    <div class="relative">
                        <input type="text" 
                               id="bidhaa-search-input"
                               placeholder="Tafuta bidhaa..." 
                               class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                               autocomplete="off">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <div id="bidhaa-search-results" class="hidden absolute z-10 w-full bg-white border border-gray-300 rounded shadow-lg max-h-60 overflow-y-auto"></div>
                    </div>
                    <input type="hidden" name="bidhaa_id" id="bidhaa_id">
                    
                    <div id="selected-bidhaa-info" class="hidden mt-2 p-2 bg-gray-50 border border-gray-200 rounded">
                        <div class="flex justify-between items-center">
                            <div>
                                <div class="font-medium text-sm text-gray-900" id="selected-jina"></div>
                                <div class="text-xs text-gray-600" id="selected-info"></div>
                            </div>
                            <button type="button" onclick="window.manunuziManager?.clearBidhaaSelection()" class="text-red-500 hover:text-red-700 text-sm">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Idadi *</label>
                        <input type="number" name="idadi" id="idadi" min="0.01" step="0.01"
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                               placeholder="Idadi" required>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Aina ya Bei *</label>
                        <select name="bei_type" id="bei_type" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                            <option value="rejareja">Rejareja (Bei per Kimoja)</option>
                            <option value="kwa_zote">Kwa Zote (Bei Jumla)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Bei Nunua (TZS) *</label>
                        <input type="number" step="0.01" name="bei_nunua" id="bei_nunua"
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                               placeholder="Bei kwa 1" required>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Bei Uza (TZS) *</label>
                        <input type="number" step="0.01" name="bei_kuuza" id="bei_kuuza"
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                               placeholder="Bei ya kuuza kwa 1" required>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Tarehe ya Mwisho</label>
                        <input type="date" name="expiry" id="expiry"
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                               value="{{ now()->format('Y-m-d') }}">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Saplaya</label>
                        <input type="text" name="saplaya" id="saplaya"
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                               placeholder="Jina la msaplaya">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Simu</label>
                        <input type="text" name="simu" id="simu"
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                               placeholder="Namba ya simu">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Maelezo</label>
                        <textarea name="mengineyo" id="mengineyo" rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                                  placeholder="Maelezo ya ziada..."></textarea>
                    </div>
                </div>

                <div id="price-error" class="text-red-600 text-xs font-medium hidden"></div>

                <div class="flex gap-2 pt-4 border-t border-gray-200">
                    <button type="submit" class="flex-1 bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700 text-sm font-medium">
                        <i class="fas fa-save mr-1"></i> Hifadhi
                    </button>
                    <button type="reset" class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 text-sm">
                        <i class="fas fa-redo mr-1"></i> Safisha
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- TAB 3: Ripoti -->
    <div id="ripoti-tab-content" class="tab-content hidden">
        <div class="grid grid-cols-1 gap-4">
            <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                <h3 class="text-sm font-medium text-gray-900 mb-3 flex items-center">
                    <i class="fas fa-filter text-emerald-600 mr-2"></i>
                    Chuja Ripoti
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Kuanzia Tarehe</label>
                        <input type="date" id="report-start-date" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                               value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Mpaka Tarehe</label>
                        <input type="date" id="report-end-date" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                               value="{{ now()->format('Y-m-d') }}">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Aina ya Ripoti</label>
                        <select id="report-type" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                            <option value="summary">Muhtasari</option>
                            <option value="detailed">Kina</option>
                            <option value="by-supplier">Kwa Saplaya</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button onclick="window.manunuziManager?.generateReport()" class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 text-sm font-medium">
                            <i class="fas fa-chart-bar mr-1"></i> Generate
                        </button>
                    </div>
                </div>
            </div>

            <div id="report-results" class="hidden">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-4">
                    <div class="bg-emerald-50 p-3 rounded-lg border border-emerald-200">
                        <p class="text-xs text-gray-600 mb-1">Jumla ya Manunuzi</p>
                        <p class="text-xl font-bold text-emerald-700" id="report-total-count">0</p>
                    </div>
                    <div class="bg-blue-50 p-3 rounded-lg border border-blue-200">
                        <p class="text-xs text-gray-600 mb-1">Jumla ya Bidhaa</p>
                        <p class="text-xl font-bold text-blue-700" id="report-total-items">0.00</p>
                    </div>
                    <div class="bg-purple-50 p-3 rounded-lg border border-purple-200">
                        <p class="text-xs text-gray-600 mb-1">Jumla ya Gharama</p>
                        <p class="text-xl font-bold text-purple-700" id="report-total-cost">0.00</p>
                    </div>
                    <div class="bg-amber-50 p-3 rounded-lg border border-amber-200">
                        <p class="text-xs text-gray-600 mb-1">Wastani wa Bei</p>
                        <p class="text-xl font-bold text-amber-700" id="report-avg-cost">0.00</p>
                    </div>
                </div>

                <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm" id="report-table">
                            <thead>
                                <tr class="bg-emerald-50">
                                    <th class="px-4 py-2 text-left font-medium text-emerald-800">Tarehe</th>
                                    <th class="px-4 py-2 text-left font-medium text-emerald-800">Bidhaa/Saplaya</th>
                                    <th class="px-4 py-2 text-center font-medium text-emerald-800">Idadi</th>
                                    <th class="px-4 py-2 text-right font-medium text-emerald-800">Gharama</th>
                                    <th class="px-4 py-2 text-right font-medium text-emerald-800">Bei Kuuza</th>
                                    <th class="px-4 py-2 text-left font-medium text-emerald-800">Maelezo</th>
                                </tr>
                            </thead>
                            <tbody id="report-tbody" class="divide-y divide-gray-100"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="edit-modal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-lg shadow-lg w-full max-w-md mx-auto z-50 max-h-[90vh] overflow-y-auto">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-800">Rekebisha Manunuzi</h3>
        </div>
        <form id="edit-form" method="POST" class="p-4">
            @csrf
            @method('PUT')
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Bidhaa</label>
                    <div class="px-3 py-2 bg-gray-50 border border-gray-300 rounded text-sm text-gray-700">
                        <span id="edit-bidhaa-jina"></span> - 
                        <span id="edit-bidhaa-aina"></span>
                    </div>
                    <input type="hidden" name="bidhaa_id" id="edit-bidhaa_id">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Idadi *</label>
                    <input type="number" name="idadi" id="edit-idadi" min="0.01" step="0.01"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500" required>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Aina ya Bei *</label>
                    <select name="bei_type" id="edit-bei-type" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                        <option value="rejareja">Rejareja (Bei per Kimoja)</option>
                        <option value="kwa_zote">Kwa Zote (Bei Jumla)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Bei Nunua (TZS) *</label>
                    <input type="number" step="0.01" name="bei_nunua" id="edit-bei-nunua"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500" required>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Bei Uza (TZS) *</label>
                    <input type="number" step="0.01" name="bei_kuuza" id="edit-bei-kuuza"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500" required>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tarehe ya Mwisho</label>
                    <input type="date" name="expiry" id="edit-expiry"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Saplaya</label>
                    <input type="text" name="saplaya" id="edit-saplaya"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Simu</label>
                    <input type="text" name="simu" id="edit-simu"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Maelezo</label>
                    <textarea name="mengineyo" id="edit-mengineyo" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"></textarea>
                </div>
            </div>
            
            <div class="flex gap-2 pt-4 border-t border-gray-200 mt-4">
                <button type="button" id="close-edit-modal" class="flex-1 px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 text-sm">
                    Ghairi
                </button>
                <button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 text-sm font-medium">
                    Hifadhi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Modal -->
<div id="delete-modal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-lg shadow-lg w-full max-w-sm mx-auto z-50">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-800">Thibitisha Kufuta</h3>
        </div>
        <div class="p-4">
            <div class="text-center mb-4">
                <i class="fas fa-exclamation-triangle text-amber-500 text-2xl mb-2"></i>
                <p class="text-gray-700 text-sm mb-1">Una uhakika unataka kufuta manunuzi ya?</p>
                <p class="text-gray-900 font-medium" id="delete-product-name"></p>
                <p class="text-gray-500 text-xs mt-2">Hatua hii haiwezi kutenduliwa</p>
            </div>
            <div class="flex gap-2">
                <button id="cancel-delete" class="flex-1 px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 text-sm">
                    Ghairi
                </button>
                <form id="delete-form" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm font-medium">
                        Futa
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
    display: flex;
    align-items: center;
    justify-content: center;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
    animation: fadeIn 0.3s ease-out;
}
#bidhaa-search-results {
    z-index: 1000;
}
.search-highlight {
    background-color: #fef08a;
    padding: 0 2px;
    border-radius: 2px;
}
#search-input {
    pointer-events: auto !important;
    opacity: 1 !important;
    background-color: white !important;
    color: black !important;
    border: 1px solid #d1d5db !important;
    z-index: 10 !important;
}
#search-input:focus {
    border-color: #10b981 !important;
    box-shadow: 0 0 0 1px #10b981 !important;
    outline: none !important;
}
/* Pagination styling */
#pagination-links nav {
    display: inline-flex;
}
#pagination-links .pagination {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    margin: 0;
    padding: 0;
}
#pagination-links .page-item {
    display: inline-block;
    margin: 0;
}
#pagination-links .page-link {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 32px;
    height: 32px;
    padding: 0 8px;
    font-size: 13px;
    font-weight: 500;
    color: #4b5563;
    background-color: white;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    transition: all 0.2s;
}
#pagination-links .page-link:hover {
    background-color: #f3f4f6;
    border-color: #d1d5db;
    color: #1f2937;
}
#pagination-links .active .page-link {
    background-color: #10b981;
    border-color: #10b981;
    color: white;
}
#pagination-links .disabled .page-link {
    background-color: #f9fafb;
    border-color: #e5e7eb;
    color: #9ca3af;
    cursor: not-allowed;
}
@media (max-width: 640px) {
    #pagination-links .pagination {
        flex-wrap: wrap;
        justify-content: center;
    }
    #pagination-links .page-link {
        min-width: 28px;
        height: 28px;
        font-size: 11px;
        padding: 0 6px;
    }
}
</style>
@endpush

@push('scripts')
<script>
// ============================================
// MANUNUZI MANAGER - SEARCHES ALL DATA LIKE BIDHAA
// ============================================

(function() {
    'use strict';

    class ManunuziManager {
        constructor() {
            console.log('ManunuziManager initializing...');
            
            this.currentTab = this.getSavedTab() || 'taarifa';
            this.isSubmitting = false;
            this.searchTimeout = null;
            this.allManunuziData = [];
            this.filteredData = [];
            this.bidhaaData = [];
            this.totalManunuziCount = 0;
            this.currentSearchTerm = '';
            this.currentPage = 1;
            this.perPage = 10;
            this.isSearchActive = false;
            
            // Load data
            this.loadData();
            
            // Initialize
            this.init();
        }

        loadData() {
            try {
                // Load bidhaa data from hidden div
                const bidhaaElement = document.getElementById('bidhaa-data');
                if (bidhaaElement) {
                    this.bidhaaData = JSON.parse(bidhaaElement.textContent);
                } else {
                    this.bidhaaData = [];
                }
                
                // Load ALL manunuzi data from hidden div
                const allManunuziElement = document.getElementById('all-manunuzi-data');
                if (allManunuziElement) {
                    this.allManunuziData = JSON.parse(allManunuziElement.textContent);
                    this.totalManunuziCount = this.allManunuziData.length;
                    this.filteredData = [...this.allManunuziData];
                } else {
                    this.allManunuziData = [];
                    this.filteredData = [];
                    this.totalManunuziCount = 0;
                }
                
                console.log('Loaded ' + this.allManunuziData.length + ' total manunuzi, ' + this.bidhaaData.length + ' bidhaa');
            } catch(e) {
                console.warn('Error loading data:', e);
                this.bidhaaData = [];
                this.allManunuziData = [];
                this.filteredData = [];
                this.totalManunuziCount = 0;
            }
        }

        getSavedTab() {
            try {
                return sessionStorage.getItem('manunuzi_tab') || 'taarifa';
            } catch(e) {
                return 'taarifa';
            }
        }

        saveTab(tab) {
            try {
                sessionStorage.setItem('manunuzi_tab', tab);
            } catch(e) {}
        }

        init() {
            console.log('Setting up ManunuziManager...');
            
            // Setup tabs
            this.setupTabs();
            
            // Setup search
            this.setupSearch();
            
            // Setup bidhaa search
            this.setupBidhaaSearch();
            
            // Setup forms
            this.setupForms();
            
            // Setup action buttons
            this.setupActionButtons();
            
            // Setup modals
            this.setupModals();
            
            // Setup date filters
            this.setupDateFilters();
            
            // Setup report
            this.setupReport();
            
            // Set default dates
            this.setDefaultDates();
            
            // Show initial tab
            this.showTab(this.currentTab);
            
            console.log('ManunuziManager ready!');
        }

        // ============================================
        // TABS
        // ============================================
        setupTabs() {
            document.querySelectorAll('.tab-button').forEach(button => {
                button.addEventListener('click', (e) => {
                    const tab = button.dataset.tab;
                    this.showTab(tab);
                    this.saveTab(tab);
                });
            });
        }

        showTab(tabName) {
            document.querySelectorAll('.tab-button').forEach(button => {
                if (button.dataset.tab === tabName) {
                    button.classList.add('bg-emerald-50', 'text-emerald-700');
                    button.classList.remove('text-gray-600', 'hover:bg-gray-50');
                } else {
                    button.classList.remove('bg-emerald-50', 'text-emerald-700');
                    button.classList.add('text-gray-600', 'hover:bg-gray-50');
                }
            });

            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            
            const tabContent = document.getElementById(`${tabName}-tab-content`);
            if (tabContent) {
                tabContent.classList.remove('hidden');
            }
            
            this.currentTab = tabName;
        }

        // ============================================
        // SEARCH - SEARCHES THROUGH ALL DATA (LIKE BIDHAA)
        // ============================================
        setupSearch() {
            const searchInput = document.getElementById('search-input');
            const clearSearchBtn = document.getElementById('clear-search');
            
            if (!searchInput) return;

            // Initial search from URL parameter
            const initialSearch = searchInput.value;
            if (initialSearch) {
                this.currentSearchTerm = initialSearch.toLowerCase().trim();
                this.filterAllManunuzi(this.currentSearchTerm);
                if (clearSearchBtn) {
                    clearSearchBtn.classList.remove('hidden');
                }
            }

            searchInput.addEventListener('input', (e) => {
                clearTimeout(this.searchTimeout);
                const searchTerm = e.target.value.trim();
                this.currentSearchTerm = searchTerm;
                
                if (clearSearchBtn) {
                    clearSearchBtn.classList.toggle('hidden', !searchTerm);
                }
                
                this.searchTimeout = setTimeout(() => {
                    if (searchTerm.length >= 2) {
                        this.isSearchActive = true;
                        this.filterAllManunuzi(searchTerm.toLowerCase());
                    } else if (searchTerm.length === 0) {
                        this.isSearchActive = false;
                        this.clearSearch();
                    }
                }, 400);
            });
            
            // Clear search button
            if (clearSearchBtn) {
                clearSearchBtn.addEventListener('click', () => {
                    this.clearSearch();
                });
            }
        }

        clearSearch() {
            const searchInput = document.getElementById('search-input');
            const clearSearchBtn = document.getElementById('clear-search');
            const searchStatus = document.getElementById('search-status');
            
            if (searchInput) {
                searchInput.value = '';
                this.currentSearchTerm = '';
            }
            if (clearSearchBtn) {
                clearSearchBtn.classList.add('hidden');
            }
            
            this.isSearchActive = false;
            this.filteredData = [...this.allManunuziData];
            this.currentPage = 1;
            this.renderTable();
            this.updatePaginationInfo();
            
            if (searchStatus) {
                searchStatus.classList.add('hidden');
            }
            
            // Show pagination
            const pagination = document.getElementById('pagination-links');
            if (pagination && this.allManunuziData.length > this.perPage) {
                pagination.style.display = 'block';
            }
        }

        filterAllManunuzi(searchTerm) {
            // Reset to page 1 when searching
            this.currentPage = 1;
            
            if (!searchTerm) {
                this.filteredData = [...this.allManunuziData];
            } else {
                // Search through ALL data
                this.filteredData = this.allManunuziData.filter(item => {
                    const searchText = `
                        ${item.bidhaa?.jina || ''}
                        ${item.bidhaa?.aina || ''}
                        ${item.saplaya || ''}
                        ${item.simu || ''}
                        ${item.mengineyo || ''}
                    `.toLowerCase();
                    return searchText.includes(searchTerm);
                });
            }
            
            // Update the table with filtered data
            this.renderTable();
            
            // Update pagination info
            this.updatePaginationInfo();
            
            // Update search status
            this.updateSearchStatus();
            
            // Show notification if no results
            if (this.filteredData.length === 0 && searchTerm) {
                this.showNotification('Hakuna manunuzi zinazolingana na "' + searchTerm + '"', 'info');
            }
        }

        renderTable() {
            const tbody = document.getElementById('manunuzi-tbody');
            if (!tbody) return;
            
            // Calculate pagination
            const start = (this.currentPage - 1) * this.perPage;
            const end = start + this.perPage;
            const pageData = this.filteredData.slice(start, end);
            
            if (pageData.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            <i class="fas fa-shopping-cart text-3xl mb-2 text-gray-300"></i>
                            <p>Hakuna manunuzi yanayolingana</p>
                        </td>
                    </tr>
                `;
                return;
            }
            
            let html = '';
            pageData.forEach(item => {
                const formattedIdadi = parseFloat(item.idadi || 0);
                const displayIdadi = formattedIdadi % 1 === 0 ? Math.floor(formattedIdadi) : formattedIdadi.toFixed(2);
                
                html += `
                    <tr class="manunuzi-row hover:bg-gray-50" data-manunuzi='${JSON.stringify(item).replace(/'/g, "&#39;")}'>
                        <td class="px-4 py-2">
                            <div class="text-xs text-gray-900">${item.created_at_formatted || ''}</div>
                            <div class="text-xs text-gray-500">${item.created_time || ''}</div>
                        </td>
                        <td class="px-4 py-2 hidden sm:table-cell">
                            <div class="font-medium text-gray-900 text-sm">${this.highlightText(item.bidhaa?.jina || '--', this.currentSearchTerm)}</div>
                            <div class="text-xs text-gray-500">${this.highlightText(item.bidhaa?.aina || '--', this.currentSearchTerm)}</div>
                            ${item.bidhaa?.kipimo ? `<div class="text-xs text-gray-400">${item.bidhaa.kipimo}</div>` : ''}
                        </td>
                        <td class="px-4 py-2 text-center">
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-emerald-100 text-emerald-800">
                                ${displayIdadi}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-right">
                            <div class="text-sm font-bold text-emerald-700">${parseFloat(item.bei || 0).toFixed(2)}</div>
                            <div class="text-xs text-gray-500">@ ${parseFloat(item.unit_cost || 0).toFixed(2)}</div>
                        </td>
                        <td class="px-4 py-2 text-right">
                            <div class="text-sm font-bold text-green-700">${parseFloat(item.bidhaa?.bei_kuuza || 0).toFixed(2)}</div>
                        </td>
                        <td class="px-4 py-2 hidden lg:table-cell">
                            <div class="text-xs text-gray-700">${this.highlightText(item.saplaya || '--', this.currentSearchTerm)}</div>
                            ${item.simu ? `<div class="text-xs text-gray-400">${this.highlightText(item.simu, this.currentSearchTerm)}</div>` : ''}
                        </td>
                        <td class="px-4 py-2 text-center print:hidden">
                            <div class="flex justify-center space-x-2">
                                <button class="edit-manunuzi-btn text-emerald-600 hover:text-emerald-800"
                                        data-id="${item.id}" title="Badili">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="delete-manunuzi-btn text-red-600 hover:text-red-800"
                                        data-id="${item.id}" data-name="${item.bidhaa?.jina || 'Bidhaa'}" title="Futa">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
            
            tbody.innerHTML = html;
            
            // Re-bind action buttons
            this.setupActionButtons();
        }

        highlightText(text, searchTerm) {
            if (!text || !searchTerm || searchTerm.length < 2) return text;
            const regex = new RegExp(`(${searchTerm.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
            return text.replace(regex, '<span class="search-highlight">$1</span>');
        }

        updateSearchStatus() {
            const searchStatus = document.getElementById('search-status');
            const searchResultCount = document.getElementById('search-result-count');
            
            if (!searchStatus || !searchResultCount) return;
            
            if (this.isSearchActive && this.currentSearchTerm) {
                searchStatus.classList.remove('hidden');
                const total = this.filteredData.length;
                const all = this.allManunuziData.length;
                searchResultCount.innerHTML = `
                    <div class="bg-emerald-50 p-2 rounded">
                        <span class="font-bold text-emerald-700">${total}</span> 
                        <span class="text-gray-600">manunuzi zinaonyeshwa kati ya ${all}</span>
                        <button onclick="window.manunuziManager.clearSearch()" 
                                class="ml-2 text-xs bg-emerald-600 text-white px-2 py-1 rounded hover:bg-emerald-700">
                            <i class="fas fa-times mr-1"></i> Ondoa
                        </button>
                    </div>
                `;
            } else {
                searchStatus.classList.add('hidden');
            }
        }

        updatePaginationInfo() {
            const visibleCount = this.filteredData.length;
            const totalCount = this.allManunuziData.length;
            
            const visibleCountSpan = document.getElementById('visible-count');
            const totalCountSpan = document.getElementById('total-count');
            const paginationInfo = document.querySelector('.manunuzi-pagination-info');
            
            if (visibleCountSpan) visibleCountSpan.textContent = visibleCount;
            if (totalCountSpan) totalCountSpan.textContent = totalCount;
            
            if (paginationInfo) {
                paginationInfo.textContent = `Inaonyesha ${visibleCount} kati ya ${totalCount} manunuzi`;
            }
            
            // Update pagination links
            this.updatePaginationLinks();
        }

        updatePaginationLinks() {
            const paginationContainer = document.getElementById('pagination-links');
            if (!paginationContainer) return;
            
            const totalPages = Math.ceil(this.filteredData.length / this.perPage);
            
            if (totalPages <= 1) {
                paginationContainer.innerHTML = '';
                return;
            }
            
            let html = '<nav class="flex items-center space-x-1">';
            
            // Previous button
            html += `
                <button onclick="window.manunuziManager.goToPage(${this.currentPage - 1})" 
                        class="px-3 py-1 rounded text-sm ${this.currentPage <= 1 ? 'text-gray-300 cursor-not-allowed' : 'text-gray-700 hover:bg-gray-100'}"
                        ${this.currentPage <= 1 ? 'disabled' : ''}>
                    &laquo;
                </button>
            `;
            
            // Page numbers
            const maxVisiblePages = 5;
            let startPage = Math.max(1, this.currentPage - Math.floor(maxVisiblePages / 2));
            let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
            
            if (endPage - startPage < maxVisiblePages - 1) {
                startPage = Math.max(1, endPage - maxVisiblePages + 1);
            }
            
            if (startPage > 1) {
                html += `<button onclick="window.manunuziManager.goToPage(1)" class="px-3 py-1 rounded text-sm text-gray-700 hover:bg-gray-100">1</button>`;
                if (startPage > 2) {
                    html += `<span class="px-2 text-gray-400">...</span>`;
                }
            }
            
            for (let i = startPage; i <= endPage; i++) {
                html += `
                    <button onclick="window.manunuziManager.goToPage(${i})" 
                            class="px-3 py-1 rounded text-sm ${i === this.currentPage ? 'bg-emerald-600 text-white' : 'text-gray-700 hover:bg-gray-100'}">
                        ${i}
                    </button>
                `;
            }
            
            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    html += `<span class="px-2 text-gray-400">...</span>`;
                }
                html += `<button onclick="window.manunuziManager.goToPage(${totalPages})" class="px-3 py-1 rounded text-sm text-gray-700 hover:bg-gray-100">${totalPages}</button>`;
            }
            
            // Next button
            html += `
                <button onclick="window.manunuziManager.goToPage(${this.currentPage + 1})" 
                        class="px-3 py-1 rounded text-sm ${this.currentPage >= totalPages ? 'text-gray-300 cursor-not-allowed' : 'text-gray-700 hover:bg-gray-100'}"
                        ${this.currentPage >= totalPages ? 'disabled' : ''}>
                    &raquo;
                </button>
            `;
            
            html += '</nav>';
            paginationContainer.innerHTML = html;
        }

        goToPage(page) {
            const totalPages = Math.ceil(this.filteredData.length / this.perPage);
            if (page < 1 || page > totalPages) return;
            
            this.currentPage = page;
            this.renderTable();
            this.updatePaginationLinks();
            
            // Scroll to top of table
            const tableContainer = document.querySelector('.bg-white.rounded-lg.border.border-gray-200.shadow-sm.overflow-hidden');
            if (tableContainer) {
                tableContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }

        // ============================================
        // BIDHAA SEARCH
        // ============================================
        setupBidhaaSearch() {
            const searchInput = document.getElementById('bidhaa-search-input');
            const searchResults = document.getElementById('bidhaa-search-results');
            
            if (!searchInput || !searchResults) return;

            let searchTimeout = null;

            searchInput.addEventListener('input', (e) => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    const query = e.target.value.toLowerCase().trim();
                    if (query.length < 2) {
                        searchResults.classList.add('hidden');
                        return;
                    }
                    
                    const filtered = this.bidhaaData.filter(b => 
                        (b.jina && b.jina.toLowerCase().includes(query)) || 
                        (b.aina && b.aina.toLowerCase().includes(query))
                    );
                    
                    this.displaySearchResults(filtered);
                }, 300);
            });

            // Hide results when clicking outside
            document.addEventListener('click', (e) => {
                if (!e.target.closest('#bidhaa-search-input') && !e.target.closest('#bidhaa-search-results')) {
                    searchResults.classList.add('hidden');
                }
            });
        }

        displaySearchResults(bidhaa) {
            const searchResults = document.getElementById('bidhaa-search-results');
            
            if (!bidhaa || bidhaa.length === 0) {
                searchResults.innerHTML = '<div class="p-3 text-sm text-gray-500">Hakuna bidhaa zinazolingana</div>';
                searchResults.classList.remove('hidden');
                return;
            }

            let html = '';
            bidhaa.forEach(item => {
                html += `
                    <div class="p-3 border-b border-gray-100 hover:bg-emerald-50 cursor-pointer"
                         onclick="window.manunuziManager.selectBidhaa(${JSON.stringify(item).replace(/"/g, '&quot;')})">
                        <div class="font-medium text-sm text-gray-900">${this.escapeHtml(item.jina)}</div>
                        <div class="text-xs text-gray-600">${this.escapeHtml(item.aina)} - ${this.escapeHtml(item.kipimo)}</div>
                        <div class="text-xs text-emerald-600 mt-1">
                            Bei: ${parseFloat(item.bei_nunua || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}
                        </div>
                    </div>
                `;
            });

            searchResults.innerHTML = html;
            searchResults.classList.remove('hidden');
        }

        selectBidhaa(item) {
            const searchInput = document.getElementById('bidhaa-search-input');
            const searchResults = document.getElementById('bidhaa-search-results');
            const bidhaaIdInput = document.getElementById('bidhaa_id');
            const selectedInfoDiv = document.getElementById('selected-bidhaa-info');
            const selectedJina = document.getElementById('selected-jina');
            const selectedInfo = document.getElementById('selected-info');

            bidhaaIdInput.value = item.id;
            searchInput.value = item.jina;
            selectedJina.textContent = item.jina || '';
            selectedInfo.textContent = `${item.aina || ''} - ${item.kipimo || ''}`;
            selectedInfoDiv.classList.remove('hidden');
            
            // Set selling price
            const beiKuuzaInput = document.getElementById('bei_kuuza');
            if (beiKuuzaInput && item.bei_kuuza) {
                beiKuuzaInput.value = item.bei_kuuza;
            }
            
            // Clear purchase price
            document.getElementById('bei_nunua').value = '';
            
            // Reset to rejareja
            document.getElementById('bei_type').value = 'rejareja';
            
            // Focus on quantity
            document.getElementById('idadi').focus();
            
            searchResults.classList.add('hidden');
        }

        clearBidhaaSelection() {
            document.getElementById('bidhaa-search-input').value = '';
            document.getElementById('bidhaa_id').value = '';
            document.getElementById('selected-bidhaa-info').classList.add('hidden');
            document.getElementById('bei_nunua').value = '';
            document.getElementById('idadi').focus();
        }

        // ============================================
        // FORMS
        // ============================================
        setupForms() {
            const manunuziForm = document.getElementById('manunuzi-form');
            if (manunuziForm) {
                manunuziForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.submitForm(e.currentTarget, 'Manunuzi yamehifadhiwa!');
                });
            }

            const editForm = document.getElementById('edit-form');
            if (editForm) {
                editForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.submitForm(e.currentTarget, 'Manunuzi yamebadilishwa!');
                    document.getElementById('edit-modal').classList.add('hidden');
                });
            }

            const deleteForm = document.getElementById('delete-form');
            if (deleteForm) {
                deleteForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.submitForm(e.currentTarget, 'Manunuzi yamefutwa!');
                    document.getElementById('delete-modal').classList.add('hidden');
                });
            }
        }

        async submitForm(form, successMessage = 'Operesheni imekamilika!') {
            if (this.isSubmitting) {
                this.showNotification('Tafadhali subiri...', 'warning');
                return;
            }
            
            this.isSubmitting = true;
            const formData = new FormData(form);
            const submitButton = form.querySelector('button[type="submit"]');
            let originalText = '';
            
            if (submitButton) {
                originalText = submitButton.innerHTML;
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Inatumwa...';
            }
            
            try {
                const response = await fetch(form.action, {
                    method: form.method || 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    this.showNotification(data.message || successMessage, 'success');
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    const error = data.errors ? Object.values(data.errors)[0][0] : data.message;
                    this.showNotification(error || 'Hitilafu imetokea', 'error');
                }
            } catch (error) {
                console.error('Form submission error:', error);
                this.showNotification('Hitilafu ya mtandao. Tafadhali jaribu tena.', 'error');
            } finally {
                this.isSubmitting = false;
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalText;
                }
            }
        }

        // ============================================
        // ACTION BUTTONS
        // ============================================
        setupActionButtons() {
            document.querySelectorAll('.edit-manunuzi-btn').forEach(button => {
                // Remove existing listeners
                const newButton = button.cloneNode(true);
                button.parentNode.replaceChild(newButton, button);
                
                newButton.addEventListener('click', (e) => {
                    const row = newButton.closest('.manunuzi-row');
                    if (!row) return;
                    
                    let manunuzi;
                    try {
                        manunuzi = JSON.parse(row.dataset.manunuzi);
                    } catch(e) {
                        this.showNotification('Hitilafu katika kupakia data', 'error');
                        return;
                    }
                    
                    this.editManunuzi(manunuzi);
                });
            });

            document.querySelectorAll('.delete-manunuzi-btn').forEach(button => {
                const newButton = button.cloneNode(true);
                button.parentNode.replaceChild(newButton, button);
                
                newButton.addEventListener('click', (e) => {
                    const manunuziId = newButton.dataset.id;
                    const productName = newButton.dataset.name || 'Bidhaa';
                    this.deleteManunuzi(manunuziId, productName);
                });
            });
        }

        editManunuzi(manunuzi) {
            const editForm = document.getElementById('edit-form');
            if (!editForm) return;

            const wasKwaZote = Math.abs(manunuzi.bei - (manunuzi.unit_cost * manunuzi.idadi)) > 0.01;
            
            const setVal = (id, val) => {
                const el = document.getElementById(id);
                if (el) el.value = val !== null && val !== undefined ? val : '';
            };
            
            const setText = (id, val) => {
                const el = document.getElementById(id);
                if (el) el.textContent = val || '';
            };
            
            setText('edit-bidhaa-jina', manunuzi.bidhaa?.jina || '');
            setText('edit-bidhaa-aina', manunuzi.bidhaa?.aina || '');
            setVal('edit-bidhaa_id', manunuzi.bidhaa_id);
            setVal('edit-idadi', manunuzi.idadi);
            setVal('edit-bei-type', wasKwaZote ? 'kwa_zote' : 'rejareja');
            setVal('edit-bei-nunua', wasKwaZote ? manunuzi.bei : manunuzi.unit_cost);
            setVal('edit-bei-kuuza', manunuzi.bidhaa?.bei_kuuza || '');
            setVal('edit-expiry', manunuzi.expiry ? manunuzi.expiry.split('T')[0] : '');
            setVal('edit-saplaya', manunuzi.saplaya || '');
            setVal('edit-simu', manunuzi.simu || '');
            setVal('edit-mengineyo', manunuzi.mengineyo || '');
            
            editForm.action = `/manunuzi/${manunuzi.id}`;
            
            document.getElementById('edit-modal').classList.remove('hidden');
        }

        deleteManunuzi(manunuziId, productName) {
            const deleteForm = document.getElementById('delete-form');
            const deleteModal = document.getElementById('delete-modal');
            const deleteProductName = document.getElementById('delete-product-name');
            
            if (!deleteForm || !deleteModal || !deleteProductName) return;
            
            deleteProductName.textContent = productName || 'Bidhaa';
            deleteForm.action = `/manunuzi/${manunuziId}`;
            deleteModal.classList.remove('hidden');
        }

        // ============================================
        // MODALS
        // ============================================
        setupModals() {
            document.getElementById('close-edit-modal')?.addEventListener('click', () => {
                document.getElementById('edit-modal').classList.add('hidden');
            });

            document.getElementById('cancel-delete')?.addEventListener('click', () => {
                document.getElementById('delete-modal').classList.add('hidden');
            });

            document.querySelectorAll('.modal').forEach(modal => {
                modal.addEventListener('click', (e) => {
                    if (e.target === modal || e.target.classList.contains('modal-overlay')) {
                        modal.classList.add('hidden');
                    }
                });
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    document.getElementById('edit-modal')?.classList.add('hidden');
                    document.getElementById('delete-modal')?.classList.add('hidden');
                }
            });
        }

        // ============================================
        // DATE FILTERS
        // ============================================
        setupDateFilters() {
            this.setDefaultDates();
        }

        setDefaultDates() {
            const today = new Date().toISOString().split('T')[0];
            const startOfMonth = new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0];
            
            const startDate = document.getElementById('start-date');
            const endDate = document.getElementById('end-date');
            const expiryInput = document.getElementById('expiry');
            
            if (startDate && !startDate.value) startDate.value = startOfMonth;
            if (endDate && !endDate.value) endDate.value = today;
            if (expiryInput && !expiryInput.value) expiryInput.value = today;
        }

        filterByDateRange() {
            const startDate = document.getElementById('start-date')?.value;
            const endDate = document.getElementById('end-date')?.value;
            
            if (!startDate || !endDate) {
                this.showNotification('Tafadhali chagua tarehe zote mbili', 'warning');
                return;
            }
            
            const start = new Date(startDate);
            start.setHours(0, 0, 0, 0);
            const end = new Date(endDate);
            end.setHours(23, 59, 59, 999);
            
            // Filter the ALL data
            this.filteredData = this.allManunuziData.filter(item => {
                const itemDate = new Date(item.created_at);
                return itemDate >= start && itemDate <= end;
            });
            
            // Also apply search if there is a search term
            if (this.currentSearchTerm) {
                this.filteredData = this.filteredData.filter(item => {
                    const searchText = `
                        ${item.bidhaa?.jina || ''}
                        ${item.bidhaa?.aina || ''}
                        ${item.saplaya || ''}
                        ${item.simu || ''}
                        ${item.mengineyo || ''}
                    `.toLowerCase();
                    return searchText.includes(this.currentSearchTerm);
                });
            }
            
            this.currentPage = 1;
            this.renderTable();
            this.updatePaginationInfo();
            this.updateSearchStatus();
            
            const dateRangeInfo = document.getElementById('date-range-info');
            const dateRangeText = document.getElementById('date-range-text');
            
            if (this.filteredData.length > 0) {
                if (dateRangeInfo) dateRangeInfo.classList.remove('hidden');
                if (dateRangeText) {
                    dateRangeText.textContent = `${new Date(startDate).toLocaleDateString()} - ${new Date(endDate).toLocaleDateString()} (${this.filteredData.length} manunuzi)`;
                }
            } else {
                if (dateRangeInfo) dateRangeInfo.classList.add('hidden');
                this.showNotification('Hakuna manunuzi katika kipindi hiki', 'info');
            }
        }

        clearDateFilter() {
            // Reset to all data with current search
            this.filteredData = [...this.allManunuziData];
            
            if (this.currentSearchTerm) {
                this.filteredData = this.filteredData.filter(item => {
                    const searchText = `
                        ${item.bidhaa?.jina || ''}
                        ${item.bidhaa?.aina || ''}
                        ${item.saplaya || ''}
                        ${item.simu || ''}
                        ${item.mengineyo || ''}
                    `.toLowerCase();
                    return searchText.includes(this.currentSearchTerm);
                });
            }
            
            this.currentPage = 1;
            this.renderTable();
            this.updatePaginationInfo();
            this.updateSearchStatus();
            
            document.getElementById('date-range-info')?.classList.add('hidden');
            this.setDefaultDates();
        }

        // ============================================
        // REPORT
        // ============================================
        setupReport() {
            // Report is triggered via onclick
        }

        generateReport() {
            const startDate = document.getElementById('report-start-date')?.value;
            const endDate = document.getElementById('report-end-date')?.value;
            const reportType = document.getElementById('report-type')?.value || 'summary';
            
            if (!startDate || !endDate) {
                this.showNotification('Tafadhali chagua tarehe zote mbili', 'warning');
                return;
            }
            
            const start = new Date(startDate);
            start.setHours(0, 0, 0, 0);
            const end = new Date(endDate);
            end.setHours(23, 59, 59, 999);
            
            const filteredData = this.allManunuziData.filter(item => {
                const itemDate = new Date(item.created_at);
                return itemDate >= start && itemDate <= end;
            });
            
            if (filteredData.length === 0) {
                this.showNotification('Hakuna data katika kipindi hiki', 'info');
                return;
            }
            
            const totalCount = filteredData.length;
            const totalItems = filteredData.reduce((sum, item) => sum + parseFloat(item.idadi || 0), 0);
            const totalCost = filteredData.reduce((sum, item) => sum + parseFloat(item.bei || 0), 0);
            const avgCost = totalCount > 0 ? totalCost / totalCount : 0;
            
            document.getElementById('report-total-count').textContent = totalCount;
            document.getElementById('report-total-items').textContent = totalItems.toFixed(2);
            document.getElementById('report-total-cost').textContent = totalCost.toFixed(2);
            document.getElementById('report-avg-cost').textContent = avgCost.toFixed(2);
            
            let tableHtml = '';
            
            if (reportType === 'summary') {
                const grouped = {};
                filteredData.forEach(item => {
                    const date = new Date(item.created_at).toLocaleDateString();
                    if (!grouped[date]) grouped[date] = { count: 0, items: 0, cost: 0 };
                    grouped[date].count++;
                    grouped[date].items += parseFloat(item.idadi || 0);
                    grouped[date].cost += parseFloat(item.bei || 0);
                });
                
                Object.keys(grouped).sort().forEach(date => {
                    tableHtml += `
                        <tr>
                            <td class="px-4 py-2">${date}</td>
                            <td class="px-4 py-2">Manunuzi ${grouped[date].count}</td>
                            <td class="px-4 py-2 text-center">${grouped[date].items.toFixed(2)}</td>
                            <td class="px-4 py-2 text-right">${grouped[date].cost.toFixed(2)}</td>
                            <td class="px-4 py-2 text-right">-</td>
                            <td class="px-4 py-2">-</td>
                        </tr>
                    `;
                });
            } else if (reportType === 'by-supplier') {
                const grouped = {};
                filteredData.forEach(item => {
                    const supplier = item.saplaya || 'Hakuna';
                    if (!grouped[supplier]) grouped[supplier] = { count: 0, items: 0, cost: 0 };
                    grouped[supplier].count++;
                    grouped[supplier].items += parseFloat(item.idadi || 0);
                    grouped[supplier].cost += parseFloat(item.bei || 0);
                });
                
                Object.keys(grouped).sort().forEach(supplier => {
                    tableHtml += `
                        <tr>
                            <td class="px-4 py-2">-</td>
                            <td class="px-4 py-2">${this.escapeHtml(supplier)}</td>
                            <td class="px-4 py-2 text-center">${grouped[supplier].items.toFixed(2)}</td>
                            <td class="px-4 py-2 text-right">${grouped[supplier].cost.toFixed(2)}</td>
                            <td class="px-4 py-2 text-right">-</td>
                            <td class="px-4 py-2">${this.escapeHtml(supplier)}</td>
                        </tr>
                    `;
                });
            } else {
                filteredData.forEach(item => {
                    tableHtml += `
                        <tr>
                            <td class="px-4 py-2">${new Date(item.created_at).toLocaleDateString()}</td>
                            <td class="px-4 py-2">${this.escapeHtml(item.bidhaa?.jina || '')}</td>
                            <td class="px-4 py-2 text-center">${parseFloat(item.idadi || 0).toFixed(2)}</td>
                            <td class="px-4 py-2 text-right">${parseFloat(item.bei || 0).toFixed(2)}</td>
                            <td class="px-4 py-2 text-right">${parseFloat(item.bidhaa?.bei_kuuza || 0).toFixed(2)}</td>
                            <td class="px-4 py-2">${this.escapeHtml(item.saplaya || '--')}</td>
                        </tr>
                    `;
                });
            }
            
            document.getElementById('report-tbody').innerHTML = tableHtml;
            document.getElementById('report-results').classList.remove('hidden');
        }

        // ============================================
        // PRINT & EXPORT
        // ============================================
        printManunuzi() {
            // Use filtered data for printing
            const dataToPrint = this.filteredData;
            
            if (dataToPrint.length === 0) {
                this.showNotification('Hakuna manunuzi ya kuchapisha', 'warning');
                return;
            }
            
            let tableRows = '';
            dataToPrint.forEach(item => {
                const formattedIdadi = parseFloat(item.idadi || 0).toFixed(2);
                tableRows += `
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 8px;">${item.created_at_formatted || ''}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">${this.escapeHtml(item.bidhaa?.jina || '')}</td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">${formattedIdadi}</td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">${parseFloat(item.bei || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">${parseFloat(item.bidhaa?.bei_kuuza || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">${this.escapeHtml(item.saplaya || '--')}</td>
                    </tr>`;
            });
            
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Orodha ya Manunuzi - ${new Date().toLocaleDateString()}</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; }
                        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                        th, td { border: 1px solid #ddd; padding: 8px; }
                        th { background-color: #f3f4f6; font-weight: bold; }
                        .header { text-align: center; margin-bottom: 30px; }
                        .header h2 { margin: 0; color: #047857; }
                        .header p { margin: 5px 0 0 0; color: #6b7280; }
                        .search-info { text-align: center; color: #6b7280; font-size: 14px; margin-top: 10px; }
                        .total-info { text-align: right; margin-top: 20px; font-weight: bold; }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h2>Orodha ya Manunuzi</h2>
                        <p>${new Date().toLocaleDateString()}</p>
                        ${this.currentSearchTerm ? `<p class="search-info">Matokeo ya utafutaji: "${this.currentSearchTerm}"</p>` : ''}
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Tarehe</th>
                                <th>Bidhaa</th>
                                <th>Idadi</th>
                                <th>Bei Nunua</th>
                                <th>Bei Uza</th>
                                <th>Saplaya</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${tableRows}
                        </tbody>
                    </table>
                    <div class="total-info">
                        Jumla ya Manunuzi: ${dataToPrint.length} | 
                        Jumla ya Gharama: ${dataToPrint.reduce((sum, item) => sum + parseFloat(item.bei || 0), 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}
                    </div>
                </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.print();
        }

        exportPDF() {
            const search = new URLSearchParams(window.location.search);
            search.set('export', 'pdf');
            window.open(`${window.location.pathname}?${search.toString()}`, '_blank');
        }

        exportExcel() {
            const search = new URLSearchParams(window.location.search);
            search.set('export', 'excel');
            window.location.href = `${window.location.pathname}?${search.toString()}`;
        }

        // ============================================
        // HELPERS
        // ============================================
        escapeHtml(text) {
            if (!text) return '';
            return String(text)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        showNotification(message, type = 'info') {
            const container = document.getElementById('notification-container');
            if (!container) return;
            
            const colors = {
                success: 'bg-emerald-50 border-emerald-200 text-emerald-800',
                error: 'bg-red-50 border-red-200 text-red-800',
                warning: 'bg-amber-50 border-amber-200 text-amber-800',
                info: 'bg-blue-50 border-blue-200 text-blue-800'
            };

            const notification = document.createElement('div');
            notification.className = `rounded border px-4 py-3 text-sm font-medium mb-2 ${colors[type] || colors.info} shadow-sm`;
            notification.textContent = message;

            container.appendChild(notification);

            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transition = 'opacity 0.3s ease';
                setTimeout(() => notification.remove(), 300);
            }, 4000);
        }
    }

    // ============================================
    // INITIALIZATION
    // ============================================
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, initializing ManunuziManager...');
        window.manunuziManager = new ManunuziManager();
    });

    // Global function for clear search (called from HTML)
    function clearManunuziSearch() {
        if (window.manunuziManager) {
            window.manunuziManager.clearSearch();
        }
    }

})();
</script>
@endpush