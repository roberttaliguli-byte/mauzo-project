{{-- resources/views/stock-take/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Uhesabuji wa Stock')
@section('page-title', 'Uhesabuji wa Stock')
@section('page-subtitle', now()->format('d/m/Y'))

@section('content')
<div class="space-y-3 md:space-y-4 px-2 md:px-0">
    @include('stock-take._nav', ['active' => 'start'])

    {{-- Flash --}}
    @if(session('success'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2.5 text-xs md:text-sm text-emerald-800 flex items-center gap-2">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="rounded-lg border border-red-200 bg-red-50 px-3 py-2.5 text-xs md:text-sm text-red-800 flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    {{-- Active or Start card --}}
    @if($open)
        <div class="bg-white rounded-xl border border-emerald-200 shadow-sm p-4 md:p-5">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <p class="text-[10px] md:text-xs text-emerald-700 font-medium">
                        <i class="fas fa-circle text-[6px] mr-1"></i> Inaendelea · {{ $open->reference }}
                    </p>
                    <h2 class="text-sm md:text-base font-semibold text-gray-900 mt-0.5">{{ $open->title }}</h2>
                    <p class="text-[10px] md:text-xs text-gray-500 mt-0.5">
                        Ilianza {{ $open->started_at->format('d/m/Y H:i') }} na {{ $open->started_by_name }}
                    </p>
                </div>
                <a href="{{ route('stocktake.show', $open->id) }}"
                   class="px-3 md:px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs md:text-sm font-medium transition">
                    <i class="fas fa-play mr-1"></i> Endelea kuhesabu
                </a>
            </div>
            <div class="mt-3">
                <div class="flex justify-between text-[10px] md:text-xs text-gray-600 mb-1">
                    <span><b class="text-emerald-700">{{ $openCounts['counted'] }}</b> / {{ $openCounts['total'] }} imehesabiwa</span>
                    <span class="text-amber-700">{{ $openCounts['remaining'] }} zimebaki</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                    <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 h-2 rounded-full transition-all duration-500"
                         style="width: {{ $openCounts['percent'] }}%"></div>
                </div>
            </div>
        </div>
    @else
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 md:p-5">
            <div class="flex items-center gap-2 mb-1">
                <div class="w-1 h-4 bg-gradient-to-b from-emerald-500 to-emerald-600 rounded-full"></div>
                <h2 class="text-sm md:text-base font-semibold text-gray-900">Anza uhesabuji mpya</h2>
            </div>
            <p class="text-xs md:text-sm text-gray-600 mb-3">
                Bidhaa zote <b>{{ $productCount }}</b> zitaingizwa — pamoja na zile zenye stock 0.
                Kuhesabu <b>hakubadilishi</b> stock ya mfumo; utaona tofauti kwanza.
            </p>
            <form method="POST" action="{{ route('stocktake.start') }}" class="grid grid-cols-1 md:grid-cols-3 gap-2 md:gap-3">
                @csrf
                <input type="text" name="title" maxlength="120" placeholder="Jina (si lazima) — mf. Hesabu ya mwezi"
                    class="px-3 py-2 border border-gray-300 rounded-lg text-xs md:text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <input type="text" name="notes" maxlength="1000" placeholder="Maelezo (si lazima)"
                    class="px-3 py-2 border border-gray-300 rounded-lg text-xs md:text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <button class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs md:text-sm font-semibold transition">
                    <i class="fas fa-plus mr-1"></i> Anza
                </button>
            </form>
            <p class="text-[10px] md:text-xs text-amber-700 mt-2 flex items-start gap-1.5">
                <i class="fas fa-lightbulb mt-0.5"></i>
                <span>Endelea kuuza kama kawaida. Kila bidhaa inalinganishwa na stock ya mfumo wakati wa kuhesabu.</span>
            </p>
        </div>
    @endif

    {{-- History table --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-3 md:px-4 py-2.5 md:py-3 border-b border-gray-200 flex items-center gap-2">
            <div class="w-1 h-3 md:h-4 bg-gradient-to-b from-emerald-500 to-emerald-600 rounded-full"></div>
            <h3 class="font-semibold text-xs md:text-sm text-gray-800">Historia ya uhesabuji</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs md:text-sm">
                <thead class="bg-gray-50 text-[10px] md:text-xs text-gray-500 uppercase tracking-wide">
                    <tr>
                        <th class="px-2 md:px-3 py-2 text-left">Tarehe</th>
                        <th class="px-2 md:px-3 py-2 text-left">Kumbukumbu</th>
                        <th class="px-2 md:px-3 py-2 text-left">Aliyehusika</th>
                        <th class="px-2 md:px-3 py-2 text-right">Imehesabiwa</th>
                        <th class="px-2 md:px-3 py-2 text-right">Upungufu</th>
                        <th class="px-2 md:px-3 py-2 text-right">Ziada</th>
                        <th class="px-2 md:px-3 py-2 text-right">Thamani upungufu</th>
                        <th class="px-2 md:px-3 py-2 text-right">Tofauti ya thamani</th>
                        <th class="px-2 md:px-3 py-2"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                @forelse($history as $h)
                    @php
                        $s = $h->summary ?? [];
                        $v = $valueRows[$h->id] ?? null;
                        $diff = $v['difference'] ?? 0;
                    @endphp
                    <tr class="hover:bg-gray-50/60">
                        <td class="px-2 md:px-3 py-2 whitespace-nowrap text-gray-600">
                            {{ optional($h->closed_at ?? $h->started_at)->format('d/m/Y') }}
                        </td>
                        <td class="px-2 md:px-3 py-2">
                            <span class="font-medium text-gray-900">{{ $h->reference }}</span>
                            @if($h->status === 'cancelled')
                                <span class="ml-1 text-[10px] text-gray-400">(imeghairiwa)</span>
                            @endif
                        </td>
                        <td class="px-2 md:px-3 py-2 text-gray-700">{{ $h->closed_by_name ?? $h->started_by_name }}</td>
                        <td class="px-2 md:px-3 py-2 text-right">{{ $s['counted'] ?? '-' }} / {{ $s['total'] ?? '-' }}</td>
                        <td class="px-2 md:px-3 py-2 text-right text-red-700">{{ $s['shortage'] ?? '-' }}</td>
                        <td class="px-2 md:px-3 py-2 text-right text-blue-700">{{ $s['excess'] ?? '-' }}</td>
                        <td class="px-2 md:px-3 py-2 text-right font-medium">
                            {{ isset($s['shortage_value']) ? number_format($s['shortage_value'], 0) . ' TZS' : '-' }}
                        </td>
                        <td class="px-2 md:px-3 py-2 text-right font-medium {{ $diff < 0 ? 'text-red-700' : ($diff > 0 ? 'text-blue-700' : 'text-gray-500') }}">
                            {{ $h->status === 'closed' && $v ? (($diff > 0 ? '+' : '') . number_format($diff, 0) . ' TZS') : '-' }}
                        </td>
                        <td class="px-2 md:px-3 py-2 text-right whitespace-nowrap space-x-1">
                            <a class="text-emerald-700 hover:underline font-medium" href="{{ route('stocktake.show', $h->id) }}">Ripoti</a>
                            @if($h->status === 'closed' && $h->previous_take_id)
                                <a class="text-blue-700 hover:underline" href="{{ route('stocktake.compare', $h->id) }}">Linganisha</a>
                            @endif
                            @if($h->status === 'closed' && !$h->applied_at)
                                <button class="apply-stock px-2 py-1 rounded bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] md:text-xs font-medium"
                                    data-id="{{ $h->id }}" data-ref="{{ $h->reference }}"
                                    data-counted="{{ $v['counted_items'] ?? 0 }}"
                                    data-uncounted="{{ $v['uncounted_items'] ?? 0 }}"
                                    data-sys="{{ $v['system_value'] ?? 0 }}"
                                    data-cnt="{{ $v['counted_value'] ?? 0 }}">
                                    Sasisha stock
                                </button>
                            @endif
                            @if($h->applied_at)
                                <span class="inline-flex items-center gap-1 text-[10px] md:text-xs text-emerald-700"
                                      title="Ilitumika {{ $h->applied_at->format('d/m/Y H:i') }} na {{ $h->applied_by_name }}">
                                    <i class="fas fa-check-circle"></i> Imesasishwa
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-3 py-10 text-center text-gray-500 text-xs md:text-sm">
                            <i class="fas fa-clipboard-list text-gray-300 text-2xl mb-2 block"></i>
                            Hakuna historia ya uhesabuji bado.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ===== Confirmation modal (replaces native confirm) ===== --}}
    <div id="ap-modal" class="hidden fixed inset-0 z-50 flex items-end sm:items-center justify-center">
        <div id="ap-bg" class="absolute inset-0 bg-black/50"></div>
        <div class="relative w-full sm:max-w-md bg-white rounded-t-3xl sm:rounded-2xl shadow-2xl max-h-[92vh] flex flex-col">
            <div class="px-5 pt-5 pb-3">
                <div class="w-10 h-1 bg-gray-300 rounded-full mx-auto mb-4 sm:hidden"></div>
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 shrink-0 rounded-xl flex items-center justify-center bg-emerald-100 text-emerald-600">
                        <i class="fas fa-boxes-stacked"></i>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-base font-semibold text-gray-900">Sasisha stock?</h3>
                        <p class="text-[11px] text-gray-500 truncate" id="ap-ref">—</p>
                    </div>
                </div>
            </div>

            <div class="px-5 pb-4 overflow-y-auto space-y-3 flex-1">
                <div class="grid grid-cols-3 gap-2 text-center">
                    <div class="rounded-lg border border-gray-200 bg-gray-50 px-2 py-2.5">
                        <p class="text-[10px] text-gray-500 leading-none">Ya sasa</p>
                        <p class="text-sm font-bold text-gray-900 tabular-nums mt-1" id="ap-sys">0</p>
                    </div>
                    <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-2 py-2.5">
                        <p class="text-[10px] text-emerald-700 leading-none">Mpya</p>
                        <p class="text-sm font-bold text-emerald-800 tabular-nums mt-1" id="ap-cnt">0</p>
                    </div>
                    <div id="ap-diff-box" class="rounded-lg border px-2 py-2.5">
                        <p class="text-[10px] leading-none" id="ap-diff-label">Tofauti</p>
                        <p class="text-sm font-bold tabular-nums mt-1" id="ap-diff">0</p>
                    </div>
                </div>

                <div class="rounded-lg border border-gray-200 bg-white p-3 space-y-1.5 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600"><i class="fas fa-check text-emerald-600 mr-1"></i>Bidhaa zilizohesabiwa</span>
                        <b class="text-gray-900" id="ap-counted">0</b>
                    </div>
                    <div class="flex items-center justify-between" id="ap-unc-row">
                        <span class="text-gray-600"><i class="fas fa-question text-amber-600 mr-1"></i>Hazikuhesabiwa</span>
                        <b class="text-gray-900" id="ap-unc">0</b>
                    </div>
                </div>

                <div class="rounded-lg border border-amber-200 bg-amber-50 p-3 text-[11px] text-amber-900 space-y-1">
                    <p><i class="fas fa-triangle-exclamation mr-1"></i><b>Angalia:</b></p>
                    <ul class="pl-4 list-disc space-y-0.5 opacity-90">
                        <li>Bidhaa zilizohesabiwa sifuri zitakuwa sifuri.</li>
                        <li>Mauzo/manunuzi baada ya kuhesabu yatapotea.</li>
                        <li>Idadi ya awali inabaki kwenye historia.</li>
                        <li class="font-semibold">Haiwezi kutenduliwa.</li>
                    </ul>
                </div>
            </div>

            <div class="px-5 py-3 border-t border-gray-100 flex gap-2 pb-[max(0.75rem,env(safe-area-inset-bottom))]">
                <button id="ap-cancel" class="flex-1 py-3 border border-gray-300 hover:bg-gray-50 rounded-xl text-sm font-semibold text-gray-700">Ghairi</button>
                <button id="ap-go" class="flex-1 py-3 rounded-xl text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 disabled:opacity-40 disabled:cursor-not-allowed">
                    <i class="fas fa-check mr-1"></i> Ndio, sasisha
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const base = @json(url('stock-take'));

    const modal  = document.getElementById('ap-modal');
    const bg     = document.getElementById('ap-bg');
    const cancel = document.getElementById('ap-cancel');
    const go     = document.getElementById('ap-go');
    const el = (id) => document.getElementById(id);

    let pending = null; // { id, ref, btn }

    const fmt = (v) => Math.abs(v).toLocaleString(undefined, {maximumFractionDigits: 0});
    const sign = (v) => (v < 0 ? '-' : '+') + fmt(v);

    function openModal(btn) {
        const sys = Number(btn.dataset.sys || 0);
        const cnt = Number(btn.dataset.cnt || 0);
        const unc = Number(btn.dataset.uncounted || 0);
        const diff = cnt - sys;

        pending = { id: btn.dataset.id, ref: btn.dataset.ref, btn };

        el('ap-ref').textContent = btn.dataset.ref;
        el('ap-sys').textContent = fmt(sys);
        el('ap-cnt').textContent = fmt(cnt);
        el('ap-counted').textContent = btn.dataset.counted;
        el('ap-unc').textContent = unc;
        el('ap-unc-row').classList.toggle('hidden', unc === 0);

        const dbox = el('ap-diff-box');
        const dlab = el('ap-diff-label');
        const dnum = el('ap-diff');

        dnum.textContent = (diff > 0 ? '+' : diff < 0 ? '-' : '') + fmt(diff);

        if (diff < 0) {
            dbox.className = 'rounded-lg border border-red-200 bg-red-50 px-2 py-2.5';
            dlab.className = 'text-[10px] leading-none text-red-700';
            dnum.className = 'text-sm font-bold tabular-nums mt-1 text-red-700';
        } else if (diff > 0) {
            dbox.className = 'rounded-lg border border-blue-200 bg-blue-50 px-2 py-2.5';
            dlab.className = 'text-[10px] leading-none text-blue-700';
            dnum.className = 'text-sm font-bold tabular-nums mt-1 text-blue-700';
        } else {
            dbox.className = 'rounded-lg border border-gray-200 bg-gray-50 px-2 py-2.5';
            dlab.className = 'text-[10px] leading-none text-gray-500';
            dnum.className = 'text-sm font-bold tabular-nums mt-1 text-gray-700';
        }

        go.disabled = false;
        go.innerHTML = '<i class="fas fa-check mr-1"></i> Ndio, sasisha';
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
        pending = null;
    }

    cancel.addEventListener('click', closeModal);
    bg.addEventListener('click', closeModal);
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
    });

    go.addEventListener('click', async () => {
        if (!pending) return;
        const { id, btn } = pending;
        go.disabled = true;
        go.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Inasasisha…';
        btn.disabled = true;
        btn.textContent = 'Inasasisha…';

        try {
            const r = await fetch(`${base}/${id}/apply-stock`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({})
            });
            const j = await r.json();
            if (j.success) {
                closeModal();
                toast(j.message || 'Stock imesasishwa.', 'success');
                setTimeout(() => { location.href = j.redirect || location.href; }, 900);
            } else {
                toast(j.message || 'Imeshindikana kusasisha stock.', 'error');
                go.disabled = false;
                go.innerHTML = '<i class="fas fa-check mr-1"></i> Ndio, sasisha';
                btn.disabled = false;
                btn.textContent = 'Sasisha stock';
            }
        } catch {
            toast('Hitilafu ya mtandao. Jaribu tena.', 'error');
            go.disabled = false;
            go.innerHTML = '<i class="fas fa-check mr-1"></i> Ndio, sasisha';
            btn.disabled = false;
            btn.textContent = 'Sasisha stock';
        }
    });

    document.querySelectorAll('.apply-stock').forEach((b) => {
        b.addEventListener('click', () => openModal(b));
    });

    /* ----- small toast (matches cleanup page style) ----- */
    function toast(msg, type) {
        const d = document.createElement('div');
        d.className = 'fixed top-5 left-1/2 -translate-x-1/2 z-[60] max-w-[90vw] px-4 py-3 rounded-xl shadow-lg text-sm font-medium text-white ' +
                      (type === 'error' ? 'bg-red-600' : 'bg-emerald-600');
        d.textContent = msg;
        document.body.appendChild(d);
        setTimeout(() => d.remove(), 3500);
    }
})();
</script>
@endpush