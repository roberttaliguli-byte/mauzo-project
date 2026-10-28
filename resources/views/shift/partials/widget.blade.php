{{-- Shift (mauzo session) widget — Mauzo page --}}
<div id="shift-widget" class="mb-4" data-current-url="{{ route('shift.current') }}"
     data-start-url="{{ route('shift.start') }}" data-close-url="{{ route('shift.close') }}"
     data-report-url-template="{{ route('shift.report', ['shiftSession' => '__ID__']) }}"
     data-send-url-template="{{ route('shift.send.report', ['shiftSession' => '__ID__']) }}">

    {{-- Loading --}}
    <div id="shift-loading" class="bg-white border border-gray-200 rounded-xl p-3 flex items-center gap-3 shadow-sm">
        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 text-gray-500">
            <i class="fas fa-clock animate-pulse"></i>
        </span>
        <span class="text-sm text-gray-600">Inatafuta hali ya shift...</span>
    </div>

    {{-- No open shift --}}
    <div id="shift-start-panel" class="hidden bg-white border border-amber-200 rounded-xl p-3 sm:p-4 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
            <div class="flex items-center gap-3 flex-1">
                <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-700">
                    <i class="fas fa-shop"></i>
                </span>
                <div>
                    <p class="text-sm font-bold text-gray-800">Shift haijaanza</p>
                    <p class="text-xs text-gray-500">Anza shift ili mauzo na matumizi yako vihesabiwe peke yako.</p>
                </div>
            </div>
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <input id="shift-opening-balance" type="number" min="0" step="0.01" inputmode="decimal"
                       placeholder="Salio la kuanzia (hiari)"
                       class="w-full sm:w-48 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-200 focus:border-amber-500">
                <button id="shift-start-btn" type="button"
                        class="shrink-0 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow transition">
                    <i class="fas fa-play mr-1"></i> Anza Shift
                </button>
            </div>
        </div>
        <p id="shift-start-error" class="hidden mt-2 text-xs text-red-600 font-semibold"></p>
    </div>

    {{-- Open shift --}}
    <div id="shift-open-panel" class="hidden rounded-xl shadow-sm border border-emerald-200 bg-gradient-to-r from-emerald-50 to-white overflow-hidden">
        <div class="p-3 sm:p-4">
            <div class="flex flex-col lg:flex-row lg:items-center gap-3">
                <div class="flex items-center gap-3 flex-1 min-w-0">
                    <span class="relative inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-600 text-white">
                        <i class="fas fa-user-clock"></i>
                        <span class="absolute -top-0.5 -right-0.5 h-3 w-3 rounded-full bg-lime-400 border-2 border-white animate-pulse"></span>
                    </span>
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-gray-800 flex items-center gap-2">
                            Shift inaendelea
                            <span id="shift-owner-badge" class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">—</span>
                        </p>
                        <p class="text-xs text-gray-500 truncate">
                            <span id="shift-start-time">—</span>
                            <span class="mx-1">•</span>
                            <span id="shift-duration">—</span>
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 flex-1">
                    <div class="bg-white rounded-lg border border-gray-100 px-2 py-1.5">
                        <p class="text-[10px] text-gray-500 uppercase font-semibold">Mauzo</p>
                        <p id="shift-live-sales" class="text-sm font-extrabold text-gray-800">Tsh 0</p>
                    </div>
                    <div class="bg-white rounded-lg border border-gray-100 px-2 py-1.5">
                        <p class="text-[10px] text-gray-500 uppercase font-semibold">Bidhaa</p>
                        <p id="shift-live-items" class="text-sm font-extrabold text-gray-800">0</p>
                    </div>
                    <div id="shift-profit-card" class="bg-white rounded-lg border border-gray-100 px-2 py-1.5">
                        <p class="text-[10px] text-gray-500 uppercase font-semibold">Faida</p>
                        <p id="shift-live-profit" class="text-sm font-extrabold text-emerald-700">Tsh 0</p>
                    </div>
                    <div class="bg-white rounded-lg border border-gray-100 px-2 py-1.5">
                        <p class="text-[10px] text-gray-500 uppercase font-semibold">Matumizi</p>
                        <p id="shift-live-expenses" class="text-sm font-extrabold text-red-600">Tsh 0</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" id="shift-refresh-btn"
                            class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-2.5 py-2 rounded-lg text-sm font-bold transition"
                            title="Sasisha takwimu">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                    <button type="button" id="shift-view-btn"
                            class="bg-white border border-emerald-300 text-emerald-800 hover:bg-emerald-50 px-3 py-2 rounded-lg text-sm font-bold transition">
                        <i class="fas fa-eye mr-1"></i> Ripoti
                    </button>
                    <button type="button" id="shift-close-btn"
                            class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg text-sm font-bold shadow transition">
                        <i class="fas fa-lock mr-1"></i> Funga Shift
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Shift report / closing modal --}}
<div id="shift-modal" class="hidden fixed inset-0 z-[70] overflow-y-auto">
    <div class="fixed inset-0 bg-black/50" id="shift-modal-backdrop"></div>
    <div class="relative mx-auto my-4 w-[96%] max-w-3xl bg-white rounded-2xl shadow-2xl overflow-hidden">
        <div class="flex items-center justify-between px-4 py-3 bg-gradient-to-r from-gray-900 to-gray-700 text-white">
            <div>
                <h3 class="text-sm sm:text-base font-bold"><i class="fas fa-clipboard-check mr-2"></i><span id="shift-modal-title">Ripoti ya Shift</span></h3>
                <p id="shift-modal-subtitle" class="text-[11px] text-gray-300">—</p>
            </div>
            <button type="button" id="shift-modal-close" class="text-white/80 hover:text-white text-xl leading-none px-2">&times;</button>
        </div>

        <div id="shift-modal-body" class="p-4 space-y-4 max-h-[75vh] overflow-y-auto">
            <div class="flex items-center justify-center py-10 text-gray-500">
                <i class="fas fa-spinner fa-spin mr-2"></i> Inapakia ripoti...
            </div>
        </div>
    </div>
</div>

@once
@push('scripts')
<script>
(function () {
    const root = document.getElementById('shift-widget');
    if (!root) return;

    const urls = {
        current: root.dataset.currentUrl,
        start: root.dataset.startUrl,
        close: root.dataset.closeUrl,
        report: root.dataset.reportUrlTemplate,
        send: root.dataset.sendUrlTemplate,
    };

    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
    let currentSession = null;
    let canViewProfit = true;
    let busy = false;

    const $ = (id) => document.getElementById(id);

    const money = (value) => 'Tsh ' + new Intl.NumberFormat('en-US', { maximumFractionDigits: 0 }).format(Number(value || 0));
    const qty = (value) => new Intl.NumberFormat('en-US', { maximumFractionDigits: 2 }).format(Number(value || 0));
    const esc = (value) => String(value ?? '').replace(/[&<>"']/g, (m) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]));

    function reportUrl(id) { return urls.report.replace('__ID__', id); }
    function sendUrl(id) { return urls.send.replace('__ID__', id); }

    async function copyText(text) {
        if (navigator.clipboard && window.isSecureContext) {
            await navigator.clipboard.writeText(text);
            return true;
        }
        const area = document.createElement('textarea');
        area.value = text;
        area.style.position = 'fixed';
        area.style.opacity = '0';
        document.body.appendChild(area);
        area.focus();
        area.select();
        const ok = document.execCommand('copy');
        document.body.removeChild(area);
        return ok;
    }

    async function request(url, options = {}) {
        const response = await fetch(url, {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
            },
            credentials: 'same-origin',
            ...options,
        });

        let data = {};
        try { data = await response.json(); } catch (e) { data = { message: 'Jibu lisilo sahihi kutoka seva.' }; }

        if (!response.ok) {
            const validationMessage = data.errors ? Object.values(data.errors).flat()[0] : null;
            const error = new Error(validationMessage || data.message || 'Hitilafu imetokea.');
            error.data = data;
            error.status = response.status;
            throw error;
        }

        return data;
    }

    function showLoading() {
        $('shift-loading')?.classList.remove('hidden');
        $('shift-start-panel')?.classList.add('hidden');
        $('shift-open-panel')?.classList.add('hidden');
    }

    function renderCurrent(data) {
        currentSession = data.session || null;
        canViewProfit = !!(data.viewer && data.viewer.can_view_profit);
        $('shift-profit-card')?.classList.toggle('hidden', !canViewProfit);
        $('shift-loading')?.classList.add('hidden');

        if (!currentSession) {
            $('shift-open-panel')?.classList.add('hidden');
            $('shift-start-panel')?.classList.remove('hidden');
            return;
        }

        $('shift-start-panel')?.classList.add('hidden');
        $('shift-open-panel')?.classList.remove('hidden');

        const totals = currentSession.totals || {};
        $('shift-owner-badge').textContent = currentSession.owner_name || '—';
        $('shift-start-time').textContent = 'Kuanzia ' + (currentSession.started_at_text || '—');
        $('shift-duration').textContent = currentSession.duration_text || '—';
        $('shift-live-sales').textContent = money(totals.total_sales);
        $('shift-live-items').textContent = qty(totals.total_items);
        $('shift-live-profit').textContent = money(totals.total_profit);
        $('shift-live-expenses').textContent = money(totals.total_expenses);
    }

    async function refresh(silent = false) {
        try {
            if (!silent) showLoading();
            const data = await request(urls.current);
            renderCurrent(data);
        } catch (error) {
            if (silent) return;
            $('shift-loading').classList.add('hidden');
            $('shift-start-panel').classList.remove('hidden');
            const box = $('shift-start-error');
            if (box) { box.textContent = error.message; box.classList.remove('hidden'); }
        }
    }

    function openModal(title, subtitle) {
        $('shift-modal-title').textContent = title || 'Ripoti ya Shift';
        $('shift-modal-subtitle').textContent = subtitle || '—';
        $('shift-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        $('shift-modal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    function statCard(label, value, classes = '') {
        return `<div class="rounded-xl border border-gray-200 p-3 ${classes}">
            <p class="text-[10px] uppercase tracking-wide text-gray-500 font-semibold">${esc(label)}</p>
            <p class="text-base font-extrabold text-gray-800 mt-0.5">${esc(value)}</p>
        </div>`;
    }

    function renderReport(summary, options = {}) {
        const s = summary.session;
        const t = summary.totals;
        const products = summary.products || [];
        const expenses = summary.expenses || [];
        const isOpen = s.status === 'open';
        const canClose = isOpen && options.canClose;

        const difference = t.cash_difference;
        const diffClass = difference === null ? 'text-gray-600' : (difference < -0.009 ? 'text-red-600' : 'text-emerald-700');

        let html = '';

        html += `<div class="rounded-xl bg-gradient-to-r from-amber-50 to-white border border-amber-200 p-3 flex flex-wrap items-center gap-3">
            <div class="h-11 w-11 rounded-full bg-amber-600 text-white flex items-center justify-center font-bold">${esc(s.owner_initials)}</div>
            <div class="flex-1 min-w-[150px]">
                <p class="text-sm font-extrabold text-gray-800">${esc(s.owner_name)} <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-800">${esc(s.owner_role)}</span></p>
                <p class="text-xs text-gray-500">${esc(s.started_at_text)} → ${isOpen ? 'Inaendelea' : esc(s.closed_at_text)} • ${esc(s.duration_text)}</p>
            </div>
            <span class="text-[11px] font-bold px-2.5 py-1 rounded-full ${isOpen ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-200 text-gray-700'}">${isOpen ? 'SHIFT INAENDELEA' : 'IMEFUNGWA'}</span>
        </div>`;

        html += `<div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
            ${statCard('Mauzo (Tsh)', money(t.total_sales))}
            ${statCard('Bidhaa zilizouzwa', qty(t.total_items))}
            ${canViewProfit ? statCard('Faida', money(t.total_profit), 'bg-emerald-50 border-emerald-200') : ''}
            ${statCard('Matumizi', money(t.total_expenses), 'bg-red-50 border-red-200')}
            ${statCard('Mauzo taslimu', money(t.cash_sales))}
            ${statCard('Salio linalotarajiwa', money(t.expected_cash), 'bg-amber-50 border-amber-200')}
        </div>`;

        if (!isOpen) {
            html += `<div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                ${statCard('Fedha iliyohesabiwa', t.closing_cash === null ? '—' : money(t.closing_cash))}
                ${statCard('Tofauti', difference === null ? '—' : money(difference), diffClass)}
                ${statCard('Jumla baada ya matumizi', money(t.net_after_expenses), 'bg-emerald-50 border-emerald-200')}
            </div>`;
        }

        html += `<div class="border border-gray-200 rounded-xl overflow-hidden">
            <div class="px-3 py-2 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                <h4 class="text-xs font-extrabold text-gray-700 uppercase tracking-wide"><i class="fas fa-boxes mr-1 text-amber-600"></i> Bidhaa zilizouzwa</h4>
                <span class="text-[11px] text-gray-500">${products.length} aina</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs sm:text-sm">
                    <thead class="bg-white text-gray-500">
                        <tr>
                            <th class="text-left px-3 py-2">Bidhaa</th>
                            <th class="text-right px-3 py-2">Idadi</th>
                            <th class="text-right px-3 py-2">Kiasi</th>
                            ${canViewProfit ? '<th class="text-right px-3 py-2">Faida</th>' : ''}
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">`;

        if (products.length === 0) {
            html += `<tr><td colspan="${canViewProfit ? 4 : 3}" class="px-3 py-6 text-center text-gray-500">Hakuna mauzo kwenye shift hii bado.</td></tr>`;
        } else {
            products.forEach((p) => {
                html += `<tr class="hover:bg-amber-50/50">
                    <td class="px-3 py-2 font-semibold text-gray-800">${esc(p.jina)}</td>
                    <td class="px-3 py-2 text-right">${qty(p.idadi)}</td>
                    <td class="px-3 py-2 text-right font-semibold">${money(p.kiasi)}</td>
                    ${canViewProfit ? `<td class="px-3 py-2 text-right font-semibold text-emerald-700">${money(p.faida)}</td>` : ''}
                </tr>`;
            });
            html += `<tr class="bg-gray-50 font-extrabold">
                <td class="px-3 py-2">JUMLA</td>
                <td class="px-3 py-2 text-right">${qty(t.total_items)}</td>
                <td class="px-3 py-2 text-right">${money(t.total_sales)}</td>
                ${canViewProfit ? `<td class="px-3 py-2 text-right text-emerald-700">${money(t.total_profit)}</td>` : ''}
            </tr>`;
        }

        html += `</tbody></table></div></div>`;

        html += `<div class="border border-gray-200 rounded-xl overflow-hidden">
            <div class="px-3 py-2 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                <h4 class="text-xs font-extrabold text-gray-700 uppercase tracking-wide"><i class="fas fa-money-bill-wave mr-1 text-red-600"></i> Matumizi</h4>
                <span class="text-[11px] font-bold text-red-600">${money(t.total_expenses)}</span>
            </div>
            <div class="divide-y divide-gray-100">`;

        if (expenses.length === 0) {
            html += `<p class="px-3 py-4 text-center text-gray-500 text-xs">Hakuna matumizi kwenye shift hii.</p>`;
        } else {
            expenses.forEach((e) => {
                html += `<div class="px-3 py-2 flex items-center justify-between gap-3 text-sm">
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-800 truncate">${esc(e.aina || 'Matumizi')}</p>
                        <p class="text-[11px] text-gray-500 truncate">${esc(e.maelezo || '—')}</p>
                    </div>
                    <span class="font-bold text-red-600 whitespace-nowrap">${money(e.gharama)}</span>
                </div>`;
            });
        }

        html += `</div></div>`;

        if (canClose) {
            html += `<div class="rounded-xl border-2 border-amber-300 bg-amber-50 p-3 space-y-3">
                <p class="text-xs font-bold text-amber-900"><i class="fas fa-calculator mr-1"></i> Funga shift na uweke salio la mwisho</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-700 mb-1">Fedha taslimu iliyohesabiwa (Tsh)</label>
                        <input id="shift-closing-cash" type="number" min="0" step="0.01" inputmode="decimal"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-200 focus:border-amber-500"
                               placeholder="Mfano: 120000">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-700 mb-1">Maelezo (hiari)</label>
                        <input id="shift-closing-notes" type="text" maxlength="1000"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-200 focus:border-amber-500"
                               placeholder="Mfano: Shift ya mchana">
                    </div>
                </div>
                <button type="button" id="shift-confirm-close"
                        class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-lg text-sm font-extrabold shadow transition">
                    <i class="fas fa-lock mr-1"></i> Funga Shift Sasa
                </button>
                <p id="shift-close-error" class="hidden text-xs text-red-700 font-semibold"></p>
            </div>`;
        }

        if (!isOpen) {
            html += `<div class="rounded-xl border border-gray-200 p-3 space-y-3">
                <p class="text-xs font-bold text-gray-800"><i class="fas fa-paper-plane mr-1 text-emerald-600"></i> Tuma ripoti kwa boss</p>
                <div class="flex flex-col sm:flex-row gap-2">
                    <button type="button" id="shift-send-copy" class="flex-1 bg-gray-800 hover:bg-gray-900 text-white px-3 py-2 rounded-lg text-sm font-bold transition"><i class="fas fa-copy mr-1"></i> Nakili Ripoti</button>
                    <button type="button" id="shift-send-whatsapp" class="flex-1 bg-[#25D366] hover:bg-[#1ebe5a] text-white px-3 py-2 rounded-lg text-sm font-bold transition"><i class="fab fa-whatsapp mr-1"></i> Tuma kwa WhatsApp</button>
                </div>
                <div class="flex items-center gap-2">
                    <input id="shift-send-phone" type="tel" class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="Namba ya boss (hiari - inachukua ya kampuni)">
                    <button type="button" id="shift-print" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-3 py-2 rounded-lg text-sm font-bold transition"><i class="fas fa-print"></i></button>
                </div>
                <p id="shift-send-status" class="hidden text-xs font-semibold"></p>
            </div>`;
        }

        $('shift-modal-body').innerHTML = html;

        if (canClose) {
            $('shift-confirm-close')?.addEventListener('click', async () => {
                if (busy) return;
                const cashValue = $('shift-closing-cash')?.value;
                if (cashValue === '' || cashValue === null || Number(cashValue) < 0) {
                    const err = $('shift-close-error');
                    err.textContent = 'Weka kiasi sahihi cha fedha taslimu (au 0).';
                    err.classList.remove('hidden');
                    return;
                }

                busy = true;
                const btn = $('shift-confirm-close');
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Inafunga...';

                try {
                    const data = await request(urls.close, {
                        method: 'POST',
                        body: JSON.stringify({
                            closing_cash: Number(cashValue),
                            notes: $('shift-closing-notes')?.value || null,
                        }),
                    });

                    currentSession = data.session;
                    await refresh();
                    renderReport(data.summary, { canClose: false });
                    $('shift-modal-title').textContent = 'Shift imefungwa';
                    $('shift-modal-subtitle').textContent = 'Salio limehifadhiwa. Tuma ripoti kwa boss.';
                } catch (error) {
                    const err = $('shift-close-error');
                    err.textContent = error.message;
                    err.classList.remove('hidden');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-lock mr-1"></i> Funga Shift Sasa';
                } finally {
                    busy = false;
                }
            });
        }

        $('shift-send-copy')?.addEventListener('click', async () => {
            if (busy || !s.id) return;
            busy = true;
            const status = $('shift-send-status');
            status.className = 'text-xs font-semibold text-gray-600';
            status.textContent = 'Inaandaa nakala...';
            status.classList.remove('hidden');

            try {
                const data = await request(sendUrl(s.id), {
                    method: 'POST',
                    body: JSON.stringify({ channel: 'copy', phone: null }),
                });
                const copied = await copyText(data.message || '');
                status.className = copied ? 'text-xs font-semibold text-emerald-700' : 'text-xs font-semibold text-red-600';
                status.textContent = copied ? 'Ripoti imenakiliwa. Unaweza kuipaste kwa boss.' : 'Imeshindikana kunakili ripoti. Jaribu tena.';
            } catch (error) {
                status.className = 'text-xs font-semibold text-red-600';
                status.textContent = error.message;
            } finally {
                busy = false;
            }
        });

        $('shift-send-whatsapp')?.addEventListener('click', async () => {
            if (busy || !s.id) return;
            busy = true;
            const waWindow = window.open('', '_blank');
            const status = $('shift-send-status');
            status.className = 'text-xs font-semibold text-gray-600';
            status.textContent = 'Inaandaa WhatsApp...';
            status.classList.remove('hidden');

            try {
                const data = await request(sendUrl(s.id), {
                    method: 'POST',
                    body: JSON.stringify({ channel: 'whatsapp', phone: $('shift-send-phone')?.value || null }),
                });
                if (waWindow) {
                    waWindow.location.href = data.wa_link;
                } else {
                    window.location.href = data.wa_link;
                }
                status.className = 'text-xs font-semibold text-emerald-700';
                status.textContent = 'WhatsApp imefunguliwa. Tuma ujumbe kwa boss.';
            } catch (error) {
                if (waWindow) waWindow.close();
                status.className = 'text-xs font-semibold text-red-600';
                status.textContent = error.message;
            } finally {
                busy = false;
            }
        });

        $('shift-print')?.addEventListener('click', () => window.print());
    }

    async function openReport(options = {}) {
        if (!currentSession) {
            await refresh();
        }
        if (!currentSession) return;

        openModal('Ripoti ya Shift', currentSession.is_open ? 'Shift inaendelea — angalia mauzo na funga unapomaliza.' : 'Shift imefungwa.');
        $('shift-modal-body').innerHTML = '<div class="flex items-center justify-center py-10 text-gray-500"><i class="fas fa-spinner fa-spin mr-2"></i> Inapakia ripoti...</div>';

        try {
            const data = await request(reportUrl(currentSession.id));
            currentSession = data.session;
            canViewProfit = !!(data.viewer && data.viewer.can_view_profit);
            renderReport(data.session.summary, { canClose: true });
        } catch (error) {
            $('shift-modal-body').innerHTML = `<div class="p-4 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">${esc(error.message)}</div>`;
        }
    }

    // Events
    $('shift-start-btn')?.addEventListener('click', async () => {
        if (busy) return;
        busy = true;
        const btn = $('shift-start-btn');
        const errorBox = $('shift-start-error');
        errorBox.classList.add('hidden');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Inaanza...';

        try {
            const opening = $('shift-opening-balance')?.value;
            const data = await request(urls.start, {
                method: 'POST',
                body: JSON.stringify({ opening_balance: opening === '' ? 0 : Number(opening) }),
            });
            currentSession = data.session;
            await refresh();
        } catch (error) {
            errorBox.textContent = error.message;
            errorBox.classList.remove('hidden');
            if (error.status === 409) {
                await refresh();
            }
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-play mr-1"></i> Anza Shift';
            busy = false;
        }
    });

    $('shift-view-btn')?.addEventListener('click', () => openReport());
    $('shift-close-btn')?.addEventListener('click', () => openReport());
    $('shift-refresh-btn')?.addEventListener('click', () => refresh());
    $('shift-modal-close')?.addEventListener('click', closeModal);
    $('shift-modal-backdrop')?.addEventListener('click', closeModal);

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') closeModal();
    });

    // Refresh when the page becomes visible again (e.g. coming back from a sale).
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden && !busy) refresh(true);
    });

    // Keep the live totals fresh while the page stays open.
    setInterval(() => {
        if (!document.hidden && !busy) refresh(true);
    }, 30000);

    refresh();
})();
</script>
@endpush
@endonce
