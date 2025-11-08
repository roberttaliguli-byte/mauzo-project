@extends('layouts.app')

@section('title', 'Bidhaa - DEMODAY')

@section('page-title', 'Bidhaa')
@section('page-subtitle', 'Usimamizi wa bidhaa zote - ' . now()->format('d/m/Y'))

@section('content')
<div x-data="bidhaaApp()" class="space-y-6">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-emerald-200 rounded-2xl shadow-lg border border-green-100 p-6 card-hover">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-green-100 text-green-600 mr-4">
                    <i class="fas fa-boxes text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-black-500 font-medium">Jumla ya Bidhaa</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $bidhaa->count() }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-amber-200 rounded-2xl shadow-lg border border-green-100 p-6 card-hover">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-emerald-100 text-emerald-600 mr-4">
                    <i class="fas fa-cubes text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-black-500 font-medium">Bidhaa Zilizopo</p>
                    <h3 class="text-2xl font-bold text-black-800">{{ $bidhaa->where('idadi', '>', 0)->count() }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-red-200 rounded-2xl shadow-lg border border-green-100 p-6 card-hover">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-amber-100 text-amber-600 mr-4">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-black-500 font-medium">Zinazokaribia Kuisha</p>
                    <h3 class="text-2xl font-bold text-black-800">{{ $bidhaa->where('idadi', '<', 10)->count() }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-green-200 rounded-2xl shadow-lg border border-green-100 p-6 card-hover">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-red-100 text-red-600 mr-4">
                    <i class="fas fa-calendar-times text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-black-500 font-medium">Zilizo Expire</p>
                    <h3 class="text-2xl font-bold text-black-800">{{ $bidhaa->where('expiry', '<', now())->count() }}</h3>
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
                <i class="fas fa-table mr-2"></i>Taarifa za Bidhaa
            </button>
            <button 
                @click="activeTab = 'ingiza'" 
                :class="activeTab === 'ingiza' ? 'border-b-2 border-green-500 text-green-600 font-semibold' : 'text-gray-500 hover:text-gray-700'" 
                class="pb-3 px-1 transition-colors flex items-center"
            >
                <i class="fas fa-plus-circle mr-2"></i>Ingiza Bidhaa Mpya
            </button>
            <button 
                @click="activeTab = 'csv'" 
                :class="activeTab === 'csv' ? 'border-b-2 border-green-500 text-green-600 font-semibold' : 'text-gray-500 hover:text-gray-700'" 
                class="pb-3 px-1 transition-colors flex items-center"
            >
                <i class="fas fa-file-csv mr-2"></i>Ingiza kwa CSV
            </button>
        </div>
    </div>

    <!-- Bidhaa Information Tab -->
    <div x-show="activeTab === 'taarifa'" class="space-y-6">
        <!-- Search and Actions -->
        <div class="bg-amber-400 rounded-2xl shadow-lg border border-gray-100 p-6 card-hover">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-800">Orodha ya Bidhaa</h2>
                <div class="flex space-x-3">
                    <div class="relative">
                        <input 
                            type="text" 
                            placeholder="Tafuta bidhaa..." 
                            x-model="searchQuery"
                            class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        >
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                    <button 
                        onclick="window.print()" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center"
                    >
                        <i class="fas fa-print mr-2"></i> Print
                    </button>
                </div>
            </div>

            <!-- Data Table -->
            <div class="overflow-x-auto">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="bg-gradient-to-r from-green-400 to-green-700">
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white">Jina la Bidhaa</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white">Aina</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white">Kipimo</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-white">Idadi</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-white">Bei Nunua</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-white">Bei Kuuza</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white">Expiry</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-white print:hidden">Vitendo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($bidhaa as $item)
                            <tr class="hover:bg-green-50 transition-all duration-200 
                                @if($item->idadi < 10) bg-gradient-to-r from-amber-50 to-yellow-50 border-l-4 border-amber-400
                                @elseif($item->expiry < now()) bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-400
                                @else bg-white @endif"
                                x-show="searchQuery === '' || 
                                       '{{ strtolower($item->jina) }}'.includes(searchQuery.toLowerCase()) || 
                                       '{{ strtolower($item->aina) }}'.includes(searchQuery.toLowerCase())">
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center text-green-800 font-semibold">
                                            {{ substr($item->jina, 0, 1) }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-semibold text-black-900">{{ $item->jina }}</div>
                                            @if($item->barcode)
                                            <div class="text-xs text-green-600 font-medium">#{{ $item->barcode }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 border border-green-200">
                                        {{ $item->aina }}
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-black-700">{{ $item->kipimo ?: '--' }}</div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="text-sm font-semibold 
                                        @if($item->idadi < 10) text-amber-700
                                        @elseif($item->idadi == 0) text-red-700
                                        @else text-green-700 @endif">
                                        {{ $item->idadi }}
                                    </div>
                                    @if($item->idadi < 10)
                                    <div class="text-xs text-amber-600 font-medium">Karibu Kwisha</div>
                                    @endif
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="text-sm font-semibold text-gray-900">{{ number_format($item->bei_nunua, 2) }}</div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="text-sm font-bold text-green-700">{{ number_format($item->bei_kuuza, 2) }}</div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm 
                                        @if($item->expiry < now()) text-red-700 font-semibold
                                        @elseif(\Carbon\Carbon::parse($item->expiry)->diffInDays(now()) < 30) text-amber-700
                                        @else text-gray-700 @endif">
                                        {{ $item->expiry ? \Carbon\Carbon::parse($item->expiry)->format('d/m/Y') : '--' }}
                                    </div>
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
                                            @click="deleteItem({{ $item->id }}, '{{ $item->jina }}')" 
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
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fas fa-boxes text-5xl text-green-300 mb-4"></i>
                                        <p class="text-lg font-semibold text-gray-600 mb-2">Hakuna bidhaa bado.</p>
                                        <p class="text-sm text-gray-500 mb-4">Anza kwa kuongeza bidhaa yako ya kwanza</p>
                                        <button 
                                            @click="activeTab = 'ingiza'" 
                                            class="bg-gradient-to-r from-green-600 to-green-700 text-white px-6 py-3 rounded-lg hover:from-green-700 hover:to-green-800 transition-all duration-300 transform hover:scale-105 flex items-center shadow-lg"
                                        >
                                            <i class="fas fa-plus-circle mr-2"></i> Ingiza Bidhaa ya Kwanza
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

           
        
        </div>
    </div>

    <!-- Ingiza Bidhaa Tab -->
    <div x-show="activeTab === 'ingiza'" class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 card-hover">
        <h2 class="text-xl font-bold text-gray-800 mb-6">Ingiza Bidhaa Mpya</h2>
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
            class="space-y-6"
        >
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Jina -->
                <div class="relative">
                    <input 
                        type="text" 
                        name="jina" 
                        placeholder=" " 
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                        required
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Jina la Bidhaa
                    </label>
                </div>

                <!-- Aina -->
                <div class="relative">
                    <input 
                        type="text" 
                        name="aina" 
                        placeholder=" " 
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                        required
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Aina ya Bidhaa
                    </label>
                </div>

                <!-- Kipimo -->
                <div class="relative">
                    <input 
                        type="text" 
                        name="kipimo" 
                        placeholder=" " 
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Kipimo (kg, ltr, pcs)
                    </label>
                </div>

                <!-- Idadi -->
                <div class="relative">
                    <input 
                        type="number" 
                        name="idadi" 
                        placeholder=" " 
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                        required
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Idadi ya Bidhaa
                    </label>
                </div>

                <!-- Bei Nunua -->
                <div class="relative">
                    <input 
                        type="number" 
                        step="0.01" 
                        name="bei_nunua" 
                        x-model="nunua" 
                        placeholder=" " 
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                        required
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Bei ya Kununua (TZS)
                    </label>
                </div>

                <!-- Bei Kuuza -->
                <div class="relative">
                    <input 
                        type="number" 
                        step="0.01" 
                        name="bei_kuuza" 
                        x-model="kuuza" 
                        placeholder=" " 
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                        required
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Bei ya Kuuza (TZS)
                    </label>
                    <!-- Error Message -->
                    <p x-show="error" x-text="error" class="text-red-600 font-medium mt-2 text-sm"></p>
                </div>

                <!-- Expiry -->
                <div class="relative">
                    <input 
                        type="date" 
                        name="expiry" 
                        placeholder=" " 
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                        required
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Tarehe ya Mwisho (Expiry)
                    </label>
                </div>

                <!-- Barcode -->
                <div class="relative">
                    <input 
                        type="text" 
                        name="barcode" 
                        placeholder=" " 
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Namba ya Barcode
                    </label>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4 pt-6 border-t border-gray-200">
                <button 
                    type="submit" 
                    class="bg-gradient-to-r from-green-600 to-green-700 text-white px-8 py-3 rounded-lg hover:from-green-700 hover:to-green-800 transition-all duration-300 transform hover:scale-105 flex items-center shadow-lg"
                >
                    <i class="fas fa-save mr-2"></i> Hifadhi Bidhaa
                </button>
                <button 
                    type="reset" 
                    @click="error=''; nunua=''; kuuza='';" 
                    class="bg-gray-300 text-gray-700 px-8 py-3 rounded-lg hover:bg-gray-400 transition-colors flex items-center"
                >
                    <i class="fas fa-redo mr-2"></i> Safisha Fomu
                </button>
            </div>
        </form>
    </div>

    <!-- CSV Upload Tab -->
    <div x-show="activeTab === 'csv'" class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 card-hover">
        <h2 class="text-xl font-bold text-gray-800 mb-6">Ingiza Bidhaa kwa CSV</h2>

        <div class="bg-gradient-to-r from-green-300 to-green-50 border border-blue-200 rounded-xl p-6 mb-6">
            <div class="flex items-center mb-4">
                <div class="p-3 rounded-lg bg-orange-200 text-black-600 mr-4">
                    <i class="fas fa-info-circle text-lg"></i>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-black-800">Maelekezo ya Upakiaji</h4>
                    <p class="text-black-700 mt-1">Hakikisha faili lako lina muundo ufuatao kabla ya kupakia</p>
                </div>
            </div>
            
            <div class="flex space-x-4">
                <a href="{{ route('bidhaa.downloadSample') }}" 
                   class="bg-gradient-to-r from-yellow-600 to-orange-400 text-white px-6 py-3 rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-300 transform hover:scale-105 flex items-center shadow-lg">
                    <i class="fas fa-download mr-2"></i> Pakua Faili Sampuli
                </a>
            </div>
        </div>

        <!-- Upload Form -->
        <form method="POST" action="{{ route('bidhaa.uploadCSV') }}" enctype="multipart/form-data" class="mb-8">
            @csrf
            <div class="border-2 border-dashed border-green-300 rounded-xl p-8 text-center bg-green-50 transition-all duration-300 hover:bg-green-100">
                <i class="fas fa-file-csv text-4xl text-green-400 mb-4"></i>
                <p class="text-lg font-semibold text-green-800 mb-2">Chagua faili la CSV</p>
                <p class="text-green-600 mb-4">Aina zinazokubalika: .csv, .txt</p>
                <input type="file" name="csv_file" accept=".csv,.txt" 
                       class="block w-full text-sm text-green-700 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-600 file:text-white hover:file:bg-green-700" 
                       required>
                <button type="submit" 
                        class="mt-4 bg-gradient-to-r from-green-600 to-green-700 text-white px-8 py-3 rounded-lg hover:from-green-700 hover:to-green-800 transition-all duration-300 transform hover:scale-105 flex items-center mx-auto shadow-lg">
                    <i class="fas fa-upload mr-2"></i> Pakia Faili la CSV
                </button>
            </div>
        </form>

        <!-- Upload Results -->
        @if(session('successCount') > 0)
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-300 rounded-xl p-6 mb-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-green-100 text-green-600 mr-4">
                        <i class="fas fa-check-circle text-lg"></i>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold text-green-800">Upakiaji Umekamilika!</h4>
                        <p class="text-green-700 mt-1">Bidhaa {{ session('successCount') }} zimeongezwa kikamilifu kwenye mfumo</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('errorsList') && count(session('errorsList')) > 0)
            <div class="bg-gradient-to-r from-red-50 to-pink-50 border border-red-300 rounded-xl p-6 mb-6">
                <div class="flex items-center mb-4">
                    <div class="p-3 rounded-lg bg-red-100 text-red-600 mr-4">
                        <i class="fas fa-exclamation-triangle text-lg"></i>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold text-red-800">Hitilafu katika Upakiaji</h4>
                        <p class="text-red-700 mt-1">Bidhaa zifuatazo hazikupakiwa kikamilifu:</p>
                    </div>
                </div>
                <ul class="space-y-2">
                    @foreach(session('errorsList') as $error)
                        <li class="flex items-center text-red-700">
                            <i class="fas fa-times-circle text-red-500 mr-2"></i>
                            {{ $error }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Sample File Structure -->
        <div class="border-t border-gray-200 pt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-table mr-2 text-green-600"></i>Muundo wa Faili la Sampuli
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm border border-green-200 rounded-lg overflow-hidden">
                    <thead>
                        <tr class="bg-gradient-to-r from-green-600 to-green-700">
                            <th class="px-4 py-3 text-left text-sm font-semibold text-white">Jina</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-white">Aina</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-white">Kipimo</th>
                            <th class="px-4 py-3 text-center text-sm font-semibold text-white">Idadi</th>
                            <th class="px-4 py-3 text-right text-sm font-semibold text-white">Bei_Nunua</th>
                            <th class="px-4 py-3 text-right text-sm font-semibold text-white">Bei_Kuuza</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-white">Expiry</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-white">Barcode</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-green-100">
                        <tr class="hover:bg-green-50">
                            <td class="px-4 py-3 text-green-800 font-medium">Soda</td>
                            <td class="px-4 py-3 text-gray-700">Vinywaji</td>
                            <td class="px-4 py-3 text-gray-700">500ml</td>
                            <td class="px-4 py-3 text-center text-green-700 font-semibold">100</td>
                            <td class="px-4 py-3 text-right text-gray-700">600.00</td>
                            <td class="px-4 py-3 text-right text-green-700 font-semibold">1000.00</td>
                            <td class="px-4 py-3 text-gray-700">2025-12-31</td>
                            <td class="px-4 py-3 text-blue-600">1234567890123</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
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
        class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4"
        @click.outside="showEditModal = false"
    >
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Badili Taarifa za Bidhaa</h3>
        </div>
        <form :action="'/bidhaa/' + editingItem.id" method="POST" class="p-6">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="relative">
                    <input 
                        type="text" 
                        name="jina" 
                        x-model="editingItem.jina"
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                        required
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Jina la Bidhaa
                    </label>
                </div>

                <div class="relative">
                    <input 
                        type="text" 
                        name="aina" 
                        x-model="editingItem.aina"
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                        required
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Aina ya Bidhaa
                    </label>
                </div>

                <div class="relative">
                    <input 
                        type="text" 
                        name="kipimo" 
                        x-model="editingItem.kipimo"
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Kipimo
                    </label>
                </div>

                <div class="relative">
                    <input 
                        type="number" 
                        name="idadi" 
                        x-model="editingItem.idadi"
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                        required
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Idadi
                    </label>
                </div>

                <div class="relative">
                    <input 
                        type="number" 
                        step="0.01" 
                        name="bei_nunua" 
                        x-model="editingItem.bei_nunua"
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                        required
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Bei Nunua
                    </label>
                </div>

                <div class="relative">
                    <input 
                        type="number" 
                        step="0.01" 
                        name="bei_kuuza" 
                        x-model="editingItem.bei_kuuza"
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                        required
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Bei Kuuza
                    </label>
                </div>

                <div class="relative">
                    <input 
                        type="date" 
                        name="expiry" 
                        x-model="editingItem.expiry"
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Tarehe ya Expiry
                    </label>
                </div>

                <div class="relative">
                    <input 
                        type="text" 
                        name="barcode" 
                        x-model="editingItem.barcode"
                        class="peer border border-gray-300 rounded-lg w-full p-4 pt-6 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                    <label class="absolute left-4 top-2 text-gray-400 text-sm transition-all
                                  peer-placeholder-shown:top-4 peer-placeholder-shown:text-gray-400
                                  peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-green-600
                                  peer-focus:text-sm font-medium">
                        Barcode
                    </label>
                </div>
            </div>
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 mt-6">
                <button 
                    type="button" 
                    @click="showEditModal = false"
                    class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors"
                >
                    Ghairi
                </button>
                <button 
                    type="submit" 
                    class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
                >
                    Hifadhi Mabadiliko
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div 
    x-show="showDeleteModal" 
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
        @click.outside="showDeleteModal = false"
    >
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Thibitisha Kufuta Bidhaa</h3>
        </div>
        <div class="p-6">
            <div class="flex items-center justify-center mb-4">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
            </div>
            <p class="text-gray-700 mb-6 text-center">
                Una uhakika unataka kufuta bidhaa "<span x-text="deletingItemName" class="font-semibold"></span>"?
                <br>Hatua hii haiwezi kutenduliwa.
            </p>
            <div class="flex justify-center space-x-3">
                <button 
                    @click="showDeleteModal = false"
                    class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors"
                >
                    Ghairi
                </button>
                <form :action="'/bidhaa/' + deletingItemId" method="POST">
                    @csrf
                    @method('DELETE')
                    <button 
                        type="submit" 
                        class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
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
function bidhaaApp() {
    return {
        activeTab: 'taarifa',
        searchQuery: '',
        showEditModal: false,
        showDeleteModal: false,
        editingItem: {},
        deletingItemId: null,
        deletingItemName: '',
        nunua: '',
        kuuza: '',
        error: '',
        
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