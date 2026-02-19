Here is the black and white HTML report code. All summary data is now presented in clean, monochrome tables for a professional, print-ready appearance.
```html
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
    <!-- Header (unchanged) -->
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

    @if($reportType === 'sales')
        <!-- SALES REPORT - all summary data in a black & white table -->
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

        <!-- Payment methods summary table (black & white) -->
        <table class="summary-table page-break-avoid" style="margin-top:8px;">
            <tr>
                <th>Cash</th>
                <th>Lipa Namba</th>
                <th>Benki</th>
            </tr>
            <tr>
                <td>{{ number_format($totalCashIncome ?? 0, 2) }} TZS</td>
                <td>{{ number_format($totalMobileIncome ?? 0, 2) }} TZS</td>
                <td>{{ number_format($totalBankIncome ?? 0, 2) }} TZS</td>
            </tr>
        </table>

        <!-- Sales details table (already B&W compatible) -->
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tarehe</th>
                    <th>Bidhaa</th>
                    <th class="text-center">Idadi</th>
                    <th class="text-center">Njia ya Malipo</th>
                    <th class="text-right">Jumla (TZS)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sales ?? [] as $index => $sale)
                    @if(!$sale->bidhaa) @continue @endif
                    @php
                        $paymentMethod = $sale->lipa_kwa ?? 'cash';
                        $methodName = $paymentMethod === 'cash' ? 'Cash' : 
                                     ($paymentMethod === 'lipa_namba' ? 'Lipa Namba' : 'Benki');
                        $idadi = $sale->idadi;
                        $formattedIdadi = is_numeric($idadi) ? 
                            ($idadi % 1 == 0 ? (string)(int)$idadi : number_format($idadi, 2)) : 
                            $idadi;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $sale->created_at ? date('d/m/Y', strtotime($sale->created_at)) : '' }}</td>
                        <td>{{ $sale->bidhaa->jina ?? 'N/A' }}</td>
                        <td class="text-center">{{ $formattedIdadi }}</td>
                        <td class="text-center">{{ $methodName }}</td>
                        <td class="text-right">{{ number_format($sale->jumla, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center no-data">Hakuna mauzo katika kipindi hiki</td>
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

        <!-- Purchases table -->
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tarehe</th>
                    <th>Bidhaa</th>
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
                        <td class="text-center">{{ $formattedIdadi }}</td>
                        <td class="text-right">{{ number_format($purchase->bei, 2) }}</td>
                        <td class="text-right">{{ number_format($unitCost, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center no-data">Hakuna manunuzi katika kipindi hiki</td>
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

        <!-- Detailed income/expense comparison table (already B&W) -->
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

        <!-- Income details table -->
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Aina</th>
                    <th>Njia ya Malipo</th>
                    <th class="text-center">Idadi</th>
                    <th class="text-right">Jumla (TZS)</th>
                </tr>
            </thead>
            <tbody>
                @php $index = 1; @endphp

                @forelse(($salesByMethod ?? []) as $item)
                    @php
                        $methodName = $item->lipa_kwa === 'cash' ? 'Cash' : 
                                     ($item->lipa_kwa === 'lipa_namba' ? 'Lipa Namba' : 'Benki');
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index++ }}</td>
                        <td>Mauzo</td>
                        <td>{{ $methodName }}</td>
                        <td class="text-center">{{ $item->count }}</td>
                        <td class="text-right">{{ number_format($item->total, 2) }}</td>
                    </tr>
                @empty
                @endforelse

                @forelse(($debtsByMethod ?? []) as $item)
                    @php
                        $methodName = $item->lipa_kwa === 'cash' ? 'Cash' : 
                                     ($item->lipa_kwa === 'lipa_namba' ? 'Lipa Namba' : 'Benki');
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index++ }}</td>
                        <td>Marejesho ya Madeni</td>
                        <td>{{ $methodName }}</td>
                        <td class="text-center">{{ $item->count }}</td>
                        <td class="text-right">{{ number_format($item->total, 2) }}</td>
                    </tr>
                @empty
                @endforelse

                @if($index === 1)
                    <tr>
                        <td colspan="5" class="text-center no-data">Hakuna data katika kipindi hiki</td>
                    </tr>
                @endif
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
