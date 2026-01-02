<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mauzo;
use App\Models\Bidhaa;
use App\Models\Manunuzi;
use App\Models\Matumizi;
use App\Models\Madeni;
use Carbon\Carbon;
use PDF;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserReportController extends Controller
{
    // Show report selection page
    public function select()
    {
        return view('user.reports.select');
    }

    // Handle PDF download based on report type & time period
    public function download(Request $request)
    {
        $timePeriod = $request->get('time_period', 'leo');
        $reportType = $request->get('report_type', 'sales');

        switch ($reportType) {
            case 'general':
                $pdfData = $this->prepareGeneralReport($timePeriod);
                $fileName = 'Ripoti_Jumla_'.$timePeriod.'_'.now()->format('Y_m_d').'.pdf';
                break;
            case 'manunuzi':
                $pdfData = $this->prepareManunuziReport($timePeriod);
                $fileName = 'Ripoti_ya_Manunuzi_'.$timePeriod.'_'.now()->format('Y_m_d').'.pdf';
                break;
            default:
                $pdfData = $this->prepareSalesReport($timePeriod);
                $fileName = 'Ripoti_ya_Mauzo_'.$timePeriod.'_'.now()->format('Y_m_d').'.pdf';
                break;
        }

        return PDF::loadView('user.reports.report', $pdfData)
                  ->download($fileName);
    }

    // Prepare Sales report data
    private function prepareSalesReport($timePeriod)
    {
        $user = Auth::user();
        $companyId = $user->company_id;

        $query = Mauzo::with('bidhaa')
                      ->whereHas('bidhaa', fn($q) => $q->where('company_id', $companyId))
                      ->latest();

        $this->applyTimeFilter($query, $timePeriod);

        $sales = $query->get();

        return [
            'reportType' => 'sales',
            'timePeriod' => $timePeriod,
            'date' => now()->format('d/m/Y H:i'),
            'sales' => $sales,
            'report' => null
        ];
    }

    // Prepare General report data
    private function prepareGeneralReport($timePeriod)
    {
        $user = Auth::user();
        $companyId = $user->company_id;
        $today = Carbon::today();

        $jumlaBidhaa = Bidhaa::where('company_id', $companyId)->count();
        $jumlaIdadi = Bidhaa::where('company_id', $companyId)->sum('idadi');
        $thamani = Bidhaa::where('company_id', $companyId)
                         ->selectRaw('SUM(idadi * bei_nunua) as jumla')
                         ->value('jumla');

        $mauzo = Mauzo::whereHas('bidhaa', fn($q) => $q->where('company_id', $companyId));
        $this->applyTimeFilter($mauzo, $timePeriod, $today);
        $jumlaMauzo = $mauzo->sum(DB::raw('idadi * bei'));

        $manunuzi = Manunuzi::where('company_id', $companyId);
        $this->applyTimeFilter($manunuzi, $timePeriod, $today);
        $jumlaManunuzi = $manunuzi->sum(DB::raw('idadi * bei'));

        $matumizi = Matumizi::where('company_id', $companyId);
        $this->applyTimeFilter($matumizi, $timePeriod, $today);
        $jumlaMatumizi = $matumizi->sum('gharama');

        $faidaHalisi = $jumlaMauzo - ($jumlaManunuzi + $jumlaMatumizi);

        $jumlaMadeni = Madeni::where('company_id', $companyId)->sum('baki');
        $idadiMadeni = Madeni::where('company_id', $companyId)->count();

        $report = compact(
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

        return [
            'reportType' => 'general',
            'timePeriod' => $timePeriod,
            'date' => now()->format('d/m/Y H:i'),
            'sales' => null,
            'report' => $report
        ];
    }

    // Prepare Manunuzi report data
    private function prepareManunuziReport($timePeriod)
    {
        $user = Auth::user();
        $companyId = $user->company_id;

        $query = Manunuzi::with('bidhaa')
                          ->where('company_id', $companyId)
                          ->latest();

        $this->applyTimeFilter($query, $timePeriod);

        $manunuzi = $query->get();

        $jumlaManunuzi = $manunuzi->sum(fn($item) => $item->idadi * $item->bei);

        return [
            'reportType' => 'manunuzi',
            'timePeriod' => $timePeriod,
            'date' => now()->format('d/m/Y H:i'),
            'manunuzi' => $manunuzi,
            'jumlaManunuzi' => $jumlaManunuzi,
        ];
    }

    // Apply time period filter to queries
    private function applyTimeFilter($query, $timePeriod, $today = null)
    {
        $today = $today ?? Carbon::today();

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
            case 'yote':
            default:
                break;
        }
    }
}
