{{-- resources/views/stock-take/_nav.blade.php --}}
@php
    $__u    = auth('web')->user() ?: auth('mfanyakazi')->user();
    $__open = $__u ? app(\App\Services\StockTakeService::class)->openTake($__u->company_id) : null;
    $tabs   = [
        ['k' => 'start',   'label' => 'Anza / Historia',          'icon' => 'fa-clipboard-list', 'url' => route('stocktake.index')],
    ];
    if ($__open) {
        $tabs[] = ['k' => 'current', 'label' => 'Uhesabuji unaoendelea', 'icon' => 'fa-hourglass-half', 'url' => route('stocktake.show', $__open->id)];
    }
    if (auth('web')->check()) {
        $tabs[] = ['k' => 'cleanup', 'label' => 'Usafishaji wa bidhaa', 'icon' => 'fa-broom', 'url' => route('cleanup.index')];
    }
    $current = collect($tabs)->firstWhere('k', $active ?? '') ?? $tabs[0];
@endphp

<div id="st-nav" class="relative">
    {{-- Trigger button --}}
    <button type="button" id="st-nav-btn"
            class="w-full flex items-center gap-2.5 px-3 md:px-4 py-2.5 md:py-3 bg-white rounded-lg border border-gray-200 shadow-sm text-left hover:bg-gray-50 transition">
        <span class="w-7 h-7 md:w-8 md:h-8 rounded-md bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0">
            <i class="fas {{ $current['icon'] }} text-[11px] md:text-xs"></i>
        </span>
        <div class="flex-1 min-w-0">
            <p class="text-[10px] md:text-[11px] font-medium text-gray-500 uppercase tracking-wide leading-none">Ukarasa wa Stock Take</p>
            <p class="text-xs md:text-sm font-semibold text-gray-900 truncate leading-tight mt-0.5">{{ $current['label'] }}</p>
        </div>
        <i id="st-nav-chevron" class="fas fa-chevron-down text-gray-400 text-xs transition-transform shrink-0"></i>
    </button>

    {{-- Dropdown menu --}}
    <div id="st-nav-menu"
         class="hidden absolute z-40 left-0 right-0 mt-1.5 bg-white rounded-lg border border-gray-200 shadow-xl overflow-hidden">
        <div class="py-1">
            @foreach($tabs as $t)
                @php $on = ($active ?? '') === $t['k']; @endphp
                <a href="{{ $t['url'] }}"
                   class="flex items-center gap-2.5 px-3 md:px-4 py-2.5 text-xs md:text-sm transition
                          {{ $on ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-gray-700 hover:bg-gray-50' }}">
                    <span class="w-6 h-6 rounded-md flex items-center justify-center shrink-0
                                 {{ $on ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-500' }}">
                        <i class="fas {{ $t['icon'] }} text-[10px]"></i>
                    </span>
                    <span class="flex-1">{{ $t['label'] }}</span>
                    @if($on)
                        <i class="fas fa-check text-emerald-600 text-[10px]"></i>
                    @endif
                </a>
            @endforeach
            <div class="border-t border-gray-100 my-1"></div>
            <a href="{{ route('bidhaa.index') }}"
               class="flex items-center gap-2.5 px-3 md:px-4 py-2.5 text-xs md:text-sm text-gray-600 hover:bg-gray-50 transition">
                <span class="w-6 h-6 rounded-md flex items-center justify-center shrink-0 bg-gray-100 text-gray-500">
                    <i class="fas fa-arrow-left text-[10px]"></i>
                </span>
                <span class="flex-1">Rudi Bidhaa</span>
            </a>
        </div>
    </div>
</div>

@once
@push('scripts')
<script>
(function () {
    const btn = document.getElementById('st-nav-btn');
    const menu = document.getElementById('st-nav-menu');
    const ch = document.getElementById('st-nav-chevron');
    if (!btn || !menu) return;

    const open = () => {
        menu.classList.remove('hidden');
        ch.classList.add('rotate-180');
    };
    const close = () => {
        menu.classList.add('hidden');
        ch.classList.remove('rotate-180');
    };
    const toggle = (e) => {
        e.stopPropagation();
        menu.classList.contains('hidden') ? open() : close();
    };
    btn.addEventListener('click', toggle);
    document.addEventListener('click', (e) => {
        if (!menu.contains(e.target) && !btn.contains(e.target)) close();
    });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') close();
    });
})();
</script>
@endpush
@endonce