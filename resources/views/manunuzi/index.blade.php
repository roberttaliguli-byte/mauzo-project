@extends('layouts.app')

@section('title', 'Manunuzi')

@section('page-title', 'Manunuzi')
@section('page-subtitle', now()->format('d/m/Y'))

@section('content')
<div class="space-y-4" id="app-container" data-current-page="{{ request()->get('page', 1) }}">
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

    <!-- Stats with Links -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="bg-white p-3 rounded-lg border border-emerald-200 shadow-sm">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Manunuzi Ya Leo</p>
                    <p class="text-xl font-bold text-emerald-700">{{ $todayPurchases }}</p>
                </div>
                <i class="fas fa-shopping-cart text-emerald-500 text-lg"></i>
            </div>
        </div>
        <div class="bg-white p-3 rounded-lg border border-blue-200 shadow-sm">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Bidhaa Zilizonunuliwa</p>
                    <p class="text-xl font-bold text-blue-700">{{ number_format($totalItemsPurchased, 2) }}</p>
                </div>
                <i class="fas fa-boxes text-blue-500 text-lg"></i>
            </div>
        </div>
        <div class="bg-white p-3 rounded-lg border border-purple-200 shadow-sm">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Jumla ya Gharama</p>
                    <p class="text-xl font-bold text-purple-700">{{ number_format($totalCost, 2) }}</p>
                </div>
                <i class="fas fa-money-bill-wave text-purple-500 text-lg"></i>
            </div>
        </div>
        <div class="bg-white p-3 rounded-lg border border-amber-200 shadow-sm">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Gharama Ya Leo</p>
                    <p class="text-xl font-bold text-amber-700">{{ number_format($todayCost, 2) }}</p>
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
        <!-- Search and Filter Bar -->
        <div class="bg-white p-3 rounded-lg border border-gray-200 shadow-sm">
            <div class="flex flex-col gap-3">
                <!-- Search Input -->
                <div class="flex flex-col sm:flex-row gap-2 sm:items-center">
                    <div class="flex-1 relative">
                        <input 
                            type="text" 
                            id="search-input"
                            placeholder="Tafuta bidhaa, saplaya, simu..." 
                            class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500"
                            value="{{ request()->search }}"
                        >
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="printManunuzi()" class="px-3 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 text-sm font-medium">
                            <i class="fas fa-print mr-1"></i> Print
                        </button>
                        <button onclick="exportPDF()" class="px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm font-medium">
                            <i class="fas fa-file-pdf mr-1"></i> PDF
                        </button>
                        <button onclick="exportExcel()" class="px-3 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm font-medium">
                            <i class="fas fa-file-excel mr-1"></i> Excel
                        </button>
                    </div>
                </div>
                
                <!-- Date Range Filter -->
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-2 items-end">
                    <div class="sm:col-span-1">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Kuanzia Tarehe</label>
                        <input type="date" id="start-date" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    </div>
                    <div class="sm:col-span-1">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Mpaka Tarehe</label>
                        <input type="date" id="end-date" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    </div>
                    <div class="sm:col-span-2 flex gap-2">
                        <button onclick="filterByDateRange()" class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 text-sm font-medium">
                            <i class="fas fa-filter mr-1"></i> Chuja
                        </button>
                        <button onclick="clearDateFilter()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 text-sm">
                            <i class="fas fa-times mr-1"></i> Ondoa
                        </button>
                        <span id="date-range-info" class="hidden text-sm text-gray-600 ml-2 self-center">
                            <i class="fas fa-calendar-alt mr-1 text-emerald-600"></i>
                            <span id="date-range-text"></span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Manunuzi Table -->
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
                            <tr class="manunuzi-row hover:bg-gray-50" data-manunuzi='@json($item)'>
                                <td class="px-4 py-2">
                                    <div class="text-xs text-gray-900">{{ $item->created_at->format('d/m/Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $item->created_at->format('H:i') }}</div>
                                </td>
                                <td class="px-4 py-2 hidden sm:table-cell">
                                    <div class="font-medium text-gray-900 text-sm">{{ $item->bidhaa->jina }}</div>
                                    <div class="text-xs text-gray-500">{{ $item->bidhaa->aina }}</div>
                                    <div class="text-xs text-emerald-600">{{ $item->bidhaa->kipimo }}</div>
                                </td>
                                <td class="px-4 py-2 text-center">
                                    @php
                                        $formattedIdadi = $item->idadi;
                                        if (is_numeric($item->idadi)) {
                                            $formattedIdadi = $item->idadi % 1 == 0 ? (string)(int)$item->idadi : number_format($item->idadi, 2);
                                        }
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-emerald-100 text-emerald-800">
                                        {{ $formattedIdadi }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-right">
                                    <!-- Show total purchase price -->
                                    <div class="text-sm font-bold text-emerald-700">{{ number_format($item->bei, 2) }}</div>
                                    <!-- Show unit cost per item -->
                                    <div class="text-xs text-gray-500">@ {{ number_format($item->unit_cost, 2) }} / 1</div>
                                </td>
                                <td class="px-4 py-2 text-right">
                                    <!-- Show selling price per item -->
                                    <div class="text-sm font-bold text-green-700">{{ number_format($item->bidhaa->bei_kuuza, 2) }}</div>
                                    <div class="text-xs text-gray-500">@ {{ number_format($item->bidhaa->bei_kuuza, 2) }} / 1</div>
                                </td>
                                <td class="px-4 py-2 hidden lg:table-cell">
                                    <div class="text-xs text-gray-700">{{ $item->saplaya ?? '--' }}</div>
                                    <div class="text-xs text-gray-500">{{ $item->simu ?? '--' }}</div>
                                </td>
                                <td class="px-4 py-2 text-center print:hidden">
                                    <div class="flex justify-center space-x-2">
                                        <button class="edit-manunuzi-btn text-emerald-600 hover:text-emerald-800"
                                                data-id="{{ $item->id }}" title="Badili">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="delete-manunuzi-btn text-red-600 hover:text-red-800"
                                                data-id="{{ $item->id }}" data-name="{{ $item->bidhaa->jina }}" title="Futa">
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
            
            <!-- Pagination -->
            @if($manunuzi->hasPages())
            <div id="pagination-container" class="px-4 py-3 border-t border-gray-200">
                {{ $manunuzi->links() }}
            </div>
            @endif
            
            <!-- Table Summary -->
            <div id="table-summary" class="hidden px-4 py-3 bg-gray-50 border-t border-gray-200 text-sm">
                <div class="flex justify-between items-center">
                    <span class="font-medium text-gray-700">Jumla ya Manunuzi yaliyoonyeshwa:</span>
                    <span class="font-bold text-emerald-700" id="displayed-total">0</span>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 2: Ingiza -->
    <div id="ingiza-tab-content" class="tab-content hidden">
        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
            <form method="POST" action="{{ route('manunuzi.store') }}" id="manunuzi-form" class="space-y-4">
                @csrf
                
                <!-- Bidhaa Selection -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Chagua Bidhaa *</label>
                    <div class="relative">
                        <input type="text" 
                               id="bidhaa-search-input"
                               placeholder="Tafuta bidhaa..." 
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                               autocomplete="off">
                        <div id="bidhaa-search-results" class="hidden absolute z-10 w-full bg-white border border-gray-300 rounded shadow-lg max-h-60 overflow-y-auto"></div>
                    </div>
                    <input type="hidden" name="bidhaa_id" id="bidhaa_id">
                    
                    <!-- Selected Product Info -->
                    <div id="selected-bidhaa-info" class="hidden mt-2 p-2 bg-gray-50 border border-gray-200 rounded">
                        <div class="flex justify-between items-center">
                            <div>
                                <div class="font-medium text-sm text-gray-900" id="selected-jina"></div>
                                <div class="text-xs text-gray-600" id="selected-info"></div>
                            </div>
                            <button type="button" onclick="clearBidhaaSelection()" class="text-red-500 hover:text-red-700 text-sm">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Form Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <!-- Idadi -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Idadi *</label>
                        <input type="number" name="idadi" id="idadi" min="0.01" step="0.01"
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                               placeholder="Idadi (mfano: 1.5, 2.75)" required oninput="calculateUnitCost()">
                        <p class="text-xs text-gray-500 mt-1">Unaweza kuweka desimali (mfano: 1.5, 2.75)</p>
                    </div>

                    <!-- Aina ya Bei - Start with Rejareja -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Aina ya Bei *</label>
                        <select name="bei_type" id="bei_type" 
                                class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500" 
                                onchange="handleBeiTypeChange()">
                            <option value="rejareja" selected>Rejareja (Bei per Kimoja)</option>
                            <option value="kwa_zote">Kwa Zote (Bei Jumla)</option>
                        </select>
                    </div>

                    <!-- Bei Nunua -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Bei Nunua (TZS) *</label>
                        <input type="number" step="0.01" name="bei_nunua" id="bei_nunua"
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                               placeholder="Bei kwa 1" required oninput="calculateUnitCost()">
                        <div class="text-xs mt-1">
                            <span id="unit-cost-display" class="font-medium text-emerald-600"></span>
                            <span id="price-instruction" class="text-gray-500 ml-2">
                                <i class="fas fa-info-circle"></i>
                                <span id="instruction-text">Ingiza bei ya 1 bidhaa</span>
                            </span>
                        </div>
                    </div>

                    <!-- Bei Uza -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Bei Uza (TZS) *</label>
                        <input type="number" step="0.01" name="bei_kuuza" id="bei_kuuza"
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                               placeholder="Bei ya kuuza kwa 1" required oninput="validatePrices()">
                    </div>

                    <!-- Expiry -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Tarehe ya Mwisho</label>
                        <input type="date" name="expiry" id="expiry"
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                               value="{{ now()->format('Y-m-d') }}">
                    </div>

                    <!-- Saplaya -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Saplaya</label>
                        <input type="text" name="saplaya" id="saplaya"
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                               placeholder="Jina la msaplaya">
                    </div>

                    <!-- Simu -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Simu</label>
                        <input type="text" name="simu" id="simu"
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                               placeholder="Namba ya simu">
                    </div>

                    <!-- Maelezo -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Maelezo</label>
                        <textarea name="mengineyo" id="mengineyo" rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                                  placeholder="Maelezo ya ziada..."></textarea>
                    </div>
                </div>

                <!-- Price Calculation Summary -->
                <div id="price-summary" class="bg-gray-50 p-3 rounded border border-gray-200 hidden">
                    <div class="text-xs font-medium text-gray-700 mb-1">Muhtasari wa Bei:</div>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div class="text-gray-600">Bei ya Kununua (kwa 1):</div>
                        <div class="text-right font-medium text-emerald-600" id="summary-unit-cost">0.00</div>
                        
                        <div class="text-gray-600">Jumla ya Kununua:</div>
                        <div class="text-right font-bold text-emerald-700" id="summary-total-cost">0.00</div>
                        
                        <div class="text-gray-600">Bei ya Kuuza (kwa 1):</div>
                        <div class="text-right font-medium text-green-600" id="summary-sell-price">0.00</div>
                        
                        <div class="text-gray-600">Faida (kwa 1):</div>
                        <div class="text-right font-bold" id="summary-profit">0.00</div>
                    </div>
                </div>

                <!-- Price Validation Error -->
                <div id="price-error" class="text-red-600 text-xs font-medium hidden"></div>

                <!-- Buttons -->
                <div class="flex gap-2 pt-4 border-t border-gray-200">
                    <button type="submit" 
                            class="flex-1 bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700 text-sm font-medium">
                        <i class="fas fa-save mr-1"></i> Hifadhi
                    </button>
                    <button type="button" onclick="clearManunuziForm()"
                            class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 text-sm">
                        <i class="fas fa-redo mr-1"></i> Safisha
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- TAB 3: Ripoti -->
    <div id="ripoti-tab-content" class="tab-content hidden">
        <div class="grid grid-cols-1 gap-4">
            <!-- Report Filters -->
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
                            <option value="by-product">Kwa Bidhaa</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button onclick="generateReport()" class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 text-sm font-medium">
                            <i class="fas fa-chart-bar mr-1"></i> Generate
                        </button>
                        <button onclick="exportReportPDF()" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm font-medium">
                            <i class="fas fa-file-pdf mr-1"></i> PDF
                        </button>
                        <button onclick="exportReportExcel()" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm font-medium">
                            <i class="fas fa-file-excel mr-1"></i> Excel
                        </button>
                    </div>
                </div>
            </div>

            <!-- Report Results -->
            <div id="report-results" class="hidden">
                <!-- Summary Cards -->
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

                <!-- Report Table -->
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm" id="report-table">
                            <thead>
                                <tr class="bg-emerald-50">
                                    <th class="px-4 py-2 text-left font-medium text-emerald-800">Tarehe</th>
                                    <th class="px-4 py-2 text-left font-medium text-emerald-800">Bidhaa</th>
                                    <th class="px-4 py-2 text-center font-medium text-emerald-800">Idadi</th>
                                    <th class="px-4 py-2 text-right font-medium text-emerald-800">Bei Nunua</th>
                                    <th class="px-4 py-2 text-right font-medium text-emerald-800">Bei Kuuza</th>
                                    <th class="px-4 py-2 text-left font-medium text-emerald-800">Saplaya</th>
                                </tr>
                            </thead>
                            <tbody id="report-tbody" class="divide-y divide-gray-100">
                                <!-- Will be filled by JavaScript -->
                            </tbody>
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
                <!-- Bidhaa Info (readonly) -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Bidhaa</label>
                    <div class="px-3 py-2 bg-gray-50 border border-gray-300 rounded text-sm text-gray-700">
                        <span id="edit-bidhaa-jina"></span> - 
                        <span id="edit-bidhaa-aina"></span>
                    </div>
                    <input type="hidden" name="bidhaa_id" id="edit-bidhaa_id">
                </div>

                <!-- Idadi -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Idadi *</label>
                    <input type="number" name="idadi" id="edit-idadi" min="0.01" step="0.01"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                           required oninput="calculateEditUnitCost()">
                </div>

                <!-- Aina ya Bei -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Aina ya Bei *</label>
                    <select name="bei_type" id="edit-bei-type" 
                            class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500" 
                            onchange="handleEditBeiTypeChange()">
                        <option value="rejareja">Rejareja (Bei per Kimoja)</option>
                        <option value="kwa_zote">Kwa Zote (Bei Jumla)</option>
                    </select>
                </div>

                <!-- Bei Nunua -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Bei Nunua (TZS) *</label>
                    <input type="number" step="0.01" name="bei_nunua" id="edit-bei-nunua"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                           required oninput="calculateEditUnitCost()">
                    <div class="text-xs mt-1">
                        <span id="edit-unit-cost-display" class="font-medium text-emerald-600"></span>
                        <span id="edit-price-instruction" class="text-gray-500 ml-2">
                            <i class="fas fa-info-circle"></i>
                            <span id="edit-instruction-text">Ingiza bei ya 1 bidhaa</span>
                        </span>
                    </div>
                </div>

                <!-- Bei Uza -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Bei Uza (TZS) *</label>
                    <input type="number" step="0.01" name="bei_kuuza" id="edit-bei-kuuza"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                           required oninput="validateEditPrices()">
                </div>

                <!-- Expiry -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tarehe ya Mwisho</label>
                    <input type="date" name="expiry" id="edit-expiry"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                </div>

                <!-- Saplaya -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Saplaya</label>
                    <input type="text" name="saplaya" id="edit-saplaya"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                </div>

                <!-- Simu -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Simu</label>
                    <input type="text" name="simu" id="edit-simu"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                </div>

                <!-- Maelezo -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Maelezo</label>
                    <textarea name="mengineyo" id="edit-mengineyo" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"></textarea>
                </div>
            </div>
            
            <!-- Edit Price Summary -->
            <div id="edit-price-summary" class="bg-gray-50 p-3 rounded border border-gray-200 mt-3 hidden">
                <div class="text-xs font-medium text-gray-700 mb-1">Muhtasari wa Bei:</div>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div class="text-gray-600">Bei ya Kununua (kwa 1):</div>
                    <div class="text-right font-medium text-emerald-600" id="edit-summary-unit-cost">0.00</div>
                    
                    <div class="text-gray-600">Jumla ya Kununua:</div>
                    <div class="text-right font-bold text-emerald-700" id="edit-summary-total-cost">0.00</div>
                    
                    <div class="text-gray-600">Bei ya Kuuza (kwa 1):</div>
                    <div class="text-right font-medium text-green-600" id="edit-summary-sell-price">0.00</div>
                    
                    <div class="text-gray-600">Faida (kwa 1):</div>
                    <div class="text-right font-bold" id="edit-summary-profit">0.00</div>
                </div>
            </div>
            
            <div class="flex gap-2 pt-4 border-t border-gray-200 mt-4">
                <button type="button" id="close-edit-modal"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 text-sm">
                    Ghairi
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 text-sm font-medium">
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
                <button id="cancel-delete"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 text-sm">
                    Ghairi
                </button>
                <form id="delete-form" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="w-full px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm font-medium">
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

/* Responsive table */
@media print {
    .no-print, .tab-button, .modal, .print\:hidden {
        display: none !important;
    }
    body {
        background: white;
    }
    .border, .shadow-sm {
        border: 1px solid #ddd !important;
        box-shadow: none !important;
    }
}
</style>
@endpush
@push('scripts')
<script>
// ============================================
// MANUNUZI MANAGER - COMPLETE FIX
// ============================================

class ManunuziManager {
    constructor() {
        this.currentTab = this.getSavedTab() || 'taarifa';
        this.searchTimeout = null;
        this.bidhaaData = [];
        this.allManunuziData = [];
        this.isSubmitting = false;
        
        // Initialize
        this.loadData();
        this.init();
    }

    loadData() {
        // Get data from blade
        try {
            this.bidhaaData = @json($bidhaa) || [];
            this.allManunuziData = @json($manunuzi->items()) || [];
        } catch(e) {
            console.warn('Error loading data:', e);
            this.bidhaaData = [];
            this.allManunuziData = [];
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
        } catch(e) {
            // Ignore
        }
    }

    init() {
        console.log('ManunuziManager initializing...');
        this.bindEvents();
        this.showTab(this.currentTab);
        this.setupAjaxForms();
        this.setupBidhaaSearch();
        this.setDefaultDates();
        this.setupDeleteButtons();
        this.setupEditButtons();
    }

    // ============================================
    // SET DEFAULT DATES
    // ============================================
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

    // ============================================
    // BIND EVENTS
    // ============================================
    bindEvents() {
        // Tab navigation - FIXED
        document.querySelectorAll('.tab-button').forEach(button => {
            button.removeEventListener('click', this.handleTabClick);
            button.addEventListener('click', this.handleTabClick.bind(this));
        });

        // Search with debounce
        const searchInput = document.getElementById('search-input');
        if (searchInput) {
            searchInput.removeEventListener('input', this.handleSearch);
            searchInput.addEventListener('input', this.handleSearch.bind(this));
        }

        // Print button
        const printBtn = document.querySelector('[onclick="printManunuzi()"]');
        if (printBtn) {
            printBtn.removeAttribute('onclick');
            printBtn.addEventListener('click', () => this.printManunuzi());
        }

        // PDF Export button
        const pdfBtn = document.querySelector('[onclick="exportPDF()"]');
        if (pdfBtn) {
            pdfBtn.removeAttribute('onclick');
            pdfBtn.addEventListener('click', () => this.exportPDF());
        }

        // Excel Export button
        const excelBtn = document.querySelector('[onclick="exportExcel()"]');
        if (excelBtn) {
            excelBtn.removeAttribute('onclick');
            excelBtn.addEventListener('click', () => this.exportExcel());
        }

        // Filter buttons
        const filterBtn = document.querySelector('[onclick="filterByDateRange()"]');
        if (filterBtn) {
            filterBtn.removeAttribute('onclick');
            filterBtn.addEventListener('click', () => this.filterByDateRange());
        }

        const clearFilterBtn = document.querySelector('[onclick="clearDateFilter()"]');
        if (clearFilterBtn) {
            clearFilterBtn.removeAttribute('onclick');
            clearFilterBtn.addEventListener('click', () => this.clearDateFilter());
        }

        // Report generation
        const generateReportBtn = document.querySelector('[onclick="generateReport()"]');
        if (generateReportBtn) {
            generateReportBtn.removeAttribute('onclick');
            generateReportBtn.addEventListener('click', () => this.generateReport());
        }

        // Report export buttons
        const reportPdfBtn = document.querySelector('[onclick="exportReportPDF()"]');
        if (reportPdfBtn) {
            reportPdfBtn.removeAttribute('onclick');
            reportPdfBtn.addEventListener('click', () => this.exportReportPDF());
        }

        const reportExcelBtn = document.querySelector('[onclick="exportReportExcel()"]');
        if (reportExcelBtn) {
            reportExcelBtn.removeAttribute('onclick');
            reportExcelBtn.addEventListener('click', () => this.exportReportExcel());
        }

        // Clear form button
        const clearFormBtn = document.querySelector('[onclick="clearManunuziForm()"]');
        if (clearFormBtn) {
            clearFormBtn.removeAttribute('onclick');
            clearFormBtn.addEventListener('click', () => this.clearManunuziForm());
        }

        // Modal close buttons
        const closeEditModal = document.getElementById('close-edit-modal');
        if (closeEditModal) {
            closeEditModal.removeEventListener('click', this.closeEditModal);
            closeEditModal.addEventListener('click', this.closeEditModal.bind(this));
        }

        const cancelDelete = document.getElementById('cancel-delete');
        if (cancelDelete) {
            cancelDelete.removeEventListener('click', this.closeDeleteModal);
            cancelDelete.addEventListener('click', this.closeDeleteModal.bind(this));
        }

        // Modal overlay clicks
        document.querySelectorAll('.modal').forEach(modal => {
            modal.removeEventListener('click', this.handleModalOverlay);
            modal.addEventListener('click', this.handleModalOverlay.bind(this));
        });

        // Price type changes
        const beiType = document.getElementById('bei_type');
        if (beiType) {
            beiType.removeEventListener('change', this.handleBeiTypeChange);
            beiType.addEventListener('change', this.handleBeiTypeChange.bind(this));
        }

        const editBeiType = document.getElementById('edit-bei-type');
        if (editBeiType) {
            editBeiType.removeEventListener('change', this.handleEditBeiTypeChange);
            editBeiType.addEventListener('change', this.handleEditBeiTypeChange.bind(this));
        }

        // Price inputs for real-time calculation
        ['idadi', 'bei_nunua', 'bei_kuuza'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.removeEventListener('input', this.handlePriceChange);
                el.addEventListener('input', this.handlePriceChange.bind(this));
            }
        });

        ['edit-idadi', 'edit-bei-nunua', 'edit-bei-kuuza'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.removeEventListener('input', this.handleEditPriceChange);
                el.addEventListener('input', this.handleEditPriceChange.bind(this));
            }
        });

        // Escape key
        document.removeEventListener('keydown', this.handleEscapeKey);
        document.addEventListener('keydown', this.handleEscapeKey.bind(this));

        console.log('Events bound successfully');
    }

    // ============================================
    // EVENT HANDLERS
    // ============================================
    handleTabClick(e) {
        const tab = e.target.closest('.tab-button').dataset.tab;
        this.showTab(tab);
        this.saveTab(tab);
    }

    handleSearch(e) {
        clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(() => {
            this.filterManunuzi(e.target.value.toLowerCase().trim());
        }, 300);
    }

    handleBeiTypeChange(e) {
        this.updatePlaceholderText();
        this.calculateUnitCost();
        this.updatePriceSummary();
        this.validatePrices();
    }

    handleEditBeiTypeChange(e) {
        this.updateEditPlaceholderText();
        this.calculateEditUnitCost();
        this.updateEditPriceSummary();
        this.validateEditPrices();
    }

    handlePriceChange(e) {
        this.calculateUnitCost();
        this.updatePriceSummary();
        this.validatePrices();
    }

    handleEditPriceChange(e) {
        this.calculateEditUnitCost();
        this.updateEditPriceSummary();
        this.validateEditPrices();
    }

    handleEscapeKey(e) {
        if (e.key === 'Escape') {
            document.getElementById('edit-modal')?.classList.add('hidden');
            document.getElementById('delete-modal')?.classList.add('hidden');
        }
    }

    handleModalOverlay(e) {
        if (e.target === e.currentTarget || e.target.classList.contains('modal-overlay')) {
            e.currentTarget.classList.add('hidden');
        }
    }

    closeEditModal() {
        document.getElementById('edit-modal')?.classList.add('hidden');
    }

    closeDeleteModal() {
        document.getElementById('delete-modal')?.classList.add('hidden');
    }

    // ============================================
    // TAB MANAGEMENT
    // ============================================
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
    // BIDHAA SEARCH
    // ============================================
    setupBidhaaSearch() {
        const searchInput = document.getElementById('bidhaa-search-input');
        const searchResults = document.getElementById('bidhaa-search-results');
        
        if (!searchInput || !searchResults) return;

        let searchTimeout = null;

        searchInput.removeEventListener('input', this.handleBidhaaSearch);
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

        searchInput.removeEventListener('focus', this.handleBidhaaFocus);
        searchInput.addEventListener('focus', () => {
            if (searchInput.value.length >= 2) {
                const query = searchInput.value.toLowerCase().trim();
                const filtered = this.bidhaaData.filter(b => 
                    (b.jina && b.jina.toLowerCase().includes(query)) || 
                    (b.aina && b.aina.toLowerCase().includes(query))
                );
                this.displaySearchResults(filtered);
            }
        });

        // Hide results when clicking outside
        document.removeEventListener('click', this.handleOutsideClick);
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
            const itemJson = JSON.stringify(item).replace(/'/g, "\\'").replace(/"/g, '&quot;');
            html += `
                <div class="p-3 border-b border-gray-100 hover:bg-emerald-50 cursor-pointer"
                     onclick="window.manunuziManager.selectBidhaa(${itemJson})">
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
        
        // Clear purchase price - user must enter it
        document.getElementById('bei_nunua').value = '';
        
        // Reset to rejareja
        document.getElementById('bei_type').value = 'rejareja';
        this.updatePlaceholderText();
        
        // Focus on quantity
        document.getElementById('idadi').focus();
        
        searchResults.classList.add('hidden');
    }

    // ============================================
    // HELPER FUNCTIONS
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

    updatePlaceholderText() {
        const beiType = document.getElementById('bei_type')?.value;
        const beiNunuaInput = document.getElementById('bei_nunua');
        const instructionText = document.getElementById('instruction-text');
        
        if (beiType === 'kwa_zote') {
            beiNunuaInput.placeholder = 'Bei ya zote pamoja';
            if (instructionText) instructionText.textContent = 'Ingiza bei jumla ya zote';
        } else {
            beiNunuaInput.placeholder = 'Bei kwa 1';
            if (instructionText) instructionText.textContent = 'Ingiza bei ya 1 bidhaa';
        }
    }

    updateEditPlaceholderText() {
        const beiType = document.getElementById('edit-bei-type')?.value;
        const beiNunuaInput = document.getElementById('edit-bei-nunua');
        const instructionText = document.getElementById('edit-instruction-text');
        
        if (beiType === 'kwa_zote') {
            beiNunuaInput.placeholder = 'Bei ya zote pamoja';
            if (instructionText) instructionText.textContent = 'Ingiza bei jumla ya zote';
        } else {
            beiNunuaInput.placeholder = 'Bei kwa 1';
            if (instructionText) instructionText.textContent = 'Ingiza bei ya 1 bidhaa';
        }
    }

    // ============================================
    // PRICE CALCULATIONS
    // ============================================
    calculateUnitCost() {
        const idadi = parseFloat(document.getElementById('idadi')?.value) || 0;
        const beiNunua = parseFloat(document.getElementById('bei_nunua')?.value) || 0;
        const beiType = document.getElementById('bei_type')?.value || 'rejareja';
        const display = document.getElementById('unit-cost-display');
        
        if (idadi <= 0 || beiNunua <= 0) {
            if (display) display.textContent = '';
            return { unitCost: 0, totalCost: 0 };
        }
        
        let unitCost = 0;
        let totalCost = 0;
        
        if (beiType === 'kwa_zote') {
            totalCost = beiNunua;
            unitCost = totalCost / idadi;
            if (display) {
                display.textContent = `Bei per kimoja: ${unitCost.toFixed(2)}`;
                display.style.color = '#059669';
            }
        } else {
            unitCost = beiNunua;
            totalCost = unitCost * idadi;
            if (display) {
                display.textContent = `Bei jumla: ${totalCost.toFixed(2)}`;
                display.style.color = '#2563eb';
            }
        }
        
        return { unitCost, totalCost };
    }

    calculateEditUnitCost() {
        const idadi = parseFloat(document.getElementById('edit-idadi')?.value) || 0;
        const beiNunua = parseFloat(document.getElementById('edit-bei-nunua')?.value) || 0;
        const beiType = document.getElementById('edit-bei-type')?.value || 'rejareja';
        const display = document.getElementById('edit-unit-cost-display');
        
        if (idadi <= 0 || beiNunua <= 0) {
            if (display) display.textContent = '';
            return { unitCost: 0, totalCost: 0 };
        }
        
        let unitCost = 0;
        let totalCost = 0;
        
        if (beiType === 'kwa_zote') {
            totalCost = beiNunua;
            unitCost = totalCost / idadi;
            if (display) {
                display.textContent = `Bei per kimoja: ${unitCost.toFixed(2)}`;
                display.style.color = '#059669';
            }
        } else {
            unitCost = beiNunua;
            totalCost = unitCost * idadi;
            if (display) {
                display.textContent = `Bei jumla: ${totalCost.toFixed(2)}`;
                display.style.color = '#2563eb';
            }
        }
        
        return { unitCost, totalCost };
    }

    // ============================================
    // PRICE VALIDATION
    // ============================================
    validatePrices() {
        const { unitCost } = this.calculateUnitCost();
        const beiKuuza = parseFloat(document.getElementById('bei_kuuza')?.value) || 0;
        const priceError = document.getElementById('price-error');
        const beiKuuzaInput = document.getElementById('bei_kuuza');
        
        if (unitCost > 0 && beiKuuza > 0 && beiKuuza < unitCost) {
            if (priceError) {
                priceError.textContent = '⚠️ Bei ya kuuza haiwezi kuwa chini ya bei ya kununua kwa kimoja!';
                priceError.classList.remove('hidden');
            }
            if (beiKuuzaInput) beiKuuzaInput.classList.add('border-red-500');
            return false;
        } else {
            if (priceError) priceError.classList.add('hidden');
            if (beiKuuzaInput) beiKuuzaInput.classList.remove('border-red-500');
            return true;
        }
    }

    validateEditPrices() {
        const { unitCost } = this.calculateEditUnitCost();
        const beiKuuza = parseFloat(document.getElementById('edit-bei-kuuza')?.value) || 0;
        const beiKuuzaInput = document.getElementById('edit-bei-kuuza');
        
        if (unitCost > 0 && beiKuuza > 0 && beiKuuza < unitCost) {
            if (beiKuuzaInput) beiKuuzaInput.classList.add('border-red-500');
            return false;
        } else {
            if (beiKuuzaInput) beiKuuzaInput.classList.remove('border-red-500');
            return true;
        }
    }

    // ============================================
    // PRICE SUMMARY
    // ============================================
    updatePriceSummary() {
        const { unitCost, totalCost } = this.calculateUnitCost();
        const beiKuuza = parseFloat(document.getElementById('bei_kuuza')?.value) || 0;
        const summaryDiv = document.getElementById('price-summary');
        
        if (unitCost > 0 || totalCost > 0 || beiKuuza > 0) {
            if (summaryDiv) summaryDiv.classList.remove('hidden');
            
            const el = (id) => document.getElementById(id);
            if (el('summary-unit-cost')) el('summary-unit-cost').textContent = unitCost.toFixed(2);
            if (el('summary-total-cost')) el('summary-total-cost').textContent = totalCost.toFixed(2);
            if (el('summary-sell-price')) el('summary-sell-price').textContent = beiKuuza.toFixed(2);
            
            const profitEl = el('summary-profit');
            if (profitEl) {
                if (beiKuuza > unitCost) {
                    profitEl.textContent = (beiKuuza - unitCost).toFixed(2);
                    profitEl.style.color = '#059669';
                } else {
                    profitEl.textContent = '0.00';
                    profitEl.style.color = '#dc2626';
                }
            }
        } else {
            if (summaryDiv) summaryDiv.classList.add('hidden');
        }
    }

    updateEditPriceSummary() {
        const { unitCost, totalCost } = this.calculateEditUnitCost();
        const beiKuuza = parseFloat(document.getElementById('edit-bei-kuuza')?.value) || 0;
        const summaryDiv = document.getElementById('edit-price-summary');
        
        if (unitCost > 0 || totalCost > 0 || beiKuuza > 0) {
            if (summaryDiv) summaryDiv.classList.remove('hidden');
            
            const el = (id) => document.getElementById(id);
            if (el('edit-summary-unit-cost')) el('edit-summary-unit-cost').textContent = unitCost.toFixed(2);
            if (el('edit-summary-total-cost')) el('edit-summary-total-cost').textContent = totalCost.toFixed(2);
            if (el('edit-summary-sell-price')) el('edit-summary-sell-price').textContent = beiKuuza.toFixed(2);
            
            const profitEl = el('edit-summary-profit');
            if (profitEl) {
                if (beiKuuza > unitCost) {
                    profitEl.textContent = (beiKuuza - unitCost).toFixed(2);
                    profitEl.style.color = '#059669';
                } else {
                    profitEl.textContent = '0.00';
                    profitEl.style.color = '#dc2626';
                }
            }
        } else {
            if (summaryDiv) summaryDiv.classList.add('hidden');
        }
    }

    // ============================================
    // FILTER MANUNUZI
    // ============================================
    filterManunuzi(searchTerm) {
        const rows = document.querySelectorAll('.manunuzi-row');
        let found = false;
        
        rows.forEach(row => {
            let manunuzi;
            try {
                manunuzi = JSON.parse(row.dataset.manunuzi);
            } catch(e) {
                return;
            }
            
            const searchText = `
                ${manunuzi.bidhaa?.jina || ''}
                ${manunuzi.bidhaa?.aina || ''}
                ${manunuzi.saplaya || ''}
                ${manunuzi.simu || ''}
            `.toLowerCase();
            
            if (searchText.includes(searchTerm) || !searchTerm) {
                row.classList.remove('hidden');
                found = true;
            } else {
                row.classList.add('hidden');
            }
        });

        if (!found && searchTerm) {
            this.showNotification('Hakuna manunuzi zinazolingana', 'info');
        }
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
        
        const rows = document.querySelectorAll('.manunuzi-row');
        let visibleCount = 0;
        let totalCost = 0;
        
        rows.forEach(row => {
            let manunuzi;
            try {
                manunuzi = JSON.parse(row.dataset.manunuzi);
            } catch(e) {
                return;
            }
            
            const rowDate = new Date(manunuzi.created_at);
            
            if (rowDate >= start && rowDate <= end) {
                row.classList.remove('hidden');
                visibleCount++;
                totalCost += parseFloat(manunuzi.bei || 0);
            } else {
                row.classList.add('hidden');
            }
        });
        
        const dateRangeInfo = document.getElementById('date-range-info');
        const dateRangeText = document.getElementById('date-range-text');
        const tableSummary = document.getElementById('table-summary');
        const displayedTotal = document.getElementById('displayed-total');
        
        if (visibleCount > 0) {
            if (dateRangeInfo) dateRangeInfo.classList.remove('hidden');
            if (dateRangeText) {
                dateRangeText.textContent = `${new Date(startDate).toLocaleDateString()} - ${new Date(endDate).toLocaleDateString()} (${visibleCount} manunuzi)`;
            }
            if (tableSummary) tableSummary.classList.remove('hidden');
            if (displayedTotal) {
                displayedTotal.textContent = totalCost.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
            }
        } else {
            if (dateRangeInfo) dateRangeInfo.classList.add('hidden');
            if (tableSummary) tableSummary.classList.add('hidden');
            this.showNotification('Hakuna manunuzi katika kipindi hiki', 'info');
        }
        
        const pagination = document.getElementById('pagination-container');
        if (pagination) pagination.classList.add('hidden');
    }

    clearDateFilter() {
        const rows = document.querySelectorAll('.manunuzi-row');
        rows.forEach(row => row.classList.remove('hidden'));
        
        const dateRangeInfo = document.getElementById('date-range-info');
        const tableSummary = document.getElementById('table-summary');
        
        if (dateRangeInfo) dateRangeInfo.classList.add('hidden');
        if (tableSummary) tableSummary.classList.add('hidden');
        
        const startDate = document.getElementById('start-date');
        const endDate = document.getElementById('end-date');
        
        if (startDate) {
            startDate.value = new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0];
        }
        if (endDate) {
            endDate.value = new Date().toISOString().split('T')[0];
        }
        
        const pagination = document.getElementById('pagination-container');
        if (pagination) pagination.classList.remove('hidden');
    }

    // ============================================
    // EDIT & DELETE
    // ============================================
    setupEditButtons() {
        document.querySelectorAll('.edit-manunuzi-btn').forEach(button => {
            button.removeEventListener('click', this.handleEdit);
            button.addEventListener('click', this.handleEdit.bind(this));
        });
    }

    setupDeleteButtons() {
        document.querySelectorAll('.delete-manunuzi-btn').forEach(button => {
            button.removeEventListener('click', this.handleDelete);
            button.addEventListener('click', this.handleDelete.bind(this));
        });
    }

    handleEdit(e) {
        const button = e.currentTarget;
        const row = button.closest('.manunuzi-row');
        if (!row) return;
        
        let manunuzi;
        try {
            manunuzi = JSON.parse(row.dataset.manunuzi);
        } catch(e) {
            this.showNotification('Hitilafu katika kupakia data', 'error');
            return;
        }
        
        this.editManunuzi(manunuzi);
    }

    handleDelete(e) {
        const button = e.currentTarget;
        const manunuziId = button.dataset.id;
        const productName = button.dataset.name || 'Bidhaa';
        this.deleteManunuzi(manunuziId, productName);
    }

    editManunuzi(manunuzi) {
        const editForm = document.getElementById('edit-form');
        if (!editForm) return;

        // Calculate if it was "kwa zote" or "rejareja"
        const wasKwaZote = Math.abs(manunuzi.bei - (manunuzi.unit_cost * manunuzi.idadi)) > 0.01;
        
        const setVal = (id, val) => {
            const el = document.getElementById(id);
            if (el) el.value = val !== null && val !== undefined ? val : '';
        };
        
        const setText = (id, val) => {
            const el = document.getElementById(id);
            if (el) el.textContent = val || '';
        };
        
        setText('edit-bidhaa-jina', manunuzi.bidhaa?.jina);
        setText('edit-bidhaa-aina', manunuzi.bidhaa?.aina);
        setVal('edit-bidhaa_id', manunuzi.bidhaa_id);
        setVal('edit-idadi', manunuzi.idadi);
        setVal('edit-bei-type', wasKwaZote ? 'kwa_zote' : 'rejareja');
        setVal('edit-bei-nunua', wasKwaZote ? manunuzi.bei : manunuzi.unit_cost);
        setVal('edit-bei-kuuza', manunuzi.bidhaa?.bei_kuuza);
        setVal('edit-expiry', manunuzi.expiry ? manunuzi.expiry.split('T')[0] : '');
        setVal('edit-saplaya', manunuzi.saplaya);
        setVal('edit-simu', manunuzi.simu);
        setVal('edit-mengineyo', manunuzi.mengineyo);
        
        editForm.action = `/manunuzi/${manunuzi.id}`;
        
        this.updateEditPlaceholderText();
        this.calculateEditUnitCost();
        this.updateEditPriceSummary();
        
        const editModal = document.getElementById('edit-modal');
        if (editModal) editModal.classList.remove('hidden');
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
    // AJAX FORMS
    // ============================================
    setupAjaxForms() {
        // Main form
        const manunuziForm = document.getElementById('manunuzi-form');
        if (manunuziForm) {
            manunuziForm.removeEventListener('submit', this.handleFormSubmit);
            manunuziForm.addEventListener('submit', this.handleFormSubmit.bind(this));
        }

        // Edit form
        const editForm = document.getElementById('edit-form');
        if (editForm) {
            editForm.removeEventListener('submit', this.handleEditSubmit);
            editForm.addEventListener('submit', this.handleEditSubmit.bind(this));
        }

        // Delete form
        const deleteForm = document.getElementById('delete-form');
        if (deleteForm) {
            deleteForm.removeEventListener('submit', this.handleDeleteSubmit);
            deleteForm.addEventListener('submit', this.handleDeleteSubmit.bind(this));
        }
    }

    async handleFormSubmit(e) {
        e.preventDefault();
        if (this.isSubmitting) return;
        
        // Validate prices before submit
        if (!this.validatePrices()) {
            this.showNotification('⚠️ Bei ya kuuza haiwezi kuwa chini ya bei ya kununua kwa kimoja!', 'error');
            return;
        }
        
        await this.submitForm(e.currentTarget, 'Manunuzi yamehifadhiwa!');
        this.clearManunuziForm();
    }

    async handleEditSubmit(e) {
        e.preventDefault();
        if (this.isSubmitting) return;
        
        if (!this.validateEditPrices()) {
            this.showNotification('⚠️ Bei ya kuuza haiwezi kuwa chini ya bei ya kununua kwa kimoja!', 'error');
            return;
        }
        
        await this.submitForm(e.currentTarget, 'Manunuzi yamebadilishwa!');
        document.getElementById('edit-modal')?.classList.add('hidden');
    }

    async handleDeleteSubmit(e) {
        e.preventDefault();
        if (this.isSubmitting) return;
        await this.submitForm(e.currentTarget, 'Manunuzi yamefutwa!');
        document.getElementById('delete-modal')?.classList.add('hidden');
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
    // CLEAR FORM
    // ============================================
    clearManunuziForm() {
        const form = document.getElementById('manunuzi-form');
        if (form) form.reset();
        
        const searchInput = document.getElementById('bidhaa-search-input');
        const bidhaaIdInput = document.getElementById('bidhaa_id');
        const selectedInfoDiv = document.getElementById('selected-bidhaa-info');
        
        if (searchInput) searchInput.value = '';
        if (bidhaaIdInput) bidhaaIdInput.value = '';
        if (selectedInfoDiv) selectedInfoDiv.classList.add('hidden');
        
        const beiType = document.getElementById('bei_type');
        if (beiType) beiType.value = 'rejareja';
        this.updatePlaceholderText();
        
        const display = document.getElementById('unit-cost-display');
        if (display) display.textContent = '';
        
        const summaryDiv = document.getElementById('price-summary');
        if (summaryDiv) summaryDiv.classList.add('hidden');
        
        const priceError = document.getElementById('price-error');
        if (priceError) priceError.classList.add('hidden');
        
        const beiKuuzaInput = document.getElementById('bei_kuuza');
        if (beiKuuzaInput) beiKuuzaInput.classList.remove('border-red-500');
        
        const today = new Date().toISOString().split('T')[0];
        const expiryInput = document.getElementById('expiry');
        if (expiryInput) expiryInput.value = today;
        
        if (searchInput) {
            setTimeout(() => searchInput.focus(), 100);
        }
    }

    // ============================================
    // NOTIFICATIONS
    // ============================================
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

    // ============================================
    // PRINT & EXPORT
    // ============================================
    printManunuzi() {
        const rows = document.querySelectorAll('.manunuzi-row:not(.hidden)');
        
        if (rows.length === 0) {
            this.showNotification('Hakuna manunuzi ya kuchapisha', 'warning');
            return;
        }
        
        let tableRows = '';
        rows.forEach(row => {
            let manunuzi;
            try {
                manunuzi = JSON.parse(row.dataset.manunuzi);
            } catch(e) { return; }
            
            const formattedIdadi = parseFloat(manunuzi.idadi || 0).toFixed(2);
            tableRows += `
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px;">${manunuzi.created_at ? new Date(manunuzi.created_at).toLocaleDateString() : ''}</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">${this.escapeHtml(manunuzi.bidhaa?.jina || '')}</td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">${formattedIdadi}</td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">${parseFloat(manunuzi.bei || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">${parseFloat(manunuzi.bidhaa?.bei_kuuza || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">${this.escapeHtml(manunuzi.saplaya || '--')}</td>
                </tr>`;
        });
        
        const dateRangeInfo = document.getElementById('date-range-info');
        const dateRangeText = dateRangeInfo?.classList.contains('hidden') ? '' : 
            `<p>Kipindi: ${document.getElementById('date-range-text')?.textContent || ''}</p>`;
        
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
                </style>
            </head>
            <body>
                <div class="header">
                    <h2>Orodha ya Manunuzi</h2>
                    <p>${new Date().toLocaleDateString()}</p>
                    ${dateRangeText}
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
    // REPORTS
    // ============================================
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
        
        // Filter data
        const filteredData = this.allManunuziData.filter(item => {
            const itemDate = new Date(item.created_at);
            return itemDate >= start && itemDate <= end;
        });
        
        if (filteredData.length === 0) {
            this.showNotification('Hakuna data katika kipindi hiki', 'info');
            return;
        }
        
        // Calculate totals
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
            // Detailed view
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

    exportReportPDF() {
        const startDate = document.getElementById('report-start-date')?.value;
        const endDate = document.getElementById('report-end-date')?.value;
        const reportType = document.getElementById('report-type')?.value;
        
        if (!startDate || !endDate) {
            this.showNotification('Tafadhali chagua tarehe', 'warning');
            return;
        }
        
        const url = `${window.location.pathname}?export=pdf&start_date=${startDate}&end_date=${endDate}&report_type=${reportType}`;
        window.open(url, '_blank');
    }

    exportReportExcel() {
        const startDate = document.getElementById('report-start-date')?.value;
        const endDate = document.getElementById('report-end-date')?.value;
        const reportType = document.getElementById('report-type')?.value;
        
        if (!startDate || !endDate) {
            this.showNotification('Tafadhali chagua tarehe', 'warning');
            return;
        }
        
        const url = `${window.location.pathname}?export=excel&start_date=${startDate}&end_date=${endDate}&report_type=${reportType}`;
        window.location.href = url;
    }
}

// ============================================
// INITIALIZATION
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing ManunuziManager...');
    
    // Clean up any existing instances
    if (window.manunuziManager) {
        console.log('Cleaning up existing instance...');
    }
    
    // Create new instance
    window.manunuziManager = new ManunuziManager();
    console.log('ManunuziManager initialized!');
});

// ============================================
// GLOBAL FUNCTIONS (for inline onclick compatibility)
// ============================================
window.printManunuzi = function() {
    if (window.manunuziManager) window.manunuziManager.printManunuzi();
};

window.exportPDF = function() {
    if (window.manunuziManager) window.manunuziManager.exportPDF();
};

window.exportExcel = function() {
    if (window.manunuziManager) window.manunuziManager.exportExcel();
};

window.filterByDateRange = function() {
    if (window.manunuziManager) window.manunuziManager.filterByDateRange();
};

window.clearDateFilter = function() {
    if (window.manunuziManager) window.manunuziManager.clearDateFilter();
};

window.generateReport = function() {
    if (window.manunuziManager) window.manunuziManager.generateReport();
};

window.exportReportPDF = function() {
    if (window.manunuziManager) window.manunuziManager.exportReportPDF();
};

window.exportReportExcel = function() {
    if (window.manunuziManager) window.manunuziManager.exportReportExcel();
};

window.clearManunuziForm = function() {
    if (window.manunuziManager) window.manunuziManager.clearManunuziForm();
};

window.handleBeiTypeChange = function() {
    if (window.manunuziManager) {
        window.manunuziManager.updatePlaceholderText();
        window.manunuziManager.calculateUnitCost();
        window.manunuziManager.updatePriceSummary();
        window.manunuziManager.validatePrices();
    }
};

window.handleEditBeiTypeChange = function() {
    if (window.manunuziManager) {
        window.manunuziManager.updateEditPlaceholderText();
        window.manunuziManager.calculateEditUnitCost();
        window.manunuziManager.updateEditPriceSummary();
        window.manunuziManager.validateEditPrices();
    }
};

window.calculateUnitCost = function() {
    if (window.manunuziManager) window.manunuziManager.calculateUnitCost();
};

window.calculateEditUnitCost = function() {
    if (window.manunuziManager) window.manunuziManager.calculateEditUnitCost();
};

window.validatePrices = function() {
    if (window.manunuziManager) return window.manunuziManager.validatePrices();
    return true;
};

window.validateEditPrices = function() {
    if (window.manunuziManager) return window.manunuziManager.validateEditPrices();
    return true;
};

window.selectBidhaa = function(item) {
    if (window.manunuziManager) window.manunuziManager.selectBidhaa(item);
};

// For backward compatibility with inline onclick
window.selectBidhaaFromJson = function(item) {
    if (window.manunuziManager) window.manunuziManager.selectBidhaa(item);
};
</script>
@endpush