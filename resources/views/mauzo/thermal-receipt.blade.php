<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Risiti {{ $receiptNo }}</title>
    <style>
        @media print {
            body {
                width: 72mm !important;
                max-width: 72mm !important;
                margin: 0 !important;
                padding: 2mm !important;
                font-family: "Arial Narrow", "Liberation Sans Narrow", sans-serif !important;
                font-size: 13px !important;
                line-height: 1.15 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box !important;
            }
            .no-print {
                display: none !important;
            }
            .container {
                width: 100% !important;
                max-width: 68mm !important;
                padding: 1mm !important;
                overflow: hidden !important;
            }
            .text-center { text-align: center !important; }
            .text-right { text-align: right !important; }
            .text-left { text-align: left !important; }
            .font-bold { 
                font-weight: bold !important;
                font-size: 14px !important;
            }
            .font-large { 
                font-size: 15px !important;
                font-weight: bold !important;
            }
            .font-xlarge { 
                font-size: 16px !important;
                font-weight: bold !important;
            }
            .font-small {
                font-size: 11px !important;
            }
            .border-top { border-top: 2px solid #000 !important; }
            .border-bottom { border-bottom: 2px solid #000 !important; }
            .py-1 { padding-top: 3px !important; padding-bottom: 3px !important; }
            .my-1 { margin-top: 3px !important; margin-bottom: 3px !important; }
            .item-row { 
                display: flex !important;
                justify-content: space-between !important;
                margin-bottom: 2px !important;
                width: 100% !important;
                clear: both !important;
            }
            .item-name { 
                flex: 3 !important; 
                word-break: break-word !important;
                overflow-wrap: break-word !important;
                padding-right: 2px !important;
            }
            .item-qty { 
                flex: 1 !important; 
                text-align: center !important;
                min-width: 15px !important;
            }
            .item-price { 
                flex: 2 !important; 
                text-align: right !important;
                min-width: 25px !important;
                padding-right: 3px !important;
            }
            .item-total { 
                flex: 2 !important; 
                text-align: right !important;
                min-width: 30px !important;
            }
            .dashed-line {
                border-top: 2px dashed #000 !important;
                margin: 5px 0 !important;
                height: 0 !important;
                clear: both !important;
            }
            .solid-line {
                border-top: 2px solid #000 !important;
                margin: 5px 0 !important;
                height: 0 !important;
                clear: both !important;
            }
            .company-info {
                text-align: center !important;
                margin-bottom: 5px !important;
                width: 100% !important;
            }
            .company-name {
                font-size: 19px !important;
                font-weight: bold !important;
                margin-bottom: 3px !important;
                text-transform: uppercase !important;
                letter-spacing: 0.5px !important;
            }
            .company-details {
                font-size: 12px !important;
                line-height: 1.2 !important;
            }
            .receipt-header {
                margin-bottom: 5px !important;
            }
            .discount-info {
                font-size: 11px !important;
                color: #000 !important;
                font-style: italic !important;
                margin-left: 5px !important;
                background-color: #f0f0f0 !important;
                padding: 1px 3px !important;
            }
            table {
                width: 100% !important;
                border-collapse: collapse !important;
            }
            td, th {
                padding: 2px 1px !important;
                vertical-align: top !important;
            }
        }
        
        /* Screen preview */
        body {
            font-family: "Arial Narrow", sans-serif !important;
            font-size: 13px !important;
            line-height: 1.15 !important;
            width: 72mm !important;
            margin: 10px auto !important;
            padding: 2mm !important;
            background: white !important;
            border: 1px solid #ccc !important;
        }
        .container {
            width: 100% !important;
            max-width: 68mm !important;
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
                <strong>Simu:</strong> {{ $company->phone }}
            </div>
            @endif
            
            <!-- Email -->
            @if($company && $company->email)
            <div class="company-details">
                <strong>Email:</strong> {{ $company->email }}
            </div>
            @endif
        </div>
        
        <!-- Separator Line -->
        <div class="solid-line"></div>
        
        <!-- Receipt Info -->
        <div class="item-row">
            <span class="text-left font-bold">Tarehe:</span>
            <span class="text-right font-bold">{{ $date }}</span>
        </div>
        <div class="item-row">
            <span class="text-left font-bold">Risiti No:</span>
            <span class="text-right font-bold">{{ $receiptNo }}</span>
        </div>
        
        <div class="dashed-line"></div>
        
        <!-- Items Header -->
        <div class="item-row font-bold">
            <span class="item-name">BIDHAA</span>
            <span class="item-qty">QTY</span>
            <span class="item-price">BEI</span>
            <span class="item-total">JUMLA</span>
        </div>
        
        <div class="dashed-line"></div>
        
        <!-- Items -->
        @foreach($sales as $sale)
        <div class="item-row">
            <span class="item-name"><strong>{{ $sale->bidhaa->jina }}</strong></span>
            <span class="item-qty">{{ $sale->idadi }}</span>
            <span class="item-price">{{ number_format($sale->bei) }}</span>
            <span class="item-total"><strong>{{ number_format($sale->jumla) }}</strong></span>
        </div>
        
        <!-- Show discount info if applicable -->
        @if($sale->punguzo > 0)
        <div class="item-row discount-info">
            <span class="text-left">
                <strong>Punguzo:</strong> 
                @if($sale->punguzo_aina === 'bidhaa')
                    {{ number_format($sale->punguzo) }} kwa kila bidhaa
                @else
                    {{ number_format($sale->punguzo) }} kwa jumla
                @endif
            </span>
            <span class="text-right">
                <strong>-{{ number_format($sale->punguzo_aina === 'bidhaa' ? $sale->punguzo * $sale->idadi : $sale->punguzo) }}</strong>
            </span>
        </div>
        @endif
        @endforeach
        
        <div class="solid-line"></div>
        
        <!-- Totals -->
        <div class="item-row">
            <span class="text-left font-bold">Jumla:</span>
            <span class="text-right font-bold">{{ number_format($subtotal) }}/=</span>
        </div>
        
        @if($totalPunguzo > 0)
        <div class="item-row">
            <span class="text-left"><strong>Punguzo:</strong></span>
            <span class="text-right"><strong>-{{ number_format($totalPunguzo) }}/=</strong></span>
        </div>
        @endif
        
        <div class="item-row font-xlarge">
            <span class="text-left">JUMLA KUU:</span>
            <span class="text-right">{{ number_format($total) }}/=</span>
        </div>
        
        <div class="solid-line"></div>
        
        <!-- Footer -->
        <div class="text-center py-1">
            <div class="font-bold font-large">ASANTE KWA KUNUNUA</div>
            <div class="py-1 font-bold">Risiti hii imethibitishwa</div>
        </div>
        
        <!-- Optional: Add owner name -->
        @if($company && $company->owner_name && !$company->company_name)
        <div class="text-center py-1">
            <div class="font-bold">Mmiliki: {{ $company->owner_name }}</div>
        </div>
        @endif
        
        <!-- Print timestamp -->
        <div class="text-center py-1 font-small">
            <div>Ilichapishwa: {{ date('Y-m-d H:i:s') }}</div>
        </div>
    </div>
    
    <script>
        window.onload = function() {
            // Force print dialog
            setTimeout(function() {
                window.print();
            }, 250);
            
            // Close window after print
            window.onafterprint = function() {
                setTimeout(function() {
                    window.close();
                }, 500);
            };
            
            // Fallback close if onafterprint doesn't fire
            setTimeout(function() {
                window.close();
            }, 5000);
        }
    </script>
</body>
</html>