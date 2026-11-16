<?php

namespace App\Http\Controllers;

use App\Models\ProductCleanupLog;
use App\Services\ProductCleanupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Boss-only. Every route is behind App\Http\Middleware\BossOnly (web guard only).
 * The password is verified on the server against the logged-in Boss account.
 */
class ProductCleanupController extends Controller
{
    private const UNLOCK_MINUTES = 15;
    private const MAX_IDS        = 2000;

    public function __construct(private ProductCleanupService $svc) {}

    private function actor(): array
    {
        $u = Auth::guard('web')->user();
        return ['user' => $u, 'id' => $u->id, 'company_id' => $u->company_id,
                'name' => $u->name ?? $u->username ?? $u->email ?? 'Boss'];
    }

    private function unlocked(Request $r): bool
    {
        $s = $r->session()->get('cleanup_unlock');
        return $s && $s['user'] === Auth::guard('web')->id() && $s['until'] > time();
    }

    private function passwordOk(Request $r): bool|string
    {
        $user = Auth::guard('web')->user();
        $key = 'cleanup-pw:' . $user->id . '|' . $r->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            return 'Too many wrong attempts. Try again in ' . RateLimiter::availableIn($key) . ' seconds.';
        }
        if (!$r->filled('password') || !Hash::check($r->password, $user->getAuthPassword())) {
            RateLimiter::hit($key, 300);
            return 'Wrong password.';
        }
        RateLimiter::clear($key);
        return true;
    }

    private function locked()
    {
        return response()->json(['success' => false, 'locked' => true, 'message' => 'Session locked. Enter your password again.'], 423);
    }

    /**
     * Resolve the IDs the caller intends to act on.
     *
     * - Normal case: use `ids[]` from the request.
     * - "Select all matching" case: `select_all=1` → re-run the SAME query
     *   the data() endpoint uses (criteria + match + search + archived)
     *   and return every matching id (capped at MAX_IDS).
     */
    private function resolveIds(Request $request, int $companyId): array
    {
        if (!$request->boolean('select_all')) {
            return (array) $request->input('ids', []);
        }

        $archived = $request->boolean('archived');
        $q = $this->svc->baseQuery($companyId, $archived);

        if (!$archived) {
            $q = $this->svc->applyCriteria(
                $q,
                (array) $request->input('criteria', []),
                (string) $request->input('match', 'any')
            );
        }
        if ($s = trim((string) $request->search)) {
            $q->where(fn ($w) => $w->where('bidhaas.jina', 'like', "%{$s}%")
                                    ->orWhere('bidhaas.barcode', 'like', "%{$s}%")
                                    ->orWhere('bidhaas.aina', 'like', "%{$s}%"));
        }

        return (clone $q)->limit(self::MAX_IDS)->pluck('bidhaas.id')->all();
    }

    public function index(Request $request)
    {
        $a = $this->actor();
        $unlocked = $this->unlocked($request);

        return view('product-cleanup.index', [
            'unlocked' => $unlocked,
            'counts' => $unlocked ? $this->svc->counts($a['company_id']) : null,
        ]);
    }

    public function unlock(Request $request)
    {
        $ok = $this->passwordOk($request);
        if ($ok !== true) return response()->json(['success' => false, 'message' => $ok], 422);

        $request->session()->put('cleanup_unlock', ['user' => Auth::guard('web')->id(), 'until' => time() + self::UNLOCK_MINUTES * 60]);

        return response()->json(['success' => true]);
    }

    public function lock(Request $request)
    {
        $request->session()->forget('cleanup_unlock');
        return response()->json(['success' => true]);
    }

    /** List (paged) or, with ?ids_only=1, every matching id (max 2000) for "select all matching". */
    public function data(Request $request)
    {
        if (!$this->unlocked($request)) return $this->locked();
        $a = $this->actor();

        $archived = $request->boolean('archived');
        $q = $this->svc->baseQuery($a['company_id'], $archived);

        if (!$archived) {
            $q = $this->svc->applyCriteria($q, (array) $request->input('criteria', []), (string) $request->input('match', 'any'));
        }
        if ($s = trim((string) $request->search)) {
            $q->where(fn ($w) => $w->where('bidhaas.jina', 'like', "%{$s}%")->orWhere('bidhaas.barcode', 'like', "%{$s}%")->orWhere('bidhaas.aina', 'like', "%{$s}%"));
        }

        if ($request->boolean('ids_only')) {
            return response()->json(['success' => true, 'ids' => (clone $q)->limit(self::MAX_IDS)->pluck('bidhaas.id')]);
        }

        $page = $q->orderBy('bidhaas.jina')->paginate(50);

        return response()->json([
            'success' => true,
            'data' => $page->getCollection()->map(fn ($r) => $this->svc->mapRow($r))->values(),
            'meta' => ['page' => $page->currentPage(), 'last' => $page->lastPage(), 'total' => $page->total()],
            'counts' => $this->svc->counts($a['company_id']),
        ]);
    }

    /** Exact names that would be affected, split into deletable vs protected. */
    public function preview(Request $request)
    {
        if (!$this->unlocked($request)) return $this->locked();

        $request->validate([
            'ids'             => 'required_if:select_all,0|array|min:1|max:' . self::MAX_IDS,
            'ids.*'           => 'integer',
            'select_all'      => 'sometimes|boolean',
            'criteria'        => 'nullable|array',
            'match'           => 'nullable|in:any,multi',
            'search'          => 'nullable|string|max:255',
            'archived'        => 'nullable|boolean',
        ]);

        $a   = $this->actor();
        $ids = $this->resolveIds($request, $a['company_id']);

        if (empty($ids)) {
            return response()->json(['success' => true, 'deletable' => [], 'protected' => [], 'total' => 0]);
        }

        return response()->json(['success' => true, 'total' => count($ids)] + $this->svc->classify($a['company_id'], $ids));
    }

    public function execute(Request $request)
    {
        if (!$this->unlocked($request)) return $this->locked();

        $data = $request->validate([
            'ids'        => 'required_if:select_all,0|array|min:1|max:' . self::MAX_IDS,
            'ids.*'      => 'integer',
            'select_all' => 'sometimes|boolean',
            'criteria'   => 'nullable|array',
            'match'      => 'nullable|in:any,multi',
            'search'     => 'nullable|string|max:255',
            'archived'   => 'nullable|boolean',
            'action'     => 'required|in:delete,archive',
            'reason'     => 'nullable|string|max:200',
            'password'   => 'required|string',
        ]);

        $ok = $this->passwordOk($request);          // password is re-checked for EVERY bulk operation
        if ($ok !== true) return response()->json(['success' => false, 'message' => $ok], 422);

        $a   = $this->actor();
        $ids = $this->resolveIds($request, $a['company_id']);

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No matching products.'], 422);
        }

        $res = $this->svc->execute(
            $a,
            $ids,
            $data['action'],
            $data['criteria'] ?? [],
            $request->ip(),
            $data['reason'] ?? ''
        );

        $verb = $data['action'] === 'delete' ? 'deleted permanently' : 'archived';
        return response()->json([
            'success' => true,
            'message' => "{$res['done']} product(s) {$verb}.",
            'skipped' => $res['skipped'],
        ]);
    }

    public function restore(Request $request)
    {
        if (!$this->unlocked($request)) return $this->locked();

        $request->validate([
            'ids'        => 'required_if:select_all,0|array|min:1|max:' . self::MAX_IDS,
            'ids.*'      => 'integer',
            'select_all' => 'sometimes|boolean',
            'search'     => 'nullable|string|max:255',
            'archived'   => 'nullable|boolean',
        ]);

        $a   = $this->actor();
        $ids = $this->resolveIds($request, $a['company_id']);

        if (empty($ids)) {
            return response()->json(['success' => true, 'message' => 'Nothing to restore.']);
        }

        $n = $this->svc->restore($a, $ids, $request->ip());

        return response()->json(['success' => true, 'message' => "{$n} product(s) restored."]);
    }

    public function log(Request $request)
    {
        if (!$this->unlocked($request)) return $this->locked();

        return response()->json(['success' => true,
            'data' => ProductCleanupLog::where('company_id', $this->actor()['company_id'])->latest('id')->limit(30)
                ->get(['id', 'user_name', 'action', 'product_count', 'created_at'])]);
    }
}