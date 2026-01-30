<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Risiti {{ $receiptNo }}</title>
    <style>
        @media print {
            body {
                width: 80mm;
                margin: 0;
                padding: 0;
                font-family: "Courier New", monospace;
                font-size: 12px;
                line-height: 1.2;
            }
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            .no-print {
                display: none !important;
            }
            .container {
                width: 100%;
                padding: 5px;
            }
            .text-center { text-align: center; }
            .text-right { text-align: right; }
            .text-left { text-align: left; }
            .font-bold { font-weight: bold; }
            .font-large { font-size: 14px; }
            .border-top { border-top: 1px dashed #000; }
            .border-bottom { border-top: 1px dashed #000; }
            .py-1 { padding-top: 2px; padding-bottom: 2px; }
            .my-1 { margin-top: 2px; margin-bottom: 2px; }
            .item-row { 
                display: flex;
                justify-content: space-between;
                margin-bottom: 1px;
            }
            .item-name { flex: 3; word-wrap: break-word; }
            .item-qty { flex: 1; text-align: center; }
            .item-price { flex: 2; text-align: right; }
            .item-total { flex: 2; text-align: right; }
            .dashed-line {
                border-top: 1px dashed #000;
                margin: 3px 0;
            }
            .company-info {
                text-align: center;
                margin-bottom: 3px;
            }
            .company-name {
                font-size: 14px;
                font-weight: bold;
                margin-bottom: 2px;
                text-transform: uppercase;
            }
            .company-details {
                font-size: 10px;
                line-height: 1.1;
            }
            .receipt-header {
                margin-bottom: 3px;
            }
        }
        
        body {
            font-family: "Courier New", monospace;
            font-size: 12px;
            line-height: 1.2;
            width: 80mm;
            margin: 0 auto;
            padding: 5px;
        }
        .company-info {
            text-align: center;
            margin-bottom: 5px;
        }
        .company-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 2px;
            text-transform: uppercase;
        }
        .company-details {
            font-size: 10px;
            line-height: 1.1;
        }
        .receipt-header {
            margin-bottom: 3px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Company Header -->
        <div class="company-info">
            <!-- Company Name -->
            @if($company && $company->company_name)
            <div class="company-name">
                {{ strtoupper($company->company_name) }}
            </div>
            @endif
            
            <!-- Location -->
            @if($company && $company->location)
            <div class="company-details">
                {{ $company->location }}
                @if($company->region)
                , {{ $company->region }}
                @endif
            </div>
            @elseif($company && $company->region)
            <div class="company-details">
                {{ $company->region }}
            </div>
            @endif
            
            <!-- Phone -->
            @if($company && $company->phone)
            <div class="company-details">
                Simu: {{ $company->phone }}
            </div>
            @endif
            
            <!-- Email -->
            @if($company && $company->email)
            <div class="company-details">
                Email: {{ $company->email }}
            </div>
            @endif
            

        </div>
        
        <!-- Separator Line - Only if there's company info -->
        @if($company && ($company->company_name || $company->location || $company->phone || $company->email))
        <div class="dashed-line"></div>
        @endif
        
        <!-- Receipt Info -->
        <div class="item-row">
            <span class="text-left">Tarehe:</span>
            <span class="text-right">{{ $date }}</span>
        </div>
        <div class="item-row">
            <span class="text-left">Risiti No:</span>
            <span class="text-right font-bold">{{ $receiptNo }}</span>
        </div>
        
        <div class="dashed-line"></div>
        
        <!-- Items Header -->
        <div class="item-row font-bold">
            <span class="item-name">Bidhaa</span>
            <span class="item-qty">Qty</span>
            <span class="item-price">Bei</span>
            <span class="item-total">Jumla</span>
        </div>
        
        <div class="dashed-line"></div>
        
        <!-- Items -->
        @foreach($sales as $sale)
        <div class="item-row">
            <span class="item-name">{{ $sale->bidhaa->jina }}</span>
            <span class="item-qty">{{ $sale->idadi }}</span>
            <span class="item-price">{{ number_format($sale->bei) }}</span>
            <span class="item-total">{{ number_format($sale->jumla) }}</span>
        </div>
        @endforeach
        
        <div class="dashed-line"></div>
        
        <!-- Totals -->
        <div class="item-row">
            <span class="text-left">Jumla:</span>
            <span class="text-right">{{ number_format($subtotal) }}/=</span>
        </div>
        
        @if($totalPunguzo > 0)
        <div class="item-row">
            <span class="text-left">Punguzo:</span>
            <span class="text-right">-{{ number_format($totalPunguzo) }}/=</span>
        </div>
        @endif
        
        <div class="item-row font-bold font-large">
            <span class="text-left">JUMLA KUU:</span>
            <span class="text-right">{{ number_format($total) }}/=</span>
        </div>
        
        <div class="dashed-line"></div>
        
        <!-- Footer -->
        <div class="text-center py-1">
            <div>ASANTE KWA KUNUNUA</div>
            <div class="py-1">Risiti hii imethibitishwa</div>
            
        </div>
        
        <!-- Optional: Add owner name at the bottom if not shown at top -->
        @if($company && $company->owner_name && !$company->company_name)
        <div class="text-center py-1 text-xs">
            <div>Mmiliki: {{ $company->owner_name }}</div>
        </div>
        @endif
    </div>
    
    <script>
        window.onload = function() {
            window.print();
            setTimeout(function() {
                window.close();
            }, 500);
        }
    </script>
</body>
</html>