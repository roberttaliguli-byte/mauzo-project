<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        .subtitle {
            font-size: 14px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .summary {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 5px;
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

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Jina la Kampuni</th>
                <th>Mmiliki</th>
                <th>Simu</th>
                <th>Barua Pepe</th>
                <th>Mkoa</th>
                <th>Kifurushi</th>
                <th>Database</th>
                <th>Tarehe ya Usajili</th>
                <th>Imethibitishwa</th>
            </tr>
        </thead>
        <tbody>
            @foreach($companies as $index => $company)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $company->company_name }}</td>
                <td>{{ $company->owner_name }}</td>
                <td>{{ $company->phone }}</td>
                <td>{{ $company->email }}</td>
                <td>{{ $company->region }}</td>
                <td>{{ $company->package ?? 'Hakuna' }}</td>
                <td>{{ $company->database_name ?? 'Hakuna' }}</td>
                <td>{{ $company->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $company->is_verified ? 'Ndio' : 'Hapana' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Ukurasa wa {{ $title }} | Imeundwa na MAUZO System | {{ $date }}
    </div>
</body>
</html>