@extends('layouts.app')

@section('title', 'Taarifa za Kampuni')

@section('content')
<div class="max-w-5xl mx-auto mt-6">

    @php
        $regions = [
            "Arusha","Dar es Salaam","Dodoma","Geita","Iringa","Kagera","Katavi",
            "Kigoma","Kilimanjaro","Lindi","Manyara","Mara","Mbeya","Morogoro",
            "Mtwara","Njombe","Pwani","Ruvuma","Rukwa","Shinyanga","Singida",
            "Tabora","Tanga","Zanzibar North","Zanzibar South","Zanzibar Urban/West"
        ];
    @endphp

    <!-- Tabs -->
    <div class="border-b border-gray-200">
        <nav class="flex space-x-6 items-center" aria-label="Tabs">
            <button id="tab-info" class="tab-button text-gray-700 py-2 px-4 border-b-2 font-medium text-sm border-green-700">
                📋 Taarifa za Kampuni
            </button>
            <button id="tab-edit" class="tab-button text-gray-500 py-2 px-4 border-b-2 font-medium text-sm border-transparent hover:text-gray-700 hover:border-gray-300">
                🛠️ Badili Taarifa
            </button>
        </nav>
    </div>

    <!-- Tab Contents -->
    <div class="mt-6">
        <!-- Info Tab -->
        <div id="content-info">
            <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden mb-6">
                <div class="bg-green-700 text-white px-6 py-4">
                    <h2 class="text-lg font-semibold">📋 Taarifa ya Kampuni</h2>
                    <p class="text-sm text-green-100">Maelezo ya kampuni yako yaliyosajiliwa</p>
                </div>

                <div class="grid grid-cols-2 gap-x-6 gap-y-3 px-6 py-6 text-gray-800 text-sm">
                    <div class="font-medium text-gray-600">Jina la Kampuni</div>
                    <div class="text-gray-900">{{ $company->company_name ?? 'Hakuna' }}</div>

                    <div class="font-medium text-gray-600">Jina la Mmiliki</div>
                    <div class="text-gray-900">{{ $company->owner_name ?? 'Hakuna' }}</div>

                    <div class="font-medium text-gray-600">Tarehe ya Kuzaliwa</div>
                    <div class="text-gray-900">
                        {{ $company && $company->owner_dob ? \Carbon\Carbon::parse($company->owner_dob)->format('d F, Y') : 'Hakuna' }}
                    </div>

                    <div class="font-medium text-gray-600">Jinsia</div>
                    <div class="text-gray-900">{{ strtoupper($company->owner_gender ?? 'Hakuna') }}</div>

                    <div class="font-medium text-gray-600">Mahali Ilipo</div>
                    <div class="text-gray-900">{{ $company->location ?? 'Hakuna' }}</div>

                    <div class="font-medium text-gray-600">Mkoa</div>
                    <div class="text-gray-900">{{ strtoupper($company->region ?? 'Hakuna') }}</div>

                    <div class="font-medium text-gray-600">Namba ya Simu</div>
                    <div class="text-gray-900">{{ $company->phone ?? 'Hakuna' }}</div>

                    <div class="font-medium text-gray-600">Barua Pepe</div>
                    <div class="text-gray-900">{{ $company->company_email ?? $company->email ?? 'Hakuna' }}</div>
                </div>
            </div>
        </div>

        <!-- Edit Tab -->
        <div id="content-edit" class="hidden">
            <div class="bg-white shadow rounded-lg border border-gray-200">
             <form action="{{ route('company.update') }}" method="POST" enctype="multipart/form-data" class="px-6 py-6">
             @csrf
                @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-gray-700">

                        <!-- Left column -->
                        <div class="space-y-4">
                            <div>
                                <label class="block font-medium mb-1">Jina la Kampuni</label>
                                <input type="text" name="company_name" value="{{ old('company_name', $company->company_name ?? '') }}"
                                       class="w-full border rounded-md px-3 py-2 focus:ring-green-500 focus:border-green-500" required>
                            </div>

                            <div>
                                <label class="block font-medium mb-1">Tarehe ya Kuzaliwa</label>
                                <input type="date" name="owner_dob" value="{{ old('owner_dob', $company->owner_dob ?? '') }}"
                                       class="w-full border rounded-md px-3 py-2 focus:ring-green-500 focus:border-green-500" required>
                            </div>

                            <div>
                                <label class="block font-medium mb-1">Mkoa</label>
                                <select name="region" class="w-full border rounded-md px-3 py-2 focus:ring-green-500 focus:border-green-500" required>
                                    <option value="">Chagua Mkoa</option>
                                    @foreach($regions as $region)
                                        <option value="{{ $region }}" {{ old('region', $company->region ?? '') == $region ? 'selected' : '' }}>
                                            {{ strtoupper($region) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block font-medium mb-1">TIN</label>
                                <input type="text" name="tin" value="{{ old('tin', $company->tin ?? '') }}"
                                       placeholder="TIN"
                                       class="w-full border rounded-md px-3 py-2 focus:ring-green-500 focus:border-green-500">
                            </div>
                        </div>

                        <!-- Right column -->
                        <div class="space-y-4">
                            <div>
                                <label class="block font-medium mb-1">Jina la Mmiliki</label>
                                <input type="text" name="owner_name" value="{{ old('owner_name', $company->owner_name ?? '') }}"
                                       class="w-full border rounded-md px-3 py-2 focus:ring-green-500 focus:border-green-500" required>
                            </div>

                            <div>
                                <label class="block font-medium mb-1">Mahali Ilipo</label>
                                <input type="text" name="location" value="{{ old('location', $company->location ?? '') }}"
                                       class="w-full border rounded-md px-3 py-2 focus:ring-green-500 focus:border-green-500" required>
                            </div>

                            <div>
                                <label class="block font-medium mb-1">Namba ya Simu</label>
                                <input type="text" name="phone" value="{{ old('phone', $company->phone ?? '') }}"
                                       class="w-full border rounded-md px-3 py-2 focus:ring-green-500 focus:border-green-500" required>
                            </div>

                            <div>
                                <label class="block font-medium mb-1">Barua Pepe</label>
                                <input type="email" name="company_email"
                                       value="{{ old('company_email', $company->company_email ?? $company->email ?? '') }}"
                                       class="w-full border rounded-md px-3 py-2 focus:ring-green-500 focus:border-green-500" required>
                            </div>

                            <div>
                                <label class="block font-medium mb-1">Logo ya Kampuni</label>
                                <input type="file" name="logo" accept="image/*"
                                       class="w-full border rounded-md px-3 py-2 bg-gray-50">
                            </div>
                        </div>

                    </div>

                    <!-- Buttons -->
                    <div class="mt-8 flex justify-end gap-3">
                        <button type="submit"
                                class="px-5 py-2 bg-green-700 text-white rounded-full hover:bg-green-800 transition">
                            💾 Hifadhi
                        </button>
                    </div>

                    @if($errors->any())
                        <div class="mt-4 text-red-500 text-sm">
                            <ul class="list-disc list-inside">
                                @foreach($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Tabs JS -->
<script>
    const tabInfo = document.getElementById('tab-info');
    const tabEdit = document.getElementById('tab-edit');
    const contentInfo = document.getElementById('content-info');
    const contentEdit = document.getElementById('content-edit');

    tabInfo.addEventListener('click', () => {
        contentInfo.classList.remove('hidden');
        contentEdit.classList.add('hidden');
        tabInfo.classList.add('text-gray-700', 'border-green-700');
        tabInfo.classList.remove('text-gray-500', 'border-transparent');
        tabEdit.classList.remove('text-gray-700', 'border-green-700');
        tabEdit.classList.add('text-gray-500', 'border-transparent');
    });

    tabEdit.addEventListener('click', () => {
        contentEdit.classList.remove('hidden');
        contentInfo.classList.add('hidden');
        tabEdit.classList.add('text-gray-700', 'border-green-700');
        tabEdit.classList.remove('text-gray-500', 'border-transparent');
        tabInfo.classList.remove('text-gray-700', 'border-green-700');
        tabInfo.classList.add('text-gray-500', 'border-transparent');
    });
</script>
@endsection
