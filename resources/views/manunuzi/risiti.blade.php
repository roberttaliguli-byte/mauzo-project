@extends('layouts.app')

@section('title', 'Risiti ' . $risiti_number)

@section('content')
<div class="max-w-6xl mx-auto space-y-4 print:max-w-none print:m-0 print:p-0">
    
    <!-- Action Buttons (Hidden on Print) -->
    <div class="flex justify-between items-center print:hidden mb-4">
        <a href="{{ route('manunuzi.index') }}" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-50 font-medium shadow-sm transition text-sm">
            <i class="fas fa-arrow-left mr-2"></i> Rudi Kwenye Manunuzi
        </a>
        <div class="flex gap-2">
            <button onclick="window.print()" class="bg-emerald-600 text-white px-6 py-2 rounded hover:bg-emerald-700 font-bold shadow-md transition text-sm">
                <i class="fas fa-print mr-2"></i> Chapisha Risiti
            </button>
        </div>
    </div>

    <!-- Printable Receipt Area -->
    <div id="print-area" class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden print:border-none print:shadow-none print:rounded-none">
        
        <!-- Top Accent Line -->
        <div class="h-2 bg-emerald-600 w-full print:bg-gray-800"></div>

        <div class="p-6 md:p-10">
            
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center border-b border-gray-200 pb-6 mb-6 gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-emerald-700 uppercase tracking-wide print:text-black">{{ $company->name ?? 'DUKA' }}</h1>
                    <p class="text-sm text-gray-500 mt-1 print:text-gray-700">{{ $company->address ?? 'Anwani haijasajiliwa' }}</p>
                    <p class="text-sm text-gray-500 print:text-gray-700">Simu: {{ $company->phone ?? '-' }}</p>
                </div>
                <div class="text-left md:text-right">
                    <h2 class="text-xl font-bold text-gray-800 uppercase print:text-black">Risiti ya Manunuzi</h2>
                    <div class="mt-2 inline-block bg-emerald-50 border border-emerald-200 text-emerald-800 font-mono text-sm font-bold px-3 py-1 rounded print:bg-transparent print:border-gray-400 print:text-black">
                        {{ $risiti_number }}
                    </div>
                </div>
            </div>

            <!-- Meta Info Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8 text-sm">
                <div>
                    <span class="block text-gray-500 uppercase text-xs font-bold tracking-wider mb-1 print:text-gray-700">Msaplaya</span>
                    <span class="font-medium text-gray-900 text-base print:text-black">{{ $supplier ?? '--' }}</span>
                </div>
                <div>
                    <span class="block text-gray-500 uppercase text-xs font-bold tracking-wider mb-1 print:text-gray-700">Simu ya Msaplaya</span>
                    <span class="font-medium text-gray-900 text-base print:text-black">{{ $simu ?? '--' }}</span>
                </div>
                <div>
                    <span class="block text-gray-500 uppercase text-xs font-bold tracking-wider mb-1 print:text-gray-700">Tarehe</span>
                    <span class="font-medium text-gray-900 text-base print:text-black">{{ $date->format('d/m/Y H:i') }}</span>
                </div>
                <div>
                    <span class="block text-gray-500 uppercase text-xs font-bold tracking-wider mb-1 print:text-gray-700">Mhudumu</span>
                    <span class="font-medium text-gray-900 text-base print:text-black">{{ $creator->name ?? 'System' }}</span>
                </div>
            </div>

            <!-- Items Table -->
            <div class="border border-gray-200 rounded-lg overflow-hidden mb-8 print:border-gray-300">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-gray-700 print:bg-gray-100 print:text-black border-b border-gray-200 print:border-gray-300">
                        <tr>
                            <th class="px-6 py-4 font-bold uppercase tracking-wider">Maelezo ya Bidhaa</th>
                            <th class="px-6 py-4 font-bold uppercase tracking-wider text-center">Idadi</th>
                            <th class="px-6 py-4 font-bold uppercase tracking-wider text-right">Bei ya Kununua</th>
                            <th class="px-6 py-4 font-bold uppercase tracking-wider text-right">Jumla</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($items as $item)
                        <tr class="bg-white hover:bg-gray-50 transition print:hover:bg-transparent">
                            <td class="px-6 py-5">
                                <div class="font-bold text-gray-900 text-base print:text-black">{{ $item->bidhaa->jina ?? '--' }}</div>
                                <div class="text-gray-500 mt-1 print:text-gray-700">{{ $item->bidhaa->aina ?? '' }} - {{ $item->bidhaa->kipimo ?? '' }}</div>
                            </td>
                            <td class="px-6 py-5 text-center font-medium text-gray-700 print:text-black">
                                {{ number_format($item->idadi, 2) }}
                            </td>
                            <td class="px-6 py-5 text-right font-medium text-gray-700 print:text-black">
                                {{ number_format($item->unit_cost, 2) }}
                            </td>
                            <td class="px-6 py-5 text-right font-bold text-emerald-700 text-base print:text-black">
                                {{ number_format($item->bei, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Summary & Notes -->
            <div class="flex flex-col md:flex-row justify-between items-start gap-8">
                <!-- Notes -->
                <div class="flex-1 text-sm text-gray-500">
                    <h4 class="font-bold text-gray-700 mb-2 uppercase tracking-wider text-xs">Maelezo ya Ziada</h4>
                    <p class="mb-4">{{ $manunuzi->mengineyo ?? 'Hakuna maelezo ya ziada kwa manunuzi haya.' }}</p>
                    <p class="text-xs italic"><i class="fas fa-info-circle mr-1"></i> Risiti hii ni uthibitisho wa manunuzi ya bidhaa na kuongezwa kwenye stock. Hakuna marejesho baada ya bidhaa kuingizwa kwenye mfumo.</p>
                </div>
                
                <!-- Totals Box -->
                <div class="w-full md:w-80 bg-gray-50 rounded-lg p-5 border border-gray-200 print:bg-transparent print:border-gray-300">
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between items-center text-gray-600 print:text-black">
                            <span>Jumla Ndogo:</span>
                            <span class="font-medium">{{ number_format($total, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center text-gray-600 print:text-black">
                            <span>Kodi (VAT 0%):</span>
                            <span class="font-medium">0.00</span>
                        </div>
                        <div class="border-t border-gray-300 pt-3 flex justify-between items-center">
                            <span class="font-bold text-gray-800 text-base print:text-black uppercase">Jumla Kuu</span>
                            <span class="font-bold text-emerald-700 text-xl print:text-black">TZS {{ number_format($total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Signatures -->
            <div class="mt-16 grid grid-cols-2 gap-12 border-t border-gray-200 pt-8 print:border-gray-300">
                <div>
                    <div class="border-b border-gray-400 w-full mb-2 h-12"></div>
                    <p class="text-xs text-gray-500 uppercase tracking-wider print:text-gray-700">Sahihi ya Msaplaya / Mtoaji</p>
                </div>
                <div class="text-right">
                    <div class="border-b border-gray-400 w-full mb-2 h-12 ml-auto"></div>
                    <p class="text-xs text-gray-500 uppercase tracking-wider print:text-gray-700">Sahihi ya Mpokeaji</p>
                </div>
            </div>

        </div>

        <!-- Footer -->
        <div class="bg-gray-50 p-6 text-center border-t border-gray-200 print:bg-transparent print:border-gray-300">
            <p class="font-bold text-gray-700 print:text-black">Asante kwa kufanya biashara nasi!</p>
            <p class="text-xs text-gray-500 mt-1 print:text-gray-700">Risiti iliyotengenezwa na mfumo wa <strong>{{ $company->name ?? 'Duka' }}</strong> - {{ now()->format('d/m/Y') }}</p>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    @media print {
        body * { visibility: hidden; }
        #print-area, #print-area * { visibility: visible; }
        #print-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            margin: 0;
            padding: 0;
        }
        body { background-color: white !important; }
        .print\:hidden { display: none !important; }
        #print-area h1 { font-size: 24px; margin-bottom: 5px; }
        #print-area h2 { font-size: 18px; }
        table { page-break-inside: avoid; }
        tr { page-break-inside: avoid; }
        @page { margin: 15mm; size: A4 portrait; }
    }
</style>
@endpush