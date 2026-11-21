<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\EmployeeSalaryDeduction;
use App\Models\Order;
use App\Models\Wafanyakazi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class EmployeesApiController extends Controller
{
    /* ───────────────────────────────────────────────────────
     |  GET /api/wafanyakazi
     |  List employees for the current company with summary.
     ─────────────────────────────────────────────────────── */
    public function index(Request $request)
    {
        $companyId = $request->user()?->company_id;
        if (!$companyId) {
            return $this->error('Akaunti yako haijaunganishwa na kampuni.', 403);
        }

        $search   = trim((string) $request->input('search', ''));
        $status   = $request->input('status');   // active | inactive
        $uwezo    = $request->input('uwezo');    // mdogo | mkubwa
        $perPage  = max(1, (int) $request->input('per_page', 20));

        $query = Wafanyakazi::query()->where('company_id', $companyId);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('jina', 'like', "%{$search}%")
                  ->orWhere('simu', 'like', "%{$search}%")
                  ->orWhere('barua_pepe', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('remarks', 'like', "%{$search}%");
            });
        }

        if ($status === 'active')   $query->where('getini', 'ingia');
        if ($status === 'inactive') $query->where('getini', 'simama');

        if ($uwezo === 'mdogo' || $uwezo === 'mkubwa') {
            $query->where('uwezo', $uwezo);
        }

        $paginator = $query->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => collect($paginator->items())
                ->map(fn (Wafanyakazi $e) => $this->payload($e))
                ->values(),
            'summary' => $this->summary($companyId),
            'meta'    => $this->meta($paginator),
            'filters' => [
                'search' => $search,
                'status' => $status,
                'uwezo'  => $uwezo,
            ],
        ]);
    }

    /* ───────────────────────────────────────────────────────
     |  GET /api/wafanyakazi/{id}
     |  Single employee with salary + deductions + performance.
     ─────────────────────────────────────────────────────── */
    public function show(Request $request, $id)
    {
        $companyId = $request->user()?->company_id;
        if (!$companyId) {
            return $this->error('Akaunti yako haijaunganishwa na kampuni.', 403);
        }

        $employee = Wafanyakazi::where('company_id', $companyId)->find($id);
        if (!$employee) {
            return $this->error('Mfanyakazi hajapatikana.', 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $this->payload($employee),
            'salary'  => $this->salaryInfo($employee),
            'performance' => $this->performanceInfo($employee),
        ]);
    }

    /* ───────────────────────────────────────────────────────
     |  POST /api/wafanyakazi
     ─────────────────────────────────────────────────────── */
    public function store(Request $request)
    {
        $companyId = $request->user()?->company_id;
        if (!$companyId) {
            return $this->error('Akaunti yako haijaunganishwa na kampuni.', 403);
        }

        $v = validator($request->all(), [
            'jina'          => 'required|string|max:255',
            'simu'          => 'nullable|string|max:20|unique:wafanyakazis,simu',
            'jinsia'        => 'required|string|in:Mwanaume,Mwanamke',
            'anuani'        => 'nullable|string|max:500',
            'barua_pepe'    => 'nullable|email|max:255|unique:wafanyakazis,barua_pepe',
            'ndugu'         => 'nullable|string|max:255',
            'simu_ndugu'    => 'nullable|string|max:20',
            'username'      => 'required|string|max:255|unique:users,username|unique:wafanyakazis,username|regex:/^[a-zA-Z0-9_]+$/',
            'password'      => 'required|string|min:6|max:255',
            'tarehe_kuzaliwa' => 'nullable|date|before:today',
            'uwezo'         => 'nullable|in:mdogo,mkubwa',
            'salary'        => 'nullable|numeric|min:0',
            'allow_counter_access' => 'nullable|boolean',
            'remarks'       => 'nullable|string|max:500',
            'getini'        => 'nullable|in:simama,ingia',
        ]);

        if ($v->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $v->errors(),
                'message' => $v->errors()->first(),
            ], 422);
        }

        $data = $v->validated();
        $data['company_id']           = $companyId;
        $data['password']             = Hash::make($data['password']);
        $data['role']                 = 'mfanyakazi';
        $data['getini']               = $data['getini'] ?? 'simama';
        $data['uwezo']                = $data['uwezo'] ?? 'mdogo';
        $data['salary']               = $data['salary'] ?? 0;
        $data['allow_counter_access'] = (bool) ($data['allow_counter_access'] ?? false);
        $data['active']               = $data['getini'] === 'ingia';

        $employee = Wafanyakazi::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Mfanyakazi amesajiliwa kikamilifu!',
            'data'    => $this->payload($employee),
        ], 201);
    }

    /* ───────────────────────────────────────────────────────
     |  PUT /api/wafanyakazi/{id}
     ─────────────────────────────────────────────────────── */
    public function update(Request $request, $id)
    {
        $companyId = $request->user()?->company_id;
        if (!$companyId) {
            return $this->error('Akaunti yako haijaunganishwa na kampuni.', 403);
        }

        $employee = Wafanyakazi::where('company_id', $companyId)->find($id);
        if (!$employee) {
            return $this->error('Mfanyakazi hajapatikana.', 404);
        }

        $v = validator($request->all(), [
            'jina'          => 'required|string|max:255',
            'simu'          => 'nullable|string|max:20|unique:wafanyakazis,simu,' . $id,
            'jinsia'        => 'required|string|in:Mwanaume,Mwanamke',
            'anuani'        => 'nullable|string|max:500',
            'barua_pepe'    => 'nullable|email|max:255|unique:wafanyakazis,barua_pepe,' . $id,
            'ndugu'         => 'nullable|string|max:255',
            'simu_ndugu'    => 'nullable|string|max:20',
            'username'      => 'nullable|string|max:255|unique:wafanyakazis,username,' . $id,
            'password'      => 'nullable|string|min:6|max:255',
            'tarehe_kuzaliwa' => 'nullable|date|before:today',
            'uwezo'         => 'nullable|in:mdogo,mkubwa',
            'salary'        => 'nullable|numeric|min:0',
            'allow_counter_access' => 'nullable|boolean',
            'remarks'       => 'nullable|string|max:500',
            'getini'        => 'nullable|in:simama,ingia',
        ]);

        if ($v->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $v->errors(),
                'message' => $v->errors()->first(),
            ], 422);
        }

        $data = $v->validated();

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        if (array_key_exists('allow_counter_access', $data)) {
            $data['allow_counter_access'] = (bool) $data['allow_counter_access'];
        }

        if (isset($data['getini'])) {
            $data['active'] = $data['getini'] === 'ingia';
        }

        $employee->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Taarifa za mfanyakazi zimesasishwa!',
            'data'    => $this->payload($employee->fresh()),
        ]);
    }

    /* ───────────────────────────────────────────────────────
     |  DELETE /api/wafanyakazi/{id}
     ─────────────────────────────────────────────────────── */
    public function destroy(Request $request, $id)
    {
        $companyId = $request->user()?->company_id;
        if (!$companyId) {
            return $this->error('Akaunti yako haijaunganishwa na kampuni.', 403);
        }

        $employee = Wafanyakazi::where('company_id', $companyId)->find($id);
        if (!$employee) {
            return $this->error('Mfanyakazi hajapatikana.', 404);
        }

        $orderCount = Order::where('created_by', $id)->count();
        if ($orderCount > 0) {
            return $this->error(
                "Haiwezi kufuta mfanyakazi huyu kwa sababu ana {$orderCount} orders zilizorekodiwa.",
                422
            );
        }

        $employee->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mfanyakazi amefutwa kikamilifu!',
        ]);
    }

    /* ───────────────────────────────────────────────────────
     |  POST /api/wafanyakazi/{id}/salary
     |  Update salary + record history.
     ─────────────────────────────────────────────────────── */
    public function updateSalary(Request $request, $id)
    {
        $companyId = $request->user()?->company_id;
        if (!$companyId) {
            return $this->error('Akaunti yako haijaunganishwa na kampuni.', 403);
        }

        $employee = Wafanyakazi::where('company_id', $companyId)->find($id);
        if (!$employee) {
            return $this->error('Mfanyakazi hajapatikana.', 404);
        }

        $v = validator($request->all(), [
            'salary'    => 'required|numeric|min:0',
            'currency'  => 'nullable|string|max:10',
            'frequency' => 'nullable|string|in:monthly,weekly,daily',
            'remarks'   => 'nullable|string|max:500',
        ]);

        if ($v->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $v->errors(),
                'message' => $v->errors()->first(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $employee->salary = $request->salary;
            $employee->save();

            if (class_exists(\App\Models\EmployeeSalary::class)) {
                \App\Models\EmployeeSalary::create([
                    'company_id'     => $companyId,
                    'mfanyakazi_id'  => $id,
                    'salary_amount'  => $request->salary,
                    'currency'       => $request->currency ?? 'TZS',
                    'frequency'      => $request->frequency ?? 'monthly',
                    'effective_date' => now(),
                    'remarks'        => $request->remarks,
                    'created_by'     => $request->user()->id,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Mshahara umesasishwa kikamilifu!',
                'data'    => $this->payload($employee->fresh()),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('updateSalary: ' . $e->getMessage());
            return $this->error('Hitilafu katika kusasisha mshahara.', 500);
        }
    }

    /* ───────────────────────────────────────────────────────
     |  POST /api/wafanyakazi/{id}/deductions
     |  Add a deduction (pending approval).
     ─────────────────────────────────────────────────────── */
    public function addDeduction(Request $request, $id)
    {
        $companyId = $request->user()?->company_id;
        if (!$companyId) {
            return $this->error('Akaunti yako haijaunganishwa na kampuni.', 403);
        }

        $employee = Wafanyakazi::where('company_id', $companyId)->find($id);
        if (!$employee) {
            return $this->error('Mfanyakazi hajapatikana.', 404);
        }

        $v = validator($request->all(), [
            'amount'   => 'required|numeric|min:0',
            'reason'   => 'required|string|max:500',
            'order_id' => 'nullable|exists:orders,id',
            'remarks'  => 'nullable|string|max:500',
        ]);

        if ($v->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $v->errors(),
                'message' => $v->errors()->first(),
            ], 422);
        }

        $approved = EmployeeSalaryDeduction::where('company_id', $companyId)
            ->where('mfanyakazi_id', $id)
            ->where('status', 'approved')
            ->sum('amount');

        if (($approved + $request->amount) > ($employee->salary ?? 0)) {
            return $this->error(
                'Kato hii inazidi mshahara wa mfanyakazi. Mshahara: '
                . number_format($employee->salary ?? 0, 2)
                . ', Makato yaliyopo: ' . number_format($approved, 2),
                422
            );
        }

        $deduction = EmployeeSalaryDeduction::create([
            'company_id'    => $companyId,
            'mfanyakazi_id' => $id,
            'order_id'      => $request->order_id,
            'amount'        => $request->amount,
            'reason'        => $request->reason,
            'status'        => 'pending',
            'remarks'       => $request->remarks,
            'approved_by'   => null,
            'approved_at'   => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kato imeongezwa! Inasubiri idhini.',
            'data'    => $this->deductionPayload($deduction),
        ], 201);
    }

    /* ───────────────────────────────────────────────────────
     |  POST /api/wafanyakazi/deductions/{id}/approve
     ─────────────────────────────────────────────────────── */
    public function approveDeduction(Request $request, $id)
    {
        $companyId = $request->user()?->company_id;
        if (!$companyId) {
            return $this->error('Akaunti yako haijaunganishwa na kampuni.', 403);
        }

        $deduction = EmployeeSalaryDeduction::where('company_id', $companyId)->find($id);
        if (!$deduction) {
            return $this->error('Kato haipatikani.', 404);
        }
        if ($deduction->status === 'approved') {
            return $this->error('Kato hii tayari imeidhinishwa.', 422);
        }

        $employee = Wafanyakazi::find($deduction->mfanyakazi_id);
        $approved = EmployeeSalaryDeduction::where('company_id', $companyId)
            ->where('mfanyakazi_id', $deduction->mfanyakazi_id)
            ->where('status', 'approved')
            ->sum('amount');

        if (($approved + $deduction->amount) > ($employee->salary ?? 0)) {
            return $this->error(
                'Kato hii inazidi mshahara wa mfanyakazi.',
                422
            );
        }

        $deduction->status      = 'approved';
        $deduction->approved_by = $request->user()->id;
        $deduction->approved_at = now();
        $deduction->save();

        return response()->json([
            'success' => true,
            'message' => 'Kato imeidhinishwa!',
            'data'    => $this->deductionPayload($deduction),
        ]);
    }

    /* ───────────────────────────────────────────────────────
     |  POST /api/wafanyakazi/deductions/{id}/reject
     ─────────────────────────────────────────────────────── */
    public function rejectDeduction(Request $request, $id)
    {
        $companyId = $request->user()?->company_id;
        if (!$companyId) {
            return $this->error('Akaunti yako haijaunganishwa na kampuni.', 403);
        }

        $deduction = EmployeeSalaryDeduction::where('company_id', $companyId)->find($id);
        if (!$deduction) {
            return $this->error('Kato haipatikani.', 404);
        }
        if ($deduction->status === 'approved') {
            return $this->error('Kato hii tayari imeidhinishwa.', 422);
        }

        $deduction->status = 'rejected';
        $deduction->save();

        return response()->json([
            'success' => true,
            'message' => 'Kato imekataliwa!',
            'data'    => $this->deductionPayload($deduction),
        ]);
    }

    /* ───────────────────────────────────────────────────────
     |  POST /api/wafanyakazi/{id}/counter-access
     ─────────────────────────────────────────────────────── */
    public function toggleCounterAccess(Request $request, $id)
    {
        $companyId = $request->user()?->company_id;
        if (!$companyId) {
            return $this->error('Akaunti yako haijaunganishwa na kampuni.', 403);
        }

        $employee = Wafanyakazi::where('company_id', $companyId)->find($id);
        if (!$employee) {
            return $this->error('Mfanyakazi hajapatikana.', 404);
        }

        $employee->allow_counter_access = !$employee->allow_counter_access;
        $employee->save();

        return response()->json([
            'success' => true,
            'message' => 'Ruhusa ya Counter imebadilishwa!',
            'data'    => $this->payload($employee->fresh()),
        ]);
    }

    /* ───────────────────────────────────────────────────────
     |  Helpers
     ─────────────────────────────────────────────────────── */
    private function payload(Wafanyakazi $e): array
    {
        return [
            'id'                 => (int) $e->id,
            'name'               => $e->jina,
            'phone'              => $e->simu,
            'email'              => $e->barua_pepe,
            'address'            => $e->anuani,
            'gender'             => $e->jinsia,
            'nextOfKin'          => $e->ndugu,
            'nextOfKinPhone'     => $e->simu_ndugu,
            'username'           => $e->username,
            'dateOfBirth'        => $e->tarehe_kuzaliwa
                ? \Carbon\Carbon::parse($e->tarehe_kuzaliwa)->toIso8601String()
                : null,
            'role'               => $e->role,
            'status'             => $e->getini,   // 'ingia' | 'simama'
            'level'              => $e->uwezo,    // 'mdogo' | 'mkubwa'
            'salary'             => (float) ($e->salary ?? 0),
            'allowCounterAccess' => (bool) ($e->allow_counter_access ?? false),
            'remarks'            => $e->remarks,
            'active'             => (bool) ($e->active ?? false),
            'createdAt'          => $e->created_at?->toIso8601String(),
            'updatedAt'          => $e->updated_at?->toIso8601String(),
        ];
    }

    private function deductionPayload(EmployeeSalaryDeduction $d): array
    {
        return [
            'id'           => (int) $d->id,
            'employeeId'   => (int) $d->mfanyakazi_id,
            'orderId'      => $d->order_id !== null ? (int) $d->order_id : null,
            'amount'       => (float) $d->amount,
            'reason'       => $d->reason,
            'status'       => $d->status,
            'remarks'      => $d->remarks,
            'approvedBy'   => $d->approved_by !== null ? (int) $d->approved_by : null,
            'approvedAt'   => $d->approved_at?->toIso8601String(),
            'createdAt'    => $d->created_at?->toIso8601String(),
        ];
    }

    private function summary(int $companyId): array
    {
        $total        = Wafanyakazi::where('company_id', $companyId)->count();
        $active       = Wafanyakazi::where('company_id', $companyId)->where('getini', 'ingia')->count();
        $male         = Wafanyakazi::where('company_id', $companyId)->where('jinsia', 'Mwanaume')->count();
        $female       = Wafanyakazi::where('company_id', $companyId)->where('jinsia', 'Mwanamke')->count();
        $counterAcc   = Wafanyakazi::where('company_id', $companyId)->where('allow_counter_access', true)->count();
        $totalSalary  = (float) Wafanyakazi::where('company_id', $companyId)->sum('salary');

        $totalDeductions = 0.0;
        if (Schema::hasTable('employee_salary_deductions')) {
            $totalDeductions = (float) EmployeeSalaryDeduction::where('company_id', $companyId)
                ->where('status', 'approved')
                ->sum('amount');
        }

        return [
            'total'           => $total,
            'active'          => $active,
            'inactive'        => $total - $active,
            'male'            => $male,
            'female'          => $female,
            'counterAccess'   => $counterAcc,
            'totalSalary'     => $totalSalary,
            'totalDeductions' => $totalDeductions,
            'netPayroll'      => $totalSalary - $totalDeductions,
        ];
    }

    private function salaryInfo(Wafanyakazi $e): array
    {
        $approved = EmployeeSalaryDeduction::where('company_id', $e->company_id)
            ->where('mfanyakazi_id', $e->id)
            ->where('status', 'approved')
            ->sum('amount');

        $pending = EmployeeSalaryDeduction::where('company_id', $e->company_id)
            ->where('mfanyakazi_id', $e->id)
            ->where('status', 'pending')
            ->sum('amount');

        return [
            'currentSalary'      => (float) ($e->salary ?? 0),
            'approvedDeductions' => (float) $approved,
            'pendingDeductions'  => (float) $pending,
            'netSalary'          => (float) (($e->salary ?? 0) - $approved),
        ];
    }

    private function performanceInfo(Wafanyakazi $e): array
    {
        $total     = Order::where('company_id', $e->company_id)->where('created_by', $e->id)->count();
        $paid      = Order::where('company_id', $e->company_id)->where('created_by', $e->id)->where('status', 'paid')->count();
        $cancelled = Order::where('company_id', $e->company_id)->where('created_by', $e->id)->where('status', 'cancelled')->count();
        $revenue   = (float) Order::where('company_id', $e->company_id)->where('created_by', $e->id)->where('status', 'paid')->sum('total');

        $rate = $total > 0 ? round(($paid / $total) * 100, 2) : 0.0;

        return [
            'totalOrders'     => $total,
            'paidOrders'      => $paid,
            'cancelledOrders' => $cancelled,
            'totalRevenue'    => $revenue,
            'completionRate'  => $rate,
        ];
    }

    private function meta($paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'last_page'    => $paginator->lastPage(),
            'per_page'     => $paginator->perPage(),
            'total'        => $paginator->total(),
            'from'         => $paginator->firstItem(),
            'to'           => $paginator->lastItem(),
        ];
    }

    private function error(string $message, int $status): \Illuminate\Http\JsonResponse
    {
        return response()->json(['success' => false, 'message' => $message], $status);
    }
}