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

        .badge {
            display: inline-block;
            padding: 2px 4px;
            border-radius: 2px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-out-stock {
            background-color: #f8d7da;
            color: #721c24;
        }
        .badge-low-stock {
            background-color: #fff3cd;
            color: #856404;
        }
        .badge-in-stock {
            background-color: #d4edda;
            color: #155724;
        }
        .filter-info {
            background-color: #f8f9fa;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #dee2e6;
            font-size: 10px;
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
        </div>
    </div>

    <!-- FILTER INFO (if any) -->
    @if(isset($filter) && $filter)
    <div class="filter-info">
        <strong>Filter:</strong> 
        @switch($filter)
            @case('available')
                Bidhaa Zilizopo (idadi > 0)
                @break
            @case('low_stock')
                Zinazokaribia Kuisha (idadi < 10)
                @break
            @case('expired')
                Zilizo Expire
                @break
            @case('out_of_stock')
                Zilizoisha (idadi = 0)
                @break
            @default
                Bidhaa Zote
        @endswitch
        @if(isset($search) && $search)
            | <strong>Tafuta:</strong> {{ $search }}
        @endif
    </div>
    @endif

    <!-- SUMMARY STATISTICS -->
    <table class="summary-table">
        <tr>
            <td><strong>Jumla ya Bidhaa</strong></td>
            <td>{{ $total_count }}</td>
            <td><strong>Zilizoisha</strong></td>
            <td>{{ $outOfStockCount ?? $bidhaa->where('idadi', 0)->count() }}</td>
        </tr>
        <tr>
            <td><strong>Zilizopo</strong></td>
            <td>{{ $inStockCount ?? $bidhaa->where('idadi', '>', 0)->count() }}</td>
            <td><strong>Zinazokaribia Kuisha (&lt;10)</strong></td>
            <td>{{ $lowStockCount ?? $bidhaa->where('idadi', '<', 10)->where('idadi', '>', 0)->count() }}</td>
        </tr>
        <tr>
            <td><strong>Zilizo Expire</strong></td>
            <td>{{ $expiredCount ?? $bidhaa->where('expiry', '<', now())->count() }}</td>
            <td><strong>Zinazokaribia Expire</strong></td>
            <td>{{ $nearExpiryCount ?? $bidhaa->where('expiry', '>', now())->where('expiry', '<=', now()->addDays(30))->count() }}</td>
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

            @forelse($bidhaa as $index => $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->jina }}</td>
                <td>{{ $item->aina }}</td>
                <td>{{ $item->kipimo ?? '--' }}</td>
                <td class="text-center">
                    @php
                        $stockClass = '';
                        $stockText = number_format($item->idadi, 2);
                        
                        if ($item->idadi == 0) {
                            $stockClass = 'badge-out-stock';
                            $stockText = number_format($item->idadi, 2) . ' (Imeisha)';
                        } elseif ($item->idadi < 10) {
                            $stockClass = 'badge-low-stock';
                            $stockText = number_format($item->idadi, 2) . ' (Inakaribia)';
                        } else {
                            $stockClass = 'badge-in-stock';
                        }
                    @endphp
                    <span class="badge {{ $stockClass }}">{{ $stockText }}</span>
                </td>
                <td class="text-right">
                    {{ number_format($item->bei_nunua, 0) }}
                </td>
                <td class="text-right">
                    {{ number_format($item->bei_kuuza, 0) }}
                </td>
                <td>
                    @if($item->expiry)
                        @php
                            $expiryDate = \Carbon\Carbon::parse($item->expiry);
                            $today = \Carbon\Carbon::today();
                            $expiryClass = '';
                            
                            if ($expiryDate < $today) {
                                $expiryClass = 'badge-out-stock';
                                $expiryText = $expiryDate->format('d/m/Y') . ' (Imepita)';
                            } elseif ($expiryDate <= $today->addDays(30)) {
                                $expiryClass = 'badge-low-stock';
                                $expiryText = $expiryDate->format('d/m/Y') . ' (Siku ' . $today->diffInDays($expiryDate) . ')';
                            } else {
                                $expiryText = $expiryDate->format('d/m/Y');
                            }
                        @endphp
                        @if(isset($expiryClass))
                            <span class="badge {{ $expiryClass }}">{{ $expiryText }}</span>
                        @else
                            {{ $expiryText }}
                        @endif
                    @else
                        --
                    @endif
                </td>
                <td>{{ $item->barcode ?? '--' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center">Hakuna bidhaa zilizopatikana</td>
            </tr>
            @endforelse

        </tbody>
    </table>

    <!-- FOOTER WITH STATISTICS -->
    <div class="footer">
        <div style="float: left;">
            <strong>Muhtasari:</strong> 
            Jumla: {{ $total_count }} | 
            Zilizoisha: {{ $outOfStockCount ?? $bidhaa->where('idadi', 0)->count() }} | 
            Zilizopo: {{ $inStockCount ?? $bidhaa->where('idadi', '>', 0)->count() }} | 
            Low Stock: {{ $lowStockCount ?? $bidhaa->where('idadi', '<', 10)->where('idadi', '>', 0)->count() }}
        </div>
        <div style="float: right;">
            Ripoti imetolewa na {{ $company->company_name ?? 'Mfumo wa Mauzo' }} | 
            {{ now()->format('d/m/Y H:i') }}
        </div>
        <div style="clear: both;"></div>
    </div>

</body>
</html>