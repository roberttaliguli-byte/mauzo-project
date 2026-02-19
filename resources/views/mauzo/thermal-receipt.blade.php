<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Risiti {{ $receiptNo }}</title>
    <style>
        @media print {
            @page {
                size: 72mm auto;
                margin: 0;
            }
            
            body {
                width: 72mm !important;
                margin: 0 !important;
                padding: 2mm !important;
                font-family: 'Arial', 'Helvetica', 'Liberation Sans', sans-serif !important;
                font-size: 12px !important; /* Slightly larger base font */
                line-height: 1.3 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color: #000000 !important;
                background: white !important;
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
                margin: 0 auto !important;
            }
            
            /* Text alignment */
            .text-center { text-align: center !important; }
            .text-right { text-align: right !important; }
            .text-left { text-align: left !important; }
            
            /* Improved bold text - using multiple techniques for better visibility */
            .font-bold, 
            .font-large, 
            .font-xlarge,
            .company-name,
            .total-section .item-row:last-child span {
                font-weight: 700 !important;
                text-shadow: 0.2px 0.2px 0.2px rgba(0,0,0,0.1) !important;
                -webkit-text-stroke: 0.2px black !important;
                letter-spacing: 0.2px !important;
            }
            
            /* Larger font sizes with better contrast */
            .font-large { 
                font-size: 15px !important;
                font-weight: 700 !important;
            }
            
            .font-xlarge { 
                font-size: 18px !important;
                font-weight: 800 !important;
            }
            
            .font-small {
                font-size: 11px !important;
            }
            
            /* Borders - made darker and thicker */
            .border-top { 
                border-top: 2px solid #000 !important; 
                border-top-color: #000000 !important;
            }
            
            .border-bottom { 
                border-bottom: 2px solid #000 !important;
                border-bottom-color: #000000 !important;
            }
            
            .py-1 { padding-top: 3px !important; padding-bottom: 3px !important; }
            .my-1 { margin-top: 3px !important; margin-bottom: 3px !important; }
            .mt-1 { margin-top: 3px !important; }
            .mb-1 { margin-bottom: 3px !important; }
            
            /* Item row layout - improved for thermal printing */
            .item-row { 
                display: flex !important;
                justify-content: space-between !important;
                margin-bottom: 3px !important;
                width: 100% !important;
                clear: both !important;
            }
            
            .item-name { 
                flex: 3 !important; 
                word-break: break-word !important;
                overflow-wrap: break-word !important;
                padding-right: 4px !important;
                white-space: normal !important; /* Changed from nowrap to normal */
                overflow: hidden !important;
                text-overflow: ellipsis !important;
                max-width: 35mm !important;
                font-weight: 400 !important;
            }
            
            .item-qty { 
                flex: 1 !important; 
                text-align: center !important;
                min-width: 35px !important;
                font-weight: 400 !important;
            }
            
            .item-price { 
                flex: 1.5 !important; 
                text-align: right !important;
                min-width: 40px !important;
                padding-right: 3px !important;
                font-weight: 400 !important;
            }
            
            .item-total { 
                flex: 1.5 !important; 
                text-align: right !important;
                min-width: 45px !important;
                font-weight: 600 !important; /* Totals slightly bolder */
            }
            
            /* Lines - made thicker and darker */
            .dashed-line {
                border-top: 1.5px dashed #000 !important;
                margin: 4px 0 !important;
                height: 0 !important;
                clear: both !important;
                border-color: #000000 !important;
            }
            
            .solid-line {
                border-top: 1.5px solid #000 !important;
                margin: 4px 0 !important;
                height: 0 !important;
                clear: both !important;
                border-color: #000000 !important;
            }
            
            .double-line {
                border-top: 3px double #000 !important;
                margin: 4px 0 !important;
                height: 0 !important;
                clear: both !important;
                border-color: #000000 !important;
            }
            
            /* Company info - enhanced */
            .company-info {
                text-align: center !important;
                margin-bottom: 8px !important;
                width: 100% !important;
            }
            
            .company-name {
                font-size: 20px !important;
                font-weight: 800 !important;
                margin-bottom: 3px !important;
                text-transform: uppercase !important;
                letter-spacing: 0.8px !important;
                background: #ffffff !important;
                color: #000000 !important;
                padding: 2px 0 !important;
            }
            
            .company-details {
                font-size: 11px !important;
                line-height: 1.3 !important;
                color: #000000 !important;
                font-weight: 400 !important;
            }
            
            .receipt-header {
                margin-bottom: 6px !important;
            }
            
            /* Discount info - improved */
            .discount-info {
                font-size: 10px !important;
                color: #000000 !important;
                margin-left: 8px !important;
                padding: 1px 3px !important;
                display: flex !important;
                justify-content: space-between !important;
                width: calc(100% - 8px) !important;
                border-left: 2px solid #666 !important;
            }
            
            /* Table fallback */
            table {
                width: 100% !important;
                border-collapse: collapse !important;
            }
            
            td, th {
                padding: 2px 2px !important;
                vertical-align: top !important;
                border: none !important;
            }
            
            /* Total section - emphasized */
            .total-section {
                margin-top: 6px !important;
            }
            
            /* Footer - enhanced */
            .footer {
                margin-top: 10px !important;
                text-align: center !important;
            }
            
            .footer .font-large {
                font-size: 16px !important;
                font-weight: 700 !important;
                margin-bottom: 3px !important;
            }
            
            /* Highlight - better contrast */
            .highlight {
                background-color: #f0f0f0 !important;
                padding: 3px 0 !important;
            }
            
            /* Ensure all text is black */
            body, div, span, p, h1, h2, h3, h4, h5, h6 {
                color: #000000 !important;
            }
            
            /* Additional styles for totals */
            .item-row:last-of-type .text-right {
                font-weight: 700 !important;
            }
            
            /* Better visibility for numbers */
            .item-total, .text-right {
                font-feature-settings: "tnum" !important;
            }
            
            /* Ensure proper contrast for all elements */
            .discount-info span {
                color: #333333 !important;
            }
            
            /* Receipt number emphasis */
            .item-row .font-bold {
                font-weight: 700 !important;
                background: #ffffff !important;
            }
        }
        
        /* Screen preview styles */
        body {
            font-family: 'Arial', sans-serif !important;
            font-size: 12px !important;
            line-height: 1.3 !important;
            width: 72mm !important;
            margin: 20px auto !important;
            padding: 3mm !important;
            background: white !important;
            border: 1px solid #999 !important;
            box-shadow: 0 3px 8px rgba(0,0,0,0.2) !important;
        }
        
        .container {
            width: 100% !important;
            max-width: 68mm !important;
            margin: 0 auto !important;
        }
        
        /* Copy print styles for screen preview */
        .font-bold { font-weight: 700 !important; }
        .font-large { font-size: 15px !important; font-weight: 700 !important; }
        .font-xlarge { font-size: 18px !important; font-weight: 800 !important; }
        .company-name { font-size: 20px !important; font-weight: 800 !important; }
        .item-row { display: flex !important; justify-content: space-between !important; }
        .item-name { flex: 3 !important; }
        .item-qty { flex: 1 !important; text-align: center !important; }
        .item-price { flex: 1.5 !important; text-align: right !important; }
        .item-total { flex: 1.5 !important; text-align: right !important; font-weight: 600 !important; }
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
            // Ensure all content is loaded
            setTimeout(function() {
                window.print();
            }, 300);
            
            // Handle after print
            window.onafterprint = function() {
                setTimeout(function() {
                    window.close();
                }, 300);
            };
            
            // Fallback close after 5 seconds
            setTimeout(function() {
                if (!window.closed) {
                    window.close();
                }
            }, 5000);
        }
    </script>
</body>
</html>