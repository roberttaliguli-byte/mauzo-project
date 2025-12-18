<?php

namespace App\Services;

use App\Models\Bidhaa;
use App\Models\Mauzo;
use App\Models\Manunuzi;
use App\Models\Matumizi;
use App\Models\Madeni;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GeneralReportService
{
    public static function generate($companyId, $timePeriod = 'leo')
    {
        $today = Carbon::today();

        // ---- Time filter closure ----
        $applyTimeFilter = function ($query) use ($timePeriod, $today) {
            switch ($timePeriod) {
                case 'leo':
                    $query->whereDate('created_at', $today);
                    break;
                case 'jana':
                    $query->whereDate('created_at', $today->copy()->subDay());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek()
                    ]);
                    break;
                case 'mwezi':
                    $query->whereMonth('created_at', Carbon::now()->month)
                          ->whereYear('created_at', Carbon::now()->year);
                    break;
            }
        };

        // --- Inventory ---
        $jumlaBidhaa = Bidhaa::where('company_id', $companyId)->count();
        $jumlaIdadi = Bidhaa::where('company_id', $companyId)->sum('idadi');

        $thamani = Bidhaa::where('company_id', $companyId)
            ->selectRaw('SUM(idadi * bei_nunua) as jumla')
            ->value('jumla');

        // --- Sales ---
        $mauzo = Mauzo::whereHas('bidhaa', fn($q) =>
            $q->where('company_id', $companyId)
        );
        $applyTimeFilter($mauzo);

        $jumlaMauzo = $mauzo->sum(DB::raw('idadi * bei'));

        // --- Purchases ---
        $manunuzi = Manunuzi::where('company_id', $companyId);
        $applyTimeFilter($manunuzi);

        $jumlaManunuzi = $manunuzi->sum(DB::raw('idadi * bei'));

        // --- Expenses ---
        $matumizi = Matumizi::where('company_id', $companyId);
        $applyTimeFilter($matumizi);

        $jumlaMatumizi = $matumizi->sum('gharama');

        // --- Profit ---
        $faidaHalisi = $jumlaMauzo - ($jumlaManunuzi + $jumlaMatumizi);

        // --- Debts ---
        $jumlaMadeni = Madeni::where('company_id', $companyId)->sum('baki');
        $idadiMadeni = Madeni::where('company_id', $companyId)->count();

        return compact(
            'jumlaBidhaa',
            'jumlaIdadi',
            'thamani',
            'jumlaMauzo',
            'jumlaManunuzi',
            'jumlaMatumizi',
            'faidaHalisi',
            'jumlaMadeni',
            'idadiMadeni'
        );
    }
}
