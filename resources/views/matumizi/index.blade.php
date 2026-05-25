@extends('layouts.app')

@section('title', 'Matumizi')

@section('page-title', 'Matumizi')
@section('page-subtitle', now()->format('d/m/Y'))

@section('content')
<div class="space-y-4" id="app-container">
    <!-- Notifications -->
    <div id="notification-container" class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-sm px-4 pointer-events-none">
        @if(session('success'))
        <div class="rounded border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 mb-2 shadow-sm animate-fade-in">
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="rounded border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800 mb-2 shadow-sm animate-fade-in">
            {{ session('error') }}
        </div>
        @endif
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="bg-white p-3 rounded-lg border border-red-200 shadow-sm">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Jumla ya Matumizi</p>
                    <p class="text-xl font-bold text-red-700">{{ number_format($totalExpenses, 0) }}</p>
                </div>
                <i class="fas fa-money-bill-wave text-red-500 text-lg"></i>
            </div>
        </div>
        <div class="bg-white p-3 rounded-lg border border-blue-200 shadow-sm">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Matumizi Ya Leo</p>
                    <p class="text-xl font-bold text-blue-700">{{ number_format($todayExpenses, 0) }}</p>
                </div>
                <i class="fas fa-calendar-day text-blue-500 text-lg"></i>
            </div>
        </div>
        <div class="bg-white p-3 rounded-lg border border-purple-200 shadow-sm">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Idadi ya Matumizi</p>
                    <p class="text-xl font-bold text-purple-700">{{ $expensesCount }}</p>
                </div>
                <i class="fas fa-list text-purple-500 text-lg"></i>
            </div>
        </div>
        <div class="bg-white p-3 rounded-lg border border-amber-200 shadow-sm">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Wastani wa Matumizi</p>
                    <p class="text-xl font-bold text-amber-700">{{ number_format($averageExpense, 0) }}</p>
                </div>
                <i class="fas fa-chart-pie text-amber-500 text-lg"></i>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm">
        <div class="flex flex-wrap">
            <button data-tab="taarifa" class="tab-button flex-1 py-3 px-4 text-sm font-medium border-r border-gray-200 bg-emerald-50 text-emerald-700">
                <i class="fas fa-table mr-2"></i> Taarifa
            </button>
            <button data-tab="ingiza" class="tab-button flex-1 py-3 px-4 text-sm font-medium border-r border-gray-200 text-gray-600 hover:bg-gray-50">
                <i class="fas fa-plus mr-2"></i> Ingiza
            </button>
            <button data-tab="sajili" class="tab-button flex-1 py-3 px-4 text-sm font-medium border-r border-gray-200 text-gray-600 hover:bg-gray-50">
                <i class="fas fa-tags mr-2"></i> Sajili Aina
            </button>
            <button data-tab="ripoti" class="tab-button flex-1 py-3 px-4 text-sm font-medium text-gray-600 hover:bg-gray-50">
                <i class="fas fa-chart-bar mr-2"></i> Ripoti
            </button>
        </div>
    </div>

    <!-- TAB 1: Taarifa -->
    <div id="taarifa-tab-content" class="tab-content space-y-3">
        <!-- Search Bar -->
        <div class="bg-white p-3 rounded-lg border border-gray-200 shadow-sm">
            <div class="flex flex-col sm:flex-row gap-2 sm:items-center">
                <div class="flex-1 relative">
                    <input 
                        type="text" 
                        id="search-input"
                        placeholder="Tafuta aina, maelezo..." 
                        class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500"
                    >
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                <div class="flex gap-2">
                    <button onclick="printMatumizi()" class="px-3 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 text-sm font-medium">
                        <i class="fas fa-print mr-1"></i> Print
                    </button>
                    <button onclick="exportPDF()" class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm font-medium">
                        <i class="fas fa-file-pdf mr-1"></i> PDF
                    </button>
                </div>
            </div>
        </div>

        <!-- Matumizi Table with Pagination -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-emerald-50">
                            <th class="px-4 py-2 text-left font-medium text-emerald-800">Tarehe</th>
                            <th class="px-4 py-2 text-left font-medium text-emerald-800">Aina</th>
                            <th class="px-4 py-2 text-left font-medium text-emerald-800 hidden md:table-cell">Maelezo</th>
                            <th class="px-4 py-2 text-right font-medium text-emerald-800">Gharama</th>
                            <th class="px-4 py-2 text-center font-medium text-emerald-800 print:hidden">Vitendo</th>
                        </tr>
                    </thead>
                    <tbody id="matumizi-tbody" class="divide-y divide-gray-100">
                        @forelse($matumizi as $item)
                            <tr class="matumizi-row hover:bg-gray-50" data-matumizi='@json($item)'>
                                <td class="px-4 py-2">
                                    <div class="text-xs text-gray-900">{{ $item->created_at->format('d/m/Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $item->created_at->format('H:i') }}</div>
                                </td>
                                <td class="px-4 py-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold
                                        @if($item->aina === 'Mshahara') bg-green-100 text-green-800 border border-green-200
                                        @elseif($item->aina === 'Bank') bg-blue-100 text-blue-800 border border-blue-200
                                        @elseif($item->aina === 'Kodi TRA') bg-red-100 text-red-800 border border-red-200
                                        @elseif($item->aina === 'Kodi Pango') bg-yellow-100 text-yellow-800 border border-yellow-200
                                        @else bg-gray-100 text-gray-800 border border-gray-200 @endif">
                                        {{ $item->aina }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 hidden md:table-cell">
                                    <div class="text-xs text-gray-700 truncate max-w-xs">{{ $item->maelezo ?: '--' }}</div>
                                </td>
                                <td class="px-4 py-2 text-right">
                                    <div class="text-sm font-bold text-red-700">{{ number_format($item->gharama, 0) }}</div>
                                </td>
                                <td class="px-4 py-2 text-center print:hidden">
                                    <div class="flex justify-center space-x-2">
                                        <button class="edit-matumizi-btn text-emerald-600 hover:text-emerald-800"
                                                data-id="{{ $item->id }}" title="Badili">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="delete-matumizi-btn text-red-600 hover:text-red-800"
                                                data-id="{{ $item->id }}" data-name="{{ $item->aina }}" title="Futa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                    <i class="fas fa-receipt text-3xl mb-2 text-gray-300"></i>
                                    <p>Hakuna matumizi bado</p>
                                    <p class="text-xs mt-1">Bonyeza kwenye kichupo cha "Ingiza" kuongeza matumizi</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($matumizi->count() > 0)
                    <tfoot>
                        <tr class="bg-gray-50 font-semibold">
                            <td colspan="2" class="px-4 py-3 text-right text-sm text-gray-700 hidden md:table-cell">
                                Jumla ya Matumizi:
                            </td>
                            <td class="px-4 py-3 text-right text-sm font-bold text-red-700">
                                {{ number_format($matumizi->sum('gharama'), 0) }}
                            </td>
                            <td class="print:hidden"></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
            
            <!-- Pagination Links -->
            @if($matumizi->hasPages())
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $matumizi->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- TAB 2: Ingiza -->
    <div id="ingiza-tab-content" class="tab-content hidden">
        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
            <form method="POST" action="{{ route('matumizi.store') }}" id="matumizi-form" class="space-y-4">
                @csrf
                
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Aina ya Matumizi *</label>
                    <select name="aina" id="aina-select" 
                            class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500" 
                            required>
                        <option value="">-- Chagua Aina --</option>
                        @foreach(['Mshahara', 'Bank', 'Kodi TRA', 'Kodi Pango'] as $aina)
                            <option value="{{ $aina }}">{{ $aina }}</option>
                        @endforeach
                        @if($aina_za_matumizi->count() > 0)
                            @foreach($aina_za_matumizi as $aina)
                                <option value="{{ $aina->jina }}">{{ $aina->jina }}</option>
                            @endforeach
                        @endif
                        <option value="mengineyo">Mengineyo (Ingiza mpya)...</option>
                    </select>
                    
                    <div id="custom-aina-container" class="mt-2 hidden">
                        <input type="text" 
                               name="aina_mpya" 
                               id="aina_mpya"
                               placeholder="Ingiza aina mpya ya matumizi..."
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Kiasi cha Gharama (TZS) *</label>
                        <input type="number" step="0.01" name="gharama" id="gharama" 
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                               placeholder="0.00" required>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Tarehe ya Matumizi</label>
                        <input type="date" name="tarehe" id="tarehe" 
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                               value="{{ now()->format('Y-m-d') }}">
                        <p class="text-xs text-gray-500 mt-1">Weka tarehe tofauti kama unarekodi matumizi ya siku iliyopita</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Maelezo (si lazima)</label>
                        <textarea name="maelezo" rows="2" id="maelezo"
                                  class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                                  placeholder="Maelezo ya ziada..."></textarea>
                    </div>
                </div>

                <div class="flex gap-2 pt-4 border-t border-gray-200">
                    <button type="submit" 
                            class="flex-1 bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700 text-sm font-medium">
                        <i class="fas fa-save mr-1"></i> Hifadhi
                    </button>
                    <button type="button" onclick="clearMatumiziForm()"
                            class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 text-sm">
                        <i class="fas fa-redo mr-1"></i> Safisha
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- TAB 3: Sajili Aina -->
    <div id="sajili-tab-content" class="tab-content hidden">
        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
            <h3 class="text-sm font-semibold text-gray-800 mb-3">Sajili Aina Mpya ya Matumizi</h3>
            <form method="POST" action="{{ route('matumizi.sajili-aina') }}" id="sajili-form" class="space-y-4">
                @csrf
                
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Jina la Aina *</label>
                    <input type="text" name="jina" id="jina-aina" 
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                           placeholder="Mfano: Mafuta ya Gari, Umeme, Maji, N.K." required>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Maelezo (si lazima)</label>
                    <textarea name="maelezo" rows="2" 
                              class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                              placeholder="Maelezo ya aina hii ya matumizi..."></textarea>
                </div>

                <div class="flex gap-2 pt-4 border-t border-gray-200">
                    <button type="submit" 
                            class="flex-1 bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700 text-sm font-medium">
                        <i class="fas fa-save mr-1"></i> Sajili
                    </button>
                    <button type="reset" 
                            class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 text-sm">
                        <i class="fas fa-redo mr-1"></i> Safisha
                    </button>
                </div>
            </form>

            @if($aina_za_matumizi->count() > 0)
            <div class="mt-6 pt-4 border-t border-gray-200">
                <h4 class="text-xs font-semibold text-gray-700 mb-3">Aina Zilizosajiliwa</h4>
                <div class="space-y-2 max-h-64 overflow-y-auto">
                    @foreach($aina_za_matumizi as $aina)
                        <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                            <div class="flex-1">
                                <span class="text-sm text-gray-800">{{ $aina->jina }}</span>
                                @if($aina->maelezo)
                                    <span class="text-xs text-gray-500 ml-2">- {{ $aina->maelezo }}</span>
                                @endif
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-xs bg-emerald-100 text-emerald-800 px-2 py-1 rounded-full">
                                    {{ $aina->matumizi_count ?? 0 }} matumizi
                                </span>
                                <button type="button" onclick="deleteAina('{{ $aina->id }}', '{{ $aina->jina }}')"
                                        class="text-red-500 hover:text-red-700 text-sm">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- TAB 4: Ripoti -->
    <div id="ripoti-tab-content" class="tab-content hidden">
        <div class="space-y-4">
            <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                <h3 class="text-sm font-medium text-gray-900 mb-3">
                    <i class="fas fa-filter text-emerald-600 mr-2"></i>
                    Chuja Ripoti
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Kuanzia Tarehe</label>
                        <input type="date" id="report-start-date" class="w-full px-3 py-2 border border-gray-300 rounded text-sm"
                               value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Mpaka Tarehe</label>
                        <input type="date" id="report-end-date" class="w-full px-3 py-2 border border-gray-300 rounded text-sm"
                               value="{{ now()->format('Y-m-d') }}">
                    </div>
                    <div class="flex items-end gap-2">
                        <button onclick="generateReport()" class="flex-1 px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 text-sm font-medium">
                            <i class="fas fa-chart-bar mr-1"></i> Generate
                        </button>
                        <button onclick="exportReportPDF()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                            <i class="fas fa-file-pdf"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div id="report-results-container" class="hidden space-y-4">
                <!-- Summary Cards -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <div class="bg-gradient-to-br from-red-50 to-red-100 p-3 rounded-lg border border-red-200">
                        <p class="text-xs text-gray-600 mb-1">Jumla ya Matumizi</p>
                        <p class="text-lg font-bold text-red-700" id="report-total">0</p>
                    </div>
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-3 rounded-lg border border-blue-200">
                        <p class="text-xs text-gray-600 mb-1">Idadi ya Matumizi</p>
                        <p class="text-lg font-bold text-blue-700" id="report-count">0</p>
                    </div>
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-3 rounded-lg border border-purple-200">
                        <p class="text-xs text-gray-600 mb-1">Wastani</p>
                        <p class="text-lg font-bold text-purple-700" id="report-average">0</p>
                    </div>
                    <div class="bg-gradient-to-br from-amber-50 to-amber-100 p-3 rounded-lg border border-amber-200">
                        <p class="text-xs text-gray-600 mb-1">Ya Juu Zaidi</p>
                        <p class="text-lg font-bold text-amber-700" id="report-max">0</p>
                    </div>
                </div>

                <!-- Charts -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Matumizi kwa Siku</h4>
                        <div class="h-64">
                            <canvas id="dailyChart"></canvas>
                        </div>
                    </div>
                    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Matumizi kwa Aina</h4>
                        <div class="h-64">
                            <canvas id="categoryChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Grouped Table by Type -->
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-emerald-50">
                                    <th class="px-4 py-2 text-left font-medium text-emerald-800">Aina ya Matumizi</th>
                                    <th class="px-4 py-2 text-right font-medium text-emerald-800">Idadi</th>
                                    <th class="px-4 py-2 text-right font-medium text-emerald-800">Jumla</th>
                                    <th class="px-4 py-2 text-right font-medium text-emerald-800">Wastani</th>
                                    <th class="px-4 py-2 text-center font-medium text-emerald-800">Asilimia</th>
                                </tr>
                            </thead>
                            <tbody id="report-grouped-tbody" class="divide-y divide-gray-100">
                            </tbody>
                            <tfoot>
                                <tr class="bg-gray-50 font-semibold">
                                    <td class="px-4 py-3 text-right text-sm">Jumla:</td>
                                    <td class="px-4 py-3 text-right text-sm" id="footer-count">0</td>
                                    <td class="px-4 py-3 text-right text-sm font-bold text-red-700" id="footer-total">0</td>
                                    <td class="px-4 py-3 text-right text-sm"></td>
                                    <td class="px-4 py-3 text-right text-sm">100%</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Detailed Table -->
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-4 py-2 text-left font-medium text-gray-700">Tarehe</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-700">Aina</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-700">Maelezo</th>
                                    <th class="px-4 py-2 text-right font-medium text-gray-700">Gharama</th>
                                </tr>
                            </thead>
                            <tbody id="report-detail-tbody" class="divide-y divide-gray-100">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="report-no-data" class="hidden">
                <div class="bg-white p-8 rounded-lg border border-gray-200 shadow-sm text-center">
                    <i class="fas fa-chart-bar text-4xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500">Hakuna data ya matumizi katika kipindi ulichochagua</p>
                    <p class="text-gray-400 text-xs mt-2">Tafadhali chagua tarehe nyingine au ingiza matumizi mapya</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="edit-modal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-lg shadow-lg w-full max-w-md mx-auto z-50">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-800">Rekebisha Matumizi</h3>
        </div>
        <form id="edit-form" method="POST" class="p-4">
            @csrf
            @method('PUT')
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Aina ya Matumizi *</label>
                    <select name="aina" id="edit-aina" class="w-full px-3 py-2 border border-gray-300 rounded text-sm" required>
                        @foreach(['Mshahara', 'Bank', 'Kodi TRA', 'Kodi Pango'] as $aina)
                            <option value="{{ $aina }}">{{ $aina }}</option>
                        @endforeach
                        @foreach($aina_za_matumizi as $aina)
                            <option value="{{ $aina->jina }}">{{ $aina->jina }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Maelezo</label>
                    <input type="text" name="maelezo" id="edit-maelezo" class="w-full px-3 py-2 border border-gray-300 rounded text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Kiasi (TZS) *</label>
                    <input type="number" step="0.01" name="gharama" id="edit-gharama" class="w-full px-3 py-2 border border-gray-300 rounded text-sm" required>
                </div>
            </div>
            <div class="flex gap-2 pt-4 border-t border-gray-200 mt-4">
                <button type="button" id="close-edit-modal" class="flex-1 px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 text-sm">Ghairi</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 text-sm font-medium">Hifadhi</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="delete-modal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-lg shadow-lg w-full max-w-sm mx-auto z-50">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-800">Thibitisha Kufuta</h3>
        </div>
        <div class="p-4">
            <p class="text-gray-700 text-sm mb-1">Una uhakika unataka kufuta matumizi ya</p>
            <p class="text-gray-900 font-medium" id="delete-expense-name"></p>
            <div class="flex gap-2 mt-4">
                <button id="cancel-delete" class="flex-1 px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 text-sm">Ghairi</button>
                <form id="delete-form" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm font-medium">Futa</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Aina Modal -->
<div id="delete-aina-modal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-lg shadow-lg w-full max-w-sm mx-auto z-50">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-800">Futa Aina ya Matumizi</h3>
        </div>
        <div class="p-4">
            <p class="text-gray-700 text-sm mb-1">Una uhakika unataka kufuta aina ya matumizi</p>
            <p class="text-gray-900 font-medium" id="delete-aina-name"></p>
            <div class="flex gap-2 mt-4">
                <button id="cancel-delete-aina" class="flex-1 px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 text-sm">Ghairi</button>
                <form id="delete-aina-form" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm font-medium">Futa</button>
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

@media print {
    .print\:hidden {
        display: none !important;
    }
    .modal {
        display: none !important;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
class MatumiziManager {
    constructor() {
        this.currentTab = localStorage.getItem('matumizi_tab') || 'taarifa';
        this.charts = {};
        this.allMatumiziData = [];
        this.init();
    }

    init() {
        this.bindEvents();
        this.showTab(this.currentTab);
        this.setupAjaxForms();
        this.setupAinaSelection();
    }

    bindEvents() {
        // Tab navigation
        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const tab = e.target.closest('.tab-button').dataset.tab;
                this.showTab(tab);
                localStorage.setItem('matumizi_tab', tab);
            });
        });

        // Search
        const searchInput = document.getElementById('search-input');
        if (searchInput) {
            searchInput.addEventListener('keyup', () => this.filterTable());
        }

        // Modal close buttons
        document.getElementById('close-edit-modal')?.addEventListener('click', () => this.closeModal('edit-modal'));
        document.getElementById('cancel-delete')?.addEventListener('click', () => this.closeModal('delete-modal'));
        document.getElementById('cancel-delete-aina')?.addEventListener('click', () => this.closeModal('delete-aina-modal'));
        
        // Close modals on overlay click
        document.querySelectorAll('.modal-overlay').forEach(overlay => {
            overlay.addEventListener('click', (e) => {
                e.target.closest('.modal')?.classList.add('hidden');
            });
        });
    }

    showTab(tabName) {
        document.querySelectorAll('.tab-button').forEach(btn => {
            const btnTab = btn.dataset.tab;
            if (btnTab === tabName) {
                btn.classList.add('bg-emerald-50', 'text-emerald-700');
                btn.classList.remove('text-gray-600', 'hover:bg-gray-50');
            } else {
                btn.classList.remove('bg-emerald-50', 'text-emerald-700');
                btn.classList.add('text-gray-600', 'hover:bg-gray-50');
            }
        });

        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        
        const activeTab = document.getElementById(`${tabName}-tab-content`);
        if (activeTab) activeTab.classList.remove('hidden');
        this.currentTab = tabName;
    }

    filterTable() {
        const searchTerm = document.getElementById('search-input')?.value.toLowerCase() || '';
        const rows = document.querySelectorAll('.matumizi-row');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    }

    setupAinaSelection() {
        const select = document.getElementById('aina-select');
        const container = document.getElementById('custom-aina-container');
        
        if (select) {
            select.addEventListener('change', () => {
                if (select.value === 'mengineyo') {
                    container?.classList.remove('hidden');
                    document.getElementById('aina_mpya')?.setAttribute('required', 'required');
                } else {
                    container?.classList.add('hidden');
                    document.getElementById('aina_mpya')?.removeAttribute('required');
                }
            });
        }
    }

    setupAjaxForms() {
        // Main form
        document.getElementById('matumizi-form')?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.submitForm(e.target, 'Matumizi yamehifadhiwa!');
        });

        // Register type form
        document.getElementById('sajili-form')?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.submitForm(e.target, 'Aina mpya imesajiliwa!');
        });

        // Edit form
        document.getElementById('edit-form')?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.submitForm(e.target, 'Matumizi yamerekebishwa!');
        });

        // Delete forms
        document.getElementById('delete-form')?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.submitForm(e.target, 'Matumizi yamefutwa!');
        });

        document.getElementById('delete-aina-form')?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.submitForm(e.target, 'Aina imefutwa!');
        });

        // Edit buttons
        document.querySelectorAll('.edit-matumizi-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const row = btn.closest('.matumizi-row');
                const data = JSON.parse(row.dataset.matumizi);
                this.openEditModal(data);
            });
        });

        // Delete buttons
        document.querySelectorAll('.delete-matumizi-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                const name = btn.dataset.name;
                this.openDeleteModal(id, name);
            });
        });
    }

    async submitForm(form, successMsg) {
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        try {
            const response = await fetch(form.action, {
                method: form.method,
                body: new FormData(form),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (response.ok) {
                this.showNotification(data.message || successMsg, 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                this.showNotification(data.message || 'Hitilafu imetokea', 'error');
            }
        } catch (error) {
            this.showNotification('Hitilafu ya mtandao', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    }

    openEditModal(data) {
        document.getElementById('edit-aina').value = data.aina;
        document.getElementById('edit-maelezo').value = data.maelezo || '';
        document.getElementById('edit-gharama').value = data.gharama;
        document.getElementById('edit-form').action = `/matumizi/${data.id}`;
        document.getElementById('edit-modal').classList.remove('hidden');
    }

    openDeleteModal(id, name) {
        document.getElementById('delete-expense-name').textContent = name;
        document.getElementById('delete-form').action = `/matumizi/${id}`;
        document.getElementById('delete-modal').classList.remove('hidden');
    }

    closeModal(modalId) {
        document.getElementById(modalId)?.classList.add('hidden');
    }

    showNotification(message, type = 'success') {
        const container = document.getElementById('notification-container');
        if (!container) return;
        
        const colors = {
            success: 'bg-emerald-50 border-emerald-200 text-emerald-800',
            error: 'bg-red-50 border-red-200 text-red-800',
            warning: 'bg-amber-50 border-amber-200 text-amber-800'
        };

        const notification = document.createElement('div');
        notification.className = `rounded border px-4 py-3 text-sm font-medium mb-2 ${colors[type]} shadow-sm animate-fade-in`;
        notification.textContent = message;

        container.appendChild(notification);
        setTimeout(() => notification.remove(), 3000);
    }
}

// Global functions
function clearMatumiziForm() {
    const form = document.getElementById('matumizi-form');
    if (form) form.reset();
    document.getElementById('tarehe').value = new Date().toISOString().split('T')[0];
    document.getElementById('custom-aina-container')?.classList.add('hidden');
    document.getElementById('aina-select').value = '';
}

function deleteAina(id, name) {
    document.getElementById('delete-aina-name').textContent = name;
    document.getElementById('delete-aina-form').action = `/matumizi/aina/${id}`;
    document.getElementById('delete-aina-modal').classList.remove('hidden');
}

function printMatumizi() {
    const printWindow = window.open('', '_blank');
    const rows = document.querySelectorAll('.matumizi-row');
    let html = '<html><head><title>Matumizi Report</title>';
    html += '<style>';
    html += 'body { font-family: Arial, sans-serif; margin: 20px; }';
    html += 'table { width: 100%; border-collapse: collapse; }';
    html += 'th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }';
    html += 'th { background-color: #f3f4f6; }';
    html += '</style></head><body>';
    html += '<h2>Orodha ya Matumizi</h2>';
    html += '<p>Tarehe: ' + new Date().toLocaleDateString() + '</p>';
    html += '<table><thead><tr><th>Tarehe</th><th>Aina</th><th>Maelezo</th><th>Gharama</th></tr></thead><tbody>';
    
    let total = 0;
    rows.forEach(row => {
        const data = JSON.parse(row.dataset.matumizi);
        total += parseFloat(data.gharama);
        html += `<tr>
            <td>${new Date(data.created_at).toLocaleDateString()}</td>
            <td>${data.aina}</td>
            <td>${data.maelezo || '--'}</td>
            <td style="text-align:right">${Number(data.gharama).toLocaleString()}</td>
        </tr>`;
    });
    
    html += `<tr style="font-weight:bold"><td colspan="3">Jumla</td><td style="text-align:right">${total.toLocaleString()}</td></tr>`;
    html += '</tbody></table></body></html>';
    
    printWindow.document.write(html);
    printWindow.document.close();
    printWindow.print();
}

function exportPDF() {
    window.location.href = '/matumizi/export-pdf';
}

async function generateReport() {
    const startDate = document.getElementById('report-start-date').value;
    const endDate = document.getElementById('report-end-date').value;
    
    if (!startDate || !endDate) {
        alert('Tafadhali chagua tarehe zote mbili');
        return;
    }
    
    const response = await fetch(`/matumizi/report-data?start_date=${startDate}&end_date=${endDate}`);
    const result = await response.json();
    
    if (!result.success || result.data.expenses.length === 0) {
        document.getElementById('report-results-container').classList.add('hidden');
        document.getElementById('report-no-data').classList.remove('hidden');
        return;
    }
    
    document.getElementById('report-results-container').classList.remove('hidden');
    document.getElementById('report-no-data').classList.add('hidden');
    
    const data = result.data;
    const total = data.total;
    
    // Update summary
    document.getElementById('report-total').textContent = total.toLocaleString();
    document.getElementById('report-count').textContent = data.count;
    document.getElementById('report-average').textContent = Math.round(data.average).toLocaleString();
    document.getElementById('report-max').textContent = data.max.toLocaleString();
    document.getElementById('footer-total').textContent = total.toLocaleString();
    document.getElementById('footer-count').textContent = data.count;
    
    // Grouped by type table
    let groupedHtml = '';
    for (const [type, amount] of Object.entries(data.categoryData)) {
        const percentage = ((amount / total) * 100).toFixed(1);
        const items = data.expenses.filter(e => e.aina === type);
        const avg = amount / items.length;
        groupedHtml += `
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-2 font-medium">${type}</td>
                <td class="px-4 py-2 text-right">${items.length}</td>
                <td class="px-4 py-2 text-right">${amount.toLocaleString()}</td>
                <td class="px-4 py-2 text-right">${Math.round(avg).toLocaleString()}</td>
                <td class="px-4 py-2 text-right">
                    <div class="flex items-center justify-end">
                        <span class="text-xs">${percentage}%</span>
                        <div class="ml-2 w-16 bg-gray-200 rounded-full h-1.5">
                            <div class="bg-emerald-500 h-1.5 rounded-full" style="width: ${percentage}%"></div>
                        </div>
                    </div>
                </td>
            </tr>
        `;
    }
    document.getElementById('report-grouped-tbody').innerHTML = groupedHtml;
    
    // Detailed table
    let detailHtml = '';
    data.expenses.forEach(expense => {
        detailHtml += `
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-2">${new Date(expense.created_at).toLocaleDateString()}</td>
                <td class="px-4 py-2">${expense.aina}</td>
                <td class="px-4 py-2">${expense.maelezo || '--'}</td>
                <td class="px-4 py-2 text-right font-medium text-red-700">${Number(expense.gharama).toLocaleString()}</td>
            </tr>
        `;
    });
    document.getElementById('report-detail-tbody').innerHTML = detailHtml;
    
    // Charts
    this.updateCharts(data.dailyData, data.categoryData);
}

function exportReportPDF() {
    const startDate = document.getElementById('report-start-date').value;
    const endDate = document.getElementById('report-end-date').value;
    window.location.href = `/matumizi/export-report-pdf?start_date=${startDate}&end_date=${endDate}`;
}

function updateCharts(dailyData, categoryData) {
    // Destroy existing charts
    if (window.dailyChart) window.dailyChart.destroy();
    if (window.categoryChart) window.categoryChart.destroy();
    
    // Daily chart
    const dailyCtx = document.getElementById('dailyChart').getContext('2d');
    window.dailyChart = new Chart(dailyCtx, {
        type: 'bar',
        data: {
            labels: Object.keys(dailyData),
            datasets: [{
                label: 'Matumizi (TZS)',
                data: Object.values(dailyData),
                backgroundColor: '#10b981',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'top' } },
            scales: { y: { beginAtZero: true, ticks: { callback: v => v.toLocaleString() } } }
        }
    });
    
    // Category chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    window.categoryChart = new Chart(categoryCtx, {
        type: 'pie',
        data: {
            labels: Object.keys(categoryData),
            datasets: [{
                data: Object.values(categoryData),
                backgroundColor: ['#10b981', '#3b82f6', '#ef4444', '#f59e0b', '#8b5cf6', '#ec489a', '#06b6d4', '#84cc16']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'right' } }
        }
    });
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    window.matumiziManager = new MatumiziManager();
});
</script>
@endpush