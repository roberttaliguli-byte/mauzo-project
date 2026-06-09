<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Invoice #{{ $order->order_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f3f4f6;
            padding: 20px;
            font-size: 14px;
        }
        
        .invoice-wrapper {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }
        
        .invoice-header {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            padding: 30px 24px;
            text-align: center;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 8px;
        }
        
        .invoice-title {
            font-size: 14px;
            opacity: 0.9;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        
        .order-number {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            padding: 8px 20px;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            margin-top: 16px;
            font-family: monospace;
            letter-spacing: 1px;
        }
        
        .invoice-body {
            padding: 24px;
        }
        
        /* Info Cards */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }
        
        .info-card {
            background: #f9fafb;
            border-radius: 12px;
            padding: 16px;
            border: 1px solid #e5e7eb;
        }
        
        .info-card-title {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #6b7280;
            margin-bottom: 12px;
        }
        
        .info-card-content {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            font-size: 13px;
            padding: 4px 0;
            border-bottom: 1px dashed #e5e7eb;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 500;
            color: #4b5563;
        }
        
        .info-value {
            font-weight: 600;
            color: #111827;
        }
        
        /* Status Badge */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 600;
        }
        
        .status-saved {
            background: #fef3c7;
            color: #d97706;
        }
        
        .status-confirmed {
            background: #d1fae5;
            color: #065f46;
        }
        
        .status-paid {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .status-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }
        
        /* Products Table */
        .products-section {
            margin: 24px 0;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: 700;
            color: #374151;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 2px solid #f59e0b;
            display: inline-block;
        }
        
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .products-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        
        .products-table th {
            background: #f9fafb;
            padding: 12px 8px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            border-bottom: 2px solid #e5e7eb;
        }
        
        .products-table td {
            padding: 12px 8px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
        }
        
        .products-table tr:last-child td {
            border-bottom: none;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        /* Totals Section */
        .totals-section {
            background: #f9fafb;
            border-radius: 12px;
            padding: 20px;
            margin: 24px 0;
            border: 1px solid #e5e7eb;
        }
        
        .totals-row {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        .totals-item {
            text-align: right;
        }
        
        .totals-label {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 4px;
        }
        
        .totals-amount {
            font-size: 16px;
            font-weight: 700;
            color: #374151;
        }
        
        .grand-total {
            background: #fef3c7;
            padding: 12px 24px;
            border-radius: 50px;
            margin-left: 20px;
        }
        
        .grand-total .totals-label {
            color: #d97706;
            font-weight: 600;
        }
        
        .grand-total .totals-amount {
            font-size: 20px;
            color: #d97706;
        }
        
        /* Notes */
        .notes-section {
            background: #fffbeb;
            border-left: 4px solid #f59e0b;
            padding: 16px;
            border-radius: 8px;
            margin: 20px 0;
        }
        
        .notes-label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            color: #d97706;
            margin-bottom: 8px;
        }
        
        .notes-text {
            font-size: 13px;
            color: #374151;
            line-height: 1.5;
        }
        
        /* Footer */
        .invoice-footer {
            background: #f9fafb;
            padding: 20px 24px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        
        .footer-text {
            font-size: 11px;
            color: #6b7280;
            margin-bottom: 4px;
        }
        
        /* Action Buttons */
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 12px;
            padding: 20px 24px;
            background: white;
            border-top: 1px solid #e5e7eb;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 10px 24px;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-print {
            background: #f59e0b;
            color: white;
        }
        
        .btn-print:hover {
            background: #d97706;
            transform: translateY(-1px);
        }
        
        .btn-close {
            background: #6b7280;
            color: white;
        }
        
        .btn-close:hover {
            background: #4b5563;
            transform: translateY(-1px);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        /* Responsive Design */
        @media (max-width: 640px) {
            body {
                padding: 10px;
                font-size: 12px;
            }
            
            .invoice-header {
                padding: 20px 16px;
            }
            
            .company-name {
                font-size: 18px;
            }
            
            .order-number {
                font-size: 12px;
                padding: 6px 16px;
            }
            
            .invoice-body {
                padding: 16px;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }
            
            .products-table th,
            .products-table td {
                padding: 8px 6px;
                font-size: 11px;
            }
            
            .totals-row {
                flex-direction: column;
                align-items: flex-end;
                gap: 12px;
            }
            
            .grand-total {
                margin-left: 0;
                width: 100%;
                text-align: center;
            }
            
            .btn {
                padding: 8px 20px;
                font-size: 12px;
            }
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .invoice-wrapper {
                box-shadow: none;
                border-radius: 0;
            }
            
            .action-buttons,
            .btn-print,
            .btn-close {
                display: none;
            }
            
            .invoice-header {
                background: #f59e0b;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .status-badge {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-wrapper">
        <!-- Header -->
        <div class="invoice-header">
            <div class="company-name">{{ $company->company_name ?? 'MAUZO SYSTEM' }}</div>
            <div class="invoice-title">TAARIFA YA ODA</div>
            <div class="order-number">{{ $order->order_number }}</div>
        </div>
        
        <!-- Body -->
        <div class="invoice-body">
            <!-- Info Grid -->
            <div class="info-grid">
                <div class="info-card">
                    <div class="info-card-title">
                        <i>📋</i> TAARIFA ZA ODA
                    </div>
                    <div class="info-card-content">
                        <div class="info-row">
                            <span class="info-label">Tarehe:</span>
                            <span class="info-value">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Hali:</span>
                            <span class="info-value">
                                <span class="status-badge status-{{ $order->status }}">
                                    @if($order->status == 'saved') 💾 Imehifadhiwa
                                    @elseif($order->status == 'confirmed') ✅ Imethibitishwa
                                    @elseif($order->status == 'paid') 💰 Imelipwa
                                    @else ❌ Imefutwa
                                    @endif
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="info-card">
                    <div class="info-card-title">
                        <i>👤</i> TAARIFA ZA MTEGOLEWA
                    </div>
                    <div class="info-card-content">
                        <div class="info-row">
                            <span class="info-label">Jina Kamili:</span>
                            <span class="info-value">{{ $order->customer_name }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Namba ya Simu:</span>
                            <span class="info-value">{{ $order->customer_phone ?? '-' }}</span>
                        </div>
                        @if($order->customer_email)
                        <div class="info-row">
                            <span class="info-label">Barua Pepe:</span>
                            <span class="info-value">{{ $order->customer_email }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Products Table -->
            <div class="products-section">
                <div class="section-title">
                    🛒 BIDHAA ZILIZOAGIZWA
                </div>
                <div class="table-responsive">
                    <table class="products-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Bidhaa</th>
                                <th class="text-right">Idadi</th>
                                <th class="text-right">Bei</th>
                                <th class="text-right">Punguzo</th>
                                <th class="text-right">Jumla</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><strong>{{ $item['jina'] }}</strong><br>
                                    @if(isset($item['aina']) && $item['aina'])
                                    <span style="font-size: 10px; color: #6b7280;">{{ $item['aina'] }}</span>
                                    @endif
                                </td>
                                <td class="text-right">{{ number_format($item['idadi'], 2) }}</td>
                                <td class="text-right">{{ number_format($item['bei'], 0) }}</td>
                                <td class="text-right">{{ number_format($item['punguzo'] ?? 0, 0) }}</td>
                                <td class="text-right"><strong>{{ number_format($item['total'], 0) }} TZS</strong></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Totals -->
            <div class="totals-section">
                <div class="totals-row">
                    <div class="totals-item">
                        <div class="totals-label">Jumla Ndogo</div>
                        <div class="totals-amount">{{ number_format($order->subtotal, 0) }} TZS</div>
                    </div>
                    @if($order->discount > 0)
                    <div class="totals-item">
                        <div class="totals-label">Punguzo la Jumla</div>
                        <div class="totals-amount" style="color: #dc2626;">-{{ number_format($order->discount, 0) }} TZS</div>
                    </div>
                    @endif
                    <div class="totals-item grand-total">
                        <div class="totals-label">JUMLA KUU</div>
                        <div class="totals-amount">{{ number_format($order->total, 0) }} TZS</div>
                    </div>
                </div>
            </div>
            
            <!-- Notes -->
            @if($order->notes)
            <div class="notes-section">
                <div class="notes-label">
                    📝 MAELEZO YA ZIADA
                </div>
                <div class="notes-text">
                    {{ $order->notes }}
                </div>
            </div>
            @endif
        </div>
        
        <!-- Footer -->
        <div class="invoice-footer">
            <div class="footer-text">Asante kwa kununua kwetu!</div>
            <div class="footer-text">Imechapishwa: {{ now()->format('d/m/Y H:i') }}</div>
        </div>
        
        <!-- Action Buttons -->
        <div class="action-buttons no-print">
            <button class="btn btn-print" onclick="window.print()">
                🖨️ Chapisha
            </button>
            <button class="btn btn-close" onclick="window.close()">
                ✖️ Funga
            </button>
        </div>
    </div>
    
    <script>
        // Auto-trigger print dialog after page loads
        window.onload = function() {
            setTimeout(function() {
                // Optional: Auto print - uncomment if needed
                // window.print();
            }, 500);
        };
    </script>
</body>
</html>