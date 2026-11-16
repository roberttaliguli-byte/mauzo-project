{{-- resources/views/stock-take/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Uhesabuji wa Stock')
@section('page-title', $take->title ?: 'Uhesabuji wa Stock')
@section('page-subtitle', $take->reference)

@section('content')
<div id="st-app" class="space-y-3 md:space-y-4 px-2 md:px-0"
     data-base="{{ url('stock-take/' . $take->id) }}"
     data-readonly="{{ $readonly ? '1' : '0' }}"
     data-compare="{{ $take->previous_take_id ? route('stocktake.compare', $take->id) : '' }}">

    @include('stock-take._nav', ['active' => $readonly ? 'start' : 'current'])

    {{-- ===== Header bar ===== --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-3 flex flex-wrap items-center justify-between gap-2">
        <div class="text-[10px] md:text-xs text-gray-600">
            Ilianza {{ $take->started_at->format('d/m/Y H:i') }} na <b>{{ $take->started_by_name }}</b>
            @if($take->isClosed())
                &middot; Ilifungwa {{ $take->closed_at->format('d/m/Y H:i') }} na <b>{{ $take->closed_by_name }}</b>
                <span class="ml-1 px-1.5 py-0.5 rounded bg-gray-100 text-gray-700 text-[10px]">Imefungwa</span>
            @endif
            @if($take->status === 'cancelled')
                <span class="ml-1 px-1.5 py-0.5 rounded bg-red-100 text-red-700 text-[10px]">Imeghairiwa</span>
            @endif
        </div>
        <div class="flex flex-wrap gap-1.5 md:gap-2">
            @if($take->previous_take_id)
                <a href="{{ route('stocktake.compare', $take->id) }}"
                   class="px-2.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-[10px] md:text-xs font-medium transition">
                    <i class="fas fa-code-compare mr-1"></i> Linganisha
                </a>
            @endif
            @if(!$readonly)
                <button id="btn-sync" class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-[10px] md:text-xs font-medium transition">
                    <i class="fas fa-sync mr-1"></i> Bidhaa mpya
                </button>
                <button id="btn-cancel" class="px-2.5 py-1.5 bg-gray-100 hover:bg-red-50 text-red-700 rounded-lg text-[10px] md:text-xs font-medium transition">
                    <i class="fas fa-xmark mr-1"></i> Ghairi
                </button>
                <button id="btn-close" class="px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[10px] md:text-xs font-semibold transition">
                    <i class="fas fa-check mr-1"></i> Kamilisha
                </button>
            @endif
        </div>
    </div>

    {{-- ===== Value comparison ===== --}}
    @if($take->status === 'closed' && $valueComparison)
    @php
        $d   = $valueComparison['difference'];
        $vCls = $d < 0 ? 'border-red-200 bg-red-50' : ($d > 0 ? 'border-emerald-200 bg-emerald-50' : 'border-gray-200 bg-gray-50');
        $vTxt = $d < 0 ? 'text-red-700'  : ($d > 0 ? 'text-emerald-700'  : 'text-gray-700');
        $vNum = $d < 0 ? 'text-red-800'  : ($d > 0 ? 'text-emerald-800'  : 'text-gray-800');
        $vMsg = $d < 0 ? 'Upungufu — thamani imepungua' : ($d > 0 ? 'Ziada — thamani imeongezeka' : 'Hakuna tofauti');
    @endphp
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-3 md:p-4">
        <div class="flex flex-wrap items-start justify-between gap-2 mb-3">
            <div>
                <div class="flex items-center gap-2">
                    <div class="w-1 h-4 bg-gradient-to-b from-emerald-500 to-emerald-600 rounded-full"></div>
                    <h3 class="text-xs md:text-sm font-semibold text-gray-900">Thamani ya stock</h3>
                </div>
                <p class="text-[10px] md:text-xs text-gray-500 mt-0.5">
                    Bidhaa zilizohesabiwa tu · kwa bei ya nunua (si bei ya uza)
                </p>
            </div>
            @if($take->applied_at)
                <span class="px-2 py-1 rounded text-[10px] md:text-xs font-medium bg-emerald-100 text-emerald-800"
                      title="Ilitumika {{ $take->applied_at->format('d/m/Y H:i') }} na {{ $take->applied_by_name }}">
                    <i class="fas fa-check-circle mr-1"></i> Imesasishwa {{ $take->applied_at->format('d/m/Y H:i') }}
                </span>
            @else
                <button id="btn-apply-stock"
                    data-ref="{{ $take->reference }}"
                    data-counted="{{ $valueComparison['counted_items'] }}"
                    data-uncounted="{{ $valueComparison['uncounted_items'] }}"
                    data-sys="{{ $valueComparison['system_value'] }}"
                    data-cnt="{{ $valueComparison['counted_value'] }}"
                    class="px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[10px] md:text-xs font-semibold transition">
                    <i class="fas fa-arrow-up-from-bracket mr-1"></i> Sasisha stock
                </button>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-2 md:gap-3">
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-2.5 md:p-3">
                <p class="text-[10px] md:text-xs text-gray-500">Thamani ya sasa</p>
                <p class="text-base md:text-xl font-bold text-gray-900">{{ number_format($valueComparison['system_value'], 0) }} <span class="text-[10px] md:text-xs font-normal text-gray-500">TZS</span></p>
                <p class="text-[9px] md:text-[10px] text-gray-500 mt-0.5">Idadi ya mfumo × bei ya nunua</p>
            </div>
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-2.5 md:p-3">
                <p class="text-[10px] md:text-xs text-gray-500">Thamani iliyohesabiwa</p>
                <p class="text-base md:text-xl font-bold text-gray-900">{{ number_format($valueComparison['counted_value'], 0) }} <span class="text-[10px] md:text-xs font-normal text-gray-500">TZS</span></p>
                <p class="text-[9px] md:text-[10px] text-gray-500 mt-0.5">Bidhaa {{ $valueComparison['counted_items'] }} zimehesabiwa</p>
            </div>
            <div class="rounded-lg border {{ $vCls }} p-2.5 md:p-3">
                <p class="text-[10px] md:text-xs {{ $vTxt }}">Tofauti ya thamani</p>
                <p class="text-base md:text-xl font-bold {{ $vNum }}">{{ ($d > 0 ? '+' : '') . number_format($d, 0) }} <span class="text-[10px] md:text-xs font-normal">TZS</span></p>
                <p class="text-[9px] md:text-[10px] {{ $vTxt }} mt-0.5">{{ $vMsg }}</p>
            </div>
        </div>

        @if($valueComparison['uncounted_items'])
            <p class="text-[10px] md:text-xs text-amber-700 mt-2 flex items-start gap-1">
                <i class="fas fa-triangle-exclamation mt-0.5"></i>
                <span>Bidhaa {{ $valueComparison['uncounted_items'] }} hazikuhesabiwa — hazitabadilika (thamani: {{ number_format($valueComparison['uncounted_system_value'], 0) }} TZS).</span>
            </p>
        @endif
    </div>
    @endif

    {{-- ===== Summary cards ===== --}}
    @php
        $dotMap = ['emerald' => 'bg-emerald-500', 'red' => 'bg-red-500', 'blue' => 'bg-blue-500', 'purple' => 'bg-purple-500', 'gray' => 'bg-gray-400'];
    @endphp
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-2" id="cards">
        @foreach([
            ['total',       'Zote',         'emerald', 'text-gray-900'],
            ['counted',     'Zimehesabiwa', 'emerald', 'text-emerald-700'],
            ['matched',     'Sawa',         'emerald', 'text-emerald-700'],
            ['shortage',    'Upungufu',     'red',     'text-red-700'],
            ['excess',      'Ziada',        'blue',    'text-blue-700'],
            ['not_found',   'Haipo',        'purple',  'text-purple-700'],
            ['not_counted', 'Bado',         'gray',    'text-gray-600'],
        ] as [$k, $l, $c, $tc])
            <div class="bg-white rounded-lg border border-gray-200 p-2.5 md:p-3">
                <div class="flex items-center gap-1.5 mb-0.5">
                    <span class="w-1.5 h-1.5 rounded-full {{ $dotMap[$c] ?? 'bg-gray-400' }}"></span>
                    <p class="text-[10px] md:text-xs text-gray-500 truncate">{{ $l }}</p>
                </div>
                <p class="text-lg md:text-xl font-bold {{ $tc }}" id="c-{{ $k }}">{{ $summary[$k] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Progress --}}
    <div class="bg-white rounded-lg border border-gray-200 p-3">
        <div class="flex justify-between text-[10px] md:text-xs text-gray-600 mb-1">
            <span id="prog-text">{{ $counts['counted'] }} / {{ $counts['total'] }} zimehesabiwa</span>
            <span id="prog-pct" class="font-medium text-emerald-700">{{ $counts['percent'] }}%</span>
        </div>
        <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
            <div id="prog-bar" class="bg-gradient-to-r from-emerald-500 to-emerald-600 h-2 rounded-full transition-all duration-500"
                 style="width: {{ $counts['percent'] }}%"></div>
        </div>
    </div>

    {{-- ===== Tabs ===== --}}
    <div class="bg-white rounded-lg border border-gray-200 overflow-x-auto">
        <div class="flex min-w-max text-[10px] md:text-xs">
            <button data-tab="count"     class="st-tab px-3 md:px-4 py-2.5 md:py-3 font-medium border-r border-gray-200">1. Hesabu</button>
            <button data-tab="recon"     class="st-tab px-3 md:px-4 py-2.5 md:py-3 font-medium border-r border-gray-200">2. Ulinganisho</button>
            <button data-tab="loss"      class="st-tab px-3 md:px-4 py-2.5 md:py-3 font-medium border-r border-gray-200">3. Upungufu</button>
            <button data-tab="unmatched" class="st-tab px-3 md:px-4 py-2.5 md:py-3 font-medium">4. Si kwenye mfumo</button>
        </div>
    </div>

    {{-- ===== COUNT TAB ===== --}}
    <section id="tab-count" class="st-pane space-y-3">
        <div class="bg-white p-2.5 md:p-3 rounded-lg border border-gray-200 flex flex-col sm:flex-row gap-2">
            <input id="q" type="search" placeholder="Tafuta jina, barcode, kundi…" autocomplete="off"
                class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-xs md:text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <select id="flt" class="px-3 py-2 border border-gray-300 rounded-lg text-xs md:text-sm focus:ring-2 focus:ring-emerald-500">
                <option value="">Zote</option>
                <option value="NOT_COUNTED">Bado kuhesabu</option>
                <option value="MATCHED">Sawa</option>
                <option value="SHORTAGE">Upungufu</option>
                <option value="EXCESS">Ziada</option>
                <option value="NOT_FOUND">Haipo</option>
                <option value="zero_system">Stock 0</option>
            </select>
        </div>
        <div id="list" class="space-y-2"></div>
        <button id="more" class="hidden w-full py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-xs md:text-sm text-gray-700 font-medium transition">
            Onyesha zaidi
        </button>
    </section>

    {{-- ===== RECON TAB ===== --}}
    <section id="tab-recon" class="st-pane hidden space-y-3">
        <div id="recon-chips" class="flex flex-wrap gap-1.5 md:gap-2 text-[10px] md:text-xs"></div>
        @if(!$readonly)
        <div id="bulk" class="bg-white rounded-lg border border-gray-200 p-2.5 md:p-3 grid grid-cols-1 md:grid-cols-4 gap-2 items-start">
            <select id="bulk-action" class="px-2.5 py-2 border border-gray-300 rounded-lg text-xs md:text-sm md:col-span-1">
                <option value="confirmed">Thibitisha hesabu</option>
                <option value="investigating">Chunguza upungufu</option>
                <option value="sale_investigation">Chunguza mauzo ambayo hayakurekodiwa</option>
                <option value="recount">Rudisha kuhesabu</option>
                <option value="unresolved">Acha kwa baadaye</option>
                <option value="adjusted">Rekodi marekebisho ya stock</option>
                <option value="receipt_corrected">Sahihisha risiti ya manunuzi</option>
            </select>
            <input id="bulk-note" type="text" maxlength="1000" placeholder="Sababu (lazima kwa marekebisho)"
                class="px-3 py-2 border border-gray-300 rounded-lg text-xs md:text-sm md:col-span-2 focus:ring-2 focus:ring-emerald-500">
            <button id="bulk-apply" class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs md:text-sm font-semibold transition">
                Tekeleza (<span id="sel-n">0</span>)
            </button>
            <p class="text-[9px] md:text-[10px] text-amber-700 md:col-span-4 flex items-start gap-1">
                <i class="fas fa-info-circle mt-0.5"></i>
                <span>Kuhesabu hakubadilishi stock. Ni <b>marekebisho</b> na <b>risiti</b> pekee hubadilisha — kila moja inaandikwa jina lako, sababu, na idadi ya zamani/mpya.</span>
            </p>
        </div>
        @endif
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-[10px] md:text-xs">
                    <thead class="bg-gray-50 text-gray-500 uppercase tracking-wide">
                        <tr>
                            <th class="px-2 py-2 w-8"><input type="checkbox" id="sel-all" class="accent-emerald-600"></th>
                            <th class="px-2 py-2 text-left">Bidhaa</th>
                            <th class="px-2 py-2 text-right">Awali</th>
                            <th class="px-2 py-2 text-right">Iliyoingia</th>
                            <th class="px-2 py-2 text-right">Iliyouzwa</th>
                            <th class="px-2 py-2 text-right">Inatarajiwa</th>
                            <th class="px-2 py-2 text-right">Mfumo</th>
                            <th class="px-2 py-2 text-right">Halisi</th>
                            <th class="px-2 py-2 text-right">Tofauti vs mfumo</th>
                            <th class="px-2 py-2 text-right">Tofauti vs tarajio</th>
                            <th class="px-2 py-2 text-right">Thamani (TZS)</th>
                            <th class="px-2 py-2 text-left">Hali</th>
                        </tr>
                    </thead>
                    <tbody id="recon-body"></tbody>
                </table>
            </div>
        </div>
    </section>

    {{-- ===== LOSS TAB ===== --}}
    <section id="tab-loss" class="st-pane hidden space-y-3">
        <div class="flex justify-between items-center">
            <p class="text-[10px] md:text-xs text-gray-600 max-w-lg">
                Tofauti ni <b>kugundua</b>. Upungufu unakuwa <b>hasara</b> baada ya kukagua.
            </p>
            <button onclick="window.print()" class="px-2.5 py-1.5 bg-gray-700 hover:bg-gray-800 text-white rounded-lg text-[10px] md:text-xs font-medium transition">
                <i class="fas fa-print mr-1"></i> Chapisha
            </button>
        </div>
        <div id="loss-body" class="space-y-3"></div>
    </section>

    {{-- ===== UNMATCHED TAB ===== --}}
    <section id="tab-unmatched" class="st-pane hidden space-y-3">
        <div class="bg-white p-3 md:p-4 rounded-lg border border-gray-200">
            <p class="text-[10px] md:text-xs text-gray-600 mb-2 flex items-start gap-1">
                <i class="fas fa-circle-info text-amber-600 mt-0.5"></i>
                <span>Bidhaa zilizoonekana dukani lakini hazipo kwenye mfumo. Hazioongezwi kwenye bidhaa zako moja kwa moja.</span>
            </p>
            @if(!$readonly)
            <div class="grid grid-cols-2 md:grid-cols-6 gap-2">
                <input id="u-name"    placeholder="Jina *"     class="col-span-2 px-2.5 py-2 border border-gray-300 rounded-lg text-xs md:text-sm">
                <input id="u-unit"    placeholder="Kipimo"     class="px-2.5 py-2 border border-gray-300 rounded-lg text-xs md:text-sm">
                <input id="u-barcode" placeholder="Barcode"    class="px-2.5 py-2 border border-gray-300 rounded-lg text-xs md:text-sm">
                <input id="u-qty"     type="number" step="0.01" min="0" placeholder="Idadi *" class="px-2.5 py-2 border border-gray-300 rounded-lg text-xs md:text-sm">
                <button id="u-add" class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs md:text-sm font-semibold transition">
                    <i class="fas fa-plus mr-1"></i> Ongeza
                </button>
            </div>
            @endif
        </div>
        <div class="bg-white rounded-lg border border-gray-200 overflow-x-auto">
            <table class="w-full text-[10px] md:text-xs">
                <thead class="bg-gray-50 text-gray-500 uppercase tracking-wide">
                    <tr>
                        <th class="px-2 md:px-3 py-2 text-left">Bidhaa</th>
                        <th class="px-2 md:px-3 py-2 text-left">Kipimo</th>
                        <th class="px-2 md:px-3 py-2 text-left">Barcode</th>
                        <th class="px-2 md:px-3 py-2 text-right">Idadi</th>
                        <th class="px-2 md:px-3 py-2 text-left">Hali</th>
                        <th class="px-2 md:px-3 py-2"></th>
                    </tr>
                </thead>
                <tbody id="u-body"></tbody>
            </table>
        </div>
    </section>

    {{-- ===== Generic confirmation modal (replaces native confirm) ===== --}}
    <div id="cm-modal" class="hidden fixed inset-0 z-50 flex items-end sm:items-center justify-center">
        <div id="cm-bg" class="absolute inset-0 bg-black/50"></div>
        <div class="relative w-full sm:max-w-md bg-white rounded-t-3xl sm:rounded-2xl shadow-2xl max-h-[92vh] flex flex-col">
            <div class="px-5 pt-5 pb-3">
                <div class="w-10 h-1 bg-gray-300 rounded-full mx-auto mb-4 sm:hidden"></div>
                <div class="flex items-center gap-3">
                    <div id="cm-icon" class="w-11 h-11 shrink-0 rounded-xl flex items-center justify-center bg-emerald-100 text-emerald-600">
                        <i class="fas fa-circle-question"></i>
                    </div>
                    <div class="min-w-0">
                        <h3 id="cm-title" class="text-base font-semibold text-gray-900">Thibitisha</h3>
                        <p id="cm-ref" class="text-[11px] text-gray-500 truncate"></p>
                    </div>
                </div>
            </div>

            <div class="px-5 pb-4 overflow-y-auto space-y-3 flex-1">
                <div id="cm-body" class="space-y-2.5"></div>
            </div>

            <div class="px-5 py-3 border-t border-gray-100 flex gap-2 pb-[max(0.75rem,env(safe-area-inset-bottom))]">
                <button id="cm-cancel" class="flex-1 py-3 border border-gray-300 hover:bg-gray-50 rounded-xl text-sm font-semibold text-gray-700">Ghairi</button>
                <button id="cm-go" class="flex-1 py-3 rounded-xl text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 disabled:opacity-40 disabled:cursor-not-allowed">
                    Thibitisha
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const app      = document.getElementById('st-app');
    const base     = app.dataset.base;
    const readonly = app.dataset.readonly === '1';
    const csrf     = document.querySelector('meta[name="csrf-token"]').content;
    const $        = (s) => document.querySelector(s);
    const esc      = (s) => String(s ?? '').replace(/[&<>"']/g,
        (c) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    const n     = (v, d = 2) => (v == null) ? '-' : Number(v).toLocaleString(undefined, {maximumFractionDigits: d});
    const money = (v) => Number(v || 0).toLocaleString(undefined, {maximumFractionDigits: 0});

    function toast(msg, type) {
        const d = document.createElement('div');
        d.className = 'fixed top-4 left-1/2 -translate-x-1/2 z-[60] max-w-[90vw] px-3.5 py-2.5 rounded-lg border text-xs md:text-sm shadow-lg ' +
            (type === 'error' ? 'bg-red-50 border-red-200 text-red-800' : 'bg-emerald-50 border-emerald-200 text-emerald-800');
        d.innerHTML = '<i class="fas ' + (type === 'error' ? 'fa-circle-exclamation' : 'fa-circle-check') + ' mr-1.5"></i>' + esc(msg);
        document.body.appendChild(d);
        setTimeout(() => d.remove(), 3500);
    }

    /* ----- Custom confirm modal ----- */
    const cModal = document.getElementById('cm-modal');
    let cmResolver = null;

    function confirmDialog(opts) {
        return new Promise((resolve) => {
            cmResolver = resolve;
            document.getElementById('cm-title').textContent = opts.title || 'Thibitisha';
            document.getElementById('cm-ref').textContent   = opts.ref || '';
            const icon = document.getElementById('cm-icon');
            const go = document.getElementById('cm-go');

            const tone = opts.tone || 'emerald';
            const toneMap = {
                emerald: 'bg-emerald-100 text-emerald-600',
                red:     'bg-red-100 text-red-600',
                amber:   'bg-amber-100 text-amber-600'
            };
            const btnMap = {
                emerald: 'bg-emerald-600 hover:bg-emerald-700',
                red:     'bg-red-600 hover:bg-red-700',
                amber:   'bg-amber-500 hover:bg-amber-600'
            };
            icon.className = 'w-11 h-11 shrink-0 rounded-xl flex items-center justify-center ' + (toneMap[tone] || toneMap.emerald);
            icon.innerHTML = '<i class="fas ' + (opts.icon || 'fa-circle-question') + '"></i>';
            go.className = 'flex-1 py-3 rounded-xl text-sm font-semibold text-white disabled:opacity-40 disabled:cursor-not-allowed ' + (btnMap[tone] || btnMap.emerald);
            go.textContent = opts.okText || 'Thibitisha';

            document.getElementById('cm-body').innerHTML = opts.body || '';
            cModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        });
    }
    function closeCm(v) {
        cModal.classList.add('hidden');
        document.body.style.overflow = '';
        const r = cmResolver; cmResolver = null;
        if (r) r(v);
    }
    document.getElementById('cm-cancel').addEventListener('click', () => closeCm(false));
    document.getElementById('cm-bg').addEventListener('click', () => closeCm(false));
    document.getElementById('cm-go').addEventListener('click', () => closeCm(true));
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !cModal.classList.contains('hidden')) closeCm(false);
    });

    /* Helper: build a bullet list block */
    const bulletBox = (cls, header, items) =>
        '<div class="rounded-xl border p-3.5 ' + cls + '">' +
            '<p class="text-xs font-semibold">' + header + '</p>' +
            '<ul class="mt-1.5 pl-4 list-disc space-y-0.5 text-[11px] opacity-90">' +
                items.map(t => '<li>' + t + '</li>').join('') +
            '</ul>' +
        '</div>';

    const statBox = (cls, label, value, hint) =>
        '<div class="rounded-lg border px-2.5 py-2 ' + cls + '">' +
            '<p class="text-[10px] leading-none">' + label + '</p>' +
            '<p class="text-sm font-bold tabular-nums mt-1">' + value + '</p>' +
            (hint ? '<p class="text-[9px] mt-0.5 opacity-70">' + hint + '</p>' : '') +
        '</div>';

    async function api(url, opt = {}) {
        const r = await fetch(url, {
            method: opt.method || 'GET',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: opt.body ? JSON.stringify(opt.body) : undefined
        });
        const j = await r.json().catch(() => ({success: false, message: 'Hitilafu ya seva'}));
        j._status = r.status;
        return j;
    }

    /* ---------- Status maps ---------- */
    const ST = {
        MATCHED:     ['Sawa',              'bg-emerald-100 text-emerald-800'],
        SHORTAGE:    ['Upungufu',          'bg-red-100 text-red-800'],
        EXCESS:      ['Ziada',             'bg-blue-100 text-blue-800'],
        NOT_FOUND:   ['Haipo',             'bg-purple-100 text-purple-800'],
        NOT_COUNTED: ['Bado kuhesabu',     'bg-gray-100 text-gray-700']
    };
    const RES = {
        confirmed:         'Hesabu imethibitishwa',
        investigating:     'Inachunguzwa',
        sale_investigation:'Kuchunguza mauzo',
        recount:           'Rudisha kuhesabu',
        unresolved:        'Imeachwa',
        adjusted:          'Stock imerekebishwa',
        receipt_corrected: 'Risiti imesahihishwa'
    };
    const badge = (s) => `<span class="px-1.5 py-0.5 rounded text-[10px] font-medium whitespace-nowrap ${ST[s][1]}">${ST[s][0]}</span>`;
    const dcls  = (v) => v == null ? '' : (v < -0.005 ? 'text-red-600' : (v > 0.005 ? 'text-blue-600' : 'text-emerald-600'));
    const sgn   = (v) => v == null ? '-' : ((v > 0 ? '+' : '') + n(v));

    /* ---------- Tabs ---------- */
    function showTab(t) {
        document.querySelectorAll('.st-pane').forEach(p => p.classList.add('hidden'));
        $('#tab-' + t).classList.remove('hidden');
        document.querySelectorAll('.st-tab').forEach(b => {
            const on = b.dataset.tab === t;
            b.classList.toggle('bg-emerald-50', on);
            b.classList.toggle('text-emerald-700', on);
            b.classList.toggle('text-gray-600', !on);
        });
        if (t !== 'count') loadReport();
    }
    document.querySelectorAll('.st-tab').forEach(b => b.addEventListener('click', () => showTab(b.dataset.tab)));

    /* ---------- Counts / progress ---------- */
    function updateCounts(c) {
        if (!c) return;
        const b = c.by_status || {};
        const set = (k, v) => { const e = $('#c-' + k); if (e) e.textContent = v; };
        set('total', c.total);
        set('counted', c.counted);
        set('not_counted', c.remaining);
        set('matched', b.MATCHED || 0);
        set('shortage', b.SHORTAGE || 0);
        set('excess', b.EXCESS || 0);
        set('not_found', b.NOT_FOUND || 0);
        $('#prog-text').textContent = `${c.counted} / ${c.total} zimehesabiwa`;
        $('#prog-pct').textContent  = `${c.percent}%`;
        $('#prog-bar').style.width  = `${c.percent}%`;
    }

    /* ---------- Count tab ---------- */
    let page = 1, last = 1, timer = null;

    function rowHtml(i) {
        const locked = readonly || i.adjusted;
        const val = i.is_unavailable ? '' : (i.physical == null ? '' : i.physical);
        return `<div class="item bg-white border border-gray-200 rounded-lg p-2.5 md:p-3" data-id="${i.id}">
            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
                <div class="flex-1 min-w-0">
                    <div class="font-medium text-gray-900 text-xs md:text-sm truncate">${esc(i.name)}</div>
                    <div class="text-[10px] md:text-xs text-gray-500">${esc([i.unit, i.category, i.barcode ? '#' + i.barcode : ''].filter(Boolean).join(' | '))}</div>
                </div>
                <div class="grid grid-cols-3 gap-2 md:gap-3 items-center text-center sm:w-80">
                    <div><div class="text-[9px] md:text-[10px] text-gray-500">Mfumo</div><div class="text-xs md:text-sm font-semibold">${n(i.system)}</div></div>
                    <div>
                        <div class="text-[9px] md:text-[10px] text-gray-500">Halisi</div>
                        <input type="number" inputmode="decimal" step="0.01" min="0" value="${val}" ${locked ? 'disabled' : ''}
                            class="phys w-full px-1.5 py-1 border border-gray-300 rounded text-xs md:text-sm text-center disabled:bg-gray-100 focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div><div class="text-[9px] md:text-[10px] text-gray-500">Tofauti</div><div class="text-xs md:text-sm font-semibold ${dcls(i.diff_system)}">${sgn(i.diff_system)}</div></div>
                </div>
                <div class="flex sm:flex-col items-center sm:items-end gap-2 justify-between">
                    ${badge(i.status)}
                    ${locked ? '' : `<div class="flex gap-2 text-[10px] md:text-xs">
                        <button class="nf text-purple-700 underline">Haipo</button>
                        <button class="clr text-gray-500 underline">Ondoa</button>
                    </div>`}
                </div>
            </div>
        </div>`;
    }

    async function loadList(reset) {
        if (reset) { page = 1; $('#list').innerHTML = ''; }
        const qs = `page=${page}&search=${encodeURIComponent($('#q').value)}&filter=${encodeURIComponent($('#flt').value)}`;
        const j  = await api(`${base}/items?${qs}`);
        if (!j.success) return toast(j.message || 'Imeshindikana kupakia', 'error');
        last = j.meta.last;
        if (reset && !j.data.length) {
            $('#list').innerHTML = '<div class="bg-white border border-gray-200 rounded-lg p-8 text-center text-gray-500 text-xs md:text-sm"><i class="fas fa-inbox text-2xl text-gray-300 block mb-2"></i>Hakuna bidhaa zinazolingana.</div>';
        }
        $('#list').insertAdjacentHTML('beforeend', j.data.map(rowHtml).join(''));
        $('#more').classList.toggle('hidden', page >= last);
        updateCounts(j.counts);
    }

    async function saveCount(el, body) {
        const j = await api(`${base}/items/${el.dataset.id}/count`, {method: 'POST', body});
        if (!j.success) return toast(j.message || 'Imeshindikana kuhifadhi', 'error');
        const t = document.createElement('div'); t.innerHTML = rowHtml(j.item);
        el.replaceWith(t.firstChild);
        updateCounts(j.counts);
    }

    $('#list').addEventListener('change', (e) => {
        if (!e.target.classList.contains('phys')) return;
        saveCount(e.target.closest('.item'), {physical_qty: e.target.value === '' ? null : e.target.value});
    });
    $('#list').addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && e.target.classList.contains('phys')) {
            e.preventDefault(); e.target.blur();
            const next = e.target.closest('.item').nextElementSibling;
            if (next) setTimeout(() => next.querySelector('.phys')?.focus(), 250);
        }
    });
    $('#list').addEventListener('click', (e) => {
        const el = e.target.closest('.item'); if (!el) return;
        if (e.target.classList.contains('nf')) saveCount(el, {unavailable: true});
        if (e.target.classList.contains('clr')) saveCount(el, {});
    });
    $('#more').addEventListener('click', () => { page++; loadList(false); });
    $('#q').addEventListener('input', () => { clearTimeout(timer); timer = setTimeout(() => loadList(true), 300); });
    $('#flt').addEventListener('change', () => loadList(true));

    /* ---------- Reports ---------- */
    let REPORT = null, GROUP = 'all';
    const GROUPS = {
        all: 'Zote', shortage: 'Upungufu', excess: 'Ziada',
        not_found: 'Haipo', not_counted: 'Bado', system_drift: 'Mfumo umepotoka'
    };

    async function loadReport() {
        const j = await api(`${base}/report`);
        if (!j.success) return toast(j.message || 'Imeshindikana kupakia ripoti', 'error');
        REPORT = j;
        renderRecon(); renderLoss(); renderUnmatched();
    }

    function renderRecon() {
        const rows = REPORT.rows;
        const cnt = (g) => g === 'all' ? rows.length : rows.filter(r => r.group === g).length;
        $('#recon-chips').innerHTML = Object.keys(GROUPS).map(g =>
            `<button data-g="${g}" class="chip px-2.5 py-1 rounded-full border ${g === GROUP ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-gray-700 border-gray-300 hover:border-emerald-400'} transition">${GROUPS[g]} (${cnt(g)})</button>`
        ).join('');

        const shown = rows.filter(r => GROUP === 'all' || r.group === GROUP);
        $('#recon-body').innerHTML = shown.length ? shown.map(r => {
            const canSel = !readonly && !r.adjusted;
            return `<tr class="border-t border-gray-100 align-top hover:bg-gray-50/60" data-id="${r.id}">
                <td class="px-2 py-2">${canSel ? '<input type="checkbox" class="sel accent-emerald-600">' : ''}</td>
                <td class="px-2 py-2">
                    <div class="font-medium text-gray-900">${esc(r.name)}</div>
                    <div class="text-[10px] text-gray-500">${esc(r.unit || '')}</div>
                </td>
                <td class="px-2 py-2 text-right text-gray-600">${n(r.previous_qty)}</td>
                <td class="px-2 py-2 text-right text-gray-600">${n(r.received)}</td>
                <td class="px-2 py-2 text-right text-gray-600">${n(r.sold)}</td>
                <td class="px-2 py-2 text-right text-gray-600">${n(r.expected)}</td>
                <td class="px-2 py-2 text-right text-gray-600">${n(r.system)}</td>
                <td class="px-2 py-2 text-right font-semibold">${r.status === 'NOT_COUNTED' ? '-' : n(r.physical)}</td>
                <td class="px-2 py-2 text-right ${dcls(r.diff_system)}">${sgn(r.diff_system)}</td>
                <td class="px-2 py-2 text-right ${dcls(r.diff_expected)}">${sgn(r.diff_expected)}</td>
                <td class="px-2 py-2 text-right ${dcls(r.value)}">${r.value ? money(r.value) : '-'}</td>
                <td class="px-2 py-2">${badge(r.status)}${r.resolution ? `<div class="text-[10px] text-gray-600 mt-1">${RES[r.resolution]}</div>` : ''}</td>
            </tr>
            <tr class="bg-gray-50/80"><td></td><td colspan="11" class="px-2 pb-2 text-[10px] md:text-xs text-gray-700">
                <div><b class="text-emerald-700">Pendekezo:</b> ${esc(r.recommendation)}</div>
                ${r.cause ? `<div class="text-gray-500 mt-0.5"><b>Sababu:</b> ${esc(r.cause)}</div>` : ''}
                ${r.resolution_note ? `<div class="text-gray-500 mt-0.5"><b>Kumbuka:</b> ${esc(r.resolution_note)}</div>` : ''}
            </td></tr>`;
        }).join('') : '<tr><td colspan="12" class="px-3 py-10 text-center text-gray-500 text-xs"><i class="fas fa-check-circle text-2xl text-emerald-300 block mb-2"></i>Hakuna cha kulinganisha hapa.</td></tr>';
        refreshSel();
    }

    $('#recon-chips').addEventListener('click', (e) => {
        const b = e.target.closest('.chip'); if (!b) return;
        GROUP = b.dataset.g; renderRecon();
    });

    const selectedIds = () => Array.from(document.querySelectorAll('#recon-body .sel:checked')).map(c => Number(c.closest('tr').dataset.id));
    function refreshSel() { const e = $('#sel-n'); if (e) e.textContent = selectedIds().length; }
    document.addEventListener('change', (e) => {
        if (e.target.classList?.contains('sel')) refreshSel();
        if (e.target.id === 'sel-all') {
            document.querySelectorAll('#recon-body .sel').forEach(c => c.checked = e.target.checked);
            refreshSel();
        }
    });

    const applyBtn = $('#bulk-apply');
    if (applyBtn) applyBtn.addEventListener('click', async () => {
        const ids = selectedIds();
        if (!ids.length) return toast('Chagua bidhaa moja au zaidi.', 'error');
        const action = $('#bulk-action').value;
        const note   = $('#bulk-note').value.trim();

        if (['adjusted', 'receipt_corrected'].includes(action)) {
            if (!note) return toast('Weka sababu ya marekebisho.', 'error');

            const ok = await confirmDialog({
                title: 'Badilisha stock?',
                ref: `Vitendo kwa bidhaa ${ids.length}`,
                icon: 'fa-triangle-exclamation',
                tone: 'amber',
                okText: 'Ndio, badilisha',
                body: bulletBox('bg-amber-50 border-amber-200 text-amber-900', 'Tafadhali angalia:', [
                    `Utabadilisha stock ya bidhaa <b>${ids.length}</b> kwa tofauti iliyohesabiwa.`,
                    'Kitendo hiki kitaandikwa kwenye historia.',
                    'Hakiwezi kutenduliwa hapa.',
                ])
            });
            if (!ok) return;
        }

        const j = await api(`${base}/resolve`, {method: 'POST', body: {item_ids: ids, action, note}});
        if (!j.success) return toast(j.message || (j.errors ? Object.values(j.errors)[0][0] : 'Imeshindikana'), 'error');
        toast(j.message);
        if (j.skipped?.length) toast('Zilizorukwa: ' + j.skipped.length, 'error');
        $('#bulk-note').value = '';
        loadReport(); loadList(true);
    });

    /* ---------- Loss tab ---------- */
    function lossTable(title, rows, valueLabel, tone) {
        if (!rows.length) return '';
        const total = rows.reduce((a, r) => a + Math.abs(r.value), 0);
        return `<div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            <div class="px-3 md:px-4 py-2 border-b border-gray-200 flex justify-between text-xs md:text-sm bg-gray-50">
                <b class="text-gray-800">${title}</b>
                ${valueLabel ? `<span class="${tone} font-semibold">${valueLabel}: ${money(total)} TZS</span>` : ''}
            </div>
            <div class="overflow-x-auto"><table class="w-full text-[10px] md:text-xs">
                <thead class="bg-gray-50 text-gray-500 uppercase tracking-wide"><tr>
                    <th class="px-2 md:px-3 py-2 text-left">Bidhaa</th>
                    <th class="px-2 md:px-3 py-2 text-right">Awali</th>
                    <th class="px-2 md:px-3 py-2 text-right">Mfumo</th>
                    <th class="px-2 md:px-3 py-2 text-right">Tarajio</th>
                    <th class="px-2 md:px-3 py-2 text-right">Halisi</th>
                    <th class="px-2 md:px-3 py-2 text-right">Tofauti</th>
                    <th class="px-2 md:px-3 py-2 text-right">Bei nunua</th>
                    <th class="px-2 md:px-3 py-2 text-right">Thamani</th>
                    <th class="px-2 md:px-3 py-2 text-left">Hatua</th>
                </tr></thead><tbody>
                ${rows.map(r => `<tr class="border-t border-gray-100 align-top">
                    <td class="px-2 md:px-3 py-2">${esc(r.name)} <span class="text-[10px] text-gray-400">${esc(r.unit || '')}</span></td>
                    <td class="px-2 md:px-3 py-2 text-right">${n(r.previous_qty)}</td>
                    <td class="px-2 md:px-3 py-2 text-right">${n(r.system)}</td>
                    <td class="px-2 md:px-3 py-2 text-right">${n(r.expected)}</td>
                    <td class="px-2 md:px-3 py-2 text-right">${r.status === 'NOT_COUNTED' ? '-' : n(r.physical)}</td>
                    <td class="px-2 md:px-3 py-2 text-right ${dcls(r.variance)}">${sgn(r.variance)}</td>
                    <td class="px-2 md:px-3 py-2 text-right">${money(r.purchase_price)}</td>
                    <td class="px-2 md:px-3 py-2 text-right ${dcls(r.value)}">${r.value ? money(r.value) : '-'}</td>
                    <td class="px-2 md:px-3 py-2 text-[10px] text-gray-600 max-w-xs">${esc(r.recommendation)}</td>
                </tr>`).join('')}
                </tbody></table></div></div>`;
    }

    function renderLoss() {
        const s = REPORT.summary, rows = REPORT.rows;
        const g = (k) => rows.filter(r => r.group === k);
        const cards = [
            ['Zote',             s.total],
            ['Zimehesabiwa',     s.counted],
            ['Sawa',             s.matched],
            ['Upungufu',         s.shortage],
            ['Ziada',            s.excess],
            ['Haipo',            s.not_found],
            ['Bado',             s.not_counted],
            ['Si kwenye mfumo',  s.unmatched]
        ];
        $('#loss-body').innerHTML =
            `<div class="grid grid-cols-2 md:grid-cols-4 gap-2">${cards.map(c =>
                `<div class="bg-white border border-gray-200 rounded-lg p-2.5 md:p-3">
                    <div class="text-[10px] md:text-xs text-gray-500">${c[0]}</div>
                    <div class="text-lg md:text-xl font-bold text-gray-900">${c[1]}</div>
                </div>`).join('')}</div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 md:gap-3">
                <div class="rounded-lg border border-red-200 bg-red-50 p-3 md:p-4">
                    <div class="text-[10px] md:text-xs text-red-700">Thamani ya upungufu (bei ya nunua)</div>
                    <div class="text-xl md:text-2xl font-bold text-red-800 mt-0.5">${money(s.shortage_value)} <span class="text-sm font-normal">TZS</span></div>
                    <div class="text-[9px] md:text-[10px] text-red-700 mt-0.5">Vipimo ${n(s.shortage_qty)} · si hasara hadi ukague.</div>
                </div>
                <div class="rounded-lg border border-blue-200 bg-blue-50 p-3 md:p-4">
                    <div class="text-[10px] md:text-xs text-blue-700">Thamani ya ziada (bei ya nunua)</div>
                    <div class="text-xl md:text-2xl font-bold text-blue-800 mt-0.5">${money(s.excess_value)} <span class="text-sm font-normal">TZS</span></div>
                    <div class="text-[9px] md:text-[10px] text-blue-700 mt-0.5">Vipimo ${n(s.excess_qty)} · haichanganywi na upungufu.</div>
                </div>
            </div>
            ${s.hidden_variance ? `<div class="rounded-lg border border-amber-200 bg-amber-50 text-amber-900 text-xs md:text-sm p-3 flex items-start gap-2">
                <i class="fas fa-triangle-exclamation mt-0.5"></i>
                <span>Bidhaa ${s.hidden_variance} zinafanana na mfumo lakini chini ya inayotarajiwa. Mfumo unaweza kuficha upungufu.</span>
            </div>` : ''}
            ${lossTable('Upungufu', g('shortage'), 'Thamani', 'text-red-700')}
            ${lossTable('Haipo dukani', g('not_found'), '', '')}
            ${lossTable('Ziada', g('excess'), 'Thamani', 'text-blue-700')}
            ${lossTable('Mfumo umepotoka (rafu inalingana)', g('system_drift'), '', '')}
            ${lossTable('Bado kuhesabu', g('not_counted'), '', '')}`;
    }

    /* ---------- Unmatched ---------- */
    function renderUnmatched() {
        const u = REPORT.unmatched || [];
        $('#u-body').innerHTML = u.length ? u.map(r => `<tr class="border-t border-gray-100 hover:bg-gray-50/60">
            <td class="px-2 md:px-3 py-2 font-medium">${esc(r.name)}</td>
            <td class="px-2 md:px-3 py-2 text-gray-600">${esc(r.unit || '')}</td>
            <td class="px-2 md:px-3 py-2 text-gray-500">${esc(r.barcode || '')}</td>
            <td class="px-2 md:px-3 py-2 text-right">${n(r.quantity)}</td>
            <td class="px-2 md:px-3 py-2"><span class="px-1.5 py-0.5 rounded text-[10px] bg-amber-100 text-amber-800 font-medium">Si kwenye mfumo</span></td>
            <td class="px-2 md:px-3 py-2 text-right">${readonly ? '' : `<button class="u-del text-red-600 hover:underline text-[10px]" data-id="${r.id}">Ondoa</button>`}</td>
        </tr>`).join('')
        : '<tr><td colspan="6" class="px-3 py-8 text-center text-gray-500 text-xs"><i class="fas fa-inbox text-2xl text-gray-300 block mb-2"></i>Hakuna iliyorekodiwa.</td></tr>';
    }

    const uAdd = $('#u-add');
    if (uAdd) uAdd.addEventListener('click', async () => {
        const body = {
            name:     $('#u-name').value.trim(),
            unit:     $('#u-unit').value.trim(),
            barcode:  $('#u-barcode').value.trim(),
            quantity: $('#u-qty').value
        };
        const j = await api(`${base}/unmatched`, {method: 'POST', body});
        if (!j.success) return toast(j.message || (j.errors ? Object.values(j.errors)[0][0] : 'Imeshindikana'), 'error');
        ['#u-name', '#u-unit', '#u-barcode', '#u-qty'].forEach(s => $(s).value = '');
        loadReport();
    });
    $('#u-body').addEventListener('click', async (e) => {
        if (!e.target.classList.contains('u-del')) return;
        await api(`${base}/unmatched/${e.target.dataset.id}`, {method: 'DELETE'});
        loadReport();
    });

    /* ---------- Header buttons ---------- */
    const bs = $('#btn-sync'), bc = $('#btn-close'), bx = $('#btn-cancel');
    if (bs) bs.addEventListener('click', async () => {
        const j = await api(`${base}/sync`, {method: 'POST'});
        toast(j.message || 'Imefanyika');
        updateCounts(j.counts); loadList(true);
    });
    if (bx) bx.addEventListener('click', async () => {
        const ok = await confirmDialog({
            title: 'Ghairi uhesabuji?',
            ref: 'Hesabu zitabaki kwenye historia',
            icon: 'fa-xmark',
            tone: 'amber',
            okText: 'Ndio, ghairi',
            body: bulletBox('bg-amber-50 border-amber-200 text-amber-900', 'Tafadhali angalia:', [
                'Hesabu zitabaki kwenye historia kama zilizoghairiwa.',
                'Hakutakuwa na mabadiliko ya stock.',
            ])
        });
        if (!ok) return;
        const j = await api(`${base}/cancel`, {method: 'POST'});
        if (j.success) location.href = j.redirect; else toast(j.message, 'error');
    });
    if (bc) bc.addEventListener('click', async () => {
        let j = await api(`${base}/close`, {method: 'POST', body: {}});
        if (!j.success && j.needs_force) {
            const ok = await confirmDialog({
                title: 'Kamilisha kwa nguvu?',
                ref: 'Bado kuna bidhaa hazijahesabiwa',
                icon: 'fa-triangle-exclamation',
                tone: 'amber',
                okText: 'Ndio, kamilisha',
                body: bulletBox('bg-amber-50 border-amber-200 text-amber-900', 'Angalia:', [
                    esc(j.message),
                    'Bidhaa ambazo hazikuhesabiwa hazitabadilika.',
                ])
            });
            if (!ok) return;
            j = await api(`${base}/close`, {method: 'POST', body: {force: true}});
        }
        if (j.success) { toast(j.message); setTimeout(() => { location.href = j.redirect; }, 800); }
        else toast(j.message || 'Imeshindikana kufunga', 'error');
    });

    /* ---------- Apply counted quantities ---------- */
    const ba = $('#btn-apply-stock');
    if (ba) ba.addEventListener('click', async () => {
        const sys = Number(ba.dataset.sys || 0), cnt = Number(ba.dataset.cnt || 0), diff = cnt - sys;
        const unc = Number(ba.dataset.uncounted || 0);
        const counted = Number(ba.dataset.counted || 0);
        const fmt = (v) => Math.abs(v).toLocaleString(undefined, {maximumFractionDigits: 0});
        const sign = (v) => (v < 0 ? '-' : '+') + fmt(v);

        const diffTone = diff < 0 ? 'red' : (diff > 0 ? 'emerald' : 'gray');
        const diffCls = diff < 0 ? 'border-red-200 bg-red-50 text-red-700'
                       : diff > 0 ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
                       : 'border-gray-200 bg-gray-50 text-gray-700';

        const body =
            '<div class="grid grid-cols-3 gap-2 text-center">' +
                statBox('border-gray-200 bg-gray-50 text-gray-700', 'Ya sasa', fmt(sys) + ' TZS', '') +
                statBox('border-emerald-200 bg-emerald-50 text-emerald-700', 'Mpya', fmt(cnt) + ' TZS', '') +
                statBox(diffCls, 'Tofauti', sign(diff) + ' TZS', '') +
            '</div>' +
            '<div class="rounded-lg border border-gray-200 bg-white p-3 space-y-1.5 text-xs">' +
                '<div class="flex items-center justify-between"><span class="text-gray-600"><i class="fas fa-check text-emerald-600 mr-1"></i>Bidhaa zilizohesabiwa</span><b>' + counted + '</b></div>' +
                (unc > 0
                    ? '<div class="flex items-center justify-between"><span class="text-gray-600"><i class="fas fa-question text-amber-600 mr-1"></i>Hazikuhesabiwa</span><b>' + unc + '</b></div>'
                    : '') +
            '</div>' +
            bulletBox('bg-amber-50 border-amber-200 text-amber-900', 'Angalia:', [
                'Bidhaa zilizohesabiwa sifuri zitakuwa sifuri.',
                'Mauzo/manunuzi baada ya kuhesabu yatapotea.',
                'Idadi ya awali inabaki kwenye historia.',
                '<b>Haiwezi kutenduliwa.</b>',
            ]);

        const ok = await confirmDialog({
            title: 'Sasisha stock?',
            ref: ba.dataset.ref,
            icon: 'fa-boxes-stacked',
            tone: 'emerald',
            okText: 'Ndio, sasisha',
            body,
        });
        if (!ok) return;

        ba.disabled = true;
        const j = await api(`${base}/apply-stock`, {method: 'POST', body: {}});
        if (j.success) { toast(j.message); setTimeout(() => { location.href = j.redirect || location.href; }, 800); }
        else { toast(j.message || 'Imeshindikana kusasisha stock', 'error'); ba.disabled = false; }
    });

    showTab('count');
    loadList(true);
})();
</script>
@endpush