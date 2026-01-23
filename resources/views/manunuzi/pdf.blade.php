<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            font-size: 12px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #047857;
        }
        
        .header h1 {
            margin: 0;
            color: #047857;
            font-size: 20px;
            margin-bottom: 5px;
        }
        
        .header p {
            margin: 0;
            color: #6b7280;
            font-size: 11px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 11px;
        }
        
        th {
            background-color: #047857;
            color: white;
            font-weight: bold;
            text-align: left;
            padding: 6px;
            border: 1px solid #036346;
        }
        
        td {
            padding: 6px;
            border: 1px solid #d1d5db;
        }
        
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .summary {
            margin-top: 20px;
            padding: 10px;
            background-color: #f0fdf4;
            border: 1px solid #a7f3d0;
            border-radius: 4px;
            font-size: 11px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        
        .summary-label {
            font-weight: bold;
        }
        
        .summary-value {
            font-weight: bold;
            color: #047857;
        }
        
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #d1d5db;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
        }
        
        .no-data {
            text-align: center;
            padding: 30px;
            color: #9ca3af;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Tarehe: {{ $date }}</p>
    </div>
    
    @if(count($manunuzi) > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 15%;">Tarehe</th>
                    <th style="width: 25%;">Bidhaa</th>
                    <th style="width: 10%;" class="text-center">Idadi</th>
                    <th style="width: 15%;" class="text-right">Bei Nunua</th>
                    <th style="width: 15%;" class="text-right">Bei Uza</th>
                    <th style="width: 20%;">Saplaya</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalQuantity = 0;
                    $totalPurchase = 0;
                    $totalSelling = 0;
                @endphp
                
                @foreach($manunuzi as $index => $item)
                    @php
                        $bidhaa = $item->bidhaa ?? null;
                        $totalQuantity += $item->idadi;
                        $totalPurchase += $item->bei;
                        $totalSelling += ($bidhaa ? $bidhaa->bei_kuuza : 0) * $item->idadi;
                    @endphp
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y') }}</td>
                        <td>
                            @if($bidhaa)
                                {{ $bidhaa->jina }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="text-center">{{ $item->idadi }}</td>
                        <td class="text-right">
                            {{ number_format($item->bei, 0) }}
                        </td>
                        <td class="text-right">
                            @if($bidhaa)
                                {{ number_format($bidhaa->bei_kuuza * $item->idadi, 0) }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                            {{ $item->saplaya ?? '--' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="summary">
            <div class="summary-row">
                <span class="summary-label">Bidhaa Zilizonunuliwa:</span>
                <span class="summary-value">{{ number_format($totalQuantity, 0) }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Jumla ya Gharama:</span>
                <span class="summary-value">TZS {{ number_format($totalPurchase, 0) }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Thamani ya Kuuza:</span>
                <span class="summary-value">TZS {{ number_format($totalSelling, 0) }}</span>
            </div>
        </div>
    @else
        <div class="no-data">
            <p>Hakuna taarifa za manunuzi zilizopatikana.</p>
        </div>
    @endif
    
    <div class="footer">
        <p>Ilizalishwa: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>