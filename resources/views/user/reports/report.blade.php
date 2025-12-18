<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $reportType === 'sales' ? 'Ripoti ya Mauzo' : 'Ripoti ya Jumla' }} | {{ $date }}</title>
    <style>
        /* Base Styles */
        body { 
            font-family: 'DejaVu Sans', 'Segoe UI', Tahoma, Geneva, sans-serif; 
            font-size: 14px; 
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
            background-color: #fff;
        }
        
        /* Header Section */
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #2c3e50;
        }
        
        .header h1 {
            color: #2c3e50;
            margin: 0 0 10px 0;
            font-size: 24px;
            font-weight: 600;
        }
        
        .report-subtitle {
            color: #7f8c8d;
            font-size: 16px;
            margin-bottom: 5px;
        }
        
        .report-meta {
            color: #95a5a6;
            font-size: 13px;
            margin-top: 15px;
        }
        
        /* Table Styles */
        .report-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin: 25px 0;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .report-table thead {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            color: white;
        }
        
        .report-table th {
            padding: 14px 12px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
            border: none;
        }
        
        .report-table tbody tr {
            transition: background-color 0.2s ease;
        }
        
        .report-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .report-table tbody tr:hover {
            background-color: #e8f4fc;
        }
        
        .report-table td {
            padding: 12px;
            border: none;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .report-table td:first-child {
            border-left: 1px solid #e0e0e0;
        }
        
        .report-table td:last-child {
            border-right: 1px solid #e0e0e0;
        }
        
        .report-table tbody tr:last-child td {
            border-bottom: none;
        }
        
        /* Summary Section */
        .summary-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 8px;
            padding: 20px;
            margin: 30px 0;
            border-left: 4px solid #3498db;
        }
        
        .summary-title {
            color: #2c3e50;
            font-size: 16px;
            font-weight: 600;
            margin: 0 0 10px 0;
        }
        
        .summary-value {
            color: #2c3e50;
            font-size: 20px;
            font-weight: 700;
        }
        
        /* Footer */
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ecf0f1;
            text-align: center;
            color: #7f8c8d;
            font-size: 12px;
        }
        
        /* Utility Classes */
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-bold {
            font-weight: 700;
        }
        
        .metric-key {
            font-weight: 600;
            color: #2c3e50;
        }
        
        .metric-value {
            color: #27ae60;
            font-weight: 600;
        }
        
        /* Print Optimizations */
        @media print {
            body { padding: 10px; }
            .report-table { box-shadow: none; }
            .summary-section { break-inside: avoid; }
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>{{ $reportType === 'sales' ? 'Ripoti ya Mauzo' : 'Ripoti ya Jumla' }}</h1>
        <div class="report-subtitle">
            Kipindi: {{ ucfirst($timePeriod) }}
        </div>
        <div class="report-meta">
            Tarehe: {{ $date }} | 
            Kimechapishwa: {{ date('d/m/Y H:i') }}
        </div>
    </div>

    @if($reportType === 'sales')
        <!-- Sales Report -->
        <table class="report-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Bidhaa</th>
                    <th>Aina</th>
                    <th class="text-right">Idadi</th>
                    <th class="text-right">Bei (TZS)</th>
                    <th class="text-right">Jumla (TZS)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sales as $index => $sale)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-bold">{{ $sale->bidhaa->jina ?? 'N/A' }}</td>
                        <td>{{ $sale->bidhaa->aina ?? 'N/A' }}</td>
                        <td class="text-right">{{ number_format($sale->idadi) }}</td>
                        <td class="text-right">{{ number_format($sale->bei, 2) }}</td>
                        <td class="text-right text-bold">{{ number_format($sale->net_total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary-section">
            <div class="summary-title">Jumla ya Mauzo:</div>
            <div class="summary-value">
                TZS {{ number_format($sales->sum('net_total'), 2) }}
            </div>
            @if($sales->count() > 0)
                <div style="margin-top: 10px; color: #7f8c8d; font-size: 13px;">
                    Jumla ya bidhaa: {{ $sales->count() }} | 
                    Jumla ya vitengo: {{ number_format($sales->sum('idadi')) }}
                </div>
            @endif
        </div>

    @elseif($reportType === 'general')
        <!-- General Report -->
        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 60%;">Kipimo</th>
                    <th style="width: 40%;">Thamani</th>
                </tr>
            </thead>
            <tbody>
                @foreach($report as $key => $value)
                    <tr>
                        <td class="metric-key">{{ ucfirst(str_replace('_', ' ', $key)) }}</td>
                        <td class="metric-value">
                            @if(is_numeric($value))
                                {{ number_format($value, 2) }}
                                @if(in_array(strtolower($key), ['jumla', 'total', 'mauzo', 'sales']))
                                    TZS
                                @endif
                            @else
                                {{ $value }}
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        @if(count($report) > 0)
            <div class="summary-section">
                <div class="summary-title">Muhtasari wa Ripoti:</div>
                <div style="margin-top: 10px; color: #7f8c8d; font-size: 13px;">
                    Kipimo kimeonyeshwa: {{ count($report) }} | 
                    Tarehe ya usindikaji: {{ date('d/m/Y H:i') }}
                </div>
            </div>
        @endif
    @endif

    <div class="footer">
        <div>© {{ date('Y') }} - Ripoti Iliyochapishwa Kimtaratibu</div>
        <div style="margin-top: 5px; font-size: 11px;">
            Ukurasa 1 wa 1 | Imesasishwa: {{ date('d/m/Y H:i:s') }}
        </div>
    </div>

</body>
</html>