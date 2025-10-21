<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wafanyakazi - Mauzo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js" crossorigin="anonymous"></script>
</head>
<body class="bg-blue-50 font-sans">

<div x-data="{ 
        sidebarOpen: true, 
        active: 'taarifa', 
        search: '', 
        showModal: false, 
        selected: null,
        showConfirm: false,
        toDeleteId: null,
        showEditModal: false,
        editEmployee: null
    }" class="flex h-screen overflow-hidden">

    {{-- Sidebar --}}
    <aside :class="sidebarOpen ? 'w-64' : 'w-16'" class="bg-green-800 text-white flex flex-col transition-all duration-300">
        <div class="p-6 text-center border-b border-gray-800 flex flex-col items-center">
            <div x-show="sidebarOpen" class="text-2xl font-bold mb-1">DEMODAY</div>
            <div x-show="sidebarOpen" class="text-sm">Boss</div>
            <button @click="sidebarOpen = !sidebarOpen" class="mt-2 text-gray-400 hover:text-white">☰</button>
        </div>

        {{-- Sidebar Links --}}
        <nav class="flex-1 p-4 space-y-2">
            <a href="{{ route('dashboard') }}" class="block px-4 py-2 hover:bg-yellow-600 rounded">🏠 Dashboard</a>
            <a href="{{ route('mauzo.index') }}" class="block px-4 py-2 hover:bg-yellow-600 rounded">🛒 Mauzo</a>
            <a href="{{ route('madeni.index') }}" class="block px-4 py-2 hover:bg-yellow-700 rounded">💳 Madeni</a>
            <a href="{{ route('matumizi.index') }}" class="block px-4 py-2 hover:bg-yellow-700 rounded">💰 Matumizi</a>
            <a href="{{ route('bidhaa.index') }}" class="block px-4 py-2 hover:bg-yellow-700 rounded">📦 Bidhaa</a>
            <a href="{{ route('manunuzi.index') }}" class="block px-4 py-2 hover:bg-yellow-700 rounded">🚚 Manunuzi</a>
            <a href="{{ route('wafanyakazi.index') }}" class="block px-4 py-2 bg-yellow-700 rounded">👔 Wafanyakazi</a>
            <a href="{{ route('masaplaya.index') }}" class="block px-4 py-2 hover:bg-yellow-700 rounded">🏆 Masaplaya</a>
            <a href="{{ route('wateja.index') }}" class="block px-4 py-2 hover:bg-yellow-700 rounded">👥 Wateja</a>
            <a href="{{ route('uchambuzi.index') }}" class="block px-4 py-2 hover:bg-yellow-700 rounded">📊 Uchambuzi</a>
        </nav>
    </aside>

    {{-- Main Content --}}
    <div class="flex-1 flex flex-col">
        {{-- Navbar --}}
        <header class="bg-white shadow px-6 py-4 flex justify-between items-center">
    <div class="flex items-center space-x-6">
        <h1 class="text-lg font-semibold">Sehemu ya Wafanyakazi</h1>

        <!-- Horizontal links next to the title -->
        <div class="flex space-x-4">
            <button 
                @click="active='taarifa'" 
                :class="active==='taarifa' ? 'border-b-2 border-blue-600 text-blue-700 font-semibold' : 'text-gray-600 hover:text-blue-600'" 
                class="pb-1">
                Taarifa za Wafanyakazi
            </button>

            <button 
                @click="active='sajili'" 
                :class="active==='sajili' ? 'border-b-2 border-blue-600 text-blue-700 font-semibold' : 'text-gray-600 hover:text-blue-600'" 
                class="pb-1">
                Sajili Wafanyakazi
            </button>
        </div>
    </div>

    
</header>


        {{-- Page Content --}}
        <main class="flex-1 p-6 overflow-auto flex space-x-6">

          

            {{-- Right Content --}}
            <div class="flex-1 bg-white p-6 rounded shadow">

                {{-- Taarifa --}}
                <div x-show="active==='taarifa'">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Taarifa za Wafanyakazi</h3>
                        <input type="text" placeholder="Tafuta..." x-model="search"
                               class="border px-2 py-1 rounded" />
                    </div>

                    <table class="w-full table-auto border-collapse">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-4 py-2">Jina</th>
                                <th class="border px-4 py-2">Simu</th>
                                <th class="border px-4 py-2">Jinsia</th>
                                <th class="border px-4 py-2">Anuani</th>
                                <th class="border px-4 py-2">Barua pepe</th>
                                <th class="border px-4 py-2">Mengineyo</th>
                                <th class="border px-4 py-2">Badili</th>
                                <th class="border px-4 py-2">Futa</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($wafanyakazi as $mfanyakazi)
                                <tr x-show="search === '' || '{{ strtolower($mfanyakazi->jina) }}'.includes(search.toLowerCase())">
                                    <td class="border px-4 py-2">{{ $mfanyakazi->jina }}</td>
                                    <td class="border px-4 py-2">{{ $mfanyakazi->simu }}</td>
                                    <td class="border px-4 py-2">{{ $mfanyakazi->jinsia }}</td>
                                    <td class="border px-4 py-2">{{ $mfanyakazi->anuani }}</td>
                                    <td class="border px-4 py-2">{{ $mfanyakazi->barua_pepe }}</td>
                                    <td class="border px-4 py-2 text-blue-600 cursor-pointer"
                                        @click="selected = {{ $mfanyakazi->toJson() }}; showModal = true">
                                        Mengineyo
                                    </td>
                                    <td class="border px-4 py-2">
                                        <button 
                                            class="text-green-600 hover:underline"
                                            @click="editEmployee = {{ $mfanyakazi->toJson() }}; showEditModal = true">
                                            Badili
                                        </button>
                                    </td>
                                    <td class="border px-4 py-2">
                                        <button @click="showConfirm=true; toDeleteId={{ $mfanyakazi->id }}" class="text-red-600 hover:underline">
                                            Futa
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">Hakuna wafanyakazi bado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Sajili --}}
                <div x-show="active==='sajili'">
                    <h3 class="text-lg font-semibold mb-4">Sajili Wafanyakazi</h3>
                    <form method="POST" action="{{ route('wafanyakazi.store') }}">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div><label>Jina kamili</label><input type="text" name="jina" class="w-full border p-2 rounded"></div>
                            <div><label>Jinsia</label>
                                <select name="jinsia" class="w-full border p-2 rounded">
                                    <option>Chagua jinsia</option>
                                    <option>Mwanaume</option>
                                    <option>Mwanamke</option>
                                </select>
                            </div>
                            <div><label>Tarehe ya kuzaliwa</label><input type="date" name="tarehe_kuzaliwa" class="w-full border p-2 rounded"></div>
                            <div><label>Anuani</label><input type="text" name="anuani" class="w-full border p-2 rounded"></div>
                            <div><label>Simu</label><input type="text" name="simu" class="w-full border p-2 rounded"></div>
                            <div><label>Barua pepe</label><input type="email" name="barua_pepe" class="w-full border p-2 rounded"></div>
                            <div><label>Ndugu</label><input type="text" name="ndugu" class="w-full border p-2 rounded"></div>
                            <div><label>Simu ya ndugu</label><input type="text" name="simu_ndugu" class="w-full border p-2 rounded"></div>
                            <div><label>Neno la kuingia</label><input type="text" name="username" class="w-full border p-2 rounded"></div>
                            <div><label>Neno la siri</label><input type="password" name="password" class="w-full border p-2 rounded"></div>
                        </div>
                        <div class="flex gap-4 mt-4">
                            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Hifadhi</button>
                            <button type="reset" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Rekebisha</button>
                        </div>
                    </form>
                </div>

            </div>
        </main>
    </div>

    {{-- Modal ya Mengineyo --}}
    <div x-show="showModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded shadow-lg w-1/2">
            <h3 class="text-lg font-semibold mb-4">Taarifa Kamili za Mfanyakazi</h3>
            <div class="space-y-2">
                <p><b>Jina:</b> <span x-text="selected?.jina"></span></p>
                <p><b>Jinsia:</b> <span x-text="selected?.jinsia"></span></p>
                <p><b>Tarehe ya Kuzaliwa:</b> <span x-text="selected?.tarehe_kuzaliwa"></span></p>
                <p><b>Anuani:</b> <span x-text="selected?.anuani"></span></p>
                <p><b>Simu:</b> <span x-text="selected?.simu"></span></p>
                <p><b>Barua pepe:</b> <span x-text="selected?.barua_pepe"></span></p>
                <p><b>Ndugu:</b> <span x-text="selected?.ndugu"></span></p>
                <p><b>Simu ya Ndugu:</b> <span x-text="selected?.simu_ndugu"></span></p>
                <p><b>Neno la Kuingia:</b> <span x-text="selected?.username"></span></p>
            </div>
            <p class="mt-4 text-sm text-gray-600">
                Leo tarehe {{ \Carbon\Carbon::now()->translatedFormat('d F, Y') }}, 
                Una jumla ya wafanyakazi {{ count($wafanyakazi) }}.
            </p>
            <div class="flex justify-end mt-4">
                <button @click="showModal=false" class="bg-red-600 text-white px-4 py-2 rounded">Funga</button>
            </div>
        </div>
    </div>

    {{-- Modal ya Badili --}}
    <div x-show="showEditModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded shadow-lg w-1/2">
            <h3 class="text-lg font-semibold mb-4">Badili Mfanyakazi</h3>
            <form :action="`/wafanyakazi/${editEmployee?.id}`" method="POST">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label>Jina kamili</label><input type="text" name="jina" x-model="editEmployee.jina" class="w-full border p-2 rounded"></div>
                    <div><label>Jinsia</label>
                        <select name="jinsia" x-model="editEmployee.jinsia" class="w-full border p-2 rounded">
                            <option value="Mwanaume">Mwanaume</option>
                            <option value="Mwanamke">Mwanamke</option>
                        </select>
                    </div>
                    <div><label>Tarehe ya kuzaliwa</label><input type="date" name="tarehe_kuzaliwa" x-model="editEmployee.tarehe_kuzaliwa" class="w-full border p-2 rounded"></div>
                    <div><label>Anuani</label><input type="text" name="anuani" x-model="editEmployee.anuani" class="w-full border p-2 rounded"></div>
                    <div><label>Simu</label><input type="text" name="simu" x-model="editEmployee.simu" class="w-full border p-2 rounded"></div>
                    <div><label>Barua pepe</label><input type="email" name="barua_pepe" x-model="editEmployee.barua_pepe" class="w-full border p-2 rounded"></div>
                    <div><label>Ndugu</label><input type="text" name="ndugu" x-model="editEmployee.ndugu" class="w-full border p-2 rounded"></div>
                    <div><label>Simu ya ndugu</label><input type="text" name="simu_ndugu" x-model="editEmployee.simu_ndugu" class="w-full border p-2 rounded"></div>
                    <div><label>Neno la kuingia</label><input type="text" name="username" x-model="editEmployee.username" class="w-full border p-2 rounded"></div>
                    <div><label>Neno la siri (acha tupu kama hujabadilisha)</label><input type="password" name="password" class="w-full border p-2 rounded"></div>
                </div>
                <div class="flex gap-4 mt-4">
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Hifadhi</button>
                    <button type="button" @click="showEditModal=false" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Funga</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal ya Thibitisha Futa --}}
    <div x-show="showConfirm" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded shadow-lg w-1/3">
            <h3 class="text-lg font-semibold mb-4">Thibitisha Futa</h3>
            <p class="mb-4">Una uhakika unataka kufuta mfanyakazi huyu?</p>
            <div class="flex justify-end gap-4">
                <button @click="showConfirm=false; toDeleteId=null" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">Hapana</button>
                <form :action="`/wafanyakazi/${toDeleteId}`" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                        Ndiyo, Futa
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>

</body>
</html>
<style>[x-cloak]{ display:none !important; }</style>
