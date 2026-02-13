<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #047857;
            padding-bottom: 15px;
        }
        .header h1 {
            color: #047857;
            margin: 0 0 5px 0;
            font-size: 24px;
        }
        .header p {
            color: #6b7280;
            margin: 0;
            font-size: 14px;
        }
        .summary {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f3f4f6;
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #047857;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 12px;
        }
        td {
            border: 1px solid #e5e7eb;
            padding: 8px;
            font-size: 11px;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            color: #6b7280;
            font-size: 10px;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .badge {
            padding: 3px 8px;
            border-radius: 9999px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-green { background-color: #d1fae5; color: #065f46; }
        .badge-yellow { background-color: #fef3c7; color: #92400e; }
        .badge-gray { background-color: #f3f4f6; color: #374151; }
        .badge-red { background-color: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>{{ $company }} | Tarehe: {{ $date }}</p>
        <p style="margin-top: 5px; color: #047857;">Jumla ya Bidhaa: {{ $total_count }}</p>
    </div>

    <div class="summary">
        <h3 style="margin-top: 0; margin-bottom: 10px; color: #374151;">Muhtasari</h3>
        <table style="width: auto; margin-top: 0;">
            <tr>
                <td style="border: none; padding: 5px;"><strong>Jumla ya Bidhaa:</strong></td>
                <td style="border: none; padding: 5px;">{{ $total_count }}</td>
                <td style="border: none; padding: 5px;"><strong>Zilizoisha (Idadi=0):</strong></td>
                <td style="border: none; padding: 5px;">{{ $bidhaa->where('idadi', 0)->count() }}</td>
            </tr>
            <tr>
                <td style="border: none; padding: 5px;"><strong>Zilizopo:</strong></td>
                <td style="border: none; padding: 5px;">{{ $bidhaa->where('idadi', '>', 0)->count() }}</td>
                <td style="border: none; padding: 5px;"><strong>Zinazokaribia Kuisha:</strong></td>
                <td style="border: none; padding: 5px;">{{ $bidhaa->where('idadi', '<', 10)->where('idadi', '>', 0)->count() }}</td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Bidhaa</th>
                <th>Aina</th>
                <th>Kipimo</th>
                <th class="text-center">Idadi</th>
                <th class="text-right">Bei Nunua</th>
                <th class="text-right">Bei Kuuza</th>
                <th>Expiry</th>
                <th>Barcode</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bidhaa as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->jina }}</td>
                <td>{{ $item->aina }}</td>
                <td>{{ $item->kipimo ?? '--' }}</td>
                <td class="text-center">
                    @if($item->idadi == 0)
                        <span class="badge badge-gray">0 (Imeisha)</span>
                    @elseif($item->idadi < 10)
                        <span class="badge badge-yellow">{{ $item->idadi }}</span>
                    @else
                        <span class="badge badge-green">{{ $item->idadi }}</span>
                    @endif
                </td>
                <td class="text-right">{{ number_format($item->bei_nunua, 0) }} TZS</td>
                <td class="text-right">{{ number_format($item->bei_kuuza, 0) }} TZS</td>
                <td>
                    @if($item->expiry)
                        @if($item->expiry < now())
                            <span style="color: #991b1b;">{{ \Carbon\Carbon::parse($item->expiry)->format('d/m/Y') }}</span>
                        @else
                            {{ \Carbon\Carbon::parse($item->expiry)->format('d/m/Y') }}
                        @endif
                    @else
                        --
                    @endif
                </td>
                <td>{{ $item->barcode ?? '--' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Imetolewa na {{ $company }} | {{ $date }} | PDF imeundwa kwa bidhaa zote {{ $total_count }}</p>
    </div>
</body>
</html>