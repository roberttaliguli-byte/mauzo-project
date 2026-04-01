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
{{ $companyPhone }},,,,,,,,,,of C:\xampp\htdocs\mauzo\resources\views\mauzo\receipt-sms.blade.php   work together?