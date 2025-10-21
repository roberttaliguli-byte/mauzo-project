<!DOCTYPE html>
<html lang="sw">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mauzo - Manunuzi</title>

<style>
    [x-cloak] { display: none !important; }
</style>

<script src="https://cdn.tailwindcss.com"></script>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js" crossorigin="anonymous"></script>
</head>

<body class="bg-blue-50 font-sans">

<div x-data="manunuziApp()" class="flex h-screen overflow-hidden">

    {{-- Sidebar --}}
    <aside :class="sidebarOpen ? 'w-64' : 'w-16'" class="bg-green-800 text-white flex flex-col transition-all duration-300">
        <div class="p-6 text-center border-b border-gray-800 flex flex-col items-center">
            <div x-show="sidebarOpen" class="text-2xl font-bold mb-1">DEMODAY</div>
            <div x-show="sidebarOpen" class="text-sm">Boss</div>
            <button @click="sidebarOpen = !sidebarOpen" class="mt-2 text-gray-400 hover:text-white">☰</button>
        </div>
        <nav class="flex-1 p-4 space-y-2">
            <a href="{{ route('dashboard') }}" class="block px-4 py-2 hover:bg-yellow-600 rounded">🏠 Dashboard</a>
            <a href="{{ route('mauzo.index') }}" class="block px-4 py-2 hover:bg-yellow-600 rounded">🛒 Mauzo</a>
            <a href="{{ route('madeni.index') }}" class="block px-4 py-2 hover:bg-yellow-700 rounded">💳 Madeni</a>
            <a href="{{ route('matumizi.index') }}" class="block px-4 py-2 hover:bg-yellow-700 rounded">💰 Matumizi</a>
            <a href="{{ route('bidhaa.index') }}" class="block px-4 py-2 hover:bg-yellow-700 rounded">📦 Bidhaa</a>
            <a href="{{ route('manunuzi.index') }}" class="block px-4 py-2 bg-yellow-700 rounded">🚚 Manunuzi</a>
            <a href="{{ route('wafanyakazi.index') }}" class="block px-4 py-2 hover:bg-yellow-700 rounded">👔 Wafanyakazi</a>
            <a href="{{ route('masaplaya.index') }}" class="block px-4 py-2 hover:bg-yellow-700 rounded">🏆 Masaplaya</a>
            <a href="{{ route('wateja.index') }}" class="block px-4 py-2 hover:bg-yellow-700 rounded">👥 Wateja</a>
            <a href="{{ route('uchambuzi.index') }}" class="block px-4 py-2 hover:bg-yellow-700 rounded">📊 Uchambuzi</a>
        </nav>
    </aside>

    {{-- Main Content --}}
    <div class="flex-1 flex flex-col">
        <header class="bg-white shadow px-6 py-4 flex justify-between items-center">
            <div class="flex space-x-4">
                <button @click="activeTab='taarifa'" :class="activeTab==='taarifa' ? 'font-bold text-blue-700' : 'text-gray-600'" class="hover:underline">Taarifa za Manunuzi</button>
                <button @click="activeTab='ingiza'" :class="activeTab==='ingiza' ? 'font-bold text-blue-700' : 'text-gray-600'" class="hover:underline">Ingiza Manunuzi</button>
            </div>
           
            
        </header>

        <main class="flex-1 overflow-y-auto p-6">

            {{-- Taarifa za Manunuzi --}}
            <div x-show="activeTab==='taarifa'" class="bg-white shadow rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-700">Taarifa za Manunuzi</h2>
                    <input type="text" x-model="search" placeholder="🔍 Tafuta..." class="border px-3 py-1 rounded">
                </div>

                <table class="min-w-full border">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-4 py-2">Tarehe</th>
                            <th class="border px-4 py-2">Bidhaa</th>
                            <th class="border px-4 py-2">Idadi</th>
                            <th class="border px-4 py-2">Bei</th>
                            <th class="border px-4 py-2">Expiry</th>
                            <th class="border px-4 py-2">Saplaya</th>
                            <th class="border px-4 py-2">Simu</th>
                            <th class="border px-4 py-2">Mengineyo</th>
                            <th class="border px-4 py-2">Badili</th>
                            <th class="border px-4 py-2">Futa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($manunuzi as $item)
                        <tr x-show="search === '' || '{{ strtolower($item->bidhaa->jina) }} {{ strtolower($item->saplaya) }} {{ strtolower($item->simu) }}'.includes(search.toLowerCase())">
                            <td class="border px-4 py-2">{{ $item->created_at->format('d/m/Y') }}</td>
                            <td class="border px-4 py-2">{{ $item->bidhaa->jina }}</td>
                            <td class="border px-4 py-2">{{ $item->idadi }}</td>
                            <td class="border px-4 py-2">{{ $item->bei }}</td>
                            <td class="border px-4 py-2">{{ $item->expiry }}</td>
                            <td class="border px-4 py-2">{{ $item->saplaya }}</td>
                            <td class="border px-4 py-2">{{ $item->simu }}</td>
                            <td class="border px-4 py-2">{{ $item->mengineyo }}</td>

                            {{-- Edit --}}
                            <td class="border px-4 py-2 text-center">
                                <button @click="openEdit({{ $item }})" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">Badili</button>
                            </td>

                            {{-- Delete --}}
                            <td class="border px-4 py-2 text-center">
                                <button @click="deleteId={{ $item->id }}" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">Futa</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Ingiza Manunuzi --}}
            <div x-show="activeTab==='ingiza'" class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold mb-4">Ingiza Manunuzi ya Bidhaa</h2>
                <form method="POST" action="{{ route('manunuzi.store') }}" class="grid grid-cols-2 gap-4">
                    @csrf
                    <select name="bidhaa_id" class="border p-2 rounded" required>
                        <option value="">Chagua Bidhaa</option>
                        @foreach($bidhaa as $b)
                        <option value="{{ $b->id }}">{{ $b->jina }}</option>
                        @endforeach
                    </select>
                    <input type="number" name="idadi" placeholder="Idadi ya bidhaa" class="border p-2 rounded" required>
                    <div class="flex space-x-2 col-span-2">
                        <select name="bei_type" class="border p-2 rounded">
                            <option value="kwa_zote">Kwa Zote</option>
                            <option value="rejareja">Rejareja</option>
                        </select>
                        <input type="number" step="0.01" name="bei" placeholder="Bei ya kununua" class="border p-2 rounded flex-1" required>
                    </div>
                    <input type="date" name="expiry" class="border p-2 rounded">
                    <input type="text" name="saplaya" placeholder="Jina la saplaya" class="border p-2 rounded">
                    <input type="text" name="simu" placeholder="Namba ya simu ya saplaya" class="border p-2 rounded">
                    <textarea name="mengineyo" placeholder="Maelezo ya ziada..." class="border p-2 rounded col-span-2"></textarea>
                    <div class="col-span-2 flex gap-4 mt-4">
                        <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded">💾 Hifadhi</button>
                        <button type="reset" class="bg-blue-600 text-white px-6 py-2 rounded">🔄 Rekebisha</button>
                    </div>
                </form>
            </div>

            {{-- Edit Modal --}}
          <div x-show="editId" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
   <div class="bg-white p-6 rounded shadow w-96">
                    <h4 class="text-lg font-semibold mb-4">Badili Manunuzi</h4>
                    <form :action="`/manunuzi/${editItem.id}`" method="POST">
                        @csrf
                        @method('PUT')
                        <select name="bidhaa_id" x-model="editItem.bidhaa_id" class="w-full border p-2 rounded mb-2">
                            @foreach($bidhaa as $b)
                                <option value="{{ $b->id }}">{{ $b->jina }}</option>
                            @endforeach
                        </select>
                        <input type="number" name="idadi" x-model="editItem.idadi" class="w-full border p-2 rounded mb-2">
                        <input type="number" step="0.01" name="bei" x-model="editItem.bei" class="w-full border p-2 rounded mb-2">
                        <input type="date" name="expiry" x-model="editItem.expiry" class="w-full border p-2 rounded mb-2">
                        <input type="text" name="saplaya" x-model="editItem.saplaya" class="w-full border p-2 rounded mb-2">
                        <input type="text" name="simu" x-model="editItem.simu" class="w-full border p-2 rounded mb-2">
                        <textarea name="mengineyo" x-model="editItem.mengineyo" class="w-full border p-2 rounded mb-2"></textarea>
                        <div class="flex justify-end gap-2">
                            <button type="button" @click="editId=null" class="bg-gray-400 px-3 py-1 rounded hover:bg-gray-500">Funga</button>
                            <button type="submit" class="bg-green-600 px-3 py-1 rounded text-white hover:bg-green-700">Hifadhi</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Delete Modal --}}
           <div x-show="deleteId" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
     <div class="bg-white p-6 rounded shadow w-96 text-center">
                    <h4 class="text-lg font-semibold mb-4">Futa Manunuzi</h4>
                    <p class="mb-4">Una uhakika unataka kufuta manunuzi hii?</p>
                    <div class="flex justify-center gap-4">
                        <button type="button" @click="deleteId=null" class="bg-gray-400 px-3 py-1 rounded hover:bg-gray-500">Funga</button>
                        <form :action="`/manunuzi/${deleteId}`" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-600 px-3 py-1 rounded text-white hover:bg-red-700">Futa</button>
                        </form>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>

<script>
function manunuziApp() {
    return {
        sidebarOpen: true,
        activeTab: 'taarifa',
        search: '',
        editId: null,
        deleteId: null,
        editItem: {},
        openEdit(item) {
            this.editId = item.id;
            this.editItem = {...item};
        }
    }
}
</script>

</body>
</html>
