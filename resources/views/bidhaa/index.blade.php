<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mauzo - Bidhaa</title>
    <style>[x-cloak]{display:none!important}</style>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js" crossorigin="anonymous"></script>
</head>

<body class="bg-blue-50 font-sans">

<div x-data="bidhaaApp()" class="flex h-screen overflow-hidden">

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
            <a href="{{ route('bidhaa.index') }}" class="block px-4 py-2 bg-yellow-700 rounded">📦 Bidhaa</a>
            <a href="{{ route('manunuzi.index') }}" class="block px-4 py-2 hover:bg-yellow-700 rounded">🚚 Manunuzi</a>
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
                <button @click="activeTab='taarifa'" :class="activeTab==='taarifa' ? 'font-bold text-blue-700' : 'text-gray-600'" class="hover:underline">Taarifa za Bidhaa</button>
                <button @click="activeTab='ingiza'" :class="activeTab==='ingiza' ? 'font-bold text-blue-700' : 'text-gray-600'" class="hover:underline">Ingiza Bidhaa</button>
                <button @click="activeTab='csv'" :class="activeTab==='csv' ? 'font-bold text-blue-700' : 'text-gray-600'" class="hover:underline">Ingiza Bidhaa kwa CSV</button>
            </div>

           
        </header>

        <main class="flex-1 overflow-y-auto p-6">

{{-- 🧾 Taarifa za Bidhaa --}}
<div x-show="activeTab==='taarifa'" class="bg-white shadow rounded-lg p-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold text-gray-700">Taarifa za Bidhaa</h2>
        <div class="flex space-x-2">
            <input type="text" x-model="search" placeholder="🔍 Tafuta bidhaa..." class="border px-3 py-1 rounded">
            <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-1 rounded hover:bg-blue-700">
                <i class="fas fa-print"></i> Chapisha
            </button>
        </div>
    </div>

    <table class="min-w-full border text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-3 py-2">Jina</th>
                <th class="border px-3 py-2">Aina</th>
                <th class="border px-3 py-2">Kipimo</th>
                <th class="border px-3 py-2">Idadi</th>
                <th class="border px-3 py-2">Bei Nunua</th>
                <th class="border px-3 py-2">Bei Kuuza</th>
                <th class="border px-3 py-2">Expiry</th>
                <th class="border px-3 py-2 text-center">Badili</th>
                <th class="border px-3 py-2 text-center">Futa</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bidhaa as $item)
            <tr x-show="!search || '{{ strtolower($item->jina) }}'.includes(search.toLowerCase())">
                <td class="border px-3 py-2">{{ $item->jina }}</td>
                <td class="border px-3 py-2">{{ $item->aina }}</td>
                <td class="border px-3 py-2">{{ $item->kipimo }}</td>
                <td class="border px-3 py-2">{{ $item->idadi }}</td>
                <td class="border px-3 py-2">{{ $item->bei_nunua }}</td>
                <td class="border px-3 py-2">{{ $item->bei_kuuza }}</td>
                <td class="border px-3 py-2">{{ $item->expiry }}</td>
                <td class="border px-3 py-2 text-center">
                    <button @click="editId={{ $item->id }}; editItem=@js($item)"
                        class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">
                        Badili
                    </button>
                </td>
                <td class="border px-3 py-2 text-center">
                    <button @click="deleteId={{ $item->id }}"
                        class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">
                        Futa
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- 📝 Edit Modal --}}
    <div x-show="editId" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded shadow w-96">
            <h4 class="text-lg font-semibold mb-4 text-gray-700">Badili Bidhaa</h4>
            <form :action="`/bidhaa/${editItem.id}`" method="POST">
                @csrf
                @method('PUT')

                <input type="text" name="jina" x-model="editItem.jina" class="w-full border p-2 rounded mb-2" required>
                <input type="text" name="aina" x-model="editItem.aina" class="w-full border p-2 rounded mb-2" required>
                <input type="text" name="kipimo" x-model="editItem.kipimo" class="w-full border p-2 rounded mb-2">
                <input type="number" name="idadi" x-model="editItem.idadi" class="w-full border p-2 rounded mb-2" required>
                <input type="number" step="0.01" name="bei_nunua" x-model="editItem.bei_nunua" class="w-full border p-2 rounded mb-2" required>
                <input type="number" step="0.01" name="bei_kuuza" x-model="editItem.bei_kuuza" class="w-full border p-2 rounded mb-2" required>
                <input type="date" name="expiry" x-model="editItem.expiry" class="w-full border p-2 rounded mb-2">

                <div class="flex justify-end gap-2 mt-3">
                    <button type="button" @click="editId=null; editItem={}" class="bg-gray-400 px-3 py-1 rounded hover:bg-gray-500">Funga</button>
                    <button type="submit" class="bg-green-600 px-3 py-1 rounded text-white hover:bg-green-700">💾 Hifadhi</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ❌ Delete Modal --}}
    <div x-show="deleteId" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded shadow w-96 text-center">
            <h4 class="text-lg font-semibold mb-4 text-gray-700">Futa Bidhaa</h4>
            <p class="mb-4 text-gray-600">Je, una uhakika unataka kufuta bidhaa hii? Hatua hii haiwezi kubatilishwa.</p>
            <div class="flex justify-center gap-4">
                <button type="button" @click="deleteId=null" class="bg-gray-400 px-3 py-1 rounded hover:bg-gray-500">Funga</button>
                <form :action="`/bidhaa/${deleteId}`" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 px-3 py-1 rounded text-white hover:bg-red-700">⚠️ Futa</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- 🟢 Ingiza Bidhaa --}}
<div 
    x-show="activeTab==='ingiza'" 
    class="bg-white shadow rounded-lg p-6"
    x-data="{ nunua: '', kuuza: '', error: '' }"
>
    <h2 class="text-lg font-semibold mb-4">Ingiza Bidhaa Mpya</h2>

    <form 
        method="POST" 
        action="{{ route('bidhaa.store') }}" 
        @submit.prevent="
            if (parseFloat(nunua) > parseFloat(kuuza)) {
                error = '⚠️ Bei ya kununua haiwezi kuwa kubwa kuliko bei ya kuuza!';
            } else {
                error = '';
                $el.submit();
            }
        "
        class="grid grid-cols-2 gap-6"
    >
        @csrf

        <!-- Jina -->
        <div class="relative w-full">
            <input type="text" name="jina" placeholder=" " class="peer border rounded w-full p-2 pt-5" required>
            <label class="absolute left-2 top-2 text-gray-400 text-sm transition-all
                          peer-placeholder-shown:top-5 peer-placeholder-shown:text-gray-400
                          peer-placeholder-shown:text-sm peer-focus:-top-1 peer-focus:text-blue-600
                          peer-focus:text-xs">Jina la Bidhaa</label>
        </div>

        <!-- Aina -->
        <div class="relative w-full">
            <input type="text" name="aina" placeholder=" " class="peer border rounded w-full p-2 pt-5" required>
            <label class="absolute left-2 top-2 text-gray-400 text-sm transition-all
                          peer-placeholder-shown:top-5 peer-placeholder-shown:text-gray-400
                          peer-placeholder-shown:text-sm peer-focus:-top-1 peer-focus:text-blue-600
                          peer-focus:text-xs">Aina</label>
        </div>

        <!-- Kipimo -->
        <div class="relative w-full">
            <input type="text" name="kipimo" placeholder=" " class="peer border rounded w-full p-2 pt-5">
            <label class="absolute left-2 top-2 text-gray-400 text-sm transition-all
                          peer-placeholder-shown:top-5 peer-placeholder-shown:text-gray-400
                          peer-placeholder-shown:text-sm peer-focus:-top-1 peer-focus:text-blue-600
                          peer-focus:text-xs">Kipimo</label>
        </div>

        <!-- Idadi -->
        <div class="relative w-full">
            <input type="number" name="idadi" placeholder=" " class="peer border rounded w-full p-2 pt-5" required>
            <label class="absolute left-2 top-2 text-gray-400 text-sm transition-all
                          peer-placeholder-shown:top-5 peer-placeholder-shown:text-gray-400
                          peer-placeholder-shown:text-sm peer-focus:-top-1 peer-focus:text-blue-600
                          peer-focus:text-xs">Idadi</label>
        </div>

        <!-- Bei Nunua -->
        <div class="relative w-full">
            <input type="number" step="0.01" name="bei_nunua" x-model="nunua" placeholder=" " 
                   class="peer border rounded w-full p-2 pt-5" required>
            <label class="absolute left-2 top-2 text-gray-400 text-sm transition-all
                          peer-placeholder-shown:top-5 peer-placeholder-shown:text-gray-400
                          peer-placeholder-shown:text-sm peer-focus:-top-1 peer-focus:text-blue-600
                          peer-focus:text-xs">Bei ya Kununua</label>
        </div>

        <!-- Bei Kuuza -->
        <div class="relative w-full">
            <input type="number" step="0.01" name="bei_kuuza" x-model="kuuza" placeholder=" " 
                   class="peer border rounded w-full p-2 pt-5" required>
            <label class="absolute left-2 top-2 text-gray-400 text-sm transition-all
                          peer-placeholder-shown:top-5 peer-placeholder-shown:text-gray-400
                          peer-placeholder-shown:text-sm peer-focus:-top-1 peer-focus:text-blue-600
                          peer-focus:text-xs">Bei ya Kuuza</label>

            <!-- Error Message appears right below this field -->
            <p x-show="error" x-text="error" class="text-red-600 font-medium mt-1"></p>
        </div>

        <!-- Expiry -->
        <div class="relative col-span-2">
            <input type="date" name="expiry" placeholder=" " class="peer border rounded w-full p-2 pt-5" required>
            <label class="absolute left-2 top-2 text-gray-400 text-sm transition-all
                          peer-placeholder-shown:top-5 peer-placeholder-shown:text-gray-400
                          peer-placeholder-shown:text-sm peer-focus:-top-1 peer-focus:text-blue-600
                          peer-focus:text-xs">Tarehe ya Expiry</label>
        </div>

        <!-- Barcode -->
        <div class="relative col-span-2">
            <input type="text" name="barcode" placeholder=" " class="peer border rounded w-full p-2 pt-5">
            <label class="absolute left-2 top-2 text-gray-400 text-sm transition-all
                          peer-placeholder-shown:top-5 peer-placeholder-shown:text-gray-400
                          peer-placeholder-shown:text-sm peer-focus:-top-1 peer-focus:text-blue-600
                          peer-focus:text-xs">Barcode ya Bidhaa</label>
        </div>

        <!-- Buttons -->
        <div class="col-span-2 flex space-x-4 mt-4">
            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">
                💾 Hifadhi
            </button>
            <button type="reset" @click="error=''; nunua=''; kuuza='';" 
                    class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                🔄 Rekebisha
            </button>
        </div>
    </form>
</div>


            {{-- 📂 Ingiza Bidhaa kwa CSV --}}
            <div x-show="activeTab==='csv'" class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold mb-4">Ingiza Bidhaa kwa CSV</h2>

                <p class="text-gray-600 mb-4">
                    💡 Tafadhali hakikisha faili lako lina muundo huu kabla ya kupakia.
                </p>

                <div class="flex space-x-4 mb-4">
                    <a href="{{ route('bidhaa.downloadSample') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">⬇️ Download Sampuli</a>
                </div>

                <form method="POST" action="{{ route('bidhaa.uploadCSV') }}" enctype="multipart/form-data" class="mb-6">
                    @csrf
                    <input type="file" name="csv_file" accept=".csv,.txt" class="border p-2 rounded w-full mb-4" required>
                    <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">📤 Pakia Faili</button>
                </form>

                {{-- Upload Results --}}
                @if(session('successCount') > 0)
                    <div class="bg-green-100 text-green-800 border border-green-300 p-3 rounded mb-4">
                        ✅ Bidhaa {{ session('successCount') }} zimeongezwa kikamilifu!
                    </div>
                @endif

                @if(session('errorsList') && count(session('errorsList')) > 0)
                    <div class="bg-red-100 text-red-800 border border-red-300 p-3 rounded mb-4">
                        <h4 class="font-semibold mb-2">⚠️ Bidhaa zifuatazo hazikupakiwa:</h4>
                        <ul class="list-disc ml-5 space-y-1">
                            @foreach(session('errorsList') as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mt-6 border-t pt-4">
                    <h3 class="text-md font-semibold mb-2">📘 Muundo wa Faili la Sampuli</h3>
                    <table class="w-full text-sm border border-gray-300">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-2 py-1">Jina</th>
                                <th class="border px-2 py-1">Aina</th>
                                <th class="border px-2 py-1">Kipimo</th>
                                <th class="border px-2 py-1">Idadi</th>
                                <th class="border px-2 py-1">Bei_Nunua</th>
                                <th class="border px-2 py-1">Bei_Kuuza</th>
                                <th class="border px-2 py-1">Expiry</th>
                                <th class="border px-2 py-1">Barcode</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="text-center">
                                <td class="border px-2 py-1">Soda</td>
                                <td class="border px-2 py-1">Vinywaji</td>
                                <td class="border px-2 py-1">500ml</td>
                                <td class="border px-2 py-1">100</td>
                                <td class="border px-2 py-1">600</td>
                                <td class="border px-2 py-1">1000</td>
                                <td class="border px-2 py-1">2025-12-31</td>
                                <td class="border px-2 py-1">1234567890123</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
function bidhaaApp() {
    return {
        sidebarOpen: true,
        activeTab: 'taarifa',
        search: '',
        editId: null,
        deleteId: null,
        editItem: {},

    

    }
}
</script>


</body>
</html>
