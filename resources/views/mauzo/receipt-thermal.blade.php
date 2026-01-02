<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $receipt_number }}</title>
    <style>
        /* Thermal printer friendly styling */
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            width: 58mm; /* Standard thermal paper width */
            margin: 0 auto;
            padding: 10px;
            background: white;
        }
        
        .receipt-header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
        }
        
        .receipt-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .receipt-company {
            font-size: 12px;
            color: #666;
        }
        
        .receipt-details {
            margin-bottom: 15px;
            font-size: 11px;
        }
        
        .receipt-details div {
            margin-bottom: 3px;
        }
        
        .receipt-items {
            margin: 15px 0;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 10px 0;
        }
        
        .receipt-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 11px;
        }
        
        .receipt-item .name {
            flex: 2;
            word-break: break-word;
        }
        
        .receipt-item .amount {
            flex: 1;
            text-align: right;
        }
        
        .receipt-total {
            text-align: right;
            font-weight: bold;
            font-size: 14px;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 2px solid #000;
        }
        
        .receipt-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 10px;
            color: #666;
            line-height: 1.4;
        }
        
        @media print {
            body {
                width: 58mm;
                margin: 0;
                padding: 5px;
            }
            
            button {
                display: none;
            }
        }
        
        button {
            width: 100%;
            padding: 10px;
            margin-top: 20px;
            background: #10B981;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
        }
        
        button:hover {
            background: #059669;
        }
    </style>
</head>
<body>
    <div class="receipt-header">
        <div class="receipt-title">{{ $company_name }}</div>
        <div class="receipt-company">Karibu Tena</div>
    </div>
    
    <div class="receipt-details">
        <div>Tarehe: {{ \Carbon\Carbon::parse($date)->format('d/m/Y H:i') }}</div>
        <div>Risiti: {{ $receipt_number }}</div>
        <div>Kass: {{ $user_name }}</div>
    </div>
    
    <div class="receipt-items">
        <div class="receipt-item">
            <div class="name">{{ $mauzo->bidhaa->jina }}</div>
            <div class="amount">{{ $mauzo->idadi }} x {{ number_format($mauzo->bei) }}</div>
        </div>
        <div class="receipt-item">
            <div class="name"></div>
            <div class="amount">{{ number_format($mauzo->jumla) }}</div>
        </div>
    </div>
    
    @if($mauzo->punguzo > 0)
    <div class="receipt-item">
        <div class="name">Punguzo:</div>
        <div class="amount">-{{ number_format($mauzo->punguzo) }}</div>
    </div>
    
    <div class="receipt-total">
        Kulipwa: {{ number_format($mauzo->jumla) }}
    </div>
    @else
    <div class="receipt-total">
        Jumla: {{ number_format($mauzo->jumla) }}
    </div>
    @endif
    
    <div class="receipt-footer">
        <div>Asante kwa Kununua!</div>
        <div>Mauzo hayarudishwi</div>
        <div>Simu: 0757XXXXXX</div>
    </div>
    
    <button onclick="window.print()">
        <i class="fas fa-print"></i> Print Risiti
    </button>
    
    <script>
        // Auto-print after 1 second
        setTimeout(() => {
            window.print();
        }, 1000);
    </script>
</body>
</html>