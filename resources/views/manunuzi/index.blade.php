@extends('layouts.app')

@section('title', 'Manunuzi')

@section('page-title', 'Manunuzi')
@section('page-subtitle', now()->format('d/m/Y'))

@section('content')
@php
    $canEditDelete = $canEditDelete ?? false;
    $draftList = collect($drafts ?? [])->map(function ($items, $token) {
        $first = $items->first();
        return [
            'draft_token' => $token,
            'saplaya'     => $first->saplaya,
            'simu'        => $first->simu,
            'mengineyo'   => $first->mengineyo,
            'item_count'  => $items->count(),
            'updated_at'  => optional($first->draft_updated_at)->format('d/m/Y H:i') ?? '-',
        ];
    })->values();
@endphp
<div class="space-y-4" id="app-container" data-current-page="{{ request()->get('page', 1) }}" data-can-edit="{{ $canEditDelete ? '1' : '0' }}">

    <!-- Hidden data -->
    <div id="bidhaa-data" style="display:none;">{{ json_encode($bidhaa->map(function($b) { return ['id' => $b->id, 'jina' => $b->jina, 'aina' => $b->aina, 'kipimo' => $b->kipimo, 'idadi' => $b->idadi, 'bei_nunua' => $b->bei_nunua, 'bei_kuuza' => $b->bei_kuuza, 'bei_uzo_jumla' => $b->bei_uzo_jumla, 'has_image' => $b->hasImage()]; })) }}</div>
    <div id="all-manunuzi-data" style="display:none;">{{ json_encode($allManunuzi->map(function($m) { return ['id' => $m->id, 'risiti_number' => $m->risiti_number, 'bidhaa_id' => $m->bidhaa_id, 'idadi' => $m->idadi, 'bei' => $m->bei, 'unit_cost' => $m->unit_cost, 'expiry' => $m->expiry, 'saplaya' => $m->saplaya, 'simu' => $m->simu, 'mengineyo' => $m->mengineyo, 'created_at' => $m->created_at, 'created_at_formatted' => $m->created_at ? $m->created_at->format('d/m/Y') : '', 'created_time' => $m->created_at ? $m->created_at->format('H:i') : '', 'bidhaa' => $m->bidhaa ? ['id' => $m->bidhaa->id, 'jina' => $m->bidhaa->jina, 'aina' => $m->bidhaa->aina, 'kipimo' => $m->bidhaa->kipimo, 'bei_kuuza' => $m->bidhaa->bei_kuuza] : null]; })) }}</div>

    <div id="notification-container" class="fixed top-4 left-1/2 transform -translate-x-1/2 z-[2000] w-full max-w-sm px-4 pointer-events-none"></div>

    <!-- STATS -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="bg-white p-3 rounded-lg border border-emerald-200 shadow-sm"><div class="flex justify-between items-center"><div><p class="text-xs text-gray-500 mb-1">Manunuzi Ya Leo</p><p class="text-xl font-bold text-emerald-700">{{ $todayPurchases ?? 0 }}</p></div><i class="fas fa-shopping-cart text-emerald-500 text-lg"></i></div></div>
        <div class="bg-white p-3 rounded-lg border border-blue-200 shadow-sm"><div class="flex justify-between items-center"><div><p class="text-xs text-gray-500 mb-1">Bidhaa Zilizonunuliwa</p><p class="text-xl font-bold text-blue-700">{{ number_format($totalItemsPurchased ?? 0, 2) }}</p></div><i class="fas fa-boxes text-blue-500 text-lg"></i></div></div>
        <div class="bg-white p-3 rounded-lg border border-purple-200 shadow-sm"><div class="flex justify-between items-center"><div><p class="text-xs text-gray-500 mb-1">Jumla ya Gharama</p><p class="text-xl font-bold text-purple-700">{{ number_format($totalCost ?? 0, 2) }}</p></div><i class="fas fa-money-bill-wave text-purple-500 text-lg"></i></div></div>
        <div class="bg-white p-3 rounded-lg border border-amber-200 shadow-sm"><div class="flex justify-between items-center"><div><p class="text-xs text-gray-500 mb-1">Gharama Ya Leo</p><p class="text-xl font-bold text-amber-700">{{ number_format($todayCost ?? 0, 2) }}</p></div><i class="fas fa-calendar-day text-amber-500 text-lg"></i></div></div>
    </div>

    <!-- TABS (4 Tabs) -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm">
        <div class="flex flex-wrap">
            <button data-tab="taarifa" class="tab-button flex-1 py-3 px-2 md:px-4 text-sm font-medium border-r border-b md:border-b-0 border-gray-200 bg-emerald-50 text-emerald-700 whitespace-nowrap"><i class="fas fa-table mr-2"></i> Orodha</button>
            <button data-tab="ingiza" class="tab-button flex-1 py-3 px-2 md:px-4 text-sm font-medium border-r border-b md:border-b-0 border-gray-200 text-gray-600 hover:bg-gray-50 whitespace-nowrap"><i class="fas fa-plus mr-2"></i> Ingiza</button>
            <button data-tab="batch" class="tab-button flex-1 py-3 px-2 md:px-4 text-sm font-medium border-r border-b md:border-b-0 border-gray-200 text-gray-600 hover:bg-gray-50 whitespace-nowrap"><i class="fas fa-file-invoice mr-2"></i> Risiti (Batch) <span id="draft-count-badge" class="ml-1 hidden px-1.5 py-0.5 rounded-full bg-amber-500 text-white text-xs"></span></button>
            <button data-tab="ripoti" class="tab-button flex-1 py-3 px-2 md:px-4 text-sm font-medium text-gray-600 hover:bg-gray-50 whitespace-nowrap"><i class="fas fa-chart-bar mr-2"></i> Ripoti</button>
        </div>
    </div>

    <!-- TAB 1: Orodha -->
    <div id="taarifa-tab-content" class="tab-content space-y-3">
        <div class="bg-white p-3 rounded-lg border border-gray-200 shadow-sm">
            <div class="flex flex-col sm:flex-row gap-2">
                <div class="flex-1 relative">
                    <input type="text" id="search-input" placeholder="Tafuta bidhaa, saplaya, simu..." class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded text-sm" value="{{ request()->search }}" autocomplete="off">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <button id="clear-search" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 hidden"><i class="fas fa-times"></i></button>
                </div>
                <div class="flex gap-2">
                    <button onclick="window.manunuziManager?.printManunuzi()" class="px-3 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 text-sm font-medium"><i class="fas fa-print mr-1"></i> Print</button>
                    <button onclick="window.manunuziManager?.exportPDF()" class="px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm font-medium"><i class="fas fa-file-pdf mr-1"></i> PDF</button>
                    <button onclick="window.manunuziManager?.exportExcel()" class="px-3 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm font-medium"><i class="fas fa-file-excel mr-1"></i> Excel</button>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-2 mt-3 items-end">
                <div class="sm:col-span-1"><label class="block text-xs font-medium text-gray-700 mb-1">Kuanzia Tarehe</label><input type="date" id="start-date" class="w-full px-3 py-2 border border-gray-300 rounded text-sm"></div>
                <div class="sm:col-span-1"><label class="block text-xs font-medium text-gray-700 mb-1">Mpaka Tarehe</label><input type="date" id="end-date" class="w-full px-3 py-2 border border-gray-300 rounded text-sm"></div>
                <div class="sm:col-span-2 flex gap-2">
                    <button onclick="window.manunuziManager?.filterByDateRange()" class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 text-sm font-medium"><i class="fas fa-filter mr-1"></i> Chuja</button>
                    <button onclick="window.manunuziManager?.clearDateFilter()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 text-sm"><i class="fas fa-times mr-1"></i> Ondoa</button>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="bg-emerald-50"><th class="px-4 py-2 text-left font-medium text-emerald-800">Risiti</th><th class="px-4 py-2 text-left font-medium text-emerald-800">Tarehe</th><th class="px-4 py-2 text-left font-medium text-emerald-800 hidden sm:table-cell">Bidhaa</th><th class="px-4 py-2 text-center font-medium text-emerald-800">Idadi</th><th class="px-4 py-2 text-right font-medium text-emerald-800">Bei Nunua</th><th class="px-4 py-2 text-right font-medium text-emerald-800">Bei Uza</th><th class="px-4 py-2 text-left font-medium text-emerald-800 hidden lg:table-cell">Saplaya</th>@if($canEditDelete)<th class="px-4 py-2 text-center font-medium text-emerald-800 print:hidden">Vitendo</th>@endif</tr></thead>
                    <tbody id="manunuzi-tbody" class="divide-y divide-gray-100"></tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-t border-gray-200 flex justify-between items-center">
                <span class="manunuzi-pagination-info text-sm text-gray-600">Inaonyesha <span id="visible-count">{{ $manunuzi->count() }}</span> kati ya <span id="total-count">{{ $manunuzi->total() }}</span> manunuzi</span>
                <div id="pagination-links"></div>
            </div>
        </div>
    </div>

    <!-- TAB 2: Ingiza (Single) -->
    <div id="ingiza-tab-content" class="tab-content hidden">
        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
            <form method="POST" action="{{ route('manunuzi.store') }}" id="manunuzi-form" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Chagua Bidhaa *</label>
                    <div class="relative">
                        <input type="text" id="bidhaa-search-input" placeholder="Tafuta bidhaa..." class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded text-sm" autocomplete="off">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <div id="bidhaa-search-results" class="hidden absolute z-10 w-full bg-white border border-gray-300 rounded shadow-lg max-h-60 overflow-y-auto"></div>
                    </div>
                    <input type="hidden" name="bidhaa_id" id="bidhaa_id">
                    <div id="selected-bidhaa-info" class="hidden mt-2 p-2 bg-gray-50 border border-gray-200 rounded"><div class="flex justify-between items-center"><div><div class="font-medium text-sm text-gray-900" id="selected-jina"></div><div class="text-xs text-gray-600" id="selected-info"></div></div><button type="button" onclick="window.manunuziManager?.clearBidhaaSelection()" class="text-red-500 hover:text-red-700 text-sm"><i class="fas fa-times"></i></button></div></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-700 mb-1">Idadi *</label><input type="number" name="idadi" id="idadi" min="0.01" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded text-sm" placeholder="Idadi" required></div>
                    <div><label class="block text-xs font-medium text-gray-700 mb-1">Aina ya Bei *</label><select name="bei_type" id="bei_type" class="w-full px-3 py-2 border border-gray-300 rounded text-sm"><option value="rejareja">Rejareja (Bei per Kimoja)</option><option value="kwa_zote">Kwa Zote (Bei Jumla)</option></select></div>
                    <div><label class="block text-xs font-medium text-gray-700 mb-1">Bei Nunua (TZS) *</label><input type="number" step="0.01" name="bei_nunua" id="bei_nunua" class="w-full px-3 py-2 border border-gray-300 rounded text-sm" placeholder="Bei kwa 1" required></div>
                    <div><label class="block text-xs font-medium text-gray-700 mb-1">Bei Uza (TZS) *</label><input type="number" step="0.01" name="bei_kuuza" id="bei_kuuza" class="w-full px-3 py-2 border border-gray-300 rounded text-sm" placeholder="Bei ya kuuza kwa 1" required></div>
                    <div><label class="block text-xs font-medium text-gray-700 mb-1">Tarehe ya Mwisho</label><input type="date" name="expiry" id="expiry" class="w-full px-3 py-2 border border-gray-300 rounded text-sm" value="{{ now()->format('Y-m-d') }}"></div>
                    <div><label class="block text-xs font-medium text-gray-700 mb-1">Saplaya</label><input type="text" name="saplaya" id="saplaya" class="w-full px-3 py-2 border border-gray-300 rounded text-sm" placeholder="Jina la msaplaya"></div>
                    <div><label class="block text-xs font-medium text-gray-700 mb-1">Simu</label><input type="text" name="simu" id="simu" class="w-full px-3 py-2 border border-gray-300 rounded text-sm" placeholder="Namba ya simu"></div>
                    <div class="md:col-span-2"><label class="block text-xs font-medium text-gray-700 mb-1">Maelezo</label><textarea name="mengineyo" id="mengineyo" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded text-sm" placeholder="Maelezo ya ziada..."></textarea></div>
                </div>
                <div id="price-error" class="text-red-600 text-xs font-medium hidden"></div>
                <div class="flex gap-2 pt-4 border-t border-gray-200">
                    <button type="submit" class="flex-1 bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700 text-sm font-medium"><i class="fas fa-save mr-1"></i> Hifadhi</button>
                    <button type="reset" class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 text-sm"><i class="fas fa-redo mr-1"></i> Safisha</button>
                </div>
            </form>
        </div>
    </div>

    <!-- TAB 3: Risiti (Batch) -->
    <div id="batch-tab-content" class="tab-content hidden">
        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
            <div class="flex flex-wrap justify-between items-center gap-2 mb-4 border-b pb-2">
                <h3 class="font-bold text-gray-800"><i class="fas fa-file-invoice text-emerald-600 mr-2"></i> Ingiza Risiti ya Msaplaya (Batch)</h3>
                <button type="button" onclick="newBatchReceipt()" class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 text-xs font-medium"><i class="fas fa-file mr-1"></i> Risiti Mpya</button>
            </div>

            <!-- DRAFTS PANEL -->
            <div id="drafts-panel" class="hidden mb-4 p-3 bg-amber-50 border border-amber-200 rounded">
                <div class="flex items-center mb-2 text-sm font-bold text-amber-800"><i class="fas fa-clock mr-2"></i> Drafts Zako (Endelea baadaye)</div>
                <div id="drafts-list" class="space-y-2"></div>
            </div>

            <!-- Current draft indicator -->
            <div id="current-draft-banner" class="hidden mb-3 p-2 bg-amber-100 border border-amber-300 rounded text-xs text-amber-900 flex justify-between items-center">
                <span><i class="fas fa-pen mr-1"></i> Unahariri draft: <strong id="current-draft-token"></strong></span>
                <button type="button" onclick="newBatchReceipt()" class="underline">Acha</button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4 bg-gray-50 p-3 rounded">
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Jina la Msaplaya</label><input type="text" id="batch-saplaya" class="w-full px-3 py-2 border rounded text-sm" placeholder="Mfano: Azam Ltd"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Simu ya Msaplaya</label><input type="text" id="batch-simu" class="w-full px-3 py-2 border rounded text-sm" placeholder="07XXXXXXXX"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Maelezo</label><input type="text" id="batch-mengineyo" class="w-full px-3 py-2 border rounded text-sm" placeholder="Maelezo..."></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-6 gap-2 mb-2 items-end bg-blue-50 p-3 rounded border border-blue-100">
                <div class="md:col-span-2 relative">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tafuta Bidhaa</label>
                    <div class="relative">
                        <input type="text" id="batch-search-input" placeholder="Andika jina la bidhaa..." class="w-full pl-10 pr-3 py-2 border border-blue-300 rounded text-sm focus:ring-1 focus:ring-blue-500" autocomplete="off">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-blue-400"></i>
                        <div id="batch-search-results" class="hidden absolute z-50 w-full bg-white border border-gray-300 rounded shadow-lg max-h-60 overflow-y-auto"></div>
                    </div>
                    <input type="hidden" id="batch-bidhaa_id">
                </div>

                <div><label class="block text-xs font-medium text-gray-700 mb-1">Idadi</label><input type="number" id="batch-idadi" step="0.01" min="0.01" class="w-full px-3 py-2 border rounded text-sm" placeholder="0"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Bei Nunua (Jumla)</label><input type="number" id="batch-bei_nunua" step="0.01" min="0" class="w-full px-3 py-2 border rounded text-sm" placeholder="TZS"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Bei Uza (Kimoja)</label><input type="number" id="batch-bei_kuuza" step="0.01" min="0" class="w-full px-3 py-2 border rounded text-sm" placeholder="TZS"></div>

                <div class="flex justify-end">
                    <button type="button" onclick="addBatchItem()" class="w-full bg-gray-800 text-white py-2 rounded font-bold text-sm hover:bg-gray-900"><i class="fas fa-plus-circle mr-1"></i> Ongeza</button>
                </div>
            </div>

            <!-- Selected / NEW product indicator + add-new button -->
            <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
                <div id="batch-selected-info" class="hidden text-xs px-2 py-1 rounded bg-emerald-50 border border-emerald-200 text-emerald-800"></div>
                <button type="button" onclick="openNewProductModal()" class="ml-auto px-3 py-1.5 bg-blue-600 text-white rounded hover:bg-blue-700 text-xs font-medium"><i class="fas fa-box-open mr-1"></i> Bidhaa haipo? Ongeza Mpya</button>
            </div>

            <div id="batch-calc-display" class="hidden mb-3 p-2 bg-emerald-50 rounded border border-emerald-200 text-sm flex justify-between items-center">
                <span class="text-gray-700">Bei ya Kununua kwa Kimoja: <strong id="calc-unit-cost" class="text-emerald-700">0.00</strong></span>
                <span class="text-gray-700">Faida kwa Kimoja: <strong id="calc-faida" class="text-blue-700">0.00</strong></span>
                <span id="calc-warning" class="text-red-600 font-bold hidden"><i class="fas fa-exclamation-triangle"></i> Bei ya kuuza ni chini ya bei ya kununua!</span>
            </div>

            <div class="overflow-x-auto border rounded mb-4">
                <table class="w-full text-sm">
                    <thead class="bg-emerald-50 text-emerald-800"><tr><th class="px-3 py-2 text-left">Bidhaa</th><th class="px-3 py-2 text-center">Idadi</th><th class="px-3 py-2 text-right">Bei Nunua</th><th class="px-3 py-2 text-right">Bei Uza</th><th class="px-3 py-2 text-center">Futa</th></tr></thead>
                    <tbody id="batch-cart-body" class="divide-y divide-gray-100"><tr><td colspan="5" class="text-center py-4 text-gray-500">Hakuna bidhaa kwenye risiti hii</td></tr></tbody>
                    <tfoot class="bg-gray-100 font-bold"><tr><td class="px-3 py-2 text-right" colspan="2">JUMLA:</td><td class="px-3 py-2 text-right text-emerald-700" id="batch-total-cost">0.00</td><td colspan="2"></td></tr></tfoot>
                </table>
            </div>

            <div class="flex flex-col sm:flex-row gap-2">
                <button type="button" onclick="saveBatchDraft()" id="batch-draft-btn" class="sm:w-1/3 bg-amber-500 text-white py-3 rounded font-bold hover:bg-amber-600"><i class="fas fa-bookmark mr-2"></i> Hifadhi Draft</button>
                <button type="button" onclick="submitBatch()" id="batch-submit-btn" class="flex-1 bg-emerald-600 text-white py-3 rounded font-bold text-lg hover:bg-emerald-700"><i class="fas fa-save mr-2"></i> Hifadhi Risiti & Ongeza Stock</button>
            </div>
        </div>
    </div>

    <!-- TAB 4: Ripoti -->
    <div id="ripoti-tab-content" class="tab-content hidden">
        <div class="grid grid-cols-1 gap-4">
            <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                <h3 class="text-sm font-medium text-gray-900 mb-3 flex items-center"><i class="fas fa-filter text-emerald-600 mr-2"></i> Chuja Ripoti</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div><label class="block text-xs font-medium text-gray-700 mb-1">Kuanzia Tarehe</label><input type="date" id="report-start-date" class="w-full px-3 py-2 border border-gray-300 rounded text-sm" value="{{ now()->startOfMonth()->format('Y-m-d') }}"></div>
                    <div><label class="block text-xs font-medium text-gray-700 mb-1">Mpaka Tarehe</label><input type="date" id="report-end-date" class="w-full px-3 py-2 border border-gray-300 rounded text-sm" value="{{ now()->format('Y-m-d') }}"></div>
                    <div><label class="block text-xs font-medium text-gray-700 mb-1">Aina ya Ripoti</label><select id="report-type" class="w-full px-3 py-2 border border-gray-300 rounded text-sm"><option value="summary">Muhtasari</option><option value="detailed">Kina</option><option value="by-supplier">Kwa Saplaya</option></select></div>
                    <div class="flex items-end gap-2"><button onclick="window.manunuziManager?.generateReport()" class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 text-sm font-medium"><i class="fas fa-chart-bar mr-1"></i> Generate</button></div>
                </div>
            </div>
            <div id="report-results" class="hidden">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-4">
                    <div class="bg-emerald-50 p-3 rounded-lg border border-emerald-200"><p class="text-xs text-gray-600 mb-1">Jumla ya Manunuzi</p><p class="text-xl font-bold text-emerald-700" id="report-total-count">0</p></div>
                    <div class="bg-blue-50 p-3 rounded-lg border border-blue-200"><p class="text-xs text-gray-600 mb-1">Jumla ya Bidhaa</p><p class="text-xl font-bold text-blue-700" id="report-total-items">0.00</p></div>
                    <div class="bg-purple-50 p-3 rounded-lg border border-purple-200"><p class="text-xs text-gray-600 mb-1">Jumla ya Gharama</p><p class="text-xl font-bold text-purple-700" id="report-total-cost">0.00</p></div>
                    <div class="bg-amber-50 p-3 rounded-lg border border-amber-200"><p class="text-xs text-gray-600 mb-1">Wastani wa Bei</p><p class="text-xl font-bold text-amber-700" id="report-avg-cost">0.00</p></div>
                </div>
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm" id="report-table">
                            <thead><tr class="bg-emerald-50"><th class="px-4 py-2 text-left font-medium text-emerald-800">Tarehe</th><th class="px-4 py-2 text-left font-medium text-emerald-800">Bidhaa/Saplaya</th><th class="px-4 py-2 text-center font-medium text-emerald-800">Idadi</th><th class="px-4 py-2 text-right font-medium text-emerald-800">Gharama</th><th class="px-4 py-2 text-right font-medium text-emerald-800">Bei Kuuza</th><th class="px-4 py-2 text-left font-medium text-emerald-800">Maelezo</th></tr></thead>
                            <tbody id="report-tbody" class="divide-y divide-gray-100"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
<div id="edit-modal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-lg shadow-lg w-full max-w-md mx-auto z-50 max-h-[90vh] overflow-y-auto">
        <div class="p-4 border-b border-gray-200"><h3 class="text-sm font-semibold text-gray-800">Rekebisha Manunuzi</h3></div>
        <form id="edit-form" method="POST" class="p-4">
            @csrf @method('PUT')
            <div class="space-y-3">
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Bidhaa</label><div class="px-3 py-2 bg-gray-50 border border-gray-300 rounded text-sm text-gray-700"><span id="edit-bidhaa-jina"></span> - <span id="edit-bidhaa-aina"></span></div><input type="hidden" name="bidhaa_id" id="edit-bidhaa_id"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Idadi *</label><input type="number" name="idadi" id="edit-idadi" min="0.01" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded text-sm" required></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Aina ya Bei *</label><select name="bei_type" id="edit-bei-type" class="w-full px-3 py-2 border border-gray-300 rounded text-sm"><option value="rejareja">Rejareja</option><option value="kwa_zote">Kwa Zote</option></select></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Bei Nunua (TZS) *</label><input type="number" step="0.01" name="bei_nunua" id="edit-bei-nunua" class="w-full px-3 py-2 border border-gray-300 rounded text-sm" required></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Bei Uza (TZS) *</label><input type="number" step="0.01" name="bei_kuuza" id="edit-bei-kuuza" class="w-full px-3 py-2 border border-gray-300 rounded text-sm" required></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Tarehe ya Mwisho</label><input type="date" name="expiry" id="edit-expiry" class="w-full px-3 py-2 border border-gray-300 rounded text-sm"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Saplaya</label><input type="text" name="saplaya" id="edit-saplaya" class="w-full px-3 py-2 border border-gray-300 rounded text-sm"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Simu</label><input type="text" name="simu" id="edit-simu" class="w-full px-3 py-2 border border-gray-300 rounded text-sm"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Maelezo</label><textarea name="mengineyo" id="edit-mengineyo" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded text-sm"></textarea></div>
            </div>
            <div class="flex gap-2 pt-4 border-t border-gray-200 mt-4">
                <button type="button" id="close-edit-modal" class="flex-1 px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 text-sm">Ghairi</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 text-sm font-medium">Hifadhi</button>
            </div>
        </form>
    </div>
</div>

<div id="delete-modal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-lg shadow-lg w-full max-w-sm mx-auto z-50">
        <div class="p-4 border-b border-gray-200"><h3 class="text-sm font-semibold text-gray-800">Thibitisha Kufuta</h3></div>
        <div class="p-4">
            <div class="text-center mb-4">
                <i class="fas fa-exclamation-triangle text-amber-500 text-2xl mb-2"></i>
                <p class="text-gray-700 text-sm mb-1">Una uhakika unataka kufuta manunuzi ya?</p>
                <p class="text-gray-900 font-medium" id="delete-product-name"></p>
                <p class="text-gray-500 text-xs mt-2">Hatua hii haiwezi kutenduliwa</p>
            </div>
            <div class="flex gap-2">
                <button id="cancel-delete" class="flex-1 px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 text-sm">Ghairi</button>
                <form id="delete-form" method="POST" class="flex-1">@csrf @method('DELETE')<button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm font-medium">Futa</button></form>
            </div>
        </div>
    </div>
</div>

<!-- NEW PRODUCT MODAL (for batch receipt) -->
<div id="new-product-modal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-lg shadow-lg w-full max-w-md mx-auto z-50 max-h-[90vh] overflow-y-auto">
        <div class="p-4 border-b border-gray-200"><h3 class="text-sm font-semibold text-gray-800"><i class="fas fa-box-open text-blue-600 mr-2"></i> Bidhaa Mpya</h3><p class="text-xs text-gray-500 mt-1">Itaundwa kwenye stock pale tu risiti itakapohifadhiwa.</p></div>
        <div class="p-4 space-y-3">
            <div><label class="block text-xs font-medium text-gray-700 mb-1">Jina la Bidhaa *</label><input type="text" id="np-jina" class="w-full px-3 py-2 border border-gray-300 rounded text-sm"></div>
            <div><label class="block text-xs font-medium text-gray-700 mb-1">Aina *</label><input type="text" id="np-aina" class="w-full px-3 py-2 border border-gray-300 rounded text-sm" placeholder="Mfano: Vinywaji"></div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Kipimo</label><input type="text" id="np-kipimo" class="w-full px-3 py-2 border border-gray-300 rounded text-sm" placeholder="500ml, 2kg"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Barcode</label><input type="text" id="np-barcode" class="w-full px-3 py-2 border border-gray-300 rounded text-sm"></div>
            </div>
            <div><label class="block text-xs font-medium text-gray-700 mb-1">Tarehe ya Mwisho (Expiry)</label><input type="date" id="np-expiry" class="w-full px-3 py-2 border border-gray-300 rounded text-sm"></div>
            <div id="np-error" class="text-red-600 text-xs font-medium hidden"></div>
        </div>
        <div class="flex gap-2 p-4 border-t border-gray-200">
            <button type="button" onclick="closeNewProductModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 text-sm">Ghairi</button>
            <button type="button" onclick="confirmNewProduct()" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm font-medium">Tumia Bidhaa Hii</button>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.modal { display: flex; align-items: center; justify-content: center; }
.modal.hidden { display: none; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
.animate-fade-in { animation: fadeIn 0.3s ease-out; }
#bidhaa-search-results, #batch-search-results { z-index: 1000; }
.search-highlight { background-color: #fef08a; padding: 0 2px; border-radius: 2px; }
#search-input { pointer-events: auto !important; opacity: 1 !important; background-color: white !important; color: black !important; border: 1px solid #d1d5db !important; z-index: 10 !important; }
#search-input:focus { border-color: #10b981 !important; box-shadow: 0 0 0 1px #10b981 !important; outline: none !important; }
#pagination-links nav { display: inline-flex; }
#pagination-links .pagination { display: flex; flex-wrap: wrap; gap: 4px; margin: 0; padding: 0; }
#pagination-links .page-item { display: inline-block; margin: 0; }
#pagination-links .page-link { display: flex; align-items: center; justify-content: center; min-width: 32px; height: 32px; padding: 0 8px; font-size: 13px; font-weight: 500; color: #4b5563; background-color: white; border: 1px solid #e5e7eb; border-radius: 6px; transition: all 0.2s; }
#pagination-links .page-link:hover { background-color: #f3f4f6; border-color: #d1d5db; color: #1f2937; }
#pagination-links .active .page-link { background-color: #10b981; border-color: #10b981; color: white; }
#pagination-links .disabled .page-link { background-color: #f9fafb; border-color: #e5e7eb; color: #9ca3af; cursor: not-allowed; }
@media (max-width: 640px) { #pagination-links .pagination { flex-wrap: wrap; justify-content: center; } #pagination-links .page-link { min-width: 28px; height: 28px; font-size: 11px; padding: 0 6px; } }
</style>
@endpush

@push('scripts')
<script>
(function() {
    'use strict';
    class ManunuziManager {
        constructor() {
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
            this.loadData();
            this.init();
        }

        loadData() {
            try {
                const bidhaaElement = document.getElementById('bidhaa-data');
                this.bidhaaData = bidhaaElement ? JSON.parse(bidhaaElement.textContent) : [];
                const allManunuziElement = document.getElementById('all-manunuzi-data');
                if (allManunuziElement) {
                    this.allManunuziData = JSON.parse(allManunuziElement.textContent);
                    this.totalManunuziCount = this.allManunuziData.length;
                    this.filteredData = [...this.allManunuziData];
                } else { this.allManunuziData = []; this.filteredData = []; }
            } catch(e) { console.warn('Error loading data:', e); }
        }

        getSavedTab() { try { return sessionStorage.getItem('manunuzi_tab') || 'taarifa'; } catch(e) { return 'taarifa'; } }
        saveTab(tab) { try { sessionStorage.setItem('manunuzi_tab', tab); } catch(e) {} }

        init() {
            this.setupTabs(); this.setupSearch(); this.setupBidhaaSearch(); this.setupForms();
            this.setupActionButtons(); this.setupModals(); this.setupDateFilters(); this.setDefaultDates();
            this.showTab(this.currentTab); this.renderTable(); this.updatePaginationInfo();
        }

        setupTabs() {
            document.querySelectorAll('.tab-button').forEach(button => {
                button.addEventListener('click', (e) => { this.showTab(button.dataset.tab); this.saveTab(button.dataset.tab); });
            });
        }

        showTab(tabName) {
            document.querySelectorAll('.tab-button').forEach(button => {
                if (button.dataset.tab === tabName) { button.classList.add('bg-emerald-50', 'text-emerald-700'); button.classList.remove('text-gray-600', 'hover:bg-gray-50'); }
                else { button.classList.remove('bg-emerald-50', 'text-emerald-700'); button.classList.add('text-gray-600', 'hover:bg-gray-50'); }
            });
            document.querySelectorAll('.tab-content').forEach(content => content.classList.add('hidden'));
            const tabContent = document.getElementById(`${tabName}-tab-content`);
            if (tabContent) tabContent.classList.remove('hidden');
            this.currentTab = tabName;
        }

        setupSearch() {
            const searchInput = document.getElementById('search-input');
            const clearSearchBtn = document.getElementById('clear-search');
            if (!searchInput) return;
            const initialSearch = searchInput.value;
            if (initialSearch) { this.currentSearchTerm = initialSearch.toLowerCase().trim(); this.filterAllManunuzi(this.currentSearchTerm); if (clearSearchBtn) clearSearchBtn.classList.remove('hidden'); }
            searchInput.addEventListener('input', (e) => {
                clearTimeout(this.searchTimeout);
                const searchTerm = e.target.value.trim();
                this.currentSearchTerm = searchTerm;
                if (clearSearchBtn) clearSearchBtn.classList.toggle('hidden', !searchTerm);
                this.searchTimeout = setTimeout(() => {
                    if (searchTerm.length >= 2) { this.isSearchActive = true; this.filterAllManunuzi(searchTerm.toLowerCase()); }
                    else if (searchTerm.length === 0) { this.isSearchActive = false; this.clearSearch(); }
                }, 400);
            });
            if (clearSearchBtn) clearSearchBtn.addEventListener('click', () => this.clearSearch());
        }

        clearSearch() {
            const searchInput = document.getElementById('search-input');
            const clearSearchBtn = document.getElementById('clear-search');
            const searchStatus = document.getElementById('search-status');
            if (searchInput) { searchInput.value = ''; this.currentSearchTerm = ''; }
            if (clearSearchBtn) clearSearchBtn.classList.add('hidden');
            this.isSearchActive = false;
            this.filteredData = [...this.allManunuziData];
            this.currentPage = 1;
            this.renderTable();
            this.updatePaginationInfo();
            if (searchStatus) searchStatus.classList.add('hidden');
        }

        filterAllManunuzi(searchTerm) {
            this.currentPage = 1;
            if (!searchTerm) { this.filteredData = [...this.allManunuziData]; }
            else {
                this.filteredData = this.allManunuziData.filter(item => {
                    const searchText = `${item.risiti_number || ''} ${item.bidhaa?.jina || ''} ${item.bidhaa?.aina || ''} ${item.saplaya || ''} ${item.simu || ''} ${item.mengineyo || ''}`.toLowerCase();
                    return searchText.includes(searchTerm);
                });
            }
            this.renderTable();
            this.updatePaginationInfo();
            this.updateSearchStatus();
            if (this.filteredData.length === 0 && searchTerm) this.showNotification('Hakuna manunuzi zinazolingana na "' + searchTerm + '"', 'info');
        }

        renderTable() {
            const tbody = document.getElementById('manunuzi-tbody');
            if (!tbody) return;
            const canEdit = document.getElementById('app-container')?.dataset.canEdit === '1';
            const start = (this.currentPage - 1) * this.perPage;
            const end = start + this.perPage;
            const pageData = this.filteredData.slice(start, end);

            if (pageData.length === 0) {
                const colSpan = canEdit ? 8 : 7;
                tbody.innerHTML = `<tr><td colspan="${colSpan}" class="px-4 py-8 text-center text-gray-500"><i class="fas fa-shopping-cart text-3xl mb-2 text-gray-300"></i><p>Hakuna manunuzi yanayolingana</p></td></tr>`;
                return;
            }

            let html = '';
            pageData.forEach(item => {
                const formattedIdadi = parseFloat(item.idadi || 0);
                const displayIdadi = formattedIdadi % 1 === 0 ? Math.floor(formattedIdadi) : formattedIdadi.toFixed(2);
                const risitiUrl = '/manunuzi/' + item.id + '/risiti';

                html += `
                    <tr class="manunuzi-row hover:bg-gray-50" data-manunuzi='${JSON.stringify(item).replace(/'/g, "&#39;")}'>
                        <td class="px-4 py-2"><a href="${risitiUrl}" class="text-xs font-mono font-bold text-emerald-600 hover:underline">${this.highlightText(item.risiti_number || '--', this.currentSearchTerm)}</a></td>
                        <td class="px-4 py-2"><div class="text-xs text-gray-900">${item.created_at_formatted || ''}</div><div class="text-xs text-gray-500">${item.created_time || ''}</div></td>
                        <td class="px-4 py-2 hidden sm:table-cell"><div class="font-medium text-gray-900 text-sm">${this.highlightText(item.bidhaa?.jina || '--', this.currentSearchTerm)}</div><div class="text-xs text-gray-500">${this.highlightText(item.bidhaa?.aina || '--', this.currentSearchTerm)}</div></td>
                        <td class="px-4 py-2 text-center"><span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-emerald-100 text-emerald-800">${displayIdadi}</span></td>
                        <td class="px-4 py-2 text-right"><div class="text-sm font-bold text-emerald-700">${parseFloat(item.bei || 0).toFixed(2)}</div><div class="text-xs text-gray-500">@ ${parseFloat(item.unit_cost || 0).toFixed(2)}</div></td>
                        <td class="px-4 py-2 text-right"><div class="text-sm font-bold text-green-700">${parseFloat(item.bidhaa?.bei_kuuza || 0).toFixed(2)}</div></td>
                        <td class="px-4 py-2 hidden lg:table-cell"><div class="text-xs text-gray-700">${this.highlightText(item.saplaya || '--', this.currentSearchTerm)}</div>${item.simu ? `<div class="text-xs text-gray-400">${this.highlightText(item.simu, this.currentSearchTerm)}</div>` : ''}</td>
                        ${canEdit ? `<td class="px-4 py-2 text-center print:hidden"><div class="flex justify-center space-x-2">
                            <a href="${risitiUrl}" class="text-blue-600 hover:text-blue-800" title="Risiti"><i class="fas fa-receipt"></i></a>
                            <button class="edit-manunuzi-btn text-emerald-600 hover:text-emerald-800" data-id="${item.id}" title="Badili"><i class="fas fa-edit"></i></button>
                            <button class="delete-manunuzi-btn text-red-600 hover:text-red-800" data-id="${item.id}" data-name="${item.bidhaa?.jina || 'Bidhaa'}" title="Futa"><i class="fas fa-trash"></i></button>
                        </div></td>` : ''}
                    </tr>`;
            });
            tbody.innerHTML = html;
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
                searchResultCount.innerHTML = `<div class="bg-emerald-50 p-2 rounded"><span class="font-bold text-emerald-700">${this.filteredData.length}</span> <span class="text-gray-600">manunuzi zinaonyeshwa kati ya ${this.allManunuziData.length}</span><button onclick="window.manunuziManager.clearSearch()" class="ml-2 text-xs bg-emerald-600 text-white px-2 py-1 rounded hover:bg-emerald-700"><i class="fas fa-times mr-1"></i> Ondoa</button></div>`;
            } else { searchStatus.classList.add('hidden'); }
        }

        updatePaginationInfo() {
            const visibleCount = this.filteredData.length;
            const totalCount = this.allManunuziData.length;
            const visibleCountSpan = document.getElementById('visible-count');
            const totalCountSpan = document.getElementById('total-count');
            if (visibleCountSpan) visibleCountSpan.textContent = visibleCount;
            if (totalCountSpan) totalCountSpan.textContent = totalCount;
            this.updatePaginationLinks();
        }

        updatePaginationLinks() {
            const paginationContainer = document.getElementById('pagination-links');
            if (!paginationContainer) return;
            const totalPages = Math.ceil(this.filteredData.length / this.perPage);
            if (totalPages <= 1) { paginationContainer.innerHTML = ''; return; }
            let html = '<nav class="flex items-center space-x-1">';
            html += `<button onclick="window.manunuziManager.goToPage(${this.currentPage - 1})" class="px-3 py-1 rounded text-sm ${this.currentPage <= 1 ? 'text-gray-300 cursor-not-allowed' : 'text-gray-700 hover:bg-gray-100'}" ${this.currentPage <= 1 ? 'disabled' : ''}>&laquo;</button>`;
            const maxVisiblePages = 5;
            let startPage = Math.max(1, this.currentPage - Math.floor(maxVisiblePages / 2));
            let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
            if (endPage - startPage < maxVisiblePages - 1) startPage = Math.max(1, endPage - maxVisiblePages + 1);
            if (startPage > 1) { html += `<button onclick="window.manunuziManager.goToPage(1)" class="px-3 py-1 rounded text-sm text-gray-700 hover:bg-gray-100">1</button>`; if (startPage > 2) html += `<span class="px-2 text-gray-400">...</span>`; }
            for (let i = startPage; i <= endPage; i++) html += `<button onclick="window.manunuziManager.goToPage(${i})" class="px-3 py-1 rounded text-sm ${i === this.currentPage ? 'bg-emerald-600 text-white' : 'text-gray-700 hover:bg-gray-100'}">${i}</button>`;
            if (endPage < totalPages) { if (endPage < totalPages - 1) html += `<span class="px-2 text-gray-400">...</span>`; html += `<button onclick="window.manunuziManager.goToPage(${totalPages})" class="px-3 py-1 rounded text-sm text-gray-700 hover:bg-gray-100">${totalPages}</button>`; }
            html += `<button onclick="window.manunuziManager.goToPage(${this.currentPage + 1})" class="px-3 py-1 rounded text-sm ${this.currentPage >= totalPages ? 'text-gray-300 cursor-not-allowed' : 'text-gray-700 hover:bg-gray-100'}" ${this.currentPage >= totalPages ? 'disabled' : ''}>&raquo;</button></nav>`;
            paginationContainer.innerHTML = html;
        }

        goToPage(page) {
            const totalPages = Math.ceil(this.filteredData.length / this.perPage);
            if (page < 1 || page > totalPages) return;
            this.currentPage = page; this.renderTable(); this.updatePaginationLinks();
            document.querySelector('.bg-white.rounded-lg.border.border-gray-200.shadow-sm.overflow-hidden')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        setupBidhaaSearch() {
            const searchInput = document.getElementById('bidhaa-search-input');
            const searchResults = document.getElementById('bidhaa-search-results');
            if (!searchInput || !searchResults) return;
            let searchTimeout = null;
            searchInput.addEventListener('input', (e) => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    const query = e.target.value.toLowerCase().trim();
                    if (query.length < 2) { searchResults.classList.add('hidden'); return; }
                    const filtered = this.bidhaaData.filter(b => (b.jina && b.jina.toLowerCase().includes(query)) || (b.aina && b.aina.toLowerCase().includes(query)));
                    this.displaySearchResults(filtered);
                }, 300);
            });
            document.addEventListener('click', (e) => { if (!e.target.closest('#bidhaa-search-input') && !e.target.closest('#bidhaa-search-results')) searchResults.classList.add('hidden'); });
        }

        displaySearchResults(bidhaa) {
            const searchResults = document.getElementById('bidhaa-search-results');
            if (!bidhaa || bidhaa.length === 0) { searchResults.innerHTML = '<div class="p-3 text-sm text-gray-500">Hakuna bidhaa zinazolingana</div>'; searchResults.classList.remove('hidden'); return; }
            let html = '';
            bidhaa.forEach(item => {
                html += `<div class="p-3 border-b border-gray-100 hover:bg-emerald-50 cursor-pointer" onclick="window.manunuziManager.selectBidhaa(${JSON.stringify(item).replace(/"/g, '&quot;')})">
                    <div class="font-medium text-sm text-gray-900">${this.escapeHtml(item.jina)}</div>
                    <div class="text-xs text-gray-600">${this.escapeHtml(item.aina)} - ${this.escapeHtml(item.kipimo)}</div>
                    <div class="text-xs text-emerald-600 mt-1">Bei: ${parseFloat(item.bei_nunua || 0).toLocaleString()}</div></div>`;
            });
            searchResults.innerHTML = html; searchResults.classList.remove('hidden');
        }

        selectBidhaa(item) {
            document.getElementById('bidhaa_id').value = item.id;
            document.getElementById('bidhaa-search-input').value = item.jina;
            document.getElementById('selected-jina').textContent = item.jina || '';
            document.getElementById('selected-info').textContent = `${item.aina || ''} - ${item.kipimo || ''}`;
            document.getElementById('selected-bidhaa-info').classList.remove('hidden');
            if (document.getElementById('bei_kuuza') && item.bei_kuuza) document.getElementById('bei_kuuza').value = item.bei_kuuza;
            document.getElementById('bei_nunua').value = '';
            document.getElementById('bei_type').value = 'rejareja';
            document.getElementById('idadi').focus();
            document.getElementById('bidhaa-search-results').classList.add('hidden');
        }

        clearBidhaaSelection() {
            document.getElementById('bidhaa-search-input').value = '';
            document.getElementById('bidhaa_id').value = '';
            document.getElementById('selected-bidhaa-info').classList.add('hidden');
            document.getElementById('bei_nunua').value = '';
            document.getElementById('idadi').focus();
        }

        setupForms() {
            const manunuziForm = document.getElementById('manunuzi-form');
            if (manunuziForm) manunuziForm.addEventListener('submit', (e) => { e.preventDefault(); this.submitForm(e.currentTarget, 'Manunuzi yamehifadhiwa!'); });
            const editForm = document.getElementById('edit-form');
            if (editForm) editForm.addEventListener('submit', (e) => { e.preventDefault(); this.submitForm(e.currentTarget, 'Manunuzi yamebadilishwa!'); document.getElementById('edit-modal').classList.add('hidden'); });
            const deleteForm = document.getElementById('delete-form');
            if (deleteForm) deleteForm.addEventListener('submit', (e) => { e.preventDefault(); this.submitForm(e.currentTarget, 'Manunuzi yamefutwa!'); document.getElementById('delete-modal').classList.add('hidden'); });
        }

        async submitForm(form, successMessage = 'Operesheni imekamilika!') {
            if (this.isSubmitting) { this.showNotification('Tafadhali subiri...', 'warning'); return; }
            this.isSubmitting = true;
            const formData = new FormData(form);
            const submitButton = form.querySelector('button[type="submit"]');
            let originalText = '';
            if (submitButton) { originalText = submitButton.innerHTML; submitButton.disabled = true; submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Inatumwa...'; }
            try {
                const response = await fetch(form.action, { method: form.method || 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '' } });
                const data = await response.json();
                if (response.ok && data.success) {
                    this.showNotification(data.message || successMessage, 'success');
                    if(data.redirect) {
                        setTimeout(() => window.location.href = data.redirect, 1000);
                    } else {
                        setTimeout(() => window.location.reload(), 1500);
                    }
                }
                else { const error = data.errors ? Object.values(data.errors)[0][0] : data.message; this.showNotification(error || 'Hitilafu imetokea', 'error'); }
            } catch (error) { this.showNotification('Hitilafu ya mtandao. Tafadhali jaribu tena.', 'error'); }
            finally { this.isSubmitting = false; if (submitButton) { submitButton.disabled = false; submitButton.innerHTML = originalText; } }
        }

        setupActionButtons() {
            document.querySelectorAll('.edit-manunuzi-btn').forEach(button => {
                const newButton = button.cloneNode(true); button.parentNode.replaceChild(newButton, button);
                newButton.addEventListener('click', (e) => {
                    const row = newButton.closest('.manunuzi-row'); if (!row) return;
                    try { this.editManunuzi(JSON.parse(row.dataset.manunuzi)); } catch(e) { this.showNotification('Hitilafu katika kupakia data', 'error'); }
                });
            });
            document.querySelectorAll('.delete-manunuzi-btn').forEach(button => {
                const newButton = button.cloneNode(true); button.parentNode.replaceChild(newButton, button);
                newButton.addEventListener('click', (e) => { this.deleteManunuzi(newButton.dataset.id, newButton.dataset.name || 'Bidhaa'); });
            });
        }

        editManunuzi(manunuzi) {
            const editForm = document.getElementById('edit-form'); if (!editForm) return;
            const wasKwaZote = Math.abs(manunuzi.bei - (manunuzi.unit_cost * manunuzi.idadi)) > 0.01;
            const setVal = (id, val) => { const el = document.getElementById(id); if (el) el.value = val !== null && val !== undefined ? val : ''; };
            const setText = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val || ''; };
            setText('edit-bidhaa-jina', manunuzi.bidhaa?.jina || ''); setText('edit-bidhaa-aina', manunuzi.bidhaa?.aina || '');
            setVal('edit-bidhaa_id', manunuzi.bidhaa_id); setVal('edit-idadi', manunuzi.idadi);
            setVal('edit-bei-type', wasKwaZote ? 'kwa_zote' : 'rejareja'); setVal('edit-bei-nunua', wasKwaZote ? manunuzi.bei : manunuzi.unit_cost);
            setVal('edit-bei-kuuza', manunuzi.bidhaa?.bei_kuuza || ''); setVal('edit-expiry', manunuzi.expiry ? manunuzi.expiry.split('T')[0] : '');
            setVal('edit-saplaya', manunuzi.saplaya || ''); setVal('edit-simu', manunuzi.simu || ''); setVal('edit-mengineyo', manunuzi.mengineyo || '');
            editForm.action = `/manunuzi/${manunuzi.id}`; document.getElementById('edit-modal').classList.remove('hidden');
        }

        deleteManunuzi(manunuziId, productName) {
            const deleteForm = document.getElementById('delete-form'); const deleteModal = document.getElementById('delete-modal'); const deleteProductName = document.getElementById('delete-product-name');
            if (!deleteForm || !deleteModal || !deleteProductName) return;
            deleteProductName.textContent = productName || 'Bidhaa';
            deleteForm.action = `/manunuzi/${manunuziId}`; deleteModal.classList.remove('hidden');
        }

        setupModals() {
            document.getElementById('close-edit-modal')?.addEventListener('click', () => document.getElementById('edit-modal').classList.add('hidden'));
            document.getElementById('cancel-delete')?.addEventListener('click', () => document.getElementById('delete-modal').classList.add('hidden'));
            document.querySelectorAll('.modal').forEach(modal => {
                modal.addEventListener('click', (e) => { if (e.target === modal || e.target.classList.contains('modal-overlay')) modal.classList.add('hidden'); });
            });
            document.addEventListener('keydown', (e) => { if (e.key === 'Escape') { document.getElementById('edit-modal')?.classList.add('hidden'); document.getElementById('delete-modal')?.classList.add('hidden'); document.getElementById('new-product-modal')?.classList.add('hidden'); } });
        }

        setupDateFilters() { this.setDefaultDates(); }

        setDefaultDates() {
            const today = new Date().toISOString().split('T')[0];
            const startOfMonth = new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0];
            const startDate = document.getElementById('start-date'); const endDate = document.getElementById('end-date'); const expiryInput = document.getElementById('expiry');
            if (startDate && !startDate.value) startDate.value = startOfMonth;
            if (endDate && !endDate.value) endDate.value = today;
            if (expiryInput && !expiryInput.value) expiryInput.value = today;
        }

        filterByDateRange() {
            const startDate = document.getElementById('start-date')?.value; const endDate = document.getElementById('end-date')?.value;
            if (!startDate || !endDate) { this.showNotification('Tafadhali chagua tarehe zote mbili', 'warning'); return; }
            const start = new Date(startDate); start.setHours(0, 0, 0, 0);
            const end = new Date(endDate); end.setHours(23, 59, 59, 999);
            this.filteredData = this.allManunuziData.filter(item => { const itemDate = new Date(item.created_at); return itemDate >= start && itemDate <= end; });
            if (this.currentSearchTerm) this.filteredData = this.filteredData.filter(item => `${item.risiti_number || ''} ${item.bidhaa?.jina || ''} ${item.bidhaa?.aina || ''} ${item.saplaya || ''} ${item.simu || ''} ${item.mengineyo || ''}`.toLowerCase().includes(this.currentSearchTerm));
            this.currentPage = 1; this.renderTable(); this.updatePaginationInfo(); this.updateSearchStatus();
            const dateRangeInfo = document.getElementById('date-range-info'); const dateRangeText = document.getElementById('date-range-text');
            if (this.filteredData.length > 0) { if (dateRangeInfo) dateRangeInfo.classList.remove('hidden'); if (dateRangeText) dateRangeText.textContent = `${new Date(startDate).toLocaleDateString()} - ${new Date(endDate).toLocaleDateString()} (${this.filteredData.length} manunuzi)`; }
            else { if (dateRangeInfo) dateRangeInfo.classList.add('hidden'); this.showNotification('Hakuna manunuzi katika kipindi hiki', 'info'); }
        }

        clearDateFilter() {
            this.filteredData = [...this.allManunuziData];
            if (this.currentSearchTerm) this.filteredData = this.filteredData.filter(item => `${item.risiti_number || ''} ${item.bidhaa?.jina || ''} ${item.bidhaa?.aina || ''} ${item.saplaya || ''} ${item.simu || ''} ${item.mengineyo || ''}`.toLowerCase().includes(this.currentSearchTerm));
            this.currentPage = 1; this.renderTable(); this.updatePaginationInfo(); this.updateSearchStatus();
            document.getElementById('date-range-info')?.classList.add('hidden'); this.setDefaultDates();
        }

        generateReport() {
            const startDate = document.getElementById('report-start-date')?.value; const endDate = document.getElementById('report-end-date')?.value; const reportType = document.getElementById('report-type')?.value || 'summary';
            if (!startDate || !endDate) { this.showNotification('Tafadhali chagua tarehe zote mbili', 'warning'); return; }
            const start = new Date(startDate); start.setHours(0, 0, 0, 0); const end = new Date(endDate); end.setHours(23, 59, 59, 999);
            const filteredData = this.allManunuziData.filter(item => { const itemDate = new Date(item.created_at); return itemDate >= start && itemDate <= end; });
            if (filteredData.length === 0) { this.showNotification('Hakuna data katika kipindi hiki', 'info'); return; }
            const totalCount = filteredData.length; const totalItems = filteredData.reduce((sum, item) => sum + parseFloat(item.idadi || 0), 0); const totalCost = filteredData.reduce((sum, item) => sum + parseFloat(item.bei || 0), 0); const avgCost = totalCount > 0 ? totalCost / totalCount : 0;
            document.getElementById('report-total-count').textContent = totalCount; document.getElementById('report-total-items').textContent = totalItems.toFixed(2); document.getElementById('report-total-cost').textContent = totalCost.toFixed(2); document.getElementById('report-avg-cost').textContent = avgCost.toFixed(2);
            let tableHtml = '';
            if (reportType === 'summary') {
                const grouped = {}; filteredData.forEach(item => { const date = new Date(item.created_at).toLocaleDateString(); if (!grouped[date]) grouped[date] = { count: 0, items: 0, cost: 0 }; grouped[date].count++; grouped[date].items += parseFloat(item.idadi || 0); grouped[date].cost += parseFloat(item.bei || 0); });
                Object.keys(grouped).sort().forEach(date => { tableHtml += `<tr><td class="px-4 py-2">${date}</td><td class="px-4 py-2">Manunuzi ${grouped[date].count}</td><td class="px-4 py-2 text-center">${grouped[date].items.toFixed(2)}</td><td class="px-4 py-2 text-right">${grouped[date].cost.toFixed(2)}</td><td class="px-4 py-2 text-right">-</td><td class="px-4 py-2">-</td></tr>`; });
            } else if (reportType === 'by-supplier') {
                const grouped = {}; filteredData.forEach(item => { const supplier = item.saplaya || 'Hakuna'; if (!grouped[supplier]) grouped[supplier] = { count: 0, items: 0, cost: 0 }; grouped[supplier].count++; grouped[supplier].items += parseFloat(item.idadi || 0); grouped[supplier].cost += parseFloat(item.bei || 0); });
                Object.keys(grouped).sort().forEach(supplier => { tableHtml += `<tr><td class="px-4 py-2">-</td><td class="px-4 py-2">${this.escapeHtml(supplier)}</td><td class="px-4 py-2 text-center">${grouped[supplier].items.toFixed(2)}</td><td class="px-4 py-2 text-right">${grouped[supplier].cost.toFixed(2)}</td><td class="px-4 py-2 text-right">-</td><td class="px-4 py-2">${this.escapeHtml(supplier)}</td></tr>`; });
            } else {
                filteredData.forEach(item => { tableHtml += `<tr><td class="px-4 py-2">${new Date(item.created_at).toLocaleDateString()}</td><td class="px-4 py-2">${this.escapeHtml(item.bidhaa?.jina || '')}</td><td class="px-4 py-2 text-center">${parseFloat(item.idadi || 0).toFixed(2)}</td><td class="px-4 py-2 text-right">${parseFloat(item.bei || 0).toFixed(2)}</td><td class="px-4 py-2 text-right">${parseFloat(item.bidhaa?.bei_kuuza || 0).toFixed(2)}</td><td class="px-4 py-2">${this.escapeHtml(item.saplaya || '--')}</td></tr>`; });
            }
            document.getElementById('report-tbody').innerHTML = tableHtml; document.getElementById('report-results').classList.remove('hidden');
        }

        printManunuzi() {
            const dataToPrint = this.filteredData;
            if (dataToPrint.length === 0) { this.showNotification('Hakuna manunuzi ya kuchapisha', 'warning'); return; }
            let tableRows = '';
            dataToPrint.forEach(item => { tableRows += `<tr><td style="border: 1px solid #ddd; padding: 8px;">${item.risiti_number || ''}</td><td style="border: 1px solid #ddd; padding: 8px;">${item.created_at_formatted || ''}</td><td style="border: 1px solid #ddd; padding: 8px;">${this.escapeHtml(item.bidhaa?.jina || '')}</td><td style="border: 1px solid #ddd; padding: 8px; text-align: center;">${parseFloat(item.idadi || 0).toFixed(2)}</td><td style="border: 1px solid #ddd; padding: 8px; text-align: right;">${parseFloat(item.bei || 0).toLocaleString()}</td><td style="border: 1px solid #ddd; padding: 8px; text-align: right;">${parseFloat(item.bidhaa?.bei_kuuza || 0).toLocaleString()}</td><td style="border: 1px solid #ddd; padding: 8px;">${this.escapeHtml(item.saplaya || '--')}</td></tr>`; });
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`<!DOCTYPE html><html><head><title>Orodha ya Manunuzi</title><style>body{font-family:Arial,sans-serif;margin:20px}table{width:100%;border-collapse:collapse;margin-top:20px}th,td{border:1px solid #ddd;padding:8px}th{background-color:#f3f4f6;font-weight:bold}.header{text-align:center;margin-bottom:30px}.header h2{margin:0;color:#047857}.header p{margin:5px 0 0 0;color:#6b7280}.search-info{text-align:center;color:#6b7280;font-size:14px;margin-top:10px}.total-info{text-align:right;margin-top:20px;font-weight:bold}</style></head><body><div class="header"><h2>Orodha ya Manunuzi</h2><p>${new Date().toLocaleDateString()}</p>${this.currentSearchTerm ? `<p class="search-info">Matokeo ya utafutaji: "${this.currentSearchTerm}"</p>` : ''}</div><table><thead><tr><th>Risiti</th><th>Tarehe</th><th>Bidhaa</th><th>Idadi</th><th>Bei Nunua</th><th>Bei Uza</th><th>Saplaya</th></tr></thead><tbody>${tableRows}</tbody></table><div class="total-info">Jumla: ${dataToPrint.length} | Gharama: ${dataToPrint.reduce((sum, item) => sum + parseFloat(item.bei || 0), 0).toLocaleString()}</div></body></html>`);
            printWindow.document.close(); printWindow.print();
        }

        exportPDF() { const search = new URLSearchParams(window.location.search); search.set('export', 'pdf'); window.open(`${window.location.pathname}?${search.toString()}`, '_blank'); }
        exportExcel() { const search = new URLSearchParams(window.location.search); search.set('export', 'excel'); window.location.href = `${window.location.pathname}?${search.toString()}`; }

        escapeHtml(text) { if (!text) return ''; return String(text).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;'); }

        showNotification(message, type = 'info') {
            const container = document.getElementById('notification-container'); if (!container) return;
            const colors = { success: 'bg-emerald-50 border-emerald-200 text-emerald-800', error: 'bg-red-50 border-red-200 text-red-800', warning: 'bg-amber-50 border-amber-200 text-amber-800', info: 'bg-blue-50 border-blue-200 text-blue-800' };
            const notification = document.createElement('div'); notification.className = `rounded border px-4 py-3 text-sm font-medium mb-2 ${colors[type] || colors.info} shadow-sm`; notification.textContent = message;
            container.appendChild(notification);
            setTimeout(() => { notification.style.opacity = '0'; notification.style.transition = 'opacity 0.3s ease'; setTimeout(() => notification.remove(), 300); }, 4000);
        }
    }

    document.addEventListener('DOMContentLoaded', function() { window.manunuziManager = new ManunuziManager(); });
    function clearManunuziSearch() { if (window.manunuziManager) window.manunuziManager.clearSearch(); }
})();
</script>

<!-- ============================================================
     BATCH RECEIPT SCRIPT: drafts + new products
     ============================================================ -->
<script>
    // ---------- STATE ----------
    let batchCart = [];
    let batchBidhaaData = [];
    let batchLastResults = [];
    let pendingNewProduct = null;          // product typed in the modal but not yet in cart
    let currentDraftToken = null;          // set when editing a saved draft
    let draftsData = @json($draftList);

    const CSRF = '{{ csrf_token() }}';
    const URL_DRAFT_SAVE   = '{{ route('manunuzi.draft.save') }}';
    const URL_DRAFT_LIST   = '{{ route('manunuzi.draft.list') }}';
    const URL_DRAFT_BASE   = '{{ url('/manunuzi/draft') }}';
    const URL_STORE        = '{{ route('manunuzi.store') }}';

    function toast(msg, type = 'info') {
        if (window.manunuziManager) window.manunuziManager.showNotification(msg, type);
    }
    function esc(t) {
        if (t === null || t === undefined) return '';
        return String(t).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }
    function jsonHeaders() {
        return { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' };
    }

    document.addEventListener('DOMContentLoaded', function() {
        const el = document.getElementById('bidhaa-data');
        if (el) batchBidhaaData = JSON.parse(el.textContent);
        renderDrafts();
    });

    // ---------- PRODUCT SEARCH (with "add new" option) ----------
    let batchSearchTimeout = null;
    document.getElementById('batch-search-input').addEventListener('input', function(e) {
        clearTimeout(batchSearchTimeout);
        const raw = e.target.value.trim();
        const query = raw.toLowerCase();
        const resultsDiv = document.getElementById('batch-search-results');

        // Typing again invalidates a previous selection
        document.getElementById('batch-bidhaa_id').value = '';
        pendingNewProduct = null;
        updateSelectedInfo();

        if (query.length < 2) { resultsDiv.classList.add('hidden'); return; }

        batchSearchTimeout = setTimeout(() => {
            batchLastResults = batchBidhaaData.filter(b =>
                (b.jina && b.jina.toLowerCase().includes(query)) ||
                (b.aina && b.aina.toLowerCase().includes(query))
            );
            renderBatchSearchResults(batchLastResults, raw);
        }, 250);
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('#batch-search-input') && !e.target.closest('#batch-search-results')) {
            document.getElementById('batch-search-results').classList.add('hidden');
        }
    });

    function renderBatchSearchResults(results, typed) {
        const resultsDiv = document.getElementById('batch-search-results');
        let html = '';

        if (results.length === 0) {
            html += '<div class="p-3 text-sm text-gray-500">Hakuna bidhaa inayolingana na "' + esc(typed) + '"</div>';
        } else {
            results.forEach((item, i) => {
                html += `<div class="p-3 border-b border-gray-100 hover:bg-blue-50 cursor-pointer" onclick="selectBatchBidhaa(${i})">
                    <div class="font-medium text-sm text-gray-900">${esc(item.jina)}</div>
                    <div class="text-xs text-gray-600">${esc(item.aina)} - ${esc(item.kipimo)}</div>
                    <div class="text-xs text-blue-600 mt-1">Bei ya Kuuza: ${parseFloat(item.bei_kuuza || 0).toLocaleString()}</div>
                </div>`;
            });
        }

        html += `<div class="p-3 bg-blue-50 hover:bg-blue-100 cursor-pointer text-sm font-semibold text-blue-700" onclick="openNewProductModal(${JSON.stringify(typed).replace(/"/g, '&quot;')})">
            <i class="fas fa-plus-circle mr-1"></i> Ongeza bidhaa mpya: "${esc(typed)}"
        </div>`;

        resultsDiv.innerHTML = html;
        resultsDiv.classList.remove('hidden');
    }

    function selectBatchBidhaa(index) {
        const item = batchLastResults[index];
        if (!item) return;
        pendingNewProduct = null;
        document.getElementById('batch-bidhaa_id').value = item.id;
        document.getElementById('batch-search-input').value = item.jina;
        document.getElementById('batch-search-results').classList.add('hidden');
        if (item.bei_kuuza) document.getElementById('batch-bei_kuuza').value = item.bei_kuuza;
        updateSelectedInfo();
        document.getElementById('batch-idadi').focus();
    }

    function updateSelectedInfo() {
        const box = document.getElementById('batch-selected-info');
        if (pendingNewProduct) {
            box.className = 'text-xs px-2 py-1 rounded bg-blue-50 border border-blue-200 text-blue-800';
            box.innerHTML = '<i class="fas fa-star mr-1"></i> <strong>BIDHAA MPYA:</strong> ' + esc(pendingNewProduct.jina) + ' (' + esc(pendingNewProduct.aina) + (pendingNewProduct.kipimo ? ' - ' + esc(pendingNewProduct.kipimo) : '') + ')';
        } else if (document.getElementById('batch-bidhaa_id').value) {
            box.className = 'text-xs px-2 py-1 rounded bg-emerald-50 border border-emerald-200 text-emerald-800';
            box.innerHTML = '<i class="fas fa-check mr-1"></i> Bidhaa iliyopo imechaguliwa';
        } else {
            box.className = 'hidden';
            box.innerHTML = '';
        }
    }

    // ---------- NEW PRODUCT MODAL ----------
    function openNewProductModal(prefillName) {
        document.getElementById('np-jina').value = (typeof prefillName === 'string' && prefillName) ? prefillName : document.getElementById('batch-search-input').value.trim();
        document.getElementById('np-aina').value = '';
        document.getElementById('np-kipimo').value = '';
        document.getElementById('np-barcode').value = '';
        document.getElementById('np-expiry').value = '';
        document.getElementById('np-error').classList.add('hidden');
        document.getElementById('batch-search-results').classList.add('hidden');
        document.getElementById('new-product-modal').classList.remove('hidden');
        setTimeout(() => document.getElementById('np-jina').focus(), 50);
    }

    function closeNewProductModal() {
        document.getElementById('new-product-modal').classList.add('hidden');
    }

    function confirmNewProduct() {
        const jina = document.getElementById('np-jina').value.trim();
        const aina = document.getElementById('np-aina').value.trim();
        const err = document.getElementById('np-error');

        if (!jina || !aina) {
            err.textContent = 'Jina na Aina vinahitajika';
            err.classList.remove('hidden');
            return;
        }

        // If same product already exists, just select it instead of duplicating
        const kipimo = document.getElementById('np-kipimo').value.trim();
        const dup = batchBidhaaData.find(b =>
            (b.jina || '').toLowerCase() === jina.toLowerCase() &&
            (b.aina || '').toLowerCase() === aina.toLowerCase() &&
            (b.kipimo || '').toLowerCase() === kipimo.toLowerCase()
        );
        if (dup) {
            closeNewProductModal();
            pendingNewProduct = null;
            document.getElementById('batch-bidhaa_id').value = dup.id;
            document.getElementById('batch-search-input').value = dup.jina;
            if (dup.bei_kuuza) document.getElementById('batch-bei_kuuza').value = dup.bei_kuuza;
            updateSelectedInfo();
            toast('Bidhaa hii tayari ipo, imechaguliwa', 'info');
            document.getElementById('batch-idadi').focus();
            return;
        }

        pendingNewProduct = {
            jina, aina, kipimo,
            barcode: document.getElementById('np-barcode').value.trim(),
            expiry: document.getElementById('np-expiry').value || null
        };
        document.getElementById('batch-bidhaa_id').value = '';
        document.getElementById('batch-search-input').value = jina;
        closeNewProductModal();
        updateSelectedInfo();
        document.getElementById('batch-idadi').focus();
    }

    // ---------- LIVE CALC ----------
    const inputIdadi = document.getElementById('batch-idadi');
    const inputBeiNunua = document.getElementById('batch-bei_nunua');
    const inputBeiKuuza = document.getElementById('batch-bei_kuuza');
    const calcDisplay = document.getElementById('batch-calc-display');

    function calculateLive() {
        const idadi = parseFloat(inputIdadi.value) || 0;
        const totalNunua = parseFloat(inputBeiNunua.value) || 0;
        const beiKuuza = parseFloat(inputBeiKuuza.value) || 0;

        if (idadi > 0 && totalNunua > 0) {
            calcDisplay.classList.remove('hidden');
            const unitCost = totalNunua / idadi;
            const faida = beiKuuza - unitCost;
            document.getElementById('calc-unit-cost').innerText = unitCost.toFixed(2);
            document.getElementById('calc-faida').innerText = faida.toFixed(2);
            const warning = document.getElementById('calc-warning');
            if (faida < 0) {
                warning.classList.remove('hidden');
                inputBeiKuuza.classList.add('border-red-500', 'bg-red-50');
                inputBeiKuuza.classList.remove('border-gray-300');
            } else {
                warning.classList.add('hidden');
                inputBeiKuuza.classList.remove('border-red-500', 'bg-red-50');
                inputBeiKuuza.classList.add('border-gray-300');
            }
        } else {
            calcDisplay.classList.add('hidden');
            inputBeiKuuza.classList.remove('border-red-500', 'bg-red-50');
        }
    }
    inputIdadi.addEventListener('input', calculateLive);
    inputBeiNunua.addEventListener('input', calculateLive);
    inputBeiKuuza.addEventListener('input', calculateLive);

    // ---------- CART ----------
    function addBatchItem() {
        const bidhaaId = document.getElementById('batch-bidhaa_id').value;
        const typedName = document.getElementById('batch-search-input').value.trim();
        const idadi = parseFloat(inputIdadi.value) || 0;
        const bei_nunua = parseFloat(inputBeiNunua.value) || 0;
        const bei_kuuza = parseFloat(inputBeiKuuza.value) || 0;

        if (!bidhaaId && !pendingNewProduct) {
            toast('Chagua bidhaa, au bonyeza "Ongeza Mpya" kama haipo', 'warning');
            document.getElementById('batch-search-input').focus();
            return;
        }
        if (idadi <= 0) { toast('Weka idadi', 'warning'); inputIdadi.focus(); return; }
        if (bei_nunua <= 0) { toast('Weka bei ya kununua', 'warning'); inputBeiNunua.focus(); return; }
        if (bei_kuuza <= 0) { toast('Weka bei ya kuuza', 'warning'); inputBeiKuuza.focus(); return; }
        if (bei_kuuza < bei_nunua / idadi) { toast('Bei ya kuuza ni chini ya bei ya kununua', 'error'); return; }

        let entry;
        if (pendingNewProduct) {
            entry = {
                bidhaa_id: null, is_new: true, new_bidhaa: { ...pendingNewProduct },
                jina: pendingNewProduct.jina, aina: pendingNewProduct.aina, kipimo: pendingNewProduct.kipimo || '',
                idadi, bei_nunua, bei_kuuza
            };
        } else {
            const itemData = batchBidhaaData.find(b => b.id == bidhaaId) || {};
            entry = {
                bidhaa_id: bidhaaId, is_new: false, new_bidhaa: null,
                jina: itemData.jina || typedName, aina: itemData.aina || '', kipimo: itemData.kipimo || '',
                idadi, bei_nunua, bei_kuuza
            };
        }
        batchCart.push(entry);

        document.getElementById('batch-bidhaa_id').value = '';
        document.getElementById('batch-search-input').value = '';
        inputIdadi.value = ''; inputBeiNunua.value = ''; inputBeiKuuza.value = '';
        pendingNewProduct = null;
        calcDisplay.classList.add('hidden');
        inputBeiKuuza.classList.remove('border-red-500', 'bg-red-50');
        updateSelectedInfo();
        renderBatchCart();
        document.getElementById('batch-search-input').focus();
    }

    function renderBatchCart() {
        const tbody = document.getElementById('batch-cart-body');
        let html = '';
        let total = 0;

        if (batchCart.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-gray-500">Hakuna bidhaa kwenye risiti hii</td></tr>';
            document.getElementById('batch-total-cost').innerText = '0.00';
            return;
        }

        batchCart.forEach((item, index) => {
            total += item.bei_nunua;
            html += `
                <tr>
                    <td class="px-3 py-2">
                        <div class="font-medium text-gray-900">${esc(item.jina)} ${item.is_new ? '<span class="ml-1 px-1.5 py-0.5 text-[10px] font-bold rounded bg-blue-600 text-white">MPYA</span>' : ''}</div>
                        <div class="text-xs text-gray-500">${esc(item.aina)}${item.kipimo ? ' - ' + esc(item.kipimo) : ''}</div>
                    </td>
                    <td class="px-3 py-2 text-center">${item.idadi}</td>
                    <td class="px-3 py-2 text-right">
                        <div class="font-bold">${item.bei_nunua.toFixed(2)}</div>
                        <div class="text-xs text-gray-500">@ ${(item.bei_nunua / item.idadi).toFixed(2)}</div>
                    </td>
                    <td class="px-3 py-2 text-right">${item.bei_kuuza.toFixed(2)}</td>
                    <td class="px-3 py-2 text-center">
                        <button type="button" onclick="removeBatchItem(${index})" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>`;
        });

        tbody.innerHTML = html;
        document.getElementById('batch-total-cost').innerText = total.toFixed(2);
    }

    function removeBatchItem(index) {
        batchCart.splice(index, 1);
        renderBatchCart();
    }

    function batchPayload() {
        return {
            _token: CSRF,
            draft_token: currentDraftToken,
            saplaya: document.getElementById('batch-saplaya').value,
            simu: document.getElementById('batch-simu').value,
            mengineyo: document.getElementById('batch-mengineyo').value,
            batch_items: batchCart
        };
    }

    // ---------- SUBMIT FINAL RECEIPT ----------
    async function submitBatch() {
        if (batchCart.length === 0) { toast('Ongeza angalau bidhaa moja kwenye risiti', 'warning'); return; }

        const btn = document.getElementById('batch-submit-btn');
        const original = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Inahifadhi...';

        try {
            const res = await fetch(URL_STORE, { method: 'POST', headers: jsonHeaders(), body: JSON.stringify(batchPayload()) });
            const data = await res.json();
            if (data.success) {
                toast(data.message || 'Risiti imehifadhiwa!', 'success');
                window.location.href = data.redirect;
            } else {
                toast(data.message || 'Imeshindwa kuhifadhi risiti', 'error');
                btn.disabled = false; btn.innerHTML = original;
            }
        } catch (error) {
            toast('Hitilafu ya mtandao. Jaribu tena.', 'error');
            btn.disabled = false; btn.innerHTML = original;
        }
    }

    // ---------- DRAFTS ----------
    async function saveBatchDraft() {
        if (batchCart.length === 0) { toast('Ongeza angalau bidhaa moja kabla ya kuhifadhi draft', 'warning'); return; }

        const btn = document.getElementById('batch-draft-btn');
        const original = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Inahifadhi...';

        try {
            const res = await fetch(URL_DRAFT_SAVE, { method: 'POST', headers: jsonHeaders(), body: JSON.stringify(batchPayload()) });
            const data = await res.json();
            if (data.success) {
                currentDraftToken = data.draft_token;
                updateDraftBanner();
                toast(data.message || 'Draft imehifadhiwa!', 'success');
                await refreshDrafts();
            } else {
                toast(data.message || 'Imeshindwa kuhifadhi draft', 'error');
            }
        } catch (e) {
            toast('Hitilafu ya mtandao. Jaribu tena.', 'error');
        } finally {
            btn.disabled = false; btn.innerHTML = original;
        }
    }

    async function refreshDrafts() {
        try {
            const res = await fetch(URL_DRAFT_LIST, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            if (data.success) { draftsData = data.drafts; renderDrafts(); }
        } catch (e) { /* ignore */ }
    }

    function renderDrafts() {
        const panel = document.getElementById('drafts-panel');
        const list = document.getElementById('drafts-list');
        const badge = document.getElementById('draft-count-badge');

        if (!draftsData || draftsData.length === 0) {
            panel.classList.add('hidden');
            badge.classList.add('hidden');
            return;
        }

        badge.textContent = draftsData.length;
        badge.classList.remove('hidden');
        panel.classList.remove('hidden');

        list.innerHTML = draftsData.map(d => `
            <div class="flex flex-wrap items-center justify-between gap-2 bg-white border ${d.draft_token === currentDraftToken ? 'border-amber-500' : 'border-amber-200'} rounded p-2">
                <div class="text-sm">
                    <div class="font-semibold text-gray-800">${esc(d.saplaya || 'Msaplaya hajawekwa')} ${d.simu ? '<span class="text-xs text-gray-500">(' + esc(d.simu) + ')</span>' : ''}</div>
                    <div class="text-xs text-gray-500">Bidhaa ${d.item_count} • ${esc(d.updated_at)}</div>
                </div>
                <div class="flex gap-1">
                    <button type="button" onclick="loadBatchDraft('${esc(d.draft_token)}')" class="px-3 py-1 bg-amber-500 text-white rounded text-xs font-medium hover:bg-amber-600"><i class="fas fa-play mr-1"></i> Endelea</button>
                    <button type="button" onclick="deleteBatchDraft('${esc(d.draft_token)}')" class="px-2 py-1 bg-red-100 text-red-600 rounded text-xs hover:bg-red-200"><i class="fas fa-trash"></i></button>
                </div>
            </div>`).join('');
    }

    function updateDraftBanner() {
        const banner = document.getElementById('current-draft-banner');
        if (currentDraftToken) {
            document.getElementById('current-draft-token').textContent = currentDraftToken;
            banner.classList.remove('hidden');
        } else {
            banner.classList.add('hidden');
        }
        renderDrafts();
    }

    async function loadBatchDraft(token) {
        if (batchCart.length > 0 && token !== currentDraftToken) {
            if (!confirm('Risiti uliyonayo sasa haijahifadhiwa. Unataka kuibadilisha na draft hii?')) return;
        }
        try {
            const res = await fetch(`${URL_DRAFT_BASE}/${encodeURIComponent(token)}/load`, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            if (!data.success) { toast(data.message || 'Draft haipatikani', 'error'); refreshDrafts(); return; }

            currentDraftToken = data.draft_token;
            document.getElementById('batch-saplaya').value = data.saplaya || '';
            document.getElementById('batch-simu').value = data.simu || '';
            document.getElementById('batch-mengineyo').value = data.mengineyo || '';

            batchCart = data.items.map(i => ({
                bidhaa_id: i.bidhaa_id,
                is_new: !!i.is_new,
                new_bidhaa: i.new_bidhaa || null,
                jina: i.jina, aina: i.aina, kipimo: i.kipimo || '',
                idadi: parseFloat(i.idadi) || 0,
                bei_nunua: parseFloat(i.bei_nunua) || 0,
                bei_kuuza: parseFloat(i.bei_kuuza) || 0
            }));

            renderBatchCart();
            updateDraftBanner();
            window.manunuziManager?.showTab('batch');
            window.manunuziManager?.saveTab('batch');
            toast('Draft imepakiwa. Endelea na risiti yako.', 'success');
        } catch (e) {
            toast('Hitilafu katika kupakia draft', 'error');
        }
    }

    async function deleteBatchDraft(token) {
        if (!confirm('Futa draft hii? Hatua hii haiwezi kutenduliwa.')) return;
        try {
            const res = await fetch(`${URL_DRAFT_BASE}/${encodeURIComponent(token)}/delete`, { method: 'DELETE', headers: jsonHeaders() });
            const data = await res.json();
            if (data.success) {
                if (token === currentDraftToken) newBatchReceipt(true);
                toast(data.message || 'Draft imefutwa', 'success');
                await refreshDrafts();
            } else {
                toast(data.message || 'Imeshindwa kufuta draft', 'error');
            }
        } catch (e) {
            toast('Hitilafu ya mtandao', 'error');
        }
    }

    // Reset the form to a blank receipt
    function newBatchReceipt(silent) {
        if (!silent && batchCart.length > 0 && !confirm('Futa risiti hii ya sasa na uanze mpya? (Draft iliyohifadhiwa haitafutwa)')) return;
        batchCart = [];
        currentDraftToken = null;
        pendingNewProduct = null;
        ['batch-saplaya', 'batch-simu', 'batch-mengineyo', 'batch-search-input', 'batch-bidhaa_id', 'batch-idadi', 'batch-bei_nunua', 'batch-bei_kuuza']
            .forEach(id => { document.getElementById(id).value = ''; });
        calcDisplay.classList.add('hidden');
        updateSelectedInfo();
        renderBatchCart();
        updateDraftBanner();
    }
</script>
@endpush