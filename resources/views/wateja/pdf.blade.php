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
        
        .summary {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f9fafb;
            border-radius: 4px;
            font-size: 11px;
        }
        
        .summary-item {
            text-align: center;
            flex: 1;
        }
        
        .summary-label {
            color: #6b7280;
            margin-bottom: 4px;
        }
        
        .summary-value {
            font-weight: bold;
            color: #047857;
            font-size: 14px;
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
    
    <div class="summary">
        <div class="summary-item">
            <div class="summary-label">Jumla ya Wateja</div>
            <div class="summary-value">{{ $total_wateja }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Wateja wa Mwezi</div>
            <div class="summary-value">{{ $new_this_month }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Wateja wa Leo</div>
            <div class="summary-value">{{ $new_today }}</div>
        </div>
    </div>
    
    @if(count($wateja) > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 25%;">Jina</th>
                    <th style="width: 15%;">Simu</th>
                    <th style="width: 20%;">Barua Pepe</th>
                    <th style="width: 25%;">Anuani</th>
                    <th style="width: 15%;">Tarehe</th>
                </tr>
            </thead>
            <tbody>
                @foreach($wateja as $item)
                    <tr>
                        <td>{{ $item->jina }}</td>
                        <td>{{ $item->simu }}</td>
                        <td>{{ $item->barua_pepe ?: '--' }}</td>
                        <td>{{ $item->anapoishi ?: '--' }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">
            <p>Hakuna taarifa za wateja zilizopatikana.</p>
        </div>
    @endif
    
    <div class="footer">
        <p>Ilizalishwa: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }} | Ukurasa 1 wa 1</p>
    </div>
</body>
</html>