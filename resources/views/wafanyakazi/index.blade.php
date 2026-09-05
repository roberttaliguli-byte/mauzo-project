@extends('layouts.app')

@section('title', 'Wafanyakazi')

@section('page-title', 'Wafanyakazi')
@section('page-subtitle', now()->format('d/m/Y'))

@section('content')
<div class="space-y-4" id="app-container" data-current-page="{{ request()->get('page', 1) }}">
    <!-- Notifications -->
    <div id="notification-container" class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-sm px-4 pointer-events-none">
        @if(session('success'))
        <div class="rounded border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 mb-2 shadow-sm">
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="rounded border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800 mb-2 shadow-sm">
            {{ session('error') }}
        </div>
        @endif
    </div>

    <!-- Stats with Links -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="bg-white p-3 rounded-lg border border-emerald-200 shadow-sm">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Jumla ya Wafanyakazi</p>
                    <p class="text-xl font-bold text-emerald-700">{{ $totalEmployees }}</p>
                </div>
                <i class="fas fa-users text-emerald-500 text-lg"></i>
            </div>
        </div>
        <div class="bg-white p-3 rounded-lg border border-blue-200 shadow-sm">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Wanaoweza Kuingia</p>
                    <p class="text-xl font-bold text-blue-700">{{ $activeEmployees }}</p>
                </div>
                <i class="fas fa-user-check text-blue-500 text-lg"></i>
            </div>
        </div>
        <div class="bg-white p-3 rounded-lg border border-purple-200 shadow-sm">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Wanaume</p>
                    <p class="text-xl font-bold text-purple-700">{{ $maleEmployees }}</p>
                </div>
                <i class="fas fa-male text-purple-500 text-lg"></i>
            </div>
        </div>
        <div class="bg-white p-3 rounded-lg border border-pink-200 shadow-sm">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Wanawake</p>
                    <p class="text-xl font-bold text-pink-700">{{ $femaleEmployees }}</p>
                </div>
                <i class="fas fa-female text-pink-500 text-lg"></i>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm">
        <div class="flex flex-wrap">
            <button data-tab="taarifa" class="tab-button flex-1 py-3 px-4 text-sm font-medium border-r border-gray-200 bg-emerald-50 text-emerald-700">
                <i class="fas fa-table mr-2"></i> Orodha
            </button>
            <button data-tab="sajili" class="tab-button flex-1 py-3 px-4 text-sm font-medium text-gray-600 hover:bg-gray-50">
                <i class="fas fa-plus mr-2"></i> Sajili
            </button>
            @if(Auth::guard('web')->check())
            <button data-tab="salary" class="tab-button flex-1 py-3 px-4 text-sm font-medium text-gray-600 hover:bg-gray-50">
                <i class="fas fa-money-bill-wave mr-2"></i> Mishahara
            </button>
            @endif
        </div>
    </div>

    <!-- TAB 1: Orodha -->
    <div id="taarifa-tab-content" class="tab-content space-y-3">
        <!-- Search Bar -->
        <div class="bg-white p-3 rounded-lg border border-gray-200 shadow-sm">
            <div class="flex flex-col sm:flex-row gap-2 sm:items-center">
                <div class="flex-1 relative">
                    <input 
                        type="text" 
                        id="search-input"
                        placeholder="Tafuta mfanyakazi, simu, email..." 
                        class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500"
                        value="{{ request()->search }}"
                    >
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                <div class="flex gap-2">
                    <button onclick="printWafanyakazi()" class="px-3 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 text-sm font-medium">
                        <i class="fas fa-print mr-1"></i> Print
                    </button>
                    <button onclick="exportPDF()" class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm font-medium">
                        <i class="fas fa-file-pdf mr-1"></i> PDF
                    </button>
                </div>
            </div>
        </div>

        <!-- Wafanyakazi Table -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-emerald-50">
                            <th class="px-4 py-2 text-left font-medium text-emerald-800">Mfanyakazi</th>
                            <th class="px-4 py-2 text-left font-medium text-emerald-800 hidden sm:table-cell">Mawasiliano</th>
                            <th class="px-4 py-2 text-center font-medium text-emerald-800">Jinsia</th>
                            <th class="px-4 py-2 text-center font-medium text-emerald-800 hidden md:table-cell">Uwezo</th>
                            <th class="px-4 py-2 text-center font-medium text-emerald-800 hidden md:table-cell">Umri</th>
                            <th class="px-4 py-2 text-center font-medium text-emerald-800">Hali</th>
                            <th class="px-4 py-2 text-center font-medium text-emerald-800 print:hidden">Vitendo</th>
                        </tr>
                    </thead>
                    <tbody id="wafanyakazi-tbody" class="divide-y divide-gray-100">
                        @forelse($wafanyakazi as $item)
                            <tr class="employee-row hover:bg-gray-50" data-employee='@json($item)'>
                                <td class="px-4 py-2">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-800 font-bold text-sm mr-2">
                                            {{ substr($item->jina, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900 text-sm">{{ $item->jina }}</div>
                                            <div class="text-xs text-gray-500 sm:hidden">{{ $item->simu ?? '--' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-2 hidden sm:table-cell">
                                    <div class="text-xs text-gray-700">{{ $item->simu ?? '--' }}</div>
                                    <div class="text-xs text-gray-500">{{ $item->barua_pepe ?? '--' }}</div>
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium 
                                        @if($item->jinsia === 'Mwanaume') bg-blue-100 text-blue-800
                                        @else bg-pink-100 text-pink-800 @endif">
                                        @if($item->jinsia === 'Mwanaume')
                                            <i class="fas fa-mars mr-1"></i>
                                        @else
                                            <i class="fas fa-venus mr-1"></i>
                                        @endif
                                        {{ $item->jinsia }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-center hidden md:table-cell">
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium 
                                        @if($item->uwezo === 'mkubwa') bg-purple-100 text-purple-800
                                        @else bg-gray-100 text-gray-600 @endif">
                                        @if($item->uwezo === 'mkubwa')
                                            <i class="fas fa-crown mr-1"></i> Mkubwa
                                        @else
                                            <i class="fas fa-user mr-1"></i> Mdogo
                                        @endif
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-center hidden md:table-cell">
                                    @if($item->tarehe_kuzaliwa)
                                        <span class="text-xs text-gray-700">
                                            {{ \Carbon\Carbon::parse($item->tarehe_kuzaliwa)->age }} yrs
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400">--</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium 
                                        @if($item->getini === 'ingia') bg-green-100 text-green-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        @if($item->getini === 'ingia')
                                            <i class="fas fa-check-circle mr-1"></i> Ingia
                                        @else
                                            <i class="fas fa-pause-circle mr-1"></i> Simama
                                        @endif
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-center print:hidden">
                                    <div class="flex justify-center space-x-2">
                                        <button class="view-employee-btn text-blue-600 hover:text-blue-800"
                                                data-id="{{ $item->id }}" title="Angalia Maelezo">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="edit-employee-btn text-emerald-600 hover:text-emerald-800"
                                                data-id="{{ $item->id }}" title="Badili">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="delete-employee-btn text-red-600 hover:text-red-800"
                                                data-id="{{ $item->id }}" data-name="{{ $item->jina }}" title="Futa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @if($item->simu)
                                        <a href="tel:{{ $item->simu }}" class="text-green-600 hover:text-green-800" title="Piga Simu">
                                            <i class="fas fa-phone"></i>
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                    <i class="fas fa-users text-3xl mb-2 text-gray-300"></i>
                                    <p>Hakuna wafanyakazi bado</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($wafanyakazi->hasPages())
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $wafanyakazi->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- TAB 2: Sajili -->
    <div id="sajili-tab-content" class="tab-content hidden">
        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
            <h2 class="text-sm font-semibold text-gray-800 mb-4">Sajili Mfanyakazi Mpya</h2>
            <form method="POST" action="{{ route('wafanyakazi.store') }}" id="employee-form" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <!-- Jina -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Jina Kamili *</label>
                        <input type="text" name="jina" 
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                               placeholder="Jina la mfanyakazi" required>
                    </div>

                    <!-- Jinsia -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Jinsia *</label>
                        <select name="jinsia" 
                                class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500" required>
                            <option value="">Chagua jinsia</option>
                            <option value="Mwanaume">Mwanaume</option>
                            <option value="Mwanamke">Mwanamke</option>
                        </select>
                    </div>

                    <!-- Simu -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Simu</label>
                        <input type="text" name="simu" 
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                               placeholder="Namba ya simu">
                    </div>

                    <!-- Tarehe ya Kuzaliwa -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Tarehe ya Kuzaliwa</label>
                        <input type="date" name="tarehe_kuzaliwa" 
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    </div>

                    <!-- Barua Pepe -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Barua Pepe</label>
                        <input type="email" name="barua_pepe" 
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                               placeholder="barua@pepe.com">
                    </div>

                    <!-- Anuani -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Anuani</label>
                        <input type="text" name="anuani" 
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                               placeholder="Anuani ya makazi">
                    </div>

                    <!-- Ndugu -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Jina la Ndugu</label>
                        <input type="text" name="ndugu" 
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                               placeholder="Jina la ndugu">
                    </div>

                    <!-- Simu ya Ndugu -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Simu ya Ndugu</label>
                        <input type="text" name="simu_ndugu" 
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                               placeholder="Simu ya ndugu">
                    </div>

                    <!-- Username -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Username</label>
                        <input type="text" name="username" 
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                               placeholder="Neno la kuingia" required>
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" name="password" 
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                               placeholder="Neno la siri" required>
                    </div>

                    <!-- UWEZO Field -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Uwezo *</label>
                        <select name="uwezo" 
                                class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500" required>
                            <option value="mdogo">Mdogo (Mfanyakazi wa Kawaida)</option>
                            <option value="mkubwa">Mkubwa (Msimamizi - Ana Ruhusa Zote)</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">
                            <i class="fas fa-info-circle"></i> 
                            Mkubwa anaweza kuona na kufanya shughuli zote kama Mkuu
                        </p>
                    </div>

                    <!-- Salary Field (Only for Boss) -->
                    @if(Auth::guard('web')->check())
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Mshahara (TZS)</label>
                        <input type="number" name="salary" 
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                               placeholder="0" min="0" step="1000" value="0">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Ruhusu Counter</label>
                        <div class="flex items-center mt-1">
                            <input type="checkbox" name="allow_counter_access" id="allow_counter_access" value="1" class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                            <label for="allow_counter_access" class="ml-2 text-sm text-gray-700">Mfanyakazi ataweza kufikia Counter</label>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Buttons -->
                <div class="flex gap-2 pt-4 border-t border-gray-200">
                    <button type="submit" 
                            class="flex-1 bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700 text-sm font-medium">
                        <i class="fas fa-save mr-1"></i> Hifadhi
                    </button>
                    <button type="reset" 
                            class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 text-sm">
                        <i class="fas fa-redo mr-1"></i> Safisha
                    </button>
                </div>
            </form>
        </div>
    </div>

<!-- TAB 3: Salary Management (Boss Only) -->
@if(Auth::guard('web')->check())
    @include('wafanyakazi.salary-partial')
@endif

</div>

<!-- View Details Modal -->
<div id="view-modal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-lg shadow-lg w-full max-w-md mx-auto z-50 max-h-[90vh] overflow-y-auto">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-800">Taarifa za Mfanyakazi</h3>
        </div>
        <div class="p-4 space-y-3">
            <div class="flex items-center space-x-3 mb-4">
                <div class="h-12 w-12 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-800 font-bold text-lg">
                    <span id="view-initial">M</span>
                </div>
                <div>
                    <h4 id="view-jina" class="font-medium text-gray-900 text-sm"></h4>
                    <p id="view-jinsia" class="text-xs text-gray-600"></p>
                </div>
            </div>

            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-xs text-gray-500">Simu:</span>
                    <span id="view-simu" class="text-xs text-gray-900"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-xs text-gray-500">Email:</span>
                    <span id="view-email" class="text-xs text-gray-900"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-xs text-gray-500">Tarehe ya Kuzaliwa:</span>
                    <span id="view-tarehe" class="text-xs text-gray-900"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-xs text-gray-500">Umri:</span>
                    <span id="view-umri" class="text-xs text-gray-900"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-xs text-gray-500">Anuani:</span>
                    <span id="view-anuani" class="text-xs text-gray-900"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-xs text-gray-500">Ndugu:</span>
                    <span id="view-ndugu" class="text-xs text-gray-900"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-xs text-gray-500">Simu ya Ndugu:</span>
                    <span id="view-simu-ndugu" class="text-xs text-gray-900"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-xs text-gray-500">Username:</span>
                    <span id="view-username" class="text-xs text-gray-900"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-xs text-gray-500">Uwezo:</span>
                    <span id="view-uwezo" class="text-xs text-gray-900"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-xs text-gray-500">Hali:</span>
                    <span id="view-hali" class="text-xs">
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium" id="view-hali-badge"></span>
                    </span>
                </div>
            </div>
        </div>
        <div class="flex gap-2 p-4 border-t border-gray-200">
            <button id="close-view-modal"
                    class="flex-1 px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 text-sm">
                Funga
            </button>
            <a id="call-button" href="#" 
               class="flex-1 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm font-medium text-center hidden">
                <i class="fas fa-phone mr-1"></i> Piga Simu
            </a>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="edit-modal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-lg shadow-lg w-full max-w-md mx-auto z-50 max-h-[90vh] overflow-y-auto">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-800">Rekebisha Mfanyakazi</h3>
        </div>
        <form id="edit-form" method="POST" class="p-4">
            @csrf
            @method('PUT')
            <div class="space-y-3">
                <!-- Jina -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Jina Kamili *</label>
                    <input type="text" name="jina" id="edit-jina"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                           required>
                </div>

                <!-- Jinsia -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Jinsia *</label>
                    <select name="jinsia" id="edit-jinsia"
                            class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500" required>
                        <option value="Mwanaume">Mwanaume</option>
                        <option value="Mwanamke">Mwanamke</option>
                    </select>
                </div>

                <!-- Simu -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Simu</label>
                    <input type="text" name="simu" id="edit-simu"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                </div>

                <!-- Tarehe ya Kuzaliwa -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tarehe ya Kuzaliwa</label>
                    <input type="date" name="tarehe_kuzaliwa" id="edit-tarehe"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                </div>

                <!-- Barua Pepe -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Barua Pepe</label>
                    <input type="email" name="barua_pepe" id="edit-email"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                </div>

                <!-- Anuani -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Anuani</label>
                    <input type="text" name="anuani" id="edit-anuani"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                </div>

                <!-- Ndugu -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Jina la Ndugu</label>
                    <input type="text" name="ndugu" id="edit-ndugu"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                </div>

                <!-- Simu ya Ndugu -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Simu ya Ndugu</label>
                    <input type="text" name="simu_ndugu" id="edit-simu-ndugu"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                </div>

                <!-- Username -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Username</label>
                    <input type="text" name="username" id="edit-username"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="password"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                           placeholder="Acha tupu kama hutaki kubadilisha">
                </div>

                <!-- Uwezo Field -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Uwezo</label>
                    <select name="uwezo" id="edit-uwezo"
                            class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                        <option value="mdogo">Mdogo (Mfanyakazi wa Kawaida)</option>
                        <option value="mkubwa">Mkubwa (Msimamizi - Ana Ruhusa Zote)</option>
                    </select>
                </div>

                <!-- Getini -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Hali ya Kuingia</label>
                    <select name="getini" id="edit-getini"
                            class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                        <option value="simama">Simama</option>
                        <option value="ingia">Ingia</option>
                    </select>
                </div>

                <!-- Salary (Boss only) -->
                @if(Auth::guard('web')->check())
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Mshahara (TZS)</label>
                    <input type="number" name="salary" id="edit-salary"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                           placeholder="0" min="0" step="1000">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Ruhusu Counter</label>
                    <div class="flex items-center mt-1">
                        <input type="checkbox" name="allow_counter_access" id="edit-allow-counter" value="1" class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                        <label for="edit-allow-counter" class="ml-2 text-sm text-gray-700">Mfanyakazi ataweza kufikia Counter</label>
                    </div>
                </div>
                @endif
            </div>
            <div class="flex gap-2 pt-4 border-t border-gray-200 mt-4">
                <button type="button" id="close-edit-modal"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 text-sm">
                    Ghairi
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 text-sm font-medium">
                    Hifadhi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Modal -->
<div id="delete-modal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-lg shadow-lg w-full max-w-sm mx-auto z-50">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-800">Thibitisha Kufuta</h3>
        </div>
        <div class="p-4">
            <div class="text-center mb-4">
                <i class="fas fa-exclamation-triangle text-amber-500 text-2xl mb-2"></i>
                <p class="text-gray-700 text-sm mb-1">Una uhakika unataka kufuta?</p>
                <p class="text-gray-900 font-medium" id="delete-employee-name"></p>
                <p class="text-gray-500 text-xs mt-2">Hatua hii haiwezi kutenduliwa</p>
            </div>
            <div class="flex gap-2">
                <button id="cancel-delete"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 text-sm">
                    Ghairi
                </button>
                <form id="delete-form" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="w-full px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm font-medium">
                        Futa
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Salary Modal -->
<div id="add-salary-modal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-lg shadow-lg w-full max-w-md mx-auto z-50">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-800">Ongeza Mshahara</h3>
        </div>
        <form id="add-salary-form" class="p-4">
            @csrf
            <input type="hidden" id="salary-employee-id">
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Mfanyakazi</label>
                    <input type="text" id="salary-employee-name-display" class="w-full px-3 py-2 border border-gray-300 rounded text-sm bg-gray-50" readonly>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Kiasi (TZS) *</label>
                    <input type="number" name="salary" id="salary-amount" 
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                           placeholder="0" required min="0" step="1000">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Muda wa Malipo</label>
                    <select name="frequency" id="salary-frequency" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                        <option value="monthly">Kila Mwezi</option>
                        <option value="weekly">Kila Wiki</option>
                        <option value="daily">Kila Siku</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Maelezo</label>
                    <textarea name="remarks" id="salary-remarks" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                              placeholder="Maelezo ya ziada..."></textarea>
                </div>
            </div>
            <div class="flex gap-2 pt-4 border-t border-gray-200 mt-4">
                <button type="button" onclick="closeAddSalaryModal()"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 text-sm">
                    Ghairi
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 text-sm font-medium">
                    Hifadhi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Add Deduction Modal -->
<div id="add-deduction-modal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-lg shadow-lg w-full max-w-md mx-auto z-50">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-800">Ongeza Kato</h3>
        </div>
        <form id="add-deduction-form" class="p-4">
            @csrf
            <input type="hidden" id="deduction-employee-id">
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Mfanyakazi</label>
                    <input type="text" id="deduction-employee-name" class="w-full px-3 py-2 border border-gray-300 rounded text-sm bg-gray-50" readonly>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Sababu ya Kato *</label>
                    <input type="text" name="reason" id="deduction-reason" 
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                           placeholder="Mfano: Order iliyofutwa, Hasara..." required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Kiasi (TZS) *</label>
                    <input type="number" name="amount" id="deduction-amount" 
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                           placeholder="0" required min="0" step="100">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Maelezo</label>
                    <textarea name="remarks" id="deduction-remarks" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                              placeholder="Maelezo ya ziada..."></textarea>
                </div>
            </div>
            <div class="flex gap-2 pt-4 border-t border-gray-200 mt-4">
                <button type="button" onclick="closeAddDeductionModal()"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 text-sm">
                    Ghairi
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm font-medium">
                    Ongeza Kato
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ============================================
// MAIN MANAGER CLASS
// ============================================
class SmartWafanyakaziManager {
    constructor() {
        this.currentTab = this.getSavedTab() || 'taarifa';
        this.searchTimeout = null;
        this.selectedEmployeeId = null;
        this.init();
    }

    init() {
        this.bindEvents();
        this.showTab(this.currentTab);
        this.setupAjaxForms();
        this.bindSalaryEvents();
    }

    getSavedTab() {
        return sessionStorage.getItem('wafanyakazi_tab') || 'taarifa';
    }

    saveTab(tab) {
        sessionStorage.setItem('wafanyakazi_tab', tab);
    }

    bindEvents() {
        // Tab navigation
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', (e) => {
                const tab = e.target.closest('.tab-button').dataset.tab;
                this.showTab(tab);
                this.saveTab(tab);
            });
        });

        // Search with debounce
        const searchInput = document.getElementById('search-input');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                clearTimeout(this.searchTimeout);
                this.searchTimeout = setTimeout(() => {
                    this.filterEmployees(e.target.value.toLowerCase().trim());
                }, 300);
            });
        }

        // Employee action buttons
        this.bindEmployeeActions();

        // Modal events
        this.bindModalEvents();
    }

    bindSalaryEvents() {
        // Employee Select Change - FIXED
        const select = document.getElementById('salary-employee-select');
        if (select) {
            select.addEventListener('change', function() {
                const employeeId = this.value;
                if (employeeId) {
                    // Store the selected ID globally
                    window.selectedEmployeeId = employeeId;
                    loadEmployeeSalaryDetails(employeeId);
                    document.getElementById('salary-employee-details').classList.remove('hidden');
                } else {
                    document.getElementById('salary-employee-details').classList.add('hidden');
                    window.selectedEmployeeId = null;
                }
            });
        }

        // Add Salary Form
        const addSalaryForm = document.getElementById('add-salary-form');
        if (addSalaryForm) {
            addSalaryForm.addEventListener('submit', function(e) {
                e.preventDefault();
                submitAddSalary(this);
            });
        }

        // Add Deduction Form
        const addDeductionForm = document.getElementById('add-deduction-form');
        if (addDeductionForm) {
            addDeductionForm.addEventListener('submit', function(e) {
                e.preventDefault();
                submitAddDeduction(this);
            });
        }
    }

    showTab(tabName) {
        // Update tabs
        document.querySelectorAll('.tab-button').forEach(button => {
            if (button.dataset.tab === tabName) {
                button.classList.add('bg-emerald-50', 'text-emerald-700');
                button.classList.remove('text-gray-600', 'hover:bg-gray-50');
            } else {
                button.classList.remove('bg-emerald-50', 'text-emerald-700');
                button.classList.add('text-gray-600', 'hover:bg-gray-50');
            }
        });

        // Update tab content
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        
        const target = document.getElementById(`${tabName}-tab-content`);
        if (target) {
            target.classList.remove('hidden');
        }
        this.currentTab = tabName;
    }

    bindEmployeeActions() {
        // View buttons
        document.querySelectorAll('.view-employee-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                const row = e.target.closest('.employee-row');
                const employee = JSON.parse(row.dataset.employee);
                this.viewEmployee(employee);
            });
        });

        // Edit buttons
        document.querySelectorAll('.edit-employee-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                const row = e.target.closest('.employee-row');
                const employee = JSON.parse(row.dataset.employee);
                this.editEmployee(employee);
            });
        });

        // Delete buttons
        document.querySelectorAll('.delete-employee-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                const employeeId = e.target.closest('.delete-employee-btn').dataset.id;
                const employeeName = e.target.closest('.delete-employee-btn').dataset.name;
                this.deleteEmployee(employeeId, employeeName);
            });
        });
    }

    bindModalEvents() {
        // View modal
        const viewModal = document.getElementById('view-modal');
        const closeViewBtn = document.getElementById('close-view-modal');

        if (closeViewBtn) {
            closeViewBtn.addEventListener('click', () => viewModal.classList.add('hidden'));
        }
        
        if (viewModal) {
            viewModal.addEventListener('click', (e) => {
                if (e.target === viewModal || e.target.classList.contains('modal-overlay')) {
                    viewModal.classList.add('hidden');
                }
            });
        }

        // Edit modal
        const editModal = document.getElementById('edit-modal');
        const closeEditBtn = document.getElementById('close-edit-modal');

        if (closeEditBtn) {
            closeEditBtn.addEventListener('click', () => editModal.classList.add('hidden'));
        }
        
        if (editModal) {
            editModal.addEventListener('click', (e) => {
                if (e.target === editModal || e.target.classList.contains('modal-overlay')) {
                    editModal.classList.add('hidden');
                }
            });
        }

        // Delete modal
        const deleteModal = document.getElementById('delete-modal');
        const cancelDeleteBtn = document.getElementById('cancel-delete');

        if (cancelDeleteBtn) {
            cancelDeleteBtn.addEventListener('click', () => deleteModal.classList.add('hidden'));
        }
        
        if (deleteModal) {
            deleteModal.addEventListener('click', (e) => {
                if (e.target === deleteModal || e.target.classList.contains('modal-overlay')) {
                    deleteModal.classList.add('hidden');
                }
            });
        }

        // Salary modals
        const addSalaryModal = document.getElementById('add-salary-modal');
        if (addSalaryModal) {
            addSalaryModal.addEventListener('click', (e) => {
                if (e.target === addSalaryModal || e.target.classList.contains('modal-overlay')) {
                    addSalaryModal.classList.add('hidden');
                }
            });
        }

        const addDeductionModal = document.getElementById('add-deduction-modal');
        if (addDeductionModal) {
            addDeductionModal.addEventListener('click', (e) => {
                if (e.target === addDeductionModal || e.target.classList.contains('modal-overlay')) {
                    addDeductionModal.classList.add('hidden');
                }
            });
        }

        // Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                if (viewModal) viewModal.classList.add('hidden');
                if (editModal) editModal.classList.add('hidden');
                if (deleteModal) deleteModal.classList.add('hidden');
                if (addSalaryModal) addSalaryModal.classList.add('hidden');
                if (addDeductionModal) addDeductionModal.classList.add('hidden');
            }
        });
    }

    filterEmployees(searchTerm) {
        const rows = document.querySelectorAll('.employee-row');
        let found = false;
        
        rows.forEach(row => {
            const employee = JSON.parse(row.dataset.employee);
            const searchText = `
                ${employee.jina || ''}
                ${employee.simu || ''}
                ${employee.barua_pepe || ''}
                ${employee.username || ''}
                ${employee.anuani || ''}
                ${employee.ndugu || ''}
            `.toLowerCase();
            
            if (searchText.includes(searchTerm) || !searchTerm) {
                row.classList.remove('hidden');
                found = true;
            } else {
                row.classList.add('hidden');
            }
        });

        if (!found && searchTerm) {
            this.showNotification('Hakuna wafanyakazi wanaolingana', 'info');
        }
    }

    viewEmployee(employee) {
        const viewModal = document.getElementById('view-modal');
        if (!viewModal) return;

        // Calculate age
        let umri = '--';
        if (employee.tarehe_kuzaliwa) {
            const birthDate = new Date(employee.tarehe_kuzaliwa);
            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const monthDiff = today.getMonth() - birthDate.getMonth();
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            umri = age + ' years';
        }

        // Populate view modal
        document.getElementById('view-initial').textContent = employee.jina ? employee.jina.charAt(0).toUpperCase() : 'M';
        document.getElementById('view-jina').textContent = employee.jina || '--';
        document.getElementById('view-jinsia').textContent = employee.jinsia || '--';
        document.getElementById('view-simu').textContent = employee.simu || '--';
        document.getElementById('view-email').textContent = employee.barua_pepe || '--';
        document.getElementById('view-tarehe').textContent = employee.tarehe_kuzaliwa || '--';
        document.getElementById('view-umri').textContent = umri;
        document.getElementById('view-anuani').textContent = employee.anuani || '--';
        document.getElementById('view-ndugu').textContent = employee.ndugu || '--';
        document.getElementById('view-simu-ndugu').textContent = employee.simu_ndugu || '--';
        document.getElementById('view-username').textContent = employee.username || '--';
        
        // Set uwezo display
        const uwezoSpan = document.getElementById('view-uwezo');
        if (employee.uwezo === 'mkubwa') {
            uwezoSpan.innerHTML = '<span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-purple-100 text-purple-800"><i class="fas fa-crown mr-1"></i> Mkubwa</span>';
        } else {
            uwezoSpan.innerHTML = '<span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-600"><i class="fas fa-user mr-1"></i> Mdogo</span>';
        }
        
        // Set hali badge
        const haliBadge = document.getElementById('view-hali-badge');
        if (employee.getini === 'ingia') {
            haliBadge.className = 'inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800';
            haliBadge.innerHTML = '<i class="fas fa-check-circle mr-1"></i> Ingia';
        } else {
            haliBadge.className = 'inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800';
            haliBadge.innerHTML = '<i class="fas fa-pause-circle mr-1"></i> Simama';
        }

        // Show/hide call button
        const callButton = document.getElementById('call-button');
        if (employee.simu) {
            callButton.href = `tel:${employee.simu}`;
            callButton.classList.remove('hidden');
        } else {
            callButton.classList.add('hidden');
        }

        viewModal.classList.remove('hidden');
    }

    editEmployee(employee) {
        const editForm = document.getElementById('edit-form');
        if (!editForm) return;

        // Populate edit form
        document.getElementById('edit-jina').value = employee.jina || '';
        document.getElementById('edit-jinsia').value = employee.jinsia || 'Mwanaume';
        document.getElementById('edit-simu').value = employee.simu || '';
        document.getElementById('edit-tarehe').value = employee.tarehe_kuzaliwa ? employee.tarehe_kuzaliwa.split('T')[0] : '';
        document.getElementById('edit-email').value = employee.barua_pepe || '';
        document.getElementById('edit-anuani').value = employee.anuani || '';
        document.getElementById('edit-ndugu').value = employee.ndugu || '';
        document.getElementById('edit-simu-ndugu').value = employee.simu_ndugu || '';
        document.getElementById('edit-username').value = employee.username || '';
        document.getElementById('edit-getini').value = employee.getini || 'simama';
        document.getElementById('edit-uwezo').value = employee.uwezo || 'mdogo';
        
        // Salary fields (Boss only)
        const salaryField = document.getElementById('edit-salary');
        if (salaryField) {
            salaryField.value = employee.salary || 0;
        }
        const counterField = document.getElementById('edit-allow-counter');
        if (counterField) {
            counterField.checked = employee.allow_counter_access || false;
        }
        
        editForm.action = `/wafanyakazi/${employee.id}`;
        
        const editModal = document.getElementById('edit-modal');
        if (editModal) editModal.classList.remove('hidden');
    }

    deleteEmployee(employeeId, employeeName) {
        const deleteForm = document.getElementById('delete-form');
        const deleteModal = document.getElementById('delete-modal');
        const deleteEmployeeName = document.getElementById('delete-employee-name');
        
        if (!deleteForm || !deleteModal || !deleteEmployeeName) return;
        
        deleteEmployeeName.textContent = employeeName;
        deleteForm.action = `/wafanyakazi/${employeeId}`;
        deleteModal.classList.remove('hidden');
    }

    setupAjaxForms() {
        // Employee form
        const employeeForm = document.getElementById('employee-form');
        if (employeeForm) {
            employeeForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.submitForm(employeeForm, 'Mfanyakazi amesajiliwa!');
                employeeForm.reset();
            });
        }

        // Edit form
        const editForm = document.getElementById('edit-form');
        if (editForm) {
            editForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.submitForm(editForm, 'Mfanyakazi imerekebishwa!');
                document.getElementById('edit-modal').classList.add('hidden');
            });
        }

        // Delete form
        const deleteForm = document.getElementById('delete-form');
        if (deleteForm) {
            deleteForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.submitForm(deleteForm, 'Mfanyakazi amefutwa!');
                document.getElementById('delete-modal').classList.add('hidden');
            });
        }
    }

    async submitForm(form, successMessage = 'Operesheni imekamilika!') {
        const formData = new FormData(form);
        const submitButton = form.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        
        try {
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Inatumwa...';
            
            const response = await fetch(form.action, {
                method: form.method,
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();

            if (response.ok) {
                const message = data.message || successMessage;
                this.showNotification(message, 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                const error = data.errors ? Object.values(data.errors)[0][0] : data.message;
                this.showNotification(error || 'Hitilafu imetokea', 'error');
            }
        } catch (error) {
            this.showNotification('Hitilafu ya mtandao', 'error');
        } finally {
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
        }
    }

    showNotification(message, type = 'info') {
        const container = document.getElementById('notification-container');
        if (!container) return;
        
        const colors = {
            success: 'bg-emerald-50 border-emerald-200 text-emerald-800',
            error: 'bg-red-50 border-red-200 text-red-800',
            warning: 'bg-amber-50 border-amber-200 text-amber-800',
            info: 'bg-blue-50 border-blue-200 text-blue-800'
        };

        const notification = document.createElement('div');
        notification.className = `rounded border px-4 py-3 text-sm font-medium mb-2 ${colors[type]} shadow-sm`;
        notification.textContent = message;
        container.appendChild(notification);

        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateY(-10px)';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
}

// ============================================
// SALARY MANAGEMENT FUNCTIONS - FIXED
// ============================================

// Global variable to store selected employee ID
window.selectedEmployeeId = null;

function getWafanyakaziUrl(path) {
    if (path.startsWith('/')) {
        path = path.substring(1);
    }
    if (path.includes('wafanyakazi')) {
        return '/' + path;
    }
    return '/wafanyakazi/' + path;
}

// Toggle Counter Access
function toggleCounterAccess() {
    const employeeId = window.selectedEmployeeId;
    if (!employeeId) {
        showNotification('Tafadhali chagua mfanyakazi kwanza', 'warning');
        return;
    }
    
    if (!confirm('Una uhakika unataka kubadilisha ruhusa ya Counter?')) return;
    
    const url = getWafanyakaziUrl(employeeId + '/toggle-counter');
    
    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('HTTP ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            loadEmployeeSalaryDetails(employeeId);
        } else {
            showNotification(data.message || 'Hitilafu imetokea', 'error');
        }
    })
    .catch(error => {
        console.error('Toggle counter error:', error);
        showNotification('Hitilafu: ' + error.message, 'error');
    });
}

// Load Employee Salary Details
function loadEmployeeSalaryDetails(employeeId) {
    if (!employeeId) {
        showNotification('Tafadhali chagua mfanyakazi', 'warning');
        return;
    }
    
    const detailsDiv = document.getElementById('salary-employee-details');
    detailsDiv.classList.remove('hidden');
    document.getElementById('salary-employee-name').textContent = 'Inapakia...';
    
    const url = getWafanyakaziUrl(employeeId + '/salary-details');
    
    fetch(url, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('HTTP ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            displayEmployeeSalaryDetails(data.data);
            window.selectedEmployeeId = employeeId;
        } else {
            showNotification(data.message || 'Hitilafu katika kupata taarifa', 'error');
            detailsDiv.classList.add('hidden');
        }
    })
    .catch(error => {
        console.error('Load salary details error:', error);
        showNotification('Hitilafu: ' + error.message, 'error');
        detailsDiv.classList.add('hidden');
    });
}

function displayEmployeeSalaryDetails(data) {
    const detailsDiv = document.getElementById('salary-employee-details');
    detailsDiv.classList.remove('hidden');
    
    const employee = data.employee;
    
    document.getElementById('salary-employee-name').textContent = employee.jina || '--';
    document.getElementById('salary-employee-info').textContent = 
        `${employee.jinsia || '--'} • ${employee.uwezo === 'mkubwa' ? 'Mkubwa' : 'Mdogo'} • Counter: ${employee.allow_counter_access ? '✅ Inaruhusiwa' : '❌ Hairuhusiwi'}`;
    
    // Update counter toggle button
    const toggleBtn = document.getElementById('counter-toggle-btn');
    if (employee.allow_counter_access) {
        toggleBtn.className = 'px-3 py-1.5 bg-red-600 text-white rounded hover:bg-red-700 text-xs font-medium transition';
        toggleBtn.innerHTML = '<i class="fas fa-times mr-1"></i> Ondoa Counter';
    } else {
        toggleBtn.className = 'px-3 py-1.5 bg-green-600 text-white rounded hover:bg-green-700 text-xs font-medium transition';
        toggleBtn.innerHTML = '<i class="fas fa-check mr-1"></i> Ruhusu Counter';
    }
    
    // Update stats
    const currentSalary = data.current_salary || 0;
    const totalDeductions = data.total_deductions || 0;
    const netSalary = data.net_salary || 0;
    
    document.getElementById('total-salary-display').textContent = 
        currentSalary.toLocaleString() + ' TZS';
    document.getElementById('total-deductions-display').textContent = 
        totalDeductions.toLocaleString() + ' TZS';
    document.getElementById('net-salary-display').textContent = 
        netSalary.toLocaleString() + ' TZS';
    
    // Render salary history
    renderSalaryHistory(data.salaries || []);
    
    // Render deductions
    renderDeductions(data.deductions || [], data.pending_deductions || []);
}

function renderSalaryHistory(salaries) {
    const tbody = document.getElementById('salary-history-body');
    if (!salaries || salaries.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="px-3 py-4 text-center text-gray-500 text-sm">Hakuna historia</td></tr>';
        return;
    }
    
    tbody.innerHTML = salaries.map(s => `
        <tr>
            <td class="px-3 py-2 text-xs">${s.created_at ? new Date(s.created_at).toLocaleDateString('sw-TZ') : '--'}</td>
            <td class="px-3 py-2 text-xs font-medium text-green-700">${(s.salary_amount || 0).toLocaleString()} ${s.currency || 'TZS'}</td>
            <td class="px-3 py-2 text-xs">${s.frequency || 'monthly'}</td>
            <td class="px-3 py-2 text-xs text-gray-500">${s.remarks || '--'}</td>
        </tr>
    `).join('');
}

function renderDeductions(deductions, pendingDeductions) {
    const tbody = document.getElementById('deductions-body');
    const allDeductions = [...(deductions || []), ...(pendingDeductions || [])];
    
    if (allDeductions.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="px-3 py-4 text-center text-gray-500 text-sm">Hakuna makato</td></tr>';
        return;
    }
    
    tbody.innerHTML = allDeductions.map(d => {
        let statusClass = '';
        let statusText = '';
        let actionButtons = '';
        
        if (d.status === 'approved') {
            statusClass = 'bg-green-100 text-green-800';
            statusText = 'Imeidhinishwa';
        } else if (d.status === 'pending') {
            statusClass = 'bg-yellow-100 text-yellow-800';
            statusText = 'Inasubiri';
            actionButtons = `
                <button onclick="approveDeduction(${d.id})" class="text-green-600 hover:text-green-800 text-xs" title="Idhinisha">
                    <i class="fas fa-check-circle"></i>
                </button>
                <button onclick="rejectDeduction(${d.id})" class="text-red-600 hover:text-red-800 text-xs" title="Kataa">
                    <i class="fas fa-times-circle"></i>
                </button>
            `;
        } else {
            statusClass = 'bg-red-100 text-red-800';
            statusText = 'Imekataliwa';
        }
        
        return `
            <tr>
                <td class="px-3 py-2 text-xs">${d.reason || '--'}</td>
                <td class="px-3 py-2 text-xs font-medium text-red-700">${(d.amount || 0).toLocaleString()} TZS</td>
                <td class="px-3 py-2 text-xs"><span class="px-2 py-0.5 rounded text-xs font-medium ${statusClass}">${statusText}</span></td>
                <td class="px-3 py-2 text-xs">${d.created_at ? new Date(d.created_at).toLocaleDateString('sw-TZ') : '--'}</td>
                <td class="px-3 py-2 text-center">
                    <div class="flex items-center justify-center gap-1">
                        ${actionButtons}
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

// Approve Deduction
function approveDeduction(deductionId) {
    if (!confirm('Una uhakika unataka kuidhinisha kato hii?')) return;
    
    const url = getWafanyakaziUrl('deduction/' + deductionId + '/approve');
    
    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('HTTP ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            loadEmployeeSalaryDetails(window.selectedEmployeeId);
        } else {
            showNotification(data.message || 'Hitilafu imetokea', 'error');
        }
    })
    .catch(error => {
        console.error('Approve deduction error:', error);
        showNotification('Hitilafu: ' + error.message, 'error');
    });
}

// Reject Deduction
function rejectDeduction(deductionId) {
    if (!confirm('Una uhakika unataka kukataa kato hii?')) return;
    
    const url = getWafanyakaziUrl('deduction/' + deductionId + '/reject');
    
    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('HTTP ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            loadEmployeeSalaryDetails(window.selectedEmployeeId);
        } else {
            showNotification(data.message || 'Hitilafu imetokea', 'error');
        }
    })
    .catch(error => {
        console.error('Reject deduction error:', error);
        showNotification('Hitilafu: ' + error.message, 'error');
    });
}

// Show Add Salary Modal
function showAddSalaryModal() {
    const employeeId = window.selectedEmployeeId;
    if (!employeeId) {
        showNotification('Tafadhali chagua mfanyakazi kwanza', 'warning');
        return;
    }
    
    document.getElementById('salary-employee-id').value = employeeId;
    document.getElementById('salary-employee-name-display').value = 
        document.getElementById('salary-employee-name').textContent;
    document.getElementById('salary-amount').value = '';
    document.getElementById('salary-frequency').value = 'monthly';
    document.getElementById('salary-remarks').value = '';
    document.getElementById('add-salary-modal').classList.remove('hidden');
}

function closeAddSalaryModal() {
    document.getElementById('add-salary-modal').classList.add('hidden');
}

// Show Add Deduction Modal
function showAddDeductionModal() {
    const employeeId = window.selectedEmployeeId;
    if (!employeeId) {
        showNotification('Tafadhali chagua mfanyakazi kwanza', 'warning');
        return;
    }
    
    document.getElementById('deduction-employee-id').value = employeeId;
    document.getElementById('deduction-employee-name').value = 
        document.getElementById('salary-employee-name').textContent;
    document.getElementById('deduction-reason').value = '';
    document.getElementById('deduction-amount').value = '';
    document.getElementById('deduction-remarks').value = '';
    document.getElementById('add-deduction-modal').classList.remove('hidden');
}

function closeAddDeductionModal() {
    document.getElementById('add-deduction-modal').classList.add('hidden');
}

// Submit Add Salary
function submitAddSalary(form) {
    const employeeId = document.getElementById('salary-employee-id').value;
    const formData = new FormData(form);
    
    const url = getWafanyakaziUrl(employeeId + '/update-salary');
    
    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('HTTP ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            closeAddSalaryModal();
            loadEmployeeSalaryDetails(employeeId);
        } else {
            showNotification(data.message || 'Hitilafu imetokea', 'error');
        }
    })
    .catch(error => {
        console.error('Add salary error:', error);
        showNotification('Hitilafu: ' + error.message, 'error');
    });
}

// Submit Add Deduction
function submitAddDeduction(form) {
    const employeeId = document.getElementById('deduction-employee-id').value;
    const formData = new FormData(form);
    
    const url = getWafanyakaziUrl(employeeId + '/add-deduction');
    
    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('HTTP ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            closeAddDeductionModal();
            loadEmployeeSalaryDetails(employeeId);
        } else {
            showNotification(data.message || 'Hitilafu imetokea', 'error');
        }
    })
    .catch(error => {
        console.error('Add deduction error:', error);
        showNotification('Hitilafu: ' + error.message, 'error');
    });
}

// Global showNotification function
function showNotification(message, type = 'info') {
    const container = document.getElementById('notification-container');
    if (!container) return;
    
    const colors = {
        success: 'bg-emerald-50 border-emerald-200 text-emerald-800',
        error: 'bg-red-50 border-red-200 text-red-800',
        warning: 'bg-amber-50 border-amber-200 text-amber-800',
        info: 'bg-blue-50 border-blue-200 text-blue-800'
    };

    const notification = document.createElement('div');
    notification.className = `rounded border px-4 py-3 text-sm font-medium mb-2 ${colors[type]} shadow-sm`;
    notification.textContent = message;
    container.appendChild(notification);

    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateY(-10px)';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Print function
function printWafanyakazi() {
    const printWindow = window.open('', '_blank');
    const rows = document.querySelectorAll('.employee-row');
    
    let tableRows = '';
    rows.forEach(row => {
        const employee = JSON.parse(row.dataset.employee);
        const age = employee.tarehe_kuzaliwa ? 
            new Date().getFullYear() - new Date(employee.tarehe_kuzaliwa).getFullYear() + ' yrs' : 
            '--';
        
        const uwezoText = employee.uwezo === 'mkubwa' ? 'Mkubwa' : 'Mdogo';
            
        tableRows += `
            <tr>
                <td style="border: 1px solid #ddd; padding: 8px;">${employee.jina}</td>
                <td style="border: 1px solid #ddd; padding: 8px;">${employee.simu || '--'}</td>
                <td style="border: 1px solid #ddd; padding: 8px;">${employee.jinsia}</td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">${uwezoText}</td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">${age}</td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">${employee.getini === 'ingia' ? 'Ingia' : 'Simama'}</td>
            </tr>`;
    });
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Orodha ya Wafanyakazi - ${new Date().toLocaleDateString()}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; }
                th { background-color: #f3f4f6; font-weight: bold; }
                .header { text-align: center; margin-bottom: 30px; }
                .header h2 { margin: 0; color: #047857; }
                .header p { margin: 5px 0 0 0; color: #6b7280; }
                @media print {
                    body { margin: 0; }
                    .no-print { display: none; }
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>Orodha ya Wafanyakazi</h2>
                <p>${new Date().toLocaleDateString()}</p>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Jina</th>
                        <th>Simu</th>
                        <th>Jinsia</th>
                        <th>Uwezo</th>
                        <th>Umri</th>
                        <th>Hali</th>
                    </tr>
                </thead>
                <tbody>
                    ${tableRows}
                </tbody>
            </table>
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

// PDF Export
function exportPDF() {
    const search = new URLSearchParams(window.location.search);
    search.set('export', 'pdf');
    window.open(`${window.location.pathname}?${search.toString()}`, '_blank');
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    window.wafanyakaziManager = new SmartWafanyakaziManager();
    
    window.addEventListener('beforeunload', () => {
        if (window.wafanyakaziManager) {
            window.wafanyakaziManager.saveTab(window.wafanyakaziManager.currentTab);
        }
    });
});
</script>
@endpush