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
                font-size: 11px !important;
                line-height: 1.2 !important;
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
            }
            .font-large { 
                font-size: 14px !important;
                font-weight: bold !important;
            }
            .font-xlarge { 
                font-size: 16px !important;
                font-weight: bold !important;
            }
            .font-small {
                font-size: 10px !important;
            }
            .border-top { border-top: 2px solid #000 !important; }
            .border-bottom { border-bottom: 2px solid #000 !important; }
            .py-1 { padding-top: 3px !important; padding-bottom: 3px !important; }
            .my-1 { margin-top: 3px !important; margin-bottom: 3px !important; }
            .mt-1 { margin-top: 3px !important; }
            .mb-1 { margin-bottom: 3px !important; }
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
                white-space: nowrap !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
            }
            .item-qty { 
                flex: 1 !important; 
                text-align: center !important;
                min-width: 35px !important;
            }
            .item-price { 
                flex: 2 !important; 
                text-align: right !important;
                min-width: 45px !important;
                padding-right: 3px !important;
            }
            .item-total { 
                flex: 2 !important; 
                text-align: right !important;
                min-width: 50px !important;
            }
            .dashed-line {
                border-top: 1px dashed #000 !important;
                margin: 3px 0 !important;
                height: 0 !important;
                clear: both !important;
            }
            .solid-line {
                border-top: 1px solid #000 !important;
                margin: 3px 0 !important;
                height: 0 !important;
                clear: both !important;
            }
            .double-line {
                border-top: 3px double #000 !important;
                margin: 3px 0 !important;
                height: 0 !important;
                clear: both !important;
            }
            .company-info {
                text-align: center !important;
                margin-bottom: 5px !important;
                width: 100% !important;
            }
            .company-name {
                font-size: 18px !important;
                font-weight: bold !important;
                margin-bottom: 2px !important;
                text-transform: uppercase !important;
                letter-spacing: 0.5px !important;
            }
            .company-details {
                font-size: 10px !important;
                line-height: 1.2 !important;
            }
            .receipt-header {
                margin-bottom: 5px !important;
            }
            .discount-info {
                font-size: 10px !important;
                color: #333 !important;
                font-style: italic !important;
                margin-left: 5px !important;
                padding: 1px 3px !important;
                display: flex !important;
                justify-content: space-between !important;
                width: 100% !important;
            }
            table {
                width: 100% !important;
                border-collapse: collapse !important;
            }
            td, th {
                padding: 2px 1px !important;
                vertical-align: top !important;
            }
            .total-section {
                margin-top: 5px !important;
            }
            .footer {
                margin-top: 8px !important;
                text-align: center !important;
            }
            .highlight {
                background-color: #f0f0f0 !important;
                padding: 2px 0 !important;
            }
        }
        
        /* Screen preview */
        body {
            font-family: "Arial Narrow", sans-serif !important;
            font-size: 11px !important;
            line-height: 1.2 !important;
            width: 72mm !important;
            margin: 10px auto !important;
            padding: 2mm !important;
            background: white !important;
            border: 1px solid #ccc !important;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1) !important;
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
            @if($company && $company->company_name)
            <div class="company-name">
                {{ strtoupper($company->company_name) }}
            </div>
            @elseif($company && $company->owner_name)
            <div class="company-name">
                {{ strtoupper($company->owner_name) }}
            </div>
            @else
            <div class="company-name">
                BIASHARA YANGU
            </div>
            @endif
            
            @if($company && ($company->location || $company->region))
            <div class="company-details">
                {{ $company->location ?? '' }}
                @if($company->location && $company->region)
                , 
                @endif
                {{ $company->region ?? '' }}
            </div>
            @endif
            
            @if($company && $company->phone)
            <div class="company-details">
                <span>Simu: {{ $company->phone }}</span>
            </div>
            @endif
            
            @if($company && $company->email)
            <div class="company-details">
                <span>{{ $company->email }}</span>
            </div>
            @endif
        </div>
        
        <!-- Separator Line -->
        <div class="double-line"></div>
        
        <!-- Receipt Info -->
        <div class="item-row">
            <span class="text-left">Risiti Na:</span>
            <span class="text-right font-bold">{{ $receiptNo }}</span>
        </div>
        <div class="item-row">
            <span class="text-left">Tarehe:</span>
            <span class="text-right">{{ $date }}</span>
        </div>
        <div class="item-row">
            <span class="text-left">Mfanyabiashara:</span>
            <span class="text-right">{{ Auth::user()->name ?? 'Mjumbe' }}</span>
        </div>
        
        <div class="dashed-line"></div>
        
        <!-- Items Header -->
        <div class="item-row font-bold">
            <span class="item-name">BIDHAA</span>
            <span class="item-qty">IDADI</span>
            <span class="item-price">BEI</span>
            <span class="item-total">JUMLA</span>
        </div>
        
        <div class="dashed-line"></div>
        
        <!-- Items -->
        @foreach($sales as $index => $sale)
        <div class="item-row">
            <span class="item-name" title="{{ $sale->bidhaa->jina }}">
                {{ Str::limit($sale->bidhaa->jina, 20) }}
            </span>
            <span class="item-qty">{{ number_format($sale->idadi, 2) }}</span>
            <span class="item-price">{{ number_format($sale->bei, 0) }}</span>
            <span class="item-total">{{ number_format($sale->jumla, 2) }}</span>
        </div>
        
        <!-- Show discount info if applicable -->
        @if($sale->punguzo > 0)
        <div class="discount-info">
            <span>
                <i>Punguzo: 
                @if($sale->punguzo_aina === 'bidhaa')
                    {{ number_format($sale->punguzo, 2) }}/@
                @else
                    Jumla
                @endif
                </i>
            </span>
            <span>
                <i>-{{ number_format($sale->punguzo_aina === 'bidhaa' ? $sale->punguzo * $sale->idadi : $sale->punguzo, 2) }}</i>
            </span>
        </div>
        @endif
        
        <!-- Add small space between items -->
        @if(!$loop->last)
        <div style="height: 2px;"></div>
        @endif
        @endforeach
        
        <div class="solid-line"></div>
        
        <!-- Totals -->
        <div class="item-row">
            <span class="text-left">Jumla Ndogo:</span>
            <span class="text-right">{{ number_format($subtotal, 2) }}/=</span>
        </div>
        
        @if($totalPunguzo > 0)
        <div class="item-row">
            <span class="text-left">Jumla ya Punguzo:</span>
            <span class="text-right">-{{ number_format($totalPunguzo, 2) }}/=</span>
        </div>
        @endif
        
        <div class="double-line"></div>
        
        <div class="item-row font-xlarge">
            <span class="text-left">JUMLA KUU:</span>
            <span class="text-right">{{ number_format($total, 2) }}/=</span>
        </div>
        
        <!-- Payment Method (if available) -->
        @if(isset($sales[0]) && $sales[0]->lipa_kwa)
        <div class="item-row mt-1">
            <span class="text-left">Njia ya Malipo:</span>
            <span class="text-right font-bold">
                @if($sales[0]->lipa_kwa == 'cash')
                    CASH
                @elseif($sales[0]->lipa_kwa == 'lipa_namba')
                    LIPA NAMBA
                @elseif($sales[0]->lipa_kwa == 'bank')
                    BENKI
                @else
                    {{ strtoupper($sales[0]->lipa_kwa) }}
                @endif
            </span>
        </div>
        @endif
        
        <div class="solid-line"></div>
        
        <!-- Footer -->
        <div class="footer">
            <div class="font-large">ASANTE KWA KUNUNUA</div>
            <div class="font-small">Risiti hii ni halali</div>
            <div class="font-small mt-1">*** Karibu tena ***</div>
        </div>
        
        <!-- Reprint count -->
        @if(isset($sales[0]) && $sales[0]->reprint_count > 0)
        <div class="text-center font-small mt-1">
            <i>(Chapisho la {{ $sales[0]->reprint_count + 1 }})</i>
        </div>
        @endif
        
        <!-- Print timestamp -->
        <div class="text-center font-small mt-1">
            <div>{{ date('d/m/Y H:i:s') }}</div>
        </div>
 
    </div>
    
    <script>
        window.onload = function() {
            // Small delay to ensure rendering
            setTimeout(function() {
                window.print();
            }, 300);
            
            // Handle after print
            window.onafterprint = function() {
                setTimeout(function() {
                    window.close();
                }, 300);
            };
            
            // Handle before print (for some browsers)
            window.onbeforeprint = function() {
                // Ensure all content is loaded
            };
            
            // Fallback close after 3 seconds
            setTimeout(function() {
                // Check if window is still open and not printed
                if (!window.closed) {
                    window.close();
                }
            }, 3000);
        }
    </script>
</body>
</html>