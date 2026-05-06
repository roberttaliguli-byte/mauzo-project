{{-- resources/views/mengineyo/pdf-banking.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>Taarifa za Benki - {{ date('d/m/Y') }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f3f4f6; }
        .header { text-align: center; margin-bottom: 30px; }
        .total { font-weight: bold; background-color: #f8f9fa; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Taarifa za Uwekaji Benki</h2>
        <p>Ripoti ya Uwekaji Benki - {{ date('d/m/Y H:i') }}</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Tarehe</th>
                <th>Benki</th>
                <th>Maelezo</th>
                <th class="text-right">Kiasi (TZS)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
            <tr>
                <td>{{ $item->tarehe->format('d/m/Y') }}</td>
                <td>{{ $item->benki }}</td>
                <td>{{ $item->maelezo ?? '--' }}</td>
                <td class="text-right">{{ number_format($item->kiasi, 0) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total">
                <td colspan="3"><strong>Jumla</strong></td>
                <td class="text-right"><strong>{{ number_format($total, 0) }}</strong></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>