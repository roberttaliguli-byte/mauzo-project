@php
    $shiftViewer = \App\Models\ShiftSession::currentOwner();
    $shiftCanViewAll = (bool) ($shiftViewer['can_view_all'] ?? false);
    $showProfit = $showProfit ?? $shiftCanViewAll;
@endphp

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm" id="vikao-app"
     data-sessions-url="{{ route('shift.sessions') }}"
     data-close-url="{{ route('shift.close') }}"
     data-report-url-template="{{ route('shift.report', ['shiftSession' => '__ID__']) }}"
     data-send-url-template="{{ route('shift.send.report', ['shiftSession' => '__ID__']) }}">

    <div class="p-4 border-b border-gray-200 bg-amber-soft">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div>
                <h2 class="text-sm font-bold flex items-center text-gray-800">
                    <i class="fas fa-user-clock text-amber-600 mr-2"></i>
                    <span class="amber-label">Vikao vya Shift (Mauzo ya Mfanyakazi)</span>
                </h2>
                <p class="text-xs text-gray-700 mt-1 font-medium">
                    Kila mfanyakazi ana shift yake. Mauzo, {{ $showProfit ? 'faida na ' : '' }}matumizi ya shift moja hayachanganywi na shift nyingine.
                    {{ $shiftCanViewAll ? 'Wewe unaona vikao vyote vya kampuni.' : 'Unaona vikao vyako pekee.' }}
                </p>
            </div>
            <span class="inline-flex items-center gap-1.5 text-[11px] font-bold px-2.5 py-1 rounded-full {{ $shiftCanViewAll ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800' }}">
                <i class="fas {{ $shiftCanViewAll ? 'fa-crown' : 'fa-user' }}"></i>
                {{ $shiftCanViewAll ? 'MWONEKANO WA BOSS' : 'SHIFT ZAKO' }}
            </span>
        </div>
    </div>

    <div class="p-4">
        <!-- Filters -->
        <div class="grid grid-cols-1 sm:grid-cols-2 {{ $shiftCanViewAll ? 'lg:grid-cols-5' : 'lg:grid-cols-4' }} gap-3 mb-4">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Kuanzia tarehe</label>
                <input type="date" id="vs-from" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Mpaka tarehe</label>
                <input type="date" id="vs-to" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Hali</label>
                <select id="vs-status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500">
                    <option value="">Zote</option>
                    <option value="open">Inaendelea</option>
                    <option value="closed">Imefungwa</option>
                </select>
            </div>
            @if($shiftCanViewAll)
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Mtumiaji</label>
                <select id="vs-user" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500">
                    <option value="">Wote</option>
                    <optgroup label="Wakuu">
                        @foreach($bossUsers ?? [] as $boss)
                            <option value="boss_{{ $boss->id }}" data-type="boss" data-id="{{ $boss->id }}">👑 {{ $boss->name ?? $boss->username }}</option>
                        @endforeach
                    </optgroup>
                    <optgroup label="Wafanyakazi">
                        @foreach($employeeUsers ?? [] as $employee)
                            <option value="employee_{{ $employee->id }}" data-type="employee" data-id="{{ $employee->id }}">👤 {{ $employee->jina }}</option>
                        @endforeach
                    </optgroup>
                </select>
            </div>
            @endif
            <div class="flex items-end gap-2">
                <button type="button" id="vs-filter" class="flex-1 bg-amber-600 hover:bg-amber-700 text-white px-3 py-2 rounded-lg text-sm font-semibold transition">
                    <i class="fas fa-filter mr-1"></i> Chuja
                </button>
                <button type="button" id="vs-reset" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-2 rounded-lg text-sm font-semibold transition">
                    <i class="fas fa-redo"></i>
                </button>
            </div>
        </div>

        <!-- Summary cards -->
        <div id="vs-summary" class="grid grid-cols-2 {{ $showProfit ? 'lg:grid-cols-5' : 'lg:grid-cols-4' }} gap-3 mb-4"></div>

        <!-- Status message -->
        <div id="vs-error" class="hidden mb-3 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700"></div>

        <!-- Table -->
        <div class="border border-gray-200 rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-xs sm:text-sm">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="text-left px-3 py-2 font-semibold">Mfanyakazi</th>
                            <th class="text-left px-3 py-2 font-semibold hidden md:table-cell">Kuanzia</th>
                            <th class="text-left px-3 py-2 font-semibold hidden lg:table-cell">Kufunga</th>
                            <th class="text-right px-3 py-2 font-semibold">Mauzo</th>
                            <th class="text-right px-3 py-2 font-semibold hidden sm:table-cell">Bidhaa</th>
                            @if($showProfit)
                            <th class="text-right px-3 py-2 font-semibold">Faida</th>
                            @endif
                            <th class="text-right px-3 py-2 font-semibold hidden lg:table-cell">Matumizi</th>
                            <th class="text-center px-3 py-2 font-semibold">Hali</th>
                            <th class="text-center px-3 py-2 font-semibold">Vitendo</th>
                        </tr>
                    </thead>
                    <tbody id="vs-rows" class="divide-y divide-gray-100">
                        <tr><td colspan="{{ $showProfit ? 9 : 8 }}" class="px-3 py-8 text-center text-gray-500"><i class="fas fa-spinner fa-spin mr-2"></i> Inapakia vikao...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        <p class="text-[10px] text-gray-500 mt-2"><i class="fas fa-info-circle mr-1"></i> Salio linalotarajiwa = fedha ya kuanzia + mauzo taslimu - matumizi ya shift.</p>
    </div>
</div>

<!-- Shift detail modal -->
<div id="vs-modal" class="hidden fixed inset-0 z-[75] overflow-y-auto">
    <div class="fixed inset-0 bg-black/50" id="vs-modal-backdrop"></div>
    <div class="relative mx-auto my-4 w-[96%] max-w-3xl bg-white rounded-2xl shadow-2xl overflow-hidden">
        <div class="flex items-center justify-between px-4 py-3 bg-gradient-to-r from-gray-900 to-gray-700 text-white">
            <div>
                <h3 class="text-sm sm:text-base font-bold"><i class="fas fa-clipboard-check mr-2"></i><span id="vs-modal-title">Ripoti ya Shift</span></h3>
                <p id="vs-modal-subtitle" class="text-[11px] text-gray-300">—</p>
            </div>
            <button type="button" id="vs-modal-close" class="text-white/80 hover:text-white text-xl leading-none px-2">&times;</button>
        </div>
        <div id="vs-modal-body" class="p-4 space-y-4 max-h-[75vh] overflow-y-auto"></div>
    </div>
</div>

@once
@push('scripts')
<script>
(function () {
    const app = document.getElementById('vikao-app');
    if (!app) return;

    const urls = {
        sessions: app.dataset.sessionsUrl,
        close: app.dataset.closeUrl,
        report: app.dataset.reportUrlTemplate,
        send: app.dataset.sendUrlTemplate,
    };

    const canViewAll = @json($shiftCanViewAll);
    const showProfit = @json($showProfit);
    const columnCount = showProfit ? 9 : 8;
    let sessions = [];
    let activeSession = null;
    let busy = false;

    const $ = (id) => document.getElementById(id);
    const money = (value) => 'Tsh ' + new Intl.NumberFormat('en-US', { maximumFractionDigits: 0 }).format(Number(value || 0));
    const qty = (value) => new Intl.NumberFormat('en-US', { maximumFractionDigits: 2 }).format(Number(value || 0));
    const esc = (value) => String(value ?? '').replace(/[&<>"']/g, (m) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]));
    const reportUrl = (id) => urls.report.replace('__ID__', id);
    const sendUrl = (id) => urls.send.replace('__ID__', id);
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

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
            throw error;
        }

        return data;
    }

    function renderSummary() {
        const totals = sessions.reduce((acc, s) => {
            acc.sales += Number(s.totals?.total_sales || 0);
            acc.profit += Number(s.totals?.total_profit || 0);
            acc.expenses += Number(s.totals?.total_expenses || 0);
            acc.items += Number(s.totals?.total_items || 0);
            if (s.is_open) acc.open += 1;
            return acc;
        }, { sales: 0, profit: 0, expenses: 0, items: 0, open: 0 });

        $('vs-summary').innerHTML = `
            <div class="rounded-xl border border-gray-200 p-3 bg-gray-50">
                <p class="text-[10px] uppercase font-semibold text-gray-500">Vikao</p>
                <p class="text-base font-extrabold text-gray-800">${sessions.length}</p>
                <p class="text-[10px] text-gray-500">${totals.open} vinaendelea</p>
            </div>
            <div class="rounded-xl border border-amber-200 p-3 bg-amber-50">
                <p class="text-[10px] uppercase font-semibold text-amber-800">Mauzo yote</p>
                <p class="text-base font-extrabold text-amber-900">${money(totals.sales)}</p>
            </div>
            ${showProfit ? `<div class="rounded-xl border border-emerald-200 p-3 bg-emerald-50">
                <p class="text-[10px] uppercase font-semibold text-emerald-800">Faida</p>
                <p class="text-base font-extrabold text-emerald-900">${money(totals.profit)}</p>
            </div>` : ''}
            <div class="rounded-xl border border-red-200 p-3 bg-red-50">
                <p class="text-[10px] uppercase font-semibold text-red-800">Matumizi</p>
                <p class="text-base font-extrabold text-red-900">${money(totals.expenses)}</p>
            </div>
            <div class="rounded-xl border border-gray-200 p-3 bg-gray-50">
                <p class="text-[10px] uppercase font-semibold text-gray-500">Bidhaa</p>
                <p class="text-base font-extrabold text-gray-800">${qty(totals.items)}</p>
            </div>`;
    }

    function renderRows() {
        renderSummary();
        const tbody = $('vs-rows');

        if (!sessions.length) {
            tbody.innerHTML = `<tr><td colspan="${columnCount}" class="px-3 py-8 text-center text-gray-500">Hakuna vikao vinavyolingana na kichujio.</td></tr>`;
            return;
        }

        tbody.innerHTML = sessions.map((s) => {
            const t = s.totals || {};
            const diff = t.cash_difference;
            const diffClass = diff === null ? 'text-gray-500' : (Number(diff) < -0.009 ? 'text-red-600' : 'text-emerald-700');
            const statusBadge = s.is_open
                ? '<span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800"><span class="h-1.5 w-1.5 rounded-full bg-emerald-600 animate-pulse"></span>Inaendelea</span>'
                : '<span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-gray-200 text-gray-700">Imefungwa</span>';

            return `<tr class="hover:bg-amber-50/60">
                <td class="px-3 py-2">
                    <div class="flex items-center gap-2">
                        <span class="h-8 w-8 shrink-0 rounded-full bg-amber-600 text-white flex items-center justify-center text-[11px] font-bold">${esc(s.owner_initials)}</span>
                        <div class="min-w-0">
                            <p class="font-bold text-gray-800 truncate">${esc(s.owner_name)}</p>
                            <p class="text-[10px] text-gray-500">${esc(s.owner_role)} • ${esc(s.duration_text)}</p>
                        </div>
                    </div>
                </td>
                <td class="px-3 py-2 hidden md:table-cell text-gray-600">${esc(s.started_at_text)}</td>
                <td class="px-3 py-2 hidden lg:table-cell text-gray-600">${s.is_open ? '—' : esc(s.closed_at_text)}</td>
                <td class="px-3 py-2 text-right font-extrabold text-gray-800">${money(t.total_sales)}</td>
                <td class="px-3 py-2 text-right hidden sm:table-cell text-gray-700">${qty(t.total_items)}</td>
                ${showProfit ? `<td class="px-3 py-2 text-right font-bold text-emerald-700">${money(t.total_profit)}</td>` : ''}
                <td class="px-3 py-2 text-right hidden lg:table-cell font-semibold text-red-600">${money(t.total_expenses)}</td>
                <td class="px-3 py-2 text-center">${statusBadge}</td>
                <td class="px-3 py-2">
                    <div class="flex items-center justify-center gap-1">
                        <button type="button" onclick="vsOpenDetail(${s.id})" class="bg-amber-100 hover:bg-amber-200 text-amber-800 px-2 py-1.5 rounded text-[11px] font-bold" title="Tazama ripoti"><i class="fas fa-eye"></i></button>
                        ${s.is_open ? `<button type="button" onclick="vsOpenDetail(${s.id})" class="bg-red-100 hover:bg-red-200 text-red-700 px-2 py-1.5 rounded text-[11px] font-bold" title="Funga shift"><i class="fas fa-lock"></i></button>` : `<button type="button" onclick="vsSendReport(${s.id}, 'copy')" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-2 py-1.5 rounded text-[11px] font-bold" title="Nakili ripoti"><i class="fas fa-copy"></i></button>
                        <button type="button" onclick="vsSendReport(${s.id}, 'whatsapp')" class="bg-[#25D366]/15 hover:bg-[#25D366]/25 text-[#128C3E] px-2 py-1.5 rounded text-[11px] font-bold" title="Tuma WhatsApp"><i class="fab fa-whatsapp"></i></button>`}
                    </div>
                </td>
            </tr>`;
        }).join('');
    }

    async function fetchSessions() {
        const params = new URLSearchParams();
        if ($('vs-from')?.value) params.set('from', $('vs-from').value);
        if ($('vs-to')?.value) params.set('to', $('vs-to').value);
        if ($('vs-status')?.value) params.set('status', $('vs-status').value);

        const userValue = $('vs-user')?.value;
        if (userValue) {
            const option = $('vs-user').selectedOptions[0];
            params.set('user_type', option.dataset.type || '');
            params.set('user_id', option.dataset.id || '');
        }

        $('vs-rows').innerHTML = `<tr><td colspan="${columnCount}" class="px-3 py-8 text-center text-gray-500"><i class="fas fa-spinner fa-spin mr-2"></i> Inachuja vikao...</td></tr>`;
        $('vs-error').classList.add('hidden');

        try {
            const data = await request(urls.sessions + '?' + params.toString());
            sessions = data.sessions || [];
            renderRows();
        } catch (error) {
            $('vs-error').textContent = error.message;
            $('vs-error').classList.remove('hidden');
            $('vs-rows').innerHTML = `<tr><td colspan="${columnCount}" class="px-3 py-8 text-center text-red-600">Imeshindikana kupata vikao.</td></tr>`;
        }
    }

    function openModal(title, subtitle) {
        $('vs-modal-title').textContent = title || 'Ripoti ya Shift';
        $('vs-modal-subtitle').textContent = subtitle || '—';
        $('vs-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        $('vs-modal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    function stat(label, value, extra = '') {
        return `<div class="rounded-lg border border-gray-200 p-2.5 ${extra}">
            <p class="text-[10px] uppercase text-gray-500 font-semibold">${esc(label)}</p>
            <p class="text-sm font-extrabold text-gray-800">${esc(value)}</p>
        </div>`;
    }

    function renderDetail(summary) {
        const s = summary.session;
        const t = summary.totals;
        const products = summary.products || [];
        const expenses = summary.expenses || [];
        const diff = t.cash_difference;
        const diffClass = diff === null ? 'text-gray-600' : (Number(diff) < -0.009 ? 'text-red-600' : 'text-emerald-700');

        let html = `<div class="rounded-xl bg-gradient-to-r from-amber-50 to-white border border-amber-200 p-3 flex flex-wrap items-center gap-3">
            <div class="h-11 w-11 rounded-full bg-amber-600 text-white flex items-center justify-center font-bold">${esc(s.owner_initials)}</div>
            <div class="flex-1 min-w-[150px]">
                <p class="text-sm font-extrabold text-gray-800">${esc(s.owner_name)} <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-800">${esc(s.owner_role)}</span></p>
                <p class="text-xs text-gray-500">${esc(s.started_at_text)} → ${s.status === 'open' ? 'Inaendelea' : esc(s.closed_at_text)} • ${esc(s.duration_text)}</p>
            </div>
            <span class="text-[11px] font-bold px-2.5 py-1 rounded-full ${s.status === 'open' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-200 text-gray-700'}">${s.status === 'open' ? 'SHIFT INAENDELEA' : 'IMEFUNGWA'}</span>
        </div>`;

        html += `<div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
            ${stat('Mauzo', money(t.total_sales))}
            ${stat('Bidhaa', qty(t.total_items))}
            ${showProfit ? stat('Faida', money(t.total_profit), 'bg-emerald-50 border-emerald-200') : ''}
            ${stat('Matumizi', money(t.total_expenses), 'bg-red-50 border-red-200')}
            ${stat('Mauzo taslimu', money(t.cash_sales))}
            ${stat('Salio linalotarajiwa', money(t.expected_cash), 'bg-amber-50 border-amber-200')}
        </div>`;

        if (s.status !== 'open') {
            html += `<div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                ${stat('Fedha iliyohesabiwa', t.closing_cash === null ? '—' : money(t.closing_cash))}
                ${stat('Tofauti', diff === null ? '—' : money(diff), diffClass)}
                ${stat('Baada ya matumizi', money(t.net_after_expenses), 'bg-emerald-50 border-emerald-200')}
            </div>`;
            if (s.report_sent_at) {
                html += `<p class="text-[11px] text-gray-500"><i class="fas fa-check-circle text-emerald-600 mr-1"></i> Ripoti ilitumwa ${esc(s.report_sent_at)} ${s.report_sent_to ? 'kwa ' + esc(s.report_sent_to) : ''}</p>`;
            }
        }

        html += `<div class="border border-gray-200 rounded-xl overflow-hidden">
            <div class="px-3 py-2 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                <h4 class="text-xs font-extrabold text-gray-700 uppercase"><i class="fas fa-boxes mr-1 text-amber-600"></i> Bidhaa zilizouzwa</h4>
                <span class="text-[11px] text-gray-500">${products.length} aina</span>
            </div>
            <div class="overflow-x-auto"><table class="w-full text-xs sm:text-sm">
                <thead class="text-gray-500"><tr>
                    <th class="text-left px-3 py-2">Bidhaa</th><th class="text-right px-3 py-2">Idadi</th><th class="text-right px-3 py-2">Kiasi</th>${showProfit ? '<th class="text-right px-3 py-2">Faida</th>' : ''}
                </tr></thead><tbody class="divide-y divide-gray-100">`;

        if (!products.length) {
            html += `<tr><td colspan="${showProfit ? 4 : 3}" class="px-3 py-5 text-center text-gray-500">Hakuna mauzo kwenye shift hii.</td></tr>`;
        } else {
            products.forEach((p) => {
                html += `<tr><td class="px-3 py-2 font-semibold text-gray-800">${esc(p.jina)}</td><td class="px-3 py-2 text-right">${qty(p.idadi)}</td><td class="px-3 py-2 text-right font-semibold">${money(p.kiasi)}</td>${showProfit ? `<td class="px-3 py-2 text-right font-semibold text-emerald-700">${money(p.faida)}</td>` : ''}</tr>`;
            });
            html += `<tr class="bg-gray-50 font-extrabold"><td class="px-3 py-2">JUMLA</td><td class="px-3 py-2 text-right">${qty(t.total_items)}</td><td class="px-3 py-2 text-right">${money(t.total_sales)}</td>${showProfit ? `<td class="px-3 py-2 text-right text-emerald-700">${money(t.total_profit)}</td>` : ''}</tr>`;
        }
        html += '</tbody></table></div></div>';

        html += `<div class="border border-gray-200 rounded-xl overflow-hidden">
            <div class="px-3 py-2 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                <h4 class="text-xs font-extrabold text-gray-700 uppercase"><i class="fas fa-money-bill-wave mr-1 text-red-600"></i> Matumizi</h4>
                <span class="text-[11px] font-bold text-red-600">${money(t.total_expenses)}</span>
            </div>
            <div class="divide-y divide-gray-100">`;
        if (!expenses.length) {
            html += '<p class="px-3 py-4 text-center text-gray-500 text-xs">Hakuna matumizi kwenye shift hii.</p>';
        } else {
            expenses.forEach((e) => {
                html += `<div class="px-3 py-2 flex items-center justify-between gap-3 text-sm">
                    <div class="min-w-0"><p class="font-semibold text-gray-800 truncate">${esc(e.aina || 'Matumizi')}</p><p class="text-[11px] text-gray-500 truncate">${esc(e.maelezo || '—')}</p></div>
                    <span class="font-bold text-red-600 whitespace-nowrap">${money(e.gharama)}</span>
                </div>`;
            });
        }
        html += '</div></div>';

        if (s.status === 'open') {
            html += `<div class="rounded-xl border-2 border-amber-300 bg-amber-50 p-3 space-y-3">
                <p class="text-xs font-bold text-amber-900"><i class="fas fa-calculator mr-1"></i> Funga shift na uweke salio la mwisho</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div><label class="block text-[11px] font-semibold text-gray-700 mb-1">Fedha taslimu iliyohesabiwa (Tsh)</label>
                        <input id="vs-closing-cash" type="number" min="0" step="0.01" inputmode="decimal" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="Mfano: 120000">
                    </div>
                    <div><label class="block text-[11px] font-semibold text-gray-700 mb-1">Maelezo (hiari)</label>
                        <input id="vs-closing-notes" type="text" maxlength="1000" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="Shift ya mchana">
                    </div>
                </div>
                <button type="button" id="vs-confirm-close" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-lg text-sm font-extrabold"><i class="fas fa-lock mr-1"></i> Funga Shift Sasa</button>
                <p id="vs-close-error" class="hidden text-xs text-red-700 font-semibold"></p>
            </div>`;
        } else {
            html += `<div class="rounded-xl border border-gray-200 p-3 space-y-3">
                <p class="text-xs font-bold text-gray-800"><i class="fas fa-paper-plane mr-1 text-emerald-600"></i> Tuma ripoti kwa boss</p>
                <div class="flex flex-col sm:flex-row gap-2">
                    <button type="button" id="vs-send-copy" class="flex-1 bg-gray-800 hover:bg-gray-900 text-white px-3 py-2 rounded-lg text-sm font-bold"><i class="fas fa-copy mr-1"></i> Nakili Ripoti</button>
                    <button type="button" id="vs-send-whatsapp" class="flex-1 bg-[#25D366] hover:bg-[#1ebe5a] text-white px-3 py-2 rounded-lg text-sm font-bold"><i class="fab fa-whatsapp mr-1"></i> Tuma kwa WhatsApp</button>
                </div>
                <div class="flex gap-2">
                    <input id="vs-send-phone" type="tel" class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="Namba ya boss (hiari - inachukua ya kampuni)">
                </div>
                <p id="vs-send-status" class="hidden text-xs font-semibold"></p>
            </div>`;
        }

        $('vs-modal-body').innerHTML = html;

        $('vs-confirm-close')?.addEventListener('click', async () => {
            if (busy) return;
            const cash = $('vs-closing-cash')?.value;
            if (cash === '' || cash === null || Number(cash) < 0) {
                const err = $('vs-close-error');
                err.textContent = 'Weka kiasi sahihi cha fedha taslimu.';
                err.classList.remove('hidden');
                return;
            }
            busy = true;
            const btn = $('vs-confirm-close');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Inafunga...';
            try {
                const data = await request(urls.close, {
                    method: 'POST',
                    body: JSON.stringify({
                        session_id: s.id,
                        closing_cash: Number(cash),
                        notes: $('vs-closing-notes')?.value || null,
                    }),
                });
                activeSession = data.session;
                renderDetail(data.summary);
                fetchSessions();
            } catch (error) {
                const err = $('vs-close-error');
                err.textContent = error.message;
                err.classList.remove('hidden');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-lock mr-1"></i> Funga Shift Sasa';
            } finally {
                busy = false;
            }
        });

        $('vs-send-copy')?.addEventListener('click', () => sendReport(s.id, 'copy'));
        $('vs-send-whatsapp')?.addEventListener('click', () => sendReport(s.id, 'whatsapp'));
    }

    async function sendReport(id, channel) {
        if (busy) return;
        busy = true;
        const waWindow = channel === 'whatsapp' ? window.open('', '_blank') : null;
        const status = $('vs-send-status');
        if (status) {
            status.className = 'text-xs font-semibold text-gray-600';
            status.textContent = channel === 'copy' ? 'Inaandaa nakala...' : 'Inaandaa WhatsApp...';
            status.classList.remove('hidden');
        }

        try {
            const data = await request(sendUrl(id), {
                method: 'POST',
                body: JSON.stringify({ channel, phone: $('vs-send-phone')?.value || null }),
            });

            if (channel === 'copy') {
                const copied = await copyText(data.message || '');
                if (status) {
                    status.className = copied ? 'text-xs font-semibold text-emerald-700' : 'text-xs font-semibold text-red-600';
                    status.textContent = copied ? 'Ripoti imenakiliwa. Unaweza kuipaste kwa boss.' : 'Imeshindikana kunakili ripoti. Jaribu tena.';
                }
                if (!copied) throw new Error('Imeshindikana kunakili ripoti.');
            } else if (data.wa_link) {
                if (waWindow) {
                    waWindow.location.href = data.wa_link;
                } else {
                    window.location.href = data.wa_link;
                }
                if (status) {
                    status.className = 'text-xs font-semibold text-emerald-700';
                    status.textContent = 'WhatsApp imefunguliwa. Tuma ujumbe kwa boss.';
                }
            }

            fetchSessions();
        } catch (error) {
            if (waWindow) waWindow.close();
            if (status) {
                status.className = 'text-xs font-semibold text-red-600';
                status.textContent = error.message;
            } else {
                alert(error.message);
            }
        } finally {
            busy = false;
        }
    }

    window.vsOpenDetail = async function (id) {
        openModal('Ripoti ya Shift', 'Inapakia...');
        $('vs-modal-body').innerHTML = '<div class="flex items-center justify-center py-10 text-gray-500"><i class="fas fa-spinner fa-spin mr-2"></i> Inapakia ripoti...</div>';

        try {
            const data = await request(reportUrl(id));
            activeSession = data.session;
            $('vs-modal-subtitle').textContent = data.session.owner_name + ' • ' + data.session.started_at_text;
            renderDetail(data.session.summary);
        } catch (error) {
            $('vs-modal-body').innerHTML = `<div class="p-4 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">${esc(error.message)}</div>`;
        }
    };

    window.vsSendReport = sendReport;

    $('vs-filter')?.addEventListener('click', fetchSessions);
    $('vs-reset')?.addEventListener('click', () => {
        if ($('vs-from')) $('vs-from').value = '';
        if ($('vs-to')) $('vs-to').value = '';
        if ($('vs-status')) $('vs-status').value = '';
        if ($('vs-user')) $('vs-user').value = '';
        fetchSessions();
    });

    $('vs-modal-close')?.addEventListener('click', closeModal);
    $('vs-modal-backdrop')?.addEventListener('click', closeModal);
    document.addEventListener('keydown', (event) => { if (event.key === 'Escape') closeModal(); });

    fetchSessions();
})();</script>
@endpush
@endonce
