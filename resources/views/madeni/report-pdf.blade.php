<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <title>Ripoti ya Madeni</title>

    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            margin: 20px;
        }

        h1, h2, p {
            margin: 0;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #059669;
            padding-bottom: 10px;
        }

        .header h1 {
            font-size: 22px;
            color: #059669;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 12px;
            color: #555;
        }

        /* Summary Section */
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .summary-table td {
            width: 33.33%;
            padding: 10px;
        }

        .summary-box {
            padding: 12px;
            border-radius: 4px;
            color: #fff;
        }

        .bg-green { background: #059669; }
        .bg-blue { background: #2563eb; }
        .bg-red { background: #dc2626; }

        .summary-label {
            font-size: 11px;
            margin-bottom: 5px;
        }

        .summary-value {
            font-size: 16px;
            font-weight: bold;
        }

        /* Main Table */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #059669;
            color: #fff;
            padding: 6px;
            font-size: 11px;
            text-align: left;
        }

        td {
            padding: 6px;
            border: 1px solid #ddd;
        }

        tbody tr:nth-child(even) {
            background: #f9f9f9;
        }

        tfoot td {
            background: #059669;
            color: #fff;
            font-weight: bold;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-red { color: #dc2626; }

        .badge {
            padding: 2px 6px;
            font-size: 10px;
            border-radius: 3px;
            font-weight: bold;
        }

        .paid {
            background: #d1fae5;
            color: #065f46;
        }

        .active {
            background: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>

<body>

    <!-- Header -->
    <div class="header">
      
        <h1><strong>RIPOTI YA MADENI</strong></h1>
        <p>{{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <!-- Summary -->
    <table class="summary-table">
        <tr>
            <td>
                <div class="summary-box bg-green">
                    <div class="summary-label">Jumla ya Madeni</div>
                    <div class="summary-value">TZS {{ number_format($totalAmount, 2) }}</div>
                </div>
            </td>
            <td>
                <div class="summary-box bg-blue">
                    <div class="summary-label">Jumla ya Malipo</div>
                    <div class="summary-value">TZS {{ number_format($totalPaid, 2) }}</div>
                </div>
            </td>
            <td>
                <div class="summary-box bg-red">
                    <div class="summary-label">Baki</div>
                    <div class="summary-value">TZS {{ number_format($totalBalance, 2) }}</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Report Table -->
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Tarehe</th>
                <th>Mkopaji</th>
                <th>Simu</th>
                <th>Bidhaa</th>
                <th class="text-center">Idadi</th>
                <th class="text-right">Jumla</th>
                <th class="text-right">Malipo</th>
                <th class="text-right">Baki</th>
                <th class="text-center">Hali</th>
            </tr>
        </thead>
        <tbody>
            @php $counter = 1; @endphp

            @forelse($debts as $debt)
                @php $paid = $debt->jumla - $debt->baki; @endphp
                <tr>
                    <td>{{ $counter++ }}</td>
                    <td>{{ $debt->created_at->format('d/m/Y') }}</td>
                    <td>{{ $debt->jina_mkopaji }}</td>
                    <td>{{ $debt->simu ?? 'N/A' }}</td>
                    <td>{{ $debt->bidhaa->jina ?? 'N/A' }}</td>
                    <td class="text-center">{{ $debt->idadi }}</td>
                    <td class="text-right">{{ number_format($debt->jumla, 2) }}</td>
                    <td class="text-right">{{ number_format($paid, 2) }}</td>
                    <td class="text-right {{ $debt->baki > 0 ? 'text-red' : '' }}">
                        {{ number_format($debt->baki, 2) }}
                    </td>
                    <td class="text-center">
                        @if($debt->baki <= 0)
                            <span class="badge paid">Imelipwa</span>
                        @else
                            <span class="badge active">Inaendelea</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center">
                        Hakuna data ya madeni katika kipindi hiki
                    </td>
                </tr>
            @endforelse
        </tbody>

        <tfoot>
            <tr>
                <td colspan="6" class="text-right">JUMLA:</td>
                <td class="text-right">{{ number_format($totalAmount, 2) }}</td>
                <td class="text-right">{{ number_format($totalPaid, 2) }}</td>
                <td class="text-right">{{ number_format($totalBalance, 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

</body>
</html>
