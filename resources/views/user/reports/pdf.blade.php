<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $companyName ?? 'Ripoti' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.3;
            color: #000000;
            background: #ffffff;
            padding: 15px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 1px solid #000;
            padding-bottom: 8px;
        }
        
        .company-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 3px;
        }
        
        .report-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 3px;
        }
        
        .report-subtitle {
            font-size: 12px;
            margin-bottom: 3px;
            color: #333;
        }
        
        .report-period {
            font-size: 10px;
            margin-bottom: 3px;
        }
        
        .report-date {
            font-size: 9px;
            text-align: center;
            margin-bottom: 10px;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 9px;
        }
        
        .data-table th {
            background: #f0f0f0;
            font-weight: bold;
            padding: 5px 3px;
            border: 1px solid #000;
            text-align: left;
        }
        
        .data-table td {
            padding: 4px 3px;
            border: 1px solid #000;
        }
        
        .data-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        /* 3x2 Grid Styles - Compact */
        .grid-3x2 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            grid-template-rows: auto auto;
            gap: 8px;
            margin: 10px 0;
            page-break-inside: avoid;
        }
        
        .grid-item {
            border: 1px solid #000;
            padding: 6px;
            border-radius: 3px;
            background: #ffffff;
            font-size: 8px;
        }
        
        .grid-item-total {
            border: 2px solid #000;
            background: #f8f9fa;
            font-weight: bold;
            grid-column: span 3;
        }
        
        .grid-item-header {
            font-weight: bold;
            margin-bottom: 5px;
            padding-bottom: 3px;
            border-bottom: 1px solid #ccc;
            text-align: center;
            font-size: 9px;
        }
        
        .grid-item-content {
            font-size: 8px;
        }
        
        .grid-line {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
            padding: 1px 0;
        }
        
        .grid-line-large {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
            padding: 2px 0;
            font-size: 9px;
        }
        
        .grid-label {
            font-weight: normal;
        }
        
        .grid-value {
            font-weight: bold;
        }
        
        .grid-total {
            border-top: 1px solid #000;
            margin-top: 4px;
            padding-top: 4px;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            font-size: 9px;
        }
        
        .grid-total-grand {
            border-top: 2px solid #000;
            margin-top: 6px;
            padding-top: 6px;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            background: #e9ecef;
            padding: 4px;
            border-radius: 2px;
        }
        
        .section-title {
            font-size: 12px;
            font-weight: bold;
            margin: 12px 0 6px 0;
            border-bottom: 1px solid #000;
            padding-bottom: 3px;
        }
        
        .date-group {
            margin-bottom: 15px;
            page-break-inside: avoid;
        }
        
        .date-header {
            background: #e9ecef;
            padding: 5px;
            margin-bottom: 5px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 9px;
            border-left: 3px solid #007bff;
        }
        
        .grand-total {
            margin-top: 15px;
            padding: 10px;
            border: 2px solid #000;
            background: #f0f0f0;
            font-weight: bold;
            text-align: center;
            font-size: 13px;
            border-radius: 4px;
            page-break-inside: avoid;
        }
        
        .footer {
            margin-top: 20px;
            padding-top: 5px;
            border-top: 1px solid #000;
            text-align: center;
            font-size: 8px;
        }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        .no-data {
            text-align: center;
            padding: 15px;
            color: #666;
            font-style: italic;
            font-size: 10px;
        }
        
        .page-break-avoid {
            page-break-inside: avoid;
        }
        
        /* For mobile money row highlight */
        .mobile-row {
            background-color: #f8f9fa !important;
        }
        
        .cash-row {
            background-color: #fff !important;
        }
        
        .bank-row {
            background-color: #f8f9fa !important;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="company-name">{{ $companyName ?? 'Biashara' }}</div>
        <div class="report-title">
            @if($reportType === 'sales')
                RIPOTI YA MAUZO
            @elseif($reportType === 'manunuzi')
                RIPOTI YA MANUNUZI
            @elseif($reportType === 'matumizi')
                RIPOTI YA MATUMIZI
            @elseif($reportType === 'general')
                RIPOTI YA JUMLA YA BIASHARA
            @endif
        </div>
        <div class="report-period">
            @if($selectedPeriod === 'custom' && $dateFrom && $dateTo)
                Kipindi: {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}
            @elseif($selectedPeriod === 'today')
                Kipindi: Leo
            @elseif($selectedPeriod === 'yesterday')
                Kipindi: Jana
            @elseif($selectedPeriod === 'week')
                Kipindi: Wiki hii
            @elseif($selectedPeriod === 'month')
                Kipindi: Mwezi huu
            @elseif($selectedPeriod === 'year')
                Kipindi: Mwaka huu
            @endif
        </div>
        <div class="report-date">
            Tarehe: {{ $date }} | Muda: {{ $currentTime }}
        </div>
    </div>

    @if($reportType === 'sales')
        <!-- SALES REPORT -->
        <div class="section-title">MAPATO KULINGANA NA NJIA YA MALIPO</div>
        
        <!-- Compact 3x2 Grid for Income Summary -->
        <div class="grid-3x2 page-break-avoid">
            <!-- Cash Income -->
            <div class="grid-item">
                <div class="grid-item-header">CASH (FEDHA TASLIMU)</div>
                <div class="grid-item-content">
                    <div class="grid-line">
                        <span class="grid-label">Mauzo:</span>
                        <span class="grid-value">{{ number_format($totalCashSales ?? 0, 2) }} TZS</span>
                    </div>
                    <div class="grid-line">
                        <span class="grid-label">Madeni:</span>
                        <span class="grid-value">{{ number_format($totalCashDebts ?? 0, 2) }} TZS</span>
                    </div>
                    <div class="grid-total">
                        <span>Jumla Cash:</span>
                        <span>{{ number_format($totalCashIncome ?? 0, 2) }} TZS</span>
                    </div>
                </div>
            </div>
            
            <!-- Mobile Money Income -->
            <div class="grid-item">
                <div class="grid-item-header">LIPA NAMBA</div>
                <div class="grid-item-content">
                    <div class="grid-line">
                        <span class="grid-label">Mauzo:</span>
                        <span class="grid-value">{{ number_format($totalMobileSales ?? 0, 2) }} TZS</span>
                    </div>
                    <div class="grid-line">
                        <span class="grid-label">Madeni:</span>
                        <span class="grid-value">{{ number_format($totalMobileDebts ?? 0, 2) }} TZS</span>
                    </div>
                    <div class="grid-total">
                        <span>Jumla Mobile:</span>
                        <span>{{ number_format($totalMobileIncome ?? 0, 2) }} TZS</span>
                    </div>
                </div>
            </div>
            
            <!-- Bank Income -->
            <div class="grid-item">
                <div class="grid-item-header">BANK (BENKI)</div>
                <div class="grid-item-content">
                    <div class="grid-line">
                        <span class="grid-label">Mauzo:</span>
                        <span class="grid-value">{{ number_format($totalBankSales ?? 0, 2) }} TZS</span>
                    </div>
                    <div class="grid-line">
                        <span class="grid-label">Madeni:</span>
                        <span class="grid-value">{{ number_format($totalBankDebts ?? 0, 2) }} TZS</span>
                    </div>
                    <div class="grid-total">
                        <span>Jumla Bank:</span>
                        <span>{{ number_format($totalBankIncome ?? 0, 2) }} TZS</span>
                    </div>
                </div>
            </div>
            
            <!-- Totals Row -->
            <div class="grid-item">
                <div class="grid-item-header">JUMLA MAUZO</div>
                <div class="grid-item-content">
                    <div class="grid-line">
                        <span>Cash:</span>
                        <span>{{ number_format($totalCashSales ?? 0, 2) }} TZS</span>
                    </div>
                    <div class="grid-line">
                        <span>Mobile:</span>
                        <span>{{ number_format($totalMobileSales ?? 0, 2) }} TZS</span>
                    </div>
                    <div class="grid-line">
                        <span>Bank:</span>
                        <span>{{ number_format($totalBankSales ?? 0, 2) }} TZS</span>
                    </div>
                    <div class="grid-total">
                        <span>Total Mauzo:</span>
                        <span>{{ number_format($totalSales ?? 0, 2) }} TZS</span>
                    </div>
                </div>
            </div>
            
            <!-- Debt Payments Row -->
            <div class="grid-item">
                <div class="grid-item-header">JUMLA MADENI</div>
                <div class="grid-item-content">
                    <div class="grid-line">
                        <span>Cash:</span>
                        <span>{{ number_format($totalCashDebts ?? 0, 2) }} TZS</span>
                    </div>
                    <div class="grid-line">
                        <span>Mobile:</span>
                        <span>{{ number_format($totalMobileDebts ?? 0, 2) }} TZS</span>
                    </div>
                    <div class="grid-line">
                        <span>Bank:</span>
                        <span>{{ number_format($totalBankDebts ?? 0, 2) }} TZS</span>
                    </div>
                    <div class="grid-total">
                        <span>Total Madeni:</span>
                        <span>{{ number_format($totalDebtRepayments ?? 0, 2) }} TZS</span>
                    </div>
                </div>
            </div>
            
            <!-- Grand Total -->
            <div class="grid-item grid-item-total">
                <div class="grid-item-header">JUMLA YOTE</div>
                <div class="grid-item-content">
                    <div class="grid-line-large">
                        <span>Jumla ya Mapato:</span>
                        <span>{{ number_format($grandTotal ?? 0, 2) }} TZS</span>
                    </div>
                    <div class="grid-total-grand">
                        <span>JUMLA YA MAPATO YOTE:</span>
                        <span>{{ number_format($grandTotal ?? 0, 2) }} TZS</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Detailed Sales Table Grouped by Date -->
        @if(isset($salesByDate) && count($salesByDate) > 0)
            <div class="section-title">ORODHA YA MAUZO KWA TAREHE</div>
            
            @foreach($salesByDate as $dateKey => $dateData)
                <div class="date-group page-break-avoid">
                    <div class="date-header">
                        Tarehe: {{ $dateData['date'] }} | 
                        Jumla: {{ number_format($dateData['total'], 2) }} TZS |
                        Cash: {{ number_format($dateData['cash'], 2) }} TZS |
                        Mobile: {{ number_format($dateData['mobile'], 2) }} TZS |
                        Bank: {{ number_format($dateData['bank'], 2) }} TZS
                    </div>
                    
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Bidhaa</th>
                                <th class="text-right">Idadi</th>
                                <th class="text-center">Njia ya Malipo</th>
                                <th class="text-right">Jumla (TZS)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dateData['sales'] as $index => $sale)
                                @if(!$sale->bidhaa) @continue @endif
                                @php
                                    $paymentMethod = $sale->lipa_kwa ?? 'cash';
                                    $rowClass = '';
                                    if($paymentMethod === 'cash') {
                                        $rowClass = 'cash-row';
                                    } elseif($paymentMethod === 'lipa_namba') {
                                        $rowClass = 'mobile-row';
                                    } elseif($paymentMethod === 'bank') {
                                        $rowClass = 'bank-row';
                                    }
                                @endphp
                                <tr class="{{ $rowClass }}">
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $sale->bidhaa->jina ?? 'N/A' }}</td>
                                    <td class="text-right">{{ number_format($sale->idadi) }}</td>
                                    <td class="text-center">
                                        @if($paymentMethod === 'cash')
                                            Cash
                                        @elseif($paymentMethod === 'lipa_namba')
                                            Lipa Namba
                                        @elseif($paymentMethod === 'bank')
                                            Bank
                                        @else
                                            Cash
                                        @endif
                                    </td>
                                    <td class="text-right">{{ number_format($sale->jumla, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
            
        @elseif(isset($sales) && count($sales) > 0)
            <!-- Fallback to regular table if not grouped by date -->
            <div class="section-title">ORODHA YA MAUZO</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Bidhaa</th>
                        <th class="text-right">Idadi</th>
                        <th class="text-center">Njia ya Malipo</th>
                        <th class="text-right">Jumla (TZS)</th>
                        <th>Tarehe</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sales as $index => $sale)
                        @if(!$sale->bidhaa) @continue @endif
                        @php
                            $paymentMethod = $sale->lipa_kwa ?? 'cash';
                            $rowClass = '';
                            if($paymentMethod === 'cash') {
                                $rowClass = 'cash-row';
                            } elseif($paymentMethod === 'lipa_namba') {
                                $rowClass = 'mobile-row';
                            } elseif($paymentMethod === 'bank') {
                                $rowClass = 'bank-row';
                            }
                        @endphp
                        <tr class="{{ $rowClass }}">
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $sale->bidhaa->jina ?? 'N/A' }}</td>
                            <td class="text-right">{{ number_format($sale->idadi) }}</td>
                            <td class="text-center">
                                @if($paymentMethod === 'cash')
                                    Cash
                                @elseif($paymentMethod === 'lipa_namba')
                                    Lipa Namba
                                @elseif($paymentMethod === 'bank')
                                    Bank
                                @else
                                    Cash
                                @endif
                            </td>
                            <td class="text-right">{{ number_format($sale->jumla, 2) }}</td>
                            <td class="text-center">{{ date('d/m/Y', strtotime($sale->created_at)) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">Hakuna mauzo katika kipindi hiki</div>
        @endif
        
        <!-- Grand Total -->
        <div class="grand-total page-break-avoid">
            JUMLA YA MAPATO YOTE: {{ number_format($grandTotal ?? 0, 2) }} TZS
        </div>

    @elseif($reportType === 'manunuzi')
        <!-- PURCHASES REPORT -->
        @if(isset($manunuzi) && count($manunuzi) > 0)
            <div class="section-title">ORODHA YA MANUNUZI</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Bidhaa</th>
                        <th class="text-right">Idadi</th>
                        <th class="text-right">Bei (TZS)</th>
                        <th class="text-center">Tarehe</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($manunuzi as $index => $purchase)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $purchase->bidhaa->jina ?? 'N/A' }}</td>
                            <td class="text-right">{{ number_format($purchase->idadi) }}</td>
                            <td class="text-right">{{ number_format($purchase->bei, 2) }}</td>
                            <td class="text-center">{{ date('d/m/Y', strtotime($purchase->created_at)) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Total -->
            <div class="grand-total page-break-avoid">
                JUMLA YA GHARAMA ZA MANUNUZI: {{ number_format($totalCost ?? 0, 2) }} TZS
            </div>
        @else
            <div class="no-data">Hakuna manunuzi katika kipindi hiki</div>
        @endif

    @elseif($reportType === 'matumizi')
        <!-- EXPENSES REPORT -->
        @if(isset($matumizi) && count($matumizi) > 0)
            <div class="section-title">ORODHA YA MATUMIZI</div>
            
            <!-- Summary by Category -->
            @if(isset($totalsByCategory) && count($totalsByCategory) > 0)
            <div class="grid-3x2 page-break-avoid">
                @php $categoryCount = 0; @endphp
                @foreach($totalsByCategory as $category => $total)
                    @php $categoryCount++; @endphp
                    <div class="grid-item">
                        <div class="grid-item-header">{{ $category }}</div>
                        <div class="grid-item-content text-center">
                            <div style="font-size: 9px; font-weight: bold; padding: 3px 0;">
                                {{ number_format($total, 2) }} TZS
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @endif
            
            <!-- Detailed Expenses Table -->
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tarehe</th>
                        <th>Aina</th>
                        <th>Maelezo</th>
                        <th class="text-right">Gharama (TZS)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($matumizi as $index => $expense)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ date('d/m/Y', strtotime($expense->created_at)) }}</td>
                            <td>{{ $expense->aina ?: 'Zingine' }}</td>
                            <td>{{ $expense->maelezo ?: '--' }}</td>
                            <td class="text-right">{{ number_format($expense->gharama, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Total -->
            <div class="grand-total page-break-avoid">
                JUMLA YA MATUMIZI: {{ number_format($totalExpenses, 2) }} TZS
            </div>
        @else
            <div class="no-data">Hakuna matumizi katika kipindi hiki</div>
        @endif

    @else
        <!-- GENERAL REPORT -->
        <div class="section-title">MUHTASARI WA MAPATO KULINGANA NA NJIA YA MALIPO</div>
        
        <!-- 3x2 Grid for Payment Methods -->
        <div class="grid-3x2 page-break-avoid">
            <!-- Cash Summary -->
            <div class="grid-item">
                <div class="grid-item-header">CASH (FEDHA TASLIMU)</div>
                <div class="grid-item-content">
                    <div class="grid-line">
                        <span class="grid-label">Mauzo ya Cash:</span>
                        <span class="grid-value">{{ number_format($mapatoCashMauzo ?? 0, 2) }} TZS</span>
                    </div>
                    <div class="grid-line">
                        <span class="grid-label">Malipo ya Madeni:</span>
                        <span class="grid-value">{{ number_format($mapatoCashMadeni ?? 0, 2) }} TZS</span>
                    </div>
                    <div class="grid-total">
                        <span>Jumla ya Cash:</span>
                        <span>{{ number_format($jumlaMapatoCash ?? 0, 2) }} TZS</span>
                    </div>
                </div>
            </div>
            
            <!-- Mobile Money Summary -->
            <div class="grid-item">
                <div class="grid-item-header">LIPA NAMBA</div>
                <div class="grid-item-content">
                    <div class="grid-line">
                        <span class="grid-label">Mauzo ya Lipa Namba:</span>
                        <span class="grid-value">{{ number_format($mapatoMobileMauzo ?? 0, 2) }} TZS</span>
                    </div>
                    <div class="grid-line">
                        <span class="grid-label">Malipo ya Madeni:</span>
                        <span class="grid-value">{{ number_format($mapatoMobileMadeni ?? 0, 2) }} TZS</span>
                    </div>
                    <div class="grid-total">
                        <span>Jumla ya Lipa Namba:</span>
                        <span>{{ number_format($jumlaMapatoMobile ?? 0, 2) }} TZS</span>
                    </div>
                </div>
            </div>
            
            <!-- Bank Summary -->
            <div class="grid-item">
                <div class="grid-item-header">BANK (BENKI)</div>
                <div class="grid-item-content">
                    <div class="grid-line">
                        <span class="grid-label">Mauzo ya Bank:</span>
                        <span class="grid-value">{{ number_format($mapatoBankMauzo ?? 0, 2) }} TZS</span>
                    </div>
                    <div class="grid-line">
                        <span class="grid-label">Malipo ya Madeni:</span>
                        <span class="grid-value">{{ number_format($mapatoBankMadeni ?? 0, 2) }} TZS</span>
                    </div>
                    <div class="grid-total">
                        <span>Jumla ya Bank:</span>
                        <span>{{ number_format($jumlaMapatoBank ?? 0, 2) }} TZS</span>
                    </div>
                </div>
            </div>
            
            <!-- Overall Income Summary -->
            <div class="grid-item grid-item-total">
                <div class="grid-item-header">JUMLA YA MAPATO</div>
                <div class="grid-item-content">
                    <div class="grid-line">
                        <span class="grid-label">Jumla ya Mauzo:</span>
                        <span class="grid-value">{{ number_format($mapatoMauzo ?? 0, 2) }} TZS</span>
                    </div>
                    <div class="grid-line">
                        <span class="grid-label">Jumla ya Madeni:</span>
                        <span class="grid-value">{{ number_format($mapatoMadeni ?? 0, 2) }} TZS</span>
                    </div>
                    <div class="grid-total-grand">
                        <span>JUMLA YOTE:</span>
                        <span>{{ number_format($jumlaMapato ?? 0, 2) }} TZS</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Business Summary -->
        <div class="section-title">MUHTASARI WA JUMLA YA BIASHARA</div>
        
        <div class="grid-3x2 page-break-avoid">
            <!-- Mapato Summary -->
            <div class="grid-item">
                <div class="grid-item-header">MAPATO</div>
                <div class="grid-item-content">
                    <div class="grid-line">
                        <span class="grid-label">Jumla ya Mauzo:</span>
                        <span class="grid-value">{{ number_format($mapatoMauzo ?? 0, 2) }} TZS</span>
                    </div>
                    <div class="grid-line">
                        <span class="grid-label">Jumla ya Madeni:</span>
                        <span class="grid-value">{{ number_format($mapatoMadeni ?? 0, 2) }} TZS</span>
                    </div>
                    <div class="grid-total">
                        <span>Jumla ya Mapato:</span>
                        <span>{{ number_format($jumlaMapato ?? 0, 2) }} TZS</span>
                    </div>
                </div>
            </div>
            
            <!-- Faida Summary -->
            <div class="grid-item">
                <div class="grid-item-header">FAIDA</div>
                <div class="grid-item-content">
                    <div class="grid-line">
                        <span class="grid-label">Faida ya Mauzo:</span>
                        <span class="grid-value">{{ number_format($faidaMauzo ?? 0, 2) }} TZS</span>
                    </div>
                    <div class="grid-line">
                        <span class="grid-label">Faida ya Marejesho:</span>
                        <span class="grid-value">{{ number_format($faidaMarejesho ?? 0, 2) }} TZS</span>
                    </div>
                    <div class="grid-total">
                        <span>Faida Halisi:</span>
                        <span>{{ number_format($faidaHalisi ?? 0, 2) }} TZS</span>
                    </div>
                </div>
            </div>
            
            <!-- Gharama na Mtaa -->
            <div class="grid-item">
                <div class="grid-item-header">GHARAMA NA FEDHA HALISI</div>
                <div class="grid-item-content">
                    <div class="grid-line">
                        <span class="grid-label">Jumla ya Matumizi:</span>
                        <span class="grid-value">{{ number_format($jumlaMatumizi ?? 0, 2) }} TZS</span>
                    </div>
                    <div class="grid-line">
                        <span class="grid-label">Fedha Dukani:</span>
                        <span class="grid-value">{{ number_format($fedhaDukani ?? 0, 2) }} TZS</span>
                    </div>
                </div>
            </div>
            
            <!-- Summary -->
            <div class="grid-item grid-item-total">
                <div class="grid-item-header">MUHTASARI</div>
                <div class="grid-item-content">
                    <div class="grid-line">
                        <span class="grid-label">Jumla ya Mapato:</span>
                        <span class="grid-value">{{ number_format($jumlaMapato ?? 0, 2) }} TZS</span>
                    </div>
                    <div class="grid-line">
                        <span class="grid-label">Jumla ya Matumizi:</span>
                        <span class="grid-value">{{ number_format($jumlaMatumizi ?? 0, 2) }} TZS</span>
                    </div>
                    <div class="grid-total-grand">
                        <span>Fedha Dukani:</span>
                        <span>{{ number_format($fedhaDukani ?? 0, 2) }} TZS</span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <div>{{ $companyName ?? 'Biashara' }} &copy; {{ date('Y') }}</div>
        <div>Imechapishwa: {{ date('d/m/Y H:i:s') }}</div>
    </div>
</body>
</html>