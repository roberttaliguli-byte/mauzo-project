{{-- resources/views/user/daily_reports/pdf.blade.php --}}
<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $report_title ?? 'Ripoti' }} - {{ $company_name ?? 'Biashara' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.3;
            color: #000000;
            background: #ffffff;
            padding: 15px;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 1px solid #000;
            padding-bottom: 8px;
        }

        .company-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .report-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .report-subtitle {
            font-size: 11px;
            margin-bottom: 3px;
            color: #333;
        }

        .report-period {
            font-size: 10px;
            margin-bottom: 3px;
        }

        .report-date {
            font-size: 9px;
            text-align: center;
            margin-bottom: 10px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 9px;
        }

        .data-table th {
            background: #f0f0f0;
            font-weight: bold;
            padding: 8px 5px;
            border: 1px solid #000;
            text-align: left;
        }

        .data-table td {
            padding: 6px 5px;
            border: 1px solid #000;
        }

        .data-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }

        .grand-total {
            margin-top: 15px;
            padding: 10px;
            border: 2px solid #000;
            background: #f0f0f0;
            font-weight: bold;
            text-align: center;
            font-size: 12px;
        }

        .footer {
            margin-top: 20px;
            padding-top: 5px;
            border-top: 1px solid #000;
            text-align: center;
            font-size: 8px;
        }

        .page-break-avoid {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">{{ $company_name ?? 'Biashara' }}</div>
        <div class="report-title">{{ $report_title ?? 'Ripoti' }}</div>
        <div class="report-subtitle">{{ $group_by_label ?? '' }}</div>
        <div class="report-period">
            Kipindi: {{ $date_range_label ?? '' }}
            @if(isset($display_from) && isset($display_to))
                <br><small>{{ $display_from }} - {{ $display_to }}</small>
            @endif
        </div>
        <div class="report-date">
            Tarehe ya utengenezaji: {{ $generated_at ?? now()->format('d/m/Y H:i:s') }}
        </div>
    </div>

    @if($report_sub_type === 'mauzo')
        <table class="data-table">
            <thead>
                <tr>
                    <th>Kipindi</th>
                    <th class="text-right">Jumla ya Mauzo</th>
                    <th class="text-right">Marejesho ya Madeni</th>
                    <th class="text-right">Jumla ya Mapato</th>
                    <th class="text-center">Idadi ya Mauzo</th>
                </tr>
            </thead>
            <tbody>
                @foreach($grouped_data as $group)
                <tr>
                    <td>{{ $group['period_label'] }}</td>
                    <td class="text-right">{{ number_format($group['data']['total_sales'], 2) }}</td>
                    <td class="text-right">{{ number_format($group['data']['total_repayments'], 2) }}</td>
                    <td class="text-right">{{ number_format($group['data']['total_income'], 2) }}</td>
                    <td class="text-center">{{ $group['data']['sales_count'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="grand-total">
            JUMLA KUU YA MAPATO: {{ number_format($grand_totals['total_income'] ?? 0, 2) }} TZS
        </div>

    @elseif($report_sub_type === 'faida')
        <table class="data-table">
            <thead>
                <tr>
                    <th>Kipindi</th>
                    <th class="text-right">Faida ya Mauzo</th>
                    <th class="text-right">Faida ya Marejesho</th>
                    <th class="text-right">Jumla ya Faida</th>
                    <th class="text-right">Matumizi</th>
                    <th class="text-right">Faida Halisi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($grouped_data as $group)
                <tr>
                    <td>{{ $group['period_label'] }}</td>
                    <td class="text-right">{{ number_format($group['data']['sales_profit'], 2) }}</td>
                    <td class="text-right">{{ number_format($group['data']['repayment_profit'], 2) }}</td>
                    <td class="text-right">{{ number_format($group['data']['total_profit'], 2) }}</td>
                    <td class="text-right">{{ number_format($group['data']['expenses'], 2) }}</td>
                    <td class="text-right">{{ number_format($group['data']['net_profit'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="grand-total">
            JUMLA YA FAIDA HALISI: {{ number_format($grand_totals['net_profit'] ?? 0, 2) }} TZS
        </div>

    @elseif($report_sub_type === 'biashara')
        <table class="data-table">
            <thead>
                <tr>
                    <th>Kipindi</th>
                    <th class="text-right">Jumla ya Mapato</th>
                    <th class="text-right">Mauzo</th>
                    <th class="text-center">Idadi ya Mauzo</th>
                    <th class="text-right">Faida</th>
                    <th class="text-right">Matumizi</th>
                    <th class="text-right">Faida Halisi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($grouped_data as $group)
                <tr>
                    <td>{{ $group['period_label'] }}</td>
                    <td class="text-right">{{ number_format($group['data']['total_income'], 2) }}</td>
                    <td class="text-right">{{ number_format($group['data']['total_sales'], 2) }}</td>
                    <td class="text-center">{{ $group['data']['sales_count'] }}</td>
                    <td class="text-right">{{ number_format($group['data']['total_profit'], 2) }}</td>
                    <td class="text-right">{{ number_format($group['data']['expenses'], 2) }}</td>
                    <td class="text-right">{{ number_format($group['data']['net_profit'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="grand-total">
            Jumla ya Mapato: {{ number_format($grand_totals['total_income'] ?? 0, 2) }} TZS | 
            Jumla ya Faida Halisi: {{ number_format($grand_totals['net_profit'] ?? 0, 2) }} TZS
        </div>

    @elseif($report_sub_type === 'matumizi')
        <table class="data-table">
            <thead>
                <tr>
                    <th>Kipindi</th>
                    <th class="text-right">Jumla ya Matumizi</th>
                    <th class="text-center">Idadi ya Matumizi</th>
                    <th>Matumizi kwa Aina</th>
                </tr>
            </thead>
            <tbody>
                @foreach($grouped_data as $group)
                <tr>
                    <td>{{ $group['period_label'] }}</td>
                    <td class="text-right">{{ number_format($group['data']['total_expenses'], 2) }}</td>
                    <td class="text-center">{{ $group['data']['expenses_count'] }}</td>
                    <td>
                        @foreach($group['data']['expenses_by_category'] ?? [] as $category => $amount)
                            <span style="display: inline-block; background: #f0f0f0; padding: 2px 4px; margin: 2px; font-size: 8px;">{{ $category }}: {{ number_format($amount, 2) }}</span>
                        @endforeach
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="grand-total">
            JUMLA KUU YA MATUMIZI: {{ number_format($grand_totals['total_expenses'] ?? 0, 2) }} TZS
        </div>
    @endif

    <div class="footer">
        <div>{{ $company_name ?? 'Biashara' }} &copy; {{ date('Y') }}</div>
        <div>Imechapishwa: {{ now()->format('d/m/Y H:i:s') }}</div>
    </div>
</body>
</html>