<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Orodha ya Wafanyakazi - {{ $company->name ?? 'Kampuni' }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .company-name { font-size: 18px; font-weight: bold; color: #333; }
        .report-title { font-size: 16px; color: #666; }
        .table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table th { background-color: #2d3748; color: white; padding: 8px; text-align: left; }
        .table td { padding: 8px; border: 1px solid #ddd; }
        .table tr:nth-child(even) { background-color: #f8f9fa; }
        .footer { margin-top: 20px; text-align: center; font-size: 10px; color: #666; }
        .status-active { color: green; font-weight: bold; }
        .status-suspended { color: red; font-weight: bold; }
        .summary { margin-bottom: 15px; padding: 10px; background-color: #f0f0f0; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">{{ $company->name ?? 'Kampuni Yangu' }}</div>
        <div class="report-title">ORODHA YA WAFANYAKAZI</div>
        <div>Tarehe: {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <div class="summary">
        Jumla ya Wafanyakazi: <strong>{{ $wafanyakazi->count() }}</strong> |
        Wanaume: <strong>{{ $wafanyakazi->where('jinsia', 'Mwanaume')->count() }}</strong> |
        Wanawake: <strong>{{ $wafanyakazi->where('jinsia', 'Mwanamke')->count() }}</strong> |
        Wanaoweza Kuingia: <strong>{{ $wafanyakazi->where('getini', 'ingia')->count() }}</strong>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Jina</th>
                <th>Simu</th>
                <th>Jinsia</th>
                <th>Barua Pepe</th>
                <th>Anuani</th>
                <th>Hali</th>
                <th>Tarehe Kuzaliwa</th>
            </tr>
        </thead>
        <tbody>
            @foreach($wafanyakazi as $index => $mfanyakazi)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $mfanyakazi->jina }}</td>
                <td>{{ $mfanyakazi->simu ?? '--' }}</td>
                <td>{{ $mfanyakazi->jinsia }}</td>
                <td>{{ $mfanyakazi->barua_pepe ?? '--' }}</td>
                <td>{{ $mfanyakazi->anuani ?? '--' }}</td>
                <td class="status-{{ $mfanyakazi->getini === 'ingia' ? 'active' : 'suspended' }}">
                    {{ ucfirst($mfanyakazi->getini) }}
                </td>
                <td>{{ $mfanyakazi->tarehe_kuzaliwa ? \Carbon\Carbon::parse($mfanyakazi->tarehe_kuzaliwa)->format('d/m/Y') : '--' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Imetoka kwenye Mfumo wa Usimamizi wa Wafanyakazi | 
        Ukurasa <span class="pageNumber"></span> wa <span class="totalPages"></span>
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $text = "Ukurasa {PAGE_NUM} wa {PAGE_COUNT}";
            $size = 8;
            $font = $fontMetrics->getFont("DejaVu Sans");
            $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
            $x = ($pdf->get_width() - $width) / 2;
            $y = $pdf->get_height() - 35;
            $pdf->page_text($x, $y, $text, $font, $size);
        }
    </script>
</body>
</html>