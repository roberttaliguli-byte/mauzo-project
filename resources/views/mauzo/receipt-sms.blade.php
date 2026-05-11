{{-- SMS Format for Receipt --}}
@php
    $company = \App\Models\Company::find($companyId);
    $companyName = $company->company_name ?? 'Biashara Yangu';
    $companyPhone = $company->phone ?? '';
@endphp

*RISITI YA MALIPO*
------------------
Kampuni: {{ $companyName }}
Namba: {{ $receiptNo }}
Tarehe: {{ $date }}
------------------
@foreach($items as $item)
{{ $item['bidhaa'] }} 
  {{ number_format($item['idadi'], 2) }} x {{ number_format($item['bei'], 0) }} = {{ number_format($item['jumla'], 0) }}
  @if($item['punguzo'] > 0)
  Punguzo: -{{ number_format($item['punguzo'], 0) }}
  @endif
@endforeach
------------------
Jumla: {{ number_format($subtotal, 0) }}
Punguzo: -{{ number_format($punguzo, 0) }}
------------------
Jumla Kuu: {{ number_format($total, 0) }}
------------------
Asante kwa kununua!
{{ $companyName }}
{{ $companyPhone }}


<select id="bidhaaSelect" name="bidhaa_id" size="5" class="...">
    <option value="">Chagua Bidhaa...</option>
    @foreach($bidhaa as $item)
    <option
        value="{{ $item->id }}"
        data-bei-rejareja="{{ $item->bei_kuuza }}"
        data-bei-jumla="{{ $item->bei_uzo_jumla }}"
        data-stock="{{ $item->idadi }}"
        data-jina="{{ e($item->jina) }}"
        data-aina="{{ e($item->aina) }}"
        data-kipimo="{{ e($item->kipimo) }}"
        data-bei-nunua="{{ $item->bei_nunua }}"
        data-barcode="{{ $item->barcode }}"
    >
        {{ $item->jina }} ({{ $item->aina }}) - {{ $item->kipimo }} - Rejareja: {{ number_format($item->bei_kuuza, 0) }} | Jumla: {{ $item->bei_uzo_jumla ? number_format($item->bei_uzo_jumla, 0) : 'N/A' }} - Stock: {{ $item->idadi }}
    </option>
    @endforeach
</select>