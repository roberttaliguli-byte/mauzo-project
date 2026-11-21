<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Bidhaa;
use App\Models\LoginHistory;
use App\Models\Marejesho;
use App\Models\Mauzo;
use App\Models\User;
use App\Models\Wafanyakazi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsApiController extends Controller
{
    /* ─────────────────────────────────────────────────────────
     | GET /api/uchambuzi/graphs
     | Faida/Mauzo/Marejesho series for Chart.js → Flutter.
     ───────────────────────────────────────────────────────── */
    public function graphs(Request $request)
    {
        $companyId = $this->companyId($request);
        if (!$companyId) return $this->err403();

        $from30 = Carbon::now()->subDays(30)->startOfDay();

        // 1. Faida kwa Bidhaa
        $faidaBidhaa = DB::table('mauzos')
            ->join('bidhaas', 'mauzos.bidhaa_id', '=', 'bidhaas.id')
            ->where('mauzos.company_id', $companyId)
            ->groupBy('bidhaas.id', 'bidhaas.jina', 'bidhaas.bei_nunua')
            ->selectRaw('bidhaas.jina as jina, SUM(mauzos.idadi) as items_sold, SUM(mauzos.jumla) as revenue, bidhaas.bei_nunua as bei_nunua')
            ->get()
            ->map(fn ($r) => [
                'label'     => $r->jina,
                'profit'    => (float) $r->revenue - ((float) $r->items_sold * (float) $r->bei_nunua),
                'revenue'   => (float) $r->revenue,
                'cost'      => (float) $r->items_sold * (float) $r->bei_nunua,
                'itemsSold' => (float) $r->items_sold,
            ])
            ->filter(fn ($i) => $i['itemsSold'] > 0)
            ->values();

        // 2. Faida kwa Siku (last 30)
        $faidaSiku = DB::table('mauzos')
            ->join('bidhaas', 'mauzos.bidhaa_id', '=', 'bidhaas.id')
            ->where('mauzos.company_id', $companyId)
            ->where('mauzos.created_at', '>=', $from30)
            ->groupBy(DB::raw('DATE(mauzos.created_at)'))
            ->selectRaw('DATE(mauzos.created_at) as d, SUM(mauzos.jumla) as revenue, SUM(mauzos.idadi * COALESCE(bidhaas.bei_nunua,0)) as cost')
            ->orderBy('d')
            ->get()
            ->map(fn ($r) => [
                'label'   => Carbon::parse($r->d)->format('d/m'),
                'profit'  => (float) $r->revenue - (float) $r->cost,
                'revenue' => (float) $r->revenue,
                'cost'    => (float) $r->cost,
            ]);

        // 3. Mauzo kwa Siku (last 30)
        $mauzoSiku = DB::table('mauzos')
            ->where('company_id', $companyId)
            ->where('created_at', '>=', $from30)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->selectRaw('DATE(created_at) as d, SUM(jumla) as total')
            ->orderBy('d')
            ->get()
            ->map(fn ($r) => [
                'label' => Carbon::parse($r->d)->format('d/m'),
                'total' => (float) $r->total,
            ]);

        // 4. Faida ya Marejesho (FIFO, last 30)
        $marejesho = $this->fifoProfitByProduct(
            $companyId,
            Carbon::now()->subDays(30),
            Carbon::now()
        );

        // 5. Mauzo Jumla kwa Bidhaa
        $mauzo = DB::table('mauzos')
            ->join('bidhaas', 'mauzos.bidhaa_id', '=', 'bidhaas.id')
            ->where('mauzos.company_id', $companyId)
            ->groupBy('bidhaas.id', 'bidhaas.jina')
            ->selectRaw('bidhaas.jina as jina, SUM(mauzos.jumla) as total, SUM(mauzos.idadi) as items_sold')
            ->get()
            ->map(fn ($r) => [
                'label'     => $r->jina,
                'total'     => (float) $r->total,
                'itemsSold' => (float) $r->items_sold,
            ])
            ->filter(fn ($i) => $i['itemsSold'] > 0)
            ->values();

        return response()->json([
            'success' => true,
            'data'    => [
                'faidaByProduct'  => $faidaBidhaa,
                'faidaByDay'      => $faidaSiku,
                'salesByDay'      => $mauzoSiku,
                'returnsProfit'   => $marejesho,
                'salesByProduct'  => $mauzo,
            ],
        ]);
    }

    /* ─────────────────────────────────────────────────────────
     | GET /api/uchambuzi/mwenendo
     | Optional ?from=&to= → returns one summary for the range.
     | Without range → returns siku/wiki/mwezi/mwaka.
     ───────────────────────────────────────────────────────── */
    public function mwenendo(Request $request)
    {
        $companyId = $this->companyId($request);
        if (!$companyId) return $this->err403();

        if ($request->filled('from') && $request->filled('to')) {
            $request->validate([
                'from' => 'required|date',
                'to'   => 'required|date|after_or_equal:from',
            ]);
            $from = Carbon::parse($request->from)->startOfDay();
            $to   = Carbon::parse($request->to)->endOfDay();
            return response()->json([
                'success' => true,
                'data'    => $this->summaryBetween($companyId, $from, $to),
            ]);
        }

        $today = Carbon::today();

        $siku  = $this->summaryBetween($companyId, $today->copy()->startOfDay(), $today->copy()->endOfDay());
        $siku['date'] = $today->format('d/m/Y');

        $wkS = Carbon::now()->startOfWeek();
        $wkE = Carbon::now()->endOfWeek();
        $wiki = $this->summaryBetween($companyId, $wkS, $wkE);
        $wiki['date'] = $wkS->format('d/m') . ' - ' . $wkE->format('d/m');

        $moS = Carbon::now()->startOfMonth();
        $moE = Carbon::now()->endOfMonth();
        $mwezi = $this->summaryBetween($companyId, $moS, $moE);
        $mwezi['date'] = $moS->format('F Y');

        $yrS = Carbon::now()->startOfYear();
        $yrE = Carbon::now()->endOfYear();
        $mwaka = $this->summaryBetween($companyId, $yrS, $yrE);
        $mwaka['date'] = $yrS->format('Y');

        return response()->json([
            'success' => true,
            'data'    => [
                'siku'  => $siku,
                'wiki'  => $wiki,
                'mwezi' => $mwezi,
                'mwaka' => $mwaka,
            ],
        ]);
    }

    /* ─────────────────────────────────────────────────────────
     | GET /api/uchambuzi/kampuni
     ───────────────────────────────────────────────────────── */
    public function kampuni(Request $request)
    {
        $companyId = $this->companyId($request);
        if (!$companyId) return $this->err403();

        $products = Bidhaa::where('company_id', $companyId)
            ->select('id', 'jina', 'aina', 'kipimo', 'idadi', 'bei_nunua', 'bei_kuuza')
            ->get();

        $before = $products->sum(fn ($p) => $p->idadi * $p->bei_nunua);
        $after  = $products->sum(fn ($p) => $p->idadi * $p->bei_kuuza);
        $faida  = $after - $before;

        $list = $products->map(fn ($p) => [
            'id'        => (int) $p->id,
            'name'      => $p->jina,
            'type'      => $p->aina,
            'unit'      => $p->kipimo,
            'quantity'  => (float) $p->idadi,
            'buyPrice'  => (float) $p->bei_nunua,
            'sellPrice' => (float) $p->bei_kuuza,
            'profit'    => (float) (($p->bei_kuuza - $p->bei_nunua) * $p->idadi),
        ]);

        return response()->json([
            'success' => true,
            'data'    => [
                'beforeTotal' => (float) $before,
                'afterTotal'  => (float) $after,
                'profitTotal' => (float) $faida,
                'products'    => $list,
            ],
        ]);
    }

    /* ─────────────────────────────────────────────────────────
     | GET /api/uchambuzi/historia
     ───────────────────────────────────────────────────────── */
    public function historia(Request $request)
    {
        $companyId = $this->companyId($request);
        if (!$companyId) return $this->err403();

        $activities = ActivityLog::where('company_id', $companyId)
            ->orderByDesc('created_at')
            ->limit(50)
            ->get()
            ->map(fn ($a) => [
                'id'          => (int) $a->id,
                'type'        => $a->activity_type,
                'description' => $a->description,
                'userName'    => $a->user_name,
                'userRole'    => $a->user_role,
                'amount'      => $a->amount !== null ? (float) $a->amount : null,
                'createdAt'   => $a->created_at?->toIso8601String(),
            ]);

        $logins = LoginHistory::where('company_id', $companyId)
            ->orderByDesc('login_at')
            ->limit(30)
            ->get()
            ->map(function ($l) {
                $name = 'Unknown';
                $role = 'Unknown';
                if ($l->user_id && $l->user) {
                    $name = $l->user->name ?? $l->user->username;
                    $role = 'Boss';
                } elseif ($l->mfanyakazi_id && $l->mfanyakazi) {
                    $name = $l->mfanyakazi->jina;
                    $role = 'Employee';
                }
                return [
                    'userName'  => $name,
                    'userRole'  => $role,
                    'loginAt'   => $l->login_at?->toIso8601String(),
                    'logoutAt'  => $l->logout_at?->toIso8601String(),
                    'ipAddress' => $l->ip_address,
                ];
            });

        return response()->json([
            'success' => true,
            'data'    => [
                'activities' => $activities,
                'logins'     => $logins,
            ],
        ]);
    }

    /* ─────────────────────────────────────────────────────────
     | GET /api/uchambuzi/wauzaji
     | Returns lists of bosses + employees for the selector.
     ───────────────────────────────────────────────────────── */
    public function wauzaji(Request $request)
    {
        $companyId = $this->companyId($request);
        if (!$companyId) return $this->err403();

        $bosses = User::where('company_id', $companyId)
            ->where('role', 'boss')
            ->select('id', 'name', 'username')
            ->get()
            ->map(fn ($u) => [
                'id'       => (int) $u->id,
                'name'     => $u->name ?? $u->username,
                'username' => $u->username,
                'type'     => 'boss',
            ]);

        $employees = Wafanyakazi::where('company_id', $companyId)
            ->select('id', 'jina', 'username')
            ->get()
            ->map(fn ($e) => [
                'id'       => (int) $e->id,
                'name'     => $e->jina,
                'username' => $e->username,
                'type'     => 'employee',
            ]);

        return response()->json([
            'success' => true,
            'data'    => [
                'bosses'    => $bosses,
                'employees' => $employees,
            ],
        ]);
    }

    /* ─────────────────────────────────────────────────────────
     | GET /api/uchambuzi/user-report
     | Query: user_id, user_type (boss|employee), from, to
     ───────────────────────────────────────────────────────── */
    public function userReport(Request $request)
    {
        $companyId = $this->companyId($request);
        if (!$companyId) return $this->err403();

        $v = validator($request->all(), [
            'user_id'   => 'required|integer',
            'user_type' => 'required|in:boss,employee',
            'from'      => 'required|date',
            'to'        => 'required|date|after_or_equal:from',
        ]);
        if ($v->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $v->errors(),
                'message' => $v->errors()->first(),
            ], 422);
        }

        $from     = Carbon::parse($request->from)->startOfDay();
        $to       = Carbon::parse($request->to)->endOfDay();
        $userId   = (int) $request->user_id;
        $userType = $request->user_type;

        $name = $userType === 'boss'
            ? (User::where('id', $userId)->where('company_id', $companyId)->value('name') ?? '—')
            : (Wafanyakazi::where('id', $userId)->where('company_id', $companyId)->value('jina') ?? '—');

        $salesQ = Mauzo::where('company_id', $companyId)
            ->whereBetween('created_at', [$from, $to])
            ->with('bidhaa');
        if ($userType === 'boss') {
            $salesQ->where('user_id', $userId);
        } else {
            $salesQ->where('mfanyakazi_id', $userId);
        }
        $sales = $salesQ->orderByDesc('created_at')->get();

        $salesData = $sales->map(fn ($s) => [
            'id'        => (int) $s->id,
            'product'   => $s->bidhaa ? [
                'name' => $s->bidhaa->jina,
                'type' => $s->bidhaa->aina,
                'unit' => $s->bidhaa->kipimo,
            ] : null,
            'quantity'  => (float) $s->idadi,
            'price'     => (float) $s->bei,
            'discount'  => (float) $s->punguzo,
            'discountType' => $s->punguzo_aina,
            'total'     => (float) $s->jumla,
            'paidWith'  => $s->lipa_kwa,
            'paidType'  => $s->lipa_kwa_type,
            'createdAt' => $s->created_at?->toIso8601String(),
        ]);

        $actsQ = ActivityLog::where('company_id', $companyId)
            ->whereBetween('created_at', [$from, $to]);
        if ($userType === 'boss') {
            $actsQ->where('user_id', $userId);
        } else {
            $actsQ->where('mfanyakazi_id', $userId);
        }
        $activities = $actsQ->orderByDesc('created_at')->get()->map(fn ($a) => [
            'id'          => (int) $a->id,
            'type'        => $a->activity_type,
            'description' => $a->description,
            'amount'      => $a->amount !== null ? (float) $a->amount : null,
            'createdAt'   => $a->created_at?->toIso8601String(),
        ]);

        $profit = 0;
        foreach ($sales as $s) {
            if ($s->bidhaa) {
                $buy = (float) ($s->bidhaa->bei_nunua ?? 0);
                $disc = $s->punguzo_aina === 'bidhaa'
                    ? (float) $s->punguzo * (float) $s->idadi
                    : (float) $s->punguzo;
                $profit += ((float) $s->bei - $buy) * (float) $s->idadi - $disc;
            }
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'userName'          => $name,
                'userType'          => $userType,
                'sales'             => $salesData,
                'activities'        => $activities,
                'totalSalesAmount'  => (float) $sales->sum('jumla'),
                'totalSalesCount'   => (int) $sales->count(),
                'totalItemsSold'    => (float) $sales->sum('idadi'),
                'totalProfit'       => (float) $profit,
                'averageSaleValue'  => $sales->count() > 0
                    ? (float) ($sales->sum('jumla') / $sales->count())
                    : 0,
                'fromDate'          => $from->format('d/m/Y'),
                'toDate'            => $to->format('d/m/Y'),
            ],
        ]);
    }

    /* ─────────────────────────────────────────────────────────
     | Helpers
     ───────────────────────────────────────────────────────── */
    private function companyId(Request $request): ?int
    {
        return $request->user()?->company_id;
    }

    private function err403()
    {
        return response()->json([
            'success' => false,
            'message' => 'Akaunti yako haijaunganishwa na kampuni.',
        ], 403);
    }

    private function summaryBetween(int $companyId, Carbon $from, Carbon $to): array
    {
        $mapatoMauzo = (float) DB::table('mauzos')
            ->where('company_id', $companyId)
            ->whereBetween('created_at', [$from, $to])
            ->sum('jumla');

        $mapatoMadeni = (float) DB::table('marejeshos')
            ->where('company_id', $companyId)
            ->whereBetween('created_at', [$from, $to])
            ->sum('kiasi');

        $matumizi = (float) DB::table('matumizis')
            ->where('company_id', $companyId)
            ->whereBetween('created_at', [$from, $to])
            ->sum('gharama');

        $cogs = (float) DB::table('mauzos')
            ->join('bidhaas', 'mauzos.bidhaa_id', '=', 'bidhaas.id')
            ->where('mauzos.company_id', $companyId)
            ->whereBetween('mauzos.created_at', [$from, $to])
            ->sum(DB::raw('mauzos.idadi * COALESCE(bidhaas.bei_nunua,0)'));

        $faidaMarejesho = $this->fifoProfitTotal($companyId, $from, $to);

        $faidaMauzo = $mapatoMauzo - $cogs;
        $faidaHalisi = ($faidaMauzo + $faidaMarejesho) - $matumizi;
        $fedhaDroo = $mapatoMauzo + $mapatoMadeni - $matumizi;

        return [
            'mapatoMauzo'     => $mapatoMauzo,
            'mapatoMadeni'    => $mapatoMadeni,
            'jumlaMapato'     => $mapatoMauzo + $mapatoMadeni,
            'jumlaMatumizi'   => $matumizi,
            'gharamaBidhaa'   => $cogs,
            'faidaMarejesho'  => $faidaMarejesho,
            'faidaMauzo'      => $faidaMauzo,
            'fedhaDroo'       => $fedhaDroo,
            'faidaHalisi'     => $faidaHalisi,
        ];
    }

    private function fifoProfitByProduct(int $companyId, Carbon $from, Carbon $to): array
    {
        $repayments = DB::table('marejeshos')
            ->join('madenis', 'marejeshos.madeni_id', '=', 'madenis.id')
            ->join('bidhaas', 'madenis.bidhaa_id', '=', 'bidhaas.id')
            ->where('marejeshos.company_id', $companyId)
            ->whereBetween('marejeshos.created_at', [$from, $to])
            ->orderBy('marejeshos.tarehe')
            ->selectRaw('madenis.id as debt_id, bidhaas.jina as name,
                        bidhaas.bei_nunua as buy_price,
                        madenis.idadi as qty,
                        marejeshos.kiasi as amount')
            ->get();

        $progress = [];
        $byProduct = [];

        foreach ($repayments as $r) {
            $id = $r->debt_id;
            $name = $r->name;
            if (!isset($byProduct[$name])) {
                $byProduct[$name] = ['label' => $name, 'total' => 0.0, 'repaymentCount' => 0];
            }
            if (!isset($progress[$id])) {
                $progress[$id] = [
                    'total_cost'   => (float) $r->buy_price * (float) $r->qty,
                    'recovered'    => 0.0,
                    'cost_covered' => false,
                ];
            }

            $remaining = (float) $r->amount;
            $profit = 0.0;
            if (!$progress[$id]['cost_covered']) {
                $toRecover = $progress[$id]['total_cost'] - $progress[$id]['recovered'];
                if ($remaining <= $toRecover) {
                    $progress[$id]['recovered'] += $remaining;
                } else {
                    $progress[$id]['recovered'] += $toRecover;
                    $progress[$id]['cost_covered'] = true;
                    $profit = $remaining - $toRecover;
                }
            } else {
                $profit = $remaining;
            }

            if ($profit > 0) $byProduct[$name]['total'] += $profit;
            $byProduct[$name]['repaymentCount']++;
        }

        return array_values($byProduct);
    }

    private function fifoProfitTotal(int $companyId, Carbon $from, Carbon $to): float
    {
        $repayments = DB::table('marejeshos')
            ->join('madenis', 'marejeshos.madeni_id', '=', 'madenis.id')
            ->join('bidhaas', 'madenis.bidhaa_id', '=', 'bidhaas.id')
            ->where('marejeshos.company_id', $companyId)
            ->whereBetween('marejeshos.created_at', [$from, $to])
            ->orderBy('marejeshos.tarehe')
            ->selectRaw('madenis.id as debt_id, bidhaas.bei_nunua as buy_price,
                        madenis.idadi as qty, marejeshos.kiasi as amount')
            ->get();

        $progress = [];
        $total = 0.0;

        foreach ($repayments as $r) {
            $id = $r->debt_id;
            if (!isset($progress[$id])) {
                $progress[$id] = [
                    'total_cost'   => (float) $r->buy_price * (float) $r->qty,
                    'recovered'    => 0.0,
                    'cost_covered' => false,
                ];
            }
            $remaining = (float) $r->amount;
            if (!$progress[$id]['cost_covered']) {
                $toRecover = $progress[$id]['total_cost'] - $progress[$id]['recovered'];
                if ($remaining <= $toRecover) {
                    $progress[$id]['recovered'] += $remaining;
                } else {
                    $progress[$id]['recovered'] += $toRecover;
                    $progress[$id]['cost_covered'] = true;
                    $total += ($remaining - $toRecover);
                }
            } else {
                $total += $remaining;
            }
        }

        return $total;
    }
}