@extends('layouts.app')

@section('title', 'Wateja - DEMODAY')

@section('page-title', 'Wateja')
@section('page-subtitle', 'Dodoso la wateja wote - ' . now()->format('d/m/Y'))

@section('content')
<div x-data="watejaApp()">

    <!-- Tabs -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 mb-6 card-hover">
        <div class="flex space-x-6 border-b border-gray-200">
            <button 
                @click="activeTab = 'taarifa'" 
                :class="activeTab === 'taarifa' ? 'border-b-2 border-green-500 text-green-600 font-semibold' : 'text-gray-500 hover:text-gray-700'" 
                class="pb-3 px-1 transition-colors"
            >
                <i class="fas fa-users mr-2"></i>Taarifa za Wateja
            </button>
            <button 
                @click="activeTab = 'sajili'" 
                :class="activeTab === 'sajili' ? 'border-b-2 border-green-500 text-green-600 font-semibold' : 'text-gray-500 hover:text-gray-700'" 
                class="pb-3 px-1 transition-colors"
            >
                <i class="fas fa-user-plus mr-2"></i>Sajili Mteja Mpya
            </button>
        </div>
    </div>

    <!-- TAB 1: Orodha ya Wateja -->
    <div x-show="activeTab === 'taarifa'" class="space-y-6">
        <div class="bg-gray-100 rounded-2xl shadow-lg border border-gray-100 p-6 card-hover">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-black">Orodha ya Wateja</h2>
                <button onclick="window.print()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                    <i class="fas fa-print mr-2"></i> Print
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="bg-white border-b">
                            <th class="px-6 py-4 text-left text-sm font-semibold text-black">Jina</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-black">Simu</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-black">Barua Pepe</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-black">Anapoishi</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-black">Tarehe</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-black">Vitendo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray">
                        @forelse($wateja as $mteja)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-green-100 rounded-full flex items-center justify-center text-green-800 font-semibold">
                                        {{ substr($mteja->jina, 0, 1) }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-black">{{ $mteja->jina }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-black">{{ $mteja->simu }}</td>
                            <td class="px-6 py-4 text-sm text-black">{{ $mteja->barua_pepe ?: '--' }}</td>
                            <td class="px-6 py-4 text-sm text-black">{{ $mteja->anapoishi ?: '--' }}</td>
                            <td class="px-6 py-4 text-sm text-black">{{ $mteja->created_at->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button 
                                        @click="openEdit({{ $mteja->id }})" 
                                        class="text-yellow-600 hover:text-yellow-800 transition-colors"
                                        title="Badili"
                                    >
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button 
                                        @click="openDelete({{ $mteja->id }}, '{{ $mteja->jina }}')" 
                                        class="text-red-600 hover:text-red-800 transition-colors"
                                        title="Futa"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-users text-4xl mb-3 text-gray-300"></i>
                                <p class="text-lg">Hakuna wateja waliosajiliwa bado.</p>
                                <button 
                                    @click="activeTab = 'sajili'" 
                                    class="mt-3 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors"
                                >
                                    Sajili Mteja wa Kwanza
                                </button>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-sm text-green-700 flex items-center">
                    <i class="fas fa-info-circle mr-2"></i>
                    Jumla ya wateja: <span class="font-semibold ml-1">{{ $wateja->count() }}</span>
                </p>
            </div>
        </div>
    </div>

    <!-- TAB 2: Sajili Mteja Mpya -->
    <div x-show="activeTab === 'sajili'" class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 card-hover">
        <h2 class="text-xl font-bold text-gray-800 mb-6">Sajili Mteja Mpya</h2>
        <form method="POST" action="{{ route('wateja.store') }}" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jina Kamili</label>
                    <input type="text" name="jina" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Namba ya Simu</label>
                    <input type="text" name="simu" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Barua Pepe</label>
                    <input type="email" name="barua_pepe" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Anapoishi</label>
                    <input type="text" name="anapoishi" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Maelezo ya Ziada</label>
                    <textarea name="maelezo" rows="4" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500"></textarea>
                </div>
            </div>
            <div class="flex gap-4 pt-4">
                <button type="submit" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700">
                    <i class="fas fa-save mr-2"></i> Hifadhi Mteja
                </button>
                <button type="reset" class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400">
                    <i class="fas fa-redo mr-2"></i> Safisha Fomu
                </button>
            </div>
        </form>
    </div>

 <!-- 🟢 EDIT MODAL -->
<div 
    x-show="editModal" 
     x-cloak
    class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center" 
    x-transition
>
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6" @click.outside="closeEdit()">
        <h3 class="text-lg font-semibold mb-4">Badili Taarifa za Mteja</h3>

        <form :action="`/wateja/${editData.id}`" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jina Kamili</label>
                <input type="text" name="jina" x-model="editData.jina" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Namba ya Simu</label>
                <input type="text" name="simu" x-model="editData.simu" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Barua Pepe</label>
                <input type="email" name="barua_pepe" x-model="editData.barua_pepe" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Anapoishi</label>
                <input type="text" name="anapoishi" x-model="editData.anapoishi" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Maelezo ya Ziada</label>
                <textarea name="maelezo" x-model="editData.maelezo" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500"></textarea>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" @click="closeEdit()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Ghairi</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Hifadhi Mabadiliko</button>
            </div>
        </form>
    </div>
</div>

<!-- 🔴 DELETE MODAL -->
<div 
    x-show="deleteId" 
    x-cloak
    class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center" 
    x-transition.opacity
>
    <div 
        class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 transform transition-all duration-200"
        x-transition.scale
        @click.outside="deleteId = null"
    >
        <h3 class="text-lg font-semibold mb-4 text-gray-800">Thibitisha Kufuta</h3>

        <p class="text-gray-700 mb-6">
            Una uhakika unataka kufuta mteja 
            <span class="font-semibold" x-text="deleteName"></span>?
        </p>

        <div class="flex justify-end space-x-3">
            <button 
                @click="deleteId = null" 
                class="px-4 py-2 border rounded-lg hover:bg-gray-100"
            >
                Ghairi
            </button>

            <form :action="`/wateja/${deleteId}`" method="POST">
                @csrf
                @method('DELETE')
                <button 
                    type="submit" 
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
                >
                    Ndio, Futa
                </button>
            </form>
        </div>
    </div>
</div>

</div>

@push('scripts')
<script src="//unpkg.com/alpinejs" defer></script>
<script>
function watejaApp() {
    return {
        activeTab: 'taarifa',
        editModal: false,
        editData: {},
        deleteId: null,
        deleteName: '',
        allWateja: @json($wateja),

        openEdit(id) {
            const mteja = this.allWateja.find(c => c.id === id);
            if (mteja) {
                this.editData = JSON.parse(JSON.stringify(mteja)); // clone object
                this.editModal = true;
            }
        },
        closeEdit() {
            this.editModal = false;
            this.editData = {};
        },

        openDelete(id, name) {
            this.deleteId = id;
            this.deleteName = name;
        },
    }
}
</script>
@endpush

@endsection
