@extends('layouts.app')

@section('title', 'Matumizi - DEMODAY')
@section('page-title', 'Matumizi')
@section('page-subtitle', 'Usimamizi wa matumizi yote - ' . now()->format('d/m/Y'))

@section('content')
<div x-data="matumiziApp()" class="space-y-6">
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
                @click="activeTab = 'taarifa'" 
                :class="activeTab === 'taarifa' ? 'border-b-2 border-green-500 text-green-600 font-semibold' : 'text-gray-500 hover:text-gray-700'" 
                class="pb-3 px-1 transition-colors flex items-center"
            >
                <i class="fas fa-table mr-2"></i>Taarifa za Matumizi
            </button>
            <button 
                @click="activeTab = 'ingiza'" 
                :class="activeTab === 'ingiza' ? 'border-b-2 border-green-500 text-green-600 font-semibold' : 'text-gray-500 hover:text-gray-700'" 
                class="pb-3 px-1 transition-colors flex items-center"
            >
                <i class="fas fa-plus-circle mr-2"></i>Ingiza Matumizi Mpya
            </button>
        </div>
    </div>

<!-- Matumizi Information Tab -->
<div x-show="activeTab === 'taarifa'" class="space-y-6">
    <!-- Search and Actions -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 card-hover">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-800">Orodha ya Matumizi</h2>
            <div class="flex space-x-3">
                <div class="relative">
                    <input 
                        type="text" 
                        placeholder="Tafuta matumizi..." 
                        x-model="searchQuery"
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
                <tbody class="divide-y divide-gray-100">
                    @forelse($matumizi as $item)
                        <tr class="hover:bg-green-50 transition-all duration-200 
                            @if($item->created_at->format('Y-m-d') === now()->format('Y-m-d')) bg-gradient-to-r from-amber-50 to-yellow-50 border-l-4 border-amber-400 @else bg-white @endif"
                            x-show="searchQuery === '' || 
                                   '{{ strtolower($item->aina) }}'.includes(searchQuery.toLowerCase()) || 
                                   '{{ strtolower($item->maelezo) }}'.includes(searchQuery.toLowerCase())">
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-800">{{ $item->created_at->format('d/m/Y') }}</div>
                                <div class="text-xs text-green-600 font-medium">{{ $item->created_at->format('H:i') }}</div>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold 
                                    @if($item->aina === 'Mshahara') bg-green-100 text-green-800 border border-green-200
                                    @elseif($item->aina === 'Bank') bg-emerald-100 text-emerald-800 border border-emerald-200
                                    @elseif($item->aina === 'Kodi TRA') bg-teal-100 text-teal-800 border border-teal-200
                                    @elseif($item->aina === 'Kodi Pango') bg-lime-100 text-lime-800 border border-lime-200
                                    @else bg-green-50 text-green-700 border border-green-100 @endif">
                                    {{ $item->aina }}
                                </span>
                            </td>
                            
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-700">{{ $item->maelezo ?: '--' }}</div>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="text-sm font-bold text-green-700">{{ number_format($item->gharama, 2) }}</div>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium print:hidden">
                                <div class="flex justify-center space-x-3">
                                    <button 
                                        @click="editItem({{ $item->toJson() }})" 
                                        class="text-amber-600 hover:text-amber-800 transition-colors transform hover:scale-110"
                                        title="Badili"
                                    >
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button 
                                        @click="deleteItem({{ $item->id }}, '{{ $item->aina }}')" 
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
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-receipt text-5xl text-green-300 mb-4"></i>
                                    <p class="text-lg font-semibold text-gray-600 mb-2">Hakuna matumizi bado.</p>
                                    <p class="text-sm text-gray-500 mb-4">Anza kwa kuongeza matumizi yako ya kwanza</p>
                                    <button 
                                        @click="activeTab = 'ingiza'" 
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
    <!-- Ingiza Matumizi Tab -->
    <div x-show="activeTab === 'ingiza'" class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 card-hover">
        <h2 class="text-xl font-bold text-gray-800 mb-6">Ingiza Matumizi Mpya</h2>
        <form method="POST" action="{{ route('matumizi.store') }}" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Aina ya Matumizi</label>
                    <select name="aina" x-model="selectedAina" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" required>
                        <option value="">-- Chagua Aina ya Matumizi --</option>
                        <option value="Bank">Bank</option>
                        <option value="Mshahara">Mshahara</option>
                        <option value="Kodi TRA">Kodi TRA</option>
                        <option value="Kodi Pango">Kodi Pango</option>
                        <option value="Mengineyo">Mengineyo</option>
                    </select>

                    <div x-show="selectedAina === 'Mengineyo'" class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Andika Aina Mpya ya Matumizi</label>
                        <input 
                            type="text" 
                            name="aina_mpya" 
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                            placeholder="Mfano: Chakula, Mafuta, Umeme..." 
                            required
                        >
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Maelezo</label>
                    <input 
                        type="text" 
                        name="maelezo" 
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                        placeholder="Maelezo ya ziada kuhusu matumizi..."
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
                        required
                    >
                </div>

                <div class="flex items-end">
                    <div class="w-full">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tarehe</label>
                        <input 
                            type="date" 
                            name="tarehe" 
                            value="{{ now()->format('Y-m-d') }}"
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

<!-- Edit Modal -->
<div 
    x-show="showEditModal" 
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center modal-overlay"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
>
    <div 
        class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4"
        @click.outside="showEditModal = false"
    >
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Badili Taarifa za Matumizi</h3>
        </div>
        <form :action="'/matumizi/' + editingItem.id" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Aina ya Matumizi</label>
                <input 
                    type="text" 
                    name="aina" 
                    x-model="editingItem.aina"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    required
                >
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Maelezo</label>
                <input 
                    type="text" 
                    name="maelezo" 
                    x-model="editingItem.maelezo"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                >
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kiasi cha Gharama (TZS)</label>
                <input 
                    type="number" 
                    name="gharama" 
                    x-model="editingItem.gharama"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    step="0.01"
                    min="0"
                    required
                >
            </div>
            <div class="flex justify-end space-x-3 pt-4">
                <button 
                    type="button" 
                    @click="showEditModal = false"
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

<div 
    x-show="showDeleteModal"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center modal-overlay bg-black bg-opacity-50"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
>

    <div 
        class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4"
        @click.outside="showDeleteModal = false"
    >
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Thibitisha Kufuta Matumizi</h3>
        </div>
        <div class="p-6">
            <p class="text-gray-700 mb-6">
                Una uhakika unataka kufuta matumizi ya <span x-text="deletingItemName" class="font-semibold"></span>?
                Hatua hii haiwezi kutenduliwa.
            </p>
            <div class="flex justify-end space-x-3">
                <button 
                    @click="showDeleteModal = false"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors"
                >
                    Ghairi
                </button>
                <form :action="'/matumizi/' + deletingItemId" method="POST">
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

@push('scripts')
<script>
function matumiziApp() {
    return {
        activeTab: 'taarifa',
        searchQuery: '',
        selectedAina: '',
        showEditModal: false,
        showDeleteModal: false,
        editingItem: {},
        deletingItemId: null,
        deletingItemName: '',
        
        editItem(item) {
            this.editingItem = { ...item };
            this.showEditModal = true;
        },
        
        deleteItem(id, name) {
            this.deletingItemId = id;
            this.deletingItemName = name;
            this.showDeleteModal = true;
        }
    }
}
</script>
@endpush