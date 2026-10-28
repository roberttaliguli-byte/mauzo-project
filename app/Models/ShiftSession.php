<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShiftSession extends Model
{
    use HasFactory;

    protected $table = 'shift_sessions';

    protected $fillable = [
        'company_id',
        'user_id',
        'mfanyakazi_id',
        'status',
        'started_at',
        'closed_at',
        'opening_balance',
        'closing_cash',
        'expected_cash',
        'cash_difference',
        'total_sales',
        'total_items',
        'total_cost',
        'total_profit',
        'total_expenses',
        'cash_sales',
        'total_returns',
        'notes',
        'report_snapshot',
        'report_sent_at',
        'report_sent_to',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'closed_at' => 'datetime',
        'report_sent_at' => 'datetime',
        'opening_balance' => 'float',
        'closing_cash' => 'float',
        'expected_cash' => 'float',
        'cash_difference' => 'float',
        'total_sales' => 'float',
        'total_items' => 'float',
        'total_cost' => 'float',
        'total_profit' => 'float',
        'total_expenses' => 'float',
        'cash_sales' => 'float',
        'total_returns' => 'float',
        'report_snapshot' => 'array',
    ];

    /** Cached open session for the current request. */
    protected static ?self $activeCache = null;
    protected static bool $activeCacheResolved = false;

    /* ------------------------------------------------------------------ */
    /* Relationships                                                       */
    /* ------------------------------------------------------------------ */

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function mfanyakazi()
    {
        return $this->belongsTo(Wafanyakazi::class, 'mfanyakazi_id');
    }

    public function mauzos()
    {
        return $this->hasMany(Mauzo::class, 'shift_session_id');
    }

    public function matumizis()
    {
        return $this->hasMany(Matumizi::class, 'shift_session_id');
    }

    /* ------------------------------------------------------------------ */
    /* Accessors                                                           */
    /* ------------------------------------------------------------------ */

    public function getOwnerNameAttribute(): string
    {
        if ($this->mfanyakazi_id) {
            return $this->mfanyakazi->jina ?? 'Mfanyakazi';
        }

        if ($this->user_id) {
            return $this->user->name ?? $this->user->username ?? 'Boss';
        }

        return 'Mtumiaji';
    }

    public function getOwnerRoleAttribute(): string
    {
        if ($this->mfanyakazi_id) {
            return ($this->mfanyakazi->uwezo ?? null) === 'mkubwa' ? 'Mfanyakazi Mkubwa' : 'Mfanyakazi';
        }

        return 'Boss';
    }

    public function getOwnerInitialsAttribute(): string
    {
        $name = trim($this->owner_name);
        $parts = preg_split('/\s+/', $name) ?: [$name];
        $initials = '';
        foreach (array_slice($parts, 0, 2) as $part) {
            $initials .= mb_strtoupper(mb_substr($part, 0, 1));
        }

        return $initials ?: 'SH';
    }

    public function getIsOpenAttribute(): bool
    {
        return $this->status === 'open';
    }

    public function getDurationMinutesAttribute(): int
    {
        $end = $this->closed_at ?? now();

        return max(0, (int) Carbon::parse($this->started_at)->diffInMinutes($end));
    }

    public function getDurationTextAttribute(): string
    {
        $minutes = $this->duration_minutes;
        $hours = intdiv($minutes, 60);
        $mins = $minutes % 60;

        return $hours > 0 ? "{$hours}s {$mins}d" : "{$mins}d";
    }

    /* ------------------------------------------------------------------ */
    /* Scopes                                                              */
    /* ------------------------------------------------------------------ */

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Limit a query to the sessions the current viewer is allowed to see.
     * Bosses / full-access employees see everything in the company;
     * ordinary employees only see their own shifts.
     */
    public function scopeVisibleToCurrentUser($query)
    {
        $owner = static::currentOwner();

        if (!$owner) {
            return $query->whereRaw('1 = 0');
        }

        $query->where('company_id', $owner['company_id']);

        if (!$owner['can_view_all']) {
            if ($owner['mfanyakazi_id']) {
                $query->where('mfanyakazi_id', $owner['mfanyakazi_id']);
            } elseif ($owner['user_id']) {
                $query->where('user_id', $owner['user_id']);
            }
        }

        return $query;
    }

    /* ------------------------------------------------------------------ */
    /* Current-user helpers                                                */
    /* ------------------------------------------------------------------ */

    /**
     * Resolve the logged-in user (boss or employee) into a small context array.
     */
    public static function currentOwner(): ?array
    {
        if (Auth::guard('mfanyakazi')->check()) {
            $employee = Auth::guard('mfanyakazi')->user();

            return [
                'company_id' => $employee->company_id,
                'user_id' => null,
                'mfanyakazi_id' => $employee->id,
                'name' => $employee->jina,
                'type' => 'employee',
                'role' => $employee->role,
                'can_view_all' => ($employee->uwezo ?? 'mdogo') === 'mkubwa',
            ];
        }

        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();

            return [
                'company_id' => $user->company_id,
                'user_id' => $user->id,
                'mfanyakazi_id' => null,
                'name' => $user->name ?? $user->username,
                'type' => 'boss',
                'role' => $user->role,
                'can_view_all' => in_array($user->role, ['boss', 'admin'], true),
            ];
        }

        return null;
    }

    /**
     * The open shift belonging to the currently logged-in user.
     */
    public static function activeForCurrentUser(bool $fresh = false): ?self
    {
        if (!$fresh && static::$activeCacheResolved) {
            return static::$activeCache;
        }

        static::$activeCacheResolved = true;
        static::$activeCache = null;

        $owner = static::currentOwner();
        if (!$owner || !$owner['company_id']) {
            return null;
        }

        $query = static::query()
            ->where('company_id', $owner['company_id'])
            ->where('status', 'open');

        if ($owner['mfanyakazi_id']) {
            $query->where('mfanyakazi_id', $owner['mfanyakazi_id']);
        } else {
            $query->where('user_id', $owner['user_id']);
        }

        static::$activeCache = $query->latest('started_at')->first();

        return static::$activeCache;
    }

    public static function forgetActiveCache(): void
    {
        static::$activeCache = null;
        static::$activeCacheResolved = false;
    }

    /* ------------------------------------------------------------------ */
    /* Report calculation                                                  */
    /* ------------------------------------------------------------------ */

    /**
     * Full report for this shift: products sold, profit, expenses, payments.
     */
    public function summary(): array
    {
        // Closed shifts keep an immutable copy of exactly what was reported.
        if ($this->status === 'closed' && is_array($this->report_snapshot) && isset($this->report_snapshot['totals'])) {
            return $this->report_snapshot;
        }

        $productRows = DB::table('mauzos')
            ->join('bidhaas', 'mauzos.bidhaa_id', '=', 'bidhaas.id')
            ->where('mauzos.shift_session_id', $this->id)
            ->groupBy('bidhaas.id', 'bidhaas.jina', 'bidhaas.bei_nunua')
            ->selectRaw('bidhaas.id as bidhaa_id, bidhaas.jina as jina,
                SUM(mauzos.idadi) as idadi,
                SUM(mauzos.jumla) as kiasi,
                SUM(mauzos.idadi * COALESCE(bidhaas.bei_nunua, 0)) as gharama')
            ->orderByDesc('kiasi')
            ->get()
            ->map(function ($row) {
                $revenue = (float) $row->kiasi;
                $cost = (float) $row->gharama;

                return [
                    'bidhaa_id' => (int) $row->bidhaa_id,
                    'jina' => $row->jina,
                    'idadi' => (float) $row->idadi,
                    'kiasi' => $revenue,
                    'gharama' => $cost,
                    'faida' => $revenue - $cost,
                ];
            })
            ->values()
            ->all();

        $expenseRows = DB::table('matumizis')
            ->where('shift_session_id', $this->id)
            ->orderBy('created_at')
            ->get(['id', 'aina', 'maelezo', 'gharama', 'created_at'])
            ->map(fn($row) => [
                'id' => (int) $row->id,
                'aina' => $row->aina,
                'maelezo' => $row->maelezo,
                'gharama' => (float) $row->gharama,
                'created_at' => $row->created_at,
            ])
            ->all();

        $paymentRows = DB::table('mauzos')
            ->where('shift_session_id', $this->id)
            ->groupBy('lipa_kwa')
            ->selectRaw('lipa_kwa, SUM(jumla) as total, COUNT(*) as transactions')
            ->get()
            ->map(fn($row) => [
                'lipa_kwa' => $row->lipa_kwa ?: 'cash',
                'total' => (float) $row->total,
                'transactions' => (int) $row->transactions,
            ])
            ->all();

        $totalSales = array_sum(array_column($productRows, 'kiasi'));
        $totalCost = array_sum(array_column($productRows, 'gharama'));
        $totalItems = array_sum(array_column($productRows, 'idadi'));
        $totalExpenses = array_sum(array_column($expenseRows, 'gharama'));
        $totalProfit = $totalSales - $totalCost;
        $cashSales = 0.0;
        $otherSales = 0.0;
        $transactionCount = 0;

        foreach ($paymentRows as $payment) {
            $transactionCount += $payment['transactions'];
            if ($payment['lipa_kwa'] === 'cash') {
                $cashSales += $payment['total'];
            } else {
                $otherSales += $payment['total'];
            }
        }

        $openingBalance = (float) ($this->opening_balance ?? 0);
        $expectedCash = $openingBalance + $cashSales - $totalExpenses;
        $closingCash = $this->closing_cash !== null ? (float) $this->closing_cash : null;
        $difference = $closingCash !== null ? $closingCash - $expectedCash : null;

        return [
            'session' => [
                'id' => $this->id,
                'status' => $this->status,
                'started_at' => optional($this->started_at)->toIso8601String(),
                'started_at_text' => optional($this->started_at)->format('d/m/Y H:i'),
                'closed_at' => optional($this->closed_at)->toIso8601String(),
                'closed_at_text' => optional($this->closed_at)->format('d/m/Y H:i'),
                'duration_minutes' => $this->duration_minutes,
                'duration_text' => $this->duration_text,
                'owner_name' => $this->owner_name,
                'owner_role' => $this->owner_role,
                'owner_initials' => $this->owner_initials,
                'notes' => $this->notes,
                'report_sent_at' => optional($this->report_sent_at)->format('d/m/Y H:i'),
                'report_sent_to' => $this->report_sent_to,
            ],
            'products' => $productRows,
            'expenses' => $expenseRows,
            'payments' => $paymentRows,
            'totals' => [
                'opening_balance' => $openingBalance,
                'total_sales' => $totalSales,
                'total_items' => $totalItems,
                'total_cost' => $totalCost,
                'total_profit' => $totalProfit,
                'total_expenses' => $totalExpenses,
                'cash_sales' => $cashSales,
                'other_sales' => $otherSales,
                'expected_cash' => $expectedCash,
                'closing_cash' => $closingCash,
                'cash_difference' => $difference,
                'net_after_expenses' => $totalSales - $totalExpenses,
                'transaction_count' => $transactionCount,
                'product_count' => count($productRows),
            ],
        ];
    }

    /**
     * Compact summary used in lists (closed sessions use their saved snapshot,
     * open sessions are calculated live).
     */
    public function listSummary(): array
    {
        if ($this->status === 'closed') {
            return [
                'total_sales' => (float) $this->total_sales,
                'total_items' => (float) $this->total_items,
                'total_profit' => (float) $this->total_profit,
                'total_expenses' => (float) $this->total_expenses,
                'cash_sales' => (float) $this->cash_sales,
                'expected_cash' => (float) ($this->expected_cash ?? 0),
                'closing_cash' => $this->closing_cash !== null ? (float) $this->closing_cash : null,
                'cash_difference' => $this->cash_difference !== null ? (float) $this->cash_difference : null,
                'net_after_expenses' => (float) $this->total_sales - (float) $this->total_expenses,
            ];
        }

        $summary = $this->summary();

        return $summary['totals'];
    }

    /**
     * Persist the closing snapshot for this shift.
     */
    public function closeWith(?float $closingCash, ?string $notes = null): array
    {
        // Set the end time first so duration/closed_at are included in the report.
        $this->closed_at = now();
        $summary = $this->summary();
        $summary['session']['status'] = 'closed';
        $totals = $summary['totals'];

        $this->status = 'closed';
        $this->closing_cash = $closingCash;
        $this->expected_cash = $totals['expected_cash'];
        $this->cash_difference = $closingCash !== null ? $closingCash - $totals['expected_cash'] : null;
        $this->total_sales = $totals['total_sales'];
        $this->total_items = $totals['total_items'];
        $this->total_cost = $totals['total_cost'];
        $this->total_profit = $totals['total_profit'];
        $this->total_expenses = $totals['total_expenses'];
        $this->cash_sales = $totals['cash_sales'];
        $this->notes = $notes;

        // Store the final cash reconciliation in the immutable snapshot too.
        $summary['session']['notes'] = $notes;
        $summary['totals']['closing_cash'] = $closingCash;
        $summary['totals']['cash_difference'] = $this->cash_difference;
        $summary['totals']['net_after_expenses'] = $totals['total_sales'] - $totals['total_expenses'];
        $this->report_snapshot = $summary;
        $this->save();

        static::forgetActiveCache();

        return $this->fresh()->summary();
    }
}
