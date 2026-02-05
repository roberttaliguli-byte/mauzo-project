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
        
        /* 2x2 Grid Styles - Compact */
        .grid-2x2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-template-rows: auto auto;
            gap: 10px;
            margin: 15px 0;
        }
        
        .grid-item {
            border: 1px solid #000;
            padding: 8px;
            border-radius: 4px;
            background: #f9f9f9;
        }
        
        .grid-item-cash {
            border-color: #28a745;
            background: #d4edda;
        }
        
        .grid-item-mobile {
            border-color: #007bff;
            background: #cce5ff;
        }
        
        .grid-item-bank {
            border-color: #6f42c1;
            background: #e0d7f7;
        }
        
        .grid-item-total {
            border-color: #000;
            background: #f8f9fa;
            font-weight: bold;
        }
        
        .grid-item-header {
            font-weight: bold;
            margin-bottom: 8px;
            padding-bottom: 4px;
            border-bottom: 1px solid #ccc;
            text-align: center;
            font-size: 10px;
        }
        
        .grid-item-content {
            font-size: 9px;
        }
        
        .grid-line {
            display: flex;
            justify-content: space-between;
            margin: 4px 0;
            padding: 2px 0;
        }
        
        .grid-label {
            font-weight: normal;
        }
        
        .grid-value {
            font-weight: bold;
        }
        
        .grid-total {
            border-top: 1px solid #000;
            margin-top: 6px;
            padding-top: 6px;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            font-size: 10px;
        }
        
        .section-title {
            font-size: 12px;
            font-weight: bold;
            margin: 15px 0 8px 0;
            border-bottom: 1px solid #000;
            padding-bottom: 3px;
        }
        
        .grand-total {
            margin-top: 15px;
            padding: 12px;
            border: 2px solid #000;
            background: #f0f0f0;
            font-weight: bold;
            text-align: center;
            font-size: 14px;
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
        
        <!-- 2x2 Grid for Income Summary -->
        <div class="grid-2x2 page-break-avoid">
            <!-- Cash Income -->
            <div class="grid-item grid-item-cash">
                <div class="grid-item-header">CASH (FEDHA TASLIMU)</div>
                <div class="grid-item-content">
                    <div class="grid-line">
                        <span class="grid-label">Mauzo ya Cash:</span>
                        <span class="grid-value">{{ number_format($totalCashSales ?? 0, 2) }} TZS</span>
                    </div>
                    <div class="grid-line">
                        <span class="grid-label">Malipo ya Madeni:</span>
                        <span class="grid-value">{{ number_format($totalCashDebts ?? 0, 2) }} TZS</span>
                    </div>
                    <div class="grid-total">
                        <span>Jumla ya Cash:</span>
                        <span>{{ number_format($totalCashIncome ?? 0, 2) }} TZS</span>
                    </div>
                </div>
            </div>
            
            <!-- Mobile Money Income -->
            <div class="grid-item grid-item-mobile">
                <div class="grid-item-header">LIPA NAMBA</div>
                <div class="grid-item-content">
                    <div class="grid-line">
                        <span class="grid-label">Mauzo ya Lipa Namba:</span>
                        <span class="grid-value">{{ number_format($totalMobileSales ?? 0, 2) }} TZS</span>
                    </div>
                    <div class="grid-line">
                        <span class="grid-label">Malipo ya Madeni:</span>
                        <span class="grid-value">{{ number_format($totalMobileDebts ?? 0, 2) }} TZS</span>
                    </div>
                    <div class="grid-total">
                        <span>Jumla ya Lipa Namba:</span>
                        <span>{{ number_format($totalMobileIncome ?? 0, 2) }} TZS</span>
                    </div>
                </div>
            </div>
            
            <!-- Bank Income -->
            <div class="grid-item grid-item-bank">
                <div class="grid-item-header">BANK (BENKI)</div>
                <div class="grid-item-content">
                    <div class="grid-line">
                        <span class="grid-label">Mauzo ya Bank:</span>
                        <span class="grid-value">{{ number_format($totalBankSales ?? 0, 2) }} TZS</span>
                    </div>
                    <div class="grid-line">
                        <span class="grid-label">Malipo ya Madeni:</span>
                        <span class="grid-value">{{ number_format($totalBankDebts ?? 0, 2) }} TZS</span>
                    </div>
                    <div class="grid-total">
                        <span>Jumla ya Bank:</span>
                        <span>{{ number_format($totalBankIncome ?? 0, 2) }} TZS</span>
                    </div>
                </div>
            </div>
            
            <!-- Grand Total -->
            <div class="grid-item grid-item-total">
                <div class="grid-item-header">JUMLA YA MAPATO YOTE</div>
                <div class="grid-item-content">
                    <div class="grid-line">
                        <span class="grid-label">Jumla ya Mauzo:</span>
                        <span class="grid-value">{{ number_format(($totalCashSales ?? 0) + ($totalMobileSales ?? 0) + ($totalBankSales ?? 0), 2) }} TZS</span>
                    </div>
                    <div class="grid-line">
                        <span class="grid-label">Jumla ya Madeni:</span>
                        <span class="grid-value">{{ number_format(($totalCashDebts ?? 0) + ($totalMobileDebts ?? 0) + ($totalBankDebts ?? 0), 2) }} TZS</span>
                    </div>
                    <div class="grid-total">
                        <span>MAPATO YOTE:</span>
                        <span>{{ number_format($grandTotal ?? 0, 2) }} TZS</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Detailed Sales Table -->
        @if(isset($sales) && count($sales) > 0)
            <div class="section-title">ORODHA YA MAUZO</div>
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
                    @foreach($sales as $index => $sale)
                        @if(!$sale->bidhaa) @continue @endif
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $sale->bidhaa->jina ?? 'N/A' }}</td>
                            <td class="text-right">{{ number_format($sale->idadi) }}</td>
                            <td class="text-center">
                                @php
                                    $paymentMethod = $sale->lipa_kwa ?? 'cash';
                                    if($paymentMethod === 'cash') {
                                        echo 'Cash';
                                    } elseif($paymentMethod === 'lipa_namba') {
                                        echo 'Lipa Namba';
                                    } elseif($paymentMethod === 'bank') {
                                        echo 'Bank';
                                    } else {
                                        echo 'Cash';
                                    }
                                @endphp
                            </td>
                            <td class="text-right">{{ number_format($sale->jumla, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
        
        <!-- Grand Total -->
        <div class="grand-total page-break-avoid">
            JUMLA YA MAPATO YOTE: {{ number_format($grandTotal ?? 0, 2) }} TZS
        </div>

@elseif($reportType === 'manunuzi')
    <!-- PURCHASES REPORT - FIXED: Proper calculation -->
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
            <div class="grid-2x2 page-break-avoid">
                @php $categoryCount = 0; @endphp
                @foreach($totalsByCategory as $category => $total)
                    @php
                        $categoryCount++;
                        $bgColor = $categoryCount % 4 == 1 ? 'bg-blue-50' : 
                                  ($categoryCount % 4 == 2 ? 'bg-green-50' : 
                                  ($categoryCount % 4 == 3 ? 'bg-amber-50' : 'bg-red-50'));
                    @endphp
                    <div class="grid-item {{ $bgColor }}">
                        <div class="grid-item-header">{{ $category }}</div>
                        <div class="grid-item-content text-center">
                            <div class="text-lg font-bold">{{ number_format($total, 2) }} TZS</div>
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
        
        <!-- 2x2 Grid for Payment Methods -->
        <div class="grid-2x2 page-break-avoid">
            <!-- Cash Summary -->
            <div class="grid-item grid-item-cash">
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
            <div class="grid-item grid-item-mobile">
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
            <div class="grid-item grid-item-bank">
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
                    <div class="grid-total">
                        <span>JUMLA YOTE:</span>
                        <span>{{ number_format($jumlaMapato ?? 0, 2) }} TZS</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Business Summary -->
        <div class="section-title">MUHTASARI WA JUMLA YA BIASHARA</div>
        
        <div class="grid-2x2 page-break-avoid">
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
                    <div class="grid-total">
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