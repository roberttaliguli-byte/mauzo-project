<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ripoti ya Jumla</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
    </style>
</head>
<body>
    <h2>Ripoti ya Jumla - {{ ucfirst($timePeriod) }}</h2>
    <p>Tarehe: {{ $date }}</p>

    <table>
        <thead>
            <tr>
                <th>Metric</th>
                <th>Value</th>
            </tr>
        </thead>
        <tbody>
            <tr><td>Jumla Bidhaa</td><td>{{ $report['jumlaBidhaa'] }}</td></tr>
            <tr><td>Jumla Idadi</td><td>{{ $report['jumlaIdadi'] }}</td></tr>
            <tr><td>Thamani</td><td>{{ number_format($report['thamani'], 2) }}</td></tr>
            <tr><td>Jumla Mauzo</td><td>{{ number_format($report['jumlaMauzo'], 2) }}</td></tr>
            <tr><td>Jumla Manunuzi</td><td>{{ number_format($report['jumlaManunuzi'], 2) }}</td></tr>
            <tr><td>Jumla Matumizi</td><td>{{ number_format($report['jumlaMatumizi'], 2) }}</td></tr>
            <tr><td>Faida Halisi</td><td>{{ number_format($report['faidaHalisi'], 2) }}</td></tr>
            <tr><td>Jumla Madeni</td><td>{{ number_format($report['jumlaMadeni'], 2) }}</td></tr>
            <tr><td>Idadi Madeni</td><td>{{ $report['idadiMadeni'] }}</td></tr>
        </tbody>
    </table>
</body>
</html>
