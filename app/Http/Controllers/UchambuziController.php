<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Mauzo;
use App\Models\Bidhaa;
use App\Models\Matumizi;
use App\Models\Marejesho;
use App\Models\Madeni;
use Carbon\Carbon;
use App\Models\Manunuzi;
use App\Models\LoginHistory;
use App\Models\ActivityLog;
use App\Models\Wafanyakazi;
use App\Models\User;

class UchambuziController extends Controller
{
    /**
     * Get authenticated user from either guard (boss or employee)
     */
    private function getAuthenticatedUser()
    {
        if (Auth::guard('mfanyakazi')->check()) {
            return Auth::guard('mfanyakazi')->user();
        }
        if (Auth::guard('web')->check()) {
            return Auth::guard('web')->user();
        }
        abort(403, 'Unauthorized - Please login first');
    }

    /**
     * Main dashboard page
     */
    public function index()
    {
        $user = $this->getAuthenticatedUser();
        $company = $user->company;

        if (!$company) {
            return redirect()->back()->with('error', 'User has no company assigned!');
        }

        $today = Carbon::today();

        // Get all bosses for the company
$bossUsers = User::where('company_id', $company->id)
    ->where('role', 'boss')
    ->select('id', 'name', 'username')
    ->get();

// Get all employees for the company
$employeeUsers = Wafanyakazi::where('company_id', $company->id)
    ->select('id', 'jina', 'role')
    ->get();

        // ---------- 1. Faida kwa Bidhaa (profit per product) ----------
        $faidaBidhaa = $company->bidhaa()
            ->with('mauzos')
            ->get()
            ->map(function ($bidhaa) {
                $totalSold = $bidhaa->mauzos->sum('idadi');
                $totalRevenue = $bidhaa->mauzos->sum('jumla');
                $totalCost = $totalSold * $bidhaa->bei_nunua;
                return [
                    'jina' => $bidhaa->jina,
                    'faida' => $totalRevenue - $totalCost,
                    'items_sold' => $totalSold,
                    'revenue' => $totalRevenue,
                    'cost' => $totalCost,
                ];
            })
            ->filter(fn($item) => $item['items_sold'] > 0)
            ->values()
            ->toArray();

        // ---------- 2. Faida kwa Siku (last 30 days) ----------
        $faidaSiku = $company->mauzo()
            ->with('bidhaa')
            ->whereDate('created_at', '>=', Carbon::now()->subDays(30))
            ->get()
            ->groupBy(fn($m) => $m->created_at->format('Y-m-d'))
            ->map(function ($mauzos, $date) {
                $totalRevenue = $mauzos->sum('jumla');
                $totalCost = $mauzos->sum(fn($m) => $m->idadi * $m->bidhaa->bei_nunua);
                return [
                    'day' => Carbon::parse($date)->format('d/m'),
                    'faida' => $totalRevenue - $totalCost,
                    'revenue' => $totalRevenue,
                    'cost' => $totalCost,
                ];
            })
            ->sortBy('day')
            ->values()
            ->toArray();

        // ---------- 3. Mauzo kwa Siku (last 30 days) ----------
        $mauzoSiku = $company->mauzo()
            ->whereDate('created_at', '>=', Carbon::now()->subDays(30))
            ->get()
            ->groupBy(fn($m) => $m->created_at->format('Y-m-d'))
            ->map(fn($mauzos, $date) => [
                'day' => Carbon::parse($date)->format('d/m'),
                'total' => $mauzos->sum('jumla')
            ])
            ->sortBy('day')
            ->values()
            ->toArray();

        // ---------- 4. Faida ya Marejesho (using FIFO method) ----------
        $marejesho = $this->calculateFifoProfit($company, Carbon::now()->subDays(30), Carbon::now());

        // ---------- 5. Mauzo Jumla kwa Bidhaa ----------
        $mauzo = $company->bidhaa()
            ->with('mauzos')
            ->get()
            ->map(fn($b) => [
                'jina' => $b->jina,
                'total' => $b->mauzos->sum('jumla'),
                'items_sold' => $b->mauzos->sum('idadi'),
            ])
            ->filter(fn($item) => $item['items_sold'] > 0)
            ->values()
            ->toArray();

        // ---------- 6. Mwenendo wa Biashara (summary for today/week/month/year) ----------
        $computeBetween = function (Carbon $from, Carbon $to) use ($company) {
            $fromDt = $from->copy()->startOfDay();
            $toDt = $to->copy()->endOfDay();

            $mapatoMauzo = $company->mauzo()->whereBetween('created_at', [$fromDt, $toDt])->sum('jumla');
            $mapatoMadeni = $company->marejeshos()->whereBetween('created_at', [$fromDt, $toDt])->sum('kiasi');
            $jumlaMapato = $mapatoMauzo + $mapatoMadeni;
            $jumlaMatumizi = $company->matumizi()->whereBetween('created_at', [$fromDt, $toDt])->sum('gharama');

            $costOfGoodsSold = $company->mauzo()
                ->with('bidhaa')
                ->whereBetween('created_at', [$fromDt, $toDt])
                ->get()
                ->sum(fn($m) => $m->idadi * $m->bidhaa->bei_nunua);

            $faidaMarejesho = $this->calculateFifoProfitTotal($company, $fromDt, $toDt);
            $faidaMauzo = $mapatoMauzo - $costOfGoodsSold;
            $faidaHalisi = ($faidaMauzo + $faidaMarejesho) - $jumlaMatumizi;
            $fedhaDroo = $jumlaMapato - $jumlaMatumizi;

            return [
                'mapato_mauzo' => (float) $mapatoMauzo,
                'mapato_madeni' => (float) $mapatoMadeni,
                'jumla_mapato' => (float) $jumlaMapato,
                'jumla_mat' => (float) $jumlaMatumizi,
                'gharama_bidhaa' => (float) $costOfGoodsSold,
                'faida_marejesho' => (float) $faidaMarejesho,
                'faida_mauzo' => (float) $faidaMauzo,
                'fedha_droo' => (float) $fedhaDroo,
                'faida_halisi' => (float) $faidaHalisi,
                'date' => $from->format('d/m/Y'),
            ];
        };

        $sikuData = $computeBetween($today, $today);
        $wikiStart = Carbon::now()->startOfWeek();
        $wikiEnd = Carbon::now()->endOfWeek();
        $wikiData = $computeBetween($wikiStart, $wikiEnd);
        $wikiData['date'] = $wikiStart->format('d/m') . ' - ' . $wikiEnd->format('d/m');
        $mweziStart = Carbon::now()->startOfMonth();
        $mweziEnd = Carbon::now()->endOfMonth();
        $mweziData = $computeBetween($mweziStart, $mweziEnd);
        $mweziData['date'] = $mweziStart->format('F Y');
        $mwakaStart = Carbon::now()->startOfYear();
        $mwakaEnd = Carbon::now()->endOfYear();
        $mwakaData = $computeBetween($mwakaStart, $mwakaEnd);
        $mwakaData['date'] = $mwakaStart->format('Y');

        $mwenendoSummary = [
            'siku' => $sikuData,
            'wiki' => $wikiData,
            'mwezi' => $mweziData,
            'mwaka' => $mwakaData,
        ];

        // ---------- 7. Thamani ya Kampuni ----------
        $thamaniBefore = $company->bidhaa->sum(fn($b) => $b->idadi * $b->bei_nunua);
        $thamaniAfter = $company->bidhaa->sum(fn($b) => $b->idadi * $b->bei_kuuza);
        $faida = $thamaniAfter - $thamaniBefore;
        $thamaniKampuniFormatted = 'Tsh ' . number_format($thamaniAfter, 0);

        // ---------- 8. Bidhaa List for table ----------
        $bidhaaList = $company->bidhaa()
            ->select('jina', 'aina', 'kipimo', 'idadi', 'bei_nunua', 'bei_kuuza')
            ->with('mauzos')
            ->get()
            ->map(function ($bidhaa) {
                $bidhaa->thamani_kabla = $bidhaa->idadi * $bidhaa->bei_nunua;
                $bidhaa->thamani_baada = $bidhaa->idadi * $bidhaa->bei_kuuza;
                $bidhaa->faida = $bidhaa->thamani_baada - $bidhaa->thamani_kabla;
                $bidhaa->total_sold = $bidhaa->mauzos->sum('idadi');
                return $bidhaa;
            });

        // ---------- 9. Activities for history ----------
        $recentActivities = ActivityLog::where('company_id', $company->id)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

   // ---------- 10. Login history ----------
$loginHistories = LoginHistory::where('company_id', $company->id)
    ->orderBy('login_at', 'desc')
    ->limit(10)
    ->get()
    ->map(function ($login) {
        $userName = 'Unknown';
        $userRole = 'Unknown';
        
        if ($login->user_id && $login->user) {
            $userName = $login->user->name ?? $login->user->username;
            $userRole = 'Boss';
        } elseif ($login->mfanyakazi_id && $login->mfanyakazi) {
            $userName = $login->mfanyakazi->jina;
            $userRole = 'Employee';
        }
        
        return (object)[
            'user_name' => $userName,
            'user_role' => $userRole,
            'login_at' => $login->login_at,
            'logout_at' => $login->logout_at,
            'ip_address' => $login->ip_address
        ];
    });
    

        return view('uchambuzi.index', compact(
            'faidaBidhaa', 'faidaSiku', 'mauzoSiku', 'marejesho', 'mauzo',
            'mwenendoSummary', 'thamaniKampuniFormatted', 'bidhaaList',
            'thamaniBefore', 'thamaniAfter', 'faida',
            'recentActivities', 'loginHistories','bossUsers', 'employeeUsers'
        ));
    }

    /**
     * Calculate FIFO profit for debt repayments (grouped by product)
     */
    private function calculateFifoProfit($company, $fromDate, $toDate)
    {
        $repayments = $company->marejeshos()
            ->with(['madeni' => fn($q) => $q->with('bidhaa')])
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->orderBy('tarehe', 'asc')
            ->get()
            ->filter(fn($r) => $r->madeni && $r->madeni->bidhaa);

        $debtProgress = [];
        $productProfits = [];

        foreach ($repayments as $marejesho) {
            $debt = $marejesho->madeni;
            $debtId = $debt->id;
            $productName = $debt->bidhaa->jina;
            $amount = $marejesho->kiasi;

            if (!isset($productProfits[$productName])) {
                $productProfits[$productName] = ['jina' => $productName, 'total' => 0, 'repayment_count' => 0];
            }

            if (!isset($debtProgress[$debtId])) {
                $buyingPrice = $debt->bidhaa->bei_nunua ?? 0;
                $quantity = $debt->idadi;
                $totalCost = $buyingPrice * $quantity;
                $debtProgress[$debtId] = [
                    'total_cost' => $totalCost,
                    'recovered_so_far' => 0,
                    'is_cost_recovered' => false
                ];
            }

            $progress = &$debtProgress[$debtId];
            $remaining = $amount;
            $profit = 0;

            if (!$progress['is_cost_recovered']) {
                $toRecover = $progress['total_cost'] - $progress['recovered_so_far'];
                if ($remaining <= $toRecover) {
                    $progress['recovered_so_far'] += $remaining;
                } else {
                    $costPortion = $toRecover;
                    $progress['recovered_so_far'] += $costPortion;
                    $progress['is_cost_recovered'] = true;
                    $profit = $remaining - $costPortion;
                }
            }
            if ($progress['is_cost_recovered'] && $remaining > 0) {
                $profit = $remaining;
            }

            if ($profit > 0) $productProfits[$productName]['total'] += $profit;
            $productProfits[$productName]['repayment_count']++;
        }

        return array_values($productProfits);
    }

    /**
     * Calculate total FIFO profit for a date range
     */
    private function calculateFifoProfitTotal($company, $fromDt, $toDt)
    {
        $repayments = $company->marejeshos()
            ->with(['madeni' => fn($q) => $q->with('bidhaa')])
            ->whereBetween('created_at', [$fromDt, $toDt])
            ->orderBy('tarehe', 'asc')
            ->get()
            ->filter(fn($r) => $r->madeni && $r->madeni->bidhaa);

        $debtProgress = [];
        $totalProfit = 0;

        foreach ($repayments as $marejesho) {
            $debt = $marejesho->madeni;
            $debtId = $debt->id;
            $amount = $marejesho->kiasi;

            if (!isset($debtProgress[$debtId])) {
                $buyingPrice = $debt->bidhaa->bei_nunua ?? 0;
                $quantity = $debt->idadi;
                $totalCost = $buyingPrice * $quantity;
                $debtProgress[$debtId] = [
                    'total_cost' => $totalCost,
                    'recovered_so_far' => 0,
                    'is_cost_recovered' => false
                ];
            }

            $progress = &$debtProgress[$debtId];
            $remaining = $amount;

            if (!$progress['is_cost_recovered']) {
                $toRecover = $progress['total_cost'] - $progress['recovered_so_far'];
                if ($remaining <= $toRecover) {
                    $progress['recovered_so_far'] += $remaining;
                } else {
                    $costPortion = $toRecover;
                    $progress['recovered_so_far'] += $costPortion;
                    $progress['is_cost_recovered'] = true;
                    $totalProfit += ($remaining - $costPortion);
                }
            } else {
                $totalProfit += $remaining;
            }
        }
        return $totalProfit;
    }

    /**
     * AJAX endpoint for custom date range summary (Mwenendo)
     */
    public function mwenendoRange(Request $request)
    {
        $request->validate([
            'from' => 'required|date',
            'to'   => 'required|date|after_or_equal:from',
        ]);

        $user = $this->getAuthenticatedUser();
        $company = $user->company;

        $from = Carbon::parse($request->query('from'))->startOfDay();
        $to   = Carbon::parse($request->query('to'))->endOfDay();

        $mapatoMauzo = $company->mauzo()->whereBetween('created_at', [$from, $to])->sum('jumla');
        $mapatoMadeni = $company->marejeshos()->whereBetween('created_at', [$from, $to])->sum('kiasi');
        $jumlaMapato = $mapatoMauzo + $mapatoMadeni;
        $jumlaMatumizi = $company->matumizi()->whereBetween('created_at', [$from, $to])->sum('gharama');

        $costOfGoodsSold = $company->mauzo()
            ->with('bidhaa')
            ->whereBetween('created_at', [$from, $to])
            ->get()
            ->sum(fn($m) => $m->idadi * $m->bidhaa->bei_nunua);

        $faidaMarejesho = $this->calculateFifoProfitTotal($company, $from, $to);
        $faidaMauzo = $mapatoMauzo - $costOfGoodsSold;
        $faidaHalisi = ($faidaMauzo + $faidaMarejesho) - $jumlaMatumizi;
        $fedhaDroo = $jumlaMapato - $jumlaMatumizi;

        return response()->json([
            'mapato_mauzo' => (float) $mapatoMauzo,
            'mapato_madeni' => (float) $mapatoMadeni,
            'jumla_mapato' => (float) $jumlaMapato,
            'jumla_mat' => (float) $jumlaMatumizi,
            'gharama_bidhaa' => (float) $costOfGoodsSold,
            'faida_marejesho' => (float) $faidaMarejesho,
            'faida_mauzo' => (float) $faidaMauzo,
            'fedha_droo' => (float) $fedhaDroo,
            'faida_halisi' => (float) $faidaHalisi,
        ]);
    }

    /**
     * Get all activities as JSON for AJAX (for modal "Tazama Zote")
     */
    public function getAllActivities()
    {
        $user = $this->getAuthenticatedUser();
        $company = $user->company;
        
        if (!$company) {
            return response()->json([]);
        }
        
        $activities = ActivityLog::where('company_id', $company->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($activity) {
                return [
                    'id' => $activity->id,
                    'activity_type' => $activity->activity_type,
                    'description' => $activity->description,
                    'user_name' => $activity->user_name,
                    'user_role' => $activity->user_role,
                    'amount' => $activity->amount,
                    'created_at' => $activity->created_at ? $activity->created_at->format('d/m/Y H:i:s') : null
                ];
            });
        
        return response()->json($activities);
    }


  /**
 * Get user report (sales and activities for a specific user)
 */
public function getUserReport(Request $request)
{
    $user = $this->getAuthenticatedUser();
    $company = $user->company;
    
    if (!$company) {
        return response()->json([
            'success' => false,
            'message' => 'Company not found'
        ], 404);
    }
    
    $request->validate([
        'user_id' => 'required|integer',
        'user_type' => 'required|in:boss,employee',
        'from' => 'required|date',
        'to' => 'required|date'
    ]);
    
    $from = Carbon::parse($request->from)->startOfDay();
    $to = Carbon::parse($request->to)->endOfDay();
    
    $userId = $request->user_id;
    $userType = $request->user_type;
    $userName = '';
    
    // Get user name
    if ($userType === 'boss') {
        $bossUser = User::where('id', $userId)->where('company_id', $company->id)->first();
        if (!$bossUser) {
            return response()->json([
                'success' => false,
                'message' => 'Mtumiaji huyo hapatikani'
            ], 404);
        }
        $userName = $bossUser->name ?? $bossUser->username;
    } else {
        $employee = Wafanyakazi::where('id', $userId)->where('company_id', $company->id)->first();
        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Mfanyakazi huyo hapatikani'
            ], 404);
        }
        $userName = $employee->jina;
    }
    
    // Query sales based on user type
    $salesQuery = Mauzo::where('company_id', $company->id)
        ->whereBetween('created_at', [$from, $to])
        ->with('bidhaa');
    
    if ($userType === 'boss') {
        $salesQuery->where('user_id', $userId);
    } else {
        $salesQuery->where('mfanyakazi_id', $userId);
    }
    
    $sales = $salesQuery->orderBy('created_at', 'desc')->get();
    
    // Debug log to see what's happening
    \Log::info('User Report Query:', [
        'user_type' => $userType,
        'user_id' => $userId,
        'from' => $from,
        'to' => $to,
        'sales_count' => $sales->count()
    ]);
    
    // Calculate totals
    $totalSalesAmount = $sales->sum('jumla');
    $totalSalesCount = $sales->count();
    $totalItemsSold = $sales->sum('idadi');
    
    // Calculate profit
    $totalProfit = 0;
    foreach ($sales as $sale) {
        if ($sale->bidhaa) {
            $buyingPrice = $sale->bidhaa->bei_nunua ?? 0;
            $discountAmount = $sale->punguzo_aina === 'bidhaa' 
                ? $sale->punguzo * $sale->idadi 
                : $sale->punguzo;
            $profit = ($sale->bei - $buyingPrice) * $sale->idadi - $discountAmount;
            $totalProfit += $profit;
        }
    }
    
    $averageSaleValue = $totalSalesCount > 0 ? $totalSalesAmount / $totalSalesCount : 0;
    
    // Add formatted data to each sale for display
    $salesData = [];
    foreach ($sales as $sale) {
        $discountAmount = $sale->punguzo_aina === 'bidhaa' 
            ? $sale->punguzo * $sale->idadi 
            : $sale->punguzo;
        
        $salesData[] = [
            'id' => $sale->id,
            'created_at' => $sale->created_at,
            'bidhaa' => $sale->bidhaa ? [
                'jina' => $sale->bidhaa->jina,
                'aina' => $sale->bidhaa->aina,
                'kipimo' => $sale->bidhaa->kipimo
            ] : null,
            'idadi' => $sale->idadi,
            'bei' => $sale->bei,
            'punguzo' => $sale->punguzo,
            'punguzo_aina' => $sale->punguzo_aina,
            'discount_amount' => $discountAmount,
            'jumla' => $sale->jumla,
            'lipa_kwa' => $sale->lipa_kwa,
            'lipa_kwa_type' => $sale->lipa_kwa_type
        ];
    }
    
    // Get activities
    $activitiesQuery = ActivityLog::where('company_id', $company->id)
        ->whereBetween('created_at', [$from, $to]);
    
    if ($userType === 'boss') {
        $activitiesQuery->where('user_id', $userId);
    } else {
        $activitiesQuery->where('mfanyakazi_id', $userId);
    }
    
    $activities = $activitiesQuery->orderBy('created_at', 'desc')->get();
    
    return response()->json([
        'success' => true,
        'data' => [
            'user_name' => $userName,
            'user_type' => $userType,
            'sales' => $salesData,
            'activities' => $activities,
            'total_sales_amount' => (float) $totalSalesAmount,
            'total_sales_count' => (int) $totalSalesCount,
            'total_items_sold' => (float) $totalItemsSold,
            'total_profit' => (float) $totalProfit,
            'average_sale_value' => (float) $averageSaleValue,
            'from_date' => $from->format('d/m/Y'),
            'to_date' => $to->format('d/m/Y')
        ]
    ]);
}
}