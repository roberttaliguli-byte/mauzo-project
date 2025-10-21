<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wateja - Mauzo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js" crossorigin="anonymous"></script>
</head>
<body class="bg-blue-50 font-sans">

<div x-data="{ sidebarOpen: true, active: 'taarifa', search: '', editId: null, deleteId: null }" class="flex h-screen overflow-hidden">

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
            <a href="{{ route('wafanyakazi.index') }}" class="block px-4 py-2 hover:bg-yellow-700 rounded">👔 Wafanyakazi</a>
            <a href="{{ route('masaplaya.index') }}" class="block px-4 py-2 hover:bg-yellow-700 rounded">🏆 Masaplaya</a>
            <a href="{{ route('wateja.index') }}" class="block px-4 py-2 bg-yellow-700 rounded">👥 Wateja</a>
            <a href="{{ route('uchambuzi.index') }}" class="block px-4 py-2 hover:bg-yellow-700 rounded">📊 Uchambuzi</a>
        </nav>
    </aside>

    {{-- Main Content --}}
    <div class="flex-1 flex flex-col">
        {{-- Navbar --}}
<!-- Header -->
<header class="bg-gray shadow px-6 py-4 flex justify-between items-center">
    <div class="flex items-center space-x-6">
        <h1 class="text-lg font-semibold">Sehemu ya Wateja</h1>

        <!-- Horizontal navigation links -->
        <div class="flex space-x-4">
            <button 
                @click="active='taarifa'" 
                :class="active==='taarifa' ? 'border-b-2 border-blue-600 text-blue-700 font-semibold' : 'text-gray-600 hover:text-blue-600'" 
                class="pb-1">
                Taarifa za Mteja
            </button>

            <button 
                @click="active='sajili'" 
                :class="active==='sajili' ? 'border-b-2 border-blue-600 text-blue-700 font-semibold' : 'text-gray-600 hover:text-blue-600'" 
                class="pb-1">
                Sajili Mteja
            </button>
        </div>
    </div>

    
</header>

            {{-- Right Content --}}
            <div class="flex-1 bg-white p-6 rounded shadow">

                {{-- Taarifa Za Mteja --}}
                <div x-show="active==='taarifa'">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Taarifa Za Wateja</h3>
                        <button onclick="window.print()" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">
                            <i class="fas fa-print"></i> Print
                        </button>
                    </div>

                    <table class="w-full table-auto border-collapse">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-4 py-2">Jina</th>
                                <th class="border px-4 py-2">Simu</th>
                                <th class="border px-4 py-2">Barua Pepe</th>
                                <th class="border px-4 py-2">Anapoishi</th>
                                <th class="border px-4 py-2">Tarehe</th>
                                <th class="border px-4 py-2">Badili</th>
                                <th class="border px-4 py-2">Futa</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($wateja as $mteja)
                                <tr x-show="search === '' || '{{ strtolower($mteja->jina) }}'.includes(search.toLowerCase())">
                                    <td class="border px-4 py-2">{{ $mteja->jina }}</td>
                                    <td class="border px-4 py-2">{{ $mteja->simu }}</td>
                                    <td class="border px-4 py-2">{{ $mteja->barua_pepe }}</td>
                                    <td class="border px-4 py-2">{{ $mteja->anapoishi }}</td>
                                    <td class="border px-4 py-2">{{ $mteja->created_at->format('d-m-Y') }}</td>

                                    {{-- Badili Button --}}
                                    <td class="border px-4 py-2 text-center">
                                        <button @click="editId={{ $mteja->id }}" class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">Badili</button>
                                    </td>

                                    {{-- Futa Button --}}
                                    <td class="border px-4 py-2 text-center">
                                        <button @click="deleteId={{ $mteja->id }}" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">Futa</button>
                                    </td>
                                </tr>

                                {{-- Edit Modal --}}
                                <div x-show="editId==={{ $mteja->id }}" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                                    <div class="bg-white p-6 rounded shadow w-96">
                                        <h4 class="text-lg font-semibold mb-4">Badili Mteja</h4>
                                        <form method="POST" action="{{ route('wateja.update', $mteja->id) }}">
                                            @csrf
                                            @method('PUT')
                                            <div class="space-y-2">
                                                <input type="text" name="jina" value="{{ $mteja->jina }}" class="w-full border p-2 rounded">
                                                <input type="text" name="simu" value="{{ $mteja->simu }}" class="w-full border p-2 rounded">
                                                <input type="email" name="barua_pepe" value="{{ $mteja->barua_pepe }}" class="w-full border p-2 rounded">
                                                <input type="text" name="anapoishi" value="{{ $mteja->anapoishi }}" class="w-full border p-2 rounded">
                                                <textarea name="maelezo" class="w-full border p-2 rounded">{{ $mteja->maelezo }}</textarea>
                                            </div>
                                            <div class="flex justify-end mt-4 gap-2">
                                                <button type="button" @click="editId=null" class="bg-gray-400 px-3 py-1 rounded hover:bg-gray-500">Funga</button>
                                                <button type="submit" class="bg-green-600 px-3 py-1 rounded text-white hover:bg-green-700">Hifadhi</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                {{-- Delete Modal --}}
                                <div x-show="deleteId==={{ $mteja->id }}" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                                    <div class="bg-white p-6 rounded shadow w-96 text-center">
                                        <h4 class="text-lg font-semibold mb-4">Futa Mteja</h4>
                                        <p class="mb-4">Una uhakika unataka kufuta <strong>{{ $mteja->jina }}</strong>?</p>
                                        <div class="flex justify-center gap-4">
                                            <button type="button" @click="deleteId=null" class="bg-gray-400 px-3 py-1 rounded hover:bg-gray-500">Funga</button>
                                            <form method="POST" action="{{ route('wateja.destroy', $mteja->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-red-600 px-3 py-1 rounded text-white hover:bg-red-700">Futa</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">Hakuna wateja waliosajiliwa bado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{-- Comment on customer count --}}
                    <div class="mt-4 p-3 bg-yellow-50 border rounded">
                        <p class="text-sm text-gray-700">
                            Jumla ya wateja: {{ $wateja->count() }}
                        </p>
                    </div>
                </div>

                {{-- Sajili Mteja --}}
                <div x-show="active==='sajili'">
                    <h3 class="text-lg font-semibold mb-4">Sajili Mteja</h3>
                    <form method="POST" action="{{ route('wateja.store') }}">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block mb-1">Jina</label>
                                <input type="text" name="jina" class="w-full border p-2 rounded">
                            </div>
                            <div>
                                <label class="block mb-1">Simu</label>
                                <input type="text" name="simu" class="w-full border p-2 rounded">
                            </div>
                            <div>
                                <label class="block mb-1">Barua Pepe</label>
                                <input type="email" name="barua_pepe" class="w-full border p-2 rounded">
                            </div>
                            <div>
                                <label class="block mb-1">Anapoishi</label>
                                <input type="text" name="anapoishi" class="w-full border p-2 rounded">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block mb-1">Maelezo ya ziada</label>
                                <textarea name="maelezo" class="w-full border p-2 rounded"></textarea>
                            </div>
                        </div>
                        <div class="flex gap-4 mt-4">
                            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Hifadhi</button>
                            <button type="reset" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">Rekebisha</button>
                        </div>
                    </form>
                </div>

            </div>
        </main>
    </div>
</div>

</body>
</html>
