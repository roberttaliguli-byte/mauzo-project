<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Risiti ya Order {{ $order->order_number ?? '#' . $order->id }}</title>
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
                font-size: 12px !important;
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
            
            .text-center { text-align: center !important; }
            .text-right { text-align: right !important; }
            .text-left { text-align: left !important; }
            
            .font-bold {
                font-weight: 700 !important;
                text-shadow: 0.2px 0.2px 0.2px rgba(0,0,0,0.1) !important;
                -webkit-text-stroke: 0.2px black !important;
                letter-spacing: 0.2px !important;
            }
            
            .font-large { 
                font-size: 15px !important;
                font-weight: 700 !important;
            }
            
            .font-xlarge { 
                font-size: 18px !important;
                font-weight: 800 !important;
            }
            
            .font-small {
                font-size: 10px !important;
            }
            
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
                white-space: normal !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
                max-width: 35mm !important;
                font-weight: 400 !important;
            }
            
            .item-qty { 
                flex: 1 !important; 
                text-align: center !important;
                min-width: 30px !important;
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
                font-weight: 600 !important;
            }
            
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
            
            .status-badge {
                display: inline-block !important;
                padding: 1px 8px !important;
                border-radius: 3px !important;
                font-size: 11px !important;
                font-weight: 700 !important;
                text-transform: uppercase !important;
            }
            
            .status-saved { color: #92400e !important; }
            .status-confirmed { color: #1e40af !important; }
            .status-paid { color: #065f46 !important; }
            .status-cancelled { color: #991b1b !important; }
            
            .footer {
                margin-top: 10px !important;
                text-align: center !important;
            }
            
            .footer .font-large {
                font-size: 16px !important;
                font-weight: 700 !important;
                margin-bottom: 3px !important;
            }
            
            body, div, span, p, h1, h2, h3, h4, h5, h6 {
                color: #000000 !important;
            }
            
            .item-total, .text-right {
                font-feature-settings: "tnum" !important;
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
        
        .font-bold { font-weight: 700 !important; }
        .font-large { font-size: 15px !important; font-weight: 700 !important; }
        .font-xlarge { font-size: 18px !important; font-weight: 800 !important; }
        .company-name { font-size: 20px !important; font-weight: 800 !important; }
        .item-row { display: flex !important; justify-content: space-between !important; }
        .item-name { flex: 3 !important; }
        .item-qty { flex: 1 !important; text-align: center !important; }
        .item-price { flex: 1.5 !important; text-align: right !important; }
        .item-total { flex: 1.5 !important; text-align: right !important; font-weight: 600 !important; }
        
        .no-print {
            display: block !important;
            text-align: center !important;
            margin-top: 10px !important;
            padding: 8px !important;
            background: #f3f4f6 !important;
            border-radius: 8px !important;
        }
        
        .no-print button {
            padding: 8px 20px !important;
            background: #10b981 !important;
            color: #fff !important;
            border: none !important;
            border-radius: 6px !important;
            font-size: 14px !important;
            font-weight: 600 !important;
            cursor: pointer !important;
            margin: 0 5px !important;
        }
        
        .no-print button:hover {
            background: #059669 !important;
        }
        
        .no-print .btn-close {
            background: #6b7280 !important;
        }
        
        .no-print .btn-close:hover {
            background: #4b5563 !important;
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
        
        <!-- Order Header -->
        <div class="item-row">
            <span class="text-left font-bold">ORDER</span>
            <span class="text-right font-bold">{{ $order->order_number ?? '#' . $order->id }}</span>
        </div>
        
        <!-- Customer Info -->
        <div class="item-row">
            <span class="text-left">Mteja:</span>
            <span class="text-right font-bold">{{ $order->customer_name ?? 'Walk-in' }}</span>
        </div>
        
        @if($order->customer_phone)
        <div class="item-row">
            <span class="text-left">Simu:</span>
            <span class="text-right">{{ $order->customer_phone }}</span>
        </div>
        @endif
        
        @if($order->customer_address)
        <div class="item-row">
            <span class="text-left">Anapoishi:</span>
            <span class="text-right">{{ $order->customer_address }}</span>
        </div>
        @endif
        
        <div class="item-row">
            <span class="text-left">Tarehe:</span>
            <span class="text-right">{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}</span>
        </div>
        
        <div class="item-row">
            <span class="text-left">Hali:</span>
            <span class="text-right status-badge status-{{ $order->status }}">
                {{ ucfirst($order->status) }}
            </span>
        </div>
        
        @if($order->order_type)
        <div class="item-row">
            <span class="text-left">Aina ya Order:</span>
            <span class="text-right">{{ ucfirst($order->order_type) }}</span>
        </div>
        @endif
        
        @if($order->table_number)
        <div class="item-row">
            <span class="text-left">Meza:</span>
            <span class="text-right">{{ $order->table_number }}</span>
        </div>
        @endif
        
        <div class="dashed-line"></div>
        
        <!-- Items Header -->
        <div class="item-row font-bold">
            <span class="item-name">BIDHAA</span>
            <span class="item-qty">IDADI</span>
            <span class="item-total">JUMLA</span>
        </div>
        
        <div class="dashed-line"></div>
        
        <!-- Items - From JSON field -->
        @php
            // Decode items from JSON field
            $items = is_string($order->items) ? json_decode($order->items, true) : $order->items;
            $items = $items ?? [];
            $subtotal = $order->subtotal ?? 0;
            $totalDiscount = $order->discount ?? 0;
            $hasDiscount = $totalDiscount > 0;
        @endphp
        
        @if(count($items) > 0)
            @foreach($items as $item)
            @php
                $itemName = $item['jina'] ?? $item['name'] ?? 'Bidhaa';
                $qty = $item['idadi'] ?? $item['qty'] ?? 1;
                $price = $item['bei'] ?? $item['price'] ?? 0;
                $total = $item['jumla'] ?? $item['total'] ?? ($qty * $price);
                $discount = $item['punguzo'] ?? $item['discount'] ?? 0;
            @endphp
            <div class="item-row">
                <span class="item-name" title="{{ $itemName }}">
                    {{ Str::limit($itemName, 20) }}
                </span>
                <span class="item-qty">{{ number_format($qty, 2) }}</span>
                <span class="item-total">{{ number_format($total, 2) }}</span>
            </div>
            
            @if($discount > 0)
            <div class="item-row font-small" style="padding-left: 8px; border-left: 2px solid #666; margin-bottom: 3px;">
                <span class="text-left" style="color: #333;">
                    <i>Punguzo: -{{ number_format($discount, 2) }}</i>
                </span>
            </div>
            @endif
            @endforeach
        @else
            <div class="item-row">
                <span class="text-center" style="width:100%;color:#999;">Hakuna bidhaa</span>
            </div>
        @endif
        
        <div class="solid-line"></div>
        
        <!-- Totals -->
        <div class="item-row">
            <span class="text-left">Jumla Ndogo:</span>
            <span class="text-right">{{ number_format($subtotal, 2) }}/=</span>
        </div>
        
        @if($totalDiscount > 0)
        <div class="item-row">
            <span class="text-left">Jumla ya Punguzo:</span>
            <span class="text-right">-{{ number_format($totalDiscount, 2) }}/=</span>
        </div>
        @endif
        
        @if($order->delivery_fee > 0)
        <div class="item-row">
            <span class="text-left">Gharama ya Usafirishaji:</span>
            <span class="text-right">{{ number_format($order->delivery_fee, 2) }}/=</span>
        </div>
        @endif
        
        @if($order->tax > 0)
        <div class="item-row">
            <span class="text-left">Kodi:</span>
            <span class="text-right">{{ number_format($order->tax, 2) }}/=</span>
        </div>
        @endif
        
        <div class="double-line"></div>
        
        <div class="item-row font-large">
            <span class="text-left">JUMLA KUU:</span>
            <span class="text-right">{{ number_format($order->total ?? ($subtotal - $totalDiscount + $order->delivery_fee + $order->tax), 2) }}/=</span>
        </div>
        
        <div class="solid-line"></div>
        
        <!-- Footer -->
        <div class="footer">
            <div class="font-large">ASANTE KWA KUNUNUA</div>
            <div class="font-small">Risiti hii ni halali</div>
            <div class="font-small mt-1">*** Karibu tena ***</div>
        </div>
        
        <!-- Print timestamp -->
        <div class="text-center font-small mt-1">
            <div>{{ date('d/m/Y H:i:s') }}</div>
        </div>
    </div>
    
    <!-- Print Controls (visible only on screen) -->
    <div class="no-print" style="text-align:center;margin-top:16px;padding:12px;background:#f3f4f6;border-radius:8px;">
        <button onclick="window.print()" style="padding:8px 20px;background:#10b981;color:#fff;border:none;border-radius:6px;font-size:14px;font-weight:600;cursor:pointer;">
            <i class="fas fa-print"></i> Chapisha
        </button>
        <button onclick="window.close()" style="padding:8px 20px;background:#6b7280;color:#fff;border:none;border-radius:6px;font-size:14px;font-weight:600;cursor:pointer;margin-left:8px;">
            <i class="fas fa-times"></i> Funga
        </button>
    </div>
    
    <script>
        window.onload = function() {
            if (window.opener) {
                setTimeout(function() {
                    window.print();
                }, 300);
            }
            
            window.onafterprint = function() {
                if (window.opener) {
                    setTimeout(function() {
                        window.close();
                    }, 500);
                }
            };
        };
    </script>
</body>
</html>