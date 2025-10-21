@extends('layouts.app')

@section('title', 'Matumizi - DEMODAY')

@section('content')
<div x-data="{
        active: 'taarifa',
        search: '',
        showEditModal: false,
        showDeleteModal: false,
        editItem: {},
        deleteId: null,
        today: '{{ now()->format('d/m/Y') }}',
    }" class="min-h-screen">

    <!-- Tabs -->
    <div class="bg-gray-100 px-6 py-3 border-b flex gap-6 text-gray-700 font-medium print:hidden">
        <button 
            @click="active='taarifa'" 
            :class="active==='taarifa' ? 'text-blue-700 font-bold underline' : 'hover:text-blue-600'">
            Taarifa za Matumizi
        </button>
        <button 
            @click="active='ingiza'" 
            :class="active==='ingiza' ? 'text-blue-700 font-bold underline' : 'hover:text-blue-600'">
            Ingiza Matumizi
        </button>
    </div>

    <!-- Main Area -->
    <main class="p-6 overflow-auto">

        <!-- ===================== Taarifa za Matumizi ===================== -->
        <div x-show="active==='taarifa'" x-cloak>
            <div class="flex justify-between items-center mb-4 print:hidden">
                <h3 class="text-lg font-semibold">Taarifa za Matumizi</h3>
                <div class="flex gap-2">
                    <input type="text" placeholder="Tafuta..." x-model="search" class="border px-2 py-1 rounded" />
                    <button onclick="window.print()" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">
                        <i class="fas fa-print"></i> Print
                    </button>
                </div>
            </div>

            <!-- Printable Header -->
            <div class="hidden print:block text-center mb-4">
                <h2 class="text-xl font-bold">Ripoti ya Matumizi - DEMODAY</h2>
                <p class="text-gray-600 text-sm">Imechapishwa: {{ now()->format('d/m/Y H:i') }}</p>
            </div>

            <!-- Data Table -->
            <table class="w-full table-auto border-collapse text-sm">
                <thead class="bg-gray-200 print:bg-gray-100">
                    <tr>
                        <th class="border px-4 py-2 text-left">Tarehe</th>
                        <th class="border px-4 py-2 text-left">Matumizi</th>
                        <th class="border px-4 py-2 text-left">Maelezo</th>
                        <th class="border px-4 py-2 text-right">Gharama</th>
                        <th class="border px-4 py-2 text-left">Muda</th>
                        <th class="border px-4 py-2 text-center print:hidden">Vitendo</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($matumizi as $item)
                        <tr 
                            x-show="search === '' || '{{ strtolower($item->aina) }}'.includes(search.toLowerCase()) || '{{ strtolower($item->maelezo) }}'.includes(search.toLowerCase())"
                            :class="('{{ $item->created_at->format('d/m/Y') }}' === today) ? 'bg-yellow-100' : ''">
                            <td class="border px-4 py-2">{{ $item->created_at->format('d/m/Y') }}</td>
                            <td class="border px-4 py-2">{{ $item->aina }}</td>
                            <td class="border px-4 py-2">{{ $item->maelezo }}</td>
                            <td class="border px-4 py-2 text-right">{{ number_format($item->gharama, 2) }}</td>
                            <td class="border px-4 py-2">{{ $item->created_at->format('H:i') }}</td>
                            <td class="border px-4 py-2 text-center print:hidden">
                                <button class="text-blue-600 hover:underline mr-2" @click="editItem = {{ $item->toJson() }}; showEditModal = true;">Badili</button>
                                <button class="text-red-600 hover:underline" @click="deleteId = {{ $item->id }}; showDeleteModal = true;">Futa</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">Hakuna matumizi bado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- ===================== Ingiza Matumizi ===================== -->
        <div x-show="active==='ingiza'" x-cloak>
            <form method="POST" action="{{ route('matumizi.store') }}">
                @csrf
                <div x-data="{ selectedAina: '' }" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="font-medium">Chagua Aina ya Matumizi</label>
                        <select name="aina" x-model="selectedAina" class="w-full border p-2 rounded" required>
                            <option value="">-- Chagua Aina --</option>
                            <option value="Bank">Bank</option>
                            <option value="Mshahara">Mshahara</option>
                            <option value="Kodi TRA">Kodi TRA</option>
                            <option value="Kodi Pango">Kodi Pango</option>
                            <option value="Mengineyo">Mengineyo</option>
                        </select>

                        <div x-show="selectedAina === 'Mengineyo'" class="mt-2">
                            <label class="font-medium">Andika Aina Mpya ya Matumizi</label>
                            <input 
                                type="text" 
                                name="aina_mpya" 
                                class="w-full border p-2 rounded mt-1" 
                                placeholder="Mfano: Chakula, Mafuta..." 
                                required>
                        </div>
                    </div>

                    <div>
                        <label class="font-medium">Maelezo</label>
                        <input type="text" name="maelezo" class="w-full border p-2 rounded" placeholder="Maelezo ya matumizi (hiari)">
                    </div>

                    <div>
                        <label class="font-medium">Gharama</label>
                        <input type="number" name="gharama" class="w-full border p-2 rounded" placeholder="Kiasi cha gharama" required>
                    </div>
                </div>

                <div class="flex gap-4 mt-4">
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">💾 Hifadhi</button>
                    <button type="reset" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">↺ Weka Upya</button>
                </div>
            </form>
        </div>
    </main>

    <!-- ===================== Edit Modal ===================== -->
    <div x-show="showEditModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center">
        <div class="bg-white rounded-lg p-6 w-96 shadow">
            <h2 class="text-lg font-semibold mb-4">Badili Matumizi</h2>
            <form :action="'/matumizi/' + editItem.id" method="POST">
                @csrf
                @method('PUT')
                <label>Aina</label>
                <input type="text" name="aina" x-model="editItem.aina" class="w-full border p-2 rounded mb-2">
                <label>Maelezo</label>
                <input type="text" name="maelezo" x-model="editItem.maelezo" class="w-full border p-2 rounded mb-2">
                <label>Gharama</label>
                <input type="number" name="gharama" x-model="editItem.gharama" class="w-full border p-2 rounded mb-4">
                <div class="flex justify-end gap-3">
                    <button type="button" @click="showEditModal=false" class="bg-gray-400 text-white px-3 py-1 rounded">Ghairi</button>
                    <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded">Hifadhi</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ===================== Delete Confirmation ===================== -->
    <div x-show="showDeleteModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center">
        <div class="bg-white rounded-lg p-6 w-80 shadow text-center">
            <p class="mb-4 text-gray-800">Una uhakika unataka kufuta matumizi haya?</p>
            <form :action="'/matumizi/' + deleteId" method="POST" class="flex justify-center gap-3">
                @csrf
                @method('DELETE')
                <button type="button" @click="showDeleteModal=false" class="bg-gray-400 text-white px-4 py-2 rounded">Hapana</button>
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded">Ndiyo, Futa</button>
            </form>
        </div>
    </div>
</div>
@endsection
@push('styles')
<style>
    [x-cloak] {
        display: none !important;
    }

    /* ======= Table Styling (Screen) ======= */
    table {
        border-collapse: collapse;
        width: 100%;
        font-size: 14px;
        background: #fff;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }

    th {
        background-color: #1f2937; /* dark gray */
        color: white;
        font-weight: 600;
        text-align: left;
        padding: 10px 12px;
        border: 1px solid #374151;
    }

    td {
        padding: 9px 12px;
        border: 1px solid #d1d5db;
        color: #374151;
    }

    tbody tr:nth-child(even) {
        background-color: #f9fafb;
    }

    tbody tr:hover {
        background-color: #eef2ff;
    }

    /* Highlight today's records */
    tr.bg-yellow-100 {
        background-color: #fef3c7 !important;
    }

    /* ======= Print Styling ======= */
    @media print {
        body {
            background: white !important;
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
        }

        aside,
        header,
        .print\:hidden,
        .print\:hidden * {
            display: none !important;
        }

        table {
            width: 100%;
            font-size: 13px;
            border: 1px solid #000;
        }

        th {
            background-color: #e5e7eb !important;
            color: #000 !important;
            border: 1px solid #000;
            font-weight: bold;
            padding: 6px;
        }

        td {
            border: 1px solid #000;
            padding: 5px;
        }

        h2 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 6px;
        }

        p {
            margin-bottom: 12px;
            font-size: 13px;
            color: #333;
        }

        @page {
            size: A4 portrait;
            margin: 1.5cm;
        }
    }
</style>
@endpush


