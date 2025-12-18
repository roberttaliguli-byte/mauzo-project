@extends('layouts.app')

@section('title', 'Bidhaa - DEMODAY')

@section('page-title', 'Bidhaa')
@section('page-subtitle', 'Usimamizi wa bidhaa zote - ' . now()->format('d/m/Y'))

@section('content')
<div class="space-y-6">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow-lg border border-green-100 p-6 card-hover">
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
        
        <div class="bg-white rounded-2xl shadow-lg border border-green-100 p-6 card-hover">
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
        
        <div class="bg-white rounded-2xl shadow-lg border border-green-100 p-6 card-hover">
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
        
        <div class="bg-white rounded-2xl shadow-lg border border-green-100 p-6 card-hover">
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
                id="taarifa-tab" 
                class="tab-button pb-3 px-1 transition-colors flex items-center border-b-2 border-green-500 text-green-600 font-semibold"
                data-tab="taarifa"
            >
                <i class="fas fa-table mr-2"></i>Taarifa za Bidhaa
            </button>
            <button 
                id="ingiza-tab" 
                class="tab-button pb-3 px-1 transition-colors flex items-center text-gray-500 hover:text-gray-700"
                data-tab="ingiza"
            >
                <i class="fas fa-plus-circle mr-2"></i>Ingiza Bidhaa Mpya
            </button>
            <button 
                id="csv-tab" 
                class="tab-button pb-3 px-1 transition-colors flex items-center text-gray-500 hover:text-gray-700"
                data-tab="csv"
            >
                <i class="fas fa-file-csv mr-2"></i>Ingiza kwa CSV
            </button>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-300 rounded-xl p-4">
            <div class="flex items-center">
                <div class="p-2 rounded-lg bg-green-100 text-green-600 mr-3">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <p class="text-green-800 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-gradient-to-r from-red-50 to-pink-50 border border-red-300 rounded-xl p-4">
            <div class="flex items-center">
                <div class="p-2 rounded-lg bg-red-100 text-red-600 mr-3">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div>
                    <h4 class="text-red-800 font-medium">Hitilafu katika Uwasilishaji</h4>
                    <ul class="list-disc list-inside text-red-700 mt-1">
                        @foreach($errors->all() as $error)
                            <li class="text-sm">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- TAB 1: Taarifa za Bidhaa -->
    <div id="taarifa-tab-content" class="space-y-6 tab-content">
        <!-- Search and Actions -->
        <div class="bg-gray-200 rounded-2xl shadow-lg border border-gray-100 p-6 card-hover">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-800">Orodha ya Bidhaa</h2>
                <div class="flex space-x-3">
                    <div class="relative">
                        <input 
                            type="text" 
                            id="search-input"
                            placeholder="Tafuta bidhaa..." 
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
                    <tbody id="products-tbody" class="divide-y divide-gray-200">
                        @forelse($bidhaa as $item)
                            <tr class="product-row hover:bg-green-50 transition-all duration-200 
                                @if($item->idadi < 10) bg-gradient-to-r from-amber-50 to-yellow-50 border-l-4 border-amber-400
                                @elseif($item->expiry < now()) bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-400
                                @else bg-white @endif"
                                data-product='@json($item)'>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center text-green-800 font-semibold">
                                            {{ substr($item->jina, 0, 1) }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-semibold text-black-900 product-name">{{ $item->jina }}</div>
                                            @if($item->barcode)
                                            <div class="text-xs text-green-600 font-medium">#{{ $item->barcode }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 border border-green-200 product-type">
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
                                            class="edit-product-btn text-amber-600 hover:text-amber-800 transition-colors transform hover:scale-110"
                                            title="Badili"
                                            data-id="{{ $item->id }}"
                                        >
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button 
                                            class="delete-product-btn text-red-500 hover:text-red-700 transition-colors transform hover:scale-110"
                                            title="Futa"
                                            data-id="{{ $item->id }}"
                                            data-name="{{ $item->jina }}"
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
                                            id="go-to-add-product"
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

    <!-- TAB 2: Ingiza Bidhaa Mpya -->
    <div id="ingiza-tab-content" class="tab-content hidden">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 card-hover">
            <h2 class="text-xl font-bold text-gray-800 mb-6">Ingiza Bidhaa Mpya</h2>
            <form method="POST" action="{{ route('bidhaa.store') }}" id="product-form" class="space-y-6">
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
                            id="buy-price"
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
                            id="sell-price"
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
                        <p id="price-error" class="text-red-600 font-medium mt-2 text-sm hidden"></p>
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
                        id="reset-form"
                        class="bg-gray-300 text-gray-700 px-8 py-3 rounded-lg hover:bg-gray-400 transition-colors flex items-center"
                    >
                        <i class="fas fa-redo mr-2"></i> Safisha Fomu
                    </button>
                </div>
            </form>
        </div>
    </div>
<!-- TAB 3: CSV Upload -->
<div id="csv-tab-content" class="tab-content hidden">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 card-hover">
        <h2 class="text-xl font-bold text-gray-800 mb-6">Ingiza Bidhaa kwa CSV</h2>

        <!-- Upload Section - Side by Side Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Upload Form - Left Side -->
            <div class="bg-gradient-to-br from-green-50 to-emerald-50 border border-green-200 rounded-xl p-6">
                <div class="text-center mb-4">
                    <i class="fas fa-file-csv text-4xl text-green-500 mb-3"></i>
                    <h3 class="text-lg font-semibold text-green-800 mb-2">Pakia Faili la CSV</h3>
                    <p class="text-sm text-green-600 mb-4">Chagua faili lako la CSV lenye orodha ya bidhaa</p>
                </div>
                
                <form method="POST" action="{{ route('bidhaa.uploadCSV') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-4">
                        <div class="border-2 border-dashed border-green-300 rounded-lg p-4 bg-white text-center transition-all duration-200 hover:border-green-400">
                            <input type="file" name="csv_file" accept=".csv,.txt" 
                                   class="block w-full text-sm text-green-700 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-green-500 file:text-white hover:file:bg-green-600 cursor-pointer" 
                                   required>
                            <p class="text-xs text-gray-500 mt-2">Aina: .csv, .txt | Ukubwa: hadi 10MB</p>
                        </div>
                        <button type="submit" 
                                class="w-full bg-gradient-to-r from-green-500 to-green-600 text-white px-4 py-3 rounded-lg hover:from-green-600 hover:to-green-700 transition-all duration-200 flex items-center justify-center shadow-md">
                            <i class="fas fa-upload mr-2"></i> Pakia Faili
                        </button>
                    </div>
                </form>
            </div>

            <!-- Download Sample - Right Side -->
            <div class="bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-200 rounded-xl p-6">
                <div class="text-center mb-4">
                    <i class="fas fa-download text-4xl text-amber-500 mb-3"></i>
                    <h3 class="text-lg font-semibold text-amber-800 mb-2">Pakua Faili Sampuli</h3>
                    <p class="text-sm text-amber-600 mb-4">Pakua faili la mfano kwa muundo sahihi</p>
                </div>
                
                <div class="space-y-4">
                    <div class="bg-white border border-amber-200 rounded-lg p-4 text-center">
                        <i class="fas fa-table text-2xl text-amber-400 mb-2"></i>
                        <p class="text-sm text-amber-700 font-medium">Muundo Sahihi</p>
                        <p class="text-xs text-gray-600 mt-1">Orodha kamili ya bidhaa</p>
                    </div>
                    <a href="{{ route('bidhaa.downloadSample') }}" 
                       class="w-full bg-gradient-to-r from-amber-500 to-orange-500 text-white px-4 py-3 rounded-lg hover:from-amber-600 hover:to-orange-600 transition-all duration-200 flex items-center justify-center shadow-md">
                        <i class="fas fa-file-download mr-2"></i> Pakua Sampuli
                    </a>
                </div>
            </div>
        </div>

        <!-- Upload Results -->
        @if(session('successCount') > 0)
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-300 rounded-xl p-4 mb-6">
                <div class="flex items-center">
                    <div class="p-2 rounded-lg bg-green-100 text-green-600 mr-3">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-green-800">Upakiaji Umekamilika!</h4>
                        <p class="text-green-700 text-sm mt-1">Bidhaa {{ session('successCount') }} zimeongezwa kikamilifu</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('errorsList') && count(session('errorsList')) > 0)
            <div class="bg-gradient-to-r from-red-50 to-pink-50 border border-red-300 rounded-xl p-4 mb-6">
                <div class="flex items-start">
                    <div class="p-2 rounded-lg bg-red-100 text-red-600 mr-3 mt-1">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-sm font-semibold text-red-800 mb-2">Hitilafu katika Upakiaji</h4>
                        <div class="max-h-32 overflow-y-auto pr-2">
                            <ul class="space-y-1 text-sm">
                                @foreach(session('errorsList') as $error)
                                    <li class="flex items-start text-red-700">
                                        <i class="fas fa-times-circle text-red-500 mr-2 mt-0.5 text-xs"></i>
                                        <span class="flex-1">{{ $error }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Instructions -->
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
            <div class="flex items-start">
                <div class="p-2 rounded-lg bg-blue-100 text-blue-600 mr-3">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-blue-800 mb-1">Maelekezo Muhimu</h4>
                    <ul class="text-sm text-blue-700 space-y-1">
                        <li class="flex items-start">
                            <span class="mr-2">•</span>
                            <span>Faili lazima liwjazwe kwa umakini, Data ya tarehe lazima iwe katika muundo: YYYY-MM-DD ilivyoonyeshwa hapo chini</span>
                        </li>
                       
                    </ul>
                </div>
            </div>
        </div>

        <!-- Sample File Structure -->
        <div class="border border-gray-200 rounded-xl overflow-hidden">
            <div class="bg-gradient-to-r from-green-600 to-green-700 px-4 py-3">
                <h3 class="text-sm font-semibold text-white flex items-center">
                    <i class="fas fa-table mr-2"></i>Muundo wa Faili la CSV
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="bg-green-50">
                            <th class="px-3 py-2 text-left font-semibold text-green-800 border-b border-green-200">Jina</th>
                            <th class="px-3 py-2 text-left font-semibold text-green-800 border-b border-green-200">Aina</th>
                            <th class="px-3 py-2 text-left font-semibold text-green-800 border-b border-green-200">Kipimo</th>
                            <th class="px-3 py-2 text-center font-semibold text-green-800 border-b border-green-200">Idadi</th>
                            <th class="px-3 py-2 text-right font-semibold text-green-800 border-b border-green-200">Bei_Nunua</th>
                            <th class="px-3 py-2 text-right font-semibold text-green-800 border-b border-green-200">Bei_Kuuza</th>
                            <th class="px-3 py-2 text-left font-semibold text-green-800 border-b border-green-200">Expiry</th>
                            <th class="px-3 py-2 text-left font-semibold text-green-800 border-b border-green-200">Barcode</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 text-green-700 font-medium">Soda</td>
                            <td class="px-3 py-2 text-gray-600">Vinywaji</td>
                            <td class="px-3 py-2 text-gray-600">500ml</td>
                            <td class="px-3 py-2 text-center text-green-600 font-semibold">100</td>
                            <td class="px-3 py-2 text-right text-gray-600">600.00</td>
                            <td class="px-3 py-2 text-right text-green-600 font-semibold">1000.00</td>
                            <td class="px-3 py-2 text-gray-600">2025-12-31</td>
                            <td class="px-3 py-2 text-blue-600 font-mono">1234567890123</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 text-green-700 font-medium">Mchele</td>
                            <td class="px-3 py-2 text-gray-600">Chakula</td>
                            <td class="px-3 py-2 text-gray-600">1kg</td>
                            <td class="px-3 py-2 text-center text-green-600 font-semibold">50</td>
                            <td class="px-3 py-2 text-right text-gray-600">2500.00</td>
                            <td class="px-3 py-2 text-right text-green-600 font-semibold">3500.00</td>
                            <td class="px-3 py-2 text-gray-600">2026-06-30</td>
                            <td class="px-3 py-2 text-blue-600 font-mono">9876543210987</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="bg-gray-50 px-3 py-2 border-t border-gray-200">
                <p class="text-xs text-gray-600 text-center">Faili lako lazima liwe na vichwa hivi na data inayofuata</p>
            </div>
        </div>
    </div>
</div>
</div>

<!-- Edit Modal -->
<div id="edit-modal" class="modal fixed inset-0 z-50 flex items-center justify-center hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 z-50">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Badili Taarifa za Bidhaa</h3>
        </div>
        <form id="edit-form" method="POST" class="p-6">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="relative">
                    <input 
                        type="text" 
                        name="jina" 
                        id="edit-jina"
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
                        id="edit-aina"
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
                        id="edit-kipimo"
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
                        id="edit-idadi"
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
                        id="edit-bei-nunua"
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
                        id="edit-bei-kuuza"
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
                        id="edit-expiry"
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
                        id="edit-barcode"
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
                    id="close-edit-modal"
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
<div id="delete-modal" class="modal fixed inset-0 z-50 flex items-center justify-center hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 z-50">
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
                Una uhakika unataka kufuta bidhaa "<span id="delete-product-name" class="font-semibold"></span>"?
                <br>Hatua hii haiwezi kutenduliwa.
            </p>
            <div class="flex justify-center space-x-3">
                <button 
                    id="cancel-delete"
                    class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors"
                >
                    Ghairi
                </button>
                <form id="delete-form" method="POST">
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

@push('styles')
<style>
.modal {
    transition: opacity 0.3s ease;
}

.tab-content {
    transition: opacity 0.3s ease;
}

.hidden {
    display: none !important;
}

.product-row.hidden {
    display: none;
}
</style>
@endpush

@push('scripts')
<script>
class BidhaaManager {
    constructor() {
        this.currentTab = 'taarifa';
        this.searchQuery = '';
        this.init();
    }

    init() {
        this.bindEvents();
        this.showTab('taarifa');
    }

    bindEvents() {
        // Tab navigation
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', (e) => {
                const tab = e.target.closest('.tab-button').dataset.tab;
                this.showTab(tab);
            });
        });

        // Search functionality
        const searchInput = document.getElementById('search-input');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                this.searchQuery = e.target.value.toLowerCase();
                this.filterProducts();
            });
        }

        // Go to add product button
        const goToAddProductBtn = document.getElementById('go-to-add-product');
        if (goToAddProductBtn) {
            goToAddProductBtn.addEventListener('click', () => {
                this.showTab('ingiza');
            });
        }

        // Product actions
        this.bindProductActions();

        // Form validation
        this.bindFormValidation();

        // Modal events
        this.bindModalEvents();
    }

    showTab(tabName) {
        // Update tabs
        document.querySelectorAll('.tab-button').forEach(button => {
            if (button.dataset.tab === tabName) {
                button.classList.add('border-b-2', 'border-green-500', 'text-green-600', 'font-semibold');
                button.classList.remove('text-gray-500');
            } else {
                button.classList.remove('border-b-2', 'border-green-500', 'text-green-600', 'font-semibold');
                button.classList.add('text-gray-500');
            }
        });

        // Update tab content
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        
        const activeContent = document.getElementById(`${tabName}-tab-content`);
        if (activeContent) {
            activeContent.classList.remove('hidden');
        }

        this.currentTab = tabName;
    }

    bindProductActions() {
        // Edit buttons
        document.querySelectorAll('.edit-product-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                const productId = e.target.closest('.edit-product-btn').dataset.id;
                const row = e.target.closest('.product-row');
                const productData = JSON.parse(row.dataset.product);
                this.editProduct(productData);
            });
        });

        // Delete buttons
        document.querySelectorAll('.delete-product-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                const productId = e.target.closest('.delete-product-btn').dataset.id;
                const productName = e.target.closest('.delete-product-btn').dataset.name;
                this.deleteProduct(productId, productName);
            });
        });
    }

    bindFormValidation() {
        const productForm = document.getElementById('product-form');
        const buyPriceInput = document.getElementById('buy-price');
        const sellPriceInput = document.getElementById('sell-price');
        const priceError = document.getElementById('price-error');
        const resetButton = document.getElementById('reset-form');

        if (productForm) {
            productForm.addEventListener('submit', (e) => {
                const buyPrice = parseFloat(buyPriceInput.value);
                const sellPrice = parseFloat(sellPriceInput.value);

                if (buyPrice > sellPrice) {
                    e.preventDefault();
                    priceError.textContent = '⚠️ Bei ya kununua haiwezi kuwa kubwa kuliko bei ya kuuza!';
                    priceError.classList.remove('hidden');
                } else {
                    priceError.classList.add('hidden');
                }
            });
        }

        if (resetButton) {
            resetButton.addEventListener('click', () => {
                priceError.classList.add('hidden');
            });
        }
    }

    bindModalEvents() {
        // Edit modal
        const editModal = document.getElementById('edit-modal');
        const closeEditBtn = document.getElementById('close-edit-modal');

        closeEditBtn.addEventListener('click', () => {
            editModal.classList.add('hidden');
        });

        editModal.addEventListener('click', (e) => {
            if (e.target === editModal || e.target.classList.contains('modal-overlay')) {
                editModal.classList.add('hidden');
            }
        });

        // Delete modal
        const deleteModal = document.getElementById('delete-modal');
        const cancelDeleteBtn = document.getElementById('cancel-delete');

        cancelDeleteBtn.addEventListener('click', () => {
            deleteModal.classList.add('hidden');
        });

        deleteModal.addEventListener('click', (e) => {
            if (e.target === deleteModal || e.target.classList.contains('modal-overlay')) {
                deleteModal.classList.add('hidden');
            }
        });

        // Close modals on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                editModal.classList.add('hidden');
                deleteModal.classList.add('hidden');
            }
        });
    }

    filterProducts() {
        const rows = document.querySelectorAll('.product-row');
        
        rows.forEach(row => {
            const name = row.querySelector('.product-name').textContent.toLowerCase();
            const type = row.querySelector('.product-type').textContent.toLowerCase();
            
            const matches = name.includes(this.searchQuery) || 
                           type.includes(this.searchQuery);
            
            if (matches || this.searchQuery === '') {
                row.classList.remove('hidden');
            } else {
                row.classList.add('hidden');
            }
        });
    }

    editProduct(product) {
        // Populate edit form
        document.getElementById('edit-jina').value = product.jina;
        document.getElementById('edit-aina').value = product.aina;
        document.getElementById('edit-kipimo').value = product.kipimo || '';
        document.getElementById('edit-idadi').value = product.idadi;
        document.getElementById('edit-bei-nunua').value = product.bei_nunua;
        document.getElementById('edit-bei-kuuza').value = product.bei_kuuza;
        document.getElementById('edit-expiry').value = product.expiry || '';
        document.getElementById('edit-barcode').value = product.barcode || '';

        // Set form action
        document.getElementById('edit-form').action = `/bidhaa/${product.id}`;

        // Show modal
        document.getElementById('edit-modal').classList.remove('hidden');
    }

    deleteProduct(productId, productName) {
        // Populate delete modal
        document.getElementById('delete-product-name').textContent = productName;
        document.getElementById('delete-form').action = `/bidhaa/${productId}`;

        // Show modal
        document.getElementById('delete-modal').classList.remove('hidden');
    }
}

// Initialize the application when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    new BidhaaManager();
});
</script>
@endpush