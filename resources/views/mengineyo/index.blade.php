@extends('layouts.app')
@section('title', 'Mengineyo - Mapato na Benki')

@section('page-title', 'Mengineyo')
@section('page-subtitle', 'Mapato Mengine na Taarifa za Benki')

@section('content')
<div class="flex flex-col lg:flex-row gap-6" id="app-container">
    
    <!-- Notifications - Auto dismiss after 3 seconds -->
    <div id="notification-container" class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-sm px-4 pointer-events-none">
        @if(session('success'))
        <div id="success-notification" class="rounded border border-emerald-200 bg-emerald-50 px-5 py-4 text-base font-medium text-emerald-800 mb-3 shadow-sm animate-fade-in">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div id="error-notification" class="rounded border border-red-200 bg-red-50 px-5 py-4 text-base font-medium text-red-800 mb-3 shadow-sm animate-fade-in">
            <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
        </div>
        @endif
    </div>

    <!-- LEFT SIDEBAR TABS -->
    <div class="lg:w-80 flex-shrink-0">
        <!-- Mobile Toggle Button -->
        <div class="lg:hidden mb-4">
            <button onclick="toggleMobileSidebar()" class="w-full bg-white rounded-xl shadow-lg px-5 py-3 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-bars text-emerald-600 text-lg"></i>
                    <span class="font-semibold text-gray-700">
                        @if($tab === 'mapato')
                            Mapato Mengine
                        @else
                            Benki
                        @endif
                        - 
                        @if(request('action') === 'view')
                            Tazama
                        @else
                            Ingiza
                        @endif
                    </span>
                </div>
                <i class="fas fa-chevron-down text-gray-500 text-sm"></i>
            </button>
        </div>
        
        <!-- Sidebar Menu -->
        <div id="mobileSidebar" class="hidden lg:block">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden sticky top-4">
                <div class="bg-gradient-to-br from-emerald-600 to-emerald-700 px-5 py-4">
                    <h3 class="text-white font-bold text-lg">
                        <i class="fas fa-chart-line mr-2"></i> Menu
                    </h3>
                    <p class="text-emerald-100 text-sm mt-1">Chagua kategoria</p>
                </div>
                
                <div class="p-3 space-y-2">
                    <!-- Mapato Mengine Dropdown -->
                    <div x-data="{ open: {{ $tab === 'mapato' ? 'true' : 'false' }} }" class="rounded-lg overflow-hidden">
                        <button @click="open = !open" 
                                class="w-full flex items-center justify-between px-4 py-3 text-left transition-all duration-200 {{ $tab === 'mapato' ? 'bg-emerald-50 text-emerald-700 border-l-4 border-emerald-600' : 'hover:bg-gray-50 text-gray-700' }}">
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-plus-circle text-lg"></i>
                                <span class="font-semibold text-base">Mapato Mengine</span>
                            </div>
                            <i class="fas fa-chevron-down text-sm transition-transform" :class="{ 'rotate-180': open }"></i>
                        </button>
                        
                        <div x-show="open" x-transition.duration.200ms class="bg-gray-50 border-t">
                            <a href="{{ route('mengineyo.index', array_merge(request()->except('tab', 'action'), ['tab' => 'mapato', 'action' => 'ingiza'])) }}" 
                               class="flex items-center px-6 py-3 text-sm hover:bg-gray-100 transition-colors {{ request('action') === 'ingiza' || !request('action') ? 'bg-emerald-100 text-emerald-700' : 'text-gray-600' }}">
                                <i class="fas fa-pen-alt w-5 text-xs"></i>
                                <span class="ml-2">Ingiza Mapato</span>
                            </a>
                            <a href="{{ route('mengineyo.index', array_merge(request()->except('tab', 'action'), ['tab' => 'mapato', 'action' => 'view'])) }}" 
                               class="flex items-center px-6 py-3 text-sm hover:bg-gray-100 transition-colors {{ request('action') === 'view' ? 'bg-emerald-100 text-emerald-700' : 'text-gray-600' }}">
                                <i class="fas fa-table-list w-5 text-xs"></i>
                                <span class="ml-2">Tazama Mapato</span>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Benki Dropdown -->
                    <div x-data="{ open: {{ $tab === 'banking' ? 'true' : 'false' }} }" class="rounded-lg overflow-hidden">
                        <button @click="open = !open" 
                                class="w-full flex items-center justify-between px-4 py-3 text-left transition-all duration-200 {{ $tab === 'banking' ? 'bg-amber-50 text-amber-700 border-l-4 border-amber-500' : 'hover:bg-gray-50 text-gray-700' }}">
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-university text-lg"></i>
                                <span class="font-semibold text-base">Benki</span>
                            </div>
                            <i class="fas fa-chevron-down text-sm transition-transform" :class="{ 'rotate-180': open }"></i>
                        </button>
                        
                        <div x-show="open" x-transition.duration.200ms class="bg-gray-50 border-t">
                            <a href="{{ route('mengineyo.index', array_merge(request()->except('tab', 'action'), ['tab' => 'banking', 'action' => 'ingiza'])) }}" 
                               class="flex items-center px-6 py-3 text-sm hover:bg-gray-100 transition-colors {{ request('action') === 'ingiza' ? 'bg-amber-100 text-amber-700' : 'text-gray-600' }}">
                                <i class="fas fa-pen-alt w-5 text-xs"></i>
                                <span class="ml-2">Ingiza Uwekaji</span>
                            </a>
                            <a href="{{ route('mengineyo.index', array_merge(request()->except('tab', 'action'), ['tab' => 'banking', 'action' => 'view'])) }}" 
                               class="flex items-center px-6 py-3 text-sm hover:bg-gray-100 transition-colors {{ request('action') === 'view' ? 'bg-amber-100 text-amber-700' : 'text-gray-600' }}">
                                <i class="fas fa-table-list w-5 text-xs"></i>
                                <span class="ml-2">Tazama Uwekaji</span>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Sidebar Footer Stats -->
                <div class="border-t p-4 bg-gray-50 hidden lg:block">
                    <div class="text-xs text-gray-500 mb-2">Summaries</div>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Jumla ya Mapato:</span>
                            <span class="font-bold text-emerald-600">{{ number_format($totalMapato, 0) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Mapato Mengine:</span>
                            <span class="font-bold text-purple-600">{{ number_format($mapatoMengine, 0) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Jumla Kuu:</span>
                            <span class="font-bold text-blue-600">{{ number_format($jumlaKuu, 0) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Benki:</span>
                            <span class="font-bold text-amber-600">{{ number_format($totalBanked, 0) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT MAIN CONTENT -->
    <div class="flex-1 space-y-4 lg:space-y-6 overflow-x-hidden">
        
        <!-- COMPACT STATS CARDS - Keep original size -->
        <div class="grid grid-cols-4 gap-2 lg:gap-3">
            <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-lg p-2 lg:p-3 shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/80 text-[10px] lg:text-xs uppercase tracking-wider">Mapato</p>
                        <p class="text-white font-bold text-sm lg:text-lg">{{ number_format($totalMapato, 0) }}</p>
                    </div>
                    <div class="bg-white/20 rounded-lg p-1.5 lg:p-2">
                        <i class="fas fa-coins text-white text-xs lg:text-sm"></i>
                    </div>
                </div>
                <div class="text-white/50 text-[8px] lg:text-[10px] mt-1">
                    Mauzo + Madeni
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg p-2 lg:p-3 shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/80 text-[10px] lg:text-xs uppercase tracking-wider">Mengineyo</p>
                        <p class="text-white font-bold text-sm lg:text-lg">{{ number_format($mapatoMengine, 0) }}</p>
                    </div>
                    <div class="bg-white/20 rounded-lg p-1.5 lg:p-2">
                        <i class="fas fa-plus-circle text-white text-xs lg:text-sm"></i>
                    </div>
                </div>
                <div class="text-white/50 text-[8px] lg:text-[10px] mt-1">
                    +{{ $percentageFromMengineyo }}% ya mapato
                </div>
            </div>

            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-2 lg:p-3 shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/80 text-[10px] lg:text-xs uppercase tracking-wider">Jumla Kuu</p>
                        <p class="text-white font-bold text-sm lg:text-lg">{{ number_format($jumlaKuu, 0) }}</p>
                    </div>
                    <div class="bg-white/20 rounded-lg p-1.5 lg:p-2">
                        <i class="fas fa-chart-pie text-white text-xs lg:text-sm"></i>
                    </div>
                </div>
                <div class="text-white/50 text-[8px] lg:text-[10px] mt-1">
                    Mapato + Mengineyo
                </div>
            </div>

            <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-lg p-2 lg:p-3 shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/80 text-[10px] lg:text-xs uppercase tracking-wider">Benki</p>
                        <p class="text-white font-bold text-sm lg:text-lg">{{ number_format($totalBanked, 0) }}</p>
                    </div>
                    <div class="bg-white/20 rounded-lg p-1.5 lg:p-2">
                        <i class="fas fa-university text-white text-xs lg:text-sm"></i>
                    </div>
                </div>
                <div class="text-white/50 text-[8px] lg:text-[10px] mt-1">
                    {{ $percentageBanked }}% ya mauzo
                </div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white rounded-xl shadow p-3 lg:p-4">
            <form method="GET" action="{{ route('mengineyo.index') }}" class="flex flex-wrap gap-2 lg:gap-3">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <input type="hidden" name="action" value="{{ request('action') }}">
                
                <div class="flex-1 min-w-[140px] lg:min-w-[200px] relative">
                    <input type="text" name="search" placeholder="Tafuta..." 
                           value="{{ $search }}" 
                           class="w-full pl-9 lg:pl-10 pr-3 py-2 lg:py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-xs lg:text-sm"></i>
                </div>
                
                <input type="date" name="start_date" value="{{ $start_date }}" 
                       class="w-auto min-w-[120px] lg:w-40 px-2 lg:px-3 py-2 lg:py-2.5 border border-gray-300 rounded-lg text-sm">
                <input type="date" name="end_date" value="{{ $end_date }}" 
                       class="w-auto min-w-[120px] lg:w-40 px-2 lg:px-3 py-2 lg:py-2.5 border border-gray-300 rounded-lg text-sm">
                
                @if($tab === 'banking')
                <select name="bank_filter" class="w-auto min-w-[120px] lg:w-40 px-2 lg:px-3 py-2 lg:py-2.5 border border-gray-300 rounded-lg text-sm">
                    <option value="">Benki zote</option>
                    @foreach($banks as $b)
                        <option value="{{ $b }}" {{ $bank_filter == $b ? 'selected' : '' }}>{{ $b }}</option>
                    @endforeach
                </select>
                @endif
                
                <button type="submit" class="px-3 lg:px-5 py-2 lg:py-2.5 bg-emerald-600 text-white rounded-lg text-sm font-medium shadow-sm hover:bg-emerald-700">
                    <i class="fas fa-search mr-1 lg:mr-2"></i><span class="hidden sm:inline">Chuja</span>
                </button>
                <a href="{{ route('mengineyo.index', ['tab' => $tab, 'action' => request('action')]) }}" 
                   class="px-3 lg:px-5 py-2 lg:py-2.5 bg-gray-200 text-gray-800 rounded-lg text-sm font-medium text-center hover:bg-gray-300">
                    <i class="fas fa-redo mr-1 lg:mr-2"></i><span class="hidden sm:inline">Safisha</span>
                </a>
            </form>
        </div>

        <!-- CONTENT BASED ON TAB AND ACTION -->
        @if($tab === 'mapato')
            @if(request('action') === 'view')
                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 px-4 lg:px-6 py-3 lg:py-4">
                        <h3 class="text-white font-bold text-base lg:text-lg">
                            <i class="fas fa-table-list mr-2"></i> Orodha ya Mapato Mengine
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[500px] lg:min-w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 lg:px-5 py-2 lg:py-3 text-left text-xs font-semibold text-gray-600 uppercase">Tarehe</th>
                                    <th class="px-3 lg:px-5 py-2 lg:py-3 text-left text-xs font-semibold text-gray-600 uppercase">Chanzo</th>
                                    <th class="px-3 lg:px-5 py-2 lg:py-3 text-right text-xs font-semibold text-gray-600 uppercase">Kiasi</th>
                                    <th class="px-3 lg:px-5 py-2 lg:py-3 text-center text-xs font-semibold text-gray-600 uppercase">Vitendo</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($mengineyo as $item)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-3 lg:px-5 py-2 lg:py-3 text-xs lg:text-sm">{{ $item->tarehe->format('d/m/Y') }}</td>
                                    <td class="px-3 lg:px-5 py-2 lg:py-3">
                                        <span class="px-2 py-0.5 lg:px-2.5 lg:py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ Str::limit($item->chanzo, 15) }}
                                        </span>
                                    </td>
                                    <td class="px-3 lg:px-5 py-2 lg:py-3 text-right font-semibold text-emerald-600 text-xs lg:text-sm">{{ number_format($item->kiasi, 0) }}</td>
                                    <td class="px-3 lg:px-5 py-2 lg:py-3 text-center">
                                        <div class="flex justify-center space-x-1 lg:space-x-2">
                                            <button onclick="editMengineyo({{ $item->id }}, '{{ addslashes($item->chanzo) }}', '{{ $item->kiasi }}', '{{ addslashes($item->maelezo) }}', '{{ $item->tarehe }}')" 
                                                    class="text-emerald-600 hover:bg-emerald-50 p-1.5 lg:p-2 rounded-lg transition-colors">
                                                <i class="fas fa-edit text-xs lg:text-sm"></i>
                                            </button>
                                            <button onclick="deleteMengineyo({{ $item->id }}, '{{ addslashes($item->chanzo) }}')" 
                                                    class="text-red-600 hover:bg-red-50 p-1.5 lg:p-2 rounded-lg transition-colors">
                                                <i class="fas fa-trash text-xs lg:text-sm"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-3 lg:px-5 py-8 lg:py-12 text-center text-gray-500">
                                        <i class="fas fa-inbox text-2xl lg:text-4xl mb-2 lg:mb-3 block"></i>
                                        <p class="text-sm lg:text-base">Hakuna mapato mengine</p>
                                        <p class="text-xs lg:text-sm mt-1">Bonyeza "Ingiza Mapato" kuongeza mapato</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if($mengineyo->count() > 0)
                            <tfoot class="bg-gray-50 border-t">
                                <tr>
                                    <td colspan="2" class="px-3 lg:px-5 py-2 lg:py-3 text-right font-bold text-xs lg:text-sm">Jumla:</td>
                                    <td class="px-3 lg:px-5 py-2 lg:py-3 text-right font-bold text-emerald-700 text-sm lg:text-lg">{{ number_format($mengineyo->sum('kiasi'), 0) }}</td>
                                    <td class="px-3 lg:px-5 py-2 lg:py-3"></td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                    @if($mengineyo->hasPages())
                    <div class="px-3 lg:px-5 py-3 border-t">
                        {{ $mengineyo->appends(['tab' => 'mapato', 'action' => 'view'])->links() }}
                    </div>
                    @endif
                    <div class="px-3 lg:px-5 py-3 border-t bg-gray-50 flex justify-end">
                        <a href="{{ route('mengineyo.export.mapato.pdf', request()->query()) }}" 
                           target="_blank" class="px-3 lg:px-5 py-2 lg:py-2.5 bg-red-600 text-white rounded-lg text-xs lg:text-sm font-semibold hover:bg-red-700">
                            <i class="fas fa-file-pdf mr-1 lg:mr-2"></i> PDF
                        </a>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 px-4 lg:px-6 py-3 lg:py-4">
                        <h3 class="text-white font-bold text-base lg:text-lg">
                            <i class="fas fa-plus-circle mr-2"></i> Ongeza Mapato Mengine
                        </h3>
                    </div>
                    <div class="p-4 lg:p-6">
                        <form method="POST" action="{{ route('mengineyo.store.mapato') }}" class="space-y-4 lg:space-y-5">
                            @csrf
                            <div class="space-y-4 lg:space-y-5">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1 lg:mb-2">Chanzo cha Mapato *</label>
                                    <input type="text" name="chanzo" required placeholder="Mfano: Mchango, Ruzuku" 
                                           class="w-full px-3 lg:px-4 py-2 lg:py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1 lg:mb-2">Kiasi (TZS) *</label>
                                    <input type="number" step="0.01" name="kiasi" required placeholder="0.00" 
                                           class="w-full px-3 lg:px-4 py-2 lg:py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1 lg:mb-2">Tarehe *</label>
                                    <input type="date" name="tarehe" value="{{ date('Y-m-d') }}" required 
                                           class="w-full px-3 lg:px-4 py-2 lg:py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1 lg:mb-2">Maelezo (Optional)</label>
                                    <textarea name="maelezo" rows="3" placeholder="Maelezo ya ziada..." 
                                              class="w-full px-3 lg:px-4 py-2 lg:py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500"></textarea>
                                </div>
                            </div>
                            <div class="flex gap-2 lg:gap-3">
                                <button type="submit" class="flex-1 bg-emerald-600 text-white px-4 lg:px-6 py-2 lg:py-2.5 rounded-lg text-sm font-semibold hover:bg-emerald-700">
                                    <i class="fas fa-save mr-1 lg:mr-2"></i> Hifadhi
                                </button>
                                <button type="reset" class="px-4 lg:px-6 py-2 lg:py-2.5 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-undo mr-1 lg:mr-2"></i> Futa
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        @endif

        @if($tab === 'banking')
            @if(request('action') === 'view')
                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <div class="bg-gradient-to-r from-amber-600 to-amber-700 px-4 lg:px-6 py-3 lg:py-4">
                        <h3 class="text-white font-bold text-base lg:text-lg">
                            <i class="fas fa-table-list mr-2"></i> Orodha ya Uwekaji Benki
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[500px] lg:min-w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 lg:px-5 py-2 lg:py-3 text-left text-xs font-semibold text-gray-600 uppercase">Tarehe</th>
                                    <th class="px-3 lg:px-5 py-2 lg:py-3 text-left text-xs font-semibold text-gray-600 uppercase">Benki</th>
                                    <th class="px-3 lg:px-5 py-2 lg:py-3 text-right text-xs font-semibold text-gray-600 uppercase">Kiasi</th>
                                    <th class="px-3 lg:px-5 py-2 lg:py-3 text-center text-xs font-semibold text-gray-600 uppercase">Vitendo</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($banking as $item)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-3 lg:px-5 py-2 lg:py-3 text-xs lg:text-sm">{{ $item->tarehe->format('d/m/Y') }}</td>
                                    <td class="px-3 lg:px-5 py-2 lg:py-3">
                                        <span class="px-2 py-0.5 lg:px-2.5 lg:py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                            {{ $item->benki }}
                                        </span>
                                    </td>
                                    <td class="px-3 lg:px-5 py-2 lg:py-3 text-right font-semibold text-amber-600 text-xs lg:text-sm">{{ number_format($item->kiasi, 0) }}</td>
                                    <td class="px-3 lg:px-5 py-2 lg:py-3 text-center">
                                        <div class="flex justify-center space-x-1 lg:space-x-2">
                                            <button onclick="editBanking({{ $item->id }}, '{{ $item->benki }}', '{{ $item->kiasi }}', '{{ addslashes($item->maelezo) }}', '{{ $item->tarehe }}')" 
                                                    class="text-emerald-600 hover:bg-emerald-50 p-1.5 lg:p-2 rounded-lg">
                                                <i class="fas fa-edit text-xs lg:text-sm"></i>
                                            </button>
                                            <button onclick="deleteBanking({{ $item->id }}, '{{ $item->benki }}')" 
                                                    class="text-red-600 hover:bg-red-50 p-1.5 lg:p-2 rounded-lg">
                                                <i class="fas fa-trash text-xs lg:text-sm"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-3 lg:px-5 py-8 lg:py-12 text-center text-gray-500">
                                        <i class="fas fa-university text-2xl lg:text-4xl mb-2 lg:mb-3 block"></i>
                                        <p class="text-sm lg:text-base">Hakuna taarifa za benki</p>
                                        <p class="text-xs lg:text-sm mt-1">Bonyeza "Ingiza Uwekaji" kuongeza rekodi</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if($banking->count() > 0)
                            <tfoot class="bg-gray-50 border-t">
                                <tr>
                                    <td colspan="2" class="px-3 lg:px-5 py-2 lg:py-3 text-right font-bold text-xs lg:text-sm">Jumla:</td>
                                    <td class="px-3 lg:px-5 py-2 lg:py-3 text-right font-bold text-amber-700 text-sm lg:text-lg">{{ number_format($banking->sum('kiasi'), 0) }}</td>
                                    <td class="px-3 lg:px-5 py-2 lg:py-3"></td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                    @if($banking->hasPages())
                    <div class="px-3 lg:px-5 py-3 border-t">
                        {{ $banking->appends(['tab' => 'banking', 'action' => 'view'])->links() }}
                    </div>
                    @endif
                    <div class="px-3 lg:px-5 py-3 border-t bg-gray-50 flex justify-end">
                        <a href="{{ route('mengineyo.export.banking.pdf', request()->query()) }}" 
                           target="_blank" class="px-3 lg:px-5 py-2 lg:py-2.5 bg-red-600 text-white rounded-lg text-xs lg:text-sm font-semibold hover:bg-red-700">
                            <i class="fas fa-file-pdf mr-1 lg:mr-2"></i> PDF
                        </a>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <div class="bg-gradient-to-r from-amber-600 to-amber-700 px-4 lg:px-6 py-3 lg:py-4">
                        <h3 class="text-white font-bold text-base lg:text-lg">
                            <i class="fas fa-university mr-2"></i> Rekodi Uwekaji Benki
                        </h3>
                    </div>
                    <div class="p-4 lg:p-6">
                        <form method="POST" action="{{ route('mengineyo.store.banking') }}" class="space-y-4 lg:space-y-5">
                            @csrf
                            <div class="space-y-4 lg:space-y-5">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1 lg:mb-2">Benki *</label>
                                    <select name="benki" required class="w-full px-3 lg:px-4 py-2 lg:py-2.5 border border-gray-300 rounded-lg text-sm bg-white">
                                        <option value="">Chagua Benki</option>
                                        <option value="CRDB">CRDB</option>
                                        <option value="NMB">NMB</option>
                                        <option value="ABS">ABS</option>
                                        <option value="KCB">KCB</option>
                                        <option value="NBC">NBC</option>
                                        <option value="Other">Nyingine</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1 lg:mb-2">Kiasi (TZS) *</label>
                                    <input type="number" step="0.01" name="kiasi" required placeholder="0.00" 
                                           class="w-full px-3 lg:px-4 py-2 lg:py-2.5 border border-gray-300 rounded-lg text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1 lg:mb-2">Tarehe *</label>
                                    <input type="date" name="tarehe" value="{{ date('Y-m-d') }}" required 
                                           class="w-full px-3 lg:px-4 py-2 lg:py-2.5 border border-gray-300 rounded-lg text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1 lg:mb-2">Maelezo (Optional)</label>
                                    <textarea name="maelezo" rows="3" placeholder="Maelezo ya uwekaji benki..." 
                                              class="w-full px-3 lg:px-4 py-2 lg:py-2.5 border border-gray-300 rounded-lg text-sm"></textarea>
                                </div>
                            </div>
                            
                            <div class="bg-amber-50 p-3 lg:p-4 rounded-lg border border-amber-200">
                                <span class="text-amber-800 font-medium text-sm">💰 Mauzo ya kipindi hiki:</span>
                                <strong class="text-amber-900 ml-1 lg:ml-2 text-base lg:text-xl">{{ number_format($totalMauzo ?? 0, 0) }} TZS</strong>
                            </div>
                            
                            <div class="flex gap-2 lg:gap-3">
                                <button type="submit" class="flex-1 bg-amber-600 text-white px-4 lg:px-6 py-2 lg:py-2.5 rounded-lg text-sm font-semibold hover:bg-amber-700">
                                    <i class="fas fa-save mr-1 lg:mr-2"></i> Hifadhi
                                </button>
                                <button type="reset" class="px-4 lg:px-6 py-2 lg:py-2.5 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-undo mr-1 lg:mr-2"></i> Futa
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>

<!-- Edit Mengineyo Modal -->
<div id="edit-mengineyo-modal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-lg shadow-xl w-full max-w-md mx-auto z-50">
        <div class="p-3 lg:p-4 border-b">
            <h3 class="text-base lg:text-lg font-bold">Rekebisha Mapato Mengine</h3>
        </div>
        <form id="edit-mengineyo-form" method="POST">
            @csrf
            @method('PUT')
            <div class="p-4 lg:p-5 space-y-3 lg:space-y-4">
                <input type="text" name="chanzo" id="edit-chanzo" required placeholder="Chanzo" class="w-full px-3 lg:px-4 py-2 border border-gray-300 rounded-lg text-sm">
                <input type="number" step="0.01" name="kiasi" id="edit-kiasi" required placeholder="Kiasi" class="w-full px-3 lg:px-4 py-2 border border-gray-300 rounded-lg text-sm">
                <input type="date" name="tarehe" id="edit-tarehe" required class="w-full px-3 lg:px-4 py-2 border border-gray-300 rounded-lg text-sm">
                <textarea name="maelezo" id="edit-maelezo" rows="3" placeholder="Maelezo" class="w-full px-3 lg:px-4 py-2 border border-gray-300 rounded-lg text-sm"></textarea>
            </div>
            <div class="flex gap-2 lg:gap-3 p-4 lg:p-5 pt-0">
                <button type="button" onclick="closeModal('edit-mengineyo-modal')" class="flex-1 px-3 lg:px-4 py-2 border border-gray-300 rounded-lg text-sm bg-white hover:bg-gray-50">Ghairi</button>
                <button type="submit" class="flex-1 px-3 lg:px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-semibold hover:bg-emerald-700">Hifadhi</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Mengineyo Modal -->
<div id="delete-mengineyo-modal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-lg shadow-xl w-full max-w-sm mx-auto z-50">
        <div class="p-3 lg:p-4 border-b">
            <h3 class="text-base lg:text-lg font-bold">Futa Mapato Mengine</h3>
        </div>
        <form id="delete-mengineyo-form" method="POST">
            @csrf
            @method('DELETE')
            <div class="p-4 lg:p-5">
                <p class="text-sm text-gray-600 mb-4">Je, una uhakika unataka kufuta <strong id="delete-mengineyo-name"></strong>?</p>
                <div class="flex gap-2 lg:gap-3">
                    <button type="button" onclick="closeModal('delete-mengineyo-modal')" class="flex-1 px-3 lg:px-4 py-2 border border-gray-300 rounded-lg text-sm bg-white hover:bg-gray-50">Ghairi</button>
                    <button type="submit" class="flex-1 px-3 lg:px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-semibold hover:bg-red-700">Futa</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Banking Modal -->
<div id="edit-banking-modal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-lg shadow-xl w-full max-w-md mx-auto z-50">
        <div class="p-3 lg:p-4 border-b">
            <h3 class="text-base lg:text-lg font-bold">Rekebisha Uwekaji Benki</h3>
        </div>
        <form id="edit-banking-form" method="POST">
            @csrf
            @method('PUT')
            <div class="p-4 lg:p-5 space-y-3 lg:space-y-4">
                <select name="benki" id="edit-benki" required class="w-full px-3 lg:px-4 py-2 border border-gray-300 rounded-lg text-sm bg-white">
                    <option value="">Chagua Benki</option>
                    <option value="CRDB">CRDB</option>
                    <option value="NMB">NMB</option>
                    <option value="ABS">ABS</option>
                    <option value="KCB">KCB</option>
                    <option value="NBC">NBC</option>
                    <option value="Other">Nyingine</option>
                </select>
                <input type="number" step="0.01" name="kiasi" id="edit-banking-kiasi" required placeholder="Kiasi" class="w-full px-3 lg:px-4 py-2 border border-gray-300 rounded-lg text-sm">
                <input type="date" name="tarehe" id="edit-banking-tarehe" required class="w-full px-3 lg:px-4 py-2 border border-gray-300 rounded-lg text-sm">
                <textarea name="maelezo" id="edit-banking-maelezo" rows="3" placeholder="Maelezo" class="w-full px-3 lg:px-4 py-2 border border-gray-300 rounded-lg text-sm"></textarea>
            </div>
            <div class="flex gap-2 lg:gap-3 p-4 lg:p-5 pt-0">
                <button type="button" onclick="closeModal('edit-banking-modal')" class="flex-1 px-3 lg:px-4 py-2 border border-gray-300 rounded-lg text-sm bg-white hover:bg-gray-50">Ghairi</button>
                <button type="submit" class="flex-1 px-3 lg:px-4 py-2 bg-amber-600 text-white rounded-lg text-sm font-semibold hover:bg-amber-700">Hifadhi</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Banking Modal -->
<div id="delete-banking-modal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-lg shadow-xl w-full max-w-sm mx-auto z-50">
        <div class="p-3 lg:p-4 border-b">
            <h3 class="text-base lg:text-lg font-bold">Futa Uwekaji Benki</h3>
        </div>
        <form id="delete-banking-form" method="POST">
            @csrf
            @method('DELETE')
            <div class="p-4 lg:p-5">
                <p class="text-sm text-gray-600 mb-4">Je, una uhakika unataka kufuta uwekaji wa <strong id="delete-banking-name"></strong>?</p>
                <div class="flex gap-2 lg:gap-3">
                    <button type="button" onclick="closeModal('delete-banking-modal')" class="flex-1 px-3 lg:px-4 py-2 border border-gray-300 rounded-lg text-sm bg-white hover:bg-gray-50">Ghairi</button>
                    <button type="submit" class="flex-1 px-3 lg:px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-semibold hover:bg-red-700">Futa</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@push('styles')
<style>
.modal { display: flex; align-items: center; justify-content: center; }
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in { animation: fadeIn 0.3s ease-out; }

@media (max-width: 1023px) {
    input, select, textarea, button {
        font-size: 16px !important;
    }
}

::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}
::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}
::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
}
::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.min.js" defer></script>
<script>
// Auto dismiss notifications after 3 seconds
document.addEventListener('DOMContentLoaded', function() {
    const successNotification = document.getElementById('success-notification');
    const errorNotification = document.getElementById('error-notification');
    
    if (successNotification) {
        setTimeout(() => {
            successNotification.style.opacity = '0';
            successNotification.style.transition = 'opacity 0.5s ease';
            setTimeout(() => {
                if (successNotification && successNotification.remove) {
                    successNotification.remove();
                }
            }, 500);
        }, 3000);
    }
    
    if (errorNotification) {
        setTimeout(() => {
            errorNotification.style.opacity = '0';
            errorNotification.style.transition = 'opacity 0.5s ease';
            setTimeout(() => {
                if (errorNotification && errorNotification.remove) {
                    errorNotification.remove();
                }
            }, 500);
        }, 3000);
    }
});

function toggleMobileSidebar() {
    const sidebar = document.getElementById('mobileSidebar');
    sidebar.classList.toggle('hidden');
}

document.querySelectorAll('#mobileSidebar a').forEach(link => {
    link.addEventListener('click', () => {
        if (window.innerWidth < 1024) {
            document.getElementById('mobileSidebar').classList.add('hidden');
        }
    });
});

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

function editMengineyo(id, chanzo, kiasi, maelezo, tarehe) {
    const form = document.getElementById('edit-mengineyo-form');
    form.action = `/mengineyo/update/${id}`;
    document.getElementById('edit-chanzo').value = chanzo;
    document.getElementById('edit-kiasi').value = kiasi;
    document.getElementById('edit-maelezo').value = maelezo || '';
    document.getElementById('edit-tarehe').value = tarehe;
    document.getElementById('edit-mengineyo-modal').classList.remove('hidden');
}

function deleteMengineyo(id, name) {
    const form = document.getElementById('delete-mengineyo-form');
    form.action = `/mengineyo/delete/${id}`;
    document.getElementById('delete-mengineyo-name').textContent = name;
    document.getElementById('delete-mengineyo-modal').classList.remove('hidden');
}

function editBanking(id, benki, kiasi, maelezo, tarehe) {
    const form = document.getElementById('edit-banking-form');
    form.action = `/mengineyo/banking/update/${id}`;
    document.getElementById('edit-benki').value = benki;
    document.getElementById('edit-banking-kiasi').value = kiasi;
    document.getElementById('edit-banking-maelezo').value = maelezo || '';
    document.getElementById('edit-banking-tarehe').value = tarehe;
    document.getElementById('edit-banking-modal').classList.remove('hidden');
}

function deleteBanking(id, name) {
    const form = document.getElementById('delete-banking-form');
    form.action = `/mengineyo/banking/delete/${id}`;
    document.getElementById('delete-banking-name').textContent = name;
    document.getElementById('delete-banking-modal').classList.remove('hidden');
}

document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function() {
        this.closest('.modal').classList.add('hidden');
    });
});
</script>
@endpush