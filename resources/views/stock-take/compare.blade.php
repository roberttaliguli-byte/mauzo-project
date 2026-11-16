{{-- resources/views/stock-take/compare.blade.php --}}
@extends('layouts.app')

@section('title', 'Linganisha uhesabuji')
@section('page-title', 'Ulinganisho wa stock')
@section('page-subtitle', $prev->reference . ' vs ' . $cur->reference)

@php
    $G = $cmp['groups'];
    $labels = [
        'resolved'       => ['Upungufu umetatuliwa',   'bg-emerald-100 text-emerald-800'],
        'improved'       => ['Upungufu umepungua',     'bg-emerald-100 text-emerald-800'],
        'worsened'       => ['Upungufu umeongezeka',   'bg-red-100 text-red-800'],
        'new_shortage'   => ['Upungufu mpya',          'bg-red-100 text-red-800'],
        'now_excess'     => ['Sasa kuna ziada',        'bg-blue-100 text-blue-800'],
        'unchanged'      => ['Haijabadilika',          'bg-gray-100 text-gray-700'],
        'not_comparable' => ['Haikulinganishika',      'bg-gray-100 text-gray-500'],
    ];
    $dotCls = [
        'resolved' => 'bg-emerald-500', 'improved' => 'bg-emerald-500',
        'worsened' => 'bg-red-500',     'new_shortage' => 'bg-red-500',
        'now_excess' => 'bg-blue-500',  'now_missing' => 'bg-purple-500',
        'unchanged' => 'bg-gray-400',   'not_comparable' => 'bg-gray-300',
    ];
    $f = fn ($v) => $v === null ? '-' : rtrim(rtrim(number_format($v, 2), '0'), '.');
    $verdictCls = $cmp['verdict'] === 'improved'
        ? 'border-emerald-200 bg-emerald-50'
        : ($cmp['verdict'] === 'deteriorated' ? 'border-red-200 bg-red-50' : 'border-gray-200 bg-white');
    $verdictTone = $cmp['verdict'] === 'improved'
        ? 'text-emerald-900'
        : ($cmp['verdict'] === 'deteriorated' ? 'text-red-900' : 'text-gray-800');
    $verdictText = $cmp['verdict'] === 'improved'
        ? 'Hali ya stock imeboreshwa'
        : ($cmp['verdict'] === 'deteriorated' ? 'Hali ya stock imezorota' : 'Hali ya stock iko sawa');

    $rows = collect($cmp['rows'])
        ->filter(fn($r) => $r['category'] !== 'not_comparable'
            && ($r['category'] !== 'unchanged' || $r['cur_variance'] != 0 || $r['now_missing']))
        ->sortBy('name');
@endphp

@section('content')
<div class="space-y-3 md:space-y-4 px-2 md:px-0">
    @include('stock-take._nav', ['active' => 'start'])

    {{-- ===== Header ===== --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-3 md:p-4 flex flex-wrap items-center justify-between gap-3">
        <div class="text-xs md:text-sm space-y-0.5">
            <div>
                <span class="text-gray-500">Awali:</span>
                <b class="text-gray-900">{{ $prev->reference }}</b>
                <span class="text-gray-400">· {{ optional($prev->closed_at ?? $prev->started_at)->format('d/m/Y') }}</span>
            </div>
            <div>
                <span class="text-gray-500">Sasa:</span>
                <b class="text-emerald-700">{{ $cur->reference }}</b>
                <span class="text-gray-400">· {{ optional($cur->closed_at ?? $cur->started_at)->format('d/m/Y') }}</span>
            </div>
        </div>
        @if($others->count())
        <form method="GET"
              onsubmit="event.preventDefault(); location.href='{{ url('stock-take/' . $cur->id . '/compare') }}/' + this.with.value;"
              class="flex gap-2 w-full sm:w-auto">
            <select name="with" class="flex-1 sm:flex-initial px-2.5 py-2 border border-gray-300 rounded-lg text-xs md:text-sm focus:ring-2 focus:ring-emerald-500">
                @foreach($others as $o)
                    <option value="{{ $o->id }}" @selected($o->id == $prev->id)>
                        {{ $o->reference }} — {{ optional($o->closed_at)->format('d/m/Y') }}
                    </option>
                @endforeach
            </select>
            <button class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs md:text-sm font-medium transition whitespace-nowrap">
                <i class="fas fa-code-compare mr-1"></i>Linganisha
            </button>
        </form>
        @endif
    </div>

    {{-- ===== Verdict ===== --}}
    <div class="rounded-lg border p-3 md:p-4 {{ $verdictCls }}">
        <div class="flex items-center gap-2">
            <i class="fas {{ $cmp['verdict'] === 'improved' ? 'fa-arrow-trend-up text-emerald-600' : ($cmp['verdict'] === 'deteriorated' ? 'fa-arrow-trend-down text-red-600' : 'fa-equals text-gray-500') }}"></i>
            <p class="font-semibold text-xs md:text-sm {{ $verdictTone }}">{{ $verdictText }}</p>
        </div>
        <p class="text-[10px] md:text-xs mt-1 {{ $verdictTone }}">
            Thamani ya upungufu (bei ya nunua):
            <b>{{ number_format($cmp['previous']['shortage_value'], 0) }} TZS</b>
            &rarr;
            <b>{{ number_format($cmp['current']['shortage_value'], 0) }} TZS</b>
            <span class="opacity-70">({{ $cmp['shortage_value_change'] > 0 ? '+' : '' }}{{ number_format($cmp['shortage_value_change'], 0) }})</span>.
            Bidhaa zenye upungufu:
            <b>{{ $cmp['previous']['shortage'] + $cmp['previous']['not_found'] }}</b>
            &rarr;
            <b>{{ $cmp['current']['shortage'] + $cmp['current']['not_found'] }}</b>.
        </p>
        <p class="text-[9px] md:text-[10px] mt-1 opacity-60">
            Kila tofauti ya sasa inajumuisha manunuzi, mauzo, marejesho na marekebisho kati ya hesabu hizi mbili.
        </p>
    </div>

    {{-- ===== Group cards ===== --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 md:gap-3">
        @foreach([
            ['resolved', 'Upungufu umetatuliwa'],
            ['improved', 'Upungufu umepungua'],
            ['worsened', 'Upungufu umeongezeka'],
            ['new_shortage', 'Upungufu mpya'],
            ['now_excess', 'Sasa kuna ziada'],
            ['now_missing', 'Ilikuwepo, haipo'],
            ['unchanged', 'Haijabadilika'],
            ['not_comparable', 'Haikulinganishika'],
        ] as [$k, $l])
            <div class="bg-white rounded-lg border border-gray-200 p-2.5 md:p-3">
                <div class="flex items-center gap-1.5 mb-0.5">
                    <span class="w-1.5 h-1.5 rounded-full {{ $dotCls[$k] ?? 'bg-gray-300' }}"></span>
                    <p class="text-[10px] md:text-xs text-gray-500 truncate">{{ $l }}</p>
                </div>
                <p class="text-lg md:text-xl font-bold text-gray-900">{{ $G[$k] ?? 0 }}</p>
            </div>
        @endforeach
    </div>

    {{-- ===== Comparison: Desktop table ===== --}}
    <div class="hidden md:block bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-gray-50 text-gray-500 uppercase tracking-wide">
                    <tr>
                        <th class="px-3 py-2 text-left">Bidhaa</th>
                        <th class="px-3 py-2 text-right">Hesabu ya awali</th>
                        <th class="px-3 py-2 text-right">Iliyoingia</th>
                        <th class="px-3 py-2 text-right">Iliyouzwa</th>
                        <th class="px-3 py-2 text-right">Inatarajiwa</th>
                        <th class="px-3 py-2 text-right">Sasa</th>
                        <th class="px-3 py-2 text-right">Tofauti awali</th>
                        <th class="px-3 py-2 text-right">Tofauti sasa</th>
                        <th class="px-3 py-2 text-left">Matokeo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                @forelse($rows as $r)
                    <tr class="hover:bg-gray-50/60">
                        <td class="px-3 py-2">
                            <div class="font-medium text-gray-900">{{ $r['name'] }}</div>
                            <div class="text-[10px] text-gray-500">{{ $r['unit'] }}</div>
                        </td>
                        <td class="px-3 py-2 text-right">{{ $f($r['prev_physical']) }}</td>
                        <td class="px-3 py-2 text-right text-gray-600">{{ $f($r['received']) }}</td>
                        <td class="px-3 py-2 text-right text-gray-600">{{ $f($r['sold']) }}</td>
                        <td class="px-3 py-2 text-right text-gray-600">{{ $f($r['expected']) }}</td>
                        <td class="px-3 py-2 text-right font-medium">{{ $f($r['cur_physical']) }}</td>
                        <td class="px-3 py-2 text-right text-gray-600">{{ $f($r['prev_variance']) }}</td>
                        <td class="px-3 py-2 text-right font-semibold
                            {{ $r['cur_variance'] < -0.005 ? 'text-red-600' : ($r['cur_variance'] > 0.005 ? 'text-blue-600' : 'text-emerald-600') }}">
                            {{ $f($r['cur_variance']) }}
                        </td>
                        <td class="px-3 py-2">
                            <div class="flex flex-wrap gap-1">
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-medium whitespace-nowrap {{ $labels[$r['category']][1] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ $labels[$r['category']][0] ?? $r['category'] }}
                                </span>
                                @if($r['now_missing'])
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-medium whitespace-nowrap bg-purple-100 text-purple-800">
                                        Haipo sasa
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-3 py-10 text-center text-gray-500 text-sm">
                            <i class="fas fa-equals text-2xl text-gray-300 block mb-2"></i>
                            Hakuna tofauti za kulinganisha.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ===== Comparison: Mobile cards ===== --}}
    <div class="md:hidden space-y-2">
        @forelse($rows as $r)
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-3">
                {{-- Header: name + status --}}
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-gray-900 break-words leading-snug">{{ $r['name'] }}</p>
                        <p class="text-[10px] text-gray-500 leading-tight">{{ $r['unit'] }}</p>
                    </div>
                    <div class="flex flex-col items-end gap-1 shrink-0">
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-medium whitespace-nowrap {{ $labels[$r['category']][1] ?? 'bg-gray-100 text-gray-700' }}">
                            {{ $labels[$r['category']][0] ?? $r['category'] }}
                        </span>
                        @if($r['now_missing'])
                            <span class="px-1.5 py-0.5 rounded text-[9px] font-medium whitespace-nowrap bg-purple-100 text-purple-800">
                                Haipo sasa
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Variance highlight --}}
                <div class="mt-2 flex items-center justify-between gap-2 rounded-md
                            {{ $r['cur_variance'] < -0.005 ? 'bg-red-50 border border-red-200' : ($r['cur_variance'] > 0.005 ? 'bg-blue-50 border border-blue-200' : 'bg-emerald-50 border border-emerald-200') }} px-2.5 py-1.5">
                    <span class="text-[10px] font-medium
                        {{ $r['cur_variance'] < -0.005 ? 'text-red-700' : ($r['cur_variance'] > 0.005 ? 'text-blue-700' : 'text-emerald-700') }}">
                        Tofauti sasa
                    </span>
                    <span class="text-sm font-bold tabular-nums
                        {{ $r['cur_variance'] < -0.005 ? 'text-red-700' : ($r['cur_variance'] > 0.005 ? 'text-blue-700' : 'text-emerald-700') }}">
                        {{ $f($r['cur_variance']) }}
                    </span>
                </div>

                {{-- Detail grid --}}
                <div class="mt-2 grid grid-cols-3 gap-1.5 text-[10px]">
                    <div class="rounded-md bg-gray-50 px-2 py-1.5">
                        <p class="text-gray-500 leading-none">Awali</p>
                        <p class="font-semibold text-gray-900 tabular-nums mt-0.5">{{ $f($r['prev_physical']) }}</p>
                    </div>
                    <div class="rounded-md bg-gray-50 px-2 py-1.5">
                        <p class="text-gray-500 leading-none">Iliyoingia</p>
                        <p class="font-semibold text-gray-900 tabular-nums mt-0.5">{{ $f($r['received']) }}</p>
                    </div>
                    <div class="rounded-md bg-gray-50 px-2 py-1.5">
                        <p class="text-gray-500 leading-none">Iliyouzwa</p>
                        <p class="font-semibold text-gray-900 tabular-nums mt-0.5">{{ $f($r['sold']) }}</p>
                    </div>
                    <div class="rounded-md bg-gray-50 px-2 py-1.5">
                        <p class="text-gray-500 leading-none">Inatarajiwa</p>
                        <p class="font-semibold text-gray-900 tabular-nums mt-0.5">{{ $f($r['expected']) }}</p>
                    </div>
                    <div class="rounded-md bg-emerald-50 px-2 py-1.5 col-span-2">
                        <p class="text-emerald-700 leading-none font-medium">Sasa (physical)</p>
                        <p class="font-bold text-gray-900 tabular-nums mt-0.5">{{ $f($r['cur_physical']) }}</p>
                    </div>
                    <div class="rounded-md bg-gray-50 px-2 py-1.5 col-span-3">
                        <p class="text-gray-500 leading-none">Tofauti awali</p>
                        <p class="font-semibold text-gray-700 tabular-nums mt-0.5">{{ $f($r['prev_variance']) }}</p>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-8 text-center">
                <i class="fas fa-equals text-2xl text-gray-300 block mb-2"></i>
                <p class="text-sm text-gray-500">Hakuna tofauti za kulinganisha.</p>
            </div>
        @endforelse
    </div>

    {{-- Footer note --}}
    <p class="text-[10px] md:text-xs text-gray-500 flex items-start gap-1.5">
        <i class="fas fa-circle-info text-gray-400 mt-0.5"></i>
        <span>
            Bidhaa ambazo hazikuhesabiwa kwenye vipindi vyote viwili, au zilizoongezwa baada ya kipindi cha awali,
            haziwezi kulinganishwa. Bidhaa zinazolingana bila tofauti zimefichwa.
        </span>
    </p>
</div>
@endsection