<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Madeni</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="bg-blue-50 font-sans">

<div x-data="madeniApp()" class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'w-64' : 'w-16'"
           class="bg-green-800 text-white flex flex-col transition-all duration-300">
        <div class="p-6 text-center border-b border-gray-800 flex flex-col items-center">
            <div x-show="sidebarOpen" class="text-2xl font-bold mb-1">DEMODAY</div>
            <div x-show="sidebarOpen" class="text-sm">Boss</div>
            <button @click="sidebarOpen = !sidebarOpen"
                    class="mt-2 text-gray-400 hover:text-white">☰</button>
        </div>

        <nav class="flex-1 p-4 space-y-2">
            <a href="{{ route('dashboard') }}" class="block px-4 py-2 hover:bg-yellow-600 rounded">🏠 Dashboard</a>
            <a href="{{ route('mauzo.index') }}" class="block px-4 py-2 hover:bg-yellow-700 rounded">🛒 Mauzo</a>
            <a href="{{ route('madeni.index') }}" class="block px-4 py-2 bg-yellow-700 rounded">💳 Madeni</a>
            <a href="{{ route('matumizi.index') }}" class="block px-4 py-2 hover:bg-yellow-700 rounded">💰 Matumizi</a>
            <a href="{{ route('bidhaa.index') }}" class="block px-4 py-2 hover:bg-yellow-700 rounded">📦 Bidhaa</a>
            <a href="{{ route('manunuzi.index') }}" class="block px-4 py-2 hover:bg-yellow-700 rounded">🚚 Manunuzi</a>
            <a href="{{ route('wafanyakazi.index') }}" class="block px-4 py-2 hover:bg-yellow-700 rounded">👔 Wafanyakazi</a>
            <a href="{{ route('masaplaya.index') }}" class="block px-4 py-2 hover:bg-yellow-700 rounded">🏆 Masaplaya</a>
            <a href="{{ route('wateja.index') }}" class="block px-4 py-2 hover:bg-yellow-700 rounded">👥 Wateja</a>
            <a href="{{ route('uchambuzi.index') }}" class="block px-4 py-2 hover:bg-yellow-700 rounded">📊 Uchambuzi</a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">

        <!-- Navbar -->
        <header class="bg-gray shadow px-6 py-4 flex justify-between items-center">
            <div class="flex items-center space-x-6">
                <h1 class="text-lg font-semibold">Sehemu ya Madeni</h1>

                <nav class="flex items-center space-x-4">
                    <a href="#" @click="activeTab='madeni'"
                       :class="activeTab==='madeni' ? 'font-bold text-blue-700' : 'hover:underline'">
                        Orodha ya Madeni
                    </a>
                    <a href="#" @click="activeTab='marejesho'"
                       :class="activeTab==='marejesho' ? 'font-bold text-blue-700' : 'hover:underline'">
                        Marejesho
                    </a>
                </nav>
            </div>


        </header>

        <!-- Page Content -->
        <main class="flex-1 p-6 overflow-auto bg-white rounded shadow">

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="bg-green-200 text-green-800 p-2 rounded mb-4">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="bg-red-200 text-red-800 p-2 rounded mb-4">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- TAB 1: Orodha ya Madeni -->
            <div x-show="activeTab==='madeni'">
                <h2 class="font-bold mb-4">Orodha ya Madeni</h2>

                <!-- Search & Print -->
                <div class="flex gap-2 p-4 bg-white mb-4">
                    <input type="text" placeholder="Tafuta..." x-model="search"
                           class="border px-2 py-1 rounded flex-1" />
                    <button @click="window.print()"
                            class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Print</button>
                </div>

                <table class="w-full table-auto border-collapse">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border px-4 py-2">Mkopo</th>
                            <th class="border px-4 py-2">Bidhaa</th>
                            <th class="border px-4 py-2">Idadi</th>
                            <th class="border px-4 py-2">Bei</th>
                            <th class="border px-4 py-2">Baki</th>
                            <th class="border px-4 py-2">Mkopaji</th>
                            <th class="border px-4 py-2">Simu</th>
                            <th class="border px-4 py-2">Vitendo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($madeni as $deni)
                        <tr x-data="{ showPayModal: false, showEditModal: false, deleteId: null, amount: {{ $deni->baki }}, payDate: '{{ \Carbon\Carbon::now()->format('Y-m-d') }}' }"
                            x-show="search === '' || '{{ strtolower($deni->jina_mkopaji) }}'.includes(search.toLowerCase()) || '{{ strtolower($deni->bidhaa->jina) }}'.includes(search.toLowerCase())">
                            
                            <td class="border px-4 py-2">{{ \Carbon\Carbon::parse($deni->tarehe_malipo)->format('d/m/Y') }}</td>
                            <td class="border px-4 py-2">{{ $deni->bidhaa->jina }}</td>
                            <td class="border px-4 py-2">{{ $deni->idadi }}</td>
                            <td class="border px-4 py-2">{{ number_format($deni->bei,2) }}</td>
                            <td class="border px-4 py-2">{{ number_format($deni->baki,2) }}</td>
                            <td class="border px-4 py-2">{{ $deni->jina_mkopaji }}</td>
                            <td class="border px-4 py-2">{{ $deni->simu }}</td>

                            {{-- Vitendo --}}
                            <td class="border px-4 py-2 text-center space-x-1">
                                {{-- Lipa --}}
                                <button @click="showPayModal = true" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">💵 Lipa</button>

                                {{-- Edit --}}
                                <button @click="showEditModal = true" class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">✏️ Badilisha</button>

                                {{-- Delete --}}
                                <button @click="deleteId={{ $deni->id }}" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">🗑️ Futa</button>

                                {{-- Pay Modal --}}
                                <div x-show="showPayModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                                    <div class="bg-white p-6 rounded shadow w-96">
                                        <h4 class="text-lg font-semibold mb-4">Lipa Deni</h4>
                                        <form method="POST" action="{{ route('madeni.rejesha', $deni->id) }}" class="space-y-3">
                                            @csrf
                                            <div>
                                                <label class="block mb-1">Kiasi (Baki)</label>
                                                <input type="number" name="kiasi" x-model="amount" class="w-full border p-2 rounded text-right" required>
                                            </div>
                                            <div>
                                                <label class="block mb-1">Tarehe</label>
                                                <input type="date" name="tarehe" x-model="payDate" class="w-full border p-2 rounded" required>
                                            </div>
                                            <div class="flex justify-end gap-2 mt-4">
                                                <button type="button" @click="showPayModal = false" class="bg-gray-400 px-3 py-1 rounded hover:bg-gray-500">Funga</button>
                                                <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">Lipa</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                {{-- Edit Modal --}}
                                <div x-show="showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                                    <div class="bg-white p-6 rounded shadow w-96">
                                        <h4 class="text-lg font-semibold mb-4">Badilisha Deni</h4>
                                        <form method="POST" action="{{ route('madeni.update', $deni->id) }}">
                                            @csrf
                                            @method('PUT')
                                            <div class="space-y-2">
                                                <input type="text" name="bidhaa" value="{{ $deni->bidhaa->jina }}" class="w-full border p-2 rounded">
                                                <input type="number" name="idadi" value="{{ $deni->idadi }}" class="w-full border p-2 rounded">
                                                <input type="number" name="bei" value="{{ $deni->bei }}" class="w-full border p-2 rounded">
                                                <input type="text" name="jina_mkopaji" value="{{ $deni->jina_mkopaji }}" class="w-full border p-2 rounded">
                                                <input type="text" name="simu" value="{{ $deni->simu }}" class="w-full border p-2 rounded">
                                            </div>
                                            <div class="flex justify-end gap-2 mt-4">
                                                <button type="button" @click="showEditModal = false" class="bg-gray-400 px-3 py-1 rounded hover:bg-gray-500">Funga</button>
                                                <button type="submit" class="bg-yellow-500 px-3 py-1 rounded text-white hover:bg-yellow-600">Hifadhi</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                {{-- Delete Modal --}}
                                <div x-show="deleteId=== {{ $deni->id }}" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                                    <div class="bg-white p-6 rounded shadow w-96 text-center">
                                        <h4 class="text-lg font-semibold mb-4">Futa Deni</h4>
                                        <p class="mb-4">Una uhakika unataka kufuta deni la <strong>{{ $deni->jina_mkopaji }}</strong>?</p>
                                        <div class="flex justify-center gap-4">
                                            <button type="button" @click="deleteId=null" class="bg-gray-400 px-3 py-1 rounded hover:bg-gray-500">Funga</button>
                                            <form method="POST" action="{{ route('madeni.destroy', $deni->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-red-600 px-3 py-1 rounded text-white hover:bg-red-700">Futa</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">Hakuna madeni yaliyorekodiwa bado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- TAB 2: Historia ya Marejesho -->
            <div x-show="activeTab==='marejesho'">
                <h2 class="font-bold mb-4">Historia ya Marejesho</h2>

                <div class="flex gap-2 p-4 bg-white mb-4">
                    <input type="text" placeholder="Tafuta..." x-model="search"
                           class="border px-2 py-1 rounded flex-1" />
                    <button @click="window.print()"
                            class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Print</button>
                </div>

                <table class="w-full table-auto border-collapse">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border px-2 py-1">Tarehe ya Mwisho</th>
                            <th class="border px-2 py-1">Bidhaa</th>
                            <th class="border px-2 py-1">Idadi</th>
                            <th class="border px-2 py-1">Deni Lote</th>
                            <th class="border px-2 py-1">Jumla Rejeshwa</th>
                            <th class="border px-2 py-1">Rejesho la Mwisho</th>
                            <th class="border px-2 py-1">Baki</th>
                            <th class="border px-2 py-1">Mkopaji</th>
                            <th class="border px-2 py-1">Simu</th>
                            <th class="border px-2 py-1">Hali</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($historia as $h)
                            <tr x-show="search=='' || '{{ $h['mkopaji'] }}'.toLowerCase().includes(search.toLowerCase()) || '{{ $h['bidhaa'] }}'.toLowerCase().includes(search.toLowerCase())">
                                <td class="border px-2 py-1">{{ $h['tarehe'] }}</td>
                                <td class="border px-2 py-1">{{ $h['bidhaa'] }}</td>
                                <td class="border px-2 py-1">{{ $h['idadi'] }}</td>
                                <td class="border px-2 py-1">{{ number_format($h['deni_lote'], 2) }}</td>
                                <td class="border px-2 py-1">{{ number_format($h['jumla_rejeshwa'], 2) }}</td>
                                <td class="border px-2 py-1">{{ number_format($h['rejesho_leo'], 2) }}</td>
                                <td class="border px-2 py-1">
                                    @if($h['baki'] == 0)
                                        <span class="text-green-600 font-bold">Amemaliza</span>
                                    @else
                                        {{ number_format($h['baki'], 2) }}
                                    @endif
                                </td>
                                <td class="border px-2 py-1">{{ $h['mkopaji'] }}</td>
                                <td class="border px-2 py-1">{{ $h['simu'] }}</td>
                                <td class="border px-2 py-1">{{ $h['status'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">Hakuna marejesho yaliyorekodiwa bado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </main>
    </div>
</div>

<script>
function madeniApp() {
    return {
        sidebarOpen: true,
        activeTab: 'madeni',
        search: '',
    }
}
</script>

</body>
</html>
