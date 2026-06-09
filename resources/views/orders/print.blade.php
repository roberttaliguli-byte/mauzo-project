<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $order->order_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; padding: 20px; }
        .invoice-container { max-width: 800px; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .company-name { font-size: 20px; font-weight: bold; }
        .order-number { font-size: 16px; font-weight: bold; margin: 10px 0; }
        .info-section { margin-bottom: 20px; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f5f5f5; }
        .text-right { text-align: right; }
        .total-section { margin-top: 20px; text-align: right; }
        .footer { text-align: center; margin-top: 30px; border-top: 1px solid #ddd; padding-top: 10px; font-size: 10px; }
        @media print { body { padding: 0; } .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="header">
            <div class="company-name">{{ $company->company_name ?? 'MAUZO SYSTEM' }}</div>
            <div>INVOICE</div>
            <div class="order-number">{{ $order->order_number }}</div>
        </div>
        
        <div class="info-section">
            <div class="info-row"><strong>Date:</strong> <span>{{ $order->created_at->format('d/m/Y H:i') }}</span></div>
            <div class="info-row"><strong>Customer:</strong> <span>{{ $order->customer_name }}</span></div>
            <div class="info-row"><strong>Phone:</strong> <span>{{ $order->customer_phone ?? '-' }}</span></div>
            <div class="info-row"><strong>Status:</strong> <span>{{ ucfirst($order->status) }}</span></div>
        </div>
        
        <table>
            <thead>
                <tr><th>#</th><th>Product</th><th class="text-right">Qty</th><th class="text-right">Price</th><th class="text-right">Discount</th><th class="text-right">Total</th></tr>
            </thead>
            <tbody>
                @foreach($order->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item['jina'] }}</td>
                    <td class="text-right">{{ number_format($item['idadi'], 2) }}</td>
                    <td class="text-right">{{ number_format($item['bei'], 0) }} TZS</td>
                    <td class="text-right">{{ number_format($item['punguzo'] ?? 0, 0) }} TZS</td>
                    <td class="text-right">{{ number_format($item['total'], 0) }} TZS</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="total-section">
            <div><strong>Subtotal:</strong> {{ number_format($order->subtotal, 0) }} TZS</div>
            @if($order->discount > 0)
            <div><strong>Discount:</strong> -{{ number_format($order->discount, 0) }} TZS</div>
            @endif
            <div style="font-size: 16px; margin-top: 5px;"><strong>TOTAL:</strong> {{ number_format($order->total, 0) }} TZS</div>
        </div>
        
        @if($order->notes)
        <div style="margin-top: 20px;"><strong>Notes:</strong> {{ $order->notes }}</div>
        @endif
        
        <div class="footer">
            <div>Thank you for your business!</div>
            <div>Generated on {{ now()->format('d/m/Y H:i') }}</div>
        </div>
        
        <div class="no-print" style="text-align: center; margin-top: 20px;">
            <button onclick="window.print()" style="padding: 8px 16px; margin: 0 5px;">Print</button>
            <button onclick="window.close()" style="padding: 8px 16px; margin: 0 5px;">Close</button>
        </div>
    </div>
    <script>window.onload = () => setTimeout(() => window.print(), 500);</script>
</body>
</html>