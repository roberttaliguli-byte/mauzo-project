{{-- resources/views/stock-take/cleanup.blade.php --}}
@extends('layouts.app')

@section('title', 'Usafishaji wa Bidhaa')
@section('page-title', 'Usafishaji wa Bidhaa')
@section('page-subtitle', 'Kwa Boss tu')

@section('content')
<div id="pc-app" class="space-y-3 pb-6"
     data-unlocked="{{ $unlocked ? '1' : '0' }}"
     data-url-unlock="{{ route('cleanup.unlock') }}"
     data-url-lock="{{ route('cleanup.lock') }}"
     data-url-data="{{ route('cleanup.data') }}"
     data-url-preview="{{ route('cleanup.preview') }}"
     data-url-execute="{{ route('cleanup.execute') }}"
     data-url-restore="{{ route('cleanup.restore') }}">

    @include('stock-take._nav', ['active' => 'cleanup'])

    @if(!$unlocked)
    {{-- ================= LOCKED ================= --}}
    <div class="max-w-sm mx-auto mt-6 bg-white rounded-2xl border border-gray-200 shadow-sm p-6 text-center">
        <div class="w-14 h-14 mx-auto rounded-full bg-emerald-50 flex items-center justify-center mb-3">
            <i class="fas fa-shield-alt text-emerald-600 text-xl"></i>
        </div>
        <h2 class="text-base font-semibold text-gray-900">Eneo la Boss</h2>
        <p class="text-sm text-gray-500 mt-1">Weka neno la siri kufungua. Ukurasa utabaki wazi dakika 15.</p>
        <div class="mt-4 text-left">
            <label for="pw" class="block text-xs font-medium text-gray-700 mb-1">Neno la siri</label>
            <div class="relative">
                <input id="pw" type="password" autocomplete="current-password" autofocus
                       class="w-full pl-3 pr-11 py-3 border border-gray-300 rounded-xl text-base focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                <button type="button" data-eye="pw" class="absolute right-0 top-0 h-full px-3 text-gray-400 hover:text-gray-600" aria-label="Onyesha"><i class="fas fa-eye"></i></button>
            </div>
            <p id="pw-err" class="text-xs text-red-600 mt-2 hidden"></p>
        </div>
        <button id="pw-go" class="mt-4 w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-semibold">Fungua</button>
    </div>

    @else
    {{-- ================= UNLOCKED ================= --}}

    {{-- Page intro --}}
    <div class="flex items-center justify-between gap-3 px-1">
        <p class="text-sm text-gray-600">Fuata hatua 3 kusafisha bidhaa ambazo hazihitajiki.</p>
        <button id="lock" class="shrink-0 px-3 py-1.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-600 rounded-lg text-xs font-medium">
            <i class="fas fa-lock mr-1"></i>Funga
        </button>
    </div>

    {{-- STEP 1 — Dropdown / Collapsible --}}
    <section id="step1" class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <button type="button" id="step1-toggle"
                class="w-full flex items-center gap-3 p-4 md:p-5 text-left hover:bg-gray-50/60 transition">
            <span class="w-8 h-8 rounded-full bg-emerald-600 text-white text-sm font-bold flex items-center justify-center shrink-0">1</span>
            <div class="flex-1 min-w-0">
                <h2 class="text-sm md:text-base font-semibold text-gray-900">Chagua aina ya bidhaa</h2>
                <p id="step1-summary" class="text-xs text-gray-500 truncate">Stock sifuri</p>
            </div>
            <i id="step1-chevron" class="fas fa-chevron-down text-gray-400 text-sm transition-transform"></i>
        </button>

        <div id="step1-body" class="hidden border-t border-gray-100 px-4 md:px-5 pb-4 md:pb-5 pt-4">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5">
                @foreach([
                    ['zero_stock', 'fa-box-open',        'Stock sifuri',        'Zimeisha dukani'],
                    ['zero_price', 'fa-tag',             'Bei nunua sifuri',    'Bei ya kununua haijawekwa'],
                    ['no_sales',   'fa-chart-line',      'Hazijauzwa kamwe',    'Hazina mauzo yoyote'],
                    ['multiple',   'fa-layer-group',     'Masharti 2 au zaidi', 'Zinakidhi angalau mawili'],
                ] as [$k, $icon, $label, $hint])
                    <button type="button" data-opt="{{ $k }}"
                            class="opt text-left rounded-xl border-2 border-gray-200 bg-white p-3 md:p-4 transition active:scale-[.98] hover:border-emerald-300">
                        <div class="flex items-start justify-between">
                            <span class="opt-ico w-9 h-9 rounded-lg bg-gray-100 text-gray-500 flex items-center justify-center"><i class="fas {{ $icon }} text-sm"></i></span>
                            <span class="opt-chk w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center text-white text-[10px]"><i class="fas fa-check hidden"></i></span>
                        </div>
                        <p class="mt-3 text-2xl font-bold text-gray-900 leading-none" id="n-{{ $k }}">{{ $counts[$k] }}</p>
                        <p class="mt-1 text-sm font-medium text-gray-800">{{ $label }}</p>
                        <p class="text-xs text-gray-500 hidden sm:block">{{ $hint }}</p>
                    </button>
                @endforeach
            </div>
            <div class="mt-3 flex justify-end">
                <button type="button" id="step1-done" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold">
                    <i class="fas fa-check mr-1"></i>Sawa, endelea
                </button>
            </div>
        </div>
    </section>

    {{-- STEP 2 --}}
    <section class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-4 md:p-5 pb-3 flex items-center gap-3">
            <span class="w-8 h-8 rounded-full bg-emerald-600 text-white text-sm font-bold flex items-center justify-center">2</span>
            <div class="flex-1 min-w-0">
                <h2 class="text-sm md:text-base font-semibold text-gray-900" id="s2-title">Chagua bidhaa za kusafisha</h2>
                <p class="text-xs text-gray-500">Weka tiki kwenye bidhaa unazotaka.</p>
            </div>
            <button id="view-toggle" class="shrink-0 text-xs font-medium text-gray-500 hover:text-emerald-700 underline"></button>
        </div>

        <div class="px-4 md:px-5 pb-3 space-y-2.5">
            <div class="relative">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input id="s" type="search" placeholder="Tafuta kwa jina au barcode" autocomplete="off"
                       class="w-full pl-10 pr-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <div class="flex flex-wrap items-center gap-x-4 gap-y-1.5 text-xs">
                <span class="font-medium text-gray-700"><span id="total">0</span> bidhaa</span>
                <span class="flex-1"></span>
                <button id="sel-safe" class="font-semibold text-emerald-700 hover:underline">Chagua zilizo salama kufuta</button>
                <button id="sel-page" class="font-semibold text-emerald-700 hover:underline">Zote kwenye ukurasa</button>
                <button id="sel-all"  class="font-semibold text-red-700 hover:underline">Chagua zote (nje ya ukurasa)</button>
                <button id="sel-none" class="text-gray-500 hover:underline">Ondoa</button>
            </div>
        </div>

        {{-- Banner for "select all matching" mode --}}
        <div id="all-banner" class="hidden mx-4 md:mx-5 mb-2 rounded-xl bg-red-50 border border-red-200 px-3 py-2 text-xs text-red-800 flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i>
            <span><b id="all-n">0</b> bidhaa zinazolingana kwenye filter yote (sio ukurasa huu tu).</span>
            <button id="all-clear" class="ml-auto font-semibold underline">Ondoa</button>
        </div>

        {{-- Column heads (desktop) --}}
        <div class="hidden md:flex items-center gap-3 px-5 py-2 bg-gray-50 border-y border-gray-100 text-xs font-medium text-gray-500">
            <span class="w-5"></span>
            <span class="flex-1">Bidhaa</span>
            <span class="w-20 text-right">Stock</span>
            <span class="w-24 text-right">Bei nunua</span>
            <span class="w-52">Hali</span>
        </div>

        <div id="list" class="divide-y divide-gray-100 border-t border-gray-100 md:border-t-0"></div>

        <div class="flex items-center justify-between gap-2 px-4 md:px-5 py-3 border-t border-gray-100 bg-gray-50/70 text-sm">
            <button id="prev" class="px-3.5 py-2 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 disabled:opacity-40"><i class="fas fa-chevron-left text-xs mr-1"></i>Nyuma</button>
            <span id="pg" class="text-xs text-gray-500"></span>
            <button id="next" class="px-3.5 py-2 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 disabled:opacity-40">Mbele<i class="fas fa-chevron-right text-xs ml-1"></i></button>
        </div>
    </section>

    {{-- STEP 3 --}}
    <section class="sticky bottom-2 z-30 bg-white rounded-2xl border border-gray-200 shadow-lg p-3 md:p-5">
        <div class="flex items-center gap-3 mb-3">
            <span class="w-8 h-8 rounded-full bg-emerald-600 text-white text-sm font-bold flex items-center justify-center">3</span>
            <div class="flex-1">
                <h2 class="text-sm md:text-base font-semibold text-gray-900">Chagua cha kufanya</h2>
                <p class="text-xs text-gray-500"><b id="sel-n" class="text-emerald-700">0</b> zimechaguliwa</p>
            </div>
        </div>
        <div id="acts-active" class="grid grid-cols-2 gap-2.5">
            <button id="btn-archive" disabled class="act rounded-xl bg-amber-500 hover:bg-amber-600 text-white p-3 text-left disabled:opacity-40 disabled:cursor-not-allowed">
                <p class="text-sm font-semibold"><i class="fas fa-archive mr-1.5"></i>Weka kando</p>
                <p class="text-[11px] opacity-90 mt-0.5 hidden sm:block">Inaficha bidhaa. Historia inabaki.</p>
            </button>
            <button id="btn-delete" disabled class="act rounded-xl bg-red-500 hover:bg-red-600 text-white p-3 text-left disabled:opacity-40 disabled:cursor-not-allowed">
                <p class="text-sm font-semibold"><i class="fas fa-trash mr-1.5"></i>Futa kabisa</p>
                <p class="text-[11px] opacity-90 mt-0.5 hidden sm:block">Kwa zisizo na historia tu.</p>
            </button>
        </div>
        <div id="acts-archived" class="hidden">
            <button id="btn-restore" disabled class="w-full rounded-xl bg-blue-600 hover:bg-blue-700 text-white py-3 text-sm font-semibold disabled:opacity-40 disabled:cursor-not-allowed">
                <i class="fas fa-undo mr-1.5"></i>Rejesha bidhaa zilizochaguliwa
            </button>
        </div>
    </section>

    {{-- Confirmation sheet --}}
    <div id="modal" class="hidden fixed inset-0 z-50 flex items-end sm:items-center justify-center">
        <div id="modal-bg" class="absolute inset-0 bg-black/50"></div>
        <div class="relative w-full sm:max-w-md bg-white rounded-t-3xl sm:rounded-2xl shadow-2xl max-h-[92vh] flex flex-col">
            <div class="px-5 pt-5 pb-3">
                <div class="w-10 h-1 bg-gray-300 rounded-full mx-auto mb-4 sm:hidden"></div>
                <div class="flex items-center gap-3">
                    <div id="m-icon" class="w-11 h-11 shrink-0 rounded-xl flex items-center justify-center"></div>
                    <h3 id="m-title" class="text-base font-semibold text-gray-900"></h3>
                </div>
            </div>
            <div class="px-5 pb-4 overflow-y-auto space-y-3 flex-1">
                <div id="m-body" class="space-y-2.5"></div>
                <input id="m-reason" maxlength="200" placeholder="Sababu (si lazima)"
                       class="hidden w-full px-3 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <div>
                    <label for="m-pw" class="block text-xs font-medium text-gray-700 mb-1">Weka neno la siri la Boss kuthibitisha</label>
                    <div class="relative">
                        <input id="m-pw" type="password" autocomplete="current-password"
                               class="w-full pl-3 pr-11 py-3 border border-gray-300 rounded-xl text-base focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <button type="button" data-eye="m-pw" class="absolute right-0 top-0 h-full px-3 text-gray-400 hover:text-gray-600" aria-label="Onyesha"><i class="fas fa-eye"></i></button>
                    </div>
                    <p id="m-err" class="text-xs text-red-600 mt-1.5 hidden"></p>
                </div>
            </div>
            <div class="px-5 py-3 border-t border-gray-100 flex gap-2 pb-[max(0.75rem,env(safe-area-inset-bottom))]">
                <button id="m-cancel" class="flex-1 py-3 border border-gray-300 hover:bg-gray-50 rounded-xl text-sm font-semibold text-gray-700">Ghairi</button>
                <button id="m-go" class="flex-1 py-3 rounded-xl text-sm font-semibold text-white disabled:opacity-40 disabled:cursor-not-allowed"></button>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
@verbatim
<script>
(function () {
    const app  = document.getElementById('pc-app');
    const D    = app.dataset;
    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    const $    = (s) => document.querySelector(s);
    const $$   = (s) => Array.from(document.querySelectorAll(s));
    const esc  = (s) => String(s == null ? '' : s).replace(/[&<>"']/g, (c) => ({'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'}[c]));
    const num  = (v) => Number(v || 0).toLocaleString(undefined, {maximumFractionDigits: 2});

    function toast(msg, type) {
        const d = document.createElement('div');
        d.className = 'fixed top-5 left-1/2 -translate-x-1/2 z-[60] max-w-[90vw] px-4 py-3 rounded-xl shadow-lg text-sm font-medium text-white ' + (type === 'error' ? 'bg-red-600' : 'bg-gray-900');
        d.textContent = msg;
        document.body.appendChild(d);
        setTimeout(() => d.remove(), 4000);
    }
    async function call(url, method, body) {
        const r = await fetch(url, {
            method: method,
            headers: {'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'Content-Type': 'application/json'},
            body: body ? JSON.stringify(body) : undefined
        });
        const j = await r.json().catch(() => ({success: false, message: 'Hitilafu ya seva'}));
        if (j.locked) location.reload();
        return j;
    }
    document.addEventListener('click', (e) => {
        const b = e.target.closest('[data-eye]'); if (!b) return;
        const i = document.getElementById(b.dataset.eye), show = i.type === 'password';
        i.type = show ? 'text' : 'password';
        b.innerHTML = '<i class="fas ' + (show ? 'fa-eye-slash' : 'fa-eye') + '"></i>';
    });

    /* ===== LOCKED ===== */
    if (D.unlocked !== '1') {
        const go = $('#pw-go');
        const unlock = async () => {
            go.disabled = true; go.textContent = 'Inathibitisha...';
            const j = await call(D.urlUnlock, 'POST', {password: $('#pw').value});
            if (j.success) { location.reload(); return; }
            go.disabled = false; go.textContent = 'Fungua';
            $('#pw-err').textContent = j.message || 'Imeshindikana';
            $('#pw-err').classList.remove('hidden');
        };
        go.addEventListener('click', unlock);
        $('#pw').addEventListener('keydown', (e) => { if (e.key === 'Enter') unlock(); });
        return;
    }

    /* ===== UNLOCKED ===== */
    const sel = new Set();
    let picked = ['zero_stock'], view = 'active', page = 1, last = 1, rows = [], timer = null, archivedCount = 0;
    let selectAll = false; // when true → server deletes every matching record, not only visible ids

    const WHY = [['sales', 'mauzo'], ['purchases', 'manunuzi'], ['debts', 'madeni'], ['stock-taking', 'ripoti ya stock'], ['adjustments', 'marekebisho'], ['orders', 'oda'], ['movement', 'mabadiliko ya stock']];
    function why(r) {
        const out = [];
        (r.reasons || []).forEach((x) => WHY.forEach((w) => { if (x.toLowerCase().indexOf(w[0]) !== -1 && out.indexOf(w[1]) === -1) out.push(w[1]); }));
        return out.length ? 'Ina ' + out.join(', ') : 'Ina historia';
    }

    /* Build the current filter as a plain object (used both for data() and for select_all). */
    function filterPayload() {
        const multi = picked.indexOf('multiple') !== -1;
        return {
            criteria: multi ? ['zero_stock', 'zero_price', 'no_sales'] : picked,
            match:    multi ? 'multi' : 'any',
            search:   $('#s').value || '',
            archived: view === 'archived',
        };
    }
    function qs(extra) {
        const p = new URLSearchParams();
        const f = filterPayload();
        f.criteria.forEach((c) => p.append('criteria[]', c));
        p.set('match', f.match);
        p.set('search', f.search);
        p.set('archived', f.archived ? 1 : 0);
        Object.keys(extra || {}).forEach((k) => p.set(k, extra[k]));
        return p.toString();
    }

    /* ----- step 1 (dropdown) ----- */
    const LABELS = {zero_stock: 'Stock sifuri', zero_price: 'Bei nunua sifuri', no_sales: 'Hazijauzwa kamwe', multiple: 'Masharti 2 au zaidi'};
    function step1Summary() {
        const el = $('#step1-summary');
        el.textContent = picked.length ? picked.map((k) => LABELS[k]).join(' + ') : 'Hakuna aina iliyochaguliwa';
    }
    $('#step1-toggle').addEventListener('click', () => {
        const body = $('#step1-body'), ch = $('#step1-chevron');
        const open = body.classList.contains('hidden');
        body.classList.toggle('hidden', !open);
        ch.classList.toggle('rotate-180', open);
    });
    $('#step1-done').addEventListener('click', () => {
        $('#step1-body').classList.add('hidden');
        $('#step1-chevron').classList.remove('rotate-180');
    });

    function paintOpts() {
        $$('.opt').forEach((b) => {
            const on = picked.indexOf(b.dataset.opt) !== -1;
            b.className = 'opt text-left rounded-xl border-2 p-3 md:p-4 transition active:scale-[.98] ' + (on ? 'border-emerald-600 bg-emerald-50' : 'border-gray-200 bg-white hover:border-emerald-300');
            b.querySelector('.opt-ico').className = 'opt-ico w-9 h-9 rounded-lg flex items-center justify-center ' + (on ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-500');
            b.querySelector('.opt-chk').className = 'opt-chk w-5 h-5 rounded-full border-2 flex items-center justify-center text-white text-[10px] ' + (on ? 'border-emerald-600 bg-emerald-600' : 'border-gray-300');
            b.querySelector('.opt-chk i').classList.toggle('hidden', !on);
        });
        step1Summary();
    }
    $$('.opt').forEach((b) => b.addEventListener('click', () => {
        const k = b.dataset.opt;
        if (k === 'multiple') picked = picked.indexOf('multiple') !== -1 ? [] : ['multiple'];
        else {
            picked = picked.filter((x) => x !== 'multiple');
            picked = picked.indexOf(k) !== -1 ? picked.filter((x) => x !== k) : picked.concat(k);
        }
        paintOpts(); reload();
    }));

    function paintView() {
        const arch = view === 'archived';
        $('#step1').classList.toggle('hidden', arch);
        $('#s2-title').textContent = arch ? 'Bidhaa zilizowekwa kando' : 'Chagua bidhaa za kusafisha';
        $('#view-toggle').textContent = arch ? 'Rudi kwenye bidhaa hai' : 'Zilizowekwa kando (' + archivedCount + ')';
        $('#acts-active').classList.toggle('hidden', arch);
        $('#acts-archived').classList.toggle('hidden', !arch);
    }
    $('#view-toggle').addEventListener('click', () => { view = view === 'active' ? 'archived' : 'active'; paintView(); reload(); });

    /* Any filter change → drop selectAll automatically. */
    function reload() { selectAll = false; sel.clear(); page = 1; load(); refreshSel(); }
    $('#s').addEventListener('input', () => { clearTimeout(timer); timer = setTimeout(reload, 300); });

    /* ----- step 2 rows ----- */
    function pill(r) {
        if (r.archived_at) return '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-gray-100 text-gray-600 text-xs font-medium"><i class="fas fa-archive text-[10px]"></i>Imewekwa kando</span>';
        if (r.protected)   return '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-medium"><i class="fas fa-link text-[10px]"></i>Weka kando tu</span><p class="text-[11px] text-gray-500 mt-1">' + esc(why(r)) + '</p>';
        return '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-medium"><i class="fas fa-check text-[10px]"></i>Salama kufuta</span>';
    }
    function rowHtml(r) {
        const sub = [r.unit, r.category].filter(Boolean).join(' / ');
        return '<label class="flex items-start md:items-center gap-3 px-4 md:px-5 py-3.5 cursor-pointer hover:bg-emerald-50/40 active:bg-emerald-50">' +
            '<input type="checkbox" class="pick mt-0.5 md:mt-0 h-5 w-5 shrink-0 accent-emerald-600" data-id="' + r.id + '"' + (sel.has(r.id) ? ' checked' : '') + '>' +
            '<div class="flex-1 min-w-0">' +
              '<p class="text-sm font-semibold text-gray-900 break-words">' + esc(r.name) + '</p>' +
              (sub ? '<p class="text-xs text-gray-500 break-words">' + esc(sub) + '</p>' : '') +
              '<p class="md:hidden text-xs text-gray-600 mt-1">Stock <b>' + num(r.stock) + '</b> &middot; Bei nunua <b>' + num(r.buy) + '</b></p>' +
              '<div class="md:hidden mt-2">' + pill(r) + '</div>' +
            '</div>' +
            '<span class="hidden md:block w-20 text-right text-sm tabular-nums text-gray-800">' + num(r.stock) + '</span>' +
            '<span class="hidden md:block w-24 text-right text-sm tabular-nums text-gray-800">' + num(r.buy) + '</span>' +
            '<div class="hidden md:block w-52">' + pill(r) + '</div></label>';
    }
    const emptyHtml = '<div class="px-4 py-14 text-center"><div class="w-12 h-12 mx-auto rounded-full bg-emerald-50 flex items-center justify-center mb-3"><i class="fas fa-check text-emerald-600"></i></div>' +
        '<p class="text-sm font-semibold text-gray-800">Hakuna bidhaa hapa</p><p class="text-xs text-gray-500 mt-1">Hakuna bidhaa zinazolingana na uchaguzi wako.</p></div>';

    function refreshSel() {
        const n = selectAll ? Number($('#total').textContent) : sel.size;
        $('#sel-n').textContent = n;
        ['#btn-archive', '#btn-delete', '#btn-restore'].forEach((s) => { $(s).disabled = n === 0; });
        const b = $('#all-banner');
        if (b) {
            b.classList.toggle('hidden', !selectAll);
            if (selectAll) $('#all-n').textContent = $('#total').textContent;
        }
    }

    async function load() {
        $('#list').innerHTML = '<div class="p-10 text-center text-sm text-gray-400">Inapakia...</div>';
        const j = await call(D.urlData + '?' + qs({page: page}), 'GET');
        if (!j.success) { toast(j.message || 'Imeshindikana kupakua', 'error'); return; }
        rows = j.data; last = j.meta.last;
        $('#total').textContent = j.meta.total;
        $('#pg').textContent = 'Ukurasa ' + j.meta.page + ' / ' + j.meta.last;
        $('#prev').disabled = page <= 1; $('#next').disabled = page >= last;
        $('#list').innerHTML = rows.length ? rows.map(rowHtml).join('') : emptyHtml;
        Object.keys(j.counts).forEach((k) => { const el = $('#n-' + k); if (el) el.textContent = j.counts[k]; });
        archivedCount = j.counts.archived || 0; paintView();
        refreshSel();
    }

    document.addEventListener('change', (e) => {
        if (!e.target.classList.contains('pick')) return;
        if (selectAll) { selectAll = false; }     // picking a single row cancels bulk mode
        const id = Number(e.target.dataset.id);
        e.target.checked ? sel.add(id) : sel.delete(id);
        refreshSel();
    });
    const mark = () => $$('.pick').forEach((c) => { c.checked = sel.has(Number(c.dataset.id)); });
    $('#sel-page').addEventListener('click', () => { selectAll = false; rows.forEach((r) => sel.add(r.id)); mark(); refreshSel(); });
    $('#sel-safe').addEventListener('click', () => {
        selectAll = false;
        rows.filter((r) => !r.protected).forEach((r) => sel.add(r.id)); mark(); refreshSel();
        toast('Zilizo salama kufuta zimechaguliwa');
    });
    $('#sel-none').addEventListener('click', () => { selectAll = false; sel.clear(); mark(); refreshSel(); });
    $('#prev').addEventListener('click', () => { if (page > 1) { page--; load(); } });
    $('#next').addEventListener('click', () => { if (page < last) { page++; load(); } });
    $('#lock').addEventListener('click', async () => { await call(D.urlLock, 'POST'); location.reload(); });

    /* ===== "Chagua zote (nje ya ukurasa)" ===== */
    $('#sel-all').addEventListener('click', () => {
        selectAll = true;
        sel.clear(); mark();
        refreshSel();
        toast('Zote zinazolingana zimechaguliwa (' + $('#total').textContent + ')');
    });
    $('#all-clear').addEventListener('click', () => {
        selectAll = false; sel.clear(); mark(); refreshSel();
    });

    /* ----- step 3 confirmation ----- */
    let pending = null;
    const modal = $('#modal');
    function closeModal() { modal.classList.add('hidden'); document.body.style.overflow = ''; $('#m-pw').value = ''; $('#m-err').classList.add('hidden'); }
    $('#m-cancel').addEventListener('click', closeModal);
    $('#modal-bg').addEventListener('click', closeModal);
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal(); });
    const validate = () => { $('#m-go').disabled = !$('#m-pw').value || !!(pending && pending.blocked); };
    $('#m-pw').addEventListener('input', validate);
    $('#m-pw').addEventListener('keydown', (e) => { if (e.key === 'Enter' && !$('#m-go').disabled) $('#m-go').click(); });

    const names = (arr) => '<details class="mt-2"><summary class="text-xs text-emerald-700 cursor-pointer select-none">Angalia majina</summary><ul class="mt-2 max-h-36 overflow-y-auto text-sm text-gray-700 space-y-1 pl-4 list-disc">' + arr.map((r) => '<li>' + esc(r.name) + '</li>').join('') + '</ul></details>';
    const box = (cls, n, title, text, arr) => '<div class="rounded-xl border p-3.5 ' + cls + '"><p><span class="text-2xl font-bold">' + n + '</span> <span class="text-sm font-semibold">' + title + '</span></p><p class="text-xs mt-1 opacity-80">' + text + '</p>' + (arr && arr.length ? names(arr) : '') + '</div>';

    async function openConfirm(action) {
        if (!selectAll && !sel.size) return;

        // Build request body: either explicit ids, or filter (select_all).
        const body = selectAll
            ? Object.assign({select_all: true}, filterPayload())
            : {ids: Array.from(sel)};

        const j = await call(D.urlPreview, 'POST', body);
        if (!j.success) { toast(j.message || 'Imeshindikana', 'error'); return; }

        const go = $('#m-go'), icon = $('#m-icon');
        let html = '', blocked = false;
        if (action === 'delete') {
            icon.className = 'w-11 h-11 shrink-0 rounded-xl flex items-center justify-center bg-red-100 text-red-600';
            icon.innerHTML = '<i class="fas fa-trash"></i>';
            $('#m-title').textContent = 'Futa kabisa?';
            html = box('bg-red-50 border-red-200 text-red-900', j.deletable.length, 'zitafutwa', 'Hazina historia. Hatua hii haiwezi kurudishwa.', j.deletable);
            if (j.protected.length) html += box('bg-amber-50 border-amber-200 text-amber-900', j.protected.length, 'hazitafutwa', 'Zina historia. Zitumie "Weka kando".', j.protected);
            if (!j.deletable.length) { blocked = true; html += '<button type="button" id="to-arch" class="w-full text-sm font-medium text-amber-700 underline">Weka kando badala yake</button>'; }
            go.textContent = 'Futa'; go.className = 'flex-1 py-3 rounded-xl text-sm font-semibold text-white bg-red-600 hover:bg-red-700 disabled:opacity-40 disabled:cursor-not-allowed';
            $('#m-reason').classList.add('hidden');
        } else {
            const all = j.deletable.concat(j.protected);
            icon.className = 'w-11 h-11 shrink-0 rounded-xl flex items-center justify-center bg-amber-100 text-amber-600';
            icon.innerHTML = '<i class="fas fa-archive"></i>';
            $('#m-title').textContent = 'Weka kando?';
            html = box('bg-gray-50 border-gray-200 text-gray-800', all.length, 'zitawekwa kando', 'Zitaondoka kwenye orodha. Historia inabaki. Unaweza kuzirejesha.', all);
            go.textContent = 'Weka kando'; go.className = 'flex-1 py-3 rounded-xl text-sm font-semibold text-white bg-amber-500 hover:bg-amber-600 disabled:opacity-40 disabled:cursor-not-allowed';
            $('#m-reason').classList.remove('hidden');
        }
        $('#m-body').innerHTML = html;

        pending = Object.assign(
            {action: action, blocked: blocked},
            selectAll ? Object.assign({select_all: true}, filterPayload()) : {ids: Array.from(sel)}
        );

        validate();
        modal.classList.remove('hidden'); document.body.style.overflow = 'hidden';
        setTimeout(() => $('#m-pw').focus(), 100);
        const t = $('#to-arch'); if (t) t.addEventListener('click', () => { closeModal(); openConfirm('archive'); });
    }
    $('#btn-delete').addEventListener('click', () => openConfirm('delete'));
    $('#btn-archive').addEventListener('click', () => openConfirm('archive'));

    $('#m-go').addEventListener('click', async () => {
        if (!pending) return;
        const go = $('#m-go'), label = go.textContent;
        go.disabled = true; go.textContent = 'Inafanya kazi...';

        const body = {
            action:   pending.action,
            password: $('#m-pw').value,
            reason:   $('#m-reason').value || '',
        };
        if (pending.select_all) {
            body.select_all = true;
            body.criteria   = pending.criteria;
            body.match      = pending.match;
            body.search     = pending.search;
            body.archived   = pending.archived;
        } else {
            body.ids = pending.ids;
        }

        const j = await call(D.urlExecute, 'POST', body);
        go.textContent = label;
        if (!j.success) {
            $('#m-err').textContent = j.message || (j.errors ? Object.values(j.errors)[0][0] : 'Imeshindikana');
            $('#m-err').classList.remove('hidden'); validate(); return;
        }
        closeModal();
        toast(j.message + (j.skipped && j.skipped.length ? ' (' + j.skipped.length + ' zimerukwa)' : ''));
        selectAll = false; sel.clear(); load(); refreshSel();
    });

    $('#btn-restore').addEventListener('click', async () => {
        if (!selectAll && !sel.size) return;
        const body = selectAll
            ? Object.assign({select_all: true}, filterPayload())
            : {ids: Array.from(sel)};
        const j = await call(D.urlRestore, 'POST', body);
        toast(j.message || 'Imefanikiwa', j.success ? '' : 'error');
        selectAll = false; sel.clear(); load(); refreshSel();
    });

    paintOpts(); paintView(); load(); refreshSel();
})();
</script>
@endverbatim
@endpush