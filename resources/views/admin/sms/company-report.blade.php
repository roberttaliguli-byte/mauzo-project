{{-- resources/views/admin/sms/pdf/company-report.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ripoti ya SMS - {{ $company->company_name }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 30px; }
        .company-info { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Ripoti ya SMS</h2>
        <h3>{{ $company->company_name }}</h3>
        <p>Tarehe: {{ now()->format('d/m/Y H:i') }}</p>
    </div>
    
    <div class="company-info">
        <p><strong>Jumla ya SMS:</strong> {{ number_format($totalSent) }}</p>
        <p><strong>Zilizofanikiwa:</strong> {{ number_format($totalDelivered) }}</p>
        <p><strong>Zilizoshindwa:</strong> {{ number_format($totalFailed) }}</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Namba</th>
                <th>Ujumbe</th>
                <th>Hali</th>
                <th>Tarehe</th>
            </tr>
        </thead>
        <tbody>
            @foreach($smsLogs as $log)
            <tr>
                <td>{{ $log->recipient }}</td>
                <td>{{ Str::limit($log->message, 100) }}</td>
                <td>{{ $log->status }}</td>
                <td>{{ $log->sent_at ? $log->sent_at->format('d/m/Y H:i') : '--' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        <p>Ripoti hii imetengenezwa na mfumo wa MAUZO</p>
    </div>
</body>
</html>