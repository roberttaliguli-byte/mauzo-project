@extends('layouts.app')

@section('title', 'Madeni')

@section('page-title', 'Madeni')
@section('page-subtitle', now()->format('d/m/Y'))

@section('content')
<div class="space-y-4">
    <!-- Top Centered Notifications -->
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
        @if($errors->any())
            @foreach($errors->all() as $error)
            <div class="rounded border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800 mb-2 shadow-sm">
                {{ $error }}
            </div>
            @endforeach
        @endif
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <a href="{{ route('madeni.index') }}" class="block bg-white p-3 rounded-lg border border-emerald-200 shadow-sm hover:bg-emerald-50 transition-colors">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Jumla ya Madeni</p>
                    <p class="text-xl font-bold text-emerald-700">TZS {{ number_format($totalDebts, 2) }}</p>
                </div>
                <i class="fas fa-money-bill-wave text-emerald-500 text-lg"></i>
            </div>
        </a>
        
        <a href="{{ route('madeni.index') }}?filter=active" class="block bg-white p-3 rounded-lg border border-red-200 shadow-sm hover:bg-red-50 transition-colors">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Madeni Yanayongoza</p>
                    <p class="text-xl font-bold text-red-700">{{ $activeDebts }}</p>
                </div>
                <i class="fas fa-hand-holding-usd text-red-500 text-lg"></i>
            </div>
        </a>
        
        <a href="{{ route('madeni.index') }}?filter=paid" class="block bg-white p-3 rounded-lg border border-green-200 shadow-sm hover:bg-green-50 transition-colors">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Yaliyolipwa</p>
                    <p class="text-xl font-bold text-green-700">{{ $paidDebts }}</p>
                </div>
                <i class="fas fa-check-circle text-green-500 text-lg"></i>
            </div>
        </a>
        
        <a href="{{ route('madeni.index') }}" class="block bg-white p-3 rounded-lg border border-blue-200 shadow-sm hover:bg-blue-50 transition-colors">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Wakopaji</p>
                    <p class="text-xl font-bold text-blue-700">{{ $totalBorrowers }}</p>
                </div>
                <i class="fas fa-users text-blue-500 text-lg"></i>
            </div>
        </a>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm">
        <div class="flex">
            <button data-tab="madeni" class="tab-button flex-1 py-3 px-4 text-sm font-medium border-r border-gray-200 bg-emerald-50 text-emerald-700">
                <i class="fas fa-list mr-2"></i> Orodha
            </button>
            <button data-tab="marejesho" class="tab-button flex-1 py-3 px-4 text-sm font-medium border-r border-gray-200 text-gray-600 hover:bg-gray-50">
                <i class="fas fa-history mr-2"></i> Marejesho
            </button>
            <button data-tab="ingiza" class="tab-button flex-1 py-3 px-4 text-sm font-medium text-gray-600 hover:bg-gray-50">
                <i class="fas fa-plus mr-2"></i> Ingiza
            </button>
        </div>
    </div>

    <!-- TAB 1: Orodha ya Madeni -->
    <div id="madeni-tab-content" class="tab-content space-y-3">
        <!-- Search Bar -->
        <div class="bg-white p-3 rounded-lg border border-gray-200 shadow-sm">
            <div class="flex flex-col sm:flex-row gap-2 sm:items-center">
                <div class="flex-1 relative">
                    <input 
                        type="text" 
                        id="search-input"
                        placeholder="Tafuta mkopaji, bidhaa, simu..." 
                        class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500"
                        value="{{ request()->search }}"
                    >
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                <div class="flex gap-2">
                    <button onclick="printDebts()" class="px-3 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 text-sm font-medium">
                        <i class="fas fa-print mr-1"></i> Print
                    </button>
                    <a href="{{ route('madeni.export') }}" class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm font-medium">
                        <i class="fas fa-file-excel mr-1"></i> Excel
                    </a>
                </div>
            </div>
        </div>

        <!-- Debts Table -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-emerald-50">
                            <th class="px-4 py-2 text-left font-medium text-emerald-800">Tarehe</th>
                            <th class="px-4 py-2 text-left font-medium text-emerald-800">Mkopaji</th>
                            <th class="px-4 py-2 text-left font-medium text-emerald-800 hidden md:table-cell">Bidhaa</th>
                            <th class="px-4 py-2 text-center font-medium text-emerald-800">Idadi</th>
                            <th class="px-4 py-2 text-right font-medium text-emerald-800">Deni</th>
                            <th class="px-4 py-2 text-right font-medium text-emerald-800">Baki</th>
                            <th class="px-4 py-2 text-center font-medium text-emerald-800 print:hidden">Vitendo</th>
                        </tr>
                    </thead>
                    <tbody id="debts-tbody" class="divide-y divide-gray-100">
                        @forelse($madeni as $deni)
                            <tr class="debt-row hover:bg-gray-50" data-debt='@json($deni)'>
                                <td class="px-4 py-2">
                                    <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($deni->created_at)->format('d/m/Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($deni->tarehe_malipo)->format('d/m/Y') }}</div>
                                </td>
                                <td class="px-4 py-2">
                                    <div class="font-medium text-gray-900 text-sm">{{ $deni->jina_mkopaji }}</div>
                                    @if($deni->simu)
                                    <div class="text-xs text-emerald-600">{{ $deni->simu }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-2 hidden md:table-cell">
                                    <span class="text-sm text-gray-700">{{ $deni->bidhaa->jina ?? 'N/A' }}</span>
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $deni->idadi }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-right">
                                    <div class="text-sm font-bold text-gray-900">{{ number_format($deni->jumla, 2) }}</div>
                                </td>
                                <td class="px-4 py-2 text-right">
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-bold 
                                        @if($deni->baki <= 0) bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ number_format($deni->baki, 2) }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-center print:hidden">
                                    <div class="flex justify-center space-x-2">
                                        @if($deni->baki > 0)
                                        <button class="pay-debt-btn text-green-600 hover:text-green-800"
                                                data-id="{{ $deni->id }}" title="Lipa">
                                            <i class="fas fa-money-bill-wave"></i>
                                        </button>
                                        @endif
                                        <button class="edit-debt-btn text-amber-600 hover:text-amber-800"
                                                data-id="{{ $deni->id }}" title="Badili">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="delete-debt-btn text-red-600 hover:text-red-800"
                                                data-id="{{ $deni->id }}" data-name="{{ $deni->jina_mkopaji }}" title="Futa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                    <i class="fas fa-hand-holding-usd text-3xl mb-2 text-gray-300"></i>
                                    <p>Hakuna madeni bado</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($madeni->hasPages())
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $madeni->links() }}
            </div>
            @endif
        </div>

        <!-- Clear Filter Button -->
        @if(request('filter'))
        <div class="text-center">
            <a href="{{ route('madeni.index') }}" class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-700 rounded text-sm hover:bg-gray-200">
                <i class="fas fa-times mr-1"></i> Ondoa Filter
            </a>
        </div>
        @endif
    </div>

    <!-- TAB 2: Historia ya Marejesho -->
    <div id="marejesho-tab-content" class="tab-content hidden">
        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-emerald-50">
                            <th class="px-4 py-2 text-left font-medium text-emerald-800">Tarehe</th>
                            <th class="px-4 py-2 text-left font-medium text-emerald-800">Mkopaji</th>
                            <th class="px-4 py-2 text-left font-medium text-emerald-800">Bidhaa</th>
                            <th class="px-4 py-2 text-center font-medium text-emerald-800">Idadi</th>
                            <th class="px-4 py-2 text-right font-medium text-emerald-800">Rejesho</th>
                            <th class="px-4 py-2 text-right font-medium text-emerald-800">Baki</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($marejesho as $rejesho)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2">
                                    <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($rejesho->tarehe)->format('d/m/Y') }}</div>
                                </td>
                                <td class="px-4 py-2">
                                    <div class="font-medium text-gray-900 text-sm">{{ $rejesho->madeni->jina_mkopaji }}</div>
                                    @if($rejesho->madeni->simu)
                                    <div class="text-xs text-emerald-600">{{ $rejesho->madeni->simu }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-2">
                                    <span class="text-sm text-gray-700">{{ $rejesho->madeni->bidhaa->jina ?? 'N/A' }}</span>
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <span class="text-sm">{{ $rejesho->madeni->idadi }}</span>
                                </td>
                                <td class="px-4 py-2 text-right">
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">
                                        {{ number_format($rejesho->kiasi, 2) }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-right">
                                    <span class="text-sm font-bold 
                                        @if($rejesho->madeni->baki <= 0) text-green-700
                                        @else text-red-700 @endif">
                                        {{ number_format($rejesho->madeni->baki, 2) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                    <i class="fas fa-history text-3xl mb-2 text-gray-300"></i>
                                    <p>Hakuna historia ya marejesho bado</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($marejesho->hasPages())
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $marejesho->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- TAB 3: Ingiza Deni -->
    <div id="ingiza-tab-content" class="tab-content hidden">
        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
            <form method="POST" action="{{ route('madeni.store') }}" id="debt-form" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Jina la Mkopaji *</label>
                        <input type="text" name="jina_mkopaji" 
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                               placeholder="Ingiza jina la mkopaji" required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Namba ya Simu *</label>
                        <input type="text" name="simu" 
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                               placeholder="+255 xxx xxx xxx" required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Bidhaa *</label>
                        <select name="bidhaa_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                                required>
                            <option value="">Chagua bidhaa...</option>
                            @foreach($bidhaa as $product)
                                <option value="{{ $product->id }}">{{ $product->jina }} (Stock: {{ $product->idadi }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Idadi *</label>
                        <input type="number" name="idadi" min="1"
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                               placeholder="Idadi" required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Bei (TZS) *</label>
                        <input type="number" step="0.01" name="bei"
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                               placeholder="0.00" required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Jumla (TZS) *</label>
                        <input type="number" step="0.01" name="jumla"
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                               placeholder="0.00" required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Tarehe ya Malipo *</label>
                        <input type="date" name="tarehe_malipo" 
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                               required>
                    </div>
                </div>
                <div class="flex gap-2 pt-4 border-t border-gray-200">
                    <button type="submit" 
                            class="flex-1 bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700 text-sm font-medium">
                        <i class="fas fa-save mr-1"></i> Hifadhi
                    </button>
                    <button type="reset" 
                            class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 text-sm">
                        <i class="fas fa-redo mr-1"></i> Safisha
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Pay Modal -->
<div id="pay-modal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-lg shadow-lg w-full max-w-md mx-auto z-50">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-800">Lipa Deni</h3>
        </div>
        <form id="pay-form" method="POST" class="p-4">
            @csrf
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Mkopaji</label>
                    <p id="pay-borrower-name" class="text-sm font-medium text-gray-900"></p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Bidhaa</label>
                    <p id="pay-product-name" class="text-sm text-gray-700"></p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Baki Lililobaki</label>
                    <p id="pay-remaining-balance" class="text-sm font-bold text-red-600"></p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Kiasi cha Kulipa *</label>
                    <input type="number" step="0.01" name="kiasi" id="pay-amount"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                           required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tarehe ya Malipo *</label>
                    <input type="date" name="tarehe" id="pay-date"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                           required>
                </div>
            </div>
            <div class="flex gap-2 pt-4 border-t border-gray-200 mt-4">
                <button type="button" id="close-pay-modal"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 text-sm">
                    Ghairi
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 text-sm font-medium">
                    <i class="fas fa-money-bill-wave mr-1"></i> Thibitisha
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="edit-modal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-lg shadow-lg w-full max-w-md mx-auto z-50 max-h-[90vh] overflow-y-auto">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-800">Badilisha Deni</h3>
        </div>
        <form id="edit-form" method="POST" class="p-4">
            @csrf
            @method('PUT')
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Jina la Mkopaji *</label>
                    <input type="text" name="jina_mkopaji" id="edit-jina-mkopaji"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                           required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Namba ya Simu</label>
                    <input type="text" name="simu" id="edit-simu"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Bidhaa *</label>
                    <select name="bidhaa_id" id="edit-bidhaa-id"
                            class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                            required>
                        @foreach($bidhaa as $product)
                            <option value="{{ $product->id }}">{{ $product->jina }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Idadi *</label>
                    <input type="number" name="idadi" id="edit-idadi" min="1"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                           required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Bei (TZS) *</label>
                    <input type="number" step="0.01" name="bei" id="edit-bei"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                           required>
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
                <p class="text-gray-700 text-sm mb-1">Una uhakika unataka kufuta deni?</p>
                <p class="text-gray-900 font-medium" id="delete-debt-name"></p>
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
/* Tab active state */
.tab-button.active {
    background-color: #f0fdf4;
    color: #059669;
    font-weight: 600;
}

.tab-button:not(.active) {
    background-color: transparent;
    color: #6b7280;
}

/* Notification auto-dismiss animation */
.notification-auto-dismiss {
    animation: fadeIn 0.3s ease-in;
    transition: opacity 0.5s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Modal animations */
.modal {
    transition: opacity 0.3s ease;
}

/* Hide elements when printing */
@media print {
    .print\:hidden {
        display: none !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
class MadeniManager {
    constructor() {
        this.currentTab = this.getSavedTab() || 'madeni';
        this.searchTimeout = null;
        this.init();
    }

    init() {
        this.bindEvents();
        this.showTab(this.currentTab);
        this.setupAjaxForms();
        this.setTodayDate();
        this.autoDismissNotifications();
    }

    getSavedTab() {
        return sessionStorage.getItem('madeni_tab') || 'madeni';
    }

    saveTab(tab) {
        sessionStorage.setItem('madeni_tab', tab);
    }

    bindEvents() {
        // Tab navigation
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', (e) => {
                const tab = e.target.closest('.tab-button').dataset.tab;
                this.showTab(tab);
                this.saveTab(tab);
            });
        });

        // Search with debounce
        const searchInput = document.getElementById('search-input');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                clearTimeout(this.searchTimeout);
                this.searchTimeout = setTimeout(() => {
                    this.filterDebts(e.target.value.toLowerCase().trim());
                }, 300);
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
                button.classList.add('bg-emerald-50', 'text-emerald-700');
                button.classList.remove('text-gray-600', 'hover:bg-gray-50');
            } else {
                button.classList.remove('bg-emerald-50', 'text-emerald-700');
                button.classList.add('text-gray-600', 'hover:bg-gray-50');
            }
        });

        // Update tab content
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        
        document.getElementById(`${tabName}-tab-content`).classList.remove('hidden');
        this.currentTab = tabName;
    }

    bindDebtActions() {
        // Pay buttons
        document.querySelectorAll('.pay-debt-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                const row = e.target.closest('.debt-row');
                const debt = JSON.parse(row.dataset.debt);
                this.openPayModal(debt);
            });
        });

        // Edit buttons
        document.querySelectorAll('.edit-debt-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                const row = e.target.closest('.debt-row');
                const debt = JSON.parse(row.dataset.debt);
                this.openEditModal(debt);
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

        if (closePayBtn) {
            closePayBtn.addEventListener('click', () => payModal.classList.add('hidden'));
        }
        
        if (payModal) {
            payModal.addEventListener('click', (e) => {
                if (e.target === payModal || e.target.classList.contains('modal-overlay')) {
                    payModal.classList.add('hidden');
                }
            });
        }

        // Edit modal
        const editModal = document.getElementById('edit-modal');
        const closeEditBtn = document.getElementById('close-edit-modal');

        if (closeEditBtn) {
            closeEditBtn.addEventListener('click', () => editModal.classList.add('hidden'));
        }
        
        if (editModal) {
            editModal.addEventListener('click', (e) => {
                if (e.target === editModal || e.target.classList.contains('modal-overlay')) {
                    editModal.classList.add('hidden');
                }
            });
        }

        // Delete modal
        const deleteModal = document.getElementById('delete-modal');
        const cancelDeleteBtn = document.getElementById('cancel-delete');

        if (cancelDeleteBtn) {
            cancelDeleteBtn.addEventListener('click', () => deleteModal.classList.add('hidden'));
        }
        
        if (deleteModal) {
            deleteModal.addEventListener('click', (e) => {
                if (e.target === deleteModal || e.target.classList.contains('modal-overlay')) {
                    deleteModal.classList.add('hidden');
                }
            });
        }

        // Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                if (payModal) payModal.classList.add('hidden');
                if (editModal) editModal.classList.add('hidden');
                if (deleteModal) deleteModal.classList.add('hidden');
            }
        });
    }

    filterDebts(searchTerm) {
        const rows = document.querySelectorAll('.debt-row');
        let found = false;
        
        rows.forEach(row => {
            const debt = JSON.parse(row.dataset.debt);
            const searchText = `
                ${debt.jina_mkopaji || ''}
                ${debt.simu || ''}
                ${debt.bidhaa?.jina || ''}
            `.toLowerCase();
            
            if (searchText.includes(searchTerm) || !searchTerm) {
                row.classList.remove('hidden');
                found = true;
            } else {
                row.classList.add('hidden');
            }
        });

        if (!found && searchTerm) {
            this.showNotification('Hakuna madeni yanayolingana', 'info');
        }
    }

    openPayModal(debt) {
        // Populate pay modal
        document.getElementById('pay-borrower-name').textContent = debt.jina_mkopaji;
        document.getElementById('pay-product-name').textContent = debt.bidhaa?.jina || 'N/A';
        document.getElementById('pay-remaining-balance').textContent = `TZS ${parseFloat(debt.baki).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
        
        // Set form action and max amount
        document.getElementById('pay-form').action = `/madeni/${debt.id}/rejesha`;
        document.getElementById('pay-amount').value = parseFloat(debt.baki);
        document.getElementById('pay-amount').max = parseFloat(debt.baki);
        document.getElementById('pay-amount').min = 0.01;
        
        const payModal = document.getElementById('pay-modal');
        if (payModal) payModal.classList.remove('hidden');
    }

    openEditModal(debt) {
        const editForm = document.getElementById('edit-form');
        if (!editForm) return;

        document.getElementById('edit-jina-mkopaji').value = debt.jina_mkopaji;
        document.getElementById('edit-simu').value = debt.simu || '';
        document.getElementById('edit-bidhaa-id').value = debt.bidhaa_id;
        document.getElementById('edit-idadi').value = debt.idadi;
        document.getElementById('edit-bei').value = debt.bei;
        editForm.action = `/madeni/${debt.id}`;
        
        const editModal = document.getElementById('edit-modal');
        if (editModal) editModal.classList.remove('hidden');
    }

    openDeleteModal(debtId, debtName) {
        const deleteForm = document.getElementById('delete-form');
        const deleteModal = document.getElementById('delete-modal');
        const deleteDebtName = document.getElementById('delete-debt-name');
        
        if (!deleteForm || !deleteModal || !deleteDebtName) return;
        
        deleteDebtName.textContent = debtName;
        deleteForm.action = `/madeni/${debtId}`;
        deleteModal.classList.remove('hidden');
    }

    setupAjaxForms() {
        // Debt form
        const debtForm = document.getElementById('debt-form');
        if (debtForm) {
            debtForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.submitForm(debtForm, 'Deni limehifadhiwa!');
            });
        }

        // Pay form
        const payForm = document.getElementById('pay-form');
        if (payForm) {
            payForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                
                const amountInput = document.getElementById('pay-amount');
                const maxAmount = parseFloat(amountInput.max);
                const amount = parseFloat(amountInput.value);
                
                if (amount > maxAmount) {
                    this.showNotification(`Kiasi kimezidi baki (max: ${maxAmount.toLocaleString()})`, 'error');
                    return;
                }
                
                await this.submitForm(payForm, 'Rejesho limehifadhiwa!');
                document.getElementById('pay-modal').classList.add('hidden');
            });
        }

        // Edit form
        const editForm = document.getElementById('edit-form');
        if (editForm) {
            editForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.submitForm(editForm, 'Deni limebadilishwa!');
                document.getElementById('edit-modal').classList.add('hidden');
            });
        }

        // Delete form
        const deleteForm = document.getElementById('delete-form');
        if (deleteForm) {
            deleteForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.submitForm(deleteForm, 'Deni limefutwa!');
                document.getElementById('delete-modal').classList.add('hidden');
            });
        }
    }

    async submitForm(form, successMessage = 'Operesheni imekamilika!') {
        const formData = new FormData(form);
        const submitButton = form.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        
        try {
            // Disable submit button
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Inatumwa...';
            
            const response = await fetch(form.action, {
                method: form.method,
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();

            if (response.ok) {
                const message = data.message || successMessage;
                this.showNotification(message, 'success');
                
                // Don't reload for specific operations
                if (!form.id.includes('pay-form') && !form.id.includes('edit-form') && !form.id.includes('delete-form')) {
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    setTimeout(() => window.location.reload(), 500);
                }
            } else {
                const error = data.errors ? Object.values(data.errors)[0][0] : data.message;
                this.showNotification(error || 'Hitilafu imetokea', 'error');
            }
        } catch (error) {
            this.showNotification('Hitilafu ya mtandao', 'error');
        } finally {
            // Re-enable submit button
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
        }
    }

    setTodayDate() {
        const today = new Date().toISOString().split('T')[0];
        const payDate = document.getElementById('pay-date');
        if (payDate) {
            payDate.value = today;
        }
        
        // Set default payment date to today
        const tareheMalipo = document.querySelector('input[name="tarehe_malipo"]');
        if (tareheMalipo && !tareheMalipo.value) {
            tareheMalipo.value = today;
        }
    }

    autoDismissNotifications() {
        document.querySelectorAll('.notification-auto-dismiss').forEach(notification => {
            const dismissTime = 3000; // 3 seconds
            
            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => {
                    notification.remove();
                }, 500);
            }, dismissTime);
        });
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
        notification.className = `rounded border px-4 py-3 text-sm font-medium mb-2 ${colors[type]} shadow-sm animate-fade-in notification-auto-dismiss`;
        notification.textContent = message;

        container.appendChild(notification);

        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateY(-10px) translateX(-50%)';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
}

// Print function
function printDebts() {
    const printWindow = window.open('', '_blank');
    const rows = document.querySelectorAll('.debt-row');
    
    let tableRows = '';
    rows.forEach(row => {
        const debt = JSON.parse(row.dataset.debt);
        tableRows += `
            <tr>
                <td style="border: 1px solid #ddd; padding: 8px;">${new Date(debt.created_at).toLocaleDateString()}</td>
                <td style="border: 1px solid #ddd; padding: 8px;">${debt.jina_mkopaji}</td>
                <td style="border: 1px solid #ddd; padding: 8px;">${debt.bidhaa?.jina || 'N/A'}</td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">${debt.idadi}</td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">${parseFloat(debt.jumla).toLocaleString()}</td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">${parseFloat(debt.baki).toLocaleString()}</td>
            </tr>`;
    });
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Orodha ya Madeni - ${new Date().toLocaleDateString()}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; }
                th { background-color: #f3f4f6; font-weight: bold; }
                .header { text-align: center; margin-bottom: 30px; }
                .header h2 { margin: 0; color: #047857; }
                .header p { margin: 5px 0 0 0; color: #6b7280; }
                @media print {
                    body { margin: 0; }
                    .no-print { display: none; }
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>Orodha ya Madeni</h2>
                <p>${new Date().toLocaleDateString()}</p>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Tarehe</th>
                        <th>Mkopaji</th>
                        <th>Bidhaa</th>
                        <th>Idadi</th>
                        <th>Deni</th>
                        <th>Baki</th>
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

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    window.madeniManager = new MadeniManager();
    
    // Save tab state
    window.addEventListener('beforeunload', () => {
        if (window.madeniManager) {
            window.madeniManager.saveTab(window.madeniManager.currentTab);
        }
    });
});
</script>
@endpush