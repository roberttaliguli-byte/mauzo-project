<!DOCTYPE html>
<html lang="sw">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mauzo</title>

<style>
    [x-cloak] { display: none !important; }
</style>

<script src="https://cdn.tailwindcss.com"></script>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="bg-blue-50 font-sans">

<div x-data="mauzoApp()" class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'w-64' : 'w-16'" class="bg-green-800 text-white flex flex-col transition-all duration-300">
        <div class="p-6 text-center border-b border-gray-800 flex flex-col items-center">
            <div x-show="sidebarOpen" class="text-2xl font-bold mb-1">DEMODAY</div>
            <div x-show="sidebarOpen" class="text-sm">Boss</div>
            <button @click="sidebarOpen = !sidebarOpen" class="mt-2 text-gray-400 hover:text-white">☰</button>
        </div>
        <nav class="flex-1 p-4 space-y-2">
            <a href="{{ route('dashboard') }}" class="block px-4 py-2 hover:bg-yellow-600 rounded">🏠 Dashboard</a>
            <a href="{{ route('mauzo.index') }}" class="block px-4 py-2 bg-yellow-700 rounded">🛒 Mauzo</a>
            <a href="{{ route('madeni.index') }}" class="block px-4 py-2 hover:bg-yellow-700 rounded">💳 Madeni</a>
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

        <!-- Header -->
        <header class="bg-white shadow px-6 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-lg font-semibold text-gray-700">Sehemu ya Mauzo</h1>


            </div>

            <!-- Horizontal Links -->
            <div class="flex space-x-6 mt-4 border-t pt-3 text-sm font-medium">
                <button @click="activeTab='sehemu'" 
                        :class="activeTab==='sehemu' ? 'text-blue-700 border-b-2 border-blue-700 pb-1' : 'text-gray-600 hover:text-blue-600'" 
                        class="transition">
                    Sehemu ya Mauzo
                </button>
                <button @click="activeTab='barcode'" 
                        :class="activeTab==='barcode' ? 'text-blue-700 border-b-2 border-blue-700 pb-1' : 'text-gray-600 hover:text-blue-600'" 
                        class="transition">
                    Mauzo kwa Barcode
                </button>
                <button @click="activeTab='taarifa'" 
                        :class="activeTab==='taarifa' ? 'text-blue-700 border-b-2 border-blue-700 pb-1' : 'text-gray-600 hover:text-blue-600'" 
                        class="transition">
                    Taarifa Fupi
                </button>
                <button @click="activeTab='jumla'" 
                        :class="activeTab==='jumla' ? 'text-blue-700 border-b-2 border-blue-700 pb-1' : 'text-gray-600 hover:text-blue-600'" 
                        class="transition">
                    Mauzo ya Jumla
                </button>

                <button 
                    @click="showKikapu = true"
                    class="text-gray-600 hover:text-blue-600 transition flex items-center gap-1"
                    title="Fungua Kikapu">
                    🧺 <span class="text-sm font-medium">Kikapu</span>
                </button>

            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 p-6 overflow-auto">
            
            <!-- Sehemu ya Mauzo -->
            <div x-show="activeTab==='sehemu'" x-data="{ showMadeniModal: false }">

                <!-- Success Message -->
                @if(session('success'))
                <div class="bg-green-200 text-green-800 p-2 rounded mb-4">{{ session('success') }}</div>
                @endif

                <!-- Validation Errors -->
                @if($errors->any())
                <div class="bg-red-200 text-red-800 p-2 rounded mb-4">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ route('mauzo.store') }}" 
                      class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6"
                      @submit.prevent="if(decreaseStock()) $el.submit()">
                    @csrf

                    <!-- Bidhaa -->
                    <div class="md:col-span-2">
                        <label for="bidhaaSelect" class="block mb-1 font-medium">Bidhaa</label>
                        <select id="bidhaaSelect"
                                name="bidhaa_id" 
                                class="w-full border p-2 rounded"
                                x-model="bidhaaSelected" 
                                @change="updateStock()">
                            <option value="">Chagua Bidhaa</option>
                            @foreach($bidhaa as $item)
                                <option value="{{ $item->id }}" data-bei="{{ $item->bei_kuuza }}">
                                    {{ $item->jina }} ({{ $item->aina }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Idadi -->
                    <div>
                        <label class="block mb-1 font-medium">Idadi</label>
                        <input type="number" name="idadi" class="w-full border p-2 rounded" min="1" :max="stock" x-model.number="idadi">
                    </div>

                    <!-- Bei -->
                    <div>
                        <label class="block mb-1 font-medium">Bei</label>
                        <input type="number" name="bei" class="w-full border p-2 rounded bg-gray-100 cursor-not-allowed" :value="bei" readonly>
                    </div>

                    <!-- Jumla -->
                    <div>
                        <label class="block mb-1 font-medium">Jumla</label>
                        <input type="number" name="jumla" class="w-full border p-2 rounded bg-gray-100 cursor-not-allowed" :value="jumla" readonly>
                    </div>

                    <!-- Idadi Iliyopo -->
                    <div>
                        <label class="block mb-1 font-medium">Idadi Iliyopo</label>
                        <input type="number" name="idadi_iliyopo" class="w-full border p-2 rounded bg-gray-100 cursor-not-allowed" :value="stock" readonly>
                    </div>

                    <!-- Punguzo -->
                    <div>
                        <div class="flex items-center mb-1">
                            <input type="checkbox" id="punguzoCheck" x-model="punguzoCheck" class="mr-2">
                            <label for="punguzoCheck" class="font-medium">Punguzo</label>
                        </div>
                        <input type="number" name="punguzo" class="w-full border p-2 rounded" min="0" x-model.number="punguzo" :disabled="!punguzoCheck">
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-2 mt-2 col-span-5">
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded flex-1">Uza</button>
                        <button type="button" @click="showMadeniModal = true" class="bg-yellow-400 text-white px-4 py-2 rounded flex-1">Kopesha</button>
                    <button type="button" @click="addToCart" class="bg-blue-600 text-white px-4 py-2 rounded flex-1">
    ➕ Ongeza Kwenye Kikapu
</button>

                    </div>
                </form>
                
                  <!-- Kopesha Modal -->
<div 
    x-show="showMadeniModal" 
    x-cloak 
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
>
    <div class="bg-white p-6 rounded shadow w-96">
        <h2 class="text-lg font-semibold mb-4">Kopesha Bidhaa</h2>

        <form 
            method="POST" 
            action="{{ route('madeni.store') }}"
            @submit.prevent="if(decreaseStock()) { showMadeniModal = false; $el.submit(); }"
        >
            @csrf

            <!-- Hidden sale info -->
            <input type="hidden" name="bidhaa_id" :value="bidhaaSelected">
            <input type="hidden" name="idadi" :value="idadi">
            <input type="hidden" name="jumla" :value="jumla">
            <input type="hidden" name="baki" :value="jumla">
            <input type="hidden" name="bei" :value="bei">
            <input type="hidden" name="kopesha" value="1">

            <!-- Registered Customer -->
            <div class="mb-2">
                <label class="block font-semibold mb-1">Chagua Mteja (kama ameshasajiliwa)</label>
                <select 
                    x-model="selectedMtejaId" 
                    @change="fillMtejaDetails"
                    class="w-full border p-2 rounded"
                >
                    <option value="">-- Mteja Mpya --</option>
                    @foreach($wateja as $m)
                        <option value="{{ $m->id }}" 
                            data-jina="{{ $m->jina }}" 
                            data-simu="{{ $m->simu }}"
                            data-barua_pepe="{{ $m->barua_pepe }}"
                            data-anapoishi="{{ $m->anapoishi }}">
                            {{ $m->jina }} - {{ $m->simu }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- New Customer / or editable fields -->
            <div class="mb-2">
                <label>Jina la Mkopaji</label>
                <input type="text" name="jina_mkopaji" x-model="mteja.jina" class="w-full border p-2 rounded" required>
            </div>

            <div class="mb-2">
                <label>Namba ya Simu</label>
                <input type="text" name="simu" x-model="mteja.simu" class="w-full border p-2 rounded" required>
            </div>

            <div class="mb-2">
                <label>Barua Pepe</label>
                <input type="email" name="barua_pepe" x-model="mteja.barua_pepe" class="w-full border p-2 rounded">
            </div>

            <div class="mb-2">
                <label>Anapoishi</label>
                <input type="text" name="anapoishi" x-model="mteja.anapoishi" class="w-full border p-2 rounded">
            </div>

            <div class="mb-2">
                <label>Tarehe ya Malipo</label>
                <input type="date" name="tarehe_malipo" class="w-full border p-2 rounded" required>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-2 mt-4">
                <button type="button" @click="showMadeniModal = false" class="bg-gray-400 px-3 py-1 rounded hover:bg-gray-500">Funga</button>
                <button type="submit" class="bg-green-600 px-3 py-1 rounded text-white hover:bg-green-700">Hifadhi</button>
            </div>
        </form>
    </div>
</div>
<!-- 💳 Kopesha Mteja Modal -->
<div 
    x-show="showKopesha" 
    x-cloak 
    class="fixed inset-y-0 left-64 right-0 bg-black bg-opacity-40 flex items-center justify-center z-50"
>
    <div 
        class="bg-white w-11/12 md:w-1/2 lg:w-1/3 p-6 rounded-2xl shadow-2xl max-h-[85vh] overflow-auto"
        x-transition
    >
        <!-- Header -->
        <div class="flex justify-between items-center mb-4 border-b pb-2">
            <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                💳 Kopesha Mteja
            </h2>
            <button 
                @click="showKopesha = false" 
                class="text-red-600 hover:text-red-800 font-bold text-2xl leading-none"
                title="Funga"
            >&times;</button>
        </div>

        <!-- Form -->
        <form @submit.prevent="submitLoan" class="space-y-4">
            <!-- Mteja -->
            <div>
                <label class="font-medium">Chagua Mteja</label>
                <select 
                    x-model="selectedMteja" 
                    class="w-full border rounded p-2 mt-1"
                    required
                >
                    <option value="">-- Chagua Mteja --</option>
                    <template x-for="m in wateja" :key="m.id">
                        <option :value="m.jina" x-text="m.jina"></option>
                    </template>
                    
                </select>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3 pt-4">
                <button 
                    type="button" 
                    @click="showKopesha = false"
                    class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 shadow"
                >
                    Ghairi
                </button>

                <button 
                    type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 shadow"
                >
                    ✅ Thibitisha Kopesha
                </button>
            </div>
        </form>
    </div>
</div>
<!-- 💳 Kopesha Mteja Modal End -->

                </div>
<!-- Mauzo kwa Barcode Tab -->
<div x-show="activeTab==='barcode'" class="mt-4 bg-green-100 p-4 rounded shadow">
    <h2 class="text-lg font-semibold mb-4">Mauzo kwa Barcode</h2>

    <!-- Instruction Label -->
    <label class="block font-medium mb-2">Ingiza bidhaa kwa kutumia Barcode</label>

    <form @submit.prevent="submitBarcodeMauzo">
        <table class="w-full table-auto border-collapse mb-2">
            <thead class="bg-gray-200">
                <tr>
                    <th class="border px-2 py-1">Barcode</th>
                    <th class="border px-2 py-1">Bidhaa</th>
                    <th class="border px-2 py-1">Bei</th>
                    <th class="border px-2 py-1">Idadi</th>
                    <th class="border px-2 py-1">Idadi Iliyopo</th>
                    <th class="border px-2 py-1">Jumla</th>
                    <th class="border px-2 py-1">⚡</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="(item, index) in barcodeCart" :key="index">
                    <tr class="border-b">
                        <td class="px-2 py-1">
                            <input type="text" x-model="item.barcode" 
                                   @change="fetchBidhaa(item,index)" 
                                   placeholder="Ingiza Barcode hapa" 
                                   class="border p-1 w-full" />
                        </td>
                        <td class="px-2 py-1">
                            <input type="text" x-model="item.jina" readonly 
                                   placeholder="Jina la Bidhaa" 
                                   class="border p-1 w-full bg-gray-100" />
                        </td>
                        <td class="px-2 py-1">
                            <input type="number" x-model="item.bei" readonly 
                                   placeholder="Bei" 
                                   class="border p-1 w-full bg-gray-100" />
                        </td>
                        <td class="px-2 py-1">
                            <input type="number" x-model.number="item.idadi" min="1" 
                                   :max="item.stock" @input="updateJumla(item)" 
                                   placeholder="Idadi" 
                                   class="border p-1 w-full" />
                        </td>
                        <td class="px-2 py-1">
                            <input type="number" x-model="item.stock" readonly 
                                   placeholder="Baki" 
                                   class="border p-1 w-full bg-gray-100" />
                        </td>
                        <td class="px-2 py-1">
                            <input type="number" x-model="item.jumla" readonly 
                                   placeholder="Jumla" 
                                   class="border p-1 w-full bg-gray-100" />
                        </td>
                        <td class="px-2 py-1 text-center">
                            <button type="button" @click="removeItem(index)" class="text-red-600" title="Futa bidhaa">🗑</button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>

        <!-- Total & Submit -->
        <div class="flex justify-between items-center mt-2">
            <div class="font-medium">Jumla ya Mauzo: <span x-text="barcodeCartTotal()"></span></div>
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Uza Bidhaa</button>
        </div>
    </form>
</div>

<!-- Mauzo ya Jumla Tab at taarifa fupi -->
                <div x-show="activeTab==='sehemu'" class="mt-4">
<h2 class="text-lg font-semibold mb-4">
    Taarifa Fupi ya Mapato (Mauzo & Marejesho) na Matumizi ya Leo [Tarehe: {{ now()->format('d/m/Y') }} ]
</h2>


                    <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                        <div class="bg-green-100 p-4 rounded shadow">
                            Mapato <div class="font-bold" x-text="totalMapato()"></div>
                        </div>
                        <div class="bg-blue-100 p-4 rounded shadow">
                            Faida <div class="font-bold" x-text="totalFaida()"></div>
                        </div>
                        <div class="bg-red-100 p-4 rounded shadow">
                            Matumizi <div class="font-bold" x-text="totalMatumizi()"></div>
                        </div>
                        <div class="bg-yellow-100 p-4 rounded shadow">
                            Fedha Leo <div class="font-bold" x-text="totalFedhaLeo()"></div>
                        </div>
                        <div class="bg-purple-100 p-4 rounded shadow">
                            Faida Halisi Leo <div class="font-bold" x-text="totalFaidaHalisi()"></div>
                        </div>
                        <div class="bg-gray-100 p-4 rounded shadow">
                            Jumla Kuu <div class="font-bold" x-text="totalJumlaKuu()"></div>
                        </div>
                    </div>
                </div>
          
<!-- Taarifa Fupi Tab -->
<div x-show="activeTab==='taarifa'" x-data="{ search:'', deleteId:null }" class="mt-4">
    <!-- Search and Print in single line -->
    <div class="flex items-center gap-2 mb-4">
        <input type="text" x-model="search" placeholder="🔍 Tafuta kwa jina la bidhaa au tarehe..." class="flex-1 border p-2 rounded">
        <button @click="window.print()" class="bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700">Print</button>
    </div>

    <table class="w-full table-auto border-collapse">
        <thead>
            <tr class="bg-gray-100">
                <th class="border px-2 py-1">Tarehe</th>
                <th class="border px-2 py-1">Bidhaa</th>
                <th class="border px-2 py-1">Idadi</th>
                <th class="border px-2 py-1">Bei</th>
                <th class="border px-2 py-1">Punguzo</th>
                <th class="border px-2 py-1">Jumla</th>
                <th class="border px-4 py-2">Futa</th>
            </tr>
        </thead>
        <tbody>
            @php $today = \Carbon\Carbon::today()->format('Y-m-d'); @endphp
            @forelse($mauzos as $item)
            @php $itemDate = $item->created_at->format('Y-m-d'); @endphp
            <tr x-show="'{{ strtolower($item->bidhaa->jina) }}'.includes(search.toLowerCase()) || '{{ $itemDate }}'.includes(search)">
                <td class="border px-2 py-1">
                    @if($itemDate === $today)
                        <span class="bg-green-100 text-green-800 px-2 py-0.5 rounded font-semibold">Leo ({{ $itemDate }})</span>
                    @else
                        {{ $itemDate }}
                    @endif
                </td>
                <td class="border px-2 py-1">{{ $item->bidhaa->jina }}</td>
                <td class="border px-2 py-1">{{ $item->idadi }}</td>
                <td class="border px-2 py-1">{{ $item->bei }}</td>
                <td class="border px-2 py-1">{{ $item->punguzo }}</td>
                <td class="border px-2 py-1">{{ $item->jumla }}</td>
                <td class="border px-2 py-1 text-center">
                    <button type="button"
                        @click="deleteId={{ $item->id }}"
                        class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">
                        🗑 Futa
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center py-4">Hakuna mauzo yaliyorekodiwa bado.</td>
            </tr>
            @endforelse
        </tbody>
    </table>



    <!-- Delete Confirmation Modal (Taarifa Fupi tu) -->
   <div x-show="deleteId" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
   <div class="bg-white p-6 rounded shadow w-96 text-center">
            <h4 class="text-lg font-semibold mb-4">Futa Mauzo</h4>
            <p class="mb-4">Una uhakika unataka kufuta mauzo haya?</p>
            <div class="flex justify-center gap-4">
                <button type="button" @click="deleteId=null"
                        class="bg-gray-400 px-3 py-1 rounded hover:bg-gray-500">
                    Funga
                </button>
                <form :action="`/mauzo/${deleteId}`" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 px-3 py-1 rounded text-white hover:bg-red-700">
                        Futa
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>


            
<div x-show="activeTab==='jumla'" class="mt-4">
    <h2 class="text-lg font-semibold mb-4">Mauzo ya Jumla (Kwa Tarehe & Bidhaa)</h2>
<div class="flex items-center gap-2 mb-4">
    <input type="date" x-model="searchDateFrom" class="border rounded p-1" placeholder="Tarehe Kuanzia">
    <input type="date" x-model="searchDateTo" class="border rounded p-1" placeholder="Tarehe Hadi">
    <input type="text" x-model="searchProduct" class="border rounded p-1" placeholder="Bidhaa">
    <button @click="applyFilter()" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">Search</button>
    <button @click="printTable()" class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">Print</button>
</div>

    <table class="w-full border-collapse border">
        <thead class="bg-gray-200">
            <tr>
                <th class="border px-2 py-1">Tarehe</th>
                <th class="border px-2 py-1">Bidhaa</th>
                <th class="border px-2 py-1">Idadi</th>
                <th class="border px-2 py-1">Bei ya Jumla</th>
                <th class="border px-2 py-1">Punguzo</th>
                <th class="border px-2 py-1">Faida</th>
            </tr>
        </thead>
        <tbody>
            <template x-for="item in groupedMauzoByDate()" :key="item.tarehe + '-' + item.jina">
                <tr class="border-b">
                    <td class="border px-2 py-1" x-text="item.tarehe"></td>
                    <td class="border px-2 py-1" x-text="item.jina"></td>
                    <td class="border px-2 py-1 text-center" x-text="item.idadi"></td>
                    <td class="border px-2 py-1 text-right" x-text="item.jumla.toLocaleString()"></td>
                    <td class="border px-2 py-1 text-right" x-text="item.punguzo.toLocaleString()"></td>
                    <td class="border px-2 py-1 text-right" x-text="item.faida.toLocaleString()"></td>
                </tr>
            </template>
        </tbody>
    </table>
</div>

<!-- 🧺 Kikapu Modal -->
<div 
    x-show="showKikapu" 
    x-cloak 
    class="fixed inset-y-0 left-64 right-0 bg-black bg-opacity-40 flex items-center justify-center z-40"
>
    <div class="bg-white w-11/12 md:w-3/4 lg:w-2/3 xl:w-1/2 p-6 rounded-2xl shadow-2xl max-h-[85vh] overflow-auto relative">

        <!-- Header -->
        <div class="flex justify-between items-center mb-4 border-b pb-2">
            <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                🧺 Bidhaa Zilizo Kwenye Kikapu
            </h2>
            <button 
                @click="showKikapu = false" 
                class="text-red-600 hover:text-red-800 font-bold text-2xl leading-none"
                title="Funga"
            >&times;</button>
        </div>

        <!-- Empty Kikapu -->
        <template x-if="cart.length === 0">
            <div class="text-center text-gray-600 py-10">
                Hakuna bidhaa kwenye kikapu kwa sasa.
            </div>
        </template>

        <!-- Kikapu Items -->
        <template x-if="cart.length > 0">
            <div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm border border-gray-300 rounded-lg mb-4">
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th class="border px-2 py-1">S/N</th>
                                <th class="border px-2 py-1">Bidhaa</th>
                                <th class="border px-2 py-1">Idadi</th>
                                <th class="border px-2 py-1">Bei</th>
                                <th class="border px-2 py-1">Punguzo</th>
                                <th class="border px-2 py-1">Faida</th>
                                <th class="border px-2 py-1">Muda</th>
                                <th class="border px-2 py-1">Ondoa</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, index) in cart" :key="index">
                                <tr class="border-b hover:bg-gray-50 transition">
                                    <td class="border px-2 py-1 text-center" x-text="index + 1"></td>
                                    <td class="border px-2 py-1" x-text="item.jina"></td>
                                    <td class="border px-2 py-1 text-center" x-text="item.idadi"></td>
                                    <td class="border px-2 py-1 text-right" x-text="item.bei.toLocaleString()"></td>
                                    <td class="border px-2 py-1 text-right" x-text="item.punguzo.toLocaleString()"></td>
                                    <td class="border px-2 py-1 text-right" x-text="item.faida.toLocaleString()"></td>
                                    <td class="border px-2 py-1 text-center" x-text="item.muda"></td>
                                    <td class="border px-2 py-1 text-center">
                                        <button 
                                            @click="removeFromCart(index)" 
                                            class="text-red-500 hover:text-red-700"
                                            title="Ondoa Bidhaa">❌</button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Total -->
                <div class="text-right font-semibold text-gray-800 mb-4">
                    Jumla ya gharama: 
                    <span class="text-green-600 text-lg" x-text="cartTotal()"></span>
                </div>

                <!-- Actions -->
                <div class="flex flex-wrap justify-end gap-3">
                    <button 
                        @click="clearCart" 
                        class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 shadow">
                        🗑️ Futa Kikapu
                    </button>

                    <button 
                        @click="checkoutCart" 
                        class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 shadow">
                        🧾 Funga Kapu
                    </button>

                    <button 
                        @click="openKopeshaModal" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 shadow">
                        💳 Kopesha Mteja
                    </button>
                </div>
            </div>
        </template>
    </div>
</div>


        </main>
    </div>
</div>
<script>
function mauzoApp() {
    return {
        // Sidebar & Tabs
        sidebarOpen: true,
        activeTab: 'sehemu',

        // Modals & customer data
        showMadeniModal: false,
        showKikapu: false,
        showKopesha: false,
        selectedMtejaId: '',
        selectedMteja: '',
        newMteja: '',
        wateja: @json($wateja),
        mteja: { jina: '', simu: '', barua_pepe: '', anapoishi: '' },
groupedMauzoByDate() {
    const grouped = {};
    this.mapatoList.forEach(item => {
        const date = item.created_at.split('T')[0]; // YYYY-MM-DD
        const key = date + '|' + item.bidhaa.jina; // group by date + product
        if (!grouped[key]) {
            grouped[key] = {
                tarehe: date,
                jina: item.bidhaa.jina,
                idadi: 0,
                jumla: 0,
                punguzo: 0,
                faida: 0,
            };
        }
        grouped[key].idadi += item.idadi;
        grouped[key].jumla += parseFloat(item.jumla);
        grouped[key].punguzo += parseFloat(item.punguzo || 0);
        const beiKuuza = parseFloat(item.bidhaa.bei_kuuza || 0);
        const beiNunua = parseFloat(item.bidhaa.bei_nunua || 0);
        grouped[key].faida += (beiKuuza - beiNunua) * item.idadi;
    });
    return Object.values(grouped).sort((a, b) => b.tarehe.localeCompare(a.tarehe));
    
}, // ✅ COMMA added

        // Fill customer details
        fillMtejaDetails() {
            const select = document.querySelector('select[x-model="selectedMtejaId"]');
            const option = select?.options[select.selectedIndex];
            if (!option || !option.value) {
                this.mteja = { jina: '', simu: '', barua_pepe: '', anapoishi: '' };
                return;
            }
            this.mteja = {
                jina: option.dataset.jina || '',
                simu: option.dataset.simu || '',
                barua_pepe: option.dataset.barua_pepe || '',
                anapoishi: option.dataset.anapoishi || '',
            };
        },

        // --- Kikapu (Cart) ---
        cart: [],

        addToCart() {
            if (!this.bidhaaSelected || this.idadi < 1) {
                alert('Tafadhali chagua bidhaa na idadi sahihi!');
                return;
            }

            const product = this.bidhaaList.find(b => b.id == this.bidhaaSelected);
            if (!product) return alert('Bidhaa haipo!');

            this.cart.push({
                jina: product.jina,
                bei: this.bei,
                idadi: this.idadi,
                punguzo: this.punguzoCheck ? this.punguzo : 0,
                faida: (this.bei - (product.bei_nunua || 0)) * this.idadi,
                muda: new Date().toLocaleTimeString(),
            });

            this.bidhaaSelected = '';
            this.idadi = 1;
            this.punguzo = 0;
            alert('Bidhaa imeongezwa kwenye kikapu!');
        },

        removeFromCart(index) {
            this.cart.splice(index, 1);
        },

        clearCart() {
            if (confirm('Una uhakika unataka kufuta bidhaa zote kwenye kikapu?')) {
                this.cart = [];
            }
        },

        cartTotal() {
            return this.cart.reduce(
                (sum, item) => sum + (item.bei * item.idadi - item.punguzo),
                0
            ).toLocaleString() + '/=';
        },

        // --- Mauzo ya Kikapu ---
        checkoutCart() {
            if (this.cart.length === 0) return alert('Kikapu hakina bidhaa!');

            fetch("{{ route('mauzo.store.kikapu') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content
                },
                body: JSON.stringify({ items: this.cart })
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message || 'Mauzo yamehifadhiwa kwa mafanikio!');
                this.cart = [];
                this.showKikapu = false;
                window.location.reload();
            })
            .catch(() => alert('Kuna tatizo kwenye kuhifadhi mauzo ya kikapu!'));
        },

        // --- Kopesha (Loan Sale) ---
        openKopeshaModal() {
            if (this.cart.length === 0) {
                alert('Hakuna bidhaa kwenye kikapu.');
                return;
            }
            this.showKopesha = true;
        },

submitLoan() {
    if (!this.selectedMteja) {
        alert('Tafadhali chagua mteja.');
        return;
    }

    fetch("{{ route('mauzo.store.kopesha') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            jina: this.selectedMteja,
            items: this.cart
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            alert(data.message || 'Mauzo yamekopeshwa kwa mafanikio!');
            this.cart = [];
            this.showKopesha = false;
            this.showKikapu = false;
            // 🔁 Refresh page automatically
            window.location.reload();
        } else {
            alert('Kuna tatizo: ' + (data.message || 'Jaribu tena.'));
        }
    })
    .catch(err => console.error(err));
},


        kopeshaCart() {
            if (this.cart.length === 0) return alert('Kikapu hakina bidhaa!');
            const jina = prompt('Ingiza jina la mteja anayekopeshwa:');
            if (!jina) return;

            fetch("{{ route('mauzo.store.kopesha') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content
                },
                body: JSON.stringify({ jina, items: this.cart })
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message || 'Bidhaa zimekopeshwa kwa mafanikio!');
                this.cart = [];
                this.showKikapu = false;
                window.location.reload();
            })
            .catch(() => alert('Kuna tatizo kwenye kukopesha bidhaa!'));
        },


        // --- Sehemu ya Mauzo ---
        bidhaaSelected: '',
        idadi: 1,
        punguzo: 0,
        punguzoCheck: false,
        stock: 0,
        mapatoList: @json($mauzos),
        matumiziList: @json($matumizi),
        bidhaaList: @json($bidhaa),
        today: "{{ \Carbon\Carbon::today()->format('Y-m-d') }}",

        updateStock() {
            if (!this.bidhaaSelected) {
                this.stock = 0;
                return;
            }
            const product = this.bidhaaList.find(b => b.id == this.bidhaaSelected);
            this.stock = product ? product.idadi : 0;
            if (this.idadi > this.stock) this.idadi = this.stock;
        },

        get bei() {
            if (!this.bidhaaSelected) return 0;
            const option = document.querySelector(
                `select[name=\"bidhaa_id\"] option[value='${this.bidhaaSelected}']`
            );
            return parseFloat(option?.dataset.bei) || 0;
        },

        get jumla() {
            return (this.idadi * this.bei) - (this.punguzoCheck ? this.punguzo : 0);
        },

        decreaseStock() {
            if (this.idadi > this.stock) {
                alert('Idadi uliyoiingiza inazidi idadi iliyopo!');
                return false;
            }
            this.stock -= this.idadi;
            return true;
        },

        // --- Barcode Mauzo ---
        barcodeCart: [{ barcode: '', jina: '', bei: 0, idadi: 1, stock: 0, jumla: 0 }],

        addBarcodeRow() {
            this.barcodeCart.push({ barcode: '', jina: '', bei: 0, idadi: 1, stock: 0, jumla: 0 });
        },

        fetchBidhaa(item, index) {
            if (!item || !item.barcode) return;
            const product = this.bidhaaList.find(b => b.barcode === item.barcode);
            if (!product) {
                alert('Bidhaa haipatikani kwa barcode hii!');
                Object.assign(item, { jina: '', bei: 0, stock: 0, idadi: 1, jumla: 0 });
                return;
            }
            item.jina = product.jina;
            item.bei = parseFloat(product.bei_kuuza);
            item.stock = parseInt(product.idadi);
            item.idadi = 1;
            item.jumla = item.bei * item.idadi;
            if (index === this.barcodeCart.length - 1) this.addBarcodeRow();
        },

        updateJumla(item) {
            if (item.idadi > item.stock) {
                alert('Idadi uliyoiingiza inazidi idadi iliyopo!');
                item.idadi = item.stock;
            }
            item.jumla = item.bei * item.idadi;
        },

        removeItem(index) {
            this.barcodeCart.splice(index, 1);
        },

        barcodeCartTotal() {
            return this.barcodeCart
                .reduce((sum, item) => sum + parseFloat(item.jumla || 0), 0)
                .toLocaleString() + '/=';
        },

        submitBarcodeMauzo() {
            const payload = this.barcodeCart
                .filter(item => item.barcode)
                .map(item => ({
                    barcode: item.barcode,
                    idadi: item.idadi,
                    punguzo: 0
                }));

            if (payload.length === 0) {
                alert('Tafadhali ingiza angalau barcode moja!');
                return;
            }

            fetch("{{ route('mauzo.store.barcode') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content
                },
                body: JSON.stringify({ items: payload })
            })
            .then(async res => {
                if (!res.ok) {
                    const errData = await res.json().catch(() => ({ message: 'Kuna tatizo kwenye uhifadhi!' }));
                    throw errData;
                }
                return res.json();
            })
            .then(data => {
                alert(data.message);
                window.location.reload();
            })
            .catch(err => {
                console.error(err);
                alert(err.message || 'Kuna tatizo kwenye uhifadhi!');
            });
        },

        // --- Summary Functions ---
        filterToday(list) {
            return list.filter(item => item.created_at?.startsWith(this.today));
        },

        totalMapato() {
            return this.filterToday(this.mapatoList)
                .reduce((sum, item) => sum + parseFloat(item.jumla), 0)
                .toLocaleString() + '/=';
        },
        totalMatumizi() {
            return this.filterToday(this.matumiziList)
                .reduce((sum, item) => sum + parseFloat(item.gharama), 0)
                .toLocaleString() + '/=';
        },
        totalFaida() {
            return this.filterToday(this.mapatoList)
                .reduce((sum, item) => {
                    const beiKuuza = parseFloat(item.bidhaa.bei_kuuza || 0);
                    const beiNunua = parseFloat(item.bidhaa.bei_nunua || 0);
                    return sum + ((beiKuuza - beiNunua) * item.idadi);
                }, 0)
                .toLocaleString() + '/=';
        },
        totalFedhaLeo() {
            const mapato = this.filterToday(this.mapatoList)
                .reduce((sum, item) => sum + parseFloat(item.jumla), 0);
            const matumizi = this.filterToday(this.matumiziList)
                .reduce((sum, item) => sum + parseFloat(item.gharama), 0);
            return (mapato - matumizi).toLocaleString() + '/=';
        },
        totalFaidaHalisi() {
            const faida = this.filterToday(this.mapatoList)
                .reduce((sum, item) => {
                    const beiKuuza = parseFloat(item.bidhaa.bei_kuuza || 0);
                    const beiNunua = parseFloat(item.bidhaa.bei_nunua || 0);
                    return sum + ((beiKuuza - beiNunua) * item.idadi);
                }, 0);
            const matumizi = this.filterToday(this.matumiziList)
                .reduce((sum, item) => sum + parseFloat(item.gharama), 0);
            return (faida - matumizi).toLocaleString() + '/=';
        },
        totalJumlaKuu() {
            return this.mapatoList
                .reduce((sum, item) => sum + parseFloat(item.jumla), 0)
                .toLocaleString() + '/=';
        }
    };
    
}

</script>




</body>
</html>