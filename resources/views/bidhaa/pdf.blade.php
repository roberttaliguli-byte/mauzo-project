<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>

    <style>
        @page {
            margin: 20px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            margin: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #000;
        }

        .header h2 {
            margin: 0;
            font-size: 18px;
        }

        .small-text {
            font-size: 10px;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .summary-table td {
            border: 1px solid #000;
            padding: 5px;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
        }

        table.data-table th {
            background: #000;
            color: #fff;
            padding: 6px;
            border: 1px solid #000;
            font-size: 11px;
        }

        table.data-table td {
            border: 1px solid #000;
            padding: 5px;
            font-size: 10px;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }

        tr {
            page-break-inside: avoid;
        }

        .footer {
            margin-top: 15px;
            font-size: 9px;
            border-top: 1px solid #000;
            padding-top: 5px;
            text-align: right;
        }
    </style>
</head>

<body>

    <!-- HEADER -->
<div class="header">
    <h2>{{ $company->company_name ?? 'KAMPUNI' }}</h2>

    <div class="small-text">
        @if($company->location)
            {{ $company->location }},
        @endif

        @if($company->region)
            {{ $company->region }} |
        @endif

        @if($company->phone)
            Simu: {{ $company->phone }} |
        @endif

        @if($company->email)
            Email: {{ $company->email }}
        @endif
    </div>

    <div class="small-text" style="margin-top:5px;">
        <strong>{{ $title }}</strong> |
        Tarehe: {{ $date }}
        @if(method_exists($bidhaa, 'currentPage'))
            | Page {{ $bidhaa->currentPage() }} / {{ $bidhaa->lastPage() }}
        @endif
    </div>
</div>


    <!-- SUMMARY -->
    <table class="summary-table">
        <tr>
            <td><strong>Jumla ya Bidhaa</strong></td>
            <td>{{ $total_count }}</td>
            <td><strong>Zilizoisha</strong></td>
            <td>{{ $bidhaa->where('idadi', 0)->count() }}</td>
        </tr>
        <tr>
            <td><strong>Zilizopo</strong></td>
            <td>{{ $bidhaa->where('idadi', '>', 0)->count() }}</td>
            <td><strong>Low Stock (&lt;10)</strong></td>
            <td>{{ $bidhaa->where('idadi', '<', 10)->where('idadi', '>', 0)->count() }}</td>
        </tr>
    </table>

    <!-- DATA TABLE -->
    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Bidhaa</th>
                <th>Aina</th>
                <th>Kipimo</th>
                <th>Idadi</th>
                <th>Bei Nunua</th>
                <th>Bei Kuuza</th>
                <th>Expiry</th>
                <th>Barcode</th>
            </tr>
        </thead>
        <tbody>

            @foreach($bidhaa as $index => $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->jina }}</td>
                <td>{{ $item->aina }}</td>
                <td>{{ $item->kipimo ?? '--' }}</td>
                <td class="text-center">
                    @if($item->idadi == 0)
                        0 (Imeisha)
                    @elseif($item->idadi < 10)
                        {{ $item->idadi }} (Low)
                    @else
                        {{ $item->idadi }}
                    @endif
                </td>
                <td class="text-right">
                    {{ number_format($item->bei_nunua, 0) }}
                </td>
                <td class="text-right">
                    {{ number_format($item->bei_kuuza, 0) }}
                </td>
                <td>
                    @if($item->expiry)
                        {{ \Carbon\Carbon::parse($item->expiry)->format('d/m/Y') }}
                    @else
                        --
                    @endif
                </td>
                <td>{{ $item->barcode ?? '--' }}</td>
            </tr>
            @endforeach

        </tbody>
    </table>

    <!-- FOOTER -->
<div class="footer">
    Ripoti imetolewa na {{ $company->company_name ?? 'Mfumo wa Mauzo' }} |
    Mmiliki: {{ $company->owner_name ?? '---' }} |
    {{ now()->format('d/m/Y H:i') }}
</div>


</body>
</html>
