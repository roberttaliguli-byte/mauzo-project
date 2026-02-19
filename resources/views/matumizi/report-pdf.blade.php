<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <title>Ripoti ya Matumizi</title>

    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #333;
            margin: 15px;
        }

        h1, h2, p {
            margin: 0;
        }

        /* Header */
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }

        .header h1 {
            font-size: 17px;
            font-weight: bold;
        }

        .header h2 {
            font-size: 13px;
            font-weight: normal;
        }

        .header p {
            font-size: 9px;
            color: #555;
        }

        /* Company Info */
        .box {
            border: 1px solid #ddd;
            padding: 6px;
            margin-bottom: 12px;
            background: #f7f7f7;
        }

        .box table {
            width: 100%;
            border-collapse: collapse;
        }

        .box td {
            padding: 3px 5px;
            border: none;
        }

        .label {
            font-weight: bold;
            width: 90px;
        }

        /* Filter Info */
        .filter-box {
            margin-bottom: 12px;
            padding: 6px;
            border-left: 3px solid #333;
            background: #fafafa;
            font-size: 9px;
        }

        /* Summary Table (PDF Safe instead of flex) */
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .summary-table td {
            width: 25%;
            border: 1px solid #ddd;
            padding: 6px;
            background: #f9f9f9;
        }

        .summary-label {
            font-size: 8px;
            color: #666;
            margin-bottom: 2px;
        }

        .summary-value {
            font-size: 12px;
            font-weight: bold;
        }

        /* Section Title */
        .section-title {
            font-weight: bold;
            font-size: 11px;
            border-bottom: 1px solid #333;
            margin: 12px 0 6px 0;
            padding-bottom: 2px;
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        th {
            background: #333;
            color: #fff;
            padding: 5px;
            font-size: 9px;
            text-align: left;
        }

        td {
            padding: 4px;
            border: 1px solid #ddd;
        }

        tbody tr:nth-child(even) {
            background: #f9f9f9;
        }

        tfoot td {
            font-weight: bold;
            background: #f0f0f0;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }

        /* Footer */
        .footer {
            text-align: center;
            font-size: 8px;
            color: #666;
            margin-top: 15px;
            border-top: 1px solid #ddd;
            padding-top: 5px;
        }
    </style>
</head>

<body>

    <!-- Header -->
    <div class="header">
        <h1>{{ $company->company_name ?? 'MFUMO WA MATUMIZI' }}</h1>
        <h2>RIPOTI YA MATUMIZI</h2>
        <p>
            {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}
            -
            {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
        </p>
    </div>

    <!-- Company Info -->
    <div class="box">
        <table>
            <tr>
                <td class="label">Kampuni:</td>
                <td>{{ $company->company_name ?? 'N/A' }}</td>
                <td class="label">Simu:</td>
                <td>{{ $company->phone ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Eneo:</td>
                <td>{{ $company->location ?? 'N/A' }}, {{ $company->region ?? 'N/A' }}</td>
                <td class="label">Email:</td>
                <td>{{ $company->email ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <!-- Filter Info -->
    <div class="filter-box">
        <strong>Kipindi:</strong>
        {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}
        -
        {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
        |
        <strong>Aina:</strong>
        @if($reportType == 'all') Zote
        @elseif($reportType == 'daily') Kwa Siku
        @elseif($reportType == 'monthly') Kwa Mwezi
        @else Kwa Mwaka
        @endif
        |
        <strong>Jumla:</strong> {{ $count }} matumizi
    </div>

    <!-- Summary -->
    <table class="summary-table">
        <tr>
            <td>
                <div class="summary-label">Jumla ya Matumizi</div>
                <div class="summary-value">{{ number_format($total, 0) }} TZS</div>
            </td>
            <td>
                <div class="summary-label">Idadi</div>
                <div class="summary-value">{{ $count }}</div>
            </td>
            <td>
                <div class="summary-label">Wastani</div>
                <div class="summary-value">{{ number_format($average, 0) }} TZS</div>
            </td>
            <td>
                <div class="summary-label">Kubwa Zaidi</div>
                <div class="summary-value">{{ number_format($max, 0) }} TZS</div>
            </td>
        </tr>
    </table>

    <!-- Category Summary -->
    <div class="section-title">MUHTASARI KWA AINA</div>
    <table>
        <thead>
            <tr>
                <th>Aina</th>
                <th class="text-right">Idadi</th>
                <th class="text-right">Jumla (TZS)</th>
                <th class="text-right">Asilimia</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categoryData as $category => $amount)
            <tr>
                <td>{{ $category }}</td>
                <td class="text-right">{{ $matumizi->where('aina', $category)->count() }}</td>
                <td class="text-right">{{ number_format($amount, 0) }}</td>
                <td class="text-right">
                    {{ $total > 0 ? number_format(($amount / $total) * 100, 1) : 0 }}%
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td class="text-right">Jumla:</td>
                <td class="text-right">{{ $count }}</td>
                <td class="text-right">{{ number_format($total, 0) }}</td>
                <td class="text-right">100%</td>
            </tr>
        </tfoot>
    </table>

    <!-- Detailed Table -->
    <div class="section-title">MATUMIZI KWA KINA</div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Tarehe</th>
                <th>Aina</th>
                <th>Maelezo</th>
                <th class="text-right">Gharama (TZS)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($matumizi as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i') }}</td>
                <td>{{ $item->aina }}</td>
                <td>{{ $item->maelezo ?: '--' }}</td>
                <td class="text-right">{{ number_format($item->gharama, 0) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-right">Jumla:</td>
                <td class="text-right">{{ number_format($total, 0) }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- Footer -->
    <div class="footer">
        Imechapishwa:
        {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}
        |
        {{ $company->company_name ?? 'Mfumo wa Matumizi' }}
    </div>

</body>
</html>
