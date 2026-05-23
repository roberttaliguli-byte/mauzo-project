<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $reportTitle ?? 'Ripoti' }}</title>
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

        /* --- BLACK & WHITE TABLES FOR ALL DATA --- */
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 10px;
            table-layout: fixed;
            border: 1px solid #000;
        }

        .summary-table td, .summary-table th {
            border: 1px solid #000;
            padding: 8px 5px;
            text-align: center;
            vertical-align: middle;
        }

        .summary-table th {
            background: #f0f0f0;
            font-weight: bold;
        }

        .summary-table td {
            background: #ffffff;
        }

        /* for tables that need two columns or special layouts */
        .summary-table td.label-cell {
            font-weight: bold;
            background: #f9f9f9;
            width: 40%;
        }

        .summary-table td.value-cell {
            width: 60%;
            font-weight: normal;
        }

        /* table within table for grouped data (pure B&W) */
        .inner-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
            border: none;
        }

        .inner-table td {
            border: none;
            padding: 3px 2px;
            background: transparent;
            text-align: left;
        }

        .inner-table td:last-child {
            text-align: right;
            font-weight: bold;
        }

        /* main data table (for details) */
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

        .grand-total {
            margin-top: 15px;
            padding: 10px;
            border: 2px solid #000;
            background: #f0f0f0;
            font-weight: bold;
            text-align: center;
            font-size: 13px;
            border-radius: 0;
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
        .text-left { text-align: left; }

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

        /* B&W row indicators - only borders, no colors */
        .cash-row td, .mobile-row td, .bank-row td {
            background: #ffffff;
        }

        /* additional spacing */
        .section-title {
            font-size: 12px;
            font-weight: bold;
            margin: 20px 0 5px 0;
            text-align: left;
            border-bottom: 1px solid #000;
            padding-bottom: 2px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="company-name">{{ $companyName ?? 'Biashara' }}</div>
        <div class="report-title">{{ $reportTitle ?? 'Ripoti' }}</div>
        @if(!empty($reportSubtitle))
        <div class="report-subtitle">{{ $reportSubtitle }}</div>
        @endif
        <div class="report-period">
            @if(!empty($displayFrom) && !empty($displayTo))
                Kipindi: {{ $displayFrom }} - {{ $displayTo }}
            @elseif($dateRange === 'today')
                Kipindi: Leo
            @elseif($dateRange === 'yesterday')
                Kipindi: Jana
            @elseif($dateRange === 'week')
                Kipindi: Wiki hii
            @elseif($dateRange === 'month')
                Kipindi: Mwezi huu
            @elseif($dateRange === 'year')
                Kipindi: Mwaka huu
            @endif
        </div>
        <div class="report-date">
            Tarehe: {{ $generatedAt ?? now()->format('d/m/Y H:i:s') }}
        </div>
    </div>

@elseif($reportType === 'sales')
    <!-- SALES REPORT - summary table -->
    <table class="summary-table page-break-avoid">
        <tr>
            <th>Jumla ya Mauzo</th>
            <th>Marejesho ya Madeni</th>
            <th>Jumla ya Mapato</th>
            <th>Idadi ya Mauzo</th>
        </tr>
        <tr>
            <td>{{ number_format($totalSales ?? 0, 2) }} TZS</td>
            <td>{{ number_format($totalDebtRepayments ?? 0, 2) }} TZS</td>
            <td>{{ number_format($grandTotal ?? 0, 2) }} TZS</td>
            <td>{{ $sales ? count($sales) : 0 }}</td>
        </tr>
    </table>

    <!-- Payment methods summary table with types -->
    <table class="summary-table page-break-avoid" style="margin-top:8px;">
        <thead>
            <tr>
                <th colspan="2">Cash</th>
                <th colspan="3">Lipa Namba</th>
                <th colspan="3">Benki</th>
            </tr>
            <tr>
                <th>Aina</th>
                <th>Kiasi (TZS)</th>
                <th>Aina</th>
                <th>Idadi</th>
                <th>Kiasi (TZS)</th>
                <th>Aina</th>
                <th>Idadi</th>
                <th>Kiasi (TZS)</th>
            </tr>
        </thead>
        <tbody>
            <!-- Cash Row -->
            <tr>
                <td>Cash</td>
                <td class="text-right">{{ number_format($totalCashIncome ?? 0, 2) }}</td>
                @php
                    $mobileTypes = [
                        'mpesa' => 'M-Pesa',
                        'mixx_by_yas' => 'Mixx by Yas', 
                        'airtel_money' => 'Airtel Money',
                        'halopesa' => 'HaloPesa',
                        'other_lipa_namba' => 'Nyingine'
                    ];
                    $mobileCount = 0;
                @endphp
                @foreach($mobileTypes as $key => $label)
                    @php
                        $salesData = $salesByPaymentType[$key] ?? ['total' => 0, 'count' => 0];
                        $debtsData = $debtsByPaymentType[$key] ?? ['total' => 0, 'count' => 0];
                        $total = $salesData['total'] + $debtsData['total'];
                        $count = $salesData['count'] + $debtsData['count'];
                    @endphp
                    @if($total > 0 || $count > 0)
                        @if($mobileCount == 0)
                            <tr>
                                <td>{{ $label }}</td>
                                <td class="text-center">{{ $count }}</td>
                                <td class="text-right">{{ number_format($total, 2) }}</td>
                        @elseif($mobileCount == 1)
                                <td>{{ $label }}</td>
                                <td class="text-center">{{ $count }}</td>
                                <td class="text-right">{{ number_format($total, 2) }}</td>
                            </tr>
                        @elseif($mobileCount == 2)
                            <tr>
                                <td>{{ $label }}</td>
                                <td class="text-center">{{ $count }}</td>
                                <td class="text-right">{{ number_format($total, 2) }}</td>
                            </tr>
                        @endif
                        @php $mobileCount++; @endphp
                    @endif
                @endforeach
                @while($mobileCount < 3)
                    @if($mobileCount == 0)
                        <td>-</td><td class="text-center">0</td><td class="text-right">0.00</td>
                    @elseif($mobileCount == 1)
                        <td>-</td><td class="text-center">0</td><td class="text-right">0.00</td>
                    </td>
                    @elseif($mobileCount == 2)
                        <td>-</td><td class="text-center">0</td><td class="text-right">0.00</td>
                    </tr>
                    @endif
                    @php $mobileCount++; @endphp
                @endwhile
                
                @php
                    $bankTypes = [
                        'crdb' => 'CRDB',
                        'nmb' => 'NMB',
                        'nbc' => 'NBC',
                        'other_bank' => 'Nyingine'
                    ];
                    $bankCount = 0;
                @endphp
                @foreach($bankTypes as $key => $label)
                    @php
                        $salesData = $salesByPaymentType[$key] ?? ['total' => 0, 'count' => 0];
                        $debtsData = $debtsByPaymentType[$key] ?? ['total' => 0, 'count' => 0];
                        $total = $salesData['total'] + $debtsData['total'];
                        $count = $salesData['count'] + $debtsData['count'];
                    @endphp
                    @if($total > 0 || $count > 0)
                        @if($bankCount == 0)
                            <td>{{ $label }}</td><td class="text-center">{{ $count }}</td><td class="text-right">{{ number_format($total, 2) }}</td>
                        @elseif($bankCount == 1)
                            <td>{{ $label }}</td><td class="text-center">{{ $count }}</td><td class="text-right">{{ number_format($total, 2) }}</td>
                        @elseif($bankCount == 2)
                            <td>{{ $label }}</td><td class="text-center">{{ $count }}</td><td class="text-right">{{ number_format($total, 2) }}</td>
                        @endif
                        @php $bankCount++; @endphp
                    @endif
                @endforeach
                @while($bankCount < 3)
                    <td>-</td><td class="text-center">0</td><td class="text-right">0.00</td>
                    @php $bankCount++; @endphp
                @endwhile
            </tr>
        </tbody>
    </table>

    <!-- Sales details table with payment type -->
    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Tarehe</th>
                <th>Aina</th>
                <th>Bidhaa</th>
                <th>Aina ya Bidhaa</th>
                <th>Kipimo</th>
                <th class="text-center">Idadi</th>
                <th>Njia ya Malipo</th>
                <th class="text-right">Jumla (TZS)</th>
            </tr>
        </thead>
        <tbody>
            @php $index = 1; @endphp
            @forelse($allTransactions ?? [] as $transaction)
                <tr>
                    <td class="text-center">{{ $index++ }}</td>
                    <td>{{ date('d/m/Y', strtotime($transaction['date'])) }}</td>
                    <td>{{ $transaction['type'] }}</td>
                    <td>{{ $transaction['product_name'] }}</td>
                    <td>{{ $transaction['product_aina'] }}</td>
                    <td>{{ $transaction['product_kipimo'] }}</td>
                    <td class="text-center">{{ $transaction['idadi'] }}</td>
                    <td>{{ $transaction['payment_method'] }}</td>
                    <td class="text-right">{{ number_format($transaction['amount'], 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center no-data">Hakuna mauzo katika kipindi hiki</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="grand-total page-break-avoid">
        JUMLA YA MAPATO YOTE: {{ number_format($grandTotal ?? 0, 2) }} TZS
    </div>

    @elseif($reportType === 'manunuzi')
        <!-- PURCHASES REPORT - summary table -->
        <table class="summary-table page-break-avoid">
            <tr>
                <th>Jumla ya Gharama</th>
                <th>Jumla ya Bidhaa</th>
                <th>Wastani wa Bei</th>
                <th>Idadi ya Manunuzi</th>
            </tr>
            <tr>
                <td>{{ number_format($totalCost ?? 0, 2) }} TZS</td>
                <td>
                    @php
                        $totalItems = $totalItems ?? 0;
                        $formattedItems = is_numeric($totalItems) ? 
                            ($totalItems % 1 == 0 ? (string)(int)$totalItems : number_format($totalItems, 2)) : 
                            $totalItems;
                    @endphp
                    {{ $formattedItems }}
                </td>
                <td>{{ number_format($averageCost ?? 0, 2) }} TZS</td>
                <td>{{ $manunuzi ? count($manunuzi) : 0 }}</td>
            </tr>
        </table>

        <!-- Purchases table with Aina and Kipimo -->
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tarehe</th>
                    <th>Bidhaa</th>
                    <th>Aina</th>
                    <th>Kipimo</th>
                    <th class="text-center">Idadi</th>
                    <th class="text-right">Bei (TZS)</th>
                    <th class="text-right">Bei kwa Kimoja</th>
                </tr>
            </thead>
            <tbody>
                @forelse($manunuzi ?? [] as $index => $purchase)
                    @php
                        $idadi = $purchase->idadi;
                        $formattedIdadi = is_numeric($idadi) ? 
                            ($idadi % 1 == 0 ? (string)(int)$idadi : number_format($idadi, 2)) : 
                            $idadi;
                        $unitCost = $purchase->unit_cost ?? ($purchase->idadi > 0 ? $purchase->bei / $purchase->idadi : 0);
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $purchase->created_at ? date('d/m/Y', strtotime($purchase->created_at)) : '' }}</td>
                        <td>{{ $purchase->bidhaa->jina ?? 'N/A' }}</td>
                        <td>{{ $purchase->bidhaa->aina ?? 'N/A' }}</td>
                        <td>{{ $purchase->bidhaa->kipimo ?? 'N/A' }}</td>
                        <td class="text-center">{{ $formattedIdadi }}</td>
                        <td class="text-right">{{ number_format($purchase->bei, 2) }}</td>
                        <td class="text-right">{{ number_format($unitCost, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center no-data">Hakuna manunuzi katika kipindi hiki</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="grand-total page-break-avoid">
            JUMLA YA GHARAMA ZA MANUNUZI: {{ number_format($totalCost ?? 0, 2) }} TZS
        </div>

    @elseif($reportType === 'matumizi')
        <!-- EXPENSES REPORT - summary table (3 columns) -->
        <table class="summary-table page-break-avoid">
            <tr>
                <th>Jumla ya Matumizi</th>
                <th>Idadi ya Matumizi</th>
                <th>Aina za Matumizi</th>
            </tr>
            <tr>
                <td>{{ number_format($totalExpenses ?? 0, 2) }} TZS</td>
                <td>{{ $matumizi ? count($matumizi) : 0 }}</td>
                <td>{{ $totalsByCategory ? count($totalsByCategory) : 0 }}</td>
            </tr>
        </table>

        <!-- Category summary table (if available) -->
        @if(!empty($totalsByCategory) && count($totalsByCategory) > 0)
        <table class="summary-table page-break-avoid" style="margin-top:8px;">
            <tr>
                @foreach($totalsByCategory as $category => $total)
                    @if($loop->index < 4)
                    <th>{{ $category }}</th>
                    @endif
                @endforeach
            </tr>
            <tr>
                @foreach($totalsByCategory as $category => $total)
                    @if($loop->index < 4)
                    <td>{{ number_format($total, 2) }} TZS</td>
                    @endif
                @endforeach
            </tr>
        </table>
        @endif

        <!-- Expenses table -->
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
                @forelse($matumizi ?? [] as $index => $expense)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $expense->created_at ? date('d/m/Y', strtotime($expense->created_at)) : '' }}</td>
                        <td>{{ $expense->aina ?: 'Zingine' }}</td>
                        <td>{{ $expense->maelezo ?: '--' }}</td>
                        <td class="text-right">{{ number_format($expense->gharama, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center no-data">Hakuna matumizi katika kipindi hiki</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="grand-total page-break-avoid">
            JUMLA YA MATUMIZI: {{ number_format($totalExpenses ?? 0, 2) }} TZS
        </div>

    @elseif($reportType === 'general')
        <!-- GENERAL REPORT - all data in black & white tables -->

        <!-- First table: Payment methods -->
        <table class="summary-table page-break-avoid">
            <tr>
                <th>Cash</th>
                <th>Lipa Namba</th>
                <th>Benki</th>
                <th>Jumla ya Mapato</th>
            </tr>
            <tr>
                <td>{{ number_format($jumlaMapatoCash ?? 0, 2) }} TZS</td>
                <td>{{ number_format($jumlaMapatoMobile ?? 0, 2) }} TZS</td>
                <td>{{ number_format($jumlaMapatoBank ?? 0, 2) }} TZS</td>
                <td>{{ number_format($jumlaMapato ?? 0, 2) }} TZS</td>
            </tr>
        </table>

        <!-- Second table: Profit & Loss -->
        <table class="summary-table page-break-avoid" style="margin-top:8px;">
            <tr>
                <th>Faida ya Mauzo</th>
                <th>Faida ya Marejesho</th>
                <th>Matumizi</th>
                <th>Fedha Dukani</th>
            </tr>
            <tr>
                <td>{{ number_format($faidaMauzo ?? 0, 2) }} TZS</td>
                <td>{{ number_format($faidaMarejesho ?? 0, 2) }} TZS</td>
                <td>{{ number_format($jumlaMatumizi ?? 0, 2) }} TZS</td>
                <td>{{ number_format($fedhaDukani ?? 0, 2) }} TZS</td>
            </tr>
        </table>

        <!-- Third table: Faida Halisi (centered via colspan) -->
        <table class="summary-table page-break-avoid" style="margin-top:8px; width:60%; margin-left:auto; margin-right:auto;">
            <tr>
                <th style="text-align:center;">Faida Halisi</th>
            </tr>
            <tr>
                <td style="text-align:center; font-weight:bold; font-size:14px;">{{ number_format($faidaHalisi ?? 0, 2) }} TZS</td>
            </tr>
        </table>

        <!-- Detailed income/expense comparison table -->
        <table class="data-table">
            <thead>
                <tr>
                    <th colspan="2" style="text-align: center; background: #f0f0f0;">Muhtasari wa Mapato</th>
                    <th colspan="2" style="text-align: center; background: #f0f0f0;">Muhtasari wa Matumizi</th>
                </tr>
                <tr>
                    <th>Aina</th>
                    <th class="text-right">Kiasi (TZS)</th>
                    <th>Aina</th>
                    <th class="text-right">Kiasi (TZS)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Mauzo ya Cash</td>
                    <td class="text-right">{{ number_format($mapatoCashMauzo ?? 0, 2) }}</td>
                    <td>Matumizi</td>
                    <td class="text-right">{{ number_format($jumlaMatumizi ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <td>Mauzo ya Lipa Namba</td>
                    <td class="text-right">{{ number_format($mapatoMobileMauzo ?? 0, 2) }}</td>
                    <td></td>
                    <td class="text-right"></td>
                </tr>
                <tr>
                    <td>Mauzo ya Benki</td>
                    <td class="text-right">{{ number_format($mapatoBankMauzo ?? 0, 2) }}</td>
                    <td></td>
                    <td class="text-right"></td>
                </tr>
                <tr>
                    <td>Marejesho ya Madeni (Cash)</td>
                    <td class="text-right">{{ number_format($mapatoCashMadeni ?? 0, 2) }}</td>
                    <td></td>
                    <td class="text-right"></td>
                </tr>
                <tr>
                    <td>Marejesho ya Madeni (Lipa Namba)</td>
                    <td class="text-right">{{ number_format($mapatoMobileMadeni ?? 0, 2) }}</td>
                    <td></td>
                    <td class="text-right"></td>
                </tr>
                <tr>
                    <td>Marejesho ya Madeni (Benki)</td>
                    <td class="text-right">{{ number_format($mapatoBankMadeni ?? 0, 2) }}</td>
                    <td></td>
                    <td class="text-right"></td>
                </tr>
                <tr style="font-weight: bold; background: #f0f0f0;">
                    <td>Jumla ya Mapato</td>
                    <td class="text-right">{{ number_format($jumlaMapato ?? 0, 2) }}</td>
                    <td>Jumla ya Matumizi</td>
                    <td class="text-right">{{ number_format($jumlaMatumizi ?? 0, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="grand-total page-break-avoid">
            FEDHA DUKANI: {{ number_format($fedhaDukani ?? 0, 2) }} TZS
        </div>

@elseif($reportType === 'mapato_by_method')
    <!-- INCOME BY PAYMENT METHOD - summary table -->
    <table class="summary-table page-break-avoid">
        <tr>
            <th>Cash</th>
            <th>Lipa Namba</th>
            <th>Benki</th>
            <th>Jumla Kuu</th>
        </tr>
        <tr>
            <td>{{ number_format($totalCash ?? 0, 2) }} TZS</td>
            <td>{{ number_format($totalMobile ?? 0, 2) }} TZS</td>
            <td>{{ number_format($totalBank ?? 0, 2) }} TZS</td>
            <td>{{ number_format($grandTotal ?? 0, 2) }} TZS</td>
        </tr>
    </table>

    <!-- Detailed breakdown by payment type -->
    <table class="data-table" style="margin-top:15px;">
        <thead>
            <tr>
                <th>#</th>
                <th>Aina ya Malipo</th>
                <th>Chanzo</th>
                <th class="text-center">Idadi</th>
                <th class="text-right">Jumla (TZS)</th>
            </tr>
        </thead>
        <tbody>
            @php $index = 1; @endphp
            
            <!-- Cash -->
            @php
                $cashSalesCount = $salesByMethod->where('lipa_kwa', 'cash')->first()->count ?? 0;
                $cashDebtsCount = $debtsByMethod->where('lipa_kwa', 'cash')->first()->count ?? 0;
                $cashSalesTotal = $salesByMethod->where('lipa_kwa', 'cash')->first()->total ?? 0;
                $cashDebtsTotal = $debtsByMethod->where('lipa_kwa', 'cash')->first()->total ?? 0;
            @endphp
            <tr style="background:#f0f0f0; font-weight:bold;">
                <td colspan="5">💰 CASH</td>
            </tr>
            <tr>
                <td class="text-center">{{ $index++ }}</td>
                <td>Cash</td>
                <td>Mauzo</td>
                <td class="text-center">{{ $cashSalesCount }}</td>
                <td class="text-right">{{ number_format($cashSalesTotal, 2) }}</td>
            </tr>
            @if($cashDebtsCount > 0)
            <tr>
                <td class="text-center">{{ $index++ }}</td>
                <td>Cash</td>
                <td>Marejesho ya Madeni</td>
                <td class="text-center">{{ $cashDebtsCount }}</td>
                <td class="text-right">{{ number_format($cashDebtsTotal, 2) }}</td>
            </tr>
            @endif
            
            <!-- Lipa Namba Types -->
            <tr style="background:#f0f0f0; font-weight:bold;">
                <td colspan="5">📱 LIPA NAMBA</td>
            </tr>
            
            @php
                $mobileTypes = [
                    'mpesa' => ['label' => 'M-Pesa', 'sales_total' => 0, 'sales_count' => 0, 'debts_total' => 0, 'debts_count' => 0],
                    'mixx_by_yas' => ['label' => 'Mixx by Yas', 'sales_total' => 0, 'sales_count' => 0, 'debts_total' => 0, 'debts_count' => 0],
                    'airtel_money' => ['label' => 'Airtel Money', 'sales_total' => 0, 'sales_count' => 0, 'debts_total' => 0, 'debts_count' => 0],
                    'halopesa' => ['label' => 'HaloPesa', 'sales_total' => 0, 'sales_count' => 0, 'debts_total' => 0, 'debts_count' => 0],
                    'other_lipa_namba' => ['label' => 'Nyingine', 'sales_total' => 0, 'sales_count' => 0, 'debts_total' => 0, 'debts_count' => 0],
                ];
                
                foreach($salesByMethod as $item) {
                    if ($item->lipa_kwa === 'lipa_namba') {
                        $type = $item->lipa_kwa_type ?? 'other_lipa_namba';
                        if (!isset($mobileTypes[$type])) $type = 'other_lipa_namba';
                        $mobileTypes[$type]['sales_total'] = $item->total;
                        $mobileTypes[$type]['sales_count'] = $item->count;
                    }
                }
                
                foreach($debtsByMethod as $item) {
                    if ($item->lipa_kwa === 'lipa_namba') {
                        $type = $item->lipa_kwa_type ?? 'other_lipa_namba';
                        if (!isset($mobileTypes[$type])) $type = 'other_lipa_namba';
                        $mobileTypes[$type]['debts_total'] = $item->total;
                        $mobileTypes[$type]['debts_count'] = $item->count;
                    }
                }
            @endphp
            
            @foreach($mobileTypes as $type)
                @php
                    $hasSales = $mobileTypes[$type]['sales_count'] > 0;
                    $hasDebts = $mobileTypes[$type]['debts_count'] > 0;
                @endphp
                @if($hasSales || $hasDebts)
                    @if($hasSales)
                    <tr>
                        <td class="text-center">{{ $index++ }}</td>
                        <td>{{ $type['label'] }}</td>
                        <td>Mauzo</td>
                        <td class="text-center">{{ $mobileTypes[$type]['sales_count'] }}</td>
                        <td class="text-right">{{ number_format($mobileTypes[$type]['sales_total'], 2) }}</td>
                    </tr>
                    @endif
                    @if($hasDebts)
                    <tr>
                        <td class="text-center">{{ $index++ }}</td>
                        <td>{{ $type['label'] }}</td>
                        <td>Marejesho ya Madeni</td>
                        <td class="text-center">{{ $mobileTypes[$type]['debts_count'] }}</td>
                        <d class="text-right">{{ number_format($mobileTypes[$type]['debts_total'], 2) }}</td>
                    </tr>
                    @endif
                @endif
            @endforeach
            
            <!-- Bank Types -->
            <tr style="background:#f0f0f0; font-weight:bold;">
                <td colspan="5">🏦 BANK</td>
            </tr>
            
            @php
                $bankTypes = [
                    'crdb' => ['label' => 'CRDB', 'sales_total' => 0, 'sales_count' => 0, 'debts_total' => 0, 'debts_count' => 0],
                    'nmb' => ['label' => 'NMB', 'sales_total' => 0, 'sales_count' => 0, 'debts_total' => 0, 'debts_count' => 0],
                    'nbc' => ['label' => 'NBC', 'sales_total' => 0, 'sales_count' => 0, 'debts_total' => 0, 'debts_count' => 0],
                    'other_bank' => ['label' => 'Nyingine', 'sales_total' => 0, 'sales_count' => 0, 'debts_total' => 0, 'debts_count' => 0],
                ];
                
                foreach($salesByMethod as $item) {
                    if ($item->lipa_kwa === 'bank') {
                        $type = $item->lipa_kwa_type ?? 'other_bank';
                        if (!isset($bankTypes[$type])) $type = 'other_bank';
                        $bankTypes[$type]['sales_total'] = $item->total;
                        $bankTypes[$type]['sales_count'] = $item->count;
                    }
                }
                
                foreach($debtsByMethod as $item) {
                    if ($item->lipa_kwa === 'bank') {
                        $type = $item->lipa_kwa_type ?? 'other_bank';
                        if (!isset($bankTypes[$type])) $type = 'other_bank';
                        $bankTypes[$type]['debts_total'] = $item->total;
                        $bankTypes[$type]['debts_count'] = $item->count;
                    }
                }
            @endphp
            
            @foreach($bankTypes as $type)
                @php
                    $hasSales = $bankTypes[$type]['sales_count'] > 0;
                    $hasDebts = $bankTypes[$type]['debts_count'] > 0;
                @endphp
                @if($hasSales || $hasDebts)
                    @if($hasSales)
                    <tr>
                        <td class="text-center">{{ $index++ }}</td>
                        <td>{{ $type['label'] }}</td>
                        <td>Mauzo</td>
                        <td class="text-center">{{ $bankTypes[$type]['sales_count'] }}</td>
                        <td class="text-right">{{ number_format($bankTypes[$type]['sales_total'], 2) }}</td>
                    </tr>
                    @endif
                    @if($hasDebts)
                    <tr>
                        <td class="text-center">{{ $index++ }}</td>
                        <td>{{ $type['label'] }}</td>
                        <td>Marejesho ya Madeni</td>
                        <td class="text-center">{{ $bankTypes[$type]['debts_count'] }}</td>
                        <td class="text-right">{{ number_format($bankTypes[$type]['debts_total'], 2) }}</td>
                    </tr>
                    @endif
                @endif
            @endforeach
        </tbody>
    </table>

    <div class="grand-total page-break-avoid">
        JUMLA YA MAPATO YOTE: {{ number_format($grandTotal ?? 0, 2) }} TZS
    </div>
@endif

    <!-- Footer -->
    <div class="footer">
        <div>{{ $companyName ?? 'Biashara' }} &copy; {{ date('Y') }}</div>
        <div>Imechapishwa: {{ now()->format('d/m/Y H:i:s') }}</div>
    </div>
</body>
</html>