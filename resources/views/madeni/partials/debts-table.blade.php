@if($madeni->count() > 0)
<div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-emerald-50">
                <th class="px-4 py-2 text-left font-medium text-emerald-800">Tarehe</th>
                <th class="px-4 py-2 text-left font-medium text-emerald-800">Mkopaji</th>
                <th class="px-4 py-2 text-left font-medium text-emerald-800 hidden md:table-cell">Bidhaa</th>
                <th class="px-4 py-2 text-center font-medium text-emerald-800">Idadi</th>
                <th class="px-4 py-2 text-right font-medium text-emerald-800">Deni</th>
                <th class="px-4 py-2 text-right font-medium text-emerald-800">Baki</th>
                <th class="px-4 py-2 text-center font-medium text-emerald-800 print:hidden">Vitendo</th>
            </tr>
        </thead>
        <tbody id="debts-tbody" class="divide-y divide-gray-100">
            @forelse($madeni as $deni)
                <tr class="debt-row hover:bg-gray-50" data-debt='@json($deni)'>
                    <td class="px-4 py-2">
                        <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($deni->created_at)->format('d/m/Y') }}</div>
                        <div class="text-xs text-gray-500">Kutakiwa: {{ \Carbon\Carbon::parse($deni->tarehe_malipo)->format('d/m/Y') }}</div>
                    </td>
                    <td class="px-4 py-2">
                        <div class="font-medium text-gray-900 text-sm">{{ $deni->jina_mkopaji }}</div>
                        @if($deni->simu)
                        <div class="text-xs text-emerald-600">{{ $deni->simu }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-2 hidden md:table-cell">
                        <span class="text-sm text-gray-700">{{ $deni->bidhaa->jina ?? 'N/A' }}</span>
                        @if($deni->punguzo > 0)
                        <div class="text-xs text-gray-500">Punguzo: {{ number_format($deni->punguzo, 2) }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-2 text-center">
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800">
                            {{ $deni->idadi }}
                        </span>
                    </td>
                    <td class="px-4 py-2 text-right">
                        <div class="text-sm font-bold text-gray-900">{{ number_format($deni->jumla, 2) }}</div>
                    </td>
                    <td class="px-4 py-2 text-right">
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-bold 
                            @if($deni->baki <= 0) bg-green-100 text-green-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ number_format($deni->baki, 2) }}
                            @if($deni->baki <= 0)
                            <i class="fas fa-check ml-1 text-xs"></i>
                            @endif
                        </span>
                    </td>
                    <td class="px-4 py-2 text-center print:hidden">
                        <div class="flex justify-center space-x-2">
                            @if($deni->baki > 0)
                            <button class="pay-debt-btn text-green-600 hover:text-green-800 p-1 rounded-full hover:bg-green-50"
                                    data-id="{{ $deni->id }}" title="Lipa Deni">
                                <i class="fas fa-money-bill-wave"></i>
                            </button>
                            @endif
                            <button class="edit-debt-btn text-amber-600 hover:text-amber-800 p-1 rounded-full hover:bg-amber-50"
                                    data-id="{{ $deni->id }}" title="Badili Deni">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="delete-debt-btn text-red-600 hover:text-red-800 p-1 rounded-full hover:bg-red-50"
                                    data-id="{{ $deni->id }}" data-name="{{ $deni->jina_mkopaji }}" title="Futa Deni">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                        <i class="fas fa-hand-holding-usd text-3xl mb-2 text-gray-300"></i>
                        <p>Hakuna madeni yaliyorekodiwa bado</p>
                        <p class="text-xs text-gray-500 mt-1">Anza kwa kuingiza deni jipya</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
@if($madeni->hasPages())
<div class="px-4 py-3 border-t border-gray-200">
    {{ $madeni->links() }}
</div>
@endif
@else
<div class="p-8 text-center text-gray-500">
    <i class="fas fa-hand-holding-usd text-3xl mb-2 text-gray-300"></i>
    <p>Hakuna madeni yaliyorekodiwa bado</p>
    <p class="text-xs text-gray-500 mt-1">Anza kwa kuingiza deni jipya</p>
</div>
@endif