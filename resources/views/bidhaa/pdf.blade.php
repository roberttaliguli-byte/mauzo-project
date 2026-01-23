<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { margin: 0; font-size: 24px; }
        .header p { margin: 5px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #f3f4f6; text-align: left; padding: 12px 8px; font-size: 12px; border: 1px solid #ddd; }
        td { padding: 10px 8px; font-size: 11px; border: 1px solid #ddd; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .footer { margin-top: 40px; text-align: center; font-size: 11px; color: #666; }
        .badge { padding: 3px 8px; border-radius: 10px; font-size: 10px; }
        .badge-success { background-color: #d1fae5; color: #065f46; }
        .badge-warning { background-color: #fef3c7; color: #92400e; }
        .badge-danger { background-color: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Tarehe: {{ $date }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Bidhaa</th>
                <th>Aina</th>
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
                    <td class="text-center">
                        <span class="badge 
                            @if($item->idadi < 10 && $item->idadi > 0) badge-warning
                            @elseif($item->idadi == 0) badge-danger
                            @else badge-success @endif">
                            {{ $item->idadi }}
                        </span>
                    </td>
                    <td class="text-right">{{ number_format($item->bei_nunua, 0) }}</td>
                    <td class="text-right">{{ number_format($item->bei_kuuza, 0) }}</td>
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

    <div class="footer">
        <p>Jumla ya bidhaa: {{ count($bidhaa) }} | Ukurasa 1 wa 1</p>
        <p>Imetolewa na DEMODAY Bidhaa System</p>
    </div>
</body>
</html>