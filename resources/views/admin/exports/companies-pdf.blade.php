<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        @page {
            margin: 15px;
            size: A4;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
            line-height: 1.2;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 1px solid #333;
            padding-bottom: 8px;
        }
        .title {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        .subtitle {
            font-size: 11px;
            color: #666;
            margin: 2px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            table-layout: fixed;
            word-wrap: break-word;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 4px 3px;
            text-align: left;
            font-size: 9px;
            line-height: 1.1;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
            padding: 5px 3px;
        }
        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 8px;
        }
        .summary {
            margin-bottom: 10px;
            padding: 6px 8px;
            background-color: #f9f9f9;
            border-radius: 3px;
            font-size: 9px;
        }
        .badge-blue {
            background: #e8f0ff;
            padding: 1px 4px;
            border-radius: 2px;
            color: #1a56db;
            font-size: 8px;
            border: 1px solid #bbcef8;
            white-space: nowrap;
        }
        .badge-orange {
            background: #fff4e5;
            padding: 1px 4px;
            border-radius: 2px;
            color: #c77820;
            font-size: 8px;
            border: 1px solid #f4d0a8;
            white-space: nowrap;
        }
        .badge-red {
            background: #fdecea;
            padding: 1px 4px;
            border-radius: 2px;
            color: #a9372c;
            font-size: 8px;
            border: 1px solid #f5b5ad;
            white-space: nowrap;
        }
        .badge-gray {
            background: #eeeeee;
            padding: 1px 4px;
            border-radius: 2px;
            color: #555;
            font-size: 8px;
            border: 1px solid #ccc;
            white-space: nowrap;
        }
        
        /* Column widths for A4 optimization */
        .col-no { width: 25px; }
        .col-company { width: 80px; }
        .col-owner { width: 70px; }
        .col-phone { width: 60px; }
        .col-email { width: 90px; }
        .col-region { width: 50px; }
        .col-package { width: 60px; }
        .col-days { width: 50px; }
        .col-date { width: 60px; }
        .col-status { width: 40px; }
        
        /* Ensure text breaks properly */
        td {
            word-break: break-word;
            overflow-wrap: break-word;
        }
        
        /* Compact layout */
        .compact-table th,
        .compact-table td {
            padding: 3px 2px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">MAUZO - {{ $title }}</div>
        <div class="subtitle">Imetolewa: {{ $date }}</div>
        <div class="subtitle">Jumla ya Makampuni: {{ $total }}</div>
    </div>

    <div class="summary">
        <strong>Muhtasari:</strong> Orodha ya makampuni yote yaliyosajiliwa kwa mujibu wa kipindi kilichochaguliwa
    </div>

    <table class="compact-table">
        <thead>
            <tr>
                <th class="col-no">#</th>
                <th class="col-company">Jina la Kampuni</th>
                <th class="col-owner">Mmiliki</th>
                <th class="col-phone">Simu</th>
                <th class="col-email">Barua Pepe</th>
                <th class="col-region">Mkoa</th>
                <th class="col-package">Kifurushi</th>
                <th class="col-days">Siku Zilizobaki</th>
                <th class="col-date">Tarehe ya Usajili</th>
                <th class="col-status">Imethibitishwa</th>
            </tr>
        </thead>
        <tbody>
            @foreach($companies as $index => $company)
            <tr>
                <td class="col-no">{{ $index + 1 }}</td>
                <td class="col-company">{{ $company->company_name }}</td>
                <td class="col-owner">{{ $company->owner_name }}</td>
                <td class="col-phone">{{ $company->phone }}</td>
                <td class="col-email">{{ $company->email }}</td>
                <td class="col-region">{{ $company->region }}</td>
                <td class="col-package">{{ $company->package ?? 'Hakuna' }}</td>

                <!-- Remaining Days -->
                <td class="col-days">
                    @php
                        $remaining = null;

                        if ($company->package && $company->package_start && $company->package_end) {
                            // Use the actual package_end date from database for accurate calculation
                            $today = \Carbon\Carbon::today();
                            $endDate = \Carbon\Carbon::parse($company->package_end)->startOfDay();
                            $remaining = $today->diffInDays($endDate, false);
                        }
                    @endphp

                    @if(!$company->package)
                        <span class="badge-gray">Hakuna</span>
                    @elseif($remaining > 0)
                        <span class="badge-blue">Siku {{ $remaining }}</span>
                    @elseif($remaining === 0)
                        <span class="badge-orange">Inaisha Leo</span>
                    @else
                        <span class="badge-red">Kimeisha</span>
                    @endif
                </td>

                <!-- Created at -->
                <td class="col-date">{{ $company->created_at->format('d/m/Y') }}</td>

                <!-- Verification -->
                <td class="col-status">{{ $company->is_verified ? 'Ndio' : 'Hapana' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Ukurasa wa {{ $title }} | Imeundwa na MAUZO System | {{ $date }}
    </div>
</body>
</html>